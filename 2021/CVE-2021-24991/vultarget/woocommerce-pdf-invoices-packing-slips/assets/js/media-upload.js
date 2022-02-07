// Thanks to Mike Jolley!
// http://mikejolley.com/2012/12/using-the-new-wordpress-3-5-media-uploader-in-plugins/

jQuery(document).ready(function($) {
		
	// Uploading files
	var file_frame;
	$( '#wpo-wcpdf-settings, .wpo-wcpdf-setup' ).on( 'click', '.wpo_upload_image_button', function( event ){
		event.preventDefault();

		// get input wrapper
		let $settings_wrapper = $(this).parent();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}
		 
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false	// Set to true to allow multiple files to be selected
		});
	 
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// get target elements
			let $input            = $settings_wrapper.find( 'input#header_logo' );
			let $logo             = $settings_wrapper.find( 'img#img-header_logo' );

			// We set multiple to false so only get one image from the uploader
			let attachment = file_frame.state().get( 'selection' ).first().toJSON();
			
			// set the value of the input field to the attachment id and set the image until we have an ajax response
			$input.val( attachment.id );
			if ( $logo.length ) {
				$logo.attr( 'src', attachment.url );
			}
			$( '.attachment-resolution, .attachment-resolution-warning' ).remove();

			// dim until we have a response
			$settings_wrapper.css( 'opacity', '0.25' );
			
			let data = {
				security:      $input.data( 'ajax_nonce' ),
				action:        'wpo_wcpdf_get_media_upload_setting_html',
				args:          $input.data( 'settings_callback_args' ),
				attachment_id: attachment.id, 
			};
	
			xhr = $.ajax({
				type:    'POST',
				url:     wpo_wcpdf_admin.ajaxurl,
				data:    data,
				success: function( response ) {
					if ( response && typeof response.success != 'undefined' && response.success === true ) {
						$settings_wrapper.html( response.data );
					}
					$settings_wrapper.removeAttr( 'style' );	
				},
				error: function (xhr, ajaxOptions, thrownError) {
					$settings_wrapper.removeAttr( 'style' );	
				}
			});
	
		});
	 
		// Finally, open the modal
		file_frame.open();
	});
 
	$( '#wpo-wcpdf-settings' ).on( 'click', '.wpo_remove_image_button', function( event ){
		// get source & target elements
		let $settings_wrapper = $(this).parent();
		let $input            = $settings_wrapper.find( 'input#header_logo' );
		let $logo             = $settings_wrapper.find( 'img#img-header_logo' );
	 	
		// clear all inputs & warnings
		$input.val( '' );
		$logo.remove();
		$( this ).remove();
		$( '.attachment-resolution, .attachment-resolution-warning' ).remove();
	});		
});