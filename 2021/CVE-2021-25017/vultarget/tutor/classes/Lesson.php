<?php
namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Lesson extends Tutor_Base {
	public function __construct() {
		parent::__construct();

		add_action( 'add_meta_boxes', array($this, 'register_meta_box') );
		add_action('save_post_'.$this->lesson_post_type, array($this, "save_lesson_meta"));

		add_action('wp_ajax_tutor_load_edit_lesson_modal', array($this, "tutor_load_edit_lesson_modal"));
		add_action('wp_ajax_tutor_modal_create_or_update_lesson', array($this, "tutor_modal_create_or_update_lesson"));
		add_action('wp_ajax_tutor_delete_lesson_by_id', array($this, "tutor_delete_lesson_by_id"));

		add_filter('get_sample_permalink', array($this, 'change_lesson_permalink'), 10, 2);
		add_action('admin_init', array($this, 'flush_rewrite_rules'));

		/**
		 * Add Column
		 */

		add_filter( "manage_{$this->lesson_post_type}_posts_columns", array($this, 'add_column'), 10,1 );
		add_action( "manage_{$this->lesson_post_type}_posts_custom_column" , array($this, 'custom_lesson_column'), 10, 2 );

		//Frontend Action
		add_action('template_redirect', array($this, 'mark_lesson_complete'));

		add_action('wp_ajax_tutor_render_lesson_content', array($this, "tutor_render_lesson_content"));
		add_action('wp_ajax_nopriv_tutor_render_lesson_content', array($this, "tutor_render_lesson_content")); // For public course access

		/**
		 * Autoplay next video
		 * @since v.1.4.9
		 */
		add_action('wp_ajax_autoload_next_course_content', array($this, 'autoload_next_course_content'));

		/**
		 * Load next course item after click complete button
		 * @since v.1.5.3
		 */
		add_action('tutor_lesson_completed_after', array($this, 'tutor_lesson_completed_after'), 999);
	}

	/**
	 * Registering metabox
	 */
	public function register_meta_box(){
		$lesson_post_type = $this->lesson_post_type;

		add_meta_box( 'tutor-course-select', __( 'Select Course', 'tutor' ), array($this, 'lesson_metabox'), $lesson_post_type );
		add_meta_box( 'tutor-lesson-videos', __( 'Lesson Video', 'tutor' ), array($this, 'lesson_video_metabox'), $lesson_post_type );
		add_meta_box( 'tutor-lesson-attachments', __( 'Attachments', 'tutor' ), array($this, 'lesson_attachments_metabox'), $lesson_post_type );
	}

	public function lesson_metabox(){
		include  tutor()->path.'views/metabox/lesson-metabox.php';
	}

	public function lesson_video_metabox(){
		include  tutor()->path.'views/metabox/video-metabox.php';
	}

	public function lesson_attachments_metabox(){
		include  tutor()->path.'views/metabox/lesson-attachments-metabox.php';
	}

	/**
	 * @param $post_ID
	 *
	 * Saving lesson meta and assets
	 *
	 */
	public function save_lesson_meta($post_ID){
		//Course
		if (isset($_POST['selected_course'])) {
			$course_id = (int) sanitize_text_field( $_POST['selected_course'] );
			if ( $course_id ) {
				update_post_meta( $post_ID, '_tutor_course_id_for_lesson', $course_id );
			}
		}

		//Video
		$video_source = sanitize_text_field( tutils()->array_get('video.source', $_POST) );
		if ( $video_source === '-1'){
			delete_post_meta($post_ID, '_video');
		}elseif($video_source) {
			$video = (array)tutor_utils()->array_get('video', $_POST, array());
			update_post_meta($post_ID, '_video', $video);
		}

		//Attachments
		$attachments = array();
		if ( ! empty($_POST['tutor_attachments'])){
			$attachments = tutor_utils()->sanitize_array($_POST['tutor_attachments']);
			$attachments = array_unique($attachments);
		} 

		/**
		 * it !empty attachment then update meta else
		 * delete meta key to prevetn empty data in db
		 * @since 1.8.9
		*/
		if( ! empty($attachments) ) {
			update_post_meta($post_ID, '_tutor_attachments', $attachments);
		} else {
			delete_post_meta($post_ID, '_tutor_attachments');
		}
		
	}

	public function tutor_load_edit_lesson_modal(){
		tutils()->checking_nonce();

		$lesson_id = (int) tutor_utils()->avalue_dot('lesson_id', $_POST);
		$topic_id = (int) sanitize_text_field( $_POST['topic_id'] );

		if(!tutils()->can_user_manage('topic', $topic_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		/**
		 * If Lesson Not Exists, provide dummy
		 */
		$post_arr = array(
			'ID' 		   => 0, 
			'post_content' => '',
			'post_type'    => $this->lesson_post_type,
			'post_title'   => __('Draft Lesson', 'tutor'),
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $topic_id,
		);

		$post = $lesson_id ? get_post($lesson_id) : (object)$post_arr;
		
		ob_start();
		include tutor()->path.'views/modal/edit-lesson.php';
		$output = ob_get_clean();

		wp_send_json_success(array('output' => $output));
	}

	/**
	 * @since v.1.0.0
	 * @updated v.1.5.1
	 */
	public function tutor_modal_create_or_update_lesson(){
		tutils()->checking_nonce();

		global $wpdb;
		
		$lesson_id = (int) sanitize_text_field(tutor_utils()->avalue_dot('lesson_id', $_POST));
		$topic_id = (int) sanitize_text_field(tutor_utils()->avalue_dot('current_topic_id', $_POST));
		$course_id = tutor_utils()->get_course_id_by('topic', $topic_id);
		$_lesson_thumbnail_id = (int) sanitize_text_field(tutor_utils()->avalue_dot('_lesson_thumbnail_id', $_POST));
		
		if(!tutils()->can_user_manage('topic', $topic_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}


		$title = sanitize_text_field($_POST['lesson_title']);
		$lesson_content = wp_kses_post($_POST['lesson_content']);

		$lesson_data = array(
			'post_type'    => $this->lesson_post_type,
			'post_title'   => $title,
			'post_name'    => sanitize_title($title),
			'post_content' => $lesson_content,
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_parent'  => $topic_id
		);

		if($lesson_id==0) {

			$lesson_data['menu_order'] = tutor_utils()->get_next_course_content_order_id( $topic_id );
			$lesson_id = wp_insert_post( $lesson_data );

			if ($lesson_id ) {
				do_action('tutor/lesson/created', $lesson_id);
				update_post_meta( $lesson_id, '_tutor_course_id_for_lesson', $course_id );
			} else {
				wp_send_json_error( array('message' => __('Couldn\'t create lesson.', 'tutor')) );
			}
		} else {
			$lesson_data['ID']=$lesson_id;

			do_action('tutor/lesson_update/before', $lesson_id);
			wp_update_post($lesson_data);
			if ($_lesson_thumbnail_id){
				update_post_meta($lesson_id, '_thumbnail_id', $_lesson_thumbnail_id);
			} else {
				delete_post_meta($lesson_id, '_thumbnail_id');
			}
			do_action('tutor/lesson_update/after', $lesson_id);
		}
		
		ob_start();
		include  tutor()->path.'views/metabox/course-contents.php';
		$course_contents = ob_get_clean();

		wp_send_json_success(array('course_contents' => $course_contents));
	}

	/**
	 * Delete Lesson from course builder
	 */
	public function tutor_delete_lesson_by_id(){
		tutils()->checking_nonce();

		$lesson_id = (int) sanitize_text_field(tutor_utils()->avalue_dot('lesson_id', $_POST));

		if(!tutils()->can_user_manage('lesson', $lesson_id)) {
			wp_send_json_error( array('message'=>__('Access Denied', 'tutor')) );
		}

		wp_delete_post($lesson_id, true);
		delete_post_meta($lesson_id, '_tutor_course_id_for_lesson');
		wp_send_json_success();
	}


	/**
	 * @param $uri
	 * @param $lesson_id
	 *
	 * @return mixed
	 *
	 * Changed the URI based
	 */

	public function change_lesson_permalink($uri, $lesson_id){
		$post = get_post($lesson_id);

		if ($post && $post->post_type === $this->lesson_post_type){
			$uri_base = trailingslashit(site_url());

			$sample_course = "sample-course";
			$is_course = tutor_utils()->get_course_id_by('lesson', get_the_ID());
			if ($is_course){
				$course = get_post($is_course);
				if ($course){
					$sample_course = $course->post_name;
				}
			}

			$new_course_base = $uri_base."course/{$sample_course}/lesson/%pagename%/";
			$uri[0] = $new_course_base;
		}

		return $uri;
	}


	public function flush_rewrite_rules(){
		$is_required_flush = get_option('required_rewrite_flush');
		if ($is_required_flush){
			flush_rewrite_rules();
			delete_option( 'required_rewrite_flush' );
		}
	}


	public function add_column($columns){
		$date_col = $columns['date'];
		unset($columns['date']);
		$columns['course'] = __('Course', 'tutor');
		$columns['date'] = $date_col;

		return $columns;
	}

	/**
	 * @param $column
	 * @param $post_id
	 *
	 */
	public function custom_lesson_column($column, $post_id ){
		if ($column === 'course'){

			$course_id = tutor_utils()->get_course_id_by('lesson', $post_id);
			if ($course_id){
				echo '<a href="'.admin_url('post.php?post='.$course_id.'&action=edit').'">'.get_the_title($course_id).'</a>';
			}

		}
	}

	/**
	 *
	 * Mark lesson completed
	 *
	 * @since v.1.0.0
	 */
	public function mark_lesson_complete(){
		if ( ! isset($_POST['tutor_action'])  ||  $_POST['tutor_action'] !== 'tutor_complete_lesson' ){
			return;
		}
		//Checking nonce
		tutor_utils()->checking_nonce();

		$user_id = get_current_user_id();

		//TODO: need to show view if not signed_in
		if ( ! $user_id){
			die(__('Please Sign-In', 'tutor'));
		}

		$lesson_id = (int) sanitize_text_field($_POST['lesson_id']);

		do_action('tutor_lesson_completed_before', $lesson_id);
		/**
		 * Marking lesson at user meta, meta format, _tutor_completed_lesson_id_{id} and value = tutor_time();
		 */
		tutor_utils()->mark_lesson_complete($lesson_id);

		do_action('tutor_lesson_completed_after', $lesson_id, $user_id);
	}

	/**
	 * Render the lesson content
	 */
	public function tutor_render_lesson_content(){
		tutils()->checking_nonce();

		$lesson_id = (int) sanitize_text_field(tutor_utils()->avalue_dot('lesson_id', $_POST));

		$ancestors = get_post_ancestors($lesson_id); 
		$course_id = !empty($ancestors) ? array_pop($ancestors): $lesson_id;

		// Course must be public or current user must be enrolled to access this lesson
		if(get_post_meta($course_id, '_tutor_is_public_course', true)!=='yes' && !tutils()->is_enrolled($course_id)){
			
			$is_admin = tutor_utils()->has_user_role('administrator');
			$allowed = $is_admin ? true : tutor_utils()->is_instructor_of_this_course(get_current_user_id(), $course_id);

			if( !$allowed ) {
				http_response_code(400);
				exit;
			}
		}

		ob_start();
		global $post;

		$post = get_post($lesson_id);
		setup_postdata($post);
		tutor_lesson_content();
		wp_reset_postdata();

		$html = ob_get_clean();
		wp_send_json_success(array('html' => $html));
	}

	/**
	 * Load next course item automatically
	 *
	 * @since v.1.4.9
	 */
	public function autoload_next_course_content(){
		tutor_utils()->checking_nonce();

		$post_id = sanitize_text_field($_POST['post_id']);
		$content_id = tutils()->get_post_id($post_id);
		$contents = tutor_utils()->get_course_prev_next_contents_by_id($content_id);

		$autoload_course_content = (bool) get_tutor_option('autoload_next_course_content');
		$next_url = false;
		if($autoload_course_content) {
			$next_url = get_the_permalink($contents->next_id);
		}
		wp_send_json_success(array('next_url' => $next_url));
	}

	/**
	 * Load next course item after click complete button
	 *
	 * @since v.1.5.3
	 */
	public function tutor_lesson_completed_after($content_id){
		$contents = tutor_utils()->get_course_prev_next_contents_by_id($content_id);
		$autoload_course_content = (bool) get_tutor_option('autoload_next_course_content');
		if($autoload_course_content) {
			wp_redirect(get_the_permalink($contents->next_id));
		} else {
			wp_redirect(get_the_permalink($content_id));
		}
		die();
	}

}


