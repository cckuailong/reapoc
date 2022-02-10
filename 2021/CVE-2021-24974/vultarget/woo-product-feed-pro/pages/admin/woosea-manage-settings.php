<?php
$domain = $_SERVER['HTTP_HOST'];
$plugin_settings = get_option( 'plugin_settings' );
$license_information = get_option( 'license_information' );
$host = $_SERVER['HTTP_HOST'];
$directory_perm_xml = "";
$directory_perm_csv = "";
$directory_perm_txt = "";
$directory_perm_tsv = "";
$directory_perm_logs = "";

$elite_disable = "enabled";
if(($license_information['license_valid'] == "false") OR (!is_array($license_information))){
	$elite_disable = "disabled";
}

if(empty($license_information['license_email'])){
	$license_information['license_email'] = "";
}
if(empty($license_information['license_key'])){
	$license_information['license_key'] = "";
}

$versions = array (
	"PHP" => (float)phpversion(),
	"Wordpress" => get_bloginfo('version'),
	"WooCommerce" => WC()->version,
	"WooCommerce Product Feed PRO" => WOOCOMMERCESEA_PLUGIN_VERSION
);

/**
 * Create notification object and get message and message type as WooCommerce is inactive
 * also set variable allowed on 0 to disable submit button on step 1 of configuration
 */
$notifications_obj = new WooSEA_Get_Admin_Notifications;
if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $notifications_box = $notifications_obj->get_admin_notifications ( '9', 'false' );
} else {
        $notifications_box = $notifications_obj->get_admin_notifications ( '14', 'false' );
}

if ($versions['PHP'] < 5.6){
        $notifications_box = $notifications_obj->get_admin_notifications ( '11', 'false' );
	$php_validation = "False";
} else {
	$php_validation = "True";
}

if ($versions['WooCommerce'] < 3){
        $notifications_box = $notifications_obj->get_admin_notifications ( '13', 'false' );
}

if (!wp_next_scheduled( 'woosea_cron_hook' ) ) {
	$notifications_box = $notifications_obj->get_admin_notifications ( '12', 'false' );
}

if(array_key_exists('notice', $license_information)){
	if($license_information['notice'] == "true"){
		$notifications_box['message_type'] = $license_information['message_type'];
		$notifications_box['message'] = $license_information['message'];
	}
}

/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return _e( 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="woo-product-feed-pro-ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!','woo-product-feed-pro' );
}
add_filter('admin_footer_text', 'my_footer_text');

                	
//we check if the page is visited by click on the tabs or on the menu button.
//then we get the active tab.
$active_tab = "woosea_manage_settings";

// create nonce
$nonce = wp_create_nonce( 'woosea_ajax_nonce' );

$header_text = __( 'Plugin settings', 'woo-product-feed-pro' );
if(isset($_GET["tab"])) {
	if($_GET["tab"] == "woosea_manage_settings"){
        	$active_tab = "woosea_manage_settings";
		$header_text = __( 'Plugin settings', 'woo-product-feed-pro' );
	} elseif ($_GET["tab"] == "woosea_system_check"){
        	$active_tab = "woosea_system_check";
		$header_text = __( 'Plugin systems check', 'woo-product-feed-pro' );
     	} else {
             	$active_tab = "woosea_manage_attributes";
		$header_text = __( 'Attribute settings', 'woo-product-feed-pro' );
		$license_information['message'] = __( 'Add extra fields to your product (edit) pages so you can add Brands, GTINs, Size, Color and many more fields to your product feeds.<br/><br/>This plugin, by default, only shows a limit amount of the extra fields in the configuration, product edit pages ond filter/rule drop-downs. We have done so for performance reasons and usability. You can however add missing extra fields by enabling them below. After enabling an extra field it shows on the product edit pages and the drop-downs during configuration so you can use them for your product feeds.', 'woo-product-feed-pro' );
	}
}
?>	

