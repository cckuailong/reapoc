<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Core\ShortcodeParser\Builder\FrontendProfileBuilder;

/**
 * Parse the individual profile shortcode of "Edit profile" builder
 */
class FrontendProfileTag
{
    public function __construct()
    {
        add_shortcode('profilepress-user-profile', array($this, 'user_profile_parser'));

        add_action('wp', array($this, 'set_up_detected_profile'));

        add_filter('pre_get_document_title', array($this, 'rewrite_profile_title'), 9999999999999999999, 1);
        add_filter('wp_title', array($this, 'rewrite_profile_title'), 9999999999999999999, 1);
    }

    /**
     * Get currently logged in user object_data
     *
     * @return \WP_User
     */
    function get_current_user_data()
    {
        return wp_get_current_user();
    }

    public function set_up_detected_profile()
    {
        global $ppress_frontend_profile_user_obj;

        $who = get_query_var('who');

        $user = '';

        if (empty($who)) {
            if (is_user_logged_in()) {
                $user = $this->get_current_user_data();
            } elseif ( ! is_user_logged_in()) {
                $profile_slug_with_slash = ppress_get_profile_slug() . '/';

                if (strpos($_SERVER['REQUEST_URI'], $profile_slug_with_slash) !== false) {
                    wp_safe_redirect(wp_login_url());
                    exit;
                }
            }
        } else {

            $username_or_nicename = apply_filters('ppress_frontend_user_profile_username', rawurldecode($who));

            // attempt to check if the slug is a nice-name and then retrieve the username of the user.
            $check = ppress_is_slug_nice_name($username_or_nicename);
            if (is_string($check)) {
                $username_or_nicename = $check;
            }

            $user = get_user_by('login', $username_or_nicename);
        }

        $user = apply_filters('ppress_frontend_profile_wp_user_object', $user);

        $ppress_frontend_profile_user_obj = $user;

        FrontendProfileBuilder::get_instance($user);
    }

    /**
     * Shortcode callback function to parse the shortcode.
     *
     * @param $atts
     *
     * @return string
     */
    public function user_profile_parser($atts)
    {
        if ( ! is_user_logged_in() && ppress_get_setting('disable_guests_can_view_profiles') == 'on') {
            return wpautop(sprintf(
                __('This content is available to members only. Please <a href="%1$s">login</a> or <a href="%2$s">register</a> to view this area.', 'wp-user-avatar'),
                ppress_login_url(),
                ppress_registration_url()
            ));
        }

        if (is_user_logged_in() && ppress_get_setting('disable_members_can_view_profiles') == 'on' && ! ppress_is_my_own_profile()) {
            return wpautop(esc_html__('You are not authorized to access this area.', 'wp-user-avatar'));
        }

        $id = absint($atts['id']);

        $user = '';

        if ( ! empty($atts['user-id'])) {

            $user = apply_filters('ppress_frontend_profile_wp_user_object', get_user_by('ID', absint($atts['user-id'])));

            // we are instantiating the class directly because it has already been called by set_up_detected_profile hooked into wp
            // action. If we had used the singleton instance method, it would have returned the previous instance.
            new FrontendProfileBuilder($user);
        }

        do_action('ppress_frond_end_profile_id', $id);

        $attribution_start = apply_filters('ppress_hide_attribution', '<!-- This WordPress front-end profile is built and powered by ProfilePress WordPress plugin - https://profilepress.net -->' . "\r\n");
        $attribution_end   = apply_filters('ppress_hide_attribution', "\r\n" . '<!-- / ProfilePress WordPress plugin. -->' . "\r\n");
        $css               = self::get_user_profile_css($id);

        // call the registration structure/design
        return apply_filters('ppress_front_end_profile', $attribution_start . $css . $this->get_user_profile_structure($id) . $attribution_end, $user, $id);
    }


    /**
     * Get the registration structure from the database
     *
     * @param int $id
     *
     * @return string
     */
    public static function get_user_profile_structure($id)
    {
        if (FR::is_drag_drop($id, FR::USER_PROFILE_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::USER_PROFILE_TYPE);
            if ( ! $form_instance) return esc_html__('Form class not found. Please check if this user profile actually exist in ProfilePress.', 'wp-user-avatar');
            $user_profile_structure = $form_instance->form_structure();
        } else {
            $user_profile_structure = FR::get_form_meta($id, FR::USER_PROFILE_TYPE, FR::FORM_STRUCTURE);
        }

        return do_shortcode($user_profile_structure);
    }


    /**
     * Get the CSS stylesheet for the ID registration
     *
     * @return mixed
     */
    public static function get_user_profile_css($id)
    {
        if (FR::is_drag_drop($id, FR::USER_PROFILE_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::USER_PROFILE_TYPE);
            if ( ! $form_instance) return '';
            $user_profile_css = $form_instance->minified_form_css();
        } else {
            $user_profile_css = FR::get_form_meta($id, FR::USER_PROFILE_TYPE, FR::FORM_CSS);
        }

        return "<style type=\"text/css\">\r\n $user_profile_css \r\n</style>";
    }

    /** Rewrite the title of the profile */
    public function rewrite_profile_title($title)
    {
        global $post, $ppress_frontend_profile_user_obj;

        // if currently viewed page is the page with the front-end profile, rewrite the title accordingly.
        if (@$post->ID == ppress_get_setting('set_user_profile_shortcode')
            || has_shortcode('profilepress-user-profile', @$post->post_content)
        ) {

            $user_object = $ppress_frontend_profile_user_obj;

            if (isset($user_object) && is_object($user_object)) {

                // if first and last name is set, use the combo as title
                if ( ! empty($user_object->first_name) && ! empty($user_object->last_name)) {
                    $title = "$user_object->first_name {$user_object->last_name}";
                } // if either first or last name is set, use either as title
                elseif ( ! empty($user_object->first_name) || ! empty($user_object->last_name)) {
                    $title = "$user_object->first_name {$user_object->last_name}";
                } // else use their username
                else {
                    $title = $user_object->user_login;
                }

                $title = apply_filters('ppress_profile_username_title', self::title_possessiveness($title), $title);
            }
        }

        return $title;
    }

    public static function title_possessiveness($string)
    {
        $string   = trim($string);
        $lastchar = substr($string, -1);

        $profile_string = esc_html__('Profile', 'wp-user-avatar');

        if ('s' == $lastchar) {
            $title = ucwords($string) . "' $profile_string";
        } else {
            $title = ucwords($string) . "'s $profile_string";
        }

        return $title;
    }

    /** Singleton instance */
    static public function get_instance()
    {
        static $instance = false;

        if ( ! $instance) {
            $instance = new self;
        }

        return $instance;
    }
}