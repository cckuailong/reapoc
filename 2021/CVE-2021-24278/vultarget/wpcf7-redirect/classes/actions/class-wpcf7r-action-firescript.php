<?php
/**
 * Class WPCF7R_Action_FireScript file - handles JavaScript actions
 */

defined( 'ABSPATH' ) || exit;

register_wpcf7r_actions(
	'FireScript',
	__( 'Fire JavaScript', 'wpcf7-redirect' ),
	'WPCF7R_Action_FireScript',
	2
);

class WPCF7R_Action_FireScript extends WPCF7R_Action {

	public function __construct( $post ) {
		parent::__construct( $post );

	}

	/**
	 * Get the fields relevant for this action
	 */
	public function get_action_fields() {
		return array_merge(
			array(
				'script'           => array(
					'name'        => 'script',
					'type'        => 'textarea',
					'label'       => __( 'Paste your JavaScript here.', 'wpcf7-redirect' ),
					'sub_title'   => esc_html( __( '(Don\'t use <script> tags)', 'wpcf7-redirect' ) ),
					'placeholder' => __( 'Paste your JavaScript here', 'wpcf7-redirect' ),
					'value'       => $this->get( 'script' ),
				),
				'short-tags-usage' => array(
					'name'          => 'general-alert',
					'type'          => 'notice',
					'label'         => __( 'Notice!', 'wpcf7-redirect' ),
					/* Translators: available mail tags */
					'sub_title'     => sprintf( __( 'You can use the following tags.<div>%s</div>', 'wpcf7-redirect' ), $this->get_formatted_mail_tags() ),
					'placeholder'   => '',
					'class'         => 'field-notice-alert',
					'show_selector' => '',
				),
				'general-alert'    => array(
					'name'          => 'general-alert',
					'type'          => 'notice',
					'label'         => __( 'Warning!', 'wpcf7-redirect' ),
					'sub_title'     => __(
						'This option is for developers only - use with caution. If the plugin does not redirect after you have added scripts,
                    it means you have a problem with your script. Either fix the script, or remove it.',
						'wpcf7-redirect'
					),
					'placeholder'   => '',
					'class'         => 'field-warning-alert',
					'show_selector' => '',
				),
			),
			parent::get_default_fields()
		);
	}

	/**
	 * Get settings page
	 */
	public function get_action_settings() {
		$this->get_settings_template( 'html-action-send-to-email.php' );
	}

	/**
	 * Handle a simple redirect rule
	 */
	public function process( $submission ) {

		$script = $this->get( 'script' );

		$script = $this->replace_tags( $script, array() );

		return $script;
	}

}
