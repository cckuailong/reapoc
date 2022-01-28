<?php
class maps_widgetUms extends moduleUms {
	public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('umsMapsWidget');
    }    
}
/**
 * Maps widget class
 */
class umsMapsWidget extends WP_Widget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'umsMapsWidget', 
            'description' => __('Displays Most Viewed Products', UMS_LANG_CODE)
        );
		parent::__construct( 'umsMapsWidget', UMS_WP_PLUGIN_NAME, $widgetOps );
    }
    public function widget($args, $instance) {
        frameUms::_()->getModule('maps_widget')->getView()->displayWidget($instance);
    }
    public function form($instance) {
        frameUms::_()->getModule('maps_widget')->getView()->displayForm($instance, $this);
    }
	public function update($new_instance, $old_instance) {
		//frameUms::_()->getModule('supsystic_promo')->getModel()->saveUsageStat('map.widget.update');
		return $new_instance;
	}
}