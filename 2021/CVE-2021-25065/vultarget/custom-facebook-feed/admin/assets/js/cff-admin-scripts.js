jQuery(document).ready(function($) {
	//Tooltips
	jQuery('#cff-admin .cff-tooltip-link').on('click', function(){
		jQuery(this).closest('tr, h3, div').find('.cff-tooltip').slideToggle();
	});

	//Toggle Access Token field
	if( jQuery('#cff_show_access_token').is(':checked') ) jQuery('.cff-access-token-hidden').show();
	jQuery('#cff_show_access_token').on('change', function(){
		jQuery('.cff-access-token-hidden').fadeToggle();
	});


	$(document).on('click', '#cff-dismiss-header-notice', function() {
		$.ajax({
			url : cffA.ajax_url,
			type : 'post',
			data : {
				action : 'cff_dismiss_upgrade_notice',
				cff_nonce: cffA.cff_nonce
			},
			success : function(data) {
				if ( data.success == true ) {
					$('#cff-header-upgrade-notice').slideUp();
					$("#cff-builder-app").removeClass('cff-builder-app-lite-dismiss');
				}
			},
			error : function(e)  {
				console.log(e);
			}
		});
	});

	$('body').on('click','.cff-dismissible .notice-dismiss',function() {
		$.ajax({
			url : cffA.ajax_url,
			type : 'post',
			data : {
				action : 'cff_dismiss_custom_cssjs_notice',
				cff_nonce: cffA.cff_nonce
			},
			success : function(data) {
			},
			error : function(e)  {
				console.log(e);
			}
		});
	});

	//The "cff_ppca_access_token_invalid" transient is set if the access token doesn't match the ID specified. Use an Ajax call to check whether that transient is set, and if so, then displays a notice under the access token field. This used so we don't need to make an API call every time the page loads. It stores the value in this transient and checks it via ajax.
	$.ajax({
		url : cffA.ajax_url,
		type : 'get',
		data : {
			action : 'cff_ppca_token_check_flag',
			cff_nonce: cffA.cff_nonce

		},
		success : function(data) {
			if( data ) $('.cff-ppca-check-notice.cff-error').show();
		},
		error : function(e)  {
			console.log(e);
		}
	});

	//If no transient exists (eg. after the settings are saved) then check the API to see whether the ID and token match
	if( typeof jQuery('#cff_access_token').attr('data-check-ppca') !== 'undefined' ){

		var page_id = $('#cff-admin #cff_page_id').val(),
			access_token =  $('#cff-admin #cff_access_token').val();

		if( page_id.indexOf(',') == -1 && access_token.indexOf(',') == -1 ){
			var ppca_check_url = 'https://graph.facebook.com/v8.0/'+page_id+'/posts?limit=1&access_token='+access_token;

			$.ajax({
		        url: ppca_check_url,
		        type: 'GET',
		        dataType: "jsonp",
		        cache: false,
		        success: function(data) {
		        	if (typeof data.error !== 'undefined') {
		        		if( data.error.message.indexOf('Public Content') !== -1 ){
		        			//If the API response shows a PPCA error then show the notice below the access token field
		        			$('.cff-ppca-check-notice.cff-error').show();

		        			//Store the API response in a transient which is then checked above on line 29
		        			$.ajax({
								url : cffA.ajax_url,
								type : 'post',
								data : {
									action : 'cff_ppca_token_set_flag'
								},
								success : function(data) {
									//Access token transient set
								},
								error : function(e)  {
									console.log(e);
								}
							});
		        		}
		        	} else {
		        		//If the API response shows a good token then display a success notice instead
		        		$('#cff-admin #cff_access_token').after('<div class="cff-ppca-check-notice cff-success" style="display: block;"><i class="fa fa-check-circle"></i>&nbsp; Valid Access Token</div>');
		        	}
		        },
		        error: function(xhr,textStatus,e) {
		        	console.log(e);

		            return;
		        }
		    }); //End ajax
		}
	}

	//Is this a page, group or profile?
	var cff_page_type = jQuery('.cff-page-type select').val(),
		$cff_page_type_options = jQuery('.cff-page-options'),
		$cff_profile_error = jQuery('.cff-profile-error.cff-page-type'),
		$cff_group_error = jQuery('.cff-group-error.cff-page-type');

	//Should we show anything initially?
	if(cff_page_type !== 'page') $cff_page_type_options.hide();
	if(cff_page_type == 'profile') $cff_profile_error.show();
	if(cff_page_type == 'group') $cff_group_error.show();

	//When page type is changed show the relevant item
	jQuery('.cff-page-type').on('change', function(){
		cff_page_type = jQuery('.cff-page-type select').val();

		if( cff_page_type !== 'page' ) {
			$cff_page_type_options.hide();
			if( cff_page_type == 'profile' ) {
					$cff_profile_error.show();
					$cff_group_error.hide();
				} else if( cff_page_type == 'group' ) {
					$cff_group_error.show();
					$cff_profile_error.hide();
				} else {
					$cff_group_error.hide();
					$cff_profile_error.hide();
				}

		} else {
			$cff_page_type_options.show();
			$cff_profile_error.hide();
			$cff_group_error.hide();
		}
	});


	//Post limit manual setting
	var cff_limit_setting = jQuery('#cff_limit_setting').val(),
			cff_post_limit = jQuery('#cff_post_limit').val(),
			$cff_limit_manual_settings = jQuery('#cff_limit_manual_settings');
	if( typeof cff_post_limit === 'undefined' ) cff_post_limit = '';

	//Should we show anything initially?
	if(cff_limit_setting == 'auto') $cff_limit_manual_settings.hide();
	if(cff_post_limit.length > 0){
		$cff_limit_manual_settings.show();
		jQuery('#cff_limit_setting').val('manual');
	}

	jQuery('#cff_limit_setting').on('change', function(){
		cff_limit_setting = jQuery('#cff_limit_setting').val();

		if(cff_limit_setting == 'auto'){
			$cff_limit_manual_settings.hide();
			jQuery('#cff_post_limit').val('');
		} else {
			$cff_limit_manual_settings.show();
		}
	});

	//Header Type Selection
	var cff_header_type = jQuery('.cff-header-type select').val(),
		$cff_facebook_header_options = jQuery('.cff-facebook-header'),
		$cff_text_header_options = jQuery('.cff-text-header');

	//Should we show anything initially?
	if(cff_header_type !== 'visual') $cff_facebook_header_options.hide();
	if(cff_header_type !== 'text') $cff_text_header_options.hide();

	//When Header type is changed show the relevant item
	jQuery('.cff-header-type').on('change', function(){
		cff_header_type = jQuery('.cff-header-type select').val();

		if( cff_header_type !== 'visual' ) {
			$cff_facebook_header_options.hide();
			$cff_text_header_options.show();
		} else {
			$cff_facebook_header_options.show();
			$cff_text_header_options.hide();
		}
	});

	//Header icon
	//Icon type
	//Check the saved icon type on page load and display it
	jQuery('#cff-header-icon-example').removeClass().addClass('fa fa-' + jQuery('#cff-header-icon').val() );
	//Change the header icon when selected from the list
	jQuery('#cff-header-icon').on('change', function() {
	    var $self = jQuery(this);

	    jQuery('#cff-header-icon-example').removeClass().addClass('fa fa-' + $self.val() );
	});


	//Test Facebook API connection button
	jQuery('#cff-api-test').on('click', function(e){
		e.preventDefault();
		//Show the JSON
		jQuery('#cff-api-test-result textarea').css('display', 'block');
	});


	//If '__ days ago' date is selected then show 'Translate this'
	var $cffTranslateDate = jQuery('#cff-translate-date');

	if ( jQuery("#cff-date-formatting option:selected").val() == '1' ) $cffTranslateDate.show();

	jQuery("#cff-date-formatting").on('change', function() {
		if ( jQuery("#cff-date-formatting option:selected").val() == '1' ) {
			$cffTranslateDate.fadeIn();
		} else {
			$cffTranslateDate.fadeOut();
		}
	});

	//Selecting a post style
	jQuery('.cff-post-style').on('click', function(){
        var $self = jQuery(this);
        $('.cff_post_style').trigger('change');
        $self.addClass('cff-layout-selected').find('#cff_post_style').attr('checked', 'checked');
        $self.siblings().removeClass('cff-layout-selected');
    });
    function cffChangePostStyleSettings() {
        setTimeout(function(){
            jQuery('.cff-post-style-settings').hide();
            jQuery('.cff-post-style-settings.cff-'+jQuery('.cff_post_style:checked').val()).show();
        }, 1);
    }
    cffChangePostStyleSettings();
    jQuery('.cff_post_style').on('change', cffChangePostStyleSettings);

	//Add the color picker
	if( jQuery('.cff-colorpicker').length > 0 ) jQuery('.cff-colorpicker').wpColorPicker();


	//Mobile width
	var cff_feed_width = jQuery('#cff-admin #cff_feed_width').val(),
			$cff_width_options = jQuery('#cff-admin #cff_width_options');

	if (typeof cff_feed_width !== 'undefined') {
		//Show initially if a width is set
		if(cff_feed_width.length > 1 && cff_feed_width !== '100%') $cff_width_options.show();

		jQuery('#cff_feed_width').on('change', function(){
			cff_feed_width = jQuery(this).val();

			if( cff_feed_width.length < 2 || cff_feed_width == '100%' ) {
				$cff_width_options.slideUp();
			} else {
				$cff_width_options.slideDown();
			}
		});
	}

	//Scroll to hash for quick links
	jQuery('#cff-admin a').on('click', function() {
	if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
	  var target = jQuery(this.hash);
	  target = target.length ? target : this.hash.slice(1);
	  if (target.length) {
	    jQuery('html,body').animate({
	      scrollTop: target.offset().top
	    }, 500);
	    return false;
	  }
	}
	});

	//Shortcode tooltips
	jQuery('#cff-admin label').on('click', function(){
	  	var $el = jQuery(this);
	    var $cff_shortcode = $el.siblings('.cff_shortcode');
	    if($cff_shortcode.is(':visible')){
	      $el.siblings('.cff_shortcode').css('display','none');
	    } else {
	      $el.siblings('.cff_shortcode').css('display','block');
	    }
	});
	jQuery('#cff-admin th').on('hover', function(){
		if( jQuery(this).find('.cff_shortcode').length > 0 ){
		  jQuery(this).find('label').append('<code class="cff_shortcode_symbol">[]</code>');
		}
	}, function(){
		jQuery(this).find('.cff_shortcode_symbol').remove();
	});
	jQuery('#cff-admin label').on('hover', function(){
		if( jQuery(this).siblings('.cff_shortcode').length > 0 ){
		  jQuery(this).attr('title', 'Click for shortcode option');
		}
	}, function(){});

	//Open/close the expandable option sections
	jQuery('.cff-expandable-options').hide();
	jQuery('.cff-expand-button a').on('click', function(e){
		e.preventDefault();
		var $self = jQuery(this);
		$self.parent().next('.cff-expandable-options').toggle();
		if( $self.text().indexOf('Show') !== -1 ){
			$self.text( $self.text().replace('Show', 'Hide') );
		} else {
			$self.text( $self.text().replace('Hide', 'Show') );
		}
	});

	function cffToggleNummobile() {
		if (jQuery('#cff_show_num_mobile').is(':checked')) {
			jQuery('#cff_show_num_mobile').closest('td').find('.cff-mobile-col-settings').slideDown();
		} else {
			jQuery('#cff_show_num_mobile').closest('td').find('.cff-mobile-col-settings').slideUp(function(){
				jQuery('#cff_num_mobile').val('');
			});
		}
	}
	cffToggleNummobile();
	jQuery('#cff_show_num_mobile').on('change', cffToggleNummobile);

	//Facebook login
	$('#cff_fb_login').on('click', function(){
		$('#cff_fb_login_modal').show();
	});
	$('#cff_admin_cancel_btn').on('click', function(){
		$('#cff_fb_login_modal').hide();
	});
	$('.cff-modal-close, #cff-close-modal-primary-button').on('click', function(){
		$('.cff_modal_tokens').hide();
	});
	$('#cff_fb_show_tokens').on('click', function(){
		$('.cff_modal_tokens, .cff-groups-list').show();
		$('#cff-group-installation').hide();
	});

	//Select a page for token
	$('.cff-managed-page').on('click', function(){
		$('#cff-insert-token, .cff-insert-reviews-token, .cff-insert-both-tokens').removeAttr('disabled');

		$('#cff_token_expiration_note').show();

		var $self = $(this);
		if( $self.hasClass('cff-page-selected') ){
			$self.removeClass('cff-page-selected');
		} else {
			$self.addClass('cff-page-selected');
		}
	});





	//Connect Accounts array object
	var cff_connected_accounts = {},
		cff_multifeed_enabled = false,
		cff_remove_primary_text = 'Remove as Primary Feed',
    	cff_add_primary_text = 'Make Primary Feed';

    if( $('#cff_page_id').hasClass('cff_multifeed_enabled') ) cff_multifeed_enabled = true;
    if( cff_multifeed_enabled ){
    	cff_remove_primary_text = 'Remove from Primary Feed';
    	cff_add_primary_text = 'Add to Primary Feed';
    }

	//If there are accounts displayed then assign them to the connected accounts array
	var cff_connected_accounts_val = $('#cff_connected_accounts').val();
	if( cff_connected_accounts_val !== '' && cff_connected_accounts_val !== '{}' && typeof cff_connected_accounts_val !== 'undefined' ){

		cff_connected_accounts = cff_connected_accounts_val.replace(/\\"/g, '"');
		cff_connected_accounts = JSON.parse(cff_connected_accounts);

		createAccountHTML(cff_connected_accounts);
	}

	//Insert Page Access Token
	$('#cff-insert-token, #cff-insert-all-tokens').on('click', function(){

		if( $(this).hasClass('cff_connect_all') ) $('.cff-managed-page').addClass('cff-page-selected');

		var $selectedPage = $('.cff-page-selected'),
			selectedPageId = $selectedPage.attr('data-page-id'),
			selectedPageToken = $selectedPage.attr('data-token');

		//Add ID to setting
		if( $('#cff_page_id').val().trim() == '' ){
			$('#cff_page_id').val( selectedPageId ).addClass('cff-success');
			cffAddCurIdLabel($('.cff-page-selected').first().find('.cff-page-info-name').text(), $('.cff-page-selected').first().find('.cff-page-avatar').attr('src'));
		}

		//Add token to setting
		if( $('#cff_access_token').val().trim() == '' || $('#cff_access_token').hasClass('cff-replace-token') ){
			//If multifeed then add ID to front so it's assigned to that ID in the feed
			if( $('#cff_page_id').hasClass('cff_multifeed_enabled') ) selectedPageToken = selectedPageId + ':' + selectedPageToken;

			$('#cff_access_token').val( selectedPageToken ).addClass('cff-success');
		}

		if( $(this).hasClass('cff-group-btn') ){
			$('.cff-groups-list').hide();
			$('#cff-group-installation').show();

			//Show directions for either group admin or member
			if( $('.cff-page-selected').hasClass('cff-group-admin') ){
				$('#cff-group-admin-directions').show();
				$('#cff-group-member-directions').hide();
			} else {
				$('#cff-group-admin-directions').hide();
				$('#cff-group-member-directions').show();
			}

			//Change page type to be group
			$('#cff_page_type').val('group');
			$('.cff-page-options').hide();

			//Dynamically create group edit link
			var cffGroupEditLink = 'https://facebook.com/groups/'+selectedPageId+'/edit';
			$('#cff-group-installation #cff-group-edit').attr('href', cffGroupEditLink);
		} else {
			$('.cff_modal_tokens').hide();
		}

		// cff_connected_accounts
		$('.cff-managed-pages').find('.cff-page-selected').each(function(){
			var $page = $(this);

			addConnectedAccounts(
				$page.attr('data-page-id'),
				$page.find('.cff-page-info-name').text(),
				$page.attr('data-pagetype'),
				$page.attr('data-token'),
				$page.find('.cff-page-avatar').attr('src')
			);

		});

		location.hash = "cffnomodal";
	});

	//Manually connect account
	//Step 1
	$('#cff_manual_account_button, #cff-admin .cff_manual_back').on('click', function(e){
		e.preventDefault();
		if( !$(this).hasClass('cff_manual_back') ) $('#cff_manual_account').toggle();
		$('#cff_manual_account_step_1').show();
		$('#cff_manual_account_step_2').hide();
	});
	//Step 2
	jQuery("#cff_manual_account_type").on('change', function() {
		cff_go_to_step_2();
	});
	$('#cff-admin .cff_manual_forward').on('click', function(){
		if( $("#cff_manual_account_type option:selected").val() ){
			cff_go_to_step_2();
		} else {
			$("#cff_manual_account_type").addClass('cff_error');
			setTimeout(function(){ $("#cff_manual_account_type").removeClass('cff_error'); }, 500);
		}
	});
	function cff_go_to_step_2(){
		$('#cff_manual_account_step_2').attr('class', 'cff_account_type_'+jQuery("#cff_manual_account_type option:selected").val() );

		$('#cff_manual_account_step_1').hide();
		$('#cff_manual_account_step_2').show();
	}

	//Add account
	$('#cff_manual_account_step_2 input[type=submit]').on('click', function(e){
		e.preventDefault();

		var $cff_manual_account = $('#cff_manual_account');

		addConnectedAccounts(
			$cff_manual_account.find('#cff_manual_account_id').val(),
			$cff_manual_account.find('#cff_manual_account_name').val(),
			$cff_manual_account.find('#cff_manual_account_type').val(),
			$cff_manual_account.find('#cff_manual_account_token').val(),
			false
		);
	});

	//Only enable manual account submit button if ID/token fields have values
	$('#cff_manual_account_id, #cff_manual_account_token').on('input', function() {
		if( $('#cff_manual_account_id').val() == '' || $('#cff_manual_account_token').val() == '' ){
			$('#cff_manual_account_step_2 #submit').attr('disabled', true);
		} else {
			$('#cff_manual_account_step_2 #submit').removeAttr('disabled');
		}
	});

	//Show raw account data (can be used for exporting/importing accounts in bulk)
	$('#cff_export_accounts').on('click', function(e){
		e.preventDefault();
		$('#cff_export_accounts_wrap').toggle();
	});


	function addConnectedAccounts(id, name, pagetype, accesstoken, avatar=false){

		if( pagetype == 'page' ) avatar = '';

		id = cffStripURLParts(id);

		//Add to connected accounts array
		cff_connected_accounts[id] = {
			id: id,
			name: encodeURI( name ),
			pagetype: pagetype,
			accesstoken: accesstoken,
			avatar: avatar
		};

		//Update setting on page
		$('#cff_connected_accounts').val( JSON.stringify(cff_connected_accounts) );

		//Add HTML to page
		createAccountHTML(cff_connected_accounts);
	}

	function removeConnectedAccount($account){
		//Remove account from array
		delete cff_connected_accounts[$account.attr('data-page-id')];

		//Update setting on page
		$('#cff_connected_accounts').val( JSON.stringify(cff_connected_accounts) );

		//Remove it from primary feed if it's in there
		removePrimaryAcount($account);

		//Remove account element from page
		$account.remove();
	}

	function cffStripURLParts(string){
		if (typeof string === 'undefined') {
			return '';
		}
		//If user pastes their full URL into the Page ID field then strip it out
		var cff_facebook_string = 'facebook.com',
			hasURL = (string.indexOf(cff_facebook_string) > -1);
		if (hasURL) {
			var stringArr = string.split('?')[0].replace(/\/$/, '').split('/');
			string = stringArr[stringArr.length-1].replace(/[\.\/]/,'');
		}

		return string;
	}

	function createAccountHTML(cff_connected_accounts){

		var accountsHTML = '';

		//Loop through accounts and create HTML
		for (var key in cff_connected_accounts) {
		    if (cff_connected_accounts.hasOwnProperty(key)) {

		        var id = cffStripURLParts(cff_connected_accounts[key]['id']),
		        	name = decodeURI(cff_connected_accounts[key]['name']),
		        	pagetype = cff_connected_accounts[key]['pagetype'],
		        	accesstoken = cff_connected_accounts[key]['accesstoken'],
		        	avatar = cff_connected_accounts[key]['avatar'],
		        	cff_account_active = '',
		        	no_avatar = false;

		        if( (!avatar || avatar == 'false' ) && pagetype == 'group' ) no_avatar = true;
		        if( !avatar || avatar == '' ) avatar = 'https://graph.facebook.com/'+id+'/picture';

		        //If it's in use then mark it as primary/active
		        if( $('#cff_page_id').val().indexOf(id) !== -1 ) cff_account_active = ' cff_account_active';

		        accountsHTML += '<div class="cff_connected_account cff_account_type_'+pagetype+cff_account_active+'" id="cff_connected_account_'+id+'" data-accesstoken="'+accesstoken+'" data-pagetype="'+pagetype+'" data-page-id="'+id+'">' +
                    '<div class="cff_ca_info">' +
                        '<div class="cff_ca_delete"><a href="JavaScript:void(0);" class="cff_delete_account"><i class="fa fa-times"></i><span class="cff_remove_text">Remove</span></a></div>'+
                        '<div class="cff_ca_username">';
                        ( no_avatar ) ? accountsHTML += '' : accountsHTML += '<img class="cff_ca_avatar" src="'+avatar+'">';
							accountsHTML += '<strong><span class="cff_ca_fullname">'+name+'</span><span class="cff_ca_pagetype">'+pagetype+' ID: '+id+'</span></strong>' +
                        '</div>' +
                        '<div class="cff_ca_actions">' +
							'<a href="JavaScript:void(0);" class="cff_make_primary">';
							if( cff_account_active !== '' ){
								accountsHTML += '<i class="fa fa-minus-circle" aria-hidden="true"></i>'+cff_remove_primary_text;
							} else {
								accountsHTML += '<i class="fa fa-plus-circle" aria-hidden="true"></i>'+cff_add_primary_text;
							}
							accountsHTML += '</a>';

							if( $('#cff_page_access_token').length && pagetype == 'page' ) accountsHTML += '<a href="JavaScript:void(0);" class="cff_make_reviews"><i class="fa fa-star" aria-hidden="true"></i>Use for Reviews Feed</a>';

							accountsHTML += '<a class="cff_ca_token_shortcode" href="JavaScript:void(0);"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i>Add to another Feed</a>' +
                            '<p class="cff_ca_show_token"><a href="javascript:void(0);" id="cff_ca_show_token_'+id+'"><i class="fa fa-ellipsis-h" style="margin: 0; font-size: 12px;" aria-hidden="true"></i></a></p>' +
                        '</div>' +
                        '<div class="cff_ca_shortcode">' +
                            '<p>Copy and paste this shortcode into your page or widget area:<br>' +
                                '<code>[custom-facebook-feed account="'+id+'" pagetype="'+pagetype+'"]</code>' +
                            '</p>';
                        if( cff_multifeed_enabled ) accountsHTML += '<p>To add multiple accounts in the same feed, simply separate them using commas:<br>' +
								'<code>[custom-facebook-feed account="'+id+', account_2, account_3"]</code>' +
                            '</p>';
                        accountsHTML += '<p>Click <a href="https://smashballoon.com/custom-facebook-feed/docs/shortcodes/" target="_blank">here</a> to learn more about shortcodes</p>' +
                        '</div>' +
                        '<div class="cff_ca_accesstoken">' +
                            '<span class="cff_ca_token_label">Access Token:</span><input type="text" class="cff_ca_token" value="'+accesstoken+'" readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).">' +
                        '</div>' +
                    '</div>' +
                '</div>';
		    }
		}

		//Add HTML to page
		$('#cff_connected_accounts_wrap').html(accountsHTML);

		//Add Raw Data button
		$('.cff_connected_actions').show();

	}

	function removePrimaryAcount($account){
		//Remove ID/token from fields
    	if( $account.hasClass('cff_account_active') ){

    		var selected_id = $account.attr('data-page-id'),
        		selected_token = $account.attr('data-accesstoken');

    		//Remove as primary account
    		cffLabelAsPrimary($account);

    		$('#cff_primary_account_label').hide();

    		if( cff_multifeed_enabled ){

    			//Find the ID from the removed account and remove it from the ID field
    			var updatedIdVal = $('#cff_page_id').val().replace(selected_id, '');
    			//Remove any stray commas left over
    			updatedIdVal = updatedIdVal.replace(',,', '').replace(' ,', '').replace(/^, |, $/g,'');

        		$('#cff_page_id').val( updatedIdVal ).removeClass('cff-success');

        		//Remove Token
        		// var updatedTokenVal = $('#cff_access_token').val().replace(selected_id+':'+selected_token, '').replace(selected_token, '');
        		var updatedTokenVal = $('#cff_access_token').val().replace(selected_id+':'+selected_token, '');
        		//Remove any stray commas left over
    			updatedTokenVal = updatedTokenVal.replace(',,', '').replace(' ,', '').replace(':,', ':').replace(/^, |, $/g,'');

        		$('#cff_access_token').val( updatedTokenVal ).removeClass('cff-success');


        	} else {

        		//Revert ID/token fields back to previous values
        		$('#cff_page_id').val( $('#cff_page_id').attr('data-page-id') ).removeClass('cff-success');
        		$('#cff_access_token').val( $('#cff_access_token').attr('data-accesstoken') ).removeClass('cff-success');

        	}
    	}
	}


	var $body = $('body');
	//Show Access Token
	$body.on('click', '.cff_ca_show_token a', function(e) {
		e.preventDefault();
        jQuery(this).closest('.cff_ca_info').find('.cff_ca_accesstoken').slideToggle(200);
    });
    $body.on('click', '.cff_ca_token_shortcode, .cff_make_primary, .cff_make_reviews', function (event) {
        event.preventDefault();
        var $clicked = $(event.target);
        //Show shortcode
        if( $clicked.hasClass('cff_ca_token_shortcode') ) {
            jQuery(this).closest('.cff_ca_info').find('.cff_ca_shortcode').slideToggle(200);
        }
        //Make Reviews account
        if( $clicked.hasClass('cff_make_reviews') ){
        	$('#cff_page_access_token').val( $clicked.closest('.cff_connected_account').attr('data-accesstoken') ).addClass('cff-success');
        }
        //Make primary account
        if( $clicked.hasClass('cff_make_primary') ){
        	var $selected_account = $clicked.closest('.cff_connected_account'),
        		selected_id = $selected_account.attr('data-page-id'),
        		selected_token = $selected_account.attr('data-accesstoken');


        	//Remove ID/token from fields
        	if( $selected_account.hasClass('cff_account_active') ){

        		removePrimaryAcount($selected_account);

	        //Add ID/token to fields
        	} else {

        		//Add as primary account
        		cffLabelAsPrimary($selected_account, true);

	        	//Add ID/token to fields
	        	if( cff_multifeed_enabled ){

	        		//Add ID to existing IDs already in field
	        		var id_sep = ', ',
	        			existing_id = $('#cff_page_id').val().trim(),
	        			existing_token = $('#cff_access_token').val().trim();

	        		if( existing_id == '' ) id_sep = '';
	        		$('#cff_page_id').val( existing_id + id_sep + selected_id ).addClass('cff-success');

	        		//Change to multiple token format
	        		var token_format = '';
	        		if( existing_token !== '' ) token_format += existing_token + ', ';
	        		token_format += selected_id + ':' + selected_token;

	        		$('#cff_access_token').val( token_format ).addClass('cff-success');

	        	} else {

	        		//Replace existing ID and token
	        		$('#cff_page_id').val( selected_id ).addClass('cff-success');
	        		$('#cff_access_token').val( selected_token ).addClass('cff-success');

	        		//Remove active account class from other accounts
	        		$selected_account.siblings().each(function(){
	        			cffLabelAsPrimary($(this));
	        		});

	        	}


        	}
        }
    });
    //Remove account
    $body.on('click', '.cff_delete_account', function(){
        removeConnectedAccount( $(this).closest('.cff_connected_account') );
    });

    //Change button label when adding/removing as primary account
    function cffLabelAsPrimary($account, makePrimary=false){
    	if( makePrimary ){
        	$account.addClass('cff_account_active').find('.cff_make_primary').html('<i class="fa fa-minus-circle" aria-hidden="true"></i>'+cff_remove_primary_text);

        	if( $account.length > 0 ) cffAddCurIdLabel($account.find('.cff_ca_fullname').text(), $account.find('.cff_ca_avatar').attr('src'));

    	} else {
    		$account.removeClass('cff_account_active').find('.cff_make_primary').html('<i class="fa fa-plus-circle" aria-hidden="true"></i>'+cff_add_primary_text);
    	}
    }

    function cffAddCurIdLabel(name, avatar){
    	var account_img = '',
    		account_name = '<span>' + name + '</span>';
    	if( avatar !== undefined ) account_img = '<img src="' + avatar + '" />';

    	$('#cff_primary_account_label').show().html( account_img + account_name );
    }

    //Label a primary account when page is first loaded
    var cff_current_page_id = $('#cff_page_id').val(),
    	cff_current_page_id_arr = [];
    if( typeof cff_current_page_id !== 'undefined' ) var cff_current_page_id_arr = cff_current_page_id.split(',');

    if( cff_current_page_id_arr.length > 1 ){
    	for (var i = 0; i < cff_current_page_id_arr.length; i++) {
		    cffLabelAsPrimary( $('#cff_connected_account_' + cffValidateID( cff_current_page_id_arr[i] ) ), true );
		}
    } else {
    	cffLabelAsPrimary( $('#cff_connected_account_' + cffValidateID( cff_current_page_id ) ), true );
    }
    //Make sure ID is a valid string
    function cffValidateID(id){
    	if( typeof id === 'undefined' || id == '' ) return;

    	//Remove slashes from end
    	id = cffStripURLParts( id.replace(/\/$/, "").trim() );
    	//Only return if it contains numbers/letters
    	if( id.match("^[A-Za-z0-9]+$") ) return id;
    }

	//Show the modal by default, but hide if the "cffnomodal" class is added to prevent it showing after saving settings
	if( location.hash !== '#cffnomodal' ){
		$('.cff_modal_tokens').removeClass('cffnomodal');
	}

	//Switch Page/Group app button in modal
	jQuery("#cff_login_type").on('change', function() {
		if ( jQuery("#cff_login_type option:selected").val() == 'group' ) {
			jQuery('#cff_page_app').hide();
			jQuery('#cff_group_app').css('display', 'inline-block');
		} else {
			jQuery('#cff_page_app').css('display', 'inline-block');
			jQuery('#cff_group_app').hide();
		}
	});

    //Load the admin share widgets
    $('#cff-admin-show-share-links').on('click', function(){
    	$(this).fadeOut();
        if( $('#cff-admin-share-links iframe').length == 0 ) $('#cff-admin-share-links').html('<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://wordpress.org/plugins/custom-facebook-feed/" data-text="Display your Facebook posts on your site your way using the Custom Facebook Feed WordPress plugin!" data-via="smashballoon" data-dnt="true">Tweet</a> <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script> <style type="text/css"> #twitter-widget-0{float: left; width: 82px !important;}.IN-widget{margin-right: 20px;}</style> <div id="fb-root" style="display: none;"></div><script>(function(d, s, id){var js, fjs=d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js=d.createElement(s); js.id=id; js.src="//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.0"; fjs.parentNode.insertBefore(js, fjs);}(document, "script", "facebook-jssdk"));</script> <div class="fb-like" data-href="https://wordpress.org/plugins/custom-facebook-feed/" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true" style="display: block; float: left; margin-right: 5px;"></div><script src="//platform.linkedin.com/in.js" type="text/javascript"> lang: en_US </script> <script type="IN/Share" data-url="https://wordpress.org/plugins/custom-facebook-feed/"></script></div>');

        setTimeout(function(){
        	$('#cff-admin-share-links').addClass('cff-show');
        }, 500);
    });

    //Group app setup screenshot
	jQuery('#cff-group-app-tooltip').on('hover', function(){
	    jQuery('#cff-group-app-screenshot').fadeIn(100);
	}, function(){
		jQuery('#cff-group-app-screenshot').fadeOut(100);
	});

	//Remove any duplicate groups
	jQuery('.cff-group-admin').each(function(){
		jQuery('.cff-groups-list #' + jQuery(this).attr('id') ).eq(1).hide();
	});


	//Show/hide mobile column setting
    var cff_masonry_desktop_col = jQuery('#cff_cols').val(),
		$cff_mobile_col_settings = jQuery('.cff-mobile-col-settings');
	if( typeof cff_post_limit === 'undefined' ) cff_masonry_desktop_col = '1';

	//Should we show anything initially?
	if( cff_masonry_desktop_col == '1' ) $cff_mobile_col_settings.hide();
	if( parseInt(cff_masonry_desktop_col) > 1 ){
		$cff_mobile_col_settings.show();
	}

	jQuery('#cff_cols').on('change', function(){
		cff_cols_num = parseInt( jQuery('#cff_cols').val() );

		if(cff_cols_num > 1){
			$cff_mobile_col_settings.slideDown(200);
		} else {
			$cff_mobile_col_settings.slideUp(200);
		}
	});

	// notices

	if (jQuery('#cff-notice-bar').length) {
		jQuery('#wpadminbar').after(jQuery('#cff-notice-bar'));
		jQuery('#wpcontent').css('padding-left', 0);
		jQuery('#wpbody').css('padding-left', '20px');
		jQuery('#cff-notice-bar').show();
	}

	jQuery('#cff-notice-bar .dismiss').on('click', function(e) {
		e.preventDefault();
		jQuery('#cff-notice-bar').remove();
		jQuery.ajax({
			url: cffA.ajax_url,
			type: 'post',
			data: {
				action : 'cff_lite_dismiss',
				cff_nonce: cffA.cff_nonce
			},
			success: function (data) {
			}
		});
	});

	jQuery('#cff-oembed-disable').on('click', function(e) {
		e.preventDefault();
		jQuery(this).addClass( 'loading' ).html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
		jQuery.ajax({
			url: cffA.ajax_url,
			type: 'post',
			data: {
				action : 'cff_oembed_disable',
				cff_nonce: cffA.cff_nonce
			},
			success: function (data) {
				jQuery('#cff-oembed-disable').closest('p').html(data);
			}
		});

	});

	jQuery('.cff_show_gdpr_list').on('click', function(){
		jQuery(this).closest('div').find('.cff_gdpr_list').slideToggle();
	});
	//Selecting a post style
	jQuery('#cff_gdpr_setting').on('change', function(){
		console.log('Slim Shady')
		cffCheckGdprSetting( jQuery(this).val() );
	});
	function cffCheckGdprSetting(option) {
		if( option == 'yes' ){
			jQuery('.cff_gdpr_yes').show();
			jQuery('.cff_gdpr_no, .cff_gdpr_auto').hide();
		}
		if( option == 'no' ){
			jQuery('.cff_gdpr_no').show();
			jQuery('.cff_gdpr_yes, .cff_gdpr_auto').hide();
		}
		if( option == 'auto' ){
			jQuery('.cff_gdpr_auto').show();
			jQuery('.cff_gdpr_yes, .cff_gdpr_no').hide();
		}
	}
	cffCheckGdprSetting(jQuery('#cff_gdpr_setting').val());

	//sb_instagram_enable_email_report
	function cffToggleEmail() {
		if (jQuery('#cff_enable_email_report').is(':checked')) {
			jQuery('#cff_enable_email_report').closest('td').find('.cff_box').slideDown();
		} else {
			jQuery('#cff_enable_email_report').closest('td').find('.cff_box').slideUp();
		}
	}cffToggleEmail();
	jQuery('#cff_enable_email_report').on('change', cffToggleEmail);
	if (jQuery('#cff-goto').length) {
		jQuery('#cff-goto').closest('tr').addClass('cff-goto');
		$('html, body').animate({
			scrollTop: $('#cff-goto').offset().top - 200
		}, 500);
	}

	jQuery('.cff-error-directions .cff-reconnect').on('click', function(){
		event.preventDefault();
		jQuery('.cff_admin_btn').trigger('click');
	});
	jQuery('.cff-clear-errors-visit-page').on('click', function(event) {
		event.preventDefault();
		var $btn = jQuery(this);
		$btn.prop( 'disabled', true ).addClass( 'loading' ).html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');
		$.ajax({
			url : cffA.ajax_url,
			type : 'post',
			data : {
				action : 'cff_reset_log'
			},
			success : function(data) {
				window.location.href = $btn.attr('data-url');
			},
			error : function(data)  {
				window.location.href = $btn.attr('data-url');
			}
		}); // ajax call
	});

	/* removing padding */
	if (jQuery('#cff-admin-about').length) {
		jQuery('#wpcontent').css('padding', 0);
	}

	$('.cff-opt-in').on('click', function(event) {
		event.preventDefault();

		var $btn = jQuery(this);
		$btn.prop( 'disabled', true ).addClass( 'loading' ).html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');

		cffSubmitOptIn(true);
	}); // clear_comment_cache click

	$('.cff-no-usage-opt-out').on('click', function(event) {
		event.preventDefault();

		var $btn = jQuery(this);
		$btn.prop( 'disabled', true ).addClass( 'loading' ).html('<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>');

		cffSubmitOptIn(false);
	}); // clear_comment_cache click

	function cffSubmitOptIn(choice) {
		$.ajax({
			url : cffA.ajax_url,
			type : 'post',
			data : {
				action : 'cff_usage_opt_in_or_out',
				opted_in: choice,
			},
			success : function(data) {
				$('.cff-no-usage-opt-out').closest('.cff-usage-tracking-notice').fadeOut();
			}
		}); // ajax call
	}

	//Click event for other plugins in menu
    $('.cff_get_sbi, .cff_get_cff, .cff_get_ctf, .cff_get_yt').parent().on('click', function(e){
        e.preventDefault();

		// remove the already opened modal
        jQuery('#cff-op-modals').remove();

		// prepend the modal wrapper
        $('#wpbody-content').prepend('<div class="cff-fb-source-ctn sb-fs-boss cff-fb-center-boss" id="cff-op-modals"><i class="fa fa-spinner fa-spin cff-loader" aria-hidden="true"></i></div>');

		// determine the plugin name
        var $self = $(this).find('span'),
            sb_get_plugin = 'twitter';

        if( $self.hasClass('cff_get_cff') ){
            sb_get_plugin = 'facebook';
        } else if( $self.hasClass('cff_get_sbi') ){
            sb_get_plugin = 'instagram';
        } else if( $self.hasClass('cff_get_yt') ){
            sb_get_plugin = 'youtube';
        }

		// send the ajax request to load plugin name and others data
		$.ajax({
			url : cffA.ajax_url,
			type : 'post',
			data : {
				action : 'sb_other_plugins_modal',
				plugin : sb_get_plugin,
				cff_nonce : cffA.cff_nonce,

			},
			success : function(data) {
				if ( data.success == true ) {
					$('#cff-op-modals').html(data.data.output);
				}
			},
			error : function(e)  {
				console.log(e);
			}
		});
    });

	/**
	 * Install other plugin on modal
	 *
	 * @since 4.0
	 */
	$(document).on('click', '#cff_install_op_btn', function() {
		let self = $(this);
		let pluginAtts = self.data('plugin-atts');
		if ( pluginAtts.step == 'install' ) {
			pluginAtts.plugin = pluginAtts.download_plugin
		}
		let loader = '<span class="cff-btn-spinner"><svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z"><animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"></animateTransform></path></svg></span>';
		self.prepend(loader);

		// send the ajax request to install or activate the plugin
		$.ajax({
			url : cffA.ajax_url,
			type : 'post',
			data : {
				action : pluginAtts.action,
				nonce : pluginAtts.nonce,
				plugin : pluginAtts.plugin,
				download_plugin : pluginAtts.download_plugin,
				type : 'plugin',
			},
			success : function(data) {
				if ( data.success == true ) {
					self.find('.cff-btn-spinner').remove();
					self.attr('disabled', 'disabled');

					if ( pluginAtts.step == 'install' ) {
						self.html( data.data.msg );
					} else {
						self.html( data.data );
					}
				}
			},
			error : function(e)  {
				console.log(e);
			}
		});
	});

	jQuery('body').on('click', '#cff_review_consent_yes', function(e) {
		let reviewStep1 = jQuery('.cff_review_notice_step_1, .cff_review_step1_notice');
		let reviewStep2 = jQuery('.cff_notice.cff_review_notice, .rn_step_2');

		reviewStep1.hide();
		reviewStep2.show();

        $.ajax({
            url : cffA.ajax_url,
            type : 'post',
            data : {
                action : 'cff_review_notice_consent_update',
				consent : 'yes'
            },
            success : function(data) {
            }
        }); // ajax call

	});

	jQuery('body').on('click', '#cff_review_consent_no', function(e) {
		let reviewStep1 = jQuery('.cff_review_notice_step_1, #cff-notifications');
		reviewStep1.hide();

        $.ajax({
            url : cffA.ajax_url,
            type : 'post',
            data : {
                action : 'cff_review_notice_consent_update',
				consent : 'no'
            },
            success : function(data) {
            }
        }); // ajax call

	});

    //Close the modal if clicking anywhere outside it
    jQuery('body').on('click', '#cff-op-modals', function(e){
        if (e.target !== this) return;
        jQuery('#cff-op-modals').remove();
    });
    jQuery('body').on('click', '.cff-fb-popup-cls', function(e){
        jQuery('#cff-op-modals').remove();
    });

    //Add class to Pro menu item
    $('.cff_get_pro').parent().attr({'class':'cff_get_pro_highlight', 'target':'_blank'});

    // Locator
    jQuery('.cff-locator-more').click(function(e) {
        e.preventDefault();
        jQuery(this).closest('td').find('.cff-full-wrap').show();
        jQuery(this).closest('td').find('.cff-condensed-wrap').hide();
        jQuery(this).remove();
    });


    //cff_reset_log
    var $cffClearLog = $('#cff_reset_log');

    $cffClearLog.on('click', function(event) {
        event.preventDefault();

        jQuery('#cff-clear-cache-success').remove();
        jQuery(this).prop("disabled",true);

        $.ajax({
            url : cffA.ajax_url,
            type : 'post',
            data : {
                action : 'cff_clear_error_log'
            },
            success : function(data) {
                $cffClearLog.prop('disabled',false);
                if(data=='1') {
                    $cffClearLog.after('<i id="cff-clear-cache-success" class="fa fa-check-circle cff-success"></i>');
                } else {
                    $cffClearLog.after('<span>error</span>');
                }
            }
        }); // ajax call
    }); // clear_error_log click

});



