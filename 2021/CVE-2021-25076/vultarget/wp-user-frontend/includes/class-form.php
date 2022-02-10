<?php

class WPUF_Form {

    /**
     * The form ID
     *
     * @var int
     */
    public $id;

    /**
     * Form fields
     *
     * @var array
     */
    public $form_fields = [];

    public function __construct( $form ) {
        if ( is_numeric( $form ) ) {
            $this->id   = $form;
            $this->data = get_post( $form );
        } elseif ( is_a( $form, 'WP_Post' ) ) {
            $this->id   = $form->ID;
            $this->data = $form;
        }
    }

    /**
     * Returns form title
     *
     * @return string
     */
    public function get_title() {
        return $this->data->post_title;
    }

    /**
     * Get the form settings
     *
     * @return array
     */
    public function get_settings() {
        $form_settings = wpuf_get_form_settings( $this->id );

        return $form_settings;
    }

    /**
     * Get guest post settings
     *
     * @return bool
     */
    public function guest_post() {
        $settings = $this->get_settings();

        if ( isset( $settings['guest_post'] ) && $settings['guest_post'] === 'true' ) {
            return true;
        }

        return false;
    }

    /**
     * Check if payment is enabled
     *
     * @return bool
     */
    public function is_charging_enabled() {
        $settings = $this->get_settings();

        if ( isset( $settings['payment_options'] ) && $settings['payment_options'] === 'true' ) {
            return true;
        }

        return false;
    }

    /**
     * Check if pay per post is enabled
     *
     * @return bool
     */
    public function is_enabled_pay_per_post() {
        $settings = $this->get_settings();

        if ( isset( $settings['enable_pay_per_post'] ) && $settings['enable_pay_per_post'] === 'true' ) {
            return true;
        }

        return false;
    }

    /**
     * Check if subscription pack is forced
     *
     * @return bool
     */
    public function is_enabled_force_pack() {
        $settings = $this->get_settings();

        if ( isset( $settings['force_pack_purchase'] ) && $settings['force_pack_purchase'] === 'true' ) {
            return true;
        }

        return false;
    }

    /**
     * Get pay per cost amount
     *
     * @return int
     */
    public function get_pay_per_post_cost() {
        $settings = $this->get_settings();

        if ( isset( $settings['pay_per_post_cost'] ) && $settings['pay_per_post_cost'] > 0 ) {
            return $settings['pay_per_post_cost'];
        }

        return 0;
    }

    /**
     * Check if fallback cost after subscription pack expiration is enabled
     *
     * @return bool
     */
    public function is_enabled_fallback_cost() {
        $settings = $this->get_settings();

        if ( isset( $settings['fallback_ppp_enable'] ) && $settings['fallback_ppp_enable'] === 'true' ) {
            return true;
        }

        return false;
    }

    /**
     * Get the fallback cost amount
     *
     * @return int
     */
    public function get_subs_fallback_cost() {
        $settings = $this->get_settings();

        if ( isset( $settings['fallback_ppp_cost'] ) && $settings['fallback_ppp_cost'] > 0 ) {
            return $settings['fallback_ppp_cost'];
        }

        return 0;
    }

    /**
     * Check if the form submission is open
     *
     * @param object $form
     * @param array  $form_settings
     * @param array  $atts
     * @param string $type
     * @param int    $post_id
     *
     * @return array
     */
    public function is_submission_open( $form, $form_settings ) {
        $info          = '';
        $user_can_post = 'yes';
        $current_user  = wpuf_get_user();
        $guest_post_enabled = $this->guest_post();

        if ( isset( $this->form_settings['message_restrict'] ) && ! $guest_post_enabled && ! is_user_logged_in() ) {
            $user_can_post = 'no';
            $info          = $this->form_settings['message_restrict'];
        }

        if ( $this->is_charging_enabled() ) {
            $pay_per_post      = $this->is_enabled_pay_per_post();
            $pay_per_post_cost = (float) $this->get_pay_per_post_cost();
            $force_pack        = $this->is_enabled_force_pack();
            $fallback_enabled  = $this->is_enabled_fallback_cost();
            $fallback_cost     = $this->get_subs_fallback_cost();
            $has_post_count    = $current_user->subscription()->has_post_count( $form_settings['post_type'] );

            // guest post payment checking
            if ( ! is_user_logged_in() && isset( $form_settings['guest_post'] ) && $form_settings['guest_post'] === 'true' ) {

                //if ( $form->is_charging_enabled() ) {

                if ( $force_pack ) {
                    $user_can_post = 'no';
                    $pack_page     = get_permalink( wpuf_get_option( 'subscription_page', 'wpuf_payment' ) );
                    /* translators: %s: Pack page link */
                    $info          = sprintf( __( 'You need to  <a href="%s">purchase a subscription package</a> to post in this form', 'wp-user-frontend' ), $pack_page );
                } elseif ( $pay_per_post && ! $force_pack ) {
                    $user_can_post = 'yes';
                    // $info = sprintf( __( 'There is a <strong>%s</strong> charge to add a new post.', 'wpuf' ), wpuf_format_price( $pay_per_post_cost ));
                    // echo '<div class="wpuf-info">' . apply_filters( 'wpuf_ppp_notice', $info, $id, $form_settings ) . '</div>';
                } else {
                    $user_can_post = 'no';
                    $info          = sprintf( __( 'Payment type not selected for this form. Please contact admin.', 'wp-user-frontend' ) );
                }

                // } else {
                //     $user_can_post = 'yes';
                // }
            } else {
                // regular payment checking
                if ( $force_pack && is_user_logged_in() ) {
                    $current_pack = $current_user->subscription()->current_pack();

                    if ( ! is_wp_error( $current_pack ) ) {
                        // user has valid post count
                        if ( $has_post_count ) {
                            $user_can_post = 'yes';
                        } else {
                            if ( $fallback_enabled && ! $has_post_count ) {
                                $user_can_post = 'yes';
                            } else {
                                $user_can_post = 'no';
                                $info          = __( 'Post Limit Exceeded for your purchased subscription pack.', 'wp-user-frontend' );
                            }
                        }
                    } else {
                        $user_can_post = 'no';
                        $info          = $current_pack->get_error_message();
                    }
                } elseif ( $pay_per_post && is_user_logged_in() && ! $current_user->subscription()->has_post_count( $form_settings['post_type'] ) ) {
                    $user_can_post = 'yes';
                    // $info = sprintf( __( 'There is a <strong>%s</strong> charge to add a new post.', 'wpuf' ), wpuf_format_price( $pay_per_post_cost ));
                    // echo '<div class="wpuf-info">' . apply_filters( 'wpuf_ppp_notice', $info, $id, $form_settings ) . '</div>';
                } elseif ( ! $pay_per_post && ! $current_user->subscription()->has_post_count( $form_settings['post_type'] ) ) {
                    $user_can_post = 'no';
                    $info          = sprintf( __( 'Payment type not selected for this form. Please contact admin.', 'wp-user-frontend' ) );
                } else {
                    $user_can_post = 'no';

                    if ( ! is_user_logged_in() ) {
                        $info = $form_settings['message_restrict'];
                    } else {
                        $info = sprintf( __( 'Payment type not selected for this form. Please contact admin.', 'wp-user-frontend' ) );
                    }
                }
            }
        } else {
            if ( isset( $form_settings['guest_post'] ) && $form_settings['guest_post'] === 'true' && !
                is_user_logged_in() ) {
                $user_can_post = 'yes';
            }
        }

        return [ $user_can_post, $info ];
    }

