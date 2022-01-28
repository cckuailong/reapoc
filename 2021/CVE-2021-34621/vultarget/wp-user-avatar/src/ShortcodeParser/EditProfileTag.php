<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\EditUserProfile;
use ProfilePress\Core\Classes\FormRepository as FR;

class EditProfileTag extends FormProcessor
{
    public function __construct()
    {
        add_action('wp', [$this, 'process_edit_profile_form']);

        add_shortcode('profilepress-edit-profile', array($this, 'parse_shortcode'));
    }

    /** Get the current user id */
    public static function get_current_user_id()
    {
        $current_user = wp_get_current_user();

        return $current_user->ID;
    }

    /**
     * Shortcode callback function to parse the shortcode.
     *
     * @param $atts
     *
     * @return string
     */
    public function parse_shortcode($atts)
    {
        do_action('ppress_edit_profile_before_parse_shortcode');

        $id       = absint($atts['id']);
        $redirect = isset($atts['redirect']) ? esc_url_raw($atts['redirect']) : '';

        $response = '';
        if (isset($this->edit_profile_form_error[$id])) {
            $response = $this->edit_profile_form_error[$id];
        }

        if (isset($_GET['edit']) && ($_GET['edit'] == 'true')) {
            $response = html_entity_decode(EditUserProfile::get_success_message($id));
        }

        $response = apply_filters('ppress_edit_profile_status', $response, $id);

        $attribution_start = apply_filters('ppress_hide_attribution', '<!-- This form was created and powered by ProfilePress WordPress plugin - https://profilepress.net -->' . "\r\n");
        $attribution_end   = apply_filters('ppress_hide_attribution', "\r\n" . '<!-- / ProfilePress WordPress plugin. -->' . "\r\n");
        $css               = self::get_edit_profile_css($id);

        $container_div_start = sprintf('<div id="pp-edit-profile-%s-wrap" class="pp-form-container pp-edit-profile-form-wrap">', $id);
        $container_div_end   = '</div>';

        return apply_filters('ppress_edit_profile_form', $attribution_start . $css . $container_div_start . $response . self::get_edit_profile_structure($id, $redirect) . $container_div_end . $attribution_end, $id);
    }

    /**
     * Get the registration structure from the database
     *
     * @param int $id
     * @param string $redirect URL to redirect to after edit profile.
     *
     * @return string
     */
    public static function get_edit_profile_structure($id, $redirect = '')
    {
        $structure = "<input type='hidden' name='editprofile_form_id' value='$id'>";

        if ( ! empty($redirect)) {
            $structure .= "<input type='hidden' name='editprofile_redirect' value='$redirect'>";
        }

        if (FR::is_drag_drop($id, FR::EDIT_PROFILE_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::EDIT_PROFILE_TYPE);
            if ( ! $form_instance) return esc_html__('Form class not found. Please check if this ProfilePress form actually exist.', 'wp-user-avatar');
            $structure .= $form_instance->form_structure();
        } else {
            $structure .= FR::get_form_meta($id, FR::EDIT_PROFILE_TYPE, FR::FORM_STRUCTURE);
        }

        $GLOBALS['pp_edit_profile_form_id'] = $id;

        $structure = do_shortcode($structure);

        $form_tag = "<form data-pp-form-submit=\"editprofile\" id='pp_edit-profile_$id' method='post' autocomplete=\"off\" enctype='multipart/form-data'" . apply_filters('ppress_edit_profile_form_tag', '', $id) . ">";

        return $form_tag . $structure . '</form>';
    }

    /**
     * Get the CSS stylesheet for the ID registration
     *
     * @return mixed
     */

    public static function get_edit_profile_css($id)
    {
        if ( ! isset($id)) return '';

        if (FR::is_drag_drop($id, FR::EDIT_PROFILE_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::EDIT_PROFILE_TYPE);
            if ( ! $form_instance) return '';
            $css = $form_instance->minified_form_css();
        } else {
            $css = FR::get_form_meta($id, FR::EDIT_PROFILE_TYPE, FR::FORM_CSS);
        }

        return "<style type=\"text/css\">\r\n $css \r\n</style>";
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