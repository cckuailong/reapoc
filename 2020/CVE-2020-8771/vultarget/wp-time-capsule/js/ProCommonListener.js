jQuery(document).ready(function($) {
	jQuery('.thickbox.open-plugin-details-modal').on("click", function(e) {
			get_current_backup_status_wptc(); //to add extra buttons for bbu
	});

	//theme update
	setTimeout(function (){
		jQuery('.theme').on('click', '.button-link , #update-theme', function(e) {
			if (is_current_update_action_set_wptc()) {
				clear_current_update_action_wptc();
			} else {
				if (this.className.indexOf('button-link-themes-staging-wptc') !== -1) {
					handle_theme_button_link_request_wptc(this, e, false, true);
				} else {
					handle_theme_button_link_request_wptc(this, e, false);
				}
			}
		});
	}, 1000);

});
function handle_plugin_upgrade_request_wptc(obj, e, request_from_direct_link, stage_n_update){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}
	if (request_from_direct_link === undefined) {
		request_from_direct_link = false;
		if(obj.className && obj.className.indexOf('upgrade-plugins-bbu-wptc') !== -1){
			request_from_direct_link = true;
		}
	}

	if (is_current_update_action_set_wptc() && request_from_direct_link === false && stage_n_update !== true) {
		clear_current_update_action_wptc();
	} else {
		prevent_action_propagation_wptc(e);
		var update_items = [];
		jQuery('#update-plugins-table').find('input[name="checked[]"]').each(function(key, index){
			if(jQuery(index).is(':checked')){
				update_items.push(jQuery(index).val());
			}
		});
		if (update_items.length > 0) {
			if (stage_n_update === true) {
				wptc_choose_update_in_stage(update_items, 'plugin');
			} else {
				check_to_show_dialog_wptc(jQuery(obj), update_items, 'plugin', request_from_direct_link);
			}
		}
	}
}

function handle_plugin_themes_link_request_wptc(obj, e, request_from_direct_link, stage_n_update, update_type){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);

		return false;
	}

	if (request_from_direct_link === undefined) {
		request_from_direct_link = false;
		if(obj.className && ( obj.className.indexOf('update-link-plugins-bbu-wptc') !== -1 || obj.className.indexOf('update-now-plugins-bbu-wptc') !== -1 ) || ( obj.className.indexOf('update-link-themes-bbu-wptc') !== -1 || obj.className.indexOf('update-now-themes-bbu-wptc') !== -1 ) ){
			request_from_direct_link = true;
		}
	}

	if ( is_current_update_action_set_wptc() 
		&& request_from_direct_link === false 
		&& stage_n_update !== true ) {

		clear_current_update_action_wptc();

	} else {
		prevent_action_propagation_wptc(e);
		var update_items = [];
		if (jQuery(obj).parents('tr').attr('data-plugin') != undefined && jQuery(obj).parents('tr').attr('data-plugin')) {
			update_items.push(jQuery(obj).parents('tr').attr('data-plugin'));
		} else if(jQuery(obj).attr('data-plugin') != undefined && jQuery(obj).attr('data-plugin')) {
			update_items.push(jQuery(obj).attr('data-plugin'));
		} else if(jQuery(obj).parents('tr').prev('tr').find("input").val() != undefined){
			update_items.push(jQuery(obj).parents('tr').prev('tr').find("input").val()); // wp 4.0 <=
		} else {
			var plugin_div = jQuery(obj).parents('.plugin-action-buttons').find('li')[0];
			if (plugin_div) {
				update_items.push(jQuery(plugin_div).find('a').attr('data-plugin'));
			}
		}
		if (update_items.length > 0) {
			if (stage_n_update === true) {
				wptc_choose_update_in_stage(update_items, update_type);
			} else {
				check_to_show_dialog_wptc(jQuery(obj), update_items, update_type , request_from_direct_link);
			}
		}
	}
}


