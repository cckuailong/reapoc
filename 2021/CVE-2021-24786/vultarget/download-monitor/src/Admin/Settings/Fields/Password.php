<?php

class DLM_Admin_Fields_Field_Password extends DLM_Admin_Fields_Field {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<input id="setting-<?php esc_attr_e( $this->get_name() ); ?>" class="regular-text" type="password"
		       name="<?php esc_attr_e( $this->get_name() ); ?>"
		       value="<?php esc_attr_e( $this->get_value() ); ?>" <?php $this->e_placeholder(); ?> />
		<?php
	}

}