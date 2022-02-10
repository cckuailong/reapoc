function mec_fields_option_listeners() {

    jQuery('.mec_field_remove').on('click', function (e) {
        jQuery(this).parent('li').remove();
    });

    jQuery('button.mec-field-add-option').off('click').on('click', function (e) {

        var container = jQuery(e.currentTarget).parents('.mec-container');
        if(container.length > 1){
            container = container[0];
        }
        var form = jQuery(e.currentTarget).parents('.mec-form-row');
        if(form.length > 1){
            form = form[0];
        }
        var item_box = jQuery(e.currentTarget).parents('li');
        var form_type = jQuery(form).data('form-type');
        var field_id = jQuery(this).data('field-id');
        var key = jQuery('#mec_new_' + form_type + '_field_option_key_' + field_id, container).val();
        var html = jQuery('.mec_field_option', container).html().replace(/:i:/g, key).replace(/:fi:/g, field_id);

        jQuery('.mec_fields_options_container', item_box).append(html);
        jQuery('#mec_new_' + form_type + '_field_option_key_' + field_id, container).val(parseInt(key) + 1);

        mec_fields_option_listeners();
    });

    if (typeof jQuery.fn.sortable !== 'undefined') {
        jQuery(".mec_form_fields").sortable(
            {
                handle: '.mec_field_sort'
            });

        jQuery(".mec_fields_options_container").sortable(
            {
                handle: '.mec_field_option_sort'
            });
    }
}

jQuery(document).ready(function ($) {

    /* Load event dates in Report page */
    if ($('.mec-reports-selectbox-event').length > 0) {
        $('.mec-reports-selectbox-event').select2();
        $('.mec-reports-selectbox-event').on('change', function (e) {
            e.preventDefault();
            var id = $('.mec-reports-selectbox-event').val();
            $.ajax({
                url: mec_admin_localize.ajax_url,
                type: 'POST',
                data: {
                    action: 'report_event_dates',
                    nonce: mec_admin_localize.ajax_nonce,
                    event_id: id,
                },
                success: function (response) {
                    $('.mec-report-selected-event-attendees-wrap').hide();
                    $('.mec-reports-selectbox-dates').remove();
                    $('.mec-report-selected-event-attendees-wrap .w-row .w-col-sm-12').html('');
                    $('.mec-report-select-event-wrap .w-row .w-col-sm-12').append(response);
                    $('.mec-report-sendmail-wrap').hide();
                    $('.mec-report-backtoselect-wrap').hide();
                },
            });
        });
    }


    jQuery('[name="mec[waiting_form][waiting_form_id]"]').on("change", function () {
        if (jQuery(this).data("name") == "waiting_form_id") {
            jQuery("div#mec_form_container").removeClass("mec-util-hidden");
            jQuery("#waiting_form_id").addClass("mec-util-hidden");
        } else {
            jQuery("div#mec_form_container").addClass("mec-util-hidden");
            jQuery("#waiting_form_id").removeClass("mec-util-hidden");
            jQuery(this).val(jQuery("#waiting_form_id").val());
        }
    });
    jQuery("#waiting_form_id").on("change", function () {
        jQuery("input[data-name=formBuilder_waiting_form_id]").val(jQuery(this).val());
    })

    if (typeof (mec_default_waiting_form_id) != "undefined") {
        jQuery("input[data-name=formBuilder_waiting_form_id]").prop("checked", true);
        jQuery("#waiting_form_id").removeClass("mec-util-hidden");
        jQuery("div#mec_form_container").addClass("mec-util-hidden");
        jQuery("input[data-name=formBuilder_waiting_form_id]").val(mec_default_waiting_form_id);
    } else {
        jQuery("input[data-name=waiting_form_id]").prop("checked", true);
    }

    $('.mec_form_field_types button').on('click', function (e) {
        var container = $(e.currentTarget).parents('.mec-container');
        if(container.length > 1){
            container = container[0];
        }
        var form = $(e.currentTarget).parents('.mec-form-row');
        if(form.length > 1){
            form = form[0];
        }
        var form_type = $(form).data('form-type');
        var type = $(this).data('type');

        if (type === 'mec_email') {
            if ($('.mec_form_fields', form).find('input[value="mec_email"][type="hidden"]').length) {
                return false;
            }
        }

        if (type === 'last_name') {
            if ($('.mec_form_fields', form).find('input[value="last_name"][type="hidden"]').length) {
                return false;
            }
        }

        if (type === 'first_name') {
            if ($('.mec_form_fields', form).find('input[value="first_name"][type="hidden"]').length) {
                return false;
            }
        }

        var key = $('#mec_new_' + form_type + '_field_key', container).val();
        var html = $('.mec_field_' + type, container).html().replace(/:i:/g, key);
        console.log(container);

        $('.mec_form_fields', form).append(html);
        $('#mec_new_' + form_type + '_field_key', container).val(parseInt(key) + 1);

        // Set onclick listener for add option fields
        mec_fields_option_listeners();
    });

    // Set onclick listener for add option fields
    mec_fields_option_listeners();
});
