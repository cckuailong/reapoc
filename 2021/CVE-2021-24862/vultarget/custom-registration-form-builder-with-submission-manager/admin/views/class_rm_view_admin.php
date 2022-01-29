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
class RM_View_Admin
{
    
    public $view_file, $view_identifier;

    public function __construct($view_identifier)
    {
        $prefix = 'template_rm_';
        $suffix = '.php';
        $this->view_identifier = $view_identifier;
        $this->view_file = plugin_dir_path(__FILE__).$prefix.$view_identifier.$suffix;
    }

    public function render($data=null)
    {
        //session_start();
        do_action('rm_pre_admin_template_render', $this->view_identifier);
        include_once $this->view_file;
        include_once 'template_rm_footer.php';
        //include plugin_dir_path(__FILE__).'template_rm_add_form.php';
    }
    
    public function read($data=null)
    {
        ob_start();
        include $this->view_file;
        return ob_get_clean();
    }

}