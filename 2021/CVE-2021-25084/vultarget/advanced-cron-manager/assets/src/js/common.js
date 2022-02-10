advanced_cron_manager.notify = function( notification, icon ) {

	if ( typeof icon == 'undefined' ) {
		icon = '';
	} else {
		icon = '<span class="dashicons dashicons-' + icon + '"></span>';
	}

	Materialize.toast( icon + notification, 4000 );

};

advanced_cron_manager.ajax_messages = function( response ) {

	if ( response.success == true ) {
		advanced_cron_manager.notify( response.data, 'yes' );
	} else {
		jQuery.each( response.data, function( number, error ) {
			advanced_cron_manager.notify( error, 'warning' );
		} );
	}

};

function ACM_Slidebar() {
	this.container    = jQuery( '.slidebar' );
	this.overlay      = jQuery( '.slidebar-overlay' );
	this.close_button = jQuery( '.slidebar .close' );

	this.close_button.click( { slidebar: this }, function( event ) {
		event.data.slidebar.close();
	} );

	this.overlay.click( { slidebar: this }, function( event ) {
		event.data.slidebar.close();
	} );

	this.open = function() {

		this.container.animate( {
			'margin-right': 0
		}, 400, 'easeInOutSine' );

		this.overlay.fadeIn( 400 );

	};

	this.close = function() {

		var $form = this.container.find( '.content .form' );

		this.container.animate( {
			'margin-right': '-' + ( this.container.outerWidth() + 5 )
		}, 400, 'easeInOutSine', function () {
			$form.html( '' );
		} );

		this.overlay.fadeOut( 400 );

	};

	this.wait = function() {
		this.container.find( '.content' ).addClass( 'loading' );
	};

	this.fulfill = function( html ) {
		this.container.find( '.content .form' ).html( html );
		this.container.find( '.content' ).removeClass( 'loading' );
	};

	this.form_process_start = function( html ) {
		this.container.find( '.content .send-form' ).attr( 'disabled', true );
		this.container.find( '.content .spinner' ).css( 'visibility', 'visible' );
	};

	this.form_process_stop = function( html ) {
		this.container.find( '.content .send-form' ).attr( 'disabled', false );
		this.container.find( '.content .spinner' ).css( 'visibility', 'hidden' );
	};

};

advanced_cron_manager.slidebar = new ACM_Slidebar;
