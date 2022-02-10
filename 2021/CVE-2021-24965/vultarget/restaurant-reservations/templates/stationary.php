<?php
/**
 * Email Template: Stationary
 *
 * An elegant template for sending a message with an air of sophistication.
 *
 * @since 0.1
 */
global $rtb_controller;
?><!doctype html>
<html>
<head>
<link href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i" rel="stylesheet">
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
@media screen and (max-width: 600px) {
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

  .full-width {
    width: 100% !important;
  }

  .letter-padding {
    padding: 25px !important;
  }

  .ptop25 {
    padding-top: 25px !important;
  }

  .pbot25 {
    padding-bottom: 25px !important;
  }
}

/* ANDROID CENTER FIX */
div[style*="margin: 16px 0;"] { margin: 0 !important; }
</style>
</head>
<body style="margin: 0 !important; padding: 0; !important background-color: #f6f6f6;" bgcolor="#f6f6f6">

<!-- HIDDEN PREHEADER TEXT -->
<div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Lora, serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
   <title><?php echo esc_html( $this->get( 'lead' ) ); ?></title>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
   <tr>
       <td align="center" valign="top" width="100%" background="images/bg.jpg" bgcolor="<?php esc_attr_e( $this->get( 'color_primary' ) ); ?>" style="background: <?php esc_attr_e( $this->get( 'color_primary' ) ); ?>; padding: 50px 15px 0 15px;" class="mobile-padding ptop25">
           <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" class="full-width mobile-padding">
               <tr>
                   <td align="center" valign="top" style="padding: 0 0 50px 0;" class="pbot25">
	 				  <a href="<?php echo esc_url( home_url() ); ?>" target="_blank">
	 					  <?php if ( $this->get( 'logo' ) ) : ?>
	 						  <img alt="<?php esc_attr_e( get_bloginfo( 'name' ) ); ?>" src="<?php echo esc_url( $this->get( 'logo' ) ); ?>" width="<?php echo absint( $this->get( 'logo_width' ) ); ?>" height="<?php echo absint( $this->get( 'logo_height' ) ); ?>" style="margin:0; padding:0; border:none; display:block;" border="0">
	 					  <?php else : ?>
	 						  <?php esc_attr_e( get_bloginfo( 'name' ) ); ?>
	 					  <?php endif; ?>
	 				  </a>
                   </td>
               </tr>
               <tr>
                   <td align="left" valign="top" style="padding: 50px; font-family: Lora, serif; color: #555555; font-size: 16px; line-height: 26px; margin: 0; border-radius: 3px; box-shadow: 0 0 5px rgba(0,0,0,.2);" bgcolor="#f6f6f6" class="letter-padding">
					   <?php if ( $this->get( 'lead' ) ) : ?>
					     <p style="color: #555555; font-size: 24px; line-height: 32px; margin: 0;"><?php echo esc_html( $this->get( 'lead' ) ); ?></p>
					   <?php endif; ?>
					   <?php echo $this->get( 'content' ); ?>
                   </td>
               </tr>
               <tr>
                   <td align="center" valign="top" style="padding: 25px 0; font-family: Lora, serif; color: <?php esc_attr_e( $this->get( 'color_primary_text' ) ); ?>;">
	   				  <?php if ( $this->get( 'footer_message' ) ) : ?>
						  <p style="font-size: 14px; line-height: 20px;"><?php echo esc_html( $this->get( 'footer_message' ) ); ?></p>
	       			  <?php endif; ?>
	   				  <?php if ( $this->get( 'show_contact' ) ) : ?>
						  <p style="font-size: 14px; line-height: 20px;">
							  <?php echo nl2br( esc_html( $this->get( 'address' )['text'] ) ); ?>
						  </p>
						  <p style="font-size: 14px; line-height: 20px;">
							  <?php echo esc_html( $this->get( 'phone' ) ); ?>
						  </p>
	   				  <?php endif; ?>
					  <br><br><br><br><br><br><br><br><br><br>
                   </td>
               </tr>
           </table>
       </td>
   </tr>
</table>

</body>
</html>
