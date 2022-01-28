<?php
abstract class modelSubscribeCfs extends modelCfs {
	public function requireConfirm() {
		$destData = frameCfs::_()->getModule('subscribe')->getDestByKey( $this->getCode() );
		return $destData['require_confirm'];
	}
}