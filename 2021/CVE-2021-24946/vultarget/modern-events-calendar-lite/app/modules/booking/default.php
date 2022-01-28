<?php
/** no direct access **/
defined('MECEXEC') or die();

// PRO Version is required
if(!$this->getPRO()) return;

// MEC Settings
$settings = $this->get_settings();

// Booking module is disabled
if(!isset($settings['booking_status']) or (isset($settings['booking_status']) and !$settings['booking_status'])) return;

$uniqueid = '';
$uniqueid = apply_filters('mec_booking_uniqueid_value', $uniqueid);

$event = $event[0];
$uniqueid = (isset($uniqueid) && !empty($uniqueid) ? $uniqueid : $event->data->ID);

$tickets = isset($event->data->tickets) ? $event->data->tickets : array();
$dates = isset($event->dates) ? $event->dates : $event->date;

// No Dates
if(!count($dates)) return;

// No Tickets
if(!count($tickets)) return;

// Shortcode Options
if(!isset($from_shortcode)) $from_shortcode = false;
if(!isset($ticket_id)) $ticket_id = NULL;

// Generate JavaScript code of Booking Module
$javascript = '<script type="text/javascript">
var mec_tickets_availability_ajax'.$uniqueid.' = false;
function mec_get_tickets_availability'.$uniqueid.'(event_id, date)
{
    if(!date) return;
    
    // Add loading Class to the ticket list
    jQuery(".mec-event-tickets-list").addClass("loading");
    jQuery("#mec_booking'.$uniqueid.' .mec-event-tickets-list input").prop("disabled", true);

    // Abort previous request
    if(mec_tickets_availability_ajax'.$uniqueid.') mec_tickets_availability_ajax'.$uniqueid.'.abort();

    // Start Preloader
    jQuery(".mec-event-tickets-list").addClass("mec-cover-loader");
    jQuery(".mec-event-tickets-list").append("<div class=\"mec-loader\"></div>");

    mec_tickets_availability_ajax'.$uniqueid.' = jQuery.ajax(
    {
        type: "GET",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: "action=mec_tickets_availability&event_id="+event_id+"&date="+date,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the ticket list
            jQuery("#mec_booking'.$uniqueid.' .mec-event-tickets-list").removeClass("loading");
            jQuery("#mec_booking'.$uniqueid.' .mec-event-tickets-list input").prop("disabled", false);

            // Set Total Booking Limit
            if(typeof data.availability.total != "undefined") jQuery("#mec_booking'.$uniqueid.' #mec_book_form_tickets_container'.$uniqueid.'").data("total-booking-limit", data.availability.total);

            var available_spots = 0;
            for(ticket_id in data.availability)
            {
                var limit = data.availability[ticket_id];
                
                if(ticket_id != "total")
                {
                    if(limit != "-1" && available_spots != "-1") available_spots += parseInt(limit);
                    else available_spots = "-1";
                }

                jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id).addClass(".mec-event-ticket"+limit);

                if(data.availability["stop_selling_"+ticket_id]) jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-"+ticket_id).attr("class", "mec-ticket-unavailable-spots mec-error").find("div").html(jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-sales-"+ticket_id).val());
                else jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-"+ticket_id).attr("class", "mec-ticket-unavailable-spots info-msg").find("div").html(jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-sold-out-"+ticket_id).val());

                // There are some available spots
                if(limit != "0")
                {
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-available-spots").removeClass("mec-util-hidden");
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-unavailable-spots").addClass("mec-util-hidden");
                }
                // All spots are sold.
                else
                {
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-available-spots").addClass("mec-util-hidden");
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-unavailable-spots").removeClass("mec-util-hidden");
                }

                if(limit == "-1")
                {
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max", "");
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-event-ticket-available span").html("'.esc_html__("Unlimited", 'modern-events-calendar-lite').'");
                }
                else
                {
                    var cur_count = jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val();
                    if(cur_count > limit) jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val(limit);

                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max", limit);
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-event-ticket-available span").html(limit);
                }
            }

            for(ticket_id in data.prices)
            {
                var price_label = data.prices[ticket_id];

                jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-event-ticket-price").html(price_label);
            }

            // Remove Preloader
            jQuery(".mec-loader").remove();
            jQuery(".mec-event-tickets-list").removeClass("mec-cover-loader");
            
            // Disable or Enable Button
            if(available_spots == "0") jQuery("#mec_booking'.$uniqueid.' #mec-book-form-btn-step-1").hide();
            else jQuery("#mec_booking'.$uniqueid.' #mec-book-form-btn-step-1").show();
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the ticket list
            jQuery("#mec_booking'.$uniqueid.' .mec-event-tickets-list").removeClass("loading");
        }
    });
}

