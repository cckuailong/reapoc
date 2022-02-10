<?php
# No need for the template engine
define( 'WP_USE_THEMES', false );
//we set wp admin to true, so the backend features get loaded.
if (!defined('CMPLZ_DOING_SYSTEM_STATUS')) define( 'CMPLZ_DOING_SYSTEM_STATUS' , true);

#find the base path
define( 'BASE_PATH', find_wordpress_base_path()."/" );

# Load WordPress Core
if ( !file_exists(BASE_PATH . 'wp-load.php') ) {
	die("WordPress not installed here");
}
require_once( BASE_PATH . 'wp-load.php' );
require_once( BASE_PATH . 'wp-includes/class-phpass.php' );
require_once( BASE_PATH . 'wp-admin/includes/image.php' );
require_once( BASE_PATH . 'wp-admin/includes/plugin.php');

if ( current_user_can( 'manage_options' ) ) {

	ob_start();

	echo 'Domain:' . esc_url_raw( site_url() ) . "\n";
	echo 'jQuery: ' . get_option('cmplz_detected_missing_jquery') ? 'No jquery found on site' : 'Successfully detected jquery';
	echo "\n";
	$console_errors = cmplz_get_console_errors();
	if (empty($console_errors)) $console_errors = "none found";
	echo 'Detected console errors: ' . $console_errors . "\n". "\n";

	echo "General\n";
	echo "Plugin version: " . cmplz_version . "\n";
	global $wp_version;
	echo "WordPress version: " . $wp_version . "\n";
	echo "PHP version: " . PHP_VERSION . "\n";
	echo "Server: " . cmplz_get_server() . "\n";
	$multisite = is_multisite() ? 'yes' : 'no';
	echo "Multisite: " . $multisite . "\n";
	echo "\n";
	$plugins         = get_option( 'active_plugins' );
	echo "Active plugins: " . "\n";
	echo implode( "\n", $plugins ) . "\n";

	$settings = get_option( 'complianz_options_settings' );
	echo "\n"."General settings" . "\n";
	echo implode_array_recursive($settings);

	$wizard   = get_option( 'complianz_options_wizard' );
	echo "\n\n"."Wizard settings" . "\n";
	$t = array_keys($wizard);
	echo implode_array_recursive($wizard);
	do_action( "cmplz_system_status" );

	$content = ob_get_clean();


	if ( function_exists( 'mb_strlen' ) ) {
		$fsize = mb_strlen( $content, '8bit' );
	} else {
		$fsize = strlen( $content );
	}
	$file_name = 'complianz-system-status.txt';
	header( "Content-type: application/octet-stream" );

	//direct download
	header( "Content-Disposition: attachment; filename=\"" . $file_name . "\"" );

	//open in browser
	header( "Content-length: $fsize" );
	header( "Cache-Control: private", false ); // required for certain browsers
	header( "Pragma: public" ); // required
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
	header( "Content-Transfer-Encoding: binary" );

	echo $content;

} else {
	//should not be here, so redirect to home
	wp_redirect( home_url() );
	exit;
}

/**
 * Get the WP Base path
 * @return false|string|null
 */
function find_wordpress_base_path()
{
	$path = dirname(__FILE__);

	do {
		if (file_exists($path . "/wp-config.php")) {
			//check if the wp-load.php file exists here. If not, we assume it's in a subdir.
			if ( file_exists( $path . '/wp-load.php') ) {
				return $path;
			} else {
				//wp not in this directory. Look in each folder to see if it's there.
				if ( file_exists( $path ) && $handle = opendir( $path ) ) {
					while ( false !== ( $file = readdir( $handle ) ) ) {
						if ( $file != "." && $file != ".." ) {
							$file = $path .'/' . $file;
							if ( is_dir( $file ) && file_exists( $file . '/wp-load.php') ) {
								$path = $file;
								break;
							}
						}
					}
					closedir( $handle );
				}
			}

			return $path;
		}
	} while ($path = realpath("$path/.."));

	return false;
}

/**
 * Generate a readable string from an array
 * @param array $array
 *
 * @return string
 */

function implode_array_recursive($array) {
	if (!is_array($array)) return '';
	return implode("\n", array_map(
		function ($v, $k) {
			if (is_array($v)){
				$output = implode_array_recursive($v);
			} else {
				$output = sprintf("%s : %s", $k, $v);
			}
			return $output;
		},
		$array,
		array_keys($array)
	));
}
