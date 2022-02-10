/**
 * Javascript functions for Image component
 *
 * @package Simple Admin Pages
 */

jQuery(document).ready(function ($) {

	var current_setting_id;

	function openMediaManager(e) {
		e.stopPropagation();
		e.preventDefault();
		current_setting_id = $( this ).parents( '.sap-image-wrapper' ).data( 'id' );
		wp.media.frames.sap_frame.open();
	}

	function setImage( setting_id, image_id, image_url ) {
		var $control = $( '.sap-image-wrapper[data-id="' + setting_id + '"]' );
		$control.find( 'img' ).attr( 'src', image_url );
		$control.find( '#' + setting_id ).val( image_id );
		$control.removeClass( 'sap-image-wrapper-no-image' ).addClass( 'sap-image-wrapper-has-image' );
	}

	function removeImage(e) {
		e.stopPropagation();
		e.preventDefault();
		var $control = $( this ).parents( '.sap-image-wrapper' );
		$control.find( 'img' ).attr( 'src', '' );
		$control.find( '#' + $control.data( 'id' ) ).val( '' );
		$control.removeClass( 'sap-image-wrapper-has-image' ).addClass( 'sap-image-wrapper-no-image' );
	}

	wp.media.frames.sap_frame = wp.media( {
		title: 'Select image',
		multiple: false,
		library: {
			type: 'image',
		},
		button: {
			text: 'Use selected image',
		},
	} );

	wp.media.frames.sap_frame.on( 'select', function() {
		var image = wp.media.frames.sap_frame.state().get( 'selection' ).first().toJSON();
		setImage( current_setting_id, image.id, image.url );
	});

	$( '.sap-image-wrapper .sap-image-btn-add, .sap-image-wrapper .sap-image-btn-change' ).click( openMediaManager );

	$( '.sap-image-wrapper .sap-image-btn-remove' ).click( removeImage );
});
