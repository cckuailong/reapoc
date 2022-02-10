<?php

class WPAM_Click_Tracking {

    public static function record_click() {
        if (isset($_REQUEST[WPAM_PluginConfig::$RefKey]) && !empty($_REQUEST[WPAM_PluginConfig::$RefKey])) { //this is the old tracking code (deprecated). This will be removed once the new tracking system is functional.
            global $wpdb;
            $strRefKey = trim(strip_tags($_REQUEST[WPAM_PluginConfig::$RefKey]));
            $aff_id = "";
            if (is_numeric($strRefKey)) {  //wpam_refkey contains affiliate ID. If a record is found save it
                $query = "SELECT * FROM ".WPAM_AFFILIATES_TBL." WHERE affiliateId = %d";        
                $affiliate = $wpdb->get_row($wpdb->prepare($query, $strRefKey));
                if($affiliate != null && $affiliate->status == "active") {
                    $aff_id = $strRefKey;
                }
            } 
            else if (is_email($strRefKey)) {  //wpam_refkey contains email. Find the ID associated with that email and save it
                $query = "SELECT * FROM ".WPAM_AFFILIATES_TBL." WHERE email = %s";        
                $affiliate = $wpdb->get_row($wpdb->prepare($query, $strRefKey));
                if($affiliate != null && $affiliate->status == "active") {
                    $aff_id = $affiliate->affiliateId;
                }
            }
            else{   //TODO start - wpam_refkey contains long tracking key. Find affiliate ID from it and save it. This block of code will just be here for backwards compatibilty
                $refKey = new WPAM_Tracking_TrackingKey();
                $refKey->unpack($strRefKey);
                $db = new WPAM_Data_DataAccess();
                $affiliateRepos = $db->getAffiliateRepository();
                $affiliateId = $affiliateRepos->getAffiliateIdFromRefKey($refKey->getAffiliateRefKey());
                if ($affiliateId === NULL) {
                    
                }
                else{
                    $aff_id = $affiliateId;
                }
            }
            //TODO end
            if(!empty($aff_id)){
                $cookie_life_time = wpam_get_cookie_life_time();
                setcookie('wpam_id', $aff_id, $cookie_life_time, "/", COOKIE_DOMAIN);
            }
        }
        //this will be the new affiliate link. A click will be tracked when wpam_id is present in the URL
        if (isset($_REQUEST[WPAM_PluginConfig::$wpam_id]) && !empty($_REQUEST[WPAM_PluginConfig::$wpam_id])) {
            global $wpdb;
            $aff_id = trim(strip_tags($_REQUEST[WPAM_PluginConfig::$wpam_id]));
            $cookie_life_time = wpam_get_cookie_life_time();
            setcookie('wpam_id', $aff_id, $cookie_life_time, "/", COOKIE_DOMAIN);
            //do not record multiple clicks within 5 seconds
            $current_datetime = date("Y-m-d H:i:s", time());
            $cd_datetime = date("Y-m-d H:i:s", strtotime($current_datetime) - 5);
            $user_ip = WPAM_Click_Tracking::get_user_ip();
            $table = WPAM_TRACKING_TOKENS_TBL;
            $result = $wpdb->get_var( $wpdb->prepare( 
            "
                    SELECT *
                    FROM $table 
                    WHERE dateCreated
                    BETWEEN %s
                    AND %s
                    AND ipAddress = %s

            ",
            $cd_datetime,        
            $current_datetime,
            $user_ip        
            ) );
            if($result != null){
                return;
            }
            $args = array();
            $args['dateCreated'] = $current_datetime;
            $args['sourceAffiliateId'] = $aff_id;
            $args['trackingKey'] = uniqid(); //save a unique ID to avoid error
            $args['sourceCreativeId'] = '';  // remove this column from the click tracking menu in the settings
            $args['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            $args['affiliateSubCode'] = '';
            $args['ipAddress'] = $user_ip;
            /*
            WPAM_Logger::log_debug('inserting click');
            WPAM_Logger::log_debug(print_r($args, true));
            */
            WPAM_Click_Tracking::insert_click_data($args);
        }
        
    }
    
    public static function insert_click_data($args){
        global $wpdb;
        $table = WPAM_TRACKING_TOKENS_TBL;
        $wpdb->insert( $table, $args);
    }
    
    /*
     * Gets total number of clicks for a given affiliate.
     * $args array requires at least 3 elements 
     * aff_id - affiliate ID
     * start_date - start date 
     * end_date - end date
     */
    public static function get_total_clicks($args){  //$args() at least requires 3 elements: affiliate ID, start date and end date
        global $wpdb;
        $table = WPAM_TRACKING_TOKENS_TBL;
        $total_clicks = 0;
        $result = $wpdb->get_var( $wpdb->prepare( 
	"
		SELECT COUNT(*) 
		FROM $table 
		WHERE sourceAffiliateId = %d
                AND dateCreated >= %s
                AND dateCreated < %s
                
	",
        $args['aff_id'],        
	$args['start_date'],
        $args['end_date']        
        ) );
        if($result != null){
            $total_clicks = $result;
        }
        return $total_clicks;
    }
    /*
     * Gets all time total number of clicks for a given affiliate.
     * $args array requires only 1 element
     * aff_id - affiliate ID
     */
    public static function get_all_time_total_clicks($args){  //$args() at least requires affiliate ID
        global $wpdb;
        $table = WPAM_TRACKING_TOKENS_TBL;
        $total_clicks = 0;
        $result = $wpdb->get_var( $wpdb->prepare( 
	"
		SELECT COUNT(*) 
		FROM $table 
		WHERE sourceAffiliateId = %d              
	",
        $args['aff_id']             
        ) );
        if($result != null){
            $total_clicks = $result;
        }
        return $total_clicks;
    }
    /*
     * delete clicks data for a given period / delete all clicks data.
     */
    public static function delete_clicks_data_by_date($args = array()){
        global $wpdb;
        $table = WPAM_TRACKING_TOKENS_TBL;
        $start_date = (isset($args['start_date']) && !empty($args['start_date'])) ? $args['start_date'] : '';
        $end_date = (isset($args['end_date']) && !empty($args['end_date'])) ? $args['end_date'] : '';
        $deletedb = '';
        if(!empty($start_date) && !empty($end_date)){
            $deletedb = "DELETE FROM $table WHERE dateCreated BETWEEN '$start_date' AND '$end_date'";
        }
        else if(!empty($start_date) && empty($end_date)){
            $deletedb = "DELETE FROM $table WHERE dateCreated >= '$start_date'";
        }
        else if(empty($start_date) && !empty($end_date)){
            $deletedb = "DELETE FROM $table WHERE dateCreated <= '$end_date'";
        }
        else{
            $deletedb = "DELETE FROM $table";
        }
        $result = $wpdb->query($deletedb);
    }
    /*
     * Get the IP Address of the user.
     */
    public static function get_user_ip() {
        $user_ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $user_ip = $_SERVER['REMOTE_ADDR'];
        }

        if (strstr($user_ip, ',')) {
            $ip_values = explode(',', $user_ip);
            $user_ip = $ip_values['0'];
        }

        return $user_ip;
    }
    
    public static function get_referrer_id_from_ip_address($ip_address) {
        if(!isset($ip_address) || empty($ip_address)){
            return "";
        }
        global $wpdb;
        $table = WPAM_TRACKING_TOKENS_TBL;
        $aff_id = '';
        $result = $wpdb->get_row( $wpdb->prepare( 
	"
		SELECT * FROM $table 
		WHERE ipAddress = %s              
	",
        $ip_address       
        ) );
        if(null !== $result){
            $aff_id = $result->sourceAffiliateId;
        }
        return $aff_id;
    }

}
