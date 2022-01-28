<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\EditUserProfile;
use ProfilePress\Core\Classes\FormRepository as FR;

class MelangeTag extends FormProcessor
{
    public function __construct()
    {
        /**
         * We're not including check_password_reset_key(), and process_password_reset_handler_form()
         * cos it is already active and called by PasswordResetTag.
         *
         * Using priority 999999999 'cos we want it to run after that of their respective form types.
         */
        add_action('wp', [$this, 'process_password_reset_form'], 999999999);
        add_action('wp', [$this, 'process_registration_form'], 999999999);
        add_action('wp', [$this, 'process_login_form'], 999999999);
        add_action('wp', [$this, 'process_edit_profile_form'], 999999999);

        add_shortcode('profilepress-melange', [$this, 'parser']);
    }

    public function parser($atts)
    {
        $atts = shortcode_atts(['id' => '', 'redirect' => ''], $atts);

        $id       = absint($atts['id']);
        $redirect = isset($atts['redirect']) ? esc_url_raw($atts['redirect']) : '';

        if (is_string($this->password_reset_form_error) && ! empty($this->password_reset_form_error)) {
            $response = apply_filters('ppress_password_reset_notice', $this->password_reset_form_error, $id);
        }

        if (is_array($this->password_reset_form_error) && isset($this->password_reset_form_error[$id])) {
            $response = apply_filters('ppress_password_reset_notice', $this->password_reset_form_error[$id], $id);
        }

        if (is_string($this->login_form_error) && ! empty($this->login_form_error)) {
            $response = apply_filters('ppress_login_error', $this->login_form_error, $id);
        }

        if (is_array($this->login_form_error) && isset($this->login_form_error[$id])) {
            $response = apply_filters('ppress_login_error', $this->login_form_error[$id], $id);
        }

        if (isset($this->registration_form_error[$id])) {
            $response = apply_filters('ppress_registration_status', $this->registration_form_error[$id], $id, $redirect);
        }

        if (isset($_GET['edit']) && ($_GET['edit'] == 'true')) {
            $response = apply_filters('ppress_edit_profile_status', html_entity_decode(EditUserProfile::get_success_message($id, true)), $id);
        }

        if (isset($this->edit_profile_form_error[$id])) {
            $response = apply_filters('ppress_edit_profile_status', $this->edit_profile_form_error[$id], $id);
        }

        // pp_login_error_output is used by modules eg email confirmation to output custom login notices.
        if (empty($response)) $response = apply_filters('ppress_login_error_output', '');

        $attribution_start = apply_filters('ppress_hide_attribution', '<!-- This (Melange) form was created and powered by ProfilePress WordPress plugin - https://profilepress.net -->' . "\r\n");
        $attribution_end   = apply_filters('ppress_hide_attribution', "\r\n" . '<!-- / ProfilePress WordPress plugin. -->' . "\r\n");

        $css = self::get_melange_css($id);

        $class = 'pp-melange-form-wrap';
        if (isset($_GET['key'], $_GET['login'])) {
            $class = 'pp-password-reset-handler-wrap';
        }
        $container_div_start = sprintf('<div id="pp-melange-form-%s-wrap" class="pp-form-container %s">', $id, $class);
        $container_div_end   = '</div>';

        return $attribution_start . $css . $container_div_start . $response . $this->get_melange_structure($id, $redirect) . $container_div_end . $attribution_end;
    }


    /**
     * Get the melange structure from the database
     *
     * @param int $id
     * @param string $redirect
     *
     * @return string
     */
    public function get_melange_structure($id, $redirect)
    {
        if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_REQUEST['key'], $_REQUEST['login'])) {
            $structure = '<form method="post">' . PasswordResetTag::get_password_reset_handler_structure() . '</form>';
        } else {
            $structure = FR::get_form_meta($id, FR::MELANGE_TYPE, FR::FORM_STRUCTURE);
        }

        $GLOBALS['pp_melange_form_id']       = $id;
        $GLOBALS['pp_melange_form_redirect'] = $redirect;

        return do_shortcode($structure);
    }


    /**
     * Get the CSS stylesheet for the ID melange
     *
     * @return string
     */
    public static function get_melange_css($melange_id)
    {
        // if no id is set return
        if ( ! isset($melange_id)) return '';

        $melange_css = FR::get_form_meta($melange_id, FR::MELANGE_TYPE, FR::FORM_CSS);

        return "<style type=\"text/css\">\r\n $melange_css \r\n</style>";
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