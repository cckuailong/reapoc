var PlansDialogWptc = {
	adjustBusinessPlanBoxPlacement: function adjustBusinessPlanBoxPlacement(interval) {
		var thisPar = jQuery(".wptc_plans_wrapper");

		var targetObj = jQuery('.wptc_business_3.' + interval, thisPar);
		var targetHtml = jQuery('.wptc_business_3.' + interval, thisPar).html();

		if(typeof targetHtml == 'undefined' || !targetHtml){

			return false;
		}

		var targetClasses = jQuery(targetObj).attr("class");
		var targetStyles = jQuery(targetObj).attr("style");

		var prepHtml = '<div class="'+targetClasses+'" plan_group_class="wptc_business_3" style="'+targetStyles+'" >'+targetHtml+'</div>'

		thisPar.prepend(prepHtml);
		targetObj.remove();
	},

	adjustFreelancerPlanBoxPlacement: function adjustFreelancerPlanBoxPlacement(interval) {
		var thisPar = jQuery(".wptc_plans_wrapper");

		var targetObj = jQuery('.wptc_freelancer_2.' + interval, thisPar);
		var targetHtml = jQuery('.wptc_freelancer_2.' + interval, thisPar).html();

		if(typeof targetHtml == 'undefined' || !targetHtml){

			return false;
		}

		var targetClasses = jQuery(targetObj).attr("class");
		var targetStyles = jQuery(targetObj).attr("style");

		var prepHtml = '<div class="'+targetClasses+'" plan_group_class="wptc_freelancer_2" style="'+targetStyles+'" >'+targetHtml+'</div>'

		thisPar.prepend(prepHtml);
		targetObj.remove();
	},

	adjustAgencyPlanBoxPlacement: function adjustAgencyPlanBoxPlacement(interval) {
		var thisPar = jQuery(".wptc_plans_wrapper");

		var targetObj = jQuery('.wptc_agency_2.' + interval, thisPar);
		var targetHtml = jQuery('.wptc_agency_2.' + interval, thisPar).html();

		if(typeof targetHtml == 'undefined' || !targetHtml){
			
			return false;
		}

		var targetClasses = jQuery(targetObj).attr("class");
		var targetStyles = jQuery(targetObj).attr("style");

		var prepHtml = '<div class="'+targetClasses+'" plan_group_class="wptc_agency_2" style="'+targetStyles+'">'+targetHtml+'</div>'

		thisPar.prepend(prepHtml);
		targetObj.remove();
	},

	adjustLifetimePlanBoxPlacement: function adjustLifetimePlanBoxPlacement(interval) {
		var thisPar = jQuery(".wptc_plans_wrapper");

		var targetObj = jQuery('.wptc_pro_lifetime_200_2.' + interval, thisPar);
		var targetHtml = jQuery('.wptc_pro_lifetime_200_2.' + interval, thisPar).html();

		if(typeof targetHtml == 'undefined' || !targetHtml){
			
			return false;
		}

		var targetClasses = jQuery(targetObj).attr("class");
		var targetStyles = jQuery(targetObj).attr("style");

		var prepHtml = '<div class="'+targetClasses+'" plan_group_class="wptc_pro_lifetime_200_2" style="'+targetStyles+'">'+targetHtml+'</div>'

		thisPar.prepend(prepHtml);
		targetObj.remove();
	},

	adjustDemoPlanBoxPlacement: function adjustDemoPlanBoxPlacement() {
		var thisPar = jQuery(".wptc_plans_wrapper");

		var targetObj = jQuery('.wptc_agency_demo', thisPar);
		var targetHtml = jQuery('.wptc_agency_demo', thisPar).html();

		if(typeof targetHtml == 'undefined' || !targetHtml){
			
			return false;
		}

		var targetClasses = jQuery(targetObj).attr("class");
		var targetStyles = jQuery(targetObj).attr("style");

		var prepHtml = '<div class="'+targetClasses+'" plan_group_class="wptc_agency_demo" style="'+targetStyles+'">'+targetHtml+'</div>'

		thisPar.prepend(prepHtml);
		targetObj.remove();
	},
};

