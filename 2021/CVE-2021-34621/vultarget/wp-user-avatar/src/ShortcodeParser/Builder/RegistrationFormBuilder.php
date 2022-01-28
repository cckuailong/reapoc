<?php

namespace ProfilePress\Core\ShortcodeParser\Builder;

use ProfilePress\Core\Classes\FormRepository;

class RegistrationFormBuilder
{
    public static function initialize()
    {
        $instance = new FieldsShortcodeCallback(FormRepository::REGISTRATION_TYPE);

        add_shortcode('reg-username', array($instance, 'username'));
        add_shortcode('reg-password', array($instance, 'password'));
        add_shortcode('reg-confirm-password', array($instance, 'confirm_password'));
        add_shortcode('reg-email', array($instance, 'email'));
        add_shortcode('reg-confirm-email', array($instance, 'confirm_email'));
        add_shortcode('reg-website', array($instance, 'website'));
        add_shortcode('reg-nickname', array($instance, 'nickname'));
        add_shortcode('reg-display-name', array($instance, 'display_name'));
        add_shortcode('reg-first-name', array($instance, 'first_name'));
        add_shortcode('reg-last-name', array($instance, 'last_name'));
        add_shortcode('reg-bio', array($instance, 'bio'));
        add_shortcode('reg-avatar', array($instance, 'avatar'));
        add_shortcode('reg-cover-image', array($instance, 'cover_image'));
        add_shortcode('reg-cpf', array($instance, 'custom_profile_field'));
        add_shortcode('reg-submit', array($instance, 'submit'));
        add_shortcode('reg-password-meter', array(__CLASS__, 'password_meter'));
        add_shortcode('reg-select-role', array(__CLASS__, 'select_role'));

        // custom fields shortcodes
        add_shortcode('reg-text-box', array($instance, 'textbox_field'));
        add_shortcode('reg-textarea', array($instance, 'textarea_field'));
        add_shortcode('reg-select-dropdown', array($instance, 'select_dropdown_field'));
        add_shortcode('reg-checkbox-list', array($instance, 'checkbox_list_field'));
        add_shortcode('reg-single-checkbox', array($instance, 'single_checkbox_field'));
        add_shortcode('reg-cf-password', array($instance, 'cf_password_field'));
        add_shortcode('reg-country', array($instance, 'country_field'));
        add_shortcode('reg-date-field', array($instance, 'date_field'));
        add_shortcode('reg-number-field', array($instance, 'number_field'));
        add_shortcode('reg-radio-buttons', array($instance, 'radio_buttons_field'));

        do_action('ppress_register_registration_form_shortcode');
    }

    public static function GET_POST()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Is field a required field?
     *
     * @param array $atts
     *
     * @return bool
     */
    public static function is_field_required($atts)
    {
        $atts = ppress_normalize_attributes($atts);

        return isset($atts['required']) && ($atts['required'] === true || $atts['required'] == 'true' || $atts['required'] == '1');
    }

    /**
     * Rewrite custom field key to something more human readable.
     *
     * @param string $key field key
     *
     * @return string
     */
    public static function human_readable_field_key($key)
    {
        return ucfirst(str_replace('_', ' ', $key));
    }

    public static function valid_field_atts($atts)
    {
        if ( ! is_array($atts)) {
            return $atts;
        }

        $invalid_atts = array('enforce', 'key', 'field_key', 'limit', 'options', 'checkbox_text');

        $valid_atts = array();

        foreach ($atts as $key => $value) {
            if ( ! in_array($key, $invalid_atts)) {
                $valid_atts[$key] = $value;
            }
        }

        return $valid_atts;
    }

    public static function field_attributes($field_name, $atts, $required = 'false')
    {
        $_POST = self::GET_POST();

        if ($field_name !== 'reg_submit') {
            $atts['required'] = isset($atts['required']) ? $atts['required'] : $required;
        }

        if ( ! in_array($field_name, ['ignore_value'])) {
            $atts['value'] = isset($_POST[$field_name]) ? esc_attr($_POST[$field_name]) : @esc_attr($atts['value']);
        }

        $output = [];

        foreach ($atts as $key => $value) {
            if ($key != 'required' && ! empty($value)) {
                $value    = esc_attr($value);
                $output[] = "$key=\"$value\"";
            }
        }

        $output = implode(' ', $output);

        if (self::is_field_required($atts)) {
            $output .= ' required="required"';
        }

        return $output;
    }

