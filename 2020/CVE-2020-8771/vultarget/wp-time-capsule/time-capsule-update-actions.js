backupclickProgress = false;
last_cron_triggered_time_js_wptc = '';
jQuery(document).ready(function($) {

	get_current_backup_status_wptc();

	jQuery(document).keyup(function(e) {
		if(e.which == 27){
			dialog_close_wptc();
		}
	});
	status_area_wptc = "#bp_progress_bar_note, #staging_progress_bar_note";

	jQuery('body').on('click', '.sub_tree_class', function (){
		if (jQuery(this).hasClass('sub_tree_class') == true) {
			if (!jQuery(this).hasClass('selected')) {
				jQuery(this).addClass('selected');
				var this_file_name = jQuery(this).find('.folder').attr('file_name');
				jQuery.each( jQuery(this).nextAll(), function( key, value ) {
					jQuery.each( jQuery(value).find('.this_leaf_node'), function( key1, value1) {
						var parent_dir = jQuery(value1).find('.file_path').attr('parent_dir');
							if(jQuery(value1).hasClass('this_leaf_node') == true && parent_dir.indexOf(this_file_name) != -1 ){
								jQuery(value1).find('li').addClass('selected');
							}
					});
					jQuery.each( jQuery(value).find('.sub_tree_class'), function( key2, value2) {
						jQuery(value2).addClass('selected');
					});
				});
			} else {
				jQuery(this).removeClass('selected');
				jQuery.each( jQuery(this).nextAll(), function( key, value ) {
					jQuery.each( jQuery(value).find('.this_leaf_node'), function( key1, value1) {
							if(jQuery(value1).hasClass('this_leaf_node') == true){
								jQuery(value1).find('li').removeClass('selected');
							}
					});
					jQuery.each( jQuery(value).find('.sub_tree_class'), function( key2, value2) {
						jQuery(value2).removeClass('selected');
					});
				});
			}
		}
		if(jQuery(this).parents('.bu_files_list_cont').find('.selected').length > 0){
			jQuery(this).parents('.bu_files_list_cont').parent().find('.this_restore').removeClass('disabled');
		} else {
			jQuery(this).parents('.bu_files_list_cont').parent().find('.this_restore').addClass('disabled');
		}
	});

	jQuery('body').on('click', '.this_leaf_node li', function (){
		if(!jQuery(this).hasClass('selected')){
			jQuery(this).addClass('selected');
		} else {
			jQuery(this).removeClass('selected');
		}
		if(jQuery(this).parents('.bu_files_list_cont').find('.selected').length > 0){
				jQuery(this).parents('.bu_files_list_cont').parent().find('.this_restore').removeClass('disabled');
		} else {
				jQuery(this).parents('.bu_files_list_cont').parent().find('.this_restore').addClass('disabled');
		}
	});

	jQuery("#form_report_close, .close").on("click", function() {
		tb_remove();
	});

	jQuery("body").on("click", ".notice-dismiss", function() {
		jQuery('.notice, #update-nag').remove();
	});

	jQuery(".test_cron_wptc").on("click", function() {
		test_connection_wptc_cron();
	});

	jQuery('body').on('click', '.dialog_close, .close', function (){
		if (!jQuery(this).hasClass('no_exit_restore_wptc')) {
			dialog_close_wptc();
		}
	});

	jQuery("#show_file_db_exp_for_exc").on("click", function() {
	   change_init_setup_button_state_wptc();
	});

	jQuery("body").on("click", "#cancel_issue", function() {
		tb_remove();
	});

	jQuery("body").on("click", "#cancel_issue_notice", function() {
		mark_update_pop_up_shown_wptc();
		tb_remove();
	});

	jQuery("body").on("click", "#refresh-c-status-area-wtpc", function() {
		if (jQuery(this).hasClass('disabled')) {
			return false;
		}
		get_current_backup_status_wptc();
	});

	jQuery(".report_issue_wptc").on('click', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();

		var log_id = $(this).attr('id');

		swal({
			title              :  wptc_get_dialog_header('Add description'),
			input 			   : 'textarea',
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			confirmButtonColor : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			confirmButtonText  : 'Submit',
			width  			   : 400,
			showLoaderOnConfirm: true,
			preConfirm: function (description) {
				return new Promise(function (resolve, reject) {
					if (description) {
						return send_report_issue_wptc(description, log_id);
					} else {
						return send_report_issue_wptc('', log_id);
					}
				})
			},
			allowOutsideClick: true
		});

	});

	jQuery("#wptc_clear_log").on('click', function() {

		swal({
			title              : wptc_get_dialog_header('Are you sure?'),
			html               : wptc_get_dialog_body('Are you sure you want to permanently delete these logs?', ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : true,
			confirmButtonColor : '',
			cancelButtonColor  : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			cancelButtonClass  : 'button-secondary wtpc-button-secondary',
			confirmButtonText  : 'Delete',
			cancelButtonText   : 'Cancel',
			}).then(function () {
				yes_delete_logs_wptc();
			}, function (dismiss) {

			}
		);

	});

	jQuery('body').on('click', '.resume_backup_wptc', function() {
		resume_backup_wptc();
	});

	jQuery('body').on('click', '.close-image-wptc', function() {
		jQuery(this).remove();
	});

	jQuery('body').on('click', '#wptc_make_this_original_site', function() {
		var old_url = jQuery('#wptc_old_connected_site_url').text();
		var new_url = jQuery('#wptc_new_connected_site_url').text();

		swal({
			title              : wptc_get_dialog_header('Are you sure?'),
			html               : wptc_get_dialog_body('The site <strong>' + old_url + '</strong> will be replaced to <strong>' + new_url + '</strong> in WP Time Capsule and don\'t worry all your exisiting backups are safe.', ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : true,
			confirmButtonColor : '',
			cancelButtonColor  : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			cancelButtonClass  : 'button-secondary wtpc-button-secondary',
			confirmButtonText  : 'Replace',
			cancelButtonText   : 'Cancel',

		}).then(function () {
			swal({
				title              : wptc_get_dialog_header('Success'),
				html               : wptc_get_dialog_body('Site URL has been replaced successfully!', 'success'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			})
				make_this_original_site_wptc();
				jQuery('#wptc-notice').remove();
			}, function (dismiss) {
				//
			}
		);

	});

	jQuery('body').on('click', '#wptc_make_this_fresh_site', function() {
		var old_url = jQuery('#wptc_old_connected_site_url').text();
		var new_url = jQuery('#wptc_new_connected_site_url').text();

		swal({
			title              : wptc_get_dialog_header('Are you sure?'),
			html               : wptc_get_dialog_body('All the backups will be erased on this site and you need to go through the initial setup once again on this site, meanwhile <strong>' + old_url + '</strong> will be working just as fine. ', ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : true,
			confirmButtonColor : '',
			cancelButtonColor  : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			cancelButtonClass  : 'button-secondary wtpc-button-secondary',
			confirmButtonText  : 'Clear everything',
			cancelButtonText   : 'Cancel',
		}).then(function () {
				swal({
					title              : wptc_get_dialog_header('This site is ready for backups!'),
					html               : wptc_get_dialog_body(' All backups are reset. Please go through the initial setup process to enable backup on this site.', 'success'),
					padding            : '0px 0px 10px 0',
					buttonsStyling     : false,
					showCancelButton   : false,
					confirmButtonColor : '',
					confirmButtonClass : 'button-primary wtpc-button-primary',
					confirmButtonText  : 'Ok',
				})
				make_this_fresh_site_wptc();
				jQuery('#wptc-notice').remove();
			}, function (dismiss) {
				//
			}
		);

	});
});

function dialog_close_wptc(){
		tb_remove();
		if (backupclickProgress) {
			backupclickProgress = false;
		}
		if (typeof update_click_obj_wptc != 'undefined' && update_click_obj_wptc) {
			parent.location.assign(parent.location.href);
		}
}

function disable_refresh_button_wptc(){
	jQuery('#refresh-c-status-area-wtpc, #refresh-s-status-area-wtpc').css('opacity', '0.5').addClass('disabled');
}

function enable_refresh_button_wptc(){
	jQuery('#refresh-c-status-area-wtpc, #refresh-s-status-area-wtpc').css('opacity', '1').removeClass('disabled');
}

function get_sibling_files_wptc(obj){
	var file_name = jQuery(obj).attr('file_name');
	var backup_id = jQuery(obj).attr('backup_id');
	var recursive_count = parseInt(jQuery(obj).parent().siblings('.this_leaf_node').attr('recursive_count'));
	if(!recursive_count){
		recursive_count = parseInt((jQuery(obj).parent().attr('recursive_count'))) + 1;
	} else {
		recursive_count += 1;
	}
	last_lazy_load = obj;
	pushed_to_dom = 0;
	var trigger_filename = jQuery(obj).attr('file_name');
	var current_filename = '';
	var current_recursive_count = jQuery(obj).parents('.sub_tree_class').attr('recursive_count');
	jQuery.each( jQuery(obj).parents('.sub_tree_class').siblings(), function( key, value ) {
		if(jQuery(value).attr('recursive_count') > current_recursive_count){
			var current_filename = jQuery(value).find('.file_path').attr('parent_dir');
			if (current_filename == undefined) {
				var current_filename = jQuery(value).find('.folder').attr('parent_dir');
			}
			if (current_filename != undefined && current_filename.indexOf(trigger_filename) != -1) {
			   jQuery(value).remove();
			}
		}
	});
	jQuery(obj).parents('.sub_tree_class').find('.this_leaf_node').remove();
	if(jQuery(obj).hasClass('open')){
		jQuery(obj).removeClass('open').addClass('close');
		return false;
	} else {
		jQuery(obj).removeClass('close').addClass('loader');
	}
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'get_sibling_files_wptc',
		data: { file_name: file_name, backup_id: backup_id, recursive_count:recursive_count},
	}, function(data) {
	   if (typeof pushed_to_dom != 'undefined' && pushed_to_dom == 0) {
			jQuery(obj).removeClass('loader close').addClass('open');
			jQuery(last_lazy_load).parent().after(data)
			styling_thickbox_wptc("");
			pushed_to_dom = 1;
	   }
		register_dialog_box_events_wptc();
		jQuery(obj).removeClass('disabled');
	});
}

function parse_wptc_response_from_raw_data_2(raw_response){
	//return substring closed by <wptc_head> and </wptc_head>
	return raw_response.split('<wptc_head>').pop().split('</wptc_head>').shift();
}

function get_current_backup_status_wptc(dont_push_button) {

	var is_wptc_page = 1;

	if (window.location.href.indexOf('wp-time-capsule') === -1) {
		var is_wptc_page = 0;
	}

	disable_refresh_button_wptc();
	dont_push_button_wptc = dont_push_button;
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'progress_wptc',
		is_wptc_page: is_wptc_page
	}, function(data) {

		enable_refresh_button_wptc();

		if (typeof data == 'undefined' || !data.length) {
			wptc_set_first_backup_auto_refresh_msec({});
			return false;
		}

		try{
			data = parse_wptc_response_from_raw_data_2(data);
			data = jQuery.parseJSON(data);
		} catch(err){
			return ;
		}

		wptc_set_first_backup_auto_refresh_msec(data);

		if (data == 0 || typeof data.error != 'undefined') {
			delete reloadFuncTimeout;
			return false;
		}

		is_backup_tab_allowed_with_admin_user_check = false;

		if( data.is_backup_tab_allowed_with_admin_user_check ){
			is_backup_tab_allowed_with_admin_user_check = true;
		}

		//Do not show users any notification now
		show_alerts_wptc = false;

		if( data.is_whitelabling_override ){
			show_alerts_wptc = true;
		}

		if( !data.is_whitelabel_active ){
			show_alerts_wptc = true;
		}

		if (data.admin_notices_wptc) {
			add_notice_wptc(data.admin_notices_wptc.msg, 1, data.admin_notices_wptc.status);
		}

		wptc_is_multisite = false;
		if (data.is_multisite) {
			wptc_is_multisite = true;
		}

		last_backup_time = data.last_backup_time;

		var progress_val = 0.0;
		var prog_con = '';
		var backup_progress = data.backup_progress;

		//Last backup taken
		if (typeof last_backup_time != 'undefined' && last_backup_time != null && last_backup_time) {
			// jQuery(status_area_wptc).text('Last backup taken : ' + last_backup_time );
		} else {
			// jQuery(status_area_wptc).text('No backups taken');
		}

		show_own_cron_status_wptc(data);

		wptc_show_first_backup_info(data);

		//Notify backup failed
		// if (data.start_backups_failed_server) {
			// wptc_backup_start_failed_note(data.start_backups_failed_server);
			// return false;
		// }

		//get backup type
		if (data.starting_first_backup != 'undefined' && data.starting_first_backup) {
			backup_type = 'starting_backup' ;
		} else {
			backup_type = 'manual_backup';
		}

		if (backup_progress != '') {
			jQuery('.bp-progress-calender').show();
			progress_val = backup_progress.progress_percent;

			//First backup progress bar
			prog_con = '<div class="bp_progress_bar_cont"><span id="bp_progress_bar_note"></span><div class="bp_progress_bar" style="width:' + progress_val + '%"></div></div><span class="rounded-rectangle-box-wptc reload-image-wptc" id="refresh-c-status-area-wtpc"></span><div class="last-c-sync-wptc">Last reload: Processing...</div>';

			wptc_backup_running = true;

			//Settings page UI
			disable_settings_wptc();
			disable_pro_button_wptc();
			showLoadingDivInCalendarBoxWptc(); //show calender page details
		} else {
			wptc_backup_running = false;

			var this_percent = 0;
			var thisCompletedText = 'Initiating the backup...';

			//Will show after backup completed things
			if (data.progress_complete) {
				if (typeof backup_type != 'undefined' && backup_type != '') { // change after backup completed
					if(typeof backup_started_time_wptc == 'undefined' || (backup_started_time_wptc + 7000) <= jQuery.now()){
						this_percent = 100;
						thisCompletedText = '<span style="top: 3px; font-size: 13px;  position: relative; left: 10px;">Backup Completed</span>';
					}

					//redirect once first backup is done
					if (backup_type == 'starting_backup') {
						backup_type = '';
						// parent.location.assign(adminUrlWptc+'admin.php?page=wp-time-capsule-monitor');
					} else if(backup_type == 'manual_backup'){ //once manual backup is done check for other stuffs like staging.
						backup_type = '';
						setTimeout(function() {
							// tb_remove();
							if(typeof update_click_obj_wptc != 'undefined'){
								delete update_click_obj_wptc;
							}
							// if (typeof is_staging_running_wptc !== 'undefined' && jQuery.isFunction(is_staging_running_wptc)) {
							//     is_staging_running_wptc(true);
							// }
							if(typeof backup_before_update != 'undefined' && backup_before_update == 'yes'){
								delete backup_before_update;
								tb_remove();
								// parent.location.assign(parent.location.href);
							}
						}, 3000);
					}
				}
			}

			//checking some Dom element to show whether backup text needs to shown or not
			if ((progress_val == 0) && (jQuery('.progress_bar').css('width') != '0px') && ( jQuery('.wptc_prog_wrap').length == 0 ||  (jQuery('.bp_progress_bar').css('width') != '0px' && jQuery('.wptc_prog_wrap').length != 0 && jQuery('.bp_progress_bar').css('width') != undefined))){
				this_percent = 100;
				progress_val = 100;
				thisCompletedText = '<span style="top: 3px; font-size: 13px;  position: relative; left: 10px;">Backup Completed</span>';
			}

			//Once backup completed then check for backup before updates
			if (thisCompletedText == '<span style="top: 3px; font-size: 13px;  position: relative; left: 10px;">Backup Completed</span>') {
				if (jQuery('#wptc-first-backup-tables-bar-title').html() != '0%' && jQuery('#wptc-first-backup-files-bar-title').html() != '0%') {
					if (window.location.href.indexOf('?page=wp-time-capsule&new_backup=set') !== -1 ){
						setTimeout(function(){
							parent.location.assign(adminUrlWptc+'admin.php?page=wp-time-capsule-monitor');
						}, 3000)
					}
				}
				if(jQuery(".this_modal_div .backup_progress_tc .progress_cont").text().indexOf('Updating') === -1){
					jQuery('.bp-progress-calender').hide();
					prog_con = '<div class="bp_progress_bar_cont"><span id="bp_progress_bar_note"></span><div class="bp_progress_bar" style="width:' + this_percent + '%">' + thisCompletedText + '</div></div><span class="rounded-rectangle-box-wptc reload-image-wptc" id="refresh-c-status-area-wtpc"></span><div class="last-c-sync-wptc">Last reload: Processing...</div>';
				} else {
					thisCompletedText = 'Updated successfully.';
					prog_con = '<div class="bp_progress_bar_cont"><span id="bp_progress_bar_note"></span><div class="bp_progress_bar" style="width:' + this_percent + '%">' + thisCompletedText + '</div></div><span class="rounded-rectangle-box-wptc reload-image-wptc" id="refresh-c-status-area-wtpc"></span><div class="last-c-sync-wptc">Last reload: Processing...</div>';
				}
			} else {
				jQuery('.bp-progress-calender').show();
			}

			enable_settings_wptc();
			enable_pro_button_wptc();
			resetLoadingDivInCalendarBoxWptc();
		}

		//show backup percentage in staging area also
		if (typeof show_backup_status_staging !== 'undefined' && jQuery.isFunction(show_backup_status_staging)) {
			show_backup_status_staging(backup_progress, progress_val);
		}

		jQuery('.wptc_prog_wrap').html('');
		jQuery('.wptc_prog_wrap').append(prog_con);
		if (jQuery('.l1.wptc_prog_wrap').hasClass('bp-progress-first-bp')) {
			jQuery('.bp_progress_bar_cont').addClass('bp_progress_bar_cont-first-b-wptc');
			jQuery('.rounded-rectangle-box-wptc').addClass('rounded-rectangle-box-wptc-first-c-wptc');
		}

		//backup before update showing data
		if (typeof bbu_message_update_progress_bar_wptc !== 'undefined' && jQuery.isFunction(bbu_message_update_progress_bar_wptc)) {
			bbu_message_update_progress_bar_wptc(data);
		}

		//Showing all the data here
		process_backup_status_wptc(backup_progress, progress_val);

		//Load new update pop up
		load_custom_popup_wptc(data.user_came_from_existing_ver , 'new_updates')

		//show users error if any
		show_users_backend_errors_wptc(data.show_user_php_error)

		//If staging running do not start backup
		stop_starting_new_backups_wptc(data);
		show_notification_bar_wptc(data);
		if (dont_push_button_wptc !== 1) {
			push_extra_button_wptc(data);
		} else {
			dont_push_button_wptc = 0;
		}
		update_backup_status_in_staging_wptc(data);
		update_last_sync_wptc();
		if (typeof process_wtc_reload !== 'undefined' && jQuery.isFunction(process_wtc_reload) && window.location.href.indexOf('page=wp-time-capsule-monitor') !== -1) {
			process_wtc_reload(data);
		}
	});
}


