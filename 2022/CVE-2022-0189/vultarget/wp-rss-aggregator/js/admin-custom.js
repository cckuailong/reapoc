// jQuery for 'Fetch Feed Items' Row Action in 'All Feed Sources' page
function fetch_items_row_action_callback(e){
    var link = jQuery(this);
    if (link.attr('disabled')) {
        return;
    }

    var allLinks = jQuery('a.wprss_fetch_items_ajax_action, a.wprss_delete_items_ajax_action');
    allLinks.attr('disabled', 'disabled');

    var original_text = link.html();
    var id = link.attr('pid');

    var errorImportingHandler = function(jqXHR, status, exceptionText) {
        displayResultMessage(status === 'parsererror' ? 'Error parsing response' : exceptionText, 'ajax-error');
    };

    var displayResultMessage = function(message, className) {
        link.text(message);
        if (className) {
            link.addClass(className);
        }

        setTimeout(function(){
            link.html(original_text);
            allLinks.removeAttr('disabled');
            if (className) {
                link.removeClass(className);
            }
        }, 2000);
    };

    link.text( wprss_admin_custom.please_wait );

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            'action': 'wprss_fetch_items_row_action',
            'id':   id,
            'wprss_admin_ajax_nonce': jQuery('#wprss_feed_source_action_nonce').data('value'), // nonce
            'wprss_admin_ajax_referer': jQuery('#_wp_http_referer').val() // referer
        },
        success: function( response, status, jqXHR ){
            if (response.is_error) {
                errorImportingHandler(jqXHR, status, response.error_message);
                return;
            }

            displayResultMessage(wprss_admin_custom.items_are_importing);
            jQuery('table.wp-list-table tbody tr.post-' + id).addClass('wpra-feed-is-updating wpra-manual-update');
        },
        error: errorImportingHandler,
        timeout: 60000 // set timeout to 1 minute
    });

    e.preventDefault();
};




// jQuery for 'Delete Items' Row Action in 'All Feed Sources' page
function delete_items_row_action_callback(e){
    var link = jQuery(this);
    if (link.attr('disabled')) {
        return;
    }

    var allLinks = jQuery('a.wprss_fetch_items_ajax_action, a.wprss_delete_items_ajax_action');
    allLinks.attr('disabled', 'disabled');

    var original_text = link.text();
    var id = link.attr('pid');

    var errorImportingHandler = function(jqXHR, status, exceptionText) {
        displayResultMessage(status === 'parsererror' ? 'Error parsing response' : exceptionText, 'ajax-error');
    };

    var displayResultMessage = function(message, className) {
        link.text(message);
        if (className) {
            link.addClass(className);
        }

        setTimeout(function(){
            link.text(original_text);
            allLinks.removeAttr('disabled');
            if (className) {
                link.removeClass(className);
            }
        }, 2000);
    };

    link.text( wprss_admin_custom.please_wait );

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            'action': 'wprss_delete_items_row_action',
            'id':   id,
            'wprss_admin_ajax_nonce': jQuery('#wprss_feed_source_action_nonce').data('value'), // nonce
            'wprss_admin_ajax_referer': jQuery('#_wp_http_referer').val() // referer
        },
        success: function( response, status, jqXHR ){
            if (response.is_error) {
                errorImportingHandler(jqXHR, status, response.error_message);
                return;
            }

            displayResultMessage(wprss_admin_custom.items_are_deleting);
            jQuery('table.wp-list-table tbody tr.post-' + id).addClass('wpra-feed-is-deleting wpra-manual-delete');
        },
        error: errorImportingHandler,
        timeout: 60000 // set timeout to 1 minute
    });

    e.preventDefault();
};



// jQuery for the feed state toggle buttons
function toggle_feed_state_ajax_callback(e) {
    var checkbox = jQuery(this);
    var id = checkbox.val();
    var checked = checkbox.prop('checked') === true;
    var container = checkbox.closest('.wprss-feed-state-container');
    var row = checkbox.closest('tr');

    var errorFunction = function (response) {
        console.log(response);
    };

    row.toggleClass('active');

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        data: {
            'action': 'wprss_toggle_feed_state',
            'id':   id,
            'wprss_admin_ajax_nonce': jQuery('#wprss_feed_source_action_nonce').data('value'), // nonce
            'wprss_admin_ajax_referer': jQuery('#_wp_http_referer').val() // referer
        },
        success: function( response, status, jqXHR ){
            if (response.is_error) {
                errorFunction(response.error_message);
            }
        },
        error: errorFunction,
        timeout: 60000
    });
}




