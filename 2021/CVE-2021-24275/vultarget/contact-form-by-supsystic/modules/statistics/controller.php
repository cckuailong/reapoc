<?php
class statisticsControllerCfs extends controllerCfs {
	public function add() {
		$res = new responseCfs();
		$connectHash = reqCfs::getVar('connect_hash', 'post');
		$id = reqCfs::getVar('id', 'post');
		if(md5(date('m-d-Y'). $id. NONCE_KEY) != $connectHash) {
			$res->pushError('Some undefined for now.....');
			$res->ajaxExec( true );
		}
		if($this->getModel()->add( reqCfs::get('post') )) {
			// Do nothing for now
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function clearForForm() {
		$res = new responseCfs();
		if($this->getModel()->clearForForm( reqCfs::get('post') )) {
			$res->addMessage(__('Done', CFS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getUpdatedStats() {
		$res = new responseCfs();
		if(($stats = $this->getModel()->getUpdatedStats( reqCfs::get('post') )) !== false) {
			$res->addData('stats', $stats);
			$res->addMessage(__('Done', CFS_LANG_CODE));
		} else
			$res->pushError ($this->getModel()->getErrors());
		$res->ajaxExec();
	}
	public function getCsv() {
		if(($stats = $this->getModel()->getPreparedStats( reqCfs::get('get') )) !== false) {
			$id = (int) reqCfs::getVar('id');
			$form = frameCfs::_()->getModule('forms')->getModel()->supGetById( $id );
			importClassCfs('filegeneratorCfs');
			importClassCfs('csvgeneratorCfs');
			$csvGenerator = new csvgeneratorCfs(sprintf(__('Statistics for %s', CFS_LANG_CODE), htmlspecialchars( $form['label'] )));
			$labels = array(
				'date' => __('Date', CFS_LANG_CODE),
				'views' => __('Views', CFS_LANG_CODE),
				'unique_requests' => __('Unique Views', CFS_LANG_CODE),
				'actions' => __('Actions', CFS_LANG_CODE),
				'conversion' => __('Conversion', CFS_LANG_CODE),
			);
			$row = $cell = 0;
			foreach($labels as $l) {
				$csvGenerator->addCell($row, $cell, $l);
				$cell++;
			}
			$row = 1;
			foreach($stats as $s) {
				$cell = 0;
				foreach($labels as $k => $l) {
					$csvGenerator->addCell($row, $cell, $s[ $k ]);
					$cell++;
				}
				$row++;
			}
			$csvGenerator->generate();
		} else {
			echo implode('<br />', $this->getModel()->getErrors());
		}
		exit();
	}
	public function getStats() {
		$res = new responseCfs();
		$res->addData($this->getModel()->getStats( reqCfs::get('post') ));
		$res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			CFS_USERLEVELS => array(
				CFS_ADMIN => array('clearForForm', 'getUpdatedStats', 'getCsv', 'getStats')
			),
		);
	}
}
