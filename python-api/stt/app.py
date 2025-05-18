from flask import Blueprint, request, jsonify
import speech_recognition as sr
from pydub import AudioSegment
import io

stt_bp = Blueprint('stt', __name__)

@stt_bp.route('/stt', methods=['POST'])
def speech_to_text():

    # Kiểm tra nếu có file audio và lang
    if 'audio' not in request.files or 'lang' not in request.form:
        return jsonify({'error': 'Missing audio file or lang parameter'}), 400
    
    audio_file = request.files['audio']
    lang = request.form['lang']
    
    # Chuyển đổi audio thành định dạng hỗ trợ nếu cần (như wav)
    try:
        audio = AudioSegment.from_file(audio_file)
        audio = audio.set_channels(1).set_frame_rate(16000)  # Chuyển thành mono và 16kHz
        audio = audio.export(format="wav")
        audio_bytes = io.BytesIO(audio.read())  # Chuyển sang byte stream để nhận dạng

        # Sử dụng SpeechRecognition để nhận dạng
        recognizer = sr.Recognizer()
        with sr.AudioFile(audio_bytes) as source:
            audio_data = recognizer.record(source)
            try:
                # Nhận diện giọng nói với ngôn ngữ yêu cầu
                text = recognizer.recognize_google(audio_data, language=lang)
                # Trả về text và thông số confidence
                response = {
                    'text': text,
                    'lang': lang,
                    'confidence': 1.0
                }
                return jsonify(response)  # Make sure the response is correctly returned as JSON

            except sr.UnknownValueError:
                return jsonify({'error': 'Speech recognition could not understand audio'}), 400
            except sr.RequestError:
                return jsonify({'error': 'Could not request results from Google Speech Recognition service'}), 500
    except Exception as e:
        return jsonify({'error': str(e)}), 500

