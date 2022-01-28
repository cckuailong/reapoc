<?php
class maps_widgetViewUms extends viewUms {
    public function displayWidget($instance) {
		if(isset($instance['id']) && $instance['id']) {
			foreach($instance as $key => $val) {
				if(empty($instance[$key])) {
					unset($instance[$key]);
				}
			}
			echo frameUms::_()->getModule('maps')->drawMapFromShortcode($instance);
		}
    }
    public function displayForm($data, $widget) {
		frameUms::_()->addStyle('maps_widget', $this->getModule()->getModPath(). 'css/maps_widget.css');

		$maps = frameUms::_()->getModule('maps')->getModel()->getAllMaps();
		$mapsOpts = array();
		if(empty($maps)) {
			$mapsOpts[0] = __('You have no maps', UMS_LANG_CODE);
		} else {
			foreach($maps as $map) {
				$mapsOpts[ $map['id'] ] = $map['title'];
			}
		}
		$this->assign('mapsOpts', $mapsOpts);
        $this->displayWidgetForm($data, $widget);
    }
}