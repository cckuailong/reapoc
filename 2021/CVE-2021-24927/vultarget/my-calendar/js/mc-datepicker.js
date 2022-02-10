if ( typeof(mc_months) !== "undefined" ) {

	jQuery(document).ready(function ($) {
	$( '.mc-datepicker' ).pickadate({
		monthsFull: mc_months,
		weekdaysShort: mc_days,
		format: 'yyyy-mm-dd',
		selectYears: true,
		selectMonths: true,
		editable: true,
		firstDay: mc_text.vals.start,
		today: mc_text.vals.today,
		clear: mc_text.vals.clear,
		close: mc_text.vals.close,
		onClose: function() {
			mc_update_date();
		}
	});
	$( '.mc-timepicker' ).pickatime({
		interval: parseInt( mcTime.interval ),
		format: mcTime.time_format,
		editable: true
	});

	var begin = $( '#mc_event_date' ).pickadate( 'picker' );
	var end   = $( '#mc_event_enddate' ).pickadate( 'picker' );
	var time  = $( '#mc_event_time' ).pickatime( 'picker' );
	var ends  = $( '#mc_event_endtime' ).pickatime( 'picker' );

	function mc_update_date() {
		var startdate = new Date( $( '#mc_event_date' ).val() );
		end.set( 'min', convertDateToUTC( startdate ) );

		if ( $( '#mc_event_enddate' ).val() != '' ) {
			var enddate   = new Date( $( '#mc_event_enddate' ).val() );
			if ( enddate < startdate ) {
				$( '#mc_event_enddate' ).val( '' );
			} else {
				begin.set( 'max', enddate );
			}
		}
	}

	/**
	 * In admin, date needs to be converted to UTC
	 */
	function convertDateToUTC(date) {
		return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds());
	}

	});

} else {
	jQuery(document).ready(function ($) {
		var datepicked = $( '.mc-datepicker' ).attr( 'data-value' );
		$( '.mc-datepicker' ).val( datepicked );
	});
}