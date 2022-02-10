jQuery(document).ready(function() {

	/**
	 * "(Un)select all" checkboxes
	 */
	var checkbox_actions = ['select_all', 'unselect_all'];
 	checkbox_actions.forEach(function(element) {
		jQuery('#permalink-manager .' + element).on('click', function() {
			jQuery(this).parents('.field-container').find('.checkboxes input[type="checkbox"]').each(function() {
				var action = (element == 'select_all') ? true : false;
				jQuery(this).prop('checked', action);
			});

			return false;
		});
	});

	jQuery('#permalink-manager .checkboxes label, #permalink-manager .single_checkbox label').not('input').on('click', function(ev) {
		var input = jQuery(this).find("input");
		if(!jQuery(ev.target).is("input")) {
			input.prop('checked', !(input.prop("checked")));
		}
	});

	/**
	 * Confirm action
	 */
	jQuery('.pm-confirm-action').on('click', function () {
		return confirm(permalink_manager.confirm);
	});

	/**
	 * Filter by dates + Search in URI Editor
	 */
	jQuery('#permalink-manager #months-filter-button, #permalink-manager #search-submit').on('click', function(e) {
		var search_value = jQuery('#permalink-manager input[name="s"]').val();
		var filter_value = jQuery("#months-filter-select").val();

		var filter_url = window.location.href;

		// Date filter
		if(filter_url.indexOf('month=') > 1) {
			filter_url = filter_url.replace(/month=([^&]+)/gm, 'month=' + filter_value);
		} else if(filter_value != '') {
			filter_url = filter_url + '&month=' + filter_value;
		}

		// Search query
		if(filter_url.indexOf('s=') > 1) {
			filter_url = filter_url.replace(/s=([^&]+)/gm, 's=' + search_value);
		} else if(search_value != '') {
			filter_url = filter_url + '&s=' + search_value;
		}

		window.location.href = filter_url;

		e.preventDefault();
		return false;
	});

	jQuery('#permalink-manager #uri_editor form input[name="s"]').on('keydown keypress keyup', function(e){
		if(e.keyCode == 13) {
			jQuery('#permalink-manager #search-submit').trigger('click');

			e.preventDefault();
			return false;
		}
	});

	/**
	 * Filter by content types in "Tools"
	 */
	jQuery('#permalink-manager *[data-field="content_type"] select').on('change', function() {
		var content_type = jQuery(this).val();
		if(content_type == 'post_types') {
			jQuery(this).parents('.form-table').find('*[data-field="post_types"],*[data-field="post_statuses"]').removeClass('hidden');
			jQuery(this).parents('.form-table').find('*[data-field="taxonomies"]').addClass('hidden');
		} else {
			jQuery(this).parents('.form-table').find('*[data-field="post_types"],*[data-field="post_statuses"]').addClass('hidden');
			jQuery(this).parents('.form-table').find('*[data-field="taxonomies"]').removeClass('hidden');
		}
	}).trigger("change");

	/**
	 * Toggle "Edit URI" box
	 */
	jQuery('#permalink-manager-toggle, .permalink-manager-edit-uri-box .close-button').on('click', function() {
		jQuery('.permalink-manager-edit-uri-box').slideToggle();

		return false;
	});

	/**
	 * Toggle "Edit Redirects" box
	 */
	jQuery('#permalink-manager').on('click', '#toggle-redirect-panel', function() {
		jQuery('#redirect-panel-inside').slideToggle();

		return false;
	});

	jQuery('#permalink-manager').on('click', '.permalink-manager.redirects-panel #permalink-manager-new-redirect', function() {
		// Find the table
		var table = jQuery(this).parents('.redirects-panel').find('table');

		// Copy the row from the sample
		var new_row = jQuery(this).parents('.redirects-panel').find('.sample-row').clone().removeClass('sample-row');

		// Adjust the array key
		var last_key = jQuery(table).find("tr:last-of-type input[data-index]").data("index") + 1;
		jQuery("input[data-index]", new_row).attr("data-index", last_key).attr("name", function(){ return jQuery(this).attr("name") + "[" + last_key + "]" });

		// Append the new row
		jQuery(table).append(new_row);

		return false;
	});

	jQuery('#permalink-manager').on('click', '.remove-redirect', function() {
		var table = jQuery(this).closest('tr').remove();
		return false;
	});

	/**
	 * Synchronize "Edit URI" input field with the sample permalink
	 */
	var custom_uri_input = jQuery('.permalink-manager-edit-uri-box input[name="custom_uri"]');
	jQuery(custom_uri_input).on('keyup change', function() {
		jQuery('.sample-permalink-span .editable').text(jQuery(this).val());
	});

	/**
	 * Synchronize "Coupon URI" input field with the final permalink
	 */
	jQuery('#permalink-manager-coupon-url input[name="custom_uri"]').on('keyup change', function() {
		var uri = jQuery(this).val();
		jQuery('#permalink-manager-coupon-url code span').text(uri);

		if(!uri) {
			jQuery('#permalink-manager-coupon-url .coupon-full-url').addClass("hidden");
		} else {
			jQuery('#permalink-manager-coupon-url .coupon-full-url').removeClass("hidden");
		}
	});

	function permalink_manager_duplicate_check(custom_uri_input, multi) {
		// Set default values
		custom_uri_input = typeof custom_uri_input !== 'undefined' ? custom_uri_input : false;
  	multi = typeof multi !== 'undefined' ? multi : false;

		var all_custom_uris_values = {};

		if(custom_uri_input) {
			var custom_uri = jQuery(custom_uri_input).val();
			var element_id = jQuery(custom_uri_input).attr("data-element-id");

			all_custom_uris_values[element_id] = custom_uri;
		} else {
			jQuery('.custom_uri').each(function(i, obj) {
				var field_name = jQuery(obj).attr('data-element-id');
			  all_custom_uris_values[field_name] = jQuery(obj).val();
			});
		}

		if(all_custom_uris_values) {
			jQuery.ajax(permalink_manager.ajax_url, {
				type: 'POST',
				async: true,
				data: {
					action: 'pm_detect_duplicates',
					custom_uris: all_custom_uris_values
				},
				success: function(data) {
					if(data.length > 5) {
						try {
							var results = JSON.parse(data);
						} catch (e) {
							console.log(e);
							console.log(data);
							return;
						}

						// Loop through results
						jQuery.each(results, function(key, is_duplicate) {
							var alert_container = jQuery('.custom_uri[data-element-id="' + key + '"]').parents('.custom_uri_container').find('.duplicated_uri_alert');

							if(is_duplicate) {
								jQuery(alert_container).text(is_duplicate);
							} else {
								jQuery(alert_container).empty();
							}
						});
					}
				}
			});
		}
	}

	/**
	 * Check if a single custom URI is not duplicated
	 */
	var custom_uri_check_timeout = null;
	jQuery('.custom_uri_container input[name="custom_uri"], .custom_uri_container input.custom_uri').each(function() {
		var input = this;

		jQuery(this).on('keyup change', function() {
			clearTimeout(custom_uri_check_timeout);

			// Wait until user finishes typing
	    custom_uri_check_timeout = setTimeout(function() {
				permalink_manager_duplicate_check(input);
	    }, 500);
		});

	});

	/**
	 * Check if any of displayed custom URIs is not duplicated
	 */
	if(jQuery('#uri_editor .custom_uri').length > 0) {
		permalink_manager_duplicate_check(false, true);
	}

	/**
	 * Disable "Edit URI" input if URI should be updated automatically
	 */
	jQuery('#permalink-manager').on('change', 'select[name="auto_update_uri"]', function() {
		var selected = jQuery(this).find('option:selected');
		var auto_update_status = jQuery(selected).data('auto-update');
		var container = jQuery(this).parents('#permalink-manager');

		if(auto_update_status == 1) {
			jQuery(container).find('input[name="custom_uri"]').attr("readonly", true);
			jQuery(container).find('.uri_locked').removeClass("hidden");
		} else {
			jQuery(container).find('input[name="custom_uri"]').removeAttr("readonly", true);
			jQuery(container).find('.uri_locked').addClass("hidden");
		}
	});
	jQuery('select[name="auto_update_uri"]').trigger("change");

	/**
	 * Restore "Default URI"
	 */
	jQuery('#permalink-manager').on('click', '.restore-default', function() {
		var input = jQuery(this).parents('.field-container, .permalink-manager-edit-uri-box, #permalink-manager .inside').find('input.custom_uri, input.permastruct-field');
		var default_uri = jQuery(input).attr('data-default');

		jQuery(input).val(default_uri).trigger('keyup');

		return false;
	});

	/**
	 * Dispaly additional permastructure settings
	 */
	jQuery('#permalink-manager').on('click', '.permastruct-toggle-button a', function() {
		jQuery(this).parents('.field-container').find('.permastruct-toggle').slideToggle();

		return false;
	});

	/**
	 * Settings tabs
	 */
	jQuery('#permalink-manager').on('click', '.settings-tabs .subsubsub a', function() {
		var tab_id = jQuery(this).attr('data-tab');

		jQuery('#permalink-manager .settings-tabs .subsubsub a').removeClass('current');
		jQuery(this).addClass('current');

		jQuery('#permalink-manager .settings-tabs form > div').hide().removeClass('active-tab');
		jQuery('#permalink-manager .settings-tabs form > div#pm_' + tab_id).show().addClass('active-tab');

		jQuery('#permalink-manager .settings-tabs form input[name="pm_active_tab"]').val(tab_id);

		return false;
	});

	/**
	 * Conditional fields in Permalink Manager settings
	 */
	jQuery('#permalink-manager .settings-tabs #extra_redirects input[type="checkbox"]').on('change', function() {
		var is_checked = jQuery(this).is(':checked');
		var rel_field_container = jQuery('#permalink-manager .settings-tabs #setup_redirects');

		if(is_checked == true) {
			rel_field_container.removeClass('hidden');
		} else {
			rel_field_container.addClass('hidden');
		}
	}).trigger("change");

	/**
	 * Hide global admin notices
	 */
	jQuery(document).on('click', '.permalink-manager-notice.is-dismissible .notice-dismiss', function() {
		var alert_id = jQuery(this).closest('.permalink-manager-notice').data('alert_id');

		jQuery.ajax(permalink_manager.ajax_url, {
			type: 'POST',
			data: {
				action: 'dismissed_notice_handler',
				alert_id: alert_id,
			}
		});
	});

	/**
	 * Save permalinks from Gutenberg with AJAX
	 */
	jQuery('#permalink-manager .save-row.hidden').removeClass('hidden');
	jQuery('#permalink-manager').on('click', '#permalink-manager-save-button', pm_gutenberg_save_uri);

	function pm_gutenberg_reload() {
		var pm_container = jQuery('#permalink-manager.postbox');
		var pm_post_id = jQuery('input[name="permalink-manager-edit-uri-element-id"]').val();

		jQuery.ajax({
			type: 'GET',
			url: permalink_manager.ajax_url + '?action=pm_get_uri_editor',
			data: {
				'post_id': pm_post_id
			},
			beforeSend: function() {
				jQuery(pm_container).LoadingOverlay("show", {
					background  : "rgba(0, 0, 0, 0.1)",
				});
			},
			success: function(html) {
				jQuery(pm_container).find('.permalink-manager-gutenberg').replaceWith(html);
				jQuery(pm_container).LoadingOverlay("hide");

				jQuery(pm_container).find('select[name="auto_update_uri"]').trigger("change");
				pm_help_tooltips();
      }
		});
	}

	function pm_gutenberg_save_uri() {
		var pm_container = jQuery('#permalink-manager.postbox');
		var pm_fields = jQuery(pm_container).find("input, select");

		jQuery.ajax({
			type: 'POST',
			url: permalink_manager.ajax_url,
			data: jQuery(pm_fields).serialize() + '&action=pm_save_permalink',
			success: pm_gutenberg_reload
		});

		return false;
	}

	/**
	 * Reload the URI Editor in Gutenberg after the post is published or the title/slug is changed
	 */
	if(typeof wp !== 'undefined' && typeof wp.data !== 'undefined' && typeof wp.data.select !== 'undefined' && typeof wp.blocks !== 'undefined' && typeof wp.data.subscribe !== 'undefined' && wp.data.select('core/editor') !== 'undefined' && wp.data.select('core/editor') !== null) {
		var pm_gutenberg_reload_in_progress = 0;

		const pm_unsubscribe = wp.data.subscribe(function() {
			var isSavingPost = wp.data.select('core/editor').isSavingPost();
			var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
			var didPostSaveRequestSucceed =  wp.data.select('core/editor').didPostSaveRequestSucceed();

			// Wait until the last occurence is called
			if(isSavingPost && !isAutosavingPost && didPostSaveRequestSucceed) {
				clearTimeout(pm_gutenberg_reload_in_progress);

				pm_gutenberg_reload_in_progress = setTimeout(function(){
					pm_gutenberg_reload();
				}, 1500);
			}
		});
	}

	/**
	 * Help tooltips
	 */
	function pm_help_tooltips() {
		if(jQuery('#permalink-manager .help_tooltip').length > 0) {
			jQuery('#permalink-manager .help_tooltip').each(function() {
				var helpTooltip = this;

				tippy(helpTooltip, {
					position: 'top-start',
					arrow: true,
					content: jQuery(helpTooltip).attr('title'),
					distance: 20
				});
			});
		}
	}
	pm_help_tooltips();


	/**
	 * Check expiration date
	 */
	jQuery(document).on('click', '#pm_get_exp_date', function() {
		jQuery.ajax(permalink_manager.ajax_url, {
			type: 'POST',
			data: {
				action: 'pm_get_exp_date',
				licence: {
					licence_key: jQuery('#permalink-manager #settings #licence_key input[type="text"]').val()
				}
			},
			beforeSend: function() {
				var spinner = '<img src="' + permalink_manager.spinners + '/wpspin_light-2x.gif" width="16" height="16">';
				jQuery('#permalink-manager .licence-info').html(spinner);
			},
			success: function(data) {
				jQuery('#permalink-manager .licence-info').html(data);
			}
		});

		return false;
	});

	/**
	 * Bulk tools
	 */
	function pm_show_progress(elem, progress) {
		if(progress) {
			jQuery(elem).LoadingOverlay("text", progress + "%");
		} else {
			jQuery(elem).LoadingOverlay("show", {
				background  : "rgba(0, 0, 0, 0.1)",
				text: '0%'
			});
		}
	}

	jQuery('#permalink-manager #tools form.form-ajax').on('submit', function() {
		var data = jQuery(this).serialize() + '&action=' + 'pm_bulk_tools';
		var form = jQuery(this);
		var updated_count = total = progress = 0;

		// Hide alert & results table
		jQuery('#permalink-manager .updated-slugs-table, .permalink-manager-notice.updated_slugs, #permalink-manager #updated-list').remove();

		jQuery.ajax({
			type: 'POST',
			url: permalink_manager.ajax_url,
			data: data,
			beforeSend: function() {
				// Show progress overlay
				pm_show_progress("#permalink-manager #tools", progress);
			},
			success: function(data) {
				var table_dom = jQuery('#permalink-manager .updated-slugs-table');

				// Display the table
				if(data.hasOwnProperty('html')) {
					var table = jQuery(data.html);

					if(table_dom.length == 0) {
						jQuery('#permalink-manager #tools').after(data.html);
					} else {
						jQuery(table_dom).append(jQuery(table).find('tbody').html());
					}
				}

				// Hide error message
				jQuery('.permalink-manager-notice.updated_slugs.error').remove();

				// Display the alert (should be hidden at first)
				if(data.hasOwnProperty('alert') && jQuery('.permalink-manager-notice.updated_slugs .updated_count').length == 0) {
					var alert = jQuery(data.alert).hide();
					jQuery('#plugin-name-heading').after(alert);
				}

				// Increase updated count
				if(data.hasOwnProperty('updated_count')) {
					if(jQuery(form).attr("data-updated_count")) {
						updated_count = parseInt(jQuery(form).attr("data-updated_count")) + parseInt(data.updated_count);
					} else {
						updated_count = parseInt(data.updated_count);
					}

					jQuery(form).attr("data-updated_count", updated_count);
					jQuery('.permalink-manager-notice.updated_slugs .updated_count').text(updated_count);
				}

				// Show total
				if(data.hasOwnProperty('total')) {
					total = parseInt(data.total);

					jQuery(form).attr("data-total", total);
				}

				// Trigger again
				if(data.hasOwnProperty('left_chunks') && (typeof total !== "undefined" && data.progress < total)) {
					jQuery.ajax(this);

					// Update progress
					if(data.hasOwnProperty('progress')) {
						progress = Math.floor((data.progress / total) * 100)
						console.log(data.progress + "/" + total + " = " + progress + "%");
					}
				} else {
					// Display results
					jQuery('.permalink-manager-notice.updated_slugs').fadeIn();
					jQuery('#permalink-manager #tools').LoadingOverlay("hide", true);

					if(table_dom.length > 0) {
						jQuery('html, body').animate({
							scrollTop: table_dom.offset().top - 100
	          }, 2000);
					}

					// Reset progress & updated count
					progress = updated_count = 0;
					jQuery(form).attr("data-updated_count", 0);
				}
      },
			error: function(xhr, status, error_data) {
				alert('Tthere was a problem running this tool and the process could not be completed. You can find more details in browser\'s console log.')
				console.log('Status: ' + status);
				console.log('Please send the debug data to contact@permalinkmanager.pro:\n\n' + xhr.responseText);

				jQuery('#permalink-manager #tools').LoadingOverlay("hide", true);
			}
		});

		return false;
	});

	/**
	 * Stop-words
	 */
	var stop_words_input = '#permalink-manager .field-container textarea.stop_words';

	if(jQuery(stop_words_input).length > 0) {
		var stop_words = new TIB(document.querySelector(stop_words_input), {
			alert: false,
			//escape: null,
			escape: [','],
			classes: ['tags words-editor', 'tag', 'tags-input', 'tags-output', 'tags-view'],
		});
		jQuery('.tags-output').hide();

		// Force lowercase
		stop_words.filter = function(text) {
			return text.toLowerCase();
		};

		// Remove all words
		jQuery('#permalink-manager .field-container .clear_all_words').on('click', function() {
			stop_words.reset();
		});

		// Load stop-words list
		jQuery('#permalink-manager #load_stop_words_button').on('click', function() {
			var lang = jQuery( ".load_stop_words option:selected" ).val();
			if(lang) {
				var json_url = permalink_manager.url + "/includes/ext/stopwords-json/dist/" + lang + ".json";

				// Load JSON with words list
				jQuery.getJSON(json_url, function(data) {
				  var new_words = [];

				  jQuery.each(data, function(key, val) {
				    new_words.push(val);
				  });

				  stop_words.update(new_words);
				});
			}

			return false;
		});
	}

	/**
	 * Quick Edit
	 */
	if(typeof inlineEditPost !== "undefined") {
		var inline_post_editor = inlineEditPost.edit;
		inlineEditPost.edit = function(id) {
			inline_post_editor.apply(this, arguments);

			// Get the Post ID
			var post_id = 0;
			if(typeof(id) == 'object') {
				post_id = parseInt(this.getId(id));
			}

			if(post_id != 0) {
				// Get the row & "Custom URI" field
				custom_uri_field = jQuery('#edit-' + post_id).find('.custom_uri');

				// Prepare the Custom URI
				custom_uri = jQuery("#post-" + post_id).find(".column-permalink-manager-col").text();

				// Fill with the Custom URI
				custom_uri_field.val(custom_uri);

				// Get auto-update settings
				auto_update = jQuery("#post-" + post_id).find(".permalink-manager-col-uri").attr('data-auto_update');
				if(typeof auto_update !== "undefined" && auto_update == 1) {
					custom_uri_field.attr('readonly', 'readonly');
				}

				// Set the element ID
				jQuery('#edit-' + post_id).find('.permalink-manager-edit-uri-element-id').val(post_id);
			}
		}
	}

	if(typeof inlineEditTax !== "undefined") {
		var inline_tax_editor = inlineEditTax.edit;
		inlineEditTax.edit = function(id) {
			inline_tax_editor.apply(this, arguments);

			// Get the Post ID
			var term_id = 0;
			if(typeof(id) == 'object') {
				term_id = parseInt(this.getId(id));
			}

			if(term_id != 0) {
				// Get the row & "Custom URI" field
				custom_uri_field = jQuery('#edit-' + term_id).find('.custom_uri');

				// Prepare the Custom URI
				custom_uri = jQuery("#tag-" + term_id).find(".column-permalink-manager-col").text();

				// Fill with the Custom URI
				custom_uri_field.val(custom_uri);

				// Set the element ID
				jQuery('#edit-' + term_id).find('.permalink-manager-edit-uri-element-id').val("tax-" + term_id);
			}
		}
	}

});
