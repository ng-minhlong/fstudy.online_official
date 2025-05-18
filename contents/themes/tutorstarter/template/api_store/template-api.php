<?php
/*
 * Template Name: Video Player FROM STUDYAI
 * Template Post Type: video
 
 */


$post_id = get_the_ID();
$user_id = get_current_user_id();




// Database credentials (update with your own database details)
$servername = "localhost";
$username = "root";
$password = ""; // No password by default
$dbname = "wordpress";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy giá trị custom_video_id từ URL
global $wp_query;
$custom_video_id = $wp_query->get('custom_video_id');

// Kiểm tra nếu custom_video_id tồn tại
if ($custom_video_id) {
  global $wpdb;

  // Thực hiện truy vấn để lấy id_video_orginal 
  $sql = "SELECT id_video_orginal FROM video_link_generator WHERE converted_video_id = %s";
  $result = $wpdb->get_row($wpdb->prepare($sql, $custom_video_id));

  if ($result) {
      

  // Đóng kết nối
  $conn->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Custom Video Player</title>
  <style>
    body {
      margin: 0;
      background-color: black;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    #video-container {
      position: relative;
      width: 100%;
      height: 100%;
      background: black;
      overflow: hidden;
    }

    video {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    #controls {
      position: absolute;
      bottom: 20px;
      left: 20px;
      right: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: rgba(0, 0, 0, 0.7);
      padding: 10px;
      border-radius: 10px;
    }

    .left-controls,
    .right-controls {
      display: flex;
      align-items: center;
    }

    .left-controls button,
    .right-controls button {
      color: white;
      background: none;
      border: none;
      margin: 0 5px;
      font-size: 20px;
      cursor: pointer;
    }

    .slider {
      width: 100px;
      margin: 0 5px;
    }

    .time {
      color: white;
      font-size: 14px;
    }

    #progress {
      width: 100%;
      height: 5px;
      background-color: #555;
      border-radius: 3px;
    }

    #progress-bar {
      height: 100%;
      background-color: #2196f3;
      border-radius: 3px;
    }

    #popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.8);
      color: white;
      padding: 20px;
      border-radius: 10px;
      font-size: 18px;
    }
  </style>
</head>
<body>
  <div id="video-container">
    <video id="video" preload="metadata"></video>
    <div id="controls">
      <div class="left-controls">
        <button class="button" id="play-pause">Play</button>
        <button class="button" id="rewind"><<</button>
        <button class="button" id="fast-forward">>></button>
        <div class="time" id="current-time">00:00</div>
      </div>
      <div style="flex-grow: 1; margin: 0 10px;">
        <div id="progress">
          <div id="progress-bar"></div>
        </div>
      </div>
      <div class="right-controls">
        <input type="range" class="slider" id="volume" value="1" min="0" max="1" step="0.1">
        <select class="dropdown" id="playback-rate">
          <option value="0.5">0.5x</option>
          <option value="1" selected>1x</option>
          <option value="1.5">1.5x</option>
          <option value="2">2x</option>
        </select>
        <button class="button" id="fullscreen">Fullscreen</button>
      </div>
    </div>
  </div>

  <div id="popup">Authorize by Long</div>

  <script>



  const driveID = "<?php echo addslashes($result->id_video_orginal); ?>"; // Embedding PHP variable
  const apiKey = "AIzaSyBeOZP4dsO9ptcykPCdvieqdhxDh1CIkcU";

    const videoElement = document.getElementById('video');

    const video = document.getElementById('video');
    const playPauseBtn = document.getElementById('play-pause');
    const rewindBtn = document.getElementById('rewind');
    const fastForwardBtn = document.getElementById('fast-forward');
    const progressBar = document.getElementById('progress-bar');
    const progressContainer = document.getElementById('progress');
    const currentTimeDisplay = document.getElementById('current-time');
    const volumeSlider = document.getElementById('volume');
    const playbackRateSelect = document.getElementById('playback-rate');
    const fullscreenBtn = document.getElementById('fullscreen');
    const popup = document.getElementById('popup');

   const videoSrc = `https://www.googleapis.com/drive/v3/files/${driveID}?alt=media&key=${apiKey}`;
//    const videoSrc = `google.com`;

    videoElement.src = videoSrc;


    // Play/Pause toggle
    playPauseBtn.addEventListener('click', () => {
      if (video.paused) {
        video.play();
        playPauseBtn.textContent = 'Pause';
      } else {
        video.pause();
        playPauseBtn.textContent = 'Play';
      }
    });

    // Update current time and progress bar
    video.addEventListener('timeupdate', () => {
      const currentTime = video.currentTime;
      const duration = video.duration;
      const progress = (currentTime / duration) * 100;
      progressBar.style.width = `${progress}%`;

      // Format time
      const minutes = Math.floor(currentTime / 60);
      const seconds = Math.floor(currentTime % 60);
      currentTimeDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    });

    // Rewind
    rewindBtn.addEventListener('click', () => {
      video.currentTime -= 10;
    });

    // Fast forward
    fastForwardBtn.addEventListener('click', () => {
      video.currentTime += 10;
    });

    // Seek video
    progressContainer.addEventListener('click', (e) => {
      const rect = progressContainer.getBoundingClientRect();
      const clickPosition = (e.clientX - rect.left) / rect.width;
      video.currentTime = clickPosition * video.duration;
    });

    // Volume control
    volumeSlider.addEventListener('input', () => {
      video.volume = volumeSlider.value;
    });

    // Playback speed control
    playbackRateSelect.addEventListener('change', () => {
      video.playbackRate = playbackRateSelect.value;
    });

    // Fullscreen toggle
    fullscreenBtn.addEventListener('click', () => {
      if (document.fullscreenElement) {
        document.exitFullscreen();
      } else {
        video.parentElement.requestFullscreen();
      }
    });

    // Right-click context menu
    document.addEventListener('contextmenu', (e) => {
      e.preventDefault();
      popup.style.display = 'block';
      setTimeout(() => {
        popup.style.display = 'none';
      }, 2000);
    });
  </script>
</body>
</html>


<?php
}
else {
  echo 'Lỗi: Video ID không tồn tại trong hệ thống.';
}


} else {
    // Xử lý khi custom_video_id không có trong URL
    echo 'Không tìm thấy ID video.';
}