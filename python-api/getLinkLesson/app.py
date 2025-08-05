from flask import Flask, request, jsonify, render_template, Blueprint, send_file
import yt_dlp
get_link_lesson__bp = Blueprint('get-link-lesson', __name__)
@get_link_lesson__bp.route('/get-link-lesson', methods=['POST'])
def get_video():
    data = request.json
    url = data.get('url')
    
    if not url:
        return jsonify({'error': 'URL is required'}), 400

    try:
        # Ưu tiên chất lượng <= 720p, container mp4, có cả audio/video
        ydl_opts = {
            'format': 'bestvideo[height<=720][ext=mp4]+bestaudio[ext=m4a]/best[height<=720][ext=mp4]',
            'quiet': True,
            'no_warnings': True,
            'noplaylist': True,
            'youtube_include_dash_manifest': False,
        }

        with yt_dlp.YoutubeDL(ydl_opts) as ydl:
            info = ydl.extract_info(url, download=False)

            # Nếu trả về 1 url duy nhất
            if 'url' in info:
                return jsonify({'video_url': info['url']})

            # Xử lý danh sách định dạng
            formats = info.get('formats', [])
            candidate_formats = [
                fmt for fmt in formats if (
                    fmt.get('ext') == 'mp4' and
                    fmt.get('vcodec') != 'none' and
                    fmt.get('acodec') != 'none' and
                    fmt.get('height') is not None and
                    fmt['height'] <= 720
                )
            ]

            # Ưu tiên định dạng có độ phân giải cao nhất <= 720p
            if candidate_formats:
                best_format = sorted(candidate_formats, key=lambda x: x['height'], reverse=True)[0]
                return jsonify({'video_url': best_format['url']})

            # Fallback nếu không có định dạng <= 720p
            for fmt in formats:
                if (
                    fmt.get('ext') == 'mp4' and
                    fmt.get('vcodec') != 'none' and
                    fmt.get('acodec') != 'none'
                ):
                    return jsonify({'video_url': fmt['url']})

            return jsonify({'error': 'No suitable format found'}), 404

    except Exception as e:
        return jsonify({'error': str(e)}), 500

