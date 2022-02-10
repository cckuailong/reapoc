<?php
namespace Tutor;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Options {

	private $option;

	public function __construct() {
		//Saving option
		add_action('wp_ajax_tutor_option_save', array($this, 'tutor_option_save'));
	}

	private function get($key = null, $default = false){
		!$this->option ? $this->option = (array) maybe_unserialize(get_option('tutor_option')) : 0;
		if (empty($this->option) || ! is_array($this->option)){
			return $default;
		}
		if ( ! $key){
			return $this->option;
		}
		if (array_key_exists($key, $this->option)){
			return apply_filters($key, $this->option[$key]);
		}
		//Access array value via dot notation, such as option->get('value.subvalue')
		if (strpos($key, '.')){
			$option_key_array = explode('.', $key);
			$new_option = $this->option;
			foreach ($option_key_array as $dotKey){
				if (isset($new_option[$dotKey])){
					$new_option = $new_option[$dotKey];
				}else{
					return $default;
				}
			}
			return apply_filters($key, $new_option);
		}

		return $default;
	}

	/**
	 * Sanitize settings options
	 */
	public function tutor_sanitize_settings_options( $input ) {
		if ( ! empty( $_POST['tutor_option'][$input] ) ) {
			return sanitize_text_field( $_POST['tutor_option'][$input] );
		}

		return '';
	}

	public function tutor_option_save(){
		tutils()->checking_nonce();

		!current_user_can( 'manage_options' ) ? wp_send_json_error( ) : 0;

		do_action('tutor_option_save_before');

		$option = (array)tutils()->array_get('tutor_option', $_POST, array());
		
		foreach ( $option as $key => $value ) {
			if ( 'login_error_message' === $key ) {
				$option['login_error_message'] = $this->tutor_sanitize_settings_options( 'login_error_message' );
			} elseif ( 'lesson_permalink_base' === $key ) {
				$option['lesson_permalink_base'] = $this->tutor_sanitize_settings_options( 'lesson_permalink_base' );
			} elseif ( 'lesson_video_duration_youtube_api_key' === $key ) {
				$option['lesson_video_duration_youtube_api_key'] = $this->tutor_sanitize_settings_options( 'lesson_video_duration_youtube_api_key' );
			} elseif ( 'email_from_name' === $key ) {
				$option['email_from_name'] = $this->tutor_sanitize_settings_options( 'email_from_name' );
			} elseif ( 'email_from_address' === $key ) {
				$option['email_from_address'] = $this->tutor_sanitize_settings_options( 'email_from_address' );
			}
		}
		
		$option = apply_filters('tutor_option_input', $option);
		
		update_option('tutor_option', $option);

		do_action('tutor_option_save_after');
		//re-sync settings
		//init::tutor_activate();

		wp_send_json_success( array('msg' => __('Option Updated', 'tutor') ) );
	}

