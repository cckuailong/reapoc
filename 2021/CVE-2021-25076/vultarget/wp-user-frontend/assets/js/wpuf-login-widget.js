jQuery( function($) {

    $('.wpuf-ajax-reset-password-form').hide();

    var login_widget  = $('.login-widget-container');

    login_widget.on('click', '#wpuf-ajax-login-url', function(e) {
        e.preventDefault();

        $('.wpuf-ajax-login-form').show();
        $('.wpuf-ajax-reset-password-form').hide();
    });

    login_widget.on('click', '#wpuf-ajax-lost-pw-url', function(e) {
        e.preventDefault();

        $('.wpuf-ajax-reset-password-form').show();
        $('.wpuf-ajax-login-form').hide();
    });

    // Login
    login_widget.find( '#wpuf_ajax_login_form' ).on('submit', function(e) {
        e.preventDefault();

        var button = $(this).find('submit');
        form_data = $('#wpuf_ajax_login_form').serialize() + '&action=wpuf_ajax_login';

        $.ajax({
            url: wpuf_ajax.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: form_data
        })
        .done( function( response, textStatus, jqXHR ) {
            if ( response.success == false ) {
                $('.wpuf-ajax-login-form .wpuf-ajax-errors').append(response.data.message);
            } else {
                window.location.reload(true);
                button.hide();
            }
        } )
        .fail( function( jqXHR, textStatus, errorThrown ) {
            console.log( 'AJAX failed', errorThrown );
        } );
    });

    // Reset Password
    login_widget.find( '#wpuf_ajax_reset_pass_form' ).on('submit', function(e) {
        e.preventDefault();

        var button = $(this).find('submit');
        var form_data = $('#wpuf_ajax_reset_pass_form').serialize() + '&action=wpuf_lost_password';
        $.ajax({
            url: wpuf_ajax.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: form_data
        })
        .done( function( response, textStatus, jqXHR ) {
            $('.wpuf-ajax-reset-password-form .wpuf-ajax-message p').html(response.data.message);
        } )
        .fail( function( jqXHR, textStatus, errorThrown ) {
            console.log( 'AJAX failed', errorThrown );
        } );
    });

    // Logout
    login_widget.on('click', '#logout-url', function(e) {
        e.preventDefault();

        $.ajax({
            url: wpuf_ajax.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'wpuf_ajax_logout',
            },
            success: function(data) {
                $('.wpuf-ajax-logout .wpuf-ajax-errors').html(data.message);
                window.location.reload(true);
            }
        });
    });

});