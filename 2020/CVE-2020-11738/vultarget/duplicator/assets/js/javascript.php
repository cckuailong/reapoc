<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<script>
/* DESCRIPTION: Methods and Objects in this file are global and common in 
 * nature use this file to place all shared methods and varibles */	

//UNIQUE NAMESPACE
Duplicator			= new Object();
Duplicator.UI		= new Object();
Duplicator.Pack		= new Object();
Duplicator.Settings = new Object();
Duplicator.Tools	= new Object();
Duplicator.Debug	= new Object();

//GLOBAL CONSTANTS
Duplicator.DEBUG_AJAX_RESPONSE = false;
Duplicator.AJAX_TIMER = null;

Duplicator.parseJSON = function(mixData) {
    try {
		var parsed = JSON.parse(mixData);
		return parsed;
	} catch (e) {
		console.log("JSON parse failed - 1");
		console.log(mixData);
	}

	if (mixData.indexOf('[') > -1 && mixData.indexOf('{') > -1) {
		if (mixData.indexOf('{') < mixData.indexOf('[')) {
			var startBracket = '{';
			var endBracket = '}';
		} else {
			var startBracket = '[';
			var endBracket = ']';
		}
	} else if (mixData.indexOf('[') > -1 && mixData.indexOf('{') === -1) {
		var startBracket = '[';
		var endBracket = ']';
	} else {
		var startBracket = '{';
		var endBracket = '}';
	}
	
	var jsonStartPos = mixData.indexOf(startBracket);
	var jsonLastPos = mixData.lastIndexOf(endBracket);
	if (jsonStartPos > -1 && jsonLastPos > -1) {
		var expectedJsonStr = mixData.slice(jsonStartPos, jsonLastPos + 1);
		try {
			var parsed = JSON.parse(expectedJsonStr);
			return parsed;
		} catch (e) {
			console.log("JSON parse failed - 2");
			console.log(mixData);
			throw e;
            return false;
		}
	}
	throw "could not parse the JSON";
    return false;
}


/* ============================================================================
*  BASE NAMESPACE: All methods at the top of the Duplicator Namespace  
*  ============================================================================	*/

/*	Starts a timer for Ajax calls */ 
Duplicator.StartAjaxTimer = function() 
{
	Duplicator.AJAX_TIMER = new Date();
};

/*	Ends a timer for Ajax calls */ 
Duplicator.EndAjaxTimer = function() 
{
	var endTime = new Date();
	Duplicator.AJAX_TIMER =  (endTime.getTime()  - Duplicator.AJAX_TIMER) /1000;
};

/*	Reloads the current window
*	@param data		An xhr object  */ 
Duplicator.ReloadWindow = function(data) 
{
	if (Duplicator.DEBUG_AJAX_RESPONSE) {
		Duplicator.Pack.ShowError('debug on', data);
	} else {
		window.location.reload(true);
	}
};

//Basic Util Methods here:
Duplicator.OpenLogWindow = function(target)
{
	var target = "log-win" || null;
	if (target != null) {
		window.open('?page=duplicator-tools&tab=diagnostics&section=log', 'log-win');
	} else {
		window.open('<?php echo esc_js(DUPLICATOR_SSDIR_URL); ?>' + '/' + log)
	}
};


/* ============================================================================
*  UI NAMESPACE: All methods at the top of the Duplicator Namespace  
*  ============================================================================	*/

/*	Saves the state of a UI element */ 
Duplicator.UI.SaveViewState = function (key, value) 
{
	if (key != undefined && value != undefined ) {
		jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			dataType: "text",
			data: {
				action : 'DUP_CTRL_UI_SaveViewState',
				key: key,
				value: value,
				nonce: '<?php echo wp_create_nonce('DUP_CTRL_UI_SaveViewState'); ?>'
			},
			success: function(respData) {
				try {
					var data = Duplicator.parseJSON(respData);
				} catch(err) {
					console.error(err);
					console.error('JSON parse failed for response data: ' + respData);
					return false;
				}
			},
			error: function(data) {}
		});	
	}
}

/*	Saves multiple states of a UI element */ 
Duplicator.UI.SaveMulViewStates = function (states)
{
	jQuery.ajax({
		type: "POST",
		url: ajaxurl,
		dataType: "text",
		data: {
			action : 'DUP_CTRL_UI_SaveViewState',
			states: states,
			nonce: '<?php echo wp_create_nonce('DUP_CTRL_UI_SaveViewState'); ?>'
		},
		success: function(respData) {
			try {
				var data = Duplicator.parseJSON(respData);
			} catch(err) {
				console.error(err);
				console.error('JSON parse failed for response data: ' + respData);
				return false;
			}
		},
		error: function(data) {}
	});
}

/* Animates the progress bar */
Duplicator.UI.AnimateProgressBar = function(id) 
{
	//Create Progress Bar
	var $mainbar   = jQuery("#" + id);
	$mainbar.progressbar({ value: 100 });
	$mainbar.height(25);
	runAnimation($mainbar);

	function runAnimation($pb) {
		$pb.css({ "padding-left": "0%", "padding-right": "90%" });
		$pb.progressbar("option", "value", 100);
		$pb.animate({ paddingLeft: "90%", paddingRight: "0%" }, 3000, "linear", function () { runAnimation($pb); });
	}
}

