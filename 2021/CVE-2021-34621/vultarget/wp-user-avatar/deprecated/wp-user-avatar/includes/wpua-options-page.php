<?php

global $show_avatars, $upload_size_limit_with_units, $wpua_admin, $wpua_disable_gravatar, $wpua_resize_crop, $wpua_resize_h, $wpua_resize_upload, $wpua_resize_w, $wpua_subscriber, $wpua_upload_size_limit, $wpua_cover_upload_size_limit, $wpua_upload_size_limit_with_units, $wpua_cover_upload_size_limit_with_units;
$updated = false;
if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') $updated = true;
$hide_resize = (bool)$wpua_resize_upload != 1 ? ' style="display:none;"' : "";
?>

<div class="wrap">
    <table>
        <tr valign="top">
            <td align="top">
                <?php settings_fields('wpua-settings-group'); ?>
                <?php do_settings_fields('wpua-settings-group', ""); ?>
                <table class="form-table">
                    <?php
                    echo implode("", apply_filters('wpua_before_settings', []));

                    echo '<tr valign="top">
            <th scope="row">
                <label for="wp_user_cover_upload_size_limit">' . __('Cover Image Maximum File Size', 'wp-user-avatar') . '
                </label>
            </th>
            <td>
                <fieldset>
                    <input name="wp_user_cover_upload_size_limit" type="text" id="wp_user_cover_upload_size_limit" value="' . $wpua_cover_upload_size_limit . '" class="regular-text" />
                    <span id="wpua-cover-readable-size">' . $wpua_cover_upload_size_limit_with_units . '</span>
                    <span id="wpua-cover-readable-size-error">' . sprintf(__('%s exceeds the maximum upload size for this site.', 'wp-user-avatar'), "") . '</span>
                    <div id="wpua-cover-slider"></div>
                    <span class="description">' . sprintf(__('Maximum upload file size: %d%s.', 'wp-user-avatar'), esc_html(wp_max_upload_size()), esc_html(' bytes (' . $upload_size_limit_with_units . ')')) . '</span>
                </fieldset>
            </td>
        </tr>';

                    $default_cover_image_url = get_option('wp_user_cover_default_image_url');

                    echo '<tr valign="top">
            <th scope="row">
                <label for="wp_user_cover_upload_size_limit">' . __('Default Cover Image', 'wp-user-avatar') . '
                </label>
            </th>
            <td>
                <fieldset>
                    <input name="wp_user_cover_default_image_url" type="text" id="wp_user_cover_default_image_url" value="' . $default_cover_image_url . '" class="regular-text" />
                    <br><span class="description">' . __('Please make sure that the default cover is large enough (min. 1000px in width) and respects the ratio you are using for cover images.', 'wp-user-avatar') . '</span>
                </fieldset>
            </td>
        </tr>';

                    echo '<tr valign="top">
            <th scope="row">
                <label for="wp_user_avatar_upload_size_limit">'
                         . __('Profile Picture Maximum File Size', 'wp-user-avatar') . '
                </label>
            </th>
            <td>
                <fieldset>
                    <input name="wp_user_avatar_upload_size_limit" type="text" id="wp_user_avatar_upload_size_limit" value="' . $wpua_upload_size_limit . '" class="regular-text" />
                    <span id="wpua-readable-size">' . $wpua_upload_size_limit_with_units . '</span>
                    <span id="wpua-readable-size-error">' . sprintf(__('%s exceeds the maximum upload size for this site.', 'wp-user-avatar'), "") . '</span>
                    <div id="wpua-slider"></div>
                    <span class="description">' . sprintf(__('Maximum upload file size: %d%s.', 'wp-user-avatar'), esc_html(wp_max_upload_size()), esc_html(' bytes (' . $upload_size_limit_with_units . ')')) . '</span>
                </fieldset>
                <fieldset>
                    <label for="wp_user_avatar_resize_upload">
                        <input name="wp_user_avatar_resize_upload" type="checkbox" id="wp_user_avatar_resize_upload" value="1" ' . checked($wpua_resize_upload, 1, 0) . ' />'
                         . __('Resize avatars on upload', 'wp-user-avatar') . '
                    </label>
                </fieldset>
                <fieldset id="wpua-resize-sizes"' . $hide_resize . '>
                <label for="wp_user_avatar_resize_w">' . __('Width', 'wp-user-avatar') . '</label>
                <input name="wp_user_avatar_resize_w" type="number" step="1" min="0" id="wp_user_avatar_resize_w" value="' . get_option('wp_user_avatar_resize_w') . '" class="small-text" />
                <label for="wp_user_avatar_resize_h">' . __('Height', 'wp-user-avatar') . '</label>
                <input name="wp_user_avatar_resize_h" type="number" step="1" min="0" id="wp_user_avatar_resize_h" value="' . get_option('wp_user_avatar_resize_h') . '" class="small-text" />
                <br />
                <input name="wp_user_avatar_resize_crop" type="checkbox" id="wp_user_avatar_resize_crop" value="1" ' . checked('1', $wpua_resize_crop, 0) . ' />
                <label for="wp_user_avatar_resize_crop">' . __('Crop avatars to exact dimensions', 'wp-user-avatar') . '</label>
                </fieldset>
            </td>
        </tr>';
                    ?>
                </table>
                <div id="wpua-contributors-subscribers">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Disable Gravatar', 'wp-user-avatar'); ?></th>
                            <td>
                                <?php
                                // Format settings in fieldsets
                                $wpua_settings             = array();
                                $wpua_settings['gravatar'] = '<fieldset>
              <label for="wp_user_avatar_disable_gravatar">
                <input name="wp_user_avatar_disable_gravatar" type="checkbox" id="wp_user_avatar_disable_gravatar" value="1" ' . checked($wpua_disable_gravatar, 1, 0) . ' />'
                                                             . __('Disable Gravatar and use only local avatars', 'wp-user-avatar') . '
              </label>
            </fieldset>';
                                /**
                                 * Filter main settings
                                 *
                                 * @param array $wpua_settings
                                 */
                                $wpua_settings = apply_filters('wpua_settings', $wpua_settings);
                                echo implode("", $wpua_settings);
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <table class="form-table">
                    <tr valign="top" id="avatar-selection">
                        <th scope="row"><?php _e('Default Profile Picture', 'wp-user-avatar') ?></th>
                        <td class="defaultavatarpicker">
                            <fieldset>
                                <legend class="screen-reader-text">
                                    <span><?php _e('Default Profile Picture', 'wp-user-avatar'); ?></span></legend>
                                <?php _e('For users without a custom avatar of their own, you can either display a generic logo or a generated one based on their e-mail address.', 'wp-user-avatar'); ?>
                                <br/>
                                <?php echo $wpua_admin->wpua_add_default_avatar(); ?>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </td>
        </tr>
    </table>
</div>
