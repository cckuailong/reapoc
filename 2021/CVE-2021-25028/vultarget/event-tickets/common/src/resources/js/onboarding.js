/**
 *
 * @type {PlainObject}
 */
tribe.onboarding = {};

/**
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.onboarding
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since TBD
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {};

	/**
	 * Concatenate the CSS classes for the tooltip.
	 *
	 * @param {Array} classes
	 * @returns {String} String containing the classes for the tooltip.
	 */
	obj.getTooltipClasses = function( classes ) {
		const defaultClasses = [ 'tribe-onboarding__tooltip' ];

		return defaultClasses.concat( classes ).join( ' ' );
	}

	/**
	 * Init onboarding steps.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	obj.initTour = function() {
		const steps = TribeOnboardingTour.steps;
		const classes = TribeOnboardingTour.classes || [];

		if ( typeof steps === 'undefined' ) {
			return;
		}

		if ( ! steps.length ) {
			return;
		}

		introJs().setOptions( {
			tooltipClass: obj.getTooltipClasses( classes ),
			steps: steps
		} ).start();
	};

	/**
	* Init hints.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	obj.initHints = function() {
		const hints = TribeOnboardingHints.hints;
		const classes = TribeOnboardingHints.classes || [];

		if ( ! Array.isArray( hints ) || ! hints.length ) {
			return;
		}

		introJs().setOptions( {
			tooltipClass: obj.getTooltipClasses( classes ),
			hintButtonLabel: TribeOnboarding.hintButtonLabel,
			hintPosition: 'middle-right',
			hintAnimation: true,
			hints: hints,
		} ).addHints();
	};

	/**
	 * Handles the initialization of the enhancer.
	 *
	 * @since TBD
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		// Init Tour.
		obj.initTour();

		// Init hint.
		obj.initHints();
	};

	// Configure on document ready.
	$document.ready( obj.ready );

} )( jQuery, tribe.onboarding );
