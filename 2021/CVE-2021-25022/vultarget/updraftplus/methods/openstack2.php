<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

// SDK uses namespacing - requires PHP 5.3 (actually the SDK states its requirements as 5.3.3)
// @codingStandardsIgnoreLine
use OpenCloud\OpenStack;

require_once(UPDRAFTPLUS_DIR.'/methods/openstack-base.php');

class UpdraftPlus_BackupModule_openstack extends UpdraftPlus_BackupModule_openstack_base {

	public function __construct() {
		// 4th parameter is a relative (to UPDRAFTPLUS_DIR) logo URL, which should begin with /, should we get approved for use of the OpenStack logo in future (have requested info)
		parent::__construct('openstack', 'OpenStack', 'OpenStack (Swift)', '');
	}

	/**
	 * Get Openstack service
	 *
	 * @param  String  $opts             THis contains: 'tenant', 'user', 'password', 'authurl', (optional) 'region'
	 * @param  Boolean $useservercerts   User server certificates
	 * @param  String  $disablesslverify Check to disable SSL Verify
	 * @return Array
	 */
	public function get_openstack_service($opts, $useservercerts = false, $disablesslverify = null) {

		// 'tenant', 'user', 'password', 'authurl', 'path', (optional) 'region'
		extract($opts);

		if (null === $disablesslverify) $disablesslverify = UpdraftPlus_Options::get_updraft_option('updraft_ssl_disableverify');

		if (empty($user) || empty($password) || empty($authurl)) throw new Exception(__('Authorisation failed (check your credentials)', 'updraftplus'));// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $user, $password and $authurl being extracted in extract() line 29

		include_once(UPDRAFTPLUS_DIR.'/vendor/autoload.php');
		global $updraftplus;
		$updraftplus->log("OpenStack authentication URL: ".$authurl);// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $authurl being extracted in extract() line 29

		$client = new OpenStack($authurl, array(// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $authurl being extracted in extract() line 29
			'username' => $user,// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $user being extracted in extract() line 29
			'password' => $password,// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $password being extracted in extract() line 29
			'tenantName' => $tenant// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable -- $tenant being extracted in extract() line 29
		));
		$this->client = $client;

		if ($disablesslverify) {
			$client->setSslVerification(false);
		} else {
			if ($useservercerts) {
				$client->setConfig(array($client::SSL_CERT_AUTHORITY => false));
			} else {
				$client->setSslVerification(UPDRAFTPLUS_DIR.'/includes/cacert.pem', true, 2);
			}
		}

		$client->authenticate();

		if (empty($region)) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
			$catalog = $client->getCatalog();
			if (!empty($catalog)) {
				$items = $catalog->getItems();
				if (is_array($items)) {
					foreach ($items as $item) {
						$name = $item->getName();
						$type = $item->getType();
						if ('swift' != $name || 'object-store' != $type) continue;
						$eps = $item->getEndpoints();
						if (!is_array($eps)) continue;
						foreach ($eps as $ep) {
							if (is_object($ep) && !empty($ep->region)) {
								$region = $ep->region;
							}
						}
					}
				}
			}
		}

		$this->region = $region;

