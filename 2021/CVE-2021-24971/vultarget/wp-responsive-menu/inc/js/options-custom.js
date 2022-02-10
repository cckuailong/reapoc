/**
 * Custom scripts needed for the colorpicker, image button selectors,
 * and navigation tabs.
 */

jQuery( document ).ready( function( $ ) {

	// Loads the color pickers
	$( '.of-color' ).wpColorPicker();

	$('[data-toggle="tooltip"]').tooltip();

	$('.wprmenu-hide-menu-pages').select2();

	// Image Options
	$( '.of-radio-img-img' ).click( function(){
		$( this ).parent().parent().find( '.of-radio-img-img' ).removeClass( 'of-radio-img-selected' );
		$( this ).addClass( 'of-radio-img-selected' );
	} );

	$( '.of-radio-img-label' ).hide();
	$( '.of-radio-img-img' ).show();
	$( '.of-radio-img-radio' ).hide();

	var ProHtml = '<div class="wpr-pro-block"><span><a target="_blank" href="http://magnigenie.com/downloads/wp-responsive-menu-pro/">Upgrade to PRO to use this option</a></span></div>';

	$('#wpr_optionsframework .pro-feature').append(ProHtml);

	$('.pro-feature').hover(function() {
		$(this).find('.wpr-pro-block').toggleClass('show');
	});
	

	$('#wpr-sortable').sortable({
  	update: function(event, ui) {
      var order = []; 
      $('#wpr-sortable li').each( function(e) {
      	order.push( $(this).attr('id'));
      });
      $('#wpr_optionsframework').find('input#order_menu_items').val(order);
     }
  });

	$("#wpr-sortable").disableSelection();

	$("ul#wpr-sortable li#Social").append('<span class="pro-ftr">[ Available In Pro ]</span>');


	// Loads tabbed sections if they exist
	if (  $( '.nav-tab-wrapper' ).length > 0  ) {
		options_framework_tabs();
	}

	function options_framework_tabs() {

		var $group = $( '.group' ),
			$navtabs = $( '.nav-tab-wrapper a' ),
			active_tab = '';

		// Hides all the .group sections to start
		$group.hide();

		// Find if a selected tab is saved in localStorage
		if (  typeof( localStorage ) != 'undefined'  ) {
			active_tab = localStorage.getItem( 'active_tab' );
		}

		// If active tab is saved and exists, load it's .group
		if (  active_tab != '' && $( active_tab ).length  ) {
			$( active_tab ).fadeIn();
			$( active_tab + '-tab' ).addClass( 'nav-tab-active' );
		} else {
			$( '.group:first' ).fadeIn();
			$( '.nav-tab-wrapper a:first' ).addClass( 'nav-tab-active' );
		}

		// Bind tabs clicks
		$navtabs.click( function( e ) {

			e.preventDefault();

			// Remove active class from all tabs
			$navtabs.removeClass( 'nav-tab-active' );

			$( this ).addClass( 'nav-tab-active' ).blur();

			if ( typeof( localStorage ) != 'undefined'  ) {
				localStorage.setItem( 'active_tab', $( this ).attr( 'href' )  );
			}

			var selected = $( this ).attr( 'href' );

			$group.hide();
			$( selected ).fadeIn();

		} );
	}


	var slideOpt = $( '#section-slide_type option:selected' ).val();
	if (  slideOpt == 'bodyslide' ) {
		$( '#section-position option:eq( 2 )' ).css(  'display', 'none'  );
		$( '#section-position option:eq( 3 )' ).css(  'display', 'none'  );
	}

	$( '#slide_type' ).change( function() {
		if (  $( this ).val() == 'bodyslide' ) {
			$( '#section-position option:eq( 2 )' ).css(  'display', 'none'  );
			$( '#section-position option:eq( 3 )' ).css(  'display', 'none'  );
		}
		else {
			$( '#section-position option:eq( 2 )' ).css(  'display', 'block'  );
			$( '#section-position option:eq( 3 )' ).css(  'display', 'block'  );			
		}
	} )

  var menutype = $( "input[name='wprmenu_options[menu_type]']:checked" ).val();
	if (  menutype == 'default' ) {
		$( '#section-custom_menu_top' ).css(  'display', 'none' );
		$( '#section-custom_menu_left' ).css(  'display', 'none' );
		$( '#section-custom_menu_bg_color' ).css(  'display', 'none' );   			
	}
  

  $( '#section-menu_type input' ).on( 'change', function() {
  	var menuType = $( 'input[name="wprmenu_options[menu_type]"]:checked', '#section-menu_type' ).val(); 
   	
   	if (  menuType == 'default' ) {
   		$( '#section-custom_menu_top' ).css(  'display', 'none' );
   		$( '#section-custom_menu_left' ).css(  'display', 'none' );
   		$( '#section-custom_menu_bg_color' ).css(  'display', 'none' );    			
   	}
   	else {
   		$( '#section-custom_menu_top' ).css(  'display', 'block' );
   		$( '#section-custom_menu_left' ).css(  'display', 'block' );
   		$( '#section-custom_menu_bg_color' ).css(  'display', 'block' );    			   			
   	}
	});

	//Live Preview Opts
	$('body').on('click', '.live-preview-badge', function() {
		$(this).toggleClass('expand');
		var IFrame = $(this).parents('.queries-holder.live-preview').find('iframe#wpr_iframe');
		IFrame.contents().find('#wpadminbar').remove();
		IFrame.contents().find('#wprmenu_bar').css('top','0px');
		IFrame.contents().find('body').removeClass("admin-bar ");


		$('div.live-preview-container').toggleClass('disable');

		if( ! $('div.live-preview-container').hasClass('disable') ) {
			$('div.live-preview-container').animate({ "right": "3px" }, "speed" );
			$('div.live-preview-badge').css('position','relative');
			$('div.live-preview-badge').css('top','307');
			$('div.live-preview-badge').animate({ "right": "292" }, "speed" );
			$(this).parent('.queries-holder.live-preview').find('.live-preview-badge').text('Hide Live Preview');
		}
		else {
			$('div.live-preview-badge').css('position','fixed');
			$('div.live-preview-badge').css('top','307');
			$('div.live-preview-badge').animate({ "right": "-66px" }, "speed" );
			$('div.live-preview-container').animate({ "right": "-400px" }, "speed" );
			$(this).parent('.queries-holder.live-preview').find('.live-preview-badge').text('Show Live Preview');
		}

		if( $(this).hasClass('expand') ) {
			$('html, body').animate({
        scrollTop: $("#wpr_optionsframework-metabox").offset().top-60
    	}, 1000);
		}
	});

	//Demo import section
	$('body').on('click', '.wprmenu-data.import-demo', function(e) {
		e.preventDefault();
		var SelectedButton = $(this);
		var SelectedButtonText = $(this).text();
		var SelectedNode = $(this).parents('.wprmenu-content').find('.wprmenu-content-image');
		var DemoType = SelectedNode.attr('data-demo-type');
		var DemoId = SelectedNode.attr('data-demo-id');
		var SettingsId = SelectedNode.attr('data-settings');

		if( SelectedNode.hasClass('free-version') ) {
			Swal({
				type  : 'info',
				title : wprOption.pro_version_text,
				text  : wprOption.pro_version_upgrade_error,
			});
			return;
		}

		if( SettingsId !== '' 
			&& DemoType !== '' 
			&& DemoId !== '' ) {
			SelectedButton.text(wprOption.please_wait);
			
			$.ajax({
				type 	  : 'POST',
				url  		: wprOption.ajax_url,
				nonce 		: wprOption.nonce,
				data    : 'settings_id='+ SettingsId + '&demo_id=' + DemoId + '&demo_type=' + DemoType + '&action=wprmenu_import_data',
				success	: function(response) {
					response = $.parseJSON(response);

					if( response.status == 'success' ) {
						wprmenu_setCookie('wprmenu_live_preview', '', 1);
						Swal({
							type  : 'success',
							title : wprOption.import_done,
							text  : wprOption.please_reload,
						}).then(function () {
            	location.reload();
        		}).catch(swal.noop);
						
						SelectedButton.text(wprOption.import_done);
					}
					else {
						Swal({
							type  : 'error',
							title : wprOption.import_error_title,
							text  : wprOption.import_error,
						});
						SelectedButton.text(wprOption.import_error);
					}
				}
			});
		}
	});

	$('.wprmenu-showcase-wrapper').find('li.wprmenu-data-list').hover(function() {
		$(this).find('.wprmenu-content').toggleClass('overlay');
		$(this).find('.wprmenu-content').toggleClass('image-overlay');
	});

	//Get Live Preview
	$('body').on('click', '.wpr-load-priv', function() {
		
		var Selected = $(this);
		var OldText = Selected.text();
		Selected.text(wprOption.loading_preview);
		
		var wpr_data = $('div#wpr_optionsframework form').serialize();

		$('div.smartphone-content').find('.overlay').removeClass('hide');
		$('div.smartphone-content').find('.overlay').addClass('show');

		$.ajax({
			type 	  : 'POST',
			url  		: wprOption.ajax_url,
			nonce 		: wprOption.nonce,
			data    : wpr_data + '&action=wpr_live_update',
			success	: function(response) {
				
				wprmenu_setCookie('wprmenu_live_preview', 'yes', 1);
				
				$('div.smartphone-content iframe').attr('src', wprOption.site_url);

				setTimeout(function() {
					$('div.smartphone-content iframe').contents().find('#wpadminbar').remove();
					$('div.smartphone-content iframe').contents().find('#wprmenu_bar').css('top','0px');
					$('div.smartphone-content iframe').contents().find('body').removeClass('admin-bar');
          $('div.smartphone-content').find('.overlay').removeClass('show');
					$('div.smartphone-content').find('.overlay').addClass('hide');
        }, 2000);

        Selected.text(wprOption.preview_done);
        Selected.text(OldText);
			}
		});
	});

	// Set Cookie
	function wprmenu_setCookie(cname, cvalue, exdays) {
  	var d = new Date();
  	d.setTime(d.getTime() + (exdays*24*60*60*1000));
  	var expires = "expires="+d.toUTCString();
  	document.cookie = cname + "=" + cvalue + "; " + expires + ";path=/";
	}

	// Get Cookie
	function wprmenu_getCookie(cname) {
  	var name = cname + "=";
  	var ca = document.cookie.split(';');
  	for(var i=0; i<ca.length; i++) {
    	var c = ca[i];
    	while (c.charAt(0)==' ') c = c.substring(1);
    	if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
  	}
  	return "";
	}

	//Add ACE to Editor
	var container = jQuery('#wpr_custom_css');
	container.width( container.parent().width() ).height( 200 );

	var editor = ace.edit( "wpr_custom_css" );
	
	container.css('width', 'auto');
	editor.setValue(container.siblings('textarea').val());
	editor.setTheme("ace/theme/chrome");
	editor.getSession().setMode('ace/mode/css');
	editor.setShowPrintMargin(false);
	editor.setHighlightActiveLine(false);
	editor.gotoLine(1);
	editor.session.setUseWorker(false);


	editor.getSession().on('change', function(e) {
		$(editor.container).siblings('textarea').val(editor.getValue());
	});


  function createIconpicker() {
		var iconPicker = $('.wpr-icon-picker').fontIconPicker({
				theme: 'fip-bootstrap'
			}),icomoon_json_icons = [],
			icomoon_json_search = [];
			// Get the JSON file
			$.ajax({
				url: wprOption.options_path + '/icons/selection.json',
				type: 'GET',
				dataType: 'json'
			})
			.done(function(response) {
			// Get the class prefix
			var classPrefix = response.preferences.fontPref.prefix;
			
			$.each(response.icons, function(i, v) {
				// Set the source
				icomoon_json_icons.push( classPrefix + v.properties.name );
	
				// Create and set the search source
				if ( v.icon && v.icon.tags && v.icon.tags.length ) {
					icomoon_json_search.push( v.properties.name + ' ' + v.icon.tags.join(' ') );
				} else {
					icomoon_json_search.push( v.properties.name );
				}
			});
		
			setTimeout(function() {
				// Set new fonts
				iconPicker.setIcons(icomoon_json_icons, icomoon_json_search);
				
			}, 1000);
		})
		.fail(function() {
			// Show error message and enable
			alert('Failed to load the icons, Please check file permission.');
		});
	}
	createIconpicker();

	$.exitIntent('enable');
	
	$(document).bind('exitintent', function() {
		$check_cookie = wprmenu_getCookie('wprmenu_live_preview');

		if( $check_cookie !== 'yes' )
			return;
		
		const swalWithBootstrapButtons = Swal.mixin({
  		confirmButtonClass: 'btn btn-primary',
  		cancelButtonClass: 'btn btn-secondary',
  		buttonsStyling: false,
		})

		swalWithBootstrapButtons({
  		title: '<strong>'+wprOption.navigating_away+'</strong>',
  		type: 'info',
  		html: wprOption.confirm_message,
  		showCloseButton: true,
  		showCancelButton: true,
  		focusConfirm: false,
  		confirmButtonText:'Save Changes',
  		cancelButtonText: 'Don\'t Save Changes'
		}).then((result) => {
  		if (result.value) {
    		$.ajax({
					type 	  : 'POST',
					url  		: wprOption.ajax_url,
					nonce 		: wprOption.nonce,
					data    : 'action=wpr_get_transient_from_data',
					success	: function(response) {
						response = $.parseJSON(response);

						if( response.status == 'success' ) {
							wprmenu_setCookie('wprmenu_live_preview', '', 1);

							swalWithBootstrapButtons(
      					'Options Saved!',
      					'The options has been saved. Please reload this page by doing click on the button below. ',
      					'success'
    					).then(function () {
            	location.reload();
        		}).catch(swal.noop);
						}
						else {
							Swal({
								type  : 'error',
								title : wprOption.import_error_title,
								text  : wprOption.import_error,
							});
						}
					}
				});
  		} else if (
    		result.dismiss === Swal.DismissReason.cancel
  		) {
    	swalWithBootstrapButtons(
      	'Options not saved',
      	'The recent changes are reverted back',
      	'error'
    	)
  	}
		})
	});

	//Hide top and bottom options when push menu is activated
	var MenuSlideStyle = $( "input[name='wprmenu_options[slide_type]']:checked" ).val();
	if( MenuSlideStyle == 'bodyslide' ) {
		$( 'label[for="wprmenu_options-position-top"]' ).css(  'display', 'none' );
		$( 'label[for="wprmenu_options-position-bottom"]' ).css(  'display', 'none' );
	}
	
	$( '#section-slide_type input' ).on( 'change', function() {
  	var SlideType = $( 'input[name="wprmenu_options[slide_type]"]:checked' ).val(); 
   	
   	if (  SlideType == 'bodyslide' ) {
   		$( 'label[for="wprmenu_options-position-top"]' ).css(  'display', 'none' );
			$( 'label[for="wprmenu_options-position-bottom"]' ).css(  'display', 'none' );   			
   	}
   	else {
   		$( 'label[for="wprmenu_options-position-top"]' ).css(  'display', 'block' );
			$( 'label[for="wprmenu_options-position-bottom"]' ).css(  'display', 'block' );  			   			
   	}
	});

});