function mec_get_tickets_availability_multiple'.$uniqueid.'(event_id)
{
    var $ticket_list = jQuery("#mec_booking'.$uniqueid.' .mec-event-tickets-list");
    
    // Add loading Class to the ticket list
    $ticket_list.addClass("loading");
    jQuery("#mec_booking'.$uniqueid.' .mec-event-tickets-list input").prop("disabled", true);

    // Abort previous request
    if(mec_tickets_availability_ajax'.$uniqueid.') mec_tickets_availability_ajax'.$uniqueid.'.abort();

    // Start Preloader
    $ticket_list.addClass("mec-cover-loader");
    $ticket_list.append("<div class=\"mec-loader\"></div>");
    
    var date = "";
    jQuery("#mec_booking'.$uniqueid.' .mec-booking-dates-checkboxes input[type=checkbox]:checked").each(function()
    {
        date += "date[]="+jQuery(this).val()+"&";
    });
    
    date = date.slice(0, -1);
    
    mec_tickets_availability_ajax'.$uniqueid.' = jQuery.ajax(
    {
        type: "GET",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: "action=mec_tickets_availability_multiple&event_id="+event_id+"&"+date,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the ticket list
            $ticket_list.removeClass("loading");
            jQuery("#mec_booking'.$uniqueid.' .mec-event-tickets-list input").prop("disabled", false);
            
            // Set Total Booking Limit
            if(typeof data.availability.total != "undefined") jQuery("#mec_booking'.$uniqueid.' #mec_book_form_tickets_container'.$uniqueid.'").data("total-booking-limit", data.availability.total);

            var available_spots = 0;
            for(ticket_id in data.availability)
            {
                var limit = data.availability[ticket_id];
                
                if(ticket_id != "total")
                {
                    if(limit != "-1" && available_spots != "-1") available_spots += parseInt(limit);
                    else available_spots = "-1";
                }

                jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id).addClass(".mec-event-ticket"+limit);

                if(data.availability["stop_selling_"+ticket_id]) jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-"+ticket_id).attr("class", "mec-ticket-unavailable-spots mec-error").find("div").html(jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-sales-"+ticket_id).val());
                else jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-"+ticket_id).attr("class", "mec-ticket-unavailable-spots info-msg").find("div").html(jQuery("#mec_booking'.$uniqueid.' #mec-ticket-message-sold-out-"+ticket_id).val());

                // There are some available spots
                if(limit != "0")
                {
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-available-spots").removeClass("mec-util-hidden");
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-unavailable-spots").addClass("mec-util-hidden");
                }
                // All spots are sold.
                else
                {
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-available-spots").addClass("mec-util-hidden");
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-ticket-unavailable-spots").removeClass("mec-util-hidden");
                }

                if(limit == "-1")
                {
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max", "");
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-event-ticket-available span").html("'.esc_html__("Unlimited", 'modern-events-calendar-lite').'");
                }
                else
                {
                    var cur_count = jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val();
                    if(cur_count > limit) jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val(limit);

                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max", limit);
                    jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-event-ticket-available span").html(limit);
                }
            }
            
            // Disable or Enable Button
            if(available_spots == "0") jQuery("#mec_booking'.$uniqueid.' #mec-book-form-btn-step-1").hide();
            else jQuery("#mec_booking'.$uniqueid.' #mec-book-form-btn-step-1").show();

            // Remove Preloader
            jQuery(".mec-loader").remove();
            $ticket_list.removeClass("mec-cover-loader");
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the ticket list
            $ticket_list.removeClass("loading");
        }
    });
}

function mec_check_tickets_availability'.$uniqueid.'(ticket_id, count)
{
    var total = jQuery("#mec_book_form_tickets_container'.$uniqueid.'").data("total-booking-limit");
    var max = jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").attr("max");

    var sum = 0;
    jQuery("#mec_booking'.$uniqueid.' .mec-book-ticket-limit").each(function()
    {
        sum += parseInt(jQuery(this).val(), 10);
    });

    if(total != "-1" && max > (total - (sum - count))) max = (total - (sum - count));

    if(parseInt(count) > parseInt(max)) jQuery("#mec_booking'.$uniqueid.' #mec_event_ticket"+ticket_id+" .mec-book-ticket-limit").val(max);
}

