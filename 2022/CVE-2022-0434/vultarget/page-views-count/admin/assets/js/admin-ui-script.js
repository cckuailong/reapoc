(function($) {
	$(document).ready(function() {

		/* Apply wp color picker */
		$('.a3rev_panel_container .a3rev-color-picker').each(function(i){
			$(this).wpColorPicker({
				change: function( event, ui ) {
					//bgImage.css('background-color', ui.color.toString());
				},
				clear: function() {
					//bgImage.css('background-color', '');
				}
			});
		});

		/* Apply UI slider */
		$('.a3rev_panel_container div.a3rev-ui-slide').each(function(i){

			if( $(this).attr('min') != undefined && $(this).attr('max') != undefined ) {

				$(this).slider( {
								isRTL: true,
								range: "min",
								min: parseInt($(this).attr('min')),
								max: parseInt($(this).attr('max')),
								value: parseInt($(this).parent('.a3rev-ui-slide-container-end').parent('.a3rev-ui-slide-container-start').next(".a3rev-ui-slide-result-container").children("input").val()),
								step: parseInt($(this).attr('inc')) ,
								slide: function( event, ui ) {
									$( this ).parent('.a3rev-ui-slide-container-end').parent('.a3rev-ui-slide-container-start').next(".a3rev-ui-slide-result-container").children("input").val(ui.value);
								}
							});

				$(this).removeAttr('min').removeAttr('max').removeAttr('inc');

			}

		});

		/* Apply Box Shadow */
		$('.a3rev_panel_container input.a3rev-ui-box_shadow-enable').each(function(i){
			if ( $(this).is(':checked') ) {
				$(this).parent('.forminp-box_shadow').find('.a3rev-ui-box_shadow-enable-container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			} else {
				$(this).parent('.forminp-box_shadow').find('.a3rev-ui-box_shadow-enable-container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
			}
			$(this).on( "a3rev-ui-onoff_checkbox-switch", function( event, value, status ) {
				if ( status == 'true') {
					$(this).parents('.forminp-box_shadow').find('.a3rev-ui-box_shadow-enable-container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} ).slideDown();
				} else {
					$(this).parents('.forminp-box_shadow').find('.a3rev-ui-box_shadow-enable-container').show().css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} ).slideUp();
				}
			});
		});

		/* Apply Background Color */
		$('.a3rev_panel_container input.a3rev-ui-bg_color-enable').each(function(i){
			if ( $(this).is(':checked') ) {
				$(this).parent('.forminp-bg_color').find('.a3rev-ui-bg_color-enable-container').css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			} else {
				$(this).parent('.forminp-bg_color').find('.a3rev-ui-bg_color-enable-container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} );
			}
			$(this).on( "a3rev-ui-onoff_checkbox-switch", function( event, value, status ) {
				if ( status == 'true') {
					$(this).parents('.forminp-bg_color').find('.a3rev-ui-bg_color-enable-container').hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} ).slideDown();
				} else {
					$(this).parents('.forminp-bg_color').find('.a3rev-ui-bg_color-enable-container').show().css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden'} ).slideUp();
				}
			});
		});

		/* Apply OnOff Checbox */
		$('.a3rev_panel_container input.a3rev-ui-onoff_checkbox').each(function(i){
			var checked_label = 'ON';
			var unchecked_label = 'OFF';
			var callback = "maincheck";

			if( $(this).attr('checked_label') != undefined ) checked_label = $(this).attr('checked_label');
			if( $(this).attr('unchecked_label') != undefined ) unchecked_label = $(this).attr('unchecked_label');
			if( $(this).attr('callback') != undefined ) callback = $(this).attr('callback');
			var input_name = $(this).attr('name');

			/* Apply for Border Corner */
			if ( $(this).prop('checked') ) {
				$(this).parents('.a3rev-ui-settings-control').find('.a3rev-ui-border-corner-value-container').css( {'display': 'block'} );
			} else {
				$(this).parents('.a3rev-ui-settings-control').find('.a3rev-ui-border-corner-value-container').css( {'display': 'none'} );
			}

			$(this).iphoneStyle({
								/*resizeContainer: false,*/
								resizeHandle: false,
								handleMargin: 10,
								handleRadius: 5,
								containerRadius: 0,
								checkedLabel: checked_label,
								uncheckedLabel: unchecked_label,
								onChange: function(elem, value) {
										var status = value.toString();
										if ( status == 'true' ) {
											/* Apply for Border Corner */
											elem.parents('.a3rev-ui-settings-control').find('.a3rev-ui-border-corner-value-container').slideDown();

											/* Apply for Google API Key */
											elem.parents('.forminp-google_api_key').find('.a3rev-ui-google-api-key-container').slideDown();

											/* Apply for Google Map API Key */
											elem.parents('.forminp-google_map_api_key').find('.a3rev-ui-google-api-key-container').slideDown();
										} else {
											/* Apply for Border Corner */
											elem.parents('.a3rev-ui-settings-control').find('.a3rev-ui-border-corner-value-container').slideUp();

											/* Apply for Google API Key */
											elem.parents('.forminp-google_api_key').find('.a3rev-ui-google-api-key-container').slideUp();

											/* Apply for Google Map API Key */
											elem.parents('.forminp-google_map_api_key').find('.a3rev-ui-google-api-key-container').slideUp();
										}

										$('input[name="' + input_name + '"]').trigger("a3rev-ui-onoff_checkbox-switch", [elem.val(), status]);
									},
								onEnd: function(elem, value) {
										var status = value.toString();

										$('input[name="' + input_name + '"]').trigger("a3rev-ui-onoff_checkbox-switch-end", [elem.val(), status]);
									}
								});
		});

		/* Apply OnOff Radio */
		$('.a3rev_panel_container input.a3rev-ui-onoff_radio').each(function(i){
			var checked_label = 'ON';
			var unchecked_label = 'OFF';

			if( $(this).attr('checked_label') != undefined ) checked_label = $(this).attr('checked_label');
			if( $(this).attr('unchecked_label') != undefined ) unchecked_label = $(this).attr('unchecked_label');
			var input_name = $(this).attr('name');
			var current_item = $(this);

			$(this).iphoneStyle({
								/*resizeContainer: false,*/
								resizeHandle: false,
								handleMargin: 10,
								handleRadius: 5,
								containerRadius: 0,
								checkedLabel: checked_label,
								uncheckedLabel: unchecked_label,
								onChange: function(elem, value) {
										var status = value.toString();
										if ( status == 'true') {
											$('input[name="' + input_name + '"]').not(current_item).prop('checked',false).removeAttr('checkbox-disabled').iphoneStyle("refresh");
										}
										$('input[name="' + input_name + '"]').trigger("a3rev-ui-onoff_radio-switch", [elem.val(), status]);
									},
								onEnd: function(elem, value) {
										var status = value.toString();
										if ( status == 'true') {
											$('input[name="' + input_name + '"]').not(current_item).removeAttr('checkbox-disabled');
											$(current_item).attr('checkbox-disabled', 'true');
										}
										$('input[name="' + input_name + '"]').trigger("a3rev-ui-onoff_radio-switch-end", [elem.val(), status]);
									}
								});
		});

		/* Apply for normal checkbox */
		$('.a3rev_panel_container .hide_options_if_checked').each(function(){

			$(this).find('input').eq(0).on( 'change', function() {

				if ($(this).is(':checked')) {
					$(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').hide();
				} else {
					$(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').show();
				}

			}).trigger('change');

		});
		$('.a3rev_panel_container .show_options_if_checked').each(function(){

			$(this).find('input').eq(0).on( 'change', function() {

				if ($(this).is(':checked')) {
					$(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').show();
				} else {
					$(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').hide();
				}

			}).trigger('change');

		});

		/* Apply chosen script for dropdown */
		$(".a3rev_panel_container .chzn-select").chosen();
		$(".a3rev_panel_container .chzn-select-deselect").chosen({ allow_single_deselect:true, search_contains: true });
		$(".chzn-select-ajaxify").each( function(){
			chosen_ajaxify($(this).attr('id'), $(this).attr('options_url'));
		});

		/* Apply help tip script */
		$('.a3rev_panel_container .help_tip').popover({ html: true, placement: 'bottom' });

		/* Apply Time Picker */
		$('.a3rev_panel_container input.a3rev-ui-time_picker').each(function(i){
			current_value = $(this).val();
			step = 60;
			time_min = false;
			time_max = false;
			time_allow = [];

			if( typeof $(this).data('time_step') != undefined ) {
				step = $(this).data('time_step');
			}
			if( typeof $(this).data('time_min') != undefined ) {
				time_min = $(this).data('time_min');
			}
			if( typeof $(this).data('time_max') != undefined ) {
				time_max = $(this).data('time_max');
			}
			if( typeof $(this).data('time_allow') != undefined ) {
				time_allow = $(this).data('time_allow');
			}

			$(this).datetimepicker({
				datepicker: false,
				format: 'H:i',
				value: current_value,
				step: step,
				minTime: time_min,
				maxTime: time_max,
				allowTimes: time_allow
			});

		});

		/* Apply Sub tab selected script */
		$('div.a3_subsubsub_section ul.subsubsub li a').eq(0).addClass('current');
		$('div.a3_subsubsub_section .section').slice(1).hide();
		$('div.a3_subsubsub_section ul.subsubsub li a').slice(1).each(function(){
			if( $(this).attr('class') == 'current') {
				$('div.a3_subsubsub_section ul.subsubsub li a').removeClass('current');
				$(this).addClass('current');
				$('div.a3_subsubsub_section .section').hide();
				$('div.a3_subsubsub_section ' + $(this).attr('href') ).show();
			}
		});
		$('div.a3_subsubsub_section ul.subsubsub li a').on( 'click', function(){
			var clicked = $(this);
			var section = clicked.closest('.a3_subsubsub_section');
			var target  = clicked.attr('href');

			section.find('a').removeClass('current');

			if ( section.find('.section:visible').length > 0 ) {
				section.find('.section:visible').fadeOut( 100, function() {
					section.find( target ).fadeIn('fast');
				});
			} else {
				section.find( target ).fadeIn('fast');
			}

			clicked.addClass('current');
			$('.last_tab').val( target );

			return false;
		});

		$('.a3rev_panel_container').each( function(i){
			$(this).css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
		});

		$('.a3rev_panel_box_inside').each( function(i){
			if ( $(this).hasClass('box_open') ) {
				$(this).css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			} else {
				$(this).hide().css( {'visibility': 'visible', 'height' : 'auto', 'overflow' : 'inherit'} );
			}
		});

		/* Apply Setting box open & close */
		$(document).on('click', '.a3-plugin-ui-panel-box', function(){
			var box_handle = $(this).parent('.a3rev_panel_box_handle');
			var box_id     = box_handle.data('box-id');
			var form_key   = box_handle.data('form-key');

			var box_data = {};
			if ( typeof a3_admin_ui_script_params != 'undefined' ) {
				var box_data = {
					action:		a3_admin_ui_script_params.plugin + '_a3_admin_ui_event',
					type: 		'open_close_panel_box',
					form_key: 	form_key,
					box_id: 	box_id,
					is_open: 	0,
					security:	a3_admin_ui_script_params.security
				};
			}

			if( $(this).hasClass('box_open') ) {
				box_data.is_open = 0;
				$(this).removeClass('box_open');
				box_handle.siblings('.a3rev_panel_box_inside').removeClass('box_open').slideUp(500);
			} else {
				box_data.is_open = 1;
				$(this).addClass('box_open');
				box_handle.siblings('.a3rev_panel_box_inside').addClass('box_open').slideDown(500);
				box_handle.siblings('.a3rev_panel_box_inside').find('img.rwd_image_maps').each(function(i){
					$(this).rwdImageMaps();
				});
			}

			if ( $(this).hasClass('enable_toggle_box_save') && typeof a3_admin_ui_script_params != 'undefined' ) {
				$.post( a3_admin_ui_script_params.ajax_url, box_data );
			}
		});

		/* Apply Manual Check version */
		$(document).on( 'click', '.a3rev-ui-manual_check_version', function(){
			var bt_check_version = $(this);
			var version_message_container = $(this).siblings('.a3rev-ui-check-version-message');
			var version_checking_status = $(this).siblings('.a3rev-ui-version-checking');
			var transient_name = bt_check_version.data('transient-name');
			if ( ! bt_check_version.hasClass('a3-version-checking') ) {
				bt_check_version.addClass('a3-version-checking');
				version_checking_status.css('display', 'inline-block');
				version_message_container.slideUp();

				var check_data = {
					action:			a3_admin_ui_script_params.plugin + '_a3_admin_ui_event',
					type: 			'check_new_version',
					transient_name: transient_name,
					security:		a3_admin_ui_script_params.security
				};

				$.post( a3_admin_ui_script_params.ajax_url, check_data, function(response){
					bt_check_version.removeClass('a3-version-checking');
					version_checking_status.css('display', 'none');

					// Get response
					data = JSON.parse( response );
					if ( 0 == data.has_new_version ) {
						version_message_container.removeClass('a3rev-ui-new-version-message');
						version_message_container.addClass('a3rev-ui-latest-version-message');
					} else {
						version_message_container.addClass('a3rev-ui-new-version-message');
						version_message_container.removeClass('a3rev-ui-latest-version-message');
					}
					version_message_container.html(data.version_message);
					version_message_container.slideDown();
				});
			}
		});

		/* Apply Validate Google API Key Submit */
		$(document).on( 'click', '.a3rev-ui-google-api-key-validate-button', function(){
			var bt_validate = $(this);
			var g_api_key_container = $(this).parents('.a3rev-ui-google-api-key-inside');
			var g_api_key_field = g_api_key_container.children('.a3rev-ui-google-api-key');
			var g_api_key = g_api_key_field.val();
			var g_api_key_type = g_api_key_field.data('type');
			if ( ! bt_validate.hasClass('validating') && '' != g_api_key ) {
				bt_validate.addClass('validating');
				g_api_key_container.removeClass('a3rev-ui-google-valid-key a3rev-ui-google-unvalid-key');

				var check_data = {
					action:			a3_admin_ui_script_params.plugin + '_a3_admin_ui_event',
					type: 			'validate_google_api_key',
					g_key:          g_api_key,
					g_key_type:     g_api_key_type, 
					security:		a3_admin_ui_script_params.security
				};

				$.post( a3_admin_ui_script_params.ajax_url, check_data, function(response){
					bt_validate.removeClass('validating');

					// Get response
					data = JSON.parse( response );
					if ( 0 == data.is_valid ) {
						g_api_key_container.removeClass('a3rev-ui-google-valid-key');
						g_api_key_container.addClass('a3rev-ui-google-unvalid-key');
					} else {
						g_api_key_container.addClass('a3rev-ui-google-valid-key');
						g_api_key_container.removeClass('a3rev-ui-google-unvalid-key');
					}
				});
			}
		});

		/* Apply Ajax Submit */
		$(document).on( 'click', '.a3rev-ui-ajax_submit-button', function(){
			var bt_ajax_submit = $(this);
			var submit_data = JSON.parse( JSON.stringify( bt_ajax_submit.data('submit_data') ) );
			if ( typeof submit_data.ajax_url == 'undefined' ) return false;

			var submit_successsed = bt_ajax_submit.siblings('.a3rev-ui-ajax_submit-successed');
			var submit_errors = bt_ajax_submit.siblings('.a3rev-ui-ajax_submit-errors');
			var progress_bar_wrap = bt_ajax_submit.siblings('.a3rev-ui-progress-bar-wrap');
			var progress_inner = progress_bar_wrap.find('.a3rev-ui-progress-inner');
			var progressing_text = progress_bar_wrap.find('.a3rev-ui-progressing-text');
			var completed_text = progress_bar_wrap.find('.a3rev-ui-completed-text');
			bt_ajax_submit.hide();
			submit_successsed.hide();
			submit_errors.hide();
			progress_bar_wrap.show();

			// Plugin have use this control type need to get this trigger to make action from plugin
			$('#' + bt_ajax_submit.attr('id') ).trigger("a3rev-ui-ajax_submit-click", [bt_ajax_submit]);

			$.ajax({
				xhr: function () {
					var xhr = new window.XMLHttpRequest();
					var progressLoading = null;

					// Upload progress
					xhr.upload.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var interValTime = 1000;
							var startWidth = 0;

							progressLoading = setInterval( function() {
								startWidth += Math.floor((Math.random() * 10) + 1);
								console.log( startWidth );
								if ( 90 <= startWidth  ) {
									clearInterval( progressLoading );
								} else {
									progress_inner.css({
										width: startWidth + '%'
									});
								}
							}, interValTime );
						}
					}, false);

					// Download progress
					xhr.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							progress_inner.css({
								width: percentComplete * 100 + '%'
							});

							if (percentComplete === 1) {
								console.log( 'process completed' );
								clearInterval( progressLoading );
							}
						}
					}, false);

					return xhr;
				},

				type: submit_data.ajax_type,
				url: submit_data.ajax_url,
				data: submit_data.data,
				success: function ( response ) {
					data = JSON.parse( response );
					$('#' + bt_ajax_submit.attr('id') ).trigger("a3rev-ui-ajax_submit-completed", [ bt_ajax_submit, data ]);

					setTimeout( function() {
						progressing_text.hide();
						completed_text.show();
					}, 2000 );

					setTimeout( function() {
						bt_ajax_submit.show();
						submit_successsed.show();
						progress_bar_wrap.hide();
						progressing_text.show();
						completed_text.hide();
						progress_inner.css({width: '0%'});
					}, 3000 );
				},
				error: function( e ) {
					console.log(e);

					$('#' + bt_ajax_submit.attr('id') ).trigger("a3rev-ui-ajax_submit-errors", [ e, bt_ajax_submit ]);

					setTimeout( function() {
						progressing_text.hide();
						completed_text.show();
					}, 2000 );

					setTimeout( function() {
						bt_ajax_submit.show();
						progress_bar_wrap.hide();
						progressing_text.show();
						completed_text.hide();
						progress_inner.css({width: '0%'});
					}, 3000 );
				}
			});
		});

		/* Apply Ajax Multi Submit */
		function a3rev_ui_ajax_submit( bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ) {
			var ajax_item = multi_ajax[ajax_item_id];
			var current_items = $('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-statistic-current-item').data('current');
			var total_items = ajax_item.total_items;

			// Call to next ajax if current ajax have current items equal or more than total items
			if ( current_items >= total_items ) {
				$('#' + bt_ajax_submit.attr('id') ).trigger('a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-completed', [ bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ]);
				return false;
			}

			var submit_successsed = bt_ajax_submit.siblings('.a3rev-ui-ajax_multi_submit-successed');
			var submit_errors     = bt_ajax_submit.siblings('.a3rev-ui-ajax_multi_submit-errors');
			var progress_bar_wrap = bt_ajax_submit.siblings('.a3rev-ui-progress-bar-wrap');
			var progress_notice   = bt_ajax_submit.siblings('.a3rev-ui-progress-notice');
			var progress_inner    = progress_bar_wrap.find('.a3rev-ui-progress-inner');
			var progressing_text  = progress_bar_wrap.find('.a3rev-ui-progressing-text');
			var completed_text    = progress_bar_wrap.find('.a3rev-ui-completed-text');

			progressing_text.html( ajax_item.progressing_text );
			completed_text.html( ajax_item.completed_text );

			progressing_text.show();
			completed_text.hide();

			var progress_current_items = progress_inner.data('current');
			var progress_total_items = progress_inner.data('total');
			var currentPercent = progress_current_items / progress_total_items;
			var maximumWidth = Math.floor( total_items / progress_total_items * 100 );

			var submit_data = ajax_item.submit_data;

			$.ajax({
				xhr: function () {
					var xhr = new window.XMLHttpRequest();
					var progressLoading = null;

					// Upload progress
					xhr.upload.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var interValTime = 1000;
							var startWidth = Math.floor( currentPercent * 100 );
							if ( startWidth > 100 ) startWidth = 100;
							progress_inner.css({
								width: startWidth + '%'
							});
						}
					}, false);

					// Download progress
					xhr.addEventListener("progress", function (evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							if (percentComplete === 1) {
								// progress completed
							}
						}
					}, false);

					return xhr;
				},

				type: submit_data.ajax_type,
				url: submit_data.ajax_url,
				data: submit_data.data,
				success: function ( response ) {
					result = JSON.parse( response );

					new_items = result.current_items;

					increase_items = new_items - current_items;
					progress_current_items += increase_items;
					progress_inner.data('current', progress_current_items);

					currentPercent = progress_current_items / progress_total_items;
					newWidth = Math.floor( currentPercent * 100 );
					if ( newWidth > 100 ) newWidth = 100;
					progress_inner.css({
						width: newWidth + '%'
					});

					a3rev_ui_ajax_multi_statistic_change( ajax_item_id, new_items, current_items, total_items, 3000, true );

					if ( typeof result.status != 'undefined' && 'completed' != result.status ) {
						$('#' + bt_ajax_submit.attr('id') ).trigger('a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-'+result.status, [ bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ]);
					} else {
						progressing_text.hide();
						completed_text.show();
						setTimeout( function(){
							$('#' + bt_ajax_submit.attr('id') ).trigger('a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-completed', [ bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ]);
						}, 2000 );
					}
				},
				error: function( e ) {
					console.log(e);

					// Allow trigger error
					$('#' + bt_ajax_submit.attr('id') ).trigger('a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-errors', [ e, bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ]);

					// Stop ajax call here
					$('#' + bt_ajax_submit.attr('id') ).trigger('a3rev-ui-ajax_multi_submit-errors', [ bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ]);

					setTimeout( function() {
						bt_ajax_submit.show();
						submit_successsed.hide();
						submit_errors.show();
						progress_bar_wrap.hide();
						progressing_text.show();
						completed_text.hide();

						if ( typeof progress_notice !== undefined ) {
							progress_notice.hide();
						}

					}, 2000 );
				}
			});

			return false;
		}

		function a3rev_ui_ajax_multi_statistic_circle_animation( ajax_item_id, current_point, total_point ) {
			current_deg = 360;
			left_deg    = 360;
			right_deg   = 180;
			if ( current_point < total_point ) {
				current_deg = Math.round( current_point / total_point * 360 );
			}

			if ( current_deg <= 180 ) {
				left_deg = right_deg = current_deg;
				$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-pie').removeClass('pie-more-50');
			} else {
				right_deg = 180;
				left_deg = current_deg;
				$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-pie').addClass('pie-more-50');
			}

			$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-pie-left-side').css('transform', 'rotate('+left_deg+'deg)');
			$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-pie-right-side').css('transform', 'rotate('+right_deg+'deg)');
		}

		function a3rev_ui_ajax_multi_statistic_change( ajax_item_id, new_point, current_point, total_point, duration, have_effect) {
			$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-statistic-current-item').data('current', new_point );
			if ( have_effect == false ) {
				$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-statistic-current-item').html(new_point);
			}

			$({current_point: current_point}).animate({current_point: new_point}, {
				duration: duration,
      			easing:'swing', // can be anything
      			step: function() {
      				if ( have_effect ) {
      					$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-statistic-current-item').html( Math.round( this.current_point) );
      				}
      				a3rev_ui_ajax_multi_statistic_circle_animation( ajax_item_id, this.current_point, total_point );
      			},
      			complete: function() {
      				if ( have_effect ) {
      					$('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-statistic-current-item').html( Math.round( this.current_point ) );
      				}
      				a3rev_ui_ajax_multi_statistic_circle_animation( ajax_item_id, this.current_point, total_point );
      			}
			});
		}

		$('.a3rev_panel_container .a3rev-ui-ajax_multi_submit-button').each(function(){
			var bt_ajax_submit = $(this);
			var multi_ajax_registered = JSON.parse( JSON.stringify( bt_ajax_submit.data('multi_ajax') ) );

			$.each( multi_ajax_registered, function( i, ajax_item ){
				ajax_item_id = ajax_item.item_id;

				$(document).on( 'a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-start', '#' + bt_ajax_submit.attr('id'), function( event, bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ) {
					console.log( 'Start - ' + ajax_item_id );
					a3rev_ui_ajax_submit( bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id );
				});

				$(document).on( 'a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-continue', '#' + bt_ajax_submit.attr('id'), function( event, bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ) {
					console.log( 'Continue - ' + ajax_item_id );
					a3rev_ui_ajax_submit( bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id );
				});

				$(document).on( 'a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-completed', '#' + bt_ajax_submit.attr('id'), function( event, bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ) {
					console.log( 'Completed - ' + ajax_item_id );

					// Compeleted multi ajax if don't have next ajax item id
					if ( '' == ajax_next_item_id ) {
						$('#' + bt_ajax_submit.attr('id') ).trigger("a3rev-ui-ajax_multi_submit-end", [bt_ajax_submit, multi_ajax]);
						return false;
					}

					ajax_next_item = multi_ajax[ajax_next_item_id];
					new_ajax_next_item_id = ajax_next_item.next_item_id;

					console.log('trigger - '+ajax_next_item_id);
					$('#' + bt_ajax_submit.attr('id') ).trigger('a3rev-ui-ajax_multi_submit-'+ajax_next_item_id+'-start', [ bt_ajax_submit, multi_ajax, ajax_next_item_id, new_ajax_next_item_id ]);
				});
			});

			$(document).on( 'a3rev-ui-ajax_multi_submit-end', '#' + bt_ajax_submit.attr('id'), function( event, bt_ajax_submit, multi_ajax ) {
				console.log( 'Completed Multi Ajax' );

				bt_ajax_submit.data( 'resubmit', 1 );

				var submit_successsed = bt_ajax_submit.siblings('.a3rev-ui-ajax_multi_submit-successed');
				var submit_errors     = bt_ajax_submit.siblings('.a3rev-ui-ajax_multi_submit-errors');
				var progress_bar_wrap = bt_ajax_submit.siblings('.a3rev-ui-progress-bar-wrap');
				var progress_notice   = bt_ajax_submit.siblings('.a3rev-ui-progress-notice');
				var progress_inner    = progress_bar_wrap.find('.a3rev-ui-progress-inner');
				var progressing_text  = progress_bar_wrap.find('.a3rev-ui-progressing-text');
				var completed_text    = progress_bar_wrap.find('.a3rev-ui-completed-text');

				progress_inner.css({
					width: '100%'
				});

				setTimeout( function() {
					progressing_text.hide();
					completed_text.show();
				}, 2000 );

				setTimeout( function() {
					bt_ajax_submit.show();
					submit_successsed.show();
					submit_errors.hide();
					progress_bar_wrap.hide();
					progressing_text.show();
					completed_text.hide();
					progress_inner.css({width: '0%'});
					if ( typeof progress_notice !== undefined ) {
						progress_notice.hide();
					}
				}, 3000 );
			});
		});

		$(document).on( 'click', '.a3rev-ui-ajax_multi_submit-button', function(){
			var bt_ajax_submit = $(this);

			var confirm_message = $(this).data('confirm_message');
			if ( typeof confirm_message !== 'undefined' && '' != confirm_message ) {
				var confirm_submit = confirm( confirm_message );
				if ( ! confirm_submit ) {
					return false;
				}
			}

			var resubmit = bt_ajax_submit.data('resubmit');
			bt_ajax_submit.data('resubmit', 0);

			var multi_ajax = JSON.parse( JSON.stringify( bt_ajax_submit.data('multi_ajax') ) );

			var submit_successsed = bt_ajax_submit.siblings('.a3rev-ui-ajax_multi_submit-successed');
			var submit_errors     = bt_ajax_submit.siblings('.a3rev-ui-ajax_multi_submit-errors');
			var progress_bar_wrap = bt_ajax_submit.siblings('.a3rev-ui-progress-bar-wrap');
			var progress_notice   = bt_ajax_submit.siblings('.a3rev-ui-progress-notice');
			var progress_inner    = progress_bar_wrap.find('.a3rev-ui-progress-inner');
			var progressing_text  = progress_bar_wrap.find('.a3rev-ui-progressing-text');
			var completed_text    = progress_bar_wrap.find('.a3rev-ui-completed-text');

			bt_ajax_submit.hide();
			submit_successsed.hide();
			submit_errors.hide();
			progress_bar_wrap.show();
			if ( typeof progress_notice !== undefined ) {
				progress_notice.show();
			}

			// Reset progressing start point to 0 for resubmit
			if ( resubmit == 1 ) {
				progress_inner.data('current', 0);
			}


			// Plugin have use this control type need to get this trigger to make action from plugin
			$('#' + bt_ajax_submit.attr('id') ).trigger("a3rev-ui-ajax_multi_submit-click", [bt_ajax_submit]);

			var progress_current_items = progress_inner.data('current');
			var progress_total_items = progress_inner.data('total')
			var currentPercent = progress_current_items / progress_total_items;
			if ( currentPercent < 1 ) {
				progress_inner.animate({
					width: Math.floor( currentPercent * 100 ) + '%'
				}, 0 );
			} else {
				currentPercent = 0;
			}

			var have_first_ajax_item = false;
			var first_ajax_item = null;
			$.each( multi_ajax, function( i, ajax_item ){
				if ( ! have_first_ajax_item ) {
					first_ajax_item = ajax_item;
					have_first_ajax_item = true;
				}
				ajax_item_id   = ajax_item.item_id;
				current_items = $('.a3rev-ui-statistic-'+ajax_item_id).find('.a3rev-ui-statistic-current-item').data('current');
				total_items = ajax_item.total_items;

				// Reset current items of each ajax to 0 for resubmit
				if ( resubmit == 1 ) {
					a3rev_ui_ajax_multi_statistic_change( ajax_item_id, 0, current_items, total_items, 500, false );
				}
			});

			// Just call first ajax submit
			ajax_item_id   = first_ajax_item.item_id;
			ajax_next_item_id = first_ajax_item.next_item_id;

			$('#' + bt_ajax_submit.attr('id') ).trigger("a3rev-ui-ajax_multi_submit-start", [bt_ajax_submit, multi_ajax]);

			console.log('trigger - '+ajax_item_id);
			$('#' + bt_ajax_submit.attr('id') ).trigger('a3rev-ui-ajax_multi_submit-'+ajax_item_id+'-start', [ bt_ajax_submit, multi_ajax, ajax_item_id, ajax_next_item_id ]);

			return false;
		});

		/* Apply Image Maps script */
		$('.a3rev_panel_container img.rwd_image_maps').each(function(i){
			$(this).rwdImageMaps();
		});

		$(document).trigger("a3rev-ui-script-loaded");

	});
})(jQuery);
