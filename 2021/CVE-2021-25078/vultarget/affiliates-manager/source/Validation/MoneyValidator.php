<?php
/**
 * @author John Hargrove
 * 
 * Date: Jul 5, 2010
 * Time: 8:49:45 PM
 */

class WPAM_Validation_MoneyValidator implements WPAM_Validation_IValidator
{
	function getError() {
		return __( 'must be a valid monetary value', 'affiliates-manager' );
	}

	function isValid($value) {
		return (preg_match('/^\\$?[0-9]+(,[0-9]{3})*(\\.[0-9]{2})?$/', $value) > 0);
	}
}
