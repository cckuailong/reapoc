/**
 * Version: 2.2.2
 */
var allps, pss;
var wpdm_pass_target = '#pps_z';
String.prototype.wpdm_shuffle = function () {
    var a = this.split(""),
        n = a.length;

    for (var i = n - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var tmp = a[i];
        a[i] = a[j];
        a[j] = tmp;
    }
    return a.join("");
}
String.prototype.wpdm_hash = function () {
    if (Array.prototype.reduce) {
        return this.split("").reduce(function (a, b) {
            a = ((a << 5) - a) + b.charCodeAt(0);
            return a & a
        }, 0);
    }
    var hash = 0;
    if (this.length === 0) return hash;
    for (var i = 0; i < this.length; i++) {
        var character = this.charCodeAt(i);
        hash = ((hash << 5) - hash) + character;
        hash = hash & hash;
    }
    return hash;
}

var WPDM = {

    init: function ($) {

    },

    copy: function ($id) {
        var copyText = document.getElementById($id);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        WPDM.notify('<i class="fa fa-check-double"></i> Copied', 'success', 'top-center', 1000);
    },

    beep: function () {
        if (WPDM.audio == undefined)
            var snd = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
        else
            var snd = new Audio(WPDM.audio);
        snd.play();
    },

    hash: function (str) {
        return str.wpdm_hash();
    },

    uniqueID: function () {
        var uniq = Date.now() + "abcdefghijklmnopqrstuvwxyz_";
        uniq = uniq.wpdm_shuffle();
        uniq = uniq.substring(1, 10);
        return uniq;
    },

    popupWindow: function (url, title, w, h) {
        /* Fixes dual-screen position                         Most browsers      Firefox */
        var dualScreenLeft = typeof window.screenLeft !== 'undefined' ? window.screenLeft : screen.left;
        var dualScreenTop = typeof window.screenTop !== 'undefined' ? window.screenTop : screen.top;

        var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
        var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

        var left = ((width / 2) - (w / 2)) + dualScreenLeft;
        var top = ((height / 2) - (h / 2)) + dualScreenTop;
        var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

        /* Puts focus on the newWindow */
        if (window.focus) {
            newWindow.focus();
        }

        return false;
    },

    html: function (elment, html, _class, _id) {
        _class = _class !== undefined ? _class : '';
        _id = _id !== undefined ? _id : '';
        return "<" + elment + " class='" + _class + "' id='" + _id + "'>" + html + "</" + elment + ">";
    },

    el: function (element, attrs, innerHTML) {
        var el = document.createElement(element);
        el = jQuery(el);
        jQuery.each(attrs, function (name, val) {
            el.attr(name, val);
        })
        if (typeof innerHTML !== 'undefined' && innerHTML !== '')
            el.html(innerHTML);
        return el[0].outerHTML;
    },
    card: function (header, body, footer, id, style) {
        if (typeof id === 'undefined') id = 'card_'+WPDM.uniqueID();
        if (typeof style === 'undefined') style = '';
        header = header !== '' ? WPDM.el("div", {'class': 'card-header'}, header) : '';
        body = WPDM.el("div", {'class': 'card-body'}, body);
        footer = footer !== '' ? WPDM.el("div", {'class': 'card-footer'}, footer) : '';
        return WPDM.el("div", {'class': 'card', id: id, style: style}, header + body + footer);
    },
    fa: function (icon) {
        return WPDM.el("i", {'class': icon});
    },
    bootAlert: function (heading, content, width) {
        var html;
        if (!width) width = 400;
        var modal_id = '__bootModal_' + WPDM.uniqueID();
        html = '<div class="w3eden" id="w3eden' + modal_id + '"><div id="' + modal_id + '" class="modal fade" tabindex="-1" role="dialog">\n' +
            '  <div class="modal-dialog" style="width: ' + width + 'px" role="document">\n' +
            '    <div class="modal-content" style="border-radius: 4px;overflow: hidden">\n' +
            '      <div class="modal-header" style="padding: 12px 15px;background: rgba(0,0,0,0.02);">\n' +
            '        <h4 class="modal-title" style="font-size: 10pt;font-weight: 600;padding: 0;margin: 0;letter-spacing: 0.5px">' + heading + '</h4>\n' +
            '      </div>\n' +
            '      <div class="modal-body fetfont" style="line-height: 1.5;text-transform: unset;font-weight:400;letter-spacing:0.5px;font-size: 12px">\n' +
            '        ' + content + '\n' +
            '      </div>\n' +
            '      <div class="modal-footer" style="padding: 10px 15px">\n' +
            '        <button type="button" class="btn btn-secondary btn-xs" data-target="#' + modal_id + '" data-dismiss="modal">Close</button>\n' +
            '      </div>\n' +
            '    </div>\n' +
            '  </div>\n' +
            '</div></div>';
        jQuery('body').append(html);
        jQuery("#" + modal_id).modal({show: true, backdrop: 'static'});
        return jQuery("#" + modal_id);
    },

    /**
     * Local push notification
     * @param title
     * @param message
     * @param icon
     * @param image
     * @param url
     */
    pushNotify: function (title, message, icon, image, url) {
        var type = 'info';
        if (title.includes('rror') || message.includes('rror')) type = 'error';

        if (!('Notification' in window) || !('ServiceWorkerRegistration' in window)) {
            WPDM.notify("<strong>" + title + "</strong><br/>" + message, type, 'top-right');
            return;
        }

        Notification.requestPermission(function (result) {
            if (result === 'granted') {
                console.log('Notification: ' + result);
                try {
                    var notification = new Notification(title, {
                        body: message,
                        icon: icon ? icon : 'https://cdn1.iconfinder.com/data/icons/hawcons/32/698558-icon-47-note-important-512.png',
                        image: image ? image : ''
                    });
                    if (url) {
                        notification.onclick = function (e) {
                            e.preventDefault();
                            window.open(url, '_blank');
                        };
                    }
                } catch (err) {
                    WPDM.notify("<strong>" + title + "</strong><br/>" + message, type, 'top-right');
                    console.log('Notification API error: ' + err);
                }
            } else {
                WPDM.notify("<strong>" + title + "</strong><br/>" + message, type, 'top-right');
                console.log('Notification: ' + result);
            }
        });

    },


    /**
     * Shows notification
     * @param message
     * @param type
     * @param position
     */
    notify: function (message, type, position, autoclose) {
        var $ = jQuery;
        if (type === undefined || !type) type = 'info';
        if (position === undefined || !position) position = 'top-right';
        if (type === 'danger') type = 'error';
        var notifycont = position.indexOf('#') >= 0 ? position : '#wpdm-notify-' + position;
        if ($(notifycont).length == 0)
            $('body').prepend("<div id='wpdm-notify-" + position + "'></div>");
        var notif = $("<div class='wpdm-notify fetfont wpdm-notify-" + type + "' style='display: none'>" + message + "</div>");
        $(notifycont).append(notif);
        $(notif).fadeIn();
        if (autoclose !== undefined) {
            setTimeout(function () {

                $(notif).animate({
                    opacity: 0
                }, 1000, function () {
                    $(this).slideUp();
                });

            }, autoclose);
        }
        return $(notif);
    },

    /**
     * Shows notification
     * @param message
     * @param type
     * @param position
     */
    floatify: function (html, position) {
        var $ = jQuery;
        if (position === undefined || !position) position = 'top-right';
        var floatifycont = '#wpdm-floatify-' + position;
        if ($(floatifycont).length == 0)
            $('body').prepend("<div class='w3eden' id='wpdm-floatify-" + position + "'></div>");
        var floatify = $("<div class='wpdm-floatify fetfont' style='margin-right: -500px'>" + html + "</div>");
        $(floatifycont).append(floatify);
        $(floatify).animate({marginRight: '0px'});
        return $(floatify);
    },

    blockUI: function (element, xhr) {
        jQuery(element).addClass("blockui");
        if (xhr)
            xhr.addEventListener("load", function () {
                jQuery(element).removeClass("blockui");
            });
    },

    unblockUI: function (element) {
        jQuery(element).removeClass("blockui");
    },

    overlay: function (element, html) {
        var $ = jQuery;
        var overlaycontent = $("<div class='wpdm-overlay-content' style='display: none'>" + html + "<div class='wpdm-overlay-close' style='cursor: pointer'><i class='far fa-times-circle'></i> close</div></div>");
        $(element).addClass('wpdm-overlay').append(overlaycontent);
        $(overlaycontent).fadeIn();
        $('body').on('click', '.wpdm-overlay-close', function () {
            $(overlaycontent).fadeOut(function () {
                $(this).remove();
            });
        });
        return $(overlaycontent);
    },


    confirm: function (heading, content, buttons) {
        var html, $ = jQuery;
        var modal_id = '__boot_popup_' + WPDM.uniqueID();
        $("#w3eden__boot_popup").remove();
        var _buttons = '';
        if (buttons) {
            _buttons = '<div class="modal-footer" style="padding: 8px 15px;">\n';
            $.each(buttons, function (i, button) {
                var id = 'btx_' + i;
                _buttons += "<button id='" + id + "' class='" + button.class + " btn-xs' style='font-size: 10px;padding: 3px 20px;'>" + button.label + "</button> ";
            });
            _buttons += '</div>\n';
        }

        html = '<div class="w3eden" id="w3eden' + modal_id + '"><div id="' + modal_id + '" style="z-index: 9999999 !important;" class="modal fade" tabindex="-1" role="dialog">\n' +
            '  <div class="modal-dialog" role="document" style="max-width: 100%;width: 350px">\n' +
            '    <div class="modal-content" style="border-radius: 3px;overflow: hidden">\n' +
            '      <div class="modal-header" style="padding: 12px 15px;background: #f5f5f5;">\n' +
            '        <h4 class="modal-title" style="font-size: 9pt;font-weight: 500;padding: 0;margin: 0;font-family:var(--wpdm-font), san-serif;letter-spacing: 0.5px">' + heading + '</h4>\n' +
            '      </div>\n' +
            '      <div class="modal-body text-center" style="font-family:var(--wpdm-font), san-serif;letter-spacing: 0.5px;font-size: 10pt;font-weight: 300;padding: 25px;line-height: 1.5">\n' +
            '        ' + content + '\n' +
            '      </div>\n' + _buttons +
            '    </div>\n' +
            '  </div>\n' +
            '</div></div>';
        $('body').append(html);
        $("#" + modal_id).modal('show');
        $.each(buttons, function (i, button) {
            var id = 'btx_' + i;
            $('#' + id).unbind('click');
            $('#' + id).bind('click', function () {
                button.callback.call($("#" + modal_id));
                return false;
            });
        });
        return $("#" + modal_id);
    },
    audioUI: function (audio) {
        var $ = jQuery, song_length, song_length_m, song_length_s;

        var player_html = '<div class="w3eden"><div style="display: none" class="wpdm-audio-player-ui" id="wpdm-audio-player-ui"><div class="card m-2"><div class="card-body text-center"><div class="media"><div class="mr-3 wpdm-audio-control-buttons"><button class="btn btn-primary btn-play" id="wpdm-btn-play"><i class="fa fa-play"></i></button> <button class="btn btn-primary btn-backward" id="wpdm-btn-backward"><i class="fa fa-backward"></i></button> <button class="btn btn-primary btn-forward" id="wpdm-btn-forward"><i class="fa fa-forward"></i></button></div><div class="media-body"><div class="position-relative"><div id="played">00:00</div><div id="mins">00:00</div></div><div class="progress"><div  id="wpdm-audio-progress" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div></div></div><div class="ml-3 wpdm-audio-control-buttons"> <button class="btn btn-info btn-volumctrl" id="wpdm-btn-volumctrl"><i class="fa fa-volume-up"></i></button> <div class="volumctrl"><input type="range" min="0" max="1" step="0.01" value="3" class="p-0" id="wpdm-audio-volume"></div></div></div></div></div></div></div>';

        if (audio.duration !== Infinity) {
            song_length = parseInt(audio.duration);
            song_length_m = parseInt(song_length / 60);
            song_length_s = song_length - (song_length_m * 60);
            song_length_m = song_length_m > 9 ? song_length_m : "0" + song_length_m;
            song_length_s = song_length_s > 9 ? song_length_s : "0" + song_length_s;
        } else {
            song_length_m = "--";
            song_length_s = "--";
            audio.addEventListener("durationchange", function (e) {
                console.log(this.duration);
                if (this.duration !== Infinity) {
                    song_length = parseInt(this.duration);
                    song_length_m = parseInt(song_length / 60);
                    song_length_s = song_length - (song_length_m * 60);
                    song_length_m = song_length_m > 9 ? song_length_m : "0" + song_length_m;
                    song_length_s = song_length_s > 9 ? song_length_s : "0" + song_length_s;
                    $('#mins').html(song_length_m + ":" + song_length_s);
                    next(song_length);
                }
            }, false);
        }

        if ($('#wpdm-audio-player-ui').length === 0) {
            $('body').append(player_html);
            $("#wpdm-audio-player-ui").slideDown();
            $('#mins').html(song_length_m + ":" + song_length_s);
            $('body').on('click', '#wpdm-audio-player-ui .progress', function (e) {
                let value = e.offsetX * 100 / this.clientWidth;
                var played = parseInt(song_length * value / 100);
                var played_m = parseInt(played / 60);
                var played_s = played - (played_m * 60);
                played_m = played_m > 9 ? played_m : "0" + played_m;
                played_s = played_s > 9 ? played_s : "0" + played_s;
                $('#played').html(played_m + ":" + played_s);
                audio.currentTime = (song_length * value) / 100;
                $(this).find('.progress-bar').css('width', value + "%");
                //video.currentTime = duration * (value / 100);
            });
            $('body').on('click', '#wpdm-btn-backward', function () {
                let value = (parseInt($('#wpdm-audio-player-ui .progress-bar').css('width')) / parseInt($('#wpdm-audio-player-ui .progress').css('width'))) * 100 - 10;
                if (value < 0) value = 0;
                var played = parseInt(song_length * value / 100);
                var played_m = parseInt(played / 60);
                var played_s = played - (played_m * 60);
                played_m = played_m > 9 ? played_m : "0" + played_m;
                played_s = played_s > 9 ? played_s : "0" + played_s;
                $('#played').html(played_m + ":" + played_s);
                audio.currentTime = (song_length * value) / 100;
                $('#wpdm-audio-player-ui .progress-bar').css('width', value + "%");
            });
            $('body').on('click', '#wpdm-btn-forward', function () {
                let value = (parseInt($('#wpdm-audio-player-ui .progress-bar').css('width')) / parseInt($('#wpdm-audio-player-ui .progress').css('width'))) * 100 + 10;
                if (value > 100) value = 100;
                var played = parseInt(song_length * value / 100);
                var played_m = parseInt(played / 60);
                var played_s = played - (played_m * 60);
                played_m = played_m > 9 ? played_m : "0" + played_m;
                played_s = played_s > 9 ? played_s : "0" + played_s;
                $('#played').html(played_m + ":" + played_s);
                audio.currentTime = (song_length * value) / 100;
                $('#wpdm-audio-player-ui .progress-bar').css('width', value + "%");
            });
            $('#wpdm-btn-volumctrl').on('click', function () {
                $(this).next('.volumctrl').toggle();
            });
            $('body').on('click', '.btn-play', function () {
                if ($(this).find('.fa').hasClass('fa-play')) {
                    $(this).find('.fa').addClass('fa-pause').removeClass('fa-play');
                    $(this).data('state', 'playing');
                    audio.play();
                } else {
                    $(this).find('.fa').addClass('fa-play').removeClass('fa-pause');
                    $(this).data('state', 'paused');
                    audio.pause();
                }
            });
            $('body').on('change', '#wpdm-audio-volume', function () {
                audio.volume = this.value;
            });

        }
        $('#mins').html(song_length_m + ":" + song_length_s);
        audio.addEventListener("play", function () {
            $('#wpdm-btn-play').find('.fa').addClass('fa-pause').removeClass('fa-play');
        });
        audio.addEventListener("pause", function () {
            $('#wpdm-btn-play').find('.fa').addClass('fa-play').removeClass('fa-pause');
        });
        audio.addEventListener("timeupdate", function (e) {
            var song_length = parseInt(audio.duration);
            var time_now = audio.currentTime;
            var percent = (time_now / song_length) * 100;
            if (percent > 100) percent = 100;
            $('#wpdm-audio-progress').css('width', percent + "%");
            var played = parseInt(time_now);
            var played_m = parseInt(played / 60);
            var played_s = played - (played_m * 60);
            played_m = played_m > 9 ? played_m : "0" + played_m;
            played_s = played_s > 9 ? played_s : "0" + played_s;
            $('#played').html(played_m + ":" + played_s);
        });


    }

};


