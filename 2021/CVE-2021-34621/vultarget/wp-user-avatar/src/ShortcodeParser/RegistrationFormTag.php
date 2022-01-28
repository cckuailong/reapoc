<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\FormRepository as FR;

class RegistrationFormTag extends FormProcessor
{
    public function __construct()
    {
        add_action('wp', [$this, 'process_registration_form']);

        add_shortcode('profilepress-registration', array($this, 'parse_shortcode'));
    }

    public function parse_shortcode($atts)
    {
        $atts = shortcode_atts(['id' => '', 'redirect' => '', 'no-login-redirect' => ''], $atts);

        $id                     = absint($atts['id']);
        $redirect               = esc_url_raw($atts['redirect']);
        $no_login_redirect      = esc_url_raw($atts['no-login-redirect']);
        $registration_structure = self::get_registration_structure($id, $redirect, $no_login_redirect);

        $registration_status = '';

        if (isset($this->registration_form_error[$id])) {
            $registration_status = $this->registration_form_error[$id];
        }

        $registration_status = apply_filters('ppress_registration_status', $registration_status, $id, $redirect);

        $attribution_start = apply_filters('ppress_hide_attribution', '<!-- This form was created and powered by ProfilePress WordPress plugin - https://profilepress.net -->' . "\r\n");
        $attribution_end   = apply_filters('ppress_hide_attribution', "\r\n" . '<!-- / ProfilePress WordPress plugin. -->' . "\r\n");

        $css = self::get_registration_css($id);

        $container_div_start = sprintf('<div id="pp-registration-%s-wrap" class="pp-form-container pp-registration-form-wrapper">', $id);
        $container_div_end   = '</div>';

        return apply_filters(
            'ppress_registration_form',
            $attribution_start . $css . $container_div_start . $registration_status . $registration_structure . $container_div_end . $attribution_end,
            $id
        );
    }


    /**
     * Get the registration structure from the database
     *
     * @param int $id
     *
     * @param $redirect
     * @param $no_login_redirect
     *
     * @return string
     */
    public static function get_registration_structure($id, $redirect, $no_login_redirect)
    {
        if ( ! get_option('users_can_register')) {
            return apply_filters('ppress_registration_disabled_text', esc_html__('Registration is disabled in this site.', 'wp-user-avatar'));
        }

        if (FR::is_drag_drop($id, FR::REGISTRATION_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::REGISTRATION_TYPE);
            if ( ! $form_instance) return esc_html__('Form class not found. Please check if this ProfilePress form actually exist.', 'wp-user-avatar');
            $registration_structure = $form_instance->form_structure();
        } else {
            $registration_structure = FR::get_form_meta($id, FR::REGISTRATION_TYPE, FR::FORM_STRUCTURE);
        }

        $referrer_url = wp_get_referer();

        $GLOBALS['pp_registration_form_id'] = $id;

        $registration_structure = do_shortcode($registration_structure);

        $form_tag = "<form data-pp-form-submit=\"signup\" id='pp_registration_$id' method=\"post\" enctype=\"multipart/form-data\"" . apply_filters('ppress_registration_form_tag', '', $id) . ">";

        if ( ! empty($redirect)) {
            $registration_structure .= "<input type='hidden' name='signup_redirect' value='$redirect'>";
        }

        if ( ! empty($no_login_redirect)) {
            $registration_structure .= "<input type='hidden' name='signup_no_login_redirect' value='$no_login_redirect'>";
        }

        $registration_structure .= '<input type="hidden" name="pp_current_url" value="' . ppress_get_current_url_query_string() . '">';
        $registration_structure .= "<input type='hidden' name='signup_form_id' value='$id'>";
        $registration_structure .= sprintf("<input type='hidden' name='signup_referrer_page' value='%s'>", !empty($referrer_url) ? $referrer_url : '');

        $registration_structure = apply_filters('ppress_form_field_structure', $registration_structure, $id);

        return $form_tag . $registration_structure . '</form>';
    }

    /**
     * Get the CSS stylesheet for the ID registration
     *
     * @param $id
     *
     * @return mixed
     */

    public static function get_registration_css($id)
    {
        if (FR::is_drag_drop($id, FR::REGISTRATION_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::REGISTRATION_TYPE);
            if ( ! $form_instance) return '';
            $registration_css = $form_instance->minified_form_css();
        } else {
            $registration_css = FR::get_form_meta($id, FR::REGISTRATION_TYPE, FR::FORM_CSS);
        }

        return "<style type=\"text/css\">\r\n $registration_css \r\n</style>";
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