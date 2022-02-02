jQuery(document).ready(function($) {

	show_schedule_time_wptc();

	jQuery("#select_wptc_backup_slots").on('change', function(){
		check_and_prevent_realtime_selection_wptc();
		show_schedule_time_wptc();
	});

	if(jQuery("#select_wptc_backup_slots").val() === 'daily'){
		jQuery('#select_wptc_default_schedule').show();
	}

	jQuery("#wptc_save_changes").on("click", function() {
		if (jQuery(this).hasClass('disabled')) {
			return false;
		}
		jQuery('#calculating_file_db_size_temp, #show_final_size').toggle();
		jQuery(this).addClass('disabled').attr('disabled', 'disabled').val('Saving new changes...').html('Saving...');
		save_settings_wptc();
		return false;
	});

	jQuery('body').on('click', '.change_dbox_user_tc', function(e) {

		if (jQuery(this).hasClass('wptc-link-disabled')) {
			e.stopImmediatePropagation();
			e.preventDefault();
			return false;
		}

		var href =  jQuery(this).attr('href');

		if (href.indexOf('show_connect_pane=set') !== -1) {
			e.stopImmediatePropagation();
			e.preventDefault();

			swal({
				title              : wptc_get_dialog_header('Warning'),
				html               : wptc_get_dialog_body('Please choose the same Cloud Account that you had connected earlier. By choosing a different integration or different account, you will end up losing all of your WPTC backups. <br><br> <strong>Are you sure about connecting it to a different account?</strong>', ''),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'I understand',
			}).then(function () {
				swal({
					title              : wptc_get_dialog_header('testing'),
					html               : wptc_get_dialog_body('Redirecting...'),
					padding            : '0px 0px 10px 0',
					buttonsStyling     : false,
					showCancelButton   : false,
					confirmButtonColor : '',
					confirmButtonClass : 'button-primary wtpc-button-primary',
					confirmButtonText  : 'Ok',
				});
				location.assign(href);
			});
			return false;
		}

	});

	jQuery('body').on('click', '#connect_to_cloud, #save_g_drive_refresh_token', function(e) {

		if (jQuery(this).hasClass('disabled')) {
			return ;
		}

		jQuery(this).addClass('disabled');

		jQuery('.cloud_error_mesg, .cloud_error_mesg_g_drive_token').html('');
		var cloud_type_wptc = $(this).attr("cloud_type");
		var auth_url_func = '';
		var wptc_gdrive_token_btn = false;
		var data = {};

		if (cloud_type_wptc == 'dropbox') {
			auth_url_func 	= 'get_dropbox_authorize_url_wptc';
			cloud_type 		= 'Dropbox';
		} else if (cloud_type_wptc == 'g_drive') {
			if(jQuery('#gdrive_refresh_token_input_wptc').is(':visible') && this.id === 'save_g_drive_refresh_token' ){
				if(jQuery('#gdrive_refresh_token_input_wptc').val().length < 1){
					jQuery('.cloud_error_mesg_g_drive_token').html('Please enter the token !').show();
					jQuery(this).removeClass('disabled');
					return false;
				}
				wptc_gdrive_token_btn = true;
				data['g_drive_refresh_token'] = jQuery('#gdrive_refresh_token_input_wptc').val();
			}
			auth_url_func = 'get_g_drive_authorize_url_wptc';
			cloud_type = 'Google Drive';
		} else if (cloud_type_wptc == 's3') {
			data['as3_access_key']       = jQuery('#as3_access_key').val();
			data['as3_secure_key']       = jQuery('#as3_secure_key').val();
			data['as3_bucket_region']    = jQuery('#as3_bucket_region').val();
			data['as3_bucket_name']      = jQuery('#as3_bucket_name').val();
			data['as3_iam_user_status'] = jQuery('input[name=wptc_iam_user_status]:checked').val();
			auth_url_func                = 'get_s3_authorize_url_wptc';
			cloud_type                   = 'Amazon S3';
		} else if (cloud_type_wptc == 'wasabi') {
			data['wasabi_access_key']       = jQuery('#wasabi_access_key').val();
			data['wasabi_secure_key']       = jQuery('#wasabi_secure_key').val();
			data['wasabi_bucket_region']    = jQuery('#wasabi_bucket_region').val();
			data['wasabi_bucket_name']      = jQuery('#wasabi_bucket_name').val();
			auth_url_func                = 'get_wasabi_authorize_url_wptc';
			cloud_type                   = 'Wasabi';
		}

		jQuery('.cloud_error_mesg').removeClass('cloud_acc_connection_error').html('').hide();

		if (data['wasabi_bucket_region'] == '') {
			jQuery('.cloud_error_mesg').addClass('cloud_acc_connection_error').html('Please select the bucket region.').show();
			jQuery(this).removeClass('disabled');
			return false;
		}

		if (auth_url_func == '') {
			jQuery('.cloud_error_mesg').addClass('cloud_acc_connection_error').html('Auth function is empty').show();
			jQuery(this).removeClass('disabled');
			return false;
		}

		if (cloud_type_wptc === 'g_drive' && !wptc_gdrive_token_btn) {
			wptc_tmp_auth_url_func = auth_url_func;
			wptc_tmp_data = data;
			wptc_tmp_gdrive_token_btn = wptc_gdrive_token_btn;
			wptc_tmp_cloud_type = cloud_type;
			if (jQuery('#gdrive_refresh_token_input_wptc').is(':visible') === false) {
				return wptc_make_cloud_auth_req(wptc_tmp_auth_url_func, wptc_tmp_data, wptc_tmp_gdrive_token_btn, wptc_tmp_cloud_type);
			}

			swal({
				title              : wptc_get_dialog_header('Are you sure?'),
				html               : wptc_get_dialog_body('Google has a limit on the number of sites you can authenticate per account, once that is crossed then all your other sites will be revoked! <br><br> <strong>Are you sure about connecting it to a different account?</strong> ', ''),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : true,
				confirmButtonColor : '',
				cancelButtonColor  : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				cancelButtonClass  : 'button-secondary wtpc-button-secondary',
				confirmButtonText  : 'Yes, I am sure',
				cancelButtonText   : 'No, its the same',
			}).then(function () {
					swal({
						title              : wptc_get_dialog_header('Redirecting...'),
						html               : wptc_get_dialog_body('You will be redirected to the authorization page once the process is compeleted.' , 'success'),
						padding            : '0px 0px 10px 0',
						buttonsStyling     : false,
						showCancelButton   : false,
						confirmButtonColor : '',
						confirmButtonClass : 'button-primary wtpc-button-primary',
						confirmButtonText  : 'Ok',
					});
					wptc_make_cloud_auth_req(wptc_tmp_auth_url_func, wptc_tmp_data, wptc_tmp_gdrive_token_btn, wptc_tmp_cloud_type);
				}, function (dismiss) {
				if (dismiss === 'cancel') {
					swal({
						title              : wptc_get_dialog_header('Need more information?'),
						html               : wptc_get_dialog_body('Please read <a target="_blank" href="http://docs.wptimecapsule.com/article/23-add-new-site-using-existing-google-drive-token">this</a> on how to use the existing authorization token.' , ''),
						padding            : '0px 0px 10px 0',
						buttonsStyling     : false,
						showCancelButton   : false,
						confirmButtonColor : '',
						confirmButtonClass : 'button-primary wtpc-button-primary',
						confirmButtonText  : 'Ok',
					});
				}
			})

			return false;
		}

		wptc_make_cloud_auth_req(auth_url_func, data, wptc_gdrive_token_btn, cloud_type);
	});

	if (typeof Clipboard != 'undefined') {
		var clipboard = new Clipboard("#copy_gdrive_token_wptc");
		if (clipboard != undefined) {
			clipboard.on("success", function(e) {
				jQuery("#gdrive_token_copy_message_wptc").show();
				setTimeout( function (){
					jQuery("#gdrive_token_copy_message_wptc").hide();
				},1000);
				e.clearSelection();
			});
			clipboard.on("error", function(e) {
				jQuery("#copy_gdrive_token_wptc").remove();
				jQuery("#gdrive_refresh_token_wptc").click(function(){jQuery(this).select();});
			});
		}else{
			jQuery("#gdrive_refresh_token_wptc").click(function(){jQuery(this).select();});
		}
	}

	jQuery('#start_backup_from_settings').click(function(e){
		e.stopImmediatePropagation();
		e.preventDefault();
		if (jQuery("#start_backup_from_settings").hasClass('disabled')) {
			return false;
		}
		start_manual_backup_wptc(this);
	});

	jQuery("#select_wptc_cloud_storage").on('change', function(){
		jQuery(".creds_box_inputs", this_par).hide();
		jQuery('#connect_to_cloud, #wptc-connect-cloud-note').show();
		jQuery('#wasabi_seperate_bucket_note, #s3_seperate_bucket_note, #see_how_to_add_refresh_token_wptc, #gdrive_refresh_token_wptc, #google_token_add_btn, #google_limit_reached_text_wptc, #wptc-connect-cloud-note-s3').hide();
		jQuery('.dummy_select, .wptc_error_div').remove();

		jQuery(".cloud_error_mesg, .cloud_error_mesg_g_drive_token").hide();
		var cur_cloud = jQuery(this).val();
		if(cur_cloud == ""){
			return false;
		}
		var cur_cloud_label = get_cloud_label_from_val_wptc(cur_cloud);
		var this_par = jQuery(this).closest(".wcard");
		jQuery("#connect_to_cloud, #save_g_drive_refresh_token").attr("cloud_type", cur_cloud);
		jQuery("#connect_to_cloud").val("Connect to " + cur_cloud_label).show();
		jQuery("#mess").show();
		jQuery("#donot_touch_note").show();
		jQuery("#donot_touch_note_cloud").html(cur_cloud_label);

		jQuery(".wptc_cloud_not_recommended").hide();

		if(cur_cloud == 'wasabi'){
			jQuery("#mess, #wasabi_seperate_bucket_note, #wptc-connect-cloud-note").toggle();
			// wptc_toggle_amazon_s3_connect_note();
			if (check_cloud_min_php_min_req.indexOf('s3') == -1) {
				jQuery(".cloud_error_mesg").show();
				jQuery(".cloud_error_mesg").html('Wasabi requires PHP v5.3.3+. Please upgrade your PHP to use Wasabi.');
				jQuery('#connect_to_cloud').hide();
				return false;
			}
			jQuery(".wasabi_inputs", this_par).show();
		}
		else if(cur_cloud == 's3'){
			jQuery("#mess, #s3_seperate_bucket_note, #wptc-connect-cloud-note").toggle();
			wptc_toggle_amazon_s3_connect_note();
			if (check_cloud_min_php_min_req.indexOf('s3') == -1) {
				jQuery(".cloud_error_mesg").show();
				jQuery(".cloud_error_mesg").html('Amazon S3 requires PHP v5.3.3+. Please upgrade your PHP to use Amazon S3.');
				jQuery('#connect_to_cloud').hide();
				return false;
			}
			jQuery(".s3_inputs", this_par).show();
		}
		else if(cur_cloud == 'g_drive'){
			jQuery(".wptc_cloud_not_recommended").show();
			if (check_cloud_min_php_min_req.indexOf('gdrive') == -1) {
				jQuery(".cloud_error_mesg").show();
				jQuery(".cloud_error_mesg").html('Google Drive requires PHP v5.4.0+. Please upgrade your PHP to use Google Drive.');
				jQuery('#connect_to_cloud').hide();
				return false;
			}
			jQuery('#see_how_to_add_refresh_token_wptc, #gdrive_refresh_token_wptc, #google_token_add_btn, #google_limit_reached_text_wptc').show();
			if (jQuery('#google_token_add_btn').length) {
				jQuery("#connect_to_cloud, #save_g_drive_refresh_token").attr("cloud_type", cur_cloud);
				jQuery("#connect_to_cloud").val("Connect to " + cur_cloud_label).show();
			}
			jQuery(".g_drive_inputs", this_par).show();
		} else if(cur_cloud == 'dropbox'){
			jQuery(".wptc_cloud_not_recommended").show();
		}
	});

	jQuery(".wcard").on('keypress', '#wptc_main_acc_email', function(e){
		wptc_trigger_login(e);
	});

	jQuery(".wcard").on('keypress', '#wptc_main_acc_pwd', function(e){
		wptc_trigger_login(e);
	});

	jQuery("#wptc_analyze_inc_exc_lists").click(function(e){
		e.stopImmediatePropagation();
		e.preventDefault();

		wptc_analyze_inc_exc_lists();

	});

	jQuery("#wptc_show_all_exc_files").click(function(e){
		e.stopImmediatePropagation();
		e.preventDefault();

		wptc_show_all_excluded_files();

	});

	jQuery(".wptc_toggle_cloud").click(function(e){
		e.stopImmediatePropagation();
		e.preventDefault();

		jQuery('#wptc_repo_html_block').toggle();
		jQuery('#wptc_users_own_repo_html_block').toggle();

	});


	jQuery('#wptc_auto_update_schedule_enabled').change(function() {
		if(jQuery(this).is(":checked")) {
			jQuery('#wptc_auto_update_schedule_time').show();
		} else {
			jQuery('#wptc_auto_update_schedule_time').hide();
		}
	});

	jQuery("#wptc_sync_purchase").click(function(e){
		e.stopImmediatePropagation();
		e.preventDefault();

		if (jQuery(this).hasClass('disabled')) {
			return ;
		}

		jQuery('#wptc_sync_purchase').val('Syncing Purchase').addClass('disabled');

		jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'wptc_sync_purchase',
		}, function(response) {

			jQuery('#wptc_sync_purchase').val('Sync Purchase').removeClass('disabled');

			swal({
				title              : wptc_get_dialog_header('Success'),
				html               : wptc_get_dialog_body('Purchase data synced successfully!', 'success'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Reload',
			}).then(function () {
				location.reload();
			});

			add_suggested_files_lists_wptc(response.files);

		});
	});


	jQuery('body').on('click', '#wptc_login', function(e) {

		e.stopImmediatePropagation();
		e.preventDefault();

		if (jQuery(this).hasClass('disabled')) {
			return false;
		}

		jQuery(this).addClass('disabled');


		var email = jQuery("#wptc_main_acc_email").val();
		if (email === '') {
			jQuery('.wptc_error_div').html('Email cannot be empty.');
			jQuery(this).removeClass('disabled');
			return ;
		}
		var password = jQuery("#wptc_main_acc_pwd").val();
		if (password === '') {
			jQuery('.wptc_error_div').html('Password cannot be empty.');
			jQuery(this).removeClass('disabled');
			return;
		}

		wptc_login_request({
			'email' 	: email,
			'password' 	: password,
		})

		return ;
	});

	jQuery('body').on('click', '#wptc-clear-all-decrypt-files', function(e) {
		jQuery.post(ajaxurl, {
			security : wptc_ajax_object.ajax_nonce,
			action   : 'clear_all_decrypt_files_wptc',
		}, function(data) {
			jQuery('#wptc-files-progress').remove();
			try{
				data = jQuery.parseJSON(data);
			} catch(err){
				return jQuery('#files').html('WPTC Cannot delete files, Manually delete the files here wp-content/wp-time-capsule/wp-tcapsule-bridge/upload/php/files').css('color', '#ca0606');
			}

			if (data.status === 'success') {
				jQuery('#wptc-file-selected-span').html('');
				jQuery('#wptc-fileupload-decrpty-key').val('');
				jQuery('#wptc-start-manual-decryption').removeAttr('disabled');

				return jQuery('#files').html('Files Deleted');
			}
		});
	});

	jQuery('body').on('click', '#wptc-fileupload-show', function(e) {
		jQuery.post(ajaxurl, {
			security : wptc_ajax_object.ajax_nonce,
			action   : 'prepare_file_upload_index_file_wptc',
		}, function(data) {
			try{
				data = jQuery.parseJSON(data);
				if (data.status === 'success') {
					jQuery('#wptc-fileupload-show').hide();
					jQuery('#wptc-fileupload').show();
					jQuery('#index-file-create-error-wptc').hide();
				} else if(typeof data.error != 'undefined'){
					jQuery('#index-file-create-error-wptc').show();
					jQuery('#index-file-create-error-wptc').text(data.error);
				}
			} catch(err){
			}
		});
	});

	if (typeof upload_decrypt_url_wptc != 'undefined') {
		jQuery('#wptc-fileupload').fileupload({
			url: upload_decrypt_url_wptc,
			dataType: 'json',
			maxChunkSize: 1000 * 1000,//1MB
			add: function(e, data) {
				$("#wptc-start-manual-decryption").off('click').on('click', function () {
					jQuery(this).attr("disabled", "disabled");

					var uploadErrors = [];

					var key = jQuery('#wptc-fileupload-decrpty-key').val();

					if(!key) {
						uploadErrors.push('First enter the decryption key.');
					}

					if(data.originalFiles[0]['name'].endsWith(".crypt") === false) {
						uploadErrors.push('File type should be .crypt');
					}

					if(uploadErrors.length > 0) {
						alert(uploadErrors.join("\n"));
						jQuery(this).removeAttr("disabled", "disabled");
					} else {
						jQuery('#files').html('File Uploading : ' + data.files[0].name);
						data.submit();
					}
				});

				jQuery('#wptc-file-selected-span').html('File selected : ' + data.files[0].name);
				jQuery('#wptc-fileupload').css('color', 'transparent');
			},
			done: function (e, data) {
				if (data.result.files[0].error) {
					alert("Server Error :" + data.result.files[0].error);
					jQuery(this).removeAttr("disabled", "disabled");
					return;
				}

				jQuery('#files').html('Uploading finished : ' + data.result.files[0].name);
				delete_file_upload_index_file_wptc();
				jQuery('#wptc-fileupload-show').show();
				jQuery('#wptc-fileupload').hide();
				start_decrypt_file_wptc(data.result.files[0].name);
			},
			progressall: function (e, data) {
				var progress = parseInt(data.loaded / data.total * 100, 10);
				jQuery('#wptc-files-progress').html(progress + '%');
			}
		});
	}

	jQuery("#enable_database_encryption").on("click", function() {
		jQuery("#database_encryption_key_div").show();
	});

	jQuery("#disable_database_encryption").on("click", function() {
		jQuery("#database_encryption_key_div").hide();
	});

	jQuery("input[name=user_excluded_files_more_than_size_status]").on("change", function() {
		jQuery("#user_excluded_files_more_than_size_div").toggle()
	});

	jQuery("input[name=wptc_iam_user_status]").on("change", function() {
		wptc_toggle_amazon_s3_connect_note();
	});
});


