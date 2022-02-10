/* ========= INFORMATION ============================
	- author:    Dmytro Lobov
	- url:       https://wow-estore.com
	- email:     givememoney1982@gmail.com
==================================================== */

'use strict';

jQuery(document).ready(function($) {
	language();
	usersroles();
	showchange();
});

function showchange(){	
	var show = jQuery('[name="param[show]"]').val();	 
	if (show == 'posts' || show == 'pages' || show == 'expost' || show == 'expage'|| show == 'taxonomy'){
		jQuery('#wow_id_post').css('display', '');
		jQuery('#wow-shortcode').css('display', 'none');
	}
	else if (show == 'shortecode'){
		jQuery('#wow-shortcode').css('display', '');
		jQuery('#wow_id_post').css('display', 'none');
	}
	else {
		jQuery('#wow-shortcode').css('display', 'none');
		jQuery('#wow_id_post').css('display', 'none');
	}		
	if (show == 'taxonomy'){
		jQuery('#wow_taxonomy').css('display', '');
	}
	else{
		jQuery('#wow_taxonomy').css('display', 'none');
	}
}
function language(){
	if (jQuery('#wow_depending_language').is(':checked')){
		jQuery('#wow_language').css('display', '');
	}
	else {
		jQuery('#wow_language').css('display', 'none');
	}
}

function usersroles(){
	var users = jQuery('input[name="param[item_user]"]:checked').val();	
	if (users == 2){
		jQuery('#wow_users_roles').css('display', '');
	}
	else{
		jQuery('#wow_users_roles').css('display', 'none');
	}
	
}

function itemadd(menu){  
 		
	var item = '<div class="wow-container include-file"> <div class="wow-element"> Type of file:<br/> <select name="param[include][]"> <option>css</option> <option>js</option> </select> </div> <div class="wow-element"> URL to file:<br/> <input type="text" value="" name="param[include_file][]" > </div> </div>';	
	jQuery(item).appendTo("#include_file");	
	
}
function itemremove(menu){		
	jQuery("div.include-file").last().remove();
}