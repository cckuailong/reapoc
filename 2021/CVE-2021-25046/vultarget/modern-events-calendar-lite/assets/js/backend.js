jQuery(document).ready(function($)
{
    // Check validation of grid skin event count
    $('#mec_skin_grid_count').keyup(function()
    {
        var valid = false;
        if($(this).val() == '1' || $(this).val() == '2' || $(this).val() == '3' || $(this).val() == '4' || $(this).val() == '6' || $(this).val() == '12')
        {
            valid = true;
        };

        if(valid === false)
        {
            $(this).addClass('bootstrap_unvalid');
            $('.mec-tooltiptext').css('visibility','visible');
        }
        else
        {
            $(this).removeClass('bootstrap_unvalid');
            $('.mec-tooltiptext').css('visibility', 'hidden');
        };
    });

    // MEC Accordion
    $('.mec-accordion .mec-acc-label .mec-acc-cat-name').on('click', function()
    {
        var key = $(this).parent().attr('data-key');
        var status = $(this).parent().attr('data-status');

        // Open the accordion
        if(status === 'close')
        {
            $('.mec-accordion .mec-acc-label ul').hide();
            $('.mec-accordion .mec-acc-label').attr('data-status', 'close');
            $(this).parent().attr('data-status', 'open');
            $('#mec-acc-'+key).show();
        } else {
            $('.mec-accordion .mec-acc-label ul').hide();
            $('.mec-accordion .mec-acc-label').attr('data-status', 'close');
            $('#mec-acc-'+key).hide();
        }

    });

    // MEC Select, Deselect, Toggle
    $(".mec-select-deselect-actions li").on('click', function()
    {
        var target = $(this).parent().data('for');
        var action = $(this).data('action');

        if(action === 'select-all')
        {
            $(target+' input[type=checkbox]').each(function()
            {
                this.checked = true;
            });
        }
        else if(action === 'deselect-all')
        {
            $(target+' input[type=checkbox]').each(function()
            {
                this.checked = false;
            });
        }
        else if(action === 'toggle')
        {
            $(target+' input[type=checkbox]').each(function()
            {
                this.checked = !this.checked;
            });
        }
    });

    // MEC image popup switcher
    if($('.mec-sed-method-wrap').length > 0)
    {
        $('.mec-sed-method-wrap').each(function()
        {
            var sed_value = $(this).find('[id*="_sed_method_field"]').val();
            if(sed_value == 'm1')
            {
                $(this).siblings('.mec-image-popup-wrap').show();
            }
        });
    }

    // MEC Single Event Display Method Switcher
    $(".mec-sed-methods li").on('click', function()
    {
        var target = $(this).parent().data('for');
        var method = $(this).data('method');

        // Set the Method
        $(target).val(method);

        // Set the active method
        $(this).parent().find('li').removeClass('active');
        $(this).addClass('active');

        // Display Image popup section
        if ( method == 'm1' ) {
            $('.mec-image-popup-wrap').show();
        } else {
            $('.mec-image-popup-wrap').hide();
        }
    });

    // Initialize WP Color Picker
    if($.fn.wpColorPicker) jQuery('.mec-color-picker').wpColorPicker();

    // Initialize MEC Skin Switcher
    $('#mec_skin').on('change', function()
    {
        mec_skin_toggle();
    });

    mec_skin_toggle();

    $('.mec-switcher').on('click', 'label[for*="mec[settings]"]', function(event)
    {
        var id = $(this).closest('.mec-switcher').data('id');
        var status = $('#mec_sn_'+id+' .mec-status').val();

        if(status === '1')
        {
            $('#mec_sn_'+id+' .mec-status').val(0);
            $('#mec_sn_'+id).removeClass('mec-enabled').addClass('mec-disabled');
        }
        else
        {
            $('#mec_sn_'+id+' .mec-status').val(1);
            $('#mec_sn_'+id).removeClass('mec-disabled').addClass('mec-enabled');
        }

    });

    // MEC Checkbox Toggle (Used in Date Filter Options)
    $('.mec-checkbox-toggle').on('change', function()
    {
        var id = $(this).attr('id');
        $(".mec-checkbox-toggle:not(#"+id+")").prop('checked', false);
    });

    // MEC Setting Sticky
    if ($('.wns-be-container-sticky').length > 0)
    {
        var stickyNav = function () {
            var stickyNavTop = $('.wns-be-container-sticky').offset().top;
            var scrollTop = $(window).scrollTop();
            var width = $('.wns-be-container-sticky').width();
            if (scrollTop > stickyNavTop) {
                $('#wns-be-infobar').addClass('sticky');
                $('#wns-be-infobar').css({
                    'width' : width,
                });
            } else {
                $('#wns-be-infobar').removeClass('sticky');
            }
        };
        stickyNav();
        $(window).scroll(function () {
            stickyNav();
        });

        $("#mec-search-settings").typeWatch(
        {
            wait: 400, // 750ms
            callback: function (value)
            {
                var elements = [];
                if (!value || value == "")
                {
                    $('.mec-options-fields').hide();
                    $('.mec-options-fields').removeClass('active');
                    $('.wns-be-group-tab form .mec-options-fields:first-of-type').addClass('active');
                    $('.subsection li').removeClass('active');
                    $('.wns-be-sidebar .wns-be-group-menu .subsection li:first-of-type').addClass('active');
                }
                else
                {
                    $(".mec-options-fields").filter(function ()
                    {
                        var search_label = $(this).find('label.mec-col-3').text().toLowerCase();
                        var search_title = $(this).find('h4.mec-form-subtitle').text().toLowerCase();
                        var search_title = $(this).find('.mec-form-row').text().toLowerCase();
                        if ((!search_label || search_label == "") && (!search_title || search_title == "")) {
                            return false;
                        }
                        if ($(this).find('label.mec-col-3').text().toLowerCase().indexOf(value) > -1 || $(this).find('h4.mec-form-subtitle').text().toLowerCase().indexOf(value) > -1 || $(this).find('.mec-form-row').text().toLowerCase().indexOf(value) > -1) {
                            $('.mec-options-fields').hide();
                            $('.mec-options-fields').removeClass('active');
                            $('.wns-be-group-menu .subsection .mec-settings-menu li').removeClass('active');
                            elements.push($(this));
                        }
                    });

                    $(".mec-settings-menu li").filter(function ()
                    {
                        var search_label = $(this).find('a').text().toLowerCase();
                        var search_title = $(this).find('a span').text().toLowerCase();
                        if ((!search_label || search_label == "") && (!search_title || search_title == "")) {
                            return false;
                        }
                        if ($(this).find('a span').text().toLowerCase().indexOf(value) > -1 || $(this).find('a span').text().toLowerCase().indexOf(value) > -1) {
                            $('.mec-settings-menu li').removeClass('active');
                            $('.wns-be-group-menu .subsection .mec-settings-menu li').removeClass('active');
                            elements.push($(this));
                        }
                    });

                    $.each(elements, function (i, searchStr)
                    {
                        searchStr.show();
                        searchStr.addClass('active')
                    });

                    jQuery("#wns-be-content .mec-form-row").each(function() {
                        if (value != "" && $(this).text().search(new RegExp(value, 'gi')) != -1) {
                            jQuery(this).addClass("results");
                        } else if (value != "" && $(this).text().search(value) != 1) {
                            jQuery(this).addClass("noresults");
                        }
                    });

                    jQuery("#wns-be-content ul li").each(function() {
                        if (value != "" && $(this).text().search(new RegExp(value, 'gi')) != -1) {
                            jQuery(this).addClass("enable");
                        } else if (value != "" && $(this).text().search(value) != 1) {
                            jQuery(this).addClass("disable");
                        }
                    });

                }
                if ( !value || value == "" ) {
                    jQuery(".results").removeClass("results");
                    jQuery(".noresults").removeClass("noresults");
                    jQuery(".enable").removeClass("enable");
                    jQuery(".disable").removeClass("disable");
                }
            }
        });
    }

    // Import Settings
    function CheckJSON(text)
    {
        if (typeof text != 'string')
            text = JSON.stringify(text);
        try {
            JSON.parse(text);
            return true;
        } catch (e) {
            return false;
        }
    }

    // Location select2
    jQuery(".mec-additional-locations select").select2();
    jQuery("#mec_location_id").select2();

    // Organizer Select2
    jQuery(".mec-additional-organizers select").select2();
    jQuery("#mec_organizer_id").select2();

    // Add shortcode select2
    jQuery(".mec-create-shortcode-tab-content select").select2();
    
    // General Calendar
    jQuery("#mec_skin_general_calendar_skins").select2();
    

    // Add Notification DropDown Select2
    jQuery(".mec-notification-dropdown-select2").select2(
    {
        closeOnSelect: false,
        width: '33%'
    });

    $('.mec-import-settings').on('click', function(e)
    {
        e.preventDefault();
        var value = $(this).parent().find('.mec-import-settings-content').val();
        if ( CheckJSON(value) || value == '' ) {
            value = jQuery.parseJSON($(this).parent().find('.mec-import-settings-content').val());
        } else {
            value = 'No-JSON';
        }
        $.ajax({
            url: mec_admin_localize.ajax_url,
            type: 'POST',
            data: {
                action: 'import_settings',
                nonce: mec_admin_localize.ajax_nonce,
                content: value,
            },
            beforeSend: function () {
                $('.mec-import-settings-wrap').append('<div class="mec-loarder-wrap"><div class="mec-loarder"><div></div><div></div><div></div></div></div>');
                $('.mec-import-options-notification').find('.mec-message-import-error').remove()
                $('.mec-import-options-notification').find('.mec-message-import-success').remove()
            },
            success: function (response) {
                $('.mec-import-options-notification').append(response);
                $('.mec-loarder-wrap').remove();
                $('.mec-import-settings-content').val('');
            },
        });
    });

    /* MEC activation */
    if($('#MECActivation').length > 0)
    {
        var LicenseType = $('#MECActivation input.checked[type=radio][name=MECLicense]').val();
        $('#MECActivation input[type=radio][name=MECLicense]').change(function () {
            $('#MECActivation').find('input').removeClass('checked');
            $(this).addClass('checked');
            LicenseType = $(this).val();
        });

        $('#MECActivation input[type=submit]').on('click', function(e){
            e.preventDefault();
            $('.wna-spinner-wrap').remove();
            $('#MECActivation').find('.MECLicenseMessage').text(' ');
            $('#MECActivation').find('.MECPurchaseStatus').removeClass('PurchaseError');
            $('#MECActivation').find('.MECPurchaseStatus').removeClass('PurchaseSuccess');
            var PurchaseCode = $('#MECActivation input[type=password][name=MECPurchaseCode]').val();
            var information = { LicenseTypeJson: LicenseType, PurchaseCodeJson: PurchaseCode };
            $.ajax({
                url: mec_admin_localize.ajax_url,
                type: 'POST',
                data: {
                    action: 'activate_license',
                    nonce: mec_admin_localize.ajax_nonce,
                    content: information,
                },
                beforeSend: function () {
                    $('#MECActivation .LicenseField').append('<div class="wna-spinner-wrap"><div class="wna-spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>');
                },
                success: function (response) {
                    if (response == 'success')
                    {
                        $('.wna-spinner-wrap').remove();
                        $('#MECActivation').find('.MECPurchaseStatus').addClass('PurchaseSuccess');
                    }
                    else
                    {
                        $('.wna-spinner-wrap').remove();
                        $('#MECActivation').find('.MECPurchaseStatus').addClass('PurchaseError');
                        $('#MECActivation').find('.MECLicenseMessage').append(response);
                    }
                },
            });
        });
    }

    /* Addons Activation */
    if ($('.box-addon-activation-toggle-head').length > 0)
    {
        $('.box-addon-activation-toggle-head').on('click', function() {
            $('.box-addon-activation-toggle-content').slideToggle('slow');
            if ($(this).find('i').hasClass('mec-sl-plus')){
                $(this).find('i').removeClass('mec-sl-plus').addClass('mec-sl-minus');
            } else if ($(this).find('i').hasClass('mec-sl-minus') ) {
                $(this).find('i').removeClass('mec-sl-minus').addClass('mec-sl-plus');
            }
        });
    }

    /* Addons Notification */
    $('.mec-addons-notification-box-wrap span').on('click', function(e)
    {
        e.preventDefault();
        $.ajax({
            url: mec_admin_localize.ajax_url,
            type: 'POST',
            data: {
                action: 'close_notification',
                nonce: mec_admin_localize.ajax_nonce,
            },
            success: function (response) {
                $(".mec-addons-notification-set-box").fadeOut(100, function () { $(this).remove(); });
                $(".mec-addons-notification-wrap").fadeOut(100, function () { $(this).remove(); });
            },
        });
    });

    /* Custom msg Notification */
    $('.mec-cmsg-notification-box-wrap span').on('click', function(e)
    {
        e.preventDefault();
        $.ajax({
            url: mec_admin_localize.ajax_url,
            type: 'POST',
            data: {
                action: 'close_cmsg_notification',
                nonce: mec_admin_localize.ajax_nonce,
            },
            success: function (response) {
                $(".mec-custom-msg-notification-set-box").fadeOut(100, function () { $(this).remove(); });
                $(".mec-custom-msg-notification-wrap").fadeOut(100, function () { $(this).remove(); });
            },
        });
    });

    $('.mec-cmsg-2-notification-box-wrap span').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: mec_admin_localize.ajax_url,
            type: 'POST',
            data: {
                action: 'close_cmsg_2_notification',
                nonce: mec_admin_localize.ajax_nonce,
            },
            success: function (response) {
                $(".mec-custom-msg-2-notification-set-box").fadeOut(100, function () { $(this).remove(); });
                $(".mec-custom-msg-2-notification-wrap").fadeOut(100, function () { $(this).remove(); });
            },
        });
    });

    /* Load event dates in Report page */
    if ( $('.mec-reports-selectbox-event').length > 0 )
    {
        $('.mec-reports-selectbox-event').select2();
        $('.mec-reports-selectbox-event').on('change', function(e)
        {
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

    $('.mec-report-backtoselect-wrap button').on('click', function (e) {
        e.preventDefault();
        $('.mec-report-backtoselect-wrap').hide();
        $('.mec-report-sendmail-wrap').show();
        $('.mec-report-selected-event-attendees-wrap').show();
        $('.mec-report-sendmail-form-wrap').hide();
    })
});

function mec_skin_full_calendar_skin_toggled(Context)
{
    var id = jQuery(Context).attr('id');
    var checked = jQuery(Context).is(':checked');
    var default_view = 'list';

    if(id === 'mec_skin_full_calendar_list')
    {
        jQuery(Context).parent().parent().parent().find('.mec-date-format').toggle();
    }
    else if(id === 'mec_skin_full_calendar_grid')
    {
        jQuery(Context).parent().parent().parent().find('.mec-date-format').toggle();
        default_view = 'grid';
    }
    else if(id === 'mec_skin_full_calendar_tile')
    {
        default_view = 'tile';
    }
    else if(id === 'mec_skin_full_calendar_yearly')
    {
        jQuery(Context).parent().parent().parent().find('.mec-date-format').toggle();
        default_view = 'yearly';
    }
    else if(id === 'mec_skin_full_calendar_monthly')
    {
        jQuery('#mec_full_calendar_monthly_style').toggle();
        jQuery('#mec_full_calendar_monthly_view_options').toggle();
        default_view = 'monthly';
    }
    else if(id === 'mec_skin_full_calendar_weekly')
    {
        default_view = 'weekly';
    }
    else if(id === 'mec_skin_full_calendar_daily')
    {
        default_view = 'daily';
    }

    var $dropdown = jQuery('#mec_skin_full_calendar_default_view');
    var current_value = $dropdown.find('option:selected').prop('value');
    var $option = $dropdown.find('option[value="'+default_view+'"]');

    if(checked) $option.removeAttr('disabled');
    else $option.attr('disabled', 'disabled');

    if(current_value === default_view) $dropdown.children('option:enabled').eq(0).prop('selected',true);
    $dropdown.niceSelect('update');
}

function mec_event_attendees(ID, occurrence)
{
    // Set Occurrence
    if(typeof occurrence === 'undefined') occurrence = '';

    jQuery.ajax(
    {
        url: mec_admin_localize.ajax_url,
        type: 'POST',
        dataType: 'JSON',
        data: {
            action: 'mec_attendees',
            id: ID,
            occurrence: occurrence
        },
        success: function(response)
        {
            if (response.email_button != '') {
                jQuery('.mec-report-selected-event-attendees-wrap').show();
                jQuery('.mec-report-selected-event-attendees-wrap .w-row .w-col-sm-12').html(response.html);
                jQuery('.mec-report-sendmail-wrap').show();
                jQuery('.mec-report-sendmail-wrap .w-row .w-col-sm-12').html(response.email_button);
            } else {
                jQuery('.mec-report-selected-event-attendees-wrap').show();
                jQuery('.mec-report-sendmail-wrap').hide();
                jQuery('.mec-report-selected-event-attendees-wrap .w-row .w-col-sm-12').html(response.html);
                jQuery('.mec-report-sendmail-wrap .w-row .w-col-sm-12').html('');
            }
        },
        error: function()
        {
        }
    });
}

function mec_submit_event_email(ID) {
    // Set Occurrence
    if (typeof ID === 'undefined') ID = '';

    if (jQuery('.mec-send-email-count > span').text() == 0) {
        alert('Please choose attendees first');
        return;
    }
    jQuery('.mec-report-sendmail-form-wrap .w-row .w-col-sm-12 #mec-send-email-editor-wrap').attr('id', 'mec-send-email-editor' + ID + '-wrap');
    jQuery('.mec-report-selected-event-attendees-wrap').hide();
    jQuery('.mec-report-sendmail-form-wrap').show();
    jQuery('#mec-send-email-editor' + ID + '-wrap').html('<textarea id="editor' + ID + '" class="wp-editor-area"></textarea>');
    jQuery('#mec-send-email-editor' + ID + '-wrap').parent().find('.mec-send-email-button').data('id', ID);
    jQuery('.mec-report-sendmail-wrap').hide();
    jQuery('.mec-report-backtoselect-wrap').show();

    wp.editor.initialize('editor' + ID,
    {
        tinymce:
        {
            wpautop:true,
            toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker',
        },
        quicktags: true,
        mediaButtons: true,
    });
}

function initSlider()
{
    jQuery('.mec-attendees-list-left-menu').owlCarousel({
        autoplay: false,
        autoWidth: true,
        items: 12,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1,
            },
            979: {
                items: 2,
            },
            1199: {
                items: 12,
            }
        },
        dots: false,
        nav: true,
    });
}

