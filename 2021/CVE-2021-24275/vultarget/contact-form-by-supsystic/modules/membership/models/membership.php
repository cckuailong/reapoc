<?php
class membershipModelCfs extends modelCfs {
	protected $_memberShipClassName;

	public function __construct() {
		$this->_memberShipClassName = 'SupsysticMembership';
		$this->_setTbl('membership_presets');
	}

	public function isPluginActive() {
		if(class_exists($this->_memberShipClassName)) {
			$tableExistsQuery =  "SHOW TABLES LIKE '@__" . $this->_tbl . "'";
			$results = dbCfs::get($tableExistsQuery);
			if(count($results)) {
				return true;
			}
		}
		return false;
	}

	public function getPluginInstallUrl() {
		return add_query_arg(
			array(
				's' => 'Membership by Supsystic',
				'tab' => 'search',
				'type' => 'term',
			),
			admin_url( 'plugin-install.php' )
		);
	}

	public function updateRow($params) {
		global $wpdb;
		if(isset($params['form_id']) && isset($params['allow_use'])) {
			$allowUse = (int)$params['allow_use'];
			$formId = (int)$params['form_id'];
			if($formId && isset($allowUse)) {
				$tableName = $wpdb->prefix . "cfs_" . $this->_tbl;
		    $data = array(
					'form_id' => $formId,
					'allow_use' => $allowUse,
				);
				$wpdb->delete($tableName, array('form_id' => $formId));
		    $res = $wpdb->insert($tableName, $data);
		    if ($res) {
					return $wpdb->insert_id;;
				}
			}
		}
		return false;
	}
}
