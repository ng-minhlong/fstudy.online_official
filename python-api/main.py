from flask import Flask
from flask_cors import CORS
from tts.app import tts_bp
from stt.app import stt_bp
from translate.app import translate_bp
from pronunciation_analyzer.app import phone_analyzer_bp
from compiler_code.app import complier_analyzer_bp
from shadowingApi.app import shadowingResult_bp
from getLinkLesson.app import get_link_lesson__bp

app = Flask(__name__)
CORS(app)

app.register_blueprint(tts_bp)
app.register_blueprint(stt_bp)
app.register_blueprint(translate_bp)

app.register_blueprint(phone_analyzer_bp)
app.register_blueprint(complier_analyzer_bp)
app.register_blueprint(shadowingResult_bp)
app.register_blueprint(get_link_lesson__bp)


if __name__ == '__main__':
    app.run(debug=True)

    #app.run(host='0.0.0.0', port=5000, debug=True)
