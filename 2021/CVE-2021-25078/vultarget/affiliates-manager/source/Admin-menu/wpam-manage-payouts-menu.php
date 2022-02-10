<?php

//display manage payouts menu
function wpam_display_manage_payouts_menu()
{
    ?>
    <div class="wrap">
    <h2><?php _e('Manage Payouts', 'affiliates-manager');?></h2>
    <div id="poststuff"><div id="post-body">
    <?php
    if (isset($_POST['wpam_generate_payout_report'])) {
        if(!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wpam_generate_payout_report')){
            wp_die('Error! Nonce Security Check Failed! Go back to the Manage Payouts menu and generate the report again.');
        }
        echo wpam_generate_payout_report();
        echo "<br />";
        echo '<div id="message" class="updated fade"><p>'.__('Payout Report Generated', 'affiliates-manager').'</p></div>';
    }
    if (isset($_POST['wpam_generate_mass_pay_file'])) {
        if(!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wpam_generate_mass_pay_file')){
            wp_die('Error! Nonce Security Check Failed! Go back to the Manage Payouts menu and generate the payout file again.');
        }
        echo wpam_create_mass_pay_file();
        echo "<br />";
    }
    if (isset($_POST['wpam_mark_as_paid'])) {
        if(!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wpam_mark_as_paid')){
            wp_die('Error! Nonce Security Check Failed! Go back to the Manage Payouts menu and mark payments as paid again.');
        }
        echo wpam_mark_payment_as_paid();
    }
    $paypal_payouts_doc = 'https://wpaffiliatemanager.com/paypal-payouts-setup/';
    ?>
    <div class="postbox">
        <h3 class="hndle"><label for="title"><?php _e('Affiliate Mass Payout by Outstanding Amount', 'affiliates-manager');?></label></h3>
    <div class="inside">    
    <form method="post" action="">
        <?php wp_nonce_field('wpam_generate_payout_report'); ?>    
        <strong><?php _e('Step 1:', 'affiliates-manager');?></strong>
        <input type="submit" class="button" name="wpam_generate_payout_report" value="<?php _e('Generate Report', 'affiliates-manager'); ?> &raquo;" />
        <br /><i><?php _e('Hit "Generate Report" to get a list of all the affiliate earnings that need to be paid.', 'affiliates-manager'); ?></i><br />
        <br />
    </form>

    <form method="post" action="">
        <?php wp_nonce_field('wpam_generate_mass_pay_file'); ?>
        <strong><?php _e('Step 2:', 'affiliates-manager');?></strong> <input type="submit" class="button" name="wpam_generate_mass_pay_file" value="<?php _e('Create Payment Report File', 'affiliates-manager'); ?> &raquo;" />
        <br /><i><?php printf(__('Use this to generate a PayPal payout file and a payment report CSV file. The PayPal payout file can be used in paypal to pay all your affiliates in one click. If you have never used PayPal Payouts check <a href="%s" target="_blank">this documentation</a>.', 'affiliates-manager'), $paypal_payouts_doc); ?></i><br />
        <br />
    </form>

    <form method="post" action="" onSubmit="return confirm('<?php _e('Do you really want to mark all the outstanding payments as paid? This action cannot be undone.', 'affiliates-manager');?>');">
        <?php wp_nonce_field('wpam_mark_as_paid'); ?>
        <strong><?php _e('Step 3:', 'affiliates-manager');?></strong> <input type="submit" class="button" name="wpam_mark_as_paid" value="<?php _e('Mark as Paid', 'affiliates-manager'); ?> &raquo;" />
        <br /><i><?php _e('After you have generated the payout report and paid all the affiliates their outstanding balance, use this button to mark all the payments as paid.', 'affiliates-manager'); ?></i><br />
        <br />

    </form>
   </div></div> 
            
   </div></div>         
    </div>
    <?php
}

