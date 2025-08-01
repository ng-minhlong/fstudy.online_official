<?php
/**
 * Handles registering header components
 *
 * @package Tutor_Starter
 */

namespace Tutor_Starter\Traits;

defined( 'ABSPATH' ) || exit;

/**
 * Header components trait
 */
trait Header_Components {

	/**
	 * Navbar toggler
	 */
	public static function navbar_toggler() {
		$toggler_html = '<li class="nav-close"><button class="btn-nav-close"><span class="close-btn">+</span></button></li>';
		return $toggler_html;
	}

	/**
	 * Tutor multi-column dropdown menu
	 */
	public static function tutor_multi_column_dropdown() {
		if ( ! class_exists( '\TUTOR\Utils' ) ) {
			return; // @todo: cross check
		}

		// $default_menus = apply_filters( 'tutor_dashboard/nav_items', self::default_menus() );
		$default_menus = self::default_menus();
		$current_user  = wp_get_current_user();
		$current_username = $current_user->user_login;
		global $wpdb;
		$site_url = get_site_url();
		echo "<script> 
		var site_url = '" .$site_url . "';
	  
	</script>";
	

		// Fetch user token from the user_token table
		$sql = "SELECT token, updated_time, token_use_history, token_practice FROM user_token WHERE username = %s";
		$user_token_result = $wpdb->get_row($wpdb->prepare($sql, $current_username));

		if ($user_token_result) {
			$user_token = $user_token_result->token;
			$user_token_practice = $user_token_result->token_practice;

			$updated_time = $user_token_result->updated_time;

		} else {
			$user_token = '0';
			$user_token_practice = '0';
			$updated_time = 'Đang cập nhật';

		}


		?>
	<style>
		.profile-name {
			display: flex;
			flex-direction: column;
			align-items: flex-start;
			gap: 5px;
		}

		.token-wrapper {
			display: flex;
			gap: 15px;
			margin-bottom: 10px;
		}

		.token-item {
			display: flex;
			align-items: center;
			gap: 5px;
			font-size: 14px;
			color: #333;
		}

		.token-icon {
			width: 20px;
			height: 20px;
			object-fit: contain;
		}

		.group-btn {
			display: flex;
			gap: 10px;
			margin-top: 10px;
		}

		.button {
			padding: 5px 10px;
			background-color: #4CAF50;
			color: white;
			text-decoration: none;
			border-radius: 3px;
			font-size: 13px;
		}

		.button:hover {
			background-color: #45a049;
		}

		.group-btn button:hover {
			background-color: #005bb5;
		}
		.darkmode-toggle {
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.darkmode-toggle span {
			font-size: 16px;
			line-height: 1;
		}

		/* Chỉnh kích thước toggle */
		.darkmode-toggle .wp-dark-mode-switch {
			transform: scale(0.8); /* Điều chỉnh kích thước, chỉnh số 0.8 tùy ý */
		}
		
        
        .user-role-badge:hover {
            background-color: #e0e0e0;
            max-width: 200px;
            overflow: visible;
        }
		.user-role-wrapper {
			display: flex;
			flex-wrap: wrap;
			gap: 5px;
		}
		.user-role-badge {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 12px;
			background-color: rgba(0, 0, 0, 0.1);
			border: 2px solid;
			font-size: 12px;
			font-weight: 500;
			white-space: nowrap;
		}
		



	</style>
		<div class="tutor-header-profile-photo">
			<?php 
			$profile_pic = tutor_utils()->get_tutor_avatar( get_current_user_id() ); 
			?>
		</div><!-- .tutor-header-profile-photo -->
		<div class="tutor-header-profile-content">
			<div class="tutor-header-profile-submenu">
				<?php echo $profile_pic; ?>
				<span role="button" class="d-flex align-items-center gap-1 fs-5 py-3 text-black-80" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
					<?php echo esc_html( ucfirst( $current_user->display_name ) ); ?>
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M5.11285 6.29697L8.17285 9.3503L11.2329 6.29697L12.1729 7.23697L8.17285 11.237L4.17285 7.23697L5.11285 6.29697Z" fill="currentColor"></path>
					</svg>
				</span>
				<!-- <div class="tutor-header-profile-name">
				</div> -->
				<!-- <div class="tutor-header-submenu-icon tutor-icon-icon-light-down-line tutor-font-size-20 tutor-text-400">
				</div> -->
			</div>
		</div>
		<div class="tutor-header-submenu">
    <?php if ( is_user_logged_in() ) : ?>
        <div class="tutor-submenu-links">
            <ul>
            <div class="profile-name">
                <!--<?php echo esc_html( ucfirst( $current_user->display_name ) ); ?> -->
                <div class="token-wrapper">
                    <span class="token-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/dist/images/token_test.png" alt="Test Token" class="token-icon">
                        <?php echo esc_html( $user_token ); ?>
                    </span>
                    <span class="token-item">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/dist/images/token_practice.png" alt="Practice Token" class="token-icon">
                        <?php echo esc_html( $user_token_practice ); ?>
                    </span>
                </div>
                
                <!-- Thêm phần hiển thị role -->
                <div class="user-role-wrapper" id="user-role-display-wrapper">
    <!-- Badges sẽ được thêm vào đây -->
    <span class="user-role-badge">Loading role...</span>
</div>
                
                <div class="group-btn">
                    <a href="<?php echo $site_url; ?>/dashboard/user_token/" class="button">Xem chi tiết</a>
                    <a href="<?php echo $site_url; ?>/dashboard/buy_token/" class="button">Mua token</a>
                </div>
            </div>

            <?php
            foreach ( $default_menus as $menu_key => $menu_item ) {
                $menu_title = $menu_item;
                $menu_link  = tutor_utils()->get_tutor_dashboard_page_permalink( $menu_key );
                if ( is_array( $menu_item ) ) {
                    $menu_title = tutor_utils()->array_get( 'title', $menu_item );
                    if ( isset( $menu_item['url'] ) ) {
                        $menu_link = $menu_item['url'];
                    }
                }
                if ( 'index' === $menu_key ) {
                    $menu_key = '';
                }
                ob_start();?>
                <li>
                    <a href="<?php echo esc_url( $menu_link ) ?>"> 
                        <span class="tutor-dashboard-menu-item-icon <?php echo $menu_item['icon'];  ?>"></span>
                        <?php echo esc_html( $menu_title ); ?>
                    </a>
                </li>
                <?php
                $menu_list_item = ob_get_clean();
                echo $menu_list_item;
            }
            ?>
            <div class="dark-mode">
                <div class="darkmode-toggle">
                    <span>Darkmode</span> 
                    <?php echo do_shortcode('[wp_dark_mode style="10"]'); ?>
                </div>
            </div>
            </ul>
        </div>
		 <script>
        (function() {
			const userId = <?php echo get_current_user_id(); ?>;
			const userName = '<?php echo esc_js($current_user->user_login); ?>';
			const restNonce = '<?php echo wp_create_nonce('wp_rest'); ?>';
			const roleWrapper = document.getElementById('user-role-display-wrapper');
			
			// Màu sắc cho viền badge (trừ default)
			const badgeBorderColors = [
				'#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', 
				'#98D8C8', '#F06292', '#7986CB', '#9575CD'
			];
			
			// Hàm tạo màu ngẫu nhiên từ danh sách
			function getRandomBorderColor() {
				return badgeBorderColors[Math.floor(Math.random() * badgeBorderColors.length)];
			}
			
			// Hàm rút gọn text nếu quá dài
			function shortenText(text, maxLength = 20) {
				return text.length <= maxLength ? text : text.slice(0, maxLength) + '...';
			}

			// Hàm tạo badge
			function createRoleBadge(roleName, isDefault = false) {
				const badge = document.createElement('span');
				badge.className = 'user-role-badge';
				badge.textContent = shortenText(roleName);
				badge.title = roleName; // Tooltip đầy đủ
				
				// Thiết lập style
				if (isDefault) {
					badge.style.borderColor = '#CCCCCC'; // Màu xám cho default
				} else {
					badge.style.borderColor = getRandomBorderColor();
				}
				
				return badge;
			}

			async function checkUserRole() {
				try {
					const getRoleResponse = await fetch(`${site_url}/api/v1/user/get_role`, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': restNonce
						},
						body: JSON.stringify({ user_id: userId })
					});
					
					const roleData = await getRoleResponse.json();
					
					if (roleData.status === 'success' && Array.isArray(roleData.roles)) {
						const roles = roleData.roles;
						roleWrapper.innerHTML = ''; // Xóa nội dung cũ
						
						// Lọc ra các role khác Default
						const specialRoles = roles.filter(role => role.id_role !== 1001);
						
						if (specialRoles.length > 0) {
							// Hiển thị các role đặc biệt
							specialRoles.forEach(role => {
								const badge = createRoleBadge(
									`${role.role} (${role.expired_date})`, 
									false // Không phải default
								);
								roleWrapper.appendChild(badge);
							});
						} else {
							// Chỉ hiển thị Default nếu không có role nào khác
							const defaultRole = roles.find(role => role.id_role === 1001);
							if (defaultRole) {
								const badge = createRoleBadge(
									`${defaultRole.role} (${defaultRole.expired_date})`, 
									true // Là default
								);
								roleWrapper.appendChild(badge);
							}
						}
					}
					else if (roleData.status === 'not_found') {
						await updateUserRole(userId, userName);
						await checkUserRole(); // Kiểm tra lại sau khi update
					}
					
				} catch (error) {
					console.error('Error checking user role:', error);
					roleWrapper.innerHTML = '<span class="user-role-badge">Error</span>';
				}
			}
			
			
            
            async function updateUserRole(userId, userName) {
                try {
                    const response = await fetch(`${site_url}/api/v1/user/update_role`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-WP-Nonce': restNonce
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            user_name: userName,
                            id_role: 1001
                        })
                    });
                    
                    const result = await response.json();
                    console.log('Update role result:', result);
                    
                } catch (error) {
                    console.error('Error updating user role:', error);
                }
            }
            
            // Gọi hàm kiểm tra khi DOM sẵn sàng
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', checkUserRole);
            } else {
                checkUserRole();
            }
        })();
        </script>

			<?php else : ?>
			<?php endif; ?>
		</div>
		<?php
	}
	/**
	 * Filtered nav items based on capabilities
	 *
	 * @return array
	 */
	public static function filtered_nav() {
		if ( ! class_exists( '\TUTOR\Utils' ) ) {
			return;
		}

		$instructor_menu = apply_filters( 'tutor_dashboard/instructor_nav_items', tutor_utils()->instructor_menus() );
		$common_navs     = array(
			'dashboard-page' => array(
				'title' => __( 'Dashboard', 'tutorstarter' ),
				'icon'  => 'tutor-icon-settings',
			),

			
			

			'settings'       => array(
				'title' => __( 'Account Settings', 'tutorstarter' ),
				'icon'  => 'tutor-icon-settings',
			),
			'logout'         => array(
				'title' => __( 'Đăng xuất', 'tutorstarter' ),
				'icon'  => 'tutor-icon-signout',
			),
		);

		$all_nav_items = array_merge( $instructor_menu, $common_navs );

		foreach ( $all_nav_items as $nav_key => $nav_item ) {

			if ( is_array( $nav_item ) ) {

				if ( isset( $nav_item['show_ui'] ) && ! tutor_utils()->array_get( 'show_ui', $nav_item ) ) {
					unset( $all_nav_items[ $nav_key ] );
				}

				if ( isset( $nav_item['auth_cap'] ) && ! current_user_can( $nav_item['auth_cap'] ) ) {
					unset( $all_nav_items[ $nav_key ] );
				}
			}
		}

		return $all_nav_items;
	}

	/**
	 * Check role
	 *
	 * @return bool
	 */
	public static function is_user_priviledged() {
		$user_is_priviledged = false;
		$current_user        = wp_get_current_user();
		$predefined_roles    = apply_filters(
			'tutor_user_is_priviledged',
			array(
				'administrator',
				'tutor_instructor',
			)
		);

		if ( array_intersect( $current_user->roles, $predefined_roles ) ) {
			$user_is_priviledged = true;
		} else {
			$user_is_priviledged = false;
		}

		return $user_is_priviledged;
	}

	/**
	 * Default Menus
	 */
	public static function default_menus() {
		return array(
			'' => array(
				'title' => __( 'Dashboard', 'tutorstarter' ),
				'icon'  => 'tutor-icon-dashboard',
			),
			'analysis_test' => array(
				'title' => __( 'Kết quả luyện thi', 'tutorstarter' ),
				'icon'  => 'fa-solid fa-square-poll-vertical',
			),
			'notion' => array(
				'title' => __( 'Notion', 'tutorstarter' ),
				'icon'  => 'fa-solid fa-note-sticky',
			),

			'notification' => array(
				'title' => __( 'Thông báo', 'tutorstarter' ),
				'icon'  => 'fa-solid fa-envelope',
			),
			
			'my-profile'       => array(
				'title' => __( 'My profile', 'tutorstarter' ),
				'icon'  => 'tutor-icon-user-bold',
			),
			'settings'       => array(
				'title' => __( 'Account Settings', 'tutorstarter' ),
				'icon'  => 'tutor-icon-gear',
			),
			'logout'         => array(
				'title' => __( 'Đăng xuất', 'tutorstarter' ),
				'icon'  => 'tutor-icon-signout',
			),
		);
	}
}