<?php

namespace TUTOR;


if ( ! defined( 'ABSPATH' ) )
	exit;

class Upgrader {

	public function __construct() {
		add_action('admin_init', array($this, 'init_upgrader'));

		$base_name = tutor()->basename;
		add_action( 'in_plugin_update_message-'.$base_name, array( $this, 'in_plugin_update_message' ), 10, 2 );

		/**
		 * Installing Gradebook Addon from TutorPro
		 *
		 */
		add_action('tutor_addon_before_enable_tutor-pro/addons/gradebook/gradebook.php', array($this, 'install_gradebook'));
		add_action('tutor_addon_before_enable_tutor-pro/addons/tutor-email/tutor-email.php', array($this, 'install_tutor_email_queue'));
		add_action('upgrader_process_complete', array($this, 'init_email_table_deployment'), 10, 2);
	}

	public function init_upgrader(){
		$upgrades = $this->available_upgrades();

		if (tutor_utils()->count($upgrades)){
			foreach ($upgrades as $upgrade){
				$this->{$upgrade}();
			}
		}
	}

	public function available_upgrades(){
		$version = get_option('tutor_version');

		$upgrades = array();
		if ($version){
			$upgrades[] = 'upgrade_to_1_3_1';
		}

		return $upgrades;
	}

	/**
	 * Upgrade to version 1.3.1
	 */
	public function upgrade_to_1_3_1(){
		if (version_compare(get_option('tutor_version'), '1.3.1', '<')) {
			global $wpdb;

			if ( ! get_option('is_course_post_type_updated')){
				$wpdb->update($wpdb->posts, array('post_type' => 'courses'), array('post_type' => 'course'));
				update_option('is_course_post_type_updated', true);
				update_option('tutor_version', '1.3.1');
				flush_rewrite_rules();
			}
		}
	}


	public function in_plugin_update_message( $args, $response ){
		$upgrade_notice = strip_tags(tutils()->array_get('upgrade_notice', $response));
		if ($upgrade_notice){
			$upgrade_notice = "<span class='version'><code>v.{$response->new_version}</code></span> <br />".$upgrade_notice;

			echo apply_filters( 'tutor_in_plugin_update_message', $upgrade_notice ? '</p> <div class="tutor_plugin_update_notice">' .$upgrade_notice. '</div> <p class="dummy">' : '' );
		}
	}


	/**
	 * Installing Gradebook if Tutor Pro exists
	 *
	 * @since v.1.4.2
	 */
	public function install_gradebook(){
		global $wpdb;

		$exists_gradebook_table = $wpdb->query("SHOW TABLES LIKE '{$wpdb->tutor_gradebooks}';");
		$exists_gradebook_results_table = $wpdb->query("SHOW TABLES LIKE '{$wpdb->tutor_gradebooks_results}';");
		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		if ( ! $exists_gradebook_table){
			$gradebook_table = "CREATE TABLE IF NOT EXISTS {$wpdb->tutor_gradebooks} (
				gradebook_id int(11) NOT NULL AUTO_INCREMENT,
				grade_name varchar(50) DEFAULT NULL,
				grade_point varchar(20) DEFAULT NULL,
				grade_point_to varchar(20) DEFAULT NULL,
				percent_from int(3) DEFAULT NULL,
				percent_to int(3) DEFAULT NULL,
				grade_config longtext,
				PRIMARY KEY (gradebook_id)
			) $charset_collate;";
			dbDelta( $gradebook_table );
		}
		if ( ! $exists_gradebook_results_table){
			$gradebook_results = "CREATE TABLE IF NOT EXISTS {$wpdb->tutor_gradebooks_results} (
				gradebook_result_id int(11) NOT NULL AUTO_INCREMENT,
				user_id int(11) DEFAULT NULL,
				course_id int(11) DEFAULT NULL,
				quiz_id int(11) DEFAULT NULL,
				assignment_id int(11) DEFAULT NULL,
				gradebook_id int(11) DEFAULT NULL,
				result_for varchar(50) DEFAULT NULL,
				grade_name varchar(50) DEFAULT NULL,
				grade_point varchar(20) DEFAULT NULL,
				earned_grade_point varchar(20) DEFAULT NULL,
				earned_percent int(3) DEFAULT NULL,
				generate_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				update_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				PRIMARY KEY (gradebook_result_id)
			) {$charset_collate};";
			dbDelta( $gradebook_results );
		}

	}

	public function init_email_table_deployment($upgrader_object, $options ) {

		if( is_object( $upgrader_object ) && is_array($upgrader_object->result) && isset($upgrader_object->result['destination_name']) && $upgrader_object->result['destination_name']=='tutor-pro' ) {
			$addonConfig = tutor_utils()->get_addon_config('tutor-pro/addons/tutor-email/tutor-email.php');
			$isEnable = (bool) tutor_utils()->avalue_dot('is_enable', $addonConfig);

			$isEnable ? $this->install_tutor_email_queue() : 0;
		}
	}

	/**
	 * Installing email addon if Tutor Pro exists
	 *
	 * @since v.1.8.6
	 */
	public function install_tutor_email_queue() {

		global $wpdb;
		$exists_email_queue_table = $wpdb->query("SHOW TABLES LIKE '{$wpdb->tutor_email_queue}';");
		$charset_collate = $wpdb->get_charset_collate();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		if ( ! $exists_email_queue_table ) {
			$table = "CREATE TABLE IF NOT EXISTS {$wpdb->tutor_email_queue} (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				mail_to varchar(255) NOT NULL,
				subject text NOT NULL,
				message text NOT NULL,
				headers text NOT NULL,
				PRIMARY KEY (id)
			) {$charset_collate};";
		
			dbDelta( $table );
		}
	}
}