function start_decrypt_file_wptc(file, offset, position, replace_collation){
	jQuery.post(ajaxurl, {
		security : wptc_ajax_object.ajax_nonce,
		action   : 'decrypt_file_wptc',
		data     : {
			file : file,
			key  : jQuery('#wptc-fileupload-decrpty-key').val()
		},
	}, function(data) {

		console.log(data);

		jQuery('#wptc-fileupload').removeClass('disabled').removeAttr("disabled", "disabled");

		if (!data) {
			return jQuery('#files').html('error : Empty response, Cannot decrypt the file').css('color', '#ca0606');
		}

		try{
			data = jQuery.parseJSON(data);
		} catch(err){
			return jQuery('#files').html('error : ' + err).css('color', '#ca0606');
		}

		if (data.error) {
			return jQuery('#files').html('error : ' + data.error).css('color', '#ca0606');
		}

		if (data.status === 'error') {
			return jQuery('#files').html('error : ' + data.status.msg).css('color', '#ca0606');
		}

		if (data.status === 'success') {
			jQuery('#files').html(data.message);
		}

	});
}

function show_prevent_realtime_warning(backup_type) {
	var backup_frequencies_proper_name = {
		every_1_hour: 'Realtime',
		every_6_hours: 'Every 6h'
	};

	var backup_frequencies_proper_name_2 = {
		every_1_hour: 'realtime backups ',
		every_6_hours: '6h-backups '
	};

	swal({
		title              : wptc_get_dialog_header('Can\'t Activate '+backup_frequencies_proper_name_2[backup_type]+''),
		html               : wptc_get_dialog_body('We currently don\'t support '+backup_frequencies_proper_name_2[backup_type]+' when the number of tables is more than '+TRIGGER_PREVENT_TABLES_COUNT_WPTC+'. ' , 'error'),
		padding            : '0px 0px 10px 0',
		buttonsStyling     : false,
		showCancelButton   : false,
		confirmButtonColor : '',
		confirmButtonClass : 'button-primary wtpc-button-primary',
		confirmButtonText  : 'Ok',
	});
}