jQuery(window).on('load', function() {

    function wprssParseDate(str){
        var t = str.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
        if( t!==null ){
            var d=+t[1], m=+t[2], y=+t[3];
            var date = new Date(y,m-1,d);
            if( date.getFullYear() === y && date.getMonth() === m-1 ){
                return date;   
            }
        }
        return null;
    }


    var WPRSS_DATE_FORMAT = 'dd/mm/yy';
    var WPRSS_TIME_FORMAT = 'HH:mm:ss';
    var WPRSS_NOW = new Date();
    var WPRSS_NOW_UTC = new Date(
        WPRSS_NOW.getUTCFullYear(),
        WPRSS_NOW.getUTCMonth(),
        WPRSS_NOW.getUTCDate(),
        WPRSS_NOW.getUTCHours(),
        WPRSS_NOW.getUTCMinutes(),
        WPRSS_NOW.getUTCSeconds()
    );

    // Set datepickers
    jQuery.datepicker.setDefaults({
        dateFormat: WPRSS_DATE_FORMAT,
    });
    jQuery.timepicker.setDefaults({
        controlType: 'slider',
        timezone: 0,
        timeFormat: WPRSS_TIME_FORMAT,
    });
    jQuery('.wprss-datetimepicker').datetimepicker();
    jQuery('.wprss-datetimepicker-from-today').datetimepicker({ minDate: WPRSS_NOW_UTC });


    jQuery('.wprss-datepicker, .wprss-datepicker-from-today').focusout( function(){
        val = jQuery(this).val();
        if ( val !== '' && wprssParseDate( val ) === null ) {
            jQuery(this).addClass('wprss-date-error');
        } else jQuery(this).removeClass('wprss-date-error');
    });

	// On TAB pressed when on title input field, go to URL input field
	jQuery('input#title').on( 'keydown', function( event ) {    
        
        if ( event.which == 9 ) {
            event.preventDefault();
            jQuery('input#wprss_url').focus();
        }
    }
    );	    

	// On TAB pressed when on description textarea field, go to Publish submit button
	jQuery('textarea#wprss_description').on( 'keydown', function( event ) {
                
        if ( event.which == 9 ) {
            event.preventDefault();
            jQuery('input#publish').focus();
        }
    }
    );	        

	jQuery('.wp-list-table').on( 'click', '.wprss_fetch_items_ajax_action', fetch_items_row_action_callback );
    jQuery('.wp-list-table').on( 'click', '.wprss_delete_items_ajax_action', delete_items_row_action_callback );
    jQuery('.wp-list-table').on( 'change', '.wprss-toggle-feed-state', toggle_feed_state_ajax_callback );

	// Make the number rollers change their value to empty string when value is 0, making
	// them use the placeholder.
	jQuery('.wprss-number-roller').on('change', function(){
		if ( jQuery(this).val() == 0 )
			jQuery(this).val('');
	});


    /* JS for admin notice - Leave a review */


    // Check to see if the Ajax Notification is visible
    if ( jQuery('#dismiss-ajax-notification').length > 0 ) {
        NOTIFICATION = jQuery('#ajax-notification');
        NOTIFICATION_DISMISS = jQuery('#dismiss-ajax-notification');
        NOTIFICATION_DISMISS.click( function(evt){
            evt.preventDefault();
            evt.stopPropagation();

            jQuery.post(ajaxurl, {
                // The name of the function to fire on the server
                action: 'wprss_hide_admin_notification',
                // The nonce value to send for the security check
                nonce: jQuery.trim( jQuery('#ajax-notification-nonce').text() )
            }, function (response) {
                // If the response was successful (that is, 1 was returned), hide the notification;
                // Otherwise, we'll change the class name of the notification
                if ( response !== '1' ) {
                    NOTIFICATION.removeClass('updated').addClass('error');
                } // end if
            });

            NOTIFICATION.fadeOut(400);
        });
    }


    if ( jQuery('#wprss_tracking_notice') ) {
        
    }

    // GENERATES A RANDOM STRING FOR THE SECURE RESET CODE FIELD
    jQuery('#wprss-secure-reset-generate').click( function(){
        jQuery('input#wprss-secure-reset-code').val( Math.random().toString(36).substr(2) );
    });

});


