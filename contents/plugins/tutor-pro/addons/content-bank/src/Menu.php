<?php
/**
 * Menu handler.
 *
 * @package TutorPro\ContentBank
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 3.7.0
 */

namespace TutorPro\ContentBank;

/**
 * Menu Class.
 *
 * @since 3.7.0
 */
class Menu {

	const PAGE_SLUG = 'tutor-content-bank';

	/**
	 * Register hooks and dependencies
	 *
	 * @since 3.7.0
	 *
	 * @param bool $register_hooks whether to register hooks or not.
	 */
	public function __construct( $register_hooks = true ) {
		if ( ! $register_hooks ) {
			return;
		}

		add_action( 'tutor_after_courses_admin_menu', array( $this, 'register_admin_menu' ), 9 );
	}

	/**
	 * Register admin menu.
	 *
	 * @since 3.7.0
	 *
	 * @return void
	 */
	public function register_admin_menu() {
		add_submenu_page( 'tutor', __( 'Content Bank', 'tutor-pro' ), __( 'Content Bank', 'tutor-pro' ) . sprintf( ' <span class="tutor-new-menu-badge">%s</span>', __( 'New', 'tutor-pro' ) ), 'manage_tutor_instructor', self::PAGE_SLUG, array( $this, 'admin_content_bank_view' ) );
	}

	/**
	 * Show admin content bank list page.
	 *
	 * @since 3.7.0
	 *
	 * @return void
	 */
	public function admin_content_bank_view() {
		include_once Helper::view_path( 'collection-list.php' );
	}
}
