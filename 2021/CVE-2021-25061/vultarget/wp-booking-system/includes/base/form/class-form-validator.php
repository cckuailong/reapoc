<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Form_Validator
{

    /**
     * The WPBS_Calendar
     *
     * @access protected
     * @var    WPBS_Calendar
     *
     */
    protected $calendar = null;

    /**
     * The WPBS_Form
     *
     * @access protected
     * @var    WPBS_Form
     *
     */
    protected $form = null;

    /**
     * The $_POST data
     *
     * @access protected
     * @var    array
     *
     */
    protected $form_data = null;

    /**
     * The form fields
     *
     * @access protected
     * @var    array
     *
     */
    protected $form_fields = null;

    /**
     * Store form errors
     *
     * @access protected
     * @var    bool
     *
     */
    protected $has_errors = null;

    /**
     * Form Language
     *
     * @access protected
     * @var    string
     *
     */
    protected $form_language = null;
    
    /**
     * Form Strings
     *
     * @access protected
     * @var    array
     *
     */
    protected $form_strings = null;

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
     * @param WPBS_Form     $form
     * @param array         $form_data
     * @param WPBS_Calendar $calendar
     *
     */
    public function __construct($form, $calendar, $form_data, $language)
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
         * Set plugin settings
         *
         */
        $this->plugin_settings = get_option('wpbs_settings', array());

        /**
         * Set the form fields
         *
         */
        $this->form_fields = $form->get('fields');

        /**
         * Set the form data
         *
         */
        parse_str($form_data, $this->form_data);

        /**
         * Set default form messages
         *
         */
        $this->form_strings = wpbs_form_default_strings();

        /**
         * Set the form language
         * 
         */
        $this->form_language = ($language == 'auto' ? wpbs_get_locale() : $language);

    }

    /**
     * Sanitize POST data
     *
     */
    public function sanitize_fields()
    {

        foreach ($this->form_fields as &$field) {
            // Skip empty fields
            if (
                !isset($this->form_data['wpbs-input-' . $this->form->get('id') . '-' . $field['id']]) ||
                empty($this->form_data['wpbs-input-' . $this->form->get('id') . '-' . $field['id']])
            ) {
                continue;
            }

            switch ($field['type']) {

                // Sanitize Textarea
                case 'textarea':
                    $value = $this->form_data['wpbs-input-' . $this->form->get('id') . '-' . $field['id']];
                    $value = stripslashes($value);
                    $value = sanitize_textarea_field($value);
                    $value = esc_textarea($value);
                    $field['user_value'] = $value;
                    break;

                // Sanitize Checkbox
                case 'checkbox':
                case 'product_checkbox':
                    foreach ($this->form_data['wpbs-input-' . $this->form->get('id') . '-' . $field['id']] as $option) {
                        $value = $option;
                        $value = stripslashes($value);
                        $value = sanitize_text_field($value);
                        $value = esc_attr($value);
                        $field['user_value'][] = $value;
                    }
                    break;

                // Sanitize everything else
                default:
                    $value = $this->form_data['wpbs-input-' . $this->form->get('id') . '-' . $field['id']];
                    $value = stripslashes($value);
                    $value = sanitize_text_field($value);
                    $value = esc_attr($value);
                    $field['user_value'] = $value;
            }
        }
    }

    /**
     * Validate POST data
     *
     */
    public function validate_fields()
    {
        foreach ($this->form_fields as &$field) {
            switch ($field['type']) {

                // Validate Email
                case 'email':

                    // If email is not required, still check if it's valid
                    if (!empty($field['user_value']) && !is_email($field['user_value'])) {
                        $field['error'] = $this->get_form_string('invalid_email');
                        $this->has_errors = true;
                    }

                    // Check if required
                    $field = $this->check_if_required_and_empty($field);
                    break;

                // Validate Captcha
                case 'captcha':
                    $field = $this->validate_recaptcha($field);
                    break;

                // Validate all other fields
                default:
                    $field = $this->check_if_required_and_empty($field);
            }
        }
    }

    /**
     * Validate Dates
     * 
     * @param array $form_args
     * @param array $calendar_args
     * 
     * @return bool
     *
     */
    public function validate_dates($form_args, $calendar_args)
    {

        // Check if dates are set.
        if (empty($calendar_args['start_date']) || empty($calendar_args['end_date'])) {
            $this->has_errors = true;
            $this->form_fields['form_error'] = $this->get_form_string('select_date');
            return false;
        }

        // Check if dates are valid
        if (!is_numeric($calendar_args['start_date']) || !is_numeric($calendar_args['end_date'])) {
            $this->has_errors = true;
            $this->form_fields['form_error'] = __('Invalid date.', 'wp-booking-system');
            return false;
        }

        // Convert JS timestamp to PHP Timestamp
        $calendar_args['start_date'] = wpbs_convert_js_to_php_timestamp($calendar_args['start_date']);
        $calendar_args['end_date'] = wpbs_convert_js_to_php_timestamp($calendar_args['end_date']);

        // Check if there is a miminum number of days.
        if (isset($form_args['minimum_days']) && $form_args['minimum_days'] != 0) {
            $difference = 1 + ($calendar_args['end_date'] - $calendar_args['start_date']) / DAY_IN_SECONDS;
            if ($difference < $form_args['minimum_days']) {
                $this->has_errors = true;
                $this->form_fields['form_error'] = sprintf($this->get_form_string('minimum_selection'), $form_args['minimum_days']);
                return false;
            }
        }

        // Check if there is a maximum number of days.
        if (isset($form_args['maximum_days']) && $form_args['maximum_days'] != 0) {
            $difference = 1 + ($calendar_args['end_date'] - $calendar_args['start_date']) / DAY_IN_SECONDS;
            if ($difference > $form_args['maximum_days']) {
                $this->has_errors = true;
                $this->form_fields['form_error'] = sprintf($this->get_form_string('maximum_selection'), $form_args['maximum_days']);
                return false;
            }
        }

        // Check if the booking must start on a specific day
        if (isset($form_args['booking_start_day']) && $form_args['booking_start_day'] != 0) {
            if($form_args['booking_start_day'] != date('N', $calendar_args['start_date'])){
                $this->has_errors = true;
                $this->form_fields['form_error'] = sprintf($this->get_form_string('start_day'), wpbs_get_weekdays()[$form_args['booking_start_day'] - 1]);
                return false;
            }
        }

        // Check if the booking must end on a specific day
        if (isset($form_args['booking_end_day']) && $form_args['booking_end_day'] != 0) {
            if($form_args['booking_end_day'] != date('N', $calendar_args['end_date'])){
                $this->has_errors = true;
                $this->form_fields['form_error'] = sprintf($this->get_form_string('end_day'), wpbs_get_weekdays()[$form_args['booking_end_day'] - 1]);
                return false;
            }
        }


        /**
         * Check if dates are available
         */

        // Get un-boobakle legend items
        $unbookable_legend_ids = array();
        $legend_items = wpbs_get_legend_items(array('calendar_id' => $this->calendar->get('id')));
        foreach ($legend_items as $legend_item) {
            if ($legend_item->get('is_bookable') == 1) {
                continue;
            }
            $unbookable_legend_ids[] = $legend_item->get('id');
        }

        // Check events
        $events = wpbs_get_events(array('calendar_id' => $this->calendar->get('id')));

        foreach ($events as $event) {
            //Get the timestamp
            $event_timestamp = mktime(0, 0, 0, $event->get('date_month'), $event->get('date_day'), $event->get('date_year'));

            //If out of selection range, no need to check
            if ($event_timestamp < $calendar_args['start_date'] || $event_timestamp > $calendar_args['end_date']) {
                continue;
            }

            //Check if events are all bookable
            if (in_array($event->get('legend_item_id'), $unbookable_legend_ids)) {
                $this->has_errors = true;
                $this->form_fields['form_error'] = __('It appears you have selected an unavailable date. Please refresh the calendar and try again.', 'wp-booking-system');
                return false;
            }
        }
    }

    /**
     * Set the general error message if validation failed.
     * 
     */
    public function set_error_message(){
        if($this->has_errors == true && empty($this->form_fields['form_error'])){
            $this->form_fields['form_error'] = $this->get_form_string('validation_errors');
        }
    }

    /**
     * Check if there are any errors
     *
     * @return bool
     *
     */
    public function get_form_string($key)
    {
        // Check for translation
        if(!empty( wpbs_get_form_meta($this->form->get('id'), 'form_strings_' . $key . '_translation_' . $this->form_language, true) )){
            return wpbs_get_form_meta($this->form->get('id'), 'form_strings_' . $key . '_translation_' . $this->form_language, true);
        }

        // Check for default
        if(!empty( wpbs_get_form_meta($this->form->get('id'), 'form_strings_' . $key, true) )){
            return wpbs_get_form_meta($this->form->get('id'), 'form_strings_' . $key, true);
        }

        return $this->form_strings[$key];
    }

    /**
     * Check if there are any errors
     *
     * @return bool
     *
     */
    public function has_errors()
    {
        return $this->has_errors;
    }

    /**
     * Returns the form fields populated with errors and POST data
     *
     * @return array
     *
     */
    public function get_form_fields()
    {
        return $this->form_fields;
    }

    /**
     * Check if a field is required and validates it
     *
     * @param array $field
     *
     * @return array
     *
     */
    protected function check_if_required_and_empty($field)
    {
        if (
            (isset($field['values']['default']['required']) && $field['values']['default']['required'] == 'on')
            ||
            $field['type'] == 'payment_method'
            ) {
            if (empty($field['user_value'])) {
                $field['error'] = $this->get_form_string('required_field');
                $this->has_errors = true;
            }
        }
        return $field;
    }

    /**
     * Check if the reCAPTCHA v2 is valid
     *
     * @param array $field
     *
     * @return array
     *
     */
    protected function validate_recaptcha($field)
    {

        // Check if we're on a payment confirmation screen. The form is rendered again, but captcha doesn't need to be validated.
        $payment_confirmation_screen = apply_filters('wpbs_validate_recaptcha_payment_confirmation', false, $this->form_data);
        if($payment_confirmation_screen === true){
            return $field;
        }

        $captcha = $this->form_data['g-recaptcha-response'];

        if (empty($captcha)) {
            $field['error'] = $this->get_form_string('required_field');
            $this->has_errors = true;
            return $field;
        }

        $recaptcha_secret_key = (isset($this->plugin_settings['recaptcha_v2_secret_key'])) ? $this->plugin_settings['recaptcha_v2_secret_key'] : '';
        $recaptcha_response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret_key . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
        if ($recaptcha_response['success'] != true) {
            $field['error'] = implode(', ', $recaptcha_response['error-codes']);
            $this->has_errors = true;
        }

        return $field;
    }

}
