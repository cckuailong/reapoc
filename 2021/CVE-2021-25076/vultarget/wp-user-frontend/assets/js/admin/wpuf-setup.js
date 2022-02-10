/*global wpuf_setup_params */
/*global wpuf_setup_currencies */
jQuery( function( $ ) {
	function blockWizardUI() {
		$('.wpuf-setup-content').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
	}

	$( '.button-next' ).on( 'click', function() {
		var form = $( this ).parents( 'form' ).get( 0 );

		if ( ( 'function' !== typeof form.checkValidity ) || form.checkValidity() ) {
			blockWizardUI();
		}

		return true;
	} );

	$( '.wpuf-wizard-services' ).on( 'change', '.wpuf-wizard-service-enable input', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( '.wpuf-wizard-service-toggle' ).removeClass( 'disabled' );
			$( this ).closest( '.wpuf-wizard-service-item' ).addClass( 'checked' );
			$( this ).closest( '.wpuf-wizard-service-item' )
				.find( '.wpuf-wizard-service-settings' ).removeClass( 'hide' );
		} else {
			$( this ).closest( '.wpuf-wizard-service-toggle' ).addClass( 'disabled' );
			$( this ).closest( '.wpuf-wizard-service-item' ).removeClass( 'checked' );
			$( this ).closest( '.wpuf-wizard-service-item' )
				.find( '.wpuf-wizard-service-settings' ).addClass( 'hide' );
		}
	} );

	$( '.wpuf-wizard-services' ).on( 'click', '.wpuf-wizard-service-enable', function( e ) {
		var eventTarget = $( e.target );

		if ( eventTarget.is( 'input' ) ) {
			e.stopPropagation();
			return;
		}

		var $checkbox = $( this ).find( 'input[type="checkbox"]' );

		$checkbox.prop( 'checked', ! $checkbox.prop( 'checked' ) ).change();
	} );

	$( '.wpuf-wizard-services-list-toggle' ).on( 'change', '.wpuf-wizard-service-enable input', function() {
		$( this ).closest( '.wpuf-wizard-services-list-toggle' ).toggleClass( 'closed' );
		$( this ).closest( '.wpuf-wizard-services' ).find( '.wpuf-wizard-service-item' )
			.slideToggle()
			.css( 'display', 'flex' );
	} );

	$( '.wpuf-wizard-services' ).on( 'change', '.wpuf-wizard-shipping-method-select .method', function( e ) {
		var zone = $( this ).closest( '.wpuf-wizard-service-description' );
		var selectedMethod = e.target.value;

		var description = zone.find( '.shipping-method-descriptions' );
		description.find( '.shipping-method-description' ).addClass( 'hide' );
		description.find( '.' + selectedMethod ).removeClass( 'hide' );

		var settings = zone.find( '.shipping-method-settings' );
		settings
			.find( '.shipping-method-setting' )
			.addClass( 'hide' )
			.find( '.shipping-method-required-field' )
			.prop( 'required', false );
		settings
			.find( '.' + selectedMethod )
			.removeClass( 'hide' )
			.find( '.shipping-method-required-field' )
			.prop( 'required', true );
	} );

	$( '.wpuf-wizard-services' ).on( 'change', '.wpuf-wizard-shipping-method-enable', function() {
		var checked = $( this ).is( ':checked' );

		$( this )
			.closest( '.wpuf-wizard-service-item' )
			.find( '.shipping-method-required-field' )
			.prop( 'required', checked );
	} );

	function submitActivateForm() {
		$( 'form.activate-jetpack' ).submit();
	}

	function waitForJetpackInstall() {
		wp.ajax.post( 'setup_wizard_check_jetpack' )
			.then( function( result ) {
				// If we receive success, or an unexpected result
				// let the form submit.
				if (
					! result ||
					! result.is_active ||
					'yes' === result.is_active
				) {
					return submitActivateForm();
				}

				// Wait until checking the status again
				setTimeout( waitForJetpackInstall, 3000 );
			} )
			.fail( function() {
				// Submit the form as normal if the request fails
				submitActivateForm();
			} );
	}

	// Wait for a pending Jetpack install to finish before triggering a "save"
	// on the activate step, which launches the Jetpack connection flow.
	$( '.activate-jetpack' ).on( 'click', '.button-primary', function( e ) {
		blockWizardUI();

		if ( 'no' === wpuf_setup_params.pending_jetpack_install ) {
			return true;
		}

		e.preventDefault();
		waitForJetpackInstall();
	} );

	$( '.wpuf-wizard-services' ).on( 'change', 'input#stripe_create_account', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( this ).closest( '.wpuf-wizard-service-settings' )
				.find( 'input.payment-email-input' )
				.prop( 'required', true );
			$( this ).closest( '.wpuf-wizard-service-settings' )
				.find( '.wpuf-wizard-service-setting-stripe_email' )
				.show();
		} else {
			$( this ).closest( '.wpuf-wizard-service-settings' )
				.find( 'input.payment-email-input' )
				.prop( 'required', false );
			$( this ).closest( '.wpuf-wizard-service-settings' )
				.find( '.wpuf-wizard-service-setting-stripe_email' )
				.hide();
		}
	} );

	$( '.wpuf-wizard-services input#stripe_create_account' ).change();

	$( 'select#store_country_state' ).on( 'change', function() {
		var countryCode = this.value.split( ':' )[ 0 ];
		$( 'select#currency_code' ).val( wpuf_setup_currencies[ countryCode ] ).change();
	} );
} );
