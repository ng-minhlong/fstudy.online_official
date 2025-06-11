<?php
/**
 * Frontend Dashboard Template
 *
 * @package Tutor\Templates
 * @subpackage Dashboard
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 1.4.3
 */

use Tutor\Models\CourseModel;
use Tutor\Models\WithdrawModel;
echo '

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">


';

	$user_id           = get_current_user_id();
	$enrolled_course   = tutor_utils()->get_enrolled_courses_by_user( $user_id, array( 'private', 'publish' ) );
	$completed_courses = tutor_utils()->get_completed_courses_ids_by_user();
	$total_students    = tutor_utils()->get_total_students_by_instructor( $user_id );
	$my_courses        = CourseModel::get_courses_by_instructor( $user_id, CourseModel::STATUS_PUBLISH );
	$earning_sum       = WithdrawModel::get_withdraw_summary( $user_id );
	$active_courses    = tutor_utils()->get_active_courses_by_user( $user_id );

	$enrolled_course_count  = $enrolled_course ? $enrolled_course->post_count : 0;
	$completed_course_count = count( $completed_courses );
	$active_course_count    = is_object( $active_courses ) && $active_courses->have_posts() ? $active_courses->post_count : 0;

	$status_translations = array(
		'publish' => __( 'Published', 'tutor' ),
		'pending' => __( 'Pending', 'tutor' ),
		'trash'   => __( 'Trash', 'tutor' ),
	);

$placeholder_img     = tutor()->url . 'assets/images/placeholder.svg';
$courses_in_progress = tutor_utils()->get_active_courses_by_user( get_current_user_id() );
$siteurl = get_site_url();
$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$current_username = $current_user->user_login;

