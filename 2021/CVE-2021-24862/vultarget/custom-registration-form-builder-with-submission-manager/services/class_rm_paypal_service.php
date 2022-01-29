<?php

class RM_Paypal_Service implements RM_Gateway_Service
{
    public $paypal;
    public $options;
    public $paypal_email;
    public $currency;
    public $paypal_page_style;

    function __construct() {
        $this->options= new RM_Options();

        $sandbox =  $this->options->get_value_of('paypal_test_mode');
        $this->paypal_email = $this->options->get_value_of('paypal_email');
        $this->currency = $this->options->get_value_of('currency');
        $this->paypal_page_style = $this->options->get_value_of('paypal_page_style');

        $this->paypal = new rm_paypal_class();

        if ($sandbox == 'yes')
            $this->paypal->toggle_sandbox(true);
        else
            $this->paypal->toggle_sandbox(false);

        $this->paypal->admin_mail = get_option('admin_email');
    }

    function getPaypal() {
        return $this->paypal;
    }

    function getOptions() {
        return $this->options;
    }

    function setPaypal($paypal) {
        $this->paypal = $paypal;
    }

    function setOptions($options) {
        $this->options = $options;
    }

    public function callback($payment_status,$rm_pproc_id, $sec_hash)
    {
        switch ($payment_status)
            {
                case 'success':
                    if ($rm_pproc_id)
                    {
                        $log_id = $rm_pproc_id;
                        $log = RM_DBManager::get_row('PAYPAL_LOGS', $log_id);
                        if ($log)
                        {
                            $exdata = maybe_unserialize($log->ex_data);
                            if(isset($exdata['sec_hash']))
                            {
                                if($sec_hash != $exdata['sec_hash'])
                                    return 'invalid_hash';
                            }
                            else
                            {
                                return 'invalid_hash';
                            }
                            
                            if ($log->log)
                            {
                                $paypal_log = maybe_unserialize($log->log);
                                $payment_status = $paypal_log['payment_status'];
                                $cstm = $paypal_log["custom"];
                                $abcd = explode("|", $cstm);
                                $user_id = (int) ($abcd[1]);
                                $form_id = $log->form_id;

                                if ($payment_status == 'Completed')
                                {
                                    $ffact = defined('REGMAGIC_ADDON') ? new RM_Form_Factory_Addon() : new RM_Form_Factory();
                                    $fef = $ffact->create_form($form_id);
                                    $fopt = $fef->get_form_options();
                                    if($fopt->auto_login)
                                         $_SESSION['RM_SLI_UID'] = $user_id;
                                    
                                    echo '<div id="rmform">';
                                    echo "<div class='rminfotextfront'>" . RM_UI_Strings::get("MSG_PAYMENT_SUCCESS") . "</br>";
                                    echo '</div></div>';
                                    return 'success';
                                } else if ($payment_status == 'Denied' || $payment_status == 'Failed' || $payment_status == 'Refunded' || $payment_status == 'Reversed' || $payment_status == 'Voided')
                                {
                                    echo '<div id="rmform">';
                                    echo "<div class='rminfotextfront'>" . RM_UI_Strings::get("MSG_PAYMENT_FAILED") . "</br>";
                                    echo '</div></div>';
                                    return 'failed';
                                } else if ($payment_status == 'In-Progress' || $payment_status == 'Pending' || $payment_status == 'Processed')
                                {
                                    echo '<div id="rmform">';
                                    echo "<div class='rminfotextfront'>" . RM_UI_Strings::get("MSG_PAYMENT_PENDING") . "</br>";
                                    echo '</div></div>';
                                    return 'pending';
                                } else if ($payment_status == 'Canceled_Reversal')
                                {
                                    return 'canceled_reversal';
                                }
                            }
                        }
                    }
                    return false;

                case 'cancel':
                    echo '<div id="rmform">';
                    echo "<div class='rminfotextfront'>" . RM_UI_Strings::get("MSG_PAYMENT_CANCEL") . "</br>";
                    echo '</div></div>';
                    return;

                case 'ipn':
                    $trasaction_id = $_POST["txn_id"];
                    $payment_status = $_POST["payment_status"];
                    $cstm = $_POST["custom"];
                    $abcd = explode("|", $cstm);
                    $user_id = (int) ($abcd[1]);
                    $acbd = explode("|", $cstm);
                    $log_entry_id = (int) ($acbd[0]); //$_POST["custom"];
                    $log_array = maybe_serialize($_POST);

                    $curr_date = RM_Utilities::get_current_time(); // date_i18n(get_option('date_format'));

                    RM_DBManager::update_row('PAYPAL_LOGS', $log_entry_id, array(
                        'status' => $payment_status,
                        'txn_id' => $trasaction_id,
                        'posted_date' => $curr_date,
                        'log' => $log_array), array('%s', '%s', '%s', '%s'));
                
                    if(defined('REGMAGIC_ADDON')) {
                        //$check_setting = apply_filters('rm_addon_paypal_callback',$trasaction_id);
                        $addon_service = new RM_Paypal_Service_Addon;
                        $check_setting = $addon_service->check_approval_settings($trasaction_id);
                    } else {
                        //$check_setting = $gopt->get_value_of('user_auto_approval');
                        $check_setting = "yes";
                    }

                    if ($this->paypal->validate_ipn())
                    {
                        //IPN is valid, check payment status and process logic
                        if ($payment_status == 'Completed')
                        {
                            if ($user_id)
                            {
                                $gopt = new RM_Options;
                                if ($check_setting == "yes")
                                {
                                    $user_service = new RM_User_Services();
                                    $user_service->activate_user_by_id($user_id);
                                }
                            }
                            return 'success';
                        }
                        else if ($payment_status == 'Denied' || $payment_status == 'Failed' || $payment_status == 'Refunded' || $payment_status == 'Reversed' || $payment_status == 'Voided')
                        {
                            return 'failed';
                        } else if ($payment_status == 'In-Progress' || $payment_status == 'Pending' || $payment_status == 'Processed')
                        {
                            return 'pending';
                        } else if ($payment_status == 'Canceled_Reversal')
                        {
                            return 'canceled_reversal';
                        }

                        return 'unknown';
                    }

                    return 'invalid_ipn';
            }
    }

