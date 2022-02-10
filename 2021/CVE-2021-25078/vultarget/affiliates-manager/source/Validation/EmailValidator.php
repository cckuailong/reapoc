<?php
/**
 * @author John Hargrove
 * 
 * Date: Jun 13, 2010
 * Time: 3:03:03 PM
 */

class WPAM_Validation_EmailValidator implements WPAM_Validation_IValidator
{
	public function __construct() { }

	function getError() {
		return __( 'must be a valid e-mail address', 'affiliates-manager' );
	}

	function isValid($value) {
		$is_email = is_email( $value );
		$return = $is_email ? true : false;
		if ( WPAM_DEBUG )
			echo "<!-- is_email {$value} : {$return} -->\n";
		return $return;
	}
}
