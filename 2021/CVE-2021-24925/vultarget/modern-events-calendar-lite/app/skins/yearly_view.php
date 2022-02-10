<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC yearly view class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_yearly_view extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'yearly_view';
    public $date_format_modern_1;
    public $date_format_modern_2;
    public $months_to_display;

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
        $this->factory->action('wp_ajax_mec_yearly_view_load_year', array($this, 'load_year'));
        $this->factory->action('wp_ajax_nopriv_mec_yearly_view_load_year', array($this, 'load_year'));
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

        // Date Formats
        $this->date_format_modern_1 = (isset($this->skin_options['modern_date_format1']) and trim($this->skin_options['modern_date_format1'])) ? $this->skin_options['modern_date_format1'] : 'l';
        $this->date_format_modern_2 = (isset($this->skin_options['modern_date_format2']) and trim($this->skin_options['modern_date_format2'])) ? $this->skin_options['modern_date_format2'] : 'F j';

        // Search Form Options
        $this->sf_options = (isset($this->atts['sf-options']) and isset($this->atts['sf-options'][$this->skin])) ? $this->atts['sf-options'][$this->skin] : array();
        
        // Search Form Status
        $this->sf_status = isset($this->atts['sf_status']) ? $this->atts['sf_status'] : true;
        $this->sf_display_label = isset($this->atts['sf_display_label']) ? $this->atts['sf_display_label'] : false;
        $this->sf_reset_button = isset($this->atts['sf_reset_button']) ? $this->atts['sf_reset_button'] : false;
        $this->sf_refine = isset($this->atts['sf_refine']) ? $this->atts['sf_refine'] : false;

        // Generate an ID for the sking
        $this->id = isset($this->atts['id']) ? $this->atts['id'] : mt_rand(100, 999);
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;
        
        // The style
        $this->style = isset($this->skin_options['style']) ? $this->skin_options['style'] : 'modern';
        if($this->style == 'fluent' and !is_plugin_active('mec-fluent-layouts/mec-fluent-layouts.php')) $this->style = 'modern';

        // reason_for_cancellation
        $this->reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;

        // display_label
        $this->display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
        
        // Next/Previous Month
        $this->next_previous_button = isset($this->skin_options['next_previous_button']) ? $this->skin_options['next_previous_button'] : true;
        
        // Override the style if the style forced by us in a widget etc
        if(isset($this->atts['style']) and trim($this->atts['style']) != '') $this->style = $this->atts['style'];
        
        // HTML class
        $this->html_class = '';
        if(isset($this->atts['html-class']) and trim($this->atts['html-class']) != '') $this->html_class = $this->atts['html-class'];

        // Booking Button
        $this->booking_button = isset($this->skin_options['booking_button']) ? (int) $this->skin_options['booking_button'] : 0;
        
        // SED Method
        $this->sed_method = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';

        // Image popup
        $this->image_popup = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
        
        // From Widget
        $this->widget = (isset($this->atts['widget']) and trim($this->atts['widget'])) ? true : false;

        // From Full Calendar
        $this->from_full_calendar = (isset($this->skin_options['from_fc']) and trim($this->skin_options['from_fc'])) ? true : false;
        
        // Init MEC
        $this->args['mec-init'] = true;
        $this->args['mec-skin'] = $this->skin;
        
        // Post Type
        $this->args['post_type'] = $this->main->get_main_post_type();

        // Post Status
        $this->args['post_status'] = 'publish';
        
        // Keyword Query
        $this->args['s'] = $this->keyword_query();
        
        // Taxonomy
        $this->args['tax_query'] = $this->tax_query();
        
        // Meta
        $this->args['meta_query'] = $this->meta_query();
        
        // Tag
        if(apply_filters('mec_taxonomy_tag', '') === 'post_tag') $this->args['tag'] = $this->tag_query();
        
        // Author
        $this->args['author'] = $this->author_query();
        
        // Pagination Options
        $this->paged = get_query_var('paged', 1);
        $this->limit = (isset($this->skin_options['limit']) and trim($this->skin_options['limit'])) ? $this->skin_options['limit'] : 12;
        
        $this->args['posts_per_page'] = $this->limit;
        $this->args['paged'] = $this->paged;
        
        // Sort Options
        $this->args['orderby'] = 'meta_value_num';
        $this->args['order'] = 'ASC';
        $this->args['meta_key'] = 'mec_start_day_seconds';

        // Show Only Expired Events
        $this->show_only_expired_events = (isset($this->atts['show_only_past_events']) and trim($this->atts['show_only_past_events'])) ? '1' : '0';

        // Show Past Events
        if($this->show_only_expired_events) $this->atts['show_past_events'] = '1';

        // Show Past Events
        $this->args['mec-past-events'] = isset($this->atts['show_past_events']) ? $this->atts['show_past_events'] : '0';
        
        // Start Date
        list($this->year, $this->month, $this->day) = $this->get_start_date();

        $this->start_date = date('Y-m-d', strtotime($this->year.'-'.$this->month.'-'.$this->day));
        $this->active_day = $this->year.'-'.$this->month.'-'.date('d');
        
        // We will extend the end date in the loop
        $this->end_date = $this->start_date;

        // Months to Display
        $this->months_to_display = (isset($this->skin_options['months']) and is_array($this->skin_options['months']) and count($this->skin_options['months'])) ? $this->skin_options['months'] : array();
    }
    
    /**
     * Search and returns the filtered events
     * @author Webnus <info@webnus.biz>
     * @return array of objects
     */
    public function search()
    {
        if($this->show_only_expired_events)
        {
            $start =  date('Y-m-d H:i:s', current_time('timestamp', 0));
            $end = date('Y-01-01', strtotime($this->start_date));
        }
        else
        {
            $start = $this->start_date;
            $end = date('Y-12-31', strtotime($this->start_date));
        }

        // Date Events
        $dates = $this->period($start, $end, true);

        // Limit
        $this->args['posts_per_page'] = $this->limit;

        $events = array();
        foreach($dates as $date=>$IDs)
        {
            // No Event
            if(!is_array($IDs) or (is_array($IDs) and !count($IDs))) continue;

            // Included in Selected Months
            if(isset($this->months_to_display) and is_array($this->months_to_display) and count($this->months_to_display))
            {
                $n = date('n', strtotime($date));
                if(isset($this->months_to_display[$n]) and !$this->months_to_display[$n]) continue;
            }

            // Include Available Events
            $this->args['post__in'] = $IDs;

            // Count of events per day
            $IDs_count = array_count_values($IDs);

            // The Query
            $query = new WP_Query($this->args);
            if($query->have_posts())
            {
                if(!isset($events[$date])) $events[$date] = array();

                // Day Events
                $d = array();

                // The Loop
                while($query->have_posts())
                {
                    $query->the_post();
                    $ID = get_the_ID();

                    $ID_count = isset($IDs_count[$ID]) ? $IDs_count[$ID] : 1;
                    for($i = 1; $i <= $ID_count; $i++)
                    {
                        $rendered = $this->render->data($ID);

                        $data = new stdClass();
                        $data->ID = $ID;
                        $data->data = $rendered;

                        $data->date = array
                        (
                            'start'=>array('date'=>$date),
                            'end'=>array('date'=>$this->main->get_end_date($date, $rendered))
                        );

                        $d[] = $this->render->after_render($data, $this, $i);
                    }
                }

                usort($d, array($this, 'sort_day_events'));
                $events[$date] = $d;
            }

            // Restore original Post Data
            wp_reset_postdata();
        }

        return $events;
    }
    
    /**
     * Returns start day of skin for filtering events
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function get_start_date()
    {
        // Default date
        $date = current_time('Y-01-01');

        if(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_current_year') $date = date('Y-m-d', strtotime('first day of January '.current_time('Y')));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_next_year') $date = date('Y-m-d', strtotime('first day of January '.(current_time('Y')+1)));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_last_year') $date = date('Y-m-d', strtotime('first day of January '.(current_time('Y')-1)));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'date') $date = date('Y-m-d', strtotime($this->skin_options['start_date']));
        
        // Hide past events
        if(isset($this->atts['show_past_events']) and !trim($this->atts['show_past_events']))
        {
            $today = current_time('Y-m-d');
            if(strtotime($date) < strtotime($today)) $date = $today;
        }
        
        // Show only expired events
        if(isset($this->show_only_expired_events) and $this->show_only_expired_events)
        {
            $yesterday = date('Y-m-d', strtotime('Yesterday'));
            if(strtotime($date) > strtotime($yesterday)) $date = $yesterday;
        }
        
        $time = strtotime($date);
        return array(date('Y', $time), date('m', $time), date('d', $time));
    }
    
    /**
     * Load year for AJAX requert
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function load_year()
    {
        $this->sf = $this->request->getVar('sf', array());
        $apply_sf_date = $this->request->getVar('apply_sf_date', 1);
        $atts = $this->sf_apply($this->request->getVar('atts', array()), $this->sf, $apply_sf_date);
        
        // Initialize the skin
        $this->initialize($atts);
        
        // Start Date
        $this->year = $this->request->getVar('mec_year', date('Y'));
        
        if($this->show_only_expired_events)
        {
            $this->start_date = date('Y-m-01', strtotime($this->year.'-01-01'));
            $this->active_day = $this->start_date;
        }
        else
        {
            $this->start_date = date('Y-m-d', strtotime($this->year.'-01-01'));
            $this->active_day = $this->year.'-01-01';
        }
        
        // We will extend the end date in the loop
        $this->end_date = $this->start_date;
        
        // Return the events
        $this->atts['return_items'] = true;
        
        // Fetch the events
        $this->fetch();
        
        // Return the output
        $output = $this->output();
        
        echo json_encode($output);
        exit;
    }
}