	public function options_attr(){
		$pages = tutor_utils()->get_pages();
		$current_user = wp_get_current_user();
		//$course_base = tutor_utils()->course_archive_page_url();
		$lesson_sample_url_text = __( "/course/sample-course/<code>lessons</code>/sample-lesson/", 'tutor' );
		$lesson_url 			= site_url().$lesson_sample_url_text;

		$student_url = site_url().'/'._x( 'profile', 'tutor student profile', 'tutor' ).'/'.$current_user->display_name ; 

		$attempts_allowed = array();
		$attempts_allowed['unlimited'] = __('Unlimited' , 'tutor');
		$attempts_allowed = array_merge($attempts_allowed, array_combine(range(1,20), range(1,20)));

		$video_sources = array(
			'html5' => __('HTML 5 (mp4)', 'tutor'),
			'external_url' => __('External URL', 'tutor'),
			'youtube' => __('Youtube', 'tutor'),
			'vimeo' => __('Vimeo', 'tutor'),
			'embedded' => __('Embedded', 'tutor')
		);

		$course_filters = array(
			'search' => __('Keyword Search', 'tutor'),
			'category' => __('Category', 'tutor'),
			'tag' => __('Tag', 'tutor'),
			'difficulty_level' => __('Difficulty Level', 'tutor'),
			'price_type' => __('Price Type', 'tutor')
		);

		$attr = array(
			'general' => array(
				'label'     => __('General', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('General', 'tutor'),
						'desc' => __('General Settings', 'tutor'),
						'fields' => array(
							'tutor_dashboard_page_id' => array(
								'type'          => 'select',
								'label'         => __('Dashboard Page', 'tutor'),
								'default'       => '0',
								'options'       => $pages,
								'desc'          => __('This page will be used for student and instructor dashboard', 'tutor'),
							),
							'enable_public_profile' => array(
								'type'      => 'checkbox',
								'label'     => __('Public Profile', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('Enable this to make a profile publicly visible',	'tutor')."<br />" .$student_url,
							),
							'enable_profile_completion' => array(
								'type'      => 'checkbox',
								'label'     => __('Profile Completion', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('Enabling this feature will show a notification bar to students and instructors to complete their profile information',	'tutor'),
							),
							'disable_tutor_native_login' => array(
								'type'      => 'checkbox',
								'label'     => __('Tutor Native Login', 'tutor'),
								'label_title' => __('Disable', 'tutor'),
								'default' => '0',
								'desc'      => __('Disable to use the default WordPress login page',	'tutor'),
							),
							'student_must_login_to_view_course' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Visibility', 'tutor'),
								'label_title' => __('Logged in only', 'tutor'),
								'desc'      => __('Students must be logged in to view course', 'tutor'),
							),
							'delete_on_uninstall' => array(
								'type'      => 'checkbox',
								'label'     => __('Erase upon uninstallation', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'desc'      => __('Delete all data during uninstallation', 'tutor'),
							),

							'enable_spotlight_mode' => array(
								'type'      => 'checkbox',
								'label'     => __('Spotlight mode', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('This will hide the header and the footer and enable spotlight (full screen) mode when students view lessons.',	'tutor'),
							),
							'disable_default_player_youtube' => array(
								'type'      => 'checkbox',
								'label'     => __('YouTube Player', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('Disable this option to use Tutor LMS video player.',	'tutor'),
							),
							'disable_default_player_vimeo' => array(
								'type'      => 'checkbox',
								'label'     => __('Vimeo Player', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('Disable this option to use Tutor LMS video player.',	'tutor'),
							),
							'pagination_per_page' => array(
								'type'      => 'number',
								'label'      => __('Pagination', 'tutor'),
								'default'   => '20',
								'desc'  => __('Number of items you would like displayed "per page" in the pagination', 'tutor'),
							),
							'enable_tutor_maintenance_mode' => array(
								'type'      => 'checkbox',
								'label'     => __('Maintenance Mode', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default'   => '0',
								'desc'      => __('Enabling the maintenance mode allows you to display a custom message on the frontend. During this time, visitors can not access the site content. But the wp-admin dashboard will remain accessible.',	'tutor'),
							),
							'hide_admin_bar_for_users' => array(
								'type'      => 'checkbox',
								'label'     => __('Frontend Admin Bar', 'tutor'),
								'label_title' => __('Hide', 'tutor'),
								'default'   => '0',
								'desc'      => __('Hide admin bar option allow you to hide WordPress admin bar entirely from the frontend. It will still show to administrator roles user',	'tutor'),
							),
							'login_error_message' => array(
								'type'      => 'text',
								'label'     => __('Error message for wrong login credentials', 'tutor'),
								'default'   => 'Incorrect username or password.',
								'desc'      => __('Login error message displayed when the user puts wrong login credentials.', 'tutor'),
							),
						)
					)
				),
			),
			'course' => array(
				'label'     => __('Course', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('General', 'tutor'),
						'desc' => __('Course Settings', 'tutor'),
						'fields' => array(
							'enable_gutenberg_course_edit' => array(
								'type'      => 'checkbox',
								'label'     => __('Gutenberg Editor', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'desc' => __('Use Gutenberg editor on course description area.', 'tutor'),
							),
							'hide_course_from_shop_page' => array(
								'type'      => 'checkbox',
								'label'     => __('Enable / Disable', 'tutor'),
								'label_title'   => __('Hide course products from shop page', 'tutor'),
								'desc' => __('Enabling this feature will remove course products from the shop page.', 'tutor'),
							),
							'course_content_access_for_ia' => array(
								'type'      => 'checkbox',
								'label'     => __('Enable / Disable', 'tutor'),
								'label_title'   => __('Course Content Access', 'tutor'),
								'desc' => __('Allow instructors and admins to view the course content without enrolling', 'tutor'),
							),
							'wc_automatic_order_complete_redirect_to_courses' => array(
								'type'      => 'checkbox',
								'label'     => __('Auto redirect to courses', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'desc' => __('When a user\'s WooCommerce order is auto-completed,  they will be redirected to enrolled courses', 'tutor'),
							),
                            'course_completion_process' => array(
                                'type'          => 'radio',
                                'label'         => __('Course Completion Process', 'tutor'),
                                'default'       => 'flexible',
                                'select_options'   => false,
                                'options'   => array(
                                    'flexible'  =>  __('Flexible', 'tutor'),
                                    'strict'    =>  __('Strict Mode', 'tutor'),
                                ),
                                'desc'          => __('Students can complete courses anytime in the Flexible mode. In the Strict mode, students have to complete, pass all the lessons and quizzes (if any) to mark a course as complete.', 'tutor'),
							),
                            'course_retake_feature' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Retake', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'desc' => __('Enabling this feature will allow students to reset course progress and start over.', 'tutor'),
							)
						),
					),
					'archive' => array(
						'label' => __('Archive', 'tutor'),
						'desc' => __('Course Archive Settings', 'tutor'),
						'fields' => array(
							'course_archive_page' => array(
								'type'      => 'select',
								'label'     => __('Course Archive Page', 'tutor'),
								'default'   => '0',
								'options'   => $pages,
								'desc'      => __('This page will be used to list all the published courses.',	'tutor'),
							),
							'courses_col_per_row' => array(
								'type'      => 'slider',
								'label'     => __('Column Per Row', 'tutor'),
								'default'   => '4',
								'options'   => array('min'=> 1, 'max' => 6),
								'desc'      => __('Define how many column you want to use to display courses.', 'tutor'),
							),
							'courses_per_page' => array(
								'type'      => 'slider',
								'label'     => __('Courses Per Page', 'tutor'),
								'default'   => '12',
								'options'   => array('min'=> 1, 'max' => 20),
								'desc'      => __('Define how many courses you want to show per page', 'tutor'),
							),
							'course_archive_filter' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Filter', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'desc' => __('Show sorting and filtering options on course archive page', 'tutor'),
							),
							'supported_course_filters' => array(
								'type'      => 'checkbox',
								'label'     => __('Preferred Course Filters', 'tutor'),
								'options'	=> $course_filters,
								'desc'      => __('Choose preferred filter options you\'d like to show in course archive page.', 'tutor'),
							),
						),
					),
					'enable_disable' => array(
						'label' => __('Enable / Disable', 'tutor'),
						'desc' => __('Course Display Settings', 'tutor'),
						'fields' => array(
							'display_course_instructors' => array(
								'type'      => 'checkbox',
								'label'     => __('Display Instructor Info', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'desc' => __('Show instructor bio on each page', 'tutor'),
							),
							'enable_q_and_a_on_course' => array(
								'type'      => 'checkbox',
								'label'     => __('Question and Answer', 'tutor'),
								'label_title' => __('Enable','tutor'),
								'default'   => '0',
								'desc'      => __('Enabling this feature will add a Q&amp;A section on every course.',	'tutor'),
							),
							'disable_course_author' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Author', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course author name from the course page.', 'tutor'),
							),
							'disable_course_level' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Level', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course level from the course page.', 'tutor'),
							),
							'disable_course_share' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Share', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course share option from the course page.', 'tutor'),
							),
							'disable_course_duration' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Duration', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course duration from the course page.', 'tutor'),
							),
							'disable_course_total_enrolled' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Total Enrolled', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course total enrolled from the course page.', 'tutor'),
							),
							'disable_course_update_date' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Update Date', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course update date from the course page.', 'tutor'),
							),
							'disable_course_progress_bar' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Progress Bar', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed completing progress bar from the course page.', 'tutor'),
							),
							'disable_course_material' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Material', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course material from the course page.', 'tutor'),
							),
							'disable_course_about' => array(
								'type'      => 'checkbox',
								'label'     => __('Course About', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course about from the course page.', 'tutor'),
							),
							'disable_course_description' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Description', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course description from the course page.', 'tutor'),
							),
							'disable_course_benefits' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Benefits', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course benefits from the course page.', 'tutor'),
							),
							'disable_course_requirements' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Requirements', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course requirements from the course page.', 'tutor'),
							),
							'disable_course_target_audience' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Target Audience', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course target audience from the course page.', 'tutor'),
							),
							'disable_course_announcements' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Announcements', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course announcements from the course page.', 'tutor'),
							),
							'disable_course_review' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Review', 'tutor'),
								'label_title' => __('Disable','tutor'),
								'default'   => '0',
								'desc'      => __('Disabling this feature will be removed course review system from the course page.', 'tutor'),
							),
							'supported_video_sources' => array(
								'type'      => 'checkbox',
								'label'     => __('Preferred Video Source', 'tutor'),
								'options'	=> $video_sources,
								'desc'      => __('Choose video sources you\'d like to support. Unchecking all will not disable video feature.', 'tutor'),
							),
							'default_video_source' => array(
								'type'      => 'select',
								'label'     => __('Default Video Source', 'tutor'),
								'default'   => '',
								'options'   => $video_sources,
								'desc'      => __('Choose video source to be selected by default.',	'tutor'),
							),
						),
					),
				),
			),
			'lesson' => array(
				'label' => __('Lessons', 'tutor'),
				'sections'    => array(
					'lesson_settings' => array(
						'label' => __('Lesson Settings', 'tutor'),
						'desc' => __('Lesson settings will be here', 'tutor'),
						'fields' => array(
							'enable_lesson_classic_editor' => array(
								'type'          => 'checkbox',
								'label'         => __('Classic Editor', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'desc'          => __('Enable classic editor to get full support of any editor/page builder.', 'tutor'),
							),
							'autoload_next_course_content' => array(
								'type'      => 'checkbox',
								'label'     => __('Enable / Disable', 'tutor'),
								'label_title'   => __('Automatically load next course content.', 'tutor'),
								'desc' => __('Enabling this feature will be load next course content automatically after finishing current video.', 'tutor'),
							),
							'lesson_permalink_base' => array(
								'type'      => 'text',
								'label'     => __('Lesson Permalink Base', 'tutor'),
								'default'   => 'lessons',
								'desc'      => $lesson_url,
							),
							'lesson_video_duration_youtube_api_key' => array(
								'type'      => 'text',
								'label'     => __('Youtube API Key', 'tutor'),
								'desc'      => __('To get dynamic video duration from Youtube, you need to set API key first'),
							),
						),
					),

				),
			),
			'quiz' => array(
				'label' => __('Quiz', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('Quiz', 'tutor'),
						'desc' => __('The values you set here define the default values that are used in the settings form when you create a new quiz.', 'tutor'),
						'fields' => array(
							'quiz_time_limit' => array(
								'type'      => 'group_fields',
								'label'     => __('Time Limit', 'tutor'),
								'desc'      => __('0 means unlimited time.', 'tutor'),
								'group_fields'  => array(
									'value' => array(
										'type'      => 'text',
										'default'   => '0',
									),
									'time' => array(
										'type'      => 'select',
										'default'   => 'minutes',
										'select_options'   => false,
										'options'   => array(
											'weeks'     =>  __('Weeks', 'tutor'),
											'days'      =>  __('Days', 'tutor'),
											'hours'     =>  __('Hours', 'tutor'),
											'minutes'   =>  __('Minutes', 'tutor'),
											'seconds'   =>  __('Seconds', 'tutor'),
										),
									),
								),
							),
							'quiz_when_time_expires' => array(
								'type'      => 'radio',
								'label'      => __('When time expires', 'tutor'),
								'default'   => 'minutes',
								'select_options'   => false,
								'options'   => array(
									'autosubmit'    =>  __('The current quiz answers are submitted automatically.', 'tutor'),
									'graceperiod'   =>  __('The current quiz answers are submitted by students.', 'tutor'),
									'autoabandon'   =>  __('Attempts must be submitted before time expires, otherwise they will not be counted', 'tutor'),
								),
								'desc'  => __('Choose which action to follow when the quiz time expires.', 'tutor'),
							),
							'quiz_attempts_allowed' => array(
								'type'      => 'number',
								'label'      => __('Attempts allowed', 'tutor'),
								'default'   => '10',
								'desc'  => __('The highest number of attempts students are allowed to take for a quiz. 0 means unlimited attempts.', 'tutor'),
							),
							'quiz_previous_button_disabled' => array(
								'type'      => 'checkbox',
								'label'     => __('Show Previous button', 'tutor'),
								'label_title' => __('Disable', 'tutor'),
								'default' => '0',
								'desc'      => __('Choose whether to show or hide previous button for single question.', 'tutor'),
							),
							'quiz_grade_method' => array(
								'type'      => 'select',
								'label'      => __('Final grade calculation', 'tutor'),
								'default'   => 'minutes',
								'select_options'   => false,
								'options'   => array(
									'highest_grade' => __('Highest Grade', 'tutor'),
									'average_grade' => __('Average Grade', 'tutor'),
									'first_attempt' => __('First Attempt', 'tutor'),
									'last_attempt' => __('Last Attempt', 'tutor'),
								),
								'desc'  => __('When multiple attempts are allowed, which method should be used to calculate a student\'s final grade for the quiz.', 'tutor'),
							),
						)
					)
				),
			),
			'instructors' => array(
				'label'     => __('Instructors', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('Instructor Profile Settings', 'tutor'),
						'desc' => __('Enable Disable Option to on/off notification on various event', 'tutor'),
						'fields' => array(
							'enable_course_marketplace' => array(
								'type'      => 'checkbox',
								'label'     => __('Course Marketplace', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('Allow multiple instructors to upload their courses.',	'tutor'),
							),
							'instructor_register_page' => array(
								'type'      => 'select',
								'label'     => __('Instructor Registration Page', 'tutor'),
								'default'   => '0',
								'options'   => $pages,
								'desc'      => __('This page will be used to sign up new instructors.', 'tutor'),
							),
							'instructor_can_publish_course' => array(
								'type'      => 'checkbox',
								'label'     => __('Allow publishing course', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('Enable instructors to publish course directly. <strong>Do not select</strong> if admins want to review courses before publishing.',	'tutor'),
							),
							'enable_become_instructor_btn' => array(
								'type'      => 'checkbox',
								'label'     => __('Become Instructor Button', 'tutor'),
								'label_title' => __('Enable', 'tutor'),
								'default' => '0',
								'desc'      => __('Uncheck this option to hide the button from student dashboard.',	'tutor'),
							),
						),
					),
				),
			),
			'students' => array(
				'label'     => __('Students', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('Student Profile settings', 'tutor'),
						'desc' => __('Enable Disable Option to on/off notification on various event', 'tutor'),
						'fields' => array(
							'student_register_page' => array(
								'type'          => 'select',
								'label'         => __('Student Registration Page', 'tutor'),
								'default'       => '0',
								'options'       => $pages,
								'desc'          => __('Choose the page for student registration page', 'tutor'),
							),
							'students_own_review_show_at_profile' => array(
								'type'          => 'checkbox',
								'label'         => __('Show reviews on profile', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'default'       => '0',
								'desc'          => __('Enabling this will show the reviews written by each student on their profile', 'tutor')."<br />" .$student_url,
							),
							'show_courses_completed_by_student' => array(
								'type'          => 'checkbox',
								'label'         => __('Show completed courses', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'default'       => '0',
								'desc'          => __('Completed courses will be shown on student profiles. <br/> For example, you can see this link-',	'tutor').$student_url,
							),
						),
					),
				),
			),
			'tutor_earning' => array(
				'label'     => __('Earning', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('Earning and commission allocation', 'tutor'),
						'desc' => __('Enable Disable Option to on/off notification on various event', 'tutor'),
						'fields' => array(
							'enable_tutor_earning' => array(
								'type'          => 'checkbox',
								'label'         => __('Earning', 'tutor'),
								'label_title'   => __('Enable', 'tutor'),
								'default'       => '0',
								'desc'          => __('If disabled, the Admin will receive 100% of the earning',	'tutor'),
							),
							'earning_admin_commission' => array(
								'type'      => 'number',
								'label'      => __('Admin Commission Percentage', 'tutor'),
								'default'   => '0',
								'desc'  => __('Define the commission of the Admin from each sale.(after deducting fees)', 'tutor'),
							),
							'earning_instructor_commission' => array(
								'type'      => 'number',
								'label'      => __('Instructor Commission Percentage', 'tutor'),
								'default'   => '0',
								'desc'  => __('Define the commission for instructors from each sale.(after deducting fees)', 'tutor'),
							),
							'tutor_earning_fees' => array(
								'type'      => 'group_fields',
								'label'     => __('Fee Deduction', 'tutor'),
								'desc'      => __('Fees are charged from the entire sales amount. The remaining amount will be divided among admin and instructors.',	'tutor'),
								'group_fields'  => array(

									'enable_fees_deducting' => array(
										'type'          => 'checkbox',
										'label'         => __('Enable', 'tutor'),
										'default'       => '0',
									),
									'fees_name' => array(
										'type'      => 'text',
										'label'         => __('Fee Name', 'tutor'),
										'default'   => '',
									),
									'fees_amount' => array(
										'type'      => 'number',
										'label'         => __('Fee Amount', 'tutor'),
										'default'   => '',
									),
									'fees_type' => array(
										'type'      => 'select',
										'default'   => 'minutes',
										'select_options'   => false,
										'options'   => array(
											''     =>  __('Select Fees Type', 'tutor'),
											'percent'     =>  __('Percent', 'tutor'),
											'fixed'      =>  __('Fixed', 'tutor'),
										),
									),

								),
							),
							'statement_show_per_page' => array(
								'type'      => 'number',
								'label'      => __('Show Statement Per Page', 'tutor'),
								'default'   => '20',
								'desc'  => __('Define the number of statements to show.', 'tutor'),
							),
						),
					),
				),
			),
			'tutor_withdraw' => array(
				'label'     => __('Withdrawal', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('Withdrawal Settings', 'tutor'),
						'fields' => array(
							'min_withdraw_amount' => array(
								'type'      => 'number',
								'label'     => __('Minimum Withdraw Amount', 'tutor'),
								'default'   => '80',
								'desc'      => __('Instructors should earn equal or above this amount to make a withdraw request.',	'tutor'),
							),
						),
					),

					'withdraw_methods' => array(
						'label' => __('Withdraw Methods', 'tutor'),
						'desc' => __('Set withdraw settings', 'tutor'),
					),
				),
			),

			'tutor_style' => array(
				'label'     => __('Style', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('Color Style', 'tutor'),
						'fields' => array(
							'tutor_primary_color' => array(
								'type'      => 'color',
								'label'     => __('Primary Color', 'tutor'),
								'default'   => '',
							),
							'tutor_primary_hover_color' => array(
								'type'      => 'color',
								'label'     => __('Primary Hover Color', 'tutor'),
								'default'   => '',
							),
							'tutor_text_color' => array(
								'type'      => 'color',
								'label'     => __('Text color', 'tutor'),
								'default'   => '',
							),
							'tutor_light_color' => array(
								'type'      => 'color',
								'label'     => __('Light color', 'tutor'),
								'default'   => '',
							),
							//tutor button style options
							
							'tutor_button_primary' => array(
								'type' => 'color',
								'label' => __('Button Primary Color','tutor'),
								'default' => ''
							),							
							
							'tutor_button_danger' => array(
								'type' => 'color',
								'label' => __('Button Danger Color','tutor'),
								'default' => ''
							),
							'tutor_button_success' => array(
								'type' => 'color',
								'label' => __('Button Success Color','tutor'),
								'default' => ''
							),
							'tutor_button_warning' => array(
								'type' => 'color',
								'label' => __('Button Warning Color','tutor'),
								'default' => ''
							),
						),
					),

				),
			),

			'monetization' => array(
				'label' => __('Monetization', 'tutor'),
				'sections'    => array(
					'general' => array(
						'label' => __('Monetization', 'tutor'),
						'desc' => __('You can monetize your LMS website by selling courses in a various way.', 'tutor'),
						'fields' => array(

							'monetize_by' => array(
								'type'      => 'radio',
								'label'      => __('Monetize Option', 'tutor'),
								'default'   => 'free',
								'select_options'   => false,
								'options'   => apply_filters('tutor_monetization_options', array(
									'free'          =>  __('Disable Monetization', 'tutor'),
								)),
								'desc'  => __('Select a monetization option to generate revenue by selling courses. Supports: WooCommerce, Easy Digital Downloads, Paid Memberships Pro',	'tutor'),
							),

						)
					)
				),
			),

		);

		$attrs = apply_filters('tutor/options/attr', $attr);
		$extends = apply_filters('tutor/options/extend/attr', array());

		if (tutils()->count($extends)){
			foreach ($extends as $extend_key => $extend_option){
				if (isset($attrs[$extend_key])&& tutils()->count($extend_option['sections']) ){
					$sections = $attrs[$extend_key]['sections'];
					$sections = array_merge($sections, $extend_option['sections']);
					$attrs[$extend_key]['sections'] = $sections;
				}
			}
		}

		return $attrs;

	}

	/**
	 * @param array $field
	 *
	 * @return string
	 *
	 * Generate Option Field
	 */
	public function generate_field($field = array()){
		ob_start();
		include tutor()->path.'views/options/option_field.php';
		return ob_get_clean();
	}

	public function field_type($field = array()){
		ob_start();
		include tutor()->path."views/options/field-types/{$field['type']}.php";
		return ob_get_clean();
	}

	public function generate(){
		ob_start();
		include tutor()->path.'views/options/options_generator.php';
		return ob_get_clean();
	}

}