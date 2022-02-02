jQuery(document).ready(function ($) {
    /* global Cookies  */
    /* global Chart  */
    var doingAjax = false;
    if (location.href.indexOf('wpdiscuz_options_page') >= 0) {
        $('.wpdiscuz-color-picker').colorPicker();
    }

    $('#wc_share_button_fb').click(function () {
        if ($(this).is(':checked')) {
            $('#wpc-fb-api-cont').attr('style', '');
        } else {
            $('#wpc-fb-api-cont').attr('style', 'display:none');
        }
    });
    $('#wpdiscuz-reset-all-options').click(function (e) {
        if (!confirm(wpdiscuzObj.msgConfirmResetOptions)) {
            e.preventDefault();
            return false;
        }
    });
    $('#wpdiscuz-reset-options').click(function (e) {
        if (!confirm(wpdiscuzObj.msgConfirmResetTabOptions)) {
            e.preventDefault();
            return false;
        }
    });
    $('#wpdiscuz-remove-votes').click(function (e) {
        if (!confirm(wpdiscuzObj.msgConfirmRemoveVotes)) {
            e.preventDefault();
            return false;
        }
    });
    $('#wpdiscuz-reset-phrases').click(function (e) {
        if (!confirm(wpdiscuzObj.msgConfirmResetPhrases)) {
            e.preventDefault();
            return false;
        }
    });
    $('#wpdiscuz-purge-gravatars-cache').click(function (e) {
        if (!confirm(wpdiscuzObj.msgConfirmPurgeGravatarsCache)) {
            e.preventDefault();
            return false;
        }
    });
    $(document).delegate('.wpd_stick_btn', 'click', function (e) {
        var btn = $(this);
        $('.fas', btn).removeClass('fa-thumbtack');
        $('.fas', btn).addClass('fa-pulse fa-spinner');
        var commentId = btn.data('comment');
        var postId = btn.data('post');
        var data = new FormData();
        data.append('action', 'wpdStickComment');
        data.append('commentId', commentId);
        data.append('postId', postId);
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            contentType: false,
            processData: false,
        }).done(function (r) {
            if (typeof r === 'object') {
                $('.fas', btn).removeClass('fa-pulse fa-spinner');
                $('.fas', btn).addClass('fa-thumbtack');
                if (r.success) {
                    $('.wpd_stick_text', btn).text(r.data);
                } else {
                    console.log(r.data);
                }
            } else {
                console.log(r);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
        e.preventDefault();
        return false;
    });
    $(document).delegate('.wpd_close_btn', 'click', function (e) {
        var btn = $(this);
        $('.fas', btn).removeClass('fa-lock fa-unlock');
        $('.fas', btn).addClass('fa-spinner fa-pulse');
        var commentId = btn.data('comment');
        var postId = btn.data('post');
        var data = new FormData();
        data.append('action', 'wpdCloseThread');
        data.append('commentId', commentId);
        data.append('postId', postId);
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            contentType: false,
            processData: false,
        }).done(function (r) {
            if (typeof r === 'object') {
                $('.fas', btn).removeClass('fa-pulse fa-spinner');
                if (r.success) {
                    $('.wpd_close_text', btn).text(r.data.data);
                    $('.fas', btn).removeClass('fa-lock fa-unlock');
                    $('.fas', btn).addClass(r.data.icon);
                } else {
                    console.log(r.data);
                }
            } else {
                console.log(r);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
        e.preventDefault();
        return false;
    });
    $(document).delegate('.import-stcr', 'click', function (e) {
        e.preventDefault();
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
        importSTCR(btn);
    });
    function importSTCR(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {stcrData: data, 'action': 'wpdImportSTCR'}
        }).done(function (response) {
            try {
                var resp = JSON.parse(response);
                $('.stcr-step').val(resp.step);
                if (resp.progress < 100) {
                    importSTCR(btn);
                } else {
                    btn.removeAttr('disabled');
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                }


                if (resp.progress <= 3) {
                    $('.stcr-import-progress').text(3 + '%');
                } else {
                    if (resp.progress < 100) {
                        $('.stcr-import-progress').text(resp.progress + '%');
                    } else {
                        $('.stcr-import-progress').css({'color': '#10b493'});
                        $('.stcr-import-progress').text(resp.progress + '% Done');
                        $('.stcr-step').val(0);
                        doingAjax = false;
                    }
                }

            } catch (e) {
                console.log(e);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }

    $(document).delegate('.import-lstc', 'click', function (e) {
        e.preventDefault();
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
        importLSTC(btn);
    });
    function importLSTC(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {lstcData: data, 'action': 'wpdImportLSTC'}
        }).done(function (response) {
            try {
                var resp = JSON.parse(response);
                $('.lstc-step').val(resp.step);
                if (resp.progress < 100) {
                    importLSTC(btn);
                } else {
                    btn.removeAttr('disabled');
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                }


                if (resp.progress <= 3) {
                    $('.lstc-import-progress').text(3 + '%');
                } else {
                    if (resp.progress < 100) {
                        $('.lstc-import-progress').text(resp.progress + '%');
                    } else {
                        $('.lstc-import-progress').css({'color': '#10b493'});
                        $('.lstc-import-progress').text(resp.progress + '% Done');
                        $('.lstc-step').val(0);
                        doingAjax = false;
                    }
                }

            } catch (e) {
                console.log(e);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }

    $('.vote-regenerate-step').val(0);
    $(document).delegate('.regenerate-vote-metas', 'click', function (e) {
        e.preventDefault();
        if ($('.vote-regenerate-start-id').val() >= 0 && parseInt($('.vote-regenerate-limit').val()) > 0) {
            var btn = $(this);
            btn.attr('disabled', 'disabled');
            $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
            updateVoteMetas(btn);
        }

    });
    function updateVoteMetas(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {voteRegenerateData: data, action: 'wpdRegenerateVoteMetas'}
        }).done(function (response) {
            try {
                var resp = JSON.parse(response);
                $('.vote-regenerate-step').val(resp.step);
                $('.vote-regenerate-start-id').val(resp.startId);
                if (resp.progress < 100) {
                    updateVoteMetas(btn);
                } else {
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                }

                if (resp.progress <= 1) {
                    $('.vote-regenerate-import-progress').text(1 + '%');
                } else {
                    if (resp.progress < 100) {
                        $('.vote-regenerate-import-progress').text(resp.progress + '%');
                    } else {
                        $('.vote-regenerate-import-progress').css({'color': '#10b493'});
                        $('.vote-regenerate-import-progress').text(resp.progress + '% Done');
                        $('.vote-regenerate-count').val(0);
                        $('.vote-regenerate-step').val(0);
                        $('.vote-regenerate-start-id').val(0);
                        doingAjax = false;
                        setTimeout(function () {
                            location.reload(true);
                        }, 2000);
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }

    $('.closed-regenerate-step').val(0);
    $(document).delegate('.regenerate-closed-comments', 'click', function (e) {
        e.preventDefault();
        if ($('.closed-regenerate-start-id').val() >= 0 && parseInt($('.closed-regenerate-limit').val()) > 0) {
            var btn = $(this);
            btn.attr('disabled', 'disabled');
            $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
            updateClosedComments(btn);
        }

    });
    function updateClosedComments(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {closedRegenerateData: data, action: 'wpdRegenerateClosedComments'}
        }).done(function (response) {
            try {
                var resp = JSON.parse(response);
                $('.closed-regenerate-step').val(resp.step);
                $('.closed-regenerate-start-id').val(resp.startId);
                if (resp.progress < 100) {
                    updateClosedComments(btn);
                } else {
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                }

                if (resp.progress <= 1) {
                    $('.closed-regenerate-import-progress').text(1 + '%');
                } else {
                    if (resp.progress < 100) {
                        $('.closed-regenerate-import-progress').text(resp.progress + '%');
                    } else {
                        $('.closed-regenerate-import-progress').css({'color': '#10b493'});
                        $('.closed-regenerate-import-progress').text(resp.progress + '% Done');
                        $('.closed-regenerate-count').val(0);
                        $('.closed-regenerate-step').val(0);
                        $('.closed-regenerate-start-id').val(0);
                        doingAjax = false;
                        setTimeout(function () {
                            location.reload(true);
                        }, 2000);
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }

    $('.regenerate-vote-data-step').val(0);
    $(document).delegate('.regenerate-vote-data', 'click', function (e) {
        e.preventDefault();
        if ($('.regenerate-vote-data-start-id').val() >= 0 && parseInt($('.regenerate-vote-data-limit').val()) > 0) {
            var btn = $(this);
            btn.attr('disabled', 'disabled');
            $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
            regenerateVoteData(btn);
        }

    });
    function regenerateVoteData(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {regenerateVoteData: data, action: 'wpdRegenerateVoteData'}
        }).done(function (response) {
            try {
                var resp = JSON.parse(response);
                $('.regenerate-vote-data-step').val(resp.step);
                $('.regenerate-vote-data-start-id').val(resp.startId);
                if (resp.progress < 100) {
                    regenerateVoteData(btn);
                } else {
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                }

                if (resp.progress <= 1) {
                    $('.regenerate-vote-data-import-progress').text(1 + '%');
                } else {
                    if (resp.progress < 100) {
                        $('.regenerate-vote-data-import-progress').text(resp.progress + '%');
                    } else {
                        $('.regenerate-vote-data-import-progress').css({'color': '#10b493'});
                        $('.regenerate-vote-data-import-progress').text(resp.progress + '% Done');
                        $('.regenerate-vote-data-count').val(0);
                        $('.regenerate-vote-data-step').val(0);
                        $('.regenerate-vote-data-start-id').val(0);
                        doingAjax = false;
                        setTimeout(function () {
                            location.reload(true);
                        }, 2000);
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }

    $(document).delegate('.sync-commenter-data', 'click', function (e) {
        e.preventDefault();
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
        syncCommenterData(btn);
    });
    function syncCommenterData(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {syncCommenterData: data, action: 'wpdSyncCommenterData'}
        }).done(function (r) {
            if (typeof r === 'object') {
                if (r.success) {
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                    $('.sync-commenter-import-progress').css({'color': '#10b493'});
                    $('.sync-commenter-import-progress').text('Done');
                    doingAjax = false;
                    setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                } else {
                    console.log('Something is wrong');
                }
            } else {
                console.log(r);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }

    $('.rebuild-ratings-step').val(0);
    $(document).delegate('.rebuild-ratings', 'click', function (e) {
        e.preventDefault();
        if ($('.rebuild-ratings-start-id').val() >= 0) {
            var btn = $(this);
            btn.attr('disabled', 'disabled');
            $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
            rebuildRatings(btn);
        }

    });
    function rebuildRatings(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {rebuildRatings: data, action: 'wpdRebuildRatings'}
        }).done(function (response) {
            try {
                var resp = JSON.parse(response);
                $('.rebuild-ratings-step').val(resp.step);
                $('.rebuild-ratings-start-id').val(resp.startId);
                if (resp.progress < 100) {
                    rebuildRatings(btn);
                } else {
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                }

                if (resp.progress <= 1) {
                    $('.rebuild-ratings-import-progress').text(1 + '%');
                } else {
                    if (resp.progress < 100) {
                        $('.rebuild-ratings-import-progress').text(resp.progress + '%');
                    } else {
                        $('.rebuild-ratings-import-progress').css({'color': '#10b493'});
                        $('.rebuild-ratings-import-progress').text(resp.progress + '% Done');
                        $('.rebuild-ratings-count').val(0);
                        $('.rebuild-ratings-step').val(0);
                        $('.rebuild-ratings-start-id').val(0);
                        doingAjax = false;
                    }
                }
            } catch (e) {
                console.log(e);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }
    $(document).delegate('.fix-tables', 'click', function (e) {
        e.preventDefault();
        var btn = $(this);
        btn.attr('disabled', 'disabled');
        $('.fas', btn).addClass('fa-pulse fa-spinner').removeClass('wc-hidden');
        fixTables(btn);
    });
    function fixTables(btn) {
        doingAjax = true;
        var data = btn.parents('.wc-form').serialize();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {fixTables: data, action: 'wpdFixTables'}
        }).done(function (r) {
            if (typeof r === 'object') {
                if (r.success) {
                    $('.fas', btn).removeClass('fa-pulse fa-spinner').addClass('wc-hidden');
                    $('.fix-tables-import-progress').css({'color': '#10b493'});
                    $('.fix-tables-import-progress').text('Done');
                    doingAjax = false;
                } else {
                    console.log('Something is wrong');
                }
            } else {
                console.log(r);
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        });
    }

    $('#wpd-disable-addons').click(function () {
        location.href = $('#wpd-disable-addons-action').val();
    });
    window.onbeforeunload = confirmExit;
    function confirmExit() {
        if (doingAjax) {
            return "You have attempted to leave this page while background task is running. Are you sure?";
        }
    }

    $(".wmu-lightbox").colorbox({
        maxHeight: "95%",
        maxWidth: "95%",
        rel: "wmu-lightbox",
        fixed: true
    });

    //========================= SETTINGS_LOGIN =====================//
    var showActivityTab = $('#showActivityTab').attr('checked');
    var showSubscriptionsTab = $('#showSubscriptionsTab').attr('checked');
    var showFollowsTab = $('#showFollowsTab').attr('checked');
    if (showActivityTab || showFollowsTab || showSubscriptionsTab) {
        $('#showUserSettingsButton').attr('checked', true);
    }
    $('#showUserSettingsButton').change(function () {
        var checked = $(this).attr('checked');
        if (checked) {
            if (showActivityTab || showFollowsTab || showSubscriptionsTab) {
                if (showActivityTab) {
                    $('#showActivityTab').attr('checked', true);
                }
                if (showFollowsTab) {
                    $('#showFollowsTab').attr('checked', true);
                }
                if (showSubscriptionsTab) {
                    $('#showSubscriptionsTab').attr('checked', true);
                }
            } else {
                $('#showActivityTab').attr('checked', true);
                $('#showSubscriptionsTab').attr('checked', true);
                $('#showFollowsTab').attr('checked', true);
            }
        } else {
            $('#showActivityTab').attr('checked', false);
            $('#showSubscriptionsTab').attr('checked', false);
            $('#showFollowsTab').attr('checked', false);
        }
    });
    $('#showActivityTab').change(function () {
        var checked = $(this).attr('checked');
        if (checked || ($('#showSubscriptionsTab').attr('checked') || $('#showFollowsTab').attr('checked'))) {
            $('#showUserSettingsButton').attr('checked', true);
        } else {
            $('#showUserSettingsButton').attr('checked', false);
        }
    });
    $('#showSubscriptionsTab').change(function () {
        var checked = $(this).attr('checked');
        if (checked || ($('#showActivityTab').attr('checked') || $('#showFollowsTab').attr('checked'))) {
            $('#showUserSettingsButton').attr('checked', true);
        } else {
            $('#showUserSettingsButton').attr('checked', false);
        }
    });
    $('#showFollowsTab').change(function () {
        var checked = $(this).attr('checked');
        if (checked || ($('#showActivityTab').attr('checked') || $('#showSubscriptionsTab').attr('checked'))) {
            $('#showUserSettingsButton').attr('checked', true);
        } else {
            $('#showUserSettingsButton').attr('checked', false);
        }
    });
    $('#enableProfileURLs').change(function () {
        if (!$(this).prop('checked')) {
            $('#websiteAsProfileUrl').prop('checked', false);
        }
    });
    //========================= /SETTINGS_LOGIN =====================//
    //========================= SETTINGS_RECAPTCHA =====================//
    $(document).delegate('#siteKey, #secretKey, #v3_sitekey, #v3_secretkey, #useV3', 'change', function () {
        if ((!$('#useV3').attr('checked') && $('#siteKey').val() && $('#secretKey').val()) || ($('#useV3').attr('checked') && $('#v3_sitekey').val() && $('#v3_secretkey').val())) {
            $('#showForGuests').attr('checked', true);
        }
    });
    //========================= /SETTINGS_RECAPTCHA =====================//
    //========================= DASHBOARD =====================//
    if ($('#wpdiscuz-news').length) {
        setTimeout(function () {
            if (parseInt(Cookies.get('wpd_show_news')) === 0) {
                $('#wpdiscuz-news').hide();
            }
        }, 1000);
    }
    $(document).delegate('.wpd-toggle-news', 'click', function () {
        var dash = $(this).children('.dashicons');
        if (dash.hasClass('dashicons-arrow-down')) {
            $('#wpdiscuz-news').show();
            Cookies.set('wpd_show_news', 1, {expires: 365, path: location.href});
        } else {
            $('#wpdiscuz-news').hide();
            Cookies.set('wpd_show_news', 0, {expires: 365, path: location.href});
        }
        dash.toggleClass('dashicons-arrow-down dashicons-arrow-up')
    });
    if ($('.wpd-stat-brief-top').length) {
        wpd_stat_brief();
    }
    if ($('.wpd-stat-subs .wpd-box-toggle .dashicons-arrow-up.wpd_not_clicked').length) {
        var el = $('.wpd-stat-subs .wpd-box-toggle .dashicons-arrow-up.wpd_not_clicked');
        el.removeClass('wpd_not_clicked');
        var body = el.parents('.wpd-box').children('.wpd-box-body');
        wpd_stat_subs(el, body);
    }
    if ($('.wpd-stat-graph .wpd-box-toggle .dashicons-arrow-up.wpd_not_clicked').length) {
        var el = $('.wpd-stat-graph .wpd-box-toggle .dashicons-arrow-up.wpd_not_clicked');
        el.removeClass('wpd_not_clicked');
        var body = el.parents('.wpd-box').children('.wpd-box-body');
        wpd_stat_graph(el, body);
    }
    if ($('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-up.wpd_not_clicked').length) {
        var el = $('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-up.wpd_not_clicked');
        el.removeClass('wpd_not_clicked');
        var body = el.parents('.wpd-box').children('.wpd-box-body');
        wpd_stat_user(el, body);
    }
    $(document).delegate('.wpd-box-toggle .dashicons-arrow-up.wpd_not_clicked, .wpd-box-toggle .dashicons-arrow-down.wpd_not_clicked', 'click', function () {
        var el = $(this);
        el.removeClass('wpd_not_clicked');
        var show = el.hasClass('dashicons-arrow-down') ? 1 : 0;
        var parent = el.parents('.wpd-box');
        var action = parent.data('box');
        Cookies.set(action, show, {expires: 365, path: location.href});
        $(this).toggleClass('dashicons-arrow-up dashicons-arrow-down');
        var body = parent.children('.wpd-box-body');
        body.toggle();
        if (show) {
            window[action](el, body);
        } else {
            if (action === 'wpd_stat_graph') {
                parent.find('.wpd-box-toggle .dashicons-admin-generic, .wpd-box-info').hide();
            } else if (action === 'wpd_stat_user') {
                parent.find('.wpd-box-toggle .dashicons-arrow-left, .wpd-box-toggle .dashicons-arrow-right').hide();
            }
            body.empty();
            el.addClass('wpd_not_clicked');
        }
    });
    $(document).delegate('.wpd-stat-graph .wpd-box-toggle .dashicons-admin-generic', 'click', function () {
        var sibling = $(this).siblings('.wpd-graph-tools');
        sibling.css({display: sibling.is(':visible') ? 'none' : 'flex'});
    });
    $('body').click(function () {
        $('.wpd-stat-graph .wpd-box-toggle .wpd-graph-tools, #wpd-opt-search-results').hide();
    });
    $(document).delegate('.wpd-stat-graph .wpd-box-toggle .wpd-graph-tools span.wpd_not_clicked', 'click', function () {
        var el = $(this);
        el.removeClass('wpd_not_clicked');
        Cookies.set('wpd_stat_graph_interval', el.data('interval'), {expires: 365, path: location.href});
        wpd_stat_graph(el, el.parents('.wpd-box').children('.wpd-box-body'));
    });
    $(document).delegate('.wpd-stat-user .wpd-sort-field', 'click', function () {
        var el = $(this);
        var order = el.hasClass('wpd-active') && el.children('.dashicons').hasClass('dashicons-arrow-down-alt2') ? 'asc' : 'desc';
        Cookies.set('wpd_stat_user_orderby', el.data('orderby'), {expires: 365, path: location.href});
        Cookies.set('wpd_stat_user_order', order, {expires: 365, path: location.href});
        wpdStatUserPage = 1;
        wpd_stat_user(el, el.parents('.wpd-box').children('.wpd-box-body'));
    });
    $(document).delegate('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-left.wpd_not_clicked, .wpd-stat-user .wpd-box-toggle .dashicons-arrow-right.wpd_not_clicked', 'click', function () {
        var el = $(this);
        if (el.hasClass('dashicons-arrow-left')) {
            if (wpdStatUserPage > 1) {
                wpdStatUserPage--;
            } else {
                $(this).css('visibility', 'hidden');
                return;
            }
        } else {
            wpdStatUserPage++;
        }
        wpd_stat_user(el, el.parents('.wpd-box').children('.wpd-box-body'));
    });
    //========================= /DASHBOARD =====================//
    //========================= SETTING SEARCH =====================//
    var searchAjax = null;
    window.onhashchange = scrollToOption;
    scrollToOption();
    function scrollToOption() {
        var matches = location.href.match(/#wpdOpt\-(\w+)/);
        if (matches !== null) {
            var wpdopt = $('[data-wpd-opt=' + matches[1] + ']');
            $('html, body').animate({
                scrollTop: wpdopt.offset().top - 32
            }, 500, function () {
                wpdopt.css('background-color', '#ebebeb');
                setTimeout(function () {
                    wpdopt.css('background-color', 'transparent');
                }, 500);
            });
        }
    }
    $(document).delegate('#wpd-opt-search-field, #wpd-opt-search-results', 'keydown', function (e) {
        var keycode = e.which;
        if (keycode == 27) {
            $('#wpd-opt-search-results').hide();
            $('#wpd-opt-search-field').focus();
            e.preventDefault();
            e.stopPropagation();
        } else if (keycode == 35 && !$('#wpd-opt-search-field').is(':focus')) {
            e.preventDefault();
            e.stopPropagation();
            var a = $('#wpd-opt-search-results > a');
            $(a[a.length - 1]).focus();
        } else if (keycode == 36 && !$('#wpd-opt-search-field').is(':focus')) {
            e.preventDefault();
            e.stopPropagation();
            var a = $('#wpd-opt-search-results > a');
            $(a[0]).focus();
        } else if (keycode == 38 || keycode == 40) {
            e.preventDefault();
            e.stopPropagation();
            var a = $('#wpd-opt-search-results > a');
            var focus_status = false;
            a.each(function (key, val) {
                if ($(val).is(':focus') || $(val).is(':hover')) {
                    if (keycode == 40) {
                        $(a[key + 1]).focus();
                        focus_status = true;
                        return false;
                    } else if (keycode == 38) {
                        $(a[key - 1]).focus();
                        focus_status = true;
                        return false;
                    }
                }
            });
            if (!focus_status) {
                $(a[0]).focus()
            }
        } else if (keycode == 13) {
            $('#wpd-opt-search-results > a:focus').click();
        } else {
            $('#wpd-opt-search-field').focus();
        }
    });
    $(document).delegate('#wpd-opt-search-field', 'input', function () {
        var s = $(this).val();
        s = s.trim();
        if (s.length > 2) {
            if (searchAjax) {
                searchAjax.abort();
            }
            searchAjax = $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'searchOption',
                    s: s
                }
            });
            searchAjax.done(function (r) {
                if (r) {
                    $('#wpd-opt-search-results').html(r).show();
                } else {
                    $('#wpd-opt-search-results').html('').hide();
                }
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            });
        }
    });
    //========================= /SETTING SEARCH =====================//
    //========================= SETTINGS MENU =====================//
    $('.wpd-setbar .wpd-menu-head .dashicons-arrow-down, .wpd-setbar .wpd-menu-head .dashicons-arrow-up').click(function () {
        var $this = $(this);
        var up = $this.hasClass('dashicons-arrow-up');
        $this.toggleClass('dashicons-arrow-down dashicons-arrow-up');
        if (up) {
            $this.parents('.wpd-menu-group').find('li:not(.wpd-menu-head)').hide();
        } else {
            $this.parents('.wpd-menu-group').find('li:not(.wpd-menu-head)').show();
        }
    });
    //========================= /SETTINGS MENU =====================//   
    //========================= TOOLBAR OPTIONS =====================//
    $('.wpd-editor-toolbar .wpd-editor-button').click(function () {
        if ($(this).hasClass('wpd-enabled')) {
            $(this).removeClass('wpd-enabled');
            $(this).addClass('wpd-disabled');
            $('#' + $(this).attr('id') + '-button').val(0);
        } else {
            $(this).removeClass('wpd-disabled');
            $(this).addClass('wpd-enabled');
            $('#' + $(this).attr('id') + '-button').val(1);
        }
    });
    $('.wpd-editor-toolbar #wpdeb_disable').click(function () {
        $('.wpd-editor-toolbar .wpd-editor-button').removeClass('wpd-enabled');
        $('.wpd-editor-toolbar .wpd-editor-button').addClass('wpd-disabled');
        $('.wpd-editor-toolbar .wpd-editor-button').next('input').val(0);
        $(this).hide();
        $('.wpd-editor-toolbar #wpdeb_enable').show();
    });
    $('.wpd-editor-toolbar #wpdeb_enable').click(function () {
        $('.wpd-editor-toolbar .wpd-editor-button').addClass('wpd-enabled');
        $('.wpd-editor-toolbar .wpd-editor-button').removeClass('wpd-disabled');
        $('.wpd-editor-toolbar .wpd-editor-button').next('input').val(1);
        $(this).hide();
        $('.wpd-editor-toolbar #wpdeb_disable').show();
    });
    //========================= /TOOLBAR OPTIONS =====================//


    /* TOOLS PAGE ACCORDION */
    if (location.href.indexOf('wpdiscuz_tools_page') >= 0) {
        var supportsHash = false;
        if ("onhashchange" in window) {
            supportsHash = true;
        }

        if (supportsHash) {
            window.addEventListener("hashchange", wpdtoolOnhashchange, false);
        }
        // TODO check if browser supports hashchange

        var accordionMatches = location.href.match(/#wpdtool\-(.+)/);
        if (accordionMatches != null) {
            var item = $('.wpdtool-accordion-title[data-wpdtool-selector="wpdtool-' + accordionMatches[1] + '"');
            toolsAccordion(item);
        }

        $('.wpdtool-accordion-title').click(function () {
            var item = $(this);

            if (!supportsHash) {
                toolsAccordion(item);
            }

            var selector = item.attr("data-wpdtool-selector");
            accordionMatches = location.href.match(/#wpdtool\-(.+)/);
            var accordionNewMatches = selector.match(/wpdtool\-(.+)/);
            if (accordionNewMatches != null && accordionMatches != null) {
                if (accordionMatches[1] == accordionNewMatches[1]) {
                    location.href = location.href.replace(accordionNewMatches[0], "");
                    if (supportsHash) {
                        toolsAccordion(item);
                    }
                } else {
                    location.href = location.href.replace(accordionMatches[1], accordionNewMatches[1]);
                }
            } else {
                location.href = location.href.indexOf("#") >= 0 ? location.href + selector : location.href + "#" + selector;
            }
        });

        function toolsAccordion(item) {
            if (item != null) {
                $(item).parent().siblings('.wpdtool-accordion-item').removeClass('wpdtool-accordion-current');
                $(item).parent().siblings('.wpdtool-accordion-item').find('.wpdtool-accordion-content').slideUp(0);
                $(item).siblings('.wpdtool-accordion-content').slideToggle(0);
                $(item).parent().toggleClass('wpdtool-accordion-current');
            }
        }

        function wpdtoolOnhashchange() {
            var accordionMatches = location.href.match(/#wpdtool\-(.+)/);
            if (accordionMatches != null) {
                item = $('.wpdtool-accordion-title[data-wpdtool-selector="wpdtool-' + accordionMatches[1] + '"');
                toolsAccordion(item);
            }
        }
    }
    /* TOOLS PAGE ACCORDION */

});
//========================= DASHBOARD =====================//
var wpdSpinner = '<div class="wpd-spinner"><span class="spinner"></span></div>';
var wpdStatUserPage = 1;
function wpd_stat_brief() {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'wpd_stat_brief'
        }
    }).done(function (r) {
        if (r.success) {
            jQuery('.wpd-stat-brief .wpd-stat-brief-all strong').text(r.data.all);
            jQuery('.wpd-stat-brief .wpd-stat-brief-inline strong').text(r.data.inline);
            jQuery('.wpd-stat-brief .wpd-stat-brief-threads strong').text(r.data.threads);
            jQuery('.wpd-stat-brief .wpd-stat-brief-replies strong').text(r.data.replies);
            jQuery('.wpd-stat-brief .wpd-stat-brief-users strong').text(r.data.users);
            jQuery('.wpd-stat-brief .wpd-stat-brief-guests strong').text(r.data.guests);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
        el.addClass('wpd_not_clicked');
    });
}

function wpd_stat_subs(el, body) {
    body.html(wpdSpinner);
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'wpd_stat_subs'
        }
    }).done(function (r) {
        body.html(r);
        el.addClass('wpd_not_clicked');
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
        el.addClass('wpd_not_clicked');
    });
}

function wpd_stat_graph(el, body) {
    body.html(wpdSpinner);
    var interval = Cookies.get('wpd_stat_graph_interval');
    if (!interval) {
        interval = 'today';
    }
    jQuery('.wpd-stat-graph .wpd-box-toggle .wpd-graph-tools span').removeClass('wpd_tool_active');
    jQuery('[data-interval=' + interval + ']').addClass('wpd_tool_active');
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'wpd_stat_graph',
            interval: interval
        }
    }).done(function (r) {
        if (r.success) {
            body.html(r.data.el);
            body.parent().find('.wpd-box-toggle .dashicons-admin-generic, .wpd-box-info').show();
            var wpdchart = document.getElementById('wpdChart');
            wpdchart.height = 250;
            Chart.defaults.global.defaultFontSize = 9;
            Chart.defaults.global.defaultFontColor = '#333';
            new Chart(wpdchart, {
                type: 'bar',
                data: {
                    labels: r.data.labels,
                    datasets: [
                        {
                            label: '',
                            data: r.data.all,
                            backgroundColor: '#46C08F',
                            borderColor: '#46C08F'
                        },
                        {
                            label: '',
                            data: r.data.inline,
                            backgroundColor: '#0498F9',
                            borderColor: '#0498F9'
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        labels: {
                            usePointStyle: true,
                        }
                    },
                    scales: {
                        yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                    }
                }
            });
        } else {
            body.html("Something is wrong");
        }
        el.addClass('wpd_not_clicked');
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
        el.addClass('wpd_not_clicked');
    });
}

function wpd_stat_user(el, body) {
    body.html(wpdSpinner);
    jQuery('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-left, .wpd-stat-user .wpd-box-toggle .dashicons-arrow-right').removeClass('wpd_not_clicked');
    var orderby = Cookies.get('wpd_stat_user_orderby');
    if (!orderby) {
        orderby = 'comments';
    }
    var order = Cookies.get('wpd_stat_user_order');
    if (!order) {
        order = 'desc';
    }
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            action: 'wpd_stat_user',
            orderby: orderby,
            order: order,
            page: wpdStatUserPage
        }
    }).done(function (r) {
        if (r.success) {
            body.html(r.data.body);
            body.parent().find('.wpd-box-toggle .dashicons-arrow-left, .wpd-box-toggle .dashicons-arrow-right').show();
            el.addClass('wpd_not_clicked');
            jQuery('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-left, .wpd-stat-user .wpd-box-toggle .dashicons-arrow-right').addClass('wpd_not_clicked');
            if (wpdStatUserPage > 1) {
                jQuery('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-left').css('visibility', 'visible');
            } else {
                jQuery('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-left').css('visibility', 'hidden');
            }
            if (r.data.more) {
                jQuery('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-right').css('visibility', 'visible');
            } else {
                jQuery('.wpd-stat-user .wpd-box-toggle .dashicons-arrow-right').css('visibility', 'hidden');
            }
        } else {
            body.html(r.data);
        }
    }).fail(function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown);
        el.addClass('wpd_not_clicked');
    });
}
//========================= /DASHBOARD =====================//