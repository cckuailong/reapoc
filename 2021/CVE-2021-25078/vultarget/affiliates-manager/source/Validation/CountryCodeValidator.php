<?php
/**
 * @author John Hargrove
 * 
 * Date: May 31, 2010
 * Time: 4:26:00 PM
 */

require_once WPAM_BASE_DIRECTORY . "/source/Validation/CountryCodes.php";

class WPAM_Validation_CountryCodeValidator implements WPAM_Validation_IValidator
{
	public function getError()
	{
		return __( 'is required', 'affiliates-manager' );
	}

	public function isValid($value)
	{
		return array_key_exists($value, WPAM_Validation_CountryCodes::get_countries());
	}
}
