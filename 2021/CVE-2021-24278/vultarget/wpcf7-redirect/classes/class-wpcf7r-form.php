<?php
/**
 * Class WPCF7R_Form - Container class that wraps the CF7 form object and adds functionality
 */

defined( 'ABSPATH' ) || exit;

class WPCF7R_Form {
	// Refrence to the mail tags
	public static $mail_tags;

	// Refrence to the current contact form 7 form
	public static $cf7_form;

	// Save refrence to the current instance
	public static $instance;
	public static $action_to_submit;

	// holds an array of items remove from $_POST for security reasons
	public static $removed_data;

	// Save proceesed actions from validation stage
	public static $processed_actions;

	// Refrence to the current submitted torm validation obj
	public static $wpcf_validation_obj;

	// Reference to the current submitted form
	public static $submission;

	/**
	 * Main class Constructor
	 *
	 * @param $cf7
	 * @param $submission
	 * @param $validation_obj
	 */
	public function __construct( $cf7, $submission = '', $validation_obj = '' ) {
		if ( is_int( $cf7 ) ) {
			$this->post_id = $cf7;
			$cf7           = WPCF7_ContactForm::get_instance( $this->post_id );
		} elseif ( $cf7 ) {
			$this->post_id = $cf7->id();
		} else {
			return;
		}

		// Keep refrences.
		if ( $cf7 ) {
			self::$cf7_form = $cf7;
		}
		if ( $validation_obj ) {
			self::$wpcf_validation_obj = $validation_obj;
		}
		if ( $submission ) {
			self::$submission = $submission;
		}

		$this->redirect_actions = new WPCF7R_Actions( $this->post_id, $this );
		$this->cf7_post         = $cf7;

		// Avoid creating 2 instances of the same form.
		if ( self::$instance && self::$instance->post_id === $this->post_id ) {
			return self::$instance;
		}

		add_action( 'admin_footer', array( $this->redirect_actions, 'html_fregments' ) );
	}

	/**
	 * Get submission refrerence
	 */
	public function get_submission() {
		return self::$submission;
	}

	/**
	 * Get the form submission status
	 */
	public function get_submission_status() {
		return self::get_submission()->get_status();
	}

	/**
	 * Disable all form actions except the requested one
	 *
	 * @param $action_id
	 */
	public function enable_action( $action_id ) {
		self::$action_to_submit = $action_id;
	}

	/**
	 * In case a specific action was required (used for testing)
	 */
	public function get_action_to_submit() {
		return self::$action_to_submit;
	}

	/**
	 * Get an instance of wpcf7 object
	 */
	public function get_cf7_form_instance() {
		return $this->cf7_post;
	}

