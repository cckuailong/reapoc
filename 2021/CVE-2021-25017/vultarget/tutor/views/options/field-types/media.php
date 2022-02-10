<?php
$value = (int) $this->get($field['field_key']);

?>

<div class="option-media-wrap">
    <div class="option-media-preview">
	    <?php
	    if ($value){
		    ?>
            <img src="<?php echo esc_url( wp_get_attachment_url($value) ); ?>" />
		    <?php
	    }
	    ?>
    </div>

    <input type="hidden" name="tutor_option[<?php esc_attr_e( $field['field_key'] ); ?>]" value="<?php esc_attr_e( $value ); ?>">
    <div class="option-media-type-btn-wrap">
        <button class="tutor-button tutor-button-primary tutor-option-media-upload-btn">
            <i class="dashicons dashicons-upload"></i>
		    <?php
		    $btn_text = tutils()->array_get('btn_text', $field);
		    if ( ! $btn_text){
			    $btn_text = $field['label'];
		    }
		    esc_html_e( $btn_text ); ?>
        </button>

        <button class="tutor-button button-danger tutor-media-option-trash-btn" style="display: <?php echo $value ? '' : 'none'; ?>;"><i class="tutor-icon-garbage"></i> <?php esc_html_e('Delete', 'tutor');
        ?></button>
    </div>
</div>