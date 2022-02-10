<?php
/**
 * @author John Hargrove
 * 
 * Date: Jun 6, 2010
 * Time: 9:32:19 PM
 */

class WPAM_Security_Crypt
{
	private $passphrase;
	public function __construct($passphrase)
	{
		$this->passphrase = $passphrase;
	}

	public function decrypt($encryptedData)
	{
	}

	public function encrypt($data)
	{

	}
}