jQuery(function ($) {

    var $body = $('body');

    $body.on('click', '.wpdm-notify, .wpdm-floatify', function () {
        $(this).animate({
            opacity: 0
        }, 1000, function () {
            $(this).slideUp();
        });
    });

    $body.on('click', '.dismis-on-click', function () {
        $(this).slideUp();
    });

    $body.on('click', '.wpdm-download-link.wpdm-download-locked', function (e) {
        e.preventDefault();
        hideLockFrame();

        var parentWindow = document.createElement("a");
        parentWindow.href = document.referrer.toString();
        var __sep = '?';
        if (wpdm_url.home.indexOf('?') > 0) __sep = '&';
        if (parentWindow.hostname === window.location.hostname || 1)
            $(window.parent.document.body).append("<iframe id='wpdm-lock-frame' style='left:0;top:0;width: 100%;height: 100%;z-index: 999999999;position: fixed;background: rgba(255,255,255,0.4) url(" + wpdm_url.home + "wp-content/plugins/download-manager/assets/images/loader.svg) center center no-repeat;background-size: 80px 80px;border: 0;' src='" + wpdm_url.home + __sep + "__wpdmlo=" + $(this).data('package') + "'></iframe>");
        else
            window.parent.postMessage({
                'task': 'showiframe',
                'iframe': "<iframe id='wpdm-lock-frame' style='left:0;top:0;width: 100%;height: 100%;z-index: 999999999;position: fixed;background: rgba(255,255,255,0.4) url(" + wpdm_url.home + "wp-content/plugins/download-manager/assets/images/loader.svg) center center no-repeat;background-size: 80px 80px;border: 0;' src='" + wpdm_url.home + __sep + "__wpdmlo=" + $(this).data('package') + "'></iframe>"
            }, "*");

    });

    $body.on('click', '.wpdm-download-link.download-on-click[data-downloadurl]', function (e) {
        e.preventDefault();
        if (this.target === '_blank')
            window.open($(this).data('downloadurl'));
        else
            window.location.href = $(this).data('downloadurl');
    });

    $body.on('click', '.__wpdm_playvideo', function (e) {
        e.preventDefault();
        $('#__wpdm_videoplayer').children('source').attr('src', $(this).data('video'));
        console.log('loading...');
        var vid = document.getElementById("__wpdm_videoplayer");
        vid.onloadeddata = function () {
            console.log('loaded....');
        };
        $("#__wpdm_videoplayer").get(0).load();

    });

    $body.on('change', '.terms_checkbox', function (e) {
        if ($(this).is(':checked'))
            $('#wpdm-filelist-' + $(this).data('pid') + ' .btn.inddl, #xfilelist .btn.inddl').removeAttr('disabled');
        else
            $('#wpdm-filelist-' + $(this).data('pid') + ' .btn.inddl, #xfilelist .btn.inddl').attr('disabled', 'disabled');
    });

    $body.on('click', '.wpdm-social-lock', function (e) {

        try {


            _PopupCenter($(this).data('url'), 'Social Lock', 600, 400);

        } catch (e) {
        }

    });

    $body.on('click', '#wpdm-dashboard-sidebar a.list-group-item', function (e) {
        location.href = this.href;
    });

    var $input_group_input = $('.input-group input');
    $input_group_input.on('focus', function () {
        $(this).parent('.input-group').find('.input-group-addon').addClass('input-group-addon-active');
    });
    $input_group_input.on('blur', function () {
        $(this).parent().find('.input-group-addon').removeClass('input-group-addon-active');
    });


    $body.on('click', 'button.btn.inddl', function (e) {
        e.preventDefault();
        var tis = this;
        if ($(this).data('dlurl') !== undefined) {
            location.href = $(this).data('dlurl');
            return;
        }
        $.post(wpdm_rest_url('validate-filepass'), {
            wpdmfileid: $(tis).data('pid'),
            wpdmfile: $(tis).data('file'),
            actioninddlpvr: 1,
            filepass: $($(tis).data('pass')).val()
        }, function (res) {
            if (res.success === true) {
                var dlurl = res.downloadurl;
                $(tis).data('dlurl', dlurl);
                wpdm_boot_popup("Password Verified!", "<div style='padding: 50px;'>Please click following button to start download.<br/><br/><a href='" + dlurl + "' class='btn btn-lg btn-success' target='_blank'>Start Download</a></div>",
                    [{
                        label: 'Close',
                        class: 'btn btn-secondary',
                        callback: function () {
                            this.modal('hide');
                            return false;
                        }
                    }]
                );
            } else {
                alert(res.msg);
            }
        });
    });

    $body.on('click', '.wpdm-indir', function (e) {
        e.preventDefault();
        WPDM.blockUI('#xfilelist');
        $('#xfilelist').load(location.href, {
            action: 'wpdmfilelistcd',
            pid: $(this).data('pid'),
            cd: $(this).data('dir')
        }, function (res) {
            WPDM.unblockUI('#xfilelist');
        });
    });


    $body.on('click', '.role-tabs a', function (e) {
        $('.role-tabs a').removeClass('active');
        $(this).addClass('active');
    });


    $body.on('click', '.btn-wpdm-a2f', function (e) {
        var a2fbtn = $(this);
        $.post(wpdm_url.ajax, {action: 'wpdm_addtofav', pid: $(this).data('package')}, function (res) {
            if (a2fbtn.hasClass('btn-secondary'))
                a2fbtn.removeClass('btn-secondary').addClass('btn-danger').html(a2fbtn.data('rlabel'));
            else
                a2fbtn.removeClass('btn-danger').addClass('btn-secondary').html(a2fbtn.data('alabel'));
        });
    });

    $body.on('click', '.wpdm-btn-play', function (e) {
        e.preventDefault();

        if ($('#wpdm-audio-player').length === 0) {
            var player = document.createElement('audio');
            player.id = 'wpdm-audio-player';
            player.controls = 'controls';
            player.autoplay = 1;
            player.type = 'audio/mpeg';
            $('body').append(player);
        }

        player = $('#wpdm-audio-player');
        var btn = $(this);

        if (btn.data('state') === 'stop' || !btn.data('state')) {
            player.css('display', 'none');
            player.attr('src', $(this).data('song') + "&play=song.mp3");
        }

        if (btn.data('state') === 'playing') {
            $(this).data('state', 'paused');
            player.trigger('pause');
            $(this).html("<i class='fa fa-play'></i>");
            return false;
        }

        if (btn.data('state') === 'paused') {
            $(this).data('state', 'playing');
            player.trigger('play');
            $('.wpdm-btn-play').html("<i class='fa fa-play'></i>");
            $(this).html("<i class='fa fa-pause'></i>");
            return false;
        }


        $('.wpdm-btn-play').data("state", "stop");
        $('.wpdm-btn-play').html("<i class='fa fa-play'></i>");
        btn.html("<i class='fas fa-sun  fa-spin'></i>");
        player.unbind('loadedmetadata');
        player.on('loadedmetadata', function () {
            console.log("Playing " + this.src + ", for: " + this.duration + "seconds.");
            btn.html("<i class='fa fa-pause'></i>");
            btn.data('state', 'playing');
            WPDM.audioUI(this);
        });
        document.getElementById('wpdm-audio-player').onended = function () {
            btn.html("<i class='fa fa-redo'></i>");
            btn.data('state', 'stop');
        }
    });

    $('.wpdm_remove_empty').remove();

    /* Uploading files */
    var file_frame, dfield;

    $body.on('click', '.wpdm-media-upload', function (event) {
        event.preventDefault();
        dfield = $($(this).attr('rel'));

        /* If the media frame already exists, reopen it. */
        if (file_frame) {
            file_frame.open();
            return;
        }

        /* Create the media frame. */
        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data('uploader_title'),
            button: {
                text: $(this).data('uploader_button_text')
            },
            multiple: false  /* Set to true to allow multiple files to be selected */
        });

        /* When an image is selected, run a callback. */
        file_frame.on('select', function () {
            /* We set multiple to false so only get one image from the uploader */
            attachment = file_frame.state().get('selection').first().toJSON();
            dfield.val(attachment.url);

        });

        /* Finally, open the modal */
        file_frame.open();
    });

    $body.on('click', '.btn-image-selector', function (event) {
        event.preventDefault();
        dfield = $($(this).attr('rel'));
        var dfield_h = $($(this).attr('rel') + '_hidden');

        if (file_frame) {
            file_frame.open();
            return;
        }

        file_frame = wp.media.frames.file_frame = wp.media({
            title: $(this).data('uploader_title'),
            button: {
                text: $(this).data('uploader_button_text')
            },
            multiple: false
        });


        file_frame.on('select', function () {

            attachment = file_frame.state().get('selection').first().toJSON();
            dfield.attr('src', attachment.url);
            dfield_h.val(attachment.url);

        });

        file_frame.open();
    });

    $body.on('click', '.pagination.async a, .__wpdm_load_async', function (e) {
        e.preventDefault();
        var _cont = $(this).data('container');
        $(_cont).addClass('blockui');
        $.get(this.href, function (res) {
            $(_cont).html($(res).find(_cont).html());
            $(_cont).removeClass('blockui');
        });
    });

    $body.on("keyup", '.wpdm-pack-search-file', function () {
        var value = $(this).val().toLowerCase();
        var filelist_item = $(this).data('filelist') + " tr";
        $(filelist_item).filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $('.__wpdm_submit_async').on('submit', function (e) {
        e.preventDefault();
        var _cont = $(this).data('container');
        $(_cont).addClass('blockui');
        $(this).ajaxSubmit({
            success: function (response) {
                $(_cont).html($(response).find(_cont).html());
                $(_cont).removeClass('blockui');
            }
        })
    });


    var unlocked = [];

    $('.wpdm-filelist-area[data-termlock]').on('mouseover', function () {

        try {
            if (unlocked[$(this).data('packageid')] === 1) return;
        } catch (e) {

        }
        $('#term-panel-' + $(this).data('packageid')).fadeIn('fast');
    });

    $('.terms_checkbox').on('click', function () {
        if ($(this).is(':checked')) {
            unlocked[$(this).data('pid')] = 1;
            $('#term-panel-' + $(this).data('pid')).fadeOut('fast');
            $('.download_footer_' + $(this).data('pid')).slideDown();
        } else
            $('.download_footer_' + $(this).data('pid')).slideUp();
    });


    $formcontrol = $('.input-wrapper input');
    $formcontrol.on('focus', function () {
        $('.input-wrapper').removeClass('input-focused');
        $(this).parent('.input-wrapper').addClass('input-focused');
    });
    $formcontrol.on('change', function () {
        $('.input-wrapper').removeClass('input-focused');
        $(this).parent('.input-wrapper').addClass('input-focused');
        if ($(this).val() !== '')
            $(this).parent('.input-wrapper').addClass('input-withvalue');
        else
            $(this).parent('.input-wrapper').removeClass('input-withvalue');
    });


});


