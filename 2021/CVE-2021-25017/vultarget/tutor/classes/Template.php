<?php
/**
 * Template Class
 *
 * @since: v.1.0.0
 */
namespace TUTOR;


if ( ! defined( 'ABSPATH' ) )
	exit;


class Template extends Tutor_Base {

	public function __construct() {
		parent::__construct();

		add_action( 'pre_get_posts', array($this, 'limit_course_query_archive'), 99 );


		/**
		 * Should Load Template Override
		 * Integration for specially oxygen builder
		 * If we found false of below filter, then we will not use this file
		 */

		$template_override = apply_filters('tutor_lms_should_template_override', true);
		if ( ! $template_override){
			return;
		}

		add_filter( 'template_include', array($this, 'load_course_archive_template'), 99 );
		add_filter( 'template_include', array($this, 'load_single_course_template'), 99 );
		add_filter( 'template_include', array($this, 'load_single_lesson_template'), 99 );
		add_filter( 'template_include', array($this, 'play_private_video'), 99 );
		add_filter( 'template_include', array($this, 'load_quiz_template'), 99 );
		add_filter( 'template_include', array($this, 'load_assignment_template'), 99 );

		add_filter( 'template_include', array($this, 'student_public_profile'), 99 );
		add_filter( 'template_include', array($this, 'tutor_dashboard'), 99 );
		add_filter( 'pre_get_document_title', array($this, 'student_public_profile_title') );

		add_filter( 'the_content', array($this, 'convert_static_page_to_template'));
	}

	/**
	 * @param $template
	 *
	 * @return bool|string
	 *
	 * Load default template for course
	 *
	 * @since v.1.0.0
	 *
	 */
	public function load_course_archive_template($template){
		global $wp_query;

		$post_type = get_query_var('post_type');
		$course_category = get_query_var('course-category');

		if ( ($post_type === $this->course_post_type || ! empty($course_category) )  && $wp_query->is_archive){
			$template = tutor_get_template('archive-course');
			return $template;
		}

		return $template;
	}

	/**
	 * @param $query
	 *
	 * limit for course archive listing
	 *
	 * Make a page to archive listing for courses
	 */
	public function limit_course_query_archive($query){
		$courses_per_page = (int) tutor_utils()->get_option('courses_per_page', 12);

		if ($query->is_main_query() && ! $query->is_feed() && ! is_admin() && is_page() ){
			$queried_object = get_queried_object();
			if ($queried_object instanceof \WP_Post){
				$page_id = $queried_object->ID;
				$selected_archive_page = (int) tutor_utils()->get_option('course_archive_page');

				if ($page_id === $selected_archive_page){
					$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
					$search_query = get_search_query();
					query_posts(array('post_type' => $this->course_post_type, 'paged' => $paged, 's' => $search_query, 'posts_per_page' => $courses_per_page ));
				}
			}
		}

		if ( $query->is_archive && $query->is_main_query() && ! $query->is_feed() && ! is_admin() ){
			$post_type = get_query_var('post_type');
			$course_category = get_query_var('course-category');
			if ( ($post_type === $this->course_post_type || ! empty($course_category) )){
				$query->set('posts_per_page', $courses_per_page);

				$course_filter = 'newest_first';
				if ( ! empty($_GET['tutor_course_filter'])){
					$course_filter = sanitize_text_field($_GET['tutor_course_filter']);
				}
				switch ($course_filter){
					case 'newest_first':
						$query->set('orderby', 'ID');
						$query->set('order', 'desc');
						break;
					case 'oldest_first':
						$query->set('orderby', 'ID');
						$query->set('order', 'asc');
						break;
					case 'course_title_az':
						$query->set('orderby', 'post_title');
						$query->set('order', 'asc');
						break;
					case 'course_title_za':
						$query->set('orderby', 'post_title');
						$query->set('order', 'desc');
						break;
				}

			}
		}
	}

