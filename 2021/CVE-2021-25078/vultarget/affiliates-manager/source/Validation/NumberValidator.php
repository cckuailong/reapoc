<?php
/**
 * @author John Hargrove
 * 
 * Date: Sep 22, 2010
 * Time: 9:49:53 PM
 */


class WPAM_Validation_NumberValidator implements WPAM_Validation_IValidator
{
	function getError()
	{
		return __( 'must be a number', 'affiliates-manager' );
	}

	function isValid($value)
	{
		return is_numeric($value);
	}
}