function check_and_prevent_realtime_selection_wptc() {
	var value = jQuery('#select_wptc_backup_slots').val();
	if(value == 'every_1_hour' || value == 'every_6_hours'){
		var no_of_tables = jQuery('#tables_count_total_wptc').val();
		var excluded_tables = jQuery('#excluded_tables_count_wptc').val();
		if(!no_of_tables){

			return false;
		}

		var calc_tables = parseInt(no_of_tables) - parseInt(excluded_tables) - 8;

		console.log(calc_tables);

		if(calc_tables > TRIGGER_PREVENT_TABLES_COUNT_WPTC){
			jQuery('#select_wptc_backup_slots option[value="daily"]').prop('selected', true);
			show_prevent_realtime_warning(value);
		}
	}
}

function show_schedule_time_wptc(){
	var value = jQuery('#select_wptc_backup_slots').val();
	if (value === 'daily') {
		jQuery('#select_wptc_default_schedule').show();
	} else {
		jQuery('#select_wptc_default_schedule').hide();
	}
}

function wptc_login_request(data){

	wptc_set_login_error('', false);

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'login_request_wptc',
		data: data
	}, function(data) {

		jQuery('#wptc_login').removeClass('disabled');

		try{
			var data = jQuery.parseJSON(data);
		} catch (e){
			wptc_set_login_error('Something went wrong.', true);
			return false;
		}

		if (typeof data.status != 'undefined' && data.status == 'success') {
			parent.location.assign(data.url);
			return false;
		} else 	if (typeof data.status != 'undefined' && data.status == 'error') {
			wptc_set_login_error(data.msg, true);
			return false;
		} else {
			wptc_set_login_error('Something went wrong.', true);
			return ;
		}

	}).fail( function(error) {
		wptc_set_login_error('Something went wrong.', true);
		console.log(error);
	});
}

