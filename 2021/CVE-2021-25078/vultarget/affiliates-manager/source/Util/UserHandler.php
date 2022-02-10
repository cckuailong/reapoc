<?php

/**
 * @author Justin Foell
 */
class WPAM_Util_UserHandler {

    public function approveAffiliate($affiliate, $bountyType, $bountyAmount, $update = true) {
        $new_user = false;
        $mailer = new WPAM_Util_EmailHandler();
        $db = new WPAM_Data_DataAccess();

        //Create Affiliate account in WP (1.1.2 if they don't have one)
        //and send them an email telling them they're approved
        $userId = '';
        $userPass = '';
        $userLogin = sanitize_user($affiliate->email);
        $userEmail = sanitize_email($affiliate->email);
        $userEmail = apply_filters('user_registration_email', $userEmail);

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $message = sprintf(__('New affiliate registration for %s: has been approved!', 'affiliates-manager'), $blogname) . "\r\n\r\n";

        if (username_exists($userLogin)) {
            $user = new WP_User(get_user_by('login', $userLogin)->ID);
            if ($user->has_cap(WPAM_PluginConfig::$AffiliateCap)) {
                throw new Exception(__('User already has an account and is already an affiliate', 'affiliates-manager'));
            } else {
                $user->add_cap(WPAM_PluginConfig::$AffiliateCap);
                $message .= __('Log into the site with your existing account and get started.', 'affiliates-manager') . "\r\n";
                $userId = $user->ID;
            }
        } elseif (email_exists($userEmail)) {
            $user = new WP_User(get_user_by('email', $userEmail)->ID);
            if ($db->getAffiliateRepository()->existsBy(array('userId' => $user->ID))) {
                throw new Exception(__('User already has an account and is already an affiliate', 'affiliates-manager'));
            } else {
                $user->add_cap(WPAM_PluginConfig::$AffiliateCap);
                $message .= __('Log into the site with your existing account and get started.', 'affiliates-manager') . "\r\n";
                $userId = $user->ID;
            }
        } else {
            //user not found by email address as username and no account with that email address exists
            //create new user using email address as username
            $userFirstName = sanitize_text_field($affiliate->firstName);
            $userLastName = sanitize_text_field($affiliate->lastName);
            $userPass = wp_generate_password();
            $userId = wp_create_user($userLogin, $userPass, $userEmail);

            if (is_wp_error($userId)){
                throw new Exception($userId->get_error_message());
            }
            $new_user = true;
            $user = new WP_User($userId);
            $user->add_cap(WPAM_PluginConfig::$AffiliateCap);
            $user->set_role( 'affiliate' );
            $userdata = array(
                'ID' => $userId,
                'first_name' => $userFirstName,
                'last_name' => $userLastName
            );
            $update_user_id = wp_update_user($userdata);
        }
        $affiliate->approve();
        $affiliate->userId = $userId;
        $affiliate->bountyType = sanitize_text_field($bountyType);
        $affiliate->bountyAmount = sanitize_text_field($bountyAmount);
        if ($update){
            $db->getAffiliateRepository()->update($affiliate);
        }
        else{
            $db->getAffiliateRepository()->insert($affiliate);
        }
        //notify the affiliate
        if ($new_user) {
            //$mailer->mailNewAffiliate( $userId, $userPass );
            $mailer->mailNewApproveAffiliate($userId, $userPass, $affiliate);
        }
        //Send user email indicating they're approved
        if (!$new_user) {
            $mailer->mailAffiliate($userEmail, sprintf(__('Affiliate Application for %s', 'affiliates-manager'), $blogname), $message);
        }
    }

