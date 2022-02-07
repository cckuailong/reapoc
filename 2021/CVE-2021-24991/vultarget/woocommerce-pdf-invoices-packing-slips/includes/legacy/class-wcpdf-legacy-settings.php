<?php
namespace WPO\WC\PDF_Invoices\Legacy;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !class_exists( '\\WPO\\WC\\PDF_Invoices\\Legacy\\Legacy_Settings' ) ) :

class Legacy_Settings {

	public $general_settings;
	public $template_settings;
	public $debug_settings;

	public $options_page_hook = 'woocommerce_page_wpo_wcpdf_options_page';

	public function __construct() {
		$this->load_legacy_settings();
	}

	public function load_legacy_settings() {
		// map new settings to old
		$settings_map = array(
			'wpo_wcpdf_settings_general' => array(
				'download_display'			=> array( 'wpo_wcpdf_general_settings' => 'download_display' ),
				'template_path'				=> array( 'wpo_wcpdf_template_settings' => 'template_path' ),
				'currency_font'				=> array( 'wpo_wcpdf_template_settings' => 'currency_font' ),
				'paper_size'				=> array( 'wpo_wcpdf_template_settings' => 'paper_size' ),
				'header_logo'				=> array( 'wpo_wcpdf_template_settings' => 'header_logo' ),
				'shop_name'					=> array( 'wpo_wcpdf_template_settings' => 'shop_name' ),
				'shop_address'				=> array( 'wpo_wcpdf_template_settings' => 'shop_address' ),
				'footer'					=> array( 'wpo_wcpdf_template_settings' => 'footer' ),
				'extra_1'					=> array( 'wpo_wcpdf_template_settings' => 'extra_1' ),
				'extra_2'					=> array( 'wpo_wcpdf_template_settings' => 'extra_2' ),
				'extra_3'					=> array( 'wpo_wcpdf_template_settings' => 'extra_3' ),
			),
			'wpo_wcpdf_documents_settings_invoice' => array(
				'attach_to_email_ids'		=> array( 'wpo_wcpdf_general_settings' => 'email_pdf' ),
				'display_shipping_address'	=> array( 'wpo_wcpdf_template_settings' => 'invoice_shipping_address' ),
				'display_email'				=> array( 'wpo_wcpdf_template_settings' => 'invoice_email' ),
				'display_phone'				=> array( 'wpo_wcpdf_template_settings' => 'invoice_phone' ),
				'display_date'				=> array( 'wpo_wcpdf_template_settings' => 'display_date' ),
				'display_number'			=> array( 'wpo_wcpdf_template_settings' => 'display_number' ),
				'number_format'				=> array( 'wpo_wcpdf_template_settings' => 'invoice_number_formatting' ),
				'reset_number_yearly'		=> array( 'wpo_wcpdf_template_settings' => 'yearly_reset_invoice_number' ),
				'my_account_buttons'		=> array( 'wpo_wcpdf_general_settings' => 'my_account_buttons' ),
				'invoice_number_column'		=> array( 'wpo_wcpdf_general_settings' => 'invoice_number_column' ),
				'disable_free'				=> array( 'wpo_wcpdf_general_settings' => 'disable_free' ),
			),
			'wpo_wcpdf_documents_settings_packing-slip' => array(
				'display_billing_address'	=> array( 'wpo_wcpdf_template_settings' => 'packing_slip_billing_address' ),
				'display_email'				=> array( 'wpo_wcpdf_template_settings' => 'packing_slip_email' ),
				'display_phone'				=> array( 'wpo_wcpdf_template_settings' => 'packing_slip_phone' ),
			),
			'wpo_wcpdf_settings_debug' => array(
				'enable_debug'				=> array( 'wpo_wcpdf_debug_settings' => 'enable_debug' ),
				'html_output'				=> array( 'wpo_wcpdf_debug_settings' => 'html_output' ),
			),
		);

		// walk through map and load into legacy properties
		foreach ($settings_map as $new_option => $new_settings_keys) {
			${$new_option} = get_option($new_option);
			foreach ($new_settings_keys as $new_key => $old_setting ) {
				$old_key = reset($old_setting);
				$old_option = key($old_setting);
				if (isset(${$new_option}[$new_key])) {
					$property_name = str_replace( 'wpo_wcpdf_', '', $old_option );
					$this->{$property_name}[$old_key] = ${$new_option}[$new_key];
				}
			}
		}
	}

	/**
	 * Redirect settings API callbacks
	 */
	public function __call( $name, $arguments ) {
		WPO_WCPDF_Legacy()->auto_enable_check( '$wpo_wcpdf->settings->'.$name.'()', false );
		$callback_map = array(
			'text_element_callback'					=> 'text_input',
			'singular_text_element_callback'		=> 'singular_text_element',
			'textarea_element_callback'				=> 'textarea',
			'checkbox_element_callback'				=> 'checkbox',
			'multiple_checkbox_element_callback'	=> 'multiple_checkboxes',
			// 'checkbox_table_callback'				=> 'textarea',
			'select_element_callback'				=> 'select',
			'radio_element_callback'				=> 'radio_button',
			'media_upload_callback'					=> 'media_upload',
			// 'invoice_number_formatting_callback'	=> 'textarea',
			// 'template_select_element_callback'		=> 'textarea',
			'section_options_callback'				=> 'section',
			'debug_section'							=> 'debug_section',
			'custom_fields_section'					=> 'custom_fields_section',
			'validate_options'						=> 'validate',
		);

		if ( array_key_exists( $name, $callback_map ) && is_object( WPO_WCPDF()->settings->callbacks ) && is_callable( array( WPO_WCPDF()->settings->callbacks, $callback_map[$name] ) ) ) {
			if (isset($arguments[0]['menu'])) {
				$arguments[0]['option_name'] = $arguments[0]['menu'];
			}
			return call_user_func_array( array( WPO_WCPDF()->settings->callbacks, $callback_map[$name] ), $arguments );
		} else {
			throw new \Exception("Call to undefined method ".__CLASS__."::{$name}()", 1);
		}
	}

	/**
	 * Invoice number formatting callback.
	 *
	 * @param  array $args Field arguments.
	 *
	 * @return string	  Media upload button & preview.
	 */
	public function invoice_number_formatting_callback( $args ) {
		$menu = $args['menu'];
		$fields = $args['fields'];
		$options = get_option( $menu );

		echo '<table>';
		foreach ($fields as $key => $field) {
			$id = $args['id'] . '_' . $key;

			if ( isset( $options[$id] ) ) {
				$current = $options[$id];
			} else {
				$current = '';
			}

			$title = $field['title'];
			$size = $field['size'];
			$description = isset( $field['description'] ) ? '<span style="font-style:italic;">'.$field['description'].'</span>' : '';

			echo '<tr>';
			printf( '<td style="padding:0 1em 0 0; ">%1$s:</td><td style="padding:0;"><input type="text" id="%2$s" name="%3$s[%2$s]" value="%4$s" size="%5$s"/></td><td style="padding:0 0 0 1em;">%6$s</td>', $title, $id, $menu, $current, $size, $description );
			echo '</tr>';
		}
		echo '</table>';

	
		// Displays option description.
		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', $args['description'] );
		}
	
		// echo $html;
	}


}

endif; // class_exists

return new Legacy_Settings();