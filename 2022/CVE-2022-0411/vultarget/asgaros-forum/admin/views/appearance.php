<?php

if (!defined('ABSPATH')) exit;

?>
<div class="wrap" id="af-options">
    <?php
    $title = __('Appearance', 'asgaros-forum');
    $titleUpdated = __('Appearance updated.', 'asgaros-forum');
    $this->render_admin_header($title, $titleUpdated);
    ?>

    <form method="post">
        <?php wp_nonce_field('asgaros_forum_save_appearance'); ?>

        <div class="settings-box">
            <div class="settings-header">
                <span class="fas fa-paint-brush"></span>
                <?php esc_html_e('Appearance', 'asgaros-forum'); ?>
            </div>
            <table>
                <?php
                $themes = $this->asgarosforum->appearance->get_themes();
                if (count($themes) > 1) {
				?>
                    <tr>
                        <th><label for="theme"><?php esc_html_e('Theme', 'asgaros-forum'); ?>:</label></th>
                        <td>
                            <select name="theme" id="theme">
                                <?php
								foreach ($themes as $k => $v) {
                                    echo '<option value="'.esc_attr($k).'" '.selected($k, $this->asgarosforum->appearance->get_current_theme(), false).'>'.esc_html($v['name']).'</option>';
                                }
								?>
                            </select>
                        </td>
                    </tr>
                <?php
                }
                $themesOption = $this->asgarosforum->appearance->is_default_theme();
                ?>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_font"><?php esc_html_e('Font:', 'asgaros-forum'); ?></label></th>
                    <td><input class="regular-text" type="text" name="custom_font" id="custom_font" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_font'])); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_font_size"><?php esc_html_e('Font size:', 'asgaros-forum'); ?></label></th>
                    <td><input class="regular-text" type="text" name="custom_font_size" id="custom_font_size" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_font_size'])); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_color"><?php esc_html_e('Forum color:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_color'])); ?>" class="color-picker" name="custom_color" id="custom_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_accent_color"><?php esc_html_e('Accent color:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_accent_color'])); ?>" class="color-picker" name="custom_accent_color" id="custom_accent_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_accent_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_text_color"><?php esc_html_e('Text color:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_text_color'])); ?>" class="color-picker" name="custom_text_color" id="custom_text_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_text_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_text_color_light"><?php esc_html_e('Text color light:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_text_color_light'])); ?>" class="color-picker" name="custom_text_color_light" id="custom_text_color_light" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_text_color_light']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_link_color"><?php esc_html_e('Link color:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_link_color'])); ?>" class="color-picker" name="custom_link_color" id="custom_link_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_link_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_background_color"><?php esc_html_e('Background color (First):', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_background_color'])); ?>" class="color-picker" name="custom_background_color" id="custom_background_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_background_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_background_color_alt"><?php esc_html_e('Background color (Second):', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_background_color_alt'])); ?>" class="color-picker" name="custom_background_color_alt" id="custom_background_color_alt" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_background_color_alt']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_border_color"><?php esc_html_e('Border color:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_border_color'])); ?>" class="color-picker" name="custom_border_color" id="custom_border_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_border_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_read_indicator_color"><?php esc_html_e('Read indicator color:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_read_indicator_color'])); ?>" class="color-picker" name="custom_read_indicator_color" id="custom_read_indicator_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_read_indicator_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_unread_indicator_color"><?php esc_html_e('Unread indicator color:', 'asgaros-forum'); ?></label></th>
                    <td><input type="text" value="<?php echo esc_attr(stripslashes($this->asgarosforum->appearance->options['custom_unread_indicator_color'])); ?>" class="color-picker" name="custom_unread_indicator_color" id="custom_unread_indicator_color" data-default-color="<?php echo esc_attr($this->asgarosforum->appearance->options_default['custom_unread_indicator_color']); ?>"></td>
                </tr>
                <tr class="custom-color-selector" <?php if (!$themesOption) { echo 'style="display: none;"'; } ?>>
                    <th><label for="custom_css"><?php esc_html_e('Custom CSS:', 'asgaros-forum'); ?></label></th>
                    <td><textarea class="large-text" data-code-editor-mode="css" rows="8" cols="80" type="text" name="custom_css" id="custom_css"><?php echo esc_html(stripslashes($this->asgarosforum->appearance->options['custom_css'])); ?></textarea></td>
                </tr>
            </table>
        </div>

        <input type="submit" name="af_appearance_submit" class="button button-primary" value="<?php esc_attr_e('Save Appearance', 'asgaros-forum'); ?>">
    </form>
</div>
