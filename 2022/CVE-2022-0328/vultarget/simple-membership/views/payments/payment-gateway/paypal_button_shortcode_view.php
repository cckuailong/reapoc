<?php

/* * ************************************************
 * PayPal Buy Now button shortcode handler
 * *********************************************** */
add_filter('swpm_payment_button_shortcode_for_pp_buy_now', 'swpm_render_pp_buy_now_button_sc_output', 10, 2);

function swpm_render_pp_buy_now_button_sc_output($button_code, $args) {

    $button_id = isset($args['id']) ? $args['id'] : '';
    if (empty($button_id)) {
        return '<p class="swpm-red-box">Error! swpm_render_pp_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';
    }

    //Check new_window parameter
    $window_target = isset($args['new_window']) ? 'target="_blank"' : '';

    $settings = SwpmSettings::get_instance();
    $button_cpt = get_post($button_id); //Retrieve the CPT for this button
    
    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);
    //Verify that this membership level exists (to prevent user paying for a level that has been deleted)
    if(!SwpmUtils::membership_level_id_exists($membership_level_id)){
        return '<p class="swpm-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
    }
    
    $paypal_email = get_post_meta($button_id, 'paypal_email', true);
    $payment_amount = get_post_meta($button_id, 'payment_amount', true);
    if (!is_numeric($payment_amount)) {
        return '<p class="swpm-red-box">Error! The payment amount value of the button must be a numeric number. Example: 49.50 </p>';
    }
    $payment_amount = round($payment_amount, 2); //round the amount to 2 decimal place.   
    $payment_currency = get_post_meta($button_id, 'payment_currency', true);

    $sandbox_enabled = $settings->get_value('enable-sandbox-testing');
    $notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_ipn=1';
    $return_url = get_post_meta($button_id, 'return_url', true);
    if (empty($return_url)) {
        $return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
    }
    $cancel_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;

    $user_ip = SwpmUtils::get_user_ip_address();
    $_SESSION['swpm_payment_button_interaction'] = $user_ip;
    
    //Custom field data
    $custom_field_value = 'subsc_ref=' . $membership_level_id;    
    $custom_field_value .= '&user_ip=' . $user_ip;
    if (SwpmMemberUtils::is_member_logged_in()) {
        $custom_field_value .= '&swpm_id=' . SwpmMemberUtils::get_logged_in_members_id();
    }
    $custom_field_value = apply_filters('swpm_custom_field_value_filter', $custom_field_value);

    /* === PayPal Buy Now Button Form === */
    $output = '';
    $output .= '<div class="swpm-button-wrapper swpm-pp-buy-now-wrapper">';
    $uniqid=uniqid();
    //apply filter to output additional form fields
    $coupon_input='';
    $coupon_input = apply_filters('swpm_payment_form_additional_fields',$coupon_input,$button_id,$uniqid);
        if (!empty($coupon_input)) {
            $output.=$coupon_input;
        }
    if ($sandbox_enabled) {
        $action_url='https://www.sandbox.paypal.com/cgi-bin/webscr';
    } else {
        $action_url='https://www.paypal.com/cgi-bin/webscr';
    }

    $output.=sprintf('<form id="swpm-paypal-payment-form-%s" action="%s" method="post" %s>',$uniqid,$action_url,$window_target);

    $output .= '<input type="hidden" name="cmd" value="_xclick" />';
    $output .= '<input type="hidden" name="charset" value="utf-8" />';
    $output .= '<input type="hidden" name="bn" value="TipsandTricks_SP" />';
    $output .= '<input type="hidden" name="business" value="' . apply_filters('swpm_buy_now_button_paypal_email',$paypal_email) . '" />';
    $output .= '<input type="hidden" name="amount" value="' . $payment_amount . '" />';
    $output .= '<input type="hidden" name="currency_code" value="' . $payment_currency . '" />';
    $output .= '<input type="hidden" name="item_number" value="' . $button_id . '" />';
    $output .= '<input type="hidden" name="item_name" value="' . htmlspecialchars($button_cpt->post_title) . '" />';

    $output .= '<input type="hidden" name="no_shipping" value="1" />'; //Do not prompt for an address

    $output .= '<input type="hidden" name="notify_url" value="' . $notify_url . '" />';
    $output .= '<input type="hidden" name="return" value="' . $return_url . '" />';
    $output .= '<input type="hidden" name="cancel_return" value="' . $cancel_url . '" />';

    $custom_field_value = urlencode($custom_field_value);//URL encode the custom field value so nothing gets lost when it is passed around.
    $output .= '<input type="hidden" name="custom" value="' . $custom_field_value . '" />';

    $checkout_logo_image_url = get_post_meta($button_id, 'checkout_logo_image_url', true);
    if (!empty($checkout_logo_image_url)) {
        $output .= '<input type="hidden" name="image_url" value="' . $checkout_logo_image_url . '" />';
    }
    
    //Filter to add additional payment input fields to the form (example: langauge code or country code etc).
    $output .= apply_filters('swpm_pp_payment_form_additional_fields', '');
    
    $button_image_url = get_post_meta($button_id, 'button_image_url', true);
    if (!empty($button_image_url)) {
        $output .= '<input type="image" src="' . $button_image_url . '" class="swpm-buy-now-button-submit" alt="' . SwpmUtils::_('Buy Now') . '"/>';
    } else {
        $button_text = (isset($args['button_text'])) ? $args['button_text'] : SwpmUtils::_('Buy Now');
        $output .= '<input type="submit" class="swpm-buy-now-button-submit" value="' . $button_text . '" />';
    }

    $output .= '</form>'; //End .form
    $output .= '</div>'; //End .swpm_button_wrapper

    return $output;
}

