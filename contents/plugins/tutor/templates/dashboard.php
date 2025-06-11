<?php
/**
 * Template for displaying frontend dashboard
 *
 * @package Tutor\Templates
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.4.3
 */

$is_by_short_code = isset( $is_shortcode ) && true === $is_shortcode;
if ( ! $is_by_short_code && ! defined( 'OTLMS_VERSION' ) ) {
	tutor_utils()->tutor_custom_header();
}

global $wp_query;

$dashboard_page_slug = '';
$dashboard_page_name = '';
if ( isset( $wp_query->query_vars['tutor_dashboard_page'] ) && $wp_query->query_vars['tutor_dashboard_page'] ) {
	$dashboard_page_slug = $wp_query->query_vars['tutor_dashboard_page'];
	$dashboard_page_name = $wp_query->query_vars['tutor_dashboard_page'];
}
/**
 * Getting dashboard sub pages
 */
if ( isset( $wp_query->query_vars['tutor_dashboard_sub_page'] ) && $wp_query->query_vars['tutor_dashboard_sub_page'] ) {
	$dashboard_page_name = $wp_query->query_vars['tutor_dashboard_sub_page'];
	if ( $dashboard_page_slug ) {
		$dashboard_page_name = $dashboard_page_slug . '/' . $dashboard_page_name;
	}
}
$dashboard_page_name = apply_filters( 'tutor_dashboard_sub_page_template', $dashboard_page_name );

$user_id                   = get_current_user_id();
$user                      = get_user_by( 'ID', $user_id );
$enable_profile_completion = tutor_utils()->get_option( 'enable_profile_completion' );
$is_instructor             = tutor_utils()->is_instructor();

// URLS.
$current_url  = tutor()->current_url;
$footer_url_1 = trailingslashit( tutor_utils()->tutor_dashboard_url( $is_instructor ? 'my-courses' : '' ) );
$footer_url_2 = trailingslashit( tutor_utils()->tutor_dashboard_url( $is_instructor ? 'question-answer' : 'my-quiz-attempts' ) );

// Footer links.
$footer_links = array(
	array(
		'title'      => $is_instructor ? __( 'My Courses', 'tutor' ) : __( 'Dashboard', 'tutor' ),
		'url'        => $footer_url_1,
		'is_active'  => $footer_url_1 == $current_url,
		'icon_class' => 'ttr tutor-icon-dashboard',
	),
	array(
		'title'      => $is_instructor ? __( 'Q&A', 'tutor' ) : __( 'Quiz Attempts', 'tutor' ),
		'url'        => $footer_url_2,
		'is_active'  => $footer_url_2 == $current_url,
		'icon_class' => $is_instructor ? 'ttr  tutor-icon-question' : 'ttr tutor-icon-quiz-attempt',
	),
	array(
		'title'      => __( 'Menu', 'tutor' ),
		'url'        => '#',
		'is_active'  => false,
		'icon_class' => 'ttr tutor-icon-hamburger-o tutor-dashboard-menu-toggler',
	),
);

//do_action( 'tutor_dashboard/before/wrap' );
?>