function handle_plugin_themes_button_action_request_wptc(obj, e , request_from_direct_link, stage_n_update, update_type){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}
	prevent_action_propagation_wptc(e);
	var update_items = [];
	jQuery('table.wp-list-table.plugins tr').each( function(key, index){
		if(jQuery(index).find('input').attr('name') === 'checked[]'){
			if(jQuery(index).find('input').is(':checked')){
				update_items.push(jQuery(index).find('input').val());
			}
		}
	});
	if (update_items.length > 0) {
		if (stage_n_update === true) {
			wptc_choose_update_in_stage(update_items, update_type);
		} else {
			check_to_show_dialog_wptc(jQuery(obj), update_items, update_type, request_from_direct_link);
		}
	}
}

function handle_themes_upgrade_request_wptc(obj, e, request_from_direct_link, stage_n_update){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}

	if (request_from_direct_link === undefined) {
		request_from_direct_link = false;
		if(obj.className && obj.className.indexOf('upgrade-themes-bbu-wptc') !== -1){
			request_from_direct_link = true;
		}
	}

	if (is_current_update_action_set_wptc() && request_from_direct_link === false && stage_n_update !== true) {
		clear_current_update_action_wptc();
	} else {
		prevent_action_propagation_wptc(e);
		var update_items = [];
		jQuery('#update-themes-table').find('input[name="checked[]"]').each(function(key, index){
			if(jQuery(index).is(':checked')){
				update_items.push(jQuery(index).val());
			}
		});
		if (update_items.length > 0) {
			if (stage_n_update === true) {
				wptc_choose_update_in_stage(update_items, 'theme');
			} else {
				check_to_show_dialog_wptc(jQuery(obj), update_items, 'theme' , request_from_direct_link);
			}
		}
	}
}

function handle_core_upgrade_request_wptc(obj, e , request_from_direct_link, stage_n_update){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}
	if (window.location.href.indexOf('update-core.php') === -1) {
		return false;
	}

	if (is_current_update_action_set_wptc() && request_from_direct_link === false && stage_n_update !== true) {
			clear_current_update_action_wptc();
	} else {
		prevent_action_propagation_wptc(e);
		var update_items = [];
		update_items.push(jQuery(obj).parents('p').find('input[name=version]').val());
		if (update_items.length > 0) {
			if (stage_n_update === true) {
				wptc_choose_update_in_stage(update_items, 'core');
			} else {
				check_to_show_dialog_wptc(jQuery(obj), update_items, 'core' , request_from_direct_link);
			}
		}
	}

}

function handle_theme_button_link_request_wptc(obj, e , request_from_direct_link, stage_n_update){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}
	prevent_action_propagation_wptc(e);
	var update_items = [];
	if (window.location.href.match(/theme=([^&]+)/) && window.location.href.match(/theme=([^&]+)/)[1]) {
		update_items.push(window.location.href.match(/theme=([^&]+)/)[1]);
	} else if(jQuery(obj).parents('.theme.focus').attr('data-slug') != undefined && jQuery(obj).parents('.theme.focus').attr('data-slug')){
		update_items.push(jQuery(obj).parents('.theme.focus').attr('data-slug'));
	} else if(jQuery(obj).parents('.theme').attr('data-slug') != undefined && jQuery(obj).parents('.theme').attr('data-slug')){
		update_items.push(jQuery(obj).parents('.theme').attr('data-slug'));
	}
	if (update_items.length > 0) {
		new_theme_update_listener = 1;
		if (stage_n_update === true) {
			wptc_choose_update_in_stage(update_items, 'theme');
		} else {
			check_to_show_dialog_wptc(jQuery(obj), update_items, 'theme', request_from_direct_link);
		}
	}
}

function handle_theme_link_request_wptc(obj, e, request_from_direct_link, stage_n_update){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}
	prevent_action_propagation_wptc(e);
	var update_items = [];
	update_items.push(jQuery(obj).siblings('#update-theme').attr('data-slug'));
	if (update_items.length > 0) {
		new_theme_update_listener = 1;
		if (stage_n_update === true) {
			wptc_choose_update_in_stage(update_items, 'theme');
		} else {
			check_to_show_dialog_wptc(jQuery(obj), update_items, 'theme', request_from_direct_link);
		}
	}
}

