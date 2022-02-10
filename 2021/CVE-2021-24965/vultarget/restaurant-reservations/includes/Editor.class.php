<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'cffrtbEditor' ) ) {
/**
 * Class which builds the form editor for Custom Fields for Restaurant
 * Reservations
 *
 * @since 0.1
 */
class cffrtbEditor {

	/**
	 * Hook suffix for the page
	 *
	 * @since 0.1
	 */
	public $hook_suffix;

	public function __construct() {

		// Add the admin menu
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 20 );

		// Handle ajax requests
		add_action( 'wp_ajax_nopriv_cffrtb-save-field' , array( $this , 'ajax_nopriv' ) );
		add_action( 'wp_ajax_cffrtb-save-field', array( $this, 'ajax_save_field' ) );
		add_action( 'wp_ajax_nopriv_cffrtb-save-order' , array( $this , 'ajax_nopriv' ) );
		add_action( 'wp_ajax_cffrtb-save-order', array( $this, 'ajax_save_order' ) );
		add_action( 'wp_ajax_nopriv_cffrtb-load-field' , array( $this , 'ajax_nopriv' ) );
		add_action( 'wp_ajax_cffrtb-load-field', array( $this, 'ajax_load_field' ) );
		add_action( 'wp_ajax_nopriv_cffrtb-delete-field' , array( $this , 'ajax_nopriv' ) );
		add_action( 'wp_ajax_cffrtb-delete-field', array( $this, 'ajax_delete_field' ) );
		add_action( 'wp_ajax_nopriv_cffrtb-enable-field' , array( $this , 'ajax_nopriv' ) );
		add_action( 'wp_ajax_cffrtb-enable-field', array( $this, 'ajax_enable_field' ) );
		add_action( 'wp_ajax_nopriv_cffrtb-reset-all' , array( $this , 'ajax_nopriv' ) );
		add_action( 'wp_ajax_cffrtb-reset-all', array( $this, 'ajax_reset_all' ) );

		// Load "pointers" (help tooltips on initial load)
		require_once( RTB_PLUGIN_DIR . '/includes/custom_fields_pointers.php' );
	}

	/**
	 * Add the booking form editor page to the admin menu
	 *
	 * @since 0.1
	 */
	public function add_menu_page() {
		$this->hook_suffix = add_submenu_page(
			'rtb-bookings',
			_x( 'Custom Fields', 'Title of the Custom Fields editor page', 'restaurant-reservations' ),
			_x( 'Custom Fields', 'Title of Custom Fields editor link in the admin menu', 'restaurant-reservations' ),
			'manage_options',
			'cffrtb-editor',
			array( $this, 'display_editor_page' )
		);

		// Print the error modal and enqueue assets
		add_action( 'load-' . $this->hook_suffix, array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_footer-' . $this->hook_suffix, array( $this, 'print_modals' ) );
	}

	/**
	 * Enqueue assets on the editor page
	 *
	 * @since 0.1
	 */
	public function enqueue_admin_assets() {
		global $rtb_controller;

		if ( ! $rtb_controller->permissions->check_permission( 'custom_fields' ) ) { return; }

		// Retrieve pointers (admin tooltips)
		$pointers = apply_filters( 'cffrtb_pointers', array() );

		// Determine editor dependencies
		$editor_css_deps = array( 'rtb-booking-form' );
		$editor_js_deps = array( 'jquery-ui-sortable', 'rtb-booking-form' );
		if ( !empty( $pointers ) ) {
			$editor_css_deps[] = 'wp-pointer';
			$editor_js_deps[] = 'wp-pointer';
		}

		// Booking form assets
		$rtb_controller->register_assets();
		rtb_enqueue_assets();

		// Editor assets
		wp_enqueue_style( 'cffrtb-editor', RTB_PLUGIN_URL . '/assets/css/editor.css', $editor_css_deps, false );
		wp_enqueue_script( 'cffrtb-editor', RTB_PLUGIN_URL . '/assets/js/editor.js', $editor_js_deps, false, true );

		// Pass the fields array to the script
		$field_controller = $rtb_controller->fields;
		$field_controller->get_valid_field_types();
		wp_localize_script(
			'cffrtb-editor',
			'cffrtb_editor',
			array(
				'ajax_nonce'		=> wp_create_nonce( 'cffrtb-editor' ),
				'default_type'		=> key( $field_controller->valid_field_types ),
				'default_subtype'	=> key( $field_controller->valid_field_types[ key( $field_controller->valid_field_types ) ]['subtypes'] ),
				'pointers'			=> $pointers,
				'strings'		=> array(
					'save'					=> __( 'Save', 'restaurant-reservations' ),
					'editor_add_field'		=> __( 'Add Field', 'restaurant-reservations' ),
					'editor_edit_field'		=> __( 'Edit Field', 'restaurant-reservations' ),
					'editor_save_field'		=> __( 'Save Field', 'restaurant-reservations' ),
					'editor_add_fieldset'	=> __( 'Add Fieldset', 'restaurant-reservations' ),
					'editor_save_fieldset'	=> __( 'Save Fieldset', 'restaurant-reservations' ),
					'field_missing_title'	=> __( 'Please enter a label for this field.', 'restaurant-reservations' ),
					'field_missing_options'	=> __( 'To add an Option field you must add at least one option below.', 'restaurant-reservations' ),
					'fieldset_not_empty'	=> __( 'This fieldset can not be deleted until all of its attached fields are removed or assigned to another fieldset.', 'restaurant-reservations' ),
					'confirm_reset_all'		=> __( 'Are you sure you want to reset the booking form? All of your changes and custom fields will be removed. This action can not be undone.', 'restaurant-reservations' ),
					'unknown_error'		=> __( 'An unspecified error occurred. Please try again. If the problem persists, try logging out and logging back in.', 'restaurant-reservations' ),
				),
			)
		);
	}

	/**
	 * Display the booking form editor page
	 *
	 * @since 0.1
	 */
	public function display_editor_page() {
		global $rtb_controller;

		$custom_fields_permission = $rtb_controller->permissions->check_permission( 'custom_fields' )

		?>

		<div class="wrap">
			<h2>
				<?php _e( 'Custom Fields Editor', 'restaurant-reservations' ); ?>
				<a href="#" class="add-new-h2 add-field">Add New</a>
			</h2>
			<?php if ( $custom_fields_permission ) { ?> 
				<div id="cffrtb-editor">
					<?php $this->print_booking_form_fields(); ?>
				</div>
			<?php } else { ?>
				<div class='rtb-premium-locked'>
					<a href="https://www.fivestarplugins.com/license-payment/?Selected=RTB&Quantity=1" target="_blank">Upgrade</a> to the premium version to use this feature
				</div>
			<?php } ?>
		</div>

		<?php
	}

	/**
	 * Print the booking form fields for editing
	 *
	 * @since 0.0.1
	 */
	public function print_booking_form_fields() {
		global $rtb_controller;

		// Retrieve the form fields
		$fields = $rtb_controller->settings->get_booking_form_fields();

		// Retrieve system fields with limited editing abilities
		$field_controller = $rtb_controller->fields;
		$field_controller->get_system_fields();
		?>

		<div class="cffrtb-lft">

			<ul id="cffrtb-list" class="cffrtb-list">

				<?php
					foreach( $fields as $fieldset => $contents ) {
						echo $this->print_field( $fieldset, $contents, 'fieldset' );
					}
				?>

			</ul>

			<a href="#" class="add-field">
				<span class="dashicons dashicons-plus-alt"></span>
				<?php _e( 'Add New', 'restaurant-reservations' ); ?>
			</a>

		</div>
		<div class="cffrtb-rgt">

			<div id="cffrtb-disabled" class="cffrtb-list">
				<h3><?php _e( 'Disabled Fields', 'restaurant-reservations' ); ?></h3>

				<?php
				$modified = get_option( $rtb_controller->custom_fields->modified_option_key );
				if ( $modified ) :

					$list = '';
					foreach( $modified as $slug => $field ) {

						if ( !empty( $field['disabled'] ) ) {

							$default_field = $slug == $field['fieldset'] ? $field_controller->default_fields[ $slug ] : $field_controller->get_nested_field( $slug, $field_controller->default_fields );
							if ( empty( $default_field ) || !is_array( $default_field ) ) {
								continue;
							}

							$type = $slug == $field['fieldset'] ? 'fieldset' : 'field';

							$list .= $this->print_field( $slug, array_merge( $default_field, $field ), $type );
						}
					}

					if ( !empty( $list ) ) :
					?>

						<ul class="fields">
							<?php echo $list; ?>
						</ul>

					<?php else : ?>

						<p class="description no-disabled-fields"><?php _e( 'You have not disabled any default fields yet.', 'restaurant-reservations' ); ?></p>

					<?php
					endif;
				endif;
				?>

				<div class="reset<?php echo !empty( $modified ) ? ' is-visible' : ''; ?>">
					<a href="#" class="button reset-all">
						<?php _e( 'Revert to default', 'restaurant-reservations' ); ?>
					</a>
					<a href="#" class="learn-more">
						<?php _e( 'Learn more', 'restaurant-reservations' ); ?>
					</a>
					<p class="description learn-more-details">
						<?php _e( 'All of your changes and custom fields will be discarded if you revert to default. This is not advised unless you want to remove all of your changes and restore the default booking form.', 'restaurant-reservations' ); ?>
					</p>
				</div>
			</div>

		</div>

		<?php
	}

	/**
	 * Print a single field in the fields list
	 *
	 * @since 0.1
	 */
	public function print_field( $slug, $field, $type = 'field' ) {
		global $rtb_controller;

		$field_controller = $rtb_controller->fields;
		if ( empty( $field_controller->system_fields ) ) {
			$field_controller->get_system_fields();
		}

		$title = '';
		if ( $type == 'fieldset' && !empty( $field['legend'] ) ) {
			$title = $field['legend'];
		} elseif ( !empty( $field['title'] ) ) {
			$title = $field['title'];
		}

		ob_start();
		?>

			<li class="<?php echo $type; echo !empty( $field['disabled'] ) ? ' disabled' : ''; ?>" data-slug="<?php echo esc_attr( $slug ); ?>"<?php echo empty( $field['ID'] ) ? '' : ' data-id="' . (int) $field['ID']  . '"'; ?>>

				<div class="title">
					<div class="view">
						<span class="value">
							<?php echo esc_html( $title ); ?>
						</span>

						<div class="controls">
							<?php if ( empty( $field['disabled'] ) ) : ?>
							<a href="#" class="label" title="<?php _e( 'Edit title', 'restaurant-reservations' ); ?>">
								<span class="dashicons dashicons-edit"></span>
							</a>
							<?php endif; ?>
							<?php if ( !empty( $field['disabled'] ) ) : ?>
								<a href="#" class="enable" title="<?php _e( 'Enable field', 'restaurant-reservations' ); ?>">
									<span class="dashicons dashicons-visibility"></span>
								</a>
							<?php elseif ( ( $type == 'field' && !in_array( $slug, $field_controller->system_fields ) ) || ( $type == 'fieldset' && !in_array( $slug, $field_controller->system_fieldsets ) ) ) : ?>
								<?php if ( $type == 'field' && !empty( $field['ID'] ) ) : ?>
								<a href="#" class="options" title="<?php _e( 'Edit field', 'restaurant-reservations' ); ?>">
									<span class="dashicons dashicons-admin-tools"></span>
								</a>
								<?php endif; ?>
								<a href="#" class="delete" title="<?php _e( 'Delete field', 'restaurant-reservations' ); ?>">
									<span class="dashicons dashicons-no"></span>
								</a>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( empty( $field['disabled'] ) ) : ?>
					<div class="edit">
						<input type="text" name="title" value="<?php echo esc_html( $title ); ?>" tabindex="-1">

						<div class="controls">
							<a href="#" class="save" tabindex="-1">
								<?php _e( 'Save', 'restaurant-reservations' ); ?>
							</a>
						</div>

						<div class="status">
							<span class="load-spinner"></span>
						</div>
					</div>
					<?php endif; ?>

				</div>

				<?php if ( $type == 'fieldset' && empty( $field['disabled'] ) && empty( $field['exclude_fields'] ) ) : ?>
					<ul class="fields">
					<?php
						if( !empty( $field['fields'] ) ) :
							foreach( $field['fields'] as $field_slug => $sub_field ) :
								echo $this->print_field( $field_slug, $sub_field );
							endforeach;
						endif;
					?>
					</ul>
				<?php endif; ?>
			</li>
		<?php

		return ob_get_clean();
	}

	/**
	 * Print a label field for the editing form
	 *
	 * @since 0.1
	 */
	public function print_label_input( $slug, $label ) {
		?>

		<div class="label">
			<label for="<?php echo esc_attr( $slug ); ?>_label">
				<?php _e( 'Label', 'restaurant-reservations' ); ?>
			</label>
			<input type="text" name="label" id="<?php echo esc_attr( $slug ); ?>_label" value="<?php echo esc_attr( $label ); ?>">
		</div>

		<?php
	}

	/**
	 * Print the error modal in the footer. This re-uses the error modal
	 * markup and styling from Restaurant Reservations
	 *
	 * @since 0.1
	 */
	public function print_modals() {
		global $rtb_controller;

		$field_controller = $rtb_controller->fields;
		$field_controller->get_valid_field_types();

		$default_type = key( $field_controller->valid_field_types );
		$default_subtype = key( $field_controller->valid_field_types[ $default_type ]['subtypes'] );
		?>
		<div id="cffrtb-field-editor" class="rtb-admin-modal">

			<form id="cffrtb-field-editor-form" class="rtb-container">
				<input type="hidden" name="id" value="">
				<input type="hidden" name="type" value="<?php echo esc_attr( $default_type ); ?>">
				<input type="hidden" name="subtype" value="<?php echo esc_attr( $default_type ); ?>">

				<div class="title">
					<h2><?php _e( 'Add Field', 'restaurant-reservations' ); ?></h2>
				</div>

				<div class="type">
					<label>
						<?php _e( 'Field Type', 'restaurant-reservations' ); ?>
					</label>

					<div class="selector">
						<ul class="types">
							<?php foreach( $field_controller->valid_field_types as $slug => $type ) : ?>
							<li>
								<a href="#" class="<?php echo esc_attr( $slug ); if ( $default_type == $slug ) : ?> current<?php endif; ?>" data-type="<?php echo esc_attr( $slug ); ?>">
									<?php echo $type['title']; ?>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>

						<?php foreach( $field_controller->valid_field_types as $slug => $type ) : ?>
						<ul class="subtypes <?php echo $slug; if ( $default_type == $slug ) : ?> current<?php endif; ?>">
							<?php foreach( $type['subtypes'] as $sub_slug => $subtype ) : ?>
							<li>
								<a href="#" class="<?php echo esc_attr( $sub_slug ); if ( $default_type == $sub_slug ) : ?> current<?php endif; ?>" data-subtype="<?php echo esc_attr( $sub_slug ); ?>">
									<?php echo $subtype['title']; ?>
								</a>
							</li>
							<?php endforeach; ?>
						</ul>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="settings">

					<div class="item">
						<label for="title">
							<?php _e( 'Label', 'restaurant-reservations' ); ?>
						</label>
						<input type="text" name="title" id="title">
					</div>

					<div class="settings-panel options">
						<div class="item">
							<label for="options-options">
								<?php _e( 'Options', 'restaurant-reservations' ); ?>
							</label>
							<div class="add">
								<input type="text" name="options" id="options-options">
								<a href="#">
									<span class="dashicons dashicons-plus-alt"></span>
									<?php _e( 'Add', 'restaurant-reservations' ); ?>
								</a>
							</div>
							<ul class="options">
							</ul>
						</div>
					</div>

					<?php do_action( 'cffrtb_field_editor_settings_panel' ); ?>

				</div>

				<div class="required">
					<label>
						<input type="checkbox" name="required" value="1">
						<?php _e( 'Required', 'restaurant-reservations' ); ?>
					</label>
				</div>

				<div class="actions">
					<a href="#" class="button-primary save">
						<?php _e( 'Add Field', 'restaurant-reservations' ); ?>
					</a>
					<a href="#" class="button cancel">
						<?php _e( 'Cancel', 'restaurant-reservations' ); ?>
					</a>
					<div class="status">
						<span class="load-spinner"></span>
					</div>
				</div>
			</form>
		</div>

		<div id="cffrtb-field-editor-option" class="rtb-admin-modal">
			<div class="rtb-container">
				<div class="option">
					<a href="#" class="field button-primary">
						<?php _e( 'Add Field', 'restaurant-reservations' ); ?>
					</a>
					<p class="description">
						<?php _e( 'Fields prompt the user to enter information or select from options.' ); ?>
					</p>
				</div>
				<div class="option">
					<a href="#" class="fieldset button">
						<?php _e( 'Add Fieldset', 'restaurant-reservations' ); ?>
					</a>
					<p class="description">
						<?php _e( 'Fieldsets group other fields under a common label.' ); ?>
					</p>
				</div>
			</div>
		</div>

		<div id="rtb-error-modal" class="rtb-admin-modal">
			<div class="rtb-error rtb-container"">
				<div class="rtb-error-msg"></div>
				<a href="#" class="button"><?php _e( 'Close', 'restaurant-reservations' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle ajax requests from logged out users
	 *
	 * @since 0.1
	 */
	public function ajax_nopriv() {

		wp_send_json_error(
			array(
				'error' => 'loggedout',
				'msg' => sprintf( __( 'You have been logged out. Please %slogin again%s.', 'restaurant-reservations' ), '<a href="' . wp_login_url( admin_url( 'admin.php?page=cffrtb-editor' ) ) . '">', '</a>' ),
			)
		);
	}

	/**
	 * Handle ajax request to save a field
	 *
	 * @since 0.1
	 */
	public function ajax_save_field() {
		global $rtb_controller;

		// Authenticate request
		if ( !check_ajax_referer( 'cffrtb-editor', 'nonce' ) || !current_user_can( 'manage_options' ) ) {
			$this->nopriv_ajax();
		}

		// Missing data
		if ( empty( $_POST['field'] ) ) {
			wp_send_json_error(
				array(
					'error' => 'no_field_data',
					'msg' => __( 'No field data was received with your request.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
				)
			);
		}

		// Missing request specification
		if ( empty( $_POST['request'] ) ) {
			wp_send_json_error(
				array(
					'error' => 'no_request',
					'msg' => __( 'Internal data that was supposed to be passed with your request was not received.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
				)
			);
		}

		$field = new cffrtbField( $_POST['field'] );

		// Just the label
		if ( $_POST['request'] == 'save_label' ) {
			$this->send_ajax_response( $field->save_label() );

		// The whole field
		} elseif ( $_POST['request'] == 'save_field' ) {
			$this->send_ajax_response( $field->save_field() );
		}

		wp_send_json_error(
			array(
				'error' => 'unknown',
				'msg' => __( 'An unknown error has occurred.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
			)
		);
	}

	/**
	 * Handle ajax request to save the order of fields and fieldsets
	 *
	 * @since 0.1
	 */
	public function ajax_save_order() {
		global $rtb_controller;

		// Authenticate request
		if ( !check_ajax_referer( 'cffrtb-editor', 'nonce' ) || !current_user_can( 'manage_options' ) ) {
			$this->nopriv_ajax();
		}

		// Missing data
		if ( empty( $_POST['order'] ) ) {
			wp_send_json_error(
				array(
					'error' => 'no_fields_data',
					'msg' => __( 'No fields data was received with your request.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
				)
			);
		}

		$modified = get_option( $rtb_controller->custom_fields->modified_option_key );
		$orig_modified = $modified;

		$custom_fields_error = array();
		$has_custom_fields = false;
		foreach( $_POST['order'] as $field ) {

			if ( !isset( $field['order'] ) || empty( $field['fieldset'] ) ) {
				continue; // @todo this indicates some kind of data error, though
			}

			// Default fields
			if ( empty( $field['ID'] ) ) {

				if ( empty( $modified[ $field['slug'] ] ) ) {
					$modified[ $field['slug'] ] = array();
				}

				// Skip if the order isn't changing
				if ( isset( $modified[ $field['slug'] ]['order'] ) && $modified[ $field['slug'] ]['order'] == $field['order'] && isset( $modified[ $field['slug'] ]['fieldset'] ) && $modified[ $field['slug'] ]['fieldset'] == $field['fieldset'] ) {
					continue;
				}

				$modified[ $field['slug'] ]['order'] = (int) $field['order'];
				$modified[ $field['slug'] ]['fieldset'] = sanitize_key( $field['fieldset'] );

			// Custom fields
			} else {

				$custom_field = new cffrtbField( $field );
				$result = $custom_field->save_field();

				if ( !$result[0] ) {
					array_push( $custom_fields_error, $result[1] );
				}

				$has_custom_fields = true;
			}
		}

		update_option( $rtb_controller->custom_fields->modified_option_key, $modified );

		if ( !$has_custom_fields || ( $has_custom_fields && empty( $custom_fields_error ) ) ) {
			wp_send_json_success();

		} else {
			wp_send_json_error(
				array(
					'error' 	=> 'save_order_failed',
					'msg' 		=> __( 'An error occurred while saving the new field order. Please try again.', 'restaurant-reservations' ),
					'fields'	=> $_POST['order'],
					'custom_fields_error'	=> $custom_fields_error,
				)
			);
		}

		wp_send_json_error(
			array(
				'error' => 'unknown',
				'msg' => __( 'An unknown error has occurred.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
			)
		);
	}

	/**
	 * Handle ajax request to load a field
	 *
	 * @since 0.1
	 */
	public function ajax_load_field() {
		global $rtb_controller;

		// Authenticate request
		if ( !check_ajax_referer( 'cffrtb-editor', 'nonce' ) || !current_user_can( 'manage_options' ) ) {
			$this->nopriv_ajax();
		}

		// Missing data
		if ( empty( $_POST['ID'] ) ) {
			wp_send_json_error(
				array(
					'error' => 'no_id',
					'msg' => __( 'The requested field could not be loaded because no ID was received with your request.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
				)
			);
		}

		$field = new cffrtbField( array( 'ID' => (int) $_POST['ID'] ) );

		wp_send_json_success(
			array(
				'field'	=> $field,
			)
		);
	}

	/**
	 * Handle ajax request to delete or disable a field
	 *
	 * @since 0.1
	 */
	public function ajax_delete_field() {
		global $rtb_controller;

		// Authenticate request
		if ( !check_ajax_referer( 'cffrtb-editor', 'nonce' ) || !current_user_can( 'manage_options' ) ) {
			$this->nopriv_ajax();
		}

		// Missing data
		if ( empty( $_POST['ID'] ) && ( empty( $_POST['slug'] ) || empty( $_POST['fieldset'] ) ) ) {
			wp_send_json_error(
				array(
					'error' => 'no_id',
					'msg' => __( 'The requested field could not be deleted because no ID or slug was received with your request.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
				)
			);
		}

		if ( !empty( $_POST['ID'] ) ) {
			$post = wp_delete_post( (int) $_POST['ID'] );

			if ( !$post ) {
				wp_send_json_error(
					array(
						'error' => 'delete_field_failed',
						'msg' => __( 'An error occurred while deleting this field.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
					)
				);
			}

			wp_send_json_success();

		} else {
			$modified = get_option( $rtb_controller->custom_fields->modified_option_key );
			$slug = sanitize_key( $_POST['slug'] );
			$fieldset = sanitize_key( $_POST['fieldset'] );
			$modified[ $slug ]['disabled'] = 1;
			$modified[ $slug ]['fieldset'] = $fieldset;
			update_option( $rtb_controller->custom_fields->modified_option_key, $modified );

			global $rtb_controller;
			$fields = $rtb_controller->settings->get_booking_form_fields();
			$field = $slug == $fieldset ? $rtb_controller->fields->default_fields[ $slug ] : $rtb_controller->fields->get_nested_field( $slug, $rtb_controller->fields->default_fields );
			$field['disabled'] = 1;

			$type = $slug == $fieldset ? 'fieldset' : 'field';

			wp_send_json_success(
				array(
					'field'	=> $this->print_field( $slug, $field, $type ),
				)
			);
		}
	}

	/**
	 * Handle an ajax request to enable a field that has been disabled
	 *
	 * @since 0.1
	 */
	public function ajax_enable_field() {
		global $rtb_controller;

		// Authenticate request
		if ( !check_ajax_referer( 'cffrtb-editor', 'nonce' ) || !current_user_can( 'manage_options' ) ) {
			$this->nopriv_ajax();
		}

		// Missing data
		if ( empty( $_POST['slug'] ) ) {
			wp_send_json_error(
				array(
					'error' => 'no_id',
					'msg' => __( 'The requested field could not be enabled because no identifying slug was received with your request.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
				)
			);
		}

		$slug = sanitize_key( $_POST['slug'] );
		$modified = get_option( $rtb_controller->custom_fields->modified_option_key );

		if ( empty( $modified ) || !is_array( $modified ) || !array_key_exists( $slug, $modified ) ) {
			wp_send_json_error(
				array(
					'error' => 'field_not_found',
					'msg' => __( 'The requested field could not be enabled because it did not appear to be disabled.', 'restaurant-reservations' ) . ' ' . $rtb_controller->custom_fields->common_error_msg
				)
			);
		}

		if ( isset( $modified[ $slug ]['disabled'] ) ) {
			unset( $modified[ $slug ]['disabled'] );
			update_option( $rtb_controller->custom_fields->modified_option_key, $modified );
		}

		global $rtb_controller;
		$fields = $rtb_controller->settings->get_booking_form_fields();
		$field = $_POST['type'] == 'fieldset' ? $rtb_controller->fields->default_fields[ $slug ] : $rtb_controller->fields->get_nested_field( $slug, $fields );

		$type = $_POST['type'] == 'fieldset' ? 'fieldset' : 'field';

		if ( $type == 'fieldset' ) {
			$field['exclude_fields'] = true;
		}

		wp_send_json_success(
			array(
				'field'	=> $this->print_field( $slug, $field, $type )
			)
		);
	}

	/**
	 * Handle an ajax request to reset all changes and delete custom
	 * fields
	 *
	 * @since 0.1
	 */
	public function ajax_reset_all( $authorized = false) {
		global $rtb_controller;

		// Authenticate request
		if ( ( !check_ajax_referer( 'cffrtb-editor', 'nonce' ) || !current_user_can( 'manage_options' ) ) and ! $authorized ) {
			$this->nopriv_ajax();
		}

		// Delete modifications to default fields
		delete_option( $rtb_controller->custom_fields->modified_option_key );

		// Delete all custom posts
		$posts = new WP_Query(
			array(
				'post_type'	=> 'cffrtb_field',
				'posts_per_page' => 1000, // Very large upper limit
			)
		);

		while( $posts->have_posts() ) {
			$posts->the_post();
			wp_delete_post( get_the_ID() );
		}

		wp_send_json_success();
	}

	/**
	 * Send an ajax response
	 *
	 * This is a generic response sender which will search for an error
	 * in the response and make the appropriate wp_send_json_*() call.
	 *
	 * @since 0.1
	 */
	public function send_ajax_response( $response = array() ) {

		$response[1] = empty( $response[1] ) ? '' : $response[1];

		if ( !empty( $response[0] ) ) {
			wp_send_json_success( $response[1] );

		} else {
			wp_send_json_error( $response[1] );
		}

	}

}
} // endif;
