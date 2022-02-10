<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
Class LFB_LeadStoreType{

function lfb_phpmailer_active( $fid ) {
    $lfb_leadform = NEW LFB_SAVE_DB();
   
    $smtp  = $lfb_leadform->lfb_get_ext_data($fid,2);
    $this->active = isset($smtp[0]->active)?$smtp[0]->active:'';
   if($this->active):
    $this->smtpmail = isset($smtp[0]->ext_api)?unserialize($smtp[0]->ext_api):'';

   $this->smtp_name = isset($this->smtpmail['smtp_name'])?$this->smtpmail['smtp_name']:'';
    $this->smtp_server = isset($this->smtpmail['smtp_server'])?$this->smtpmail['smtp_server']:'';
    $this->smtp_port = isset($this->smtpmail['smtp_port'])?$this->smtpmail['smtp_port']:25;
    $this->smtp_enc_type = isset($this->smtpmail['smtp_enc_type'])?$this->smtpmail['smtp_enc_type']:'';
    $this->smtp_username = isset($this->smtpmail['smtp_username'])?$this->smtpmail['smtp_username']:'';
    $this->smtp_pass = isset($this->smtpmail['smtp_pass'])?$this->smtpmail['smtp_pass']:'';
        add_action( 'phpmailer_init',array($this,'lfb_phpmailer_send'));
   endif;
}



function lfb_phpmailer_send($phpmailer){
  //  remove_action( 'phpmailer_init', array($this,__function__ ));
    $phpmailer->isSMTP();    
    $phpmailer->Host = $this->smtp_server;
    $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
   $phpmailer->Port = $this->smtp_port;
   $phpmailer->Username = $this->smtp_username;
   $phpmailer->Password = $this->smtp_pass;

    // Additional settings…
   $phpmailer->SMTPSecure = $this->smtp_enc_type; // Choose SSL or TLS, if necessary for your server
    //$phpmailer->From = "you@yourdomail.com";
    //$phpmailer->FromName = "Your Name";  
  }

  function lfb_save_data($form_id,$form_data){
        $server_request = $_SERVER['HTTP_USER_AGENT'];
    	$ip_address = $this->lfb_get_user_ip_addres();
        global $wpdb;
        $data_table_name = LFB_FORM_DATA_TBL;

       $update_leads = $wpdb->query( $wpdb->prepare( 
         "INSERT INTO $data_table_name ( form_id, form_data, ip_address, server_request, date ) 
         VALUES ( %d, %s, %s, %s, %s )",
          $form_id, $form_data, $ip_address, $server_request, date('Y/m/d g:i:s') ) );
        if ($update_leads) {
            return "inserted";
        }
    }

    function lfb_mail_filter($form_id,$form_data){
      $th_save_db = new LFB_SAVE_DB($wpdb);
      $form_field = $th_save_db->lfb_admin_email_send($form_id);
      $form_data = maybe_unserialize($form_data);
      $i =0;
    $table = '<table rules="all" style="width: 80%; border: 1px solid #FBFBFB;"  cellpadding="10"><tbody>';
    foreach ($form_field as $key => $value) {
      $trnth = ($i%2)?'background:#FBFBFB;':'';

      if(isset($form_data[$key]) && is_array($form_data[$key])){
                      if(strstr($key, 'upload_')){

                        $upload_filename = isset($form_data[$key]['filename'])?$form_data[$key]['filename']:$form_data[$key]['error'];

                      $upload = isset($form_data[$key]['url'])?'<a target="_blank" href="'.$form_data[$key]["url"].'">'.$upload_filename.'</a>':$upload_filename;

                        $table .='<tr style="'.$trnth.'" ><td style="padding:8px;" ><strong>'.$value.'</strong></td><td style="padding:8px;" >'.$upload.'</td></tr>';

                    } else {
                      $fieldVal = implode(", ",$form_data[$key]);
                     $table .='<tr style="'.$trnth.'" ><td style="padding:8px;" ><strong>'.$value.'</strong></td><td style="padding:8px;" >'.$fieldVal.'</td></tr>';
                   }
      } else{
              $table .=(isset($form_data[$key]))?'<tr style="'.$trnth.'" ><td style="padding:8px;" ><strong> '.$value.'</td></strong><td style="padding:8px;" >'.$form_data[$key].'</td></tr>':'<tr style="'.$trnth.'" ><td style="padding:8px;" ><strong>'.$value.'</strong></td><td style="padding:8px;" > - </td></tr>';
      }
      $i++;
    }
          return $table ."</tbody></table>";
}
/** Admin Email Send **/
    function lfb_send_data_email($form_id,$form_data,$mail_setting,$user_email){
      $form_entry_data = $this->lfb_mail_filter($form_id,$form_data);

      $user_email['leads'] = $form_entry_data;
      $reply_to = $user_email['emailid'];
      $to = get_option('admin_email');
      $subject ='New Lead Recieved';
      $new_message = 'Recieved New Leads';

    	 $form_entry_data .=	"<br/>";
       $headers[] = 'Content-Type: text/html; charset=UTF-8';

       if(!empty($mail_setting)){
          $sitelink = preg_replace('#^https?://#', '', site_url());

         $to = $mail_setting['email_setting']['to'];
         $subject  = esc_html($mail_setting['email_setting']['subject']);
         $message  = $mail_setting['email_setting']['message'];
         $header = (isset($mail_setting['email_setting']['header']))?$mail_setting['email_setting']['header']:$sitelink;

         $shortcodes_a =  '[lf-new-form-data]';
         $shortcodes_b =  $form_entry_data;               
         $new_message = '';
         $new_message = ($message=='')?'New Leads':str_replace($shortcodes_a, $shortcodes_b, $message);

         $headers[] = "From:".$header." <".$mail_setting['email_setting']['from'].">";
          $headers[] = "Reply-To:".$header." <".$reply_to.">";

          $multiple = isset($mail_setting['email_setting']['multiple'])?$mail_setting['email_setting']['multiple']:'';
          if($multiple!=''){
            $explode = explode( ',',$multiple );
            if(is_array($explode)){
              foreach ( $explode as $bcc_email ) {
                $fname = explode( '@',$bcc_email );
               $bcc_head = isset($fname[0])?$fname[0]:'user';
                $headers[]= "Bcc:".$bcc_head." <".trim( $bcc_email ).">";

              } // foreach
            } // //is array
         } //explode
       }
       // Admin Email Send
       wp_mail( $to, $subject, $new_message, $headers);

              //user email send

       if(!empty($user_email['user_email_settings'])){
           $usermail_option = $user_email['user_email_settings']['user-email-setting-option'];
             $emailid =       $user_email['emailid'];

            if(($usermail_option =="ON") && ($emailid !='invalid_email') && is_email($emailid)){
                     $this->lfb_useremail_send($user_email);
           }
       }

    }

/** User Email Send **/  
function lfb_useremail_send($user_email){
   $usermail_setting = $user_email['user_email_settings'];
   $headers[] = 'Content-Type: text/html; charset=UTF-8';
   $to = $user_email['emailid'];
   $subject  =  $usermail_setting['user_email_setting']['subject'];
   $message  =  $usermail_setting['user_email_setting']['message'];
   $header   =  (isset($usermail_setting['user_email_setting']['header']))?$usermail_setting['user_email_setting']['header']:'Submit Form';

   $headers[] = "From:".$header." <".$usermail_setting['user_email_setting']['from'].">";
   $headers[] = "Reply-To:".$header." <".$usermail_setting['user_email_setting']['from'].">";  
   $shortcodes_a =  '[lf-new-form-data]';
   $shortcodes_b =  $user_email['leads'];               
   $new_message = str_replace($shortcodes_a, $shortcodes_b, $message);
   wp_mail( $to, $subject, $new_message, $headers);
  }

/** Mail send and Mail type
  * 1 = Recieve Leads in Email
  * 2 = Save Leads in database
  * 3 = Recieve Leads in Email and Save in database
  */
  function lfb_mail_type($form_id,$form_data,$lfbdb,$user_emailid){
      $return             = '';
      $posts              = $lfbdb->lfb_mail_store_type($form_id);
      $storeType          = $posts[0]->storeType;
      $mail_setting       = $posts[0]->mail_setting;
      $admin_mail_setting       = maybe_unserialize($mail_setting);
      $usermail_setting   = $posts[0]->usermail_setting;
      $usermail           = maybe_unserialize($usermail_setting);
      $user_email         = array('user_email_settings'=>$usermail,'emailid'=>$user_emailid);

      if ($storeType == 1) {
         $this->lfb_send_data_email($form_id, $form_data, $admin_mail_setting,$user_email);
         $return = "inserted";
      }
      if ($storeType == 2) {
         $return =  $this->lfb_save_data($form_id, $form_data);
      }
      if ($storeType == 3) {
          $return = $this->lfb_save_data($form_id, $form_data);
         $this->lfb_send_data_email($form_id, $form_data, $admin_mail_setting,$user_email);
      }
      echo $return;
  }

function lfb_get_user_ip_addres(){
          $ipaddress = '';
          if (getenv('HTTP_CLIENT_IP'))
              $ipaddress = getenv('HTTP_CLIENT_IP');
          else if(getenv('HTTP_X_FORWARDED_FOR'))
              $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
          else if(getenv('HTTP_X_FORWARDED'))
              $ipaddress = getenv('HTTP_X_FORWARDED');
          else if(getenv('HTTP_FORWARDED_FOR'))
              $ipaddress = getenv('HTTP_FORWARDED_FOR');
          else if(getenv('HTTP_FORWARDED'))
              $ipaddress = getenv('HTTP_FORWARDED');
          else if(getenv('REMOTE_ADDR'))
              $ipaddress = getenv('REMOTE_ADDR');
          else
              $ipaddress = 'UNKNOWN';
          return $ipaddress;
    }

}