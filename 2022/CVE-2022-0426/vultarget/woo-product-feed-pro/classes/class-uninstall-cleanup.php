<?php
/**
 * Clean up your mess like your mom always told you to do :-)
 * This class is called upon plugin uninstalling and cleans up cron hooks and wp_options
 * When the plugin is removed completely the configured projects will be losed and deleted
 */
class WooSEA_Uninstall_Cleanup {
        public static function uninstall_cleanup() {
                wp_clear_scheduled_hook( 'woosea_cron_hook' );
                wp_clear_scheduled_hook( 'woosea_check_license' );
//              delete_option( 'channel_statics' );
//              delete_option( 'cron_projects' );
//		delete_option('woosea_getelite_notification');
//              delete_option('woosea_license_notification_closed'); // This one is new
        }
}
