<?php
global $wpdb;
$site_url = get_site_url();
echo '
<script>
  var siteUrl = "' . $site_url .'";
</script>
';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- displays site properly based on user's device -->
    <link
      rel="icon"
      type="image/png"
      sizes="32x32"
      href="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/favicon-32x32.png"
    />
    <title>Frontend Mentor | Interactive comments section</title>
    <link rel="stylesheet" href="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/style.css" />
  </head>
  <body>
    <template class="reply-input-template">
    <div class="reply-input container">
      <img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/avatars/image-juliusomo.webp" alt="" class="usr-img">
      <textarea class="cmnt-input" placeholder="Add a comment..."></textarea>
      <button class="bu-primary">SEND</button>
    </div>
  </template>

  <template class="comment-template">
    <div class="comment-wrp">
      <div class="comment container">
        <div class="c-score">
          <img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/icon-plus.svg" alt="plus" class="score-control score-plus">
          <p class="score-number">5</p>
          <img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/icon-minus.svg" alt="minus" class="score-control score-minus">
        </div>
        <div class="c-controls">
          <a  class="delete"><img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/icon-delete.svg" alt="" class="control-icon">Delete</a>
          <a  class="edit"><img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/icon-edit.svg" alt="" class="control-icon">Edit</a>
          <a  class="reply"><img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/icon-reply.svg" alt="" class="control-icon">Reply</a>
        </div>
        <div class="c-user">
          <img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/avatars/image-maxblagun.webp" alt="" class="usr-img">
          <p class="usr-name">maxblagun</p>
          <p class="cmnt-at">2 weeks ago</p>    
        </div>
        <p class="c-text">
          <span class="reply-to"></span>
          <span class="c-body"></span>
        </p>
      </div><!--comment-->
      <div class="replies comments-wrp">
      </div><!--replies-->
    </div>
  </template>
<main>
  <div class="comment-section">

    <div class="comments-wrp">

    </div> <!--commentS wrapper-->
  <div class="reply-input container">
      <img src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/images/avatars/image-juliusomo.webp" alt="" class="usr-img">
      <textarea class="cmnt-input" placeholder="Add a comment..."></textarea>
      <button class="bu-primary">SEND</button>
    </div> <!--reply input-->
  </div> <!--comment sectio-->
  
  <div class="modal-wrp invisible">
    <div class="modal container">
      <h3>Delete comment</h3>
      <p>Are you sure you want to delete this comment? This will remove the comment and cant be undone</p>
      <button class="yes">YES,DELETE</button>
      <button class="no">NO,CANCEL</button>
    </div>
  </div>
</main>
    <script src="<?php echo $site_url ?>/contents/themes/tutorstarter/custom-comment/app.js"></script>
  </body>
</html>
