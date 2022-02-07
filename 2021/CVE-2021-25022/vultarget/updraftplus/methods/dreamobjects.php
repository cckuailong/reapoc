<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

require_once(UPDRAFTPLUS_DIR.'/methods/s3.php');

/**
 * Converted to multi-options (Feb 2017-) and previous options conversion removed: Yes
 */
class UpdraftPlus_BackupModule_dreamobjects extends UpdraftPlus_BackupModule_s3 {

	// This gets populated in the constructor
	private $dreamobjects_endpoints = array();
	
	protected $provider_can_use_aws_sdk = false;
	
	protected $provider_has_regions = true;

	/**
	 * Class constructor
	 */
	public function __construct() {
		// When new endpoint introduced in future, Please add it here and also add it as hard coded option for endpoint dropdown in self::get_partial_configuration_template_for_endpoint()
		// Put the default first
		$this->dreamobjects_endpoints = array(
			// Endpoint, then the label
			'objects-us-east-1.dream.io' => 'objects-us-east-1.dream.io',
			'objects-us-west-1.dream.io' => 'objects-us-west-1.dream.io ('.__('Closing 1st October 2018', 'updraftplus').')',
		);
	}
	
	protected $use_v4 = false;

	/**
	 * Given an S3 object, possibly set the region on it
	 *
	 * @param Object $obj		  - like UpdraftPlus_S3
	 * @param String $region	  - or empty to fetch one from saved configuration
	 * @param String $bucket_name
	 */
	protected function set_region($obj, $region = '', $bucket_name = '') {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $bucket_name

		$config = $this->get_config();
		$endpoint = ('' != $region && 'n/a' != $region) ? $region : $config['endpoint'];
		global $updraftplus;
		if ($updraftplus->backup_time) {
			$updraftplus->log("Set endpoint: $endpoint");
		
			// Warning for objects-us-west-1 shutdown in Oct 2018
			if ('objects-us-west-1.dream.io' == $endpoint) {
				$updraftplus->log("The objects-us-west-1.dream.io endpoint shut down on the 1st October 2018. The upload is expected to fail. Please see the following article for more information https://help.dreamhost.com/hc/en-us/articles/360002135871-Cluster-migration-procedure", 'warning', 'dreamobjects_west_shutdown');
			}
		}
		
		$obj->setEndpoint($endpoint);
	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not mentioned are asuumed to not be supported)
	 */
	public function get_supported_features() {
		// This options format is handled via only accessing options via $this->get_options()
		return array('multi_options', 'config_templates', 'multi_storage', 'conditional_logic');
	}

	/**
	 * Retrieve default options for this remote storage module.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array(
			'accesskey' => '',
			'secretkey' => '',
			'path' => '',
		);
	}

	/**
	 * Retrieve specific options for this remote storage module
	 *
	 * @param Boolean $force_refresh - if set, and if relevant, don't use cached credentials, but get them afresh
	 *
	 * @return Array - an array of options
	 */
	protected function get_config($force_refresh = false) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- $force_refresh unused
		$opts = $this->get_options();
		$opts['whoweare'] = 'DreamObjects';
		$opts['whoweare_long'] = 'DreamObjects';
		$opts['key'] = 'dreamobjects';
		if (empty($opts['endpoint'])) {
			$endpoints = array_keys($this->dreamobjects_endpoints);
			$opts['endpoint'] = $endpoints[0];
		}
		return $opts;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
		$this->get_pre_configuration_template_engine('dreamobjects', 'DreamObjects', 'DreamObjects', 'DreamObjects', 'https://panel.dreamhost.com/index.cgi?tree=storage.dreamhostobjects', '<a href="https://dreamhost.com/cloud/dreamobjects/" target="_blank"><img alt="DreamObjects" src="'.UPDRAFTPLUS_URL.'/images/dreamobjects_logo-horiz-2013.png"></a>');
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		return $this->get_configuration_template_engine('dreamobjects', 'DreamObjects', 'DreamObjects', 'DreamObjects', 'https://panel.dreamhost.com/index.cgi?tree=storage.dreamhostobjects', '<a href="https://dreamhost.com/cloud/dreamobjects/" target="_blank"><img alt="DreamObjects" src="'.UPDRAFTPLUS_URL.'/images/dreamobjects_logo-horiz-2013.png"></a>');
	}
	
	/**
	 * Get handlebar partial template string for endpoint of s3 compatible remote storage method. Other child class can extend it.
	 *
	 * @return String the partial template string
	 */
	protected function get_partial_configuration_template_for_endpoint() {
		// When new endpoint introduced in future, Please add it  as hard coded option for below  endpoint dropdown and also add as array value in private $dreamobjects_endpoints variable
		return '<tr class="'.$this->get_css_classes().'">
					<th>'.sprintf(__('%s end-point', 'updraftplus'), 'DreamObjects').'</th>
					<td>
						<select data-updraft_settings_test="endpoint" '.$this->output_settings_field_name_and_id('endpoint', true).' style="width: 360px">							
							{{#each dreamobjects_endpoints as |description endpoint|}}
								<option value="{{endpoint}}" {{#ifeq ../endpoint endpoint}}selected="selected"{{/ifeq}}>{{description}}</option>
							{{/each}}
						</select>
					</td>
				</tr>';
	}
	
	/**
	 * Modifies handerbar template options
	 *
	 * @param array $opts
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		$opts['endpoint'] = empty($opts['endpoint']) ? '' : $opts['endpoint'];
		$opts['dreamobjects_endpoints'] = $this->dreamobjects_endpoints;
		return $opts;
	}
}
