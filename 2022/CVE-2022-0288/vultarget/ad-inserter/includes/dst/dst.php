<?php

/**
 * DST - WP theme and plugin usage tracking client
 * Experimental
 *
 * @version 1.0.7
 * @author Igor Funa <igor.funa/gmail.com>
 * @license MIT
 */

/*

Change Log

DST 1.0.7 - 2020-01-04
- Added timeout for plugin deactivation form

DST 1.0.6 - 2019-02-02
- Nonce value encoded
- Bug fixes

DST 1.0.5 - 2018-10-27
- Improved support for translation
- Improved deactivation form (ajax check)

DST 1.0.4 - 2018-10-05
- Added additional update after upgrade event

DST 1.0.3 - 2018-10-02
- Added option to not show deactivation form again
- Fix to support notice HTML
- No deactivation form code if form not enabled

DST 1.0.2 - 2018-09-23
- Added transient for last action
- Added last action check for update when admin IP address is sent
- Added filter for options

DST 1.0.1 - 2018-09-22
- Fix for admin IP address
- Fix for compatibility with older PHP versions (below 5.4)

DST 1.0.0 - 2018-09-13
- Initial release

*/

if (!defined ('ABSPATH')) exit;

if (!class_exists ('DST_Client')) {

//define ('DST_DEBUG', true);

if (defined ('DST_DEBUG') && DST_DEBUG) {
//  define ('DST_DEBUG_JS', true);                 // if defined, log Javascript debug messages
//  define ('DST_DEBUG_ADMIN_TRACK', true);        // if defined, tracking action is triggered on admin page load
//  define ('DST_DEBUG_TIME_TO_TRACK', true);      // if defined, it is the return value of is_time_to_track ()
//  define ('DST_DEBUG_SCHEDULER', true);          // if defined, tracking will be scheduled every minute
//  define ('DST_DEBUG_LOG', true);                // if defined, log file is written on each tracking action
}

class DST_Client {

  const DST_TEXT_PLUGIN             = 'plugin';
  const DST_TEXT_THEME              = 'theme';

  const DST_DEFAULT_SCHEDULE        = 'monthly';  // 'daily', 'weekly' or 'monthly' schedule (DST defines 'weekly', 'twicemonthly' and 'monthly')

  const DST_PRODUCT_TYPE_PLUGIN     = 1;
  const DST_PRODUCT_TYPE_THEME      = 2;

  const DST_TRACKING_OFF                      = 0;
  const DST_TRACKING_OPTIN                    = 1;
  const DST_TRACKING_INITIALLY_DISABLED       = 2;
  const DST_TRACKING_NO_OPTIN                 = 3;

  const DST_USE_EMAIL_OFF                     = 0;
  const DST_USE_EMAIL_OPTIN_WITH_TRACKING     = 1;
  const DST_USE_EMAIL_OPTIN_AFTER_TRACKING    = 2;
  const DST_USE_EMAIL_INITIALLY_DISABLED      = 3;
  const DST_USE_EMAIL_NO_OPTIN                = 4;

  const DST_MULTISITE_SITES_TRACKING_OFF            = 0;
  const DST_MULTISITE_SITES_TRACKING_AS_MAIN        = 1;
  const DST_MULTISITE_SITES_TRACKING_WAIT_FOR_MAIN  = 2;
  const DST_MULTISITE_SITES_NO_OPTIN                = 3;

  const DST_TRACKING_URL_PARAMETER        = 'dst';

  const DST_THEME_MOD_ALLOW_TRACKING      = 'dst-allow-tracking';

  const DST_TRACKING_ACTION_HOOK          = 'dst_update_';

  const DST_TRANSIENT_THEME_ACTIVATED     = 'dst_theme_activated_';
  const DST_TRANSIENT_ADMIN_IP            = 'dst_admin_ip';
  const DST_TRANSIENT_ADMIN_IP_CACHE_TIME = 10368000;
  const DST_TRANSIENT_LAST_ACTION         = 'dst_last_action_';
  const DST_TRANSIENT_UPGRADED            = 'dst_upgraded_';

  const DST_OPTION_OPTIN_TRACKING         = 'dst_optin_tracking';
  const DST_OPTION_OPTIN_NEWSLETTER       = 'dst_optin_newsletter';
  const DST_OPTION_LAST_TRACK_TIMES       = 'dst_last_track_times';
  const DST_NOTIFICATION_TIMES            = 'dst_notification_times';
  const DST_OPTION_DEACTIVATION_REASON    = 'dst_deactivation_reason';
  const DST_OPTION_DEACTIVATION_DETAILS   = 'dst_deactivation_details';
  const DST_OPTION_HIDE_DEACTIVATION_FORM = 'dst_hide_deactivation_form';

  const DST_FILTER_IS_LOCAL               = 'dst_is_local_';
  const DST_FILTER_OPTIN_NOTICE_TEXT      = 'dst_optin_notice_text_';
  const DST_FILTER_EMAIL_NOTICE_TEXT      = 'dst_email_notice_text_';
  const DST_FILTER_FORM_TEXT              = 'dst_deactivation_form_text_';
  const DST_FILTER_SCHEDULE               = 'dst_schedule_';
  const DST_FILTER_OPTIONS                = 'dst_options_';

  const DST_ACTION_NONE                             = 0;
  const DST_ACTION_START_TRACKING                   = 1;
  const DST_ACTION_END_TRACKING                     = 2;
  const DST_ACTION_UPGRADE                          = 3;
  const DST_ACTION_OPTIN_TRACKING                   = 4;
  const DST_ACTION_OPTIN_NEWSLETTER                 = 5;
  const DST_ACTION_OPTIN_TRACKING_NEWSLETTER        = 6;
  const DST_ACTION_OPTIN_NO_NEWSLETTER              = 7;
  const DST_ACTION_SCHEDULE                         = 8;
  const DST_ACTION_LIVE                             = 9;
  const DST_ACTION_LIVE_UPDATED                     = 10;
  const DST_ACTION_ADMIN_IP_SET                     = 11;

  private $dst_default_settings = array (
    'tracking_url'           => '',
    'main_file'              => '',
    'options'                => array (),
    'tracking'               => self::DST_TRACKING_OPTIN,
    'use_email'              => self::DST_USE_EMAIL_OPTIN_WITH_TRACKING,
    'deactivation_form'      => true,
    'track_local'            => true,
    'product_type'           => self::DST_PRODUCT_TYPE_PLUGIN,
    'theme_tracking'         => false,
    'admin_ip_tracking'      => true,
    'notice_icon'            => '',
    'delay_notification'     => 0,
    'multisite_tracking'     => self::DST_MULTISITE_SITES_TRACKING_WAIT_FOR_MAIN,
    'remove'                 => false,
  );

  private $array_options = array (
    self::DST_OPTION_OPTIN_TRACKING,
    self::DST_OPTION_OPTIN_NEWSLETTER,
    self::DST_OPTION_LAST_TRACK_TIMES,
    self::DST_NOTIFICATION_TIMES,
    self::DST_OPTION_DEACTIVATION_REASON,
    self::DST_OPTION_DEACTIVATION_DETAILS,
    self::DST_OPTION_HIDE_DEACTIVATION_FORM,
  );

  private $version = '1.0.7';
  private $tracking_url;
  private $main_file;
  private $slug;
  private $options;
  private $tracking;
  private $deactivation_form;
  private $use_email;
  private $track_local;
  private $product_type;
  private $theme_tracking;
  private $admin_ip_tracking;
  private $notice_icon;
  private $delay_notification;
  private $action;

  private $tracking_known;
  private $use_email_known;

  public function __construct ($_settings = array ()) {

    if (!isset ($_settings ['main_file'])) {
//      $debug_backtrace = debug_backtrace (false, 1);
      $debug_backtrace = debug_backtrace (0, 1);
      $_settings ['main_file'] = $debug_backtrace [0]['file'];
    }

    $settings = array_merge ($this->dst_default_settings, $_settings);

    $this->tracking_url           = $settings ['tracking_url'];
    $this->main_file              = $settings ['main_file'];
    $this->options                = $settings ['options'];
    $this->tracking               = $settings ['tracking'];
    $this->deactivation_form      = $settings ['deactivation_form'];
    $this->use_email              = $settings ['use_email'];
    $this->track_local            = $settings ['track_local'];
    $this->product_type           = $settings ['product_type'];
    $this->theme_tracking         = $settings ['theme_tracking'];
    $this->admin_ip_tracking      = $settings ['admin_ip_tracking'];
    $this->notice_icon            = $settings ['notice_icon'];
    $this->delay_notification     = $settings ['delay_notification'];
    $this->multisite_tracking     = $settings ['multisite_tracking'];
    $this->action                 = self::DST_ACTION_NONE;

    $this->slug = '';
    if (basename ($this->main_file, '.php') != 'functions') {
      $plugin_basename = plugin_basename ($this->main_file);
      if (strpos ($plugin_basename, DIRECTORY_SEPARATOR)) {
        $this->slug = str_replace (DIRECTORY_SEPARATOR.basename ($this->main_file), '', $plugin_basename);
      } else $this->slug = basename ($this->main_file, '.php');
    } else {
        $this->product_type = self::DST_PRODUCT_TYPE_THEME;
        $this->slug = get_option ('stylesheet');
    }

    if ($settings ['remove']) {
      $this->delete_settings ();
      return;
    }

    if ($this->use_email == self::DST_USE_EMAIL_INITIALLY_DISABLED) {
      $use_email = get_option (self::DST_OPTION_OPTIN_NEWSLETTER);
      if (!isset ($use_email [$this->slug])) set_use_email (false);
    }

    if (is_multisite () && !is_main_site ()) {
      switch ($this->multisite_tracking) {
        case self::DST_MULTISITE_SITES_TRACKING_OFF:
          return;
          break;
        case self::DST_MULTISITE_SITES_TRACKING_WAIT_FOR_MAIN:
          $this->tracking   = self::DST_TRACKING_OFF;
          $this->use_email  = self::DST_USE_EMAIL_OFF;

          if (defined ('BLOG_ID_CURRENT_SITE')) {
            $optin_tracking = get_blog_option (BLOG_ID_CURRENT_SITE, self::DST_OPTION_OPTIN_TRACKING);
            if (isset ($optin_tracking [$this->slug]) && $optin_tracking [$this->slug])
              $this->tracking  = self::DST_TRACKING_NO_OPTIN;

            $optin_newsletter = get_blog_option (BLOG_ID_CURRENT_SITE, self::DST_OPTION_OPTIN_NEWSLETTER);
            if (isset ($optin_newsletter [$this->slug]) && $optin_newsletter [$this->slug])
              $this->use_email  = self::DST_USE_EMAIL_NO_OPTIN;
          }

          break;
        case self::DST_MULTISITE_SITES_NO_OPTIN:
          $this->tracking  = self::DST_TRACKING_NO_OPTIN;
          $this->use_email = self::DST_USE_EMAIL_NO_OPTIN;
          break;
      }
    }

    if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {
      $this->theme_tracking = get_theme_mod (self::DST_THEME_MOD_ALLOW_TRACKING, 0);
      add_action ('after_switch_theme',   array ($this, 'start_tracking_theme'));
      add_action ('switch_theme',         array ($this, 'end_tracking'));
      add_action ('customize_save_after', array ($this, 'customize_save_after'));

    } else {
        register_activation_hook   ($this->main_file, array ($this, 'start_tracking'));
        register_deactivation_hook ($this->main_file, array ($this, 'end_tracking'));
    }

    if (did_action ('init')) {
      $this->load_text_domain ();
    } else {
        add_action ('init', array ($this, 'load_text_domain'));
    }

    $this->init();
  }

  public function load_text_domain () {
    $domain = 'dst';
    $locale = apply_filters (
      'plugin_locale',
      (is_admin() && function_exists ('get_user_locale')) ? get_user_locale() : get_locale(),
      $domain
    );

    $moFile = $domain . '-' . $locale . '.mo';
    $path = realpath (dirname (__FILE__) . '/languages');

    if ($path && file_exists ($path)) {
      load_textdomain ($domain, $path . '/' . $moFile);
    }
  }


  public function init() {
    add_filter ('cron_schedules', array ($this, 'dst_cron_schedules'));
    add_action (self::DST_TRACKING_ACTION_HOOK . $this->slug, array ($this, 'update'));

    if (defined ('DST_DEBUG_ADMIN_TRACK') && DST_DEBUG_ADMIN_TRACK) {
      add_action ('admin_init', array ($this, 'update'));
    }

    add_action ('admin_init',             array ($this, 'init_data'));
    add_action ('admin_notices',          array ($this, 'optin_notice'));
    add_action ('network_admin_notices',  array ($this, 'optin_notice'));
    add_action ('admin_footer',           array ($this, 'admin_footer'));

    // Upgrade
    add_action ('upgrader_process_complete', array ($this, 'dst_upgrader_process_complete'), 10, 2);

    // Deactivation
    add_filter ('plugin_action_links_' . plugin_basename ($this->main_file), array ($this, 'filter_action_links'));
    add_action ('admin_footer-plugins.php', array ($this, 'generate_deactivation_form'));
    add_action ('wp_ajax_dst_ajax_' . $this->slug, array ($this, 'process_ajax'));

    $this->check_schedule ();

    if ($upgrade_time = get_transient (self::DST_TRANSIENT_UPGRADED . $this->slug)) {
      $this->log_event ('UPGRADED ' .(time () - $upgrade_time) . ' s ago');

      delete_transient (self::DST_TRANSIENT_UPGRADED . $this->slug);
      $this->action = self::DST_ACTION_UPGRADE;
      $this->update (true);
    }

    if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {
      if ($activation_time = get_transient (self::DST_TRANSIENT_THEME_ACTIVATED . $this->slug)) {
        $this->log_event ('THEME ACTIVATED ' .(time () - $activation_time) . ' s ago');

        delete_transient (self::DST_TRANSIENT_THEME_ACTIVATED . $this->slug);
        $this->action = self::DST_ACTION_START_TRACKING;
        $this->update ();
      }
    }
  }

  public function start_tracking () {
    $this->action = self::DST_ACTION_START_TRACKING;
    $this->update (true);
  }

  public function start_tracking_theme () {
    $this->log_event ('after_switch_theme');

    set_transient (self::DST_TRANSIENT_THEME_ACTIVATED . $this->slug, time (), 20);
  }

  function customize_save_after () {
    if (get_theme_mod (self::DST_THEME_MOD_ALLOW_TRACKING, 0) && !$this->theme_tracking) {
      $this->log_event ('THEME TRACKING ENABLED');

      $this->theme_tracking = true;
      $this->action = self::DST_ACTION_OPTIN_TRACKING;
      $this->update (true);
    }
  }

    public function dst_cron_schedules ($schedules) {
      if (defined ('DST_DEBUG_SCHEDULER') && DST_DEBUG_SCHEDULER) {
        $schedules ['everyminute'] = array (
          'interval'  => 60,
          'display'   => __('Every minute', 'dst')
        );
      }
      $schedules ['weekly'] = array (
        'interval'  => 604800,
        'display'   => __('Once Weekly', 'dst')
      );
      $schedules ['monthly'] = array (
        'interval'  => 2635200,
        'display'   => __('Once Monthly', 'dst')
      );
      $schedules ['twicemonthly'] = array (
        'interval'  => 1317600,
        'display'   => __('Twice Monthly', 'dst')
      );
      return $schedules;
    }

    public function get_schedule () {
      if (defined ('DST_DEBUG_SCHEDULER') && DST_DEBUG_SCHEDULER) {
        return ('everyminute');
      }

      $schedule = apply_filters (self::DST_FILTER_SCHEDULE . $this->slug, self::DST_DEFAULT_SCHEDULE);

      return $schedule;
    }

    public function check_schedule () {
      $schedule = $this->get_schedule ();
      $hook = self::DST_TRACKING_ACTION_HOOK . $this->slug;
      if (!wp_next_scheduled ($hook) || wp_get_schedule ($hook) != $schedule) {
        wp_clear_scheduled_hook ($hook);
        wp_schedule_event (time (), $schedule, $hook);
      }
    }

    public function delete_settings () {
      foreach ($this->array_options as $array_option) {
        $saved_option = get_option ($array_option);
        if (isset ($saved_option [$this->slug])) {
          unset ($saved_option [$this->slug]);
          if (!empty ($saved_option)) {
            update_option ($array_option, $saved_option);
          } else delete_option ($array_option);
        }
      }

      wp_clear_scheduled_hook (self::DST_TRACKING_ACTION_HOOK . $this->slug);
      delete_transient (self::DST_TRANSIENT_THEME_ACTIVATED . $this->slug);
      delete_transient (self::DST_TRANSIENT_ADMIN_IP);
      delete_transient (self::DST_TRANSIENT_LAST_ACTION . $this->slug);
      delete_transient (self::DST_TRANSIENT_UPGRADED . $this->slug);

      if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {
        remove_theme_mod (self::DST_THEME_MOD_ALLOW_TRACKING);
      }
    }

    public function update ($force = false) {
      if (!$this->tracking_url) return;

      if (!$this->get_tracking ()) return;

      if (!$force) {
        if (!$this->is_time_to_track ()) return;
      }

      $data = $this->get_data ();

      $this->send_data ($data);
    }

    function dst_upgrader_process_complete ($upgrader_object, $options) {
      $this->log_event ('', serialize ($options));

      $this->update_admin_ip ();
      if (is_array ($options) && array_key_exists ('action', $options) && $options ['action'] == 'update' && array_key_exists ('type', $options)) {
        if ($options ['type'] == 'plugin' && array_key_exists ('plugins', $options) && is_array ($options ['plugins']) && !empty ($options ['plugins'])) {
          $this_plugin = plugin_basename ($this->main_file);
          foreach ($options ['plugins'] as $plugin) {
            if ($plugin == $this_plugin) {
              $this->action = self::DST_ACTION_UPGRADE;
              $this->update (true);
              set_transient (self::DST_TRANSIENT_UPGRADED . $this->slug, time (), 60);
              break;
            }
          }
        }
        elseif ($options ['type'] == 'theme' && array_key_exists ('themes', $options) && is_array ($options ['themes']) && !empty ($options ['themes'])) {
          foreach ($options ['themes'] as $theme) {
            if ($theme == $this->slug) {
              $this->action = self::DST_ACTION_UPGRADE;
              $this->update (true);
              set_transient (self::DST_TRANSIENT_UPGRADED . $this->slug, time (), 60);
              break;
            }
          }
        }
      }
    }

    public function log_event ($event_data = '', $data = '') {
      if (defined ('DST_DEBUG_LOG') && DST_DEBUG_LOG) {
        $debug_backtrace = debug_backtrace (false, 7);
        $backtrace = array ();
        foreach ($debug_backtrace as $index => $debug_backtrace_data) {
          if ($index == 0) continue;
          if (isset ($debug_backtrace_data ['function'])) {
            $backtrace []= $debug_backtrace_data ['function'];
          }
        }

        if ($event_data != '') $event_data = '[' . $event_data . '] ';
        $line = sprintf ('%s  %02d  % -30s  %s%s   %s', date ('Y-m-d H:i:s'), $this->action, $this->slug, $event_data, implode (' - ', $backtrace), $data) . PHP_EOL;
        $file_path = __DIR__ . '/dst.log';

        $file = fopen ($file_path, "a");
        fwrite ($file, $line);
        fclose ($file);
      }
    }

    public function send_data ($data) {
      if (!$this->tracking_url) return;

      $this->log_event ('', serialize ($data));

      set_transient (self::DST_TRANSIENT_LAST_ACTION . $this->slug, $data ['action'], 365 * 24 * 3600);

      $request = wp_remote_post (
        esc_url ($this->tracking_url . '?' . self::DST_TRACKING_URL_PARAMETER . '=' . $this->slug),
        array (
          'method'      => 'POST',
          'timeout'     => 20,
          'redirection' => 5,
          'httpversion' => '1.1',
          'blocking'    => false,
          'body'        => $data,
          'user-agent'  => 'PUT/1.0.0; ' . home_url ()
        )
      );

      $this->set_track_time ();

      if (is_wp_error ($request)) {
        return $request;
      }
    }

    public function get_data () {
      $data ['message'] = '';

      $data = array (
        'slug'            => sanitize_text_field ($this->slug),
        'url'             => home_url (),
        'site_name'       => get_bloginfo ('name'),
        'wp_version'      => get_bloginfo ('version'),
        'language'        => get_bloginfo ('language'),
        'charset'         => get_bloginfo ('charset'),
        'client_location' => __FILE__,
        'client_version'  => $this->version,
        'server'          => isset ($_SERVER ['SERVER_SOFTWARE']) ? $_SERVER ['SERVER_SOFTWARE'] : '',
        'php_version'     => phpversion (),
        'multisite'       => is_multisite() ? (is_main_site () ? 1 : 2) : 0,
        'network_url'     => rtrim (network_home_url (), '/'),
        'site_count'      => is_multisite() ? get_blog_count() : '',
      );

      if ($this->admin_ip_tracking) {
        $this->update_admin_ip ();
        $data ['admin_ip'] = get_transient (self::DST_TRANSIENT_ADMIN_IP) ? get_transient (self::DST_TRANSIENT_ADMIN_IP) : '';
      }

      $data ['use_email'] = $this->get_use_email ();

      if ($this->get_use_email ()) {
        $data ['email'] = get_bloginfo ('admin_email');
      }

      if (!function_exists ('get_plugins')) {
        include ABSPATH . '/wp-admin/includes/plugin.php';
      }

      $all_plugins    = array_keys (get_plugins());
      $active_plugins = get_option ('active_plugins', array());

      $plugins = array ();
      foreach ($all_plugins as $key => $plugin) {
        if (in_array ($plugin, $active_plugins))
          $plugins [$plugin] = 1; else
            $plugins [$plugin] = 0;
      }

      $data ['plugins'] = json_encode ($plugins);

      $data ['text_direction'] = 'LTR';
      if (function_exists ('is_rtl')) {
        if (is_rtl ()) {
          $data ['text_direction'] = 'RTL';
        }
      } else $data ['text_direction'] = '';

      $data ['status'] = '1';
      if ($this->product_type == self::DST_PRODUCT_TYPE_PLUGIN) {
        $plugin = $this->plugin_data ();
        if (empty ($plugin)) {
          $data ['message'] .= __('No plugin data.', 'dst');
          $data ['status'] = '-1';
        } else {
          if (isset ($plugin ['Name'])) {
            $data ['plugin'] = sanitize_text_field ($plugin ['Name']);
          }
          if( isset( $plugin ['Version'] ) ) {
            $data ['plugin_version'] = sanitize_text_field ($plugin ['Version']);
          }
        }
      } else {
          $data ['plugin'] = 'theme';
        }

      $data ['options'] = json_encode (apply_filters (self::DST_FILTER_OPTIONS . $this->slug, $this->options));

      $theme = wp_get_theme ();
      if ($theme->Name) {
        $data ['theme'] = sanitize_text_field ($theme->Name);
      }
      if ($theme->Version) {
        $data ['theme_version'] = sanitize_text_field ($theme->Version);
      }
      if ($theme->Template) {
        $data ['theme_parent'] = sanitize_text_field ($theme->Template);
      }

      if ($this->action == self::DST_ACTION_NONE) {
        if ($this->product_type == self::DST_PRODUCT_TYPE_THEME && $activation_time = get_transient (self::DST_TRANSIENT_THEME_ACTIVATED . $this->slug)) {
          $this->action = self::DST_ACTION_START_TRACKING;
          delete_transient (self::DST_TRANSIENT_THEME_ACTIVATED . $this->slug);
        } else {
            $debug_backtrace = debug_backtrace (false, 3);
            // get_data - update - call_user_func_array
            // BACKTRACE: update - apply_filters - do_action - do_action_ref_array

            if ($debug_backtrace [1]['function'] == 'update' && ($debug_backtrace [2]['function'] == 'call_user_func_array' || $debug_backtrace [2]['function'] == 'apply_filters')) {
              $this->action = self::DST_ACTION_SCHEDULE;
            }
            else {
              $debug_backtrace = debug_backtrace (false, 7);
              $backtrace = array ();
              foreach ($debug_backtrace as $index => $debug_backtrace_data) {
                if ($index == 0) continue;
                if (isset ($debug_backtrace_data ['function'])) {
                  $backtrace []= $debug_backtrace_data ['function'];
                }
              }

              $data ['message'] = 'BACKTRACE: ' . implode (' - ', $backtrace);
            }
        }
      }

      $data ['action'] = $this->action;

      return $data;
    }

    public function plugin_data () {
      if ($this->product_type == self::DST_PRODUCT_TYPE_PLUGIN) {
        if (!function_exists ('get_plugin_data')) {
          include ABSPATH . '/wp-admin/includes/plugin.php';
        }
        $plugin = get_plugin_data ($this->main_file);
        return $plugin;
      }
    }

    public function end_tracking () {
      if (!$this->tracking_url) return;

      if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {
        $allow_tracking = $this->theme_tracking;
      } else {
          $allow_tracking = $this->get_tracking ();
        }

      if (!$allow_tracking) return;

      $this->action = self::DST_ACTION_END_TRACKING;

      $data = $this->get_data ();

      $data ['status'] = '0';

      if ($deactivation_reason = $this->get_deactivation_reason ()) {
        $data ['deactivation_reason'] = $deactivation_reason;
      }
      if ($deactivation_details = $this->get_deactivation_details ()) {
        $data ['deactivation_details'] = $deactivation_details;
      }

      $this->send_data ($data);

      wp_clear_scheduled_hook (self::DST_TRACKING_ACTION_HOOK . $this->slug);
      delete_transient (self::DST_TRANSIENT_THEME_ACTIVATED . $this->slug);

      $track_time = get_option (self::DST_OPTION_LAST_TRACK_TIMES);
      if (isset ($track_time [$this->slug])) {
        unset ($track_time [$this->slug]);
        update_option (self::DST_OPTION_LAST_TRACK_TIMES, $track_time);
      }
    }

    public function get_plugin_tracking () {
      switch ($this->tracking) {
        case self::DST_TRACKING_OFF:
        case self::DST_TRACKING_NO_OPTIN:
          return null;
          break;
      }

      $allow_tracking = get_option (self::DST_OPTION_OPTIN_TRACKING);
      if (!isset ($allow_tracking [$this->slug])) return null;
      return $allow_tracking [$this->slug];
    }

    public function get_tracking () {
      switch ($this->tracking) {
        case self::DST_TRACKING_OFF:
          return false;
          break;
        case self::DST_TRACKING_NO_OPTIN:
          return true;
          break;
      }

      if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {
        return get_theme_mod (self::DST_THEME_MOD_ALLOW_TRACKING, 0);
      } else {
          $allow_tracking = get_option (self::DST_OPTION_OPTIN_TRACKING);
          if (!isset ($allow_tracking [$this->slug])) return false;
          return $allow_tracking [$this->slug];
        }
    }

    public function set_tracking ($enabled) {
      if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {
        if ($enabled != $this->get_tracking ()) {
          set_theme_mod (self::DST_THEME_MOD_ALLOW_TRACKING, $enabled);
          if ($enabled) {
            $this->action = self::DST_ACTION_OPTIN_TRACKING;
            $this->update (true);
          }
        }
      } else {
          $allow_tracking = get_option (self::DST_OPTION_OPTIN_TRACKING);
          if (!isset ($allow_tracking [$this->slug]) || $enabled != $this->get_tracking ()) {
            if (empty ($allow_tracking) || !is_array ($allow_tracking)) {
              $allow_tracking = array ($this->slug => (int) $enabled);
            } else {
                $allow_tracking [$this->slug] = (int) $enabled;
              }
            update_option (self::DST_OPTION_OPTIN_TRACKING, $allow_tracking);
            if ($enabled) {
              $this->action = self::DST_ACTION_OPTIN_TRACKING;
              $this->update (true);
            }
          }
        }
    }

    public function get_hide_deactivation_form () {
      if ($this->product_type != self::DST_PRODUCT_TYPE_PLUGIN) {
        return false;
      } else {
          $hide_deactivation_form = get_option (self::DST_OPTION_HIDE_DEACTIVATION_FORM);
          if (!isset ($hide_deactivation_form [$this->slug])) return false;
          return $hide_deactivation_form [$this->slug];
        }
    }

    public function set_hide_deactivation_form ($hide) {
      if ($this->product_type != self::DST_PRODUCT_TYPE_PLUGIN) return;

      $hide_deactivation_form = get_option (self::DST_OPTION_HIDE_DEACTIVATION_FORM);
      if (!isset ($hide_deactivation_form [$this->slug]) || $hide != $this->get_hide_deactivation_form ()) {
        if (empty ($hide_deactivation_form) || !is_array ($hide_deactivation_form)) {
          $hide_deactivation_form = array ($this->slug => (int) $hide);
        } else {
            $hide_deactivation_form [$this->slug] = (int) $hide;
          }
        update_option (self::DST_OPTION_HIDE_DEACTIVATION_FORM, $hide_deactivation_form);
      }
    }

    public function is_time_to_track () {
      if (defined ('DST_DEBUG_TIME_TO_TRACK')) {
        $this->log_event ('DST_DEBUG_TIME_TO_TRACK: ' . ((bool) DST_DEBUG_TIME_TO_TRACK));
        return DST_DEBUG_TIME_TO_TRACK;
      }

      $track_times = get_option (self::DST_OPTION_LAST_TRACK_TIMES, array());

      if (!isset ($track_times [$this->slug])) return true;

      $schedule = $this->get_schedule ();

      if (defined ('DST_DEBUG_SCHEDULER') && DST_DEBUG_SCHEDULER) {
        $period = 'everyminute';
      }
      elseif ($schedule == 'hourly') $period = 'hour';
      elseif ($schedule == 'daily') $period = 'day';
      elseif ($schedule == 'weekly') $period = 'week';
      elseif ($schedule == 'monthly') $period = 'month';
      else {
        $this->log_event ('UNKNOWN SCHEDULE: ' . $schedule);
        $period = 'month';
      }

      if ($track_times [$this->slug] < strtotime ('-1 ' . $period)) return true;

      if (defined ('DST_DEBUG_LOG') && DST_DEBUG_LOG) {
        $difference = $track_times [$this->slug] - strtotime ('-1 ' . $period);
        $days = floor ($difference / (3600 * 24));
        $hours = floor (($difference - $days * (3600 * 24)) / 3600);
        $minutes = floor (($difference - $days * (3600 * 24) - $hours * 3600) / 60);
        $seconds = $difference - $days * (3600 * 24) - $hours * 3600 - $minutes * 60;
        $this->log_event ("$schedule: " . sprintf ('%02d %02d:%02d:%02d', $days, $hours, $minutes, $seconds) . ' to go');
      }

      return false;
    }

    public function set_track_time () {
      $track_times = get_option (self::DST_OPTION_LAST_TRACK_TIMES, array ());
      $track_times [$this->slug] = time();
      update_option (self::DST_OPTION_LAST_TRACK_TIMES, $track_times);
    }

    public function update_admin_ip ($update_status = false) {
      if ($this->admin_ip_tracking && current_user_can ('administrator')) {
        $saved_admin_ip_address = get_transient (self::DST_TRANSIENT_ADMIN_IP);
        $transient_timeout = get_option ('_transient_timeout_' . self::DST_TRANSIENT_ADMIN_IP);
        if (!$saved_admin_ip_address || !($transient_timeout) || ($transient_timeout - time ()) < self::DST_TRANSIENT_ADMIN_IP_CACHE_TIME / 2) {
          $admin_ip_address = $this->get_client_ip_address ();
          set_transient (self::DST_TRANSIENT_ADMIN_IP, $admin_ip_address, self::DST_TRANSIENT_ADMIN_IP_CACHE_TIME);

          if (!$saved_admin_ip_address && $update_status && get_transient (self::DST_TRANSIENT_LAST_ACTION . $this->slug) == self::DST_ACTION_SCHEDULE) {
            $this->action = self::DST_ACTION_ADMIN_IP_SET;
            $this->update (true);
          }
        }
      }
    }

    public function init_data () {
      $notification_times = get_option (self::DST_NOTIFICATION_TIMES, array ());
      if (!isset ($notification_times [$this->slug])) {
        $notification_time = time() + absint ($this->delay_notification);
        $notification_times [$this->slug] = $notification_time;
        update_option (self::DST_NOTIFICATION_TIMES, $notification_times);
      }
      $this->update_admin_ip (true);
    }

    public function get_is_notification_time () {
      $notification_times = get_option (self::DST_NOTIFICATION_TIMES, array ());
      $time = time();
      if (isset ($notification_times [$this->slug])) {
        $notification_time = $notification_times [$this->slug];
        if ($notification_time <= $time) return true;
      }
      return false;
    }

    public function get_use_email () {
      switch ($this->use_email) {
        case self::DST_USE_EMAIL_OFF:
          return false;
          break;
        case self::DST_USE_EMAIL_OPTIN_WITH_TRACKING:
        case self::DST_USE_EMAIL_OPTIN_AFTER_TRACKING:
          switch ($this->tracking) {
            case self::DST_TRACKING_OFF:
              return false;
              break;
            case self::DST_TRACKING_NO_OPTIN:
              return true;
              break;
          }
          break;
        case self::DST_USE_EMAIL_NO_OPTIN:
          return true;
          break;
      }

      $use_email = get_option (self::DST_OPTION_OPTIN_NEWSLETTER);

      if (!isset ($use_email [$this->slug])) return false;
      return $use_email [$this->slug];
    }

    public function set_use_email ($enabled) {
      $use_email = get_option (self::DST_OPTION_OPTIN_NEWSLETTER);
      if (!isset ($use_email [$this->slug]) || $enabled != $this->get_use_email ()) {
        if (empty ($use_email) || !is_array ($use_email)) {
          $use_email = array ($this->slug => (int) $enabled);
        } else {
            $use_email [$this->slug] = (int) $enabled;
          }
        update_option (self::DST_OPTION_OPTIN_NEWSLETTER, $use_email);
      }
    }

    public function get_deactivation_reason () {
      $reasons = get_option (self::DST_OPTION_DEACTIVATION_REASON);
      if (!isset ($reasons [$this->slug])) return '';
      return $reasons [$this->slug];
    }

    public function set_deactivation_reason ($reason) {
      $reasons = get_option (self::DST_OPTION_DEACTIVATION_REASON);
      if (empty ($reasons) || !is_array ($reasons)) {
        $reasons = array ($this->slug => $reason);
      } else {
          $reasons [$this->slug] = $reason;
        }
      update_option (self::DST_OPTION_DEACTIVATION_REASON, $reasons);
    }

    public function get_deactivation_details () {
      $reasons = get_option (self::DST_OPTION_DEACTIVATION_DETAILS);
      if (!isset ($reasons [$this->slug])) return '';
      return $reasons [$this->slug];
    }

    public function set_deactivation_details ($detail) {
      $details = get_option (self::DST_OPTION_DEACTIVATION_DETAILS);
      if (empty ($details) || !is_array ($details)) {
        $details = array ($this->slug => $detail);
      } else {
          $details [$this->slug] = $detail;
        }
      update_option (self::DST_OPTION_DEACTIVATION_DETAILS, $details);
    }

    public function notice_html ($notice_text, $action_yes, $action_no, $class = 'dst-notice') {
      $name = '';
      if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {
        $theme = wp_get_theme ();
        if ($theme->Name) {
          $name = sanitize_text_field ($theme->Name);
        }

      } else {
          $plugin = $this->plugin_data ();
          $name = $plugin ['Name'];
        }
?>
        <div class="notice notice-info <?php echo $class; ?> dst-no-phone" style="display: none;" data-slug="<?php echo $this->slug; ?>" data-value="<?php echo base64_encode (wp_create_nonce ("dst_data")); ?>">
<?php
        if ($this->notice_icon != '') {
?>
          <div class="dst-notice-element">
            <img src="<?php echo $this->notice_icon; ?>" style="width: 50px; margin: 5px 10px 0px 10px;" />
          </div>
<?php
        }
?>
          <div class="dst-notice-element" style="width: 100%; padding: 0 10px 0;">
<?php
        $notice = str_replace (array ('[BR]', '[STRONG]', '[/STRONG]', '[NAME]'), array ("<br />", '<strong>', '</strong>', esc_html ($name)), $notice_text);
        $paragraphs = explode ('[P]', $notice);
        foreach ($paragraphs as $paragraph) {
          echo '<p>',$paragraph, '</p>';
        }
?>
          </div>
          <div class="dst-notice-element dst-notice-buttons last">
            <div class="dst-notice-text-button dst-notice-dismiss" data-action="<?php echo $action_yes; ?>">
              <button class="button-primary"><?php _ex("Allow", 'Button', 'dst'); ?></button>
            </div>
            <div class="dst-notice-text-button dst-notice-dismiss" data-action="<?php echo $action_no; ?>"><?php _ex("Do not allow", 'Button', 'dst'); ?></div>
          </div>
        </div>
<?php
    }

    public function notice_needed () {
      if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) {

        switch ($this->tracking) {
          case self::DST_TRACKING_OPTIN:
            $mod = get_theme_mod (self::DST_THEME_MOD_ALLOW_TRACKING, '#');
            $this->tracking_known = $mod !== '#';
            break;
          default:
            $this->tracking_known = true;
            break;
        }

      } else {
          switch ($this->tracking) {
            case self::DST_TRACKING_OPTIN:
              $allow_tracking = get_option (self::DST_OPTION_OPTIN_TRACKING);
              $this->tracking_known = isset ($allow_tracking [$this->slug]);
              break;
            default:
              $this->tracking_known = true;
              break;
          }
        }


      switch ($this->use_email) {
        case self::DST_USE_EMAIL_OPTIN_WITH_TRACKING:
        case self::DST_USE_EMAIL_OPTIN_AFTER_TRACKING:

          switch ($this->tracking) {
            case self::DST_TRACKING_OPTIN:
              $use_email = get_option (self::DST_OPTION_OPTIN_NEWSLETTER);
              $this->use_email_known = isset ($use_email [$this->slug]);
              break;
            default:
              $this->use_email_known = true;
              break;
          }

          break;
        default:
          $this->use_email_known = true;
          break;
      }

      return (!$this->tracking_known || !$this->use_email_known);
    }

    public function admin_footer () {

      if (!current_user_can ('manage_options')) return;

      if (!$this->notice_needed ()) return;
?>
<style>
  .dst-notice {
    vertical-align: middle;
    padding: 0;
    border-top: 1px solid #E5E5E5;
    border-right: 1px solid #E5E5E5;
    border-radius: 6px;
  }
  .dst-notice-hidden {
    display: none;
  }
  .dst-notice img {
  }
  .dst-notice-element {
    display: table-cell;
    vertical-align: middle;
    color: #444;
    font-size: 13px;
    font-family: 'Open Sans', sans-serif;
    user-select: none;
  }
  .dst-notice-buttons {
    border-left: 1px solid #E5E5E5;
    padding: 0 15px;
    background: #F8F8F8;
    position: relative;
    white-space: nowrap;
    text-align: center;
  }
  .dst-notice-buttons.last {
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
  }
  .dst-notice-buttons button.button-primary {
    margin: 10px;
    line-height: 28px;
  }
  .dst-notice-buttons a {
    text-decoration: none;
    box-shadow: 0 0 0;
    color: #fff;
  }
  .dst-notice-text-button {
    display: inline-block;
    color: #bbb;
    cursor: pointer;
    margin: 0px 10px 0px;
    vertical-align: middle;
  }

  @media (max-width: 1200px) {
    .dst-notice-text-button {
      display: block;
    }
  }

  @media (max-width: 768px) {
    .dst-no-phone {
      display: none!important;
    }
  }
</style>
<script>
jQuery (document).ready (function ($) {
  var dst_debugging = <?php echo defined ('DST_DEBUG_JS') && DST_DEBUG_JS ? 'true' : 'false'; ?>;

  $(document).on ('click', '.dst-notice[data-slug=<?php echo $this->slug; ?>] .dst-notice-dismiss', function () {
    var notice_div = $(this).closest ('.dst-notice');
    var nonce = atob (notice_div.attr ('data-value'));
    var slug = '<?php echo $this->slug; ?>';
    var action = $(this).data ('action');

    if (dst_debugging) console.log ('DST NOTICE CLICK', slug, action);

    notice_div.hide ();

    $.ajax (ajaxurl, {
      type: 'POST',
      data: {
        action:    'dst_ajax_' + slug,
        dst_check: nonce,
        slug:      slug,
        click:     action,
      }
    }).done (function (data) {

        if (dst_debugging) console.log ('DST NOTICE CLICK DONE');

        if (action == 'yes-') {

          if (dst_debugging) console.log ('DST NOTICE NEXT');

          $('.dst-notice-hidden[data-slug=' + slug + ']').fadeIn ("fast", function() {
            $(this).css ('display', 'table').removeClass ('dst-notice-hidden').addClass ('dst-notice');
          });
        }
    });

  });

  function show_dst_notice (slug) {
    $('.dst-notice[data-slug=' + slug + ']').fadeIn ("fast", function() {
      $(this).css ('display', 'table');
    });
  }

  if (typeof ajaxurl !== 'undefined') {
    var notice_div = $('.dst-notice');
    var nonce = notice_div.attr ('data-value');

    if (typeof nonce !== 'undefined') {
      nonce = atob (nonce);
    }
    var slug  = '<?php echo $this->slug; ?>';

    if (dst_debugging) console.log ('DST NOTICE ajaxurl', ajaxurl);
    if (dst_debugging) console.log ('DST NOTICE ajax check', slug, nonce);

    if (typeof nonce !== 'undefined') {
      $.ajax (ajaxurl, {
        type: 'POST',
        data: {
          action:         'dst_ajax_' + slug,
          slug:           slug,
          dst_check:      nonce,
          'notice-check': nonce
        }
      }).done (function (data) {
          if (dst_debugging) console.log ('DST NOTICE CHECK RESPONSE for', slug + ':', data == nonce ? 'ok' : 'wrong data: ' + data);

          if (data == nonce) {
            setTimeout (show_dst_notice, 500);
            setTimeout (function() {show_dst_notice (slug);}, 500);
          }
      });
    }
  }
});
</script>
<?php
    }

    public function optin_notice () {

      if (!current_user_can ('manage_options')) return;

      if (!$this->notice_needed ()) return;

      if (!$this->get_is_notification_time ()) return;

      if (defined ('DST_DEBUG') && DST_DEBUG) {
        $is_local = false;
      } elseif (!$this->track_local) {
        $is_local =
          stristr (network_site_url ( '/' ), '.dev' ) !== false ||
          stristr (network_site_url ('/'), 'localhost' ) !== false ||
          stristr (network_site_url ('/'), ':8888' ) !== false;
      } else $is_local = false;

      $is_local = apply_filters (self::DST_FILTER_IS_LOCAL . $this->slug, $is_local);

      if ($is_local) return;

      if (!$this->tracking_known) {
        if ($this->use_email == self::DST_USE_EMAIL_OPTIN_WITH_TRACKING) {
          if ($this->product_type == self::DST_PRODUCT_TYPE_PLUGIN) {
            $notice_text = '[STRONG][NAME][/STRONG][P]' .
              __("Thank you for installing our plugin. We'd like your permission to track its usage on your site and subscribe you to our newsletter. This is completely optional.", 'dst'). '[BR]' .
              __("We won't record any sensitive data, only information regarding the WordPress environment and plugin settings, which will help us to make improvements to the plugin.", 'dst');
          } else {
              $notice_text = '[STRONG][NAME][/STRONG][P]' .
                __("Thank you for installing our theme. We'd like your permission to track its usage on your site and subscribe you to our newsletter. This is completely optional.", 'dst') . '[BR]' .
                __("We won't record any sensitive data, only information regarding the WordPress environment and theme settings, which will help us to make improvements to the theme.", 'dst');
            }

          $notice_text = apply_filters (self::DST_FILTER_OPTIN_NOTICE_TEXT . esc_attr ($this->slug), $notice_text);
          $this->notice_html ($notice_text, 'yes-yes', 'no-no');
        } else {
          if ($this->product_type == self::DST_PRODUCT_TYPE_PLUGIN) {
            $notice_text = '[STRONG][NAME][/STRONG][P]' .
              __("Thank you for installing our plugin. We would like to track its usage on your site. This is completely optional.", 'dst') . '[BR]' .
              __("We don't record any sensitive data, only information regarding the WordPress environment and plugin settings, which will help us to make improvements to the plugin.", 'dst');
          } else {
              $notice_text = '[STRONG][NAME][/STRONG][P]' .
                __("Thank you for installing our theme. We would like to track its usage on your site. This is completely optional.", 'dst') . '[BR]' .
                __("We don't record any sensitive data, only information regarding the WordPress environment and theme settings, which will help us to make improvements to the theme.", 'dst');
            }

          $notice_text = apply_filters (self::DST_FILTER_OPTIN_NOTICE_TEXT . esc_attr ($this->slug), $notice_text);
          $this->notice_html ($notice_text, 'yes-', 'no-no');
        }
      }

      if ($this->use_email == self::DST_USE_EMAIL_OPTIN_AFTER_TRACKING && !$this->use_email_known) {
        if ($this->product_type == self::DST_PRODUCT_TYPE_PLUGIN) {
          $notice_text = __('Thank you for opting in to tracking. Would you like to receive occasional news about this plugin, including details of new features and special offers?', 'dst');
        } else {
            $notice_text = __('Thank you for opting in to tracking. Would you like to receive occasional news about this theme, including details of new features and special offers?', 'dst');
          }

        $notice_text = apply_filters (self::DST_FILTER_EMAIL_NOTICE_TEXT . esc_attr ($this->slug), $notice_text);
        $this->notice_html ($notice_text, '-yes', '-no', $this->tracking_known ? 'dst-notice' : 'dst-notice-hidden');
      }
    }

    public function filter_action_links ($links) {
      if (!$this->get_tracking ()) return $links;

      if (isset ($links ['deactivate']) && $this->deactivation_form && !$this->get_hide_deactivation_form ()) {
        $deactivation_link = $links ['deactivate'];
        $deactivation_link = str_replace ( '<a ', '<span id="dst-original-link-' . esc_attr ($this->slug) . '">'.$deactivation_link.'</span> <span class="dst-deactivation-form-wrapper"><span class="dst-deactivation-form" id="dst-deactivation-form-' . esc_attr ($this->slug) . '"></span></span><a onclick="javascript:event.preventDefault();" id="dst-deactivation-link-' . esc_attr ($this->slug) . '" style="display: none;" ', $deactivation_link);
        $links ['deactivate'] = $deactivation_link;
      }
      return $links;
    }

    public function default_deactivation_form_text () {
      $form = array();

      $form ['heading'] = __( 'Sorry to see you go', 'dst');
      $form ['body'] = __( 'Before you deactivate the plugin, would you quickly give us your reason for doing so?', 'dst');
      $form ['options'] = array (
        __('Set up is too difficult',     'dst')  . '#Set up is too difficult',
        __('Lack of documentation',       'dst')  . '#Lack of documentation',
        __('Not the features I wanted',   'dst')  . '#Not the features I wanted',
        __("Doesn't work",                'dst')  . "#Doesn't work",
        __('Found a better plugin',       'dst')  . '#Found a better plugin',
        __('Installed by mistake',        'dst')  . '#Installed by mistake',
        __('Just testing',                'dst')  . '#Just testing#2',
        __('Only required temporarily',   'dst')  . '#Only required temporarily#2',
        __("Don't show this form again",  'dst')  . "#Don't show this form again#1",
      );
      $form ['details'] = __('Details (optional)', 'dst');
      $form ['info'] = __('This information will greatly help us to improve the plugin.', 'dst');
      $form ['leaving'] = __( 'Goodbye!', 'dst');

      return $form;
    }

    public function filtered_deactivation_form_text () {
      $form = $this->default_deactivation_form_text ();
      return apply_filters (self::DST_FILTER_FORM_TEXT . esc_attr ($this->slug), $form);
    }

    public function generate_deactivation_form () {

      if ($this->product_type == self::DST_PRODUCT_TYPE_THEME) return;

      if (!$this->deactivation_form || $this->get_hide_deactivation_form ()) return;

      $form = $this->filtered_deactivation_form_text ();
      if (!isset ($form ['heading']) || !isset( $form ['body']) || !isset ($form ['options']) || !is_array ($form ['options']) || !isset ($form ['info']) || !isset ($form ['details'])) {
        $form = $this->default_deactivation_form_text ();
      }

      $html = '<div class="dst-deactivation-form-head"><strong>' . esc_html ($form ['heading']) . '</strong></div>';
      $html .= '<div class="dst-deactivation-form-body"><p>' . (str_replace ("'", "\'", $form['body'])) . '</p>';
      if (is_array ($form ['options'])) {
        $html .= '<div class="dst-deactivation-options"><p>';
        foreach ($form ['options'] as $option) {
          $option_data = explode ('#', $option);
          $attributes = '';
          if (isset ($option_data [2])) {
            $attributes = 'data-option="' . $option_data [2] .'"';
            if ($option_data [2] == 1) {
              $attributes .= ' style="visibility: hidden;"';
            }
          }
          $translated_option = $option_data [0];
          $option = $option_data [1];
          $id = strtolower (str_replace (array (" ", "&#039;"), "", esc_attr ($option)));
          $html .= '<input type="checkbox" name="dst-deactivation-options[]" id="' . $id . '" value="' . esc_attr ($option) . '"' . $attributes . '> <label for="' . $id . '"' . $attributes . '>' . esc_attr ($translated_option) . '</label><br>';
        }
        $html .= '</p><label for="dst-deactivation-reasons">' . esc_html ($form ['details']) .'</label><textarea name="dst-deactivation-reasons" id="dst-deactivation-reasons" rows="3" style="width:100%"></textarea><p>' . esc_html ($form['info']) . '</p>';
        $html .= '</div>';
      }
      $html .= '</div>';
      $html .= '<p class="deactivating-spinner"><span class="spinner"></span> ' . __( 'Submitting form', 'dst') . '</p>';
      ?>
<div class="dst-deactivation-form-bg"></div>
<style type="text/css">
  .dst-form-active .dst-deactivation-form-bg {
    background: rgba( 0, 0, 0, .7);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  .dst-deactivation-form-wrapper {
    position: relative;
    z-index: 999;
    display: none;
  }
  .dst-form-active .dst-deactivation-form-wrapper {
    display: inline-block;
  }
  .dst-deactivation-form {
    display: none;
  }
  .dst-form-active .dst-deactivation-form {
    position: fixed;
    top: 10%;
    left: 18%;
    max-width: 400px;
    background: #fff;
    white-space: normal;
    border-radius: 6px;
  }
  .dst-deactivation-form-head {
    background: #716eef;
    color: #fff;
    padding: 8px 18px;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
  }
  .dst-deactivation-form-body {
    padding: 8px 18px;
    color: #444;
    text-align: justify;
  }
  div.dst-deactivation-form-body strong {
    display: inline;
  }
  .dst-deactivation-options {
    margin-top: 20px;
  }
  .deactivating-spinner {
    width: 200px;
    height: 30px;
    display: none;
  }
  .deactivating-spinner .spinner {
    float: none;
    margin: 4px 4px 0 18px;
    vertical-align: bottom;
    visibility: visible;
  }
  .dst-deactivation-form-footer {
    padding: 8px 18px;
  }
  .dst-deactivation-form-footer .button.secondary {
    float: left;
    background: #fff;
    color: #000;
    margin: 0 10px 0 0;
  }
  .dst-deactivation-form-footer .button.primary {
    float: right;
    background: #716eef;
    color: #fff;
  }
  @media (max-width: 768px) {
    .dst-deactivation-form-footer .button.primary {
      padding: 6px 6px;
    }
    .dst-deactivation-form-footer .button.secondary {
      padding: 6px 5px;
    }
    .row-actions span .dst-deactivation-form {
      padding-top: 0;
    }
    .dst-deactivation-form-body {
      text-align: left;
    }
  }
  @media (max-width: 500px) {
    .dst-form-active .dst-deactivation-form {
      width: 100%;
      top: 50%;
      left: 50%;
      transform: translate(-50%,-50%);
    }
  }
</style>
<script>
  jQuery(document).ready (function ($){
    var dst_debugging = <?php echo defined ('DST_DEBUG_JS') && DST_DEBUG_JS ? 'true' : 'false'; ?>;
    var hide_form_visible = 0;

    setTimeout (function() {
      var data = {
        'action': 'dst_ajax_<?php echo $this->slug; ?>',
        'dst_check': atob ("<?php echo base64_encode (wp_create_nonce ('dst_data')); ?>"),
        'slug': '<?php echo $this->slug; ?>',
        'test': '<?php echo $this->slug; ?>',
        'dataType': "json"
      }

      if (dst_debugging) console.log ('DST AJAX TEST <?php echo $this->slug; ?> SUBMIT', data);

      $.post(
        ajaxurl,
        data,
        function (response) {
          if (dst_debugging) console.log ('DST AJAX TEST <?php echo $this->slug; ?> RESPONSE:', response);
          if (response == '<?php echo $this->slug; ?>') {
            if (dst_debugging) console.log ('DST AJAX TEST <?php echo $this->slug; ?> OK');

            $("#dst-original-link-<?php echo esc_attr ($this->slug); ?>").hide ();
            $("#dst-deactivation-link-<?php echo esc_attr ($this->slug); ?>").show ();

            if (dst_debugging) console.log ('DST DEACTIVATION FORM LINK DISPLAYED', '<?php echo esc_attr ($this->slug); ?>');
          }
        }
      );
    }, 10);


    $("#dst-deactivation-link-<?php echo esc_attr ($this->slug); ?>").on ("click",function() {
      var url = $("#dst-deactivation-link-<?php echo esc_attr ($this->slug); ?>").attr ('href');

      if (dst_debugging) console.log ('DST DEACTIVATION url', url);

      $('body').toggleClass ('dst-form-active');
      $("#dst-deactivation-form-<?php echo esc_attr ($this->slug); ?>").fadeIn();
      $("#dst-deactivation-form-<?php echo esc_attr ($this->slug); ?>").html ('<?php echo $html; ?><div class="dst-deactivation-form-footer"><p><a id="dst-cancel" class="button secondary" href="'+url+'"><?php _ex('Cancel', 'Button', 'dst'); ?></a>&nbsp;<a id="dst-just-deactivate" class="button secondary" href="'+url+'"><?php _ex('Just Deactivate', 'Button', 'dst'); ?></a>&nbsp;<a id="dst-submit-form" class="button primary" href="#"><?php _ex( 'Submit and Deactivate', 'Button', 'dst' ); ?></a></p></div>');
      $('#dst-submit-form').on ('click', function (e){
        $("#dst-deactivation-form-<?php echo esc_attr ($this->slug); ?> .dst-deactivation-form-head").html('<strong><?php echo esc_html ($form ['leaving']); ?></strong>');
        $("#dst-deactivation-form-<?php echo esc_attr ($this->slug); ?> .dst-deactivation-form-body").hide();
        $("#dst-deactivation-form-<?php echo esc_attr ($this->slug); ?> .dst-deactivation-form-footer").hide();
        $("#dst-deactivation-form-<?php echo esc_attr ($this->slug); ?> .deactivating-spinner").show();
        e.preventDefault();

        var values = new Array();
        var hide_form = false;
        $.each ($("input[name='dst-deactivation-options[]']:checked"), function (x){
          var data = $(this).data ('option');
          if (typeof data != 'undefined') {
            switch (data) {
              case 1:
                hide_form = true;
                break;
            }
          }
          values.push ($(this).val ());
        });

        var details = $('#dst-deactivation-reasons').val();
        var data = {
          'action': 'dst_ajax_<?php echo $this->slug; ?>',
          'slug':  '<?php echo $this->slug; ?>',
          'values': values,
          'details': details,
          'dst_check': atob ("<?php echo base64_encode (wp_create_nonce ('dst_data')); ?>"),
          'dataType': "json"
        }

        if (hide_form) {
          data ['hide_form'] = 1;
        }

        setTimeout (function() {
          if (dst_debugging) console.log ('DST DEACTIVATION SUBMIT TIMEOUT');

          window.location.href = url;
        }, 3000);

        if (dst_debugging) console.log ('DST DEACTIVATION SUBMIT', data);

        $.post(
          ajaxurl,
          data,
//          function (response) {
//          if (dst_debugging) console.log ("DST DEACTIVATION SUBMIT SUCCESS - REDIRECTION", response);
//            window.location.href = url;
//          }
        ).done (function (data) {
          if (dst_debugging) console.log ('DST DEACTIVATION SUBMIT DONE', data);
        }).fail (function (xhr, status, error) {
          if (dst_debugging) console.log ("DST DEACTIVATION SUBMIT ERROR:", xhr.status, xhr.statusText);
        }).always (function() {
          if (dst_debugging) console.log ("DST DEACTIVATION SUBMIT REDIRECTION");

          window.location.href = url;
        });

      });
      $('#dst-just-deactivate').on ('click', function (e){
        e.preventDefault();
        $('.dst-deactivation-form-wrapper').hide ();
        $('.dst-deactivation-form-bg').hide ();
        window.location.href = $('#dst-just-deactivate').attr ('href');
      });

      $('#dst-cancel').on ('click', function (e){
        e.preventDefault();
        $('.dst-deactivation-form-bg').click ();
      });

      $('.dst-deactivation-form-bg').on ('click', function (){
        $("#dst-deactivation-form-<?php echo esc_attr ($this->slug); ?>").fadeOut();
        $('body').removeClass ('dst-form-active');
      });

      $("input[data-option='2']").on ('click', function (){
        if ($(this).is (":checked"))
          hide_form_visible ++; else
            hide_form_visible --;

        if (hide_form_visible) {
          $("[data-option=1]").css ('visibility', '');
        } else {
            $("[data-option=1]").css ('visibility', 'hidden');
          }
      });

      window.onkeydown = function( event ) {
        if (event.keyCode === 27 ) {
          $('.dst-deactivation-form-bg').click ();
        }
      }
    });
  });
</script>
    <?php }

  public function process_ajax () {
    check_admin_referer ("dst_data", "dst_check");

    if (isset ($_POST ["slug"]) && isset ($_POST ["click"])) {
      $action = sanitize_text_field ($_POST ["click"]);
      if ($action == 'yes-') {
        $this->set_tracking (true);
        $this->action = self::DST_ACTION_OPTIN_TRACKING;
        $this->update (true);
      } elseif ($action == 'no-no') {
        $this->set_tracking (false);
        $this->set_use_email (false);
      } elseif ($action == 'yes-yes') {
        $this->set_tracking (true);
        $this->set_use_email (true);
        $this->action = self::DST_ACTION_OPTIN_TRACKING_NEWSLETTER;
        $this->update (true);
      } elseif ($action == '-yes') {
        $this->set_use_email (true);
        $this->action = self::DST_ACTION_OPTIN_NEWSLETTER;
        $this->update (true);
      } elseif ($action == '-no') {
        $this->set_use_email (false);
        $this->action = self::DST_ACTION_OPTIN_NO_NEWSLETTER;
        $this->update (true);
      }
    }

    elseif (isset ($_POST ["slug"]) && isset ($_POST ["notice-check"])) {
      echo $_POST ["notice-check"];
    }

    elseif (isset ($_POST ["slug"]) && isset ($_POST ["values"]) && isset ($_POST ['details'])) {
      $values = json_encode (wp_unslash ($_POST ['values']));
      $this->set_deactivation_reason ($values);

      $details = sanitize_text_field (wp_unslash ($_POST ['details']));
      $this->set_deactivation_details ($details);

      if (isset ($_POST ["hide_form"])) {
        $this->set_hide_deactivation_form ($_POST ["hide_form"]);
      }
    }

    elseif (isset ($_POST ["slug"]) && isset ($_POST ["test"])) {
      echo sanitize_text_field ($_POST ["test"]);
    }

    wp_die();
  }

  function get_client_ip_address (){
    $server_addr = isset ($_SERVER ['SERVER_ADDR']) ? $_SERVER ['SERVER_ADDR'] : '';
    foreach (array (
        'HTTP_CF_CONNECTING_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR',
      ) as $key) {
      if (array_key_exists ($key, $_SERVER) === true) {
        foreach (explode (',', $_SERVER[$key]) as $ip) {
          $ip = str_replace ("for=", "", $ip);
          $ip = trim ($ip); // just to be safe
          if ($server_addr != '' && $ip == $server_addr) continue 2; // HTTP_X_FORWARDED_FOR may report server IP address
          return $ip;
        }
      }
    }
   return '';
  }
}

}
