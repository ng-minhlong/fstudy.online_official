from flask import Blueprint, request, send_file, jsonify
from gtts import gTTS
from io import BytesIO

tts_bp = Blueprint('tts', __name__)


SUPPORTED_LANGUAGES = {
    'en': 'en',     # English
    'fr': 'fr',     # French
    'zh': 'zh-cn',  # Chinese
    'vi': 'vi',     # Vietnamese
    'es': 'es',     # Spanish
    'de': 'de',     # German
    'ko': 'ko',     # Korean
    'ja': 'ja',     # Japanese
    'ru': 'ru'      # Russian
}
@tts_bp.route('/tts', methods=['GET'])

def text_to_speech():
    text = request.args.get('text')
    lang = request.args.get('lang')

    if not text:
        return jsonify({'error': 'Missing text parameter'}), 400
    if not lang:
        return jsonify({'error': 'Missing lang parameter'}), 400

    # Chuyển đổi mã ngôn ngữ từ frontend sang mã gTTS
    gtts_lang = SUPPORTED_LANGUAGES.get(lang.lower())
    if not gtts_lang:
        return jsonify({'error': f'Language {lang} not supported'}), 400

    try:
        tts = gTTS(text=text, lang=gtts_lang)
        audio_file = BytesIO()
        tts.write_to_fp(audio_file)
        audio_file.seek(0)
        return send_file(
            audio_file,
            mimetype='audio/mpeg',
            as_attachment=True,
            download_name='speech.mp3'
        )
    except Exception as e:
        return jsonify({'error': str(e)}), 500

