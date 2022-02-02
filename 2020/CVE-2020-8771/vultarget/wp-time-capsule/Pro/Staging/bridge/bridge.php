<?php

if (!isset($_REQUEST)) {
	$this->send_response(array('error' => "Request is missing"));
}
$bridge = new Wptc_Bridge($_REQUEST);
$bridge->init();


class Wptc_Bridge{
	protected $params;
	protected $secret_code_start;
	protected $secret_code_end;
	protected $options_obj;
	protected $staging_abspath;
	protected $meta_file_name;

	public function __construct($params){
		$this->params = $params;
		$this->secret_code_start = '<WPTCHEADER>';
		$this->secret_code_end = '</ENDWPTCHEADER>';
		$this->staging_abspath = $this->get_staging_abspath();
		$this->meta_file_name = $this->staging_abspath.'wp-tcapsule-bridge/wordpress-db_meta_data.sql';
	}

	public function get_staging_abspath(){
		return dirname(dirname(__FILE__)). '/';
	}

	public function init(){
		if (!isset($this->params['data'])) {
			$this->send_response(array('error' => "Request data is missing"));
		}
		$this->decode_request_data();
		$this->find_action();
	}

	public function decode_request_data(){
		$this->params = unserialize(base64_decode($this->params['data']));
	}

	public function find_action(){
		if (!isset($this->params['action'])){
			$this->send_response(array('error' => "could not find action"));
		}
		$this->define_constants();
		switch ($this->params['action']) {
			case 'update_in_staging':
				$this->update_in_staging();
				break;
			default:
				$this->send_response(array('error' => "action is not found"));
		}
	}

	public function define_constants(){
		if(!defined('WP_DEBUG')){
			define('WP_DEBUG', false);
		}
		if(!defined('WP_DEBUG_DISPLAY')){
			define('WP_DEBUG_DISPLAY', false);
		}
	}

	public function send_response($data){
		$response_data = $this->secret_code_start . base64_encode(serialize($data)) . $this->secret_code_end;
		die($response_data);
	}

	private function include_wp_config(){
		@include_once $this->staging_abspath.'wp-config.php';
		@include_once $this->staging_abspath.'wp-admin/includes/file.php';
	}

	private function update_in_staging(){
		$this->perform_updates();
	}

	private function perform_updates(){
		$this->include_wp_config();
		$this->do_single_upgrades($this->params['type'], $this->params['update_items']);
	}

	private function do_single_upgrades($type, $update_items) {
		echo $type;
		print_r($update_items);
		if ($type == 'plugin') {
			$return = $this->upgrade_plugin($update_items);
		} else if ($type == 'theme') {
			$return = $this->upgrade_theme($update_items);
		} else if ($type == 'core') {
			$return = $this->upgrade_core($update_items);
		} else if ($type == 'translation') {
			$return = $this->upgrade_translation($update_items);
		}
		$this->send_response($return);
	}

	private function wptc_mmb_get_transient($option_name) {
		if (trim($option_name) == '') {
			return FALSE;
		}
		global $wp_version;
		$transient = array();
		if (version_compare($wp_version, '2.7.9', '<=')) {
			return get_option($option_name);
		} else if (version_compare($wp_version, '2.9.9', '<=')) {
			$transient = get_option('_transient_' . $option_name);
			return apply_filters("transient_" . $option_name, $transient);
		} else {
			$transient = get_option('_site_transient_' . $option_name);
			return apply_filters("site_transient_" . $option_name, $transient);
		}
	}

	private function wptc_mmb_get_error($error_object) {
		if (!is_wp_error($error_object)) {
			return $error_object != '' ? $error_object : '';
		} else {
			$errors = array();
			if(!empty($error_object->error_data))  {
				foreach ($error_object->error_data as $error_key => $error_string) {
					$errors[] = str_replace('_', ' ', ucfirst($error_key)) . ': ' . $error_string;
				}
			} elseif (!empty($error_object->errors)){
				foreach ($error_object->errors as $error_key => $err) {
					$errors[] = 'Error: '.str_replace('_', ' ', strtolower($error_key));
				}
			}
			return implode('<br />', $errors);
		}
	}