function mec_skin_toggle()
{
    var skin = jQuery('#mec_skin').val();

    jQuery('.mec-skin-options-container').hide();
    jQuery('#mec_'+skin+'_skin_options_container').show();

    jQuery('.mec-search-form-options-container').hide();
    jQuery('#mec_'+skin+'_search_form_options_container').show();

    // Show/Hide Filter Options
    if(skin === 'countdown' || skin === 'cover' || skin === 'available_spot')
    {
        jQuery('#mec_meta_box_calendar_filter').hide();
        jQuery('#mec_meta_box_calendar_no_filter').show();
    }
    else
    {
        jQuery('#mec_meta_box_calendar_no_filter').hide();
        jQuery('#mec_meta_box_calendar_filter').show();
    }

    // Show/Hide Search Widget Options
    if(skin === 'countdown' || skin === 'cover' || skin === 'available_spot' || skin === 'masonry' || skin === 'carousel' || skin === 'slider' || skin === 'timeline')
    {
        jQuery('#mec_calendar_search_form').hide();
    }
    else
    {
        jQuery('#mec_calendar_search_form').show();
    }

    // Show/Hide Ongoing Events
    if(skin === 'list' || skin === 'grid' || skin === 'agenda' || skin === 'timeline' || skin === 'custom'){
        jQuery('#mec_date_ongoing_filter').show();
    }else{
        jQuery("#mec_show_only_ongoing_events").prop('checked', false);
        jQuery('#mec_date_ongoing_filter').hide();
    }

    // Show/Hide Expired Events
    if(skin === 'map')
    {
        jQuery("#mec_show_only_past_events").prop('checked', false);
        jQuery('#mec_date_only_past_filter').hide();
    }
    else jQuery('#mec_date_only_past_filter').show();

    // Trigger change event of skin style in order to show/hide related fields
    jQuery('#mec_skin_'+skin+'_style').trigger('change');
}

