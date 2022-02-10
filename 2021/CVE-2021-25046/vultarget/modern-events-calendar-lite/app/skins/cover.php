<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC cover class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_cover extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'cover';

    public $event_id;
    public $date_format_clean1;
    public $date_format_clean2;
    public $date_format_clean3;
    public $date_format_classic1;
    public $date_format_classic2;
    public $date_format_modern1;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Registers skin actions into WordPress
     * @author Webnus <info@webnus.biz>
     */
    public function actions()
    {
    }
    
    /**
     * Initialize the skin
     * @author Webnus <info@webnus.biz>
     * @param array $atts
     */
    public function initialize($atts)
    {
        $this->atts = $atts;
        $this->skin_options = (isset($this->atts['sk-options']) and isset($this->atts['sk-options'][$this->skin])) ? $this->atts['sk-options'][$this->skin] : array();
        
        // Date Formats
        $this->date_format_clean1 = (isset($this->skin_options['date_format_clean1']) and trim($this->skin_options['date_format_clean1'])) ? $this->skin_options['date_format_clean1'] : 'd';
        $this->date_format_clean2 = (isset($this->skin_options['date_format_clean2']) and trim($this->skin_options['date_format_clean2'])) ? $this->skin_options['date_format_clean2'] : 'M';
        $this->date_format_clean3 = (isset($this->skin_options['date_format_clean3']) and trim($this->skin_options['date_format_clean3'])) ? $this->skin_options['date_format_clean3'] : 'Y';
        
        $this->date_format_classic1 = (isset($this->skin_options['date_format_classic1']) and trim($this->skin_options['date_format_classic1'])) ? $this->skin_options['date_format_classic1'] : 'F d';
        $this->date_format_classic2 = (isset($this->skin_options['date_format_classic2']) and trim($this->skin_options['date_format_classic2'])) ? $this->skin_options['date_format_classic2'] : 'l';
        
        $this->date_format_modern1 = (isset($this->skin_options['date_format_modern1']) and trim($this->skin_options['date_format_modern1'])) ? $this->skin_options['date_format_modern1'] : 'l, F d Y';
        
        // Search Form Status
        $this->sf_status = false;
        
        $this->id = mt_rand(100, 999);
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;
        
        // The style
        $this->style = isset($this->skin_options['style']) ? $this->skin_options['style'] : 'classic';
        if($this->style == 'fluent' and !is_plugin_active('mec-fluent-layouts/mec-fluent-layouts.php')) $this->style = 'classic';
        
        // Override the style if the style forced by us in a widget etc
        if(isset($this->atts['style']) and trim($this->atts['style']) != '') $this->style = $this->atts['style'];

        // reason_for_cancellation
        $this->reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;

        // display_label
        $this->display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
        
        // HTML class
        $this->html_class = '';
        if(isset($this->atts['html-class']) and trim($this->atts['html-class']) != '') $this->html_class = $this->atts['html-class'];
        
        // From Widget
        $this->widget = (isset($this->atts['widget']) and trim($this->atts['widget'])) ? true : false;
        
        // Init MEC
        $this->args['mec-skin'] = $this->skin;
        
        $this->event_id = isset($this->skin_options['event_id']) ? $this->skin_options['event_id'] : 0;
        $this->maximum_dates = isset($this->atts['maximum_dates']) ? $this->atts['maximum_dates'] : 6;
    }
    
    /**
     * Search and returns the filtered events
     * @author Webnus <info@webnus.biz>
     * @return array of objects
     */
    public function search()
    {
        if(!get_post($this->event_id)) return array();

        $events = array();
        $rendered = $this->render->data($this->event_id, (isset($this->atts['content']) ? $this->atts['content'] : ''));
        
        $data = new stdClass();
        $data->ID = $this->event_id;
        $data->data = $rendered;
        $data->dates = $this->render->dates($this->event_id, $rendered, $this->maximum_dates);
        $data->date = isset($data->dates[0]) ? $data->dates[0] : array();

        $events[] = $this->render->after_render($data, $this);
        
        return $events;
    }
}