/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.tickets = tribe.tickets || {};
tribe.tickets.rsvp = tribe.tickets.rsvp || {};

/**
 * Configures RSVP Tooltip Object in the Global Tribe variable
 *
 * @since 5.0.0
 *
 * @type   {PlainObject}
 */
tribe.tickets.rsvp.tooltip = {};

/**
 * Initializes in a Strict env the code that manages the RSVP Tooltip
 *
 * @since 5.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.tickets.rsvp.tooltip
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	var $document = $( document );

	/**
	 * Config used for tooltip setup.
	 *
	 * @since 5.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.config = {
		delayHoverIn: 300,
		delayHoverOut: 300,
	};

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 5.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		tooltipTrigger: '[data-js~="tribe-tickets-tooltip"]',
		tribeTicketsRsvpTooltipTriggerHoverClass: '.tribe-tickets-tooltip-trigger--hover',
		tribeTicketsRsvpTooltipThemeClass: '.tribe-tickets-tooltip-theme',
		tribeTicketsRsvpTooltipThemeHoverClass: '.tribe-tickets-tooltip-theme--hover',
		tribeCommonClass: '.tribe-common',
		tribeTicketsClass: '.event-tickets',
	};

	/**
	 * Handle tooltip focus event.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleOriginFocus = function( event ) {
		setTimeout( function() {
			if (
				event.data.target.is( ':focus' ) ||
				event.data.target.hasClass(
					obj.selectors.tribeTicketsRsvpTooltipTriggerHoverClass.className()
				)
			) {
				event.data.target.tooltipster( 'open' );
			}
		}, obj.config.delayHoverIn );
	};

	/**
	 * Handle tooltip blur event.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleOriginBlur = function( event ) {
		event.data.target.tooltipster( 'close' );
	};

	/**
	 * Handle origin mouseenter and touchstart events.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleOriginHoverIn = function( event ) {
		event.data.target.addClass(
			obj.selectors.tribeTicketsRsvpTooltipTriggerHoverClass.className()
		);
	};

	/**
	 * Handle origin mouseleave and touchleave events.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleOriginHoverOut = function( event ) {
		event.data.target.removeClass(
			obj.selectors.tribeTicketsRsvpTooltipTriggerHoverClass.className()
		);
	};

	/**
	 * Handle tooltip mouseenter and touchstart event.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleTooltipHoverIn = function( event ) {
		event.data.target.addClass(
			obj.selectors.tribeTicketsRsvpTooltipThemeHoverClass.className()
		);
	};

	/**
	 * Handle tooltip mouseleave and touchleave events.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleTooltipHoverOut = function( event ) {
		event.data.target.removeClass(
			obj.selectors.tribeTicketsRsvpTooltipThemeHoverClass.className()
		);
	};

	/**
	 * Handle tooltip instance closing event.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleInstanceClose = function( event ) {
		var $origin = event.data.origin;
		var $tooltip = $( event.tooltip );

		// if trigger is focused, hovered, or tooltip is hovered, do not close tooltip
		if (
			$origin.is( ':focus' ) ||
			$origin.hasClass( obj.selectors.tribeTicketsRsvpTooltipTriggerHoverClass.className() ) ||
			$tooltip.hasClass( obj.selectors.tribeTicketsRsvpTooltipThemeHoverClass.className() )
		) {
			event.stop();
		}
	};

	/**
	 * Handle tooltip instance close event.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event event object
	 *
	 * @return {void}
	 */
	obj.handleInstanceClosing = function( event ) {
		$( event.tooltip )
			.off( 'mouseenter touchstart', obj.handleTooltipHoverIn )
			.off( 'mouseleave touchleave', obj.handleTooltipHoverOut );
	};

	/**
	 * Override of the `functionInit` tooltipster method.
	 * A custom function to be fired only once at instantiation.
	 *
	 * @since 5.0.0
	 *
	 * @param {Tooltipster} instance instance of Tooltipster
	 * @param {PlainObject} helper   helper object with tooltip origin
	 *
	 * @return {void}
	 */
	obj.onFunctionInit = function( instance, helper ) {
		var $origin = $( helper.origin );
		$origin
			.on( 'focus', { target: $origin }, obj.handleOriginFocus )
			.on( 'blur', { target: $origin }, obj.handleOriginBlur )
			.on( 'mouseenter touchstart', { target: $origin }, obj.handleOriginHoverIn )
			.on( 'mouseleave touchleave', { target: $origin }, obj.handleOriginHoverOut );
		instance
			.on( 'close', { origin: $origin }, obj.handleInstanceClose )
			.on( 'closing', { origin: $origin }, obj.handleInstanceClosing );
	};

	/**
	 * Override of the `functionReady` tooltipster method.
	 * A custom function to be fired when the tooltip and its contents have been added to the DOM.
	 *
	 * @since 5.0.0
	 *
	 * @param {Tooltipster} instance instance of Tooltipster
	 * @param {PlainObject} helper   helper object with tooltip origin
	 *
	 * @return {void}
	 */
	obj.onFunctionReady = function( instance, helper ) {
		var $tooltip = $( helper.tooltip );
		$tooltip
			.on( 'mouseenter touchstart', { target: $tooltip }, obj.handleTooltipHoverIn )
			.on( 'mouseleave touchleave', { target: $tooltip }, obj.handleTooltipHoverOut );
	};

	/**
	 * Deinitialize accessible tooltips via tooltipster.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.deinitTooltips = function( $container ) {
		$container
			.find( obj.selectors.tooltipTrigger )
			.each( function( index, trigger ) {
				$( trigger )
					.off()
					.tooltipster( 'instance' )
					.off();
			} );
	};

	/**
	 * Initialize accessible tooltips via tooltipster.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of RSVP container.
	 *
	 * @return {void}
	 */
	obj.initTooltips = function( $container ) {
		var theme = $container.data( 'tribeTicketsRsvpTooltipTheme' );

		$container
			.find( obj.selectors.tooltipTrigger )
			.each( function( index, trigger ) {
				$( trigger ).tooltipster( {
					animationDuration: 0,
					interactive: true,
					delay: [ obj.config.delayHoverIn, obj.config.delayHoverOut ],
					delayTouch: [ obj.config.delayHoverIn, obj.config.delayHoverOut ],
					theme: theme,
					functionInit: obj.onFunctionInit,
					functionReady: obj.onFunctionReady,
				} );
			} );
	};

	/**
	 * Initialize tooltip theme.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of RSVP container.
	 *
	 * @return {void}
	 */
	obj.initTheme = function( $container ) {
		$container.trigger( 'beforeTooltipInitTheme.tribeTicketsRsvp', [ $container ] );

		var theme = [
			obj.selectors.tribeTicketsRsvpTooltipThemeClass.className(),
			obj.selectors.tribeCommonClass.className(),
			obj.selectors.tribeTicketsClass.className(),
		];
		$container.data( 'tribeTicketsRsvpTooltipTheme', theme );

		$container.trigger( 'afterTooltipInitTheme.tribeTicketsRsvp', [ $container ] );
	};

	/**
	 * Deinitialize tooltip JS.
	 *
	 * @since 5.0.0
	 *
	 * @param  {Event}       event    event object for 'beforeAjaxSuccess.tribeTicketsRsvp' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {PlainObject} settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.deinit = function( event, jqXHR, settings ) { // eslint-disable-line no-unused-vars
		var $container = event.data.container;
		obj.deinitTooltips( $container );
		$container.off( 'beforeAjaxSuccess.tribeTicketsRsvp', obj.deinit );
	};

	/**
	 * Initialize tooltips JS.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event}   event      event object for 'afterSetup.tribeTicketsRsvp' event
	 * @param {integer} index      jQuery.each index param from 'afterSetup.tribeTicketsRsvp' event.
	 * @param {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container ) {
		obj.initTheme( $container );
		obj.initTooltips( $container );
		$container.on( 'beforeAjaxSuccess.tribeTicketsRsvp', { container: $container }, obj.deinit );
	};

	/**
	 * Handles the initialization of the scripts when Document is ready.
	 *
	 * @since 5.0.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on(
			'afterSetup.tribeTicketsRsvp',
			tribe.tickets.rsvp.manager.selectors.container,
			obj.init
		);
	};

	// Configure on document ready.
	$( obj.ready );
} )( jQuery, tribe.tickets.rsvp.tooltip );
