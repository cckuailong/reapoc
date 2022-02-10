// MEC Single Event Displayer
var mecSingleEventDisplayer =
{
    getSinglePage: function(id, occurrence, time, ajaxurl, layout, image_popup)
    {
        if(jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
        jQuery('.mec-modal-result').addClass('mec-modal-preloader');

        jQuery.ajax(
        {
            url: ajaxurl,
            data: "action=mec_load_single_page&id=" + id + (occurrence != null ? "&occurrence=" + occurrence : "") + (time != null ? "&time=" + time : "") + "&layout=" + layout,
            type: "get",
            success: function(response)
            {
                jQuery('.mec-modal-result').removeClass("mec-modal-preloader");
                jQuery.featherlight(response);

                setTimeout(function()
                {
                    if(typeof grecaptcha !== 'undefined' && jQuery('#g-recaptcha').length > 0)
                    {
                        grecaptcha.render("g-recaptcha", {
                            sitekey: mecdata.recapcha_key
                        });
                    }
                }, 1000);

                if(image_popup != 0)
                {
                    if(jQuery('.featherlight-content .mec-events-content a img').length > 0)
                    {
                        jQuery('.featherlight-content .mec-events-content a img').each(function()
                        {
                            jQuery(this).closest('a').attr('data-featherlight', 'image');
                        });
                    }
                }
                else
                {
                    jQuery('.featherlight-content .mec-events-content a img').remove();
                    jQuery('.featherlight-content .mec-events-content img').remove();
                }

                if(typeof mecdata.enableSingleFluent != 'undefined' && mecdata.enableSingleFluent) mecFluentSinglePage();
            },
            error: function () { }
        });
    }
};

var mec_search_callback1;
var mec_search_callback2;

// MEC SEARCH FORM PLUGIN
(function ($) {
    $.fn.mecSearchForm = function (options) {
        if(typeof settings != 'undefined') console.log(settings.callback);
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            refine: 0,
            ajax_url: '',
            search_form_element: '',
            atts: '',
            callback: function () { }
        }, options);

        if(typeof mec_search_callback1 === 'undefined') mec_search_callback1 = settings.callback;
        else if(typeof mec_search_callback2 === 'undefined') mec_search_callback2 = settings.callback;

        var $event_cost_min = $("#mec_sf_event_cost_min_" + settings.id);
        var $event_cost_max = $("#mec_sf_event_cost_max_" + settings.id);
        var $time_start = $("#mec_sf_timepicker_start_" + settings.id);
        var $time_end = $("#mec_sf_timepicker_end_" + settings.id);
        var $s = $("#mec_sf_s_" + settings.id);
        var $address = $("#mec_sf_address_s_" + settings.id);
        var $date_start = $('#mec_sf_date_start_' + settings.id);
        var $date_end = $('#mec_sf_date_end_' + settings.id);
        var $event_type = $('#mec_sf_event_type_' + settings.id);
        var $event_type_2 = $('#mec_sf_event_type_2_' + settings.id);
        var $attribute = $('#mec_sf_attribute_' + settings.id);
        var $reset = $("#mec_search_form_" + settings.id + '_reset');
        var last_field;

        // Trigger
        trigger();

        $s.on('change', function (e) {
            last_field = 's';
            search();
        });

        $address.on('change', function (e) {
            last_field = 'address';
            search();
        });

        $event_cost_min.on('change', function (e) {
            last_field = 'cost-min';
            $event_cost_max.attr('min', $(this).val());
            search();
        });

        $event_cost_max.on('change', function (e) {
            last_field = 'cost-max';
            $event_cost_min.attr('max', $(this).val());
            search();
        });

        // Timepicker
        if ($time_start.length) {
            var format = (($time_start.data('format') === 12) ? 'hh:mm p' : 'HH:mm');
            $time_start.timepicker(
            {
                timeFormat: format,
                minTime: new Date(0, 0, 0, 0, 0, 0),
                maxTime: new Date(0, 0, 0, 23, 55, 0),
                interval: 5,
                dropdown: false,
                change: function () {
                    last_field = 'time-start';
                    search();
                }
            });

            $time_end.timepicker(
            {
                timeFormat: format,
                minTime: new Date(0, 0, 0, 0, 0, 0),
                maxTime: new Date(0, 0, 0, 23, 55, 0),
                interval: 5,
                dropdown: false,
                change: function () {
                    last_field = 'time-end';
                    search();
                }
            });
        }

        var $month = $("#mec_sf_month_" + settings.id);
        var $year = $("#mec_sf_year_" + settings.id);
        var $month_or_year = $("#mec_sf_month_" + settings.id + ', ' + "#mec_sf_year_" + settings.id);

        $month_or_year.on('change', function (e) {
            last_field = 'date-dropdown';

            var mec_month_val = $month.val();
            var mec_year_val = $year.val();

            if ((mec_month_val !== 'none' && mec_year_val !== 'none') || ((mec_month_val === 'none' && mec_year_val === 'none'))) search();
        });

        $date_end.on('change', function () {
            last_field = 'date-end';
            search();
        });

        $event_type.on('change', function (e) {
            last_field = 'event-type';
            search();
        });

        $event_type_2.on('change', function (e) {
            last_field = 'event-type-2';
            search();
        });

        $attribute.on('change', function (e) {
            last_field = 'attribute';
            search();
        });

        if (settings.fields && settings.fields.length > 0) {
            for (var k in settings.fields) {
                $("#mec_sf_" + settings.fields[k] + '_' + settings.id).on('change', function (e) {
                    search();
                });
            }
        }

        // Reset
        if ($reset.length) {
            $reset.on('click', function (e) {
                reset();
            });
        }

        function trigger() {
            $("#mec_sf_category_" + settings.id).off('change').on('change', function (e) {
                last_field = 'category';
                search();
            });

            $("#mec_sf_location_" + settings.id).off('change').on('change', function (e) {
                last_field = 'location';
                search();
            });

            $("#mec_sf_organizer_" + settings.id).off('change').on('change', function (e) {
                last_field = 'organizer';
                search();
            });

            $("#mec_sf_speaker_" + settings.id).off('change').on('change', function (e) {
                last_field = 'speaker';
                search();
            });

            $("#mec_sf_tag_" + settings.id).off('change').on('change', function (e) {
                last_field = 'tag';
                search();
            });

            $("#mec_sf_label_" + settings.id).off('change').on('change', function (e) {
                last_field = 'label';
                search();
            });
        }

        function search() {
            var $category = $("#mec_sf_category_" + settings.id);
            var $location = $("#mec_sf_location_" + settings.id);
            var $organizer = $("#mec_sf_organizer_" + settings.id);
            var $speaker = $("#mec_sf_speaker_" + settings.id);
            var $tag = $("#mec_sf_tag_" + settings.id);
            var $label = $("#mec_sf_label_" + settings.id);

            var s = $s.length ? $s.val() : '';
            var address = $address.length ? $address.val() : '';
            var location = $location.length ? $location.val() : '';
            var organizer = $organizer.length ? $organizer.val() : '';
            var speaker = $speaker.length ? $speaker.val() : '';
            var tag = $tag.length ? $tag.val() : '';
            var label = $label.length ? $label.val() : '';
            var month = $month.length ? $month.val() : '';
            var year = $year.length ? $year.val() : '';
            var event_type = $event_type.length ? $event_type.val() : '';
            var event_type_2 = $event_type_2.length ? $event_type_2.val() : '';
            var attribute = $attribute.length ? $attribute.val() : '';

            var start = $date_start.length ? $date_start.val() : '';
            var end = $date_end.length ? $date_end.val() : '';

            var cost_min = $event_cost_min.length ? $event_cost_min.val() : '';
            var cost_max = $event_cost_max.length ? $event_cost_max.val() : '';

            var time_start = $time_start.length ? $time_start.val() : '';
            var time_end = $time_end.length ? $time_end.val() : '';

            var category;
            if ($category.prop('tagName') && $category.prop('tagName').toLowerCase() === 'div') {
                category = '';
                $category.find($('select')).each(function () {
                    category += $(this).val() + ',';
                });
            }
            else category = $category.length ? $category.val() : '';

            if (year === 'none' && month === 'none') {
                year = '';
                month = '';
            }

            var addation_attr = '';
            if (settings.fields && settings.fields.length > 0) {
                for (var k in settings.fields) {
                    var field = '#mec_sf_' + settings.fields[k] + '_' + settings.id;
                    var val = $(field).length ? $(field).val() : '';

                    addation_attr += '&sf[' + settings.fields[k] + ']=' + val;
                }
            }

            // Search Parameters
            var sf = 'sf[s]=' + s + '&sf[address]=' + address + '&sf[cost-min]=' + cost_min + '&sf[cost-max]=' + cost_max + '&sf[time-start]=' + time_start + '&sf[time-end]=' + time_end + '&sf[month]=' + month + '&sf[year]=' + year + '&sf[start]=' + start + '&sf[end]=' + end + '&sf[category]=' + category + '&sf[location]=' + location + '&sf[organizer]=' + organizer + '&sf[speaker]=' + speaker + '&sf[tag]=' + tag + '&sf[label]=' + label + '&sf[event_type]=' + event_type + '&sf[event_type_2]=' + event_type_2 + '&sf[attribute]=' + attribute + addation_attr;

            // Refine Parameters
            if (settings.refine) refine(sf);

            // Attributes
            var atts = settings.atts + '&' + sf;

            // Search
            if(typeof mec_search_callback1 !== 'undefined') mec_search_callback1(atts);
            if(typeof mec_search_callback2 !== 'undefined') mec_search_callback2(atts);
        }

        function reset() {
            var $category = $("#mec_sf_category_" + settings.id);
            var $location = $("#mec_sf_location_" + settings.id);
            var $organizer = $("#mec_sf_organizer_" + settings.id);
            var $speaker = $("#mec_sf_speaker_" + settings.id);
            var $tag = $("#mec_sf_tag_" + settings.id);
            var $label = $("#mec_sf_label_" + settings.id);

            if ($category.length && $category.prop('tagName') && $category.prop('tagName').toLowerCase() === 'div') {
                $category.find($('select')).each(function () {
                    $(this).val(null).trigger('change');
                });
            }
            else if ($category.length) $category.val(null);

            if ($location.length) $location.val(null);
            if ($organizer.length) $organizer.val(null);
            if ($speaker.length) $speaker.val(null);
            if ($tag.length) $tag.val(null);
            if ($label.length) $label.val(null);
            if ($s.length) $s.val(null);
            if ($address.length) $address.val(null);
            if ($month.length) $month.val(null);
            if ($year.length) $year.val(null);
            if ($event_cost_min.length) $event_cost_min.val(null);
            if ($event_cost_max.length) $event_cost_max.val(null);
            if ($date_start.length) $date_start.val(null);
            if ($date_end.length) $date_end.val(null);
            if ($time_start.length) $time_start.val(null);
            if ($time_end.length) $time_end.val(null);

            // Search Again
            setTimeout(function () {
                search();
            }, 200);
        }

        function refine(sf) {
            var $category = $("#mec_sf_category_" + settings.id);
            var $location = $("#mec_sf_location_" + settings.id);
            var $organizer = $("#mec_sf_organizer_" + settings.id);
            var $speaker = $("#mec_sf_speaker_" + settings.id);
            var $tag = $("#mec_sf_tag_" + settings.id);
            var $label = $("#mec_sf_label_" + settings.id);

            var category_type;
            if ($category.length && $category.prop('tagName') && $category.prop('tagName').toLowerCase() === 'div') category_type = 'checkboxes';
            else if ($category.length) category_type = 'dropdown';

            $.ajax(
                {
                    url: settings.ajax_url,
                    data: "action=mec_refine_search_items&" + sf + '&last_field=' + last_field + '&category_type=' + category_type + '&id=' + settings.id,
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Categories
                        if (typeof response.categories !== 'undefined' && response.categories !== '') {
                            // Checkboxes
                            if ($category.length && $category.prop('tagName') && $category.prop('tagName').toLowerCase() === 'div') {
                                $category.html(response.categories);
                            }
                            // Dropdown
                            else if ($category.length) {
                                $category.replaceWith(response.categories)
                            }

                            // Categories Search bar
                            jQuery(".mec-searchbar-category-wrap select").select2();
                        }

                        // Locations
                        if (typeof response.locations !== 'undefined' && response.locations !== '') {
                            $location.replaceWith(response.locations)
                        }

                        // Organizers
                        if (typeof response.organizers !== 'undefined' && response.organizers !== '') {
                            $organizer.replaceWith(response.organizers)
                        }

                        // Speakers
                        if (typeof response.speakers !== 'undefined' && response.speakers !== '') {
                            $speaker.replaceWith(response.speakers)
                        }

                        // Labels
                        if (typeof response.labels !== 'undefined' && response.labels !== '') {
                            $label.replaceWith(response.labels)
                        }

                        // Tags
                        if (typeof response.tags !== 'undefined' && response.tags !== '') {
                            $tag.replaceWith(response.tags)
                        }

                        // Trigger
                        trigger();
                    },
                    error: function () { }
                });
        }
    };
}(jQuery));

jQuery(document).ready(function ($) {
    // Select2
    jQuery(".mec-fes-form-cntt #mec-location select, .mec-fes-form-cntt #mec-organizer select").select2();
    // Location select2
    jQuery("#mec_location_id").select2();
    // Organizer Select2
    jQuery("#mec_organizer_id").select2();
    // Categories Search bar
    jQuery(".mec-searchbar-category-wrap select").select2();

    jQuery(".mec-search-form.mec-totalcal-box").find(".mec-search-reset-button").parents().eq(2).addClass("mec-there-reset-button");

    jQuery(".mec-search-form.mec-totalcal-box").find(".mec-minmax-event-cost").parent().find(".mec-text-address-search").addClass("with-mec-cost");
    jQuery(".mec-search-form.mec-totalcal-box").find(".mec-text-address-search").parent().find(".mec-minmax-event-cost").addClass("with-mec-address");

    /** New Searchbar JS */
    jQuery(".mec-full-calendar-search-ends").find(".mec-text-input-search").parent().find(".mec-tab-loader").removeClass("col-md-12").addClass("col-md-6");
    jQuery(".mec-search-form.mec-totalcal-box").find(".mec-text-input-search").parent().find(".mec-date-search").parent().find(".mec-text-input-search").addClass("col-md-6");
    jQuery(".mec-search-form.mec-totalcal-box").find(".mec-text-input-search").parent().find(".mec-time-picker-search").parent().find(".mec-text-input-search").addClass("col-md-6");
    jQuery(".mec-full-calendar-search-ends").find(".mec-text-input-search").addClass("col-md-12").parent().find(".mec-time-picker-search").addClass("col-md-6");
    jQuery(".mec-search-form.mec-totalcal-box").find(".mec-date-search").parent().find(".mec-time-picker-search").addClass("with-mec-date-search");
    jQuery(".mec-search-form.mec-totalcal-box").find(".mec-time-picker-search").parent().find(".mec-date-search").addClass("with-mec-time-picker");
});

// MEC FULL CALENDAR PLUGIN
(function ($) {
    $.fn.mecFullCalendar = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            skin: '',
        }, options);

        // Set onclick Listeners
        setListeners();

        mecFluentCurrentTimePosition();
        mecFluentCustomScrollbar();

        var sf;

        function setListeners() {
            // Search Widget
            if (settings.sf.container !== '') {
                sf = $(settings.sf.container).mecSearchForm({
                    id: settings.id,
                    refine: settings.sf.refine,
                    ajax_url: settings.ajax_url,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        search();
                    }
                });
            }

            // Add the onclick event
            $("#mec_skin_" + settings.id + " .mec-totalcal-box .mec-totalcal-view span:not(.mec-fluent-more-views-icon):not(.mec-fluent-more-views-content)").on('click', function (e) {
                e.preventDefault();
                var skin = $(this).data('skin');
                var mec_month_select = $('#mec_sf_month_' + settings.id);
                var mec_year_select = $('#mec_sf_year_' + settings.id);

                if (mec_year_select.val() == 'none') {
                    mec_year_select.find('option').each(function () {
                        var option_val = $(this).val();
                        if (option_val == mecdata.current_year) mec_year_select.val(option_val);
                    });
                }

                if (mec_month_select.val() == 'none') {
                    mec_month_select.find('option').each(function () {
                        var option_val = $(this).val();
                        if (option_val == mecdata.current_month) mec_month_select.val(option_val);
                    });
                }

                if (skin == 'list' || skin == 'grid' || skin == 'agenda') {
                    var mec_filter_none = '<option class="mec-none-item" value="none">' + $('#mec-filter-none').val() + '</option>';
                    if (mec_month_select.find('.mec-none-item').length == 0) mec_month_select.prepend(mec_filter_none);
                    if (mec_year_select.find('.mec-none-item').length == 0) mec_year_select.prepend(mec_filter_none);
                }
                else {
                    if (mec_month_select.find('.mec-none-item').length != 0) mec_month_select.find('.mec-none-item').remove();
                    if (mec_year_select.find('.mec-none-item').length != 0) mec_year_select.find('.mec-none-item').remove();
                }

                $("#mec_skin_" + settings.id + " .mec-totalcal-box .mec-totalcal-view span").removeClass('mec-totalcalview-selected')
                $(this).addClass('mec-totalcalview-selected');
                if ($(this).closest('.mec-fluent-more-views-content').length > 0) {
                    $('.mec-fluent-more-views-icon').addClass('active');
                    $('.mec-fluent-more-views-content').removeClass('active');
                } else {
                    $('.mec-fluent-more-views-icon').removeClass('active');
                }

                loadSkin(skin);
            });
        }

        function loadSkin(skin) {
            // Set new Skin
            settings.skin = skin;

            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_full_calendar_switch_skin&skin=" + skin + "&" + settings.atts + "&apply_sf_date=1&sed=" + settings.sed_method,
                dataType: "json",
                type: "post",
                success: function (response) {
                    $("#mec_full_calendar_container_" + settings.id).html(response);

                    // Remove loader
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    // Focus First Active Day
                    mecFocusDay(settings);

                    mecFluentCurrentTimePosition();
                    mecFluentCustomScrollbar();

                    jQuery(document).trigger( 'mec_load_skin_success', $("#mec_full_calendar_container_" + settings.id) );
                },
                error: function () { }
            });
        }

        function search() {
            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_full_calendar_switch_skin&skin=" + settings.skin + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    $("#mec_full_calendar_container_" + settings.id).html(response);

                    // Remove loader
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    // Focus First Active Day
                    mecFocusDay(settings);

                    // Focus First Active Week
                    mec_focus_week(settings.id);

                    mecFluentCurrentTimePosition();
                    mecFluentCustomScrollbar();

                    jQuery(document).trigger( 'mec_search_success', $("#mec_full_calendar_container_" + settings.id) );
                },
                error: function () { }
            });
        }
    };

}(jQuery));

