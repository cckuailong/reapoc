jQuery(document).ready(function(){

    jQuery('#pmpro-sendwp-connect').on( 'click', function(e) {
        e.preventDefault();
        document.body.style.cursor = 'wait';
        paid_memberships_pro_sendwp_remote_install();
    });

    jQuery('#pmpro-sendwp-disconnect').on( 'click', function(e) {
        e.preventDefault();
        document.body.style.cursor = 'wait';
        paid_memberships_pro_sendwp_disconnect();
    });

    function paid_memberships_pro_sendwp_remote_install() {
        var data = {
            'action': 'paid_memberships_pro_sendwp_remote_install',
            'sendwp_nonce': paid_memberships_pro_sendwp_vars.nonce
        };
        
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {
            var data = JSON.parse(response);
            //Check for errors before calling paid_memberships_pro_sendwp_register_client()
            if( data.error ){
                // @todo update "#wpbody-content" selector to a more meaningful location inside of your plugin's admin. 
                var message;
    
                if( data.debug === '!security'){
                    message = paid_memberships_pro_sendwp_vars.security_failed_message;
                } else if( data.debug === '!user_capablity'){
                    message = paid_memberships_pro_sendwp_vars.user_capability_message;
                } else if( data.debug === 'sendwp_connected'){
                    message = paid_memberships_pro_sendwp_vars.sendwp_connected_message;
                } else {
                    message = 'error!';
                }

                jQuery('#pmpro-sendwp-description').text( message );
                document.body.style.cursor = 'default';
    
            } else {
                paid_memberships_pro_sendwp_register_client(data.register_url, data.client_name, data.client_secret, data.client_redirect, data.partner_id, data.client_url);
            }
            
        });
    }
    
    function paid_memberships_pro_sendwp_register_client(register_url, client_name, client_secret, client_redirect, partner_id, client_url) {
    
        var form = document.createElement("form");
        form.setAttribute("method", 'POST');
        form.setAttribute("action", register_url);
    
        function paid_memberships_pro_sendwp_append_form_input(name, value) {
            var input = document.createElement("input");
            input.setAttribute("type", "hidden");
            input.setAttribute("name", name);
            input.setAttribute("value", value);
            form.appendChild(input);
        }
    
        paid_memberships_pro_sendwp_append_form_input('client_name', client_name);
        paid_memberships_pro_sendwp_append_form_input('client_secret', client_secret);
        paid_memberships_pro_sendwp_append_form_input('client_redirect', client_redirect); 
        paid_memberships_pro_sendwp_append_form_input('partner_id', partner_id);
        paid_memberships_pro_sendwp_append_form_input('client_url', client_url); 
        
        document.body.appendChild(form);
        form.submit();
    }

    function paid_memberships_pro_sendwp_disconnect() {

        var data = {
            'action': 'paid_memberships_pro_sendwp_disconnect',
            'sendwp_nonce': paid_memberships_pro_sendwp_vars.nonce
        };

        jQuery.post(ajaxurl, data, function( response ) {
            location.reload();
        });

    }

});