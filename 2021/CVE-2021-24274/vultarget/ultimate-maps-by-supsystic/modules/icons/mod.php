<?php
class  iconsUms extends moduleUms {
	public function init(){
		parent::init();
		add_filter('upload_mimes', array($this, 'addMimeTypes'));
		$this->getModel()->checkDefIcons();
		/*if(frameUms::_()->isAdminPlugPage()){
			$umsExistsIcons = $this->getModel()->getIcons();
			frameUms::_()->addJSVar('iconOpts', 'umsExistsIcons', $umsExistsIcons);
			frameUms::_()->addScript('iconOpts', $this->getModPath() .'js/iconOpts.js');			
		}*/
	}
	function addMimeTypes($mimes) {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
}