function wptc_set_login_error(msg, show ){
	if (show) {
		jQuery('.wptc_error_div').html(msg).show();
		jQuery('#wptc_login').val('Login');
		jQuery('#wptc_login').removeClass('disabled');
		return ;
	}

	jQuery('.wptc_error_div').hide();
	jQuery('#wptc_login').val('Logging in');
}

function wptc_make_cloud_auth_req(auth_url_func, data, wptc_gdrive_token_btn, cloud_type){
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: auth_url_func,
		credsData: data
	}, function(data) {
		try{
			var data = jQuery.parseJSON(data);
		} catch (e){
			if (typeof wptc_gdrive_token_btn != 'undefined' && wptc_gdrive_token_btn) {
				jQuery('.cloud_error_mesg_g_drive_token').addClass('cloud_acc_connection_error').html(data).show();
				delete wptc_gdrive_token_btn;
			} else {
				jQuery('.cloud_error_mesg').addClass('cloud_acc_connection_error').html(data).show();
			}
			jQuery('#connect_to_cloud').removeClass('disabled').removeAttr("disabled").val('Connect to '+cloud_type);
			jQuery('#connect_to_cloud, #save_g_drive_refresh_token').removeClass('disabled');
			return false;
		}

		if (typeof data.error != 'undefined') {
			jQuery('#connect_to_cloud').removeClass('disabled').removeAttr("disabled").val('Connect to '+cloud_type);
			jQuery('.cloud_error_mesg').addClass('cloud_acc_connection_error').html(data.error).show();
			jQuery('#connect_to_cloud, #save_g_drive_refresh_token').removeClass('disabled');
			return false;
		}

		parent.location.assign(data.authorize_url);
	});
}

