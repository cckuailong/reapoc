<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\FormRepository as FR;

class MemberDirectoryTag
{
    public function __construct()
    {
        add_shortcode('profilepress-member-directory', array($this, 'parser'));

        add_action('init', [$this, 'base64_search_query_params']);
    }

    public function base64_search_query_params()
    {
        if ( ! empty($_GET['ppmd-search'])) {

            $directory_id = absint($_GET['ppmd-search']);

            $url = ppress_get_current_url_raw();

            if ( ! empty($_GET['ppmd-search']) &&
                 ( ! empty($_GET['search-' . $directory_id]) || ! empty(array_filter($_GET['filters'])))
            ) {

                $url = add_query_arg([
                    sprintf('filter%s', absint($_GET['ppmd-search'])) => base64_encode(wp_json_encode($_GET))
                ], ppress_get_current_url_raw());
            }

            wp_safe_redirect($url);

            exit;
        }
    }

    /**
     * @param $atts
     *
     * @return string
     */
    public function parser($atts)
    {
        if (empty($atts['id'])) return esc_html__('No member directory ID specified.');

        $id = absint($atts['id']);

        $attribution_start = apply_filters('ppress_hide_attribution', '<!-- This WordPress member directory is built and powered by ProfilePress WordPress plugin - https://profilepress.net -->' . "\r\n");
        $attribution_end   = apply_filters('ppress_hide_attribution', "\r\n" . '<!-- / ProfilePress WordPress plugin. -->' . "\r\n");
        $css               = self::get_user_profile_css($id);

        return apply_filters('ppress_member_directory', $attribution_start . $css . $this->get_user_profile_structure($id) . $attribution_end, $id);
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
        if (FR::is_drag_drop($id, FR::MEMBERS_DIRECTORY_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::MEMBERS_DIRECTORY_TYPE);
            if ( ! $form_instance) return esc_html__('Member directory class not found. Please check it actually exist in ProfilePress.', 'wp-user-avatar');
            $user_profile_structure = $form_instance->form_structure();
        } else {
            $user_profile_structure = FR::get_form_meta($id, FR::MEMBERS_DIRECTORY_TYPE, FR::FORM_STRUCTURE);
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
        if (FR::is_drag_drop($id, FR::MEMBERS_DIRECTORY_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::MEMBERS_DIRECTORY_TYPE);
            if ( ! $form_instance) return '';
            $css = $form_instance->minified_form_css();
        } else {
            $css = FR::get_form_meta($id, FR::MEMBERS_DIRECTORY_TYPE, FR::FORM_CSS);
        }

        return "<style type=\"text/css\">$css</style>";
    }

    public static function get_instance()
    {
        static $instance = false;

        if ( ! $instance) {
            $instance = new self;
        }

        return $instance;
    }
}