// MEC YEARLY VIEW PLUGIN
(function ($) {
    $.fn.mecYearlyView = function (options) {
        var active_year;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            id: 0,
            events_label: 'Events',
            event_label: 'Event',
            year_navigator: 0,
            atts: '',
            next_year: {},
            sf: {},
            ajax_url: '',
        }, options);

        mecFluentYearlyUI(settings.id, settings.year_id);

        // Initialize Year Navigator
        if (settings.year_navigator) initYearNavigator();

        // Set onclick Listeners
        setListeners();

        // load more
        $(document).on("click", "#mec_skin_events_" + settings.id + " .mec-load-more-button", function () {
            var year = $(this).parent().parent().parent().data('year-id');
            loadMoreButton(year);
        });

        // Search Widget
        if (settings.sf.container !== '') {
            sf = $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    active_year = $('.mec-yearly-view-wrap .mec-year-navigator').filter(function () {
                        return $(this).css('display') == "block";
                    });
                    active_year = parseInt(active_year.find('h2').text());
                    search(active_year);
                }
            });
        }

        function initYearNavigator() {
            // Add onclick event
            $("#mec_skin_" + settings.id + " .mec-load-year").off("click").on("click", function () {
                var year = $(this).data("mec-year");
                setYear(year);
            });
        }

        function search(year) {
            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_yearly_view_load_year&mec_year=" + year + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    active_year = response.current_year.year;

                    // Append Year
                    $("#mec_skin_events_" + settings.id).html('<div class="mec-year-container" id="mec_yearly_view_year_' + settings.id + '_' + response.current_year.id + '" data-year-id="' + response.current_year.id + '">' + response.year + '</div>');

                    // Append Year Navigator
                    $("#mec_skin_" + settings.id + " .mec-yearly-title-sec").append('<div class="mec-year-navigator" id="mec_year_navigator_' + settings.id + '_' + response.current_year.id + '">' + response.navigator + '</div>');

                    // Re-initialize Year Navigator
                    initYearNavigator();

                    // Set onclick Listeners
                    setListeners();

                    // Toggle Year
                    toggleYear(response.current_year.id);

                    // Remove loading Class
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    mecFluentYearlyUI(settings.id, active_year);
                    mecFluentCustomScrollbar();

                },
                error: function () { }
            });
        }

        function setYear(year, do_in_background) {
            if (typeof do_in_background === "undefined") do_in_background = false;

            var year_id = year;
            active_year = year;

            // Year exists so we just show it
            if ($("#mec_yearly_view_year_" + settings.id + "_" + year_id).length) {
                // Toggle Year
                toggleYear(year_id);
                mecFluentCustomScrollbar();
            } else {
                if (!do_in_background) {
                    // Add Loading Class
                    if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                    jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
                }

                $.ajax({
                    url: settings.ajax_url,
                    data: "action=mec_yearly_view_load_year&mec_year=" + year + "&" + settings.atts + "&apply_sf_date=0",
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Append Year
                        $("#mec_skin_events_" + settings.id).append('<div class="mec-year-container" id="mec_yearly_view_year_' + settings.id + '_' + response.current_year.id + '" data-year-id="' + response.current_year.id + '">' + response.year + '</div>');

                        // Append Year Navigator
                        $("#mec_skin_" + settings.id + " .mec-yearly-title-sec").append('<div class="mec-year-navigator" id="mec_year_navigator_' + settings.id + '_' + response.current_year.id + '">' + response.navigator + '</div>');

                        // Re-initialize Year Navigator
                        initYearNavigator();

                        // Set onclick Listeners
                        setListeners();

                        if (!do_in_background) {
                            // Toggle Year
                            toggleYear(response.current_year.id);

                            // Remove loading Class
                            $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                            // Set Year Filter values in search widget
                            $("#mec_sf_year_" + settings.id).val(year);
                        } else {
                            $("#mec_yearly_view_year_" + settings.id + "_" + response.current_year.id).hide();
                            $("#mec_year_navigator_" + settings.id + "_" + response.current_year.id).hide();
                        }
                        mecFluentYearlyUI(settings.id, year);
                        if (!do_in_background) {
                            mecFluentCustomScrollbar();
                        }
                    },
                    error: function () { }
                });
            }
        }

        function toggleYear(year_id) {
            // Toggle Year Navigator
            $("#mec_skin_" + settings.id + " .mec-year-navigator").hide();
            $("#mec_year_navigator_" + settings.id + "_" + year_id).show();

            // Toggle Year
            $("#mec_skin_" + settings.id + " .mec-year-container").hide();
            $("#mec_yearly_view_year_" + settings.id + "_" + year_id).show();
        }

        var sf;

        function setListeners() {
            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }

            // Yearly view
            $("#mec_skin_" + settings.id + " .mec-has-event a").on('click', function (e) {
                e.preventDefault();

                var des = $(this).attr('href');
                var visible = $(des).is(':visible');
                if (!visible) {
                    var year = $(des).parents('.mec-year-container').data('year-id');
                    if (year) {

                        while (!visible) {
                            loadMoreButton(year);

                            visible = $(des).is(':visible');
                        }
                    }
                }

                $('.mec-events-agenda').removeClass('mec-selected');
                $(des).closest('.mec-events-agenda').addClass('mec-selected');

                var scrollTopVal = $(des).closest('.mec-events-agenda').offset().top - 35;
                if ($(this).closest('.mec-fluent-wrap').length > 0) {
                    var parent = jQuery(this).closest('.mec-fluent-wrap').find('.mec-yearly-agenda-sec');
                    scrollTopVal = parent.scrollTop() + ($(des).closest('.mec-events-agenda').offset().top - parent.offset().top);
                    jQuery(this).closest('.mec-fluent-wrap').find('.mec-yearly-agenda-sec').getNiceScroll(0).doScrollTop(scrollTopVal - 15, 120);
                }
                else {
                    $('html, body').animate({
                        scrollTop: scrollTopVal
                    }, 300);
                }
            });
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-agenda-event-title a").off('click').on('click', function (e) {
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMoreButton(year) {
            var $max_count, $current_count = 0;
            $max_count = $("#mec_yearly_view_year_" + settings.id + "_" + year + " .mec-yearly-max").data('count');
            $current_count = $("#mec_yearly_view_year_" + settings.id + "_" + year + " .mec-util-hidden").length;

            if ($current_count > 10) {
                for (var i = 0; i < 10; i++) {
                    $("#mec_yearly_view_year_" + settings.id + "_" + year + " .mec-util-hidden").slice(0, 2).each(function () {
                        $(this).removeClass('mec-util-hidden');
                    });
                }
            }

            if ($current_count < 10 && $current_count != 0) {
                for (var j = 0; j < $current_count; j++) {
                    $("#mec_yearly_view_year_" + settings.id + "_" + year + " .mec-util-hidden").slice(0, 2).each(function () {
                        $(this).removeClass('mec-util-hidden');
                        $("#mec_yearly_view_year_" + settings.id + "_" + year + " .mec-load-more-wrap").css('display', 'none');
                    });
                }
            }
        }
    };

}(jQuery));


// MEC GENERAL CALENDAR VIEW PLUGIN
(function ($) {
    $.fn.mecGeneralCalendarView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
        }, options);

        // Set onclick Listeners
        setListeners();

        function setListeners() {
            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }
        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .fc-daygrid-event").off('click').on('click', function (e) {
                e.preventDefault();
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method || '_self' === sed_method || 'no' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).attr('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }
    };

}(jQuery));

// MEC MONTHLY VIEW PLUGIN
(function ($) {
    $.fn.mecMonthlyView = function (options) {
        var active_month;
        var active_year;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            id: 0,
            events_label: 'Events',
            event_label: 'Event',
            month_navigator: 0,
            atts: '',
            active_month: {},
            next_month: {},
            sf: {},
            ajax_url: '',
        }, options);

        // Initialize Month Navigator
        if (settings.month_navigator) initMonthNavigator();

        active_month = settings.active_month.month;
        active_year = settings.active_month.year;

        // Set onclick Listeners
        setListeners();

        // Search Widget
        if (settings.sf.container !== '') {
            sf = $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    search(active_year, active_month);
                }
            });
        }

        function initMonthNavigator() {
            // Remove the onclick event
            $("#mec_skin_" + settings.id + " .mec-load-month").off("click");

            // Add onclick event
            $("#mec_skin_" + settings.id + " .mec-load-month").on("click", function () {
                var year = $(this).data("mec-year");
                var month = $(this).data("mec-month");

                setMonth(year, month, false, true);
            });
        }

        function search(year, month) {
            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_monthly_view_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    active_month = response.current_month.month;
                    active_year = response.current_month.year;

                    // Append Month
                    $("#mec_skin_events_" + settings.id).html('<div class="mec-month-container" id="mec_monthly_view_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                    // Append Month Navigator
                    $("#mec_skin_" + settings.id + " .mec-skin-monthly-view-month-navigator-container").html('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                    // Append Events Side
                    $("#mec_skin_" + settings.id + " .mec-calendar-events-side").html('<div class="mec-month-side" id="mec_month_side_' + settings.id + '_' + response.current_month.id + '">' + response.events_side + '</div>');

                    // Re-initialize Month Navigator
                    initMonthNavigator();

                    // Set onclick Listeners
                    setListeners();

                    // Toggle Month
                    toggleMonth(response.current_month.id);

                    jQuery(document).trigger('load_calendar_data');

                    // Remove loading Class
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                },
                error: function () { }
            });
        }

        function setMonth(year, month, do_in_background, navigator_click) {
            if (typeof do_in_background === "undefined") do_in_background = false;
            navigator_click = navigator_click || false;
            var month_id = year + "" + month;

            if (!do_in_background) {
                active_month = month;
                active_year = year;
            }

            // Month exists so we just show it
            if ($("#mec_monthly_view_month_" + settings.id + "_" + month_id).length) {
                // Toggle Month
                toggleMonth(month_id);
            } else {
                if (!do_in_background) {

                    // Add Loading Class
                    if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                    jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
                }

                $.ajax({
                    url: settings.ajax_url,
                    data: "action=mec_monthly_view_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=0" + "&navigator_click=" + navigator_click,
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Append Month
                        $("#mec_skin_events_" + settings.id).append('<div class="mec-month-container" id="mec_monthly_view_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                        // Append Month Navigator
                        $("#mec_skin_" + settings.id + " .mec-skin-monthly-view-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                        // Append Events Side
                        $("#mec_skin_" + settings.id + " .mec-calendar-events-side").append('<div class="mec-month-side" id="mec_month_side_' + settings.id + '_' + response.current_month.id + '">' + response.events_side + '</div>');

                        // Re-initialize Month Navigator
                        initMonthNavigator();

                        // Set onclick Listeners
                        setListeners();

                        if (!do_in_background) {
                            // Toggle Month
                            toggleMonth(response.current_month.id);

                            // Remove loading Class
                            $('.mec-modal-result').removeClass("mec-month-navigator-loading");


                            // Set Month Filter values in search widget
                            $("#mec_sf_month_" + settings.id).val(month);
                            $("#mec_sf_year_" + settings.id).val(year);
                        } else {
                            $("#mec_monthly_view_month_" + settings.id + "_" + response.current_month.id).hide();
                            $("#mec_month_navigator_" + settings.id + "_" + response.current_month.id).hide();
                            $("#mec_month_side_" + settings.id + "_" + response.current_month.id).hide();
                        }
                        if (typeof custom_month !== undefined) var custom_month;
                        if (typeof custom_month != undefined) {
                            if (custom_month == 'true') {
                                $(".mec-month-container .mec-calendar-day").removeClass('mec-has-event');
                                $(".mec-month-container .mec-calendar-day").removeClass('mec-selected-day');
                                $('.mec-calendar-day').unbind('click');
                            }
                        }

                        jQuery(document).trigger('load_calendar_data');

                    },
                    error: function () { }
                });
            }
        }

        function toggleMonth(month_id) {
            var active_month = $("#mec_skin_" + settings.id + " .mec-month-container-selected").data("month-id");
            var active_day = $("#mec_monthly_view_month_" + settings.id + "_" + active_month + " .mec-selected-day").data("day");

            if (active_day <= 9) active_day = "0" + active_day;

            // Toggle Month Navigator
            $("#mec_skin_" + settings.id + " .mec-month-navigator").hide();
            $("#mec_month_navigator_" + settings.id + "_" + month_id).show();

            // Toggle Month
            $("#mec_skin_" + settings.id + " .mec-month-container").hide();
            $("#mec_monthly_view_month_" + settings.id + "_" + month_id).show();

            // Add selected class
            $("#mec_skin_" + settings.id + " .mec-month-container").removeClass("mec-month-container-selected");
            $("#mec_monthly_view_month_" + settings.id + "_" + month_id).addClass("mec-month-container-selected");

            // Toggle Events Side
            $("#mec_skin_" + settings.id + " .mec-month-side").hide();
            $("#mec_month_side_" + settings.id + "_" + month_id).show();
        }

        var sf;

        function setListeners() {
            // Remove the onclick event
            $("#mec_skin_" + settings.id + " .mec-has-event").off("click");

            // Add the onclick event
            $("#mec_skin_" + settings.id + " .mec-has-event").on('click', function (e) {
                // define variables
                var $this = $(this),
                    data_mec_cell = $this.data('mec-cell'),
                    month_id = $this.data('month');

                if (settings.display_all == 0) {
                    e.preventDefault();

                    $("#mec_monthly_view_month_" + settings.id + "_" + month_id + " .mec-calendar-day").removeClass('mec-selected-day');
                    $this.addClass('mec-selected-day');

                    $('#mec_month_side_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec:not([data-mec-cell=' + data_mec_cell + '])').slideUp();
                    $('#mec_month_side_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec[data-mec-cell=' + data_mec_cell + ']').slideDown();

                    $('#mec_monthly_view_month_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec:not([data-mec-cell=' + data_mec_cell + '])').slideUp();
                    $('#mec_monthly_view_month_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec[data-mec-cell=' + data_mec_cell + ']').slideDown();
                }
                else {
                    $("#mec_monthly_view_month_" + settings.id + "_" + month_id + " .mec-calendar-day").removeClass('mec-selected-day');
                    $this.addClass('mec-selected-day');
                }
            });

            mec_tooltip();

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }

            if (settings.style == 'novel') {
                if ($('.mec-single-event-novel').length > 0) {
                    $('.mec-single-event-novel').colourBrightness();
                    $('.mec-single-event-novel').each(function () {
                        $(this).colourBrightness()
                    });
                }
            }
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-title a,#mec_skin_" + settings.id + " .event-single-link-novel,#mec_skin_" + settings.id + " .mec-monthly-tooltip").off('click').on('click', function (e) {
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });

        }

        function mec_tooltip() {
            if ($('.mec-monthly-tooltip').length >= 1) {
                if (Math.max(document.documentElement.clientWidth, window.innerWidth || 0) > 768) {
                    $('.mec-monthly-tooltip').tooltipster({
                        theme: 'tooltipster-shadow',
                        interactive: true,
                        delay: 100,
                        minWidth: 350,
                        maxWidth: 350
                    });
                    if (settings.sed_method != '0') {
                        sed();
                    }
                } else {
                    var touchtime = 0;
                    $(".mec-monthly-tooltip").on("click", function (event) {
                        event.preventDefault();
                        if (touchtime == 0) {
                            $('.mec-monthly-tooltip').tooltipster({
                                theme: 'tooltipster-shadow',
                                interactive: true,
                                delay: 100,
                                minWidth: 350,
                                maxWidth: 350,
                                trigger: "custom",
                                triggerOpen: {
                                    click: true,
                                    tap: true
                                },
                                triggerClose: {
                                    click: true,
                                    tap: true
                                }
                            });
                            touchtime = new Date().getTime();
                        } else {
                            if (((new Date().getTime()) - touchtime) < 200) {
                                var el = $(this);
                                var link = el.attr("href");
                                window.location = link;
                                touchtime = 0;
                            } else {
                                touchtime = new Date().getTime();
                            }
                        }
                    });
                }
            }
        }
    };

}(jQuery));

