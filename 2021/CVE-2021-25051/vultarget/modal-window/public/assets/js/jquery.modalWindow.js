/* ========= INFORMATION ============================
	- document:  Wow Modal Windows - The most powerful creator of popups & flyouts!
	- author:    Wow-Company
	- profile:   https://wow-estore.com/item/wow-modal-windows-pro/
	- version:   4.0
==================================================== */

"use strict";

(function ($) {

    $('.modal-window-content .wow-modal-form').on('submit', function (event) {
        event.preventDefault();
        let get_id = this.id;
        let dataform = $(this).serialize();
        let data = 'action=send_modal_window&' + dataform;
        $.post(send_modal_form.ajaxurl, data, function (msg) {
            $('#' + get_id).html(msg);
        });
    });

    // Error checking
    if (!$ || typeof $ === 'undefined') {
        return console.log('[ModalWindow] No jQuery library detected. Load ModalWindow after jQuery has been loaded on the page.');
    }

    $.fn.ModalWindow = function (options) {

        let _default = {
            animation: ['no', 400, 'no', 400], // [animationIn, speedIn, animationOut, speedOut]
            overlay: true, // [Enabled, Background]
            overlay_css: ['rgba(0, 0, 0, 0.7)'], // Overlay CSS [Background]
            modal_css: {},
            content_css: {},
            title_css: {},
            video: [false, false, false], // [Enable, AutoPlay, StopOnClose]
            action: ['load', 0], // [Action, Delay]
            scrolled: [0, 'px'], // [Distance, Unit]
            closeBtn: [false, 0], // [Remove, Delay]
            autoClose: [false, 5], // [Enable, Time]
            closeAction: [false, false], // [Overlay, Esc]
            screenMax: [false, 1024], // [Enable, Screen]
            screenMin: [false, 480], // [Enable, Screen]
            floatBtn: [false, 'wow-modal-button', 'right'], // [Enabled, Class, Position]
            floatBtnAnimation: [false, 'no', 5, 'wow-animated'],// [ Animation Enable, animation, time, animation class]
            cookie: [false, 0, 'cookie-name'], // [Enable cookie, days]
            closeRedirect: [false, "", ""],
            triggers: ['modal-open', 'modal-close', 'close-btn'],
            zindex: '999999',

        };

        return this.each(function () {
            let settings = $.extend(true, {}, _default, options);
            let self = this,
                id = self.id,
                screen = $(window).width();

            let video = videoHosting();

            function videoHosting() {
                let youtube = $(self).find('iframe[src*="youtube.com"]');
                let vimeo = $(self).find('iframe[src*="vimeo.com"]');
                if (youtube.length > 0) {
                    return youtube;
                } else if (vimeo.length > 0) {
                    return vimeo;
                } else {
                    return false;
                }
            }

            styles();
            openPopupActions();
            closePopupActions();

            function styles() {
                if (settings.overlay) {
                    overlayStyle();
                }
                contentStyle();
                popupStyle();
                titleStyle();
                closeStyle();
            }

            function overlayStyle() {
                let overlay = $(self);
                $(overlay).css({
                    'z-index': settings.zindex - 3,
                    'background-color': settings.overlay_css[0],
                });
            }

            function popupStyle() {
                let popup = $(self).children('.wow-modal-window');
                $(popup).css('z-index', settings.zindex - 1);
                $(popup).css(settings.modal_css);

            }

            function closeStyle() {
                let close = $(self).children('.mw-close-btn');
                $(close).css('z-index', settings.zindex);
            }

            function titleStyle() {
                if ($.isEmptyObject(settings.title_css) === false) {
                    let title = $(self).find('.mw-title');
                    $(title).css(settings.title_css);
                }

            }

            function contentStyle() {
                let content = $(self).children('.modal-window-content');
                $(content).css(settings.content_css);
            }

            function floatBtnStyle() {

            }


            function openPopupActions() {
                if (deviceWidth() === true) {
                    if (settings.cookie[0] === true) {
                        let modalCookie = getModalCookie(settings.cookie[2]);
                        if (modalCookie !== undefined) {
                            return;
                        }
                    }

                    let action = settings.action[0];

                    switch (action) {
                        case "hover":
                            hoverOpenAction();
                            break;
                        case "load":
                            delayModalWindow();
                            break;
                        case "close":
                            showModalExit();
                            break;
                        case "scroll":
                            showModalScroll();
                            break;
                        case "rightclick":
                            showModalRightClick();
                            break;
                        case "selectedtext":
                            showModalSelectedText();
                            break;
                    }

                    if (settings.floatBtnAnimation[0] === true) {
                        modalButtonAnimation();
                    }
                    clickOpenAction();
                }
            }


            function clickOpenAction() {
                let trigger = settings.triggers[0];
                let triggers = '#' + trigger + ', .' + trigger + ', a[href$="' + trigger + '"]';
                $(triggers).on('click', function (event) {
                    event.preventDefault();
                    showModalWindow();
                });
            }

            function hoverOpenAction() {
                let trigger = settings.triggers[0];
                let triggers = '#' + trigger + ', .' + trigger + ', a[href$="' + trigger + '"]';
                $(triggers).on('mouseover', function (event) {
                    event.preventDefault();
                    showModalWindow();
                });
            }

            function closePopupActions() {
                clickCloseAction();
                closePopupOverlay();
                closePopupESC();
            }

            function clickCloseAction() {
                let trigger = settings.triggers[1];
                let triggers = '#' + trigger + ', .' + trigger + ', a[href$="' + trigger + '"]' + ', #' + settings.triggers[2];
                $(triggers).on('click', function (event) {
                    event.preventDefault();
                    closeModalWindow();
                });
                $(self).find('.ds-close-popup').on('click', function () {
                    closeModalWindow();
                });
            }

            function closePopupOverlay() {
                if (settings.closeAction[0] === true) {
                    $(self).children(".wow-modal-overclose").on('click', function () {
                        closeModalWindow();
                    });
                }
            }


            function closePopupESC() {
                if (settings.closeAction[1] === true) {
                    $(window).on('keydown', function (event) {
                        if (event.key === 'Escape' || event.key === 'Esc') {
                            closeModalWindow();
                        }
                    });
                }
            }


            function showModalWindow() {
                let speed = parseInt(settings.animation[1]);
                if (settings.overlay === true) {
                    $('html, body').addClass('no-scroll');
                }
                $(self).fadeIn(speed, function () {
                    let pieces = settings.animation[0].split(':');
                    if (pieces == 'no') {
                        $(self).children(".wow-modal-window").fadeIn(speed);
                    } else if (pieces == 'fade') {
                        $(self).children(".wow-modal-window").show('fade', speed);
                    } else {
                        switch (pieces[1]) {
                            case 'direction':
                                $(self).children(".wow-modal-window").show(pieces[0], {
                                    direction: pieces[2]
                                }, speed);
                                break;
                            case 'times':
                                $(self).children(".wow-modal-window").show(pieces[0], {
                                    times: pieces[2]
                                }, speed);
                                break;
                            case 'pieces':
                                $(self).children(".wow-modal-window").show(pieces[0], {
                                    pieces: pieces[2]
                                }, speed);
                                break;
                            case 'size':
                                $(self).children(".wow-modal-window").show(pieces[0], {
                                    size: pieces[2]
                                }, speed);
                                break;
                            case 'percent':
                                $(self).children(".wow-modal-window").show(pieces[0], {
                                    percent: pieces[2]
                                }, speed);
                                break;
                            case 'color':
                                $(self).children(".wow-modal-window").show(pieces[0], {
                                    color: pieces[2]
                                }, speed);
                                break;
                        }
                    }
                });
                videoAutoPlay();
                if (settings.closeBtn[0] !== true) {
                    showCloseButton();
                }
                if (settings.autoClose[0] === true) {
                    autoCloseModal();
                }
            }

            function closeModalWindow() {
                let speed = parseInt(settings.animation[3]);
                let pieces = settings.animation[2].split(':');
                if (pieces == 'no') {
                    $(self).children(".wow-modal-window").fadeOut(speed, closeModalOverlay());
                } else if (pieces == 'fade') {
                    $(self).children(".wow-modal-window").hide('fade', speed, closeModalOverlay());
                } else {
                    switch (pieces[1]) {
                        case 'direction':
                            $(self).children(".wow-modal-window").hide(pieces[0], {
                                direction: pieces[2]
                            }, speed, closeModalOverlay());
                            break;
                        case 'times':
                            $(self).children(".wow-modal-window").hide(pieces[0], {
                                times: pieces[2]
                            }, speed, closeModalOverlay());
                            break;
                        case 'pieces':
                            $(self).children(".wow-modal-window").hide(pieces[0], {
                                pieces: pieces[2]
                            }, speed, closeModalOverlay());
                            break;
                        case 'size':
                            $(self).children(".wow-modal-window").hide(pieces[0], {
                                size: pieces[2]
                            }, speed, closeModalOverlay());
                            break;
                        case 'percent':
                            $(self).children(".wow-modal-window").hide(pieces[0], {
                                percent: pieces[2]
                            }, speed, closeModalOverlay());
                            break;
                        case 'color':
                            $(self).children(".wow-modal-window").hide(pieces[0], {
                                color: pieces[2]
                            }, speed, closeModalOverlay());
                            break;
                    }
                    ;
                }
                setModalCookie();
                redirectOnClose();
            }

            function closeModalOverlay() {
                if (settings.overlay === true) {
                    let speed = parseFloat(settings.animation[3]);
                    $(self).fadeOut(speed);
                    videoStop();
                    $('html, body').removeClass('no-scroll');
                }
            }

            function delayModalWindow() {
                let delay = parseInt(settings.action[1]) * 1000;
                setTimeout(function () {
                    showModalWindow();
                }, delay)
            }

            function autoCloseModal() {
                let timer = parseInt(settings.autoClose[1]) * 1000;
                setTimeout(function () {
                    closeModalWindow();
                }, timer);
            }

            function showCloseButton() {
                let timer = parseInt(settings.closeBtn[1]) * 1000;
                setTimeout(function () {
                    $(self).find('.mw-close-btn').show();
                }, timer);
            }


            function deviceWidth() {
                if (settings.screenMax[0] === true && settings.screenMax[1] < screen) {
                    return false;
                }

                if (settings.screenMin[0] === true && settings.screenMin[1] > screen) {
                    return false;
                }
                return true;

            }

            function showModalExit() {
                $(document).on('mouseleave', function (e) {
                    if (e.clientY < 0) {
                        showModalWindow();
                        $(document).off('mouseleave');
                    }
                });

            }

            function showModalScroll() {
                $(document).on('scroll', function () {
                    let scrollTop = $(window).scrollTop();
                    let docHeight = $(document).height();
                    let winHeight = $(window).height();
                    if (settings.scrolled[1] === 'px') {
                        let scrollY = $(this).scrollTop();
                        let distance = (docHeight - winHeight) * parseInt(settings.scrolled[0]) / 100;
                        if (scrollY >= distance) {
                            delayModalWindow();
                            $(document).off('scroll');
                        }
                    } else {
                        let scrollPercent = (scrollTop) / (docHeight - winHeight);
                        let scrollPercentRounded = Math.round(scrollPercent * 100);
                        if (scrollPercentRounded >= parseInt(settings.scrolled[0])) {
                            delayModalWindow();
                            $(document).off('scroll');
                        }
                    }

                });

            }

            function showModalRightClick() {
                $(document).on('contextmenu', function () {
                    delayModalWindow();
                    return false;
                });
            }

            function showModalSelectedText() {
                $(document).on('mouseup', function (e) {
                    let selected_text = ((window.getSelection && window.getSelection()) || (document.getSelection && document.getSelection()) || (document.selection && document.selection.createRange && document.selection.createRange().text));
                    if (selected_text.toString().length > 2) {
                        delayModalWindow();
                    }
                });

            }

            function redirectOnClose() {
                if (settings.closeRedirect[0]) {
                    let redirectUrl = settings.closeRedirect[1];
                    if (redirectUrl !== '' && redirectUrl.indexOf('http') > -1) {
                        window.open(redirectUrl, settings.closeRedirect[2]);
                    }
                }
            }

            // Youtube video auto play
            function videoAutoPlay() {
                if ((settings.video[0] === true) && (settings.video[1] === true) && video) {
                    let videoURL = $(video).attr('src');
                    $(video).attr('src', videoURL + '?autoplay=1');
                }
            }

            // Youtube video stop
            function videoStop() {
                if ((settings.video[0] === true) && (settings.video[2] === true) && video) {
                    let videoURL = $(video).attr('src');
                    videoURL = videoURL.split('?')[0];
                    $(video).attr('src', videoURL + '?autoplay=0');
                }
            }


            function modalButtonAnimation() {
                $('.' + settings.floatBtn[1]).addClass(settings.floatBtnAnimation[3] + ' wow-infinite ' + settings.floatBtnAnimation[1] + settings.floatBtn[2]);
                let time = parseFloat(settings.floatBtnAnimation[2]) * 1000;
                setTimeout(function () {
                    $('.' + settings.floatBtn[4]).removeClass(settings.floatBtnAnimation[3] + ' wow-infinite ' + settings.floatBtnAnimation[1] + settings.floatBtn[2]);
                    modalButtonPause();
                }, time)
            }

            function modalButtonPause() {
                let time = parseFloat(settings.floatBtnAnimation[2]) * 1000;
                setTimeout(function () {
                    modalButtonAnimation();
                }, time)
            }


            function setModalCookie() {
                if (settings.cookie[0] !== true) {
                    return;
                }
                let days = parseFloat(settings.cookie[1]);
                let CookieDate = new Date();
                CookieDate.setTime(CookieDate.getTime() + (days * 24 * 60 * 60 * 1000));
                if (days > 0) {
                    document.cookie = settings.cookie[2] + '=yes; path=/; expires=' + CookieDate.toGMTString();
                } else {
                    document.cookie = settings.cookie[2] + '=yes; path=/;';
                }
            }


            function getModalCookie(name) {
                let matches = document.cookie.match(new RegExp(
                    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                ));
                return matches ? decodeURIComponent(matches[1]) : undefined;
            }

        });
    }

}(jQuery));