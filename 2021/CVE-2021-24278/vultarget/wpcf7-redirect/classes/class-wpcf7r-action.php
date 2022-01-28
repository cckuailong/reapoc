<?php
/**
 * Class WPCF7R_Action - פarent class that handles all redirect actions.
 */

defined( 'ABSPATH' ) || exit;

class WPCF7R_Action {
	// Save a reference to the lead id in case the save lead action is on.
	public static $lead_id;

	// Saved data from validation action to submission action.
	public static $data;

	/**
	 * Class constructor
	 * Set required parameters
	 *
	 * @param string $post [description]
	 */
	public function __construct( $post = '' ) {
		$this->priority = 2;

		if ( $post ) {
			// save a refference to the action post.
			$this->action_post = $post;
			// set the action post ID.
			$this->action_post_id = $post->ID;
			// get the custom action fields.
			$this->fields_values = get_post_custom( $this->action_post_id );
			// get the contact form 7 post id.
			$this->wpcf7_id = $this->get_action_wpcf7_id( $this->action_post_id );
			// get the type of action.
			$this->action = self::get_action_type( $this->action_post_id );
			// get tje status of the action (is it active or not).
			$this->action_status = $this->get_action_status( $this->action_post_id );
			// get conditional logic blocks.
			$this->logic_blocks = $this->get( 'blocks' );
		}
	}

	/**
	 * Returns an html for displaying a link to the form.
	 *
	 * @return [string] - a link to the form edit screen.
	 */
	public function get_cf7_link_html() {
		return WPCF7r_Form_Helper::get_cf7_link_html( $this->wpcf7_id );
	}

	/**
	 * Connected to manage_columns hooks.
	 *
	 * @param [string] $column - key of the column.
	 * @param [int]    $post_id - the id of the relevant post.
	 * @return void
	 */
	public function display_action_column_content( $column, $post_id ) {

	}

	/**
	 * Process validation action
	 * This function will be called on validation hook
	 *
	 * @param $submission
	 */
	public function process_validation( $submission ) { }

	/**
	 * Get action name
	 */
	public function get_name() {
		return WPCF7r_Utils::get_action_name( $this->action );
	}

	/**
	 * Adds a blank select option for select fields
	 */
	public function get_tags_optional() {
		$tags          = $this->get_mail_tags_array();
		$tags_optional = array_merge( array( __( 'Select', 'wpcf7-redirect' ) ), $tags );
		return $tags_optional;
	}

	/**
	 * save a reference to the lead id in case the save lead action is on
	 *
	 * @param $lead_id
	 */
	public function set_lead_id( $lead_id ) {
		self::$lead_id = $lead_id;
	}

	/**
	 * Get all system user roles
	 */
	public function get_available_user_roles() {
		return wp_roles()->get_names();
	}

	/**
	 * Return the current lead id if it is availavle
	 */
	public static function get_lead_id() {
		return isset( self::$lead_id ) ? self::$lead_id : '';
	}

	/**
	 * General function to retrieve meta
	 *
	 * @param $key
	 */
	public function get( $key ) {
		return isset( $this->fields_values[ $key ][0] ) ? $this->fields_values[ $key ][0] : '';
	}

	/**
	 * Get the contact form 7 related post id
	 */
	public function get_cf7_post_id() {
		return isset( $this->wpcf7_id ) ? $this->wpcf7_id : '';
	}

	/**
	 * Set action property
	 *
	 * @param $key
	 * @param $value
	 */
	public function set( $key, $value ) {
		update_post_meta( $this->action_post_id, $key, $value );
		$this->fields_values[ $key ][0] = $value;
	}

	/**
	 * Enqueue extension scripts and styles.
	 *
	 * @return void
	 */
	public static function enqueue_backend_scripts() {

	}

	/**
	 * Enqueue extension scripts and styles.
	 *
	 * @return void
	 */
	public static function enqueue_frontend_scripts() {

	}
	/**
	 * Parent get action fields function
	 */
	public function get_action_fields() {
		return array();
	}

