<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RM_Notes_Service
 *
 * @author CMSHelplive
 */
class RM_Note_Service extends RM_Services
{
    public function notify_users($note){
        if(defined('REGMAGIC_ADDON')) {
            $addon_service = new RM_Note_Service_Addon();
            return $addon_service->notify_users($note, $this);
        }
        $gopt = new RM_Options;
        if($gopt->get_value_of('user_notification_for_notes')=="yes")
        {
            if($note->get_status() != 'publish')
                return;

            $submission= new RM_Submissions();
            $submission->load_from_db($note->get_submission_id());
            $email= new stdClass();
            //echo '<pre>';
            //print_r($submission); die;
            $email->to= $submission->get_user_email();
            $from_email= $gopt->get_value_of('senders_email_formatted');
            $header = "From: $from_email\r\n";
            $header.= "MIME-Version: 1.0\r\n";
            $header.= "Content-Type: text/html; charset=utf-8\r\n";
            $email->type = RM_EMAIL_NOTE_ADDED;
            $email->subject= get_bloginfo( 'name', 'display' )." ".__("Notification from Admin",'custom-registration-form-builder-with-submission-manager')." " ;
            $email->message= RM_UI_Strings::get('MSG_NOTE_FROM_ADMIN').$note->get_notes();
            $email->header= $header;
            $email->attachments = array();
            $form_id = $submission->get_form_id();
            $email->exdata = array('form_id'=>$form_id, 'exdata' => $sub_id);
            RM_Utilities::send_mail($email);
        }
    }
}