	private function upgrade_plugin($plugins, $plugin_details = false) {


		if (!$plugins || empty($plugins)) {
			return array(
				'error' => 'No plugin files for upgrade.', 'error_code' => 'no_plugin_files_for_upgrade'
			);
		}

		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin.php';


		$current = $this->wptc_mmb_get_transient('update_plugins');

		$versions = array();
		if (!empty($current)) {
			foreach ($plugins as $plugin => $data) {
				if (isset($current->checked[$plugin])) {
					$versions[$current->checked[$plugin]] = $plugin;
				} else if (isset($current->response[$plugin])) {
					$versions[$plugin] =  $current->response[$plugin];
				}
			}
		}

		$return = array();

		if (class_exists('Plugin_Upgrader') && class_exists('Bulk_Plugin_Upgrader_Skin')) {
			if (!function_exists('wp_update_plugins'))
				include_once(ABSPATH . 'wp-includes/update.php');

			@wp_update_plugins();
			$upgrader = new Plugin_Upgrader(new Bulk_Plugin_Upgrader_Skin(compact('nonce', 'url')));
			$result = $upgrader->bulk_upgrade(array_keys($plugins));

			$current = $this->wptc_mmb_get_transient('update_plugins');

			if (!empty($result)) {
				foreach ($result as $plugin_slug => $plugin_info) {
					if (!$plugin_info || is_wp_error($plugin_info)) {
						$return[$plugin_slug] = array('error' => $this->wptc_mmb_get_error($plugin_info), 'error_code' => 'upgrade_plugins_wp_error');
					} else {
						if(
							!empty($result[$plugin_slug])
							|| (
									isset($current->checked[$plugin_slug])
									&& version_compare(array_search($plugin_slug, $versions), $current->checked[$plugin_slug], '<') == true
								)
						){
							$return[$plugin_slug] = 1;
						} else {
							$return[$plugin_slug] = array('error' => 'Could not refresh upgrade transients, please reload website data', 'error_code' => 'upgrade_plugins_could_not_refresh_upgrade_transients_please_reload_website_data');
						}
					}
				}
				@ob_end_clean();
				return array(
					'upgraded' => $return
				);
			} else {
				return array(
					'error' => 'Upgrade failed.', 'error_code' => 'upgrade_failed_upgrade_plugins'
				);
			}
		} else {
			ob_end_clean();
			return array(
				'error' => 'WordPress update required first.', 'error_code' => 'upgrade_plugins_wordPress_update_required_first'
			);
		}
	}

	private function upgrade_theme($themes, $theme_details = false) {
		if (!$themes || empty($themes)) {
			return array(
				'error' => 'No theme files for upgrade.', 'error_code' => 'no_theme_files_for_upgrade'
			);
		}

		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/theme.php';

		$current = $this->wptc_mmb_get_transient('update_themes');

		$versions = array();
		if (!empty($current)) {
			foreach ($themes as $theme) {
				if (isset($current->checked[$theme])) {
					$versions[$current->checked[$theme]] = $theme;
				} else if (isset($current->response[$theme])) {
					$versions[$theme] =  $current->response[$theme];
				}
			}
		}

		if (class_exists('Theme_Upgrader') && class_exists('Bulk_Theme_Upgrader_Skin')) {
			$upgrader = new Theme_Upgrader(new Bulk_Theme_Upgrader_Skin(compact('title', 'nonce', 'url', 'theme')));
			$result = $upgrader->bulk_upgrade($themes);

			if (!function_exists('wp_update_themes')) {
				include_once ABSPATH . 'wp-includes/update.php';
			}

			@wp_update_themes();
			$current = $this->wptc_mmb_get_transient('update_themes');
			$return = array();

			if (!empty($result)) {
				foreach ($result as $theme_tmp => $theme_info) {
					 if (is_wp_error($theme_info) || empty($theme_info)) {
						$return[$theme_tmp] = array('error' => $this->wptc_mmb_get_error($theme_info), 'error_code' => 'upgrade_themes_wp_error');
					}  else {
						if(!empty($result[$theme_tmp]) || (isset($current->checked[$theme_tmp]) && version_compare(array_search($theme_tmp, $versions), $current->checked[$theme_tmp], '<') == true)){
							$return[$theme_tmp] = 1;
						} else {
							$return[$theme_tmp] = array('error' => 'Could not refresh upgrade transients, please reload website data', 'error_code' => 'upgrade_themes_could_not_refresh_upgrade_transients_reload_website');
						}
					}
				}
				return array(
					'upgraded' => $return
				);
			} else {
				return array(
					'error' => 'Upgrade failed.', 'error_code' => 'upgrade_failed_upgrade_themes'
				);
			}
		} else {
			ob_end_clean();
			return array(
				'error' => 'WordPress update required first', 'error_code' => 'wordPress_update_required_first_upgrade_themes'
			);
		}
	}