function mec_skin_style_changed(skin, style, context) {
    if (style.includes('fluent')) {
        jQuery('.mec-' + skin + '-fluent').removeClass('mec-fluent-hidden');
        jQuery('.mec-not-' + skin + '-fluent').addClass('mec-fluent-hidden');
    } else {
        jQuery('.mec-' + skin + '-fluent').addClass('mec-fluent-hidden');
        jQuery('.mec-not-' + skin + '-fluent').removeClass('mec-fluent-hidden');
    }

    jQuery('.mec-skin-' + skin + '-date-format-container').hide();
    jQuery('#mec_skin_' + skin + '_date_format_' + style + '_container').show();

    // Show Or Hide Include Events Time Switcher
    if (style == 'classic' || style == 'minimal' || style == 'modern') jQuery(context).parent().parent().find('.mec-include-events-times').show();
    else jQuery(context).parent().parent().find('.mec-include-events-times').hide();

    if (style == 'accordion') jQuery(context).parent().parent().find('#mec_skin_list_localtime').hide();
}

function mec_skin_map_toggle(context)
{
    jQuery(context).parent().parent().parent().find('.mec-set-geolocation').toggle();
}

function mec_skin_geolocation_toggle(context)
{
    jQuery(context).parent().parent().parent().parent().find('.mec-set-geolocation-focus').toggle();
}

