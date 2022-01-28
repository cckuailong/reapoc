<?php

namespace ProfilePress\Core\ShortcodeParser;

use ProfilePress\Core\Classes\FormRepository as FR;

class PasswordResetTag extends FormProcessor
{
    public function __construct()
    {
        add_action('wp', [$this, 'process_password_reset_form']);
        add_action('wp', [$this, 'check_password_reset_key']);
        add_action('wp', [$this, 'process_password_reset_handler_form']);

        add_shortcode('profilepress-password-reset', array($this, 'parser'));
    }

    /**
     * Parse the password reset shortcode
     *
     * @param $atts
     *
     * @return string
     */
    public function parser($atts)
    {
        $id = absint($atts['id']);

        $password_reset_status = '';

        if (is_string($this->password_reset_form_error) && ! empty($this->password_reset_form_error)) {
            $password_reset_status = $this->password_reset_form_error;
        }

        if (is_array($this->password_reset_form_error) && isset($this->password_reset_form_error[$id])) {
            $password_reset_status = $this->password_reset_form_error[$id];
        }

        $password_reset_status = apply_filters('ppress_password_reset_notice', $password_reset_status, $id);

        $attribution_start = apply_filters('ppress_hide_attribution', '<!-- This form was created and powered by ProfilePress WordPress plugin - https://profilepress.net -->' . "\r\n");
        $attribution_end   = apply_filters('ppress_hide_attribution', "\r\n" . '<!-- / ProfilePress WordPress plugin. -->' . "\r\n");

        $password_reset_css = self::get_password_reset_css($id);

        $class = 'pp-password-reset-wrap';
        if (isset($_GET['key'], $_GET['login'])) {
            $class = 'pp-password-reset-handler-wrap';
        }
        $container_div_start = sprintf('<div id="pp-password-reset-%s-wrap" class="pp-form-container %s">', $id, $class);
        $container_div_end   = '</div>';

        return apply_filters(
            'ppress_password_reset_form',
            $attribution_start . $password_reset_css . $container_div_start . $password_reset_status . $this->get_password_reset_structure($id) . $container_div_end . $attribution_end,
            $id
        );
    }

    /**
     * Get the password reset structure from the database
     *
     * @param int $id
     *
     * @return string
     */
    public function get_password_reset_structure($id)
    {
        // do not show password reset form again after user reset password.
        if (isset($_GET['password']) && $_GET['password'] === 'changed') return '';

        if ('GET' == $_SERVER['REQUEST_METHOD'] && isset($_REQUEST['key']) && isset($_REQUEST['login'])) {
            $structure = self::get_password_reset_handler_structure($id);
        } else {
            if (FR::is_drag_drop($id, FR::PASSWORD_RESET_TYPE)) {
                $form_instance = FR::dnd_class_instance($id, FR::PASSWORD_RESET_TYPE);
                if ( ! $form_instance) return esc_html__('Form class not found. Please check if this ProfilePress form actually exist.', 'wp-user-avatar');
                $structure = $form_instance->form_structure();
            } else {
                $structure = FR::get_form_meta($id, FR::PASSWORD_RESET_TYPE, FR::FORM_STRUCTURE);
            }
        }

        $GLOBALS['pp_password_reset_form_id'] = $id;

        $structure = do_shortcode($structure);

        $structure .= "<input type='hidden' name='passwordreset_form_id' value='$id'>";

        $form_tag = "<form data-pp-form-submit=\"passwordreset\" id='pp_password_reset_$id' method=\"post\"" . apply_filters('ppress_password_reset_form_tag', '', $id) . ">";

        return $form_tag . $structure . '</form>';
    }

    public static function get_default_handler_form()
    {
        ob_start();
        ?>
        <div class="pp-reset-password-form">
            <h3><?= esc_html__('Enter your new password below', 'wp-user-avatar'); ?></h3>
            <label for="password1"><?= esc_html__('New password', 'wp-user-avatar'); ?>
                <span class="req">*</span></label>
            [enter-password id="password1" required autocomplete="off"]

            <label for="password2"><?= esc_html__('Re-enter new password', 'wp-user-avatar') ?>
                <span class="req">*</span></label>
            [re-enter-password id="password2" required autocomplete="off"]

            [password-reset-submit class="pp-reset-button pp-reset-button-block" value="<?= esc_html__('Save', 'wp-user-avatar'); ?>"]
        </div>
        <?php
        return apply_filters('ppress_form_default_handler_form', ob_get_clean());
    }

    /**
     * Return password reset handler form or redirect to password reset page when key is invalid.
     *
     * @param int $id
     *
     * @return null|string
     */
    public static function get_password_reset_handler_structure($id = null)
    {
        $handler_structure = FR::get_form_meta($id, FR::PASSWORD_RESET_TYPE, FR::PASSWORD_RESET_HANDLER);

        if (empty($handler_structure)) {
            $handler_structure = self::get_default_handler_form();
        }

        $handler_structure .= '<input type="hidden" name="reset_key" value="' . esc_attr($_REQUEST['key']) . '">';
        $handler_structure .= '<input type="hidden" name="reset_login" value="' . esc_attr($_REQUEST['login']) . '">';

        return $handler_structure;
    }


    /**
     * Get the CSS stylesheet for the ID password reset
     *
     * @param $form_id
     *
     * @return mixed
     */
    public static function get_password_reset_css($form_id)
    {
        if (FR::is_drag_drop($form_id, FR::PASSWORD_RESET_TYPE)) {
            $form_instance = FR::dnd_class_instance($form_id, FR::PASSWORD_RESET_TYPE);
            if ( ! $form_instance) return '';
            $password_reset_css = $form_instance->minified_form_css();
        } else {
            $password_reset_css = FR::get_form_meta($form_id, FR::PASSWORD_RESET_TYPE, FR::FORM_CSS);
        }

        return "<style type=\"text/css\">\r\n $password_reset_css \r\n</style>";
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