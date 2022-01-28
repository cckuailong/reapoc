<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC VC addon class
 * @author Webnus <info@webnus.biz>
 */
class MEC_addon_VC extends MEC_base
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
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC Factory class
        $this->factory = $this->getFactory();
        
        // MEC Main class
        $this->main = $this->getMain();
    }
    
    /**
     * Initialize the VC addon
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Visual Composer is not installed
        if(!function_exists('vc_map')) return false;
        
        $this->factory->action('vc_before_init', array($this, 'map'));
        return true;
    }
    
    /**
     * Register the addon in VC
     * @author Webnus <info@webnus.biz>
     */
    public function map()
    {
        $calendar_posts = get_posts(array('post_type'=>'mec_calendars', 'posts_per_page'=>'-1'));
        
        $calendars = array();
        foreach($calendar_posts as $calendar_post) $calendars[$calendar_post->post_title] = $calendar_post->ID;
        
        vc_map(array(
            'name'=>esc_html__('Modern Events Calendar', 'modern-events-calendar-lite'),
            'base'=>'MEC',
            'class'=>'',
            'controls'=>'full',
            'icon'=>$this->main->asset('img/ico-mec-vc.png'),
            'category'=>esc_html__('Content', 'modern-events-calendar-lite'),
            'params'=>array(
               array(
                  'type'=>'dropdown',
                  'holder'=>'div',
                  'class'=>'',
                  'heading'=>esc_html__('Shortcode', 'modern-events-calendar-lite'),
                  'param_name'=>'id',
                  'value'=>$calendars,
                  'description'=>esc_html__('Select from predefined shortcodes', 'modern-events-calendar-lite')
               )
            )
        ));
    }
}