	/**
	 * Get old redirection plugin rules
	 */
	public function get_cf7_redirection_settings() {
		$custom_data  = get_post_custom( $this->post_id );
		$old_settings = array();
		if ( isset( $custom_data['_wpcf7_redirect_redirect_type'] ) ) {
			$old_settings['_wpcf7_redirect_redirect_type'] = maybe_unserialize( $custom_data['_wpcf7_redirect_redirect_type'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_page_id'] ) ) {
			$old_settings['page_id'] = maybe_unserialize( $custom_data['_wpcf7_redirect_page_id'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_external_url'] ) ) {
			$old_settings['external_url'] = maybe_unserialize( $custom_data['_wpcf7_redirect_external_url'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_use_external_url'] ) ) {
			$old_settings['use_external_url'] = maybe_unserialize( $custom_data['_wpcf7_redirect_use_external_url'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_open_in_new_tab'] ) ) {
			$old_settings['open_in_new_tab'] = maybe_unserialize( $custom_data['_wpcf7_redirect_open_in_new_tab'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_http_build_query'] ) ) {
			$old_settings['http_build_query'] = maybe_unserialize( $custom_data['_wpcf7_redirect_http_build_query'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_http_build_query_selectively'] ) ) {
			$old_settings['http_build_query_selectively'] = maybe_unserialize( $custom_data['_wpcf7_redirect_http_build_query_selectively'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_http_build_query_selectively_fields'] ) ) {
			$old_settings['http_build_query_selectively_fields'] = maybe_unserialize( $custom_data['_wpcf7_redirect_http_build_query_selectively_fields'][0] );
		}
		if ( isset( $custom_data['_wpcf7_redirect_after_sent_script'] ) ) {
			$old_settings['fire_sctipt'] = maybe_unserialize( $custom_data['_wpcf7_redirect_after_sent_script'][0] );
		}

		if ( isset( $custom_data['_wpcf7_redirect_delay_redirect'] ) && (int) $custom_data['_wpcf7_redirect_delay_redirect'] > 0 ) {
			$old_settings['delay_redirect_seconds'] = $custom_data['_wpcf7_redirect_delay_redirect'][0] / 1000;
		}

		return $old_settings;
	}

	/**
	 * Get the old contact form 7 to api settings
	 */
	public function get_cf7_api_settings() {
		$custom_data  = get_post_custom( $this->post_id );
		$old_settings = array();
		if ( isset( $custom_data['_wpcf7_api_data'] ) ) {
			$old_settings['_wpcf7_api_data'] = maybe_unserialize( $custom_data['_wpcf7_api_data'][0] );
		}
		if ( isset( $custom_data['_wpcf7_api_data_map'] ) ) {
			$old_settings['_wpcf7_api_data_map'] = maybe_unserialize( $custom_data['_wpcf7_api_data_map'][0] );
		}
		if ( isset( $custom_data['_template'] ) ) {
			$old_settings['_template'] = maybe_unserialize( $custom_data['_template'][0] );
		}
		if ( isset( $custom_data['_json_template'] ) ) {
			$old_settings['_json_template'] = maybe_unserialize( $custom_data['_json_template'][0] );
		}
		return $old_settings;
	}

	/**
	 * Check if a form has a specific action type
	 *
	 * @param $type
	 * @return boolean
	 */
	public function has_action( $type ) {
		$args       = array();
		$meta_query = array(
			array(
				'key'   => 'action_type',
				'value' => $type,
			),
		);

		$args['meta_query'] = $meta_query;
		$actions            = $this->get_actions( 'default', 1, true, $args );

		return $actions ? $actions : false;
	}

	/**
	 * Update the plugin has migrated
	 *
	 * @param $migration_type
	 */
	public function update_migration( $migration_type ) {
		update_post_meta( $this->post_id, $migration_type, true );
	}

	/**
	 * Check if a form was migrated from old version
	 *
	 * @param $migration_type
	 * @return boolean
	 */
	public function has_migrated( $migration_type ) {
		return get_post_meta( $this->post_id, $migration_type, true );
	}

	/**
	 * Check if there is old data on the DB
	 *
	 * @param $type
	 * @return boolean
	 */
	public function has_old_data( $type ) {
		if ( 'migrate_from_cf7_api' === $type ) {
			return $this->get_cf7_api_settings();
		} else {
			return $this->get_cf7_redirection_settings();
		}
	}

	/**
	 * Get a singelton
	 *
	 * @param string $post_id
	 */
	public static function get_instance( $post_id = '' ) {
		if ( null === self::$instance || ( self::$cf7_form->id() !== $post_id && $post_id ) ) {
			self::$instance = new self( $post_id );
		}
		return self::$instance;
	}

	public function is_new_form() {
		return isset( $this->post_id ) && $this->post_id ? false : true;
	}

	/**
	 * Initialize form
	 */
	public function init() {
		// Check if this is a new form
		if ( $this->is_new_form() ) {
			self::$mail_tags = __( 'You need to save your form', 'wpcf7-redirect' );
			include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'save-form.php';
		} else {
			self::$mail_tags  = $this->get_cf7_fields();
			$fields           = $this->get_fields_values();
			$redirect_type    = isset( $fields['redirect_type'] ) && $fields['redirect_type'] ? $fields['redirect_type'] : '';
			$active_tab_title = 'active';
			$active_tab       = 'active';
			$this->html       = new WPCF7R_html( self::$mail_tags );
			include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'settings.php';
		}
	}

	/**
	 * Get all posts relevant to this contact form
	 * returns posts object
	 *
	 * @param $rule
	 */
	public function get_action_posts( $rule ) {
		return $this->redirect_actions->get_action_posts( $rule );
	}

	/**
	 * Get all actions relevant for this contact form
	 * Returns action classes
	 *
	 * @param $rule
	 * @param integer $count
	 * @param boolean $is_active
	 * @param array   $args
	 */
	public function get_actions( $rule, $count = -1, $is_active = false, $args = array() ) {
		$action_id = $this->get_action_to_submit();

		if ( $action_id ) {
			$args['post__in'] = array( $action_id );
		}

		$actions = isset( $this->redirect_actions ) && $this->redirect_actions ? $this->redirect_actions->get_actions( $rule, -1, $is_active, $args ) : array();

		$this->actions = $actions;

		return $actions;
	}

	/**
	 * Get all active actions
	 */
	public function get_active_actions() {
		return $this->get_actions( 'default', -1, true );
	}

	/**
	 * Save reference for the items removed from the $_POST data
	 *
	 * @param $removed_data
	 */
	public function set_removed_posted_data( $removed_data ) {
		if ( isset( self::$removed_data ) ) {
			self::$removed_data = array_merge( $removed_data, self::$removed_data );
		} else {
			self::$removed_data = $removed_data;
		}
	}

	/**
	 * Get all params removed from the $_POST
	 */
	public function get_removed_form_params() {
		return isset( self::$removed_data ) ? self::$removed_data : '';
	}

	/**
	 * Validate and store meta data
	 */
	public function store_meta() {
		if ( ! isset( $_POST ) || empty( $_POST['wpcf7-redirect'] ) ) {
			return;
		} else {
			if ( ! wp_verify_nonce( $_POST['wpcf7_redirect_page_metaboxes_nonce'], 'wpcf7_redirect_page_metaboxes' ) ) {
				return;
			}
			$form_id = $this->post_id;
			$fields  = $this->get_plugin_fields( $form_id );
			$data    = $_POST['wpcf7-redirect'];
			$this->save_meta_fields( $form_id, $fields, $data );
			if ( isset( $data['actions'] ) && $data['actions'] ) {
				$this->save_actions( $data['actions'] );
			}
		}
	}

	/**
	 * Save all actions and actions data
	 *
	 * @param $actions
	 */
	public function save_actions( $actions ) {
		foreach ( $actions as $post_id => $action_fields ) {
			$action = WPCF7R_Action::get_action( $post_id );
			if ( $action ) {
				$action->delete_all_fields();
				foreach ( $action_fields as $action_field_key => $action_field_value ) {
					update_post_meta( $post_id, $action_field_key, $action_field_value );
				}
				if ( isset( $action_fields['post_title'] ) ) {
					$update_post = array(
						'ID'         => $post_id,
						'post_title' => $action_fields['post_title'],
					);
					wp_update_post( $update_post );
				}
			}
		}
	}

	/**
	 * Save meta fields to cf7 post
	 * Save each action to its relevant action post
	 *
	 * @param $post_id
	 * @param $fields
	 * @param $data
	 */
	public function save_meta_fields( $post_id, $fields, $data ) {
		unset( $data['actions'] );
		if ( $data ) {
			foreach ( $fields as $field ) {
				$value = isset( $data[ $field['name'] ] ) ? $data[ $field['name'] ] : '';
				switch ( $field['type'] ) {
					case 'password':
					case 'text':
					case 'checkbox':
						$value = sanitize_text_field( $value );
						break;
					case 'textarea':
						$value = htmlspecialchars( $value );
						break;
					case 'number':
						$value = intval( $value );
						break;
					case 'url':
						$value = esc_url_raw( $value );
						break;
				}
				update_post_meta( $post_id, '_wpcf7_redirect_' . $field['name'], $value );
			}
		}
	}

	/**
	 * Check if the form has active actions
	 */
	public function has_actions() {
		$rule      = 'default';
		$count     = 1;
		$is_active = true;
		$args      = array();
		$actions   = $this->get_actions( $rule, $count, $is_active, $args ) ? true : false;

		return $actions;
	}

	/**
	 * Get specific form fields
	 *
	 * @param $fields
	 */
	public function get_form_fields( $fields ) {
		$forms = array();
		foreach ( $fields as $field ) {
			$forms[ $this->post_id ][ $field['name'] ] = get_post_meta( $this->post_id, '_wpcf7_redirect_' . $field['name'], true );
			if ( 'textarea' === $field['type'] ) {
				$forms[ $this->post_id ][ $field['name'] ] = $forms[ $this->post_id ][ $field['name'] ];
			}
		}

		// Thank you page URL is a little bit different...
		$forms[ $this->post_id ]['thankyou_page_url'] = $forms[ $this->post_id ]['page_id'] ? get_permalink( $forms[ $this->post_id ]['page_id'] ) : '';
		return reset( $forms );
	}

	/**
	 * Get rules for a specific contact form
	 */
	public function get_redirect_rules() {
		return get_post_meta( $this->post_id, '_wpcf7_redirect_' . $field['name'], true );
	}

	/**
	 * Get all fields values
	 */
	public function get_fields_values() {
		$fields = $this->get_plugin_fields();
		foreach ( $fields as $field ) {
			$values[ $field['name'] ] = get_post_meta( $this->post_id, '_wpcf7_redirect_' . $field['name'], true );
		}
		return $values;
	}

	/**
	 * Create plugin fields
	 */
	public function get_plugin_fields() {
		$fields = array_merge(
			WPCF7r_Form_Helper::get_plugin_default_fields(),
			array(
				array(
					'name' => 'blocks',
					'type' => 'blocks',
				),
			)
		);
		return $fields;
	}

	/**
	 * Get the contact form id
	 */
	public function get_id() {
		return $this->post_id;
	}

	/**
	 * Get the form fields for usage on the selectors
	 */
	public function get_cf7_fields() {
		$tags = self::get_mail_tags();
		return $tags;
	}

	/**
	 * Get special mail tags
	 */
	static function get_special_mail_tags() {
		$mailtags          = array();
		$special_mail_tags = array(
			'_remote_ip',
			'_user_agent',
			'_url',
			'_date',
			'_time',
			'_post_id',
			'_post_name',
			'_post_title',
			'_post_url',
			'_post_author',
			'_post_author_email',
			'_site_title',
			'_site_description',
			'_site_url',
			'_site_admin_email',
			'user_login',
			'user_email',
			'user_url',
		);

		foreach ( $special_mail_tags as $special_mail_tag ) {
			$tag        = new WPCF7_MailTag( $special_mail_tag, $special_mail_tag, array() );
			$mailtags[] = $tag;
		}

		return $mailtags;
	}

	/**
	 * Collect the mail tags from the form
	 */
	static function get_mail_tags() {
		// If this is a new form there are no tags yet
		if ( ! isset( self::$cf7_form ) || ! self::$cf7_form ) {
			return;
		}

		$tags = apply_filters( 'wpcf7r_collect_mail_tags', self::$cf7_form->scan_form_tags() );

		foreach ( (array) $tags as $tag ) {
			$type = trim( $tag['type'], ' *' );
			if ( empty( $type ) || empty( $tag['name'] ) ) {
				continue;
			} elseif ( ! empty( $args['include'] ) ) {
				if ( ! in_array( $type, $args['include'], true ) ) {
					continue;
				}
			} elseif ( ! empty( $args['exclude'] ) ) {
				if ( in_array( $type, $args['exclude'], true ) ) {
					continue;
				}
			}
			$mailtags[] = $tag;
		}

		// create an instance to get the current form instance
		$instance = self::get_instance( self::$cf7_form );

		// add a save lead tag in case save lead action is on
		if ( $instance->has_action( 'save_lead' ) ) {
			$scanned_tag = array(
				'type'       => 'lead_id',
				'basetype'   => trim( '[lead_id]', '*' ),
				'name'       => 'lead_id',
				'options'    => array(),
				'raw_values' => array(),
				'values'     => array(
					WPCF7R_Action::get_lead_id(),
				),
				'pipes'      => null,
				'labels'     => array(),
				'attr'       => '',
				'content'    => '',
			);
			$mailtags[]  = new WPCF7_FormTag( $scanned_tag );
		}

		return $mailtags;
	}

	/**
	 * Process actions that are relevant for validation process 
	 */
	public function process_validation_actions() {
		$actions = $this->get_active_actions();
		$results = array();

		if ( $actions ) {
			foreach ( $actions as $action ) {
				$action_result = $action->process_validation( self::get_submission() );
				if ( $action_result ) {
					$results[ $action->get_type() ][] = $action_result;
				}
			}
		}
		return $results;
	}

	/**
	 * Get validation object
	 */
	public static function get_validation_obj() {
		return self::$wpcf_validation_obj;
	}

	/**
	 * Get validation object $tags
	 */
	public static function get_validation_obj_tags() {
		$validation_obj = self::get_validation_obj();
		return isset( $validation_obj->tags ) ? $validation_obj->tags : '';
	}

	/**
	 * Save a referrence to the validaiont object
	 * This will enable invalidating tags later on
	 *
	 * @param object $wpcf_validation_obj
	 */
	public function set_validation_obj( $wpcf_validation_obj ) {
		self::$wpcf_validation_obj = $wpcf_validation_obj;
	}

	/**
	 * Handles submission actions
	 */
	public function process_actions() {
		// get all active actions
		$actions = $this->get_active_actions();
		// prepeare the results array
		$results = array();
		// get removed fom data
		self::$submission->removed_posted_data = $this->get_removed_form_params();

		if ( $actions ) {// loop through actions and process
			foreach ( $actions as $action ) {
				// save the validation object in case this action manipulates validations
				// do the action
				$action_result = $action->process_action( $this );
				// add the action to the results array
				$results[ $action->get_type() ][] = $action_result;
				self::$processed_actions[]        = $action;
			}
		} else {
			return false;
		}

		return $results;
	}

	/**
	 * Get all processed actions
	 */
	public function get_processed_actions() {
		return isset( self::$processed_actions ) && self::$processed_actions ? self::$processed_actions : '';
	}

	/**
	 * Check if actions hold one last thing to do before returning result to the user
	 */
	public function maybe_perform_pre_result_action() {
		$actions = $this->get_processed_actions();
		if ( $actions ) {
			foreach ( $actions as $action ) {
				$action->maybe_perform_pre_result_action();
			}
		}
	}
}
