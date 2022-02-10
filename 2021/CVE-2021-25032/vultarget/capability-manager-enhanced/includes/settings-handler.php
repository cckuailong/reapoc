<?php
/*
 * PublishPress Capabilities [Free]
 * 
 * Process updates to plugin settings
 * 
 */

add_action('init', function() {

    if (!empty($_POST['all_options'])) {
        foreach(explode(',', $_POST['all_options']) as $option_name) {
            $value = isset($_POST[$option_name]) ? $_POST[$option_name] : '';

            if (!is_array($value)) {
                $value = trim($value);
            }

            update_option($option_name, stripslashes_deep($value));
        }
    }

    do_action('pp-capabilities-update-settings');
});