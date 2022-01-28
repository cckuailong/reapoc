<?php
class tableCountriesCfs extends tableCfs {
    public function __construct() {
        $this->_table = '@__countries';
        $this->_id = 'id';
        $this->_alias = 'cfs_countries';
        $this->_addField('id', 'text', 'int')
				->_addField('name', 'text', 'varchar')
				->_addField('iso_code_2', 'text', 'varchar')
				->_addField('iso_code_3', 'text', 'varchar');
    }
}
