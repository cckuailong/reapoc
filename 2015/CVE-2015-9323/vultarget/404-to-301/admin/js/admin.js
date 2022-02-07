(function($) {
    'use strict';

    /**
     * All of the code for our admin-specific JavaScript source
     * should reside in this file.
     *
     * Note that this assume you're going to use jQuery, so it prepares
     * the $ function reference to be used within the scope of this
     * function.
     *
     * From here, we are able to define handlers for when the DOM is
     * ready:
     *
     * $(function() {
     *
     * });
     *
     * Or when the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and so on.
     */
    $(function() {
        $('#i4t3_redirect_to').change(function() {
            var redirect_to = $(this).val();
            if(redirect_to == 'page') {
                $('#custom_page').show();
                $('#custom_url').hide();
            } else if(redirect_to == 'link') {
                $('#custom_url').show();
                $('#custom_page').hide();
            } else if(redirect_to == 'none') {
                $('#custom_page').hide();
                $('#custom_url').hide();
            }
        })
    })
})(jQuery);