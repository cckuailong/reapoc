<?php
class tableUsage_statUms extends tableUms{
    public function __construct() {

        $this->_table = '@__usage_stat';
        $this->_id = 'id';
        $this->_alias = 'ums_icons';
        $this->_addField('id', 'int', 'int', '11', __('Usage id', UMS_LANG_CODE))
               ->_addField('code', 'varchar', 'varchar', '200', __('Code', UMS_LANG_CODE))
               ->_addField('visits', 'int', 'int', '11', __('Visits Count', UMS_LANG_CODE));
    }
}

