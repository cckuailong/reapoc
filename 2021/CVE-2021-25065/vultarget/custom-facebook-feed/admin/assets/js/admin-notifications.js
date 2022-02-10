/**
 * CFF Admin Notifications.
 *
 * @since 2.18
 */

'use strict';

var CFFAdminNotifications = window.CFFAdminNotifications || ( function( document, window, $ ) {

	/**
	 * Elements holder.
	 *
	 * @since 2.18
	 *
	 * @type {object}
	 */
	var el = {

		$notifications:    $( '#cff-notifications' ),
		$nextButton:       $( '#cff-notifications .navigation .next' ),
		$prevButton:       $( '#cff-notifications .navigation .prev' ),
		$adminBarCounter:  $( '#wp-admin-bar-wpforms-menu .cff-menu-notification-counter' ),
		$adminBarMenuItem: $( '#wp-admin-bar-cff-notifications' ),

	};

	/**
	 * Public functions and properties.
	 *
	 * @since 2.18
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 2.18
		 */
		init: function() {
			el.$notifications.find( '.messages a').each(function() {
				if ($(this).attr('href').indexOf('dismiss=') > -1 ) {
					$(this).addClass('button-dismiss');
				}
			})

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 2.18
		 */
		ready: function() {

			app.updateNavigation();
			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 2.18
		 */
		events: function() {

			el.$notifications
				.on( 'click', '.dismiss', app.dismiss )
				.on( 'click', '.button-dismiss', app.buttonDismiss )
				.on( 'click', '.next', app.navNext )
				.on( 'click', '.prev', app.navPrev );
		},

		/**
		 * Click on a dismiss button.
		 *
		 * @since 2.18
		 */
		buttonDismiss: function( event ) {
			event.preventDefault();
			app.dismiss();
		},

		/**
		 * Click on the Dismiss notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		dismiss: function( event ) {

			if ( el.$currentMessage.length === 0 ) {
				return;
			}

			// Update counter.
			var count = parseInt( el.$adminBarCounter.text(), 10 );
			if ( count > 1 ) {
				--count;
				el.$adminBarCounter.html( '<span>' + count + '</span>' );
			} else {
				el.$adminBarCounter.remove();
				el.$adminBarMenuItem.remove();
			}

			// Remove notification.
			var $nextMessage = el.$nextMessage.length < 1 ? el.$prevMessage : el.$nextMessage,
				messageId = el.$currentMessage.data( 'message-id' );

			if ( $nextMessage.length === 0 ) {
				el.$notifications.remove();
			} else {
				el.$currentMessage.remove();
				$nextMessage.addClass( 'current' );
				app.updateNavigation();
			}

			// AJAX call - update option.
			var data = {
				action: 'cff_dashboard_notification_dismiss',
				nonce: cff_admin.nonce,
				id: messageId,
			};

			$.post( cff_admin.ajax_url, data, function( res ) {

				if ( ! res.success ) {
					//CFFAdmin.debug( res );
				}
			} ).fail( function( xhr, textStatus, e ) {

				//CFFAdmin.debug( xhr.responseText );
			} );
		},

		/**
		 * Click on the Next notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		navNext: function( event ) {

			if ( el.$nextButton.hasClass( 'disabled' ) ) {
				return;
			}

			el.$currentMessage.removeClass( 'current' );
			el.$nextMessage.addClass( 'current' );

			app.updateNavigation();
		},

		/**
		 * Click on the Previous notification button.
		 *
		 * @since 2.18
		 *
		 * @param {object} event Event object.
		 */
		navPrev: function( event ) {

			if ( el.$prevButton.hasClass( 'disabled' ) ) {
				return;
			}

			el.$currentMessage.removeClass( 'current' );
			el.$prevMessage.addClass( 'current' );

			app.updateNavigation();
		},

		/**
		 * Update navigation buttons.
		 *
		 * @since 2.18
		 */
		updateNavigation: function() {

			el.$currentMessage = el.$notifications.find( '.message.current' );
			el.$nextMessage = el.$currentMessage.next( '.message' );
			el.$prevMessage = el.$currentMessage.prev( '.message' );

			if ( el.$nextMessage.length === 0 ) {
				el.$nextButton.addClass( 'disabled' );
			} else {
				el.$nextButton.removeClass( 'disabled' );
			}

			if ( el.$prevMessage.length === 0 ) {
				el.$prevButton.addClass( 'disabled' );
			} else {
				el.$prevButton.removeClass( 'disabled' );
			}
		},
	};

	return app;

}( document, window, jQuery ) );

// Initialize.
CFFAdminNotifications.init();
