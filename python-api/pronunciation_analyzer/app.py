from flask import Flask, request, jsonify, render_template, Blueprint, send_file
import requests
import nltk
import os
from pydub import AudioSegment
import tempfile
import urllib.request

phone_analyzer_bp = Blueprint('pronunciation-analyzer', __name__)


nltk.download('cmudict')
from nltk.corpus import cmudict



cmu_dict = cmudict.dict()
os.makedirs("uploads", exist_ok=True)

def get_cmu_phones(word):
    word = word.lower()
    if word in cmu_dict:
        return cmu_dict[word][0]
    return []

def download_audio_from_url(url):
    try:
        temp_file = tempfile.NamedTemporaryFile(delete=False, suffix=".mp3")
        urllib.request.urlretrieve(url, temp_file.name)
        return temp_file.name
    except Exception as e:
        print(f"Error downloading audio: {e}")
        return None

@phone_analyzer_bp.route('/pronunciation_analyzer', methods=['GET'])
def pronunciation_analyzer_index():
    return render_template('index.html')


@phone_analyzer_bp.route('/analyze_sentence', methods=['POST'])
def analyze_sentence():
    data = request.get_json()
    sentence = data.get('text', '').strip().lower()
    audio_url = data.get('audio')
    audio_url = audio_url.replace('\/', '/')


    #sentence = request.form.get('text', '').strip().lower()
    if not sentence:
        return jsonify({"error": "Missing text input"}), 400

    file_path = "uploads/temp_sentence.wav"

    # Ưu tiên file, sau đó đến URL
    #if 'audio' in request.files:
     #   audio_file = request.files['audio']
      #  audio_file.save(file_path)
    if audio_url:
        if "drive.google.com" in audio_url and "id=" in audio_url:
            import re
            match = re.search(r'id=([^&]+)', audio_url)
            if match:
                file_id = match.group(1)
                audio_url = f"https://drive.google.com/uc?export=download&id={file_id}"
            else:
                return jsonify({"error": "Invalid Google Drive URL"}), 400

        file_path = download_audio_from_url(audio_url)
        if not file_path:
            return jsonify({"error": "Failed to download audio"}), 500
    else:
        return jsonify({"error": "No audio source provided"}), 400

    sound = AudioSegment.from_file(file_path)
    sound = sound.set_channels(1).set_frame_rate(16000)
    sound.export(file_path, format="wav")

    with open(file_path, 'rb') as f_audio:
        response = requests.post(
            'http://localhost:8765/transcriptions?async=false',
            files={'audio': f_audio},
            data={'transcript': sentence}
        )

    if response.status_code != 200:
        return jsonify({"error": "Gentle failed"}), 500

    result = response.json()
    if 'words' not in result:
        return jsonify({"error": "Alignment failed"}), 500

    output = {"sentence": sentence, "words": []}

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
            comparison.append({
                "expected": expected_phones[i],
                "user": None,
                "match": False
            })

        for i in range(min_len, len(user_phones)):
            comparison.append({
                "expected": None,
                "user": user_phones[i],
                "match": False
            })

        stress_index = next((i for i, p in enumerate(cmu_phones) if '1' in p), -1)
        stress_ok = False
        if stress_index != -1 and stress_index < len(word_data.get('phones', [])):
            stressed = word_data['phones'][stress_index]
            if stressed['duration'] > 0.15:
                stress_ok = True

        output['words'].append({
            "word": word,
            "expected_phones": expected_phones,
            "user_phones": user_phones,
            "comparison": comparison,
            "stress_correct": stress_ok
        })

    return jsonify(output)

__all__ = ['phone_analyzer_bp']
