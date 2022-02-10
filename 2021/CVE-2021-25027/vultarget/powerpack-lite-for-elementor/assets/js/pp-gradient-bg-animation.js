( function ( $ ) {
	"use strict";

	var AnimatedGradientBg = function ( $scope, $ ) {

		if ( ! $scope.hasClass( 'pp-animated-gradient-bg-yes' ) ) {
			return;
		}

		var sectionId      = $scope.data( 'id' ),
			color          = $scope.data( 'color' ),
			angle          = $scope.data( 'angle' ),
			gradientColor  = 'linear-gradient( ' + angle + ',' + color + ' )';
		
		$scope.css( 'background-image', gradientColor );

		if ( elementorFrontend.isEditMode() ) {
			color = $scope.find( '.pp-animated-gradient-bg' ).data( 'color' );
			angle = $scope.find( '.pp-animated-gradient-bg' ).data( 'angle' );
			var gradientColorEditor = 'linear-gradient( ' + angle + ',' + color + ' )';
			$scope.prepend( '<div class="pp-animated-gradient-bg" style="background-image : ' + gradientColorEditor + ' "></div>' );
		}

	};

	$( window ).on( 'elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', AnimatedGradientBg );
	} );
	
}( jQuery ) );