function handle_iframe_requests_wptc(obj, e, request_from_direct_link, stage_n_update){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}
	if (is_current_update_action_set_wptc() && obj.className.indexOf('plugin-update-from-iframe-bbu-wptc') === -1) {
		clear_current_update_action_wptc();
		if (window.parent.location.href.indexOf('plugins.php') !== -1) {
			return false;
		}
		var link = jQuery(obj).attr('href');
		if (link) {
			window.parent.location.assign(link);
		}
	} else {
		if(jQuery(obj).attr('href') && jQuery(obj).attr('href').toLowerCase().indexOf('action=install') !== -1){
			return false;
		}
		prevent_action_propagation_wptc(e);
		var update_items = [];
		if (jQuery(obj).parents('tr').attr('data-plugin') != undefined && jQuery(obj).parents('tr').attr('data-plugin')) {
			update_items.push(jQuery(obj).parents('tr').attr('data-plugin'));
		} else if(jQuery(obj).attr('data-plugin') != undefined && jQuery(obj).attr('data-plugin')) {
			update_items.push(jQuery(obj).attr('data-plugin'));
		} else if(jQuery(obj).attr('href') != undefined && jQuery(obj).attr('href').match(/plugin=([^&]+)/) != undefined && jQuery(obj).attr('href').match(/plugin=([^&]+)/)[1] != undefined) {
			update_items.push(decodeURIComponent(jQuery(obj).attr('href').match(/plugin=([^&]+)/)[1]));
		} else if(jQuery(obj).siblings('#plugin_update_from_iframe').length){
			update_items.push(jQuery(obj).siblings('#plugin_update_from_iframe').attr('data-plugin'));
		}
		if (update_items.length > 0) {
			if (stage_n_update === true) {
				wptc_choose_update_in_stage(update_items, 'plugin');
			} else {
				check_to_show_dialog_wptc(jQuery(obj), update_items, 'plugin', request_from_direct_link);
			}
		}
	}
}

function handle_translation_upgrade_request_wptc(obj, e , request_from_direct_link, stage_n_update){
	if (jQuery(obj).hasClass('disabled')) {
		prevent_action_propagation_wptc(e);
		return false;
	}
	if (window.location.href.indexOf('update-core.php') === -1) {
		return false;
	}

	if (jQuery(obj).parents('form').attr('action').toLowerCase().indexOf('action=do-translation-upgrade') === -1) {
		return false;
	}

	if (is_current_update_action_set_wptc()) {
			clear_current_update_action_wptc();
	} else {
		prevent_action_propagation_wptc(e);
		var update_items = [];
		update_items.push('translation');
		if (update_items.length > 0) {
			if (stage_n_update === true) {
				wptc_choose_update_in_stage(update_items, 'translation');
			} else {
				check_to_show_dialog_wptc(jQuery(obj), update_items, 'translation', request_from_direct_link);
			}
		}
	}
}

function clear_current_update_action_wptc(){
	delete current_update_action;
}


function is_current_update_action_set_wptc(){
	if ( typeof current_update_action != "undefined" 
		 && current_update_action == "no" 
		 // || (typeof show_alerts_wptc != 'undefined' && !show_alerts_wptc) 
		) {

		if(typeof is_backup_tab_allowed_with_admin_user_check != 'undefined' 
			&& !is_backup_tab_allowed_with_admin_user_check) {

			return true;
		}

		return true;
	} else {
		if(typeof is_backup_tab_allowed_with_admin_user_check != 'undefined' 
			&& !is_backup_tab_allowed_with_admin_user_check) {

			return true;
		}
		
		return false;
	}
}

function prevent_action_propagation_wptc(e){
	e.preventDefault();
	e.stopImmediatePropagation();
	e.stopPropagation();
	return false;
}

