(function ($) {
    $.fn.ecwd_popup = function (opt) {
        //default params 
        var default_options = {
            button: '',
            title: 'Event Details',
            body_id: '',
            body_class: '',
            container_class: '',
            fillhtml: false,
            only_open: false,
            get_ajax_data: function (el) {
            },
            popup_close: function () {
            },
            after_popup_show: function (el) {
            },
            get_el_class: function (el) {
            }
        };

        for (var key in default_options) {
            if (opt[key] == undefined) {
                opt[key] = default_options[key];
            }
        }
        var params = opt;
        var el = $(this);

        el.hide();
        params.button.on('click', function (e) {
            e.preventDefault();
                         
                var data = params.get_ajax_data($(this));
                if (data) {
                    if (params.fillhtml) {
                        data.html = params.fillhtml.val();
                    }
                    jQuery.post(ecwd.ajaxurl, data, function (response) {
                        if (response != 0) {
                            open_popup(response);
                            show_gmap();
                        }
                    });
                } else if (params.fillhtml.length > 0 && params.fillhtml) {
                    open_popup(params.fillhtml.val());
                } else {
                    open_popup(el.html());
                }
                el.on('click', '.ecwd_close_popup', function () {
                    el.hide();
                    jQuery('body').removeClass("body-ecwd_open_popup");
                    params.popup_close(el);
                });
                $(document).keyup(function (e) {
                    if (e.keyCode == 27) { // escape key maps to keycode `27`
                        el.hide();
                        jQuery('body').removeClass("body-ecwd_open_popup");
                        params.popup_close(el);
                    }
                });
            
        });

        function open_popup(html) {
            if (params.only_open == false) {
                add_popup(html);
            }
            jQuery('body').addClass("body-ecwd_open_popup");
            el.show();
            params.after_popup_show(el);
            el.addClass('ecwd_popup_el');
        }

        function add_popup(html) {

            var head = get_popup_head();
            var body = get_popup_body(html);

            var popup_html =
                    "<div class='ecwd_popup_back ecwd_open_popup'>" +
                    "<div class='ecwd_popup_container " + params.container_class + "'>" +
                    head + body +
                    "</div>" +
                    "</div>";
            el.html(popup_html);
        }

        function get_popup_head() {
            var popup_head =
                    "<div class='ecwd_popup_head'>" +
                    "<div class='ecwd_popup_title'><h4>" + params.title + "</h4></div>" +
                    "<div class='ecwd_close_popup'><i class='fa fa-times'></i></div>" +
                    "</div>";
            return popup_head;
        }

        function get_popup_body(html) {
            var body_id = (params.body_id != '') ? "id='" + params.body_id + "'" : "";
            var popup_body = "<div " + body_id + " class='ecwd_popup_body " + params.body_class + "'>" + html + "</div>";
            return popup_body;
        }


        function show_gmap() {
            var interval = setInterval(function () {
                var el = $('.ecwd_popup_container').find('.ecwd-show-map .ecwd_markers');
                if (el.html() != "") {
                    ecwd_js_init_call.showMap();
                    clearInterval(interval);
                }
            }, 100);
        }

        $.fn.ecwd_popup.params = default_options;
    };
}(jQuery));

