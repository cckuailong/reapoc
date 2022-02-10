<?php

/**
 * Class Instructor
 * @package TUTOR
 *
 * @since v.1.0.0
 */

namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;


class Instructor {

	protected $error_msgs = '';
	public function __construct() {
		add_action('template_redirect', array($this, 'register_instructor'));
		add_action('template_redirect', array($this, 'apply_instructor'));

		//Add instructor from admin panel.
		add_action('wp_ajax_tutor_add_instructor', array($this, 'add_new_instructor'));

		/**
		 * Instructor Approval
		 * Block Unblock
		 *
		 * @since v.1.5.3
		 */
		add_action('wp_ajax_instructor_approval_action', array($this, 'instructor_approval_action'));

        /**
         * Check if instructor can publish courses
         * @since v.1.5.9
         */
        add_action('tutor_option_save_after', array($this, 'can_publish_tutor_courses'));

		/**
		 * Hide instructor rejection message
		 * @since v1.9.2
		 */
		add_action( 'wp_loaded', array($this, 'hide_instructor_notice') );
    }

	/**
	 * Register new user and mark him as instructor
	 *
	 * @since v.1.0.0
	 */
	public function register_instructor(){
		if ( ! isset($_POST['tutor_action'])  ||  $_POST['tutor_action'] !== 'tutor_register_instructor' ){
			return;
		}
		//Checking nonce
		tutor_utils()->checking_nonce();

		$required_fields = apply_filters('tutor_instructor_registration_required_fields', array(
			'first_name'                => __('First name field is required', 'tutor'),
			'last_name'                 =>  __('Last name field is required', 'tutor'),
			'email'                     => __('E-Mail field is required', 'tutor'),
			'user_login'                => __('User Name field is required', 'tutor'),
			'password'                  => __('Password field is required', 'tutor'),
			'password_confirmation'     => __('Password Confirmation field is required', 'tutor'),
		));

		$validation_errors = array();

		/*
		*registration_errors
		*push into validation_errors	
		*/
		$errors = apply_filters('registration_errors',new  \WP_Error,'','');
		foreach ($errors->errors as $key => $value) 
		{
		 	$validation_errors[$key] = $value[0];
		 	
		}
				
		foreach ($required_fields as $required_key => $required_value){
			if (empty($_POST[$required_key])){
				$validation_errors[$required_key] = $required_value;
			}
		}

		if (!filter_var(tutor_utils()->input_old('email'), FILTER_VALIDATE_EMAIL)) {
			$validation_errors['email'] = __('Valid E-Mail is required', 'tutor');
		}
		if (tutor_utils()->input_old('password') !== tutor_utils()->input_old('password_confirmation')){
			$validation_errors['password_confirmation'] = __('Confirm password does not matched with Password field', 'tutor');
		}

		if (count($validation_errors)){
			$this->error_msgs = $validation_errors;
			add_filter('tutor_instructor_register_validation_errors', array($this, 'tutor_instructor_form_validation_errors'));
			return;
		}

		$first_name     = sanitize_text_field(tutor_utils()->input_old('first_name'));
		$last_name      = sanitize_text_field(tutor_utils()->input_old('last_name'));
		$email          = sanitize_text_field(tutor_utils()->input_old('email'));
		$user_login     = sanitize_text_field(tutor_utils()->input_old('user_login'));
		$password       = sanitize_text_field(tutor_utils()->input_old('password'));

		$userdata = array(
			'user_login'    =>  $user_login,
			'user_email'    =>  $email,
			'first_name'    =>  $first_name,
			'last_name'     =>  $last_name,
			//'role'          =>  tutor()->instructor_role,
			'user_pass'     =>  $password,
		);

		$user_id = wp_insert_user( $userdata ) ;
		if ( ! is_wp_error($user_id)){
			update_user_meta($user_id, '_is_tutor_instructor', tutor_time());
			update_user_meta($user_id, '_tutor_instructor_status', apply_filters('tutor_initial_instructor_status', 'pending'));

            do_action('tutor_new_instructor_after', $user_id);

            $user = get_user_by( 'id', $user_id );
			if( $user ) {
				wp_set_current_user( $user_id, $user->user_login );
				wp_set_auth_cookie( $user_id );
			}
		}else{
			$this->error_msgs = $user_id->get_error_messages();
			add_filter('tutor_instructor_register_validation_errors', array($this, 'tutor_instructor_form_validation_errors'));
			return;
		}

		wp_redirect(tutor_utils()->input_old('_wp_http_referer'));
		die();
	}