function enable_settings_button_wptc(){
	jQuery("#wptc_save_changes").removeAttr('disabled').removeClass('disabled').val("Save Changes").html("Save");
	jQuery('#exc_files_db_canc').css('color','#0073aa').unbind('click', false);
}

function save_settings_wptc(){
	var hash = jQuery('.wptc-nav-tab-wrapper .nav-tab-active').attr('href');
	switch(hash){
		case '':
		case '#wp-time-capsule-tab-general':
		save_general_settings_wptc();
		break;
		case '#wp-time-capsule-tab-backup':
		save_backup_settings_wptc();
		break;
		case '#wp-time-capsule-tab-bbu':
		save_bbu_settings_wptc();
		break;
		case '#wp-time-capsule-tab-vulns':
		save_vulns_settings_wptc();
		break;
		case '#wp-time-capsule-tab-staging':
		save_staging_settings_wptc();
		break;
		case '#wp-time-capsule-tab-advanced':
		save_advanced_settings_wptc();
		break;
		default:
		jQuery("#wptc_save_changes").removeAttr('disabled').removeClass('disabled').val("Hash does not match !").html("Hash does not match !");
		enable_settings_button_wptc();
	}
}

function save_general_settings_wptc(){
	var anonymouse = jQuery('input[name=anonymous_datasent]:checked').val();
	save_settings_ajax_request_wptc('save_general_settings_wptc', {'anonymouse' : anonymouse});
}

function save_advanced_settings_wptc(){
	// save_settings_ajax_request_wptc('save_advanced_settings_wptc', {});
}

function save_settings_ajax_request_wptc(action, data){
	jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: action,
			data : data,
	}, function(data) {
		try{
			var data = jQuery.parseJSON(data);
		} catch(err){
			return ;
		}

		if (data == undefined) {
			swal({
				title              : wptc_get_dialog_header('Oops...'),
				html               : wptc_get_dialog_body('Update setting failed, Please try again!' , 'error'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});
		} else 	if (data.notice == undefined) {
			swal({
				title              : wptc_get_dialog_header('Success'),
				html               : wptc_get_dialog_body('Settings updated successfully!' , 'success'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});
		} else {
			swal({
				title              : wptc_get_dialog_header(data.notice.title),
				html               : wptc_get_dialog_body(data.notice.message , data.notice.type),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'I understand',
			}).then(function () {
					swal({
						title              : wptc_get_dialog_header('Success'),
						html               : wptc_get_dialog_body('Settings updated successfully!' , 'success'),
						padding            : '0px 0px 10px 0',
						buttonsStyling     : false,
						showCancelButton   : false,
						confirmButtonColor : '',
						confirmButtonClass : 'button-primary wtpc-button-primary',
						confirmButtonText  : 'Ok',
					});
				}
			);
		}

		enable_settings_button_wptc();
	});
}

