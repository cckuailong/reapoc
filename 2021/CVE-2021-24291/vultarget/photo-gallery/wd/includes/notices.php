<?php
if ( !defined('ABSPATH') ) {
  exit;
}

class TenWebNewLibNotices {
  protected $promo_link = '';
  public $config;
  public $notice_spam = 0;
  public $notice_spam_max = 2;

  // Basic actions to run
  public function __construct( $config = array() ) {
    $this->config = $config;
    // Runs the admin notice ignore function incase a dismiss button has been clicked
    add_action('admin_init', array( $this, 'admin_notice_ignore' ));
    // Runs the admin notice temp ignore function incase a temp dismiss link has been clicked
    add_action('admin_init', array( $this, 'admin_notice_temp_ignore' ));
    add_action('admin_notices', array( $this, 'wd_admin_notices' ));
  }

  // Checks to ensure notices aren't disabled and the user has the correct permissions.
  public function wd_admin_notice() {
    $wd_options = $this->config;
    $settings = get_option($wd_options->prefix . '_admin_notice');
    if ( !isset($settings['disable_admin_notices']) || (isset($settings['disable_admin_notices']) && $settings['disable_admin_notices'] == 0) ) {
      if ( current_user_can('manage_options') ) {
        return TRUE;
      }
    }

    return FALSE;
  }