	public function tutor_instructor_form_validation_errors(){
		return $this->error_msgs;
	}

	/**
	 *
	 * Usage for instructor applying when a user already logged in
	 *
	 * @since v.1.0.0
	 */
	public function apply_instructor(){
		if ( ! isset($_POST['tutor_action'])  ||  $_POST['tutor_action'] !== 'tutor_apply_instructor' ){
			return;
		}
		//Checking nonce
		tutor_utils()->checking_nonce();

		$user_id = get_current_user_id();
		if ($user_id){
			if (tutor_utils()->is_instructor()){
				die(__('Already applied for instructor', 'tutor'));
			}else{
				update_user_meta($user_id, '_is_tutor_instructor', tutor_time());
				update_user_meta($user_id, '_tutor_instructor_status', apply_filters('tutor_initial_instructor_status', 'pending'));

                do_action('tutor_new_instructor_after', $user_id);
            }
		}else{
			die(__('Permission denied', 'tutor'));
		}

		wp_redirect(tutor_utils()->input_old('_wp_http_referer'));
		die();
	}


	public function add_new_instructor(){

		tutils()->checking_nonce();
		
		// Only admin should be able to add instructor
		if(!current_user_can( 'manage_options' ) || !get_option( 'users_can_register', false )) {
			wp_send_json_error( );
		}
		

		$required_fields = apply_filters('tutor_instructor_registration_required_fields', array(
			'first_name'                => __('First name field is required', 'tutor'),
			'last_name'                 =>  __('Last name field is required', 'tutor'),
			'email'                     => __('E-Mail field is required', 'tutor'),
			'user_login'                => __('User Name field is required', 'tutor'),
			'phone_number'              => __('Phone Number field is required', 'tutor'),
			'password'                  => __('Password field is required', 'tutor'),
			'password_confirmation'     => __('Password Confirmation field is required', 'tutor'),
		));

		$validation_errors = array();
		foreach ($required_fields as $required_key => $required_value){
			if (empty($_POST[$required_key])){
				$validation_errors[$required_key] = $required_value;
			}
		}

		if (!filter_var(tutor_utils()->input_old('email'), FILTER_VALIDATE_EMAIL)) {
			$validation_errors['email'] = __('Valid E-Mail is required', 'tutor');
		}
		if (tutor_utils()->input_old('password') !== tutor_utils()->input_old('password_confirmation')){
			$validation_errors['password_confirmation'] = __('Confirm password does not matched with Password field', 'tutor');
		}

		if (count($validation_errors)){
			wp_send_json_error(array('errors' => $validation_errors));
		}

		$first_name     = sanitize_text_field(tutor_utils()->input_old('first_name'));
		$last_name      = sanitize_text_field(tutor_utils()->input_old('last_name'));
		$email          = sanitize_text_field(tutor_utils()->input_old('email'));
		$user_login     = sanitize_text_field(tutor_utils()->input_old('user_login'));
		$phone_number   = sanitize_text_field(tutor_utils()->input_old('phone_number'));
		$password       = sanitize_text_field(tutor_utils()->input_old('password'));
		$tutor_profile_bio = wp_kses_post(tutor_utils()->input_old('tutor_profile_bio'));

		$userdata = apply_filters('add_new_instructor_data', array(
			'user_login'    =>  $user_login,
			'user_email'    =>  $email,
			'first_name'    =>  $first_name,
			'last_name'     =>  $last_name,
			'role'          =>  tutor()->instructor_role,
			'user_pass'     =>  $password,
		));

		do_action('tutor_add_new_instructor_before');

		$user_id = wp_insert_user( $userdata ) ;
		if ( ! is_wp_error($user_id)) {
			update_user_meta($user_id, 'phone_number', $phone_number);
			update_user_meta($user_id, 'description', $tutor_profile_bio);
			update_user_meta($user_id, '_tutor_profile_bio', $tutor_profile_bio);
			update_user_meta($user_id, '_is_tutor_instructor', tutor_time());
			update_user_meta($user_id, '_tutor_instructor_status', apply_filters('tutor_initial_instructor_status', 'approved'));

			do_action('tutor_add_new_instructor_after', $user_id);

			wp_send_json_success(array('msg' => __('Instructor has been added successfully', 'tutor') ));
		}

		wp_send_json_error(array('errors' => $user_id));
	}

