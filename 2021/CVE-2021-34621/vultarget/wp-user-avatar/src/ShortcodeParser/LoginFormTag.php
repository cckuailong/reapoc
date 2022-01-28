<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\FormRepository as FR;

class LoginFormTag extends FormProcessor
{
    public function __construct()
    {
        add_action('wp', [$this, 'process_login_form']);

        add_shortcode('profilepress-login', array($this, 'login_parser'));
    }

    public function login_parser($atts)
    {
        $atts = shortcode_atts(['id' => '', 'redirect' => ''], $atts);

        $id = absint($atts['id']);

        $login_error = '';

        if (is_string($this->login_form_error) && ! empty($this->login_form_error)) {
            $login_error = $this->login_form_error;
        }

        if (is_array($this->login_form_error) && isset($this->login_form_error[$id])) {
            $login_error = $this->login_form_error[$id];
        }

        // the filter pp_login_error_output is used to add custom error to login form.
        $login_error = ! empty($login_error) ? $login_error : apply_filters('ppress_login_error_output', '');
        $login_error = apply_filters('ppress_login_error', $login_error, $id);

        $attribution_start = apply_filters('ppress_hide_attribution', '<!-- This form was created and powered by ProfilePress WordPress plugin - https://profilepress.net -->' . "\r\n");
        $attribution_end   = apply_filters('ppress_hide_attribution', "\r\n" . '<!-- / ProfilePress WordPress plugin. -->' . "\r\n");

        $css = self::get_login_css($id);

        $container_div_start = sprintf('<div id="pp-login-%s-wrap" class="pp-form-container pp-login-form-wrap">', $id);
        $container_div_end   = '</div>';

        return apply_filters(
            'ppress_login_form',
            $attribution_start . $css . $container_div_start . $login_error . $this->get_login_structure($id, $atts['redirect']) . $container_div_end . $attribution_end,
            $id
        );
    }

    /**
     * Build the login structure
     *
     * @param int $id login builder ID
     * @param string $redirect url to redirect to. only used by ajax login form.
     *
     * @return string string login structure
     */
    public function get_login_structure($id, $redirect = '')
    {
        if (FR::is_drag_drop($id, FR::LOGIN_TYPE)) {
            $form_instance = FR::dnd_class_instance($id, FR::LOGIN_TYPE);
            if ( ! $form_instance) return esc_html__('Form class not found. Please check if this ProfilePress form actually exist.', 'wp-user-avatar');
            $login_structure = $form_instance->form_structure();
        } else {
            $login_structure = FR::get_form_meta($id, FR::LOGIN_TYPE, FR::FORM_STRUCTURE);
        }

        $GLOBALS['pp_login_form_id'] = $id;

        $login_structure = do_shortcode($login_structure);

        $referrer_url = wp_get_referer() ? wp_get_referer() : '';

        if ( ! empty($_REQUEST['redirect_to'])) {
            $redirect = rawurldecode($_REQUEST['redirect_to']);
        }

        if ( ! empty($redirect)) {
            $login_structure .= '<input type="hidden" name="login_redirect" value="' . esc_attr($redirect) . '">';
        }

        $login_structure .= "<input type='hidden' name='login_form_id' value='$id'>";
        $login_structure .= '<input type="hidden" name="pp_current_url" value="' . esc_attr(ppress_get_current_url_query_string()) . '">';
        $login_structure .= '<input type="hidden" name="login_referrer_page" value="' . esc_attr($referrer_url) . '">';

        $form_tag = "<form data-pp-form-submit=\"login\" id='pp_login_$id' method=\"post\"" . apply_filters('ppress_login_form_tag', '', $id) . ">";

        return $form_tag . $login_structure . '</form>';
    }

    /**
     * Get the CSS stylesheet for the ID login
     *
     * @param $form_id
     *
     * @return mixed
     */
    public static function get_login_css($form_id)
    {
        if (FR::is_drag_drop($form_id, FR::LOGIN_TYPE)) {
            $form_instance = FR::dnd_class_instance($form_id, FR::LOGIN_TYPE);
            if ( ! $form_instance) return '';
            $login_css = $form_instance->minified_form_css();
        } else {
            $login_css = FR::get_form_meta($form_id, FR::LOGIN_TYPE, FR::FORM_CSS);
        }

        // added a break-line to the style tag to keep it in a new line - viewed when viewing site source code
        return "\r\n <style type=\"text/css\">\r\n" . $login_css . "\r\n</style>\r\n";
    }

    static function get_instance()
    {
        static $instance = false;

        if ( ! $instance) {
            $instance = new self;
        }

        return $instance;
    }
}