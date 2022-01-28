<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC map skin class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_map extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'map';
    public $geolocation;
    
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
        $this->factory->action('wp_ajax_mec_map_get_markers', array($this, 'get_markers'));
        $this->factory->action('wp_ajax_nopriv_mec_map_get_markers', array($this, 'get_markers'));
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
        
        // Generate an ID for the sking
        $this->id = isset($this->atts['id']) ? $this->atts['id'] : mt_rand(100, 999);
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;
        
        // HTML class
        $this->html_class = '';
        if(isset($this->atts['html-class']) and trim($this->atts['html-class']) != '') $this->html_class = $this->atts['html-class'];
        
        // From Widget
        $this->widget = (isset($this->atts['widget']) and trim($this->atts['widget'])) ? true : false;
        
        // Init MEC
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
        $this->limit = (isset($this->skin_options['limit']) and trim($this->skin_options['limit'])) ? $this->skin_options['limit'] : 200;
        
        $this->args['posts_per_page'] = $this->limit;
        $this->args['paged'] = $this->paged;
        
        // Sort Options
        $this->args['orderby'] = 'meta_value_num';
        $this->args['order'] = 'ASC';
        $this->args['meta_key'] = 'mec_start_day_seconds';
        
        // Show Past Events
        $this->args['mec-past-events'] = isset($this->atts['show_past_events']) ? $this->atts['show_past_events'] : 0;

        // Geolocation
        $this->geolocation = isset($this->skin_options['geolocation']) ? $this->skin_options['geolocation'] : 0;
        
        // Geolocation Focus
        $this->geolocation_focus = isset($this->skin_options['geolocation_focus']) ? $this->skin_options['geolocation_focus'] : 0;

        // Start Date
        $this->start_date = $this->get_start_date();

        // End Date
        $this->end_date = ((isset($this->atts['date-range-end']) and trim($this->atts['date-range-end'])) ? $this->atts['date-range-end'] : NULL);
        if(!$this->end_date and isset($this->sf['month']) and trim($this->sf['month']) and isset($this->sf['year']) and trim($this->sf['year'])) $this->end_date = date('Y-m-t', strtotime($this->sf['year'].'-'.$this->sf['month'].'-01'));
    }
    
    /**
     * Returns start day of skin for filtering events
     * @author Webnus <info@webnus.biz>
     * @return string
     */
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
        
        return $date;
    }
    
    /**
     * Search and returns the filtered events
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function search()
    {
        $events = array();
        $sorted = array();

        $yesterday = ($this->end_date ? $this->start_date : date('Y-m-d', strtotime('Yesterday', strtotime($this->start_date))));

        // The Query
        $query = new WP_Query($this->args);

        if($query->have_posts())
        {
            // The Loop
            while($query->have_posts())
            {
                $query->the_post();

                $event_id = get_the_ID();
                $rendered = $this->render->data($event_id);

                $data = new stdClass();
                $data->ID = $event_id;
                $data->data = $rendered;
                $data->dates = $this->render->dates($event_id, $rendered, 1, $yesterday);
                $data->date = isset($data->dates[0]) ? $data->dates[0] : array();

                if(strtotime($data->date['end']['date']) < strtotime($this->start_date)) continue;
                if($this->end_date and strtotime($data->date['start']['date']) > strtotime($this->end_date)) continue;

                if($this->hide_time_method == 'end' and strtotime($data->date['end']['date']) < strtotime($this->start_date)) continue;
                elseif($this->hide_time_method != 'end')
                {
                    if((isset($this->atts['show_past_events']) and !$this->atts['show_past_events']) and strtotime($data->date['start']['date']) < strtotime($this->start_date)) continue;
                }

                // Caclculate event start time
                $event_start_time = (isset($data->date['start']) ? strtotime($data->date['start']['date']) : 0) + $rendered->meta['mec_start_day_seconds'];

                // Add the event into the to be sorted array
                if(!isset($sorted[$event_start_time])) $sorted[$event_start_time] = array();
                $sorted[$event_start_time][] = $this->render->after_render($data, $this);
            }

            ksort($sorted, SORT_NUMERIC);
        }

        // Add sorted events to the results
        foreach($sorted as $sorted_events)
        {
            if(!is_array($sorted_events)) continue;
            foreach($sorted_events as $sorted_event) $events[$sorted_event->ID] = $sorted_event;
        }

        // Restore original Post Data
        wp_reset_postdata();
        
        return $events;
    }
    
    /**
     * Get markers for AJAX requert
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function get_markers()
    {
        $this->sf = $this->request->getVar('sf', array());
        $apply_sf_date = $this->request->getVar('apply_sf_date', 1);
        $atts = $this->sf_apply($this->request->getVar('atts', array()), $this->sf, $apply_sf_date);

        // Initialize the skin
        $this->initialize($atts);
        
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