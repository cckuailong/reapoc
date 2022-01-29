<?php
/**
 * Adds OTP widget.
 */
class RM_OTP_Widget extends WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    
    function __construct()
    {
        parent::__construct(
            'rm_otp_widget', // Base ID
            __('RegistrationMagic Login', 'custom-registration-form-builder-with-submission-manager'), // Name
            array('description' => __('Login system for RegistrationMagic form submissions with OTP login for non-WP users.', 'custom-registration-form-builder-with-submission-manager'),) // Args
        );
    }
    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        wp_enqueue_script('rm_front');
        wp_enqueue_style( 'style_rm_otpw', RM_BASE_URL."public/widgets/css/otp.css",array(), RM_PLUGIN_VERSION, 'all');
        wp_enqueue_script( 'script_rm_otpw', RM_BASE_URL."public/widgets/js/otp.js",array(), RM_PLUGIN_VERSION, 'all');
        
        echo $args['before_widget'];
        
        $rm_public = new RM_front_service(null);
        
        $data = new stdClass;
        $data->rm_public = $rm_public;
        $data->args = $args;
        $data->instance = $instance;
        $data->uid = "rm_otpw_inst_".$this->number;
        
        include RM_PUBLIC_DIR."widgets/html/otp.php";
        
        echo $args['after_widget'];
    }
    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('RegistrationMagic Login', 'custom-registration-form-builder-with-submission-manager');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','custom-registration-form-builder-with-submission-manager'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
} // class Foo_Widget