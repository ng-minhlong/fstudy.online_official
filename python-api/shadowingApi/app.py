from flask import Blueprint, request, jsonify
import speech_recognition as sr
from pydub import AudioSegment
import io
import os
import nltk
from nltk.corpus import cmudict
import requests
import tempfile

# Init
shadowingResult_bp = Blueprint('shadowingResult', __name__)
nltk.download('cmudict')
cmu_dict = cmudict.dict()

def get_cmu_phones(word):
    word = word.lower()
    if word in cmu_dict:
        return cmu_dict[word][0]
    return []

@shadowingResult_bp.route('/shadowingResult', methods=['POST'])
def shadowing_processer():
    if 'audio' not in request.files or 'lang' not in request.form or 'text' not in request.form:
        return jsonify({'error': 'Missing audio file, text or lang parameter'}), 400

    audio_file = request.files['audio']
    lang = request.form['lang']
    input_text = request.form['text'].strip().lower()

    try:
        audio = AudioSegment.from_file(audio_file)
        audio = audio.set_channels(1).set_frame_rate(16000)
        
        # Lưu file tạm cho Gentle
        with tempfile.NamedTemporaryFile(delete=False, suffix=".wav") as temp_audio:
            audio.export(temp_audio.name, format="wav")
            temp_audio_path = temp_audio.name

        # Tạo byte stream để nhận dạng STT
        audio_bytes = io.BytesIO()
        audio.export(audio_bytes, format="wav")
        audio_bytes.seek(0)

        recognizer = sr.Recognizer()
        with sr.AudioFile(audio_bytes) as source:
            audio_data = recognizer.record(source)
            try:
                recognized_text = recognizer.recognize_google(audio_data, language=lang)
            except sr.UnknownValueError:
                recognized_text = "[Unrecognized]"
            except sr.RequestError:
                return jsonify({'error': 'Could not connect to Google Speech Recognition'}), 500

        # Gửi đến Gentle để phân tích pronunciation dựa vào `input_text`
        with open(temp_audio_path, 'rb') as f_audio:
            gentle_response = requests.post(
                'http://localhost:8765/transcriptions?async=false',
                files={'audio': f_audio},
                data={'transcript': input_text}
            )

        os.remove(temp_audio_path)

        if gentle_response.status_code != 200:
            return jsonify({"error": "Gentle alignment failed"}), 500

        result = gentle_response.json()
        if 'words' not in result:
            return jsonify({"error": "Alignment failed"}), 500

        # Phân tích pronunciation
        words_output = []
        for word_data in result['words']:
            word = word_data.get('word', '').lower()
            if not word.isalpha():
                continue

            cmu_phones = get_cmu_phones(word)
            if not cmu_phones:
                continue

            expected_phones = [p[:-1] if p[-1].isdigit() else p for p in cmu_phones]
            user_phones = [p['phone'].split('_')[0].upper() for p in word_data.get('phones', [])]

            comparison = []
            min_len = min(len(expected_phones), len(user_phones))
            for i in range(min_len):
                comparison.append({
                    "expected": expected_phones[i],
                    "user": user_phones[i],
                    "match": expected_phones[i] == user_phones[i]
                })
            for i in range(min_len, len(expected_phones)):
                comparison.append({"expected": expected_phones[i], "user": None, "match": False})
            for i in range(min_len, len(user_phones)):
                comparison.append({"expected": None, "user": user_phones[i], "match": False})

            stress_index = next((i for i, p in enumerate(cmu_phones) if '1' in p), -1)
            stress_ok = False
            if stress_index != -1 and stress_index < len(word_data.get('phones', [])):
                stressed = word_data['phones'][stress_index]
                if stressed['duration'] > 0.15:
                    stress_ok = True

            words_output.append({
                "word": word,
                "expected_phones": expected_phones,
                "user_phones": user_phones,
                "comparison": comparison,
                "stress_correct": stress_ok
            })

        return jsonify({
            'input_text': input_text,
            'recognized_text': recognized_text,
            'lang': lang,
            'confidence': 1.0 if recognized_text != "[Unrecognized]" else 0.0,
            'pronunciation_analysis': {
                'sentence': input_text,
                'words': words_output
            }
        })

    except Exception as e:
        return jsonify({'error': str(e)}), 500
