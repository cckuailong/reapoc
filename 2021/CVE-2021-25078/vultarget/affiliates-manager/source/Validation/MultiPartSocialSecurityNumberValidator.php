<?php
/**
 * @author John Hargrove
 * 
 * Date: 11/21/10
 * Time: 8:07 PM
 */

class WPAM_Validation_MultiPartSocialSecurityNumberValidator implements WPAM_Validation_IValidator
{
	function getError()
	{
		return __( 'must be a valid social security number', 'affiliates-manager' );
	}

	function isValid($value)
	{
		return (
			preg_match('/^\\d{3}$/', $value[0])
			&& preg_match('/^\\d{2}$/', $value[1])
			&& preg_match('/^\\d{4}$/', $value[2])
		);
	}
}