function save_backup_settings_wptc(){
	var backup_slot = '';
	if (jQuery("#select_wptc_backup_slots").hasClass('disabled') === false) {
		var backup_slot  = jQuery("#select_wptc_backup_slots").val();
	}

	var scheduled_time = '';
	if (jQuery("#select_wptc_default_schedule").hasClass('disabled') === false) {
		var scheduled_time  = jQuery("#select_wptc_default_schedule").val();
	}

	var timezone = '';
	if (jQuery("#wptc_timezone").hasClass('disabled') === false) {
		var timezone  = jQuery("#wptc_timezone").val();
	}

	var revision_limit 					          = jQuery( "#wptc-restore-window-slider" ).slider( "value" )
	var user_excluded_extenstions                 = jQuery("#user_excluded_extenstions").val();
	var user_excluded_files_more_than_size_status = jQuery('input[name=user_excluded_files_more_than_size_status]:checked').val();
	var user_excluded_files_more_than_size        = jQuery("#user_excluded_files_more_than_size").val();
	var backup_db_query_limit                     = jQuery("#backup_db_query_limit").val();
	var database_encryption_status                = jQuery('input[name=database_encryption_status]:checked').val();
	var database_encryption_key                   = jQuery("#database_encryption_key").val();

	if(database_encryption_status === 'yes' && !database_encryption_key ){
		enable_settings_button_wptc();
		return alert('Error: Enter Encryption Phrase.');
	}

	if (scheduled_time && timezone) {
			var request_params = {
				"backup_slot"                                 : backup_slot,
				"scheduled_time"                              : scheduled_time,
				"timezone"                                    : timezone,
				"revision_limit"                              : revision_limit,
				"user_excluded_extenstions"                   : user_excluded_extenstions,
				"user_excluded_files_more_than_size_settings" : {status: user_excluded_files_more_than_size_status, size: user_excluded_files_more_than_size},
				"backup_db_query_limit"                       : backup_db_query_limit,
				"database_encryption_settings"                : {'status' : database_encryption_status, 'key' : database_encryption_key},
			};
	} else {
		var request_params = {
			"revision_limit" : revision_limit,
			"user_excluded_extenstions" : user_excluded_extenstions,
			"user_excluded_files_more_than_size_settings" : {status: user_excluded_files_more_than_size_status, size: user_excluded_files_more_than_size},
		};
	}

	if (wptc_current_repo === 's3' && revision_limit < wptc_current_revision_limit) {
		swal({
			title              : wptc_get_dialog_header('Are you sure you want to reduce your Restore Window ?'),
			html               : wptc_get_dialog_body('It will delete your previous restore points.', ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : true,
			confirmButtonColor : '',
			cancelButtonColor  : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			cancelButtonClass  : 'button-secondary wtpc-button-secondary',
			confirmButtonText  : 'Confirm',
			cancelButtonText   : 'Cancel',
		}).then(function () {
			wptc_current_revision_limit = revision_limit;
			save_settings_ajax_request_wptc('save_backup_settings_wptc', request_params);
		}, function (dismiss) {
			enable_settings_button_wptc();
			return ;
		});

		return ;
	}

	wptc_current_revision_limit = revision_limit;

	save_settings_ajax_request_wptc('save_backup_settings_wptc', request_params);
}

function save_bbu_settings_wptc(){

	var backup_before_update_setting      = jQuery('#backup_before_update_always').is(":checked");
	var backup_type                       = jQuery('#backup_type').val();
	var auto_update_wptc_setting          = jQuery('input[name=auto_update_wptc_setting]:checked').val();
	var auto_updater_core_major           = jQuery('input[name=wptc_auto_core_major]:checked').val();
	var auto_updater_core_minor           = jQuery('input[name=wptc_auto_core_minor]:checked').val();
	var auto_updater_plugins              = jQuery('input[name=wptc_auto_plugins]:checked').val();
	var auto_updater_plugins_included     = jQuery('#auto_include_plugins_wptc').val();
	var auto_updater_themes               = jQuery('input[name=wptc_auto_themes]:checked').val();
	var auto_updater_themes_included      = jQuery('#auto_include_themes_wptc').val();
	var schedule_enabled                  = jQuery('input[name=wptc_auto_update_schedule_enabled]:checked').val();
	var include_new_plugins_automatically = jQuery('#wptc-include-new-plugins-au').hasClass('fancytree-selected') ? 1 : 0;
	var include_new_themes_automatically  = jQuery('#wptc-include-new-themes-au').hasClass('fancytree-selected') ? 1 : 0;

	var schedule_time = '';

	if (jQuery("#wptc_auto_update_schedule_time").hasClass('disabled') === false) {
		var schedule_time  = jQuery("#wptc_auto_update_schedule_time").val();
	}

	var request_params = {
		"backup_before_update_setting"  : backup_before_update_setting,
		"auto_update_wptc_setting"      : auto_update_wptc_setting,
		"auto_updater_core_major"       : (auto_updater_core_major) ? auto_updater_core_major : 0,
		"auto_updater_core_minor"       : (auto_updater_core_minor) ? auto_updater_core_minor : 0,
		"auto_updater_plugins"          : (auto_updater_plugins) ? auto_updater_plugins : 0,
		"auto_updater_plugins_included" : (auto_updater_plugins_included) ? auto_updater_plugins_included : '',
		"auto_updater_themes"           : (auto_updater_themes) ? auto_updater_themes : 0,
		"auto_updater_themes_included"  : (auto_updater_themes_included) ? auto_updater_themes_included : '',
		"schedule_time"              	: (schedule_time) ? schedule_time : '',
		"schedule_enabled"  			: (schedule_enabled) ? schedule_enabled : '',
		"include_automatically"  		: {plugins: include_new_plugins_automatically, themes: include_new_themes_automatically},
	}
	save_settings_ajax_request_wptc('save_bbu_settings_wptc', request_params);
}

function save_staging_settings_wptc(){
	var db_rows_clone_limit_wptc     = jQuery("#db_rows_clone_limit_wptc").val();
	var files_clone_limit_wptc       = jQuery("#files_clone_limit_wptc").val();
	var deep_link_replace_limit_wptc = jQuery("#deep_link_replace_limit_wptc").val();
	var enable_admin_login_wptc      = jQuery('input[name=enable_admin_login_wptc]:checked').val();
	var reset_permalink_wptc         = jQuery('input[name=reset_permalink_wptc]:checked').val();
	var login_custom_link_wptc       = jQuery('#login_custom_link_wptc').val();
	var user_excluded_extenstions_staging       = jQuery('#user_excluded_extenstions_staging').val();

	var request_params = {
			"db_rows_clone_limit_wptc"     : db_rows_clone_limit_wptc,
			"files_clone_limit_wptc"       : files_clone_limit_wptc,
			"deep_link_replace_limit_wptc" : deep_link_replace_limit_wptc,
			"enable_admin_login_wptc"      : enable_admin_login_wptc,
			"reset_permalink_wptc"         : reset_permalink_wptc,
			"login_custom_link_wptc"       : login_custom_link_wptc,
			"user_excluded_extenstions_staging"       : user_excluded_extenstions_staging,
	};

	save_settings_ajax_request_wptc('save_staging_settings_wptc', request_params);
}

function save_vulns_settings_wptc(){
	var enable_vulns_email_wptc = jQuery('input[name=enable_vulns_email_wptc]:checked').val();
	var vulns_wptc_setting = jQuery('input[name=vulns_wptc_setting]:checked').val();
	var wptc_vulns_core = jQuery('input[name=wptc_vulns_core]:checked').val();
	var wptc_vulns_plugins = jQuery('input[name=wptc_vulns_plugins]:checked').val();
	var wptc_vulns_themes = jQuery('input[name=wptc_vulns_themes]:checked').val();
	var vulns_include_themes_wptc = jQuery('#vulns_include_themes_wptc').val();
	var vulns_include_plugins_wptc = jQuery('#vulns_include_plugins_wptc').val();

	var request_params = {
		"enable_vulns_email_wptc": enable_vulns_email_wptc,
		"vulns_wptc_setting": vulns_wptc_setting,
		"wptc_vulns_core": wptc_vulns_core,
		"wptc_vulns_plugins": wptc_vulns_plugins,
		"wptc_vulns_themes": wptc_vulns_themes,
		"vulns_themes_included": vulns_include_themes_wptc,
		"vulns_plugins_included": vulns_include_plugins_wptc,
	};

	save_settings_ajax_request_wptc('save_vulns_settings_wptc', request_params);
}

function wptc_trigger_login(e) {

	if (e.which == 13) {
		jQuery("#wptc_login").click();
		return false;
	} else {
		jQuery('.wptc_error_div').html('').hide();
	}
}

function wptc_analyze_inc_exc_lists(is_continue){

	if (!is_continue) {
		wptc_cache_lists_of_files = [];
		swal({
			title              : wptc_get_dialog_header('Analyzing ...'),
			html               : wptc_get_dialog_body('Do not close the window, it will take few mins' , ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : false,
			confirmButtonColor : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			confirmButtonText  : 'Ok',
		});
	}

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'analyze_inc_exc_lists_wptc',
	}, function(response) {
		response = jQuery.parseJSON(response)

		if (response.status == 'continue') {
			wptc_combine_cache_lists_of_files(response.files);
			wptc_analyze_inc_exc_lists('continue');
			return ;
		}
		swal({
			title              : wptc_get_dialog_header('Optimize MySQL backups'),
			html               : wptc_get_dialog_body('Please make changes to the exclusion by moving your mouse near the table name with size greater than 100MB.' , ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : false,
			confirmButtonColor : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			confirmButtonText  : 'OK',
		}).then(function () {
				jQuery('.button.button-secondary.wptc_dropdown').trigger('click');
				// wptc_exclude_all_suggested();
			}, function (dismiss) {
			if (dismiss === 'cancel') {
				swal({
					title              : wptc_get_dialog_header('Success'),
					html               : wptc_get_dialog_body('You custom changes are saved!' , 'success'),
					padding            : '0px 0px 10px 0',
					buttonsStyling     : false,
					showCancelButton   : false,
					confirmButtonColor : '',
					confirmButtonClass : 'button-primary wtpc-button-primary',
					confirmButtonText  : 'Ok',
				});
			}
		});

		if (Object.keys(response).length == 0) {
			swal({
				title              : wptc_get_dialog_header('Your database has been analyzed'),
				html               : wptc_get_dialog_body('Everything looks good!' , ''),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});

			return ;
		}

		if ( ( !response.tables || !response.tables.length ) && ( !response.files || !response.files.length ) ) {
			swal({
				title              : wptc_get_dialog_header('Your database has been analyzed'),
				html               : wptc_get_dialog_body('Everything looks good!' , ''),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});
			return ;
		}

		if (!response.tables || !response.tables.length ) {
			jQuery("#wptc-suggested-exclude-tables").html('All tables are good !');
		} else {
			add_suggested_tables_lists_wptc(response.tables);
		}

		// if (!response.files || !response.files.length) {
		// 	jQuery("#wptc-suggested-exclude-files").html('All files are good !');
		// } else {
		// 	add_suggested_files_lists_wptc(response.files);
		// }

	});
}

function wptc_combine_cache_lists_of_files(new_list){
	if (typeof wptc_cache_lists_of_files == 'undefined' || wptc_cache_lists_of_files.length === 0) {
		wptc_cache_lists_of_files = new_list;
	} else {
		wptc_cache_lists_of_files = wptc_cache_lists_of_files.concat(new_list);
	}
}

function add_suggested_files_lists_wptc(source_data){
	wptc_combine_cache_lists_of_files(source_data);

	jQuery("#wptc-suggested-exclude-files").fancytree({
		checkbox: false,
		selectMode: 3,
		clickFolderMode: 3,
		debugLevel:0,
		source: wptc_cache_lists_of_files,
		postProcess: function(event, data) {
			data.result = data.response;
		},
		init: function (event, data) {
			data.tree.getRootNode().visit(function (node) {
				if (node.data.preselected) node.setSelected(true);
				if (node.data.partial) node.addClass('fancytree-partsel');
			});
		},
		renderNode: function(event, data){ // called for every toggle
			if (!data.node.getChildren())
				return false;
			if(data.node.expanded === false){
				data.node.resetLazy();
			}
			jQuery.each( data.node.getChildren(), function( key, value ) {
				if (value.data.preselected){
					value.setSelected(true);
				} else {
					value.setSelected(false);
				}
			});
		},
		loadChildren: function(event, data) {
			data.node.fixSelection3AfterClick();
			data.node.fixSelection3FromEndNodes();
			last_lazy_load_call = jQuery.now();
		},
		dblclick: function(event, data) {
			return false;
			// data.node.toggleSelected();
		},
		keydown: function(event, data) {
			if( event.which === 32 ) {
				data.node.toggleSelected();
				return false;
			}
		},
		cookieId: "fancytree-Cb3",
		idPrefix: "fancytree-Cb3-"
	}).on("mouseenter", '.fancytree-node', function(event){
		mouse_enter_files_wptc(event);
	}).on("mouseleave", '.fancytree-node' ,function(event){
		mouse_leave_files_wptc(event);
	}).on("click", '.fancytree-file-exclude-key' ,function(event){
		mouse_click_files_exclude_key_wptc(event);
	}).on("click", '.fancytree-file-include-key' ,function(event){
		mouse_click_files_include_key_wptc(event);
	});
}

function add_suggested_tables_lists_wptc(source_data){

	jQuery("#wptc-suggested-exclude-tables").fancytree({
		checkbox: false,
		selectMode: 1,
		icon:false,
		debugLevel:0,
		source: source_data,
		init: function (event, data) {
			data.tree.getRootNode().visit(function (node) {
				if (node.data.preselected){
					node.setSelected(true);
					node.selected = true;
					node.addClass('fancytree-selected ');
					if (node.data.content_excluded && node.data.content_excluded == 1) {
						node.addClass('fancytree-partial-selected ');
					}
				}
			});
		},
		loadChildren: function(event, ctx) {
			last_lazy_load_call = jQuery.now();
		},
		dblclick: function(event, data) {
			return false;
		},
		keydown: function(event, data) {
			if( event.which === 32 ) {
				data.node.toggleSelected();
				return false;
			}
		},
		cookieId: "fancytree-Cb3",
		idPrefix: "fancytree-Cb3-"
	}).on("mouseenter", '.fancytree-node', function(event){
		mouse_enter_tables_wptc(event, 'backup');
	}).on("mouseleave", '.fancytree-node' ,function(event){
		mouse_leave_tables_wptc(event, 'backup');
	}).on("click", '.fancytree-table-exclude-key' ,function(event){
		mouse_click_table_exclude_key_wptc(event, 'backup');
	}).on("click", '.fancytree-table-include-key' ,function(event){
		mouse_click_table_include_key_wptc(event, 'backup');
	}).on("click", '.fancytree-table-exclude-content' ,function(event){
		mouse_click_table_exclude_content_wptc(event, 'backup');
	});

}

function wptc_exclude_all_suggested(){
	var files 	= wptc_get_exclude_all_suggested_items('files');
	var tables 	= wptc_get_exclude_all_suggested_items('tables');

	swal({
		title              : wptc_get_dialog_header('Processing ...'),
		html               : wptc_get_dialog_body('Excluding contents for all those tables...' , ''),
		padding            : '0px 0px 10px 0',
		buttonsStyling     : false,
		showCancelButton   : false,
		confirmButtonColor : '',
		confirmButtonClass : 'button-primary wtpc-button-primary',
		confirmButtonText  : 'Ok',
	});

	jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'exclude_all_suggested_items_wptc',
			data : {
				files: files,
				tables: tables,
			},
	}, function(response) {
		console.log(response);
		response = jQuery.parseJSON(response)
		if (response.status !== 'success') {
			swal({
				title              : wptc_get_dialog_header('Something went wrong!'),
				html               : wptc_get_dialog_body('Cannot save the list! please try again!' , 'error'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});

			return ;
		}

		swal({
			title              : wptc_get_dialog_header('Success'),
			html               : wptc_get_dialog_body('Changes saved' , 'success'),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : false,
			confirmButtonColor : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			confirmButtonText  : 'Ok',
		});

	});
}