function mec_toggle_first_for_all'.$uniqueid.'(context)
{
    var status = jQuery("#mec_book_first_for_all'.$uniqueid.'").is(":checked") ? true : false;

    if(status)
    {
        jQuery("#mec_booking'.$uniqueid.' .mec-book-ticket-container:not(:first-child)").addClass("mec-util-hidden");
        jQuery(context).parent().find("input[type=\"checkbox\"]").attr("checked", "checked");
    }
    else
    {
        jQuery("#mec_booking'.$uniqueid.' .mec-book-ticket-container").removeClass("mec-util-hidden");
        jQuery(context).parent().find("input[type=\"checkbox\"]").removeAttr("checked");
    }
}

function mec_label_first_for_all'.$uniqueid.'(context)
{
    var input = jQuery("#mec_book_first_for_all'.$uniqueid.'");
    if(!input.is(":checked"))
    {
        input.prop("checked", true);
        mec_toggle_first_for_all'.$uniqueid.'(context);
    }
    else
    {
        input.prop("checked", false);
        mec_toggle_first_for_all'.$uniqueid.'(context);
    }
}

function mec_book_form_submit'.$uniqueid.'()
{
    var step = jQuery("#mec_book_form'.$uniqueid.' input[name=step]").val();

    // Validate Checkboxes and Radio Buttons on Booking Form
    if(step == 2)
    {
        var valid = true;

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-mec_email.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[tickets]["+ticket_id+"][email]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-name.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[tickets]["+ticket_id+"][name]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-checkbox.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"][]\']").is(":checked"))
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-file.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });
        
        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-radio.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']:checked").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-agreement.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']:checked").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-tel.mec-reg-mandatory, .mec-book-ticket-container .mec-book-reg-field-email.mec-reg-mandatory, .mec-book-ticket-container .mec-book-reg-field-date.mec-reg-mandatory, .mec-book-ticket-container .mec-book-reg-field-text.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-select.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' select[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-ticket-container .mec-book-reg-field-textarea.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var ticket_id = jQuery(this).data("ticket-id");
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' textarea[name=\'book[tickets]["+ticket_id+"][reg]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });
        
        // Fixed Fields
        jQuery("#mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-text.mec-reg-mandatory, #mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-date.mec-reg-mandatory, #mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-email.mec-reg-mandatory, #mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-tel.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[fields]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });
        
        jQuery("#mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-checkbox.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[fields]["+field_id+"][]\']").is(":checked"))
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });
        
        jQuery("#mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-radio.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[fields]["+field_id+"]\']:checked").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-agreement.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' input[name=\'book[fields]["+field_id+"]\']:checked").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });
        
        jQuery("#mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-select.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' select[name=\'book[fields]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        jQuery("#mec_book_form'.$uniqueid.' .mec-book-bfixed-fields-container .mec-book-bfixed-field-textarea.mec-reg-mandatory").filter(":visible").each(function(i)
        {
            var field_id = jQuery(this).data("field-id");

            if(!jQuery("#mec_book_form'.$uniqueid.' textarea[name=\'book[fields]["+field_id+"]\']").val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });
        
        // Manual Username and Password
        jQuery("#mec_book_form'.$uniqueid.' #mec_book_form_username, #mec_book_form'.$uniqueid.' #mec_book_form_password").filter(":visible").each(function(i)
        {
            if(!jQuery(this).val())
            {
                valid = false;
                jQuery(this).addClass("mec-red-notification");
                if ( jQuery(this).find(".mec-booking-field-required").length < 1) {
                    jQuery(this).find("label").append("<span class=\'mec-booking-field-required\'>'.esc_html__('This field is required.', 'modern-events-calendar-lite').'</span>");
                }
            }
            else 
            {
                jQuery(this).find(".mec-booking-field-required").remove();
                jQuery(this).removeClass("mec-red-notification");
            }
        });

        if(!valid) return false;
    }

    // Add loading Class to the button
    jQuery("#mec_book_form'.$uniqueid.' button[type=submit]").addClass("loading").attr("disabled" , "true");
    jQuery("#mec_booking_message'.$uniqueid.'").removeClass("mec-success mec-error").hide();

    var fileToUpload = false;

    var data = jQuery("#mec_book_form'.$uniqueid.'").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: new FormData(jQuery("#mec_book_form'.$uniqueid.'")[0]),
        dataType: "JSON",
        processData: false,
        contentType: false,
        cache: false,
        success: function(data)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form'.$uniqueid.' button[type=submit]").removeClass("loading").removeAttr("disabled");

            if(data.success)
            {
                // Redirect to Checkout Page
                if(typeof data.data.next != "undefined" && data.data.next != "")
                {
                    if(data.data.next.type === "url")
                    {
                        window.parent.location.href = data.data.next.url; 
                        return;
                    }
                    else
                    {
                        jQuery("#mec_booking'.$uniqueid.'").html(data.data.next.message);
                        return;
                    }
                }
                
                jQuery("#mec_booking'.$uniqueid.'").html(data.output);

                // Show Invoice Link
                if(typeof data.data.invoice_link != "undefined" && data.data.invoice_link != "")
                {
                    jQuery("#mec_booking'.$uniqueid.'").append("<a class=\"mec-invoice-download\" href=\""+data.data.invoice_link+"\">'.esc_js(__('Download Invoice', 'modern-events-calendar-lite')).'</a>");
                }

                // Redirect to thank you page
                if(typeof data.data.redirect_to != "undefined" && data.data.redirect_to != "")
                {
                    setTimeout(function(){window.location.href = data.data.redirect_to;}, 2000);
                }
                
                jQuery("html,body").animate({
                    scrollTop: jQuery(".mec-events-meta-group-booking").offset().top - 100
                }, "slow");

                if(jQuery(".mec-single-fluent-wrap").length>0 && typeof jQuery.fn.niceSelect !== "undefined")
                {
                    jQuery(".mec-single-fluent-wrap").find("select").niceSelect();
                }
            }
            else
            {
                jQuery("#mec_booking_message'.$uniqueid.'").addClass("mec-error").html(data.message).show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form'.$uniqueid.' button[type=submit]").removeClass("loading");
        }
    });
}

