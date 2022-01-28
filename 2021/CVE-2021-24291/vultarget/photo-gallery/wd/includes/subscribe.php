<?php
if ( !defined('ABSPATH') ) {
  exit;
}

class TenWebNewLibSubscribe {
  public $config;

  public function __construct( $config = array() ) {
    $this->config = $config;
    add_action('admin_init', array( $this, 'after_subscribe' ));
  }

  public function subscribe_scripts() {
    $wd_options = $this->config;
    wp_register_script('subscribe_js', $wd_options->wd_url_js . '/subsribe.js');
    wp_enqueue_script('subscribe_js');
  }

  public function subscribe_styles() {
    $wd_options = $this->config;
    wp_enqueue_style($wd_options->prefix . 'subscribe', $wd_options->wd_url_css . '/subscribe.css');
  }

  public function subscribe_display_page() {
    $wd_options = $this->config;
    require_once($wd_options->plugin_dir . '/admin/views/LibSubscribe.php');
  }

  public function after_subscribe() {
    $wd_options = $this->config;
    if ( isset($_GET[$wd_options->prefix . "_sub_action"]) ) {

      if ( $_GET[$wd_options->prefix . "_sub_action"] == "allow" ) {
        $data = array();
        $data["wp_site_url"] = site_url();
        $admin_data = wp_get_current_user();
        $user_first_name = get_user_meta($admin_data->ID, "first_name", TRUE);
        $user_last_name = get_user_meta($admin_data->ID, "last_name", TRUE);
        $name = $user_first_name || $user_last_name ? $user_first_name . " " . $user_last_name : $admin_data->data->user_login;
        $data["name"] = isset($_GET[$wd_options->prefix . "_user_name"]) ? sanitize_text_field($_GET[$wd_options->prefix . "_user_name"]) : $name;
        $data["email"] = isset($_GET[$wd_options->prefix . "_user_email"]) ? sanitize_email($_GET[$wd_options->prefix . "_user_email"]) : $admin_data->data->user_email;
        $data["product_id"] = $wd_options->plugin_id;
        $response = wp_remote_post(TEN_WEB_NEW_LIB_SUBSCRIBE_URL, array(
                                                                  'method' => 'POST',
                                                                  'timeout' => 45,
                                                                  'redirection' => 5,
                                                                  'httpversion' => '1.0',
                                                                  'blocking' => TRUE,
                                                                  'headers' => array( "Accept" => "application/x.10webcore.v1+json" ),
                                                                  'body' => $data,
                                                                  'cookies' => array(),
                                                                ));
        $response_body = (!is_wp_error($response) && isset($response["body"])) ? json_decode($response["body"], TRUE) : NULL;
        if ( is_array($response_body) && $response_body["status"] == "ok" ) {
          if ( get_option($wd_options->prefix . "_subscribe_email") !== FALSE ) {
            update_option($wd_options->prefix . "_subscribe_email", sanitize_email($data["email"]));
          }
          else {
            add_option($wd_options->prefix . "_subscribe_email", sanitize_email( $data["email"]), '', 'no');
          }
        }
      }
      if ( get_option($wd_options->prefix . "_subscribe_done") != 1 ) {
        update_option($wd_options->prefix . "_subscribe_done", 1);
      }
      else {
        add_option($wd_options->prefix . "_subscribe_done", "1", '', 'no');
      }
      if ( $_GET[$wd_options->prefix . "_sub_action"] == "skip" ) {
        wp_safe_redirect($wd_options->after_subscribe);
      }
    }
  }
}
