<?php
class tableMarker_groups_relationUms extends tableUms{
	public function __construct() {

		$this->_table = '@__marker_groups_relation';
		$this->_id = 'id';
		$this->_alias = 'ums_mrgrr';
		$this->_addField('id', 'int', 'int', '11', '')
			->_addField('marker_id', 'int', 'int', '11', '')
			->_addField('groups_id', 'int', 'int', '11', '');
	}
}

