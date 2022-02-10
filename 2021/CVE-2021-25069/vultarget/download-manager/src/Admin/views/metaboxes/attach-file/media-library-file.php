<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div class="w3eden">
    <button type="button" class="btn btn-primary btn-block" id="attachml"><?php echo __( "Select from media library", "download-manager" ); ?></button>
    <script>
        jQuery(function ($) {
            var file_frame;
            $('body').on('click', '#attachml' , function( event ){
                event.preventDefault();
                if ( file_frame ) {
                    file_frame.open();
                    return;
                }
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: $( this ).data( 'uploader_title' ),
                    button: {
                        text: $( this ).data( 'uploader_button_text' )
                    },
                    multiple: false
                });
                file_frame.on( 'select', function() {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    console.log(attachment);
                    $('#wpdmfile').val(attachment.url);
                    $('#cfl').html('<div><strong>'+attachment.filename+'</strong><br/>'+attachment.filesizeHumanReadable).slideDown();
                    $('input[name="file[package_size]"]').val(attachment.filesizeHumanReadable);

                });
                file_frame.open();
            });
        });
    </script>
</div>
