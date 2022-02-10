<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
?>

<div class="rtec-form-field rtec-<?php echo esc_attr( $field_name ); ?> rtec-field-<?php echo esc_attr( $field_settings['type'] ); ?>" data-rtec-error-message="<?php echo esc_attr( $field_settings['error_message'] ); ?>" data-rtec-type="<?php echo esc_attr( $field_settings['type'] ); ?>">
    <?php if ( $field_name !== 'terms_conditions' ) : ?>
	<label for="<?php echo esc_attr( $field_settings['field_name'] ); ?>" class="rtec-field-label<?php echo esc_attr( $label_classes ); ?>" aria-label="<?php echo esc_attr( stripslashes( $field_settings['label'] ) ); ?>"><?php echo esc_html( stripslashes( $field_settings['label'] ) ); ?></label>
	<?php endif; ?>
    <div class="rtec-input-wrapper">
		<?php echo $this->get_input_html_for_field_type( $field_settings ); ?>
		<?php echo $field_settings['html']; ?>
		<?php if ( $field_settings['error'] ) : ?>
		<p class="rtec-error-message" role="alert"><?php echo esc_html( $field_settings['error_message'] ); ?></p>
		<?php endif; ?>
	</div>
</div>