// MEC WEEKLY VIEW PLUGIN
(function ($) {
    $.fn.mecWeeklyView = function (options) {
        var active_year;
        var active_month;
        var active_week;
        var active_week_number;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            week: 1,
            id: 0,
            current_year: null,
            current_month: null,
            changeWeekElement: '.mec-load-week',
            month_navigator: 0,
            atts: '',
            ajax_url: '',
            sf: {}
        }, options);

        // Set Active Time
        active_year = settings.current_year;
        active_month = settings.current_month;

        // Search Widget
        if (settings.sf.container !== '') {
            $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    search(active_year, active_month, active_week);
                }
            });
        }

        // Set The Week
        setThisWeek(settings.month_id + settings.week);

        // Set Listeners
        setListeners();

        // Initialize Month Navigator
        if (settings.month_navigator) initMonthNavigator(settings.month_id);

        function setListeners() {
            $(settings.changeWeekElement).off('click').on('click', function (e) {
                var week = $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active').data('week-id');
                var max_weeks = $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active').data('max-weeks');
                var new_week_number = active_week_number;

                if ($(this).hasClass('mec-previous-month')) {
                    week = parseInt(week) - 1;
                    new_week_number--;
                } else {
                    week = parseInt(week) + 1;
                    new_week_number++;
                }

                if (new_week_number <= 1 || new_week_number >= max_weeks) {
                    // Disable Next/Previous Button
                    $(this).css({
                        'opacity': .6,
                        'cursor': 'default'
                    });
                    $(this).find('i').css({
                        'opacity': .6,
                        'cursor': 'default'
                    });
                } else {
                    // Enable Next/Previous Buttons
                    $('#mec_skin_' + settings.id + ' .mec-load-week, #mec_skin_' + settings.id + ' .mec-load-week i').css({
                        'opacity': 1,
                        'cursor': 'pointer'
                    });
                }

                // Week is not in valid range
                if (new_week_number === 0 || new_week_number > max_weeks) { } else {
                    setThisWeek(week);
                }
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function setThisWeek(week, auto_focus) {
            if (typeof auto_focus === 'undefined') auto_focus = false;

            // Week is not exists
            if (!$('#mec_weekly_view_week_' + settings.id + '_' + week).length) {
                return setThisWeek((parseInt(week) - 1));
            }

            // Set week to active in week list
            $('#mec_skin_' + settings.id + ' .mec-weekly-view-week').removeClass('mec-weekly-view-week-active');
            $('#mec_weekly_view_week_' + settings.id + '_' + week).addClass('mec-weekly-view-week-active');
            $('#mec_weekly_view_top_week_' + settings.id + '_' + week).addClass('mec-weekly-view-week-active');

            // Show related events
            $('#mec_skin_' + settings.id + ' .mec-weekly-view-date-events').addClass('mec-util-hidden');
            $('.mec-weekly-view-week-' + settings.id + '-' + week).removeClass('mec-util-hidden');
            $('#mec_skin_' + settings.id + ' .mec-calendar-row').addClass('mec-util-hidden');
            $('#mec_skin_' + settings.id + ' .mec-calendar-row[data-week=' + week % 10 + ']').removeClass('mec-util-hidden');

            active_week = week;
            active_week_number = $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active').data('week-number');

            $('#mec_skin_' + settings.id + ' .mec-calendar-d-top').find('.mec-current-week').find('span').remove();
            $('#mec_skin_' + settings.id + ' .mec-calendar-d-top').find('.mec-current-week').append('<span>' + active_week_number + '</span>');

            if (active_week_number === 1) {
                // Disable Previous Button
                $('#mec_skin_' + settings.id + ' .mec-previous-month.mec-load-week').css({
                    'opacity': .6,
                    'cursor': 'default'
                });
                $('#mec_skin_' + settings.id + ' .mec-previous-month.mec-load-week').find('i').css({
                    'opacity': .6,
                    'cursor': 'default'
                });
            }

            // Go To Event Week
            if (auto_focus) mec_focus_week(settings.id);

            mecFluentCustomScrollbar();
        }

        function initMonthNavigator(month_id) {
            $('#mec_month_navigator' + settings.id + '_' + month_id + ' .mec-load-month').off('click');
            $('#mec_month_navigator' + settings.id + '_' + month_id + ' .mec-load-month').on('click', function () {
                var year = $(this).data('mec-year');
                var month = $(this).data('mec-month');

                setMonth(year, month, active_week, true);
            });
        }

        function search(year, month, week, navigation_click) {
            var week_number = (String(week).slice(-1));

            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_weekly_view_load_month&mec_year=" + year + "&mec_month=" + month + "&mec_week=" + week_number + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    // Remove Loading Class
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    // Append Month
                    $("#mec_skin_events_" + settings.id).html('<div class="mec-month-container" id="mec_weekly_view_month_' + settings.id + '_' + response.current_month.id + '">' + response.month + '</div>');

                    // Append Month Navigator
                    $("#mec_skin_" + settings.id + " .mec-skin-weekly-view-month-navigator-container").html('<div class="mec-month-navigator" id="mec_month_navigator' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                    // Set Listeners
                    setListeners();

                    // Toggle Month
                    toggleMonth(response.current_month.id);

                    // Set active week
                    setThisWeek(response.week_id, true);
                    mecFluentCustomScrollbar();
                },
                error: function () { }
            });
        }

        function setMonth(year, month, week, navigation_click) {
            var month_id = '' + year + month;
            var week_number = (String(week).slice(-1));

            active_month = month;
            active_year = year;
            navigation_click = navigation_click || false;

            // Month exists so we just show it
            if ($("#mec_weekly_view_month_" + settings.id + "_" + month_id).length) {
                // Toggle Month
                toggleMonth(month_id);

                // Set active week
                setThisWeek('' + month_id + week_number);
                mecFluentCustomScrollbar();
            } else {
                // Add Loading Class
                if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

                $.ajax({
                    url: settings.ajax_url,
                    data: "action=mec_weekly_view_load_month&mec_year=" + year + "&mec_month=" + month + "&mec_week=" + week_number + "&" + settings.atts + "&apply_sf_date=0" + "&navigator_click=" + navigation_click,
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Remove Loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        $('#mec_skin_' + settings.id + ' .mec-calendar-d-top h3').after(response.topWeeks);

                        // Append Month
                        $("#mec_skin_events_" + settings.id).append('<div class="mec-month-container" id="mec_weekly_view_month_' + settings.id + '_' + response.current_month.id + '">' + response.month + '</div>');

                        // Append Month Navigator
                        $("#mec_skin_" + settings.id + " .mec-skin-weekly-view-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                        // Set Listeners
                        setListeners();

                        // Toggle Month
                        toggleMonth(response.current_month.id);

                        // Set active week
                        setThisWeek(response.week_id, true);

                        // Set Month Filter values in search widget
                        $("#mec_sf_month_" + settings.id).val(month);
                        $("#mec_sf_year_" + settings.id).val(year);
                        mecFluentCustomScrollbar();
                    },
                    error: function () { }
                });
            }
        }

        function toggleMonth(month_id) {
            // Show related events
            $('#mec_skin_' + settings.id + ' .mec-month-container').addClass('mec-util-hidden');
            $('#mec_weekly_view_month_' + settings.id + '_' + month_id).removeClass('mec-util-hidden');

            $('#mec_skin_' + settings.id + ' .mec-month-navigator').addClass('mec-util-hidden');
            $('#mec_month_navigator' + settings.id + '_' + month_id).removeClass('mec-util-hidden');

            // Initialize Month Navigator
            if (settings.month_navigator) initMonthNavigator(month_id);
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-title a").off('click').on('click', function (e) {
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }
    };

}(jQuery));

// MEC DAILY VIEW PLUGIN
(function ($) {
    $.fn.mecDailyView = function (options) {
        var active_month;
        var active_year;
        var active_day;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            id: 0,
            changeDayElement: '.mec-daily-view-day',
            events_label: 'Events',
            event_label: 'Event',
            month_navigator: 0,
            atts: '',
            ajax_url: '',
            sf: {},
        }, options);

        active_month = settings.month;
        active_year = settings.year;
        active_day = settings.day;

        mecFluentCustomScrollbar();

        // Set Today
        setToday(settings.today);

        // Set Listeners
        setListeners();

        // Initialize Month Navigator
        if (settings.month_navigator) initMonthNavigator(settings.month_id);

        // Initialize Days Slider
        initDaysSlider(settings.month_id);

        // Slider first event day focus when page load.
        mecFocusDay(settings);

        // Search Widget
        if (settings.sf.container !== '') {
            $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    search(active_year, active_month, active_day);
                }
            });
        }

        function setListeners() {
            $(settings.changeDayElement).on('click', function () {
                var today = $(this).data('day-id');
                setToday(today);
                mecFluentCustomScrollbar();
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        var current_monthday;

        function setToday(today) {
            // For caring about 31st, 30th and 29th of some months
            if (!$('#mec_daily_view_day' + settings.id + '_' + today).length) {
                setToday(parseInt(today) - 1);
                return false;
            }

            // Set day to active in day list
            $('.mec-daily-view-day').removeClass('mec-daily-view-day-active mec-color');
            $('#mec_daily_view_day' + settings.id + '_' + today).addClass('mec-daily-view-day-active mec-color');

            // Show related events
            $('.mec-daily-view-date-events').addClass('mec-util-hidden');
            $('#mec_daily_view_date_events' + settings.id + '_' + today).removeClass('mec-util-hidden');
            $('.mec-daily-view-events').addClass('mec-util-hidden');
            $('#mec-daily-view-events' + settings.id + '_' + today).removeClass('mec-util-hidden');

            // Set today label
            var weekday = $('#mec_daily_view_day' + settings.id + '_' + today).data('day-weekday');
            var monthday = $('#mec_daily_view_day' + settings.id + '_' + today).data('day-monthday');
            var count = $('#mec_daily_view_day' + settings.id + '_' + today).data('events-count');
            var month_id = $('#mec_daily_view_day' + settings.id + '_' + today).data('month-id');

            $('#mec_today_container' + settings.id + '_' + month_id).html('<h2>' + monthday + '</h2><h3>' + weekday + '</h3><div class="mec-today-count">' + count + ' ' + (count > 1 ? settings.events_label : settings.event_label) + '</div>');

            if (monthday <= 9) current_monthday = '0' + monthday;
            else current_monthday = monthday;
        }

        function initMonthNavigator(month_id) {
            $('#mec_month_navigator' + settings.id + '_' + month_id + ' .mec-load-month').off('click');
            $('#mec_month_navigator' + settings.id + '_' + month_id + ' .mec-load-month').on('click', function () {
                var year = $(this).data('mec-year');
                var month = $(this).data('mec-month');

                setMonth(year, month, current_monthday, true);
            });
        }

        function initDaysSlider(month_id, day_id) {
            // Set Global Month Id
            mec_g_month_id = month_id;

            // Check RTL website
            var owl_rtl = $('body').hasClass('rtl') ? true : false;

            // Init Days slider
            var owl = $("#mec-owl-calendar-d-table-" + settings.id + "-" + month_id);
            owl.owlCarousel({
                responsiveClass: true,
                responsive: {
                    0: {
                        items: owl.closest('.mec-fluent-wrap').length > 0 ? 3 : 2,
                    },
                    479: {
                        items: 4,
                    },
                    767: {
                        items: 7,
                    },
                    960: {
                        items: 14,
                    },
                    1000: {
                        items: 19,
                    },
                    1200: {
                        items: 22,
                    }
                },
                dots: false,
                loop: false,
                rtl: owl_rtl,
            });

            // Custom Navigation Events
            $("#mec_daily_view_month_" + settings.id + "_" + month_id + " .mec-table-d-next").click(function (e) {
                e.preventDefault();
                owl.trigger('next.owl.carousel');
            });

            $("#mec_daily_view_month_" + settings.id + "_" + month_id + " .mec-table-d-prev").click(function (e) {
                e.preventDefault();
                owl.trigger('prev.owl.carousel');
            });

            if (typeof day_id === 'undefined') day_id = $('.mec-daily-view-day-active').data('day-id');

            var today_str = day_id.toString().substring(6, 8);
            var today_int = parseInt(today_str);

            owl.trigger('owl.goTo', [today_int]);
        }

        function search(year, month, day) {
            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_daily_view_load_month&mec_year=" + year + "&mec_month=" + month + "&mec_day=" + day + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    // Remove Loading Class
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    // Append Month
                    $("#mec_skin_events_" + settings.id).html('<div class="mec-month-container" id="mec_daily_view_month_' + settings.id + '_' + response.current_month.id + '">' + response.month + '</div>');

                    // Append Month Navigator
                    $("#mec_skin_" + settings.id + " .mec-calendar-a-month.mec-clear").html('<div class="mec-month-navigator" id="mec_month_navigator' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                    // Set Listeners
                    setListeners();

                    active_year = response.current_month.year;
                    active_month = response.current_month.month;

                    // Toggle Month
                    toggleMonth(response.current_month.id, '' + active_year + active_month + active_day);

                    // Set Today
                    setToday('' + active_year + active_month + active_day);

                    // Focus First Active Day
                    mecFocusDay(settings);
                    mecFluentCustomScrollbar();
                },
                error: function () { }
            });
        }

        function setMonth(year, month, day, navigation_click) {
            var month_id = '' + year + month;

            active_month = month;
            active_year = year;
            active_day = day;
            navigation_click = navigation_click || false;

            // Month exists so we just show it
            if ($("#mec_daily_view_month_" + settings.id + "_" + month_id).length) {
                // Toggle Month
                toggleMonth(month_id);

                // Set Today
                setToday('' + month_id + day);
            } else {
                // Add Loading Class
                if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

                $.ajax({
                    url: settings.ajax_url,
                    data: "action=mec_daily_view_load_month&mec_year=" + year + "&mec_month=" + month + "&mec_day=" + day + "&" + settings.atts + "&apply_sf_date=0" + "&navigator_click=" + navigation_click,
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Remove Loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Append Month
                        $("#mec_skin_events_" + settings.id).append('<div class="mec-month-container" id="mec_daily_view_month_' + settings.id + '_' + response.current_month.id + '">' + response.month + '</div>');

                        // Append Month Navigator
                        $("#mec_skin_" + settings.id + " .mec-calendar-a-month.mec-clear").append('<div class="mec-month-navigator" id="mec_month_navigator' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                        // Set Listeners
                        setListeners();

                        // Toggle Month
                        toggleMonth(response.current_month.id, '' + year + month + '01');

                        // Set Today
                        setToday('' + year + month + '01');

                        // Set Month Filter values in search widget
                        $("#mec_sf_month_" + settings.id).val(month);
                        $("#mec_sf_year_" + settings.id).val(year);
                        mecFluentCustomScrollbar();
                    },
                    error: function () { }
                });
            }
        }

        function toggleMonth(month_id, day_id) {
            // Show related events
            $('#mec_skin_' + settings.id + ' .mec-month-container').addClass('mec-util-hidden');
            $('#mec_daily_view_month_' + settings.id + '_' + month_id).removeClass('mec-util-hidden');

            $('#mec_skin_' + settings.id + ' .mec-month-navigator').addClass('mec-util-hidden');
            $('#mec_month_navigator' + settings.id + '_' + month_id).removeClass('mec-util-hidden');

            // Initialize Month Navigator
            if (settings.month_navigator) initMonthNavigator(month_id);

            // Initialize Days Slider
            initDaysSlider(month_id, day_id);

            // Focus First Active Day
            mecFocusDay(settings);
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-title a").off('click').on('click', function (e) {
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }
    };

}(jQuery));

// MEC TIMETABLE PLUGIN
(function ($) {
    $.fn.mecTimeTable = function (options) {
        var active_year;
        var active_month;
        var active_week;
        var active_week_number;
        var active_day;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            week: 1,
            active_day: 1,
            id: 0,
            changeWeekElement: '.mec-load-week',
            month_navigator: 0,
            atts: '',
            ajax_url: '',
            sf: {}
        }, options);

        // Search Widget
        if (settings.sf.container !== '') {
            $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    search(active_year, active_month, active_week, active_day);
                }
            });
        }

        // Set The Week
        setThisWeek(settings.month_id + settings.week, settings.active_day);

        // Set Listeners
        setListeners();

        // Initialize Month Navigator
        if (settings.month_navigator) initMonthNavigator(settings.month_id);

        function setListeners() {
            // Change Week Listener
            $(settings.changeWeekElement).off('click').on('click', function () {
                var week = $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active').data('week-id');
                var max_weeks = $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active').data('max-weeks');
                var new_week_number = active_week_number;

                if ($(this).hasClass('mec-previous-month')) {
                    week = parseInt(week) - 1;
                    new_week_number--;
                } else {
                    week = parseInt(week) + 1;
                    new_week_number++;
                }

                if (new_week_number <= 1 || new_week_number >= max_weeks) {
                    // Disable Next/Previous Button
                    $(this).css({
                        'opacity': .6,
                        'cursor': 'default'
                    });
                    $(this).find('i').css({
                        'opacity': .6,
                        'cursor': 'default'
                    });
                } else {
                    // Enable Next/Previous Buttons
                    $('#mec_skin_' + settings.id + ' .mec-load-week, #mec_skin_' + settings.id + ' .mec-load-week i').css({
                        'opacity': 1,
                        'cursor': 'pointer'
                    });
                }

                // Week is not in valid range
                if (new_week_number === 0 || new_week_number > max_weeks) { } else {
                    setThisWeek(week);
                }
            });

            // Change Day Listener
            $('#mec_skin_' + settings.id + ' .mec-weekly-view-week dt').not('.mec-timetable-has-no-event').off('click').on('click', function () {
                var day = $(this).data('date-id');
                setDay(day);
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function setThisWeek(week, day) {
            // Week is not exists
            if (!$('#mec_weekly_view_week_' + settings.id + '_' + week).length) {
                return setThisWeek((parseInt(week) - 1), day);
            }

            // Set week to active in week list
            $('#mec_skin_' + settings.id + ' .mec-weekly-view-week').removeClass('mec-weekly-view-week-active');
            $('#mec_weekly_view_week_' + settings.id + '_' + week).addClass('mec-weekly-view-week-active');

            setDay(day);

            active_week = week;
            active_week_number = $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active').data('week-number');

            $('#mec_skin_' + settings.id + ' .mec-calendar-d-top').find('.mec-current-week').find('span').remove();
            $('#mec_skin_' + settings.id + ' .mec-calendar-d-top').find('.mec-current-week').append('<span>' + active_week_number + '</span>');

            if (active_week_number === 1) {
                // Disable Previous Button
                $('#mec_skin_' + settings.id + ' .mec-previous-month.mec-load-week').css({
                    'opacity': .6,
                    'cursor': 'default'
                });
                $('#mec_skin_' + settings.id + ' .mec-previous-month.mec-load-week').find('i').css({
                    'opacity': .6,
                    'cursor': 'default'
                });
            }
        }

        function setDay(day) {
            // Find the date automatically
            if (typeof day === 'undefined') {
                day = $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active dt').not('.mec-timetable-has-no-event').first().data('date-id');
            }

            // Activate the date element
            $('#mec_skin_' + settings.id + ' dt').removeClass('mec-timetable-day-active');
            $('#mec_skin_' + settings.id + ' .mec-weekly-view-week-active dt[data-date-id="' + day + '"]').addClass('mec-timetable-day-active');

            // Show related events
            $('#mec_skin_' + settings.id + ' .mec-weekly-view-date-events').addClass('mec-util-hidden');
            $('.mec_weekly_view_date_events' + settings.id + '_' + day).removeClass('mec-util-hidden').show();
        }

        function initMonthNavigator(month_id) {
            $('#mec_month_navigator' + settings.id + '_' + month_id + ' .mec-load-month').off('click').on('click', function () {
                var year = $(this).data('mec-year');
                var month = $(this).data('mec-month');

                setMonth(year, month, active_week);
            });
        }

        function search(year, month, week) {
            var week_number = (String(week).slice(-1));

            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            // Set MEC Year And Month If Undefined
            year = typeof year == 'undefined' ? '' : year;
            month = typeof month == 'undefined' ? '' : month;

            // Append current week to data body for used after filter.
            $('body').data('currentweek', $("#mec_skin_events_" + settings.id).find('.mec-current-week > span').html());

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_timetable_load_month&mec_year=" + year + "&mec_month=" + month + "&mec_week=" + week_number + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    // Remove Loading Class
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    // Append Month
                    $("#mec_skin_events_" + settings.id).html('<div class="mec-month-container" id="mec_timetable_month_' + settings.id + '_' + response.current_month.id + '">' + response.month + '</div>');

                    // Append Month Navigator
                    $("#mec_skin_" + settings.id + " .mec-skin-weekly-view-month-navigator-container").html('<div class="mec-month-navigator" id="mec_month_navigator' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                    // Set Listeners
                    setListeners();

                    // Toggle Month
                    toggleMonth(response.current_month.id);

                    // Set active week
                    setThisWeek(response.week_id);

                    // Focus First Active Week
                    mec_focus_week(settings.id, 'timetable');

                    mecFluentCustomScrollbar();
                },
                error: function () { }
            });
        }

        function setMonth(year, month, week) {
            var month_id = '' + year + month;
            var week_number = (String(week).slice(-1));

            active_month = month;
            active_year = year;

            // Month exists so we just show it
            if ($("#mec_timetable_month_" + settings.id + "_" + month_id).length) {
                // Toggle Month
                toggleMonth(month_id);

                // Set active week
                setThisWeek('' + month_id + week_number);
            } else {
                // Add Loading Class
                if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

                $.ajax({
                    url: settings.ajax_url,
                    data: "action=mec_timetable_load_month&mec_year=" + year + "&mec_month=" + month + "&mec_week=" + week_number + "&" + settings.atts + "&apply_sf_date=0",
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Remove Loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Append Month
                        $("#mec_skin_events_" + settings.id).append('<div class="mec-month-container" id="mec_timetable_month_' + settings.id + '_' + response.current_month.id + '">' + response.month + '</div>');

                        // Append Month Navigator
                        $("#mec_skin_" + settings.id + " .mec-skin-weekly-view-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                        // Set Listeners
                        setListeners();

                        // Toggle Month
                        toggleMonth(response.current_month.id);

                        // Set active week
                        setThisWeek(response.week_id);

                        // Set Month Filter values in search widget
                        $("#mec_sf_month_" + settings.id).val(month);
                        $("#mec_sf_year_" + settings.id).val(year);
                    },
                    error: function () { }
                });
            }
        }

        function toggleMonth(month_id) {
            // Show related events
            $('#mec_skin_' + settings.id + ' .mec-month-container').addClass('mec-util-hidden');
            $('#mec_timetable_month_' + settings.id + '_' + month_id).removeClass('mec-util-hidden');

            $('#mec_skin_' + settings.id + ' .mec-month-navigator').addClass('mec-util-hidden');
            $('#mec_month_navigator' + settings.id + '_' + month_id).removeClass('mec-util-hidden');

            // Initialize Month Navigator
            if (settings.month_navigator) initMonthNavigator(month_id);
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-timetable-event-title a").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }
    };

}(jQuery));

