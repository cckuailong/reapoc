jQuery(document).ready(function(){
	jQuery('.wptc-show-more').on('click',function(){
		if(jQuery(this).attr('status') != 'open') {
			jQuery(this).attr('status', 'open');
			jQuery(this).html('Hide details');
		} else {
			jQuery(this).attr('status', '');
			jQuery(this).html('View details');
		}
		var action_id=jQuery(this).attr('action_id');
		var more_logs = jQuery('#'+action_id);
		more_logs.toggle('fast');
	});
	jQuery('body').on('click', '.wptc_activity_log_load_more', function (){
		if(jQuery(this).hasClass('disabled')){
			return false;
		}
		activity_log_obj = this;
		var limit = jQuery(this).attr('limit');
		var action_id = jQuery(this).attr('action_id');
		jQuery(this).addClass('disabled loader').css('color' ,'gray');
		jQuery.post(ajaxurl, {
			security: wptc_ajax_object.ajax_nonce,
			action: 'lazy_load_activity_log_wptc',
			data: {action_id:action_id, limit:limit},
		}, function(data) {
			if (!data.length) {
				return false;
			}
			var parent_tr = jQuery(activity_log_obj).parents('tr')[0];
			if (data != 0) {
				jQuery(parent_tr).after(data);
			}
			jQuery(parent_tr).remove();
		});
	});
});