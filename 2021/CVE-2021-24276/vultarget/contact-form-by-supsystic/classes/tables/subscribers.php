<?php
class tableSubscribersCfs extends tableCfs {
    public function __construct() {
        $this->_table = '@__subscribers';
        $this->_id = 'id';
        $this->_alias = 'sup_subscribers';
        $this->_addField('id', 'hidden', 'int')
			->_addField('username', 'text', 'varchar')
			->_addField('email', 'text', 'varchar')
			->_addField('hash', 'text', 'varchar')
			->_addField('activated', 'text', 'int')
			->_addField('form_id', 'text', 'int')
			->_addField('date_created', 'text', 'varchar')
			->_addField('all_data', 'text', 'text');
    }
}