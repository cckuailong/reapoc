<?php
class  markerUms extends moduleUms {
	public function init() {
		//dispatcherUms::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		//dispatcherUms::addAction('tplHeaderBegin',array($this,'showFavico'));
		//dispatcherUms::addAction('tplBodyEnd',array($this, 'GoogleAnalitics'));
		//dispatcherUms::addAction('in_admin_footer',array($this, 'showPluginFooter'));
	}
	/*public function addOptionsTab($tabs){
		if(frameUms::_()->isAdminPlugPage()){
//			frameUms::_()->addScript('adminMetaOptions',$this->getModPath().'js/admin.marker.js',array(),false,true);
		}
		return $tabs;
	}*/
	/*public function connectAssets() {
		frameUms::_()->addScript('marker', $this->getModPath(). 'js/marker.js');
	}*/
	public function getAnimationList() {
		return array(
			0 => __('None', UMS_LANG_CODE),
			1 => __('Drop', UMS_LANG_CODE),	//DROP
			2 => __('Bounce', UMS_LANG_CODE),	//BOUNCE
		);
	}
}