function wptc_set_first_backup_auto_refresh_msec(data){
	var sec = 1000 * 10;

	if (data && data.first_backup_auto_refresh_msec) {
		sec = data.first_backup_auto_refresh_msec;
	}

	if (typeof wptc_first_backup_auto_refresher != 'undefined') {
		return;
	}

	if (location.search && ~location.search.indexOf('page=wp-time-capsule&new_backup=set')) {
		wptc_first_backup_auto_refresher = setInterval(function() {
			get_current_backup_status_wptc();
		}, sec);
	}
}


function wptc_show_first_backup_info(data){
	if (!data || !data.backup_progress) {
		return wptc_set_first_backup_calculating_state();
	}

	wptc_show_first_backup_database_info(data);
	wptc_show_first_backup_files_info(data);
	wptc_show_first_backup_current_status_msg(data);
}

function wptc_set_first_backup_calculating_state(){
	jQuery('#wptc-first-backup-tables-size, #wptc-first-backup-files-size').html('calculating...');
}

function wptc_show_first_backup_current_status_msg(data){
	if (!data.backup_progress.current_state) {
		return wptc_set_first_backup_calculating_state();
	}

	jQuery('.wptc-loader').html(data.backup_progress.current_state.msg);
}

function wptc_show_first_backup_database_info(data){

	if (!data.backup_progress.db) {
		return wptc_set_first_backup_calculating_state();
	}

	jQuery('#wptc-first-backup-processed-tables-count').html(data.backup_progress.db.processed);
	jQuery('#wptc-first-backup-total-tables-count').html(data.backup_progress.db.overall);
	jQuery('#wptc-first-backup-tables-bar-title').html(data.backup_progress.db.percentage + '%');
	jQuery('#wptc-first-backup-tables-bar').css('width', data.backup_progress.db.percentage + '%');
	if (!data.backup_progress.db.processed || data.backup_progress.db.processed == 0 || !data.backup_progress.db.size || data.backup_progress.db.size === '0 B') {
		jQuery('#wptc-first-backup-tables-size').html('calculating...');
	} else{
		jQuery('#wptc-first-backup-tables-size').html(data.backup_progress.db.size);
	}
}

