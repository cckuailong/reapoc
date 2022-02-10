<?php
$my_currency = get_woocommerce_currency();
$aelia_currencies = apply_filters('wc_aelia_cs_enabled_currencies', $my_currency);
$host = $_SERVER['HTTP_HOST'];

/**
 * Change default footer text, asking to review our plugin
 **/
function my_footer_text($default) {
    return _e( 'If you like our <strong>WooCommerce Product Feed PRO</strong> plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-product-feed-pro/reviews?rate=5#new-post" target="_blank" class="woo-product-feed-pro-ratingRequest">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks in advance!','woo-product-feed-pro') ;
}
add_filter('admin_footer_text', 'my_footer_text');

/**
 * Create notification object and get message and message type as WooCommerce is inactive
 * also set variable allowed on 0 to disable submit button on step 1 of configuration
 */
$versions = array (
        "PHP" => (float)phpversion(),
        "Wordpress" => get_bloginfo('version'),
	"WooCommerce" => WC()->version,
        "WooCommerce Product Feed PRO" => WOOCOMMERCESEA_PLUGIN_VERSION
);

$license_information = get_option( 'license_information' );

$notifications_obj = new WooSEA_Get_Admin_Notifications;
if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $notifications_box = $notifications_obj->get_admin_notifications ( "9", "false" );
	$locale = "NL";
} else {
	$notifications_box = $notifications_obj->get_admin_notifications ( '0', 'false' );
	$default = wc_get_base_location();
	$locale = apply_filters( 'woocommerce_countries_base_country', $default['country'] );
}

if (!empty($license_information)){
	if($license_information['notice'] == "true"){
        	$notifications_box['message_type'] = $license_information['message_type'];
        	$notifications_box['message'] = $license_information['message'];
	}
}

if ($versions['PHP'] < 5.6){
        $notifications_box = $notifications_obj->get_admin_notifications ( '11', 'false' );
}

if (!wp_next_scheduled( 'woosea_cron_hook' ) ) {
        $notifications_box = $notifications_obj->get_admin_notifications ( '12', 'false' );
}

if ($versions['WooCommerce'] < 3){
        $notifications_box = $notifications_obj->get_admin_notifications ( '13', 'false' );
}

/**
 * Get shipping zones
 */
$shipping_zones = WC_Shipping_Zones::get_zones();
$nr_shipping_zones = count($shipping_zones);

/**
 * Get channels
 */
$channel_configs = get_option ('channel_statics');

/**
 * Get countries and channels
 */
$channel_obj = new WooSEA_Attributes;
$countries = $channel_obj->get_channel_countries();
$channels = $channel_obj->get_channels($locale);

