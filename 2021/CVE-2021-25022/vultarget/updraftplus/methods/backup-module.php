<?php

if (!defined('UPDRAFTPLUS_DIR')) die('No direct access allowed.');

abstract class UpdraftPlus_BackupModule {

	private $_options;

	private $_instance_id;

	private $_storage;
	
	/**
	 * Store options (within this class) for this remote storage module. There is also a parameter for saving to the permanent storage (i.e. database).
	 *
	 * @param  array       $options     array of options to store
	 * @param  Boolean     $save        whether or not to also save the options to the database
	 * @param  null|String $instance_id optionally set the instance ID for this instance at the same time. This is required if you have not already set an instance ID with set_instance_id()
	 * @return void|Boolean If saving to DB, then the result of the DB save operation is returned.
	 */
	public function set_options($options, $save = false, $instance_id = null) {
	
		$this->_options = $options;
		
		// Remove any previously-stored storage object, because this is usually tied to the options
		if (!empty($this->_storage)) unset($this->_storage);

		if ($instance_id) $this->set_instance_id($instance_id);
		
		if ($save) return $this->save_options();

	}
	
	/**
	 * Saves the current options to the database. This is a private function; external callers should use set_options().
	 *
	 * @throws Exception if trying to save options without indicating an instance_id, or if the remote storage module does not have the multi-option capability
	 */
	private function save_options() {
	
		if (!$this->supports_feature('multi_options')) {
			throw new Exception('save_options() can only be called on a storage method which supports multi_options (this module, '.$this->get_id().', does not)');
		}
	
		if (!$this->_instance_id) {
			throw new Exception('save_options() requires an instance ID, but was called without setting one (either directly or via set_instance_id())');
		}
		
		$current_db_options = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format($this->get_id());

		if (is_wp_error($current_db_options)) {
			throw new Exception('save_options(): options fetch/update failed ('.$current_db_options->get_error_code().': '.$current_db_options->get_error_message().')');
		}

		$current_db_options['settings'][$this->_instance_id] = $this->_options;

		return UpdraftPlus_Options::update_updraft_option('updraft_'.$this->get_id(), $current_db_options);
	
	}
	
	/**
	 * Retrieve default options for this remote storage module.
	 * This method would normally be over-ridden by the child.
	 *
	 * @return Array - an array of options
	 */
	public function get_default_options() {
		return array();
	}

