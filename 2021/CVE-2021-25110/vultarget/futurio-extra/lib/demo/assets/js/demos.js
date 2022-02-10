( function( $ ) {

	"use strict";

	$( document ).ready( function() {
		fwpDemoImport.init();
	} );

	var fwpDemoImport = {

		importData: {},
		allowPopupClosing: true,

		init: function() {
			var that = this;

			// Categories filter
			this.categoriesFilter();

			// Search functionality.
			$( '.fwp-search-input' ).on( 'keyup', function() {
				if ( 0 < $( this ).val().length ) {
					// Hide all items.
					$( '.fwp-demo-wrap .themes' ).find( '.theme-wrap' ).hide();

					// Show just the ones that have a match on the import name.
					$( '.fwp-demo-wrap .themes' ).find( '.theme-wrap[data-name*="' + $( this ).val().toLowerCase() + '"]' ).show();
				} else {
					$( '.fwp-demo-wrap .themes' ).find( '.theme-wrap' ).show();
				}
			} );

			// Prevent the popup from showing when the live preview button
			$( '.fwp-demo-wrap .theme-actions a.button' ).on( 'click', function( e ) {
			   e.stopPropagation();
			} );

			// Get demo data
			$( '.fwp-open-popup' ).click( function( e ) {
				e.preventDefault();

				// Vars
				var $selected_demo 		= $( this ).data( 'demo-id' ),
					$loading_icon 		= $( '.preview-' + $selected_demo ),
					$disable_preview 	= $( '.preview-all-' + $selected_demo );

				$loading_icon.show();
				$disable_preview.show();
				
				that.getDemoData( $selected_demo );
			} );

			$( document ).on( 'click' 						, '.install-now', this.installNow );
			$( document ).on( 'click' 						, '.activate-now', this.activatePlugins );
			$( document ).on( 'wp-plugin-install-success'	, this.installSuccess );
			$( document ).on( 'wp-plugin-installing' 		, this.pluginInstalling );
			$( document ).on( 'wp-plugin-install-error'		, this.installError );

		},

		// Category filter.
		categoriesFilter: function() {

			// Cache selector to all items
			var $items 				= $( '.fwp-demo-wrap .themes' ).find( '.theme-wrap' ),
				fadeoutClass 		= 'fwp-is-fadeout',
				fadeinClass 		= 'fwp-is-fadein',
				animationDuration 	= 200;

			// Hide all items.
			var fadeOut = function () {
				var dfd = $.Deferred();

				$items.addClass( fadeoutClass );

				setTimeout( function() {
					$items.removeClass( fadeoutClass ).hide();

					dfd.resolve();
				}, animationDuration );

				return dfd.promise();
			};

			var fadeIn = function ( category, dfd ) {
				var filter = category ? '[data-categories*="' + category + '"]' : 'div';

				if ( 'all' === category ) {
					filter = 'div';
				}

				$items.filter( filter ).show().addClass( 'fwp-is-fadein' );

				setTimeout( function() {
					$items.removeClass( fadeinClass );

					dfd.resolve();
				}, animationDuration );
			};

			var animate = function ( category ) {
				var dfd = $.Deferred();

				var promise = fadeOut();

				promise.done( function () {
					fadeIn( category, dfd );
				} );

				return dfd;
			};

			$( '.fwp-navigation-link' ).on( 'click', function( event ) {
				event.preventDefault();

				// Remove 'active' class from the previous nav list items.
				$( this ).parent().siblings().removeClass( 'active' );

				// Add the 'active' class to this nav list item.
				$( this ).parent().addClass( 'active' );

				var category = this.hash.slice(1);

				// show/hide the right items, based on category selected
				var $container = $( '.fwp-demo-wrap .themes' );
				$container.css( 'min-width', $container.outerHeight() );

				var promise = animate( category );

				promise.done( function () {
					$container.removeAttr( 'style' );
				} );
			} );

		},

		// Get demo data.
		getDemoData: function( demo_name ) {
			var that = this;

			// Get import data
			$.ajax( {
				url: futurioDemos.ajaxurl,
				type: 'get',

				data: {
					action: 'futurio_ajax_get_import_data',
					demo_name: demo_name,
					security: futurioDemos.futurio_import_data_nonce
				},

				complete: function( data ) {
					that.importData = $.parseJSON( data.responseText );
				}
			} );

			// Run the import
			$.ajax( {
				url: futurioDemos.ajaxurl,
				type: 'get',

				data: {
					action : 'futurio_ajax_get_demo_data',
					demo_name: demo_name,
					demo_data_nonce: futurioDemos.demo_data_nonce
				},

				complete: function( data ) {
					that.runPopup( data );

					// Vars
					var $loading_icon 		= $( '.preview-' + demo_name ),
						$disable_preview 	= $( '.preview-all-' + demo_name );

					// Hide loader
					$loading_icon.hide();
					$disable_preview.hide();
				}

			} );

		},

		// Run popup.
		runPopup: function( data ) {
			var that = this

			var innerWidth = $( 'html' ).innerWidth();
			$( 'html' ).css( 'overflow', 'hidden' );
			var hiddenInnerWidth = $( 'html' ).innerWidth();
			$( 'html' ).css( 'margin-right', hiddenInnerWidth - innerWidth );

			// Show popup
			$( '#fwp-demo-popup-wrap' ).fadeIn();
			$( data.responseText ).appendTo( $( '#fwp-demo-popup-content' ) );

			// Close popup
			$( '.fwp-demo-popup-close, .fwp-demo-popup-overlay' ).on( 'click', function( e ) {
				e.preventDefault();
				if ( that.allowPopupClosing === true ) {
					that.closePopup();
				}
			} );

			// Display the step two
			$( '.fwp-plugins-next' ).on( 'click', function( e ) {
				e.preventDefault();
				
				// Hide step one
				$( '#fwp-demo-plugins' ).hide();

				// Display step two
				$( '#fwp-demo-import-form' ).show();

			} );

			// if clicked on import data button
			$( '#fwp-demo-import-form' ).submit( function( e ) {
				e.preventDefault();

				// Vars
				var demo 	= $( this ).find( '[name="futurio_import_demo"]' ).val(),
					nonce 	= $( this ).find( '[name="futurio_import_demo_data_nonce"]' ).val(),
					contentToImport = [];

				// Check what need to be imported
				$( this ).find( 'input[type="checkbox"]' ).each( function() {
					if ( $( this ).is( ':checked' ) === true ) {
						contentToImport.push( $( this ).attr( 'name' ) );
					}
				} );

				// Hide the checkboxes and show the loader
				$( this ).hide();
				$( '.fwp-loader' ).show();

				// Start importing the content
				that.importContent( {
					demo: demo,
					nonce: nonce,
					contentToImport: contentToImport,
					isXML: $( '#futurio_import_xml' ).is( ':checked' )
				} );
			} );

		},

		// importing the content.
		importContent: function( importData ) {
			var that = this,
				currentContent,
				importingLimit,
				timerStart = Date.now(),
				ajaxData = {
					futurio_import_demo: importData.demo,
					futurio_import_demo_data_nonce: importData.nonce
				};

			this.allowPopupClosing = false;
			$( '.fwp-demo-popup-close' ).fadeOut();

			// When all the selected content has been imported
			if ( importData.contentToImport.length === 0 ) {
				
				// Show the imported screen after 1 second
				setTimeout( function() {
					$( '.fwp-loader' ).hide();
					$( '.fwp-last' ).show();
				}, 1000 );

				// Notify the server that the importing process is complete
				$.ajax( {
					url: futurioDemos.ajaxurl,
					type: 'post',
					data: {
						action: 'futurio_after_import',
						futurio_import_demo: importData.demo,
						futurio_import_demo_data_nonce: importData.nonce,
						futurio_import_is_xml: importData.isXML
					},
					complete: function( data ) {}
				} );

				this.allowPopupClosing = true;
				$( '.fwp-demo-popup-close' ).fadeIn();

				return;
			}

			// Check the content that was selected to be imported.
			for ( var key in this.importData ) {

				// Check if the current item in the iteration is in the list of importable content
				var contentIndex = $.inArray( this.importData[ key ][ 'input_name' ], importData.contentToImport );

				// If it is:
				if ( contentIndex !== -1 ) {

					// Get a reference to the current content
					currentContent = key;

					// Remove the current content from the list of remaining importable content
					importData.contentToImport.splice( contentIndex, 1 );

					// Get the AJAX action name that corresponds to the current content
					ajaxData.action = this.importData[ key ]['action'];

					// After an item is found get out of the loop and execute the rest of the function
					break;
				}
			}

			// Tell the user which content is currently being imported
			$( '.fwp-import-status' ).append( '<p class="fwp-importing">' + this.importData[ currentContent ]['loader'] + '</p>' );

			// Tell the server to import the current content
			var ajaxRequest = $.ajax( {
				url: futurioDemos.ajaxurl,
				type: 'post',
				data: ajaxData,
				complete: function( data ) {
					clearTimeout( importingLimit );

					// Indicates if the importing of the content can continue
					var continueProcess = true;

					// Check if the importing of the content was successful or if there was any error
					if ( data.status === 500 || data.status === 502 || data.status === 503 ) {
						$( '.fwp-importing' )
							.addClass( 'fwp-importing-failed' )
							.removeClass( 'fwp-importing' )
							.text( futurioDemos.content_importing_error + ' '+ data.status );
					} else if ( data.responseText.indexOf( 'successful import' ) !== -1 ) {
						$( '.fwp-importing' ).addClass( 'fwp-imported' ).removeClass( 'fwp-importing' );
					} else {
						var errors = $.parseJSON( data.responseText ),
							errorMessage = '';

						// Iterate through the list of errors
						for ( var error in errors ) {
							errorMessage += errors[ error ];

							// If there was an error with the importing of the XML file, stop the process
							if ( error === 'xml_import_error' ) {
								continueProcess = false;
							}
						}

						// Display the error message
						$( '.fwp-importing' )
							.addClass( 'fwp-importing-failed' )
							.removeClass( 'fwp-importing' )
							.text( errorMessage );

						that.allowPopupClosing = true;
						$( '.fwp-demo-popup-close' ).fadeIn();
					}

					// Continue with the loading only if an important error was not encountered
					if ( continueProcess === true ) {

						// Load the next content in the list
						that.importContent( importData );
					}

				}
			} );

			// Set a time limit of 15 minutes for the importing process.
			importingLimit = setTimeout( function() {

				// Abort the AJAX request
				ajaxRequest.abort();

				// Allow the popup to be closed
				that.allowPopupClosing = true;
				$( '.fwp-demo-popup-close' ).fadeIn();

				$( '.fwp-importing' )
					.addClass( 'fwp-importing-failed' )
					.removeClass( 'fwp-importing' )
					.text( futurioDemos.content_importing_error );
			}, 15 * 60 * 1000 );

		},

		// Close demo popup.
		closePopup: function() {
			$( 'html' ).css( {
				'overflow': '',
				'margin-right': '' 
			} );

			// Hide loader
			$( '.preview-icon' ).hide();
			$( '.preview-all' ).hide();

			// Hide demo popup
			$( '#fwp-demo-popup-wrap' ).fadeOut();

			// Remove content in the popup
			setTimeout( function() {
				$( '#fwp-demo-popup-content' ).html( '' );
			}, 600);
		},

		// Install required plugins.
		installNow: function( e ) {
			e.preventDefault();

			// Vars
			var $button 	= $( e.target ),
				$document   = $( document );

			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
				wp.updates.requestFilesystemCredentials( e );

				$document.on( 'credential-modal-cancel', function() {
					var $message = $( '.install-now.updating-message' );

					$message
						.removeClass( 'updating-message' )
						.text( wp.updates.l10n.installNow );

					wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
				} );
			}

			wp.updates.installPlugin( {
				slug: $button.data( 'slug' )
			} );
		},

		// Activate required plugins.
		activatePlugins: function( e ) {
			e.preventDefault();

			// Vars
			var $button = $( e.target ),
				$init 	= $button.data( 'init' ),
				$slug 	= $button.data( 'slug' );

			if ( $button.hasClass( 'updating-message' ) || $button.hasClass( 'button-disabled' ) ) {
				return;
			}

			$button.addClass( 'updating-message button-primary' ).html( futurioDemos.button_activating );

			$.ajax( {
				url: futurioDemos.ajaxurl,
				type: 'POST',
				data: {
					action : 'futurio_ajax_required_plugins_activate',
					init   : $init,
				},
			} ).done( function( result ) {

				if ( result.success ) {

					$button.removeClass( 'button-primary install-now activate-now updating-message' )
						.attr( 'disabled', 'disabled' )
						.addClass( 'disabled' )
						.text( futurioDemos.button_active );

				}

			} );
		},

		// Install success.
		installSuccess: function( e, response ) {
			e.preventDefault();

			var $message = $( '.fwp-plugin-' + response.slug ).find( '.button' );

			// Transform the 'Install' button into an 'Activate' button.
			var $init = $message.data('init');

			$message.removeClass( 'install-now installed button-disabled updated-message' )
				.addClass( 'updating-message' )
				.html( futurioDemos.button_activating );

			// WordPress adds "Activate" button after waiting for 1000ms. So we will run our activation after that.
			setTimeout( function() {

				$.ajax( {
					url: futurioDemos.ajaxurl,
					type: 'POST',
					data: {
						action : 'futurio_ajax_required_plugins_activate',
						init   : $init,
					},
				} ).done( function( result ) {

					if ( result.success ) {

						$message.removeClass( 'button-primary install-now activate-now updating-message' )
							.attr( 'disabled', 'disabled' )
							.addClass( 'disabled' )
							.text( futurioDemos.button_active );

					} else {
						$message.removeClass( 'updating-message' );
					}

				} );

			}, 1200 );
		},

		// Plugin installing.
		pluginInstalling: function( e, args ) {
			e.preventDefault();

			var $card = $( '.fwp-plugin-' + args.slug ),
				$button = $card.find( '.button' );

			$button.addClass( 'updating-message' );
		},

		// Plugin install error.
		installError: function( e, response ) {
			e.preventDefault();

			var $card = $( '.fwp-plugin-' + response.slug );

			$card.removeClass( 'button-primary' ).addClass( 'disabled' ).html( wp.updates.l10n.installFailedShort );
		}

	};

} ) ( jQuery );