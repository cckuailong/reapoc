<?php
class tableForms_ratingCfs extends tableCfs {
    public function __construct() {
        $this->_table = '@__forms_rating';
        $this->_id = 'id';
        $this->_alias = 'sup_forms_rating';
        $this->_addField('id', 'text', 'int')
				->_addField('fid', 'text', 'int')
				->_addField('field_name', 'text', 'varchar')
				->_addField('rate', 'text', 'int')
				->_addField('max_rate', 'text', 'int')
				->_addField('date_created', 'text', 'text');
    }
}