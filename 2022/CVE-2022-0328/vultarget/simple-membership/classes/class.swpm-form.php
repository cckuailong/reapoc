<?php

class SwpmForm {

    protected $fields;
    protected $op;
    protected $errors;
    protected $sanitized;

    public function __construct($fields) {
        $this->fields = $fields;
        $this->sanitized = array();
        $this->errors = array();
        $this->validate_wp_user_email();
        if ($this->is_valid()){
            foreach ($fields as $key => $value){
                $this->$key();
            }
        }
    }
    protected function validate_wp_user_email(){
        $user_name = filter_input(INPUT_POST, 'user_name',FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW);
        if (empty($user_name)) {
            return;
        }

        $user = get_user_by('login', $user_name);
        if ($user && ($user->user_email != $email)){
            $error_msg = SwpmUtils::_("Wordpress account exists with given username. But the given email doesn't match.");
            $error_msg .= SwpmUtils::_(" Use a different username to complete the registration. If you want to use that username then you must enter the correct email address associated with the existing WP user to connect with that account.");
            $this->errors['wp_email'] = $error_msg;
            return;
        }
        $user = get_user_by('email', $email);
        if($user && ($user_name != $user->user_login)){
            $error_msg = SwpmUtils::_("Wordpress account exists with given email. But the given username doesn't match.");
            $error_msg .= SwpmUtils::_(" Use a different email address to complete the registration. If you want to use that email then you must enter the correct username associated with the existing WP user to connect with that account.");
            $this->errors['wp_user'] = $error_msg;
        }
    }

    protected function user_name() {
        global $wpdb;
        if (!empty($this->fields['user_name'])){return;}
        $user_name = filter_input(INPUT_POST, 'user_name',FILTER_SANITIZE_STRING);
        if (empty($user_name)) {
            $this->errors['user_name'] = SwpmUtils::_('Username is required');
            return;
        }
        if (!SwpmMemberUtils::is_valid_user_name($user_name)) {
            $this->errors['user_name'] = SwpmUtils::_('Username contains invalid character');
            return;
        }
        $saned = sanitize_text_field($user_name);
        $query = "SELECT count(member_id) FROM {$wpdb->prefix}swpm_members_tbl WHERE user_name= %s";
        $result = $wpdb->get_var($wpdb->prepare($query, strip_tags($saned)));
        if ($result > 0) {
            if ($saned != $this->fields['user_name']) {
                $this->errors['user_name'] = SwpmUtils::_('Username already exists.');
                return;
            }
        }
        $this->sanitized['user_name'] = $saned;
    }

    protected function first_name() {
        $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
        $this->sanitized['first_name'] = sanitize_text_field($first_name);
    }

    protected function last_name() {
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
        $this->sanitized['last_name'] = sanitize_text_field($last_name);
    }

    protected function password() {
        $password = filter_input(INPUT_POST, 'password',FILTER_UNSAFE_RAW);
        $password_re = filter_input(INPUT_POST, 'password_re',FILTER_UNSAFE_RAW);
        if (empty($this->fields['password']) && empty($password)) {
            $this->errors['password'] = SwpmUtils::_('Password is required');
            return;
        }
        if (!empty($password)) {
            $saned = sanitize_text_field($password);
            $saned_re = sanitize_text_field($password_re);
            if ($saned != $saned_re){
                $this->errors['password'] = SwpmUtils::_('Password mismatch');
            }
            $this->sanitized['plain_password'] = $password;
            $this->sanitized['password'] = SwpmUtils::encrypt_password(trim($password)); //should use $saned??;
        }
    }

    protected function email() {
        global $wpdb;
        $email = filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW);
        if (empty($email)) {
            $this->errors['email'] = SwpmUtils::_('Email is required');
            return;
        }
        if (!is_email($email)) {
            $this->errors['email'] = SwpmUtils::_('Email is invalid') . " (".$email.")";
            return;
        }
        $saned = sanitize_email($email);
        $query = "SELECT count(member_id) FROM {$wpdb->prefix}swpm_members_tbl WHERE email= %s";
        $member_id = filter_input(INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT);
        if (!empty($member_id)) {
            $query .= ' AND member_id !=%d';
            $result = $wpdb->get_var($wpdb->prepare($query, strip_tags($saned), $member_id));
        }
        else{
            $result = $wpdb->get_var($wpdb->prepare($query, strip_tags($saned)));
        }

