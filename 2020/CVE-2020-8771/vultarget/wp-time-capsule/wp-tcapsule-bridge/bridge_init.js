jQuery(document).ready(function(){
	jQuery('#check_db_creds').on('click', function(){
		jQuery(".error").html('');
		if (jQuery(this).hasClass('disabled')) {
			return;
		}
		jQuery(this).addClass('processing disabled');
		jQuery(this).text('Validating...');
		var db_host = jQuery("#db_host").val();
		var db_name = jQuery("#db_name").val();
		var db_username = jQuery("#db_username").val();
		var db_password = jQuery("#db_password").val();
		var db_charset = jQuery("#db_charset").val();
		var db_collate = jQuery("#db_collate").val();
		var wp_content_dir = jQuery("#wp_content_dir").val();
		bridge_do_call(db_host, db_name, db_username, db_password, db_charset, db_collate, wp_content_dir);
	});
	jQuery('.show_restore_points').on('click', function(e){
		var current_date = jQuery(this).parents('li');
		if(jQuery(this).text() == "HIDE RESTORE POINTS"){
			jQuery(this).text("SHOW RESTORE POINTS");
		} else {
			jQuery(this).text("HIDE RESTORE POINTS");
		}
		jQuery(current_date).find('.rp').slideToggle("fast");
		e.preventDefault();
		e.stopImmediatePropagation();
	});

	jQuery('#load_from_wp_config').on('click', function(e){
		window.location.assign('index.php?step=show_points');
	});

	jQuery('#custom_creds').on('click', function(e){
		if (jQuery(this).hasClass('disabled')) {
			return false;
		}
		jQuery(this).addClass('disabled');
		jQuery('#db_creds_form').show();
		jQuery(this).removeClass('disabled');
		return false;
	});

	jQuery("body").on('change', '#bridge_replace_links', function(){
		if (jQuery(this).is(':checked')) {
			return jQuery('#replace_link_migration').removeAttr('disabled');
		}
		return jQuery('#replace_link_migration').attr('disabled', 'disabled');
	});

	get_migration_html();

});


function init_meta_upload_listener(){
	jQuery('#fileupload').fileupload({
		url: 'upload/php/',
		dataType: 'json',
		maxChunkSize: 1000 * 1000,//1MB
		add: function(e, data) {
			jQuery(this).attr("disabled", "disabled");
			jQuery('#files, #percentage').html('');
			var uploadErrors = [];
			var acceptFileTypes = ['application/gzip', 'application/sql','application/x-gzip','text/x-sql'];

			if(data.originalFiles[0]['type'].length && acceptFileTypes.indexOf(data.originalFiles[0]['type']) === -1) {
				uploadErrors.push('Not an accepted file type');
			}

			if(uploadErrors.length > 0) {
				alert(uploadErrors.join("\n"));
				jQuery(this).removeAttr("disabled", "disabled");
			} else {
				jQuery('#files').html('File Uploading : ' + data.files[0].name);
				data.submit();
			}
		},
		done: function (e, data) {
			if (data.result.files[0].error) {
				alert("Server Error :" + data.result.files[0].error);
				jQuery(this).removeAttr("disabled", "disabled");
				return;
			}

			jQuery('#files').html('Uploading finished : ' + data.result.files[0].name);
			jQuery('.fileinput-button').html('Importing meta file').attr("disabled", "disabled");
			start_import_meta(data.result.files[0].name);
		},
		progressall: function (e, data) {
			var progress = parseInt(data.loaded / data.total * 100, 10);
			jQuery('.progress').show();
			jQuery('.progress-bar').css('width', progress + '%');
		}
	});
}

function bridge_do_call(db_host, db_name, db_username, db_password, db_charset, db_collate, wp_content_dir){
	var submit_button = jQuery('#check_db_creds');
	jQuery.post('index.php', {
		action: 'check_db_creds',
		data: {db_host:db_host, db_name:db_name, db_username:db_username, db_password:db_password, db_charse:db_charset, db_collate:db_collate, wp_content_dir:wp_content_dir},
	}, function(data) {
		if(data.length == 0){
			submit_button.text('Loading restore points');
			jQuery("#db_creds_form").submit();
		} else{
			submit_button.removeClass('processing disabled');
			submit_button.text('Load restore points');
			 if (data.indexOf("Access denied") != -1) {
				jQuery(".error").html("Access denied. Please check your credentials and try again.");
			} else if(data.indexOf("host") != -1) {
				jQuery(".error").html("No such host is known. Please check host name and try again.");
			} else {
				jQuery(".error").html(data);
			}
		}
	});
}

function start_import_meta(file, offset, position, replace_collation){
	jQuery.post('index.php', {
		action: 'import_meta_file',
		data: {
			file: file,
			offset: (offset != undefined ) ? offset : 0,
			position: (position != undefined ) ? position : 'uncompress',
			replace_collation: (replace_collation != undefined ) ? replace_collation : false,
		},
	}, function(data) {

		if (!data) {
			return jQuery('#files').html('error : Empty response').css('color', '#ca0606');
		}

		data = parse_wptc_response_from_raw_data(data);

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
			// return alert(data.status.msg);
		}

		if (data.status === 'continue') {
			jQuery('#files').html('Importing meta file (' + data.offset + ')');
			return start_import_meta(file, data.offset, data.position, data.replace_collation);
		}

		if (data.status === 'completed') {
			jQuery('#files').html('Meta imported');
			location.reload(true);
		}

	});
}

function get_migration_html(){

	if (location.href.indexOf('step=show_points') === -1 ) {
		return ;
	}

	jQuery.post('index.php', {
		action: 'get_migration_html',
		data: {
			url: location.href,
		},
	}, function(response) {

		if (!response) {
			return ;
		}

		response = parse_wptc_response_from_raw_data(response);

		try{
			response = jQuery.parseJSON(response);
		} catch(err){
			console.log(err);
			return ;
		}

		return jQuery('#migration_settings').html(response.html);

	});
}
