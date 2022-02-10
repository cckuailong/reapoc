<?php
$cc_whmcs_bridge_version=get_option("cc_whmcs_bridge_version");
$submit='Save Settings';
?>
<form method="post">
    <?php wp_nonce_field( 'cc_bridge_update_settings_submit' ); ?>

	<div class="alert success">
	  <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
		Once you have completed your settings and saved them, please go to the<br/>"Help" tab and use the "Check my installation" function.	
	</div>

    <?php require(dirname(__FILE__).'/../includes/cpedit.inc.php')?>

    <p class="submit">
        <input class="button-positive" name="install" type="submit" value="<?php echo $submit;?>" />
        <input type="hidden" name="action" value="install"/>
    </p>

</form>
