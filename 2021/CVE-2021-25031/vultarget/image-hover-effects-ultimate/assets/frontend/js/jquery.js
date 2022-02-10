jQuery.noConflict();
(function ($) {
    'use strict';

    var InitWaypointAnimations = (function () {
        function onScrollInitAnimation(items, container, options) {
            const containerOffset = (container) ? container.attr("sa-data-animation-offset") || options.offset : null;
            items.each(function () {
                const element = $(this),
                        animationClass = element.attr("sa-data-animation"),
                        animationDelay = element.attr("sa-data-animation-delay") || options.delay,
                        animationDuration = element.attr("sa-data-animation-duration") || options.delay,
                        animationOffset = element.attr("sa-data-animation-offset") || options.offset;

                element.css({
                    "-webkit-animation-delay": animationDelay,
                    "-moz-animation-delay": animationDelay,
                    "animation-delay": animationDelay,
                    "animation-duration": animationDuration
                });

                const trigger = (container) ? container : element;

                trigger.waypoint(function () {
                    element
                            .addClass("animated")
                            .addClass(animationClass);

                }, {
                    triggerOnce: true,
                    offset: containerOffset || animationOffset
                });
            });
        }

        function InitWaypointAnimations(defaults) {
            if (!defaults) {
                defaults = {};
            }
            const options = {
                offset: defaults.offset || "90%",
                delay: defaults.delay || "0ms",
                animateClass: defaults.animateClass || "sa-data-animation",
                animateGroupClass: defaults.animateGroupClass || "sa-data-animation-group"
            }

            const animateGroupClassSelector = classToSelector(options.animateGroupClass);
            const animateClassSelector = classToSelector(options.animateClass);

            // Attach waypoint animations to grouped animate elements
            $(animateGroupClassSelector).each((index, group) => {
                const container = $(group);
                const items = $(group).find(animateClassSelector);
                onScrollInitAnimation(items, container, options);
            });

            // Attach waypoint animations to ungrouped animate elements
            $(animateClassSelector)
                    .filter((index, element) => {
                        return $(element).parents(animateGroupClassSelector).length === 0;
                    })
                    .each((index, element) => {
                        onScrollInitAnimation($(element), null, options);
                    });
        }

        function classToSelector(className) {
            return "." + className;
        }

        return InitWaypointAnimations;
    }());

    $(document).ready(function () {
        InitWaypointAnimations();
    });

    $("[sa-data-animation]").each(function (index, value) {
        if ($(this).attr('sa-data-animation') !== '') {
            $(this).addClass('sa-data-animation');
        }
    });

})(jQuery);

