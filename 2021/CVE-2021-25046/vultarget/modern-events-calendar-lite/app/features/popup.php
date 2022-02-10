<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_popup extends MEC_base
{
    public $factory;
    public $main;
    public $settings;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();
        
        // Import MEC Main
        $this->main = $this->getMain();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }
    
    /**
     * Initialize popup feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Shortcode & Event Popup
        $this->factory->action('restrict_manage_posts', array($this, 'add_popup'));

        // Shortcode Save
        $this->factory->action('wp_ajax_mec_popup_shortcode', array($this, 'shortcode_save'));

        // Event Save
        $this->factory->action('wp_ajax_mec_popup_event', array($this, 'event_save'));
        $this->factory->action('wp_ajax_mec_popup_event_category', array($this, 'save_category'));
    }

    public function add_popup($post_type)
    {
        // Shortcode Popup
        if($post_type == $this->main->get_shortcode_post_type())
        {
            $path = MEC::import('app.features.popup.shortcode', true, true);
            include $path;
        }
        //Event Popup
        elseif($post_type == $this->main->get_main_post_type())
        {
            $path = MEC::import('app.features.popup.event', true, true);
            include $path;
        }
    }

    public function shortcode_save()
    {
        // Security Nonce
        $wpnonce = isset($_POST['_mecnonce']) ? $_POST['_mecnonce'] : NULL;

        // Check if our nonce is set.
        if(!trim($wpnonce)) $this->main->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'mec_shortcode_popup')) $this->main->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        $params = (isset($_POST['shortcode']) and is_array($_POST['shortcode'])) ? $_POST['shortcode'] : array();

        $skin = isset($params['skin']) ? $params['skin'] : 'list';
        $title = isset($params['name']) ? $params['name'] : ucwords(str_replace('_', ' ', $skin));

        $show_past_events = isset($params['show_past_events']) ? $params['show_past_events'] : 1;
        $show_only_past_events = isset($params['show_only_past_events']) ? $params['show_only_past_events'] : 0;
        $show_only_ongoing_events = isset($params['show_only_ongoing_events']) ? $params['show_only_ongoing_events'] : 0;

        $sed = isset($params['sed']) ? $params['sed'] : 0;
        $style = isset($params['style']) ? $params['style'] : 'clean';
        $event = isset($params['event']) ? $params['event'] : 0;
        $custom_style = isset($params['custom_style']) ? $params['custom_style'] : '';

        $skin_options = array(
            'list' => array(
                'style' => $style,
                'start_date_type' => 'today',
                'start_date' => '',
                'maximum_date_range' => '',
                'include_events_times' => 0,
                'load_more_button' => 1,
                'month_divider' => 1,
                'map_on_top' => 0,
                'set_geolocation' => 0,
                'toggle_month_divider' => 0,
            ),
            'grid' => array(
                'style' => $style,
                'start_date_type' => 'today',
                'start_date' => '',
                'maximum_date_range' => '',
                'count' => 3,
                'load_more_button' => 1,
                'map_on_top' => 0,
                'set_geolocation' => 0,
            ),
            'agenda' => array(
                'style' => $style,
                'start_date_type' => 'today',
                'start_date' => '',
                'maximum_date_range' => '',
                'month_divider' => 1,
                'load_more_button' => 1,
            ),
            'full_calendar' => array(
                'start_date_type' => 'start_current_month',
                'default_view' => 'list',
                'monthly_style' => $style,
                'list' => 1,
                'yearly' => 0,
                'monthly' => 1,
                'weekly' => 1,
                'daily' => 1,
                'display_price' => 0,
            ),
            'yearly_view' => array(
                'style' => $style,
                'start_date_type' => 'start_current_year',
                'start_date' => '',
                'next_previous_button' => 1,
            ),
            'monthly_view' => array(
                'style' => $style,
                'start_date_type' => 'start_current_month',
                'start_date' => '',
                'next_previous_button' => 1,
            ),
            'map' => array(
                'start_date_type' => 'today',
                'start_date' => '',
                'limit' => 200,
                'geolocation' => 0,
            ),
            'daily_view' => array(
                'start_date_type' => 'today',
                'start_date' => '',
                'next_previous_button' => 1,
            ),
            'weekly_view' => array(
                'start_date_type' => 'start_current_week',
                'start_date' => '',
                'next_previous_button' => 1,
            ),
            'timetable' => array(
                'style' => $style,
                'start_date_type' => 'start_current_week',
                'start_date' => '',
                'number_of_days' => 5,
                'week_start' => -1,
                'start_time' => 8,
                'end_time' => 20,
                'next_previous_button' => 1,
            ),
            'masonry' => array(
                'start_date_type' => 'today',
                'start_date' => '',
                'maximum_date_range' => '',
                'filter_by' => '',
                'fit_to_row' => 0,
                'masonry_like_grid' => 0,
                'load_more_button' => 1,
            ),
            'cover' => array(
                'style' => $style,
                'event_id' => $event,
            ),
            'countdown' => array(
                'style' => $style,
                'event_id' => $event,
            ),
            'available_spot' => array(
                'event_id' => $event,
            ),
            'carousel' => array(
                'style' => $style,
                'start_date_type' => 'today',
                'start_date' => '',
                'count' => 3,
                'autoplay' => 1,
            ),
            'slider' => array(
                'style' => $style,
                'start_date_type' => 'today',
                'start_date' => '',
                'autoplay' => 1,
            ),
            'timeline' => array(
                'start_date_type' => 'today',
                'start_date' => '',
                'maximum_date_range' => '',
                'load_more_button' => 1,
                'month_divider' => 0,
            ),
            'tile' => array(
                'start_date_type' => 'start_current_month',
                'start_date' => '',
                'count' => 4,
                'next_previous_button' => 1,
            ),
            'custom' => array(
                'style' => $custom_style,
            ),
        );

        $sk = isset($skin_options[$skin]) ? $skin_options[$skin] : array('style' => $style, 'start_date_type' => 'today', 'start_date' => '');

        $sk['sed_method'] = $sed;
        $sk['image_popup'] = 0;

        $sf = array();
        $sf_status = 0;
        $sf_display_label = '';

        if($skin == 'full_calendar')
        {
            $sf = array('month_filter'=>array('type'=>'dropdown'), 'text_search'=>array('type'=>'text_input'));
            $sf_status = 1;
        }

        // Create Default Calendars
        $metas = array(
            'label' => '',
            'category' => '',
            'location' => '',
            'organizer' => '',
            'tag' => '',
            'author' => '',
            'skin' => $skin,
            'sk-options' => array(
                $skin => $sk
            ),
            'sf-options' => array($skin => $sf),
            'sf_status' => $sf_status,
            'sf_display_label' => $sf_display_label,
            'show_past_events' => $show_past_events,
            'show_only_past_events' => $show_only_past_events,
            'show_only_ongoing_events' => $show_only_ongoing_events,
        );

        $post = array('post_title'=>$title, 'post_content'=>'', 'post_type'=>'mec_calendars', 'post_status'=>'publish');
        $post_id = wp_insert_post($post);

        foreach($metas as $key=>$value) update_post_meta($post_id, $key, $value);

        $this->main->response(array('success'=>1, 'id'=>$post_id));
    }

    public function event_save()
    {
        // Security Nonce
        $wpnonce = isset($_POST['_mecnonce']) ? $_POST['_mecnonce'] : NULL;

        // Check if our nonce is set.
        if(!trim($wpnonce)) $this->main->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'mec_event_popup')) $this->main->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        $mec = (isset($_POST['mec']) and is_array($_POST['mec'])) ? $_POST['mec'] : array();

        $post_title = isset($mec['title']) ? sanitize_text_field($mec['title']) : '';
        $post_content = isset($mec['content']) ? $mec['content'] : '';
        $featured_image = isset($mec['featured_image']) ? sanitize_text_field($mec['featured_image']) : '';

        // Post Status
        $status = 'pending';
        if(current_user_can('publish_posts')) $status = 'publish';

        $post = array('post_title'=>$post_title, 'post_content'=>$post_content, 'post_type'=>$this->main->get_main_post_type(), 'post_status'=>$status);
        $post_id = wp_insert_post($post);

        // Categories
        $categories = (isset($_POST['tax_input']) and isset($_POST['tax_input']['mec_category']) and is_array($_POST['tax_input']['mec_category'])) ? $_POST['tax_input']['mec_category'] : array();
        wp_set_post_terms($post_id, $categories, 'mec_category');

        // Color
        $color = isset($mec['color']) ? sanitize_text_field(trim($mec['color'], '# ')) : '';
        update_post_meta($post_id, 'mec_color', $color);

        // Featured Image
        if($featured_image) set_post_thumbnail($post_id, $featured_image);

        // Location
        $location_id = isset($mec['location_id']) ? sanitize_text_field($mec['location_id']) : 0;

        // Selected a saved location
        if($location_id)
        {
            // Set term to the post
            wp_set_object_terms($post_id, (int) $location_id, 'mec_location');
        }
        else
        {
            $address = (isset($mec['location']['address']) and trim($mec['location']['address'])) ? sanitize_text_field($mec['location']['address']) : '';
            $name = (isset($mec['location']['name']) and trim($mec['location']['name'])) ? sanitize_text_field($mec['location']['name']) : (trim($address) ? $address : 'Location Name');

            $term = get_term_by('name', $name, 'mec_location');

            // Term already exists
            if(is_object($term) and isset($term->term_id))
            {
                // Set term to the post
                wp_set_object_terms($post_id, (int) $term->term_id, 'mec_location');

                $location_id = (int) $term->term_id;
            }
            else
            {
                $term = wp_insert_term($name, 'mec_location');

                $location_id = $term['term_id'];
                if($location_id)
                {
                    // Set term to the post
                    wp_set_object_terms($post_id, (int) $location_id, 'mec_location');

                    $latitude = (isset($mec['location']['latitude']) and trim($mec['location']['latitude'])) ? sanitize_text_field($mec['location']['latitude']) : 0;
                    $longitude = (isset($mec['location']['longitude']) and trim($mec['location']['longitude'])) ? sanitize_text_field($mec['location']['longitude']) : 0;
                    $thumbnail = (isset($mec['location']['thumbnail']) and trim($mec['location']['thumbnail'])) ? sanitize_text_field($mec['location']['thumbnail']) : '';

                    if(!trim($latitude) or !trim($longitude))
                    {
                        $geo_point = $this->main->get_lat_lng($address);

                        $latitude = $geo_point[0];
                        $longitude = $geo_point[1];
                    }

                    update_term_meta($location_id, 'address', $address);
                    update_term_meta($location_id, 'latitude', $latitude);
                    update_term_meta($location_id, 'longitude', $longitude);
                    update_term_meta($location_id, 'thumbnail', $thumbnail);
                }
                else $location_id = 1;
            }
        }

        update_post_meta($post_id, 'mec_location_id', $location_id);

        $dont_show_map = isset($mec['dont_show_map']) ? sanitize_text_field($mec['dont_show_map']) : 0;
        update_post_meta($post_id, 'mec_dont_show_map', $dont_show_map);

        // Organizer
        $organizer_id = isset($mec['organizer_id']) ? sanitize_text_field($mec['organizer_id']) : 0;

        // Selected a saved organizer
        if($organizer_id)
        {
            // Set term to the post
            wp_set_object_terms($post_id, (int) $organizer_id, 'mec_organizer');
        }
        else
        {
            $name = (isset($mec['organizer']['name']) and trim($mec['organizer']['name'])) ? sanitize_text_field($mec['organizer']['name']) : 'Organizer Name';

            $term = get_term_by('name', $name, 'mec_organizer');

            // Term already exists
            if(is_object($term) and isset($term->term_id))
            {
                // Set term to the post
                wp_set_object_terms($post_id, (int) $term->term_id, 'mec_organizer');
                $organizer_id = (int) $term->term_id;
            }
            else
            {
                $term = wp_insert_term($name, 'mec_organizer');

                $organizer_id = $term['term_id'];
                if($organizer_id)
                {
                    // Set term to the post
                    wp_set_object_terms($post_id, (int) $organizer_id, 'mec_organizer');

                    $tel = (isset($mec['organizer']['tel']) and trim($mec['organizer']['tel'])) ? sanitize_text_field($mec['organizer']['tel']) : '';
                    $email = (isset($mec['organizer']['email']) and trim($mec['organizer']['email'])) ? sanitize_text_field($mec['organizer']['email']) : '';
                    $url = (isset($mec['organizer']['url']) and trim($mec['organizer']['url'])) ? (strpos($mec['organizer']['url'], 'http') === false ? 'http://'.sanitize_text_field($mec['organizer']['url']) : sanitize_text_field($mec['organizer']['url'])) : '';
                    $thumbnail = (isset($mec['organizer']['thumbnail']) and trim($mec['organizer']['thumbnail'])) ? sanitize_text_field($mec['organizer']['thumbnail']) : '';

                    update_term_meta($organizer_id, 'tel', $tel);
                    update_term_meta($organizer_id, 'email', $email);
                    update_term_meta($organizer_id, 'url', $url);
                    update_term_meta($organizer_id, 'thumbnail', $thumbnail);
                }
                else $organizer_id = 1;
            }
        }

        update_post_meta($post_id, 'mec_organizer_id', $organizer_id);

        // Date Options
        $date = isset($mec['date']) ? $mec['date'] : array();

        $start_date = (isset($date['start']['date']) and trim($date['start']['date'])) ? $this->main->standardize_format($date['start']['date']) : date('Y-m-d');
        $end_date = (isset($date['end']['date']) and trim($date['end']['date'])) ? $this->main->standardize_format($date['end']['date']) : date('Y-m-d');

        // Set the start date
        $date['start']['date'] = $start_date;

        $start_time_hour = isset($date['start']) ? $date['start']['hour'] : '8';
        $start_time_minutes = isset($date['start']) ? $date['start']['minutes'] : '00';
        $start_time_ampm = (isset($date['start']) and isset($date['start']['ampm'])) ? $date['start']['ampm'] : 'AM';

        // Fix end_date if it's smaller than start_date
        if(strtotime($end_date) < strtotime($start_date)) $end_date = $start_date;

        // Set the end date
        $date['end']['date'] = $end_date;

        $end_time_hour = isset($date['end']) ? $date['end']['hour'] : '6';
        $end_time_minutes = isset($date['end']) ? $date['end']['minutes'] : '00';
        $end_time_ampm = (isset($date['end']) and isset($date['end']['ampm'])) ? $date['end']['ampm'] : 'PM';

        // If 24 hours format is enabled then convert it back to 12 hours
        if(isset($this->settings['time_format']) and $this->settings['time_format'] == 24)
        {
            if($start_time_hour < 12) $start_time_ampm = 'AM';
            elseif($start_time_hour == 12) $start_time_ampm = 'PM';
            elseif($start_time_hour > 12)
            {
                $start_time_hour -= 12;
                $start_time_ampm = 'PM';
            }
            elseif($start_time_hour == 0)
            {
                $start_time_hour = 12;
                $start_time_ampm = 'AM';
            }

            if($end_time_hour < 12) $end_time_ampm = 'AM';
            elseif($end_time_hour == 12) $end_time_ampm = 'PM';
            elseif($end_time_hour > 12)
            {
                $end_time_hour -= 12;
                $end_time_ampm = 'PM';
            }
            elseif($end_time_hour == 0)
            {
                $end_time_hour = 12;
                $end_time_ampm = 'AM';
            }

            // Set converted values to date array
            $date['start']['hour'] = $start_time_hour;
            $date['start']['ampm'] = $start_time_ampm;

            $date['end']['hour'] = $end_time_hour;
            $date['end']['ampm'] = $end_time_ampm;
        }

        $allday = isset($date['allday']) ? 1 : 0;

        // Set start time and end time if event is all day
        if($allday == 1)
        {
            $start_time_hour = '8';
            $start_time_minutes = '00';
            $start_time_ampm = 'AM';

            $end_time_hour = '6';
            $end_time_minutes = '00';
            $end_time_ampm = 'PM';
        }

        $day_start_seconds = $this->main->time_to_seconds($this->main->to_24hours($start_time_hour, $start_time_ampm), $start_time_minutes);
        $day_end_seconds = $this->main->time_to_seconds($this->main->to_24hours($end_time_hour, $end_time_ampm), $end_time_minutes);

        update_post_meta($post_id, 'mec_start_date', $start_date);
        update_post_meta($post_id, 'mec_start_time_hour', $start_time_hour);
        update_post_meta($post_id, 'mec_start_time_minutes', $start_time_minutes);
        update_post_meta($post_id, 'mec_start_time_ampm', $start_time_ampm);
        update_post_meta($post_id, 'mec_start_day_seconds', $day_start_seconds);

        update_post_meta($post_id, 'mec_end_date', $end_date);
        update_post_meta($post_id, 'mec_end_time_hour', $end_time_hour);
        update_post_meta($post_id, 'mec_end_time_minutes', $end_time_minutes);
        update_post_meta($post_id, 'mec_end_time_ampm', $end_time_ampm);
        update_post_meta($post_id, 'mec_end_day_seconds', $day_end_seconds);

        // Repeat Options
        $repeat = array();
        $repeat_type = NULL;
        $repeat_status = 0;

        $repeat_end = '';
        $repeat_end_at_occurrences = '';
        $repeat_end_at_date = '';

        update_post_meta($post_id, 'mec_date', $date);
        update_post_meta($post_id, 'mec_repeat', $repeat);
        update_post_meta($post_id, 'mec_certain_weekdays', '');
        update_post_meta($post_id, 'mec_allday', $allday);
        update_post_meta($post_id, 'mec_hide_time', 0);
        update_post_meta($post_id, 'mec_hide_end_time', 0);
        update_post_meta($post_id, 'mec_comment', '');
        update_post_meta($post_id, 'mec_repeat_status', $repeat_status);
        update_post_meta($post_id, 'mec_repeat_type', '');
        update_post_meta($post_id, 'mec_repeat_interval', '');
        update_post_meta($post_id, 'mec_repeat_end', $repeat_end);
        update_post_meta($post_id, 'mec_repeat_end_at_occurrences', $repeat_end_at_occurrences);
        update_post_meta($post_id, 'mec_repeat_end_at_date', $repeat_end_at_date);
        update_post_meta($post_id, 'mec_advanced_days', '');

        // Creating $event array for inserting in mec_events table
        $event = array('post_id'=>$post_id, 'start'=>$start_date, 'repeat'=>$repeat_status, 'rinterval'=>NULL, 'time_start'=>$day_start_seconds, 'time_end'=>$day_end_seconds);

        $year = NULL;
        $month = NULL;
        $day = NULL;
        $week = NULL;
        $weekday = NULL;
        $weekdays = NULL;

        $in_days = '';
        $not_in_days = '';

        update_post_meta($post_id, 'mec_in_days', $in_days);
        update_post_meta($post_id, 'mec_not_in_days', $not_in_days);

        // Repeat End Date
        $repeat_end_date = '0000-00-00';

        // Add parameters to the $event
        $event['end'] = $repeat_end_date;
        $event['year'] = $year;
        $event['month'] = $month;
        $event['day'] = $day;
        $event['week'] = $week;
        $event['weekday'] = $weekday;
        $event['weekdays'] = $weekdays;
        $event['days'] = $in_days;
        $event['not_in_days'] = $not_in_days;

        // DB Library
        $db = $this->getDB();

        // Update MEC Events Table
        $mec_event_id = $db->select("SELECT `id` FROM `#__mec_events` WHERE `post_id`='$post_id'", 'loadResult');

        if(!$mec_event_id)
        {
            $q1 = "";
            $q2 = "";

            foreach($event as $key=>$value)
            {
                $q1 .= "`$key`,";

                if(is_null($value)) $q2 .= "NULL,";
                else $q2 .= "'$value',";
            }

            $db->q("INSERT INTO `#__mec_events` (".trim($q1, ', ').") VALUES (".trim($q2, ', ').")", 'INSERT');
        }
        else
        {
            $q = "";

            foreach($event as $key=>$value)
            {
                if(is_null($value)) $q .= "`$key`=NULL,";
                else $q .= "`$key`='$value',";
            }

            $db->q("UPDATE `#__mec_events` SET ".trim($q, ', ')." WHERE `id`='$mec_event_id'");
        }

        // Update Schedule
        $schedule = $this->getSchedule();
        $schedule->reschedule($post_id, $schedule->get_reschedule_maximum($repeat_type));

        // Hourly Schedule Options
        $hourly_schedules = array();
        update_post_meta($post_id, 'mec_hourly_schedules', $hourly_schedules);

        // Booking and Ticket Options
        $booking = array();
        update_post_meta($post_id, 'mec_booking', $booking);

        $tickets = array();
        update_post_meta($post_id, 'mec_tickets', $tickets);

        // Fee options
        $fees_global_inheritance = 1;
        update_post_meta($post_id, 'mec_fees_global_inheritance', $fees_global_inheritance);

        $fees = array();
        update_post_meta($post_id, 'mec_fees', $fees);

        // Ticket Variation options
        $ticket_variations_global_inheritance = 1;
        update_post_meta($post_id, 'mec_ticket_variations_global_inheritance', $ticket_variations_global_inheritance);

        $ticket_variations = array();
        update_post_meta($post_id, 'mec_ticket_variations', $ticket_variations);

        // Registration Fields options
        $reg_fields_global_inheritance = 1;
        update_post_meta($post_id, 'mec_reg_fields_global_inheritance', $reg_fields_global_inheritance);

        $reg_fields = array();
        update_post_meta($post_id, 'mec_reg_fields', $reg_fields);

        // Organizer Payment Options
        $op = array();
        update_post_meta($post_id, 'mec_op', $op);
        update_user_meta(get_post_field('post_author', $post_id), 'mec_op', $op);

        // For Event Notification Badge.
        update_post_meta($post_id, 'mec_event_date_submit', date('YmdHis', current_time('timestamp', 0)));

        do_action('mec_after_publish_admin_event', $post_id, false);

        $this->main->response(array(
            'success' => 1,
            'id' => $post_id,
            'link' => get_post_permalink($post_id),
        ));
    }

    public function save_category()
    {
        $category = isset($_POST['category']) ? $_POST['category'] : '';

        $term = term_exists($category, 'mec_category');
        if(!$term)
        {
            $term = wp_insert_term($category, 'mec_category');
            $category_id = $term['term_id'];
        }
        else $category_id = $term['term_id'];

        $this->main->response(array('success'=>1, 'id'=>$category_id, 'name'=>$category));
    }
}