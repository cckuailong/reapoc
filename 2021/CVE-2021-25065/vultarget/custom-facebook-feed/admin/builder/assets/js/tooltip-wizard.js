/**
 * Custom Facebook Tooltip Manager
 *
 * @since 4.0
 */
'use strict';

var CFFTooltipWizard = window.CFFTooltipWizard || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 4.0
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 4.0
		 */
		init: function() {

			//$( app.ready );
			$( window ).on( 'load', function() {
				if ( $.isFunction( $.ready.then ) ) {
					$.ready.then( app.load );
				} else {
					app.load();
				}
			} );
		},


		/**
		 * Window load.
		 *
		 * @since 4.0
		 */
		load: function() {
			if(cff_admin_tooltip_wizard.cff_wizard_gutenberg){
				app.initGutenbergTooltip();
			}
		},


		initGutenbergTooltip : function(){
			if ( typeof $.fn.tooltipster === 'undefined' ) {
				return;
			}
			var $dot = $( '<span class="wpforms-admin-form-embed-wizard-dot">&nbsp;</span>' );
			var anchor = '.block-editor .edit-post-header-toolbar__inserter-toggle';
			var tooltipsterArgs = {
				content          : $( '#cff-gutenberg-tooltip-content' ),
				trigger          : 'custom',
				interactive      : true,
				animationDuration: 0,
				delay            : 0,
				theme            : [ 'tooltipster-default', 'cff-tooltip-wizard' ],
				side             : 'bottom',
				distance         : 3,
				functionReady    : function( instance, helper ) {
					instance._$tooltip.on( 'click', '.cff-tlp-wizard-close', function() {
						instance.close();
					} );

					instance.reposition();
				},
			};

			$('.components-button.edit-post-header-toolbar__inserter-toggle').on('click',function() {
				$('.cff-tooltip-wizard.tooltipster-sidetip').hide();
			});

			$dot.insertAfter( anchor ).tooltipster( tooltipsterArgs ).tooltipster( 'open' );
		},

		/**
		 * Check if we're in Gutenberg editor.
		 *
		 * @since 4.0
		 *
		 * @returns {boolean} Is Gutenberg or not.
		 */
		isGutenberg: function() {

			return typeof wp !== 'undefined' && Object.prototype.hasOwnProperty.call( wp, 'blocks' );
		},
	}

	return app;
}( document, window, jQuery ) );

CFFTooltipWizard.init();
