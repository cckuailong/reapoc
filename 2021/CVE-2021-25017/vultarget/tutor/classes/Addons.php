<?php
/**
 * Addons class
 *
 * @author: themeum
 * @author_uri: https://themeum.com
 * @package Tutor
 * @since v.1.0.0
 */


namespace TUTOR;

if ( ! defined( 'ABSPATH' ) )
	exit;

class Addons {

	public function __construct() {
		add_filter('tutor_pro_addons_lists_for_display', array($this, 'tutor_addons_lists_to_show'));
	}

	public function tutor_addons_lists_to_show(){
		$addons = array(
			'buddypress'     => array(
				'name'          => __('BuddyPress', 'tutor'),
				'description'   => 'Discuss about course and share your knowledge with your friends through BuddyPress',
			),
			'gradebook'     => array(
				'name'          => __('Gradebook', 'tutor'),
				'description'   => 'Shows student progress from assignment and quiz',
			),
			'content-drip'  => array(
				'name'          => __('Content Drip', 'tutor'),
				'description'   => 'Unlock lessons by schedule or when the student meets specific condition.',
			),
			'enrollments'       => array(
				'name'          => __('Enrollments', 'tutor'),
				'description'   => 'Take advanced control on enrollments. Enroll the student manually.',
			),
			'wc-subscriptions' => array(
				'name'          => __('WooCommerce Subscriptions', 'tutor'),
				'description'   => 'Capture Residual Revenue with Recurring Payments.',
			),
			'pmpro'             => array(
				'name'          => __('Paid Memberships Pro', 'tutor'),
				'description'   => 'Maximize revenue by selling membership access to all of your courses.',
			),
			'restrict-content-pro'  => array(
				'name'          => __('Restrict Content Pro', 'tutor'),
				'description'   => 'Unlock Course depending on Restrict Content Pro Plugin Permission.',
			),
			'tutor-assignments' => array(
				'name'          => __('Tutor Assignments', 'tutor'),
				'description'   => 'Tutor assignments is a great way to assign tasks to students.',
			),
			'tutor-certificate' => array(
				'name'          => __('Tutor Certificate', 'tutor'),
				'description'   => 'Students will be able to download a certificate after course completion.',
			),
			'tutor-course-attachments' => array(
				'name'          => __('Tutor Course Attachments', 'tutor'),
				'description'   => 'Add unlimited attachments/ private files to any Tutor course',
			),
			'tutor-course-preview' => array(
				'name'          => __('Tutor Course Preview', 'tutor'),
				'description'   => 'Unlock some lessons for students before enrollment.',
			),
			'tutor-email' => array(
				'name'          => __('Tutor E-Mail', 'tutor'),
				'description'   => 'Send email on various tutor events',
			),
			'tutor-multi-instructors' => array(
				'name'          => __('Tutor Multi Instructors', 'tutor'),
				'description'   => 'Start a course with multiple instructors by Tutor Multi Instructors',
			),
			'tutor-prerequisites' => array(
				'name'          => __('Tutor Prerequisites', 'tutor'),
				'description'   => 'Specific course you must complete before you can enroll new course by Tutor Prerequisites',
			),
			'tutor-report' => array(
				'name'          => __('Tutor Report', 'tutor'),
				'description'   => 'Check your course performance through Tutor Report stats.',
			),
			'quiz-import-export' => array(
				'name'          => __('Quiz Export/Import', 'tutor'),
				'description'   => __('Save time by exporting/importing quiz data with easy options.', 'tutor'),
			),
			'tutor-zoom' => array(
				'name'          => __('Tutor Zoom Integration', 'tutor'),
				'description'   => __('Connect Tutor LMS with Zoom to host live online classes. Students can attend live classes right from the lesson page.', 'tutor'),
			),
			'google-classroom' => array(
				'name'          => __('Google Classroom Integration', 'tutor'),
				'description'   => __('Helps connect Google Classrooms with Tutor LMS courses, allowing you to use features like Classroom streams and files directly from the Tutor LMS course.', 'tutor'),
			),
			'push-notification' => array(
				'name'			=> 'Push Notification',
				'description'	=> 'Users will get push notification on specified events.'
			),
			'tutor-wpml'		=> array(
				'name'			=> __('WPML Multilingual CMS', 'tutor'),
				'description'	=> __('Create multilingual courses, lessons, dashboard and more for a global audience.', 'tutor')
			)
		);

		return $addons;
	}


	/**
	 * @deprecated from alpha version
	 */

	public function addons_page(){
		
		if ( false === ( $addons_themes_data = get_transient( 'tutor_addons_themes_data' ) ) ) {
			//Request New
			$api_endpoint = 'https://www.themeum.com/wp-json/addon-serve/v2/get-products';
			$response = wp_remote_post( $api_endpoint, array(
					'method' => 'POST',
					'timeout' => 45,
					'user-agent' => 'Tutor/'.TUTOR_VERSION.'; '.home_url( '/' ),
					'headers' => array(
						'wp_blog' => home_url( '/' )
					),
					'body' => array('plugin_slug' => 'tutor', 'wp_blog' => home_url( '/' )),
				)
			);

			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: $error_message";
			} else {
				if (tutor_utils()->avalue_dot('body', $response) && tutor_utils()->avalue_dot('response.code', $response) == 200 ){
					$api_data = tutor_utils()->avalue_dot('body', $response);

					$addons_themes_data = array(
						'last_checked_time' => tutor_time(),
						'data' => $api_data,
					);
				}
			}

			//Save the Final api call result on the database
			set_transient( 'tutor_addons_themes_data', $addons_themes_data, 6 * HOUR_IN_SECONDS );
		}


		//Finally Show the View Page
		include tutor()->path.'views/pages/addons.php';
	}

}