if ( tutor_utils()->get_option( 'enable_profile_completion' ) ) {
	$profile_completion = tutor_utils()->user_profile_completion();
	$is_instructor      = tutor_utils()->is_instructor( null, true );
	$total_count        = count( $profile_completion );
	$incomplete_count   = count(
		array_filter(
			$profile_completion,
			function( $data ) {
				return ! $data['is_set'];
			}
		)
	);
	$complete_count     = $total_count - $incomplete_count;

	if ( $is_instructor ) {
		if ( isset( $total_count ) && isset( $incomplete_count ) && $incomplete_count <= $total_count ) {
			?>
			<div class="tutor-profile-completion tutor-card tutor-px-32 tutor-py-24 tutor-mb-40">
				<div class="tutor-row tutor-gx-0">
					<div class="tutor-col-lg-7 <?php echo tutor_utils()->is_instructor() ? 'tutor-profile-completion-content-admin' : ''; ?>">
						<div class="tutor-fs-5 tutor-fw-medium tutor-color-black">
							<?php esc_html_e( 'Complete Your Profile', 'tutor' ); ?>
						</div>

						<div class="tutor-row tutor-align-center tutor-mt-12">
							<div class="tutor-col">
								<div class="tutor-row tutor-gx-1">
									<?php for ( $i = 1; $i <= $total_count; $i++ ) : ?>
										<div class="tutor-col">
											<div class="tutor-progress-bar" style="--tutor-progress-value: <?php echo $i > $complete_count ? 0 : 100; ?>%; height: 8px;"><div class="tutor-progress-value" area-hidden="true"></div></div>
										</div>
									<?php endfor; ?>
								</div>
							</div>

							<div class="tutor-col-auto">
								<span class="tutor-round-box tutor-my-n20">
									<i class="tutor-icon-trophy" area-hidden="true"></i>
								</span>
							</div>
						</div>

						<div class="tutor-fs-6 tutor-mt-20">
							<?php
								$profile_complete_text = __( 'Please complete profile', 'tutor' );
							if ( $complete_count > ( $total_count / 2 ) && $complete_count < $total_count ) {
								$profile_complete_text = __( 'You are almost done', 'tutor' );
							} elseif ( $complete_count === $total_count ) {
								$profile_complete_text = __( 'Thanks for completing your profile', 'tutor' );
							}
								$profile_complete_status = $profile_complete_text;
							?>

							<span class="tutor-color-muted"><?php echo esc_html( $profile_complete_status ); ?>:</span>
							<span><?php echo esc_html( $complete_count . '/' . $total_count ); ?></span>
						</div>
					</div>

					<div class="tutor-col-lg-1 tutor-text-center tutor-my-24 tutor-my-lg-n24">
						<div class="tutor-vr tutor-d-none tutor-d-lg-inline-flex"></div>
						<div class="tutor-hr tutor-d-flex tutor-d-lg-none"></div>
					</div>

					<div class="tutor-col-lg-4 tutor-d-flex tutor-flex-column tutor-justify-center">
						<?php
						$i           = 0;
						$monetize_by = tutils()->get_option( 'monetize_by' );
						foreach ( $profile_completion as $key => $data ) {
							if ( '_tutor_withdraw_method_data' === $key ) {
								if ( 'free' === $monetize_by ) {
									continue;
								}
							}
							$is_set = $data['is_set']; // Whether the step is done or not.
							?>
								<div class="tutor-d-flex tutor-align-center<?php echo $i < ( count( $profile_completion ) - 1 ) ? ' tutor-mb-8' : ''; ?>">
									<?php if ( $is_set ) : ?>
										<span class="tutor-icon-circle-mark-line tutor-color-success tutor-mr-8"></span>
									<?php else : ?>
										<span class="tutor-icon-circle-times-line tutor-color-warning tutor-mr-8"></span>
									<?php endif; ?>

									<span class="<?php echo $is_set ? 'tutor-color-secondary' : 'tutor-color-muted'; ?>">
										<a class="tutor-btn tutor-btn-ghost tutor-has-underline" href="<?php echo esc_url( $data['url'] ); ?>">
											<?php echo esc_html( $data['text'] ); ?>
										</a>
									</span>
								</div>
								<?php
								$i++;
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		if ( ! $profile_completion['_tutor_profile_photo']['is_set'] ) {
			$alert_message = sprintf(
				'<div class="tutor-alert tutor-primary tutor-mb-20">
					<div class="tutor-alert-text">
						<span class="tutor-alert-icon tutor-fs-4 tutor-icon-circle-info tutor-mr-12"></span>
						<span>
							%s
						</span>
					</div>
					<div class="alert-btn-group">
						<a href="%s" class="tutor-btn tutor-btn-sm">' . __( 'Click Here', 'tutor' ) . '</a>
					</div>
				</div>',
				$profile_completion['_tutor_profile_photo']['text'],
				tutor_utils()->tutor_dashboard_url( 'settings' )
			);

			echo $alert_message; //phpcs:ignore
		}
	}
}


?>
<style>
  body{
    background: #fff;
			border: 1px solid #ccd0d4;
			color: #444;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
			margin: 2em auto !important;
			padding: 1em 2em !important;
		  max-width: 100% !important; 
			-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
			box-shadow: 0 1px 1px rgba(0, 0, 0, .04);

  }
        .button-12 {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 6px 14px;
            font-family: -apple-system, BlinkMacSystemFont, 'Roboto', sans-serif;
            border-radius: 6px;
            border: none;
            background: #6E6D70;
            box-shadow: 0px 0.5px 1px rgba(0, 0, 0, 0.1), inset 0px 0.5px 0.5px rgba(255, 255, 255, 0.5), 0px 0px 0px 0.5px rgba(0, 0, 0, 0.12);
            color: #DFDEDF;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
        }

        .button-12:focus {
            box-shadow: inset 0px 0.8px 0px -0.25px rgba(255, 255, 255, 0.2), 0px 0.5px 1px rgba(0, 0, 0, 0.1), 0px 0px 0px 3.5px rgba(58, 108, 217, 0.5);
            outline: 0;
        }
</style>
<style>
/* Navigation Styles */
.tutor-course-navigation {
    position: relative;
    z-index: 10;
}

/* Course Item Styles */
.tutor-course-item {
    display: none;
    animation: fadeIn 0.3s ease;
}

.tutor-course-item.active {
    display: block;
}

/* Modal Styles */
.tutor-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    overflow-y: auto;
}

.tutor-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.tutor-modal-window {
    position: relative;
    margin: 5% auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    max-width: 90%;
    width: 900px;
}

.tutor-modal-lg {
    max-width: 900px;
}

.tutor-modal-content {
    position: relative;
    display: flex;
    flex-direction: column;
    max-height: 90vh;
}

.tutor-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.tutor-modal-title {
    font-size: 1.25rem;
    font-weight: 600;
}

.tutor-modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #666;
    transition: color 0.2s;
}

.tutor-modal-close:hover {
    color: #333;
}

.tutor-modal-body {
    padding: 20px;
    overflow-y: auto;
    flex-grow: 1;
}