function wptc_show_first_backup_files_info(data){

	if (!data.backup_progress.files || !data.backup_progress.files.processed || !data.backup_progress.files.processing) {
		return ;
	}

	jQuery('#wptc-first-backup-processed-files-count').html(data.backup_progress.files.processed.current);
	jQuery('#wptc-first-backup-total-files-count').html(data.backup_progress.files.processing.overall);
	jQuery('#wptc-first-backup-files-bar-title').html(data.backup_progress.files.processing.percentage + '%');
	jQuery('#wptc-first-backup-files-bar').css('width',  data.backup_progress.files.processing.percentage + '%');

	if (!data.backup_progress.files.processed.current || data.backup_progress.files.processed.current == 0 || !data.backup_progress.files.processing.size || data.backup_progress.files.processing.size === '0 B') {
		jQuery('#wptc-first-backup-files-size').html('calculating...');
	} else{
		jQuery('#wptc-first-backup-files-size').html(data.backup_progress.files.processing.size);
	}
}

function update_last_sync_wptc(){
	jQuery('.last-c-sync-wptc, .last-s-sync-wptc').html('Last reload: '+gettime_wptc());
}

function stop_starting_new_backups_wptc(data){
	if (data.is_staging_running && data.is_staging_running == 1) {
		jQuery("#select_wptc_default_schedule, #wptc_timezone, #wptc_auto_update_schedule_time").addClass('disabled').attr('disabled', 'disabled');
		jQuery('#start_backup_from_settings').attr('action', 'disabled').addClass('disabled');
	}
}