jQuery(document).ready(function($) {
	PlansDialogWptc.adjustBusinessPlanBoxPlacement('lifetime');
	PlansDialogWptc.adjustBusinessPlanBoxPlacement('yearly');
	PlansDialogWptc.adjustFreelancerPlanBoxPlacement('lifetime');
	PlansDialogWptc.adjustFreelancerPlanBoxPlacement('yearly');
	PlansDialogWptc.adjustAgencyPlanBoxPlacement('lifetime');
	PlansDialogWptc.adjustAgencyPlanBoxPlacement('yearly');
	PlansDialogWptc.adjustLifetimePlanBoxPlacement('lifetime');
PlansDialogWptc.adjustDemoPlanBoxPlacement();

	jQuery('.package_wptc').on('mouseenter', function(e){
		// $(this).find(".plan_select_btn_wptc").addClass('active');
	});

	jQuery('.package_wptc').on('mouseleave', function(e){
		// $(this).find(".plan_select_btn_wptc").removeClass('active');
	});

	jQuery('.plan_select_btn_wptc').on('click', function(e){
		var this_par = jQuery(this).closest(".package_wptc");

		var plan_dets = jQuery(".selected_plan_dets_wptc", this_par).attr("to_purchase_wptc");

		if(jQuery(this).hasClass('redirect_to_purchase_page')){
			showClickAfterCheckoutBtnWptc();
			redirectToPurchasePageWptc(jQuery(this));
			return false;
		}

		if(jQuery(this).hasClass('is_free_plan') || jQuery(this).hasClass('is_slot_plan')){

			$(".selected_plan_index_wptc").val($(this).attr('plan_index'));
			proceedDirectSubscriptionWPTC($(this));

			e.preventDefault();
			return false;
		}

		if(jQuery(this).attr('card_added') == '1'){
			show_purchase_confirm_dialog_wptc(jQuery(this));
			fill_plans_conf_dialog_wptc(jQuery(this));
		} else{
			window.open(service_url_wptc + '/index.php?'+plan_dets, '_self');
		}

		e.preventDefault();
	});

	jQuery(document).on('click','.proceed_to_pay_wptc:not(".disabled")', function(e){
		var stripeData = {};
		stripeData['plan_index'] = $(".selected_plan_index_wptc").val();

		jQuery(".plan_select_btn_wptc").attr('disabled', 'disabled');
		jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'proceed_to_pay_wptc',
			data: stripeData,
		}, function(data) {
			data = get_message_in_between_wptc(data);

			if(data != null && data != false){
				data = jQuery.parseJSON(data);
			}

			if(typeof data != 'undefined' && typeof data.success != 'undefined'){
				var success_flap_message = '';
				if(typeof data.success.stripe_payment_succeed != 'undefined'){
					var this_plan_name = jQuery('.dialog-div-initial-wptc .main_name_pur.section_title_wptc').text();

					window.location = adminUrlWptc + 'admin.php?page=wp-time-capsule&show_plan_success_flap=true&b_plan=' + this_plan_name;
				}
			}

			tb_remove();
		});
	});

	jQuery(document).on('click', '.package_wptc .plan_interval_change_wptc', function(e) {
		var to_change = $(this).text();

		var plan_group_class = '.' + $(this).closest('.package_wptc').attr('plan_group_class');
		$(plan_group_class).hide();

		var to_show_class  = plan_group_class + '.' + to_change;
		$(to_show_class).show();

		return false;
	});

	jQuery(document).on('change', '.dialog-div-initial-wptc .agree_terms_wptc', function (e){
		var jqObj = jQuery('.dialog-div-initial-wptc .proceed_to_pay_wptc');
		jqObj.addClass('disabled');
		jqObj.attr("title", "Please agree to the terms and conditions");
		if($(this).is(":checked")){
			jqObj.removeClass('disabled');
			jqObj.removeAttr('title');
		}
	});

	jQuery('.proceed_to_pay_wptc.disabled').on('mouseover', function (e){
		var jqObj = jQuery(this);
		var title = jqObj.attr("title");
		jqObj.parent('span').attr("data-balloon", title);
		jqObj.parent('span').attr("data-balloon-pos", "down");
	});

	jQuery('.proceed_to_pay_wptc.disabled').on('mouseleave', function (e){
		var jqObj = jQuery(this);
		var title = jqObj.attr("title");
		jqObj.parent('span').removeAttr("data-balloon");
		jqObj.parent('span').removeAttr("data-balloon-pos");
	});

	jQuery(document).on('click', '.click_here_after_checkout_wptc', function (e){
		window.location = adminUrlWptc + 'admin.php?page=wp-time-capsule';
	});

	jQuery(document).on('change', '.wptc-select-plans', function (e){
		jQuery('#wptc-select-plans-status').hide().removeClass('notice-error notice-success');

		// wptc_selected_plan_name = jQuery(this).text();

		jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'proceed_to_pay_wptc',
			data: {plan_index: jQuery(this).val(), 'is_change_plan': true},
		}, function(data) {
			jQuery('#wptc-select-plans-status').show();

			if(data == null || data == false || data == undefined){
				jQuery('#wptc-select-plans-status').addClass('notice-error');
				return jQuery('#wptc-select-plans-status p').html('Error : Empty Response, Try Again.');
			}

			try{
				data = jQuery.parseJSON(data);
			} catch(err){
				jQuery('#wptc-select-plans-status').addClass('notice-error');
				return jQuery('#wptc-select-plans-status p').html('Error : ' + data + ' Try Again.');
			}

			if (typeof data.error != 'undefined' && data.error) {
				jQuery('#wptc-select-plans-status').addClass('notice-error');
				return jQuery('#wptc-select-plans-status p').html('Error : ' + data.error);
			}

			if (typeof data.success != 'undefined' && data.success) {
				jQuery('#wptc-select-plans-status').addClass('notice-success');
				return jQuery('#wptc-select-plans-status p').html('Plan Changed Successfully! for the site ' + data.success.stripe_update_succeeded);
			}


		});

	});

});

