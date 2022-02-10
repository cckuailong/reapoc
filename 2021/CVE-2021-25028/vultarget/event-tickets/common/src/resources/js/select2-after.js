jQuery.fn.select2.amd.define( 'jquery.select2TEC', [
	'jquery',
	'jquery-mousewheel',

	'./select2/core',
	'./select2/defaults',
], function( $, _, Select2, Defaults ) {
	if ( $.fn.select2TEC == null ) {
		// All methods that should return the element
		var thisMethods = [ 'open', 'close', 'destroy' ];

		$.fn.select2TEC = function( options ) {
			options = options || {};

			if ( typeof options === 'object' ) {
				this.each( function() {
					var instanceOptions = $.extend( true, {}, options );

					var instance = new Select2( $( this ), instanceOptions ); // eslint-disable-line no-unused-vars,max-len
				} );

				return this;
			} else if ( typeof options === 'string' ) {
				var ret;
				var args = Array.prototype.slice.call( arguments, 1 );

				this.each( function() {
					var instance = $( this ).data( 'select2' );

					if ( instance == null && window.console && console.error ) {
						console.error(
							'The select2(\'' + options + '\') method was called on an ' +
							'element that is not using Select2.'
						);
					}

					ret = instance[ options ].apply( instance, args );
				} );

				// Check if we should be returning `this`
				if ( $.inArray( options, thisMethods ) > -1 ) {
					return this;
				}

				return ret;
			} else {
				throw new Error( 'Invalid arguments for Select2: ' + options );
			}
		};
	}

	if ( $.fn.select2TEC.defaults == null ) {
		$.fn.select2TEC.defaults = Defaults;
	}

	return Select2;
} );

jQuery.fn.select2.amd.require( 'jquery.select2TEC' );
