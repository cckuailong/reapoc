<?php

/*
 * Handles unattended jobs aka Cron jobs.
 * 
 * 
 */

class RM_Job_Manager {

    public static $job_batch = array();

    public static function dump() {
        echo('<pre>');
        print_r($job_batch);
        /*
          echo "<br><br>DB result:<br>";
          {
          $result = RM_DBManager::get_submissions_for_form($job_batch[0]->form_id, $job_batch[0]->job_size, $job_batch[0]->offset, 'user_email');
          print_r($result);
          } */
        echo('</pre>');
    }

    /*
      public function _construct($interval = 30)
      {
      self::$interval = $interval;
      //add_filter('cron_schedules', array(static,'add_custom_interval'));
      }
     */

    public static function add_job($form_id, array $mail_packet, $mails_in_one_go = 10, $optional_callback = null) {
        $job_batch = get_option("rm_option_jobman_job", null);
        $inv_service = new RM_Invitations_Service;
        if ($job_batch == null)
            $job_batch = array();

        $test = RM_DBManager::count('SUBMISSIONS', array('form_id' => $form_id, 'user_email' => 'not null'));

        //Test if there is an 'user_email' entry for the given form
        if ($test === false || $test === 0)
            return false;

        $job = new stdClass();

        $job->form_id = $form_id;
        $job->job_size = $mails_in_one_go;
        $job->callback = $optional_callback;
        $job->offset = 0;
        $job->mail_packet = $mail_packet;
        $job->total = $inv_service->get_resp_count($form_id);

        $job->started_on = RM_Utilities::get_current_time();

        $form_fields = array();
        $fields = self::get_fields($form_id);

        if ($fields) {
            foreach ($fields as $field) {
                $form_fields[] = $field->field_type . "_" . $field->field_id;
            }
        }

        $job->form_fields = $form_fields;


        $job_batch["_" . $form_id] = $job;
        //error_log('job added');
        // self::log_var_dump($job_batch);
        update_option("rm_option_jobman_job", $job_batch);
        //error_log('exiting from add_job');

        return true;
    }

    public static function remove_job($form_id) {
        $job_batch = get_option("rm_option_jobman_job", null);

        if ($job_batch == null)
            return;

        if (isset($job_batch["_" . $form_id]))
            unset($job_batch["_" . $form_id]);
        
        if (wp_next_scheduled('rm_job_hook'))
            wp_unschedule_event(wp_next_scheduled('rm_job_hook'), 'rm_job_hook');

        update_option("rm_option_jobman_job", $job_batch);
    }

    public static function do_job() {
        //error_log("******* in the chronos ********");

        $job_batch = get_option("rm_option_jobman_job", null);

        if ($job_batch == null)
            return;

        //echo "<br>Batch:<br>";
        //return;
        //self::log_var_dump($job_batch);
        $inv_service = new RM_Invitations_Service;
        $gopts = new RM_Options;
        $from_email = $gopts->get_value_of('senders_email_formatted');
        $header = "From: $from_email\r\n";
        $header.= "Content-Type: text/html; charset=utf-8\r\n";

        foreach ($job_batch as $key => $job) {
            $results = $inv_service->get_subs_to_process($job->form_id, $job->job_size, $job->offset);
            //echo "<br>in foreach:<br>";
            //var_dump($results);
            if ($results != false) {
                foreach ($results as $result) {
                    //error_log("Doing a job, email: " . $result->user_email);
                    $sub_values = maybe_unserialize($result->data);
                    $processed_msg = $job->mail_packet['message'];

                    foreach ($job->form_fields as $field_placeholder) {
                        $abab = explode("_", $field_placeholder);
                        $field_id = $abab[1];

                        if (isset($sub_values[$field_id])) {
                            if (is_array($sub_values[$field_id]->value))
                            {
                                if($sub_values[$field_id]->type == 'Checkbox')
                                    $sub_values[$field_id]->value = implode(",", RM_Utilities::get_lable_for_option ($field_id, $sub_values[$field_id]->value));
                                else
                                    $sub_values[$field_id]->value = implode(",", $sub_values[$field_id]->value);
                            }
                            else
                            {
                                if($sub_values[$field_id]->type == 'Radio' || $sub_values[$field_id]->type == 'Select')
                                    $sub_values[$field_id]->value = RM_Utilities::get_lable_for_option ($field_id, $sub_values[$field_id]->value);                                
                            }

                            $processed_msg = str_replace("{{" . $field_placeholder . "}}", $sub_values[$field_id]->value, $processed_msg);
                        }
                    }
                    //Remove remaining unreplaced placeholders
                    $processed_msg = preg_replace("/{{[^}]*}}/","",$processed_msg);
                    
                    $cron_mail = new stdClass;
                    $cron_mail->type = RM_EMAIL_BATCH;
                    $cron_mail->to = $result->user_email;
                    $cron_mail->header = $header;
                    $cron_mail->message = wpautop($processed_msg);
                    $cron_mail->subject = $job->mail_packet['subject'];
                    $cron_mail->attachments = array();
                    $cron_mail->exdata = array('form_id'=>$job->form_id);
                    RM_Utilities::send_mail($cron_mail);
                    //wp_mail($result->user_email, $job->mail_packet['subject'], $processed_msg, $header);
                }

                $job_batch[$key]->offset += count($results); //$job->job_size;

                if ($job->callback !== null)
                    $job->callback(true);
            }

            if ($job_batch[$key]->offset >= $job_batch[$key]->total)
                unset($job_batch[$key]);
        }

        update_option("rm_option_jobman_job", $job_batch);
    }

    public static function log_var_dump($expression) {
        ob_start();
        var_dump($expression);
        $result = ob_get_clean();
        error_log("expression " . $result);
    }

    public static function get_job_total($form_id) {
        $job_batch = get_option("rm_option_jobman_job", null);

        if (isset($job_batch["_" . $form_id])) {
            return $job_batch["_" . $form_id]->total;
        } else
            return null;
    }

    public static function get_job_offset($form_id) {
        $job_batch = get_option("rm_option_jobman_job", null);

        if (isset($job_batch["_" . $form_id])) {
            return $job_batch["_" . $form_id]->offset;
        } else
            return null;
    }

    public static function get_job_starting_time($form_id) {
        $job_batch = get_option("rm_option_jobman_job", null);

        if (isset($job_batch["_" . $form_id])) {
            return $job_batch["_" . $form_id]->started_on;
        } else
            return null;
    }

    public static function get_job_array() {
        return get_option("rm_option_jobman_job", null);
    }

    public static function get_fields($form_id) {
        $where = array("form_id" => $form_id);
        $data_specifier = array("%s", "%d");
        $email_fields = RM_DBManager::get(RM_Fields::get_identifier(), $where, $data_specifier, $result_type = 'results', $offset = 0, $limit = 1000, $column = '*', $sort_by = null, $descending = false);
        $fields = array();

        foreach ($email_fields as $field) {
            if ($field->field_type != 'Price' && $field->field_type != 'HTMLH' && $field->field_type != 'File' && $field->field_type != 'HTMLP' && $field->field_type != 'Terms') {
                $fields[] = $field;
            }
        }

        return $fields;
    }

}
