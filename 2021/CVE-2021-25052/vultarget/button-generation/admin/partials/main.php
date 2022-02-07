<?php
/**
 * Plugin main page
 *
 * @package     Wow_Plugin
 * @subpackage  Admin/Main_page
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'tools-data-base.php';

$current_tab = ( isset( $_REQUEST["tab"] ) ) ? sanitize_text_field( $_REQUEST["tab"] ) : 'list';

$tabs = apply_filters( $this->plugin['slug'] . '_tab_menu', array(
	'list'    => esc_attr__( 'List', $this->plugin['text'] ),
	'add-new' => esc_attr__( 'Add new', $this->plugin['text'] ),
	'extension' => esc_attr__( 'Pro Features', $this->plugin['text'] ),
	'support' => esc_attr__( 'Support', $this->plugin['text'] ),
	'items'   => esc_attr__( 'Plugins', $this->plugin['text'] ),
) );

$rating = $this->rating['wp_url'];

?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php echo $this->plugin['name']; ?> v. <?php echo $this->plugin['version']; ?></h1>
		<a href="?page=<?php echo $this->plugin['slug']; ?>&tab=add-new" class="page-title-action"><?php esc_attr_e( 'Add New',
			$this->plugin['text'] ); ?></a> <a href="<?php echo $this->url['facebook']; ?>" class="page-title-action"
																							 target="_blank">Stay in touch</a>
		<hr class="wp-header-end">
		<?php if ( get_option( 'wow_' . $this->plugin['prefix'] . '_message' ) != 'read' ) : ?>
            <div class="notice notice-info is-dismissible wow-plugin-message">
                <p class="ideas">
                    <i class="fas fa-bullhorn"></i>We are constantly trying to improve the plugin and add more useful features to it. Your support and your ideas for improving the plugin are very important to us. <br/>
                    <i class="fas fa-star"></i>If you like the plugin, please <a href="<?php echo esc_url($rating); ?>" target="_blank">leave a review</a> about it at WordPress.org.<br/>
                    <i class="fas fa-share-alt"></i>Help other users find this plugin and take advantage of it. <b>Share:</b> <span data-share="facebook">Facebook</span>, <span data-share="twitter">Twitter</span>, <span data-share="vk">VK</span>, <span data-share="linkedin">LinkedIn</span>, <span data-share="pinterest">Pinterest</span>, <span data-share="xing">XING</span>, <span data-share="reddit">Reddit</span>, <span data-share="blogger">Blogger</span>, <span data-share="telegram">Telegram</span>
                </p>
                <input type="hidden" id="wp-title" value="<?php echo esc_attr($this->rating['wp_title']);?>">
                <input type="hidden" id="wp-url" value="<?php echo esc_url($this->rating['wp_home']);?>">
            </div>
		<?php endif;?>

		<div id="wow-message"></div>

	  <?php
	  echo '<h2 class="nav-tab-wrapper">';
	  foreach ( $tabs as $tab => $name ) {
		  $class = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
		  if ( $tab == 'add_new' ) {
			  $action = ( isset( $_REQUEST["act"] ) ) ? sanitize_text_field( $_REQUEST["act"] ) : '';
			  if ( ! empty( $action ) && $action == 'update' ) {
				  echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . $this->plugin['slug'] . '&tab=' .
				       esc_attr( $tab ) . '">' . esc_attr__( 'Update', $this->plugin['prefix'] ) . ' #' .
				       absint( $_REQUEST["id"] ) . '</a>';
			  } else {
				  echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . $this->plugin['slug'] . '&tab=' .
				       esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
			  }
		  } else {
			  echo '<a class="nav-tab' . esc_attr( $class ) . '" href="?page=' . $this->plugin['slug'] . '&tab=' .
			       esc_attr( $tab ) . '">' . esc_attr( $name ) . '</a>';
		  }

	  }
	  echo '</h2>';
	  $current_tab = array_key_exists( $current_tab, $tabs ) ? $current_tab : 'list';
	  $file        = apply_filters( $this->plugin['slug'] . '_menu_file', $current_tab );
	  include_once( $file . '.php' );
	  ?>
	</div>
<?php
