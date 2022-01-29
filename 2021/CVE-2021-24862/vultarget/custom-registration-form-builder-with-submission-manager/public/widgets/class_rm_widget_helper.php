<?php

class RM_Widget_Helper {

    public function getLoginForm() {
        include_once('html/login.php');
    }

    public function getSubmissions() {
        $rm_services = new RM_Services();
        $rm_front_service = new RM_Front_Service();
        $user_email = $rm_front_service->get_user_email();
        if (!empty($user_email)) {
            $submissions = $rm_services->get_submissions_by_email($user_email);
            if (!empty($submissions)) {
                foreach ($submissions as $submission) {
                    $submission->form_name = $rm_services->get('FORMS', array('form_id' => $submission->form_id), array('%d'), 'var', 0, 1, 'form_name');
                }
                include_once('html/submissions.php');
            } else {
                echo '<div class="rmnotice-container"><div class="rmnotice">' . RM_UI_Strings::get('MSG_NO_DATA_FOR_EMAIL') . '</div></div>';
            }
        }
    }


    public function getPayments() {
        $rm_services = new RM_Services();
        $rm_front_service = new RM_Front_Service();
        $user_email = $rm_front_service->get_user_email();
        $payments = array();
        if (!empty($user_email)) {
            $submissions = $rm_services->get_submissions_by_email($user_email);
            if (!empty($submissions)) {
                foreach ($submissions as $submission) {
                    $oldest_sub = $rm_front_service->get_oldest_submission_from_group($submission->submission_id);
                    $payment = $rm_services->get_payments_by_submission_id($oldest_sub);
                    if ($payment) {
                        $payment->form_name = $rm_services->get('FORMS', array('form_id' => $submission->form_id), array('%d'), 'var', 0, 1, 'form_name');
                        $payments[] = $payment;
                    }
                }
            }

            if (!empty($payments)) {
                include_once('html/payments.php');
            } else {
                echo '<div class="rmnotice-container"><div class="rmnotice">' . RM_UI_Strings::get('MSG_NO_DATA_FOR_EMAIL') . '</div></div>';
            }
        }
    }

    public function get_account() {
        $service = new RM_Front_Service();
        $user_email = $service->get_user_email();
        $user = get_user_by('email', $user_email);
        $data = new stdClass;
        if ($user instanceof WP_User) {
            $data->is_user = true;
            $data->user = $user;
            $data->custom_fields = $service->get_custom_fields($user_email);
            if($data->custom_fields){
            foreach($data->custom_fields as $id => $f)
                if($f->type == 'Rating')unset($data->custom_fields[$id]);
            }
            $widget_template_account = locate_template("registrationmagic/templates/account.php");
            if(file_exists($widget_template_account))
            {
                include_once $widget_template_account;
            } 
            else{
                include_once 'html/account.php';
            }
        }
    }

    public function get_user_level() {
        if (is_user_logged_in())
            return 0x4;

        $service = new RM_Front_Service;
        $user_email = $service->get_user_email();
        if ($user_email) {
            $user = get_user_by('email', $user_email);
            if ($user instanceof WP_User)
                $user_level = 0x4;
            else
                $user_level = 0x2;
        } else
            $user_level = 0x1;

        return $user_level;
    }

    public function get_user() {
        $service = new RM_Front_Service();
        $user_email = $service->get_user_email();
        $user_login=$service->get_user_login_name();
        if($user_email)
            $user = get_user_by('email', $user_email);
        else
            $user = get_user_by('login', $user_login);
        $data = new stdClass;
        if ($user instanceof WP_User)
            return $user;
        else
            return false;
    }

    public function get_fab_icon(){
        $rm_services = new RM_Services();
        $icon_src = RM_IMG_URL . "user.png";
        $fab_icon = $rm_services->get_setting('fab_icon');
        if($fab_icon){
            $src = wp_get_attachment_image_url($fab_icon);
            if($src){
                $icon_src = $src;
            }
        }
        return $icon_src;
    }
    
    public function check_link_show($visibility)
    {
        if($visibility=='everyone')
        {
            return true;
        }
        elseif($visibility=='unreg')
        {
            return  !is_user_logged_in();
                  
        }
        elseif($visibility=='reg'){
             return  is_user_logged_in();
        }
        else{
            if(!is_user_logged_in())
                return false;
            else{
                $user=$this->get_user();
                if(in_array($visibility, $user->roles))
                        return true;
                else
                    return false;
            }
        }
    }
}