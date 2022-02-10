<?php

/**
 * Class ECWD_Event
 */
class ECWD_Event {

    /**
     * Class constructor
     *
     */
    public function __construct($id, $calendar_id, $title, $description, $location, $start_time, $end_time, $url, $latLong = '', $permalink = '', $event='', $term_metas = '', $metas = '', $image='') {
        $this->event_id = $id;
        $this->calendar_id = $calendar_id;
        $this->title = $title;
        $this->description =  do_shortcode($description);
        $this->location = $location;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->url = $url;
        $this->latlong = $latLong;
        $this->permalink = $permalink;
        $this->post = $event;
        $this->terms = $term_metas;
        $this->metas = $metas;
        $this->image = $image;


    }

    /**
     * Returns an array of days (as UNIX timestamps) that this events spans
     */
    public function get_days($event) {
        if ($event->start_time !== null) {
           // echo $this->event_id.'----------'.$event->start_time.'----'.$event->end_time."<br />";
            //Round start date to nearest day
            $start_time = mktime(0, 0, 0, ECWD::ecwd_date('m', $event->start_time), ECWD::ecwd_date('d', $event->start_time), ECWD::ecwd_date('Y', $event->start_time));

            $days = array();

            //If multiple day events should be handled, and this event is a multi-day event, add multiple day event to required days
            if ($event->multiple_day_events && ( 'MPD' == $event->day_type || 'MWD' == $event->day_type )) {
                $on_next_day = true;
                $next_day = $start_time;

                while ($on_next_day) {
                    //If the end time of the event is after 00:00 on the next day (therefore, not doesn't end on this day)
                    if ($event->end_time > $next_day) {
                        $days[] = ECWD::ecwd_date('Y-m-d H:i:s', $next_day);
                    } else {
                        $on_next_day = false;
                    }
                    $next_day += 86400;
                }
            } else {
                //Add event into array of events for that day
                $days[] = ECWD::ecwd_date('Y-m-d H:i:s', $start_time);
            }
            return $days;
        } else {
            return array();
        }
    }