/**
 * WP-like collapsing settings in metabox 
 */
(function($, wprss_admin_custom){
    $(window).on('load', function() {

		// Adds the Bulk Add button
		$('<a>').text( wprss_admin_custom.bulk_add ).attr('href', wprss_urls.import_export).addClass('add-new-h2').insertAfter( $('.add-new-h2') );
		
        // Initialize all collapsable meta settings
        $('.wprss-meta-slider').each(function(){
            // Get all required elements
            var slider = $(this);
            var viewerID = slider.attr('data-collapse-viewer');
            var viewer = $( '#' + viewerID );
            var editLink = viewer.next();

            var hybrid = slider.attr('data-hybrid');
            var fields = ( typeof hybrid !== 'undefined' && hybrid !== false ) ? $( hybrid ) : slider.find('*').first();

            var labelAttr = slider.attr('data-label');
            var label = ( typeof labelAttr !== 'undefined' && labelAttr !== false ) ? $( labelAttr ) : null;

            // The controller is the field that, when using hybrid fields, determines if the value is empty or not
            var controllerAttr = slider.attr( 'data-empty-controller' );
            var controller = ( typeof controllerAttr !== 'undefined' && controllerAttr !== false ) ? $( controllerAttr ) : null;

            var labelWhenEmpty = null;
            if ( label !== null ) {
                var whenEmpty = label.attr('data-when-empty');
                labelWhenEmpty = ( typeof whenEmpty !== 'undefined' && whenEmpty !== false ) ? whenEmpty : label.text();
            }

            var defaultValue = slider.attr('data-default-value');
            // Edit link opens the settings
            editLink.click(function( e ){
                // If not open already, open it
                if ( !slider.hasClass('wprss-open') )
                    slider.slideDown().addClass('wprss-open');
                e.preventDefault();
                fields.each( function(){
                    $(this).attr( 'data-old-value', $(this).val() );
                });
            });

            // The update function
            var update = function(){
                // On click, get the value of the fields
                var val = '';
                fields.each( function(){
                    if ( $(this).is('select') ) {
                        val += ' ' + $( this ).find('option:selected').text();
                    }
                    else val += ' ' + $(this).val();
                });
                // check the controller
                var controllerVal = '=';
                if ( controller !== null ) {
                    if ( controller.is('select') ) {
                        controllerVal = ' ' + controller.find('option:selected').text();
                    } else controllerVal = ' ' + controller.val();
                }
                // If empty, use the default value
                if ( val.trim() === '' || controllerVal.trim() === '' ) {
                    val = defaultValue;
                    // If the label is set, and it has alternate text for empty values, switch its text and attr
                    if ( label !== null ) {
                        var whenEmpty = label.attr('data-when-empty');
                        var labelWhenEmpty = ( typeof whenEmpty !== 'undefined' && whenEmpty !== false ) ? whenEmpty : null;
                        if ( labelWhenEmpty !== null ) {
                            label.attr( 'data-when-not-empty', label.text() ).text( labelWhenEmpty );
                        }
                    }
                }
                // Otherwise if the value is not empty, and the label is set to its empty counterpart, switch it back
                else {
                    if ( label !== null ) {
                        var whenNotEmpty = label.attr('data-when-not-empty');
                        var labelWhenNotEmpty = ( typeof whenNotEmpty !== 'undefined' && whenNotEmpty !== false ) ? whenNotEmpty : null;
                        if ( labelWhenNotEmpty !== null ) {
                            label.attr( 'data-when-empty', label.text() ).text( labelWhenNotEmpty );
                        }
                    }
                }
                // Set the text of the viewer to the value
                viewer.text( val );
            };

            // Create the OK Button
            var okBtn = $('<a>').addClass('wprss-slider-button button-secondary').text( wprss_admin_custom.ok ).click( update );
            // Create the Cancel Button
            var cancelBtn = $('<a>').addClass('wprss-slider-button').text( wprss_admin_custom.cancel ).click( function() {
                fields.each( function(){
                    $(this).val( $(this).attr( 'data-old-value' ) );
                    $(this).removeAttr( 'data-old-value' );
                });
            });

            // Add the buttons
            slider.append( $('<br>') ).append( $('<br>') ).append( okBtn ).append( cancelBtn );

            // Make both buttons close the div
            slider.find('.wprss-slider-button').click( function(){
                slider.slideUp().removeClass('wprss-open');
            });

            // Update when ready
            update();
        });

    });
})(jQuery, wprss_admin_custom);


