<?php

/**
 *
 *
 * @author CMSHelplive
 */
class RM_Sent_Emails_Service extends RM_Services
{

  public function get_sent_email($email_id)
  {
      return RM_DBManager::get('SENT_EMAILS', array('mail_id'=>$email_id), array('%d'));
  }
  
  public function send_email()
  {
      return RM_DBManager::get('SENT_EMAILS', 1, null);
  }

}