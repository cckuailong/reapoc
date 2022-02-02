jQuery(document).ready(function($) {

	jQuery('body').on('click', '.restore_to_staging_wptc', function (){
		if (jQuery(this).hasClass('disabled')) {
			return ;
		}
		restore_obj = this;

		jQuery('#TB_overlay, #TB_ajaxContent').hide();


		swal({
			title              : wptc_get_dialog_header('Are you sure?'),
			html               : wptc_get_dialog_body('This will erase your entire staging site and do fresh staging then initiate the restore !'),
			padding            : '0px 0px 10px 0',
			buttonsStyling     : false,
			showCancelButton   : true,
			confirmButtonColor : '',
			cancelButtonColor  : '',
			confirmButtonClass : 'button-primary wtpc-button-primary',
			cancelButtonClass  : 'button-secondary wtpc-button-secondary',
			confirmButtonText  : 'Do it',
			cancelButtonText   : 'Cancel',
		}).then(function () {
			dialog_close_wptc();
			// jQuery('#TB_overlay, #TB_ajaxContent').show();
			backupclickProgress = false;

			swal({
				title              : wptc_get_dialog_header('Process started !'),
				html               : wptc_get_dialog_body('During the restore process on your staging site, there will be multiple page redirects. Don\'t close the window during this process and kindly wait till it completes.'),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});

			setTimeout(function(){
				wptc_init_restore_to_staging();
			}, 2000);

		}, function (dismiss) {
			// dismiss can be 'cancel', 'overlay',
			// 'close', and 'timer'
			jQuery('#TB_overlay, #TB_ajaxContent').show();
			revert_confirmation_backup_popups_wptc();
			backupclickProgress = false;
			// if (dismiss === 'cancel') {
			// }
		})
	});

});

function wptc_init_restore_to_staging(){

	//as of now support only restore to point to staging
	var cur_res_b_id = jQuery(restore_obj).closest(".single_group_backup_content").attr("this_backup_id");
	var type = 'restore_to_point';

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'init_restore_to_staging_wptc',
		type: type,
		selected_folder: false,
		is_first_call: true,
		cur_res_b_id: cur_res_b_id,
		is_latest_restore_point : wptc_is_latest_restore_point_click(restore_obj),
	}, function(data) {
		var data = jQuery.parseJSON(data);

		console.log('start_restore_in_staging_wptc_stored', data);
		if (data.status === 'success') {
			wptc_R2S_redirect_to_staging = true;
			copy_staging_wptc();
		} else if(data.status === 'error'){
			swal({
				title              : wptc_get_dialog_header('Oops'),
				html               : wptc_get_dialog_body( data.msg, 'error' ),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});

		} else {
			swal({
				title              : wptc_get_dialog_header('Something went wrong'),
				html               : wptc_get_dialog_body( 'Please try again!', 'error' ),
				padding            : '0px 0px 10px 0',
				buttonsStyling     : false,
				showCancelButton   : false,
				confirmButtonColor : '',
				confirmButtonClass : 'button-primary wtpc-button-primary',
				confirmButtonText  : 'Ok',
			});

		}
	});
}

function wptc_redirect_to_staging_page(){
	if (typeof wptc_R2S_redirect_to_staging != 'undefined' && wptc_R2S_redirect_to_staging === true) {
		if (window.location.href.indexOf('wp-time-capsule-staging-options') !== -1) {
			return ;
		}

		delete wptc_R2S_redirect_to_staging;

		parent.location.assign(wptc_ajax_object.admin_url + 'admin.php?page=wp-time-capsule-staging-options');
	}
}