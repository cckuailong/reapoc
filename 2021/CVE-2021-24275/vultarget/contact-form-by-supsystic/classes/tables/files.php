<?php
class tableFilesCfs extends tableCfs {
    public function __construct() {
        $this->_table = '@__files';
        $this->_id = 'id';
        $this->_alias = 'sup_files';
        $this->_addField('id', 'text', 'int')
				->_addField('fid', 'text', 'int')
				->_addField('field_name', 'text', 'varchar')
			
				->_addField('name', 'text', 'varchar')
				->_addField('dest_name', 'text', 'varchar')
				->_addField('path', 'text', 'varchar')
			
				->_addField('mime_type', 'text', 'varchar')
				->_addField('size', 'text', 'int')
				->_addField('hash', 'text', 'varchar')
				->_addField('date_created', 'text', 'varchar');
    }
}