<?php
/**
 * Clean up your mess like your mom always told you to do :-)
 * This class is called upon plugin deactivation and cleans up cron and variables
 * We do NOT delete previously configured feeds and it events as one would like to re-use it when the plugin gets activated again
 */
class WooSEA_Deactivate_Cleanup {
        public static function deactivate_cleanup() {
                wp_clear_scheduled_hook('woosea_cron_hook');
                wp_clear_scheduled_hook( 'woosea_check_license' );
		delete_option('woosea_getelite_notification');
		delete_option('woosea_license_notification_closed'); // This one is new
	}
}