		return $client->objectStoreService('swift', $region);

	}

	/**
	 * This method overrides the parent method and lists the supported features of this remote storage option.
	 *
	 * @return Array - an array of supported features (any features not
	 * mentioned are assumed to not be supported)
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
			'user' => '',
			'authurl' => '',
			'password' => '',
			'tenant' => '',
			'path' => '',
			'region' => ''
		);
	}

	/**
	 * Get the pre middlesection configuration template
	 *
	 * @return String - the template
	 */
	public function get_pre_configuration_middlesection_template() {
		
		?>
		<p><?php _e('Get your access credentials from your OpenStack Swift provider, and then pick a container name to use for storage. This container will be created for you if it does not already exist.', 'updraftplus');?> <a href="<?php echo apply_filters("updraftplus_com_link", "https://updraftplus.com/faqs/there-appear-to-be-lots-of-extra-files-in-my-rackspace-cloud-files-container/");?>" target="_blank"><?php _e('Also, you should read this important FAQ.', 'updraftplus'); ?></a></p>

		<?php
	}
	
	/**
	 * This outputs the html to the settings page for the Openstack settings.
	 *
	 * @return String - the partial template, ready for substitutions to be carried out
	 */
	public function get_configuration_middlesection_template() {
		ob_start();
		$classes = $this->get_css_classes();
		?>

		<tr class="<?php echo $classes; ?>">
			<th><?php echo ucfirst(__('authentication URI', 'updraftplus'));?>:</th>
			<td><input title="<?php echo _x('This needs to be a v2 (Keystone) authentication URI; v1 (Swauth) is not supported.', 'Keystone and swauth are technical terms which cannot be translated', 'updraftplus');?>" data-updraft_settings_test="authurl" type="text" autocomplete="off" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('authurl');?> value="{{authurl}}" />
			<br>
			<em><?php echo _x('This needs to be a v2 (Keystone) authentication URI; v1 (Swauth) is not supported.', 'Keystone and swauth are technical terms which cannot be translated', 'updraftplus');?></em>
			</td>
		</tr>

		<tr class="<?php echo $classes; ?>">
			<th><a href="http://docs.openstack.org/openstack-ops/content/projects_users.html" title="<?php _e('Follow this link for more information', 'updraftplus');?>" target="_blank"><?php _e('Tenant', 'updraftplus');?></a>:</th>
			<td><input data-updraft_settings_test="tenant" type="text" autocomplete="off" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('tenant');?> value="{{tenant}}" />
			</td>
		</tr>

		<tr class="<?php echo $classes; ?>">
			<th><?php _e('Region', 'updraftplus');?>:</th>
			<td><input title="<?php _e('Leave this blank, and a default will be chosen.', 'updraftplus');?>" data-updraft_settings_test="region" type="text" autocomplete="off" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('region');?> value="{{region}}" />
			<br>
			<em><?php _e('Leave this blank, and a default will be chosen.', 'updraftplus');?></em>
			</td>
		</tr>

		<tr class="<?php echo $classes; ?>">
			<th><?php _e('Username', 'updraftplus');?>:</th>
			<td><input data-updraft_settings_test="user" type="text" autocomplete="off" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('user');?> value="{{user}}" />
			</td>
		</tr>

		<tr class="<?php echo $classes; ?>">
			<th><?php _e('Password', 'updraftplus');?>:</th>
			<td><input data-updraft_settings_test="password" type="<?php echo apply_filters('updraftplus_admin_secret_field_type', 'password'); ?>" autocomplete="off" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('password');?> value="{{password}}" />
			</td>
		</tr>

		<tr class="<?php echo $classes; ?>">
			<th><?php echo __('Container', 'updraftplus');?>:</th>
			<td><input data-updraft_settings_test="path" type="text" class="updraft_input--wide" <?php $this->output_settings_field_name_and_id('path');?> value="{{path}}" /></td>
		</tr>
		<?php
		return ob_get_clean();
	}

	public function credentials_test($posted_settings) {

		if (empty($posted_settings['user'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('username', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['password'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('password', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['tenant'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), _x('tenant', '"tenant" is a term used with OpenStack storage - Google for "OpenStack tenant" to get more help on its meaning', 'updraftplus'));
			return;
		}

		if (empty($posted_settings['authurl'])) {
			printf(__("Failure: No %s was given.", 'updraftplus'), __('authentication URI', 'updraftplus'));
			return;
		}

		$opts = array(
			'user' => $posted_settings['user'],
			'password' => $posted_settings['password'],
			'authurl' => $posted_settings['authurl'],
			'tenant' => $posted_settings['tenant'],
			'region' => empty($posted_settings['region']) ? '' : $posted_settings['region'],
		);

		$this->credentials_test_go($opts, $posted_settings['path'], $posted_settings['useservercerts'], $posted_settings['disableverify']);
	}

	/**
	 * Check whether options have been set up by the user, or not
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) {
		if (is_array($opts) && $opts['user'] && '' !== $opts['user'] && !empty($opts['authurl'])) return true;
		return false;
	}
}
