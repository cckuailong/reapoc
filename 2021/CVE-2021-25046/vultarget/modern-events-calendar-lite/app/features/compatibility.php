<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC compatibility class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_compatibility extends MEC_base
{
    /**
     * @var MEC_factory
     */
    public $factory;

    /**
     * @var MEC_main
     */
    public $main;

    /**
     * To Override default HTML ID of MEC template files
     * @var string
     */
    public $html_id = '';
    
    /**
     * To add HTML Classes to MEC template files
     * @var array
     */
    public $html_class = array();
    
    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();
        
        // Import MEC Main
        $this->main = $this->getMain();
    }
    
    /**
     * Initialize compatibility
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // MEC Filters for changing HTML ID of MEC Pages
        $this->factory->filter('mec_archive_page_html_id', array($this, 'html_id'));
        $this->factory->filter('mec_category_page_html_id', array($this, 'html_id'));
        $this->factory->filter('mec_single_page_html_id', array($this, 'html_id'));
        
        // MEC Filters for changing HTML class of MEC Pages
        $this->factory->filter('mec_archive_page_html_class', array($this, 'html_class'));
        $this->factory->filter('mec_category_page_html_class', array($this, 'html_class'));
        $this->factory->filter('mec_single_page_html_class', array($this, 'html_class'));
        
        // Make MEC compatible with themes and child themes
        $this->factory->action('init', array($this, 'make_it_compatible'));
    }
    
    /**
     * Make MEC compatible by adding/changing HTML Classes/IDs
     * @author Webnus <info@webnus.biz>
     */
    public function make_it_compatible()
    {
        $template = get_template();
        
        switch($template)
        {
            case 'logitrans':
                
                $this->html_class = array('wrapper');
                
                break;
        }
    }
    
    /**
     * Return HTML ID of MEC Pages
     * @author Webnus <info@webnus.biz>
     * @param string $id
     * @return string
     */
    public function html_id($id)
    {
        if(trim($this->html_id)) return $this->html_id;
        else return $id;
    }
    
    /**
     * Return HTML Class of MEC Pages
     * @author Webnus <info@webnus.biz>
     * @param string $class
     * @return string
     */
    public function html_class($class)
    {
        if(is_array($this->html_class) and count($this->html_class)) return $class.' '.implode(' ', $this->html_class);
        else return $class;
    }
}