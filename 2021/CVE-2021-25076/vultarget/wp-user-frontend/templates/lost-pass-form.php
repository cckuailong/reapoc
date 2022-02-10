<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
WPUF will always look in your theme's directory first, before using this default template.
*/
?>
<div class="login" id="wpuf-login-form">

	<?php WPUF_Simple_Login::init()->show_errors(); ?>
	<?php WPUF_Simple_Login::init()->show_messages(); ?>

	<form name="lostpasswordform" id="lostpasswordform" action="" method="post">
		<p>
			<label for="wpuf-user_login"><?php esc_html_e( 'Username or E-mail:', 'wp-user-frontend' ); ?></label>
			<input type="text" name="user_login" id="wpuf-user_login" class="input" value="" size="20" />
		</p>

		<?php do_action( 'lostpassword_form' ); ?>

		<p class="submit">
			<input type="submit" name="wp-submit" id="wp-submit" value="<?php esc_attr_e( 'Get New Password', 'wp-user-frontend' ); ?>" />
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( WPUF_Simple_Login::get_posted_value( 'redirect_to' ) ); ?>" />
			<input type="hidden" name="wpuf_reset_password" value="true" />
			<input type="hidden" name="action" value="lostpassword" />

			<?php wp_nonce_field( 'wpuf_lost_pass' ); ?>
		</p>
	</form>

	<?php echo wp_kses_post( WPUF_Simple_Login::init()->get_action_links( [ 'lostpassword' => false ] ) ); ?>
</div>