<div class="tutor-wrap tutor-wrap-parent tutor-dashboard tutor-frontend-dashboard tutor-dashboard-student tutor-pb-80">
	<div class="tutor-container">
		<div class="tutor-row tutor-d-flex tutor-justify-between tutor-frontend-dashboard-header">
			<div class="tutor-header-left-side tutor-dashboard-header tutor-col-md-6 tutor-d-flex tutor-align-center" style="border: none;">
				
				<!--<div class="tutor-dashboard-header-avatar">
					<?php
					tutor_utils()->get_tutor_avatar( $user_id, 'xl', true )
					?>
				</div>
				

				<div class="tutor-user-info tutor-ml-24">
					<?php
					$instructor_rating = tutor_utils()->get_instructor_ratings( $user->ID );

					if ( current_user_can( tutor()->instructor_role ) ) {
						?>
						<div class="tutor-fs-4 tutor-fw-medium tutor-color-black tutor-dashboard-header-username">
							<?php echo esc_html( $user->display_name ); ?>
						</div>
						<div class="tutor-dashboard-header-stats">
							<div class="tutor-dashboard-header-ratings">
								<?php tutor_utils()->star_rating_generator_v2( $instructor_rating->rating_avg, $instructor_rating->rating_count, true ); ?>
							</div>
						</div>
						<?php
					} else {
						?>
						<div class="tutor-dashboard-header-display-name tutor-color-black">
							<div class="tutor-fs-5 tutor-dashboard-header-greetings">
								<?php esc_html_e( 'Xin chào', 'tutor' ); ?>,
							</div>
							<div class="tutor-fs-4 tutor-fw-medium tutor-dashboard-header-username">
								<?php echo esc_html( $user->display_name ); ?>
							</div>
						</div>
						<?php
					}
					?> 
				</div>-->
			</div>
			<div class="tutor-header-right-side tutor-col-md-6 tutor-d-flex tutor-justify-end tutor-mt-20 tutor-mt-md-0">
				<div class="tutor-d-flex tutor-align-center">
					
					<?php
					do_action( 'tutor_dashboard/before_header_button' );
					$instructor_status  = tutor_utils()->instructor_status( 0, false );
					$instructor_status  = is_string( $instructor_status ) ? strtolower( $instructor_status ) : '';
					$rejected_on        = get_user_meta( $user->ID, '_is_tutor_instructor_rejected', true );
					$info_style         = 'vertical-align: middle; margin-right: 7px;';
					$info_message_style = 'display:inline-block; color:#7A7A7A; font-size: 15px;';

					ob_start();
					if ( tutor_utils()->get_option( 'enable_become_instructor_btn' ) ) {
						?>
						<a id="tutor-become-instructor-button" class="tutor-btn tutor-btn-outline-primary" href="<?php echo esc_url( tutor_utils()->instructor_register_url() ); ?>">
							<i class="tutor-icon-user-bold"></i> &nbsp; <?php esc_html_e( 'Become an instructor', 'tutor' ); ?>
						</a>
						<?php
					}
					$become_button = ob_get_clean();

					if ( current_user_can( tutor()->instructor_role ) ) {
						$course_type = tutor()->course_post_type;
						?>
						<?php
						/**
						 * Render create course button based on free & pro
						 *
						 * @since v2.0.7
						 */
						if ( function_exists( 'tutor_pro' ) ) :
							?>
							<?php do_action( 'tutor_course_create_button' ); ?>
							<?php else : ?>
							<a href="#" class="tutor-btn tutor-btn-outline-primary tutor-create-new-course">
								<i class="tutor-icon-plus-square tutor-my-n4 tutor-mr-8"></i>
								<?php esc_html_e( 'Create a New Course', 'tutor' ); ?>
							</a>
					<?php endif; ?>
						<?php
					} elseif ( 'pending' === $instructor_status ) {
						$on = get_user_meta( $user->ID, '_is_tutor_instructor', true );
						$on = gmdate( 'd F, Y', $on );
						echo '<span style="' . esc_attr( $info_message_style ) . '">
                                    <i class="dashicons dashicons-info tutor-color-warning" style=" ' . esc_attr( $info_style ) . '"></i>',
						esc_html__( 'Your Application is pending as of', 'tutor' ), ' <b>', esc_html( $on ), '</b>',
						'</span>';
					} elseif ( $rejected_on || 'blocked' !== $instructor_status ) {
						echo $become_button; //phpcs:ignore --data escaped above
					}
					?>
				</div>
			</div>
		</div>

		<div class="tutor-row tutor-frontend-dashboard-maincontent">
			<div class="tutor-col-12 tutor-col-md-4 tutor-col-lg-3 tutor-dashboard-left-menu">
				<ul class="tutor-dashboard-permalinks">
					<div class="tutor-dashboard-menu-item">
						<a href="#" class="tutor-dashboard-menu-item-link tutor-fs-6 tutor-color-black tutor-dashboard-menu-toggler">
							<span class='tutor-dashboard-menu-item-icon'>
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<line x1="3" y1="12" x2="21" y2="12"></line>
									<line x1="3" y1="6" x2="21" y2="6"></line>
									<line x1="3" y1="18" x2="21" y2="18"></line>
								</svg>
							</span>
							<span class='tutor-dashboard-menu-item-text tutor-ml-12'>
								<?php esc_html_e('Toggle Menu', 'tutor'); ?>
							</span>
						</a>
					</div>

					
					<?php
					$dashboard_pages = tutor_utils()->tutor_dashboard_nav_ui_items();
					// get reviews settings value.
					$disable = ! get_tutor_option( 'enable_course_review' );
					foreach ( $dashboard_pages as $dashboard_key => $dashboard_page ) {
						/**
						 * If not enable from settings then quit
						 *
						 *  @since v2.0.0
						 */
						if ( $disable && 'reviews' === $dashboard_key ) {
							continue;
						}

						$menu_title = $dashboard_page;
						$menu_link  = tutor_utils()->get_tutor_dashboard_page_permalink( $dashboard_key );
						$separator  = false;
						$menu_icon  = '';

						if ( is_array( $dashboard_page ) ) {
							$menu_title     = tutor_utils()->array_get( 'title', $dashboard_page );
							$menu_icon_name = tutor_utils()->array_get( 'icon', $dashboard_page, ( isset( $dashboard_page['icon'] ) ? $dashboard_page['icon'] : '' ) );
							if ( $menu_icon_name ) {
								$menu_icon = "<span class='{$menu_icon_name} tutor-dashboard-menu-item-icon'></span>";
							}
							// Add new menu item property "url" for custom link.
							if ( isset( $dashboard_page['url'] ) ) {
								$menu_link = $dashboard_page['url'];
							}
							if ( isset( $dashboard_page['type'] ) && 'separator' === $dashboard_page['type'] ) {
								$separator = true;
							}
						}
						if ( $separator ) {
							echo '<li class="tutor-dashboard-menu-divider"></li>';
							if ( $menu_title ) {
								?>
								<li class='tutor-dashboard-menu-divider-header'>
									<?php echo esc_html( $menu_title ); ?>
								</li>
								<?php
							}
						} else {
							$li_class = "tutor-dashboard-menu-{$dashboard_key}";
							if ( 'index' === $dashboard_key ) {
								$dashboard_key = '';
							}
							$active_class    = $dashboard_key == $dashboard_page_slug ? 'active' : '';
							$data_no_instant = 'logout' == $dashboard_key ? 'data-no-instant' : '';
							$menu_link = apply_filters( 'tutor_dashboard_menu_link', $menu_link, $menu_title );
							?>
							<li class='tutor-dashboard-menu-item <?php echo esc_attr( $li_class . ' ' . $active_class ); ?>'>
								<a <?php echo esc_html( $data_no_instant ); ?> href="<?php echo esc_url( $menu_link ); ?>" class='tutor-dashboard-menu-item-link tutor-fs-6 tutor-color-black'>
									<?php
									echo wp_kses(
										$menu_icon,
										tutor_utils()->allowed_icon_tags()
									);
									?>
									<span class='tutor-dashboard-menu-item-text tutor-ml-12'>
										<?php echo esc_html( $menu_title ); ?>
									</span>
								</a>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>

			<div class="tutor-col-12 tutor-col-md-8 tutor-col-lg-9">
				<div class="tutor-dashboard-content">
					<?php

					if ( $dashboard_page_name ) {
						do_action( 'tutor_load_dashboard_template_before', $dashboard_page_name );

						/**
						 * Load dashboard template part from other location
						 *
						 * This filter is basically added for adding templates from respective addons
						 *
						 * @since version 1.9.3
						 */
						$other_location      = '';
						$from_other_location = apply_filters( 'load_dashboard_template_part_from_other_location', $other_location );

						if ( '' == $from_other_location ) {
							tutor_load_template( 'dashboard.' . $dashboard_page_name );
						} else {
							// Load template from other location full abspath.
							include_once $from_other_location;
						}

						do_action( 'tutor_load_dashboard_template_after', $dashboard_page_name );
					} else {
						tutor_load_template( 'dashboard.dashboard' );
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<div id="tutor-dashboard-footer-mobile">
		<div class="tutor-container">
			<div class="tutor-row">
				<?php foreach ( $footer_links as $link_item ) : ?>
					<a class="tutor-col-4 <?php echo $link_item['is_active'] ? 'active' : ''; ?>" href="<?php echo esc_url( $link_item['url'] ); ?>">
						<i class="<?php echo esc_attr( $link_item['icon_class'] ); ?>"></i>
						<span><?php echo esc_html( $link_item['title'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>


<style>
#chat-container{
	display: none !important;
}
#toggle-chat{
	display: none !important;
}
/* CSS cho toggle menu */
.tutor-dashboard-menu-toggle {
    display: none;
    position: fixed;
    left: 0;
    top: 100px;
    z-index: 1000;
    background: var(--tutor-primary-color);
    color: white;
    border: none;
    border-radius: 0 4px 4px 0;
    padding: 10px;
    cursor: pointer;
    box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.tutor-dashboard-menu-toggle:hover {
    background: var(--tutor-primary-hover-color);
}

.tutor-dashboard-left-menu {
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.tutor-dashboard-left-menu.collapsed {
    width: 90px !important;
    min-width: 70px !important;
}

.tutor-dashboard-left-menu.collapsed .tutor-dashboard-menu-item-text {
    display: none;
}

.tutor-dashboard-left-menu.collapsed .tutor-dashboard-menu-item-link {
    justify-content: center;
    padding: 10px 0;
}

.tutor-dashboard-left-menu.collapsed .tutor-dashboard-menu-item-icon {
    margin-right: 0;
    font-size: 1.5rem;
}

.tutor-dashboard-left-menu.collapsed .tutor-dashboard-menu-divider-header {
    display: none;
}

.tutor-dashboard-left-menu.collapsed .tutor-dashboard-menu-divider {
    margin: 10px auto;
    width: 30px;
}

.tutor-dashboard-menu-toggler {
    cursor: pointer;
}


/* Responsive adjustments */
@media (max-width: 991.98px) {
    .tutor-dashboard-menu-toggle {
        display: block;
    }
    
    .tutor-dashboard-left-menu {
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 999;
        background: white;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        transform: translateX(0);
        transition: transform 0.3s ease;
    }
    
    .tutor-dashboard-left-menu.collapsed {
        transform: translateX(-100%);
    }
    
    .tutor-dashboard-content {
        transition: margin-left 0.3s ease;
    }
    
    .tutor-dashboard-left-menu:not(.collapsed) + .tutor-col-md-8 {
        /*margin-left: 25%;*/
    }
}
.tutor-dashboard-left-menu.collapsed + .tutor-col-md-8 {
    width: 90% !important;
    flex: 0 0 90% !important;
    max-width: 90% !important;
}

/* Adjust the left menu width when collapsed */
.tutor-dashboard-left-menu.collapsed {
    width: 10% !important;
    min-width: 70px !important;
}

/* For mobile view */
@media (max-width: 991.98px) {
    .tutor-dashboard-left-menu:not(.collapsed) + .tutor-col-md-8 {
        /*margin-left: 25%;
        width: 75% !important;
        flex: 0 0 75% !important;
        max-width: 75% !important;*/
    }
    
    .tutor-dashboard-left-menu.collapsed + .tutor-col-md-8 {
        width: 100% !important;
        flex: 0 0 100% !important;
        max-width: 100% !important;
        margin-left: 0;
    }
}

</style>

<script>
	jQuery(document).ready(function($) {
    // Thêm data-title cho các menu item
    $('.tutor-dashboard-menu-item').each(function() {
        const text = $(this).find('.tutor-dashboard-menu-item-text').text().trim();
        $(this).attr('data-title', text);
    });

    // Toggle menu function
    function toggleDashboardMenu() {
        $('.tutor-dashboard-left-menu').toggleClass('collapsed');
        
        // Trên mobile, sử dụng class 'active' thay vì 'collapsed'
        if ($(window).width() <= 991.98) {
            $('.tutor-dashboard-left-menu').toggleClass('active');
        }
        
        // Lưu trạng thái vào localStorage
        localStorage.setItem('tutorDashboardMenuCollapsed', $('.tutor-dashboard-left-menu').hasClass('collapsed'));
    }
    
    // Xử lý click nút toggle
    $('.tutor-dashboard-menu-toggler').on('click', function(e) {
        e.preventDefault();
        toggleDashboardMenu();
    });
    
    // Kiểm tra trạng thái đã lưu
    if (localStorage.getItem('tutorDashboardMenuCollapsed') === 'true') {
        $('.tutor-dashboard-left-menu').addClass('collapsed');
    } else {
        $('.tutor-dashboard-left-menu').removeClass('collapsed');
    }
    
    // Thêm nút toggle cho mobile
    if ($('.tutor-dashboard-menu-toggle').length === 0) {
        $('body').append(
            '<button class="tutor-dashboard-menu-toggle">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
            '<line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line>' +
            '</svg>' +
            '</button>'
        );
    }
    
    // Xử lý click nút toggle mobile
    $(document).on('click', '.tutor-dashboard-menu-toggle', function() {
        $('.tutor-dashboard-left-menu').toggleClass('active');
    });
    
    // Xử lý resize window
    $(window).on('resize', function() {
        if ($(window).width() > 991.98) {
            $('.tutor-dashboard-left-menu').removeClass('active');
        }
    });
});
</script>




<?php do_action( 'tutor_dashboard/after/wrap' ); ?>

<?php
if ( ! $is_by_short_code && ! defined( 'OTLMS_VERSION' ) ) {
	tutor_utils()->tutor_custom_footer();
}
