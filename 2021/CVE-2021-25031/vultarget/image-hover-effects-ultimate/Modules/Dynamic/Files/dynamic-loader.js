jQuery.noConflict();
(function ($) {
    $(".oxi-image-hover-dynamic-load-infinite").each(function () {
        var $WRAPPER = $(this);
        var $WRAPPERID = $(this).attr('id');

        $(window).scroll(function () {
            if ($("#" + $WRAPPERID).length == 0) {
                return;
            }



            if ($(window).scrollTop() >= ($('#' + $WRAPPERID).offset().top - $(window).height() + 100)) {
                if (!($WRAPPER.hasClass("post-loading"))) {
                    $WRAPPER.addClass("post-loading");
                    var $CLASS = $WRAPPER.data('class'),
                            $function = $WRAPPER.data('function'),
                            $args = $WRAPPER.data('args'),
                            $settings = $WRAPPER.data('settings'),
                            $page = parseInt($WRAPPER.data("page")) + 1;


                    $.ajax({
                        url: oxi_image_dynamic_loader.ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'image_hover_ultimate',
                            _wpnonce: oxi_image_dynamic_loader.nonce,
                            class: $CLASS,
                            functionname: $function,
                            rawdata: JSON.stringify($settings),
                            args: $args,
                            optional: $page
                        }
                    }).done(function (response) {
                        var word = 'Image Hover Empty Data';
                        var regex = new RegExp('\\b' + word + '\\b');
                        var button = regex.test(response);

                        if (button) {
                            response = response.replace(regex, '');
                            $WRAPPER.data("page", $page);
                            $(response).insertBefore($WRAPPER);
                            $WRAPPER.remove();
                        } else {
                            $WRAPPER.data("page", $page);
                            $(response).insertBefore($WRAPPER);
                            $WRAPPER.removeClass("post-loading");
                        }
                    });
                }
            }
        });
    });



    $(document).on("click", ".oxi-image-load-more-button", function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        var $WRAPPER = $(this);
        $WRAPPER.addClass("button--loading");
        var $CLASS = $WRAPPER.data('class'),
                $function = $WRAPPER.data('function'),
                $args = $WRAPPER.data('args'),
                $settings = $WRAPPER.data('settings'),
                $page = parseInt($WRAPPER.data("page")) + 1;
        $.ajax({
            url: oxi_image_dynamic_loader.ajaxurl,
            method: 'POST',
            data: {
                action: 'image_hover_ultimate',
                _wpnonce: oxi_image_dynamic_loader.nonce,
                class: $CLASS,
                functionname: $function,
                rawdata: JSON.stringify($settings),
                args: $args,
                optional: $page
            }
        }).done(function (response) {
            var word = 'Image Hover Empty Data';
            var regex = new RegExp('\\b' + word + '\\b');
            var button = regex.test(response);
            if (button) {
                response = response.replace(regex, '');
                $WRAPPER.data("page", $page);
                $(response).insertBefore($WRAPPER.parent());
                $WRAPPER.parent().remove();
            } else {
                $WRAPPER.data("page", $page);
                $(response).insertBefore($WRAPPER.parent());
                $WRAPPER.removeClass("button--loading");
            }
        });

    });
})(jQuery)