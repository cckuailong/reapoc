<?php
/**
 * Email Template: Statement
 *
 * A plain template for delivering a direct message.
 *
 * @since 0.1
 */
global $rtb_controller;
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noindex, nofollow">
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>

    <title><?php echo esc_html( $this->get( 'subject' ) ); ?></title>

    <style type="text/css">
      @import url(https://fonts.googleapis.com/css?family=Montserrat);
      body, table, td, a{-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;}
      table, td{mso-table-lspace: 0pt; mso-table-rspace: 0pt;}
      img{border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic;}
      table{border-collapse: collapse !important;}
      body{font-family: 'Montserrat', Arial, sans-serif; height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important;}
      div[style*="margin: 16px 0;"] { margin:0 !important; }
      a[x-apple-data-detectors] {
      color: inherit !important;
      text-decoration: none !important;
      font-size: inherit !important;
      font-family: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
      }
      @media only screen and (max-width: 639px){
      *[class].wrapper { width:100% !important; }
      *[class].container { width:100% !important; }
      *[class].mobile { width:100% !important; display:block !important; }
      *[class].image{ width:100% !important; height:auto; }
      *[class].center{ margin:0 auto !important; text-align:center !important; }
      *[class="mobileOff"] { width: 0px !important; display: none !important; }
      *[class*="mobileOn"] { display: block !important; max-height:none !important; }
      }
    </style>

    <!--[if mso]>
    <style type="text/css">
      .body-text {
      font-family: Arial, sans-serif !important;
      }
    </style>
    <![endif]-->

    <!--[if gte mso 9]>
    <xml>
      <o:OfficeDocumentSettings>
        <o:AllowPNG/>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->

  </head>
  <body style="margin:0; padding:0; background-color:<?php esc_attr_e( $this->get( 'color_primary' ) ); ?>;">
    <table width="100%" height="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="<?php esc_attr_e( $this->get( 'color_primary' ) ); ?>">
      <tr>
        <td width="100%" valign="top" align="center">

          <div style="display: none; font-size: 1px; line-height: 1px; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
            <?php echo esc_html( $this->get( 'lead' ) ); ?>
          </div>

          <!-- START HEADER -->
          <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" class="wrapper" style="border-bottom:1px solid #dddddd;" bgcolor="#FFFFFF">
            <tr>
              <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
            </tr>
            <tr>
              <td align="left" style="padding:0 25px;">
				  <a href="<?php echo esc_url( home_url() ); ?>" target="_blank" style="font-family: 'Montserrat', Arial, sans-serif; font-size:20px; line-height:26px; color:#222222; font-weight:bold; text-decoration:none;">
					  <?php if ( $this->get( 'logo' ) ) : ?>
						  <img alt="<?php esc_attr_e( get_bloginfo( 'name' ) ); ?>" src="<?php echo esc_url( $this->get( 'logo' ) ); ?>" width="<?php echo absint( $this->get( 'logo_width' ) ); ?>" height="<?php echo absint( $this->get( 'logo_height' ) ); ?>" style="margin:0; padding:0; border:none; display:block;" border="0">
					  <?php else : ?>
						  <?php esc_attr_e( get_bloginfo( 'name' ) ); ?>
					  <?php endif; ?>
				  </a>
              </td>
            </tr>
            <tr>
              <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
            </tr>
          </table>
          <!-- END HEADER -->

          <!-- START INTRO -->
          <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" class="wrapper" bgcolor="#FFFFFF">
            <tr>
              <td height="40" style="font-size:40px; line-height:40px;">&nbsp;</td>
            </tr>
            <tr>
              <td align="center" style="padding:0 20px;">
                <table width="580" cellpadding="0" cellspacing="0" border="0" class="container">
				  <?php if ( $this->get( 'lead' ) ) : ?>
	                <tr>
	                    <td align="left" style="font-family: 'Montserrat', Arial, sans-serif; font-size:20px; line-height:26px; color:#222222; font-weight:bold; text-transform:uppercase;" class="body-text">
	                      <h2 style="font-family: 'Montserrat', Arial, sans-serif; font-size:20px; line-height:26px; color:#222222; font-weight:bold; text-transform:uppercase; padding:0; margin:0;" class="body-text"><?php echo esc_html( $this->get( 'lead' ) ); ?></h2>
	                    </td>
	                </tr>
				  <?php endif; ?>
                  <tr>
                    <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                  </tr>
                  <tr>
                    <td align="left" style="font-family: 'Montserrat', Arial, sans-serif; font-size:14px; line-height:20px; color:#666666;" class="body-text">
                    	<?php echo $this->get( 'content' ); ?>
                    </td>
                  </tr>
                  <tr>
                    <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                  </tr>
				  <?php if ( $this->get( 'book_again' ) ) :?>
				  <tr>
                    <td align="center">
                      <table border="0" cellspacing="0" cellpadding="0" align="center">
                        <tbody><tr>
                          <td>
                            <table border="0" cellspacing="0" cellpadding="0" align="center">
                              <tbody><tr>
								<?php
									$booking_page = $rtb_controller->settings->get_setting( 'booking-page' );
									if ( !empty( $booking_page ) ) {
										$booking_url = get_permalink( $booking_page );
									} else {
										$booking_url = home_url();
									}
								?>
                                <td align="center" style="-webkit-border-radius: 50px; -moz-border-radius: 50px; border-radius: 50px;" bgcolor="<?php esc_attr_e( $this->get( 'color_button' ) ); ?>" class="body-text">
                                  <a href="<?php echo esc_url( $booking_url ); ?>" target="_blank" style="font-size: 14px; font-family: 'Montserrat', Arial, sans-serif; color: <?php esc_attr_e( $this->get( 'color_button_text' ) ); ?>; text-decoration: none; text-decoration: none; border-radius: 50px; padding: 12px 22px; border: 1px solid <?php esc_attr_e( $this->get( 'color_button' ) ); ?>; display: inline-block; text-transform:uppercase; font-weight:bold;" class="body-text"><?php echo esc_html( $this->get( 'book_again' ) ); ?></a>
                                </td>
                              </tr>
                            </tbody></table>
                          </td>
                        </tr>
                      </tbody></table>
                    </td>
                  </tr>
	              <?php endif; ?>
                </table>
              </td>
            </tr>
            <tr>
              <td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
            </tr>
          </table>
          <!-- END INTRO -->

          <!-- START FOOTER INFO -->
          <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" class="wrapper" bgcolor="#FFFFFF" style="border-top:1px solid #dddddd;">
            <tr>
              <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
            </tr>
            <tr>
              <td align="center" style="padding:0 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
				  <?php if ( $this->get( 'footer_message' ) ) : ?>
				  <tr>
					<td width="600" align="center" class="mobile" style="font-family: 'Montserrat', Arial, sans-serif; font-size:12px; line-height:18px; color:#666666;" class="body-text">
						<p style="font-family: 'Montserrat', Arial, sans-serif; font-size:12px; line-height:18px; color:#666666; padding:0; margin:0;" class="body-text"><?php echo esc_html( $this->get( 'footer_message' ) ); ?></p>
					</td>
				  </tr>
					  <?php if ( $this->get( 'show_contact' ) ) : ?>
	                  <tr>
	                    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
	                  </tr>
				  	  <?php endif; ?>
    			  <?php endif; ?>
				  <?php if ( $this->get( 'show_contact' ) ) : ?>
					  <tr>
						  <td width="600" align="center" class="mobile" style="font-family: 'Montserrat', Arial, sans-serif; font-size:12px; line-height:18px; color:#666666;" class="body-text">
							  <p style="font-family: 'Montserrat', Arial, sans-serif; font-size:12px; line-height:18px; color:#666666; padding:0; margin:0;" class="body-text"><?php echo nl2br( esc_html( $this->get( 'address' )['text'] ) ); ?></p>
							  <p style="font-family: 'Montserrat', Arial, sans-serif; font-size:12px; line-height:18px; color:#666666; padding:0; margin:0;" class="body-text"><?php echo esc_html( $this->get( 'phone' ) ); ?></p>
						  </td>
					  </tr>
				  <?php endif; ?>
                </table>
              </td>
            </tr>
            <tr>
              <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
            </tr>
          </table>
          <!-- END FOOTER INFO -->

          <!-- START COPYRIGHT -->
          <table width="640" cellpadding="0" cellspacing="0" border="0" align="center" class="wrapper" style="border-top:1px solid #dddddd;" bgcolor="#FFFFFF">
            <tr>
              <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
            </tr>
            <tr>
              <td align="center" style="padding:0 20px;">
                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                  <tr>
                    <td width="600" align="center" class="mobile" style="font-family: 'Montserrat', Arial, sans-serif; font-size:12px; line-height:18px; color:#666666;" class="body-text">
                      <p style="font-family: 'Montserrat', Arial, sans-serif; font-size:12px; line-height:18px; color:#666666; padding:0; margin:0;" class="body-text"><?php echo esc_html( $this->get( 'acknowledgement' ) ); ?></p>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
            </tr>
          </table>
          <!-- END COPYRIGHT -->

        </td>
      </tr>
    </table>
    <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;">
      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
      &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
    </div>
  </body>
</html>
