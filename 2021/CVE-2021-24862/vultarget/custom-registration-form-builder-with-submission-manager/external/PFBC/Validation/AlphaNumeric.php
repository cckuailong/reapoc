<?php
class Validation_AlphaNumeric extends Validation_RegExp {
	public $message = null;

	public function __construct($message = "") {
                $this->message= __('Error: %element% must be alphanumeric (contain only numbers, letters, underscores, and/or hyphens).','custom-registration-form-builder-with-submission-manager');
		parent::__construct("/^[a-zA-Z0-9_-]+$/", $message);
	}
}
