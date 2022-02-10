;(function($) {
	PPInfoList = function( settings ) {
		this.id = settings.id;
		this.layout = settings.layout;
		this.breakpoints = settings.breakpoints;
		this.node = $( '.fl-node-' + this.id );
		this.wrap = this.node.find( '.pp-infolist' );
		this.connector = this.node.find( '.pp-list-connector' );

		this._init();

		$( window ).on( 'resize', $.proxy( this._init, this ) );
	};

	PPInfoList.prototype = {
		_init: function() {
			var layout = this._getLayout();

			this.wrap.removeClass( 'layout-1 layout-2 layout-3' );
			this.wrap.addClass( 'layout-' + layout );

			if ( 1 == layout ) {
				this.connector.css( 'left', ( this.node.find( '.pp-icon-wrapper' ).width() / 2 ) + 'px' );
			}

			if ( 2 == layout ) {
				this.connector.css( 'right', ( this.node.find( '.pp-icon-wrapper' ).width() / 2 ) + 'px' );
			}

			if ( 3 == layout ) {
				this.connector.css( 'top', ( this.node.find( '.pp-icon-wrapper' ).height() / 2 ) + 'px' );
				this.connector.css( 'left', ( this.node.find( '.pp-list-item' ).outerWidth() / 2 ) + 'px' );
			}
		},

		_getLayout: function() {
			var layout = this.layout;
			var breakpoints = this._getBreakpoints();

			if ( '' === layout.medium ) {
				layout.medium = layout.large;
			}

			if ( '' === layout.responsive ) {
				layout.responsive = layout.medium;
			}

			if ( window.innerWidth <= breakpoints.responsive ) {
				return layout.responsive;
			}
			if ( window.innerWidth <= breakpoints.medium ) {
				return layout.medium;
			}

			return layout.large;
		},

		_getBreakpoints: function() {
			var breakpoints = this.breakpoints;

			if ( '' === breakpoints.medium ) {
				breakpoints.medium = 1024;
			}

			if ( '' === breakpoints.responsive ) {
				breakpoints.responsive = 768;
			}

			return breakpoints;
		},
	};
})(jQuery);