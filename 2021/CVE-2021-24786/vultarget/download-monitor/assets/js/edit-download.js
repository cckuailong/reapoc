jQuery( function ( $ ) {

    // Expand all files
    jQuery( '.expand_all' ).click( function () {
        jQuery( this ).closest( '.dlm-metaboxes-wrapper' ).find( '.dlm-metabox table' ).show();
        return false;
    } );

    // Close all files
    jQuery( '.close_all' ).click( function () {
        jQuery( this ).closest( '.dlm-metaboxes-wrapper' ).find( '.dlm-metabox table' ).hide();
        return false;
    } );

    // Open/close
    jQuery( '.dlm-metaboxes-wrapper' ).on( 'click', '.dlm-metabox h3', function ( event ) {
        // If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
        if ( jQuery( event.target ).filter( ':input, option' ).length ) return;

        jQuery( this ).next( '.dlm-metabox-content' ).toggle();
    } );

    // Closes all to begin
    jQuery( '.dlm-metabox.closed' ).each( function () {
        jQuery( this ).find( '.dlm-metabox-content' ).hide();
    } );

    // Date picker
    jQuery( ".date-picker-field" ).datepicker( {
        dateFormat: "yy-mm-dd",
        numberOfMonths: 1,
        showButtonPanel: true,
    } );

    // Ordering
    jQuery( '.downloadable_files' ).sortable( {
        items: '.downloadable_file',
        cursor: 'move',
        axis: 'y',
        handle: 'h3',
        scrollSensitivity: 40,
        forcePlaceholderSize: true,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'dlm-metabox-sortable-placeholder',
        start: function ( event, ui ) {
            ui.item.css( 'background-color', '#f6f6f6' );
        },
        stop: function ( event, ui ) {
            ui.item.removeAttr( 'style' );
            downloadable_file_row_indexes();
        }
    } );

    function downloadable_file_row_indexes() {
        jQuery( '.downloadable_files .downloadable_file' ).each( function ( index, el ) {
            jQuery( '.file_menu_order', el ).val( parseInt( jQuery( el ).index( '.downloadable_files .downloadable_file' ) ) );
        } );
    };

    // Add a file
    jQuery( '.download_monitor_files' ).on( 'click', 'a.add_file', function () {

        jQuery( '.download_monitor_files' ).block( {
            message: null,
            overlayCSS: {
                background: '#fff url(' + $( '#dlm-plugin-url' ).val() + '/assets/images/ajax-loader.gif) no-repeat center',
                opacity: 0.6
            }
        } );

        var size = jQuery( '.downloadable_files .downloadable_file' ).size();

        var data = {
            action: 'download_monitor_add_file',
            post_id: $( '#dlm-post-id' ).val(),
            size: size,
            security: $( '#dlm-ajax-nonce-add-file' ).val()
        };

        jQuery.post( ajaxurl, data, function ( response ) {

            jQuery( '.downloadable_files' ).prepend( response );

            downloadable_file_row_indexes();

            jQuery( '.download_monitor_files' ).unblock();

            // Date picker
            jQuery( ".date-picker-field" ).datepicker( {
                dateFormat: "yy-mm-dd",
                numberOfMonths: 1,
                showButtonPanel: true
            } );
        } );

        return false;

    } );

    // Remove a file
    jQuery( '.download_monitor_files' ).on( 'click', 'button.remove_file', function ( e ) {
        e.preventDefault();
        var answer = confirm( dlm_ed_strings.confirm_delete );
        if ( answer ) {

            var el = jQuery( this ).closest( '.downloadable_file' );
            var file_id = el.attr( 'data-file' );

            if ( file_id > 0 ) {

                jQuery( el ).block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff url(' + $( '#dlm-plugin-url' ).val() + '/assets/images/ajax-loader.gif) no-repeat center',
                        opacity: 0.6
                    }
                } );

                var data = {
                    action: 'download_monitor_remove_file',
                    file_id: file_id,
                    download_id: $( '#dlm-post-id' ).val(),
                    security: $( '#dlm-ajax-nonce-remove-file' ).val()
                };

                jQuery.post( ajaxurl, data, function ( response ) {
                        jQuery( el ).fadeOut( '300' ).remove();
                    }
                )
                ;

            } else {
                jQuery( el ).fadeOut( '300' ).remove();
            }
        }
        return false;
    } );

    // Browse for file
    jQuery( '.download_monitor_files' ).on( 'click', 'a.dlm_browse_for_file', function ( e ) {

        downloadable_files_field = jQuery( this ).closest( '.downloadable_file' ).find( 'textarea[name^="downloadable_file_urls"]' );

        window.send_to_editor = window.send_to_browse_file_url;

        tb_show( dlm_ed_strings.browse_file, 'media-upload.php?post_id=' + $( '#dlm-post-id' ).val() + '&amp;type=downloadable_file_browser&amp;from=wpdlm01&amp;TB_iframe=true' );

        return false;
    } );

    window.send_to_browse_file_url = function ( html ) {

        if ( html ) {
            old = jQuery.trim( jQuery( downloadable_files_field ).val() );
            if ( old ) old = old + "\n";
            jQuery( downloadable_files_field ).val( old + html );
        }

        tb_remove();

        window.send_to_editor = window.send_to_editor_default;
    }

    // Uploading files
    var dlm_upload_file_frame;

    jQuery( document ).on( 'click', '.dlm_upload_file', function ( event ) {

        var $el = $( this );
        var $file_path_field = $el.parent().parent().find( '.downloadable_file_urls' );
        var file_paths = $file_path_field.val();

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( dlm_upload_file_frame ) {
            dlm_upload_file_frame.close();
        }

        var downloadable_file_states = [
            // Main states.
            new wp.media.controller.Library( {
                library: wp.media.query(),
                multiple: true,
                title: $el.data( 'choose' ),
                priority: 20,
                filterable: 'uploaded',
            } )
        ];

        // Create the media frame.
        dlm_upload_file_frame = wp.media.frames.downloadable_file = wp.media( {
            // Set the title of the modal.
            title: $el.data( 'choose' ),
            library: {
                type: ''
            },
            button: {
                text: $el.data( 'update' ),
            },
            multiple: true,
            states: downloadable_file_states,
        } );

        // When an image is selected, run a callback.
        dlm_upload_file_frame.on( 'select', function () {

            var selection = dlm_upload_file_frame.state().get( 'selection' );

            selection.map( function ( attachment ) {

                attachment = attachment.toJSON();

                if ( attachment.url )
                    file_paths = file_paths ? file_paths + "\n" + attachment.url : attachment.url

            } );

            $file_path_field.val( file_paths );
        } );

        // Set post to 0 and set our custom type
        dlm_upload_file_frame.on( 'ready', function () {
            dlm_upload_file_frame.uploader.options.uploader.params = {
                type: 'dlm_download'
            };
        } );

        // Finally, open the modal.
        dlm_upload_file_frame.open();
    } );

} );