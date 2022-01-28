if(typeof(CFS_DATA) == 'undefined')
	var CFS_DATA = {};
if(isNumber(CFS_DATA.animationSpeed)) 
    CFS_DATA.animationSpeed = parseInt(CFS_DATA.animationSpeed);
else if(jQuery.inArray(CFS_DATA.animationSpeed, ['fast', 'slow']) == -1)
    CFS_DATA.animationSpeed = 'fast';
CFS_DATA.showSubscreenOnCenter = parseInt(CFS_DATA.showSubscreenOnCenter);
var sdLoaderImgCfs = '<img src="'+ CFS_DATA.loader+ '" />';
var g_cfsAnimationSpeed = 300;

jQuery.fn.showLoaderCfs = function() {
    return jQuery(this).html( sdLoaderImgCfs );
};
jQuery.fn.appendLoaderCfs = function() {
    jQuery(this).append( sdLoaderImgCfs );
};
jQuery.sendFormCfs = function(params) {
	// Any html element can be used here
	return jQuery('<br />').sendFormCfs(params);
};
/**
 * Send form or just data to server by ajax and route response
 * @param string params.fid form element ID, if empty - current element will be used
 * @param string params.msgElID element ID to store result messages, if empty - element with ID "msg" will be used. Can be "noMessages" to not use this feature
 * @param function params.onSuccess funstion to do after success receive response. Be advised - "success" means that ajax response will be success
 * @param array params.data data to send if You don't want to send Your form data, will be set instead of all form data
 * @param array params.appendData data to append to sending request. In contrast to params.data will not erase form data
 * @param string params.inputsWraper element ID for inputs wraper, will be used if it is not a form
 * @param string params.clearMsg clear msg element after receive data, if is number - will use it to set time for clearing, else - if true - will clear msg element after 5 seconds
 */
jQuery.fn.sendFormCfs = function(params) {
    var form = null;
    if(!params)
        params = {fid: false, msgElID: false, onSuccess: false};

    if(params.fid)
        form = jQuery('#'+ fid);
    else
        form = jQuery(this);
    
    /* This method can be used not only from form data sending, it can be used just to send some data and fill in response msg or errors*/
    var sentFromForm = (jQuery(form).tagName() == 'FORM');
    var data = new Array();
    if(params.data)
        data = params.data;
    else if(sentFromForm)
        data = jQuery(form).serialize();
    
    if(params.appendData) {
		var dataIsString = typeof(data) == 'string';
		var addStrData = [];
        for(var i in params.appendData) {
			if(dataIsString) {
				addStrData.push(i+ '='+ params.appendData[i]);
			} else
            data[i] = params.appendData[i];
        }
		if(dataIsString)
			data += '&'+ addStrData.join('&');
    }
    var msgEl = null;
    if(params.msgElID) {
        if(params.msgElID == 'noMessages')
            msgEl = false;
        else if(typeof(params.msgElID) == 'object')
           msgEl = params.msgElID;
       else
            msgEl = jQuery('#'+ params.msgElID);
    }
	if(typeof(params.inputsWraper) == 'string') {
		form = jQuery('#'+ params.inputsWraper);
		sentFromForm = true;
	}
	if(sentFromForm && form) {
        jQuery(form).find('*').removeClass('cfsInputError');
    }
	if(msgEl && !params.btn) {
		jQuery(msgEl)
			.removeClass('cfsSuccessMsg')
			.removeClass('cfsErrorMsg');
		if(!params.btn) {
			jQuery(msgEl).showLoaderCfs();
		}
	} 
	if(params.btn) {
		jQuery(params.btn).attr('disabled', 'disabled');
		// Font awesome usage
		params.btnIconElement = jQuery(params.btn).find('.fa').length ? jQuery(params.btn).find('.fa') : jQuery(params.btn);
		if(jQuery(params.btn).find('.fa').length) {
			params.btnIconElement
				.data('prev-class', params.btnIconElement.attr('class'))
				.attr('class', 'fa fa-spinner fa-spin');
		}
	}
    var url = '';
	if(typeof(params.url) != 'undefined')
		url = params.url;
    else if(typeof(ajaxurl) == 'undefined')
        url = CFS_DATA.ajaxurl;
    else
        url = ajaxurl;
    
    jQuery('.cfsErrorForField').hide(CFS_DATA.animationSpeed);
	var dataType = params.dataType ? params.dataType : 'json';
	// Set plugin orientation
	if(typeof(data) == 'string') {
		data += '&pl='+ CFS_DATA.CFS_CODE;
		data += '&reqType=ajax';
	} else {
		data['pl'] = CFS_DATA.CFS_CODE;
		data['reqType'] = 'ajax';
	}
	
    jQuery.ajax({
        url: url,
        data: data,
        type: 'POST',
        dataType: dataType,
        success: function(res) {
            toeProcessAjaxResponseCfs(res, msgEl, form, sentFromForm, params);
			if(params.clearMsg) {
				setTimeout(function(){
					if(msgEl)
						jQuery(msgEl).animateClear();
				}, typeof(params.clearMsg) == 'boolean' ? 5000 : params.clearMsg);
			}
        }
    });
};
/**
 * Hide content in element and then clear it
 */