/* global smash_admin, jconfirm, wpCookies, Choices, List */

(function($) {

	'use strict';

	// Global settings access.
	var s;

	// Admin object.
	var SmashCFFAdmin = {

		// Settings.
		settings: {
			iconActivate: '<i class="fa fa-toggle-on fa-flip-horizontal" aria-hidden="true"></i>',
			iconDeactivate: '<i class="fa fa-toggle-on" aria-hidden="true"></i>',
			iconInstall: '<i class="fa fa-cloud-download" aria-hidden="true"></i>',
			iconSpinner: '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>',
			mediaFrame: false
		},

		/**
		 * Start the engine.
		 *
		 * @since 1.3.9
		 */
		init: function() {

			// Settings shortcut.
			s = this.settings;

			// Document ready.
			$( document ).ready( SmashCFFAdmin.ready );

			// Addons List.
			SmashCFFAdmin.initAddons();
		},

		/**
		 * Document ready.
		 *
		 * @since 1.3.9
		 */
		ready: function() {

			// Action available for each binding.
			$( document ).trigger( 'smashReady' );
		},

		//--------------------------------------------------------------------//
		// Addons List.
		//--------------------------------------------------------------------//

		/**
		 * Element bindings for Addons List page.
		 *
		 * @since 1.3.9
		 */
		initAddons: function() {

			// Some actions have to be delayed to document.ready.
			$( document ).on( 'smashReady', function() {

				// Only run on the addons page.
				if ( ! $( '#cff-admin-addons' ).length ) {
					return;
				}

				// Display all addon boxes as the same height.
                if( $( '#cff-admin-about.cff-admin-wrap').length ){
                    $( '#cff-admin-about .addon-item .details' ).matchHeight( { byrow: false, property: 'height' } );
                }

				// Addons searching.
				if ( $('#cff-admin-addons-list').length ) {
					var addonSearch = new List( 'cff-admin-addons-list', {
						valueNames: [ 'addon-name' ]
					} );

					$( '#cff-admin-addons-search' ).on( 'keyup', function () {
						var searchTerm = $( this ).val(),
							$heading = $( '#addons-heading' );

						if ( searchTerm ) {
							$heading.text( cff_admin.addon_search );
						}
						else {
							$heading.text( $heading.data( 'text' ) );
						}

						addonSearch.search( searchTerm );
					} );
				}
			});

			// Toggle an addon state.
			$( document ).on( 'click', '#cff-admin-addons .addon-item button, .cff-notice-admin-btn', function( event ) {

				event.preventDefault();

				if ( $( this ).hasClass( 'disabled' ) ) {
					return false;
				}

				SmashCFFAdmin.addonToggle( $( this ) );
			});
		},

		/**
		 * Toggle addon state.
		 *
		 * @since 1.3.9
		 */
		addonToggle: function( $btn ) {

			var $addon = $btn.closest( '.addon-item' ),
				plugin = $btn.attr( 'data-plugin' ),
				plugin_type = $btn.attr( 'data-type' ),
				action,
				cssClass,
				statusText,
				buttonText,
				errorText,
				successText;

			if ( $btn.hasClass( 'status-go-to-url' ) ) {
				// Open url in new tab.
				window.open( $btn.attr('data-plugin'), '_blank' );
				return;
			}

			$btn.prop( 'disabled', true ).addClass( 'loading' );
			$btn.html( s.iconSpinner );

			if ( $btn.hasClass( 'status-active' ) ) {
				// Deactivate.
				action     = 'cff_deactivate_addon';
				cssClass   = 'status-inactive';
				if ( plugin_type === 'plugin' ) {
					cssClass += ' button button-secondary';
				}
				statusText = cff_admin.addon_inactive;
				buttonText = cff_admin.addon_activate;
				if ( plugin_type === 'addon' ) {
					buttonText = s.iconActivate + buttonText;
				}
				errorText  = s.iconDeactivate + cff_admin.addon_deactivate;

			} else if ( $btn.hasClass( 'status-inactive' ) ) {
				// Activate.
				action     = 'cff_activate_addon';
				cssClass   = 'status-active';
				if ( plugin_type === 'plugin' ) {
					cssClass += ' button button-secondary disabled';
				}
				statusText = cff_admin.addon_active;
				buttonText = cff_admin.addon_deactivate;
				if ( plugin_type === 'addon' ) {
					buttonText = s.iconDeactivate + buttonText;
				} else if ( plugin_type === 'plugin' ) {
					buttonText = cff_admin.addon_activated;
				}
				errorText  = s.iconActivate + cff_admin.addon_activate;

			} else if ( $btn.hasClass( 'status-download' ) ) {
				// Install & Activate.
				action   = 'cff_install_addon';
				cssClass = 'status-active';
				if ( plugin_type === 'plugin' ) {
					cssClass += ' button disabled';
				}
				statusText = cff_admin.addon_active;
				buttonText = cff_admin.addon_activated;
				if ( plugin_type === 'addon' ) {
					buttonText = s.iconActivate + cff_admin.addon_deactivate;
				}
				errorText = s.iconInstall + cff_admin.addon_activate;

			} else {
				return;
			}

			var data = {
				action: action,
				nonce : cff_admin.nonce,
				plugin: plugin,
				type  : plugin_type
			};
			$.post( cff_admin.ajax_url, data, function( res ) {
				console.log(res)
				if ( res.success ) {
					if ( 'cff_install_addon' === action ) {
						$btn.attr( 'data-plugin', res.data.basename );
						successText = res.data.msg;
						if ( ! res.data.is_activated ) {
							cssClass = 'status-inactive';
							if ( plugin_type === 'plugin' ) {
								cssClass = 'button';
							}
							statusText = cff_admin.addon_inactive;
							buttonText = s.iconActivate + cff_admin.addon_activate;
						}
					} else {
						successText = res.data;
					}
					$addon.find( '.actions' ).append( '<div class="msg success">'+successText+'</div>' );
					$addon.find( 'span.status-label' )
						.removeClass( 'status-active status-inactive status-download' )
						.addClass( cssClass )
						.removeClass( 'button button-primary button-secondary disabled' )
						.text( statusText );
					$btn
						.removeClass( 'status-active status-inactive status-download' )
						.removeClass( 'button button-primary button-secondary disabled' )
						.addClass( cssClass ).html( buttonText );
				} else {
					if ( 'download_failed' === res.data[0].code ) {
						if ( plugin_type === 'addon' ) {
							$addon.find( '.actions' ).append( '<div class="msg error">'+cff_admin.addon_error+'</div>' );
						} else {
							$addon.find( '.actions' ).append( '<div class="msg error">'+cff_admin.plugin_error+'</div>' );
						}
					} else {
						$addon.find( '.actions' ).append( '<div class="msg error">'+res.data+'</div>' );
					}
					$btn.html( errorText );
				}

				$btn.prop( 'disabled', false ).removeClass( 'loading' );

				// Automatically clear addon messages after 3 seconds.
				setTimeout( function() {
					$( '.addon-item .msg' ).remove();
				}, 3000 );

			}).fail( function( xhr ) {
				console.log( xhr.responseText );
			});
		},

	};

	SmashCFFAdmin.init();

	window.SmashCFFAdmin = SmashCFFAdmin;

})( jQuery );