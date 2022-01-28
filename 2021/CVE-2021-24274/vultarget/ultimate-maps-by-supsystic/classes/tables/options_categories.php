<?php
class tableOptions_categoriesUms extends tableUms {
    public function __construct() {
        $this->_table = '@__options_categories';
        $this->_id = 'id';     
        $this->_alias = 'toe_opt_cats';
        $this->_addField('id', 'hidden', 'int', 0, __('ID', UMS_LANG_CODE))
            ->_addField('label', 'text', 'varchar', 0, __('Method', UMS_LANG_CODE), 128);
    }
}
?>
