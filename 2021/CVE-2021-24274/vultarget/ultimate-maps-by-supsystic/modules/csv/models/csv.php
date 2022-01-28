<?php
class csvModelUms extends modelUms {
	private $_sitePath = '';
	public function getSitePath() {
		if(!$this->_sitePath) {
			$this->_sitePath = get_bloginfo('wpurl');
		}
		return $this->_sitePath;
	}
	private function _detectMarkerGoupId($marker) {
		if(!empty($marker['marker_group_id'])) {
			$groupByTitle = frameUms::_()->getModule('marker_groups')->getModel()->getGroupByTitle($marker['marker_group_title']);
			if($groupByTitle) {
				return $groupByTitle['id'];
			} else {
				return frameUms::_()->getModule('marker_groups')->getModel()->saveGroup(array(
					'title' => $marker['marker_group_title'],
					'description' => $marker['marker_group_description'],
				));
			}
		}
		return 0;
	}
	private function _beforeInsert($field) {
		$field = trim(htmlspecialchars_decode($field));
		// Remove double quotes from start and end of the value
		if(strpos($field, '"') === 0) {
			$field = substr($field, 1, strlen($field));
			$field = substr($field, 0, strlen($field)-1);
		}
		$field = str_replace('[UMS_SITE_PATH]', $this->getSitePath(), $field);
		return $field;
	}
	public function import($fileArray, $overwriteSameNames = 1) {
		$inputFields = $this->_getFieldsListFromInput($fileArray);
		if(!empty($inputFields)) {
			if($importType = $this->_detectImportType($inputFields)) {
				$counters = array('map' => array(), 'marker' => array());
				$counters['map'] = $counters['marker'] = array('added' => 0, 'updated' => 0);
				$lastMapId = 0;
				//var_dump($inputFields);
				for($i = 1; $i < count($fileArray); $i++) {	// $i = 1 - we will ignore first (0) row - as it is titles
					$addData = array();
					$res = false;
					for($j = 0; $j < count($fileArray[$i]); $j++) {
						/*$addData[ $inputFields[$j] ] = trim(htmlspecialchars_decode($fileArray[$i][$j]));
						// Remove double quotes from start and end of the value
						if(strpos($addData[ $inputFields[$j] ], '"') === 0) {
							$addData[ $inputFields[$j] ] = substr($addData[ $inputFields[$j] ], 1, strlen($addData[ $inputFields[$j] ]));
							$addData[ $inputFields[$j] ] = substr($addData[ $inputFields[$j] ], 0, strlen($addData[ $inputFields[$j] ])-1);
						}*/
						$addData[ $inputFields[$j] ] = $this->_beforeInsert($fileArray[$i][$j]);
					}
					$currentImporType = $importType;
					if($importType == 'map_marker') {
						$countMap = $countMarker = 0;
						foreach($addData as $fieldKey => $fieldData) {
							if(strpos($fieldKey, 'for_marker_') === 0) {
								$countMarker++;
							} else {
								$countMap++;
							}
						}
						//var_dump($countMarker, $countMap, $addData);
						$countMarker ? $currentImporType = 'marker' : $currentImporType = 'map';
						//var_dump($currentImporType);
						if($currentImporType == 'marker') {
							$newAddData = array();
							foreach($addData as $fieldKey => $fieldVal) {
								$newAddData[ str_replace('for_marker_', '', $fieldKey) ] = $fieldVal;
							}
							$addData = $newAddData;
							$addData['map_id'] = $lastMapId;
							$addData['group_id'] = $this->_detectMarkerGoupId($addData);
						}
					}
					/*var_dump($addData);
					exit();*/
					switch($currentImporType) {
						case 'marker':
							if(isset($addData['id'] ))
								unset($addData['id']);
							$updateId = 0;
							if($overwriteSameNames) {
								$marker = frameUms::_()->getModule('marker')->getModel()->getMarkerByTitle($addData['title']);
								if($marker && $marker['id']) {
									$updateId = $marker['id'];
								}
							}
							if($updateId) {
								$addData['id'] = $updateId;
							}
							$res = frameUms::_()->getModule('marker')->getModel()->save($addData);
							if($res) {
								$updateId ? $counters['marker']['updated']++ : $counters['marker']['added']++;
							}
							break;
						case 'map':

							// Fix empty map after import with invalid "map_center" value.
							if (array_key_exists('map_center', $addData)) {
								$center = explode(',', $addData['map_center']);

								$addData['map_center'] = array(
									'coord_x' => trim($center[0]),
									'coord_y' => trim($center[1]),
								);
							}

							$updateId = 0;
							if($overwriteSameNames) {
								$map = frameUms::_()->getModule('maps')->getModel()->getMapByTitle($addData['title']);
								if($map && $map['id']) {
									$updateId = $map['id'];
								}
							}
							if($updateId) {
								$addData['id'] = $updateId;
								$lastMapId = $updateId;
								$res = frameUms::_()->getModule('maps')->getModel()->updateMap($addData);
							} else {
								$res = frameUms::_()->getModule('maps')->getModel()->saveNewMap($addData);
								$lastMapId = $res;
							}
							if($res) {
								$updateId ? $counters['map']['updated']++ : $counters['map']['added']++;
							}
							break;
					}
				}
				return $counters;
			} else
				$this->pushError (__('Can not detect import list type', UMS_LANG_CODE));
		} else
			$this->pushError (__('Can not find fields names', UMS_LANG_CODE));
		return false;
	}
	public function importMaps($fileArray, $inputFields) {
		
	}
	public function importMarkers($fileArray) {
		
	}
	private function _getFieldsListFromInput($fileArray) {
		$fields = array();
		foreach($fileArray[0] as $headLabel) {
			$field = $this->_getFieldNameFromStr($headLabel);
			if($field)
				$fields[] = $field;
		}
		return $fields;
	}
	private function _getFieldNameFromStr($str) {
		$str = trim($str);
		preg_match('/\[(?<key>.+)\]/i', $str, $matches);
		if($matches && isset($matches['key']) && !empty($matches['key'])) {
			$key = trim($matches['key']);
			if(strpos($str, 'Marker - ') === 0)
				$key = 'for_marker_'. $key;
			return $key;
		}
		return false;
	}
	private function _detectImportType($inputFields) {
		$markerInputFields = $this->getModule()->getMarkerHeadersList();
		$mapInputFields = $this->getModule()->getMapHeadersList();
		
		$intersectWithMarkers = array_intersect($inputFields, array_keys($markerInputFields));
		$intersectWithMaps = array_intersect($inputFields, array_keys($mapInputFields));
		foreach($inputFields as $key) {
			if(strpos($key, 'for_marker') === 0)
				return 'map_marker';
		}
		if(count($intersectWithMarkers) == 17 && count($intersectWithMaps) == 21)
			return 'map_marker';
		if(count($intersectWithMarkers) > count($intersectWithMaps)) {
			return 'marker';
		} elseif(count($intersectWithMarkers) < count($intersectWithMaps)) {
			return 'map';
		}
		return false;
	}
}
