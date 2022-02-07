<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

require_once(UPDRAFTPLUS_DIR.'/methods/s3.php');

/**
 * Converted to multi-options (Feb 2017-) and previous options conversion removed: Yes
 */
class UpdraftPlus_BackupModule_s3generic extends UpdraftPlus_BackupModule_s3 {

	protected $use_v4 = false;
	
	protected $provider_can_use_aws_sdk = false;
	
	protected $provider_has_regions = false;

	/**
	 * Given an S3 object, possibly set the region on it
	 *
	 * @param Object $obj		  - like UpdraftPlus_S3
	 * @param String $region
	 * @param String $bucket_name
	 */
	protected function set_region($obj, $region = '', $bucket_name = '') {
		$config = $this->get_config();
		$endpoint = ('' != $region && 'n/a' != $region) ? $region : $config['endpoint'];
		$log_message = "Set endpoint: $endpoint";
		$log_message_append = '';
		if (is_string($endpoint) && preg_match('/^(.*):(\d+)$/', $endpoint, $matches)) {
			$endpoint = $matches[1];
			$port = $matches[2];
			$log_message_append = ", port=$port";
			$obj->setPort($port);
		}
		// This provider requires domain-style access. In future it might be better to provide an option rather than hard-coding the knowledge.
		if (is_string($endpoint) && preg_match('/\.aliyuncs\.com$/i', $endpoint)) {
			$obj->useDNSBucketName(true, $bucket_name);
		}
		global $updraftplus;
		if ($updraftplus->backup_time) $this->log($log_message.$log_message_append);
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
			'endpoint' => '',
		);
	}

	/**
	 * Retrieve specific options for this remote storage module
	 *
	 * @param Boolean $force_refresh - if set, and if relevant, don't use cached credentials, but get them afresh
	 *
	 * @return Array - an array of options
	 */
	protected function get_config($force_refresh = false) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Filter use
		$opts = $this->get_options();
		$opts['whoweare'] = 'S3';
		$opts['whoweare_long'] = __('S3 (Compatible)', 'updraftplus');
		$opts['key'] = 's3generic';
		return $opts;
	}

	/**
	 * Get the pre configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_template() {
	
		$opening_html = '<p>'.__('Examples of S3-compatible storage providers:', 'updraftplus').' <a href="https://updraftplus.com/use-updraftplus-digital-ocean-spaces/" target="_blank">DigitalOcean Spaces</a>, <a href="https://www.linode.com/products/object-storage/" target="_blank">Linode Object Storage</a>, <a href="https://www.cloudian.com" target="_blank">Cloudian</a>, <a href="https://www.mh.connectria.com/rp/order/cloud_storage_index" target="_blank">Connectria</a>, <a href="https://www.constant.com/cloud/storage/" target="_blank">Constant</a>, <a href="https://www.eucalyptus.cloud/" target="_blank">Eucalyptus</a>, <a href="http://cloud.nifty.com/storage/" target="_blank">Nifty</a>, <a href="http://www.ntt.com/business/services/cloud/iaas/cloudn.html" target="_blank">Cloudn</a>'.__('... and many more!', 'updraftplus').'</p>';
		
		$this->get_pre_configuration_template_engine('s3generic', 'S3', __('S3 (Compatible)', 'updraftplus'), 'S3', '', $opening_html);
	}

	/**
	 * Get the configuration template
	 *
	 * @return String - the template, ready for substitutions to be carried out
	 */
	public function get_configuration_template() {
		// 5th parameter = control panel URL
		// 6th = image HTML
		return $this->get_configuration_template_engine('s3generic', 'S3', __('S3 (Compatible)', 'updraftplus'), 'S3', '', '');
	}
	
	/**
	 * Modifies handerbar template options
	 * The function require because It should override parent class's UpdraftPlus_BackupModule_s3::transform_options_for_template() functionality with no operation.
	 *
	 * @param array $opts
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		return $opts;
	}
	
	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		return (parent::options_exist($opts) && !empty($opts['endpoint']));
	}
	
	/**
	 * Get handlebar partial template string for endpoint of s3 compatible remote storage method. Other child class can extend it.
	 *
	 * @return String the partial template string
	 */
	protected function get_partial_configuration_template_for_endpoint() {
		return '<tr class="'.$this->get_css_classes().'">
					<th>'.sprintf(__('%s end-point', 'updraftplus'), 'S3').'</th>
					<td>
						<input data-updraft_settings_test="endpoint" type="text" class="updraft_input--wide" '.$this->output_settings_field_name_and_id('endpoint', true).' value="{{endpoint}}" />
					</td>
				</tr>
				<tr class="'.$this->get_css_classes().'">
					<th>'.__('Bucket access style', 'updraftplus').':<br><a aria-label="'.esc_attr__('Read more about bucket access style', 'updraftplus').'" href="https://updraftplus.com/faqs/what-is-the-different-between-path-style-and-bucket-style-access-to-an-s3-compatible-bucket/" target="_blank"><em>'.__('(Read more)', 'updraftplus').'</em></a></th>
					<td>
						<select data-updraft_settings_test="bucket_access_style" '.$this->output_settings_field_name_and_id('bucket_access_style', true).'>
							<option value="path_style" {{#ifeq "path_style" bucket_access_style}}selected="selected"{{/ifeq}}>'.__('Path style', 'updraftplus').'</option>
							<option value="virtual_host_style" {{#ifeq "virtual_host_style" bucket_access_style}}selected="selected"{{/ifeq}}>'.__('Virtual-host style', 'updraftplus').'</option>
						</select>
					</td>
				</tr>';
	}

	/**
	 * Use DNS bucket name if the remote storage is found to be using s3generic and its bucket access style is set to virtual-host
	 *
	 * @param Object $storage S3 Name
	 * @param Array  $config  an array of specific options for particular S3 remote storage module
	 * @return Boolean true if currently processing s3generic remote storage that uses virtual-host style, false otherwise
	 */
	protected function maybe_use_dns_bucket_name($storage, $config) {
		if ((!empty($config['endpoint']) && preg_match('/\.aliyuncs\.com$/i', $config['endpoint'])) || (!empty($config['bucket_access_style']) && 'virtual_host_style' === $config['bucket_access_style'])) {
			// due to the recent merge of S3-generic bucket access style on March 2021, if virtual-host bucket access style is selected, connecting to an amazonaws bucket location where the user doesn't have an access to it will throw an S3 InvalidRequest exception. It requires the signature to be set to version 4
			if (preg_match('/\.amazonaws\.com$/i', $config['endpoint'])) {
				$this->use_v4 = true;
				$storage->setSignatureVersion('v4');
			}
			return $this->use_dns_bucket_name($storage, '');
		} else {
			return false;
		}
	}
}