<div class="wrap">
 
        <div class="woo-product-feed-pro-form-style-2">

                <tbody class="woo-product-feed-pro-body">
                        <div class="woo-product-feed-pro-form-style-2-heading">
				<span>
				<?php
					print "$header_text";
				?>
				</span>
			</div>

			<?php
                        if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
                                ?>                              
                                        <div class="notice notice-error is-dismissible">
                                                <p>
                                                <strong><?php _e( 'WARNING: Your WP-Cron is disabled', 'woo-product-feed-pro' );?></strong><br/></br/>
                                                We detected that your WP-cron has been disabled in your wp-config.php file. Our plugin heavily depends on the WP-cron being active for it to be able to update and generate your product feeds. More information on the inner workings of our plugin and instructions on how to enable your WP-Cron can be found here: <a href="https://adtribes.io/help-my-feed-processing-is-stuck/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=cron-warning&utm_content=notification" target="_blank"><strong>My feed won't update or is stuck processing</strong></a>.
                                                </p>
                                        </div>
                                <?php
                        }
                        ?>

    
        	    	<!-- wordpress provides the styling for tabs. -->
			<h2 class="nav-tab-wrapper">
                		<!-- when tab buttons are clicked we jump back to the same page but with a new parameter that represents the clicked tab. accordingly we make it active -->
                		<a href="?page=woosea_manage_settings&tab=woosea_manage_settings" class="nav-tab <?php if($active_tab == 'woosea_manage_settings'){echo 'nav-tab-active';} ?> "><?php _e('Plugin settings', 'woo-product-feed-pro'); ?></a>
                		<a href="?page=woosea_manage_settings&tab=woosea_system_check" class="nav-tab <?php if($active_tab == 'woosea_system_check'){echo 'nav-tab-active';} ?>"><?php _e('Plugin systems check', 'woo-product-feed-pro'); ?></a>
	  		</h2>

			<div class="woo-product-feed-pro-table-wrapper">
				<div class="woo-product-feed-pro-table-left">
					<?php
					if($active_tab == "woosea_manage_settings"){
					?>

			       		<table class="woo-product-feed-pro-table">
                                                <tr><td><strong><?php _e( 'Plugin setting', 'woo-product-feed-pro' );?></strong></td><td><strong><?php _e( 'Off / On', 'woo-product-feed-pro' );?></strong></td></tr>

						<form action="" method="post">
						<?php
						if($elite_disable == "enabled"){
						?>
						<tr class="<?php print"$elite_disable";?>" id="json_option">
							<td>
								<span><?php _e( 'Increase the number of products that will be approved in Google\'s Merchant Center:', 'woo-product-feed-pro' );?><br/>
								<?php _e( 'This option will fix WooCommerce\'s (JSON-LD) structured data bug and add extra structured data elements to your pages.', 'woo-product-feed-pro' );?> (<a href="https://adtribes.io/woocommerce-structured-data-bug/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_content=structured data bug" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro' );?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
								<?php
								$structured_data_fix = get_option ('structured_data_fix');
 	                                                       	if($structured_data_fix == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"fix_json_ld\" name=\"fix_json_ld\" class=\"checkbox-field\" checked $elite_disable>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"fix_json_ld\" name=\"fix_json_ld\" class=\"checkbox-field\" $elite_disable>";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr class="<?php print"$elite_disable";?>" id="structured_vat_option">
							<td>
								<span><?php _e( 'Exclude TAX from structured data prices', 'woo-product-feed-pro' );?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
								<?php
								$structured_vat = get_option ('structured_vat');
 	                                                       	if($structured_vat == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"no_structured_vat\" name=\"no_structured_vat\" class=\"checkbox-field\" checked $elite_disable>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"no_structured_vat\" name=\"no_structured_vat\" class=\"checkbox-field\" $elite_disable>";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr class="<?php print"$elite_disable";?>" id="identifier_option">
							<td>
								<span><?php _e( 'Add GTIN, MPN, UPC, EAN, Product condition, Optimised title, Installment, Unit measure, Brand and many more attributes to your store:', 'woo-product-feed-pro' );?> (<a href="https://adtribes.io/add-gtin-mpn-upc-ean-product-condition-optimised-title-and-brand-attributes/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_content=adding fields" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro' );?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_unique_identifiers = get_option ('add_unique_identifiers');
                                                        	if($add_unique_identifiers == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_identifiers\" name=\"add_identifiers\" class=\"checkbox-field\" checked $elite_disable>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_identifiers\" name=\"add_identifiers\" class=\"checkbox-field\" $elite_disable>";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr class="<?php print"$elite_disable";?>" id="manipulation_option">
							<td>
								<span><?php _e( 'Enable the Product Data Manipulation feature:', 'woo-product-feed-pro' );?> (<a href="https://adtribes.io/feature-product-data-manipulation/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_content=wpml support" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro' );?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_manipulation_support = get_option ('add_manipulation_support');
                                                        	if($add_manipulation_support == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_manipulation_support\" name=\"add_manipulation_support\" class=\"checkbox-field\" checked $elite_disable>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_manipulation_support\" name=\"add_manipulation_support\" class=\"checkbox-field\" $elite_disable>";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>

						<tr class="<?php print"$elite_disable";?>" id="wpml_option">
							<td>
								<span><?php _e( 'Enable WPML support:', 'woo-product-feed-pro');?> (<a href="https://adtribes.io/wpml-support/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_content=wpml support" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro');?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_wpml_support = get_option ('add_wpml_support');
                                                        	if($add_wpml_support == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_wpml_support\" name=\"add_wpml_support\" class=\"checkbox-field\" checked $elite_disable>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_wpml_support\" name=\"add_wpml_support\" class=\"checkbox-field\" $elite_disable>";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>

						<tr class="<?php print"$elite_disable";?>" id="aelia_option">
							<td>
								<span><?php _e( 'Enable Aelia Currency Switcher support:', 'woo-product-feed-pro');?> (<a href="https://adtribes.io/aelia-currency-switcher-feature/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_content=aelia support" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro'); ?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_aelia_support = get_option ('add_aelia_support');
                                                        	if($add_aelia_support == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_aelia_support\" name=\"add_aeli_support\" class=\"checkbox-field\" checked $elite_disable>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_aelia_support\" name=\"add_aeli_support\" class=\"checkbox-field\" $elite_disable>";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<td>
								<span><?php _e( 'Use parent variable product image for variations', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_mother_image = get_option ('add_mother_image');
                                                        	if($add_mother_image == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_mother_image\" name=\"add_mother_image\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_mother_image\" name=\"add_mother_image\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr>
							<td>
								<span><?php _e( 'Add shipping costs for all countries to your feed (Google Shopping / Facebook only)', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_all_shipping = get_option ('add_all_shipping');
                                                        	if($add_all_shipping == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_all_shipping\" name=\"add_all_shipping\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_all_shipping\" name=\"add_all_shipping\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr>
							<td>
								<span><?php _e( 'Remove all other shipping classes when free shipping criteria are met (Google Shopping / Facebook only)', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$free_shipping = get_option ('free_shipping');
                                                        	if($free_shipping == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"free_shipping\" name=\"free_shipping\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"free_shipping\" name=\"free_shipping\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr>
							<td>
								<span><?php _e( 'Remove the free shipping zone from your feed (Google Shopping / Facebook only)', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$remove_free_shipping = get_option ('remove_free_shipping');
                                                        	if($remove_free_shipping == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"remove_free_shipping\" name=\"remove_free_shipping\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"remove_free_shipping\" name=\"remove_free_shipping\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr>
							<td>
								<span><?php _e( 'Remove the local pickup shipping zone from your feed (Google Shopping / Facebook only)', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$local_pickup_shipping = get_option ('local_pickup_shipping');
                                                        	if($local_pickup_shipping == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"local_pickup_shipping\" name=\"local_pickup_shipping\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"local_pickup_shipping\" name=\"local_pickup_shipping\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<tr>
							<td>
								<span><?php _e( 'Enable logging', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_woosea_logging = get_option ('add_woosea_logging');
                                                        	if($add_woosea_logging == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_woosea_logging\" name=\"add_woosea_logging\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_woosea_logging\" name=\"add_woosea_logging\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<!--
						<tr>
							<td>
								<span><?php _e( 'Add CDATA to title, description and short description:', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_woosea_cdata = get_option ('add_woosea_cdata');
                                                        	if($add_woosea_cdata == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_woosea_cdata\" name=\"add_woosea_cdata\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_woosea_cdata\" name=\"add_woosea_cdata\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						-->

						<tr id="facebook_pixel">
							<td>
								<span><?php _e( 'Add Facebook Pixel', 'woo-product-feed-pro');?> (<a href="https://adtribes.io/facebook-pixel-feature/" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro' );?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_facebook_pixel = get_option ('add_facebook_pixel');
                                                        	if($add_facebook_pixel == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_facebook_pixel\" name=\"add_facebook_pixel\" class=\"checkbox-field\" value=\"$nonce\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_facebook_pixel\" name=\"add_facebook_pixel\" class=\"checkbox-field\" value=\"$nonce\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<?php
                                                if($add_facebook_pixel == "yes"){
							$facebook_pixel_id = get_option('woosea_facebook_pixel_id');
							print "<tr id=\"facebook_pixel_id\"><td colspan=\"2\"><span>Insert your Facebook Pixel ID</span>&nbsp;<input type=\"hidden\" name=\"nonce_facebook_pixel_id\" id=\"nonce_facebook_pixel_id\" value=\"$nonce\"><input type=\"text\" class=\"input-field-medium\" id=\"fb_pixel_id\" name=\"fb_pixel_id\" value=\"$facebook_pixel_id\">&nbsp;<input type=\"button\" id=\"save_facebook_pixel_id\" value=\"Save\"></td></tr>";	
						}
						?>

						<?php
						$content_ids = "variation";
						$content_ids = get_option( 'add_facebook_pixel_content_ids' );
						?>

						<tr id="content_ids">
							<td colspan="2">
                                                        	<span><?php _e( 'Content IDS variable products Facebook Pixel', 'woo-product-feed-pro');?></span>
								<select id="woosea_content_ids" name="woosea_content_ids" class="select-field">
									<?php
									if($content_ids == "variation"){
										print "<option value=\"variation\" selected>Variation product ID's</option>";
										print "<option value=\"variable\">Variable product ID</option>";
									} else {
										print "<option value=\"variation\" selected>Variation product ID's</option>";
										print "<option value=\"variable\" selected>Variable product ID</option>";
									}
									?>
								</select>
							</td>
						</tr>
					        <?php
                                                if($elite_disable == "enabled"){
                                                ?>	
						<tr class="<?php print"$elite_disable";?>" id="facebook_capi">
							<td>
								<span><?php _e( 'Enable Facebook Conversion API:', 'woo-product-feed-pro');?> (<a href="https://adtribes.io/facebook-conversion-api/" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro' );?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_facebook_capi = get_option ('add_facebook_capi');
                                                        	if($add_facebook_capi == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_facebook_capi\" name=\"add_facebook_capi\" class=\"checkbox-field\" value=\"$nonce\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_facebook_capi\" name=\"add_facebook_capi\" class=\"checkbox-field\" value=\"$nonce\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<?php
                                                if($add_facebook_capi == "yes"){
							$facebook_capi_token = get_option('woosea_facebook_capi_token');
							print "<tr id=\"facebook_capi_token\"><td colspan=\"2\"><span>Insert your Facebook Conversion API token:</span><br/><br/><input type=\"hidden\" name=\"nonce_facebook_capi_id\" id=\"nonce_facebook_capi_id\" value=\"$nonce\"><input type=\"textarea\" class=\"textarea-field\" id=\"fb_capi_token\" name=\"fb_capi_token\" value=\"$facebook_capi_token\"><br/><br/><input type=\"button\" id=\"save_facebook_capi_token\" value=\"Save\"></td></tr>";	
						}
						?>
						<?php
						}
						?>
						<tr id="remarketing">
							<td>
								<span><?php _e( 'Add Google Dynamic Remarketing Pixel:', 'woo-product-feed-pro');?></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
								<?php
								$add_remarketing = get_option ('add_remarketing');
                                                        	if($add_remarketing == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_remarketing\" name=\"add_remarketing\" class=\"checkbox-field\" value=\"$nonce\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_remarketing\" name=\"add_remarketing\" class=\"checkbox-field\" value=\"$nonce\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<?php
                                                if($add_remarketing == "yes"){
							$adwords_conversion_id = get_option('woosea_adwords_conversion_id');

							print "<tr id=\"adwords_conversion_id\"><td colspan=\"2\"><span>Insert your Dynamic Remarketing Conversion tracking ID:</span>&nbsp;<input type=\"hidden\" name=\"nonce_adwords_conversion_id\" id=\"nonce_adwords_conversion_id\" value=\"$nonce\"><input type=\"text\" class=\"input-field-medium\" id=\"adwords_conv_id\" name=\"adwords_conv_id\" value=\"$adwords_conversion_id\">&nbsp;<input type=\"button\" id=\"save_conversion_id\" value=\"Save\"></td></tr>";	
						}
						?>

						<tr id="batch">
							<td>
								<span><?php _e( 'Change products per batch number', 'woo-product-feed-pro');?> (<a href="https://adtribes.io/batch-size-configuration-product-feed/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_content=batch size" target="_blank"><?php _e( 'Read more about this', 'woo-product-feed-pro' );?>)</a></span>
							</td>
							<td>
                                                		<label class="woo-product-feed-pro-switch">
                                                        	<?php
								$add_batch = get_option ('add_batch');
                                                        	if($add_batch == "yes"){
                                                                	print "<input type=\"checkbox\" id=\"add_batch\" name=\"add_batch\" class=\"checkbox-field\" checked>";
							 	} else {
                                                                	print "<input type=\"checkbox\" id=\"add_batch\" name=\"add_batch\" class=\"checkbox-field\">";
                                                        	}
                                                        	?>
                                                        	<div class="woo-product-feed-pro-slider round"></div>
                                                		</label>
							</td>
						</tr>
						<?php
                                                if($add_batch == "yes"){
							$woosea_batch_size = get_option('woosea_batch_size');
							print "<tr id=\"woosea_batch_size\"><td colspan=\"2\"><span>Insert batch size:</span>&nbsp;<input type=\"hidden\" name=\"nonce_batch\" id=\"nonce_batch\" value=\"$nonce\"><input type=\"text\" class=\"input-field-medium\" id=\"batch_size\" name=\"batch_size\" value=\"$woosea_batch_size\">&nbsp;<input type=\"button\" id=\"save_batch_size\" value=\"Save\"></td></tr>";	
						}
						?>
						</form>
					</table>
					<?php
					} elseif ($active_tab == "woosea_system_check"){
						// Check if the product feed directory is writeable
						$upload_dir = wp_upload_dir();
						$external_base = $upload_dir['basedir'];
                				$external_path = $external_base . "/woo-product-feed-pro/";
                				$external_path_xml = $external_base . "/woo-product-feed-pro/";
                				$external_path_csv = $external_base . "/woo-product-feed-pro/";
                				$external_path_txt = $external_base . "/woo-product-feed-pro/";
                				$external_path_tsv = $external_base . "/woo-product-feed-pro/";
                				$external_path_logs = $external_base . "/woo-product-feed-pro/";
						$test_file = $external_path . "/tesfile.txt";				
						$test_file_xml = $external_path . "xml/tesfile.txt";				
						$test_file_csv = $external_path . "csv/tesfile.txt";				
						$test_file_txt = $external_path . "txt/tesfile.txt";				
						$test_file_tsv = $external_path . "tsv/tesfile.txt";				
						$test_file_logs = $external_path . "logs/tesfile.txt";				

						if (is_writable($external_path)) {
				                      	// Normal root category
                                                        $fp = @fopen($test_file, 'w');
                                                        @fwrite($fp, 'Cats chase mice');
                                                        @fclose($fp);
                                                        if(is_file($test_file)){
                                                                $directory_perm = "True";
                                                        }

                                                        // XML subcategory
                                                        $fp = @fopen($test_file_xml, 'w');
                                                        if(!is_bool($fp)){
                                                                @fwrite($fp, 'Cats chase mice');
                                                                @fclose($fp);
                                                                if(is_file($test_file_xml)){
                                                                        $directory_perm_xml = "True";
                                                                } else {
                                                                        $directory_perm_xml = "False";
                                                                }
                                                        } else {
                                                                $directory_perm_xml = "Unknown";
                                                        }

                                                        // CSV subcategory
                                                        $fp = @fopen($test_file_csv, 'w');
                                                        if(!is_bool($fp)){
                                                                @fwrite($fp, 'Cats chase mice');
                                                                @fclose($fp);
                                                                if(is_file($test_file_csv)){
                                                                        $directory_perm_csv = "True";
                                                                } else {
                                                                        $directory_perm_csv = "False";
                                                                }
                                                        } else {
                                                                $directory_perm_csv = "Unknown";
                                                        }

                                                        // TXT subcategory
                                                        $fp = @fopen($test_file_txt, 'w');
                                                        if(!is_bool($fp)){
                                                                @fwrite($fp, 'Cats chase mice');
                                                                @fclose($fp);
                                                                if(is_file($test_file_txt)){
                                                                        $directory_perm_txt = "True";
                                                                } else {
                                                                        $directory_perm_txt = "False";
                                                                }
                                                        } else {
                                                                $directory_perm_txt = "Unknown";
                                                        }		
					                                                        // TSV subcategory
                                                        $fp = @fopen($test_file_tsv, 'w');
                                                        if(!is_bool($fp)){
                                                                @fwrite($fp, 'Cats chase mice');
                                                                @fclose($fp);
                                                                if(is_file($test_file_tsv)){
                                                                        $directory_perm_tsv = "True";
                                                                } else {
                                                                        $directory_perm_tsv = "False";
                                                                }
                                                        } else {
                                                                $directory_perm_tsv = "Uknown";
                                                        }

                                                        // Logs subcategory
                                                        $fp = @fopen($test_file_logs, 'w');
                                                        if(!is_bool($fp)){
                                                                @fwrite($fp, 'Cats chase mice');
                                                                @fclose($fp);
                                                                if(is_file($test_file_logs)){
                                                                        $directory_perm_logs = "True";
                                                                } else {
                                                                        $directory_perm_logs = "False";
                                                                }
                                                        } else {
                                                                $directory_perm_logs = "Unknown";
                                                        }	
						} else {
							$directory_perm = "False";
						}

						// Check if the cron is enabled
						if (!wp_next_scheduled( 'woosea_cron_hook' ) ) {
							$cron_enabled = "False";
						} else {
							$cron_enabled = "True";
						}

			                        if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
							$cron_enabled = "<strong>False</strong>";
						}

						print "<table class=\"woo-product-feed-pro-table\">";
						print "<tr><td><strong>System check</strong></td><td><strong>Status</strong></td></tr>";
						print "<tr><td>WP-Cron enabled</td><td>$cron_enabled</td></tr>";
						print "<tr><td>PHP-version</td><td>$php_validation ($versions[PHP])</td></tr>";
						print "<tr><td>Product feed directory writable</td><td>$directory_perm</td></tr>";
						print "<tr><td>Product feed XML directory writable</td><td>$directory_perm_xml</td></tr>";
						print "<tr><td>Product feed CSV directory writable</td><td>$directory_perm_csv</td></tr>";
						print "<tr><td>Product feed TXT directory writable</td><td>$directory_perm_txt</td></tr>";
						print "<tr><td>Product feed TSV directory writable</td><td>$directory_perm_tsv</td></tr>";
						print "<tr><td>Product feed LOGS directory writable</td><td>$directory_perm_logs</td></tr>";
						print "<tr><td colspan=\"2\">&nbsp;</td></tr>";
						print "</table>";

					} else {
					?>
					<table class="woo-product-feed-pro-table">
						<?php
						if(!get_option( 'woosea_extra_attributes' )){
							$extra_attributes = array();
						} else {
							$extra_attributes = get_option( 'woosea_extra_attributes' );
						}
						print "<tr><td><strong>Attribute name</strong></td><td><strong>On / Off</strong></td></tr>";

						$list = array (
    							"custom_attributes__woosea_brand" 			=>  "woosea brand",
    							"custom_attributes__woosea_gtin" 			=>  "woosea gtin",
    							"custom_attributes__woosea_ean" 			=>  "woosea ean",
    							"custom_attributes__woosea_mpn" 			=>  "woosea mpn",
    							"custom_attributes__woosea_optimized_title" 		=>  "woosea optimized title",
							"custom_attributes__woosea_age_group" 			=>  "woosea age group",
    							"custom_attributes__woosea_color" 			=>  "woosea color",
    							"custom_attributes__woosea_condition" 			=>  "woosea condition",
							"custom_attributes__woosea_cost_of_good_sold" 		=>  "woosea cost of good sold",
    							"custom_attributes__woosea_custom_field_0" 		=>  "woosea custom field 0",
    							"custom_attributes__woosea_custom_field_1" 		=>  "woosea custom field 1",
    							"custom_attributes__woosea_custom_field_2" 		=>  "woosea custom field 2",
    							"custom_attributes__woosea_custom_field_3" 		=>  "woosea custom field 3",
    							"custom_attributes__woosea_custom_field_4" 		=>  "woosea custom field 4",
    							"custom_attributes__woosea_energy_efficiency_class" 	=>  "woosea energy efficiency class",
    							"custom_attributes__woosea_exclude_product" 		=>  "woosea exclude product",
    							"custom_attributes__woosea_gender" 			=>  "woosea gender",
    							"custom_attributes__woosea_installment_amount" 		=>  "woosea installment amount",
    							"custom_attributes__woosea_installment_months" 		=>  "woosea installment months",
    							"custom_attributes__woosea_is_bundle" 			=>  "woosea is bundle",
    							"custom_attributes__woosea_is_promotion" 		=>  "woosea is promotion",
    							"custom_attributes__woosea_material" 			=>  "woosea material",
    							"custom_attributes__woosea_max_energy_efficiency_class" =>  "woosea max energy efficiency class",
    							"custom_attributes__woosea_min_energy_efficiency_class" =>  "woosea min energy efficiency class",
    							"custom_attributes__woosea_multipack" 			=>  "woosea multipack",
    							"custom_attributes__woosea_pattern" 			=>  "woosea pattern",
    							"custom_attributes__woosea_size" 			=>  "woosea size",
    							"custom_attributes__woosea_unit_pricing_base_measure" 	=>  "woosea unit pricing base measure",
    							"custom_attributes__woosea_unit_pricing_measure" 	=>  "woosea unit pricing measure",
    							"custom_attributes__woosea_upc" 			=>  "woosea upc",
						);

						foreach ($list as $key => $value){
							// Trim spaces before and after			
							$value = trim($value);	
	
							if(in_array($value, $extra_attributes,TRUE)){
								$checked = "checked";
							} else {
								$checked = "";
							}

							if(strpos($key, 'woosea')){
								$value_display = str_replace("woosea", "",$value);

								print "<tr id=\"$key\"><td><span>$value_display</span></td>";
								print "<td>";
								?>
                                	                                <label class="woo-product-feed-pro-switch">
                                        	                        <input type="hidden" name="manage_attribute" value="<?php print "$key";?>"><input type="checkbox" id="attribute_active" name="<?php print "$value";?>" class="checkbox-field" value="<?php print "$key";?>" <?php print "$checked";?>>
									<div class="woo-product-feed-pro-slider round"></div>
                                                        	        </label>
								<?php
								print "</td>";
								print "</tr>";
							}
						}
						?>
					</table>
					<?php
					}
					?>
				</div>

				<div class="woo-product-feed-pro-table-right">

                                <table class="woo-product-feed-pro-table">
                                        <tr>
                                                <td><strong><?php _e( 'Why upgrade to Elite?', 'woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <?php _e( 'Enjoy all priviliges of our Elite features and priority support and upgrade to the Elite version of our plugin now!', 'woo-product-feed-pro' );?>
                                                        <ul>
                                                                <li><strong>1.</strong> <?php _e( 'Priority support: get your feeds live faster', 'woo-product-feed-pro' );?></li>
                                                                <li><strong>2.</strong> <?php _e( 'More products approved by Google', 'woo-product-feed-pro' );?></li>
                                                                <li><strong>3.</strong> <?php _e( 'Add GTIN, brand and more fields to your store', 'woo-product-feed-pro' );?></li>
                                                                <li><strong>4.</strong> <?php _e( 'Exclude individual products from your feeds', 'woo-product-feed-pro' );?></li>
                                                                <li><strong>5.</strong> <?php _e( 'WPML support', 'woo-product-feed-pro' );?></li>
                                                                <li><strong>6.</strong> <?php _e( 'Aelia currency switcher support', 'woo-product-feed-pro');?></li>
                                                                <li><strong>7.</strong> <?php _e( 'Facebook pixel feature', 'woo-product-feed-pro');?></li>
                                                                <li><strong>8.</strong> <?php _e( 'Polylang support', 'woo-product-feed-pro');?></li>
							</ul>
                                                        <strong>
                                                        <a href="https://adtribes.io/pro-vs-elite/?utm_source=<?php print"$host";?>&utm_medium=manage-settings&utm_campaign=why-upgrade-box" target="_blank"><?php _e( 'Upgrade to Elite here!', 'woo-product-feed-pro' );?></a>
                                                        </strong>
                                                </td>
                                        </tr>
                                </table><br/>

                                <table class="woo-product-feed-pro-table">
                                        <tr>
                                                <td><strong><?php _e( 'We’ve got you covered!', 'woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <?php _e( 'Need assistance? Check out our', 'woo-product-feed-pro' );?>
                                                        <ul>
                                                                <li><strong><a href="https://adtribes.io/support/?utm_source=<?php print"$host";?>&utm_medium=manage-settings&utm_campaign=faq" target="_blank"><?php _e( 'Frequently Asked Questions', 'woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong><a href="https://www.youtube.com/channel/UCXp1NsK-G_w0XzkfHW-NZCw" target="_blank"><?php _e( 'YouTube tutorials', 'woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong><a href="https://adtribes.io/tutorials/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=tutorials" target="_blank"><?php _e( 'Tutorials', 'woo-product-feed-pro' );?></a></strong></li>
                                                        </ul>
                                                        <?php _e( 'Or just reach out to us at', 'woo-product-feed-pro' );?>  <strong><a href="https://wordpress.org/support/plugin/woo-product-feed-pro/" target="_blank"><?php _e( 'our Wordpress forum', 'woo-product-feed-pro' ); ?></a></strong> <?php _e( 'and we will make sure your product feeds will be up-and-running within no-time.', 'woo-product-feed-pro' );?>
                                                </td>
                                        </tr>
                                </table><br/>
	
                                <table class="woo-product-feed-pro-table">
                                        <tr>
                                                <td><strong><?php _e( 'Our latest tutorials', 'woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <ul>
                                                                <li><strong>1. <a href="https://adtribes.io/setting-up-your-first-google-shopping-product-feed/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=first shopping feed" target="_blank"><?php _e( 'Create a Google Shopping feed', 'woo-product-feed-pro' );?></a></strong></li>

								<li><strong>2. <a href="https://adtribes.io/feature-product-data-manipulation/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=product_data_manipulation" target="_blank"><?php _e( 'Product data manipulation','woo-product-feed-pro' );?></a></strong></li>

                                                                <li><strong>3. <a href="https://adtribes.io/how-to-create-filters-for-your-product-feed/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=how to create filters" target="_blank"><?php _e( 'How to create filters for your product feed', 'woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>4. <a href="https://adtribes.io/how-to-create-rules/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=how to create rules" target="_blank"><?php _e( 'How to set rules for your product feed', 'woo-product-feed-pro');?></a></strong></li>
                                                                <li><strong>5. <a href="https://adtribes.io/add-gtin-mpn-upc-ean-product-condition-optimised-title-and-brand-attributes/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=adding fields" target="_blank"><?php _e( 'Adding GTIN, Brand, MPN and more', 'woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>6. <a href="https://adtribes.io/woocommerce-structured-data-bug/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=structured data bug" target="_blank"><?php _e( 'WooCommerce structured data markup bug', 'woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>7. <a href="https://adtribes.io/wpml-support/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=wpml support" target="_blank"><?php _e( 'Enable WPML support', 'woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>8. <a href="https://adtribes.io/aelia-currency-switcher-feature/?utm_source=<?php print "$host";?>&utm_medium=manage-settings&utm_campaign=aelia support" target="_blank"><?php _e( 'Enable Aelia currency switcher support','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>9. <a href="https://adtribes.io/help-my-feed-processing-is-stuck/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=feed stuck" target="_blank"><?php _e( 'Help, my feed is stuck!','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>10. <a href="https://adtribes.io/help-i-have-none-or-less-products-in-my-product-feed-than-expected/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=too few products" target="_blank"><?php _e( 'Help, my feed has no or too few products!', 'woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>11. <a href="https://adtribes.io/polylang-support-product-feeds/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=polylang support" target="_blank"><?php _e( 'How to use the Polylang feature', 'woo-product-feed-pro' );?></a></strong></li>
						    </ul>
                                                </td>
                                        </tr>
                                </table><br/>
		
				</div>
			</div>
		</tbody>
	</div>
</div>
