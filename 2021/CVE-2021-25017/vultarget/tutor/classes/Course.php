<?php
namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Course extends Tutor_Base {
	
	private $additional_meta=array(
		'_tutor_disable_qa',
		'_tutor_is_public_course'
	);

	public function __construct() {
		parent::__construct();
		
		add_action( 'add_meta_boxes', array($this, 'register_meta_box') );
		add_action('save_post_'.$this->course_post_type, array($this, 'save_course_meta'), 10, 2);
		add_action('wp_ajax_tutor_add_course_topic', array($this, 'tutor_add_course_topic'));
		add_action('wp_ajax_tutor_update_topic', array($this, 'tutor_update_topic'));

		//Add Column
		add_filter( "manage_{$this->course_post_type}_posts_columns", array($this, 'add_column'), 10,1 );
		add_action( "manage_{$this->course_post_type}_posts_custom_column" , array($this, 'custom_lesson_column'), 10, 2 );

		add_action('admin_action_tutor_delete_topic', array($this, 'tutor_delete_topic'));
		add_action('admin_action_tutor_delete_announcement', array($this, 'tutor_delete_announcement'));

		//Frontend Action
		add_action('template_redirect', array($this, 'enroll_now'));
		add_action('init', array($this, 'mark_course_complete'));

		//Modal Perform
		add_action('wp_ajax_tutor_load_instructors_modal', array($this, 'tutor_load_instructors_modal'));
		add_action('wp_ajax_tutor_add_instructors_to_course', array($this, 'tutor_add_instructors_to_course'));
		add_action('wp_ajax_detach_instructor_from_course', array($this, 'detach_instructor_from_course'));

		/**
		 * Frontend Dashboard
		 */
		add_action('wp_ajax_tutor_delete_dashboard_course', array($this, 'tutor_delete_dashboard_course'));

		/**
		 * Gutenberg author support
		 */
		add_filter('wp_insert_post_data', array($this, 'tutor_add_gutenberg_author'), '99', 2);

		/**
		 * Frontend metabox supports for course builder
		 * @since  v.1.3.4
		 */
		add_action('tutor/dashboard_course_builder_form_field_after', array($this, 'register_meta_box_in_frontend'));

		/**
		 * Do Stuff for the course save from frontend
		 */
		add_action('save_tutor_course', array($this, 'attach_product_with_course'), 10, 2);

		/**
		 * Add course level to course settings
		 * @since v.1.4.1
		 */
		add_action('tutor_course/settings_tab_content/after/general', array($this, 'add_course_level_to_settings'));

		/**
		 * Enable Disable Course Details Page Feature
		 * @since v.1.4.8
		 */
		$this->course_elements_enable_disable();

		/**
		 * @since v.1.4.8
		 * Check if course starting, set meta if starting
		 */
		add_action('tutor_lesson_load_before', array($this, 'tutor_lesson_load_before'));

		/**
		 * @since v.1.4.9
		 * Filter product in shop page
		 */
		$this->filter_product_in_shop_page();

        /**
         * Remove the course price if enrolled
         * @since 1.5.8
         */
		add_filter('tutor_course_price', array($this, 'remove_price_if_enrolled'));

        /**
         * Remove course complete button if course completion is strict mode
         * @since v.1.6.1
         */
        add_filter('tutor_course/single/complete_form', array($this, 'tutor_lms_hide_course_complete_btn'));
		add_filter('get_gradebook_generate_form_html', array($this, 'get_generate_greadbook'));
		
        /**
         * Add social share content in header
         * @since v.1.6.3
         */
		add_action('wp_head', array($this, 'social_share_content'));
		
        /**
         * Delete course data after deleted course
         * @since v.1.6.6
         */
		add_action('deleted_post', array($this, 'delete_tutor_course_data'));
		add_action('tutor/dashboard_course_builder_form_field_after', array($this, 'tutor_course_setting_metabox_frontend'));

		/**
         * Delete course data after deleted course
         * @since v.1.8.2
         */
		add_action('before_delete_post', array($this, 'delete_associated_enrollment'));

		/**
		 * Show only own uploads in media library if user is instructor
		 * @since v1.8.9
		 */
		add_filter('posts_where', array($this, 'restrict_media' ) );

		/**
		 * Restrict new enrol/purchase button if course member limit reached
		 * @since v1.9.0
		 */
		add_filter('tutor_course_restrict_new_entry', array($this, 'restrict_new_student_entry'));

		/**
		 * Reset course progress on retake
		 * @since v1.9.5
		 */
		add_action( 'wp_ajax_tutor_reset_course_progress', array($this, 'tutor_reset_course_progress') );
	
		/**
		 * Popup for review
		 * @since v1.9.7
		 */
		add_action( 'wp_footer', array($this, 'popup_review_form') );

		/**
		 * Do enroll after login if guest take enroll attempt
		 * 
		 * @since 1.9.8
		 */
		add_action(  'tutor_do_enroll_after_login_if_attempt', array( $this, 'enroll_after_login_if_attempt' ), 10, 1 );
	}

	public function restrict_new_student_entry($content) {

		if(!tutils()->is_course_fully_booked()) {
			// No restriction if not fully booked
			return $content;
		}
			
		return '<span class="tutor-course-booked-fully">
			<img src="' . tutor()->url . '/assets/images/icon-warning-info.svg"/>
			<span>' . __('Fully booked', 'tutor') . '</span>
		</span>';
	}

	function restrict_media( $where ){
		
		if( isset( $_POST['action'] ) && $_POST['action'] == 'query-attachments' && tutor_utils()->is_instructor()){
			if(!tutor_utils()->has_user_role(array('administrator', 'editor'))) {
				$where .= ' AND post_author=' . get_current_user_id();
			}
		}
	
		return $where;
	}

	/**
	 * Registering metabox
	 */
	public function register_meta_box(){
		$coursePostType = tutor()->course_post_type;
		$course_marketplace = tutor_utils()->get_option('enable_course_marketplace');
        //add_meta_box( 'tutor-course-levels', __( 'Course Level', 'tutor' ), array($this, 'course_level_metabox'), $coursePostType );
		add_meta_box( 'tutor-course-topics', __( 'Course Builder', 'tutor' ), array($this, 'course_meta_box'), $coursePostType );
		add_meta_box( 'tutor-course-additional-data', __( 'Additional Data', 'tutor' ), array($this, 'course_additional_data_meta_box'), $coursePostType );
		add_meta_box( 'tutor-course-videos', __( 'Video', 'tutor' ), array($this, 'video_metabox'), $coursePostType );
		if ($course_marketplace) {
			add_meta_box( 'tutor-instructors', __( 'Instructors', 'tutor' ), array( $this, 'instructors_metabox' ), $coursePostType );
		}

		/**
         * Tutor course sidebar settings metabox
         * @since v.1.7.0
         */
		add_meta_box( 'tutor-course-sidebar-settings', __( 'Tutor Settings', 'tutor' ), array($this, 'tutor_course_setting_metabox'), $coursePostType, 'side' );
	}

	public function course_meta_box($echo = true){
		ob_start();
		include  tutor()->path.'views/metabox/course-topics.php';
		$content = ob_get_clean();

		if ($echo){
			echo $content;
		}else{
			return $content;
		}
	}

	public function course_additional_data_meta_box($echo = true){

		ob_start();
		include  tutor()->path.'views/metabox/course-additional-data.php';
		$content = ob_get_clean();

		if ($echo){
			echo $content;
		}else{
			return $content;
		}
	}

	public function video_metabox($echo = true){
		ob_start();
		include  tutor()->path.'views/metabox/video-metabox.php';
		$content = ob_get_clean();

		if ($echo){
			echo $content;
		}else{
			return $content;
		}
	}

	public function course_level_metabox($echo = true){
		ob_start();
		include  tutor()->path.'views/metabox/course-level-metabox.php';
		$content = ob_get_clean();

		if ($echo){
			echo $content;
		}else{
			return $content;
		}
	}

	public function instructors_metabox($echo = true){
		ob_start();
		include tutor()->path . 'views/metabox/instructors-metabox.php';
		$content = ob_get_clean();

		if ($echo){
			echo $content;
		}else{
			return $content;
		}
	}

	/**
	 * Register metabox in course builder tutor
	 * @since v.1.3.4
	 */
	public function register_meta_box_in_frontend(){
		do_action('tutor_course_builder_metabox_before', get_the_ID());
        course_builder_section_wrap($this->video_metabox($echo = false), __( 'Video', 'tutor' ) );
        course_builder_section_wrap($this->course_meta_box($echo = false), __( 'Course Builder', 'tutor' ) );
        course_builder_section_wrap($this->instructors_metabox($echo = false), __( 'Instructors', 'tutor' ) );
        course_builder_section_wrap($this->course_additional_data_meta_box($echo = false), __( 'Additional Data', 'tutor' ) );
		do_action('tutor_course_builder_metabox_after', get_the_ID());
	}

	/**
	 * @param $post_ID
	 *
	 * Insert Topic and attached it with Course
	 */
	public function save_course_meta($post_ID, $post){
		global $wpdb;

		do_action( "tutor_save_course", $post_ID, $post);
		
		/**
		 * Save course price type
		 */
		$price_type = tutils()->array_get('tutor_course_price_type', $_POST);
		if ($price_type){
			update_post_meta($post_ID, '_tutor_course_price_type', $price_type);
		}

		//Course Duration
		if ( ! empty($_POST['course_duration'])){
			$video = tutils()->sanitize_array($_POST['course_duration']);
			update_post_meta($post_ID, '_course_duration', $video);
		}

		if ( ! empty($_POST['course_level'])){
			$course_level = sanitize_text_field($_POST['course_level']);
			update_post_meta($post_ID, '_tutor_course_level', $course_level);
		}

		$additional_data_edit = tutils()->avalue_dot('_tutor_course_additional_data_edit', $_POST);
		if ($additional_data_edit) {
			if (!empty($_POST['course_benefits'])) {
				$course_benefits = wp_kses_post($_POST['course_benefits']);
				update_post_meta($post_ID, '_tutor_course_benefits', $course_benefits);
			} else {
				delete_post_meta($post_ID, '_tutor_course_benefits');
			}

			if (!empty($_POST['course_requirements'])) {
				$requirements = wp_kses_post($_POST['course_requirements']);
				update_post_meta($post_ID, '_tutor_course_requirements', $requirements);
			} else {
				delete_post_meta($post_ID, '_tutor_course_requirements');
			}

			if (!empty($_POST['course_target_audience'])) {
				$target_audience = wp_kses_post($_POST['course_target_audience']);
				update_post_meta($post_ID, '_tutor_course_target_audience', $target_audience);
			} else {
				delete_post_meta($post_ID, '_tutor_course_target_audience');
			}

			if (!empty($_POST['course_material_includes'])) {
				$material_includes = wp_kses_post($_POST['course_material_includes']);
				update_post_meta($post_ID, '_tutor_course_material_includes', $material_includes);
			} else {
				delete_post_meta($post_ID, '_tutor_course_material_includes');
			}
		}


		/**
		 * Sorting Topics and lesson
		 */
		if ( ! empty($_POST['tutor_topics_lessons_sorting'])){
			$new_order = sanitize_text_field(stripslashes($_POST['tutor_topics_lessons_sorting']));
			$order = json_decode($new_order, true);

			if (is_array($order) && count($order)){
				$i = 0;
				foreach ($order as $topic ){
					$i++;
					$wpdb->update(
						$wpdb->posts,
						array('menu_order' => $i),
						array('ID' => $topic['topic_id'])
					);

					/**
					 * Removing All lesson with topic
					 */

					$wpdb->update(
						$wpdb->posts,
						array('post_parent' => 0),
						array('post_parent' => $topic['topic_id'])
					);

					/**
					 * Lesson Attaching with topic ID
					 * sorting lesson
					 */
					if (isset($topic['lesson_ids'])){
						$lesson_ids = $topic['lesson_ids'];
					}else{
						$lesson_ids = array();
					}
					if (count($lesson_ids)){
						foreach ($lesson_ids as $lesson_key => $lesson_id ){
							$wpdb->update(
								$wpdb->posts,
								array('post_parent' => $topic['topic_id'], 'menu_order' => $lesson_key),
								array('ID' => $lesson_id)
							);
						}
					}
				}
			}
		}
		
		if ($additional_data_edit) {
			if ( ! empty($_POST['video']['source'])) { //Video
				$video = tutor_utils()->array_get('video', $_POST);
				update_post_meta($post_ID, '_video', $video);
			}else{
				delete_post_meta($post_ID, '_video');
			}
		}

		/**
		 * Adding author to instructor automatically
		 */

		$author_id = $post->post_author;
		$attached = (int) $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(umeta_id) FROM {$wpdb->usermeta} 
			WHERE user_id = %d AND meta_key = '_tutor_instructor_course_id' AND meta_value = %d ", $author_id, $post_ID));

		if ( ! $attached){
			add_user_meta($author_id, '_tutor_instructor_course_id', $post_ID);
		}

		/**
		 * Disable question and answer for this course
		 * @since 1.7.0
		 */
		if ($additional_data_edit) {
			foreach($this->additional_meta as $key){
				update_post_meta($post_ID, $key, (isset($_POST[$key]) ? 'yes' : 'no'));
			}
		}

		do_action( "tutor_save_course_after", $post_ID, $post);
	}

	/**
	 * Tutor add course topic
	 */
	public function tutor_add_course_topic(){
		tutils()->checking_nonce();

		if (empty($_POST['topic_title']) ) {
			wp_send_json_error();
		}
		$course_id = (int) tutor_utils()->avalue_dot('tutor_topic_course_ID', $_POST);
		$next_topic_order_id = tutor_utils()->get_next_topic_order_id($course_id);
		
		if(!tutils()->can_user_manage('course', $course_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$topic_title   = sanitize_text_field( $_POST['topic_title'] );
		$topic_summery = wp_kses_post( $_POST['topic_summery'] );

		$post_arr = array(
			'post_type'    => 'topics',
			'post_title'   => $topic_title,
			'post_content' => $topic_summery,
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $course_id,
			'menu_order'  => $next_topic_order_id,
		);
		$current_topic_id = wp_insert_post( $post_arr );

		ob_start();
		include  tutor()->path.'views/metabox/course-contents.php';
		$course_contents = ob_get_clean();

		wp_send_json_success(array('course_contents' => $course_contents));
	}

	/**
	 * Update the topic
	 */
	public function tutor_update_topic(){
		tutils()->checking_nonce();

		$topic_id = (int) sanitize_text_field($_POST['topic_id']);
		$topic_title = sanitize_text_field($_POST['topic_title']);
		$topic_summery = wp_kses_post($_POST['topic_summery']);

		if(!tutils()->can_user_manage('topic', $topic_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		$topic_attr = array(
			'ID'           => $topic_id,
			'post_title'   => $topic_title,
			'post_content' => $topic_summery,
		);
		wp_update_post( $topic_attr );

		wp_send_json_success(array('msg' => __('Topic has been updated', 'tutor') ));
	}


	/**
	 * @param $columns
	 *
	 * @return mixed
	 *
	 * Add Lesson column
	 */
	public function add_column($columns){
		$date_col = $columns['date'];
		unset($columns['date']);
		$columns['lessons'] = __('Lessons', 'tutor');
		$columns['students'] = __('Students', 'tutor');
		$columns['price'] = __('Price', 'tutor');
		$columns['date'] = $date_col;

		return $columns;
	}

	/**
	 * @param $column
	 * @param $post_id
	 *
	 */
	public function custom_lesson_column($column, $post_id ){
		if ($column === 'lessons'){
			echo tutor_utils()->get_lesson_count_by_course($post_id);
		}

		if ($column === 'students'){
			echo tutor_utils()->count_enrolled_users_by_course($post_id);
		}

		if ($column === 'price'){
			$price = tutor_utils()->get_course_price($post_id);
			if ($price){
				$monetize_by = tutils()->get_option('monetize_by');
				if (function_exists('wc_price') && $monetize_by === 'wc'){
					echo '<span class="tutor-label-success">'.wc_price($price).'</span>';
				}else{
					echo '<span class="tutor-label-success">'.$price.'</span>';
				}
			}else{
				echo apply_filters('tutor-loop-default-price', __( 'free', 'tutor' ));
			}
		}
	}


	public function tutor_delete_topic(){

		tutils()->checking_nonce('get'); 
		
		!isset($_GET['topic_id']) ? exit() : 0;

		global $wpdb;

		$topic_id = (int) sanitize_text_field($_GET['topic_id']);
		$wpdb->update(
			$wpdb->posts,
			array('post_parent' => 0),
			array('post_parent' => $topic_id)
		);

		$wpdb->delete(
			$wpdb->postmeta,
			array('post_id' => $topic_id)
		);

		wp_delete_post($topic_id);
		wp_safe_redirect(wp_get_referer());
	}

	public function tutor_delete_announcement(){
		tutor_utils()->checking_nonce('get');

		$announcement_id = (int) sanitize_text_field($_GET['topic_id']);

		wp_delete_post($announcement_id);
		wp_safe_redirect(wp_get_referer());
	}

	public function enroll_now(){

		//Checking if action comes from Enroll form
		if (tutor_utils()->array_get('tutor_course_action', $_POST) !== '_tutor_course_enroll_now' || ! isset($_POST['tutor_course_id']) ){
			return;
		}
		//Checking Nonce
		tutor_utils()->checking_nonce();

		$user_id = get_current_user_id();
		if ( ! $user_id){
			exit(__('Please Sign In first', 'tutor'));
		}

		$course_id = (int) sanitize_text_field($_POST['tutor_course_id']);
		$user_id = get_current_user_id();

		/**
		 * TODO: need to check purchase information
		 */

		$is_purchasable = tutor_utils()->is_course_purchasable($course_id);

		/**
		 * If is is not purchasable, it's free, and enroll right now
		 *
		 * if purchasable, then process purchase.
		 *
		 * @since: v.1.0.0
		 */
		if ($is_purchasable){
			//process purchase

		}else{
			//Free enroll
			tutor_utils()->do_enroll($course_id);
		}

		$referer_url = wp_get_referer();
		wp_redirect($referer_url);
	}

	/**
	 *
	 * Mark complete completed
	 *
	 * @since v.1.0.0
	 */
	public function mark_course_complete(){
		if ( ! isset($_POST['tutor_action'])  ||  $_POST['tutor_action'] !== 'tutor_complete_course' ){
			return;
		}
		//Checking nonce
		tutor_utils()->checking_nonce();

		$user_id = get_current_user_id();

		//TODO: need to show view if not signed_in
		if ( ! $user_id){
			die(__('Please Sign-In', 'tutor'));
		}

		$course_id = (int) sanitize_text_field($_POST['course_id']);

		do_action('tutor_course_complete_before', $course_id);
		/**
		 * Marking course completed at Comment
		 */

		global $wpdb;

		$date = date("Y-m-d H:i:s", tutor_time());

		//Making sure that, hash is unique
		do{
			$hash = substr(md5(wp_generate_password(32).$date.$course_id.$user_id), 0, 16);
			$hasHash = (int) $wpdb->get_var($wpdb->prepare(
				"SELECT COUNT(comment_ID) from {$wpdb->comments} 
				WHERE comment_agent = 'TutorLMSPlugin' AND comment_type = 'course_completed' AND comment_content = %s ", $hash));

		}while($hasHash > 0);

		$data = array(
			'comment_post_ID'   => $course_id,
			'comment_author'    => $user_id,
			'comment_date'      => $date,
			'comment_date_gmt'  => get_gmt_from_date($date),
			'comment_content'   => $hash, //Identification Hash
			'comment_approved'  => 'approved',
			'comment_agent'     => 'TutorLMSPlugin',
			'comment_type'      => 'course_completed',
			'user_id'           => $user_id,
		);

		$wpdb->insert($wpdb->comments, $data);

		do_action('tutor_course_complete_after', $course_id, $user_id);

		$permalink = get_the_permalink($course_id);

		// Set temporary identifier to show review pop up
		if(!get_tutor_option( 'disable_course_review' )) {
			$rating = tutor_utils()->get_course_rating_by_user($course_id, $user_id);
			if(!$rating || (empty($rating->rating) && empty($rating->review))) {
				update_option( 'tutor_course_complete_popup_'.$user_id, array(
					'course_id' => $course_id,
					'course_url' => $permalink,
					'expires' => time()+10
				));
			}
		}
		
		wp_redirect($permalink);
		exit;
	}

	public function popup_review_form() {
		if(is_user_logged_in()) {
			$key = 'tutor_course_complete_popup_' . get_current_user_id();
			$popup = get_option( $key );

			if(is_array($popup)) {

				if($popup['expires']>time()) {
					$course_id = $popup['course_id'];
					include tutor()->path.'views/modal/review.php';
				}

				delete_option($key);
			}
		}
	}
	
	public function tutor_load_instructors_modal(){
		tutils()->checking_nonce();
		
		global $wpdb;

		$course_id = (int) sanitize_text_field($_POST['course_id']);
		$search_terms = sanitize_text_field(tutor_utils()->avalue_dot('search_terms', $_POST));

		if(!tutils()->can_user_manage('course', $course_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}
		
		$saved_instructors = tutor_utils()->get_instructors_by_course($course_id);
		$instructors = array();

		$not_in_sql = apply_filters('tutor_instructor_query_when_exists', " AND ID <1 ");

		if ($saved_instructors){
			$saved_instructors_ids = wp_list_pluck($saved_instructors, 'ID');
			$instructor_not_in_ids = implode(',', $saved_instructors_ids);
			$not_in_sql .= "AND user.ID NOT IN($instructor_not_in_ids) ";
		}

		$search_sql = '';
		if ($search_terms){
			$search_sql = "AND (user.user_login like '%{$search_terms}%' or user.user_nicename like '%{$search_terms}%' or user.display_name like '%{$search_terms}%') ";
		}

		$instructors = $wpdb->get_results("SELECT user.ID, user.display_name from {$wpdb->users} user
			INNER JOIN {$wpdb->usermeta} meta ON user.ID = meta.user_id AND meta.meta_key = '_tutor_instructor_status' AND meta.meta_value = 'approved'
			WHERE 1=1 {$not_in_sql} {$search_sql} limit 10 ");

		$output = '';
		if (is_array($instructors) && count($instructors)){
			$instructor_output = '';
			foreach ($instructors as $instructor){
				$instructor_output .= "<p><label><input type='radio' name='tutor_instructor_ids[]' value='{$instructor->ID}' > {$instructor->display_name} </label></p>";
			}

			$output .= apply_filters('tutor_course_instructors_html', $instructor_output, $instructors);

		}else{
			$output .= __('<p>No instructor available or you have already added maximum instructors</p>', 'tutor');
		}


		if ( ! defined('TUTOR_MT_VERSION')){
			$output .= '<p class="tutor-notice-warning" style="margin-top: 50px; font-size: 14px;">'. sprintf( __('To add unlimited multiple instructors in your course, get %sTutor LMS Pro%s', 'tutor'), '<a href="https://www.themeum.com/product/tutor-lms" target="_blank">', "</a>" ) .'</p>';
		}

		wp_send_json_success(array('output' => $output));
	}

	public function tutor_add_instructors_to_course(){
		tutils()->checking_nonce();

		$course_id = (int) sanitize_text_field($_POST['course_id']);
		$instructor_ids = tutor_utils()->avalue_dot('tutor_instructor_ids', $_POST);
		
		if(!tutils()->can_user_manage('course', $course_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		if (is_array($instructor_ids) && count($instructor_ids)){
			foreach ($instructor_ids as $instructor_id){
				add_user_meta($instructor_id, '_tutor_instructor_course_id', $course_id);
			}
		}

		$saved_instructors = tutor_utils()->get_instructors_by_course($course_id);
		$output = '';

		if ($saved_instructors){
			foreach ($saved_instructors as $t){

				$output .= '<div id="added-instructor-id-'.$t->ID.'" class="added-instructor-item added-instructor-item-'.$t->ID.'" data-instructor-id="'.$t->ID.'">
                    <span class="instructor-icon">'.get_avatar($t->ID, 30).'</span>
                    <span class="instructor-name"> '.$t->display_name.' </span>
                    <span class="instructor-control">
                        <a href="javascript:;" class="tutor-instructor-delete-btn"><i class="tutor-icon-line-cross"></i></a>
                    </span>
                </div>';
			}
		}

		wp_send_json_success(array('output' => $output));
	}

	public function detach_instructor_from_course(){
		tutils()->checking_nonce();

		global $wpdb;

		$instructor_id = (int) sanitize_text_field($_POST['instructor_id']);
		$course_id = (int) sanitize_text_field($_POST['course_id']);

		if(!tutils()->can_user_manage('course', $course_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}
		
		$wpdb->delete($wpdb->usermeta, array('user_id' => $instructor_id, 'meta_key' => '_tutor_instructor_course_id', 'meta_value' => $course_id) );
		wp_send_json_success();
	}

	public function tutor_delete_dashboard_course(){
		tutils()->checking_nonce();

		$course_id = intval(sanitize_text_field($_POST['course_id']));

		if(!tutils()->can_user_manage('course', $course_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		wp_trash_post($course_id);
		wp_send_json_success(['element'=>'course']);
	}


	public function tutor_add_gutenberg_author($data , $postarr){
		global $wpdb;

		$courses_post_type = tutor()->course_post_type;
		$post_type = tutils()->array_get('post_type', $postarr);

		if ($courses_post_type === $post_type){
            $post_ID = (int) tutor_utils()->avalue_dot('ID', $postarr);
            $post_author = (int) $wpdb->get_var($wpdb->prepare("SELECT post_author FROM {$wpdb->posts} WHERE ID = %d ", $post_ID));

            if ($post_author > 0){
                $data['post_author'] = $post_author;
            }else{
                $data['post_author'] = get_current_user_id();
            }
        }

		return $data;
	}


	/**
	 * @param $post_ID
	 * @param $postData
	 *
	 * Attach product during save course from the frontend course dashboard.
	 * 
	 * @return string
	 *
	 * @since v.1.3.4
	 */
	public function attach_product_with_course($post_ID, $postData){
		$attached_product_id = tutor_utils()->get_course_product_id($post_ID);
		$course_price = sanitize_text_field(tutor_utils()->array_get('course_price', $_POST));

		if ( ! $course_price){
			return;
		}

		$monetize_by = tutor_utils()->get_option('monetize_by');
		$course = get_post($post_ID);

		if ($monetize_by === 'wc'){

			$is_update = false;
			if ($attached_product_id){
				$wc_product = get_post_meta($attached_product_id, '_product_version', true);
				if ($wc_product){
					$is_update = true;
				}
			}

			if ($is_update) {
				$productObj = wc_get_product($attached_product_id);
				$productObj->set_price($course_price); // set product price
				$productObj->set_regular_price($course_price); // set product regular price
				$productObj->set_sold_individually(true);
				$product_id = $productObj->save();
				if($productObj->is_type('subscription')) {
					update_post_meta( $attached_product_id, '_subscription_price', $course_price );
				}
			} else {
				$productObj = new \WC_Product();
				$productObj->set_name($course->post_title);
				$productObj->set_status('publish');
				$productObj->set_price($course_price); // set product price
				$productObj->set_regular_price($course_price); // set product regular price
				$productObj->set_sold_individually(true);

				$product_id = $productObj->save();
				if ($product_id) {
					update_post_meta( $post_ID, '_tutor_course_product_id', $product_id );
					//Mark product for woocommerce
					update_post_meta( $product_id, '_virtual', 'yes' );
					update_post_meta( $product_id, '_tutor_product', 'yes' );

					$coursePostThumbnail = get_post_meta( $post_ID, '_thumbnail_id', true );
					if ( $coursePostThumbnail ) {
						set_post_thumbnail( $product_id, $coursePostThumbnail );
					}
				}
			}

		}elseif ($monetize_by === 'edd'){

			$is_update = false;
			
			if ($attached_product_id){
				$edd_price = get_post_meta($attached_product_id, 'edd_price', true);
				if ($edd_price){
					$is_update = true;
				}
			}

			if ($is_update){
				//Update the product
				update_post_meta( $attached_product_id, 'edd_price', $course_price );
			}else{
				//Create new product

				$post_arr = array(
					'post_type'    => 'download',
					'post_title'   => $course->post_title,
					'post_status'  => 'publish',
					'post_author'  => get_current_user_id(),
				);
				$download_id = wp_insert_post( $post_arr );
				if ($download_id ) {
					//edd_price
					update_post_meta( $download_id, 'edd_price', $course_price );

					update_post_meta( $post_ID, '_tutor_course_product_id', $download_id );
					//Mark product for EDD
					update_post_meta( $download_id, '_tutor_product', 'yes' );

					$coursePostThumbnail = get_post_meta( $post_ID, '_thumbnail_id', true );
					if ( $coursePostThumbnail ) {
						set_post_thumbnail( $download_id, $coursePostThumbnail );
					}
					
				}

			}


		}

	}


	/**
	 * Add Course level to course settings
	 * @since v.1.4.1
	 */
	public function add_course_level_to_settings(){
		include  tutor()->path.'views/metabox/course-level-metabox.php';
	}

	/**
	 * Check if course starting
	 *
	 * @since v.1.4.8
	 */
	public function tutor_lesson_load_before(){
		$course_id = tutils()->get_course_id_by_content(get_the_ID());
		$completed_lessons = tutor_utils()->get_completed_lesson_count_by_course($course_id);
		if (is_user_logged_in()){
			$is_course_started = get_post_meta($course_id, '_tutor_course_started', true);
			if ( ! $completed_lessons && ! $is_course_started){
				update_post_meta($course_id, '_tutor_course_started', tutor_time());
				do_action('tutor/course/started', $course_id);
			}
		}
	}

	/**
	 * Add Course level to course settings
	 * @since v.1.4.8
	 */
	public function course_elements_enable_disable(){
		add_filter('tutor_course/single/completing-progress-bar', array($this, 'enable_disable_course_progress_bar') );
		add_filter('tutor_course/single/material_includes', array($this, 'enable_disable_material_includes') );
		add_filter('tutor_course/single/content', array($this, 'enable_disable_course_content') );
		add_filter('tutor_course/single/benefits_html', array($this, 'enable_disable_course_benefits') );
		add_filter('tutor_course/single/requirements_html', array($this, 'enable_disable_course_requirements') );
		add_filter('tutor_course/single/audience_html', array($this, 'enable_disable_course_target_audience') );
		add_filter('tutor_course/single/enrolled/nav_items', array($this, 'enable_disable_course_nav_items') );
	}

	/**
	 * Enable disable course progress bar
	 * @since v.1.4.8
	 */
	public function enable_disable_course_progress_bar($html){
		$disable_option = (bool) get_tutor_option('disable_course_progress_bar');
		if($disable_option){
			return '';
		}
		return $html;
	}

	/**
	 * Enable disable material includes
	 * @since v.1.4.8
	 */
	public function enable_disable_material_includes($html){
		$disable_option = (bool) get_tutor_option('disable_course_material');
		if($disable_option){
			return '';
		}
		return $html;
	}

	/**
	 * Enable disable course content
	 * @since v.1.4.8
	 */
	public function enable_disable_course_content($html){
		$disable_option = (bool) get_tutor_option('disable_course_description');
		if($disable_option){
			return '';
		}
		return $html;
	}

	/**
	 * Enable disable course benefits
	 * @since v.1.4.8
	 */
	public function enable_disable_course_benefits($html){
		$disable_option = (bool) get_tutor_option('disable_course_benefits');
		if($disable_option){
			return '';
		}
		return $html;
	}

	/**
	 * Enable disable course requirements
	 * @since v.1.4.8
	 */
	public function enable_disable_course_requirements($html){
		$disable_option = (bool) get_tutor_option('disable_course_requirements');
		if($disable_option){
			return '';
		}
		return $html;
	}

	/**
	 * Enable disable course target audience
	 * @since v.1.4.8
	 */
	public function enable_disable_course_target_audience($html){
		$disable_option = (bool) get_tutor_option('disable_course_target_audience');
		if($disable_option){
			return '';
		}
		return $html;
	}
	
	/**
	 * Enable disable course nav items
	 * @since v.1.4.8
	 */
	public function enable_disable_course_nav_items($items){
		global $wp_query, $post;
		$enable_q_and_a_on_course = (bool) get_tutor_option('enable_q_and_a_on_course');
		$disable_course_announcements = (bool) get_tutor_option('disable_course_announcements');

		$disable_qa_for_this_course = ($wp_query->is_single && !empty($post)) ? get_post_meta($post->ID, '_tutor_disable_qa', true) : '';

		if(!$enable_q_and_a_on_course || $disable_qa_for_this_course == 'yes') {
			if(tutils()->array_get('questions', $items)) {
				unset($items['questions']);
			}
		}
		if($disable_course_announcements){
			if(tutils()->array_get('announcements', $items)) {
				unset($items['announcements']);
			}
		}
		return $items;
	}

	/**
	 * Filter product in shop page
	 * @since v.1.4.9
	 */
	public function filter_product_in_shop_page(){
		$hide_course_from_shop_page = (bool) get_tutor_option('hide_course_from_shop_page');
		if(!$hide_course_from_shop_page){
			return;
		}
		add_action('woocommerce_product_query', array($this, 'filter_woocommerce_product_query'));
		add_filter('edd_downloads_query', array($this, 'filter_edd_downloads_query'), 10, 2);
		add_action('pre_get_posts', array($this, 'filter_archive_meta_query'), 1);
	}

	/**
	 * Tutor product meta query
	 * @since v.1.4.9
	 */
	public function tutor_product_meta_query(){
		$meta_query = array(
			'key'     	=> '_tutor_product',
			'compare' 	=> 'NOT EXISTS'
		);
		return $meta_query;
	}

	/**
	 * Filter product in woocommerce shop page
	 * @since v.1.4.9
	 */
	public function filter_woocommerce_product_query($wp_query){
		$wp_query->set('meta_query', array($this->tutor_product_meta_query()));
		return $wp_query;
	}

	/**
	 * Filter product in edd downloads shortcode page
	 * @since v.1.4.9
	 */
	public function filter_edd_downloads_query($query){
		$query['meta_query'][] = $this->tutor_product_meta_query();
		return $query;
	}

	/**
	 * Filter product in edd downloads archive page
	 * @since v.1.4.9
	 */
	public function filter_archive_meta_query($wp_query){
		if(!is_admin() && $wp_query->is_archive && $wp_query->get('post_type') === 'download'){
			$wp_query->set('meta_query', array($this->tutor_product_meta_query()));
		}
		return $wp_query;
	}

    /**
     * @param $html
     * @return string
     *
     * Removed course price if already enrolled at single course
     *
     * @since v.1.5.8
     */
	public function remove_price_if_enrolled($html){
	    $should_removed = apply_filters('should_remove_price_if_enrolled', true);

	    if ($should_removed){
            $course_id = get_the_ID();
	        $enrolled = tutils()->is_enrolled($course_id);
	        if ($enrolled){
	            $html = '';
            }
        }
	    return $html;
    }

    /**
     * @param $html
     * @return string
     *
     * Check if all lessons and quizzes done before mark course complete.
     */
    function tutor_lms_hide_course_complete_btn($html){

	    $completion_mode = tutils()->get_option('course_completion_process');
	    if ($completion_mode !== 'strict'){
	        return $html;
        }

        $completed_lesson = tutils()->get_completed_lesson_count_by_course();
        $lesson_count = tutils()->get_lesson_count_by_course();

        if ($completed_lesson < $lesson_count){
            return '<p class="suggestion-before-course-complete">'.__('complete all lessons to mark this course as complete', 'tutor').'</p>';
        }

        $quizzes = array();

        $course_contents = tutils()->get_course_contents_by_id();
        if (tutils()->count($course_contents)){
            foreach ($course_contents as $content){
                if ($content->post_type === 'tutor_quiz'){
                    $quizzes[] = $content;
                }
            }
        }

        $is_pass = true;
        $required_quiz_pass = 0;

        if (tutils()->count($quizzes)){
            foreach ($quizzes as $quiz){

                $attempt = tutils()->get_quiz_attempt($quiz->ID);
                if ($attempt) {
                    $passing_grade = tutor_utils()->get_quiz_option($quiz->ID, 'passing_grade', 0);
                    $earned_percentage = $attempt->earned_marks > 0 ? (number_format(($attempt->earned_marks * 100) / $attempt->total_marks)) : 0;

                    if ($earned_percentage < $passing_grade) {
                        $required_quiz_pass++;
                        $is_pass = false;
                    }
                }else{
                    $required_quiz_pass++;
                    $is_pass = false;
                }
            }
        }

        if ( ! $is_pass){
            return '<p class="suggestion-before-course-complete">'.sprintf(__('You have to pass %s quizzes to complete this course.', 'tutor'), $required_quiz_pass).'</p>';
        }

        return $html;
    }

    public function get_generate_greadbook($html){
        if ( ! tutils()->is_completed_course()){
            return '';
        }
        return $html;
	}

	/**
	 * Add social share content in header
	 * @since v.1.6.3
	 */
    public function social_share_content(){
		global $wp_query, $post;
		if ($wp_query->is_single && ! empty($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] === $this->course_post_type) { ?>
			<!--Facebook-->
			<meta property="og:type" content="website"/>
			<meta property="og:image" content="<?php echo get_tutor_course_thumbnail_src(); ?>" />
			<meta property="og:description" content="<?php echo esc_html($post->post_content); ?>" />
			<!--Twitter-->
			<meta name="twitter:image" content="<?php echo get_tutor_course_thumbnail_src(); ?>">
			<meta name="twitter:description" content="<?php echo esc_html($post->post_content); ?>">
			<!--Google+-->
			<meta itemprop="image" content="<?php echo get_tutor_course_thumbnail_src(); ?>">
			<meta itemprop="description" content="<?php echo esc_html($post->post_content); ?>"> <?php
		}
	}

	/**
	 * Get posts by type and parent
	 * @since v.1.6.6
	 */
	public function tutor_get_post_ids($post_type, $post_parent) {
		$args = array(
			'fields' 		 => 'ids',
			'post_type'      => $post_type,
			'post_parent'    => $post_parent,
			'post_status'    => 'any',
			'posts_per_page' => -1,
		);
		return get_posts($args);
	}
	
	/**
	 * Delete course data when permanently deleting a course.
	 * @since v.1.6.6
	 */
	function delete_tutor_course_data( $post_id ) {
		$course_post_type = tutor()->course_post_type;
		$lesson_post_type = tutor()->lesson_post_type;

		if (get_post_type($post_id) == $course_post_type) {
			global $wpdb;
			$topic_ids = $this->tutor_get_post_ids('topics', $post_id);
			if ( !empty($topic_ids) ) {
				foreach ($topic_ids as $topic_id) {
					$content_post_type = apply_filters('tutor_course_contents_post_types', array($lesson_post_type, 'tutor_quiz'));
					$topic_content_ids = $this->tutor_get_post_ids($content_post_type, $topic_id);
					
					foreach ($topic_content_ids as $content_id) {
						if( get_post_type($content_id) == 'tutor_quiz') {
							$wpdb->delete($wpdb->prefix.'tutor_quiz_attempts', array('quiz_id' => $content_id));
							$wpdb->delete($wpdb->prefix.'tutor_quiz_attempt_answers', array('quiz_id' => $content_id));

							$questions_ids = $wpdb->get_col($wpdb->prepare("SELECT question_id FROM {$wpdb->prefix}tutor_quiz_questions WHERE quiz_id = %d ", $content_id));
							if (is_array($questions_ids) && count($questions_ids)){
								$in_question_ids = "'".implode("','", $questions_ids)."'";
								$wpdb->query("DELETE FROM {$wpdb->prefix}tutor_quiz_question_answers WHERE belongs_question_id IN({$in_question_ids}) ");
							}
							$wpdb->delete($wpdb->prefix.'tutor_quiz_questions', array('quiz_id' => $content_id));
						}
						wp_delete_post($content_id, true);
					}
					wp_delete_post($topic_id, true);
				}
			}
			$child_post_ids = $this->tutor_get_post_ids(array('tutor_announcements', 'tutor_enrolled'), $post_id);
			if ( !empty($child_post_ids) ) {
				foreach ($child_post_ids as $child_post_id) {
					wp_delete_post($child_post_id, true);
				}
			}
		}
	}

	/**
	 * tutor course setting metabox
	 * @since v.1.7.0
	 */
	function tutor_course_setting_metabox( $post ) {
		
		$disable_qa = $this->additional_meta[0];
		$is_public = $this->additional_meta[1];

		$disable_qa_checked = get_post_meta($post->ID, $disable_qa, true)=='yes' ? 'checked="checked"' : '';
		$is_public_checked = get_post_meta($post->ID, $is_public, true)=='yes' ? 'checked="checked"' : '';

		do_action('tutor_before_course_sidebar_settings_metabox', $post);
		?>
		<div class="tutor-course-sidebar-settings-item" id="_tutor_is_course_public_meta_checkbox" style="display:none">
			<label for="<?php echo $is_public; ?>">
				<input id="<?php echo $is_public; ?>" type="checkbox" name="<?php echo $is_public; ?>" value="yes" <?php echo $is_public_checked; ?> />
				<?php _e('Make This Course Public', 'tutor'); ?>
				<small style="display:block;padding-left:24px">
					<?php _e('No enrollment required.', 'tutor'); ?> 
				</small>
			</label>
		</div>
		<div class="tutor-course-sidebar-settings-item">
			<label for="<?php echo $disable_qa; ?>">
				<input type="hidden" name="_tutor_course_additional_data_edit" value="true" />
				<input id="<?php echo $disable_qa; ?>" type="checkbox" name="<?php echo $disable_qa; ?>" value="yes" <?php echo $disable_qa_checked; ?> />
				<?php _e('Disable Q&A', 'tutor'); ?>
			</label>
		</div>
		<?php
		do_action('tutor_after_course_sidebar_settings_metabox', $post);
	}

	function tutor_course_setting_metabox_frontend( $post ){
		?>		
			<div class="tutor-course-builder-section tutor-course-builder-info">
				<div class="tutor-course-builder-section-title">
					<h3><i class="tutor-icon-down"></i><span><?php esc_html_e('Tutor Settings', 'tutor'); ?></span></h3>
				</div>
				<div class="tutor-course-builder-section-content">
					<div class="tutor-frontend-builder-item-scope">
						<div class="tutor-form-group">
							<?php $this->tutor_course_setting_metabox($post); ?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Delete associated enrollment
	 * @since v.1.8.2
	 */
	public function delete_associated_enrollment($post_id) {
        global $wpdb;

        $enroll_id = $wpdb->get_var( $wpdb->prepare(
			"SELECT 
				post_id 
			FROM 
				{$wpdb->postmeta}
			WHERE 
				meta_key='_tutor_enrolled_by_order_id' 
				AND meta_value = %d
			", 
			$post_id
		) );
        
        if(is_numeric($enroll_id) && $enroll_id>0) {

            $course_id = get_post_field('post_parent', $enroll_id);
            $user_id = get_post_field('post_author', $enroll_id);

            tutils()->cancel_course_enrol($course_id, $user_id);
        }
    }

	public function tutor_reset_course_progress() {
		tutils()->checking_nonce();
		$course_id = tutor_utils()->array_get('course_id', $_POST);

		if(!$course_id || !is_numeric($course_id) || !tutor_utils()->is_enrolled( $course_id )) {
			wp_send_json_error(array('message' => __('Invalid Course ID or Access Denied.', 'tutor')));
			return;
		}

		tutor_utils()->delete_course_progress($course_id);
		wp_send_json_success(array('redirect_to' => tutor_utils()->get_course_first_lesson( $course_id ) ));
	}

	/**
	 * Do enroll if guest attempt to enroll and course is free
	 * 
	 * @param $course_id
	 * 
	 * @since 1.9.8
	 */
	public function enroll_after_login_if_attempt( $course_id ) {
		$course_id = sanitize_text_field( $course_id );
		if ( $course_id ) {
			$is_purchasable = tutor_utils()->is_course_purchasable($course_id);
			if ( !$is_purchasable ) {
				tutor_utils()->do_enroll($course_id);
				do_action( 'guest_attempt_after_enrollment', $course_id );
			}
		}
	}
}