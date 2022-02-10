<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function lfb_widget_init() {
    register_widget('lfb_widget');
}
add_action('widgets_init', 'lfb_widget_init');
/**
 * Lead Form Builder Widget
 */
class lfb_widget extends WP_Widget {
    /** constructor */
    function __construct() {
        $widget_ops = array(
            'classname' => 'lfb_widget',
            'description' => 'Form Builder Widget'
        );
        parent::__construct('lfb-form-builder', 'Lead Form Builder', $widget_ops);
    }

    function widget($args, $instance) {
        extract($args);
        echo $before_widget;
        // Widget title
        $lfb_shortcode = isset($instance['lfb_shortcode'])?$instance['lfb_shortcode']:'';

        ?>
        <!--Start Review Thumb-->
        <div class="lfb-widget-title">
            <h4 class="widgettitle">
            <span><?php echo apply_filters('widget_title', $instance['title'] ); ?></span>
           </h4>  

           <?php do_shortcode($lfb_shortcode); ?>   
        </div>
      <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['lfb_shortcode'] = esc_attr($new_instance['lfb_shortcode']);
        return $instance;
    }

    function form($instance) {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : 'Contact Us Form';
        $lfb_shortcode = isset($instance['lfb_shortcode']) ? esc_attr($instance['lfb_shortcode']) : '';
        $shortcode = "<a href='".admin_url('admin.php?page=wplf-plugin-menu')."'>Go Shortcode</a>";

     
        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title','lead_form_builder'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
    
        <p>
           <label for="<?php echo $this->get_field_id('lfb_shortcode'); ?>"><?php _e('Lead Form Builder ['.$shortcode.']','lead_form_builder'); ?>
        </label>
        <textarea  name="<?php echo $this->get_field_name('lfb_shortcode'); ?>" id="<?php echo $this->get_field_id('lfb_shortcode'); ?>"  class="widefat" ><?php echo $lfb_shortcode; ?></textarea>
        </p>
        <p>     
                <input type="hidden" id="<?php echo $this->get_field_id('show_type'); ?>" name="<?php echo $this->get_field_name('show_type'); ?>" value="listing"/>
        </p>
        <?php
    }
}
?>
