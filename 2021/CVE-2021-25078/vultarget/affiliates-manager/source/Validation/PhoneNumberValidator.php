<?php
/**
 * @author John Hargrove
 * 
 * Date: Jul 14, 2010
 * Time: 10:10:55 PM
 */

class WPAM_Validation_PhoneNumberValidator implements WPAM_Validation_IValidator
{
	function getError() {
		return __( 'must be a phone number', 'affiliates-manager' );
	}

	function isValid($value) {
		//return (preg_match('/^([0-9-\\(\\)\\s]+)$/', $value) > 0);
		return (preg_match("/^([0-9()\s.+-]{10,20})$/", $value) > 0);
	}
}
