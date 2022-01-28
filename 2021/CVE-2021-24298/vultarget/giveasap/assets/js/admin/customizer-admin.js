(function ( $, customize ) {

    /**
     * TinyMCE Custom Control
     *
     * @author Aurooba Ahmed
     * @license http://www.gnu.org/licenses/gpl-2.0.html
     *
     */
    function tinyMCE_setup() {
        console.log($( this ).attr( 'id' ));
        // Get the toolbar strings that were passed from the PHP Class
        var tinyMCEToolbar1 = _wpCustomizeSettings.controls[$( this ).attr( 'id' )].sg_tinymce_toolbar1;

        var tinyMCEToolbar2 = _wpCustomizeSettings.controls[$( this ).attr( 'id' )].sg_tinymce_toolbar2; // Get the media button condition passed from the PHP Class


        var tinyMCEMediaButtons = _wpCustomizeSettings.controls[$( this ).attr( 'id' )].sg_tinymce_mediabuttons; // Get the editor height passed from the PHP Class


        var tinyMCEheight = _wpCustomizeSettings.controls[$( this ).attr('id')].sg_tinymce_height; // Initialize editor with passed settings

        // initialize with unique ID (so you can have multiple) and with all the settings attributed correctly
        wp.editor.initialize( $( this ).attr( 'id' ), {
            tinymce: {
                wpautop: false,
                toolbar1: tinyMCEToolbar1,
                toolbar2: tinyMCEToolbar2,
                height: tinyMCEheight
            },
            quicktags: true,
            mediaButtons: tinyMCEMediaButtons
        });
    }

    function initialize_tinyMCE(event, editor) {
        editor.on( 'change', function () {
            tinyMCE.triggerSave();
            $( "#".concat( editor.id ) ).trigger( 'change' );
        });
    }

    function init() {
        wp.customize.panel( 'sg_mailtpl' ).focus();
        $('head title').html('Customize: Simple Giveaways');

        $( document ).on( 'tinymce-editor-init', initialize_tinyMCE );
        $( '.customize-control-tinymce-editor' ).each( tinyMCE_setup );
    }

    $(window).load(function () {
       init();
    });

})( jQuery );