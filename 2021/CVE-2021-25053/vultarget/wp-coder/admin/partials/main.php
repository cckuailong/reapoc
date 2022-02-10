<?php
/**
 * Plugin main page
 *
 * @package     Wow Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'tools-data-base.php';

$current_tab = ( isset( $_REQUEST["tab"] ) ) ? sanitize_text_field( $_REQUEST["tab"] ) : 'list';

$tabs = apply_filters( 'wp_coder_menu', array(
	'list'      => __( 'List', 'wpcoder' ),
	'add_new'   => __( 'Add new', 'wpcoder' ),
	'items'     => __( 'CSS & JS Items', 'wpcoder' ),
	'extension' => __( 'Extension', 'wpcoder' ),
	'support'   => __( 'Support', 'wpcoder' ),
) );

$rating = 'https://wordpress.org/support/plugin/wp-coder/reviews/?filter=5#new-post';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo $this->plugin_name; ?> v. <?php echo $this->plugin_version; ?></h1>
    <a href="?page=<?php echo $this->plugin_slug; ?>&tab=add_new"
       class="page-title-action"><?php _e( 'Add New', 'wpcoder' ); ?></a> <a href="https://www.facebook.com/wowaffect/"
                                                                             class="page-title-action wow-facebook"
                                                                             target="_blank">Stay in touch</a>
    <hr class="wp-header-end">
	<?php if ( get_option( 'wp_coder_message' ) != 'read' ) : ?>
        <div class="notice notice-info is-dismissible wow-plugin-message">
            <p class="ideas">
                <i class="dashicons dashicons-megaphone"></i>We are constantly trying to improve the plugin and add more useful
                features to it. Your support and your ideas for improving the plugin are very important to us. <br/>
                <i class="dashicons dashicons-star-filled"></i>If you like the plugin, please <a href="<?php echo esc_url( $rating ); ?>"
                                                                             target="_blank">leave a review</a> about it
                at WordPress.org.<br/>
                <i class="dashicons dashicons-share"></i>Help other users find this plugin and take advantage of it.
                <b>Share:</b> <span data-share="facebook">Facebook</span>, <span data-share="twitter">Twitter</span>,
                <span data-share="vk">VK</span>, <span data-share="linkedin">LinkedIn</span>, <span
                        data-share="pinterest">Pinterest</span>, <span data-share="xing">XING</span>, <span
                        data-share="reddit">Reddit</span>, <span data-share="blogger">Blogger</span>, <span
                        data-share="telegram">Telegram</span>
            </p>
            <input type="hidden" id="wp-title" value="WP Coder – add custom html, css and js code">
            <input type="hidden" id="wp-url" value="https://wordpress.org/plugins/wp-coder/">
        </div>
	<?php endif; ?>

	<?php
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $tabs as $tab => $name ) {
		$class = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
		if ( $tab == 'add_new' ) {
			$action = ( isset( $_REQUEST["act"] ) ) ? sanitize_text_field( $_REQUEST["act"] ) : '';
			if ( ! empty( $action ) && $action == 'update' ) {
				echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . $this->plugin_slug . '&tab=' . esc_attr( $tab ) . '">' . __( 'Update', 'leadgeneration' ) . ' #' . absint( $_REQUEST["id"] ) . '</a>';
			} else {
				echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . $this->plugin_slug . '&tab=' . esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
			}
		} else {
			echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . $this->plugin_slug . '&tab=' . esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
		}

	}
	echo '</h2>';
	$current_tab = array_key_exists( $current_tab, $tabs ) ? $current_tab : 'list';
	$file = apply_filters( 'wp_coder_file', $current_tab );
	include_once( $file . '.php' );

	?>
</div>


