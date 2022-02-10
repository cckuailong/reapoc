jQuery(function ($) {

    /**
     * Resizes the calendar to always have square dates
     *
     */
    function resize_calendar($calendars_wrapper) {

        /**
         * Set variables
         *
         */
        var $months_wrapper = $calendars_wrapper.find('.wpbs-calendars-wrapper');
        var $months_wrapper_width = $calendars_wrapper.find('.wpbs-calendars');
        var calendar_min_width = $calendars_wrapper.data('min_width');
        var calendar_max_width = $calendars_wrapper.data('max_width');

        var $month_inner = $calendars_wrapper.find('.wpbs-calendar-wrapper');

        /**
         * Set the calendar months min and max width from the data attributes
         *
         */
        if ($calendars_wrapper.data('min_width') > 0)
            $calendars_wrapper.find('.wpbs-calendar').css('min-width', calendar_min_width);

        if ($calendars_wrapper.data('max_width') > 0)
            $calendars_wrapper.find('.wpbs-calendar').css('max-width', calendar_max_width)


        /**
         * Set the column count
         *
         */
        var column_count = 0;

        if ($months_wrapper_width.width() < calendar_min_width * 2)
            column_count = 1;

        else if ($months_wrapper_width.width() < calendar_min_width * 3)
            column_count = 2;

        else if ($months_wrapper_width.width() < calendar_min_width * 4)
            column_count = 3;

        else if ($months_wrapper_width.width() < calendar_min_width * 6)
            column_count = 4;

        else
            column_count = 6;


        // Adjust for when there are fewer months in a calendar than columns
        if ($calendars_wrapper.find('.wpbs-calendar').length <= column_count)
            column_count = $calendars_wrapper.find('.wpbs-calendar').length;

        // Set column count
        $calendars_wrapper.attr('data-columns', column_count);


        /**
         * Set the max-width of the calendars container that has a side legend
         *
         */
        if ($months_wrapper.hasClass('wpbs-legend-position-side')) {

            $months_wrapper.css('max-width', 'none');
            $months_wrapper.css('max-width', $calendars_wrapper.find('.wpbs-calendar').first().outerWidth(true) * column_count);

        }


        /**
         * Handle the height of each date
         *
         */
        var td_width = $calendars_wrapper.find('td').first().width();

        $calendars_wrapper.find('td .wpbs-date-inner, td .wpbs-week-number').css('height', Math.ceil(td_width) + 1 + 'px');
        $calendars_wrapper.find('td .wpbs-date-inner, td .wpbs-week-number').css('line-height', Math.ceil(td_width) + 1 + 'px');

        var th_height = $calendars_wrapper.find('th').first().height();
        $calendars_wrapper.find('th').css('height', Math.ceil(th_height) + 1 + 'px');

        /**
         * Set calendar month height
         *
         */
        var calendar_month_height = 0;

        $month_inner.css('min-height', '1px');

        $month_inner.each(function () {

            if ($(this).height() >= calendar_month_height)
                calendar_month_height = $(this).height();

        });

        $month_inner.css('min-height', Math.ceil(calendar_month_height) + 'px');

        /**
         * Show the calendars
         *
         */
        $calendars_wrapper.css('visibility', 'visible');

    }



    /**
     * Refreshed the output of the calendar with the given data
     *
     */
    function refresh_calendar($calendar_container, current_year, current_month) {

        var $calendar_container = $calendar_container;

        if ($calendar_container.hasClass('wpbs-is-loading'))
            return false;

        /**
         * Prepare the calendar data
         *
         */
        var data = $calendar_container.data();

        data['action'] = 'wpbs_refresh_calendar';
        data['current_year'] = current_year;
        data['current_month'] = current_month;

        /**
         * Add loading animation
         *
         */
        $calendar_container.find('.wpbs-calendar').append('<div class="wpbs-overlay"><div class="wpbs-overlay-spinner"><div class="wpbs-overlay-bounce1"></div><div class="wpbs-overlay-bounce2"></div><div class="wpbs-overlay-bounce3"></div></div></div>');
        $calendar_container.addClass('wpbs-is-loading');
        $calendar_container.find('select').attr('disabled', true);

        /**
         * Set ajaxurl in the front-end
         *
         */
        if (typeof wpbs_ajaxurl != 'undefined')
            ajaxurl = wpbs_ajaxurl;


        /**
         * Make the request
         *
         */
        $.post(ajaxurl, data, function (response) {

            $calendar_container.replaceWith(response);

            $('.wpbs-container').each(function () {
                resize_calendar($(this));
                wpbs_mark_selected_dates($(this).parents('.wpbs-main-wrapper'));
                wpbs_set_off_screen_date_limits($(this).parents('.wpbs-main-wrapper'));

                if (!$(this).siblings('form').length) {
                    $(this).removeClass('wpbs-enable-hover')
                }
            });


        });

    }




    /**
     * Resize the calendars on page load
     *
     */
    $('.wpbs-container').each(function () {
        resize_calendar($(this));
    });

    /**
     * Resize the calendars on page resize
     *
     */
    $(window).on('resize', function () {

        $('.wpbs-container').each(function () {
            resize_calendar($(this));
        });

    });


    /**
     * Handles the navigation of the Previous button
     *
     */
    $(document).on('click', '.wpbs-container .wpbs-prev', function (e) {

        e.preventDefault();

        // Set container
        var $container = $(this).closest('.wpbs-container');

        // Set the current year and month that are displayed in the calendar
        var current_month = $container.data('current_month');
        var current_year = $container.data('current_year');

        // Calculate the 
        var navigate_count = 1;

      
        for (var i = 1; i <= navigate_count; i++) {

            current_month -= 1;

            if (current_month < 1) {
                current_month = 12;
                current_year -= 1;
            }

        }

        refresh_calendar($container, current_year, current_month);

    });

    /**
     * Handles the navigation of the Next button
     *
     */
    $(document).on('click', '.wpbs-container .wpbs-next', function (e) {

        e.preventDefault();

        // Set container
        var $container = $(this).closest('.wpbs-container');

        // Set the current year and month that are displayed in the calendar
        var current_month = $container.data('current_month');
        var current_year = $container.data('current_year');

        // Calculate the 
        var navigate_count = 1;

        

        for (var i = 1; i <= navigate_count; i++) {

            current_month += 1;

            if (current_month > 12) {
                current_month = 1;
                current_year += 1;
            }

        }

        refresh_calendar($container, current_year, current_month);

    });

    /**
     * Handles the navigation of the Month Selector for the Single Calendar
     *
     */
    $(document).on('change', '.wpbs-container .wpbs-select-container select', function () {

        // Set container
        var $container = $(this).closest('.wpbs-container');

        var date = new Date($(this).val() * 1000);

        var year = date.getFullYear();
        var month = date.getMonth() + 1;

        refresh_calendar($container, year, month);

    });




    /**
     * Show Payment method description
     * 
     */
    $(document).on("change", ".wpbs-form-field-payment_method input[type='radio']", function () {
        $(this).parents('.wpbs-form-field-payment_method').find('p.wpbs-payment-method-description-open').removeClass('wpbs-payment-method-description-open');
        $(this).parent().next('p').addClass('wpbs-payment-method-description-open');
    })

    /***********************************
     * Form Scripts
     *
     **********************************/


    /**
     * Submitting the form
     * 
     */

    $(".wpbs-main-wrapper").on('submit', '.wpbs-form-container', function (e) {
        e.preventDefault();

        $form = $(this);

        var $calendar = $form.parents('.wpbs-main-wrapper').find('.wpbs-container');

        /**
         * Set ajaxurl in the front-end
         *
         */
        if (typeof wpbs_ajaxurl != 'undefined')
            ajaxurl = wpbs_ajaxurl;

        /**
         * Prepare the calendar data
         *
         */
        var data = {};

        data['action'] = 'wpbs_submit_form';

        data['form'] = $form.data()
        data['calendar'] = $calendar.data();

        data['wpbs_token'] = wpbs_ajax.token;
        data['form_data'] = $form.serialize();



        $.post(ajaxurl, data, function (response) {

            response = JSON.parse(response);

            // If validation failed, we show the form again
            if (response.success === false) {
                $form.replaceWith(response.html);
                wpbs_render_recaptcha();

                // Scroll to the top of the calendar
                $('html, body').stop().animate({scrollTop: $calendar.parents('.wpbs-main-wrapper').offset().top})


                // If validation succeeded, we show the form confirmation
            } else if (response.success === true) {

                // Message
                if (response.confirmation_type == 'message') {
                    if ($form.parents('.wpbs-payment-confirmation').length) {
                        $form.parents('.wpbs-payment-confirmation').replaceWith('<div class="wpbs-form-confirmation-message"><p>' + response.confirmation_message + '</p></div>');
                    } else {
                        $form.replaceWith('<div class="wpbs-form-confirmation-message"><p>' + response.confirmation_message + '</p></div>');
                        
                        // Scroll to the top of the calendar
                        $('html, body').stop().animate({scrollTop: $calendar.parents('.wpbs-main-wrapper').offset().top})
                    }

                    // Refresh the Calendar

                    // Clear Selection
                    $calendar_instance = $calendar.parents('.wpbs-main-wrapper');
                    wpbs_remove_selection_dates($calendar_instance);
                    $calendar_instance.data('future_date_limit', 'infinite');
                    $calendar_instance.data('past_date_limit', 'infinite');

                    var current_month = $calendar.data('current_month');
                    var current_year = $calendar.data('current_year');
                    refresh_calendar($calendar, current_year, current_month);


                    // Redirect
                } else if (response.confirmation_type == 'redirect') {
                    window.location.href = response.confirmation_redirect_url;
                }

            }
        });
    })


    /**
     * Render all captchas on Window Load
     * 
     */
    $(window).on('load', function () {
        wpbs_render_recaptcha();
    })

    /**
     * Function that renders the Google reCAPTCHA
     * 
     */
    function wpbs_render_recaptcha() {
        if (!$(".wpbs-google-recaptcha").length) return;

        $(".wpbs-google-recaptcha").each(function () {
            $recaptcha = $(this);

            if ($recaptcha.find('iframe').length) {
                return true;
            }

            grecaptcha.render($recaptcha.attr('id'), {
                'sitekey': $recaptcha.data('sitekey')
            });
        })

    }


    /***********************************
     * Calendar Selection Scripts
     *
     **********************************/

    /**
     * Handle date selection clicks
     * 
     */
    $(document).on('click', '.wpbs-container .wpbs-is-bookable', function () {

        $el = $(this);

        $calendar_instance = $el.parents('.wpbs-main-wrapper');

        // Exit if the user clicks on a calendar gap.
        if ($el.hasClass('wpbs-gap')) {
            return false;
        }

        // Exit if there is no form attached to the calendar
        if ($calendar_instance.hasClass('wpbs-main-wrapper-form-0')) {
            return false;
        }

        if (!$calendar_instance.find('.wpbs-form-container').length){
            return false;
        }



        if (wpbs_get_selection_start_date($calendar_instance) === false) {
            // No dates selected

            // Set off-screen limits
            $calendar_instance.data('future_date_limit', 'infinite');
            $calendar_instance.data('past_date_limit', 'infinite');

            // Set starting date
            wpbs_set_selection_start_date(wpbs_get_element_date($el), $calendar_instance);

            // Search for limits
            wpbs_set_off_screen_date_limits($calendar_instance);

            // Trigger mouseenter
            if (!wpbs_is_touch_device())
                $el.trigger('mouseenter');

        } else if (wpbs_get_selection_start_date($calendar_instance) !== false && wpbs_get_selection_end_date($calendar_instance) === false) {
            // Only start day is selected

            // Don't allow user to click and end selection on an invalid date
            if (!$el.hasClass('wpbs-date-hover')) {
                return false;
            }

            // Set ending date
            wpbs_set_selection_end_date(wpbs_get_element_date($el), $calendar_instance);

            // Select the dates
            wpbs_mark_selected_dates($calendar_instance);

            // Enable CSS hovering
            $calendar_instance.find('.wpbs-container').addClass('wpbs-enable-hover');

        } else if (wpbs_get_selection_start_date($calendar_instance) !== false && wpbs_get_selection_end_date($calendar_instance) !== false) {
            // Both start and end day selected, clear selection and start selection again.

            // Clear Selection
            wpbs_remove_selection_dates($calendar_instance);

            // Set off-screen limits
            $calendar_instance.data('future_date_limit', 'infinite');
            $calendar_instance.data('past_date_limit', 'infinite');

            // Set starting date
            wpbs_set_selection_start_date(wpbs_get_element_date($el), $calendar_instance);

            // Search for limits
            wpbs_set_off_screen_date_limits($calendar_instance);

            // Trigger mouseenter
            if (!wpbs_is_touch_device())
                $el.trigger('mouseenter');
        }

    });

    /**
     * Mouseenter event on dates
     * 
     */
    $(document).on('mouseenter', '.wpbs-container .wpbs-is-bookable', function () {

        $el = $(this);

        $calendar_instance = $el.parents('.wpbs-main-wrapper');

        // Exit if there is no form attached to the calendar
        if ($calendar_instance.hasClass('wpbs-main-wrapper-form-0')) {
            return false;
        }

        // Only hover if start date is selected and end date is empty.
        if (wpbs_get_selection_start_date($calendar_instance) === false || wpbs_get_selection_end_date($calendar_instance) !== false) return false;


        // Disable CSS hovering
        $calendar_instance.find('.wpbs-container').removeClass('wpbs-enable-hover');

        // The date we're hovering on
        current_date = wpbs_get_element_date($el);

        // The starting date
        selection_start_date = wpbs_get_selection_start_date($calendar_instance);
        // Clear all hovers and add them again below
        $calendar_instance.find('.wpbs-container .wpbs-date').removeClass('wpbs-date-hover');

        // The loops
        if (current_date > selection_start_date) {
            // Forward selection
            start_date = selection_start_date;
            end_date = current_date;

            // Loop through dates
            for (var i = start_date; i <= end_date; i.setUTCDate(i.getUTCDate() + 1)) {
                if (wpbs_mark_hover_selection(i, $calendar_instance) === false) break;
            }

        } else {
            // Backward selection
            start_date = current_date;
            end_date = selection_start_date;

            // Loop through dates
            for (var i = end_date; i >= start_date; i.setUTCDate(i.getUTCDate() - 1)) {
                if (wpbs_mark_hover_selection(i, $calendar_instance) === false) break;
            }
        }

        

    })

    /**
     * Set selection start date
     * 
     */
    function wpbs_set_selection_start_date(date, $calendar_instance) {
        $calendar_instance.find(".wpbs-container").data('start_date', date.getTime());
    }

    /**
     * Set selection end date
     * 
     */
    function wpbs_set_selection_end_date(date, $calendar_instance) {

        start_date = wpbs_get_selection_start_date($calendar_instance);

        if (start_date.getTime() > date) {
            // If start date is greater than end date, put them in the correct order.
            wpbs_set_selection_start_date(date, $calendar_instance);
            start_date.setUTCDate(start_date.getUTCDate());
            $calendar_instance.find(".wpbs-container").data('end_date', start_date.getTime());
        } else {
            // If not, just save end date as is.
            $calendar_instance.find(".wpbs-container").data('end_date', date.getTime());
        }
    }

    /**
     * Get selection start date
     * 
     */
    function wpbs_get_selection_start_date($calendar_instance) {
        if (typeof $calendar_instance.find(".wpbs-container").data('start_date') === 'undefined' || $calendar_instance.find(".wpbs-container").data('start_date') == "") {
            return false;
        }
        date = new Date($calendar_instance.find(".wpbs-container").data('start_date'))
        return date;
    }

    /**
     * Get selection end date
     * 
     */
    function wpbs_get_selection_end_date($calendar_instance) {
        if (typeof $calendar_instance.find(".wpbs-container").data('end_date') === 'undefined' || $calendar_instance.find(".wpbs-container").data('end_date') == "") {
            return false;
        }
        date = new Date($calendar_instance.find(".wpbs-container").data('end_date'))
        return date;
    }

    /**
     * Clear date selection
     * 
     */
    function wpbs_remove_selection_dates($calendar_instance) {
        $calendar_instance.find(".wpbs-container").data('start_date', false);
        $calendar_instance.find(".wpbs-container").data('end_date', false);
        $calendar_instance.find('.wpbs-container .wpbs-date').removeClass('wpbs-date-selected');
        $calendar_instance.find('.wpbs-container .wpbs-date').removeClass('wpbs-date-hover');

        $calendar_instance.find(".wpbs-container .wpbs-date").removeClass('wpbs-selected-first').removeClass('wpbs-selected-last');
        $calendar_instance.find(".wpbs-container .wpbs-date .wpbs-legend-icon-select").remove();

        $calendar_instance.data('future_date_limit', 'infinite');
        $calendar_instance.data('past_date_limit', 'infinite');

    }

    /**
     * Handle date hovering classes
     * 
     * @param date 
     */
    function wpbs_mark_hover_selection(date, $calendar_instance) {
        $el = $calendar_instance.find('.wpbs-container .wpbs-date[data-day="' + date.getUTCDate() + '"][data-month="' + (date.getUTCMonth() + 1) + '"][data-year="' + date.getUTCFullYear() + '"]');
        // Check if date is bookable
        if ($el.length && !$el.hasClass('wpbs-is-bookable')) return false;

        // Check if we are hovering over changeovers
        changeover_start = $calendar_instance.find(".wpbs-container").data('changeover_start');
        changeover_end = $calendar_instance.find(".wpbs-container").data('changeover_end');

        if (changeover_start && changeover_end) {

            var hovered_dates = {};

            // Create an object with the hovered dates

            // Add hovered elements
            $calendar_instance.find('.wpbs-date-hover').each(function () {
                hovered_date_legend = 'normal';
                if ($(this).hasClass('wpbs-legend-item-' + changeover_start)) hovered_date_legend = 'start';
                if ($(this).hasClass('wpbs-legend-item-' + changeover_end)) hovered_date_legend = 'end';
                hovered_dates["" + $(this).data('year') + $(this).data('month') + $(this).data('day')] = hovered_date_legend;
            })

            // Add current element as well
            hovered_date_legend = 'normal';
            if ($el.hasClass('wpbs-legend-item-' + changeover_start)) hovered_date_legend = 'start';
            if ($el.hasClass('wpbs-legend-item-' + changeover_end)) hovered_date_legend = 'end';
            hovered_dates["" + $el.data('year') + $el.data('month') + $el.data('day')] = hovered_date_legend;

            // The rule is that if a start changeover exists in an array, we shouln't allow the selection past an end changeover

            // Assume no start date found
            start_date_found = false;

            // Wether or not we should exit the selection
            exit_selection = false;

            // Loop through the object
            $.each(hovered_dates, function (date, hovered_date_legend) {
                // We found a starting date
                if (hovered_date_legend == 'start') start_date_found = true;

                // Now if we find an ending date and a starting date was previously found, we exit.
                if (hovered_date_legend == 'end' && start_date_found == true) {
                    exit_selection = true
                    return;
                }
            })

            // Exit here as well.
            if (exit_selection == true) {
                return false;
            }

        }

        // When dates are off screen, we save limits

        // Past date limit
        if ($calendar_instance.data('past_date_limit') != 'infinite' && date.getTime() < $calendar_instance.data('past_date_limit')) return false;

        //Future date limit
        if ($calendar_instance.data('future_date_limit') != 'infinite' && date.getTime() > $calendar_instance.data('future_date_limit')) return false;

        $el.addClass('wpbs-date-hover');

        return true;
    }

    /**
     * Handle date selection classes
     * 
     * @param date 
     */
    function wpbs_mark_selection(date, $calendar_instance) {
        $el = $calendar_instance.find('.wpbs-container .wpbs-date[data-day="' + date.getUTCDate() + '"][data-month="' + (date.getUTCMonth() + 1) + '"][data-year="' + date.getUTCFullYear() + '"]');
        $el.addClass('wpbs-date-selected');
    }

    /**
     * Handle date selection classes for split start
     * 
     * @param date 
     */
    function wpbs_mark_selection_split_start(date, $calendar_instance) {

        $el = $calendar_instance.find('.wpbs-container .wpbs-date[data-day="' + date.getUTCDate() + '"][data-month="' + (date.getUTCMonth() + 1) + '"][data-year="' + date.getUTCFullYear() + '"]');
        $el.addClass('wpbs-selected-first').find('.wpbs-legend-item-icon').append('<div class="wpbs-legend-icon-select"><svg height="100%" width="100%" viewBox="0 0 50 50" preserveAspectRatio="none"><polygon points="0,50 50,50 50,0" /></svg></div>');
    }

    /**
     * Handle date selection for split end
     * 
     * @param date 
     */
    function wpbs_mark_selection_split_end(date, $calendar_instance) {
        $el = $calendar_instance.find('.wpbs-container .wpbs-date[data-day="' + date.getUTCDate() + '"][data-month="' + (date.getUTCMonth() + 1) + '"][data-year="' + date.getUTCFullYear() + '"]');
        $el.addClass('wpbs-selected-last').find('.wpbs-legend-item-icon').append('<div class="wpbs-legend-icon-select"><svg height="100%" width="100%" viewBox="0 0 50 50" preserveAspectRatio="none"><polygon points="0,0 0,50 50,0" /></svg></div>');
    }

    /**
     * Handle date selection classes
     * 
     * @param date 
     */
    function wpbs_mark_selected_dates($calendar_instance) {
        // Check if start and end dates exist
        if (wpbs_get_selection_start_date($calendar_instance) === false) return;
        if (wpbs_get_selection_end_date($calendar_instance) === false) return;

        // Remove existing classes
        $calendar_instance.find(".wpbs-date").removeClass('wpbs-date-selected');
        $calendar_instance.find(".wpbs-date").removeClass('wpbs-date-hover');

        // Loop through dates
        for (var i = wpbs_get_selection_start_date($calendar_instance); i <= wpbs_get_selection_end_date($calendar_instance); i.setUTCDate(i.getUTCDate() + 1)) {
            wpbs_mark_selection(i, $calendar_instance);
        }


    }

    /**
     * Verify the limits of the next and previous dates
     * 
     */
    function wpbs_set_off_screen_date_limits($calendar_instance) {
        // Check if no starting date was selected
        if (wpbs_get_selection_start_date($calendar_instance) === false) {
            return false;
        }

        // If we already found both limits, stop looking
        if ($calendar_instance.data('future_date_limit') != 'infinite' && $calendar_instance.data('past_date_limit') != 'infinite') {
            return false;
        }

        var future_dates = [];
        var past_dates = [];
        var selected_date = wpbs_get_selection_start_date($calendar_instance).getTime();

        // Loop through all visible dates and search for a limit
        $calendar_instance.find('.wpbs-date').not('.wpbs-is-bookable').not('.wpbs-gap').each(function () {
            date = wpbs_get_element_date($(this)).getTime();
            if (date > selected_date) {
                future_dates.push(date);
            } else {
                past_dates.push(date);
            }
        })

        //Sort and save nearest limit
        if (future_dates.length && $calendar_instance.data('future_date_limit') == 'infinite') {
            future_dates.sort();
            $calendar_instance.data('future_date_limit', future_dates[0]);
        }

        if (past_dates.length && $calendar_instance.data('past_date_limit') == 'infinite') {
            past_dates.sort().reverse();
            $calendar_instance.data('past_date_limit', past_dates[0]);
        }
    }

    /**
     * Transform a .wpbs-date elements data attributes into a Date() object
     * 
     * @param $el 
     */
    function wpbs_get_element_date($el) {
        date = new Date(Date.UTC($el.data('year'), $el.data('month') - 1, $el.data('day'), 0, 0, 0));
        return date;
    }




    /**
     * Check for touch device
     * 
     */
    function wpbs_is_touch_device() {
        var prefixes = ' -webkit- -moz- -o- -ms- '.split(' ');
        var mq = function (query) {
            return window.matchMedia(query).matches;
        }

        if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
            return true;
        }

        var query = ['(', prefixes.join('touch-enabled),('), 'heartz', ')'].join('');
        return mq(query);
    }

});


