<?php
class marker_groups_relationModelUms extends modelUms {
	function __construct() {
		$this->_setTbl('marker_groups_relation');
	}

	public function getRelationsByMarkerId($id){
//		$relations =  frameUms::_()->getTable('marker_groups_relation')->get('groups_id', 'marker_id = ' . $id, '', 'col');
			global $wpdb;
			$relations = $wpdb->get_col("SELECT groups_id FROM {$wpdb->prefix}ums_marker_groups_relation AS ums_mrgrr WHERE " . $wpdb->prepare("marker_id = %s", $id));
			return $relations;
	}

}
