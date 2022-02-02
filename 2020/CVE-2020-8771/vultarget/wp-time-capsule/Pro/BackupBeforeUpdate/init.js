jQuery(document).ready(function($) {
	if (window.location.href.indexOf('themes.php?theme=') !== -1) {
		listen_theme_more_info_model_wptc();
		listen_theme_more_info_model_wptc_before_4_3_wptc();
	}
	// same as you'd pass it to bind()
	// [fn] is the handler function
	jQuery.fn.bindFirst = function(name, fn) {
		// bind as you normally would
		// don't want to miss out on any jQuery magic
		this.on(name, fn);

		// Thanks to a comment by @Martin, adding support for
		// namespaced events too.
		this.each(function() {
			var handlers = jQuery._data(this, 'events')[name.split('.')[0]];
			// take out the handler we just inserted from the end
			var handler = handlers.pop();
			// move it at the beginning
			handlers.splice(0, 0, handler);
		});
	};

	jQuery("#plugin_update_from_iframe, #plugin-information-footer a").bindFirst('click', function(e) {
		handle_iframe_requests_wptc(this, e , false);
	});

	jQuery('body').on("click", '.plugin-update-from-iframe-bbu-wptc', function(e) {
		handle_iframe_requests_wptc(this, e , true);
	});

	//Update page theme and plugin update also plugin normal update
	jQuery(".plugin-update-tr .update-message a, .update-now.button, .upgrade .button, .plugin-action-buttons .update-now").on("click", function(e) {
		var thisObjW23 = jQuery(this);
		if (!thisObjW23.hasClass('thickbox')) {
			if ( thisObjW23.hasClass('update-link') 
				|| thisObjW23.parents('td').hasClass('plugin-update') 
				|| ( thisObjW23.parents('ul').hasClass('plugin-action-buttons') && thisObjW23.hasClass('update-now') ) ) {

				if (window.location.href.toLowerCase().indexOf('themes.php') !== -1){
					handle_plugin_themes_link_request_wptc(this, e, false, false , 'theme');
				} else {
					handle_plugin_themes_link_request_wptc(this, e, false, false , 'plugin');
				}
			} else if (this.id.toLowerCase().indexOf('upgrade-plugins') !== -1){
				handle_plugin_upgrade_request_wptc(this, e, false);
			} else if (this.id.toLowerCase().indexOf('upgrade-themes') !== -1){
				handle_themes_upgrade_request_wptc(this, e, false);
			}
		}
	});


	//Plugins page bulk update
	jQuery("#bulk-action-form .button, .bulkactions .button").on("click", function(e) {
		if(window.location.href.toLowerCase().indexOf('plugins.php') !== -1 && jQuery(this).prev('select').val().toLowerCase().indexOf('update') !== -1){
			if (is_current_update_action_set_wptc()) {
				clear_current_update_action_wptc();
			} else {
				if(jQuery(this).prev('select').val().toLowerCase().indexOf('update') !== -1){
					handle_plugin_themes_button_action_request_wptc(this, e , false, false, 'plugin');
				}
			}
		}

		if(window.location.href.toLowerCase().indexOf('themes.php') !== -1 && jQuery(this).prev('select').val().toLowerCase().indexOf('update') !== -1){
			if (is_current_update_action_set_wptc()) {
				clear_current_update_action_wptc();
			} else {
				if(jQuery(this).prev('select').val().toLowerCase().indexOf('update') !== -1){
					handle_plugin_themes_button_action_request_wptc(this, e , false, false, 'theme');
				}
			}
		}
	});

	//core update
	jQuery("form #upgrade").on("click", function(e) {
		handle_core_upgrade_request_wptc(this, e , false);
	});

	//translation update
	jQuery('form.upgrade .button').on("click", function(e) {
		handle_translation_upgrade_request_wptc(this, e, false);
	});

	//theme update
	setTimeout(function (){
		jQuery('.theme-screenshot , .more-details, .theme-update').on("click", function(e) {
			listen_theme_more_info_model_wptc();
			listen_theme_more_info_model_wptc_before_4_3_wptc();
		});
	}, 500);

	//wordpress 4.5
	jQuery('body').on("click", '.theme-info .notice-warning a', function(e) {
		if (!jQuery(this).hasClass('thickbox')) {
			if (is_current_update_action_set_wptc()) {
				clear_current_update_action_wptc();
			} else {
				prevent_action_propagation_wptc(e);
				var update_items = [];
				update_items.push(jQuery(this).attr('data-slug'));
				if (update_items.length > 0) {
					new_theme_update_listener = 1;
					check_to_show_dialog_wptc(jQuery(this), update_items, 'theme');
				}
			}
		}
	});

	//registering the events
	jQuery("body").on("click" , ".tc_backup_before_update", function(e) {
		if (jQuery(this).hasClass('disabled')) {
			return false;
		}
		jQuery('#TB_window').removeClass('thickbox-loading');
		var checkbox_selected = jQuery('input[name="backup_before_update"]:checked').val();
		if (checkbox_selected == 'on') {
			start_manual_backup_wptc(wptc_bbu_obj, 'from_bbu', wptc_update_items, wptc_update_ptc_type, 'always');
		} else {
			start_manual_backup_wptc(wptc_bbu_obj, 'from_bbu', wptc_update_items, wptc_update_ptc_type);
		}
	});

	// jQuery("body").on("click", ".tc_no_backup, .tc_no_backup#no_backup_just_update, #no_backup_just_update", function(e) {
	// 	prevent_action_propagation_wptc(e);
	// 	if (wptc_update_ptc_type == 'theme' && typeof new_theme_update_listener != 'undefined' && new_theme_update_listener == 1) {
	// 			jQuery.each(_wpThemeSettings.themes, function( key, value ) {
	// 				if(value.id == update_required_theme_wptc){
	// 				   parent.location.assign(jQuery(value.update).find("#update-theme").attr('href'));
	// 				}
	// 			});
	// 	}
	// 	var checkbox_selected = jQuery('input[name="backup_before_update"]:checked').val();
	// 	if (checkbox_selected == 'on') {
	// 		update_backup_before_update_never();
	// 	}
	// 	if (typeof wptc_this_update_link != "undefined" && wptc_this_update_link != '' && wptc_this_update_link != "undefined") {
	// 		parent.location.assign(wptc_this_update_link);
	// 	} else {
	// 		tb_remove();
	// 		current_update_action = 'no'; //this global variable is used only for upgrade-input related updates; continuing update after backup process
	// 		jQuery(wptc_bbu_obj).click();
	// 	}
	// });

	jQuery("body").on("click", ".dialog_close" ,function() {
		tb_remove();
	});

	jQuery("#enable_auto_update_wptc").on("click", function() {
		jQuery(".enable_auto_update_options_wptc").show();
	});

	jQuery("#disable_auto_update_wptc").on("click", function() {
		jQuery(".enable_auto_update_options_wptc").hide();
	});

	jQuery("input[name=wptc_auto_plugins]:checkbox").change(function(){
		jQuery('#wptc_auto_update_plugins_dw, #wptc-select-all-plugins-au, #wptc-include-new-plugins-au').hide();
		if(jQuery(this).is(':checked')){
			jQuery('#wptc_auto_update_plugins_dw, #wptc-select-all-plugins-au, #wptc-include-new-plugins-au').show();
			fancy_tree_init_auto_update_plugins_wptc();
		}
	});

	//Do not listen manual settings to auto
	// jQuery("input:radio[name=backup_before_update_setting]").change(function(){
		// if(jQuery(this).val() === 'always'){
		// 	jQuery('#auto_update_settings_wptc').show();
		// } else {
		// 	jQuery('#auto_update_settings_wptc').hide();
		// }
	// });

	jQuery("input[name=wptc_auto_themes]:checkbox").change(function(){
		jQuery('#wptc_auto_update_themes_dw, #wptc-select-all-themes-au, #wptc-include-new-themes-au').hide();
		if(jQuery(this).is(':checked')){
			jQuery('#wptc_auto_update_themes_dw, #wptc-select-all-themes-au, #wptc-include-new-themes-au').show();
			fancy_tree_init_auto_update_themes_wptc();
		}
	});

	if(jQuery("input[name=wptc_auto_plugins]").is(':checked')){
		jQuery('#wptc_auto_update_plugins_dw, #wptc-select-all-plugins-au, #wptc-include-new-plugins-au').show();
		fancy_tree_init_auto_update_plugins_wptc();
	}

	if(jQuery("input[name=wptc_auto_themes]").is(':checked')){
		jQuery('#wptc_auto_update_themes_dw, #wptc-select-all-themes-au, #wptc-include-new-themes-au').show();
		fancy_tree_init_auto_update_themes_wptc();
	}

	jQuery("body").on("click", "#wptc-select-all-plugins-au, #wptc-select-all-themes-au, #wptc-include-new-plugins-au, #wptc-include-new-themes-au" ,function(e) {

		var current_id = jQuery(this).attr('id');

		if (current_id  === 'wptc-select-all-plugins-au') {
			var tree = jQuery('#wptc_auto_update_plugins_dw').fancytree('getTree');
		} else if(current_id === 'wptc-select-all-themes-au') {
			var tree = jQuery('#wptc_auto_update_themes_dw').fancytree('getTree');
		} else {
			var tree = false;
		}

		if ( !jQuery(this).hasClass('fancytree-selected') ) {

			jQuery(this).addClass('fancytree-selected');

			if (!tree) {
				return ;
			}

			if (!jQuery.isFunction(tree.getDeSelectedNodes)) {
				return ;
			}

			jQuery.each( tree.getDeSelectedNodes(), function( key, value ) {
				value.setSelected(true);
			});


			return ;
		}

		jQuery(this).removeClass('fancytree-selected');

		if (!tree) {
			return ;
		}

		if (!jQuery.isFunction(tree.getSelectedNodes)) {
			return ;
		}

		jQuery.each( tree.getSelectedNodes(), function( key, value ) {
			value.setSelected(false);
		});

	});

	jQuery("body").on("click", ".upgrade-plugins-bbu-wptc" ,function(e) {
		handle_plugin_upgrade_request_wptc(this, e , true);
	});

	jQuery("body").on("click", ".update-link-plugins-bbu-wptc, .update-now-plugins-bbu-wptc" ,function(e) {
		handle_plugin_themes_link_request_wptc(this, e, true, false , 'plugin');
	});

	jQuery("body").on("click", ".update-link-themes-bbu-wptc, .update-now-themes-bbu-wptc" ,function(e) {
		handle_plugin_themes_link_request_wptc(this, e, true, false , 'theme');
	});

	jQuery("body").on("click", ".button-action-plugins-bbu-wptc" ,function(e) {
		handle_plugin_themes_button_action_request_wptc(this, e , true, false, 'plugin');
	});

	jQuery("body").on("click", ".button-action-themes-bbu-wptc" ,function(e) {
		handle_plugin_themes_button_action_request_wptc(this, e , true, false, 'theme');
	});


	jQuery("body").on("click", ".upgrade-themes-bbu-wptc" ,function(e) {
		handle_themes_upgrade_request_wptc(this, e , true);
	});

	jQuery("body").on("click", ".upgrade-core-bbu-wptc" ,function(e) {
		handle_core_upgrade_request_wptc(this, e , true);
	});

	jQuery("body").on("click", ".upgrade-translations-bbu-wptc" ,function(e) {
		handle_translation_upgrade_request_wptc(this, e , true);
	});
});

