<?php
class tableFormsCfs extends tableCfs {
    public function __construct() {
        $this->_table = '@__forms';
        $this->_id = 'id';
        $this->_alias = 'sup_forms';
        $this->_addField('id', 'text', 'int')
				->_addField('label', 'text', 'varchar')
				->_addField('active', 'text', 'int')
				->_addField('original_id', 'text', 'int')
				->_addField('unique_id', 'text', 'text')
				->_addField('params', 'text', 'text')
				->_addField('html', 'text', 'text')
				->_addField('css', 'text', 'text')
				->_addField('img_preview', 'text', 'text')
				
				->_addField('views', 'text', 'int')
				->_addField('unique_views', 'text', 'int')
				->_addField('actions', 'text', 'int')
				->_addField('ab_id', 'text', 'int')
				
				->_addField('sort_order', 'text', 'int')
				->_addField('date_created', 'text', 'text');
    }
}