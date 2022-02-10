<div class="aff-wrap">
    <?php
    include WPAM_BASE_DIRECTORY . "/html/affiliate_cp_nav.php";
    /** better method but currently does not work
    global $wpdb;
    $currentUser = wp_get_current_user();
    $user_id = $currentUser->ID;
    $table_name = WPAM_AFFILIATES_TBL;
    $affiliate = $wpdb->get_row("SELECT * FROM $table_name WHERE userId = '$user_id' AND status = 'active'");
    $record_found = true;
    if(!$affiliate){
        $record_found = false;
    }
    $table_name = WPAM_CREATIVES_TBL;
    $default_creative_id = get_option(WPAM_PluginConfig::$DefaultCreativeId);
    $creative = '';
    if(empty($default_creative_id)){
        $record_found = false;
    }
    else{
        $creative = $wpdb->get_row("SELECT * FROM $table_name WHERE creativeId = '$default_creative_id'");
        if(!$creative){
            $record_found = false;
        }
    }
    $alink = '';
    $alink_id = '';
    $alink_email = '';
    if($record_found){
        $aid = $affiliate->affiliateId;
        $alink_id = add_query_arg( array( WPAM_PluginConfig::$RefKey => $aid ), home_url('/') );
        $aemail = $affiliate->email;
        $alink_email = add_query_arg( array( WPAM_PluginConfig::$RefKey => $aemail ), home_url('/') );
        $trackingKey = new WPAM_Tracking_TrackingKey();
        $trackingKey->setAffiliateRefKey($affiliate->uniqueRefKey);
        $trackingKey->setCreativeId($creative->creativeId);
        $alink = add_query_arg( array( WPAM_PluginConfig::$RefKey => $trackingKey->pack() ), home_url('/') );
    }
    ****/
    $db = new WPAM_Data_DataAccess();
    $currentUser = wp_get_current_user();
    $alink_id = '';
    $aff_id = '';
    $affiliateRepos = $db->getAffiliateRepository();
    $affiliate = $affiliateRepos->loadBy(array('userId' => $currentUser->ID, 'status' => 'active'));
    if ( $affiliate === NULL ) {  //affiliate with this WP User ID does not exist
        return;
    }

    $default_url = home_url('/');
    $aff_landing_page = get_option(WPAM_PluginConfig::$AffLandingPageURL);
    if(isset($aff_landing_page) && !empty($aff_landing_page)){
        $default_url = $aff_landing_page;
    }
    $aff_id = $affiliate->affiliateId;
    $alink_id = add_query_arg( array( WPAM_PluginConfig::$wpam_id => $aff_id ), $default_url );
    if(isset($_REQUEST['wpam_link_generation_url'])) {
        $default_url = strip_tags($_REQUEST['wpam_link_generation_url']);
    }
    ?>

    <div class="wrap">
        <?php
        if(!empty($alink_id)){
        ?>
        <h3><?php _e('Your Affiliate Link Using Affiliate ID', 'affiliates-manager') ?></h3>
        <textarea class="wpam-creative-code" rows="1"><?php echo $alink_id; ?></textarea>
        <?php
        }       
        $output = '<h3>'.__('Referral URL Generator', 'affiliates-manager').'</h3>';
        $output .= '<form id="wpam_link_generation_form" action="" method="post">';
        $output .= wp_nonce_field('wpam_generate_referral_link', '_wpnonce', true, false);
        $output .= '<div class="wpam_link_gen_page_url_label">'.__('Enter any URL from this site in the form below to generate a referral link', 'affiliates-manager').'</div>';
        $output .= '<div class="wpam_link_generation_input"><input type="text" name="wpam_link_generation_url" value="'.$default_url.'" size="60" /></div>';    
        if (isset($_REQUEST['wpam_generate_referral_link']) && is_numeric($aff_id)) {
            if(!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wpam_generate_referral_link')){
                wp_die('Error! Nonce Security Check Failed! Please enter a URL to generate a referral link again.');
            }
            $referral_url = add_query_arg( array( WPAM_PluginConfig::$wpam_id => $aff_id ), $default_url );
            $output .= '<br />';
            $output .= '<div class="wpam_referral_url_label">'.__('Below is your referral URL (You can copy it and share anywhere)', 'affiliates-manager').'</div>';
            $output .= '<div class="wpam_referral_url_input"><input type="text" name="wpam_referral_url_input" value="'.$referral_url.'" size="60" /></div>';
        } 
        $output .= '<br />';
        $output .= '<div class="wpam_link_generation_submit"><input type="submit" class="button" name="wpam_generate_referral_link" value="'.__('Generate Referral URL', 'affiliates-manager').'" /></div>';
        $output .= '</form>';
        echo $output;
        ?>
        <h3><?php _e('The following creatives are available for publication.', 'affiliates-manager') ?></h3>

        <table class="pure-table">
            <thead>
                <tr>
                    <th><?php _e('Type', 'affiliates-manager') ?></th>
                    <th><?php _e('Name', 'affiliates-manager') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->viewData['creatives'] as $creative) { ?>
                    <tr>
                        <td class="wpam-creative-type"><?php echo $creative->type ?></td>
                        <td class="wpam-creative-name"><a href="?page_id=<?php echo the_ID() ?>&sub=creatives&action=detail&creativeId=<?php echo $creative->creativeId ?>"><?php echo $creative->name ?></a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>