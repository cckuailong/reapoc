jQuery(document).ready(function ($) {

    var deactivateUrl = '';

    $(document).delegate('#the-list tr[data-plugin="wpdiscuz/class.WpdiscuzCore.php"] .deactivate a', 'click', function (e) {
        e.preventDefault();
        $('#wpdDeactivationReasonAnchor').trigger('click');
        deactivateUrl = $(this).attr('href');
        return false;
    });

    var parentItem = $('.wpd-deactivation-reason:checked').parents('.wpd-deactivation-reason-item');
    $('.wpd-deactivation-reason-more-info').slideUp(500);
    $('.wpd-deactivation-reason-more-info', parentItem).slideDown(500);

    $(document).delegate('.wpd-deactivation-reason', 'change', function (e) {
        $('.wpd-deactivation-reason-more-info').slideUp(500);
        var parentItem = $(this).parents('.wpd-deactivation-reason-item');
        $('.wpd-deactivation-reason-more-info', parentItem).slideDown(500);
    });

    $(document).delegate('.wpd-deactivate', 'click', function (e) {
        if (isChecked($(this))) {
            var formData = '';
            if ($(this).hasClass('wpd-submit')) {
                var checkedItem = $('.wpd-deactivation-reason:checked');
                var parentItem = checkedItem.parents('.wpd-deactivation-reason-item');
                var reasonDesc = $('.dr_more_info', parentItem);
                var receiveEmail = $('[name=deactivation_feedback_receive_email]', parentItem).attr('checked');
                var receiverEmail = $('[name=deactivation_feedback_email]', parentItem);
                var isValid = true;

                if (reasonDesc.length && reasonDesc.is(':visible')) {
                    var attr = reasonDesc.attr('required');
                    if (typeof attr !== typeof undefined && attr !== false) {
                        if ($.trim(reasonDesc.val().length) == 0) {
                            isValid = false;
                        }
                    }
                }

                if (isValid) {
                    formData = 'deactivation_reason=' + checkedItem.val();
                    if (reasonDesc.length && $.trim(reasonDesc.val().length) > 0) {
                        formData += '&deactivation_reason_desc=' + reasonDesc.val();
                    }
                    if (receiveEmail && receiverEmail.length && $.trim(receiverEmail.val().length) > 0) {
                        formData += '&deactivation_feedback_email=' + receiverEmail.val();
                    }
                    $('.wpd-loading', this).toggleClass('wpdiscuz-hidden');
                } else {
                    alert(deactivationObj.msgReasonDescRequired);
                    return false;
                }
            } else {
                formData = 'never_show=1';
            }
            
            if (formData) {
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'wpdDeactivate',
                        deactivateData: formData
                    }
                }).done(function (response) {
                    try {
                        var r = $.parseJSON(response);
                        var locHref = deactivateUrl ? deactivationObj.adminUrl + deactivateUrl : location.href;
                        if (r.code == 'dismiss_and_deactivate') {
                            setTimeout(function () {
                                location.href = locHref;
                            }, 100);
                        } else if (r.code == 'send_and_deactivate') {
                            $('.wpd-deactivation-reason-form, .wpdiscuz-thankyou').toggleClass('wpdiscuz-hidden');
                            $('#wpdDeactivationReason').css({'width': '400px'});
                            setTimeout(function () {
                                location.href = locHref;
                            }, 1000);
                        }
                    } catch (e) {
                        console.log(e);
                    }
                });
            }
        } else {
            alert(deactivationObj.msgReasonRequired);
        }
    });

    function isChecked(btn) {
        if (btn.hasClass('wpd-submit')) {
            var elem = $('.wpd-deactivation-reason-form input[name="deactivation_reason"]');
            for (var i = 0; i < elem.length; i++) {
                if (elem[i].type == 'radio' && elem[i].checked) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    function isValid() {
        if ($('.dr_more_info').is(':visible')) {
            return $.trim($('.dr_more_info:visible').length);
        } else {
            return true;
        }
    }

});