;(function( $ ){
    $( function(){
        $( '#wpbody' ).on( 'click', '.notice.wicked-dismissable .wicked-dismiss', function(){
            $( this ).parents( '.notice' ).slideUp();
            var key = $( this ).attr( 'data-key' );
            $.ajax(
                ajaxurl,
                {
                    data: {
                        'action':   'wicked_folders_dismiss_message',
                        'key':      key
                    },
                    method: 'POST',
                    dataType: 'json'
                }
            );
            return false;
        } );

        $( '#adminmenu #toplevel_page_wicked_folders_toggle a' ).click( function(){
            if ( $( 'body' ).hasClass( 'wicked-object-folder-pane' ) ) {
                $( 'body' ).removeClass( 'wicked-object-folder-pane' );
                $( 'body' ).trigger( 'wickedfolders:toggleFolderPane', [ false ] );
            } else {
                $( 'body' ).addClass( 'wicked-object-folder-pane' );
                $( 'body' ).trigger( 'wickedfolders:toggleFolderPane', [ true ] );
            }

            return false;
        } );
    } );
})( jQuery );
