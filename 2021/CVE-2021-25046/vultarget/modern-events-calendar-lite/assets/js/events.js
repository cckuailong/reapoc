// Set datepicker default value.
var datepicker_format = 'yy-mm-dd';

jQuery(document).ready(function($)
{
    // Image picker on terms menu
    $('.mec_upload_image_button').click(function(event)
    {
        event.preventDefault();

        var frame;
        if(frame)
        {
            frame.open();
            return;
        }

        frame = wp.media();
        frame.on('select', function()
        {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $('#mec_thumbnail_img').html('<img src="'+attachment.attributes.url+'" />');
            $('#mec_thumbnail').val(attachment.attributes.url);

            $('.mec_remove_image_button').toggleClass('mec-util-hidden');

            frame.close();
        });

        frame.open();
    });

    // Image remover on terms menu
    $('.mec_remove_image_button').click(function(event)
    {
        event.preventDefault();

        $('#mec_thumbnail_img').html('');
        $('#mec_thumbnail').val('');

        $('.mec_remove_image_button').toggleClass('mec-util-hidden');
    });

    // Image picker on add event menu for location
    $('.mec_location_upload_image_button').click(function(event)
    {
        event.preventDefault();

        var frame;
        if(frame)
        {
            frame.open();
            return;
        }

        frame = wp.media();
        frame.on('select', function()
        {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $('#mec_location_thumbnail_img').html('<img src="'+attachment.attributes.url+'" />');
            $('#mec_location_thumbnail').val(attachment.attributes.url);

            $('.mec_location_remove_image_button').toggleClass('mec-util-hidden');

            frame.close();
        });

        frame.open();
    });

    // Image remover on add event menu for location
    $('.mec_location_remove_image_button').click(function(event)
    {
        event.preventDefault();

        $('#mec_location_thumbnail_img').html('');
        $('#mec_location_thumbnail').val('');

        $('.mec_location_remove_image_button').toggleClass('mec-util-hidden');
    });

    // Image picker on add event menu for organizer
    $('.mec_organizer_upload_image_button').click(function(event)
    {
        event.preventDefault();

        var frame;
        if(frame)
        {
            frame.open();
            return;
        }

        frame = wp.media();
        frame.on('select', function()
        {
            // Grab the selected attachment.
            var attachment = frame.state().get('selection').first();

            $('#mec_organizer_thumbnail_img').html('<img src="'+attachment.attributes.url+'" />');
            $('#mec_organizer_thumbnail').val(attachment.attributes.url);

            $('.mec_organizer_remove_image_button').toggleClass('mec-util-hidden');

            frame.close();
        });

        frame.open();
    });

    // Image remover on add event menu for organizer
    $('.mec_organizer_remove_image_button').click(function(event)
    {
        event.preventDefault();

        $('#mec_organizer_thumbnail_img').html('');
        $('#mec_organizer_thumbnail').val('');

        $('.mec_organizer_remove_image_button').toggleClass('mec-util-hidden');
    });

    // Image remover on frontend event submission menu
    $('#mec_fes_remove_image_button').click(function(event)
    {
        event.preventDefault();

        $('#mec_fes_thumbnail_img').html('');
        $('#mec_fes_thumbnail').val('');
        $('#mec_featured_image_file').val('');

        $('#mec_fes_remove_image_button').addClass('mec-util-hidden');
    });

    // Location Image remover on frontend event submission menu
    $('#mec_fes_location_remove_image_button').click(function(event)
    {
        event.preventDefault();

        $('#mec_fes_location_thumbnail_img').html('');
        $('#mec_fes_location_thumbnail').val('');
        $('#mec_fes_location_thumbnail_file').val('');

        $('#mec_fes_location_remove_image_button').addClass('mec-util-hidden');
    });

    // Organizer Image remover on frontend event submission menu
    $('#mec_fes_organizer_remove_image_button').click(function(event)
    {
        event.preventDefault();

        $('#mec_fes_organizer_thumbnail_img').html('');
        $('#mec_fes_organizer_thumbnail').val('');
        $('#mec_fes_organizer_thumbnail_file').val('');

        $('#mec_fes_organizer_remove_image_button').addClass('mec-util-hidden');
    });

    var date_splite;
    if(typeof mec_admin_localize !== 'undefined')
    {
        date_splite = mec_admin_localize.datepicker_format.split('&');
        if(date_splite[0] !== undefined && date_splite.length == 2) datepicker_format = date_splite[0];
    }
    else if(typeof mecdata !== 'undefined')
    {
        date_splite = mecdata.datepicker_format.split( '&' );
        if(date_splite[0] !== undefined && date_splite.length == 2) datepicker_format = date_splite[0];
    }

    $('[id^="mec-book-form-btn-step"]').on('click',function()
    {
        setTimeout(function()
        {
            if($.fn.datepicker)
            {
                if('date' !== $('.mec-date-picker').prop('type'))
                {
                    $('.mec-date-picker').datepicker(
                    {
                        changeYear: true,
                        changeMonth: true,
                        dateFormat: datepicker_format,
                        gotoCurrent: true,
                        yearRange: 'c-3:c+5',
                    });
                }

            }
        }, 1000);
    });

    if($.fn.datepicker)
    {
        $('.mec-date-picker').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: datepicker_format,
            gotoCurrent: true,
            yearRange: 'c-3:c+5',
        });

        $('#mec_start_date').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: datepicker_format,
            gotoCurrent: true,
            yearRange: 'c-3:c+5',
        });

        $('#mec_end_date').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: datepicker_format,
            gotoCurrent: true,
            yearRange: 'c-3:c+5',
        });

        $('#mec_date_repeat_end_at_date').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: datepicker_format,
            gotoCurrent: true,
            yearRange: 'c-3:c+5',
        });

        $('.mec_date_picker_dynamic_format').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: datepicker_format,
            gotoCurrent: true,
            yearRange: 'c-3:c+5',
        });

        $('.mec_date_picker').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: 'yy-mm-dd',
            gotoCurrent: true,
            yearRange: 'c-3:c+5',
        });

        $('.mec_date_picker_dynamic_format_start').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: datepicker_format,
            gotoCurrent: true,
            yearRange: 'c-1:c+5',
            onSelect: function(date)
            {
                var selectedDate = new Date(date);
                var endDate = new Date(selectedDate.getTime());

                var $end_picker = $(this).next();
                $end_picker.datepicker("option", "minDate", endDate);
                $end_picker.datepicker("option", "maxDate", '+5y');
            }
        });

        $('.mec_date_picker_dynamic_format_end').datepicker(
        {
            changeYear: true,
            changeMonth: true,
            dateFormat: datepicker_format,
            gotoCurrent: true,
            yearRange: 'c-1:c+5',
        });
    }

    $('#mec_location_id').on('change', function()
    {
        mec_location_toggle();
    });

    $('#mec_organizer_id').on('change', function()
    {
        mec_organizer_toggle();
        var mec_organizer_val = parseInt($(this).val());
        var mec_additional_organizer = $(this).parent().parent().find('#mec-additional-organizer-wrap');

        if(mec_organizer_val != 1) mec_additional_organizer.show();
        else mec_additional_organizer.hide();
    });

    mec_location_toggle();
    mec_organizer_toggle();

    $('#mec_repeat').on('change', function()
    {
        mec_repeat_toggle();
    });

    mec_repeat_toggle();

    $('#mec_repeat_type').on('change', function()
    {
        mec_repeat_type_toggle();
    });

    mec_repeat_type_toggle();

    $('#mec_bookings_limit_unlimited').on('change', function()
    {
        mec_bookings_unlimited_toggle();
    });

    $('#mec_add_in_days').on('click', function()
    {
        var allday = $(this).data('allday');

        var start = $('#mec_exceptions_in_days_start_date').val();
        if(start === '') return false;

        var end = $('#mec_exceptions_in_days_end_date').val();
        if(end === '') return false;

        var start_hour = $('#mec_exceptions_in_days_start_hour').val();
        if(start_hour.length === 1) start_hour = '0'+start_hour;

        var start_minutes = $('#mec_exceptions_in_days_start_minutes').val();
        if(start_minutes.length === 1) start_minutes = '0'+start_minutes;

        var start_ampm = $('#mec_exceptions_in_days_start_ampm').val();
        if(typeof start_ampm === 'undefined') start_ampm = '';

        var end_hour = $('#mec_exceptions_in_days_end_hour').val();
        if(end_hour.length === 1) end_hour = '0'+end_hour;

        var end_minutes = $('#mec_exceptions_in_days_end_minutes').val();
        if(end_minutes.length === 1) end_minutes = '0'+end_minutes;

        var end_ampm = $('#mec_exceptions_in_days_end_ampm').val();
        if(typeof end_ampm === 'undefined') end_ampm = '';

        var value = start + ':' + end + ':' + start_hour + '-' + start_minutes + '-' + start_ampm + ':' + end_hour + '-' + end_minutes + '-' + end_ampm;
        var label = start + ' <span class="mec-time-picker-label '+(allday ? 'mec-util-hidden' : '')+'">' + start_hour + ':' + start_minutes + ' ' + start_ampm + '</span> - ' + end + ' <span class="mec-time-picker-label '+(allday ? 'mec-util-hidden' : '')+'">' + end_hour + ':' + end_minutes + ' ' + end_ampm + '</span>';

        // Don't add exactly same occurrences
        if($('#mec_in_days input[value="'+value+'"]').length > 0) return false;

        var $key = $('#mec_new_in_days_key');

        var key = $key.val();
        var html = $('#mec_new_in_days_raw').html().replace(/:i:/g, key).replace(/:val:/g, value).replace(/:label:/g, label);

        $('#mec_in_days').append(html);
        $key.val(parseInt(key)+1);
    });

    $('#mec_add_not_in_days').on('click', function()
    {
        var date = $('#mec_exceptions_not_in_days_date').val();
        if(date === '') return false;

        var key = $('#mec_new_not_in_days_key').val();
        var html = $('#mec_new_not_in_days_raw').html().replace(/:i:/g, key).replace(/:val:/g, date);

        $('#mec_not_in_days').append(html);
        $('#mec_new_not_in_days_key').val(parseInt(key)+1);
    });

    $('#mec_add_ticket_button').on('click', function()
    {
        var key = $('#mec_new_ticket_key').val();
        var html = $('#mec_new_ticket_raw').html().replace(/:i:/g, key);

        $('#mec_tickets').append(html);
        $('#mec_new_ticket_key').val(parseInt(key)+1);

        $('.mec_add_price_date_button').off('click').on('click', function()
        {
            mec_handle_add_price_date_button(this);
        });

        $.each($(".mec-select2"), function(i,v){

            if( $(v).attr('name').search(":i:") > 0 ){

                return;
            }

            if( typeof $(v).data('select2-id') == 'undefined' ){

                $(v).select2();
            }
        });
    });

    $('.mec_add_price_date_button').off('click').on('click', function()
    {
        mec_handle_add_price_date_button(this);
    });

    mec_hourly_schedule_add_day_listener();

    $('#mec_add_fee_button').on('click', function()
    {
        var key = $('#mec_new_fee_key').val();
        var html = $('#mec_new_fee_raw').html().replace(/:i:/g, key);

        $('#mec_fees_list').append(html);
        $('#mec_new_fee_key').val(parseInt(key)+1);
    });

    $('#mec_add_ticket_variation_button').on('click', function()
    {
        var key = $('#mec_new_ticket_variation_key').val();
        var html = $('#mec_new_ticket_variation_raw').html().replace(/:i:/g, key);

        $('#mec_ticket_variations_list').append(html);
        $('#mec_new_ticket_variation_key').val(parseInt(key)+1);
    });

    $('.mec-form-row.mec-available-color-row span').on('click', function()
    {
        $('.mec-form-row.mec-available-color-row span').removeClass('color-selected');
        $(this).addClass('color-selected');
    });

    $('#mec_reg_form_field_types button').on('click', function()
    {
        var type = $(this).data('type');

        if (type == 'mec_email') {
            if ($('#mec_reg_form_fields').find('input[value="mec_email"][type="hidden"]').length) {
                return false;
            }
        }

        if (type == 'name') {
            if ($('#mec_reg_form_fields').find('input[value="name"][type="hidden"]').length) {
                return false;
            }
        }

        var key  = $('#mec_new_reg_field_key').val();
        var html = $('#mec_reg_field_'+type).html().replace(/:i:/g, key);

        $('#mec_reg_form_fields').append(html);
        $('#mec_new_reg_field_key').val(parseInt(key)+1);

        // Set onclick listener for add option fields
        mec_reg_fields_option_listeners();
    });

    // Set onclick listener for add option fields
    mec_reg_fields_option_listeners();

    // Advanced Repeating
    $('#mec-advanced-wraper ul > ul > li').click(function()
    {
        if($(this).attr('class') == '') $(this).attr('class', 'mec-active');
        else $(this).attr('class', '');

        $('#mec_date_repeat_advanced').val($('#mec-advanced-wraper div:first-child > ul').find('.mec-active').find('span').text().slice(0, -1));
    });

    $('#mec_event_form_field_types button').on('click', function()
    {
        var type = $(this).data('type');

        var key  = $('#mec_new_event_field_key').val();
        var html = $('#mec_event_field_'+type).html().replace(/:i:/g, key);

        $('#mec_event_form_fields').append(html);
        $('#mec_new_event_field_key').val(parseInt(key)+1);

        // Set onclick listener for add option fields
        mec_event_fields_option_listeners();
    });

    // Set onclick listener for add option fields
    mec_event_fields_option_listeners();

    $('#mec_bfixed_form_field_types button').on('click', function()
    {
        var type = $(this).data('type');

        var key  = $('#mec_new_bfixed_field_key').val();
        var html = $('#mec_bfixed_field_'+type).html().replace(/:i:/g, key);

        $('#mec_bfixed_form_fields').append(html);
        $('#mec_new_bfixed_field_key').val(parseInt(key)+1);

        // Set onclick listener for add option fields
        mec_bfixed_fields_option_listeners();
    });

    // Set onclick listener for add option fields
    mec_bfixed_fields_option_listeners();

    // Additional Organizers
    mec_additional_organizers_listeners();
});

