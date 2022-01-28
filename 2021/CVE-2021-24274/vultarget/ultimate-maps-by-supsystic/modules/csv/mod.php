<?php
class  csvUms extends moduleUms {
	private $_markerHeaders = array();
	private $_mapHeaders = array();

	public function init() {
		dispatcherUms::addFilter('mainAdminTabs', array($this, 'addAdminTab'));
	}
	public function addAdminTab($tabs) {
		$tabs[ $this->getCode() ] = array(
			'label' => __('Maps Import / Export', UMS_LANG_CODE), 'callback' => array($this, 'getTabContent'), 'fa_icon' => 'fa-download', 'sort_order' => 50,
		);
		return $tabs;
	}
	public function getTabContent() {
		frameUms::_()->addScript('admin.csv', $this->getModPath(). 'js/admin.csv.js');
		return $this->getView()->getTabContent();
	}
	/*public function addSettingsBlock($bloks) {
		frameUms::_()->addScript('admin.csv', $this->getModPath(). 'js/admin.csv.js');
		$bloks['csvImportExport'] = $this->getView()->getSettitngsBlockHtml();
		return $bloks;
	}*/
	public function getMarkerHeadersList() {
		if(empty($this->_markerHeaders)) {
			$this->_markerHeaders = array(
				'id' => 'ID',
				'title' => 'Title',
				'description' => 'Description',
				'map_id' =>  'Map ID',
				'address' =>  'Address',
				'coord_x' =>  'Longitude',
				'coord_y' =>  'Latitude',
				'animation' =>  'Animation',

				'icon' =>  'Icon ID',
				'icon_path' =>  'Icon Path',
				'icon_title' =>  'Icon Title',

				'marker_group_id' =>  'Group ID',
				'marker_group_title' =>  'Group Title',
				'marker_group_description' =>  'Group Description',

				'more_info_link' => 'Add More info in description window',
				'icon_fit_standard_size' => 'Fit icon in standard size',
			);
		}
		return $this->_markerHeaders;
	}
	public function getMapHeadersList() {
		if(empty($this->_mapHeaders)) {
			$this->_mapHeaders = array(
				'id' => 'ID',
				'title' => 'Title',
				// params
				'enable_zoom' => 'Enable Zoom',
				'enable_mouse_zoom' => 'Enable Mouse Zoom',
				'description_mouse_hover' => 'Infowindow on mouse over',
				'zoom' => 'Zoom',
				'type' => 'Type',
				'language' => 'Language',
				'map_display_mode' => 'Display mode',
				'map_center' => 'Map Center',
				'infowindow_height' => 'Infowindow Height',
				'infowindow_width' => 'Infowindow Width',
				'enable_marker_clasterer' => 'Enable markers clusterization',
				'markers_list_show_only_visible' => 'Show only visible markers in list',
				'disable_search_table' => 'Disable search in markers list',
				'disable_categories_selection' => 'Disable categories selection',
				'markers_desc_show_get_direction' => 'Get Directions link',
				'markers_desc_show_get_direction' => 'Get Directions link',
				'markers_desc_show_view_on_google_maps' => 'View on Google Maps link',
				'ad_publisher_id' => 'AD Publisher ID (PRO)',
				'ad_pos' => 'AD Position (PRO)',
				'markers_list_type' => 'Markers list type (PRO)',
				'custom_map_controls' => 'Custom map controls (PRO)',
				'stylization' => 'Stylization (PRO)',
				'enable_full_screen_btn' => 'Enable full screen button (PRO)',
				'enable_trafic_layer' => 'Enable trafic layer (PRO)',
				'enable_transit_layer' => 'Enable transit layer (PRO)',
				'enable_bicycling_layer' => 'Enable bicycling layer (PRO)',
				// html options
				'width' => 'Width',
				'width_units' => 'Width units',
				'height' => 'Height',
				'align' => 'Align',
				'margin' => 'Margin',
				'border_color' => 'Border Color',
				'border_width' => 'Border Width',
			);
		}
		return $this->_mapHeaders;
	}
}
