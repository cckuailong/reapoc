<?php
class Validation_Unique extends Validation{
	public $message;
        public $handler;
        
	public function __construct($message = "",$handler=null) {
                $this->handler= $handler;
		//if(!empty($message))
		$this->message = empty($message) ? __('Error: %element%  should be unique.','custom-registration-form-builder-with-submission-manager') : $message;
	}
        
        public function getMessage() {
		return $this->message;
	}

	public function isNotApplicable($value) {
		if(is_null($value) || is_array($value) || $value === "")
			return true;
		return false;
	}

	public function isValid($value)
        {
            if($this->handler==null)
                return true;
            return $this->handler->is_valid($value);
        }

}