function mec_location_toggle()
{
    if(jQuery('#mec_location_id').val() != '0') jQuery('#mec_location_new_container').hide();
    else jQuery('#mec_location_new_container').show();
}

function mec_organizer_toggle()
{
    if(jQuery('#mec_organizer_id').val() != '0') jQuery('#mec_organizer_new_container').hide();
    else jQuery('#mec_organizer_new_container').show();
}

function mec_repeat_toggle()
{
    if(jQuery('#mec_repeat').is(':checked')) jQuery('.mec-form-repeating-event-row').show();
    else jQuery('.mec-form-repeating-event-row').hide();
}

function mec_repeat_type_toggle()
{
    var repeat_type = jQuery('#mec_repeat_type').val();

    if(repeat_type == 'certain_weekdays')
    {
        jQuery('#mec_repeat_interval_container').hide();
        jQuery('#mec_repeat_certain_weekdays_container').show();
        jQuery('#mec_exceptions_in_days_container').hide();
        jQuery('#mec_end_wrapper').show();
        jQuery('#mec-advanced-wraper').hide();
    }
    else if(repeat_type == 'custom_days')
    {
        jQuery('#mec_repeat_interval_container').hide();
        jQuery('#mec_repeat_certain_weekdays_container').hide();
        jQuery('#mec_exceptions_in_days_container').show();
        jQuery('#mec_end_wrapper').hide();
        jQuery('#mec-advanced-wraper').hide();
    }
    else if(repeat_type == 'advanced')
    {
        jQuery('#mec_repeat_interval_container').hide();
        jQuery('#mec_repeat_certain_weekdays_container').hide();
        jQuery('#mec_exceptions_in_days_container').hide();
        jQuery('#mec_end_wrapper').show();
        jQuery('#mec-advanced-wraper').show();
    }
    else if(repeat_type != 'daily' && repeat_type != 'weekly' && repeat_type != 'monthly')
    {
        jQuery('#mec_repeat_interval_container').hide();
        jQuery('#mec_repeat_certain_weekdays_container').hide();
        jQuery('#mec_exceptions_in_days_container').hide();
        jQuery('#mec_end_wrapper').show();
        jQuery('#mec-advanced-wraper').hide();
    }
    else
    {
        jQuery('#mec_repeat_interval_container').show();
        jQuery('#mec_repeat_certain_weekdays_container').hide();
        jQuery('#mec_exceptions_in_days_container').hide();
        jQuery('#mec_end_wrapper').show();
        jQuery('#mec-advanced-wraper').hide();
    }
}

