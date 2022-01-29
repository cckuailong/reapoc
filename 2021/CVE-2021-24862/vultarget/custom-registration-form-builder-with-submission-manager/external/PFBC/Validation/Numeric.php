<?php
class Validation_Numeric extends Validation {
	public $message;

	public function __construct($message = "") {
		//if(!empty($message))
		$this->message = RM_UI_Strings::get('FORM_ERR_INVALID_NUMBER');
	}
    
    public function getMessage() {
		return $this->message;
	}

    public function isNotApplicable($value) {
		if(is_null($value) || is_array($value) || $value === "")
			return true;
		return false;
	}

	public function isValid($value) {
		if($this->isNotApplicable($value) || is_numeric($value))
			return true;
		return false;	
	}
}