    /**
     * Password strength meter field.
     * @see http://code.tutsplus.com/articles/using-the-included-password-strength-meter-script-in-wordpress--wp-34736
     */
    public static function password_meter($atts)
    {
        $atts['enforce'] = isset($atts['enforce']) ? $atts['enforce'] : 'true';

        $attributes = self::field_attributes('ignore_value', self::valid_field_atts(ppress_normalize_attributes($atts)));

        wp_localize_script('password-strength-meter', 'pwsL10n', array(
            'empty'    => esc_html__('Strength indicator', 'wp-user-avatar'),
            'short'    => esc_html__('Very weak', 'wp-user-avatar'),
            'bad'      => esc_html__('Weak', 'wp-user-avatar'),
            'good'     => _x('Medium', 'password strength', 'wp-user-avatar'),
            'strong'   => esc_html__('Strong', 'wp-user-avatar'),
            'mismatch' => esc_html__('Mismatch', 'wp-user-avatar'),
        ));

        ob_start(); ?>

        <?php if ('true' == $atts['enforce']) : ?>
        <input type="hidden" name="pp_enforce_password_meter" value="true">
    <?php endif; ?>
        <div id="pp-pass-strength-result" <?php echo $attributes; ?>><?php _e('Strength indicator'); ?></div>
        <script type="text/javascript">
            var pass_strength = 0;
            jQuery(document).ready(function ($) {
                var password1 = $('input[name=reg_password]');
                var password2 = $('input[name=reg_password2]');
                var submitButton = $('input[name=reg_submit]');
                var strengthMeterId = $('#pp-pass-strength-result');

                $('body').on('keyup', 'input[name=reg_password], input[name=reg_password2]', function () {
                        pp_checkPasswordStrength(password1, password2, strengthMeterId, submitButton, []);
                    }
                );

                // set a time delay to enable the checker function to initialize
                setTimeout(function () {
                    pp_checkPasswordStrength(password1, password2, strengthMeterId, submitButton, []);
                }, 500);

                submitButton.click(function () {
                    $('input[name=pp_enforce_password_meter]').val(pass_strength);
                });
            });

            function pp_checkPasswordStrength($pass1, $pass2, $strengthResult, $submitButton, blacklistArray) {
                var min_password_strength = <?php echo apply_filters('ppress_min_password_strength', 4); ?>;
                var pass1 = $pass1.val();
                var pass2 = $pass2.val();

                <?php if('true' == $atts['enforce']) : ?>
                // Reset the form & meter
                $submitButton.attr('disabled', 'disabled').css("opacity", ".4");
                <?php endif; ?>
                $strengthResult.removeClass('short bad good strong');

                // Extend our blacklist array with those from the inputs & site data
                blacklistArray = blacklistArray.concat(wp.passwordStrength.userInputBlacklist());

                // Get the password strength
                var strength = wp.passwordStrength.meter(pass1, blacklistArray, pass2);

                // Add the strength meter results
                switch (strength) {
                    case 1:
                    case 2:
                        $strengthResult.addClass('bad').html(pwsL10n.bad);
                        break;
                    case 3:
                        $strengthResult.addClass('good').html(pwsL10n.good);
                        if (min_password_strength === 3) {
                            pass_strength = 1;
                        }
                        break;
                    case 4:
                        $strengthResult.addClass('strong').html(pwsL10n.strong);
                        if (min_password_strength === 4) {
                            pass_strength = 1;
                        }
                        break;
                    case 5:
                        $strengthResult.addClass('short').html(pwsL10n.mismatch);
                        break;
                    default:
                        $strengthResult.addClass('short').html(pwsL10n.short);
                }

                // The meter function returns a result even if pass2 is empty,
                // enable only the submit button if the password is strong
                <?php if('true' == $atts['enforce']) : ?>
                if (min_password_strength <= strength) {
                    $submitButton.removeAttr('disabled').css("opacity", "");
                }
                <?php endif; ?>

                return strength;
            }
        </script>
        <?php
        return apply_filters('ppress_registration_password_meter_field', ob_get_clean(), $atts);
    }

    public static function select_role($atts)
    {
        $attributes = self::field_attributes('ignore_value', self::valid_field_atts(ppress_normalize_attributes($atts)));

        if ( ! empty($atts['options'])) {
            $selectible_roles = array_map('trim', explode(',', $atts['options']));
        }

        if (isset($selectible_roles)) {
            $wp_roles = array_filter(ppress_get_editable_roles(), function ($value) use ($selectible_roles) {
                // get the array key of the $value value.
                $key = array_search($value, ppress_get_editable_roles());

                return in_array($key, $selectible_roles);
            });
        } else {
            $wp_roles = ppress_get_editable_roles();
        }

        $html = "<select name=\"reg_select_role\" $attributes>";

        if (is_array($wp_roles)) {
            foreach ($wp_roles as $key => $value) {
                $_POST    = self::GET_POST();
                $selected = selected(@$_POST['reg_select_role'], $key, false);
                $label    = $value['name'];
                $html     .= "<option value='$key' id='select_role_$key' class='select_role_option' $selected>$label</option>";
            }
        }

        $html .= '</select>';

        return apply_filters('ppress_registration_select_role_field', $html, $atts);
    }
}