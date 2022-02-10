<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Booking_Mailer extends WPBS_Mailer
{

    /**
     * The $_POST data
     *
     * @access protected
     * @var    array
     *
     */
    protected $post_data;

    /**
     * The booking
     *
     * @access protected
     * @var    WPBS_Booking
     *
     */
    protected $booking;

    public function __construct($booking, $post_data)
    {

        /**
         * Set booking
         *
         */
        $this->booking = $booking;

        /**
         * Set the form fields
         *
         */
        $this->post_data = $post_data;

    }

    /**
     * Prepare the email fields
     *
     * @param string $type
     *
     */
    public function prepare($type)
    {
        // Check if $type is a valid notification type
        if (!in_array($type, array('accept_booking', 'customer'))) {
            return false;
        }

        // Set Fields
        $this->send_to = $this->get_field('send_to', $type);
        $this->from_name = $this->get_field('from_name', $type);
        $this->from_email = $this->get_field('from_email', $type);
        $this->reply_to = $this->get_field('reply_to', $type);
        $this->subject = $this->get_field('subject', $type);
        $this->message = nl2br($this->get_field('message', $type));

        if ($this->get_field('include_booking_details', $type) !== null && !empty($this->get_field('include_booking_details', $type))) {
            $this->include_booking_details();
        }

    }

    /**
     * Helper function to get the value of a field
     *
     * @param string $field
     * @param string $type
     *
     * @return string
     *
     */
    protected function get_field($field, $type)
    {
        if (isset($this->post_data['booking_email_' . $type . '_' . $field])) {
            if ($field == 'message') {
                return $this->post_data['booking_email_' . $type . '_' . $field];
            } else {
                return esc_attr($this->post_data['booking_email_' . $type . '_' . $field]);
            }
        }

    }

    /**
     * Helper function to include the booking details
     *
     * @param string $field
     * @param string $type
     *
     * @return string
     *
     */
    protected function include_booking_details()
    {
        $this->message .= '<br /><br />';

        $this->message .= '<h3>' . __('Your booking details') . '</h3>';

        $this->message .= '<br /><br />';

        $this->message .= '<strong>' . __('Booking ID', 'wp-booking-system') . '</strong>: #' . $this->booking->get('id') . '<br>';
        $this->message .= '<strong>' . __('Start Date', 'wp-booking-system') . '</strong>: ' . date(get_option('date_format'), strtotime($this->booking->get('start_date'))) . '<br>';
        $this->message .= '<strong>' . __('End Date', 'wp-booking-system') . '</strong>: ' . date(get_option('date_format'), strtotime($this->booking->get('end_date'))) . '<br>';
        $this->message .= '<strong>' . __('Booked on', 'wp-booking-system') . '</strong>: ' . date(get_option('date_format'), strtotime($this->booking->get('date_created'))) . '<br>';

       


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


            $this->message .= '<strong>' . $field['values']['default']['label'] . '</strong>: ' . $value . '<br>';

        }
    }

}
