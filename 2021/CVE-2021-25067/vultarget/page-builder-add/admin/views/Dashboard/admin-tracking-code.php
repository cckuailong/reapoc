<?php if ( ! defined( 'ABSPATH' ) ) exit; 

?>

<h2> Global Tracking Scripts</h2>

<div class="settings-page-wrapper">
	<div class="pbp_form" style="width: 650px !important; margin-left: 50px;">
		<form id="ulpb_settings_form">
			<div style="display:; width:100%;">
				<h2> Enter your google analytics, pixel and other tracking scripts here. </h2>
				<p style="font-size: 13px;">These scripts will be added in all your landing pages & pages created using PluginOps Page Builder.</p>
				<br>
			</div>
			<p>Scripts for Head Tag</p>
			<div style="width:100%;">
			<?php
				$ulpb_global_tracking_scripts = get_option( 'ulpb_global_tracking_scripts' );
				$ulpb_global_tracking_scriptsBodyTag = get_option( 'ulpb_global_tracking_scriptsBodyTag' );

				$plugOps_pageBuilder_settings_nonce = wp_create_nonce( 'POPB_settings_nonce' );
			?>
			<textarea id="TrackingScriptsField" class="TrackingScriptsField" name="TrackingScriptsField" style="width:600px; height: 300px;"><?php echo $ulpb_global_tracking_scripts; ?></textarea>
			<br><br>
			<p>Scripts for Body Tag</p>
			<textarea id="TrackingScriptsFieldBody" class="TrackingScriptsFieldBody" name="TrackingScriptsFieldBody" style="width:600px; height: 250px;"><?php echo $ulpb_global_tracking_scriptsBodyTag; ?></textarea>
			</div>
			<br>
			<br>
		</form>
		<button id="ulpb_settings_form_submit" class="publishBtn"  style="height: 35px; width: 220px; cursor: pointer; ">Save Changes</button>

		<p id="response"></p>
	</div>
	
</div>

<script type="text/javascript">
	(function($){

    $('#ulpb_settings_form_submit').on('click',function(){
        $('#response').text('Processing'); 
        var form = $('#ulpb_settings_form');
        $.ajax({
            url: "<?php echo admin_url('admin-ajax.php?action=ulpb_tracking_scripts_data&POPB_settings_page_nonce='.$plugOps_pageBuilder_settings_nonce ); ?>",
            method: 'post',
            data: form.serialize(),
            success: function(result){
                if (result == 'Success'){
                    $('#response').text(result);  
                }else {
                    $('#response').text(result);
                }
            }
        });
         
        return false;   
    });

})(jQuery);
</script>