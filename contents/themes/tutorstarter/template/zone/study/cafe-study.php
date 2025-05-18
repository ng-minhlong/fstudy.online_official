<?php

$servername = DB_HOST;
$username = DB_USER;
$password = DB_PASSWORD;
$dbname = DB_NAME;


$isLoggedIn = is_user_logged_in() ? 'true' : 'false';
echo "<script>var isLoggedIn = $isLoggedIn;</script>";
echo "<script> console.log('Is log in ?', $isLoggedIn) </script>";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$site_url = get_site_url();



echo "<script> 
var siteUrl = '" .$site_url . "';
</script>";

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafe Study Room</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        .banner {
            position: relative;
            height: 100%;
            width: 100%;
        }
        video {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: translate(-50%, -50%);
        }
        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 50px;
            z-index: 1;
        }
        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }
        .navbar ul li {
            margin: 0 10px;
        }
        .navbar ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
        }
        .logo {
            height: 50px;
        }
        .content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 1;
            width: 80%;
            max-width: 800px;
        }
        .content h1 {
            font-size: 50px;
            margin: 0;
        }
        .content button {
            padding: 10px 20px;
            font-size: 18px;
            margin: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: rgba(255,255,255,0.2);
            color: white;
        }
        .content button:hover {
            background-color: rgba(255,255,255,0.3);
        }
        .music-controls {
            margin-top: 20px;
            background-color: rgba(0,0,0,0.5);
            padding: 15px;
            border-radius: 10px;
        }
        .music-mode {
            margin: 10px 0;
        }
        #youtubePlayer {
            display: none;
        }
        .timer-container {
            margin-top: 20px;
            background-color: rgba(0,0,0,0.5);
            padding: 15px;
            border-radius: 10px;
        }
        .timer-display {
            font-size: 48px;
            margin: 10px 0;
        }
        .timer-buttons button {
            padding: 8px 15px;
            margin: 5px;
        }
        .lap-times {
            margin-top: 10px;
            max-height: 150px;
            overflow-y: auto;
        }
        .lap-time {
            padding: 5px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        .study-mode {
            margin-top: 20px;
            background-color: rgba(0,0,0,0.5);
            padding: 15px;
            border-radius: 10px;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: rgba(0,0,0,0.8);
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 100;
            display: none;
        }
    </style>
</head>
<body>
    <!-- YouTube player will be loaded here -->
    <div id="youtubePlayer"></div>
    
    <!-- Notification -->
    <div class="notification" id="notification"></div>
    
    <div class="banner">
        <video id="backgroundVideo" autoplay loop muted playsinline>
            Your browser does not support the video element.
        </video>
        

        <div class="content">
            <h1>Study Caffe</h1>
            <div>
                <button type="button" onclick="changeBackground()">Change Background Video</button>
                <button type="button" id="toggleMusicBtn" onclick="toggleMusic()">Pause Music</button>
                <button type="button" id="backHompageBtn" onclick="homepageRedirect()">Exit to Homepage</button>

            </div>
            <select id="videoSelect">
                <!-- Options will be populated dynamically by the script -->
            </select>

            <select id="musicSelect">
                <!-- Options will be populated dynamically by the script -->
            </select>
            
            <div class="music-controls">
                <div class="music-mode">
                    <label>Music Play Mode:</label>
                    <select id="musicMode" onchange="changeMusicMode()">
                        <option value="random">Random All</option>
                        <option value="genre">By Genre</option>
                        <option value="single">Single Track</option>
                    </select>
                </div>
                <div id="genreSelector" style="display: none;">
                    <label>Select Genre:</label>
                    <select id="genreSelect" onchange="changeGenre()">
                        <!-- Genres will be populated dynamically -->
                    </select>
                </div>
            </div>
            
            <div class="timer-container">
                <div class="timer-display" id="timerDisplay">00:00:00</div>
                <div class="timer-buttons">
                    <button id="startStopBtn" onclick="startStopTimer()">Start</button>
                    <button onclick="resetTimer()">Reset</button>
                    <button onclick="clearLaps()">Clear Laps</button>
                    <button onclick="saveToDatabase()">Save to Database</button>
                </div>
                <div class="lap-times" id="lapTimes"></div>
            </div>
            
            <div class="study-mode">
                <h3>Study Mode</h3>
                <label>
                    <input type="checkbox" id="pomodoroCheckbox" onchange="togglePomodoro()"> 
                    Enable Pomodoro (25 min work + 5 min break)
                </label>
                <div id="pomodoroStatus" style="margin-top: 10px;"></div>
            </div>
        </div>
    </div>
    
    <script>
        // Load YouTube IFrame API
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
        
        // Global variables
        var player;
        var videoSelect = document.getElementById('videoSelect');
        var musicSelect = document.getElementById('musicSelect');
        var musicModeSelect = document.getElementById('musicMode');
        var genreSelect = document.getElementById('genreSelect');
        var genreSelector = document.getElementById('genreSelector');
        var toggleMusicBtn = document.getElementById('toggleMusicBtn');
        var timerDisplay = document.getElementById('timerDisplay');
        var startStopBtn = document.getElementById('startStopBtn');
        var lapBtn = document.getElementById('lapBtn');
        var lapTimes = document.getElementById('lapTimes');
        var notification = document.getElementById('notification');
        var pomodoroCheckbox = document.getElementById('pomodoroCheckbox');
        var pomodoroStatus = document.getElementById('pomodoroStatus');
        
        // Extended data with genres (now using YouTube video IDs)
        const videos = [
            { name: 'Video 1', src: `${siteUrl}/contents/themes/tutorstarter/template/zone/study/assets/theme/theme 1 - cafe shop.mp4` }
          //  { name: 'Video 2', src: 'video2.mp4' },
            //{ name: 'Video 3', src: 'video3.mp4' }
        ];

        const musicTracks = [
            { name: 'White Noise for Sleep', src: 'CCnCMHNyny8', genre: 'white-noise' },
            { name: 'Rain Sounds for Sleeping', src: 'X49g9Ho3RRQ', genre: 'rain-sound' },

            // Lofi
            { name: 'Lofi Hip Hop Radio', src: 'jfKfPfyJRdk', genre: 'lofi' },
            { name: 'Chillhop Best', src: 'IU3yBo2szD8', genre: 'lofi' },
            { name: 'Chill Summer Lofi', src: 'kyqpSycLASY', genre: 'lofi' },
            { name: 'Night Time Cruise', src: 'baeChxHtmRs', genre: 'lofi' },
            { name: 'Endless Sunday Chillhop', src: 'D_uLM5i0Z4c', genre: 'lofi' },
            { name: 'Chillhop Spring 2023', src: 'Ve_uoTy4ggg', genre: 'lofi' },

            // Jazz
            { name: 'Relaxing Jazz Playlist', src: 'GTO_8OBG7_k', genre: 'jazz' },
            { name: 'Motown Jazz', src: 'c9xtkAZSK50', genre: 'jazz' },
            { name: 'Smooth Jazz Cafe', src: 'U3n31M81RpE', genre: 'jazz' },
            { name: 'Jazz Instrumental Classics', src: 'CQSHiA97e_k', genre: 'jazz' },
            { name: 'Mellow Smooth Jazz', src: 'kMJFJk59mgg', genre: 'jazz' },

            // Classical
            { name: '8 Hours Classical Piano', src: 'WLWJy1eXX2c', genre: 'classical' },
            { name: 'Beautiful Classical Piano', src: 'cGYyOY4XaFs', genre: 'classical' },
            { name: '4h Piano Study Music', src: 'PkjATKxRDO0', genre: 'classical' },
            { name: 'Mozart Reading Piano', src: 'VB6SIKl8Md0', genre: 'classical' },
            { name: 'Chopin Mozart Debussy', src: 'qZMgcyaBkvc', genre: 'classical' },
            { name: 'Best Classical Piano', src: 'RPxuhz02ITQ', genre: 'classical' },

            // Focus
            { name: '4h Study Focus Music', src: 'sjkrrmBnpGE', genre: 'lofi' },
            { name: '1h Focus Music', src: 'TuRQYo2YuB8', genre: 'lofi' },
            { name: 'Focus Music Background', src: '_4kHxtiuML0', genre: 'lofi' },
            { name: 'Concentration Music', src: 'lW-QNVuNkc8', genre: 'lofi' },
            { name: 'Relaxing Study Music', src: '_wX1C-uVvgk', genre: 'lofi' },
            { name: 'Ambient Focus Instrumental', src: 'mHgLompr_R0', genre: 'lofi' },

            // Rock
            { name: 'Royalty Free Rock', src: 'ha_rZuvr0dw', genre: 'rock' },
            { name: 'Hard Rock Compilation', src: 'PL5BUPBOTOMtl3uOXCFKCdVOrs7ghI1Gim', genre: 'rock' }, // playlist ID
            { name: 'Rock Background Music', src: 'OmRIeBtjIi8', genre: 'rock' },
            { name: 'Melodic Rock 90m', src: 'uNIpbNgobVE', genre: 'rock' },
            { name: 'Gaming Rock Instrumental', src: 'L6E0_WgJw68', genre: 'rock' },
            { name: 'Soft Rock Instrumental', src: 'FrbKBvyaVts', genre: 'rock' },

            // Pop
            { name: '3h Pop Instrumental', src: 'nJKeTfW3k9A', genre: 'pop' },
            { name: '2h Pop Classroom Music', src: 'X-yJhGcS7U4', genre: 'pop' },
            { name: 'Pop Jams Instrumental', src: 'Gk6EMP3gk40', genre: 'pop' },
            { name: 'Work Pop Mix', src: 'D9yYrHR8XoU', genre: 'pop' },
            { name: 'Study Pop Songs 2020', src: 'mX2L_lVSkOY', genre: 'pop' },
            { name: 'Best Pop Playlist 2023', src: 'efXy609wvwM', genre: 'pop' }
        ];


        // Variables to manage playback
        let currentVideoIndex = 0;
        let currentMusicIndex = 0;
        let currentPlaylist = [];
        let playedTracks = [];
        let currentMode = 'random';
        let currentGenre = 'all';
        let isMusicPlaying = true;
        
        // Timer variables
        let timerInterval;
        let startTime;
        let elapsedTime = 0;
        let isTimerRunning = false;
        let laps = [];
        
        // Pomodoro variables
        let pomodoroInterval;
        let pomodoroPhase = 'work'; // 'work' or 'break'
        let pomodoroTimeLeft = 25 * 60; // 25 minutes in seconds
        let isPomodoroRunning = false;
        
        // Get all unique genres from music tracks
        const allGenres = [...new Set(musicTracks.map(track => track.genre))];
        
        // Initialize YouTube player
        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtubePlayer', {
                height: '0',
                width: '0',
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange
                }
            });
        }
        
        function onPlayerReady(event) {
            // Start with random video and music
            playRandomVideo();
            setupMusicPlayback('random');
        }
        
        function onPlayerStateChange(event) {
            // When video ends, play next track (except for single mode)
            if (event.data === YT.PlayerState.ENDED && currentMode !== 'single') {
                playNextTrack();
            }
        }
        
        // Initialize the page
        function init() {
            // Populate video select dropdown
            videos.forEach((video, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = video.name;
                videoSelect.appendChild(option);
            });

            // Populate music select dropdown
            musicTracks.forEach((track, index) => {
                const option = document.createElement('option');
                option.value = index;
                option.textContent = track.name;
                musicSelect.appendChild(option);
            });
            
            // Populate genre select dropdown
            allGenres.forEach(genre => {
                const option = document.createElement('option');
                option.value = genre;
                option.textContent = genre.charAt(0).toUpperCase() + genre.slice(1);
                genreSelect.appendChild(option);
            });
        }
        
        // Play a random video
        function playRandomVideo() {
            currentVideoIndex = Math.floor(Math.random() * videos.length);
            const selectedVideo = videos[currentVideoIndex];
            const videoElement = document.getElementById('backgroundVideo');
            videoElement.src = selectedVideo.src;
            videoElement.play();
            videoSelect.value = currentVideoIndex;
        }
        
        // Set up music playback based on mode
        function setupMusicPlayback(mode) {
            currentMode = mode;
            playedTracks = [];
            
            if (mode === 'random') {
                currentPlaylist = [...musicTracks];
                shuffleArray(currentPlaylist);
                genreSelector.style.display = 'none';
            } else if (mode === 'genre') {
                genreSelector.style.display = 'block';
                if (currentGenre === 'all') {
                    currentGenre = allGenres[0];
                    genreSelect.value = currentGenre;
                }
                updateGenrePlaylist();
            } else if (mode === 'single') {
                currentPlaylist = [musicTracks[currentMusicIndex]];
                genreSelector.style.display = 'none';
            }
            
            playNextTrack();
        }
        
        // Update playlist when genre changes
        function updateGenrePlaylist() {
            currentPlaylist = musicTracks.filter(track => track.genre === currentGenre);
            shuffleArray(currentPlaylist);
            playedTracks = [];
            playNextTrack();
        }
        
        // Play the next track in the playlist
        function playNextTrack() {
            if (currentPlaylist.length === 0) {
                // If playlist is empty (shouldn't happen), reset it
                if (currentMode === 'genre') {
                    updateGenrePlaylist();
                } else {
                    setupMusicPlayback(currentMode);
                }
                return;
            }
            
            // If we've played all tracks, reset played list
            if (playedTracks.length === currentPlaylist.length) {
                playedTracks = [];
            }
            
            // Find a track that hasn't been played yet
            let nextTrack;
            for (let track of currentPlaylist) {
                if (!playedTracks.includes(track.src)) {
                    nextTrack = track;
                    break;
                }
            }
            
            // If all tracks have been played (shouldn't happen), pick first one
            if (!nextTrack) {
                nextTrack = currentPlaylist[0];
            }
            
            // Play the YouTube track
            player.loadVideoById(nextTrack.src);
            if (!isMusicPlaying) {
                player.pauseVideo();
            }
            
            // Add to played tracks
            playedTracks.push(nextTrack.src);
            
            // Update UI to show current track
            const trackIndex = musicTracks.findIndex(t => t.src === nextTrack.src);
            if (trackIndex !== -1) {
                musicSelect.value = trackIndex;
                currentMusicIndex = trackIndex;
            }
        }
        
        // Toggle music play/pause
        function toggleMusic() {
            isMusicPlaying = !isMusicPlaying;
            if (isMusicPlaying) {
                player.playVideo();
                toggleMusicBtn.textContent = 'Pause Music';
            } else {
                player.pauseVideo();
                toggleMusicBtn.textContent = 'Play Music';
            }
        }
        
        // Change music mode
        function changeMusicMode() {
            const mode = musicModeSelect.value;
            setupMusicPlayback(mode);
        }
        
        // Change genre
        function changeGenre() {
            currentGenre = genreSelect.value;
            updateGenrePlaylist();
        }

        
        // Change background video
        function changeBackground() {
            currentVideoIndex = (currentVideoIndex + 1) % videos.length;
            const selectedVideo = videos[currentVideoIndex];
            const videoElement = document.getElementById('backgroundVideo');
            videoElement.src = selectedVideo.src;
            videoElement.play();
            videoSelect.value = currentVideoIndex;
        }
        
        // Change music track
        function changeMusic() {
            if (currentMode === 'single') {
                currentMusicIndex = (currentMusicIndex + 1) % musicTracks.length;
                currentPlaylist = [musicTracks[currentMusicIndex]];
                playNextTrack();
            } else {
                // For other modes, just skip to next track
                playNextTrack();
            }
        }
        
        function startStopTimer() {
            if (isTimerRunning) {
                clearInterval(timerInterval);
                isTimerRunning = false;
                startStopBtn.textContent = 'Start';
                lapBtn.disabled = true;
                
                // Tự động ghi nhận Lap khi dừng
                const lapTime = elapsedTime;
                laps.push(lapTime);
                
                const lapItem = document.createElement('div');
                lapItem.className = 'lap-time';
                lapItem.textContent = `Session ${laps.length}: ${formatTime(lapTime)}`;
                lapTimes.appendChild(lapItem);
            }  else {
                // Khi ấn START
                startTime = Date.now() - elapsedTime;
                timerInterval = setInterval(updateTimer, 10);
                isTimerRunning = true;
                startStopBtn.textContent = 'Stop';
            }
        }
        
        function updateTimer() {
            const currentTime = Date.now();
            elapsedTime = currentTime - startTime;
            displayTime(elapsedTime);
        }
        
        function displayTime(time) {
            const date = new Date(time);
            const hours = date.getUTCHours().toString().padStart(2, '0');
            const minutes = date.getUTCMinutes().toString().padStart(2, '0');
            const seconds = date.getUTCSeconds().toString().padStart(2, '0');
            const milliseconds = Math.floor(date.getUTCMilliseconds() / 10).toString().padStart(2, '0');
            
            timerDisplay.textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        function recordLap() {
            const lapTime = elapsedTime;
            laps.push(lapTime);
            
            const lapItem = document.createElement('div');
            lapItem.className = 'lap-time';
            lapItem.textContent = `Lap ${laps.length}: ${formatTime(lapTime)}`;
            lapTimes.appendChild(lapItem);
        }
        
        function formatTime(time) {
            const date = new Date(time);
            const hours = date.getUTCHours().toString().padStart(2, '0');
            const minutes = date.getUTCMinutes().toString().padStart(2, '0');
            const seconds = date.getUTCSeconds().toString().padStart(2, '0');
            return `${hours}:${minutes}:${seconds}`;
        }
        
        function resetTimer() {
            clearInterval(timerInterval);
            isTimerRunning = false;
            elapsedTime = 0;
            displayTime(elapsedTime);
            startStopBtn.textContent = 'Start';
            lapBtn.disabled = true;
        }
        
        function clearLaps() {
            laps = [];
            lapTimes.innerHTML = '';
        }


        function saveToDatabase() {
           

            
            if (!isLoggedIn) {
                showNotification('You are not logged in. Please login to save your data.');
            } else {
                // In a real app, you would send data to your backend here
                showNotification('Data saved successfully!');
                console.log('Saving laps to database:', laps);
            }
        }
        
        function showNotification(message) {
            notification.textContent = message;
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }
        
        // Pomodoro functions
        function togglePomodoro() {
            if (pomodoroCheckbox.checked) {
                startPomodoro();
            } else {
                stopPomodoro();
            }
        }
        
        function startPomodoro() {
            isPomodoroRunning = true;
            pomodoroPhase = 'work';
            pomodoroTimeLeft = 25 * 60; // 25 minutes
            updatePomodoroDisplay();
            
            pomodoroInterval = setInterval(() => {
                pomodoroTimeLeft--;
                updatePomodoroDisplay();
                
                if (pomodoroTimeLeft <= 0) {
                    if (pomodoroPhase === 'work') {
                        // Switch to break
                        pomodoroPhase = 'break';
                        pomodoroTimeLeft = 5 * 60; // 5 minutes
                        showNotification('Time for a 5 minute break!');
                    } else {
                        // Switch back to work
                        pomodoroPhase = 'work';
                        pomodoroTimeLeft = 25 * 60; // 25 minutes
                        showNotification('Back to work! 25 minutes remaining.');
                    }
                }
            }, 1000);
        }
        
        function stopPomodoro() {
            isPomodoroRunning = false;
            clearInterval(pomodoroInterval);
            pomodoroStatus.textContent = '';
        }
        
        function updatePomodoroDisplay() {
            const minutes = Math.floor(pomodoroTimeLeft / 60);
            const seconds = pomodoroTimeLeft % 60;
            
            pomodoroStatus.textContent = `${pomodoroPhase === 'work' ? 'Work' : 'Break'} time: ${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        // Utility function to shuffle an array
        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        }
        function homepageRedirect(){
            window.location.href = `${siteUrl}`;
        }
        
        // Initialize when page loads
        window.onload = init;
    </script>
</body>
</html>