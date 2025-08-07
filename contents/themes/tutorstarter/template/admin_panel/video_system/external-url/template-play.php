<?php
/*
 * Template Name: Video Player FROM STUDYAI
 * Template Post Type: video
 */
$video_encode = get_query_var('custom_video_id');
$video_id = base64_decode($video_encode);

if ($video_id) {
    $site_url = get_site_url();
    $user_id = get_current_user_id();
    $current_user = wp_get_current_user();
    $current_username = $current_user->user_login;
    $username = $current_username;
    $current_user_id = $current_user->ID;
    global $wpdb;
    $table_name =  'lessons_management';
    $course_id = $wpdb->get_var($wpdb->prepare("SELECT course_id FROM $table_name WHERE external_url = %s", $video_id));

    
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Video Player</title>
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
  <style>
    html, body { margin:0; padding:0; height:100%; overflow:hidden; }
    #player-container { position: relative; width:100vw; height:100vh; background:#000; }
    /* Buttons */
    #choose-server-btn, #report-error-btn { 
      position: fixed; 
      top: 20px; 
      z-index: 10001; 
      padding: 10px 15px; 
      border: none; 
      cursor: pointer; 
      border-radius: 6px; 
      font-weight: bold; 
      display: flex;
      align-items: center;
      justify-content: center;
    }
    #choose-server-btn { 
      right: 20px; 
      background: #007bff; 
      color: #fff; 
      min-width: 130px;
    }
    #report-error-btn { 
      right: 160px; 
      background: rgba(255, 255, 255, 0.2); 
      width: 40px;
      height: 40px;
      padding: 8px;
    }
    #report-error-btn svg { 
      width: 24px; 
      height: 24px; 
      stroke: #e74c3c; 
      transition: transform .2s; 
    }
    #report-error-btn:hover { background: rgba(255, 255, 255, 0.3); }
    #report-error-btn:hover svg { transform: scale(1.1); }
    
    /* Server Popup */
    #server-popup, #report-popup { 
      display: none; 
      position: fixed; 
      background: #1a1a1a; 
      border: 1px solid #333; 
      border-radius: 8px; 
      padding: 15px; 
      box-shadow: 0 4px 15px rgba(0,0,0,0.5); 
      z-index: 10000; 
      color: #fff;
    }
    #server-popup { 
      top: 70px; 
      right: 20px; 
      min-width: 200px; 
    }
    #report-popup { 
      top: 70px; 
      right: 160px; 
      width: 260px; 
    }
    #report-popup h4 { 
      margin: 0 0 12px; 
      color: #e74c3c; 
      font-size: 16px;
      padding-bottom: 8px;
      border-bottom: 1px solid #333;
    }
    .error-option { 
      display: flex; 
      align-items: center; 
      margin: 8px 0; 
      padding: 6px;
      border-radius: 4px;
      transition: background .2s;
    }
    .error-option:hover { background: #333; }
    .error-option input { margin-right: 10px; }
    .submit-report { 
      width: 100%; 
      padding: 10px; 
      margin-top: 12px; 
      background: #e74c3c; 
      color: #fff; 
      border: none; 
      border-radius: 6px; 
      cursor: pointer; 
      font-weight: bold;
      transition: background .2s;
    }
    .submit-report:hover { background: #c0392b; }
    .submit-report:disabled { 
      opacity: .5; 
      cursor: not-allowed; 
      background: #7f8c8d;
    }
    
    /* Overlay texts */
    .overlay { 
      position: absolute; 
      padding: 8px 15px; 
      background: rgba(0,0,0,.7); 
      color: #fff; 
      font-size: 14px; 
      z-index: 10000; 
      border-radius: 6px; 
      max-width: 80%;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    #promo-top-left { top: 20px; left: 20px; }
    #promo-bottom-left { bottom: 20px; left: 20px; }
    #user-info { 
      bottom: 20px; 
      right: 20px; 
      background: rgba(0, 0, 0, 0.8);
      font-family: monospace;
      font-size: 13px;
      padding: 10px 15px;
    }
    
    /* Server list */
    .server-option {
      display: block;
      width: 100%;
      padding: 8px 12px;
      margin: 5px 0;
      background: #333;
      color: #fff;
      border: none;
      border-radius: 4px;
      text-align: left;
      cursor: pointer;
      transition: background .2s;
    }
    .server-option:hover {
      background: #007bff;
    }
  </style>
</head>
<body>
  <div id="player-container"></div>
  
  <!-- Buttons -->
  <button id="choose-server-btn">Chọn Server</button>
  <button id="report-error-btn" title="Báo lỗi">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
      <line x1="12" y1="9" x2="12" y2="13"/>
      <line x1="12" y1="17" x2="12.01" y2="17"/>
    </svg>
  </button>

  <!-- Popups -->
  <div id="server-popup">
    <p style="font-weight:bold; margin:0 0 10px; font-size:16px;">Chọn Server:</p>
    <div id="server-list"></div>
  </div>
  
  <div id="report-popup">
    <h4>Báo lỗi Server: <span id="report-server-name"></span></h4>
    <div class="error-option"><input type="checkbox" value="Không có tiếng"> Không có tiếng</div>
    <div class="error-option"><input type="checkbox" value="Không có video"> Không có video</div>
    <div class="error-option"><input type="checkbox" value="Video bị mờ"> Video bị mờ</div>
    <div class="error-option"><input type="checkbox" value="Link die"> Link die</div>
    <button id="submit-report-btn" class="submit-report" disabled>Gửi báo cáo</button>
  </div>
  

  <!-- Overlay texts -->
  <div id="promo-top-left" class="overlay">Khám phá kho tàng khóa học online tại <?php echo esc_html($site_url); ?></div>
  <div id="promo-bottom-left" class="overlay" style="display:none;">Khám phá kho tàng khóa học online tại <?php echo esc_html($site_url); ?></div>
  <div id="user-info" class="overlay"></div>

  <script src="https://www.youtube.com/iframe_api"></script>
  <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
  <script>
    const COURSE_ID = "<?php echo esc_js($course_id); ?>";
    const VIDEO_ID = "<?php echo esc_js($video_id); ?>";
    const VIDEO_ID_DECODE = "<?php echo esc_js($video_encode); ?>";

    const CurrentuserID = "<?php echo esc_js($current_user_id); ?>";
    const Currentusername = "<?php echo esc_js($current_username); ?>";
    const site_url = "<?php echo esc_js(get_site_url()); ?>";
    let player, currentServer = 'abyss';
    let userIP = 'Đang lấy IP...';

    // Lấy địa chỉ IP của người dùng
    async function getUserIP() {
      try {
        const response = await fetch('https://api.ipify.org?format=json');
        const data = await response.json();
        return data.ip;
      } catch (error) {
        console.error('Lỗi khi lấy IP:', error);
        return 'Không xác định';
      }
    }

    // Cập nhật thông tin người dùng
    async function updateUserInfo() {
      const userInfoEl = document.getElementById('user-info');
      if (!userInfoEl) return;
      
      userIP = await getUserIP();
      userInfoEl.innerHTML = `
        <div>User: ${Currentusername}</div>
        <div>ID: ${CurrentuserID}</div>
        <div>IP: ${userIP}</div>
      `;
    }

    document.addEventListener('DOMContentLoaded', async () => {
      // Cập nhật thông tin người dùng và IP
      updateUserInfo();
      
      // Load data và hiển thị player
      const resp = await fetch(`${site_url}/api/v1/video-data-2/${VIDEO_ID_DECODE}`);
      const data = await resp.json();
      
      if (data.abyss_status === 'Live') {
        renderIframe(`https://short.icu/${data.abyss_slug}`);
        currentServer = 'abyss';
      } else if (data.youtube_status === 'Live') {
        renderYoutube(data.youtube_id);
        currentServer = 'youtube';
      }
      else if (data.external_url_status === 'Live') {
        renderExternalUrl(data.external_url);
        currentServer = 'External Url';
      }
      
      setupServerButton(data);
      setupReportButton(data);
      initOverlays();
    });

    async function renderYoutube(youtubeId) {
        if (player && player.destroy) player.destroy();
        document.getElementById('error-overlay').style.display = 'none';
        document.getElementById('loading-overlay').style.display = 'flex';

        try {
        const response = await fetch(`<?php echo URL_PYTHON_API; ?>/get-link-lesson`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: `https://www.youtube.com/watch?v=${youtubeId}` })
        });

        const data = await response.json();
        if (!data.video_url) throw new Error(data.error || "Không lấy được link video");

        document.getElementById('player-container').innerHTML = `
            <video id="player" controls playsinline>
            <source src="${data.video_url}" type="video/mp4">
            </video>
            <div id="error-overlay" style="...">...</div>
        `;

        player = new Plyr('#player', {
            autoplay: true,
            controls: [
            'play-large', 'rewind', 'play', 'fast-forward', 'progress', 'current-time',
            'mute', 'volume', 'captions', 'settings', 'fullscreen'
            ]
        });


        player.play();
        } catch (err) {
        console.error('Lỗi khi lấy link MP4:', err);
        document.getElementById('error-overlay').innerHTML = '❌ Không thể phát video từ YouTube.<br>' + err.message;
        document.getElementById('error-overlay').style.display = 'flex';
        } finally {
        document.getElementById('loading-overlay').style.display = 'none';
        }
  }



  async function renderExternalUrl(external_url){
    if (player && player.destroy) player.destroy();
    document.getElementById('error-overlay').style.display = 'none';
    document.getElementById('loading-overlay').style.display = 'flex';

    try {
      const response = await fetch(`<?php echo URL_PYTHON_API; ?>/get-link-lesson`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ url: `${external_url}` })
      });

      const data = await response.json();
      if (!data.video_url) throw new Error(data.error || "Không lấy được link video");

      document.getElementById('player-container').innerHTML = `
        <video id="player" controls playsinline>
          <source src="${data.video_url}" type="video/mp4">
        </video>
        <div id="error-overlay" style="...">...</div>
      `;

      player = new Plyr('#player', {
        autoplay: true,
        controls: [
          'play-large', 'rewind', 'play', 'fast-forward', 'progress', 'current-time',
          'mute', 'volume', 'captions', 'settings', 'fullscreen'
        ]
      });


      player.play();
    } catch (err) {
      console.error('Lỗi khi lấy link MP4:', err);
      document.getElementById('error-overlay').innerHTML = '❌ Không thể phát video từ YouTube.<br>' + err.message;
      document.getElementById('error-overlay').style.display = 'flex';
    } finally {
      document.getElementById('loading-overlay').style.display = 'none';
    }
  }


  function renderIframe(url) {
    if (player && player.destroy) player.destroy();
    document.getElementById('loading-overlay').style.display = 'flex';
    document.getElementById('error-overlay').style.display = 'none';

    // Simulate slight delay
    setTimeout(() => {
      document.getElementById('player-container').innerHTML = `
        <iframe src="${url}" 
                style="width:100%;height:100%;border:none;" 
                allowfullscreen></iframe>
        <div id="error-overlay" style="...">...</div>
      `;
      document.getElementById('loading-overlay').style.display = 'none';
    }, 500); // Optional delay
  }

    function setupServerButton(data) {
      const btn = document.getElementById('choose-server-btn');
      const popup = document.getElementById('server-popup');
      const list = document.getElementById('server-list');
      
      // Tạo danh sách server
      const servers = [
        ['youtube', data.youtube_id, 'YouTube'],
        ['abyss', data.abyss_slug, 'Abyss'],
        ['external_url', data.external_url, 'External Url'],
        ['bunny', data.bunny_slug, 'Bunny']
      ].filter(([key, id]) => id && data[`${key}_status`] === 'Live');
      
      list.innerHTML = servers.map(([key, id, label], i) => `
        <button class="server-option" 
                data-key="${key}" 
                data-id="${id}">
          Server ${i + 1} (${label})
        </button>
      `).join('');
      
      // Xử lý sự kiện nút chọn server
      btn.addEventListener('click', e => {
        e.stopPropagation();
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
        document.getElementById('report-popup').style.display = 'none';
      });
      
      // Xử lý click ngoài để đóng popup
      document.addEventListener('click', () => {
        popup.style.display = 'none';
      });
      
      // Ngăn popup đóng khi click bên trong
      popup.addEventListener('click', e => e.stopPropagation());
      
      // Xử lý chọn server
      list.addEventListener('click', e => {
        if (!e.target.matches('.server-option')) return;
        
        e.stopPropagation();
        const key = e.target.dataset.key;
        const id = e.target.dataset.id;
        popup.style.display = 'none';
        
        if (key === 'youtube') {
          renderYoutube(id);
          currentServer = 'youtube';
        } else if (key === 'abyss') {
          renderIframe(`https://short.icu/${id}`);
          currentServer = 'abyss';
        } else if (key === 'external_url') {
          renderExternalUrl(id);
          currentServer = 'external_url';
        } else {
          renderIframe(`https://your-bunny-url.com/${id}`);
          currentServer = 'bunny';
        }
      });
    }

    function setupReportButton(data) {
      const btn = document.getElementById('report-error-btn');
      const popup = document.getElementById('report-popup');
      const serverName = document.getElementById('report-server-name');
      const checkboxes = popup.querySelectorAll('input[type="checkbox"]');
      const submit = document.getElementById('submit-report-btn');
      
      // Xử lý sự kiện nút báo lỗi
      btn.addEventListener('click', e => {
        e.stopPropagation();
        serverName.textContent = currentServer;
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
        document.getElementById('server-popup').style.display = 'none';
      });
      
      // Ngăn popup đóng khi click bên trong
      popup.addEventListener('click', e => e.stopPropagation());
      
      // Xử lý checkbox
      checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
          const isChecked = [...checkboxes].some(c => c.checked);
          submit.disabled = !isChecked;
        });
        
        // Ngăn sự kiện lan ra ngoài
        cb.addEventListener('click', e => e.stopPropagation());
      });
      
      // Xử lý gửi báo cáo
      submit.addEventListener('click', async e => {
        e.stopPropagation();
        
        const errors = [...checkboxes]
          .filter(c => c.checked)
          .map(c => c.value)
          .join(', ');
        
        submit.disabled = true;
        submit.textContent = 'Đang gửi...';
        
        const payload = {
          report_by_username: Currentusername,
          report_by_user_id: CurrentuserID,
          course_id: COURSE_ID,
          source_type: currentServer,
          source: currentServer === 'youtube' 
            ? data.youtube_id 
            : (currentServer === 'abyss' ? data.abyss_slug : data.bunny_slug),
          error_log: errors
        };
        
        try {
          const res = await fetch(`${site_url}/api/v1/report-broken-link`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          
          const j = await res.json();
          if (j.success) {
            submit.textContent = '✓ Đã gửi';
            setTimeout(() => {
              popup.style.display = 'none';
              submit.textContent = 'Gửi báo cáo';
              checkboxes.forEach(c => c.checked = false);
              submit.disabled = true;
            }, 1500);
          } else {
            throw new Error('Gửi báo cáo thất bại');
          }
        } catch (error) {
          submit.textContent = '❌ Lỗi';
          setTimeout(() => {
            submit.textContent = 'Gửi báo cáo';
            submit.disabled = false;
          }, 2000);
        }
      });
    }

    function initOverlays() {
      const promoTL = document.getElementById('promo-top-left');
      const promoBL = document.getElementById('promo-bottom-left');
      const showDuration = 10000; // 10 giây
      
      // Hiển thị luân phiên các overlay
      const cycles = [
        () => {
          promoTL.style.display = 'block';
          setTimeout(() => promoTL.style.display = 'none', showDuration);
        },
        () => {
          promoBL.style.display = 'block';
          setTimeout(() => promoBL.style.display = 'none', showDuration);
        }
      ];
      
      // Bắt đầu chu kỳ
      let cycleIndex = 0;
      cycles[cycleIndex]();
      
      setInterval(() => {
        cycleIndex = (cycleIndex + 1) % cycles.length;
        cycles[cycleIndex]();
      }, 300000); // 5 phút
    }

    // Chặn chuột trái
    document.addEventListener('mousedown', e => {
      if (e.button === 0) e.preventDefault();
    });
  </script>
  <?php wp_footer(); ?>

  <div id="loading-overlay" style="
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.85);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 18px;
  font-weight: bold;
  z-index: 99999;
  display: none;
">Đang tải video, vui lòng chờ...</div>
<div id="error-overlay" style="
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.8);
  color: red;
  display: none;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  z-index: 10000;
  text-align: center;
  padding: 20px;
"></div>

</body>
</html>
<?php
} else {
    echo '<p>Không tìm thấy video</p>';
}
?>