function mec_book_apply_coupon'.$uniqueid.'()
{
    // Add loading Class to the button
    jQuery("#mec_book_form_coupon'.$uniqueid.' button[type=submit]").addClass("loading");
    jQuery("#mec_booking'.$uniqueid.' .mec-book-form-coupon .mec-coupon-message").removeClass("mec-success mec-error").hide();

    var data = jQuery("#mec_book_form_coupon'.$uniqueid.'").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: data,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_coupon'.$uniqueid.' button[type=submit]").removeClass("loading");

            if(data.success)
            {
                // It converts to free booking because of applied coupon
                if(data.data.price_raw === 0)
                {
                    jQuery("#mec_booking'.$uniqueid.' .mec-book-form-gateways").hide();
                    jQuery("#mec_book_form_free_booking'.$uniqueid.'").show();
                }

                jQuery("#mec_booking'.$uniqueid.' .mec-book-form-coupon .mec-coupon-message").addClass("mec-success").html(data.message).show();

                jQuery("#mec_booking'.$uniqueid.' .mec-book-price-details li").remove();
                jQuery("#mec_booking'.$uniqueid.' .mec-book-price-details").html(data.data.price_details);

                jQuery("#mec_booking'.$uniqueid.' .mec-book-price-total").html(data.data.price);
                jQuery("#mec_booking'.$uniqueid.' #mec_do_transaction_paypal_express_form"+data.data.transaction_id+" input[name=amount]").val(data.data.price_raw);
                jQuery("#mec_booking'.$uniqueid.' #mec_ideal_stripe_amount").val(data.data.price_raw * 100);
            }
            else
            {
                jQuery("#mec_booking'.$uniqueid.' .mec-book-form-coupon .mec-coupon-message").addClass("mec-error").html(data.message).show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_coupon'.$uniqueid.' button[type=submit]").removeClass("loading");
        }
    });
}

