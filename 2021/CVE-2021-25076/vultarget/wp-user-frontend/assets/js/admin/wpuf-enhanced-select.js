/*global wpuf_enhanced_select_params */
jQuery( function( $ ) {

	function getEnhancedSelectFormatString() {
		return {
			'language': {
				errorLoading: function() {
					// Workaround for https://github.com/select2/select2/issues/4355 instead of i18n_ajax_error.
					return wpuf_enhanced_select_params.i18n_searching;
				},
				inputTooLong: function( args ) {
					var overChars = args.input.length - args.maximum;

					if ( 1 === overChars ) {
						return wpuf_enhanced_select_params.i18n_input_too_long_1;
					}

					return wpuf_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
				},
				inputTooShort: function( args ) {
					var remainingChars = args.minimum - args.input.length;

					if ( 1 === remainingChars ) {
						return wpuf_enhanced_select_params.i18n_input_too_short_1;
					}

					return wpuf_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
				},
				loadingMore: function() {
					return wpuf_enhanced_select_params.i18n_load_more;
				},
				maximumSelected: function( args ) {
					if ( args.maximum === 1 ) {
						return wpuf_enhanced_select_params.i18n_selection_too_long_1;
					}

					return wpuf_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
				},
				noResults: function() {
					return wpuf_enhanced_select_params.i18n_no_matches;
				},
				searching: function() {
					return wpuf_enhanced_select_params.i18n_searching;
				}
			}
		};
	}

	try {
		$( document.body )

			.on( 'wpuf-enhanced-select-init', function() {

				// Regular select boxes
				$( ':input.wpuf-enhanced-select, :input.chosen_select' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = $.extend({
						minimumResultsForSearch: 10,
						allowpuflear:  $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' )
					}, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				});

				$( ':input.wpuf-enhanced-select-nostd, :input.chosen_select_nostd' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = $.extend({
						minimumResultsForSearch: 10,
						allowpuflear:  true,
						placeholder: $( this ).data( 'placeholder' )
					}, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				});

				// Ajax product search box
				$( ':input.wpuf-product-search' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = {
						allowpuflear:  $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
						escapeMarkup: function( m ) {
							return m;
						},
						ajax: {
							url:         wpuf_enhanced_select_params.ajax_url,
							dataType:    'json',
							delay:       250,
							data:        function( params ) {
								return {
									term:     params.term,
									action:   $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
									security: wpuf_enhanced_select_params.search_products_nonce,
									exclude:  $( this ).data( 'exclude' ),
									include:  $( this ).data( 'include' ),
									limit:    $( this ).data( 'limit' )
								};
							},
							processResults: function( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, text ) {
										terms.push( { id: id, text: text } );
									});
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					};

					select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );

					if ( $( this ).data( 'sortable' ) ) {
						var $select = $(this);
						var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

						$list.sortable({
							placeholder : 'ui-state-highlight select2-selection__choice',
							forcePlaceholderSize: true,
							items       : 'li:not(.select2-search__field)',
							tolerance   : 'pointer',
							stop: function() {
								$( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function() {
									var id     = $( this ).data( 'data' ).id;
									var option = $select.find( 'option[value="' + id + '"]' )[0];
									$select.prepend( option );
								} );
							}
						});
					// Keep multiselects ordered alphabetically if they are not sortable.
					} else if ( $( this ).prop( 'multiple' ) ) {
						$( this ).on( 'change', function(){
							var $children = $( this ).children();
							$children.sort(function(a, b){
								var atext = a.text.toLowerCase();
								var btext = b.text.toLowerCase();

								if ( atext > btext ) {
									return 1;
								}
								if ( atext < btext ) {
									return -1;
								}
								return 0;
							});
							$( this ).html( $children );
						});
					}
				});

				// Ajax customer search boxes
				$( ':input.wpuf-customer-search' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = {
						allowpuflear:  $( this ).data( 'allow_clear' ) ? true : false,
						placeholder: $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '1',
						escapeMarkup: function( m ) {
							return m;
						},
						ajax: {
							url:         wpuf_enhanced_select_params.ajax_url,
							dataType:    'json',
							delay:       1000,
							data:        function( params ) {
								return {
									term:     params.term,
									action:   'woocommerce_json_search_customers',
									security: wpuf_enhanced_select_params.search_customers_nonce,
									exclude:  $( this ).data( 'exclude' )
								};
							},
							processResults: function( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, text ) {
										terms.push({
											id: id,
											text: text
										});
									});
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					};

					select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );

					if ( $( this ).data( 'sortable' ) ) {
						var $select = $(this);
						var $list   = $( this ).next( '.select2-container' ).find( 'ul.select2-selection__rendered' );

						$list.sortable({
							placeholder : 'ui-state-highlight select2-selection__choice',
							forcePlaceholderSize: true,
							items       : 'li:not(.select2-search__field)',
							tolerance   : 'pointer',
							stop: function() {
								$( $list.find( '.select2-selection__choice' ).get().reverse() ).each( function() {
									var id     = $( this ).data( 'data' ).id;
									var option = $select.find( 'option[value="' + id + '"]' )[0];
									$select.prepend( option );
								} );
							}
						});
					}
				});

				// Ajax category search boxes
				$( ':input.wpuf-category-search' ).filter( ':not(.enhanced)' ).each( function() {
					var select2_args = $.extend( {
						allowpuflear        : $( this ).data( 'allow_clear' ) ? true : false,
						placeholder       : $( this ).data( 'placeholder' ),
						minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : 3,
						escapeMarkup      : function( m ) {
							return m;
						},
						ajax: {
							url:         wpuf_enhanced_select_params.ajax_url,
							dataType:    'json',
							delay:       250,
							data:        function( params ) {
								return {
									term:     params.term,
									action:   'woocommerce_json_search_categories',
									security: wpuf_enhanced_select_params.search_categories_nonce
								};
							},
							processResults: function( data ) {
								var terms = [];
								if ( data ) {
									$.each( data, function( id, term ) {
										terms.push({
											id:   term.slug,
											text: term.formatted_name
										});
									});
								}
								return {
									results: terms
								};
							},
							cache: true
						}
					}, getEnhancedSelectFormatString() );

					$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
				});
			})

			// WooCommerce Backbone Modal
			.on( 'wpuf_backbone_modal_before_remove', function() {
				$( '.wpuf-enhanced-select, :input.wpuf-product-search, :input.wpuf-customer-search' ).filter( '.select2-hidden-accessible' ).selectWoo( 'close' );
			})

			.trigger( 'wpuf-enhanced-select-init' );

		$( 'html' ).on( 'click', function( event ) {
			if ( this === event.target ) {
				$( '.wpuf-enhanced-select, :input.wpuf-product-search, :input.wpuf-customer-search' ).filter( '.select2-hidden-accessible' ).selectWoo( 'close' );
			}
		} );
	} catch( err ) {
		// If select2 failed (conflict?) log the error but don't stop other scripts breaking.
		window.console.log( err );
	}
});