    public function cancel() {

    }

    public function charge($data,$pricing_details) {
        $form_id= $data->form_id;
        $this_script = get_permalink();
        global $rm_form_diary;
        $form_no = $rm_form_diary[$form_id];
        $sec_hash = wp_generate_password(12, false);        
        $ex_data = array(); //Store additional data to pick up payment at a later point.
        $ex_data['user_id'] = isset($data->user_id) ? $data->user_id : null;
        $ex_data['sec_hash'] = $sec_hash;
        if(false == $this_script){
            $this_script = admin_url('admin-ajax.php?action=registrationmagic_embedform&form_id='.$data->form_id);
        }
        $sign = strpos($this_script, '?') ? '&' : '?';

        $i = 1;
        foreach ($pricing_details->billing as $item)
        {
            $this->paypal->add_field('item_name_' . $i, $item->label);
            $i++;
        }

        $total_amount = $pricing_details->total_price;       
                
        $i = 1;
        foreach ($pricing_details->billing as $item)
        {
            $this->paypal->add_field('amount_' . $i, $item->price);
            $i++;
        }
                
        $i = 1;
        foreach ($pricing_details->billing as $item)
        {
            $qty = isset($item->qty) ? $item->qty : 1;
            $this->paypal->add_field('quantity_' . $i, $qty);
            $i++;
        }

        $invoice = (string) date("His") . rand(1234, 9632);

        $this->paypal->add_field('business', $this->paypal_email); // Call the facilitator eaccount
        $this->paypal->add_field('cmd', '_cart'); // cmd should be _cart for cart checkout
        $this->paypal->add_field('upload', '1');
        $this->paypal->add_field('return', $this_script . $sign . 'rm_pproc=success&rm_pproc_id=0'.'&rm_fid='.$form_id.'&rm_fno='.$form_no.'&sh='.$sec_hash); // return URL after the transaction got over
        $this->paypal->add_field('cancel_return', $this_script . $sign . 'rm_pproc=cancel&rm_pproc_id=0'.'&rm_fid='.$form_id.'&rm_fno='.$form_no.'&sh='.$sec_hash); // cancel URL if the trasaction was cancelled during half of the transaction
        $notify_url = esc_url(add_query_arg(array('action'=>'rm_paypal_ipn','rm_fid'=>$form_id,'rm_fno'=>$form_no),admin_url('admin-ajax.php')));
        //$this->paypal->add_field('notify_url', $this_script . $sign . 'rm_pproc=ipn&rm_pproc_id=0'.'&rm_fid='.$form_id.'&rm_fno='.$form_no.'&sh='.$sec_hash); // Notify URL which received IPN (Instant Payment Notification)
        $this->paypal->add_field('notify_url', $notify_url); 
        $this->paypal->add_field('currency_code', $this->currency);
        $this->paypal->add_field('invoice', $invoice);

        $this->paypal->add_field('page_style', $this->paypal_page_style);

        //Insert into PayPal log table

        $curr_date = RM_Utilities::get_current_time(); //date_i18n(get_option('date_format'));

        if ($total_amount <= 0.0)
        {
            $log_entry_id = RM_DBManager::insert_row('PAYPAL_LOGS', array('submission_id' => $data->submission_id,
                        'form_id' => $form_id,
                        'invoice' => $invoice,
                        'status' => 'Completed',
                        'total_amount' => $total_amount,
                        'currency' => $this->currency,
                        'posted_date' => $curr_date,
                        'pay_proc' => 'paypal',
                        'bill' => maybe_serialize($pricing_details),
                        'ex_data' => maybe_serialize($ex_data)), array('%d', '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s'));

            return true;
        } else {
            $log_entry_id = RM_DBManager::insert_row('PAYPAL_LOGS', array('submission_id' => $data->submission_id,
                        'form_id' => $form_id,
                        'invoice' => $invoice,
                        'status' => 'Pending',
                        'total_amount' => $total_amount,
                        'currency' => $this->currency,
                        'posted_date' => $curr_date,
                        'pay_proc' => 'paypal',
                        'bill' => maybe_serialize($pricing_details),
                        'ex_data' => maybe_serialize($ex_data)), array('%d', '%d', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s'));
        }
        
        if(isset($data->user_id))
            $cstm_data = $log_entry_id."|".$data->user_id;
        else
            $cstm_data = $log_entry_id."|0";
        
        $this->paypal->add_field('custom', $cstm_data);
        $this->paypal->add_field('bn', 'CMSHelp_SP');
        $this->paypal->add_field('return', $this_script . $sign . 'rm_pproc=success&rm_pproc_id='.$log_entry_id.'&rm_fid='.$form_id.'&rm_fno='.$form_no.'&sh='.$sec_hash); // return URL after the transaction got over
        $this->paypal->add_field('cancel_return', $this_script . $sign . 'rm_pproc=cancel&rm_pproc_id='.$log_entry_id.'&rm_fid='.$form_id.'&rm_fno='.$form_no.'&sh='.$sec_hash); // cancel URL if the trasaction was cancelled during half of the transaction
        //$this->paypal->add_field('notify_url', $this_script . $sign . 'rm_pproc=ipn&rm_pproc_id='.$log_entry_id.'&rm_fid='.$form_id.'&rm_fno='.$form_no.'&sh='.$sec_hash); // Notify URL which received IPN (Instant Payment Notification)
        $this->paypal->add_field('notify_url', $notify_url);                 
          $data=array();
       
         // POST it to paypal
        $data['html']= $this->paypal->submit_paypal_post();
        $data['status']='do_not_redirect';
        ob_end_clean();
        return $data; //We do not want form redirect to work in case paypal processing is going on.
    }

    public function refund() {
        
    }

    public function subscribe() {
        
    }

}

