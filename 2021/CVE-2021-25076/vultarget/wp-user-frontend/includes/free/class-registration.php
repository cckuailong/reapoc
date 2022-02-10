<?php

/**
 * Registration handler class
 *
 * @since 2.5.8
 */
class WPUF_Registration {
    private $registration_errors = [];

    private $messages = [];

    private static $_instance;

    public $atts = [];

    public $userrole = '';

    public function __construct() {
        add_shortcode( 'wpuf-registration', [ $this, 'registration_form' ] );

        add_action( 'init', [ $this, 'process_registration' ] );
        add_action( 'init', [ $this, 'wp_registration_page_redirect' ] );
        add_action( 'template_redirect', [ $this, 'registration_page_redirects' ] );

        add_filter( 'register_url', [ $this, 'get_registration_url' ] );
        add_filter( 'wpuf_registration_redirect', [ $this, 'redirect_to_payment_page' ], 10, 2 );
    }

    /**
     * Singleton object
     *
     * @return self
     */
    public static function init() {
        if ( ! self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Is override enabled
     *
     * @return bool
     */
    public function is_override_enabled() {
        $override = wpuf_get_option( 'register_link_override', 'wpuf_profile', 'off' );

        if ( $override !== 'on' ) {
            return false;
        }

        return true;
    }

    /**
     * Get action url based on action type
     *
     * @param string $action
     * @param string $redirect_to url to redirect to
     *
     * @return string
     */
    public function get_action_url( $action = 'registration', $redirect_to = '' ) {
        $root_url = $this->get_registration_url();

        switch ( $action ) {
            case 'register':
                return $this->get_registration_url();
                break;

            default:
                if ( empty( $redirect_to ) ) {
                    return $root_url;
                }

                return add_query_arg( [ 'redirect_to' => urlencode( $redirect_to ) ], $root_url );
                break;
        }
    }

    /**
     * Get registration page url
     *
     * @return bool|string
     */
    public function get_registration_url( $register_url = null ) {
        $register_link_override = wpuf_get_option( 'register_link_override', 'wpuf_profile', false );
        $page_id                = wpuf_get_option( 'reg_override_page', 'wpuf_profile', false );

        if ( $register_link_override === 'off' ) {
            return $register_url;
        }

        if ( ! $page_id ) {
            return false;
        }

        $url = get_permalink( $page_id );

        return apply_filters( 'wpuf_register_url', $url, $page_id );
    }

    /**
     * Get actions links for displaying in forms
     *
     * @param array $args
     *
     * @return string
     */
    public function get_action_links( $args = [] ) {
        $defaults = [
            'register'     => true,
        ];

        $args   = wp_parse_args( $args, $defaults );
        $links  = [];

        if ( $args['register'] && get_option( 'users_can_register' ) ) {
            $links[] = sprintf( '<a href="%s">%s</a>', $this->get_action_url( 'register' ), esc_html__( 'Register', 'wp-user-frontend' ) );
        }

        return implode( ' | ', $links );
    }

    /**
     * Shows the registration form
     *
     * @return string
     */
    public function registration_form( $atts ) {
        $atts = shortcode_atts(
            [
                'role' => '',
            ], $atts
        );
        $userrole = $atts['role'];

        $roleencoded = wpuf_encryption( $userrole );

        $reg_page = $this->get_registration_url();

        if ( false === $reg_page ) {
            return;
        }

        ob_start();

        if ( is_user_logged_in() ) {
            wpuf_load_template(
                'logged-in.php', [
                    'user' => wp_get_current_user(),
                ]
            );
        } else {
            $queries = wp_unslash( $_GET );

            array_walk(
                $queries, function ( &$a ) {
                    $a = sanitize_text_field( $a );
                }
            );

            $args = [
                'action_url' => add_query_arg( $queries, $reg_page ),
                'userrole'   => $roleencoded,
            ];

            wpuf_load_template( 'registration-form.php', $args );
        }

        return ob_get_clean();
    }

    /**
     * Process registration form
     *
     * @return void
     */
    public function process_registration() {
        if ( ! empty( $_POST['wpuf_registration'] ) && ! empty( $_POST['_wpnonce'] ) ) {
            $userdata = [];

            if ( isset( $_POST['_wpnonce'] ) ) {
                $nonce = sanitize_key( wp_unslash( $_POST['_wpnonce'] ) );
                wp_verify_nonce( $nonce, 'wpuf_registration_action' );
            }

            $validation_error = new WP_Error();

            $reg_fname = isset( $_POST['reg_fname'] ) ? sanitize_text_field( wp_unslash( $_POST['reg_fname'] ) ) : '';
            $reg_lname = isset( $_POST['reg_lname'] ) ? sanitize_text_field( wp_unslash( $_POST['reg_lname'] ) ) : '';
            $reg_email = isset( $_POST['reg_email'] ) ? sanitize_email( wp_unslash( $_POST['reg_email'] ) ) : '';
            $pwd1      = isset( $_POST['pwd1'] ) ? sanitize_text_field( wp_unslash( $_POST['pwd1'] ) ) : '';
            $pwd2      = isset( $_POST['pwd2'] ) ? sanitize_text_field( wp_unslash( $_POST['pwd2'] ) ) : '';
            $log       = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
            $urhidden  = isset( $_POST['urhidden'] ) ? sanitize_text_field( wp_unslash( $_POST['urhidden'] ) ) : '';

            $validation_error = apply_filters( 'wpuf_process_registration_errors', $validation_error, $reg_fname, $reg_lname, $reg_email, $log, $pwd1, $pwd2 );

            if ( $validation_error->get_error_code() ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . $validation_error->get_error_message();

                return;
            }

            if ( empty( $reg_fname ) ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'First name is required.', 'wp-user-frontend' );

                return;
            }

            if ( empty( $reg_lname ) ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'Last name is required.', 'wp-user-frontend' );

                return;
            }

            if ( empty( $reg_email ) ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'Email is required.', 'wp-user-frontend' );

                return;
            }

            if ( empty( $log ) ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'Username is required.', 'wp-user-frontend' );

                return;
            }

