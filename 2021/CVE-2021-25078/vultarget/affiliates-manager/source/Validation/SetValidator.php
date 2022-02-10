<?php
/**
 * @author John Hargrove
 * 
 * Date: May 31, 2010
 * Time: 4:17:41 PM
 */

class WPAM_Validation_SetValidator implements WPAM_Validation_IValidator
{
	private $options;
	public function __construct($validOptions)
	{
		$this->options = $validOptions;
	}
	function getError()
	{
		return __( 'must be selected', 'affiliates-manager' );
	}

	function isValid($value)
	{
		return in_array($value, $this->options);
	}
}