.tutor-modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Responsive adjustments */
@media (max-width: 767px) {
    .tutor-modal-window {
        margin: 2% auto;
        max-width: 95%;
    }
    
    .tutor-course-progress-item .tutor-row {
        flex-direction: column;
    }
    
    .tutor-course-progress-item .tutor-col-lg-4 {
        width: 100%;
    }
}
.streak-img{
	width:100px !important;
}
</style>
<!--<div class="tutor-fs-5 tutor-fw-medium tutor-color-black tutor-text-capitalize tutor-mb-24 tutor-dashboard-title"><?php esc_html_e( 'Dashboard', 'tutor' ); ?></div>-->
<div class="flex min-h-screen">
    <!-- Sidebar -->
   

    <!-- Main content -->
    <main class="w-full flex-1">
      <!-- Header -->
      <div class="bg-blue-300 text-white px-4 py-2 rounded font-semibold">
        Dashboard
      </div>

      <!-- Line chart + 2 boxes bên phải -->
    <div class="grid grid-cols-3 gap-4 h-[400px]">
      <!-- Line Chart -->
      <div class="col-span-2 bg-white p-4 rounded shadow">
        <canvas id="lineChart" class="h-full w-full"></canvas>
      </div>

      <!-- 2 box bên phải biểu đồ -->
      <div class="flex flex-col gap-4 h-full">
		<!-- Box 1 - 30% -->
		<div class="flex-[3] bg-white p-4 rounded shadow">
			<div class="flex items-center mb-2">
			<h3 class="font-semibold text-gray-700 mr-2">Streak</h3>
			<div class="relative group inline-block">
				<svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24"
					fill="none" stroke="#000000" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="bevel"
					class="cursor-pointer">
				<circle cx="12" cy="12" r="10"></circle>
				<line x1="12" y1="8" x2="12" y2="12"></line>
				<line x1="12" y1="16" x2="12.01" y2="16"></line>
				</svg>
				<div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 hidden group-hover:block bg-black text-white text-xs rounded py-1 px-2 z-10 whitespace-nowrap">
				Đăng nhập hàng ngày để nhận thêm streak
				</div>
			</div>


			</div>
			
			<div class="flex justify-between items-center mb-4">
			<button id="prevTab" class="tutor-btn tutor-btn-outline-primary tutor-btn-sm mr-2">
				<i class="tutor-icon-angle-left"></i> 
			</button>
			<button id="nextTab" class="tutor-btn tutor-btn-outline-primary tutor-btn-sm">
				<i class="tutor-icon-angle-right"></i>
			</button>
			</div>

			<div id="tabContent">
			<!-- Streak Tab -->
			<div id="streakTab">
				<div class="flex items-center mb-4">
				<img class="streak-img mr-4" src="<?php echo $siteurl ?>\contents\plugins\tutor\assets\images\streak.png" alt="Streak">
				<div class="text-lg font-medium" id="streak-count">Số streak hiện tại</div>
				</div>
				<div class="text-center">
				<button class="tutor-btn tutor-btn-primary" onclick="claimStreak()">Nhận Streak hôm nay</button>
				</div>
			</div>
			
			<!-- Gift Tab (hidden by default) -->
			<div id="giftTab" style="display: none;">
				Nội dung quà có thể nhận được sẽ hiển thị ở đây
			</div>
			</div>
		</div>
	


        <!-- Box 2 - 70% -->
        <div class="flex-[7] bg-white p-4 rounded shadow overflow-auto">
          <h3 class="font-semibold text-gray-700 mb-2">Thống kê nhanh</h3>
          <ul class="list-disc list-inside text-gray-700 mt-2 text-sm">
            <div>
				<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
					<i class="tutor-icon-book-open" area-hidden="true"></i>
				</span>
				Số khóa học đã tham gia <?php echo esc_html( $enrolled_course_count ); ?>
			</div>
            <div>
				<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
					<i class="tutor-icon-mortarboard-o" area-hidden="true"></i>
				</span>	
				Số khóa học đang hoạt động<?php echo esc_html( $active_course_count ); ?>
			</div>
            <div>
				<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
					<i class="tutor-icon-trophy" area-hidden="true"></i>
				</span>	
				Số khóa học đã hoàn thành
				<?php echo esc_html( $completed_course_count ); ?>
			</div>

            <div>
				<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
					<i class="tutor-icon-trophy" area-hidden="true"></i>
				</span>	
				Số đề thi đã hoàn thành
				<?php echo esc_html( $completed_course_count ); ?>
			</div>
          </ul>
        </div>
      </div>




        
      </div>
	  
<!-- Wrapper cha: full width -->
<div class="w-full">
  <!-- Container chia 2 nửa bằng nhau -->
  <div class="grid grid-cols-2 gap-4 w-full h-[400px]">
    <!-- BÊN TRÁI: 2 box nhỏ -->
    <div class="flex flex-col gap-4 h-full w-full">
      <!-- Box 1 - 30% -->
      <div class="flex-[3] bg-white p-4 rounded shadow">
		<h3 class="font-semibold text-gray-700 mb-2">Đề thi cần làm nốt</h3>
		<ul id="progress-list" class="list-disc list-inside text-gray-700 mt-2 text-sm"></ul>
	</div>

      <!-- Box 2 - 70% -->
    <div class="flex-[7] bg-white p-4 rounded shadow overflow-auto">
		<h3 class="font-semibold text-gray-700 mb-2">Recent Result Test</h3>
		<ul id="result-list" class="list-disc list-inside text-gray-700 mt-2 text-sm"></ul>
	</div>
    </div>

    <!-- BÊN PHẢI: Line chart -->
    <div class="bg-white p-4 rounded shadow h-full w-full relative">
    <!-- Navigation Controls -->
    <div class="tutor-course-navigation flex justify-between mb-4">
		<h3 class="font-semibold text-gray-700 mb-2">Khóa học đang sở hữu</h3>
        <div>
            <button id="prev-course" class="tutor-btn tutor-btn-outline-primary tutor-btn-sm mr-2">
                <i class="tutor-icon-angle-left"></i> <?php esc_html_e('Previous', 'tutor'); ?>
            </button>
            <button id="next-course" class="tutor-btn tutor-btn-outline-primary tutor-btn-sm">
                <?php esc_html_e('Next', 'tutor'); ?> <i class="tutor-icon-angle-right"></i>
            </button>
        </div>
        <button id="expand-course" class="tutor-btn tutor-btn-primary tutor-btn-sm">
            <i class="tutor-icon-expand"></i> <?php esc_html_e('Expand', 'tutor'); ?>
        </button>
    </div>

    <!-- Course Container -->
