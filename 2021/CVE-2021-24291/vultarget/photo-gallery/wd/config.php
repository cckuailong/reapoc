<?php
if ( !defined('ABSPATH') ) {
  exit;
}

class TenWebNewLibConfig {
  public static $instance;
  public $prefix = NULL;
  public $plugin_id = NULL;
  public $wd_plugin_id = NULL;
  public $plugin_title = NULL;
  public $plugin_wordpress_slug = NULL;
  public $plugin_dir = NULL;
  public $plugin_main_file = NULL;
  public $description = NULL;
  public $plugin_features = NULL;
  public $video_youtube_id = NULL;
  public $plugin_wd_url = NULL;
  public $plugin_wd_demo_link = NULL;
  public $plugin_wd_addons_link = NULL;
  public $plugin_wizard_link = NULL;
  public $after_subscribe = NULL;
  public $plugin_menu_title = NULL;
  public $plugin_menu_icon = NULL;
  public $wd_dir = NULL;
  public $wd_dir_includes = NULL;
  public $wd_dir_templates = NULL;
  public $wd_dir_assets = NULL;
  public $wd_url_css = NULL;
  public $wd_url_js = NULL;
  public $wd_url_img = NULL;
  public $deactivate = NULL;
  public $subscribe = NULL;
  public $custom_post = NULL;
  public $menu_capability = NULL;
  public $menu_position = NULL;
  public $overview_welcome_image = NULL;
  public $display_overview = TRUE;

  public function set_options( $options ) {

    if ( isset($options["prefix"]) ) {
      $this->prefix = $options["prefix"];
    }
    if ( isset($options["plugin_id"]) ) {
      $this->plugin_id = $options["plugin_id"];
    }
    if ( isset($options["wd_plugin_id"]) ) {
      $this->wd_plugin_id = $options["wd_plugin_id"];
    }
    if ( isset($options["plugin_title"]) ) {
      $this->plugin_title = $options["plugin_title"];
    }
    if ( isset($options["plugin_wordpress_slug"]) ) {
      $this->plugin_wordpress_slug = $options["plugin_wordpress_slug"];
    }
    if ( isset($options["plugin_dir"]) ) {
      $this->plugin_dir = $options["plugin_dir"];
    }
    if ( isset($options["plugin_main_file"]) ) {
      $this->plugin_main_file = $options["plugin_main_file"];
    }
    if ( isset($options["description"]) ) {
      $this->description = $options["description"];
    }
    if ( isset($options["plugin_features"]) ) {
      $this->plugin_features = $options["plugin_features"];
    }
    if ( isset($options["video_youtube_id"]) ) {
      $this->video_youtube_id = $options["video_youtube_id"];
    }
    if ( isset($options["plugin_wd_url"]) ) {
      $this->plugin_wd_url = $options["plugin_wd_url"];
    }
    if ( isset($options["plugin_wd_demo_link"]) ) {
      $this->plugin_wd_demo_link = $options["plugin_wd_demo_link"];
    }
    if ( isset($options["plugin_wd_demo_link"]) ) {
      $this->plugin_wd_demo_link = $options["plugin_wd_demo_link"];
    }
    if ( isset($options["plugin_wd_docs_link"]) ) {
      $this->plugin_wd_docs_link = $options["plugin_wd_docs_link"];
    }
    if ( isset($options["plugin_wizard_link"]) ) {
      $this->plugin_wizard_link = $options["plugin_wizard_link"];
    }
    if ( isset($options["after_subscribe"]) ) {
      $this->after_subscribe = $options["after_subscribe"];
    }
    if ( isset($options["plugin_menu_title"]) ) {
      $this->plugin_menu_title = $options["plugin_menu_title"];
    }
    if ( isset($options["plugin_menu_icon"]) ) {
      $this->plugin_menu_icon = $options["plugin_menu_icon"];
    }
    if ( isset($options["deactivate"]) ) {
      $this->deactivate = $options["deactivate"];
    }
    if ( isset($options["subscribe"]) ) {
      $this->subscribe = $options["subscribe"];
    }
    if ( isset($options["custom_post"]) ) {
      $this->custom_post = $options["custom_post"];
    }
    if ( isset($options["menu_capability"]) ) {
      $this->menu_capability = $options["menu_capability"];
    }
    if ( isset($options["menu_position"]) ) {
      $this->menu_position = $options["menu_position"];
    }
    if ( isset($options["overview_welcome_image"]) ) {
      $this->overview_welcome_image = $options["overview_welcome_image"];
    }
    if ( isset($options["display_overview"]) ) {
      $this->display_overview = $options["display_overview"];
    }
    // directories
    $this->wd_dir = dirname($this->plugin_main_file) . '/wd';
    $this->wd_dir_includes = $this->wd_dir . '/includes';
    $this->wd_dir_templates = $this->wd_dir . '/templates';
    $this->wd_dir_assets = $this->wd_dir . '/assets';
    $this->wd_url_css = plugins_url(plugin_basename($this->wd_dir)) . '/assets/css';
    $this->wd_url_js = plugins_url(plugin_basename($this->wd_dir)) . '/assets/js';
    $this->wd_url_img = plugins_url(plugin_basename($this->wd_dir)) . '/assets/img';
  }
}
