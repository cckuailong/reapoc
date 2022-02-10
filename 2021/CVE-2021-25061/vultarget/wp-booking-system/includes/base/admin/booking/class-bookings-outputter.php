<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Bookings_Outputter
{

    /**
     * The ID of the calendar
     *
     * @access protected
     * @var    int
     *
     */
    protected $calendar_id;

    /**
     * The bookings
     *
     * @access protected
     * @var    array
     *
     */
    protected $bookings;

    /**
     * The booking tabs
     *
     * @access protected
     * @var    array
     *
     */
    protected $tabs;

    /**
     * The plugin general settings
     *
     * @access protected
     * @var    array
     *
     */
    protected $plugin_settings = array();

    /**
     * Constructor
     *
     * @param $calendar_id
     *
     */
    public function __construct($calendar_id)
    {

        /**
         * Set calendar ID
         *
         */
        $this->calendar_id = absint($calendar_id);

        /**
         * Get the bookings
         *
         */
        $this->bookings = wpbs_get_bookings(array('calendar_id' => $this->calendar_id));

        /**
         * Set the tabs
         *
         */
        $this->tabs = array(
            'pending' => __('Pending', 'wp-booking-system'),
            'accepted' => __('Accepted', 'wp-booking-system'),
            'trash' => __('Deleted', 'wp-booking-system'),
        );

        /**
         * Set plugin settings
         *
         */
        $this->plugin_settings = get_option('wpbs_settings', array());

    }

    /**
     * Displays the Bookings meta box content
     *
     */
    public function display()
    {

        $output = '';

        $output .= $this->header();

        $output .= '<div class="wpbs-booking-fields">';

        $output .= $this->bookings();

        $output .= '</div>';

        $output .= $this->pagination();

        echo $output;

    }

    /**
     * Returns the header of the booking meta box
     *
     * Tabs, Search and Sort controls
     *
     * @return string
     *
     */
    protected function header()
    {
        $output = '';

        $output .= $this->tabs();

        $output .= '<div class="wpbs-bookings-header">';

        $output .= $this->sorting();
        $output .= $this->search();

        $output .= '</div>';

        return $output;
    }

    /**
     * Returns the booking tabs
     *
     * @return string
     *
     */
    protected function tabs()
    {

        $output = '<ul class="wpbs-bookings-tab-navigation subsubsub">';
        foreach ($this->tabs as $tab_id => $tab_name) {
            $active_class = $tab_id == 'pending' ? 'class="current"' : '';
            $output .= '<li class="' . $tab_id . '"><a href="#" ' . $active_class . ' data-tab="wpbs-bookings-tab-' . $tab_id . '"><span class="label">' . $tab_name . '</span> <span class="count">()</span></a><span class="separator"> |</span></li>';
        }
        $output .= '</ul>';

        return $output;

    }

    /**
     * Returns the booking sorting dropdowns
     *
     * @return string
     *
     */
    protected function sorting()
    {
        $output = '<select id="wpbs-bookings-order-by">';
        $output .= '<option value="" disabled selected>' . __('Sort by', 'wp-booking-system') . '</option>';
        $output .= '<option value="id">' . __('Date', 'wp-booking-system') . '</option>';
        $output .= '<option value="check-in-date">' . __('Check-in date', 'wp-booking-system') . '</option>';
        $output .= '<option value="check-out-date">' . __('Check-out date', 'wp-booking-system') . '</option>';
        $output .= '</select>';

        $output .= '<select id="wpbs-bookings-order">';
        $output .= '<option value="" disabled selected>' . __('Sort order', 'wp-booking-system') . '</option>';
        $output .= '<option value="asc">' . __('Ascending', 'wp-booking-system') . '</option>';
        $output .= '<option value="desc">' . __('Descending', 'wp-booking-system') . '</option>';
        $output .= '</select>';

        return $output;
    }

    /**
     * Returns the booking search input
     *
     * @return string
     *
     */
    protected function search()
    {
        $output = '<p class="search-box">';
        $output .= '<input type="search" id="wpbs-bookings-search" name="wpbs-bookings-search" value="" placeholder="' . __('Search bookings', 'wp-booking-system') . '" >';
        $output .= '</p>';

        return $output;
    }

    /**
     * Returns the bookings grouped by tabs
     *
     * @return string
     *
     */
    protected function bookings()
    {

        $output = '';

        // Loop through tabs
        foreach ($this->tabs as $tab_id => $tab_name) {
            $active_class = $tab_id == 'pending' ? 'active' : '';

            $output .= '<div class="wpbs-bookings-tab ' . $active_class . '" id="wpbs-bookings-tab-' . $tab_id . '">';

            // Loop through bookings
            foreach ($this->bookings as $booking) {

                // Skif if not in the correct tab
                if ($booking->get('status') != $tab_id) {
                    continue;
                }

                $output .= $this->booking($booking);
            }

            $output .= '</div>';
        }

        $output .= '<p class="wpbs-bookings-no-results">' . sprintf(__("You don't have any %s bookings.", 'wp-booking-system'), '<strong></strong>') . '</p>';
        $output .= '<p class="wpbs-bookings-no-search-results">' . sprintf(__("No results for %s.", 'wp-booking-system'), '<strong></strong>') . '</p>';

        return $output;

    }

    /**
     * Returns the bookings
     *
     * @return string
     *
     */
    protected function booking($booking)
    {
        $output = '';
        $output .= '<div class="wpbs-booking-field wpbs-open-booking-details wpbs-booking-field-is-read-' . $booking->get('is_read') . '"
                    data-id="' . $booking->get('id') . '"
                    data-check-in-date="' . strtotime($booking->get('start_date')) . '"
                    data-check-out-date="' . strtotime($booking->get('end_date')) . '">';

        $output .= '<div class="wpbs-booking-field-inner">';
        $output .= '<div class="wpbs-booking-field-header">';
        $output .= '<div class="wpbs-booking-field-header-fixed-elements">';
        $output .= '<div class="wpbs-booking-field-booking-id wpbs-booking-color-' . ($booking->get('id') % 10) . '">#' . $booking->get('id') . '</div>';
        $output .= '<p class="wpbs-booking-field-check-in-date">';
        $output .= '<i class="wpbs-icon-check-in"></i>';
        $output .= '<span class="wpbs-booking-field-header-label"> ' . date(get_option('date_format'), strtotime($booking->get('start_date'))) . '</span>';
        $output .= '</p>';
        $output .= '<p class="wpbs-booking-field-check-out-date">';
        $output .= '<i class="wpbs-icon-check-out"></i>';
        $output .= '<span class="wpbs-booking-field-header-label"> ' . date(get_option('date_format'), strtotime($booking->get('end_date'))) . '</span>';
        $output .= '</p>';
        $output .= '</div>';
        $output .= $this->booking_details($booking);
        $output .= $this->is_read($booking);

        $output .= '</div>';
        $output .= '</div>';

        $output .= '</div>';

        return $output;
    }

    /**
     * Returns the booking detail field
     *
     * @param WPBS_Booking $booking
     *
     * @return string
     *
     */
    protected function booking_details($booking)
    {
        $details = '';
        foreach ($booking->get('fields') as $field) {
            if (!isset($field['user_value']) || empty($field['user_value'])) {
                continue;
            }

            // Exclude some fields
            if (in_array($field['type'], wpbs_get_excluded_fields())) {
                continue;
            }


            if (is_array($field['user_value'])) {
                $user_value = trim(implode(', ', $field['user_value']), ', ');

            } else {
                $user_value = $field['user_value'];
            }

            $details .= '<span><strong>' . $this->get_translated_label($field) . ':</strong> <span>' . $user_value . '</span></span>';
        }

        $output = '<p class="wpbs-booking-field-details">' . $details . '</p>';
        return $output;
    }

    /**
     * Helper function to get label translations
     *
     * @param array $field
     *
     * @return string
     *
     */
    protected function get_translated_label($field)
    {
        $language = wpbs_get_locale();
        if (isset($field['values'][$language]['label']) && !empty($field['values'][$language]['label'])) {
            return $field['values'][$language]['label'];
        }

        return $field['values']['default']['label'];
    }

    /**
     * Generates and returns the HTML for the "new" label for unread bookings
     *
     * @param WPBS_Booking $booking
     *
     * @return string
     *
     */
    protected function is_read($booking)
    {
        if ($booking->get('is_read') == 1) {
            return false;
        }

        return '<div class="wpbs-booking-field-new-booking"><div class="wpbs-booking-field-booking-id">' . __('New', 'wp-booking-system') . '</div></div>';
    }

    /**
     * Returns the booking pagination
     *
     * @return string
     *
     */
    protected function pagination()
    {
        $output = '';
        $output .= '<div class="tablenav-pages wpbs-bookings-pagination">';
        $output .= '<span class="displaying-num"><span></span> ' . __('bookings', 'wp-booking-system') . '</span>';
        $output .= '<span class="pagination-links">';
        $output .= '<a class="first-page button" href="#"><span aria-hidden="true">&laquo;</span></a>';
        $output .= '<a class="prev-page button" href="#"><span aria-hidden="true">&lsaquo;</span></a>';
        $output .= '<span class="paging-input">';
        $output .= '<span class="tablenav-paging-text"><span class="current-page"></span> ' . __('of', 'wp-booking-system') . ' <span class="total-pages"></span></span>';
        $output .= '</span>';

        $output .= '<a class="next-page button" href="#"><span aria-hidden="true">&rsaquo;</span></a>';
        $output .= '<a class="last-page button" href="#"><span aria-hidden="true">&raquo;</span></a>';
        $output .= '</span>';
        $output .= '</div>';

        return $output;
    }

}
