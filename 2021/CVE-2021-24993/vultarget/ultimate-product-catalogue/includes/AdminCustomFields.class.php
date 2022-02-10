<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ewdupcpAdminCustomFields' ) ) {
/**
 * Class to handle the admin custom fields page for Ultimate Product Catalog
 *
 * @since 5.0.0
 */
class ewdupcpAdminCustomFields {

	public function __construct() {

		// Add the admin menu
		add_action( 'admin_menu', array( $this, 'add_menu_page' ), 12 );

		// Enqueue admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 12 );
	}

	/**
	 * Add the top-level admin menu page
	 * @since 5.0.0
	 */
	public function add_menu_page() {
		global $ewd_upcp_controller;

		add_submenu_page( 
			'edit.php?post_type=upcp_product', 
			_x( 'Custom Fields', 'Title of admin page that lets you view and edit all custom fields', 'ultimate-product-catalogue' ),
			_x( 'Custom Fields', 'Title of the custom fields admin menu item', 'ultimate-product-catalogue' ), 
			$ewd_upcp_controller->settings->get_setting( 'access-role' ), 
			'ewd-upcp-custom-fields', 
			array( $this, 'show_admin_custom_fields_page' )
		);
	}

	/**
	 * Display the admin custom fields page
	 * @since 5.0.0
	 */
	public function show_admin_custom_fields_page() {
		global $ewd_upcp_controller;

		$custom_fields_permission = $ewd_upcp_controller->permissions->check_permission( 'custom_fields' );

		if ( empty( $custom_fields_permission ) ) { ?> 

			<div class='ewd-upcp-premium-locked'>
				<a href="https://www.etoilewebdesign.com/license-payment/?Selected=UPCP&Quantity=1" target="_blank">Upgrade</a> to the premium version to use this feature
			</div>

			<?php 

			return;

		}

		if ( ! empty( $_POST['ewd-upcp-custom-fields-submit'] ) ) {

			$this->save_custom_fields();
		}

		$custom_fields = get_option( 'ewd-upcp-custom-fields' );

		$custom_fields[] = (object) array(
			'id'					=> 0,
			'name'					=> '',
			'slug'					=> '',
			'type'					=> '',
			'options'				=> '',
			'displays'				=> array(),
			'searchable'			=> '',
			'filter_control_type'	=> '',
			'tabbed_display'		=> '',
			'comparison_display'	=> '',
			'woocommerce_id'		=> 0,
		);

		?>

		<div class="wrap">
			<h1>
				<?php _e( 'Custom Fields', 'ultimate-product-catalogue' ); ?>
			</h1>

			<?php do_action( 'ewd_upcp_custom_fields_table_top' ); ?>

			<form id="ewd-upcp-custom-fields-table" method="POST" action="">

				<div id='ewd-upcp-custom-fields-table-div'>

					<input type='hidden' name='ewd-upcp-custom-field-save-values' />

					<div class='ewd-upcp-custom-field-heading-row'>
						<div class='ewd-upcp-custom-field-heading-cell'><?php _e( 'Name', 'ultimate-product-catalogue' ); ?></div>
						<div class='ewd-upcp-custom-field-heading-cell'><?php _e( 'Slug', 'ultimate-product-catalogue' ); ?></div>
						<div class='ewd-upcp-custom-field-heading-cell'><?php _e( 'Type', 'ultimate-product-catalogue' ); ?></div>
						<div class='ewd-upcp-custom-field-heading-cell'><?php _e( 'Input Values', 'ultimate-product-catalogue' ); ?></div>
						<div class='ewd-upcp-custom-field-heading-cell'><?php _e( 'Displays', 'ultimate-product-catalogue' ); ?></div>
						<div class='ewd-upcp-custom-field-heading-cell ewd-upcp-custom-field-cell-options'><?php _e( 'Options', 'ultimate-product-catalogue' ); ?></div>
						<div class='ewd-upcp-custom-field-heading-cell'></div>
					</div>

					<?php foreach ( $custom_fields as $custom_field ) { ?>

						<div class='ewd-upcp-custom-field <?php echo ( empty( $custom_field->id ) ? 'ewd-upcp-hidden ewd-upcp-custom-field-template' : '' ); ?>'>
							<input type='hidden' name='ewd_upcp_custom_field_id' value='<?php echo esc_attr( $custom_field->id ); ?>' />
							<input type='hidden' name='ewd_upcp_custom_field_woocommerce_id' value='<?php echo esc_attr( $custom_field->woocommerce_id ); ?>' />

							<div class='ewd-upcp-custom-field-cell'>
								<label><?php _e( 'Name', 'ultimate-product-catalogue' ); ?></label>
								<input type='text' name='ewd_upcp_custom_field_name' value='<?php echo esc_attr( $custom_field->name ); ?>' />
							</div>

							<div class='ewd-upcp-custom-field-cell'>
								<label><?php _e( 'Slug', 'ultimate-product-catalogue' ); ?></label>
								<input type='text' name='ewd_upcp_custom_field_slug' value='<?php echo esc_attr( $custom_field->slug ); ?>' />
							</div>

							<div class='ewd-upcp-custom-field-cell'>

								<label><?php _e( 'Type', 'ultimate-product-catalogue' ); ?></label>

								<select name='ewd_upcp_custom_field_type'>

									<option value='text' <?php echo ( $custom_field->type == 'text' ? 'selected' : '' ); ?>><?php _e( 'Text', 'ultimate-product-catalogue' ); ?></option>
									<option value='number' <?php echo ( $custom_field->type == 'number' ? 'selected' : '' ); ?>><?php _e( 'Number', 'ultimate-product-catalogue' ); ?></option>
									<option value='textarea' <?php echo ( $custom_field->type == 'textarea' ? 'selected' : '' ); ?>><?php _e( 'Textarea', 'ultimate-product-catalogue' ); ?></option>
									<option value='select' <?php echo ( $custom_field->type == 'select' ? 'selected' : '' ); ?>><?php _e( 'Dropdown', 'ultimate-product-catalogue' ); ?></option>
									<option value='radio' <?php echo ( $custom_field->type == 'radio' ? 'selected' : '' ); ?>><?php _e( 'Radio', 'ultimate-product-catalogue' ); ?></option>
									<option value='checkbox' <?php echo ( $custom_field->type == 'checkbox' ? 'selected' : '' ); ?>><?php _e( 'Checkbox', 'ultimate-product-catalogue' ); ?></option>
									<option value='file' <?php echo ( $custom_field->type == 'file' ? 'selected' : '' ); ?>><?php _e( 'File', 'ultimate-product-catalogue' ); ?></option>
									<option value='date' <?php echo ( $custom_field->type == 'date' ? 'selected' : '' ); ?>><?php _e( 'Date', 'ultimate-product-catalogue' ); ?></option>
									<option value='datetime' <?php echo ( $custom_field->type == 'datetime' ? 'selected' : '' ); ?>><?php _e( 'Datetime', 'ultimate-product-catalogue' ); ?></option>

								</select>

							</div>

							<div class='ewd-upcp-custom-field-cell'>
								<label><?php _e( 'Input Values', 'ultimate-product-catalogue' ); ?></label>
								<input type='text' name='ewd_upcp_custom_field_options' value='<?php echo esc_attr( $custom_field->options ); ?>' />
							</div>

							<div class='ewd-upcp-custom-field-cell'>

							<label><?php _e( 'Displays', 'ultimate-product-catalogue' ); ?></label>

								<div class="ewd-upcp-custom-field-cell-checkbox-container">
									<div class="ewd-upcp-custom-field-cell-checkbox-each"><input type='checkbox' name='ewd_upcp_custom_field_displays[]' value='thumbnail' <?php echo ( in_array( 'thumbnail', $custom_field->displays ) ? 'checked' : '' ); ?> /><?php _e( 'Thumbnail', 'ultimate-product-catalogue' ); ?></div>
									<div class="ewd-upcp-custom-field-cell-checkbox-each"><input type='checkbox' name='ewd_upcp_custom_field_displays[]' value='list' <?php echo ( in_array( 'list', $custom_field->displays ) ? 'checked' : '' ); ?> /><?php _e( 'List', 'ultimate-product-catalogue' ); ?></div>
									<div class="ewd-upcp-custom-field-cell-checkbox-each"><input type='checkbox' name='ewd_upcp_custom_field_displays[]' value='detail' <?php echo ( in_array( 'detail', $custom_field->displays ) ? 'checked' : '' ); ?> /><?php _e( 'Detail', 'ultimate-product-catalogue' ); ?></div>
								</div>

							</div>

							<div class='ewd-upcp-custom-field-cell ewd-upcp-custom-field-cell-options'>

								<label><?php _e( 'Options', 'ultimate-product-catalogue' ); ?></label>

								<div class='ewd-upcp-custom-field-cell-checkbox-container'>
									<div class="ewd-upcp-custom-field-cell-checkbox-each">
										<input type='checkbox' name='ewd_upcp_custom_field_searchable' value='1' <?php echo ( ! empty( $custom_field->searchable ) ? 'checked' : '' ); ?> /><?php _e( 'Searchable', 'ultimate-product-catalogue' ); ?>
									</div>
									<div class="ewd-upcp-custom-field-cell-checkbox-each">
										<div class="ewd-upcp-custom-field-cell-filtering-label"><?php _e( 'Filtering Control Type', 'ultimate-product-catalogue' ); ?></div>
										<select name='ewd_upcp_custom_field_filter_control_type'>
											<option value='checkbox' <?php echo ( $custom_field->filter_control_type == 'checkbox' ? 'selected' : '' ); ?>><?php _e( 'Checkbox', 'ultimate-product-catalogue' ); ?></option>
											<option value='radio' <?php echo ( $custom_field->filter_control_type == 'radio' ? 'selected' : '' ); ?>><?php _e( 'Radio', 'ultimate-product-catalogue' ); ?></option>
											<option value='dropdown' <?php echo ( $custom_field->filter_control_type == 'dropdown' ? 'selected' : '' ); ?>><?php _e( 'Dropdown', 'ultimate-product-catalogue' ); ?></option>
											<option value='slider' <?php echo ( $custom_field->filter_control_type == 'slider' ? 'selected' : '' ); ?>><?php _e( 'Slider (Number type only)', 'ultimate-product-catalogue' ); ?></option>
										</select>
									</div>
									<div class="ewd-upcp-custom-field-cell-checkbox-each">
										<input type='checkbox' name='ewd_upcp_custom_field_tabbed_display' value='1' <?php echo ( ! empty( $custom_field->tabbed_display ) ? 'checked' : '' ); ?> /><?php _e( 'Tabbed Product Page Display?', 'ultimate-product-catalogue' ); ?>
									</div>
									<div class="ewd-upcp-custom-field-cell-checkbox-each">
										<input type='checkbox' name='ewd_upcp_custom_field_comparison_display' value='1' <?php echo ( ! empty( $custom_field->comparison_display ) ? 'checked' : '' ); ?> /><?php _e( 'Product Comparison Display?', 'ultimate-product-catalogue' ); ?>
									</div>
								</div>

							</div>

							<div class='ewd-upcp-custom-field-cell ewd-upcp-custom-field-delete'>
								<?php _e( 'Delete', 'ultimate-product-catalogue' ); ?>
							</div>

						</div>

					<?php } ?>

					<div class='ewd-upcp-custom-fields-add'>
						<?php _e( '&plus;&nbsp;ADD', 'ultimate-product-catalogue' ); ?>
					</div>

				</div>

				<input type='submit' class='button button-primary' name='ewd-upcp-custom-fields-submit' value='<?php _e( 'Update Fields', 'ultimate-product-catalogue' ); ?>' />
				
			</form>
			<?php do_action( 'ewd_upcp_custom_fields_table_bottom' ); ?>
		</div>

		<?php
	}

	/**
	 * Save the custom fields when the form is submitted
	 * @since 5.0.0
	 */
	public function save_custom_fields() {

		$custom_fields = json_decode( stripslashes( sanitize_text_field( $_POST['ewd-upcp-custom-field-save-values'] ) ) );

		if ( ! empty( $custom_fields ) ) {

			update_option( 'ewd-upcp-custom-fields', $custom_fields );
		}

		do_action( 'ewd_upcp_custom_fields_updated' );
	}

	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( $screen->id == 'tracking_page_ewd-upcp-custom-fields' ) {

			wp_enqueue_style( 'ewd-upcp-admin-css', EWD_UPCP_PLUGIN_URL . '/assets/css/ewd-upcp-admin.css', array(), EWD_UPCP_VERSION );
			wp_enqueue_script( 'ewd-upcp-admin-js', EWD_UPCP_PLUGIN_URL . '/assets/js/ewd-upcp-admin.js', array( 'jquery', 'jquery-ui-sortable' ), EWD_UPCP_VERSION, true );
		}
	}
}
} // endif;
