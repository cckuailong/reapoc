<?php
class Validation_Required extends Validation {
	public $message = "";

	function __construct(){
		$this->message= " %element% ".RM_UI_Strings::get("ERROR_REQUIRED");
	}

	public function isValid($value) {
		$valid = false;
                $value= is_scalar($value) ? trim($value): $value;
                if(is_array($value) && isset($value['original'])){
                    $value = $value['original'];
                }
		if(!is_null($value) && ((!is_array($value) && $value !== "") || (is_array($value) && !empty($value))))
			$valid = true;
		return $valid;	
	}
    
    public function getMessage() {
		return $this->message;
	}
}
