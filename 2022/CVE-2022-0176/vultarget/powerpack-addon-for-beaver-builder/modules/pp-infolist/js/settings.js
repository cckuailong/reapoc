(function($){

	FLBuilder.registerModuleHelper('pp-infolist', {

		_previewMode: 'default',

		rules: {
			link: {
				required: true
			},
			'box_border_width': {
				number: true
			},
			'icon_font_size': {
				number: true
			},
			'icon_width': {
				number: true
			},
			'image_width': {
				number: true
			},
			'icon_box_size': {
				number: true
			},
			'icon_border_width': {
				number: true
			},
			'icon_box_width': {
				number: true
			},
			'icon_border_radius': {
				number: true
			},
			'title_font_size': {
				number: true
			},
			'text_font_size': {
				number: true
			},
			'animation_duration': {
				number: true
			}
		},

		init: function() {
			var form = $('.fl-builder-settings'),
				nodeId = form.data('node'),
				node = $('.fl-node-' + nodeId),
				self = this;

			var field = form.find( 'select[name="layouts"]' ),
				layout = field.val();

			var fieldMedium = form.find( 'select[name="layouts_medium"]' ),
				layoutMedium = '' === ( fieldMedium.val() ) ? layout : fieldMedium.val();

			var fieldResponsive = form.find( 'select[name="layouts_responsive"]' ),
				layoutResponsive = '' === ( fieldResponsive.val() ) ? layoutMedium : fieldResponsive.val();

			// Update layout class.
			field.on( 'change', function() {
				layout = field.val();
				node.find( '.pp-infolist' ).removeClass( 'layout-1 layout-2 layout-3' );
				node.find( '.pp-infolist' ).addClass( 'layout-' + layout );
				self._updateConnector( layout, node );
			} );

			fieldMedium.on( 'change', function() {
				layoutMedium = '' === ( fieldMedium.val() ) ? layout : fieldMedium.val();
				node.find( '.pp-infolist' ).removeClass( 'layout-1 layout-2 layout-3' );
				node.find( '.pp-infolist' ).addClass( 'layout-' + layoutMedium );
				self._updateConnector( layoutMedium, node );
			} );

			fieldResponsive.on( 'change', function() {
				layoutResponsive = '' === ( fieldResponsive.val() ) ? layoutMedium : fieldResponsive.val();
				node.find( '.pp-infolist' ).removeClass( 'layout-1 layout-2 layout-3' );
				node.find( '.pp-infolist' ).addClass( 'layout-' + layoutResponsive );
				self._updateConnector( layoutResponsive, node );
			} );

			$('body').on( 'fl-builder.responsive-editing-switched.preview-' + FLBuilder.preview.id, function(e, mode) {
				self._previewMode = mode;

				node.find( '.pp-infolist' ).removeClass( 'layout-1 layout-2 layout-3' );

				if ( 'default' == mode ) {
					node.find( '.pp-infolist' ).addClass( 'layout-' + layout );
				}

				if ( 'medium' == mode ) {
					node.find( '.pp-infolist' ).addClass( 'layout-' + layoutMedium );
				}

				if ( 'responsive' == mode ) {
					node.find( '.pp-infolist' ).addClass( 'layout-' + layoutResponsive );
				}
			} );
		},

		_updateConnector: function( layout, node ) {
			var connector = node.find( '.pp-list-connector' );

			if ( 1 == layout ) {
				connector.css( 'left', ( node.find( '.pp-icon-wrapper' ).width() / 2 ) + 'px' );
			}

			if ( 2 == layout ) {
				connector.css( 'right', ( node.find( '.pp-icon-wrapper' ).width() / 2 ) + 'px' );
			}

			if ( 3 == layout ) {
				connector.css( 'top', ( node.find( '.pp-icon-wrapper' ).height() / 2 ) + 'px' );
				connector.css( 'left', ( node.find( '.pp-list-item' ).outerWidth() / 2 ) + 'px' );
			}
		},

	});

})(jQuery);
