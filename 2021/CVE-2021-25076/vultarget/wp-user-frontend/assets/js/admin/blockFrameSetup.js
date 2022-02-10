// moved to separate file so we could enqueue it and make sure jquery was loaded
(function() {
    jQuery( document ).ready( function() {
        var frameEl = window.frameElement;

        // get the form element
        var $form = jQuery('.wpuf-form-add');
        // get the height of the form
        var height = $form.find( '.wpuf-form' ).outerHeight(true);

        if (frameEl) {
            frameEl.height = height + 200;
        }
    });
})();