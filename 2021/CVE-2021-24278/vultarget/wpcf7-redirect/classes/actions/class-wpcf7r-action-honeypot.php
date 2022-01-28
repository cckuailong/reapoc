<?php
/**
 * Class WPCF7R_Action_Honeypot
 * A Class that handles javascript actions
 */

defined( 'ABSPATH' ) || exit;

register_wpcf7r_actions(
	'honeypot',
	__( 'Honeypot', 'wpcf7-redirect' ),
	'WPCF7R_Action_Honeypot',
	5
);

class WPCF7R_Action_Honeypot extends WPCF7R_Action {
	public function __construct( $post ) {
		parent::__construct( $post );
	}

	/**
	 * Get the fields relevant for this action
	 */
	public function get_action_fields() {
		return array_merge(
			array(
				'general-alert' => array(
					'name'          => 'general-alert',
					'type'          => 'notice',
					'label'         => __( 'Notice!', 'wpcf7-redirect' ),
					'sub_title'     => __(
						'Honeypot creates a random number of anti-spam fields on your form. If a robot fills out one of these fields the submission is marked as spam.<br/>
                        <a href="https://en.wikipedia.org/wiki/Honeypot_(computing)" target="_blank">Learn More</a>',
						'wpcf7-redirect'
					),
					'placeholder'   => '',
					'class'         => 'field-warning-notice',
					'show_selector' => '',
				),
			),
			parent::get_default_fields()
		);
	}

	/**
	 * Create the honeypot field names
	 *
	 * @param integer $length
	 */
	function readable_random_string( $length = 6 ) {
		$string     = '';
		$vowels     = array( 'a', 'e', 'i', 'o', 'u' );
		$consonants = array(
			'b',
			'c',
			'd',
			'f',
			'g',
			'h',
			'j',
			'k',
			'l',
			'm',
			'n',
			'p',
			'r',
			's',
			't',
			'v',
			'w',
			'x',
			'y',
			'z',
		);
		$max        = $length / 2;

		for ( $i = 1; $i <= $max; $i++ ) {
			$string .= $consonants[ rand( 0, 19 ) ];
			$string .= $vowels[ rand( 0, 4 ) ];
		}

		return $string;
	}

	/**
	 * Get the honeypot fields names
	 */
	private function get_honeypot_names() {
		return get_post_meta( $this->get_id(), 'honeypot_names', true );
	}

	/**
	 * Render callback
	 */
	public function render_callback_once( $properties ) {
		add_action(
			'wp_footer',
			function() {
				$honeypot_names = $this->get_honeypot_names();

				foreach ( $honeypot_names as $honeypot_name ) {
					echo "<style>.{$honeypot_name}{transform:scale(0);position: absolute;z-index: -10;}</style>";
				}
			}
		);

		return $properties;
	}

	/**
	 * Render an element on the form frontend.
	 *
	 * @param array $properties
	 * @param object $form
	 */
	public function render_callback( $properties, $form ) {
		$honeypot_names = $this->get_honeypot_names();

		if ( ! $honeypot_names ) {
			$rand           = rand( 1, 4 );
			$honeypot_names = array();

			for ( $i = 1;$i <= $rand;$i++ ) {
				$honeypot_names[] = $this->readable_random_string();
			}

			update_post_meta( $this->get_id(), 'honeypot_names', $honeypot_names );
		}

		if ( isset( $properties['form'] ) ) {
			foreach ( $honeypot_names as $honeypot_name ) {
				$properties['form'] .= "<p class='{$honeypot_name}'>[text {$honeypot_name}]</p>";
			}
		}

		return $properties;
	}

	/**
	 * Process the honeypot validations
	 */
	public function process_validation( $submission ) {
		$response       = array();
		$posted_data    = $submission->get_posted_data();
		$honeypot_names = $this->get_honeypot_names();

		if ( $honeypot_names ) {
			foreach ( $honeypot_names as $honeypot_name ) {
				if ( isset( $posted_data[ $honeypot_name ] ) && $posted_data[ $honeypot_name ] ) {
					$error                      = array(
						'tag'           => $honeypot_name,
						'error_message' => __( 'Something went wrong', 'wpcf7-redirect' ),
					);
					$response['invalid_tags'][] = new WP_error( 'tag_invalid', $error );
				} else {
					$response['ignored_tags'][] = $honeypot_name;
				}
			}
		}

		return $response;
	}
}
