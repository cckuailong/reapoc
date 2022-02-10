<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function wpedon_plugin_options() {
	if ( !current_user_can( "manage_options" ) )  {
		wp_die( __( "You do not have sufficient permissions to access this page." ) );
	}




// media uploader
function load_admin_things() {
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
}
load_admin_things();

?>

<script>
jQuery(document).ready(function() {
	var formfield;
	jQuery('.upload_image_button').click(function() {
		jQuery('html').addClass('Image');
		formfield = jQuery(this).prev().attr('name');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		return false;
	});
	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html){
	if (formfield) {
		fileurl = jQuery('img',html).attr('src');
		jQuery('#'+formfield).val(fileurl);
		tb_remove();
		jQuery('html').removeClass('Image');
	} else {
		window.original_send_to_editor(html);
	}
	};
});
</script>

<?php


// settings page
echo "<table width='100%'><tr><td width='70%'><br />";
echo "<label style='color: #000;font-size:18pt;'><center>Accept Donations with PayPal Settings</center></label>";
echo "<form method='post' action='".esc_url($_SERVER["REQUEST_URI"])."'>";


// save and update options
if (isset($_POST['update'])) {

	if (!isset($_POST['action_save']) || ! wp_verify_nonce($_POST['action_save'],'nonce_save') ) {
	   print 'Sorry, your nonce did not verify.';
	   exit;
	}
	
	$options['currency'] =			intval($_POST['currency']);
	if (!$options['currency']) { 	$options['currency'] = "25"; }
		
	$options['language'] = 			intval($_POST['language']);
	if (!$options['language']) { 	$options['language'] = "3";	}
		
	$options['mode'] = 				intval($_POST['mode']);
	if (!$options['mode']) { 		$options['mode'] = "1";	}
		
	$options['size'] = 				intval($_POST['size']);
	if (!$options['size']) { 		$options['size'] = "1";	}
		
	$options['opens'] = 			intval($_POST['opens']);
	if (!$options['opens']) { 		$options['opens'] = "1"; }
		
	$options['no_shipping'] = 		intval($_POST['no_shipping']);
	if (!$options['no_shipping']) { $options['no_shipping'] = "0"; }
		
	$options['no_note'] = 			intval($_POST['no_note']);
	if (!$options['no_note']) { 	$options['no_note'] = "0"; }
		
	$options['liveaccount'] = 		sanitize_text_field($_POST['liveaccount']);
	$options['sandboxaccount'] = 	sanitize_text_field($_POST['sandboxaccount']);
	$options['image_1'] = 			sanitize_text_field($_POST['image_1']);
	$options['cancel'] = 			sanitize_text_field($_POST['cancel']);
	$options['return'] = 			sanitize_text_field($_POST['return']);
	
	
	update_option("wpedon_settingsoptions", $options);
	
	echo "<br /><div class='updated'><p><strong>"; _e("Settings Updated."); echo "</strong></p></div>";
}


// get options
$options = get_option('wpedon_settingsoptions');
foreach ($options as $k => $v ) { $value[$k] = $v; }

echo "</td><td></td></tr><tr><td>";

// form
echo "<br />";
?>

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Usage - How to use this plugin
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<b>1. Enter PayPal account</b><br />
Enter your PayPal account on this page in the field titled "Live Account". <br /><br />

<b>2. Make a button</b><br />
On the <a href='admin.php?page=wpedon_buttons' target='_blank'>buttons page</a>, make a new button. <br /><br />

<b>3. Place button on page</b><br />
You can place the button on your site in 3 ways. In you Page / Post editor you can use the button titled "PayPal Donation Button". You can use the "PayPal Donation Button" Widget. Or you can manually place the shortcode on a Page / Post.<br /><br />

<b>4. View donations</b><br />
On the <a href='admin.php?page=wpedon_menu' target='_blank'>donations page</a> you can view the donations that have been made on your site.<br /><br />

