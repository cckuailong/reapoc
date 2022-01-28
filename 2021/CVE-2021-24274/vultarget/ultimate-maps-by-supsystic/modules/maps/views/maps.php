<?php
class mapsViewUms extends viewUms {
	private static $_mapsData;
	private $_mapsObj = array();
	private $_shortCodeHtmlParams = array('width', 'height', 'align');
	private $_paramsCanNotBeEmpty = array('width', 'height');
	private $_mapStyles = array();
	private $_displayColumns = array();

	private $_mapsApiUrls = array();

	public function getApiKey($engine) {
		$apiKey = '';

		if($userApiKey = dispatcherUms::applyFilters('rewriteApiKey', frameUms::_()->getModule('options')->get($engine. '_key'))) {
			$apiKey = trim($userApiKey);
		}
		return $apiKey;
	}
	private function _getMapsEngine($engine) {
		if(strpos($engine, 'l-') === 0) {
			$engine = 'leaflet';
		}
		return $engine;
	}
	// Public alias
	public function getMapsEngine($engine) {
		return $this->_getMapsEngine($engine);
	}
	private function _getMapsFullEngine($engine) {
		return $engine;
	}
	private function _getApiUrl($engine) {
		if(!isset($this->_mapsApiUrls[$engine])) {
			$apiUrl = '';
			switch($engine) {
				case 'bing':
					$apiUrl = 'https://www.bing.com/mapspreview/sdk/mapcontrol/?callback=umsBingMapsLoadComplete';
					break;
				case 'leaflet':
				  $apiUrl = frameUms::_()->getModule('maps')->getModPath(). 'js/leaflet.js';
					break;
				/*case 'mapbox':
					//$apiUrl = 'https://api.mapbox.com/mapbox.js/v3.1.1/mapbox.js';
					$apiUrl = 'https://api.tiles.mapbox.com/mapbox-gl-js/v0.49.0/mapbox-gl.js';
					break;*/
			}
			$urlParams = dispatcherUms::applyFilters('apiUrlParams', array('key' => $this->getApiKey($engine)));
			$this->_mapsApiUrls[$engine] = $apiUrl. (strpos($apiUrl, '?') === false ? '?' : '&'). http_build_query($urlParams);
		}
		return $this->_mapsApiUrls[$engine];
	}
	private function _connectApiAssets($engine, $fullEngine) {
		$assetPrimaryName = 'ums_'. $engine. '_maps_api';
		switch($engine) {
			case 'leaflet':
				frameUms::_()->addStyle($assetPrimaryName, frameUms::_()->getModule('maps')->getModPath(). 'css/leaflet.css');
				break;
		}
		frameUms::_()->addScript($assetPrimaryName, $this->_getApiUrl($engine));
		switch($engine) {
			case 'leaflet':
				$fullEngineSlugName = explode('-', $fullEngine);
				$leafletTypesName = 'umsLeaFletTypes__'. (isset($fullEngineSlugName[ 1 ]) ? $fullEngineSlugName[ 1 ] : $fullEngineSlugName[ 0 ]);
				frameUms::_()->addJSVar($assetPrimaryName, $leafletTypesName, $this->_getMapLeaFletTypes($fullEngine));
				break;
		}
	}
	private function _connectMarkerClusterAssets($engine) {
		switch($engine) {
			case 'leaflet':
				frameUms::_()->addStyle('ums_'. $engine. '_markercluster_api', frameUms::_()->getModule('maps')->getModPath(). 'js/assets/MarkerCluster.css');
				frameUms::_()->addStyle('ums_'. $engine. '_markercluster_def_api', frameUms::_()->getModule('maps')->getModPath(). 'js/assets/MarkerCluster.Default.css');
				frameUms::_()->addScript('ums_'. $engine. '_markercluster_api', frameUms::_()->getModule('maps')->getModPath(). 'js/assets/leaflet.markercluster.js');
				break;
		}
	}
	public function addMapData($params){
		if(empty(self::$_mapsData)) {
			self::$_mapsData = array();
		}
		if(!empty($params))
			self::$_mapsData[] = $params;
	}
	public function getMapData(){
		return self::$_mapsData;
	}
	public function getMapsObj() {
		if(empty($this->_mapsObj)) {
			$mapsInPosts = $this->getModule()->getMapsInPosts();

			foreach($mapsInPosts as $mapId) {
				$mapObj = frameUms::_()->getModule('maps')->getModel()->getMapById($mapId);

				if(empty($mapObj)) continue;

				$mapObj['isDisplayed'] = false;
				$this->_mapsObj[$mapObj['view_id']] = $mapObj;
			}
		}
		return $this->_mapsObj;
	}
	public function addMapStyles($mapViewId) {
		$mapObj = is_array($mapViewId) ? $mapViewId : $this->_mapsObj[$mapViewId];
		$mapsInPostsParams = $this->getModule()->_mapsInPostsParams;

		if(!empty($mapsInPostsParams) && !empty($mapsInPostsParams[$mapObj['view_id']])) {
			$mapObj = $this->applyShortcodeHtmlParams($mapObj, $mapsInPostsParams[$mapObj['view_id']]);
		}
		$this->assign('currentMap', $mapObj);
		array_push($this->_mapStyles, $mapObj['view_id']);

		return parent::getContent('mapsMapStyles');
	}
	public function drawMap($params) {
		$mapObj = array();
		$mapMarkersGroupsList = array();

		foreach($this->_mapsObj as $view_id => $map) {
			if($map['id'] == $params['id'] && !$map['isDisplayed']) {
				$this->_mapsObj[$view_id]['isDisplayed'] = true;
				$mapObj = $this->_mapsObj[$view_id];
				break;
			}
		}
		$mapObj = $mapObj ? $mapObj : frameUms::_()->getModule('maps')->getModel()->getMapById($params['id']);

		if(empty($mapObj)){
			return isset($params['id'])
				? sprintf(__('Map with ID %d not found', UMS_LANG_CODE), $params['id'])
				: __('Map not found', UMS_LANG_CODE);
		}
		$mapObj = $this->applyShortcodeHtmlParams($mapObj, $params);
		$mapObj = $this->applyShortcodeMapParams($mapObj, $params);

		if(isset($params['plugin-info']) && $params['plugin-info'] == 'Membership-by-Supsystic' && isset($params['membership-params'])) {
			$membershipModule = frameUms::_()->getModule('membership');
			if($membershipModule) {
				$membershipModel = $membershipModule->getModel('membership_presets');
				if($membershipModel) {
					$params = $membershipModel->prepareParamsWithMarkers($params);
					$membershipModel->replaceMapsParamsForMembership($mapObj, $params);
				}
			}
		}
		if(!empty($mapObj['markers'])) {
			if(!empty($params['marker_show_description'])) {
				foreach($mapObj['markers'] as $key => $marker) {
					if(isset($marker['params']['show_description'])) {
						unset($mapObj['markers'][$key]['params']['show_description']);
					}
					if($marker['id'] == $params['marker_show_description']) {
						$mapObj['markers'][$key]['params']['show_description'] = 1;
					}
				}
			}

			if(!empty($params['marker_category'])) {
				$category = explode(',', $params['marker_category']);
				foreach($mapObj['markers'] as $key => $marker) {
					if( count( array_intersect($marker['marker_group_ids'], $category) ) > 0){
						continue;
					}
					unset($mapObj['markers'][$key]);

				}
				$mapObj['markers'] = array_values($mapObj['markers']);	// 'reindex' array
			}
		}
		if(empty($mapObj['params']['map_display_mode'])){
			$mapObj['params']['map_display_mode'] = 'map';
		}
		if($mapMarkersGroupsList) {
			$mapObj['marker_groups'] = frameUms::_()->getModule('marker_groups')->getModel()->getMarkerGroupsByIds($mapMarkersGroupsList);
		}

		if (isset($mapObj['params']['markers_list_type']) && !empty($mapObj['params']['markers_list_type'])) {
            frameUms::_()->getModule('templates')->loadFontAwesome();
        }

		$mapObj['params']['markers_list_type'] = isset($params['markers_list_type']) ? $params['markers_list_type'] : ( (isset($mapObj['params']['markers_list_type']) && !empty($mapObj['params']['markers_list_type']))
				? $mapObj['params']['markers_list_type']
				: '');
		$mapObj = dispatcherUms::applyFilters('mapDataRender', $mapObj);
		$mapObj['params']['ss_html'] = $this->generateSocialSharingHtml($mapObj);

		$this->connectMapsAssets( $mapObj );

		// for Membership activity Map add window
		if(!empty($params['membership-integrating'])) {
			$this->assign('mbsIntegrating', $params['id']);
			$mapObj['mbs_presets'] = 1;
		}
		// for Membership activity draw post
		if(!empty($params['membership-id'])) {
			$this->assign('mbsMapId', $params['membership-id']);
			if(!empty($params['membership-params'])) {
				$this->assign('mbsMapInfo', json_encode($params['membership-params']));
			}
			$mapObj['mbs_created'] = 1;
		}

		frameUms::_()->addScript('frontend.maps', $this->getModule()->getModPath(). 'js/frontend.maps.js', array('jquery'), false, true);
		$this->addMapData(dispatcherUms::applyFilters('mapDataToJs', $mapObj));

		$this->assign('markersDisplayType', $mapObj['params']['markers_list_type']);
		$this->assign('currentMap', $mapObj);
		$res = '';
		if(!in_array($mapObj['view_id'], $this->_mapStyles)) {
			$res .= $this->addMapStyles($mapObj);
		}
		return ($res. parent::getInlineContent('mapsDrawMap'));
	}
	public function applyShortcodeHtmlParams($mapObj, $params){
		//Add support shortcode data-tables-by-supsystic and other shortcodes START
		$address = $mapObj['params']['map_center']['address'];
		if ( strpos($address, '[') !== false && strpos($address, ']') !== false ) {
			ob_start();
			$address = do_shortcode($address);
			ob_get_clean();
			if (strpos($address, 'supsystic-table') !== false) {
				preg_match('/data-original-value="(.*?)"/', $address, $match);
				$address = !empty($match[1]) ? $match[1] : '';
			}
			$mapObj['params']['map_center']['address'] = $address;
			$remoteUrl = 'https://nominatim.openstreetmap.org/search?q='.$address.'&format=json&polygon=0&addressdetails=1';
			$remoteGet = wp_remote_get( $remoteUrl );
			if (!empty($remoteGet) && !is_wp_error($remoteGet) && !empty($remoteGet['body'])) {
				$remoteGet = json_decode($remoteGet['body']);
				$addressLat = !empty($remoteGet[0]->lat) ? $remoteGet[0]->lat : '';
				$addressLon = !empty($remoteGet[0]->lon) ? $remoteGet[0]->lon : '';
				$mapObj['params']['map_center']['coord_x'] = $addressLat;
				$mapObj['params']['map_center']['coord_y'] = $addressLon;
			}
		}
		//Add support shortcode data-tables-by-supsystic and other shortcodes END
		foreach($this->_shortCodeHtmlParams as $code) {
			if(isset($params[$code])){
				if(in_array($code, $this->_paramsCanNotBeEmpty) && empty($params[$code])) continue;
				$mapObj['html_options'][$code] = $params[$code];
			}
		}
		return $mapObj;
	}
	public function applyShortcodeMapParams($mapObj, $params){
		$shortCodeMapParams = $this->getModel()->getParamsList();

		if(isset($params['map_center']) && is_string($params['map_center'])) {
			if(strpos($params['map_center'], ';')) {
				$centerXY = array_map('trim', explode(';', $params['map_center']));
				$params['map_center'] = array(
					'coord_x' => $centerXY[0],
					'coord_y' => $centerXY[1],
				);
			} elseif(is_numeric($params['map_center'])) {	// Map center - is coords of one of it's marker
				$params['map_center'] = (int) trim($params['map_center']);
				$found = false;

				if(!empty($mapObj['markers'])) {
					foreach($mapObj['markers'] as $marker) {
						if($marker['id'] == $params['map_center']) {
							$params['map_center'] = array(
								'coord_x' => $marker['coord_x'],
								'coord_y' => $marker['coord_y'],
							);
							$found = true;
							break;
						}
					}
				}
				// If no marker with such ID were found - just unset it to prevent map broke
				if(!$found) {
					unset($params['map_center']);
				}
			} else {
				// If it is set, but not valid - just unset it to not break user map
				unset($params['map_center']);
			}
		}
		foreach($shortCodeMapParams as $code){
			if(isset($params[$code])) {
				if(in_array($code, $this->_paramsCanNotBeEmpty) && empty($params[$code])) continue;
				$mapObj['params'][$code] = $params[$code];
			}
		}
		return $mapObj;
	}
	public function addMapDataToJs(){
		frameUms::_()->addJSVar('frontend.maps', 'umsAllMapsInfo', self::$_mapsData);
	}
	public function getDisplayColumns() {
		if(empty($this->_displayColumns)) {
			$this->_displayColumns = array(
				'id'				=> array('label' => __('ID'), 'db' => 'id'),
				'title'				=> array('label' => __('Title'), 'db' => 'title'),
				'list_html_options'	=> array('label' => __('Html options'), 'db' => 'html_options'),
				'list_markers'		=> array('label' => __('Markers'), 'db' => 'markers'),
				'operations'		=> array('label' => __('Operations'), 'db' => 'operations'),
			);
		}
		return $this->_displayColumns;
	}
	public function getListMarkers($map) {
		$this->assign('map', $map);
		return parent::getContent('mapsListMarkers');
	}
	public function getListOperations($map) {
		$this->assign('map', $map);
		$this->assign('editLink', $this->getModule()->getEditMapLink( $map['id'] ));
		return parent::getContent('mapsListOperations');
	}
	public function getTabContent() {
		frameUms::_()->getModule('templates')->loadJqGrid();
		frameUms::_()->addScript('admin.maps.list', $this->getModule()->getModPath(). 'js/admin.maps.list.js');
		frameUms::_()->addJSVar('admin.maps.list', 'umsTblDataUrl', uriUms::mod('maps', 'getListForTbl', array('reqType' => 'ajax')));
		frameUms::_()->addStyle('admin.maps', $this->getModule()->getModPath(). 'css/admin.maps.css');

		$this->assign('addNewLink', frameUms::_()->getModule('options')->getTabUrl('maps_add_new'));
		return parent::getContent('mapsAdmin');
	}
	public function getEditMap($id = 0) {
		$editMap = $id ? true : false;
		$isPro = frameUms::_()->getModule('supsystic_promo')->isPro();

		// TODO: Add possibility to get engine - from map params
		$engine = $defEngine = frameUms::_()->getModule('options')->get('def_engine');
		/*$engineFromReq = reqUms::getVar('ums_engine');
		if(!empty($engineFromReq)) {
			$defEngine = $engineFromReq;
		}*/
		//$gMapApiParams = array('language' => '');
		$markerLists = $this->getModule()->getMarkerLists();
		$positionsList = $this->getModule()->getControlsPositions();
		$isContactFormsInstalled = utilsUms::classExists('frameCfs');

		$allStylizationsList = $this->getModule()->getStylizationsList();
		$stylizationsForSelect = array('none' => __('None', UMS_LANG_CODE),);

		foreach($allStylizationsList as $styleName => $json) {
			$stylizationsForSelect[ $styleName ] = $styleName;	// JSON data will be attached on js side
		}
		frameUms::_()->getModule('templates')->loadJqGrid();
		frameUms::_()->addScript('jquery-ui-sortable');
		frameUms::_()->addScript('wp.tabs', UMS_JS_PATH. 'wp.tabs.js');
		frameUms::_()->addScript('admin.maps.edit', $this->getModule()->getModPath(). 'js/admin.maps.edit.js');
		frameUms::_()->addScript('admin.marker.edit', frameUms::_()->getModule('marker')->getModPath(). 'js/admin.marker.edit.js');

		frameUms::_()->addStyle('admin.maps', $this->getModule()->getModPath(). 'css/admin.maps.css');

		frameUms::_()->addJSVar('admin.maps.edit', 'umsMapShortcode', UMS_SHORTCODE);
		frameUms::_()->addJSVar('admin.maps.edit', 'umsAllStylizationsList', $allStylizationsList);
		frameUms::_()->addJSVar('admin.maps.edit', 'umsMapsListUrl', frameUms::_()->getModule('options')->getTabUrl('maps'));

		// jqGrid tables urls
		$umsMarkersTblDataUrl =  uriUms::mod('marker', 'getListForTbl', array('reqType' => 'ajax', 'map_id' => $id));
		frameUms::_()->addJSVar('admin.maps.edit', 'umsMarkersTblDataUrl', $umsMarkersTblDataUrl);
		frameUms::_()->addJSVar('admin.marker.edit', 'umsMarkersTblDataUrl', $umsMarkersTblDataUrl);

		/*if($isPro) {
			$umsShapesTblDataUrl =  uriUms::mod('shape', 'getListForTbl', array('reqType' => 'ajax', 'map_id' => $id));
			frameUms::_()->addJSVar('admin.maps.edit', 'umsShapesTblDataUrl', $umsShapesTblDataUrl);
			frameUms::_()->addJSVar('admin.shape.edit', 'umsShapesTblDataUrl', $umsShapesTblDataUrl);
		}*/
		$map = array();
		if($editMap) {
			$map = $this->getModel()->getMapById( $id );
			$engine = empty($map['engine']) ? $defEngine : $map['engine'];
			//$gMapApiParams = $map['params'];
			$mapMarkersGroupsList = array();
			$mapMarkersGroups = array();

			if($map['markers'] && !empty($map['markers'])) {
				foreach($map['markers'] as $marker) {
					if($marker['marker_group_id']) {
						if(in_array($marker['marker_group_id'], $mapMarkersGroupsList)) continue;
						array_push($mapMarkersGroupsList, $marker['marker_group_id']);
					}
				}
			}
			if($mapMarkersGroupsList) {
				$allMarkerGroupsList = frameUms::_()->getModule('marker_groups')->getModel()->getAllMarkerGroups();

				foreach($allMarkerGroupsList as $group) {
					if(in_array($group['id'], $mapMarkersGroupsList)) array_push($mapMarkersGroups, $group);
				}
				$map['marker_groups'] = $mapMarkersGroups;
			}

			$this->assign('map', $map);

			frameUms::_()->addJSVar('admin.maps.edit', 'umsMainMap', $map);
		} else {
			$engineFromReq = reqUms::getVar('ums_engine');
			if(!empty($engineFromReq)) {
				$this->assign('engineFromReq', $engineFromReq);
			}
		}

		$this->connectMapsAssets($map, true);

		$this->assign('engineOpts', $this->_getEngineOpts($engine));
		$this->assign('editMap', $editMap);
		$this->assign('isPro', $isPro);
		$this->assign('icons', frameUms::_()->getModule('icons')->getModel()->getIcons());
		$this->assign('countries', $this->getModule()->getCountriesList());
		$this->assign('stylizationsForSelect', $stylizationsForSelect);
		$this->assign('positionsList', $positionsList);
		$this->assign('mainLink', frameUms::_()->getModule('supsystic_promo')->getMainLink());
		$this->assign('markerLists', $markerLists);
		$this->assign('markerGroupsForSelect', frameUms::_()->getModule('marker_groups')->getModel()->getMarkerGroupsForSelect(array('0' => __('None', UMS_LANG_CODE),)));
		$this->assign('viewId', $editMap ? $map['view_id'] : 'preview_id_'. mt_rand(1, 9999));
		$this->assign('promoModPath', frameUms::_()->getModule('supsystic_promo')->getModPath());

		if($isContactFormsInstalled) {
			frameUms::_()->addJSVar('admin.maps.edit', 'umsContactFormEditUrl', frameCfs::_()->getModule('options')->getTabUrl('forms_edit'));
			$this->assign('contactFormsForSelect', $this->getAllContactForms());
		}
		$this->assign('isContactFormsInstalled', $isContactFormsInstalled);

		$membershipModule = frameUms::_()->getModule('membership');
		if($membershipModule) {
			$membershipModel = $membershipModule->getModel('membership_presets');
			if(!$membershipModel) {
				$this->assign('membershipPluginError', __('Error inside google maps plugin.', UMS_LANG_CODE));
			} elseif($membershipModel->isPluginActive() === null) {
				$this->assign('pluginInstallUrl', $membershipModel->getPluginInstallUrl());
			} elseif(!$membershipModel->isPluginActive()) {
				$this->assign('membershipPluginError', __('To use this feature, You need to reactivate your Ultimate Maps by Supsystic plugin.'), UMS_LANG_CODE );
			} else {
				$this->assign('canUseMembershipFeature', 1);
			}
		} else {
			$this->assign('membershipPluginError', __('To use this feature, You need to reactivate your Ultimate Maps by Supsystic plugin.'), UMS_LANG_CODE );
		}

		$this->assign('addProElementAttrs', $isPro ? '' : ' title="'. esc_html(__("This option is available in <a target='_blank' href='%s'>PRO version</a> only, you can get it <a target='_blank' href='%s'>here.</a>", UMS_LANG_CODE)). '"');
		$this->assign('addProElementClass', $isPro ? '' : 'supsystic-tooltip umsProOpt');
		$isCustSearchAndMarkersPeriodAvailable = true;
		if($isPro) {	// It's not available for old PRO
			$isCustSearchAndMarkersPeriodAvailable = false;
			if(frameUms::_()->getModule('custom_controls')
				&& method_exists(frameUms::_()->getModule('custom_controls'), 'isCustSearchAndMarkersPeriodAvailable')
				&& frameUms::_()->getModule('custom_controls')->isCustSearchAndMarkersPeriodAvailable()
			) {
				$isCustSearchAndMarkersPeriodAvailable = true;
			}
		}
		$this->assign('isCustSearchAndMarkersPeriodAvailable', $isCustSearchAndMarkersPeriodAvailable);
		$this->assign('enginesForSelect', $this->getModule()->getEnginesForSelect());
		$this->assign('defEngine', $defEngine);
		$this->assign('engine', $engine);

		$promoData = frameUms::_()->getModule('supsystic_promo')->addPromoMapTabs();
		//$this->assign('promoData', frameUms::_()->getModule('supsystic_promo')->addPromoMapTabs());

		$this->assign('tabs', array(
			'umsMapTab' => array(
				'label' => __('Map', UMS_LANG_CODE),
				'icon' => 'fa-globe',
				'content' => parent::getContent('mapsEditMap'),
			),
			'umsMarkerTab' => array(
				'label' => __('Markers', UMS_LANG_CODE),
				'icon' => 'fa-map-marker',
				'btns' => array(
					array('id' => 'umsAddNewMarkerBtn', 'label' => __('New', UMS_LANG_CODE)),
				),
				'content' => parent::getContent('mapsEditMarkers'),
			),
			// Those 2 - for future ;)
			'umsShapeTab' => array(
				'label' => __('Shapes', UMS_LANG_CODE),
				'icon' => 'fa-cubes',
				'btns' => array(
					array('id' => 'umsAddNewShapeBtn', 'classes' => 'umsProOpt', 'label' => __('New', UMS_LANG_CODE)),
				),
				'content' => $isPro && frameUms::_()->getModule('shape')
					? frameUms::_()->getModule('shape')->getView()->getEditShapes(array(
						'editMap' => $editMap, 'map' => $map, 'id' => $id
					))
					: $promoData['umsShapeTab']['content'],
			),
			/*'umsHeatmapTab' => array(
				'label' => __('Heatmap Layer', UMS_LANG_CODE),
				'icon' => 'fa-map',
				'content' => parent::getContent('mapsEditHeatmap'),
			),*/

		));
		return parent::getContent('mapsEdit');
	}
	public function getAllContactForms() {
		$formsList = array();
		$forms = frameCfs::_()->getModule('forms')->getModel()->getSimpleList('original_id != 0 AND ab_id = 0');

		if($forms) {
			foreach($forms as $f) {
				$formsList[ $f['id'] ] = $f['label'];
			}
		}
		return $formsList;
	}



