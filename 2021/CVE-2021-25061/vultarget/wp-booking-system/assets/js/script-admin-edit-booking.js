jQuery(function ($) {

    var wpbs_bookings_page = 1;
    var wpbs_bookings_posts_per_page = 5;

    /**
     * Sorting
     * 
     */
    function wpbs_bookings_sort() {
        // Get select values
        var sort_by = $("#wpbs-bookings-order-by").val() ? $("#wpbs-bookings-order-by").val() : 'id';
        var sort_order = $("#wpbs-bookings-order").val() ? $("#wpbs-bookings-order").val() : 'desc';

        // Save fields in a variable
        var bookings = $(".wpbs-bookings-tab.active .wpbs-booking-field");

        // Do the actual sorting
        bookings.sort(function (a, b) {
            if (sort_order == 'asc') {
                return wpbs_bookings_sort_asc(a, b, sort_by);
            }
            return wpbs_bookings_sort_desc(a, b, sort_by);
        });

        // Put back the fields in the correct order
        $(".wpbs-bookings-tab.active").html(bookings);

        // Reset pagination
        wpbs_bookings_reset_pagination();

    }
    // ASC sorting function
    function wpbs_bookings_sort_asc(a, b, sort_by) {
        return $(a).data(sort_by) - $(b).data(sort_by)
    }

    // DESC sorting function
    function wpbs_bookings_sort_desc(a, b, sort_by) {
        return $(b).data(sort_by) - $(a).data(sort_by)
    }

    /**
     * Tab Count
     * 
     */
    function wpbs_bookings_tab_count() {
        $(".wpbs-bookings-tab-navigation li").each(function () {
            $li = $(this);
            $li.find('.count').text('(' + $("#" + $li.find('a').data('tab')).find('.wpbs-booking-field:not(.hidden)').length + ')');
        })
    }

    /**
     * Pagination Function
     * 
     */

    function wpbs_bookings_pagination() {

        // Show the pagination after script has loaded
        $(".wpbs-bookings-pagination").show();

        // Get total number of items and total number of pages
        total = $(".wpbs-bookings-tab.active .wpbs-booking-field:not(.hidden)").length
        pages = Math.ceil(total / wpbs_bookings_posts_per_page);

        // If there aren't enough results, hide the pagination
        if (pages <= 1) {
            $(".wpbs-bookings-pagination").hide();
        }

        // Change pagination interface numbers
        $("#wpbs-bookings-postbox .displaying-num").text(total + ' bookings');
        $("#wpbs-bookings-postbox .current-page").text(wpbs_bookings_page);
        $("#wpbs-bookings-postbox .total-pages").text(pages);

        // Hide all fields
        $(".wpbs-bookings-tab.active .wpbs-booking-field").hide();

        // And display the ones on the current page
        for (var i = (wpbs_bookings_page - 1) * wpbs_bookings_posts_per_page; i < wpbs_bookings_posts_per_page * wpbs_bookings_page; i++) {
            $(".wpbs-bookings-tab.active .wpbs-booking-field:not(.hidden)").eq(i).show();
        }

        // Disable or enable buttons depending on the page we're on
        if (wpbs_bookings_page == 1) {
            $(".prev-page, .first-page").removeClass('button')
            $(".prev-page span, .first-page span").addClass('button disabled');
        } else {
            $(".prev-page, .first-page").addClass('button')
            $(".prev-page span, .first-page span").removeClass('button disabled');
        }

        if (wpbs_bookings_page == pages) {
            $(".next-page, .last-page").removeClass('button')
            $(".next-page span, .last-page span").addClass('button disabled');
        } else {
            $(".next-page, .last-page").addClass('button')
            $(".next-page span, .last-page span").removeClass('button disabled');
        }

        // Resize layout
        wpbs_bookings_dynamic_layout();
    }

    /**
     * Reset Pagination
     * 
     */

    function wpbs_bookings_reset_pagination() {
        wpbs_bookings_page = 1;
        $("#wpbs-bookings-search").val('');
        $(".wpbs-booking-field").removeClass('hidden').show();
        wpbs_bookings_pagination();
    }

    /**
     * Custom jQuery :selector
     * 
     */
    jQuery.expr[':'].wpbs_icontains = function (a, i, m) {
        return jQuery(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    /**
     * Set the widths for the booking desctiption columns
     * 
     */
    function wpbs_bookings_dynamic_layout() {

        // Set same width for check in dates
        var max_check_in_date = 0;
        $(".wpbs-booking-field-check-in-date").width('auto').each(function () {
            if ($(this).outerWidth(true) > max_check_in_date) {
                max_check_in_date = $(this).outerWidth(true);
            }
        });
        $(".wpbs-booking-field-check-in-date").width(max_check_in_date);

        //Set same width for check out dates
        var max_check_in_date = 0;
        $(".wpbs-booking-field-check-out-date").width('auto').each(function () {
            if ($(this).outerWidth(true) > max_check_in_date) {
                max_check_in_date = $(this).outerWidth(true);
            }
        });
        $(".wpbs-booking-field-check-out-date").width(max_check_in_date);
        
        for (var i = 1; i < 20; i++) {
            var span_width = 0;
            $("p.wpbs-booking-field-details > span:nth-child(" + i + ")").width('auto').each(function () {
                if ($(this).outerWidth(true) > span_width) {
                    span_width = $(this).outerWidth(true);
                }
            });
            $("p.wpbs-booking-field-details > span:nth-child(" + i + ")").width(span_width);
        }
    }

    // Set Sizes
    wpbs_bookings_dynamic_layout();
    $(window).resize(wpbs_bookings_dynamic_layout);

    // Tab counts
    wpbs_bookings_tab_count();

    // Initialize Pagination
    wpbs_bookings_pagination();

    /**
     * Sorting boxes handlers
     * 
     */

    $("#wpbs-bookings-order-by, #wpbs-bookings-order").change(function () {
        wpbs_bookings_sort();
    })

    /**
     * Tabs Click Handler
     */
    $(".wpbs-bookings-tab-navigation a").click(function (e) {
        e.preventDefault();
        $a = $(this);

        $(".wpbs-bookings-tab-navigation a").removeClass('current');
        $a.addClass('current');

        $(".wpbs-bookings-tab").removeClass('active').hide();

        $("p.wpbs-bookings-no-results").hide();
        $("p.wpbs-bookings-no-search-results").hide();

        $tab = $("#" + $a.data('tab'));
        $tab.addClass('active').show();

        if (!$tab.find('.wpbs-booking-field').length) {
            $('.wpbs-bookings-no-results strong').text($a.find('span.label').text())
            $('.wpbs-bookings-no-results').show();
        }

        wpbs_bookings_sort();

        wpbs_bookings_reset_pagination();

        wpbs_bookings_dynamic_layout();
    })

    /**
     * Open first tab Handler
     * 
     * Checks to see if there are new bookings and shows them. If not, show the Accepted bookings tab.
     * 
     */

    if (!$("#wpbs-bookings-tab-pending > .wpbs-booking-field").length) {
        $('.wpbs-bookings-tab-navigation a[data-tab="wpbs-bookings-tab-accepted"]').trigger('click');
    }


    /**
     * Search
     */
    $("#wpbs-bookings").on('keyup change search', '#wpbs-bookings-search', function () {
        val = $(this).val();

        // Split query to match each word
        var words = val.split(' ');

        // Hide all fields
        $('.wpbs-bookings-tab.active .wpbs-booking-field').hide().addClass('hidden');

        // Hide no-results message
        $("p.wpbs-bookings-no-search-results").hide();

        // Loop through fields
        $('.wpbs-bookings-tab.active .wpbs-booking-field').each(function () {
            var found = 0;
            var $field = $(this);

            //Loop though words
            words.forEach(function (word) {
                if ($field.find('.wpbs-booking-field-details span span:wpbs_icontains("' + word + '")').length) {
                    found++;
                }
            })
            if (found == words.length) {
                // Show only matched fields
                $field.show().removeClass('hidden');
            }
        })

        // Reset search
        wpbs_bookings_page = 1;
        wpbs_bookings_pagination();

        // Check if there are no results
        if (!$('.wpbs-bookings-tab.active .wpbs-booking-field:visible').length) {
            $("p.wpbs-bookings-no-search-results strong").text(val);
            $("p.wpbs-bookings-no-search-results").show();
        }

        wpbs_bookings_dynamic_layout();


    })

    /**
     * Pagination Navigation
     * 
     */

    // First Page
    $(".wpbs-bookings-pagination").on('click', '.first-page.button', function (e) {
        e.preventDefault();
        wpbs_bookings_page = 1;
        wpbs_bookings_pagination();
    })

    // Previous Page
    $(".wpbs-bookings-pagination").on('click', '.prev-page.button', function (e) {
        e.preventDefault();
        wpbs_bookings_page--;
        wpbs_bookings_pagination();
    })

    // Next Page
    $(".wpbs-bookings-pagination").on('click', '.next-page.button', function (e) {
        e.preventDefault();
        wpbs_bookings_page++;
        wpbs_bookings_pagination();
    })

    // Last Page
    $(".wpbs-bookings-pagination").on('click', '.last-page.button', function (e) {
        e.preventDefault();
        wpbs_bookings_page = Math.ceil($(".wpbs-bookings-tab.active .wpbs-booking-field:not(.hidden)").length / wpbs_bookings_posts_per_page);
        wpbs_bookings_pagination();
    })


    /**
     * Booking Details
     * 
     */

    // Open Modal
    $(document).on('click', '.wpbs-open-booking-details', function (e) {
        e.preventDefault();
        $a = $(this);

        // Remove the "NEW" tag
        if ($a.hasClass('wpbs-booking-field-is-read-0')) {
            $a.removeClass('wpbs-booking-field-is-read-0').addClass('wpbs-booking-field-is-read-1')
            $a.find('.wpbs-booking-field-new-booking').remove();
        }

        $("html").css('overflow', 'hidden');

        booking_id = parseInt($a.data('id'));

        // Set ajaxurl in the front-end
        if (typeof wpbs_ajaxurl != 'undefined')
            ajaxurl = wpbs_ajaxurl;
        // Prepare data
        data = {
            action: 'wpbs_open_booking_details',
            wpbs_token : wpbs_localized_data_booking.open_bookings_token,
            id: booking_id
        }

        // Add the overlay
        $("body").append('<div id="wpbs-booking-details-modal-overlay" />');
        $("#wpbs-booking-details-modal-overlay").animate({ opacity: 1 }, 400);

        // Make the request
        $.post(ajaxurl, data, function (response) {

            $("#wpbs-booking-details-modal-overlay").html(response);
            $("#wpbs-booking-details-modal-inner").animate({ opacity: 1 }, 400);


            // Hacky-hack to make wp_editor work :(
            $(".wpbs-wp-editor-ajax").each(function () {
                var tiny_mce_id = $(this).data('id');

                tinyMCE.execCommand('mceRemoveEditor', true, tiny_mce_id);

                tinyMCE.init(tinyMCEPreInit.mceInit['wpbs_placeholder_editor']);
                tinyMCE.execCommand('mceAddEditor', true, tiny_mce_id);

                setTimeout(function () {
                    quicktags({ id: tiny_mce_id });
                }, 1000)
            })
        });
    });

    // Close Modal
    $(document).on('click', '#wpbs-booking-details-modal-close, #wpbs-booking-details-modal-overlay', function (e) {
        e.preventDefault();

        // Remove the overlay
        $("#wpbs-booking-details-modal-overlay").animate({ opacity: 0 }, 400, function () {
            $("html").css('overflow', 'visible');
            $("#wpbs-booking-details-modal-overlay").remove();
            wpbs_calendar_editor_dynamic_layout();
        });
    });


    // Add stop propagation to inner modal
    $(document).on('click', '#wpbs-booking-details-modal-inner', function (e) {
        e.stopPropagation();
    });


    // Bind escape key to close the modal
    $(document).keyup(function (e) {
        if (e.key === "Escape" && $("#wpbs-booking-details-modal-overlay").length) {
            $("#wpbs-booking-details-modal-overlay").animate({ opacity: 0 }, 400, function () {
                $("#wpbs-booking-details-modal-overlay").remove();
            });
        };
    });

    $(document).on('change', '#wpbs-booking-details-modal-inner h3 .wpbs-notification-toggle', function () {
        $(this).parents('.wpbs-tab').find(".wpbs-booking-details-modal-email-wrapper").toggleClass('wpbs-booking-details-modal-email-wrapper-show');
    })


    /**
	 * Handles the saving of the calendar by making an AJAX call to the server
	 * with the wpbs_calendar_data.
	 *
	 * Upon success refreshes the page and adds a success message
	 *
	 */
    $(document).on('click', '.wpbs-action-update-booking', function (e) {

        e.preventDefault();

        wpbs_form_submitting = true;

        $button = $(this);

        if ($button.data('action') == 'delete' && !confirm("Are you sure you want to delete this booking?")){
            return false;
        }

        

        // Trigger MCE Save so .serialize() will work
        tinyMCE.triggerSave();

        // Prepare data
        var form_data = $('.wpbs-wrap-edit-calendar form').serialize();
        var email_form_data = $('.wpbs-booking-details-modal-accept-booking-email form').serialize();
        var data = {
            action: 'wpbs_save_calendar_data',
            form_data: form_data,
            email_form_data: email_form_data,
            booking_action: $button.data('action'),
            calendar_data: JSON.stringify(wpbs_calendar_data),
            booking_id: $button.data('booking-id'),
            current_year: $('.wpbs-container').data('current_year'),
            current_month: $('.wpbs-container').data('current_month')
        }

        // Disable all buttons
        $('#wpbs-booking-details-modal-inner input, #wpbs-booking-details-modal-inner select, #wpbs-booking-details-modal-inner textarea, #wpbs-booking-details-modal-inner button').attr('disabled', true);

        // Send the request
        $.post(ajaxurl, data, function (response) {
            if (typeof response != 'undefined')
                window.location.replace(response);
        });

    });


    /**
     * Permanently Delete Booking Confirmation
     */
    $(document).on('click', '.wpbs-permanently-delete-booking', function (e) {

        if (!confirm("Are you sure you want to permanently delete this booking?"))
            return false;
        
    })

    

    /**
     * Highlight all Booking IDs when hovering
     */
    $(document).on('mouseenter', '.wpbs-calendar-date .wpbs-calendar-date-booking-id', function(){
        id = $(this).data('id');
        $('.wpbs-calendar-date-booking-id[data-id="'+id+'"]').addClass('hover');
    });

    $(document).on('mouseleave', '.wpbs-calendar-date .wpbs-calendar-date-booking-id', function(){
        $('.wpbs-calendar-date-booking-id').removeClass('hover');
    });
    

});