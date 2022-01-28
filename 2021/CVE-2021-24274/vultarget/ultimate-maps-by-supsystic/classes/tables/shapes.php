<?php
class tableShapeUms extends tableUms{
    public function __construct() {
        $this->_table = '@__shapes';
        $this->_id = 'id';
        $this->_alias = 'toe_shp';
        $this->_addField('id', 'int', 'int', '11', __('Shape ID', UMS_LANG_CODE))
                ->_addField('title', 'varchar', 'varchar', '255', __('Shape name', UMS_LANG_CODE))
                ->_addField('description', 'text', 'text', '', __('Description of Shape', UMS_LANG_CODE))
                ->_addField('coords', 'text', 'text', '', __('Shape coordinates list', UMS_LANG_CODE))
                ->_addField('type', 'varchar', 'varchar', '30', __('Shape type', UMS_LANG_CODE))
                ->_addField('map_id', 'int', 'int', '11', __('Map Id', UMS_LANG_CODE))
				->_addField('create_date','text','text','',  __('Creation date', UMS_LANG_CODE))
				->_addField('animation','int','int','0', __('Animation', UMS_LANG_CODE))
                ->_addField('params','text','text','', __('Params', UMS_LANG_CODE))
				->_addField('sort_order','int','int','0', __('Sort Order', UMS_LANG_CODE));
    }
}