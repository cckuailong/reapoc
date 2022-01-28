<?php

namespace ProfilePress\Core\Widgets;

use ProfilePress\Core\Classes\FormRepository as FR;
use WP_Widget;

class Form extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'pp_form',
            esc_html__('ProfilePress Form', 'wp-user-avatar'),
            array('description' => esc_html__('Easily add your ProfilePress forms to widget areas.', 'wp-user-avatar'))
        );
    }

    public function widget($args, $instance)
    {
        $chosen_form = sanitize_text_field($instance['chosen_form']);

        $hide_widget = $instance['hide'];

        if ('yes' == $hide_widget && is_user_logged_in()) return;

        echo $args['before_widget'];

        if ( ! empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        echo do_shortcode($chosen_form);

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @param array $instance
     *
     * @return void
     */
    public function form($instance)
    {
        $title       = ! empty($instance['title']) ? $instance['title'] : '';
        $hide        = ! empty($instance['hide']) ? $instance['hide'] : '';
        $chosen_form = ! empty($instance['chosen_form']) ? $instance['chosen_form'] : '';

        $login_form_ids        = FR::get_form_ids(FR::LOGIN_TYPE);
        $registration_form_ids = FR::get_form_ids(FR::REGISTRATION_TYPE);
        $password_reset_ids    = FR::get_form_ids(FR::PASSWORD_RESET_TYPE);
        $edit_profile_form_ids = FR::get_form_ids(FR::EDIT_PROFILE_TYPE);
        $melange_ids           = FR::get_form_ids(FR::MELANGE_TYPE);

        if (empty($login_form_ids) || empty($registration_form_ids) || empty($password_reset_ids) || empty($edit_profile_form_ids) || empty($melange_ids)) {
            echo '<p>' . __(apply_filters('ppress_no_form_widget', 'No ProfilePress form is available.'), 'wp-user-avatar') . '</p>';

            return;
        }
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-user-avatar'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo sanitize_text_field($title); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('chosen_form'); ?>"><?php _e('Select Form', 'wp-user-avatar'); ?></label><br/>
            <select class="widefat" id="<?php echo $this->get_field_id('chosen_form'); ?>" name="<?php echo $this->get_field_name('chosen_form'); ?>">
                <?php
                if ( ! empty($login_form_ids)) {
                    printf('<optgroup label="%s">', esc_html__('Login Form', 'wp-user-avatar'));
                    foreach ($login_form_ids as $login_form_id) {
                        $key = sprintf("[profilepress-login id=%s]", $login_form_id);
                        printf('<option value="%s" %s>%s</option>',
                            $key,
                            selected($key, $chosen_form, false),
                            FR::get_name($login_form_id, FR::LOGIN_TYPE)
                        );
                    }
                    echo '</optgroup>';
                }

                if ( ! empty($registration_form_ids)) {
                    printf('<optgroup label="%s">', esc_html__('Registration Form', 'wp-user-avatar'));
                    foreach ($registration_form_ids as $registration_form_id) {
                        $key = sprintf("[profilepress-registration id=%s]", $registration_form_id);
                        printf('<option value="%s" %s>%s</option>',
                            $key,
                            selected($key, $chosen_form, false),
                            FR::get_name($registration_form_id, FR::REGISTRATION_TYPE)
                        );
                    }
                    echo '</optgroup>';
                }

                if ( ! empty($password_reset_ids)) {
                    printf('<optgroup label="%s">', esc_html__('Password Reset Form', 'wp-user-avatar'));
                    foreach ($password_reset_ids as $password_reset_id) {
                        $key = sprintf("[profilepress-password-reset id=%s]", $password_reset_id);
                        printf('<option value="%s" %s>%s</option>',
                            $key,
                            selected($key, $chosen_form, false),
                            FR::get_name($password_reset_id, FR::PASSWORD_RESET_TYPE)
                        );
                    }
                    echo '</optgroup>';
                }

                if ( ! empty($edit_profile_form_ids)) {
                    printf('<optgroup label="%s">', esc_html__('Edit Profile Form', 'wp-user-avatar'));
                    foreach ($edit_profile_form_ids as $edit_profile_form_id) {
                        $key = sprintf("[profilepress-edit-profile id=%s]", $edit_profile_form_id);
                        printf('<option value="%s" %s>%s</option>',
                            $key,
                            selected($key, $chosen_form, false),
                            FR::get_name($edit_profile_form_id, FR::EDIT_PROFILE_TYPE)
                        );
                    }
                    echo '</optgroup>';
                }

                if ( ! empty($melange_ids)) {
                    printf('<optgroup label="%s">', esc_html__('Melange Form', 'wp-user-avatar'));
                    foreach ($melange_ids as $melange_id) {
                        $key = sprintf("[profilepress-melange id=%s]", $melange_id);
                        printf('<option value="%s" %s>%s</option>',
                            $key,
                            selected($key, $chosen_form, false),
                            FR::get_name($melange_id, FR::MELANGE_TYPE)
                        );
                    }
                    echo '</optgroup>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('hide'); ?>"><?php _e('Hide when a user is logged in:', 'wp-user-avatar'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('hide'); ?>" name="<?php echo $this->get_field_name('hide'); ?>" type="checkbox" value="yes" <?php checked($hide, 'yes'); ?>>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance                = array();
        $instance['title']       = ( ! empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['hide']        = ( ! empty($new_instance['hide'])) ? sanitize_text_field($new_instance['hide']) : '';
        $instance['chosen_form'] = ( ! empty($new_instance['chosen_form'])) ? sanitize_text_field($new_instance['chosen_form']) : '';

        return $instance;
    }
}