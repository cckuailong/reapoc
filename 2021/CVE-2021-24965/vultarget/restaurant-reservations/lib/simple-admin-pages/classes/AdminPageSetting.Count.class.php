<?php

/**
 * Register, display and save a count/unit option with drop-down menus.
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'blank_option'	=> true, 			// Whether or not to show a blank option
 *		'min_value'		=> 0, 				// The lowest value to include
 *		'max_value'		=> 10, 				// The highest value to include
 *		'increment'		=> 1,				// How many values to increase by each loop
 *		'units'			=> array(			// An array of key/value pairs which
 *			'unit_one'	=> 'Unit 1',		// Define the units.
 *			'unit_two'	=> 'Unit 2',
 *			...
 *		);
 * );
 *
 * @since 2.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingCount_2_6_1 extends sapAdminPageSetting_2_6_1 {

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Add in the JS requried for rows to be added and the values to be stored
	 * @since 2.0
	 */
	public $scripts = array(
		'sap-count' => array(
			'path'			=> 'js/count.js',
			'dependencies'	=> array( 'jquery' ),
			'version'		=> SAP_VERSION,
			'footer'		=> true,
		),
	);

	// Whether or not to display a blank option
	public $blank_option = true;

	// The default value for the field when none has been set
	public $default;

	// The lowest value to include
	public $min_value = 0;
	
	// The lowest value to include
	public $max_value = 10;

	// The lowest value to include
	public $increment = 1;

	// An array of options for this select field, accepted as a key/value pair.
	public $units = array();

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {

		$this->value = $this->value ? $this->value : $this->default;

		$count = strpos( $this->value, '_' ) !== false ? substr( $this->value, 0, strpos( $this->value, '_' ) ) : $this->value; 
		$unit = strpos( $this->value, '_' ) !== false ? substr( $this->value, strpos( $this->value, '_' ) + 1 ) : '';
		
		?>

			<fieldset <?php $this->print_conditional_data(); ?>>
				<input id='<?php echo $this->id; ?>' type='hidden' name='<?php echo $this->get_input_name(); ?>' value='<?php echo $this->value; ?>' />
				<select id="<?php echo $this->id; ?>_count" <?php echo ( $this->disabled ? 'disabled' : ''); ?> class='sap-count-count' data-id='<?php echo $this->id; ?>'>

					<?php if ( $this->blank_option === true ) : ?>
						<option></option>
					<?php endif; ?>

					<?php for ( $i = $this->min_value; $i <= $this->max_value; $i = $i + $this->increment ) : ?>
						<option value="<?php echo $i; ?>"<?php if( $count == $i ) : ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
					<?php endfor; ?>

				</select>

				<?php if ( ! empty($this->units) ) { ?>

					<?php if ( sizeof( $this->units ) == 1 ) { ?>
						<input type='hidden' id='<?php echo $this->id; ?>_unit' data-id='<?php echo $this->id; ?>' /><span><?php echo esc_html( reset( $this->units ) ); ?></span>
					<?php } else { ?>
						<select id='<?php echo $this->id; ?>_unit' <?php echo ( $this->disabled ? 'disabled' : ''); ?> class='sap-count-unit' data-id='<?php echo $this->id; ?>'>
	
							<?php if ( $this->blank_option === true ) : ?>
								<option></option>
							<?php endif; ?>
	
							<?php foreach ( $this->units as $id => $title  ) : ?>
								<option value='<?php echo esc_attr( $id ); ?>' <?php if( $unit == $id ) : ?> selected="selected"<?php endif; ?>><?php echo esc_html( $title ); ?></option>
							<?php endforeach; ?>
	
						</select>
					<?php } ?>

				<?php } ?>
				<?php $this->display_disabled(); ?>		
			</fieldset>

		<?php

		$this->display_description();

	}

}
