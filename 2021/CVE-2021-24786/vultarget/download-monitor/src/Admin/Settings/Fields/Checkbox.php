<?php

class DLM_Admin_Fields_Field_Checkbox extends DLM_Admin_Fields_Field {

	/** @var String */
	private $cb_label;

	/**
	 * DLM_Admin_Fields_Field_Checkbox constructor.
	 *
	 * @param String $name
	 * @param String $value
	 * @param String $cb_label
	 */
	public function __construct( $name, $value, $cb_label ) {
		$this->cb_label = $cb_label;
		parent::__construct( $name, $value, '' );
	}


	/**
	 * @return String
	 */
	public function get_cb_label() {
		return $this->cb_label;
	}

	/**
	 * @param String $cb_label
	 */
	public function set_cb_label( $cb_label ) {
		$this->cb_label = $cb_label;
	}


	/**
	 * Renders field
	 */
	public function render() {
		?>
		<label><input id="setting-<?php esc_attr_e( $this->get_name() ); ?>"
		              name="<?php esc_attr_e( $this->get_name() ); ?>" type="checkbox"
		              value="1" <?php checked( '1', $this->get_value() ); ?> /> <?php echo $this->get_cb_label(); ?>
		</label>
		<?php
	}

}