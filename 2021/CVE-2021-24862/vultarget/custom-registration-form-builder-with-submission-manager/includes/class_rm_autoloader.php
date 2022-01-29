<?php

/**
 * Autoloading class for plugin.
 * 
 * @link http://www.registrationmagic.com
 * @since      1.0.0
 *
 * @package    Registraion_Magic
 * @subpackage Registraion_Magic/includes
 */

/**
 * Autoloading class for plugin.
 * 
 * Defines the autoloader and registers it.
 * Defines various file paths for the class.
 *
 * @author cmshelplive
 */
class RM_Autoloader
{

    public $file_prefix;
    public $file_extension;
    public $dir_path_for;

    
    public function __construct()
    {
        $this->file_prefix = 'class_';
        $this->file_extension = '.php';
        $this->dir_path_for = array();
        $this->dir_path_for['INCLUSION'] = 'includes/';
        $this->dir_path_for['ADMIN'] = 'admin/';
        $this->dir_path_for['PUBLIC'] = 'public/';
        $this->dir_path_for['ADMIN_MODELS'] = 'admin/models/';
        $this->dir_path_for['PUBLIC_MODELS'] = 'public/models/';
        $this->dir_path_for['PUBLIC_FIELD_MODELS'] = 'public/models/frontend_fields/';
        $this->dir_path_for['PUBLIC_VIEWS'] = 'public/views/';
        $this->dir_path_for['ADMIN_VIEWS'] = 'admin/views/';
        $this->dir_path_for['CONTROLLER'] = 'admin/controllers/';
        $this->dir_path_for['FRONT_CONTROLLERS'] = 'public/controllers/';
        $this->dir_path_for['FRONT_WIDGETS'] = 'public/widgets/';
        $this->dir_path_for['SERVICES'] = 'services/';
        $this->dir_path_for['FACTORY'] = 'libs/factory/';
        $this->dir_path_for['PUBLISH'] = 'libs/publish/';
        $this->dir_path_for['PUBLISH'] = 'libs/validators/';
        $this->dir_path_for['REPO'] = 'repo/';
    }

    /**
     * Autoloader
     * 
     * This function perofrms autoloading
     * 
     * @access  public
     * @param   $class_name     string      Name of the class being autoloaded 
     * 
     * @return  void
     */
    public function autoload($class_name)
    {
        $file_name = $this->get_file_name($class_name);
        $plugin_path = plugin_dir_path(dirname(__FILE__));

        foreach ($this->dir_path_for as $DIR_PATH)
        {
            $file = $plugin_path . $DIR_PATH . $file_name;
            if (file_exists($file))
            {
                include_once $file;
                break;
            }
        }
    }

    /**
     * This function registers the autoloader.
     * 
     * @access public
     * @return void
     */
    public function register()
    {
        spl_autoload_register(array($this, 'autoload'));
    }

    /*
     * This Function should be in utilities.
     * I can not decide yet. so left it here till then. 
     */

    /**
     * Function to get the file name for a class
     * 
     * @access  public
     * 
     * @var     $class_name     string      Name of the class
     * @return  $file_name      string      Name of the file for the corresponding class
     */
    public function get_file_name($class_name)
    {
        $class_name = strtolower($class_name);
        $file_name = $this->file_prefix . $class_name . $this->file_extension;
        return $file_name;
    }

}
