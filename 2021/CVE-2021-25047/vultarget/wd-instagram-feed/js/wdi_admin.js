jQuery(document).ready(function() {
	/* @ToDo It must be separate for each user */
  jQuery("#wdi_reset_cache").click(function (e) {
    jQuery(".wdi_reset_cache_success").remove();
    jQuery("#wdi_save_loading").removeClass("wdi_hidden");
    e.preventDefault();
    jQuery.ajax({
      type: "POST",
      url: wdi_ajax.ajax_url,
      dataType:"json",
      data: {
        wdi_nonce:wdi_ajax.wdi_nonce,
        task:"reset",
        action:"wdi_cache"
      },
      success: function(result){
        if(result.success === false){
          jQuery("#wdi_reset_cache").after("<span class='wdi_reset_cache_success' style='color: #fc0000; margin-left: 15px; line-height: 2;'>Failed</span>");
        } else{

					wdi_controller.instagram = new WDIInstagram();

					jQuery.each( result['data'], function( key, value ) {

						var users = JSON.parse(value['users']);
						var username = '';
						var tagname = '';
						var tag_id = '';
						var endpoint = value['endpoint'];

						jQuery.each( users, function( key1, value1 ) {
							/* Hashtag case */
							if( value1['tag_id'] !== "" ) {
									tagname = value1['username'];
									tagname = tagname.substr(1, tagname.length);
									tagname = tagname.replace(' ', '');
									tag_id = value1['tag_id'];
							} else {
									username = wdi_controller.getUserObj(value1['username']);
									if (username) {
										username = username['user_name'];
									}
							}
						});
						var feed_id = value['feed_id'];
						wdi_controller.instagram.set_cache_data( '', username, feed_id, '', 0, 0, tagname, tag_id, endpoint, '' );
					});
					jQuery("#wdi_reset_cache").after("<span class='wdi_reset_cache_success' style='color: #029117; margin-left: 15px; line-height: 2;'>Success</span>");
        }
      }
    });
  });
	jQuery(".wdi_account_refresh").click(function () {
		var __this = jQuery(this);
		var wdi_user_name = __this.data("wdi_account");
		jQuery.ajax({
			type: 'POST',
			url: wdi_ajax.ajax_url,
			dataType: 'json',
			data: {
				page: 'wdi_settings',
				action: 'wdi_account_refresh',
				nonce: wdi_ajax.wdi_nonce,
				user_name: wdi_user_name
			},
			success: function ( data ) {
				var wdi_reset_cache_success = __this.closest("div").find(".wdi_reset_cache_success");
				wdi_reset_cache_success.remove();
				if ( data.success === true ) {
					__this.after("<span class='wdi_reset_cache_success' style='color: #029117; margin-left: 15px; line-height: 2;'>Success</span>");
					var wdi_token_filed = __this.closest("div").find(".wdi_user_token");
					wdi_token_filed.val(data.token);
				}
				else {
					__this.after("<span class='wdi_reset_cache_success' style='color: #fc0000; margin-left: 15px; line-height: 2;'>Failed</span>");
				}
			}
		});
	});

	/*Feeds page*/
	wdi_controller.bindSaveFeedEvent();
	wdi_controller.bindAddNewUserOrHashtagEvent();
	jQuery('.display_type input').on('click', function() {
		wdi_controller.displaySettingsSection(jQuery(this));
	});
	/*-----------Conditional Filters-----------*/
	wdi_controller.conditionalFiltersTabInit();
	/*Themes page*/
	wdi_controller.bindSaveThemeEvent();

	if(jQuery('body').hasClass('instagram-feed_page_wdi_settings')){
        //wdi_multiple_accounts_option_controller();
        wdi_advanced_option_controller();
	}

  jQuery(".wdi_section_name").click(function () {
    wdi_show_hide_sections(jQuery(this));
  });
    wdi_show_hide_sections(false);

    function wdi_show_hide_sections(element) {
        if (element === false) {
            var wdi_hide_show_sections_list = {
                'wdi_layout_section': 'show',
                'wdi_media': 'show',
                'wdi_layout': 'hide',
                'wdi_advanced': 'hide',

                'wdi_lightbox_general': 'show',
                'wdi_lightbox_advanced': 'hide',

                'wdi_conditional_filters': 'show',
                'wdi_how_to_publish': 'show',
            };
            var wdi_sections = JSON.parse(localStorage.getItem('wdi_sections'));
            if (wdi_sections === null || wdi_sections === false) {
                wdi_sections = wdi_hide_show_sections_list;
                localStorage.setItem('wdi_sections', JSON.stringify(wdi_sections));
            }
            for (i in wdi_sections) {
                var wdi_section_el = jQuery("#wdi_save_feed").find("[data-section_name='" + i + "']");
                wdi_show_hide(wdi_sections[i], wdi_section_el);
            }
        }
        else {
            var wdi_section_parent_id = jQuery(element).data("section_name");
            var show_hide_section = null;
            if (element.hasClass("wdi_section_open")) {
                wdi_show_hide("show", element);
                show_hide_section = "show";
            } else if (element.hasClass("wdi_section_close")) {
                wdi_show_hide("hide", element);
                show_hide_section = "hide";
            }
            if (show_hide_section != null) {
                var old_wdi_sections = JSON.parse(localStorage.getItem('wdi_sections')) || {};
                old_wdi_sections[wdi_section_parent_id] = show_hide_section;
                localStorage.setItem('wdi_sections', JSON.stringify(old_wdi_sections));
            }
        }
    }

	function wdi_show_hide(type, element) {
		if (type === "show") {
			var wdi_closable_section = element.closest(".wdi_section").find(".wdi_elements");
			if (wdi_closable_section.data("display") === "table") {
					wdi_closable_section.css({
							"display": "table"
					});
			} else {
					wdi_closable_section.css({
							"display": "block"
					});
			}

			element.css({
					'border-bottom': "1px solid #f1f1f1",
					'margin': '0 auto 15px'
			});
			element.removeClass("wdi_section_open");
			element.addClass("wdi_section_close");
		} else {
			element.closest(".wdi_section").find(".wdi_elements").css({
					'display': 'none'
			});
			element.css({
					'border-bottom': "0px",
					'margin': '0 auto 0px'
			});
			element.removeClass("wdi_section_close");
			element.addClass("wdi_section_open");
		}
	}

	jQuery(document).on('click', '.wdi-account-show-token', function() {
		jQuery(this).find('i').toggleClass('dashicons-arrow-up-alt2 dashicons-arrow-down-alt2');
		jQuery(this).parents('li').find('.wdi-account-accesstoken' ).slideToggle(300);
	});

	jQuery(document).on('click', '.wdi-advanced-options .wdi-advanced-headline', function() {
		jQuery(this).find('i').toggleClass('dashicons-arrow-up-alt2 dashicons-arrow-down-alt2');
		jQuery(this).parents('.wdi-advanced-options').find('.wdi-advanced-body' ).slideToggle(500);
	});

	jQuery(document).on('click', '#wdi_verify', function () {
		jQuery('#wdi_submit').prop('disabled', true);
		if ( jQuery(this).is(":checked") ) {
			jQuery('#wdi_submit').prop('disabled', false);
		}
	});

	jQuery(document).on('click', '#wdi_submit', function () {
		if ( confirm(wdi_messages.uninstall_plugin) ) {
			jQuery('#wdi_uninstall_form').submit();
		}
		return false;
	});

	jQuery( '#WDI_feed_name' ).on( "keypress", function () {
		jQuery( this ).removeAttr( "style" );
	} );
});

