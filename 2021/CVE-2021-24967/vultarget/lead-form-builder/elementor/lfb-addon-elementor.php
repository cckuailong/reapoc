<?php

if ( ! defined( 'ABSPATH' ) ) {
   exit; // Exit if accessed directly.
}

// Version constant for easy CSS refreshes
define('LFB_EXT_FILE', __FILE__ );
define('LFB_UN_PLUGIN_URL', plugin_dir_url(LFB_EXT_FILE));
define('LFB_EXT_DIR', plugin_dir_path(LFB_EXT_FILE ) );

require_once plugin_dir_path( __FILE__ ).'class-lfb-init.php';