Duplicator.UI.IsSaveViewState = true;
/* Toggle MetaBoxes */ 
Duplicator.UI.ToggleMetaBox = function() 
{
	var $title = jQuery(this);
	var $panel = $title.parent().find('.dup-box-panel');
	var $arrow = $title.parent().find('.dup-box-arrow i');
	var key   = $panel.attr('id');
	var value = $panel.is(":visible") ? 0 : 1;
	$panel.toggle();
	if (Duplicator.UI.IsSaveViewState)
		Duplicator.UI.SaveViewState(key, value);
	(value) 
		? $arrow.removeClass().addClass('fa fa-caret-up') 
		: $arrow.removeClass().addClass('fa fa-caret-down');
	
}

Duplicator.UI.readonly = function(item)
{
	jQuery(item).attr('readonly', 'true').css({color:'#999'});
}

Duplicator.UI.disable = function(item)
{
	jQuery(item).attr('disabled', 'true').css({color:'#999'});
}

Duplicator.UI.enable = function(item)
{
	jQuery(item).removeAttr('disabled').css({color:'#000'});
	jQuery(item).removeAttr('readonly').css({color:'#000'});
}

//Init
jQuery(document).ready(function($) 
{
	
	//INIT: Tabs
	$("div[data-dup-tabs='true']").each(function () {

		//Load Tab Setup
		var $root   = $(this);
		var $lblRoot = $root.find('ul:first-child')
		var $lblKids = $lblRoot.children('li');
		var $pnls	 = $root.children('div');

		//Apply Styles
		$root.addClass('categorydiv');
		$lblRoot.addClass('category-tabs');
		$pnls.addClass('tabs-panel').css('display', 'none');
		$lblKids.eq(0).addClass('tabs').css('font-weight', 'bold');
		$pnls.eq(0).show();

		//Attach Events
		$lblKids.click(function(evt) 
		{
			var $lbls = $(evt.target).parent().children('li');
			var $pnls = $(evt.target).parent().parent().children('div');
			var index = ($(evt.target).index());
			
			$lbls.removeClass('tabs').css('font-weight', 'normal');
			$lbls.eq(index).addClass('tabs').css('font-weight', 'bold');
			$pnls.hide();
			$pnls.eq(index).show();
		});
	 });
	
	//Init: Toggle MetaBoxes
	$('div.dup-box div.dup-box-title').each(function() { 
		var $title = $(this);
		var $panel = $title.parent().find('.dup-box-panel');
		var $arrow = $title.find('.dup-box-arrow');
		$title.click(Duplicator.UI.ToggleMetaBox); 
		($panel.is(":visible")) 
			? $arrow.html('<i class="fa fa-caret-up"></i>')
			: $arrow.html('<i class="fa fa-caret-down"></i>');
	});


	Duplicator.UI.loadQtip = function()
	{
		//Look for tooltip data
		$('i[data-tooltip!=""]').qtip({
			content: {
				attr: 'data-tooltip',
				title: {
					text: function() { return  $(this).attr('data-tooltip-title'); }
				}
			},
			style: {
				classes: 'qtip-light qtip-rounded qtip-shadow',
				width: 500
			},
			 position: {
				my: 'top left',
				at: 'bottom center'
			}
		});
	}

	Duplicator.UI.loadQtip();


	//HANDLEBARS HELPERS
	if  (typeof(Handlebars) != "undefined"){

		function _handleBarscheckCondition(v1, operator, v2) {
			switch(operator) {
				case '==':
					return (v1 == v2);
				case '===':
					return (v1 === v2);
				case '!==':
					return (v1 !== v2);
				case '<':
					return (v1 < v2);
				case '<=':
					return (v1 <= v2);
				case '>':
					return (v1 > v2);
				case '>=':
					return (v1 >= v2);
				case '&&':
					return (v1 && v2);
				case '||':
					return (v1 || v2);
				case 'obj||':
					v1 = typeof(v1) == 'object' ? v1.length : v1;
					v2 = typeof(v2) == 'object' ? v2.length : v2;
					return (v1 !=0 || v2 != 0);
				default:
					return false;
			}
		}

		Handlebars.registerHelper('ifCond', function (v1, operator, v2, options) {
			return _handleBarscheckCondition(v1, operator, v2)
						? options.fn(this)
						: options.inverse(this);
		});

		Handlebars.registerHelper('if_eq',		function(a, b, opts) { return (a == b) ? opts.fn(this) : opts.inverse(this);});
		Handlebars.registerHelper('if_neq',		function(a, b, opts) { return (a != b) ? opts.fn(this) : opts.inverse(this);});
	}

	//Prevent notice boxes from flashing as its re-positioned in DOM
	$('div.dup-wpnotice-box').show(300);

});

jQuery(document).ready(function($) {
    $('.duplicator-message .notice-dismiss, .duplicator-message .duplicator-notice-dismiss, .duplicator-message  .duplicator-notice-rate-now').on('click', function (event) {
		if ('button button-primary duplicator-notice-rate-now' !== $(event.target).attr('class')) {
			event.preventDefault();
		}
        $.post(ajaxurl, {
            action: 'duplicator_set_admin_notice_viewed',
            notice_id: $(this).closest('.duplicator-message-dismissed').data('notice_id')
        });
        var $wrapperElm = $(this).closest('.duplicator-message-dismissed');
        $wrapperElm.fadeTo(100, 0, function () {
            $wrapperElm.slideUp(100, function () {
                $wrapperElm.remove();
            });
        });
    });   
});

</script>