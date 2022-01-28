<?php

namespace ProfilePress\Core\Themes\DragDrop;

use ProfilePress\Core\Base;
use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\FormRepository as FR;

abstract class AbstractTheme implements ThemeInterface
{
    public $form_id;

    public $form_type;

    public $tag_name;

    public $metabox_settings;

    public $asset_image_url;

    public function __construct($form_id, $form_type)
    {
        $this->form_id   = $form_id;
        $this->form_type = $form_type;

        $this->asset_image_url = PPRESS_ASSETS_URL . '/images';

        $this->tag_name = 'login';

        switch ($this->form_type) {
            case FR::REGISTRATION_TYPE:
                $this->tag_name = 'reg';
                break;
            case FR::EDIT_PROFILE_TYPE:
                $this->tag_name = 'edit-profile';
                break;
            case FR::PASSWORD_RESET_TYPE:
                $this->tag_name = 'reset';
                break;
        }

        add_shortcode('pp-form-wrapper', [$this, 'form_wrapper_shortcode']);

        add_filter('ppress_form_builder_meta_box_settings', [$this, 'metabox_settings'], 10, 3);

        add_filter('ppress_form_builder_meta_box_submit_button_settings', [$this, 'submit_button_settings']);

        add_filter('ppress_form_builder_meta_box_appearance_settings', [$this, 'appearance_settings']);

        add_filter('ppress_form_builder_meta_box_colors_settings', [$this, 'color_settings']);

        // add div wrapper to remember me checkbox
        add_filter('ppress_form_field_listing_login-remember', [$this, 'remember_me_checkbox_wrapper'], 10, 2);
        // when label is found in field settings, it is automatically added before the field so we are removing it.
        add_filter('ppress_form_field_listing_setting_login-remember', [$this, 'remember_me_checkbox_remove_label']);
    }

    public function is_show_social_login()
    {
        return in_array($this->form_type, [FR::LOGIN_TYPE, FR::REGISTRATION_TYPE]) && EM::is_enabled(EM::SOCIAL_LOGIN);
    }

    /**
     * Array of fields whose settings in form builder should not be modified. Should be as is.
     */
    public function disallowed_settings_fields()
    {
        return ['pp-custom-html', 'pp-recaptcha', 'pp-user-avatar', 'pp-user-cover-image'];
    }

    public function minified_form_css()
    {
        return ppress_minify_css($this->form_css());
    }

    public function get_meta($key)
    {
        $metabox_settings = FR::get_form_meta($this->form_id, $this->form_type, FR::METABOX_FORM_BUILDER_SETTINGS);

        if (empty($metabox_settings)) $metabox_settings = [];

        $default_metabox_settings = $this->default_metabox_settings();

        return isset($metabox_settings[$key]) ? $metabox_settings[$key] : (isset($default_metabox_settings[$key]) ? $default_metabox_settings[$key] : '');
    }

    public function remember_me_checkbox_remove_label($field_setting)
    {
        unset($field_setting['label']);

        return $field_setting;
    }

    public function remember_me_checkbox_wrapper($tag, $field_setting)
    {
        return sprintf(
            '<div class="ppform-remember-me"><label class="ppform-remember-checkbox">%s <span class="ppform-remember-label">%s</span></label></div>',
            $tag, $field_setting['label']
        );
    }

    public function default_metabox_settings()
    {
        $button_text     = esc_html__('Log In', 'wp-user-avatar');
        $success_message = '';

        switch ($this->form_type) {
            case FR::REGISTRATION_TYPE:
                $button_text     = esc_html__('Register', 'wp-user-avatar');
                $success_message = esc_html__('Registration successful.', 'wp-user-avatar');
                break;
            case FR::EDIT_PROFILE_TYPE:
                $button_text     = esc_html__('Update Profile', 'wp-user-avatar');
                $success_message = esc_html__('Changes saved.', 'wp-user-avatar');
                break;
            case FR::PASSWORD_RESET_TYPE:
                $button_text     = esc_html__('Reset Password', 'wp-user-avatar');
                $success_message = esc_html__('Check your email for further instruction.', 'wp-user-avatar');
                break;
        }

        return [
            'submit_button_text'             => $button_text,
            'submit_button_processing_label' => esc_html__('Processing', 'wp-user-avatar'),
            FR::SUCCESS_MESSAGE              => $success_message,
            FR::REGISTRATION_USER_ROLE       => 'subscriber'
        ];
    }

    public function metabox_settings($settings, $form_type, $DragDropBuilderInstance)
    {
        return $settings;
    }

    public function submit_button_settings($settings)
    {
        return $settings;
    }

    public function appearance_settings($settings)
    {
        return $settings;
    }

    public function color_settings($settings)
    {
        return $settings;
    }

