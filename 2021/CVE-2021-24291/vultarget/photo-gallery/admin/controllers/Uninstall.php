<?php

/**
 * Class UninstallController_bwg
 */
class UninstallController_bwg {
  /**
   * @var $model
   */
  private $model;
  /**
   * @var $view
   */
  private $view;
  /**
   * @var string $page
   */
  private $page;
 
  public function __construct() {
    if ( !BWG()->is_pro ) {
      global $bwg_options;
      if ( !class_exists("TenWebNewLibConfig") ) {
        $plugin_dir = apply_filters('tenweb_new_free_users_lib_path', array('version' => '1.1.3', 'path' => BWG()->plugin_dir));
        include_once($plugin_dir['path'] . "/wd/config.php");
      }
      $config = new TenWebNewLibConfig();
      $config->set_options($bwg_options);
      $deactivate_reasons = new TenWebNewLibDeactivate($config);
      $deactivate_reasons->submit_and_deactivate();
    }

    $this->model = new UninstallModel_bwg();
    $this->view = new UninstallView_bwg();

    $this->page = WDWLibrary::get('page');
  }

  /**
   * Execute.
   */
  public function execute() {
    $task = WDWLibrary::get('task');

    if ( method_exists($this, $task) ) {
      check_admin_referer(BWG()->nonce, BWG()->nonce);
      $this->$task();
    }
    else {
      $this->display();
    }
  }

  /**
   * Display.
   */
  public function display() {
    $params = array();
    $params['page_title'] = sprintf(__('Uninstall %s', BWG()->prefix), BWG()->nicename);
    $params['tables'] = $this->get_tables();

    $this->view->display($params);
  }

  /**
   * Return DB tables names.
   *
   * @return array
   */
  private function get_tables() {
    global $wpdb;
    $tables = array(
      $wpdb->prefix . 'bwg_album',
      $wpdb->prefix . 'bwg_album_gallery',
      $wpdb->prefix . 'bwg_gallery',
      $wpdb->prefix . 'bwg_image',
      $wpdb->prefix . 'bwg_image_comment',
      $wpdb->prefix . 'bwg_image_rate',
      $wpdb->prefix . 'bwg_image_tag',
      $wpdb->prefix . 'bwg_option',
      $wpdb->prefix . 'bwg_theme',
      $wpdb->prefix . 'bwg_shortcode',
      $wpdb->prefix . 'bwg_file_paths',
    );

    return $tables;
  }

  /**
   * Uninstall.
   */
  public function uninstall() {
    $params = array();
    $params['tables'] = $this->get_tables();

    $this->model->delete_folder();
    $this->model->delete_db_tables($params);
    // Deactivate all addons.
    WDWLibrary::deactivate_all_addons(BWG()->main_file);
    $params['page_title'] = sprintf(__('Uninstall %s', BWG()->prefix), BWG()->nicename);
    $deactivate_url =
            add_query_arg(
                array(
                    'action'   => 'deactivate',
                    'plugin'   => BWG()->main_file,
                    '_wpnonce' => wp_create_nonce('deactivate-plugin_' . BWG()->main_file)
                ),
                admin_url('plugins.php')
            );
    wp_redirect($deactivate_url);
    exit();
  }
}