</div><br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Language & Currency
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<b>Language:</b>
<select name="language">
<option <?php if ($value['language'] == "1") { echo "SELECTED"; } ?> value="1">Danish</option>
<option <?php if ($value['language'] == "2") { echo "SELECTED"; } ?> value="2">Dutch</option>
<option <?php if ($value['language'] == "3") { echo "SELECTED"; } ?> value="3">English</option>
<option <?php if ($value['language'] == "20") { echo "SELECTED"; } ?> value="20">English - UK</option>
<option <?php if ($value['language'] == "4") { echo "SELECTED"; } ?> value="4">French</option>
<option <?php if ($value['language'] == "5") { echo "SELECTED"; } ?> value="5">German</option>
<option <?php if ($value['language'] == "6") { echo "SELECTED"; } ?> value="6">Hebrew</option>
<option <?php if ($value['language'] == "7") { echo "SELECTED"; } ?> value="7">Italian</option>
<option <?php if ($value['language'] == "8") { echo "SELECTED"; } ?> value="8">Japanese</option>
<option <?php if ($value['language'] == "9") { echo "SELECTED"; } ?> value="9">Norwgian</option>
<option <?php if ($value['language'] == "10") { echo "SELECTED"; } ?> value="10">Polish</option>
<option <?php if ($value['language'] == "11") { echo "SELECTED"; } ?> value="11">Portuguese</option>
<option <?php if ($value['language'] == "12") { echo "SELECTED"; } ?> value="12">Russian</option>
<option <?php if ($value['language'] == "13") { echo "SELECTED"; } ?> value="13">Spanish</option>
<option <?php if ($value['language'] == "14") { echo "SELECTED"; } ?> value="14">Swedish</option>
<option <?php if ($value['language'] == "15") { echo "SELECTED"; } ?> value="15">Simplified Chinese - China only</option>
<option <?php if ($value['language'] == "16") { echo "SELECTED"; } ?> value="16">Traditional Chinese - Hong Kong only</option>
<option <?php if ($value['language'] == "17") { echo "SELECTED"; } ?> value="17">Traditional Chinese - Taiwan only</option>
<option <?php if ($value['language'] == "18") { echo "SELECTED"; } ?> value="18">Turkish</option>
<option <?php if ($value['language'] == "19") { echo "SELECTED"; } ?> value="19">Thai</option>
</select>

PayPal currently supports 20 languages.
<br /><br />

<b>Currency:</b> 
<select name="currency">
<option <?php if ($value['currency'] == "1") { echo "SELECTED"; } ?> value="1">Australian Dollar - AUD</option>
<option <?php if ($value['currency'] == "2") { echo "SELECTED"; } ?> value="2">Brazilian Real - BRL</option> 
<option <?php if ($value['currency'] == "3") { echo "SELECTED"; } ?> value="3">Canadian Dollar - CAD</option>
<option <?php if ($value['currency'] == "4") { echo "SELECTED"; } ?> value="4">Czech Koruna - CZK</option>
<option <?php if ($value['currency'] == "5") { echo "SELECTED"; } ?> value="5">Danish Krone - DKK</option>
<option <?php if ($value['currency'] == "6") { echo "SELECTED"; } ?> value="6">Euro - EUR</option>
<option <?php if ($value['currency'] == "7") { echo "SELECTED"; } ?> value="7">Hong Kong Dollar - HKD</option> 	 
<option <?php if ($value['currency'] == "8") { echo "SELECTED"; } ?> value="8">Hungarian Forint - HUF</option>
<option <?php if ($value['currency'] == "9") { echo "SELECTED"; } ?> value="9">Israeli New Sheqel - ILS</option>
<option <?php if ($value['currency'] == "10") { echo "SELECTED"; } ?> value="10">Japanese Yen - JPY</option>
<option <?php if ($value['currency'] == "11") { echo "SELECTED"; } ?> value="11">Malaysian Ringgit - MYR</option>
<option <?php if ($value['currency'] == "12") { echo "SELECTED"; } ?> value="12">Mexican Peso - MXN</option>
<option <?php if ($value['currency'] == "13") { echo "SELECTED"; } ?> value="13">Norwegian Krone - NOK</option>
<option <?php if ($value['currency'] == "14") { echo "SELECTED"; } ?> value="14">New Zealand Dollar - NZD</option>
<option <?php if ($value['currency'] == "15") { echo "SELECTED"; } ?> value="15">Philippine Peso - PHP</option>
<option <?php if ($value['currency'] == "16") { echo "SELECTED"; } ?> value="16">Polish Zloty - PLN</option>
<option <?php if ($value['currency'] == "17") { echo "SELECTED"; } ?> value="17">Pound Sterling - GBP</option>
<option <?php if ($value['currency'] == "18") { echo "SELECTED"; } ?> value="18">Russian Ruble - RUB</option>
<option <?php if ($value['currency'] == "19") { echo "SELECTED"; } ?> value="19">Singapore Dollar - SGD</option>
<option <?php if ($value['currency'] == "20") { echo "SELECTED"; } ?> value="20">Swedish Krona - SEK</option>
<option <?php if ($value['currency'] == "21") { echo "SELECTED"; } ?> value="21">Swiss Franc - CHF</option>
<option <?php if ($value['currency'] == "22") { echo "SELECTED"; } ?> value="22">Taiwan New Dollar - TWD</option>
<option <?php if ($value['currency'] == "23") { echo "SELECTED"; } ?> value="23">Thai Baht - THB</option>
<option <?php if ($value['currency'] == "24") { echo "SELECTED"; } ?> value="24">Turkish Lira - TRY</option>
<option <?php if ($value['currency'] == "25") { echo "SELECTED"; } ?> value="25">U.S. Dollar - USD</option>
</select>
PayPal currently supports 25 currencies.
<br /><br /></div>

