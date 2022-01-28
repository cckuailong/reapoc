<?php
class forms_widgetCfs extends moduleCfs {
    public function init() {
        parent::init();
        add_action('widgets_init', array($this, 'registerWidget'));
    }
    public function registerWidget() {
        return register_widget('formsWidgetWpCfs');
    }
}
/**
 * Forms Widget class
 */
class formsWidgetWpCfs extends WP_Widget {
    public function __construct() {
        $widgetOps = array( 
            'classname' => 'formsWidgetWpCfs', 
            'description' => __('Display Contact Form', CFS_LANG_CODE)
        );
        $control_ops = array(
            'id_base' => 'formsWidgetWpCfs'
        );
		parent::__construct( 'formsWidgetWpCfs', CFS_WP_PLUGIN_NAME, $widgetOps );
    }
    public function widget($args, $instance) {
        frameCfs::_()->getModule('forms_widget')->getView()->displayWidget($args, $instance);
    }
    public function update($new_instance, $old_instance) {
        return $new_instance;
    }
    public function form($instance) {
        frameCfs::_()->getModule('forms_widget')->getView()->displayForm($instance, $this);
    }
}

