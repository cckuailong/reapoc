jQuery(document).ready(function($){

    // date picker
    function rtecDiffInDays( a, b ) {
        return Math.ceil( (a - b) / (1000 * 60 * 60 * 24) );
    }

    var deadlineDate = parseInt( $('.rtec-date-picker').attr('data-rtec-deadline') ) * 1000,
        nowTime = Date.now();

    $('.rtec-date-picker').each(function() {
        $(this).datepicker({
            defaultDate: rtecDiffInDays(deadlineDate, nowTime),
            dateFormat: 'yy-mm-dd',
            beforeShow: function( element, object ){
                // Capture the datepicker div here; it's dynamically generated so best to grab here instead of elsewhere.
                $dpDiv = $( object.dpDiv );

                // "Namespace" our CSS a bit so that our custom jquery-ui-datepicker styles don't interfere with other plugins'/themes'.
                $dpDiv.addClass( 'tribe-ui-datepicker rtec-ui-datepicker' );
            }
        });
    });

    // time picker
    if (typeof $().timepicker !== 'undefined') {
        $('.rtec-time-picker').each(function() {
            $(this).timepicker();
        });
    }

    $('.rtec-reveal-mvt').on('click',function(event) {
        event.preventDefault();

        var mvtNames = $('#rtec_mvt_names').val().split(','),
            hits = 0;

        $('.rtec-mvt-fields-select-wrapper').find('.rtec-mvt-field').each(function() {
            if (hits === 0) {
                if (mvtNames.indexOf($(this).attr('data-name')) === -1) {
                    hits = 1;
                    mvtNames.push($(this).attr('data-name'));
                    $(this).show().insertAfter($(this).prev());
                }
            }
        });
        $('#rtec_mvt_names').val(mvtNames.join(','))
    });

    var $body = $('#wpbody-content');

    function rtecUpdateMVTNames(el) {
        var names = [];
        el.closest('.rtec-mvt-fields-select-wrapper').find('.rtec-mvt-field').each(function() {
            names.push($(this).attr('data-name'));
        });

        el.val(names.join(','));
    }

    function rtecDisabledToggle($wrapEl) {
        var $disableReg = $wrapEl.find('input[name="_RTECregistrationsDisabled"]'),
            $eventForm = $wrapEl.find('select[name="_RTECformID"]'),
            $whoCan = $wrapEl.find('input[name="_RTECwhoCanRegister"]'),
            $limitReg = $wrapEl.find('input[name="_RTEClimitRegistrations"]'),
            $maxReg = $wrapEl.find('input[name="_RTECmaxRegistrations"]'),
            $deadlineType = $wrapEl.find('input[name="_RTECdeadlineType"]'),
            $attendeeList = $wrapEl.find('input[name="_RTECshowRegistrantsData"]'),
            $attendeeWho = $wrapEl.find('input[name="_RTECregistrantsDataWho"]'),
            $attendeeLoggedOnly = $wrapEl.find('input[name="_RTECattendeeListLoggedInOnly"]');

        if ($disableReg.is(':checked')) {
            $eventForm.prop('disabled',true);
            $limitReg.prop('disabled',true);
            $whoCan.prop('disabled',true);
            $maxReg.prop('disabled',true);
            $deadlineType.prop('disabled',true);
        } else {
            $eventForm.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
            $whoCan.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
            $limitReg.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
            $deadlineType.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
            $attendeeList.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
            if ($limitReg.is(':checked')) {
                $maxReg.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
            } else {
                $maxReg.prop('disabled',true);
            }
            if ($attendeeList.is(':checked')) {
                $attendeeWho.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
                $attendeeLoggedOnly.prop('disabled',false).closest('.rtec-fade').removeClass('rtec-fade');
            } else {
                $attendeeWho.prop('disabled',true);
                $attendeeLoggedOnly.prop('disabled',true);
            }
        }

        if ($wrapEl.find('input[name=_RTECdeadlineType]:checked').val() === 'other') {
            $wrapEl.find('.rtec-time-picker, .rtec-date-picker').removeClass('rtec-fade');
        } else {
            $wrapEl.find('.rtec-time-picker, .rtec-date-picker').addClass('rtec-fade');
        }
    }

    $('.rtec-eventtable .rtec-hidden-option-wrap input').on('change', function() {
        rtecDisabledToggle($(this).closest('.rtec-eventtable'));
    });
    $('.rtec-hidden-options .rtec-hidden-option-wrap input').on('change', function() {
        rtecDisabledToggle($(this).closest('.rtec-hidden-options'));
    });

    $body.on('click', '.rtec-mvt-field-remove', function (event) {
        var $rtecEl = $(event.target).closest('.rtec-field-options-wrapper'),
            $passThis = $rtecEl.closest('.rtec-mvt-fields-select-wrapper').find('#rtec_mvt_names');
        $(event.target).closest('.rtec-field-options-wrapper').remove();
        rtecUpdateMVTNames($passThis);
    });

    $body.on('click', '.rtec-mvt-field-hide', function (event) {
        var $rtecEl = $(event.target).closest('.rtec-field-options-wrapper'),
            mvtNames = $rtecEl.closest('.rtec-mvt-fields-select-wrapper').find('#rtec_mvt_names').val().split(','),
            removeIndex = mvtNames.indexOf($rtecEl.attr('data-name'));
        $rtecEl.hide();
        $('.rtec-mvt-field').last().after($rtecEl);
        if (removeIndex > -1) {
            mvtNames.splice(removeIndex, 1);
        }
        $('#rtec_mvt_names').val(mvtNames.join(','))
    });

    $('.rtec-mvt-use-custom-checkbox').on('click',function() {
        if ($(this).is(':checked')) {
            $(this).closest('.rtec-event-options').find('.rtec-hidden-mvt').slideDown();
        } else {
            $(this).closest('.rtec-event-options').find('.rtec-hidden-mvt').slideUp();
        }
    });

    $('#rtec-use_custom_confirmation-checkbox').on('click',function() {
        if ($(this).is(':checked')) {
            $('.rtec-hidden-conf-email').slideDown();
        } else {
            $('.rtec-hidden-conf-email').slideUp();
        }
    });
});
