<?php
/**
 * Created by PhpStorm.
 * User: themeum
 * Date: 30/9/19
 * Time: 3:20 PM
 */

namespace TUTOR;


class Email {

	public function __construct() {
		add_filter('tutor/options/attr', array($this, 'add_options'), 10); // Priority index is important. Content Drip uses 11.

		if ( ! function_exists('tutor_pro')) {
			add_action( 'tutor_options_before_email_notification', array( $this, 'no_pro_message' ) );
		}
	}

	public function add_options($attr){
		$attr['email_notification'] = array(
			'label'     => __('E-Mail Notification', 'tutor'),
			'sections'    => array(
				'email_settings' => array(
					'label' => __('E-Mail Settings', 'tutor'),
					'desc' => __('Check and place necessary information here.', 'tutor'),
					'fields' => array(
						'email_from_name' => array(
							'type'      => 'text',
							'label'     => __('Name', 'tutor'),
							'default'   => get_option('blogname'),
							'desc'      => __('The name under which all the emails will be sent',	'tutor'),
						),
						'email_from_address' => array(
							'type'      => 'text',
							'label'     => __('E-Mail Address', 'tutor'),
							'default'   => get_option('admin_email'),
							'desc'      => __('The E-Mail address from which all emails will be sent', 'tutor'),
						),
						'email_footer_text' => array(
							'type'      => 'textarea',
							'label'     => __('E-Mail Footer Text', 'tutor'),
							'default'   => '',
							'desc'      => __('The text to appear in E-Mail template footer', 'tutor'),
						),
					),
				),

			),
		);


		return $attr;
	}


	public function no_pro_message(){
		tutor_alert(sprintf(__(' %s Get Tutor LMS Pro %s to extend email functionality and send email notifications for certain events. You can easily choose the events for which you wish to send emails.', 'tutor'), "<strong> <a href='https://www.themeum.com/product/tutor-lms/?utm_source=tutor_lms_email_settings' target='_blank'>", "</a></strong>"  ) );

	}

}