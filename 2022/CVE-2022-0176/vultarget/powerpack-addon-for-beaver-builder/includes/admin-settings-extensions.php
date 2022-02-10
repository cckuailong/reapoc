<?php if ( is_network_admin() || !is_multisite() ) { ?>

    <?php
    $quick_preview      = self::get_option('bb_powerpack_quick_preview');
    $search_box         = self::get_option('bb_powerpack_search_box');
    $extensions         = pp_extensions();
    $enabled_extensions = self::get_enabled_extensions();
    ?>

    <table class="form-table">
        <tbody>
            <?php if ( ! class_exists( 'FLBuilderUIContentPanel' ) ) { ?>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('Quick Preview', 'bb-powerpack-lite'); ?>
                </th>
                <td>
                    <p>
                        <label>
                            <input type="checkbox" name="bb_powerpack_quick_preview" value="1" <?php echo ( $quick_preview == 1 ) ? 'checked="checked"' : ''; ?> />
                            <?php _e('Enable Quick Preview', 'bb-powerpack-lite'); ?>
                        </label>
                    </p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('Search Box', 'bb-powerpack-lite'); ?>
                </th>
                <td>
                    <p>
                        <label>
                            <input type="checkbox" name="bb_powerpack_search_box" value="1" <?php echo ( $search_box == 1 ) ? 'checked="checked"' : ''; ?> />
                            <?php _e('Enable Search Box in panel', 'bb-powerpack-lite'); ?>
                        </label>
                    </p>
                </td>
            </tr>
            <?php } ?>
            <tr valign="top">
                <th scope="row" valign="top">
                    <?php _e('Row Extensions', 'bb-powerpack-lite'); ?>
                </th>
                <td>
                    <?php foreach ( $extensions['row'] as $extension => $name ) :
                        $checked = ( array_key_exists($extension, $enabled_extensions['row']) || in_array( $extension, $enabled_extensions['row'] ) ) ? 'checked="checked"' : '';  ?>
                    <p>
                        <label>
                            <input type="checkbox" name="bb_powerpack_extensions[row][]" value="<?php echo $extension; ?>" <?php echo $checked; ?> />
                            <?php echo $name; ?>
                        </label>
                    </p>
                    <?php endforeach; ?>
                </td>
            </tr>

        </tbody>
    </table>

    <?php submit_button(); ?>
    <?php wp_nonce_field('pp-extensions', 'pp-extensions-nonce'); ?>

<?php } ?>
