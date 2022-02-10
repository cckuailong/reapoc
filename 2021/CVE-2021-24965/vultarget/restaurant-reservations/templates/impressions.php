<?php
/**
 * Email Template: Impressions
 *
 * A small email template that's great for making a quick impression.
 *
 * @since 0.1
 */
global $rtb_controller;
?><!doctype html>
<html>
<head>
<title><?php echo esc_html( $this->get( 'subject' ) ); ?></title>
<style type="text/css">
/* CLIENT-SPECIFIC STYLES */
body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
img { -ms-interpolation-mode: bicubic; }

/* RESET STYLES */
img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
table { border-collapse: collapse !important; }
body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }

/* iOS BLUE LINKS */
a[x-apple-data-detectors] {
    color: inherit !important;
    text-decoration: none !important;
    font-size: inherit !important;
    font-family: inherit !important;
    font-weight: inherit !important;
    line-height: inherit !important;
}

/* MOBILE STYLES */
@media screen and (max-width: 500px) {
  .img-max {
    width: 100% !important;
    max-width: 100% !important;
    height: auto !important;
  }

  .max-width {
    max-width: 100% !important;
  }

  .mobile-wrapper {
    width: 85% !important;
    max-width: 85% !important;
  }

  .mobile-padding {
    padding-left: 5% !important;
    padding-right: 5% !important;
  }
}

/* ANDROID CENTER FIX */
div[style*="margin: 16px 0;"] { margin: 0 !important; }
</style>
</head>
<body style="margin: 0 !important; padding: 0; !important background-color: #ffffff;" bgcolor="#ffffff">

