<?php
/**
 * Display Video or Iframe Based on External URL
 *
 * @package Tutor\Templates
 * @subpackage Single\Video
 * @author Themeum
 * @link https://themeum.com
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$site_url = get_site_url();

$video_info   = tutor_utils()->get_video_info();
$poster       = tutor_utils()->avalue_dot( 'poster', $video_info );
$poster_url   = $poster ? wp_get_attachment_url( $poster ) : '';
$external_url = tutor_utils()->array_get( 'source_external_url', $video_info );
$external_url_encoded = base64_encode( $external_url );

do_action( 'tutor_lesson/single/before/video/external_url' );

if ( $video_info && $external_url ) :
	$ext = strtolower( pathinfo( parse_url( $external_url, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
	?>

	<div class="tutor-video-player">
		<input type="hidden" id="tutor_video_tracking_information" value="<?php echo esc_attr( json_encode( $jsonData ?? null ) ); ?>">
		<div class="loading-spinner" aria-hidden="true"></div>

		<?php if ( $ext === 'mp4' ) : ?>
			<video poster="<?php echo esc_url( $poster_url ); ?>" class="tutorPlayer" playsinline controls>
				<source src="<?php echo esc_url( $external_url ); ?>" type="video/mp4">
			</video>
		<?php else : ?>
			<div class="tutor-ratio tutor-ratio-16x9 tutor-iframe-container">
			<iframe src="<?php echo  $site_url; ?>/storage/media/video/e/<?php echo esc_attr( $external_url_encoded ); ?>" frameborder="0" allowfullscreen allowtransparency allow="autoplay"></iframe> 

				
			<!--<iframe 
				src="<?php echo esc_url( $external_url ); ?>" 
				frameborder="0" 
				allowfullscreen 
				class="tutor-iframe-player"
				loading="lazy"
			></iframe> -->
			</div>
		<?php endif; ?>
	</div>

<?php endif; ?>

<?php do_action( 'tutor_lesson/single/after/video/external_url' ); ?>
