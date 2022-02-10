<?php

require_once WPAM_BASE_DIRECTORY . "/source/Validation/ValidatorError.php";
class WPAM_Validation_ValidatorResult
{
	public function getErrors() { return $this->errors; }
	public function getIsValid() { return count($this->errors) == 0; }

	private $errors = array();

	public function addError(WPAM_Validation_ValidatorError $error)
	{
		$this->errors[] = $error;
	}
}