	/**
	 * @param $template
	 *
	 * @return bool|string
	 *
	 * Load Single Course Template
	 *
	 * @since v.1.0.0
	 * @updated v.1.3.5
	 */
	public function load_single_course_template($template){
		global $wp_query;

		if ($wp_query->is_single && ! empty($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] === $this->course_post_type){

			do_action( 'single_course_template_before_load', get_the_ID() );
			
			$student_must_login_to_view_course = tutor_utils()->get_option('student_must_login_to_view_course');
			if ($student_must_login_to_view_course){
				if ( ! is_user_logged_in() ) {
					return tutor_get_template( 'login' );
				}
			}

			wp_reset_query();
			if (empty( $wp_query->query_vars['course_subpage'])) {
				$template = tutor_get_template( 'single-course' );
				if ( is_user_logged_in() ) {
					$is_administrator = current_user_can('administrator');
					$is_instructor = tutor_utils()->is_instructor_of_this_course();
					$course_content_access = (bool) get_tutor_option('course_content_access_for_ia');
					if ( tutor_utils()->is_enrolled() ) {
						$template = tutor_get_template( 'single-course-enrolled' );
					} else if ( $course_content_access && ($is_administrator || $is_instructor) ) {
						$template = tutor_get_template( 'single-course-instructor' );
					}
				}
			}else{
				//If Course Subpage Exists
				if ( is_user_logged_in() ) {
					$course_subpage = $wp_query->query_vars['course_subpage'];
					$template = tutor_get_template_path( 'single-course-enrolled-' . $course_subpage );
					if ( ! file_exists( $template ) ) {
						$template = tutor_get_template( 'single-course-enrolled-subpage' );
					}
				}else{
					$template = tutor_get_template('login');
				}
			}
			return $template;
		}
		return $template;
	}

	private function get_root_post_parent_id($id){
		$ancestors = get_post_ancestors($id);
		$root = is_array($ancestors) ? end($ancestors) : null;

		return is_numeric($root) ? $root : $id;
	}

	/**
	 * @param $template
	 *
	 * @return bool|string
	 *
	 * Load lesson template
	 *
	 * @since v.1.0.0
	 */

	public function load_single_lesson_template($template){
		global $wp_query;

		if ($wp_query->is_single && ! empty($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] === $this->lesson_post_type){
			$page_id = get_the_ID();

			do_action('tutor_lesson_load_before', $template);

			setup_postdata($page_id);

			if (is_user_logged_in()) {
				$has_content_access = tutils()->has_enrolled_content_access('lesson');
				if ($has_content_access) {
					$template = tutor_get_template('single-lesson');
				} else {
					$template = tutor_get_template('single.lesson.required-enroll'); //You need to enroll first
				}
			}else{
				$template = tutor_get_template('login');
			}
			wp_reset_postdata();

			// Forcefully show lessons if it is public and not paid
			$course_id = $this->get_root_post_parent_id($page_id);
			if(get_post_meta($course_id, '_tutor_is_public_course', true)=='yes' && !tutor_utils()->is_course_purchasable($course_id)){
				$template = tutor_get_template( 'single-lesson' );
			}
			
			return apply_filters('tutor_lesson_template', $template);
		}
		return $template;
	}

	/**
	 * @param $template
	 *
	 * @return mixed
	 *
	 * Play the video in this url.
	 */
	public function play_private_video($template){
		global $wp_query;

		if ($wp_query->is_single && ! empty($wp_query->query_vars['lesson_video']) && $wp_query->query_vars['lesson_video'] === 'true') {

			$isPublicVideo = apply_filters('tutor_video_stream_is_public', false,  get_the_ID());
			if ($isPublicVideo){
				$video_info = tutor_utils()->get_video_info();
				if ( $video_info ) {
					$stream = new Video_Stream( $video_info->path );
					$stream->start();
				}
				exit();
			}

			if (tutor_utils()->is_course_enrolled_by_lesson()) {
				$video_info = tutor_utils()->get_video_info();
				if ( $video_info ) {
					$stream = new Video_Stream( $video_info->path );
					$stream->start();
				}
			}else{
				_e('Permission denied', 'tutor');
			}
			exit();
		}

		return $template;
	}

	/**
	 * @param $content
	 *
	 * @return mixed
	 *
	 * Tutor Dashboard Page, Responsible to show dashboard stuffs
	 *
	 * @since v.1.0.0
	 */
	public function convert_static_page_to_template($content){
		//Dashboard Page
		$student_dashboard_page_id = (int) tutor_utils()->get_option('tutor_dashboard_page_id');
		if ($student_dashboard_page_id === get_the_ID()){
			$shortcode = new Shortcode();
			return $shortcode->tutor_dashboard();
		}

		//Instructor Registration Page
		$instructor_register_page_page_id = (int) tutor_utils()->get_option('instructor_register_page');
		if ($instructor_register_page_page_id === get_the_ID()){
			$shortcode = new Shortcode();
			return $shortcode->instructor_registration_form();
		}

		$student_register_page_id = (int) tutor_utils()->get_option('student_register_page');
		if ($student_register_page_id === get_the_ID()){
			$shortcode = new Shortcode();
			return $shortcode->student_registration_form();
		}

		return $content;
	}

