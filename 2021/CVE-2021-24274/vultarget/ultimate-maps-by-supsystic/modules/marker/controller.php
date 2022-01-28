<?php
class markerControllerUms extends controllerUms {
	public function save() {
		$res = new responseUms();
		$markerData = reqUms::getVar('marker_opts');
		$update = false;
		if($id = $this->getModel()->save($markerData, $update)){
			$res->addMessage(__('Done', UMS_LANG_CODE));
			$res->addData('marker', $this->getModel()->getById($id));
			$res->addData('update', $update);
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
        //frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('marker.save');
        return $res->ajaxExec();
	}
	public function updatePos() {
		$res = new responseUms();
		if($this->getModel()->updatePos(reqUms::get('post'))) {
			//$res->addMessage(__('Done', UMS_LANG_CODE));	// Do nothing for now - void method
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
        return $res->ajaxExec();
	}
    public function findAddress(){
        $data = reqUms::get('post');
        $res = new responseUms();
        $result = $this->getModel()->findAddress($data);
        if($result) {
            $res->addData($result);
        } else {
			$res->pushError($this->getModel()->getErrors());
        }
        //frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('geolocation.address.search');
        return $res->ajaxExec();
    }
    public function removeMarker(){
        $params = reqUms::get('post');
        $res = new responseUms();
        if(!isset($params['id'])){
            $res->pushError(__('Marker Not Found', UMS_LANG_CODE));
            return $res->ajaxExec();
        }
        if($this->getModel()->removeMarker($params["id"])){
           $res->addMessage(__("Done", UMS_LANG_CODE));
        }else{
            $res->pushError(__("Cannot remove marker", UMS_LANG_CODE));
        }
        //frameUms::_()->getModule("supsystic_promo")->getModel()->saveUsageStat('marker.delete');
        return $res->ajaxExec();
    }
	public function removeList() {
		$params = reqUms::get('post');
        $res = new responseUms();
        if(!isset($params['remove_ids'])){
			$res->pushError(__('Marker Not Found', UMS_LANG_CODE));
			return $res->ajaxExec();
        }
        if($this->getModel()->removeList($params['remove_ids'])){
           $res->addMessage(__('Done', UMS_LANG_CODE));
        } else {
            $res->pushError(__('Cannot remove markers', UMS_LANG_CODE));
        }
        //frameUms::_()->getModule("supsystic_promo")->getModel()->saveUsageStat('marker.delete_list');
        return $res->ajaxExec();
	}
	public function getMarkerForm($params){
		return $this->getView()->getMarkerForm($params);
	}
	public function getListForTable() {
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
			$listReqData['additionalCondition'] = 'title LIKE "%'. $dbSearch. '%" OR description LIKE "%'. $dbSearch. '%"';
		}
		$list = $this->getModel()->getAllMarkers( $listReqData, true );

		$res->addData('aaData', $this->_convertDataForDatatable($list));
		$res->addData('iTotalRecords', $count);
		$res->addData('iTotalDisplayRecords', $count);
		$res->addData('sEcho', reqUms::getVar('sEcho'));
		$res->addMessage(__('Done'));
		return $res->ajaxExec();
	}
	public function getListForTbl() {
      $res = new responseUms();
      $res->ignoreShellData();
      $model = $this->getModel();
      $page = (int)sanitize_text_field(reqUms::getVar('page'));
      $rowsLimit = (int)sanitize_text_field(reqUms::getVar('rows'));
			$mapId = (int)sanitize_text_field(reqUms::getVar('map_id'));
      $search = reqUms::getVar('search');
      $search = !empty($search['text_like']) ? sanitize_text_field($search['text_like']) : '';
      $totalCount = $model->getTotalCountBySearch($search, $mapId);
      $totalPages = 0;
      if ($totalCount > 0) {
         $totalPages = ceil($totalCount / $rowsLimit);
      }
      if ($page > $totalPages) {
         $page = $totalPages;
      }
      $limitStart = $rowsLimit * $page - $rowsLimit;
      if ($limitStart < 0) $limitStart = 0;
      $data = $model->getListForTblBySearch($search, $limitStart, $rowsLimit, $mapId);
      $data = $this->_prepareListForTbl($data);
      $res->addData('page', $page);
      $res->addData('total', $totalPages);
      $res->addData('rows', $data);
      $res->addData('records', $model->getLastGetCount());
      $res = dispatcherUms::applyFilters($this->getCode() . '_getListForTblResults', $res);
      $res->ajaxExec();
   }
	public function getMapMarkers() {
		$res = new responseUms();
		$mapId = (int) reqUms::getVar('map_id', 'post');
		$markers = array();
		if($mapId) {
			$markers = $this->getModel()->getMapMarkers( $mapId );
		} else {
			$addedMarkerIds = reqUms::getVar('added_marker_ids', 'post');
			if(!empty($addedMarkerIds)) {
				$markers = $this->getModel()->getMarkersByIds( $addedMarkerIds );
			}
		}
		if($markers !== false) {
			$res->addData('markers', $markers);
		} else
			$res->pushError($this->getModel ()->getErrors());
		return $res->ajaxExec();
	}
	private function _convertDataForDatatable($list) {
		foreach($list as $i => $marker) {
			$list[$i]['marker_check'] = htmlUms::checkbox('marker_check['. $list[$i]['id']. ']');
			$list[$i]['list_icon'] = $this->getView()->getListIcon($list[$i]);
			$list[$i]['list_title'] = $this->getView()->getListTitle($list[$i]);
			$list[$i]['group_title'] = $list[$i]['marker_group']['title'];
			$list[$i]['list_address'] = $this->getView()->getListAddress($list[$i]);
			$list[$i]['uses_on_map'] = $this->getView()->getListUsesOnMap($list[$i]);
			$list[$i]['operations'] = $this->getView()->getListOperations($list[$i]);
		}
		return $list;
	}
	public function getMarker() {
		$res = new responseUms();
		$id = (int) reqUms::getVar('id');
		if($id) {
			$marker = $this->getModel()->getById($id);
			if(!empty($marker)) {
				$res->addData('marker', $marker);
			} else
				$res->pushError ($this->getModel()->getErrors());
		} else
			$res->pushError (__('Empty or invalid marker ID', UMS_LANG_CODE));
		return $res->ajaxExec();
	}
	protected function _prepareModelBeforeListSelect($model) {
		$map_id = (int) reqUms::getVar('map_id');
		$model->addWhere(array('map_id' => $map_id));
		return $model;
	}
	protected function _prepareSortOrder($orderBy) {
		if(!$orderBy)
			$orderBy = 'sort_order';
		return $orderBy;
	}
	protected function _prepareListForTbl($data) {
		if (!empty($data)) {
			$markersIds = array('map_id' => $data[0]['map_id'], 'markers_list' => array());
			foreach($data as $i => $m) {
				$data[$i] = $this->getModel()->_afterGet($data[$i]);
				// Save Marker sort order
				$markersIds['markers_list'][] = $data[$i]['id'];
				// Marker Icon Image
				$icon = '<div class="egm-marker-icon"><img src="'. $data[$i]['icon_data']['path'] .'" /></div>';
				$data[$i]['icon_img'] = preg_replace('/\s\s+/', ' ', trim($icon));

				// Marker Coordinates
				$coords = '<div class="egm-marker-latlng">'
					. round($data[$i]['coord_x'], 2) . '"N '
					. round($data[$i]['coord_y'], 2) . '"E
					</div>';
				$data[$i]['coords'] = preg_replace('/\s\s+/', ' ', trim($coords));

				// Marker Action Buttons
				$data[$i]['actions'] = frameUms::_()->getModule('marker')->getView()->getListOperations($data[$i]['id']);
			}
			//frameUms::_()->getModule('maps')->getModel()->resortMarkers($markersIds);
		}

		return $data;
	}
	public function saveFindAddressStat() {
		//frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('geolocation.address.search');
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			UMS_USERLEVELS => array(
				UMS_ADMIN => array('save', 'removeMarker', 'getMarkerForm', 'getListForTable', 'getMarker', 'removeList', 'getMapMarkers', 'updatePos')
			),
		);
	}
}