function check_to_show_dialog_wptc(obj, update_items, update_ptc_type, direct_update) {
	jQuery('#TB_window').html('');
	//to show the backup dialog box before updating plugins , themes etc
	if (typeof check_to_show_dialog_called != 'undefined' && check_to_show_dialog_called == 1 ) {
		return false;
	}
	check_to_show_dialog_called = 1;
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'get_check_to_show_dialog_wptc',
	}, function(data) {
		delete check_to_show_dialog_called;
		try{
			data = jQuery.parseJSON(data);
		} catch(err){
			return ;
		}
		if (typeof data != 'undefined') {
			var is_backup_running = 0;
			if (data['is_backup_running'] == 'yes') {
				is_backup_running = 1;
			}
			if ( (data['backup_before_update_setting'] == 'everytime' && direct_update === true) || data['backup_before_update_setting'] == 'always') {
				show_is_backup_dialog_box_tc_wptc(obj, 'always', update_items, update_ptc_type, is_backup_running);
			} else {
				if ((typeof obj.attr("href") != 'undefined') && obj.attr("href") != '') {
					current_update_action = 'no'; //this global variable is used only for upgrade-input related updates; continuing update after backup process
					jQuery(obj).click();
					// parent.location.assign(obj.attr("href"));
				} else {
					current_update_action = 'no'; //this global variable is used only for upgrade-input related updates; continuing update after backup process
					jQuery(obj).click();
				}
			}
		}
	});
}

function show_is_backup_dialog_box_tc_wptc(obj, direct_backup, update_items, update_ptc_type, is_backup_running) {
	remove_other_thickbox_wptc();
	// jQuery('.notice, #update-nag').remove();
	jQuery('.TB_window').removeClass('thickbox_loading');
	jQuery('#TB_load').remove();
	jQuery('#TB_window').html('');
	//this function shows the dialog box to choose backup before updating
	jQuery("#wptc-content-id").remove();
	jQuery(".wrap").append('<div id="wptc-content-id" style="display:none;"> <p> hidden cont. </p></div><a class="thickbox wptc-thickbox" style="display:none" href="#TB_inline?width=500&height=500&inlineId=wptc-content-id&modal=true"></a>');
	//store the update link in a global variable
	update_click_obj_wptc = obj;
	if (update_ptc_type == 'theme') {
		update_obj_type_wptc = 'theme';
		update_required_theme_wptc = update_items[0];
	} else{
		update_obj_type_wptc = 'not_theme';
		update_required_theme_wptc = '';
	}

	this_update_link = obj.attr("href");

	if (this_update_link) {
		new_updated_action = 'href=' + obj.attr('href') + '';
	} else {
		current_update_action = "no";
		new_updated_action = "id='no_backup_just_update'";
	}

	if (is_backup_running) {
		swal({
			title              : wptc_get_dialog_header('Updating ' + update_ptc_type),
			html               : wptc_get_dialog_body('A backup is currently running, please wait for it to complete before you initiate backup and update', ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : true,
			confirmButtonColor : '',
			cancelButtonColor  : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			cancelButtonClass  : 'button-secondary wtpc-button-secondary',
			confirmButtonText  : 'Just update without backup',
			cancelButtonText   : 'Cancel',
		}).then(function () {
			clear_upgrade_after_backup_flags_wptc();
			if(typeof obj.attr('href') != 'undefined'){
				parent.location.assign(obj.attr('href'));
			} else {
				jQuery(obj).click();
			}
		}, function (dismiss) {
			return ;
		});
	} else if ((typeof direct_backup != 'undefined') && (direct_backup == 'always')) {
		jQuery(obj).addClass('disabled button-disabled-bbu-wptc');
		start_backup_bbu_wptc(obj, update_items, update_ptc_type);
		return false;
	}
}

function clear_upgrade_after_backup_flags_wptc(){
	jQuery.post(ajaxurl, {
		security : wptc_ajax_object.ajax_nonce,
		action   : 'clear_upgrade_after_backup_flags_wptc',
	}, function(data) {

		// try{
		// 	data = jQuery.parseJSON(data);
		// 	if (data.status === 'success') {
		// 		// jQuery('#wptc-fileupload-show').hide();
		// 		// jQuery('#wptc-fileupload').show();
		// 	}
		// } catch(err){

		// }
	});
}