/* * ************************************************
 * PayPal subscription button shortcode handler 
 * *********************************************** */
add_filter('swpm_payment_button_shortcode_for_pp_subscription', 'swpm_render_pp_subscription_button_sc_output', 10, 2);

function swpm_render_pp_subscription_button_sc_output($button_code, $args) {

    $button_id = isset($args['id']) ? $args['id'] : '';
    if (empty($button_id)) {
        return '<p style="color: red;">Error! swpm_render_pp_subscription_button_sc_output() function requires the button ID value to be passed to it.</p>';
    }

    //Check new_window parameter
    $window_target = isset($args['new_window']) ? 'target="_blank"' : '';

    $settings = SwpmSettings::get_instance();
    $button_cpt = get_post($button_id); //Retrieve the CPT for this button
    
    $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);
    //Verify that this membership level exists (to prevent user paying for a level that has been deleted)
    if(!SwpmUtils::membership_level_id_exists($membership_level_id)){
        return '<p class="swpm-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
    }
    
    $paypal_email = get_post_meta($button_id, 'paypal_email', true);
    $payment_currency = get_post_meta($button_id, 'payment_currency', true);

    //Subscription payment details
    $billing_amount = get_post_meta($button_id, 'billing_amount', true);
    if (!is_numeric($billing_amount)) {
        return '<p style="color: red;">Error! The billing amount value of the button must be a numeric number. Example: 49.50 </p>';
    }
    $billing_amount = round($billing_amount, 2); //round the amount to 2 decimal place.  
    $billing_cycle = get_post_meta($button_id, 'billing_cycle', true);
    $billing_cycle_term = get_post_meta($button_id, 'billing_cycle_term', true);
    $billing_cycle_count = get_post_meta($button_id, 'billing_cycle_count', true);
    $billing_reattempt = get_post_meta($button_id, 'billing_reattempt', true);

    //Trial billing details
    $trial_billing_amount = get_post_meta($button_id, 'trial_billing_amount', true);
    if (!empty($trial_billing_amount)) {
        if(!is_numeric($trial_billing_amount)){
            return '<p style="color: red;">Error! The trial billing amount value of the button must be a numeric number. Example: 19.50 </p>';
        }
    }
    $trial_billing_cycle = get_post_meta($button_id, 'trial_billing_cycle', true);
    $trial_billing_cycle_term = get_post_meta($button_id, 'trial_billing_cycle_term', true);

    $sandbox_enabled = $settings->get_value('enable-sandbox-testing');
    $notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_ipn=1';
    $return_url = get_post_meta($button_id, 'return_url', true);
    if (empty($return_url)) {
        $return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
    }
    $cancel_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;

    $user_ip = SwpmUtils::get_user_ip_address();
    $_SESSION['swpm_payment_button_interaction'] = $user_ip;
    
    //Custom field data
    $custom_field_value = 'subsc_ref=' . $membership_level_id;
    $custom_field_value .= '&user_ip=' . $user_ip;
    if (SwpmMemberUtils::is_member_logged_in()) {
        $custom_field_value .= '&swpm_id=' . SwpmMemberUtils::get_logged_in_members_id();
    }
    $custom_field_value = apply_filters('swpm_custom_field_value_filter', $custom_field_value);

    /* === PayPal Subscription Button Form === */
    $output = '';
    $output .= '<div class="swpm-button-wrapper swpm-pp-subscription-wrapper">';
    if ($sandbox_enabled) {
        $output .= '<form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" ' . $window_target . '>';
    } else {
        $output .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" ' . $window_target . '>';
    }

    $output .= '<input type="hidden" name="cmd" value="_xclick-subscriptions" />';
    $output .= '<input type="hidden" name="charset" value="utf-8" />';
    $output .= '<input type="hidden" name="bn" value="TipsandTricks_SP" />';
    $output .= '<input type="hidden" name="business" value="' . apply_filters('swpm_subscription_button_paypal_email',$paypal_email) . '" />';
    $output .= '<input type="hidden" name="currency_code" value="' . $payment_currency . '" />';
    $output .= '<input type="hidden" name="item_number" value="' . $button_id . '" />';
    $output .= '<input type="hidden" name="item_name" value="' . htmlspecialchars($button_cpt->post_title) . '" />';

    //Check trial billing
    if (!empty($trial_billing_cycle)) {
        $output .= '<input type="hidden" name="a1" value="' . $trial_billing_amount . '" /><input type="hidden" name="p1" value="' . $trial_billing_cycle . '" /><input type="hidden" name="t1" value="' . $trial_billing_cycle_term . '" />';
    }
    //Main subscription billing
    if (!empty($billing_cycle)) {
        $output .= '<input type="hidden" name="a3" value="' . $billing_amount . '" /><input type="hidden" name="p3" value="' . $billing_cycle . '" /><input type="hidden" name="t3" value="' . $billing_cycle_term . '" />';
    }
    //Re-attempt on failure
    if ($billing_reattempt != '') {
        $output .= '<input type="hidden" name="sra" value="1" />';
    }
    //Reccurring times
    if ($billing_cycle_count > 1) { //do not include srt value if billing cycle count set to 1 or a negetive number.
        $output .= '<input type="hidden" name="src" value="1" /><input type="hidden" name="srt" value="' . $billing_cycle_count . '" />';
    } else if (empty($billing_cycle_count)) {
        $output .= '<input type="hidden" name="src" value="1" />';
    }

    //Other required data
    $output .= '<input type="hidden" name="no_shipping" value="1" />'; //Do not prompt for an address    
    $output .= '<input type="hidden" name="notify_url" value="' . $notify_url . '" />';
    $output .= '<input type="hidden" name="return" value="' . $return_url . '" />';
    $output .= '<input type="hidden" name="cancel_return" value="' . $cancel_url . '" />';
    
    $custom_field_value = urlencode($custom_field_value);//URL encode the custom field value so nothing gets lost when it is passed around.
    $output .= '<input type="hidden" name="custom" value="' . $custom_field_value . '" />';

    $checkout_logo_image_url = get_post_meta($button_id, 'checkout_logo_image_url', true);
    if (!empty($checkout_logo_image_url)) {
        $output .= '<input type="hidden" name="image_url" value="' . $checkout_logo_image_url . '" />';
    }
    
    //Filter to add additional payment input fields to the form (example: langauge code or country code etc).
    $output .= apply_filters('swpm_pp_payment_form_additional_fields', '');
            
    //Submit button
    $button_image_url = get_post_meta($button_id, 'button_image_url', true);
    if (!empty($button_image_url)) {
        $output .= '<input type="image" src="' . $button_image_url . '" class="swpm-subscription-button-submit" alt="' . SwpmUtils::_('Subscribe Now') . '"/>';
    } else {
        $button_text = (isset($args['button_text'])) ? $args['button_text'] : SwpmUtils::_('Subscribe Now');
        $output .= '<input type="submit" class="swpm-subscription-button-submit" value="' . $button_text . '" />';
    }

    $output .= '</form>'; //End .form
    $output .= '</div>'; //End .swpm_button_wrapper

    return $output;
}
