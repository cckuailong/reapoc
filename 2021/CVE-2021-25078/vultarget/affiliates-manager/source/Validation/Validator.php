<?php
/**
 * @author John Hargrove
 * 
 * Date: May 30, 2010
 * Time: 10:15:52 PM
 */

require_once WPAM_BASE_DIRECTORY . "/source/Validation/IValidator.php";
require_once WPAM_BASE_DIRECTORY . "/source/Validation/ValidatorResult.php";

class WPAM_Validation_Validator
{
	private $validators = array();

	public function __construct() {
		$this->result = new WPAM_Validation_ValidatorResult();
	}

	public function addValidator($field, WPAM_Validation_IValidator $validator)
	{
		if (!array_key_exists($field, $this->validators))
			$this->validators[$field] = array();

		$this->validators[$field][] = $validator;
	}

	public function validate($request)
	{
		foreach ($this->validators as $field => $validators)
		{
			foreach ($validators as $validator)
			{
				if (!$validator->isValid($request[$field]))
				{
					$this->addError( new WPAM_Validation_ValidatorError( $field, $validator->getError() ) );
				}
			}
		}
		return $this->result;
	}

	public function addError( WPAM_Validation_ValidatorError $error ) {
		$this->result->addError( $error );
	}
}
