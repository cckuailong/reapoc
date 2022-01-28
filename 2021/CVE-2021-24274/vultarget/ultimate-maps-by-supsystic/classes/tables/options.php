<?php
class tableOptionsUms extends tableUms {
     public function __construct() {
        $this->_table = '@__options';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_opt';
        $this->_addField('id', 'text', 'int', 0, __('ID', UMS_LANG_CODE))->
                _addField('code', 'text', 'varchar', '', __('Code', UMS_LANG_CODE), 64)->
                _addField('value', 'text', 'varchar', '', __('Value', UMS_LANG_CODE), 134217728)->
                _addField('label', 'text', 'varchar', '', __('Label', UMS_LANG_CODE), 255)->
                _addField('params', 'text', 'text', '', __('Params', UMS_LANG_CODE) )->
                _addField('description', 'text', 'text', '', __('Description', UMS_LANG_CODE))->
                _addField('htmltype_id', 'selectbox', 'text', '', __('Type', UMS_LANG_CODE))->
				_addField('cat_id', 'hidden', 'int', '', __('Category ID', UMS_LANG_CODE))->
				_addField('sort_order', 'hidden', 'int', '', __('Sort Order', UMS_LANG_CODE))->
				_addField('value_type', 'hidden', 'varchar', '', __('Value Type', UMS_LANG_CODE));;
    }
}
?>
