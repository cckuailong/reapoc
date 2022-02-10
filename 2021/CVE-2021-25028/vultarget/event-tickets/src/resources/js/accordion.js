( function() {
	'use strict';

	// This is our global accordion index to keep unique ids
	var topIndex = 0;

	window.MTAccordion = function( options, callback ) {
		if ( 'undefined' === typeof options.target ) {
			return false;
		}

		var accordion = document.querySelector( options.target );

		if ( ! accordion ) {
			return;
		}

		// Simple iterator for reuse
		var forEach = function( array, callback, scope ) {
			for ( var i = 0, imax = array.length; i < imax; i++ ) {
				callback.call( scope, i, array[i] ); // passes back stuff we need
			}
		};
		// set ARIA role
		accordion.setAttribute( 'role', 'tablist' );

		var accordionContent = accordion.getElementsByClassName( 'accordion-content' );
		var accordionHeader  = accordion.getElementsByClassName( 'accordion-header' );

		topIndex++;

		forEach( accordionHeader, function( index, value ) {
			var head = value;
			index++;

			// Prevent Reconfiguring Accordion
			if ( 'tab' === head.getAttribute( 'role' ) ) {
				return;
			}

			// Set ARIA and ID attributes
			head.setAttribute( 'id', 'tab' + topIndex + '-' + index );
			head.setAttribute( 'aria-selected', 'false' );
			head.setAttribute( 'aria-expanded', 'false' );
			head.setAttribute( 'aria-controls', 'panel' + topIndex + '-' + index );
			head.setAttribute( 'role', 'tab' );

			head.addEventListener( 'click', accordionHandle );

			function accordionHandle( event ) {
				var nextPanel = value.nextElementSibling;
				var nextPanelLabel = nextPanel.getElementsByClassName( 'accordion-label' )[0];

				value.classList.toggle( 'is-active' );

				nextPanel.classList.toggle( 'is-active' );

				nextPanelLabel.setAttribute( 'tabindex', -1 );
				nextPanelLabel.focus();

				if ( nextPanel.classList.contains( 'is-active' ) ) {
					head.setAttribute( 'aria-selected', 'true' );
					head.setAttribute( 'aria-expanded', 'true' );
					nextPanel.setAttribute( 'aria-hidden', 'false' );
				} else {
					head.setAttribute( 'aria-selected', 'false' );
					head.setAttribute( 'aria-expanded', 'false' );
					nextPanel.setAttribute( 'aria-hidden', 'true' );
				}

				event.preventDefault();
			}
		});

		forEach( accordionContent, function( index, value ) {
			var content = value;
			index++;

			// Prevent Reconfiguring Accordion
			if ( 'tabpanel' === content.getAttribute( 'role' ) ) {
				return;
			}

			// Set ARIA and ID attributes
			content.setAttribute( 'id', 'panel' + topIndex + '-' + index );
			content.setAttribute( 'aria-hidden', 'true' );
			content.setAttribute( 'aria-labelledby', 'tab' + topIndex + '-' + index );
			content.setAttribute( 'role', 'tabpanel' );
			//content.setAttribute( 'tabindex', '-1' );
		});

		// Execute the callback function
		if ( typeof callback === 'function' ) {
			callback.call();
		}
	}

	// IE8 compatible alternative to DOMContentLoaded
	document.onreadystatechange = function () {
		if ( 'interactive' === document.readyState ) {
			window.MTAccordion( {
				target: '.accordion', // ID (or class) of accordion container
			} );
		}
	}
} )();
