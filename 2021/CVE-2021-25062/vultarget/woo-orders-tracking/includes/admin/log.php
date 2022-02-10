<?php

/**
 * Class VI_WOO_ORDERS_TRACKING_ADMIN_LOG
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
ini_set( 'auto_detect_line_endings', true );

class VI_WOO_ORDERS_TRACKING_ADMIN_LOG {
	public static function log( $logs_content ) {
		$log_file     = VI_WOO_ORDERS_TRACKING_CACHE . 'import_tracking.txt';
		$logs_content = PHP_EOL . "[" . date( "Y-m-d H:i:s" ) . "] " . $logs_content;
		if ( is_file( $log_file ) ) {
			file_put_contents( $log_file, $logs_content, FILE_APPEND );
		} else {
			file_put_contents( $log_file, $logs_content );
		}
	}

	public static function create_plugin_cache_folder() {
		if ( ! is_dir( VI_WOO_ORDERS_TRACKING_CACHE ) ) {
			wp_mkdir_p( VI_WOO_ORDERS_TRACKING_CACHE );
			file_put_contents( VI_WOO_ORDERS_TRACKING_CACHE . '.htaccess', '<IfModule !mod_authz_core.c>
Order deny,allow
Deny from all
</IfModule>
<IfModule mod_authz_core.c>
  <RequireAll>
    Require all denied
  </RequireAll>
</IfModule>
' );
		}
	}
}
