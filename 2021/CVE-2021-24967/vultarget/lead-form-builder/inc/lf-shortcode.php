<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function lfb_lead_form_shortcode($atts) {
    $output = '';
    $pull_quote_atts = shortcode_atts(array(
        'form-id' => 'My Quote',
        'title' => 'Author',
            ), $atts);
    $this_form_id = wp_kses_post($pull_quote_atts['form-id']);
    $th_front_end_froms = new LFB_Front_end_FORMS();
    $output = $th_front_end_froms->lfb_show_front_end_forms($this_form_id);
    return $output;
}
add_shortcode('lead-form', 'lfb_lead_form_shortcode');
add_filter('widget_text', 'do_shortcode');
