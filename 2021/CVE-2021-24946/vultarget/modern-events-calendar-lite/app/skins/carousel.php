<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC carousel class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skin_carousel extends MEC_skins
{
    /**
     * @var string
     */
    public $skin = 'carousel';
    public $date_format_type1_1;
    public $date_format_type1_2;
    public $date_format_type1_3;
    public $date_format_type2_1;
    public $date_format_type3_1;
    public $archive_link;
    public $head_text;
    public $autoplay;
    public $autoplay_status;
    public $loop;

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
        
        // Skin Options
        $this->skin_options = (isset($this->atts['sk-options']) and isset($this->atts['sk-options'][$this->skin])) ? $this->atts['sk-options'][$this->skin] : array();
        
        // Date Formats
        $this->date_format_type1_1 = (isset($this->skin_options['type1_date_format1']) and trim($this->skin_options['type1_date_format1'])) ? $this->skin_options['type1_date_format1'] : 'd';
        $this->date_format_type1_2 = (isset($this->skin_options['type1_date_format2']) and trim($this->skin_options['type1_date_format2'])) ? $this->skin_options['type1_date_format2'] : 'F';
        $this->date_format_type1_3 = (isset($this->skin_options['type1_date_format3']) and trim($this->skin_options['type1_date_format3'])) ? $this->skin_options['type1_date_format3'] : 'Y';
        
        $this->date_format_type2_1 = (isset($this->skin_options['type2_date_format1']) and trim($this->skin_options['type2_date_format1'])) ? $this->skin_options['type2_date_format1'] : 'M d, Y';
        $this->date_format_type3_1 = (isset($this->skin_options['type3_date_format1']) and trim($this->skin_options['type3_date_format1'])) ? $this->skin_options['type3_date_format1'] : 'M d, Y';
        
        // Search Form Status
        $this->sf_status = false;
        
        // Generate an ID for the sking
        $this->id = isset($this->atts['id']) ? $this->atts['id'] : mt_rand(100, 999);
        
        // Set the ID
        if(!isset($this->atts['id'])) $this->atts['id'] = $this->id;
        
        // The style
        $this->style = isset($this->skin_options['style']) ? $this->skin_options['style'] : 'type1';
        if($this->style == 'fluent' and !is_plugin_active('mec-fluent-layouts/mec-fluent-layouts.php')) $this->style = 'type1';

        // The archive link
        $this->archive_link = isset($this->skin_options['archive_link']) ? $this->skin_options['archive_link'] : '';

        // The Head text
        $this->head_text = isset($this->skin_options['head_text']) ? $this->skin_options['head_text'] : '';
        
        // Auto Play
        $this->autoplay_status = (!isset($this->skin_options['autoplay_status']) or (isset($this->skin_options['autoplay_status']) and trim($this->skin_options['autoplay_status']))) ? true : false;
        $this->autoplay = (isset($this->skin_options['autoplay']) and trim($this->skin_options['autoplay'])) ? $this->skin_options['autoplay'] : 3000;

        // Loop
        $this->loop = (!isset($this->skin_options['loop_status']) or (isset($this->skin_options['loop_status']) and trim($this->skin_options['loop_status']))) ? true : false;

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

        // reason_for_cancellation
        $this->reason_for_cancellation = isset($this->skin_options['reason_for_cancellation']) ? $this->skin_options['reason_for_cancellation'] : false;

        // Event Times
        $this->include_events_times = isset($this->skin_options['include_events_times']) ? $this->skin_options['include_events_times'] : false;
        $this->localtime = isset($this->skin_options['include_local_time']) ? $this->skin_options['include_local_time'] : false;

        // display_label
        $this->display_label = isset($this->skin_options['display_label']) ? $this->skin_options['display_label'] : false;
        
        // From Widget
        $this->widget = (isset($this->atts['widget']) and trim($this->atts['widget'])) ? true : false;
		if($this->widget)
        {
			$this->skin_options['count'] = '1';
		}
        
        // The count in row
        $this->count = isset($this->skin_options['count']) ? $this->skin_options['count'] : '3';
        
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
        if($this->show_only_expired_events)
        {
            $this->atts['show_past_events'] = '1';
            $this->args['order'] = 'DESC';
        }

        // Show Past Events
        $this->args['mec-past-events'] = isset($this->atts['show_past_events']) ? $this->atts['show_past_events'] : '0';
        
        // Start Date
        $this->start_date = $this->get_start_date();
        
        // We will extend the end date in the loop
        $this->end_date = $this->start_date;
        
        // Apply Maximum Date
        if($this->request->getVar('apply_sf_date', 0) == 1) $this->maximum_date = date('Y-m-t', strtotime($this->start_date));
        
        // Found Events
        $this->found = 0;

        do_action('mec-carousel-initialize-end', $this);
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

        // Show only expired events
        if(isset($this->show_only_expired_events) and $this->show_only_expired_events)
        {
            $now = date('Y-m-d H:i:s', current_time('timestamp', 0));
            if(strtotime($date) > strtotime($now)) $date = $now;
        }
        
        return $date;
    }
}