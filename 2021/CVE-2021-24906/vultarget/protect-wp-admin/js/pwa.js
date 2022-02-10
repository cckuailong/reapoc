/* admin js*/
jQuery(document).ready(function(){
		jQuery(".pwa-tab").hide();
		jQuery("#div-pwa-general").show();
	    jQuery(".pwa-tab-links").click(function(){
			var divid=jQuery(this).attr("id");
			jQuery(".pwa-tab-links").removeClass("active");
			jQuery(".pwa-tab").hide();
			jQuery("#"+divid).addClass("active");
			jQuery("#div-"+divid).fadeIn();
		});
		   console.log(pwa_admin_object.st+'ffff');
	   jQuery("#pwa-settings-form-admin .button-primary").click(function(){
		 var $el = jQuery("#pwa_active");
		 var $vlue = jQuery("#pwa_rewrite_text").val();
		 var pwaActive = pwa_admin_object.st;
		 if( ( $el[0].checked ) && $vlue=="" ) {
			 	 jQuery("#pwa_rewrite_text").css("border","1px solid red");
			 	 jQuery("#adminurl").append(" <span style=\'color:red;display:block;\'>Please enter new admin slug</span>");
			 	 return false;
			 }
			
			var seoUrlVal=jQuery("#check_permalink").val();
			var htaccessWriteable = pwa_admin_object.ht;
			var hostIP =pwa_admin_object.ip;
		//	alert(hostIP);
			if(seoUrlVal=="no")
			{
			alert("Please update permalinks before activate the plugin. permalinks option should not be default!.");
			window.open(pwa_admin_object.ur,"_blank");
			return false;
				}
				else
				{
					return true;
					}
			});
			
	/* add image upload image button */
	jQuery(".upload_image").click(function() {	
	inputfieldId = jQuery(this).attr("data-id");
	formfield = jQuery("#"+inputfieldId).attr("name");
	tb_show( "", "media-upload.php?type=image&amp;TB_iframe=true" );
	return false;
	});
	window.send_to_editor = function(html) {
	imgurl = jQuery(html).attr('href');
	if(imgurl==undefined){ imgurl = jQuery(html).attr('src');}
	jQuery("#"+inputfieldId).val(imgurl);
	tb_remove();
   }
   // Add Color Picker to all inputs that have 'color-field' class
      jQuery('.color-field').wpColorPicker();
});
