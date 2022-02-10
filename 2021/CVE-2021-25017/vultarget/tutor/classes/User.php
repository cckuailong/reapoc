<?php

namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;


class User {

	private static $hide_registration_notice = false;

	public function __construct() {
		add_action('edit_user_profile', array($this, 'edit_user_profile'));
		add_action('show_user_profile', array($this, 'edit_user_profile'), 10, 1);

		add_action('profile_update', array($this, 'profile_update'));
		add_action('set_user_role', array($this, 'set_user_role'), 10, 3);

		add_action('wp_ajax_tutor_user_photo_remove', array($this, 'tutor_user_photo_remove'));
		add_action('wp_ajax_tutor_user_photo_upload', array($this, 'update_user_photo'));
		
		add_action('tutor_options_after_instructors', array($this, 'tutor_instructor_profile_layout'));
		// add_action('tutor_options_after_students', array($this, 'tutor_student_profile_layout'));

		add_action( 'admin_notices', array( $this, 'show_registration_disabled' ) );
		add_action( 'admin_init', array( $this, 'hide_notices' ) );
	}

	private $profile_layout = array(
		'pp-circle',
		'pp-rectangle',
		'no-cp'
	);

	/**
	 * Show layout selection dashboard in instructor and student setting
	 */
	public function tutor_instructor_profile_layout(){
		tutor_load_template('public-profile-setting', array('profile_templates'=>$this->profile_layout, 'layout_option_name'=>'instructor'));
	}
	public function tutor_student_profile_layout(){
		tutor_load_template('public-profile-setting', array('profile_templates'=>$this->profile_layout, 'layout_option_name'=>'student'));
	}

	public function edit_user_profile($user){
		include  tutor()->path.'views/metabox/user-profile-fields.php';
	}

	private function delete_existing_user_photo($user_id, $type){
		$meta_key = $type=='cover_photo' ? '_tutor_cover_photo' : '_tutor_profile_photo';
		$photo_id = get_user_meta($user_id, $meta_key, true);
		is_numeric($photo_id) ? wp_delete_attachment( $photo_id, true) : 0;
		delete_user_meta( $user_id, $meta_key);
	}

	public function tutor_user_photo_remove(){
		tutils()->checking_nonce();
		
		$this->delete_existing_user_photo(get_current_user_id(), $_POST['photo_type']);
	}

	public function update_user_photo(){
		tutils()->checking_nonce();

		$user_id = get_current_user_id();
		$meta_key = $_POST['photo_type']=='cover_photo' ? '_tutor_cover_photo' : '_tutor_profile_photo';
		
		/**
		 * Photo Update from profile
		 *
		 */
		$photo = tutils()->array_get('photo_file', $_FILES);
		$photo_size = tutils()->array_get('size', $photo);
		$photo_type = tutils()->array_get('type', $photo);

		if ($photo_size && strpos($photo_type, 'image') !== false) {
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$upload_overrides = array( 'test_form' => false );
			$movefile         = wp_handle_upload( $photo, $upload_overrides );

			if ( $movefile && ! isset( $movefile['error'] ) ) {
				$file_path = tutils()->array_get( 'file', $movefile );
				$file_url  = tutils()->array_get( 'url', $movefile );

				$media_id = wp_insert_attachment( array(
					'guid'           => $file_path,
					'post_mime_type' => mime_content_type( $file_path ),
					'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_url ) ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				), $file_path, 0 );

				if ($media_id) {
					// wp_generate_attachment_metadata() won't work if you do not include this file
					require_once( ABSPATH . 'wp-admin/includes/image.php' );

					// Generate and save the attachment metas into the database
					wp_update_attachment_metadata( $media_id, wp_generate_attachment_metadata( $media_id, $file_path ) );

					//Update it to user profile
					$this->delete_existing_user_photo($user_id, $_POST['photo_type']);
					update_user_meta($user_id, $meta_key, $media_id );

					exit(json_encode(array('status'=>'success')));
				}
			}
		}
	}

	public function profile_update($user_id){
		if (tutils()->array_get('tutor_action', $_POST) !== 'tutor_profile_update_by_wp' ){
			return;
		}

		$_tutor_profile_job_title = sanitize_text_field(tutor_utils()->avalue_dot('_tutor_profile_job_title', $_POST));
		$_tutor_profile_bio = wp_kses_post(tutor_utils()->avalue_dot('_tutor_profile_bio', $_POST));
		$_tutor_profile_image = wp_kses_post(tutor_utils()->avalue_dot('_tutor_profile_photo', $_POST));

		update_user_meta($user_id, '_tutor_profile_job_title', $_tutor_profile_job_title);
		update_user_meta($user_id, '_tutor_profile_bio', $_tutor_profile_bio);
		update_user_meta($user_id, '_tutor_profile_photo', $_tutor_profile_image);
	}

	public function set_user_role($user_id, $role, $old_roles ){
		$instructor_role = tutor()->instructor_role;

		if (in_array($instructor_role, $old_roles)){
			// tutor_utils()->remove_instructor_role($user_id);
		}

		// if ($role === $instructor_role){
		if ($role === $instructor_role || in_array($instructor_role, $old_roles)){
			tutor_utils()->add_instructor_role($user_id);
		}
	}

	public function hide_notices() {
		if(is_admin() && isset( $_GET['tutor-hide-notice'] ) && $_GET['tutor-hide-notice']=='registration') {
			tutils()->checking_nonce('get');

			if(isset($_GET['tutor-registration']) && $_GET['tutor-registration']==='enable') {
				update_option( 'users_can_register', 1 );
			} else {
				self::$hide_registration_notice = true;
				setcookie('tutor_notice_hide_registration', 1, time() + (86400 * 30), tutor()->basepath);
			}
		}
	}

	public function show_registration_disabled() {

		if( 
				self::$hide_registration_notice ||
				!tutils()->is_tutor_dashboard() || 
				get_option( 'users_can_register' ) || 
				isset( $_COOKIE['tutor_notice_hide_registration'] ) ||
				!current_user_can( 'manage_options' )
			) {
			return;
		}

		$hide_url = wp_nonce_url( add_query_arg( 'tutor-hide-notice', 'registration' ), tutor()->nonce_action, tutor()->nonce );
		?>
		<div class="wrap tutor-user-registration-notice-wrapper">
			<div class="tutor-user-registration-notice">
				<div>
					<img src="<?php echo tutor()->url; ?>assets/images/icon-info-round.svg"/>
				</div>
				<div>
					<?php _e('As membership is turned off, students and instructors will not be able to sign up. <strong>Press Enable</strong> or go to <strong>Settings > General > Membership</strong> and enable "Anyone can register".'); ?>
				</div>
				<div>
					<a href="<?php echo esc_url( add_query_arg( 'tutor-registration', 'enable', $hide_url ) ); ?>"><?php _e('Enable', 'tutor'); ?></a>
					<hr/>
					<a href="<?php echo esc_url( $hide_url ); ?>"><?php _e('Dismiss', 'tutor'); ?></a>
				</div>
			</div>
		</div>
		<?php
	}
}