function wpam_generate_payout_report() {
    $output = '
    <table class="widefat">
    <thead><tr>
    <th scope="col">' . __('Affiliate ID', 'affiliates-manager') . '</th>
    <th scope="col">' . __('Name', 'affiliates-manager') . '</th>   
    <th scope="col">' . __('PayPal Email', 'affiliates-manager') . '</th>
    <th scope="col">' . __('Pending Amount', 'affiliates-manager') . '</th>
    </tr></thead>
    <tbody>';
    $min_payout = get_option(WPAM_PluginConfig::$MinPayoutAmountOption);
    $no_pending_payment = true;
    $counter = 0;
    global $referrers;
    global $payouts;

    global $wpdb;
    $affiliates_table = WPAM_AFFILIATES_TBL;
    $db = new WPAM_Data_DataAccess();
    $aff_db = $db->getAffiliateRepository();

    //Load and process affiliate records using the paging concept (so we are not trying to laod thousands of recoreds at once).  
    $page = 1;
    $query_limit = 500;//Load 500 rows per query    
    $total_rows = wpam_get_total_affiliates_count();
    $total_pages = ceil($total_rows/$query_limit);
    //$resultset = $wpdb->get_results("SELECT * FROM $affiliates_table ORDER BY date", OBJECT);
    if ($total_rows >= 1) {//There are more than 1 affiliates in this site
        while ($page <= $total_pages){
            $query_start = ($page - 1) * $query_limit;//Calculate the query start position for this iteration
            $query = "SELECT * FROM $affiliates_table LIMIT ".$query_start.", ".$query_limit;//Load affiliates in batches
            $resultset = $wpdb->get_results($query, OBJECT);

            foreach ($resultset as $wpam_aff_db) {
                $affiliate = $aff_db->loadAffiliateSummary(array('affiliateId' => $wpam_aff_db->affiliateId));
                $affiliate = $affiliate[0];
                $pending_payment = number_format($affiliate->balance, 2, '.', '');
                if ($pending_payment >= $min_payout) {
                    $affiliates_name = $wpam_aff_db->firstName . " " . $wpam_aff_db->lastName;
                    $paypal_email = isset($wpam_aff_db->paypalEmail) && !empty($wpam_aff_db->paypalEmail) ? $wpam_aff_db->paypalEmail : '';
                    $output .= '<tr>';
                    $output .= '<td>' . $wpam_aff_db->affiliateId . '</td>';
                    $output .= '<td><strong>'.$affiliates_name.'</strong></td>';
                    $output .= '<td><strong>'.$paypal_email.'</strong></td>';
                    $output .= '<td><strong>'.$pending_payment.'</strong></td>';
                    $output .= '</tr>';
                    $no_pending_payment = false;
                    $referrers[$counter] = $wpam_aff_db->affiliateId;
                    $payouts[$counter] = $pending_payment;
                    $counter++;
                }      
            }//End of foreach loop
            $page++;//Increment the page count for the next iteration           
        }//End of while loop
    } else {
        $output .= '<tr> <td colspan="4">' . __('No Affiliates Found in the Database.', 'affiliates-manager') . '</td> </tr>';
    }
    if ($no_pending_payment) {
        $output .= '<tr> <td colspan="4">' . __('No Pending Payment Found.', 'affiliates-manager') . '</td> </tr>';
    }
    $output .= '</tbody></table>';
    
    update_option('wpam_payout_report_generated', true);
    update_option('wp_affiliates_manager_referrers', $referrers);
    update_option('wp_affiliates_manager_payouts', $payouts);
    
    return $output;
}

