<?php

/**
 * ECWD_Admin
 */
class ECWD_Admin {
  protected static $instance = NULL;
  protected $ecwd_page = NULL;
  protected $notices = NULL;
  protected static $default_shortcode = '[ecwd id="%s" type="full" page_items="5" event_search="yes" display="full" displays="full,list,week,day" filters=""]';

  private function __construct() {
    $plugin = ECWD::get_instance();
    $this->prefix = $plugin->get_prefix();
    $this->notices = new ECWD_Notices();
    add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path(__FILE__) . $this->prefix . '.php'), array(
      $this,
      'add_action_links',
    ));
    $this->ecwd_config();
    // Setup admin stants
    add_action('init', array( $this, 'define_admin_constants' ));
    add_action('init', array( $this, ECWD_PLUGIN_PREFIX . '_shortcode_button' ));
    // Add admin styles and scripts
    add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ));
    add_action('admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ));
    //Add organizer,venue form event edit page
    add_action('wp_ajax_ecwd_add_post', array( $this, 'wp_ajax_add_post' ));
    add_action('wp_ajax_ecwd_set_default_calendar', array( $this, 'ecwd_set_default_calendar' ));
    //add shortcode in calendar post content
    add_action('wp_insert_post_data', array( $this, 'add_calendar_shortcode' ));
    // Add the options page and menu item.
    add_action('admin_menu', array( $this, 'add_plugin_admin_menu' ), 10);
    foreach ( array( 'post.php', 'post-new.php' ) as $hook ) {
      add_action("admin_head-$hook", array( $this, 'admin_head' ));
    }
    add_action('wp_ajax_ecwd_shortcode', array( $this, 'ecwd_shortcode_data' ));

    // Enqueue block editor assets for Gutenberg.
    add_filter('tw_get_block_editor_assets', array($this, 'register_block_editor_assets'));
    add_filter('tw_get_plugin_blocks', array($this, 'register_plugin_block'));
    add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));

    // Runs the admin notice ignore function incase a dismiss button has been clicked
    add_action('admin_init', array( $this, 'admin_notice_ignore' ));
    add_action('admin_notices', array( $this, 'ecwd_admin_notices' ));
    add_action('admin_notices', array( $this, 'ecwd_helper_bar' ), 10000);
    add_filter('parent_file', array( $this, 'ecwd_submenu_parent_file' ));
    $meta_value = get_option('wd_seo_notice_status');
    if ( (!function_exists('wd_bp_install_notice')) && (!is_dir(plugin_dir_path(__DIR__) . 'seo-by-10web')) && ($meta_value === '' || $meta_value === FALSE) ) {
      add_action('admin_notices', array( $this, 'wd_bp_install_notice' ));
      add_action('admin_enqueue_scripts', array( $this, 'wd_bp_script_style' ));
      add_action('wp_ajax_wd_seo_dismiss', array( $this, 'wd_bp_install_notice_status' ));
    }
    add_filter('default_hidden_meta_boxes', array( $this, 'default_hidden_meta_boxes' ), 2, 2);
    add_filter("plugin_row_meta", array( $this, 'ecwd_add_plugin_meta_links' ), 10, 2);
    add_action('untrashed_post', array( $this, 'set_default_metas_to_restored_events' ));

  }

  public function wd_bp_install_notice(){
      $get_current = get_current_screen();
      $current_screen_id = array(
        'edit-ecwd_event',
        'ecwd_event',
        'edit-ecwd_event_category',
        'edit-ecwd_event_tag',
        'edit-ecwd_organizer',
        'edit-ecwd_venue',
        'edit-ecwd_calendar',
        'edit-ecwd_theme',
        'ecwd_event_page_ecwd_general_settings',
        'ecwd_event_page_overview_ecwd',
        'ecwd_event_page_ecwd_updates',
        'ecwd_event_page_ecwd_licensing',
        'toplevel_page_ecwd_addons',
        'toplevel_page_ecwd_themes',
      );
      if(in_array($get_current->id, $current_screen_id)){
        $wd_bp_plugin_url = ECWD_URL;
        $prefix = 'ecwd';
        $meta_value = get_option('wd_seo_notice_status');
        if ($meta_value === '' || $meta_value === false) {
          ob_start();
          ?>
          <div class="notice notice-info" id="wd_bp_notice_cont">
            <p>
              <img id="wd_bp_logo_notice" src="<?php echo $wd_bp_plugin_url . '/assets/seo_logo.png'; ?>">
              <?php _e("Event Calendar WD advises: Optimize your web pages for search engines with the", $prefix) ?>
              <a href="https://wordpress.org/plugins/seo-by-10web/" title="<?php _e("More details", $prefix) ?>"
                 target="_blank"><?php _e("FREE SEO", $prefix) ?></a>
              <?php _e("plugin.", $prefix) ?>
              <a class="button button-primary wd_notice_button"
                 href="<?php echo esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=seo-by-10web'), 'install-plugin_seo-by-10web')); ?>">
                <span class="wd_notice_button" onclick="wd_bp_notice_install()"><?php _e("Install", $prefix); ?></span>
              </a>
            </p>
            <button type="button" class="wd_bp_notice_dissmiss notice-dismiss"><span class="screen-reader-text"></span>
            </button>
          </div>
          <script>wd_bp_url = '<?php echo add_query_arg(array('action' => 'wd_seo_dismiss',), admin_url('admin-ajax.php')); ?>'</script>
          <?php
          echo ob_get_clean();
        }
      }
    }
    public function wd_bp_script_style() {
      wp_enqueue_script('wd_bck_install', ECWD_URL . '/js/wd_bp_install.js', array('jquery'));
      wp_enqueue_style('wd_bck_install', ECWD_URL . '/css/wd_bp_install.css');
    }
    public function wd_bp_install_notice_status() {
      update_option('wd_seo_notice_status', '1', 'no');
    }
    public function default_hidden_meta_boxes($hidden, $screen) {
      if ($screen->id == 'ecwd_calendar') {
        if (!in_array('postcustom',$hidden)) {
          array_push($hidden, 'postcustom');
  
        }
      }
      return $hidden;
    }

    function ecwd_submenu_parent_file($parent_file) {
      $screen = get_current_screen();
      if ($screen->post_type == "ecwd_organizer" || $screen->post_type == "ecwd_venue") {
        return ECWD_MENU_SLUG;
      }
      return $parent_file;
    }

  public function register_block_editor_assets($assets) {
    $version = '2.0.4';
    $js_path = ECWD_URL . '/js/tw-gb/block.js';
    $css_path = ECWD_URL . '/css/tw-gb/block.css';
    if (!isset($assets['version']) || version_compare($assets['version'], $version) === -1) {
      $assets['version'] = $version;
      $assets['js_path'] = $js_path;
      $assets['css_path'] = $css_path;
    }
    return $assets;
  }

  public function register_plugin_block($blocks) {
    $ecwd_shortcode_nonce = wp_create_nonce( "ecwd_shortcode" );
    $blocks['tw/' . $this->prefix] = array(
      'title' => 'Event calendar',
      'titleSelect' => sprintf(__('Select %s', $this->prefix), 'Event calendar'),
      'iconUrl' => ECWD_URL . '/assets/event_cal_1.svg',
      'iconSvg' => array('width' => 20, 'height' => 20, 'src' => ECWD_URL . '/assets/event_cal.svg'),
      'isPopup' => true,
      'containerClass' => 'tw-container-wrap-420-450',
      'data' => array('shortcodeUrl' => add_query_arg(array('action' => 'ecwd_shortcode', 'nonce'=>$ecwd_shortcode_nonce), admin_url('admin-ajax.php'))),
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
     * Check user is on plugin page
     * @return  bool
     */
    private function ecwd_page() {
        if (!isset($this->ecwd_page)) {
            return false;
        }
        $screen = get_current_screen();
        if ($screen->id == 'edit-ecwd_event' || $screen->id == ECWD_PLUGIN_PREFIX . '_event' || in_array($screen->id, $this->ecwd_page) || $screen->post_type == ECWD_PLUGIN_PREFIX . '_event' || $screen->post_type == ECWD_PLUGIN_PREFIX . '_theme' || $screen->post_type == ECWD_PLUGIN_PREFIX . '_venue' || $screen->id == 'edit-ecwd_calendar' || $screen->id == ECWD_PLUGIN_PREFIX . '_calendar' || $screen->id == ECWD_PLUGIN_PREFIX . '_countdown_theme' || $screen->post_type == ECWD_PLUGIN_PREFIX . '_organizer') {
            return true;
        } else {
            return false;
        }
    }

    public function set_default_metas_to_restored_events($post_id){

        if(get_post_type($post_id) !== "ecwd_event") {
          return;
        }

      $today = ECWD::ecwd_date('Y-m-d H:i');
      $start_date = ECWD::ecwd_date('Y/m/d H:i', strtotime($today . "+1 days"));
      $end_date = ECWD::ecwd_date('Y/m/d H:i', strtotime($start_date . "+1 hour"));
      $date_from = get_post_meta($post_id, 'ecwd_event_date_from', true);
      if(empty($date_from)) {
        update_post_meta($post_id, 'ecwd_event_date_from', $start_date);
        update_post_meta($post_id, 'ecwd_event_date_to', $end_date);
      }
    }



  public static function activate() {
        if (!defined('ECWD_PLUGIN_PREFIX')) {
            define('ECWD_PLUGIN_PREFIX', 'ecwd');
        }

        delete_site_transient('ecwd_uninstall');
        delete_option('ecwd_admin_notice');
        $has_option = get_option('ecwd_old_events');
        if ($has_option === false) {
            $old_event = get_posts(array(
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => 'ecwd_event',
                'post_status' => 'any'
            ));
            if ($old_event && isset($old_event[0]->post_date)) {
                add_option('ecwd_old_events', 1);
            } else {
                add_option('ecwd_old_events', 0);
            }
        }

      $calendars = get_posts(array(
        'post_type' => 'ecwd_calendar',
        'numberposts' => -1
      ));

      //$blue_theme = get_page_by_title('Default', 'OBJECT', 'ecwd_theme');
      //$blue_id = (isset($blue_theme->ID)) ? $blue_theme->ID : 0;

      $calendar = get_posts($calendars);
      if (!empty($calendar)) {
        foreach ($calendars as $calendar) {
          $theme_id = get_post_meta($calendar->ID, 'ecwd_calendar_theme', true);
          if ($theme_id == "calendar_grey") {
            update_post_meta($calendar->ID, 'ecwd_calendar_theme', "calendar_grey");
          } else {
            update_post_meta($calendar->ID, 'ecwd_calendar_theme', "calendar");
          }
        }
      }

        include_once ECWD_DIR . '/includes/ecwd_config.php';
        $conf = ECWD_Config::get_instance();
        $conf->update_conf_file();


      $version_option = get_option("ecwd_version");

      if($version_option == false){
        self::fix_events_locations();
      }

    $saved_events_opt = get_option('ecwd_settings_events');

    if($version_option == false || version_compare(substr($version_option, 2), '0.83', '<=')) {
        $opt = get_option('ecwd_settings_general');
        if (isset($opt['show_events_detail'])) {
          $events_opt = get_option('ecwd_settings_events');
          $events_opt['show_events_detail'] = $opt['show_events_detail'];
          update_option('ecwd_settings_events', $events_opt);
        }
      }


      if ($version_option == false || version_compare(substr($version_option, 2), '0.94', '<=')) {
        self::update_to_95($calendars);
      }

	  if ($version_option == false || version_compare(substr($version_option, 2), '1.16', '<')) {

		$events_opt = get_option('ecwd_settings_events');
	    if(!isset($events_opt["use_custom_template"])){
		   $events_opt["use_custom_template"] = ($version_option === false) ? "0" : "1";
		   update_option('ecwd_settings_events', $events_opt);
	    }
	  }

    if($version_option == false || version_compare(substr($version_option, 2), '1.18', '<=')) {
      if($saved_events_opt !== false && isset($saved_events_opt['related_events_count'])) {
        if($saved_events_opt['related_events_count'] === "") {
          $events_opt = get_option('ecwd_settings_events');
          $events_opt['related_events_count'] = "100";
          update_option('ecwd_settings_events', $events_opt);
        }
      }
    }



      update_option('ecwd_version',ECWD_VERSION);

    }

  public static function uninstall_menu(){
    $slug = ECWD_MENU_SLUG;
    if(get_site_transient('ecwd_uninstall') === '1') {
      add_menu_page(
        'Events',
        'Events',
        'manage_options',
        'ecwd_event_menu',
        array('ECWD_Admin', 'display_uninstall_page'),
        plugins_url('/assets/Insert-icon.png', ECWD_MAIN_FILE),
        '25'
      );
      $slug = 'ecwd_event_menu';
    }


    add_submenu_page(
      "",
      __('Uninstall', 'event-calendar-wd'),
      __('Uninstall', 'event-calendar-wd'),
      'manage_options',
      'ecwd_uninstall',
      array('ECWD_Admin', 'display_uninstall_page')
    );
  }

  public static function display_uninstall_page(){
    include_once 'includes/ecwd-uninstall.php';
    $uninstall = new ecwd_uninstall();
  }


  public static function check_silent_update(){

    $current_version = ECWD_VERSION;
    $saved_version = get_option('ecwd_version');

    $old_version =  substr($saved_version, 2);
    $new_version =  substr($current_version, 2);

    if($new_version  != $old_version ){

      self::activate();
    }

  }
	
	public static function global_activate($networkwide)
    {
		if (function_exists('is_multisite') && is_multisite()) {
			// Check if it is a network activation - if so, run the activation function for each blog id.
			if ($networkwide) {
				global $wpdb;
				// Get all blog ids.
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					self::activate();
					restore_current_blog();
				}
				return;
			}
		}
		self::activate();
   }

  private static function update_to_95($calendars) {
    //add shortcodes on calendar content
    if(!empty($calendars)) {
      foreach ($calendars as $calendar) {
        if (get_post_meta($calendar->ID, "ecwd_added_shortcode", true) === '1') {
          continue;
        }
        $content = $calendar->post_content;
        if (has_shortcode($content, 'ecwd') == false) {
          $content .= sprintf(self::$default_shortcode, $calendar->ID);
          $calendar->post_content = $content;
          wp_update_post($calendar);
        }
        update_post_meta($calendar->ID, 'ecwd_added_shortcode', '1');
      }
    }

    //if no calendar create default
    if (empty($calendars)) {
      $post_data = array(
        'post_title' => __("Calendar", 'event-calendar-wd'),
        'post_content' => "",
        'post_status' => 'publish',
        'post_type' => 'ecwd_calendar'
      );

      $post_id = wp_insert_post($post_data);

      $default_calendar_post = get_post($post_id);
      $default_calendar_post->post_content = sprintf(self::$default_shortcode, $post_id);
      wp_update_post($default_calendar_post);

      update_option('ecwd_default_calendar', $post_id);
      update_post_meta($post_id, 'ecwd_added_shortcode', '1');
    }

    //create venues for events locations
    $args = array(
      'post_type' => 'ecwd_event',
      'post_status' => 'publish',
      'numberposts' => -1,
      'meta_query' => array(
        array(
          'key' => 'ecwd_event_venue',
          'compare' => 'NOT EXISTS'
        ),
        array(
          'key' => 'ecwd_event_location',
          'meta_value' => '',
          'meta_compare' => '!=',
        ),
        array(
          'key' => 'ecwd_lat_long',
          'meta_value' => '',
          'meta_compare' => '!=',
        )
      )
    );

    $generated_venues_title = array();
    $events = get_posts($args);

    if (!empty($events)) {
      foreach ($events as $event) {

        $lat_long = get_post_meta($event->ID, 'ecwd_lat_long', true);

        if (isset($generated_venues_title[$lat_long])) {
          $venue_id = $generated_venues_title[$lat_long];
        } else {
          $map_zoom = get_post_meta($event->ID, 'ecwd_map_zoom', true);
          $location = get_post_meta($event->ID, 'ecwd_event_location', true);

          $args = array(
            'post_title' => $location,
            'post_content' => "",
            'post_status' => 'publish',
            'meta_input' => array(
              'ecwd_venue_lat_long' => $lat_long,
              'ecwd_venue_location' => $location,
              'ecwd_map_zoom' => $map_zoom,
              'ecwd_venue_meta_phone' => '',
              'ecwd_venue_meta_website' => '',
              'ecwd_venue_show_map' => '1'
            ),
            'post_type' => 'ecwd_venue'
          );

          $venue_id = wp_insert_post($args);
          $generated_venues_title[$lat_long] = $venue_id;
        }

        update_post_meta($event->ID, 'ecwd_event_venue', $venue_id);

      }
    }
  }

    static function fix_events_locations(){
        $venue_cache = array();
        $args = array(
            'numberposts' => -1,
            'post_type' => 'ecwd_event'
        );
        $events = get_posts($args);
        if(empty($events)){
            return;
        }

        foreach ($events as $event) {
            $venue_id = intval(get_post_meta($event->ID,'ecwd_event_venue',true));
            if(empty($venue_id)){
                continue;
            }

            if(!isset($venue_cache[$venue_id])){
                $venue_cache[$venue_id] = array(
                    'ecwd_venue_location' => get_post_meta($venue_id,'ecwd_venue_location',true),
                    'ecwd_venue_lat_long' => get_post_meta($venue_id,'ecwd_venue_lat_long',true)
                );
            }
            update_post_meta($event->ID,'ecwd_event_location',$venue_cache[$venue_id]['ecwd_venue_location']);
            update_post_meta($event->ID,'ecwd_lat_long',$venue_cache[$venue_id]['ecwd_venue_lat_long']);
        }

    }

    public static function uninstall() {

    }

    public function add_plugin_admin_menu() {
      global $ecwd_config;

      $this->ecwd_page[] = add_submenu_page(
        ECWD_MENU_SLUG,
        __('Settings', 'event-calendar-wd'),
        __('Settings', 'event-calendar-wd'),
        'manage_options',
        $this->prefix . '_general_settings', array($this, 'display_admin_page')
      );

      $this->ecwd_page[] = add_submenu_page(
        ECWD_MENU_SLUG,
        __('Calendar Extensions', 'event-calendar-wd'),
        __('Calendar Extensions', 'event-calendar-wd'),
        'manage_options', $this->prefix . '_addons',
        array($this, 'display_addons_page')
      );

      $this->ecwd_page[] = add_submenu_page(
        ECWD_MENU_SLUG,
        __('Calendar Themes', 'event-calendar-wd'),
        __('Calendar Themes', 'event-calendar-wd'),
        'manage_options',
        $this->prefix . '_themes',
        array($this, 'display_themes_page')
      );

      if ($ecwd_config['show_config_submenu']) {

        $this->ecwd_page[] = add_submenu_page(
          ECWD_MENU_SLUG,
          __('Config', 'event-calendar-wd'),
          __('Config', 'event-calendar-wd'),
          'manage_options',
          $this->prefix . '_config',
          array($this, 'display_config_page')
        );

      }

      if ( !ECWD_PRO ) {
        /* Custom link to wordpress.org*/
        global $submenu;
        $url = 'https://wordpress.org/support/plugin/event-calendar-wd/#new-post';
        $submenu[ECWD_MENU_SLUG][] = array(
          '<div id="ecwd_ask_question">' . __('Ask a question', $this->prefix) . '</div>',
          'manage_options',
          $url
        );
      }


    }

    public function include_ecwd_pointer_class() {
        include_once ('includes/ecwd_pointers.php');
        $ecwd_pointer = new Ecwd_pointers();
    }


    public function display_themes_page() {
        include_once( ECWD_DIR . '/views/admin/ecwd-theme-meta.php' );
    }

  public function display_addons_page() {
    $addons = array(
      'Management' => array(
        'add_event' => array(
          'name' => 'ECWD Frontend Event Management',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'This add-on is designed for  allowing the users/guests to add events to the calendar from the front end. In addition, the users can also have permissions to edit/delete their events.',
          'icon' => '',
          'image' => plugins_url('assets/add_addevent.jpg', __FILE__),
        ),
        'import_export' => array(
          'name' => 'ECWD Import/Export',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'The following data of the Event Calendar WD can be exported and imported: Events, Categories, Venues,Organizers and Tags. The exported/imported data will be in CSV format, which can be further edited, modified and imported',
          'icon' => '',
          'image' => plugins_url('assets/import_export.png', __FILE__),
        ),
        'custom_fields' => array(
          'name' => 'ECWD Custom Fields',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'Custom Fields Add-On will enable you to have more fields for more detailed and structured content: you can use this add-on and create additional fields for each event, venue and organizer.',
          'icon' => '',
          'image' => plugins_url('assets/custom_fields.png', __FILE__),
        ),
        'ecwd_subscribe' => array(
          'name' => 'ECWD Subscribe',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'Event Calendar Subscription Add-on  is a great too which allows subscribing to events based on category, tag, organizer and venue.',
          'icon' => '',
          'image' => plugins_url('assets/Subscribe.png', __FILE__),
        ),
        'ecwd_export' => array(
          'name' => 'ECWD Export to GCal/ICal',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'Export add-on will enable your calendar users to export single or whole month events in CSV and ICS formats and import to their iCalendars and Google calendars.',
          'icon' => '',
          'image' => plugins_url('assets/export_addon.png', __FILE__),
        ),
      ),
      'Events Grouping' => array(
        'event_filters' => array(
          'name' => 'ECWD Filter Bar',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'This add-on is designed for advanced event filter and browsing. It will display multiple filters, which will make it easier for the user to find the relevant event from the calendar.',
          'icon' => '',
          'image' => plugins_url('assets/add_filters.png', __FILE__),
        ),
        'event_countdown' => array(
          'name' => 'ECWD Event Countdown',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'With this add-on you can add an elegant countdown to your site. It supports calendar events or a custom one. The styles and colors of the countdown can be modified. It can be used as both as widget and shortcode.',
          'icon' => '',
          'image' => plugins_url('assets/add_cdown.jpg', __FILE__),
        ),
        'upcoming_events' => array(
          'name' => 'ECWD Upcoming events widget',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'The Upcoming events widget is designed for displaying upcoming events lists. The number of events, the event date ranges, as well as the appearance of the widget is fully customizable and easy to manage.',
          'icon' => '',
          'image' => plugins_url('assets/upcoming_events.png', __FILE__),
        ),
        'upcoming_events' => array(
          'name' => 'ECWD Upcoming events widget',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'The Upcoming events widget is designed for displaying upcoming events lists. The number of events, the event date ranges, as well as the appearance of the widget is fully customizable and easy to manage.',
          'icon' => '',
          'image' => plugins_url('assets/upcoming_events.png', __FILE__),
        ),
        'ecwd_views' => array(
          'name' => 'ECWD views',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'ECWD Views is a convenient add-on for displaying one of the additional Premium views within the pages and posts. The add-on allows choosing the time range of the events, which will be displayed with a particular view.',
          'icon' => '',
          'image' => plugins_url('assets/ecwd_views.png', __FILE__),
        ),
      ),
      'Integrations' => array(
        'gcal' => array(
          'name' => 'ECWD Google Calendar Integration',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'This addon integrates ECWD with your Google Calendar and gives functionality to import events or just display events without importing.',
          'icon' => '',
          'image' => plugins_url('assets/add_gcal.jpg', __FILE__),
        ),
        'ical' => array(
          'name' => 'ECWD iCAL Integration',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'This addon integrates ECWD with your iCAL Calendar and gives functionality to import events or just display events without importing.',
          'icon' => '',
          'image' => plugins_url('assets/add_ical.jpg', __FILE__),
        ),
        'tickets' => array(
          'name' => 'ECWD Event Tickets',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'Event Tickets Add-on is an easy set up tool for integrating ECWD with WooCommerce to sell tickets for your events.',
          'icon' => '',
          'image' => plugins_url('assets/ticketing_addon.png', __FILE__),
        ),
        'ecwd_embed' => array(
          'name' => 'ECWD Embed',
          'url' => 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin#plugin_steps',
          'description' => 'This add-on will allow displaying a calendar from your site  to other websites using embed code without need of installing ECWD plugin.',
          'icon' => '',
          'image' => plugins_url('assets/embed_addon.png', __FILE__),
        ),
      ),
    );
    include_once('views/admin/addons.php');
  }

    public function display_admin_page() {
        include_once( 'views/admin/admin.php' );
    }

    public function display_config_page() {
        $post_type = (isset($_GET['post_type']) && $_GET['post_type'] == 'ecwd_calendar');
        $page = (isset($_GET['page']) && $_GET['page'] == 'ecwd_config');
        $save_config = (isset($_GET['ecwd_save_config']) && $_GET['ecwd_save_config'] == '1');

        $config_obj = ECWD_Config::get_instance();

        if ($post_type && $page && $save_config) {
            $config_obj->save_new_config($_POST);
        }

        $configs = $config_obj->get_config();
        $response = $config_obj->get_response();
        $action = $_SERVER['REQUEST_URI'] . '&ecwd_save_config=1';

        include(ECWD_DIR . '/views/admin/ecwd-config.php');
    }

    public function ecwd_edit_template($type) {
        $option = $this->mail_template[$type]['option_name'];
        $name = $this->mail_template[$type]['name'];

        if (isset($_POST['mail_content']) && isset($_POST['ecwd_edit_template']) && check_admin_referer($type, 'ecwd_edit_template')) {
            update_option($option, $_POST['mail_content']);
        }
        $html = get_option($option);
        if ($html !== false) {
            $ajax_action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : "";
            $events = array();
            if (isset($_GET['ecwd_event_list']) && $_GET['ecwd_event_list'] == true) {
                $events = get_posts(array('numberposts' => -1, 'post_type' => 'ecwd_event', 'post_status' => 'publish'));
            }
            include_once('views/admin/ecwd-mail-template.php');
        }
    }

    /**
     * Enqueue styles for the admin area
     */
    public function enqueue_admin_styles() {

        $styles_key = ECWD_VERSION . '_' . ECWD_SCRIPTS_KEY;
        wp_enqueue_style($this->prefix . '-calendar-buttons-style', plugins_url('css/admin/mse-buttons.css', __FILE__), '', $styles_key, 'all');
        if ($this->ecwd_page()) {
            //wp_enqueue_style($this->prefix . '-main', plugins_url('css/calendar.css', __FILE__), '', $styles_key);
            wp_enqueue_style('ecwd-admin-css', plugins_url('css/admin/admin.css', __FILE__), array(), $styles_key, 'all');
            wp_enqueue_style('ecwd-admin-datetimepicker-css', plugins_url('css/admin/jquery.datetimepicker.css', __FILE__), array(), $styles_key, 'all');
            wp_enqueue_style($this->prefix . '-magnific-popup_css', plugins_url('css/magnific-popup.css', __FILE__), array(), $styles_key, 'all');
            wp_enqueue_style($this->prefix . '-datatables_css', plugins_url('css/datatables.min.css', __FILE__), array(), $styles_key, 'all');
            wp_enqueue_style('ecwd-admin-colorpicker-css', plugins_url('css/admin/evol.colorpicker.css', __FILE__), array(), $styles_key, 'all');
            wp_enqueue_style($this->prefix . '-calendar-style', plugins_url('css/style.css', __FILE__), '', $styles_key, 'all');
            wp_enqueue_style($this->prefix . '_font-awesome', plugins_url('/css/font-awesome/font-awesome.css', __FILE__), '', $styles_key, 'all');
            wp_enqueue_style($this->prefix . '-licensing', plugins_url('/css/admin/licensing.css', __FILE__), '', $styles_key, 'all');
            wp_enqueue_style($this->prefix . '-popup-styles', plugins_url('/css/ecwd_popup.css', __FILE__), '', $styles_key, 'all');
        }
    }

    /**
     * Register scripts for the admin area
     */
    public function enqueue_admin_scripts() {
        $scripts_key = ECWD_VERSION . '_' . ECWD_SCRIPTS_KEY;
        if ($this->ecwd_page()) {
          global $ecwd_options;

            wp_enqueue_script($this->prefix . '-gmap-public-admin', plugins_url('js/gmap/gmap3.js', __FILE__), array('jquery'), $scripts_key, true);
            wp_enqueue_script($this->prefix . '-admin-datetimepicker', plugins_url('js/admin/jquery.datetimepicker.js', __FILE__), array(
                'jquery',
                'jquery-ui-widget'
                    ), $scripts_key, true);
            wp_enqueue_script($this->prefix . '-admin-colorpicker', plugins_url('js/admin/evol.colorpicker.js', __FILE__), array('jquery'), $scripts_key, true);
            wp_enqueue_script($this->prefix . '-admin-ecwd-popup', plugins_url('js/ecwd_popup.js', __FILE__), array('jquery'), $scripts_key, true);
            wp_enqueue_script($this->prefix . '-public', plugins_url('js/scripts.js', __FILE__), array(
                'jquery',
                'masonry',
                $this->prefix . '-admin-ecwd-popup'
                    ), $scripts_key, true);
            wp_register_script($this->prefix . '-admin-scripts', plugins_url('js/admin/admin.js', __FILE__), array(
                'jquery',
                'jquery-ui-datepicker',
                'jquery-ui-tabs',
                'jquery-ui-selectable',
                $this->prefix . '-magnific_popup_js',
                $this->prefix . '-datatables_js',
                $this->prefix . '-public',
                $this->prefix . '-admin-ecwd-popup'
                    ), $scripts_key, true);
          $rest_route = add_query_arg(array(
            'rest_route' => '/'.ECWD_REST_NAMESPACE . '/'
          ), get_site_url()."/");
          global $post;
          $calendar_id = "";
          if(isset($post->ID)){
            $calendar_id = $post->ID;
          }
          wp_localize_script($this->prefix . '-admin-scripts', $this->prefix .'ServerVars', array(
            'calendar_id' =>$calendar_id,
            'root' => esc_url_raw(rest_url()),
            'pluginRestPath' => 'ecwd/v1/',
            'rest_route' => $rest_route,
            'includesUrl' => includes_url(),
            'wpRestNonce' => wp_create_nonce('wp_rest'),
            'ecwdRestNonce' => wp_create_nonce('ecwd_rest_nonce'),
            'version' => ECWD_VERSION,
          ));
          wp_register_script($this->prefix . '-magnific_popup_js', plugins_url('js/magnific-popup.min.js', __FILE__), array(
            'jquery',
          ), $scripts_key, true);
          wp_register_script($this->prefix . '-datatables_js', plugins_url('js/datatables.min.js', __FILE__), array(
            'jquery',
          ), $scripts_key, true);
          wp_enqueue_script($this->prefix . '-admin-datetimepicker-scripts', plugins_url('js/admin/datepicker.js', __FILE__), array('jquery'), $scripts_key, true);

            $params['ajaxurl'] = admin_url('admin-ajax.php');
            $params['version'] = get_bloginfo('version');
            if ($params['version'] >= 3.5) {
                wp_enqueue_media();
            } else {
                wp_enqueue_style('thickbox');
                wp_enqueue_script('thickbox');
            }
          global $ecwd_options;
          if(isset($ecwd_options["time_type"])){
            $time_format = $ecwd_options["time_type"];
          }else{
            $time_format = "";
          }
            $gmap_key = (isset($ecwd_options['gmap_key'])) ? $ecwd_options['gmap_key'] : "";
            $params['gmap_style'] = (isset($ecwd_options['gmap_style'])) ? $ecwd_options['gmap_style'] : "";

            wp_localize_script($this->prefix . '-admin-scripts', 'ecwd_admin_params', $params);
            wp_localize_script(ECWD_PLUGIN_PREFIX . '-public', 'ecwd', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajaxnonce' => wp_create_nonce(ECWD_PLUGIN_PREFIX . '_ajax_nonce'),
                'loadingText' => __('Loading...', 'event-calendar-wd'),
                'plugin_url' => ECWD_URL,
                'gmap_key' => trim($gmap_key),
                'gmap_style' => (isset($ecwd_options['gmap_style'])) ? $ecwd_options['gmap_style'] : "",
                'time_picker_format' => $time_format
            ));
            wp_localize_script(ECWD_PLUGIN_PREFIX . '-public', 'ecwd_admin_translation', array(
              'none'=>__("None",'event-calendar-wd'),
              'enter_event_name'=>__("Enter event name",'event-calendar-wd'),
              'event_list'=>__("Event List",'event-calendar-wd'),
              'calendar'=>__("Calendar",'event-calendar-wd'),
            ));

            wp_enqueue_script($this->prefix . '-admin-scripts');
            wp_enqueue_script($this->prefix . '-magnific_popup_js');
            wp_enqueue_script($this->prefix . '-datatables_js');

            wp_register_style('event-calendar-wd-roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700');
            wp_register_style('event-calendar-wd-pricing', ECWD_URL . '/css/pricing.css' , array(), ECWD_VERSION);
        }
      $screen = get_current_screen();
      if (!$this->ecwd_page() || $screen->post_type == "ecwd_calendar") {
        wp_localize_script('jquery', 'ecwd_translate', array(
          'ecwd_ECWD_shortcode'=>__('ECWD Shortcode','event-calendar-wd'),
          'ecwd_select_calendar'=>__("Select Calendar", 'event-calendar-wd'),
          'ecwd_please_add'=>__("Please add a calendar before using the shortcode", "event-calendar-wd"),
          'ecwd_select_view_type'=>__("Select View type", "event-calendar-wd"),
          'ecwd_events_per_page'=>__("Events per page in list view", 'event-calendar-wd'),
          'ecwd_calendar_start_date'=>__("Calendar start date", 'event-calendar-wd'),
          'ecwd_enable_event_search'=>__("Enable event search", 'event-calendar-wd'),
          'ecwd_general'=>__("General", 'event-calendar-wd'),
          'ecwd_views'=>__("Views", 'event-calendar-wd'),
          'ecwd_filters'=>__("Filters", 'event-calendar-wd'),
          'ecwd_date_format'=>__("Date format Y-m(2016-05) or empty for current date", 'event-calendar-wd'),
          'ecwd_view_1'=>__("View 1", 'event-calendar-wd'),
          'ecwd_view_2'=>__("View 2", 'event-calendar-wd'),
          'ecwd_view_3'=>__("View 3", 'event-calendar-wd'),
          'ecwd_view_4'=>__("View 4", 'event-calendar-wd'),
          'ecwd_view_activate_filters'=>__("Get and activate filters add-on", 'event-calendar-wd'),
          'ecwd_upgrade_paid'=>__('Upgrade to Premium version.','event-calendar-wd'),
          'ecwd_upgrade_premium_version'=>__('Upgrade to Premium version to access three more view options: posterboard, map and 4 days.','event-calendar-wd'),
          'ecwd_filter_addon'=>__('Filter addon should be purchased separately.'),
        ));
      }
    }

  public function add_calendar_shortcode($post_data) {
    global $post;
    
    if (!isset($post->ID)) {
      return $post_data;
    }

    if ($post_data['post_type'] !== 'ecwd_calendar') {
      return $post_data;
    }

    if (get_post_meta($post->ID, "ecwd_added_shortcode", true) === '1') {
      return $post_data;
    }

    $content = $post_data['post_content'];

    if (has_shortcode($content, 'ecwd') == false) {
      $post_data['post_content'] .= sprintf(self::$default_shortcode, $post->ID);
    }

    update_post_meta($post->ID, 'ecwd_added_shortcode', '1');
    return $post_data;
  }


  public function wp_ajax_add_post() {

    $response = array(
      "success" => false,
      "id" => 0
    );

    if (wp_verify_nonce($_POST['nonce'], 'ecwd_ajax_nonce') === false || empty($_POST['post_data'])) {
      die(json_encode($response));
    }

    $post_data = $_POST['post_data'];
    $post_types = array('ecwd_organizer', 'ecwd_venue');


    if (empty($post_data['post_type']) || !in_array($post_data['post_type'], $post_types)) {
      die(json_encode($response));
    }

    if ($post_data['post_type'] == 'ecwd_venue') {
      $venue_data = ECWD_Cpt::add_new_venue($post_data);

      if ($venue_data['id'] == 0) {
        die(json_encode($response));
      }

      $response['venue_data'] = $venue_data;
      $response['venue_data']['edit_link'] = 'post.php?post=' . $venue_data['id'] . '&action=edit';
      $response['success'] = true;
      die(json_encode($response));
    }


    $post_args = array();

    $post_args['post_title'] = (!empty($post_data['title'])) ? sanitize_text_field($post_data['title']) : "";
    $post_args['post_content'] = (!empty($post_data['content'])) ? sanitize_text_field($post_data['content']) : "";
    $post_args['post_type'] = sanitize_text_field($post_data['post_type']);
    $post_args['post_status'] = 'publish';

    if ($post_args['post_type'] == 'ecwd_organizer') {

      $post_args['meta_input']['ecwd_organizer_meta_phone'] = (!empty($post_data['metas']['phone'])) ? sanitize_text_field($post_data['metas']['phone']) : "";
      $post_args['meta_input']['ecwd_organizer_meta_website'] = (!empty($post_data['metas']['website'])) ? sanitize_text_field($post_data['metas']['website']) : "";

    }

    $post_id = wp_insert_post($post_args);

    $response['success'] = ($post_id !== 0);
    $response['id'] = $post_id;
    $response['title'] = sanitize_text_field($post_args['post_title']);

    die(json_encode($response));
  }

  public function ecwd_set_default_calendar(){
    $response = array(
      "success" => false,
      "id" => 0
    );
    if (wp_verify_nonce($_POST['nonce'], 'ecwd_ajax_nonce') === false) {
      die(json_encode($response));
    }
    update_option('ecwd_default_calendar', $_POST['id']);
  }
    /**
     * Localize Script
     */
    public function admin_head() {

        $args = array(
            'post_type' => ECWD_PLUGIN_PREFIX . '_calendar',
            'post_status' => 'publish',
            'posts_per_page' => - 1,
            'ignore_sticky_posts' => 1
        );
        $calendar_posts = get_posts($args);
        $args = array(
            'post_type' => $this->prefix . '_event',
            'post_status' => 'publish',
            'posts_per_page' => - 1,
            'ignore_sticky_posts' => 1
        );
        $event_posts = get_posts($args);
          if(current_user_can('read_private_posts')) {
            $private_args = $args;
            $private_args['post_status'] = array('private');
            $private_events = get_posts($private_args);
            if(!empty($private_events)) {
              foreach($private_events as $private_event) {
                $event_posts[] = $private_event;
              }
            }
          }
      $plugin_url = plugins_url('/', __FILE__);
        ?>
        <!-- TinyMCE Shortcode Plugin -->
        <script type='text/javascript'>
            var ecwd_plugin = {
            'url': '<?php echo $plugin_url; ?>',
                    'ecwd_calendars': [
        <?php foreach ($calendar_posts as $calendar) { ?>
                        {
                        text: '<?php echo str_replace("'", "\'", $calendar->post_title); ?>',
                                value: '<?php echo $calendar->ID; ?>'
                        },
        <?php } ?>
                    ],
                    'ecwd_events': [
                    {text: '<?php _e("None","event-calendar-wd")?>', value: 'none'},
        <?php foreach ($event_posts as $event) { ?>
                        {
                        text: '<?php echo str_replace("'", "\'", $event->post_title); ?>',
                                value: '<?php echo $event->ID; ?>'
                        },
        <?php } ?>
                    ],
                    'ecwd_views': [
                    {text: '<?php _e("None","event-calendar-wd")?>', value: 'none'},
                    {text: '<?php _e("Month","event-calendar-wd")?>', value: 'month'},
                    {text: '<?php _e("List","event-calendar-wd")?>', value: 'list'},
                    {text: '<?php _e("Week","event-calendar-wd")?>', value: 'week'},
                    {text: '<?php _e("Day","event-calendar-wd")?>', value: 'day'},
                    ]
            };
        </script>
        <!-- TinyMCE Shortcode Plugin -->
        <?php
    }
    public function ecwd_shortcode_data(){
      if(wp_verify_nonce ($_GET['nonce'], "ecwd_shortcode")){
        wp_print_scripts('jquery');

        $this->admin_head();
        require_once ("views/admin/ecwd-shortcode-iframe.php");
        die();
      }
      die;
    }
    public function ecwd_shortcode_button() {

        // Don't bother doing this stuff if the current user lacks permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }

        // Add only in Rich Editor mode
        if (get_user_option('rich_editing') == 'true') {
            // filter the tinyMCE buttons and add our own
            add_filter("mce_external_plugins", array($this, 'add_tinymce_plugin'));
            add_filter('mce_buttons', array($this, 'register_buttons'));
        }
    }