function mec_in_days_remove(i)
{
    jQuery('#mec_in_days_row'+i).remove();
}

function mec_not_in_days_remove(i)
{
    jQuery('#mec_not_in_days_row'+i).remove();
}

function mec_bookings_unlimited_toggle()
{
    jQuery('#mec_bookings_limit').toggleClass('mec-util-hidden');
}

function mec_hourly_schedule_add_day_listener()
{
    jQuery('.mec-add-hourly-schedule-day-button').each(function()
    {
        jQuery(this).off('click').on('click', function()
        {
            var k = jQuery(this).data('key');
            var raw = jQuery(this).data('raw');
            var append = jQuery(this).data('append');

            var key = jQuery(k).val();
            var html = jQuery(raw).html().replace(/:d:/g, key).replace(/:dd:/g, parseInt(key)+1);

            jQuery(append).append(html);
            jQuery(k).val(parseInt(key)+1);

            mec_hourly_schedule_listeners();
        });

        mec_hourly_schedule_listeners();
    });
}

function mec_hourly_schedule_listeners()
{
    jQuery('.mec-add-hourly-schedule-button').off('click').on('click', function()
    {
        var prefix = jQuery(this).data('prefix');
        var day = jQuery(this).data('day');
        var $key = jQuery('#'+prefix+'mec_new_hourly_schedule_key'+day);

        var key = $key.val();
        var html = jQuery('#'+prefix+'mec_new_hourly_schedule_raw'+day).html().replace(/:i:/g, key).replace(/:d:/g, day).replace();
        var g_field_id = prefix+"-hourly_schedules-"+day+"-schedules-:i:-description";
        var field_id = prefix+"-hourly_schedules-"+day+"-schedules-"+key+"-description";
        html = html.replace(g_field_id,field_id);
        html = html.replace(':k:',key);
        jQuery('#'+prefix+'mec_hourly_schedules'+day).append(html);
        $key.val(parseInt(key)+1);

        wp.editor.initialize("mec"+field_id,{
            tinymce: {
                wpautop: true,
                plugins : 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                toolbar1: 'bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen | wp_adv',
                toolbar2: 'formatselect alignjustify forecolor | pastetext removeformat charmap | outdent indent | undo redo | wp_help'
            },
            quicktags: true,
            mediaButtons: false,
          });
    });
}

