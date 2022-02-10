!function (t) {
    var e = {};

    function i(n) {
        if (e[n]) return e[n].exports;
        var o = e[n] = {i: n, l: !1, exports: {}};
        return t[n].call(o.exports, o, o.exports, i), o.l = !0, o.exports
    }

    i.m = t, i.c = e, i.d = function (t, e, n) {
        i.o(t, e) || Object.defineProperty(t, e, {configurable: !1, enumerable: !0, get: n})
    }, i.n = function (t) {
        var e = t && t.__esModule ? function () {
            return t.default
        } : function () {
            return t
        };
        return i.d(e, "a", e), e
    }, i.o = function (t, e) {
        return Object.prototype.hasOwnProperty.call(t, e)
    }, i.p = "/", i(i.s = 10)
}({
    10: function (t, e, i) {
        i(11), t.exports = i(12)
    }, 11: function (t, e) {
        !function (t) {
            var animationTimer;
            var isWidgetEnabled = 0;
            var e;
            var animationClass = ".i-trigger .chaty-widget-i .svg, .i-trigger .chaty-widget-i .widget-img, .i-trigger .chaty-widget-i .facustom-icon";

            var isChatyInMobile = false; //initiate as false
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
                || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
                isChatyInMobile = true;
            }


            function i(t) {
                if(get_cta_status() && chaty_settings.object_settings.click_setting == 'click') {
                    return "none";
                } else {
                    return "";
                }
            }

            var n = new Date;

            function o() {
                if(jQuery(".chaty-widget").hasClass("one_widget")) {
                    jQuery(".chaty-widget").removeClass("chaty-widget-show");
                }
            }

            jQuery(document).ready(function(){
                if (isChatyInMobile) {
                    jQuery("body").addClass("chaty-in-mobile");
                } else {
                    jQuery("body").addClass("chaty-in-desktop");
                }
                "" != i("display_cta"), token = "", jQuery(document).ready(function () {
                    e = chaty_settings;
                    "true" == e.object_settings.active && (function (e, n) {
                        var o = e.object_settings.device, a = "";
                        if ("right" == e.object_settings.position) a = "left: auto;bottom: 25px; right: 25px;"; else if ("left" == e.object_settings.position) a = "right: auto; bottom: 25px; left: 25px;"; else if ("custom" == e.object_settings.position) {
                            var c = e.object_settings.pos_side, s = e.object_settings.bot, r = e.object_settings.side;
                            a = "right" === c ? "left: auto; bottom: " + s + "px; right: " + r + "px" : "left: " + r + "px; bottom: " + s + "px; right: auto"
                        }
                        var g = e.object_settings.cta, d = "", l = e.object_settings.social;

                        if (Object.keys(l).length >= 1 && (d = '<div class="chaty-widget hide-widget ' + n + " " + o + ' "   style="display:block; ' + a + '" dir="ltr">', d += '<div class="chaty-widget-is" id="transition_disabled">'), d += function (e) {
                            var i = "", n = 0;
                            return t.each(e.object_settings.social, function (t, o) {
                                if (e.object_settings.isPRO && jQuery("body").addClass("has-pro-version"), !e.object_settings.isPRO && "3" == ++n) return !1;
                                extra_class = "", "1" != e.object_settings.analytics && 1 != e.object_settings.analytics || (extra_class += " update-analytics ");
                                var desktopClass = (e.object_settings.social[t].is_desktop == 1) ? "is-in-desktop" : "";
                                var mobileClass = (e.object_settings.social[t].is_mobile == 1) ? "is-in-mobile" : "";
                                var targetAction = (e.object_settings.is_mobile == 1) ? e.object_settings.social[t].mobile_target : e.object_settings.social[t].desktop_target;
                                if (jQuery("body").hasClass("chaty-in-mobile")) {
                                    e.object_settings.social[t].href_url = e.object_settings.social[t].mobile_url;
                                }

                                if (e.object_settings.social[t].social_channel == "viber") {
                                    if (jQuery("body").hasClass("chaty-in-mobile")) {
                                        var viberVal = e.object_settings.social[t].href_url;
                                        if (!isNaN(viberVal)) {
                                            viberVal = viberVal.replace("+", "");
                                            if (navigator.userAgent.match(/(iPod|iPhone|iPad)/)) {
                                                viberVal = "+" + viberVal;
                                            }
                                        }
                                        e.object_settings.social[t].href_url = viberVal;
                                    }
                                    e.object_settings.social[t].href_url = "viber://chat?number=" + e.object_settings.social[t].href_url;
                                }
                                if(parseInt(e.object_settings.social[t].has_custom_popup) == 1) {
                                    if(e.object_settings.social[t].channel_type == "whatsapp") {
                                        if(e.object_settings.social[t].is_default_open) {
                                            if(is_chaty_settings_expired("cht_whatsapp_window")) {
                                                extra_class += " open-it-by-default";
                                            }
                                        }
                                        targetAction = "";
                                        e.object_settings.social[t].mobile_target = "";
                                        e.object_settings.social[t].desktop_target = "";
                                        extra_class += " has-custom-chaty-popup whatsapp-button";
                                    } else if(e.object_settings.social[t].channel_type == "contact_us") {
                                        extra_class += " has-custom-chaty-popup whatsapp-button";
                                    }
                                }
                                var onclick_settings = "";
                                if (e.object_settings.social[t].on_click != "") {
                                    onclick_settings = ' onclick="' + e.object_settings.social[t].on_click + '"';
                                }
                                socialString = '<div data-popup="'+e.object_settings.social[t].popup_html+'" data-rgb="'+e.object_settings.social[t].rbg_color+'" class="chaty-widget-i chaty-main-widget ' + desktopClass + " " + mobileClass + " " + extra_class + " channel-" + e.object_settings.social[t].social_channel + '" data-title="' + e.object_settings.social[t].val + '" id="chaty-channel-' + e.object_settings.social[t].social_channel + '" data-channel="' + e.object_settings.social[t].social_channel + '" data-code="' + e.object_settings.social[t].qr_code_image + '">',
                                    bgColor = "",
                                "" != e.object_settings.social[t].bg_color && (socialString += "<style>#chaty-channel-" + e.object_settings.social[t].social_channel + " .color-element {fill: " + e.object_settings.social[t].bg_color + "; background: " + e.object_settings.social[t].bg_color + "}</style>", bgColor = "style='background-color: " + e.object_settings.social[t].bg_color + ";'"), socialString += "<a class='set-url-target' " + onclick_settings + " rel='noopener' data-mobile-target='" + e.object_settings.social[t].mobile_target + "' data-desktop-target='" + e.object_settings.social[t].desktop_target + "' target='" + targetAction + "' href='" + e.object_settings.social[t].href_url + "' >", socialString += "<span class='sr-only'>"+e.object_settings.social[t].title+"</span>", "" != e.object_settings.social[t].img_url ? socialString += "<span " + bgColor + " class='chaty-social-img'><img src='" + e.object_settings.social[t].img_url + "' alt='" + e.object_settings.social[t].title + "' /></span>" : socialString += e.object_settings.social[t].default_icon, socialString += "</a>", socialString += "<div class='chaty-widget-i-title'><p>" + e.object_settings.social[t].title + "</p></div>", socialString += "</div>";
                                i += socialString;
                            }), i
                        }(e), l = e.object_settings.social, Object.keys(l).length >= 1) {
                            d += "</div>", d += '<div class="i-trigger">';
                            var h = i("display_cta");
                            var CU = current_url = window.location.origin;
                            CU = CU.replace("https://", "");
                            CU = CU.replace("http://", "");
                            if ("" != g && "none" != h) var p = "true"; else p = "no-tooltip";
                            if (chaty_settings.object_settings.click_setting != 'click') {
                                p = true;
                            }
                            p = true;
                            d += '<div class="chaty-widget-i chaty-close-settings i-trigger-open ' + p + ' ">', d += function (t) {
                                switch (t.object_settings.widget_type) {
                                    case"chat-image":
                                        if (t.object_settings.widget_img.length > 1) return '<div class="widget-img" style="background-color:' + t.object_settings.color + '"><img src="' + t.object_settings.widget_img + '"/></div>';
                                    case"chat-smile":
                                        return '<svg version="1.1" id="smile" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496.8 507.1 54 54" style="enable-background:new -496.8 507.1 54 54;" xml:space="preserve"><style type="text/css">.sts1{fill:#FFFFFF;}  .sts2{fill:none;stroke:#808080;stroke-width:1.5;stroke-linecap:round;stroke-linejoin:round;}</style><g><circle cx="-469.8" cy="534.1" r="27" fill="' + t.object_settings.color + '"/></g><path class="sts1" d="M-459.5,523.5H-482c-2.1,0-3.7,1.7-3.7,3.7v13.1c0,2.1,1.7,3.7,3.7,3.7h19.3l5.4,5.4c0.2,0.2,0.4,0.2,0.7,0.2c0.2,0,0.2,0,0.4,0c0.4-0.2,0.6-0.6,0.6-0.9v-21.5C-455.8,525.2-457.5,523.5-459.5,523.5z"/><path class="sts2" d="M-476.5,537.3c2.5,1.1,8.5,2.1,13-2.7"/><path class="sts2" d="M-460.8,534.5c-0.1-1.2-0.8-3.4-3.3-2.8"/></svg>';
                                    case"chat-bubble":
                                        return '<svg version="1.1"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496.9 507.1 54 54" style="enable-background:new -496.9 507.1 54 54;" xml:space="preserve"><style type="text/css">.sts1{fill:#FFFFFF;}</style><g><circle  cx="-469.9" cy="534.1" r="27" fill="' + t.object_settings.color + '"/></g><path class="sts1" d="M-472.6,522.1h5.3c3,0,6,1.2,8.1,3.4c2.1,2.1,3.4,5.1,3.4,8.1c0,6-4.6,11-10.6,11.5v4.4c0,0.4-0.2,0.7-0.5,0.9   c-0.2,0-0.2,0-0.4,0c-0.2,0-0.5-0.2-0.7-0.4l-4.6-5c-3,0-6-1.2-8.1-3.4s-3.4-5.1-3.4-8.1C-484.1,527.2-478.9,522.1-472.6,522.1z   M-462.9,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-464.6,534.6-463.9,535.3-462.9,535.3z   M-469.9,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-471.7,534.6-471,535.3-469.9,535.3z   M-477,535.3c1.1,0,1.8-0.7,1.8-1.8c0-1.1-0.7-1.8-1.8-1.8c-1.1,0-1.8,0.7-1.8,1.8C-478.8,534.6-478.1,535.3-477,535.3z"/></svg>';
                                    case"chat-db":
                                        return '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.1 54 54" style="enable-background:new -496 507.1 54 54;" xml:space="preserve"><style type="text/css">.sts1{fill:#FFFFFF;}</style><g><circle  cx="-469" cy="534.1" r="27" fill="' + t.object_settings.color + '"/></g><path class="sts1" d="M-464.6,527.7h-15.6c-1.9,0-3.5,1.6-3.5,3.5v10.4c0,1.9,1.6,3.5,3.5,3.5h12.6l5,5c0.2,0.2,0.3,0.2,0.7,0.2c0.2,0,0.2,0,0.3,0c0.3-0.2,0.5-0.5,0.5-0.9v-18.2C-461.1,529.3-462.7,527.7-464.6,527.7z"/><path class="sts1" d="M-459.4,522.5H-475c-1.9,0-3.5,1.6-3.5,3.5h13.9c2.9,0,5.2,2.3,5.2,5.2v11.6l1.9,1.9c0.2,0.2,0.3,0.2,0.7,0.2c0.2,0,0.2,0,0.3,0c0.3-0.2,0.5-0.5,0.5-0.9v-18C-455.9,524.1-457.5,522.5-459.4,522.5z"/></svg>';
                                    default:
                                        return '<svg version="1.1" id="ch" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54" style="enable-background:new -496 507.7 54 54;" xml:space="preserve"><style type="text/css">.sts1 {fill: #FFFFFF;}.st0{fill: #808080;}</style><g><circle cx="-469" cy="534.7" r="27" fill="' + t.object_settings.color + '"/></g><path class="sts1" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"/><path class="st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,530.8-478.2,530.5-477.7,530.5z"/><path class="st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0C-478.6,533.9-478.2,533.5-477.7,533.5z"/></svg>'
                                }
                            }(e), h = i("display_cta"), "" != g && "none" != h && (d += ' <div class="chaty-widget-i-title true"> ', d += g, d += "</div>"), d += "</div>", d += '<div class="chaty-widget-i chaty-close-settings i-trigger-close" data-title="' + e.object_settings.close_text + '" style="background-color:' + e.object_settings.color + '">', "" == e.object_settings.close_img ? (d += '<svg viewBox="0 0 54 54" fill="none" xmlns="http://www.w3.org/2000/svg">', d += '<ellipse cx="26" cy="26" rx="26" ry="26" fill="' + e.object_settings.color + '"/>', d += '<rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(18.35 15.6599) scale(0.998038 1.00196) rotate(45)" fill="white"/>', d += '<rect width="27.1433" height="3.89857" rx="1.94928" transform="translate(37.5056 18.422) scale(0.998038 1.00196) rotate(135)" fill="white"/>', d += "</svg>") : d += "<span class='chaty-social-img'><img alt='" + e.object_settings.close_text + "' src='" + e.object_settings.close_img + "' /></span>", d += '<div class="chaty-widget-i-title">', d += e.object_settings.close_text, d += "</div>", d += "</div>", d += " </div>", 0 === n.length && !e.object_settings.isPRO && (d += ''), d += "</div>"
                        } else ;
                        t("body").append(d);
                    }(e, token)), function () {
                        var n = t(".chaty-widget"),
                            a = e.object_settings.widget_size ? e.object_settings.widget_size : 54,
                            c = +e.object_settings.widget_size + 8;

                        function s() {
                            var t = n.position().top, e = n.find(".chaty-widget-is .chaty-widget-i").length;
                            if (jQuery("body").hasClass("chaty-in-desktop")) {
                                e = n.find(".chaty-widget-is .chaty-widget-i.is-in-desktop").length;
                            } else {
                                e = n.find(".chaty-widget-is .chaty-widget-i.is-in-mobile").length;
                            }
                            if (e * c > (jQuery(window).height() - (chaty_settings.object_settings.widget_size + 8))) {
                                var i = Math.round(Math.sqrt(e)), o = Math.ceil(Math.sqrt(e));
                                n.find(".chaty-widget-is").css({
                                    height: o * c,
                                    width: i * c
                                }), g(o), n.find("span:not(.cht-pending-message)").css({
                                    height: o * c,
                                    width: i * c
                                }), g(o), n.find("img").css({height: o * c, width: i * c}), g(o)
                            } else g()
                        }

                        function r() {
                            "left" === e.object_settings.position && n.addClass("chaty-widget-is-right"), "custom" === e.object_settings.position && "left" === e.object_settings.pos_side && n.addClass("chaty-widget-is-right")
                        }

                        function g(t) {
                            var e;
                            if (jQuery("body").hasClass("chaty-in-desktop")) {
                                e = n.find(".chaty-widget-is .chaty-widget-i.is-in-desktop").length;
                            } else {
                                e = n.find(".chaty-widget-is .chaty-widget-i.is-in-mobile").length;
                            }
                            n.find(".chaty-widget-i").css({
                                height: a + "px",
                                width: a + "px"
                            }), n.find("img").css({
                                height: a + "px",
                                width: a + "px"
                            }), n.find("span:not(.cht-pending-message)").css({
                                height: a + "px",
                                width: a + "px"
                            }), n.find(".chaty-widget-is").css({top: "-" + 100 * e + "%"});
                            n.find(".chaty-widget-is").height(e * (parseInt(a) + 8));
                            n.find(".chaty-widget-is").width(parseInt(a) + 8);

                            if (chaty_settings.object_settings.mode == "horizontal") {
                                n.find(".chaty-widget-is").css({top: "0"});
                                n.find(".chaty-widget-is").width(e * (parseInt(a) + 8));
                                n.find(".chaty-widget-is").height(parseInt(a) + 8);
                            } else {
                                n.find(".chaty-widget-is").height(e * (parseInt(a) + 8));
                                n.find(".chaty-widget-is").width(parseInt(a) + 8);
                            }
                        }

                        if(jQuery(".open-it-by-default").length) {
                            jQuery(".open-it-by-default").trigger("click");
                        }

                        r(), s(), jQuery(window).resize(function () {
                            s(), 1 == Object.keys(e.object_settings.social).length && (jQuery(".chaty-widget").addClass("chaty-widget-show"), jQuery(".chaty-widget-is").css("top", "auto"))
                            if(jQuery(".chaty-widget").hasClass("one_widget")) {
                                jQuery(".chaty-widget").removeClass("chaty-widget-show");
                            }
                        }), t(".chaty-widget-i.facebook").mouseenter(function () {
                            t(".facebook_two_mess").css({opacity: "1", "z-index": "1"}), t(this).addClass("before")
                        }), t(".chaty-widget-i.facebook").mouseleave(function () {
                            t(".facebook_two_mess").css({opacity: "0", "z-index": "1"}), t(this).removeClass("before")
                        }), t(".chaty-widget-i.facebook").on("click", function () {
                            t(this).addClass("active"), t(".facebook_two_mess").hide(), jQuery(".chaty-widget .get a").hide(), t(this).children(".face_title").css({
                                opacity: "1",
                                "z-index": "2"
                            }).show(), t(".chaty-widget").hasClass("one_widget") ? t(".chaty-widget").hasClass("chaty-widget-is-right") ? t(".face_title").css({
                                top: "calc(100% - 447px)",
                                left: "20px"
                            }) : t(".face_title").css({
                                top: "calc(100% - 447px)",
                                left: "auto",
                                right: "20px"
                            }) : (t(".facebook_two_mess").css({
                                opacity: "0",
                                "z-index": "10001"
                            }), t(".ico_d").hide(), t(".chaty-widget .chaty-widget-i").css({"box-shadow": "0px 3px 6px rgba(0,0,0,0)"}), t(".chaty-widget").hasClass("one_widget") || t(".i-trigger").hide())
                        }), t(".i-trigger-close").on("click", function (e) {
                            e.preventDefault(), t(".chaty-widget-i.facebook").hasClass("active") || (n.removeClass("chaty-widget-show"), n.addClass("none-widget-show"))
                        }), t("body").on("click", ".close_facebook", function () {
                            t(".facebook_two_mess").show(), jQuery(".chaty-widget .get a").show(), t(".chaty-widget-i.facebook").removeClass("active"), t(this).parent().parent().css({opacity: "1"}).hide(), t(".i-trigger").show(), t(".chaty-widget-is .chaty-widget-i svg").show()
                        }), t(".i-trigger-close").on("click", function (e) {
                            t(".i-trigger-open").addClass("active_clos"), e.preventDefault(), t(".chaty-widget-i.facebook").hasClass("active") || (n.removeClass("chaty-widget-show"), n.addClass("none-widget-show"))
                        }), t("body").on("click", ".update-analytics", function (t) {
                            if (channelName = jQuery(this).attr("data-channel"), null != channelName && "" != channelName) if (window.hasOwnProperty("gtag")) gtag("event", "chaty_" + channelName, {
                                eventCategory: "chaty_" + channelName,
                                event_action: "chaty_" + channelName
                            }); else if (window.hasOwnProperty("ga")) {
                                var e = window.ga.getAll()[0];
                                e && e.send("event", "click", {
                                    eventCategory: "chaty_" + channelName,
                                    eventAction: "chaty_" + channelName
                                })
                            }
                        }), t("body").on("click", ".wechat", function () {
                            var t = jQuery(".chaty-widget .chaty-widget-i.wechat").attr("data-code");
                            null != t && "" != t && (jQuery("#wechat-qr-code").length || (htmlString = "<div id='wechat-qr-code' class='wechat-qr-code'>", htmlString += '<div class="wechat-box-head">WeChat<svg xmlns="http://www.w3.org/2000/svg" class="close_facebook" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="612px" height="612px" viewBox="0 0 612 612" style="fill: #fff;    float: right; margin-top: 4px;" xml:space="preserve"><path xmlns="http://www.w3.org/2000/svg" d="M268.064,256.75l138.593-138.593c3.124-3.124,3.124-8.189,0-11.313c-3.125-3.124-8.189-3.124-11.314,0L256.75,245.436   L118.157,106.843c-3.124-3.124-8.189-3.124-11.313,0c-3.125,3.124-3.125,8.189,0,11.313L245.436,256.75L106.843,395.343   c-3.125,3.125-3.125,8.189,0,11.314c1.562,1.562,3.609,2.343,5.657,2.343s4.095-0.781,5.657-2.343L256.75,268.064l138.593,138.593   c1.563,1.562,3.609,2.343,5.657,2.343s4.095-0.781,5.657-2.343c3.124-3.125,3.124-8.189,0-11.314L268.064,256.75z"></path></svg></div>', htmlString += "<div class='wechat-box'><img src='" + t + "' alt='QR Code' /><a href='javascript:;'>", htmlString += "</a></div></div>", jQuery("body").append(htmlString)), jQuery("#wechat-qr-code").show())
                        });
                        var d = i("display_cta");
                        if (chaty_settings.object_settings.display_state == "hover") {
                            n.find(".i-trigger-open").mouseenter(function (e) {
                                e.stopPropagation();
                                //removeAnimation();
                                //t(this).removeClass("active_clos");
                                o(), t(".chaty-widget-is").removeAttr("id"), t(this).hasClass("active_clos") || t(".chaty-widget-i.facebook").hasClass("active") || n.hasClass("one_widget") || (r(), t(n).hasClass("chaty-widget-show") || (n.addClass("chaty-widget-show"), n.removeClass("none-widget-show")));
                                if(jQuery(".chaty-widget").hasClass("one_widget")) {
                                    jQuery(".chaty-widget").removeClass("chaty-widget-show");
                                }
                                //o();
                                if (chaty_settings.object_settings.click_setting == 'click') {
                                    if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                        jQuery(this).closest(".chaty-widget-i").addClass("hide-chaty-arrow");
                                        jQuery("body .chaty-widget-i-title.true").remove();
                                    }
                                }
                            });
                        } else {
                            n.find(".i-trigger-open").click(function (e) {
                                e.stopPropagation();
                                removeAnimation();
                                o(), t(".chaty-widget-is").removeAttr("id"), t(this).hasClass("active_clos") || t(".chaty-widget-i.facebook").hasClass("active") || n.hasClass("one_widget") || (r(), t(n).hasClass("chaty-widget-show") || (n.addClass("chaty-widget-show"), n.removeClass("none-widget-show")));
                                o();
                                if (chaty_settings.object_settings.click_setting == 'click') {
                                    if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                        jQuery(this).closest(".chaty-widget-i").addClass("hide-chaty-arrow");
                                        jQuery("body .chaty-widget-i-title.true").remove();
                                    }
                                }
                                jQuery(".cht-pending-message").remove();
                            });
                        }
                        t(".one_widget").on("click", function () {
                            //t(".chaty-widget-i-title").hasClass("face_title") ? (t(".facebook_two_mess").detach(), t(".chaty-widget-i").addClass("bofore_del")) : (t(".chaty-widget-i-title").detach(), t(".chaty-widget-i").addClass("bofore_del"));
                            removeAnimation();
                            jQuery(".cht-pending-message").remove();
                        }), n.find(".i-trigger-open").on("click", function () {
                            jQuery(".cht-pending-message").remove();
                            "none" != d && (t(".chaty-widget").hasClass(".one_widget") || t(".i-trigger-open .chaty-widget-i-title1").detach());
                            if (chaty_settings.object_settings.click_setting == "click") {
                                t(".i-trigger-open").addClass("no-tooltip")
                            };
                            t(this).hasClass("active_clos") || t(".chaty-widget-i.facebook").hasClass("active") || n.hasClass("one_widget") || (r(), t(n).hasClass("chaty-widget-show") || (n.addClass("chaty-widget-show"), n.removeClass("none-widget-show")));
                            removeAnimation();
                        }), n.find(".i-trigger-open").mouseenter(function () {
                            if (chaty_settings.object_settings.click_setting == "click") {
                                t(this).addClass("no-tooltip");
                            }
                            t(".i-trigger-open").removeClass("active_clos");
                            s();
                        }), n.find(".chaty-widget-is").mouseleave(function () {
                            t(".i-trigger-open").removeClass("active_clos"), t(".chaty-widget-i.facebook").hasClass("active")
                        }), 1 === Object.keys(e.object_settings.social).length && n.find(".i-trigger-open").on("touchstart", function (e) {
                            "use strict";
                            t(this).find(".chaty-widget-i").toggleClass("hover")
                        }), 1 == Object.keys(e.object_settings.social).length && (jQuery(".chaty-widget").addClass("chaty-widget-show"), jQuery(".chaty-widget-is").css("top", "auto"), jQuery(".chaty-widget-is").css("z-index", "10001"), jQuery(".chaty-widget-i.i-trigger-close, .i-trigger.chaty-widget-i").remove(), jQuery(".chaty-widget .get a").css("top", "-10px"), jQuery(".chaty-widget").addClass("one_widget"), jQuery(document).on("click", ".chaty-widget-i", function () {
                            if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                jQuery(".chaty-widget-i:first .chaty-widget-i-title:last").hide(), jQuery("body").addClass("hide-cht-widget")
                            }
                        }), jQuery("body").append("<style>.chaty-widget-is{top:0!important;}</style>"))
                    }(), t(document).ready(function () {
                        1 !== Object.keys(e.object_settings.social).length || !e.object_settings.social.snapchat && !e.object_settings.social.wechat || e.object_settings.cta || (t(".chaty-widget-i-1title").detach(), t(".chaty-widget-i").addClass("bofore_del")), t(".chaty-widget-i-title").hasClass("one_go") && (t(".chaty-widget-i-title p").html(t(".chaty-widget-i-title.one_go").html()), "none" == i("display_cta") && (t(".chaty-widget-i-title").hasClass("face_title") ? (t(".facebook_two_mess").detach(), t(".chaty-widget-i").addClass("bofore_del")) : (t(".chaty-widget-i-title11").detach(), t(".chaty-widget-i").addClass("bofore_del")))), window.matchMedia("only screen and (max-width: 760px)").matches && t(".i-trigger-open").addClass("active_clos"), jQuery(".chaty-widget-i-title").each(function () {
                            "" == jQuery(this).text() && jQuery(this).remove()
                        })
                    }), t(document).ready(function () {
                        var activeWidget = 0;
                        if (jQuery("body").hasClass("chaty-in-desktop")) {
                            activeWidget = jQuery(".chaty-widget-is .chaty-widget-i.is-in-desktop").length;
                        } else {
                            activeWidget = jQuery(".chaty-widget-is .chaty-widget-i.is-in-mobile").length;
                        }
                        if (activeWidget == 0) {
                            jQuery(".chaty-widget").addClass("hide-widget").removeClass("desktop_active").removeClass("mobile_active");
                        } else {
                            if (jQuery("body").hasClass("chaty-in-desktop")) {
                                jQuery(".chaty-widget").removeClass("hide-widget").addClass("desktop_active");
                            } else {
                                jQuery(".chaty-widget").removeClass("hide-widget").addClass("mobile_active");
                            }
                            if (activeWidget == 1) {
                                jQuery(".chaty-close-settings").hide();
                                if (jQuery("body").hasClass("chaty-in-desktop")) {
                                    htmlToAdd = jQuery(".chaty-widget-is .chaty-widget-i.is-in-desktop:first").clone();
                                    jQuery(".i-trigger").html(htmlToAdd);
                                } else {
                                    htmlToAdd = jQuery(".chaty-widget-is .chaty-widget-i.is-in-mobile:first").clone();
                                    jQuery(".i-trigger").html(htmlToAdd);
                                }
                                if(!get_cta_status()) {
                                    if(chaty_settings.object_settings['click_setting'] != "all_time") {
                                        jQuery(".i-trigger").attr("data-title", jQuery(".chaty-widget-is .chaty-widget-i-title p").html());
                                        jQuery(".i-trigger .chaty-widget-i-title p").html(chaty_settings.object_settings.cta);
                                    }
                                } else {
                                    if(chaty_settings.object_settings['click_setting'] != "all_time") {
                                        jQuery(".i-trigger").addClass("show-channel");
                                        jQuery(".i-trigger").attr("data-title", jQuery(".chaty-widget-is .chaty-widget-i-title p").html());
                                    } else {
                                        jQuery(".i-trigger .chaty-widget-i-title p").html(chaty_settings.object_settings.cta);
                                    }
                                }
                                jQuery(".chaty-widget").addClass("one_widget");

                                jQuery(".chaty-widget.one_widget, .i-trigger, .chaty-widget-i").mouseenter(function () {
                                    o();
                                    if (chaty_settings.object_settings.click_setting == 'click') {
                                        if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                            jQuery(".chaty-widget-i-title").remove();
                                            jQuery(".chaty-widget").addClass("hide-tooltip-arrow");
                                        }
                                        jQuery(".chaty-widget").removeClass("chaty-widget-show");
                                    }
                                    if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                        set_cta_status();
                                    }
                                    //removeAnimation();
                                });
                                jQuery(".chaty-widget").removeClass("chaty-widget-show");
                                jQuery(".i-trigger").addClass("one-widget");
                                if(get_cta_status());

                                setCSSKeyFrames(jQuery(".i-trigger .chaty-widget-i:first").data("rgb"));
                            } else {
                                jQuery(".chaty-widget").removeClass("one_widget");
                                if (chaty_settings.object_settings.click_setting == 'click') {
                                    jQuery(".i-trigger .chaty-main-widget").remove();
                                }
                                jQuery(".chaty-close-settings").show();
                                jQuery(".chaty-widget-i-title").removeClass("hide-it");
                                jQuery(".chaty-widget.one_widget, .i-trigger, .chaty-widget-i").mouseenter(function () {
                                    o();
                                    if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                        set_cta_status();
                                        if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                            jQuery(".i-trigger .chaty-widget-i-title").addClass("hide-it");
                                        }
                                    }
                                    // jQuery(".chaty-widget").addClass("hide-tooltip-arrow");
                                    removeAnimation();
                                });

                                if (chaty_settings.object_settings.display_state == "open") {
                                    t(".i-trigger-open").addClass("no-tooltip")
                                }

                                setCSSKeyFrames(chaty_settings.object_settings.rgb_color);
                            }
                        }

                        jQuery(document).on("click", ".i-trigger.one-widget", function(){
                            set_cta_status();
                        });

                        /* check for display rules */
                        var displayStatus = 0;
                        if (parseInt(chaty_settings.object_settings.display_conditions) == 1) {
                            var displayRules = chaty_settings.object_settings.display_rules;

                            if (displayRules.length > 0) {
                                var localDate = new Date();
                                localDate.setHours(localDate.getHours() + (chaty_settings.object_settings.gmt));
                                var utcHours = localDate.getUTCHours();
                                var utcMin = localDate.getUTCMinutes();
                                var utcDay = localDate.getUTCDay();
                                for (var rule = 0; rule < displayRules.length; rule++) {
                                    var hourStatus = 0;
                                    var minStatus = 0;
                                    var checkForTime = 0;
                                    if (displayRules[rule].days == -1) {
                                        checkForTime = 1;
                                    } else if (displayRules[rule].days >= 0 && displayRules[rule].days <= 6) {
                                        if (displayRules[rule].days == utcDay) {
                                            checkForTime = 1;
                                        }
                                    } else if (displayRules[rule].days == 7) {
                                        if (utcDay >= 0 && utcDay <= 4) {
                                            checkForTime = 1;
                                        }
                                    } else if (displayRules[rule].days == 8) {
                                        if (utcDay >= 1 && utcDay <= 5) {
                                            checkForTime = 1;
                                        }
                                    } else if (displayRules[rule].days == 9) {
                                        if (utcDay == 5 || utcDay == 6) {
                                            checkForTime = 1;
                                        }
                                    }
                                    if (checkForTime == 1) {
                                        if (utcHours > displayRules[rule].start_hours && utcHours < displayRules[rule].end_hours) {
                                            hourStatus = 1;
                                        } else if (utcHours == displayRules[rule].start_hours && utcHours < displayRules[rule].end_hours) {
                                            if (utcMin >= displayRules[rule].start_min) {
                                                hourStatus = 1;
                                            }
                                        } else if (utcHours > displayRules[rule].start_hours && utcHours == displayRules[rule].end_hours) {
                                            if (utcMin <= displayRules[rule].end_min) {
                                                hourStatus = 1;
                                            }
                                        } else if (utcHours == displayRules[rule].start_hours && utcHours == displayRules[rule].end_hours) {
                                            if (utcMin >= displayRules[rule].start_min && utcMin <= displayRules[rule].end_min) {
                                                hourStatus = 1;
                                            }
                                        }

                                        if (hourStatus == 1) {
                                            if (utcMin >= displayRules[rule].start_min && utcMin <= displayRules[rule].end_min) {
                                                minStatus = 1;
                                            }
                                        }
                                    }

                                    if (hourStatus == 1 && checkForTime == 1) {
                                        displayStatus = 1;
                                    }
                                    if (displayStatus == 1) {
                                        rule = displayRules.length + 1;
                                    }
                                }
                            } else {
                                displayStatus = 1;
                            }
                        } else {
                            displayStatus = 1;
                        }

                        if (activeWidget == 1) {
                            if (i("display_cta") != "") {
                                if (chaty_settings.object_settings.click_setting == 'click') {
                                    if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                        jQuery(".chaty-widget-i-title").remove();
                                        jQuery(".chaty-widget").addClass("hide-tooltip-arrow");
                                    }
                                }
                                setInterval(function () {
                                    //set_cta_status();
                                }, 10000);
                            }

                            jQuery(".chaty-main-widget").addClass("i-trigger-open").addClass("single-button");

                            jQuery(".chaty-widget-show .i-trigger-open svg").css("transform", "rotate(0deg)");

                            jQuery(".chaty-widget").removeClass("chaty-widget-show");
                        }

                        if (chaty_settings.object_settings.animation_class != "") {
                            if (chaty_settings.object_settings.animation_class != "sheen") {
                                jQuery(".i-trigger .chaty-widget-i svg").wrap(function () {
                                    return "<div class='svg'></div>";
                                });
                            } else {
                                if (!jQuery(".i-trigger .chaty-widget-i .wrap-svg").length) {
                                    jQuery(".i-trigger .chaty-widget-i svg").wrap(function () {
                                        return "<div class='wrap-svg'></div>";
                                    });
                                }
                            }
                        }

                        if (!get_cta_status()) {
                            if (chaty_settings.object_settings.pending_messages == "on") {
                                if (chaty_settings.object_settings.animation_class != "sheen") {
                                    if(jQuery(".i-trigger .i-trigger-open .svg, .i-trigger .i-trigger-open.single-button .svg").length) {
                                        jQuery(".i-trigger .i-trigger-open .svg, .i-trigger .i-trigger-open.single-button .svg").append("<span class='cht-pending-message'>" + chaty_settings.object_settings.number_of_messages + "</span>");
                                    } else if(jQuery(".i-trigger .i-trigger-open.single-button svg").length) {
                                        jQuery(".i-trigger .i-trigger-open.single-button svg").after("<span class='cht-pending-message'>" + chaty_settings.object_settings.number_of_messages + "</span>");
                                    } else {
                                        jQuery(".i-trigger .i-trigger-open, .i-trigger .i-trigger-open.single-button svg").after("<span class='cht-pending-message'>" + chaty_settings.object_settings.number_of_messages + "</span>");
                                    }
                                } else {
                                    jQuery(".i-trigger .i-trigger-open, .i-trigger .i-trigger-open.single-button").after("<span class='cht-pending-message'>" + chaty_settings.object_settings.number_of_messages + "</span>");
                                }
                                jQuery(".cht-pending-message").css("color", chaty_settings.object_settings.number_color);
                                jQuery(".cht-pending-message").css("background", chaty_settings.object_settings.number_bg_color);
                            }

                            jQuery(document).on("click", ".i-trigger .i-trigger-open, .i-trigger.one-widget", function () {
                                jQuery(".cht-pending-message").remove();

                                if(chaty_settings.object_settings['click_setting'] != "all_time" && jQuery(this).hasClass("one-widget")) {
                                    jQuery(this).addClass("show-channel");
                                    var tooltipText = jQuery(this).data("title");
                                    jQuery(this).find(".chaty-widget-i-title").find("p").html(tooltipText);
                                }
                            });
                        }

                        jQuery(document).on("click", ".i-trigger.one-widget", function(){
                            if(chaty_settings.object_settings['click_setting'] != "all_time" && jQuery(this).hasClass("one-widget")) {
                                jQuery(this).addClass("show-channel");
                                var tooltipText = jQuery(this).data("title");
                                jQuery(this).find(".chaty-widget-i-title").find("p").html(tooltipText);
                            }
                        });

                        jQuery("chaty-main-widget").mouseenter(function () {
                            if(!jQuery(".i-trigger").hasClass("one-widget")) {
                                set_cta_status();
                            }
                        });

                        if (displayStatus == 1) {
                            var widget_status = is_chaty_settings_expired("cta_widget_status");
                            if (!widget_status) {
                                jQuery(".chaty-widget").removeClass("hide-widget");
                                show_chaty_widget();
                            } else {
                                /* set animation */
                                if (chaty_settings.object_settings.time_trigger == "no" && chaty_settings.object_settings.exit_intent == "no" && chaty_settings.object_settings.on_page_scroll == "no") {
                                    jQuery(".chaty-widget").removeClass("hide-widget");
                                    if (chaty_settings.object_settings.display_state == "open" && chaty_settings.object_settings.has_close_button == "no") {
                                        chaty_settings.object_settings.has_close_button = "no";
                                    } else {
                                        chaty_settings.object_settings.display_state = "hover";
                                        chaty_settings.object_settings.has_close_button = "yes";
                                    }
                                    show_chaty_widget();
                                } else {
                                    jQuery(".chaty-widget").addClass("hide-widget");
                                    if (chaty_settings.object_settings.time_trigger == "yes") {
                                        if(parseInt(chaty_settings.object_settings.trigger_time) > 0) {
                                            setTimeout(function () {
                                                if (!isWidgetEnabled) {
                                                    jQuery(".chaty-widget").removeClass("hide-widget");
                                                    show_chaty_widget();
                                                }
                                            }, parseInt(chaty_settings.object_settings.trigger_time) * 1000);
                                        } else {
                                            jQuery(".chaty-widget").removeClass("hide-widget");
                                            show_chaty_widget();
                                        }
                                    }
                                    if (chaty_settings.object_settings.exit_intent == "yes") {
                                        function addEvent(obj, evt, fn) {
                                            if (obj.addEventListener) {
                                                obj.addEventListener(evt, fn, false);
                                            } else if (obj.attachEvent) {
                                                obj.attachEvent("on" + evt, fn);
                                            }
                                        }

                                        addEvent(document, 'mouseout', function (evt) {
                                            if (evt.toElement == null && evt.relatedTarget == null) {
                                                var widget_status = get_chaty_cookie("cta_exit_intent_shown");
                                                if (widget_status == null) {
                                                    set_chaty_cookie("cta_exit_intent_shown", "yes", 1);
                                                    isWidgetEnabled = true;
                                                    jQuery(".chaty-widget").removeClass("hide-widget");
                                                    jQuery(".chaty-widget").addClass("chaty-animation-widget");
                                                    jQuery(".chaty-animation-widget").append("<div class='chaty-nav'></div>");
                                                    if (chaty_settings.object_settings.display_state == "open" && chaty_settings.object_settings.has_close_button == "no") {
                                                        chaty_settings.object_settings.has_close_button = "no";
                                                    } else {
                                                        chaty_settings.object_settings.display_state = "open";
                                                        chaty_settings.object_settings.has_close_button = "yes";
                                                    }
                                                    show_chaty_widget();
                                                    if (chaty_settings.object_settings.position == "left") {
                                                        jQuery(".chaty-widget").addClass("left-position");
                                                    } else if (chaty_settings.object_settings.position == "right") {
                                                        jQuery(".chaty-widget").addClass("right-position");
                                                    } else if (chaty_settings.object_settings.position == "custom") {
                                                        if (e.object_settings.pos_side == "left") {
                                                            jQuery(".chaty-widget").addClass("left-position");
                                                        } else {
                                                            jQuery(".chaty-widget").addClass("right-position");
                                                        }
                                                    }
                                                    removeAnimation();
                                                    setTimeout(function () {
                                                        jQuery(".chaty-animation-widget").addClass("active");
                                                    }, 100);
                                                    setTimeout(function () {
                                                        jQuery(".chaty-nav").remove();
                                                    }, 2500);
                                                }
                                            }
                                        });
                                    }
                                    if (chaty_settings.object_settings.on_page_scroll == "yes") {
                                        if (parseInt(chaty_settings.object_settings.page_scroll) > 0) {
                                            jQuery(window).scroll(function () {
                                                if (!isWidgetEnabled) {
                                                    var scrollHeight = jQuery(document).height() - jQuery(window).height();
                                                    var scrollPos = jQuery(window).scrollTop();
                                                    if (scrollPos != 0) {
                                                        if (((scrollPos / scrollHeight) * 100) >= parseInt(chaty_settings.object_settings.page_scroll)) {
                                                            jQuery(".chaty-widget").removeClass("hide-widget");
                                                            show_chaty_widget();
                                                        }
                                                    }
                                                }
                                            });
                                        } else {
                                            jQuery(".chaty-widget").removeClass("hide-widget");
                                            show_chaty_widget();
                                        }
                                    }
                                }
                            }
                        } else {
                            jQuery(".chaty-widget").addClass("hide-widget");
                        }

                        if(jQuery(".chaty-widget-i-title").length) {
                            jQuery(".chaty-widget-i-title").each(function(){
                                if(jQuery.trim(jQuery(this).text()) == "") {
                                    jQuery(this).closest(".chaty-widget-i").addClass("hide-chaty-arrow");
                                    jQuery(this).remove();
                                }
                            });
                        }

                        if(!jQuery(".chaty-widget-i.true.chaty-widget-i-title").length) {
                            if(jQuery(".chaty-widget-i-title").text() == "") {
                                jQuery(".chaty-widget-i").addClass("hide-chaty-arrow");
                            }
                        }

                        if(!jQuery(".i-trigger-open .chaty-widget-i-title").length) {
                            jQuery(".i-trigger .i-trigger-open").addClass("hide-chaty-arrow");
                        } else {
                            if(jQuery(".i-trigger-open .chaty-widget-i-title").text() == "") {
                                jQuery(".i-trigger-open").addClass("hide-chaty-arrow");
                            }
                        }
                    });
                    jQuery(".chaty-widget-i-title").each(function () {
                        if (jQuery(this).text() == "") {
                            jQuery(this).closest(".chaty-widget-i").addClass("hide-chaty-arrow");
                            jQuery(this).remove();
                        }
                    });

                    if (!jQuery(".chaty-widget").hasClass("one_widget")) {
                        jQuery("body").append("<div class='trigger-chaty-button'></div>");
                    }

                    jQuery("body").addClass(chaty_settings.object_settings.mode + "-cht-menu");
                    jQuery("body").addClass(chaty_settings.object_settings.position + "-cht-position");
                });
            });

            jQuery(document).on("click", ".trigger-chaty-button", function(){
                //chaty_settings.object_settings.display_state = "open";
                jQuery(".chaty-widget").removeClass("hide-widget");
                show_chaty_widget();
                if(jQuery(".chaty-widget").hasClass("none-widget-show") || !jQuery(".chaty-widget").hasClass("chaty-widget-show")) {
                    jQuery(".i-trigger-open").trigger("click");
                }
            });

            jQuery(document).on("click", ".has-custom-chaty-popup.whatsapp-button", function(e){
                var timeInterval = 0;
                if(jQuery("#chaty-inline-popup").length) {
                    var thisIndex = jQuery(this).data("data-index");
                    jQuery("#chaty-widget-"+thisIndex).removeClass("chaty-popup-open");
                    jQuery(".chaty-popup-open").removeClass("chaty-popup-open");
                    jQuery(".chaty-widget.hide-block").removeClass("active");
                }
                if(jQuery(this).hasClass("open-it-by-default")) {
                    e.preventDefault();
                }
                jQuery("#chaty-inline-popup").remove();
                if(jQuery(this).attr("data-popup") != undefined && jQuery(this).attr("data-popup") != "") {
                    var thisIndex = "";
                    var widgetIndex = "";
                    jQuery(".chaty-widget").addClass("hide-block");
                    jQuery(".chaty-widget").addClass("chaty-popup-open");
                    var htmlString = "<div data-index='"+thisIndex+"' id='chaty-inline-popup' class='chaty-inline-popup chaty-popup-form "+jQuery(this).data("channel")+"-channel'>";
                    htmlString += jQuery(this).attr("data-popup");
                    htmlString + "</div>";
                    jQuery("body").append(htmlString);

                    if(jQuery(".chaty-inline-popup .default-value").length) {

                        thisHref = jQuery(".chaty-inline-popup .default-value").text();
                        thisHref = decodeURI(thisHref);
                        thisHref = thisHref.replace(/{title}/g, jQuery("title").text());
                        thisHref = thisHref.replace(/{url}/g, window.location.href);

                        jQuery(".chaty-whatsapp-msg").val(jQuery(".chaty-inline-popup .default-value").text());
                        jQuery(".chaty-whatsapp-phone").val(jQuery(".chaty-inline-popup .default-msg-phone").text());

                        chatyHtml = jQuery(".chaty-inline-popup .default-msg-value").html();
                        chatyHtml = chatyHtml.replace(/{title}/g, jQuery("title").text());
                        chatyHtml = chatyHtml.replace(/{url}/g, window.location.href);

                        jQuery(".chaty-whatsapp-message").html(chatyHtml);
                    }
                    jQuery(".chaty-widget").addClass("active");
                    setTimeout(function(){
                        jQuery("#chaty-inline-popup").addClass("active");
                    }, 150);
                    if(!jQuery("body").hasClass("chaty-in-mobile")) {
                        jQuery(".chaty-whatsapp-msg").focus();
                    }
                }
                if(isChatyInMobile) {
                    jQuery(".whatsapp-chaty-form").prop("target", "");
                }
            });

            jQuery(document).on("click", ".close-chaty-popup, .close-chaty-box", function(){
                if(jQuery(this).hasClass("is-whatsapp-btn")) {
                    save_chaty_settings("cht_whatsapp_window");
                }
                jQuery("#chaty-inline-popup").removeClass("active");

                jQuery(".chaty-widget").removeClass("chaty-popup-open");
                setTimeout(function(){
                    jQuery(".chaty-widget.hide-block").removeClass("active");
                }, 250);
            });

            jQuery(document).on("submit", ".whatsapp-chaty-form", function(e){
                save_chaty_settings("cht_whatsapp_window");
                jQuery("#chaty-inline-popup").removeClass("active");
                jQuery(".chaty-widget").removeClass("chaty-popup-open");
                setTimeout(function(){
                    jQuery(".chaty-widget.hide-block").removeClass("active");
                }, 250);
                if(jQuery("body").hasClass("chaty-in-mobile")) {
                    e.preventDefault();
                    window.location = "https://wa.me/"+jQuery(this).find(".chaty-whatsapp-phone").val()+"?text="+jQuery(this).find(".chaty-whatsapp-msg").val();
                    return false;
                }
            });


            jQuery(document).on("submit", ".chaty-contact-form-data", function(e){
                var inputErrorCounter = 0;
                jQuery(".has-chaty-error").removeClass("has-chaty-error");
                jQuery(".chaty-error-msg").remove();
                jQuery(".chaty-ajax-error-message").remove();
                jQuery(".chaty-ajax-success-message").remove();
                jQuery(this).find(".is-required").each(function(){
                    if(jQuery.trim(jQuery(this).val()) == "") {
                        inputErrorCounter++;
                        jQuery(this).addClass("has-chaty-error");
                    }
                });
                if(inputErrorCounter == 0) {
                    var $form = jQuery(this);
                    jQuery(".chaty-contact-submit-btn").attr("disabled", true);
                    jQuery.ajax({
                        url: chaty_settings.ajax_url,
                        data: {
                            action: "chaty_front_form_save_data",
                            name:   $form.find(".chaty-field-name").length?$form.find(".chaty-field-name").val():"",
                            email:  $form.find(".chaty-field-email").length?$form.find(".chaty-field-email").val():"",
                            phone:  $form.find(".chaty-field-phone").length?$form.find(".chaty-field-phone").val():"",
                            message: $form.find(".chaty-field-message").length?$form.find(".chaty-field-message").val():"",
                            nonce:  $form.find(".chaty-field-nonce").length?$form.find(".chaty-field-nonce").val():"",
                            channel: $form.find(".chaty-field-channel").length?$form.find(".chaty-field-channel").val():"",
                            widget: $form.find(".chaty-field-widget").length?$form.find(".chaty-field-widget").val():"",
                            ref_url: window.location.href
                        },
                        type: 'post',
                        async: true,
                        defer: true,
                        success: function (response) {
                            response = jQuery.parseJSON(response);
                            jQuery(".chaty-ajax-error-message").remove();
                            jQuery(".chaty-ajax-success-message").remove();
                            jQuery(".chaty-contact-submit-btn").attr("disabled", false);
                            if(response.status == 1) {
                                jQuery(".chaty-contact-footer").append("<div class='chaty-ajax-success-message'>"+response.message+"</div>");
                                jQuery(".chaty-field-name, .chaty-field-email, .chaty-field-message").val("");
                                if(response.redirect_action == "yes") {
                                    if( response.link_in_new_tab == "yes" ) {
                                        window.open( response.redirect_link, '_blank' );
                                    } else {
                                        window.location = response.redirect_link;
                                    }
                                }
                                if(response.close_form_after == "yes") {
                                    setTimeout(function(){
                                        jQuery("#chaty-inline-popup").removeClass("active");
                                        jQuery(".chaty-widget").removeClass("chaty-popup-open");
                                        setTimeout(function(){
                                            jQuery(".chaty-widget.hide-block").removeClass("active");
                                        }, 250);
                                    }, parseInt(response.close_form_after_seconds)*1000);
                                }
                            } else if(response.error == 1) {
                                if(response.errors.length) {
                                    for(var i=0; i<response.errors.length; i++) {
                                        jQuery("."+response.errors[i].field).addClass("has-chaty-error");
                                        jQuery("."+response.errors[i].field).after("<span class='chaty-error-msg'>"+response.errors[i].message+"</span>");
                                    }
                                }
                            } else {
                                jQuery(".chaty-contact-footer").append("<div class='chaty-ajax-error-message'>"+response.message+"</div>");
                            }
                        }
                    });
                } else {
                    jQuery(".has-chaty-error:first").focus();
                }
                return false;
            });

            function setCSSKeyFrames(colorCode) {
                var colorString = '@-webkit-keyframes chaty-animation-shockwave ' +
                    '{ ' +
                    '0% { transform: scale(1); box-shadow: 0 0 2px rgba('+colorCode+', 0.30), inset 0 0 1px rgba('+colorCode+', 0.30); } ' +
                    '95% { box-shadow: 0 0 50px rgba('+colorCode+', 0), inset 0 0 30px rgba('+colorCode+', 0); } ' +
                    '100% { transform: scale(2.25); } ' +
                    '} ' +
                    '' +
                    '@keyframes chaty-animation-shockwave { ' +
                    '0% { transform: scale(1); box-shadow: 0 0 2px rgba('+colorCode+', 0.30), inset 0 0 1px rgba('+colorCode+', 0.30); } ' +
                    '95% { box-shadow: 0 0 50px rgba('+colorCode+', 0), inset 0 0 30px rgba('+colorCode+', 0); } ' +
                    '100% { transform: scale(2.25); } ' +
                    '}';
                if(!jQuery("#chaty-advance-css").length) {
                    jQuery("body").append("<div id='chaty-advance-css'></div>");
                }
                jQuery("#chaty-advance-css").append("<style>"+colorString+"</style>");
            }

            function get_cta_status() {
                var cookieStr = "cta";
                var cookieValue = check_for_chaty_settinigs(cookieStr);
                if(cookieValue != null && cookieValue != "") {
                    cookieValue = new Date(cookieValue);
                    var diffTime = Math.abs(new Date() - cookieValue);
                    var diffMin = Math.floor(diffTime / (1000 * 60));
                    if(diffMin <= 10) {
                        return true;
                    } else {
                        return false;
                    }
                }
                return false;
            }

            function check_for_chaty_settinigs(cookieStr) {
                var cookieString = get_chaty_cookie("chaty_settings");
                var cookieArray = [];
                if(cookieString != null && cookieString != "") {
                    cookieArray = JSON.parse(cookieString);
                }
                if(cookieArray.length > 0) {
                    for(var i=0; i<cookieArray.length; i++) {
                        if(cookieArray[i]['k'] == cookieStr) {
                            return cookieArray[i]['v'];
                        }
                    }
                }
                return null;
            }

            function save_chaty_settings(cookieStr) {
                var cookieString = get_chaty_cookie("chaty_settings");
                var cookieArray = [];
                if(cookieString != null && cookieString != "") {
                    cookieArray = JSON.parse(cookieString);
                }
                var cookieFound = false;
                if(cookieArray.length > 0) {
                    for(var i=0; i<cookieArray.length; i++) {
                        if(cookieArray[i]['k'] == cookieStr) {
                            cookieFound = true;
                            cookieArray[i]['v'] = new Date();
                        }
                    }
                }
                if(!cookieFound) {
                    cookieArray.push({"k": cookieStr, "v": new Date()});
                }
                cookieString = JSON.stringify(cookieArray);
                set_chaty_cookie("chaty_settings", cookieString, "7");
            }

            function is_chaty_settings_expired(cookieStr) {
                var cookieValue = check_for_chaty_settinigs(cookieStr);
                if(cookieValue != null && cookieValue != "") {
                    cookieValue = new Date(cookieValue);
                    var diffTime = Math.abs(new Date() - cookieValue);
                    var diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
                    if(diffDays >= 1) {
                        return true;
                    } else {
                        return false;
                    }
                }
                return true;
            }

            function set_cta_status() {
                var cookieStr = "cta";
                var cookieString = get_chaty_cookie("chaty_settings");
                var cookieArray = [];
                if(cookieString != null && cookieString != "") {
                    cookieArray = JSON.parse(cookieString);
                }
                var cookieFound = false;
                if(cookieArray.length > 0) {
                    for(var i=0; i<cookieArray.length; i++) {
                        if(cookieArray[i]['k'] == cookieStr) {
                            cookieFound = true;
                            cookieArray[i]['v'] = new Date();
                        }
                    }
                }
                if(!cookieFound) {
                    cookieArray.push({"k": cookieStr, "v": new Date()});
                }
                cookieString = JSON.stringify(cookieArray);
                set_chaty_cookie("chaty_settings", cookieString, "7");
            }

            function set_chaty_cookie(name,value,days) {
                var expires = "";
                if (days) {
                    var date = new Date();
                    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                    expires = "; expires=" + date.toUTCString();
                }
                document.cookie = name + "=" + (value || "") + expires + "; path=/";
            }
            function get_chaty_cookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for(var i=0;i < ca.length;i++) {
                    var c = ca[i];
                    while (c.charAt(0)==' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }

            function show_chaty_widget() {
                save_chaty_settings("cta_widget_status");
                isWidgetEnabled = 1;
                var activeWidget = 0;
                if(jQuery("body").hasClass("chaty-in-desktop")) {
                    activeWidget = jQuery(".chaty-widget-is .chaty-widget-i.is-in-desktop").length;
                } else {
                    activeWidget = jQuery(".chaty-widget-is .chaty-widget-i.is-in-mobile").length;
                }
                var chatyAnimation = is_chaty_settings_expired("chaty-animation");
                if(!chatyAnimation) {
                    chaty_settings.object_settings.animation_class = "";
                }

                if(chaty_settings.object_settings.animation_class != "") {
                    // jQuery(".i-trigger .chaty-widget-i svg").wrap(function() {
                    //     return "<div class='svg'></div>";
                    // });
                    if(chaty_settings.object_settings.animation_class != "sheen") {
                        if (activeWidget > 1) {
                            jQuery(animationClass).addClass("chaty-animation-" + chaty_settings.object_settings.animation_class).addClass("start-now");
                        } else if (activeWidget == 1) {
                            animationClass = ".chaty-main-widget .svg, .chaty-main-widget img, .chaty-main-widget .facustom-icon";
                            jQuery(animationClass).removeClass("chaty-animation-" + chaty_settings.object_settings.animation_class).removeClass("start-now");
                            jQuery(animationClass).addClass("chaty-animation-" + chaty_settings.object_settings.animation_class).addClass("start-now");
                        }
                    } else {
                        animationClass = ".i-trigger .chaty-widget-i .wrap-svg";
                        if(!jQuery(".i-trigger .chaty-widget-i .wrap-svg").length) {
                            // jQuery(".i-trigger .chaty-widget-i svg").wrap(function() {
                            //     return "<div class='wrap-svg'></div>";
                            // });
                        }
                        jQuery(animationClass).removeClass("chaty-animation-sheen").removeClass("start-now");
                        jQuery(animationClass).addClass("chaty-animation-sheen").addClass("start-now");
                    }
                }

                if(chaty_settings.object_settings.display_state == "open") {
                    if(chaty_settings.object_settings.has_close_button == "no") {
                        if(activeWidget > 1) {
                            removeAnimation();
                            jQuery(".chaty-widget").find(".i-trigger-open").removeClass("active_clos");
                            jQuery(".chaty-widget").find(".i-trigger-open").trigger("click");
                            jQuery(".chaty-widget-is").addClass("has-no-close-btn");
                            jQuery(".i-trigger").remove();
                        }
                    } else {
                        if(activeWidget > 1) {
                            removeAnimation();
                            jQuery(".chaty-widget").find(".i-trigger-open").removeClass("active_clos");
                            jQuery(".chaty-widget").find(".i-trigger-open").trigger("click");
                        }
                    }
                }

                if(jQuery(".chaty-widget").hasClass("one_widget")) {
                    jQuery(".chaty-widget").removeClass("chaty-widget-show");
                }
            }

            function removeAnimation() {
                save_chaty_settings("chaty-animation");
                jQuery(".chaty-animation-" + chaty_settings.object_settings.animation_class).removeClass("chaty-animation-" + chaty_settings.object_settings.animation_class).removeClass("start-now");
                jQuery(".start-now").removeClass("start-now");
                chaty_settings.object_settings.animation_class = "";
            }
        }(jQuery)
    }, 12: function (t, e) {
    }
});


function launch_chaty() {
    if(!jQuery(".chaty-widget").hasClass("one_widget")) {
        jQuery(".trigger-chaty-button").trigger("click");
    }
}
function close_chaty() {
    if(jQuery("#chaty-inline-popup").hasClass("active")) {
        jQuery("#chaty-inline-popup .close-chaty-popup").trigger("click");
    }
    if(jQuery(".chaty-widget.chaty-widget-show").length) {
        jQuery(".chaty-widget.chaty-widget-show").each(function(){
            if(jQuery(this).find(".chaty-close-settings").length) {
                jQuery(this).find(".chaty-close-settings").trigger("click");
            }
        });
    }
}