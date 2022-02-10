var time_buffer_value = 0;					// Customization of bufer time for DAN
var is_check_start_time_gone = false;		// Check  start time or end time for the time, which is gone already TODAY.
var start_time_checking_index;
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//FixIn: Deprecated
function prepare_tooltip( myParam ){
	wpbc_set_popover_in_cal( myParam );
}

//FixIn: Deprecated
function hoverDayTime( value, date_obj, resource_id ){
	wpbc_prepare_tooltip_content( value, date_obj, resource_id );
}

//FixIn: Deprecated
function is_this_time_selections_not_available( resource_id, form_elements ){
    var reslt = wpbc_is_this_time_selection_not_available( resource_id, form_elements );
    return reslt;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


/**
 * Prepare for showing popovers in calendar at front-end side
 *
 * @param resource_id
 */
function wpbc_set_popover_in_cal( resource_id ){

	var tooltip_day_class_4_show = " .timespartly";

	if ( is_show_availability_in_tooltips ){
		if ( wpdev_in_array( parent_booking_resources, resource_id ) )
			tooltip_day_class_4_show = " .datepick-days-cell";	//" .datepick-days-cell a";  							// each day
	}

	if ( is_show_cost_in_tooltips ){
		tooltip_day_class_4_show = " .datepick-days-cell";		//" .datepick-days-cell a";  							// each day
	}

	// Show tooltip at each day if time availability filter is set
	if ( typeof(global_avalaibility_times[ resource_id ]) != "undefined" ){
		if ( global_avalaibility_times[ resource_id ].length > 0 ) tooltip_day_class_4_show = " .datepick-days-cell";  	// each day
	}

	if ( 'function' === typeof( jQuery( "#calendar_booking" + resource_id + tooltip_day_class_4_show ).popover ) ){     	//FixIn: 7.0.1.2  - 2016-12-10
		jQuery( "#calendar_booking" + resource_id + tooltip_day_class_4_show ).popover( {
			  placement: 'top auto'
			, trigger  : 'hover'
			, delay    : {show: 500, hide: 1}
			, content  : ''
			, template : '<div class="popover popover_calendar_hover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
			, container: '#calendar_booking' + resource_id
			, html     : 'true'
		} );
	}
}


/**
 * Sort times array - prevent issue with same start/end times.
 * @param times_array  [
						   0: Array [ 10, "00", "01" ]
						   1: Array [ 12, "00", "01" ]
						   2: Array [ 12, "00", "02" ]
 					   ]
 * @returns 		   [
						   0: Array [ 10, "00", "01" ]
						   1: Array [ 12, "00", "02" ]
						   2: Array [ 12, "00", "01" ]
 					   ]
 */
function wpbc_sort_times_array( times_array ){

	var sort_time_arr=[];
	var sort_time_obj = [];
	var time_second = 0
	var is_it_endtime = 0;
	for ( var i = 0 ; i < times_array.length; i++ ){

		if ( times_array[ i ].length > 2 ) {

			time_second = parseInt( times_array[ i ][ 0 ] ) * 3600 + parseInt( times_array[ i ][ 1 ] ) * 60 + parseInt( times_array[ i ][ 2 ] );

			is_it_endtime = time_second % 10;   // 1 -start || 2 - end

			if ( 2 === is_it_endtime ) {
				time_second = time_second - 10;		// End time
			} else {
				time_second = time_second + 10;		// Start time
			}

			sort_time_obj[ sort_time_obj.length ] = { second: time_second, value: times_array[ i ] };
		}
	}

	// sort by seconds
	sort_time_obj.sort(function (a, b) {
	  return a.second - b.second;
	});

	for ( var i = 0 ; i < sort_time_obj.length; i++ ){
		sort_time_arr[ sort_time_arr.length ] = sort_time_obj[ i ].value;
	}
	return  sort_time_arr;
}


/**
 *  Prepare content for popover,  like Times, Costs, Availability, ....
 * @param value
 * @param date_obj
 * @param resource_id
 */
function wpbc_prepare_tooltip_content( value, date_obj, resource_id ){

	if ( date_obj == null ) return;

	var i = 0;
	var h = '';
	var m = '';
	var s = '';
	var td_class;

	var tooltip_time = '';
	var times_array = [];

	td_class = ( date_obj.getMonth() + 1 ) + '-' + date_obj.getDate() + '-' + date_obj.getFullYear();

	// Get Times from Approved dates
	var times_array_approved = wpbc_get_times_from_dates_arr( date_approved, resource_id, td_class );
	for ( i = 0; i < times_array_approved.length; i++ ){
		times_array[ times_array.length ] = times_array_approved[ i ];
	}

	// Get Times from Pending dates
	var times_array_pending  = wpbc_get_times_from_dates_arr( date2approve,  resource_id, td_class );
	for ( i = 0; i < times_array_pending.length; i++ ){
		times_array[ times_array.length ] = times_array_pending[ i ];
	}

 	// Time availability
	if ( typeof(hover_day_check_global_time_availability) == 'function' ){
		times_array = hover_day_check_global_time_availability( date_obj, resource_id, times_array );
	}


	//FixIn: 8.2.1.9
	if ( times_array.length > 0 ){
		times_array = wpbc_sort_times_array( times_array );
	}
	//	times_array.sort();


	for ( i = 0; i < times_array.length; i++ ){  // s = 2 - end time,   s = 1 - start time
		s = parseInt( times_array[ i ][ 2 ] );
		if ( s == 2 ){
			if ( tooltip_time == '' ) tooltip_time = '&nbsp;&nbsp;&nbsp;&nbsp;...&nbsp;&nbsp;&nbsp; - ';
		}      // End time and before was no dates so its start from start of date_obj
		if ( (tooltip_time == '') && (times_array[ i ][ 0 ] == '00') && (times_array[ i ][ 1 ] == '00') )
			tooltip_time = '&nbsp;&nbsp;&nbsp;&nbsp;...&nbsp;&nbsp;&nbsp;';  //start date at the midnight
		else if ( (i == (times_array.length - 1)) && (times_array[ i ][ 0 ] == '23') && (times_array[ i ][ 1 ] == '59') )
			tooltip_time += ' &nbsp;&nbsp;&nbsp;&nbsp;... ';
		else {
			var hours_show = times_array[ i ][ 0 ];
			var hours_show_sufix = '';
			if ( is_am_pm_inside_time ){
				if ( hours_show >= 12 ){
					hours_show = hours_show - 12;
					if ( hours_show == 0 ) hours_show = 12;
					hours_show_sufix = ' pm';
				} else {
					hours_show_sufix = ' am';
				}
			}
//Customization of bufer time for DAN
			if ( times_array[ i ][ 2 ] == '02' ){
				times_array[ i ][ 1 ] = (times_array[ i ][ 1 ] * 1) + time_buffer_value;
				if ( times_array[ i ][ 1 ] > 59 ){
					times_array[ i ][ 1 ] = times_array[ i ][ 1 ] - 60;
					hours_show = (hours_show * 1) + 1;
				}
				if ( times_array[ i ][ 1 ] < 10 ) times_array[ i ][ 1 ] = '0' + times_array[ i ][ 1 ];
			}

			tooltip_time += hours_show + ':' + times_array[ i ][ 1 ] + hours_show_sufix;
		}


		if ( s == 1 ){
			tooltip_time += ' - ';
			if ( i == (times_array.length - 1) ) tooltip_time += ' &nbsp;&nbsp;&nbsp;&nbsp;... ';
		}
		if ( s == 2 ){
			if ( typeof(wpbc_get_additional_info_for_tooltip) == 'function' ){
				tooltip_time += wpbc_get_additional_info_for_tooltip( resource_id, td_class, parseInt( times_array[ i ][ 0 ] * 60 ) + parseInt( times_array[ i ][ 1 ] ) );
			}
		   tooltip_time += '<br/>';
		}
	}

	// jQuery( '#calendar_booking'+resource_id+' td.cal4date-'+td_class )  // TODO: continue working here, check unshow times at full booked days
	if ( tooltip_time.indexOf( "undefined" ) > -1 ){
		tooltip_time = '';
	}
	else {
		if ( (tooltip_time != '') && (bk_highlight_timeslot_word != '') ){
			if ( is_booking_used_check_in_out_time === true )                   // Disable showing time tooltip,  if we are using check in/out times
				tooltip_time = '';
			else
				tooltip_time = '<span class="wpbc_booked_times_word">' + bk_highlight_timeslot_word + ' </span><br />' + tooltip_time;
		}
	}
	if ( typeof(getDayPrice4Show) == 'function' ){
		tooltip_time = getDayPrice4Show( resource_id, tooltip_time, td_class );
	}
	if ( typeof(getDayAvailability4Show) == 'function' ){
		tooltip_time = getDayAvailability4Show( resource_id, tooltip_time, td_class );
	}

	//tooltip_time = 'Already booked time slots: </br>' + tooltip_time ;
	jQuery( '#calendar_booking' + resource_id + ' td.cal4date-' + td_class ).attr( 'data-content', tooltip_time );
}


/**
 * Get Times array [ [ h, m, s ], ... ] from  Dates array ( date_approved || date2approve ), or return  empty  array otherwise
 * @arr dates_arr
 * @int resource_id
 * @string td_class
 * @returns {Array}
 */
function wpbc_get_times_from_dates_arr( dates_arr, resource_id, td_class ){

	var i;
	var h = '';
	var m = '';
	var s = '';
	var times_array = [];

	// Get Times from Dates Array

	if (   ( typeof( dates_arr[ resource_id ] ) !== 'undefined' )
		&& ( typeof( dates_arr[ resource_id ][ td_class ] ) !== 'undefined' )
		&& (
			   ( dates_arr[ resource_id ][ td_class ][ 0 ][ 3 ] != 0 )
			|| ( dates_arr[ resource_id ][ td_class ][ 0 ][ 4 ] != 0 )
		   )
	){
		for ( i = 0; i < dates_arr[ resource_id ][ td_class ].length; i++ ){

			h = dates_arr[ resource_id ][ td_class ][ i ][ 3 ];
			if ( h < 10 ) h = '0' + h;
			if ( h == 0 ) h = '00';

			m = dates_arr[ resource_id ][ td_class ][ i ][ 4 ];
			if ( m < 10 ) m = '0' + m;
			if ( m == 0 ) m = '00';

			s = dates_arr[ resource_id ][ td_class ][ i ][ 5 ];
			if ( s == 2 ) s = '02';
			if ( s == 1 ) s = '01';
			if ( s == 0 ) s = '00';

			times_array[ times_array.length ] = [ h, m, s ];
		}
	}

	return times_array;
}


/**
 * Check if in booking form  exist  times fields for booking for specific time-slot
 * @param resource_id
 * @param form_elements
 * @returns {boolean}
 */
function wpbc_is_time_field_in_booking_form( resource_id, form_elements ){											//FixIn: 8.2.1.28

	var count = form_elements.length;
	var start_time = false;
	var end_time = false;
	var duration = false;
	var element;

	/**
	 *  Get from booking form  'rangetime', 'durationtime', 'starttime', 'endtime',  if exists.
	 */
	for ( var i = 0; i < count; i++ ){

		element = form_elements[ i ];

		// Skip elements from garbage
		if ( jQuery( element ).closest( '.booking_form_garbage' ).length ){											//FixIn: 7.1.2.14
			continue;
		}

		if ( element.name != undefined ){
			var my_element = element.name; //.toString();
			if ( my_element.indexOf( 'rangetime' ) !== -1 ){                       	// Range Time

				return true;
			}
			if ( (my_element.indexOf( 'durationtime' ) !== -1) ){                	// Duration
				duration = element.value;
			}
			if ( my_element.indexOf( 'starttime' ) !== -1 ){                     	// Start Time
				start_time = element.value;
			}
			if ( my_element.indexOf( 'endtime' ) !== -1 ){                        	// End Time
				end_time = element.value;
			}
		}
	}

	// Duration get Values
	if ( ( duration !== false ) && ( start_time !== false ) ){  // we have Duration and Start time
		return true;
	}

	if ( ( start_time !== false ) && ( end_time !== false ) ){  // we have End time and Start time
		return true;
	}

	return false;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//TODO: Continue Refactoring here 2018-04-21
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	//PS: This function  from ../booking/inc/js/personal.js
	function wpbc_is_this_time_selection_not_available( resource_id, form_elements ){

		// Skip this checking if we are in the Admin  panel at Add booking page
		if ( location.href.indexOf( 'page=wpbc-new' ) > 0 ) {
			return false;
		}

		var count = form_elements.length;
		var start_time = false;
		var end_time = false;
		var duration = false;
		var element;
		var element_start = false;
		var element_end = false;
		var element_duration = false;
		var element_rangetime = false;

		/**
		 *  Get from booking form  'rangetime', 'durationtime', 'starttime', 'endtime',  if exists.
		 */
		for ( var i = 0; i < count; i++ ){

			element = form_elements[ i ];

			// Skip elements from garbage
			if ( jQuery( element ).closest( '.booking_form_garbage' ).length ){											//FixIn: 7.1.2.14
				continue;
			}

			if ( element.name != undefined ){
				var my_element = element.name; //.toString();
				if ( my_element.indexOf( 'rangetime' ) !== -1 ){                       	// Range Time
					if ( element.value == '' ){                                 										//FixIn: 7.0.Beta.19
						showErrorMessage( element, message_verif_requred , false );		//FixIn: 8.5.1.3
						return true;
					}
					var my_rangetime = element.value.split( '-' );
					if ( my_rangetime.length > 1 ){
						start_time = my_rangetime[ 0 ].replace( /(^\s+)|(\s+$)/g, "" ); 	// Trim
						end_time = my_rangetime[ 1 ].replace( /(^\s+)|(\s+$)/g, "" );
						element_rangetime = element;
					}
				}
				if ( (my_element.indexOf( 'durationtime' ) !== -1) ){                	// Duration
					duration = element.value;
					element_duration = element;
				}
				if ( my_element.indexOf( 'starttime' ) !== -1 ){                     	// Start Time
					start_time = element.value;
					element_start = element;
				}
				if ( my_element.indexOf( 'endtime' ) !== -1 ){                        	// End Time
					end_time = element.value;
					element_end = element;
				}
			}
		}


		// Duration get Values
		if ( (duration !== false) && (start_time !== false) ){  // we have Duration and Start time so  try to get End time

			var mylocalstarttime = start_time.split( ':' );
			var d = new Date( 1980, 1, 1, mylocalstarttime[ 0 ], mylocalstarttime[ 1 ], 0 );

			var my_duration = duration.split( ':' );
			my_duration = my_duration[ 0 ] * 60 * 60 * 1000 + my_duration[ 1 ] * 60 * 1000;
			d.setTime( d.getTime() + my_duration );

			var my_hours = d.getHours();
			if ( my_hours < 10 ) my_hours = '0' + (my_hours + '');
			var my_minutes = d.getMinutes();
			if ( my_minutes < 10 ) my_minutes = '0' + (my_minutes + '');

			// We are get end time
			end_time = (my_hours + '') + ':' + (my_minutes + '');
			if ( end_time == '00:00' ) end_time = '23:59';
		}


		if ( (start_time === false) || (end_time === false) ){                     // We do not have Start or End time or Both of them, so do not check it

			return false;

		} else {

			var valid_time = true;
			if ( (start_time == '') || (end_time == '') ) valid_time = false;

			if ( !isValidTimeTextField( start_time ) ) valid_time = false;
			if ( !isValidTimeTextField( end_time ) ) valid_time = false;

			if ( valid_time === true )
				if (
					(typeof(checkRecurentTimeInside) == 'function') &&
					(typeof(is_booking_recurrent_time) !== 'undefined') &&
					(is_booking_recurrent_time == true)
				){                                                                // Recheck Time here !!!
					valid_time = checkRecurentTimeInside( [ start_time, end_time ], resource_id );
				} else {

					if ( typeof(checkTimeInside) == 'function' ){
						valid_time = checkTimeInside( start_time, true, resource_id );
					}

					if ( valid_time === true ){
						if ( typeof(checkTimeInside) == 'function' ){
							valid_time = checkTimeInside( end_time, false, resource_id );
						}
					}
				}

			if ( valid_time !== true ){
				//return false;                                                  // do not show warning for setting pending days selectable,  if making booking for time-slot   //FixIn: 7.0.1.23
				if ( (is_booking_used_check_in_out_time) && (element_start !== false) && (element_end !== false) ){      //FixIn:6.1.1.1
					showMessageUnderElement( '#date_booking' + resource_id, message_checkinouttime_error, '' );
					makeScroll( '#calendar_booking' + resource_id );                  // Scroll to the calendar
					return true;
				}
				if ( element_rangetime !== false ) showErrorTimeMessage( message_rangetime_error, element_rangetime );
				if ( element_duration !== false ) showErrorTimeMessage( message_durationtime_error, element_duration );
				if ( element_start !== false ) showErrorTimeMessage( message_starttime_error, element_start );
				if ( element_end !== false ) showErrorTimeMessage( message_endtime_error, element_end );

				return true;

			} else {
				return false;
			}

		}


	}


	function isTimeTodayGone( myTime, sort_date_array ){
		var date_to_check = sort_date_array[ 0 ];
		if ( is_check_start_time_gone == false ){
			date_to_check = sort_date_array[ (sort_date_array.length - 1) ];
		}

		if ( parseInt( date_to_check[ 0 ] ) < parseInt( wpdev_bk_today[ 0 ] ) ) return true;
		if ( (parseInt( date_to_check[ 0 ] ) == parseInt( wpdev_bk_today[ 0 ] )) && (parseInt( date_to_check[ 1 ] ) < parseInt( wpdev_bk_today[ 1 ] )) )
			return true;
		if ( (parseInt( date_to_check[ 0 ] ) == parseInt( wpdev_bk_today[ 0 ] )) && (parseInt( date_to_check[ 1 ] ) == parseInt( wpdev_bk_today[ 1 ] )) && (parseInt( date_to_check[ 2 ] ) < parseInt( wpdev_bk_today[ 2 ] )) )
			return true;
		if ( (parseInt( date_to_check[ 0 ] ) == parseInt( wpdev_bk_today[ 0 ] )) &&
			(parseInt( date_to_check[ 1 ] ) == parseInt( wpdev_bk_today[ 1 ] )) &&
			(parseInt( date_to_check[ 2 ] ) == parseInt( wpdev_bk_today[ 2 ] )) ){
			var mytime_value = myTime.split( ":" );
			mytime_value = mytime_value[ 0 ] * 60 + parseInt( mytime_value[ 1 ] );

			var current_time_value = wpdev_bk_today[ 3 ] * 60 + parseInt( wpdev_bk_today[ 4 ] );

			if ( current_time_value > mytime_value ) return true;

		}
		return false;
	}


	function checkTimeInside( mytime, is_start_time, bk_type ){

		// Check time availability for global filters
		if ( typeof(check_entered_time_to_global_availability_time) == 'function' ){
			if ( !check_entered_time_to_global_availability_time( mytime, is_start_time, bk_type ) ) return false;
		}

		var my_dates_str = document.getElementById( 'date_booking' + bk_type ).value;                 // GET DATES From TEXTAREA

		return checkTimeInsideProcess( mytime, is_start_time, bk_type, my_dates_str );

	}


	function checkRecurentTimeInside( my_rangetime, bk_type ){

		var valid_time = true;
		var my_dates_str = document.getElementById( 'date_booking' + bk_type ).value;                 // GET DATES From TEXTAREA
		// recurrent time check for all days in loop

		var date_array = my_dates_str.split( ", " );
		if ( date_array.length == 2 ){ // This recheck is need for editing booking, with single day
			if ( date_array[ 0 ] == date_array[ 1 ] ){
				date_array = [ date_array[ 0 ] ];
			}
		}
		var temp_date_str = '';
		for ( var i = 0; i < date_array.length; i++ ){  // Get SORTED selected days array
			temp_date_str = date_array[ i ];
			if ( checkTimeInsideProcess( my_rangetime[ 0 ], true, bk_type, temp_date_str ) == false ) valid_time = false;
			if ( checkTimeInsideProcess( my_rangetime[ 1 ], false, bk_type, temp_date_str ) == false ) valid_time = false;

		}

		return valid_time;
	}


// Function check start and end time at selected days
	function checkTimeInsideProcess( mytime, is_start_time, bk_type, my_dates_str ){


		var date_array = my_dates_str.split( ", " );
		if ( date_array.length == 2 ){ // This recheck is need for editing booking, with single day
			if ( date_array[ 0 ] == date_array[ 1 ] ){
				date_array = [ date_array[ 0 ] ];
			}
		}

		var temp_elemnt;
		var td_class;
		var sort_date_array = [];
		var work_date_array = [];
		var times_array = [];
		var is_check_for_time;

		for ( var i = 0; i < date_array.length; i++ ){  // Get SORTED selected days array
			temp_elemnt = date_array[ i ].split( "." );
			sort_date_array[ i ] = [ temp_elemnt[ 2 ], temp_elemnt[ 1 ] + '', temp_elemnt[ 0 ] + '' ]; // [2009,7,1],...
		}
		sort_date_array.sort();                                                                   // SORT    D a t e s
		for ( i = 0; i < sort_date_array.length; i++ ){                                  // trnasform to integers
			sort_date_array[ i ] = [ parseInt( sort_date_array[ i ][ 0 ] * 1 ), parseInt( sort_date_array[ i ][ 1 ] * 1 ), parseInt( sort_date_array[ i ][ 2 ] * 1 ) ]; // [2009,7,1],...
		}

		if ( ((is_check_start_time_gone) && (is_start_time)) ||
			((!is_check_start_time_gone) && (!is_start_time)) ){

			if ( isTimeTodayGone( mytime, sort_date_array ) ) return false;
		}
		//  CHECK FOR BOOKING INSIDE OF     S E L E C T E D    DAY RANGE AND FOR TOTALLY BOOKED DAYS AT THE START AND END OF RANGE
		work_date_array = sort_date_array;
		for ( var j = 0; j < work_date_array.length; j++ ){
			td_class = work_date_array[ j ][ 1 ] + '-' + work_date_array[ j ][ 2 ] + '-' + work_date_array[ j ][ 0 ];

			if ( (j == 0) || (j == (work_date_array.length - 1)) ) is_check_for_time = true;         // Check for time only start and end time
			else is_check_for_time = false;

			// Get dates and time from pending dates
			if ( typeof(date2approve[ bk_type ]) !== 'undefined' ){
				if ( (typeof(date2approve[ bk_type ][ td_class ]) !== 'undefined') ){
					if ( ! is_check_for_time ){
						return false;
					} // its mean that this date is booked inside of range selected dates
					if ( (date2approve[ bk_type ][ td_class ][ 0 ][ 3 ] != 0) || (date2approve[ bk_type ][ td_class ][ 0 ][ 4 ] != 0) ){
						// Evrything good - some time is booked check later
					} else {
						return false;
					} // its mean that this date tottally booked
				}
			}

			// Get dates and time from pending dates
			if ( typeof(date_approved[ bk_type ]) !== 'undefined' ){
				if ( (typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined') ){
					if ( !is_check_for_time ){
						return false;
					} // its mean that this date is booked inside of range selected dates
					if ( (date_approved[ bk_type ][ td_class ][ 0 ][ 3 ] != 0) || (date_approved[ bk_type ][ td_class ][ 0 ][ 4 ] != 0) ){
						// Evrything good - some time is booked check later
					} else {
						return false;
					} // its mean that this date tottally booked
				}
			}
		}  ///////////////////////////////////////////////////////////////////////////////////////////////////////


		// Check    START   OR    END   time for time no in correct fee range
		if ( is_start_time ) work_date_array = sort_date_array[ 0 ];
		else work_date_array = sort_date_array[ sort_date_array.length - 1 ];

		td_class = work_date_array[ 1 ] + '-' + work_date_array[ 2 ] + '-' + work_date_array[ 0 ];

		// Get dates and time from pending dates
		if ( typeof(date2approve[ bk_type ]) !== 'undefined' )
			if ( typeof(date2approve[ bk_type ][ td_class ]) !== 'undefined' )
				for ( i = 0; i < date2approve[ bk_type ][ td_class ].length; i++ ){
					h = date2approve[ bk_type ][ td_class ][ i ][ 3 ];
					if ( h < 10 ) h = '0' + h;
					if ( h == 0 ) h = '00';
					m = date2approve[ bk_type ][ td_class ][ i ][ 4 ];
					if ( m < 10 ) m = '0' + m;
					if ( m == 0 ) m = '00';
					s = date2approve[ bk_type ][ td_class ][ i ][ 5 ];

//Customization of bufer time for DAN
					if ( s == '02' ){
						m = (m * 1) + time_buffer_value;
						if ( m > 59 ){
							m = m - 60;
							h = (h * 1) + 1;
						}
						if ( m < 10 ) m = '0' + m;
					}

					times_array[ times_array.length ] = [ h, m, s ];
				}

		// Get dates and time from pending dates
		if ( typeof(date_approved[ bk_type ]) !== 'undefined' )
			if ( typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined' )
				for ( i = 0; i < date_approved[ bk_type ][ td_class ].length; i++ ){
					h = date_approved[ bk_type ][ td_class ][ i ][ 3 ];
					if ( h < 10 ) h = '0' + h;
					if ( h == 0 ) h = '00';
					m = date_approved[ bk_type ][ td_class ][ i ][ 4 ];
					if ( m < 10 ) m = '0' + m;
					if ( m == 0 ) m = '00';
					s = date_approved[ bk_type ][ td_class ][ i ][ 5 ];

//Customization of bufer time for DAN
					if ( s == '02' ){
						m = (m * 1) + time_buffer_value;
						if ( m > 59 ){
							m = m - 60;
							h = (h * 1) + 1;
						}
						if ( m < 10 ) m = '0' + m;
					}


					times_array[ times_array.length ] = [ h, m, s ];
				}


		times_array.sort();                     // SORT TIMES

		var times_in_day = [];                  // array with all times
		var times_in_day_interval_marks = [];   // array with time interval marks 1- stsrt time 2 - end time


		for ( i = 0; i < times_array.length; i++ ){
			s = times_array[ i ][ 2 ];         // s = 2 - end time,   s = 1 - start time
			// Start close interval
			if ( (s == 2) && (i == 0) ){
				times_in_day[ times_in_day.length ] = 0;
				times_in_day_interval_marks[ times_in_day_interval_marks.length ] = 1;
			}
			// Normal
			times_in_day[ times_in_day.length ] = times_array[ i ][ 0 ] * 60 + parseInt( times_array[ i ][ 1 ] );
			times_in_day_interval_marks[ times_in_day_interval_marks.length ] = s;
			// End close interval
			if ( (s == 1) && (i == (times_array.length - 1)) ){
				times_in_day[ times_in_day.length ] = (24 * 60);
				times_in_day_interval_marks[ times_in_day_interval_marks.length ] = 2;
			}
		}

		// Get time from entered time
		var mytime_value = mytime.split( ":" );
		mytime_value = mytime_value[ 0 ] * 60 + parseInt( mytime_value[ 1 ] );

//alert('My time:'+ mytime_value + '  List of times: '+ times_in_day + '  Saved indexes: ' + start_time_checking_index + ' Days: ' + sort_date_array ) ;

		var start_i = 0;
		if ( start_time_checking_index != undefined )
			if ( start_time_checking_index[ 0 ] != undefined )
				if ( (!is_start_time) && (sort_date_array.length == 1) ){
					start_i = start_time_checking_index[ 0 ];
					/*start_i++;*/
				}
		i = start_i;

		// Main checking inside a day
		for ( i = start_i; i < times_in_day.length; i++ ){
			times_in_day[ i ] = parseInt( times_in_day[ i ] );
			mytime_value = parseInt( mytime_value );
			if ( is_start_time ){
				if ( mytime_value > times_in_day[ i ] ){
					// Its Ok, lets Loop to next item
				} else if ( mytime_value == times_in_day[ i ] ){
					if ( times_in_day_interval_marks[ i ] == 1 ){
						return false;     //start time is begin with some other interval
					} else {
						if ( (i + 1) <= (times_in_day.length - 1) ){
							if ( times_in_day[ i + 1 ] <= mytime_value ) return false;  //start time  is begin with next elemnt interval
							else {                                                 // start time from end of some other
								if ( sort_date_array.length > 1 )
									if ( (i + 1) <= (times_in_day.length - 1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
								start_time_checking_index = [ i, td_class, mytime_value ];
								return true;
							}
						}
						if ( sort_date_array.length > 1 )
							if ( (i + 1) <= (times_in_day.length - 1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
						start_time_checking_index = [ i, td_class, mytime_value ];
						return true;                                            // start time from end of some other
					}
				} else if ( mytime_value < times_in_day[ i ] ){
					if ( times_in_day_interval_marks[ i ] == 2 ){
						return false;     // start time inside of some interval
					} else {
						if ( sort_date_array.length > 1 )
							if ( (i + 1) <= (times_in_day.length - 1) ) return false;   // Its mean that we make end booking at some other day then this and we have some booking time at this day after start booking  - its wrong
						start_time_checking_index = [ i, td_class, mytime_value ];
						return true;
					}
				}
			} else {
				if ( sort_date_array.length == 1 ){

					if ( start_time_checking_index != undefined )
						if ( start_time_checking_index[ 2 ] != undefined )

							if ( (start_time_checking_index[ 2 ] == times_in_day[ i ]) && (times_in_day_interval_marks[ i ] == 2) ){    // Good, because start time = end of some other interval and we need to get next interval for current end time.
							} else if ( times_in_day[ i ] < mytime_value ) return false;                 // some interval begins before end of curent "end time"
							else {
								if ( start_time_checking_index[ 2 ] >= mytime_value ) return false;  // we are select only one day and end time is earlythe starttime its wrong
								return true;                                                    // if we selected only one day so evrything is fine and end time no inside some other intervals
							}
				} else {
					if ( times_in_day[ i ] < mytime_value ) return false;                 // Some other interval start before we make end time in the booking at the end day selection
					else return true;
				}

			}
		}

		if ( is_start_time ) start_time_checking_index = [ i, td_class, mytime_value ];
		else {
			if ( start_time_checking_index != undefined )
				if ( start_time_checking_index[ 2 ] != undefined )
					if ( (sort_date_array.length == 1) && (start_time_checking_index[ 2 ] >= mytime_value) ) return false;  // we are select only one day and end time is earlythe starttime its wrong
		}
		return true;
	}

	//PS: This function  from ../booking/inc/js/personal.js
	function showErrorTimeMessage( my_message, element ){

		var element_name = element.name;
		makeScroll( element );
		//FixIn: 8.7.11.10
		if ( jQuery( "[name='" + element_name + "']" ).is( ':visible' ) ){

			jQuery( "[name='" + element_name + "']" )
				.css( {'border': '1px solid red'} )
				.fadeOut( 350 )
				.fadeIn( 500 )
				.animate( {opacity: 1}, 4000 )
				.animate( {border: '1px solid #DFDFDF'}, 100 )
			;  // mark red border
		}

		jQuery( "[name='" + element_name + "']" )
			.after( '<div class="wpdev-help-message alert alert-warning">' + my_message + '</div>' ); // Show message
		jQuery( ".wpdev-help-message" )
		//                    .css( {'color' : 'red'} )
			.animate( {opacity: 1}, 10000 )
			.fadeOut( 2000 );   // hide message
		jQuery( element).trigger( 'focus' );    //FixIn: 8.7.11.12
		return true;
	}

	//PS: This function  from ../booking/inc/js/personal.js
	function isValidTimeTextField( timeStr ){
		// Checks if time is in HH:MM AM/PM format.
		// The seconds and AM/PM are optional.

		var timePat = /^(\d{1,2}):(\d{2})(\s?(AM|am|PM|pm))?$/;

		var matchArray = timeStr.match( timePat );
		if ( matchArray == null ){
			return false; //("<?php _e('Time is not in a valid format. Use this format HH:MM or HH:MM AM/PM'); ?>");
		}
		var hour = matchArray[ 1 ];
		var minute = matchArray[ 2 ];
		var ampm = matchArray[ 4 ];

		if ( ampm == "" ){
			ampm = null
		}

		if ( hour < 0 || hour > 24 ){		//FixIn: 8.3.1.1
			return false; //("<?php _e('Hour must be between 1 and 12. (or 0 and 23 for military time)'); ?>");
		}
		if ( hour > 12 && ampm != null ){
			return false; //("<?php _e('You can not specify AM or PM for military time.'); ?>");
		}
		if ( minute < 0 || minute > 59 ){
			return false; //("<?php _e('Minute must be between 0 and 59.'); ?>");
		}
		return true;
	}


	//FixIn: 8.4.7.6
	// Disable Booked Time Slots in selectbox
	function bkDisableBookedTimeSlots( all_dates, bk_type ){

		var inst = jQuery.datepick._getInst( document.getElementById( 'calendar_booking' + bk_type ) );
		var td_class;

		var time_slot_field_name = 'select[name="rangetime' + bk_type + '"]';
		var time_slot_field_name2 = 'select[name="rangetime' + bk_type + '[]"]';

		var start_time_slot_field_name = 'select[name="starttime' + bk_type + '"]';
		var start_time_slot_field_name2 = 'select[name="starttime' + bk_type + '[]"]';

		var end_time_slot_field_name = 'select[name="endtime' + bk_type + '"]';
		var end_time_slot_field_name2 = 'select[name="endtime' + bk_type + '[]"]';


		// HERE WE WILL DISABLE ALL OPTIONS IN RANGE TIME INTERVALS FOR SINGLE DAYS SELECTIONS FOR THAT DAYS WHERE HOURS ALREADY BOOKED
		//here is not range selections
		all_dates = get_first_day_of_selection( all_dates );
		if ( (bk_days_selection_mode == 'single')  ){   // Only single day selections here		//FixIn: 8.7.11.6
		//if ( ( bk_days_selection_mode == 'single' ) || ( bk_days_selection_mode == 'multiple' ) ) {
			var current_single_day_selections = all_dates.split( '.' );
			td_class = (current_single_day_selections[ 1 ] * 1) + '-' + (current_single_day_selections[ 0 ] * 1) + '-' + (current_single_day_selections[ 2 ] * 1);
			var times_array = [];

			jQuery(   time_slot_field_name + ' option:disabled,' + time_slot_field_name2 + ' option:disabled,'
					+ start_time_slot_field_name + ' option:disabled,' + start_time_slot_field_name2 + ' option:disabled,'
					+ end_time_slot_field_name + ' option:disabled,' + end_time_slot_field_name2 + ' option:disabled'
				  ).removeClass( 'booked' );   // Remove class "booked"
			jQuery( time_slot_field_name + ' option:disabled,' + time_slot_field_name2 + ' option:disabled,'
					+ start_time_slot_field_name + ' option:disabled,' + start_time_slot_field_name2 + ' option:disabled,'
					+ end_time_slot_field_name + ' option:disabled,' + end_time_slot_field_name2 + ' option:disabled'
			      ).prop( 'disabled', false );  // Make active all times


			if ( jQuery( time_slot_field_name + ',' + time_slot_field_name2 + ',' + start_time_slot_field_name + ',' + start_time_slot_field_name2 ).length == 0 ) return;  // WE DO NOT HAVE RANGE SELECTIONS AT THIS FORM SO JUST RETURN

			var range_time_object = jQuery( time_slot_field_name + ' option:first,' + time_slot_field_name2 + ' option:first,' + start_time_slot_field_name + ' option:first,' + start_time_slot_field_name2 + ' option:first' );
			if ( range_time_object == undefined ) return;  // WE DO NOT HAVE RANGE SELECTIONS AT THIS FORM SO JUST RETURN

			// Get dates and time from aproved dates
			if ( typeof(date_approved[ bk_type ]) !== 'undefined' )
				if ( typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined' ){
					if ( (date_approved[ bk_type ][ td_class ][ 0 ][ 3 ] != 0) || (date_approved[ bk_type ][ td_class ][ 0 ][ 4 ] != 0) ){
						for ( i = 0; i < date_approved[ bk_type ][ td_class ].length; i++ ){
							h = date_approved[ bk_type ][ td_class ][ i ][ 3 ];
							if ( h < 10 ) h = '0' + h;
							if ( h == 0 ) h = '00';
							m = date_approved[ bk_type ][ td_class ][ i ][ 4 ];
							if ( m < 10 ) m = '0' + m;
							if ( m == 0 ) m = '00';
							s = date_approved[ bk_type ][ td_class ][ i ][ 5 ];
							if ( s == 2 ) s = '02';
							times_array[ times_array.length ] = [ h, m, s ];
						}
					}
				}

			// Get dates and time from pending dates
			if ( typeof(date2approve[ bk_type ]) !== 'undefined' )
				if ( typeof(date2approve[ bk_type ][ td_class ]) !== 'undefined' )
					if ( (date2approve[ bk_type ][ td_class ][ 0 ][ 3 ] != 0) || (date2approve[ bk_type ][ td_class ][ 0 ][ 4 ] != 0) ) //check for time here
					{
						for ( i = 0; i < date2approve[ bk_type ][ td_class ].length; i++ ){
							h = date2approve[ bk_type ][ td_class ][ i ][ 3 ];
							if ( h < 10 ) h = '0' + h;
							if ( h == 0 ) h = '00';
							m = date2approve[ bk_type ][ td_class ][ i ][ 4 ];
							if ( m < 10 ) m = '0' + m;
							if ( m == 0 ) m = '00';
							s = date2approve[ bk_type ][ td_class ][ i ][ 5 ];
							if ( s == 2 ) s = '02';
							times_array[ times_array.length ] = [ h, m, s ];
						}
					}

			// Check about situations, when  we have end time without start  time (... - 09:00) or start  time without end time (21:00 - ...)
			times_array.sort();
			if ( times_array.length > 0 ){
				s = parseInt( times_array[ 0 ][ 2 ] );
				if ( s == 2 ){
					times_array[ times_array.length ] = [ '00', '00', '01' ];
					times_array.sort();
				}
				s = parseInt( times_array[ (times_array.length - 1) ][ 2 ] );
				if ( s == 1 ){
					times_array[ times_array.length ] = [ '23', '59', '02' ];
					times_array.sort();
				}
			}


			var check_times_fields=[
									[ time_slot_field_name, time_slot_field_name2 ],
									[ start_time_slot_field_name, start_time_slot_field_name2 ]
							];
			// Check about disabling "end times" only in "single day" selection  mode		//FixIn: 8.7.2.1
			if ( ( bk_days_selection_mode == 'single' ) ) {
				check_times_fields.push( [ end_time_slot_field_name, end_time_slot_field_name2 ] );
			}

			for ( var ctf= 0; ctf < check_times_fields.length; ctf++ ){

				var time_field_to_check = check_times_fields[ ctf ];

				//TODO: continue here with  time_field_to_check
				var removed_time_slots = is_time_slot_booked_for_this_time_array( bk_type, times_array, td_class , time_field_to_check );
//console.log( 'removed_time_slots',time_field_to_check, removed_time_slots );

				var my_time_value = jQuery( time_field_to_check[ 0 ] + ' option,' + time_field_to_check[ 1 ] + ' option' );

				for ( j = 0; j < my_time_value.length; j++ ){
					if ( wpdev_in_array( removed_time_slots, j ) ){
						jQuery( time_field_to_check[ 0 ] + ' option:eq(' + j + '),' + time_field_to_check[ 1 ] + ' option:eq(' + j + ')' ).attr( 'disabled', 'disabled' ); // Make disable some options
						jQuery( time_field_to_check[ 0 ] + ' option:eq(' + j + '),' + time_field_to_check[ 1 ] + ' option:eq(' + j + ')' ).addClass( 'booked' );           // Add "booked" CSS class

						if (
							jQuery( time_field_to_check[ 0 ] + ' option:eq(' + j + '),' + time_field_to_check[ 1 ] + ' option:eq(' + j + ')' ).attr( 'selected' )
						){  // iF THIS ELEMENT IS SELECTED SO REMOVE IT FROM THIS TIME
							jQuery( time_field_to_check[ 0 ] + ' option:eq(' + j + '),' + time_field_to_check[ 1 ] + ' option:eq(' + j + ')' ).removeAttr( 'selected' );

							if ( IEversion_4_bk == 7 ){ // Emulate disabling option in selectboxes for IE7 - its set selected option, which is not disabled

								var rangetime_element = document.getElementsByName( "rangetime" + bk_type );
								if ( typeof (rangetime_element) != 'undefined' && rangetime_element != null ){
									set_selected_first_not_disabled_option_IE7( document.getElementsByName( "rangetime" + bk_type )[ 0 ] );
								}

								var start_element = document.getElementsByName( "starttime" + bk_type );
								if ( typeof (start_element) != 'undefined' && start_element != null ){
									set_selected_first_not_disabled_option_IE7( document.getElementsByName( "starttime" + bk_type )[ 0 ] );
								}

							}
						}
					}
				}

			}

			if ( IEversion_4_bk == 7 ){ // Emulate disabling option in selectboxes for IE7 - its set grayed text options, which is disabled
				emulate_disabled_options_to_gray_IE7( "rangetime" + bk_type );
				emulate_disabled_options_to_gray_IE7( "starttime" + bk_type );
				emulate_disabled_options_to_gray_IE7( "endtime" + bk_type );
			}
		}

		//FixIn: 8.7.11.9
		jQuery( ".booking_form_div" ).trigger( 'wpbc_hook_timeslots_disabled', [bk_type, all_dates] );					// Trigger hook on disabling timeslots.		Usage: 	jQuery( ".booking_form_div" ).on( 'wpbc_hook_timeslots_disabled', function ( event, bk_type, all_dates ){ ... } );

	}


	// Check if this IE and get version of IE otherwise setversion of IE to 0
	var isIE_4_bk = (navigator.appName == "Microsoft Internet Explorer");
	var IEversion_4_bk = navigator.appVersion;
	if ( isIE_4_bk ){
		IEversion_4_bk = parseInt( IEversion_4_bk.substr( IEversion_4_bk.indexOf( "MSIE" ) + 4 ) );
	} else {
		IEversion_4_bk = 0;
	}
	// IE7 select box emulate functions for disabling select boxes:
	if ( IEversion_4_bk == 7 ){

		window.onload = function (){
			if ( document.getElementsByTagName ){
				var s = document.getElementsByTagName( "select" );

				if ( s.length > 0 ){
					window.select_current = new Array();

					for ( var i = 0, select; select = s[ i ]; i++ ){
						select.onfocus = function (){
							window.select_current[ this.id ] = this.selectedIndex;
						}
						select.onchange = function (){
							set_selected_previos_selected_option_IE7( this );
						}
						emulate_disabled_options_to_gray_IE7( select.name );
					}
				}
			}
		}

		function set_selected_previos_selected_option_IE7( e ){
			if ( e.options[ e.selectedIndex ].disabled ){
				e.selectedIndex = window.select_current[ e.id ];
			}
		}

		function set_selected_first_not_disabled_option_IE7( e ){

			if ( e.options[ e.selectedIndex ].disabled ){
				for ( var i = 0, option; option = e.options[ i ]; i++ ){
					if ( !option.disabled ){
						e.selectedIndex = i;
						return 0;
					}
				}
			}
			return 0;
		}

		function emulate_disabled_options_to_gray_IE7( ename ){

			jQuery( 'select[name="' + ename + '"] option,select[name="' + ename + '[]"] option' ).each( function ( index ){
				if ( jQuery( this ).prop( 'disabled' ) ){
					jQuery( this ).css( 'color', 'graytext' );
				} else {
					jQuery( this ).css( 'color', 'menutext' );
				}
			} );
			/*
			for (var i=0, option; option = e.options[i]; i++) {

					if (option.disabled) { option.style.color = "graytext";}
					else {                 option.style.color = "menutext";}
			}*/
		}
	}


	//FixIn: 8.4.7.6
	function is_time_slot_booked_for_this_time_array( bk_type, times_array, td_class, time_field_to_check ) {

		// Get time element from possible conditional section                  //FixIn: 7.0.Beta.11
		if ( typeof(wpbc_get_conditional_section_id_for_weekday) == 'function' ){
			var conditional_field_element_id = wpbc_get_conditional_section_id_for_weekday( td_class, bk_type );
			if ( conditional_field_element_id !== false ){
				time_field_to_check[ 0 ] = conditional_field_element_id + ' ' + time_field_to_check[ 0 ];
				time_field_to_check[ 1 ] = conditional_field_element_id + ' ' + time_field_to_check[ 1 ];
			}
		}


		times_array.sort();
		var my_time_value = '';
		var j;
		var bk_time_slot_selection = '';
		var minutes_booked;
		var minutes_slot;
		var my_range_time;

		var removed_time_slots = [];
		if ( times_array.length > 0 ){                                         //FixIn: 6.1.1.6
			//Situation when we have first time option as End time. So  we need to  add start  time to  the midnight
			if ( parseInt( times_array[ 0 ][ 2 ] ) == 2 ){
				var new_times_array = [];
				new_times_array[ new_times_array.length ] = [ '00', '00', '01' ];
				for ( var i = 0; i < times_array.length; i++ ){
					new_times_array[ new_times_array.length ] = times_array[ i ];
				}
				times_array = new_times_array;
			}
			//Situation when we have last time option as Start time. So  we need to  add End  time to  end of this times
			if ( parseInt( times_array[ (times_array.length - 1) ][ 2 ] ) == 1 ){
				times_array[ times_array.length ] = [ '23', '59', '02' ];
			}
		}


		for ( var i = 0; i < times_array.length; i++ ){  // s = 2 - end time,   s = 1 - start time
			var s = parseInt( times_array[ i ][ 2 ] );

			if ( i > 0 ){

				if ( s == 2 ){
					my_range_time = times_array[ i - 1 ][ 0 ] + ':' + times_array[ i - 1 ][ 1 ] + ' - ' + times_array[ i ][ 0 ] + ':' + times_array[ i ][ 1 ];
					my_time_value = jQuery( time_field_to_check[ 0 ] + ' option,' + time_field_to_check[ 1 ] + ' option' );

					for ( j = 0; j < my_time_value.length; j++ ){

						if ( my_time_value[ j ].value == my_range_time ){  // Mark as disable this option


							removed_time_slots[ removed_time_slots.length ] = j;
							//return  true;

						} else {
							// We will recheck here if, may  be some interval here inside of already booked intervals, so then we need to disable it.
							bk_time_slot_selection = my_time_value[ j ].value;
							var is_time_range = bk_time_slot_selection.indexOf( "-" );

							if ( is_time_range > -1 ){ // Timeslots
								bk_time_slot_selection = bk_time_slot_selection.split( '-' );
								bk_time_slot_selection[ 0 ] = bk_time_slot_selection[ 0 ].trim();		//FixIn: 8.7.11.12
								bk_time_slot_selection[ 1 ] = bk_time_slot_selection[ 1 ].trim();

								bk_time_slot_selection[ 0 ] = bk_time_slot_selection[ 0 ].split( ':' );
								bk_time_slot_selection[ 1 ] = bk_time_slot_selection[ 1 ].split( ':' );

								// Get only minutes
								minutes_booked = [ (parseInt( times_array[ i - 1 ][ 0 ] * 60 ) + parseInt( times_array[ i - 1 ][ 1 ] )), (parseInt( times_array[ i ][ 0 ] * 60 ) + parseInt( times_array[ i ][ 1 ] )) ];
								minutes_slot = [ (parseInt( bk_time_slot_selection[ 0 ][ 0 ] * 60 ) + parseInt( bk_time_slot_selection[ 0 ][ 1 ] )), (parseInt( bk_time_slot_selection[ 1 ][ 0 ] * 60 ) + parseInt( bk_time_slot_selection[ 1 ][ 1 ] )) ];


								if (
									((minutes_booked[ 0 ] >= minutes_slot[ 0 ]) && (minutes_booked[ 0 ] < minutes_slot[ 1 ])) ||
									((minutes_booked[ 1 ] > minutes_slot[ 0 ]) && (minutes_booked[ 1 ] <= minutes_slot[ 1 ]))
									||
									((minutes_slot[ 0 ] >= minutes_booked[ 0 ]) && (minutes_slot[ 0 ] < minutes_booked[ 1 ])) ||
									((minutes_slot[ 1 ] > minutes_booked[ 0 ]) && (minutes_slot[ 1 ] <= minutes_booked[ 1 ]))
								){
									removed_time_slots[ removed_time_slots.length ] = j;
									//return  true;
								}
							} else { // Just  some time (like start time)
								bk_time_slot_selection = bk_time_slot_selection.split( ':' );

								// Get only minutes
								minutes_booked = [ (parseInt( times_array[ i - 1 ][ 0 ] * 60 ) + parseInt( times_array[ i - 1 ][ 1 ] )) , (parseInt( times_array[ i ][ 0 ] * 60 ) + parseInt( times_array[ i ][ 1 ] )) ];
								minutes_slot = [ (parseInt( bk_time_slot_selection[ 0 ] * 60 ) + parseInt( bk_time_slot_selection[ 1 ] )) ];

								//FixIn: 8.6.1.17
								var is_end_time = time_field_to_check[ 0 ].indexOf( "endtime" );

								if ( -1 !== is_end_time ){		// This is End Time

									// Transform  to seconds,  and minus 10 second
									minutes_booked[ 0 ] = minutes_booked[ 0 ] * 60;
									minutes_booked[ 1 ] = minutes_booked[ 1 ] * 60;
									minutes_slot[ 0 ] = minutes_slot[ 0 ] * 60 - 10;
								}
								if (
									((minutes_slot[ 0 ] >= minutes_booked[ 0 ]) && (minutes_slot[ 0 ] < minutes_booked[ 1 ]))
								){
									removed_time_slots[ removed_time_slots.length ] = j;
									//return  true;
								}

							}

						}

					}
				}
			}

		}

		return removed_time_slots;
	}


	// Times
	function isDayFullByTime( bk_type, td_class ) {

	   var times_array = [];
		var time_slot_field_name = 'select[name="rangetime' + bk_type + '"]';
		var time_slot_field_name2 = 'select[name="rangetime' + bk_type + '[]"]';

		// Get rangetime element from possible conditional Weekdays section                  //FixIn: 5.4.5.2
		if( typeof( wpbc_get_conditional_section_id_for_weekday ) == 'function' ) {
			var conditional_field_element_id = wpbc_get_conditional_section_id_for_weekday( td_class, bk_type );
			if ( conditional_field_element_id !== false ) {
				time_slot_field_name  = conditional_field_element_id + ' ' + 'select[name="rangetime' + bk_type + '"]';
				time_slot_field_name2 = conditional_field_element_id + ' ' + 'select[name="rangetime' + bk_type + '[]"]';
			}
		}

		// Get rangetime element from possible conditional Seasonal section                  //FixIn: 8.4.5.3
		if ( typeof(wpbc_get_conditional_section_id_for_seasons) == 'function' ){
			var conditional_field_element_id2 = wpbc_get_conditional_section_id_for_seasons( td_class, bk_type );

			if ( conditional_field_element_id2 !== false ){
				time_slot_field_name  = conditional_field_element_id2 + ' ' + 'select[name="rangetime' + bk_type + '"]';
				time_slot_field_name2 = conditional_field_element_id2 + ' ' + 'select[name="rangetime' + bk_type + '[]"]';
			}
		}

//console.log( time_slot_field_name, time_slot_field_name2 );

	   // Get dates and time from aproved dates
	   if(typeof(date_approved[ bk_type ]) !== 'undefined')
	   if(typeof(date_approved[ bk_type ][ td_class ]) !== 'undefined') {
		  for ( i=0; i< date_approved[ bk_type ][ td_class ].length; i++){
			 if( ( date_approved[ bk_type ][ td_class ][0][3] != 0) ||  ( date_approved[ bk_type ][ td_class ][0][4] != 0) ) {
				h = date_approved[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
				m = date_approved[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
				s = date_approved[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
				times_array[ times_array.length ] = [h,m,s];
			 }
		 }
	   }

	   // Get dates and time from pending dates
	   if(typeof( date2approve[ bk_type ]) !== 'undefined')
	   if(typeof( date2approve[ bk_type ][ td_class ]) !== 'undefined')
		  for ( i=0; i< date2approve[ bk_type ][ td_class ].length; i++){
			if( ( date2approve[ bk_type ][ td_class ][0][3] != 0) ||  ( date2approve[ bk_type ][ td_class ][0][4] != 0) ) {
				h = date2approve[ bk_type ][ td_class ][i][3];if (h < 10) h = '0' + h;if (h == 0) h = '00';
				m = date2approve[ bk_type ][ td_class ][i][4];if (m < 10) m = '0' + m;if (m == 0) m = '00';
				s = date2approve[ bk_type ][ td_class ][i][5];if (s == 2) s = '02';
				times_array[ times_array.length ] = [h,m,s];
			  }
		   }

		times_array.sort();

		//Customization Bence - make day with start and end time - unavailable
		//var is_start_here = false;
		//var is_end_here = false;
		//for (var jj=0; jj< times_array.length; jj++){
		//    if (times_array[jj][2]=='01' ) is_start_here = true;
		//    if (times_array[jj][2]=='02' ) is_end_here = true;
		//}
		//if ( (is_start_here) && (is_end_here) ) return true;

	// check here according time ranges selection
	// and check all slots for reserVATION.
	// IF ALL SLOTS ARE RESERVED, INSIDE OF times_array
	// SO THEN RETURN TRUE

		//var is_element_exist = jQuery( time_slot_field_name+','+time_slot_field_name2 ).length;                                   //FixIn:6.1.1.6   - Previously was this line
		var start_time_fields  = 'select[name="starttime' + bk_type + '"]';               //FixIn:6.1.1.6
		var start_time_fields2 = 'select[name="starttime' + bk_type + '[]"]';               //FixIn:6.1.1.6
		var is_element_exist = jQuery( time_slot_field_name + ',' + time_slot_field_name2 + ',' + start_time_fields + ',' + start_time_fields2 ).length;       //FixIn:6.1.1.6

		if (is_element_exist) {
			var my_timerange_value = jQuery( time_slot_field_name + ' option,'+time_slot_field_name2 + ' option');
			var my_st_en_times;
			var my_temp_time;
			var times_ranges_array=[];

			for (var j=0; j< my_timerange_value.length; j++){

				my_st_en_times = my_timerange_value[j].value.split(' - ');

				my_temp_time = my_st_en_times[0].split(':');
				times_ranges_array[ times_ranges_array.length ] = [ my_temp_time[0], my_temp_time[1], '01' ]; //Start time

				my_temp_time = my_st_en_times[1].split(':');
				times_ranges_array[ times_ranges_array.length ] = [ my_temp_time[0], my_temp_time[1], '02' ]; //End time
			}

			// check if all time slots from the selectbox are the booked inside of this day. Simple checking for the same
			if (times_array.length ==  times_ranges_array.length) {
				var is_all_same = true;
				for ( var i=0; i< times_array.length; i++){
					 if (
						  ( times_array[i][0] != times_ranges_array[i][0] ) ||
						  ( times_array[i][1] != times_ranges_array[i][1] ) ||
						  ( times_array[i][2] != times_ranges_array[i][2] )
						)
					  is_all_same = false;
				}
				if ( is_all_same) return true;
			}

			// Star Time checking                                                   //FixIn:6.1.1.6
			var my_start_time_options = jQuery( start_time_fields + ' option,'+start_time_fields2 + ' option');

			if (   ( my_start_time_options.length > 0 )
				&& ( ( bk_days_selection_mode == 'single' ) /*|| ( bk_days_selection_mode == 'multiple' )*/ )	//FixIn: 8.5.2.4
			   ){  // Only if range selections exist and we are have single days selections
			   var removed_time_slots = is_time_slot_booked_for_this_time_array( bk_type, times_array, td_class, [ start_time_fields, start_time_fields2 ] );
			   var some_exist_time_slots = [];
			   var my_time_value = jQuery( start_time_fields + ' option,'+start_time_fields2 + ' option');

			   for ( j=0; j< my_time_value.length; j++){

				   if (  wpdev_in_array( removed_time_slots, j ) ) {
				   } else {
					   some_exist_time_slots[some_exist_time_slots.length] = j;
				   }
			   }
			   if (some_exist_time_slots.length == 0 ) return true;
			}
			// End                                                                  //FixIn:6.1.1.6    End fix

			//Check may be its not possible to select any other time slots from the selectbox, because its already booked, sothen mark this day as booked.
			if (   ( my_timerange_value.length > 0 )
				&& ( ( bk_days_selection_mode == 'single' ) || ( bk_days_selection_mode == 'multiple' ) )
			   ){  // Only if range selections exist and we are have single days selections
			   var removed_time_slots = is_time_slot_booked_for_this_time_array( bk_type, times_array, td_class, [ time_slot_field_name, time_slot_field_name2 ] );
			   var some_exist_time_slots = [];
			   var my_time_value = jQuery( time_slot_field_name + ' option,'+time_slot_field_name2 + ' option');

			   for ( j=0; j< my_time_value.length; j++){

				   if (  wpdev_in_array( removed_time_slots, j ) ) {

				   } else {
					   some_exist_time_slots[some_exist_time_slots.length] = j;
				   }
			   }
			   if (some_exist_time_slots.length == 0 ) return true;
			}

		}

		for ( var i=0; i< times_array.length; i++){  // s = 2 - end time,   s = 1 - start time
		   s = parseInt( times_array[i][2] );

		   if  (i == 0)
				if  (s !== 2)  {return false;} // Its not start at the start of day

		   if ( i > 0 ) {

				if ( s == 1 )
					if  ( !( ( times_array[i-1][0] == times_array[i][0] ) &&  ( times_array[i-1][1] == times_array[i][1] ) ) ) {
							return false; // previos time is not equal to current so we have some free interval
					}

		   }

		   if (i == ( times_array.length-1))
				   if (s !== 1)   {return false;} // Its not end  at the end of day

		}
		return true;
	}