    /**
     * Returns the markup for this event
     */
    public function get_event_markup($display_type, $num_in_day, $num) {
        //Set the display type (either tooltip or list)
        $this->type = $display_type;

        //Set which number event this is in day (first in day etc)
        $this->num_in_day = $num_in_day;

        //Set the position of this event in array of events currently being processed
        $this->pos = $num;

        $this->time_now = current_time('timestamp');

        // First check if we use the builder or not
        $use_simple = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_simple', true);

        if (empty($use_simple)) {
            return $this->use_builder();
        }

        // Setup the markup to return
        //$display_options = get_option( ECWD_PLUGIN_PREFIX.'_settings_general' );

        $display_options['display_start'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_start', true);
        $display_options['display_start_text'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_start_text', true);
        $display_options['display_end'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_end', true);
        $display_options['display_end_text'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_end_text', true);
        $display_options['display_location'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_location', true);
        $display_options['display_location_text'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_location_text', true);
        $display_options['display_desc'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_description', true);
        $display_options['display_deecwd_text'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_description_text', true);
        $display_options['display_deecwd_limit'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_description_max', true);
        $display_options['display_link'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_link', true);
        $display_options['display_link_text'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_link_text', true);
        $display_options['display_separator'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_separator', true);
        $display_options['display_link_target'] = get_post_meta($this->event->id, ECWD_PLUGIN_PREFIX.'_display_link_tab', true);

        $markup = '<p class="ecwd-' . $this->type . '-event">' . esc_html($this->title) . '</p>';

        $start_end = array();

        //If start date / time should be displayed, set up array of start date and time
        if (!empty($display_options['display_start']) && 'none' != $display_options['display_start']) {
            $sd = $this->start_time;
            $start_end['start'] = array(
                'time' => date_i18n($this->event->time_format, $sd),
                'date' => date_i18n($this->event->date_format, $sd)
            );
        }

        //If end date / time should be displayed, set up array of end date and time
        if (!empty($display_options['display_end']) && 'none' != $display_options['display_end']) {
            $ed = $this->end_time;
            $start_end['end'] = array(
                'time' => date_i18n($this->event->time_format, $ed),
                'date' => date_i18n($this->event->date_format, $ed)
            );
        }

        //Add the correct start / end, date / time information to $markup
        foreach ($start_end as $start_or_end => $info) {
            $markup .= '<p class="ecwd-' . $this->type . '-' . $start_or_end . '"><span>' . esc_html($display_options['display_' . $start_or_end . '_text']) . '</span> ';

            if (!empty($display_options['display_' . $start_or_end])) {
                switch ($display_options['display_' . $start_or_end]) {
                    case 'time': $markup .= esc_html($info['time']);
                        break;
                    case 'date': $markup .= esc_html($info['date']);
                        break;
                    case 'time-date': $markup .= esc_html($info['time'] . $display_options['display_separator'] . $info['date']);
                        break;
                    case 'date-time': $markup .= esc_html($info['date'] . $display_options['display_separator'] . $info['time']);
                }
            }

            $markup .= '</p>';
        }

        //If location should be displayed (and is not empty) add to $markup
        if (!empty($display_options['display_location'])) {
            $event_location = $this->location;
            if ('' != $event_location)
                $markup .= '<p class="ecwd-' . $this->type . '-loc"><span>' . esc_html($display_options['display_location_text']) . '</span> ' . esc_html($event_location) . '</p>';
        }

        //If description should be displayed (and is not empty) add to $markup
        if (!empty($display_options['display_desc'])) {
            $event_desc = $this->description;

            if ('' != $event_desc) {
                //Limit number of words of description to display, if required
                if ('' != $display_options['display_deecwd_limit']) {
                    preg_match('/([\S]+\s*){0,' . $display_options['display_deecwd_limit'] . '}/', $this->description, $event_desc);
                    $event_desc = trim($event_desc[0]);
                }

                $markup .= '<p class="ecwd-' . $this->type . '-desc"><span>' . $display_options['display_deecwd_text'] . '</span> ' . make_clickable(nl2br(esc_html($event_desc))) . '</p>';
            }
        }

        //If link should be displayed add to $markup
        if (!empty($display_options['display_link'])) {
            $target = (!empty($display_options['display_link_target']) ? 'target="blank"' : '' );

            $ctz = get_option('timezone_string');

            $link = $this->link . (!empty($ctz) ? '&ctz=' . $ctz : '' );

            $markup .= '<p class="ecwd-' . $this->type . '-link"><a href="' . eecwd_url($link) . '" ' . $target . '>' . esc_html($display_options['display_link_text']) . '</a></p>';
        }

        return $markup;
    }




    //Returns the difference between two times in human-readable formats
    function ecwd_human_time_diff($from, $to = '', $limit = 1) {
        $units = array(
            31556926 => array(__('%s year', 'event-calendar-wd'), __('%s years', 'event-calendar-wd')),
            2629744 => array(__('%s month', 'event-calendar-wd'), __('%s months', 'event-calendar-wd')),
            604800 => array(__('%s week', 'event-calendar-wd'), __('%s weeks', 'event-calendar-wd')),
            86400 => array(__('%s day', 'event-calendar-wd'), __('%s days', 'event-calendar-wd')),
            3600 => array(__('%s hour', 'event-calendar-wd'), __('%s hours', 'event-calendar-wd')),
            60 => array(__('%s min', 'event-calendar-wd'), __('%s mins', 'event-calendar-wd')),
        );

        if (empty($to))
            $to = time();

        $from = (int) $from;
        $to = (int) $to;
        $diff = (int) abs($to - $from);

        $items = 0;
        $output = array();

        foreach ($units as $unitsec => $unitnames) {
            if ($items >= $limit)
                break;

            if ($diff < $unitsec)
                continue;

            $numthisunits = floor($diff / $unitsec);
            $diff = $diff - ( $numthisunits * $unitsec );
            $items++;

            if ($numthisunits > 0)
                $output[] = sprintf(_n($unitnames[0], $unitnames[1], $numthisunits), $numthisunits);
        }

        $seperator = _x(', ', 'human_time_diff');

        if (!empty($output)) {
            return implode($seperator, $output);
        } else {
            $smallest = array_pop($units);
            return sprintf($smallest[0], 1);
        }
    }


	public static function getLink( $event, $date ) {

        return trailingslashit( get_permalink( $event->ID ) );
        //TODO REMOVE FROM FREE VERSION
        /*
		// if permalinks are off use ugly links.
		$date = date('Y-m-d', strtotime($date));
		if ( '' == get_option( 'permalink_structure' ) ) {
			return esc_url_raw( self::uglyLink($event,$date ) );
		}

		$link     = trailingslashit( get_permalink( $event->ID ) );
		$eventUrl = trailingslashit( esc_url_raw( $link ) );
		$eventUrl  = trailingslashit( esc_url_raw( $eventUrl . $date ) );
		//$eventUrl = add_query_arg('eventDate', $date, $eventUrl );
		return $eventUrl;
        */
	}

	public static function uglyLink(  $event, $date  ) {
		$eventUrl = add_query_arg( 'post_type', 'ecwd_event', home_url() );
		$eventUrl = add_query_arg( array( 'eventDate' => $date ), get_permalink($event->ID) );
		return $eventUrl;
	}


}