<div id="course-container" class="tutor-course-progress-item tutor-card">
    <?php
    $courses = array();

    if ( $courses_in_progress instanceof WP_Query && $courses_in_progress->have_posts() ) :
        while ( $courses_in_progress->have_posts() ) : 
            $courses_in_progress->the_post();
            $courses[] = get_the_ID();
            $tutor_course_img = get_tutor_course_thumbnail_src();
            $course_rating    = tutor_utils()->get_course_rating( get_the_ID() );
            $course_progress  = tutor_utils()->get_course_completed_percent( get_the_ID(), 0, true );
            $completed_number = 0 === (int) $course_progress['completed_count'] ? 1 : (int) $course_progress['completed_count'];

            ob_start();
            ?>
            <div class="tutor-course-item" data-course-id="<?php echo get_the_ID(); ?>">
                <!-- nội dung hiển thị khóa học như cũ -->
            </div>
            <?php
            $course_content = ob_get_clean();
            echo $course_content;
        endwhile;
        wp_reset_postdata();
    else :
        ?>
        <div class="tutor-no-course tutor-text-center tutor-py-5">
            <p><?php _e('Bạn chưa đăng ký khóa học nào cả.', 'tutor'); ?></p>
            <a href="<?php echo esc_url(site_url('/courses')); ?>" class="tutor-btn tutor-btn-primary">
                <?php _e('Khám phá các khóa học tại đây', 'tutor'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
</div>

<!-- Popup Modal -->
<div id="course-modal" class="tutor-modal" style="display: none;">
    <div class="tutor-modal-overlay"></div>
    <div class="tutor-modal-window tutor-modal-lg">
        <div class="tutor-modal-content">
            <div class="tutor-modal-header">
                <div class="tutor-modal-title">
                    <?php esc_html_e('Course Details', 'tutor'); ?>
                </div>
                <button class="tutor-modal-close tutor-icon-line-cross"></button>
            </div>
            <div class="tutor-modal-body" id="modal-course-content">
                <!-- Course content will be loaded here -->
            </div>
            <div class="tutor-modal-footer">
                <button class="tutor-btn tutor-btn-outline-primary" id="modal-prev-course">
                    <i class="tutor-icon-angle-left"></i> <?php esc_html_e('Previous', 'tutor'); ?>
                </button>
                <button class="tutor-btn tutor-btn-primary" id="modal-next-course">
                    <?php esc_html_e('Next', 'tutor'); ?> <i class="tutor-icon-angle-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

  </div>
</div>

    </main>
  </div>
  <script>
jQuery(document).ready(function($) {
    // Store all course IDs
    const courseIds = [<?php echo implode(',', $courses); ?>];
    let currentIndex = 0;
    
    // Initialize - show first course
    showCourse(currentIndex);
    
    // Navigation functions
    function showCourse(index) {
        $('.tutor-course-item').removeClass('active');
        $(`.tutor-course-item[data-course-id="${courseIds[index]}"]`).addClass('active');
        
        // Update button states
        $('#prev-course, #modal-prev-course').prop('disabled', index <= 0);
        $('#next-course, #modal-next-course').prop('disabled', index >= courseIds.length - 1);
    }
    
    function showCourseInModal(index) {
        const courseContent = $(`.tutor-course-item[data-course-id="${courseIds[index]}"]`).html();
        $('#modal-course-content').html(courseContent);
        
        // Update modal button states
        $('#modal-prev-course').prop('disabled', index <= 0);
        $('#modal-next-course').prop('disabled', index >= courseIds.length - 1);
    }
    
    // Event handlers
    $('#prev-course, #modal-prev-course').on('click', function() {
        if (currentIndex > 0) {
            currentIndex--;
            showCourse(currentIndex);
            if ($('#course-modal').is(':visible')) {
                showCourseInModal(currentIndex);
            }
        }
    });
    
    $('#next-course, #modal-next-course').on('click', function() {
        if (currentIndex < courseIds.length - 1) {
            currentIndex++;
            showCourse(currentIndex);
            if ($('#course-modal').is(':visible')) {
                showCourseInModal(currentIndex);
            }
        }
    });
    
    // Expand button
    $('#expand-course').on('click', function() {
        showCourseInModal(currentIndex);
        $('#course-modal').fadeIn();
        $('body').css('overflow', 'hidden');
    });
    
    // Close modal
    $('.tutor-modal-close').on('click', function() {
        $('#course-modal').fadeOut();
        $('body').css('overflow', 'auto');
    });
    
    // Close when clicking outside
    $('.tutor-modal-overlay').on('click', function() {
        $('#course-modal').fadeOut();
        $('body').css('overflow', 'auto');
    });
    
    // Keyboard navigation
    $(document).keydown(function(e) {
        if ($('#course-modal').is(':visible')) {
            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                showCourseInModal(currentIndex);
            } else if (e.key === 'ArrowRight' && currentIndex < courseIds.length - 1) {
                currentIndex++;
                showCourseInModal(currentIndex);
            } else if (e.key === 'Escape') {
                $('#course-modal').fadeOut();
                $('body').css('overflow', 'auto');
            }
        }
    });
});
</script>
<script>
  const prevBtn = document.getElementById('prevTab');
  const nextBtn = document.getElementById('nextTab');
  const streakTab = document.getElementById('streakTab');
  const giftTab = document.getElementById('giftTab');
  
  let currentTab = 'streak';
  
  function switchTab() {
    if (currentTab === 'streak') {
      streakTab.style.display = 'none';
      giftTab.style.display = 'block';
      currentTab = 'gift';
    } else {
      streakTab.style.display = 'block';
      giftTab.style.display = 'none';
      currentTab = 'streak';
    }
  }
  
  prevBtn.addEventListener('click', switchTab);
  nextBtn.addEventListener('click', switchTab);

  function claimStreak() {
    // Your streak claim logic here
    console.log("Claiming today's streak");
  }
