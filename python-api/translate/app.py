from flask import Blueprint, request, jsonify
from googletrans import Translator

translate_bp = Blueprint('translate', __name__)
translator = Translator()


@translate_bp.route('/translate', methods=['POST'])

def translate_text():
    data = request.json
    text = data.get('text')
    target_lang = data.get('target', 'vi')
    source_lang = data.get('source', 'en')
    
    if not text:
        return jsonify({"error": "No text provided"}), 400
    
    try:
        result = translator.translate(text, src=source_lang, dest=target_lang)
        return jsonify({"translatedText": result.text})
    except Exception as e:
        return jsonify({"error": str(e)}), 500