function mec_hourly_schedule_remove(day, i, prefix)
{
    jQuery("#"+prefix+"mec_hourly_schedule_row"+day+'_'+i).remove();
}

function mec_hourly_schedule_day_remove(day, prefix)
{
    jQuery("#"+prefix+"mec_meta_box_hourly_schedule_day_"+day).remove();
}

function mec_ticket_remove(i)
{
    jQuery("#mec_ticket_row"+i).remove();
}

function mec_set_event_color(color)
{
    try
    {
        jQuery("#mec_event_color").wpColorPicker('color', '#'+color);
    }
    catch(e)
    {
        jQuery("#mec_event_color").val(color);
    }
}

function mec_remove_fee(key)
{
    jQuery("#mec_fee_row"+key).remove();
}

function mec_remove_ticket_variation(key, id_prefix)
{
    jQuery("#mec_"+id_prefix+"_row"+key).remove();
}

function add_variation_per_ticket(ticket_id)
{
    var $input = jQuery('#mec_new_variation_per_ticket_key');

    var key = $input.val();
    var html = jQuery('#mec_new_variation_per_ticket_raw'+ticket_id).html().replace(/:v:/g, key);

    jQuery('#mec_ticket_variations_list'+ticket_id).append(html);
    $input.val(parseInt(key)+1);
}