function update_backup_status_in_staging_wptc(data){
	if (data.is_staging_running && data.is_staging_running == 1) {
		jQuery("#select_wptc_default_schedule, #wptc_timezone, #wptc_auto_update_schedule_time").addClass('disabled').attr('disabled', 'disabled');
		jQuery('#start_backup_from_settings').attr('action', 'disabled').addClass('disabled');
		jQuery('.change_dbox_user_tc').addClass('wptc-link-disabled');
		jQuery('.setting_backup_progress_note_wptc').show();
	}
}

function show_notification_bar_wptc(data){

	if (show_alerts_wptc === false) {
		return false;
	}

	if (data.bbu_note_view) {
		jQuery('.success-bar-wptc, .error-bar-wptc, .warning-bar-wptc, .message-bar-wptc').remove();
		jQuery('.success-bar-wptc, .error-bar-wptc, .warning-bar-wptc, .message-bar-wptc', window.parent.document).remove();
		if(jQuery("#wpadminbar").length > 0){
			var adminbar = "#wpadminbar";
			var iframe = false;
		} else {
			var adminbar = jQuery('#wpadminbar', window.parent.document);
			var iframe = true;
		}
		if (data.bbu_note_view.type === 'success') {
			jQuery(adminbar).after("<div style='display:none' class='success-bar-wptc success-image-wptc close-image-wptc'><span id='bar-note-wptc'>"+data.bbu_note_view.note+"</span></div>");
				setTimeout(function(){
					if(iframe){
					   if(!jQuery('.success-bar-wptc', window.parent.document).is(':visible')){
							jQuery('.success-bar-wptc', window.parent.document).slideToggle(); //sample
					   }
					} else {
					   if(!jQuery('.success-bar-wptc').is(':visible')){
							jQuery('.success-bar-wptc').slideToggle(); //sample
					   }
					}
				   if (typeof clear_bbu_notes_wptc !== 'undefined' && jQuery.isFunction(clear_bbu_notes_wptc)) {
						clear_bbu_notes_wptc();
					}
				}, 1000);
		} else if (data.bbu_note_view.type === 'error') {
			jQuery(adminbar).after("<div style='display:none' class='error-bar-wptc error-image-wptc close-image-wptc'><span id='bar-note-wptc'>"+data.bbu_note_view.note+"</span></div>");
				setTimeout(function(){
					if(iframe){
					   if(!jQuery('.error-bar-wptc', window.parent.document).is(':visible')){
							jQuery('.error-bar-wptc', window.parent.document).slideToggle(); //sample
					   }
					} else {
					   if(!jQuery('.error-bar-wptc').is(':visible')){
							jQuery('.error-bar-wptc').slideToggle(); //sample
					   }
					}

				if (typeof clear_bbu_notes_wptc !== 'undefined' && jQuery.isFunction(clear_bbu_notes_wptc)) {
					clear_bbu_notes_wptc();
					}
				}, 1000);
		} else if (data.bbu_note_view.type === 'warning') {
			jQuery(adminbar).after("<div style='display:none' class='warning-bar-wptc warning-image-wptc close-image-wptc'><span id='bar-note-wptc'>"+data.bbu_note_view.note+"</span></div>");
				setTimeout(function(){
					if(iframe){
					   if(!jQuery('.warning-bar-wptc', window.parent.document).is(':visible')){
							jQuery('.warning-bar-wptc', window.parent.document).slideToggle(); //sample
					   }
					} else {
					   if(!jQuery('.warning-bar-wptc').is(':visible')){
							jQuery('.warning-bar-wptc').slideToggle(); //sample
					   }
					}

				if (typeof clear_bbu_notes_wptc !== 'undefined' && jQuery.isFunction(clear_bbu_notes_wptc)) {
					clear_bbu_notes_wptc();
					}
				}, 1000);
		} else if (data.bbu_note_view.type === 'message') {
			jQuery(adminbar).after("<div style='display:none' class='message-bar-wptc message-image-wptc close-image-wptc'><span id='bar-note-wptc'>"+data.bbu_note_view.note+"</span></div>");
				setTimeout(function(){
					if(iframe){
					   if(!jQuery('.message-bar-wptc', window.parent.document).is(':visible')){
							jQuery('.message-bar-wptc', window.parent.document).slideToggle(); //sample
					   }
					} else {
					   if(!jQuery('.message-bar-wptc').is(':visible')){
							jQuery('.message-bar-wptc').slideToggle(); //sample
					   }
					}

				if (typeof clear_bbu_notes_wptc !== 'undefined' && jQuery.isFunction(clear_bbu_notes_wptc)) {
					clear_bbu_notes_wptc();
					}
				}, 1000);
		}
	}
}

function push_extra_button_wptc(data){
	if (data.hide_trigger_backup) {
		return ;
	}

	if (window.location.href.indexOf('update-core.php') === -1 && window.location.href.indexOf('plugins.php') === -1 && window.location.href.indexOf('themes.php') === -1 && window.location.href.indexOf('plugin-install.php') === -1){
		return false;
	}
	if (typeof push_staging_button_wptc !== 'undefined' && jQuery.isFunction(push_staging_button_wptc)) {
		push_staging_button_wptc(data);
	}
	if (typeof push_bbu_button_wptc !== 'undefined' && jQuery.isFunction(push_bbu_button_wptc)) {
		push_bbu_button_wptc(data);
	}
}

function disable_pro_button_wptc(){

}

function enable_pro_button_wptc(){
	 if (typeof enable_staging_button_wptc !== 'undefined' && jQuery.isFunction(enable_staging_button_wptc)) {
		enable_staging_button_wptc();
	}
}

function show_own_cron_status_wptc(data){

	if (show_alerts_wptc === false) {
		return false;
	}

	if (!data.wptc_own_cron_status || !data.user_logged_in) {
		return false;
	}
	if(typeof data.wptc_own_cron_status.status != 'undefined'){
		if (data.wptc_own_cron_status.status == 'success') {
			//leave it
		} else if (data.wptc_own_cron_status.status == 'error') {
			load_cron_status_failed_popup_wptc(data.wptc_own_cron_status.statusCode, data.wptc_own_cron_status.body, data.wptc_own_cron_status.cron_url, data.wptc_own_cron_status.ips, data.wptc_own_cron_status_notified);
			return false;
		}
	}
}

function disable_settings_wptc(){
	if (jQuery("#start_backup_from_settings").text() != 'Stopping backup...') {
		jQuery("#start_backup_from_settings").attr("action", "stop").text("Stop Backup");
		jQuery("#backup_button_status_wptc").text("Clicking on Stop Backup will erase all progress made in the current backup.");
		jQuery("#select_wptc_backup_slots, #select_wptc_default_schedule, #wptc_timezone, #wptc_auto_update_schedule_time").addClass('disabled').attr('disabled', 'disabled');
		jQuery('.change_dbox_user_tc').addClass('wptc-link-disabled');
		jQuery('.setting_backup_stop_note_wptc').show();
		jQuery('.setting_backup_start_note_wptc').hide();
		jQuery('.setting_backup_progress_note_wptc').show();
	}
}


