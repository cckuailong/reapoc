<?php
if ( !defined('ABSPATH') ) {
  exit;
}

class TenWebNewLibOverview {
  public $config;

  public function __construct( $config = array() ) {
    $this->config = $config;
    $wd_options = $this->config;
  }

  public function display_overview_page() {
    $wd_options = $this->config;
    $start_using_url = "";
    if ( !empty($this->config->custom_post) ) {
      if ( strpos($this->config->custom_post, 'post_type', 0) !== FALSE ) {
        $start_using_url = admin_url($this->config->custom_post);
      }
      else {
        $start_using_url = menu_page_url($this->config->custom_post, FALSE);
      }
    }
    require_once($wd_options->wd_dir_templates . "/display_overview.php");
  }

  public function overview_styles() {
    $wd_options = $this->config;
    $version = get_option($wd_options->prefix . "_version");
    wp_enqueue_style($wd_options->prefix . '_overview_css', $wd_options->wd_url_css . '/overview.css', array(), $version);
  }

  public function overview_scripts() {
    $wd_options = $this->config;
    $version = get_option($wd_options->prefix . "_version");
    wp_enqueue_script($wd_options->prefix . '_overview_js', $wd_options->wd_url_js . '/overview.js', array(), $version);
  }

  private function remote_get( $plugin_wp_slug ) {
    $request = wp_remote_get(" http://api.wordpress.org/plugins/info/1.0/" . $plugin_wp_slug);
    $data = array();
    if ( !is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200 ) {
      $body = unserialize($request['body']);
      $data["downloaded"] = $body->downloaded;
      $ratings = $body->ratings;
      if ( $ratings[5] == 0 && $ratings[4] == 0 && $ratings[3] == 0 && $ratings[2] == 0 && $ratings[1] == 0 ) {
        $data["rating"] = 100;
      }
      else {
        $data["rating"] = round((($ratings[5] * 5 + $ratings[4] * 4 + $ratings[3] * 3 + $ratings[2] * 2 + $ratings[1] * 1) / $body->num_ratings), 1);
        $data["rating"] = round(($data["rating"] / 5) * 100);
      }

      return $data;
    }

    return FALSE;
  }
}
