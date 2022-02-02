<?php

class Wptc_Update_Stats {

	public function __construct(){
		include_once(ABSPATH . 'wp-includes/update.php');
		include_once(ABSPATH . '/wp-admin/includes/update.php');
	}

	public function get_upgradable_plugins( $filter = array() ) {
		if (!function_exists('wp_update_plugins')){
			include_once(ABSPATH . 'wp-includes/update.php');
		}

		@wp_update_plugins();

		$current            = $this->get_transient('update_plugins');
		$upgradable_plugins = array();

		if (!empty($current->response)) {
			if (!function_exists('get_plugin_data'))
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			foreach ($current->response as $plugin_path => $plugin_data) {
				// if ($plugin_path == 'wp-time-capsule/wp-time-capsule.php')
					// continue;

				$data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
				if(isset($data['Name']) && in_array($data['Name'], $filter))
					continue;

				if (strlen($data['Name']) > 0 && strlen($data['Version']) > 0) {
					$current->response[$plugin_path]->name        = $data['Name'];
					$current->response[$plugin_path]->old_version = $data['Version'];
					$current->response[$plugin_path]->file        = $plugin_path;
					unset($current->response[$plugin_path]->upgrade_notice);
					$upgradable_plugins[]                         = $current->response[$plugin_path];
				}
			}
			return $upgradable_plugins;
		} else{
			return array();
		}
	}

	public function get_upgradable_themes($filter = array()) {
		if (function_exists('wp_get_themes')) {
			$all_themes     = wp_get_themes();
			$upgrade_themes = array();

			$current = $this->get_transient('update_themes');
			if (!empty($current->response)) {
				foreach ((array) $all_themes as $theme_template => $theme_data) {
					foreach ($current->response as $current_themes => $theme) {
						if ($theme_data->Stylesheet !== $current_themes) {
							continue;
						}

						if (strlen($theme_data->Name) === 0 || strlen($theme_data->Version) === 0) {
							continue;
						}

						$current->response[$current_themes]['name']        = $theme_data->Name;
						$current->response[$current_themes]['old_version'] = $theme_data->Version;
						$current->response[$current_themes]['theme_tmp']   = $theme_data->Stylesheet;

						$upgrade_themes[] = $current->response[$current_themes];
					}
				}
			}
		} else {
			$all_themes = get_themes();

			$upgrade_themes = array();

			$current = $this->get_transient('update_themes');

			if (!empty($current->response)) {
				foreach ((array) $all_themes as $theme_template => $theme_data) {
					if (isset($theme_data['Parent Theme']) && !empty($theme_data['Parent Theme'])) {
						continue;
					}

					if (isset($theme_data['Name']) && in_array($theme_data['Name'], $filter)) {
						continue;
					}

					if (method_exists($theme_data,'parent') && !$theme_data->parent()) {
						foreach ($current->response as $current_themes => $theme) {
							if ($theme_data['Template'] == $current_themes) {
								if (strlen($theme_data['Name']) > 0 && strlen($theme_data['Version']) > 0) {
									$current->response[$current_themes]['name']        = $theme_data['Name'];
									$current->response[$current_themes]['old_version'] = $theme_data['Version'];
									$current->response[$current_themes]['theme_tmp']   = $theme_data['Template'];
									$upgrade_themes[]                                  = $current->response[$current_themes];
								}
							}
						}
					}
				}
			}

		}

		return $upgrade_themes;
	}

	public function get_upgradable_translations() {
		 if (!function_exists('wp_get_translation_updates')){
			include_once(ABSPATH . 'wp-includes/update.php');
		 }

		if (function_exists('wp_get_translation_updates')) {
			$translations_object = wp_get_translation_updates();
			$translations_object = array_filter($translations_object);
		 }

		if (isset($translations_object) && !empty($translations_object)){
			return true;
		} else{
			return false;
		}
	}

	public function get_core_update($stats = array())
	{
		global $wp_version;

		$core = $this->get_transient('update_core');
		if (isset($core->updates) && !empty($core->updates)) {
			$current_transient = $core->updates[0];
			if ($current_transient->response == "development" || version_compare($wp_version, $current_transient->current, '<')) {
				$current_transient->current_version = $wp_version;
				return $current_transient;
			} else
				return false;
		}

		return false;
	}

	public function get_transient($option_name) {
		global $wp_version;

		if (trim($option_name) == '') {
			return false;
		}

		if (version_compare($wp_version, '3.4', '>')) {
			return get_site_transient($option_name);
		}

		if ( is_multisite() ) {
			return $this->get_sitemeta_transient($option_name, true);
		}

		$transient = get_option('_site_transient_'.$option_name);

		return apply_filters("site_transient_".$option_name, $transient);
	}

	public function get_sitemeta_transient($option_name, $is_multisite){
		global $wpdb;
		$option_name = '_site_transient_'. $option_name;

		$result = $wpdb->get_var( $wpdb->prepare("SELECT `meta_value` FROM `{$wpdb->sitemeta}` WHERE meta_key = %s AND `site_id` = %s", $option_name, $is_multisite));
		$result = maybe_unserialize($result);
		return $result;
	}
}