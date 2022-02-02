jQuery(document).ready(function($) {
	jQuery('body').on('click', '#wptc_copy_stage_to_live', function (e){
		if (jQuery(this).hasClass('disabled')) {
			return false;
		}
		jQuery('.staging_area_wptc').hide();
		tb_remove();
		live_in_progress_wptc();
		wptc_copy_stage_to_live();
	});

	wptc_staging_in_progress = false;
	resume_count_wptc = 0;
	window.onbeforeunload = confirm_exist_wptc;

	jQuery('body').on('click', '.close', function (e){
		tb_remove();
	});

	jQuery('body').on('click', '#ask_copy_staging_wptc', function (e){
		// jQuery('.staging_area_wptc').hide();
		var head_html = '<div class="theme-overlay wptc_stage_to_live_confirmation" style="z-index: 1000;">';
		var head_html_1 = '<div class="theme-wrap wp-clearfix" style="width: 450px;height: 220px;margin: auto;">';
		var title = '<div class="theme-header"><button class="close dashicons dashicons-no"><span class="screen-reader-text">Close details dialog</span></button> <h2 style="margin-left: 127px;">Copy staging to live</h2></div>'

		var body = '<div class="theme-about wp-clearfix"> <h4 style="font-weight: 100;text-align: center;">Clicking on Yes will continue to copy your staging site to live site.<br> Are you sure want to continue ? <br><i><b> Note: This cannot be undone</b></i></h4></div>';
		var footer = '<div class="theme-actions"><div class="active-theme"><a lass="button button-primary customize load-customize hide-if-no-customize">Customize</a><a class="button button-secondary">Widgets</a> <a class="button button-secondary">Menus</a> <a class="button button-secondary hide-if-no-customize">Header</a> <a class="button button-secondary">Header</a> <a class="button button-secondary hide-if-no-customize">Background</a> <a class="button button-secondary">Background</a></div><div class="inactive-theme"><a class="button button-primary load-customize hide-if-no-customize btn_pri" id="wptc_copy_stage_to_live" >Yes, COPY</a><a class="button button-secondary activate close btn_sec">No</a></div></div></div></div>';
		var html = head_html+head_html_1+title+body+footer;
		jQuery(".wptc-thickbox").click();
		jQuery('#TB_ajaxContent').hide();
		jQuery('#TB_load').remove();
		jQuery('#TB_window').html(html).removeClass('thickbox-loading');
		jQuery('#TB_title').remove();
	});

	jQuery('body').on('click', '#resume_s2l_wptc', function (e){
		resume_count_wptc = 0;

		resume_s2l_auto_click_wptc();
	});

	//Remove all notices to avoid css collapse
	// jQuery('#update-nag').remove();
});

function resume_s2l_auto_click_wptc(){
	if(jQuery('#resume_s2l_wptc').is(":visible")){
		jQuery('#resume_s2l_wptc').hide();
		jQuery('#staging_progress_bar_note').css('color', 'black');
		wptc_copy_stage_to_live();
	}
}


function confirm_exist_wptc(){
	if (wptc_staging_in_progress) {
		return "Do not close the tab until process gets done !";
	}
}

function live_in_progress_wptc(){
	var html = copying_in_progress_template_wptc();
	jQuery('#staging_area_wptc').after(html);
}

 function copying_in_progress_template_wptc(){
	var header = '<div id="dashboard_activity" class="postbox wptc-progress" style="width: 700px;margin: 60px auto 460px;"> <h2 class="hndle ui-sortable-handle title-bar-staging-wptc"><span style="margin-left: 15px;position: relative;bottom: 8px;">Staging to Live Progress</span><span style="margin-left: 15px;position: relative;bottom: 8px;float: right;right: 35px; display:none" id="staging_err_retry"><a style="cursor: pointer;text-decoration: underline; font-size: 14px; float: right;">Try again</a></span></h2><div class="inside" style="width: 500px; height: 180px;">';
	var inside = ' <div style="min-height: 40px;background: #fef4f4;border-left: 5px solid #e82828;width: 330px;position: absolute;left: 102px;top: 21px; display:none"><span style="position: relative;left: 5px;top: 10px;word-break: break-word;">Error: Folder Paths mismatch</span></div> <div class="l1 wptc_prog_wrap_staging" style=" top: 40px;position: relative; margin: 0px 0px 0px 90px; width: 100% !important;"><div class="staging_progress_bar_cont"><span id="staging_progress_bar_note">Syncing changes</span><div class="staging_progress_bar" style="width:0%"></div></div></div>';
	var footer = '<div class="l1 do_not_close_staging" style="position: relative;top: 90px;text-align: center;left: 100px;">Do not close the tab. until it’s done. <div id="resume_s2l_wptc" style="margin-top: 6px; display: none;"><a class="button button-primary">Resume</a></div> </div></div></div><?php';
	var final_html = header + inside + footer;
	return final_html;
}

function showS2LResumeButton() {
	jQuery("#staging_progress_bar_note").html('Error: Something went wrong, please click the resume button.').css('color', '#ca4a1f');
	jQuery('#resume_s2l_wptc').show();

	if(typeof resume_count_wptc == 'undefined'){
		resume_count_wptc = 0;
	}

	if(resume_count_wptc > 9){

		return;
	}

	resume_count_wptc++;

	setTimeout(function(){

		resume_s2l_auto_click_wptc();
	}, 180000);
}

function wptc_copy_stage_to_live(){
	wptc_staging_in_progress = true;
	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'wptc_copy_stage_to_live',
	}, function(data) {
		try{
			var data = jQuery.parseJSON(data);

			console.log(data);
		
			jQuery('#staging_progress_bar_note').html(data.msg);
			jQuery(".staging_progress_bar").css('width', data.percentage+'%');

			if(typeof data == 'undefined' || !data.status ){
				showS2LResumeButton();

				return;
			}

			if(data.status === 'continue'){
				wptc_copy_stage_to_live();
			} else if(data.status == 'error'){
				jQuery("#staging_progress_bar_note").html('Error: '+ data.msg);
				jQuery('#resume_s2l_wptc').show();

				if(typeof resume_count_wptc == 'undefined'){
					resume_count_wptc = 0;
				}

				if(resume_count_wptc > 10){

					return;
				}

				resume_count_wptc++;

				setTimeout(function(){
					resume_s2l_auto_click_wptc();
				}, 180000);

			} else {
				wptc_staging_in_progress = false;
				jQuery('#last_copy_to_live').html(data.time);
				setTimeout(function(){
					jQuery('#dashboard_activity').hide();
					jQuery('.staging_area_wptc').show();
				}, 3000);
			}

		} catch(err){
			console.log(err, 'caught live js');

			showS2LResumeButton();

			return ;
		}
	}).fail(function(request) {
		showS2LResumeButton();
	});;
}
