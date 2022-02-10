<?php
/**
 * @version 1.0
 * @package Booking Calendar 
 * @subpackage Files Loading
 * @category Bookings
 * 
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 29.09.2015
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

////////////////////////////////////////////////////////////////////////////////
//   L O A D   F I L E S
////////////////////////////////////////////////////////////////////////////////

require_once( WPBC_PLUGIN_DIR . '/core/any/class-css-js.php' );                 // Abstract. Loading CSS & JS files                 = Package: Any =
require_once( WPBC_PLUGIN_DIR . '/core/any/class-admin-settings-api.php' );     // Abstract. Settings API.        
require_once( WPBC_PLUGIN_DIR . '/core/any/class-admin-page-structure.php' );   // Abstract. Page Structure in Admin Panel    
require_once( WPBC_PLUGIN_DIR . '/core/any/class-admin-menu.php' );             // CLASS. Menus of plugin
require_once( WPBC_PLUGIN_DIR . '/core/any/admin-bs-ui.php' );                  // Functions. Toolbar BS UI Elements
if( is_admin() ) {
    require_once WPBC_PLUGIN_DIR . '/core/class/wpbc-class-dismiss.php';        // Class - Dismiss                 
    require_once WPBC_PLUGIN_DIR . '/core/class/wpbc-class-notices.php';        // Class - Showing different messages and alerts. Including some predefined static messages.    
    require_once WPBC_PLUGIN_DIR . '/core/class/wpbc-class-welcome.php';        // Class - Welcome Page - info  about  new version.
}
// Functions
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-debug.php' );                       // Debug                                            = Package: WPBC =
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-core.php' );                        // Core 
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-dates.php' );                       // Dates 
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-translation.php' );                 // Translations 
if ( file_exists( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations.php' ) ){  

	require_once( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations.php' );    // All Translation Terms

	//FixIn: 8.7.3.6
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations1.php' ) ){
		require_once( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations1.php' );
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations2.php' ) ){
		require_once( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations2.php' );
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations3.php' ) ){
		require_once( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations3.php' );
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations4.php' ) ){
		require_once( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations4.php' );
	}
	if ( file_exists( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations5.php' ) ){
		require_once( WPBC_PLUGIN_DIR . '/core/lib/wpbc_all_translations5.php' );
	}
}
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-functions.php' );                   // Functions
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-emails.php' );                      // Emails
// JS & CSS
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-css.php' );                         // Load CSS
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-js.php' );                          // Load JavaScript and define JS Varibales
// Admin UI
require_once( WPBC_PLUGIN_DIR . '/core/admin/wpbc-toolbars.php' );              // Toolbar - BS UI Elements
require_once( WPBC_PLUGIN_DIR . '/core/admin/wpbc-sql.php' );                   // Data Engine for Booking Listing / Calendar Overview pages
require_once( WPBC_PLUGIN_DIR . '/core/admin/wpbc-class-listing.php' );         // CLASS. Booking Listing Table

//FixIn: 8.6.1.13
require_once( WPBC_PLUGIN_DIR . '/core/timeline/flex-timeline.php' );           // New. Flex. Timeline

require_once( WPBC_PLUGIN_DIR . '/core/admin/wpbc-dashboard.php' );             // Dashboard Widget
// Admin Pages
require_once( WPBC_PLUGIN_DIR . '/core/admin/page-bookings.php' );              // Booking Listing              
require_once( WPBC_PLUGIN_DIR . '/core/admin/page-timeline.php' );              // Timeline
require_once( WPBC_PLUGIN_DIR . '/core/admin/page-new.php' );                   // Add New Booking page

require_once( WPBC_PLUGIN_DIR . '/core/admin/page-settings.php' );              // Settings page 
    require_once( WPBC_PLUGIN_DIR . '/core/admin/api-settings.php' );           // Settings API

require_once( WPBC_PLUGIN_DIR . '/core/admin/wpbc-gutenberg.php' );              // Settings page

////////////////////////////////////////////////////////////////////////////////

if ( file_exists( WPBC_PLUGIN_DIR.'/inc/_ps/personal.php' ) ){   
    require_once WPBC_PLUGIN_DIR . '/inc/_ps/personal.php';  
} else {
	require_once( WPBC_PLUGIN_DIR . '/core/admin/page-up.php' );                // Up                                   //FixIn: 8.0.1.6
	require_once( WPBC_PLUGIN_DIR . '/core/admin/page-form-free.php' );         // Fields

    require_once( WPBC_PLUGIN_DIR . '/core/admin/page-email-new-admin.php' );   // Email - New admin.
    require_once( WPBC_PLUGIN_DIR . '/core/admin/page-email-new-visitor.php' ); // Email - New visitor
    require_once( WPBC_PLUGIN_DIR . '/core/admin/page-email-deny.php' );        // Email - Deny - set  pending
    require_once( WPBC_PLUGIN_DIR . '/core/admin/page-email-approved.php' );    // Email - Approved
    require_once( WPBC_PLUGIN_DIR . '/core/admin/page-email-trash.php' );       // Email - Trash
    require_once( WPBC_PLUGIN_DIR . '/core/admin/page-email-deleted.php' );     // Email - Deleted - completely  erase

	require_once( WPBC_PLUGIN_DIR . '/core/admin/page-ics-general.php' );		// General ICS Help Settings page		    //FixIn: 8.1.1.10
	require_once( WPBC_PLUGIN_DIR . '/core/admin/page-ics-import.php' );        // Import ICS Help Settings page			//FixIn: 8.0
	require_once( WPBC_PLUGIN_DIR . '/core/admin/page-ics-export.php' );        // Export ICS Feeds Settings page			//FixIn: 8.0
    require_once( WPBC_PLUGIN_DIR . '/core/admin/page-import-gcal.php' );       // Import from  Google Calendar Settings page 
}

    
// Old Working        
require_once WPBC_PLUGIN_DIR . '/core/lib/wpdev-booking-widget.php';            // W i d g e t s
require_once WPBC_PLUGIN_DIR . '/js/captcha/captcha.php';                       // C A P T C H A


//FixIn: 8.8.3.8
add_action( 'plugins_loaded', 'wpbc_late_load_country_list', 100, 1 );
/**
 * Load country list after locale defined by some other translation  plugin.
 */
