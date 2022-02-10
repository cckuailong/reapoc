(function ($) {
    'use strict';

    var timer;

    function trackLeave(ev) {
        if (ev.clientY > 0) {
            return;
        }

        if (timer) {
            clearTimeout(timer);
        }

        if ($.exitIntent.settings.sensitivity <= 0) {
            $.event.trigger('exitintent');
            return;
        }

        timer = setTimeout(
            function() {
                timer = null;
                $.event.trigger('exitintent');
            }, $.exitIntent.settings.sensitivity);
    }

    function trackEnter() {
        if (timer) {
            clearTimeout(timer);
            timer = null;
        }
    }

    $.exitIntent = function(enable, options) {
        $.exitIntent.settings = $.extend($.exitIntent.settings, options);

        if (enable == 'enable') {
            $(window).mouseleave(trackLeave);
            $(window).mouseenter(trackEnter);
        } else if (enable == 'disable') {
            trackEnter(); // Turn off any outstanding timer
            $(window).unbind('mouseleave', trackLeave);
            $(window).unbind('mouseenter', trackEnter);
        } else {
            throw "Invalid parameter to jQuery.exitIntent -- should be 'enable'/'disable'";
        }
    }

    $.exitIntent.settings = {
        'sensitivity': 300
    };

})(jQuery);
