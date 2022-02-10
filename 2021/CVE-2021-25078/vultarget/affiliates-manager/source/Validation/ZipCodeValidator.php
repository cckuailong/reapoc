<?php
/**
 * @author John Hargrove
 * 
 * Date: Sep 19, 2010
 * Time: 11:42:05 PM
 */



class WPAM_Validation_ZipCodeValidator implements WPAM_Validation_IValidator
{

	function getError()
	{
		return __( 'must be a valid zip code', 'affiliates-manager' );
	}

	function isValid($value)
	{
            if(!empty($value)){
                return true;
            }
            return false;
	}
}

