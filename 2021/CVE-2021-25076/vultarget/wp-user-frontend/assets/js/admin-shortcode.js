/*jshint devel:true */
/*global send_to_editor */
/*global tb_remove */

jQuery(function($) {

    $('#wpuf-form-insert').on('click', function(e) {
        e.preventDefault();

        var shortcode  = '',
            type       = $('#wpuf-form-type').val();

        if ( type === 'post' ) {
            var post    = $('#wpuf-form-post').val();
            shortcode += '[wpuf_form id="' + post + '"]';
        } else {
            var registration = $('#wpuf-form-registration').val();
            shortcode += '[wpuf_profile type="registration" id="' + registration + '"] [wpuf_profile type="profile" id="' + registration + '"]';
        }

        send_to_editor(shortcode);
        tb_remove();
    });

    $('#wpuf-form-type').on('change', function() {
        var val = $(this).val();

        if ( val === 'post' ) {
            $('.show-if-post').show();
            $('.show-if-registration').hide();
        } else {
            $('.show-if-post').hide();
            $('.show-if-registration').show();
        }
    });

    $('#wpuf-form-type').trigger('change');
});