function fancy_tree_init_auto_update_plugins_wptc(){
	jQuery("#wptc_auto_update_plugins_dw").fancytree({
		checkbox: true,
		selectMode: 2,
		icon:true,
		debugLevel:0,
		source: {
			url: ajaxurl,
			data: {
				"action": "get_installed_plugins_wptc",
				security: wptc_ajax_object.ajax_nonce,
			},
		},
		init: function (event, data) {
			data.tree.getRootNode().visit(function (node) {
				if (node.data.preselected) node.setSelected(true);
			});
		},
		select: function(event, data) {
			// Get a list of all selected nodes, and convert to a key array:
			var selKeys = jQuery.map(data.tree.getSelectedNodes(), function(node){
				return node.key;
			});
			jQuery("#auto_include_plugins_wptc").val(selKeys.join(","));
		},
		dblclick: function(event, data) {
			data.node.toggleSelected();
		},
		keydown: function(event, data) {
			if( event.which === 32 ) {
				data.node.toggleSelected();
				return false;
			}
		},
		cookieId: "fancytree-Cb3",
		idPrefix: "fancytree-Cb3-"
	});
}

function fancy_tree_init_auto_update_themes_wptc(){
	jQuery("#wptc_auto_update_themes_dw").fancytree({
		checkbox: true,
		selectMode: 2,
		icon:true,
		debugLevel:0,
		source: {
			url: ajaxurl,
			security: wptc_ajax_object.ajax_nonce,
			data: {
				"action": "get_installed_themes_wptc",
				security: wptc_ajax_object.ajax_nonce,
			},
		},
		init: function (event, data) {
			data.tree.getRootNode().visit(function (node) {
				if (node.data.preselected) node.setSelected(true);
			});
		},
		select: function(event, data) {
			var selKeys = jQuery.map(data.tree.getSelectedNodes(), function(node){
				return node.key;
			});
			jQuery("#auto_include_themes_wptc").val(selKeys.join(","));
		},
		dblclick: function(event, data) {
			data.node.toggleSelected();
		},
		keydown: function(event, data) {
			if( event.which === 32 ) {
				data.node.toggleSelected();
				return false;
			}
		},
		cookieId: "fancytree-Cb3",
		idPrefix: "fancytree-Cb3-"
	});
}


