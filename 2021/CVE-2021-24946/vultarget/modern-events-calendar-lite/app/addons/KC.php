<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC King Composer addon class
 * @author Webnus <info@webnus.biz>
 */
class MEC_addon_KC extends MEC_base
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
     * Initialize the KC addon
     * @author Webnus <info@webnus.biz>
     * @return boolean
     */
    public function init()
    {
        // King Composer is not installed
        if(!function_exists('kc_add_map')) return false;
        
        $this->factory->action('init', array($this, 'map'));
        return true;
    }
    
    /**
     * Register the addon in KC
     * @author Webnus <info@webnus.biz>
     */
    public function map()
    {
        $calendar_posts = get_posts(array('post_type'=>'mec_calendars', 'posts_per_page'=>'-1'));
        
        $calendars_name = $calendars_number = array();
        foreach($calendar_posts as $calendar_post)
        {
            $calendars_name[] = $calendar_post->post_title;
            $calendars_number[] = $calendar_post->ID;
        }

        $calendars_array  = array_combine($calendars_number, $calendars_name);

        kc_add_map(array
        (
            'MEC' => array(
                'name' => esc_html__('Modern Events Calendar', 'modern-events-calendar-lite'),
                'icon' => 'mec-kingcomposer-icon',
                'category' => esc_html__('Content', 'modern-events-calendar-lite'),
                'params' => array(
                    'General' => array(
                        array(
                            'name' => 'id',
                            'label' => esc_html__('Shortcode', 'modern-events-calendar-lite'),
                            'type' => 'select',
                            'options' => $calendars_array,
                            'description' => esc_html__('Select from predefined shortcodes', 'modern-events-calendar-lite'),
                        ),
                    ),
                )
            ),
        ));
    }
}