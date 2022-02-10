<?php
/**
 * Plugin main page
 *
 * @package     Wow_Plugin
 * @subpackage  Admin/Main_page
 * @author      Wow-Company <helper@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$data = $wpdb->prefix . 'wow_' . $this->plugin['prefix'];
$info = ( isset( $_REQUEST['info'] ) ) ? sanitize_text_field( $_REQUEST['info'] ) : '';
if ( $info == 'saved' ) {
	echo '<div class="updated" id="message"><p><strong>' . esc_attr__( 'Item Added', $this->plugin['text'] ) . '</strong>.</p></div>';
} elseif ( $info == 'update' ) {
	echo '<div class="updated" id="message"><p><strong>' . esc_attr__( 'Item Updated', $this->plugin['text'] ) . '</strong>.</p></div>';
} elseif ( $info == 'delete' ) {
	$delid = absint( $_GET['did'] );
	$wpdb->delete( $data, [ 'id' => $delid ], [ '%d' ] );
	echo '<div class="updated" id="message"><p><strong>' . esc_attr__( 'Item Deleted', $this->plugin['text'] ) . '</strong>.</p></div>';
}

$current_tab = ( isset( $_REQUEST["tab"] ) ) ? sanitize_text_field( $_REQUEST["tab"] ) : 'list';

$tabs = apply_filters( $this->plugin['slug'] . '_tab_menu', array(
	'list'      => esc_attr__( 'List', $this->plugin['text'] ),
	'settings'  => esc_attr__( 'Add new', $this->plugin['text'] ),
	'extension' => esc_attr__( 'Pro Features', $this->plugin['text'] ),
	'support'   => esc_attr__( 'Support', $this->plugin['text'] ),
	'tools'     => esc_attr__( 'Import/Export', $this->plugin['text'] ),
	'rate'      => esc_attr__( 'Rate', $this->plugin['text'] ),
) );

$rate_url = 'https://wordpress.org/support/plugin/float-menu/reviews/#new-post';

$rating = $this->rating['wp_url'];
?>

    <div class="wrap">
        <h1 class="wp-heading-inline"><?php echo esc_attr( $this->plugin['name'] ); ?>
            v. <?php echo esc_attr( $this->plugin['version'] ); ?></h1>
        <a href="?page=<?php echo esc_attr( $this->plugin['slug'] ); ?>&tab=settings" class="page-title-action">
			<?php esc_attr_e( 'Add New', $this->plugin['text'] ); ?></a>
        <hr class="wp-header-end">
		<?php if ( get_option( 'wow_' . $this->plugin['prefix'] . '_message' ) != 'read' ) : ?>
            <div class="notice notice-info is-dismissible wow-plugin-message">
                <p class="ideas">
                    <i class="fas fa-bullhorn"></i>We are constantly trying to improve the plugin and add more useful
                    features to it. Your support and your ideas for improving the plugin are very important to us. <br/>
                    <i class="fas fa-star"></i>If you like the plugin, please <a
                            href="<?php echo esc_url( $rating ); ?>" target="_blank">leave a review</a> about it at
                    WordPress.org.

                </p>
            </div>
		<?php endif; ?>

        <div id="wow-message"></div>

		<?php
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
			if ( $tab === 'options' ) {
				$action = ( isset( $_REQUEST["act"] ) ) ? sanitize_text_field( $_REQUEST["act"] ) : '';
				if ( ! empty( $action ) && $action == 'update' ) {
					echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . esc_attr( $this->plugin['slug'] ) . '&tab=' .
					     esc_attr( $tab ) . '">' . esc_attr__( 'Update', $this->plugin['prefix'] ) . ' #' .
					     absint( $_REQUEST["id"] ) . '</a>';
				} else {
					echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . esc_attr( $this->plugin['slug'] ) . '&tab=' .
					     esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
				}
			} elseif ( $tab === 'extension' ) {
				echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . esc_attr( $this->plugin['slug'] ) . '&tab='
				     . esc_attr( $tab ) . '"><span class="dashicons dashicons-yes" style="color:#00d1b2;"></span> ' . esc_attr( $name ) . '</a>';
			} elseif ( $tab === 'rate' ) {
				echo '<a class="nav-tab' . esc_attr( $class ) . '" href="' . esc_url( $rate_url ) . '" target="_blank"><span class="dashicons dashicons-star-filled" style="color:#ffcc01;"></span> ' . esc_attr( $name ) . '</a>';

			} elseif ( $tab === 'docs' ) {
				echo '<a class="nav-tab' . esc_attr( $class ) . '" href="' . esc_url( $docs_url ) . '" target="_blank"><span class="dashicons dashicons-media-document" style="color:#006bb3;"></span>' . esc_attr( $name ) . '</a>';

			} else {
				echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . esc_attr( $this->plugin['slug'] ) . '&tab=' .
				     esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
			}

		}
		echo '</h2>';
		$file = array_key_exists( $current_tab, $tabs ) ? 'page-' . $current_tab : 'page-list';
		include_once( $file . '.php' );
		?>
    </div>
<?php
