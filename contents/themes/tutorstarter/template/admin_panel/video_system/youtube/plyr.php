
<?php
/*
 * Template Name: Video Player FROM STUDYAI
 * Template Post Type: video
 */
$video_id = get_query_var('custom_video_id');

if ($video_id) {

?>
<!-- YouTube Player API Script -->
<script src="https://www.youtube.com/iframe_api"></script>

<script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

<!-- Video Embed Container -->
<div id="player-container" class="plyr__video-embed" data-plyr-provider="youtube" data-plyr-embed-id="<?php echo esc_attr($video_id); ?>"></div>

<!-- Initialize Plyr -->
<script>
  let player;

  // This function is called when the YouTube Player API is ready
  function onYouTubePlayerAPIReady() {
    const plyrInstance = new Plyr('#player-container');

    // Get the YouTube Player from Plyr
    player = plyrInstance.source;

    // Setup YouTube Player API events
    player.on('ready', onPlayerReady);
    player.on('statechange', onPlayerStateChange);
  }

  // This function will be called when the player is ready
  function onPlayerReady() {
    player.cueVideoById({
      videoId: '<?php echo esc_attr($video_id); ?>',
      suggestedQuality: 'hd720'
    });
  }

  // This function handles state changes in the YouTube player
  function onPlayerStateChange(event) {
    if (event.data === 0) { // Video has finished playing
      player.cueVideoById({
        videoId: '<?php echo esc_attr($video_id); ?>',
        suggestedQuality: 'hd720'
      });
    }
  }

  // Load YouTube Player API when ready
  window.onYouTubePlayerAPIReady = onYouTubePlayerAPIReady;
</script>

<style>
  /* Adjust iframe size within Plyr container */
  .plyr__video-embed iframe {
    top: 0;
    height: 100%;
  }

  /* Custom CSS for player state and poster */
  .plyr--paused {
    /* Force poster to render on pause */
    background: #000; /* or use a custom color or image */
  }

  .plyr__poster {
    /* Force poster to cover the video */
    background-size: cover;
    z-index: 2;
  }

  /* Additional custom styling for iframe */
  .plyr__video-embed iframe {
    top: -50%;
    height: 200%;
  }
</style>


<?php wp_footer(); ?>
<?php
} else {
    echo '<p>Không tìm thấy video</p>';
}
?> 