var myCREDCharts = {};
jQuery(function($){

	$(document).ready(function(){

		$.each( myCREDStats.charts, function(elementid, data){

			if( $( 'canvas#' + elementid ).length > 0 ) {
				myCREDCharts[ elementid ] = new Chart( $( 'canvas#' + elementid ).get(0).getContext( '2d' ), data );
			}

		});

	});

});