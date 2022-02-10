/**
 * PPOM Fields Conditions
 **/
"use strict"

var ppom_field_matched_rules = {};
var ppom_hidden_fields = [];
jQuery(function($) {

    $(".ppom-wrapper").on('change', 'select,input:radio,input:checkbox', function(e) {

        ppom_check_conditions();
    });

    $(document).on('ppom_field_shown', function(e) {


        // Remove from array
        $.each(ppom_hidden_fields, function(i, item) {
            if (item === e.field) {


                // Set checked/selected again
                ppom_set_default_option(item);

                ppom_hidden_fields.splice(i, 1);
                $.event.trigger({
                    type: "ppom_hidden_fields_updated",
                    field: e.field,
                    time: new Date()
                });

            }
        });

        // Apply FileAPI to DOM
        var field_meta = ppom_get_field_meta_by_id(e.field);
        if (field_meta.type === 'file' || field_meta.type === 'cropper') {

            ppom_setup_file_upload_input(field_meta);
        }

        // Price Matrix
        if (field_meta.type == 'pricematrix') {
            // Resettin
            $(".ppom_pricematrix").removeClass('active');

            // Set Active
            var classname = "." + field_meta.data_name;
            // console.log(classname);
            $(classname).find('.ppom_pricematrix').addClass('active')
        }

        //Imageselect (Image dropdown)
        if (field_meta.type === 'imageselect') {

            var dd_selector = 'ppom_imageselect_' + field_meta.data_name;
            var ddData = $('#' + dd_selector).data('ddslick');
            var image_replace = $('#' + dd_selector).attr('data-enable-rpimg');
            ppom_create_hidden_input(ddData);
            ppom_update_option_prices();
            setTimeout(function() {
                ppom_image_selection(ddData, image_replace);
            }, 100);
            // $('#'+dd_selector).ddslick('select', {index: 0 });
        }


        // Multiple Select Addon
        if (field_meta.type === 'multiple_select') {

            var selector = jQuery('select[name="ppom[fields][' + field_meta.data_name + '][]"]');
            var selected_value = selector.val();
            var default_value = field_meta.selected;

            if (selected_value === null && default_value) {

                var selected_opt_arr = default_value.split(',');

                selector.val(selected_opt_arr).trigger('change');

                var selected_options = selector.find('option:selected');
                jQuery.each(selected_options, function(index, default_selected) {

                    var option_id = jQuery(default_selected).attr('data-option_id');
                    var option_label = jQuery(default_selected).attr('data-optionlabel');
                    var option_price = jQuery(default_selected).attr('data-optionprice');

                    ppom_multiple_select_create_hidden_input(field_meta.data_name, option_id, option_price, option_label, field_meta.title);
                });
            }
        }

    });

    $(document).on('ppom_hidden_fields_updated', function(e) {


        $("#conditionally_hidden").val(ppom_hidden_fields);
        ppom_update_option_prices();
    });

    $(document).on('ppom_field_hidden', function(e) {

        var element_type = ppom_get_field_type_by_id(e.field);
        switch (element_type) {

            case 'select':
                $('select[name="ppom[fields][' + e.field + ']"]').val('');
                break;

            case 'multiple_select':

                var selector = $('select[name="ppom[fields][' + e.field + '][]"]');
                var selected_value = selector.val();
                var selected_options = selector.find('option:selected');

                jQuery.each(selected_options, function(index, default_selected) {

                    var option_id = jQuery(default_selected).attr('data-option_id');
                    var the_id = 'ppom-multipleselect-' + e.field + '-' + option_id;

                    $("#" + the_id).remove();
                });

                if (selected_value) {

                    $('select[name="ppom[fields][' + e.field + '][]"]').val(null).trigger("change");
                }

                break;

            case 'checkbox':
                $('input[name="ppom[fields][' + e.field + '][]"]').prop('checked', false);
                break;
            case 'radio':
                $('input[name="ppom[fields][' + e.field + ']"]').prop('checked', false);
                break;

            case 'file':
                $('#filelist-' + e.field).find('.u_i_c_box').remove();
                break;

            case 'palettes':
            case 'image':
                $('input[name="ppom[fields][' + e.field + '][]"]').prop('checked', false);
                break;

            case 'imageselect':
                var the_id = 'ppom-imageselect' + e.field;
                $("#" + the_id).remove();
                break;

            default:
                // Reset text/textarea/date/email etc types
                $('#' + e.field).val('');
                break;
        }

        ppom_hidden_fields.push(e.field);
        $.event.trigger({
            type: "ppom_hidden_fields_updated",
            field: e.field,
            time: new Date()
        });
    });


    setTimeout(function() {
        ppom_check_conditions();
    }, 500);

});

function ppom_set_default_option(field_id) {

    // get product id
    var product_id = ppom_input_vars.product_id;

    var field = ppom_get_field_meta_by_id(field_id);
    switch (field.type) {

        // Check if field is 
        case 'radio':
            jQuery.each(field.options, function(label, options) {
                var opt_id = product_id + '-' + field.data_name + '-' + options.id;

                if (options.option == field.selected) {
                    jQuery("#" + opt_id).prop('checked', true);
                }
            });

            break;

        case 'select':
            jQuery("#" + field.data_name).val(field.selected);
            break;

        case 'image':
            jQuery.each(field.images, function(index, img) {

                if (img.title == field.selected) {
                    jQuery("#" + field.data_name + '-' + img.id).prop('checked', true);
                }
            });
            break;

        case 'checkbox':
            jQuery.each(field.options, function(label, options) {
                var opt_id = product_id + '-' + field.data_name + '-' + options.id;

                var default_checked = field.checked.split('\r\n');
                if (jQuery.inArray(options.option, default_checked) > -1) {
                    jQuery("#" + opt_id).prop('checked', true);

                }
            });
            break;

        case 'text':
        case 'date':
        case 'number':
            jQuery("#" + field.data_name).val(field.default_value);
            break;
    }
}