function _PopupCenter(url, title, w, h) {
    /* Fixes dual-screen position                         Most browsers      Firefox */
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    /* Puts focus on the newWindow */
    if (window.focus) {
        newWindow.focus();
    }

    return false;
}

function generatepass(id) {
    wpdm_pass_target = '#' + id;
    jQuery('#generatepass').modal('show');
}

function hideLockFrame() {
    jQuery('#wpdm-lock-frame').remove();
}

function wpdm_bootModal(heading, content, width) {
    var html;
    if (!width) width = 400;
    jQuery("#w3eden__bootModal").remove();
    html = '<div class="w3eden" id="w3eden__bootModal"><div id="__bootModal" class="modal fade" tabindex="-1" role="dialog">\n' +
        '  <div class="modal-dialog" style="width: ' + width + 'px" role="document">\n' +
        '    <div class="modal-content" style="border-radius: 3px;overflow: hidden">\n' +
        '      <div class="modal-header" style="padding: 12px 15px;background: #f5f5f5;">\n' +
        '        <h4 class="modal-title" style="font-size: 9pt;font-weight: 500;padding: 0;margin: 0;letter-spacing: 0.5px">' + heading + '</h4>\n' +
        '      </div>\n' +
        '      <div class="modal-body fetfont" style="line-height: 1.5;text-transform: unset;font-weight:400;letter-spacing:0.5px;font-size: 12px">\n' +
        '        ' + content + '\n' +
        '      </div>\n' +
        '      <div class="modal-footer" style="padding: 10px 15px">\n' +
        '        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal">Close</button>\n' +
        '      </div>\n' +
        '    </div>\n' +
        '  </div>\n' +
        '</div></div>';
    jQuery('body').append(html);
    jQuery("#__bootModal").modal('show');
}

