<?php
class optionsControllerCfs extends controllerCfs {
	public function saveGroup() {
		$res = new responseCfs();
		if($this->getModel()->saveGroup(reqCfs::get('post'))) {
			$res->addMessage(__('Done', CFS_LANG_CODE));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			CFS_USERLEVELS => array(
				CFS_ADMIN => array('saveGroup')
			),
		);
	}
}

