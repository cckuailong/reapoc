<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules;

if (!defined('ABSPATH')) {
    exit;
}

class Widget extends \WP_Widget {

    function __construct() {
        parent::__construct(
                'iheu_widget',
                __('Image Hover Effects Ultimate', 'iheu_widget_widget'),
                array('description' => __('Image Hover Effects Ultimate Widget', 'iheu_widget_widget'),)
        );
    }

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        echo $args['before_widget'];
        echo \OXI_IMAGE_HOVER_PLUGINS\Classes\Bootstrap::instance()->shortcode_render($title, 'user');
        echo $args['after_widget'];
    }

    public function iheu_widget_widget() {
        register_widget($this);
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('1', 'iheu_widget_widget');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Style ID:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }

}