function ppom_check_conditions() {

    jQuery.each(ppom_input_vars.conditions, function(field, condition) {


        // It will return rules array with True or False
        ppom_field_matched_rules[field] = ppom_get_field_rule_status(condition);

        // get length of condition
        var obj_length = Object.keys(condition.rules).length;

        // Now check if all rules are valid
        if (condition.bound === 'Any' && ppom_field_matched_rules[field] > 0) {
            ppom_unlock_field_from_condition(field, condition.visibility);
        }
        else if (condition.bound === 'All' && ppom_field_matched_rules[field] == obj_length) {
            ppom_unlock_field_from_condition(field, condition.visibility);
        }
        else {
            ppom_lock_field_from_condition(field, condition.visibility);
        }

    });
}

function ppom_unlock_field_from_condition(field, unlock) {

    var classname = '.ppom-input-' + field;
    if (unlock === 'Show') {
        jQuery(classname).show().removeClass('ppom-locked').addClass('ppom-unlocked')
            .trigger({
                type: "ppom_field_shown",
                field: field,
                time: new Date()
            });
    }
    else {
        jQuery(classname).hide().removeClass('ppom-locked').addClass('ppom-unlocked')
            .trigger({
                type: "ppom_field_hidden",
                field: field,
                time: new Date()
            });
    }
}

function ppom_lock_field_from_condition(field, lock) {

    var classname = '.ppom-input-' + field;
    if (lock === 'Show') {
        jQuery(classname).hide().removeClass('ppom-unlocked').addClass('ppom-locked')
            .trigger({
                type: "ppom_field_hidden",
                field: field,
                time: new Date()
            });
    }
    else {
        jQuery(classname).show().removeClass('ppom-unlocked').addClass('ppom-locked')
            .trigger({
                type: "ppom_field_shown",
                field: field,
                time: new Date()
            });
    }

    jQuery.event.trigger({
        type: "ppom_field_locked",
        field: field,
        lock: lock,
        time: new Date()
    });
}

// It will return rules array with True or False
function ppom_get_field_rule_status(condition) {

    var ppom_rules_matched = 0;
    jQuery.each(condition.rules, function(i, rule) {

        var element_type = ppom_get_field_type_by_id(rule.elements);

        // console.log(element_type);
        switch (rule.operators) {
            case 'is':
                if (element_type === 'checkbox') {
                    var element_value = ppom_get_element_value(rule.elements);
                    jQuery(element_value).each(function(i, item) {
                        if (item === rule.element_values) {
                            ppom_rules_matched++;
                        }
                    });
                }
                else if (element_type === 'image') {
                    var element_value = ppom_get_element_value(rule.elements);
                    jQuery(element_value).each(function(i, item) {
                        if (item === rule.element_values) {
                            ppom_rules_matched++;
                        }
                    });
                }
                else if (ppom_get_element_value(rule.elements) === rule.element_values) {
                    ppom_rules_matched++;
                }
                break;

            case 'not':
                if (element_type === 'checkbox') {
                    var element_value = ppom_get_element_value(rule.elements);
                    jQuery(element_value).each(function(i, item) {
                        if (item !== rule.element_values) {
                            ppom_rules_matched++;
                        }
                    });
                }
                else if (ppom_get_element_value(rule.elements) !== rule.element_values) {
                    ppom_rules_matched++;
                }
                break;

            case 'greater than':
                if (element_type === 'checkbox') {
                    var element_value = ppom_get_element_value(rule.elements);
                    jQuery(element_value).each(function(i, item) {
                        if (parseFloat(item) > parseFloat(rule.element_values)) {
                            ppom_rules_matched++;
                        }
                    });
                }
                else if (parseFloat(ppom_get_element_value(rule.elements)) > parseFloat(rule.element_values)) {
                    ppom_rules_matched++;
                }
                break;

            case 'less than':
                if (element_type === 'checkbox') {
                    var element_value = ppom_get_element_value(rule.elements);
                    jQuery(element_value).each(function(i, item) {
                        if (parseFloat(item) < parseFloat(rule.element_values)) {
                            ppom_rules_matched++;
                        }
                    });
                }
                else if (parseFloat(ppom_get_element_value(rule.elements)) < parseFloat(rule.element_values)) {
                    ppom_rules_matched++;
                }
                break;


        }
    });

    return ppom_rules_matched;
}

// Getting rule element value
function ppom_get_element_value(field_name) {

    var element_type = ppom_get_field_type_by_id(field_name);
    var value_found = '';
    var value_found_cb = [];

    switch (element_type) {

        case 'select':
            value_found = jQuery('select[name="ppom[fields][' + field_name + ']"]').val();
            break;

        case 'radio':
            value_found = jQuery('input[name="ppom[fields][' + field_name + ']"]:checked').val();
            break;

        case 'checkbox':
            jQuery('input[name="ppom[fields][' + field_name + '][]"]:checked').each(function(i) {
                value_found_cb[i] = jQuery(this).val();
            });
            break;

        case 'image':
            // value_found = jQuery('input[name="ppom[fields]['+field_name+'][]"]:checked').attr('data-label');
            jQuery('input[name="ppom[fields][' + field_name + '][]"]:checked').each(function(i) {
                value_found_cb[i] = jQuery(this).attr('data-label');
            });
            break;

        case 'imageselect':
            value_found = jQuery('input[name="ppom[fields][' + field_name + ']"]:checked').attr('data-title');
            break;

    }

    if (element_type === 'checkbox' || element_type === 'image') {
        return value_found_cb;
    }

    return value_found;
}
