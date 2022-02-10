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

$plugin  = $this->plugin['name'] . ' v.' . $this->plugin['version'];
$website = get_option( 'home' );
$license = get_option( 'wow_license_key_' . $this->plugin['prefix'], 'no' );

?>

    <div class="about-wrap wow-box">
        <div class="feature-section one-col">
            <div class="col">

                <p>To get your support related question answered in the fastest timing, please send a message via the
                    form below
                    or write to us on email <a href="mailto:helper@wow-company.com">helper@wow-company.com</a>.</p>

                <p>Also, you can send us your ideas and suggestions for improving the plugin.</p>
				<?php $error = array();
				if ( ! empty( $_POST['action'] ) && ! empty( $_POST['wow_support_field'] ) ) {

					if ( wp_verify_nonce( $_POST['wow_support_field'], 'wow_support_action' )
					     && current_user_can( 'manage_options' )
					) {
						$name    = ! empty( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
						$email   = ! empty( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
						$type    = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
						$subject = ! empty( $_POST['subject'] ) ? sanitize_text_field( $_POST['subject'] ) : '';
						$message = ! empty( $_POST['message'] ) ? wp_kses_post( $_POST['message'] ) : '';
						if ( empty( $name ) ) {
							$error[] = esc_attr__( 'Please, Enter your Name.', 'modal-window' );
						}
						if ( empty( $email ) ) {
							$error[] = esc_attr__( 'Please, Enter your Email.', 'modal-window' );
						}
						if ( empty( $subject ) ) {
							$error[] = esc_attr__( 'Please, Enter Subject of Message.', 'modal-window' );
						}
						if ( empty( $message ) ) {
							$error[] = esc_attr__( 'Please, Enter your Message.', 'modal-window' );
						}

						if ( count( $error ) == 0 ) {
							$headers = array(
								'From: ' . $name . ' <' . $email . '>',
								'content-type: text/html',
							);
							$message = '				
								<html>
                                <head></head>
                                <body>
                                <table>
                                <tr>
                                <td width="30%"><strong>License Key:</strong></td>
                                <td>' . esc_attr( $license ) . '</td>
                                </tr>
                                <tr>
                                <td><strong>Plugin:</strong></td>
                                <td>' . esc_attr( $plugin ) . '</td>
                                </tr>
                                <tr>
                                <td><strong>Website:</strong></td>
                                <td><a href="' . esc_url( $website ) . '">' . esc_url( $website ) . '</a></td>
                                </tr>
                                </table>
                                ' . nl2br( wp_kses_post( $message ) ) . '
                                </body>
                                </html>';
							$subject = $type . ': ' . $subject;
							wp_mail( 'helper@wow-company.com', $subject, $message, $headers );
							echo '<div class="notice notice-success is-dismissible"><p>'
							     . esc_attr__( 'Your Message sent to the Support.', 'modal-window' ) . '</p></div>';
						}
					} else {
						echo '<div class="notice notice-warning is-dismissible"><p>'
						     . esc_attr__( 'Sorry, but message did not send. Please, contact us helper@wow-company.com',
								'modal-window' ) . ' </p></div>';
					}
				}
				?>
				<?php if ( count( $error ) > 0 ) {
					echo '<div class="notice notice-error is-dismissible"><p>' . implode( "<br />", $error )
					     . '</p></div>';
				} ?>


                <form method="post" action="" class="wow-plugin">

                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">From</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <p class="control is-expanded has-icons-left">
                                    <input class="input is-radiusless is-dark" type="text" name="name"
                                           placeholder="Name"
                                           required>
                                    <span class="icon is-small is-left">
										<i class="dashicons dashicons-admin-users"></i>
									</span>
                                </p>
                            </div>
                            <div class="field">
                                <p class="control is-expanded has-icons-left">
                                    <input class="input is-radiusless is-dark" type="email" name="email"
                                           placeholder="Email"
                                           required value="<?php echo get_option( 'admin_email' ); ?>">
                                    <span class="icon is-small is-left">
										<i class="dashicons dashicons-email"></i>
									</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Subject</label>
                        </div>
                        <div class="field-body">
                            <div class="field has-addons">
                                <div class="control">
									<span class="select is-dark">
									<select name="type" class="is-radiusless">
										<option value="Issue"><?php esc_html_e( 'Issue', 'modal-window' ); ?></option>
										<option value="Idea"><?php esc_html_e( 'Idea', 'modal-window' ); ?></option>
									</select>
									</span>
                                </div>
                                <div class="control is-expanded">
                                    <input class="input is-radiusless is-dark" type="text" name="subject"
                                           placeholder="Enter Message Subject" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field is-horizontal">
                        <div class="field-label is-normal">
                            <label class="label">Question</label>
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
		                            <?php
		                            $content   = esc_attr__( 'Enter Your Message', 'modal-window' );
		                            $editor_id = 'editormessage';
		                            $settings  = array(
			                            'textarea_name' => 'message',
		                            );
		                            wp_editor( $content, $editor_id, $settings ); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field is-horizontal">
                        <div class="field-label">
                            <!-- Left empty for spacing -->
                        </div>
                        <div class="field-body">
                            <div class="field">
                                <div class="control">
                                    <input type="submit" name="action" class="button is-info is-radiusless"
                                           value="Send message">
                                </div>
                            </div>
                        </div>
                    </div>

					<?php wp_nonce_field( 'wow_support_action', 'wow_support_field' ); ?>

                </form>

            </div>

        </div>
    </div>
<?php
