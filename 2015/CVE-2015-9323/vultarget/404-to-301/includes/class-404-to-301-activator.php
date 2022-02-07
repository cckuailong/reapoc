<?php
if ( ! defined( 'WPINC' ) ) {
	die('Damn it.! Dude you are looking for what?');
}
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link		http://iscode.co/product/404-to-301/
 * @since		2.0.0
 * @package		I4T3
 * @subpackage	I4T3/includes
 * @author		Joel James <me@joelsays.com>
 */
class _404_To_301_Activator {

	/**
	* Function to run during activation
	* Transfering old options to new - 404 to 301
 	*
 	* 404 to 301 Coding sturucture and options are changed to new sturcture.
 	* So we need to transfer old values to new structure. This file will 
 	* be used once. After transferring, we will never use these functions.
 	*
 	* @since    2.0.0
 	* @author 	Joel James
 	*/
	public static function activate() {

		// Set default values for the plugin
		$i4t3_type			= self::transfer( 'type', 'redirect_type', '301' );
		$i4t3_link			= self::transfer( 'link', 'redirect_link', site_url() );
		$i4t3_enable		= self::transfer( '', 'redirect_log', 1 );
		$i4t3_to			= self::transfer( '', 'redirect_to', 'link' );
		$i4t3_page			= self::transfer( '', 'redirect_page', '' );
		$i4t3_notify		= self::transfer( '', 'email_notify', 1 );

		// New general settings array to be added
		$i4t3GnrlOptions = array( 
					'redirect_type' => $i4t3_type,
					'redirect_link' => $i4t3_link,
					'redirect_log' => $i4t3_enable,
					'redirect_to' => $i4t3_to,
					'redirect_page' => $i4t3_page,
					'email_notify' => $i4t3_notify
				);	

		/**
		 *	Array of all settings arrays.
		 *	We are adding this to an array as we need to register
		 *	multiple settings in future for addons
		 */
		$i4t3_options = array(
					'i4t3_gnrl_options' => $i4t3GnrlOptions
				);

		// If not already exist, adding values
		foreach ($i4t3_options as $key => $value) {	
			update_option($key, $value );
		}

		// remember, two spaces after PRIMARY KEY otherwise WP borks
		$installed_version = get_option('i4t3_db_version');
		
		if( I4T3_DB_VERSION != $installed_version ) {

			global $wpdb;
			$table = $wpdb->prefix . "404_to_301";
			
			$sql = "CREATE TABLE $table (
				id BIGINT NOT NULL AUTO_INCREMENT,
				date DATETIME NOT NULL,
				url VARCHAR(512) NOT NULL,
				ref VARCHAR(512) NOT NULL default '', 
				ip VARCHAR(40) NOT NULL default '',
				ua VARCHAR(512) NOT NULL default '',
				PRIMARY KEY  (id)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql); // To be safe on db upgrades
			update_option( 'i4t3_db_version', I4T3_DB_VERSION );
		}
	}


	/**
	* Function to get existing settings
 	*
 	* This function used to check if the new setting is already available
 	* in datatabse, then consider that. Otherwise check for the old one 
 	* and if available, takes that.
 	* If both the values are not available, then creates new default settings.
 	*
 	* @since    2.0.0
 	* @author 	Joel James
 	*/
	public static function transfer( $old, $new, $fresh ){

		$option = 'i4t3_gnrl_options';
		
		// let us check if new options already exists
		if( get_option( $option ) ) {
			$i4t3_option = get_option( $option );
			// If exists, then take that option value
			$fresh = $i4t3_option[$new];
			// Check if old value is available for the same option
			if(get_option( $old )) {
				// If available delete it, as we are moving to new settings
				delete_option( $old );
			}
		} 
		// Fine, new options doesn't exist, then let us search for old
		else if( get_option( $old ) ) {
			// Take old value and set it to new
			$fresh = get_option($old);
			// Delete it, as we are moving to new settings
			delete_option( $old );
		}

		return $fresh;
	}

}