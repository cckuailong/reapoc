jQuery( function ( $ ) {

	$( '#setting-dlm_default_template' ).change( function () {
		if ( $( this ).val() === 'custom' ) {
			$( '#setting-dlm_custom_template' ).closest( 'tr' ).show();
		} else {
			$( '#setting-dlm_custom_template' ).closest( 'tr' ).hide();
		}
	} ).change();

	$( '#setting-dlm_enable_logging' ).change( function () {
		if ( $( this ).is( ":checked" ) === true ) {
			$( '#setting-dlm_count_unique_ips' ).closest( 'tr' ).show();
		} else {
			$( '#setting-dlm_count_unique_ips' ).closest( 'tr' ).hide();
		}
	} ).change();

	$( document ).ready( function () {

		// load lazy-select elements
		$.each( $( '.dlm-lazy-select' ), function () {

			var lazy_select_el = $( this );

			// add AJAX loader
			$( '<span>' ).addClass( 'dlm-lazy-select-loader' ).append(
				$( '<img>' ).attr( 'src', dlm_settings_vars.img_path + 'ajax-loader.gif' )
			).insertAfter( lazy_select_el );

			// load data
			$.post( ajaxurl, {
				action: 'dlm_settings_lazy_select',
				nonce: dlm_settings_vars.lazy_select_nonce,
				option: lazy_select_el.attr( 'name' )
			}, function ( response ) {

				// remove current option(s)
				lazy_select_el.find( 'option' ).remove();

				// set new options
				if ( response ) {
					var selected = lazy_select_el.data( 'selected' );
					for ( var i = 0; i < response.length; i ++ ) {
						var opt = $( '<option>' ).attr( 'value', response[i].key ).html( response[i].lbl );
						if ( selected === response[i].key ) {
							opt.attr( 'selected', 'selected' );
						}
						lazy_select_el.append( opt );
					}
				}

				// remove ajax loader
				lazy_select_el.parent().find( '.dlm-lazy-select-loader' ).remove();

			} );


		} );

	} );

} );