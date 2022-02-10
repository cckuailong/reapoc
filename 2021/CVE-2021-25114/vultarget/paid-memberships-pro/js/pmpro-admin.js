/**
 * Show a system prompt before redirecting to a URL.
 * Used for delete links/etc.
 * @param	text	The prompt, i.e. are you sure?
 * @param	url		The url to redirect to.
 */
 function pmpro_askfirst( text, url ) {
	var answer = window.confirm( text );

	if ( answer ) {
		window.location = url;
	}
}

/**
 * Deprecated in v2.1
 * In case add-ons/etc are expecting the non-prefixed version.
 */
if ( typeof askfirst !== 'function' ) {
    function askfirst( text, url ) {
        return pmpro_askfirst( text, url );
    }
}

/*
 * Toggle elements with a specific CSS class selector.
 * Used to hide/show sub settings when a main setting is enabled.
 * @since v2.1
 */
function pmpro_toggle_elements_by_selector( selector, checked ) {
	if( checked === undefined ) {
		jQuery( selector ).toggle();
	} else if ( checked ) {
		jQuery( selector ).show();
	} else {
		jQuery( selector ).hide();
	}
}

/*
 * Find inputs with a custom attribute pmpro_toggle_trigger_for,
 * and bind change to toggle the specified elements.
 * @since v2.1
 */
jQuery(document).ready(function() {
	jQuery( 'input[pmpro_toggle_trigger_for]' ).on( 'change', function() {
		pmpro_toggle_elements_by_selector( jQuery( this ).attr( 'pmpro_toggle_trigger_for' ), jQuery( this ).prop( 'checked' ) );
	});
});

/** JQuery to hide the notifications. */
jQuery(document).ready(function(){
	jQuery(document).on( 'click', '.pmpro-notice-button.notice-dismiss', function() {
		var notification_id = jQuery( this ).val();

		var postData = {
			action: 'pmpro_hide_notice',
			notification_id: notification_id
		}

		jQuery.ajax({
			type: "POST",
			data: postData,
			url: ajaxurl,
			success: function( response ) {
				///console.log( notification_id );
				jQuery('#'+notification_id).hide();
			}
		})
	
	});
});

/*
 * Create Webhook button for Stripe on the payment settings page.
 */
jQuery(document).ready(function() {
	// Check that we are on payment settings page.
	if ( ! jQuery( '#stripe_publishablekey' ).length || ! jQuery( '#stripe_secretkey' ).length || ! jQuery( '#pmpro_stripe_create_webhook' ).length ) {
		return;
	}

    // Disable the webhook buttons if the API keys aren't complete yet.
    jQuery('#stripe_publishablekey,#stripe_secretkey').on('change keyup', function() {
        pmpro_stripe_check_api_keys();
    });
	pmpro_stripe_check_api_keys();
    
    // AJAX call to create webhook.
	jQuery('#pmpro_stripe_create_webhook').on( 'click', function(event){
        event.preventDefault();
                
		var postData = {
			action: 'pmpro_stripe_create_webhook',
			secretkey: pmpro_stripe_get_secretkey(),
		}
		jQuery.ajax({
			type: "POST",
			data: postData,
			url: ajaxurl,
			success: function( response ) {
				response = jQuery.parseJSON( response );
                ///console.log( response );
                
                jQuery( '#pmpro_stripe_webhook_notice' ).parent('div').removeClass('error')
                jQuery( '#pmpro_stripe_webhook_notice' ).parent('div').removeClass('notice-success')
                
                if ( response.notice ) {
                    jQuery('#pmpro_stripe_webhook_notice').parent('div').addClass(response.notice);
                }
                if ( response.message ) {
                    jQuery('#pmpro_stripe_webhook_notice').html(response.message);
                }
                if ( response.success ) {
                    jQuery('#pmpro_stripe_create_webhook').hide();
                }
			}
		})
    });
    
    // AJAX call to delete webhook.
	jQuery('#pmpro_stripe_delete_webhook').on( 'click', function(event){
        event.preventDefault();
                
		var postData = {
			action: 'pmpro_stripe_delete_webhook',
			secretkey: pmpro_stripe_get_secretkey(),
		}

		jQuery.ajax({
			type: "POST",
			data: postData,
			url: ajaxurl,
			success: function( response ) {
				response = jQuery.parseJSON( response );
                ///console.log( response );
                
                jQuery( '#pmpro_stripe_webhook_notice' ).parent('div').removeClass('error')
                jQuery( '#pmpro_stripe_webhook_notice' ).parent('div').removeClass('notice-success')
                
                if ( response.notice ) {
                    jQuery('#pmpro_stripe_webhook_notice').parent('div').addClass(response.notice);
                }
                if ( response.message ) {
                    jQuery('#pmpro_stripe_webhook_notice').html(response.message);
                }
                if ( response.success ) {
                    jQuery('#pmpro_stripe_create_webhook').show();
                }				
			}
		})
	});

	// AJAX call to rebuild webhook.
	jQuery('#pmpro_stripe_rebuild_webhook').on( 'click', function(event){
        event.preventDefault();
                
		var postData = {
			action: 'pmpro_stripe_rebuild_webhook',
			secretkey: pmpro_stripe_get_secretkey(),
		}

		jQuery.ajax({
			type: "POST",
			data: postData,
			url: ajaxurl,
			success: function( response ) {
				response = jQuery.parseJSON( response );
                ///console.log( response );
                
                jQuery( '#pmpro_stripe_webhook_notice' ).parent('div').removeClass('error')
                jQuery( '#pmpro_stripe_webhook_notice' ).parent('div').removeClass('notice-success')
                
                if ( response.notice ) {
                    jQuery('#pmpro_stripe_webhook_notice').parent('div').addClass(response.notice);
                }
                if ( response.message ) {
                    jQuery('#pmpro_stripe_webhook_notice').html(response.message);
                }
                if ( response.success ) {
                    jQuery('#pmpro_stripe_create_webhook').hide();
                }				
			}
		})
    });
});

