(function ($) {
	$(function () {

		
		/****************  Start: Unzip Marker module *****************/

		$('#rvm_custom_marker_icon_module_unzipper_button').click( function(e) {
			e.preventDefault();
			//console.log( 'unzip marker module button  fired!' ) ; 

			// Get value of marker module path
			var rvm_custom_marker_icon_module_path = $('#rvm_option_custom_marker_icon_module_path').val();

			if (rvm_custom_marker_icon_module_path.length) {

				var ajax_loader = '<div class=\"rvm_ajax_loader\"><h1>' + objectL10n.unzipping + '</h1></div>';
				/*ajax_loader = ajax_loader + '<img src=\"' ;
				ajax_loader = ajax_loader + objectL10n.images_js_path ;
				ajax_loader = ajax_loader + '\/ajax-loader.gif"></div>' ;*/
				
				$('#rvm_marker_global_settings_message').fadeOut();
				$('#rvm_custom_marker_icon_module_unzip_progress').html(ajax_loader);

				var data = {

					action: 'rvm_custom_marker_icon_module', // The function for handling the request
					custom_marker_icon_module_path: rvm_custom_marker_icon_module_path, // marker
					nonce: $('#rvm_ajax_nonce').text() // The security nonce							

				};

				$.post(ajaxurl, data, function (response) {
					$('#rvm_custom_marker_icon_module_unzip_progress').html(response);

				});

			} else {
				alert( objectL10n.no_marker_module_selected );
			}


		}); // $( '#rvm_mbe_unzip_custom_map' ).click( function()  


		/****************  End: Unzip Custom Map *****************/



	});
})(jQuery);