function listen_theme_more_info_model_wptc_before_4_3_wptc(){
	setTimeout(function (){
		jQuery('.theme-info .theme-update-message a').on("click", function(e) {
			if (!jQuery(this).hasClass('thickbox')) {
				if (is_current_update_action_set_wptc()) {
					clear_current_update_action_wptc();
				} else {
					prevent_action_propagation_wptc(e);
					var update_items = [];
					if (window.location.href.match(/theme=([^&]+)/) && window.location.href.match(/theme=([^&]+)/)[1]) {
						update_items.push(window.location.href.match(/theme=([^&]+)/)[1]);
					} else if(jQuery(this).attr('data-slug') != undefined){
						update_items.push(jQuery(this).attr('data-slug'));
					}
					if (update_items.length > 0) {
						new_theme_update_listener = 1;
						check_to_show_dialog_wptc(jQuery(this), update_items, 'theme');
					}
				}
			}
		});
	}, 500);
}

function listen_theme_more_info_model_wptc(){
	setTimeout(function (){
		jQuery('#update-theme').on("click", function(e) {
			if (!jQuery(this).hasClass('thickbox')) {
				if (is_current_update_action_set_wptc()) {
					clear_current_update_action_wptc();
				} else {
					prevent_action_propagation_wptc(e);
					var update_items = [];
					update_items.push(jQuery(this).attr('data-slug'));
					if (update_items.length > 0) {
						new_theme_update_listener = 1;
						check_to_show_dialog_wptc(jQuery(this), update_items, 'theme');
					}
				}
			}
		});
		get_current_backup_status_wptc(); //to add extra buttons for bbu
	}, 500);
}

