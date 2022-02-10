<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Full Calendar class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_full_calendar extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'full_calendar';

    public $include_local_time;
    public $reason_for_cancellation;
    public $display_label;
    public $default_view;
    public $monthly_style;
    public $yearly;
    public $monthly;
    public $weekly;
    public $daily;
    public $list;
    public $list_date_end;
    public $list_maximum_date;
    public $grid;
    public $grid_date_end;
    public $grid_maximum_date;
    public $tile;

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

        // Include Local Time
        $this->include_local_time = (isset($this->skin_options['include_local_time']) and trim($this->skin_options['include_local_time'])) ? '1' : '0';
        
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

        // reason_for_cancellation
        $this->reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;

        // display_label
        $this->display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
        
        // Default View of Full Calendar
        $this->default_view = isset($this->skin_options['default_view']) ? $this->skin_options['default_view'] : 'list';

        // Default style for Monthly View
        $this->monthly_style = isset($this->skin_options['monthly_style']) ? $this->skin_options['monthly_style'] : 'clean';
        if(isset($this->skin_options[$this->monthly_style]) and !$this->skin_options[$this->monthly_style]) $this->monthly_style = 'clean';

        $this->yearly = (isset($this->skin_options['yearly']) and $this->getPRO()) ? $this->skin_options['yearly'] : false;
        $this->monthly = isset($this->skin_options['monthly']) ? $this->skin_options['monthly'] : true;
        $this->weekly = isset($this->skin_options['weekly']) ? $this->skin_options['weekly'] : true;
        $this->daily = isset($this->skin_options['daily']) ? $this->skin_options['daily'] : true;
        $this->list = isset($this->skin_options['list']) ? $this->skin_options['list'] : true;
        $this->grid = isset($this->skin_options['grid']) ? $this->skin_options['grid'] : true;
        $this->tile = isset($this->skin_options['tile']) ? $this->skin_options['tile'] : true;

        // If all of skins are disabled
        if(!$this->monthly and !$this->weekly and !$this->daily and !$this->list and !$this->yearly and !$this->grid and !$this->tile)
        {
            $this->monthly = true;
            $this->list = true;
        }

        // Validate Default View
        if(isset($this->{$this->default_view}) and !$this->{$this->default_view}) $this->default_view = 'list';
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;

        do_action('mec-full-calendar-initialize-end', $this);
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
                $atts['sk-options']['yearly_view']['start_date'] = (isset($this->skin_options['start_date']) and trim($this->skin_options['start_date'])) ? $this->skin_options['start_date'] : current_time('Y-01-01');
                $atts['sk-options']['yearly_view']['style'] = 'modern';
                $atts['sk-options']['yearly_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['yearly_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['yearly_view']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['yearly_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sk-options']['yearly_view']['modern_date_format1'] = isset($this->skin_options['date_format_yearly_1']) ? $this->skin_options['date_format_yearly_1'] : 'l';
                $atts['sk-options']['yearly_view']['modern_date_format2'] = isset($this->skin_options['date_format_yearly_2']) ? $this->skin_options['date_format_yearly_2'] : 'F j';
                $atts['sk-options']['yearly_view']['display_label'] = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
                $atts['sk-options']['yearly_view']['reason_for_cancellation'] = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
                $atts['sk-options']['yearly_view']['include_local_time'] = $this->include_local_time;
                $atts['sk-options']['yearly_view']['booking_button'] = isset($this->skin_options['booking_button']) ? $this->skin_options['booking_button'] : 0;
                $atts['sk-options']['yearly_view']['from_fc'] = 1;
                $atts['sf_status'] = false;

                $atts = apply_filters('mec-full-calendar-load-skin-yearly', $atts, $this, 'yearly_view');
                $output = $this->render->vyear($atts);

                break;

            case 'monthly':
                
                $atts = $this->atts;

                $start_date_type = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : 'today';
                if($start_date_type == 'today') $start_date_type = 'start_current_month';

                $atts['sk-options']['monthly_view']['start_date_type'] = $start_date_type;
                $atts['sk-options']['monthly_view']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['monthly_view']['style'] = $this->monthly_style;
                $atts['sk-options']['monthly_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['monthly_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['monthly_view']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['monthly_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sk-options']['monthly_view']['display_label'] = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
                $atts['sk-options']['monthly_view']['reason_for_cancellation'] = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
                $atts['sk-options']['monthly_view']['include_local_time'] = $this->include_local_time;
                $atts['sk-options']['monthly_view']['activate_first_date'] = isset($this->skin_options['activate_first_date']) ? $this->skin_options['activate_first_date'] : '0';
                $atts['sk-options']['monthly_view']['activate_current_day'] = isset($this->skin_options['activate_current_day']) ? $this->skin_options['activate_current_day'] : '1';
                $atts['sk-options']['monthly_view']['booking_button'] = isset($this->skin_options['booking_button']) ? $this->skin_options['booking_button'] : 0;
                $atts['sk-options']['monthly_view']['from_fc'] = 1;
                $atts['sf_status'] = false;

                $atts = apply_filters('mec-full-calendar-load-skin-monthly', $atts, $this, 'monthly_view');
                $output = $this->render->vmonth($atts);
                
                break;
            
            case 'weekly':
                
                $atts = $this->atts;

                $start_date_type = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : 'today';
                if($start_date_type == 'today') $start_date_type = 'start_current_month';

                $atts['sk-options']['weekly_view']['start_date_type'] = $start_date_type;
                $atts['sk-options']['weekly_view']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['weekly_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['weekly_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['weekly_view']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['weekly_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sk-options']['weekly_view']['display_label'] = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
                $atts['sk-options']['weekly_view']['reason_for_cancellation'] = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
                $atts['sk-options']['weekly_view']['include_local_time'] = $this->include_local_time;
                $atts['sk-options']['weekly_view']['booking_button'] = isset($this->skin_options['booking_button']) ? $this->skin_options['booking_button'] : 0;
                $atts['sk-options']['weekly_view']['from_fc'] = 1;
                $atts['sf_status'] = false;

                $atts = apply_filters('mec-full-calendar-load-skin-weekly', $atts, $this, 'weekly_view');
                $output = $this->render->vweek($atts);
                
                break;
            
            case 'daily':
                
                $atts = $this->atts;
                $atts['sk-options']['daily_view']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['daily_view']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['daily_view']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['daily_view']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['daily_view']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['daily_view']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sk-options']['daily_view']['display_label'] = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
                $atts['sk-options']['daily_view']['reason_for_cancellation'] = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
                $atts['sk-options']['daily_view']['include_local_time'] = $this->include_local_time;
                $atts['sk-options']['daily_view']['booking_button'] = isset($this->skin_options['booking_button']) ? $this->skin_options['booking_button'] : 0;
                $atts['sk-options']['daily_view']['from_fc'] = 1;
                $atts['sf_status'] = false;

                $atts = apply_filters('mec-full-calendar-load-skin-daily', $atts, $this, 'daily_view');
                $output = $this->render->vday($atts);
                
                break;

            case 'grid':

                // Maximum Date Range.
                $end_date_type = (isset($this->skin_options['end_date_type_grid']) and trim($this->skin_options['end_date_type_grid'])) ? trim($this->skin_options['end_date_type_grid']) : 'date';

                if($end_date_type === 'today') $maximum_date = current_time('Y-m-d');
                elseif($end_date_type === 'tomorrow') $maximum_date = date('Y-m-d', strtotime('Tomorrow'));
                else $maximum_date = (isset($this->skin_options['maximum_date_range_grid']) and trim($this->skin_options['maximum_date_range_grid'])) ? trim($this->skin_options['maximum_date_range_grid']) : NULL;

                $atts = $this->atts;
                $atts['sk-options']['grid']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['grid']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['grid']['end_date_type'] = $end_date_type;
                $atts['sk-options']['grid']['maximum_date_range'] = $maximum_date;
                $atts['sk-options']['grid']['style'] = 'modern';
                $atts['sk-options']['grid']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['grid']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['grid']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['grid']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sk-options']['grid']['modern_date_format1'] = 'd';
                $atts['sk-options']['grid']['modern_date_format2'] = 'F';
                $atts['sk-options']['grid']['modern_date_format3'] = 'l';
                $atts['sk-options']['grid']['count'] = '3';
                $atts['sk-options']['grid']['display_label'] = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
                $atts['sk-options']['grid']['reason_for_cancellation'] = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
                $atts['sk-options']['grid']['include_local_time'] = $this->include_local_time;
                $atts['sk-options']['grid']['booking_button'] = isset($this->skin_options['booking_button']) ? $this->skin_options['booking_button'] : 0;
                $atts['sk-options']['grid']['from_fc'] = 1;
                $atts['sf_status'] = false;

                $atts = apply_filters('mec-full-calendar-load-skin-grid', $atts, $this, 'grid');
                $output = $this->render->vgrid($atts);

                break;

            case 'tile':

                $atts = $this->atts;
                $atts['sk-options']['tile']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['tile']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['tile']['style'] = 'clean';
                $atts['sk-options']['tile']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['tile']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['tile']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['tile']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sk-options']['tile']['clean_date_format1'] = 'j';
                $atts['sk-options']['tile']['clean_date_format2'] = 'M';
                $atts['sk-options']['tile']['display_label'] = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
                $atts['sk-options']['tile']['reason_for_cancellation'] = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
                $atts['sk-options']['tile']['include_local_time'] = $this->include_local_time;
                $atts['sk-options']['tile']['booking_button'] = isset($this->skin_options['booking_button']) ? $this->skin_options['booking_button'] : 0;
                $atts['sk-options']['tile']['from_fc'] = 1;
                $atts['sf_status'] = false;

                $atts = apply_filters('mec-full-calendar-load-skin-tile', $atts, $this, 'tile');
                $output = $this->render->vtile($atts);

                break;
            
            case 'list':
            default:

                // Maximum Date Range.
                $end_date_type = (isset($this->skin_options['end_date_type_list']) and trim($this->skin_options['end_date_type_list'])) ? trim($this->skin_options['end_date_type_list']) : 'date';

                if($end_date_type === 'today') $maximum_date = current_time('Y-m-d');
                elseif($end_date_type === 'tomorrow') $maximum_date = date('Y-m-d', strtotime('Tomorrow'));
                else $maximum_date = (isset($this->skin_options['maximum_date_range_list']) and trim($this->skin_options['maximum_date_range_list'])) ? trim($this->skin_options['maximum_date_range_list']) : NULL;

                $atts = $this->atts;
                $atts['sk-options']['list']['start_date_type'] = isset($this->skin_options['start_date_type']) ? $this->skin_options['start_date_type'] : '';
                $atts['sk-options']['list']['start_date'] = isset($this->skin_options['start_date']) ? $this->skin_options['start_date'] : '';
                $atts['sk-options']['list']['end_date_type'] = $end_date_type;
                $atts['sk-options']['list']['maximum_date_range'] = $maximum_date;
                $atts['sk-options']['list']['style'] = 'standard';
                $atts['sk-options']['list']['sed_method'] = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';
                $atts['sk-options']['list']['image_popup'] = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
                $atts['sk-options']['list']['display_price'] = isset($this->skin_options['display_price']) ? $this->skin_options['display_price'] : 0;
                $atts['sk-options']['list']['limit'] = isset($this->skin_options['limit']) ? $this->skin_options['limit'] : 12;
                $atts['sk-options']['list']['standard_date_format1'] = isset($this->skin_options['date_format_list']) ? $this->skin_options['date_format_list'] : 'd M';
                $atts['sk-options']['list']['display_label'] = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
                $atts['sk-options']['list']['reason_for_cancellation'] = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;
                $atts['sk-options']['list']['include_local_time'] = $this->include_local_time;
                $atts['sk-options']['list']['booking_button'] = isset($this->skin_options['booking_button']) ? $this->skin_options['booking_button'] : 0;
                $atts['sk-options']['list']['from_fc'] = 1;
                $atts['sf_status'] = false;

                $atts = apply_filters('mec-full-calendar-load-skin-list', $atts, $this, 'list');
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