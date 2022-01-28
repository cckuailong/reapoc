<?php
class marker_groupsControllerUms extends controllerUms {
	/**
	 * @see controller::getPermissions();
	 */
	protected function _prepareTextLikeSearch($val) {
		$query = '(title LIKE "%'. $val. '%"';
		if(is_numeric($val)) {
			$query .= ' OR id LIKE "%'. (int) $val. '%"';
		}
		$query .= ')';
		return $query;
	}
	protected function _prepareSortOrder($orderBy) {
		if(!$orderBy)
			$orderBy = 'sort_order';
		return $orderBy;
	}
	protected function _prepareListForTbl($data) {
		if (!empty($data)) {
			$markerGroupsIds = array();
			$parentsTitlesList = array();
			foreach($data as $i => $v) {
				// Save Markers Groups sort order
				$markerGroupsIds[] = $data[$i]['id'];
				$parent = (int)$data[$i]['parent'];

				// Marker Group Parent Title
				if(!empty($parent)) {
					if(!isset($parentsTitlesList[$parent])) {
						if($parentGroup = $this->getModel()->getById($parent)) {
							$parentsTitlesList[$parent] = !empty($parentGroup['title']) ? $parentGroup['title'] : '';
						}
					}
				}
				$data[$i]['parent'] = !empty($parentsTitlesList[$parent]) ? $parentsTitlesList[$parent] : '-';

				// Actions
				$actions = $this->getView()->getListOperations($v);
				$data[$i]['actions'] = preg_replace('/\s\s+/', ' ', trim($actions));
			}
			$this->getModel()->resortMarkerGroups($markerGroupsIds);
		}
		return $data;
	}
	public function save() {
		$saveRes = false;
		$data = reqUms::get('post');
		$res = new responseUms();
		$markerGroupId = 0;
		$edit = true;
		if(!isset($data['marker_group'])) {
			$res->pushError(__('Marker Category data not found', UMS_LANG_CODE));
			return $res->ajaxExec();
		}
		if(isset($data['marker_group']['id']) && !empty($data['marker_group']['id'])) {
			$saveRes = $this->getModel()->updateMarkerGroup($data['marker_group']);
			$markerGroupId = $data['marker_group']['id'];
		} else {
			$saveRes = $this->getModel()->saveNewMarkerGroup($data['marker_group']);
			$markerGroupId = $saveRes;
			$edit = false;
		}
		if($saveRes) {
			$res->addData('marker_group_id', $markerGroupId);
			$res->addData('marker_group', $this->getModel()->getMarkerGroupById( $markerGroupId ));
			if(!$edit) {	// For new marker category
				$fullEditUrl = $this->getModule()->getEditMarkerGroupLink( $markerGroupId );
				$res->addData('edit_url', $fullEditUrl);
			}
		} else {
			$res->pushError( $this->getModel()->getErrors() );
		}
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
	public function updateMarkerGroups() {
		$res = new responseUms();
		$postData = reqUms::get('post');
		if(!$this->getModel()->resortMarkerGroups($postData['ids'])) {
			$res->pushError( $this->getModel()->getErrors() );
		}
		if(!$this->getModel()->updateMarkerGroupParent($postData['current'], $postData['parent'])) {
			$res->pushError( $this->getModel()->getErrors() );
		}
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			UMS_USERLEVELS => array(
				UMS_ADMIN => array('getAllMarkerGroups', 'save', 'remove')
			),
		);
	}
}