function mec_book_free'.$uniqueid.'()
{
    // Add loading Class to the button
    jQuery("#mec_book_form_free_booking'.$uniqueid.'").find("button").prop("disabled", true);
    jQuery("#mec_book_form_free_booking'.$uniqueid.' button[type=submit]").addClass("loading");
    jQuery("#mec_booking_message'.$uniqueid.'").removeClass("mec-success mec-error").hide();

    var data = jQuery("#mec_book_form_free_booking'.$uniqueid.'").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: data,
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_free_booking'.$uniqueid.' button[type=submit]").removeClass("loading");

            if(data.success)
            {
                jQuery("#mec_booking'.$uniqueid.'").html(data.output);

                // Show Invoice Link
                if(typeof data.data.invoice_link != "undefined" && data.data.invoice_link != "")
                {
                    jQuery("#mec_booking'.$uniqueid.'").append("<a class=\"mec-invoice-download\" href=\""+data.data.invoice_link+"\">'.esc_js(__('Download Invoice', 'modern-events-calendar-lite')).'</a>");
                }

                // Redirect to thank you page
                if(typeof data.data.redirect_to != "undefined" && data.data.redirect_to != "")
                {
                    setTimeout(function(){window.location.href = data.data.redirect_to;}, 2000);
                }
            }
            else
            {   
                jQuery("#mec_booking_message'.$uniqueid.'").addClass("mec-error").html(data.message).show();
                jQuery("#mec_book_form_free_booking'.$uniqueid.'").find("button").prop("disabled", false);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the button
            jQuery("#mec_book_form_free_booking'.$uniqueid.' button[type=submit]").removeClass("loading");
            jQuery("#mec_book_form_free_booking'.$uniqueid.'").find("button").prop("disabled", false);
        }
    });
}

function mec_check_variation_min_max'.$uniqueid.'(variation)
{
    var value = parseInt(jQuery(variation).val());
    var max = parseInt(jQuery(variation).prop("max"));
    var min = parseInt(jQuery(variation).prop("min"));

    if(value > max) jQuery(variation).val(max);
    if(value < min) jQuery(variation).val(min);
}

function mec_adjust_booking_fees'.$uniqueid.'(gateway_id, transaction_id)
{
    // Add loading class to the wrapper
    jQuery("#mec_booking'.$uniqueid.' .mec-book-form-price").addClass("loading");
    
    jQuery.ajax(
    {
        type: "POST",
        url: "'.admin_url('admin-ajax.php', NULL).'",
        data: "action=mec_adjust_booking_fees&gateway_id="+gateway_id+"&transaction_id="+transaction_id+"&_wpnonce='.wp_create_nonce('mec_adjust_booking_fees').'",
        dataType: "JSON",
        success: function(data)
        {
            // Remove the loading Class to the wrapper
            jQuery("#mec_booking'.$uniqueid.' .mec-book-form-price").removeClass("loading");

            if(data.success)
            {
                jQuery("#mec_booking'.$uniqueid.' .mec-book-price-details li").remove();
                jQuery("#mec_booking'.$uniqueid.' .mec-book-price-details").html(data.data.price_details);

                jQuery("#mec_booking'.$uniqueid.' .mec-book-price-total").html(data.data.price);
                jQuery("#mec_booking'.$uniqueid.' #mec_do_transaction_paypal_express_form"+data.data.transaction_id+" input[name=amount]").val(data.data.price_raw);
                jQuery("#mec_booking'.$uniqueid.' #mec_ideal_stripe_amount").val(data.data.price_raw * 100);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            // Remove the loading Class to the wrapper
            jQuery("#mec_booking'.$uniqueid.' .mec-book-form-price").removeClass("loading");
        }
    });
}

'.((defined('DOING_AJAX') and DOING_AJAX) ? 'jQuery(document).ready(function()
{
    mec_get_tickets_availability'.$uniqueid.'('.$event->ID.', jQuery("#mec_book_form_date'.$uniqueid.'").val());
});' : '').'
</script>';

$javascript = apply_filters('mec-javascript-code-of-booking-module', $javascript, $uniqueid);

// Include javascript code into the footer
if($this->is_ajax()) echo $javascript;
else
{
    $factory = $this->getFactory();
    $factory->params('footer', $javascript);
}
?>
<div class="mec-booking <?php echo ($from_shortcode ? 'mec-booking-shortcode' : ''); ?>" id="mec_booking<?php echo $uniqueid; ?>">
    <?php
        include MEC::import('app.modules.booking.steps.tickets', true, true);
    ?>
</div>
<div id="mec_booking_message<?php echo $uniqueid; ?>" class="mec-util-hidden"></div>