// MEC WEEKLY PROGRAM PLUGIN
(function ($) {
    $.fn.mecWeeklyProgram = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            sf: {}
        }, options);

        // Search Widget
        if (settings.sf.container !== '') {
            $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    search();
                }
            });
        }

        // Set Listeners
        setListeners();

        function setListeners() {
            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function search() {
            var $modal = $('.mec-modal-result');

            // Add Loading Class
            if ($modal.length === 0) $('.mec-wrap').append('<div class="mec-modal-result"></div>');
            $modal.addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_weeklyprogram_load&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    // Remove Loading Class
                    $modal.removeClass("mec-month-navigator-loading");

                    // Append Month
                    $("#mec_skin_events_" + settings.id).html(response.date_events);

                    // Set Listeners
                    setListeners();
                },
                error: function () { }
            });
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-title a").off('click').on('click', function (e) {
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }
    };

}(jQuery));

// MEC MASONRY VIEW PLUGIN
(function ($) {
    $.fn.mecMasonryView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            end_date: '',
            offset: 0,
            start_date: '',
        }, options);

        // Set onclick Listeners
        setListeners();

        // Init Masonry
        initMasonry();
        if (typeof custom_dev !== undefined) var custom_dev;
        if (custom_dev == 'yes') {
            $(".mec-wrap").css("height", "1550");
            if (Math.max(document.documentElement.clientWidth, window.innerWidth || 0) < 768) {
                $(".mec-wrap").css("height", "5500");
            }
            if (Math.max(document.documentElement.clientWidth, window.innerWidth || 0) < 480) {
                $(".mec-wrap").css("height", "5000");
            }
            $(".mec-event-masonry .mec-masonry-item-wrap:nth-child(n+20)").css("display", "none");
            $(".mec-load-more-button").on("click", function () {
                $(".mec-event-masonry .mec-masonry-item-wrap:nth-child(n+20)").css("display", "block");
                $(".mec-wrap").css("height", "auto");
                initMasonry();
                $(".mec-load-more-button").hide();
            })
            $(".mec-events-masonry-cats a:first-child").on("click", function () {
                $(".mec-wrap").css("height", "auto");
                $(".mec-event-masonry .mec-masonry-item-wrap:nth-child(n+20)").css("display", "block");
                $(".mec-load-more-button").hide();
                initMasonry();
            })
            $(".mec-events-masonry-cats a:not(:first-child)").on("click", function () {
                $(".mec-load-more-button").hide();
                $(".mec-wrap").css("height", "auto");
                $(".mec-wrap").css("min-height", "400");
                $(".mec-event-masonry .mec-masonry-item-wrap").css("display", "block");
                var element = document.querySelector("#mec_skin_" + settings.id + " .mec-event-masonry");
                var selector = $(this).attr('data-group');
                var CustomShuffle = new Shuffle(element, {
                    itemSelector: '.mec-masonry-item-wrap',
                });
                CustomShuffle.sort({
                    by: element.getAttribute('data-created'),
                });
                CustomShuffle.filter(selector != '*' ? selector : Shuffle.ALL_ITEMS);
                $(".mec-event-masonry .mec-masonry-item-wrap").css("visibility", "visible");
            })
        }

        // Fix Elementor Masonry
        if (mecdata.elementor_edit_mode != 'no') elementorFrontend.hooks.addAction('frontend/element_ready/global', initMasonry());

        function initMasonry() {
            var $container = $("#mec_skin_" + settings.id + " .mec-event-masonry");
            var data_sortAscending = $("#mec_skin_" + settings.id).data('sortascending');
            $container.imagesLoaded(function () {
                var $grid = $container.isotope({
                    filter: '*',
                    itemSelector: '.mec-masonry-item-wrap',
                    getSortData: {
                        date: '[data-sort-masonry]',
                    },
                    sortBy: 'date',
                    sortAscending: data_sortAscending,
                    animationOptions: {
                        duration: 750,
                        easing: 'linear',
                        queue: false
                    },
                });
                if (settings.fit_to_row == 1) $grid.isotope({
                    layoutMode: 'fitRows',
                    sortAscending: data_sortAscending,
                });
                // Fix Elementor tab
                $('.elementor-tabs').find('.elementor-tab-title').click(function () {
                    $grid.isotope({
                        sortBy: 'date',
                        sortAscending: data_sortAscending,
                    });
                });
            })

            $("#mec_skin_" + settings.id + " .mec-events-masonry-cats a").click(function () {
                var selector = $(this).attr('data-filter');
                var $grid_cat = $container.isotope({
                    filter: selector,
                    itemSelector: '.mec-masonry-item-wrap',
                    getSortData: {
                        date: '[data-sort-masonry]',
                    },
                    sortBy: 'date',
                    sortAscending: data_sortAscending,
                    animationOptions: {
                        duration: 750,
                        easing: 'linear',
                        queue: false
                    },
                });
                if (settings.masonry_like_grid == 1) $grid_cat.isotope({
                    sortBy: 'date',
                    sortAscending: data_sortAscending,
                });
                return false;
            });

            var $optionSets = $("#mec_skin_" + settings.id + " .mec-events-masonry-cats"),
                $optionLinks = $optionSets.find('a');

            $optionLinks.click(function () {
                var $this = $(this);

                // don't proceed if already selected
                if ($this.hasClass('selected')) return false;

                var $optionSet = $this.parents('.mec-events-masonry-cats');
                $optionSet.find('.mec-masonry-cat-selected').removeClass('mec-masonry-cat-selected');
                $this.addClass('mec-masonry-cat-selected');
            });
        }

        function setListeners() {
            if (settings.sed_method != '0') {
                sed();
            }

        }

        $("#mec_skin_" + settings.id + " .mec-events-masonry-cats > a").click(function () {
            var mec_load_more_btn = $("#mec_skin_" + settings.id + " .mec-load-more-button");
            var mec_filter_value = $(this).data('filter').replace('.mec-t', '');

            if (mec_load_more_btn.hasClass('mec-load-more-loading')) mec_load_more_btn.removeClass('mec-load-more-loading');
            if (mec_load_more_btn.hasClass("mec-hidden-" + mec_filter_value)) mec_load_more_btn.addClass("mec-util-hidden");
            else mec_load_more_btn.removeClass("mec-util-hidden");
        });

        $("#mec_skin_" + settings.id + " .mec-load-more-button").on("click", function () {
            loadMore();
        });

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-masonry-img a, #mec_skin_" + settings.id + " .mec-event-title a, #mec_skin_" + settings.id + " .mec-booking-button").off('click').on('click', function (e) {
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMore() {
            // Add loading Class
            $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-load-more-loading");
            var mec_cat_elem = $('#mec_skin_' + settings.id).find('.mec-masonry-cat-selected');
            var mec_filter_value = (mec_cat_elem && mec_cat_elem.data('filter') != undefined) ? mec_cat_elem.data('filter').replace('.mec-t', '') : '';
            var mec_filter_by = $('#mec_skin_' + settings.id).data('filterby');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_masonry_load_more&mec_filter_by=" + mec_filter_by + "&mec_filter_value=" + mec_filter_value + "&mec_start_date=" + settings.end_date + "&mec_offset=" + settings.offset + "&" + settings.atts + "&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden mec-hidden-" + mec_filter_value);
                    } else {
                        // Show load more button
                        if (typeof response.has_more_event === 'undefined' || (typeof response.has_more_event !== 'undefined' && response.has_more_event)) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Append Items
                        var node = $("#mec_skin_" + settings.id + " .mec-event-masonry");
                        var markup = '',
                            newItems = $(response.html).find('.mec-masonry-item-wrap');

                        newItems.each(function (index) {
                            node.isotope()
                                .append(newItems[index])
                                .isotope('appended', newItems[index]);
                        });

                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                    }
                },
                error: function () { }
            });
        }
    };
}(jQuery));

// MEC LIST VIEW PLUGIN
(function ($) {
    $.fn.mecListView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            current_month_divider: '',
            end_date: '',
            offset: 0,
            limit: 0
        }, options);

        // Set onclick Listeners
        setListeners();

        var sf;

        function setListeners() {
            // Search Widget
            if (settings.sf.container !== '') {
                sf = $(settings.sf.container).mecSearchForm({
                    id: settings.id,
                    refine: settings.sf.refine,
                    ajax_url: settings.ajax_url,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        search();
                    }
                });
            }

            $("#mec_skin_" + settings.id + " .mec-load-more-button").on("click", function () {
                loadMore();
            });

            // Accordion Toggle
            if (settings.style === 'accordion') {
                if (settings.toggle_month_divider) {
                    $('#mec_skin_' + settings.id + ' .mec-month-divider:first-of-type').addClass('active');
                    $('#mec_skin_' + settings.id + ' .mec-month-divider:first-of-type').find('i').removeClass('mec-sl-arrow-down').addClass('mec-sl-arrow-up');

                    toggle();
                }

                accordion();
            }

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function toggle() {
            $('#mec_skin_' + settings.id + ' .mec-month-divider').off("click").on("click", function (event) {
                event.preventDefault();

                var status = $(this).hasClass('active');

                // Remove Active Style of Month Divider
                $('#mec_skin_' + settings.id + ' .mec-month-divider').removeClass('active');

                // Hide All Events
                $('#mec_skin_' + settings.id + ' .mec-divider-toggle').slideUp('fast');

                if (status) {
                    $(this).removeClass('active');
                    $('.mec-month-divider').find('i').removeClass('mec-sl-arrow-up').addClass('mec-sl-arrow-down');
                } else {
                    $(this).addClass('active');
                    $('.mec-month-divider').find('i').removeClass('mec-sl-arrow-up').addClass('mec-sl-arrow-down')
                    $(this).find('i').removeClass('mec-sl-arrow-down').addClass('mec-sl-arrow-up');

                    var month = $(this).data('toggle-divider');
                    $('#mec_skin_' + settings.id + ' .' + month).slideDown('fast');
                }
            });
        }

        function toggleLoadmore() {
            $('#mec_skin_' + settings.id + ' .mec-month-divider:not(:last)').each(function () {
                if ($(this).hasClass('active')) $(this).removeClass('active');
                var month = $(this).data('toggle-divider');
                $('#mec_skin_' + settings.id + ' .' + month).slideUp('fast');
            });

            // Set Active Class For Last Article
            $('#mec_skin_' + settings.id + ' .mec-month-divider:last').addClass('active');

            // Register Listeners
            toggle();
        }

        function accordion() {
            // Accordion Toggle
            $("#mec_skin_" + settings.id + " .mec-toggle-item-inner").off("click").on("click", function (event) {
                event.preventDefault();

                var $this = $(this);
                $(this).parent().find(".mec-content-toggle").slideToggle("fast", function () {
                    $this.children("i").toggleClass("mec-sl-arrow-down mec-sl-arrow-up");
                });

                // Trigger Google Map
                var unique_id = $(this).parent().find(".mec-modal-wrap").data('unique-id');

                window['mec_init_gmap' + unique_id]();
            });
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-title > a, #mec_skin_" + settings.id + " .mec-booking-button, #mec_skin_" + settings.id + " .mec-detail-button").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });

            $("#mec_skin_" + settings.id + " .mec-event-image a img").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).parent().attr('href');

                var id = $(this).parent().data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMore() {
            // Add loading Class
            $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-load-more-loading");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_list_load_more&mec_start_date=" + settings.end_date + "&mec_offset=" + settings.offset + "&" + settings.atts + "&current_month_divider=" + settings.current_month_divider + "&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == '0') {
                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");
                    } else {
                        // Show load more button
                        if (typeof response.has_more_event === 'undefined' || (typeof response.has_more_event !== 'undefined' && response.has_more_event)) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Append Items
                        $("#mec_skin_events_" + settings.id).append(response.html);

                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;
                        settings.current_month_divider = response.current_month_divider;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }

                        // Accordion Toggle
                        if (settings.style === 'accordion') {
                            if (settings.toggle_month_divider) toggleLoadmore();

                            accordion();
                        }
                    }
                },
                error: function () { }
            });
        }

        function search() {
            // Hide no event message
            $("#mec_skin_no_events_" + settings.id).addClass("mec-util-hidden");

            // Add loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
            jQuery("#gmap-data").val("");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_list_load_more&mec_start_date=" + settings.start_date + "&" + settings.atts + "&current_month_divider=0&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html('');

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Hide Map
                        $('.mec-skin-map-container').addClass("mec-util-hidden");

                        // Hide it
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Show no event message
                        $("#mec_skin_no_events_" + settings.id).removeClass("mec-util-hidden");
                    } else {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html(response.html);

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Show Map
                        $('.mec-skin-map-container').removeClass("mec-util-hidden");

                        // Show load more button
                        if (response.count >= settings.limit) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        // Hide load more button
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;
                        settings.current_month_divider = response.current_month_divider;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }

                        // Accordion Toggle
                        if (settings.style === 'accordion') {
                            if (settings.toggle_month_divider) toggle();

                            accordion();
                        }
                    }
                },
                error: function () { }
            });
        }
    };

}(jQuery));

// MEC GRID VIEW PLUGIN
(function ($) {
    $.fn.mecGridView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            end_date: '',
            offset: 0,
            start_date: '',
        }, options);

        // Set onclick Listeners
        setListeners();

        var sf;

        function setListeners() {
            // Search Widget
            if (settings.sf.container !== '') {
                sf = $(settings.sf.container).mecSearchForm({
                    id: settings.id,
                    refine: settings.sf.refine,
                    ajax_url: settings.ajax_url,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        search();
                    }
                });
            }

            $("#mec_skin_" + settings.id + " .mec-load-more-button").on("click", function () {
                loadMore();
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-title a, #mec_skin_" + settings.id + " .mec-booking-button").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
            $("#mec_skin_" + settings.id + " .mec-event-image a img").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).parent().attr('href');

                var id = $(this).parent().data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMore() {
            // Add loading Class
            $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-load-more-loading");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_grid_load_more&mec_start_date=" + settings.end_date + "&mec_offset=" + settings.offset + "&" + settings.atts + "&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");
                    } else {
                        // Show load more button
                        if (typeof response.has_more_event === 'undefined' || (typeof response.has_more_event !== 'undefined' && response.has_more_event)) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Append Items
                        $("#mec_skin_events_" + settings.id).append(response.html);

                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                    }
                },
                error: function () { }
            });
        }

        function search() {
            // Hide no event message
            $("#mec_skin_no_events_" + settings.id).addClass("mec-util-hidden");

            // Add loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
            jQuery("#gmap-data").val("");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_grid_load_more&mec_start_date=" + settings.start_date + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html('');

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Hide Map
                        $('.mec-skin-map-container').addClass("mec-util-hidden");

                        // Hide it
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Show no event message
                        $("#mec_skin_no_events_" + settings.id).removeClass("mec-util-hidden");
                    } else {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html(response.html);

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Show Map
                        $('.mec-skin-map-container').removeClass("mec-util-hidden");

                        // Show load more button
                        if (response.count >= settings.limit) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        // Hide load more button
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                    }
                },
                error: function () { }
            });
        }
    };

}(jQuery));

// MEC CUSTOM VIEW PLUGIN
(function ($) {
    $.fn.mecCustomView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            end_date: '',
            offset: 0,
            start_date: '',
        }, options);

        // Set onclick Listeners
        setListeners();

        var sf;

        function setListeners() {
            // Search Widget
            if (settings.sf.container !== '') {
                sf = $(settings.sf.container).mecSearchForm({
                    id: settings.id,
                    refine: settings.sf.refine,
                    ajax_url: settings.ajax_url,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        search();
                    }
                });
            }

            $("#mec_skin_" + settings.id + " .mec-load-more-button").on("click", function () {
                loadMore();
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-title a, #mec_skin_" + settings.id + " .mec-booking-button").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
            $("#mec_skin_" + settings.id + " .mec-event-image a img").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).parent().attr('href');

                var id = $(this).parent().data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMore() {
            // Add loading Class
            $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-load-more-loading");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_custom_load_more&mec_start_date=" + settings.end_date + "&mec_offset=" + settings.offset + "&" + settings.atts + "&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");
                    } else {
                        // Show load more button
                        if (typeof response.has_more_event === 'undefined' || (typeof response.has_more_event !== 'undefined' && response.has_more_event)) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        var html = $(response.html);
                        if ($('.mec-month-divider', html).length) {
                            var df = $('.mec-month-divider:first', html).data('toggle-divider');
                            var dl = $("#mec_skin_events_" + settings.id + " .mec-month-divider:last").data('toggle-divider');

                            if (df == dl) {
                                $(html).find('.mec-month-divider:first').remove();
                                response.html = html;
                            }

                        }

                        // Append Items
                        $("#mec_skin_events_" + settings.id).append(response.html);

                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;

                        if ($('.mec-event-sd-countdown').length > 0) {
                            $('.mec-event-sd-countdown').each(function (event) {
                                var dc = $(this).attr('data-date-custom');
                                $(this).mecCountDown(
                                    {
                                        date: dc,
                                        format: "off"
                                    },
                                    function () {
                                    });
                            })
                        }
                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                    }
                },
                error: function () { }
            });
        }

        function search() {
            // Hide no event message
            $("#mec_skin_no_events_" + settings.id).addClass("mec-util-hidden");

            // Add loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
            jQuery("#gmap-data").val("");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_custom_load_more&mec_start_date=" + settings.start_date + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html('');

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Hide Map
                        $('.mec-skin-map-container').addClass("mec-util-hidden");

                        // Hide it
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Show no event message
                        $("#mec_skin_no_events_" + settings.id).removeClass("mec-util-hidden");
                    } else {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html(response.html);

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Show Map
                        $('.mec-skin-map-container').removeClass("mec-util-hidden");

                        // Show load more button
                        if (response.count >= settings.limit) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        // Hide load more button
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                    }
                },
                error: function () { }
            });
        }
    };

}(jQuery));