function redirectToPurchasePageWptc(jqObj) {
	window.open(jQuery(jqObj).attr('purchase_url'), '_blank');
}

function hideClickAfterCheckoutBtnWptc() {
	jQuery('.click_here_after_checkout_wptc').hide();
}

function showClickAfterCheckoutBtnWptc() {
	// jQuery('.wptc_plans_wrapper').hide();
	jQuery('.plan_select_btn_wptc.buy_now').attr('disabled', 'disabled');
	jQuery('.click_here_after_checkout_wptc').show();
}

function proceedDirectSubscriptionWPTC(jqObj){
	var stripeData = {};
	stripeData['plan_index'] = jQuery(".selected_plan_index_wptc").val();

	var tempPlanName = jQuery(jqObj).attr('plan_name');

	jQuery.post(ajaxurl, {
		security: wptc_ajax_object.ajax_nonce,
		action: 'proceed_to_pay_wptc',
		data: stripeData,
	}, function(data) {
		data = get_message_in_between_wptc(data);

		if(data != null && data != false){
			data = jQuery.parseJSON(data);
		}

		if(typeof data != 'undefined' && typeof data.success != 'undefined' && data.success !== null){
			// window.location = adminUrlWptc + 'admin.php?page=wp-time-capsule';
			window.location = adminUrlWptc + 'admin.php?page=wp-time-capsule&show_plan_success_flap=true&b_plan=' + tempPlanName;
		}else if(typeof data != 'undefined' && typeof data.error != 'undefined' && data.error !== null){
			window.location = adminUrlWptc + 'admin.php?page=wp-time-capsule&show_plan_error_flap=true&err_msg=' + data.error;
		}
		else {
			window.location = adminUrlWptc + 'admin.php?page=wp-time-capsule&show_plan_error_flap=true&err_msg=Subscription Failed.';
		}
	});
}

function doCallWptc(url, data, callback,dataType){
	jQuery.ajax({
		traditional: true,
		type: 'post',
		url: url,
		data: jQuery.param(data),
		success: function(request) {
			request = get_message_in_between_wptc(request);

			if(request != null && request != false){
				request = jQuery.parseJSON(request);
			}

			if(callback !== undefined){
				eval(callback+"(request)");
			}
		},
		error: function(err) {
		}
	});
}

