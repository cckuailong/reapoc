<?php
class statisticsModelCfs extends modelCfs {
	public function __construct() {
		$this->_setTbl('statistics');
	}
	public function add($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		$d['type'] = isset($d['type']) ? $d['type'] : '';
		if(!empty($d['id']) && !empty($d['type'])) {
			$typeId = $this->getModule()->getTypeIdByCode( $d['type'] );
			$isUnique = 0;
			if(isset($d['is_unique']) && !empty($d['is_unique'])) {
				$isUnique = (int) 1;	// This is realy cool :)
			}
			$formModel = frameCfs::_()->getModule('forms')->getModel();
			if(in_array($d['type'], array('show'))) {
				$formModel->addViewed( $d['id'] );
				if($isUnique) {
					$formModel->addUniqueViewed( $d['id'] );
				}
			} else {	// Any action count here
				if(!in_array($d['type'], array('submit', 'submit_error'))) {	// Do not count empty submits here
					$formModel->addActionDone( $d['id'] );
				}
			}
			$data = array(
				'form_id' => $d['id'],
				'type' => $typeId,
				'is_unique' => $isUnique,
			);
			return $this->supInsert($data);
		} else
			$this->pushError(__('Send me some info, pls', CFS_LANG_CODE));
		return false;
	}
	/**
	 * Get list for form
	 * @param numeric $pid Form ID
	 * @param array $params Additional selection params, $params = array('type' => '')
	 * @return array List of statistics data
	 */
	public function getForForm($formId, $params = array()) {
		$where = array('form_id' => $formId);
		$typeId = isset($params['type']) ? $params['type'] : 0;
		if($typeId && !is_numeric($typeId)) {
			$typeId = $this->getModule()->getTypeIdByCode( $typeId );
		}
		if($typeId) {
			$where['type'] = $typeId;
		}
		$group = !empty($params['group']) ? $params['group'] : 'day';
		$sqlDateFormat = '';
		global $wpdb;
		switch ($group) {
			case 'day':
				if ($formId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(date_created, '%m-%d-%Y') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s ORDER BY date DESC", $formId), ARRAY_A);
				}
				if ($formId && $typeId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(date_created, '%m-%d-%Y') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s AND type = %s ORDER BY date DESC", $formId, $typeId), ARRAY_A);
				}
			break;
			case 'hour':
				if ($formId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(date_created, '%m-%d-%Y %H:00') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s ORDER BY date DESC", $formId), ARRAY_A);
				}
				if ($formId && $typeId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(date_created, '%m-%d-%Y %H:00') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s AND type = %s ORDER BY date DESC", $formId, $typeId), ARRAY_A);
				}
			break;
			case 'week':
				if ($formId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(DATE_SUB(date_created, INTERVAL DAYOFWEEK(date_created)-1 DAY), '%m-%d-%Y') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s ORDER BY date DESC", $formId), ARRAY_A);
				}
				if ($formId && $typeId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(DATE_SUB(date_created, INTERVAL DAYOFWEEK(date_created)-1 DAY), '%m-%d-%Y') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s AND type = %s ORDER BY date DESC", $formId, $typeId), ARRAY_A);
				}
			break;
			case 'month':
				if ($formId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(date_created, '%m-01-%Y') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s ORDER BY date DESC", $formId), ARRAY_A);
				}
				if ($formId && $typeId) {
					$count = $wpdb->get_results("SELECT COUNT(*) AS total_requests, SUM(is_unique) AS unique_requests, DATE_FORMAT(date_created, '%m-01-%Y') AS date FROM {$wpdb->prefix}cfs_statistics WHERE ".$wpdb->prepare(" form_id = %s AND type = %s ORDER BY date DESC", $formId, $typeId), ARRAY_A);
				}
			break;
		}
		return $count;
	}
	public function getAllForFormSorted($id, $params = array()) {
		$allTypes = $this->getModule()->getTypes();
		$allStats = array();
		$haveData = false;
		$i = 0;
		foreach($allTypes as $typeCode => $type) {
			$params['type'] = $type['id'];
			$allStats[ $i ] = $type;
			$allStats[ $i ]['code'] = $typeCode;
			$allStats[ $i ]['points'] = $this->getForForm($id, $params);
			if(!empty($allStats[ $i ]['points'])) {
				$haveData = true;
			}
			$i++;
		}
		$allStats = dispatcherCfs::applyFilters('formStatsSorted', $allStats, $id, $params);
		$res = !empty($haveData) ? $allStats : false;
		return $res;
	}
	public function getStats( $d ) {
		$chartId = isset($d['chart_id']) ? $d['chart_id'] : false;
		$group = isset($d['group']) ? $d['group'] : false;
		$id = isset($d['id']) ? (int) $d['id'] : false;
		switch($chartId) {
			case 'cfsMainStats':
			$stats = $this->getAllForFormSorted($id, array('group' => $group));
			return $stats;
			case 'cfsRatingStats':
				return frameCfs::_()->getModule('add_fields') && frameCfs::_()->getModule('add_fields')->getModel('rating')
					? frameCfs::_()->getModule('add_fields')->getModel('rating')->getStatsForFormSorted($id, array('group' => $group))
					: array();
		}
		return false;
	}
	public function clearForForm($d = array()) {
		$d['id'] = isset($d['id']) ? (int) $d['id'] : 0;
		if($d['id']) {
			frameCfs::_()->getModule('forms')->getModel()->clearCachedStats( $d['id'] );
			return $this->delete(array('form_id' => $d['id']));
		} else
			$this->pushError(__('Invalid ID', CFS_LANG_CODE));
		return false;
	}
	public function getAllForFormId($id, $params = array()) {
		$allTypes = $this->getModule()->getTypes();
		$allStats = array();
		$haveData = false;
		$i = 0;
		foreach($allTypes as $typeCode => $type) {
			$params['type'] = $type['id'];
			$allStats[ $i ] = $type;
			$allStats[ $i ]['code'] = $typeCode;
			$allStats[ $i ]['points'] = $this->getForForm($id, $params);
			if(!empty($allStats[ $i ]['points'])) {
				$haveData = true;
			}
			$i++;
		}
		return $haveData ? $allStats : false;
	}
	public function getUpdatedStats($d = array()) {
		$id = isset($d['id']) ? (int) $d['id'] : 0;
		if($id) {
			$form = frameCfs::_()->getModule('forms')->getModel()->supGetById( $id );
			$params = array();
			if(isset($d['group']))
				$params['group'] = $d['group'];
			$allStats = $this->getAllForFormId($id, $params);
			$allStats = dispatcherCfs::applyFilters('formStatsAdminData', $allStats, $form);
			return $allStats;
		} else
			$this->pushError (__('Invalid ID', CFS_LANG_CODE));
		return false;
	}
	public function getPreparedStats($d = array()) {
		$stats = $this->getUpdatedStats( $d );
		if($stats) {
			$dataToDate = array();
			foreach($stats as $i => $stat) {
				if(isset($stat['points']) && !empty($stat['points'])) {
					foreach($stat['points'] as $j => $point) {
						$date = $point['date'];
						$currentData = array(
							'date' => $date,
							'views' =>  0,
							'unique_requests' => 0,
							'actions' => 0,
							'conversion' => 0,
						);
						if(in_array($stat['code'], array('show'))) {
							$currentData['views'] = (int)( $point['total_requests'] );
						} elseif(!in_array($stat['code'], array('submit', 'submit_error'))) {	// Do not count empty submits here
							$currentData['actions'] = (int)( $point['total_requests'] );
						}
						$uniqueRequests = (int)( $point['unique_requests'] );
						if($uniqueRequests) {
							$currentData['unique_requests'] = $uniqueRequests;
						}
						if(isset($dataToDate[ $date ])) {
							$currentData['views'] += $dataToDate[ $date ]['views'];
							$currentData['actions'] += $dataToDate[ $date ]['actions'];
							$currentData['unique_requests'] += $dataToDate[ $date ]['unique_requests'];
						}
						$dataToDate[ $date ] = $currentData;
					}
				}
			}
			return $dataToDate;
		} else
			$this->pushError (__('No data found', CFS_LANG_CODE));
		return false;
	}
}
