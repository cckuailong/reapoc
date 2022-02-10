<?php

define('WPRA_TMP_0_2_BASENAME', 'wp-rss-templates-0.2/wp-rss-templates.php');
define('WPRA_TMP_PROPER_BASENAME', 'wp-rss-templates/wp-rss-templates.php');

add_action('update_option_active_plugins', function ($oldValue, $newValue) {
    $oldPlugins = array_flip($oldValue);
    $newPlugins = array_flip($newValue);

    if (isset($oldPlugins[WPRA_TMP_0_2_BASENAME]) && !isset($newPlugins[WPRA_TMP_0_2_BASENAME])) {
        if (file_exists(WP_PLUGIN_DIR . '/' . WPRA_TMP_PROPER_BASENAME)) {
            activate_plugin(WPRA_TMP_PROPER_BASENAME);
        }
    }
}, 10, 2);
