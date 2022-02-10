<div class="myStickyelements-intro-popup" id="myStickyelements-intro-popup" style="display:none;" title="<?php esc_attr_e( 'Welcome to My Sticky Elements &#127881;', 'mystickyelement' ); ?>">
	<p><?php _e( 'Select your contact form fields, chat, and social channels. Need help? Visit our ' ); ?><a href="https://premio.io/help/mystickyelements/?utm_soruce=wordpressmystickyelements" target="_blank"><?php _e( 'Help Center' ); ?></a><?php _e( ' and check the video.' ); ?></p>
	
	<iframe width="420" height="240" src="https://www.youtube.com/embed/-XN1FxDlQOY?start=20" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	<input type="hidden" id="myStickyelements_update_popup_status" value="<?php echo wp_create_nonce("myStickyelements_update_popup_status") ?>">
</div>
<script>
	( function( $ ) {
		"use strict";
		$( document ).ready( function(){
			jQuery( "#myStickyelements-intro-popup" ).dialog({
				resizable: false,
				modal: true,
				draggable: false,
				height: 'auto',
				width: 600,				
                sticky: true,
				dialogClass: "myStickyelements-intro-popup-wrap",
				buttons: {
					"Go to My Sticky Elements": {
						click: function () {
							myStickyelements_intro_popup_close();
						},
						text: 'Go to My Sticky Elements',
						class: 'purple-btn'
					},
				}
			});
			$( "#myStickyelements-intro-popup" ).on( "dialogclose", function( event, ui ) {
				myStickyelements_intro_popup_close();
			} );
			
			$( document ).on( 'click',  function( event ) {
				if ( !$( event.target ).closest( ".myStickyelements-intro-popup-wrap" ).length ) {
					myStickyelements_intro_popup_close();
				}
			});
		});
		function myStickyelements_intro_popup_close(){
			var nonceVal = jQuery("#myStickyelements_update_popup_status").val();
			$( "#myStickyelements-intro-popup" ).dialog('close');
			jQuery.ajax({
				url: ajaxurl,
				type:'post',
				data: {
					action: 'myStickyelements_intro_popup_action',
					nonce: nonceVal
				},
				success: function( data ){
					
				},
			});
		}
	})( jQuery );
</script>