	public function instructor_approval_action(){
		tutils()->checking_nonce();

		if(!current_user_can( 'manage_options' )) {
			wp_send_json_error( array('message' =>__('Access Denied', 'tutor')) );
		}

		$instructor_id = (int) sanitize_text_field(tutils()->array_get('instructor_id', $_POST));
		$action = sanitize_text_field(tutils()->array_get('action_name', $_POST));

		if( 'approve' === $action ) {
			do_action('tutor_before_approved_instructor', $instructor_id);

			update_user_meta($instructor_id, '_tutor_instructor_status', 'approved');
			update_user_meta($instructor_id, '_tutor_instructor_approved', tutor_time());

			$instructor = new \WP_User($instructor_id);
			$instructor->add_role(tutor()->instructor_role);

			// Send E-Mail to this user about instructor approval via hook
			do_action('tutor_after_approved_instructor', $instructor_id);
		}

		if( 'blocked' === $action ) {
			do_action('tutor_before_blocked_instructor', $instructor_id);
			update_user_meta($instructor_id, '_tutor_instructor_status', 'blocked');

			$instructor = new \WP_User($instructor_id);
			$instructor->remove_role(tutor()->instructor_role);
			do_action('tutor_after_blocked_instructor', $instructor_id);

			//TODO: send E-Mail to this user about instructor blocked, should via hook
		}

		if( 'remove-instructor' === $action)
		{
			do_action('tutor_before_rejected_instructor', $instructor_id);

			$user = new \WP_User($instructor_id);
			$user->remove_role(tutor()->instructor_role);

			tutor_utils()->remove_instructor_role($instructor_id);
			update_user_meta($instructor_id, '_is_tutor_instructor_rejected', tutor_time());
			update_user_meta($instructor_id, 'tutor_instructor_show_rejection_message', true);

			// Send E-Mail to this user about instructor rejection via hook
			do_action('tutor_after_rejected_instructor', $instructor_id);
		}

		wp_send_json_success();
	}

	public function hide_instructor_notice() {
		if(isset( $_GET['tutor_action'] ) && $_GET['tutor_action']=='hide_instructor_notice') {
			delete_user_meta( get_current_user_id(), 'tutor_instructor_show_rejection_message' );
		}
	}

    /**
     * Can instructor publish courses directly
     * Fixed in Gutenberg @since v.1.5.9
     */

	public function can_publish_tutor_courses(){
        $can_publish_course = (bool) tutor_utils()->get_option('instructor_can_publish_course');

        $instructor_role = tutor()->instructor_role;
        $instructor = get_role( $instructor_role );

        if ($can_publish_course){
            $instructor->add_cap('publish_tutor_courses');
        }else{
            $instructor->remove_cap('publish_tutor_courses');
        }
    }

}