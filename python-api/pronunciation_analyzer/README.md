# 🗣 Pronunciation Analyzer (Flask + Gentle + CMUdict)

## ✅ Features
- Upload hoặc ghi âm audio
- Nhận diện câu nói tiếng Anh
- Phân tích từng từ, từng âm (đúng/sai/thừa/thiếu)
- Kiểm tra trọng âm

## 🚀 How to run

1. Cài dependencies:
```bash
pip install flask requests nltk pydub
sudo apt install ffmpeg  # hoặc brew install ffmpeg nếu dùng Mac
```

2. Chạy Gentle (phải cài Docker):
```bash
docker run -it -p 8765:8765 lowerquality/gentle
```

3. Chạy Flask app:
```bash
python app.py
```

4. Mở trình duyệt: http://localhost:5000

## 🎤 Ghi âm trực tiếp?
Có luôn! Nhấn nút "🎙 Ghi âm", sau đó "⏹ Dừng" → hệ thống sẽ lấy audio để phân tích.

---
Enjoy phát âm chuẩn như người bản xứ 😄