<?php


?>
<br /><br /><div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; PayPal Account </div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<?php

echo "<b>Live Account: </b><input type='text' name='liveaccount' value='".esc_attr($value['liveaccount'])."'> Required";
echo "<br />Enter a valid Merchant account ID (strongly recommend) or PayPal account email address. All payments will go to this account.";
echo "<br /><br />You can find your Merchant account ID in your PayPal account under Profile -> My business info -> Merchant account ID";

echo "<br /><br />If you don't have a PayPal account, you can sign up for free at <a target='_blank' href='https://paypal.com'>PayPal</a>. <br /><br />";


echo "<b>Sandbox Account: </b><input type='text' name='sandboxaccount' value='".esc_attr($value['sandboxaccount'])."'> Optional";
echo "<br />Enter a valid sandbox PayPal account email address. A Sandbox account is a fake account used for testing. This is useful to make sure your PayPal account and settings are working properly being going live.";
echo "<br /><br />If you don't have a PayPal developer account, you can sign up for free at the <a target='_blank' href='https://developer.paypal.com/developer'>PayPal Developer</a> site. <br /><br />";

echo "<b>Sandbox Mode:</b>";
echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "1") { echo "checked='checked'"; } echo " type='radio' name='mode' value='1'>On (Sandbox mode)";
echo "&nbsp; &nbsp; <input "; if ($value['mode'] == "2") { echo "checked='checked'"; } echo " type='radio' name='mode' value='2'>Off (Live mode)";

echo "<br /><br /></div>";



?>

<br /><br />
<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Other Settings
</div><div style="background-color:#fff;border: 1px solid #E5E5E5;padding:5px;"><br />

<?php
echo "<table><tr><td valign='top'>";

echo "<b>Default&nbsp;Button&nbsp;Style:</b></td><td valign='top' style='text-align: left;'>";

echo "<input "; if ($value['size'] == "1") { echo "checked='checked'"; } echo " type='radio' name='size' value='1'>Small <br /><img src='https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif'></td><td valign='top' style='text-align: left;'>";
echo "<input "; if ($value['size'] == "2") { echo "checked='checked'"; } echo " type='radio' name='size' value='2'>Big <br /><img src='https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif'></td><td valign='top' style='text-align: left;'>";
echo "<input "; if ($value['size'] == "3") { echo "checked='checked'"; } echo " type='radio' name='size' value='3'>Big with Credit Cards <br /><img src='https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif'>";

echo "</td></tr><tr><td></td><td valign='top'>";

echo "<input "; if ($value['size'] == "4") { echo "checked='checked'"; } echo " type='radio' name='size' value='4'>Small 2 <br />(English only)<br /><img src='https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_74x21.png'></td><td valign='top' style='text-align: left;'>";
echo "<input "; if ($value['size'] == "5") { echo "checked='checked'"; } echo " type='radio' name='size' value='5'>Big 2 <br />(English only)<br /><img src='https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_92x26.png'></td><td valign='top' style='text-align: left;'>";
echo "<input "; if ($value['size'] == "6") { echo "checked='checked'"; } echo " type='radio' name='size' value='6'>Big 2 with Credit Cards <br />(English only)<br /><img src='https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_cc_147x47.png'></td><td valign='top' style='text-align: left;'>";
echo "<input "; if ($value['size'] == "7") { echo "checked='checked'"; } echo " type='radio' name='size' value='7'>Big 3 with logo <br />(English only)<br /><img src='https://www.paypalobjects.com/webstatic/en_US/btn/btn_donate_pp_142x27.png'>";

