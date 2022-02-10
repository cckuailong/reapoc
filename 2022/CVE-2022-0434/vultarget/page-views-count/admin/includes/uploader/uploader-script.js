(function ($) {

a3Uploader = {

/*-----------------------------------------------------------------------------------*/
/* Remove file when the "remove" button is clicked.
/*-----------------------------------------------------------------------------------*/

	removeFile: function () {
		$(document).on( 'click', '.a3_uploader_remove', function(event) {
			$(this).hide();
			$(this).parents().parents().children('.a3_upload').val( '' ).trigger('change');
			$(this).parents().parents().children('.a3_upload_attachment_id').val(0);
			$(this).parents( '.a3_screenshot').slideUp();

			return false;
		});
	},

/*-----------------------------------------------------------------------------------*/
/* Use a custom function when working with the Media Uploads popup.
/* Requires jQuery, Media Upload and Thickbox JavaScripts.
/*-----------------------------------------------------------------------------------*/

	mediaUpload: function () {
		$.noConflict();

		var formfield, file_frame, formID, upload_title, insertImage, btnContent = true;

		$(document).on( 'click', 'input.a3_upload_button', function (e) {
			e.preventDefault();

			formfield = $(this).prev( 'input').attr( 'id' );
			formID = $(this).attr( 'rel' );
			upload_title =  $(this).prev( 'input').attr( 'rel' );

			var insertImage = wp.media.controller.Library.extend({
				defaults :  _.defaults({
					id: 'a3-insert-image',
					title: upload_title,
					filterable: 'uploaded',
					allowLocalEdits: true,
					displaySettings: true,
					displayUserSettings: true,
					multiple : false,
					type : 'image'
				}, wp.media.controller.Library.prototype.defaults )
			});

			/*
			if ( file_frame ) {
				file_frame.open();
				return;
			}
			*/

			file_frame = wp.media.frames.file_frame = wp.media({
				title: 'Select Image',
				button: {
			  		text: 'Use as ' + upload_title,
				},
				state : 'a3-insert-image',
				states : [
					new insertImage()
				],
				multiple: false
			});

			file_frame.open();

			file_frame.on( 'select', function() {
				var selection = file_frame.state().get('selection');
				var size = $('.attachment-display-settings .size').val();
				var attachment = selection.first().toJSON();
				if ( !size ) {
					attachment.url = attachment.url;
				} else {
					attachment.url = attachment.sizes[size].url;
				}
				var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
				var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
				var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
				var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;

				if (attachment.url.match(image)) {
					btnContent = '<img class="a3_uploader_image" src="'+attachment.url+'" alt="" /><a href="#" class="a3_uploader_remove a3-plugin-ui-delete-icon">&nbsp;</a>';
				} else {
					html = '<a href="'+attachment.url+'" target="_blank" rel="a3_external">View File</a>';
					btnContent = '<div class="a3_no_image"><span class="a3_file_link">'+html+'</span><a href="#" class="a3_uploader_remove a3-plugin-ui-delete-icon">&nbsp;</a></div>';
				}
				var strip_methods = $( '#' + formfield).data('strip-methods');
				if ( strip_methods === 0 ) {
					$( '#' + formfield).val(attachment.url).trigger('change');
				} else {
					$( '#' + formfield).val(attachment.url.replace(/http:|https:/, '' )).trigger('change');
				}
				$( '#' + formfield + '_attachment_id').val(attachment.id);
				$( '#' + formfield).siblings( '.a3_screenshot').slideDown().html(btnContent);
				$('.media-modal-close').trigger('click');
			});

			return false;

		});
	}
};

	$(document).ready(function () {

		a3Uploader.removeFile();
		a3Uploader.mediaUpload();

	});

})(jQuery);
