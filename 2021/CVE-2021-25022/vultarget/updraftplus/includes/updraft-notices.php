<?php

if (!defined('ABSPATH')) die('No direct access allowed');

/**
 * If we ever change the API of the Updraft_Notices class, then we'll need to rename and version it, e.g. Updraft_Notices_1_0, because otherwise a plugin may find that it's loaded an older instance than it wanted from another plugin.
 */
abstract class Updraft_Notices {

	protected $notices_content;
	
	// These variables are just short-hands to be used in advert content.
	protected $dashboard_top = array('top');

	protected $dashboard_top_or_report = array('top', 'report', 'report-plain');

	protected $dashboard_bottom_or_report = array('bottom', 'report', 'report-plain');

	protected $anywhere = array('top', 'bottom', 'report', 'report-plain');

	protected $autobackup = array('autobackup');

	protected $autobackup_bottom_or_report = array('autobackup', 'bottom', 'report', 'report-plain');

	protected function populate_notices_content() {
		// Global adverts that appear in all products will be returned to the child to display
		return array();
	}
	
	/**
	 * Call this method to setup the notices
	 */
	abstract protected function notices_init();

	protected function translation_needed($plugin_base_dir, $product_name) {
		$wplang = get_locale();
		if (strlen($wplang) < 1 || 'en_US' == $wplang || 'en_GB' == $wplang) return false;
		if (defined('WP_LANG_DIR') && is_file(WP_LANG_DIR.'/plugins/'.$product_name.'-'.$wplang.'.mo')) return false;
		if (is_file($plugin_base_dir.'/languages/'.$product_name.'-'.$wplang.'.mo')) return false;
		return true;
	}
	
	protected function url_start($html_allowed, $url, $https = false, $website_home = null) {
		$proto = ($https) ? 'https' : 'http';
		if (strpos($url, $website_home) !== false) {
			return $html_allowed ? "<a href=".apply_filters(str_replace('.', '_', $website_home).'_link', $proto.'://'.$url).">" : "";
		} else {
			return $html_allowed ? "<a href=\"$proto://$url\">" : "";
		}
	}

	protected function url_end($html_allowed, $url, $https = false) {
		$proto = ($https) ? 'https' : 'http';
		return $html_allowed ? '</a>' : " ($proto://$url)";
	}

	public function do_notice($notice = false, $position = 'top', $return_instead_of_echo = false) {

		$this->notices_init();
	
		if (false === $notice) $notice = apply_filters('updraft_notices_force_id', false, $this);
		
		$notice_content = $this->get_notice_data($notice, $position);
		
		if (false != $notice_content) {
			return $this->render_specified_notice($notice_content, $return_instead_of_echo, $position);
		}
	}

	/**
	 * This method will return a notice ready for display.
	 *
	 * @param  boolean $notice   the notice to grab the data
	 * @param  string  $position position of notice
	 * @return array
	 */
	protected function get_notice_data($notice = false, $position = 'top') {

		// If a specific notice has been passed to this method then return that notice.
		if ($notice) {
			if (!isset($this->notices_content[$notice])) return false;
		
			// Does the notice support the position specified?
			if (isset($this->notices_content[$notice]['supported_positions']) && !in_array($position, $this->notices_content[$notice]['supported_positions'])) return false;

			/*
				First check if the advert passed can be displayed and hasn't been dismissed, we do this by checking what dismissed value we should be checking.
			 */
			$dismiss_time = $this->notices_content[$notice]['dismiss_time'];

			$dismiss = $this->check_notice_dismissed($dismiss_time);

			if ($dismiss) return false;

			// If the advert has a validity function, then require the advert to be valid
			if (!empty($this->notices_content[$notice]['validity_function']) && !call_user_func(array($this, $this->notices_content[$notice]['validity_function']))) return false;

			return $this->notices_content[$notice];
		}

		// create an array to add non-seasonal adverts to so that if a seasonal advert can't be returned we can choose a random advert from this array.
		$available_notices = array();

		// If Advert wasn't passed then next we should check to see if a seasonal advert can be returned.
		foreach ($this->notices_content as $notice_id => $notice_data) {
			// Does the notice support the position specified?
			if (isset($this->notices_content[$notice_id]['supported_positions']) && !in_array($position, $this->notices_content[$notice_id]['supported_positions'])) continue;
			
			// If the advert has a validity function, then require the advert to be valid
			if (!empty($notice_data['validity_function']) && !call_user_func(array($this, $notice_data['validity_function']))) continue;
		
			if (isset($notice_data['valid_from']) && isset($notice_data['valid_to'])) {

				if ($this->skip_seasonal_notices($notice_data)) return $notice_data;

			} else {

				$dismiss_time = $this->notices_content[$notice_id]['dismiss_time'];
				$dismiss = $this->check_notice_dismissed($dismiss_time);
			
				if (!$dismiss) $available_notices[$notice_id] = $notice_data;
			}
		}
		
		if (empty($available_notices)) return false;

		// If a seasonal advert can't be returned then we will return a random advert

		// Here we give a 25% chance for the rate advert to be returned before selecting a random advert from the entire collection which also includes the rate advert
		if (0 == rand(0, 3) && isset($available_notices['rate'])) return $available_notices['rate'];

		/*
			Using shuffle here as something like rand which produces a random number and uses that as the array index fails, this is because in future an advert may not be numbered and could have a string as its key which will then cause errors.
		*/
		
		shuffle($available_notices);
		return $available_notices[0];
	}

	protected function skip_seasonal_notices($notice_data) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		return false;
	}

	abstract protected function check_notice_dismissed($dismiss_time);
}
