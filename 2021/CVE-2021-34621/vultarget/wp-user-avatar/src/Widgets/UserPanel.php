<?php

namespace ProfilePress\Core\Widgets;

class UserPanel extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'pp_user_panel_widget',
            esc_html__('ProfilePress User Panel', 'wp-user-avatar'),
            array(
                'description' => esc_html__('Display currently logged in user\'s avatar and links to logout and edit the profile.', 'wp-user-avatar'),
            ),
            array('width' => 400, 'height' => 350)// Args
        );
    }

    /**
     * Display Widget.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        if ( ! is_user_logged_in()) return;

        $edit_profile_label = ! empty($instance['edit_profile_label']) ? sanitize_text_field($instance['edit_profile_label']) : esc_html__('Edit your Profile', 'wp-user-avatar');
        $logout_label       = ! empty($instance['logout_label']) ? sanitize_text_field($instance['logout_label']) : esc_html__('Log Out', 'wp-user-avatar');

        echo $args['before_widget'];

        $user_data = wp_get_current_user();
        ?>

        <div class="pp-user-panel">
            <?php
            do_action('ppress_before_user_panel_widget');

            if (empty($instance['remove_avatar']) || $instance['remove_avatar'] != 'on') {
                echo '<a href="' . ppress_profile_url() . '">';
                echo '<div class="pp-tab-widget-avatar">';
                echo get_avatar($user_data->ID, 500);
                echo '</div>';
                echo '</a>';
            }
            ?>
            <h3 class="pp-user-panel-title">
                <?php printf(__('Welcome %s', 'wp-user-avatar'), ucfirst($user_data->display_name)); ?>
            </h3>
            <br/>
            <p>
                <a class="pp-tabbed-btn pp-tabbed-btn-inverse" href="<?php echo ppress_edit_profile_url(); ?>"><?= $edit_profile_label ?></a>
            </p>
            <p>
                <a class="pp-tabbed-btn pp-tabbed-btn-inverse" href="<?php echo wp_logout_url(); ?>"><?= $logout_label ?></a>
            </p>
            <?php do_action('ppress_after_user_panel_widget'); ?>
        </div>
        <?php
        echo $args['after_widget'];
    }


    public function form($instance)
    {
        $title              = ! empty($instance['title']) ? sanitize_text_field($instance['title']) : esc_html__('User Panel', 'wp-user-avatar');
        $remove_avatar      = ! empty($instance['remove_avatar']) ? sanitize_text_field($instance['remove_avatar']) : '';
        $edit_profile_label = ! empty($instance['edit_profile_label']) ? sanitize_text_field($instance['edit_profile_label']) : esc_html__('Edit your Profile', 'wp-user-avatar');
        $logout_label       = ! empty($instance['logout_label']) ? sanitize_text_field($instance['logout_label']) : esc_html__('Log Out', 'wp-user-avatar');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <input class="widefat" id="<?php echo $this->get_field_id('remove_avatar'); ?>" name="<?php echo $this->get_field_name('remove_avatar'); ?>" type="checkbox" value="on" <?php checked($remove_avatar, 'on'); ?>>
            <label for="<?php echo $this->get_field_id('remove_avatar'); ?>"><?php _e('Check to remove user profile picture from panel.'); ?></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('edit_profile_label'); ?>"><?php _e('Label for "edit profile" link:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('edit_profile_label'); ?>" name="<?php echo $this->get_field_name('edit_profile_label'); ?>" type="text" value="<?php echo $edit_profile_label; ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('logout_label'); ?>"><?php _e('Label for logout link:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('logout_label'); ?>" name="<?php echo $this->get_field_name('logout_label'); ?>" type="text" value="<?php echo $logout_label; ?>">
        </p>

        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     * @see WP_Widget::update()
     *
     */
    public function update($new_instance, $old_instance)
    {
        $instance                       = array();
        $instance['title']              = ( ! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['remove_avatar']      = ( ! empty($new_instance['remove_avatar'])) ? strip_tags($new_instance['remove_avatar']) : '';
        $instance['edit_profile_label'] = ( ! empty($new_instance['edit_profile_label'])) ? strip_tags($new_instance['edit_profile_label']) : '';
        $instance['logout_label']       = ( ! empty($new_instance['logout_label'])) ? strip_tags($new_instance['logout_label']) : '';

        return $instance;
    }
}