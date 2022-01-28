<?php
class mailViewCfs extends viewCfs {
	public function getTabContent() {
		frameCfs::_()->getModule('templates')->loadJqueryUi();
		frameCfs::_()->addScript('admin.'. $this->getCode(), $this->getModule()->getModPath(). 'js/admin.'. $this->getCode(). '.js');
		
		$this->assign('options', frameCfs::_()->getModule('options')->getCatOpts( $this->getCode() ));
		$this->assign('testEmail', frameCfs::_()->getModule('options')->get('notify_email'));
		return parent::getContent('mailAdmin');
	}
}