	/**
	 * Check whether options have been set up by the user, or not
	 * This method would normally be over-ridden by the child.
	 *
	 * @param Array $opts - the potential options
	 *
	 * @return Boolean
	 */
	public function options_exist($opts) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return false;
	}

	/**
	 * Retrieve a list of supported features for this storage method
	 * This method should be over-ridden by methods supporting new
	 * features.
	 *
	 * Keys are strings, and values are booleans.
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
		return array();
	}

	/**
	 * This method should only be called if the feature 'multi storage' is supported. In that case, it returns a template with information about the remote storage. The code below is a placeholder, and methods supporting the feature should always over-ride it.
	 *
	 * @return String - HTML template
	 */
	public function get_pre_configuration_template() {
		return $this->get_id().": called, but not implemented in the child class (coding error)";
	}

	/**
	 * This method should only be called if the feature 'config templates' is supported. In that case, it returns a template with appropriate placeholders for specific settings. The code below is a placeholder, and methods supporting the feature should always over-ride it.
	 *
	 * @return String - HTML template
	 */
	public function get_configuration_template() {
		return $this->get_id().": called, but not implemented in the child class (coding error)";
	}

	/**
	 * This method will set the stored storage object to that indicated
	 *
	 * @param Object $storage - the storage client
	 */
	public function set_storage($storage) {
		$this->_storage = $storage;
	}

	/**
	 * This method will return the stored storage client
	 *
	 * @return Object - the stored remote storage client
	 */
	public function get_storage() {
		if (!empty($this->_storage)) return $this->_storage;
	}
	
	/**
	 * Outputs id and name fields, as if currently within an input tag
	 *
	 * This assumes standardised options handling (i.e. that the options array is updraft_(method-id))
	 *
	 * @param Array|String $field                  - the field identifiers
	 * @param Boolean      $return_instead_of_echo - tells the method if it should return the output or echo it to page
	 */
	public function output_settings_field_name_and_id($field, $return_instead_of_echo = false) {
	
		$method_id = $this->get_id();
		
		$instance_id = $this->supports_feature('config_templates') ? '{{instance_id}}' : $this->_instance_id;
		
		$id = '';
		$name = '';

		if (is_array($field)) {
			foreach ($field as $value) {
				$id .= '_'.$value;
				$name .= '['.$value.']';
			}
		} else {
			$id = '_'.$field;
			$name = '['.$field.']';
		}
		
		$output = "id=\"updraft_${method_id}${id}_${instance_id}\" name=\"updraft_${method_id}[settings][${instance_id}]${name}\" ";

		if ($return_instead_of_echo) {
			return $output;
		} else {
			echo $output;
		}
	}
	
	/**
	 * Get the CSS ID
	 *
	 * @param String $field - the field identifier to return a CSS ID for
	 *
	 * @return String
	 */
	public function get_css_id($field) {
		$method_id = $this->get_id();
		$instance_id = $this->supports_feature('config_templates') ? '{{instance_id}}' : $this->_instance_id;
		return "updraft_${method_id}_${field}_${instance_id}";
	}
	
	/**
	 * Get handlebarsjs template
	 * This deals with any boiler-plate, prior to calling config_print()
	 *
	 * @uses self::config_print()
	 * @uses self::get_configuration_template()
	 *
	 * return handlebarsjs template or html
	 */
	public function get_template() {
		ob_start();
		// Allow methods to not use this hidden field, if they do not output any settings (to prevent their saved settings being over-written by just this hidden field)
		if ($this->print_shared_settings_fields()) {
			?><tr class="<?php echo $this->get_css_classes(); ?>"><input type="hidden" name="updraft_<?php echo $this->get_id();?>[version]" value="1"></tr><?php
		}
		
		if ($this->supports_feature('config_templates')) {
			?>
			{{#if first_instance}}
			<?php
				
				$this->get_pre_configuration_template();
				
				if ($this->supports_feature('multi_storage')) {
					do_action('updraftplus_config_print_add_multi_storage', $this->get_id(), $this);
				}
				
			?>
			{{/if}}
			<?php
			do_action('updraftplus_config_print_before_storage', $this->get_id(), $this);
			if ('updraftvault' !== $this->get_id()) do_action('updraftplus_config_print_add_conditional_logic', $this->get_id(), $this);
			if ($this->supports_feature('multi_storage')) {
				do_action('updraftplus_config_print_add_instance_label', $this->get_id(), $this);
			}

			$template = ob_get_clean();
			$template .= $this->get_configuration_template();
			if ('updraftvault' === $this->get_id()) {
				ob_start();
				do_action('updraftplus_config_print_add_conditional_logic', $this->get_id(), $this);
				$template .= ob_get_clean();
			}
		} else {
			do_action('updraftplus_config_print_before_storage', $this->get_id(), $this);
			do_action('updraftplus_config_print_add_conditional_logic', $this->get_id(), $this);
			// N.B. These are mutually exclusive: config_print() is not used if config_templates is supported. So, even during transition, the UpdraftPlus_BackupModule instance only needs to support one of the two, not both.
			$this->config_print();
			$template = ob_get_clean();
		}
		return $template;
	}
	
	/**
	 * Modifies handerbar template options. Other child class can extend it.
	 *
	 * @param array $opts
	 * @return Array - Modified handerbar template options
	 */
	public function transform_options_for_template($opts) {
		return $opts;
	}
	
	/**
	 * Gives settings keys which values should not passed to handlebarsjs context.
	 * The settings stored in UD in the database sometimes also include internal information that it would be best not to send to the front-end (so that it can't be stolen by a man-in-the-middle attacker)
	 *
	 * @return Array - Settings array keys which should be filtered
	 */
	public function filter_frontend_settings_keys() {
		return array();
	}

	/**
	 * Over-ride this to allow methods to not use the hidden version field, if they do not output any settings (to prevent their saved settings being over-written by just this hidden field
	 *
	 * @return [boolean] - return true to output the version field or false to not output the field
	 */
	public function print_shared_settings_fields() {
		return true;
	}

	/**
	 * Prints out the configuration section for a particular module. This is now (Sep 2017) considered deprecated; things are being ported over to get_configuration_template(), indicated via the feature 'config_templates'.
	 */
	public function config_print() {
		echo $this->get_id().": module neither declares config_templates support, nor has a config_print() method (coding bug)";
	}

	/**
	 * Supplies the list of keys for options to be saved in the backup job.
	 *
	 * @return Array
	 */
	public function get_credentials() {
		$keys = array('updraft_ssl_disableverify', 'updraft_ssl_nossl', 'updraft_ssl_useservercerts');
		if (!$this->supports_feature('multi_servers')) $keys[] = 'updraft_'.$this->get_id();
		return $keys;
	}
	
	/**
	 * Returns a space-separated list of CSS classes suitable for rows in the configuration section
	 *
	 * @param Boolean $include_instance - a boolean value to indicate if we want to include the instance_id in the css class, we may not want to include the instance if it's for a UI element that we don't want to be removed along with other UI elements that do include a instance id.
	 *
	 * @returns String - the list of CSS classes
	 */
	public function get_css_classes($include_instance = true) {
		$classes = 'updraftplusmethod '.$this->get_id();
		if (!$include_instance) return $classes;
		if ($this->supports_feature('multi_options')) {
			if ($this->supports_feature('config_templates')) {
				$classes .= ' '.$this->get_id().'-{{instance_id}}';
			} else {
				$classes .= ' '.$this->get_id().'-'.$this->_instance_id;
			}
		}
		return $classes;
	}
	
	/**
	 *
	 * Returns HTML for a row for a test button
	 *
	 * @param String $title - The text to be used in the button
	 *
	 * @returns String - The HTML to be inserted into the settings page
	 */
	protected function get_test_button_html($title) {
		ob_start();
		$instance_id = $this->supports_feature('config_templates') ? '{{instance_id}}' : $this->_instance_id;
		?>
		<tr class="<?php echo $this->get_css_classes(); ?>">
			<th></th>
			<td><p><button id="updraft-<?php echo $this->get_id();?>-test-<?php echo $instance_id;?>" type="button" class="button-primary updraft-test-button updraft-<?php echo $this->get_id();?>-test" data-instance_id="<?php echo $instance_id;?>" data-method="<?php echo $this->get_id();?>" data-method_label="<?php echo esc_attr($title);?>"><?php printf(__('Test %s Settings', 'updraftplus'), $title);?></button></p></td>
		</tr>
		<?php
		return ob_get_clean();
	}
	
	/**
	 * Get the backup method identifier for this class
	 *
	 * @return String - the identifier
	 */
	public function get_id() {
		$class = get_class($this);
		// UpdraftPlus_BackupModule_
		return substr($class, 25);
	}
	
	/**
	 * Get the backup method description for this class
	 *
	 * @return String - the identifier
	 */
	public function get_description() {
		global $updraftplus;

		$methods = $updraftplus->backup_methods;

		$id = $this->get_id();

		return isset($methods[$id]) ? $methods[$id] : $id;
	}

	/**
	 * Sets the instance ID - for supporting multi_options
	 *
	 * @param String $instance_id - the instance ID
	 */
	public function set_instance_id($instance_id) {
		$this->_instance_id = $instance_id;
	}
	
	/**
	 * Sets the instance ID - for supporting multi_options
	 *
	 * @returns String the instance ID
	 */
	public function get_instance_id() {
		return $this->_instance_id;
	}
	
	/**
	 * Check whether this storage module supports a mentioned feature
	 *
	 * @param String $feature - the feature concerned
	 *
	 * @returns Boolean
	 */
	public function supports_feature($feature) {
		return in_array($feature, $this->get_supported_features());
	}
	
	/**
	 * Retrieve options for this remote storage module.
	 * N.B. The option name instance_id is reserved and should not be used.
	 *
	 * @uses get_default_options
	 *
	 * @return Array - array of options. This will include default values for any options not set.
	 */
	public function get_options() {
	
		global $updraftplus;
	
		$supports_multi_options = $this->supports_feature('multi_options');

		if (is_array($this->_options)) {
			// First, prioritise any options that were explicitly set. This is the eventual goal for all storage modules.
			$options = $this->_options;
			
		} elseif (is_callable(array($this, 'get_opts'))) {
			// Next, get any options available via a legacy / over-ride method.
		
			if ($supports_multi_options) {
				// This is forbidden, because get_opts() is legacy and is for methods that do not support multi-options. Supporting multi-options leads to the array format being updated, which will then break get_opts().
				die('Fatal error: method '.$this->get_id().' both supports multi_options and provides a get_opts method');
			}
			
			$options = $this->get_opts();
			
		} else {

			// Next, look for job options (which in turn, falls back to saved settings if no job options were set)
	
			$options = $updraftplus->get_job_option('updraft_'.$this->get_id());
			if (!is_array($options)) $options = array();

			if ($supports_multi_options) {

				if (!isset($options['version'])) {
					$options_full = UpdraftPlus_Storage_Methods_Interface::update_remote_storage_options_format($this->get_id());
					
					if (is_wp_error($options_full)) {
						$updraftplus->log("Options retrieval failure: ".$options_full->get_error_code().": ".$options_full->get_error_message()." (".json_encode($options_full->get_error_data()).")");
						return array();
					}
					
				} else {
					$options_full = $options;
				}
				
				// UpdraftPlus_BackupModule::get_options() is for getting the current instance's options. So, this branch (going via the job option) is a legacy route, and hence we just give back the first one. The non-legacy route is to call the set_options() method externally.
				$options = reset($options_full['settings']);

				if (false === $options) {
					$updraftplus->log("Options retrieval failure (no options set)");
					return array();
				}
				$instance_id = key($options_full['settings']);
				$this->set_options($options, false, $instance_id);
				
			}
			
		}

		$options = apply_filters(
			'updraftplus_backupmodule_get_options',
			wp_parse_args($options, $this->get_default_options()),
			$this
		);
		
		return $options;
		
	}
	
	/**
	 * Set job data that is local to this storage instance
	 * (i.e. the key does not need to be unique across instances)
	 *
	 * @uses UpdraftPlus::jobdata_set()
	 *
	 * @param String $key	- the key for the job data
	 * @param Mixed  $value - the data to be stored
	 */
	public function jobdata_set($key, $value) {
	
		$instance_key = $this->get_id().'-'.($this->_instance_id ? $this->_instance_id : 'no_instance');
		
		global $updraftplus;
		
		$instance_data = $updraftplus->jobdata_get($instance_key);
		
		if (!is_array($instance_data)) $instance_data = array();
		
		$instance_data[$key] = $value;
		
		$updraftplus->jobdata_set($instance_key, $instance_data);
		
	}

	/**
	 * Get job data that is local to this storage instance
	 * (i.e. the key does not need to be unique across instances)
	 *
	 * @uses UpdraftPlus::jobdata_get()
	 *
	 * @param String	  $key		  - the key for the job data
	 * @param Mixed		  $default	  - the default to return if nothing was set
	 * @param String|Null $legacy_key - the previous name of the key, prior to instance-specific job data (so that upgrades across versions whilst a backup is in progress can still find its data). In future, support for this can be removed.
	 */
	public function jobdata_get($key, $default = null, $legacy_key = null) {
	
		$instance_key = $this->get_id().'-'.($this->_instance_id ? $this->_instance_id : 'no_instance');
		
		global $updraftplus;
		
		$instance_data = $updraftplus->jobdata_get($instance_key);
		
		if (is_array($instance_data) && isset($instance_data[$key])) return $instance_data[$key];
		
		return is_string($legacy_key) ? $updraftplus->jobdata_get($legacy_key, $default) : $default;
		
	}
	
	/**
	 * Delete job data that is local to this storage instance
	 * (i.e. the key does not need to be unique across instances)
	 *
	 * @uses UpdraftPlus::jobdata_set()
	 *
	 * @param String	  $key		  - the key for the job data
	 * @param String|Null $legacy_key - the previous name of the key, prior to instance-specific job data (so that upgrades across versions whilst a backup is in progress can still find its data)
	 */
	public function jobdata_delete($key, $legacy_key = null) {
	
		$instance_key = $this->get_id().'-'.($this->_instance_id ? $this->_instance_id : 'no_instance');
		
		global $updraftplus;
		
		$instance_data = $updraftplus->jobdata_get($instance_key);
		
		if (is_array($instance_data) && isset($instance_data[$key])) {
			unset($instance_data[$key]);
			$updraftplus->jobdata_set($instance_key, $instance_data);
		}
		
		if (is_string($legacy_key)) $updraftplus->jobdata_delete($legacy_key);
		
	}

	/**
	 * This method will either return or echo the constructed auth link for the remote storage method
	 *
	 * @param Boolean $echo_instead_of_return     - a boolean to indicate if the authentication link should be echo or returned
	 * @param Boolean $template_instead_of_notice - a boolean to indicate if the authentication link is for a template or a notice
	 * @return Void|String                        - returns a string or nothing depending on the parameters
	 */
	public function get_authentication_link($echo_instead_of_return = true, $template_instead_of_notice = true) {
		if (!$echo_instead_of_return) {
			ob_start();
		}

		$account_warning = '';
		$id = $this->get_id();
		$description = $this->get_description();

		if ($this->output_account_warning()) {
			$account_warning = __('Ensure you are logged into the correct account before continuing.', 'updraftplus');
		}

		if ($template_instead_of_notice) {
			$instance_id = "{{instance_id}}";
			$text = sprintf(__("<strong>After</strong> you have saved your settings (by clicking 'Save Changes' below), then come back here once and follow this link to complete authentication with %s.", 'updraftplus'), $description);
		} else {
			$instance_id = $this->get_instance_id();
			$text = sprintf(__('Follow this link to authorize access to your %s account (you will not be able to backup to %s without it).', 'updraftplus'), $description, $description);
		}

		echo $account_warning . ' <a class="updraft_authlink" href="'.UpdraftPlus_Options::admin_page_url().'?&action=updraftmethod-'.$id.'-auth&page=updraftplus&updraftplus_'.$id.'auth=doit&updraftplus_instance='.$instance_id.'" data-instance_id="'.$instance_id.'" data-remote_method="'.$id.'">'.$text.'</a>';

		if (!$echo_instead_of_return) {
			return ob_get_clean();
		}
	}
	
	/**
	 * Check the authentication is valid before proceeding to call the authentication method
	 */
	public function action_authenticate_storage() {
		if (isset($_GET['updraftplus_'.$this->get_id().'auth']) && 'doit' == $_GET['updraftplus_'.$this->get_id().'auth'] && !empty($_GET['updraftplus_instance'])) {
			$this->authenticate_storage((string) $_GET['updraftplus_instance']);
		}
	}
	
	/**
	 * Authenticate the remote storage and save settings
	 *
	 * @param String $instance_id - The remote storage instance id
	 */
	public function authenticate_storage($instance_id) {
		if (method_exists($this, 'do_authenticate_storage')) {
			$this->do_authenticate_storage($instance_id);
		} else {
			error_log($this->get_id().": module does not have an authenticate storage method (coding bug)");
		}
	}
	
	/**
	 * This method will either return or echo the constructed deauth link for the remote storage method
	 *
	 * @param  Boolean $echo_instead_of_return - a boolean to indicate if the deauthentication link should be echo or returned
	 * @return Void|String                     - returns a string or nothing depending on the parameters
	 */
	public function get_deauthentication_link($echo_instead_of_return = true) {
		if (!$echo_instead_of_return) {
			ob_start();
		}
		
		$id = $this->get_id();
		$description = $this->get_description();

		echo ' <a class="updraft_deauthlink" href="'.UpdraftPlus_Options::admin_page_url().'?action=updraftmethod-'.$id.'-auth&page=updraftplus&updraftplus_'.$id.'auth=deauth&nonce='.wp_create_nonce($id.'_deauth_nonce').'&updraftplus_instance={{instance_id}}" data-instance_id="{{instance_id}}" data-remote_method="'.$id.'">'.sprintf(__("Follow this link to remove these settings for %s.", 'updraftplus'), $description).'</a>';

		if (!$echo_instead_of_return) {
			return ob_get_clean();
		}
	}
	
	/**
	 * Check the deauthentication is valid before proceeding to call the deauthentication method
	 */
	public function action_deauthenticate_storage() {
		if (isset($_GET['updraftplus_'.$this->get_id().'auth']) && 'deauth' == $_GET['updraftplus_'.$this->get_id().'auth'] && !empty($_GET['nonce']) && !empty($_GET['updraftplus_instance']) && wp_verify_nonce($_GET['nonce'], $this->get_id().'_deauth_nonce')) {
			$this->deauthenticate_storage($_GET['updraftplus_instance']);
		}
	}
	
	/**
	 * Deauthenticate the remote storage and remove the saved settings
	 *
	 * @param String $instance_id - The remote storage instance id
	 */
	public function deauthenticate_storage($instance_id) {
		if (method_exists($this, 'do_deauthenticate_storage')) {
			$this->do_deauthenticate_storage($instance_id);
		}
		$opts = $this->get_default_options();
		$this->set_options($opts, true, $instance_id);
	}

	/**
	 * Get the manual authorisation template
	 *
	 * @return String - the template
	 */
	public function get_manual_authorisation_template() {

		$id = $this->get_id();
		$description = $this->get_description();

		$template = "<div id='updraftplus_manual_authorisation_template_{$id}'>";
		$template .= "<strong>".sprintf(__('%s authentication:', 'updraftplus'), $description)."</strong>";
		$template .= "<p>".sprintf(__('If you are having problems authenticating with %s you can manually authorize here.', 'updraftplus'), $description)."</p>";
		$template .= "<p>".__('To complete manual authentication, at the orange UpdraftPlus authentication screen select the "Having problems authenticating?" link, then copy and paste the code given here.', 'updraftplus')."</p>";
		$template .= "<label for='updraftplus_manual_authentication_data_{$id}'>".sprintf(__('%s authentication code:', 'updraftplus'), $description)."</label> <input type='text' id='updraftplus_manual_authentication_data_{$id}' name='updraftplus_manual_authentication_data_{$id}'>";
		$template .= "<p id='updraftplus_manual_authentication_error_{$id}'></p>";
		$template .= "<button type='button' data-method='{$id}' class='button button-primary' id='updraftplus_manual_authorisation_submit_{$id}'>".__('Complete manual authentication', 'updraftplus')."</button>";
		$template .= '<span class="updraftplus_spinner spinner">' . __('Processing', 'updraftplus') . '...</span>';
		$template .= "</div>";

		return $template;
	}

	/**
	 * This will call the remote storage methods complete authentication function
	 *
	 * @param string $state - the remote storage authentication state
	 * @param string $code  - the remote storage authentication code
	 *
	 * @return String - returns a string response
	 */
	public function complete_authentication($state, $code) {
		if (method_exists($this, 'do_complete_authentication')) {
			return $this->do_complete_authentication($state, $code, true);
		} else {
			$message = $this->get_id().": module does not have an complete authentication method (coding bug)";
			error_log($message);
			return $message;
		}
	}

	/**
	 * Over-ride this to allow methods to output extra information about using the correct account for OAuth storage methods
	 *
	 * @return Boolean - return false so that no extra information is output
	 */
	public function output_account_warning() {
		return false;
	}

	/**
	 * This function is a wrapper and will call $updraftplus->log(), the backup modules should use this so we can add information to the log lines to do with the remote storage and instance settings.
	 *
	 * @param string  $line       - the log line
	 * @param string  $level      - the log level: notice, warning, error. If suffixed with a hypen and a destination, then the default destination is changed too.
	 * @param boolean $uniq_id    - each of these will only be logged once
	 * @param boolean $skip_dblog - if true, then do not write to the database
	 *
	 * @return void
	 */
	public function log($line, $level = 'notice', $uniq_id = false, $skip_dblog = false) {
		global $updraftplus;

		$prefix = $this->get_storage_label();

		$updraftplus->log("$prefix: $line", $level, $uniq_id, $skip_dblog);
	}

	/**
	 * This function will build and return the remote storage instance label
	 *
	 * @return String - the remote storage instance label
	 */
	private function get_storage_label() {
		
		$opts = $this->get_options();
		$label = isset($opts['instance_label']) ? $opts['instance_label'] : '';

		$description = $this->get_description();

		if (!empty($label)) {
			$prefix = (false !== strpos($label, $description)) ? $label : "$description: $label";
		} else {
			$prefix = $description;
		}

		return $prefix;
	}
}