function wpdm_boot_popup(heading, content, buttons) {
    var html, $ = jQuery;
    $("#w3eden__boot_popup").remove();
    var _buttons = '';
    if (buttons) {
        _buttons = '<div class="modal-footer" style="padding: 8px 15px;">\n';
        $.each(buttons, function (i, button) {
            var id = 'btx_' + i;
            _buttons += "<button id='" + id + "' class='" + button.class + " btn-xs' style='font-size: 10px;padding: 3px 20px;'>" + button.label + "</button> ";
        });
        _buttons += '</div>\n';
    }

    html = '<div class="w3eden" id="w3eden__boot_popup"><div id="__boot_popup" style="z-index: 9999999 !important;" class="modal fade" tabindex="-1" role="dialog">\n' +
        '  <div class="modal-dialog" role="document" style="max-width: 100%;width: 350px">\n' +
        '    <div class="modal-content" style="border-radius: 3px;overflow: hidden">\n' +
        '      <div class="modal-header" style="padding: 12px 15px;background: #f5f5f5;">\n' +
        '        <h4 class="modal-title" style="font-size: 9pt;font-weight: 500;padding: 0;margin: 0;letter-spacing: 0.5px">' + heading + '</h4>\n' +
        '      </div>\n' +
        '      <div class="modal-body text-center" style="letter-spacing: 0.5px;font-size: 10pt;font-weight: 300;padding: 25px;line-height: 1.5">\n' +
        '        ' + content + '\n' +
        '      </div>\n' + _buttons +
        '    </div>\n' +
        '  </div>\n' +
        '</div></div>';
    $('body').append(html);
    $("#__boot_popup").modal('show');
    $.each(buttons, function (i, button) {
        var id = 'btx_' + i;
        $('#' + id).unbind('click');
        $('#' + id).bind('click', function () {
            button.callback.call($("#__boot_popup"));
            return false;
        });
    });
    return $("#__boot_popup");
}

