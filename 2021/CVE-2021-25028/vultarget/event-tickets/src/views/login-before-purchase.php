<?php
/**
 * Renders a link displayed to customers when they can first login
 * before purchasing tickets.
 *
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/login-before-purchase.php
 *
 * @version 4.7
 */

$login_url          = Tribe__Tickets__Tickets::get_login_url();
$users_can_register = (bool) get_option( 'users_can_register' );
$login_message      = null;
$register_message   = null;
$registration_url   = null;

if ( ! is_user_logged_in() ) {
	if ( ! $users_can_register ) {
		$login_message = _x( 'Log in before purchasing', 'Login link on Tribe Commerce checkout page', 'event-tickets' );
	} else {
		$login_message    = _x( 'Log in', 'Login link on Tribe Commerce checkout page, shown as an alternative to the registration link', 'event-tickets' );
		$register_message = _x( 'create an account', 'Registration link on Tribe Commerce checkout page, shown as an alternative the login link', 'event-tickets' );
		$registration_url = wp_registration_url();
	}
}
?>

<?php if ( ! is_user_logged_in() ) : ?>
	<a href="<?php echo esc_attr( $login_url ); ?>"><?php echo esc_html( $login_message ); ?></a>

	<?php if ( $users_can_register ) : ?>
		or <a href="<?php echo esc_attr( $registration_url ); ?>"><?php echo esc_html( $register_message ); ?></a> before purchasing
	<?php endif; ?>
<?php endif;
