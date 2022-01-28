<?php
class mapsControllerUms extends controllerUms {
	/*public function getAllMaps($withMarkers = false){
	   $maps = $this->getModel()->getAllMaps($withMarkers);
	   var_dump($maps);
	   return $maps;
	}*/
	public function getListForTbl() {
      $res = new responseUms();
      $res->ignoreShellData();
      $model = $this->getModel();
      $page = (int)sanitize_text_field(reqUms::getVar('page'));
      $rowsLimit = (int)sanitize_text_field(reqUms::getVar('rows'));
      $search = reqUms::getVar('search');
      $search = !empty($search['text_like']) ? sanitize_text_field($search['text_like']) : '';
      $totalCount = $model->getTotalCountBySearch($search);
      $totalPages = 0;
      if ($totalCount > 0) {
         $totalPages = ceil($totalCount / $rowsLimit);
      }
      if ($page > $totalPages) {
         $page = $totalPages;
      }
      $limitStart = $rowsLimit * $page - $rowsLimit;
      if ($limitStart < 0) $limitStart = 0;
      $data = $model->getListForTblBySearch($search, $limitStart, $rowsLimit);
      $data = $this->_prepareListForTbl($data);
      $res->addData('page', $page);
      $res->addData('total', $totalPages);
      $res->addData('rows', $data);
      $res->addData('records', $model->getLastGetCount());
      $res = dispatcherUms::applyFilters($this->getCode() . '_getListForTblResults', $res);
      $res->ajaxExec();
   }
	protected function _prepareTextLikeSearch($val) {
		$query = '(title LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	public function save() {
		$saveRes = false;
		$data = reqUms::get('post');
		$res = new responseUms();
		$mapId = 0;
		$edit = true;
		if(!isset($data['map_opts'])) {
			$res->pushError(__('Map data not found', UMS_LANG_CODE));
			return $res->ajaxExec();
		}
		if(isset($data['map_opts']['id']) && !empty($data['map_opts']['id'])) {
			$saveRes = $this->getModel()->updateMap($data['map_opts']);
			$mapId = $data['map_opts']['id'];
		} else {
			$saveRes = $this->getModel()->saveNewMap($data['map_opts']);
			$mapId = $saveRes;
			$edit = false;
		}
		if($saveRes) {
			// save Membership param
			$membershipModule = frameUms::_()->getModule('membership');
			if($membershipModule) {
				$membershipModel = $membershipModule->getModel('membership_presets');
				if($membershipModel) {
					$membershipModel->updateRow(array('maps_id' => $mapId, 'allow_use' => isset($data['map_opts']['membershipEnable']) ? $data['map_opts']['membershipEnable'] : 0));
				}
			}
			$addMarkerIds = reqUms::getVar('add_marker_ids');
			if($addMarkerIds && !empty($addMarkerIds)) {
				frameUms::_()->getModule('marker')->getModel()->setMarkersToMap($addMarkerIds, $mapId);
				$this->getModel()->resortMarkers(array('map_id' => $mapId));
			}
			if(frameUms::_()->getModule('supsystic_promo')->isPro()) {
				$addShapeIds = reqUms::getVar('add_shape_ids');
				if($addShapeIds && !empty($addShapeIds) && frameUms::_()->getModule('shape')) {
					frameUms::_()->getModule('shape')->getModel()->setShapesToMap($addShapeIds, $mapId);
					$this->getModel()->resortShapes(array('map_id' => $mapId));
				}
			}
			$res->addMessage(__('Done', UMS_LANG_CODE));
			$res->addData('map_id', $mapId);
			$res->addData('map', $this->getModel()->getMapById( $mapId ));
			if(!$edit) {	// For new maps
				$fullEditUrl = $this->getModule()->getEditMapLink( $mapId );
				$res->addData('edit_url', $fullEditUrl);
			}
		} else {
			$res->pushError( $this->getModel()->getErrors() );
		}
		//frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('map.edit');
		return $res->ajaxExec();
	}
	public function remove() {
		$res = new responseUms();
		if($this->getModel()->remove(reqUms::getVar('id', 'post'))) {
			$res->addMessage(__('Done', UMS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function cloneMapGroup() {
		$res = new responseUms();
		if($this->getModel()->cloneMapGroup(reqUms::getVar('listIds', 'post'))) {
			$res->addMessage(__('Done', UMS_LANG_CODE));
		} else
			$res->pushError($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	/*public function removeMap(){
		$data=  reqUms::get('post');
		$res = new responseUms();
		if(!isset($data['map_id']) || empty($data['map_id'])){
			$res->pushError(__("Nothing to remove", UMS_LANG_CODE));
			return $res->ajaxExec();
		}

		if($this->getModel()->remove($data['map_id'])){
			$res->addMessage(__("Done", UMS_LANG_CODE));
		}else{
			$res->pushError($this->getModel()->getErrors());
		}
		frameUms::_()->getModule("supsystic_promo")->getModel()->saveUsageStat("map.delete");
		return $res->ajaxExec();
	}*/

	/*public function getListForTable() {
		$res = new responseUms();
		$res->ignoreShellData();

		$count = $this->getModel()->getCount();
		$listReqData = array(
			'limitFrom' => reqUms::getVar('iDisplayStart'),
			'limitTo' => reqUms::getVar('iDisplayLength'),
		);
		$displayColumns = $this->getView()->getDisplayColumns();
		$displayColumnsKeys = array_keys($displayColumns);
		$iSortCol = reqUms::getVar('iSortCol_0');
		if(!is_null($iSortCol) && is_numeric($iSortCol)) {
			$listReqData['orderBy'] = $displayColumns[ $displayColumnsKeys[ $iSortCol ] ]['db'];
			$iSortDir = reqUms::getVar('sSortDir_0');
			if(!is_null($iSortDir)) {
				$listReqData['orderBy'] .= ' '. strtoupper($iSortDir);
			}
		}
		$search = reqUms::getVar('sSearch');
		if(!is_null($search) && !empty($search)) {
			$dbSearch = dbUms::escape($search);
			$listReqData['additionalCondition'] = 'title LIKE "%'. $dbSearch. '%"';
		}
		$list = $this->getModel()->getAllMaps( $listReqData, true );

		$res->addData('aaData', $this->_convertDataForDatatable($list));
		$res->addData('iTotalRecords', $count);
		$res->addData('iTotalDisplayRecords', $count);
		$res->addData('sEcho', reqUms::getVar('sEcho'));
		$res->addMessage(__('Done'));
		return $res->ajaxExec();
	}*/

	/*public function getMapById()
	{
		$res = new responseUms();

	    $req = reqUms::get('post');

		if (!isset($req['id']) || 1 > (int)$req['id']) {
			$res->pushError(__('Invalid map identifier.', UMS_LANG_CODE));

			return $res->ajaxExec();
		}
		$model = $this->getModel();
		$map = $model->getMapById($req['id']);

		if (!$map) {
			$res->pushError(__('Failed to find map.', UMS_LANG_CODE));

			return $res->ajaxExec();
		}

		$res->addData('map', (array)$map);

		return $res->ajaxExec();
	}*/

	protected function _prepareListForTbl($data) {
		if (!empty($data)) {
			foreach($data as $i => $v) {
				$mapId   = (int)$data[$i]['id'];
				$map     = $this->getModel()->getMapById($mapId);

				// Pretty date format based on the WordPress options
				$format = get_option('date_format');
				$createDate = date($format, strtotime($data[$i]['create_date']));

				// Markers
				$markers = $this->getView()->getListMarkers($map);

				// Actions
				$actions = $this->getView()->getListOperations($map);

				$data[$i]['title'] = '<a href="'. $this->getModule()->getEditMapLink( $map['id'] ). '">'. $data[ $i ]['title']. '&nbsp;<i class="fa fa-fw fa-pencil" style="margin-top: 2px;"></i></a>';

				$data[$i]['create_date'] = $createDate;
				$data[$i]['markers'] = preg_replace('/\s+/', ' ', trim($markers));
				$data[$i]['actions'] = preg_replace('/\s\s+/', ' ', trim($actions));
			}
		}

		return $data;
	}
//	protected function _prepareTextLikeSearch($val) {
//		$query = '(ip LIKE "%'. $val. '%"';
//		if(is_numeric($val)) {
//			$query .= ' OR id LIKE "%'. (int) $val. '%"';
//		}
//		$query .= ')';
//		return $query;
//	}
//	protected function _prepareSortOrder($sortOrder) {
//		switch($sortOrder) {
//			case 'type_label':
//				$sortOrder = 'type';
//				break;
//		}
//		return $sortOrder;
//	}


	/*private function _convertDataForDatatable($list, $single = false) {
		$returnList = array();
		if($single) {
			$list = array($list);
		}
		foreach($list as $i => $map) {
			$returnList[ $i ] = $map;
			$returnList[ $i ]['list_html_options'] = $this->getView()->getListHtmlOptions( $map );
			$returnList[ $i ]['list_markers'] = $this->getView()->getListMarkers( $map );
			$returnList[ $i ]['operations'] = $this->getView()->getListOperations( $map );
		}
		if($single) {
			return $returnList[0];
		}
		return $returnList;
	}*/
	public function resortMarkers() {
		$res = new responseUms();
		if(!$this->getModel()->resortMarkers(reqUms::get('post'))) {
			$res->pushError( $this->getModel()->getErrors() );
		}
		return $res->ajaxExec();
	}
	public function resortShapes() {
		$res = new responseUms();
		if(!$this->getModel()->resortShapes(reqUms::get('post'))) {
			$res->pushError( $this->getModel()->getErrors() );
		}
		return $res->ajaxExec();
	}
	public function changeEngine() {
		$res = new responseUms();
		$id = (int) reqUms::getVar('id');
		if(!$id) {	// This is new map - just change it
			$res->addData('new_engine_url', frameUms::_()->getModule('options')->getTabUrl($this->getCode(). '_add_new'). '&ums_engine='. reqUms::getVar('engine'));
		} elseif(!$this->getModel()->changeEngine(reqUms::get('post'))) {
			$res->pushError( $this->getModel()->getErrors() );
		}
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			UMS_USERLEVELS => array(
				UMS_ADMIN => array('getListForTbl', 'getAllMaps', 'save', 'clear', 'remove', 'removeGroup',
					'cloneMapGroup', 'resortMarkers', 'changeEngine')
			),
		);
	}
}