// MEC TIMELINE VIEW PLUGIN
(function ($) {
    $.fn.mecTimelineView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            end_date: '',
            offset: 0,
            start_date: '',
        }, options);

        // Set onclick Listeners
        setListeners();

        var sf;

        function setListeners() {
            // Search Widget
            if (settings.sf.container !== '') {
                sf = $(settings.sf.container).mecSearchForm({
                    id: settings.id,
                    refine: settings.sf.refine,
                    ajax_url: settings.ajax_url,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        search();
                    }
                });
            }

            $("#mec_skin_" + settings.id + " .mec-load-more-button").on("click", function () {
                loadMore();
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-timeline-event-image a, #mec_skin_" + settings.id + " .mec-event-title a, #mec_skin_" + settings.id + " .mec-booking-button").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
            $("#mec_skin_" + settings.id + " .mec-event-image a img").off('click').on('click', function (e) {
                e.preventDefault();
                var href = $(this).parent().attr('href');

                var id = $(this).parent().data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMore() {
            // Add loading Class
            $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-load-more-loading");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_timeline_load_more&mec_start_date=" + settings.end_date + "&mec_offset=" + settings.offset + "&" + settings.atts + "&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");
                    } else {
                        // Show load more button
                        if (typeof response.has_more_event === 'undefined' || (typeof response.has_more_event !== 'undefined' && response.has_more_event)) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Append Items
                        $("#mec_skin_events_" + settings.id).append(response.html);

                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }

                    }
                },
                error: function () { }
            });
        }

        function search() {
            // Hide no event message
            $("#mec_skin_no_events_" + settings.id).addClass("mec-util-hidden");

            // Add loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_timeline_load_more&mec_start_date=" + settings.start_date + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html('');

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Hide Map
                        $('.mec-skin-map-container').addClass("mec-util-hidden");

                        // Hide it
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Show no event message
                        $("#mec_skin_no_events_" + settings.id).removeClass("mec-util-hidden");
                    } else {
                        // Append Items
                        $("#mec_skin_events_" + settings.id).html(response.html);

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Show Map
                        $('.mec-skin-map-container').removeClass("mec-util-hidden");

                        // Show load more button
                        if (response.count >= settings.limit) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        // Hide load more button
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                    }
                },
                error: function () { }
            });
        }
    };

}(jQuery));

// MEC AGENDA VIEW PLUGIN
(function ($) {
    $.fn.mecAgendaView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            current_month_divider: '',
            end_date: '',
            offset: 0,
        }, options);

        // Set onclick Listeners
        setListeners();

        var sf;

        function setListeners() {
            // Search Widget
            if (settings.sf.container !== '') {
                sf = $(settings.sf.container).mecSearchForm({
                    id: settings.id,
                    refine: settings.sf.refine,
                    ajax_url: settings.ajax_url,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        search();
                    }
                });
            }

            $("#mec_skin_" + settings.id + " .mec-load-more-button").on("click", function () {
                loadMore();
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-agenda-event-title a").off('click').on('click', function (e) {
                var sed_method = $(this).attr('target');
                if ('_blank' === sed_method) {

                    return;
                }
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMore() {
            // Add loading Class
            $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-load-more-loading");

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_agenda_load_more&mec_start_date=" + settings.end_date + "&mec_offset=" + settings.offset + "&" + settings.atts + "&current_month_divider=" + settings.current_month_divider + "&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Hide load more button
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");
                    } else {
                        // Show load more button
                        if (typeof response.has_more_event === 'undefined' || (typeof response.has_more_event !== 'undefined' && response.has_more_event)) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Append Items
                        $("#mec_skin_events_" + settings.id + " .mec-events-agenda-container").append(response.html);

                        // Remove loading Class
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-load-more-loading");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;
                        settings.current_month_divider = response.current_month_divider;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                        mecFluentCustomScrollbar();
                    }
                },
                error: function () { }
            });
        }

        function search() {
            // Hide no event message
            $("#mec_skin_no_events_" + settings.id).addClass("mec-util-hidden");

            // Add loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
            mecFluentCustomScrollbar();

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_agenda_load_more&mec_start_date=" + settings.start_date + "&" + settings.atts + "&current_month_divider=0&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    if (response.count == "0") {
                        // Append Items
                        $("#mec_skin_events_" + settings.id + " .mec-events-agenda-container").html('');

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Hide it
                        $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Show no event message
                        $("#mec_skin_no_events_" + settings.id).removeClass("mec-util-hidden");
                    } else {
                        // Append Items
                        $("#mec_skin_events_" + settings.id + " .mec-events-agenda-container").html(response.html);

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                        // Show load more button
                        if (response.count >= settings.limit) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                        // Hide load more button
                        else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                        // Update the variables
                        settings.end_date = response.end_date;
                        settings.offset = response.offset;
                        settings.current_month_divider = response.current_month_divider;

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }
                    }
                    mecFluentCustomScrollbar();
                },
                error: function () { }
            });
        }
    };
}(jQuery));

// MEC CAROUSEL VIEW PLUGIN
(function ($) {
    $.fn.mecCarouselView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            ajax_url: '',
            sf: {},
            items: 3,
            loop: true,
            autoplay_status: true,
            autoplay: '',
            style: 'type1',
            start_date: ''
        }, options);

        // Init Sliders
        initSlider(settings);

        // Single Event Method
        if (settings.sed_method != '0') {
            sed(settings);
        }

        function initSlider(settings) {
            // Check RTL website
            if ($('body').hasClass('rtl')) {
                var owl_rtl = true;
            } else {
                var owl_rtl = false;
            }

            if (settings.style === 'type1') {
                // Start carousel skin
                var owl = $("#mec_skin_" + settings.id + " .mec-event-carousel-type1 .mec-owl-carousel");

                owl.owlCarousel({
                    autoplay: settings.autoplay_status,
                    autoplayTimeout: settings.autoplay, // Set AutoPlay to 3 seconds
                    loop: settings.loop,
                    items: settings.items,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1,
                        },
                        979: {
                            items: 2,
                        },
                        1199: {
                            items: settings.count,
                        }
                    },
                    dots: true,
                    nav: false,
                    autoplayHoverPause: true,
                    rtl: owl_rtl,
                });

                if (settings.autoplay_status) {
                    owl.bind(
                        "mouseleave",
                        function (event) {
                            $("#mec_skin_" + settings.id + " .mec-owl-carousel").trigger('play.owl.autoplay');
                        }
                    );
                }
            } else if (settings.style === 'type4') {
                $("#mec_skin_" + settings.id + " .mec-owl-carousel").owlCarousel({
                    autoplay: settings.autoplay_status,
                    loop: settings.loop,
                    autoplayTimeout: settings.autoplay,
                    items: settings.items,
                    dots: false,
                    nav: true,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1,
                            stagePadding: 50,
                        },
                        979: {
                            items: 2,
                        },
                        1199: {
                            items: settings.count,
                        }
                    },
                    autoplayHoverPause: true,
                    navText: ["<i class='mec-sl-arrow-left'></i>", " <i class='mec-sl-arrow-right'></i>"],
                    rtl: owl_rtl,
                });

                if (settings.autoplay_status) {
                    $("#mec_skin_" + settings.id + " .mec-owl-carousel").bind(
                        "mouseleave",
                        function (event) {
                            $("#mec_skin_" + settings.id + " .mec-owl-carousel").trigger('play.owl.autoplay');
                        }
                    );
                }
            } else {
                $("#mec_skin_" + settings.id + " .mec-owl-carousel").owlCarousel({
                    autoplay: settings.autoplay_status,
                    loop: settings.loop,
                    autoplayTimeout: settings.autoplay,
                    items: settings.items,
                    dots: typeof settings.dots_navigation != 'undefined' ? settings.dots_navigation : false,
                    nav: typeof settings.navigation != 'undefined' ? settings.navigation : true,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1,
                        },
                        979: {
                            items: 2,
                        },
                        1199: {
                            items: settings.count,
                        }
                    },
                    autoplayHoverPause: true,
                    navText: typeof settings.navText != 'undefined' ? settings.navText : ["<i class='mec-sl-arrow-left'></i>", " <i class='mec-sl-arrow-right'></i>"],
                    rtl: owl_rtl,
                });

                if (settings.autoplay_status) {
                    $("#mec_skin_" + settings.id + " .mec-owl-carousel").bind(
                        "mouseleave",
                        function (event) {
                            $("#mec_skin_" + settings.id + " .mec-owl-carousel").trigger('play.owl.autoplay');
                        }
                    );
                }
            }
        }
    };

    function sed(settings) {
        // Single Event Display
        $("#mec_skin_" + settings.id + " .mec-event-carousel-title a, #mec_skin_" + settings.id + " .mec-event-image a, #mec_skin_" + settings.id + " .mec-booking-button, #mec_skin_" + settings.id + " .mec-event-button").off('click').on('click', function (e) {
            var sed_method = $(this).attr('target');
            if ('_blank' === sed_method) {

                return;
            }
            e.preventDefault();
            var href = $(this).attr('href');

            var id = $(this).data('event-id');
            var occurrence = get_parameter_by_name('occurrence', href);
            var time = get_parameter_by_name('time', href);

            mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
        });
    }
}(jQuery));

// MEC SLIDER VIEW PLUGIN
(function ($) {
    $.fn.mecSliderView = function (options) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            id: 0,
            atts: '',
            transition_time: 250,
            autoplay: false,
            ajax_url: '',
            sf: {},
            start_date: ''
        }, options);

        var rtl = false;

        // Init Sliders
        initSlider();

        function initSlider() {
            // Check RTL website
            if ($('body').hasClass('rtl')) rtl = true;

            $("#mec_skin_" + settings.id + " .mec-owl-carousel").owlCarousel(
                {
                    autoplay: true,
                    smartSpeed: settings.transition_time,
                    autoplayTimeout: settings.autoplay,
                    loop: true,
                    items: 1,
                    responsiveClass: true,
                    responsive: {
                        0: {
                            items: 1,
                        },
                        960: {
                            items: 1,
                        },
                        1200: {
                            items: 1,
                        }
                    },
                    dots: false,
                    nav: true,
                    autoplayHoverPause: true,
                    navText: typeof settings.navText != 'undefined' ? settings.navText : ["<i class='mec-sl-arrow-left'></i>", " <i class='mec-sl-arrow-right'></i>"],
                    rtl: rtl,
                });
        }
    };
}(jQuery));

// MEC COUNTDOWN MODULE
(function ($) {
    $.fn.mecCountDown = function (options, callBack) {
        // Default Options
        var settings = $.extend({
            // These are the defaults.
            date: null,
            format: null
        }, options);

        var callback = callBack;
        var selector = $(this);

        startCountdown();
        var interval = setInterval(startCountdown, 1000);

        function startCountdown() {
            var eventDate = Date.parse(settings.date) / 1000;
            var currentDate = Math.floor($.now() / 1000);

            if (eventDate <= currentDate) {
                callback.call(this);
                clearInterval(interval);
            }

            var seconds = eventDate - currentDate;

            var days = Math.floor(seconds / (60 * 60 * 24));
            seconds -= days * 60 * 60 * 24;

            var hours = Math.floor(seconds / (60 * 60));
            seconds -= hours * 60 * 60;

            var minutes = Math.floor(seconds / 60);
            seconds -= minutes * 60;

            if (days == 1) selector.find(".mec-timeRefDays").text(mecdata.day);
            else selector.find(".mec-timeRefDays").text(mecdata.days);

            if (hours == 1) selector.find(".mec-timeRefHours").text(mecdata.hour);
            else selector.find(".mec-timeRefHours").text(mecdata.hours);

            if (minutes == 1) selector.find(".mec-timeRefMinutes").text(mecdata.minute);
            else selector.find(".mec-timeRefMinutes").text(mecdata.minutes);

            if (seconds == 1) selector.find(".mec-timeRefSeconds").text(mecdata.second);
            else selector.find(".mec-timeRefSeconds").text(mecdata.seconds);

            if (settings.format === "on") {
                days = (String(days).length >= 2) ? days : "0" + days;
                hours = (String(hours).length >= 2) ? hours : "0" + hours;
                minutes = (String(minutes).length >= 2) ? minutes : "0" + minutes;
                seconds = (String(seconds).length >= 2) ? seconds : "0" + seconds;
            }

            if (!isNaN(eventDate)) {
                selector.find(".mec-days").text(days);
                selector.find(".mec-hours").text(hours);
                selector.find(".mec-minutes").text(minutes);
                selector.find(".mec-seconds").text(seconds);
            } else {
                clearInterval(interval);
            }
        }
    };

}(jQuery));

