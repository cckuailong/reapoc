<?php if(!defined('ABSPATH')) exit; // Exit if accessed directlys

class PerfectSurveyResources extends PerfectSurveyCore
{

  public function wp_init()
  {
    add_action('init', array($this, 'init'));
  }

  public function init()
  {
    //action
    add_action('admin_menu', array($this, 'load_backend_menu'));
  }

  /**
  * create admin menu
  */
  public function load_backend_menu()
  {
    add_submenu_page('edit.php?post_type='.PRSV_PLUGIN_CODE, __('Statistics', 'perfect-survey'), __('Statistics', 'perfect-survey'), 'read', 'pswp_stats', array($this,'backend_page_statistics'));
    add_submenu_page('edit.php?post_type='.PRSV_PLUGIN_CODE, __('Import/Export', 'perfect-survey'), '<span class="psrv_new_feautres"><i class="pswp_set_icon-star pswp_set_icon-hilight"></i> '.__('Import/Export', 'perfect-survey').'</span>', 'manage_options', 'importexport', array($this,'backend_page_importexport'));
    add_submenu_page('edit.php?post_type='.PRSV_PLUGIN_CODE, __('About', 'perfect-survey'), __('About', 'perfect-survey'), 'read', 'welcome', array($this,'backend_page_welcome'));
    add_submenu_page(null, 'Single survey', 'Single survey', 'read', 'single_statistic', array($this,'backend_page_singlestats'));
  }

  public function backend_page_statistics()
  {
    $this->include_backend('statistics');
  }

  public function backend_page_importexport()
  {
    $this->include_backend('importexport');
  }

  public function backend_page_singlestats()
  {
    $this->include_backend('singlestats');
  }

  public function backend_page_welcome()
  {
    $this->include_backend('welcome');
  }

  /**
  * Render admin template
  *
  * @param string $page template page path
  * @param array  $data data variables
  *
  * @return string
  */
  public function render_backend($page, array $data = array())
  {
    return $this->render_resource('backend',$page, $data);
  }

  /**
  * Render frontend template
  *
  * @param string $page  template page path
  * @param array  $data  data variables
  *
  * @return string
  */
  public function render_frontend($page, array $data = array())
  {
    return $this->render_resource('frontend',$page, $data);
  }

  /**
  * Include frontend resource
  *
  * @param string $page  template page path
  * @param array  $data  vars
  *
  * @return bool
  */
  public function include_frontend($page, array $data = array())
  {
    return $this->include_resource('frontend',$page,$data);
  }

  /**
  * Include admin resource
  *
  * @param string $page  template page path
  * @param array  $data  vars
  *
  * @return bool
  */
  public function include_backend($page, array $data = array())
  {
    return $this->include_resource('backend',$page,$data);
  }


  /**
  * Render layout template
  *
  * @param string $layout    layout
  * @param string $page      template page path
  * @param array  $data      array data
  *
  * @return string
  */
  private function render_resource($layout, $page, array $data = array())
  {
    ob_start();

    $this->include_resource($layout, $page, $data);

    $output = ob_get_clean();

    return $output;
  }

  /**
  * Include resource
  *
  * @param string $layout    layout
  * @param string $page      template page path
  * @param array  $data      array data
  *
  * @return boolean
  */
  private function include_resource($layout, $page, array $data = array())
  {
    $file_path = constant('PRSV_BASE_PATH_RESOURCES_'.strtoupper($layout)). '/' . $page;

    if(!strstr($file_path,'.php'))
    {
      $file_path.='.php';
    }

    if(!file_exists($file_path))
    {
      return false;
    }

    extract($data);

    require $file_path;

    return true;
  }
}