function enable_settings_wptc(){
	if (jQuery("#start_backup_from_settings").text() != 'Starting backup...') {
		jQuery("#start_backup_from_settings").attr("action", "start").text("Backup now");
		jQuery("#backup_button_status_wptc").text("Click Backup Now to backup the latest changes.");
		jQuery("#select_wptc_backup_slots, #select_wptc_default_schedule, #wptc_timezone, #wptc_auto_update_schedule_time").removeClass('disabled').removeAttr('disabled');
		jQuery('.change_dbox_user_tc').removeClass('wptc-link-disabled');
		jQuery('.setting_backup_stop_note_wptc').hide();
		jQuery('.setting_backup_start_note_wptc').show();
		jQuery('.setting_backup_progress_note_wptc').hide();
	}
}

function show_backup_progress_dialog_wptc(obj, type) {
	ask_backup_name_wptc(type);
}

function ask_backup_name_wptc(type){
	var bbu_note = '';

	if (type === 'bbu') {
		var data = {
			bbu_note_view:{
				type: 'message',note:'We will notify you once the backup and updates are completed.',
			},
		};
		return show_notification_bar_wptc(data);
	}

	swal({
		title               : wptc_get_dialog_header('Give this backup a name'),
		input               : 'text',
		padding             : '0px 0px 10px 0',
		buttonsStyling      : false,
		showCancelButton    : false,
		confirmButtonColor  : '',
		confirmButtonClass  : 'button-primary wtpc-button-primary',
		confirmButtonText   : 'Submit',
		showLoaderOnConfirm : true,
		preConfirm: function (name) {
			return new Promise(function (resolve, reject) {
				if (name) {
					return save_manual_backup_name_wptc(name);
				} else {
					swal.hideLoading();
					swal.showValidationError('');
				}
			})
		},
		allowOutsideClick: true
	});
}

function start_backup_wptc(type, update_items, update_ptc_type, backup_before_update_always) {
	bp_in_progress = true;
	backup_started_time_wptc = jQuery.now();
	var is_staging_req = 0;

	get_current_backup_status_wptc();

	if (type == 'from_setting') {
		show_backup_progress_dialog_wptc('', 'incremental');
	} else if (type == 'from_staging'){
		if (typeof copy_staging_wptc != 'undefined' && copy_staging_wptc) {
			is_staging_req = 2;
		} else{
			is_staging_req = 1;
		}
	} else if(type == 'from_bbu'){
		show_backup_progress_dialog_wptc('', 'bbu');
	}

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'start_fresh_backup_tc_wptc',
		type: 'manual',
		backup_before_update : update_items,
		update_ptc_type: update_ptc_type,
		is_auto_update: 0,
		is_staging_req : is_staging_req,
		backup_before_update_setting: backup_before_update_always,
	}, function(data) {
		bp_in_progress = true;
		get_current_backup_status_wptc();
	});
}

function stop_backup_wptc() {
	var this_obj = jQuery(this);
	backup_type = '';
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'stop_fresh_backup_tc_wptc'
	}, function(data) {
		jQuery('#start_backup').text("Stop Backup");
		jQuery(this_obj).hide();
		bp_in_progress = false;
		get_current_backup_status_wptc();
	});
}

function stop_restore_wptc() {
	var this_obj = jQuery(this);
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'stop_restore_tc_wptc'
	}, function(data) {
		jQuery(this_obj).hide();
	});
}

function showLoadingDivInCalendarBoxWptc() {
	jQuery('.tc_backup_before_update').addClass('disabled backup_is_going');
	bp_in_progress = true;
}

function resetLoadingDivInCalendarBoxWptc() {
	bp_in_progress = false;
	jQuery('.tc_backup_before_update').removeClass('disabled backup_is_going');
}

