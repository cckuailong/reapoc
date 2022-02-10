(function ($) {
	$(function () {
		// Delete single instances of recurring events.
		$( '.mc_response' ).hide();
		$('button.delete_occurrence').on( 'click', function () {
			var value = $(this).attr( 'data-value' );
			var id    = $(this).attr( 'data-event' );
			var begin = $(this).attr( 'data-begin' );
			var end   = $(this).attr( 'data-end' );
			var data = {
				'action': mc_data.action,
				'occur_id': value,
				'event_id': id,
				'occur_begin': begin,
				'occur_end': end,
				'security': mc_data.security
			};
			$.post( ajaxurl, data, function (response) {
				if ( response.success == 1 ) {
					$( "button[data-value='"+value+"']" ).parent( 'li' ).hide();
				}
				$('.mc_response').text( response.response ).show( 300 );
			}, "json" );
		});

		$( '.mc_add_new' ).hide();

		$( 'button.add-occurrence').on( 'click', function() {
			var expanded = $( this ).attr( 'aria-expanded' );
			if ( expanded == 'true' ) {
				$( this ).attr( 'aria-expanded', 'false' );
			} else {
				$( this ).attr( 'aria-expanded', 'true' );
			}
			$( '.mc_add_new' ).toggle();
		});

		$( 'button.save-occurrence').on( 'click', function() {
			var date    = $( '#r_begin' ).val();
			var begin   = $( '#r_time' ).val();
			var end     = $( '#r_endtime' ).val();
			var enddate = $( '#r_enddate' ).val();
			var event_id = $( 'input[name="event_id"]' ).val();
			var group_id = $( 'input[name="event_group_id"]' ).val();

			var data    = {
				'action': mc_data.recur,
				'event_id': event_id,
				'group_id': group_id,
				'event_date' : date,
				'event_time' : begin,
				'event_endtime' : end,
				'event_enddate' : enddate,
				'security': mc_data.security
			};
			$.post( ajaxurl, data, function (response) {
				if ( response.success == 1 ) {
					$( '.instance-list' ).append( '<li class="new"><strong>+</strong> ' + date + ' ' + begin + '</li>' );
				}
				$('.mc_response').text( response.response ).show( 300 );
			}, "json" );
		});

		// display notice informing users of lack of support for recur month by day.
		$( '#e_recur' ).on( 'change', function (e) {
			var recur = $(this).val();
			if ( recur == 'U' ) {
				$( '#e_every' ).attr( 'max', 1 ).val( 1 );
			} else {
				$( '#e_every' ).attr( 'max', 99 );
			}
		});

		var is_checked = $( 'input[id="e_allday"]' ).prop( "checked" );
		if ( ! is_checked ) {
			$( '.event_time_label' ).hide();
		}

		$( 'input[id="e_allday"]' ).change( function() {
			var checked = $(this).prop( "checked" );
			if ( checked ) {
				$( '.event_time_label' ).show();
			} else {
				$( '.event_time_label' ).hide();
			}
		});
	});
}(jQuery));