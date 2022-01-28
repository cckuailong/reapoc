<?php
if ( ! function_exists('add_action') ) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

if ( ! current_user_can('manage_options') ) { 
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// BPS jQuery UI Dialog Uninstall Options Form Notes:
// This Form is only for saving the Uninstall Option DB value for use in the bulletproof_security_uninstall() function.
// The bulletproof_security_uninstall() function is in /admin/includes/admin.php.
// /bps-backup/ folder|file deletion: bpsPro_pop_uninstall_bps_backup_folder($source) function is in admin.php.
// Note: this form does not work in Network|Multisite so do not show the Uninstall Options link in Action Links 
function bpsPro_pop_get_message() {
	
	if ( current_user_can('manage_options') ) {
		
		if ( isset( $_GET['settings-updated'] ) && @$_GET['settings-updated'] == true) {
	
		$text = '<div style="margin:10px 0px 0px 0px;"><font color="green"><strong>'.__('BPS Plugin Uninstall Option Saved Successfully. Click the Close button below to go back to the Plugins page and deactivate and delete the BPS plugin.', 'bulletproof-security').'</strong></font></div>';
		echo $text;	
		}
	}
}

?>

<style>
<!--
.wp-dialog.bps-dialog .ui-dialog-titlebar-close {visibility:hidden;}

#bps-pop-uninstall .button {height:28px;}
#bps-pop-uninstall .bps-button {background:#0490b3;border-color:#037c9a;color:white;-webkit-box-shadow:inset 0 1px 0 #22cffb, 0 1px 0 rgba(0, 0, 0, 0.15);box-shadow:inset 0 1px 0 #22cffb, 0 1px 0 rgba(0, 0, 0, 0.15);}
#bps-pop-uninstall .bps-button:hover, #bps-container .bps-button:focus {background:#05b5e1;border-color:#036881;color:white;-webkit-box-shadow:inset 0 1px 0 #09cafa;box-shadow:inset 0 1px 0 #09cafa;}
#bps-pop-uninstall .bps-button:focus {-webkit-box-shadow:inset 0 1px 0 #09cafa, 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, 0.8);box-shadow:inset 0 1px 0 #09cafa, 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, 0.8);}
#bps-pop-uninstall .bps-button:active {background:#037c9a;border-color:#036881;color:white;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0, 0, 0, 0.5), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, 0.8);box-shadow:inset 0 2px 5px -3px rgba(0, 0, 0, 0.5), 0 0 0 1px #5b9dd9, 0 0 2px 1px rgba(30, 140, 190, 0.8);}
-->
</style>

<div id="bps-pop-uninstall" title="<?php _e('BPS Plugin Uninstall Options', 'bulletproof-security'); ?>">
<p><?php $text = '<strong>'.__('If you are upgrading to BPS Pro, select the BPS Pro Upgrade Uninstall option and click the Save Option button or just click the Close button below and do a normal plugin uninstall.', 'bulletproof-security').'</strong><br><br><strong>'.__('If you want to completely delete the BPS plugin, all files, Custom Code and BPS database settings, select the Complete BPS Plugin Uninstall option and click the Save Option button.', 'bulletproof-security').'</strong>'; echo $text; ?></p>

<form name="bpsPOPuninstall" action="options.php" method="post">
	<?php settings_fields('bulletproof_security_options_pop_uninstall'); ?> 
	<?php $POPoptions = get_option('bulletproof_security_options_pop_uninstall'); 
	$bps_pop_uninstall = ! isset($POPoptions['bps_pop_uninstall']) ? '' : $POPoptions['bps_pop_uninstall'];
	?>

<strong><label for="bps-pop-uninstall"><?php _e('BPS Plugin Uninstall Options:', 'bulletproof-security'); ?></label></strong><br />
<select name="bulletproof_security_options_pop_uninstall[bps_pop_uninstall]" style="width:380px;">
<option value="1" <?php selected('1', $bps_pop_uninstall); ?>><?php _e('BPS Pro Upgrade Uninstall', 'bulletproof-security'); ?></option>
<option value="2" <?php selected('2', $bps_pop_uninstall); ?>><?php _e('Complete BPS Plugin Uninstall', 'bulletproof-security'); ?></option>
</select><br />

<?php bpsPro_pop_get_message(); ?>

<input type="submit" name="bpsPOPuninstallSubmit" value="<?php esc_attr_e('Save Option', 'bulletproof-security'); ?>" style="margin:15px 0px 0px 0px" class="button bps-button" />
</form>
</div>

<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){				
	
	var $pop = $("#bps-pop-uninstall");     
	
	$pop.dialog({                            
		dialogClass: "wp-dialog bps-dialog",  
		autoOpen: true,
		show: {
			effect: "blind",
			duration: 500
		},
		hide: {
			effect: "explode",
			duration: 300
		},
		modal: true,
	 	width: 420,
	 	height: 400,			
	 	position: { 
			my: "center", 
			at: "center" 
		},
		buttons: {             
			"Close": function() {                 
				$(this).dialog("close");
				 window.location.href = '/wp-admin/plugins.php';            
			}         
		}
	});
});
/* ]]> */
</script>