<?php

/**
 * Register and display a table for saving the order of a list of items.
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'items'		=> array(
 *		   'item' => 'Label' // The items to be re-ordered
 *		) 
 * );
 *
 * @since 2.5
 * @package Simple Admin Pages
 */

class sapAdminPageSettingOrdering_2_6_3 extends sapAdminPageSetting_2_6_3 {

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Add in the JS requried for the values to be stored
	 * @since 2.5
	 */
	public $scripts = array(
		'sap-ordering-table' => array(
			'path'			=> 'js/ordering.js',
			'dependencies'	=> array( 'jquery' ),
			'version'		=> SAP_VERSION,
			'footer'		=> true,
		),
	);

	/**
	 * Add in the CSS requried for rows to be displayed correctly
	 * @since 2.5
	 */
	public $styles = array(
		'sap-ordering-table' => array(
			'path'			=> 'css/ordering.css',
			'dependencies'	=> array( ),
			'version'		=> SAP_VERSION,
			'media'			=> 'all',
		),
	);

	/**
	 * Display this setting
	 * @since 2.0
	 */
	public function display_setting() {

		$input_name = $this->get_input_name();
		$values = is_array( $this->value ) ? $this->value : json_decode( html_entity_decode( $this->value ), true );

		if ( ! is_array( $values ) )
			$values = array();

		if ( empty( $values ) and is_string( $this->items ) ) 
			$values = array_merge(  $values, json_decode( $this->items, true ) );
		
		?>
		
		<fieldset <?php $this->print_conditional_data(); ?>>
			<div class='sap-ordering-table <?php echo ( $this->disabled ? 'disabled' : ''); ?>'>
				<input type='hidden' id="sap-ordering-table-main-input" name='<?php echo esc_attr( $input_name ); ?>' value='<?php echo esc_attr( json_encode( $values ) ); ?>' />
				<table>
					<tbody>
						<?php foreach ( $values as $value => $label ) { ?>
							<tr class='sap-ordering-table-row'>
								<td>
									<input type='hidden' value='<?php echo esc_attr( $value ); ?>' />
									<span><?php echo esc_html( $label ); ?></span>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>

			<?php $this->display_disabled(); ?>
		</fieldset>

		<?php

		$this->display_description();

	}

}
