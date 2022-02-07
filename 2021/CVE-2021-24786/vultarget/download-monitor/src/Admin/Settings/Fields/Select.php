<?php

class DLM_Admin_Fields_Field_Select extends DLM_Admin_Fields_Field {

	/** @var array */
	private $options;

	/**
	 * DLM_Admin_Fields_Field constructor.
	 *
	 * @param String $name
	 * @param String $value
	 * @param array $options
	 */
	public function __construct( $name, $value, $options ) {
		$this->options = $options;
		parent::__construct( $name, $value, '' );
	}

	/**
	 * @return array
	 */
	public function get_options() {
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function set_options( $options ) {
		$this->options = $options;
	}

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<select id="setting-<?php esc_attr_e( $this->get_name() ); ?>" class="regular-text"
		        name="<?php esc_attr_e( $this->get_name() ); ?>"><?php
			foreach ( $this->get_options() as $key => $name ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $this->get_value(), $key, false ) . '>' . esc_html( $name ) . '</option>';
			}
			?></select>
		<?php
	}

}