<?php
class tableHeatmapUms extends tableUms{
	public function __construct() {
		$this->_table = '@__heatmaps';
		$this->_id = 'id';
		$this->_alias = 'toe_hmp';
		$this->_addField('id', 'int', 'int', '11', __('Heatmap ID', UMS_LANG_CODE))
			->_addField('map_id', 'int', 'int', '11', __('Map Id', UMS_LANG_CODE))
			->_addField('coords', 'text', 'text', '', __('Heatmap coordinates list', UMS_LANG_CODE))
			->_addField('params','text','text','', __('Params', UMS_LANG_CODE))
			->_addField('create_date','text','text','',  __('Creation date', UMS_LANG_CODE));
	}
}