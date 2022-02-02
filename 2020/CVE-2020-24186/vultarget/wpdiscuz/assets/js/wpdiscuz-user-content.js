;
jQuery(document).ready(function ($) {
    var refreshAfterDeleting = 0;
    var isNativeAjaxEnabled = parseInt(wpdiscuzAjaxObj.isNativeAjaxEnabled);
    var additionalTab = parseInt(wpdiscuzUCObj.additionalTab);
    $(document).delegate('.wpd-info,.wpd-page-link,.wpd-delete-content,.wpd-user-email-delete-links', 'click', function (e) {
        e.preventDefault();
    });

    $(document).delegate('.wpd-info.wpd-not-clicked', 'click', function (e) {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        var data = new FormData();
        data.append('action', 'wpdGetInfo');
        wpdFullInfo(btn, data);
        return false;
    });

    function wpdFullInfo(btn, data) {
        var icon = $('.fas', btn);
        var oldClass = icon.attr('class');
        icon.removeClass();
        icon.addClass('fas fa-pulse fa-spinner');
        wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                .done(function (response) {
                    btn.addClass('wpd-not-clicked');
                    icon.removeClass();
                    icon.addClass(oldClass);
                    if (response) {
                        $('#wpdUserContentInfo').html(response);
                        $('#wpdUserContentInfo ul.wpd-list .wpd-list-item:first-child').addClass('wpd-active');
                        $('#wpdUserContentInfo div.wpd-content .wpd-content-item:first-child').addClass('wpd-active');

                        if (!($('#wpdUserContentInfo').is(':visible'))) {
                            $('#wpdUserContentInfoAnchor').trigger('click');
                        }
                    }
                });
    }

    $(document).delegate('.wpd-list-item', 'click', function () {
        var relValue = $('input.wpd-rel', this).val();
        $('#wpdUserContentInfo .wpd-list-item').removeClass('wpd-active');
        $('#wpdUserContentInfo .wpd-content-item').removeClass('wpd-active');
        var $this = $(this);
        if (!$('#wpdUserContentInfo #' + relValue).text().length) {
            var data = new FormData();
            data.append('action', $this.attr('data-action'));
            data.append('page', 0);
            $('#wpdUserContentInfo #' + relValue).addClass('wpd-active');
            $('#wpdUserContentInfo #' + relValue).css('text-align', 'center');
            wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, true, data)
                    .done(function (response) {
                        if (response) {
                            $('#wpdUserContentInfo #' + relValue).css('text-align', '');
                            $this.addClass('wpd-active');
                            $('#wpdUserContentInfo #' + relValue).html(response);
                        }
                        $('#wpdiscuz-loading-bar').hide();
                    });
        } else {
            $this.addClass('wpd-active');
            $('#wpdUserContentInfo #' + relValue).addClass('wpd-active');
        }
    });


    $(document).delegate('.wpd-page-link.wpd-not-clicked', 'click', function (e) {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        var goToPage = btn.data('wpd-page');
        var action = $('.wpd-active .wpd-pagination .wpd-action').val();
        var data = new FormData();
        data.append('action', action);
        data.append('page', goToPage);
        wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, true, data)
                .done(function (response) {
                    btn.addClass('wpd-not-clicked');
                    if (response) {
                        $('.wpd-content-item.wpd-active').html(response);
                    }
                    $('#wpdiscuz-loading-bar').hide();
                });
    });

    $(document).delegate('.wpd-delete-content.wpd-not-clicked', 'click', function () {

        var btn = $(this);
        var id = parseInt(btn.data('wpd-content-id'));
        if (!isNaN(id)) {
            var action = btn.data('wpd-delete-action');
            if (action == 'wpdDeleteComment' && !confirm(wpdiscuzUCObj.msgConfirmDeleteComment)) {
                return false;
            } else if (action == 'wpdCancelSubscription' && !confirm(wpdiscuzUCObj.msgConfirmCancelSubscription)) {
                return false;
            } else if (action == 'wpdCancelFollow' && !confirm(wpdiscuzUCObj.msgConfirmCancelFollow)) {
                return false;
            }
            var icon = $('i', btn);
            var oldClass = icon.attr('class');
            var goToPage = $('.wpd-wrapper .wpd-page-number').val();
            var childCount = $('.wpd-content-item.wpd-active').children('.wpd-item').length;
            btn.removeClass('wpd-not-clicked');
            icon.removeClass().addClass('fas fa-pulse fa-spinner');
            if (childCount == 1 && goToPage > 0) {
                goToPage = goToPage - 1;
            }

            var data = new FormData();
            data.append('id', id);
            data.append('page', goToPage);
            data.append('action', action);

            wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                    .done(function (response) {
                        btn.addClass('wpd-not-clicked');
                        icon.removeClass().addClass(oldClass);
                        $('.wpd-content-item.wpd-active').html(response);
                        refreshAfterDeleting = 1;
                    });

        }
    });

    $(document).delegate('[data-lity-close]', 'click', function (e) {
        if ($(e.target).is('[data-lity-close]')) {
            if (refreshAfterDeleting) {
                window.location.reload(true);
            }
        }
    });

    $(document).delegate('.wpd-user-email-delete-links.wpd-not-clicked', 'click', function () {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        $('.wpd-loading', btn).addClass('wpd-show');
        var data = new FormData();
        data.append('action', 'wpdEmailDeleteLinks');
        wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                .done(function (response) {
                    btn.addClass('wpd-not-clicked');
                    $('[data-lity-close]', window.parent.document).trigger('click');
                });
    });

    $(document).delegate('.wpd-user-settings-button.wpd-not-clicked', 'click', function () {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        var guestAction = btn.data('wpd-delete-action');
        console.log(guestAction);
        if (guestAction !== 'deleteCookies') {
            btn.find('.wpd-loading').addClass('wpd-show');
            var data = new FormData();
            data.append('action', 'wpdGuestAction');
            data.append('guestAction', guestAction);
            wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                    .done(function (response) {
                        btn.addClass('wpd-not-clicked');
                        btn.find('.wpd-loading').removeClass('wpd-show');
                        try {
                            var r = $.parseJSON(response);
                            btn.after(r.message);
                            var messageWrap = btn.next('.wpd-guest-action-message');
                            messageWrap.fadeIn(100).fadeOut(7000, function () {
                                messageWrap.remove();
                                if (parseInt(r.code) === 1) {
                                    btn.parent().remove();
                                    guestActionDeleteCookieClass();
                                }
                            });
                        } catch (e) {
                            console.log(e);
                        }
                    });
        } else {
            wpdDeleteAllCookies();
        }
    });

    function guestActionDeleteCookieClass() {
        if (!$('.wpd-delete-all-comments').length && !$('.wpd-delete-all-subscriptions').length) {
            $('.wpd-delete-all-cookies').parent().addClass('wpd-show');
        }
    }

    function wpdDeleteAllCookies() {
        var wpdCookies = document.cookie.split(";");
        for (var i = 0; i < wpdCookies.length; i++) {
            var wpdCookie = wpdCookies[i];
            var eqPos = wpdCookie.indexOf("=");
            var name = eqPos > -1 ? wpdCookie.substr(0, eqPos) : wpdCookie;
            Cookies.remove(name.trim());
        }
        location.reload(true);
    }

});