/*NEW DASHBOARD MOBILE MENU AND WIDGET TOGGLING*/
jQuery(document).ready(function($){
	$('#bpfwp-dash-mobile-menu-open').click(function(){
		$('.bpfwp-admin-header-menu .nav-tab:nth-of-type(1n+2)').toggle();
		$('#bpfwp-dash-mobile-menu-up-caret').toggle();
		$('#bpfwp-dash-mobile-menu-down-caret').toggle();
		return false;
	});
	$(function(){
		$(window).resize(function(){
			if($(window).width() > 800){
				$('.bpfwp-admin-header-menu .nav-tab:nth-of-type(1n+2)').show();
			}
			else{
				$('.bpfwp-admin-header-menu .nav-tab:nth-of-type(1n+2)').hide();
				$('#bpfwp-dash-mobile-menu-up-caret').hide();
				$('#bpfwp-dash-mobile-menu-down-caret').show();
			}
		}).resize();
	});	
	$('#bpfwp-dashboard-support-widget-box .bpfwp-dashboard-new-widget-box-top').click(function(){
		$('#bpfwp-dashboard-support-widget-box .bpfwp-dashboard-new-widget-box-bottom').toggle();
		$('#bpfwp-dash-mobile-support-up-caret').toggle();
		$('#bpfwp-dash-mobile-support-down-caret').toggle();
	});
	$('#bpfwp-dashboard-optional-table .bpfwp-dashboard-new-widget-box-top').click(function(){
		$('#bpfwp-dashboard-optional-table .bpfwp-dashboard-new-widget-box-bottom').toggle();
		$('#bpfwp-dash-optional-table-up-caret').toggle();
		$('#bpfwp-dash-optional-table-down-caret').toggle();
	});
});

/*LOCK BOXES*/
jQuery(document).ready(function($){
	$(function(){
		$(window).resize(function(){
			$('.bpfwp-premium-options-table-overlay').each(function(){
				var eachProTableOverlay = $(this);
				var associatedTable = eachProTableOverlay.next();
				var tableWidth = associatedTable.outerWidth(true);
				associatedTable.css('min-height', '240px');
				var tableHeight = associatedTable.outerHeight();
				var tablePosition = associatedTable.position();
				var tableLeft = tablePosition.left; 
				var tableTop = tablePosition.top; 
				eachProTableOverlay.css('width', tableWidth+'px');
				eachProTableOverlay.css('height', tableHeight+'px');
				eachProTableOverlay.css('left', tableLeft+'px');
				eachProTableOverlay.css('top', tableTop+'px');
			});
		}).resize();
	});	
});

//OPTIONS PAGE YES/NO TOGGLE SWITCHES
jQuery(document).ready(function($){
	$('.bpfwp-admin-option-toggle').on('change', function() {
		var Input_Name = $(this).data('inputname'); console.log(Input_Name);
		if ($(this).is(':checked')) {
			$('input[name="' + Input_Name + '"][value="1"]').prop('checked', true).trigger('change');
			$('input[name="' + Input_Name + '"][value=""]').prop('checked', false);
		}
		else {
			$('input[name="' + Input_Name + '"][value="1"]').prop('checked', false).trigger('change');
			$('input[name="' + Input_Name + '"][value=""]').prop('checked', true);
		}
	});
});
