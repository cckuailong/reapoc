<?php

/**
 * Adds Login Button Widget
 */
class RM_Login_Btn_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'rm_login_btn_widget', // Base ID
                __('RegistrationMagic Login Button', 'custom-registration-form-builder-with-submission-manager'), // Name
                array('description' => __('Login Button', 'custom-registration-form-builder-with-submission-manager'),) // Args
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
    public function widget($args, $instance) {
        echo $args['before_widget'];

        include RM_PUBLIC_DIR . "widgets/html/login_btn.php";

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        wp_enqueue_script('rm_login_btn_widget',RM_BASE_URL."public/js/login_btn_widget.js",array('jquery'));
        $title = !empty($instance['title']) ? $instance['title'] : __('RegistrationMagic Login Button', 'custom-registration-form-builder-with-submission-manager');
        $login_label= isset($instance['login_label']) ? $instance['login_label'] : __('Login', 'custom-registration-form-builder-with-submission-manager');
        $login_method= isset($instance['login_method']) ? $instance['login_method'] : __('popup', 'custom-registration-form-builder-with-submission-manager');
        $login_url= isset($instance['login_url']) ? $instance['login_url'] : 0;
        $logout_label= isset($instance['logout_label']) ? $instance['logout_label'] : __('Logout', 'custom-registration-form-builder-with-submission-manager');
        $display_card= isset($instance['display_card'])  ? $instance['display_card'] : 1;
    ?>

           <p> <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'custom-registration-form-builder-with-submission-manager'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"></p>

        <div>
            <div class="rm-logged-out-view">
                <div>
                    <h3><?php _e('Logged Out View', 'custom-registration-form-builder-with-submission-manager') ?></h3>
                </div>
                <p>
                    <label for="<?php echo $this->get_field_name('rm_login_label'); ?>"><?php _e('Login Label', 'custom-registration-form-builder-with-submission-manager'); ?></label>
                    <input type="text" name="<?php echo $this->get_field_name('login_label'); ?>" id="<?php echo $this->get_field_name('rm_login_label'); ?>" value="<?php echo $login_label; ?>" class="widefat" />
                    <span class="rm-widget-helptext"><?php _e('Label of the button when user is in logged out state.', 'custom-registration-form-builder-with-submission-manager') ?></span>
                </p>
                <p>
                    <label for="rm_login_method" class="rm-widget-label-fw"><?php _e('Login Method', 'custom-registration-form-builder-with-submission-manager'); ?></label>
                    <input class="rm_login_method" onchange="rmw_login_method_change(this)" type="radio" name="<?php echo $this->get_field_name('login_method'); ?>" <?php echo $login_method=='popup' ? 'checked' : '';  ?> value="popup" /> <?php _e('Popup', 'custom-registration-form-builder-with-submission-manager'); ?>
                    <input class="rm_login_method" onchange="rmw_login_method_change(this)" type="radio" value="url" name="<?php echo $this->get_field_name('login_method'); ?>" <?php echo $login_method=='url' ? 'checked' : '';  ?> id="rm_login_method_url" /> <?php _e('URL', 'custom-registration-form-builder-with-submission-manager'); ?>
                    
                </p>
                <p id="<?php echo $this->get_field_name('url_options'); ?>" style="<?php if($login_method=='url') echo 'display:none;'; else 'display:block;'; ?>">
                    <span class="rm-widget-helptext"><?php _e('Define what happens when user clicks login button. Popup will open popup box with login fields.', 'custom-registration-form-builder-with-submission-manager'); ?></span>
                </p>
                <p id="<?php echo $this->get_field_name('url_options'); ?>" style="<?php if($login_method!='url') echo 'display:none;'; else 'display:block;'; ?>">
                    <label for="rm_logout_label"><?php _e('Login Page URL', 'custom-registration-form-builder-with-submission-manager'); ?></label>
                    <?php $pages= RM_Utilities::wp_pages_dropdown();?>
                    <select name="<?php echo $this->get_field_name('login_url'); ?>" class="widefat">
                        <?php foreach($pages as $index=>$page): ?>
                                <option <?php echo $login_url==$index ? 'selected':''; ?> value="<?php echo $index; ?>"><?php echo $page; ?></option>
                        <?php endforeach; ?>    
                    </select>
                    <span class="rm-widget-helptext"><?php _e('Make sure the page you selected has login box.', 'custom-registration-form-builder-with-submission-manager'); ?></span>
                </p>
            </div>
            
            <div class="rm-logged-in-view">
                <div>
                    <h3><?php _e('Logged In View', 'custom-registration-form-builder-with-submission-manager'); ?></h3>
                </div>
                
                <p>
                    <label for="rm_logout_label"><?php _e('Logout Label', 'custom-registration-form-builder-with-submission-manager'); ?></label>
                    <input type="text" name="<?php echo $this->get_field_name('logout_label'); ?>" id="rm_logout_label" value="<?php echo $logout_label; ?>" class="widefat"/>
                    <span class="rm-widget-helptext"><?php _e('Label of the button when user is in logged in state.', 'custom-registration-form-builder-with-submission-manager'); ?></span>
                </p>
                
                <p>
                    <label for="rm_user_card"><?php _e('Display User card on hover', 'custom-registration-form-builder-with-submission-manager'); ?></label>
                    <input type="checkbox" id="rm_user_card" value="1" name="<?php echo $this->get_field_name('display_card'); ?>" <?php echo $display_card==1 ? 'checked' : ''; ?>  />
                    <span class="rm-widget-helptext"><?php _e('Displays user information card when user hovers cursor above the button.', 'custom-registration-form-builder-with-submission-manager'); ?></span>
                </p>
                
            </div>

        </div>

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
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['login_label'] = (!empty($new_instance['login_label'])) ? strip_tags($new_instance['login_label']) : __('Login', 'custom-registration-form-builder-with-submission-manager');
        $instance['login_method']= (!empty($new_instance['login_method'])) ? strip_tags($new_instance['login_method']) : __('popup', 'custom-registration-form-builder-with-submission-manager');
        $instance['login_url']= (!empty($new_instance['login_url'])) ? strip_tags($new_instance['login_url']) : '';
        $instance['logout_label']= (!empty($new_instance['logout_label'])) ? strip_tags($new_instance['logout_label']) : __('Logout', 'custom-registration-form-builder-with-submission-manager');
        $instance['display_card']= isset($new_instance['display_card']) ? 1 : 0;
        return $instance;
    }

}

// class Foo_Widget
