<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED Shortcode: mycred_email_subscriptions
 * Returns a given users rank
 * @see http://codex.mycred.me/shortcodes/mycred_email_subscriptions/
 * @since 1.4.6
 * @version 1.1
 */
if ( ! function_exists( 'mycred_render_email_subscriptions' ) ) :
	function mycred_render_email_subscriptions( $atts = array(), $content = '' ) {

		extract( shortcode_atts( array(
			'success' => __( 'Settings Updated', 'mycred' )
		), $atts, MYCRED_SLUG . '_email_subscriptions' ) );

		if ( ! is_user_logged_in() ) return $content;

		$user_id         = get_current_user_id();
		$unsubscriptions = mycred_get_user_meta( $user_id, 'mycred_email_unsubscriptions', '', true );

		if ( $unsubscriptions == '' ) $unsubscriptions = array();

		// Save
		$saved           = false;
		if ( isset( $_REQUEST['do'] ) && $_REQUEST['do'] == 'mycred-unsubscribe' && wp_verify_nonce( $_REQUEST['token'], 'update-mycred-email-subscriptions' ) ) {

			if ( isset( $_POST['mycred_email_unsubscribe'] ) && ! empty( $_POST['mycred_email_unsubscribe'] ) )
				$new_selection = $_POST['mycred_email_unsubscribe'];
			else
				$new_selection = array();

			mycred_update_user_meta( $user_id, 'mycred_email_unsubscriptions', '', $new_selection );
			$unsubscriptions = $new_selection;
			$saved           = true;

		}

		global $wpdb;

		$email_notices   = $wpdb->get_results( $wpdb->prepare( "
			SELECT * 
			FROM {$wpdb->posts} notices

			LEFT JOIN {$wpdb->postmeta} prefs 
				ON ( notices.ID = prefs.post_id AND prefs.meta_key = 'mycred_email_settings' )

			WHERE notices.post_type = 'mycred_email_notice' 
				AND notices.post_status = 'publish'
				AND ( prefs.meta_value LIKE %s OR prefs.meta_value LIKE %s );", '%s:9:"recipient";s:4:"user";%', '%s:9:"recipient";s:4:"both";%' ) );

		ob_start();

		if ( $saved )
			echo '<p class="updated-email-subscriptions">' . $success . '</p>';

			$url             = add_query_arg( array( 'do' => 'mycred-unsubscribe', 'user' => get_current_user_id(), 'token' => wp_create_nonce( 'update-mycred-email-subscriptions' ) ) );

?>
<form action="<?php echo esc_url( $url ); ?>" id="mycred-email-subscriptions" method="post">
	<table class="table">
		<thead>
			<tr>
				<th class="check"><?php _e( 'Unsubscribe', 'mycred' ); ?></th>
				<th class="notice-title"><?php _e( 'Email Notice', 'mycred' ); ?></th>
			</tr>
		</thead>
		<tbody>

		<?php if ( ! empty( $email_notices ) ) : ?>

			<?php foreach ( $email_notices as $notice ) : $settings = mycred_get_email_settings( $notice->ID ); ?>

			<?php if ( $settings['recipient'] == 'admin' ) continue; ?>

			<tr>
				<td class="check"><input type="checkbox" name="mycred_email_unsubscribe[]"<?php if ( in_array( $notice->ID, $unsubscriptions ) ) echo ' checked="checked"'; ?> value="<?php echo $notice->ID; ?>" /></td>
				<td class="notice-title"><?php echo $settings['label']; ?></td>
			</tr>

			<?php endforeach; ?>
		
		<?php else : ?>

			<tr>
				<td colspan="2"><?php _e( 'There are no email notifications yet.', 'mycred' ); ?></td>
			</tr>

		<?php endif; ?>

		</tbody>
	</table>
	<input type="submit" class="btn btn-primary button button-primary pull-right" value="<?php _e( 'Save Changes', 'mycred' ); ?>" />
</form>
<?php

		$content = ob_get_contents();
		ob_end_clean();

		return apply_filters( 'mycred_render_email_subscriptions', $content, $atts );

	}
endif;