</script>

<script>
  const username = "<?php echo $current_username ?>"; // <-- Thay bằng username thực tế
  const user_id = "<?php echo $user_id ?>";; // Lấy từ backend, hoặc WordPress wp_localize_script

async function claimStreak() {
    try {
        const res = await fetch(`<?php echo $siteurl ?>/api/v1/update-streak?user_id=${user_id}&username=${username}`);
        const data = await res.json();
        alert(data.message || 'Đã cập nhật');
        document.getElementById('streak-count').innerText = `Số streak hiện tại: ${data.streak_count}`;
    } catch (err) {
        console.error('Lỗi khi nhận streak:', err);
        alert('Lỗi khi nhận streak');
    }
}

async function loadStreak() {
    try {
        const res = await fetch(`<?php echo $siteurl ?>/api/v1/get-streak?user_id=${user_id}`);
        const data = await res.json();
        document.getElementById('streak-count').innerText = `Số streak hiện tại: ${data.streak_count}`;
    } catch (err) {
        console.error('Lỗi khi load streak:', err);
    }
}

document.addEventListener('DOMContentLoaded', loadStreak);



  // Fetch Progress API
  fetch(`<?php echo $siteurl ?>/api/v1/get-all-progress`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username }),
  })
    .then((res) => res.json())
    .then((data) => {
      const progressList = document.getElementById("progress-list");
      if (data.success && Array.isArray(data.data)) {
        data.data.forEach((item) => {
          const li = document.createElement("li");
          li.textContent = `${item.testname} - Hoàn thành ${item.percent_completed}% - (${item.date})`;
          progressList.appendChild(li);
        });
      } else {
        progressList.innerHTML = "<li>Không có dữ liệu.</li>";
      }
    })
    .catch((err) => {
      console.error("Error fetching progress:", err);
      document.getElementById("progress-list").innerHTML = "<li>Lỗi khi tải dữ liệu.</li>";
    });


	