    /**
     * Each fields default values.
     *
     * @return array
     */
    public function default_fields_settings()
    {
        $defaults = [
            $this->tag_name . '-username'         => [
                'placeholder' => esc_html__('Username', 'wp-user-avatar')
            ],
            $this->tag_name . '-email'            => [
                'placeholder' => esc_html__('Email Address', 'wp-user-avatar')
            ],
            $this->tag_name . '-password'         => [
                'placeholder' => esc_html__('Password', 'wp-user-avatar')
            ],
            $this->tag_name . '-confirm-password' => [
                'placeholder' => esc_html__('Confirm Password', 'wp-user-avatar')
            ],
            $this->tag_name . '-confirm-email'    => [
                'placeholder' => esc_html__('Confirm Email', 'wp-user-avatar')
            ],
            $this->tag_name . '-website'          => [
                'placeholder' => esc_html__('Website', 'wp-user-avatar')
            ],
            $this->tag_name . '-nickname'         => [
                'placeholder' => esc_html__('Nickname', 'wp-user-avatar')
            ],
            $this->tag_name . '-display-name'     => [
                'placeholder' => esc_html__('Display Name', 'wp-user-avatar')
            ],
            $this->tag_name . '-first-name'       => [
                'placeholder' => esc_html__('First Name', 'wp-user-avatar')
            ],
            $this->tag_name . '-last-name'        => [
                'placeholder' => esc_html__('Last Name', 'wp-user-avatar')
            ],
            $this->tag_name . '-bio'              => [
                'placeholder' => esc_html__('Biography', 'wp-user-avatar')
            ],
            $this->tag_name . '-avatar'           => [],
            $this->tag_name . '-password-meter'   => [
                'enforce' => true
            ],
            $this->tag_name . '-select-role'      => ['options' => ''],

            // edit profile only
            'pp-user-avatar'                      => ['size' => 300],

            // login form
            'login-username'                      => [
                'placeholder' => esc_html__('Username or Email', 'wp-user-avatar')
            ],
            'login-password'                      => [
                'placeholder' => esc_html__('Password', 'wp-user-avatar')
            ],
            'login-remember'                      => [
                'label' => esc_html__('Remember Me', 'wp-user-avatar')
            ],

            // password reset
            'user-login'                          => [
                'placeholder' => esc_html__('Username or Email', 'wp-user-avatar')
            ],

            // user profile
            'profile-username'                    => [
                'label' => esc_html__('Username', 'wp-user-avatar')
            ],
            'profile-email'                       => [
                'label' => esc_html__('Email Address', 'wp-user-avatar')
            ],
            'profile-first-name'                  => [
                'label' => esc_html__('First Name', 'wp-user-avatar')
            ],
            'profile-last-name'                   => [
                'label' => esc_html__('Last Name', 'wp-user-avatar')
            ],
            'profile-website'                     => [
                'label' => esc_html__('Website', 'wp-user-avatar')
            ],
            'profile-bio'                         => [
                'label' => esc_html__('Bio', 'wp-user-avatar')
            ],
        ];

        if ($this->form_type == FR::MEMBERS_DIRECTORY_TYPE) {
            // user profile
            $defaults['profile-username']   = [];
            $defaults['profile-email']      = [];
            $defaults['profile-first-name'] = [];
            $defaults['profile-last-name']  = [];
            $defaults['profile-website']    = [];
            $defaults['profile-bio']        = [];
        }

        return $defaults;
    }

    public function form_wrapper_shortcode($atts, $content)
    {
        $form_id   = $this->form_id;
        $form_type = $this->form_type;

        $atts = shortcode_atts(['class' => '', 'style' => ''], $atts);

        $classes = ['pp-form-wrapper', "pp-$form_type", "pp-$form_type-$form_id"];
        if (isset($atts['class']) && ! empty($atts['class'])) {
            $classes[] = esc_attr($atts['class']);
        }

        return sprintf(
            '<div id="%s" class="%s"%s>%s</div>',
            "pp-$form_type-$form_id",
            implode(' ', $classes),
            ! empty($atts['style']) ? ' style="' . esc_attr($atts['style']) . '"' : '',
            do_shortcode($content)
        );
    }

    public function field_listing()
    {
        return (new FieldListing($this->form_id, $this->form_type))->defaults($this->default_fields_settings())->forge();
    }

    /**
     * @return ProfileFieldListing
     */
    public function profile_listing()
    {
        return (new ProfileFieldListing($this->form_id))->defaults($this->default_fields_settings());
    }

