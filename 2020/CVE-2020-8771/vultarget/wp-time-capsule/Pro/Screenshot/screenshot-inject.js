var ajaxUrlWPB = "http://dark.dev.com/wptc-service/index.php";

var screenshot_disable_animation = 0;
var screenshot_disable_transition = 0;
var screenshot_disable_transform = 0;

var CUR_PAGE_WPTC_SS = window.location.href;
CUR_PAGE_WPTC_SS = CUR_PAGE_WPTC_SS.split('?')[0];
CUR_PAGE_WPTC_SS = CUR_PAGE_WPTC_SS.replace(/index.php\//g, '');
CUR_PAGE_WPTC_SS = CUR_PAGE_WPTC_SS.replace(/index.php/g, '');
CUR_PAGE_WPTC_SS = window.btoa(CUR_PAGE_WPTC_SS);
CUR_PAGE_WPTC_SS = CUR_PAGE_WPTC_SS.replace(/=/g, '');

screenshotDisableDivs = {};
screenshotDisableDivs[CUR_PAGE_WPTC_SS] = {};

function doCallInjected(url, data, callback, dataType) {
	if (typeof dataType == 'undefined') {
		dataType = 'json';
	}

	jQuery.ajax({
		traditional: true,
		type: 'post',
		url: url,
		dataType: dataType,
		data: jQuery.param(data),
		success: function(request) {
			if (callback !== undefined) {
				eval(callback + "(request)");
			}
		},
		error: function(err) {
			console.log(err);
			if (callback !== undefined) {
				eval(callback + "({error:'Ajax error.'})");
			}
		}
	});
}

function addPathToScreenshotDisableDivsArr(pathStr){
	pathStr = pathStr.replace(/.logged-in/g, '');
	pathStr = pathStr.replace(/.admin-bar/g, '');
	var pathStrKey = encodeURI(pathStr);

	if(typeof screenshotDisableDivs == 'undefined'){
		screenshotDisableDivs = {};
	}

	if(typeof screenshotDisableDivs[CUR_PAGE_WPTC_SS] == 'undefined'){
		screenshotDisableDivs[CUR_PAGE_WPTC_SS] = {};
	}

	screenshotDisableDivs[CUR_PAGE_WPTC_SS][pathStrKey] = [];
	screenshotDisableDivs[CUR_PAGE_WPTC_SS][pathStrKey].push(pathStr);
}

function removePathFromScreenshotDisableDivsArr(pathStr){
	pathStr = pathStr.replace(/.logged-in/g, '');
	pathStr = pathStr.replace(/.admin-bar/g, '');
	var pathStrKey = encodeURI(pathStr);

	if(screenshotDisableDivs[CUR_PAGE_WPTC_SS] && screenshotDisableDivs[CUR_PAGE_WPTC_SS][pathStrKey]){
		if(Object.keys(screenshotDisableDivs[CUR_PAGE_WPTC_SS]).length === 1){
			screenshotDisableDivs[CUR_PAGE_WPTC_SS] = {};
		} else {
			delete screenshotDisableDivs[CUR_PAGE_WPTC_SS][pathStrKey];
		}
	}
}

function screenshot_save_wptc(){
	var userData = {};
	userData['site_url'] = window.location.href;
	userData['screenshot_disable_divs'] = {};
	// userData['screenshot_disable_divs'][CUR_PAGE_WPTC_SS] = screenshotDisableDivs[CUR_PAGE_WPTC_SS];

	if(Object.keys(screenshotDisableDivs[CUR_PAGE_WPTC_SS]).length == 0){
		userData['screenshot_disable_divs'][CUR_PAGE_WPTC_SS] = '';
	} else {
		userData['screenshot_disable_divs'][CUR_PAGE_WPTC_SS] = screenshotDisableDivs[CUR_PAGE_WPTC_SS];
	}

	jQuery.post(wptc_screenshots_actions.ajaxurl + window.location.search , {
			action: 'screenshot_save_wptc',
			data: userData,
			security: wptc_screenshots_actions.ajax_nonce
	}, function(data) {
		console.log("screenshot_save_response", data);
		alert('Successfully Saved');
	});
}

function att_settings_save_wptc(){
	var userData = {};
	userData['site_url'] = window.location.href;
	userData['screenshot_disable_transform'] = screenshot_disable_transform;
	userData['screenshot_disable_transition'] = screenshot_disable_transition;
	userData['screenshot_disable_animation'] = screenshot_disable_animation;

	jQuery.post(wptc_screenshots_actions.ajaxurl + window.location.search , {
			action: 'att_settings_save_wptc',
			data: userData,
			security: wptc_screenshots_actions.ajax_nonce
	}, function(data) {
		console.log("screenshot_save_response", data);
	});
}

function get_message_in_between_screenshot_wptc(response_str){
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

function prefill_scrn_shot_settings_wptc(){
	var userData = {};
	userData['site_url'] = window.location.href;

	jQuery.post(wptc_screenshots_actions.ajaxurl + window.location.search , {
			action: 'prefill_scrn_shot_settings_wptc',
			data: userData,
			security: wptc_screenshots_actions.ajax_nonce
	}, function(data) {
		var actualData = {};
		var messageInBetween = get_message_in_between_screenshot_wptc(data).toString();

		var jsonData = JSON.parse( messageInBetween );

		if(typeof jsonData.success != 'undefined'){
			actualData = jsonData.success;
		}

		if( actualData.screenshot_disable_animation != null 
			&& actualData.screenshot_disable_animation == true){
			makeAnimationsDisabled();
		} else {
			makeAnimationsEnabled();
		}

		if( actualData.screenshot_disable_transition != null 
			&& actualData.screenshot_disable_transition == true){
			makeTransitionsDisabled();
		} else {
			makeTransitionsEnabled();
		}

		if( actualData.screenshot_disable_transform != null 
			&& actualData.screenshot_disable_transform == true){
			makeTransformsDisabled();
		} else {
			makeTransformsEnabled();
		}

		var tempScreenDisableDivs = {};
		if( typeof actualData.screenshot_disable_divs != 'undefined' 
			&& actualData.screenshot_disable_divs != null
			&& typeof actualData.screenshot_disable_divs != 'string' 
			&& actualData.screenshot_disable_divs.length != 0){
				tempScreenDisableDivs = actualData.screenshot_disable_divs;
		}

		jQuery.each(tempScreenDisableDivs, function(kk, vv){
			if(kk != CUR_PAGE_WPTC_SS){

				return false;
			}

			if(!(typeof vv == 'object' || typeof vv == 'array')){

				return false;
			}

			jQuery.each(vv, function(k, v){
				jQuery(v[0]).trigger('click');
			});
		});

	});
}

function makeAnimationsDisabled(){
	screenshot_disable_animation = 1;
	jQuery('.enable_animation_wptc').attr('disabled_wptc', "true");

	var label_type = jQuery('.enable_animation_wptc').attr('label_type');
	jQuery('.enable_animation_wptc').val(label_type + ' Disabled');
}

function makeAnimationsEnabled(){
	screenshot_disable_animation = 0;
	jQuery('.enable_animation_wptc').attr('disabled_wptc', "false");

	var label_type = jQuery('.enable_animation_wptc').attr('label_type');
	jQuery('.enable_animation_wptc').val(label_type + ' Enabled');
}

function makeTransitionsDisabled(){
	screenshot_disable_transition = 1;
	jQuery('.enable_transition_wptc').attr('disabled_wptc', "true");

	var label_type = jQuery('.enable_transition_wptc').attr('label_type');
	jQuery('.enable_transition_wptc').val(label_type + ' Disabled');
}

function makeTransitionsEnabled(){
	screenshot_disable_transition = 0;
	jQuery('.enable_transition_wptc').attr('disabled_wptc', "false");

	var label_type = jQuery('.enable_transition_wptc').attr('label_type');
	jQuery('.enable_transition_wptc').val(label_type + ' Enabled');
}

function makeTransformsDisabled(){
	screenshot_disable_transform = 1;
	jQuery('.enable_transform_wptc').attr('disabled_wptc', "true");

	var label_type = jQuery('.enable_transform_wptc').attr('label_type');
	jQuery('.enable_transform_wptc').val(label_type + ' Disabled');
}

function makeTransformsEnabled(){
	screenshot_disable_transform = 0;
	jQuery('.enable_transform_wptc').attr('disabled_wptc', "false");

	var label_type = jQuery('.enable_transform_wptc').attr('label_type');
	jQuery('.enable_transform_wptc').val(label_type + ' Enabled');
}

function getRecursiveParentPathFromParJQObj(jQObj, carry){
    var genNode2 = '';

    if(jQObj == null){

        return genNode2;
    }

    var curId = '';
    if(jQObj['id'] != ''){
        curId = '#' + jQObj['id'];
    }

    var curClasses = jQObj['className'].split(' ').join('.');
    if(curClasses != '' && curClasses != '.'){
        curClasses = '.' + curClasses;
    } else if(curClasses == '.') {
        curClasses = '';
    }

    genNode2 = jQObj['nodeName'] + curId + curClasses + ' ' + carry;

    if(jQObj.offsetParent == null){

        return genNode2;
    }

    return getRecursiveParentPathFromParJQObj(jQObj.offsetParent, genNode2);
}

function getNewXPathFromJQObj(jQObj){
    var curId = '';
    if(jQObj[0]['id'] != ''){
        curId = '#' + jQObj[0]['id'];
    }

    var curClasses = jQObj[0]['className'].split(' ').join('.');
    if(curClasses != '' && curClasses != '.'){
        curClasses = '.' + curClasses;
    } else if(curClasses == '.') {
        curClasses = '';
    }

    var genNode = jQObj[0]['nodeName'] + curId + curClasses;
    genNode = genNode.replace(/.wptc_border_screen/g, '');

    var parNodes = getRecursiveParentPathFromParJQObj(jQObj[0].offsetParent, '');
    parNodes = parNodes.replace(/.wptc_border_screen/g, '');

    return parNodes + ' ' + genNode;
}

jQuery.fn.extend({
	getPath: function( path ) {
		if ( typeof path == 'undefined' ){
			path = '';
		}

		if ( this.is('html') ){

			return 'html ' + path;
		}

		var cur = this.get(0).nodeName.toLowerCase();
		var id  = this.attr('id');
		var	class1 = this.attr('class');

		if ( typeof id != 'undefined' && id != null && id != ''){
			cur += '#' + id;
		}

		if ( typeof class1 != 'undefined' && class1 != null){
			var someClassStr = '.' + class1.split(/[\s\n]+/).join('.');

			if(someClassStr == '.'){
				someClassStr = '';
			}

			cur += someClassStr;
		}

		return this.parent().getPath( '>' + ' ' + cur + ' ' + path).replace(/(^[.\s]+)|([.\s]+$)/g, '').replace(".wptc_border_screen", "");
	}
});

function eventsRegisterInjectWptc(argument) {
	jQuery(document).on('click', '*:not(".dont_do_border_wptc")', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();

		if(jQuery(this).hasClass('save_selected_divs_wptc')){

			return false;
		} else if(jQuery(this).hasClass('close_border_screen_perm')){

			return false;
		} else if(jQuery(this).hasClass('wptc_border_screen_perm')){

			return false;
		} else {
			var path = jQuery(this).getPath();
			// var path = getNewXPathFromJQObj(jQuery(this));

			var thisWidth = jQuery(this).css("width");
			var thisHeight = jQuery(this).css("height");

			jQuery(this).children().addClass('wptc_border_screen_perm_children_hide');

			var historyTextAttr = '';

			if(!jQuery(this).children().length){
				historyTextAttr = 'history_text_wptc = "' + jQuery(this).text() + '"';
				jQuery(this).text('');
			}

			var insideDiv = '<div class="inside_border_overlay_wptc dont_do_border_wptc" style="width: '+thisWidth+'; height: '+thisHeight+'; border: 1px solid red; background-color: white;"><div class="close_border_screen_perm dont_do_border_wptc" '+ historyTextAttr +' >X</div></div>';
			jQuery(this).addClass('wptc_border_screen_perm dont_do_border_wptc').prepend(insideDiv);

			addPathToScreenshotDisableDivsArr(path);
		}

		return false;
	});

	jQuery(document).on('hover', '*:not(".dont_do_border_wptc")', function(e) {
		e.stopImmediatePropagation();

		// var path = jQuery(this).getPath();
		jQuery('*').removeClass('wptc_border_screen');
		jQuery(this).addClass('wptc_border_screen');

		return false;
	});

	jQuery(document).on('click', '.save_selected_divs_wptc', function(e){
		e.preventDefault();
		e.stopImmediatePropagation();

		screenshot_save_wptc();

		return false;
	});

	jQuery(document).on('click', '.reset_selected_divs_wptc', function(e){
		e.preventDefault();
		e.stopImmediatePropagation();

		window.location.reload(true);
		// jQuery('.close_border_screen_perm').trigger('click');

		return false;
	});

	jQuery(document).on('click', '.close_border_screen_perm', function(e){
		e.preventDefault();
		e.stopImmediatePropagation();

		var reqJQObj = jQuery(this).closest('.wptc_border_screen_perm');
		jQuery(this).parent('.inside_border_overlay_wptc').remove();

		reqJQObj.children().removeClass('wptc_border_screen_perm_children_hide');
		reqJQObj.removeClass('wptc_border_screen_perm');
		reqJQObj.removeClass('dont_do_border_wptc');

		if( typeof jQuery(this).attr('history_text_wptc') != 'undefined' 
			&& jQuery(this).attr('history_text_wptc') ){
			reqJQObj.text(jQuery(this).attr('history_text_wptc'));
		}
		
		var path = reqJQObj.getPath();
		// var path = getNewXPathFromJQObj(reqJQObj);
		removePathFromScreenshotDisableDivsArr(path);

		reqJQObj.removeClass('wptc_border_screen_perm');

		return false;
	});

	jQuery(document).on('click', '.enabling_att_wptc', function(e){
		e.preventDefault();
		e.stopImmediatePropagation();

		var label_type = jQuery(this).attr('label_type');

		if(jQuery(this).attr('disabled_wptc') == "false"){
			jQuery(this).attr('disabled_wptc', "true");
			jQuery(this).val(label_type + ' Disabled');

			if(label_type == 'Animations'){
				screenshot_disable_animation = 1;
			} else if(label_type == 'Transitions'){
				screenshot_disable_transition = 1;
			} else if(label_type == 'Transforms'){
				screenshot_disable_transform = 1;
			}
		} else {
			jQuery(this).attr('disabled_wptc', "false");
			jQuery(this).val(label_type + ' Enabled');

			if(label_type == 'Animations'){
				screenshot_disable_animation = 0;
			} else if(label_type == 'Transitions'){
				screenshot_disable_transition = 0;
			} else if(label_type == 'Transforms'){
				screenshot_disable_transform = 0;
			}
		}


		att_settings_save_wptc();

		return false;
	});
}

jQuery(document).ready(function() {
	if(jQuery('#wpadminbar').is(':visible') || jQuery('body').hasClass('admin-bar')){
		alert('You need to logout to select the html elements that you want to ignore during screenshot comparison.');
	} else {
		jQuery('body').prepend('<div class="dont_do_border_wptc wptc-top-selector"> <div class="dont_do_border_wptc content-group-wptc"> <span class="dont_do_border_wptc wptc-vs-label">Select the HTML elements to ignore during Screenshot comparision and click Save </span> <div class="enabling_animations dont_do_border_wptc"> <input type="button" class="enabling_att_wptc dont_do_border_wptc enable_animation_wptc" disabled_wptc="false" label_type="Animations" value="Animations enabled"> <input type="button" class="enabling_att_wptc dont_do_border_wptc enable_transition_wptc"  disabled_wptc="false" label_type="Transitions" value="Transitions enabled"> <input type="button" class="enabling_att_wptc dont_do_border_wptc enable_transform_wptc"  disabled_wptc="false" label_type="Transforms" value="Transforms enabled"> </div> <div class="dont_do_border_wptc save_selected_divs_wptc">Save</div> <div class="dont_do_border_wptc reset_selected_divs_wptc">Reset</div> </div> </div>');

		jQuery('#wpadminbar').hide();

		setTimeout(function(){ 
			prefill_scrn_shot_settings_wptc(); 
			eventsRegisterInjectWptc();
		}, 2000);
	}

});