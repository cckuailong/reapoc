<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

if (!class_exists('UpdraftPlus_BackupModule')) require_once(UPDRAFTPLUS_DIR.'/methods/backup-module.php');

class UpdraftPlus_BackupModule_AddonNotYetPresent extends UpdraftPlus_BackupModule {

	private $method;

	private $description;

	public function __construct($method, $description, $required_php = false, $image = null) {
		$this->method = $method;
		$this->description = $description;
		$this->required_php = $required_php;
		$this->image = $image;
		$this->error_msg = 'This remote storage method ('.$this->description.') requires PHP '.$this->required_php.' or later';
		$this->error_msg_trans = sprintf(__('This remote storage method (%s) requires PHP %s or later.', 'updraftplus'), $this->description, $this->required_php);
	}

	public function backup($backup_array) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		$this->log("You do not have the UpdraftPlus ".$this->method.' add-on installed - get it from '.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").'');
		
		$this->log(sprintf(__('You do not have the UpdraftPlus %s add-on installed - get it from %s', 'updraftplus'), $this->description, ''.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").''), 'error', 'missingaddon-'.$this->method);
		
		return false;

	}

	/**
	 * Retrieve a list of supported features for this storage method
	 *
	 * Currently known features:
	 *
	 * - multi_options : indicates that the remote storage module
	 * can handle its options being in the Feb-2017 multi-options
	 * format. N.B. This only indicates options handling, not any
	 * other multi-destination options.
	 *
	 * - multi_servers : not implemented yet: indicates that the
	 * remote storage module can handle multiple servers at backup
	 * time. This should not be specified without multi_options.
	 * multi_options without multi_servers is fine - it will just
	 * cause only the first entry in the options array to be used.
	 *
	 * - config_templates : not implemented yet: indicates that
	 * the remote storage module can output its configuration in
	 * Handlebars format via the get_configuration_template() method.
	 *
	 * - conditional_logic : indicates that the remote storage module
	 * can handle predefined logics regarding how backups should be
	 * sent to the remote storage
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// The 'multi_options' options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates');
	}

	public function delete($files, $method_obj = false, $sizeinfo = array()) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		$this->log('You do not have the UpdraftPlus '.$this->method.' add-on installed - get it from '.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").'');
		
		$this->log(sprintf(__('You do not have the UpdraftPlus %s add-on installed - get it from %s', 'updraftplus'), $this->description, ''.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/").''), 'error', 'missingaddon-'.$this->method);

		return false;

	}

	public function listfiles($match = 'backup_') {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return new WP_Error('no_addon', sprintf(__('You do not have the UpdraftPlus %s add-on installed - get it from %s', 'updraftplus'), $this->description, ''.apply_filters("updraftplus_com_link", "https://updraftplus.com/shop/")));
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		global $updraftplus;
		
		$link = sprintf(__('%s support is available as an add-on', 'updraftplus'), $this->description).' - <a href="'.$updraftplus->get_url('premium').'" target="_blank">'.__('follow this link to get it', 'updraftplus');

		$default = '
		<tr class="updraftplusmethod '.$this->method.'">
			<th>'.$this->description.':</th>
			<td>'.((!empty($this->image)) ? '<p><img src="'.UPDRAFTPLUS_URL.'/images/'.$this->image.'"></p>' : '').$link.'</a></td>
			</tr>';

		if (version_compare(phpversion(), $this->required_php, '<')) {
			$default .= '<tr class="updraftplusmethod '.$this->method.'">
			<th></th>
			<td>
				<em>
					'.htmlspecialchars($this->error_msg_trans).'
					'.htmlspecialchars(__('You will need to ask your web hosting company to upgrade.', 'updraftplus')).'
					'.sprintf(__('Your %s version: %s.', 'updraftplus'), 'PHP', phpversion()).'
				</em>
			</td>
			</tr>';
		}
		return $default;
	}
}
