<?php

namespace ProfilePress\Core\ShortcodeParser\Builder;

use ProfilePress\Core\Classes\FormRepository;

/**
 * Parser for the child-shortcode of login form
 */
class LoginFormBuilder
{
    /**
     * define all login builder sub shortcode.
     */
    function __construct()
    {
        add_shortcode('login-username', array($this, 'login_username'));

        add_shortcode('login-password', array($this, 'login_password'));

        add_shortcode('login-remember', array($this, 'login_remember'));

        add_shortcode('login-submit', array($this, 'login_submit'));

        do_action('ppress_register_login_form_shortcode');
    }

    /**
     * parse the [login-username] shortcode
     *
     * @param array $atts
     *
     * @return string
     */
    function login_username($atts)
    {
        // grab unofficial attributes
        $other_atts_html = ppress_other_field_atts($atts);

        $atts = shortcode_atts(
            array(
                'class'       => '',
                'id'          => '',
                'value'       => '',
                'title'       => 'Username',
                'placeholder' => '',
                'required'    => true

            ),
            $atts
        );

        $atts = apply_filters('ppress_login_username_field_atts', $atts);

        $class       = ! empty($atts['class']) ? 'class="' . $atts['class'] . '"' : null;
        $placeholder = ! empty($atts['placeholder']) ? 'placeholder="' . $atts['placeholder'] . '"' : null;
        $id          = ! empty($atts['id']) ? 'id="' . $atts['id'] . '"' : null;
        $value       = ! empty($atts['value']) ? 'value="' . $atts['value'] . '"' : 'value="' . esc_attr(@$_POST['login_username']) . '"';

        $title    = 'title="' . $atts['title'] . '"';
        $required = isset($atts['required']) && ($atts['required'] === true || $atts['required'] == 'true') ? 'required="required"' : null;

        $html = "<input name=\"login_username\" type=\"text\" {$value} {$title} $class $placeholder $id $other_atts_html $required>";

        return apply_filters('ppress_login_username_field', $html, $atts);
    }

    /**
     * @param array $atts
     *
     * parse the [login-password] shortcode
     *
     * @return string
     */
    function login_password($atts)
    {
        // grab unofficial attributes
        $other_atts_html = ppress_other_field_atts($atts);

        $atts = shortcode_atts(
            array(
                'class'       => '',
                'id'          => '',
                'value'       => '',
                'title'       => 'Password',
                'placeholder' => '',
                'required'    => true
            ),
            $atts
        );

        $atts = apply_filters('ppress_login_password_field_atts', $atts);

        $class       = 'class="' . $atts['class'] . '"';
        $placeholder = 'placeholder="' . $atts['placeholder'] . '"';

        $id       = ! empty($atts['id']) ? 'id="' . $atts['id'] . '"' : null;
        $value    = ! empty($atts['value']) ? 'value="' . esc_attr($atts['value']) . '"' : 'value="' . esc_attr(@$_POST['login_password']) . '"';
        $title    = 'title="' . $atts['title'] . '"';
        $required = isset($atts['required']) && ($atts['required'] === true || $atts['required'] == 'true') ? 'required="required"' : null;

        $html = "<input name='login_password' type='password' $title $value $class $placeholder $id $other_atts_html $required>";

        return apply_filters('ppress_login_password_field', $html, $atts);

    }

    /** Remember me checkbox */
    function login_remember($atts)
    {
        // grab unofficial attributes
        $other_atts_html = ppress_other_field_atts($atts);

        $atts = shortcode_atts(
            array(
                'class' => '',
                'id'    => '',
                'title' => ''
            ),
            $atts
        );

        $atts = apply_filters('ppress_login_remember_field_atts', $atts);

        $class = 'class="' . $atts['class'] . '"';
        $id    = 'id="' . $atts['id'] . '"';
        $title = 'title="' . $atts['title'] . '"';

        $html = "<input name='login_remember' value='true' type='checkbox' $title $class $id $other_atts_html checked='checked'>";

        return apply_filters('ppress_login_remember_field', $html, $atts);
    }

    public function login_submit($atts)
    {
        $form_type = FormRepository::LOGIN_TYPE;
        $form_id = isset($GLOBALS['pp_login_form_id']) ? $GLOBALS['pp_login_form_id'] : 0;

        if (isset($GLOBALS['pp_melange_form_id'])) {
            $form_id = $GLOBALS['pp_melange_form_id'];
            $form_type = FormRepository::MELANGE_TYPE;
        }

        // grab unofficial attributes
        $other_atts_html = ppress_other_field_atts($atts);

        $atts = shortcode_atts(
            array(
                'class'            => '',
                'id'               => '',
                'title'            => '',
                'value'            => '',
                'processing_label' => '',
                'name'             => 'login_submit',
            ),
            $atts
        );

        $atts = apply_filters('ppress_login_submit_field_atts', $atts);

        $name  = 'name="' . $atts['name'] . '"';
        $class = 'class="pp-submit-form ' . $atts['class'] . '"';
        $id    = 'id="' . $atts['id'] . '"';

        $value            = ! empty($atts['value']) ? $atts['value'] : esc_html__('Log In', 'wp-user-avatar');
        $processing_label = ! empty($atts['processing_label']) ? $atts['processing_label'] : FormRepository::get_processing_label($form_id, $form_type);

        $title = 'title="' . $atts['title'] . '"';

        $html = sprintf(
            '<input data-pp-submit-label="%2$s" data-pp-processing-label="%3$s" type="submit" value="%2$s" %1$s>',
            "$name $title $class $id $other_atts_html",
            $value,
            $processing_label
        );

        return apply_filters('ppress_login_submit_field', $html, $atts);
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
