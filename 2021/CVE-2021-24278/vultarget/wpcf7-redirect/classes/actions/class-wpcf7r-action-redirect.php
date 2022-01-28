<?php
/**
 * Class WPCF7R_Action_Redirect file.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_wpcf7r_actions(
	'redirect',
	__( 'Redirect', 'wpcf7-redirect' ),
	'WPCF7R_Action_Redirect',
	1
);

/**
 * Class WPCF7R_Action_Redirect
 * A Class that handles redirect actions
 */
class WPCF7R_Action_Redirect extends WPCF7R_Action {

	/**
	 * Init the parent action class
	 *
	 * @param $post
	 */
	public function __construct( $post ) {
		parent::__construct( $post );

	}

	/**
	 * Get the action admin fields
	 */
	public function get_action_fields() {

		$parent_fields = parent::get_default_fields();

		unset( $parent_fields['action_status'] );

		return array_merge(
			array(
				array(
					'name'          => 'use_external_url',
					'type'          => 'checkbox',
					'label'         => __( 'Use custom URL', 'wpcf7-redirect' ),
					'sub_title'     => '',
					'placeholder'   => '',
					'show_selector' => '.field-wrap-external_url,.field-wrap-page_id',
					'value'         => $this->get( 'use_external_url' ),
				),
				array(
					'name'        => 'page_id',
					'type'        => 'page_select',
					'label'       => __( 'Select a page', 'wpcf7-redirect' ),
					'placeholder' => __( 'Select a page', 'wpcf7-redirect' ),
					'value'       => $this->get( 'page_id' ),
					'class'       => $this->get( 'use_external_url' ) ? 'field-hidden' : '',
				),
				array(
					'name'        => 'external_url',
					'type'        => 'url',
					'label'       => __( 'Use external URL', 'wpcf7-redirect' ),
					'placeholder' => __( 'Use external URL', 'wpcf7-redirect' ),
					'tooltip'     => __( 'You can build a custom url like this https://example.com/[your-name]?[your-email]&[your-tel]', 'wpcf7-redirect' ),
					'footer'      => '<div>' . $this->get_formatted_mail_tags() . '</div><div class="wpcf7-redirect-butify-wrap"></div>',
					'value'       => $this->get( 'external_url' ),
					'class'       => $this->get( 'use_external_url' ) ? '' : 'field-hidden',
				),
				array(
					'name'        => 'redirect_as_x_form_encoded_url',
					'type'        => 'checkbox',
					'label'       => __( 'Redirect as X-WWW-FORM-URLENCODED (Form Post)', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'redirect_as_x_form_encoded_url' ),
				),
				array(
					'name'          => 'open_in_new_tab',
					'type'          => 'checkbox',
					'label'         => __( 'Open page in a new tab', 'wpcf7-redirect' ),
					'sub_title'     => '',
					'placeholder'   => '',
					'show_selector' => '.field-wrap-open_in_new_tab_notice',
					'value'         => $this->get( 'open_in_new_tab' ),
				),
				array(
					'name'          => 'open_in_new_tab_notice',
					'type'          => 'notice',
					'label'         => '<strong>' . __( 'Notice!', 'wpcf7-redirect' ) . '</strong>',
					'sub_title'     => __( 'This option might not work as expected, since browsers often block popup windows. This option depends on the browser settings.', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'class'         => $this->get( 'open_in_new_tab' ) ? 'field-notice-alert' : 'field-hidden field-notice-alert',
					'show_selector' => '',
				),
				array(
					'name'          => 'http_build_query',
					'type'          => 'checkbox',
					'label'         => __( 'Pass all the fields from the form as URL query parameters', 'wpcf7-redirect' ),
					'sub_title'     => '',
					'placeholder'   => '',
					'show_selector' => '.field-wrap-get_param_shortcode',
					'value'         => $this->get( 'http_build_query' ),
				),
				array(
					'name'          => 'http_build_query_selectively',
					'type'          => 'checkbox',
					'label'         => __( 'Pass specific fields from the form as URL query parameters', 'wpcf7-redirect' ),
					'sub_title'     => '',
					'placeholder'   => '',
					'show_selector' => '.field-wrap-http_build_query_selectively_fields,.field-wrap-get_param_shortcode',
					'value'         => $this->get( 'http_build_query_selectively' ),
				),
				array(
					'name'        => 'url_encode',
					'type'        => 'checkbox',
					'label'       => __( 'Encode passed query parameters', 'wpcf7-redirect' ),
					'sub_title'   => '',
					'placeholder' => '',
					'value'       => $this->get( 'url_encode' ),
				),
				array(
					'name'        => 'http_build_query_selectively_fields',
					'type'        => 'text',
					'label'       => __( 'Fields to pass as URL query parameters, separated by commas:', 'wpcf7-redirect' ),
					'footer'      => '<div>' . $this->get_formatted_mail_tags( true ) . '</div><div class="wpcf7-redirect-butify-wrap"></div>',
					'placeholder' => '',
					'value'       => $this->get( 'http_build_query_selectively_fields' ),
					'class'       => $this->get( 'http_build_query_selectively' ) ? '' : 'field-hidden',
				),
				array(
					'name'          => 'get_param_shortcode',
					'type'          => 'notice',
					'label'         => '<strong>' . __( '[get_param]!', 'wpcf7-redirect' ) . '</strong>',
					'sub_title'     => __( 'You can use this tag on the target page to collect the data transerred [get_param param="your-name"].', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'class'         => $this->get( 'http_build_query_selectively' ) ? 'field-notice-alert' : 'field-hidden field-notice-alert',
					'show_selector' => '',
				),
				array(
					'name'        => 'delay_redirect_seconds',
					'type'        => 'number',
					'label'       => __( 'How many seconds to delay', 'wpcf7-redirect' ),
					'placeholder' => __( 'Number of seconds', 'wpcf7-redirect' ),
					'value'       => $this->get( 'delay_redirect_seconds' ),
					'class'       => '',
				),
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
					'label'         => __( '<strong>NOTICE!</strong><br/>', 'wpcf7-redirect' ),
					'sub_title'     => __( 'Redirection will always be the last action regardless of the actions order.', 'wpcf7-redirect' ),
					'placeholder'   => '',
					'class'         => $this->get( 'action_status' ) ? 'field-notice-alert' : 'field-hidden field-notice-alert',
					'show_selector' => '',
				),
			),
			$parent_fields
		);
	}

	/**
	 * Get an HTML of the
	 */
	public function get_action_settings() {
		$this->get_settings_template( 'html-action-redirect.php' );
	}

	/**
	 * Handle a simple redirect rule
	 *
	 * @param $submission
	 */
	public function process( $submission ) {
		$response = array();

		$this->posted_data = $submission->get_posted_data();

		foreach ( $this->posted_data as $posted_data_key => $posted_data_value ) {
			if ( is_array( $posted_data_value ) ) {
				$this->posted_data[ $posted_data_key ] = implode( ',', $posted_data_value );
			} elseif ( $this->get( 'url_encode' ) ) {
				$this->posted_data[ $posted_data_key ] = rawurlencode( $posted_data_value );
			}
		}

		if ( $this->get( 'use_external_url' ) === 'on' && $this->get( 'external_url' ) ) {
			$response = array(
				'type'         => 'redirect',
				'redirect_url' => $this->replace_tags( $this->get( 'external_url' ) ),
				'delay'        => $this->get( 'delay_redirect_seconds' ) ? (int) $this->get( 'delay_redirect_seconds' ) : 0,
			);
		} elseif ( $this->get( 'page_id' ) ) {
			$response = array(
				'type'         => 'redirect',
				'redirect_url' => get_permalink( $this->get( 'page_id' ) ),
				'delay'        => $this->get( 'delay_redirect_seconds' ) ? (int) $this->get( 'delay_redirect_seconds' ) : 0,
			);
		}

		if ( $this->get( 'redirect_as_x_form_encoded_url' ) && isset( $response['redirect_url'] ) && $response['redirect_url'] ) {

			ob_start();

			$params = parse_url( $response['redirect_url'] );

			parse_str( $params['query'], $fields );

			if ( isset( $fields['sum'] ) && isset( $fields['qty'] ) ) {
				$fields['sum'] = $fields['sum'] * $fields['qty'];
			}

			if ( $this->get( 'http_build_query_selectively' ) === 'on' ) {
				$fields_to_add = explode( ',', $this->get( 'http_build_query_selectively_fields' ) );

				foreach ( $fields_to_add as $field_to_add ) {
					$field_to_add            = trim( $field_to_add );
					$fields[ $field_to_add ] = isset( $this->posted_data[ $field_to_add ] ) ? $this->posted_data[ $field_to_add ] : '';
				}
			} elseif ( $this->get( 'http_build_query' ) === 'on' ) {
				$fields = array_merge( $this->posted_data, $fields );
			}

			?>
			<form id="cf7r-result-form" action="<?php echo $response['redirect_url']; ?>" method="post">
				<?php foreach ( $fields as $field_key => $field_value ) : ?>
					<input type="hidden" name="<?php echo $field_key; ?>" value="<?php echo $field_value; ?>">
				<?php endforeach; ?>
			</form>
			<?php
			$response['form'] = ob_get_clean();

		} elseif ( $this->get( 'http_build_query' ) === 'on' ) {

			$response['http_build_query'] = true;

			$response['redirect_url'] = add_query_arg( $this->posted_data, $response['redirect_url'] );

		} elseif ( $this->get( 'http_build_query_selectively' ) === 'on' ) {

			$fields_to_add      = explode( ',', $this->get( 'http_build_query_selectively_fields' ) );
			$fields_to_add_args = array();

			foreach ( $fields_to_add as $field_to_add ) {
				$field_to_add                        = trim( $field_to_add );
				$fields_to_add_args[ $field_to_add ] = isset( $this->posted_data[ $field_to_add ] ) ? $this->posted_data[ $field_to_add ] : '';
			}

			$response['redirect_url'] = add_query_arg( $fields_to_add_args, $response['redirect_url'] );
		}

		if ( $this->get( 'after_sent_script' ) ) {
			$response['after_sent_script'] = $this->get( 'after_sent_script' );
		}

		if ( $this->get( 'open_in_new_tab' ) === 'on' ) {
			$response['type'] = 'new_tab';
		}

		return $response;
	}
}
