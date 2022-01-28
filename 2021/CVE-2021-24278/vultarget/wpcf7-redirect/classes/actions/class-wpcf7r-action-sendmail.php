<?php
/**
 * Class WPCF7R_Action_SendMail file - handles send mail action
 */

defined( 'ABSPATH' ) || exit;

register_wpcf7r_actions(
	'SendMail',
	__( 'Send Email', 'wpcf7-redirect' ),
	'WPCF7R_Action_SendMail',
	4
);

class WPCF7R_Action_SendMail extends WPCF7R_Action {
	public function __construct( $post ) {
		parent::__construct( $post );
	}
	/**
	 * Get the fields relevant for this action
	 */
	public function get_action_fields() {
		$parent_fields = parent::get_default_fields();

		unset( $parent_fields['action_status'] );

		$fields = array_merge(
			array(
				'send_to_emails_values' => array(
					'name'        => 'send_to_emails_values',
					'type'        => 'text',
					'label'       => __( 'To:', 'wpcf7-redirect' ),
					'placeholder' => __( 'Email addresses seperated by comma (,)', 'wpcf7-redirect' ),
					'value'       => $this->get( 'send_to_emails_values' ),
				),
				'email_sender'          => array(
					'name'        => 'email_sender',
					'type'        => 'text',
					'label'       => __( 'From:', 'wpcf7-redirect' ),
					'placeholder' => __( 'name <address>', 'wpcf7-redirect' ),
					'value'       => $this->get( 'email_sender' ),
				),
				'email_subject'         => array(
					'name'  => 'email_subject',
					'type'  => 'text',
					'label' => __( 'Subject:', 'wpcf7-redirect' ),
					'value' => $this->get( 'email_subject' ),
				),
				'email_format'          => array(
					'name'   => 'email_format',
					'type'   => 'editor',
					'label'  => __( 'Email Format (use the same structure used on Contact Form 7)', 'wpcf7-redirect' ),
					'footer' => $this->get_formatted_mail_tags(),
					'value'  => $this->get( 'email_format' ),
				),
				'email_attachments'     => array(
					'name'  => 'email_attachments',
					'type'  => 'text',
					'label' => __( 'Add your form attachments shortcodes here', 'wpcf7-redirect' ),
					'value' => $this->get( 'email_attachments' ),
				),
				'plain_text'            => array(
					'name'          => 'plain_text',
					'type'          => 'checkbox',
					'label'         => __( 'Use plain text email', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'show_selector' => '.field-wrap-plain_text',
					'value'         => $this->get( 'plain_text' ),
				),
				// 'disable_cf7_mail'      => array(
				// 	'name'        => 'disable_cf7_mail',
				// 	'type'        => 'checkbox',
				// 	'label'       => __( 'Disable contact form 7 default email', 'wpcf7-redirect' ),
				// 	'placeholder' => '',
				// 	'value'       => $this->get( 'disable_cf7_mail' ),
				// ),
				'action_status'         => array(
					'name'          => 'action_status',
					'type'          => 'checkbox',
					'label'         => $this->get_action_status_label(),
					'sub_title'     => __( 'if this is off the rule will not be applied', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'show_selector' => '.field-wrap-disable_default_email',
					'toggle-label'  => json_encode(
						array(
							'.field-wrap-action_status .checkbox-label,.column-status a' => array(
								__( 'Enabled', 'wpcf7-redirect' ),
								__( 'Disabled', 'wpcf7-redirect' ),
							),
						)
					),
					'value'         => $this->get( 'action_status' ),
				),
				'disable_default_email' => array(
					'name'          => 'disable_default_email',
					'type'          => 'notice',
					'label'         => '<strong>' . __( 'Notice!', 'wpcf7-redirect' ) . '</strong>',
					'sub_title'     => __( 'When email action is enabled the default Contact Form 7 mailing feature will be disabled.', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'class'         => $this->get( 'action_status' ) ? 'field-notice-alert' : 'field-hidden field-notice-alert',
					'show_selector' => '',
				),
			),
			$parent_fields
		);
		return $fields;
	}

	/**
	 * Get settings page
	 */
	public function get_action_settings() {
		$this->get_settings_template( 'html-action-send-to-email.php' );
	}

	/**
	 * Handle a simple redirect rule
	 *
	 * @param  $rules
	 * @param  $response
	 */
	public function process( $submission ) {
		$response              = array();
		$email_to              = $this->get( 'send_to_emails_values' );
		$email_sender          = $this->get( 'email_sender' );
		$email_format          = $this->get( 'email_format' );
		$email_subject         = $this->get( 'email_subject' );
		$email_attachments     = $this->get( 'email_attachments' );
		$plain_text            = $this->get( 'plain_text' );

		// set the email address to recipient
		$mail_settings = $this->cf7r_form->cf7_post->get_properties( 'mail' );

		if ( $email_to ) {
			$mail_settings['mail']['recipient'] = $email_to;
		}

		if ( $email_sender ) {
			$mail_settings['mail']['sender'] = $email_sender;
		}

		if ( $email_format ) {
			$mail_settings['mail']['body'] = $email_format;
		}

		if ( $email_subject ) {
			$mail_settings['mail']['subject'] = $email_subject;
		}

		if ( $email_attachments ) {
			$mail_settings['mail']['attachments'] = $email_attachments;
		}

		$mail_settings['mail']['use_html'] = ! $plain_text;

		$result = $this->send_mail( $mail_settings['mail'] );

		return $result;
	}

	/**
	 * Maybe perform actions before sending results to the user
	 */
	public function maybe_perform_pre_result_action() {
		if ( $this->get( 'disable_cf7_mail' ) ) {
			add_filter( 'wpcf7_skip_mail', '__return_false' );
		}
	}


	/**
	 * Use contact form 7 function to send the email
	 *
	 * @param $mail
	 */
	public function send_mail( $mail ) {
		$result = WPCF7_Mail::send( $mail, 'mail' );
		return $result;
	}
}
