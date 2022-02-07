<?php

class DLM_Admin_Fields_Field_Textarea extends DLM_Admin_Fields_Field {

	/**
	 * Renders field
	 */
	public function render() {
		?>
		<textarea id="setting-<?php esc_attr_e( $this->get_name() ); ?>" class="large-text" cols="50" rows="3"
		          name="<?php esc_attr_e( $this->get_name() ); ?>" <?php $this->e_placeholder(); ?>><?php echo esc_textarea( $this->get_value() ); ?></textarea>
		<?php
	}

}