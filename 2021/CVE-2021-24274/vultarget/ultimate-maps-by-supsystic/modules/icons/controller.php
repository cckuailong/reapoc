<?php
class iconsControllerUms extends controllerUms {
	public function saveNewIcon(){
		$data= reqUms::get('post');
		$res = new responseUms();
		$result = $this->getModel()->saveNewIcon($data['icon']);
		if($result) {
			$data['icon']['id'] = $result;
			$res->addData($data['icon']);
		} else {
			$res->pushError( $this->getModel()->getErrors() );
		}
		//frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('icon.add');
		return $res->ajaxExec();
	}
	public function downloadIconFromUrl(){
		$data = reqUms::get('post');
		$res = new responseUms();
		if(!isset($data['icon_url']) || empty($data['icon_url'])){
			$res->pushError(__('Empty url', UMS_LANG_CODE));
			return $res->ajaxExec();
		}
		$result = $this->getModel()->downloadIconFromUrl($data['icon_url']);
		if($result) {
			$res->addData($result);
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	public function remove() {
		$res = new responseUms();
		if(!$this->getModel()->remove(reqUms::get('post'))) {
			$res->pushError($this->getModel()->getErrors());
		}
		//frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('icon.delete');
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			UMS_USERLEVELS => array(
				UMS_ADMIN => array('saveNewIcon', 'downloadIconFromUrl', 'remove')
			),
		);
	}
}