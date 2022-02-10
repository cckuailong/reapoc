<?php

function wpam_display_addons_menu()
{
    echo '<div class="wrap">';
    echo '<h2>' .__('Affiliates Manager Add-ons', 'affiliates-manager') . '</h2>';
    echo '<link type="text/css" rel="stylesheet" href="' . WPAM_URL . '/style/wpam-addons-listing.css" />' . "\n";
    
    $addons_data = array();

    $addon_1 = array(
        'name' => 'MailChimp Integration',
        'thumbnail' => WPAM_URL . '/images/addons/mailchimp-integration.png',
        'description' => 'Allows you to signup the affiliates to your MailChimp list after registration',
        'page_url' => 'https://wpaffiliatemanager.com/signup-affiliates-mailchimp-list/',
    );
    array_push($addons_data, $addon_1);

    $addon_2 = array(
        'name' => 'Google reCAPTCHA',
        'thumbnail' => WPAM_URL . '/images/addons/google-recaptcha.png',
        'description' => 'Allows you to add Google recaptcha to your affiliate signup page. Helps prevent spam signup.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-google-recaptcha-integration/',
    );
    array_push($addons_data, $addon_2);

    $addon_3 = array(
        'name' => 'Mailpoet Newsletter',
        'thumbnail' => WPAM_URL . '/images/addons/mailpoet-integration.png',
        'description' => 'You can automatically sign up your affiliates to a specific MailPoet newsletter list.',
        'page_url' => 'https://wpaffiliatemanager.com/sign-affiliates-to-mailpoet-list/',
    );
    array_push($addons_data, $addon_3);
    
    $addon_4 = array(
        'name' => 'Referral Bonus',
        'thumbnail' => WPAM_URL . '/images/addons/affiliate-referral-bonus-addon.png',
        'description' => 'Award bonus commission to an affiliate when they refer another affiliate to your site.',
        'page_url' => 'https://wpaffiliatemanager.com/award-bonus-commission-for-referring-an-affiliate/',
    );
    array_push($addons_data, $addon_4);

    $addon_5 = array(
        'name' => 'Simple Membership',
        'thumbnail' => WPAM_URL . '/images/addons/simple-membership-integration.png',
        'description' => 'Integrates with the Simple Membership plugin so you can reward affiliates for sending members.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-and-simple-membership-integration/',
    );
    array_push($addons_data, $addon_5);
    
    $addon_6 = array(
        'name' => 'Sell Digital Downloads',
        'thumbnail' => WPAM_URL . '/images/addons/sdd-plugin-integraton.png',
        'description' => 'Integrates with sell digital downloads plugin so you can reward affiliates for sending customers.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-sell-digital-downloads-integration/',
    );
    array_push($addons_data, $addon_6);
    
    $addon_7 = array(
        'name' => 'WP eStore Integration',
        'thumbnail' => WPAM_URL . '/images/addons/wp-estore-plugin.png',
        'description' => 'Integrates with WP eStore plugin so you can reward affiliates for sending customer.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-wp-estore-integration/',
    );
    array_push($addons_data, $addon_7);
    
    $addon_8 = array(
        'name' => 'WooCommerce Affiliates',
        'thumbnail' => WPAM_URL . '/images/addons/woo-affiliates.png',
        'description' => 'Automatically create affiliate accounts for your WooCommerce customers.',
        'page_url' => 'https://wpaffiliatemanager.com/automatically-create-affiliate-account-woocommerce-customers/',
    );
    array_push($addons_data, $addon_8);
    
    $addon_9 = array(
        'name' => 'Woo Subscription',
        'thumbnail' => WPAM_URL . '/images/addons/woo-subscriptions.png',
        'description' => 'Integrate with the subscription addon so you can award affiliate commissions for recurring payments.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-woocommerce-subscription-integration/',
    );
    array_push($addons_data, $addon_9);
    
    $addon_10 = array(
        'name' => 'WooCommerce Coupons',
        'thumbnail' => WPAM_URL . '/images/addons/woo-coupons.png',
        'description' => 'Track affiliate commission via coupons/discount codes configured in your WooCommerce plugin',
        'page_url' => 'https://wpaffiliatemanager.com/tracking-affiliate-commission-using-woocommerce-coupons-or-discount-codes/',
    );
    array_push($addons_data, $addon_10);

    $addon_11 = array(
        'name' => 'Woo Product Commission',
        'thumbnail' => WPAM_URL . '/images/addons/woo-product-specific-commission.png',
        'description' => 'Allows you to configure product specific commission for your WooCommerce products.',
        'page_url' => 'https://wpaffiliatemanager.com/product-specific-affiliate-commission-for-woocommerce-products/',
    );
    array_push($addons_data, $addon_11);
    
    $addon_12 = array(
        'name' => 'GetResponse Integration',
        'thumbnail' => WPAM_URL . '/images/addons/getresponse-integration-addon.png',
        'description' => 'Automatically signup your affiliates to a specific list in your GetResponse account',
        'page_url' => 'https://wpaffiliatemanager.com/signup-affiliates-getresponse-list/',
    );
    array_push($addons_data, $addon_12);

    $addon_13 = array(
        'name' => 'S2member Integration',
        'thumbnail' => WPAM_URL . '/images/addons/s2member-integration.png',
        'description' => 'Integrates with s2member plugin so you can reward affiliates for sending members.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-s2member-integration/',
    );
    array_push($addons_data, $addon_13);
        
    $addon_14 = array(
        'name' => 'PMPRO Integration',
        'thumbnail' => WPAM_URL . '/images/addons/pmpro-integration.png',
        'description' => 'Integrates with paid membership pro plugin so you can reward affiliates for sending members.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-paid-memberships-pro-integration/',
    );
    array_push($addons_data, $addon_14);
    
    $addon_15 = array(
        'name' => 'Infusionsoft Integration',
        'thumbnail' => WPAM_URL . '/images/addons/infusionsoft-integration.png',
        'description' => 'Automatically signup your affiliates to a specific tag in Your Infusionsoft account.',
        'page_url' => 'https://wpaffiliatemanager.com/infusionsoft-affiliates-manager-plugin-integration/',
    );
    array_push($addons_data, $addon_15);
    
    $addon_16 = array(
        'name' => 'Optimizemember',
        'thumbnail' => WPAM_URL . '/images/addons/optimizemember-integration.png',
        'description' => 'Integrates with optimizemember plugin so you can reward affiliates for sending members.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-and-optimizemember-plugin-integration/',
    );
    array_push($addons_data, $addon_16);
    
    $addon_17 = array(
        'name' => 'Woo Revenue Share',
        'thumbnail' => WPAM_URL . '/images/addons/woo-revenue-share.png',
        'description' => 'Allows you to share revenue with partners for your WooCommerce products.',
        'page_url' => 'https://wpaffiliatemanager.com/share-revenue-partner-woocommerce-products/',
    );
    array_push($addons_data, $addon_17);

    $addon_18 = array(
        'name' => 'Gravity Forms PayPal',
        'thumbnail' => WPAM_URL . '/images/addons/gravity-forms-paypal-addon.png',
        'description' => 'Integrates with Gravity Forms PayPal Addon so you can reward affiliates for sending customers.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-gravity-forms-paypal-integration/',
    );
    array_push($addons_data, $addon_18);
    
    $addon_19 = array(
        'name' => 'Gravity Forms Stripe',
        'thumbnail' => WPAM_URL . '/images/addons/gravity-forms-stripe-addon.png',
        'description' => 'Integrates with Gravity Forms Stripe Addon so you can reward affiliates for sending customers.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-gravity-forms-stripe-integration/',
    );
    array_push($addons_data, $addon_19);
     
    $addon_20 = array(
        'name' => 'WishList Member Addon',
        'thumbnail' => WPAM_URL . '/images/addons/wishlist-member-integration.png',
        'description' => 'Integrates with Wishlist member plugin so you can reward affiliates for sending members.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-and-wishlist-member-plugin-integration/',
    );
    array_push($addons_data, $addon_20);
    
    $addon_21 = array(
        'name' => 'AWeber Integration',
        'thumbnail' => WPAM_URL . '/images/addons/aweber-integration.png',
        'description' => 'Automatically signup your affiliates to a specific list in your AWeber account',
        'page_url' => 'https://wpaffiliatemanager.com/signup-affiliates-aweber-list/',
    );
    array_push($addons_data, $addon_21);
    
    $addon_22 = array(
        'name' => 'MemberMouse Integration',
        'thumbnail' => WPAM_URL . '/images/addons/membermouse-integration.png',
        'description' => 'Integrates with MemberMouse plugin so you can reward affiliates for sending members.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-membermouse-plugin-integration/',
    );
    array_push($addons_data, $addon_22);
    
    $addon_23 = array(
        'name' => 'AppThemes Integration',
        'thumbnail' => WPAM_URL . '/images/addons/appthemes-integration.png',
        'description' => 'Integrates with AppThemes apps so you can reward affiliates for sending customers.',
        'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-appthemes-integration/',
    );
    array_push($addons_data, $addon_23);
    
    //Display the list
    $output = '';
    foreach ($addons_data as $addon) {
        $output .= '<div class="wpam_addon_item_canvas">';

        $output .= '<div class="wpam_addon_item_thumb">';
        $img_src = $addon['thumbnail'];
        $output .= '<img src="' . $img_src . '" alt="' . $addon['name'] . '">';
        $output .= '</div>'; //end thumbnail

        $output .='<div class="wpam_addon_item_body">';
        $output .='<div class="wpam_addon_item_name">';
        $output .= '<a href="' . $addon['page_url'] . '" target="_blank">' . $addon['name'] . '</a>';
        $output .='</div>'; //end name

        $output .='<div class="wpam_addon_item_description">';
        $output .= $addon['description'];
        $output .='</div>'; //end description

        $output .='<div class="wpam_addon_item_details_link">';
        $output .='<a href="'.$addon['page_url'].'" class="wpam_addon_view_details" target="_blank">View Details</a>';
        $output .='</div>'; //end detils link      
        $output .='</div>'; //end body

        $output .= '</div>'; //end canvas
    }
    echo $output;
    
    echo '</div>';//end of wrap
}