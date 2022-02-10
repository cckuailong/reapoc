<?php
/**
 * Email header template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width">
	<title><?php echo esc_html( $title ); ?></title>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" style="border-collapse:collapse;border-spacing:0px;vertical-align:top;min-width:100%;box-sizing:border-box;background-color:rgb(233,234,236);color:rgb(68,68,68);font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-weight:normal;padding:0px;margin:0px;text-align:left;font-size:14px;line-height:140%;height:100%;width:100%"><tbody><tr style="padding:0px;vertical-align:top;text-align:left">
		<td align="center" valign="top" style="vertical-align:top;color:rgb(68,68,68);font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-weight:normal;padding:0px;margin:0px;font-size:14px;line-height:140%;text-align:center;border-collapse:collapse">
			<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border-spacing:0px;padding:0px;vertical-align:top;width:600px;margin:0px auto;text-align:inherit">
				<tbody><tr style="padding:0px;vertical-align:top;text-align:left">
					<td align="center" valign="middle" style="vertical-align:top;color:rgb(68,68,68);font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-weight:normal;margin:0px;font-size:14px;line-height:140%;text-align:center;padding:30px 30px 22px;border-collapse:collapse">
						<?php if ( ! empty( $header_image ) ) : ?>
							<img src="<?php echo esc_url( $header_image ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
						<?php endif; ?>
					</td>
				</tr>
				<tr style="padding:0px;vertical-align:top;text-align:left">
					<td align="left" valign="top" style="vertical-align:top;color:rgb(68,68,68);font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-weight:normal;margin:0px;text-align:left;font-size:14px;line-height:140%;background-color:rgb(255,255,255);padding:60px 75px 45px;border-width:3px 1px 1px;border-style:solid;border-color:rgb(227, 71, 22) rgb(221,221,221) rgb(221,221,221);border-collapse:collapse">

						<table style="border-collapse:collapse;border-spacing:0px;padding:0px;vertical-align:top;text-align:left;width:100%"><tbody><tr style="padding:0px;vertical-align:top;text-align:left">
								<td style="vertical-align:top;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-weight:normal;padding:0px;margin:0px;text-align:left;font-size:14px;line-height:140%;color:rgb(119,119,119);border-collapse:collapse">
									<?php echo $message_content; ?>

									<table class="summary-info-table" style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; text-align: left; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin: 38px 0 0 0; Margin: 38px 0 0 0; font-size: 15px; border: 1px solid #dddddd; border-radius: 6px; display: block;">
										<tbody><tr style="padding: 0; vertical-align: top; text-align: left;">
											<td class="summary-info-content" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal; margin: 0; Margin: 0; text-align: left; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; color: #777777; padding: 25px 30px 30px 30px;">
												<table style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; text-align: left; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
													<tbody><tr style="padding: 0; vertical-align: top; text-align: left;">
														<td class="text-center" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; text-align: center; color: #777777;">
															<h6 style="padding: 0; word-wrap: normal; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: bold; mso-line-height-rule: exactly; line-height: 130%; font-size: 18px; color: #444444; text-align: inherit; margin: 0 0 20px 0; Margin: 0 0 20px 0;"><?php echo esc_html( $dyk_message['title'] ); ?></h6>
														</td>
													</tr>
													<tr style="padding: 0; vertical-align: top; text-align: left;">
														<td class="text-center" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; text-align: center; color: #777777;">
															<?php echo esc_html( $dyk_message['content'] ); ?>
														</td>
													</tr>
													</tbody></table>
											</td>
										</tr>
										<tr style="padding: 0; vertical-align: top; text-align: left;">
											<td class="summary-info-content button-container" style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal; margin: 0; Margin: 0; text-align: left; font-size: 14px; mso-line-height-rule: exactly; line-height: 140%; color: #777777; padding: 0 30px 30px 30px;">
												<center style="width: 100%;">
													<table class="button rounded-button" style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; text-align: left; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; width: auto; border-top-left-radius: 3px; border-top-right-radius: 3px; border-bottom-left-radius: 3px; border-bottom-right-radius: 3px; overflow: hidden;margin: auto;Margin: auto;"><tbody><tr style="padding: 0; vertical-align: top; text-align: left;">
															<td style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; text-align: left; font-size: 14px; mso-line-height-rule: exactly; line-height: 100%; color: #777777;">
																<table style="border-collapse: collapse; border-spacing: 0; padding: 0; vertical-align: top; text-align: left; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;"><tbody><tr style="padding: 0; vertical-align: top; text-align: left;">
																		<td style="word-wrap: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; mso-table-lspace: 0pt; mso-table-rspace: 0pt; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: normal; padding: 0; margin: 0; Margin: 0; font-size: 14px; text-align: center; color: #ffffff; background: rgb(227, 71, 22); border: 1px solid #c45e1b; border-bottom: 3px solid #c45e1b; mso-line-height-rule: exactly; line-height: 100%;">
																			<a href="<?php echo esc_url( $dyk_message['more'] ); ?>" rel="noopener noreferrer" target="_blank" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin: 0; Margin: 0; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #ffffff; text-decoration: none; text-align: center; display: inline-block; border: 0 solid #c45e1b; mso-line-height-rule: exactly; line-height: 100%; padding: 17px 30px 15px 30px;">
																				<?php echo esc_html( __( 'Learn More', 'custom-facebook-feeds' ) ); ?>
																			</a>
																		</td>
																	</tr></tbody></table>
															</td>
														</tr></tbody></table>
												</center>
											</td>
										</tr>
										</tbody>
									</table>
								</td>
							</tr></tbody></table>
					</td>
				</tr>
				<tr style="padding:0px;vertical-align:top;text-align:left">
					<td align="center" valign="top" style="vertical-align:top;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-weight:normal;margin:0px;line-height:140%;padding:30px;color:rgb(114,119,124);font-size:12px;text-align:center;border-collapse:collapse">
						<?php
						/* translators: %s - link to a site. */
						esc_html_e( 'This is a courtesy email sent from the Smash Balloon Custom Facebook Feed plugin on your website to alert you when there is an issue with one of your Facebook feeds.', 'custom-facebook-feeds' );
						?>
					</td>
				</tr>
				<tr style="padding:0px;vertical-align:top;text-align:left">
					<td align="center" valign="top" style="vertical-align:top;font-family:&quot;Helvetica Neue&quot;,Helvetica,Arial,sans-serif;font-weight:normal;margin:0px;line-height:140%;padding:30px;color:rgb(114,119,124);font-size:12px;text-align:center;border-collapse:collapse">
						<?php
						printf( esc_html__( 'Sent from %s', 'custom-facebook-feeds' ), '<a href="' . esc_url( home_url() ) . '">' . esc_html( wp_specialchars_decode( get_bloginfo( 'name' ) ) ) . '</a>' );
						?>
						<span>&#8226;</span>
						<?php
						printf( esc_html__( '%sLog in and disable these emails%s', 'custom-facebook-feeds' ), '<a href="' . esc_url( $footer_link ) . '">', '</a>' );
						?>
					</td>
				</tr>
				</tbody>
			</table>
		</td>
	</tr></tbody></table>
</body>
</html>