// MEC TILE VIEW PLUGIN
(function ($) {
    $.fn.mecTileView = function (options) {
        var active_month;
        var active_year;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            id: 0,
            events_label: 'Events',
            event_label: 'Event',
            month_navigator: 0,
            atts: '',
            active_month: {},
            next_month: {},
            sf: {},
            ajax_url: ''
        }, options);

        // Initialize Month Navigator
        if (settings.month_navigator) initMonthNavigator();

        // Load Next Month in background
        if (settings.load_method === 'month') setMonth(settings.next_month.year, settings.next_month.month, true);

        active_month = settings.active_month.month;
        active_year = settings.active_month.year;

        // Set onclick Listeners
        setListeners();

        // Search Widget
        if (settings.sf.container !== '') {
            sf = $(settings.sf.container).mecSearchForm(
                {
                    id: settings.id,
                    refine: settings.sf.refine,
                    ajax_url: settings.ajax_url,
                    atts: settings.atts,
                    callback: function (atts) {
                        settings.atts = atts;
                        search(active_year, active_month);
                    }
                });
        }

        function initMonthNavigator() {
            $("#mec_skin_" + settings.id + " .mec-load-month").off("click").on("click", function () {
                var year = $(this).data("mec-year");
                var month = $(this).data("mec-month");

                setMonth(year, month, false, true);
            });
        }

        function search(year, month) {
            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            $.ajax(
                {
                    url: settings.ajax_url,
                    data: "action=mec_tile_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=1",
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        if (settings.load_method === 'month') {
                            active_month = response.current_month.month;
                            active_year = response.current_month.year;

                            // Append Month
                            $("#mec_skin_events_" + settings.id).html('<div class="mec-month-container" id="mec_tile_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                            // Append Month Navigator
                            $("#mec_skin_" + settings.id + " .mec-skin-tile-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                            // Re-initialize Month Navigator
                            initMonthNavigator();

                            // Set onclick Listeners
                            setListeners();

                            // Toggle Month
                            toggleMonth(response.current_month.id);
                        }
                        else {
                            // Append Items
                            $("#mec_skin_events_" + settings.id).html(response.html);

                            // Show load more button
                            if (response.count >= settings.limit) $("#mec_skin_" + settings.id + " .mec-load-more-button").removeClass("mec-util-hidden");
                            // Hide load more button
                            else $("#mec_skin_" + settings.id + " .mec-load-more-button").addClass("mec-util-hidden");

                            // Update the variables
                            settings.end_date = response.end_date;
                            settings.offset = response.offset;

                            // Set onclick Listeners
                            setListeners();
                        }

                        // Remove loading Class
                        $('.mec-modal-result').removeClass("mec-month-navigator-loading");
                    },
                    error: function () { }
                });
        }

        function setMonth(year, month, do_in_background, navigator_click) {
            if (typeof do_in_background === "undefined") do_in_background = false;
            navigator_click = navigator_click || false;

            var month_id = year + "" + month;

            if (!do_in_background) {
                active_month = month;
                active_year = year;
            }

            // Month exists so we just show it
            if ($("#mec_tile_month_" + settings.id + "_" + month_id).length) {
                // Toggle Month
                toggleMonth(month_id);
            }
            else {
                if (!do_in_background) {
                    // Add Loading Class
                    if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                    jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
                }

                $.ajax(
                    {
                        url: settings.ajax_url,
                        data: "action=mec_tile_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=0" + "&navigator_click=" + navigator_click,
                        dataType: "json",
                        type: "post",
                        success: function (response) {
                            // Append Month
                            $("#mec_skin_events_" + settings.id).append('<div class="mec-month-container" id="mec_tile_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                            // Append Month Navigator
                            $("#mec_skin_" + settings.id + " .mec-skin-tile-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                            // Re-initialize Month Navigator
                            initMonthNavigator();

                            // Set onclick Listeners
                            setListeners();

                            if (!do_in_background) {
                                // Toggle Month
                                toggleMonth(response.current_month.id);

                                // Remove loading Class
                                $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                                // Set Month Filter values in search widget
                                $("#mec_sf_month_" + settings.id).val(month);
                                $("#mec_sf_year_" + settings.id).val(year);
                            }
                            else {
                                $("#mec_tile_month_" + settings.id + "_" + response.current_month.id).hide();
                                $("#mec_month_navigator_" + settings.id + "_" + response.current_month.id).hide();
                            }
                        },
                        error: function () { }
                    });
            }
        }

        function toggleMonth(month_id) {
            var active_month = $("#mec_skin_" + settings.id + " .mec-month-container-selected").data("month-id");
            var active_day = $("#mec_tile_month_" + settings.id + "_" + active_month + " .mec-selected-day").data("day");

            if (active_day <= 9) active_day = "0" + active_day;

            // Toggle Month Navigator
            $("#mec_skin_" + settings.id + " .mec-month-navigator").hide();
            $("#mec_month_navigator_" + settings.id + "_" + month_id).show();

            // Toggle Month
            $("#mec_skin_" + settings.id + " .mec-month-container").hide().removeClass("mec-month-container-selected");
            $("#mec_tile_month_" + settings.id + "_" + month_id).show().addClass("mec-month-container-selected");
        }

        var sf;

        function setListeners() {
            $("#mec_skin_" + settings.id + " .mec-load-more-button").off("click").on("click", function () {
                loadMore();
            });

            $("#mec_skin_" + settings.id + " article").off("click").on("click", function (e) {
                // Link Clicked
                if (e.target.nodeName.toLowerCase() === 'a') return;

                var href = $(this).data('href');
                if (!href) return;

                var target = $(this).data('target');

                if (target === 'blank') window.open(href, '_blank');
                else if (target !== 'm1') document.location.href = href;
            });

            // Add the onclick event
            $("#mec_skin_" + settings.id + " .mec-has-event").off("click").on('click', function (e) {
                e.preventDefault();

                // define variables
                var $this = $(this),
                    data_mec_cell = $this.data('mec-cell'),
                    month_id = $this.data('month');

                $("#mec_monthly_view_month_" + settings.id + "_" + month_id + " .mec-calendar-day").removeClass('mec-selected-day');
                $this.addClass('mec-selected-day');

                $('#mec_month_side_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec:not([data-mec-cell=' + data_mec_cell + '])').slideUp();
                $('#mec_month_side_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec[data-mec-cell=' + data_mec_cell + ']').slideDown();

                $('#mec_monthly_view_month_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec:not([data-mec-cell=' + data_mec_cell + '])').slideUp();
                $('#mec_monthly_view_month_' + settings.id + '_' + month_id + ' .mec-calendar-events-sec[data-mec-cell=' + data_mec_cell + ']').slideDown();
            });

            // Single Event Method
            if (settings.sed_method != '0') {
                sed();
            }
        }

        function sed() {
            // Single Event Display
            $("#mec_skin_" + settings.id + " .mec-event-content").off('click').on('click', function (e) {
                var sed_method = $(this).parent().data('target');
                if ('_blank' === sed_method) {
                    return;
                }
                e.preventDefault();
                var href = $(this).parent().data('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }

        function loadMore() {
            // Load More Button
            var $load_more_button = $("#mec_skin_" + settings.id + " .mec-load-more-button");

            // Add loading Class
            $load_more_button.addClass("mec-load-more-loading");

            $.ajax(
                {
                    url: settings.ajax_url,
                    data: "action=mec_tile_load_more&mec_start_date=" + settings.end_date + "&mec_offset=" + settings.offset + "&" + settings.atts + "&current_month_divider=" + settings.current_month_divider + "&apply_sf_date=0",
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        if (response.count == '0') {
                            // Remove loading Class
                            $load_more_button.removeClass("mec-load-more-loading");

                            // Hide load more button
                            $load_more_button.addClass("mec-util-hidden");
                        }
                        else {
                            // Show load more button
                            if (typeof response.has_more_event === 'undefined' || (typeof response.has_more_event !== 'undefined' && response.has_more_event)) $load_more_button.removeClass("mec-util-hidden");
                            else $load_more_button.addClass("mec-util-hidden");

                            // Append Items
                            $("#mec_skin_events_" + settings.id).append(response.html);

                            // Remove loading Class
                            $load_more_button.removeClass("mec-load-more-loading");

                            // Update the variables
                            settings.end_date = response.end_date;
                            settings.offset = response.offset;
                            settings.current_month_divider = response.current_month_divider;

                            // Single Event Method
                            if (settings.sed_method != '0') {
                                sed();
                            }
                        }
                    },
                    error: function () { }
                });
        }
    };
}(jQuery));

function mec_gateway_selected(gateway_id) {
    // Hide all gateway forms
    jQuery('.mec-book-form-gateway-checkout').addClass('mec-util-hidden');

    // Show selected gateway form
    jQuery('#mec_book_form_gateway_checkout' + gateway_id).removeClass('mec-util-hidden');
}

function mec_wrap_resize() {
    var $mec_wrap = jQuery('.mec-wrap'),
        mec_width = $mec_wrap.width();
    if (mec_width < 959) {
        $mec_wrap.addClass('mec-sm959');
    } else {
        $mec_wrap.removeClass('mec-sm959');
    }
}

function get_parameter_by_name(name, url) {
    if (!url) {
        url = window.location.href;
    }

    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);

    if (!results) return null;
    if (!results[2]) return '';

    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

// Focus events day
var mec_g_month_id = null;

function mecFocusDay(settings) {
    if (mec_g_month_id != null) {
        setTimeout(function () {
            var id = settings.id,
                date = new Date(),
                mec_owl_year = mec_g_month_id.substr(0, 4),
                mec_current_year = date.getFullYear(),
                mec_owl_month = mec_g_month_id.substr(4, 6),
                mec_current_month = date.getMonth() + 1,
                mec_current_day = date.getDate(),
                mec_owl_go = jQuery("#mec-owl-calendar-d-table-" + id + "-" + mec_g_month_id),
                mec_day_exist = false;
            mec_owl_go.find('.owl-stage > div').each(function (index) {
                if (parseInt(jQuery(this).children('div').data("events-count")) > 0) {
                    if ((((mec_owl_year != mec_current_year) && (mec_owl_month != mec_current_month)) || (mec_owl_year == mec_current_year) && (mec_owl_month != mec_current_month)) || parseInt(jQuery(this).children('div').text()) > mec_current_day) {
                        var index_plus = index + 1;
                        jQuery('#mec_daily_view_day' + id + '_' + mec_g_month_id + (index < 10 ? '0' + index_plus : index_plus)).trigger('click');
                        mec_owl_go.trigger('to.owl.carousel', index_plus);
                        mec_day_exist = true;
                        return false;
                    }
                    else {
                        jQuery('#mec_daily_view_day' + id + '_' + mec_g_month_id + mec_current_day).trigger('click');
                        mec_owl_go.trigger('to.owl.carousel', mec_current_day);
                        mec_day_exist = true;
                        return false;
                    }
                }
            });

            if (!mec_day_exist && ((mec_owl_year == mec_current_year) && (mec_owl_month == mec_current_month))) {
                jQuery('#mec_daily_view_day' + id + '_' + mec_g_month_id + mec_current_day).trigger('click');
                mec_owl_go.trigger('to.owl.carousel', mec_current_day);
            }
        }, 1000);
    }
}

// Focus events week
function mec_focus_week(id, skin) {
    skin = skin || 'weekly';
    var wrap_elem = jQuery('.mec-weeks-container .mec-weekly-view-week-active').parent();
    var days = wrap_elem.find('dt');
    var week = wrap_elem.find('dl').length;
    var focus_week = false;
    var i = j = 1;

    for (i = 1; i < week; i++) {
        setTimeout(function () {
            var event = new Event('click');
            jQuery('#mec_skin_' + id + ' .mec-previous-month.mec-load-week')[0].dispatchEvent(event);
        }, 33);
    }

    days.each(function (i) {
        if (jQuery(this).data('events-count') > 0) {
            if (focus_week === false) {
                focus_week = parseInt(jQuery(this).parent().data('week-number'));
            }

            if (skin == 'timetable') {
                if (parseInt(jQuery(this).parent().data('week-number')) == parseInt(jQuery('body').data('currentweek'))) {
                    focus_week = parseInt(jQuery(this).parent().data('week-number'));
                    return false;
                }
            }
            else {
                return false;
            }
        }
    });

    if (focus_week !== false) {
        for (j = 1; j < focus_week; j++) {
            setTimeout(function () {
                var event = new Event('click');
                jQuery('#mec_skin_' + id + ' .mec-next-month.mec-load-week')[0].dispatchEvent(event);
            }, 33);
        }
    }
}

// TODO must be cleaned JS codes
(function ($) {
    $(document).ready(function () {
        // Check RTL website
        if ($('body').hasClass('rtl')) {
            var owl_rtl = true;
        } else {
            var owl_rtl = false;
        }

        // MEC WIDGET CAROUSEL
        $(".mec-widget .mec-event-grid-classic").each(function () {
            var loop_status = $(this).data('widget-loop');
            if (typeof loop_status === 'undefined') loop_status = 1;

            var autoplay_status = $(this).data('widget-autoplay');
            if (typeof autoplay_status === 'undefined') autoplay_status = 1;

            var autoplay_time = $(this).data('widget-autoplay-time');
            if (typeof autoplay_time === 'undefined') autoplay_time = 3000;

            $(this).addClass('mec-owl-carousel mec-owl-theme');
            $(this).owlCarousel(
                {
                    autoplay: (autoplay_status ? true : false),
                    autoplayTimeout: autoplay_time,
                    autoplayHoverPause: true,
                    loop: (loop_status ? true : false),
                    dots: false,
                    nav: true,
                    navText: ["<i class='mec-sl-arrow-left'></i>", " <i class='mec-sl-arrow-right'></i>"],
                    items: 1,
                    autoHeight: true,
                    responsiveClass: true,
                    rtl: owl_rtl,
                });
        });

        // add mec-sm959 class if mec-wrap div size < 959
        mec_wrap_resize();

        jQuery(window).bind('resize', function () {
            mec_wrap_resize();
        });

        // Fixed: social hover in iphone
        $('.mec-event-sharing-wrap').hover(function () {
            $(this).find('.mec-event-sharing').show(0);
        },
            function () {
                $(this).find('.mec-event-sharing').hide(0);
            });

        // Register Booking Smooth Scroll
        $('a.simple-booking[href^="#mec-events-meta-group-booking"]').click(function () {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');

                if (target.length) {
                    var scrollTopVal = target.offset().top - 30;

                    $('html, body').animate({
                        scrollTop: scrollTopVal
                    }, 600);

                    return false;
                }
            }
        });

        // Load Information widget under title in mobile/tablet
        if ($('.single-mec-events .mec-single-event:not(".mec-single-modern")').length > 0) {
            if ($('.single-mec-events .mec-event-info-desktop.mec-event-meta.mec-color-before.mec-frontbox').length > 0) {
                var html = $('.single-mec-events .mec-event-info-desktop.mec-event-meta.mec-color-before.mec-frontbox')[0].outerHTML;
                if (Math.max(document.documentElement.clientWidth, window.innerWidth || 0) < 960) {
                    $('.single-mec-events .col-md-4 .mec-event-info-desktop.mec-event-meta.mec-color-before.mec-frontbox').remove();
                    $('.single-mec-events .mec-event-info-mobile').html(html)
                }
            }
        }
    });
})(jQuery);

// Weather
(function ($) {
    // Convart fahrenheit to centigrade
    function convertToC(value) {
        return Math.round(((parseFloat(value) - 32) * 5 / 9));
    }

    // Convert centigrade to fahrenheit
    function convertToF(value) {
        return Math.round(((1.8 * parseFloat(value)) + 32));
    }

    // Convert miles to kilometers
    function MPHToKPH(value) {
        return Math.round(1.609344 * parseFloat(value));
    }

    // Convert kilometers to miles
    function KPHToMPH(value) {
        return Math.round((0.6214 * parseFloat(value)));
    }

    $(document).ready(function ($) {
        var degree = $('.mec-weather-summary-temp');
        var weather_extra = $('.mec-weather-extras');
        var wind = weather_extra.children('.mec-weather-wind');
        var visibility = weather_extra.children('.mec-weather-visibility');
        var feelslike = weather_extra.children('.mec-weather-feels-like');

        //  Events
        $('.degrees-mode').on('click', function () {
            var degree_mode = degree.children('var').text().trim();
            var wind_text = wind.text().substring(5);
            var visibility_text = visibility.text().substring(11);
            var feelslike_text = feelslike.text().substring(12);

            if (degree_mode == degree.data('c').trim()) {
                degree.html(convertToF(parseInt(degree.text())) + ' <var>' + degree.data('f') + '</var>');
                feelslike.html('<span>Feels Like:</span> ' + convertToF(parseInt(feelslike_text)) + ' <var>' + feelslike.data('f') + '</var>');
                wind.html('<span>Wind:</span> ' + KPHToMPH(parseInt(wind_text)) + '<var>' + wind.data('mph') + '</var>');
                visibility.html('<span>Visibility:</span> ' + KPHToMPH(parseInt(visibility_text)) + '<var>' + visibility.data('mph') + '</var>');
                $(this).text($(this).data('metric'));
            }
            else if (degree_mode == degree.data('f').trim()) {
                degree.html(convertToC(parseInt(degree.text())) + ' <var>' + degree.data('c') + '</var>');
                feelslike.html('<span>Feels Like:</span> ' + convertToC(parseInt(feelslike_text)) + ' <var>' + feelslike.data('c') + '</var>');
                wind.html('<span>Wind:</span> ' + MPHToKPH(parseInt(wind_text)) + '<var>' + wind.data('kph') + '</var>');
                visibility.html('<span>Visibility:</span> ' + MPHToKPH(parseInt(visibility_text)) + '<var>' + visibility.data('kph') + '</var>');
                $(this).text($(this).data('imperial'));
            }
        });

        $('a').on('click', function () { });

        // FES Speakers Adding
        $('#mec_add_speaker_button').on('click', function () {
            var $this = this;
            var content = $($this).parent().find('input');
            var list = $('#mec-fes-speakers-list');
            var key = list.find('.mec-error').length;

            $($this).prop("disabled", true).css('cursor', 'wait');
            $.post(ajaxurl, {
                action: "speaker_adding",
                content: content.val(),
                key: key
            })
                .done(function (data) {
                    if ($(data).hasClass('mec-error')) {
                        list.prepend(data);
                        setTimeout(function () {
                            $('#mec-speaker-error-${key}').remove();
                        }, 1500);
                    } else {
                        list.html(data);
                        content.val('');
                    }

                    $($this).prop("disabled", false).css('cursor', 'pointer');
                });
        });

        // Check RTL website
        var owl_rtl = $('body').hasClass('rtl') ? true : false;

        // MEC FES Date Wrappers
        var fes_export_list = $('.mec-export-list-wrapper');

        // MEC FES Date Item Event
        fes_export_list.find('.mec-export-list-item').click(function () {
            $('.mec-export-list-item').removeClass('fes-export-date-active');
            $(this).addClass('fes-export-date-active');
        });

        // MEC BuddyPress Integration Attendees Modules
        var mec_bd_attendees_modules = $('.mec-attendees-list-details > ul > li');
        mec_bd_attendees_modules.click(function () {
            $(this).find('.mec-attendees-toggle').toggle();
        });

        // MEC FES export csv
        $('.mec-event-export-csv, .mec-event-export-excel').click(function () {
            var mec_event_id = $(this).parent().parent().data('event-id');

            var time = $(this).parent().parent().find($('.fes-export-date-active')).data('time');
            if (typeof time === 'undefined') time = 0;

            var type = $(this).hasClass('mec-event-export-excel') ? 'ms-excel' : 'csv';

            var url = mecdata.ajax_url + "?action=mec_fes_csv_export&fes_nonce=" + mecdata.fes_nonce + "&mec_event_id=" + mec_event_id + "&timestamp=" + time + "&type=" + type;

            window.location = url;
        });
    });
})(jQuery);

function mec_book_form_submit(event, unique_id) {
    event.preventDefault();
    window["mec_book_form_submit" + unique_id]();
}

function mec_book_form_back_btn_cache(context, unique_id) {
    var id = jQuery(context).attr('id');
    var mec_form_data = jQuery('#mec_book_form' + unique_id).serializeArray();

    if (id == "mec-book-form-btn-step-1") jQuery('body').data('mec-book-form-step-1', jQuery('#mec_booking' + unique_id).html()).data('unique-id', unique_id).data('mec-book-form-data-step-1', mec_form_data);
    else if (id == "mec-book-form-btn-step-2") jQuery('body').data('mec-book-form-step-2', jQuery('#mec_booking' + unique_id).html()).data('mec-book-form-data-step-2', mec_form_data);
}

function mec_agreement_change(context) {
    var status = jQuery(context).is(":checked");

    if (status) jQuery(context).prop("checked", "checked");
    else jQuery(context).removeProp("checked");
}

function mec_book_form_back_btn_click(context, unique_id) {
    var id = jQuery(context).attr('id');
    unique_id = jQuery('body').data('unique-id');

    jQuery('#mec_booking_message' + unique_id).hide();
    if (id == "mec-book-form-back-btn-step-2") {
        var mec_form_data_step_1 = jQuery('body').data('mec-book-form-data-step-1');
        console.log(mec_form_data_step_1);

        jQuery('#mec_booking' + unique_id).html(jQuery('body').data('mec-book-form-step-1'));
        jQuery.each(mec_form_data_step_1, function(index, object_item)
        {
            if(object_item.name === 'book[date][]')
            {
                jQuery('[value="' + object_item.value + '"]').prop('checked', true);
            }
            else jQuery('[name="' + object_item.name + '"]').val(object_item.value);
        });

        // Booking Refresh Recaptcha When Back Button Click.
        var recaptcha_check = jQuery('#mec_booking' + unique_id).find('#g-recaptcha').length;
        if (recaptcha_check != 0) {
            jQuery('#g-recaptcha').html('');
            grecaptcha.render("g-recaptcha", {
                sitekey: mecdata.recapcha_key
            });
        }

        var event_id = jQuery('input[name="event_id"]').val();
        var date = jQuery('#mec_book_form_date' + unique_id).val();

        // Update Availability
        window['mec_get_tickets_availability' + unique_id](event_id, date);
    }
    else if (id == "mec-book-form-back-btn-step-3") {
        var mec_form_data_step_2 = jQuery('body').data('mec-book-form-data-step-2');

        jQuery('#mec_booking' + unique_id).html(jQuery('body').data('mec-book-form-step-2'));
        jQuery.each(mec_form_data_step_2, function (index, object_item) {
            var mec_elem = jQuery('[name="' + object_item.name + '"]');
            var mec_type = mec_elem.attr('type');

            if ((mec_type == 'checkbox' || mec_type == 'radio')) {
                var mec_elem_len = jQuery('[name="' + object_item.name + '"]').length;

                if (mec_elem_len > 1) {
                    var id = '#' + mec_elem.attr('id').match(/mec_book_reg_field_reg.*_/g) + object_item.value.toLowerCase();
                    jQuery(id).prop('checked', true);
                }
                else {
                    mec_elem.prop('checked', true);
                }
            }

            mec_elem.val(object_item.value);
        });
    }
}

// Google map Skin
function gmapSkin(NewJson) {
    var gmap_temp = jQuery("#gmap-data");
    var beforeJson = gmap_temp.val();
    if (typeof beforeJson === 'undefined') beforeJson = '';

    var newJson = NewJson;
    var jsonPush = (typeof beforeJson != 'undefined' && beforeJson.trim() == "") ? [] : JSON.parse(beforeJson);
    var pushState = jsonPush.length < 1 ? false : true;

    for (var key in newJson) {
        if (pushState) {
            jsonPush.forEach(function (Item, Index) {
                var render_location = jsonPush[Index].latitude + "," + jsonPush[Index].longitude;
                if (key.trim() == render_location.trim()) {
                    // LightBox Count Update
                    newJson[key].count = newJson[key].count + jsonPush[Index].count;

                    // LightBox Ids Update
                    newJson[key].event_ids = newJson[key].event_ids.concat(jsonPush[Index].event_ids);

                    // LightBox Initialize
                    var dom = jQuery(newJson[key].lightbox).find("div:nth-child(2)");
                    var main_items = dom.html();
                    var new_items = jQuery(jsonPush[Index].lightbox).find("div:nth-child(2)").html();

                    var render_items = dom.html(main_items + new_items).html();
                    var new_info_lightbox = '<div><div class="mec-event-detail mec-map-view-event-detail"><i class="mec-sl-map-marker"></i> ' + newJson[key].name + '</div><div>' + render_items + '</div></div>';
                    newJson[key].lightbox = new_info_lightbox;

                    // LightBox info
                    var new_info_window = '<div class="mec-marker-infowindow-wp"><div class="mec-marker-infowindow-count">' + newJson[key].count + '</div><div class="mec-marker-infowindow-content"><span>Event at this location</span><span>' + newJson[key].name + '</span></div></div>';
                    newJson[key].infowindow = new_info_window;

                    // Remove before values of this location
                    jsonPush.splice(Index, 1);
                }
            });
        }

        jsonPush.push(newJson[key]);
    }

    gmap_temp.val(JSON.stringify(jsonPush));
    return jsonPush;
}

// Fluent Scripts
jQuery(document).ready(function () {
    if (jQuery('.mec-fluent-wrap').length < 0) {
        return;
    }
    // Events
    jQuery(window).on('resize', mecFluentToggoleDisplayValueFilterContent);
    jQuery(document).on('click', '.mec-fluent-wrap .mec-filter-icon', mecFluentToggleFilterContent);
    jQuery(document).on('click', '.mec-fluent-wrap .mec-more-events-icon', mecFluentToggleMoreEvents);
    jQuery(document).on('click', '.mec-fluent-wrap .mec-yearly-calendar', mecFluentYearlyCalendar);
    jQuery(document).on('click', mecFluentOutsideEvent);
    jQuery(document).on('click', '.mec-fluent-more-views-icon', mecFluentMoreViewsContent);
    jQuery(document).on('change', '.mec-fluent-wrap .mec-filter-content select, .mec-fluent-wrap .mec-filter-content input', mecFluentSmartFilterIcon);
    // Run
    mecFluentTimeTableUI();
    mecFluentUI();
    mecFluentNiceSelect();
    mecFluentWrapperFullScreenWidth();
    jQuery(window).on('load', mecFluentWrapperFullScreenWidth);
    jQuery(window).on('load', mecFluentCurrentTimePosition);
    jQuery(window).on('resize', mecFluentWrapperFullScreenWidth);
    jQuery(window).on('resize', mecFluentTimeTableUI);
    mecFluentSliderUI();
    mecFluentFullCalendar();
    jQuery(window).on('resize', mecFluentFullCalendar);
    mecFluentCustomScrollbar();
});

function mecFluentSinglePage() {
    if (jQuery().niceScroll) {
        jQuery('.mec-single-fluent-body .featherlight .mec-single-fluent-wrap').niceScroll({
            horizrailenabled: false,
            cursorcolor: '#C1C5C9',
            cursorwidth: '4px',
            cursorborderradius: '4px',
            cursorborder: 'none',
            railoffset: {
                left: 10,
            }
        });
    }
}

function mecFluentFullCalendar() {
    if (jQuery('.mec-fluent-wrap.mec-skin-full-calendar-container').length > 0) {
        var widowWidth = jQuery(window).innerWidth();
        if (widowWidth <= 767) {
            jQuery('.mec-fluent-wrap.mec-skin-full-calendar-container .mec-skin-monthly-view-month-navigator-container, .mec-fluent-wrap.mec-skin-full-calendar-container .mec-calendar-a-month, .mec-fluent-wrap.mec-skin-full-calendar-container .mec-yearly-title-sec').css({
                paddingTop: jQuery('.mec-fluent-wrap.mec-skin-full-calendar-container').children('.mec-totalcal-box').height() + 40,
            });
        } else {
            jQuery('.mec-fluent-wrap.mec-skin-full-calendar-container .mec-skin-monthly-view-month-navigator-container, .mec-fluent-wrap.mec-skin-full-calendar-container .mec-calendar-a-month, .mec-fluent-wrap.mec-skin-full-calendar-container .mec-yearly-title-sec').css({
                paddingTop: 32,
            });
        }
    }
}

function mecFluentSmartFilterIcon() {
    var filterContent = jQuery(this).closest('.mec-filter-content');
    var hasValue = false;
    if (jQuery(this).closest('.mec-date-search').length > 0) {
        var yearValue = jQuery(this).closest('.mec-date-search').find('select[id*="mec_sf_year"]').val();
        var monthValue = jQuery(this).closest('.mec-date-search').find('select[id*="mec_sf_month"]').val();
        if ((yearValue == 'none' && monthValue == 'none') || (yearValue != 'none' && monthValue != 'none')) {
            filterContent.hide();
            if ((yearValue != 'none' && monthValue != 'none')) {
                hasValue = true;
            } else {
                hasValue = false;
            }
        } else {
            return false;
        }
    } else {
        filterContent.hide();
    }
    if (!hasValue) {
        filterContent.find(':not(.mec-date-search)').find('select, input:not([type="hidden"])').each(function () {
            if (jQuery(this).val()) {
                hasValue = true;
                return false;
            }
        });
    }
    if (hasValue) {
        jQuery(this).closest('.mec-search-form').find('.mec-filter-icon').addClass('active');
    } else {
        jQuery(this).closest('.mec-search-form').find('.mec-filter-icon').removeClass('active');
    }
}

function mecFluentMoreViewsContent() {
    jQuery(this).find('.mec-fluent-more-views-content').toggleClass('active');
}

function mecFluentWrapperFullScreenWidth() {
    if (jQuery('.mec-fluent-bg-wrap').length > 0) {
        jQuery('.mec-fluent-bg-wrap').css({
            maxWidth: jQuery('body').width() + 8,
        });
    }
}

function mecFluentUI() {
    if (typeof mecdata != 'undefined' && typeof mecdata.enableSingleFluent != 'undefined' && mecdata.enableSingleFluent) {
        jQuery('body').addClass('mec-single-fluent-body');
    }
    // Set filter content position
    jQuery(window).on('load resize', function () {
        if (jQuery('.mec-filter-content').length > 0) {
            jQuery('.mec-filter-content').css({
                right: -(jQuery('.mec-calendar').width() - jQuery('.mec-search-form.mec-totalcal-box').position().left - jQuery('.mec-search-form.mec-totalcal-box').width() + 40),
                left: -jQuery('.mec-search-form.mec-totalcal-box').position().left + 40,
            });
        }
        if (jQuery('.mec-filter-icon').is(':visible')) {
            var filterIconLeftPosition = parseInt(jQuery('.mec-search-form.mec-totalcal-box').position().left) + parseInt(jQuery('.mec-filter-icon').position().left) - 25;
            jQuery('head').find('style[title="mecFluentFilterContentStyle"]').remove().end().append('<style title="mecFluentFilterContentStyle">.mec-fluent-wrap .mec-filter-content:before{left: ' + filterIconLeftPosition + 'px;}.mec-fluent-wrap .mec-filter-content:after{left: ' + (filterIconLeftPosition + 1) + 'px;}</style>');
        }
    });
    // Hide empty filter content
    if (jQuery('.mec-filter-content').is(':empty')) {
        jQuery('.mec-filter-icon').hide();
    }
    // Prevend Default For Event Share Icon
    jQuery(document).on('click', '.mec-event-share-icon', function (e) {
        e.preventDefault();
    });
}

function mecFluentCurrentTimePosition() {
    if (jQuery('.mec-fluent-wrap').length > 0) {
        jQuery('.mec-fluent-current-time').each(function () {
            var currentTimeMinutes = jQuery(this).data('time');
            var height = jQuery(this).closest('.mec-fluent-current-time-cell').height();
            jQuery(this).css({
                top: (currentTimeMinutes / 60) * height,
            });
        });
    }
}

function mecFluentNiceSelect() {
    if (jQuery('.mec-fluent-wrap').length < 0) {
        return;
    }

    if (jQuery().niceSelect) {
        jQuery('.mec-fluent-wrap').find('.mec-filter-content').find('select').niceSelect();
    }
}

function mecFluentCustomScrollbar(y) {
    if (jQuery('.mec-fluent-wrap').length < 0) {
        return;
    }

    if (jQuery().niceScroll) {
        jQuery('.mec-custom-scrollbar').niceScroll({
            cursorcolor: '#C7EBFB',
            cursorwidth: '4px',
            cursorborderradius: '4px',
            cursorborder: 'none',
            railoffset: {
                left: -2,
            }
        });
        jQuery('.mec-custom-scrollbar').getNiceScroll().resize();
        jQuery('.mec-custom-scrollbar').each(function () {
            if (jQuery(this).find('.mec-fluent-current-time-cell').length > 0) {
                var parentTopOffset = jQuery(this).offset().top;
                var currentTimeCellOffset = jQuery(this).find('.mec-fluent-current-time-cell').offset().top;
                jQuery(this).getNiceScroll(0).doScrollTop(currentTimeCellOffset - parentTopOffset - 16, 120);
                jQuery(this).on('scroll', function () {
                    if (jQuery(this).getNiceScroll(0).scroll.y != 0) {
                        jQuery(this).addClass('mec-scrolling');
                    } else {
                        jQuery(this).removeClass('mec-scrolling');
                    }
                });
            }
            if (typeof y != 'undefined') {
                if (jQuery(this).closest('.mec-skin-list-wrap').length > 0 || jQuery(this).closest('.mec-skin-grid-wrap').length > 0) {
                    jQuery(this).getNiceScroll(0).doScrollTop(0, 120);
                }
            }
        });
    }
}

function mecFluentTimeTableUI() {
    jQuery('.mec-fluent-wrap.mec-timetable-wrap .mec-cell').css('min-height', 0);
    var maxHeight = Math.max.apply(null, jQuery('.mec-fluent-wrap.mec-timetable-wrap .mec-cell').map(function () {
        return jQuery(this).height();
    }).get());
    maxHeight = maxHeight > 87 ? maxHeight : 87;
    jQuery('.mec-fluent-wrap.mec-timetable-wrap .mec-cell').css('min-height', maxHeight + 2);
}

function mecFluentSliderUI() {
    jQuery(window).on('load', function () {
        jQuery('.mec-fluent-wrap.mec-skin-slider-container .owl-next').prepend('<span>Next</span>');
        jQuery('.mec-fluent-wrap.mec-skin-slider-container .owl-prev').append('<span>Prev</span>');
    });
}

function mecFluentToggleFilterContent(e) {
    e.preventDefault();
    if (jQuery('.mec-filter-content').is(':visible')) {
        jQuery('.mec-filter-content').css({
            display: 'none',
        });
    } else {
        const displayValue = jQuery(window).width() <= 790 ? 'block' : 'flex';
        jQuery('.mec-filter-content').css({
            display: displayValue,
        });
    }
}

function mecFluentToggoleDisplayValueFilterContent() {
    const displayValue = jQuery(window).width() <= 767 ? 'block' : 'flex';
    if (jQuery('.mec-filter-content').is(':visible')) {
        jQuery('.mec-filter-content').css({
            display: displayValue,
        });
    }
}

function mecFluentToggleMoreEvents(e) {
    e.preventDefault();
    const moreEventsWrap = jQuery(this).siblings('.mec-more-events-wrap');
    const moreEvents = moreEventsWrap.children('.mec-more-events');
    jQuery('.mec-more-events-wrap').removeClass('active');
    moreEventsWrap.addClass('active');
    jQuery('.mec-more-events-wrap:not(.active)').hide();
    if (moreEventsWrap.is(':visible')) {
        moreEventsWrap.hide();
    } else {
        topElement = moreEventsWrap.closest('.mec-more-events-inner-controller').length > 0 ? moreEventsWrap.closest('.mec-more-events-inner-controller') : moreEventsWrap.closest('.mec-more-events-controller');
        moreEventsWrap.show().css({
            top: topElement.offset().top - window.scrollY,
            left: moreEventsWrap.closest('.mec-more-events-controller').offset().left,
            width: moreEventsWrap.closest('.mec-more-events-controller').width(),
        });
        if (moreEventsWrap.width() > 400) {
            moreEvents.css({
                left: (moreEventsWrap.width() / 2) - (moreEvents.width() / 2),
                width: 400,
            });
        } else {
            moreEvents.css({
                width: moreEventsWrap.width(),
                left: 0,
            });
        }
    }
}

function mecFluentOutsideEvent(e) {
    if (!jQuery(e.target).is('.mec-more-events-icon') && !jQuery(e.target).closest('.mec-more-events-wrap').length) {
        jQuery('.mec-more-events-wrap').hide();
    }
    if (!jQuery(e.target).is('.mec-filter-icon') && !jQuery(e.target).closest('.mec-filter-content').length) {
        jQuery('.mec-filter-content').hide();
    }
    if (!jQuery(e.target).is('.mec-fluent-more-views-icon') && !jQuery(e.target).closest('.mec-fluent-more-views-content').length) {
        jQuery('.mec-fluent-more-views-content').removeClass('active');
    }
}

function mecFluentYearlyCalendar() {
    const monthNum = jQuery(this).data('month');
    const monthName = jQuery(this).find('.mec-calendar-table-title').text();
    jQuery('.mec-fluent-wrap').find('.mec-yearly-calendar').removeClass('active');
    jQuery(this).addClass('active')
        .closest('.mec-year-container')
        .find('.mec-yearly-agenda-sec-title span').text(monthName).end()
        .find('.mec-events-agenda').addClass('mec-util-hidden').end()
        .find('.mec-events-agenda[data-month=' + monthNum + ']').removeClass('mec-util-hidden');
    mecFluentCustomScrollbar();
}

function mecFluentYearlyUI(eventID, yearID) {
    var fluentWrap = jQuery('#mec_skin_' + eventID + '.mec-fluent-wrap');
    if (fluentWrap.length < 0) {
        return;
    }
    var monthNum = fluentWrap.find('.mec-year-container[data-year-id=' + yearID + ']').find('.mec-events-agenda:not(.mec-util-hidden)').data('month');
    var activeMonth = fluentWrap.find('.mec-year-container[data-year-id=' + yearID + ']').find('.mec-yearly-calendar[data-month=' + monthNum + ']');
    var activeMonthName = activeMonth.find('.mec-calendar-table-title').text();
    activeMonth.addClass('active');
}

// MEC LIST VIEW Fluent PLUGIN
(function ($) {
    $.fn.mecListViewFluent = function (options) {
        var active_month;
        var active_year;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            id: 0,
            events_label: 'Events',
            event_label: 'Event',
            month_navigator: 0,
            atts: '',
            active_month: {},
            next_month: {},
            sf: {},
            ajax_url: '',
        }, options);

        mecFluentCustomScrollbar();

        initLoadMore('#mec_list_view_month_' + settings.id + '_' + settings.month_id);

        function initLoadMore(monthID) {
            $(monthID).off().on('click', '.mec-load-more-button', function () {
                loadMore(this);
            });
        }

        function loadMore(This) {
            // Add loading Class
            var currentLoadMore = $(This);
            currentLoadMore.addClass("mec-load-more-loading");
            var endDate = currentLoadMore.data('end-date');
            var maximumDate = currentLoadMore.data('maximum-date');
            var nextOffset = currentLoadMore.data('next-offset');
            var year = currentLoadMore.data('year');
            var month = currentLoadMore.data('month');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_list_load_more&mec_year=" + year + "&mec_month=" + month + "&mec_maximum_date=" + maximumDate + "&mec_start_date=" + endDate + "&mec_offset=" + nextOffset + "&" + settings.atts + "&current_month_divider=0&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    currentLoadMore.parent().remove();
                    if (response.count != '0') {
                        // Append Items
                        $('#mec_list_view_month_' + settings.id + '_' + response.current_month.id).append(response.month);

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }

                        mecFluentCustomScrollbar();
                        initLoadMore('#mec_list_view_month_' + settings.id + '_' + response.current_month.id);
                    }
                },
                error: function () { }
            });
        }

        // Initialize Month Navigator
        if (settings.month_navigator) initMonthNavigator();

        // Load Next Month in background
        setMonth(settings.next_month.year, settings.next_month.month, true);

        var initMonth;
        var initYear;
        active_month = initMonth = settings.active_month.month;
        active_year = initYear = settings.active_month.year;

        // Search Widget
        if (settings.sf.container !== '') {
            sf = $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    search(active_year, active_month);
                }
            });
        }

        // Single Event Method
        if (settings.sed_method != '0') {
            sed();
        }

        function initMonthNavigator() {
            $("#mec_skin_" + settings.id + " .mec-load-month").off().on("click", function () {
                var year = $(this).data("mec-year");
                var month = $(this).data("mec-month");
                setMonth(year, month, false, true);
            });
        }

        function parseQuery(queryString) {
            var query = {};
            var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
            for (var i = 0; i < pairs.length; i++) {
                var pair = pairs[i].split('=');
                query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
            }
            return query;
        }

        function updateQueryStringParameter(uri, key, val) {
            return uri
                .replace(RegExp("([?&]" + key + "(?=[=&#]|$)[^#&]*|(?=#|$))"), "&" + key + "=" + encodeURIComponent(val))
                .replace(/^([^?&]+)&/, "$1?");
        }

        function search(year, month) {
            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            var ObjAtts = parseQuery(settings.atts);
            if (!(ObjAtts['sf[month'] || ObjAtts['sf[year]'])) {
                settings.atts = updateQueryStringParameter(settings.atts.trim(), 'sf[year]', initYear);
                settings.atts = updateQueryStringParameter(settings.atts.trim(), 'sf[month]', initMonth);
            }

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_list_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    active_month = response.current_month.month;
                    active_year = response.current_month.year;

                    // Append Month
                    $("#mec_skin_events_" + settings.id).html('<div class="mec-month-container" id="mec_list_view_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                    // Append Month Navigator
                    $("#mec_skin_" + settings.id + " .mec-skin-list-view-month-navigator-container").html('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                    // Re-initialize Month Navigator
                    initMonthNavigator();

                    // Toggle Month
                    toggleMonth(response.current_month.id);

                    initLoadMore('#mec_list_view_month_' + settings.id + '_' + response.current_month.id);

                    // Remove loading Class
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    mecFluentCustomScrollbar();
                },
                error: function () { }
            });
        }

        function setMonth(year, month, do_in_background, navigator_click) {
            if (typeof do_in_background === "undefined") do_in_background = false;
            navigator_click = navigator_click || false;
            var month_id = year + "" + month;

            if (!do_in_background) {
                active_month = month;
                active_year = year;
            }

            // Month exists so we just show it
            if ($("#mec_list_view_month_" + settings.id + "_" + month_id).length) {
                // Toggle Month
                toggleMonth(month_id);
                mecFluentCustomScrollbar(0);
            } else {
                if (!do_in_background) {
                    // Add Loading Class
                    if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                    jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
                }

                $.ajax({
                    url: settings.ajax_url,
                    data: "action=mec_list_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=0" + "&navigator_click=" + navigator_click,
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Append Month
                        $("#mec_skin_events_" + settings.id).append('<div class="mec-month-container" id="mec_list_view_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                        // Append Month Navigator
                        $("#mec_skin_" + settings.id + " .mec-skin-list-view-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                        // Re-initialize Month Navigator
                        initMonthNavigator();
                        initLoadMore('#mec_list_view_month_' + settings.id + '_' + response.current_month.id);

                        if (!do_in_background) {
                            // Toggle Month
                            toggleMonth(response.current_month.id);

                            // Remove loading Class
                            $('.mec-modal-result').removeClass("mec-month-navigator-loading");


                            // Set Month Filter values in search widget
                            $("#mec_sf_month_" + settings.id).val(month);
                            $("#mec_sf_year_" + settings.id).val(year);
                        } else {
                            $("#mec_list_view_month_" + settings.id + "_" + response.current_month.id).hide();
                            $("#mec_month_navigator_" + settings.id + "_" + response.current_month.id).hide();
                        }
                        if (typeof custom_month !== undefined) var custom_month;
                        if (typeof custom_month != undefined) {
                            if (custom_month == 'true') {
                                $(".mec-month-container .mec-calendar-day").removeClass('mec-has-event');
                                $(".mec-month-container .mec-calendar-day").removeClass('mec-selected-day');
                                $('.mec-calendar-day').unbind('click');
                            }
                        }
                        if (!do_in_background) {
                            mecFluentCustomScrollbar(0);
                        }
                    },
                    error: function () { }
                });
            }
        }

        function toggleMonth(month_id) {
            var active_month = $("#mec_skin_" + settings.id + " .mec-month-container-selected").data("month-id");
            var active_day = $("#mec_list_view_month_" + settings.id + "_" + active_month + " .mec-selected-day").data("day");

            if (active_day <= 9) active_day = "0" + active_day;

            // Toggle Month Navigator
            $("#mec_skin_" + settings.id + " .mec-month-navigator").hide();
            $("#mec_month_navigator_" + settings.id + "_" + month_id).show();

            // Toggle Month
            $("#mec_skin_" + settings.id + " .mec-month-container").hide();
            $("#mec_list_view_month_" + settings.id + "_" + month_id).show();

            // Add selected class
            $("#mec_skin_" + settings.id + " .mec-month-container").removeClass("mec-month-container-selected");
            $("#mec_list_view_month_" + settings.id + "_" + month_id).addClass("mec-month-container-selected");
        }

        var sf;

        function sed() {
            // Single Event Display
            $(".mec-skin-list-wrap#mec_skin_" + settings.id).off('click').on('click', '[data-event-id]', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }
    };
}(jQuery));

