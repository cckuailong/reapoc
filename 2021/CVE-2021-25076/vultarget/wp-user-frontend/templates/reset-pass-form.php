<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
WPUF will always look in your theme's directory first, before using this default template.
*/
?>
<div class="login" id="wpuf-login-form">

    <?php WPUF_Simple_Login::init()->show_errors(); ?>
    <?php WPUF_Simple_Login::init()->show_messages(); ?>

    <form name="resetpasswordform" id="resetpasswordform" action="" method="post">
        <p>
            <label for="wpuf-pass1"><?php esc_html_e( 'New password', 'wp-user-frontend' ); ?></label>
            <input autocomplete="off" name="pass1" id="wpuf-pass1" class="input" size="20" value="" type="password" autocomplete="off" />
        </p>

        <p>
            <label for="wpuf-pass2"><?php esc_html_e( 'Confirm new password', 'wp-user-frontend' ); ?></label>
            <input autocomplete="off" name="pass2" id="wpuf-pass2" class="input" size="20" value="" type="password" autocomplete="off" />
        </p>

        <?php do_action( 'resetpassword_form' ); ?>

        <p class="submit">
            <input type="submit" name="wp-submit" id="wp-submit" value="<?php esc_attr_e( 'Reset Password', 'wp-user-frontend' ); ?>" />
            <input type="hidden" name="key" value="<?php echo esc_attr( WPUF_Simple_Login::get_posted_value( 'key' ) ); ?>" />
            <input type="hidden" name="login" id="user_login" value="<?php echo esc_attr( WPUF_Simple_Login::get_posted_value( 'login' ) ); ?>" />
            <input type="hidden" name="wpuf_reset_password" value="true" />
        </p>

        <?php wp_nonce_field( 'wpuf_reset_pass' ); ?>
    </form>
</div>
