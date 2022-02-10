<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC skins class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_skins extends MEC_base
{
    /**
     * Default skin
     * @var string
     */
    public $skin = 'list';

    /**
     * @var array
     */
    public $atts = array();

    /**
     * @var array
     */
    public $args = array();

    /**
     * @var int
     */
    public $maximum_dates = 6;

	/**
     * Offset for don't load duplicated events in list/grid views on load more action
     * @var int
     */
	public $offset = 0;

	/**
     * Offset for next load more action
     * @var int
     */
	public $next_offset = 0;

    /**
     * Display Booking Method
     * @var int
     */
    public $booking_button = 0;

    /**
     * Single Event Display Method
     * @var string
     */
    public $sed_method = '0';

    public $factory;
    public $main;
    public $db;
    public $file;
    public $render;
    public $request;
    public $found;
    public $multiple_days_method;
    public $hide_time_method;
    public $skin_options;
    public $style;
    public $show_only_expired_events;
    public $maximum_date_range;
    public $limit;
    public $paged;
    public $start_date;
    public $end_date;
    public $show_ongoing_events;
    public $include_ongoing_events;
    public $maximum_date;
    public $html_class;
    public $sf;
    public $sf_status;
    public $sf_display_label;
    public $sf_reset_button;
    public $sf_refine;
    public $sf_options;
    public $id;
    public $events;
    public $widget;
    public $count;
    public $settings;
    public $layout;
    public $year;
    public $month;
    public $day;
    public $next_previous_button;
    public $active_date;
    public $today;
    public $weeks;
    public $week;
    public $week_of_days;
    public $events_str;
    public $active_day;
    public $load_more_button;
    public $month_divider;
    public $toggle_month_divider;
    public $image_popup;
    public $map_on_top;
    public $geolocation;
    public $geolocation_focus;
    public $include_events_times;
    public $localtime;
    public $reason_for_cancellation;
    public $display_label;
    public $display_price;
    public $display_detailed_time;
    public $cache;
    public $from_full_calendar = false;

    /**
     * Has More Events
     * @var bool
     */
    public $has_more_events = true;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // MEC factory library
        $this->factory = $this->getFactory();

        // MEC main library
        $this->main = $this->getMain();

        // MEC file library
        $this->file = $this->getFile();

        // MEC db library
        $this->db = $this->getDB();

        // MEC render library
        $this->render = $this->getRender();

        // MEC request library
        $this->request = $this->getRequest();

        // MEC Settings
        $this->settings = $this->main->get_settings();

        // Found Events
        $this->found = 0;

        // How to show multiple days events
        $this->multiple_days_method = $this->main->get_multiple_days_method();

        // Hide event on start or on end
        $this->hide_time_method = $this->main->get_hide_time_method();

        // Cache
        $this->cache = $this->getCache();
    }

    /**
     * Registers skin actions into WordPress hooks
     * @author Webnus <info@webnus.biz>
     */
    public function actions()
    {
    }

    /**
     * Loads all skins
     * @author Webnus <info@webnus.biz>
     */
    public function load()
    {
        // MEC add filters
        $this->factory->filter('posts_join', array($this, 'join'), 10, 2);

        $skins = $this->main->get_skins();
        foreach($skins as $skin=>$skin_name)
        {
            $path = MEC::import('app.skins.'.$skin, true, true);
            $skin_path = apply_filters('mec_skin_path', $skin);

            if($skin_path != $skin and $this->file->exists($skin_path)) $path = $skin_path;
            if(!$this->file->exists($path)) continue;

            include_once $path;

            $skin_class_name = 'MEC_skin_'.$skin;

            // Create Skin Object Class
            $SKO = new $skin_class_name();

            // init the actions
            $SKO->actions();
        }

        // Init Single Skin
        include_once MEC::import('app.skins.single', true, true);

        // Register the actions
        $SKO = new MEC_skin_single();
        $SKO->actions();
    }

    /**
     * Get path of one skin file
     * @author Webnus <info@webnus.biz>
     * @param string $file
     * @return string
     */
    public function get_path($file = 'tpl')
    {
        return MEC::import('app.skins.'.$this->skin.'.'.$file, true, true);
    }

    /**
     * Returns path of skin tpl
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_tpl_path()
    {
        $path = $this->get_path('tpl');

        // Apply filters
        $settings = $this->main->get_settings();

        if($this->skin == 'single' and (isset($settings['single_single_style']) and $settings['single_single_style'] == 'fluent')) $filtered_path = apply_filters('mec_get_skin_tpl_path', $this->skin, 'fluent');
        else $filtered_path = apply_filters('mec_get_skin_tpl_path', $this->skin, $this->style);

        if($filtered_path != $this->skin and $this->file->exists($filtered_path)) $path = $filtered_path;

        return $path;
    }

    /**
     * Returns path of skin render file
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function get_render_path()
    {
        $path = $this->get_path('render');

        // Apply filters
        $filtered_path = apply_filters('mec_get_skin_render_path', $this->skin);
        if($filtered_path != $this->skin and $this->file->exists($filtered_path)) $path = $filtered_path;

        return $path;
    }

    /**
     * Returns calendar file path of calendar views
     * @author Webnus <info@webnus.biz>
     * @param string $style
     * @return string
     */
    public function get_calendar_path($style = 'calendar')
    {
        $path = $this->get_path($style);

        // Apply filters
        $filtered_path = apply_filters('mec_get_skin_calendar_path', $this->skin);
        if($filtered_path != $this->skin and $this->file->exists($filtered_path)) $path = $filtered_path;

        return $path;
    }

    /**
     * Generates skin output
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function output()
    {
        if(!$this->main->getPRO() and in_array($this->skin, array('agenda', 'yearly_view', 'timetable', 'masonry', 'map', 'available_spot')))
        {
            return '';
        }

        // Include needed assets for loading single event details page
        if($this->sed_method) $this->main->load_sed_assets();

        ob_start();
        include $this->get_tpl_path();
        return ob_get_clean();
    }

    /**
     * Returns keyword query for adding to WP_Query
     * @author Webnus <info@webnus.biz>
     * @return null|string
     */
    public function keyword_query()
    {
        // Add keyword to filters
        if(isset($this->atts['s']) and trim($this->atts['s']) != '') return $this->atts['s'];
        else return NULL;
    }

    /**
     * Returns taxonomy query for adding to WP_Query
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function tax_query()
    {
        $tax_query = array('relation'=>'AND');

        // Add event label to filter
        if(isset($this->atts['label']) and trim($this->atts['label'], ', ') != '')
        {
            $tax_query[] = array(
                'taxonomy'=>'mec_label',
                'field'=>'term_id',
                'terms'=>explode(',', trim($this->atts['label'], ', '))
            );
        }

        // Add event category to filter
        if(isset($this->atts['category']) and trim($this->atts['category'], ', ') != '')
        {
            $tax_query[] = array(
                'taxonomy'=>'mec_category',
                'field'=>'term_id',
                'terms'=>explode(',', trim($this->atts['category'], ', '))
            );
        }

        // Add event location to filter
        if(isset($this->atts['location']) and trim($this->atts['location'], ', ') != '')
        {
            $tax_query[] = array(
                'taxonomy'=>'mec_location',
                'field'=>'term_id',
                'terms'=>explode(',', trim($this->atts['location'], ', '))
            );
        }

        // Add event address to filter
        if(isset($this->atts['address']) and trim($this->atts['address'], ', ') != '')
        {
            $get_locations_id = $this->get_locations_id($this->atts['address']);
            $tax_query[] = array(
                'taxonomy'=>'mec_location',
                'field'=>'term_id',
                'terms'=>$get_locations_id,
            );
        }

        // Add event organizer to filter
        if(isset($this->atts['organizer']) and trim($this->atts['organizer'], ', ') != '')
        {
            $tax_query[] = array(
                'taxonomy'=>'mec_organizer',
                'field'=>'term_id',
                'terms'=>explode(',', trim($this->atts['organizer'], ', '))
            );
        }

        // Add event speaker to filter
        if(isset($this->atts['speaker']) and trim($this->atts['speaker'], ', ') != '')
        {
            $tax_query[] = array(
                'taxonomy'=>'mec_speaker',
                'field'=>'term_id',
                'terms'=>explode(',', trim($this->atts['speaker'], ', '))
            );
        }

        //Event types
        if(isset($this->atts['event_type']) and trim($this->atts['event_type'], ', ') != '')
        {
            $tax_query[] = array(
                'taxonomy'=>'mec_event_type',
                'field'=>'term_id',
                'terms'=>explode(',', trim($this->atts['event_type'], ', '))
            );
        }

        if(isset($this->atts['event_type_2']) and trim($this->atts['event_type_2'], ', ') != '')
        {
            $tax_query[] = array(
                'taxonomy'=>'mec_event_type_2',
                'field'=>'term_id',
                'terms'=>explode(',', trim($this->atts['event_type_2'], ', '))
            );
        }

        // Add event tags to filter
        if(apply_filters('mec_taxonomy_tag', '') !== 'post_tag' and isset($this->atts['tag']) and trim($this->atts['tag'], ', ') != '')
        {
            if(is_numeric($this->atts['tag']))
            {
                $tax_query[] = array(
                    'taxonomy'=>'mec_tag',
                    'field'=>'term_id',
                    'terms'=>explode(',', trim($this->atts['tag'], ', '))
                );
            }
            else
            {
                $tax_query[] = array(
                    'taxonomy'=>'mec_tag',
                    'field'=>'name',
                    'terms'=>explode(',', trim($this->atts['tag'], ', '))
                );
            }
        }

        $tax_query = apply_filters('mec_map_tax_query', $tax_query, $this->atts);

        return $tax_query;
    }

    /**
     * Returns meta query for adding to WP_Query
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function meta_query()
    {
        $meta_query = array();
        $meta_query['relation'] = 'AND';

        // Event Min Cost
        if(isset($this->atts['cost-min']) and trim($this->atts['cost-min']) != '')
        {
            $meta_query[] = array(
                'key'     => 'mec_cost',
                'value'   => $this->atts['cost-min'],
                'type'    => 'numeric',
                'compare' => '>=',
            );
        }

        // Event Max Cost
        if(isset($this->atts['cost-max']) and trim($this->atts['cost-max']) != '')
        {
            $meta_query[] = array(
                'key'     => 'mec_cost',
                'value'   => $this->atts['cost-max'],
                'type'    => 'numeric',
                'compare' => '<=',
            );
        }

        return apply_filters('mec_map_meta_query', $meta_query, $this->atts);
    }

    /**
     * Returns tag query for adding to WP_Query
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function tag_query()
    {
        $tag = '';

        // Add event tags to filter
        if(isset($this->atts['tag']) and trim($this->atts['tag'], ', ') != '')
        {
            if(is_numeric($this->atts['tag']))
            {
                $term = get_term_by('id', $this->atts['tag'], apply_filters('mec_taxonomy_tag', ''));
                if($term) $tag = $term->slug;
            }
            else
            {
                $tags = explode(',', $this->atts['tag']);
                foreach($tags as $t)
                {
                    $term = get_term_by('name', $t, apply_filters('mec_taxonomy_tag', ''));
                    if($term) $tag .= $term->slug.',';
                }
            }
        }

        return trim($tag, ', ');
    }

    /**
     * Returns author query for adding to WP_Query
     * @author Webnus <info@webnus.biz>
     * @return array
     */
    public function author_query()
    {
        $author = '';

        // Add event authors to filter
        if(isset($this->atts['author']) and trim($this->atts['author'], ', ') != '')
        {
            $author = $this->atts['author'];
        }

        return $author;
    }

    /**
     * Set the current day for filtering events in WP_Query
     * @author Webnus <info@webnus.biz>
     * @param String $today
     * @return void
     */
    public function setToday($today = NULL)
    {
        if(is_null($today)) $today = date('Y-m-d');

        $this->args['mec-today'] = $today;
        $this->args['mec-now'] = strtotime($this->args['mec-today']);

        $this->args['mec-year'] = date('Y', $this->args['mec-now']);
        $this->args['mec-month'] = date('m', $this->args['mec-now']);
        $this->args['mec-day'] = date('d', $this->args['mec-now']);

        $this->args['mec-week'] = (int) ((date('d', $this->args['mec-now']) - 1) / 7) + 1;
        $this->args['mec-weekday'] = date('N', $this->args['mec-now']);
    }

    /**
     * Join MEC table with WP_Query for filtering the events
     * @author Webnus <info@webnus.biz>
     * @param string $join
     * @param object $wp_query
     * @return string
     */
    public function join($join, $wp_query)
    {
        if(is_string($wp_query->query_vars['post_type']) and $wp_query->query_vars['post_type'] == $this->main->get_main_post_type() and $wp_query->get('mec-init', false))
        {
            $join .= $this->db->_prefix(" LEFT JOIN `#__mec_events` AS mece ON #__posts.ID = mece.post_id LEFT JOIN `#__mec_dates` AS mecd ON #__posts.ID = mecd.post_id");
        }

        return $join;
    }

    /**
     * @param string $start
     * @param string $end
     * @param boolean $exclude
     * @return array
     */
    public function period($start, $end, $exclude = false)
    {
        // Search till the end of End Date!
        if(!$this->show_only_expired_events and date('H:i:s', strtotime($end)) == '00:00:00') $end .= ' 23:59:59';

        // Search From last second of start date
        if($this->show_only_expired_events)
        {
            if(date('Y-m-d', strtotime($start)) !== current_time('Y-m-d') and date('H:i:s', strtotime($start)) == '00:00:00') $start .= ' 23:59:59';
            elseif(date('Y-m-d', strtotime($start)) === current_time('Y-m-d') and date('H:i:s', strtotime($start)) == '00:00:00') $start .= ' '.current_time('H:i:s');
        }

        $seconds_start = strtotime($start);
        $seconds_end = strtotime($end);

        $order = "`tstart` ASC";
        $where_OR = "(`tstart`>='".$seconds_start."' AND `tend`<='".$seconds_end."') OR (`tstart`<='".$seconds_end."' AND `tend`>='".$seconds_end."') OR (`tstart`<='".$seconds_start."' AND `tend`>='".$seconds_start."')";
        // (Start: In, Finish: In) OR (Start: Before or In, Finish: After) OR (Start: Before, Finish: In or After)

        if($this->show_only_expired_events)
        {
            $column = 'tstart';

            if($this->hide_time_method == 'plus1') $seconds_start -= 3600;
            elseif($this->hide_time_method == 'plus2') $seconds_start -= 7200;
            elseif($this->hide_time_method == 'plus10') $seconds_start -= 36000;
            elseif($this->hide_time_method == 'end') $column = 'tend';

            $order = "`tstart` DESC";

            $where_OR = "`".$column."`<'".$seconds_start."'";
            if($column != 'tend') $where_OR .= " AND `tend`<'".$seconds_start."'";
        }
        elseif($this->show_ongoing_events)
        {
            if(in_array($this->skin, array('list', 'grid')) && !(strpos($this->style, 'fluent') === false))
            {
                $now = current_time('timestamp', 0);
                if($this->skin_options['start_date_type'] != 'today')
                {
                    $startDateTime = strtotime($this->start_date) + (int) $this->main->get_gmt_offset_seconds();
                    $now = $startDateTime > $now ? $startDateTime : $now;
                }

                $where_OR = "(`tstart`>'".$now."' AND `tend`<='".$seconds_end."')";
            }
            else
            {
                $now = current_time('timestamp', 0);
                $where_OR = "(`tstart`<='".$now."' AND `tend`>='".$now."')";
            }
        }

        $where_AND = '1 AND `public`=1';

        // Exclude Events
        if(isset($this->atts['exclude']) and is_array($this->atts['exclude']) and count($this->atts['exclude'])) $where_AND .= " AND `post_id` NOT IN (".implode(',', $this->atts['exclude']).")";

        // Include Events
        if(isset($this->atts['include']) and is_array($this->atts['include']) and count($this->atts['include'])) $where_AND .= " AND `post_id` IN (".implode(',', $this->atts['include']).")";

        $query = "SELECT * FROM `#__mec_dates` WHERE (".$where_OR.") AND (".$where_AND.") ORDER BY ".$order;
        $mec_dates = $this->db->select($query, 'loadObjectList');

        // Today and Now
        $today = current_time('Y-m-d');
        $now = current_time('timestamp', 0);

        // Midnight Hour
        $midnight_hour = (isset($this->settings['midnight_hour']) and $this->settings['midnight_hour']) ? $this->settings['midnight_hour'] : 0;

        // Local Time Filter
        $local_time_start = NULL;
        $local_time_start_datetime = NULL;
        $local_time_end = NULL;
        $local_time_end_datetime = NULL;

        if(isset($this->atts['time-start']) and trim($this->atts['time-start'])) $local_time_start = $this->atts['time-start'];
        if(isset($this->atts['time-end']) and trim($this->atts['time-end'])) $local_time_end = $this->atts['time-end'];

        // Local Timezone
        $local_timezone = NULL;
        if($local_time_start or $local_time_end)
        {
            $local_timezone = $this->main->get_timezone_by_ip();
            if(!trim($local_timezone)) $local_timezone = $this->main->get_timezone();
        }

        $dates = array();
        foreach($mec_dates as $mec_date)
        {
            $s = strtotime($mec_date->dstart);
            $e = strtotime($mec_date->dend);

            // Skip Events Based on Local Start Time Search
            if($local_time_start)
            {
                $local_time_start_datetime = $mec_date->dstart.' '.$local_time_start;

                // Local Current Time
                $local = new DateTime($local_time_start_datetime, new DateTimeZone($local_timezone));

                $event_timezone = $this->main->get_timezone($mec_date->post_id);
                $local_time_in_event_timezone = $local->setTimezone(new DateTimeZone($event_timezone))->format('Y-m-d H:i:s');

                if(strtotime($local_time_in_event_timezone) > $mec_date->tstart) continue;
            }

            // Skip Events Based on Local End Time Search
            if($local_time_end)
            {
                $local_time_end_datetime = (isset($this->atts['date-range-end']) ? $this->atts['date-range-end'] : $mec_date->dstart).' '.$local_time_end;

                // End Time is Earlier than Start Time so Add 1 Day to the End Date
                if($local_time_start_datetime and strtotime($local_time_end_datetime) <= strtotime($local_time_start_datetime)) $local_time_end_datetime = date('Y-m-d', strtotime('+1 Day', strtotime($mec_date->dend))).' '.$local_time_end;

                // Local Current Time
                $local = new DateTime($local_time_end_datetime, new DateTimeZone($local_timezone));

                $event_timezone = $this->main->get_timezone($mec_date->post_id);
                $local_time_in_event_timezone = $local->setTimezone(new DateTimeZone($event_timezone))->format('Y-m-d H:i:s');

                if(strtotime($local_time_in_event_timezone) < $mec_date->tend) continue;
            }

            // Hide Events Based on Start Time
            if(!$this->include_ongoing_events and !$this->show_ongoing_events and !$this->show_only_expired_events and !$this->args['mec-past-events'] and $s <= strtotime($today))
            {
                if($this->hide_time_method == 'start' and $now >= $mec_date->tstart) continue;
                elseif($this->hide_time_method == 'plus1' and $now >= $mec_date->tstart+3600) continue;
                elseif($this->hide_time_method == 'plus2' and $now >= $mec_date->tstart+7200) continue;
                elseif($this->hide_time_method == 'plus10' and $now >= $mec_date->tstart+36000) continue;
            }

            // Hide Events Based on End Time
            if(!$this->show_only_expired_events and !$this->args['mec-past-events'] and $e <= strtotime($today))
            {
                if($this->hide_time_method == 'end' and $now >= $mec_date->tend) continue;
            }

            if(($this->multiple_days_method == 'first_day' or ($this->multiple_days_method == 'first_day_listgrid' and in_array($this->skin, array('list', 'grid', 'slider', 'carousel', 'agenda', 'tile')))))
            {
                // Hide Shown Events on AJAX
                if(defined('DOING_AJAX') and DOING_AJAX and $s != $e and $s < strtotime($start) and !$this->show_only_expired_events) continue;

                $d = date('Y-m-d', $s);

                if(!isset($dates[$d])) $dates[$d] = array();
                $dates[$d][] = $mec_date->post_id;
            }
            else
            {
                $diff = $this->main->date_diff($mec_date->dstart, $mec_date->dend);
                $days_long = (isset($diff->days) and !$diff->invert) ? $diff->days : 0;

                while($s <= $e)
                {
                    if((!$this->show_only_expired_events and $seconds_start <= $s and $s <= $seconds_end) or ($this->show_only_expired_events and $seconds_start >= $s and $s >= $seconds_end))
                    {
                        $d = date('Y-m-d', $s);
                        if(!isset($dates[$d])) $dates[$d] = array();

                        // Check for exclude events
                        if($exclude)
                        {
                            $current_id = !isset($current_id) ? 0 : $current_id;

                            if(!isset($not_in_day))
                            {
                                $query = "SELECT `post_id`,`not_in_days` FROM `#__mec_events`";
                                $not_in_day =  $this->db->select($query);
                            }

                            if(array_key_exists($mec_date->post_id, $not_in_day) and trim($not_in_day[$mec_date->post_id]->not_in_days))
                            {
                                $days =  $not_in_day[$mec_date->post_id]->not_in_days;
                                $current_id = $mec_date->post_id;
                            }
                            else $days = '';

                            if(strpos($days, $d) === false)
                            {
                                $midnight = $s+(3600*$midnight_hour);
                                if($days_long == '1' and $midnight >= $mec_date->tend) break;

                                $dates[$d][] = $mec_date->post_id;
                            }
                        }
                        else
                        {
                            $midnight = $s+(3600*$midnight_hour);
                            if($days_long == '1' and $midnight >= $mec_date->tend) break;

                            $dates[$d][] = $mec_date->post_id;
                        }
                    }

                    $s += 86400;
                }
            }
        }

        // Show only one occurrence of events
        $first_event = $this->db->select("SELECT `post_id`, `tstart` FROM `#__mec_dates` WHERE `tstart` >= {$now} AND `tstart` <= {$seconds_end} ORDER BY `tstart` ASC");

        // Force to Show Only Once Occurrence Based on Shortcode Options
        $shortcode_display_one_occurrence = (isset($this->atts['show_only_one_occurrence']) ? (boolean) $this->atts['show_only_one_occurrence'] : false);

        $did_one_occurrence = array();
        foreach($dates as $date => $event_ids)
        {
            if(!is_array($event_ids) or (is_array($event_ids) and !count($event_ids))) continue;

            foreach($event_ids as $index => $event_id)
            {
                $one_occurrence = get_post_meta($event_id, 'one_occurrence', true);
                if($one_occurrence != '1' and !$shortcode_display_one_occurrence) continue;

                if(isset($first_event[$event_id]->tstart) and date('Y-m-d', strtotime($date)) != date('Y-m-d', $first_event[$event_id]->tstart))
                {
                    $dates[$date][$index] = '';
                }
                else
                {
                    if(in_array($event_id, $did_one_occurrence)) $dates[$date][$index] = '';
                    else $did_one_occurrence[] = $event_id;
                }
            }
        }

        return $dates;
    }

    /**
     * Perform the search
     * @author Webnus <info@webnus.biz>
     * @return array of objects \stdClass
     */
    public function search()
    {
        global $MEC_Events_dates;
        if($this->show_only_expired_events)
        {
            $apply_sf_date = $this->request->getVar('apply_sf_date', 1);
            $start = ((isset($this->sf) || $this->request->getVar('sf', array())) and $apply_sf_date) ? date('Y-m-t', strtotime($this->start_date)) : $this->start_date;

            $end = date('Y-m-01', strtotime('-15 Years', strtotime($start)));

            if(isset($this->maximum_date_range) and trim($this->maximum_date_range)) $this->maximum_date_range = $start;
        }
        else
        {
            $start = $this->start_date;
            $end = date('Y-m-t', strtotime('+15 Years', strtotime($start)));
        }

        // Set a certain maximum date from shortcode page.
        if(trim($this->maximum_date) == '' and (isset($this->maximum_date_range) and trim($this->maximum_date_range))) $this->maximum_date = $this->maximum_date_range;

        // Date Events
        $dates = $this->period($start, $end, true);

        // Limit
        $this->args['posts_per_page'] = apply_filters('mec_skins_search_posts_per_page', 100);
        $dates = apply_filters('mec_event_dates_search', $dates, $start, $end, $this);

        $last_timestamp = NULL;
        $last_event_id = NULL;

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
            if($i === 0 and $this->start_date === $date) $this->args['offset'] = $this->offset;
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

                        $event_data = $this->render->after_render($data, $this, $i);
                        $date_times = array(
                            'start'=>array(
                                'date'=> $event_data->date['start']['date'],
                                'time' => $event_data->data->time['start'],
                                'timestamp' => $event_data->data->time['start_timestamp'],
                            ),
                            'end'=>array(
                                'date'=> $event_data->date['end']['date'],
                                'time' => $event_data->data->time['end'],
                                'timestamp' => $event_data->data->time['end_timestamp'],
                            )
                        );

                        $primary_key = $event_data->data->time['start_timestamp'];

                        $last_timestamp = $event_data->data->time['start_timestamp'];
                        $last_event_id = $ID;

                        // global variable for use dates
                        $MEC_Events_dates[$ID][$primary_key] = $date_times;

                        $d[] = $event_data;
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

        // Has More Events
        if($last_timestamp and $last_event_id) $this->has_more_events = (boolean) $this->db->select("SELECT COUNT(id) FROM `#__mec_dates` WHERE `tstart` >= ".$last_timestamp." AND `post_id`!='".$last_event_id."'", 'loadResult');

        return $events;
    }

    /**
     * Run the search command
     * @author Webnus <info@webnus.biz>
     * @return array of objects
     */
    public function fetch()
    {
        // Events! :)
        return $this->events = $this->search();
    }

    /**
     * Draw Monthly Calendar
     * @author Webnus <info@webnus.biz>
     * @param string|int $month
     * @param string|int $year
     * @param array $events
     * @param string $style
     * @return string
     */
    public function draw_monthly_calendar($year, $month, $events = array(), $style = 'calendar')
    {
        $calendar_path = $this->get_calendar_path($style);

        // Generate Month
        ob_start();
        include $calendar_path;
        return ob_get_clean();
    }

    /**
     * @param object $event
     * @return string
     */
    public function get_event_classes($event)
    {
        // Labels are not set
        if(!isset($event->data) or (isset($event->data) and !isset($event->data->labels))) return NULL;

        // No Labels
        if(!is_array($event->data->labels) or (is_array($event->data->labels) and !count($event->data->labels))) return NULL;

        $classes = '';
        foreach($event->data->labels as $label)
        {
            if(!isset($label['style']) or (isset($label['style']) and !trim($label['style']))) continue;
            $classes .= ' '.$label['style'];
        }

        return trim($classes);
    }

    /**
     * Generates Search Form
     * @author Webnus <info@webnus.biz>
     * @return string
     */
    public function sf_search_form()
    {
        // If no fields specified
        if(!count($this->sf_options)) return '';

        $display_style = $fields = $end_div = '';
        $first_row = 'not-started';
        $display_form = array();

        foreach($this->sf_options as $field=>$options)
        {
            $display_form[] = (isset($options['type']) ? $options['type'] : NULL);
            $fields_array = array('category', 'location', 'organizer', 'speaker', 'tag', 'label');
            $fields_array = apply_filters('mec_filter_fields_search_array', $fields_array);

            if(in_array($field, $fields_array) and $first_row == 'not-started')
            {
                $first_row = 'started';
                if($this->sf_options['category']['type'] != 'dropdown' and $this->sf_options['category']['type'] != 'checkboxes' and $this->sf_options['location']['type'] != 'dropdown' and $this->sf_options['organizer']['type'] != 'dropdown' and (isset($this->sf_options['speaker']['type']) && $this->sf_options['speaker']['type'] != 'dropdown') and (isset($this->sf_options['tag']['type']) && $this->sf_options['tag']['type'] != 'dropdown') and  $this->sf_options['label']['type'] != 'dropdown')
                {
                    $display_style = 'style="display: none;"';
                }

                $fields .= '<div class="mec-dropdown-wrap" ' . $display_style . '>';
            }

            if(!in_array($field, $fields_array) and $first_row == 'started')
            {
                $first_row = 'finished';
                $fields .= '</div>';
            }

            $fields .= $this->sf_search_field($field, $options, $this->sf_display_label);
        }

        $fields = apply_filters('mec_filter_fields_search_form', $fields, $this);

        $form = '';
        if(trim($fields) && (in_array('dropdown', $display_form) || in_array('checkboxes', $display_form) || in_array('text_input', $display_form) || in_array('address_input', $display_form) || in_array('minmax', $display_form) || in_array('local-time-picker', $display_form)))
        {
            $form .= '<div id="mec_search_form_'.$this->id.'" class="mec-search-form mec-totalcal-box">';
            $form .= $fields;

            // Reset Button
            if($this->sf_reset_button) $form .='<div class="mec-search-reset-button"><button class="button mec-button" id="mec_search_form_'.$this->id.'_reset" type="button">'.esc_html__('Reset', 'modern-events-calendar-lite').'</button></div>';


            $form = apply_filters('mec_sf_search_form_end', $form, $this );

            $form .= '</div>';
        }

        return apply_filters('mec_sf_search_form', $form, $this );
    }

    /**
     * Generate a certain search field
     * @author Webnus <info@webnus.biz>
     * @param string $field
     * @param array $options
     * @param int $display_label
     * @return string
     */
    public function sf_search_field($field, $options , $display_label = null)
    {
        $type = isset($options['type']) ? $options['type'] : '';

        // Field is disabled
        if(!trim($type)) return '';

        // Status of Speakers Feature
        $speakers_status = (!isset($this->settings['speakers_status']) or (isset($this->settings['speakers_status']) and !$this->settings['speakers_status'])) ? false : true;

        // Import
        self::import('app.libraries.walker');
        if(!function_exists('wp_terms_checklist')) include ABSPATH.'wp-admin/includes/template.php';

        $output = '';
        if($field == 'category')
        {
            $label = $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite'));

            if($type == 'dropdown')
            {
                $output .='<div class="mec-dropdown-search">';
                $display_label == 1 ? $output .= '<label for="mec_sf_category_'.$this->id.'">'.$label.': </label>' : null;

                $output .='<i class="mec-sl-folder"></i>';
                $output .= wp_dropdown_categories(array
                (
                    'echo'=>false,
                    'taxonomy'=>'mec_category',
                    'name'=>' ',
                    'include'=>((isset($this->atts['category']) and trim($this->atts['category'])) ? $this->atts['category'] : ''),
                    'id'=>'mec_sf_category_'.$this->id,
                    'hierarchical'=>true,
                    'show_option_none'=>$label,
                    'option_none_value'=>'',
                    'selected'=>(isset($this->atts['category']) ? $this->atts['category'] : ''),
                    'orderby'=>'name',
                    'order'=>'ASC',
                    'show_count'=>0,
                ));

                $output .= '</div>';
            }
            elseif($type == 'checkboxes' and wp_count_terms(array('taxonomy' => 'mec_category')))
            {
                $output .= '<div class="mec-checkboxes-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_category_'.$this->id.'">'.$label.': </label>' : null;
                $output .='<i class="mec-sl-folder"></i>';

                $selected = ((isset($this->atts['category']) and trim($this->atts['category'])) ? explode(',', trim($this->atts['category'], ', ')) : array());

                $output .= '<div class="mec-searchbar-category-wrap">';
                $output .= '<div id="mec_sf_category_'.$this->id.'">';
                $output .= wp_terms_checklist(0, array
                (
                    'echo'=>false,
                    'taxonomy'=>'mec_category',
                    'selected_cats'=>$selected,
                    'checked_ontop'=>false,
                    'walker'=>(new MEC_walker(array(
                        'include'=>$selected,
                        'id' => $this->id,
                    ))),
                ));

                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';
            }
        }
        elseif($field == 'location')
        {
            $label = $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite'));

            if($type == 'dropdown')
            {
                $output .= '<div class="mec-dropdown-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_location_'.$this->id.'">'.$label.': </label>' : null;

                $output .= '<i class="mec-sl-location-pin"></i>';
                $output .= wp_dropdown_categories(array
                (
                    'echo'=>false,
                    'taxonomy'=>'mec_location',
                    'name'=>' ',
                    'include'=>((isset($this->atts['location']) and trim($this->atts['location'])) ? $this->atts['location'] : ''),
                    'id'=>'mec_sf_location_'.$this->id,
                    'hierarchical'=>true,
                    'show_option_none'=>$label,
                    'option_none_value'=>'',
                    'selected'=>(isset($this->atts['location']) ? $this->atts['location'] : ''),
                    'orderby'=>'name',
                    'order'=>'ASC',
                    'show_count'=>0,
                ));

                $output .= '</div>';
            }
        }
        elseif($field == 'organizer')
        {
            $label = $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite'));

            if($type == 'dropdown')
            {
                $output .= '<div class="mec-dropdown-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_organizer_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-user"></i>';

                $output .= wp_dropdown_categories(array
                (
                    'echo'=>false,
                    'taxonomy'=>'mec_organizer',
                    'name'=>' ',
                    'include'=>((isset($this->atts['organizer']) and trim($this->atts['organizer'])) ? $this->atts['organizer'] : ''),
                    'id'=>'mec_sf_organizer_'.$this->id,
                    'hierarchical'=>true,
                    'show_option_none'=>$label,
                    'option_none_value'=>'',
                    'selected'=>(isset($this->atts['organizer']) ? $this->atts['organizer'] : ''),
                    'orderby'=>'name',
                    'order'=>'ASC',
                    'show_count'=>0,
                ));

                $output .= '</div>';
            }
        }
        elseif($field == 'speaker' and $speakers_status)
        {
            $label = $this->main->m('taxonomy_speaker', __('Speaker', 'modern-events-calendar-lite'));

            if($type == 'dropdown')
            {
                $output .= '<div class="mec-dropdown-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_speaker_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-microphone"></i>';

                $output .= wp_dropdown_categories(array
                (
                    'echo'=>false,
                    'taxonomy'=>'mec_speaker',
                    'name'=>' ',
                    'include'=>((isset($this->atts['speaker']) and trim($this->atts['speaker'])) ? $this->atts['speaker'] : ''),
                    'id'=>'mec_sf_speaker_'.$this->id,
                    'hierarchical'=>true,
                    'show_option_none'=>$label,
                    'option_none_value'=>'',
                    'selected'=>(isset($this->atts['speaker']) ? $this->atts['speaker'] : ''),
                    'orderby'=>'name',
                    'order'=>'ASC',
                    'show_count'=>0,
                ));

                $output .= '</div>';
            }
        }
        elseif($field == 'tag')
        {
            $label = $this->main->m('taxonomy_tag', __('Tag', 'modern-events-calendar-lite'));

            if($type == 'dropdown')
            {
                $output .= '<div class="mec-dropdown-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_tag_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-tag"></i>';

                $output .= wp_dropdown_categories(array
                (
                    'echo'=>false,
                    'taxonomy'=>apply_filters('mec_taxonomy_tag', ''),
                    'name'=>' ',
                    'id'=>'mec_sf_tag_'.$this->id,
                    'hierarchical'=>true,
                    'show_option_none'=>$label,
                    'option_none_value'=>'',
                    'selected'=>(isset($this->atts['tag']) ? $this->atts['tag'] : ''),
                    'orderby'=>'name',
                    'order'=>'ASC',
                    'show_count'=>0,
                ));

                $output .= '</div>';
            }
        }
        elseif($field == 'label')
        {
            $label = $this->main->m('taxonomy_label', __('Label', 'modern-events-calendar-lite'));

            if($type == 'dropdown')
            {
                $output .= '<div class="mec-dropdown-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_label_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-pin"></i>';

                $output .= wp_dropdown_categories(array
                (
                    'echo'=>false,
                    'taxonomy'=>'mec_label',
                    'name'=>' ',
                    'include'=>((isset($this->atts['label']) and trim($this->atts['label'])) ? $this->atts['label'] : ''),
                    'id'=>'mec_sf_label_'.$this->id,
                    'hierarchical'=>true,
                    'show_option_none'=>$label,
                    'option_none_value'=>'',
                    'selected'=>(isset($this->atts['label']) ? $this->atts['label'] : ''),
                    'orderby'=>'name',
                    'order'=>'ASC',
                    'show_count'=>0,
                ));

                $output .= '</div>';
            }
        }
        elseif($field == 'month_filter')
        {
            $label = __('Date', 'modern-events-calendar-lite');
            if($type == 'dropdown')
            {
                $time = isset($this->start_date) ? strtotime($this->start_date) : '';
                $now = current_time('timestamp', 0);

                $skins = array('list', 'grid', 'agenda', 'map');
                if(isset($this->skin_options['default_view']) and $this->skin_options['default_view'] == 'list') array_push($skins, 'full_calendar');

                $item = __('Select', 'modern-events-calendar-lite');
                $option = in_array($this->skin, $skins) ? '<option class="mec-none-item" value="none" selected="selected">'.$item.'</option>' : '';

                $output .= '<div class="mec-date-search"><input type="hidden" id="mec-filter-none" value="'.$item.'">';
                $display_label == 1 ? $output .='<label for="mec_sf_category_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-calendar"></i>
                    <select id="mec_sf_month_'.$this->id.'">
                        <option value="">'.__('Select Month','modern-events-calendar-lite').'</option>';

                $output .= $option;
                $Y = date('Y', $time);

                for($i = 1; $i <= 12; $i++)
                {
                    $output .= '<option value="'.($i < 10 ? '0'.$i : $i).'">'.$this->main->date_i18n('F', mktime(0, 0, 0, $i, 10)).'</option>';
                }

                $output .= '</select>';
                $output .= '<select id="mec_sf_year_'.$this->id.'">'.$option;

                $start_year = $min_start_year = $this->db->select("SELECT MIN(cast(meta_value as unsigned)) AS date FROM `#__postmeta` WHERE `meta_key`='mec_start_date'", 'loadResult');
                $end_year = $max_end_year = $this->db->select("SELECT MAX(cast(meta_value as unsigned)) AS date FROM `#__postmeta` WHERE `meta_key`='mec_end_date'", 'loadResult');

                if(!trim($start_year)) $start_year = date('Y', strtotime('-4 Years', $time));
                if(!trim($end_year) or $end_year < date('Y', strtotime('+4 Years', $time))) $end_year = date('Y', strtotime('+4 Years', $time));

                if(!isset($this->atts['show_past_events']) or (isset($this->atts['show_past_events']) and !$this->atts['show_past_events']))
                {
                    $start_year = $Y;
                    $end_year = date('Y', strtotime('+8 Years', $time));
                }

                if(isset($this->show_only_expired_events) and $this->show_only_expired_events)
                {
                    $start_year = $min_start_year;
                    $end_year = $Y;
                }

                for($i = $start_year; $i <= $end_year; $i++)
                {
                    $selected = (!in_array($this->skin, $skins) and $i == date('Y', $now)) ? 'selected="selected"' : '';
                    $output .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                }

                $output .= '</select></div>';
            }
            elseif($type == 'date-range-picker')
            {
                $min_date = (isset($this->start_date) ? $this->start_date : NULL);

                $output .= '<div class="mec-date-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_date_start_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-calendar"></i>
                    <input class="mec-col-3 mec_date_picker_dynamic_format_start" data-min="'.$min_date.'" type="text"
                           id="mec_sf_date_start_'.$this->id.'"
                           placeholder="'.esc_attr__('Start', 'modern-events-calendar-lite').'" autocomplete="off">
                    <input class="mec-col-3 mec_date_picker_dynamic_format_end" type="text"
                           id="mec_sf_date_end_'.$this->id.'"
                           placeholder="'.esc_attr__('End', 'modern-events-calendar-lite').'" autocomplete="off">
                </div>';
            }
        }
        elseif($field == 'time_filter')
        {
            $label = __('Time', 'modern-events-calendar-lite');
            if($type == 'local-time-picker')
            {
                $this->main->load_time_picker_assets();

                $output .= '<div class="mec-time-picker-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_timepicker_start_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-clock"></i>
                    <input type="text" class="mec-timepicker-start" id="mec_sf_timepicker_start_'.$this->id.'" placeholder="'.__('Start Time', 'modern-events-calendar-lite').'" data-format="'.$this->main->get_hour_format().'" />
                    <input type="text" class="mec-timepicker-end" id="mec_sf_timepicker_end_'.$this->id.'" placeholder="'.__('End Time', 'modern-events-calendar-lite').'" data-format="'.$this->main->get_hour_format().'" />
                </div>';
            }
        }
        elseif($field == 'text_search')
        {
            $label = __('Text', 'modern-events-calendar-lite');
            if($type == 'text_input')
            {
                $placeholder = (isset($options['placeholder']) ? $options['placeholder'] : '');

                $output .= '<div class="mec-text-input-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_s_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-magnifier"></i>
                    <input type="search" value="'.(isset($this->atts['s']) ? $this->atts['s'] : '').'" id="mec_sf_s_'.$this->id.'" placeholder="'.esc_attr($placeholder).'" />
                </div>';
            }
        }
        elseif($field == 'address_search')
        {
            $label = __('Address', 'modern-events-calendar-lite');
            if($type == 'address_input')
            {
                $placeholder = (isset($options['placeholder']) ? $options['placeholder'] : '');

                $output .= '<div class="mec-text-address-search">';
                $display_label == 1 ? $output .='<label for="mec_sf_address_s_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-map"></i>
                    <input type="search" value="'.(isset($this->atts['address']) ? $this->atts['address'] : '').'" id="mec_sf_address_s_'.$this->id.'" placeholder="'.esc_attr($placeholder).'" />
                </div>';
            }
        }
        elseif($field == 'event_cost')
        {
            $label = __('Cost', 'modern-events-calendar-lite');
            if($type == 'minmax')
            {
                $output .= '<div class="mec-minmax-event-cost">';
                $display_label == 1 ? $output .='<label for="mec_sf_event_cost_min_'.$this->id.'">'.$label.': </label>' : null;
                $output .= '<i class="mec-sl-credit-card"></i>
                    <input type="number" min="0" step="0.01" value="'.(isset($this->atts['event-cost-min']) ? $this->atts['event-cost-min'] : '').'" id="mec_sf_event_cost_min_'.$this->id.'" class="mec-minmax-price" placeholder="'.esc_attr__('Min Price', 'modern-events-calendar-lite').'" />
                    <input type="number" min="0" step="0.01" value="'.(isset($this->atts['event-cost-max']) ? $this->atts['event-cost-max'] : '').'" id="mec_sf_event_cost_max_'.$this->id.'" class="mec-minmax-price" placeholder="'.esc_attr__('Max Price', 'modern-events-calendar-lite').'" />
                </div>';
            }
        }

        return apply_filters('mec_search_fields_to_box', $output, $field, $type, $this->atts, $this->id);
    }

    public function sf_apply($atts, $sf = array(), $apply_sf_date = 1)
    {
        // Return normal atts if sf is empty
        if(!count($sf)) return $atts;

        // Apply Text Search Query
        if(isset($sf['s'])) $atts['s'] = $sf['s'];

        // Apply Address Search Query
        if(isset($sf['address'])) $atts['address'] = $sf['address'];

        // Apply Category Query
        if(isset($sf['category']) and trim($sf['category'])) $atts['category'] = $sf['category'];

        // Apply Location Query
        if(isset($sf['location']) and trim($sf['location'])) $atts['location'] = $sf['location'];

        // Apply Organizer Query
        if(isset($sf['organizer']) and trim($sf['organizer'])) $atts['organizer'] = $sf['organizer'];

        // Apply speaker Query
        if(isset($sf['speaker']) and trim($sf['speaker'])) $atts['speaker'] = $sf['speaker'];

        // Apply tag Query
        if(isset($sf['tag']) and trim($sf['tag'])) $atts['tag'] = $sf['tag'];

        // Apply Label Query
        if(isset($sf['label']) and trim($sf['label'])) $atts['label'] = $sf['label'];

        // Apply Event Cost Query
        if(isset($sf['cost-min'])) $atts['cost-min'] = $sf['cost-min'];
        if(isset($sf['cost-max'])) $atts['cost-max'] = $sf['cost-max'];

        // Apply Local Time Query
        if(isset($sf['time-start'])) $atts['time-start'] = $sf['time-start'];
        if(isset($sf['time-end'])) $atts['time-end'] = $sf['time-end'];

        // Apply SF Date or Not
        if($apply_sf_date == 1)
        {
            // Apply Month of Month Filter
            if(isset($sf['month']) and trim($sf['month'])) $this->request->setVar('mec_month', $sf['month']);

            // Apply Year of Month Filter
            if(isset($sf['year']) and trim($sf['year'])) $this->request->setVar('mec_year', $sf['year']);

            // Apply to Start Date
            if(isset($sf['month']) and trim($sf['month']) and isset($sf['year']) and trim($sf['year']))
            {
                $start_date = $sf['year'].'-'.$sf['month'].'-'.(isset($sf['day']) ? $sf['day'] : '01');
                $this->request->setVar('mec_start_date', $start_date);

                $skins = $this->main->get_skins();
                foreach($skins as $skin=>$label)
                {
                    $atts['sk-options'][$skin]['start_date_type'] = 'date';
                    $atts['sk-options'][$skin]['start_date'] = $start_date;
                }
            }

            // Apply Start and End Dates
            if(isset($sf['start']) and trim($sf['start']) and isset($sf['end']) and trim($sf['end']))
            {
                $start = $this->main->standardize_format($sf['start']);
                $this->request->setVar('mec_start_date', $start);

                $end = $this->main->standardize_format($sf['end']);
                $this->request->setVar('mec_maximum_date', $end);
                $this->maximum_date = $end;

                $skins = $this->main->get_skins();
                foreach($skins as $skin=>$label)
                {
                    $atts['sk-options'][$skin]['start_date_type'] = 'date';
                    $atts['sk-options'][$skin]['start_date'] = $start;
                }

                $atts['date-range-start'] = $start;
                $atts['date-range-end'] = $end;
            }
        }

        return apply_filters('add_to_search_box_query', $atts, $sf);
    }

    /**
     * Get Locations ID
     * @param string $address
     * @return array
     */
    public function get_locations_id($address = '')
    {
        if(!trim($address)) return array();

        $address = str_replace(' ', ',', $address);
        $locations = explode(',', $address);
        $query = "SELECT `term_id` FROM `#__termmeta` WHERE `meta_key` = 'address'";

        foreach($locations as $location) if(trim($location)) $query .= " AND `meta_value` LIKE '%" . trim($location) . "%'";

        $locations_id = $this->db->select($query, 'loadAssocList');
        return array_map(function($value)
        {
            return intval($value['term_id']);
        }, $locations_id);
    }

    public function sort_day_events($a, $b)
    {
        $a_start_date = $a->date['start']['date'];
        $b_start_date = $b->date['start']['date'];

        $a_timestamp = strtotime($a_start_date.' '.$a->data->time['start_raw']);
        $b_timestamp = strtotime($b_start_date.' '.$b->data->time['start_raw']);

        if($a_timestamp == $b_timestamp) return 0;
        return ($a_timestamp > $b_timestamp) ? +1 : -1;
    }

    public function sort_dates($a, $b)
    {
        $a_timestamp = strtotime($a);
        $b_timestamp = strtotime($b);

        if($a_timestamp == $b_timestamp) return 0;
        return ($a_timestamp > $b_timestamp) ? +1 : -1;
    }

    public function booking_button($event, $type = 'button')
    {
        if(!$this->booking_button) return '';
        if(!$this->main->can_show_booking_module($event)) return '';
        if($this->main->is_sold($event, $event->data->time['start_timestamp']) and isset($this->settings['single_date_method']) and $this->settings['single_date_method'] !== 'referred') return '';

        $link = $this->main->get_event_date_permalink($event, $event->date['start']['date']);
        $link = $this->main->add_qs_var('method', 'mec-booking-modal', $link);

        $modal = 'data-featherlight="iframe" data-featherlight-iframe-height="450" data-featherlight-iframe-width="700"';
        $title = $this->main->m('booking_button', __('Book Event', 'modern-events-calendar-lite'));

        if($type === 'button') return '<a class="mec-modal-booking-button mec-mb-button" href="'.esc_url($link).'" '.$modal.'>'.esc_html($title).'</a>';
        else return '<a class="mec-modal-booking-button mec-mb-icon" title="' . esc_attr($title) . '" href="'.esc_url($link).'" '.$modal.'><i class="mec-sl-note"></i></a>';
    }

    public function display_custom_data($event)
    {
        $output = '';

        $status = isset($this->skin_options['custom_data']) ? (boolean) $this->skin_options['custom_data'] : false;
        if($status and is_object($event))
        {
            $single = new MEC_skin_single();

            ob_start();
            $single->display_data_fields($event, false, true);
            $output .= ob_get_clean();
        }

        return $output;
    }

    public function display_detailed_time($event)
    {
        // Event Date
        $date = (isset($event->date) ? $event->date : array());

        $to = $date['end']['date'];
        $from = $this->main->get_start_of_multiple_days($event->ID, $to);

        $start_time = NULL;
        if(isset($date['start']['hour']))
        {
            $s_hour = $date['start']['hour'];
            if(strtoupper($date['start']['ampm']) == 'AM' and $s_hour == '0') $s_hour = 12;

            $start_time = sprintf("%02d", $s_hour).':';
            $start_time .= sprintf("%02d", $date['start']['minutes']);
            $start_time .= ' '.trim($date['start']['ampm']);
        }
        elseif(isset($event->data->time) and is_array($event->data->time) and isset($event->data->time['start_timestamp'])) $start_time = date('H:i', $event->data->time['start_timestamp']);

        $end_time = NULL;
        if(isset($date['end']['hour']))
        {
            $e_hour = $date['end']['hour'];
            if(strtoupper($date['end']['ampm']) == 'AM' and $e_hour == '0') $e_hour = 12;

            $end_time = sprintf("%02d", $e_hour).':';
            $end_time .= sprintf("%02d", $date['end']['minutes']);
            $end_time .= ' '.trim($date['end']['ampm']);
        }
        elseif(isset($event->data->time) and is_array($event->data->time) and isset($event->data->time['end_timestamp'])) $end_time = date('H:i', $event->data->time['end_timestamp']);

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        $output = '<div class="mec-detailed-time-wrapper">';
        $output .= '<div class="mec-detailed-time-start">'.sprintf(__('Start from: %s - %s', 'modern-events-calendar-lite'), date_i18n($date_format, strtotime($from)), date_i18n($time_format, strtotime($from.' '.$start_time))).'</div>';
        $output .= '<div class="mec-detailed-time-end">'.sprintf(__('End at: %s - %s', 'modern-events-calendar-lite'), date_i18n($date_format, strtotime($to)), date_i18n($time_format, strtotime($to.' '.$end_time))).'</div>';
        $output .= '</div>';

        return $output;
    }

    public function display_categories($event)
    {
        $output = '';

        $status = isset($this->skin_options['display_categories']) ? (boolean) $this->skin_options['display_categories'] : false;
        if($status and is_object($event) and isset($event->data->categories) and count($event->data->categories))
        {
            foreach($event->data->categories as $category)
            {
                if(isset($category['name']) and trim($category['name']))
                {
                    $color = ((isset($category['color']) and trim($category['color'])) ? $category['color'] : '');

                    $color_html = '';
                    if($color) $color_html .= '<span class="mec-event-category-color" style="--background-color: '.esc_attr($color).';background-color: '.esc_attr($color).'">&nbsp;</span>';

                    $output .= '<li class="mec-category"><a class="mec-color-hover" href="'.esc_url(get_term_link($category['id'])).'" target="_blank">' . trim($category['name']) . $color_html .'</a></li>';
                }
            }
        }

        return $output ? '<ul class="mec-categories">' . $output . '</ul>' : $output;
    }

    public function display_organizers($event)
    {
        $output = '';

        $status = isset($this->skin_options['display_organizer']) ? (boolean) $this->skin_options['display_organizer'] : false;
        if($status and is_object($event) and isset($event->data->organizers) and count($event->data->organizers))
        {
            foreach($event->data->organizers as $organizer)
            {
                $organizer_url = !empty($organizer['url']) ? 'href="'. $organizer['url'] .'" target="_blank"' : 'href="#"';
                if(isset($organizer['name']) and trim($organizer['name'])) $output .= '<li class="mec-organizer-item"><a class="mec-color-hover" '.$organizer_url.'>' . trim($organizer['name']) . '</a></li>';
            }
        }

        return $output ? '<div class="mec-shortcode-organizers"><i class="mec-sl-user"></i><ul class="mec-organizers">' . $output . '</ul></div>' : $output;
    }

    public function display_cost($event)
    {
        $output = '';
        if($this->display_price)
        {
            $cost = (isset($event->data->meta) and isset($event->data->meta['mec_cost']) and trim($event->data->meta['mec_cost'])) ? $event->data->meta['mec_cost'] : '';
            if(isset($event->date) and isset($event->date['start']) and isset($event->date['start']['timestamp'])) $cost = MEC_feature_occurrences::param($event->ID, $event->date['start']['timestamp'], 'cost', $cost);

            if($cost)
            {
                $output .= '<div class="mec-price-details">
                    <i class="mec-sl-wallet"></i>
                    <span>'.(is_numeric($cost) ? $this->main->render_price($cost, $event->ID) : $cost).'</span>
                </div>';
            }
        }

        return $output;
    }

    /**
     * @param $event
     * @param null $title
     * @param null $class
     * @param null $attributes
     * @return string|null
     */
    public function display_link($event, $title = NULL, $class = NULL, $attributes = NULL)
    {
        // Event Title
        if(is_null($title)) $title = $event->data->title;

        // Link Class
        if(is_null($class)) $class = 'mec-color-hover';

        $method = isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : false;

        // Link is disabled
        if($method == 'no' and in_array($class, array('mec-booking-button', 'mec-detail-button', 'mec-booking-button mec-bg-color-hover mec-border-color-hover', 'mec-event-link'))) return '';
        elseif($method == 'no') return $title;
        else
        {
            $sed_method = (isset($this->skin_options['sed_method']) ? $this->skin_options['sed_method'] : '');
            switch($sed_method)
            {
                case '0':

                    $sed_method = '_self';
                    break;
                case 'new':

                    $sed_method = '_blank';
                    break;
            }

            $sed_method = ($sed_method ? $sed_method : '_self');
        }

        $target = (!empty($sed_method) ? 'target="'.$sed_method.'" rel="noopener"' : '');
        $target = apply_filters('mec_event_link_change_target' , $target, $event->data->ID);
        return '<a '.($class ? 'class="'.$class.'"' : '').' '.($attributes ? $attributes : '').' data-event-id="'.$event->data->ID.'" href="'.$this->main->get_event_date_permalink($event, $event->date['start']['date']).'" '.$target.'>'.$title.'</a>';
    }

    public function get_end_date()
    {
        $end_date_type = (isset($this->skin_options['end_date_type']) and trim($this->skin_options['end_date_type'])) ? trim($this->skin_options['end_date_type']) : 'date';

        if($end_date_type === 'today') $maximum_date = current_time('Y-m-d');
        elseif($end_date_type === 'tomorrow') $maximum_date = date('Y-m-d', strtotime('Tomorrow'));
        else $maximum_date = (isset($this->skin_options['maximum_date_range']) and trim($this->skin_options['maximum_date_range'])) ? trim($this->skin_options['maximum_date_range']) : NULL;

        return $maximum_date;
    }

    public function get_label_captions($event, $extra_class = null)
    {
        $captions = '';
        if(isset($event->data->labels) and is_array($event->data->labels) and count($event->data->labels))
        {
            foreach($event->data->labels as $label)
            {
                if(!isset($label['style']) or (isset($label['style']) and !trim($label['style']))) continue;

                $captions .= '<span class="mec-event-label-captions '.$extra_class.'" style="--background-color: '.esc_attr($label['color']).';background-color: '.esc_attr($label['color']).'">';
                if($label['style'] == 'mec-label-featured') $captions .= esc_html__($label['name'], 'modern-events-calendar-lite');
                elseif($label['style'] == 'mec-label-canceled') $captions .= esc_html__($label['name'], 'modern-events-calendar-lite');
                elseif($label['style'] == 'mec-label-custom' and isset($label['name']) and trim($label['name'])) $captions .= esc_html__($label['name'], 'modern-events-calendar-lite');
                $captions .= '</span>';

                break;
            }
        }

        return $captions;
    }
}