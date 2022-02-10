<?php
/*
  If you would like to edit this file, copy it to your current theme's directory and edit it there.
  WPUF will always look in your theme's directory first, before using this default template.
 */
?>
<div class="login" id="wpuf-login-form">

    <?php

    $message = apply_filters( 'login_message', '' );

    if ( !empty( $message ) ) {
        echo $message . "\n";
    }
    ?>

    <?php wpuf()->login->show_errors(); ?>
    <?php wpuf()->login->show_messages(); ?>

    <form name="loginform" class="wpuf-login-form" id="loginform" action="<?php echo esc_attr( $action_url ); ?>" method="post">
        <p>
            <label for="wpuf-user_login"><?php esc_html_e( 'Username or Email', 'wp-user-frontend' ); ?></label>
            <input type="text" name="log" id="wpuf-user_login" class="input" value="" size="20" />
        </p>
        <p>
            <label for="wpuf-user_pass"><?php esc_html_e( 'Password', 'wp-user-frontend' ); ?></label>
            <input type="password" name="pwd" id="wpuf-user_pass" class="input" value="" size="20" />
        </p>

        <?php $recaptcha = wpuf_get_option( 'login_form_recaptcha', 'wpuf_profile', 'off' ); ?>
        <?php if ( $recaptcha == 'on' ) { ?>
            <p>
                <div class="wpuf-fields">
                    <?php echo wp_kses( recaptcha_get_html( wpuf_get_option( 'recaptcha_public', 'wpuf_general' ), true, null, is_ssl() ),[
                        'div' => [
                            'class' => [],
                            'data-sitekey' => [],
                        ],

                        'script' => [
                            'src' => []
                        ],

                        'noscript' => [],

                        'iframe' => [
                            'src' => [],
                            'height' => [],
                            'width' => [],
                            'frameborder' => [],
                        ],
                        'br' => [],
                        'textarea' => [
                            'name' => [],
                            'rows' => [],
                            'cols' => [],
                        ],
                        'input' => [
                            'type'   => [],
                            'value' => [],
                            'name'   => [],
                        ]
                    ] ); ?>
                </div>
            </p>
        <?php } ?>

        <p class="forgetmenot">
            <input name="rememberme" type="checkbox" id="wpuf-rememberme" value="forever" />
            <label for="wpuf-rememberme"><?php esc_html_e( 'Remember Me', 'wp-user-frontend' ); ?></label>
        </p>

        <p class="submit">
            <input type="submit" name="wp-submit" id="wp-submit" value="<?php esc_html_e( 'Log In', 'wp-user-frontend' ); ?>" />
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
            <input type="hidden" name="wpuf_login" value="true" />
            <input type="hidden" name="action" value="login" />
            <?php wp_nonce_field( 'wpuf_login_action','wpuf-login-nonce' ); ?>
        </p>
        <p>
            <?php do_action( 'wpuf_login_form_bottom' ); ?>
        </p>
    </form>

    <?php echo wp_kses_post( wpuf()->login->get_action_links( [ 'login' => false ] ) ); ?>
</div>
