<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


		if (isset($_POST['update'])) {
			
			$post_id = intval($_GET['product']);
		
			// check nonce for security
			$nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $nonce, 'edit_'.$post_id ) ) {
				echo "Nonce verification failed.";
				exit;
			}
			
			if (!$post_id) {
				echo'<script>window.location="admin.php?page=wpedon_buttons"; </script>';
				exit;
			}
			
			// Update data
			
			if (!isset($error)) {
			
				$my_post = array(
				'ID'           => $post_id,
				'post_title'   => sanitize_text_field($_POST['wpedon_button_name'])
				);
				wp_update_post($my_post);
				
				
				$wpedon_button_price = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_price'], 'post' );
				update_post_meta($post_id, 'wpedon_button_price', $wpedon_button_price);
				
				update_post_meta($post_id, 'wpedon_button_id', sanitize_text_field($_POST['wpedon_button_id']));

                $wpedon_button_enable_name = !empty($_POST['wpedon_button_enable_name']) ? sanitize_text_field($_POST['wpedon_button_enable_name']) : 0;
                update_post_meta($post_id, 'wpedon_button_enable_name', $wpedon_button_enable_name);

                $wpedon_button_enable_price = !empty($_POST['wpedon_button_enable_price']) ? sanitize_text_field($_POST['wpedon_button_enable_price']) : 0;
                update_post_meta($post_id, 'wpedon_button_enable_price', $wpedon_button_enable_price);

                $wpedon_button_enable_currency = !empty($_POST['wpedon_button_enable_currency']) ? intval($_POST['wpedon_button_enable_currency']) : 0;
                update_post_meta($post_id, 'wpedon_button_enable_currency', $wpedon_button_enable_currency);
				
				$wpedon_button_currency =			intval($_POST['wpedon_button_currency']);
				if (!$wpedon_button_currency) { 	$wpedon_button_currency = ""; }
				update_post_meta($post_id, 'wpedon_button_currency', $wpedon_button_currency);	
				
				$wpedon_button_language =			intval($_POST['wpedon_button_language']);
				if (!$wpedon_button_language) { 	$wpedon_button_language = ""; }
				update_post_meta($post_id, 'wpedon_button_language', $wpedon_button_language);	
				
				$wpedon_button_buttonsize =			intval($_POST['wpedon_button_buttonsize']);
				if (!$wpedon_button_buttonsize && $wpedon_button_buttonsize != "0") { 	$wpedon_button_buttonsize = ""; }
				update_post_meta($post_id, 'wpedon_button_buttonsize', $wpedon_button_buttonsize);
				
				update_post_meta($post_id, 'wpedon_button_account', sanitize_text_field($_POST['wpedon_button_account']));
				update_post_meta($post_id, 'wpedon_button_return', sanitize_text_field($_POST['wpedon_button_return']));
				
				update_post_meta($post_id, 'wpedon_button_scpriceprice', sanitize_text_field($_POST['wpedon_button_scpriceprice']));
				update_post_meta($post_id, 'wpedon_button_scpriceaname', sanitize_text_field($_POST['wpedon_button_scpriceaname']));
				update_post_meta($post_id, 'wpedon_button_scpricebname', sanitize_text_field($_POST['wpedon_button_scpricebname']));
				update_post_meta($post_id, 'wpedon_button_scpricecname', sanitize_text_field($_POST['wpedon_button_scpricecname']));
				update_post_meta($post_id, 'wpedon_button_scpricedname', sanitize_text_field($_POST['wpedon_button_scpricedname']));
				update_post_meta($post_id, 'wpedon_button_scpriceename', sanitize_text_field($_POST['wpedon_button_scpriceename']));
				update_post_meta($post_id, 'wpedon_button_scpricefname', sanitize_text_field($_POST['wpedon_button_scpricefname']));
				update_post_meta($post_id, 'wpedon_button_scpricegname', sanitize_text_field($_POST['wpedon_button_scpricegname']));
				update_post_meta($post_id, 'wpedon_button_scpricehname', sanitize_text_field($_POST['wpedon_button_scpricehname']));
				update_post_meta($post_id, 'wpedon_button_scpriceiname', sanitize_text_field($_POST['wpedon_button_scpriceiname']));
				update_post_meta($post_id, 'wpedon_button_scpricejname', sanitize_text_field($_POST['wpedon_button_scpricejname']));
				
				$wpedon_button_scpricea = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpricea'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpricea', $wpedon_button_scpricea);
				$wpedon_button_scpriceb = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpriceb'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpriceb', $wpedon_button_scpriceb);
				$wpedon_button_scpricec = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpricec'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpricec', $wpedon_button_scpricec);
				$wpedon_button_scpriced = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpriced'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpriced', $wpedon_button_scpriced);
				$wpedon_button_scpricee = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpricee'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpricee', $wpedon_button_scpricee);
				$wpedon_button_scpricef = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpricef'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpricef', $wpedon_button_scpricef);
				$wpedon_button_scpriceg = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpriceg'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpriceg', $wpedon_button_scpriceg);
				$wpedon_button_scpriceh = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpriceh'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpriceh', $wpedon_button_scpriceh);
				$wpedon_button_scpricei = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpricei'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpricei', $wpedon_button_scpricei);
				$wpedon_button_scpricej = sanitize_meta( 'currency_wpedon', $_POST['wpedon_button_scpricej'], 'post' );
				update_post_meta($post_id, 'wpedon_button_scpricej', $wpedon_button_scpricej);	
				
				$message = "Saved";
				
			}
		}
			
			
		// check nonce for security
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'edit_'.$post_id ) ) {
			echo "Nonce verification failed.";
			exit;
		}
		
		?>
		
		<div style="width:98%;">
			
			<form method='post'>
			
				<?php
				$post_id = intval($_GET['product']);
				
				$post_data = get_post($post_id);
				$title = $post_data->post_title;
				
				$siteurl = get_site_url();
				?>

				<table width="100%"><tr><td valign="bottom" width="85%">
					<br />
					<span style="font-size:20pt;">Edit PayPal Donation Button</span>
					</td><td valign="bottom">
					<input type="submit" class="button-primary" style="font-size: 14px;height: 30px;float: right;" value="Save PayPal Donation Button">
					</td><td valign="bottom">
					<a href="admin.php?page=wpedon_buttons" class="button-secondary" style="font-size: 14px;height: 30px;float: right;">View All Donation Buttons</a>
				</td></tr></table>

				<?php
				// error
				if (isset($error) && isset($message)) {
					echo "<div class='error'><p>"; echo esc_html($message); echo"</p></div>";
				}
				// saved
				if (!isset($error) && isset($message)) {
					echo "<div class='updated'><p>"; echo esc_html($message); echo"</p></div>";
				}
				?>
				
				<br />
				
				<div style="background-color:#fff;padding:8px;border: 1px solid #CCCCCC;"><br />
				
					<table><tr><td>
					
						<b>Shortcode</b> </td><td></td></td></td></tr><tr><td>
						Shortcode: </td><td><input type="text" readonly="true" value="<?php echo "[wpedon id=$post_id]"; ?>"></td><td>Put this in a page, post, PayPal widget, or <a target="_blank" href="https://wpplugin.org/documentation/?document=2314">in your theme</a>, to show the PayPal button on your site. <br />You can also use the button inserter found above the page or post editor.</td></tr><tr><td>
						</td><td><br /></td></td></td></tr><tr><td>
						
						<b>Main</b> </td><td></td></td></td></tr><tr><td>
						Purpose / Name: </td><td><input type="text" name="wpedon_button_name" value="<?php echo esc_attr($title); ?>"></td><td> Optional - The purpose of the donation. If blank, customer enters purpose.</td></tr><tr><td>
						Donation Amount: </td><td><input type="text" name="wpedon_button_price" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_price',true)); ?>"></td><td> Optional - Example: 10.50. If blank, customer enters amount on PayPal page. If using dropdown prices, leave blank.</td></tr><tr><td>
						Donation ID: </td><td><input type="text" name="wpedon_button_id" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_id',true)); ?>"></td><td> Optional - Example: S12T-Gec-RS.</td></tr><tr><td>
						
						</td><td><br /></td></td></td></tr><tr><td>
						<b>Language & Currency</b> </td><td></td></td></td></tr><tr><td>
						
						</td><td><br /></td></td></td></tr><tr><td>
						Language: </td><td>
						<select name="wpedon_button_language" style="width: 190px">
                            <?php $wpedon_button_language = get_post_meta($post_id,'wpedon_button_language',true); ?>
							<option <?php if($wpedon_button_language == "0") { echo "SELECTED"; } ?> value="0">Default Language</option>
							<option <?php if($wpedon_button_language == "1") { echo "SELECTED"; } ?> value="1">Danish</option>
							<option <?php if($wpedon_button_language == "2") { echo "SELECTED"; } ?> value="2">Dutch</option>
							<option <?php if($wpedon_button_language == "3") { echo "SELECTED"; } ?> value="3">English</option>
							<option <?php if($wpedon_button_language == "20") { echo "SELECTED"; } ?> value="20">English - UK</option>
							<option <?php if($wpedon_button_language == "4") { echo "SELECTED"; } ?> value="4">French</option>
							<option <?php if($wpedon_button_language == "5") { echo "SELECTED"; } ?> value="5">German</option>
							<option <?php if($wpedon_button_language == "6") { echo "SELECTED"; } ?> value="6">Hebrew</option>
							<option <?php if($wpedon_button_language == "7") { echo "SELECTED"; } ?> value="7">Italian</option>
							<option <?php if($wpedon_button_language == "8") { echo "SELECTED"; } ?> value="8">Japanese</option>
							<option <?php if($wpedon_button_language == "9") { echo "SELECTED"; } ?> value="9">Norwgian</option>
							<option <?php if($wpedon_button_language == "10") { echo "SELECTED"; } ?> value="10">Polish</option>
							<option <?php if($wpedon_button_language == "11") { echo "SELECTED"; } ?> value="11">Portuguese</option>
							<option <?php if($wpedon_button_language == "12") { echo "SELECTED"; } ?> value="12">Russian</option>
							<option <?php if($wpedon_button_language == "13") { echo "SELECTED"; } ?> value="13">Spanish</option>
							<option <?php if($wpedon_button_language == "14") { echo "SELECTED"; } ?> value="14">Swedish</option>
							<option <?php if($wpedon_button_language == "15") { echo "SELECTED"; } ?> value="15">Simplified Chinese -China only</option>
							<option <?php if($wpedon_button_language == "16") { echo "SELECTED"; } ?> value="16">Traditional Chinese - Hong Kong only</option>
							<option <?php if($wpedon_button_language == "17") { echo "SELECTED"; } ?> value="17">Traditional Chinese - Taiwan only</option>
							<option <?php if($wpedon_button_language == "18") { echo "SELECTED"; } ?> value="18">Turkish</option>
							<option <?php if($wpedon_button_language == "19") { echo "SELECTED"; } ?> value="19">Thai</option>
						</select></td><td>Optional - Will override setttings page value.</td></td></td></tr><tr><td>
						
						</td><td><br /></td></td></td></tr><tr><td>
						Currency: </td><td>
						<select name="wpedon_button_currency" style="width: 190px">
                            <?php $wpedon_button_currency = get_post_meta($post_id,'wpedon_button_currency',true); ?>
							<option <?php if($wpedon_button_currency == "0") { echo "SELECTED"; } ?> value="0">Default Currency</option>
							<option <?php if($wpedon_button_currency == "1") { echo "SELECTED"; } ?> value="1">Australian Dollar - AUD</option>
							<option <?php if($wpedon_button_currency == "2") { echo "SELECTED"; } ?> value="2">Brazilian Real - BRL</option>
							<option <?php if($wpedon_button_currency == "3") { echo "SELECTED"; } ?> value="3">Canadian Dollar - CAD</option>
							<option <?php if($wpedon_button_currency == "4") { echo "SELECTED"; } ?> value="4">Czech Koruna - CZK</option>
							<option <?php if($wpedon_button_currency == "5") { echo "SELECTED"; } ?> value="5">Danish Krone - DKK</option>
							<option <?php if($wpedon_button_currency == "6") { echo "SELECTED"; } ?> value="6">Euro - EUR</option>
							<option <?php if($wpedon_button_currency == "7") { echo "SELECTED"; } ?> value="7">Hong Kong Dollar - HKD</option>
							<option <?php if($wpedon_button_currency == "8") { echo "SELECTED"; } ?> value="8">Hungarian Forint - HUF</option>
							<option <?php if($wpedon_button_currency == "9") { echo "SELECTED"; } ?> value="9">Israeli New Sheqel - ILS</option>
							<option <?php if($wpedon_button_currency == "10") { echo "SELECTED"; } ?> value="10">Japanese Yen - JPY</option>
							<option <?php if($wpedon_button_currency == "11") { echo "SELECTED"; } ?> value="11">Malaysian Ringgit - MYR</option>
							<option <?php if($wpedon_button_currency == "12") { echo "SELECTED"; } ?> value="12">Mexican Peso - MXN</option>
							<option <?php if($wpedon_button_currency == "13") { echo "SELECTED"; } ?> value="13">Norwegian Krone - NOK</option>
							<option <?php if($wpedon_button_currency == "14") { echo "SELECTED"; } ?> value="14">New Zealand Dollar - NZD</option>
							<option <?php if($wpedon_button_currency == "15") { echo "SELECTED"; } ?> value="15">Philippine Peso - PHP</option>
							<option <?php if($wpedon_button_currency == "16") { echo "SELECTED"; } ?> value="16">Polish Zloty - PLN</option>
							<option <?php if($wpedon_button_currency == "17") { echo "SELECTED"; } ?> value="17">Pound Sterling - GBP</option>
							<option <?php if($wpedon_button_currency == "18") { echo "SELECTED"; } ?> value="18">Russian Ruble - RUB</option>
							<option <?php if($wpedon_button_currency == "19") { echo "SELECTED"; } ?> value="19">Singapore Dollar - SGD</option>
							<option <?php if($wpedon_button_currency == "20") { echo "SELECTED"; } ?> value="20">Swedish Krona - SEK</option>
							<option <?php if($wpedon_button_currency == "21") { echo "SELECTED"; } ?> value="21">Swiss Franc - CHF</option>
							<option <?php if($wpedon_button_currency == "22") { echo "SELECTED"; } ?> value="22">Taiwan New Dollar - TWD</option>
							<option <?php if($wpedon_button_currency == "23") { echo "SELECTED"; } ?> value="23">Thai Baht - THB</option>
							<option <?php if($wpedon_button_currency == "24") { echo "SELECTED"; } ?> value="24">Turkish Lira - TRY</option>
							<option <?php if($wpedon_button_currency == "25") { echo "SELECTED"; } ?> value="25">U.S. Dollar - USD</option>
						</select></td><td>Optional - Will override setttings page value.</td></td></td></tr><tr><td>
						
						</td><td><br /></td></td></td></tr><tr><td>
						<b>Other</b> </td><td></td></td></td></tr><tr><td>
						PayPal Account: </td><td><input type="text" name="wpedon_button_account" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_account',true)); ?>"></td><td> Optional - Will override setttings page value.</td></tr><tr><td>
						Return URL: </td><td><input type="text" name="wpedon_button_return" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_return',true)); ?>"></td><td> Optional - Will override setttings page value. <br />Example: <?php echo $siteurl; ?>/thankyou</td></tr><tr><td>
						
						Button Size: </td><td>
						<select name="wpedon_button_buttonsize" style="width:190px;">
                            <?php $wpedon_button_buttonsize = get_post_meta($post_id,'wpedon_button_buttonsize',true); ?>
							<option value="0" <?php if($wpedon_button_buttonsize == "0") { echo "SELECTED"; } ?>>Default Button</option>
							<option value="1" <?php if($wpedon_button_buttonsize == "1") { echo "SELECTED"; } ?>>Small</option>
							<option value="2" <?php if($wpedon_button_buttonsize == "2") { echo "SELECTED"; } ?>>Big</option>
							<option value="3" <?php if($wpedon_button_buttonsize == "3") { echo "SELECTED"; } ?>>Big with Credit Cards</option>
							<option value="4" <?php if($wpedon_button_buttonsize == "4") { echo "SELECTED"; } ?>>Small 2 (English only)</option>
							<option value="5" <?php if($wpedon_button_buttonsize == "5") { echo "SELECTED"; } ?>>Big 2 (English only)</option>
							<option value="6" <?php if($wpedon_button_buttonsize == "6") { echo "SELECTED"; } ?>>Big 2 with Credit Cards (English only)</option>
							<option value="7" <?php if($wpedon_button_buttonsize == "7") { echo "SELECTED"; } ?>>Big 3 with logo (English only)</option>
							<option value="8" <?php if($wpedon_button_buttonsize == "8") { echo "SELECTED"; } ?>>Custom</option>
						</select></td><td> Optional - Will override setttings page value.</td></tr><tr><td>
						
						Show Purpose / Name: </td><td><input type="checkbox" name="wpedon_button_enable_name" value="1" <?php if (get_post_meta($post_id,'wpedon_button_enable_name',true) == "1") { echo "CHECKED"; } ?>></td><td>Optional - Show the purpose / name above the button.</td></tr><tr><td>
						Show Donation Amount: </td><td><input type="checkbox" name="wpedon_button_enable_price" value="1" <?php if (get_post_meta($post_id,'wpedon_button_enable_price',true) == "1") { echo "CHECKED"; } ?>></td><td>Optional - Show the donation amount above the button.</td></tr><tr><td>
						Show Currency: </td><td><input type="checkbox" name="wpedon_button_enable_currency" value="1" <?php if (get_post_meta($post_id,'wpedon_button_enable_currency',true) == "1") { echo "CHECKED"; } ?>></td><td>Optional - Show the currency (example: USD) after the amount.</td></tr><tr><td>
						
						</td><td><br /></td></td></td></tr><tr><td>
						<b>Dropdown Menu</b> <br /><br /></td><td></td></td></td></tr><tr><td>						
						
						Amount Dropdown Menu: </td><td></td></td></td></tr><tr><td colspan="3">
							<table><tr><td>
								Amount Menu Name: &nbsp;  &nbsp;  &nbsp;  &nbsp;&nbsp;</td><td><input type="text" name="wpedon_button_scpriceprice" id="wpedon_button_scpriceprice" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriceprice',true)); ?>"></td><td> Optional, but required to show menu - show an amount dropdown menu. </td></tr><tr><td>
								Option / Amount 1: </td><td><input type="text" name="wpedon_button_scpriceaname" id="wpedon_button_scpriceaname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriceaname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpricea" id="wpedon_button_scpricea" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricea',true)); ?>"></td><td> Optional - Example Option: Size Medium Example Amount: 5.00 </td></tr><tr><td>
								Option / Amount 2: </td><td><input type="text" name="wpedon_button_scpricebname" id="wpedon_button_scpricebname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricebname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpriceb" id="wpedon_button_scpriceb" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriceb',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 3: </td><td><input type="text" name="wpedon_button_scpricecname" id="wpedon_button_scpricecname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricecname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpricec" id="wpedon_button_scpricec" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricec',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 4: </td><td><input type="text" name="wpedon_button_scpricedname" id="wpedon_button_scpricedname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricedname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpriced" id="wpedon_button_scpriced" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriced',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 5: </td><td><input type="text" name="wpedon_button_scpriceename" id="wpedon_button_scpriceename" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriceename',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpricee" id="wpedon_button_scpricee" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricee',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 6: </td><td><input type="text" name="wpedon_button_scpricefname" id="wpedon_button_scpricefname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricefname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpricef" id="wpedon_button_scpricef" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricef',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 7: </td><td><input type="text" name="wpedon_button_scpricegname" id="wpedon_button_scpricegname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricegname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpriceg" id="wpedon_button_scpriceg" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriceg',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 8: </td><td><input type="text" name="wpedon_button_scpricehname" id="wpedon_button_scpricehname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricehname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpriceh" id="wpedon_button_scpriceh" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriceh',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 9: </td><td><input type="text" name="wpedon_button_scpriceiname" id="wpedon_button_scpriceiname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpriceiname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpricei" id="wpedon_button_scpricei" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricei',true)); ?>"></td><td> Optional </td></tr><tr><td>
								Option / Amount 10: </td><td><input type="text" name="wpedon_button_scpricejname" id="wpedon_button_scpricejname" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricejname',true)); ?>" style="width:94px;"><input style="width:93px;" type="text" name="wpedon_button_scpricej" id="wpedon_button_scpricej" value="<?php echo esc_attr(get_post_meta($post_id,'wpedon_button_scpricej',true)); ?>"></td><td> Optional
							</td></tr></table>
							
						<?php wp_nonce_field( 'edit_'.$post_id ); ?>
						<input type="hidden" name="update">
							
						</td></tr></table>						
				</div>
				
			</form>