function styling_thickbox_wptc(styleType) {
	jQuery("#TB_window").removeClass("thickbox-loading");
	jQuery("#TB_title").hide();
	if (styleType == 'progress') {
		jQuery("#TB_window").width("518px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("518px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
		//jQuery("#TB_ajaxContent").css("max-height", "322px");
		jQuery("#TB_ajaxContent").css("height", "auto");
	} else if (styleType == 'backup_yes') {
		jQuery("#TB_window").width("578px");
		jQuery("#TB_ajaxContent").width("578px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
		jQuery("#TB_window").css("height", "auto");
		jQuery("#TB_ajaxContent").css("height", "auto");
		jQuery("#TB_window").css("margin-top", "66px");
		jQuery("#TB_ajaxContent").css("max-height", "322px");
		jQuery("#TB_window").css("max-height", "322px");
	} else if (styleType == 'backup_yes_no') {
		jQuery("#TB_window").width("578px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("578px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
		jQuery("#TB_ajaxContent").css("height", "auto");
		jQuery("#TB_window").css("height", "auto");
		jQuery("#TB_window").css("margin-top", "66px");
		jQuery("#TB_window").css("max-height", "274px");
		jQuery("#TB_ajaxContent").css("max-height", "274px");
		jQuery("#TB_window").css("max-width", "578px");
	} else if (styleType == 'restore') {
		jQuery("#TB_window").width("518px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("518px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
		jQuery("#TB_ajaxContent").css("max-height", "322px");
		jQuery("#TB_ajaxContent").css("height", "auto");
	} else if (styleType == 'change_account') {
		jQuery("#TB_window").width("578px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("578px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
		jQuery("#TB_ajaxContent").css("max-height", "500px");
		jQuery("#TB_ajaxContent").css("height", "auto");
	} else if (styleType == 'report_issue') {
		jQuery("#TB_window").width("518px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("518px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
		jQuery("#TB_ajaxContent").css("max-height", "600px");
		jQuery("#TB_ajaxContent").css("height", "auto");
	} else if (styleType == 'initial_backup') {
		jQuery("#TB_window").width("630px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("630px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
		jQuery("#TB_ajaxContent").css("max-height", "500px");
		jQuery("#TB_ajaxContent").css("min-height", "225px");
		jQuery("#TB_ajaxContent").css("height", "auto");
		jQuery("#TB_overlay").attr("onclick", "tb_remove()");
	} else if(styleType == 'backup_before'){
		jQuery("#TB_window").width("518px");
		jQuery("#TB_ajaxContent").width("518px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_window").css("height", "220px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
	}else if(styleType == 'backup_before'){
		jQuery("#TB_window").width("518px");
		jQuery("#TB_ajaxContent").width("518px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_window").css("height", "220px");
		jQuery("#TB_ajaxContent").css("overflow", "hidden");
	} else if(styleType == 'staging_db'){
		 jQuery("#TB_window").width("612px");
		 jQuery("#TB_window").css("margin-top", "-245px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("627px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("height", "auto");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_window").css("top", "300px");
		//jQuery("#TB_window").css("overflow", "hidden");
		var this_height = (jQuery(window).height() * .9) + "px";
		jQuery("#TB_ajaxContent").css("max-height", this_height);
	} else {
		jQuery("#TB_window").width("891px");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_ajaxContent").width("891px");
		jQuery("#TB_ajaxContent").css("padding", "0px");
		jQuery("#TB_ajaxContent").css("left", "30%");
		jQuery("#TB_ajaxContent").css("height", "auto");
		jQuery("#TB_window").height("auto");
		jQuery("#TB_window").css("top", "300px");
		//jQuery("#TB_window").css("overflow", "hidden");
		var this_height = (jQuery(window).height() * .8) + "px";
		jQuery("#TB_ajaxContent").css("max-height", this_height);
		// var win_height = (jQuery("#TB_ajaxContent").height() / 4) + "px";
		// jQuery("#TB_window").css("margin-top", "-" + win_height);
	}
	jQuery("#TB_window").css('margin-bottom', '0px');

}

function send_report_issue_wptc(description, log_id) {

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'send_issue_report_wptc',
		data: {
			log_id : log_id,
			description : description,
		}
	}, function(data) {
		try{
			var data = jQuery.parseJSON(data);
		} catch(err){
			return ;
		}

		if (data.success) {
			swal({
				title              : wptc_get_dialog_header('Success'),
				html               : wptc_get_dialog_body('Issue sent successfully, We will get in touch with you shortly.', 'success'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});
		} else {
			swal({
				title              : wptc_get_dialog_header('Failed'),
				html               : wptc_get_dialog_body('Issue sending failed, try after sometime or email us at <a href="mailto:help@wptimecapsule.com?Subject=Contact" target="_top">help@wptimecapsule.com</a>.', 'error'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});
		}
	});
}

function yes_delete_logs_wptc() {
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'clear_wptc_logs'
	}, function(data) {
		try{
			var data = jQuery.parseJSON(data);
		} catch(err){
			return ;
		}
		if (data.success) {
			swal({
				title              : wptc_get_dialog_header('Success'),
				html               : wptc_get_dialog_body('Logs are deleted', 'success'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});

			setTimeout(function(){
				parent.location.assign(parent.location.href);
			}, 2000)

		} else {
			swal({
				title              : wptc_get_dialog_header('Failed'),
				html               : wptc_get_dialog_body('Failed to remove logs', 'error'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});
		}
	});
}

// function reload_monitor_page_wptc() {
// 	parent.location.assign(parent.location.href);
// }

function fresh_backup_popup_show_wptc() {
	var StartBackup = jQuery('#start_backup').html();
	var StopBackup = jQuery('#stop_backup').html();
	if (StartBackup != "Stop Backup" && StopBackup != "Stop Backup") {
		var dialog_content = '<div class="this_modal_div" style="background-color: #f1f1f1;font-family: \'open_sansregular\' !important;color: #444;padding: 0px 34px 26px 34px; left:20%; z-index:1000"><span class="dialog_close"></span><div class="pu_title">Your first backup</div><div class="wcard clearfix" style="width:480px"><div class="l1">Do you want to backup your site now?</div><a style="margin-left: 29px;" class="btn_pri" onclick="initial_setup_backup_wptc()">Yes. Backup now.</a><a class="btn_sec" id="no_change" onclick="tb_remove()">No. I will do it later.</a></div></div>';
		setTimeout(function() {
			remove_other_thickbox_wptc();
			jQuery("#wptc-content-id").html(dialog_content);
			jQuery(".wptc-thickbox").click();
			styling_thickbox_wptc('initial_backup');
		}, 3000);
	}
}

function initial_setup_backup_wptc() {
	jQuery('#start_backup').click()
	tb_remove();
}

// function show_get_name_dialog_wptc() {
// 	var this_content = '<div class="wcard clearfix backup_name_dialog" style="margin-top:30px;">  <div class="l1" style="padding-top: 0px;">Do you want to name this backup?</div>  <input type="text" placeholder="Backup Name" class="backup_name_tc"><a class="btn_pri backup_name_enter">SAVE</a>  <a class="skip">NO, SKIP THIS</a> </div>';

// 	jQuery(".backup_progress_tc").parent().append(this_content);
// 	jQuery(".skip").on("click", function() {
// 		jQuery(".backup_name_dialog").remove();
// 	});
// 	jQuery(".backup_name_enter").on("click", function() {
// 		store_this_name_tc();
// 	});
// }

// function store_this_name_tc() {
// 	var this_name = jQuery(".backup_name_tc").val();
// 	jQuery.post(ajaxurl, {
// 		security: wptc_ajax_object.ajax_nonce,
// 		action: 'store_name_for_this_backup_wptc',
// 		data: this_name
// 	}, function(data) {
// 		if (data) {
// 			jQuery(".backup_name_dialog").hide();
// 		}
// 	});
// }

function process_backup_status_wptc(backup_progress, prog_percent) {
	if (backup_progress == '') {
		return update_backup_status_wptc('backup_completed');
	}

	if (backup_progress.meta.running) {
		return update_backup_status_wptc('meta', backup_progress.meta.message);
	}

	if(backup_progress.db.running){
		return update_backup_status_wptc('db', backup_progress.db.progress);
	}

	if(backup_progress.files.processing.running){
		return update_backup_status_wptc('analyze', backup_progress.files.processing.progress);
	}

	if(backup_progress.files.processed.running) {
		return update_backup_status_wptc('files', prog_percent);
	}

}

function update_backup_status_wptc(type, value) {

	switch(type){
		case 'analyze':
			if (!value) value = '';
			jQuery(status_area_wptc).html(' Processing files ' + value);
			break;
		case 'db':
			jQuery(status_area_wptc).html(' Syncing database (' + value + ' tables)');
			break;
		case 'files':
			jQuery(status_area_wptc).html(' Syncing changed files ' + value + '%');
			jQuery('.staging_progress_bar').css('width', value + '%');
			break;
		case 'meta':
			jQuery(status_area_wptc).html(value);
			jQuery('.bp_progress_bar').css('width', '100%');
			break;
		default :
			jQuery(status_area_wptc).html(value);
	}
}

function get_this_day_backups_wptc(backupIds){
	remove_other_thickbox_wptc();
	 jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'get_this_day_backups_wptc',
			data: backupIds
		}, function(data) {
			jQuery(".dialog_cont").remove();
			jQuery("#wptc-content-id").html(data);
			jQuery(jQuery("#wptc-content-id").find('.bu_name')).each(function( index ) {
				if(jQuery(this).text().indexOf('Updated on') == 0)
				{
					jQuery(this).hide();
				}
			});
			jQuery(".wptc-thickbox").click();

			styling_thickbox_wptc();
			register_dialog_box_events_wptc();
			//do the UI action to hide the folders, display the folders based on tree
			jQuery(".this_parent_node .sub_tree_class").hide();
			jQuery(".this_parent_node .this_leaf_node").hide();
			jQuery(".this_leaf_node").show();
			jQuery(".sub_tree_class.sl0").show();

			//for hiding the backups folder and its sql-file
			var sqlFileParent = jQuery(".sql_file").parent(".this_parent_node");
			jQuery(sqlFileParent).hide();
			//jQuery(sqlFileParent).parent(".this_parent_node").hide();
			//jQuery(sqlFileParent).parent(".this_parent_node").prev(".sub_tree_class").hide();
			jQuery(sqlFileParent).prev(".sub_tree_class").hide();
		});
}

// function load_pop_up(){
// 	remove_other_thickbox_wptc();
// 	if(jQuery('#cancel_issue_notice').css('display') != 'none' && jQuery('#cancel_issue_notice').css('display') != undefined || location.href.indexOf("page=wp-time-capsule") == -1){
// 		return false;
// 	}
// 	if(location.href.toLowerCase().indexOf('wp-time-capsule') === -1){
// 		return false;
// 	}
// 	jQuery('.notice, #update-nag').remove();
// 	var form_content = '<div class=row-wptc style="padding: 0 0 49px 0;"><ul><li style="text-align: justify;">Starting v1.3.0 we will backup only the WordPress core files, folders & tables. If you want to include or exclude more files, go to Settings -> Exclude / Include from Backup.</li></ul><br><input id="cancel_issue_notice" style="margin-left: 40%;" class="button button-primary" type="button" value="Okay, got it"></div>';
// 	var dialog_content = '<div class="this_modal_div" style="background-color: #f1f1f1;font-family: \'open_sansregular\' !important;color: #444;padding: 0px 35px 0px 35px; width: 450px;left:20%; z-index:1000"><div class="pu_title">Updates to existing backup mechanism</div><form name="issue_form" id="issue_form">' + form_content + '</form></div>';
// 	jQuery("#wptc-content-id").html(dialog_content);
// 	jQuery(".wptc-thickbox").click();
// 	styling_thickbox_wptc('progress');
// 	mark_update_pop_up_shown_wptc();
// }

function mark_update_pop_up_shown_wptc(){
	jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'plugin_update_notice_wptc',
		}, function(data) {
			//tb_remove();
		});
}

function load_cron_status_failed_popup_wptc(status_code, err_msg, cron_url, ips){

	if (ips == undefined || !ips) {
		ips = '52.33.122.174';
	}

	if (err_msg.length > 1000) {
		err_msg = err_msg.substring(0, 1000);
	}

	swal({
		title              : wptc_get_dialog_header('Connection failed!'),
		html               : wptc_get_dialog_body('<div style ="max-width: 500px; max-height: 400px; overflow: auto;"> ' + err_msg + '  </div> <br><br><br> <hr><div style="font-size: 13px;text-align: justify;"> Note: Please whitelist the IP Address ('+ips+') to access the URL : <a style="text-decoration: none;" href="'+cron_url+'">'+cron_url+'</a> or get in touch with your hosting provider to have it whitelisted. You can check the Cron Status anytime under WPTC -> Settings or email us at <a href="mailto:help@wptimecapsule.com?Subject=Contact" target="_top">help@wptimecapsule.com</a></div></div>', ''),
		padding            : '0px 0px 10px 0',
		buttonsStyling     : false,
		confirmButtonColor : '',
		confirmButtonClass : 'button-primary wtpc-button-primary',
		confirmButtonText  : 'Ok',
	});

	update_test_connection_err_shown_wptc();

	delete reloadFuncTimeout;
}

function add_notice_wptc(note, all_page, status){

	if (show_alerts_wptc === false) {
		return false;
	}

	var status_class = '';
	if (!status) {
		status_class = 'notice-warning';
	} else {
		switch(status){
			case 'success':
			status_class = 'notice-success';
			break;
			case 'error':
			status_class = 'notice-error';
			break;
			case 'warning':
			status_class = 'notice-warning';
			break;
		}
	}

	var notice = '<div id="wptc-notice" class=" notice '+status_class+'  is-dismissible" > <p>'+note+'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>'

	jQuery('#wptc-notice').remove();

	if (all_page) {
		jQuery('.wrap').before(notice);
	} else {
		jQuery('#wptc').before(notice);
	}
}


function load_custom_popup_wptc(show, type, show_all_page, title, msg, footer){

	if (show_alerts_wptc === false) {
		return false;
	}

	if (!show) {
		return false;
	}

	if(location.href.toLowerCase().indexOf('wp-time-capsule') === -1 && !show_all_page){
		return false;
	}

	if (type == 'new_updates') {
		title = '';
		msg = '';
		footer = '';
	}


	swal({
		title              : wptc_get_dialog_header(title),
		html               : wptc_get_dialog_body(msg),
		padding            : '0px 0px 10px 0',
		buttonsStyling     : false,
		showCancelButton   : false,
		confirmButtonColor : '',
		confirmButtonClass : 'button-primary wtpc-button-primary',
		confirmButtonText  : 'Ok',
	});

	setTimeout(function() {
		mark_update_pop_up_shown_wptc();
	}, 2000);
}

function start_manual_backup_wptc(obj, type, update_items, update_ptc_type, backup_before_update_always){
	if(type == 'from_bbu'){
		backup_started_wptc = 'from_bbu';
		start_backup_wptc('from_bbu', update_items, update_ptc_type, backup_before_update_always);
		return false;
	}
	if(jQuery(obj).attr("action") == 'start'){
		jQuery(obj).text('Starting backup...');
		backup_started_wptc = 'from_setting';
		start_backup_wptc('from_setting');
	} else if(jQuery(obj).attr("action") == 'stop'){
		jQuery(obj).text('Stopping backup...');
		stop_backup_wptc();
	}
}

function update_sycn_db_view_wptc(){
	 jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'update_sycn_db_view_wptc',
		}, function(data) {});
}

function update_test_connection_err_shown_wptc(){
	 jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'update_test_connection_err_shown_wptc',
		}, function(data) {});
}

function showed_processing_files_view_wptc(){
	 jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'show_processing_files_view_wptc',
		}, function(data) {});
}