function get_message_in_between_wptc(response_str){
	var start_str = '<WPTC_START>';
	var start_str_len = start_str.length;
	var end_str = '<WPTC_END>';
	var end_str_len = end_str.length;

	if(response_str.indexOf(start_str) === false){
		return false;
	}

	var start_str_full_pos = response_str.indexOf(start_str) + start_str_len;
	var in_between = response_str.substr(start_str_full_pos);

	var end_str_full_pos = in_between.indexOf(end_str);
	in_between = in_between.substr(0, end_str_full_pos);

	return in_between;
}

function show_purchase_confirm_dialog_wptc(jqObj){
	var dialog_content = '<div class="dialog-div-initial-wptc">'+
		'<div class="top_pur row section_title_wptc">'+
		  '<div class="top_item_pur col-md-7">Item</div>'+
		  '<div class="top_price_pur col-md-2">Price</div>'+
		  '<div class="top_bill_cycle_pur col-md-3">Billing cycle</div>'+
		'</div>'+
		'<div class="clear-both"></div><hr>'+
		'<div class="main_pur row">'+
		  '<div class="main_item_pur pull-left col-md-7">'+
			'<div class="row">'+
			  '<div class="col-md-4 pull-left">'+
				'<div class="pro-plan-badge-div"><img class="wptc-icon" src='+wptcOptionsPageURl+'/images/WPTC-icon.svg>'+
				  '<div class="plan-lable">'+
					'<div class="pro-plan-name main_name_pur">Pro Plan 1</div>'+
				  '</div>'+
				'</div>'+
			  '</div>'+
			  '<div class="col-md-offset-2 col-md-6 pull-left">'+
				'<div class="main_name_pur section_title_wptc"></div>'+
				'<div class="main_desc_pur"></div>'+      
			  '</div>'+
			'</div>'+
		  '</div>'+
		  '<h4 class="main_price_pur dollar_amt_style pull-left col-md-2"></h4>'+
		  '<h4 class="main_bill_cycle_pur pull-left col-md-3"></h4>'+
		'</div>'+
		'<hr>'+
		'<div class="bottom_pur row">'+
		  '<div class="total_price_label col-md-2 col-md-offset-7">Total</div>'+
		  '<div class="total_price col-md-2"></div>'+
		  '<div class="pull-left terms_group_wptc">'+
			'<label class="checkbox-inline">'+
			  '<input type="checkbox" class="agree_terms_wptc" value="">Agree to</label><a href="blah">terms and conditions.</a>'+
		  '</div>'+
		  '<div class="pull-right dialog-btn-group">'+
			'<span><input class="proceed_to_pay_wptc btn btn-primary disabled" type="button" value="Proceed to pay"></span>'+
		  '</div>'+
		'</div>'+
	  '</div>';

	jQuery("#wptc-content-id").html(dialog_content); //since it is the first call we are generating thickbox like this
	jQuery(".wptc-thickbox").click();

	jQuery('#TB_ajaxContent').css('height', '330px');
	jQuery('#TB_title').hide();
	// styling_thickbox_tc('progress');
}

function fill_plans_conf_dialog_wptc(jqObj){
	jQuery(".selected_plan_index_wptc").val(jQuery(jqObj).attr('plan_index'));

	// jQuery(".selected_user_email_wptc").val(jQuery(jqObj).attr('user_email'));

	// jQuery(".selected_user_token_wptc").val(jQuery(jqObj).attr('user_token'));
	// jQuery(".selected_plan_id_wptc").val($(jqObj).attr('plan_id'));

	var par = jqObj.closest('.package_wptc');

	jQuery(".main_name_pur").text(jQuery('.plan_name_wptc', par).text());
	jQuery(".main_desc_pur").html(jQuery('.plan_desc_wptc', par).html());
	jQuery(".main_price_pur").text(jQuery('.dollar_wptc', par).text());
	jQuery(".main_bill_cycle_pur").text(jQuery('.price_interval_wptc', par).text());

	jQuery(".bottom_pur .total_price").text(jQuery('.dollar_wptc', par).text());

	jQuery(".proceed_to_pay_wptc").removeAttr("disabled").attr("site_url", jQuery(jqObj).attr('site_url')).attr("plan_id", jQuery(jqObj).attr('plan_id'));

	jQuery('.proceed_to_pay_wptc').attr("user_email", jQuery(jqObj).attr('user_email'));
	jQuery('.proceed_to_pay_wptc').attr("user_token", jQuery(jqObj).attr('user_token'));
}
