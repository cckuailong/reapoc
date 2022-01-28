<?php

namespace ProfilePress\Core\NavigationMenuLinks;

class Init
{
    public static function init()
    {
        register_activation_hook(PROFILEPRESS_SYSTEM_FILE_PATH, function () {
            // deactivate https://wordpress.org/plugins/wp-navigation-menu-links/ since it's now in core
            $path = 'wp-navigation-menu-links/pp-navigation-menu-links.php';
            if (is_plugin_active($path)) {
                deactivate_plugins($path);
            }
        });

        add_action('plugins_loaded', function () {
            Backend::get_instance();
            Frontend::get_instance();
        });
    }
}