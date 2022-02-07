<?php
/**
 * Support Page
 *
 * @package     Wow_Plugin
 * @subpackage  Admin/Support
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin  = $this->plugin['name'] . ' v.' . $this->plugin['version'];
$website = get_option( 'home' );
$license = get_option( 'wow_license_key_' . $this->plugin['version'], 'no' );

?>

	<div class="about-wrap wow-support">
		<div class="feature-section one-col">
			<div class="col">

				<p>To get your support related question answered in the fastest timing, please send a message via the form below
					or write to us on email <a href="mailto:support@wow-company.com">support@wow-company.com</a>.</p>

				<p>Also, you can send us your ideas and suggestions for improving the plugin.</p>
		  <?php $error = array();
		  if ( ! empty( $_POST['action'] ) && ! empty( $_POST['wow_support_field'] ) ) {
			  if ( wp_verify_nonce( $_POST['wow_support_field'], 'wow_support_action' ) &&
			       current_user_can( 'manage_options' ) ) {

				  $fname   = ! empty( $_POST['wow-fname'] ) ? sanitize_text_field( $_POST['wow-fname'] ) : '';
				  $lname   = ! empty( $_POST['wow-lname'] ) ? sanitize_text_field( $_POST['wow-lname'] ) : '';
				  $message = ! empty( $_POST['wow-message'] ) ? sanitize_text_field( $_POST['wow-message'] ) : '';
				  $email   = ! empty( $_POST['wow-email'] ) ? sanitize_email( $_POST['wow-email'] ) : '';
				  $type    =
					  ! empty( $_POST['wow-message-type'] ) ? sanitize_text_field( $_POST['wow-message-type'] ) : '';

				  if ( empty( $fname ) ) {
					  $error[] = esc_attr__( 'Please, Enter your First Name.', $this->plugin['text'] );
				  }
				  if ( empty( $lname ) ) {
					  $error[] = esc_attr__( 'Please, Enter your Last Name.', $this->plugin['text'] );
				  }
				  if ( empty( $message ) ) {
					  $error[] = esc_attr__( 'Please, Enter your Message.', $this->plugin['text'] );
				  }
				  if ( empty( $email ) ) {
					  $error[] = esc_attr__( 'Please, Enter your Email.', $this->plugin['text'] );
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
					  echo '<div class="wow-alert wow-alert-update "><p class="wow_error">' .
					       esc_attr__( 'Your Message sent to the Support.', $this->plugin['text'] ) . '</p></div>';

				  }


			  } else {
				  echo '<div class="wow-alert wow-alert-error "><p class="wow_error">' .
				       esc_attr__( 'Sorry, but message did not send. Please, contact us support@wow-company.com',
					       $this->plugin['text'] ) . ' </p></div>';
			  }
		  }
		  ?>
		  <?php if ( count( $error ) > 0 ) {
			  echo '<div class="wow-alert wow-alert-error "><p class="wow_error">' . implode( "<br />", $error ) .
			       '</p></div>';
		  } ?>


				<form method="post" action="" class="wow-plugin">
					<div class="container">
						<div class="element">
							<label><?php esc_html_e( 'First Name', $this->plugin['text'] ); ?></label><br/>
							<input type="text" name="wow-fname" value=""
										 placeholder="<?php esc_html_e( 'Enter Your First Name', $this->plugin['text'] ); ?>">
						</div>
						<div class="element">
							<label><?php esc_html_e( 'Last Name', $this->plugin['text'] ); ?></label><br/>
							<input type="text" name="wow-lname" value=""
										 placeholder="<?php esc_html_e( 'Enter Your Last Name', $this->plugin['text'] ); ?>">
						</div>
					</div>
					<div class="container">
						<div class="element">
							<label><?php esc_html_e( 'WebSite', $this->plugin['text'] ); ?></label><br/>
							<input type="text" disabled name="wow-website" value="<?php echo get_option( 'home' ); ?>">
						</div>
						<div class="element">
							<label><?php esc_html_e( 'Contact email', $this->plugin['text'] ); ?></label><br/>
							<input type="text" name="wow-email" value="<?php echo get_option( 'admin_email' ); ?>">
						</div>

					</div>

					<div class="container">
						<div class="element">
							<label><?php esc_html_e( 'License Key', $this->plugin['text'] ); ?></label><br/>
							<input type="text" disabled name="wow-license-key" value="<?php if ( ! empty( $license ) ) {
				  echo $license;
			  }; ?>">
						</div>
						<div class="element">
							<label><?php esc_html_e( 'Plugin', $this->plugin['text'] ); ?></label><br/>
							<input type="text" disabled name="wow-plugin" value="<?php if ( ! empty( $name ) ) {
				  echo $plugin;
			  }; ?>">
						</div>
					</div>
					<div class="container">
						<div class="element">
							<label><?php esc_html_e( 'Message type', $this->plugin['text'] ); ?></label><br/>
							<select name="wow-message-type">
								<option value="Issue"><?php esc_html_e( 'Issue', $this->plugin['text'] ); ?></option>
								<option value="Idea"><?php esc_html_e( 'Idea', $this->plugin['text'] ); ?></option>
							</select>
						</div>
					</div>
					<div class="container">
						<div class="element">
          <textarea name="wow-message" rows="10"
										placeholder="<?php esc_html_e( 'Enter Your Message', $this->plugin['text'] ); ?>"></textarea>
						</div>
					</div>
					<div class="container">
						<div class="element">
							<input type="submit" class="add-item" name="action"
										 value="<?php esc_html_e( 'Send to Support', $this->plugin['text'] ); ?>">
						</div>
					</div>
			<?php wp_nonce_field( 'wow_support_action', 'wow_support_field' ); ?>
				</form>
			</div>

		</div>
	</div>
<?php