// For Blacklist
(function($, wprss_admin_custom) {
	$(document).ready( function(){
		if ( $('body').hasClass('post-type-wprss_blacklist') ) {
			
			
			
			$('<p>').text( wprss_admin_custom.blacklist_desc )
			.insertBefore( $('.tablenav.top') );
			
			
			// Construct the bulk delete button
			$('<a>').addClass('button').attr('href', '#').text( wprss_admin_custom.blacklist_remove )
			// Add it to the page
			.appendTo( $('div.tablenav.top div.bulkactions') )
			// Bind the click event
			.click( function(e){
				var ids = [];
				$('table.wp-list-table tbody th.check-column').each( function(){
					var checkbox = $(this).find('input[type="checkbox"]');
					if ( checkbox.is(':checked') ) {
						var idAttr = checkbox.attr('id');
						var id = idAttr.split('-')[2];
						ids.push( id );
					}
				});
				var id_str = ids.join();
                var blacklist_selected_url = wprss_admin_custom.blacklist_remove_url + id_str;
				$(this).attr('href', blacklist_selected_url);
				//e.preventDefault();
			});
			
			// Unlink the titles in the table
			$('table.wp-list-table a.row-title').contents().unwrap();
		}
	});
})(jQuery, wprss_admin_custom);


// Utility string trim method, if it does not exist
if ( !String.prototype.trim ) {
    String.prototype.trim = function(){
        return this.replace(/^\s+|\s+$/g, '');
    };
}

// For add-ons page
(function($) {
    $(window).on('load', function() {
        $('#add-ons .add-on-group').each(function(){                
            var $el = $(this),
                h = 0;                                        
            $el.find('.add-on').each(function(){                        
                h = Math.max( $(this).height(), h );                        
            });                    
            $el.find('.add-on').height( h );                    
        });                
    });            
})(jQuery);

