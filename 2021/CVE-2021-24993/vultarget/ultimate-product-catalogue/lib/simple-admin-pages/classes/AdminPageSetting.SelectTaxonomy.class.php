<?php

/**
 * Register, display and save a selection with a drop-down list of any taxonomy
 *
 * This setting accepts the following arguments in its constructor function.
 *
 * $args = array(
 *		'id'			=> 'setting_id', 	// Unique id
 *		'title'			=> 'My Setting', 	// Title or label for the setting
 *		'description'	=> 'Description', 	// Help text description
 *		'taxonomies'	=> array();			// Array of taxonomies to fetch (required)
 *		'blank_option'	=> true, 			// Whether or not to show a blank option
 *		'args'			=> array();			// Arguments to pass to WordPress's get_terms() function
 * );
 * type
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingSelectTaxonomy_2_6_1 extends sapAdminPageSetting_2_6_1 {

	public $sanitize_callback = 'intval';

	// Whether or not to display a blank option
	public $blank_option = true;

	// Arrays of taxonomies to fetch (required)
	public $taxonomies;

	/**
	 * Array of options accepted by get_terms()
	 * See: http://codex.wordpress.org/Function_Reference/get_terms
	 */
	public $args = array();

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {

		$terms = get_terms( $this->taxonomies, $this->args );

		?>

		<fieldset <?php $this->print_conditional_data(); ?>>

			<select name="<?php echo $this->get_input_name(); ?>" id="<?php echo $this->get_input_name(); ?>" <?php echo ( $this->disabled ? 'disabled' : ''); ?>>

				<?php if ( $this->blank_option === true ) : ?>
					<option></option>
				<?php endif; ?>

				<?php foreach ( $terms as $term  ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>"<?php if( $this->value == $term->term_id ) : ?> selected="selected"<?php endif; ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>

			</select>

			<?php $this->display_disabled(); ?>	

		</fieldset>

		<?php

		$this->display_description();

	}

}
