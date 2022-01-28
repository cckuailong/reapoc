<?php
class markerViewUms extends viewUms {
	public function getListOperations($markerId) {
		$this->assign('marker', array('id' => $markerId));
		return parent::getContent('markerListOperations');
	}
}