// MEC Grid VIEW Fluent PLUGIN
(function ($) {
    $.fn.mecGridViewFluent = function (options) {
        var active_month;
        var active_year;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            today: null,
            id: 0,
            events_label: 'Events',
            event_label: 'Event',
            month_navigator: 0,
            atts: '',
            active_month: {},
            next_month: {},
            sf: {},
            ajax_url: '',
        }, options);

        initLoadMore('#mec_grid_view_month_' + settings.id + '_' + settings.month_id);

        function initLoadMore(monthID) {
            $(monthID).off().on('click', '.mec-load-more-button', function () {
                loadMore(this);
            });
        }

        function loadMore(This) {
            // Add loading Class
            var currentLoadMore = $(This);
            currentLoadMore.addClass("mec-load-more-loading");
            var endDate = currentLoadMore.data('end-date');
            var maximumDate = currentLoadMore.data('maximum-date');
            var nextOffset = currentLoadMore.data('next-offset');
            var year = currentLoadMore.data('year');
            var month = currentLoadMore.data('month');

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_grid_load_more&mec_year=" + year + "&mec_month=" + month + "&mec_maximum_date=" + maximumDate + "&mec_start_date=" + endDate + "&mec_offset=" + nextOffset + "&" + settings.atts + "&current_month_divider=0&apply_sf_date=0",
                dataType: "json",
                type: "post",
                success: function (response) {
                    currentLoadMore.parent().remove();
                    if (response.count != '0') {
                        // Append Items
                        $('#mec_grid_view_month_' + settings.id + '_' + response.current_month.id).append(response.month);

                        // Single Event Method
                        if (settings.sed_method != '0') {
                            sed();
                        }

                        mecFluentCustomScrollbar();
                        initLoadMore('#mec_grid_view_month_' + settings.id + '_' + response.current_month.id);
                    }
                },
                error: function () { }
            });
        }

        // Initialize Month Navigator
        if (settings.month_navigator) initMonthNavigator();

        // Load Next Month in background
        setMonth(settings.next_month.year, settings.next_month.month, true);

        var initMonth;
        var initYear;
        active_month = initMonth = settings.active_month.month;
        active_year = initYear = settings.active_month.year;

        // Search Widget
        if (settings.sf.container !== '') {
            sf = $(settings.sf.container).mecSearchForm({
                id: settings.id,
                refine: settings.sf.refine,
                ajax_url: settings.ajax_url,
                atts: settings.atts,
                callback: function (atts) {
                    settings.atts = atts;
                    search(active_year, active_month);
                }
            });
        }

        // Single Event Method
        if (settings.sed_method != '0') {
            sed();
        }

        function initMonthNavigator() {
            $("#mec_skin_" + settings.id + " .mec-load-month").off().on("click", function () {
                var year = $(this).data("mec-year");
                var month = $(this).data("mec-month");
                setMonth(year, month, false, true);
            });
        }

        function parseQuery(queryString) {
            var query = {};
            var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
            for (var i = 0; i < pairs.length; i++) {
                var pair = pairs[i].split('=');
                query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
            }
            return query;
        }

        function updateQueryStringParameter(uri, key, val) {
            return uri
                .replace(RegExp("([?&]" + key + "(?=[=&#]|$)[^#&]*|(?=#|$))"), "&" + key + "=" + encodeURIComponent(val))
                .replace(/^([^?&]+)&/, "$1?");
        }

        function search(year, month) {
            // Add Loading Class
            if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
            jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');

            var ObjAtts = parseQuery(settings.atts);
            if (!(ObjAtts['sf[month'] || ObjAtts['sf[year]'])) {
                settings.atts = updateQueryStringParameter(settings.atts.trim(), 'sf[year]', initYear);
                settings.atts = updateQueryStringParameter(settings.atts.trim(), 'sf[month]', initMonth);
            }

            $.ajax({
                url: settings.ajax_url,
                data: "action=mec_grid_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=1",
                dataType: "json",
                type: "post",
                success: function (response) {
                    active_month = response.current_month.month;
                    active_year = response.current_month.year;

                    // Append Month
                    $("#mec_skin_events_" + settings.id).html('<div class="mec-month-container" id="mec_grid_view_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                    // Append Month Navigator
                    $("#mec_skin_" + settings.id + " .mec-skin-grid-view-month-navigator-container").html('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                    // Re-initialize Month Navigator
                    initMonthNavigator();

                    // Toggle Month
                    toggleMonth(response.current_month.id);

                    initLoadMore('#mec_grid_view_month_' + settings.id + '_' + response.current_month.id);

                    // Remove loading Class
                    $('.mec-modal-result').removeClass("mec-month-navigator-loading");

                    mecFluentCustomScrollbar();
                },
                error: function () { }
            });
        }

        function setMonth(year, month, do_in_background, navigator_click) {
            if (typeof do_in_background === "undefined") do_in_background = false;
            navigator_click = navigator_click || false;
            var month_id = year + "" + month;

            if (!do_in_background) {
                active_month = month;
                active_year = year;
            }

            // Month exists so we just show it
            if ($("#mec_grid_view_month_" + settings.id + "_" + month_id).length) {
                // Toggle Month
                toggleMonth(month_id);
                mecFluentCustomScrollbar();
            } else {
                if (!do_in_background) {
                    // Add Loading Class
                    if (jQuery('.mec-modal-result').length === 0) jQuery('.mec-wrap').append('<div class="mec-modal-result"></div>');
                    jQuery('.mec-modal-result').addClass('mec-month-navigator-loading');
                }

                $.ajax({
                    url: settings.ajax_url,
                    data: "action=mec_grid_load_month&mec_year=" + year + "&mec_month=" + month + "&" + settings.atts + "&apply_sf_date=0" + "&navigator_click=" + navigator_click,
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // Append Month
                        $("#mec_skin_events_" + settings.id).append('<div class="mec-month-container" id="mec_grid_view_month_' + settings.id + '_' + response.current_month.id + '" data-month-id="' + response.current_month.id + '">' + response.month + '</div>');

                        // Append Month Navigator
                        $("#mec_skin_" + settings.id + " .mec-skin-grid-view-month-navigator-container").append('<div class="mec-month-navigator" id="mec_month_navigator_' + settings.id + '_' + response.current_month.id + '">' + response.navigator + '</div>');

                        // Re-initialize Month Navigator
                        initMonthNavigator();
                        initLoadMore('#mec_grid_view_month_' + settings.id + '_' + response.current_month.id);

                        if (!do_in_background) {
                            // Toggle Month
                            toggleMonth(response.current_month.id);

                            // Remove loading Class
                            $('.mec-modal-result').removeClass("mec-month-navigator-loading");


                            // Set Month Filter values in search widget
                            $("#mec_sf_month_" + settings.id).val(month);
                            $("#mec_sf_year_" + settings.id).val(year);
                        } else {
                            $("#mec_grid_view_month_" + settings.id + "_" + response.current_month.id).hide();
                            $("#mec_month_navigator_" + settings.id + "_" + response.current_month.id).hide();
                        }
                        if (typeof custom_month !== undefined) var custom_month;
                        if (typeof custom_month != undefined) {
                            if (custom_month == 'true') {
                                $(".mec-month-container .mec-calendar-day").removeClass('mec-has-event');
                                $(".mec-month-container .mec-calendar-day").removeClass('mec-selected-day');
                                $('.mec-calendar-day').unbind('click');
                            }
                        }
                        if (!do_in_background) {
                            mecFluentCustomScrollbar();
                        }
                    },
                    error: function () { }
                });
            }
        }

        function toggleMonth(month_id) {
            var active_month = $("#mec_skin_" + settings.id + " .mec-month-container-selected").data("month-id");
            var active_day = $("#mec_grid_view_month_" + settings.id + "_" + active_month + " .mec-selected-day").data("day");

            if (active_day <= 9) active_day = "0" + active_day;

            // Toggle Month Navigator
            $("#mec_skin_" + settings.id + " .mec-month-navigator").hide();
            $("#mec_month_navigator_" + settings.id + "_" + month_id).show();

            // Toggle Month
            $("#mec_skin_" + settings.id + " .mec-month-container").hide();
            $("#mec_grid_view_month_" + settings.id + "_" + month_id).show();

            // Add selected class
            $("#mec_skin_" + settings.id + " .mec-month-container").removeClass("mec-month-container-selected");
            $("#mec_grid_view_month_" + settings.id + "_" + month_id).addClass("mec-month-container-selected");
        }

        var sf;

        function sed() {
            // Single Event Display
            $(".mec-skin-grid-wrap#mec_skin_" + settings.id).off('click').on('click', '[data-event-id]', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                var id = $(this).data('event-id');
                var occurrence = get_parameter_by_name('occurrence', href);
                var time = get_parameter_by_name('time', href);

                mecSingleEventDisplayer.getSinglePage(id, occurrence, time, settings.ajax_url, settings.sed_method, settings.image_popup);
            });
        }
    };
}(jQuery));

