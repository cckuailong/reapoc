<?php
/**
 * Support Page
 *
 * @package     Wow_Plugin
 * @subpackage  Admin/Support
 * @author      Dmytro Lobov <i@wpbiker.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin  = $this->plugin_name . ' v.' . $this->plugin_version;
$website = get_option( 'home' );
$license = get_option( 'wow_license_key_' . $this->plugin_pref, 'no' );

?>

	<div class="about-wrap wow-support">
		<div class="feature-section one-col">
			<div class="col">

				<p>To get your support related question answered in the fastest timing, please send a message via the form below
					or write to us on email <a href="mailto:support@wow-company.com">support@wow-company.com</a>.</p>

				<p>Also, you can send us your ideas and suggestions for improving the plugin.</p>
		  <?php $error = array();
		  if ( ! empty( $_POST['action'] ) && ! empty( $_POST['wow_support_field'] ) ) {
			  if ( wp_verify_nonce( $_POST['wow_support_field'], 'wow_support_action' )
			       && current_user_can( 'manage_options' )
			  ) {

				  $fname   = ! empty( $_POST['wow-fname'] ) ? sanitize_text_field( $_POST['wow-fname'] ) : '';
				  $lname   = ! empty( $_POST['wow-lname'] ) ? sanitize_text_field( $_POST['wow-lname'] ) : '';
				  $message = ! empty( $_POST['wow-message'] ) ? sanitize_text_field( $_POST['wow-message'] ) : '';
				  $email   = ! empty( $_POST['wow-email'] ) ? sanitize_email( $_POST['wow-email'] ) : '';
				  $type
				           = ! empty( $_POST['wow-message-type'] ) ? sanitize_text_field( $_POST['wow-message-type'] )
					  : '';

				  if ( empty( $fname ) ) {
					  $error[] = esc_attr__( 'Please, Enter your First Name.', 'wpcoder' );
				  }
				  if ( empty( $lname ) ) {
					  $error[] = esc_attr__( 'Please, Enter your Last Name.', 'wpcoder' );
				  }
				  if ( empty( $message ) ) {
					  $error[] = esc_attr__( 'Please, Enter your Message.', 'wpcoder' );
				  }
				  if ( empty( $email ) ) {
					  $error[] = esc_attr__( 'Please, Enter your Email.', 'wpcoder' );
				  }
				  if ( count( $error ) == 0 ) {


					  $headers = array(
						  'From: ' . $fname . ' ' . $lname . ' <' . $email . '>',
						  'content-type: text/html',
					  );
					  $message = '				
				<html>
				<head></head>
				<body>
				<table>
				<tr>
				<td width="30%"><strong>License Key:</strong></td>
				<td>' . $license . '</td>
				</tr>
				<tr>
				<td><strong>Plugin:</strong></td>
				<td>' . $plugin . '</td>
				</tr>
				<tr>
				<td><strong>Website:</strong></td>
				<td>' . $website . '</td>
				</tr>
				</table>
				' . $message . '					
				</body>
				</html>';
					  wp_mail( 'support@wow-company.com', 'Support Ticket: ' . $type, $message, $headers );
					  echo '<div class="wow-alert wow-alert-update "><p class="wow_error">'
					       . esc_attr__( 'Your Message sent to the Support.', 'wpcoder' ) . '</p></div>';

				  }


			  } else {
				  echo '<div class="wow-alert wow-alert-error "><p class="wow_error">'
				       . esc_attr__( 'Sorry, but message did not send. Please, contact us support@wow-company.com',
						  'wpcoder' ) . ' </p></div>';
			  }
		  }
		  ?>
		  <?php if ( count( $error ) > 0 ) {
			  echo '<div class="wow-alert wow-alert-error "><p class="wow_error">' . implode( "<br />", $error )
			       . '</p></div>';
		  } ?>


				<form method="post" action="" class="wow-plugin">
					<div class="wow-container">
						<div class="wow-element">
							<label><?php esc_html_e( 'First Name', 'wpcoder' ); ?></label><br/>
							<input type="text" name="wow-fname" value=""
										 placeholder="<?php esc_html_e( 'Enter Your First Name', 'wpcoder' ); ?>">
						</div>
						<div class="wow-element">
							<label><?php esc_html_e( 'Last Name', 'wpcoder' ); ?></label><br/>
							<input type="text" name="wow-lname" value=""
										 placeholder="<?php esc_html_e( 'Enter Your Last Name', 'wpcoder' ); ?>">
						</div>
					</div>
					<div class="wow-container">
						<div class="wow-element">
							<label><?php esc_html_e( 'WebSite', 'wpcoder' ); ?></label><br/>
							<input type="text" disabled name="wow-website" value="<?php echo get_option( 'home' ); ?>">
						</div>
						<div class="wow-element">
							<label><?php esc_html_e( 'Contact email', 'wpcoder' ); ?></label><br/>
							<input type="text" name="wow-email" value="<?php echo get_option( 'admin_email' ); ?>">
						</div>

					</div>

					<div class="wow-container">
						<div class="wow-element">
							<label><?php esc_html_e( 'License Key', 'wpcoder' ); ?></label><br/>
							<input type="text" disabled name="wow-license-key" value="<?php if ( ! empty( $license ) ) {
				  echo $license;
			  }; ?>">
						</div>
						<div class="wow-element">
							<label><?php esc_html_e( 'Plugin', 'wpcoder' ); ?></label><br/>
							<input type="text" disabled name="wow-plugin" value="<?php if ( ! empty( $name ) ) {
				  echo $plugin;
			  }; ?>">
						</div>
					</div>
					<div class="wow-container">
						<div class="wow-element">
							<label><?php esc_html_e( 'Message type', 'wpcoder' ); ?></label><br/>
							<select name="wow-message-type">
								<option value="Issue"><?php esc_html_e( 'Issue', 'wpcoder' ); ?></option>
								<option value="Idea"><?php esc_html_e( 'Idea', 'wpcoder' ); ?></option>
							</select>
						</div>
					</div>
					<div class="wow-container">
						<div class="wow-element">
          <textarea name="wow-message" rows="10"
										placeholder="<?php esc_html_e( 'Enter Your Message', 'wpcoder' ); ?>"></textarea>
						</div>
					</div>
					<div class="wow-container">
						<div class="wow-element">
							<input type="submit" class="add-item" name="action"
										 value="<?php esc_html_e( 'Send to Support', 'wpcoder' ); ?>">
						</div>
					</div>
			<?php wp_nonce_field( 'wow_support_action', 'wow_support_field' ); ?>
				</form>
			</div>

		</div>
	</div>
<?php