function wpbc_late_load_country_list() {

	//FixIn: 8.8.2.5
	$locale = wpbc_get_booking_locale();
	if ( ! empty( $locale ) ) {
		$locale_lang    = strtolower( substr( $locale, 0, 2 ) );
		$locale_country = strtolower( substr( $locale, 3 ) );

		if ( ( $locale_lang !== 'en' ) && ( wpbc_is_file_exist( '/languages/wpdev-country-list-' . $locale . '.php' ) ) ) {
			require_once WPBC_PLUGIN_DIR . '/languages/wpdev-country-list-' . $locale . '.php';
		} else {
			require_once WPBC_PLUGIN_DIR . '/languages/wpdev-country-list.php';
		}
	} else {
		require_once WPBC_PLUGIN_DIR . '/languages/wpdev-country-list.php';
	}
}


require_once WPBC_PLUGIN_DIR . '/core/lib/wpdev-booking-class.php';             // C L A S S    B o o k i n g
require_once WPBC_PLUGIN_DIR . '/core/lib/wpbc-booking-new.php';                // N e w    
require_once WPBC_PLUGIN_DIR . '/core/lib/wpbc-cron.php';                       // CRON  @since: 5.2.0

if( is_admin() ) {
    require_once WPBC_PLUGIN_DIR . '/core/admin/wpbc-toolbar-tiny.php';         // B o o k i n g    B u t t o n s   in   E d i t   t o o l b a r        
} 

require_once WPBC_PLUGIN_DIR . '/core/sync/wpbc-gcal-class.php';                //DONE: in 7.0     // Google Calendar Feeds Import @since: 5.2.0  - v.3.0 API support @since: 5.4.0
require_once WPBC_PLUGIN_DIR . '/core/sync/wpbc-gcal.php';                      //DONE: in 7.0     // Sync Google Calendar Events with  WPBC @since: 5.2.0  - v.3.0 API support @since: 5.4.0

require_once( WPBC_PLUGIN_DIR . '/core/any/activation.php' );
require_once( WPBC_PLUGIN_DIR . '/core/wpbc-activation.php' );

require_once( WPBC_PLUGIN_DIR . '/core/wpbc-dev-api.php' );					// API for Booking Calendar integrations	//FixIn: 8.0

make_bk_action( 'wpbc_loaded_php_files' );