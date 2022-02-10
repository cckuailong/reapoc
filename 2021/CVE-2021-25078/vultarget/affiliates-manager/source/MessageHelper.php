<?php
/**
 * @author John Hargrove
 * 
 * Date: 12/11/10
 * Time: 10:04 PM
 */

require_once WPAM_BASE_DIRECTORY . "/source/Data/DataAccess.php";

class WPAM_MessageHelper
{
	/**
	 * @var WPAM_Data_DataAccess
	 */
	private static $db;
	
	public static function __StaticConstructor()
	{
		if (self::$db === NULL)
			self::$db = new WPAM_Data_DataAccess();
	}

	public static function GetMessage($name)
	{
		$msgRepo = self::$db->getMessageRepository();
		$msg = $msgRepo->loadBy(array('name' => $name));
		if ($msg === NULL)
			throw new Exception( sprintf( __( "Attempt to load invalid message of name '%s'", 'affiliates-manager' ), $name ) );
		return $msg->content;
	}
}

WPAM_MessageHelper::__StaticConstructor();