    public function AutoapproveAffiliate($affiliate, $bountyType = '', $bountyAmount = '') {
        $new_user = false;
        $mailer = new WPAM_Util_EmailHandler();
        $db = new WPAM_Data_DataAccess();

        //Create Affiliate account in WP (1.1.2 if they don't have one)
        //and send them an email telling them they're approved
        $userId = '';
        $userPass = '';
        $userLogin = sanitize_user($affiliate->email);
        $userEmail = sanitize_email($affiliate->email);
        $userEmail = apply_filters('user_registration_email', $userEmail);

        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        $message = sprintf(__('New affiliate registration for %s: has been approved!', 'affiliates-manager'), $blogname) . "\r\n\r\n";

        if (username_exists($userLogin)) {
            $user = new WP_User(get_user_by('login', $userLogin)->ID);
            if ($user->has_cap(WPAM_PluginConfig::$AffiliateCap)) {
                throw new Exception(__('User already has an account and is already an affiliate', 'affiliates-manager'));
            } else {
                $user->add_cap(WPAM_PluginConfig::$AffiliateCap);
                $message .= __('Log into the site with your existing account and get started.', 'affiliates-manager') . "\r\n";
                $userId = $user->ID;
            }
        } elseif (email_exists($userEmail)) {
            $user = new WP_User(get_user_by('email', $userEmail)->ID);
            if ($db->getAffiliateRepository()->existsBy(array('userId' => $user->ID))) {
                throw new Exception(__('User already has an account and is already an affiliate', 'affiliates-manager'));
            } else {
                $user->add_cap(WPAM_PluginConfig::$AffiliateCap);
                $message .= __('Log into the site with your existing account and get started.', 'affiliates-manager') . "\r\n";
                $userId = $user->ID;
            }
        } else {
            //user not found by email address as username and no account with that email address exists
            //create new user using email address as username
            $userFirstName = sanitize_text_field($affiliate->firstName);
            $userLastName = sanitize_text_field($affiliate->lastName);
            $userPass = wp_generate_password();
            $userId = wp_create_user($userLogin, $userPass, $userEmail);

            if (is_wp_error($userId)){
                throw new Exception($userId->get_error_message());
            }
            $new_user = true;
            $user = new WP_User($userId);
            $user->add_cap(WPAM_PluginConfig::$AffiliateCap);
            $user->set_role( 'affiliate' );
            $userdata = array(
                'ID' => $userId,
                'first_name' => $userFirstName,
                'last_name' => $userLastName
            );
            $update_user_id = wp_update_user($userdata);
        }
        $affiliate->activate();
        $affiliate->userId = $userId;
        if (isset($bountyType) && !empty($bountyType)) {
            $affiliate->bountyType = sanitize_text_field($bountyType);
        } else {
            $affiliate->bountyType = get_option(WPAM_PluginConfig::$AffBountyType);
        }
        if (isset($bountyAmount) && !empty($bountyAmount)) {
            $affiliate->bountyAmount = sanitize_text_field($bountyAmount);
        } else {
            $affiliate->bountyAmount = get_option(WPAM_PluginConfig::$AffBountyAmount);
        }
        $id = $db->getAffiliateRepository()->insert($affiliate);
        if ($id == 0) {
            if (WPAM_DEBUG) {
                echo '<pre>', var_export($affiliate, true), '</pre>';
            }
            wp_die(__('Error submitting your details to the database. This is a bug, and your application was not submitted.', 'affiliates-manager'));
        }
        //notify the affiliate
        if ($new_user) {
            //$mailer->mailNewAffiliate( $userId, $userPass );
            $affiliate->affiliateId = $id;
            $mailer->mailNewApproveAffiliate($userId, $userPass, $affiliate);
        }
        if(get_option(WPAM_PluginConfig::$SendAdminRegNotification) == 1){
            //Notify admin that affiliate has registered
            $admin_message = sprintf(__('New affiliate registration on your site %s:', 'affiliates-manager'), $blogname) . "\r\n\r\n";
            $admin_message .= sprintf(__('Name: %s %s', 'affiliates-manager'), $affiliate->firstName, $affiliate->lastName) . "\r\n";
            $admin_message .= sprintf(__('Email: %s', 'affiliates-manager'), $affiliate->email) . "\r\n";
            $admin_message .= sprintf(__('Company: %s', 'affiliates-manager'), $affiliate->companyName) . "\r\n";
            $admin_message .= sprintf(__('Website: %s', 'affiliates-manager'), $affiliate->websiteUrl) . "\r\n\r\n";
            $admin_message .= sprintf(__('View Application: %s', 'affiliates-manager'), admin_url('admin.php?page=wpam-affiliates&viewDetail=' . $id)) . "\r\n";
            $admin_email = get_option(WPAM_PluginConfig::$AdminRegNotificationEmail);
            if(!isset($admin_email) || empty($admin_email)){
                $admin_email = get_option('admin_email');
            }
            $mailer->mailAffiliate($admin_email, __('New Affiliate Registration', 'affiliates-manager'), $admin_message);
        }
        //Send user email indicating they're approved
        if (!$new_user) {
            $mailer->mailAffiliate($userEmail, sprintf(__('Affiliate Application for %s', 'affiliates-manager'), $blogname), $message);
        }
    }
            
    /*** Inserts data into the affiliates table to create an affiliate profile ***/
    function create_wpam_affiliate_record($fields)
    {
        global $wpdb;
        
        //Do some validation to make sure we have some minimum required info
        if(!isset($fields['email']) || empty($fields['email'])){
            WPAM_Logger::log_debug("create_wpam_affiliate_record() - Error, email address is missing. Cannot create affiliate record!", 4);
            return;
        }
        
        if(!isset($fields['userId']) || empty($fields['userId'])){
            WPAM_Logger::log_debug("create_wpam_affiliate_record() - Error, userId value is missing. Cannot create affiliate record!", 4);
            return;
        }        
                
        //Check and set the default status values
        if(!isset($fields['status']) || empty($fields['status'])){
            if(get_option(WPAM_PluginConfig::$AutoAffiliateApproveIsEnabledOption) == 1){
                $fields['status'] = 'active';
            }
            else{
                $fields['status'] = 'applied';
            }
        }
        
        //Check and set default dateCreated value
        if(!isset($fields['dateCreated']) || empty($fields['dateCreated'])){
            $fields['dateCreated'] = current_time('mysql'); //date("Y-m-d H:i:s");
        }
        
        //Check and set default dateCreated value
        if(!isset($fields['uniqueRefKey']) || empty($fields['uniqueRefKey'])){
            $idGenerator = new WPAM_Tracking_UniqueIdGenerator();
            $binConverter = new WPAM_Util_BinConverter();
            $fields['uniqueRefKey'] = $binConverter->binToString($idGenerator->generateId());
        }        
        
        //Check and set default bountyType
        if(!isset($fields['bountyType']) || empty($fields['bountyType'])){
            $fields['bountyType'] = get_option(WPAM_PluginConfig::$AffBountyType);
        }
        
        //Check and set default bountyAmount
        if(!isset($fields['bountyAmount']) || empty($fields['bountyAmount'])){
            $fields['bountyAmount'] = get_option(WPAM_PluginConfig::$AffBountyAmount);
        }
        
        $fields['userData'] = serialize(array()); //assign an empty array so $wpdb->insert does not fail
        $wpdb->insert( WPAM_AFFILIATES_TBL, $fields );
    }
    
}