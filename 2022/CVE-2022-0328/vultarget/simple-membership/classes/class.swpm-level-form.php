<?php

class SwpmLevelForm {

    protected $fields;
    protected $op;
    protected $errors;
    protected $sanitized;

    public function __construct($fields) {
        $this->fields = $fields;
        $this->sanitized = array();
        $this->errors = array();
        
        foreach ($fields as $key => $value){
            $this->$key();
        }
    }

    protected function id() {
        
    }

    protected function alias() {
        $alias = filter_input(INPUT_POST, 'alias');
        $this->sanitized['alias'] = sanitize_text_field($alias);
    }

    protected function role() {
        $role = filter_input(INPUT_POST, 'role');
        $this->sanitized['role'] = sanitize_text_field($role);
    }

    protected function permissions() {
        $this->sanitized['permissions'] = 63;
    }

    protected function subscription_period() {
        $subscript_duration_type = filter_input(INPUT_POST, 'subscription_duration_type');

        if ($subscript_duration_type == SwpmMembershipLevel::NO_EXPIRY) {
            $this->sanitized['subscription_period'] = "";
            return;
        }

        $subscription_period = filter_input(INPUT_POST, 'subscription_period_'. $subscript_duration_type);
        if (($subscript_duration_type == SwpmMembershipLevel::FIXED_DATE)){
            $dateinfo = date_parse($subscription_period);
            if ($dateinfo['warning_count']|| $dateinfo['error_count']){
                $this->errors['subscription_period'] = SwpmUtils::_("Date format is not valid.");
                return;
            }
            $this->sanitized['subscription_period'] = sanitize_text_field($subscription_period);
            return;
        }
        
        if (!is_numeric($subscription_period)) {
            $this->errors['subscription_period'] = SwpmUtils::_("Access duration must be > 0.");
            return;
        }
        $this->sanitized['subscription_period'] = sanitize_text_field($subscription_period);
    }

    protected function subscription_duration_type(){
        $subscription_duration_type = filter_input(INPUT_POST, 'subscription_duration_type');
        $this->sanitized['subscription_duration_type'] = $subscription_duration_type;
        return;
    }
    protected function subscription_unit(){
        
    }
    protected function loginredirect_page() {
        
    }

    protected function category_list() {
        
    }

    protected function page_list() {
        
    }

    protected function post_list() {
        
    }

    protected function comment_list() {
        
    }

    protected function attachment_list() {
        
    }

    protected function custom_post_list() {
        
    }

    protected function disable_bookmark_list() {
        
    }

    protected function options() {
        
    }

    protected function campaign_name() {
        
    }

    protected function protect_older_posts() {
        $checked = filter_input(INPUT_POST, 'protect_older_posts');
        $this->sanitized['protect_older_posts'] = empty($checked) ? 0 : 1;
    }

    public function is_valid() {
        return count($this->errors) < 1;
    }

    public function get_sanitized() {
        return $this->sanitized;
    }

    public function get_errors() {
        return $this->errors;
    }

}