	public function connectMapsAssets($map, $forAdminArea = false, $params = array()) {
		/*$map['params']['language'] = isset($map['params']['language']) && !empty($map['params']['language'])
				? $map['params']['language']
				: utilsUms::getLangCode2Letter();*/

		frameUms::_()->addScript('ums.core.maps', $this->getModule()->getModPath(). 'js/core.maps.js');
		frameUms::_()->addScript('ums.core.marker', frameUms::_()->getModule('marker')->getModPath(). 'js/core.marker.js');

		$defEngine = frameUms::_()->getModule('options')->get('def_engine');
		$engine = !empty($map) && !empty($map['engine']) ? $map['engine'] : $defEngine;
		if($params && isset($params['force_engine'])) {
			$engine = $params['force_engine'];
		}

		$fullEngine = $this->_getMapsFullEngine($engine);
		$engine = $this->_getMapsEngine($engine);

		$this->_connectApiAssets($engine, $fullEngine);
		frameUms::_()->addScript('ums_'. $engine. '.core.maps', $this->getModule()->getModPath(). 'js/engines/core.'. $engine. '.js');
		frameUms::_()->addScript('ums_'. $engine. '.core.marker', frameUms::_()->getModule('marker')->getModPath(). 'js/engines/core.'. $engine. '.marker.js');

		// Connect markers clusterization assets
		if(!empty($map) && (isset($map['params']['marker_clasterer']) && $map['params']['marker_clasterer'] != 'none')
			|| $forAdminArea
			|| ($params && isset($params['add_clasterer']))
		) {
			$this->_connectMarkerClusterAssets($engine);
		}
		frameUms::_()->addStyle('core.maps', $this->getModule()->getModPath(). 'css/core.maps.css');

		dispatcherUms::doAction('afterConnectMapAssets', $map, $forAdminArea);
	}

