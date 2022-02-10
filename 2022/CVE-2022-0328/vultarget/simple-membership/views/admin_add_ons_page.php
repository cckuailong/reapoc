<?php

//Check current_user_can() or die.
SwpmMiscUtils::check_user_permission_and_is_admin('Main Addons Listing Menu');

$output = '';
echo '<link type="text/css" rel="stylesheet" href="' . SIMPLE_WP_MEMBERSHIP_URL . '/css/swpm.addons.listing.css" />' . "\n";
?>

<div class="wrap">
    <h1><?php echo SwpmUtils::_('Simple WP Membership::Add-ons') ?></h1>

    <div id="poststuff"><div id="post-body">

            <?php
            $addons_data = array();
            $addon_1 = array(
                'name' => 'After Login Redirection',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-login-redirection.png',
                'description' => 'Allows you to configure after login redirection to a specific page based on the member\'s level',
                'page_url' => 'https://simple-membership-plugin.com/configure-login-redirection-members/',
            );
            array_push($addons_data, $addon_1);

            $addon_2 = array(
                'name' => 'MailChimp Integration',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/mailchimp-integration.png',
                'description' => 'Allows you to signup the member to your MailChimp list after registration',
                'page_url' => 'https://simple-membership-plugin.com/signup-members-mailchimp-list/',
            );
            array_push($addons_data, $addon_2);

            $addon_3 = array(
                'name' => 'Form Shortcode',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/form-shortcode-generator.png',
                'description' => 'Simple Membership Addon to generate form shortcode for specific membership level.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-registration-form-shortcode-generator/',
            );
            array_push($addons_data, $addon_3);

            $addon_4 = array(
                'name' => 'Member Directory Listing',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-member-directory-listing-addon.png',
                'description' => 'Allows you to create a list of all the users on your site, with pagination and search option.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-member-directory-listing-addon/',
            );
            array_push($addons_data, $addon_4);

            $addon_5 = array(
                'name' => 'Form Builder',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-form-builder.png',
                'description' => 'Allows you to fully customize the fields that appear on the registration and edit profile forms of your membership site',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-form-builder-addon/',
            );
            array_push($addons_data, $addon_5);

            $addon_6 = array(
                'name' => 'Custom Messages',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-custom-messages.png',
                'description' => 'Custom Messages addon allows you to customize the content protection message that gets output from the membership plugin',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-custom-messages-addon/',
            );
            array_push($addons_data, $addon_6);

            $addon_7 = array(
                'name' => 'WooCommerce Payments',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-woocommerce-addon.png',
                'description' => 'This addon can be used to accept membership payment via the WooCommerce plugin',
                'page_url' => 'https://simple-membership-plugin.com/woocommerce-simple-membership-plugin-integration/',
            );
            array_push($addons_data, $addon_7);

            $addon_8 = array(
                'name' => 'WP Express Checkout',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/wp-express-checkout-integration.png',
                'description' => 'Allows you to integrate with the Express Checkout plugin to accept membership payments.',
                'page_url' => 'https://simple-membership-plugin.com/wp-express-checkout-plugin-integration-for-membership-payment/',
            );
            array_push($addons_data, $addon_8);

            $addon_9 = array(
                'name' => 'Affiliates Manager',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/affiliates-manager-integration.png',
                'description' => 'Allows you to integrate with the Affiliates Manager plugin so you can reward affiliates for sending paid members your way.',
                'page_url' => 'https://wpaffiliatemanager.com/affiliates-manager-and-simple-membership-integration/',
            );
            array_push($addons_data, $addon_9);

            $addon_10 = array(
                'name' => 'Affiliate Platform',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/affiliate-platform-integration.png',
                'description' => 'Allows you to integrate with the Affiliate Platform plugin so you can reward affiliates for sending paid members your way.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-and-wp-affiliate-platform-integration/',
            );
            array_push($addons_data, $addon_10);

            $addon_11 = array(
                'name' => 'bbPress Integration',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-bbpress-integration.png',
                'description' => 'Adds bbPress forum integration with the simple membership plugin to offer members only forum functionality.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-bbpress-forum-integration-addon/',
            );
            array_push($addons_data, $addon_11);

            $addon_12 = array(
                'name' => 'Google reCAPTCHA',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/google-recaptcha-addon.png',
                'description' => 'Allows you to add Google reCAPTCHA to your membership registration form/page.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-and-google-recaptcha-integration/',
            );
            array_push($addons_data, $addon_12);

            $addon_13 = array(
                'name' => 'Full Page Protection',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/full-page-protection-addon.png',
                'description' => 'Allows you to protect the full post or page (header to footer).',
                'page_url' => 'https://simple-membership-plugin.com/full-page-protection-addon-simple-membership/',
            );
            array_push($addons_data, $addon_13);

            $addon_14 = array(
                'name' => 'Protect Older Posts',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-older-posts-protection.png',
                'description' => 'The protect older posts addon allows you to control protection of posts that were published before a member\'s access start date.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-protect-older-posts-addon/',
            );
            array_push($addons_data, $addon_14);

            $addon_15 = array(
                'name' => 'Custom Post Protection',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/custom-post-type-protection-enhanced.png',
                'description' => 'Offers a better solution for protecting custom post type posts.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-addon-better-custom-post-type-protection/',
            );
            array_push($addons_data, $addon_15);

            $addon_16 = array(
                'name' => 'Partial Protection',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-partial-protection-addon.png',
                'description' => 'Allows you to apply partial or section protection to posts and pages.',
                'page_url' => 'https://simple-membership-plugin.com/apply-partial-section-protection/',
            );
            array_push($addons_data, $addon_16);

            $addon_17 = array(
                'name' => 'Member Data Exporter',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-data-exporter-addon.png',
                'description' => 'Allows you to export all the members profile data and payments data to a CSV file.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-member-data-exporter-addon/',
            );
            array_push($addons_data, $addon_17);

            $addon_18 = array(
                'name' => 'Bulk Member Importer',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-bulk-member-importer-from-csv-addon.png',
                'description' => 'Allows you to bulk import all your members info from a CSV file.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-bulk-import-member-data-csv-file/',
            );
            array_push($addons_data, $addon_18);

            $addon_19 = array(
                'name' => 'Display Member Payments',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-member-payments-addon.png',
                'description' => 'This addon allows you to display the member payments on a page using a shortcode.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-member-payments-listing-addon/',
            );
            array_push($addons_data, $addon_19);

            $addon_20 = array(
                'name' => 'AWeber Integration',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-aweber-integration-addon.png',
                'description' => 'You can automatically signup your members to a specific list in your AWeber account when they register.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-aweber-integration-addon/',
            );
            array_push($addons_data, $addon_20);

            $addon_21 = array(
                'name' => 'WP User Import',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/wp-user-import.png',
                'description' => 'Addon for importing existing Wordpress users to Simple Membership plugin',
                'page_url' => 'https://simple-membership-plugin.com/import-existing-wordpress-users-simple-membership-plugin/',
            );
            array_push($addons_data, $addon_21);

            $addon_22 = array(
                'name' => 'ConvertKit Integration',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-convertkit-integration-addon.png',
                'description' => 'Allows you to automatically signup your members to a sequence in your ConvertKit account',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-convertkit-integration-addon/',
            );
            array_push($addons_data, $addon_22);

            $addon_23 = array(
                'name' => 'Google First Click Free',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/google-first-click-free-addon.png',
                'description' => 'Allows you to integrate with the Google First Click Free feature.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-google-first-click-free-integration-addon',
            );
            array_push($addons_data, $addon_23);

            $addon_24 = array(
                'name' => 'Miscellaneous Shortcodes',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/swpm-misc-shortcodes-addon.png',
                'description' => 'This addon has a collection of miscellaneous shortcodes',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-miscellaneous-shortcodes-addon/',
            );
            array_push($addons_data, $addon_24);

            $addon_25 = array(
                'name' => 'Show Member Info',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/show-member-info.png',
                'description' => 'Allows you to show various member info using shortcodes.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-addon-show-member-info/',
            );
            array_push($addons_data, $addon_25);

            $addon_26 = array(
                'name' => 'iDevAffiliate',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/idevaffiliate-integration.png',
                'description' => 'Allows you to integrate with iDevAffiliates so you can reward affiliates for sending paid members your way.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-and-idevaffiliate-integration/',
            );
            array_push($addons_data, $addon_26);

            $addon_27 = array(
                'name' => 'Expiry Email Notification',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/email-notification-and-broadcast-addon.png',
                'description' => 'Allows you to configure and send various expiry email notifications for members.',
                'page_url' => 'https://simple-membership-plugin.com/simple-membership-email-notification-broadcast-addon/',
            );
            array_push($addons_data, $addon_27);

            $addon_28 = array(
                'name' => '2 Factor Authentication',
                'thumbnail' => SIMPLE_WP_MEMBERSHIP_URL . '/images/addons/2fa-addon-icon.png',
                'description' => 'This addon adds the 2 factor authentication for member login to increase login security.',
                'page_url' => 'https://simple-membership-plugin.com/swpm-two-factor-authentication-addon/',
            );
            array_push($addons_data, $addon_28);

            /*** Show the addons list ***/
            foreach ($addons_data as $addon) {
                $output .= '<div class="swpm_addon_item_canvas">';

                $output .= '<div class="swpm_addon_item_thumb">';
                $img_src = $addon['thumbnail'];
                $output .= '<img src="' . $img_src . '" alt="' . $addon['name'] . '">';
                $output .= '</div>'; //end thumbnail

                $output .='<div class="swpm_addon_item_body">';
                $output .='<div class="swpm_addon_item_name">';
                $output .= '<a href="' . $addon['page_url'] . '" target="_blank">' . $addon['name'] . '</a>';
                $output .='</div>'; //end name

                $output .='<div class="swpm_addon_item_description">';
                $output .= $addon['description'];
                $output .='</div>'; //end description

                $output .='<div class="swpm_addon_item_details_link">';
                $output .='<a href="' . $addon['page_url'] . '" class="swpm_addon_view_details" target="_blank">View Details</a>';
                $output .='</div>'; //end detils link
                $output .='</div>'; //end body

                $output .= '</div>'; //end canvas
            }

            echo $output;
            ?>

        </div></div><!-- end of poststuff and post-body -->
</div><!-- end of .wrap -->