// Disable the webhook buttons if the API keys aren't complete yet.
function pmpro_stripe_check_api_keys() {  
    if( ( jQuery('#stripe_publishablekey').val().length > 0 && jQuery('#stripe_secretkey').val().length > 0 ) || jQuery('#live_stripe_connect_secretkey').val().length > 0 ) {
        jQuery('#pmpro_stripe_create_webhook').removeClass('disabled');
        jQuery('#pmpro_stripe_create_webhook').addClass('button-secondary');
    } else {            
        jQuery('#pmpro_stripe_create_webhook').removeClass('button-secondary');
        jQuery('#pmpro_stripe_create_webhook').addClass('disabled');
    }
}

function pmpro_stripe_get_secretkey() {
    // We can't do the webhook calls with the Connect keys anyway,
    // so we just look for the legacy key here.
    if ( jQuery('#stripe_secretkey').val().length > 0 ) {
		return jQuery('#stripe_secretkey').val();
	} else {
		return '';
	}
}

// EMAIL TEMPLATES.
jQuery(document).ready(function($) {
    
	/* Variables */
	var template, disabled, $subject, $editor, $testemail;
	$subject = $("#pmpro_email_template_subject").closest("tr");
	$editor = $("#wp-email_template_body-wrap");
	$testemail = $("#test_email_address").closest("tr");
	
    $(".hide-while-loading").hide();
    $(".controls").hide();

    /* PMPro Email Template Switcher */
    $("#pmpro_email_template_switcher").change(function() {
        
        $(".status_message").hide();
        template = $(this).val();
        
        //get template data
        if (template)
            pmpro_get_template(template);
        else {
            $(".hide-while-loading").hide();
            $(".controls").hide();
        }
    });

    $("#pmpro_submit_template_data").click(function() {
        pmpro_save_template()
    });

    $("#pmpro_reset_template_data").click(function() {
        pmpro_reset_template();
    });

    $("#pmpro_email_template_disable").click(function(e) {
        pmpro_disable_template();
    });

    $("#send_test_email").click(function(e) {       
		pmpro_save_template().done(setTimeout(function(){pmpro_send_test_email();}, '1000'));
    });

    /* Functions */
    function pmpro_get_template(template) {        
				
		//hide stuff and show ajax spinner
        $(".hide-while-loading").hide();
        $("#pmproet-spinner").show();

        //get template data
        $data = {
            template: template,
            action: 'pmpro_email_templates_get_template_data',
            security: $('input[name=security]').val()
        };

        //console.log( $data );

        $.post(ajaxurl, $data, function(response) {
            var template_data = JSON.parse(response);

            //show/hide stuff
			$("#pmproet-spinner").hide();
            $(".controls").show();
            $(".hide-while-loading").show();
            $(".status").hide();

            //change disable text
            if (template == 'header' || template === 'footer') {

                $subject.hide();
				$testemail.hide();
				
                if(template == 'header')
                    $("#disable_label").text("Disable email header for all PMPro emails?");
                else
                    $("#disable_label").text("Disable email footer for all PMPro emails?");

                //hide description
                $("#disable_description").hide();
            }
            else {
                $testemail.show();
				$("#disable_label").text("Disable this email?");
                $("#disable_description").show().text("PMPro emails with this template will not be sent.");
            }

            // populate help text, subject, and body
            $('#pmpro_email_template_help_text').text(template_data['help_text']);
			$('#pmpro_email_template_subject').val(template_data['subject']);
			$('#pmpro_email_template_body').val(template_data['body']);

            // disable form
            disabled = template_data['disabled'];
            pmpro_toggle_form_disabled(disabled);
        });
    }

    function pmpro_save_template() {

        $("#submit_template_data").attr("disabled", true);
        $(".status").hide();
        // console.log(template);

        $data = {
            template: template,
            subject: $("#pmpro_email_template_subject").val(),
            body: $("#pmpro_email_template_body").val(),
            action: 'pmpro_email_templates_save_template_data',
            security: $('input[name=security]').val()
        };
        $.post(ajaxurl, $data, function(response) {
            if(response != 0) {
                $(".status_message_wrapper").addClass('updated');
            }
            else {
                $(".status_message_wrapper").addClass("error");
            }
            $("#submit_template_data").attr("disabled", false);
            $(".status_message").html(response);
            $(".status").show();
            $(".status_message").show();
        });

		return $.Deferred().resolve();
    }

    function pmpro_reset_template() {

        var r = confirm('Are you sure? Your current template settings will be deleted permanently.');

        if(!r) return false;

        $data = {
            template: template,
            action: 'pmpro_email_templates_reset_template_data',
            security: $('input[name=security]').val()
        };
        $.post(ajaxurl, $data, function(response) {
            var template_data = $.parseJSON(response);
            $('#pmpro_email_template_subject').val(template_data['subject']);
            $('#pmpro_email_template_body').val(template_data['body']);
        });

        return true;
    }

    function pmpro_disable_template() {

        //update wp_options
        data = {
            template: template,
            action: 'pmpro_email_templates_disable_template',
            disabled: $("#pmpro_email_template_disable").is(":checked"),
            security: $('input[name=security]').val()
        };

        $.post(ajaxurl, data, function(response) {

            response = JSON.parse(response);

            //failure
            if(response['result'] == false) {
                $(".status_message_wrapper").addClass("error");
                $(".status_message").show().text("There was an error updating your template settings.");
            }
            else {
                if(response['status'] == 'true') {
                    $(".status_message_wrapper").addClass("updated");
                    $(".status_message").show().text("Template Disabled");
                }
                else {
                    $(".status_message_wrapper").addClass("updated");
                    $(".status_message").show().text("Template Enabled");
                }
            }

            $(".hide-while-loading").show();

            disabled = response['status'];

            pmpro_toggle_form_disabled(disabled);
        });
    }

    function pmpro_send_test_email() {

        //hide stuff and show ajax spinner
        $(".hide-while-loading").hide();
        $("#pmproet-spinner").show();

        data = {
            template: template,
            email: $("#test_email_address").val(),			
            action: 'pmpro_email_templates_send_test',
            security: $('input[name=security]').val()
        };

        $.post(ajaxurl, data, function(success) {
            //show/hide stuff
            $("#pmproet-spinner").hide();
            $(".controls").show();
            $(".hide-while-loading").show();

            if(success) {
                $(".status_message_wrapper").addClass("updated").removeClass("error");
                $(".status_message").show().text("Test email sent successfully.");
            }
            else {
                $(".status_message_wrapper").addClass("error").removeClass("updated");
                $(".status_message").show().text("Test email failed.");
            }

        })
    }

    function pmpro_toggle_form_disabled(disabled) {
        if(disabled == 'true') {
            $("#pmpro_email_template_disable").prop('checked', true);
            $("#pmpro_email_template_body").attr('readonly', 'readonly').attr('disabled', 'disabled');
            $("#pmpro_email_template_subject").attr('readonly', 'readonly').attr('disabled', 'disabled');
            $(".controls").hide();
        }
        else {
            $("#pmpro_email_template_disable").prop('checked', false);
            $("#pmpro_email_template_body").removeAttr('readonly','readonly').removeAttr('disabled', 'disabled');
            $("#pmpro_email_template_subject").removeAttr('readonly','readonly').removeAttr('disabled', 'disabled');
            $(".controls").show();
        }

    }

});
