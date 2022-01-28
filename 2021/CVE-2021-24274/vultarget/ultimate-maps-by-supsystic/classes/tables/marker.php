<?php
class tableMarkerUms extends tableUms{
    public function __construct() {
        $this->_table = '@__markers';
        $this->_id = 'id';
        $this->_alias = 'toe_mr';
        $this->_addField('id', 'int', 'int', '11', __('Marker ID', UMS_LANG_CODE))
                ->_addField('title', 'varchar', 'varchar', '255', __('Marker name', UMS_LANG_CODE))
                ->_addField('description', 'text', 'text', '', __('Description Of Marker', UMS_LANG_CODE))
                ->_addField('coord_x', 'varchar', 'varchar', '50', __('X coordinate of marker(lng)', UMS_LANG_CODE))
                ->_addField('coord_y', 'varchar', 'varchar', '50', __('Y coordinate of marker(lat)', UMS_LANG_CODE))
                ->_addField('icon', 'varchar', 'varchar', '255', __('Path of icon file', UMS_LANG_CODE))
                ->_addField('map_id', 'int', 'int', '11', __('Map Id', UMS_LANG_CODE))                
                ->_addField('address', 'text', 'text', '', __('Marker Address', UMS_LANG_CODE))                
                ->_addField('marker_group_id', 'int', 'int', '11', __("Id of Marker's group", UMS_LANG_CODE))
                ->_addField('animation','int','int','0', __('Animation', UMS_LANG_CODE))
                ->_addField('params','text','text','', __('Params', UMS_LANG_CODE))
				->_addField('sort_order','int','int','0', __('Sort Order', UMS_LANG_CODE))
                ->_addField('create_date','datetime','datetime','',  __('Creation date', UMS_LANG_CODE))
				->_addField('period_from', 'datetime', 'datetime', null, __('Period date from', UMS_LANG_CODE))
				->_addField('period_to', 'datetime', 'datetime', null, __('Period date to', UMS_LANG_CODE))
				->_addField('hash', 'varchar', '32', __('Import Kml Layer unique index', UMS_LANG_CODE))
                ->_addField('user_id','int','int','11',  __('User who created marker', UMS_LANG_CODE));
    }
}