	public function generateSocialSharingHtml($map) {
		$res = '';
		$socialSharingHtml = apply_filters('supsystic_maps_sm_html', '', $map);

		if(!empty($socialSharingHtml)) {
			$res = $socialSharingHtml;
		}

		return $res;
	}

	private function _getEngineOpts($engine) {
		$modes = array();
		$fullEngine = $this->_getMapsFullEngine($engine);
		$leafletEngine = $this->_getMapsEngine($fullEngine);
		switch($fullEngine) {
			case 'bing':
				$modes = array(
					'navigation_bar_mode' => array(
						'compact' => __('Compact', UMS_LANG_CODE),
						'default' => __('Default', UMS_LANG_CODE),
						'minified' => __('Minified', UMS_LANG_CODE),
						//'none' => __('None', UMS_LANG_CODE),
					),
					'map_type' => array(
						'aerial' => __('Aerial', UMS_LANG_CODE),
						'canvasDark' => __('Canvas Dark', UMS_LANG_CODE),
						'canvasLight' => __('Canvas Light', UMS_LANG_CODE),
						'birdseye' => __('Birdseye', UMS_LANG_CODE),
						'grayscale' => __('Grayscale', UMS_LANG_CODE),
						// This two are not working propertly somehow
						//'mercator' => 'mercator', 'ordnanceSurvey' => 'ordnanceSurvey',
						'road' => __('Road', UMS_LANG_CODE),
						'streetside' => __('Street side', UMS_LANG_CODE),
					),
                    'hide_poi' => true,
					'map_stylization' => false,	// :)
				);
				break;
			case 'l-mapbox':
				$modes = array(

				);
				break;
			case 'l-thunderforest':
				$modes = array(

				);
				break;
			case 'leaflet':
				$modes = array(

				);
				break;
		}
		// Apply common styles
		if($leafletEngine == 'leaflet') {
			$mapLeafletTypes = $this->_getMapLeaFletTypes($fullEngine);
			$typesForSelect = array();
			foreach($mapLeafletTypes as $t => $tD) {
				$typesForSelect[ $t ] = is_array($tD) ? $tD['label'] : $tD;
			}
			$modes = array_merge($modes, array(
				'navigation_bar_mode' => array(
					'full' => __('Full (Zoom + Scale)', UMS_LANG_CODE),
					'zoom_only' => __('Zoom Only', UMS_LANG_CODE),
					'scale_only' => __('Scale Only', UMS_LANG_CODE),
					'none' => __('None', UMS_LANG_CODE),
				),
				'map_type' => $typesForSelect,
				'map_stylization' => false,
			));
		}
		return $modes;
	}
	// TODO: Move all those provider map types data detection here
	private function _getMapLeaFletTypes($engine) {
		$types = array();
		switch($engine) {
			case 'l-mapbox':
				$types = array(
					'streets-v11' => array('label' => __('Streets', UMS_LANG_CODE),
						'attr' => ''),
					'outdoors-v11' => __('Outdoors', UMS_LANG_CODE),
					'light-v10' => __('Light', UMS_LANG_CODE),
					'dark-v10' => __('Dark', UMS_LANG_CODE),
					'satellite-v9' => __('Satellite', UMS_LANG_CODE),
					// 'mapbox.terrain-rgb' => __('Terrain RGB', UMS_LANG_CODE),
				);
				break;
			case 'l-thunderforest':
				$types = array(
					'cycle' => array('label' => __('OpenCycleMap', UMS_LANG_CODE),
						'attr' => '&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'),
					'transport' => __('Transport', UMS_LANG_CODE),
					'landscape' => __('Landscape', UMS_LANG_CODE),
					'outdoors' => __('Outdoors', UMS_LANG_CODE),
					'transport-dark' => __('Transport Dark', UMS_LANG_CODE),
					'spinal-map' => __('Spinal Map', UMS_LANG_CODE),
					'pioneer' => __('Pioneer', UMS_LANG_CODE),
					'mobile-atlas' => __('Mobile Atlas', UMS_LANG_CODE),
					'neighbourhood' => __('Neighbourhood', UMS_LANG_CODE),
				);
				break;
			case 'leaflet':
				$types = array(
					'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' => array('label' => __('Default', UMS_LANG_CODE),
						'attr' => ''),
					'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png' => array('label' => __('OpenTopoMap', UMS_LANG_CODE),
						'attr' => ''),
					'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png' => array('label' => __('Wikimedia Labs', UMS_LANG_CODE),
						'attr' => ''),
					'https://maps.wikimedia.org/osm/{z}/{x}/{y}{r}.png' => __('Wikimedia Labs No Labels', UMS_LANG_CODE),
					'http://{s}.tiles.wmflabs.org/osm/{z}/{x}/{y}.png' => __('Wikimedia Labs OSM', UMS_LANG_CODE),
					'http://{s}.tiles.wmflabs.org/osm-no-labels/{z}/{x}/{y}.png' => __('Wikimedia Labs OSM No Labels', UMS_LANG_CODE),
					'http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png' => __('Black and White', UMS_LANG_CODE),
					'http://{s}.tiles.wmflabs.org/hillshading/{z}/{x}/{y}.png' => __('Hill Shading', UMS_LANG_CODE),
					'http://{s}.tiles.wmflabs.org/hikebike/{z}/{x}/{y}.png' => __('Hike and Bike', UMS_LANG_CODE),

					'https://{s}.tile.openstreetmap.se/hydda/base/{z}/{x}/{y}.png' => __('Hydda Base', UMS_LANG_CODE),
					'https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}{r}.png' => array('label' =>  __('Stamen Toner', UMS_LANG_CODE),
						'attr' => '',
					),
					'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-background/{z}/{x}/{y}{r}.png' => __('Stamen Background', UMS_LANG_CODE),
					'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}{r}.png' => __('Stamen Lite', UMS_LANG_CODE),
					'https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.png' => __('Stamen Watercolor', UMS_LANG_CODE),
					'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain/{z}/{x}/{y}{r}.png' => __('Stamen Terrain', UMS_LANG_CODE),
					'https://stamen-tiles-{s}.a.ssl.fastly.net/terrain-background/{z}/{x}/{y}{r}.png' => __('Stamen Terrain Background', UMS_LANG_CODE),

					'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}' => array('label' => __('Esri World Street', UMS_LANG_CODE),
						'attr' => 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012'
					),
					'https://server.arcgisonline.com/ArcGIS/rest/services/Specialty/DeLorme_World_Base_Map/MapServer/tile/{z}/{y}/{x}' => array('label' => __('Esri DeLorme', UMS_LANG_CODE),
						'attr' => 'Tiles &copy; Esri &mdash; Copyright: &copy;2012 DeLorme'
					),
					'https://server.arcgisonline.com/ArcGIS/rest/services/Ocean_Basemap/MapServer/tile/{z}/{y}/{x}' => array('label' => __('Esri Ocean Base', UMS_LANG_CODE),
						'attr' => 'Tiles &copy; Esri &mdash; Sources: GEBCO, NOAA, CHS, OSU, UNH, CSUMB, National Geographic, DeLorme, NAVTEQ, and Esri'
					),
				);
				break;
		}
		return $types;
	}
}