// registers the buttons for use
  function register_buttons($buttons) {
    // inserts a separator between existing buttons and our new one
    $screen = get_current_screen();
    if (!$this->ecwd_page() || $screen->post_type == "ecwd_calendar") {
      array_push($buttons, "|", ECWD_PLUGIN_PREFIX);
    }

    return $buttons;
  }

// add the button to the tinyMCE bar
  function add_tinymce_plugin($plugin_array) {
    $screen = get_current_screen();
    if (!$this->ecwd_page() || $screen->post_type == "ecwd_calendar") {
      $plugin_array[ECWD_PLUGIN_PREFIX] = plugins_url('js/admin/editor-buttons.js', __FILE__);
    }

    return $plugin_array;
  }

    //auto update plugin
    function ecwd_update($update, $item) {
        global $ecwd_options;
        if (!isset($ecwd_options['auto_update']) || $ecwd_options['auto_update'] == 1) {
            $plugins = array(// Plugins to  auto-update
                'event-calendar-wd'
            );
            if (in_array($item->slug, $plugins)) {
                return true;
            } // Auto-update specified plugins
            else {
                return false;
            } // Don't auto-update all other plugins
        }
    }

    public function define_admin_constants() {
        if (!defined('ECWD_DIR')) {
            define('ECWD_DIR', dirname(__FILE__));
        }
    }

    /*     * ******ECWD notices*********** */

    function ecwd_admin_notices() {
        // Notices filter and run the notices function.

        $admin_notices = apply_filters('ecwd_admin_notices', array());
        $this->notices->admin_notice($admin_notices);
    }

    // Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked
    public function admin_notice_ignore() {
        $slug = ( isset($_GET['ecwd_admin_notice_ignore']) ) ? sanitize_text_field($_GET['ecwd_admin_notice_ignore']) : '';
        if (isset($_GET['ecwd_admin_notice_ignore']) && current_user_can('manage_options')) {
            $admin_notices_option = get_option('ecwd_admin_notice', array());
            $admin_notices_option[sanitize_text_field($_GET['ecwd_admin_notice_ignore'])]['dismissed'] = 1;
            update_option('ecwd_admin_notice', $admin_notices_option);
            $query_str = remove_query_arg('ecwd_admin_notice_ignore');
            wp_redirect($query_str);
            exit;
        }
    }

    public function ecwd_config() {
        include_once ECWD_DIR . '/includes/ecwd_config.php';
        ECWD_Config::get_instance();
    }

    /**
     * Return an instance of this class.
     */
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }


        return self::$instance;
    }

    /**
     * Return the page
     */
    public function get_page() {
        return $this->ecwd_page();
    }

    /**
     * Return plugin name
     */
    public function get_plugin_title() {
        return __('Event Calendar WD', 'event-calendar-wd');
    }

    public function add_action_links($links) {
      $array = array(
        'settings' => '<a href="' . admin_url(ECWD_MENU_SLUG . '&page=ecwd_general_settings') . '">' . __('Settings', 'event-calendar-wd') . '</a>',
        'events' => '<a href="' . admin_url('edit.php?post_type=ecwd_event') . '">' . __('Events', 'event-calendar-wd') . '</a>',
      );
      if ( !ECWD_PRO ) {
        $array['help'] = '<a href="https://wordpress.org/support/plugin/event-calendar-wd/#new-post" target="_blank">' . __('Help', 'event-calendar-wd') . '</a>';
      }
      return array_merge($array, $links);
    }

  public function topbar() {
    $user_guide_link = 'https://help.10web.io/hc/en-us/articles/';
    $show_guide_link = true;
    $description = "";
    $support_forum_link = 'https://wordpress.org/support/plugin/event-calendar-wd/#new-post';
    $premium_link = 'https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&amp;utm_medium=free_plugin';

    $current_screen = get_current_screen();
    if ($current_screen->parent_file != ECWD_MENU_SLUG) {
      return;
    }
    switch ($current_screen->id) {
      case "edit-ecwd_calendar":
      case "ecwd_calendar":
        $description .= __('Create, edit and delete Calendars','event-calendar-wd');
        $user_guide_link .= '360016280212-Creating-Calendars-on-WordPress';
        break;
      case "edit-ecwd_event":
      case "ecwd_event":
        $description .= __('Create, edit and delete Events','event-calendar-wd');
        $user_guide_link .= '360016499891-Creating-Events';
        break;
      case "edit-ecwd_organizer":
      case "ecwd_organizer":
        $description .= __('Create, edit and delete Organizers','event-calendar-wd');
        $user_guide_link .= '360016500091-Event-Organizers-and-Venues';
        break;
      case "edit-ecwd_venue":
      case "ecwd_venue":
        $description .= __('Create, edit and delete Venues','event-calendar-wd');
        $user_guide_link .= '360016500091-Event-Organizers-and-Venues';
        break;
      case "edit-ecwd_event_category":
        $description .= __('Create, edit and delete Event Categories','event-calendar-wd');
        $user_guide_link .= '360016499951-Event-Categories-and-Tags';
        break;
      case "edit-ecwd_event_tag":
        $description .= __('Create, edit and delete Event Tags','event-calendar-wd');
        $user_guide_link .= '360016499951-Event-Categories-and-Tags';
        break;
      case "ecwd_event_page_ecwd_general_settings":
        $description .= __('Change settings','event-calendar-wd');
        $user_guide_link .= '360016280732-Configuring-Event-Calendar-Settings';
        break;
      case "ecwd_event_page_ecwd_mail_template":
        $description .= __('Event Calendar Subscribe Extension','event-calendar-wd');
        $user_guide_link .= '360016281192-Event-Calendar-Subscribe-Extension-';
        break;
      case "ecwd_event_page_subscribe_events":
        $description .= __('Event Calendar Subscribe Extension','event-calendar-wd');
        $user_guide_link .= '360016281192-Event-Calendar-Subscribe-Extension-';
        break;
      case "ecwd_event_page_ecwd_themes":
        $description .= __('Themes','event-calendar-wd');
        $user_guide_link = 'https://help.10web.io/hc/en-us/articles/360016500311-Editing-Event-Calendar-Themes';
        break;
      case "ecwd_event_page_ecwd_addons":
        $description .= __('Extensions','event-calendar-wd');
        $user_guide_link = 'https://help.10web.io/hc/en-us/sections/360004676531-Extensions';
        break;
    }
    wp_enqueue_style('event-calendar-wd-roboto');
    wp_enqueue_style('event-calendar-wd-pricing');
    ob_start();
    ?>
    <div class="wrap">
      <h1 class="head-notice">&nbsp;</h1>
      <div class="topbar-container">
        <?php
        if ( !ECWD_PRO ) {
          ?>
          <div class="topbar topbar-content">
            <div class="topbar-content-container">
              <div class="topbar-content-title">
                <?php _e('Event Calendar by 10Web Premium', 'event-calendar-wd'); ?>
              </div>
              <div class="topbar-content-body">
                <?php echo $description; ?>
              </div>
            </div>
            <div class="topbar-content-button-container">
              <a href="<?php echo $premium_link; ?>" target="_blank" class="topbar-upgrade-button"><?php _e( 'Upgrade','event-calendar-wd' ); ?></a>
            </div>
          </div>
          <?php
        }
        ?>
        <div class="topbar_cont">
          <?php
          if ( $show_guide_link ) {
            ?>
            <div class="topbar topbar-links">
              <div class="topbar-links-container">
                <a href="<?php echo $user_guide_link; ?>" target="_blank" class="topbar_user_guid">
                  <div class="topbar-links-item">
                    <?php _e('User guide', 'event-calendar-wd'); ?>
                  </div>
                </a>
              </div>
            </div>
            <?php
          }
          if ( !ECWD_PRO ) {
            ?>
            <div class="topbar topbar-links topbar_support_forum">
              <div class="topbar-links-container">
                <a href="<?php echo $support_forum_link; ?>" target="_blank" class="topbar_support_forum">
                  <div class="topbar-links-item">
                    <img src="<?php echo ECWD_URL . '/css/images/help.svg'; ?>" class="help_icon" />
                    <?php _e('Ask a question', 'event-calendar-wd'); ?>
                  </div>
                </a>
              </div>
            </div>
            <?php
          }
          ?>
        </div>
      </div>
    </div>
    <?php
    echo ob_get_clean();
  }

  public function ecwd_helper_bar() {
      $this->topbar();
  }

  public function ecwd_add_plugin_meta_links($meta_fields, $file){

    if(ECWD_MAIN_FILE == $file) {

      $meta_fields[] = "<a href='https://wordpress.org/support/plugin/event-calendar-wd/#new-post' target='_blank'>" . __('Ask a question', 'event-calendar-wd') . "</a>";
      $meta_fields[] = "<a href='https://wordpress.org/support/plugin/event-calendar-wd/reviews#new-post' target='_blank' title='Rate'>
            <i class='ecwd-rate-stars'>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
        . "</i></a>";

      $stars_color = "#ffb900";

      echo "<style>"
        . ".ecwd-rate-stars{display:inline-block;color:" . $stars_color . ";position:relative;top:3px;}"
        . ".ecwd-rate-stars svg{fill:" . $stars_color . ";}"
        . ".ecwd-rate-stars svg:hover{fill:" . $stars_color . "}"
        . ".ecwd-rate-stars svg:hover ~ svg{fill:none;}"
        . "</style>";
    }

    return $meta_fields;
  }

  public static function ecwd_freemius() {
    if ( !isset($_REQUEST['ajax']) ) {

      if ( !class_exists("TenWebLib") ) {
        $plugin_dir = apply_filters('tenweb_free_users_lib_path', array( 'version' => '1.1.1', 'path' => ECWD_DIR ));
        require_once($plugin_dir['path'] . '/wd/start.php');
      }
      global $ecwd_wd_freemius_config;
      $ecwd_options = array(
        "prefix" => "ecwd",
        "wd_plugin_id" => 86,
        "plugin_id" => 25,
        "plugin_title" => "Event Calendar WD",
        "plugin_wordpress_slug" => "event-calendar-wd",
        "plugin_dir" => ECWD_DIR,
        "plugin_main_file" => ECWD_PLUGIN_MAIN_FILE,
        "description" => __('Event Calendar WD is an easy event management and planning tool with advanced features.', 'event-calendar-wd'),
        "plugin_features" => array(
          array(
            "title" => __("Quick and Easy Event Management", "event-calendar-wd"),
            "description" => __("The powerful and intuitive plugin allows you to publish events quick and easy. Add a calendar in minutes and start creating as many events as you want, using event categories and tags, venues, organizers and other custom fields.", "event-calendar-wd"),
          ),
          array(
            "title" => __("Recurring Events", "event-calendar-wd"),
            "description" => __("Use the recurring events functionality to easily manage repeating events. Create events on daily, weekly, monthly or yearly recurrence schedule.", "event-calendar-wd"),
          ),
          array(
            "title" => __("Responsive and SEO-friendly", "event-calendar-wd"),
            "description" => __("The Event calendar WD is responsive and runs very smoothly on all devices. The calendar is created with your website SEO in mind and allows you to use Structured event markup (microdata).", "event-calendar-wd"),
          ),
          array(
            "title" => __("5 Customizable Themes", "event-calendar-wd"),
            "description" => __("The WordPress event calendar plugin comes with 5 pre-designed, beautiful themes. You can choose to use one of the pre-built customizable calendar themes or create your own to better fit your website.", "event-calendar-wd"),
          ),
          array(
            "title" => __("7 Views", "event-calendar-wd"),
            "description" => __("The Event Calendar WD has wide range of view options. The plugin allows to display events in 7 elegant views: month, day, week, list, map, poster board (masonry) and 4 day.", "event-calendar-wd"),
          )
        ),
        "user_guide" => array(
          array(
            "main_title" => __("Installation Wizard/ Options Menu", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016499771-Introducing-WordPress-Event-Calendar",
            "titles" => array(),
          ),
          array(
            "main_title" => __("Calendars", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016280212-Creating-Calendars-on-WordPress",
            "titles" => array(
              array(
                "title" => __("All Calendars", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016280212-Creating-Calendars-on-WordPress",
              ),
              array(
                "title" => __("Adding a Calendar", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016280212-Creating-Calendars-on-WordPress",
              ),
              array(
                "title" => __("Preview/Add Event", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016499891-Creating-Events",
              ),
              array(
                "title" => __("Settings", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016280732-Configuring-Event-Calendar-Settings",
              ),
            )
          ),
          array(
            "main_title" => __("Creating/Modifying Events", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016499891-Creating-Events",
            "titles" => array(
              array(
                "title" => __("All Events", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016499891-Creating-Events",
              ),
              array(
                "title" => __("Adding Events", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016499891-Creating-Events",
              ),
              array(
                "title" => __("Event Categories", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016499951-Event-Categories-and-Tags",
              ),
              array(
                "title" => __("Event Tags", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016499951-Event-Categories-and-Tags",
              ),
            )
          ),
          array(
            "main_title" => __("Creating/Adding Organizers", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016500091-Event-Organizers-and-Venues",
            "titles" => array(
              array(
                "title" => __("All Organizers", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016500091-Event-Organizers-and-Venues",
              ),
              array(
                "title" => __("Adding an organizer", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016500091-Event-Organizers-and-Venues",
              ),
            )
          ),
          array(
            "main_title" => __("Creating/Adding Venues", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016500091-Event-Organizers-and-Venues",
            "titles" => array(
              array(
                "title" => __("All Venues", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016500091-Event-Organizers-and-Venues",
              ),
              array(
                "title" => __("Adding a venue", "event-calendar-wd"),
                "url" => "https://help.10web.io/hc/en-us/articles/360016500091-Event-Organizers-and-Venues",
              ),
            )
          ),
          array(
            "main_title" => __("Calendar Themes", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016500311-Editing-Event-Calendar-Themes",
            "titles" => array(),
          ),
          array(
            "main_title" => __("Publishing the Calendar into a Page/Post", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016280992-Publishing-Event-Calendar-on-WordPress",
            "titles" => array(),
          ),
          array(
            "main_title" => __("Publishing the Calendar as a Widget", "event-calendar-wd"),
            "url" => "https://help.10web.io/hc/en-us/articles/360016280992-Publishing-Event-Calendar-on-WordPress",
            "titles" => array(),
          ),
        ),
        "video_youtube_id" => "htmdAkRuIzw", // e.g. https://www.youtube.com/watch?v=acaexefeP7o youtube id is the acaexefeP7o
        "plugin_wd_url" => "https://10web.io/plugins/wordpress-event-calendar/?utm_source=event_calendar&utm_medium=free_plugin",
        "plugin_wd_demo_link" => "https://demo.10web.io/olddemo/event-calendar",
        "plugin_wd_addons_link" => "https://10web.io/plugins/wordpress-event-calendar#plugin_extensions",
        "plugin_wd_docs_link" => "https://help.10web.io/hc/en-us/sections/360002402952-Event-Calendar",
        "after_subscribe" => add_query_arg(array('post_type' => 'ecwd_event'), admin_url('edit.php')), // this can be plagin overview page or set up page
        "plugin_wizard_link" => NULL,
        "plugin_menu_title" => "Events", //null
        "plugin_menu_icon" =>ECWD_URL."/assets/event-icon.png", // SC_URL . '/images/Staff_Directory_WD_menu.png', //null
        "deactivate" => true,
        "subscribe" => false,
        "custom_post" => ECWD_MENU_SLUG,
        "menu_position" => 25,
        "display_overview" => false,
      );
      if ( get_site_transient('ecwd_uninstall') === '1' ) {
        $ecwd_options['subscribe'] = FALSE;
        $ecwd_options['custom_post'] = NULL;
      }
      ten_web_lib_init($ecwd_options);
      $ecwd_wd_freemius_config = $ecwd_options;
    }
  }
}
