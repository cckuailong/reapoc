<?php
class tableModulesUms extends tableUms {
    public function __construct() {
        $this->_table = '@__modules';
        $this->_id = 'id';     /*Let's associate it with posts*/
        $this->_alias = 'toe_m';
        $this->_addField('label', 'text', 'varchar', 0, __('Label', UMS_LANG_CODE), 128)
                ->_addField('type_id', 'selectbox', 'smallint', 0, __('Type', UMS_LANG_CODE))
                ->_addField('active', 'checkbox', 'tinyint', 0, __('Active', UMS_LANG_CODE))
                ->_addField('params', 'textarea', 'text', 0, __('Params', UMS_LANG_CODE))
                ->_addField('has_tab', 'checkbox', 'tinyint', 0, __('Has Tab', UMS_LANG_CODE))
                ->_addField('description', 'textarea', 'text', 0, __('Description', UMS_LANG_CODE), 128)
                ->_addField('code', 'hidden', 'varchar', '', __('Code', UMS_LANG_CODE), 64)
                ->_addField('ex_plug_dir', 'hidden', 'varchar', '', __('External plugin directory', UMS_LANG_CODE), 255);
    }
}
?>