function mec_show_widget_options(context)
{
    var skin = jQuery(context).find(jQuery(':selected')).data('skin');
    if(skin === 'monthly_view')
    {
        jQuery(context).parent().parent().find(jQuery('.mec-current-check-wrap')).show();
        jQuery(context).parent().parent().find(jQuery('.mec-grid-options-wrap')).hide();
    }
    else if(skin === 'grid')
    {
        jQuery(context).parent().parent().find(jQuery('.mec-current-check-wrap')).hide();
        jQuery(context).parent().parent().find(jQuery('.mec-grid-options-wrap')).show();
    }
    else
    {
        jQuery(context).parent().parent().find(jQuery('.mec-current-check-wrap')).hide();
        jQuery(context).parent().parent().find(jQuery('.mec-grid-options-wrap')).hide();
    }
}

// Niceselect
jQuery(document).ready(function()
{
    if(jQuery('.wn-mec-select').length > 0) jQuery('.wn-mec-select').niceSelect();

    // Send Custom Email To Attendees Button
    jQuery('.mec-send-email-button').click(function()
    {
        var $this = this;
        var data_send = jQuery('.mec-attendees-content').find('input[type="checkbox"]:checked').parent().find('.mec-send-email-attendee-info').text();
        var mail_subject = jQuery('#mec-send-email-subject').val();
        var mail_content = wp.editor.getContent('editor' + jQuery(this).data('id'));
        var mail_message = jQuery('#mec-send-email-message');
        var mail_copy = jQuery('#mec-send-admin-copy').is(':checked') ? 1 : 0;

        if(data_send.length == 0) mail_message.attr('class', 'mec-util-hidden mec-error').html(jQuery('#mec-send-email-no-user-selected').val()).show();
        else if(mail_subject.length == 0) mail_message.attr('class', 'mec-util-hidden mec-error').html(jQuery('#mec-send-email-empty-subject').val()).show();
        else if(mail_content.length == 0) mail_message.attr('class', 'mec-util-hidden mec-error').html(jQuery('#mec-send-email-empty-content').val()).show();
        else
        {
            mail_message.hide();
            jQuery($this).html(jQuery('#mec-send-email-label-loading').val());
            jQuery.ajax(
            {
                url: mec_admin_localize.ajax_url,
                type: 'POST',
                data: {
                    action: 'mec_mass_email',
                    nonce: mec_admin_localize.ajax_nonce,
                    mail_recipients_info: data_send,
                    mail_subject: mail_subject,
                    mail_content: mail_content,
                    mail_copy: mail_copy
                },
                success: function(response)
                {
                    jQuery($this).html(jQuery('#mec-send-email-label').val());
                    if(response == true) mail_message.attr('class', 'mec-util-hidden mec-success').html(jQuery('#mec-send-email-success').val()).show();
                    else mail_message.attr('class', 'mec-util-hidden mec-error').html(jQuery('#mec-send-email-error').val()).show();
                },
                error: function()
                {
                    jQuery($this).html(jQuery('#mec-send-email-label').val());
                    mail_message.attr('class', 'mec-util-hidden mec-error').html(jQuery('#mec-send-email-error').val()).show();
                }
            });
        }
    });

    jQuery('.mec-attendees-list-left-menu .owl-item').click(function()
    {
        jQuery(this).parent().parent().parent().parent().parent().find('.mec-send-email-count > span').html(0);
    });
});