function wdi_multiple_accounts_option_controller() {
	var $table = jQuery(jQuery('#wdi_user_id').closest('form').find('.form-table').get(1));
	$table.addClass('wdi_multiple_accounts_section');
	var html = '';
	if( wdi_options.wdi_authenticated_users_list ) {
		var users_list = JSON.parse(wdi_options.wdi_authenticated_users_list);
		var index = 0;
		for (var i in users_list) {
			html += "<tr data-multiple-account='" + index + "'>";
			html += "<th>Access Token</th>";
			html += '<td><input type="text" name="wdi_instagram_options[wdi_authenticated_users_list][access_token][]" size="53" required="" value="' + users_list[i].access_token + '"></td>';
			html += "</tr>";
			html += "<tr data-multiple-account='" + index + "' class='wdi_username_tr'>";
			html += "<th>Username</th>";
			html += '<td>' +
				'<div class="wdi_input_wrapper"><input type="text" name="wdi_instagram_options[wdi_authenticated_users_list][user_name][]" size="53" required="" value="' + users_list[i].user_name + '"></div>' +
				'<div class="wdi_remove_auth_user">Delete</div>' +
				'</td>';
			html += '<input type="hidden" name="wdi_instagram_options[wdi_authenticated_users_list][user_id][]" size="53" required="" value="' + users_list[i].user_id + '">';
			html += "</tr>";
			index++;
		}
		$table.append(html);
	}

	jQuery('.wdi_remove_auth_user').on('click', function (e) {
		e.preventDefault();
		var data = jQuery(this).closest('.wdi_username_tr').data('multiple-account');
		jQuery(this).closest('.wdi_multiple_accounts_section').find('tr[data-multiple-account="' + data + '"]').remove();
		return false;
	});
}

function wdi_advanced_option_controller() {
	var $table = jQuery(jQuery('#wdi_user_id').closest('form').find('.form-table').get(1));
	$table.addClass('wdi_advanced_option wdi_advanced_option_close');
	var tr = "<tr class='wdi_advanced_option_head'><th style='width: 100%;'>ADVANCED OPTIONS AND MULTIPLE INSTAGRAM ACCOUNTS</th><td><div class='wdi_advanced_option_icon'></div></td></tr>";
	$tr = jQuery(tr);
	$table.prepend($tr);

	$tr.on('click', function () {
		if ($table.hasClass('wdi_advanced_option_open')) {
			$table.removeClass('wdi_advanced_option_open');
			$table.addClass('wdi_advanced_option_close');
		} else {
			$table.removeClass('wdi_advanced_option_close');
			$table.addClass('wdi_advanced_option_open');
		}
	});
}

function wdi_controller() {};

/**
 * Gets query parameter by name
 * @param  {String} name [parameter name]
 * @return {String}      [parameter value]
 */
