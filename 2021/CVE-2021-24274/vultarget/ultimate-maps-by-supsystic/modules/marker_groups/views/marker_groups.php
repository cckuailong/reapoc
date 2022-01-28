<?php
class marker_groupsViewUms extends viewUms {
	public function getTabContent() {
		$markerGroups = $this->getModel()->getAllMarkerGroups();

		foreach($markerGroups as $k => $mg) {
			$markerGroups[$k]['actions'] = $this->getListOperations($mg);
		}
		frameUms::_()->addStyle('admin.mgr', $this->getModule()->getModPath(). 'css/admin.marker.groups.css');
		frameUms::_()->getModule('templates')->loadJqTreeView();
		frameUms::_()->addScript('admin.mgr.list', $this->getModule()->getModPath(). 'js/admin.marker_groups.list.js');
		frameUms::_()->addJSVar('admin.mgr.list', 'mgrTblData', $markerGroups);
		
		$this->assign('addNewLink', frameUms::_()->getModule('options')->getTabUrl('marker_groups_add_new'));
		return parent::getContent('mgrAdmin');
	}
	public function getEditMarkerGroup($id = 0) {
		frameUms::_()->addScript('admin.mgr.edit', $this->getModule()->getModPath(). 'js/admin.marker_groups.edit.js');
		frameUms::_()->addStyle('admin.mgr', $this->getModule()->getModPath() . 'css/admin.marker.groups.css');
		$editMarkerGroup = $id ? true : false;

		if($editMarkerGroup) {
			$markerGroup = $this->getModel()->getMarkerGroupById( $id );
			$this->assign('marker_group', $markerGroup);
			frameUms::_()->addJSVar('admin.mgr.edit', 'mgrMarkerGroup', $markerGroup);
		}
		$this->assign('editMarkerGroup', $editMarkerGroup);
		$this->assign('parentsList', $this->getModel()->getMarkerGroupsForSelect(array( 0 => __('None', UMS_LANG_CODE) ), $id));
		$this->assign('addNewLink', frameUms::_()->getModule('options')->getTabUrl('marker_groups_add_new'));
		return parent::getContent('mgrEditMarkerGroup');
	}
	public function getListOperations($markerGroup) {
		$this->assign('marker_group', $markerGroup);
		$this->assign('editLink', $this->getModule()->getEditMarkerGroupLink( $markerGroup['id'] ));
		return trim(parent::getInlineContent('mgrListOperations'));
	}
}