function start_backup_bbu_wptc(obj, update_items, update_ptc_type){
	window.parent.start_manual_backup_wptc(obj, 'from_bbu', update_items, update_ptc_type);
	jQuery(window.parent.document.body).find('TB_overlay').remove();
	jQuery(window.parent.document.body).find('TB_window').remove();
}

function push_bbu_button_wptc(data) {

	if(typeof data != 'undefined' && !data.is_backup_tab_allowed_with_admin_user_check){

		return ;
	}

	if(data.bbu_setting_status === 'everytime'){
		if(Object.keys(data.backup_progress).length > 0){
			bbu_buttons_wptc(true);
		} else {
			bbu_buttons_wptc(false);
		}
	} else if(data.bbu_setting_status === 'always'){
	} else {
	}
}

function bbu_buttons_wptc(backup_running){
	var extra_classs = '';
	if(backup_running){
		var extra_classs = 'disabled button-disabled-bbu-wptc';
	}
	var current_path = window.location.href;
	if (current_path.toLowerCase().indexOf('update-core') !== -1) {

		if (!wptc_is_allowed_to_show_extra_buttons()) {	return ; }

		jQuery('.upgrade-plugins-bbu-wptc , .upgrade-themes-bbu-wptc , .upgrade-translations-bbu-wptc, .upgrade-core-bbu-wptc, .plugin-update-from-iframe-bbu-wptc').remove();
		var update_plugins = '&nbsp;<input class="upgrade-plugins-bbu-wptc button '+extra_classs+'" type="submit" value="Backup and update">';
		var update_themes = '&nbsp;<input class="upgrade-themes-bbu-wptc button  '+extra_classs+'" type="submit" value="Backup and update">';
		var update_translations = '&nbsp;<input class="upgrade-translations-bbu-wptc button  '+extra_classs+'" type="submit" value="Backup and update">';
		var update_core = '&nbsp;<input type="submit" class="upgrade-core-bbu-wptc button button regular  '+extra_classs+'" value="Backup and update">';
		var iframe_update = '<a class="plugin-update-from-iframe-bbu-wptc button button-primary right  '+extra_classs+'" style=" margin-right: 10px;">Backup and update</a>';
		jQuery('form[name=upgrade-plugins]').find('input[name=upgrade]').after(update_plugins);
		jQuery('form[name=upgrade-themes]').find('input[name=upgrade]').after(update_themes);
		jQuery('form[name=upgrade]').find('input[name=upgrade]').after(update_core);
		jQuery('form[name=upgrade-translations]').find('input[name=upgrade]').after(update_translations);
		setTimeout(function(){
			jQuery("#TB_iframeContent").contents().find(".plugin-update-from-iframe-bbu-wptc").remove();
			jQuery("#TB_iframeContent").contents().find("#plugin_update_from_iframe").after(iframe_update);
			add_tool_tip_bbu_wptc(true);
		}, 5000);
	} else if(current_path.toLowerCase().indexOf('plugins.php') !== -1){

		if (!wptc_is_allowed_to_show_extra_buttons()) {	return ; }

		jQuery('.wptc-span-spacing-bbu , .update-link-plugins-bbu-wptc , .button-action-plugins-bbu-wptc').remove();
		var in_app_update = '<span class="wptc-span-spacing-bbu">&nbsp;or</span> <a href="#" class="update-link-plugins-bbu-wptc '+extra_classs+'">Backup and update</a>';
		var selected_update = '<span class="wptc-span-spacing-bbu">&nbsp;</span><input type="submit" class="button-action-plugins-bbu-wptc button  '+extra_classs+'" value="Backup and update">';
		var iframe_update = '<a class="plugin-update-from-iframe-bbu-wptc button button-primary right  '+extra_classs+'" style=" margin-right: 10px;">Backup and update</a>';
		jQuery('form[id=bulk-action-form]').find('.update-link').after(in_app_update);
		jQuery('form[id=bulk-action-form]').find('.button.action').after(selected_update);
		setTimeout(function(){
			jQuery("#TB_iframeContent").contents().find(".plugin-update-from-iframe-bbu-wptc").remove();
			jQuery("#TB_iframeContent").contents().find("#plugin_update_from_iframe").after(iframe_update);
			add_tool_tip_bbu_wptc(true);
		}, 5000);
	} else if(current_path.toLowerCase().indexOf('plugin-install.php') !== -1){

		if (!wptc_is_allowed_to_show_extra_buttons()) {	return ; }

		jQuery('.update-now-plugins-bbu-wptc, .plugin-update-from-iframe-bbu-wptc').remove();
		var in_app_update = '<li><a class="button update-now-plugins-bbu-wptc '+extra_classs+'" href="#">Backup and update</a></li>';
		var iframe_update = '<a class="plugin-update-from-iframe-bbu-wptc button button-primary right  '+extra_classs+'" style=" margin-right: 10px;">Backup and update</a>';
		setTimeout(function(){
			jQuery("#TB_iframeContent").contents().find(".plugin-update-from-iframe-bbu-wptc").remove();
			jQuery("#TB_iframeContent").contents().find("#plugin_update_from_iframe").after(iframe_update);
			add_tool_tip_bbu_wptc(true);
		}, 5000);
		jQuery('.plugin-action-buttons .update-now.button').parents('.plugin-action-buttons').append(in_app_update);
	} else if(current_path.toLowerCase().indexOf('themes.php') !== -1){

		if (!wptc_is_allowed_to_show_extra_buttons()) {	return ; }

		jQuery('.button-link-themes-bbu-wptc, .button-action-themes-bbu-wptc , #update-theme-bbu-wptc, .wptc-span-spacing-bbu, .button-link-themes-bbu-wptc').remove();
		var in_app_update = '<span class="wptc-span-spacing-bbu">&nbsp;or</span><button class="button-link-themes-bbu-wptc button-link '+extra_classs+'" type="button">Backup and update</button>';
		var selected_update = '<span class="wptc-span-spacing-bbu">&nbsp;</span><input type="submit" class="button-action-themes-bbu-wptc button  '+extra_classs+'" value="Backup and update">';
		var popup_update = '<span class="wptc-span-spacing-bbu">&nbsp;or</span> <a href="#" id="update-theme-bbu-wptc" class=" '+extra_classs+'">Backup and update</a>';
		jQuery('.button-link[type=button]').not('.wp-auth-check-close, .button-link-themes-staging-wptc').after(in_app_update);
		jQuery('form[id=bulk-action-form]').find('.button.action').after(selected_update);
		jQuery('#update-theme').after(popup_update);

		if (wptc_is_multisite) {
			jQuery('.wptc-span-spacing-bbu , .update-link-themes-bbu-wptc').remove();
			var in_app_update = '<span class="wptc-span-spacing-bbu">&nbsp;or</span> <a href="#" class="update-link-themes-bbu-wptc '+extra_classs+'">Backup and update</a>';
			jQuery('form[id=bulk-action-form]').find('.update-link').after(in_app_update);
		}
	}
	setTimeout(function (){
		jQuery('.theme').on('click', '.button-link-themes-bbu-wptc , #update-theme', function(e) {
			handle_theme_button_link_request_wptc(this, e, true);
		});
	}, 1000);

	setTimeout(function (){
		jQuery('#update-theme-bbu-wptc').on('click', function(e) {
			handle_theme_link_request_wptc(this, e, true);
		});
	}, 500);
	if(backup_running){
		add_tool_tip_bbu_wptc();
	}
}

function add_tool_tip_bbu_wptc(iframe){
	var class_bbu = ".button-disabled-bbu-wptc";
	// if(iframe){
	// 	class_bbu = jQuery("#TB_iframeContent").contents().find(".button-disabled-bbu-wptc");
	// }
	jQuery(class_bbu).each(function(tagElement , key) {
		jQuery(key).opentip('Backup in progress. Please wait until it finishes', { style: "dark" });
	});
}

function bbu_message_update_progress_bar_wptc(data){
	if (data.backup_before_update_progress) {
		var update_message = ''
		if (data.backup_before_update_progress == 'core') {
			update_message = 'wordpress';
		} else {
			update_message = data.backup_before_update_progress;
		}
		// jQuery('.bp_progress_bar').text('Updating '+ update_message +'...');
		backup_before_update = 'yes';
	} else if (data.meta_data_backup_process) {
		// jQuery('.bp_progress_bar').text('Backing up meta data...');
	}
}

function clear_bbu_notes_wptc() {
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'clear_bbu_notes_wptc',
	}, function(data) {
	});
}
