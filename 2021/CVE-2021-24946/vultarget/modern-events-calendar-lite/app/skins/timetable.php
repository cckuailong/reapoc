<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC Timetable class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_timetable extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'timetable';
    public $number_of_days;
    public $number_of_days_modern;
    public $week_start;
    public $start_time;
    public $end_time;

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
        $this->factory->action('wp_ajax_mec_timetable_load_month', array($this, 'load_month'));
        $this->factory->action('wp_ajax_nopriv_mec_timetable_load_month', array($this, 'load_month'));

        $this->factory->action('wp_ajax_mec_weeklyprogram_load', array($this, 'load_weeklyprogram'));
        $this->factory->action('wp_ajax_nopriv_mec_weeklyprogram_load', array($this, 'load_weeklyprogram'));
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
        
        // Generate an ID for the skin
        $this->id = isset($this->atts['id']) ? $this->atts['id'] : mt_rand(100, 999);
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;

        // The style
        $this->style = isset($this->skin_options['style']) ? $this->skin_options['style'] : 'modern';
        if($this->style == 'fluent' and !is_plugin_active('mec-fluent-layouts/mec-fluent-layouts.php')) $this->style = 'modern';
        
        // Next/Previous Month
        $this->next_previous_button = isset($this->skin_options['next_previous_button']) ? $this->skin_options['next_previous_button'] : true;
        
        // HTML class
        $this->html_class = '';
        if(isset($this->atts['html-class']) and trim($this->atts['html-class']) != '') $this->html_class = $this->atts['html-class'];

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

        // Number of Days
        $this->number_of_days = (isset($this->skin_options['number_of_days']) ? $this->skin_options['number_of_days'] : 5);
        $this->number_of_days_modern = (isset($this->skin_options['number_of_days_modern']) ? $this->skin_options['number_of_days_modern'] : 7);

        // First Day of the Week
        $this->week_start = (isset($this->skin_options['week_start']) and trim($this->skin_options['week_start']) != '' and $this->skin_options['week_start'] != '-1') ? $this->skin_options['week_start'] : NULL;

        // Start time - classic view
        $this->start_time = isset($this->skin_options['start_time']) ? $this->skin_options['start_time'] : 1;

        // End time - classic view
        $this->end_time = isset($this->skin_options['end_time']) ? $this->skin_options['end_time'] : 24;
        
        // From Widget
        $this->widget = (isset($this->atts['widget']) and trim($this->atts['widget'])) ? true : false;
        
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
        
        $this->today = $this->year.'-'.$this->month.'-'.$this->day;
        $this->start_date = $this->year.'-'.$this->month.(($this->style == 'clean' || $this->style == 'classic' || $this->style == 'fluent') ? '-'.$this->day : '-01');

        $this->active_date = (strtotime($this->start_date) > strtotime(date('Y-m-d'))) ? $this->start_date : date('Y-m-d');

        // Set the maximum date in current month
        if($this->show_only_expired_events) $this->maximum_date = date('Y-m-d', strtotime('Yesterday'));
        
        // We will extend the end date in the loop
        $this->end_date = $this->start_date;
        
        $this->weeks = $this->main->split_to_weeks($this->start_date, date('Y-m-t', strtotime($this->start_date)));
        
        $this->week_of_days = array();
        foreach($this->weeks as $week_number=>$week) foreach($week as $day) $this->week_of_days[$day] = $week_number;

        // Number of Days
        if($this->style === 'modern' and $this->number_of_days_modern < 7)
        {
            $unset = array();
            $remove = 7 - $this->number_of_days_modern;

            foreach($this->weeks as $w => $week)
            {
                for($i = 1; $i <= $remove; $i++) $unset[] = array_pop($week);
                $this->weeks[$w] = $week;
            }

            // New Active Date
            while(in_array($this->active_date, $unset)) $this->active_date = date('Y-m-d', strtotime('+1 day', strtotime($this->active_date)));
        }
    }
    
    /**
     * Search and returns the filtered events
     * @author Webnus <info@webnus.biz>
     * @return array of objects
     */
    public function search()
    {
        if($this->style == 'clean' || $this->style == 'classic' || $this->style == 'fluent')
        {
            $start = $this->start_date;
            $end = $this->maximum_date ? $this->maximum_date : date('Y-m-t', strtotime($this->start_date));
        }
        else
        {
            $start = $this->main->array_key_first($this->week_of_days);
            $end = $this->maximum_date ? $this->maximum_date : $this->main->array_key_last($this->week_of_days);
        }

        // Date Events
        $dates = $this->period($start, $end);

        if($this->style == 'clean' || $this->style == 'classic' || $this->style == 'fluent')
        {
            $s = $start;
            $sorted = array();
            while(strtotime($s) <= strtotime($end))
            {
                if(isset($dates[$s])) $sorted[$s] = $dates[$s];
                else $sorted[$s] = array();

                $s = date('Y-m-d', strtotime('+1 Day', strtotime($s)));
            }

            $dates = $sorted;
        }

        // Limit
        $this->args['posts_per_page'] = $this->limit;

        $events = array();
        foreach($dates as $date=>$IDs)
        {
            // Check Finish Date
            if(isset($this->maximum_date) and strtotime($date) > strtotime($this->maximum_date)) break;

            // Extending the end date
            $this->end_date = $date;

            // Include Available Events
            $this->args['post__in'] = $IDs;

            // Count of events per day
            $IDs_count = array_count_values($IDs);

            // The Query
            $query = new WP_Query($this->args);
            if(is_array($IDs) and count($IDs) and $query->have_posts())
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

                        $repeat_type = !empty($rendered->meta['mec_repeat_type']) ?  $rendered->meta['mec_repeat_type'] : '';
                        $occurrence = $date;

                        if(strtotime($occurrence) and in_array($repeat_type, array('certain_weekdays', 'custom_days', 'weekday', 'weekend'))) $occurrence = date('Y-m-d', strtotime($occurrence));
                        elseif(strtotime($occurrence)) $occurrence = date('Y-m-d', strtotime('-1 day', strtotime($occurrence)));
                        else $occurrence = NULL;

                        $dates = $this->render->dates(get_the_ID(), $rendered, $this->maximum_dates, $occurrence);

                        $data = new stdClass();
                        $data->ID = $ID;
                        $data->data = $rendered;

                        $data->dates = $dates;
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
            else
            {
                $events[$date] = array();
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
        $date = current_time('Y-m-d');

        // Start of Week
        $week_start = !is_null($this->week_start) ? $this->week_start : $this->main->get_first_day_of_week();

        // Weekdays
        $weekdays = $this->main->get_weekday_labels($week_start);
        
        if(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_current_week')
        {
            if(date('w') == $this->main->get_first_day_of_week()) $date = date('Y-m-d', strtotime('This '.$weekdays[0]));
            else $date = date('Y-m-d', strtotime('Last '.$weekdays[0]));
        }
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_next_week') $date = date('Y-m-d', strtotime('Next '.$weekdays[0]));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_last_week') $date = date('Y-m-d', strtotime('Last '.$weekdays[0]));
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
        if(isset($this->show_only_expired_events) and $this->show_only_expired_events)
        {
            $yesterday = date('Y-m-d', strtotime('Yesterday'));
            if(strtotime($date) > strtotime($yesterday)) $date = $yesterday;
        }

        // Show from start week
        if($this->style == 'clean' || $this->style == 'classic' || $this->style == 'fluent')
        {
            if(date('w', strtotime($date)) == $week_start) $date = date('Y-m-d', strtotime('This '.$weekdays[0], strtotime($date)));
            else $date = date('Y-m-d', strtotime('Last '.$weekdays[0], strtotime($date)));

            $this->maximum_date = date('Y-m-d', strtotime('+'.($this->number_of_days - 1).' days', strtotime($date)));
        }
        
        $time = strtotime($date);
        return array(date('Y', $time), date('m', $time), date('d', $time));
    }
    
    /**
     * Load month for AJAX requert (Modern Style)
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function load_month()
    {
        $this->sf = $this->request->getVar('sf', array());
        $apply_sf_date = $this->request->getVar('apply_sf_date', 1);
        $atts = $this->sf_apply($this->request->getVar('atts', array()), $this->sf, $apply_sf_date);
        
        // Initialize the skin
        $this->initialize($atts);
        
        // Start Date
        $this->year = $this->request->getVar('mec_year', date('Y'));
        $this->month = $this->request->getVar('mec_month', date('m'));
        $this->week = 1;

        // Set MEC Year And Month If Disable Options
        if(!trim($this->year)) $this->year = date('Y');
        if(!trim($this->month)) $this->month = date('m');

        $this->start_date = $this->year.'-'.$this->month.'-01';
        
        // We will extend the end date in the loop
        $this->end_date = $this->start_date;
        
        // Weeks
        $this->weeks = $this->main->split_to_weeks($this->start_date, date('Y-m-t', strtotime($this->start_date)));
        
        // Get week of days
        $this->week_of_days = array();
        foreach($this->weeks as $week_number=>$week) foreach($week as $day) $this->week_of_days[$day] = $week_number;

        // Number of Days
        if($this->style === 'modern' and $this->number_of_days_modern < 7)
        {
            $unset = array();
            $remove = 7 - $this->number_of_days_modern;

            foreach($this->weeks as $w => $week)
            {
                for($i = 1; $i <= $remove; $i++) $unset[] = array_pop($week);
                $this->weeks[$w] = $week;
            }

            // New Active Date
            while(in_array($this->active_date, $unset)) $this->active_date = date('Y-m-d', strtotime('+1 day', strtotime($this->active_date)));
        }
        
        // Some times some months have 6 weeks but next month has 5 or even 4 weeks
        if(!isset($this->weeks[$this->week])) $this->week = $this->week-1;
        if(!isset($this->weeks[$this->week])) $this->week = $this->week-1;
        
        $this->today = $this->weeks[$this->week][0];
        $this->active_date = $this->today;
        
        // Return the events
        $this->atts['return_items'] = true;
        
        // Fetch the events
        $this->fetch();
        
        // Return the output
        $output = $this->output();
        
        echo json_encode($output);
        exit;
    }

    /**
     * Load month for AJAX requert (Clean Style)
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function load_weeklyprogram()
    {
        $this->sf = $this->request->getVar('sf', array());
        $apply_sf_date = $this->request->getVar('apply_sf_date', 1);
        $atts = $this->sf_apply($this->request->getVar('atts', array()), $this->sf, $apply_sf_date);

        // Initialize the skin
        $this->initialize($atts);

        // Start Date
        $this->year = $this->request->getVar('mec_year', date('Y'));
        $this->month = $this->request->getVar('mec_month', date('m'));

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