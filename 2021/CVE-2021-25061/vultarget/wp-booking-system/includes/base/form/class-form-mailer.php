<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Form_Mailer extends WPBS_Mailer
{

    /**
     * The WPBS_Form
     *
     * @access protected
     * @var    WPBS_Form
     *
     */
    protected $form = null;

    /**
     * The WPBS_Calendar
     *
     * @access protected
     * @var    WPBS_Calendar
     *
     */
    protected $calendar = null;

    /**
     * The booking id
     *
     * @access protected
     * @var    int
     *
     */
    protected $booking_id = null;

    /**
     * The form fields
     *
     * @access protected
     * @var    array
     *
     */
    protected $form_fields = null;

    /**
     * The language of the email
     *
     * @access protected
     * @var    string
     *
     */
    protected $language;

    /**
     * Booking Start Date
     *
     * @access protected
     * @var    string
     *
     */
    protected $booking_start_date;

    /**
     * Booking End Date
     *
     * @access protected
     * @var    string
     *
     */
    protected $booking_end_date;

    /**
     * Constructor
     *
     * @param WPBS_Form $form
     * @param array     $args
     *
     */
    public function __construct($form, $calendar, $booking_id, $form_fields, $language, $booking_start_date, $booking_end_date)
    {

        /**
         * Set the form
         *
         */
        $this->form = $form;

        /**
         * Set the calendar
         *
         */
        $this->calendar = $calendar;

        /**
         * Set the booking id
         *
         */
        $this->booking_id = $booking_id;

        /**
         * Set the form fields
         *
         */
        $this->form_fields = $form_fields;

        /**
         * Set the language
         *
         */
        $this->language = $language;

        /**
         * Set the booking dates
         *
         */
        $this->booking_start_date = $booking_start_date;
        $this->booking_end_date = $booking_end_date;

    }

    public function prepare($type)
    {
        // Check if $type is a valid notification type
        if (!in_array($type, array('user', 'admin'))) {
            return false;
        }

        // Check if notification is enabled
        $notification = $this->get_field('enable', $type);
        if ($notification != 'on') {
            return false;
        }

        // Set Fields
        $this->send_to = $this->parse_email_tags($this->get_field('send_to', $type));
        $this->from_name = $this->parse_email_tags($this->get_field('from_name', $type));
        $this->from_email = $this->parse_email_tags($this->get_field('from_email', $type));
        $this->reply_to = $this->parse_email_tags($this->get_field('reply_to', $type));
        $this->subject = $this->parse_email_tags($this->get_field('subject', $type));
        $this->message = $this->parse_email_tags(nl2br($this->get_field('message', $type)));

    }

    /**
     * Replaces the email tags with the correct values submitted in the form.
     *
     * @param string $text
     *
     * @return string
     *
     */
    protected function parse_email_tags($text)
    {
        // Exit if $text is empty
        if (empty($text)) {
            return false;
        }

        // Get email tags
        $tags = wpbs_form_get_email_tags($text);

        // Loop through them
        if ($tags) {
            foreach ($tags as $tag) {

                // Get the id of the tag
                $tag_id = wpbs_form_get_email_tag_id($tag);

                switch ($tag_id) {
                    case 'All Fields':
                        $all_fields = '';

                        $all_fields .= '<strong>' . __('Booking ID', 'wp-booking-system') . '</strong>: ' .  $this->booking_id . '<br>';
                        // Add Dates
                        $all_fields .= '<strong>' . __('Start Date', 'wp-booking-system') . '</strong>: ' . date(get_option('date_format'), $this->booking_start_date) . '<br>';
                        $all_fields .= '<strong>' . __('End Date', 'wp-booking-system') . '</strong>: ' . date(get_option('date_format'), $this->booking_end_date) . '<br>';

                        

                        // Loop through fields
                        foreach ($this->form_fields as $form_field) {

                            if (in_array($form_field['type'], wpbs_get_excluded_fields())) {
                                continue;
                            }

                            // Get the value
                            $value = (isset($form_field['user_value'])) ? $form_field['user_value'] : '';

                           
                            $value = wpbs_get_field_display_user_value($value);

                            if ($form_field['type'] == 'textarea') {
                                $value = nl2br($value);
                            }

                           

                            $field_name = $this->get_form_field_translation($form_field['values'], 'label');

                            $all_fields .= '<strong>' . $field_name . '</strong>: ' . $value . '<br>';
                        }
                        $text = str_replace($tag, $all_fields, $text);
                        break;
                    case 'Start Date':
                        $text = str_replace($tag, date(get_option('date_format'), $this->booking_start_date), $text);
                        break;
                    case 'End Date':
                        $text = str_replace($tag, date(get_option('date_format'), $this->booking_end_date), $text);
                        break;
                    case 'Calendar Title':
                        $calendar_name = wpbs_get_calendar_meta($this->calendar->get('id'), 'calendar_name_translation_' . $this->language, true);
                        if (empty($calendar_name)) {
                            $calendar_name = $this->calendar->get('name');
                        }
                        $text = str_replace($tag, $calendar_name, $text);
                        break;
                    

                    default:
                        // Dynamic Field

                        // Search for the matching form field
                        foreach ($this->form_fields as $form_field) {
                            // Skip of not the one we're looking for
                            if ($form_field['id'] != $tag_id) {
                                continue;
                            }

                            // Get the value
                            $value = (isset($form_field['user_value'])) ? $form_field['user_value'] : '';


                            $value = wpbs_get_field_display_user_value($value);

                            if ($form_field['type'] == 'textarea') {
                                $value = nl2br($value);
                            }


                            // If found, replace with form value
                            $text = str_replace($tag, $value, $text);
                        }
                }

            }
        }

        return $text;
    }

    /**
     * Helper function to get the translated value of a field
     *
     * @param string $field
     * @param string $type
     *
     * @return string
     *
     */
    protected function get_field($field, $type)
    {
        return wpbs_get_translated_form_meta($this->form->get('id'), $type . '_notification_' . $field, $this->language);
    }

    /**
     * Helper function to get translations
     *
     * @param array $values
     * @param string $key
     *
     * @return string
     *
     */
    protected function get_form_field_translation($values, $key)
    {
        if (array_key_exists($this->language, $values) && !empty($values[$this->language][$key])) {
            return $values[$this->language][$key];
        }

        return $values['default'][$key];
    }

}
