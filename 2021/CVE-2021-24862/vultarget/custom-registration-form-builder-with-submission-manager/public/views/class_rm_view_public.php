<?php
/**
 * Class used to render view for form manager of the plugin.
 * 
 * This class conatains the rendering functionality and the view(HTML) for the 
 * plugin admin area formamanager.
 *
 * @link        http://registration_magic.com
 * @since       1.0.0
 *
 * @package     Registraion_Magic
 * @subpackage  Registraion_Magic/includes
 * @author      CMSHelplive
 */

/**
 * Still in development phase. Designed only a rough view for testing.
 * 
 * All the views will be merged in a single class that will load templates for the view.
 */
class RM_View_Public
{
    
    public $view_file;

    public function __construct($view_identifier)
    {
        $prefix = 'template_rm_';
        $suffix = '.php';
        $this->view_file = plugin_dir_path(__FILE__).$prefix.$view_identifier.$suffix;
    }

    public function render($data=null, $force_enable_multiple_forms = false)
    {  // session_start();
        if($force_enable_multiple_forms)
            include $this->view_file;
        else
            include_once $this->view_file;
       // include plugin_dir_path(__FILE__).'template_rm_add_form.php';
    }
    
    
    public function read($data=null, $force_enable_multiple_forms = false, $is_sub = false)
    {  
        ob_start();
        echo '<!--noptimize-->';
        //include_once $this->view_file;
        if($force_enable_multiple_forms)
            include $this->view_file;
        else
            include $this->view_file;
        echo '<!--/noptimize-->';
        return ob_get_clean();
    }

}