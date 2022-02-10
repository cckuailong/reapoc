<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC monthly view class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_general_calendar extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'general_calendar';
    public $activate_first_date = false;
    public $activate_current_day = true;
    public $display_all = false;

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
        $this->factory->action('wp_ajax_mec_general_calendar_load_month', array($this, 'load_month'));
        $this->factory->action('wp_ajax_nopriv_mec_general_calendar_load_month', array($this, 'load_month'));
        $this->factory->action( 'rest_api_init', array($this,'mec_general_calendar_get_events_api'));

    }

 
    public function mec_general_calendar_get_events_api(){
        register_rest_route( 'mec/v1', '/events', array(
            'methods' => 'GET',
            'callback' => array($this,'get_custom_users_data'),
        ));
    }

    public function get_custom_users_data($request) {
        // Attributes
        // $atts = array(
        //     'show_past_events'=>1,
        //     'start_date_type'=>'start_last_month',
        //     'show_ongoing_events'=>'1',
        //     'sk-options'=> array(
        //         'general_calendar' => array('limit'=>100)
        //     ),
        // );
        $skin = 'general_calendar';
        // Initialize the skin
        $this->initialize($atts);
        // Fetch the events
        $upcoming_events = $this->get_events($request->get_param('startParam'), $request->get_param('endParam'));
        $upcoming_events_test = $this->get_events('2021-01-01', '2021-12-28');
        $localtime = isset($this->skin_options['include_local_time']) ? $this->skin_options['include_local_time'] : false;
        $events = [];
        foreach($upcoming_events_test as $date => $content)
        {
            $event_a = [];
            foreach($content as $array_id => $event)
            {
                $location_id = $this->main->get_master_location_id($event);
                
                $event_title = $event->data->title;
                $event_link = $event->data->permalink;
                $event_color = '#'.$event->data->color;
                $event_content = $event->data->content;
                $event_date_start = $this->main->date_i18n('c', $event->date['start']['timestamp']);
                $event_date_start_str = $event->date['start']['timestamp'];
                $event_date_end = $this->main->date_i18n('c', $event->date['end']['timestamp']);
                $event_date_end_str = $event->date['end']['timestamp'];
                $event_a['id']=  $event->data->ID;
                $event_a['title']=  $event->data->title;
                $event_a['start']= $event_date_start;
                $event_a['end']= $event_date_end;
                $event_a['startStr']= $event_date_start_str;
                $event_a['endStr']= $event_date_end_str;
                $event_a['url']= $event_link;
                $event_a['backgroundColor']= $event_color;
                $event_a['borderColor']= $event_color;
                $event_a['description']= $event_content;
                $event_a['localtime']= $localtime;
                // $event_a['allDay'] = $event->data->meta['mec_allday'] == '1' ? true : false;
                array_push($events,$event_a);
            }
        }
        $result = array_values(
            array_reduce($events, function($r, $a){
                if (!isset($r[$a['id'] . $a['endStr']])) $r[$a['id'] . $a['endStr']] = $a;
                return $r;
            }, [])
        );
        return $result;
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
        
        // The events
        $this->events_str = '';

        // Generate an ID for the sking
        $this->id = isset($this->atts['id']) ? $this->atts['id'] : mt_rand(100, 999);
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;
        
        // The style
        $this->style = isset($this->skin_options['style']) ? $this->skin_options['style'] : 'modern';
        if($this->style == 'fluent' and !is_plugin_active('mec-fluent-layouts/mec-fluent-layouts.php')) $this->style = 'modern';
        
        // Next/Previous Month
        $this->next_previous_button = isset($this->skin_options['next_previous_button']) ? $this->skin_options['next_previous_button'] : true;

        // Display All Events
        $this->display_all = ((in_array($this->style, array('clean', 'modern')) and isset($this->skin_options['display_all'])) ? (boolean) $this->skin_options['display_all'] : false);

        // Override the style if the style forced by us in a widget etc
        if(isset($this->atts['style']) and trim($this->atts['style']) != '') $this->style = $this->atts['style'];
        
        // HTML class
        $this->html_class = '';
        if(isset($this->atts['html-class']) and trim($this->atts['html-class']) != '') $this->html_class = $this->atts['html-class'];

        // Booking Button
        $this->booking_button = isset($this->skin_options['booking_button']) ? (int) $this->skin_options['booking_button'] : 0;
        
        // SED Method
        $this->sed_method = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '0';

        // reason_for_cancellation
        $this->reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;

        // display_label
        $this->display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;

        // Image popup
        $this->image_popup = isset($this->skin_options['image_popup']) ? $this->skin_options['image_popup'] : '0';
        
        // From Widget
        $this->widget = (isset($this->atts['widget']) and trim($this->atts['widget'])) ? true : false;

        // From Full Calendar
        $this->from_full_calendar = (isset($this->skin_options['from_fc']) and trim($this->skin_options['from_fc'])) ? true : false;

        // Display Price
        $this->display_price = (isset($this->skin_options['display_price']) and trim($this->skin_options['display_price'])) ? true : false;

        // Detailed Time
        $this->display_detailed_time = (isset($this->skin_options['detailed_time']) and trim($this->skin_options['detailed_time'])) ? true : false;
        
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

        // Activate Current Day
        $this->activate_current_day = (!isset($this->skin_options['activate_current_day']) or (isset($this->skin_options['activate_current_day']) and $this->skin_options['activate_current_day']));
        
        $this->start_date = date('Y-m-d', strtotime($this->year.'-'.$this->month.'-'.$this->day));
        $this->active_day = $this->year.'-'.$this->month.'-'.current_time('d');

        if(!$this->activate_current_day and $this->month != current_time('m')) $this->active_day = $this->start_date;
        
        // We will extend the end date in the loop
        $this->end_date = $this->start_date;

        // Activate First Date With Event
        $this->activate_first_date = (isset($this->skin_options['activate_first_date']) and $this->skin_options['activate_first_date']);
    }
    
    /**
     * Search and returns the filtered events
     * @author Webnus <info@webnus.biz>
     * @return array of objects
     */
    public function get_events($start,$end)
    {
        $start = date('Y-m-d', strtotime($start));
        $end = date('Y-m-d', strtotime($end));
        if($this->show_only_expired_events)
        {
            $start = date('Y-m-d H:i:s', current_time('timestamp', 0));
            $end = date('Y-m-d', strtotime('first day of this month'));

            $this->weeks = $this->main->split_to_weeks($end, $start);

            $this->week_of_days = array();
            foreach($this->weeks as $week_number=>$week) foreach($week as $day) $this->week_of_days[$day] = $week_number;

            $end = $this->main->array_key_first($this->week_of_days);
        }

        
        // Date Events
        // $dates = $this->period($start,$end, true);

        // // Limit
        // $this->args['posts_per_page'] = $this->limit;

        // $events = array();
        // foreach($dates as $date=>$IDs)
        // {
        //     // No Event
        //     if(!is_array($IDs) or (is_array($IDs) and !count($IDs)))
        //     {
        //         $events[$date] = array();
        //         continue;
        //     }

        //     // Include Available Events
        //     $this->args['post__in'] = $IDs;

        //     // Count of events per day
        //     $IDs_count = array_count_values($IDs);

        //     // The Query
        //     $query = new WP_Query($this->args);
        //     if($query->have_posts())
        //     {
        //         if(!isset($events[$date])) $events[$date] = array();

        //         if($this->activate_first_date and $this->active_day and strtotime($date) >= current_time('timestamp', 0) and date('m', strtotime($date)) == $this->month)
        //         {
        //             $this->active_day = $date;
        //             $this->activate_first_date = false;
        //         }

        //         // Day Events
        //         $d = array();

        //         // The Loop
        //         while($query->have_posts())
        //         {
        //             $query->the_post();
        //             $ID = get_the_ID();

        //             $ID_count = isset($IDs_count[$ID]) ? $IDs_count[$ID] : 1;
        //             for($i = 1; $i <= $ID_count; $i++)
        //             {
        //                 $rendered = $this->render->data($ID);

        //                 $data = new stdClass();
        //                 $data->ID = $ID;
        //                 $data->data = $rendered;

        //                 $data->date = array
        //                 (
        //                     'start'=>array('date'=>$date),
        //                     'end'=>array('date'=>$this->main->get_end_date($date, $rendered))
        //                 );

        //                 $d[] = $this->render->after_render($data, $this, $i);
        //             }
        //         }

        //         usort($d, array($this, 'sort_day_events'));
        //         $events[$date] = $d;
        //     }

        //     // Restore original Post Data
        //     wp_reset_postdata();
        // }

        // return $events;




        $dates = $this->period($start, $end, true);
        ksort($dates);

        if($this->show_only_expired_events && $this->loadMoreRunning) $this->show_only_expired_events = '1';

        // Limit
        $this->args['posts_per_page'] = $this->limit;

        $i = 0;
        $found = 0;
        $events = array();

        foreach($dates as $date=>$IDs)
        {
            // No Event
            if(!is_array($IDs) or (is_array($IDs) and !count($IDs))) continue;

            // Check Finish Date
            if(isset($this->maximum_date) and strtotime($date) > strtotime($this->maximum_date)) break;

            // Include Available Events
            $this->args['post__in'] = $IDs;

            // Count of events per day
            $IDs_count = array_count_values($IDs);

            // Extending the end date
            $this->end_date = $date;

            // Continue to load rest of events in the first date
            if($i === 0) $this->args['offset'] = $this->offset;
            // Load all events in the rest of dates
            else 
            {
                $this->offset = 0;
                $this->args['offset'] = 0;
            }

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
                        $found++;
                    }

                    if($found >= $this->limit)
                    {
                        // Next Offset
                        $this->next_offset = ($query->post_count-($query->current_post+1)) >= 0 ? ($query->current_post+1)+$this->offset : 0;

                        usort($d, array($this, 'sort_day_events'));
                        $events[$date] = $d;

                        // Restore original Post Data
                        wp_reset_postdata();

                        break 2;
                    }
                }

                usort($d, array($this, 'sort_day_events'));
                $events[$date] = $d;
            }

            // Restore original Post Data
            wp_reset_postdata();
            $i++;
        }

        // Set Offset for Last Page
        if($found < $this->limit)
        {
            // Next Offset
            $this->next_offset = $found + ((isset($date) and $this->start_date === $date) ? $this->offset : 0);
        }

        // Set found events
        $this->found = $found;
        
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
        
        if(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_current_month') $date = date('Y-m-d', strtotime('first day of this month'));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_next_month') $date = date('Y-m-d', strtotime('first day of next month'));
        elseif(isset($this->skin_options['start_date_type']) and $this->skin_options['start_date_type'] == 'start_last_month') $date = date('Y-m-d', strtotime('first day of last month'));
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
     * Load month for AJAX requert
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function load_month()
    {
        $this->sf = $this->request->getVar('sf', array());
        $apply_sf_date = $this->request->getVar('apply_sf_date', 1);
        $atts = $this->sf_apply($this->request->getVar('atts', array()), $this->sf, $apply_sf_date);
        $navigator_click = $this->request->getVar('navigator_click', false);

        // Initialize the skin
        $this->initialize($atts);
        
        // Search Events If Not Found In Current Month 
        $c = 0;
        $break = false;

        do
        {
            if($c > 12) $break = true;
            if($c and !$break)
            {
                if(intval($this->month) == 12)
                {
                    $this->year = intval($this->year)+1;
                    $this->month = '01';
                }

                $this->month = sprintf("%02d", intval($this->month)+1);
            }
           else
            {
                // Start Date
                $this->year = $this->request->getVar('mec_year', current_time('Y'));
                $this->month = $this->request->getVar('mec_month', current_time('m'));
            }

            if($this->show_only_expired_events)
            {
                $this->start_date = date('Y-m-d', strtotime($this->year.'-'.$this->month.'-01'));
                $this->active_day = date('Y-m-t', strtotime($this->year.'-'.$this->month.'-01'));
            }
            else
            {
                $this->start_date = date('Y-m-d', strtotime($this->year.'-'.$this->month.'-01'));

                $day = current_time('d');
                $this->active_day = $this->year.'-'.$this->month.'-'.$day;

                if(!$this->activate_current_day and $this->month != current_time('m')) $this->active_day = $this->start_date;

                // If date is not valid then use the first day of month
                if(!$this->main->validate_date($this->active_day, 'Y-m-d')) $this->active_day = $this->year.'-'.$this->month.'-01';
            }
            
            // We will extend the end date in the loop
            $this->end_date = $this->start_date;
            
            // Return the events
            $this->atts['return_items'] = true;
            
            // Fetch the events
            $this->fetch();
            
            // Break the loop if not resault
            if($break) break;
            if($navigator_click) break;
          
            $c++;
        }

        
        while(!count($this->events));
            return($this->events);
        exit;
    }

    public function day_label($time)
    {
        // No Label when all events is set to display
        if($this->display_all) return '';

        $date_suffix = (isset($this->settings['date_suffix']) && $this->settings['date_suffix'] == '0') ? $this->main->date_i18n('jS', $time) : $this->main->date_i18n('j', $time);

        if($this->main->is_day_first())
        {
            return '<h6 class="mec-table-side-title">'.sprintf(__('Events for %s %s', 'modern-events-calendar-lite'), '<span class="mec-color mec-table-side-day"> '.$date_suffix.'</span>', $this->main->date_i18n('F', $time)).'</h6>';
        }
        else return '<h6 class="mec-table-side-title">'.sprintf(__('Events for %s', 'modern-events-calendar-lite'), $this->main->date_i18n('F', $time)).'</h6><h3 class="mec-color mec-table-side-day"> '.$date_suffix.'</h3>';
    }
}