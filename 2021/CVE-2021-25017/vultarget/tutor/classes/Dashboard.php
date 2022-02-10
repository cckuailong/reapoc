<?php
/**
 * Dashboard class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.3.4
 */

namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Dashboard {

	public function __construct() {
		add_action('tutor_load_template_before', array($this, 'tutor_load_template_before'), 10, 2);
		add_action('tutor_load_template_after', array($this, 'tutor_load_template_after'), 10, 2);
		add_filter('should_tutor_load_template', array($this, 'should_tutor_load_template'), 10, 2);

		add_action('tutor_dashboard/notification_area', array($this, 'profile_completion_notification'), 10, 2);
	}

	/**
	 * @param $template
	 * @param $variables
	 */
	public function tutor_load_template_before($template, $variables){
		global $wp_query;

		$tutor_dashboard_page = tutor_utils()->array_get('query_vars.tutor_dashboard_page', $wp_query);
		if ($tutor_dashboard_page === 'create-course') {
			global $post;
			wp_reset_query();


			/**
			 * Get course which currently in edit, or insert new course
			 */
			$course_ID = (int) sanitize_text_field(tutor_utils()->array_get('course_ID', $_GET));

			if ($course_ID){
				$post_id = $course_ID;
			}else{
				$post_type = tutor()->course_post_type;
				$post_id = wp_insert_post( array( 'post_title' => __( 'Auto Draft', 'tutor' ), 'post_type' => $post_type, 'post_status' => 'draft' ) );
			}

			$post = get_post( $post_id );
			setup_postdata( $post );
		}
	}

	public function tutor_load_template_after(){
		global $wp_query;

		$tutor_dashboard_page = tutor_utils()->array_get('query_vars.tutor_dashboard_page', $wp_query);
		if ($tutor_dashboard_page === 'create-course'){
			wp_reset_query();
		}
	}

	public function should_tutor_load_template($bool, $template){
		if ($template === 'dashboard.create-course' && ! tutor()->has_pro){
			return false;
		}
		return $bool;
	}


	/**
	 * Display completion notification
	 * 
	 * @return string
	 *
	 * @since v.1.6.6
	 */
	public function profile_completion_notification() {
        $output = '';
        $enable_profile_completion = tutils()->get_option('enable_profile_completion');
        if ($enable_profile_completion) {
            ob_start();
            tutor_load_template('dashboard.notifications.profile-completion');
            $output = apply_filters('tutor_profile_completion_notification_html', ob_get_clean());
        }

        echo $output;
    }
}