	private function upgrade_core($current) {

		if (!$current || empty($current)) {
			return array(
				'error' => 'No core data for upgrade.', 'error_code' => 'no_core_files_for_upgrade'
			);
		}

		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';
		@include_once ABSPATH . 'wp-admin/includes/template.php';

		if (!function_exists('wp_version_check') || !function_exists('get_core_checksums')) {
			include_once ABSPATH . '/wp-admin/includes/update.php';
		}

		@wp_version_check();

		$current_update = false;
		ob_end_flush();
		ob_end_clean();

		$is_multisite = is_multisite();
		$is_function_exist = function_exists('get_site_option');
		if( $is_multisite && $is_function_exist ){
			$core = get_site_option( '_site_transient_update_core' );
		} else {
			$core = wptc_mmb_get_transient('update_core');
		}

		if (isset($core->updates) && !empty($core->updates)) {
			$updates = $core->updates[0];
			$updated = $core->updates[0];
			if (!isset($updated->response) || $updated->response == 'latest') {
				return array(
					'upgraded' => 'updated'
				);
			}

			if ($updated->response == "development" && $current->response == "upgrade") {
				return array(
					'error' => '<font color="#900">Unexpected error. Please upgrade manually.</font>', 'error_code' => 'unexpected_error_please_upgrade_manually'
				);
			} else if ($updated->response == $current->response || ($updated->response == "upgrade" && $current->response == "development")) {
				if ($updated->locale != $current->locale) {
					foreach ($updates as $update) {
						if ($update->locale == $current->locale) {
							$current_update = $update;
							break;
						}
					}
					if ($current_update == false) {
						return array(
							'error' => ' Localization mismatch. Try again.', 'error_code' => 'localization_mismatch'
						);
					}
				} else {
					$current_update = $updated;
				}
			} else {
				return array(
					'error' => ' Transient mismatch. Try again.', 'error_code' => 'transient_mismatch'
				);
			}
		} else {
			return array(
				'error' => ' Refresh transient failed. Try again.', 'error_code' => 'refresh_transient_failed'
			);
		}
		if ($current_update != false) {
			global $wp_filesystem, $wp_version;

			if (version_compare($wp_version, '3.1.9', '>')) {
				if (!class_exists('Core_Upgrader')) {
					include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				}
				$core = new Core_Upgrader();
				$result = $core->upgrade($current_update);
				$this->wptc_mmb_maintenance_mode(false);
				if (is_wp_error($result)) {
					return array(
						'error' => $this->wptc_mmb_get_error($result), 'error_code' => 'maintenance_mode_upgrade_core'
					);
				} else {
					return array(
						'upgraded' => 'updated'
					);
				}
			} else {
				if (!class_exists('WP_Upgrader')) {
					include_once ABSPATH . 'wp-admin/includes/update.php';
					if (function_exists('wp_update_core')) {
						$result = wp_update_core($current_update);
						if (is_wp_error($result)) {
							return array(
								'error' => $this->wptc_mmb_get_error($result), 'error_code' => 'wp_update_core_upgrade_core'
							);
						} else {
							return array(
								'upgraded' => 'updated'
							);
						}
					}
				}
				if (class_exists('WP_Upgrader')) {
					$upgrader_skin = new WP_Upgrader_Skin();
					$upgrader_skin->done_header = true;

					$upgrader = new WP_Upgrader($upgrader_skin);

					// Is an update available?
					if (!isset($current_update->response) || $current_update->response == 'latest') {
						return array(
							'upgraded' => 'updated'
						);
						return false;
					}
					$res = $upgrader->fs_connect(array(
						ABSPATH,
						WP_CONTENT_DIR,
					));
					if (is_wp_error($res)) {
						return array(
							'error' => $this->wptc_mmb_get_error($res), 'error_code' => 'upgrade_core_wp_error_res'
						);
					}
					$wp_dir = trailingslashit($wp_filesystem->abspath());

					$core_package = false;
					if (isset($current_update->package) && !empty($current_update->package)) {
						$core_package = $current_update->package;
					} elseif (isset($current_update->packages->full) && !empty($current_update->packages->full)) {
						$core_package = $current_update->packages->full;
					}

					$download = $upgrader->download_package($core_package);
					if (is_wp_error($download)) {
						return array(
							'error' => $this->wptc_mmb_get_error($download), 'error_code' => 'download_upgrade_core'
						);
					}

					$working_dir = $upgrader->unpack_package($download);
					if (is_wp_error($working_dir)) {
						return array(
							'error' => $this->wptc_mmb_get_error($working_dir), 'error_code' => 'working_dir_upgrade_core'
						);
					}

					if (!$wp_filesystem->copy($working_dir . '/wordpress/wp-admin/includes/update-core.php', $wp_dir . 'wp-admin/includes/update-core.php', true)) {
						$wp_filesystem->delete($working_dir, true);
						return array(
							'error' => 'Unable to move update files.', 'error_code' => 'unable_to_move_update_files'
						);
					}

					$wp_filesystem->chmod($wp_dir . 'wp-admin/includes/update-core.php', FS_CHMOD_FILE);

					require ABSPATH . 'wp-admin/includes/update-core.php';

					$update_core = update_core($working_dir, $wp_dir);

					$this->wptc_mmb_maintenance_mode(false);
					if (is_wp_error($update_core)) {
						return array(
							'error' => $this->wptc_mmb_get_error($update_core), 'error_code' => 'upgrade_core_wp_error'
						);
					}
					ob_end_flush();
					return array(
						'upgraded' => 'updated'
					);
				} else {
					return array(
						'error' => 'failed', 'error_code' => 'failed_WP_Upgrader_class_not_exists'
					);
				}
			}
		} else {
			return array(
				'error' => 'failed', 'error_code' => 'failed_current_update_false'
			);
		}
	}