function mec_reg_fields_option_listeners()
{
    jQuery('button.mec-reg-field-add-option').off('click').on('click', function()
    {
        var field_id = jQuery(this).data('field-id');
        var key = jQuery('#mec_new_reg_field_option_key_'+field_id).val();
        var html = jQuery('#mec_reg_field_option').html().replace(/:i:/g, key).replace(/:fi:/g, field_id);

        jQuery('#mec_reg_fields_'+field_id+'_options_container').append(html);
        jQuery('#mec_new_reg_field_option_key_'+field_id).val(parseInt(key)+1);
    });

    if(typeof jQuery.fn.sortable !== 'undefined')
    {
        jQuery("#mec_reg_form_fields").sortable(
        {
            handle: '.mec_reg_field_sort'
        });

        jQuery(".mec_reg_fields_options_container").sortable(
        {
            handle: '.mec_reg_field_option_sort'
        });
    }
}

function mec_reg_fields_option_remove(field_key, key)
{
    jQuery("#mec_reg_fields_option_"+field_key+"_"+key).remove();
}

function mec_reg_fields_remove(key)
{
    jQuery("#mec_reg_fields_"+key).remove();
}

function mec_handle_add_price_date_button(e)
{
    var key = jQuery(e).data('key');
    var p = jQuery('#mec_new_ticket_price_key_'+key).val();
    var html = jQuery('#mec_new_ticket_price_raw_'+key).html().replace(/:i:/g, key).replace(/:j:/g, p);

    jQuery('#mec-ticket-price-dates-'+key).append(html);
    jQuery('#mec_new_ticket_price_key_'+key).val(parseInt(p)+1);
    jQuery('#mec-ticket-price-dates-'+key+' .new_added').datepicker(
    {
        changeYear: true,
        changeMonth: true,
        dateFormat: datepicker_format,
        gotoCurrent: true,
        yearRange: 'c-3:c+5',
    });
}