function wptc_get_exclude_all_suggested_items(type){
	var id = '';
	if (type === 'files') {
		id = '#wptc-suggested-exclude-files';
	} else {
		id = '#wptc-suggested-exclude-tables';
	}
	if(!jQuery(id + " ul").hasClass('ui-fancytree') || jQuery(id).fancytree('getTree').rootNode === undefined){
		return false;
	}

	var childrens = jQuery(id).fancytree('getTree').getRootNode().children;

	var items = [];
	jQuery(childrens).each(function(index, value){
		items.push(value.key);
	})

	return items;
}

function wptc_show_all_excluded_files(){
	wptc_cache_lists_of_files = [];
	swal({
		title              : wptc_get_dialog_header('Analyzing...'),
		html               : wptc_get_dialog_body('Do not close the window, it will take few minutes' , ''),
		padding            : '0px 0px 10px 0',
		buttonsStyling     : false,
		showCancelButton   : false,
		confirmButtonColor : '',
		confirmButtonClass : 'button-primary wtpc-button-primary',
		confirmButtonText  : 'Ok',
	});

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'get_all_excluded_files_wptc',
	}, function(response) {
		response = jQuery.parseJSON(response);
		if (!response.files || !response.files.length) {
			swal({
				text: 'No files excluded on this site',
			});
			return ;
		}

		swal({
			title              : wptc_get_dialog_header('Excluded files'),
			html               : wptc_get_dialog_body('<div style="text-align: left;float: left; width:100%" id="wptc-suggested-exclude-files"></div>' , ''),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : false,
			showConfirmButton: false,
		});

		add_suggested_files_lists_wptc(response.files);

	});
}

function delete_file_upload_index_file_wptc(){
	jQuery.post(ajaxurl, {
		security : wptc_ajax_object.ajax_nonce,
		action   : 'delete_file_upload_index_file_wptc',
	}, function(data) {
		try{
			data = jQuery.parseJSON(data);
			if (data.status === 'success') {
				// jQuery('#wptc-fileupload-show').hide();
				// jQuery('#wptc-fileupload').show();
			}
		} catch(err){

		}
	});
}

function wptc_toggle_amazon_s3_connect_note(){
	if (jQuery('input[name=wptc_iam_user_status]:checked').val() === 'full_access') {
		jQuery('#wptc-connect-cloud-note-s3-automatic').show();
		jQuery('#wptc-connect-cloud-note-s3-manual').hide();
		jQuery('#wptc_bucket_note').show();
	} else if (jQuery('input[name=wptc_iam_user_status]:checked').val() === 'restricted_access'){
		jQuery('#wptc-connect-cloud-note-s3-automatic').hide();
		jQuery('#wptc-connect-cloud-note-s3-manual').show();
		jQuery('#wptc_bucket_note').hide();
	}
}