function wpam_create_mass_pay_file() {
    $referrers = get_option('wp_affiliates_manager_referrers');
    $payouts = get_option('wp_affiliates_manager_payouts');
    $currency_code = get_option(WPAM_PluginConfig::$AffCurrencyCode);
    global $wpdb;
    $affiliates_table = WPAM_AFFILIATES_TBL;
    $output = '';
    if (empty($referrers) || empty($payouts)) {
        $output = '<div id="message" class="updated fade"><p>'. __('There is no pending payment.','affiliates-manager').'</p></div>';
        return $output;
    }
    for ($i = 0; $i < sizeof($referrers); $i++) {
        $row = $wpdb->get_row("select * from $affiliates_table where affiliateId = '$referrers[$i]'", OBJECT);
        if(isset($row->paypalEmail) && !empty($row->paypalEmail)){
            $output .= $row->paypalEmail;
            $output .= ",";
            $output .= $payouts[$i];
            $output .= ",";
            $output .= $currency_code;
            $output .= "\n";
        }
    }
    $paypal_payout_file_path = WPAM_PATH . '/paypal_payout.csv';
    $Handle = fopen($paypal_payout_file_path, 'w') or die(__("can't open file named 'paypal_payout.csv'", 'affiliates-manager'));
    fwrite($Handle, $output);
    fclose($Handle);


    $separator = ", ";
    $csv_output = "";
    $csv_output.= "Commission Amount" . $separator;
    $csv_output.= "Currency" . $separator;
    $csv_output.= "Affiliate ID" . $separator;
    $csv_output.= "First Name" . $separator;
    $csv_output.= "Last Name" . $separator;
    $csv_output.= "Email" . $separator;
    $csv_output.= "Street" . $separator;
    $csv_output.= "City" . $separator;
    $csv_output.= "State" . $separator;
    $csv_output.= "Postal Code" . $separator;
    $csv_output.= "Country" . $separator;
    $csv_output.= "Phone" . $separator;
    $csv_output.= "PayPal Email Address" . $separator;
    $csv_output.= "Payment Method" . $separator;
    $csv_output.= "\n";
    for ($i = 0; $i < sizeof($referrers); $i++) {
        $row = $wpdb->get_row("select * from $affiliates_table where affiliateId = '$referrers[$i]'", OBJECT);

        $csv_output.= wpam_escape_csv_value($payouts[$i]) . $separator;
        $csv_output.= wpam_escape_csv_value($currency_code) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->affiliateId)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->firstName)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->lastName)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->email)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->addressLine1)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->addressCity)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->addressState)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->addressZipCode)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->addressCountry)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->phoneNumber)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->paypalEmail)) . $separator;
        $csv_output.= wpam_escape_csv_value(stripslashes($row->paymentMethod)) . $separator;
        $csv_output.= "\n";
    }

    $aff_payout_report_abs_path = WPAM_PATH . '/affiliate_payout_report.csv';
    $Handle = fopen($aff_payout_report_abs_path, 'w') or die(__("can't open file named 'affiliate_payout_report.csv'", 'affiliates-manager'));
    fwrite($Handle, $csv_output);
    fclose($Handle);

    $output = nl2br($output);
    if (empty($output)) {
        $output .= '<div id="message" class="error"><p>'.__('Note: Please make sure that the PayPal email address field of the affiliates that are about to get paid via PayPal are not empty. PayPal payouts do not work without PayPal email address. You can ignore this warning if you are going to pay your affiliates via other means.', 'affiliates-manager').'</p></div>';
    } else {
        $paypal_payout_file = WPAM_URL.'/paypal_payout.csv';
        $output .= '<div id="message" class="updated fade"><p>'.sprintf(__('PayPal payout file created. Download the <a href="%s">PayPal Payout File</a> (Right click and choose "Save Link As"). You can use this file to make a PayPal mass payment and pay the commissions in one go.', 'affiliates-manager'), $paypal_payout_file).'</p></div>';
    }
    
    $affiliate_payout_report_file = WPAM_URL.'/affiliate_payout_report.csv';
    //Show link for the affiliate payouts report file
    $output .= '<div id="message" class="updated fade"><p>'.sprintf(__('CSV file with outstanding affiliate commission details created. Download the <a href="%s">Affiliate Payout Report File</a> (Right click and choose "Save Link As"). You can use this file to manually send money to your affiliates.', 'affiliates-manager'), $affiliate_payout_report_file).'</p></div>';

    return $output;
}

function wpam_mark_payment_as_paid() {
    $referrers = get_option('wp_affiliates_manager_referrers');
    $payouts = get_option('wp_affiliates_manager_payouts');
    global $wpdb;
    $table = WPAM_TRANSACTIONS_TBL;
    $data = array();
    if (sizeof($referrers) == 0) {
        $output = '<div id="message" class="updated fade"><p>'.__('There is no pending payment to mark', 'affiliates-manager').'</p></div>';
        return $output;
    }
    for ($i = 0; $i < sizeof($referrers); $i++) {
        $amount = $payouts[$i] * -1;
        $data['dateModified'] = date("Y-m-d H:i:s", time());
        $data['dateCreated'] = date("Y-m-d H:i:s", time());
        $data['affiliateId'] = $referrers[$i];
        $data['amount'] = number_format($amount, 2, '.', '');
        $data['type'] = 'payout';
        $data['description'] = __('Payout', 'affiliates-manager');
        //$data['status'] = 'confirmed';
        $wpdb->insert($table, $data);
    }
    
    $output = '<div id="message" class="updated fade"><p>'.__('Marked payments as paid', 'affiliates-manager').'</p></div>';
    return $output;
}
