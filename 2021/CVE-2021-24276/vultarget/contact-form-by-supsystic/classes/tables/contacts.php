<?php
class tableContactsCfs extends tableCfs {
    public function __construct() {
        $this->_table = '@__contacts';
        $this->_id = 'id';
        $this->_alias = 'sup_contacts';
        $this->_addField('id', 'text', 'int')
				->_addField('form_id', 'text', 'int')
				->_addField('fields', 'text', 'text')
				->_addField('ip', 'text', 'text')
				->_addField('url', 'text', 'text')

				->_addField('date_created', 'text', 'text');
    }
}