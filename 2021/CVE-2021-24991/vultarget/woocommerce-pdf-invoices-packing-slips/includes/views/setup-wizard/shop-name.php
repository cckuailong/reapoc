<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wpo-step-description">
	<h2><?php _e( 'Enter your shop name', 'woocommerce-pdf-invoices-packing-slips' ); ?></h2>
	<p><?php _e( 'Lets quickly setup your invoice. Please enter the name and address of your shop in the fields on the right.', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
</div>
<div class="wpo-setup-input">
	<?php
	$current_settings = get_option( 'wpo_wcpdf_settings_general', array() );
	?>
	<input type="text" class="shop-name" placeholder="Shop name" name="wcpdf_settings[wpo_wcpdf_settings_general][shop_name][default]" value="<?php echo !empty($current_settings['shop_name']) ? array_pop($current_settings['shop_name']) : get_bloginfo( 'name' ); ?>">
	<textarea class="shop-address" placeholder="Shop address" name="wcpdf_settings[wpo_wcpdf_settings_general][shop_address][default]"><?php echo isset($current_settings['shop_address']) ? array_pop($current_settings['shop_address']) : ''; ?></textarea>
</div>