	public function tutor_dashboard($template){
		global $wp_query;
		if ($wp_query->is_page) {
			$student_dashboard_page_id = (int) tutor_utils()->get_option('tutor_dashboard_page_id');
			$student_dashboard_page_id = apply_filters( 'tutor_dashboard_page_id_filter', $student_dashboard_page_id );

			if ($student_dashboard_page_id === get_the_ID()) {
				/**
				 * Handle if logout URL
				 * @since v.1.1.2
				 */
				if (tutor_utils()->array_get('tutor_dashboard_page', $wp_query->query_vars) === 'logout'){
					$redirect = get_permalink($student_dashboard_page_id);
					wp_logout();
					wp_redirect($redirect);
					die();
				}


				$dashboard_page = tutor_utils()->array_get('tutor_dashboard_page', $wp_query->query_vars);
				$get_dashboard_config = tutils()->tutor_dashboard_permalinks();
				$target_dashboard_page = tutils()->array_get($dashboard_page, $get_dashboard_config);

				if (isset($target_dashboard_page['login_require']) && $target_dashboard_page['login_require'] === false){
					$template = tutor_load_template_part( "template-part.{$dashboard_page}" );
				}else{

					/**
					 * Load view page based on dashboard Endpoint
					 */
					if (is_user_logged_in()) {

						global $wp;
						$full_path = explode('/', trim( str_replace( get_home_url(), '', home_url( $wp->request ) ), '/' ) );
						$template = tutor_get_template( end($full_path)=='create-course' ? implode('/', $full_path) : 'dashboard' );
						
						/**
						 * Check page page permission
						 *
						 * @since v.1.3.4
						 */
						$query_var = tutor_utils()->array_get('tutor_dashboard_page', $wp_query->query_vars);
						$dashboard_pages = tutor_utils()->tutor_dashboard_pages();
						$dashboard_page_item = tutor_utils()->array_get($query_var, $dashboard_pages);
						$auth_cap = tutor_utils()->array_get('auth_cap', $dashboard_page_item);
						if ($auth_cap && ! current_user_can($auth_cap) ){
							$template = tutor_get_template( 'permission-denied' );
						}
					}else{
						$template = tutor_get_template( 'login' );
					}

				}

			}
		}

		return $template;
	}

	/**
	 * @param $template
	 *
	 * @return bool|string
	 *
	 * @since v.1.0.0
	 */
	public function load_quiz_template($template){
		global $wp_query;

		if ($wp_query->is_single && ! empty($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] === 'tutor_quiz'){
			if (is_user_logged_in()){
				$has_content_access = tutils()->has_enrolled_content_access('quiz');
				if ($has_content_access) {
					$template = tutor_get_template('single-quiz');
				} else {
					$template = tutor_get_template('single.lesson.required-enroll'); //You need to enroll first
				}
			}else{
				$template = tutor_get_template('login');
			}
			return $template;
		}
		return $template;
	}

	public function load_assignment_template($template){
		global $wp_query;

		if ($wp_query->is_single && ! empty($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] === 'tutor_assignments'){
			if (is_user_logged_in()){
				$has_content_access = tutils()->has_enrolled_content_access('assignment');
				if ($has_content_access) {
					$template = tutor_get_template('single-assignment');
				} else {
					$template = tutor_get_template('single.lesson.required-enroll'); //You need to enroll first
				}
			}else{
				$template = tutor_get_template('login');
			}
			return $template;
		}

		return $template;
	}

	/**
	 * @param $template
	 *
	 * @return bool|string
	 *
	 * @since v.1.0.0
	 */
	public function student_public_profile($template){
		global $wp_query;

		if ( ! empty($wp_query->query['tutor_student_username'])){
			$template = tutor_get_template( 'student-public-profile' );
		}

		return $template;
	}

	/**
	 * @return string
	 * Show student Profile
	 *
	 * @since v.1.0.0
	 */
	public function student_public_profile_title(){
		global $wp_query;

		if ( ! empty($wp_query->query['tutor_student_username'])){
			global $wpdb;

			$user_name = sanitize_text_field($wp_query->query['tutor_student_username']);
			$user = $wpdb->get_row($wpdb->prepare("SELECT display_name from {$wpdb->users} WHERE user_login = %s limit 1; ", $user_name));

			if ( ! empty($user->display_name)){
				return sprintf("%s's Profile page ", $user->display_name );
			}
		}
		return '';
	}


}