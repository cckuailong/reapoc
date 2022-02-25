<?php if(!defined('ABSPATH')) exit; // Exit if accessed directlys

require_once 'PerfectSurveyCore.php';

class PerfectSurvey extends PerfectSurveyCore
{
  /**
  * Settings
  *
  * @var array
  */
  public $settings = array();

  /**
  * Assets class
  *
  * @var PerfectSurveyAssets
  */
  public $assets;

  /**
  * DB Class
  *
  * @var PerfectSurveyDB
  */
  public $db;

  /**
  * Resources Class
  *
  * @var PerfectSurveyResources
  */
  public $resources;

  /**
  * Post Type
  *
  * @var PerfectSurveyPostType
  */
  public $post_type;

  /**
  * Post Type Options
  *
  * @var PerfectSurveyPostTypeMeta
  */
  public $post_type_meta;

  /**
  * Global Options
  *
  * @var PerfectSurveyGlobalSetting
  */
  public $ps_meta;

  /**
  * Post Type Model
  *
  * @var PerfectSurveyPostTypeModel
  */
  public $post_type_model;

  /**
  * Post Type Actions
  *
  * @var PerfectSurveyPostTypeAction
  */
  public $post_type_action;

  /**
  * Booting plugin
  *
  * @return $this
  */
  public function boot()
  {
    add_action('init', array($this, 'wp_init'), 1);

    return $this;
  }


  /**
  * Call when plugin is running
  *
  * @return void
  */
  public function run()
  {
    /**
    * @TODO make something here..
    */
  }


  public function wp_init()
  {
    add_action('init', function(){ ob_get_level() <= 1 ? ob_start() : null; });

    $this->load_helpers();

    $this->load_post_type();

    $this->load_global_setting();

    $this->load_textdomain();

    $this->load_db();

    $this->load_resources();

    $this->load_settings();

    $this->load_assets();

    $this->check_installation();
  }


  /**
  * Uninstall plugin
  *
  * @return boolean
  */
  public function uninstall()
  {
    $this->load_db();
    return $this->db->execute_sql_file('uninstall');
  }

  /**
  * check if plugin is installed or check updates
  *
  * @return boolean
  */
  public function check_installation()
  {
    if(!$this->settings['installed'])
    {
      return $this->install();
    }

    $this->update();

    return true;
  }

  /**
  * Install plugin
  *
  * @return bool
  */
  private function install()
  {
    $this->load_db();
    return $this->db->execute_sql_file('install');
  }

  /**
  * Check for update
  *
  * @return bool
  */
  private function update()
  {
    if(version_compare($this->settings['db']['version'],PRSV_PLUGIN_VERSION) == -1) //versione attuale sul db è più vecchia di quella applicativa
    {
      return $this->db->execute_sql_file('update');
    }

    return false;
  }

  /**
  * Load assets
  *
  * @return PerfectSurvey
  */
  private function load_assets()
  {
    $this->assets  = new PerfectSurveyAssets();
    $this->assets->wp_init();

    return $this;
  }


  /**
  * Load text domains text
  *
  * @return $this
  */
  private function load_textdomain()
  {

    load_textdomain('perfect-survey' , PRSV_BASE_PATH . '/lang/ps-'.get_locale().'.mo');

    return $this;
  }

  /**
  * load db manager of plugin
  *
  * @return $this
  */
  private function load_db()
  {
    $this->db = new PerfectSurveyDB();
    $this->db->wp_init();

    return $this;
  }

  /**
  * load db manager of plugin
  *
  * @return $this
  */
  private function load_resources()
  {
    $this->resources = new PerfectSurveyResources();
    $this->resources->wp_init();

    return $this;
  }

  /**
  * Load post type of survey
  *
  * @return $this
  */
  private function load_post_type()
  {
    $this->post_type = new PerfectSurveyPostType();
    $this->post_type->wp_init();

    $this->post_type_meta = new PerfectSurveyPostTypeMeta();
    $this->post_type_meta->wp_init();

    $this->post_type_model = new PerfectSurveyPostTypeModel();
    $this->post_type_model->wp_init();

    $this->post_type_action  = new PerfectSurveyPostTypeAction();
    $this->post_type_action->wp_init();

    return $this;
  }

  /**
  * Load global settings
  *
  * @return $this
  */
  private function load_global_setting()
  {

    $this->ps_meta = new PerfectSurveyGlobalSetting();
    $this->ps_meta->wp_init();

    return $this;
  }


  /**
  * Include ps helpers files
  *
  * @return $this
  */
  private function load_helpers()
  {
    require_once PRSV_BASE_PATH_HELPERS . '/debug.php';

    require_once PRSV_BASE_PATH_HELPERS . '/input.php';

    require_once PRSV_BASE_PATH_HELPERS . '/common.php';

    require_once PRSV_BASE_PATH_HELPERS . '/post_type_meta.php';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    return $this;
  }

  /**
  * Load plugin settings
  *
  * @return array
  */
  private function load_settings()
  {
    $this->settings = array(
      'name'           => __('Perfect Survey', 'perfect-survey'),
      'file'           => __(__FILE__),
      'version'        => PRSV_PLUGIN_VERSION,
      'basename'       => plugin_basename(__FILE__),
      'dir'            => plugin_dir_url('perfect-survey.php'),
      'path'           => plugin_dir_url('perfect-survey.php'),
      'valid_purchase' => TRUE,
      'db'             => NULL,
      'installed'      => FALSE
    );

    $this->settings['db'] = $this->db->get_plugin_info();

    $this->settings['installed'] = !empty($this->settings['db']);

    return $this->settings;
  }

}
