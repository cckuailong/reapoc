<?php
/**
 * Adds OTP widget.
 */
class RM_Form_Widget extends WP_Widget
{
    /**
     * Register widget with WordPress.
     */
    
    function __construct()
    {
        parent::__construct(
            'rm_form_widget', // Base ID
            __('RegistrationMagic Form', 'custom-registration-form-builder-with-submission-manager'), // Name
            array('description' => __('Attaches RegistrationMagic form.', 'custom-registration-form-builder-with-submission-manager'),) // Args
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
        echo $args['before_widget'];
        if(isset($instance['rm_form']) && !empty($instance['rm_form'])){
                echo do_shortcode("[RM_Form id='".$instance['rm_form']."']");
        }
        
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
   
        $rm_form=  isset($instance['rm_form']) ? $instance['rm_form']:"";
        $title = !empty($instance['title']) ? $instance['title'] : __('RegistrationMagic Form', 'custom-registration-form-builder-with-submission-manager');
        wp_enqueue_script('rm_front');
        ?>
        <p>
            <select name="<?php echo $this->get_field_name('rm_form'); ?>">
                <option value="">Select Form</option>
           <?php  $forms=RM_Utilities::get_forms_dropdown(new RM_Services()); 
                  foreach($forms as $index=>$form_name):
                      $selected= $index==$rm_form ? 'selected' : "";
            ?>
                <option value="<?php echo $index ?>" <?php echo $selected; ?>><?php echo $form_name; ?></option>
            <?php
                  endforeach;  
           ?>
            </select> 
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
        $instance['rm_form'] = (!empty($new_instance['rm_form']) ) ? $new_instance['rm_form'] : '';
        return $instance;
    }
} // class Foo_Widget