/*
 * Multiple Before and After Comparer v.1
 *
 * Authored by Jan Duldulao
 *
 *
 * Copyright 2017, Jan Duldulao
 * License: GNU General Public License, version 3 (GPL-3.0)
 * http://www.opensource.org/licenses/gpl-3.0.html
 *
 */

;
(function ($) {
    "use strict";

    $.fn.mbac = function (o) {

        // comparer default settings
        var _width = $(this).width();

        var defaults = {

            width: _width,
            height: "auto",

            animduration: 450, // length of transition
            animspeed: 4000, // delay between transitions
            target: "ul.mbac",
            z_index: 500,
            responsive: false
        };

        // create settings from defauls and user options
        var settings = $.extend({}, defaults, o);

        this.each(function () {
            var $wrapper = $(this),
                $slider = $wrapper.find(settings.target),
                $slides = $slider.children('li'),
                // marker elements
                $m_wrapper = null,
                $m_markers = null;

            var state = {
                slidecount: $slides.length, // total number of slides
                animating: false,
                paused: false,
                currentindex: 0
            };



            var responsive = {
                width: $wrapper.width(),
                height: null,
                ratio: null
            };

            // run through options and initialise settings
            var init = function () {

                // differentiate slider li from content li
                $slides.addClass('mbac-slide');


                // conf dimensions, responsive or static
                if (settings.responsive) {
                    confResponsive();
                } else {
                    confStatic();
                }

                // slide components are hidden by default, show them now
                $slider.show();



            }; // init

            var calcSlidesWidth = function (wx) {
                var z_index = settings.z_index;
                var start_width = wx / 2 / (state.slidecount - 1);
                var x = start_width;
                var increment = start_width;

                for (var i = 0; i < state.slidecount; i++) {
                    if (i + 1 < state.slidecount) {
                        $($slides[i])
                                .css({
                                    "width": x,
                                    "z-index": z_index -= 5,
                                    position: "absolute"
                                })
                                .data("wz", {w: x, z: z_index -= 5});

                    } else {

                        $($slides[i]).css({
                            "width": "100%",
                            "z-index": z_index -= 5
                        })
                        .data("wz", {w: "100%", z: z_index -= 5});
                    }



                    x = x + increment;

                }
            };

            var conf_action = function () {

                $slides.on('mouseover', function (e) {

                    e.preventDefault();
                    if (!state.animating) {
                        open($(this).index());
                    }

                });

                $slides.on('mouseleave', function (e) {

                    e.preventDefault();
                    if (!state.animating) {
                        close($(this).index());
                    }

                });
            };

            var confStatic = function () {

                calcSlidesWidth(settings.width);
                var h_arr = [];
                var h = "100%";
                for(var i = $slides.length-1;i>=0;i--) {
                    if(i == $slides.length-1) {
                        $($slides[i]).css("position","relative");
                    }
                    $($slides[i]).children().width(settings.width);
                }


                $wrapper.css({
                    'height': settings.height,
                    'width': settings.width,
                    'position': 'relative',
                    'margin': "auto",
                    'overflow': 'hidden'
                });

                conf_action();

            };

            var confResponsive = function () {
                $slider.addClass("responsive");

                var h_arr = [], w_arr = [];
                $slides.children('img').each(function(){
                   h_arr.push($(this).height());
                   w_arr.push($(this).width());
                });

                var h_final = Math.min.apply(Math,h_arr); // get the min height
                var w_final = Math.max.apply(Math,w_arr); // get the min width


                calcSlidesWidth(w_final);


                $wrapper.css({
                    'height': h_final,
                    'width': "100%",
                    'position': 'relative',
                    'margin': "auto"
                });

                conf_action();

            };

            var open = function (index) {
                state.currentindex = index;
                var wz = $($slides[state.currentindex]).data("wz");
                $($slides[state.currentindex])
                        .css({
                            "width"     : "100%"
                        })
                        .prevAll()
                        .css({
                            "width": 0
                        });
            };

            var close = function (index) {
                var wz = $($slides[state.currentindex]).data("wz");
                var i = index;
                $($slides[state.currentindex])
                        .css({
                            "width": wz.w,
                            "z-index": wz.z
                        })
                        .prevAll().each(function(){
                            i--;
                            var prev_wz = $($slides[i]).data("wz");
                            $(this).css({
                                "width": prev_wz.w,
                                "z-index": prev_wz.z
                            });

                        });

            };

            // lets get the party started :)
            init();
        });

    };

})(jQuery);