function test_connection_wptc_cron(){
	if (jQuery('.test_cron_wptc').hasClass('disabled')) {
		return false;
	}
	jQuery('.cron_current_status').html('Waiting for response').css('color', '#444');
	jQuery('.test_cron_wptc').addClass('disabled').html('Connecting...');
	jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'test_connection_wptc_cron',
		}, function(data) {
			 try{
					var obj = jQuery.parseJSON(data);
					if (typeof obj.status != 'undefined' && obj.status == "success") {
						jQuery('#wptc_cron_status_failed').hide();
						// jQuery('.test_cron_wptc').hide();
						jQuery('.test_cron_wptc').removeClass('disabled').html('Test Again');
						jQuery('#wptc_cron_status_passed').show();
						jQuery('.cron_current_status').html('Success').css('color', '');
					} else {
						load_cron_status_failed_popup_wptc(obj.status, obj.err_msg, obj.cron_url, obj.ips, '');
						jQuery('#wptc_cron_status_failed').show();
						jQuery('.test_cron_wptc').show();
						jQuery('.test_cron_wptc').removeClass('disabled').html('Test Again');
						jQuery('#wptc_cron_failed_note').html('Failed').css('color', '');
						jQuery('#wptc_cron_status_passed').hide();
					}
				} catch (e){
					jQuery('.test_cron_wptc').removeClass('disabled').html('Test Again');
					jQuery('#wptc_cron_failed_note').html('Failed');
					return false;
				}
	});
}

// function wptc_backup_start_failed_note(failed_backups){
// 	jQuery("#start_backup_from_settings").attr('action', 'start').html('Backup now');
// 	jQuery("#backup_button_status_wptc").text("Click Backup Now to backup the latest changes.");
// 	tb_remove();
// 	var total_failed_count = jQuery(failed_backups).length;
// 	var backup_text = (total_failed_count > 1) ? 'backups have ' : 'backup has ';
// 	var note = 'The plugin is not able to communicate with the server hence backups have been stopped. This is applicable to manual , scheduled backups and Staging. The following '+backup_text+' been stopped due to lack of communication between the plugin and server.<br>';
// 	var backup_list = '';
// 	jQuery(failed_backups).each(function( index ) {
// 		backup_list = backup_list + failed_backups[index] +"<br>";
// 	});
// 	note = note + backup_list;
// 	jQuery('.notice, #update-nag').remove();
// 	var notice = '<div class="update-nag  notice is-dismissible" id="setting-error-tgmpa"> <h4>WP Time Capsule</h4> <p>'+note+'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>'
// 	jQuery('.wrap').before(notice);
// }

