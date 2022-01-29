<?php
class Validation_Date extends Validation {
    public $message;

    public function __construct($message = "") {
		//if(!empty($message))
		$this->message = RM_UI_Strings::get('FORM_ERR_INVALID_DATE');
	}

    public function isValid($value) {
        return true;
        /*try {
            $date = new DateTime($value);
            return true;
        } catch(Exception $e) {
            return false;
        }*/
    }
    
    public function getMessage() {
		return $this->message;
	}
}
