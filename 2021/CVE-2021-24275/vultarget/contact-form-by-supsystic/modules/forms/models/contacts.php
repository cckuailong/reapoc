<?php
class contactsModelCfs extends modelCfs {
	public function __construct() {
		$this->_setTbl('contacts');
	}
	protected function _afterGetFromTbl($row) {
		if(isset($row['fields']) && !empty($row['fields'])) {
			$row['fields'] = utilsCfs::decodeArrayTxt($row['fields']);
		}
		return $row;
	}
	public function supGetById($id, $customTable = '') {
    global $wpdb;
  	$res = $wpdb->get_results(
	  		$wpdb->prepare("SELECT * FROM {$wpdb->prefix}cfs_contacts WHERE id = %s", $id), ARRAY_A
  	);
		if ($res) {
			foreach ($res as $key => $row) {
				$res = $this->_afterGetFromTbl($row);
			}
		}
		return $res;
  }
	public function supGetByFormId($id, $customTable = '') {
    global $wpdb;
  	$res = $wpdb->get_results(
	  		$wpdb->prepare("SELECT * FROM {$wpdb->prefix}cfs_contacts WHERE form_id = %s", $id), ARRAY_A
  	);
		return $res;
  }
	public function getSimpleList($where = array(), $params = array()) {
		if($where)
			$this->setWhere ($where);
		return $this->setSelectFields('*')->getFromTbl( $params );
	}
	public function setSimpleGetFields() {
		$this->setSelectFields('*');
		return parent::setSimpleGetFields();
	}
}
