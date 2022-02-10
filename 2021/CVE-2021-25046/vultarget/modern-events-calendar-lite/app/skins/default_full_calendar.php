<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Full Calendar class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_default_full_calendar extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'default_full_calendar';

    public $default_view;
    public $monthly_style;
    public $yearly;
    public $monthly;
    public $weekly;
    public $daily;
    public $list;
    
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
        $this->factory->action('wp_ajax_mec_full_calendar_switch_skin', array($this, 'switch_skin'));
        $this->factory->action('wp_ajax_nopriv_mec_full_calendar_switch_skin', array($this, 'switch_skin'));
    }
    
    /**
     * Initialize the skin
     * @author Webnus <info@webnus.biz>
     * @param array $atts
     */
    public function initialize($atts)
    {
        $this->atts = $atts;
        
        // Skin Options
        $this->skin_options = (isset($this->atts['sk-options']) and isset($this->atts['sk-options'][$this->skin])) ? $this->atts['sk-options'][$this->skin] : array();
        
        // Search Form Options
        $this->sf_options = (isset($this->atts['sf-options']) and isset($this->atts['sf-options'][$this->skin])) ? $this->atts['sf-options'][$this->skin] : array();
        
        // Search Form Status
        $this->sf_status = isset($this->atts['sf_status']) ? $this->atts['sf_status'] : true;
        $this->sf_display_label = isset($this->atts['sf_display_label']) ? $this->atts['sf_display_label'] : false;
        $this->sf_reset_button = isset($this->atts['sf_reset_button']) ? $this->atts['sf_reset_button'] : false;
        $this->sf_refine = isset($this->atts['sf_refine']) ? $this->atts['sf_refine'] : false;
        
        // Show Only Expired Events
        $this->show_only_expired_events = (isset($this->atts['show_only_past_events']) and trim($this->atts['show_only_past_events'])) ? '1' : '0';
        
        // Start Date
        $this->start_date = $this->get_start_date();
        
        // Generate an ID for the skin
        $this->id = isset($this->atts['id']) ? $this->atts['id'] : mt_rand(100, 999);

        // Booking Button
        $this->booking_button = isset($this->skin_options['booking_button']) ? (int) $this->skin_options['booking_button'] : 0;
        
        // SED Method
        $this->sed_method = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';

        // Image popup
        $this->image_popup = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
        
        // Default View of Full Calendar
        $this->default_view = isset($this->skin_options['default_view']) ? $this->skin_options['default_view'] : 'list';
        if(isset($this->skin_options[$this->default_view]) and !$this->skin_options[$this->default_view]) $this->default_view = 'list';

        // Default style for Monthly View
        $this->monthly_style = isset($this->skin_options['monthly_style']) ? $this->skin_options['monthly_style'] : 'clean';
        if(isset($this->skin_options[$this->monthly_style]) and !$this->skin_options[$this->monthly_style]) $this->monthly_style = 'clean';


        $this->yearly = isset($this->skin_options['yearly']) ? $this->skin_options['yearly'] : true;
        $this->monthly = isset($this->skin_options['monthly']) ? $this->skin_options['monthly'] : true;
        $this->weekly = isset($this->skin_options['weekly']) ? $this->skin_options['weekly'] : true;
        $this->daily = isset($this->skin_options['daily']) ? $this->skin_options['daily'] : true;
        $this->list = isset($this->skin_options['list']) ? $this->skin_options['list'] : true;
        
        // If all of skins are disabled
        if(!$this->monthly and !$this->weekly and !$this->daily and !$this->list and !$this->yearly)
        {
            $this->monthly = true;
            $this->list = true;
        }
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;
    }
    
    public function get_start_date()
    {
        // Default date
        $date = current_time('Y-m-d');
        
        if(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'today') $date = current_time('Y-m-d');
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'tomorrow') $date = date('Y-m-d', strtotime('Tomorrow'));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'yesterday') $date = date('Y-m-d', strtotime('Yesterday'));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_last_month') $date = date('Y-m-d', strtotime('first day of last month'));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_current_month') $date = date('Y-m-d', strtotime('first day of this month'));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_next_month') $date = date('Y-m-d', strtotime('first day of next month'));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'date') $date = date('Y-m-d', strtotime($this->skin_options['start_date']));
        
        // Hide past events
        if(isset($this->atts['show_past_events']) and !trim($this->atts['show_past_events']))
        {
            $today = current_time('Y-m-d');
            if(strtotime($date) < strtotime($today)) $date = $today;
        }
        
        // Show only expired events
        if(isset($this->show_expired_events) and $this->show_expired_events)
        {
            $now = date('Y-m-d H:i:s', current_time('timestamp', 0));
            if(strtotime($date) > strtotime($now)) $date = $now;
        }
        
        return $date;
    }
    
    public function search()
    {
    }
    
    public function load_skin($skin = 'list')
    {
        switch($skin)
        {
            case 'yearly':

                $atts = $this->atts;

                $start_date_type = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : 'start_current_year';

                if($start_date_type == 'start_current_month') $start_date_type = 'start_current_year';
                elseif($start_date_type == 'start_next_month') $start_date_type = 'start_next_year';
                else $start_date_type = 'date';

                $atts['sk-options']['yearly_view']['start_date_type'] = $start_date_type;
                $atts['sk-options']['yearly_view']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : current_time('Y-01-01');
                $atts['sk-options']['yearly_view']['style'] = 'modern';
                $atts['sk-options']['yearly_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['yearly_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['yearly_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sf_status'] = false;

                $output = $this->render->vyear($atts);

                break;

            case 'monthly':
                
                $atts = $this->atts;
                $atts['sk-options']['monthly_view']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['monthly_view']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['monthly_view']['style'] = $this->monthly_style;
                $atts['sk-options']['monthly_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['monthly_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['monthly_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sf_status'] = false;
                
                $output = $this->render->vmonth($atts);
                
                break;
            
            case 'weekly':
                
                $atts = $this->atts;
                $atts['sk-options']['weekly_view']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['weekly_view']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['weekly_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['weekly_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['weekly_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sf_status'] = false;
                
                $output = $this->render->vweek($atts);
                
                break;
            
            case 'daily':
                
                $atts = $this->atts;
                $atts['sk-options']['daily_view']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['daily_view']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['daily_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['daily_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['daily_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sf_status'] = false;
                
                $output = $this->render->vday($atts);
                
                break;
            
            case 'list':
            default:
                
                $atts = $this->atts;
                $atts['sk-options']['list']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['list']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['list']['style'] = 'standard';
                $atts['sk-options']['list']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['list']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['list']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['list']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sf_status'] = false;

                $output = $this->render->vlist($atts);
                
                break;
        }
        
        return $output;
    }
    
    /**
     * Load skin for AJAX requert
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function switch_skin()
    {
        $this->sf = $this->request->getVar('sf', array());
        $apply_sf_date = $this->request->getVar('apply_sf_date', 1);
        $atts = $this->sf_apply($this->request->getVar('atts', array()), $this->sf, $apply_sf_date);
        
        $skin = $this->request->getVar('skin', 'list');
        
        // Single Event Display
        $atts['sed_method'] = $this->request->getVar('sed', 0);
        $atts['image_popup'] = $this->request->getVar('image', 0);
        
        // Initialize the skin
        $this->initialize($atts);
        
        // Return the output
        $output = $this->load_skin($skin);
        
        echo json_encode($output);
        exit;
    }
}