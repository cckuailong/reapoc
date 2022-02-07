<?php

/**
 * OAuth storage handler using WordPress options
 * This can only be used if you have a WordPress environment loaded, such that the (get|update|delete)_option functions are available
 * See an example usage in http://wordpress.org/extend/plugins/updraftplus
 * @author David Anderson <david@updraftplus.com>
 * @link https://updraftplus.com
 * @package Dropbox\Oauth
 * @subpackage Storage
 */

class Dropbox_WordPress implements Dropbox_StorageInterface
{
	/**
	 * Option name
	 * @var string
	 */
	protected $option_name_prefix = 'dropbox_token';
	
	/**
	 * Option name (array storage)
	 * @var string
	 */
	protected $option_array = '';
	
	/**
	 * Encyption object
	 * @var Encrypter|null
	 */
	protected $encrypter = null;

	/**
	 * Backup module object
	 * @var Backup_module_object|null
	 */
	protected $backup_module_object = null;
	
	/**
	 * Check if an instance of the encrypter is passed, set the encryption object
	 * @return void
	 */
	public function __construct(Dropbox_Encrypter $encrypter = null, $option_name_prefix = 'dropbox_token', $option_array = 'dropbox', $backup_module_object = null)
	{
		if ($encrypter instanceof Dropbox_Encrypter) {
			$this->encrypter = $encrypter;
		}

		if ($backup_module_object instanceof UpdraftPlus_BackupModule) {
			$this->backup_module_object = $backup_module_object;
		}

		$this->option_name_prefix = $option_name_prefix;
		$this->option_array = $option_array;

	}
	
	/**
	 * Get an entry from the Dropbox options in the database
	 * If the encryption object is set then decrypt the token before returning
	 * @param string $type is the key to retrieve
	 * @return array|bool
	 */
	public function get($type)
	{
		if ($type != 'request_token' && $type != 'access_token' && $type != 'appkey' && $type != 'CSRF' && $type != 'code') {
			throw new Dropbox_Exception("Expected a type of either 'request_token', 'access_token', 'CSRF' or 'code', got '$type'");
		} else {
			if (false !== ($opts = $this->backup_module_object->get_options())) {
				if ($type == 'request_token' || $type == 'access_token'){
					if (!empty($opts[$this->option_name_prefix.$type])) {
						$gettoken = $opts[$this->option_name_prefix.$type];
						$token = $this->decrypt($gettoken);
						return $token;
					}
				} else {
					if (!empty($opts[$type])) {
						return $opts[$type];
					}
				}
			}
			return false;
		}
	}
	
	/**
	 * Set a value in the database by type
	 * If the value is a token and the encryption object is set then encrypt the token before storing
	 * @param \stdClass Token object to set
	 * @param string $type Token type
	 * @return void
	 */
	public function set($token, $type)
	{
		if ($type != 'request_token' && $type != 'access_token' && $type != 'upgraded' && $type != 'CSRF' && $type != 'code') {
			throw new Dropbox_Exception("Expected a type of either 'request_token', 'access_token', 'CSRF', 'upgraded' or 'code', got '$type'");
		} else {
			
			$opts = $this->backup_module_object->get_options();
			
			if ($type == 'access_token'){
				$token = $this->encrypt($token);
				$opts[$this->option_name_prefix.$type] = $token;
			} else if ($type == 'request_token' ) {
				$opts[$this->option_name_prefix.$type] = $token;
			} else {
				$opts[$type] = $token;
			}
			
			$this->backup_module_object->set_options($opts, true);
		}
	}
	
	/**
	 * Remove a value in the database by type rather than setting to null / empty
	 * set the value to null here so that when it gets to the options filter it will
	 * unset the value there, this avoids a bug where if the value is not set then 
	 * the option filter will take the value from the database and save that version back.
	 * 
	 * N.B. Before PHP 7.0, you can't call a method name unset()
	 * 
	 * @param string $type Token type
	 * @return void
	 */
	public function do_unset($type)
	{
		if ($type != 'request_token' && $type != 'access_token' && $type != 'upgraded' && $type != 'CSRF' && $type != 'code') {
			throw new Dropbox_Exception("Expected a type of either 'request_token', 'access_token', 'CSRF', 'upgraded' or 'code', got '$type'");
		} else {
			
			$opts = $this->backup_module_object->get_options();
			
			if ($type == 'access_token' || $type == 'request_token'){
				$opts[$this->option_name_prefix.$type] = null;
			} else {
				$opts[$type] = null;
			}
			$this->backup_module_object->set_options($opts, true);
		}
	}
	
	/**
	 * Delete the request and access tokens currently stored in the database
	 * @return bool
	 */
	public function delete()
	{
		$opts = $this->backup_module_object->get_options();
		$opts[$this->option_name_prefix.'request_token'] = null;
		$opts[$this->option_name_prefix.'access_token'] = null;
		unset($opts['ownername']);
		unset($opts['upgraded']);
		$this->backup_module_object->set_options($opts, true);
		return true;
	}
	
	/**
	 * Use the Encrypter to encrypt a token and return it
	 * If there is not encrypter object, return just the 
	 * serialized token object for storage
	 * @param stdClass $token OAuth token to encrypt
	 * @return stdClass|string
	 */
	protected function encrypt($token)
	{
		// Serialize the token object
		$token = serialize($token);
		
		// Encrypt the token if there is an Encrypter instance
		if ($this->encrypter instanceof Dropbox_Encrypter) {
			$token = $this->encrypter->encrypt($token);
		}
		
		// Return the token
		return $token;
	}
	
	/**
	 * Decrypt a token using the Encrypter object and return it
	 * If there is no Encrypter object, assume the token was stored
	 * serialized and return the unserialized token object
	 * @param stdClass $token OAuth token to encrypt
	 * @return stdClass|string
	 */
	protected function decrypt($token)
	{
		// Decrypt the token if there is an Encrypter instance
		if ($this->encrypter instanceof Dropbox_Encrypter) {
			$token = $this->encrypter->decrypt($token);
		}
		
		// Return the unserialized token
		return @unserialize($token);
	}
}
