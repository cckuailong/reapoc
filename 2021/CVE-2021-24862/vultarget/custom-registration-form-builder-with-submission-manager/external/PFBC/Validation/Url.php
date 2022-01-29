<?php
class Validation_Url extends Validation {
	public $message;

   public function __construct($message = "") {
		//if(!empty($message))
		$this->message = RM_UI_Strings::get('FORM_ERR_INVALID_URL');
	}

	public function isValid($value) {
		if($this->isNotApplicable($value) || filter_var($value, FILTER_VALIDATE_URL))
			return true;
		return false;	
	}
    
    public function isNotApplicable($value) {
		if(is_null($value) || is_array($value) || $value === "")
			return true;
		return false;
	}
    
    public function getMessage() {
		return $this->message;
	}
}
