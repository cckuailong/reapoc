<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class WPBS_Shortcodes
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {

        // Register the single calendar shortcode
        add_shortcode('wpbs', array(__CLASS__, 'single_calendar'));

        

    }

    /**
     * The callback for the WPBS single calendar shortcode
     *
     * @param array $atts
     *
     */
    public static function single_calendar($atts)
    {

        // Shortcode default attributes
        $default_atts = array(
            // Calendar
            'id' => 0,
            'title' => 'yes',
            'legend' => 'yes',
            'dropdown' => 'yes',
            'start' => 1,
            'display' => 1,
            
            'language' => 'auto',

            // Form
            'form_id' => 0,
            // Form backwards compatibility
            'form' => null,
            

        );

        // Shortcode attributes
        $atts = shortcode_atts($default_atts, $atts);

        /**
         * Calendar Args
         */

        // Calendar outputter default arguments
        $default_calendar_args = wpbs_get_calendar_output_default_args();

        // Translating values from the shortcode attributes to the calendar arguments
        $calendar_args = array(
            'show_title' => (!empty($atts['title']) && $atts['title'] == 'yes' ? 1 : 0),
            'show_legend' => (!empty($atts['legend']) && $atts['legend'] == 'yes' ? 1 : 0),
            'language' => ($atts['language'] == 'auto' ? wpbs_get_locale() : $atts['language']),
        );


        // Calendar arguments
        $calendar_args = wp_parse_args($calendar_args, $default_calendar_args);

        // Calendar id
        $calendar_id = (!empty($atts['id']) ? (int) $atts['id'] : 0);

        // Calendar
        $calendar = wpbs_get_calendar($calendar_id);

        /**
         * Form Args
         */

        // Form outputter default arguments
        $default_form_args = wpbs_get_form_output_default_args();

       
        // Translating values from the shortcode attributes to the form arguments
        $form_args = array(
            
            'language' => ($atts['language'] == 'auto' ? wpbs_get_locale() : $atts['language']),
        );

        // Form arguments
        $form_args = wp_parse_args($form_args, $default_form_args);

        // Form id
        $form_id = (!empty($atts['form']) ? (int) $atts['form'] : ((!empty($atts['form_id']) ? (int) $atts['form_id'] : 0)));

        if (is_null($calendar)) {

            $output = '<p>' . __('Calendar does not exist.', 'wp-booking-system') . '</p>';

        } else {

            $output = '<div class="wpbs-main-wrapper wpbs-main-wrapper-calendar-' . $calendar_id . ' wpbs-main-wrapper-form-' . $form_id . '">';

            // Initialize the calendar outputter
            $calendar_outputter = new WPBS_Calendar_Outputter($calendar, $calendar_args);

            $output .= $calendar_outputter->get_display();

            if ($form_id !== 0) {

                // Form
                $form = wpbs_get_form($form_id);

                if (is_null($form)) {

                    $output .= '<p>' . __('Form does not exist.', 'wp-booking-system') . '</p>';

                } else {

                    // Initialize the form outputter
                    $form_outputter = new WPBS_Form_Outputter($form, $form_args, array(), $calendar_id);
                    $output .= $form_outputter->get_display();

                }
            }

            $output .= '</div>';

        }

        return $output;

    }

}

// Init shortcodes
new WPBS_Shortcodes();