    /**
     * Prepare_entries
     *
     * @return array
     */
    public function prepare_entries() {
        $fields       = wpuf()->fields->get_fields();
        $form_fields  = $this->get_fields();
        $entry_fields = [];

        $ignore_list = apply_filters( 'wpuf_entry_ignore_list', [ 'recaptcha' ] );

        foreach ( $form_fields as $field ) {
            if ( in_array( $field['template'], $ignore_list, true ) ) {
                continue;
            }

            if ( ! array_key_exists( $field['template'], $fields ) ) {
                continue;
            }

            $field_class = $fields[ $field['template'] ];

            $entry_fields[ $field['name'] ] = $field_class->prepare_entry( $field );
        }

        return apply_filters( 'wpuf_prepare_entries', $entry_fields );
    }

    /**
     * Get all form fields of this form
     *
     * @return array
     */
    public function get_fields() {

        // return if already fetched
        if ( $this->form_fields ) {
            return $this->form_fields;
        }

        $fields = get_children(
            [
				'post_parent' => $this->id,
				'post_status' => 'publish',
				'post_type'   => 'wpuf_input',
				'numberposts' => '-1',
				'orderby'     => 'menu_order',
				'order'       => 'ASC',
			]
        );

        $form_fields = [];

        foreach ( $fields as $key => $content ) {
            $field = maybe_unserialize( $content->post_content );

            if ( empty( $field['template'] ) ) {
                continue;
            }

            $field['id'] = $content->ID;

            // Add inline property for radio and checkbox fields
            $inline_supported_fields = apply_filters( 'wpuf_inline_supported_fields_list', [ 'radio_field', 'checkbox_field' ] );

            if ( in_array( $field['template'], $inline_supported_fields, true ) ) {
                if ( ! isset( $field['inline'] ) ) {
                    $field['inline'] = 'no';
                }
            }

            // Add 'selected' property
            $option_based_fields = apply_filters( 'wpuf_option_based_fields_list', [ 'dropdown_field', 'multiple_select', 'radio_field', 'checkbox_field' ] );

            if ( in_array( $field['template'], $option_based_fields, true ) ) {
                if ( ! isset( $field['selected'] ) ) {
                    if ( 'dropdown_field' === $field['template'] || 'radio_field' === $field['template'] ) {
                        $field['selected'] = '';
                    } else {
                        $field['selected'] = [];
                    }
                }
            }

            // Add 'multiple' key for template:repeat
            if ( 'repeat_field' === $field['template'] && ! isset( $field['multiple'] ) ) {
                $field['multiple'] = '';
            }

            if ( 'recaptcha' === $field['template'] ) {
                $field['name']              = 'recaptcha';
                $field['enable_no_captcha'] = isset( $field['enable_no_captcha'] ) ? $field['enable_no_captcha'] : '';
                $field['recaptcha_theme']   = isset( $field['recaptcha_theme'] ) ? $field['recaptcha_theme'] : 'light';
            }

            // $form_fields[] = apply_filters( 'wpuf-get-form-field', $field );

            $form_fields[] = apply_filters( 'wpuf-get-form-fields', $field );
        }

        // $this->form_fields = apply_filters( 'wpuf-get-form-fields', $form_fields );

        // return $this->form_fields;

        return $form_fields;
    }
}