wdi_controller.getParameterByName = function(name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
		results = regex.exec(location.search);
	return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

/**
 * Was tirggerd when redirected from api page 
 * Sets access token from query string to input
 */
wdi_controller.apiRedirected = function() {
	var access_token_raw = this.getParameterByName('access_token');
	var arr = access_token_raw.split('.');
	var validRegEx = /^[^\\\/&?><"']*$/;
	for (i = 0; i < arr.length; i++) {
		if (arr[i].match(validRegEx) === null) {
			return;
		}
	}
	var access_token = arr.join('.');
	jQuery(document).ready(function() {
        if (wdi_options.wdi_access_token === "") {
            jQuery('#wdi_access_token').attr('value', access_token);
        } else {
            jQuery('.wdi_more_token_template .wdi_more_access_token').prop('disabled', false);
            jQuery('.wdi_more_token_template .wdi_more_access_token').attr('value', access_token);
        }
	});

	//if access token is getted then overwrite it
	wdi_controller.instagram.addToken(access_token);
	wdi_controller.getUserInfo(access_token);
}

/**
 * Used in Settings page for finding access token owners username
 * and and for filling it in username input field
 * 
 * @param  {String} access_token [Instagram API access token]
 */
wdi_controller.getUserInfo = function (access_token) {
	this.instagram.getSelfInfo({
		success: function (response) {
			if (wdi_options.wdi_access_token === "") {
				jQuery('#wdi_user_name').attr('value', response['data']['username']);
				jQuery('#wdi_user_id').attr('value', response['data']['id']);
			}
			else {
				jQuery('.wdi_more_token_template .wdi_more_user_name').prop('disabled', false);
				jQuery('.wdi_more_token_template .wdi_more_user_id').prop('disabled', false);
				jQuery('.wdi_more_token_template .wdi_more_user_name').attr('value', response['data']['username']);
				jQuery('.wdi_more_token_template .wdi_more_user_id').attr('value', response['data']['id']);
			}
			jQuery(document).trigger('wdi_settings_filled');
		}
	})
}

wdi_controller.oldDisplayType = {};
wdi_controller.displayTypeMemory = {};

/*
 * Switches between feeds admin page tabs
 */
wdi_controller.switchFeedTabs = function ( tabname, section ) {
	//add tabname in hidden field
	jQuery('#wdi_refresh_tab').attr('value', tabname);
	//hiding options of other tabs
	jQuery('.wdi_tab').hide();
	jQuery('#' + tabname + '_tab').show();
	jQuery('.wdi_element_name_popup_enable_comment').hide();
	var type = jQuery('#WDI_user_name option:selected').data('type');
	if ( type === 'business' ) {
		jQuery('.wdi_element_name_popup_enable_comment').show();
	}

	//hiding all display_type elements
	jQuery('.display_type').css('display', 'none');
	//showing only requested display_type tab elements
	jQuery('.display_type[tab="' + tabname + '"]').css('display', 'block');
	if ( !jQuery('.display_type[tab="' + tabname + '"]').length ) {
		jQuery('.display_type_content').hide();
	}
	else {
		jQuery('.display_type_content').show();
	}
	//swap active tab class
	jQuery('.wdi_feed_tabs').filter('.wdi_feed_tab_active').each(function () {
		jQuery(this).removeClass('wdi_feed_tab_active');
	});

	jQuery('#wdi_' + tabname).addClass('wdi_feed_tab_active');
	var selectedSection = jQuery();
	var sectionSelectedFLag = false;
	if ( section != undefined && section != '' ) {
		//check value which came from backend
		selectedSection = jQuery('.display_type #' + section).prop('checked', true);
		jQuery('#wdi_feed_type').attr('value', section);
		//sectionSelectedFLag = true;
	}

	//find the selected feed_type option
	if ( !sectionSelectedFLag ) {
		selectedSection = jQuery('.display_type[tab="' + tabname + '"] input[name="feed_type"]:checked');
		if ( selectedSection.length != 0 ) {
			sectionSelectedFLag = true;
		}
	}

	//if there are no selected feed_type option then set default option
	if ( !sectionSelectedFLag ) {
		//make default section as selected
		selectedSection = jQuery('.display_type[tab="' + tabname + '"] #thumbnails');
		if ( selectedSection.length != 0 ) {
			sectionSelectedFLag = true;
			selectedSection.prop('checked', true);
			jQuery('#wdi_feed_type').attr('value', 'thumbnails');
		}
	}
	//if under currect tab we have feed_type section then show it
	if ( sectionSelectedFLag ) {
		wdi_controller.displaySettingsSection(selectedSection);
	}

	// @ToDo free There is a difference in free!
	// if tabname is conditional filters then call tab interface updater
	if ( tabname == 'conditional_filters' ) {
		wdi_controller.updateConditionalFiltersUi();
	}
}

/*
 * Displays Settings Section for admin pages
 */
wdi_controller.displaySettingsSection = function($this) {
	var sectionName = $this.attr('id').toLowerCase().trim();
	var tab = $this.parent().parent().attr('tab');
	var sectionHiddenField = jQuery('#wdi_refresh_section');
	wdi_controller.oldDisplayType = {
		'section': sectionName,
		'tab': tab
	};
	wdi_controller.displayTypeMemory[tab] = wdi_controller.oldDisplayType;
	//works only in theme page, because only theme page has #wdi_refresh_section hidden field
	if (sectionHiddenField != undefined) {
		sectionHiddenField.attr('value', sectionName);
	}

	var formTable = jQuery('.wdi_border_wrapper .form-table');
	jQuery('#wdi_feed_type').attr('value', sectionName);
	var i = 0,
		j = 0;
	var sectionFlag = false;
	formTable.find('.wdi_element').each(function() {
		i++;
		var sectionStr = jQuery(this).find(".wdwt_param").children().children().children().attr('section');
		if (sectionStr !== undefined) {
			sectionFlag = false;
			var sections = sectionStr.toLowerCase().trim().split(',');
			for (j = 0; j < sections.length; j++) {
				if (sections[j] === sectionName) {
					jQuery(this).css('display', 'block');
					sectionFlag = true;
				}
			}
			if (sectionFlag === false) {
				jQuery(this).css('display', 'none');
			}
		}
	});
}

/*
 * Switches between themes admin page tabs
 */
wdi_controller.switchThemeTabs = function(tabname, section) {
	//swap active tab class
	jQuery('.wdi_feed_tabs').filter('.wdi_feed_tab_active').each(function() {
		jQuery(this).removeClass('wdi_feed_tab_active');
	});
	jQuery('#wdi_' + tabname).addClass('wdi_feed_tab_active');

	//hiding options of other tabs
	jQuery('[tab]').each(function() {
		if (jQuery(this).attr('tab') != tabname) {
			jQuery(this).parent().parent().parent().parent().parent().filter('tr').css('display', 'none');
		} else {
			jQuery(this).parent().parent().parent().parent().parent().filter('tr').css('display', 'block');
		}
	});

	//hiding all display_type elements
	jQuery('.display_type').css('display', 'none');
	//showing only requested display_type tab elements
	jQuery('.display_type[tab="' + tabname + '"]').css('display', 'block');

	//add tabname in hidden field
	jQuery('#wdi_refresh_tab').attr('value', tabname);
	//add sectionname in hidden field
	if (section != undefined && section != '') {
		jQuery('#wdi_refresh_section').attr('value', section);
	}

	//check if any section was previously clicked then set to that section
	if (section == undefined && section != '') {
		if (wdi_controller.displayTypeMemory[tabname] != undefined) {
			jQuery('.display_type #' + wdi_controller.displayTypeMemory[tabname]['section']).trigger('click');
		} else {
			//default section
			jQuery('.display_type[tab="' + tabname + '"]').first().find('input').trigger('click');
		}
	} else {
		jQuery('.display_type #' + section).trigger('click');
	}
}

/**
 * Binds events to control buttons
 */
wdi_controller.bindSaveFeedEvent = function() {
	var _this = this;

	jQuery('#wdi_save_feed_submit').on('click', function() {
		_this.save_feed('save_feed')
	});
	jQuery('#wdi_save_feed_apply').on('click', function() {
		_this.save_feed('apply_changes')
	});

	jQuery('#wdi_cancel_changes').on('click', function() {
		_this.save_feed('cancel')
	});
}

/**
 * Submits form baset on given task
 * if task is cancel then it reloades the page
 * @param  {String} task [this is self explanatory]
 */
wdi_controller.save_feed = function ( task ) {
	var feed_users = [],
		id,
		type,
		username,
		feed_title,
		default_user = {},
		json_feed_users = {};
	if ( 'cancel' == task ) {
		window.location = window.location.href;
	}
	type = jQuery('#WDI_user_name option:selected').data('type');
	username = jQuery('#WDI_user_name option:selected').val();
	feed_title = jQuery('#WDI_feed_name');
	var userObj = wdi_controller.getUserObj(username);
	if (feed_title.val() == '') {
		alert(wdi_messages.feed_title_field_required);
		feed_title.focus().attr( 'style', 'border-color: #FF0000;' );
		return false;
	}
	if ( !userObj ) {
		alert(wdi_messages.user_field_required);
		return false;
	}
	default_user = {
		id: userObj.user_id,
		username: userObj.user_name
	}
	// check if user input field is not empty then cancel save process and make an ajax request
	// add user in input field and then after it trigger save,apply or whatever
	wdi_controller.checkIfUserNotSaved(task);
	if ( wdi_controller.waitingAjaxRequestEnd.button != 0 ) {
		return;
	}
	json_feed_users = jQuery('#WDI_feed_users').val();
	if ( this.isJsonString(json_feed_users) ) {
		json_feed_users = JSON.parse(json_feed_users);
		for ( var i in json_feed_users ) {
			if ( type == 'business' && json_feed_users[i].username.charAt(0) === '#' ) {
				feed_users.push(json_feed_users[i]);
			}
		}
		feed_users.push(default_user);
	}
	else {
		feed_users.push(default_user);
	}
	users = this.stringifyUserData(feed_users);
	jQuery('#WDI_feed_users').val(users);
	jQuery('#wdi_feed_thumb').val('');
	if ( type == 'business' ) {
		jQuery('#wdi_feed_thumb').val(userObj.profile_picture_url);
	}
	if ( task == 'apply_changes' || task == 'save_feed' ) {
		id = jQuery('#wdi_add_or_edit').val();
		jQuery('#wdi_current_id').val(id);
	}
	jQuery('#task').attr('value', task);
	submit_ajax();
}

var comlete_redirect_url = '';

function submit_ajax() {
	var data = jQuery("#wdi_save_feed").serialize();
			data = data + '&action=wdi_apply_changes&page=wdi_feeds&wdi_nonce='+wdi_ajax.wdi_nonce;
	jQuery("#wdi_save_loading").removeClass("wdi_hidden");
	jQuery.ajax({
		url: wdi_ajax.ajax_url,
		type: 'POST',
		dataType: 'json',
		data: data,
		success: function (response) {
			/* comlete_redirect_url url is redirect url which will be done after cash ajax complete */
			comlete_redirect_url = response['url'];
			var feed_id = response['feed_id'];
			if( response['need_cache'] == 1 ) {
				jQuery(".caching-process-message").removeClass("wdi_hidden");
				/* TODO Timeout need as loader not appear without timeout, need to fix */
				setTimeout(function(){
					 wdi_controller.instagram.set_cache_data(comlete_redirect_url, '', feed_id, '', 0, 0, '', '', '', '');
				}, 1000);
			}
			else {
				jQuery("#wdi_save_loading").addClass("wdi_hidden");
				window.location = comlete_redirect_url;
			}
		},
		error: function (xhr, status, error) {
			window.location = comlete_redirect_url;
		}
	});
}

/**
 * Takes user input as argument and makes an
 * instagram request for getting meta info such as username and user id
 * stores getted data in wdi_controller.feed_users array and updates some admin elements which
 * depend on users
 * 
 * @param  {String} tag_name hashtag, [Note. hashtags should start with #]
 * @param {String} backend [if is set to 'backend' all confirms will be ignored while making requests]
 * @return {Void}  
 */
wdi_controller.makeInstagramUserRequest = function ( user_input, ignoreConfirm ) {
	var _this = this,
		input_type = this.getInputType(user_input),
		username,
		userObj;
	var is_hashtag = input_type === 'hashtag';
	var feed_id = jQuery("#wdi_add_or_edit").val();
	if ( user_input == '' ) {
		alert(wdi_messages.please_write_hashtag);
		return false;
	}
	if ( user_input != '' && !is_hashtag ) {
		alert(wdi_messages.invalid_hashtag);
		return false;
	}
	if ( _this.checkForDuplicateUser(user_input) ) {
		alert(user_input + ' ' + wdi_messages.already_added);
		return false;
	}
	username = jQuery('#WDI_user_name option:selected').val();
	userObj = wdi_controller.getUserObj(username);
	if ( userObj ) {
		this.instagram.user = userObj;
	}
	switch ( input_type ) {
		case 'user': {
			break;
		}
		case 'hashtag': {
			var tagname = user_input.substr(1, user_input.length);
			tagname = tagname.replace(' ', '');
			var radio = jQuery("input[name='wdi_feed_settings[hashtag_top_recent]']:checked").val();
			var data = {
						tagname : tagname,
						action : 'wdi_getHashtagId',
						wdi_nonce: wdi_ajax.wdi_nonce,
						user_name: username,
						feed_id: feed_id
					};
			jQuery.ajax({
				type: "POST",
				url: wdi_ajax.ajax_url,
				dataType: 'json',
				data: data,
				success: function (response) {
					jQuery('#wdi_add_user_ajax').removeAttr('disabled');
					var vObj = _this.isValidResponse(response);
					if ( vObj.valid ) {
						_this.addHashtag(tagname, response);
					}
					else {
						alert( vObj.msg )
					}
				},
				error: function (xhr, status, error) {
				}
			});

			break;
		}
	}
}

/**
 * Scans wdi_controller.feed_users array and if duplicate matched then returns false else true
 * @param  {String} username [name of user we want to check]
 * @return {Boolean}
 */
wdi_controller.checkForDuplicateUser = function(username) {
	var feed_users = jQuery('#WDI_feed_users').val();
	if ( this.isJsonString(feed_users) ) {
		feed_users = JSON.parse(feed_users);
		for (var i = 0; i < feed_users.length; i++) {
			if (username == feed_users[i]['username']) {
				return true;
			}
		}
	}
	return false;
}

wdi_controller.getInputType = function (input) {
	switch (input[0]) {
		case '#': {
			return 'hashtag';
			break;
		}
		case '%': {
			return 'location';
			break;
		}
		default: {
			return 'user';
			break;
		}
	}
}

/**
 * Makes username and id pairs from users array and return json_encoded string
 * @param  {Array} feed_users [array of feed_users containing username and id and other parameters]
 * @return {String}           [JSON encoded data]
 */
wdi_controller.stringifyUserData = function (feed_users) {
	var users = [];
	for ( var i = 0; i < feed_users.length; i++ ) {
		users.push({
			id: feed_users[i]['id'],
			username: feed_users[i]['username'],
			tag_id: (typeof feed_users[i]['tag_id'] !== 'undefined') ? feed_users[i]['tag_id'] : ''
		})
	}

	return JSON.stringify(users);
}

/**
 * Binds 'click' and 'enter' event to add user button
 * 
 */
wdi_controller.bindAddNewUserOrHashtagEvent = function () {
	jQuery('#wdi_add_user_ajax').on('click', function () {
		// ToDo. what is this for.
		if ( typeof jQuery(this).prop('disabled') !== 'undefined' ) {
			 //return;
		}
		else {
			jQuery(this).prop('disabled', true);
		}
		var tag_name = jQuery('#wdi_add_user_ajax_input').val().trim().toLowerCase();
		if ( wdi_controller.makeInstagramUserRequest(tag_name) === false ) {
			jQuery(this).removeAttr('disabled', 'disabled');
		}
	});
	jQuery('#wdi_add_user_ajax_input').on('keypress', function ( e ) {
		// ToDo. what is this for.
		if ( e.keyCode == 13 ) {
			if ( typeof jQuery('#wdi_add_user_ajax').prop('disabled') !== 'undefined' ) {
				 //return;
			}
			else {
				jQuery('#wdi_add_user_ajax').prop('disabled', true);
			}
			var tag_name = jQuery('#wdi_add_user_ajax_input').val().trim().toLowerCase();
			if ( wdi_controller.makeInstagramUserRequest(tag_name) === false ) {
				jQuery('#wdi_add_user_ajax').removeAttr('disabled', 'disabled');
			}
			return false; // prevent the button click from happening
		}
	});
}

/**
 * Removes users from internal wdi_controller.feed_users array and also
 * updates GUI (by removing user elements)
 * 
 * @param  {Object} $this [jQuery object of remove user button]
 */
wdi_controller.removeFeedUser = function ( $this ) {
	var username = $this.parent().find('a span').text();
	if ( $this.parent().find('a span').hasClass('wdi_hashtag') ) {
		username = '#' + username;
	}
	if ( this.feed_users.length == 0 ) {
		var feed_users = jQuery('#WDI_feed_users').val();
		if ( typeof feed_users !== 'undefined' && this.isJsonString(feed_users) ) {
			this.feed_users = JSON.parse(feed_users);
		}
	}
	for ( var i = 0; i < this.feed_users.length; i++ ) {
		var name = this.feed_users[i]['username'];
		if ( name == username ) {
			this.feed_users.splice(i, 1);
			break;
		}
	}
	for ( var i = 0; i < this.feed_users.length; i++ ) {
		var name = this.feed_users[i]['username'];
		if ( name.charAt(0) !== '#' ) {
			this.feed_users.splice(i, 1);
		}
	}
	jQuery('#WDI_feed_users').val(this.stringifyUserData(this.feed_users));
	$this.parent().remove();
	wdi_controller.changed_users();
}

wdi_controller.bindSaveThemeEvent = function() {
	jQuery('#wdi_save_theme_submit').on('click', function() {
		jQuery('#task').attr('value', 'save_feed');
		jQuery('#wdi_save_feed').submit();
	});
	jQuery('#wdi_save_theme_apply').on('click', function() {
		jQuery('#task').attr('value', 'apply_changes');
		var id = jQuery('#wdi_add_or_edit').attr('value');
		jQuery('#wdi_current_id').attr('value', id);
		jQuery('#wdi_save_feed').submit();
	});
	jQuery('#wdi_save_theme_reset').on('click', function() {
		jQuery('#task').attr('value', 'reset_changes');
		var id = jQuery('#wdi_add_or_edit').attr('value');
		jQuery('#wdi_current_id').attr('value', id);
		jQuery('#wdi_save_feed').submit();
	});
}

/**
 * This function is called when one of controll buttons are being clicked
 * it checks if user has typed any username in unsername input
 * but forgetted to add it then it creates an object called wdi_controller.waitingAjaxRequestEnd
 * which previous task
 * 
 * @param  {String} task [how to save element save/apply/reset]
 * @return {Boolean}     [1 if user forgotted to save and 0 if input field was empty]
 */
wdi_controller.checkIfUserNotSaved = function(task) {
	switch (task) {
		case 'save_feed':
			{
				task = 'submit';
				break;
			}
		case 'apply_changes':
			{
				task = "apply";
				break;
			}
		case 'reset_changes':
			{
				task = 'reset';
				break;
			}
	}

	// checking if user has typed username in input field but didn't saved it, trigger add action
	if ( jQuery('#wdi_add_user_ajax_input').val().trim() != '' ) {
		var user_input = jQuery('#wdi_add_user_ajax_input').val().trim().toLowerCase();
		wdi_controller.waitingAjaxRequestEnd = {
			button: task
		};
		// making request
		wdi_controller.makeInstagramUserRequest(user_input);
		return 1;
	}
	else {
		wdi_controller.waitingAjaxRequestEnd = {
			button: 0
		};
		return 0;
	}
}

/**
 * if user was clicked save before ajax request then trigger save after getting info
 * 
 * @param  {String} correctUserFlag [if set to false form wouldn't be submitted]
 */
wdi_controller.saveFeedAfterAjaxWait = function(correctUserFlag) {

	if (wdi_controller.waitingAjaxRequestEnd != undefined) {
		//if save button was clicked before ajax request then trigger save button
		var save_type_btn = wdi_controller.waitingAjaxRequestEnd.button;
		if (correctUserFlag && save_type_btn != 0) {
			jQuery('#wdi_save_feed_' + save_type_btn).trigger('click');
		}
		wdi_controller.waitingAjaxRequestEnd = undefined;
	}
}

/**
 * Gets cookie value by name
 * @param  {String} name [cookie name]
 * @return {String}      [cookie value]
 */
wdi_controller.getCookie = function(name) {
	var value = "; " + document.cookie;
	var parts = value.split("; " + name + "=");
	if (parts.length == 2) return parts.pop().split(";").shift();
}

/**
 * Checks if response has meta code other then 200 or if it has not any data in it
 * then returns false
 * @param  {Object}  response [Instagram API response]
 * @return {Boolean}      
 */
wdi_controller.isValidResponse = function (response) {
	var obj = {};
	if (typeof response == 'undefined' || typeof response['meta']['code'] == 'undefined' || response['meta']['code'] != 200) {
		obj.valid = false;
		if (typeof response == 'undefined') {
			obj.msg = wdi_messages.instagram_server_error;
		}
		else if (response['meta']['code'] !== 200) {
			obj.msg = response['meta']['message'];
		}
		else {
			obj.msg = '';
		}
	}
	else {
		obj.valid = true;
		obj.msg = 'success';
	}
	return obj;
}

/**
 * Return true if response has data object which is not empty
 * @param  {Onject}  response [instagram API response]
 * @return {Boolean}          [true or false]
 */
wdi_controller.hasData = function(response) {
	if (typeof response != 'undefined' && typeof response['data'] != 'undefined' && response['data'].length != 0 ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Return true if user is featured user
 * @param {String} [user] username we want to check
 * @return {Boolean} true or false
 */
wdi_controller.thumbUser = function(user) {
    return (this.feed_users.length > 0 && this.feed_users[0].username === user);
	//return (jQuery('#wdi_thumb_user').val() == user) ? true : false;
}

/**
 * finds user by username in instagram api request object
 * if user is found then returns user object otherwise returns false
 * 
 * @param  {String} username [username we are searching for]
 * @param  {Object} response [instagram API response]
 * @return {Boolenan || Object}     
 */
wdi_controller.findUser = function(username, response) {
	var data = [];
	if (typeof response != 'undefined' && typeof response['data'] != 'undefined') {
		data = response['data'];
	}

	for (var i = 0; i < data.length; i++) {
		if (data[i]['username'] == username) {
			return data[i];
		}
	}
	return false;
}

/**
 * Sanitizes hashtag and if it's ok then add it to internal wdi_controller.feed_users array
 * besodes that it also updates GUI
 * 
 * @param {String} tagname  [name of hashtag to add without '#']
 * @param {Object} response [instagram API response]
 */
wdi_controller.addHashtag = function(tagname, response) {
	// if tagname doesn't contain invalid characters
	if (tagname.match(/[~!@$%&*#^()<>?]/) == null) {
			var newHashtag = jQuery('<div class="wdi_user"><a target="_blank" href="https://instagram.com/explore/tags/' + tagname + '">' + '<img class="wdi_profile_pic" src="' + wdi_url.plugin_url + 'images/hashtag.png"><span class="wdi_hashtag">' + tagname + '</span><i style="display:table-cell;width:25px;"></i></a><img class="wdi_remove_user" onclick="wdi_controller.removeFeedUser(jQuery(this))" src="' + wdi_url.plugin_url + '/images/delete_user.png"></div>');
			jQuery('#wdi_feed_users_ajax').append(newHashtag);
			jQuery('#wdi_add_user_ajax_input').val('');
			var profile_picture = '';
/*
			if (typeof response != 'undefined') {
				// ToDo. check all type
				profile_picture = (response['data'].length != 0 && typeof response['data'][0]['images']['thumbnail'] !== "undefined") ? response['data'][0]['images']['thumbnail']['url'] : '';
			}
*/
			var feed_users = jQuery('#WDI_feed_users').val();
			if ( this.isJsonString(feed_users) ) {
				feed_users = JSON.parse(feed_users);
				for ( var i = 0; i < feed_users.length; i++ ) {
					var name = feed_users[i]['username'];
					if ( name.charAt(0) !== '#' ) {
						feed_users.splice(i, 1);
					}
				}
			}
			else {
				feed_users = [];
			}
			var tag_obj = {
				id: '#' + tagname,
				username: '#' + tagname,
				profile_picture: profile_picture,
				tag_id: response.tag_id
			};
			feed_users.push(tag_obj);
			this.feed_users = feed_users;
			jQuery('#WDI_feed_users').val(this.stringifyUserData(this.feed_users));
	}
	else {
		alert(wdi_messages.invalid_hashtag);
	}

	this.updateConditionalFiltersUi();
	wdi_controller.saveFeedAfterAjaxWait(true);
	wdi_controller.changed_users();
};

wdi_controller.changed_users = function () {
	var has_hashtag = false;
	for ( var i = 0; i < this.feed_users.length; i++ ) {
		if ( this.feed_users[i].username[0] === '#' ) {
			has_hashtag = true;
			break;
		}
	}
	if ( has_hashtag ) {
		jQuery('.wdi_element_name_hashtag_top_recent').show();
		jQuery('.wdi_element_name_show_username_on_thumb').hide();
	}
	else {
		jQuery('.wdi_element_name_hashtag_top_recent').hide();
		jQuery('.wdi_element_name_show_username_on_thumb').show();
	}
};

/**
 * @ToDo This function is no longer used.
 * Adds given user to internal array wdi_controller.feed_users and also updates GUI
 * 
 * @param {Object} user [Object conatining user information such as id, username and profile picture]
 */
wdi_controller.addUser = function(user) {
	user.username = user.user_name;
	if ( this.checkForDuplicateUser(user.username) == false ) {
		newUser = jQuery('<div class="wdi_user"><a target="_blank" href="http://www.instagram.com/' + user.username + '"><span class="wdi_username">' + user.username + '</span><i style="display:table-cell;width:25px;"></i></a><img class="wdi_remove_user" onclick="wdi_controller.removeFeedUser(jQuery(this))" src="' + wdi_url.plugin_url + 'images/delete_user.png"></div>');
		jQuery('#wdi_feed_users_ajax').append(newUser);
		jQuery('#wdi_add_user_ajax_input').val('');

		this.feed_users.push({
			id: user.id,
			username: user.username,
			profile_picture: '' // user['profile_picture']
		});

	} else {
		alert(user.username + ' ' + wdi_messages.already_added);
	}
	this.updateConditionalFiltersUi();
	wdi_controller.saveFeedAfterAjaxWait(true);
}

/**
 * Scans internal wdi_controller.feed_users array and return profile picture url of given user
 * if there is no profile picture then returns blank string
 * 
 * @param  {String} username 
 * @return {String}    ['profile picture url of user']
 */
wdi_controller.getUserProfilePic = function(username) {
	for (var i = 0; i < this.feed_users.length; i++) {
		if (username == this.feed_users[i]['username']) {
			return this.feed_users[i]['profile_picture'];
		}
	}
	return 'false';
}

/*-------------------------------------------------------------
----------------Conditional Filters Tab Methods----------------
-------------------------------------------------------------*/

/**
 * Initiailizes conditional filter tabs with variables and methods
 */
wdi_controller.conditionalFiltersTabInit = function() {
	//get data from textarea and display it
	this.setInitialFilters();
	this.updateFiltersUi();

	var _this = this;
	jQuery('#wdi_add_filter').on('click', function() {
		var flag = _this.addConditionalFilter();
		if( flag ){
			jQuery('#wdi_filter_input').val('');
		}
	})


	jQuery('.wdi_filter_radio').on('click', function() {
		jQuery('#wdi_filter_input').trigger('focus');
	});

	jQuery('#wdi_filter_input').on('keypress', function(e) {
		if (e.keyCode == 13) {
			var flag = _this.addConditionalFilter();
			if( flag ){
				jQuery(this).val('');
			}
			return false; // prevent the button click from happening
		}
	});


	conditional_filters_toggler();
	jQuery('#WDI_wrap_conditional_filter_enable input').on('change', function() {
		conditional_filters_toggler();
	})

	function conditional_filters_toggler() {
		switch (jQuery('#WDI_wrap_conditional_filter_enable input:checked').val()) {
			case '0':
				{
					jQuery('#WDI_conditional_filters').parent().parent().addClass('wdi_hidden');
					jQuery('#WDI_conditional_filter_type').parent().parent().parent().parent().parent().addClass('wdi_hidden');
					jQuery('#wdi_final_condition').addClass('wdi_hidden');
					jQuery('#WDI_filter_source').addClass('wdi_hidden');
					break;
				}
			case '1':
				{
					jQuery('#WDI_conditional_filters').parent().parent().removeClass('wdi_hidden');
					jQuery('#WDI_conditional_filter_type').parent().parent().parent().parent().parent().removeClass('wdi_hidden');
					jQuery('#wdi_final_condition').removeClass('wdi_hidden');
					jQuery('#WDI_filter_source').removeClass('wdi_hidden');
					break;
				}
		}
	}

	jQuery('#WDI_conditional_filter_type').on('change', function() {
		if (jQuery(this).val() == 'none') {

		} else {
			jQuery('#WDI_conditional_filters').css('display', 'block');
		}

		jQuery(this).parent().find('label').css({
			// 'line-height': '24px',
			// 'height': '24px',
			// 'padding': '2px 5px',
			 'display': 'inline-block',
			// 'font-size': '15px',
			// 'color': 'black',
			// 'font-weight': '500',
			// '-webkit-user-select': 'none',
			// /* Chrome/Safari */
			// '-moz-user-select': 'none',
			// /* Firefox */
			// '-ms-user-select': 'none',
			// /* IE10+ */
            //
			// /* Rules below not implemented in browsers yet */
			// '-o-user-select': 'none',
			// 'user-select': 'none',
		});

		switch (jQuery(this).val()) {
			case 'AND':
				{
					jQuery('#WDI_conditional_filters').css('display', 'block');
					jQuery(this).parent().find('label').html(wdi_messages.and_descr);
					break;
				}
			case 'OR':
				{
					jQuery('#WDI_conditional_filters').css('display', 'block');
					jQuery(this).parent().find('label').html(wdi_messages.or_descr);
					break;
				}
			case 'NOR':
				{
					jQuery('#WDI_conditional_filters').css('display', 'block');
					jQuery(this).parent().find('label').html(wdi_messages.nor_descr);
					break;
				}
		}

		wdi_controller.updateFiltersUi();
	});
	//triggering change for updating first time
	jQuery('#WDI_conditional_filter_type').trigger('change');
}

/**
 * Takes user input and adds new filter based on filter type and user input
 */
wdi_controller.addConditionalFilter = function() {
	var input = jQuery('#wdi_filter_input').val(),
	// filter_type = jQuery('input[name="wdi_filter_type"]:checked').val(),
	filter_type = jQuery('#wdi_filter_type').val();
	filter = {};

	if (input == '') {
		return false;
	}
	input = input.trim();

	if (filter_type == null) {
		if (input[0] == '@') {
			filter_type = 'mention';
		} else if (input[0] == '#') {
			filter_type = 'hashtag';
		} else {
			if (input.split('://')[0] == 'http' || input.split('://')[0] == 'https') {
				filter_type = 'url';
			}
		}
	}

	switch (filter_type) {
		case 'username':
			{
				if (input[0] == '@') {
					input = input.substr(1, input.length);
				}
				break;
			}
		case 'mention':
			{
				if (input[0] == '@') {
					input = input.substr(1, input.length);
				}
				break;
			}
		case 'hashtag':
			{
				if (input[0] == '#') {
					input = input.substr(1, input.length);
				}
				break;
			}
		case 'url':
			{
				var urlRegex = /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
				if (!urlRegex.test(input)) {
					alert(wdi_messages.invalid_url);
					return false;
				}
				break;
			}
	}



	filter = {
		'filter_type': filter_type,
		'filter_by': input,
		'id': this.randomId()
	};
	if (filter_type != null) {
		if (!this.filterExists(filter)) {
			this.conditionalFilters.push(filter);
			this.updateFiltersUi();
			return true;
		} else {
			alert(input + ' ' + wdi_messages.already_added);
			return false;
		}
	} else {
		alert(wdi_messages.selectConditionType);
		return false;
	}
}

/**
 * Returns true if filter exists else returns false
 * @param  {Object} filter [Filter objecr]
 * @return {Booleans}        [true or false]
 */
wdi_controller.filterExists = function(filter) {
	for (var i = 0; i < this.conditionalFilters.length; i++) {
		if (this.conditionalFilters[i].filter_type == filter.filter_type && this.conditionalFilters[i].filter_by == filter.filter_by) {
			return true;
		}
	}
	return false;
}

/**
 * Updates #wdi_filters_ui div to the latest version of filters according wdi_controller.conditionalFilters 
 */
wdi_controller.updateFiltersUi = function() {
	var uiElement = jQuery('#wdi_filters_ui').html('');
	for (var i = 0; i < this.conditionalFilters.length; i++) {

		if (i == 0) {
			if (this.conditionalFilters.length != 1) {
				switch (jQuery('#WDI_conditional_filter_type').val()) {
					case 'AND':
						{

							break;
						}
					case 'OR':
						{
							uiElement.append(jQuery('<span class="wdi_logic">' + wdi_messages.either + '</span>'));
							break;
						}
					case 'NOR':
						{
							uiElement.append(jQuery('<span class="wdi_logic">' + wdi_messages.neither + '</span>'));
							break;
						}
				}
			} else {
				switch (jQuery('#WDI_conditional_filter_type').val()) {
					case 'AND':
						{
							break;
						}
					case 'OR':
						{
							break;
						}
					case 'NOR':
						{
							uiElement.append(jQuery('<span class="wdi_logic">' + wdi_messages.not + '</span>'));
							break;
						}
				}
			}

		}

		var glue;
		switch (jQuery('#WDI_conditional_filter_type').val()) {
			case 'AND':
				{
					glue = wdi_messages.and;
					break;
				}
			case 'OR':
				{
					glue = wdi_messages.or;
					break;
				}
			case 'NOR':
				{
					glue = wdi_messages.nor;
					break;
				}
		}

		if (i >= 1) {
			uiElement.append(jQuery('<span class="wdi_logic">' + glue + '</span>'));
		}

		uiElement.append(this.createUiElement(this.conditionalFilters[i]));

	}
	this.updateFilterTextarea();
}

/**
 * Creates jQuery element for filter
 * @param  {Object} filter [filter object]
 * @return {Object}        [jQuery Object]
 */
wdi_controller.createUiElement = function(filter) {
	var specialChar;
	switch (filter['filter_type']) {
		case 'mention':
			{
				specialChar = '@';
				break;
			}
		case 'hashtag':
			{
				specialChar = '#';
				break;
			}
		case 'location':
			{
				specialChar = '%';
				break;
			}
		default:
			{
				specialChar = '';
				break;
			}
	}

	var filter_item = jQuery('<span data-id="' + filter['id'] + '" class="wdi_filter_item wdi_filter_by_' + filter['filter_type'] + '"></span>').
	html(specialChar + filter['filter_by'] + '<span onclick="wdi_controller.removeConditionalFilter(jQuery(this));" class="wdi_remove_filter">X</span>');
	return filter_item;
}

/**
 * Used for generating random ids
 * @return {String} [random 5 length string]
 */
wdi_controller.randomId = function() {
	var text = "";
	var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	for (var i = 0; i < 5; i++)
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	return text;
}

/**
 * Removes filter from wdi_controller.conditionalFilters array and updates #wdi_filters_ui
 * @param  {Object} element [jQuery object]
 */
wdi_controller.removeConditionalFilter = function(element) {
	var id = element.parent().attr('data-id');
	for (var i = 0; i < this.conditionalFilters.length; i++) {
		if (this.conditionalFilters[i]['id'] == id) {
			this.conditionalFilters.splice(i, 1);
		}
	}
	this.updateFiltersUi();
}

/**
 * Updates textarea to the latest version of conditionalFilters json
 */
wdi_controller.updateFilterTextarea = function() {
	var json,
		filters = this.conditionalFilters;

	json = JSON.stringify(filters);
	jQuery('#wdi_conditional_filters_textarea').val(json);
}

/**
 * Gets json from textarea and sets them as conditionalfilters array
 */
wdi_controller.setInitialFilters = function() {
	var filters = [],
		json = jQuery('#wdi_conditional_filters_textarea').val();

	if (this.isJsonString(json)) {
		filters = JSON.parse(json);
	}

	this.conditionalFilters = filters;
}

/**
 * Updates Conditional Filter User interfaces
 */
wdi_controller.updateConditionalFiltersUi = function() {
	wdi_controller.updateFilterSource();
}

/**
 * Updates Conditinal filter source
 */
wdi_controller.updateFilterSource = function() {
	if(jQuery('input[name="wdi_feed_settings[liked_feed]"]:checked').val() == 'liked'){
		var sourceDiv = jQuery('#wdi_filter_source').html('');
  	var singleUserHtml = "<div class='wdi_source_user'><span class='wdi_source_username'>Media I liked</span></div>";
		sourceDiv.html(sourceDiv.html() + singleUserHtml);
		return;
	}


	var users = [],
		username,
		userThumb;

	jQuery('.wdi_user').each(function() {
		if (jQuery(this).find('.wdi_username').length != 0) {
			username = jQuery(this).find('.wdi_username').text();
		} else {
			username = jQuery(this).find('.wdi_hashtag').text();
		}
		userThumb = jQuery(this).find('img').attr('src');
		users.push({
			'username': username,
			'image': userThumb
		})
	});

	var sourceDiv = jQuery('#wdi_filter_source').html('');
	for (var i = 0; i < users.length; i++) {
		var singleUserHtml = "<div class='wdi_source_user'><span class='wdi_source_img'><img src='" + users[i].image + "'></span><span class='wdi_source_username'>" + users[i].username + "</span></div>";
		sourceDiv.html(sourceDiv.html() + singleUserHtml);
	}
}

/**
 * Checks if given string is JSON string
 * @param  {String}  str [string to check]
 * @return {Boolean}     [true or false]
 */
wdi_controller.isJsonString = function(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

wdi_controller.getUserObj = function (user) {
	var users = JSON.parse(wdi_options.wdi_authenticated_users_list);
	if (typeof users == 'object') {
		if (typeof users[user] == 'object' && users[user] != '') {
			return users[user];
		}
		else {
			console.log('Error: User not exist on Users object');
		}
	}
	else {
    console.log('Error: Wrong response when parsed on users (JSON.parse)');
	}

	return false;
}

///////////////////////////////////////////////////////////////////////////////
///////////////Feeds and themes first view functions///////////////////////////
////////////////////////////////////////////////////////////////////////////////
function wdi_spider_select_value(obj) {
	obj.focus();
	obj.select();
}

// Set value by id.
function wdi_spider_set_input_value(input_id, input_value) {
	if (input_value === 'add') {
		if (jQuery('#wdi_access_token').attr('value') == '') {
			alert('Please get your access token');
		}
	}
	if (document.getElementById(input_id)) {
		document.getElementById(input_id).value = input_value;
	}
}

// Submit form by id.
function wdi_spider_form_submit(event, form_id) {
	if (document.getElementById(form_id)) {
		document.getElementById(form_id).submit();
	}
	if (event.preventDefault) {
		event.preventDefault();
	} else {
		event.returnValue = false;
	}
}

function wdi_bulk_actions(that) {
  var action = jQuery(that).val();
  if (action != '') {
    if (action == 'delete_all') {
      if (!confirm(wdi_messages.do_you_want_to_delete_selected_items)) {
        return false;
      }
    }
    wdi_spider_set_input_value('task', action);
    jQuery('#wdi_feed_form').submit();
  }
  else {
    return false;
  }
  return true;
}

// Check all items.
function wdi_spider_check_all_items() {
	wdi_spider_check_all_items_checkbox();
	// if (!jQuery('#check_all').prop('checked')) {
	jQuery('#check_all').trigger('click');
	// }
}

function wdi_spider_check_all_items_checkbox() {
	if (jQuery('#check_all_items').prop('checked')) {
		jQuery('#check_all_items').prop('checked', false);
		jQuery('#draganddrop').hide();
	} else {
		var saved_items = (parseInt(jQuery(".displaying-num").html()) ? parseInt(jQuery(".displaying-num").html()) : 0);
		var added_items = (jQuery('input[id^="check_pr_"]').length ? parseInt(jQuery('input[id^="check_pr_"]').length) : 0);
		var items_count = added_items + saved_items;
		jQuery('#check_all_items').prop('checked', true);
		if (items_count) {
			jQuery('#draganddrop').html("<strong><p>Selected " + items_count + " item" + (items_count > 1 ? "s" : "") + ".</p></strong>");
			jQuery('#draganddrop').show();
		}
	}
}

function wdi_spider_check_all(current) {
	if (!jQuery(current).prop('checked')) {
		jQuery('#check_all_items').prop('checked', false);
		jQuery('#draganddrop').hide();
	}
}

// Set value by id.
function wdi_spider_set_input_value(input_id, input_value) {
	if (input_value === 'add') {
		if (jQuery('#wdi_access_token').attr('value') == '') {
			alert('Please get your access token');
		}
	}
	if (document.getElementById(input_id)) {
		document.getElementById(input_id).value = input_value;
	}
}


function wdi_account_remove(user_name,user_id) {
	jQuery.ajax({
		type: 'POST',
		url: wdi_ajax.ajax_url,
		dataType: 'json',
		data: {
			page: 'wdi_settings',
			action: 'wdi_account_disconnect',
			nonce: wdi_ajax.wdi_nonce,
			user_name: user_name,
			user_id: user_id,
		},
		success: function (response) {
			if ( response.success ) {
				if (jQuery('[class*="wdi-account-list"]').length == 1) {
					var account = jQuery('.wdi-account-list-' + user_id).parent()
					account.prev().remove()
					account.remove();
					jQuery("#toplevel_page_wdi_feeds ul").remove()
					jQuery("#toplevel_page_wdi_feeds a").attr("href", "admin.php?page=wdi_settings")
				} else {
					jQuery('.wdi-account-list-' + user_id).remove();
				}
			}
		}
	});
}

function wdi_popup_open(){
	jQuery('.wdi-popup').show();
	return;
}

function wdi_popup_close(){
	jQuery('.wdi-popup').hide();
	return;
}