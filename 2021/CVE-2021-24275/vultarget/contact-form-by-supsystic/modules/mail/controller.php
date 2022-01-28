<?php
class mailControllerCfs extends controllerCfs {
	public function testEmail() {
		$res = new responseCfs();
		$email = reqCfs::getVar('test_email', 'post');
		if($this->getModel()->testEmail($email)) {
			$res->addMessage(__('Now check your email inbox / spam folders for test mail.'));
		} else 
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function saveMailTestRes() {
		$res = new responseCfs();
		$result = (int) reqCfs::getVar('result', 'post');
		frameCfs::_()->getModule('options')->getModel()->save('mail_function_work', $result);
		$res->ajaxExec();
	}
	public function saveOptions() {
		$res = new responseCfs();
		$optsModel = frameCfs::_()->getModule('options')->getModel();
		$submitData = reqCfs::get('post');
		if($optsModel->saveGroup($submitData)) {
			$res->addMessage(__('Done', CFS_LANG_CODE));
		} else
			$res->pushError ($optsModel->getErrors());
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			CFS_USERLEVELS => array(
				CFS_ADMIN => array('testEmail', 'saveMailTestRes', 'saveOptions')
			),
		);
	}
}