fetch(`<?php echo $siteurl ?>/api/v1/latest-results?username=${encodeURIComponent(username)}`, {
  method: "GET",
  headers: { "Content-Type": "application/json" },
})
  .then((res) => {
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
    return res.json();
  })
  .then((results) => {
    const resultList = document.getElementById("result-list");
    if (Array.isArray(results)) {
      results.forEach((item) => {
        const li = document.createElement("li");
        li.textContent = `${item.testname} - Kết quả: ${item.result} - (${item.date})`;
        resultList.appendChild(li);
      });
    } else {
      resultList.innerHTML = "<li>Không có dữ liệu.</li>";
    }
  })
  .catch((err) => {
    console.error("Error fetching results:", err);
    document.getElementById("result-list").innerHTML = "<li>Lỗi khi tải kết quả.</li>";
  });

    new Chart(document.getElementById('lineChart'), {
      type: 'line',
      data: {
        labels: ['01', '02', '03', '04', '05'],
        datasets: [{
          label: 'Điểm',
          data: [2, 5, 5, 6, 7],
          borderColor: '#ef4444',
          backgroundColor: '#fecaca',
          fill: false,
          tension: 0.3
        }]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

    new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: {
        labels: ['Đúng', 'Sai'],
        datasets: [{
          data: [24, 28],
          backgroundColor: ['#10B981', '#EF4444']
        }]
      },
      options: {
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  </script>
<div class="tutor-dashboard-content-inner">
	
	<div class="tutor-row tutor-gx-lg-4">
		<!--<div class="tutor-col-lg-6 tutor-col-xl-4 tutor-mb-16 tutor-mb-lg-32">
			<div class="tutor-card">
				<div class="tutor-d-flex tutor-flex-lg-column tutor-align-center tutor-text-lg-center tutor-px-12 tutor-px-lg-24 tutor-py-8 tutor-py-lg-32">
					<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
						<i class="tutor-icon-book-open" area-hidden="true"></i>
					</span>
					<div class="tutor-fs-3 tutor-fw-bold tutor-d-none tutor-d-lg-block"><?php echo esc_html( $enrolled_course_count ); ?></div>
					<div class="tutor-fs-7 tutor-color-secondary"><?php esc_html_e( 'Khóa học đã tham gia', 'tutor' ); ?></div>
					<div class="tutor-fs-4 tutor-fw-bold tutor-d-block tutor-d-lg-none tutor-ml-auto"><?php echo esc_html( $enrolled_course_count ); ?></div>
				</div>
			</div>
		</div>

		<div class="tutor-col-lg-6 tutor-col-xl-4 tutor-mb-16 tutor-mb-lg-32">
			<div class="tutor-card">
				<div class="tutor-d-flex tutor-flex-lg-column tutor-align-center tutor-text-lg-center tutor-px-12 tutor-px-lg-24 tutor-py-8 tutor-py-lg-32">
					<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
						<i class="tutor-icon-mortarboard-o" area-hidden="true"></i>
					</span>
					<div class="tutor-fs-3 tutor-fw-bold tutor-d-none tutor-d-lg-block"><?php echo esc_html( $active_course_count ); ?></div>
					<div class="tutor-fs-7 tutor-color-secondary"><?php esc_html_e( 'Khóa học đang sở hữu', 'tutor' ); ?></div>
					<div class="tutor-fs-4 tutor-fw-bold tutor-d-block tutor-d-lg-none tutor-ml-auto"><?php echo esc_html( $active_course_count ); ?></div>
				</div>
			</div>
		</div>

		<div class="tutor-col-lg-6 tutor-col-xl-4 tutor-mb-16 tutor-mb-lg-32">
			<div class="tutor-card">
				<div class="tutor-d-flex tutor-flex-lg-column tutor-align-center tutor-text-lg-center tutor-px-12 tutor-px-lg-24 tutor-py-8 tutor-py-lg-32">
					<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
						<i class="tutor-icon-trophy" area-hidden="true"></i>
					</span>
					<div class="tutor-fs-3 tutor-fw-bold tutor-d-none tutor-d-lg-block"><?php echo esc_html( $completed_course_count ); ?></div>
					<div class="tutor-fs-7 tutor-color-secondary"><?php esc_html_e( 'Khóa học đã hoàn thành', 'tutor' ); ?></div>
					<div class="tutor-fs-4 tutor-fw-bold tutor-d-block tutor-d-lg-none tutor-ml-auto"><?php echo esc_html( $completed_course_count ); ?></div>
				</div>
			</div>
		</div>

		<div class="tutor-col-lg-6 tutor-col-xl-4 tutor-mb-16 tutor-mb-lg-32">
			<div class="tutor-card">
				<div class="tutor-d-flex tutor-flex-lg-column tutor-align-center tutor-text-lg-center tutor-px-12 tutor-px-lg-24 tutor-py-8 tutor-py-lg-32">
					<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
						<i class="tutor-icon-trophy" area-hidden="true"></i>
					</span>
					<div class="tutor-fs-3 tutor-fw-bold tutor-d-none tutor-d-lg-block"><?php echo esc_html( $completed_course_count ); ?></div>
					<div class="tutor-fs-7 tutor-color-secondary"><?php esc_html_e( 'Số lượng test đã hoàn thành', 'tutor' ); ?></div>
					<div class="tutor-fs-4 tutor-fw-bold tutor-d-block tutor-d-lg-none tutor-ml-auto"><?php echo esc_html( $completed_course_count ); ?></div>
				</div>
			</div>
		</div> -->


		<?php
		if ( current_user_can( tutor()->instructor_role ) ) :
			?>
			<div class="tutor-col-lg-6 tutor-col-xl-4 tutor-mb-16 tutor-mb-lg-32">
				<div class="tutor-card">
					<div class="tutor-d-flex tutor-flex-lg-column tutor-align-center tutor-text-lg-center tutor-px-12 tutor-px-lg-24 tutor-py-8 tutor-py-lg-32">
						<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
							<i class="tutor-icon-user-graduate" area-hidden="true"></i>
						</span>
						<div class="tutor-fs-3 tutor-fw-bold tutor-d-none tutor-d-lg-block"><?php echo esc_html( $total_students ); ?></div>
						<div class="tutor-fs-7 tutor-color-secondary"><?php esc_html_e( 'Total Students', 'tutor' ); ?></div>
						<div class="tutor-fs-4 tutor-fw-bold tutor-d-block tutor-d-lg-none tutor-ml-auto"><?php echo esc_html( $total_students ); ?></div>
					</div>
				</div>
			</div>

			<div class="tutor-col-lg-6 tutor-col-xl-4 tutor-mb-16 tutor-mb-lg-32">
				<div class="tutor-card">
					<div class="tutor-d-flex tutor-flex-lg-column tutor-align-center tutor-text-lg-center tutor-px-12 tutor-px-lg-24 tutor-py-8 tutor-py-lg-32">
						<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
							<i class="tutor-icon-box-open" area-hidden="true"></i>
						</span>
						<div class="tutor-fs-3 tutor-fw-bold tutor-d-none tutor-d-lg-block"><?php echo esc_html( count( $my_courses ) ); ?></div>
						<div class="tutor-fs-7 tutor-color-secondary"><?php esc_html_e( 'Total Courses', 'tutor' ); ?></div>
						<div class="tutor-fs-4 tutor-fw-bold tutor-d-block tutor-d-lg-none tutor-ml-auto"><?php echo esc_html( count( $my_courses ) ); ?></div>
					</div>
				</div>
			</div>

			<div class="tutor-col-lg-6 tutor-col-xl-4 tutor-mb-16 tutor-mb-lg-32">
				<div class="tutor-card">
					<div class="tutor-d-flex tutor-flex-lg-column tutor-align-center tutor-text-lg-center tutor-px-12 tutor-px-lg-24 tutor-py-8 tutor-py-lg-32">
						<span class="tutor-round-box tutor-mr-12 tutor-mr-lg-0 tutor-mb-lg-12">
							<i class="tutor-icon-coins" area-hidden="true"></i>
						</span>
						<div class="tutor-fs-3 tutor-fw-bold tutor-d-none tutor-d-lg-block"><?php echo wp_kses_post( tutor_utils()->tutor_price( $earning_sum->total_income ) ); ?></div>
						<div class="tutor-fs-7 tutor-color-secondary"><?php esc_html_e( 'Total Earnings', 'tutor' ); ?></div>
						<div class="tutor-fs-4 tutor-fw-bold tutor-d-block tutor-d-lg-none tutor-ml-auto"><?php echo wp_kses_post( tutor_utils()->tutor_price( $earning_sum->total_income ) ); ?></div>
					</div>
				</div>
			</div>
			<?php
		endif;
		?>
	</div>
</div>

<!--
<?php if ( $courses_in_progress && $courses_in_progress->have_posts() ) : ?>
	<div class="tutor-frontend-dashboard-course-progress">
		<div class="tutor-fs-5 tutor-fw-medium tutor-color-black tutor-text-capitalize tutor-mb-24">
			<?php esc_html_e( 'In Progress Courses', 'tutor' ); ?>
		</div>
		<?php
		while ( $courses_in_progress->have_posts() ) :
			$courses_in_progress->the_post();
			$tutor_course_img = get_tutor_course_thumbnail_src();
			$course_rating    = tutor_utils()->get_course_rating( get_the_ID() );
			$course_progress  = tutor_utils()->get_course_completed_percent( get_the_ID(), 0, true );
			$completed_number = 0 === (int) $course_progress['completed_count'] ? 1 : (int) $course_progress['completed_count'];
			?>
			<div class="tutor-course-progress-item tutor-card tutor-mb-20">
				<div class="tutor-row tutor-gx-0">
					<div class="tutor-col-lg-4">
						<div class="tutor-ratio tutor-ratio-3x2">
							<img class="tutor-card-image-left" src="<?php echo empty( $tutor_course_img ) ? esc_url( $placeholder_img ) : esc_url( $tutor_course_img ); ?>" alt="<?php the_title(); ?>" loading="lazy">
						</div>
					</div>

					<div class="tutor-col-lg-8 tutor-align-self-center">
						<div class="tutor-card-body">
						<?php if ( $course_rating ) : ?>
								<div class="tutor-ratings tutor-mb-4">
									<?php tutor_utils()->star_rating_generator( $course_rating->rating_avg ); ?>
									<div class="tutor-ratings-count">
										<?php echo esc_html( number_format( $course_rating->rating_avg, 2 ) ); ?>
									</div>
								</div>
							<?php endif; ?>

							<div class="tutor-course-progress-item-title tutor-fs-5 tutor-fw-medium tutor-color-black tutor-mb-12">
							<?php the_title(); ?>
							</div>

							<div class="tutor-d-flex tutor-fs-7 tutor-mb-32">
								<span class="tutor-color-muted tutor-mr-4"><?php esc_html_e( 'Completed Lessons:', 'tutor' ); ?></span>
								<span class="tutor-fw-medium tutor-color-black">
									<span>
									<?php echo esc_html( $course_progress['completed_count'] ); ?>
									</span>
								<?php esc_html_e( 'of', 'tutor' ); ?>
									<span>
									<?php echo esc_html( $course_progress['total_count'] ); ?>
									</span>
								<?php echo esc_html( _n( 'lesson', 'lessons', $completed_number, 'tutor' ) ); ?>
								</span>
							</div>

							<div class="tutor-row tutor-align-center">
								<div class="tutor-col">
									<div class="tutor-progress-bar tutor-mr-16" style="--tutor-progress-value:<?php echo esc_attr( $course_progress['completed_percent'] ); ?>%"><span class="tutor-progress-value" area-hidden="true"></span></div>
								</div>

								<div class="tutor-col-auto">
									<span class="progress-percentage tutor-fs-7 tutor-color-muted">
										<span class="tutor-fw-medium tutor-color-black ">
										<?php echo esc_html( $course_progress['completed_percent'] . '%' ); ?>
										</span><?php esc_html_e( 'Complete', 'tutor' ); ?>
									</span>
								</div>
							</div>
						</div>
					</div>
					<a class="tutor-stretched-link" href="<?php the_permalink(); ?>"></a>
				</div>
			</div>
			<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</div>
<?php endif; ?>
						-->
<?php
$instructor_course = tutor_utils()->get_courses_for_instructors( get_current_user_id() );

if ( count( $instructor_course ) ) {
	$course_badges = array(
		'publish' => 'success',
		'pending' => 'warning',
		'trash'   => 'danger',
	);

	?>
		<div class="popular-courses-heading-dashboard tutor-d-flex tutor-justify-between tutor-mb-24 tutor-mt-md-40 tutor-mt-0">
			<span class="tutor-fs-5 tutor-fw-medium tutor-color-black"><?php esc_html_e( 'My Courses', 'tutor' ); ?></span>
			<a class="tutor-btn tutor-btn-ghost" href="<?php echo esc_url( tutor_utils()->tutor_dashboard_url( 'my-courses' ) ); ?>">
				<?php esc_html_e( 'View All', 'tutor' ); ?>
			</a>
		</div>
		
		<div class="tutor-dashboard-content-inner">
			<div class="tutor-table-responsive">
				<table class="tutor-table table-popular-courses">
					<thead>
						<tr>
							<th>
								<?php esc_html_e( 'Course Name', 'tutor' ); ?>
							</th>
							<th>
								<?php esc_html_e( 'Enrolled', 'tutor' ); ?>
							</th>
							<th>
								<?php esc_html_e( 'Rating', 'tutor' ); ?>
							</th>
						</tr>
					</thead>
	
					<tbody>
						<?php if ( is_array( $instructor_course ) && count( $instructor_course ) ) : ?>
							<?php
							foreach ( $instructor_course as $course ) :
								$enrolled      = tutor_utils()->count_enrolled_users_by_course( $course->ID );
								$course_status = isset( $status_translations[ $course->post_status ] ) ? $status_translations[ $course->post_status ] : __( $course->post_status, 'tutor' ); //phpcs:ignore
								$course_rating = tutor_utils()->get_course_rating( $course->ID );
								$course_badge  = isset( $course_badges[ $course->post_status ] ) ? $course_badges[ $course->post_status ] : 'dark';
								?>
								<tr>
									<td>
										<a href="<?php echo esc_url( get_the_permalink( $course->ID ) ); ?>" target="_blank">
											<?php echo esc_html( $course->post_title ); ?>
										</a>
									</td>
									<td>
										<?php echo esc_html( $enrolled ); ?>
									</td>
									<td>
										<?php tutor_utils()->star_rating_generator_v2( $course_rating->rating_avg, null, true ); ?>
									</td>
								</tr>
							<?php endforeach; ?>
							<?php else : ?>
								<tr>
									<td colspan="100%" class="column-empty-state">
										<?php tutor_utils()->tutor_empty_state( tutor_utils()->not_found_text() ); ?>
									</td>
								</tr>
							<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>

		
	<?php
}

?>
