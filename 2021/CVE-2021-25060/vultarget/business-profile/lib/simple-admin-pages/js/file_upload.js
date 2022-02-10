jQuery(document).ready(function($){
 
    var custom_uploader;
    var input_field;
    var preview_value;
 
    jQuery( '.sap-file-upload-button' ).click(function(e) {
 
        e.preventDefault();

        input_field = jQuery( this ).parent().find( 'input[type="hidden"]' );
        preview_value = jQuery( this ).parent().find( '.sap-file-upload-preview-value' );
 
        //If the uploader object has already been created, reopen the dialog
        if ( custom_uploader ) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose File',
            button: {
                text: 'Choose File'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on( 'select', function() {

            attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
            input_field.val( attachment.url );
            preview_value.html( attachment.url );
        });
 
        //Open the uploader dialog
        custom_uploader.open();
 
    });
});