function mec_ticket_price_remove(ticket_key, price_key)
{
    jQuery("#mec_ticket_price_raw_"+ticket_key+"_"+price_key).remove();
}

function mec_event_fields_option_listeners()
{
    jQuery('button.mec-event-field-add-option').off('click').on('click', function()
    {
        var field_id = jQuery(this).data('field-id');
        var key = jQuery('#mec_new_event_field_option_key_'+field_id).val();
        var html = jQuery('#mec_event_field_option').html().replace(/:i:/g, key).replace(/:fi:/g, field_id);

        jQuery('#mec_event_fields_'+field_id+'_options_container').append(html);
        jQuery('#mec_new_event_field_option_key_'+field_id).val(parseInt(key)+1);
    });

    if(typeof jQuery.fn.sortable !== 'undefined')
    {
        jQuery("#mec_event_form_fields").sortable(
        {
            handle: '.mec_event_field_sort'
        });

        jQuery(".mec_event_fields_options_container").sortable(
        {
            handle: '.mec_event_field_option_sort'
        });
    }
}

function mec_event_fields_option_remove(field_key, key)
{
    jQuery("#mec_event_fields_option_"+field_key+"_"+key).remove();
}

function mec_event_fields_remove(key)
{
    jQuery("#mec_event_fields_"+key).remove();
}

function mec_bfixed_fields_option_listeners()
{
    jQuery('button.mec-bfixed-field-add-option').off('click').on('click', function()
    {
        var field_id = jQuery(this).data('field-id');
        var key = jQuery('#mec_new_bfixed_field_option_key_'+field_id).val();
        var html = jQuery('#mec_bfixed_field_option').html().replace(/:i:/g, key).replace(/:fi:/g, field_id);

        jQuery('#mec_bfixed_fields_'+field_id+'_options_container').append(html);
        jQuery('#mec_new_bfixed_field_option_key_'+field_id).val(parseInt(key)+1);
    });

    if(typeof jQuery.fn.sortable !== 'undefined')
    {
        jQuery("#mec_bfixed_form_fields").sortable(
        {
            handle: '.mec_bfixed_field_sort'
        });

        jQuery(".mec_bfixed_fields_options_container").sortable(
        {
            handle: '.mec_bfixed_field_option_sort'
        });
    }
}

function mec_bfixed_fields_option_remove(field_key, key)
{
    jQuery("#mec_bfixed_fields_option_"+field_key+"_"+key).remove();
}

function mec_bfixed_fields_remove(key)
{
    jQuery("#mec_bfixed_fields_"+key).remove();
}

function mec_additional_organizers_listeners()
{
    jQuery('#mec_additional_organizers_add').off('click').on('click', function()
    {
        var value = jQuery('.mec-additional-organizers select').val();
        var text = jQuery('.mec-additional-organizers select option:selected').text();

        var sortLabel = jQuery(this).data('sort-label');
        var removeLabel = jQuery(this).data('remove-label');

        jQuery('.mec-additional-organizers-list').append('<li><span class="mec-additional-organizer-sort">'+sortLabel+'</span> <span onclick="mec_additional_organizers_remove(this);" class="mec-additional-organizer-remove">'+removeLabel+'</span><input type="hidden" name="mec[additional_organizer_ids][]" value="'+value+'"><span class="mec_orgz_item_name">'+text+'</span></li>');

        mec_additional_organizers_listeners();
    });

    if(typeof jQuery.fn.sortable !== 'undefined')
    {
        jQuery(".mec-additional-organizers-list").sortable(
        {
            handle: '.mec-additional-organizer-sort'
        });
    }
}

function mec_additional_organizers_remove(element)
{
    jQuery(element).parent().remove();
}