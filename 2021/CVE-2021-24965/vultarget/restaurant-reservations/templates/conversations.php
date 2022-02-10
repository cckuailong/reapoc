<?php
/**
 * Email Template: Conversations
 *
 * A clean, simple email template for talking to directly to your customer.
 *
 * @since 0.1
 */
global $rtb_controller;
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo esc_html( $this->get( 'subject' ) ); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<style type="text/css">
	/* FONTS */
    @media screen {
		@font-face {
		  font-family: 'Lato';
		  font-style: normal;
		  font-weight: 400;
		  src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v11/qIIYRU-oROkIk8vfvxw6QvesZW2xOQ-xsNqO47m55DA.woff) format('woff');
		}

		@font-face {
		  font-family: 'Lato';
		  font-style: normal;
		  font-weight: 700;
		  src: local('Lato Bold'), local('Lato-Bold'), url(https://fonts.gstatic.com/s/lato/v11/qdgUG4U09HnJwhYI-uK18wLUuEpTyoUstqEm5AMlJo4.woff) format('woff');
		}

		@font-face {
		  font-family: 'Lato';
		  font-style: italic;
		  font-weight: 400;
		  src: local('Lato Italic'), local('Lato-Italic'), url(https://fonts.gstatic.com/s/lato/v11/RYyZNoeFgb0l7W3Vu1aSWOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
		}

		@font-face {
		  font-family: 'Lato';
		  font-style: italic;
		  font-weight: 700;
		  src: local('Lato Bold Italic'), local('Lato-BoldItalic'), url(https://fonts.gstatic.com/s/lato/v11/HkF_qI1x_noxlxhrhMQYELO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
		}
    }

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
	@media screen and (max-width:600px){
		h1 {
			font-size: 32px !important;
			line-height: 32px !important;
		}
	}

    /* ANDROID CENTER FIX */
    div[style*="margin: 16px 0;"] { margin: 0 !important; }
</style>
</head>
<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">

<!-- HIDDEN PREHEADER TEXT -->
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: 'Lato', Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
    <?php echo esc_html( $this->get( 'lead' ) ); ?>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <!-- LOGO -->
    <tr>
        <td bgcolor="<?php esc_attr_e( $this->get( 'color_primary' ) ); ?>" align="center">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
            <tr>
            <td align="center" valign="top" width="600">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >
                <tr>
                    <td align="center" valign="top" style="padding: 40px 10px 40px 10px;">
                        <a href="<?php echo esc_url( home_url() ); ?>" target="_blank" style="display: block; font-family: 'Lato', Helvetica, Arial, sans-serif; color: <?php esc_attr_e( $this->get( 'color_primary_text' ) ); ?>; font-size: 24px; text-decoration: none;">
							<?php if ( $this->get( 'logo' ) ) : ?>
								<img alt="<?php esc_attr_e( get_bloginfo( 'name' ) ); ?>" src="<?php echo esc_url( $this->get( 'logo' ) ); ?>" width="<?php echo absint( $this->get( 'logo_width' ) ); ?>" height="<?php echo absint( $this->get( 'logo_height' ) ); ?>" style="display: block; width: <?php echo absint( $this->get( 'logo_width' ) ); ?>px; max-width: <?php echo absint( $this->get( 'logo_width' ) ); ?>px; min-width: <?php echo absint( $this->get( 'logo_width' ) ); ?>px; font-family: 'Lato', Helvetica, Arial, sans-serif; color: <?php esc_attr_e( $this->get( 'color_primary_text' ) ); ?>; font-size: 18px;" border="0">
							<?php else : ?>
								<?php esc_attr_e( get_bloginfo( 'name' ) ); ?>
							<?php endif; ?>
                        </a>
                    </td>
                </tr>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
	    <!-- HERO -->
	    <tr>
	        <td bgcolor="<?php esc_attr_e( $this->get( 'color_primary' ) ); ?>" align="center" style="padding: 0px 10px 0px 10px;">
	            <!--[if (gte mso 9)|(IE)]>
	            <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
	            <tr>
	            <td align="center" valign="top" width="600">
	            <![endif]-->
	            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >
	                <tr>
	                    <td bgcolor="#ffffff" align="center" valign="top" style="padding: 40px 20px 20px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 36px; font-weight: 400; line-height: 48px;" mc:edit="main heading">
							<?php if ( $this->get( 'lead' ) ) : ?>
								<h1 style="font-size: 36px; font-weight: 400; margin: 0;">
									<?php echo esc_html( $this->get( 'lead' ) ); ?>
								</h1>
							<?php endif; ?>
	                    </td>
	                </tr>
	            </table>
	            <!--[if (gte mso 9)|(IE)]>
	            </td>
	            </tr>
	            </table>
	            <![endif]-->
	        </td>
	    </tr>
    <!-- COPY BLOCK -->
    <tr>
        <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
            <tr>
            <td align="center" valign="top" width="600">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >
              <!-- COPY -->
              <tr>
                <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
					<?php echo $this->get( 'content' ); ?>
                </td>
              </tr>
              <!-- BULLETPROOF BUTTON -->
			  <?php if ( $this->get( 'book_again' ) ) :?>
              <tr>
                <td bgcolor="#ffffff" align="left">
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td bgcolor="#ffffff" align="center" style="padding: 20px 30px 60px 30px;">
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
                              <td align="center" style="border-radius: 3px;" bgcolor="<?php esc_attr_e( $this->get( 'color_button' ) ); ?>"><a href="<?php echo esc_url( $booking_url ); ?>" target="_blank" style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; color: <?php esc_attr_e( $this->get( 'color_button_text' ) ); ?>; text-decoration: none; text-decoration: none; padding: 15px 25px; border-radius: 2px; border: 1px solid <?php esc_attr_e( $this->get( 'color_button' ) ); ?>; display: inline-block;"><?php echo esc_html( $this->get( 'book_again' ) ); ?></a></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <?php endif; ?>
            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <!-- FOOTER CALLOUT -->
    <?php if ( $this->get( 'footer_message' ) || $this->get( 'show_contact' ) ) : ?>
    <tr>
        <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
            <tr>
            <td align="center" valign="top" width="600">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px;" >

				<?php if ( $this->get( 'footer_message' ) ) : ?>
                <tr>
                  <td bgcolor="#333333" align="center" style="padding: 30px 30px 30px 30px; color: #ffffff; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;<?php if ( !$this->get( 'show_contact' ) ) : ?> border-radius: 0px 0px 4px 4px;<?php endif; ?>">
                    <p style="margin: 0;"><?php echo esc_html( $this->get( 'footer_message' ) ); ?></p>
                  </td>
                </tr>
				<?php endif; ?>

				<?php if ( $this->get( 'show_contact' ) ) : ?>
				<tr>
				  <td bgcolor="#333333" align="center" style="padding: 30px 30px 10px 30px; color: #ffffff; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
					<p style="margin: 0;"><?php echo nl2br( esc_html( $this->get( 'address' )['text'] ) ); ?></p>
				  </td>
				</tr>
				<tr>
				  <td bgcolor="#333333" align="center" style="padding: 0px 30px 30px 30px; border-radius: 0px 0px 4px 4px; color: #ffffff; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 25px;">
					<p style="margin: 0;"><?php echo esc_html( $this->get( 'phone' ) ); ?></p>
				  </td>
				</tr>
				<?php endif; ?>

            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
	<?php endif; ?>
	<!-- FOOTER -->
	<tr>
         <td bgcolor="#f4f4f4" align="center" style="padding: 0px 10px 0px 10px;">
             <table border="0" cellpadding="0" cellspacing="0" width="600" class="wrapper" >
               <!-- NAVIGATION -->
               <tr>
                 <td bgcolor="#f4f4f4" align="left" style="padding: 30px 30px 30px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 400; line-height: 18px;" >
                   <p style="margin: 0;"><?php echo esc_html( $this->get( 'acknowledgement' ) ); ?></p>
                 </td>
               </tr>
             </table>
         </td>
     </tr>
</table>

</body>
</html>
