<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No access.');

/**
 * Handles UpdraftVault Commands to pull Amazon S3 Bucket credentials
 * from user's UpdraftVault and some default filters for per page display
 *
 * @method array get_credentials()
 */
class UpdraftCentral_UpdraftVault_Commands extends UpdraftCentral_Commands {
	
   /**
	* Gets the Amazon S3 Credentials
	*
	* Extract the needed credentials to connect to the user's Amazon S3 Bucket
	* by pulling this info from the UpdraftVault server.
	*
	* @return array $result - An array containing the Amazon S3 settings/config if successful,
	*						  otherwise, it will contain the error details/info of the generated error.
	*/
	public function get_credentials() {
		$storage_objects_and_ids = UpdraftPlus_Storage_Methods_Interface::get_storage_objects_and_ids(array('updraftvault'));

		// UpdraftVault isn't expected to have multiple options currently, so we just grab the first instance_id in the settings and use the options from that. If in future we do decide we want to make UpdraftVault multiple options then we will need to update this part of the code e.g a instance_id needs to be passed in and used by the following lines of code.
		if (isset($storage_objects_and_ids['updraftvault']['instance_settings'])) {
			$instance_id = key($storage_objects_and_ids['updraftvault']['instance_settings']);
			$opts = $storage_objects_and_ids['updraftvault']['instance_settings'][$instance_id];
			$vault = $storage_objects_and_ids['updraftvault']['object'];
			$vault->set_options($opts, false, $instance_id);
		} else {
			if (!class_exists('UpdraftPlus_BackupModule_updraftvault')) include_once(UPDRAFTPLUS_DIR.'/methods/updraftvault.php');
			$vault = new UpdraftPlus_BackupModule_updraftvault();
		}

		$result = $vault->get_config();
		
		if (isset($result['error']) && !empty($result['error'])) {
			$result = array('error' => true, 'message' => $result['error']['message'], 'values' => $result['error']['values']);
		}
		
		return $this->_response($result);
	}
}
