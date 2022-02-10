( function( $ ) {

	var loadStatus = true;
	var count = 1;
	var loader = '';
	var total = 0;
	
	function equalHeight( slider_wrapper ) {
		var slickSlider = slider_wrapper.find('.pp-posts-carousel'),
            equalHeight = slickSlider.data( 'equal-height' );
		
		if ( 'yes' != equalHeight ) {
        	return;
        }
		
		slickSlider.find('.slick-slide').height('auto');

		var slickTrack = slickSlider.find('.slick-track'),
			slickTrackHeight = $(slickTrack).height();

		slickSlider.find('.slick-slide').css('height', slickTrackHeight + 'px');
	}
	
	var PostsHandler = function( $scope, $ ) {
		
		var container = $scope.find( '.pp-posts-container' ),
			selector = $scope.find( '.pp-posts-grid' ),
			layout = $scope.find( '.pp-posts' ).data( 'layout' ),
			loader = $scope.find( '.pp-posts-loader' );

		if ( 'masonry' == layout ) {

			$scope.imagesLoaded( function(e) {

				selector.isotope({
					layoutMode: layout,
					itemSelector: '.pp-grid-item-wrap',
				});

			});
		}
		
		if ( 'carousel' == layout ) {
			var $carousel		= $scope.find( '.pp-posts-carousel' ).eq( 0 ),
				$slider_options	= JSON.parse( $carousel.attr('data-slider-settings') );

			if ( $carousel.length > 0 ) {
				$scope.imagesLoaded( function() {
					$carousel.slick($slider_options);
				});
			}

			$($carousel).on('setPosition', function () {
				equalHeight($scope);
			});
		}
	}

	$( 'body' ).delegate( '.pp-posts-pagination-ajax .page-numbers', 'click', function( e ) {

		$scope = $( this ).closest( '.elementor-widget-pp-posts' );
		
		if ( 'main' == $scope.find( '.pp-posts-grid' ).data( 'query-type' ) ) {
			return;
		}

		e.preventDefault();

		$scope.find( '.pp-posts-grid .pp-post' ).last().after( '<div class="pp-post-loader"><div class="pp-loader"></div><div class="pp-loader-overlay"></div></div>' );

		var page_number = 1;
		var curr = parseInt( $scope.find( '.pp-posts-pagination .page-numbers.current' ).html() );

		if ( $( this ).hasClass( 'next' ) ) {
			page_number = curr + 1;
		} else if ( $( this ).hasClass( 'prev' ) ) {
			page_number = curr - 1;
		} else {
			page_number = $( this ).html();
		}

		$scope.find( '.pp-posts-grid .pp-post' ).last().after( '<div class="pp-post-loader"><div class="pp-loader"></div><div class="pp-loader-overlay"></div></div>' );

		var $args = {
			'page_id':		$scope.find( '.pp-posts-grid' ).data('page'),
			'widget_id':	$scope.data( 'id' ),
			'skin':			$scope.find( '.pp-posts-grid' ).data( 'skin' ),
			'page_number':	page_number
		};

		$('html, body').animate({
			scrollTop: ( ( $scope.find( '.pp-posts-container' ).offset().top ) - 30 )
		}, 'slow');

		_callAjax( $scope, $args );

	} );

	var _callAjax = function( $scope, $obj, $append, $count ) {

		var loader = $scope.find( '.pp-posts-loader' );
		
		$.ajax({
			url: pp_posts_script.ajax_url,
			data: {
				action:			'pp_get_post',
				page_id:		$obj.page_id,
				widget_id:		$obj.widget_id,
				skin:			$obj.skin,
				page_number:	$obj.page_number,
				nonce:			pp_posts_script.posts_nonce,
			},
			dataType: 'json',
			type: 'POST',
			success: function( data ) {

				var sel = $scope.find( '.pp-posts-grid' );

				if ( true == $append ) {

					var html_str = data.data.html;

					sel.append( html_str );
				} else {
					sel.html( data.data.html );
				}

				$scope.find( '.pp-posts-pagination-wrap' ).html( data.data.pagination );

				var layout = $scope.find( '.pp-posts-grid' ).data( 'layout' ),
					selector = $scope.find( '.pp-posts-grid' );

				if ( 'masonry' == layout ) {

					$scope.imagesLoaded( function() {
						selector.isotope( 'destroy' );
						selector.isotope({
							layoutMode: layout,
							itemSelector: '.pp-grid-item-wrap',
						});
					});
				}

				//	Complete the process 'loadStatus'
				loadStatus = true;
				if ( true == $append ) {
					loader.hide();
				}
				
				$count = $count + 1;

				$scope.trigger('posts.rendered');
			}
		});
	}

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.classic', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.card', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.checkerboard', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.creative', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.event', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.news', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.portfolio', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.overlap', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.template', PostsHandler );

	});

} )( jQuery );
