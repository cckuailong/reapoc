jQuery(function ($) {

	/*
     * Strips one query argument from a given URL string
     *
     */
    function remove_query_arg(key, sourceURL) {

        var rtn = sourceURL.split("?")[0],
            param,
            params_arr = [],
            queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";

        if (queryString !== "") {
            params_arr = queryString.split("&");
            for (var i = params_arr.length - 1; i >= 0; i -= 1) {
                param = params_arr[i].split("=")[0];
                if (param === key) {
                    params_arr.splice(i, 1);
                }
            }

            rtn = rtn + "?" + params_arr.join("&");

        }

        if (rtn.split("?")[1] == "") {
            rtn = rtn.split("?")[0];
        }

        return rtn;
    }


    /*
     * Adds an argument name, value pair to a given URL string
     *
     */
    function add_query_arg(key, value, sourceURL) {

        return sourceURL + '&' + key + '=' + value;

    }


	/**
	 * Initialize colorpicker
	 *
	 */
    $('.wpbs-colorpicker').wpColorPicker();

	/**
	 * Initialize Chosen
	 *
	 */
    if (typeof $.fn.chosen != 'undefined') {

        $('.wpbs-chosen').chosen();

    }

	/**
	 * Links that have the inactive class should do nothing
	 *
	 */
    $(document).on('click', 'a.wpbs-inactive, input[type=submit].wpbs-inactive', function () {

        return false;

    });

	/**
	 * Initialize the sortable function on the Calendar Legend List Table
	 *
	 */
    $('table.wpbs_legend_items tbody').sortable({
        handle: '.wpbs-move-legend-item',
        containment: '#wpcontent',
        placeholder: 'wpbs-list-table-sort-placeholder',
        helper: function (e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();

            $helper.children().each(function (index) {
                // Set helper cell sizes to match the original sizes
                $(this).width($originals.eq(index).width());
            });

            return $helper;
        },
        update: function (e, ui) {

            var legend_item_ids = [];

            $('table.wpbs_legend_items tbody tr .wpbs-move-legend-item').each(function () {
                legend_item_ids.push($(this).data('id'));
            })

            var data = {
                action: 'wpbs_sort_legend_items',
                token: $('[name="wpbs_token"]').val(),
                calendar_id: $('[name="calendar_id"]').val(),
                legend_item_ids: legend_item_ids
            }

            // Add table wrapper and overlay
            $('table.wpbs_legend_items').wrap('<div class="wpbs-wp-list-table-wrapper"></div>');
            $('table.wpbs_legend_items').closest('.wpbs-wp-list-table-wrapper').append('<div class="wpbs-overlay"><div class="spinner"></div></div>');

            // Make sort ajax call
            $.post(ajaxurl, data, function (response) {

                response = JSON.parse(response);

                if (!response.success) {

                    window.location.replace(response.redirect_url_error);

                } else {

                    // Remove table wrapper and overlay
                    $('table.wpbs_legend_items').siblings('.wpbs-overlay').remove();
                    $('table.wpbs_legend_items').unwrap('.wpbs-wp-list-table-wrapper');

                }


            });

        }
    });

    $('table.wpbs_legend_items tbody').disableSelection();


	/**
	 * Handle show/hide of the second color option for Legend Item add/edit screen
	 *
	 */
    $(document).on('change', 'select[name="legend_item_type"]', function () {

        if ($(this).val() == 'single')
            $('#wpbs-legend-item-color-2').closest('.wp-picker-container').hide();
        else
            $('#wpbs-legend-item-color-2').closest('.wp-picker-container').show();

    });

    $(document).ready(function () {

        if ($('select[name="legend_item_type"]').length > 0)
            $('select[name="legend_item_type"]').trigger('change');


        if ($("body.wp-booking-system_page_wpbs-calendars .wp-list-table.wpbs_legend_items.wpbs_legend_items").length > 0 && $(window).width() < 1100) {
            $("body.wp-booking-system_page_wpbs-calendars .wp-list-table.wpbs_legend_items.wpbs_legend_items #the-list tr").each(function () {
                $tr = $(this);
                $tr.find('td.name.column-name a.row-title').clone().addClass('remove-link').insertAfter($tr.find('td.sort.column-sort .wpbs-move-legend-item.ui-sortable-handle'));
                $tr.find('td .remove-link').removeAttr('href')
            });
        };

    });

	/**
	 * Tab Navigation
	 *
	 */
    $(document).on('click', '.wpbs-nav-tab', function (e) {
        e.preventDefault();

        // Nav Tab activation
        $('.wpbs-nav-tab').removeClass('wpbs-active').removeClass('nav-tab-active');
        $(this).addClass('wpbs-active').addClass('nav-tab-active');

        // Show tab
        $('.wpbs-tab').removeClass('wpbs-active');

        var nav_tab = $(this).attr('data-tab');
        $('.wpbs-tab[data-tab="' + nav_tab + '"]').addClass('wpbs-active');
        $('input[name=active_tab]').val(nav_tab);

        // Change http referrer
        $_wp_http_referer = $('input[name=_wp_http_referer]');

        var _wp_http_referer = $_wp_http_referer.val();

        if (_wp_http_referer) {
            _wp_http_referer = remove_query_arg('tab', _wp_http_referer);
            $_wp_http_referer.val(add_query_arg('tab', $(this).attr('data-tab'), _wp_http_referer));
        }


    });



    /**
	 * Calendar Title Translations Toggle
	 */
    $(".wrap.wpbs-wrap-edit-calendar #titlediv .titlewrap-toggle").click(function (e) {
        e.preventDefault();
        $(this).toggleClass('open');
        $(".titlewrap-translations").slideToggle();

    });

    /**
     * Toggle settings translations
     * 
     */
    $(".wpbs-wrap").on('click', '.wpbs-settings-field-show-translations', function (e) {
        e.preventDefault();
        $(this).parents('.wpbs-settings-field-translation-wrapper').find(".wpbs-settings-field-translations").slideToggle();
        $(this).toggleClass('open');
    })


	/**
	 * Modifies the modal inner height to permit the scrollbar to function properly
	 *
	 */
    $(window).resize(function () {

        $('.wpbs-modal-inner').outerHeight($('.wpbs-modal.wpbs-active').outerHeight() - $('.wpbs-modal.wpbs-active .wpbs-modal-header').outerHeight() - $('.wpbs-modal.wpbs-active .wpbs-modal-nav-tab-wrapper').outerHeight());

    });

	/**
	 * Close modal window
	 *
	 */
    $(document).on('click', '.wpbs-modal-close', function (e) {

        e.preventDefault();

        $(this).closest('.wpbs-modal').find('.wpbs-modal-inner').scrollTop(0);

        $(this).closest('.wpbs-modal').removeClass('wpbs-active');
        $(this).closest('.wpbs-modal').siblings('.wpbs-modal-overlay').removeClass('wpbs-active');

        $(window).resize();

    });

	/**
	 * Close modal on clicking the modal overlay
	 *
	 */
    $(document).on('click', '.wpbs-modal-overlay.wpbs-active', function (e) {

        $('.wpbs-modal.wpbs-active').find('.wpbs-modal-close').click();

    });

	/**
	 * Open Shortcode Generator modal
	 *
	 */
    $(document).on('click', '#wpbs-shortcode-generator-button', function (e) {

        e.preventDefault();

        $('#wpbs-modal-add-calendar-shortcode, #wpbs-modal-add-calendar-shortcode-overlay').addClass('wpbs-active');

        $(window).resize();

        $('.wpbs-modal.wpbs-active').click();

    });

	/**
	 * Builds the shortcode for the Single Calendar and inserts it in the WordPress text editor
	 *
	 */
    $(document).on('click', '#wpbs-insert-shortcode-single-calendar', function (e) {

        e.preventDefault();

        // Begin shortcode
        var shortcode = '[wpbs ';

        $('#wpbs-modal-add-calendar-shortcode.wpbs-active .wpbs-shortcode-generator-field-calendar').each(function () {

            shortcode += $(this).data('attribute') + '="' + $(this).val() + '" ';

        });

        // End shortcode
        shortcode = shortcode.trim();
        shortcode += ']';

        window.send_to_editor(shortcode);

        $(this).closest('.wpbs-modal').find('.wpbs-modal-close').first().trigger('click');

    });




	/**
	 * Register and deregister website functionality
	 *
	 */
    $(document).on('click', '#wpbs-register-website-button, #wpbs-deregister-website-button', function (e) {

        e.preventDefault();

        window.location = add_query_arg('serial_key', $('[name="serial_key"]').val(), $(this).attr('href'));

    });

    $(document).on('click', '#wpbs-check-for-updates-button', function (e) {

        if ($(this).attr('disabled') == 'disabled')
            e.preventDefault();

    });


    /**
     * Toggle wrapper fields
     */
    $(document).on('change', '.wpbs-settings-wrap-toggle', function () {
        $(this).closest('.wpbs-tab').find(".wpbs-settings-wrapper").toggleClass('wpbs-settings-wrapper-show');
    })

	/**
	 * Move the calendar from the sidebar to the main content and back in the calendar edit screen 
	 * when resizing the window
	 *
	 */
    $(window).on('resize', function () {

        // Move the calendar from the sidebar to the main content
        if ($(window).innerWidth() < 850) {

            $('.wpbs-container').closest('.postbox').detach().prependTo('#post-body-content');

        } else {

            $('.wpbs-container').closest('.postbox').detach().prependTo('#postbox-container-1');

        }

    });

    $(window).trigger('resize');


    /**
     * Dnyamically calculate calendar editor Bookings column width.
     * 
     */
    $(document).ready(function () {
        wpbs_calendar_editor_dynamic_layout();
    })

    $(window).on('resize', wpbs_calendar_editor_dynamic_layout);


    /**
     * Submit the form when changing the ledeng's auto pending <select>
     */

    $(".wpbs-auto-accept-booking-as").change(function () {
        $(this).parents('form').submit();
    })

    
});