function resume_backup_wptc(){
	jQuery(".resume_backup_wptc").addClass("disabled").attr('style', 'cursor:auto; color:gray').text('Reconnecting...');
	jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'resume_backup_wptc',
	}, function(data) {

		if (data != undefined) {
			try{
				var obj = jQuery.parseJSON(data);
			if (typeof obj.status != 'undefined' && obj.status == "success") {
					jQuery(".resume_backup_wptc").text('Backup Resumed').removeAttr('style').attr('style', 'color:green;');
					setTimeout(function(){
						jQuery("#wptc_cron_status_div").show();
						jQuery("#wptc_cron_status_paused").hide();
					}, 1000);
				} else {
					load_cron_status_failed_popup_wptc(obj.status, obj.err_msg, obj.cron_url, obj.ips, '');
					jQuery(".resume_backup_wptc").text('Failed to resume backup');
					setTimeout(function(){
						jQuery(".resume_backup_wptc").removeClass("disabled").removeAttr('style').attr('style', 'cursor:pointer;').text('Resume backup');
					}, 1000);
				}
			} catch (e){
				jQuery(".resume_backup_wptc").text('Failed to resume backup');
				setTimeout(function(){
						jQuery(".resume_backup_wptc").removeClass("disabled").removeAttr('style').attr('style', 'cursor:pointer;').text('Resume backup');
					}, 1000);
				return false;
			}
		}
	});
}

function basename_wptc(path) {
   return path.split('/').reverse()[0];
}

function change_init_setup_button_state_wptc(){
	jQuery("#file_db_exp_for_exc_view").toggle();
	jQuery(".view-user-exc-extensions").toggle();
	jQuery("#wptc_init_toggle_tables").click();
	jQuery("#wptc_init_toggle_files").click();
}

function convert_bytes_to_hr_format_wptc(size){
	if (1024 > size) {
		return size + ' B';
	} else if (1048576 > size) {
		return ( (size / 1024)).toFixed(2) + ' KB';
	} else if (1073741824 > size) {
		return ((size / 1024) / 1024).toFixed(2) + ' MB';
	} else if (1099511627776 > size) {
		return (((size / 1024) / 1024) / 1024).toFixed(2) + ' GB';
	}
}

function save_manual_backup_name_wptc(name){
	jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'save_manual_backup_name_wptc',
			name: name,
	}, function(data) {

		try{
			var obj = jQuery.parseJSON(data);
		} catch(err){
			return ;
		}

		if(obj.status && obj.status == 'success'){
			swal({
				title              : wptc_get_dialog_header('Success'),
				html               : wptc_get_dialog_body('Backup name saved :)' , 'success'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});


			setTimeout(function(){
				if(typeof backup_started_wptc != 'undefined' && backup_started_wptc == 'from_setting'){
					location.assign(adminUrlWptc+'admin.php?page=wp-time-capsule-monitor');
				}
			},3000);
		}
	});
}

function remove_other_thickbox_wptc(){
	jQuery('.thickbox').each(function(index){
		if(!jQuery(this).hasClass("wptc-thickbox") && !jQuery(this).hasClass("open-plugin-details-modal")){
			jQuery(this).remove();
		}
	});
}

function gettime_wptc() {
	var d = new Date();
	var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	var date = d.getDate() + " " + month[d.getMonth()];
	var nowHour = d.getHours();
	var nowMinutes = d.getMinutes();
	var nowSeconds = d.getSeconds();
	var suffix = nowHour >= 12 ? "PM" : "AM";
	nowHour = (suffix == "PM" & (nowHour > 12 & nowHour < 24)) ? (nowHour - 12) : nowHour;
	nowHour = nowHour == 0 ? 12 : nowHour;
	nowMinutes = nowMinutes < 10 ? "0" + nowMinutes : nowMinutes;
	nowSeconds = nowSeconds < 10 ? "0" + nowSeconds : nowSeconds;
	var currentTime = nowHour + ":" + nowMinutes + ":" + nowSeconds + ' ' + suffix;
	return date + ' '+currentTime;
}

function show_users_backend_errors_wptc(error){

	if (show_alerts_wptc === false) {
		return false;
	}

	if (error) {
		var note = 'WP Time Capsule Error : '+ error + '   <br>If you are not sure what went wrong, please email us at <a href="mailto:help@wptimecapsule.com?Subject=Contact" target="_top">help@wptimecapsule.com</a>';
		add_notice_wptc(note, true);
		clear_show_users_backend_errors_wptc();
	}
}

function clear_show_users_backend_errors_wptc(){
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'clear_show_users_backend_errors_wptc',
	}, function(data) {
		//data
	});
}

function make_this_fresh_site_wptc(){
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'make_this_fresh_site_wptc',
	}, function(data) {
		parent.location.assign(adminUrlWptc+'admin.php?page=wp-time-capsule-monitor');
	});
}

function make_this_original_site_wptc(){
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'make_this_original_site_wptc',
	}, function(data) {
		//data
	});
}

function get_cloud_label_from_val_wptc(val){
	if(typeof val == 'undefined' || val == ''){
		return 'Cloud';
	}
	var cloudLabels = {};
	cloudLabels['g_drive'] = 'Google Drive';
	cloudLabels['s3'] = 'Amazon S3';
	cloudLabels['dropbox'] = 'Dropbox';
	cloudLabels['wptc_repo'] = 'WP Time Capsule Cloud';
	cloudLabels['wasabi'] = 'Wasabi';

	return cloudLabels[val];
}

function wptc_is_backup_running(){
	if(typeof wptc_backup_running == 'undefined' || !wptc_backup_running){
		return false;
	}

	return true;
}

function wptc_request_failed(){

	swal({
		title: wptc_get_dialog_header('Request failed!'),
		html: wptc_get_dialog_body('Please try again !', 'error'),
		padding: '0px 0px 10px 0',
		buttonsStyling: false,
		showCancelButton: false,
		confirmButtonColor: '',
		confirmButtonClass: 'button-primary wtpc-button-primary',
		confirmButtonText:'Ok',
	});
}

function wptc_is_allowed_to_show_extra_buttons(){
	if (!wptc_is_multisite) {
		return true;
	}

	return window.location.href.toLowerCase().indexOf('/network') !== -1 ? true : false;
}

function wptc_get_dialog_header(title){
	return '<div class="ui-dialog-titlebar ui-widget-header  ui-helper-clearfix" style="text-align: left;padding-left: 10px;"><span class="ui-dialog-title" style="font-size: 15px;line-height: 29px;text-align: left !important;">' + title + '</span></div>';
}

function wptc_get_dialog_body(content, status){

	var status_icon = '';

	if (status != undefined) {
		switch(status){
			case 'success':
				status_icon = '<span class=" wptc-model-icon dashicons dashicons-yes" style="color: #79ba49;"></span>';
				break;
			case 'warning':
				status_icon = '<span class="wptc-model-alert-icon dashicons dashicons-warning" style="color: #ffb900;"></span>';
				break;
			case 'error':
				status_icon = '<span class=" wptc-model-icon dashicons dashicons-no-alt" style="color: #dc3232;"></span>';
				break;
		}
	}

	return '<div class="ui-dialog-content ui-widget-content" style="font-size: 14px;text-align: left;padding: 20px 30px 20px 30px;"> ' + status_icon + content + '</div><hr>';
}
