<?php
/**
 * @author John Hargrove
 * 
 * Date: May 30, 2010
 * Time: 10:17:37 PM
 */

interface WPAM_Validation_IValidator
{
	function getError();
	function isValid($value);
}
