<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Calendar_Editor_Outputter
{

    /**
     * The arguments for the calendar outputter
     *
     * @access protected
     * @var    array
     *
     */
    protected $args;

    /**
     * The WPBS_Calendar
     *
     * @access protected
     * @var    WPBS_Calendar
     *
     */
    protected $calendar = null;

    /**
     * The list of legend items associated with the calendar
     *
     * @access protected
     * @var    array
     *
     */
    protected $legend_items = array();

    /**
     * The default legend item of the calendar
     *
     * @access protected
     * @var    WPBS_Legend_Item
     *
     */
    protected $default_legend_item = null;

    /**
     * The list of events for the calendar for the given displayed range
     *
     * @access protected
     * @var    array
     *
     */
    protected $events = array();

    /**
     * The list of bookings for the calendar
     *
     * @access protected
     * @var    array
     *
     */
    protected $bookings = array();

    /**
     * The list of events from the linked iCal feeds
     *
     * @access protected
     * @var    array
     *
     */
    protected $ical_events = array();

    /**
     * Custom calendar data arranged by date
     *
     * @access protected
     * @var    array
     *
     */
    protected $data = array();

    /**
     * The view where the editor is displayed.
     *
     * False for Calendar, True for Booking Popup
     *
     * @access protected
     * @var    string
     *
     */
    protected $booking_view = false;

    /**
     * The default price for events
     *
     * @access protected
     * @var    string
     *
     */
    protected $default_price = 0;

    /**
     * Constructor
     *
     * @param int   $calendar    - the calendar for which to print the editable calendar fields
     * @param array $args          - arguments from which to build the calendar fields
     * @param array $data          - extra data to be populated in the calendar fields on top of the
     *                                already saved events
     *
     */
    public function __construct($calendar, $args, $data = array())
    {

        $defaults = array(
            'current_year' => (!empty($args['current_year']) ? $args['current_year'] : date('Y')),
            'current_month' => (!empty($args['current_month']) ? $args['current_month'] : date('n')),
            'booking_view' => false,
            'booking_start_date' => '',
            'booking_end_date' => '',
        );

        /**
         * Set arguments
         *
         */
        $this->args = wp_parse_args($args, $defaults);

        /**
         * Set the view
         *
         */

        $this->booking_view = $this->args['booking_view'];

        /**
         * Set the calendar
         *
         */
        $this->calendar = $calendar;

        /**
         * Set the calendar legend items
         *
         */
        $this->legend_items = wpbs_get_legend_items(array('calendar_id' => $calendar->get('id')));

        /**
         * Set the default legend item
         *
         */
        foreach ($this->legend_items as $legend_item) {

            if ($legend_item->get('is_default') == 1) {
                $this->default_legend_item = $legend_item;
            }

        }

        /**
         * Set the calendar events
         *
         */
        $this->events = wpbs_get_events(array('calendar_id' => $calendar->get('id'), 'date_year' => $this->args['current_year'], 'date_month' => $this->args['current_month']));

        /**
         * Set the calendar bookings
         *
         */
        $this->bookings = wpbs_get_bookings_as_events($calendar->get('id'), $this->events);


        /**
         * Set the calendar data
         *
         */
        $this->data = $data;

        /**
		 * Set the default price
		 */
		$this->default_price = wpbs_get_calendar_meta($this->calendar->get('id'), 'default_price', true);

    }

    /**
     * Constructs and returns the HTML for the entire calendar month editor
     *
     * @return string
     *
     */
    public function get_display()
    {

        $start_day = 1;

        $total_days = date('t', mktime(0, 0, 0, $this->args['current_month'], 1, $this->args['current_year']));

        if ($this->booking_view) {
            $start_day = date('j', $this->args['booking_start_date']);
            $total_days = date('j', $this->args['booking_end_date']);
        }

        $output = '<div class="wpbs-calendar-editor">';

        $output .= $this->get_display_date_header();

        for ($i = $start_day; $i <= $total_days; $i++) {

            $output .= $this->get_display_date($this->args['current_year'], $this->args['current_month'], $i);

        }

        /**
         * Calendar Editor Custom CSS
         *
         */
        $output .= $this->get_custom_css();

        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML for the dates columns header
     *
     * @return string
     *
     */
    protected function get_display_date_header()
    {

        // Don't output header for Booking view
        if ($this->booking_view) {
            return false;
        }

        $output = '<div class="wpbs-calendar-date">';

        $output .= '<div class="wpbs-calendar-date-legend-item-header">' . __('Availability', 'wp-booking-system') . '</div>';
        $output .= '<div class="wpbs-calendar-date-booking-ids-header">' . wpbs_get_output_tooltip(__('In this column you can see the bookings associated with each date.', 'wp-booking-system')) . '</div>';

        $output .= apply_filters('wpbs_calendar_editor_columns_header_before_description', '');

        $output .= '<div class="wpbs-calendar-date-description-header">' . __('Description', 'wp-booking-system') . wpbs_get_output_tooltip(__('You can use the description field of the date to add private information regarding your booking. This information will not be displayed anywhere else, but here.', 'wp-booking-system')) . '</div>';

        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML for a single calendar given date
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return string
     *
     */
    protected function get_display_date($year, $month, $day)
    {

        /**
         * Prepare data
         *
         */
        $data = $this->get_data_by_date($year, $month, $day);
        $event = $this->get_event_by_date($year, $month, $day);
        $ical_event = $this->get_ical_event_by_date($year, $month, $day);

        /**
         * Prepare output
         *
         */
        $output = '<div class="wpbs-calendar-date">';

        if (!is_null($ical_event)) {

            // Get the date legend item
            $output .= $this->get_display_date_legend_item($year, $month, $day, $ical_event, null);

            /**
             * Prepare iCal description output
             *
             */
            $output .= '<div class="wpbs-calendar-date-description-ical">';

            $output .= '<span class="dashicons dashicons-calendar-alt"></span>';
            $output .= '<input type="text" readonly value="' . esc_attr($ical_event->get('description')) . '" />';

            $output .= '</div>';

        } else {

            // Get the date legend item
            $output .= $this->get_display_date_legend_item($year, $month, $day, $event, $data);

            
            // Get the date description
            $output .= $this->get_display_booking_id($year, $month, $day, $event, $data);

            $output .= apply_filters('wpbs_calendar_editor_columns_before_description', '', $year, $month, $day, $event, $data, $this->default_price);

            // Get the date description
            $output .= $this->get_display_date_description($year, $month, $day, $event, $data);

            

            // Set-up extra data that can be added by others
            $output .= '<div class="wpbs-calendar-date-meta">';

            /**
             * Filter to add custom calendar date meta
             *
             * @param int $year
             * @param int $month
             * @param int $day
             * @param mixed WPBS_Event|null
             * @param mixed array|null
             *
             */
            $output .= apply_filters('wpbs_calendar_editor_display_date', '', $year, $month, $day, $event, $data);

            $output .= '</div>';

        }

        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML for the date legend item selector
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param mixed WPBS_Event|null
     * @param mixed array|null
     *
     * @return string
     *
     */
    protected function get_display_date_legend_item($year, $month, $day, $event, $data)
    {

        /**
         * Set selected value
         *
         */
        $selected_id = '';
        $selected = null;

        if (!is_null($data)) {

            $selected_id = $data['legend_item_id'];

        } elseif (!is_null($event)) {

            $selected_id = $event->get('legend_item_id');

        }

        foreach ($this->legend_items as $legend_item) {

            if ($selected_id == $legend_item->get('id')) {
                $selected = $legend_item;
                break;
            }

        }

        // If none of the existing legend items is the selected one,
        // set the default legend item as the selected one
        if (is_null($selected)) {
            $selected = $this->default_legend_item;
            $selected_id = $selected->get('id');
        }

        /**
         * Prepare output
         *
         */
        $output = '<div class="wpbs-calendar-date-legend-item wpbs-calendar-date-legend-item-' . esc_attr($selected->get('id')) . '">';

        $output .= '<div class="wpbs-legend-item-icon-wrapper">';
        $output .= '<div class="wpbs-date-inner">' . esc_attr($day) . '</div>';
        $output .= wpbs_get_legend_item_icon($selected->get('id'), $selected->get('type'));
        $output .= '</div>';

        $output .= '<select data-name="legend_item_id" data-year="' . esc_attr($year) . '" data-month="' . esc_attr($month) . '" data-day="' . esc_attr($day) . '" ' . (!is_null($event) && is_null($event->get('id')) ? 'disabled' : '') . '>';

        foreach ($this->legend_items as $legend_item) {

            $output .= '<option value="' . esc_attr($legend_item->get('id')) . '" ' . selected($selected_id, $legend_item->get('id'), false) . ' data-type="' . esc_attr($legend_item->get('type')) . '">' . $legend_item->get('name') . '</option>';

        }

        $output .= '</select>';
        $output .= '</div>';

        return $output;

    }

    /**
     * Constructs and returns the HTML for the booking id field
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param mixed WPBS_Event|null
     * @param mixed array|null
     *
     * @return string
     *
     */
    protected function get_display_booking_id($year, $month, $day, $event, $data)
    {

        if($this->booking_view){
            return '';
        }
        
        /**
         * Prepare output
         *
         */

        $events = $this->get_event_by_booking($year, $month, $day);

        $output = '<div class="wpbs-calendar-date-booking-ids">';
        
        if (!empty($events)) {
            foreach ($events as $event) {

                $booking = wpbs_get_booking( $event->get('booking_id') );

                $output .= '<a href="#" class="wpbs-calendar-date-booking-id wpbs-open-booking-details wpbs-booking-color-status-'.$booking->get('status').' wpbs-booking-color-' . ($event->get('booking_id') % 10) . ' " data-id="' . $event->get('booking_id') . '">#' . $event->get('booking_id') . '</a>';
            }
        } else {
            $output .= '<div class="wpbs-calendar-date-booking-id wpbs-calendar-date-booking-id-color-10">&nbsp;</div>';
        }

        $output .= '</div>';

        return $output;
    }

    /**
     * Constructs and returns the HTML for the date description field
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param mixed WPBS_Event|null
     * @param mixed array|null
     *
     * @return string
     *
     */
    protected function get_display_date_description($year, $month, $day, $event, $data)
    {

        /**
         * Set selected value
         *
         */
        $value = '';

        if (!is_null($data) && isset($data['description'])) {

            $value = $data['description'];

        } elseif (!is_null($event)) {

            $value = $event->get('description');

        }

        /**
         * Prepare output
         *
         */
        $output = '<div class="wpbs-calendar-date-description">';

        $output .= '<span class="dashicons dashicons-edit"></span>';
        $output .= '<input type="text" value="' . esc_attr($value) . '" data-name="description" data-year="' . esc_attr($year) . '" data-month="' . esc_attr($month) . '" data-day="' . esc_attr($day) . '" />';

        $output .= '</div>';

        return $output;

    }

    /**
     * Passes through all stored events and searches for the event that matches the given date
     * If an event is found it is returned, else null is returned
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return mixed WPBS_Event|null
     *
     */
    protected function get_event_by_date($year, $month, $day)
    {

        foreach ($this->events as $event) {

            if ($event->get('date_year') == $year && $event->get('date_month') == $month && $event->get('date_day') == $day) {
                return $event;
            }

        }

        return null;

    }

    /**
     * Passes through all stored booking events and searches for the event that matches the given date
     * If events are found they are returned, else an empty array is returned
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return mixed array
     *
     */
    protected function get_event_by_booking($year, $month, $day)
    {

        $bookings = array();

        foreach ($this->bookings as $booking) {
            if ($booking->get('date_year') == $year && $booking->get('date_month') == $month && $booking->get('date_day') == $day) {
                $bookings[] = $booking;
            }
        }

        if (!empty($bookings)) {
            return $bookings;
        }

        return array();

    }

    /**
     * Passes through all stored ical events and searches for the event that matches the given date
     * If an event is found it is returned, else null is returned
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return mixed WPBS_Event|null
     *
     */
    protected function get_ical_event_by_date($year, $month, $day)
    {

        foreach ($this->ical_events as $event) {

            if ($event->get('date_year') == $year && $event->get('date_month') == $month && $event->get('date_day') == $day) {
                return $event;
            }

        }

        return null;

    }

    /**
     * Passes through all stored calendar data and searches for the data that matches the given date
     * If data is found it is returned, else null is returned
     *
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return mixed array|null
     *
     */
    protected function get_data_by_date($year, $month, $day)
    {

        if (isset($this->data[$year][$month][$day])) {
            return $this->data[$year][$month][$day];
        }

        return null;

    }

    /**
     * Constructs and returns the calendar's custom CSS
     *
     * @return string
     *
     */
    protected function get_custom_css()
    {

        $output = '<style>';

        // Set the parent calendar class
        $calendar_parent_class = '.wpbs-calendar-editor';

        /**
         * Legend Items CSS
         *
         */
        foreach ($this->legend_items as $legend_item) {

            // Background colors
            $colors = $legend_item->get('color');

            $output .= $calendar_parent_class . ' .wpbs-legend-item-icon-' . esc_attr($legend_item->get('id')) . ' div:first-of-type { background-color: ' . (!empty($colors[0]) ? esc_attr($colors[0]) : 'transparent') . '; }';
            $output .= $calendar_parent_class . ' .wpbs-legend-item-icon-' . esc_attr($legend_item->get('id')) . ' div:nth-of-type(2) { background-color: ' . (!empty($colors[1]) ? esc_attr($colors[1]) : 'transparent') . '; }';

            // Text color
            $color_text = $legend_item->get('color_text');

            if (!empty($color_text)) {
                $output .= $calendar_parent_class . ' .wpbs-calendar-date-legend-item-' . esc_attr($legend_item->get('id')) . ' .wpbs-date-inner { color: ' . esc_attr($color_text) . '; }';
            }

        }

        $output .= '</style>';

        return $output;

    }

    /**
     * Helper function that prints the calendar editor
     *
     */
    public function display()
    {

        echo $this->get_display();

    }

}
