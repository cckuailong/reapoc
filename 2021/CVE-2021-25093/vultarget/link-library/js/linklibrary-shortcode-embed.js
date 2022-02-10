(function ($) {

	function linklibrary_insert_shortcode() {

		var shortcode = linklibrary_get_selected_shortcode();
		var attributes = linklibrary_get_attributes( shortcode );
		var constructed = linklibrary_construct_shortcode( shortcode, attributes );

		window.send_to_editor( constructed );
	}

	function linklibrary_get_selected_shortcode() {
		return $( '#linklibrary_shortcode_selector' ).val();
	}

	function linklibrary_get_attributes( shortcode ) {
		var attrs = [];
		var inputs = linklibrary_get_shortcode_inputs( shortcode );
		$.each( inputs, function( index, el ) {
			if ( '' !== el.value && undefined !== el.value ) {
				attrs.push( el.name + '="' + el.value + '"' );
			}
		});
		return attrs;
	}

	function linklibrary_get_shortcode_inputs( shortcode ) {
		return $( '.text, .select', '#' + shortcode + '_wrapper' );
	}

	function linklibrary_construct_shortcode( shortcode, attributes ) {
		var output = '[';
		output += shortcode;

		if ( attributes ) {
			for( i = 0; i < attributes.length; i++ ) {
				output += ' ' + attributes[i];
			}

			$.trim( output );
		}
		output += ']';

		return output;
	}

	function linklibrary_shortcode_hide_all_sections() {
		$( '.linklibrary-shortcode-section' ).hide();
	}

	function linklibrary_shortcode_show_section( section_name ) {
		$( '#' + section_name + '_wrapper' ).show();
	}

	linklibrary_shortcode_hide_all_sections();
	linklibrary_shortcode_show_section( linklibrary_get_selected_shortcode() );

	// Listen for changes to the selected shortcode
	$( '#linklibrary_shortcode_selector' ).on( 'change', function() {
		linklibrary_shortcode_hide_all_sections();
		linklibrary_shortcode_show_section( linklibrary_get_selected_shortcode() );
	}).change();

	// Listen for clicks on the "insert" button
	$( '#linklibrary_insert' ).on( 'click', function( e ) {
		e.preventDefault();
		linklibrary_insert_shortcode();
	});

	// Listen for clicks on the "cancel" button
	$( '#linklibrary_cancel' ).on( 'click', function( e ) {
		e.preventDefault();
		tb_remove();
	});

	// Listen for clicks to open the shortcode picker
	$( '#insert_linklibrary_shortcodes' ).on( 'click', function(e) {
		var inputs = $( '.select2-container' );
		$.each( inputs, function( index, el ){
			$( el ).select2( 'val', '' );
		});
	});

	// Resize ThickBox when "Add linklibrary Shortcode" link is clicked
	$('body').on( 'click', '#insert_linklibrary_shortcodes', function(e) {
		e.preventDefault();
		linklibrary_shortcode_setup_thickbox( $(this) );
	});

	// Resize shortcode thickbox on window resize
	$(window).resize(function() {
		linklibrary_shortcode_resize_tb( $('#insert_linklibrary_shortcodes') );
	});

	// Add a custom class to our shortcode thickbox, then resize
	function linklibrary_shortcode_setup_thickbox( link ) {
		setTimeout( function() {
		$('#TB_window').addClass('linklibrary-shortcode-thickbox');
			linklibrary_shortcode_resize_tb( link );
		}, 0 );
	}

	// Force shortcode thickboxes to our specified width/height
	function linklibrary_shortcode_resize_tb( link ) {
		setTimeout( function() {

		var width = link.attr('data-width');

		$('.linklibrary-shortcode-thickbox').width( width );

		var containerheight = $('.linklibrary-shortcode-thickbox').height();

		$(
			'.linklibrary-shortcode-thickbox #TB_ajaxContent,' +
			'.linklibrary-shortcode-thickbox .wrap'
		).width( ( width - 50 ) )
		 .height( ( containerheight - 50 ) );

		}, 0 );
	}

}(jQuery));