	private function wptc_mmb_maintenance_mode($enable = false, $maintenance_message = '') {
		global $wp_filesystem;
		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (empty($wp_filesystem)) {
				send_response_wptc('FS_INIT_FAILED-015');
				return false;
			}
		}
		$maintenance_message .= '<?php $upgrading = ' . time() . '; ?>';

		$file = $wp_filesystem->abspath() . '.maintenance';
		if ($enable) {
			$wp_filesystem->delete($file);
			$wp_filesystem->put_contents($file, $maintenance_message, FS_CHMOD_FILE);
		} else {
			$wp_filesystem->delete($file);
		}
	}


	private function initiate_filesystem_wptc() {
		$creds = request_filesystem_credentials("", "", false, false, null);
		if (false === $creds) {
			return false;
		}

		if (!WP_Filesystem($creds)) {
			return false;
		}
	}

	private function upgrade_translation($data = false) {

		@include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		@include_once ABSPATH . 'wp-admin/includes/file.php';
		@include_once ABSPATH . 'wp-admin/includes/misc.php';
		@include_once ABSPATH . 'wp-admin/includes/template.php';
		@include_once ABSPATH . 'wp-admin/includes/plugin.php';
		@include_once ABSPATH . 'wp-admin/includes/theme.php';

		if (!function_exists('wp_version_check') || !function_exists('get_core_checksums')) {
			include_once ABSPATH . '/wp-admin/includes/update.php';
		}

		$upgrader = new Language_Pack_Upgrader(new Language_Pack_Upgrader_Skin(compact('url', 'nonce', 'title', 'context')));
		$result = $upgrader->bulk_upgrade();
		$upgradeFailed = false;

		if (!empty($result)) {
			foreach ($result as $translate_tmp => $translate_info) {
				if (is_wp_error($translate_info) || empty($translate_info)) {
					$upgradeFailed = true;
					$return = array('error' => $this->wptc_mmb_get_error($translate_info), 'error_code' => 'upgrade_translations_wp_error');
					break;
				}
			}
			if (!$upgradeFailed) {
				$update_message = 'Translations are updated successfully ';
				$return = 'updated';
			}
			return array('upgraded' => $return);
		} else {
			return array(
				'error' => 'Upgrade failed.', 'error_code' => 'unable_to_update_translations_files'
			);
		}
	}
}
