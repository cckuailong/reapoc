<?php
/**
 * Plugin Name: Photo Gallery
 * Plugin URI: https://10web.io/plugins/wordpress-photo-gallery/?utm_source=photo_gallery&utm_medium=free_plugin
 * Description: This plugin is a fully responsive gallery plugin with advanced functionality.  It allows having different image galleries for your posts and pages. You can create unlimited number of galleries, combine them into albums, and provide descriptions and tags.
 * Version: 1.5.68
 * Author: Photo Gallery Team
 * Author URI: https://10web.io/plugins/?utm_source=photo_gallery&utm_medium=free_plugin
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') || die('Access Denied');

$bwg = 0;
final class BWG {
  /**
   * The single instance of the class.
   */
  protected static $_instance = null;

  public $plugin_link = 'https://10web.io/plugins/wordpress-photo-gallery/';
  public $utm_source = '?utm_source=photo_gallery&utm_medium=free_plugin';
  /**
   * Plugin directory path.
   */
  public $plugin_dir = '';
  /**
   * Plugin directory url.
   */
  public $plugin_url = '';
  /**
   * Plugin main file.
   */
  public $main_file = '';
  /**
   * Plugin version.
   */
  public $plugin_version = '';
  /**
   * Plugin database version.
   */
  public $db_version = '';

  /**
   * Plugin prefix.
   */
  public $prefix = '';
  public $nicename = '';
  public $nonce = 'bwg_nonce';
  public $is_pro = FALSE;
  public $is_demo = FALSE;
  public $options = array();
  public $upload_dir = '';
  public $upload_url = '';
  public $free_msg = '';
  public $front_url = '';
  /* $abspath variable is using as defined APSPATH doesn't work in wordpress.com */
  public $abspath = '';

  /**
   * Main BWG Instance.
   *
   * Ensures only one instance is loaded or can be loaded.
   *
   * @static
   * @return BWG - Main instance.
   */
  public static function instance() {
    if ( is_null( self::$_instance ) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * BWG Constructor.
   */
  public function __construct() {
    $this->define_constants();
    $this->add_actions();
  }

  /**
   * get ABSPATH from WP_CONTENT_DIR.
   *
   * @param string $dirpath
   *
   * @return string
   */
  public static function get_abspath() {
    $dirpath = defined( 'WP_CONTENT_DIR' ) ? WP_CONTENT_DIR : ABSPATH;
    $array = explode( "wp-content", $dirpath );
    if( isset( $array[0] ) && $array[0] != "" ) {
      return $array[0];
    }
    return ABSPATH;
  }

  /**
   * Define Constants.
   */
  private function define_constants() {
    $this->abspath = self::get_abspath();
    $this->plugin_dir = WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__));
    $this->plugin_url = plugins_url(plugin_basename(dirname(__FILE__)));
    $this->front_url = $this->plugin_url;
    $this->main_file = plugin_basename(__FILE__);
    $this->plugin_version = '1.5.68';
    $this->db_version = '1.5.68';
    $this->prefix = 'bwg';
    $this->nicename = __('Photo Gallery', $this->prefix);
    require_once($this->plugin_dir . '/framework/WDWLibrary.php');
    require_once($this->plugin_dir . '/framework/BWGOptions.php');
    $this->options = new WD_BWG_Options();
    require_once($this->plugin_dir . '/framework/WD_BWG_Theme.php');

    $this->is_demo = get_site_option('tenweb_admin_demo');

    $this->upload_dir = $this->options->upload_dir;
    $this->upload_url = $this->options->upload_url;

    if ( $this->is_demo ) {
      $this->upload_dir = preg_replace('/uploads(.+)photo-gallery/', 'uploads/photo-gallery', $this->upload_dir);
      $this->upload_url = preg_replace('/uploads(.+)photo-gallery/', 'uploads/photo-gallery', $this->upload_url);
    }

    $this->free_msg = __('This option is available in Premium version', $this->prefix);
  }

  /**
   * Add actions.
   */
  private function add_actions() {
    add_action('init', array($this, 'init_free_users_lib'), 8);
    add_action('init', array($this, 'init'), 9);
    add_action('admin_menu', array( $this, 'admin_menu' ) );

    // Frontend AJAX actions.
    add_action('wp_ajax_bwg_frontend_data', array($this, 'frontend_data'));
    add_action('wp_ajax_nopriv_bwg_frontend_data', array($this, 'frontend_data'));
    add_action('wp_ajax_GalleryBox', array($this, 'frontend_ajax'));
    add_action('wp_ajax_nopriv_GalleryBox', array($this, 'frontend_ajax'));
    add_action('wp_ajax_bwg_captcha', array($this, 'bwg_captcha'));
    add_action('wp_ajax_nopriv_bwg_captcha', array($this, 'bwg_captcha'));
    if ( $this->is_pro ) {
      add_action('wp_ajax_Share', array( $this, 'frontend_ajax' ));
      add_action('wp_ajax_nopriv_Share', array( $this, 'frontend_ajax' ));
      add_action('wp_ajax_view_facebook_post', array($this, 'bwg_add_embed_ajax'));
      add_action('wp_ajax_nopriv_view_facebook_post', array($this, 'bwg_add_embed_ajax'));
      add_action('wp_ajax_nopriv_download_gallery', array($this, 'frontend_ajax'));
      add_action('wp_ajax_download_gallery', array($this, 'frontend_ajax'));
    }

    // Admin AJAX actions.
    add_action('wp_ajax_galleries_' . $this->prefix , array($this, 'admin_ajax'));
    add_action('wp_ajax_albumsgalleries_' . $this->prefix , array($this, 'admin_ajax'));
    add_action('wp_ajax_bwg_UploadHandler', array($this, 'bwg_UploadHandler'));
    add_action('wp_ajax_addImages', array($this, 'bwg_filemanager_ajax'));
    add_action('wp_ajax_addMusic', array($this, 'bwg_filemanager_ajax'));
    add_action('wp_ajax_addEmbed', array($this, 'bwg_add_embed_ajax'));
    add_action('wp_ajax_editimage_' . $this->prefix, array($this, 'admin_ajax'));
    add_action('wp_ajax_addTags_' . $this->prefix, array($this, 'admin_ajax'));
    add_action('wp_ajax_options_' . $this->prefix, array($this, 'admin_ajax'));
    if( $this->is_pro ) {
      add_action('wp_ajax_addInstagramGallery', array( $this, 'bwg_add_embed_ajax' ));
      add_action('wp_ajax_addFacebookGallery', array( $this, 'bwg_add_embed_ajax' ));
    }

    if ( !is_admin() ) {
      add_shortcode('Best_Wordpress_Gallery', array($this, 'shortcode'));
    }
    // Editor message dismiss.
    add_action('wp_ajax_bwg_editor_missing_dismissed', array($this, 'dismiss_notice'));
    add_action('wp_ajax_bwg_recreate_dismissed', array($this, 'dismiss_notice'));

    // Add media button to WP editor.
    add_action('wp_ajax_shortcode_' . $this->prefix, array($this, 'admin_ajax'));
    add_action('media_buttons', array($this, 'media_button'));
    add_filter('mce_external_plugins', array($this, 'bwg_register'));
    add_filter('mce_buttons', array($this, 'media_internal_button'), 0);

    // Add script to header.
    add_action('admin_head', array($this, 'global_script'));

    // Photo Gallery Widget.
    if ( class_exists('WP_Widget') ) {
      add_action('widgets_init', array($this, 'register_widgets'));
    }

    // Plugin activation.
    register_activation_hook(__FILE__, array($this, 'global_activate'));
    add_action('wpmu_new_blog', array($this, 'new_blog_added'), 10, 6);

    // Plugin update.
    if ( !isset($_GET['action']) || $_GET['action'] != 'deactivate' ) {
      add_action('admin_init', array($this, 'global_update'));
    }

    // Plugin deactivate.
    register_deactivation_hook( __FILE__, array($this, 'global_deactivate'));

    // Register scripts/styles.
    add_action('wp_enqueue_scripts', array($this, 'register_frontend_scripts'));
    add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));

    add_filter('set-screen-option', array($this, 'set_option_galleries'), 10, 3);
    add_filter('set-screen-option', array($this, 'set_option_albums'), 10, 3);
    add_filter('set-screen-option', array($this, 'set_option_themes'), 10, 3);
    add_filter('set-screen-option', array($this, 'set_option_comments'), 10, 3);
    add_filter('set-screen-option', array($this, 'set_option_rates'), 10, 3);

    if ( $this->is_pro ) {
      add_filter('single_template', array( $this, 'share_template' ));
    }

    add_filter('widget_tag_cloud_args', array($this, 'tag_cloud_widget_args'));

    if ( $this->is_pro ) {
      add_filter('cron_schedules', array( $this, 'autoupdate_interval' ));
      add_action('bwg_schedule_event_hook', array( $this, 'social_galleries' ));
    }

	  // Check add-ons versions.
    if ( $this->is_pro ) {
      add_action('admin_notices', array($this, 'check_addons_compatibility'));
    }
  	add_action('plugins_loaded', array($this, 'plugins_loaded'));
    // There is no instagram provider for https.
    wp_oembed_add_provider('#https://instagr(\.am|am\.com)/p/.*#i', 'https://api.instagram.com/oembed', TRUE);
    if ( !$this->is_pro ) {
      add_filter("plugin_row_meta", array($this, 'add_plugin_meta_links'), 10, 2);
    }

    // Enqueue block editor assets for Gutenberg.
    add_filter('tw_get_block_editor_assets', array($this, 'register_block_editor_assets'));
    add_filter('tw_get_plugin_blocks', array($this, 'register_plugin_block'));
    add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));

    add_action('admin_notices', array($this, 'admin_notices'));

	  // Privacy policy.
    add_action( 'admin_init', array($this, 'add_privacy_policy_content') );
    // Prevent adding shortcode conflict with some builders.

    $this->before_shortcode_add_builder_editor();

    // Register widget for Elementor builder.
    add_action('elementor/widgets/widgets_registered', array($this, 'register_elementor_widgets'));

    //fires after elementor editor styles and scripts are enqueued.
    add_action('elementor/editor/after_enqueue_styles', array($this, 'enqueue_editor_styles'), 11);

    add_action('elementor/editor/after_enqueue_scripts', array($this, 'enqueue_elementor_widget_scripts'));


    // Register 10Web category for Elementor widget if 10Web builder isn't installed.
    add_action('elementor/elements/categories_registered', array($this, 'register_widget_category'), 1, 1);

    // Add noindex/nofollow to custom posts to not allow search engines to index custom posts.
    add_action('wp_head', array($this, 'robots'), 9, 1);

    // Divi frontend builder assets.
    add_action('et_fb_enqueue_assets', array($this, 'enqueue_divi_bulder_assets'));
  	add_action('et_fb_enqueue_assets', array($this, 'global_script'));

    // Add Photo Gallery images to sitemap xml.
    require_once ($this->plugin_dir . '/framework/WDWSitemap.php');
    add_filter('wd_seo_sitemap_images', array( WDWSitemap::instance(), 'add_wpseo_xml_sitemap_images'), 10, 2);
    add_filter('wpseo_sitemap_urlimages', array( WDWSitemap::instance(), 'add_wpseo_xml_sitemap_images'), 10, 2);

    if ( !$this->is_pro ) {
      /* Add wordpress.org support custom link in plugin page */
      add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_ask_question_links' ));
    }
  }

  /**
   * Add plugin action links.
   *
   * Add a link to the settings page on the plugins.php page.
   *
   * @since 1.0.0
   *
   * @param  array  $links List of existing plugin action links.
   * @return array         List of modified plugin action links.
   */
  function add_ask_question_links ( $links ) {
    $slug = 'photo-gallery';
    $fm_ask_question_link = array('<a href="https://wordpress.org/support/plugin/' . $slug . '/#new-post" target="_blank">' . __('Help', $this->prefix) . '</a>');
    return array_merge( $links, $fm_ask_question_link );
  }

  public function enqueue_divi_bulder_assets() {
    wp_enqueue_style('thickbox');
  	wp_enqueue_script('thickbox');
  }

  /**
   * Add noindex/nofollow to custom posts to not allow search engines to index custom posts.
   */
  public function robots() {
    if ( isset($this->options->noindex_custom_post) && $this->options->noindex_custom_post ) {
      global $wp;
      $current_relative_url = trailingslashit($wp->request);
      if ( isset($_SERVER['QUERY_STRING']) ) {
        $current_relative_url = trailingslashit(add_query_arg($_SERVER['QUERY_STRING'], '', $current_relative_url));
      }
      if ( strpos($current_relative_url, 'bwg_gallery') !== FALSE
      || strpos($current_relative_url, 'bwg_album') !== FALSE
      || strpos($current_relative_url, 'bwg_tag') !== FALSE ) {
        echo '<meta name="robots" content="noindex,nofollow" />' . "\n";
      }
    }
  }

  public function enqueue_editor_styles() {
    wp_enqueue_style('twbb-editor-styles', $this->plugin_url . '/css/bwg_elementor_icon/bwg_elementor_icon.css', array(), '1.0.0');
  }

  /**
   * Register widget for Elementor builder.
   */
  public function register_elementor_widgets() {
    if ( defined('ELEMENTOR_PATH') && class_exists('Elementor\Widget_Base') ) {
      require_once ($this->plugin_dir . '/admin/controllers/elementorWidget.php');
    }
  }

  /**
   * Register 10Web category for Elementor widget if 10Web builder doesn't installed.
   *
   * @param $elements_manager
   */
  public function register_widget_category( $elements_manager ) {
    $elements_manager->add_category('tenweb-plugins-widgets', array(
      'title' => __('10WEB Plugins', 'tenweb-plugins-widgets'),
      'icon' => 'fa fa-plug',
    ));
  }

  public function register_block_editor_assets($assets) {
    $version = '2.0.6';
    $js_path = $this->plugin_url . '/js/tw-gb/block.js';
    $css_path = $this->plugin_url . '/css/tw-gb/block.css';
    if (!isset($assets['version']) || version_compare($assets['version'], $version) === -1) {
      $assets['version'] = $version;
      $assets['js_path'] = $js_path;
      $assets['css_path'] = $css_path;
    }
    return $assets;
  }

  function add_privacy_policy_content() {
    if ( !function_exists('wp_add_privacy_policy_content') ) {
      return;
    }
    $content = __('When you leave a comment on this site, we send your name, email
        address, IP address and comment text to example.com. Example.com does
        not retain your personal data.', BWG()->prefix);
    wp_add_privacy_policy_content(BWG()->nicename, wp_kses_post(wpautop($content, FALSE)));
  }

  public function register_plugin_block($blocks) {
    $blocks['tw/' . $this->prefix] = array(
      'title' => $this->nicename,
      'titleSelect' => sprintf(__('Select %s', $this->prefix), $this->nicename),
      'iconUrl' => $this->plugin_url . '/images/tw-gb/shortcode_new.jpg',
      'iconSvg' => array('width' => 20, 'height' => 20, 'src' => $this->plugin_url . '/images/tw-gb/icon.svg'),
      'isPopup' => true,
      'data' => array('shortcodeUrl' => add_query_arg(array('action' => 'shortcode_bwg'), admin_url('admin-ajax.php'))),
    );
    return $blocks;
  }

  public function enqueue_block_editor_assets() {
    // Remove previously registered or enqueued versions
    $wp_scripts = wp_scripts();
    foreach ($wp_scripts->registered as $key => $value) {
      // Check for an older versions with prefix.
      if (strpos($key, 'tw-gb-block') > 0) {
        wp_deregister_script( $key );
        wp_deregister_style( $key );
      }
    }
    // Get plugin blocks from all 10Web plugins.
    $blocks = apply_filters('tw_get_plugin_blocks', array());
    // Get the last version from all 10Web plugins.
    $assets = apply_filters('tw_get_block_editor_assets', array());
    // Not performing unregister or unenqueue as in old versions all are with prefixes.
    wp_enqueue_script('tw-gb-block', $assets['js_path'], array( 'wp-blocks', 'wp-element' ), $assets['version']);
    wp_localize_script('tw-gb-block', 'tw_obj_translate', array(
      'nothing_selected' => __('Nothing selected.', $this->prefix),
      'empty_item' => __('- Select -', $this->prefix),
      'blocks' => json_encode($blocks)
    ));
    wp_enqueue_style('tw-gb-block', $assets['css_path'], array( 'wp-edit-blocks' ), $assets['version']);
  }

  /**
   * Wordpress init actions.
   */
  public function init() {
    ob_start();
    $this->overview();
	add_action('init', array($this, 'language_load'));
    add_action('init', array($this, 'create_post_types'));
  }

  /**
   * Wordpress admin notice actions.
   */
  public function admin_notices() {
    // Show this notice only on Photo Gallery pages.
    if ( isset( $_GET['page'] ) && strpos( esc_html( $_GET['page'] ), '_bwg' ) !== FALSE ) {
      /**
       * possible values are 'editor_missing', 'editor_missing_dismissed', 'recreate_dismissed', false
       */
      $wp_editor_state = get_option( 'bwg_wp_editor_state' );
      // Check if host is ready to edit images.
      $this->wp_editor_exists = wp_image_editor_supports( array( 'methods' => array( 'resize' ) ) );
      $wp_editor_message = false;
      $wp_editor_new_state = false;
      if ( !$this->wp_editor_exists ) {
        // Editor missing and error notification is not dismissed.
        if ( false === $wp_editor_state || 'editor_missing' === $wp_editor_state ) {
          $wp_editor_message_action = 'bwg_editor_missing_dismissed';
          $wp_editor_message = '<p>' . sprintf(__('Image edit functionality is not supported by your web host. We highly recommend you to contact your hosting provider and ask them to enable %s library.', $this->prefix), '<b>' . __("PHP GD", $this->prefix) . '</b>') . '</p>';
          $wp_editor_message .= '<p>' . sprintf(__('Without image editing functions, image thumbnails will not be created, thus causing load time issues on published galleries. Furthermore, some of Photo Gallery\'s features, e.g. %s, %s, and %s, will not be available.', $this->prefix), '<b>' . __("crop", $this->prefix) . '</b>', '<b>' . __("edit", $this->prefix) . '</b>', '<b>' . __("rotate", $this->prefix) . '</b>') . '</p>';
          $wp_editor_new_state = 'editor_missing';
        }
      }
      else {
        // Editor exists, some error state was detected before and recreate thumbnails message is not dismissed.
        if ( false !== $wp_editor_state && 'recreate_dismissed' != $wp_editor_state ) {
          $options_url = admin_url('admin.php?page=options_bwg');
          $wp_editor_message_action = 'bwg_recreate_dismissed';
          $wp_editor_message = '<p>' . sprintf(__('Image edit functionality was just activated on your web host. Please go to %s, navigate to %s tab and press %s button.', $this->prefix), '<b><a href="' . $options_url . '" title="' . __("Options", $this->prefix) . '">' .  __("Options page", $this->prefix) . '</a></b>', '<b>' . __("General", $this->prefix) . '</b>', '<b>' . __("Recreate", $this->prefix) . '</b>') . '</p>';
          $wp_editor_new_state = 'recreate';
        }
      }
      if ( $wp_editor_new_state ) {
        update_option( 'bwg_wp_editor_state', $wp_editor_new_state );
      }
      if ( $wp_editor_message ) {
        ?>
        <div id="bwg_image_editor_notice" class="wd-notice bwg-notice notice notice-warning is-dismissible" data-action="<?php echo $wp_editor_message_action; ?>">
          <?php echo $wp_editor_message; ?>
        </div>
        <?php
      }
    }
  }

  /**
   * Dismiss Image editor messages.
   */
  public function dismiss_notice() {
    $action = WDWLibrary::get('action');
    $allowed_pages = array(
      'bwg_editor_missing_dismissed',
      'bwg_recreate_dismissed',
    );
    if ( !empty($action) && in_array($action, $allowed_pages) ) {
      $action = str_replace(BWG()->prefix . '_', '', $action);
      update_option( 'bwg_wp_editor_state', $action );
    }
    die();
  }

  /**
   * Plugin menu.
   */
  public function admin_menu() {
    $permissions = $this->is_pro ? $this->options->permissions : 'manage_options';
    $tags_permission = $this->is_pro && $this->options->tag_role ? $this->options->permissions : 'manage_options';
    $themes_permission = $this->is_pro && $this->options->theme_role ? $this->options->permissions : 'manage_options';
    $settings_permission = $this->is_pro && $this->options->settings_role ? $this->options->permissions : 'manage_options';
    $parent_slug = 'galleries_' . $this->prefix;
    add_menu_page($this->nicename, $this->nicename, $permissions, 'galleries_' . $this->prefix, array($this , 'admin_pages'), $this->plugin_url . '/images/icons/icon.png');

    $galleries_page = add_submenu_page($parent_slug, __('Add Galleries/Images', $this->prefix), __('Add Galleries/Images', $this->prefix), $permissions, 'galleries_' . $this->prefix, array($this , 'admin_pages'));
    add_action('load-' . $galleries_page, array($this, 'galleries_per_page_option'));

    $albums_page = add_submenu_page($parent_slug, __('Gallery Groups', $this->prefix), __('Gallery Groups', $this->prefix), $permissions, 'albums_' . $this->prefix, array($this , 'admin_pages'));
    add_action('load-' . $albums_page, array($this, 'albums_per_page_option'));

    add_submenu_page($parent_slug, __('Tags', $this->prefix), __('Tags', $this->prefix), $tags_permission, 'edit-tags.php?taxonomy=bwg_tag');

    add_submenu_page($parent_slug, __('Global Settings', $this->prefix), __('Global Settings', $this->prefix), $settings_permission, 'options_' . $this->prefix, array($this , 'admin_pages'));

    $themes_page = add_submenu_page($parent_slug, __('Themes', $this->prefix), __('Themes', $this->prefix), $themes_permission, 'themes_' . $this->prefix, array($this , 'admin_pages'));
    add_action('load-' . $themes_page, array($this, 'themes_per_page_option'));

    if( $this->is_pro ) {
      $comments_page = add_submenu_page($parent_slug, __('Comments', $this->prefix), __('Comments', $this->prefix), 'manage_options', 'comments_' . $this->prefix, array($this , 'admin_pages'));
      add_action('load-' . $comments_page, array($this, 'comments_per_page_option'));

      $rates_page = add_submenu_page($parent_slug, __('Ratings', $this->prefix), __('Ratings', $this->prefix), 'manage_options', 'ratings_' . $this->prefix, array($this , 'admin_pages'));
      add_action('load-' . $rates_page, array($this, 'rates_per_page_option'));
    }
    else {
      // Temporary deactivated.
      // add_submenu_page($parent_slug, __('Premium Version', $this->prefix), __('Premium Version', $this->prefix), 'manage_options', 'licensing_' . $this->prefix, array($this , 'admin_pages'));
    }

    do_action('bwg_add_submenu_item', $parent_slug);
    
    add_submenu_page($parent_slug, __('Add-ons',$this->prefix), __('Add-ons', $this->prefix), 'manage_options', 'addons_' . $this->prefix, array($this , 'addons'));

    add_submenu_page(NULL, __('Uninstall', $this->prefix), __('Uninstall', $this->prefix), 'manage_options', 'uninstall_' . $this->prefix, array($this , 'admin_pages'));
    add_submenu_page(NULL, __('Generate Shortcode', $this->prefix), __('Generate Shortcode', $this->prefix), $permissions, 'shortcode_' . $this->prefix, array($this , 'admin_pages'));

    if ( !$this->is_pro && current_user_can( $permissions ) ) {
      /* Custom link to wordpress.org*/
      global $submenu;
      $slug = 'photo-gallery';
      $submenu[$parent_slug][] = array(
        '<div id="bwg_ask_question">' . __('Ask a question', $this->prefix) . '</div>',
        'manage_options',
        'https://wordpress.org/support/plugin/' . $slug . '/#new-post',
      );
    }
  }

  /**
   * Admin pages.
   */
  public function admin_pages() {
    $allowed_pages = array(
      'galleries_' . $this->prefix,
      'albums_' . $this->prefix,
      'options_' . $this->prefix,
      'themes_' . $this->prefix,
      'comments_' . $this->prefix,
      'ratings_' . $this->prefix,
      'uninstall_' . $this->prefix,
      'shortcode_' . $this->prefix,
      'licensing_' . $this->prefix,
    );
    $page = WDWLibrary::get('page');
    if ( !empty($page) && in_array($page, $allowed_pages) ) {
      $page = WDWLibrary::clean_page_prefix($page);
      $controller_page = $this->plugin_dir . '/admin/controllers/' . $page . '.php';
      $model_page = $this->plugin_dir . '/admin/models/' . $page . '.php';
      $view_page = $this->plugin_dir . '/admin/views/' . $page . '.php';
      if ( !is_file($controller_page) ) {
        echo wp_sprintf(__('The controller %s file not exist.', $this->prefix), '"<b>' . $page . '</b>"');

        return FALSE;
      }
      if ( !is_file($view_page) ) {
        echo wp_sprintf(__('The view %s file not exist.', $this->prefix), '"<b>' . $page . '</b>"');

        return FALSE;
      }
      // Load page file.
      require_once($this->plugin_dir . '/admin/views/AdminView.php');
      require_once($controller_page);
      if ( is_file($model_page) ) {
        require_once($model_page);
      }
      require_once($view_page);
      $controller_class = $page . 'Controller_' . $this->prefix;
      $model_class = $page . 'Model_' . $this->prefix;
      $view_class = $page . 'View_' . $this->prefix;
      // Checking page class.
      if ( !class_exists($controller_class) ) {
        echo wp_sprintf(__('The %s class not exist.', $this->prefix), '"<b>' . $controller_class . '</b>"');

        return FALSE;
      }
      if ( !class_exists($view_class) ) {
        echo wp_sprintf(__('The %s class not exist.', $this->prefix), '"<b>' . $view_class . '</b>"');

        return FALSE;
      }
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  /**
   * Add-ons page.
   */
  public function addons() {
    if (function_exists('current_user_can')) {
      if (!current_user_can('manage_options')) {
        die('Access Denied');
      }
    }
    else {
      die('Access Denied');
    }
    require_once($this->plugin_dir . '/addons/addons.php');
    bwg_addons_display();
  }

  /**
   * Register admin pages scripts/styles.
   */
  public function register_admin_scripts() {
    $required_scripts = array( 'jquery' );
    $required_styles = array(
      // 'admin-bar',
      // 'dashicons',
      'wp-admin', // admin styles
      'buttons', // buttons styles
      'media-views', // media uploader styles
      'wp-auth-check', // check all
    );
    if ( $this->is_pro ) {
      wp_register_style($this->prefix . '_fontselect', $this->plugin_url . '/js/fontselect/styles/fontselect-default.css', $required_styles, $this->plugin_version);
      array_push($required_styles, $this->prefix . '_fontselect');
      wp_register_script($this->prefix . '_fontselect', $this->plugin_url . '/js/fontselect/jquery.fontselect.js', $required_scripts, $this->plugin_version);
    }
    wp_register_style($this->prefix . '_tables', $this->plugin_url . '/css/bwg_tables.css', $required_styles, $this->plugin_version);
    wp_register_style($this->prefix . '_gallery-upgrade', $this->plugin_url . '/css/gallery-upgrade.css', $required_styles, $this->plugin_version);

    wp_register_script($this->prefix . '_admin', $this->plugin_url . '/js/bwg.js', $required_scripts, $this->plugin_version);
    wp_localize_script($this->prefix . '_admin', 'bwg', array(
      'delete_confirmation' => __('Do you want to delete selected items?', $this->prefix),
      'select_at_least_one_item' => __('You must select at least one item.', $this->prefix),
      'remove_pricelist_confirmation' => __('Do you want to remove pricelist from selected items?', $this->prefix),
      'google_fonts' => WDWLibrary::get_google_fonts(),
      'bwg_premium_text' => __(' view is<br>available in Premium Version', $this->prefix),
    ));

    wp_register_script($this->prefix . '_embed', $this->plugin_url . '/js/bwg_embed.js', array('jquery'), $this->plugin_version);

    wp_localize_script($this->prefix . '_admin', 'bwg_objectL10B', array(
      'bwg_field_required'  => __('field is required.', $this->prefix),
      'bwg_select_image'  => __('You must select an image file.', $this->prefix),
      'bwg_select_audio'  => __('You must select an audio file.', $this->prefix),
      'bwg_access_token'  => __('You do not have Instagram access token. Sign in with Instagram in Options -> Advanced tab -> Social. ', $this->prefix),
      'bwg_client_id' => __('You do not have Instagram CLIENT_ID. Input its value in Options->Embed options.', $this->prefix),
      'bwg_post_number'  => __('Instagram recent post number must be between 1 and 25.', $this->prefix),
      'bwg_not_empty'  => __('Gallery type cannot be changed, since it is not empty. In case you would like to have Instagram gallery, please create a new one.', $this->prefix),
      'bwg_enter_url'  => __('Please enter url to embed.', $this->prefix),
      'bwg_cannot_response'  => __('Error: cannot get response from the server.', $this->prefix),
      'bwg_something_wrong'  => __('Error: something wrong happened at the server.', $this->prefix),
      'bwg_error'  => __('Error', $this->prefix),
      'bwg_show_order'  => __('Show order column', $this->prefix),
      'bwg_hide_order'  => __('Hide order column', $this->prefix),
      'selected_item'  =>  __('Selected %d item.', $this->prefix),
      'selected_items'  =>  __('Selected %d items.', $this->prefix),
      'saved'  => __('Items Succesfully Saved.', $this->prefix),
      'recovered'  => __('Item Succesfully Recovered.', $this->prefix),
      'published'  => __('Item Succesfully Published.', $this->prefix),
      'unpublished'  => __('Item Succesfully Unpublished.', $this->prefix),
      'deleted'  => __('Item Succesfully Deleted.', $this->prefix),
      'one_item'  => __('You must select at least one item.', $this->prefix),
      'resized'  => __('Items Succesfully resized.', $this->prefix),
      'watermark_set'  => __('Watermarks Succesfully Set.', $this->prefix),
      'reset'  => __('Items Succesfully Reset.', $this->prefix),
      'save_tag' => __('Save Tag', $this->prefix),
      'delete_alert' => __('Do you want to delete selected items?', $this->prefix),
      'default_warning' => __('This action will reset gallery type to mixed and will save that choice. You cannot undo it.', $this->prefix),
      'change_warning' => __('After pressing save/apply buttons, you cannot change gallery type back to Instagram!', $this->prefix),
      'other_warning' => __('This action will reset gallery type to mixed and will save that choice. You cannot undo it.', $this->prefix),
      'insert' => __('Insert', $this->prefix),
      'import_failed' => __('Failed to import images from media library', $this->prefix),
      'only_the_following_types_are_allowed' => __('Sorry, only jpg, jpeg, gif, png types are allowed.', $this->prefix),
      'wp_upload_dir' => wp_upload_dir(),
      'ajax_url' => wp_nonce_url( admin_url('admin-ajax.php'), 'bwg_UploadHandler', 'bwg_nonce' ),
      'uploads_url' => BWG()->options->upload_url,
      'recreate_success' => __('Thumbnails successfully recreated.', $this->prefix),
      'watermark_option_reset' => __('All images are successfully reset.', $this->prefix),
    ));
    wp_localize_script($this->prefix . '_admin', 'bwg_objectGGF', WDWLibrary::get_google_fonts());
    wp_enqueue_script('jquery-ui-sortable');
    wp_register_script($this->prefix . '_jscolor', $this->plugin_url . '/js/jscolor/jscolor.js', array('jquery'), '1.3.9');

    wp_register_style($this->prefix . '_addons', $this->plugin_url . '/addons/style.css');

    // Open Sans
    wp_register_style($this->prefix . '-opensans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800&display=swap');
    wp_enqueue_style($this->prefix . '-opensans');

    wp_register_style($this->prefix . '_shortcode', $this->plugin_url . '/css/bwg_shortcode.css', $required_styles, $this->plugin_version);
    wp_register_script($this->prefix . '_shortcode', $this->plugin_url . '/js/bwg_shortcode.js', $required_scripts, $this->plugin_version);
    wp_localize_script($this->prefix . '_shortcode', 'bwg_objectGGF', WDWLibrary::get_google_fonts());
    wp_localize_script($this->prefix . '_shortcode', 'bwg_premium_text', __(' view is<br>available in Premium Version', $this->prefix));

    if ( !$this->is_pro ) {
      wp_register_style($this->prefix . '_licensing', $this->plugin_url . '/css/bwg_licensing.css', $required_styles, $this->plugin_version);
    }

    // Roboto font for top bar.
    wp_register_style($this->prefix . '-roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap');
    wp_register_style($this->prefix . '-pricing', $this->plugin_url . '/css/pricing.css', array(), $this->plugin_version);

    // For drag and drop on mobiles.
    wp_register_script($this->prefix . '_jquery.ui.touch-punch.min', $this->plugin_url . '/js/jquery.ui.touch-punch.min.js', array(), '0.2.3');

    $current_screen = get_current_screen();
    if ( !$this->is_pro && !empty($current_screen->id) && $current_screen->id == "toplevel_page_bwg_subscribe" ) {
      wp_enqueue_style($this->prefix . '_subscribe', $this->plugin_url . '/css/bwg_subscribe.css', array(), $this->plugin_version);
    }
  }

  /**
   * Frontend AJAX actions.
   */
  public function frontend_data() {
    $params = array();
    $params['id'] = WDWLibrary::get('shortcode_id', 0, 'intval');

    // Get values for elementor widget.
    $params['gallery_type'] = WDWLibrary::get('gallery_type', 'thumbnails');
    $params['gallery_id'] = WDWLibrary::get('gallery_id', 0);
    $params['tag'] = WDWLibrary::get('tag', 0);
    $params['album_id'] = WDWLibrary::get('album_id', 0);
    $params['theme_id'] = WDWLibrary::get('theme_id', 0);
    $params['current_url'] = WDWLibrary::get('current_url', NULL);
    $params['ajax'] = TRUE;

    echo $this->shortcode($params);

    die();
  }

  /**
   * Frontend AJAX actions.
   */
  public function frontend_ajax() {
    if (function_exists('switch_to_locale') && function_exists('get_locale')) {
      switch_to_locale(get_locale());
    }

    $allowed_pages = array(
      'GalleryBox',
      'Share',
      'download_gallery',
    );
    $page = WDWLibrary::get('action');
    if ( !empty($page) && in_array($page, $allowed_pages) ) {
      require_once($this->plugin_dir . '/frontend/controllers/BWGController' . ucfirst($page) . '.php');
      $controller_class = 'BWGController' . ucfirst($page);
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  public function shortcode( $params = array() ) {
    if ( is_admin() && defined('DOING_AJAX') && !DOING_AJAX) {
      return;
    }
    if ( isset($params['id']) && $params['id'] ) {
      global $wpdb;
      $shortcode = $wpdb->get_var($wpdb->prepare("SELECT tagtext FROM " . $wpdb->prefix . "bwg_shortcode WHERE id='%d'", $params['id']));
      if ($shortcode) {
        $shortcode_params = explode('" ', $shortcode);
        foreach ($shortcode_params as $shortcode_param) {
          $shortcode_param = str_replace('"', '', $shortcode_param);
          $shortcode_elem = explode('=', $shortcode_param);
          $params[str_replace(' ', '', $shortcode_elem[0])] = $shortcode_elem[1];
        }
      }
      else {
        return;
      }
    }

    // 'gallery_type' is the only parameter not being checked.
    // Checking for incomplete shortcodes.
    $gallery_allowed_types = WDWLibrary::get_gallery_allowed_types();
    if ( isset($params['gallery_type']) && in_array($params['gallery_type'], $gallery_allowed_types) ) {
      $pairs = WDWLibrary::get_shortcode_option_params( $params );
      if ( isset($params['ajax']) ) {
        $pairs['ajax'] = $params['ajax'];
      }
      ob_start();
      $this->front_end( $pairs );
      return str_replace( array( "\r\n", "\n", "\r" ), '', ob_get_clean() );
    }
  }

  /**
   * Frontend.
   *
   * @param $params
   */
  public function front_end($params) {
    require_once(BWG()->plugin_dir . '/framework/WDWLibraryEmbed.php');
    require_once(BWG()->plugin_dir . '/frontend/controllers/controller.php');
    $controller = new BWGControllerSite( ucfirst( $params[ 'gallery_type' ] ) );
    if ( WDWLibrary::get('shortcode_id', 0) || isset($params['ajax']) ) {
      $controller->execute($params, 1, WDWLibrary::get('bwg', 0, 'intval'));
    }
    else {
      $bwg = WDWLibrary::unique_number();
      $controller->execute($params, 1, $bwg);
    }

    return;
  }

  // TODO: move
  public function bwg_captcha() {
    if ( WDWLibrary::get('action') == 'bwg_captcha') {
      $i = WDWLibrary::get('i');
      $r2 = WDWLibrary::get('r2', 0, 'intval');
      $rrr = WDWLibrary::get('rrr', 0, 'intval');
      $randNum = 0 + $r2 + $rrr;
      $digit = WDWLibrary::get('digit', 0, 'intval');
      $cap_width = $digit * 10 + 15;
      $cap_height = 26;
      $cap_length_min = $digit;
      $cap_length_max = $digit;
      $cap_digital = 1;
      $cap_latin_char = 1;
      function code_generic($_length, $_digital = 1, $_latin_char = 1) {
        $dig = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $lat = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
        $main = array();
        if ($_digital) {
          $main = array_merge($main, $dig);
        }
        if ($_latin_char) {
          $main = array_merge($main, $lat);
        }
        shuffle($main);
        $pass = substr(implode('', $main), 0, $_length);
        return $pass;
      }
      $l = rand($cap_length_min, $cap_length_max);
      $code = code_generic($l, $cap_digital, $cap_latin_char);
      WDWLibrary::bwg_session_start();
      $_SESSION['bwg_captcha_code'] = $code;
      if (function_exists('imagecreatetruecolor')) {
        $canvas = imagecreatetruecolor( $cap_width, $cap_height );
        $c = imagecolorallocate( $canvas, rand( 150, 255 ), rand( 150, 255 ), rand( 150, 255 ) );
        imagefilledrectangle( $canvas, 0, 0, $cap_width, $cap_height, $c );
        $count = strlen( $code );
        $color_text = imagecolorallocate( $canvas, 0, 0, 0 );
        for ( $it = 0; $it < $count; $it++ ) {
          $letter = $code[ $it ];
          imagestring( $canvas, 6, (10 * $it + 10), $cap_height / 4, $letter, $color_text );
        }
        for ( $c = 0; $c < 150; $c++ ) {
          $x = rand( 0, $cap_width - 1 );
          $y = rand( 0, 29 );
          $col = '0x' . rand( 0, 9 ) . '0' . rand( 0, 9 ) . '0' . rand( 0, 9 ) . '0';
          imagesetpixel( $canvas, $x, $y, $col );
        }
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
        header( 'Pragma: no-cache' );
        header( 'Content-Type: image/jpeg' );
        imagejpeg( $canvas, NULL, BWG()->options->jpeg_quality );
      }
      die('');
    }
  }
  // TODO: move
  public function bwg_add_embed_ajax() {
    $permissions = $this->is_pro ? BWG()->options->permissions : 'manage_options';
    if (function_exists('current_user_can')) {
      if (!current_user_can($permissions)) {
        die('Access Denied');
      }
    }
    else {
      die('Access Denied');
    }
    require_once(BWG()->plugin_dir . '/framework/WDWLibrary.php');
    if (!WDWLibrary::verify_nonce('')) {
      die(WDWLibrary::delimit_wd_output(json_encode(array("error", "Sorry, your nonce did not verify."))));
    }

    require_once(BWG()->plugin_dir . '/framework/WDWLibraryEmbed.php');
    $embed_action = WDWLibrary::get('action');
    switch ( $embed_action ) {
      case 'addEmbed':
        $url_to_embed = WDWLibrary::get('URL_to_embed');
        $data = WDWLibraryEmbed::add_embed($url_to_embed);
        echo WDWLibrary::delimit_wd_output($data);
        wp_die();
        break;
      case 'addInstagramGallery':
        $instagram_access_token = WDWLibrary::get('instagram_access_token');
        $autogallery_image_number = WDWLibrary::get('autogallery_image_number');
        $whole_post = WDWLibrary::get('whole_post');
        $data = WDWLibraryEmbed::add_instagram_gallery($instagram_access_token, $whole_post, $autogallery_image_number);
        if ( !$data ) {
          echo WDWLibrary::delimit_wd_output(json_encode(array( "error", "Cannot get instagram data" )));
        }
        if ( $data ) {
          $images_new = json_decode($data, TRUE);
          if ( empty($images_new) ) {
            echo WDWLibrary::delimit_wd_output(json_encode(array( "error", "Cannot get instagram data" )));
          }
          else {
            echo WDWLibrary::delimit_wd_output($data);
          }
        }
        wp_die();
        break;
      case 'addFacebookGallery':
        $arg = array(
          'app_id' => WDWLibrary::get('app_id'),
          'app_secret' => WDWLibrary::get('app_secret'),
          'album_url' => WDWLibrary::get('album_url'),
          'album_limit' => WDWLibrary::get('album_limit'),
          'update_flag' => WDWLibrary::get('update_flag'),
          'content_type' => WDWLibrary::get('content_type'),
        );
        if ( has_filter('init_facebook_album_data_bwg') ) {
          $data = apply_filters('init_facebook_album_data_bwg', array(), $arg);
          echo json_encode($data);
        }
        wp_die();
        break;
      default:
        die('Nothing to add');
        break;
    }
  }

  public function admin_ajax() {
    $page = WDWLibrary::get('action');
    if ( $page == 'shortcode_' . $this->prefix ) {
      $permissions = 'edit_posts';
    }
    else {
      $permissions = $this->is_pro ? BWG()->options->permissions : 'manage_options';
    }
    if ( function_exists('current_user_can') ) {
      if ( !current_user_can($permissions) ) {
        die('Access Denied');
      }
    }
    else {
      die('Access Denied');
    }
    $allowed_pages = array(
      'galleries_' . $this->prefix,
      'albumsgalleries_' . $this->prefix,
      'addTags_' . $this->prefix,
      'shortcode_' . $this->prefix,
      'editimage_' . $this->prefix,
      'options_' . $this->prefix,
    );
    if ( !empty($page) && in_array($page, $allowed_pages) ) {
      $page = WDWLibrary::clean_page_prefix($page);
      $controller_page = $this->plugin_dir . '/admin/controllers/' . $page . '.php';
      $model_page = $this->plugin_dir . '/admin/models/' . $page . '.php';
      $view_page = $this->plugin_dir . '/admin/views/' . $page . '.php';
      // Load page file.
      require_once($this->plugin_dir . '/admin/views/AdminView.php');
      require_once($controller_page);
      require_once($model_page);
      require_once($view_page);
      $controller_class = $page . 'Controller_' . $this->prefix;
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  // TODO:
  public function bwg_UploadHandler() {
    require_once(BWG()->plugin_dir . '/framework/WDWLibrary.php');
    if(!WDWLibrary::verify_nonce('bwg_UploadHandler')){
      die('Sorry, your nonce did not verify.');
    }
    require_once(BWG()->plugin_dir . '/filemanager/UploadHandler.php');
  }
  // TODO:
  public function bwg_filemanager_ajax() {
    $permissions = $this->is_pro ? BWG()->options->permissions : 'manage_options';
    if (function_exists('current_user_can')) {
      if (!current_user_can($permissions)) {
        die('Access Denied');
      }
    }
    else {
      die('Access Denied');
    }
    require_once(BWG()->plugin_dir . '/framework/WDWLibrary.php');
    $page = WDWLibrary::get('action');
    if (($page != '') && (($page == 'addImages') || ($page == 'addMusic'))) {
      if (!WDWLibrary::verify_nonce($page)) {
        die('Sorry, your nonce did not verify.');
      }
      require_once(BWG()->plugin_dir . '/filemanager/controller.php');
      $controller_class = 'FilemanagerController';
      $controller = new $controller_class();
      $controller->execute();
    }
  }

  /**
    * Register Photo Gallery button.
    *
    * @param $plugin_array
    *
    * @return mixed
    */
  public function bwg_register($plugin_array) {
    $url = BWG()->plugin_url . '/js/bwg_editor_button.js';
    $plugin_array["bwg_mce"] = $url;

    return $plugin_array;
  }

  /**
   * Add media button to Wp editor.
   *
   * @return string
   */
  function media_button() {
    ob_start();
    $url = add_query_arg(array('action' => 'shortcode_bwg', 'TB_iframe' => '1'), admin_url('admin-ajax.php'));
	  ?>
    <a onclick="if ( typeof tb_click == 'function' && ( jQuery(this).parent().attr('id') && jQuery(this).parent().attr('id').indexOf('elementor') !== -1 || typeof bwg_check_ready == 'function') ) {
            tb_click.call(this);
            bwg_create_loading_block();
            bwg_set_shortcode_popup_dimensions(); } return false;" href="<?php echo $url; ?>" class="bwg-shortcode-btn button" title="<?php _e('Insert Photo Gallery', $this->prefix); ?>">
      <span class="wp-media-buttons-icon" style="background: url(<?php echo $this->plugin_url; ?>/images/icons/bwg_edit_but.png) no-repeat scroll left top rgba(0, 0, 0, 0);"></span>
      <?php _e('Add Photo Gallery', $this->prefix); ?>
    </a>
    <?php
    echo ob_get_clean();
  }

  /**
   * Add media button to visual editor.
   *
   * @param $buttons
   *
   * @return mixed
   */
  function media_internal_button($buttons) {
    array_push($buttons, "bwg_mce");

    return $buttons;
  }

  /**
   * Add script to header.
   */
  public function global_script() {
	  ?>
    <script>
      var bwg_admin_ajax = '<?php echo add_query_arg(array('action' => 'shortcode_' . $this->prefix), admin_url('admin-ajax.php')); ?>';
      var bwg_ajax_url = '<?php echo add_query_arg(array('action' => ''), admin_url('admin-ajax.php')); ?>';
      var bwg_plugin_url = '<?php echo BWG()->plugin_url; ?>';
      document.addEventListener('DOMContentLoaded', function(){ // Analog of $(document).ready(function(){
        bwg_check_ready = function () {}
        document.onkeyup = function(e){
          if ( e.key == 'Escape' ) {
            bwg_remove_loading_block();
          }
        };
      });

      // Set shortcode popup dimensions.
      function bwg_set_shortcode_popup_dimensions() {
        var H = jQuery(window).height(), W = jQuery(window).width();
        jQuery("#TB_title").hide().first().show();
        // New
        var tbWindow = jQuery('#TB_window');
        if (tbWindow.size()) {
          tbWindow.width(W).height(H);
          jQuery('#TB_iframeContent').width(W).height(H);
          tbWindow.attr('style',
            'top:'+ '0px !important;' +
            'left:' + '0px !important;' +
            'margin-left:' + '0;' +
            'z-index:' + '1000500;' +
            'max-width:' + 'none;' +
            'max-height:' + 'none;' +
            '-moz-transform:' + 'none;' +
            '-webkit-transform:' + 'none'
          );
        }
        // Edit
        var tbWindow = jQuery('.mce-window[aria-label="Photo Gallery"]');
        if (tbWindow.length) {
          // To prevent wp centering window with old sizes.
          setTimeout(function() {
            tbWindow.width(W).height(H);
            tbWindow.css({'top': 0, 'left': 0, 'margin-left': '0', 'z-index': '1000500'});
            tbWindow.find('.mce-window-body').width(W).height(H);
          }, 10);
        }
      }
      // Create loading block.
      function bwg_create_loading_block() {
        jQuery('body').append('<div class="loading_div" style="display:block; width: 100%; height: 100%; opacity: 0.6; position: fixed; background-color: #000000; background-image: url('+ bwg_plugin_url +'/images/spinner.gif); background-position: center; background-repeat: no-repeat; background-size: 50px; z-index: 1001000; top: 0; left: 0;"></div>');
      }
      // Remove loading block.
      function bwg_remove_loading_block() {
        jQuery(".loading_div", window.parent.document).remove();
        jQuery('.loading_div').remove();
      }
	  </script>
    <?php
  }

  /**
   * Register widget.
   */
  public function register_widgets() {
    require_once(BWG()->plugin_dir . '/framework/WDWLibraryEmbed.php');
    require_once(BWG()->plugin_dir . '/admin/controllers/Widget.php');
    register_widget("WidgetController_bwg");
    require_once(BWG()->plugin_dir . '/admin/controllers/WidgetSlideshow.php');
    register_widget("WidgetSlideshowController_bwg");
    if ( $this->is_pro ) {
      require_once(BWG()->plugin_dir . '/admin/controllers/WidgetTags.php');
      register_widget("WidgetTagsController_bwg");
    }
    // Allow to work old widgets registered with this name of class added with SiteOrigin builder.
    register_widget("BWGControllerWidget");
    register_widget("BWGControllerWidgetSlideshow");
    if ( $this->is_pro ) {
      register_widget("BWGControllerWidgetTags");
    }
  }

  /**
   * Global activate.
   *
   * @param $networkwide
   */
  public function global_activate($networkwide) {
    if ( function_exists('is_multisite') && is_multisite() ) {
      // Check if it is a network activation - if so, run the activation function for each blog id.
      if ( $networkwide ) {
        global $wpdb;
        // Get all blog ids.
        $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ( $blogids as $blog_id ) {
          switch_to_blog($blog_id);
          $this->activate();
          restore_current_blog();
        }

        return;
      }
    }
    $this->activate();
  }

  /**
   * Activate.
   */
  public function activate() {
    if ( $this->is_pro ) {
      delete_transient('bwg_update_check');
      wp_schedule_event(time(), 'bwg_autoupdate_interval', 'bwg_schedule_event_hook');
    }
    $version = get_option('wd_bwg_version');
    $new_version = $this->db_version;
    if ($version && version_compare($version, $new_version, '<')) {
      require_once BWG()->plugin_dir . "/update.php";
      BWGUpdate::tables($version);
      update_option("wd_bwg_version", $new_version);
      delete_user_meta(get_current_user_id(), 'bwg_photo_gallery');
    }
    elseif (!$version) {
      require_once $this->plugin_dir . "/insert.php";
      BWGInsert::tables();
      update_user_meta(get_current_user_id(),'bwg_photo_gallery', '1');
      add_option("wd_bwg_version", $new_version, '', 'no');
      add_option("wd_bwg_initial_version", $new_version, '', 'no');
      if ( !$this->is_pro ) {
        add_option("wd_bwg_theme_version", '1.0.0', '', 'no');
      }
    }
    else {
      require_once $this->plugin_dir . "/insert.php";
      BWGInsert::tables();
      add_option("wd_bwg_version", $new_version, '', 'no');
      if ( !$this->is_pro ) {
        add_option("wd_bwg_theme_version", '1.0.0', '', 'no');
      }
    }
    $this->create_post_types();
    // Using this insted of flush_rewrite_rule() for better performance with multisite.
    global $wp_rewrite;
    $wp_rewrite->init();
    $wp_rewrite->flush_rules();
  }

  /**
   * Global deactivate.
   *
   * @param $networkwide
   */
  public function global_deactivate($networkwide) {
    if ( function_exists('is_multisite') && is_multisite() ) {
      if ( $networkwide ) {
        global $wpdb;
        // Check if it is a network activation - if so, run the activation function for each blog id.
        // Get all blog ids.
        $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ( $blogids as $blog_id ) {
          switch_to_blog($blog_id);
          $this->deactivate();
          restore_current_blog();
        }

        return;
      }
    }
    $this->deactivate();
  }

  /**
   * Deactivate.
   */
  public function deactivate() {
    wp_clear_scheduled_hook( 'bwg_schedule_event_hook' );
    // Using this insted of flush_rewrite_rule() for better performance with multisite.
    global $wp_rewrite;
    $wp_rewrite->init();
    $wp_rewrite->flush_rules();
  }

  public function new_blog_added( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    if ( is_plugin_active_for_network('photo-gallery/photo-gallery.php') ) {
      switch_to_blog($blog_id);
      $this->activate();
      restore_current_blog();
    }
  }

  /**
   * Global update.
   */
  public function global_update() {
    if (function_exists('is_multisite') && is_multisite()) {
      global $wpdb;
      // Get all blog ids.
      $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
      foreach ($blogids as $blog_id) {
        switch_to_blog($blog_id);
        $this->update_hook();
        restore_current_blog();
      }
      return;
    }
    $this->update_hook();
  }

  /**
   * Update.
   */
  public function update_hook() {
    $version = get_option('wd_bwg_version');
    $new_version = $this->db_version;
    if ( $version && version_compare($version, $new_version, '<') ) {
      require_once BWG()->plugin_dir . "/update.php";
      BWGUpdate::tables($version);
      update_option("wd_bwg_version", $new_version);
    }
  }

  /**
   * Add pagination to gallery admin pages.
   */
  public function galleries_per_page_option() {
    $option = 'per_page';
    $args_galleries = array(
      'default' => 20,
      'option' => 'bwg_galleries_per_page',
    );
    add_screen_option($option, $args_galleries);
  }
  public function albums_per_page_option() {
    $option = 'per_page';
    $args_albums = array(
      'default' => 20,
      'option' => 'bwg_albums_per_page'
    );
    add_screen_option( $option, $args_albums );
  }
  public function themes_per_page_option() {
    $option = 'per_page';
    $args_themes = array(
      'default' => 20,
      'option' => 'bwg_themes_per_page',
    );
    add_screen_option($option, $args_themes);
  }
  public function comments_per_page_option() {
    $option = 'per_page';
    $args_comments = array(
      'default' => 20,
      'option' => 'bwg_comments_per_page',
    );
    add_screen_option($option, $args_comments);
  }
  public function rates_per_page_option() {
    $option = 'per_page';
    $args_rates = array(
      'default' => 20,
      'option' => 'bwg_rates_per_page',
    );
    add_screen_option($option, $args_rates);
  }
  public function set_option_galleries( $status, $option, $value ) {
    if ( 'bwg_galleries_per_page' == $option ) {
      return $value;
    }

    return $status;
  }
  public function set_option_albums( $status, $option, $value ) {
    if ( 'bwg_albums_per_page' == $option ) {
      return $value;
    }

    return $status;
  }
  public function set_option_themes( $status, $option, $value ) {
    if ( 'bwg_themes_per_page' == $option ) {
      return $value;
    }

    return $status;
  }
  public function set_option_comments( $status, $option, $value ) {
    if ( 'bwg_comments_per_page' == $option ) {
      return $value;
    }

    return $status;
  }
  public function set_option_rates( $status, $option, $value ) {
    if ( 'bwg_rates_per_page' == $option ) {
      return $value;
    }

    return $status;
  }

  /**
   * Register frontend scripts and styles.
   */
  public function register_frontend_scripts() {
    $version = BWG()->plugin_version;
    $required_styles = array(
	  $this->prefix . '_fonts',
      'sumoselect',
      'mCustomScrollbar'
    );
  	$required_scripts = array('jquery');
  	$in_footer = BWG()->options->use_inline_stiles_and_scripts || WDWLibrary::elementor_is_active() ? true : false;
	  // Google fonts.
    if (BWG()->options->enable_google_fonts) {
      require_once(BWG()->plugin_dir . '/framework/WDWLibrary.php');
      $google_fonts_link = WDWLibrary::get_all_used_google_fonts();
      if (!empty($google_fonts_link)) {
        wp_register_style($this->prefix . '_googlefonts', $google_fonts_link, null, null);
        array_push($required_styles, $this->prefix . '_googlefonts');
      }
    }
    wp_register_script('instagram-embed', 'https://www.instagram.com/embed.js', $required_scripts, '', $in_footer);
    wp_register_script('sumoselect', BWG()->front_url . '/js/jquery.sumoselect.min.js', $required_scripts, '3.0.3', $in_footer);
    wp_register_style('sumoselect', BWG()->front_url . '/css/sumoselect.min.css', array(), '3.0.3');

    // Styles/Scripts for popup.
    wp_register_style($this->prefix . '_fonts', BWG()->front_url . '/css/bwg-fonts/fonts.css', array(), '0.0.1');
    // jquery.mobile js file contain "Defaults, Namespace, Events All" selected from  https://jquerymobile.com/download-builder/
    wp_register_script('jquery-mobile', BWG()->front_url . '/js/jquery.mobile.min.js', $required_scripts, '1.4.5', $in_footer);
    wp_register_script('mCustomScrollbar', BWG()->front_url . '/js/jquery.mCustomScrollbar.concat.min.js', $required_scripts, $version, $in_footer);
    wp_register_style('mCustomScrollbar', BWG()->front_url . '/css/jquery.mCustomScrollbar.min.css', array(), $version);

    wp_register_script('jquery-fullscreen', BWG()->front_url . '/js/jquery.fullscreen-0.4.1.min.js', $required_scripts, '0.4.1', $in_footer);
    wp_register_script($this->prefix . '_lazyload', BWG()->front_url . '/js/jquery.lazy.min.js', $required_scripts, $version, $in_footer);

    array_push($required_scripts,
               'sumoselect',
               'jquery-mobile',
               'mCustomScrollbar',
               'jquery-fullscreen'
    );

    if ( BWG()->options->developer_mode ) {
      // These scripts are minified in none developer mode so there is no need to register scripts and set as required.
      wp_register_script($this->prefix . '_gallery_box', BWG()->front_url . '/js/bwg_gallery_box.js', $required_scripts, $version, $in_footer);
      wp_register_script($this->prefix . '_embed', BWG()->front_url . '/js/bwg_embed.js', $required_scripts, $version, $in_footer);
      array_push($required_scripts,
                 $this->prefix . '_gallery_box',
                 $this->prefix . '_embed'
      );
      if ( $this->is_pro ) {
        wp_register_script($this->prefix . '_raty', BWG()->front_url . '/js/jquery.raty.min.js', $required_scripts, '2.5.2', $in_footer);
        wp_register_script($this->prefix . '_featureCarousel', BWG()->plugin_url . '/js/jquery.featureCarousel.js', $required_scripts, $version, $in_footer);
        // 3D Tag Cloud.
        wp_register_script($this->prefix . '_3DEngine', BWG()->front_url . '/js/3DEngine/3DEngine.min.js', $required_scripts, '1.0.0', $in_footer);

        array_push($required_scripts,
                   $this->prefix . '_raty',
                   $this->prefix . '_featureCarousel',
                   $this->prefix . '_3DEngine'
        );
      }
      $style_file  = BWG()->front_url . '/css/bwg_frontend.css';
      $script_file = BWG()->front_url . '/js/bwg_frontend.js';
    }
    else {
      $style_file  = BWG()->front_url . '/css/styles.min.css';
      $script_file = BWG()->front_url . '/js/scripts.min.js';
    }

    wp_register_style($this->prefix . '_frontend', $style_file, $required_styles, $version);
    wp_register_script($this->prefix . '_frontend', $script_file, $required_scripts, $version, $in_footer);

    if( BWG()->options->lazyload_images ) {
      wp_enqueue_script($this->prefix . '_lazyload');
    }

    if ( !BWG()->options->use_inline_stiles_and_scripts || WDWLibrary::elementor_is_active() ) {
      wp_enqueue_style($this->prefix . '_frontend');
      wp_enqueue_script($this->prefix . '_frontend');
    }

    wp_localize_script($this->prefix . '_frontend', 'bwg_objectsL10n', array(
	    'bwg_field_required'  => __('field is required.', $this->prefix),
      'bwg_mail_validation' => __('This is not a valid email address.', $this->prefix),
      'bwg_search_result' => __('There are no images matching your search.', $this->prefix),
      'bwg_select_tag'  => __('Select Tag', $this->prefix),
      'bwg_order_by'  => __('Order By', $this->prefix),
      'bwg_search' => __('Search', $this->prefix),
      'bwg_show_ecommerce' =>  __('Show Ecommerce', $this->prefix),
      'bwg_hide_ecommerce' =>  __('Hide Ecommerce', $this->prefix),
      'bwg_show_comments' =>  __('Show Comments', $this->prefix),
      'bwg_hide_comments' =>  __('Hide Comments', $this->prefix),
      'bwg_restore' =>  __('Restore', $this->prefix),
      'bwg_maximize' =>  __('Maximize', $this->prefix),
      'bwg_fullscreen' =>  __('Fullscreen', $this->prefix),
      'bwg_exit_fullscreen' =>  __('Exit Fullscreen', $this->prefix),
      'bwg_search_tag' =>  __('SEARCH...', $this->prefix),
      'bwg_tag_no_match' => __('No tags found', $this->prefix),
      'bwg_all_tags_selected' => __('All tags selected', $this->prefix),
      'bwg_tags_selected' => __('tags selected', $this->prefix),
      'play' => __('Play', $this->prefix),
      'pause' => __('Pause', $this->prefix),
      'is_pro' => $this->is_pro,
      'bwg_play' => __('Play', $this->prefix),
      'bwg_pause' => __('Pause', $this->prefix),
      'bwg_hide_info' => __('Hide info', $this->prefix),
      'bwg_show_info' => __('Show info', $this->prefix),
      'bwg_hide_rating' => __('Hide rating', $this->prefix),
      'bwg_show_rating' => __('Show rating', $this->prefix),
      'ok' => __('Ok', $this->prefix),
      'cancel' => __('Cancel', $this->prefix),
      'select_all' => __('Select all', $this->prefix),
      'lazy_load'=> BWG()->options->lazyload_images,
      'lazy_loader'=> BWG()->plugin_url."/images/ajax_loader.png",
      'front_ajax' => BWG()->options->front_ajax,
    ));
  }

  /**
   * Languages localization.
   */
  public function language_load() {
    load_plugin_textdomain($this->prefix, FALSE, basename(dirname(__FILE__)) . '/languages');
  }

  public function init_free_users_lib() {
    add_filter('tenweb_new_free_users_lib_path', array($this, 'tenweb_lib_path'));
  }

  public function tenweb_lib_path($path) {
    // The version of WD Lib
    $version = '1.1.3';
    if (!isset($path['version']) || version_compare($path['version'], $version) === -1) {
      $path['version'] = $version;
      $path['path'] = $this->plugin_dir;
    }
    return $path;
  }

  /**
   * Overview.
   */
  public function overview() {
    if (is_admin() && !isset($_REQUEST['ajax'])) {
      if (!class_exists("TenWebLibNew")) {
        $plugin_dir = apply_filters('tenweb_new_free_users_lib_path', array('version' => '1.1.3', 'path' => $this->plugin_dir));
        require_once($plugin_dir['path'] . '/wd/start.php');
      }

      global $bwg_options;
      $bwg_options = array(
        "prefix" => "bwg",
        "wd_plugin_id" => 55,
        "plugin_id" => 101,
        "plugin_title" => "Photo Gallery",
        "plugin_wordpress_slug" => "photo-gallery",
        "plugin_dir" => BWG()->plugin_dir,
        "plugin_main_file" => __FILE__,
        "description" => __('Photo Gallery is a fully responsive gallery plugin with advanced functionality. It allows having different image galleries for your posts and pages. You can create unlimited number of galleries, combine them into gallery groups, and provide descriptions and tags.', $this->prefix),
        // from web-dorado.com
        "plugin_features" => array(
          0 => array(
            "title" => __("Easy Set-up and Management", $this->prefix),
            "description" => __("Create stunning, 100% responsive, SEO-friendly photo galleries in minutes. Use the File Manager with single-step and easy-to-manage functionality to rename, upload, copy, add and remove images and image directories. Otherwise use WordPress built in media uploader.", $this->prefix),
          ),
          1 => array(
            "title" => __("Unlimited Photos and Albums", $this->prefix),
            "description" => __("The plugin allows creating unlimited number of galleries or gallery groups and upload images in each gallery as many as you wish. Add single/ multiple galleries into your pages and posts with the help of functional shortcode; visual shortcodes for an easier management.", $this->prefix),
          ),
          2 => array(
            "title" => __("Customizable", $this->prefix),
            "description" => __("The gallery plugin is easily customizable. You can edit themes changing sizes and colors for different features. Specify the number of images to display in a single row in an gallery group. Additionally, you can customize thumbnail images by cropping, flipping and rotating them.", $this->prefix),
          ),
          3 => array(
            "title" => __("10 View Options", $this->prefix),
            "description" => __("Photo Gallery plugin allows displaying galleries and gallery groups in 10 elegant and beautiful views:, Thumbnails, Masonry, Mosaic, Slideshow, Image Browser, Masonry Album, Compact Album, Extended Album, Blog Style Gallery, Ecommerce.", $this->prefix),
          ),
          4 => array(
            "title" => __("Audio and Video Support", $this->prefix),
            "description" => __("You can include both videos and images within a single gallery. WordPress Photo Gallery Plugin supports YouTube and Vimeo videos within Galleries. It's also possible to add audio tracks for the image slideshow.", $this->prefix),
          )
        ),
        // user guide from web-dorado.com
        "user_guide" => array(
          0 => array(
            "main_title" => __("Installing", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/installing.html",
            "titles" => array()
          ),
          1 => array(
            "main_title" => __("Creating/Editing Galleries", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/creating-editing-galleries.html",
            "titles" => array(
              array(
                "title" => __("Instagram Gallery", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/creating-editing-galleries/instagram-gallery.html",
              ),
            )
          ),
          2 => array(
            "main_title" => __("Creating/Editing Tags", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/creating-editing-tag.html",
            "titles" => array()
          ),
          3 => array(
            "main_title" => __("Creating/Editing Albums", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/creating-editing-albums.html",
            "titles" => array()
          ),
          4 => array(
            "main_title" => __("Editing Options", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/editing-options.html",
            "titles" => array(
              array(
                "title" => __("Global Options", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/global-options.html",
              ),
              array(
                "title" => __("Watermark", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/watermark.html",
              ),
              array(
                "title" => __("Advertisement", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/advertisement.html",
              ),
              array(
                "title" => __("Lightbox", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/lightbox.html",
              ),
              array(
                "title" => __("Album Options", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/album-options.html",
              ),
              array(
                "title" => __("Slideshow", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/slideshow.html",
              ),
              array(
                "title" => __("Thumbnail Options", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/thumbnail-options.html",
              ),
              array(
                "title" => __("Image Options", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/image-options.html",
              ),
              array(
                "title" => __("Social Options", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/social-options.html",
              ),
              array(
                "title" => __("Carousel Options", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-options/carousel-options.html",
              ),
            )
          ),
          5 => array(
            "main_title" => __("Creating/Editing Themes", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/editing-themes.html",
            "titles" => array(
              array(
                "title" => __("Thumbnails", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/thumbnails.html",
              ),
              array(
                "title" => __("Masonry", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/masonry.html",
              ),
              array(
                "title" => __("Mosaic", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/mosaic.html",
              ),
              array(
                "title" => __("Slideshow", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/slideshow.html",
              ),
              array(
                "title" => __("Image Browser", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/image-browser.html",
              ),
              array(
                "title" => __("Compact Album", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/compact-album.html",
              ),
              array(
                "title" => __("Masonry Album", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/masonry-album.html",
              ),
              array(
                "title" => __("Extended Album", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/extended-album.html",
              ),
              array(
                "title" => __("Blog Style", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/blog-style.html",
              ),
              array(
                "title" => __("Lightbox", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/lightbox.html",
              ),
              array(
                "title" => __("Page Navigation", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/page-navigation.html",
              ),
              array(
                "title" => __("Carousel", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/editing-themes/carousel.html",
              ),
            )
          ),
          6 => array(
            "main_title" => __("Generating Shortcode", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/shortcode-generating.html",
            "titles" => array()
          ),
          7 => array(
            "main_title" => __("Editing Comments", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/comments-editing.html",
            "titles" => array()
          ),
          8 => array(
            "main_title" => __("Editing Ratings", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/ratings-editing.html",
            "titles" => array()
          ),
          9 => array(
            "main_title" => __("Publishing the Created Photo Gallery", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery.html",
            "titles" => array(
              array(
                "title" => __("General Parameters", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery/general-parameters.html",
              ),
              array(
                "title" => __("Lightbox Parameters", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery/lightbox-parameters.html",
              ),
              array(
                "title" => __("Advertisement", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery/advertisement.html",
              ),
            )
          ),
          10 => array(
            "main_title" => __("Publishing Photo Gallery Widgets", $this->prefix),
            "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery-widgets.html",
            "titles" => array(
              array(
                "title" => __("Tag Cloud", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery-widgets/tag-cloud.html",
              ),
              array(
                "title" => __("Photo Gallery Tags Cloud", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery-widgets/gallery-tags-cloud.html",
              ),
              array(
                "title" => __("Photo Gallery Slideshow", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery-widgets/gallery-slideshow.html",
              ),
              array(
                "title" => __("Photo Gallery Widget", $this->prefix),
                "url" => "https://web-dorado.com/wordpress-gallery/publishing-gallery-widgets/gallery-widget.html",
              ),
            )
          ),
        ),
        "video_youtube_id" => "4Mxg0FsFZZE",  // e.g. https://www.youtube.com/watch?v=acaexefeP7o youtube id is the acaexefeP7o
        "plugin_wd_url" => BWG()->plugin_link . BWG()->utm_source,
        "plugin_wd_demo_link" => "https://demo.10web.io/photo-gallery/" . BWG()->utm_source,
        "plugin_wd_addons_link" => BWG()->plugin_link . BWG()->utm_source,
        "plugin_wd_docs_link" => "https://help.10web.io/hc/en-us/sections/360002159111-Photo-Gallery/" . BWG()->utm_source,
        "after_subscribe" => admin_url('admin.php?page=galleries_bwg'), // this can be plagin overview page or set up page
        "plugin_wizard_link" => '',
        "plugin_menu_title" => $this->nicename,
        "plugin_menu_icon" => BWG()->plugin_url . '/images/icons/icon.png',
        "deactivate" => !$this->is_pro,
        "subscribe" => false,
        "custom_post" => '',
        "menu_position" => null,
        "display_overview" => false,
      );

      ten_web_new_lib_init($bwg_options);
    }
  }

  /**
   * Create custom post types.
   */
  public function create_post_types() {
    if (!isset(BWG()->options)) {
      BWG()->options = new WD_BWG_Options();
    }

    if (BWG()->options->show_hide_post_meta == 1) {
      $show_hide_post_meta = array('editor', 'comments', 'thumbnail', 'title');
    }
    else {
      $show_hide_post_meta = array('editor', 'thumbnail', 'title');
    }
    if (BWG()->options->show_hide_custom_post == 0) {
      $show_hide_custom_post = false;
    }
    else {
      $show_hide_custom_post = true;
    }
    $args = array(
      'label' => 'Gallery',
      'public' => TRUE,
      'exclude_from_search' => TRUE,
      'publicly_queryable' => TRUE,
      'show_ui' => $show_hide_custom_post,
      'show_in_menu' => TRUE,
      'show_in_nav_menus' => TRUE,
      'permalink_epmask' => TRUE,
      'rewrite' => TRUE,
      'label'  => __('Galleries', $this->prefix),
      'supports' => $show_hide_post_meta,
    );
    register_post_type( 'bwg_gallery', $args );

    $args = array(
      'label'=> 'Gallery group',
      'public' => TRUE,
      'exclude_from_search' => TRUE,
      'publicly_queryable' => TRUE,
      'show_ui' => $show_hide_custom_post,
      'show_in_menu' => TRUE,
      'show_in_nav_menus' => TRUE,
      'permalink_epmask' => TRUE,
      'rewrite' => TRUE,
      'label'  => __('Albums', $this->prefix),
      'supports' => $show_hide_post_meta
    );
    register_post_type( 'bwg_album', $args );

    $args = array(
      'label' => 'Gallery Tags',
      'public' => TRUE,
      'exclude_from_search' => TRUE,
      'publicly_queryable' => TRUE,
      'show_ui' => $show_hide_custom_post,
      'show_in_menu' => TRUE,
      'show_in_nav_menus' => TRUE,
      'permalink_epmask' => TRUE,
      'rewrite' => TRUE,
      'label'  => __('Gallery tags', $this->prefix),
      'supports' => $show_hide_post_meta
    );
    register_post_type( 'bwg_tag', $args );

    if ( $this->is_pro ) {
      $args = array(
        'label' => 'Gallery Share',
        'public' => FALSE,
        'publicly_queryable' => TRUE,
        'exclude_from_search' => TRUE,
        /*'query_var'          => 'share',
        'rewrite'            => array('slug' => 'share'),*/
      );
      register_post_type('bwg_share', $args);
    }

    WDWLibrary::register_custom_taxonomies();
  }

  /**
   * Change Share template.
   *
   * @param $single_template
   *
   * @return string
   */
  public function share_template( $single_template ) {
    global $post;
    if ( isset($post) && isset($post->post_type) && $post->post_type == 'bwg_share' ) {
      $single_template = BWG()->plugin_dir . '/framework/WDWShare.php';
    }

    return $single_template;
  }

  public function tag_cloud_widget_args($args) {
    if ($args['taxonomy'] == 'bwg_tag') {
      require_once BWG()->plugin_dir . "/frontend/models/BWGModelWidget.php";
      $model = new BWGModelWidgetFrontEnd();
      $model->get_tags_data(0);
    }
    return $args;
  }

  public function autoupdate_interval( $schedules ) {
    require_once(BWG()->plugin_dir . '/framework/WDWLibraryEmbed.php');
    $schedules['bwg_autoupdate_interval'] = array(
      'interval' => 60 * BWG()->options->autoupdate_interval,
      'display' => __('Photo gallery plugin autoupdate interval.', $this->prefix),
    );
    return $schedules;
  }

  public function social_galleries() {
    if ( BWG()->options->instagram_access_token != '' ) {
      $this->instagram_galleries();
      wp_die();
    }
  }

  public function instagram_galleries() {
    /* Check if instagram galleries exist and refresh them every hour.*/
    require_once(BWG()->plugin_dir . '/framework/WDWLibraryEmbed.php');
    /* Array of IDs of instagram galleries.*/
    $response = array();
    $instagram_galleries = WDWLibraryEmbed::check_instagram_galleries();
    if ( !empty($instagram_galleries[0]) ) {
      foreach ( $instagram_galleries as $gallery ) {
        array_push($response, WDWLibraryEmbed::refresh_social_gallery($gallery));
      }
    }
  }

	/**
	* Plugins loaded actions.
	*/
	public function plugins_loaded() {
		// Initialize add-ons.
		if ( $this->is_pro ) {
			do_action('bwg_init_addons');
		}
	}
	
	/**
	* Incompatibility message.
	*
	* @param $add_ons_notice
	*/
	function addons_compatibility_notice($add_ons_notice) {
		$addon_names = implode(', ', $add_ons_notice);
		$count = count($add_ons_notice);
		$single = __('Please update the %s add-on to start using.', $this->prefix);
		$plural = __('Please update the %s add-ons to start using.', $this->prefix);
		echo '<div class="error"><p>' . sprintf( _n($single, $plural, $count, $this->prefix), $addon_names ) .'</p></div>';
	}

   /**
   * Check add-ones version compatibility with Photo Gallery.
   */
	function check_addons_compatibility() {
		$add_ons = array(
		  'photo-gallery-facebook' => array( 'version' => '1.1.0', 'file' => 'photo-gallery-facebook.php' ),
		  'photo-gallery-export' => array( 'version' => '1.0.3', 'file' => 'photo-gallery-export.php' ),
		  'photo-gallery-ecommerce' => array( 'version' => '1.0.17', 'file' => 'photo-gallery-ecommerce.php' ),
		);
		$add_ons_notice = array();
		include_once(BWG()->abspath . 'wp-admin/includes/plugin.php');
		foreach ( $add_ons as $add_on_key => $add_on_value ) {
		  $addon_path = plugin_dir_path(dirname(__FILE__)) . $add_on_key . '/' . $add_on_value['file'];
		  if ( is_plugin_active($add_on_key . '/' . $add_on_value['file']) ) {
			$addon = get_plugin_data($addon_path);
			if ( version_compare($addon['Version'], $add_on_value['version'], '<=') ) {
			  deactivate_plugins($addon_path);
			  array_push($add_ons_notice, $addon['Name']);
			}
		  }
		}
		if ( !empty($add_ons_notice) ) {
		  $this->addons_compatibility_notice($add_ons_notice);
		}
	}

  /**
   * Add star rating to plugin meta links.
   *
   * @param $meta_fields
   * @param $file
   *
   * @return array
   */
  function add_plugin_meta_links($meta_fields, $file) {
    if ( plugin_basename(__FILE__) == $file ) {
      $plugin_url = "https://wordpress.org/support/plugin/photo-gallery";
      $prefix = $this->prefix;
      $meta_fields[] = "<a href='" . $plugin_url . "/#new-post' target='_blank'>" . __('Ask a question', $prefix) . "</a>";
      $meta_fields[] = "<a href='" . $plugin_url . "/reviews#new-post' target='_blank' title='" . __('Rate', $prefix) . "'>
            <i class='wdi-rate-stars'>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "</i></a>";

      $stars_color = "#ffb900";

      echo "<style>"
        . ".wdi-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
        . ".wdi-rate-stars svg{fill:" . $stars_color . ";}"
        . ".wdi-rate-stars svg:hover{fill:" . $stars_color . "}"
        . ".wdi-rate-stars svg:hover ~ svg{fill:none;}"
        . "</style>";
    }

    return $meta_fields;
  }

  /**
   * Allowed upload mime_types.
   *
   * @param array $mimes
   *
   * @return array $mimes
   */
  function allowed_upload_mime_types( $mimes ) {
    // Optional. allowed a mime type.
    $allowed = array( 'jpg|jpeg|jpe', 'gif', 'png', 'svg' );
    foreach ( $mimes as $key => $val ) {
      if ( !in_array( $key, $allowed ) ) {
        unset( $mimes[ $key ] );
      }
    }
    return $mimes;
  }

  /**
   * Prevent adding shortcode conflict with some builders.
   */
  private function before_shortcode_add_builder_editor() {
    if ( defined('ELEMENTOR_VERSION') && did_action( 'elementor/loaded' ) ) {
      add_action('elementor/editor/footer', array( $this, 'global_script' ));
    }
    if ( class_exists('FLBuilder') ) {
      add_action('wp_enqueue_scripts', array( $this, 'global_script' ));
    }
  }

  public function enqueue_elementor_widget_scripts() {
    wp_enqueue_script(BWG()->prefix . 'elementor_widget_js', plugins_url('js/bwg_elementor_widget.js', __FILE__), array( 'jquery' ));
  }

  public function webinar_banner() {
    // Webinar banner
    if ( !class_exists( 'TWPGWebinar' ) ) {
      require_once( $this->plugin_dir . '/framework/TWWebinar.php' );
    }
    new TWPGWebinar(array(
      'menu_postfix' => '_' . $this->prefix,
      'title' => 'Join the Webinar',
      'description' => 'How to Create a Fully Functional WP Website with Beautiful Photo Gallery in Just an Hour + SPECIAL GIFT FOR WEBINAR ATTENDEES',
      'preview_type' => 'youtube',
      'preview_url' => 'A111ykjWdW8',
      'button_text' => 'SIGN UP',
      'button_link' => 'https://my.demio.com/ref/ydTJSUzyVqOgcUOV',
    ));
  }
}

/**
 * Main instance of BWG.
 *
 * @return BWG The main instance to prevent the need to use globals.
 */
function BWG() {
  return BWG::instance();
}

BWG();

/**
 * Display gallery with function.
 *
 * @param $id Shortcode id.
 */
function photo_gallery( $id ) {
  echo BWG()->shortcode(array( 'id' => $id ));
}