function wpbs_calendar_editor_dynamic_layout() {

    jQuery(".wpbs-wrap-edit-calendar .wpbs-calendar-date-booking-ids, .wpbs-wrap-edit-calendar .wpbs-calendar-date-booking-ids-header").css('min-width', 0);

    wpbs_bookings_max_width = 0;
    jQuery(".wpbs-wrap-edit-calendar .wpbs-calendar-date-booking-ids").each(function () {
        if (jQuery(this).width() > wpbs_bookings_max_width) {
            wpbs_bookings_max_width = jQuery(this).width();
        }
    })

    jQuery(".wpbs-wrap-edit-calendar .wpbs-calendar-date-booking-ids, .wpbs-wrap-edit-calendar .wpbs-calendar-date-booking-ids-header").css('min-width', wpbs_bookings_max_width + 1);


    jQuery(".wpbs-wrap-edit-calendar .wpbs-calendar-date-description, .wpbs-wrap-edit-calendar .wpbs-calendar-date-description-header").css('width', (jQuery(".wpbs-calendar-editor").width() - (187 + wpbs_bookings_max_width + 7 + 7 + 1)));

   

    jQuery(".wpbs-calendar-editor").css({ opacity: 1 });
};

jQuery(document).ready(function () {
    // Hide booking IDs on mobile if empty
    jQuery(".wpbs-calendar-date-booking-id").each(function () {
        $this = $(this);
        if ($this.text() == '\xa0') {
            $this.parent().addClass('hide');
        };
    });
});

jQuery(window).on('load resize', function(){
    jQuery(".wpbs-wrap-upgrade-to-premium ul li").wpbs_adjust_height();
})


/***** Adjust Height Function *****/
jQuery.fn.wpbs_adjust_height = function () {
    var maxHeightFound = 0;
    this.css('min-height', '1px');

    if (this.is('a')) {
        this.removeClass('loaded');
    };

    this.each(function () {
        if ($(this).outerHeight() > maxHeightFound) {
            maxHeightFound = $(this).outerHeight();
        }
    });
    this.css('min-height', maxHeightFound);
    if (this.is('a')) {
        this.addClass('loaded');
    };
};