        if ($result > 0) {
            if ($saned != $this->fields['email']) {
                $error_msg = SwpmUtils::_('Email is already used.') . " (" . $saned .")";
                $this->errors['email'] = $error_msg;
                return;
            }
        }
        $this->sanitized['email'] = $saned;
    }

    protected function phone() {
        $phone = filter_input(INPUT_POST, 'phone', FILTER_UNSAFE_RAW);
        $saned = wp_kses($phone, array());
        $this->sanitized['phone'] = $saned;
        return;
    }

    protected function address_street() {
        $address_street = filter_input(INPUT_POST, 'address_street', FILTER_SANITIZE_STRING);
        $this->sanitized['address_street'] = wp_kses($address_street, array());
    }

    protected function address_city() {
        $address_city = filter_input(INPUT_POST, 'address_city', FILTER_SANITIZE_STRING);
        $this->sanitized['address_city'] = wp_kses($address_city, array());
    }

    protected function address_state() {
        $address_state = filter_input(INPUT_POST, 'address_state', FILTER_SANITIZE_STRING);
        $this->sanitized['address_state'] = wp_kses($address_state, array());
    }

    protected function address_zipcode() {
        $address_zipcode = filter_input(INPUT_POST, 'address_zipcode', FILTER_UNSAFE_RAW);
        $this->sanitized['address_zipcode'] = wp_kses($address_zipcode, array());
    }

    protected function country() {
        $country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
        $this->sanitized['country'] = wp_kses($country, array());
    }

    protected function company_name() {
        $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_STRING);
        $this->sanitized['company_name'] = $company_name;
    }

    protected function member_since() {
        $member_since = filter_input(INPUT_POST, 'member_since', FILTER_UNSAFE_RAW);
        if (empty($member_since)) {return;}
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $member_since)){
            $this->sanitized['member_since'] =  sanitize_text_field($member_since);
            return;
        }
        $this->errors['member_since'] = SwpmUtils::_('Member since field is invalid');

    }

    protected function subscription_starts() {
        $subscription_starts = filter_input(INPUT_POST, 'subscription_starts', FILTER_SANITIZE_STRING);
        if(empty($subscription_starts)) {return ;}
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $subscription_starts)){
            $this->sanitized['subscription_starts'] =  sanitize_text_field($subscription_starts);
            return;
        }
        $this->errors['subscription_starts'] = SwpmUtils::_('Access starts field is invalid');
    }

    protected function gender() {
        $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
        if(empty($gender)) {return;}
        if (in_array($gender, array('male', 'female', 'not specified'))){
            $this->sanitized['gender'] = $gender;
        }
        else{
            $this->errors['gender'] = SwpmUtils::_('Gender field is invalid');
        }
    }

    protected function account_state() {
        $account_state = filter_input(INPUT_POST, 'account_state', FILTER_SANITIZE_STRING);
        if(empty($account_state)) {return;}
        if (in_array($account_state, array('active', 'pending', 'activation_required', 'inactive', 'expired'))){
            $this->sanitized['account_state'] = $account_state;
        }
        else{
            $this->errors['account_state'] = SwpmUtils::_('Account state field is invalid');
        }
    }

    protected function membership_level() {
        $membership_level = filter_input(INPUT_POST, 'membership_level', FILTER_SANITIZE_NUMBER_INT);
        if ($membership_level == 1){
            $this->errors['membership_level'] = SwpmUtils::_('Invalid membership level');
            return;
        }

        if (empty($membership_level)) {return;}
        $this->sanitized['membership_level'] = $membership_level;
    }

    protected function password_re() {

    }

    protected function last_accessed() {

    }

    protected function last_accessed_from_ip() {

    }

    protected function referrer() {

    }

    protected function extra_info() {

    }

    protected function reg_code() {

    }

    protected function txn_id() {

    }

    protected function subscr_id() {
        $subscr_id = filter_input(INPUT_POST, 'subscr_id', FILTER_SANITIZE_STRING);
        $this->sanitized['subscr_id'] = $subscr_id;
    }

    protected function flags() {

    }

    protected function more_membership_levels() {

    }

    protected function initial_membership_level() {

    }

    protected function home_page() {

    }

    protected function notes() {

    }

    protected function profile_image() {

    }

    protected function expiry_1st() {

    }

    protected function expiry_2nd() {

    }

    protected function member_id() {

    }

    public function is_valid() {

        if (!isset($this->errors)){
            //Errors are not set at all. Return true.
            return true;
        }

        return count($this->errors) < 1;
    }

    public function get_sanitized_member_form_data() {
        return $this->sanitized;
    }

    public function get_errors() {
        return $this->errors;
    }

}
