<?php

# Check get_plugin_data function exist
if (!function_exists('get_plugin_data')) {
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

# Set Plugin path and url defines.
define('WP_STATISTICS_URL', plugin_dir_url(dirname(__FILE__)));
define('WP_STATISTICS_DIR', plugin_dir_path(dirname(__FILE__)));
define('WP_STATISTICS_MAIN_FILE', WP_STATISTICS_DIR . 'wp-statistics.php');
define('WP_STATISTICS_UPLOADS_DIR', 'wp-statistics');

# Get plugin Data.
$plugin_data = get_plugin_data(WP_STATISTICS_MAIN_FILE);

# Set another useful Plugin defines.
define('WP_STATISTICS_VERSION', $plugin_data['Version']);
define('WP_STATISTICS_SITE', $plugin_data['PluginURI']);
define('WP_STATISTICS_REQUIRE_PHP_VERSION', '5.4.0');
