<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wpo-step-description">
	<h2><?php _e( 'Your logo' , 'woocommerce-pdf-invoices-packing-slips' ); ?></h2>
	<p><?php _e( 'Set the header image that will display on your invoice.' , 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
</div>
<div class="wpo-setup-input">
	<?php
	$current_settings = get_option( 'wpo_wcpdf_settings_general', array() );
	$logo_id = !empty($current_settings['header_logo']) ? $current_settings['header_logo'] : '';

	if ( !empty($logo_id) && $logo = wp_get_attachment_image_src( $logo_id, 'full', false ) ) {
		$logo_src = $logo[0];
	} else {
		// empty image
		$logo_src = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';
	}
	?>
	<img src="<?php echo $logo_src; ?>" width="100%" height="20px" alt="" id="img-header_logo"/>
	<input id="header_logo" name="wcpdf_settings[wpo_wcpdf_settings_general][header_logo]" type="hidden" value="<?php echo $logo_id; ?>" />
	<span class="button wpo_upload_image_button header_logo" data-uploader_title="<?php _e( 'Select or upload your invoice header/logo', 'woocommerce-pdf-invoices-packing-slips' ); ?>" data-uploader_button_text="<?php _e( 'Set image', 'woocommerce-pdf-invoices-packing-slips' ); ?>" data-remove_button_text="<?php _e( 'Remove image', 'woocommerce-pdf-invoices-packing-slips' ); ?>" data-input_id="header_logo"><?php _e( 'Set image', 'woocommerce-pdf-invoices-packing-slips' ); ?></span>
</div>