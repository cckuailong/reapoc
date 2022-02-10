<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
Class LFB_EmailSettingForm {
    function __construct($this_form_id) {
        global $wpdb;
        $th_save_db = new LFB_SAVE_DB($wpdb);
        $table_name = LFB_FORM_FIELD_TBL;
        $prepare_9 =  $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d LIMIT 1",$this_form_id );
        $posts = $th_save_db->lfb_get_form_content($prepare_9);
        if ($posts){
            $form_title = $posts[0]->form_title;
            $form_status = $posts[0]->form_status;
            $captcha_status = $posts[0]->captcha_status;
            $storeType = $posts[0]->storeType;
            $storedate = $posts[0]->date;
            $mail_setting = maybe_unserialize($posts[0]->mail_setting);
            $usermail_setting = maybe_unserialize($posts[0]->usermail_setting);
            $form_data = maybe_unserialize($posts[0]->form_data);
        }
    }

    function lfb_email_setting_form($this_form_id, $mail_setting_result,$usermail_setting) {

            $mail_setting_to = get_option('admin_email');
            $mail_setting_from = get_option('admin_email');
            $mail_setting_subject = "Form Leads"; 
            $mail_setting_message = '[lf-new-form-data]';
            $multi_mail = "";
            $mail_setting_header  = "New Lead Received";
        if (!empty($mail_setting_result)) {
            $mail_setting_result = maybe_unserialize($mail_setting_result);
            $mail_setting_to = $mail_setting_result['email_setting']['to'];
            $mail_setting_from = $mail_setting_result['email_setting']['from'];
            $mail_setting_subject = $mail_setting_result['email_setting']['subject'];
            $mail_setting_message = $mail_setting_result['email_setting']['message'];
            $multi_mail = (isset($mail_setting_result['email_setting']['multiple']))?$mail_setting_result['email_setting']['multiple']:'';

            $mail_setting_header = (isset($mail_setting_result['email_setting']['header']))?$mail_setting_result['email_setting']['header']:$mail_setting_header;
        }
        echo "<form id='form-email-setting' action='' method='post'>
    <div class='inside email_setting_section'>
     <div class='card'>
     <div class='infobox'>
        <h2>Admin Email Notifications</h2><br>
        <table class='form-table'>
            <tbody>
                <tr><th scope='row'><label for='email_setting_to'>To".LFB_REQUIRED_SIGN."</label></th>
                    <td><input name='email_setting[to]' required type='email' id='email_setting_to' value='" . $mail_setting_to . "' class='regular-text'>
                        <p class='description' id='from-description'>To address for emails.</p></td>
                </tr>
                <tr><th scope='row'><label for='multiemail_setting_from'>Multiple Email Recieved</label></th>
                    <td>
                    <textarea name='email_setting[multiple]' id='email_setting_message' rows='2' cols='46'>" . $multi_mail . "</textarea></label>
                        <p class='description' id='message-description'>Multiple Email: Comma separated emails. <b>like: abc@gmail.com,xyz@yahoo.com,abc@yahoo.com</b></p></td>
                </tr>
                <tr><th scope='row'><label for='email_setting_from'>From".LFB_REQUIRED_SIGN."</label></th>
                    <td><input name='email_setting[from]' required type='email' id='email_setting_from' value='" . $mail_setting_from . "' class='regular-text'>
                        <p class='description' id='from-description'>From address for emails.</p></td>
                </tr>
                <tr>
                    <th scope='row'><label for='email_setting_header'>Header".LFB_REQUIRED_SIGN."</label></th>
                    <td><input name='email_setting[header]' type='text' id='email_setting_header' value='" . $mail_setting_header . "' class='regular-text' required>
                        <p class='description' id='header-description'>Your emails header line.</p></td>
                </tr>
                <tr>
                    <th scope='row'><label for='email_setting_subject'>Subject".LFB_REQUIRED_SIGN."</label></th>
                    <td><input name='email_setting[subject]' type='text' id='email_setting_subject' value='" . $mail_setting_subject . "' class='regular-text' required>
                        <p class='description' id='subject-description'>Your emails subject line.</p></td>
                </tr>
                <tr>
                    <th scope='row'><label for='email_setting_message'>Message".LFB_REQUIRED_SIGN."</th>
                    <td>
                        <textarea name='email_setting[message]' id='email_setting_message' rows='5' cols='46' required>" . $mail_setting_message . "</textarea></label>
                        <p class='description' id='message-description'>Type your message here.<br/> Use This code </i><b> [lf-new-form-data] </b></i> in your message box to get all form entries in email.</p></td>
                    </td>
                </tr>
                <tr>
                    <td><input type='hidden' name='email_setting[form-id]' required value='" . $this_form_id . "'> 
                    <input type='submit' class='button-primary' id='button' value='Save'></p>
                    </td>
                </tr>
            </tbody></table>
    </div>  <div id='error-message-email-setting'></div></div></div>
</form>";


            $usermail_setting_from      = get_option('admin_email');
            $usermail_setting_subject   = "Received a lead";
            $usermail_setting_message   = "Form Submitted Successfully";
            $usermail_setting_option    = "OFF";
            $usermail_setting_header    = 'New Lead Received';
        if (!empty($usermail_setting)) {
            $usermail_setting_result = maybe_unserialize($usermail_setting);
            $usermail_setting_from = $usermail_setting_result['user_email_setting']['from'];
            $usermail_setting_subject = $usermail_setting_result['user_email_setting']['subject'];
            $usermail_setting_message = $usermail_setting_result['user_email_setting']['message'];
            $usermail_setting_option = $usermail_setting_result['user-email-setting-option'];
            $usermail_setting_header = (isset($usermail_setting_result['user_email_setting']['header']))?$usermail_setting_result['user_email_setting']['header']:$usermail_setting_header;

            
        }

echo "<form id='form-user-email-setting' action='' method='post'>
    <div class='inside email_setting_section'>
     <div class='card'>
     <div class='infobox'>
        <h2>User Email Notifications</h2><br>
        <p>To send email to user on form submit please make sure that the form must contain one <b>Email</b> named field to collect emails of users.</p>
        <table class='form-table'>
            <tbody>
                <tr><th scope='row'><label for='user_email_setting_from'>From".LFB_REQUIRED_SIGN."</label></th>
                    <td><input name='user_email_setting[from]' required type='email' id='user_email_setting_from' value='" . $usermail_setting_from . "' class='regular-text'>
                        <p class='description' id='from-description'>From address for emails.</p></td>
                </tr>

                <tr>
                    <th scope='row'><label for='user_email_setting_header'>Header".LFB_REQUIRED_SIGN."</label></th>
                    <td><input name='user_email_setting[header]' required type='text' id='user_email_setting_header' value='" . $usermail_setting_header . "' class='regular-text'>
                        <p class='description' id='header-description'>Your emails header line.</p></td>
                </tr>
                <tr>
                    <th scope='row'><label for='user_email_setting_subject'>Subject".LFB_REQUIRED_SIGN."</label></th>
                    <td><input name='user_email_setting[subject]' required type='text' id='user_email_setting_subject' value='" . $usermail_setting_subject . "' class='regular-text'>
                        <p class='description' id='subject-description'>Your emails subject line.</p></td>
                </tr>
                <tr>
                    <th scope='row'><label for='user_email_setting_message'>Message".LFB_REQUIRED_SIGN."</th>
                    <td>
                        <textarea name='user_email_setting[message]' id='user_email_setting_message' rows='5' cols='46' required>" . $usermail_setting_message . "</textarea></label>
                        <p class='description' id='message-description'>Type Your message here.<br/> Use This code </i><b> [lf-new-form-data] </b></i> in your message box to get all form entries in user email.</i></p></td>
                    </td>
                </tr>
                <tr>
                <th scope='row'><label for='user-email-setting'></th>
                <td>
                <p><input type='radio' name='user-email-setting-option' " . ($usermail_setting_option == 'ON' ? 'checked' : '' ) . " value='ON'><span>Send email to user when submit form.</span></p>
                <p><input type='radio' name='user-email-setting-option' " . ($usermail_setting_option == 'OFF' ? 'checked' : '' ) . " value='OFF'><span>Don't Send.</span></p>
                </td></tr>
                <tr>
                    <td><input type='hidden' name='user_email_setting[form-id]' required value='" . $this_form_id . "'> 
                    <input type='submit' class='button-primary' id='button' value='Save'></p>
                    </td>
                </tr>
            </tbody></table> </div>
    <div id='error-message-user-email-setting'></div></div> </div>
</form>
";

    }

    function lfb_captcha_setting_form($this_form_id, $captcha_option) {
        if (isset($captcha_option)) {
            $captcha_option_val = $captcha_option;
        } else {
            $captcha_option_val = "OFF";
        }
        $captcha_sitekey = get_option('captcha-setting-sitekey');
        $captcha_secret = get_option('captcha-setting-secret');
        echo '<div class="wrap">
<div class="card" id="recaptcha">
<div class="infobox">
<h2>Setup Captcha</h2><br>
<a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Get your Keys</a></div>
<br class="clear">
<div class="inside">
<p>reCAPTCHA is a free service to protect your website from spam and abuse.</p>
<form method="post" id="captcha-form" action="">
<table>
<tbody>
<tr>
    <th scope="row"><label for="sitekey">Site Key</label></th>
    <td><input type="text" required value="' . $captcha_sitekey . '" id="sitekey" name="captcha-setting-sitekey" class="regular-text code"></td>
</tr>
<tr>
    <th scope="row"><label for="secret">Secret Key</label></th>
    <td><input type="text" required value="' . $captcha_secret . '" id="secret" name="captcha-setting-secret" class="regular-text code"></td>
</tr>
</tbody>
</table>
<input type="hidden" name="captcha-keys" required value="' . $this_form_id . '">
<p class="submit"><input type="submit" class="button button-primary" id="captcha_save_settings" value="Save" name="submit"></p>
</form><br/>
<div id="error-message-captcha-key"></div>
</div>
</div>
</div>';
        if ($captcha_sitekey) {
            echo '<div class="inside setting_section">
           <div class="card">
                <form name="" id="captcha-on-off-setting" method="post" action="">
                <h2>Captcha On/Off Option</h2>
                <p><input type="radio" name="captcha-on-off-setting" ' . ($captcha_option_val == "ON" ? 'checked' : "" ) . ' value="ON"><span>Enable</span></p>
                <p><input type="radio" name="captcha-on-off-setting" ' . ($captcha_option_val == "OFF" ? 'checked' : "" ) . ' value="OFF"><span>Disable</span></p>
                <p><input type="submit" class="button button-primary" id="captcha_on_off_form_id" value="Save"></p>
                <input type="hidden" name="captcha_on_off_form_id" required value="' . $this_form_id . '">
                </form><br/>
<div id="error-message-captcha-option"></div>            
            </div>
            </div>';
        }
    }

    function lfb_lead_setting_form($this_form_id, $lead_store_option) {
        if (isset($lead_store_option)) {
            $lead_store_option = $lead_store_option;
        } else {
            $lead_store_option = 2;
        }
        echo '<div class="inside setting_section lead-receiving">
           <div class="card">
                <form name="" id="lead-email-setting" method="post" action="">
                <h2>Lead Receiving Method</h2>
                <p><input type="radio" name="data-recieve-method" ' . ($lead_store_option == 1 ? 'checked' : "" ) . ' value="1"><span>Receive Leads in Email</span></p>
                <p><input type="radio" name="data-recieve-method" ' . ($lead_store_option == 2 ? 'checked' : "" ) . ' value="2"><span>Save Leads in database (you can see all leads in the lead option)</span></p>
                <p><input type="radio" name="data-recieve-method" ' . ($lead_store_option == 3 ? 'checked' : "" ) . ' value="3"><span>Receive Leads in Email and Save in database</span><br><span id="data-rec-met-err"></span></p>
                <p><input type="submit" class="button button-primary" id="advance_lead_setting" value="Update"></p>
                <input type="hidden" name="action-lead-setting" value="' . $this_form_id . '">    
                </form><br/>
<div id="error-message-lead-store"></div>          
            </div>
            </div>';
            
        global $wpdb;
        $th_save_db = new LFB_SAVE_DB($wpdb);
        $table_name = LFB_FORM_FIELD_TBL;
        $prepare_10 =  $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d LIMIT 1",$this_form_id );
        $posts = $th_save_db->lfb_get_form_content($prepare_10);
        if(isset($posts[0]->multiData)){
            $multidata = unserialize($posts[0]->multiData);
            $successMsg = isset($multidata['lfb_success_msg'])?$multidata['lfb_success_msg']:'';
            $redirectUrl = isset($multidata['lfb_redirect_url'])?$multidata['lfb_redirect_url']:'';
        }else{
            $successMsg =__("Thank You ...",'lead-form-builder');
            $redirectUrl = '';
        }
            echo '<div class="inside setting_section lead-form-setting">
            <div class="card">
                <form name="" id="lfb-form-success-msg" method="post" action="">
                <h2>Form submitting Message (Thankyou Message)</h2>
                <div class="tablenav top">
                <p>
                 <textarea name="lfb_success_msg" id="lfb_success_msg">'.$successMsg.'</textarea> 
                 <br/>                
                 <i>This message will display to the visitor at your site. After submitting form.</i>

                </p>
                <h2>Redirect Url</h2>
                <p>
                 <input name="lfb_redirect_url" id="lfb_redirect_url" value="'.$redirectUrl.'">
                 <p><i>Visitor will be redirected to this URL after submitting form.</i></p>
                 <i> Enter full url like : http://themehunk.com/thankyou</i>
                </p>
                </div>
                            '.LFB_FORM_PRO_TEXT.LFB_FORM_PRO_FEATURE.'       
            </form>
            </div>
            </div>';
    }
}