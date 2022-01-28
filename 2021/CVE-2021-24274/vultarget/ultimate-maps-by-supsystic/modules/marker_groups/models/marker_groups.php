<?php
class marker_groupsModelUms extends modelUms {
	function __construct() {
		$this->_setTbl('marker_groups');
	}
	public function getAllMarkerGroups($d = array()){
		if(isset($d['limitFrom']) && isset($d['limitTo']))
			frameUms::_()->getTable('marker_groups')->limitFrom($d['limitFrom'])->limitTo($d['limitTo']);

		$sortOrder = (isset($d['orderBy']) && !empty($d['orderBy'])) ? $d['orderBy'] : 'sort_order';
		global $wpdb;
		$markerGroups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ums_marker_groups AS ums_mrgr ORDER BY sort_order", ARRAY_A);
		//$markerGroups = frameUms::_()->getTable('marker_groups')->orderBy($sortOrder)->get('*', $d);
		$markerGroups = $this->_afterGet($markerGroups);
		return $markerGroups;
	}
	public function getMarkerGroupsByIds($ids){
		if(!$ids){
			return false;
		}
		if(!is_array($ids))
		$ids = array( $ids );
		$ids = array_map('intval', $ids);
		global $wpdb;
		$ids = implode(',', array_map('absint', $ids));
		$groups = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_marker_groups WHERE id IN (%1s) ORDER BY sort_order", $ids), ARRAY_A);
		//$groups = frameUms::_()->getTable('marker_groups')->orderBy('sort_order')->get('*', array('additionalCondition' => 'id IN (' . implode(',', $ids) . ')'));
		$groups = $this->_afterGet($groups);
		if(!empty($groups)) {
			return $groups;
		}
		return false;
	}
	public function getMarkerGroupById($id = false){
		if(!$id){
			return false;
		}
		global $wpdb;
		$markerGroup = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ums_marker_groups WHERE id = %s ORDER BY sort_order", $id), ARRAY_A);
		//$markerGroup = frameUms::_()->getTable('marker_groups')->orderBy('sort_order')->get('*', array('id' => (int)$id), '', 'row');
		$markerGroup = $this->_afterGet($markerGroup, true);
		if(!empty($markerGroup)){
			return $markerGroup;
		}
		return false;
	}
	public function remove($markerGroupId){
		$markerGroupId = (int) $markerGroupId;
		if(!empty($markerGroupId)) {
			global $wpdb;
			$tableName = $wpdb->prefix . "ums_marker_groups";
			$data_where = array(
				 'id' => $markerGroupId
			);
			$deleteMarkerGroup = $wpdb->delete($tableName, $data_where);

			$data_update = array('parent' => 0);
			$data_where = array('parent' => $markerGroupId);
			$wpdb->update($tableName, $data_update, $data_where);

			if($deleteMarkerGroup){
				global $wpdb;
				$tableName = $wpdb->prefix . "ums_markers";
				$data_update = array('marker_group_id' => 0);
				$data_where = array('marker_group_id' => $markerGroupId);
				return $res = $wpdb->update($tableName , $data_update, $data_where);
			}
		} else
			$this->pushError (__('Invalid ID', UMS_LANG_CODE));
		return false;
	}
	protected function _afterGet($data, $single = false) {
		if($single) {
			$data = array($data);
		}
		foreach($data as $k => $group) {
			$data[$k]['params'] = utilsUms::unserialize($data[$k]['params']);
		}
		if($single) {
			$data = $data[0];
		}
		return $data;
	}
	protected function _dataSave($data, $update = false) {
		$data['title'] = trim($data['title']);

		$mgrParamsKeys = array('bg_color','text_color', 'claster_icon', 'claster_icon_width', 'claster_icon_height');
		$mgrParams = array();
		foreach($mgrParamsKeys as $k){
			$mgrParams[$k] = isset($data[$k]) ? $data[$k] : null;
		}
		$data['params'] = utilsUms::serialize($mgrParams);

		return $data;
	}
	private function _validateSaveMarkerGroup($markerGroup) {
		if(empty($markerGroup['title'])) {
			$this->pushError(__('Please enter Marker Category'), 'marker_group[title]', UMS_LANG_CODE);
		}
		return !$this->haveErrors();
	}
	public function updateMarkerGroup($params){
		$data = $this->_dataSave($params);
		if($this->_validateSaveMarkerGroup($data)) {
			global $wpdb;
			$tableName = $wpdb->prefix . "ums_marker_groups";
			$data_where = array('id' => (int)$params['id']);
			$data_update = array(
				'title' => $data['title'],
				'parent' => $data['parent'],
				'description' => $data['description'],
				'params' => $data['params'],
			);
			return $res = $wpdb->update($tableName , $data_update, $data_where);
		}
		return false;
	}
	public function updateMarkerGroupParent($id, $parentId){
		$tableName = $wpdb->prefix . "ums_marker_groups";
		$data_where = array('id' => $id);
		return $res = $wpdb->update($tableName , array('parent' => $parentId), $data_where);
	}
	public function removeGroup($ids) {
		if(!is_array($ids))
			$ids = array($ids);
		// Remove all empty values
		$ids = array_filter(array_map('intval', $ids));
		if(!empty($ids)) {
      global $wpdb;
      $ids = implode(',', $ids);
      $prepareQuery = $wpdb->prepare( "DELETE FROM {$wpdb->prefix}ums_marker_groups WHERE id IN (%1s)", $ids );
			if($res = $wpdb->query( $prepareQuery )) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				return true;
			} else
				$this->pushError (__('Database error detected', UMS_LANG_CODE));
		} else
			$this->pushError(__('Invalid ID', UMS_LANG_CODE));
		return false;
	}
	public function saveNewMarkerGroup($params){
		if(!empty($params)) {
			$insertData = $this->_dataSave($params);
			if($this->_validateSaveMarkerGroup($insertData)) {

				global $wpdb;
				$tableName = $wpdb->prefix . "ums_marker_groups";
				$dbRes = $wpdb->insert($tableName, array(
					 'title' => $insertData['title'],
					 'parent' => $insertData['parent'],
					 'description' => $insertData['description'],
					 'params' => $insertData['params'],
				));
				if ($dbRes) {
					 $newMarkerGroupId = $wpdb->insert_id;;
				}
				if(!empty($newMarkerGroupId) && $newMarkerGroupId){
					$allMarkerGroups = $this->getAllMarkerGroups();
					$allMarkerGroupsIds = array();

					foreach($allMarkerGroups as $g) {
						if($g['id'] != $newMarkerGroupId) {
							array_push($allMarkerGroupsIds, $g['id']);
						}
					}
					$elem = $this->getNewMarkerGroupSortOrder($allMarkerGroups, $newMarkerGroupId, $insertData['parent']);
					$offset = array_search($elem, $allMarkerGroupsIds);
					array_splice($allMarkerGroupsIds, ($offset + 1), 0, array((string)$newMarkerGroupId));
					$this->resortMarkerGroups($allMarkerGroupsIds);
					return $newMarkerGroupId;
				} else {
					$this->pushError(frameUms::_()->getTable('marker_groups')->getErrors());
				}
			}
		} else
			$this->pushError(__('Empty Params', UMS_LANG_CODE));
		return false;
	}
	public function getNewMarkerGroupSortOrder($allMarkerGroups, $newMarkerGroupId, $parent) {
		$allChildren = $this->getChildrenList($allMarkerGroups, $parent);
		unset($allChildren[array_search($newMarkerGroupId, $allChildren)]);
		$allChildren = array_values($allChildren);
		return !empty($allChildren) ? $allChildren[count($allChildren) - 1] : $parent;
	}
	public function getChildrenList($groups, $parent, $children = array()) {
		foreach($groups as $g) {
			if($g['parent'] == $parent) {
				array_push($children, $g['id']);
				$children = $this->getChildrenList($groups, $g['id'], $children);
			}
		}
		return $children;
	}
	public function resortMarkerGroups($markerGroupsIds = array()) {
		if($markerGroupsIds) {
			$i = 1;
			global $wpdb;
			foreach($markerGroupsIds as $mgrId) {
				$tableName = $wpdb->prefix . "ums_marker_groups";
				$data_update = array('sort_order' => $i++);
				$data_where = array('id' => $mgrId);
				$wpdb->update($tableName , $data_update, $data_where);
			}
		}
		return true;
	}
	public function getCurrentMapMarkersGroupsTree($map, $withUncategorized = false) {
		$uncategorized = $withUncategorized ? $this->getUncategorizedGroupData() : array();
		$markerGroupsTree = array();

		if(isset($map['markers']) && !empty($map['markers'])) {
			$groupsForCurMap = array();

			foreach($map['markers'] as $marker) {
				if(is_array($marker['marker_group_ids'])){
					foreach ($marker['marker_group_ids'] as $marker_group_id) {
						array_push($groupsForCurMap, $marker_group_id);
					}
				}
			}
			$markerGroups = $this->getMarkerGroupsByIds($groupsForCurMap);

			if(!empty($markerGroups)) {
				$markerGroups = $this->updateMarkerGroupsListByParents($markerGroups, $groupsForCurMap);
				$markerGroupsTree = $this->getMarkerGroupsTree($markerGroups);
				$markerGroupsTree = array_merge($markerGroupsTree, $uncategorized);	// should be in the end of list
			} else {
				$markerGroupsTree = $uncategorized;
			}
		}
		return $markerGroupsTree;
	}
	public function getMarkerGroupsTree($groups, $parentId = 0) {
		$res = array();
		foreach($groups as $i => $g) {
			if(isset($g['parent']) && $g['parent'] == $parentId) {
				$res[] = array_merge($g, array('children' => $this->getMarkerGroupsTree($groups, $g['id'])));
			}
		}
		return $res;
	}
	public function updateMarkerGroupsListByParents($groups, $groupsIds) {
		foreach($groups as $i => $g) {
			if(isset($g['parent']) && $g['parent'] > 0 && !in_array($g['parent'], $groupsIds)) {
				array_push($groups, $this->getMarkerGroupById($g['parent']));
				array_push($groupsIds, $g['parent']);
				return $this->updateMarkerGroupsListByParents($groups, $groupsIds);
			}
		}
		return $groups;
	}
	public function getUncategorizedGroupData() {
		return array(array(
			'id' => 0,
			'parent' => 0,
			'title' => __('Uncategorized', UMS_LANG_CODE),	// group for uncategorized markers
			'params' => '',
			'markers' => array(),
		));
	}
	public function getMarkerGroupsForSelect($markerGroupsForSelect = array(), $current_group_id = 0) {
		$allMarkerGroupsList = $this->getAllMarkerGroups();

		foreach($allMarkerGroupsList as $key => $value) {
		    if ($current_group_id && $current_group_id == $value['id']) {
		        continue;
            }
			$markerGroupsForSelect[ $value['id'] ] = $this->_updateTitleForTreeView($value['title'], $value, $allMarkerGroupsList);
		}
		return $markerGroupsForSelect;
	}
	public function _updateTitleForTreeView($title, $group, $allMarkerGroups) {
		$level = $this->_itemGetLevel($group, $allMarkerGroups);
		$title = str_repeat('-', $level) . ' ' . $title;
		return $title;
	}
	public function _itemGetLevel($group, $allMarkerGroups, $level = 0) {
		if($group['parent'] != 0 && $level < 10) {
			foreach($allMarkerGroups as $g) {
				if($g['id'] == $group['parent']) {
					$level = $this->_itemGetLevel($g, $allMarkerGroups, ++$level);
				}
			}
		}
		return $level;
	}
}