// The WPRA debug log
(function($, undefined) {
    /**
     * Creates a new WPRA log instance.
     *
     * @param el The jQuery element.
     */
    function WpraLogViewer(el)
    {
        this.el = el;
        this.filterEls = el.find('.wpra-toggle-logs');
        this.logEls = el.find('table tbody tr');
        this.filters = {};

        this.resetFilters();
        this.init();
        this.update();
        this.el.show();
    }

    /**
     * Initializes the instance.
     */
    WpraLogViewer.prototype.init = function () {
        var self = this;
        // Init and bind events to the filter elements
        this.filterEls.each(function () {
            var filterEl = $(this);
            var linkEl = filterEl.find('> a');
            var level = filterEl.data('level');
            var selected = filterEl.hasClass('wpra-selected');
            var count = self.getLogCount(level);

            // Add the number of logs to the text of the filter element
            linkEl.text(linkEl.text() + ' (' + count + ')')

            // If there are no logs for this filter, disable it
            if (count === 0) {
                filterEl.addClass('wpra-log-filter-disabled');
                return;
            }

            // Bind the click event
            linkEl.click(function () {
                self.filters[level] = !self.filters[level];
                self.update();
            });
        });
    }

    /**
     * Updates the entire element.
     */
    WpraLogViewer.prototype.update = function () {
        this.updateFilters();
        this.updateLogs();
    };

    /**
     * Updates the logs.
     */
    WpraLogViewer.prototype.updateLogs = function () {
        // Show all logs
        this.logEls.show();

        // If the "all" filter is enabled, do nothing else
        if (this.filters.all) {
            return;
        }

        // Hide logs whose filter is disabled
        for (var level in this.filters) {
            if (!this.filters[level]) {
                this.el.find('table tbody tr.wpra-log-' + level).hide();
            }
        }
    };

    /**
     * Updates the filters.
     */
    WpraLogViewer.prototype.updateFilters = function () {
        // Whether or not the filters are all selected (except for the "all" filter)
        var allFiltersSelected = true;

        var self = this;
        this.filterEls.each(function () {
            var filterEl = $(this);
            var level = filterEl.data('level');

            // Ignore the "all" filter and disabled filters
            if (level === 'all' || filterEl.hasClass('wpra-log-filter-disabled')) {
                return;
            }

            // The filter is marked selected if: its explicitly true in the `filters` map
            // or the "all" filter is selected
            var selected = self.filters[level] || self.filters.all;
            // Update the elements "selected" class
            filterEl.toggleClass('wpra-selected', selected);
            // AND the flag so that if at least one filter is not selected, "allFiltersSelected" is false
            allFiltersSelected = allFiltersSelected && selected;
        });

        // Toggle the "all" filter link's class based on whether all filters are selected
        this.filterEls.filter('[data-level="all"]').toggleClass('wpra-selected', allFiltersSelected);
    },

    /**
     * Counts the logs for a given log level.
     *
     * @param level The log level to count.
     */
    WpraLogViewer.prototype.getLogCount = function (level) {
        var countEls = this.logEls;

        if (level !== 'all') {
            countEls = countEls.filter('.wpra-log-' + level);
        }

        return countEls.length;
    };

    /**
     * Resets the filters.
     */
    WpraLogViewer.prototype.resetFilters = function () {
        this.filters = {
            all: false,
            info: true,
            error: true,
            warning: false,
            notice: false,
            debug: false,
        };
    };

    $(document).ready(function() {
        window.WpraLogViewers = [];
        // Init each WPRA log on the page
        $('.wpra-log').each(function () {
            WpraLogViewers.push(new WpraLogViewer($(this)));
        });
    });
})(jQuery);

// Image options in feed source edit page
(function($) {
    function update() {
        var f2pTypeSelector = $('select#wprss_ftp_post_type');
        var showMetaBox = (f2pTypeSelector.length === 0) || (f2pTypeSelector.val() === 'wprss_feed_item');

        var ftImage = $('#wpra_ft_image').val();
        var downloadImages = $('#wpra_download_images').prop('checked') === true;
        var ftImagesEnabled = (ftImage !== '');
        var useDefaultFtImage = (ftImage === 'default');

        // Only show the meta box when F2P is not active, or it is and the Post Type is "Feed Item"
        $('#wpra-images').toggle(showMetaBox);

        // Only show the "must have ft image" and "remove ft image" options if featured images are enabled
        $('#wpra_siphon_ft_image').toggle(ftImagesEnabled);
        $('#wpra_must_have_ft_image').toggle(ftImagesEnabled);

        // Show the image minimum size options if either featured images or image downloading are enabled
        $('#wpra_image_min_size_row').toggle( (ftImagesEnabled || downloadImages) && !useDefaultFtImage );
    }

    $(document).ready(function () {
        var defFtImage = $('#wprss-feed-def-ft-image');

        if (defFtImage.length) {
            $('#wpra_ft_image').on('change', update);
            $('#wpra_download_images').on('change', update);

            // If the F2P Post Type selector is on the page, update the image options when the post type selection changes
            var f2pTypeSelector = $('select#wprss_ftp_post_type');
            if (f2pTypeSelector.length) {
                f2pTypeSelector.on('change', update);
            }

            update();

            var gallery = new WpraGallery({
                id: 'wpra-feed-def-ft-image',
                title: "Choose a default featured image",
                button: "Set default featured image",
                library: {type: 'image'},
                multiple: false,
                elements: {
                    value: defFtImage,
                    open: $('#wprss-feed-set-def-ft-image'),
                    remove: $('#wprss-feed-remove-def-ft-image'),
                    preview: $('#wprss-feed-def-ft-image-preview'),
                    previewHint: $('#wprss-feed-def-ft-image-preview-hint'),
                },
            });
        }

    });
})(jQuery);
