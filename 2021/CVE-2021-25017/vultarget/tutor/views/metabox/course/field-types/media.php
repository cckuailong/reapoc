<?php
$value = (int) $this->get($field['field_key']);
?>


<div class="option-media-wrap">
    <div class="option-media-preview">
        <?php
        if ($value){
            ?>
            <img src="<?php echo wp_get_attachment_url($value); ?>" />
	        <?php
        }
        ?>
    </div>

    <input type="hidden" name="_tutor_course_settings[<?php echo $field['field_key']; ?>]" value="<?php echo $value; ?>">
    <button class="button button-cancel tutor-option-media-upload-btn">
        <i class="dashicons dashicons-upload"></i>
        <?php echo $field['label']; ?>
    </button>
</div>

