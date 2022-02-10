(function($) {
    $(document).ready(function() {
        // Adds a new spoiler-button to the editor.
        $(document).on('tinymce-editor-setup', function(event, editor) {
            editor.settings.toolbar1 += ',spoiler';

            editor.addButton('spoiler', {
                icon: 'spoiler',
                tooltip: 'Spoiler',
                onclick: function() {
                    editor.insertContent('[spoiler title=""][/spoiler]');
                }
            });
        });

        // Toggles a clicked spoiler.
        $('#af-wrapper .spoiler .spoiler-head').click(function() {
    		$(this).toggleClass('closed').toggleClass('opened').next().toggle();
    	});
    });
})(jQuery);