    public function get_profile_field($field_key, $parse_shortcode = false)
    {
        if (empty($field_key)) return '';

        $return = sprintf('[profile-cpf key=%s]', $field_key);

        $standard_fields = array_keys(ppress_standard_fields_key_value_pair(true));

        if (in_array($field_key, $standard_fields)) {

            if ($field_key == 'first_last_names') {
                $return = '[profile-first-name] [profile-last-name]';
            } elseif ($field_key == 'last_first_names') {
                $return = '[profile-last-name] [profile-first-name]';
            } elseif ($field_key == 'registration_date') {
                $return = '[profile-date-registered]';
            } else {
                $return = sprintf('[profile-%s]', $field_key);
            }
        }

        return $parse_shortcode === true ? do_shortcode($return, true) : $return;
    }

    public function form_submit_button()
    {
        $submit_button_text = $this->get_meta('submit_button_text');
        $processing_label   = $this->get_meta('submit_button_processing_label');

        return sprintf(
            '[%s-submit class="ppform-submit-button" value="%s" processing_label="%s"]',
            $this->tag_name, $submit_button_text, $processing_label
        );
    }

    public function social_profile_icons()
    {
        if ( ! EM::is_enabled(EM::CUSTOM_FIELDS)) return false;

        $facebook_url  = $this->get_profile_field(Base::cif_facebook, true);
        $twitter_url   = $this->get_profile_field(Base::cif_twitter, true);
        $linkedin_url  = $this->get_profile_field(Base::cif_linkedin, true);
        $github_url    = $this->get_profile_field(Base::cif_github, true);
        $instagram_url = $this->get_profile_field(Base::cif_instagram, true);
        $youtube_url   = $this->get_profile_field(Base::cif_youtube, true);
        $vk_url        = $this->get_profile_field(Base::cif_vk, true);

        if (
            empty($facebook_url) &&
            empty($twitter_url) &&
            empty($linkedin_url) &&
            empty($github_url) &&
            empty($instagram_url) &&
            empty($youtube_url) &&
            empty($vk_url)) {
            return false;
        }
        ?>
        <div class="ppress-pf-profile-connect">

            <?php if ( ! empty($facebook_url)) :  // ignore_html set to true to quicken the parsing ?>
                <a href="<?= $facebook_url ?>" target="_blank" class="ppress-pf-social-icon dpf-facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48" style=" fill:#000000;">
                        <path fill="#039be5" d="M24 5A19 19 0 1 0 24 43A19 19 0 1 0 24 5Z"></path>
                        <path fill="#fff" d="M26.572,29.036h4.917l0.772-4.995h-5.69v-2.73c0-2.075,0.678-3.915,2.619-3.915h3.119v-4.359c-0.548-0.074-1.707-0.236-3.897-0.236c-4.573,0-7.254,2.415-7.254,7.917v3.323h-4.701v4.995h4.701v13.729C22.089,42.905,23.032,43,24,43c0.875,0,1.729-0.08,2.572-0.194V29.036z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($twitter_url)) : ?>
                <a href="<?= $twitter_url ?>" target="_blank" class="ppress-pf-social-icon dpf-twitter">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="48" height="48" viewBox="0 0 48 48" style=" fill:#000000;">
                        <path fill="#03a9f4" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                        <path fill="#fff" d="M36,17.12c-0.882,0.391-1.999,0.758-3,0.88c1.018-0.604,2.633-1.862,3-3 c-0.951,0.559-2.671,1.156-3.793,1.372C31.311,15.422,30.033,15,28.617,15C25.897,15,24,17.305,24,20v2c-4,0-7.9-3.047-10.327-6 c-0.427,0.721-0.667,1.565-0.667,2.457c0,1.819,1.671,3.665,2.994,4.543c-0.807-0.025-2.335-0.641-3-1c0,0.016,0,0.036,0,0.057 c0,2.367,1.661,3.974,3.912,4.422C16.501,26.592,16,27,14.072,27c0.626,1.935,3.773,2.958,5.928,3c-1.686,1.307-4.692,2-7,2 c-0.399,0-0.615,0.022-1-0.023C14.178,33.357,17.22,34,20,34c9.057,0,14-6.918,14-13.37c0-0.212-0.007-0.922-0.018-1.13 C34.95,18.818,35.342,18.104,36,17.12"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($linkedin_url)) : ?>
                <a href="<?= $linkedin_url ?>" target="_blank" class="ppress-pf-social-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         width="48" height="48"
                         viewBox="0 0 48 48"
                         style="fill:#000000;">
                        <path fill="#0288d1" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                        <path fill="#fff" d="M14 19H18V34H14zM15.988 17h-.022C14.772 17 14 16.11 14 14.999 14 13.864 14.796 13 16.011 13c1.217 0 1.966.864 1.989 1.999C18 16.11 17.228 17 15.988 17zM35 24.5c0-3.038-2.462-5.5-5.5-5.5-1.862 0-3.505.928-4.5 2.344V19h-4v15h4v-8c0-1.657 1.343-3 3-3s3 1.343 3 3v8h4C35 34 35 24.921 35 24.5z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($instagram_url)) : ?>
                <a href="<?= $instagram_url ?>" target="_blank" class="ppress-pf-social-icon dpf-instagram">
                    <svg viewBox="0 0 128 128" width="35px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g>
                            <linearGradient gradientTransform="matrix(1 0 0 -1 594 633)" gradientUnits="userSpaceOnUse" id="SVGID_1_" x1="-566.7114" x2="-493.2875" y1="516.5693" y2="621.4296">
                                <stop offset="0" style="stop-color:#FFB900"/>
                                <stop offset="1" style="stop-color:#9100EB"/>
                            </linearGradient>
                            <circle cx="64" cy="64" fill="url(#SVGID_1_)" r="64"/>
                        </g>
                        <g>
                            <g>
                                <path d="M82.333,104H45.667C33.72,104,24,94.281,24,82.333V45.667C24,33.719,33.72,24,45.667,24h36.666    C94.281,24,104,33.719,104,45.667v36.667C104,94.281,94.281,104,82.333,104z M45.667,30.667c-8.271,0-15,6.729-15,15v36.667    c0,8.271,6.729,15,15,15h36.666c8.271,0,15-6.729,15-15V45.667c0-8.271-6.729-15-15-15H45.667z" fill="#FFFFFF"/>
                            </g>
                            <g>
                                <path d="M64,84c-11.028,0-20-8.973-20-20c0-11.029,8.972-20,20-20s20,8.971,20,20C84,75.027,75.028,84,64,84z     M64,50.667c-7.352,0-13.333,5.981-13.333,13.333c0,7.353,5.981,13.333,13.333,13.333S77.333,71.353,77.333,64    C77.333,56.648,71.353,50.667,64,50.667z" fill="#FFFFFF"/>
                            </g>
                            <g>
                                <circle cx="85.25" cy="42.75" fill="#FFFFFF" r="4.583"/>
                            </g>
                        </g>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($youtube_url)) : ?>
                <a href="<?= $youtube_url ?>" target="_blank" class="ppress-pf-social-icon dpf-youtube">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         width="48" height="48"
                         viewBox="0 0 48 48"
                         style=" fill:#000000;">
                        <path fill="#f44336" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                        <path fill="#fff" d="M17,34l18-10L17,14V34z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($vk_url)) : ?>
                <a href="<?= $vk_url ?>" target="_blank" class="ppress-pf-social-icon dpf-vk">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                         width="48" height="48"
                         viewBox="0 0 48 48"
                         style=" fill:#000000;">
                        <path fill="#1976d2" d="M24 4A20 20 0 1 0 24 44A20 20 0 1 0 24 4Z"></path>
                        <path fill="#fff" d="M35.937,18.041c0.046-0.151,0.068-0.291,0.062-0.416C35.984,17.263,35.735,17,35.149,17h-2.618 c-0.661,0-0.966,0.4-1.144,0.801c0,0-1.632,3.359-3.513,5.574c-0.61,0.641-0.92,0.625-1.25,0.625C26.447,24,26,23.786,26,23.199 v-5.185C26,17.32,25.827,17,25.268,17h-4.649C20.212,17,20,17.32,20,17.641c0,0.667,0.898,0.827,1,2.696v3.623 C21,24.84,20.847,25,20.517,25c-0.89,0-2.642-3-3.815-6.932C16.448,17.294,16.194,17,15.533,17h-2.643 C12.127,17,12,17.374,12,17.774c0,0.721,0.6,4.619,3.875,9.101C18.25,30.125,21.379,32,24.149,32c1.678,0,1.85-0.427,1.85-1.094 v-2.972C26,27.133,26.183,27,26.717,27c0.381,0,1.158,0.25,2.658,2c1.73,2.018,2.044,3,3.036,3h2.618 c0.608,0,0.957-0.255,0.971-0.75c0.003-0.126-0.015-0.267-0.056-0.424c-0.194-0.576-1.084-1.984-2.194-3.326 c-0.615-0.743-1.222-1.479-1.501-1.879C32.062,25.36,31.991,25.176,32,25c0.009-0.185,0.105-0.361,0.249-0.607 C32.223,24.393,35.607,19.642,35.937,18.041z"></path>
                    </svg>
                </a>
            <?php endif; ?>

            <?php if ( ! empty($github_url)) : ?>
                <a href="<?= $github_url ?>" target="_blank" class="ppress-pf-social-icon dpf-github">
                    <svg height="35" viewBox="0 0 16 16" version="1.1" width="35">
                        <path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path>
                    </svg>
                </a>
            <?php endif; ?>

        </div>
        <?php
    }

    public static function get_instance($form_id, $form_type)
    {
        static $instance = false;

        $class = get_called_class();

        if ( ! $instance) {
            $instance = new $class($form_id, $form_type);
        }

        return $instance;
    }
}