echo "</td></tr><tr><td></td><td valign='top' colspan='3'>";


echo "<input "; if ($value['size'] == "8") { echo "checked='checked'"; } echo " type='radio' name='size' value='8'>Custom <br /> Use your own image <br />
<input type='text' id='image_1' name='image_1' size='15' value='"; echo isset($value["image_1"]) ? esc_attr($value["image_1"]) : ''; echo "'><input id='_btn' class='upload_image_button' type='button' value='Select Image'>";

echo "</td></tr><tr><td><b><br />Buttons open PayPal in:</b></td>";
echo "<td><input "; if ($value['opens'] == "1") { echo "checked='checked'"; } echo " type='radio' name='opens' value='1'>Same page</td>";
echo "<td><input "; if ($value['opens'] == "2") { echo "checked='checked'"; } echo " type='radio' name='opens' value='2'>New page</td></tr>";

echo "</td></tr><tr><td><b><br />Prompt buyers to include a note<br /> with their payments:</b></td>";
echo "<td><input "; if ($value['no_note'] == "0") { echo "checked='checked'"; } echo " type='radio' name='no_note' value='0'>Yes</td>";
echo "<td><input "; if ($value['no_note'] == "1") { echo "checked='checked'"; } echo " type='radio' name='no_note' value='1'>No</td></tr>";

echo "</td></tr><tr><td><b><br />Prompt buyers for a shipping<br /> address:</b></td>";
echo "<td><input "; if ($value['no_shipping'] == "0") { echo "checked='checked'"; } echo " type='radio' name='no_shipping' value='0'>Yes</td>";
echo "<td><input "; if ($value['no_shipping'] == "1") { echo "checked='checked'"; } echo " type='radio' name='no_shipping' value='1'>No</td>";
echo "<td><input "; if ($value['no_shipping'] == "2") { echo "checked='checked'"; } echo " type='radio' name='no_shipping' value='2'>Yes, and require</td></tr>";


echo "</table><br /><br />";



$siteurl = get_site_url();

echo "<b>Cancel URL: </b>";
echo "<input type='text' name='cancel' value='".esc_attr($value['cancel'])."'> Optional <br />";
echo "If the customer goes to PayPal and clicks the cancel button, where do they go. Example: $siteurl/cancel. Max length: 1,024 characters. <br /><br />";

echo "<b>Return URL: </b>";
echo "<input type='text' name='return' value='".esc_attr($value['return'])."'> Optional <br />";
echo "If the customer goes to PayPal and successfully pays, where are they redirected to after. Example: $siteurl/thankyou. Max length: 1,024 characters. <br /><br />";


?>
<br /><br /></div>

<input type='hidden' name='update'><br />
<?php echo wp_nonce_field('nonce_save','action_save'); ?>
<input type='submit' name='btn2' class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;' value='Save Settings'>





<br /><br /><br />


WPPlugin is an offical PayPal Partner. Various trademarks held by their respective owners.


</form>




</td><td width='5%'>
</td><td width='24%' valign='top'>

<br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
	&nbsp; Get the Pro Version
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;">

<center><label style="font-size:14pt;">With the Pro version you'll <br /> be able to: </label></center>
	
<br />	
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div>Offer recurring donations.<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div>Offer daily, weekly, monthly, and yearly billing.<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div>Set how long should billing should continue.<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div>Offers customers a recurring donations dropdown menu.<br />
<div class="dashicons dashicons-yes" style="margin-bottom: 6px;"></div>Offer up to 20 amount dropdown menu options instead of 10.<br />

<br />
<center><a target='_blank' href="https://wpplugin.org/downloads/paypal-donation-pro/" class='button-primary' style='font-size: 17px;line-height: 28px;height: 32px;'>Learn More</a></center>
<br />
	
</div>

<br /><br />

<div style="background-color:#333333;padding:8px;color:#eee;font-size:12pt;font-weight:bold;">
&nbsp; Quick Links
</div>

<div style="background-color:#fff;border: 1px solid #E5E5E5;padding:8px;"><br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wordpress.org/support/plugin/easy-paypal-donation">Support Forum</a> <br />

<div class="dashicons dashicons-arrow-right" style="margin-bottom: 6px;"></div> <a target="_blank" href="https://wpplugin.org/documentation">FAQ</a> <br />

</div>



</td><td width='1%'>

</td></tr></table>


<?php
// end settings page and required permissions
}