            if ( empty( $pwd1 ) ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'Password is required.', 'wp-user-frontend' );

                return;
            }

            if ( empty( $pwd2 ) ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'Confirm Password is required.', 'wp-user-frontend' );

                return;
            }

            if ( $pwd1 !== $pwd2 ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'Passwords are not same.', 'wp-user-frontend' );

                return;
            }

            if ( get_user_by( 'login', $log ) === $log ) {
                $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'A user with same username already exists.', 'wp-user-frontend' );

                return;
            }

            if ( is_email( $log ) ) {
                $user = get_user_by( 'email', $log );
            }

            if ( $user && apply_filters( 'wpuf_get_username_from_email', true ) ) {
                if ( isset( $user->user_login ) ) {
                    $userdata['user_login'] = $user->user_login;
                } else {
                    $this->registration_errors[] = '<strong>' . esc_html__( 'Error', 'wp-user-frontend' ) . ':</strong> ' . esc_html__( 'A user could not be found with this email address.', 'wp-user-frontend' );

                    return;
                }
            } else {
                $userdata['user_login'] = $log;
            }

            $dec_role = wpuf_decryption( $urhidden );

            $userdata['first_name'] = $reg_fname;
            $userdata['last_name']  = $reg_lname;
            $userdata['user_email'] = $reg_email;
            $userdata['user_pass']  = $pwd1;

            if ( get_role( $dec_role ) ) {
                $userdata['role'] = $dec_role;
            }

            $user = wp_insert_user( $userdata );

            if ( is_wp_error( $user ) ) {
                $this->registration_errors[] = $user->get_error_message();

                return;
            } else {
                $wpuf_user  = new WP_User( $user );
                $user_login = stripslashes( $wpuf_user->user_login );
                $user_email = stripslashes( $wpuf_user->user_email );
                $blogname   = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

                /* translators: %s: site name */
                $message = sprintf( esc_html__( 'New user registration on your site %s:', 'wp-user-frontend' ), get_option( 'blogname' ) ) . "\r\n\r\n";
                /* translators: %s: username */
                $message .= sprintf( esc_html__( 'Username: %s', 'wp-user-frontend' ), $user_login ) . "\r\n\r\n";
                /* translators: %s: email */
                $message .= sprintf( esc_html__( 'E-mail: %s', 'wp-user-frontend' ), $user_email ) . "\r\n";
                $subject = 'New User Registration';

                $subject = apply_filters( 'wpuf_default_reg_admin_mail_subject', $subject );
                $message = apply_filters( 'wpuf_default_reg_admin_mail_body', $message );
                /* translators: %s %s: site name subject*/
                wp_mail( get_option( 'admin_email' ), sprintf( esc_html__( '[%1$s] %2$s', 'wp-user-frontend' ), $blogname, $subject ), $message );
                /* translators: %s: username */
                $message = sprintf( esc_html__( 'Hi, %s', 'wp-user-frontend' ), $user_login ) . "\r\n";
                $message .= 'Congrats! You are Successfully registered to ' . $blogname . "\r\n\r\n";
                $message .= 'Thanks';
                $subject = 'Thank you for registering';

                $subject = apply_filters( 'wpuf_default_reg_mail_subject', $subject );
                $message = apply_filters( 'wpuf_default_reg_mail_body', $message );
                /* translators: %s %s: site name subject*/
                wp_mail( $user_email, sprintf( esc_html__( '[%1$s] %2$s', 'wp-user-frontend' ), $blogname, $subject ), $message );
            }

            $autologin_after_registration = wpuf_get_option( 'autologin_after_registration', 'wpuf_profile', 'on' );

            if ( $autologin_after_registration === 'on' ) {
                wp_clear_auth_cookie();
                wp_set_current_user( $user );
                wp_set_auth_cookie( $user );
            }

            if ( is_wp_error( $user ) ) {
                $this->registration_errors[] = $user->get_error_message();

                return;
            } else {
                if ( ! empty( $_POST['redirect_to'] ) ) {
                    $redirect = sanitize_text_field( wp_unslash( $_POST['redirect_to'] ) );
                } else {
                    $redirect = $this->get_registration_url() . '?success=yes';
                }
                wp_redirect( apply_filters( 'wpuf_registration_redirect', $redirect, $user ) );
                exit;
            }
        }
    }

    /**
     * Redirect to registration page
     *
     * @return void
     */
    public function wp_registration_page_redirect() {
        global $pagenow;
        $action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';

        if ( ! is_admin() && $pagenow === 'wp-login.php' && $action === 'register' ) {
            if ( wpuf_get_option( 'register_link_override', 'wpuf_profile' ) !== 'on' ) {
                return;
            }

            $reg_page = get_permalink( wpuf_get_option( 'reg_override_page', 'wpuf_profile', false ) );
            wp_redirect( $reg_page );
            exit;
        }
    }

    /**
     * Redirect to subscription page
     *
     * @since 3.3.0
     *
     * @return void
     */
    public function registration_page_redirects() {
        global $post;

        $registration_page = wpuf_get_option( 'reg_override_page', 'wpuf_profile' );

        if ( ! isset( $post->ID ) || $post->ID !== absint( $registration_page ) ) {
            return;
        }

        // Choose subscription pack first then register
        if ( $this->is_activated_subscription_on_registration() ) {
            $subscription_page_id = wpuf_get_option( 'subscription_page', 'wpuf_payment' );

            if ( empty( $subscription_page_id ) ) {
                WP_User_Frontend::log( 'subscription-on-registration', esc_html__( 'Subscription Page settings not set in admin settings', 'wp-user-frontend' ) );
                return;
            }

            if ( isset( $_GET['type'] ) && 'wpuf_sub' === $_GET['type'] && ! empty( $_GET['pack_id'] ) ) {
                return;
            }

            wp_safe_redirect( get_permalink( $subscription_page_id ) );
            exit;
        }
    }

    /**
     * Show errors on the form
     *
     * @return void
     */
    public function show_errors() {
        /**
         * Filter the allowed html for registration error notice
         *
         * @since 3.3.0
         *
         * @param array $allowed_html
         */
        $allowed_html = apply_filters(
            'wpuf_registration_errors_allowed_html', [
                'strong' => [],
            ]
        );

        if ( $this->registration_errors ) {
            foreach ( $this->registration_errors as $error ) {
                printf(
                    '<div class="wpuf-error">%s</div>',
                    wp_kses( $error, $allowed_html )
                );
            }
        }
    }

    /**
     * Show messages on the form
     *
     * @return void
     */
    public function show_messages() {
        if ( $this->messages ) {
            foreach ( $this->messages as $message ) {
                printf( '<div class="wpuf-message">%s</div>', esc_html( $message ) );
            }
        }
    }

    /**
     * Get a posted value for showing in the form field
     *
     * @param string $key
     *
     * @return string
     */
    public static function get_posted_value( $key ) {
        $get = wp_unslash( $_GET );

        if ( isset( $_REQUEST[ $key ] ) ) {
            $required_key = sanitize_text_field( wp_unslash( $_REQUEST[ $key ] ) );
            return $required_key;
        }

        return '';
    }

    /**
     * Check if subscription need to be selected before registration
     *
     * Settings could be found under Payments section in WPUF admin settings.
     *
     * @since 3.3.0
     *
     * @return bool
     */
    public function is_activated_subscription_on_registration() {
        $enable_payment        = wpuf_get_option( 'enable_payment', 'wpuf_payment' );
        $register_subscription = wpuf_get_option( 'register_subscription', 'wpuf_payment' );

        if ( wpuf_validate_boolean( $enable_payment ) && wpuf_validate_boolean( $register_subscription ) ) {
            return true;
        }

        return false;
    }

    /**
     * Filter the redirect page url to payment page
     *
     * @since 3.3.0
     *
     * @param string $redirect
     * @param int    $user
     *
     * @return string
     */
    public function redirect_to_payment_page( $redirect, $user ) {
        $get = wp_unslash( $_GET );

        if ( isset( $get['type'] ) && 'wpuf_sub' === $get['type'] && ! empty( $get['pack_id'] ) && $user ) {
            $payment_page_id = wpuf_get_option( 'payment_page', 'wpuf_payment' );
            $payment_page    = get_permalink( $payment_page_id );

            if ( $payment_page ) {
                $redirect = add_query_arg(
                    [
                        'action'  => 'wpuf_pay',
                        'user_id' => $user,
                        'type'    => 'pack',
                        'pack_id' => $get['pack_id'],
                    ], $payment_page
                );
            }
        }

        return $redirect;
    }
}
