<?php

namespace ProfilePress\Core\ShortcodeParser\Builder;

use ProfilePress\Core\Classes\FormRepository;

class EditProfileBuilder
{
    public function __construct()
    {
        $instance = new FieldsShortcodeCallback(FormRepository::EDIT_PROFILE_TYPE);

        add_shortcode('edit-profile-username', array($instance, 'username'));

        add_shortcode('edit-profile-password', array($instance, 'password'));

        add_shortcode('edit-profile-confirm-password', array($instance, 'confirm_password'));

        add_shortcode('edit-profile-password-meter', array(__CLASS__, 'password_meter'));

        add_shortcode('edit-profile-email', array($instance, 'email'));

        add_shortcode('edit-profile-confirm-email', array($instance, 'confirm_email'));

        add_shortcode('edit-profile-website', array($instance, 'website'));

        add_shortcode('edit-profile-nickname', array($instance, 'nickname'));

        add_shortcode('edit-profile-display-name', array($instance, 'display_name'));

        add_shortcode('edit-profile-first-name', array($instance, 'first_name'));

        add_shortcode('edit-profile-last-name', array($instance, 'last_name'));

        add_shortcode('edit-profile-bio', array($instance, 'bio'));

        add_shortcode('delete-avatar-button', array($instance, 'remove_user_avatar'));
        add_shortcode('pp-remove-avatar-button', array($instance, 'remove_user_avatar'));

        add_shortcode('edit-profile-avatar', array($instance, 'avatar'));

        add_shortcode('edit-profile-cover-image', array($instance, 'cover_image'));
        add_shortcode('pp-remove-cover-image-button', array($instance, 'remove_cover_image'));

        add_shortcode('edit-profile-cpf', array($instance, 'custom_profile_field'));

        add_shortcode('edit-profile-submit', array($instance, 'submit'));

        add_shortcode('edit-profile-text-box', array($instance, 'textbox_field'));
        add_shortcode('edit-profile-textarea', array($instance, 'textarea_field'));
        add_shortcode('edit-profile-select-dropdown', array($instance, 'select_dropdown_field'));
        add_shortcode('edit-profile-checkbox-list', array($instance, 'checkbox_list_field'));
        add_shortcode('edit-profile-single-checkbox', array($instance, 'single_checkbox_field'));
        add_shortcode('edit-profile-cf-password', array($instance, 'cf_password_field'));
        add_shortcode('edit-profile-country', array($instance, 'country_field'));
        add_shortcode('edit-profile-date-field', array($instance, 'date_field'));
        add_shortcode('edit-profile-number-field', array($instance, 'number_field'));
        add_shortcode('edit-profile-radio-buttons', array($instance, 'radio_buttons_field'));

        do_action('ppress_register_edit_profile_form_shortcode');
    }

    /**
     * Password strength meter field.
     */
    public static function password_meter($atts)
    {
        // grab unofficial attributes
        $other_atts_html = ppress_other_field_atts($atts);

        $atts = shortcode_atts(
            array(
                'class'   => '',
                'enforce' => 'false',
            ),
            $atts
        );

        $atts = apply_filters('ppress_edit_profile_password_meter_field_atts', $atts);

        wp_localize_script('password-strength-meter', 'pwsL10n', array(
            'empty'    => esc_html__('Strength indicator'),
            'short'    => esc_html__('Very weak'),
            'bad'      => esc_html__('Weak'),
            'good'     => _x('Medium', 'password strength'),
            'strong'   => esc_html__('Strong'),
            'mismatch' => esc_html__('Mismatch'),
        ));

        ob_start(); ?>

        <?php if ('true' == $atts['enforce']) : ?>
        <input type="hidden" name="pp_enforce_password_meter" value="true">
    <?php endif; ?>
        <div id="pp-pass-strength-result" <?php echo 'class="' . $atts['class'] . '"' . $other_atts_html; ?>><?php _e('Strength indicator'); ?></div>
        <script type="text/javascript">
            var pass_strength = 0;
            jQuery(document).ready(function ($) {
                var password1 = $('input[name=eup_password]');
                var password2 = $('input[name=eup_password2]');
                var submitButton = $('input[name=eup_submit]');
                var strengthMeterId = $('#pp-pass-strength-result');

                $('body').on('keyup', 'input[name=eup_password], input[name=eup_password2]',
                    function (event) {
                        pp_checkPasswordStrength(password1, password2, strengthMeterId, submitButton, []);
                    }
                );

                if (password1.val() != '') {
                    // trigger 'keyup' event to check password strength when password field isn't empty.
                    $('body input[name=eup_password]').trigger('keyup');
                }

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
        return apply_filters('ppress_edit_profile_password_meter_field', ob_get_clean(), $atts);
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