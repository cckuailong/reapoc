<?php

namespace ProfilePress\Core\Classes;

/**
 * Indicate which form or social login where user signup from in ser listing page.
 *
 * @package ProfilePress\Core\Classes
 */
class UserSignupLocationListingPage
{
    public function __construct()
    {
        // add custom column to use listing
        add_filter('manage_users_columns', array($this, 'add_column'));
        add_action('manage_users_custom_column', array($this, 'populate_column'), 10, 3);
    }

    public function add_column($columns)
    {
        $column_name           = apply_filters('ppress_signup_via_column', esc_html__('Registered Via', 'wp-user-avatar'));
        $columns['signup_via'] = $column_name;

        return $columns;

    }

    public function populate_column($status, $column_name, $user_id)
    {
        if ('signup_via' == $column_name) {

            $melange_form_id = get_user_meta($user_id, '_pp_signup_melange_via', true);
            $val             = get_user_meta($user_id, '_pp_signup_via', true);

            if ( ! empty($melange_val)) {
                return FormRepository::get_name($melange_form_id, FormRepository::MELANGE_TYPE);
            }

            if ( ! empty($val)) {

                switch ($val) {
                    case 'facebook':
                        $status = esc_html__('Facebook', 'wp-user-avatar');
                        break;

                    case 'twitter':
                        $status = esc_html__('Twitter', 'wp-user-avatar');
                        break;

                    case 'google':
                        $status = esc_html__('Google', 'wp-user-avatar');
                        break;

                    case 'linkedin':
                        $status = esc_html__('LinkedIn', 'wp-user-avatar');
                        break;

                    case 'github':
                        $status = esc_html__('GitHub', 'wp-user-avatar');
                        break;

                    case 'vk':
                        $status = esc_html__('Vkontakte', 'wp-user-avatar');
                        break;
                    case 'tab_widget':
                        $status = esc_html__('ProfilePress Tabbed Widget', 'wp-user-avatar');
                        break;
                    default:
                        return FormRepository::get_name(absint($val), FormRepository::REGISTRATION_TYPE);
                }
            }
        }

        return $status;
    }

    /**
     * @return UserSignupLocationListingPage
     */
    public static function get_instance()
    {
        static $instance;

        if ( ! isset($instance)) {
            $instance = new self;
        }

        return $instance;
    }
}