jQuery.fn.animateClear = function() {
	var newContent = jQuery('<span>'+ jQuery(this).html()+ '</span>');
	jQuery(this).html( newContent );
	jQuery(newContent).hide(CFS_DATA.animationSpeed, function(){
		jQuery(newContent).remove();
	});
};
/**
 * Hide content in element and then remove it
 */
jQuery.fn.animateRemoveCfs = function(animationSpeed, onSuccess) {
	animationSpeed = animationSpeed == undefined ? CFS_DATA.animationSpeed : animationSpeed;
	jQuery(this).hide(animationSpeed, function(){
		jQuery(this).remove();
		if(typeof(onSuccess) === 'function')
			onSuccess();
	});
};
function toeProcessAjaxResponseCfs(res, msgEl, form, sentFromForm, params) {
    if(typeof(params) == 'undefined')
        params = {};
    if(typeof(msgEl) == 'string')
        msgEl = jQuery('#'+ msgEl);
    if(msgEl)
        jQuery(msgEl).html('');
	if(params.btn) {
		jQuery(params.btn).removeAttr('disabled');
		if(params.btnIconElement) {
			params.btnIconElement.attr('class', params.btnIconElement.data('prev-class'));
		}
	}
    /*if(sentFromForm) {
        jQuery(form).find('*').removeClass('cfsInputError');
    }*/
    if(typeof(res) == 'object') {
        if(res.error) {
            if(msgEl) {
                jQuery(msgEl)
					.removeClass('cfsSuccessMsg')
					.addClass('cfsErrorMsg');
            }
			var errorsArr = [];
            for(var name in res.errors) {
                if(sentFromForm) {
					var inputError = jQuery(form).find('[name*="'+ name+ '"]');
                    inputError.addClass('cfsInputError');
					if(inputError.attr('placeholder')) {
						//inputError.attr('placeholder', res.errors[ name ]);
					}
					if(!inputError.data('keyup-error-remove-binded')) {
						inputError.keydown(function(){
							jQuery(this).removeClass('cfsInputError');
						}).data('keyup-error-remove-binded', 1);
					}
                }
                if(jQuery('.cfsErrorForField.toe_'+ nameToClassId(name)+ '').exists())
                    jQuery('.cfsErrorForField.toe_'+ nameToClassId(name)+ '').show().html(res.errors[name]);
                else if(msgEl)
                    jQuery(msgEl).append(res.errors[name]).append('<br />');
				else
					errorsArr.push( res.errors[name] );
            }
			if(errorsArr.length && params.btn && jQuery.fn.dialog && !msgEl) {
				jQuery('<div title="'+ toeLangCfs("Really small warning :)")+ '" />').html( errorsArr.join('<br />') ).appendTo('body').dialog({
					modal: true
				,	width: '500px'
				});
			}
        } else if(res.messages.length) {
            if(msgEl) {
                jQuery(msgEl)
					.removeClass('cfsErrorMsg')
					.addClass('cfsSuccessMsg');
                for(var i = 0; i < res.messages.length; i++) {
                    jQuery(msgEl).append(res.messages[i]).append('<br />');
                }
            }
        }
    }
    if(params.onSuccess && typeof(params.onSuccess) == 'function') {
        params.onSuccess(res);
    }
}

function getDialogElementCfs() {
	return jQuery('<div/>').appendTo(jQuery('body'));
}

function toeOptionCfs(key) {
	if(CFS_DATA.options && CFS_DATA.options[ key ])
		return CFS_DATA.options[ key ];
	return false;
}
function toeLangCfs(key) {
	if(CFS_DATA.siteLang && CFS_DATA.siteLang[key])
		return CFS_DATA.siteLang[key];
	return key;
}
function toePagesCfs(key) {
	if(typeof(CFS_DATA) != 'undefined' && CFS_DATA[key])
		return CFS_DATA[key];
	return false;;
}
/**
 * This function will help us not to hide desc right now, but wait - maybe user will want to select some text or click on some link in it.
 */
function toeOptTimeoutHideDescriptionCfs() {
	jQuery('#cfsOptDescription').removeAttr('toeFixTip');
	setTimeout(function(){
		if(!jQuery('#cfsOptDescription').attr('toeFixTip'))
			toeOptHideDescriptionCfs();
	}, 500);
}
/**
 * Show description for options
 */
