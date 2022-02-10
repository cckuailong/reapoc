<?php
/**
 * @author John Hargrove
 * 
 * Date: May 31, 2010
 * Time: 4:35:35 PM
 */

require_once WPAM_BASE_DIRECTORY . "/source/Validation/StateCodes.php";

class WPAM_Validation_StateCodeValidator implements WPAM_Validation_IValidator
{
	public function getError() {
		return __( 'must be selected', 'affiliates-manager' );
	}

	function isValid($value) {
		return array_key_exists($value, WPAM_Validation_StateCodes::$stateCodes);
	}
}
