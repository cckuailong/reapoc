<?php
class RM_User_Email_Validator implements RM_Validator
{
    public $field_id;
    
    public function __construct($field_id) {
        $this->field_id= $field_id;
    }
    
    public function is_valid($value)
    {
        if(is_user_logged_in())
            return true;
        
        $exists= email_exists($value);
        if($exists)
            return false;
        else
            return true;
    }
}