  // Primary notice function that can be called from an outside function sending necessary variables
  public function admin_notice( $admin_notices ) {
    $wd_options = $this->config;
    // Check options
    if ( !$this->wd_admin_notice() ) {
      return FALSE;
    }
    foreach ( $admin_notices as $slug => $admin_notice ) {
      // Call for spam protection
      if ( $this->anti_notice_spam() ) {
        return FALSE;
      }
      // Check for proper page to display on
      if ( isset($admin_notices[$slug]['pages']) && is_array($admin_notices[$slug]['pages']) ) {
        if ( !$this->admin_notice_pages($admin_notices[$slug]['pages']) ) {
          return FALSE;
        }
      }
      // Check for required fields
      if ( !$this->required_fields($admin_notices[$slug]) ) {

        // Get the current date then set start date to either passed value or current date value and add interval
        $current_date = current_time("n/j/Y");
        $start = (isset($admin_notices[$slug]['start']) ? $admin_notices[$slug]['start'] : $current_date);
        $start = date("n/j/Y", strtotime($start));
        $date_array = explode('/', $start);
        $interval = (isset($admin_notices[$slug]['int']) ? $admin_notices[$slug]['int'] : 0);
        $date_array[1] += $interval;
        $start = date("n/j/Y", mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]));
        // This is the main notices storage option
        $admin_notices_option = get_option($wd_options->prefix . '_admin_notice', array());
        // Check if the message is already stored and if so just grab the key otherwise store the message and its associated date information
        if ( !array_key_exists($slug, $admin_notices_option) ) {
          $admin_notices_option[$slug]['start'] = $start;
          $admin_notices_option[$slug]['int'] = $interval;
          update_option($wd_options->prefix . '_admin_notice', $admin_notices_option);
        }
        // Sanity check to ensure we have accurate information
        // New date information will not overwrite old date information
        $admin_display_check = (isset($admin_notices_option[$slug]['dismissed']) ? $admin_notices_option[$slug]['dismissed'] : 0);
        $admin_display_start = (isset($admin_notices_option[$slug]['start']) ? $admin_notices_option[$slug]['start'] : $start);
        $admin_display_interval = (isset($admin_notices_option[$slug]['int']) ? $admin_notices_option[$slug]['int'] : $interval);
        $admin_display_msg = (isset($admin_notices[$slug]['msg']) ? $admin_notices[$slug]['msg'] : '');
        $admin_display_title = (isset($admin_notices[$slug]['title']) ? $admin_notices[$slug]['title'] : '');
        $admin_display_link = (isset($admin_notices[$slug]['link']) ? $admin_notices[$slug]['link'] : '');
        $output_css = FALSE;
        // Ensure the notice hasn't been hidden and that the current date is after the start date
        if ( $admin_display_check == 0 && strtotime($admin_display_start) <= strtotime($current_date) ) {

          // Get remaining query string
          $query_str = (isset($admin_notices[$slug]['later_link']) ? $admin_notices[$slug]['later_link'] : esc_url(add_query_arg($wd_options->prefix . '_admin_notice_ignore', $slug)));
          if ( strpos($slug, 'promo') === FALSE ) {
            // Admin notice display output
            echo '<div class="update-nag wd-admin-notice">
                                <div class="wd-notice-logo" style="background-image: url(' . $wd_options->wd_url_img . '/' . $wd_options->prefix . '_main_plugin.png);"></div>
                                <p class="wd-notice-title">' . $admin_display_title . '</p>
                                <p class="wd-notice-body">' . $admin_display_msg . '</p>
                                <ul class="wd-notice-body wd-blue">' . $admin_display_link . '</ul>
                                <a href="' . $query_str . '" class="dashicons dashicons-dismiss"></a>
                              </div>';
          }
          else {
            echo '<div class="admin-notice-promo">';
            echo $admin_display_msg;
            echo '<ul class="notice-body-promo blue">
                                    ' . $admin_display_link . '
                                  </ul>';
            echo '<a href="' . $query_str . '" class="dashicons dashicons-dismiss close-promo"></a>';
            echo '</div>';
          }
          $this->notice_spam += 1;
          $output_css = TRUE;
        }
        if ( $output_css ) {
          wp_enqueue_style($wd_options->prefix . '-admin-notices', $wd_options->wd_url_css . '/notices.css', array(), get_option($wd_options->prefix . "_version"));
        }
      }
    }
  }

  // Spam protection check
  public function anti_notice_spam() {
    if ( $this->notice_spam >= $this->notice_spam_max ) {
      return TRUE;
    }

    return FALSE;
  }

  // Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked
  public function admin_notice_ignore() {
    $wd_options = $this->config;
    // If user clicks to ignore the notice, update the option to not show it again
    if ( isset($_GET[$wd_options->prefix . '_admin_notice_ignore']) ) {
      $admin_notices_option = get_option($wd_options->prefix . '_admin_notice', array());
      $admin_notices_option[sanitize_text_field($_GET[$wd_options->prefix . '_admin_notice_ignore'])]['dismissed'] = 1;
      update_option($wd_options->prefix . '_admin_notice', $admin_notices_option);
      $query_str = remove_query_arg($wd_options->prefix . '_admin_notice_ignore');
      wp_redirect($query_str);
      exit;
    }
  }

  // Temp Ignore function that gets ran at admin init to ensure any messages that were temp dismissed get their start date changed
  public function admin_notice_temp_ignore() {
    $wd_options = $this->config;
    // If user clicks to temp ignore the notice, update the option to change the start date - default interval of 14 days
    if ( isset($_GET[$wd_options->prefix . '_admin_notice_temp_ignore']) ) {
      $admin_notices_option = get_option($wd_options->prefix . '_admin_notice', array());
      $current_date = current_time("n/j/Y");
      $date_array = explode('/', $current_date);
      $interval = (isset($_GET['wd_int']) ? intval($_GET['wd_int']) : 14);
      $date_array[1] += $interval;
      $new_start = date("n/j/Y", mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]));
      $admin_notices_option[sanitize_text_field($_GET[$wd_options->prefix . '_admin_notice_temp_ignore'])]['start'] = $new_start;
      $admin_notices_option[sanitize_text_field($_GET[$wd_options->prefix . '_admin_notice_temp_ignore'])]['dismissed'] = 0;
      update_option($wd_options->prefix . '_admin_notice', $admin_notices_option);
      $query_str = remove_query_arg(array( $wd_options->prefix . '_admin_notice_temp_ignore', 'wd_int' ));
      wp_redirect($query_str);
      exit;
    }
  }

  public function admin_notice_pages( $pages ) {
    $wd_options = $this->config;
    foreach ( $pages as $key => $page ) {
      if ( is_array($page) ) {
        if ( isset($_GET['page']) && $_GET['page'] == $page[0] && isset($_GET['tab']) && $_GET['tab'] == $page[1] ) {
          return TRUE;
        }
      }
      else {
        if ( $page == 'all' ) {
          return TRUE;
        }
        if ( get_current_screen()->id === $page ) {
          return TRUE;
        }
        if ( isset($_GET['page']) && $_GET['page'] == $page ) {
          return TRUE;
        }
      }

      return FALSE;
    }
  }

  // Required fields check
  public function required_fields( $fields ) {
    if ( !isset($fields['msg']) || (isset($fields['msg']) && empty($fields['msg'])) ) {
      return TRUE;
    }
    if ( !isset($fields['title']) || (isset($fields['title']) && empty($fields['title'])) ) {
      return TRUE;
    }

    return FALSE;
  }

  // Special parameters function that is to be used in any extension of this class
  public function special_parameters( $admin_notices ) {
    // Intentionally left blank
  }

  public function wd_admin_notices() {
    $wd_options = $this->config;
    $two_week_review_ignore = add_query_arg(array( $wd_options->prefix . '_admin_notice_ignore' => 'two_week_review' ));
    $two_week_review_temp = add_query_arg(array(
                                            $wd_options->prefix . '_admin_notice_temp_ignore' => 'two_week_review',
                                            'int' => 14,
                                          ));
    $promo_close = add_query_arg(array( $wd_options->prefix . '_admin_notice_ignore' => 'ecommerce_promo' ));
    $notices['two_week_review'] = array(
      'title' => __('Leave A Review?', $wd_options->prefix),
      'msg' => sprintf(__('We hope you\'ve enjoyed using WordPress %s! Would you consider leaving us a review on WordPress.org?', $wd_options->prefix), $wd_options->plugin_title),
      'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://wordpress.org/support/plugin/' . $wd_options->plugin_wordpress_slug . '/reviews?filter=5&rate=5#new-post" target="_blank">' . __('Sure! I\'d love to!', $wd_options->prefix) . '</a></li>
                         <li><span class="dashicons dashicons-smiley"></span><a href="' . $two_week_review_ignore . '"> ' . __('I\'ve already left a review', $wd_options->prefix) . '</a></li>
                         <li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $two_week_review_temp . '">' . __('Maybe Later', $wd_options->prefix) . '</a></li>
                         <li><span class="dashicons dashicons-dismiss"></span><a href="' . $two_week_review_ignore . '">' . __('Never show again', $wd_options->prefix) . '</a></li>',
      'later_link' => $two_week_review_temp,
      'int' => 14,
    );
    $this->admin_notice($notices);
  }
}
