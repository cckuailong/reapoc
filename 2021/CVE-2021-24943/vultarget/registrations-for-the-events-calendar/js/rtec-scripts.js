jQuery(document).ready(function($) {
    if (typeof window.rtecInit === 'undefined') {
        window.rtecInit = function () {
            $('.rtec').addClass('rtec-initialized');
            $('.rtec-js-show').show();
            $('.rtec-js-hide').hide();

            // move the form for backwards compatibility
            if ($('#rtec-js-move-flag').length
                || $('.rtec-js-placement').length
                || $('footer').find('.rtec').length
                || $('footer').find('.rtec-outer-wrap').length
                || $('footer').find('.rtec-success-message').length) {

                var $moveEl = $('#rtec-js-move-flag'),
                    rtecLocation = typeof $('.rtec-outer-wrap').attr('data-location') !== 'undefined' ? $('.rtec-outer-wrap').attr('data-location') : false;
                if ( ! rtecLocation ) {
                    rtecLocation = typeof $('#rtec-js-move-flag').attr('data-location') !== 'undefined' ? $('#rtec-js-move-flag').attr('data-location') : 'tribe_events_single_event_before_the_content';
                }
                if ($('.rtec-outer-wrap.rtec-js-placement').length) {
                    $moveEl = $('.rtec-outer-wrap.rtec-js-placement');
                } else if ($('.rtec').length) {
                    $moveEl = $('.rtec');
                } else if ($('.rtec-success-message').length) {
                    $moveEl = $('.rtec-success-message');
                }

                // move the element that needs to be moved jQuery('.tribe-events-single-event-description')
                if ($('.tribe-events-single-event-description').length) {
                    if (rtecLocation === 'tribe_events_single_event_after_the_content') {
                        $('.tribe-events-single-event-description').first().after($moveEl);
                    } else {
                        $('.tribe-events-single-event-description').first().before($moveEl);
                    }
                } else if ($('.tribe-events-single-section.tribe-events-event-meta').length
                    && rtecLocation === 'tribe_events_single_event_after_the_content') {
                    $('.tribe-events-single-section.tribe-events-event-meta').first().before($moveEl);
                } else if ($('.tribe-events-schedule').length) {
                    if (rtecLocation === 'tribe_events_single_event_after_the_content') {
                        if ($('.tribe-block.tribe-block__event-price').prev('p').length) {
                            $('.tribe-block.tribe-block__event-price').prev('p').after($moveEl);
                        } else if ($('.tribe-block.tribe-block__organizer__details').prev('p').length) {
                            $('.tribe-block.tribe-block__organizer__details').prev('p').after($moveEl);
                        } else if ($('.tribe-block.tribe-block__venue').prev('p').length) {
                            $('.tribe-block.tribe-block__venue').prev('p').after($moveEl);
                        } else {
                            $('.tribe-events-schedule').first().after($moveEl);
                        }
                    } else {
                        $('.tribe-events-schedule').first().after($moveEl);
                    }
                } else if ($('.tribe-events-single .tribe_events').length) {
                    $('.tribe-events-single .tribe_events').first().prepend($moveEl);
                } else if ($('.tribe-events-single h1').length) {
                    $('.tribe-events-single h1').first().after($moveEl);
                } else if ($('.tribe-events-single h2').length) {
                    $('.tribe-events-single h2').first().after($moveEl);
                }

                if ($('.rtec-login-wrap').length) {
                    $('.rtec-login-wrap').each(function() {
                        var $context = $(this).closest($('.tribe-events-single'));
                        $context.find('.rtec-success-message').first().after($('.rtec-login-wrap').closest('.rtec-event-meta'));
                    });
                }
            }

            $('.rtec').each(function(index) {
                var $rtec = $(this);

                rtecCheckHoneypot($rtec);

                if ($(this).closest('.rtec-outer-wrap').length && $(this).closest('.rtec-outer-wrap').find('.rtec-already-registered-options').length) {
                    var $outerWrap = $(this).closest('.rtec-outer-wrap'),
                        $form = $(this).closest('.rtec-outer-wrap').find('.rtec-already-registered-options form'),
                        sendUnregisterText = '';
                    $form.find('input[type=submit]').each(function() {
                        sendUnregisterText = $(this).val();
                    });
                    $form.on('submit',function(event) {
                        event.preventDefault();

                        var action = 'rtec_send_unregister_link';

                        $form.after($('.rtec-spinner').clone());
                        $form.next('.rtec-spinner').show();
                        $form.fadeTo(500,.1);
                        $form.find('input[type=submit]').prop('disabled',true).css('opacity', .1);

                        var submittedData = {
                            'action': action,
                            'event_id': $outerWrap.find('.rtec').attr('data-event'),
                            'email': $form.find('input[name=rtec-visitor_email]').val()
                        };

                        $.ajax({
                            url: rtec.ajaxUrl,
                            type: 'post',
                            data: submittedData,
                            success: function (data) {
                                $form.next('.rtec-spinner').remove();
                                $form.fadeTo(500,1);
                                $form.find('input[type=submit]').prop('disabled',false).css('opacity', 1);
                                if (data.trim().indexOf('{') > -1) {
                                    var response = JSON.parse(data.trim());

                                    if (typeof response.success !== 'undefined') {
                                        $form.replaceWith(response.success);
                                    } else if (typeof response.error !== 'undefined') {
                                        var $formField = $form.find('input[name=rtec-visitor_email]').closest('.rtec-input-wrapper');
                                        if (!$formField.find('.rtec-error-message').length) {
                                            $formField.append('<p class="rtec-error-message" role="alert">'+response.error+'</p>');
                                        }
                                        $form.find('input[name=rtec-visitor_email]').attr('aria-invalid','true');
                                    }
                                }

                            }
                        }); // ajax
                    });
                    $form.find('input[type=submit]').on('click',function() {
                        $("input[type=submit]", $(this).parents("form")).prop("clicked",false);
                        $(this).prop("clicked", "true");
                    });

                }

                //rtec-outer-wrap
            });

            $('.rtec-form-toggle-button').on('click', function() {
                $rtecEl = $(this).closest('.rtec');
                var useModal = typeof $rtecEl.attr('data-modal') !== 'undefined';
                if ( useModal ) {
                    $rtecEl.wrap('<div class="rtec-modal-placeholder"></div>');
                    $rtecEl.find('.rtec-form-wrapper').show();
                    $('.rtec-modal-content').empty().prepend($rtecEl);
                    rtecToggleModal();
                } else {
                    $rtecEl.find('.rtec-toggle-on-click').toggle('slow');
                    if ($(this).hasClass('tribe-bar-filters-open')) {
                        $(this).removeClass('tribe-bar-filters-open');
                    } else {
                        $(this).addClass('tribe-bar-filters-open');
                    }
                }

            });

            function rtecCheckHoneypot($rtecEl) {
                if ($rtecEl.find('input[name=rtec_user_comments]').length &&
                    $rtecEl.find('input[name=rtec_user_comments]').val() !== '') {
                    if (!$rtecEl.find('.rtec-honeypot-clear-wrap').length) {
                        var errorText = 'I am not a robot';
                        if (typeof rtec.translations !== 'undefined') {
                            errorText = rtec.translations.honeypotClear;
                        }
                        $rtecEl.find('#rtec-form .rtec-form-field').last().after('<div class="rtec-honeypot-clear-wrap">' +
                            '<button class="rtec-honeypot-clear rtec-error">'+errorText+'</button>' +
                            '</div>');
                        $rtecEl.find('.rtec-honeypot-clear').on('click',function() {
                            $(this).closest('.rtec-error').remove();
                            $rtecEl.find('input[name=rtec_user_comments]').val('');
                        });
                    }

                }
            }

            var RtecForm = {

                validClass: 'rtec-valid',

                invalidClass: 'rtec-error',

                showErrorMessage: function (formEl) {
                    var $formField = formEl.closest($('.rtec-input-wrapper'));
                    if (!$formField.find('.rtec-error-message').length) {
                        $formField.append('<p class="rtec-error-message" role="alert">' + formEl.closest($('.rtec-form-field')).attr('data-rtec-error-message') + '</p>');
                    }
                    formEl.attr('aria-invalid', 'true');
                },

                removeErrorMessage: function (formEl) {
                    formEl.closest($('.rtec-input-wrapper')).find('.rtec-error-message').remove();
                    formEl.attr('aria-invalid', 'false');
                },

                addScreenReaderError: function () {
                    $('#rtec .rtec-form-wrapper').prepend('<div class="rtec-screen-reader rtec-screen-reader-error" role="alert" aria-live="assertive">There were errors with your submission. Please try again.</div>');
                },

                validateLength: function (formEl, min, max) {
                    if (formEl.val().length > max || formEl.val().length < min) {
                        if (formEl.hasClass(RtecForm.validClass)) {
                            formEl.removeClass(RtecForm.validClass);
                        }
                        formEl.addClass(RtecForm.invalidClass);
                        RtecForm.showErrorMessage(formEl);
                    } else {
                        if (formEl.hasClass(RtecForm.invalidClass)) {
                            formEl.removeClass(RtecForm.invalidClass);
                        }
                        formEl.addClass(RtecForm.validClass);
                        RtecForm.removeErrorMessage(formEl);
                    }
                },

                validateOption: function ($input) {

                    var eqTest = false;

                    if (!$input.find('option').length) {
                        if ($input.is(':checked')) {
                            eqTest = true;
                        }
                        var formEl = $input.closest('.rtec-input-wrapper');
                    } else {
                        if ($input.find('option:selected').val() !== '') {
                            eqTest = true;
                        }
                        var formEl = $input;
                    }

                    if (eqTest) {
                        if (formEl.hasClass(RtecForm.invalidClass)) {
                            formEl.removeClass(RtecForm.invalidClass);
                        }
                        formEl.addClass(RtecForm.validClass);
                        RtecForm.removeErrorMessage(formEl);
                    } else {
                        if (formEl.hasClass(RtecForm.validClass)) {
                            formEl.removeClass(RtecForm.validClass);
                        }
                        formEl.addClass(RtecForm.invalidClass);
                        RtecForm.showErrorMessage(formEl);
                    }
                },

                validateRecapthca: function (val, formEl) {
                    if (val.length > 0) {
                        if (formEl.hasClass(RtecForm.invalidClass)) {
                            formEl.removeClass(RtecForm.invalidClass);
                        }
                        formEl.addClass(RtecForm.validClass);
                        RtecForm.removeErrorMessage(formEl);
                    } else {
                        if (formEl.hasClass(RtecForm.validClass)) {
                            formEl.removeClass(RtecForm.validClass);
                        }
                        formEl.addClass(RtecForm.invalidClass);
                        RtecForm.showErrorMessage(formEl);
                    }
                },

                isValidEmail: function (val) {
                    var regEx = /[^\s@]+@[^\s@]+\.[^\s@]+/;

                    return regEx.test(val.trim());
                },

                validateEmail: function (formEl) {
                    if (RtecForm.isValidEmail(formEl.val()) && !formEl.closest('form').find('#rtec-error-duplicate').length) {
                        if (formEl.hasClass(RtecForm.invalidClass)) {
                            formEl.removeClass(RtecForm.invalidClass);
                        }
                        formEl.addClass(RtecForm.validClass);
                        RtecForm.removeErrorMessage(formEl);
                    } else {
                        if (formEl.hasClass(RtecForm.validClass)) {
                            formEl.removeClass(RtecForm.validClass);
                        }
                        formEl.addClass(RtecForm.invalidClass);
                        RtecForm.showErrorMessage(formEl);
                    }
                },

                validateCount: function (formEl, validCountArr) {

                    var strippedNumString = formEl.val().replace(/\D/g, ''),
                        formElCount = strippedNumString.length,
                        validCountNumbers = validCountArr.map(function (x) {
                            return parseInt(x);
                        }),
                        countTest = validCountNumbers.indexOf(formElCount);

                    // if the valid counts is blank, allow any entry that contains at least one number
                    if (validCountArr[0] === '') {
                        countTest = formElCount - 1;
                    }

                    if (countTest !== -1) {
                        if (formEl.hasClass(RtecForm.invalidClass)) {
                            formEl.removeClass(RtecForm.invalidClass);
                        }
                        formEl.addClass(RtecForm.validClass);
                        RtecForm.removeErrorMessage(formEl);
                    } else {
                        if (formEl.hasClass(RtecForm.validClass)) {
                            formEl.removeClass(RtecForm.validClass);
                        }
                        formEl.addClass(RtecForm.invalidClass);
                        RtecForm.showErrorMessage(formEl);
                    }
                },

                validateSum: function (formEl, val1, val2) {

                    var eqTest = (parseInt(val1) === parseInt(val2));

                    if (eqTest) {
                        if (formEl.hasClass(RtecForm.invalidClass)) {
                            formEl.removeClass(RtecForm.invalidClass);
                        }
                        formEl.addClass(RtecForm.validClass);
                        RtecForm.removeErrorMessage(formEl);
                    } else {
                        if (formEl.hasClass(RtecForm.validClass)) {
                            formEl.removeClass(RtecForm.validClass);
                        }
                        formEl.addClass(RtecForm.invalidClass);
                        RtecForm.showErrorMessage(formEl);
                    }
                },

                enableSubmitButton: function (_callback, $context) {
                    if (_callback()) {
                        $context.find('input[name=rtec_submit]').prop('disabled',false).css('opacity', 1);
                    }
                },

                isDuplicateEmail: function (email, eventID, $context) {
                    var $emailEl = $context.find('input[name=rtec_email]'),
                        $spinnerImg = $('.rtec-spinner').length ? $('.rtec-spinner').html() : '',
                        $spinner = '<span class="rtec-email-spinner">' + $spinnerImg + '</span>';

                    $emailEl.closest('div').append($spinner);

                    var submittedData = {
                        'action': 'rtec_registrant_check_for_duplicate_email',
                        'event_id': eventID,
                        'email': email.trim()
                    };

                    $.ajax({
                        url: rtec.ajaxUrl,
                        type: 'post',
                        data: submittedData,
                        success: function (data) {
                            var json = {
                                'approved' : true,
                                'message' : ''
                            };
                            if (data.trim().indexOf('{') === 0) {
                                json = JSON.parse(data);
                            } else if (data.indexOf('<p class=') > -1) {
                                json = {
                                    'approved' : false,
                                    'message' : data
                                };
                            }

                            if (!json.approved) {
                                RtecForm.removeErrorMessage($emailEl);
                                if ($emailEl.hasClass(RtecForm.validClass)) {
                                    $emailEl.removeClass(RtecForm.validClass);
                                }
                                $emailEl.addClass(RtecForm.invalidClass);
                                $emailEl.closest($('.rtec-input-wrapper')).append(json.message);
                            } else {
                                if ($emailEl.hasClass(RtecForm.invalidClass)) {
                                    $emailEl.removeClass(RtecForm.invalidClass);
                                }
                                $emailEl.addClass(RtecForm.validClass);
                                RtecForm.removeErrorMessage($emailEl);
                            }

                            $context.find('input[name=rtec_submit]').prop('disabled',false).css('opacity', 1);
                            $context.find('.rtec-email-spinner').remove();
                        }
                    }); // ajax

                }

            };

            if (typeof rtec.checkForDuplicates !== 'undefined' && rtec.checkForDuplicates === '1') {
                var $rtecEmailField = $('input[name=rtec_email]'),
                    typingTimer,
                    doneTypingInterval = 1500;
                $rtecEmailField.on('input', function () {
                    var $this = $(this),
                        $context = $this.closest('.rtec');
                    $context.find('input[name=rtec_submit]').prop('disabled', true).css('opacity', '.5');
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(function () {
                        var $eventID = $context.find('input[name=rtec_event_id]').val();
                        if (RtecForm.isValidEmail($this.val())) {
                            RtecForm.isDuplicateEmail($this.val(), $eventID, $context);
                        }
                    }, doneTypingInterval);
                });
                $rtecEmailField.each(function () {
                    var $this = $(this),
                        $context = $this.closest('.rtec'),
                        $eventID = $context.find('input[name=rtec_event_id]').val();
                    if (RtecForm.isValidEmail($this.val())) {
                        RtecForm.isDuplicateEmail($this.val(), $eventID, $context);
                    }
                });
            }

            $('.rtec-form').on('submit',function (event) {
                event.preventDefault();

                var $form = $(this),
                    $rtecEl = $(this).closest('.rtec');
                rtecCheckHoneypot($rtecEl);
                $rtecEl.find('input[name=rtec_submit]').prop('disabled', true);

                if ($rtecEl.find('.rtec-screen-reader-error').length) {
                    $rtecEl.find('.rtec-screen-reader-error').remove();
                }

                var required = [];

                $rtecEl.find('#rtec-form .rtec-form-field').each(function () {
                    var $input = $(this).find('.rtec-field-input');
                    if ($input.attr('aria-required') == 'true') {
                        if ($input.attr('data-rtec-valid-email') == 'true') {
                            RtecForm.validateEmail($input);
                        } else if (typeof $input.attr('data-rtec-valid-count') == 'string') {
                            RtecForm.validateCount($input, $input.attr('data-rtec-valid-count').replace(' ', '').split(','), $input.attr('data-rtec-count-what'));
                        } else if (typeof $input.attr('data-rtec-recaptcha') == 'string') {
                            RtecForm.validateSum($input, $input.val(), $input.closest('.rtec-form').find('input[name=' + $input.attr('name') + '_sum]').val());
                        } else if ($input.attr('data-rtec-valid-options') == 'true') {
                            RtecForm.validateOption($input);
                        } else {
                            RtecForm.validateLength($input, 1, 10000);
                        }
                    } else if ($(this).find('.g-recaptcha').length) {
                        var recaptchaVal = typeof grecaptcha !== 'undefined' ? grecaptcha.getResponse() : '';
                        RtecForm.validateRecapthca(recaptchaVal, $(this));
                    }
                });

                if (!$rtecEl.find('.rtec-error').length && !$rtecEl.find('#rtec-error-duplicate').length) {
                    $rtecEl.find('.rtec-spinner').show();
                    $rtecEl.find('.rtec-form-wrapper #rtec-form, .rtec-form-wrapper p').fadeTo(500, .1);
                    $rtecEl.find('#rtec-form-toggle-button').css('visibility', 'hidden');

                    var submittedData = {};

                    $rtecEl.find('#rtec-form :input').each(function () {
                        var name = $(this).attr('name');
                        if ($(this).attr('type') === 'checkbox') {
                            if ($(this).is(':checked')) {
                                submittedData[name] = $(this).val();
                            }
                        } else {
                            submittedData[name] = $(this).val().trim();
                        }
                    });

                    submittedData['action'] = 'rtec_process_form_submission';
                    if ($('input[name=lang]').length) {
                        submittedData['lang'] = $('input[name=lang]').val();
                    }

                    $.ajax({
                        url: rtec.ajaxUrl,
                        type: 'post',
                        data: submittedData,
                        success: function (data) {
                            $rtecEl.find('.rtec-spinner, #rtec-form-toggle-button').remove();
                            $rtecEl.find('.rtec-form-wrapper').slideUp(400,function() {
                                $rtecEl.find('.rtec-form-wrapper').remove();
                            });
                            $('html, body').animate({
                                scrollTop: $rtecEl.offset().top - 200
                            }, 750);

                            if (data !== '') {
                                $rtecEl.prepend(data);
                                $('.rtec-already-registered-reveal, .rtec-already-registered-options').remove();
                                if (typeof rtecAfterSubmit === 'function') {
                                    rtecAfterSubmit();
                                }
                                var evt = $.Event('rtecsubmissionajax');
                                evt.el = $rtecEl;

                                $(window).trigger(evt);
                            } else {
                                console.log('no data');
                            }

                        }
                    }); // ajax
                } else { // if not .rtec-error
                    $rtecEl.find('input[name=rtec_submit]').prop('disabled',false).css('opacity', 1);
                    RtecForm.addScreenReaderError();

                    if ($('.rtec-error-message').length) {
                        $('html, body').animate({
                            scrollTop: $rtecEl.find('.rtec-error-message').first().closest('.rtec-input-wrapper').offset().top - 200
                        }, 750);
                    }

                } // if not .rtec-error
            }); // on rtec-form submit

            // hide options initially
            var $rtecReveal = $('.rtec-already-registered-reveal'),
                $rtecOptions = $('.rtec-already-registered-options.rtec-is-visitor'),
                $rtecOptionsRemove = $('.rtec-already-registered-js-remove');
            $rtecReveal.show();
            $rtecOptions.hide();
            $rtecOptionsRemove.remove();
            $rtecReveal.each(function() {
                $(this).on('click',function () {
                    var $thisOptions = $(this).closest('.rtec-outer-wrap').find('.rtec-already-registered-options.rtec-is-visitor');
                    if ($thisOptions.is(':visible')) {
                        $thisOptions.slideUp();
                    } else {
                        $thisOptions.slideDown();
                    }
                });
            });

            function rtecToggleModal() {
                $('body').toggleClass('rtec-modal-is-open');

                $('.rtec-modal-backdrop, .rtec-media-modal-close').on('click',function () {
                    var $modalRtec = $('.rtec-modal-content').find('.rtec');
                    $modalRtec.find('.rtec-form-wrapper').hide();
                    $('.rtec-modal-placeholder').replaceWith($modalRtec);
                    $('.rtec-register-button').show();

                    $('body').removeClass('rtec-modal-is-open');
                });
            }

            $(window).on('rtecsubmissionajax', function (event) {
                var $rtecEl = event.el.closest('.rtec-outer-wrap').length ? event.el.closest('.rtec-outer-wrap') : event.el;

                if ($rtecEl.find('.rtec-attendee-list-meta').length || $('.rtec-attendee-list-meta').length === 1) {
                    var $attendeeList = $rtecEl.find('.rtec-attendee-list-meta').length ? $rtecEl.find('.rtec-attendee-list-meta') : $('.rtec-attendee-list-meta');
                    $attendeeList.prepend($rtecEl.find('.rtec-spinner')).find('.rtec-spinner').show();
                    $attendeeList.fadeTo(500,.1);

                    var eventId = typeof $attendeeList.closest('.rtec-outer-wrap').find('.rtec').attr('data-event') !== 'undefined' ? $attendeeList.closest('.rtec-outer-wrap').find('.rtec').attr('data-event') : event.el.attr('data-event');

                    $.ajax({
                        url : rtec.ajaxUrl,
                        type : 'post',
                        data : {
                            'action': 'rtec_refresh_event_info',
                            'event_id' : eventId
                        },
                        success : function(data) {
                            $attendeeList.find('.rtec-spinner').hide();
                            $attendeeList.fadeTo(500,1);
                            if (data.trim().indexOf('<div') === 0) {
                                $attendeeList.replaceWith(data);
                            }

                        }
                    }); // ajax
                }

            });
        }
    }

    if($('.rtec').length && !$('.rtec').hasClass('rtec-initialized')) {
        rtecInit();
    }

});