function toeOptShowDescriptionCfs(description, x, y, moveToLeft) {
    if(typeof(description) != 'undefined' && description != '') {
        if(!jQuery('#cfsOptDescription').length) {
            jQuery('body').append('<div id="cfsOptDescription"></div>');
        }
		if(moveToLeft)
			jQuery('#cfsOptDescription').css('right', jQuery(window).width() - (x - 10));	// Show it on left side of target
		else
			jQuery('#cfsOptDescription').css('left', x + 10);
        jQuery('#cfsOptDescription').css('top', y);
        jQuery('#cfsOptDescription').show(200);
        jQuery('#cfsOptDescription').html(description);
    }
}
/**
 * Hide description for options
 */
function toeOptHideDescriptionCfs() {
	jQuery('#cfsOptDescription').removeAttr('toeFixTip');
    jQuery('#cfsOptDescription').hide(200);
}
/**
 * Check if value is in array (object)
 * @param {type} needle Find for
 * @param {type} haystack Find where
 * @returns {Boolean} true if value found, else - false
 */
function toeInArrayCfs(needle, haystack) {
	if(haystack) {
		for(var i in haystack) {
			if(haystack[i] == needle)
				return true;
		}
	}
	return false;
}
function toeShowDialogCustomized(element, options) {
	options = jQuery.extend({
		resizable: false
	,	width: 500
	,	height: 300
	,	closeOnEscape: true
	,	open: function(event, ui) {
			jQuery('.ui-dialog-titlebar').css({
				'background-color': '#222222'
			,	'background-image': 'none'
			,	'border': 'none'
			,	'margin': '0'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'color': '#CFCFCF'
			,	'height': '27px'
			});
			jQuery('.ui-dialog-titlebar-close').css({
				'background': 'url("'+ CFS_DATA.cssPath+ 'img/tb-close.png") no-repeat scroll 0 0 transparent'
			,	'border': '0'
			,	'width': '15px'
			,	'height': '15px'
			,	'padding': '0'
			,	'border-radius': '0'
			,	'margin': '7px 7px 0'
			}).html('');
			jQuery('.ui-dialog').css({
				'border-radius': '3px'
			,	'background-color': '#FFFFFF'
			,	'background-image': 'none'
			,	'padding': '1px'
			,	'z-index': '300000'
			,	'position': 'fixed'
			,	'top': '60px'
			});
			jQuery('.ui-dialog-buttonpane').css({
				'background-color': '#FFFFFF'
			});
			jQuery('.ui-dialog-title').css({
				'color': '#CFCFCF'
			,	'font': '12px sans-serif'
			,	'padding': '6px 10px 0'
			});
			if(options.openCallback && typeof(options.openCallback) == 'function') {
				options.openCallback(event, ui);
			}
			jQuery('.ui-widget-overlay').css({
				'z-index': jQuery( event.target ).parents('.ui-dialog:first').css('z-index') - 1
			,	'background-image': 'none'
			});
			if(options.modal && options.closeOnBg) {
				jQuery('.ui-widget-overlay').unbind('click').bind('click', function() {
					jQuery( element ).dialog('close');
				});
			}
		}
	}, options);
	return jQuery(element).dialog(options);
}
/**
 * @see html::slider();
 **/
function toeSliderMove(event, ui) {
    var id = jQuery(event.target).attr('id');
    jQuery('#toeSliderDisplay_'+ id).html( ui.value );
    jQuery('#toeSliderInput_'+ id).val( ui.value ).change();
}
function cfsCorrectJqueryUsed() {
	return (typeof(jQuery.fn.sendFormCfs) === 'function');
}
function cfsReloadCoreJs(clb, params) {
	var scriptsHtml = ''
	,	coreScripts = ['common.js', 'core.js'];
	for(var i = 0; i < coreScripts.length; i++) {
		scriptsHtml += '<script type="text/javascript" class="cfsReloadedScript" src="'+ CFS_DATA.jsPath+ coreScripts[ i ]+ '"></script>';
	}
	jQuery('head').append( scriptsHtml );
	if(clb) {
		_cfsRunClbAfterCoreReload( clb, params );
	}
}
function _cfsRunClbAfterCoreReload(clb, params) {
	if(cfsCorrectJqueryUsed()) {
		callUserFuncArray(clb, params);
		return;
	}
	setTimeout(function(){
		cfsCorrectJqueryUsed(clb, params);
	}, 500);
}
function base64_decode(input) {
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
     var base64test = /[^A-Za-z0-9\+\/\=]/g;
     if (base64test.exec(input)) {
        alert("There were invalid base64 characters in the input text.\n" +
              "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
              "Expect errors in decoding.");
     }
     input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

     do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
           output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
           output = output + String.fromCharCode(chr3);
        }

        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

     } while (i < input.length);

     return unescape(output);
  }
