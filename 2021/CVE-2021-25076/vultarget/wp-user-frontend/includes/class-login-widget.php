<?php

/**
 * Ajax Login and Forgot password handler class
 *
 * @since 2.8
 */
class WPUF_Login_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'WPUF_Login_Widget',
            __( 'WPUF Ajax Login', 'wp-user-frontend' ),
            [ 'description' => __( 'Ajax Login widget for WP User Frontend', 'wp-user-frontend' )]
         );

        add_action( 'wp_ajax_nopriv_wpuf_ajax_login', [ $this, 'ajax_login' ] );
        add_action( 'wp_ajax_nopriv_wpuf_lost_password', [ $this, 'ajax_reset_pass' ] );
        add_action( 'wp_ajax_wpuf_ajax_logout', [ $this, 'ajax_logout' ] );
    }

    /**
     * Ajax Login function
     *
     * @return void
     */
    public function ajax_login() {
        $user_login     = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
        $user_pass      = isset( $_POST['pwd'] ) ? sanitize_text_field( wp_unslash( $_POST['pwd'] ) ) : '';
        $rememberme     = isset( $_POST['rememberme'] ) ? sanitize_text_field( wp_unslash( $_POST['rememberme'] ) ) : false;
        $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce , 'wpuf_lost_pass' ) ) {

        }

        if ( empty( $user_login ) || empty( $user_pass ) ) {
            wp_send_json_error( [ 'message'=> __( 'Please fill all form fields', 'wp-user-frontend' ) ] );
        } else {
            $user = wp_signon( ['user_login' => $user_login, 'user_password' => $user_pass], false );

            if ( is_wp_error( $user ) ) {
                wp_send_json_error( [ 'message'=> $user->get_error_message() ] );
            } else {
                wp_send_json_success( [ 'message'=> __( 'Login successful!', 'wp-user-frontend' ) ] );
            }
        }
        wp_set_auth_cookie( $user->ID, $rememberme );
    }

    /**
     * Ajax Logout function
     *
     * @return void
     */
    public function ajax_logout() {
        wp_logout();
        wp_send_json_success( [ 'message'=> __( 'Logout successful!', 'wp-user-frontend' ) ] );
    }

    /**
     * Ajax password reset function
     *
     * @return void
     */
    public function ajax_reset_pass() {
        $username_or_email = isset( $_POST['user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) : '';
        $nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

        if ( isset( $nonce ) && ! wp_verify_nonce( $nonce, 'wpuf_lost_pass' ) ) {

        }

        // Check if input variables are empty
        if ( empty( $username_or_email ) ) {
            wp_send_json_error( [ 'error' => true, 'message'=> __( 'Please fill all form fields', 'wp-user-frontend' ) ] );
        } else {
            $username = is_email( $username_or_email ) ? sanitize_email( $username_or_email ) : sanitize_user( $username_or_email );

            $user_forgotten = $this->ajax_lostpassword_retrieve( $username );

            if ( is_wp_error( $user_forgotten ) ) {
                $lostpass_error_messages = $user_forgotten->errors;

                $display_errors = '';

                foreach ( $lostpass_error_messages as $error ) {
                    $display_errors .= '<p>' . $error[0] . '</p>';
                }

                wp_send_json_error( [ 'message' => $display_errors ] );
            } else {
                wp_send_json_success( [ 'message' => __( 'Password has been reset. Please check your email.', 'wp-user-frontend' ) ] );
            }
        }
    }

    /**
     * Password retrieve function
     *
     * @return mixed
     */
    private function ajax_lostpassword_retrieve( $user_input ) {
        global $wpdb, $wp_hasher;

        $errors = new WP_Error();

        if ( empty( $user_input ) ) {
            $errors->add( 'empty_username', __( '<strong>ERROR</strong>: Enter a username or email address.', 'wp-user-frontend' ) );
        } elseif ( strpos( $user_input, '@' ) ) {
            $user_data = get_user_by( 'email', trim( $user_input ) );

            if ( empty( $user_data ) ) {
                $errors->add( 'invalid_email', __( '<strong>ERROR</strong>: There is no user registered with that email address.', 'wp-user-frontend' ) );
            }
        } else {
            $login     = trim( $user_input );
            $user_data = get_user_by( 'login', $login );
        }

        /*
         * Fires before errors are returned from a password reset request.
         */
        do_action( 'lostpassword_post', $errors );

        if ( $errors->get_error_code() ) {
            return $errors;
        }

        if ( !$user_data ) {
            $errors->add( 'invalidcombo', __( '<strong>ERROR</strong>: Invalid username or email.', 'wp-user-frontend' ) );

            return $errors;
        }

        // Redefining user_login ensures we return the right case in the email.
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key        = get_password_reset_key( $user_data );

        if ( is_wp_error( $key ) ) {
            return $key;
        }

        $message = __( 'Someone has requested a password reset for the following account:', 'wp-user-frontend' ) . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf( __( 'Username: %s', 'wp-user-frontend' ), $user_login ) . "\r\n\r\n";
        $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'wp-user-frontend' ) . "\r\n\r\n";
        $message .= __( 'To reset your password, visit the following address:', 'wp-user-frontend' ) . "\r\n\r\n";
        $message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";

        $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

        $title = sprintf( __( '[%s] Password Reset', 'wp-user-frontend' ), $blogname );

        $title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

        $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

        if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
            $errors->add( 'mailfailed', __( '<strong>ERROR</strong>: The email could not be sent.Possible reason: your host may have disabled the mail() function.', 'wp-user-frontend' ) );
        }

        return true;
    }

    /**
     * Display Ajax Login widget
     *
     * @return void
     */
    public function widget( $args, $instance ) {
        wp_enqueue_script( 'wpuf_ajax_login' );

        $title            = apply_filters( 'widget_title', $instance['title'] );
        $log_in_header    = apply_filters( 'widget_text_content', $instance['log_in_header'] );
        $pwd_reset_header = apply_filters( 'widget_text_content', $instance['pwd_reset_header'] );
        $uname_label      = apply_filters( 'widget_text', $instance['uname_label'] );
        $pwd_label        = apply_filters( 'widget_text', $instance['pwd_label'] );
        $remember_label   = apply_filters( 'widget_text', $instance['remember_label'] );
        $log_in_label     = apply_filters( 'widget_text', $instance['log_in_label'] );
        $pass_reset_label = apply_filters( 'widget_text', $instance['pass_reset_label'] );

        echo wp_kses_post( $args['before_widget'] );

        if ( !empty( $title ) ) {
            echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
        }

        if ( is_user_logged_in() ) {
            $user_id = get_current_user_id();
            echo wp_kses_post( get_avatar( $user_id, 24 ) );
        }

        $login_args = [
            'form_id'           => 'wpuf_ajax_login_form',
            'label_username'    => $uname_label,
            'label_password'    => $pwd_label,
            'label_remember'    => $remember_label,
            'label_log_in'      => $log_in_label,
        ]; ?>
        <div class="login-widget-container">
            <?php
            if ( !is_user_logged_in() ) { ?>

                <!-- Login form -->
                <div class="wpuf-ajax-login-form">
                    <div class="wpuf-ajax-errors"></div>

                    <p><?php echo wp_kses_post( $log_in_header ); ?></p>

                    <?php
                    wp_login_form( $login_args );

                    if ( get_option( 'users_can_register' ) ) {
                        $registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register', 'wp-user-frontend' ) );
                        echo wp_kses( $registration_url, [
                            'a' => [
                                'href' => []
                            ]
                        ] );
                        echo esc_html( apply_filters( 'login_link_separator', ' | ' ) );
                    }?>
                    <a href="#wpuf-ajax-lost-pw-url" id="wpuf-ajax-lost-pw-url"><?php esc_html_e( 'Lost your password?', 'wp-user-frontend' ); ?></a>
                </div>

                <!-- Lost Password form -->
                <div class="wpuf-ajax-reset-password-form">
                    <form id="wpuf_ajax_reset_pass_form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="POST">
                        <div class="wpuf-ajax-message"> <?php echo wp_kses_post( $pwd_reset_header ); ?></div>
                        <p>
                            <label for="wpuf-user_login"><?php esc_html_e( 'Username or E-mail:', 'wp-user-frontend' ); ?></label>
                            <input type="text" name="user_login" id="wpuf-user_login" class="input" value="" size="20" />
                        </p>

                        <?php do_action( 'lostpassword_form' ); ?>

                        <p class="submit">
                            <input type="submit" name="wp-submit" id="wp-submit" value="<?php echo esc_attr(  $pass_reset_label ); ?>" />
                            <input type="hidden" name="redirect_to" value="<?php echo esc_attr( WPUF_Simple_Login::get_posted_value( 'redirect_to' ) ); ?>" />
                            <input type="hidden" name="wpuf_reset_password" value="true" />
                            <input type="hidden" name="action" value="lost_password" />

                            <?php wp_nonce_field( 'wpuf_lost_pass' ); ?>
                        </p>
                    </form>
                    <div id="ajax-lp-section">
                        <a href="#wpuf-ajax-login-url" id="wpuf-ajax-login-url"> <?php esc_html_e( 'Login', 'wp-user-frontend' ); ?> </a>
                        <?php
                        if ( get_option( 'users_can_register' ) ) {
                            echo esc_html( apply_filters( 'login_link_separator', ' | ' ) );
                            $registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register', 'wp-user-frontend' ) );
                            echo wp_kses_post( $registration_url );
                        }
                        ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="wpuf-ajax-logout">
                    <a id="logout-url" href="#logout"><?php echo esc_attr( __( 'Log out', 'wp-user-frontend' ) ); ?></a>
                </div>
            <?php } ?>
        </div>

        <?php
        echo wp_kses_post( $args['after_widget'] );
    }

    /**
     * Ajax Login widget backend
     *
     * @return void
     */
    public function form( $instance ) {
        $title            = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'WPUF Login Widget', 'wp-user-frontend' );
        $log_in_header    = isset( $instance[ 'log_in_header' ] ) ? $instance[ 'log_in_header' ] : __( 'Username or Email Address', 'wp-user-frontend' );
        $pwd_reset_header = isset( $instance[ 'pwd_reset_header' ] ) ? $instance[ 'pwd_reset_header' ] : __( 'Please enter your username or email address. You will receive a link to create a new password via email', 'wp-user-frontend' );
        $uname_label      = isset( $instance[ 'uname_label' ] ) ? $instance[ 'uname_label' ] : __( 'Username', 'wp-user-frontend' );
        $pwd_label        = isset( $instance[ 'pwd_label' ] ) ? $instance[ 'pwd_label' ] : __( 'Password', 'wp-user-frontend' );
        $remember_label   = isset( $instance[ 'remember_label' ] ) ? $instance[ 'remember_label' ] : __( 'Remember Me', 'wp-user-frontend' );
        $log_in_label     = isset( $instance[ 'log_in_label' ] ) ? $instance[ 'log_in_label' ] : __( 'Log In', 'wp-user-frontend' );
        $pass_reset_label = isset( $instance[ 'pass_reset_label' ] ) ? $instance[ 'pass_reset_label' ] : __( 'Reset Password', 'wp-user-frontend' ); ?>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'log_in_header' ) ); ?>"><?php esc_html_e( 'Log-in Text:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'log_in_header' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'log_in_header' ) ); ?>" type="textarea" value="<?php echo esc_attr( $log_in_header ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'uname_label' ) ); ?>"><?php esc_html_e( 'Username Label:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'uname_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'uname_label' ) ); ?>" type="text" value="<?php echo esc_attr( $uname_label ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'pwd_label' ) ); ?>"><?php esc_html_e( 'Password Label:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pwd_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pwd_label' ) ); ?>" type="text" value="<?php echo esc_attr( $pwd_label ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'remember_label' ) ); ?>"><?php esc_html_e( 'Remember Me Label:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'remember_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'remember_label' ) ); ?>" type="text" value="<?php echo esc_attr( $remember_label ); ?>" />
        </p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'log_in_label' ) ); ?>"><?php esc_html_e( 'Log In Label:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'log_in_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'log_in_label' ) ); ?>" type="text" value="<?php echo esc_attr( $log_in_label ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'pwd_reset_header' ) ); ?>"><?php esc_html_e( 'Password Reset Text:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pwd_reset_header' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pwd_reset_header' ) ); ?>" type="textarea" value="<?php echo esc_attr( $pwd_reset_header ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'pass_reset_label' ) ); ?>"><?php esc_html_e( 'Password Reset Label:', 'wp-user-frontend' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'pass_reset_label' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pass_reset_label' ) ); ?>" type="text" value="<?php echo esc_attr( $pass_reset_label ); ?>" />
        </p>
        <?php
    }

    /**
     * Updating widget replacing old instances with new
     *
     * @return $instance
     */
    public function update( $new_instance, $old_instance ) {
        $instance                     = [];
        $instance['title']            = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['log_in_header']    = ( !empty( $new_instance['log_in_header'] ) ) ? strip_tags( $new_instance['log_in_header'] ) : '';
        $instance['pwd_reset_header'] = ( !empty( $new_instance['pwd_reset_header'] ) ) ? strip_tags( $new_instance['pwd_reset_header'] ) : '';
        $instance['uname_label']      = ( !empty( $new_instance['uname_label'] ) ) ? strip_tags( $new_instance['uname_label'] ) : '';
        $instance['pwd_label']        = ( !empty( $new_instance['pwd_label'] ) ) ? strip_tags( $new_instance['pwd_label'] ) : '';
        $instance['remember_label']   = ( !empty( $new_instance['remember_label'] ) ) ? strip_tags( $new_instance['remember_label'] ) : '';
        $instance['log_in_label']     = ( !empty( $new_instance['log_in_label'] ) ) ? strip_tags( $new_instance['log_in_label'] ) : '';
        $instance['pass_reset_label'] = ( !empty( $new_instance['pass_reset_label'] ) ) ? strip_tags( $new_instance['pass_reset_label'] ) : '';

        return $instance;
    }
}

/**
 * Register WPUF_Login_Widget widget
 *
 * @return void
 */
function wpuf_register_ajax_login_widget() {
    register_widget( 'WPUF_Login_Widget' );
}
add_action( 'widgets_init', 'wpuf_register_ajax_login_widget' );

/**
 * Registers widget scripts
 *
 * @return void
 */
function wpuf_register_login_scripts() {
    wp_register_script( 'wpuf_ajax_login', WPUF_ASSET_URI . '/js/wpuf-login-widget.js', [ 'jquery' ], false, true );

    wp_localize_script( 'wpuf_ajax_login', 'wpuf_ajax', [
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'wpuf_register_login_scripts' );
