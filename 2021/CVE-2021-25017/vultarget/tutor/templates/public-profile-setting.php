<?php
if ( ! defined( 'ABSPATH' ) )
	exit;
?>

<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for=""><?php _e('Public Profile Layout', 'tutor'); ?></label>
    </div>
    <div class="tutor-option-field">
        <div class="instructor-layout-templates-fields">
			<?php
                $url_base = tutor()->url.'assets/images/public-profile/';

				foreach ($profile_templates as $template){
                    $img = $url_base.$template.'.jpg';
				    $selected_template = tutor_utils()->get_option($layout_option_name.'_public_profile_layout');
					?>
                    <label class="instructor-layout-template <?php echo ($template === $selected_template) ? 'selected-template' : '' ?> ">
                        <img src="<?php echo $img; ?>" />
                        <input type="radio" name="tutor_option[<?php echo $layout_option_name; ?>_public_profile_layout]" value="<?php echo $template; ?>" <?php checked($template, $selected_template) ?> style="display: none;" >
                    </label>
					<?php
				}
			?>
        </div>
        <p class="desc">
            <?php _e('Selected one will be used as public profile layout.', 'tutor'); ?>
        </p>
    </div>
</div>

