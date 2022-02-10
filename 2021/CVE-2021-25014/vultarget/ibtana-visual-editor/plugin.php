<?php
/**
 * Plugin Name:       Ibtana - WordPress Website Builder
 * Plugin URI:        https://www.vwthemes.com/plugins/wordpress-website-builder/
 * Description:       Ibtana Gutenberg Editor has ready made eye catching responsive templates build with custom blocks and options to extend Gutenberg’s default capabilities. You can easily import demo content for the block or templates with a single click. Once done, you can straight away start making the desired changes. It also kit with individual components and blocks to build internal pages. Now you don’t need to invest too much time in editing or recreating the template you love. Now its just drag and drop and easy edit of your favourite template with just few clicks.
 * Version:           1.1.4.8
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            VowelWeb
 * Author URI:        https://www.vowelweb.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ibtana-visual-editor
 * Domain Path:       /languages
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'IBTANA_EXT_FILE', __FILE__ );
define( 'IBTANA_PLUGIN_URI', plugins_url( '/', IBTANA_EXT_FILE ) );
define( 'IBTANA_PLUGIN_DIR', plugin_dir_path( IBTANA_EXT_FILE ) );
define( 'IBTANA_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'IBTANA_PLUGIN_THEME', 'ibtana' );
define( 'IVE_DESKTOP_STARTPOINT', '1025' );
define( 'IVE_TABLET_BREAKPOINT', '1024' );
define( 'IVE_MOBILE_BREAKPOINT', '767' );
define( 'IVE_FILE', __FILE__ );
define( 'IVE_BASE', plugin_basename( IVE_FILE ) );
define( 'IVE_DIR', plugin_dir_path( IVE_FILE ) );
define( 'IVE_URL', plugins_url( '/', IVE_FILE ) );
if( ! function_exists('get_plugin_data') ) {
  require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
$plugin_data = get_plugin_data( __FILE__ );
define( 'IVE_VER', $plugin_data['Version'] );
define( 'IBTANA_LICENSE_API_ENDPOINT', 'https://www.vwthemes.com/wp-json/ibtana-licence/v2/' );
define( 'IBTANA_THEME_URL', 'https://www.vwthemes.com/' );

// Add the links on the Plugins administration screen
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ibtana_visual_editor_action_links' );
function ibtana_visual_editor_action_links( $links ) {
  $plugin_links = array(
    '<a href="' . admin_url( 'admin.php?page=ibtana-visual-editor' ) . '">Settings</a>'
  );
  return array_merge( $plugin_links, $links );
}
// Add the links on the Plugins administration screen END
// Admin Menu To Display Premium Products
require_once IVE_DIR . 'classes/class-ive-loader.php';
require_once IVE_DIR . 'classes/class-ive-config.php';
require_once IVE_DIR . 'classes/class-ive-block-helper.php';
require_once IVE_DIR . 'classes/class-ive-block-js.php';
require_once IVE_DIR . 'classes/class-ive-helper.php';
require_once IVE_DIR . 'classes/class-ive-admin.php';
require_once IVE_DIR . 'classes/class-cpt.php';
require_once IVE_DIR . 'classes/ive-notice.php';
require_once IVE_DIR . 'admin-menu.php';
// Admin Menu To Display Premium Products END


require_once IVE_DIR . 'src/blocks/form/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/text/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/email/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/name/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/url/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/phone/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/number/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/date/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/textarea/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/select/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/checkbox/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/radio/block.php';
require_once IVE_DIR . 'src/blocks/form/fields/hidden/block.php';

require_once IVE_DIR . 'src/init.php';
require_once IVE_DIR . 'dist/class-ibtana-blocks-frontend.php';
require_once IVE_DIR . 'dist/post/plugin-post.php';
require_once IVE_DIR . 'admin/settings.php';
require_once IVE_DIR . 'ive-countdown.php';

require_once IVE_DIR . 'classes/ibtana-update-checker.php';
?>