/**
 * Open an url in iframe modal
 * @param url
 * @param closebutton
 * @returns {boolean}
 */
function wpdm_iframe_modal(url, closebutton) {
    var iframe, $ = jQuery;
    if (url === 'close') {
        $('#wpdm_iframe_modal').remove();
        $('#ifcb').remove();
        $('body').removeClass('wpdm-iframe-modal-open');
        return false;
    }
    var closebutton_html = "";
    if (closebutton !== undefined && closebutton === true)
        closebutton_html = "<span id='ifcb' class='w3eden'><a href='#' onclick='return wpdm_iframe_modal(\"close\");' style='border-radius: 0;position: fixed;top: 0;right: 0;z-index: 9999999999 !important;width: 40px;line-height: 40px;padding: 0' class='btn btn-danger'><i class='fas fa-times'></i></a></span>";

    iframe = '<iframe src="' + url + '" style="width: 100%;height: 100%;position: fixed;z-index: 999999999 !important;border: 0;left: 0;top: 0;right: 0;bottom: 0;background: rgba(0,0,0,0.2);display: none;" id="wpdm_iframe_modal"></iframe>' + closebutton_html;
    $('body').append(iframe).addClass('wpdm-iframe-modal-open');
    $('#wpdm_iframe_modal').fadeIn();

}


