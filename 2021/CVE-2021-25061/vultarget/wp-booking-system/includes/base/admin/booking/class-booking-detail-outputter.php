<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Booking_Details_Outputter
{

    /**
     * The calendar
     *
     * @access protected
     * @var    WPBS_Calendar
     *
     */
    protected $calendar_id;

    /**
     * The booking
     *
     * @access protected
     * @var    WPBS_Booking
     *
     */
    protected $booking;

    /**
     * Tabs
     *
     * @access protected
     * @var    array
     *
     */
    protected $tabs;

    /**
     * Plugin Settings
     *
     * @access protected
     * @var    array
     *
     */
    protected $plugin_settings;

    /**
     * Constructor
     *
     * @param WPBS_Booking $booking
     *
     */
    public function __construct($booking)
    {
        /**
         * Get Booking
         *
         */
        $this->booking = $booking;

        /**
         * Get Calendar
         *
         */
        $this->calendar = wpbs_get_calendar($this->booking->get('calendar_id'));

        /**
         * Set default tabs
         *
         */
        $this->tabs = array(
            'manage-booking' => __('Manage Booking', 'wp-booking-system'),
            'booking-details' => __('Booking Details', 'wp-booking-system')
        );

        $this->tabs = apply_filters('wpbs_booking_modal_tabs', $this->tabs);

        $this->check_tabs();

        /**
         * Set plugin settings
         *
         */
        $this->plugin_settings = get_option('wpbs_settings', array());

    }

    /**
     * Displays the modal HTML
     *
     */
    public function display()
    {
        include 'views/view-modal.php';
    }

    /**
     * Show or hide tabs depending on the stastus of the booking
     *
     */
    protected function check_tabs()
    {
        
    }

    /**
     * Get the active tab depending on the stastus of the booking
     *
     * @return string
     *
     */
    protected function get_active_tab()
    {
        if ($this->booking->get('status') == 'accepted') {
            return 'booking-details';
        }
        return $this->active_tab = 'manage-booking';
    }

    /**
     * Get the button label depending on the stastus of the booking
     *
     * @return string
     *
     */
    protected function get_manage_booking_button_label()
    {
        if ($this->booking->get('status') == 'pending') {
            return __('Accept Booking', 'wp-booking-system');
        } else if ($this->booking->get('status') == 'trash') {
            return __('Restore Booking', 'wp-booking-system');
        }
        return __('Update Booking', 'wp-booking-system');
    }

    /**
     * Get email heading depending on the stastus of the booking
     *
     * @return string
     *
     */
    protected function get_email_customer_heading()
    {
        if ($this->booking->get('status') == 'pending') {
            return __('Send an email to the customer when accepting the booking', 'wp-booking-system');
        }
        return __('Send an email to the customer when updating the booking', 'wp-booking-system');
    }

    /**
     * Get booking data
     *
     * @return array
     *
     */
    protected function get_booking_data()
    {
        $data = array();

        $data[] = array(
            'label' => __('Booking ID', 'wp-booking-system'),
            'value' => '#' . $this->booking->get('id'),
        );

        $data[] = array(
            'label' => __('Start Date', 'wp-booking-system'),
            'value' => date(get_option('date_format'), strtotime($this->booking->get('start_date'))),
        );

        $data[] = array(
            'label' => __('End Date', 'wp-booking-system'),
            'value' => date(get_option('date_format'), strtotime($this->booking->get('end_date'))),
        );

        $data[] = array(
            'label' => __('Booked on', 'wp-booking-system'),
            'value' => date(get_option('date_format'), strtotime($this->booking->get('date_created'))),
        );

        return $data;
    }

    /**
     * Get form data
     *
     * @return array
     *
     */
    protected function get_form_data()
    {

        $data = array();

        foreach ($this->booking->get('fields') as $field) {

            if (in_array($field['type'], wpbs_get_excluded_fields())) {
                continue;
            }

            // Get value
            $value = (isset($field['user_value'])) ? $field['user_value'] : '';
            
            
            $value = wpbs_get_field_display_user_value($value);
            

            if ($field['type'] == 'textarea') {
                $value = nl2br($value);
            }


            $data[] = array(
                'label' => $this->get_translated_label($field),
                'value' => $value,
            );
        }

        return $data;

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
     * Get the calendar edirot
     *
     * @return string
     *
     */
    protected function calendar_editor()
    {

        $output = '';

        // Set start date
        $start_date = new DateTime();
        $start_date->setTimestamp(strtotime($this->booking->get('start_date')));

        // Set end date
        $end_date = new DateTime();
        $end_date->setTimestamp(strtotime($this->booking->get('end_date')));
        $end_date->modify('+1 day');

        // Set loop interval
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start_date, $interval, $end_date);

        $months = array();

        // Loop through dates
        foreach ($period as $date) {
            // Set the first day of the month
            if (!isset($months[$date->format('n')]['start'])) {
                $months[$date->format('n')]['start'] = $date->getTimestamp();
            }

            // Set the last day of the month
            $months[$date->format('n')]['end'] = $date->getTimestamp();
        }

        // Output Calendar Editor
        foreach ($months as $month => $days) {
            $month_object = DateTime::createFromFormat('!m', $month);
            $output .= '<h3>' . $month_object->format('F') . '</h3>';

            $calendar_args = array(
                'current_year' => date('Y', $days['start']),
                'current_month' => date('n', $days['start']),
                'booking_view' => true,
                'booking_start_date' => $days['start'],
                'booking_end_date' => $days['end'],
            );
            $calendar_editor_outputter = new WPBS_Calendar_Editor_Outputter($this->calendar, $calendar_args);
            $output .= $calendar_editor_outputter->get_display();
        }

        return $output;
    }

    /**
     * Get calendar legends as <option> tags
     *
     * @return string
     *
     */
    protected function get_legends_as_options()
    {

        $legend_items = wpbs_get_legend_items(array('calendar_id' => $this->calendar->get('id')));

        $output = '';
        foreach ($legend_items as $legend_item) {

            $output .= '<option value="' . esc_attr($legend_item->get('id')) . '">' . $legend_item->get('name') . '</option>';

        }

        return $output;
    }

    /**
     * Get the email addresses submitted in the form
     *
     * @return array
     *
     */
    protected function get_email_addresses()
    {

        $emails = array();

        foreach ($this->booking->get('fields') as $field) {
            if ($field['type'] != 'email') {
                continue;
            }

            if (empty($field['user_value'])) {
                continue;
            }

            $emails[] = $field['user_value'];
        }

        if (empty($emails)) {
            return false;
        }

        return $emails;

    }

    /**
     * Get the email addresses submitted in the form as <option> tags
     *
     * @return string
     *
     */
    protected function get_email_addresses_as_options()
    {

        $emails = $this->get_email_addresses();

        $output = '';

        if (!empty($emails)) {
            foreach ($emails as $email) {
                $output .= '<option value="' . $email . '">' . $email . '</option>';
            }
        }

        return $output;

    }

}
