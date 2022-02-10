<?php

/**
 * Register, display and save a section on a custom admin menu
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSection_2_6_3 {

	// Page defaults
	public $id; // unique id for this section
	public $title; // optional title to display above this section
	public $description; // optional description of the section
	public $settings = array(); // Array of settings to display in this option set
	public $disabled = false; // whether a setting should be disabled

	// Array to store errors
	public $errors = array();

	/**
	 * Initialize the section
	 * @since 1.0
	 */
	public function __construct( $args ) {

		// Parse the values passed
		$this->parse_args( $args );

		// Set an error if there is no id for this section
		if ( !isset( $this->id ) ) {
			$this->set_error(
				array(
					'type'		=> 'missing_data',
					'data'		=> 'id'
				)
			);
		}

	}

	/**
	 * Parse the arguments passed in the construction and assign them to
	 * internal variables.
	 * @since 1.0
	 */
	private function parse_args( $args ) {
		foreach ( $args as $key => $val ) {
			switch ( $key ) {

				case 'id' :
					$this->{$key} = esc_attr( $val );

				default :
					$this->{$key} = $val;

			}
		}
	}

	/**
	 * Add a setting to this section
	 * @since 1.0
	 */
	public function add_setting( $setting ) {

		if ( !$setting ) {
			return;
		}

		if ( $this->disabled ) {
			$setting->disabled = true;
		}

		if ( method_exists( $setting, 'has_position' ) && $setting->has_position() ) {

			// Top
			if ( $setting->position[0] == 'top' ) {
				$this->settings = array_merge( array( $setting->id => $setting ), $this->settings );
				return;
			}

			// Position setting relative to another setting
			if ( !empty( $setting->position[1] ) ) {

				$new_settings = array();
				foreach( $this->settings as $id => $current ) {

					// Above
					if ( $setting->position[1] == $id && $setting->position[0] == 'above' ) {
						$new_settings[ $setting->id ] = $setting;
					}

					$new_settings[ $id ] = $current;

					// Below
					if ( $setting->position[1] == $id && $setting->position[0] == 'below' ) {
						$new_settings[ $setting->id ] = $setting;
					}
				}

				$this->settings = $new_settings;

				return;
			}
		}

		// Fallback to appending it at the end
		$this->settings[ $setting->id ] = $setting;
	}

	/**
	 * Display the description for this section
	 * @since 1.0
	 */
	public function display_section() {

		if ( !count( $this->settings ) ) {
			return;
		}

		if ( !empty( $this->description ) ) :
		?>

			<p class="description"><?php echo esc_html( $this->description ); ?></p>

		<?php
		endif;

		if ( $this->disabled and isset($this->disabled_image) ) {

		?>

			<?php echo ( isset($this->purchase_link ) ? "<div class='sap-premium-options-table-overlay'>" : '' ); ?>
				<div class="section-disabled">
					<img src="<?php echo plugins_url( '../img/options-asset-lock.png', __FILE__ ); ?>" alt="Upgrade to Premium">
					<p>Access this section by upgrading to premium</p>
					<a href="<?php echo esc_url( $this->purchase_link ); ?>" class="sap-dashboard-get-premium-widget-button" target="_blank">UPGRADE NOW</a>
				</div>
			<?php echo ( isset( $this->purchase_link ) ? "</div>" : '' ); ?>

		<?php

		}
	}

	/**
	 * Add the settings section to the page in WordPress
	 * @since 1.0
	 */
	public function add_settings_section() {

		// Callback can be overridden from outside the class.
		$callback = property_exists( $this, 'callback' ) && is_callable( $this->callback )
			? $this->callback
			: array( $this, 'display_section' );

		add_settings_section( $this->id, $this->title, $callback, $this->get_page_slug() );
	}

	/**
	 * Determine the page slug to use when calling add_settings_section.
	 *
	 * Tabs should use their own ID and settings that are attached to tabs
	 * should use that tab's ID.
	 * @since 2.0
	 */
	public function get_page_slug() {
		if ( isset( $this->is_tab ) && $this->is_tab === true ) {
			return $this->id;
		} elseif ( isset( $this->tab ) ) {
			return $this->tab;
		} else {
			return $this->page;
		}
	}

	/**
	 * Set an error
	 * @since 1.0
	 */
	public function set_error( $error ) {
		$this->errors[] = array_merge(
			$error,
			array(
				'class'		=> get_class( $this ),
				'id'		=> $this->id,
				'backtrace'	=> debug_backtrace()
			)
		);
	}

}
