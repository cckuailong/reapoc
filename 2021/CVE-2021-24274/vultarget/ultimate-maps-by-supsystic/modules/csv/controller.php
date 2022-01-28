<?php
class csvControllerUms extends controllerUms {
	public function exportMaps() {
		$data = reqUms::get('get');
		$delimiter = !empty($data['delimiter']) ? $data['delimiter'] : ';';
		$fileDate = str_replace(array('/', '.', ':'), '_', date(UMS_DATE_FORMAT_HIS));
		$fileName = sprintf(__('Maps from %s - %s', UMS_LANG_CODE), get_bloginfo('name'), $fileDate);
		$maps = frameUms::_()->getModule('maps')->getModel()->getAllMaps(array());	// Only maps data
		if(empty($maps)) {
			_e('You have no maps for now.', UMS_LANG_CODE);
			exit();
		}

		// Remove unneeded values
		foreach($maps as $key => $val) {
			unset($maps[$key]['original_id']);
			unset($maps[$key]['view_id']);
			unset($maps[$key]['view_html_id']);
			unset($maps[$key]['params']['view_id']);
			unset($maps[$key]['params']['view_html_id']);
			unset($maps[$key]['params']['id']);
		}

		$keys = $this->_getKeys($maps[0]);
		$c = $r = 0;
		$this->_connectCsvLib();
		$csvGenerator = toeCreateObjUms('csvgeneratorUms', array($fileName));
		$csvGenerator->setDelimiter($delimiter);
		foreach($keys as $k) {
			$csvGenerator->addCell($r, $c, $k);
			$c++;
		}
		$c = 0;
		$r = 1;
		foreach($maps as $map) {
			$c = 0;
			foreach($keys as $k) {
				$mapValue = $this->_prepareValueToExport( $this->_getKeyVal($map, $k) );
				if(is_array($mapValue)) {
					$mapValue = implode(';', $mapValue);
				}
				$csvGenerator->addCell($r, $c, $mapValue);
				$c++;
			}
			$r++;
		}
		$csvGenerator->generate();
		frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('csv.export.maps');
		exit();
	}
	public function exportMarkers() {
		$data = reqUms::get('get');
		$delimiter = !empty($data['delimiter']) ? $data['delimiter'] : ';';
		$fileSiteDate = str_replace(array('/', '.', ':'), '_', esc_html(get_bloginfo('name')). ' - '. date(UMS_DATE_FORMAT_HIS));
		$fileName = sprintf(__('Markers from %s', UMS_LANG_CODE), $fileSiteDate);
		$markers = frameUms::_()->getModule('marker')->getModel()->getAllMarkers();
		if(empty($markers)) {
			_e('You have no markers for now.', UMS_LANG_CODE);
			exit();
		}
		$this->_connectCsvLib();
		$csvGenerator = toeCreateObjUms('csvgeneratorUms', array($fileName));
		if(!empty($data['delimiter'])) {
			$csvGenerator->setDelimiter($delimiter);
		}
		$c = $r = 0;
		$keys = array('id', 'map_id', 'title', 'description', 'coord_x', 'coord_y',);
		$marker_keys = array();
		foreach($markers as $m) {
			$marker_keys = array_unique(array_merge($marker_keys, $this->_getKeys($m)));
		}
		sort($marker_keys);
		$keys = array_unique(array_merge($keys, $marker_keys));
		foreach($keys as $k) {
			$csvGenerator->addCell($r, $c, $k);
			$c++;
		}
		$c = 0;
		$r = 1;
		foreach($markers as $marker) {
			$c = 0;
			foreach($keys as $k) {
				$markerValue = $this->_prepareValueToExport( $this->_getKeyVal($marker, $k) );
				$csvGenerator->addCell($r, $c, $markerValue);
				$c++;
			}
			$r++;
		}
		$csvGenerator->generate();
		frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('csv.export.markers');
		exit();
	}
	public function import() {
		$data = reqUms::get('post');
		@ini_set('auto_detect_line_endings', true);
		$res = new responseUms();
		$this->_connectCsvLib();
		$csvGenerator = toeCreateObjUms('csvgeneratorUms', array(''));
		$type = reqUms::getVar('type');
        $file = $type == 'maps' ? reqUms::getVar('csv_import_file_maps', 'file') : reqUms::getVar('csv_import_file_markers', 'file');
        if(empty($file) || empty($file['size']))
            $res->pushError (__('Missing File', UMS_LANG_CODE));
        if(!empty($file['error']))
            $res->pushError (sprintf(__('File uploaded with error code %s', $file['error'])));
        if(!$res->error()) {
            $fileArray = array();
			$handle = fopen($file['tmp_name'], 'r');
			$csvParams['delimiter'] = !empty($data['delimiter']) ? $data['delimiter'] : $csvGenerator->getDelimiter();
			$csvParams['enclosure'] = $csvGenerator->getEnclosure();
			$csvParams['escape'] = $csvGenerator->getEscape();
			//if(version_compare( phpversion(), '5.3.0' ) == -1) //for PHP lower than 5.3.0 third parameter - escape - is not implemented
				while($row = @fgetcsv( $handle, 0, $csvParams['delimiter'], '"' )) $fileArray[] = $row;
			/*else
				while($row = @fgetcsv( $handle, 0, $csvParams['delimiter'], $csvParams['enclosure'], $csvParams['escape'] )) $fileArray[] = $row;*/
			/*var_dump($fileArray);
			exit();*/
			if(!empty($fileArray)) {
				if(count($fileArray) > 1) {
					//$overwriteSameNames = (int) reqUms::getVar('overwrite_same_names');
					$keys = array_shift($fileArray);
					switch($type) {
						case 'maps':
							$mapModel = frameUms::_()->getModule('maps')->getModel();
							foreach($fileArray as $i => $row) {
								$map = array();
								foreach($keys as $j => $key) {
									$value = $this->_prepareValueToImport($row[ $j ]);
									if(strpos($key, '.')) {
										$realKeys = explode('.', $key);
										$realKey = array_pop( $realKeys );
										$realPreKey = array_pop( $realKeys );
										if($realPreKey == 'map_center') {
											$valueMapCenter = isset($map['map_center']) ? $map['map_center'] : array();
											$valueMapCenter[ $realKey ] = $value;
											$value = $valueMapCenter;
											$realKey = 'map_center';
										}
									} else
										$realKey = $key;
									if($value === '')
										$value = NULL;
									$map[ $realKey ] = $value;
								}
								if(isset($map['id']) && $mapModel->existsId($map['id'])) {
									$mapModel->updateMap($map);
								} else {
									$originalMapId = isset($map['id']) ? $map['id'] : 0;
									if(isset($map['id']))
										unset($map['id']);
									$newMapId = $mapModel->saveNewMap($map);
									if($newMapId && $originalMapId) {
										global $wpdb;
										$wpdb->query($wpdb->prepare( "UPDATE {$wpdb->prefix}ums_maps SET id = %d WHERE id = %d", $originalMapId, $newMapId));
										if($originalMapId > $newMapId) {
											$newVar = $originalMapId + 1;
											$wpdb->query($wpdb->prepare( "ALTER TABLE {$wpdb->prefix}ums_maps AUTO_INCREMENT = %s", $newVar) );
										}
									}
								}
							}
							break;
						case 'markers':
							$markerModel = frameUms::_()->getModule('marker')->getModel();
							foreach($fileArray as $i => $row) {
								$marker = array();
								foreach($keys as $j => $key) {
									$this->_setKeyVal($marker, $key, $this->_prepareValueToImport($row[ $j ]));
								}
								if(isset($marker['id']) && !$markerModel->existsId($marker['id'])) {
									unset($marker['id']);
								}
								$markerModel->save($marker);
							}
							break;
					}
					/*$importRes = $this->getModel()->import($fileArray, $overwriteSameNames);
					if($importRes) {
						if($importRes['map']['added'])
							$res->addMessage (sprintf(__('Added %s maps', UMS_LANG_CODE), $importRes['map']['added']));
						if($importRes['map']['updated'])
							$res->addMessage (sprintf(__('Updated %s maps', UMS_LANG_CODE), $importRes['map']['added']));
						if($importRes['marker']['added'])
							$res->addMessage (sprintf(__('Added %s markers', UMS_LANG_CODE), $importRes['map']['added']));
						if($importRes['marker']['updated'])
							$res->addMessage (sprintf(__('Updated %s markers', UMS_LANG_CODE), $importRes['map']['added']));
					} else
						$res->pushError ($this->getModel()->getErrors());*/
				} else
					$res->pushError (__('File should contain more then 1 row, at least 1 row should be for headers', UMS_LANG_CODE));
			} else
				$res->pushError (__('Empty data in file', UMS_LANG_CODE));
		}
		frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('csv.import');
		$res->ajaxExec();
	}
	public function saveCsvOptions() {
		$res = new responseUms();
		if(frameUms::_()->getModule('options')->getModel()->saveGroup(reqUms::get('post'))) {
			$res->addMessage(__('Done', UMS_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	private function _connectCsvLib() {
		importClassUms('filegeneratorUms');
		importClassUms('csvgeneratorUms');
	}
	private function _getSitePath() {
		return $this->getModel()->getSitePath();
	}
	private function _getKeys($data, $prefix = array()) {
		$keys = array();
		foreach($data as $k => $v) {
			if(is_array($v)) {
				$newPrefix = $prefix;
				$newPrefix[] = $k;
				$keys = array_merge($keys, $this->_getKeys($v, $newPrefix));
			} else {
				$keys[] = empty($prefix) ? $k : implode('.', $prefix). '.'. $k;
			}
		}
		return $keys;
	}
	private function _getKeyVal($data, $key) {
		if(strpos($key, '.')) {
			$keys = explode('.', $key);
			$firstKey = array_shift($keys);
			return isset($data[ $firstKey ]) ? $this->_getKeyVal($data[ $firstKey ], implode('.', $keys)) : '';
		} else {
			return isset($data[ $key ]) ? $data[ $key ] : '';
		}
	}
	private function _setKeyVal(&$data, $key, $val) {
		if(strpos($key, '.')) {
			$keys = explode('.', $key);
			$firstKey = array_shift($keys);
			if(!isset($data[ $firstKey ]))
				$data[ $firstKey ] = array();
			$this->_setKeyVal($data[ $firstKey ], implode('.', $keys), $val);
		} else {
			$data[ $key ] = $val;
		}
	}
	private function _prepareValueToExport($val) {
		$sitePath = $this->_getSitePath();
		return htmlspecialchars(str_replace($sitePath, '[UMS_SITE_PATH]', $val));
	}
	private function _prepareValueToImport($val) {
		$sitePath = $this->_getSitePath();
		return str_replace('[UMS_SITE_PATH]', $sitePath, htmlspecialchars_decode(addslashes($val)));
	}
	private function _toYesNo($val) {
		return empty($val) ? 'No' : 'Yes';
	}
	private function _fromYesNo($val) {
		return $val === 'No' ? 0 : 1;
	}

	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			UMS_USERLEVELS => array(
				UMS_ADMIN => array('exportMaps', 'exportMaps', 'import', 'saveCsvOptions')
			),
		);
	}
}
