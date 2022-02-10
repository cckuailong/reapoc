/* global jQuery, ecwd, ecwd_calendar */

/**
 * Public JS functions
 */
var functions_interval;

if (typeof ecwd_js_init_call != "object")
    var ecwd_js_init_call;
if (typeof ecwd_js_init != "function")
    var ecwd_js_init;
(function ($) {
    ecwd_js_init = function () {
        ecwd_eventsOff();
        if (jQuery('#ecwd-calendar-main-css').length == 0) {
            jQuery("<link/>", {
                id: 'ecwd-calendar-main-css',
                rel: "stylesheet",
                type: "text/css",
                href: ecwd.plugin_url + '/css/calendar.css?ver=1'
            }).appendTo("head");
        }
        var cwidth = jQuery(".calendar_main .ecwd_calendar").width();
        var view_click = 1;
        var click = 1;
        jQuery('a[href="#ecwd-modal-preview"]').click(function () {
            setTimeout(function () {
                showFilterSliderArrow();
            }, 1);
        });

        clearTimeout(functions_interval);
        functions_interval = setTimeout(function () {
            show_filters(0);
            showMiniCalendarEventOnHover();
            showFullCalendarEventOnHover();
            showWidgetEventDesc();
            calendarFullResponsive();
            showFilterSliderArrow();
            createSearchForm();
            upcomingEventsSlider();
            doMasonry();
        }, 1);


        jQuery('.ecwd_reset_filters').click(function () {
            jQuery(this).closest('.ecwd_filters').find('input:checkbox').prop('checked', false);
        });
        jQuery('.ecwd-calendar-more-event').each(function () {
            jQuery(this).find('.ecwd-more-events-container').ecwd_popup({
                button: jQuery(this).find('.more_events_link'),
                title: jQuery(this).find('.ecwd-more-event-title').val(),
                body_id: "ecwd-modal-preview",
                body_class: "ecwd-excluded-events",
                container_class: "ecwd_more_event",
                after_popup_show: function (el) {
                    el.find('li.inmore').show();
                    el.find('.more_events').find('li').on('click', function () {
                        el.find('li').find('.event-details-container').slideUp();
                        if (jQuery(this).find('.event-details-container').is(":visible"))
                            jQuery(this).find('.event-details-container').slideUp();
                        else
                            jQuery(this).find('.event-details-container').slideDown();
                    });
                    add_single_events_popup();
                },
            });
        });

        add_single_events_popup();

        jQuery('.ecwd_calendar_prev_next .next, .ecwd_calendar_prev_next .previous, .ecwd_calendar .type, .cpage, .current-month a, .ecwd_filter, .ecwd_reset_filters').on('click', function (e) {
            var days = jQuery('input[name="ecwd_weekdays[]"]:checked').map(function () {
                return this.value;
            }).get();
            var cats = jQuery('input[name="ecwd_categories[]"]:checked').map(function () {
                return this.value;
            }).get();
            var tags = jQuery('input[name="ecwd_tags[]"]:checked').map(function () {
                return this.value;
            }).get();
            var venues = jQuery('input[name="ecwd_venues[]"]:checked').map(function () {
                return this.value;
            }).get();
            var organizers = jQuery('input[name="ecwd_organizers[]"]:checked').map(function () {
                return this.value;
            }).get();
            var el = jQuery(this);
            if (!jQuery(this).is(':checkbox')) {
                e.preventDefault();
            }
            var navLink = jQuery(this);
            if ((!navLink.attr('href') || navLink.attr('href') == 'undefined') && !navLink.is(':checkbox') && navLink.attr('class') != 'ecwd_reset_filters') {
                navLink = jQuery(this).find('a');
            }
            var main_div = navLink.closest('.calendar_main');

            var calendar_ids_class_1 = jQuery(main_div).find('div.ecwd_calendar').find('div:first-child').data("id");
            var calendar_ids_class_2 = jQuery(main_div).find('div.ecwd_calendar').find('div:first-child').data("type");

            var display = jQuery(main_div).find('div.ecwd_calendar').attr('class').split(' ')[0].split('-')[2];
            var calendar_ids = calendar_ids_class_1;
            var query = jQuery(main_div).find('input.ecwd-search').val();
            var tag = jQuery('.ecwd_tags').val();
            var venue = jQuery('.ecwd_venues').val();
            var organizer = jQuery('.ecwd_organizers').val();
            var category = jQuery('.ecwd_categories').val();
            var displays = jQuery(main_div).find('.ecwd_displays').val();
            var date = jQuery(main_div).find('.ecwd_date').val();
            var page_items = jQuery(main_div).find('.ecwd_page_items').val();
            var event_search = jQuery(main_div).find('.event_search').val();
            var filters = jQuery(main_div).find('.ecwd_filters').val();
            jQuery(main_div).find('.ecwd_loader').show();
            jQuery.post(ecwd.ajaxurl, {
                action: 'ecwd_ajax',
                ecwd_calendar_ids: calendar_ids,
                ecwd_link: navLink.attr('href'),
                ecwd_type: calendar_ids_class_2,
                ecwd_query: query,
                ecwd_weekdays: days,
                ecwd_categories: cats,
                ecwd_tags: tags,
                ecwd_venues: venues,
                ecwd_organizers: organizers,
                ecwd_category: category,
                ecwd_tag: tag,
                ecwd_venue: venue,
                ecwd_organizer: organizer,
                ecwd_displays: displays,
                ecwd_prev_display: display,
                ecwd_page_items: page_items,
                ecwd_event_search: event_search,
                ecwd_filters: filters,
                ecwd_date: 1,
                ecwd_date_filter: date,
                ecwd_nonce: ecwd.ajaxnonce
            }, function (data) {
                $(main_div).find('div.ecwd_calendar').replaceWith(data);
                $(main_div).find('.ecwd_loader').hide();
                if ($('.ecwd_open_event_popup').length > 0) {
                    $('.ecwd_open_event_popup').css({
                        'cursor': 'pointer'
                    });
                }
            })
            e.stopPropagation();
        });


        function createSearchForm() {
            var scinpt = document.getElementById("ecwd-search-submit");
            if (scinpt !== null) {
                //scinpt.addEventListener('focus', doSearch, false);
            }
            jQuery('.ecwd-search').on("keyup", function (e) {
                if (e.keyCode == 13) {
                    doSearch(this);
                }
            });
            jQuery('.ecwd-search-submit').on("focus", function (e) {
                doSearch(this);
            });

            jQuery('.ecwd-tag-container .ecwd-dropdown-menu > div').click(function (e) {
                jQuery('.ecwd_tags').val(jQuery(this).attr("data-term-tag"));
                doSearch(this);
            });
            jQuery('.ecwd-category-container .ecwd-dropdown-menu > div').click(function (e) {
                jQuery('.ecwd_categories').val(jQuery(this).attr("data-term-category"));
                doSearch(this);
            });
            jQuery('.ecwd-venue-container .ecwd-dropdown-menu > div').click(function (e) {
                jQuery('.ecwd_venues').val(jQuery(this).attr("data-term-venue"));
                doSearch(this);
            });
            jQuery('.ecwd-organizer-container .ecwd-dropdown-menu > div').click(function (e) {
                jQuery('.ecwd_organizers').val(jQuery(this).attr("data-term-organizer"));
                doSearch(this);
            });
        }


        function doSearch(el) {

            var main_div = jQuery(el).closest('.calendar_main');
            var navLink = jQuery(main_div).find('.ecwd_current_link');
            var query = jQuery(main_div).find('input.ecwd-search').val();
            var tag = jQuery(main_div).find('.ecwd_tags').val();
            var venue = jQuery(main_div).find('.ecwd_venues').val();
            var organizer = jQuery(main_div).find('.ecwd_organizers').val();
            var category = jQuery(main_div).find('.ecwd_categories').val();
            var calendar_ids_class_1 = jQuery(main_div).find('div.ecwd_calendar').find('div:first-child').data("id");
            var calendar_ids_class_2 = jQuery(main_div).find('div.ecwd_calendar').find('div:first-child').data("type");
            var calendar_ids = calendar_ids_class_1;
            var displays = jQuery(main_div).find('.ecwd_displays').val();
            var page_items = jQuery(main_div).find('.ecwd_page_items').val();
            var event_search = jQuery(main_div).find('.event_search').val();
            var filters = jQuery(main_div).find('.ecwd_filters').val();
            jQuery(main_div).find('.ecwd_loader').show();
            jQuery.post(ecwd.ajaxurl, {
                action: 'ecwd_ajax',
                ecwd_query: query,
                ecwd_category: category,
                ecwd_tag: tag,
                ecwd_venue: venue,
                ecwd_organizer: organizer,
                ecwd_displays: displays,
                ecwd_filters: filters,
                ecwd_page_items: page_items,
                ecwd_link: navLink.val(),
                ecwd_calendar_ids: calendar_ids,
                ecwd_event_search: event_search,
                ecwd_date: 1,
                ecwd_type: calendar_ids_class_2,
                ecwd_calendar_search:1,//not filter
                ecwd_nonce: ecwd.ajaxnonce
            }, function (data) {
                $(main_div).find('div.ecwd_calendar').replaceWith(data);
            });
            jQuery('.ecwd-search-submit').blur();
        }

        function showMiniCalendarEventOnHover() {
        }
        ;

        var ulEvent, day;
        var ulEventFull, dayFull;
        jQuery('div.ecwd_calendar .has-events').on('click', function (e) {
            dayFull = jQuery(this).attr('data-date').split('-');
            dayFull = dayFull[2];
            ulEventFull = jQuery(this).find('ul.events');
            if (parseInt(jQuery(this).closest('.ecwd_calendar').width()) <= 300 || parseInt(jQuery(window).width()) <= 768 || jQuery(this).closest('.ecwd_calendar').hasClass('ecwd-widget-mini') || $(this).closest('.ecwd_calendar').hasClass('ecwd-page-mini')) {
                if (dayFull == jQuery(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').attr('data-dayNumber')
                        && jQuery(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').is(':empty') == false) {
                    jQuery(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').html('');
                } else {
                    showEvent(ulEventFull, this);
                }
                jQuery(this).closest('.ecwd_calendar').find('.ecwd-events-day-details').attr('data-dayNumber', dayFull);
            }
        });

        function showEvent(el, calendar) {
            if (el.parent().parent().parent().parent().attr('class').indexOf("full") != -1) {
                var obj = el;
                if (el.length > 1) {
                    el.each(function () {
                        if ($(this).hasClass('more_events')) {
                            obj = $(this);
                            return;
                        }
                    });
                }
                $(calendar).closest('.ecwd_calendar').find('.ecwd-events-day-details').html(obj.find('.event-details').clone().css('display', 'block'));                
            } else if (el.parent().parent().parent().parent().attr('class').indexOf("mini") != -1) {                
                $(calendar).closest('.ecwd_calendar').find('.ecwd-events-day-details').html(el.clone());
            }
            $(calendar).closest('.ecwd_calendar').find('.ecwd-events-day-details').find('li').each(function(){
                $(this).css('background','none');
            
            });
            add_single_events_popup();
        }

        function showFullCalendarEventOnHover() {
            if (parseInt(jQuery(window).width()) >= 768) {
                jQuery('div.ecwd-page-full .has-events ul.events:not(.more_events) > li:not(.ecwd-calendar-more-event)').on('mouseover', function (e) {
                    jQuery(this).find('ul.events').show();
                    var show_event_hover_info = jQuery(".show_event_hover_info");
                    if(show_event_hover_info.length>0){
                        jQuery(this).find('div.event-details-container').show();
                    }
                });

                jQuery('div.ecwd-page-full .has-events ul.events:not(.more_events) > li:not(.ecwd-calendar-more-event)').on('mouseout', function (e) {
                    jQuery(this).find('div.event-details-container').css('display', 'none');
                });
            }

            jQuery('div.ecwd-page-full .has-events ul.more_events > li').on('click', function (e) {
                jQuery('div.ecwd-page-full .has-events ul.more_events > li').find('.event-details-container').slideUp();
                if (jQuery(this).find('.event-details-container').is(":visible"))
                    jQuery(this).find('.event-details-container').slideUp();
                else
                    jQuery(this).find('.event-details-container').slideDown();
            });
        }



        jQuery('.ecwd-show-map-span').click(function () {
            jQuery('.ecwd-show-map').show();
        });


        function doMasonry() {
            var $container = jQuery('.ecwd-poster-board');
            if ($container.length && jQuery('.ecwd-poster-board').find('.ecwd-poster-item').length > 0) {
                $container.imagesLoaded(function () {
                    $container.masonry({
                        itemSelector: '.ecwd-poster-item'
                    });
                });
            }

        }

        function showFilterSliderArrow() {
            var li_position, li_width, last_child;
            $(".calendar_main:not([class^='ecwd_widget'] .calendar_main) .ecwd_calendar_view_tabs").each(function (key, element) {
                var cwidth = $(element).closest('.ecwd_calendar').outerWidth();
                if (cwidth == 0)
                    cwidth = 600;
                if ($(this).find('.ecwd-search').length != 0)
                    var ecwd_calendar_view_tabs_width = parseInt(cwidth) - 50;
                else
                    var ecwd_calendar_view_tabs_width = parseInt(cwidth);
                if (parseInt(jQuery('body').width()) <= 768 || $(".calendar_full_content .ecwd_calendar").width() < 600) {
                    var ecwd_calendar_view_visible_count = parseInt(ecwd_calendar_view_tabs_width / 110);
                } else if (parseInt(jQuery('body').width()) <= 500 || $(".calendar_full_content .ecwd_calendar").width() < 400) {
                    var ecwd_calendar_view_visible_count = parseInt(ecwd_calendar_view_tabs_width / 90);
                } else {
                    var ecwd_calendar_view_visible_count = parseInt(ecwd_calendar_view_tabs_width / 150);
                }
                $(element).find('.filter-container').width(ecwd_calendar_view_tabs_width);
                $(element).find('ul li').each(function (keyli, elementli) {
                    if ($(elementli).hasClass('ecwd-selected-mode')) {
                        li_position = keyli;
                        li_width = $(elementli).outerWidth();
                    }
                });
                if ($(element).find(".filter-arrow-right").css("display") == "block" || last_child == 1)
                    $(element).find('.filter-container ul li').width((ecwd_calendar_view_tabs_width - 30) / ecwd_calendar_view_visible_count);
                else
                    $(element).find('.filter-container ul li').width((ecwd_calendar_view_tabs_width) / ecwd_calendar_view_visible_count);
                var ecwd_view_item_width = $(element).find('.filter-container ul li').eq(0).innerWidth() - 1;
                if (!(ecwd_calendar_view_tabs_width < ecwd_view_item_width * parseInt($(element).find('.filter-container ul li').length) && !($(element).find("ul li:last-child").hasClass("ecwd-selected-mode"))))
                    $(element).find('.filter-arrow-right').hide();
                if (ecwd_calendar_view_tabs_width < ecwd_view_item_width * parseInt($(element).find('.filter-container ul li').length) && !($(element).find("ul li:last-child").hasClass("ecwd-selected-mode"))) {
                    $(element).find('.filter-arrow-right').show();
                } else if ($(element).find("ul li:last-child").hasClass("ecwd-selected-mode")) {
                    last_child = 1;
                }
                if (ecwd_calendar_view_visible_count <= li_position && li_position != 0) {
                    $(element).find('ul li').css({left: "-" + ((li_position + 1 - ecwd_calendar_view_visible_count) * ecwd_view_item_width) + "px"});
                    $(element).find('.filter-arrow-left').show();
                } else
                    $(element).find('ul li').css({left: "0px"});
            });

            $('.ecwd_calendar_view_tabs .filter-arrow-right').click(function () {
                var view_filter_width = $(this).parent().find('ul li').eq(0).outerWidth();
                var cwidth = $(this).closest('.ecwd_calendar').outerWidth();
                if (cwidth == 0)
                    cwidth = 600;
                if ($(this).find('.ecwd-search').length != 0)
                    var view_filter_container_width = parseInt(cwidth) - 50;
                else
                    var view_filter_container_width = parseInt(cwidth);
                var view_filter_count = parseInt($(this).parent().find('ul li').length);
                $(this).parent().find('.filter-arrow-left').show();
                if (parseInt($(this).parent().find('ul li').css('left')) <= -((view_filter_width * view_filter_count) - view_filter_container_width) + view_filter_width)
                    $(this).hide();
                if (click && view_filter_container_width < view_filter_width * view_filter_count && parseInt($(this).parent().find('ul li').css('left')) >= -(view_filter_width * (view_filter_count) - view_filter_container_width)) {
                    click = 0;
                    $(this).parent().find('ul li').animate({left: "-=" + view_filter_width}, 400, function () {
                        click = 1
                    });
                }
            });
            $('.ecwd_calendar_view_tabs .filter-arrow-left').click(function () {
                var view_filter_width = $(this).parent().find('ul li').eq(0).outerWidth();
                if ($(this).parent().find('.filter-arrow-right').css('display') == 'none')
                    $(this).parent().find('.filter-arrow-right').show();
                if (parseInt($(this).parent().find('ul li').css('left')) == -view_filter_width)
                    $(this).hide();
                if (click && parseInt($(this).parent().find('ul li').css('left')) < 0) {
                    click = 0;
                    $(this).parent().find('ul li').animate({left: "+=" + view_filter_width}, 400, function () {
                        click = 1
                    });
                }
            });
        }

        function upcomingEventsSlider() {
            var current_date = Date.parse(Date());

            var upcoming_events_slider_main = jQuery('.upcoming_events_slider').width();
            jQuery('.upcoming_events_slider .upcoming_events_item').width(upcoming_events_slider_main);


            jQuery('.upcoming_events_slider .upcoming_event_container').width(parseInt(upcoming_events_slider_main) - 80);
            jQuery('.upcoming_events_slider > ul').width(upcoming_events_slider_main * jQuery('.upcoming_events_slider .upcoming_events_item').length);

            if (jQuery(".upcoming_events_slider").width() < jQuery('.upcoming_events_slider > ul').width()) {
                jQuery('.upcoming_events_slider .upcoming_events_slider-arrow-right').show();
            }


            var min = 0;
            jQuery('.upcoming_events_slider .upcoming_events_item').each(function () {
                var item_date = Date.parse(jQuery(this).data('date'));
                if (item_date < current_date) {
                    min++;
                } else {
                    return false;
                }
            });

            if (min && min == jQuery('.upcoming_events_slider .upcoming_events_item').length) {
                min--;
            }

            jQuery('.upcoming_events_slider .upcoming_events_item').css('left', -upcoming_events_slider_main * min);

            if (parseInt(jQuery('.upcoming_events_slider .upcoming_events_item').css('left')) < 0) {
                jQuery('.upcoming_events_slider').parent().find('.upcoming_events_slider-arrow-left').show();
            }
            if (parseInt(jQuery('.upcoming_events_slider .upcoming_events_item').css('left')) == -(jQuery('.upcoming_events_slider > ul').width() - upcoming_events_slider_main)) {
                jQuery('.upcoming_events_slider').parent().find('.upcoming_events_slider-arrow-right').hide();
            }

            jQuery('.upcoming_events_slider .upcoming_events_slider-arrow-right').click(function () {
                var events_item_width = jQuery(this).parent().find('ul li').eq(0).width();

                var events_item_count = parseInt(jQuery(this).parent().find('ul li').length);
                if (jQuery(this).parent().find('.upcoming_events_slider-arrow-left').css('display') == 'none')
                    jQuery(this).parent().find('.upcoming_events_slider-arrow-left').show();
                if (click && upcoming_events_slider_main < events_item_width * events_item_count && parseInt(jQuery(this).parent().find('ul li').css('left')) >= -(events_item_width * (events_item_count) - upcoming_events_slider_main)) {
                    click = 0;
                    jQuery(this).parent().find('ul li').animate({left: "-=" + events_item_width}, 400, function () {
                        click = 1
                    });
                }
                if (parseInt(jQuery(this).parent().find('ul li').css('left')) <= -(jQuery('.upcoming_events_slider > ul').width() - (2 * events_item_width)))
                    jQuery(this).hide();
            });
            jQuery('.upcoming_events_slider .upcoming_events_slider-arrow-left').click(function () {
                var events_item_width = jQuery(this).parent().find('ul li').eq(0).width();
                if (jQuery(this).parent().find('.upcoming_events_slider-arrow-right').css('display') == 'none')
                    jQuery(this).parent().find('.upcoming_events_slider-arrow-right').show();
                if (parseInt(jQuery(this).parent().find('ul li').css('left')) == -events_item_width)
                    jQuery(this).hide();
                if (click && parseInt(jQuery(this).parent().find('ul li').css('left')) < 0) {
                    click = 0;
                    jQuery(this).parent().find('ul li').animate({left: "+=" + events_item_width}, 400, function () {
                        click = 1
                    });
                }

            });
        }
        function showWidgetEventDesc() {
            jQuery('.ecwd-widget-mini .event-container, .ecwd-widget-mini .ecwd_list .event-main-content').each(function () {
                if (jQuery(this).find('.arrow-down').length == 0) {
                    jQuery(this).find('.ecwd-list-date-cont').append("<span class='arrow-down'>&nbsp</span>");
                    jQuery(this).find('.ecwd-list-date-cont').after("<div class='event_dropdown_cont'></div>");
                    jQuery(this).find('.event_dropdown_cont').append(jQuery(this).children(".event-venue,.event-content, .event-organizers"));

                    jQuery(this).find('.arrow-down').click(function () {
                        if (jQuery(this).hasClass('open')) {
                            jQuery(this).parent().parent().find('.event_dropdown_cont').slideUp(400);
                            jQuery(this).removeClass('open');
                        } else {
                            jQuery(this).parent().parent().find('.event_dropdown_cont').slideDown(400);
                            jQuery(this).addClass('open');
                        }
                    });
                }
            })
        }

        function calendarFullResponsive() {
            if (jQuery(window).width() <= 500) {
                jQuery('div[class^="ecwd-page"] .event-container, div[class^="ecwd-page"] .ecwd_list .event-main-content').each(function () {
                    if (jQuery(this).find('.arrow-down').length == 0) {
                        var content = jQuery(this).find('.event-content').html();
                        if (jQuery(this).hasClass("event-container")) {
                            jQuery(this).find('.event-content').html(jQuery(this).find('.ecwd-list-img').html() + content);
                            jQuery(this).find('.ecwd-list-img').remove();
                        } else {
                            var content = jQuery(this).find('.event-content').html();
                            jQuery(this).find('.event-content').html(jQuery(this).prev().html() + content);
                            jQuery(this).prev().remove();

                        }
                        jQuery(this).find('.ecwd-list-date-cont').append("<span class='arrow-down'>&nbsp</span>");
                        jQuery(this).find('.ecwd-list-date-cont').after("<div class='event_dropdown_cont'></div>");
                        jQuery(this).find('.event_dropdown_cont').append(jQuery(this).children(".event-venue,.event-content, .event-organizers"));
                        jQuery(this).find('.arrow-down').each(function () {
                            jQuery(this).click(function () {
                                if (jQuery(this).hasClass('open')) {
                                    jQuery(this).parent().parent().find('.event_dropdown_cont').slideUp(400);
                                    jQuery(this).removeClass('open');
                                } else {
                                    jQuery(this).parent().parent().find('.event_dropdown_cont').slideDown(400);
                                    jQuery(this).addClass('open');
                                }
                            });
                        })
                    }
                })
            } else if (jQuery(window).width() > 500) {
                jQuery('div[class^="ecwd-page"] .event-container, div[class^="ecwd-page"] .ecwd_list .event-main-content').each(function () {
                    if (jQuery(this).find('.arrow-down').length != 0) {
                        //  jQuery(this).css('height','auto');
                        if (jQuery(this).hasClass("event-container")) {
                            jQuery(this).find('.event-title').before('<div class="ecwd-list-img"><div class="ecwd-list-img-container">' + jQuery(this).find('.ecwd-list-img-container').html() + '</div></div>');
                            jQuery(this).find('.event-content .ecwd-list-img-container').remove();
                            jQuery(this).find('.ecwd-list-date-cont').after(jQuery(this).find('.event_dropdown_cont').html());
                            jQuery(this).find('.event_dropdown_cont').remove();
                        } else {
                            jQuery(this).parent().find('.ecwd-list-date.resp').after('<div class="ecwd-list-img"><div class="ecwd-list-img-container">' + jQuery(this).find('.ecwd-list-img-container').html() + '</div></div>');
                            jQuery(this).find('.event-content .ecwd-list-img-container').remove();
                            jQuery(this).find('.ecwd-list-date-cont').after(jQuery(this).find('.event_dropdown_cont').html());
                            jQuery(this).find('.event_dropdown_cont').remove();
                        }

                        jQuery(this).find('.arrow-down').remove();
                    }
                })

            }

        }
        function add_single_events_popup() {
            if(jQuery('.ecwd_open_event_popup').length == 0){
                return;
            }
            jQuery('.single_event_popup').ecwd_popup({
                button: jQuery('.ecwd_open_event_popup'),
                body_class: "ecwd-excluded-events ecwd_popup_body_scroll",
                title: ecwd.event_popup_title_text,
                get_ajax_data: function (el) {
                    var start_date = el.attr('start-date-data');
                    var end_date = el.attr('end-date-data');
                    if (start_date && end_date) {
                        var data = {
                            action: 'ecwd_event_popup_ajax',
                            id: el.attr('class').split('event')[2],
                            start_date: start_date,
                            end_date: end_date
                        };
                        return data;
                    } else {
                        return {};
                    }
                }
            });
        }

        setTimeout(function () {
            if (parseInt(jQuery('body').width()) <= 768 || jQuery(".calendar_full_content").width() <= 550) {
                jQuery('.calendar_main').each(function (k, v) {
                    jQuery(this).find('.ecwd_show_filters').click(function () {
                        if (jQuery(this).find('span').hasClass('open')) {
                            jQuery(this).find('span').html(jQuery('.ecwd_show_filters_text').val());
                            jQuery(this).next().hide();
                            jQuery(this).find('span').removeClass('open');
                        } else {
                            jQuery(this).find('span').html(jQuery('.ecwd_hide_filters_text').val());
                            jQuery(this).next().show();
                            jQuery(this).find('span').addClass('open');
                        }
                    });
                });
            } else {
                jQuery('.calendar_main').each(function () {
                    jQuery(this).find('.ecwd_show_filters span').click(function () {
                        if (jQuery(this).hasClass('open')) {
                            jQuery(this).html(jQuery('.ecwd_show_filters_text').val());
                            jQuery(this).closest(".calendar_full_content").find(".ecwd_calendar").css({
                                "max-width": "100%",
                                "width": "100%"
                            });
                            jQuery(this).parent().next().hide();
                            jQuery(this).removeClass('open');
                            showFilterSliderArrow();
                            if (jQuery('.ecwd-poster-board').length > 0) {
                                doMasonry();
                            }
                        } else {
                            jQuery(this).html(jQuery('.ecwd_hide_filters_text').val());
                            jQuery(this).closest(".calendar_full_content").find(".ecwd_filters").css({
                                "max-width": "27%",
                                "width": "27%",
                                "float": "left"
                            });
                            jQuery(this).closest(".calendar_full_content").find(".ecwd_calendar").css({
                                "max-width": "71%",
                                "float": "left"
                            });
                            jQuery(this).parent().next().show();
                            jQuery(this).addClass('open');
                            showFilterSliderArrow();
                            if (jQuery('.ecwd-poster-board').length > 0) {
                                doMasonry();
                            }
                        }
                    });
                });
                jQuery(".month-name").show();
            }
            jQuery('.ecwd_filter_item').each(function () {
                jQuery(this).find('.ecwd_filter_heading').click(function () {
                    if (jQuery(this).hasClass('open')) {
                        jQuery(this).next().slideUp(400);
                        jQuery(this).removeClass('open');
                    } else {
                        jQuery(this).next().slideDown(400);
                        jQuery(this).addClass('open');
                    }
                });
            });
        }, 100);

        function show_filters(main_div) {

            if (parseInt(jQuery('body').width()) <= 768 || jQuery(".calendar_full_content").width() <= 550) {
                jQuery(".calendar_full_content .ecwd_calendar").css("max-width", "100%");
                jQuery(".calendar_full_content .ecwd_filters, .calendar_full_content .ecwd_calendar").css({
                    "max-width": "100%",
                    "width": "100%",
                    "float": "none"
                });
                jQuery(".ecwd_show_filters").removeClass('ecwd_show_filters_left').addClass('ecwd_show_filters_top');
                if (!main_div) {
                    jQuery(".ecwd_show_filters span").html(jQuery('.ecwd_show_filters_text').val());
                    jQuery(".ecwd_show_filters span").removeClass("open");
                    jQuery(".ecwd_filters").hide();
                }
            } else {

                if (!main_div) {
                    jQuery(".ecwd_show_filters").removeClass('ecwd_show_filters_top').addClass('ecwd_show_filters_left');
                } else {
                    if (main_div.find(".ecwd_calendar").hasClass('ecwd-widget-mini') === false) {
                        if (main_div.find(".ecwd_show_filters span").hasClass('open')) {
                            main_div.find(".ecwd_calendar").css({"max-width": "71%", "float": "left"});
                            main_div.find(".ecwd_filters").css({"max-width": "27%", "width": "27%", "float": "left"});
                        } else {

                            main_div.find(".ecwd_filters").css({"max-width": "100%", "width": "100%", "float": "none"});

                            main_div.find(".ecwd_calendar").css({"max-width": "100%", "width": "100%", "float": "none"});
                        }
                    }
                }
            }

        }        
        this.showMap = function () {
            if (ecwd.gmap_key == "") {
                jQuery(".ecwd_map_div").each(function (k, v) {
                    var $this = jQuery(this);
                    if ($this.closest('.ecwd-show-map').length == 1) {
                        $this.closest('.ecwd-show-map').hide();
                    }
                    $this.hide();
                });
                return;
            }

            if (typeof google == 'undefined' || typeof google.maps == "undefined") {
                var script = document.createElement('script');
                script.type = 'text/javascript';                
                script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&callback=ecwd_js_init_call.showMap&key='+ecwd.gmap_key;
                document.body.appendChild(script);
            } else {
                var maps = [];
                jQuery(".ecwd_map_div").each(function (k, v) {
                    maps[k] = this;
                    var locations = JSON.parse(jQuery(maps[k]).next('textarea').val());
                    var locations_len = Object.keys(locations).length;

                    jQuery(maps[k]).gmap3();

                    var markers = [];
                    var zoom = 17;
                    if (locations_len > 0 && typeof locations[0] != 'undefined' && typeof locations[0]['zoom'] != 'undefined') {
                        zoom = parseInt(locations[0]['zoom']);
                    } else {
                        zoom = 2;
                    }

                    var center = {lat: 0, lng: 0}

                    for (var i = 0; i < locations_len; i++) {
                        if (locations[i]) {

                            var marker = new Object();
                            marker.lat = locations[i].latlong[0];
                            marker.lng = locations[i].latlong[1];
                            marker.data = locations[i].infow;
                            marker.options = new Object();
                            markers.push(marker);
                            center.lat += parseFloat(locations[i].latlong[0]);
                            center.lng += parseFloat(locations[i].latlong[1]);
                        }

                    }
                    if(locations_len > 0) {
                        center.lat = center.lat / locations_len;
                        center.lng = center.lng / locations_len;
                    }

                    jQuery(maps[k]).gmap3({
                        map: {
                            options: {
                                zoom: zoom,
                                zoomControl: true,
                                styles: (ecwd.gmap_style !== "") ? JSON.parse(ecwd.gmap_style) : null,
                                center: center
                            },
                            center: center
                        },
                        marker: {
                            values: markers,
                            options: {
                                draggable: false
                            },
                            events: {
                                click: function (marker, event, context) {
                                    var map = jQuery(maps[k]).gmap3("get"),
                                            infowindow = jQuery(maps[k]).gmap3({get: {name: "infowindow"}});
                                    if (infowindow) {
                                        infowindow.open(map, marker);
                                        infowindow.setContent(context.data);
                                    } else {
                                        jQuery(maps[k]).gmap3({
                                            infowindow: {
                                                anchor: marker,
                                                options: {content: context.data}
                                            }
                                        });
                                    }
                                }

                            }
                        },
                        //autofit: {maxZoom: zoom}
                    });

                });
            }
        }
        // $('#ecwd_back_link').on('click', function (e) {
        //     e.preventDefault();
        //     window.history.back();
        // });

        if (jQuery('.ecwd_map_div').length > 0) {
            this.showMap();
        }
    }

    ecwd_js_init_call = new ecwd_js_init();
}(jQuery));
function ecwd_eventsOff() {
    jQuery(".calendar_main,.ecwd-event,.ecwd-organizer,.ecwd-venue").find("*").off();
    jQuery(".calendar_main,.ecwd-event,.ecwd-organizer,.ecwd-venue").children().off();
}

jQuery(window).resize(function () {
    var window_width = jQuery(window).width();
    if (typeof checkw == 'undefined')
        checkw = window_width;
    if (window_width != checkw) {
        ecwd_js_init_call = new ecwd_js_init();
        checkw = window_width;
    }
});