// MEC Booking Calendar
(function ($) {
    $.fn.mecBookingCalendar = function (options) {
        var active_month;
        var active_year;

        // Default Options
        var settings = $.extend({
            // These are the defaults.
            active_month: {},
            next_month: {},
            ajax_url: '',
            event_id: '',
        }, options);

        // Initialize Month Navigator
        initMonthNavigator();

        active_month = settings.active_month.month;
        active_year = settings.active_month.year;

        // Set onclick Listeners
        setListeners();

        function initMonthNavigator() {
            // Add onclick event
            $("#mec_booking_calendar_" + settings.id + " .mec-load-month").off('click').on('click', function () {
                var year = $(this).data('mec-year');
                var month = $(this).data('mec-month');

                setMonth(year, month);
            });
        }

        function setMonth(year, month) {
            active_month = month;
            active_year = year;

            var $modal = $('.mec-modal-result');

            // Add Loading Class
            if ($modal.length === 0) $('.mec-wrap').append('<div class="mec-modal-result"></div>');
            $modal.addClass('mec-month-navigator-loading');

            $.ajax(
                {
                    url: settings.ajax_url,
                    data: "action=mec_booking_calendar_load_month&event_id=" + settings.event_id + "&uniqueid=" + settings.id + "&year=" + year + "&month=" + month,
                    dataType: "json",
                    type: "post",
                    success: function (response) {
                        // HTML
                        $('#mec_booking_calendar_wrapper' + settings.id).html(response.html);

                        // Hide Message
                        $('#mec_book_form' + settings.id + ' .mec-ticket-unavailable-spots').addClass('mec-util-hidden');

                        // Empty the Date
                        $('#mec_book_form_date' + settings.id).val('').trigger('change');

                        // Remove loading Class
                        $modal.removeClass("mec-month-navigator-loading");

                    },
                    error: function () {
                        // Remove loading Class
                        $modal.removeClass("mec-month-navigator-loading");
                    }
                });
        }

        function setListeners() {
            // Add the onclick event
            $("#mec_booking_calendar_" + settings.id + " .mec-booking-calendar-date").off('click').on('click', function (e) {
                e.preventDefault();

                // Activate
                $("#mec_booking_calendar_" + settings.id + " .mec-booking-calendar-date").removeClass('mec-active');
                $("#mec_booking_calendar_" + settings.id + " .mec-calendar-day").removeClass('mec-wrap-active');
                $(this).addClass('mec-active');
                $(this).parents('.mec-calendar-day').addClass('mec-wrap-active');

                // Set Data
                var timestamp = $(this).data('timestamp');
                $('#mec_book_form_date' + settings.id).val(timestamp).trigger('change');
            });

            // Add the onclick event on calendar date
            $("#mec_booking_calendar_" + settings.id + " .mec-has-one-repeat-in-day").off('click').on('click', function (e) {
                e.preventDefault();

                var mec_date_value = $(this).attr('data-timestamp');
                // Activate
                $("#mec_booking_calendar_" + settings.id + " .mec-has-one-repeat-in-day").removeClass('mec-active');
                $("#mec_booking_calendar_" + settings.id + " [data-timestamp=\"" + mec_date_value + "\"]").addClass('mec-active');

                // Set Data
                var timestamp = $(this).data('timestamp');
                $('#mec_book_form_date' + settings.id).val(timestamp).trigger('change');
            });

            // If day has some time slot
            $("#mec_booking_calendar_" + settings.id + " .mec-has-time-repeat .mec-calendar-novel-selected-day").off('click').on('click', function (e) {
                $("#mec_booking_calendar_" + settings.id + " .mec-has-time-repeat").removeClass('mec-wrap-active');
                $("#mec_booking_calendar_" + settings.id + " .mec-has-time-repeat").removeClass('mec-active');
                $(".mec-has-time-repeat").find('.mec-booking-calendar-date').hide();
                $(this).parents(".mec-has-time-repeat").find('.mec-booking-calendar-date').toggle();
                $(this).parents(".mec-has-time-repeat").addClass('mec-active');
            });

            // Find more time in tooltip to set button
            $("#mec_booking_calendar_" + settings.id + " .mec-has-time-repeat").on('mouseenter', function () {
                var moreTimeFinder = $(this).find(".mec-booking-calendar-date");
                if (moreTimeFinder.length >= 1) {
                    $(this).find(".mec-booking-tooltip").removeClass("multiple-time");
                    $(this).find(".mec-booking-tooltip").addClass("multiple-time");
                }
                $(this).find(".mec-booking-calendar-date").css("display", "block");
            });

            $("#mec_booking_calendar_" + settings.id + " .mec-has-time-repeat").off('click').on('click', function () {
                $("#mec_booking_calendar_" + settings.id + " .mec-has-time-repeat").removeClass('mec-wrap-active');
                $("#mec_booking_calendar_" + settings.id + " .mec-has-time-repeat").removeClass('mec-active');
                $(this).addClass("mec-active");

                // Send message under the calendar for multiple time in one day
                var sendTimeToMessage = $(this).find(".multiple-time .mec-booking-calendar-date.mec-active").text();

                $(this).parents().eq(3).find(".mec-choosen-time-message").removeClass("disable");
                $(this).parents().eq(3).find(".mec-choosen-time-message .mec-choosen-time").empty();
                $(this).parents().eq(3).find(".mec-choosen-time-message .mec-choosen-time").append(sendTimeToMessage);
            });

        }
    };
}(jQuery));

// Booking Shortcode Scripts
jQuery(document).ready(function () {
    if (jQuery('.mec-booking-shortcode').length < 0) {
        return;
    }
    // Events
    if (jQuery().niceSelect) {
        jQuery('.mec-booking-shortcode').find('.mec-book-first').find('select').niceSelect();
    }

    // General Calendar
    if ( jQuery("#mec-gCalendar-month-filter").length > 0 ) {
        jQuery("#mec-gCalendar-month-filter").MonthPicker({
            Button: '<button class="gCalendarMonthFilterButton"><i class="mec-sl-arrow-down"></i></button>',
            Position: {
                collision: 'fit',
                of: jQuery('.fc-toolbar-chunk')
            }
        });
        jQuery("#mec-gCalendar-month-filter").MonthPicker('option', 'MonthFormat', 'yy-mm-01' );
    }
});