<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

if (!class_exists('UpdraftPlus_BackupModule')) require_once(UPDRAFTPLUS_DIR.'/methods/backup-module.php');

class UpdraftPlus_BackupModule_insufficientphp extends UpdraftPlus_BackupModule {

	private $required_php;

	private $error_msg;

	private $method;

	public function __construct($method, $desc, $php, $image = null) {
		$this->method = $method;
		$this->desc = $desc;
		$this->required_php = $php;
		$this->image = $image;
		$this->error_msg = 'This remote storage method ('.$this->desc.') requires PHP '.$this->required_php.' or later';
		$this->error_msg_trans = sprintf(__('This remote storage method (%s) requires PHP %s or later.', 'updraftplus'), $this->desc, $this->required_php);
	}

	private function log_error() {
		global $updraftplus;
		$updraftplus->log($this->error_msg);
		$updraftplus->log($this->error_msg_trans, 'error', 'insufficientphp');
		return false;
	}

	/**
	 * backup method: takes an array, and shovels them off to the cloud storage
	 *
	 * @param  array $backup_array An array backups
	 * @return Array
	 */
	public function backup($backup_array) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->log_error();
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
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
	 */
	public function get_supported_features() {
		// The 'multi_options' options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates');
	}

	/**
	 * $match: a substring to require (tested via strpos() !== false)
	 *
	 * @param  String $match THis will specify which match is used for the SQL but by default it is set to 'backup_' unless specified
	 * @return Array
	 */
	public function listfiles($match = 'backup_') {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return new WP_Error('insufficient_php', $this->error_msg_trans);
	}

	/**
	 * delete method: takes an array of file names (base name) or a single string, and removes them from the cloud storage
	 *
	 * @param  String  $files 	 List of files
	 * @param  Boolean $data 	 Specifies data or not
	 * @param  array   $sizeinfo This is the size info on the file.
	 * @return Array
	 */
	public function delete($files, $data = false, $sizeinfo = array()) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->log_error();
	}

	/**
	 * download method: takes a file name (base name), and brings it back from the cloud storage into Updraft's directory
	 * You can register errors with $updraftplus->log("my error message", 'error')
	 *
	 * @param  String $file List of files
	 * @return Array
	 */
	public function download($file) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return $this->log_error();
	}

	private function extra_config() {
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		ob_start();
		$this->extra_config();
		?>
		<tr class="updraftplusmethod <?php echo $this->method;?>">
			<th><?php echo htmlspecialchars($this->desc);?>:</th>
			<td>
				<em>
					<?php echo (!empty($this->image)) ? '<p><img src="'.UPDRAFTPLUS_URL.'/images/'.$this->image.'"></p>' : ''; ?>
					<?php echo htmlspecialchars($this->error_msg_trans);?>
					<?php echo htmlspecialchars(__('You will need to ask your web hosting company to upgrade.', 'updraftplus'));?>
					<?php echo sprintf(__('Your %s version: %s.', 'updraftplus'), 'PHP', phpversion());?>
				</em>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}
}