	/**
	 * Get a set of fields/specific field settings by key
	 *
	 * @param $fields_key
	 */
	public function get_fields_settings( $fields_key ) {
		$fields = $this->get_action_fields();

		return $fields[ $fields_key ];
	}

	/**
	 * Get the id of the rule
	 */
	public function get_rule_id() {
		return $this->get( 'wpcf7_rule_id' );
	}

	/**
	 * Get all fields values
	 */
	public function get_fields_values() {
		$fields = $this->get_action_fields();
		foreach ( $fields as $field ) {
			$values[ $field['name'] ] = $this->get_field_value( $field );
		}
		return $values;
	}

	/**
	 * Get mail tags objects
	 */
	public function get_mail_tags() {
		$mail_tags = WPCF7R_Form::get_mail_tags();
		return $mail_tags;
	}

	/**
	 * Get mail tags objects
	 */
	public function get_mail_tags_array() {
		$mail_tags       = WPCF7R_Form::get_mail_tags();
		$mail_tags_array = array();
		if ( $mail_tags ) {
			foreach ( $mail_tags as $mail_tag ) {
				$mail_tags_array[ $mail_tag->name ] = $mail_tag->name;
			}
		}
		return $mail_tags_array;
	}

	/**
	 * Get mail tags to display on the settings panel
	 *
	 * @param boolean $clean
	 */
	public function get_formatted_mail_tags( $clean = false ) {
		$formatted_tags = array();

		if ( ! is_array( WPCF7R_Form::get_mail_tags() ) ) {
			return;
		}

		foreach ( WPCF7R_Form::get_mail_tags() as $mail_tag ) {
			$formatted_tags[] = "<span class='mailtag code'>[{$mail_tag->name}]</span>";
		}

		// foreach( WPCF7R_Form::get_special_mail_tags() as $mail_tag ){
		// $formatted_tags[] = "<br/><span class='mailtag code'>[".$mail_tag->field_name()."]</span>";
		// }

		$formatted_tags = implode( '', $formatted_tags );
		if ( $clean ) {
			$formatted_tags = str_replace( array( ']' ), ', ', $formatted_tags );
			$formatted_tags = str_replace( array( '[' ), '', $formatted_tags );
		}

		ob_start();
		?>

		<div class="mail-tags-wrapper">
			<div class="mail-tags-title" data-toggle=".mail-tags-wrapper-inner">
				<strong><?php _e( 'Available mail tags', 'wpcf7-redirect' ); ?></strong> <span class="dashicons dashicons-arrow-down"></span>
			</div>
			<div class="mail-tags-wrapper-inner field-hidden">
				<?php echo $formatted_tags; ?>
				<div class="special-mail-tags">
					<br/>
					<a href="https://contactform7.com/special-mail-tags/"><?php _e( 'Special mail tags' ); ?></a>
					<div><small><?php _e( 'These tags are available only inside the loop as described by the plugin author', 'wpcf7-redirect' ); ?></small></div>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Replace lead id from the lead manager
	 *
	 * @param $template
	 */
	public function replace_lead_id_tag( $template ) {
		return str_replace( '[lead_id]', self::get_lead_id(), $template );
	}

	/**
	 * Replace all mail tags in a string
	 *
	 * @param $content
	 * @param $args
	 */
	public function replace_tags( $content, $args = '' ) {
		if ( true === $args ) {
			$args = array( 'html' => true );
		}
		$args          = wp_parse_args(
			$args,
			array(
				'html'          => false,
				'exclude_blank' => false,
			)
		);
		$replaced_tags = wpcf7_mail_replace_tags( $content, $args );
		$replaced_tags = do_shortcode( $replaced_tags );
		$replaced_tags = $this->replace_lead_id_tag( $replaced_tags );
		return $replaced_tags;
	}

	/**
	 * Get the value of a specific field
	 *
	 * @param $field
	 */
	public function get_field_value( $field ) {
		if ( is_array( $field ) ) {
			return get_post_meta( $this->action_post_id, '_wpcf7_redirect_' . $field['name'], true );
		} else {
			return get_post_meta( $this->action_post_id, '_wpcf7_redirect_' . $field, true );
		}
	}

	/**
	 * Get an instance of the relevant action class
	 *
	 * @param $post
	 */
	public static function get_action( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! isset( $post->ID ) ) {
			return false;
		}

		$action_type = self::get_action_type( $post->ID );
		$class       = "WPCF7R_Action_{$action_type}";
		$action      = '';

		if ( class_exists( $class ) ) {
			$action = new $class( $post );
		} else {
			$action_type = str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $action_type ) ) );
			$class       = "WPCF7R_Action_{$action_type}";

			if ( class_exists( $class ) ) {
				$action = new $class( $post );
			} else {
				$action = new WP_Error( 'get_action', "Class {$class} Does not exist" );
			}
		}

		return $action;
	}

	/**
	 * Get the action post_id
	 */
	public function get_id() {
		return $this->action_post_id;
	}

	/**
	 * Get the type of the action
	 *
	 * @param $post_id
	 */
	public static function get_action_type( $post_id ) {
		$action_type = get_post_meta( $post_id, 'action_type', true );

		$migration_list = array(
			'send_mail'   => 'SendMail',
			'fire_script' => 'FireScript',
		);

		if ( isset( $migration_list[ $action_type ] ) ) {
			update_post_meta( $post_id, 'action_type', $migration_list[ $action_type ] );

			$action_type = $migration_list[ $action_type ];
		}

		return $action_type;
	}

	/**
	 * Get action status
	 */
	public function get_action_status() {
		return $this->get( 'action_status' );
	}

	/**
	 * Get action status
	 */
	public function get_action_status_label() {

		/* translators:%s the action status name */
		return $this->get_action_status() === 'on' ? sprintf( __( 'Enable %s', 'wpcf7-redirect' ), $this->get_name() ) : __( 'Disabled', 'wpcf7-redirect' );
	}


	/**
	 * Get contact form id
	 *
	 * @return int form id
	 */
	public function get_action_wpcf7_id() {
		return $this->get( 'wpcf7_id' );
	}

	/**
	 * Get the action title
	 *
	 * @return string action title
	 */
	public function get_title() {
		return $this->action_post->post_title;
	}

	/**
	 * Get the action type
	 *
	 * @return string action type
	 */
	public function get_type() {
		return $this->action;
	}

	/**
	 * Get the action pretty name
	 *
	 * @return string action pretty name
	 */
	public function get_type_label() {
		$actions = wpcf7r_get_available_actions();
		$type    = $actions[ $this->get_type() ]['label'];
		return $type;
	}

	/**
	 * Get the action status
	 *
	 * @return string action status
	 */
	public function get_status() {
		return $this->action_status;
	}

	/**
	 * Get the action menu order
	 */
	public function get_menu_order() {
		return $this->action_post->menu_order;
	}

	/**
	 * Get the tags used on the form
	 *
	 * @param string $tag_name
	 */
	public function get_validation_mail_tags( $tag_name = '' ) {
		$tags = WPCF7R_Form::get_validation_obj_tags();
		if ( $tag_name ) {
			foreach ( $tags as $tag ) {
				if ( $tag->name === $tag_name ) {
					return $tag;
				}
			}
		} else {
			return $tags;
		}
	}

	/**
	 * Get default actions field
	 * This actions will apply for all child action classes
	 */
	function get_default_fields() {
		$args = array(
			'action_status' => array(
				'name'          => 'action_status',
				'type'          => 'checkbox',
				'label'         => $this->get_action_status_label(),
				'sub_title'     => 'if this is off the rule will not be applied',
				'placeholder'   => '',
				'show_selector' => '',
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
		);

		if ( conditional_logic_enabled() ) {
			$args['conditional_logic'] = array(
				'name'          => 'conditional_logic',
				'type'          => 'checkbox',
				'label'         => __( 'Conditional Logic', 'wpcf7-redirect' ),
				'sub_title'     => '',
				'placeholder'   => '',
				'show_selector' => '.conditional-logic-blocks',
				'value'         => $this->get( 'conditional_logic' ),
			);
		} else {
			$args['conditional_logic'] = array(
				'name'          => 'conditional_logic',
				'type'          => 'notice',
				'label'         => __( '<strong>CONDITIONAL LOGIC!</strong><br/>', 'wpcf7-redirect' ),
				'sub_title'     => __( 'You can purchase and activate conditional logic addon on the extensions tab.', 'wpcf7-redirect' ),
				'placeholder'   => '',
				'class'         => 'field-notice-alert',
				'show_selector' => '',
			);
		}

		$args['blocks'] = array(
			'name'                  => 'blocks',
			'type'                  => 'blocks',
			'has_conditional_logic' => $this->get( 'conditional_logic' ),
			'blocks'                => $this->get_conditional_blocks(),
		);
		return $args;
	}

	/**
	 * Reset all action fields
	 */
	public function delete_all_fields() {
		$fields = $this->get_action_fields();

		foreach ( $fields as $field ) {
			delete_post_meta( $this->action_post_id, $field['name'] );

			if ( isset( $field['fields'] ) && $field['fields'] ) {
				foreach ( $field['fields'] as $sub_field_key => $sub_field ) {
					delete_post_meta( $this->action_post_id, $sub_field_key );
				}
			}
		}
	}

	/**
	 * Get the template to display on the admin field
	 *
	 * @param $template
	 */
	public function get_settings_template( $template ) {
		$prefix = "[actions][$this->action_post_id]";
		include WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH . 'rule-title.php';
		include WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH . $template;
	}

	/**
	 * Get a single action row
	 */
	public function get_action_row() {
		ob_start();
		do_action( 'before_wpcf7r_action_row', $this );
		?>
		<tr class="drag primary <?php echo $this->get_action_status() ? 'active' : 'non-active'; ?>" data-actionid="<?php echo $this->get_id(); ?>" id="post-<?php echo $this->get_id(); ?>">
			<td class="manage-column check-column ">
				<span class="num"><?php echo $this->get_menu_order(); ?></span>
			</td>
			<td class="manage-column column-title column-primary sortable desc">
				<span class="edit">
					<a href="#" class="column-post-title" aria-label="<?php _e( 'Edit', 'wpcf7-redirect' ); ?>"><?php echo $this->get_title(); ?></a>
				</span>
				<div class="row-actions">
					<span class="edit">
						<a href="#" aria-label="<?php _e( 'Edit', 'wpcf7-redirect' ); ?>"><?php _e( 'Edit', 'wpcf7-redirect' ); ?></a> |
					</span>
					<span class="trash">
						<a href="#" class="submitdelete" data-id="<?php echo $this->get_id(); ?>" aria-label="<?php _e( 'Move to trash', 'wpcf7-redirect' ); ?>"><?php _e( 'Move to trash', 'wpcf7-redirect' ); ?></a> |
					</span>
					<span class="duplicate">
						<a href="#" class="submitduplicate" data-ruleid="default" data-id="<?php echo $this->get_id(); ?>" aria-label="<?php _e( 'Duplicate', 'wpcf7-redirect' ); ?>"><?php _e( 'Duplicate', 'wpcf7-redirect' ); ?></a>
					</span>
					<?php if ( $this->get_type() === 'save_lead' ) : ?>
						<span class="view-leads">
						 | <a href="<?php echo WPCF7R_Leads_Manager::get_admin_url( $this->wpcf7_id ); ?>" class="show-leads" data-id="<?php echo $this->get_id(); ?>" aria-label="<?php _e( 'View Leads', 'wpcf7-redirect' ); ?>" target="_blank"><?php _e( 'View Leads', 'wpcf7-redirect' ); ?></a> |
						</span>
					<?php endif; ?>

					<?php do_action( 'wpcf7r_after_actions_links', $this ); ?>
				</div>
			</td>
			<td class="manage-column column-primary sortable desc edit">
				<a href="#" aria-label="<?php _e( 'Edit', 'wpcf7-redirect' ); ?>"><?php echo $this->get_type_label(); ?></a>
			</td>
			<td class="manage-column column-primary sortable desc edit column-status">
				<a href="#" aria-label="<?php _e( 'Edit', 'wpcf7-redirect' ); ?>"><?php echo $this->get_action_status_label(); ?></a>
			</td>
			<td class="manage-column check-column">
				<input type="hidden" name="post[]" value="<?php echo $this->get_id(); ?>">
				<span class="dashicons dashicons-menu handle"></span>
			</td>
		</tr>
		<tr data-actionid="<?php echo $this->get_id(); ?>" class="action-container">
			<td colspan="5" >
				<div class="hidden-action">
					<?php $this->get_action_settings(); ?>
				</div>
			</td>
		</tr>
		<?php

		do_action( 'after_wpcf7r_action_row', $this );

		return apply_filters( 'wpcf7r_get_action_rows', ob_get_clean(), $this );
	}

	/**
	 * Get settings page
	 */
	public function get_action_settings() {
		$this->get_settings_template( 'html-action-send-to-email.php' );
	}

	/**
	 * Render HTML field
	 *
	 * @param $field
	 * @param $prefix
	 */
	public function render_field( $field, $prefix ) {
		WPCF7R_Html::render_field( $field, $prefix );
	}

	/**
	 * Check if the action has conditional rules
	 *
	 * @return boolean
	 */
	public function has_conditional_logic() {
		return $this->get( 'conditional_logic' ) && conditional_logic_enabled() ? true : false;
	}

	/**
	 * Maybe perform actions before sending results to the user
	 */
	public function maybe_perform_pre_result_action() {
	}

	/**
	 * Get the submitted form data
	 */
	public function get_posted_data() {
		return $this->submission_data;
	}

	/**
	 * This will process the required rules
	 *
	 * @param $cf7r_form
	 */
	public function process_action( $cf7r_form ) {
		$results = array();

		$this->cf7r_form       = $cf7r_form;
		$this->submission_data = $this->cf7r_form->get_submission();
		$this->posted_data_raw = $this->submission_data->get_posted_data();
		$this->form_tags       = $this->cf7r_form->get_cf7_form_instance()->scan_form_tags();

		// get conditional logic object
		$clogic = class_exists( 'WPCF7_Redirect_Conditional_Logic' ) ? new WPCF7_Redirect_Conditional_Logic( $this->logic_blocks, $this->cf7r_form ) : '';

		if ( ! conditional_logic_enabled() || ! $this->has_conditional_logic() ) {
			// if no conditions are defined
			$results = $this->process( $this->submission_data );
		} elseif ( conditional_logic_enabled() && $clogic->conditions_met() ) {
			$results = $this->process( $this->submission_data );
		}

		return $results;
	}

	/**
	 * Handle a simple redirect rule
	 *
	 * @param  $rules
	 * @param  $response
	 */
	public function process( $submission ) {

	}

	/**
	 * Get all saved blocks
	 */
	public function get_conditional_blocks() {
		$blocks = $this->get( 'blocks' );
		if ( ! $blocks ) {
			$blocks = array(
				array(
					'block_title' => 'Block title',
					'groups'      => $this->get_groups(),
					'block_key'   => 'block_1',
				),
			);
		} else {
			$blocks                           = maybe_unserialize( $blocks );
			$blocks['block_1']['block_key']   = 'block_1';
			$blocks['block_1']['block_title'] = 'Block title';
		}
		return $blocks;
	}

	/**
	 * Find the relevant rule to use
	 */
	public function get_valid_rule_block() {
		$blocks = $this->get( 'blocks' );
		$blocks = maybe_unserialize( $blocks );
		if ( isset( $blocks ) && $blocks ) {
			foreach ( $blocks as $block ) {
				if ( isset( $block['groups'] ) && $block['groups'] ) {
					foreach ( $block['groups'] as $and_rows ) {
						$valid = true;
						if ( $and_rows ) {
							foreach ( $and_rows as $and_row ) {
								if ( ! $this->is_valid( $and_row ) ) {
									$valid = false;
									break;
								}
							}
							if ( $valid ) {
								break;
							}
						}
					}
					if ( $valid ) {
						return $block;
					}
				}
			}
		}
	}

	/**
	 * Get an instance of a form tag object
	 *
	 * @param $form_tag_name
	 */
	private function get_form_tag( $form_tag_name ) {
		if ( $this->form_tags ) {
			foreach ( $this->form_tags as $form_tag ) {
				if ( $form_tag->name === $form_tag_name ) {
					return $form_tag;
				}
			}
		}
	}

	/**
	 * Use cf7 mechanizm to get the form tag value
	 * Including pipes and default values
	 *
	 * @param  $form_tag_name
	 */
	private function get_form_tag_posted_data( $form_tag_name ) {
		$form_tag = $this->get_form_tag( $form_tag_name );
		$value    = '';

		if ( $form_tag ) {
			$posted_value = $this->submission_data->get_posted_data( $form_tag_name );
			$type         = $form_tag->type;
			$name         = $form_tag->name;
			$pipes        = $form_tag->pipes;
			$value_orig   = $value;
			$value_orig   = $posted_value;
			if (
				( defined( 'WPCF7_USE_PIPE' ) && WPCF7_USE_PIPE )
				&& $pipes instanceof WPCF7_Pipes
				&& ! $pipes->zero()
			) {
				if ( is_array( $value_orig ) ) {
					$value = array();
					foreach ( $value_orig as $v ) {
						$value[] = $pipes->do_pipe( wp_unslash( $v ) );
					}
				} else {
					$value = $pipes->do_pipe( wp_unslash( $value_orig ) );
				}
			} else {
				$value = $posted_value;
			}
		}
		return $value;
	}

	/**
	 * Check rule
	 *
	 * @param $and_row
	 */
	public function is_valid( $and_row ) {
		$valid = false;
		if ( isset( $and_row['condition'] ) && $and_row['condition'] ) {
			$tag_name      = isset( $and_row['if'] ) ? $and_row['if'] : '';
			$posted_value  = $this->get_form_tag_posted_data( $tag_name );
			$compare_value = $and_row['value'];
			switch ( $and_row['condition'] ) {
				case 'equal':
					if ( isset( $posted_value ) && is_array( $posted_value ) ) {
						$valid = in_array( $compare_value, $posted_value, true ) || $compare_value === $posted_value ? true : false;
					} else {
						$valid = $compare_value === $posted_value;
					}
					break;
				case 'not-equal':
					if ( is_array( $posted_value ) ) {
						$valid = ! in_array( $compare_value, $posted_value, true );
					} else {
						$valid = $compare_value !== $posted_value;
					}
					break;
				case 'contain':
					$valid = strpos( $posted_value, $compare_value ) !== false;
					break;
				case 'not-contain':
					$valid = strpos( $posted_value, $compare_value ) === false;
					break;
				case 'greater_than':
					$valid = $posted_value > $compare_value;
					break;
				case 'less_than':
					$valid = $posted_value < $compare_value;
					break;
				case 'is_null':
					$valid = '' === $posted_value;
					break;
				case 'is_not_null':
					$valid = '' === $posted_value;
					break;
			}
		}
		return apply_filters( 'wpcf7r_is_valid', $valid, $and_row );
	}

	/**
	 * Get the fields relevant for conditional group
	 */
	public function get_group_fields() {
		return array_merge(
			array(
				array(
					'if'        => '',
					'condition' => '',
					'value'     => '',
				),
			)
		);
	}

	/**
	 * Get the saved groups or display the default first one
	 */
	public function get_groups() {
		$groups = array(
			'group-0' => $this->get_group_fields(),
		);
		return $groups;
	}

	/**
	 * Process all pre cf7 submit actions
	 */
	public function process_pre_submit_actions() {
	}
}
