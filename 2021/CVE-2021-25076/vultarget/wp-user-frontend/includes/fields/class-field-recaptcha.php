<?php

/**
 * Recaptcha Field Class
 */
class WPUF_Form_Field_reCaptcha extends WPUF_Field_Contract {

    public function __construct() {
        $this->name       = __( 'reCaptcha', 'wp-user-frontend' );
        $this->input_type = 'recaptcha';
        $this->icon       = 'qrcode';
    }

    /**
     * Render the Recaptcha field
     *
     * @param array $field_settings
     * @param int   $form_id
     *
     * @return void
     */
    public function render( $field_settings, $form_id, $type = 'post', $post_id = null ) {
        if ( $post_id ) {
            return;
        }
        $recaptcha_site = wpuf_get_option( 'recaptcha_public', 'wpuf_general' );
        // 'recaptcha_secret'  => wpuf_get_option( 'recaptcha_private', 'wpuf_general' ),

        // $settings     = wpuf_get_settings( 'recaptcha' );
        $is_invisible = false;
        $public_key   = isset( $recaptcha_site ) ? $recaptcha_site : '';
        $theme        = isset( $field_settings['recaptcha_theme'] ) ? $field_settings['recaptcha_theme'] : 'light';

        if ( isset( $field_settings['recaptcha_type'] ) ) {
            $is_invisible = $field_settings['recaptcha_type'] == 'invisible_recaptcha' ? true : false;
        }

        $invisible_css   = $is_invisible ? ' style="margin: 0; padding: 0" ' : ''; ?> <li <?php $this->print_list_attributes( $field_settings );
        echo esc_attr( $invisible_css ); ?>>

            <?php

            if ( !$is_invisible ) {
                $this->print_label( $field_settings );
            }

        if ( !$public_key ) {
            esc_html_e( 'reCaptcha API key is missing.', 'wp-user-frontend' );
        } else {
            ?>

                <div class="wpuf-fields <?php echo esc_attr( ' wpuf_' . $field_settings['name'] . '_' . $form_id ); ?>">
                    <script>
                        function wpufRecaptchaCallback(token) {
                            jQuery('[name="g-recaptcha-response"]').val(token);
                            jQuery('.wpuf-submit-button').attr('disabled',false).show();
                            jQuery('.wpuf-submit-btn-recaptcha').hide();
                        }

                        jQuery(document).ready( function($) {
                            $('.wpuf-submit-button').attr('disabled',true);
                        });
                    </script>

                    <input type="hidden" name="g-recaptcha-response">
                <?php

                if ( $is_invisible ) { ?>

                    <script src="https://www.google.com/recaptcha/api.js?onload=wpufreCaptchaLoaded&render=explicit&hl=en" async defer></script>

                    <script>

                        jQuery(document).ready(function($) {
                            var btn = $('.wpuf-submit-button');
                            var gc_btn = btn.clone().removeClass().addClass('wpuf-submit-btn-recaptcha').attr('disabled',false);
                            btn.after(gc_btn);
                            btn.hide();

                            $(document).on('click','.wpuf-submit-btn-recaptcha',function(e){
                                e.preventDefault();
                                e.stopPropagation();
                                grecaptcha.execute();
                            })
                        });

                        var wpufreCaptchaLoaded = function() {

                            grecaptcha.render('recaptcha', {
                                'size' : 'invisible',
                                'sitekey' : '<?php echo esc_attr( $public_key ); ?>',
                                'callback' : wpufRecaptchaCallback
                            });

                            grecaptcha.execute();
                        };
                    </script>

                    <div id='recaptcha' class="g-recaptcha" data-theme="<?php echo esc_attr( $theme ); ?>" data-sitekey="<?php echo esc_attr( $public_key ); ?>" data-callback="wpufRecaptchaCallback" data-size="invisible"></div>

                <?php } else { ?>

                    <script src="https://www.google.com/recaptcha/api.js"></script>
                    <div id='recaptcha' data-theme="<?php echo esc_attr( $theme ); ?>" class="g-recaptcha" data-sitekey="<?php echo esc_attr( $public_key ); ?>" data-callback="wpufRecaptchaCallback"></div>
                <?php } ?>

                </div>

            <?php
        } ?>

        </li>

        <?php
    }

    /**
     * Custom validator
     *
     * @return array
     */
    public function get_validator() {
        return [
            'callback'      => 'has_recaptcha_api_keys',
            'button_class'  => 'button-faded',
            'msg_title'     => __( 'Site key and Secret key', 'wp-user-frontend' ),
            'msg'           => sprintf(
                __( 'You need to set Site key and Secret key in <a href="%s" target="_blank">Settings</a> in order to use "Recaptcha" field. <a href="%s" target="_blank">Click here to get the these key</a>.', 'wp-user-frontend' ),
                admin_url( 'admin.php?page=wpuf-settings' ),
                'https://www.google.com/recaptcha/'
             ),
        ];
    }

    /**
     * Get field options setting
     *
     * @return array
     */
    public function get_options_settings() {

        // $default_options = $this->get_default_option_settings(false,array('dynamic'));

        $settings = [
            [
                'name'          => 'label',
                'title'         => __( 'Title', 'wp-user-frontend' ),
                'type'          => 'text',
                'section'       => 'basic',
                'priority'      => 10,
                'help_text'     => __( 'Title of the section', 'wp-user-frontend' ),
            ],

            [
                'name'          => 'recaptcha_type',
                'title'         => 'reCaptcha type',
                'type'          => 'radio',
                'options'       => [
                    'enable_no_captcha'    => __( 'Enable noCaptcha', 'wp-user-frontend' ),
                    'invisible_recaptcha'  => __( 'Enable Invisible reCaptcha', 'wp-user-frontend' ),
                ],
                'default'       => 'enable_no_captcha',
                'section'       => 'basic',
                'priority'      => 11,
                'help_text'     => __( 'Select reCaptcha type', 'wp-user-frontend' ),
            ],

            [
                'name'          => 'recaptcha_theme',
                'title'         => 'reCaptcha Theme',
                'type'          => 'radio',
                'options'       => [
                    'light' => __( 'Light', 'wp-user-frontend' ),
                    'dark'  => __( 'Dark', 'wp-user-frontend' ),
                ],
                'default'       => 'light',
                'section'       => 'advanced',
                'priority'      => 12,
                'help_text'     => __( 'Select reCaptcha Theme', 'wp-user-frontend' ),
            ],
        ];

        // return array_merge( $default_options,$settings);

        return $settings;
    }

    /**
     * Get the field props
     *
     * @return array
     */
    public function get_field_props() {

        $props = [
            'input_type'      => 'recaptcha',
            'template'        => $this->get_type(),
            'label'           => '',
            'recaptcha_type'  => 'enable_no_captcha',
            'is_meta'         => 'yes',
            'id'              => 0,
            'is_new'          => true,
            'wpuf_cond'       => null,
            'recaptcha_theme' => 'light',
        ];

        return $props;
    }
}