// Check All Send Custom Email To Attendees
function mec_send_email_check(Context)
{
    var all_item = jQuery(Context).parent().parent().parent().find('.mec-attendees-content');
    var item_len = all_item.find('input[type="checkbox"]').length;
    var check_len = all_item.find('input[type="checkbox"]:checked').length;
    var all_check = jQuery(Context).parent().parent().parent().find('#mec-send-email-check-all');

    jQuery('.mec-send-email-count > span').html(check_len);
    if(item_len === check_len) all_check.prop('checked', true);
    else all_check.prop('checked', false);
}

function mec_send_email_check_all(Context)
{
    var all_item = jQuery(Context).parent().parent().parent().parent().find('.mec-attendees-content');

    if(jQuery(Context).is(':checked')) all_item.find('input[type="checkbox"]').prop('checked', true);
    else all_item.find('input[type="checkbox"]').prop('checked', false);

    var check_len = all_item.find('input[type="checkbox"]:checked').length;
    jQuery('.mec-send-email-count > span').html(check_len);
}

(function(wp, $)
{
    var items = '';
    if(typeof mec_admin_localize !== "undefined") items = JSON.parse(mec_admin_localize.mce_items);

    // Block Editor
    if(items && wp && wp.blocks)
    {
        items.shortcodes.forEach(function(e, i)
        {
            wp.blocks.registerBlockType(`mec/blockeditor-${i}`,
            {
                title: items.shortcodes[i]['PN'].toLowerCase().replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g, function(s)
                {
                    return s.toUpperCase().replace(/-/g,' ');
                }),
                icon: 'calendar-alt',
                category: 'mec.block.category',
                edit: function()
                {
                    return `[MEC id="${(items.shortcodes[i]['ID'])}"]`;
                },
                save: function()
                {
                    return `[MEC id="${(items.shortcodes[i]['ID'])}"]`;
                }
            });
        });
    }
})(window.wp, jQuery);