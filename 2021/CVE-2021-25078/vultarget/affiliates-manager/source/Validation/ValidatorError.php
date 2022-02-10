<?php
/**
 * @author John Hargrove
 * 
 * Date: May 31, 2010
 * Time: 2:14:24 PM
 */

class WPAM_Validation_ValidatorError
{
	private $fieldName;
	private $message;

	public function getFieldName() { return $this->fieldName; }
	public function getMessage() { return $this->message; }

	public function __construct($fieldName, $message)
	{
		$this->fieldName = $fieldName;
		$this->message = $message;
	}
}