<!-- HIDDEN PREHEADER TEXT -->
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Open Sans, Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    <?php echo esc_html( $this->get( 'lead' ) ); ?>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" valign="top" width="100%" bgcolor="<?php esc_attr_e( $this->get( 'color_primary' ) ); ?>" style="background: <?php esc_attr_e( $this->get( 'color_primary' ) ); ?>; padding: 50px 15px;" class="mobile-padding">
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="500" class="mobile-wrapper">
                <tr>
                    <td align="center" valign="top" style="padding: 0 0 20px 0;">
                        <a href="<?php echo esc_url( home_url() ); ?>" target="_blank" style="display: block; font-family: 'Lato', Helvetica, Arial, sans-serif; color: <?php esc_attr_e( $this->get( 'color_primary_text' ) ); ?>; font-size: 24px; text-decoration: none;">
							<?php if ( $this->get( 'logo' ) ) : ?>
								<img alt="<?php esc_attr_e( get_bloginfo( 'name' ) ); ?>" src="<?php echo esc_url( $this->get( 'logo' ) ); ?>" width="<?php echo absint( $this->get( 'logo_width' ) ); ?>" height="<?php echo absint( $this->get( 'logo_height' ) ); ?>" style="display: block; width: <?php echo absint( $this->get( 'logo_width' ) ); ?>px; max-width: <?php echo absint( $this->get( 'logo_width' ) ); ?>px; min-width: <?php echo absint( $this->get( 'logo_width' ) ); ?>px; color: <?php esc_attr_e( $this->get( 'color_primary_text' ) ); ?>; font-size: 18px;" border="0">
							<?php else : ?>
								<?php esc_attr_e( get_bloginfo( 'name' ) ); ?>
							<?php endif; ?>
                        </a>
                    </td>
                </tr>
				<?php if ( $this->get( 'lead' ) ) : ?>
                <tr>
                    <td align="center" valign="top" style="padding: 0; font-family: Open Sans, Helvetica, Arial, sans-serif;">
                        <h1 style="font-size: 40px; color: <?php esc_attr_e( $this->get( 'color_primary_text' ) ); ?>;"><?php echo esc_html( $this->get( 'lead' ) ); ?></h1>
                    </td>
                </tr>
				<?php endif; ?>
                <tr>
                    <td align="center" valign="top" style="font-family: Open Sans, Helvetica, Arial, sans-serif; padding-top: 0; color: <?php esc_attr_e( $this->get( 'color_primary_text' ) ); ?>; font-size: 16px; line-height: 24px; margin: 0;">
 						<?php echo $this->get( 'content' ); ?>
                    </td>
                </tr>
				<?php if ( $this->get( 'book_again' ) ) :?>
                <tr>
                    <td align="center" style="padding: 25px 0 0 0;">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
	  							<?php
	  								$booking_page = $rtb_controller->settings->get_setting( 'booking-page' );
	  								if ( !empty( $booking_page ) ) {
	  									$booking_url = get_permalink( $booking_page );
	  								} else {
	  									$booking_url = home_url();
	  								}
	  							?>
                                <td align="center" style="border-radius: 28px;" bgcolor="<?php esc_attr_e( $this->get( 'color_button' ) ); ?>">
                                    <a href="<?php echo esc_url( $booking_url ); ?>" target="_blank" style="font-size: 18px; font-family: Open Sans, Helvetica, Arial, sans-serif; color: <?php esc_attr_e( $this->get( 'color_button_text' ) ); ?>; text-decoration: none; border-radius: 28px; background-color: <?php esc_attr_e( $this->get( 'color_button' ) ); ?>; padding: 16px 28px; border: 1px solid <?php esc_attr_e( $this->get( 'color_button' ) ); ?>; display: block;"><?php echo esc_html( $this->get( 'book_again' ) ); ?></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
				<?php endif; ?>
            </table>
        </td>
    </tr>
	<?php if ( $this->get( 'footer_message' ) || $this->get( 'show_contact' ) ) : ?>
		<?php if ( $this->get( 'footer_message' ) ) : ?>
		    <tr>
		        <td align="center" height="100%" valign="top" width="100%" bgcolor="#f6f6f6" style="padding: 20px 15px;">
		            <table align="center" border="0" cellpadding="0" cellspacing="0" width="500"  class="mobile-wrapper">
		                <tr>
		                    <td align="center" valign="top" style="padding: 0; font-family: Open Sans, Helvetica, Arial, sans-serif; color: #999999; font-size: 14px; line-height: 20px;">
								<p style="margin: 0;"><?php echo esc_html( $this->get( 'footer_message' ) ); ?></p>
		                    </td>
		                </tr>
		            </table>
		        </td>
			</tr>
		<?php endif; ?>
		<?php if ( $this->get( 'show_contact' ) ) : ?>
			<tr>
		        <td align="center" height="100%" valign="top" width="100%" bgcolor="#f6f6f6" style="padding: 20px 15px 5px 15px;">
		            <table align="center" border="0" cellpadding="0" cellspacing="0" width="500"  class="mobile-wrapper">
		                <tr>
		                    <td align="center" valign="top" style="padding: 0; font-family: Open Sans, Helvetica, Arial, sans-serif; color: #999999; font-size: 14px; line-height: 20px;">
		  						<p style="margin: 0;"><?php echo nl2br( esc_html( $this->get( 'address' )['text'] ) ); ?></p>
		                    </td>
		                </tr>
		            </table>
		        </td>
			</tr>
			<tr>
		        <td align="center" height="100%" valign="top" width="100%" bgcolor="#f6f6f6" style="padding: 5px 15px 20px 15px;">
		            <table align="center" border="0" cellpadding="0" cellspacing="0" width="500"  class="mobile-wrapper">
		                <tr>
		                    <td align="center" valign="top" style="padding: 0; font-family: Open Sans, Helvetica, Arial, sans-serif; color: #999999; font-size: 14px; line-height: 20px;">
		  						<p style="margin: 0;"><?php echo esc_html( $this->get( 'phone' ) ); ?></p>
		                    </td>
		                </tr>
		            </table>
		        </td>
			</tr>
		<?php endif; ?>
	<?php endif; ?>
	<tr>
		<td align="center" height="100%" valign="top" width="100%" bgcolor="#ffffff" style="padding: 40px 15px;">
			<table align="center" border="0" cellpadding="0" cellspacing="0" width="500"  class="mobile-wrapper">
				<tr>
					<td align="center" valign="top" style="padding: 0; font-family: Open Sans, Helvetica, Arial, sans-serif; color: #999999; font-size: 12px; line-height: 20px;">
						<p style="margin: 0;"><?php echo esc_html( $this->get( 'acknowledgement' ) ); ?></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

</body>
</html>