if (array_key_exists('project_hash', $_GET)){
        $project = WooSEA_Update_Project::get_project_data($_GET['project_hash']);
	$manage_project = "yes";
}
?>

	<div class="wrap">
		<div class="woo-product-feed-pro-form-style-2">
				<?php
                                // Set default notification to show
                                $getelite_notice = get_option('woosea_getelite_notification');
                                if(empty($getelite_notice['show'])){
                                        $getelite_notice['show'] = "yes";
                                        $getelite_notice['timestamp'] = date( 'd-m-Y' );
                                }

                                if($getelite_notice['show'] <> "no"){
                                ?>
					<div class="notice notice-info get_elite is-dismissible"> 
                                                <p>
                                                <strong><?php _e( 'Would you like to get more out of your product feeds? Upgrade to the Elite version of the plugin and you will get:','woo-product-feed-pro' );?></strong><br/></br/>
                                                <span class="dashicons dashicons-yes"></span><?php _e( 'Priority support - we will help you to get your product feed(s) up-and-running;','woo-product-feed-pro' );?><br/>
                                                <span class="dashicons dashicons-yes"></span><?php _e( 'GTIN, Brand, MPN, EAN, Condition and more fields for your product feeds','woo-product-feed-pro' );?> [<a href="https://adtribes.io/add-gtin-mpn-upc-ean-product-condition-optimised-title-and-brand-attributes/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=adding%20fields" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
                                                <span class="dashicons dashicons-yes"></span><?php _e('Enhanched structured data on your product pages: more products approved in your Google Merchant Center','woo-product-feed-pro' );?> [<a href="https://adtribes.io/woocommerce-structured-data-bug/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=structured%20data%20bug" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
                                                <span class="dashicons dashicons-yes"></span><?php _e( 'Advanced product data manipulation','woo-product-feed-pro' );?> [<a href="https://adtribes.io/feature-product-data-manipulation/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=product%20data%20manipulation" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
                                                <span class="dashicons dashicons-yes"></span><?php _e( 'WPML support - including their currency switcher','woo-product-feed-pro' );?> [<a href="https://adtribes.io/wpml-support/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=wpml%20support" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
                                                <span class="dashicons dashicons-yes"></span><?php _e( 'Polylang support','woo-product-feed-pro' );?> [<a href="https://adtribes.io/polylang-support-product-feeds/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=polylang%20support" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/>
                                                <span class="dashicons dashicons-yes"></span>Aelia currency switcher support [<a href="https://adtribes.io/aelia-currency-switcher-feature/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=aelia%20support" target="_blank">Read more</a>];<br/>
                                                <span class="dashicons dashicons-yes"></span><?php _e( 'Facebook pixel feature','woo-product-feed-pro' );?> [<a href="https://adtribes.io/facebook-pixel-feature/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=facebook pixel feature" target="_blank"><?php _e( 'Read more','woo-product-feed-pro' );?></a>];<br/><br/>
                                                <?php _e( 'Upgrade to the','woo-product-feed-pro' );?> <strong><a href="https://adtribes.io/pro-vs-elite/?utm_source=<?php print"$host";?>&utm_medium=manage-feed&utm_campaign=top-notification&utm_content=notification" target="_blank"><?php _e( 'Elite version of our plugin</a></strong> to get all these features.','woo-product-feed-pro' );?>
                                                </p>
                                        </div> 
				<?php
				}
				?>
		<div class="woo-product-feed-pro-form-style-2-heading"><?php _e( 'General feed settings','woo-product-feed-pro' );?></div>
       
		<form action="" id="myForm" method="post" name="myForm">
		
              	<div class="woo-product-feed-pro-table-wrapper">
                <div class="woo-product-feed-pro-table-left">

		<table class="woo-product-feed-pro-table">
			<tbody class="woo-product-feed-pro-body">
				<div id="projecterror"></div>
				<tr>
					<td width="30%"><span><?php _e( 'Project name','woo-product-feed-pro' );?>:<span class="required">*</span></span></td>
					<td>
						<div style="display: block;">
							<?php
							if (isset($project)){
								print"<input type=\"text\" class=\"input-field\" id=\"projectname\" name=\"projectname\" value=\"$project[projectname]\"/> <div id=\"projecterror\"></div>";
							} else {
								print"<input type=\"text\" class=\"input-field\" id=\"projectname\" name=\"projectname\"/> <div id=\"projecterror\"></div>";
							}
							?>
						</div>
					</td>
				</tr>
				<?php
                                $add_aelia_support = get_option ('add_aelia_support');
                                if($add_aelia_support == "yes"){
					if ((is_array($aelia_currencies)) AND (count($aelia_currencies) > 0)){
						 if (isset($manage_project)){
							print "<tr>";
							print "	<td><span>Aelia Currency:</span></td>";
							print "	<td>";
							print "	<select name=\"AELIA\" class=\"aelia_switch\">";
								foreach ($aelia_currencies as $key => $value){
									if(isset($project['AELIA'])){	
										if($value == $project['AELIA']){
											print "<option value=\"$value\" selected>$value</option>";
										} else {
											print "<option value=\"$value\">$value</option>";
										}
									} else {
										print "<option value=\"$value\">$value</option>";
									}
								}
							print "</select>";
							print "<input type=\"hidden\" name=\"base_currency\" value=\"$my_currency\">";
							print "</td>";
							print "</tr>";
						} else {
							print "<tr>";
							print "	<td><span>Aelia Currency:</span></td>";
							print "	<td>";
							print "	<select name=\"AELIA\">";
							foreach ($aelia_currencies as $key => $value){
								if($value == $my_currency){
									print "<option value=\"$value\" selected>$value</option>";
								} else {
									print "<option value=\"$value\">$value</option>";
								}
							}
							print "</select>";
							print "<input type=\"hidden\" name=\"base_currency\" value=\"$my_currency\">";
							print "</td>";
							print "</tr>";
						}
					}
				}
				
				if ( ( is_plugin_active('sitepress-multilingual-cms') ) OR ( function_exists('icl_object_id') ) ){

					// This is WPML
                        		if( !class_exists( 'Polylang' ) ) {
                                 		$add_wpml_support = get_option ('add_wpml_support');
                                     		if($add_wpml_support == "yes"){
							// Adding WPML support here
							$my_current_lang = apply_filters( 'wpml_current_language', NULL );

							global $sitepress;
       		 					$list_lang = $sitepress->get_active_languages();
							$nr_lang = count($list_lang);

							$wcml_currencies = array();
							// Check if WCML plugin is active
							if ( function_exists('wcml_loader') ) {
								$wcml_settings = get_option('_wcml_settings');
								$currencies = $wcml_settings['currency_options'];

								foreach ($currencies as $cur_key => $cur_val){
									array_push($wcml_currencies, $cur_key);
								}
							}

							if($nr_lang > 0){
                                		     		if (isset($manage_project)){
									print "<tr>";
									print "<td><span>WPML Language:</span></td>";
									print "<td>";
									print "<select name=\"WPML\" disabled>";
									foreach ($list_lang as $key => $value){
										if($key == $project['WPML']){
											print "<option value=\"$key\" selected>$value[english_name]</option>";
										} else {
											print "<option value=\"$key\">$value[english_name]</option>";
										}
									}
									print "</select>";
									print "</td>";
									print "</tr>";

									if((count($wcml_currencies) > 0) AND ($wcml_settings['enable_multi_currency'] > 0)){
										print "<tr>";
										print "<td><span>WCML Currency:</span></td>";
										print "<td>";
										print "<select name=\"WCML\" disabled>";
										foreach ($wcml_currencies as $key => $value){
											if($value == $project['WCML']){
												print "<option value=\"$value\" selected>$value</option>";
											} else {
												print "<option value=\"$value\">$value</option>";
											}
										}
										print "</select>";
										print "</td>";
										print "</tr>";
									}
								} else {
									print "<tr>";
									print "<td><span>WPML Language:</span></td>";
									print "<td>";
									print "<select name=\"WPML\">";
									foreach ($list_lang as $key => $value){
										if($key == $my_current_lang){
											print "<option value=\"$key\" selected>$value[english_name]</option>";
										} else {
											print "<option value=\"$key\">$value[english_name]</option>";
										}
									}
									print "</select>";
									print "</td>";
									print "</tr>";

									if((count($wcml_currencies) > 0) AND ($wcml_settings['enable_multi_currency'] > 0)){
										$my_currency = get_woocommerce_currency();
										print "<tr>";
										print "<td><span>WCML Currency:</span></td>";
										print "<td>";
										print "<select name=\"WCML\">";
										foreach ($wcml_currencies as $key => $value){
											if($value == $my_currency){
												print "<option value=\"$value\" selected>$value</option>";
											} else {
												print "<option value=\"$value\">$value</option>";
											}
										}
										print "</select>";
										print "</td>";
										print "</tr>";
									}
								}
							}
						}
					}
				}
				?>
				<tr>
					<td><span><?php _e( 'Country','woo-product-feed-pro' );?>:</span></td>
					<td>
						<?php
						if (isset($manage_project)){
							//print"<select name=\"countries\" id=\"countries\" class=\"select-field\" disabled>";
							print"<select name=\"countries\" id=\"countries\" class=\"select-field\">";
						} else {
							print"<select name=\"countries\" id=\"countries\" class=\"select-field\">";
						}
						?>
						<option><?php _e( 'Select a country','woo-product-feed-pro' );?></option>
						<?php
							foreach ($countries as $value){
								if((isset($project)) AND ($value == $project['countries'])){
									print "<option value=\"$value\" selected>$value</option>";
								} else {
									print "<option value=\"$value\">$value</option>";
								}
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td><span><?php _e( 'Channel','woo-product-feed-pro' );?>:</span></td>
					<td>
						<?php
						if (isset($manage_project)){
							print "<select name=\"channel_hash\" id=\"channel_hash\" class=\"select-field\" disabled>";
							print "<option value=\"$project[channel_hash]\" selected>$project[name]</option>";
							print "</select>";
						} else {
							$customfeed = "";
							$advertising = "";
							$marketplace = "";
							$shopping = "";
							$optgroup_customfeed = 0;
							$optgroup_advertising = 0;
							$optgroup_marketplace = 0;
							$optgroup_shopping = 0;

							print "<select name=\"channel_hash\" id=\"channel_hash\" class=\"select-field\">";

							foreach ($channels as $key=>$val){
								if ($val['type'] == "Custom Feed"){
									if ($optgroup_customfeed == 1){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$customfeed .= "<option value=\"$val[channel_hash]\" selected>$key</option>";
										} else {
											$customfeed .= "<option value=\"$val[channel_hash]\">$key</option>";
										}	
									} else {	
										$customfeed =  "<optgroup label=\"Custom Feed\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$customfeed .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$customfeed .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}									
										$optgroup_customfeed = 1;
									}
								}

								if ($val['type'] == "Advertising"){
									if ($optgroup_advertising == 1){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$advertising .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$advertising .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
									} else {	
										$advertising = "<optgroup label=\"Advertising\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$advertising .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$advertising .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
										$optgroup_advertising = 1;
									}
								}
	
								if ($val['type'] == "Marketplace"){
									if ($optgroup_marketplace == 1){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$marketplace .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$marketplace .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
									} else {	
										$marketplace = "<optgroup label=\"Marketplace\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$marketplace .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$marketplace .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
										$optgroup_marketplace = 1;
									}
								}

								if ($val['type'] == "Comparison shopping engine"){
									if ($optgroup_shopping == 0){
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$shopping .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										} else {
											$shopping .= "<option value=\"$val[channel_hash]\">$key</option>";	
										}
									} else {	
										$shopping = "<optgroup label=\"Comparison Shopping Engine\">";
										if((isset($project)) AND ($val['channel_hash'] == $project['channel_hash'])){
											$shopping .= "<option value=\"$val[channel_hash]\">$key</option>";	
										} else {
											$shopping .= "<option value=\"$val[channel_hash]\" selected>$key</option>";	
										}
										$optgroup_shopping = 1;
									}
								}
							}
							print "$customfeed";
							print "$advertising";
							print "$marketplace";
							print "$shopping";
							print "</select>";
						}
						?>
					</td>
				</tr>
				<tr id="product_variations">
					<td><span><?php _e('Include product variations','woo-product-feed-pro' );?>:</span></td>
					<td>
                                                <label class="woo-product-feed-pro-switch">
                                                        <?php
                                                        if(isset($project['product_variations'])){
                                                                print "<input type=\"checkbox\" id=\"variations\" name=\"product_variations\" class=\"checkbox-field\" checked>";
                                                        } else {
                                                                print "<input type=\"checkbox\" id=\"variations\" name=\"product_variations\" class=\"checkbox-field\">";
                                                        }
                                                        ?>
                                                        <div class="woo-product-feed-pro-slider round"></div>
                                                </label>
					</td>
				</tr>
				<tr id="default_variation">
					<td><span><?php _e( 'And only include default product variation','woo-product-feed-pro' );?>:</span></td>
					<td>
                                                <label class="woo-product-feed-pro-switch">
                                                        <?php
                                                        if(isset($project['default_variations'])){
                                                                print "<input type=\"checkbox\" id=\"default_variations\" name=\"default_variations\" class=\"checkbox-field\" checked>";
                                                        } else {
                                                                print "<input type=\"checkbox\" id=\"default_variations\" name=\"default_variations\" class=\"checkbox-field\">";
                                                        }
                                                        ?>
                                                        <div class="woo-product-feed-pro-slider round"></div>
                                                </label>
					</td>
				</tr>
				<tr id="lowest_price_variation">
					<td><span><?php _e( 'And only include lowest priced product variation(s)','woo-product-feed-pro' );?>:</span></td>
					<td>
                                                <label class="woo-product-feed-pro-switch">
                                                        <?php
                                                        if(isset($project['lowest_price_variations'])){
                                                                print "<input type=\"checkbox\" id=\"lowest_price_variations\" name=\"lowest_price_variations\" class=\"checkbox-field\" checked>";
                                                        } else {
                                                                print "<input type=\"checkbox\" id=\"lowest_price_variations\" name=\"lowest_price_variations\" class=\"checkbox-field\">";
                                                        }
                                                        ?>
                                                        <div class="woo-product-feed-pro-slider round"></div>
                                                </label>
					</td>
				</tr>
				<tr id="file">
					<td><span><?php _e( 'File format','woo-product-feed-pro' );?>:</span></td>
					<td>
						<select name="fileformat" id="fileformat" class="select-field">
							<?php
							$format_arr = array("xml","csv","txt","tsv");
							foreach ($format_arr as $format){
								$format_upper = strtoupper($format);
								if ((isset($project)) AND ($format == $project['fileformat'])){
									print "<option value=\"$format\" selected>$format_upper</option>";
								} else {
									print "<option value=\"$format\">$format_upper</option>";
								}
							}	
							?>
						</select>
					</td>
				</tr>
				<tr id="delimiter">
					<td><span><?php _e( 'Delimiter','woo-product-feed-pro' );?>:</span></td>
					<td>
						<select name="delimiter" class="select-field">
							<?php
							$delimiter_arr = array(",","|",";","tab","#");
							foreach ($delimiter_arr as $delimiter){
								if((isset($project)) AND (array_key_exists('delimiter', $project)) AND ($delimiter == $project['delimiter'])){
									print "<option value=\"$delimiter\" selected>$delimiter</option>";
								} else {
									print "<option value=\"$delimiter\">$delimiter</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><span><?php _e( 'Refresh interval','woo-product-feed-pro' );?>:</span></td>
					<td>
						<select name="cron" class="select-field">
							<?php
							$refresh_arr = array("daily","twicedaily","hourly","no refresh");
							foreach ($refresh_arr as $refresh){
								$refresh_upper = ucfirst($refresh);
								if ((isset($project)) AND ($refresh == $project['cron'])){
									print "<option value=\"$refresh\" selected>$refresh_upper</option>";
								} else {
									print "<option value=\"$refresh\">$refresh_upper</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
		
				<tr>
					<td><span><?php _e( 'Refresh only when products changed','woo-product-feed-pro' );?>:</span></td>
					<td>
						<?php
						if((isset($project)) AND (array_key_exists('products_changed',$project))){
							print "<input name=\"products_changed\" type=\"checkbox\" class=\"checkbox-field\" checked>";
						} else {
							print "<input name=\"products_changed\" type=\"checkbox\" class=\"checkbox-field\">";
						}
						?>
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<?php
						if (isset($project)){
							print "<input type=\"hidden\" name=\"project_hash\" id=\"project_hash\" value=\"$project[project_hash]\" />";
							print "<input type=\"hidden\" name=\"channel_hash\" id=\"channel_hash\" value=\"$project[channel_hash]\" />";
							print "<input type=\"hidden\" name=\"project_update\" id=\"project_update\" value=\"yes\" />";
							print "<input type=\"hidden\" name=\"step\" id=\"step\" value=\"100\" />";
							print "<input type=\"submit\" id=\"goforit\" value=\"Save\" />";
					
						} else {
							print "<input type=\"hidden\" name=\"step\" id=\"step\" value=\"99\" />";
							print "<input type=\"submit\" id=\"goforit\" value=\"Save & continue\" />";
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
                        <div class="woo-product-feed-pro-table-right">

                                <table class="woo-product-feed-pro-table">
                                        <tr>    
                                                <td><strong><?php _e( 'Why upgrade to Elite?','woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <?php _e( 'Enjoy all priviliges of our Elite features and priority support and upgrade to the Elite version of our plugin now!','woo-product-feed-pro' );?>
                                                        <ul>
                                                                <li><strong>1.</strong> <?php _e( 'Priority support: get your feeds live faster','woo-product-feed-pro' );?></li>
                                                                <li><strong>2.</strong> <?php _e( 'More products approved by Google','woo-product-feed-pro' );?></li>
                                                                <li><strong>3.</strong> <?php _e( 'Add GTIN, brand and more fields to your store','woo-product-feed-pro' );?></li>
                                                                <li><strong>4.</strong> <?php _e( 'Exclude individual products from your feeds','woo-product-feed-pro' );?></li>
                                                                <li><strong>5.</strong> <?php _e( 'WPML support','woo-product-feed-pro' );?></li>
                                                                <li><strong>6.</strong> <?php _e( 'Aelia currency switcher support','woo-product-feed-pro' );?></li>
                                                                <li><strong>7.</strong> <?php _e( 'Facebook pixel feature','woo-product-feed-pro' );?></li>
                                                                <li><strong>8.</strong> <?php _e( 'Polylang support','woo-product-feed-pro' );?></li>
							 </ul>
                                                        <strong>
                                                        <a href="https://adtribes.io/pro-vs-elite/?utm_source=<?php print"$host";?>&utm_medium=page-0&utm_campaign=why-upgrade-box" target="_blank"><?php _e( 'Upgrade to Elite here!','woo-product-feed-pro' );?></a>
                                                        </strong>
                                                </td>
                                        </tr>
                                </table><br/>

                                <table class="woo-product-feed-pro-table">
                                        <tr>    
                                                <td><strong><?php _e( 'We’ve got you covered!','woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <?php _e( 'Need assistance? Check out our:','woo-product-feed-pro' );?>
                                                        <ul>
                                                                <li><strong><a href="https://adtribes.io/support/?utm_source=<?php print"$host";?>&utm_medium=page-0&utm_campaign=faq" target="_blank"><?php _e( 'Frequently Asked Questions','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong><a href="https://www.youtube.com/channel/UCXp1NsK-G_w0XzkfHW-NZCw" target="_blank"><?php _e( 'YouTube tutorials','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong><a href="https://adtribes.io/tutorials/?utm_source=<?php print "$host";?>&utm_medium=page-0&utm_campaign=tutorials" target="_blank"><?php _e( 'Tutorials','woo-product-feed-pro' );?></a></strong></li>
                                                        </ul>
                                                        <?php _e( 'Or just reach out to us at','woo-product-feed-pro' );?>  <strong><a href="https://wordpress.org/support/plugin/woo-product-feed-pro/" target="_blank"><?php _e( 'our Wordpress forum','woo-product-feed-pro' );?></a></strong> <?php _e( 'and we will make sure your product feeds will be up-and-running within no-time.','woo-product-feed-pro' );?>
                                                </td>
                                        </tr>
                                </table><br/>

                                <table class="woo-product-feed-pro-table">
                                        <tr>
                                                <td><strong><?php _e( 'Our latest tutorials','woo-product-feed-pro' );?></strong></td>
                                        </tr>
                                        <tr>
                                                <td>
                                                        <ul>
                                                                <li><strong>1. <a href="https://adtribes.io/setting-up-your-first-google-shopping-product-feed/?utm_source=<?php print "$host";?>&utm_medium=page0&utm_campaign=first shopping feed" target="_blank"><?php _e( 'Create a Google Shopping feed','woo-product-feed-pro' );?></a></strong></li>
                                                             	<li><strong>2. <a href="https://adtribes.io/feature-product-data-manipulation/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=product_data_manipulation" target="_blank"><?php _e( 'Product data manipulation','woo-product-feed-pro' );?></a></strong></li>
								<li><strong>3. <a href="https://adtribes.io/how-to-create-filters-for-your-product-feed/?utm_source=<?php print "$host";?>&utm_medium=page0&utm_campaign=how to create filters" target="_blank"><?php _e( 'How to create filters for your product feed','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>4. <a href="https://adtribes.io/how-to-create-rules/?utm_source=<?php print "$host";?>&utm_medium=page0&utm_campaign=how to create rules" target="_blank"><?php _e('How to set rules for your product feed','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>5. <a href="https://adtribes.io/add-gtin-mpn-upc-ean-product-condition-optimised-title-and-brand-attributes/?utm_source=<?php print "$host";?>&utm_medium=page0&utm_campaign=adding fields" target="_blank"><?php _e( 'Adding GTIN, Brand, MPN and more','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>6. <a href="https://adtribes.io/woocommerce-structured-data-bug/?utm_source=<?php print "$host";?>&utm_medium=page0&utm_campaign=structured data bug" target="_blank"><?php _e('WooCommerce structured data markup bug','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>7. <a href="https://adtribes.io/wpml-support/?utm_source=<?php print "$host";?>&utm_medium=page0&utm_campaign=wpml support" target="_blank"><?php _e( 'Enable WPML support','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>8. <a href="https://adtribes.io/aelia-currency-switcher-feature/?utm_source=<?php print "$host";?>&utm_medium=page0&utm_campaign=aelia support" target="_blank"><?php _e( 'Enable Aelia currency switcher support','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>9. <a href="https://adtribes.io/help-my-feed-processing-is-stuck/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=feed stuck" target="_blank"><?php _e( 'Help, my feed is stuck!','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>10. <a href="https://adtribes.io/help-i-have-none-or-less-products-in-my-product-feed-than-expected/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=too few products" target="_blank"><?php _e( 'Help, my feed has no or too few products!','woo-product-feed-pro' );?></a></strong></li>
                                                                <li><strong>11. <a href="https://adtribes.io/polylang-support-product-feeds/?utm_source=<?php print "$host";?>&utm_medium=manage-feed&utm_campaign=polylang support" target="_blank"><?php _e( 'How to use the Polylang feature', 'woo-product-feed-pro' );?></a></strong></li>
                                                        </ul>
                                                </td>
                                        </tr>
                                </table><br/>
                        </div>
                        </div>
		</form>
		</div>
	</div>
