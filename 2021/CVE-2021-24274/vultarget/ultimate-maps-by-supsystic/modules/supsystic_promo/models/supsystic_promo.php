<?php
class supsystic_promoModelUms extends modelUms {
	private $_apiUrl = '';
	private function _getApiUrl() {
		if(empty($this->_apiUrl)) {
			$this->_initApiUrl();
		}
		return $this->_apiUrl;
	}
	public function welcomePageSaveInfo($d = array()) {
		$reqUrl = $this->_getApiUrl(). '?mod=options&action=saveWelcomePageInquirer&pl=rcs';
		$d['where_find_us'] = (int) $d['where_find_us'];
		$desc = '';
		if(in_array($d['where_find_us'], array(4, 5))) {
			$desc = $d['where_find_us'] == 4 ? $d['find_on_web_url'] : $d['other_way_desc'];
		}
		wp_remote_post($reqUrl, array(
			'body' => array(
				'site_url' => get_bloginfo('wpurl'),
				'site_name' => get_bloginfo('name'),
				'where_find_us' => $d['where_find_us'],
				'desc' => $desc,
				'plugin_code' => UMS_CODE,
			)
		));
		// In any case - give user posibility to move futher
		return true;
	}
	public function saveUsageStat($code, $unique = false) {
		if($unique && $this->_checkUniqueStat($code)) {
			return;
		}
		// $query = 'INSERT INTO @__usage_stat SET code = "'. $code.'", visits = 1
		// 	ON DUPLICATE KEY UPDATE visits = visits + 1';
		// return dbUms::query($query);
	}
	private function _checkUniqueStat($code) {
		$uniqueStats = get_option(UMS_CODE. '_unique_stats');
		if(empty($uniqueStats))
			$uniqueStats = array();
		if(in_array($code, $uniqueStats)) {
			return true;
		}
		$uniqueStats[] = $code;
		update_option(UMS_CODE. '_unique_stats', $uniqueStats);
		return false;
	}
	public function saveSpentTime($code, $spent) {
		// $spent = (int) $spent;
		// $query = 'UPDATE @__usage_stat SET spent_time = spent_time + '. $spent. ' WHERE code = "'. $code. '"';
		// return dbUms::query($query);
	}
	public function getAllUsageStat() {
		$query = 'SELECT * FROM @__usage_stat';
		return dbUms::get($query);
	}
	public function sendUsageStat() {
		$allStat = $this->getAllUsageStat();
		$reqUrl = $this->_getApiUrl(). '?mod=options&action=saveUsageStat&pl=rcs';
		$res = wp_remote_post($reqUrl, array(
			'body' => array(
				'site_url' => get_bloginfo('wpurl'),
				'site_name' => get_bloginfo('name'),
				'plugin_code' => UMS_CODE,
				'all_stat' => $allStat
			)
		));
		$this->clearUsageStat();
		// In any case - give user posibility to move futher
		return true;
	}
	public function clearUsageStat() {
		// $query = 'DELETE FROM @__usage_stat';
		// return dbUms::query($query);
	}
	public function getUserStatsCount() {
		$query = 'SELECT SUM(visits) AS total FROM @__usage_stat';
		return (int) dbUms::get($query, 'one');
	}
	public function checkAndSend($force = false){
		$statCount = $this->getUserStatsCount();
		if($statCount >= $this->getModule()->getMinStatSend() || $force) {
			$this->sendUsageStat();
		}
	}
	protected function _initApiUrl() {
		$this->_apiUrl = implode('', array('','h','t','tp',':','/','/u','p','da','t','e','s.','s','u','ps','y','st','i','c.','c','o','m',''));
	}
}
