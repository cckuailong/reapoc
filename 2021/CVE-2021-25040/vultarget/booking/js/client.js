var is_booking_without_payment = false;
var date_approved = [];
var date2approve = [];
var date_admin_blank = [];
var dates_additional_info = [];
var is_all_days_available = [];
var avalaibility_filters = [];
var is_show_cost_in_tooltips = false;
var is_show_cost_in_date_cell = false;
var is_show_availability_in_tooltips = false;
var global_avalaibility_times = [];   
var numbb = 0;

//var is_use_visitors_number_for_availability;
var timeoutID_of_thank_you_page = null;

/**
 * Booking Calendar -  JavaScript Settings
 *
 * Example or redefine some settings:
 * <script type="text/javascript">  wpbc_settings.set_option( 'pending_days_selectable', true ); </script>
 * [booking type=1]
 *
 //FixIn: 8.6.1.18
 */
var wpbc_settings = (function ( obj, $) {

	// Define private property
	var p_options = obj.options = obj.options || [];

	p_options['pending_days_selectable'] = false;

	// Get Option
	obj.get_option = function ( item_id ) {

		return p_options[ item_id ];
	};

	// Set Option
	obj.set_option = function ( item_id, item_value ) {

		p_options[ item_id ] = item_value;
	};

	return obj;

}(wpbc_settings || {}, jQuery ));


/**
 * Calendar Init
 *
 * @param bk_type			- resource ID
 * @param date_approved_par
 * @param my_num_month
 * @param start_day_of_week
 * @param start_bk_month
 * @returns {boolean}
 */
function init_datepick_cal(bk_type,  date_approved_par, my_num_month, start_day_of_week, start_bk_month  ){

        if ( jQuery('#calendar_booking'+ bk_type).hasClass('hasDatepick') == true ) { // If the calendar with the same Booking resource is activated already, then exist.
            return false;
        }

        var cl = document.getElementById('calendar_booking'+ bk_type);if (cl === null) return; // Get calendar instance and exit if its not exist

        date_approved[ bk_type ] = date_approved_par;

        var isRangeSelect = false;
        var bkMultiDaysSelect   = 365;
        if ( bk_days_selection_mode==='dynamic' ) { isRangeSelect     = true; bkMultiDaysSelect = 0; }
        if ( bk_days_selection_mode==='single' )    bkMultiDaysSelect = 0;

        var bkMinDate = 0;
        var bkMaxDate = booking_max_monthes_in_calendar;

        var is_this_admin = false;
        if (   ( location.href.indexOf('page=wpbc-new') != -1 )
            && ( location.href.indexOf('booking_hash') != -1 )                  // Comment this line for ability to add  booking in past days at  Booking > Add booking page.
            ){
            is_this_admin = true; 
            bkMinDate = null;
            bkMaxDate = null;
        }

        function click_on_cal_td(){
            if(typeof( selectDayPro ) == 'function') {selectDayPro(  bk_type);}
        }

        function selectDay(date) {

			// Set unselectable,  if only Availability Calendar  here (and we do not insert Booking form by mistake).
			var is_calendar_booking_unselectable = jQuery( '#calendar_booking_unselectable' + bk_type );				//FixIn: 8.0.1.2
			var is_booking_form_also = jQuery( '#booking_form_div' + bk_type );
			if ( ( is_calendar_booking_unselectable.length > 0 ) && ( is_booking_form_also.length <= 0 ) ){

				wpbc_unselect_all_days( bk_type );
				jQuery('.wpbc_only_calendar .popover_calendar_hover').remove();                      					//Hide all opened popovers
				return false;
			}																											//FixIn: 8.0.1.2 end

			//console.log( jQuery.fn.popover.Constructor.VERSION );   // Check if this minimum BS version, and then proced
            if ( 'function' === typeof( jQuery( ".datepick-days-cell" ).popover ) ){    								    //FixIn: 7.0.1.2  - 2016-12-10
                jQuery( '.datepick-days-cell' ).popover( 'hide' );
            }

			jQuery( '#date_booking' + bk_type ).val( date );

			if ( typeof( bkDisableBookedTimeSlots ) == 'function' ){
                if(typeof( prepare_tooltip ) == 'function') {setTimeout("prepare_tooltip("+bk_type+");",1000);}
				// HERE WE WILL DISABLE ALL OPTIONS IN RANGE TIME INTERVALS FOR SINGLE DAYS SELECTIONS FOR THAT DAYS WHERE HOURS ALREADY BOOKED
				bkDisableBookedTimeSlots( jQuery( '#date_booking' + bk_type ).val(), bk_type );
			}
			if ( typeof( selectDayPro ) == 'function' ){
				selectDayPro( date, bk_type );
			}
			jQuery( ".booking_form_div" ).trigger( "date_selected", [ bk_type, date ] );
        }

        function hoverDay(value, date){

			if ( typeof( hoverDayTime ) == 'function' ){
				hoverDayTime( value, date, bk_type );
			}

			if ( (location.href.indexOf( 'page=wpbc' ) == -1) ||
				(location.href.indexOf( 'page=wpbc-new' ) > 0) ){ // Do not show it (range) at the main admin page
				if ( typeof( hoverDayPro ) == 'function' ){
					hoverDayPro( value, date, bk_type );
				}
			}

			var is_calendar_booking_unselectable = jQuery( '#calendar_booking_unselectable' + bk_type );				//FixIn: 8.0.1.2
			var is_booking_form_also = jQuery( '#booking_form_div' + bk_type );
			// Set unselectable,  if only Availability Calendar  here (and we do not insert Booking form by mistake).
			if ( ( is_calendar_booking_unselectable.length == 1 ) && ( is_booking_form_also.length != 1 ) ){
				jQuery( '#calendar_booking' + bk_type + ' .datepick-days-cell-over' ).removeClass( 'datepick-days-cell-over' );        // clear all highlight days selections
				jQuery( '.wpbc_only_calendar #calendar_booking' + bk_type + ' .datepick-days-cell, ' +
						'.wpbc_only_calendar #calendar_booking' + bk_type + ' .datepick-days-cell a' ).css( 'cursor', 'default' );
				return false;
			}																											//FixIn: 8.0.1.2	end
		}

        function applyCSStoDays(date ){
            var class_day = (date.getMonth()+1) + '-' + date.getDate() + '-' + date.getFullYear();
            var additional_class = ' wpbc_weekday_' + date.getDay() + '  ';

            //Block today date if current today  time older than  18:00
            if ( ( false ) && ( date.getDate() == (wpdev_bk_today[2] )  ) && ( date.getMonth() == (wpdev_bk_today[1]-1)  ) &&( date.getFullYear() == (wpdev_bk_today[0])  ) ){
				var my_test_date1 = new Date( wpdev_bk_today[ 0 ], (wpdev_bk_today[ 1 ] - 1), wpdev_bk_today[ 2 ], wpdev_bk_today[ 3 ], wpdev_bk_today[ 4 ], 0 );  //Get today
				var my_test_date2 = new Date( wpdev_bk_today[ 0 ], (wpdev_bk_today[ 1 ] - 1), wpdev_bk_today[ 2 ], 18, 0, 0 );      //Today 18:00
				if ( my_test_date2.getTime() < my_test_date1.getTime() )
					return [ false, 'cal4date-' + class_day + ' date_user_unavailable' ];
			}

            if(typeof( prices_per_day  ) !== 'undefined')
                if(typeof(  prices_per_day[bk_type] ) !== 'undefined')
                    if(typeof(  prices_per_day[bk_type][class_day] ) !== 'undefined') {
                        additional_class += ' rate_'+prices_per_day[bk_type][class_day];
                    }

            // define season filter names as classes
            if(typeof( wpdev_bk_season_filter  ) !== 'undefined')
                    if(typeof(  wpdev_bk_season_filter[class_day] ) !== 'undefined') {
                        additional_class += ' '+wpdev_bk_season_filter[class_day].join(' ');
                    }

            if (is_this_admin == false) {
                var my_test_date = new Date( wpdev_bk_today[0],(wpdev_bk_today[1]-1), wpdev_bk_today[2] ,0,0,0 );  //Get today
                if ( (days_between( date, my_test_date)) < block_some_dates_from_today ) 
                    return [false, 'cal4date-' + class_day +' date_user_unavailable']; 
                
                if( typeof( wpbc_available_days_num_from_today  ) !== 'undefined')
                    if ( parseInt( '0' + wpbc_available_days_num_from_today ) > 0 )
                        if ( (days_between( date, my_test_date)) > parseInt( '0' + wpbc_available_days_num_from_today ) ) 
                            return [false, 'cal4date-' + class_day +' date_user_unavailable']; 
            }

            if (typeof( is_this_day_available ) == 'function') {
                var is_day_available = is_this_day_available( date, bk_type);
                var season_filter = '';                                         //FixIn: 6.0.1.8
                if ( is_day_available instanceof Array ) {
                    season_filter = ' season_filter_id_' + is_day_available[1];
                    is_day_available = is_day_available[0];                    
                }
                if (! is_day_available) {return [false, 'cal4date-' + class_day +' date_user_unavailable ' + season_filter ];}                
            }

            // Time availability
            if (typeof( check_global_time_availability ) == 'function') {check_global_time_availability( date, bk_type );}

            var blank_admin_class_day = '';
            if(typeof(date_admin_blank[ bk_type ]) !== 'undefined')
                if(typeof(date_admin_blank[ bk_type ][ class_day ]) !== 'undefined') {
                    blank_admin_class_day = ' date_admin_blank ';
                }

            // Check availability per day for BL
            var reserved_days_count = 1;
            if(typeof(availability_per_day) !== 'undefined')
            if(typeof(availability_per_day[ bk_type ]) !== 'undefined')
               if(typeof(availability_per_day[ bk_type ][ class_day ]) !== 'undefined') {
                  reserved_days_count = parseInt( availability_per_day[ bk_type ][ class_day ] );}

            // Number of Check In Dates for BL      
            var checkin_days_count = [0 ,0];
            if(typeof(wpbc_check_in_dates) !== 'undefined')
            if(typeof(wpbc_check_in_dates[ bk_type ]) !== 'undefined')
               if(typeof(wpbc_check_in_dates[ bk_type ][ class_day ]) !== 'undefined') {
                   // [ Number of check in bookings, Pending or Approved status ]
                  checkin_days_count = [ wpbc_check_in_dates[ bk_type ][ class_day ][ 0 ] ,  wpbc_check_in_dates[ bk_type ][ class_day ][ 1 ] ];
              }

            // Number of Check Out Dates for BL 
            var checkout_days_count = [0 ,0];
            if(typeof(wpbc_check_out_dates) !== 'undefined')
            if(typeof(wpbc_check_out_dates[ bk_type ]) !== 'undefined')
               if(typeof(wpbc_check_out_dates[ bk_type ][ class_day ]) !== 'undefined') {
                   // [ Number of check Out bookings, Pending or Approved status ]
                  checkout_days_count = [ wpbc_check_out_dates[ bk_type ][ class_day ][ 0 ] , wpbc_check_out_dates[ bk_type ][ class_day ][ 1 ] ];
              }

            // Booked both  check  in/out dates in the same child resources  
            var both_check_in_out_num = 0;
            if ( typeof( getNumberClosedCheckInOutDays ) == 'function' ) {                       
                  both_check_in_out_num =  getNumberClosedCheckInOutDays( bk_type, class_day );
            }


            // we have 0 available at this day - Only for resources, which have childs
            if (  wpdev_in_array( parent_booking_resources, bk_type ) )
                    if (reserved_days_count <= 0) {
                            if(typeof(date2approve[ bk_type ]) !== 'undefined')
                               if(typeof(date2approve[ bk_type ][ class_day ]) !== 'undefined')
                                 return [false, 'cal4date-' + class_day +' date2approve date_unavailable_for_all_childs ' + blank_admin_class_day];
                             return [false, 'cal4date-' + class_day +' date_approved date_unavailable_for_all_childs ' + blank_admin_class_day];
                    }

            var th=0;
            var tm=0;
            var ts=0;
            var time_return_value = false;

            var is_set_pending_days_selectable = wpbc_settings.get_option( 'pending_days_selectable' );                 // set pending days selectable          //FixIn: 8.6.1.18

            // Select dates which need to approve, its exist only in Admin
            if(typeof(date2approve[ bk_type ]) !== 'undefined')
               if(typeof(date2approve[ bk_type ][ class_day ]) !== 'undefined') {

                  for (var ia=0;ia<date2approve[ bk_type ][ class_day ].length;ia++) {

                      th = date2approve[ bk_type ][ class_day ][ia][3];
                      tm = date2approve[ bk_type ][ class_day ][ia][4];
                      ts = date2approve[ bk_type ][ class_day ][ia][5];
                      if ( ( th == 0 ) && ( tm == 0 ) && ( ts == 0 ) ) {
                            return [ is_set_pending_days_selectable, 'cal4date-' + class_day +' date2approve' + blank_admin_class_day];             // Orange       //FixIn: 8.6.1.18
                      } else {
                          if ( is_booking_used_check_in_out_time === true ) {
                              if (ts == '1')  additional_class += ' check_in_time' + ' check_in_time_date2approve';         //FixIn: 6.0.1.2
                              if (ts == '2')  additional_class += ' check_out_time'+ ' check_out_time_date2approve';        //FixIn: 6.0.1.2
                          } else {
                              additional_class += ' times_clock';                                                           //FixIn: 8.2.1.27
                          }
                          time_return_value = [true, 'date_available cal4date-' + class_day +' date2approve timespartly' + additional_class];       // Times
                          if(typeof( isDayFullByTime ) == 'function') {
                              if ( isDayFullByTime(bk_type, class_day ) ) {
                                    return [ is_set_pending_days_selectable, 'cal4date-' + class_day +' date2approve' + blank_admin_class_day];     // Orange       //FixIn: 8.6.1.18
                              }
                          }
                      }

                  }

               }

            //select Approved dates
            if(typeof(date_approved[ bk_type ]) !== 'undefined')
              if(typeof(date_approved[ bk_type ][ class_day ]) !== 'undefined') {

                  for (var ia=0;ia<date_approved[ bk_type ][ class_day ].length;ia++) {

                      th = date_approved[ bk_type ][ class_day ][ia][3];
                      tm = date_approved[ bk_type ][ class_day ][ia][4];
                      ts = date_approved[ bk_type ][ class_day ][ia][5];
                      if ( ( th == 0 ) && ( tm == 0 ) && ( ts == 0 ) )
                        return [false, 'cal4date-' + class_day +' date_approved' + blank_admin_class_day]; //Blue or Grey in client
                      else {
                          if ( is_booking_used_check_in_out_time === true ) {
                              if (ts == '1')  additional_class += ' check_in_time' + ' check_in_time_date_approved';        //FixIn: 6.0.1.2
                              if (ts == '2')  additional_class += ' check_out_time'+ ' check_out_time_date_approved';       //FixIn: 6.0.1.2
                          } else {
                              additional_class += ' times_clock';                                                           //FixIn: 8.2.1.27
                          }
                        time_return_value = [true,  'date_available cal4date-' + class_day +' date_approved timespartly' + additional_class]; // Times
                        if(typeof( isDayFullByTime ) == 'function') {
                            if ( isDayFullByTime(bk_type, class_day ) ) return [false, 'cal4date-' + class_day +' date_approved' + blank_admin_class_day]; // Blue or Grey in client
                        }
                      }

                  }
              }


            for (var i=0; i<user_unavilable_days.length;i++) {
                if (date.getDay()==user_unavilable_days[i])   return [false, 'cal4date-' + class_day +' date_user_unavailable' ];
            }

            var is_datepick_unselectable = '';
			//FixIn: 8.0.1.2
            /*
            var is_calendar_booking_unselectable = jQuery('#calendar_booking_unselectable' + bk_type);            
            var is_booking_form_also             = jQuery('#booking_form_div' + bk_type);            
            // Set unselectable,  if only Availability Calendar  here (and we do not insert Booking form by mistake).
            if (  ( is_calendar_booking_unselectable.length == 1 ) && ( is_booking_form_also.length != 1 )  ){        
                is_datepick_unselectable = 'datepick-unselectable';  // 'ui-datepicker-unselectable ui-state-disabled';
            }*/


            //FixIn: 8.1.2.3
            if ( false ){
				if ( (time_return_value !== false) && (time_return_value[ 1 ].indexOf( 'timespartly' ) != -1) && (is_booking_used_check_in_out_time !== true) ){
					time_return_value[ 1 ] = time_return_value[ 1 ].replace( "date_approved", "" );
					time_return_value[ 1 ] = time_return_value[ 1 ].replace( "date2approve", "" );
				}
			}
            var is_exist_check_in_out_for_parent_resource = Math.max( checkin_days_count[0], checkout_days_count[0] );
            //FixIn: 8.4.7.29
            if (  ! wpdev_in_array( parent_booking_resources, bk_type ) ) {
                is_exist_check_in_out_for_parent_resource = 0;
            }

            if ( ( time_return_value !== false ) && ( is_exist_check_in_out_for_parent_resource == 0 ) ) { // Check  this only for single booking resources - is_exist_check_in_out_for_parent_resource == 0
                if ( is_booking_used_check_in_out_time === true ) {
                    // If the date is cehck in/out and the check in/out time is activated so  then  this date is unavailbale
                    if ( ( additional_class.indexOf('check_in_time') != -1 ) && ( additional_class.indexOf('check_out_time') != -1 ) ){ 
                        // Make this date unavailbale
                        time_return_value[0] = false;                             
                        //FixIn: 6.0.1.2                                                 
                        if ( ! (
                                    ( ( additional_class.indexOf('check_in_time_date_approved') != -1 ) && ( additional_class.indexOf('check_out_time_date2approve') != -1 ) )
                                  || ( ( additional_class.indexOf('check_out_time_date_approved') != -1 ) && ( additional_class.indexOf('check_in_time_date2approve') != -1 ) )
                            ) ) { 
                            // Remove CSS classes from this date
                            time_return_value[1]=time_return_value[1].replace("check_in_time",""); 
                            time_return_value[1]=time_return_value[1].replace("check_out_time",""); 
                            time_return_value[1]=time_return_value[1].replace("timespartly","");        
                        }
                        time_return_value[1]=time_return_value[1].replace("date_available",""); 
                    }
                }
                if (  (  wpdev_in_array( parent_booking_resources, bk_type ) ) && ( (reserved_days_count - both_check_in_out_num ) <= 0 ) ) {    //FixIn: 6.0.1.2
                    time_return_value[0] = false;  
                    time_return_value[1]=time_return_value[1].replace("check_in_time",""); 
                    time_return_value[1]=time_return_value[1].replace("check_out_time",""); 
                    time_return_value[1]=time_return_value[1].replace("timespartly","");                                
                    time_return_value[1]=time_return_value[1].replace("date_available","");                     
                }
                return time_return_value;

            } else { 
                
                if ( 
                          ( is_booking_used_check_in_out_time === true ) 
                       && (  ( is_exist_check_in_out_for_parent_resource > 0 ) || ( (reserved_days_count - both_check_in_out_num ) <= 0 )  )
                    ) { // Check  Check  In / Out dates for the parent resources. // reserved_days_count - number of available items,  including check in/out dates ||  both_check_in_out_num number of items with both  check in/out   //FixIn: 6.0.1.12

                    // Unavailable 
                    if ( (reserved_days_count - both_check_in_out_num ) <= 0 ) {
                        // Check  Pending or Approved by the Check In date
                        if ( checkin_days_count[1] == 1 )   additional_class = ' date_approved';    
                        else                                additional_class = ' date2approve';                                                       
                        return [false, 'cal4date-' + class_day + additional_class + blank_admin_class_day]; 
                    }

                    // Recheck  if this date check in/out
                    if ( (reserved_days_count - both_check_in_out_num - checkin_days_count[0]) <= 0 ) {
                        if ( checkin_days_count[1] == 1 )   additional_class += ' date_approved';
                        else                                additional_class += ' date2approve';                           
                        additional_class += ' timespartly check_in_time';
                    }
                    if ( (reserved_days_count - both_check_in_out_num  - checkout_days_count[0]) <= 0 ) {
                        if ( checkout_days_count[1] == 1 )  additional_class += ' date_approved';
                        else                                additional_class += ' date2approve';
                        additional_class += ' timespartly check_out_time';
                    }                                               
                }

                return [true, 'date_available cal4date-' + class_day +' reserved_days_count' + reserved_days_count + ' '  + is_datepick_unselectable + additional_class+ ' '];
            }
        }

        function changeMonthYear(year, month){ 

            if(typeof( prepare_tooltip ) == 'function') {
                setTimeout("prepare_tooltip("+bk_type+");",1000);
            }
            if(typeof( prepare_highlight ) == 'function') {
             setTimeout("prepare_highlight();",1000);
            }
        }
        // Configure and show calendar
        jQuery('#calendar_booking'+ bk_type).text('');
        jQuery('#calendar_booking'+ bk_type).datepick(
                {beforeShowDay: applyCSStoDays,
                    onSelect: selectDay,
                    onHover:hoverDay,
                    onChangeMonthYear:changeMonthYear,
                    showOn: 'both',
                    multiSelect: bkMultiDaysSelect,
                    numberOfMonths: my_num_month,
                    stepMonths: 1,
                    prevText: '&laquo;',
                    nextText: '&raquo;',
                    dateFormat: 'dd.mm.yy',
                    changeMonth: false, 
                    changeYear: false,
                    minDate: bkMinDate, maxDate: bkMaxDate, //'1Y',
                    // minDate: new Date(2020, 2, 1), maxDate: new Date(2020, 9, 31),             // Ability to set any  start and end date in calendar
                    showStatus: false,
                    multiSeparator: ', ',
                    closeAtTop: false,
                    firstDay:start_day_of_week,
                    gotoCurrent: false,
                    hideIfNoPrevNext:true,
                    rangeSelect:isRangeSelect,
                    // showWeeks: true, 
                    useThemeRoller :false // ui-cupertino.datepick.css
                }
        );
        
        //FixIn: 7.1.2.8
        setTimeout( function ( ) {
            jQuery( '.datepick-days-cell.datepick-today.datepick-days-cell-over' ).removeClass( 'datepick-days-cell-over' );
        }, 500 );
        
        if ( start_bk_month != false ) {
            var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking'+bk_type));
            inst.cursorDate = new Date();
            inst.cursorDate.setFullYear( start_bk_month[0], (start_bk_month[1]-1) ,  1 );
            // In some cases,  the setFullYear can  set  only Year,  and not the Month and day      //FixIn:6.2.3.5
            inst.cursorDate.setMonth( parseInt( start_bk_month[1] - 1 ) );
            inst.cursorDate.setDate( 1 );

            inst.drawMonth = inst.cursorDate.getMonth();
            inst.drawYear = inst.cursorDate.getFullYear();

            jQuery.datepick._notifyChange(inst);
            jQuery.datepick._adjustInstDate(inst);
            jQuery.datepick._showDate(inst);
            jQuery.datepick._updateDatepick(inst);
        }

	if ( typeof( prepare_tooltip ) == 'function' ){
		setTimeout( "prepare_tooltip(" + bk_type + ");", 1000 );
	}
}


////////////////////////////////////////////////////////////////////////////
// Days Selections - support functions 
////////////////////////////////////////////////////////////////////////////

// Get fisrst day of selection
function get_first_day_of_selection(dates) {

    // Multiple days selections
    if ( dates.indexOf(',') != -1 ){                  
        var dates_array =dates.split(/,\s*/);
        var length = dates_array.length;
        var element = null;
        var new_dates_array = [];

        for (var i = 0; i < length; i++) {

          element = dates_array[i].split(/\./);

          new_dates_array[new_dates_array.length] = element[2]+'.' + element[1]+'.' + element[0];       //2013.12.20
        }        
        new_dates_array.sort();

        element = new_dates_array[0].split(/\./);

        return element[2]+'.' + element[1]+'.' + element[0];                    //20.12.2013
    }

    // Range days selection
    if ( dates.indexOf(' - ') != -1 ){                  
        var start_end_date = dates.split(" - ");
        return start_end_date[0];
    }

    // Single day selection 
    return dates;                                                               //20.12.2013
}


// Get fisrst day of selection
function get_last_day_of_selection(dates) {

    // Multiple days selections
    if ( dates.indexOf(',') != -1 ){                  
        var dates_array =dates.split(/,\s*/);
        var length = dates_array.length;
        var element = null;
        var new_dates_array = [];

        for (var i = 0; i < length; i++) {

          element = dates_array[i].split(/\./);

          new_dates_array[new_dates_array.length] = element[2]+'.' + element[1]+'.' + element[0];       //2013.12.20
        }        
        new_dates_array.sort();

        element = new_dates_array[(new_dates_array.length-1)].split(/\./);

        return element[2]+'.' + element[1]+'.' + element[0];                    //20.12.2013
    }

    // Range days selection
    if ( dates.indexOf(' - ') != -1 ){                  
        var start_end_date = dates.split(" - ");
        return start_end_date[(start_end_date.length-1)];
    }

    // Single day selection 
    return dates;                                                               //20.12.2013
}


// Set selected days at calendar as UnAvailable
function setUnavailableSelectedDays( bk_type ){
    var sel_dates = jQuery('#calendar_booking'+bk_type).datepick('getDate');
    var class_day2;
    for( var i =0; i <sel_dates.length; i++) {
      class_day2 = (sel_dates[i].getMonth()+1) + '-' + sel_dates[i].getDate() + '-' + sel_dates[i].getFullYear();
      date_approved[ bk_type ][ class_day2 ] = [ (sel_dates[i].getMonth()+1) ,  sel_dates[i].getDate(),  sel_dates[i].getFullYear(),0,0,0];
      jQuery('#calendar_booking'+bk_type+' td.cal4date-'+class_day2).html(sel_dates[i].getDate());
      // jQuery('#calendar_booking'+bk_type).datepick('refresh');
    }
}


// After reservation action is done
function setReservedSelectedDates( bk_type ){

    var is_pay_now = false;

    if (document.getElementById('calendar_booking'+bk_type) === null )  {
        
        jQuery( '#submiting' + bk_type ).html( '' );
        jQuery( '#booking_form_div' + bk_type ).hide();
        makeScroll( '#ajax_respond_insert'+bk_type );

        if ( ( document.getElementById('gateway_payment_forms'+bk_type) != null ) && 
             ( document.getElementById('gateway_payment_forms'+bk_type).innerHTML != '' ) )
                is_pay_now = true;

        if ( (! is_pay_now) || ( is_booking_without_payment == true ) )             
            if (type_of_thank_you_message == 'page') {      // Page
                timeoutID_of_thank_you_page = setTimeout(function ( ) {location.href= thank_you_page_URL;} ,1000);
            } else {                                        // Message
                document.getElementById('submiting'+bk_type).innerHTML = '<div class=\"submiting_content wpdev-help-message alert alert-warning alert-success\" >'+new_booking_title+'</div>';
                jQuery('.submiting_content').fadeOut( new_booking_title_time );
                setTimeout( function () { location.reload( true ); }, parseInt( 1000 + new_booking_title_time ) );  //FixIn: 8.1.2.14
            }

    } else {

        setUnavailableSelectedDays( bk_type );           
        document.getElementById('date_booking' + bk_type).value = '';           
        jQuery('#calendar_booking' + bk_type + ', .block_hints').hide();

        if ( location.href.indexOf('admin.php') == -1 ) {                       // Front End
            
            // Get calendar from the html and insert it before form div, which will hide after btn click
            jQuery('#calendar_booking' + bk_type).insertBefore("#booking_form_div" + bk_type);
            document.getElementById("booking_form_div" + bk_type).style.display = "none";

            jQuery( '#hided_booking_form' + bk_type ).prevAll( 'select[name="active_booking_form"]' ).hide();       //FixIn: 7.1.2.13
            jQuery( '#hided_booking_form' + bk_type ).prevAll( 'label[for="calendar_type"]' ).hide();

            makeScroll('#ajax_respond_insert' + bk_type);

            if ((document.getElementById('gateway_payment_forms' + bk_type) != null) &&
                    (document.getElementById('gateway_payment_forms' + bk_type).innerHTML != ''))
                is_pay_now = true;

            if ((!is_pay_now) || (is_booking_without_payment == true)) {
                if (type_of_thank_you_message == 'page') {      // Page
                    timeoutID_of_thank_you_page = setTimeout(function ( ) {
                        location.href = thank_you_page_URL;
                    }, 1000);
                } else {                                        // Message
                    document.getElementById('submiting' + bk_type).innerHTML = '<div class=\"submiting_content wpdev-help-message alert alert-warning alert-success\" >' + new_booking_title + '</div>';

                    //FixIn: 8.5.2.26
                    if  ( ! jQuery('#submiting' + bk_type ).is('visible') ) {
                        jQuery('#submiting' + bk_type ).closest( 'form.booking_form').before( jQuery('#submiting' + bk_type ) );
                        jQuery('#submiting' + bk_type ).show();
                        jQuery(".wpbc_submit_spinner").hide();
                    }

                    makeScroll( '#submiting' + bk_type );
                    jQuery('.submiting_content').fadeOut( new_booking_title_time );
                    setTimeout( function () { location.reload( true ); }, parseInt( 1000 + new_booking_title_time ) );  //FixIn: 8.1.2.14
                }
            }

        } else {                                                                // Back End
            setTimeout( function () { 
                location.reload( true );
            }, 1000);
        }
    }
}


/**
 * Check ID of selected additional calendars
 *
 * @param int bk_type
 * @returns array
 */
function wpbc_get_arr_of_selected_additional_calendars( bk_type ){                                                      //FixIn: 8.5.2.26

    var selected_additionl_calendars = [];

    // Checking according additional calendars
    if ( document.getElementById( 'additional_calendars' + bk_type ) != null ){

        var id_additional_str = document.getElementById( 'additional_calendars' + bk_type ).value;
        var id_additional_arr = id_additional_str.split( ',' );

        var is_all_additional_days_unselected = true;

        for ( var ia = 0; ia < id_additional_arr.length; ia++ ){
            if ( document.getElementById( 'date_booking' + id_additional_arr[ ia ] ).value != '' ){
                selected_additionl_calendars.push( id_additional_arr[ ia ] );
            }
        }
    }
    return selected_additionl_calendars;
}

////////////////////////////////////////////////////////////////////////////
// Submit Booking Data 
////////////////////////////////////////////////////////////////////////////

// Check fields at form and then send request
function mybooking_submit( submit_form , bk_type, wpdev_active_locale){
//console.log( wpbc_get_arr_of_selected_additional_calendars( bk_type ) );


    var target_elm = jQuery( ".booking_form_div" ).trigger( "booking_form_submit_click", [bk_type, submit_form, wpdev_active_locale] );     //FixIn: 8.8.3.13
    if  (
            ( jQuery( target_elm ).find( 'input[name="booking_form_show_summary"]' ).length > 0 )
         && ( 'pause_submit' === jQuery( target_elm ).find( 'input[name="booking_form_show_summary"]' ).val() )
        )
    {
        return false;
    }

    //FixIn: 8.4.0.2
    var is_error = wpbc_check_errors_in_booking_form( bk_type );
    if ( is_error ) { return false; }

    // Show message if no selected days in Calendar(s)
    if (document.getElementById('date_booking' + bk_type).value == '')  {

        var arr_of_selected_additional_calendars = wpbc_get_arr_of_selected_additional_calendars( bk_type );            //FixIn: 8.5.2.26

        if ( arr_of_selected_additional_calendars.length == 0 ) {
            showMessageUnderElement( '#date_booking' + bk_type, message_verif_selectdts, '');
            makeScroll('#calendar_booking' + bk_type);            // Scroll to the calendar
            return;
        }
    }

    var count = submit_form.elements.length;
    var formdata = '';
    var inp_value;
    var element;
    var el_type;


    // Serialize form here
    for (i=0; i<count; i++)   {
        element = submit_form.elements[i];
        
        if ( jQuery( element ).closest( '.booking_form_garbage' ).length ) {
            continue;       // Skip elements from garbage                                           //FixIn: 7.1.2.14
        }

        if ( 
               ( element.type !== 'button' ) 
            && ( element.type !== 'hidden' ) 
            && ( element.name !== ( 'date_booking' + bk_type ) )   
            // && ( jQuery( element ).is( ':visible' ) )                                            //FixIn: 7.2.1.12.2 // Its prevent of saving hints,  and some other hidden element
        ) {           // Skip buttons and hidden element - type                                     //FixIn: 7.2.1.12


            // Get Element Value
            if ( element.type == 'checkbox' ){

                if (element.value == '') {
                    inp_value = element.checked;
                } else {
                    if (element.checked) inp_value = element.value;
                    else inp_value = '';
                }

            } else if ( element.type == 'radio' ) {

                if (element.checked) {
                    inp_value = element.value; 
                } else {
                        // Cehck  if this radio required,  and if its do not checked,  then show warning, otherwise if it not required or some other option checked skip this loop
                        // We need to  check  it here, because radio have the several  otions with  same name and type and otherwsie we will save several options with  selcted and empty values.
                    if (                                                        //FixIn: 7.0.1.62
                           ( element.className.indexOf('wpdev-validates-as-required') !== -1 )
                        && ( jQuery( element ).is( ':visible' ) )                                            //FixIn: 7.2.1.12.2 // Its prevent of saving hints,  and some other hidden element
                        && ( ! jQuery(':radio[name="'+element.name+'"]', submit_form).is(":checked") ) ) {
                        showErrorMessage( element , message_verif_requred_for_radio_box, false );   		//FixIn: 8.5.1.3
                        return;   
                    }
                    continue;
                }
            } else {
                inp_value = element.value;
            }                      

            // Get value in selectbox of multiple selection
            if (element.type =='select-multiple') {
                inp_value = jQuery('[name="'+element.name+'"]').val() ;
                if (( inp_value == null ) || (inp_value.toString() == '' ))
                    inp_value='';
            }

            // Make validation  only  for visible elements
            if ( jQuery( element ).is( ':visible' ) ) {                                             //FixIn: 7.2.1.12.2
                // Recheck for max num. available visitors selection
                if ( element.name == ('visitors'+bk_type) ){
					if ( typeof(is_max_visitors_selection_more_than_available) == 'function' ){

						//FixIn: 8.2.1.28
					    if (    ( wpbc_is_time_field_in_booking_form( bk_type, submit_form.elements ) )
					         && ( ! wpdev_in_array( parent_booking_resources, bk_type ) )
                           ) {

					        // We are having timeslots and this is single booking resource,  so  skip checking for max number of visitors

                        }  else {

							if ( is_max_visitors_selection_more_than_available( bk_type, inp_value, element ) ){
								return;
							}
						}
					}
				}
                // Phone validation
                /*if ( element.name == ('phone'+bk_type) ) {
                    // we validate a phone number of 10 digits with no comma, no spaces, no punctuation and there will be no + sign in front the number - See more at: http://www.w3resource.com/javascript/form/phone-no-validation.php#sthash.U9FHwcdW.dpuf
                    var reg =  /^\d{10}$/;
                    var message_verif_phone = "Please enter correctly phone number";
                    if ( inp_value != '' )
                        if(reg.test(inp_value) == false) {showErrorMessage( element , message_verif_phone, false );return;}
                }*/

                // Validation Check --- Requred fields
                if ( element.className.indexOf('wpdev-validates-as-required') !== -1 ){             
                    if  ((element.type =='checkbox') && ( element.checked === false)) {
                        if ( ! jQuery(':checkbox[name="'+element.name+'"]', submit_form).is(":checked") ) {
                            showErrorMessage( element , message_verif_requred_for_check_box, false );   		//FixIn: 8.5.1.3
                            return;                            
                        }
                    }
                    if  (element.type =='radio') {
                        if ( ! jQuery(':radio[name="'+element.name+'"]', submit_form).is(":checked") ) {
                            showErrorMessage( element , message_verif_requred_for_radio_box, false );   		//FixIn: 8.5.1.3
                            return;                            
                        }
                    }

                    if (  (element.type != 'checkbox') && (element.type != 'radio') && ( '' === wpbc_trim( inp_value ) )  ){       //FixIn: 8.8.1.3   //FixIn:7.0.1.39       //FixIn: 8.7.11.12
                        showErrorMessage( element , message_verif_requred, false );   		//FixIn: 8.5.1.3
                        return;
                    }
                }

                // Validation Check --- Email correct filling field
                if ( element.className.indexOf('wpdev-validates-as-email') !== -1 ){   
                    inp_value = inp_value.replace(/^\s+|\s+$/gm,'');                // Trim  white space //FixIn: 5.4.5
                    var reg = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;
                    if ( inp_value != '' )
                        if(reg.test(inp_value) == false) {
                            showErrorMessage( element , message_verif_emeil, false );   		//FixIn: 8.5.1.3
                            return;
                        }
                }

                // Validation Check --- Same Email Field
                if ( ( element.className.indexOf('wpdev-validates-as-email') !== -1 ) && ( element.className.indexOf('same_as_') !== -1 ) ) { 

                    // Get  the name of Primary Email field from the "same_as_NAME" class                    
                    var primary_email_name = element.className.match(/same_as_([^\s])+/gi); 
                    if (primary_email_name != null) { // We found
                        primary_email_name = primary_email_name[0].substr(8);

                        // Recehck if such primary email field exist in the booking form
                        if (jQuery('[name="' + primary_email_name + bk_type + '"]').length > 0) {

                            // Recheck the values of the both emails, if they do  not equla show warning                    
                            if ( jQuery('[name="' + primary_email_name + bk_type + '"]').val() !== inp_value ) {
                                showErrorMessage( element , message_verif_same_emeil , false );   		//FixIn: 8.5.1.3
                                return;
                            }
                        }
                    }
                    // Skip one loop for the email veryfication field
                    continue;                                                                                           //FixIn: 8.1.2.15
                }

            }
            
            // Get Form Data
            if ( element.name !== ('captcha_input' + bk_type) ) {
                if (formdata !=='') formdata +=  '~';                                                // next field element

                el_type = element.type;
                if ( element.className.indexOf('wpdev-validates-as-email') !== -1 )  el_type='email';
                if ( element.className.indexOf('wpdev-validates-as-coupon') !== -1 ) el_type='coupon';

                inp_value = inp_value + '';
                inp_value = inp_value.replace(new RegExp("\\^",'g'), '&#94;'); // replace registered characters
                inp_value = inp_value.replace(new RegExp("~",'g'), '&#126;'); // replace registered characters

                inp_value = inp_value.replace(/"/g, '&#34;'); // replace double quot
                inp_value = inp_value.replace(/'/g, '&#39;'); // replace single quot

                formdata += el_type + '^' + element.name + '^' + inp_value ;                    // element attr
            }
        }

    }  // End Fields Loop

    //FixIn:6.1.1.3
    if( typeof( is_this_time_selections_not_available ) == 'function' ) {
        
        if ( document.getElementById('date_booking' + bk_type).value == '' )  {         // Primary calendar not selected.    

            if ( document.getElementById('additional_calendars' + bk_type ) != null ) { // Checking additional calendars.

                var id_additional_str = document.getElementById('additional_calendars' + bk_type).value; //Loop have to be here based on , sign
                var id_additional_arr = id_additional_str.split(',');
                var is_times_dates_ok = false;
                for ( var ia=0;ia<id_additional_arr.length;ia++ ) {
                    if (
                            ( document.getElementById('date_booking' + id_additional_arr[ia] ).value != '' ) 
                         && ( ! is_this_time_selections_not_available( id_additional_arr[ia], submit_form.elements ) )
                       ){
                        is_times_dates_ok = true;
                    }
                }
                if ( ! is_times_dates_ok ) return;
            }
        } else {                                                                        //Primary calendar selected.
            if ( is_this_time_selections_not_available( bk_type, submit_form.elements ) )
                return;            
        }
    }

    if ( bk_days_selection_mode == 'dynamic' ) {                                // Check if visitor finish  dates selection.
                                                                                //FixIn:6.1.1.5
        var selected_dates_cal_id = [];                                         // Get ID of calendars,  where selected dates.  
        if ( document.getElementById('date_booking' + bk_type).value != '' ) {  
            selected_dates_cal_id[selected_dates_cal_id.length] = bk_type;   
        }
        if ( document.getElementById('additional_calendars' + bk_type) != null ) {  // Checking according additional calendars.
            var id_additional_str = document.getElementById('additional_calendars' + bk_type).value; //Loop have to be here based on , sign
            var id_additional_arr = id_additional_str.split(',');
            var is_all_additional_days_unselected = true;
            for (var ia=0;ia<id_additional_arr.length;ia++) {
                if (document.getElementById('date_booking' + id_additional_arr[ia] ).value != '' ) {
                    selected_dates_cal_id[selected_dates_cal_id.length] = id_additional_arr[ia];
                }
            }
        }     
        for( var ci = 0; ci < selected_dates_cal_id.length; ci++) {
            var abk_type = selected_dates_cal_id[selected_dates_cal_id.length]
            if (document.getElementById('calendar_booking'+abk_type) != null ) {
                var inst = jQuery.datepick._getInst(document.getElementById('calendar_booking'+abk_type));
                if (bk_2clicks_mode_days_min != undefined) {
                    if(typeof( check_conditions_for_range_days_selection_for_check_in ) == 'function') { 
                        var first_date  = get_first_day_of_selection(document.getElementById('date_booking' + abk_type).value);                  
                        var date_sections = first_date.split("."); 
                        var selceted_first_day = new Date;       
                        selceted_first_day.setFullYear( parseInt(date_sections[2]-0) ,parseInt(date_sections[1]-1), parseInt(date_sections[0]-0) );
                        check_conditions_for_range_days_selection_for_check_in(selceted_first_day, abk_type); 
                    } 
                    if (inst.dates.length < bk_2clicks_mode_days_min ) {
                        showMessageUnderElement( '#date_booking' + abk_type, message_verif_selectdts, '');                             
                        makeScroll('#calendar_booking' + abk_type);            // Scroll to the calendar    
                        return;
                    }
                }
            }
        }
    }

    // Cpatch  verify
    var captcha = document.getElementById('wpdev_captcha_challenge_' + bk_type);

    //Disable Submit button
    jQuery('#booking_form_div' + bk_type + ' input[type="button"]').prop("disabled", true);
    jQuery('#booking_form_div' + bk_type + ' button').prop("disabled", true);   //FixIn: 8.5.2.7
    if (captcha != null)  form_submit_send( bk_type, formdata, captcha.value, document.getElementById('captcha_input' + bk_type).value ,wpdev_active_locale);
    else                  form_submit_send( bk_type, formdata, '',            '' ,                                                      wpdev_active_locale);
    return;
}


// Gathering params for sending Ajax request and then send it
function form_submit_send( bk_type, formdata, captcha_chalange, user_captcha ,wpdev_active_locale){

    document.getElementById('submiting' + bk_type).innerHTML = '<div style="height:20px;width:100%;text-align:center;margin:15px auto;"><img style="vertical-align:middle;box-shadow:none;width:14px;" src="'+wpdev_bk_plugin_url+'/assets/img/ajax-loader.gif"><//div>';

    var my_booking_form = '';
    var my_booking_hash = '';
    if (document.getElementById('booking_form_type' + bk_type) != undefined)
        my_booking_form =document.getElementById('booking_form_type' + bk_type).value;

    if ( wpdev_bk_edit_id_hash != '' ) my_booking_hash = wpdev_bk_edit_id_hash;

    var is_send_emeils = 1;
    if ( jQuery('#is_send_email_for_pending').length ) {
        is_send_emeils = jQuery( '#is_send_email_for_pending' ).is( ':checked' );       //FixIn: 8.7.9.5
        if ( false === is_send_emeils ) { is_send_emeils = 0; }
        else                            { is_send_emeils = 1; }
    }

    if ( document.getElementById('date_booking' + bk_type).value != '' )        //FixIn:6.1.1.3
        send_ajax_submit(bk_type,formdata,captcha_chalange,user_captcha,is_send_emeils,my_booking_hash,my_booking_form,wpdev_active_locale   ); // Ajax sending request
    else {
        jQuery('#booking_form_div' + bk_type ).hide();
        jQuery('#submiting' + bk_type ).hide();        
    }
    
    var formdata_additional_arr;
    var formdata_additional;
    var my_form_field;
    var id_additional;
    var id_additional_str;
    var id_additional_arr;
    if (document.getElementById('additional_calendars' + bk_type) != null ) {

        id_additional_str = document.getElementById('additional_calendars' + bk_type).value; //Loop have to be here based on , sign
        id_additional_arr = id_additional_str.split(',');

        //FixIn: 8.5.2.26
        if ( ! jQuery( '#booking_form_div' + bk_type ).is( ':visible' ) ) {
            jQuery( '#booking_form_div' + bk_type ).after(
                '<div class="wpbc_submit_spinner" style="height:20px;width:100%;text-align:center;margin:15px auto;"><img style="vertical-align:middle;box-shadow:none;width:14px;" src="'+wpdev_bk_plugin_url+'/assets/img/ajax-loader.gif"></div>'
            );
        }



        for (var ia=0;ia<id_additional_arr.length;ia++) {
            formdata_additional_arr = formdata;
            formdata_additional = '';
            id_additional = id_additional_arr[ia];


            formdata_additional_arr = formdata_additional_arr.split('~');
            for (var j=0;j<formdata_additional_arr.length;j++) {
                my_form_field = formdata_additional_arr[j].split('^');
                if (formdata_additional !=='') formdata_additional +=  '~';

                if (my_form_field[1].substr( (my_form_field[1].length -2),2)=='[]')
                  my_form_field[1] = my_form_field[1].substr(0, (my_form_field[1].length - (''+bk_type).length ) - 2 ) + id_additional + '[]';
                else
                  my_form_field[1] = my_form_field[1].substr(0, (my_form_field[1].length - (''+bk_type).length ) ) + id_additional ;


                formdata_additional += my_form_field[0] + '^' + my_form_field[1] + '^' + my_form_field[2];
            }

            if ( jQuery('#gateway_payment_forms' + bk_type).length > 0 ) {         // If Payment form  for main  booking resources is showing then append payment form  for additional  calendars.
                jQuery('#gateway_payment_forms' + bk_type).after('<div id="gateway_payment_forms'+id_additional+'"></div>');
                jQuery('#gateway_payment_forms' + bk_type).after('<div id="ajax_respond_insert'+id_additional+'" style="display:none;"></div>');
            }
            //FixIn: 8.5.2.17
            send_ajax_submit( id_additional ,formdata_additional,captcha_chalange,user_captcha,is_send_emeils,my_booking_hash,my_booking_form ,wpdev_active_locale  );  // Submit

//            if (document.getElementById('date_booking' + id_additional).value != '' ) {
//                setUnavailableSelectedDays(id_additional);                                              // Set selected days unavailable in this calendar
//                jQuery('#calendar_booking'+id_additional).insertBefore("#booking_form_div"+bk_type);    // Insert calendar before form to do not hide it
//                if (document.getElementById('gateway_payment_forms'+id_additional) != null)
//                    jQuery('#gateway_payment_forms'+id_additional).insertBefore("#booking_form_div"+bk_type);    // Insert payment form to do not hide it
//                else {
//                    jQuery("#booking_form_div"+bk_type).append('<div id="gateway_payment_forms'+id_additional+'" ></div>');
//                    jQuery("#booking_form_div"+bk_type).append('<div id="ajax_respond_insert'+id_additional+'" ></div>');
//                }
//                send_ajax_submit( id_additional ,formdata_additional,captcha_chalange,user_captcha,is_send_emeils,my_booking_hash,my_booking_form ,wpdev_active_locale  );
//            }
        }
    }
}


//<![CDATA[
function send_ajax_submit(bk_type,formdata,captcha_chalange,user_captcha,is_send_emeils,my_booking_hash,my_booking_form  ,wpdev_active_locale ) {
        // Ajax POST here

        var my_bk_res = bk_type;
        if ( document.getElementById('bk_type' + bk_type) != null ) my_bk_res = document.getElementById('bk_type' + bk_type).value;

        jQuery.ajax({                                           // Start Ajax Sending
            // url: wpdev_bk_plugin_url+ '/' + wpdev_bk_plugin_filename,
            url: wpbc_ajaxurl, 
            type:'POST',
            success: function (data, textStatus){if( textStatus == 'success')   jQuery('#ajax_respond_insert' + bk_type).html( data ) ;},
            error:function (XMLHttpRequest, textStatus, errorThrown){window.status = 'Ajax sending Error status:'+ textStatus;alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);if (XMLHttpRequest.status == 500) {alert('Please check at this page according this error:' + ' https://wpbookingcalendar.com/faq/#ajax-sending-error');}},
            // beforeSend: someFunction,
            data:{
                // ajax_action : 'INSERT_INTO_TABLE',
                action : 'INSERT_INTO_TABLE',
                bktype: my_bk_res ,
                dates: document.getElementById('date_booking' + bk_type).value ,
                form: formdata,
                captcha_chalange:captcha_chalange,
                captcha_user_input: user_captcha,
                is_send_emeils : is_send_emeils,
                my_booking_hash:my_booking_hash,
                booking_form_type:my_booking_form,
                wpdev_active_locale:wpdev_active_locale,
                wpbc_nonce: document.getElementById('wpbc_nonce' + bk_type).value 
            }
        });
}
//]]>

////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////

// Show Error Message in Booking Form  at Front End
function showErrorMessage( element , errorMessage , isScrollStop ) {    //FixIn: 8.5.1.3
    //FixIn: 8.4.0.2
    // if(typeof( bk_form_step_click ) == 'function') {
    //     bk_form_step_click();                                                   // rollback  to 1st  step,  if system  will show warning and booking form  is using this customization: in the Exmaple #2 here: https://wpbookingcalendar.com/faq/customize-booking-form-for-having-several-steps-of-reservation/
    // }

    //FixIn: 8.5.1.3
    if ( ! isScrollStop ){
        makeScroll( element );
    }
    //FixIn: 8.7.11.10
    if ( jQuery( "[name='" + element.name + "']" ).is( ':visible' ) ){
        jQuery("[name='"+ element.name +"']")
                .fadeOut( 350 ).fadeIn( 300 )
                .fadeOut( 350 ).fadeIn( 400 )
                .fadeOut( 350 ).fadeIn( 300 )
                .fadeOut( 350 ).fadeIn( 400 )
                .animate( {opacity: 1}, 4000 )
        ;  // mark red border
    }
    if (jQuery("[name='"+ element.name +"']").attr('type') == "radio") {

        if (    ( ! jQuery( "[name='" + element.name + "']" ).parent().parent().parent().next().hasClass( 'alert-warning' ))
             || (
                      (   jQuery( "[name='" + element.name + "']" ).parent().parent().parent().next().hasClass( 'alert-warning' ))
                   && ( ! jQuery( "[name='" + element.name + "']" ).parent().parent().parent().next().is( ':visible' ) )
                )
        ){  //FixIn: 8.4.5.7
            jQuery( "[name='" + element.name + "']" ).parent().parent().parent()
                .after( '<span class="wpdev-help-message alert alert-warning">' + errorMessage + '</span>' ); // Show message
        }

    } else if (jQuery("[name='"+ element.name +"']").attr('type') == "checkbox") {

        if (    ( ! jQuery( "[name='" + element.name + "']" ).parent().next().hasClass( 'alert-warning' ))
             || (
                      (   jQuery( "[name='" + element.name + "']" ).parent().next().hasClass( 'alert-warning' ))
                   && ( ! jQuery( "[name='" + element.name + "']" ).parent().next().is( ':visible' ) )
                )
        ){  //FixIn: 8.4.5.7
            jQuery( "[name='" + element.name + "']" ).parent()
                .after( '<span class="wpdev-help-message alert alert-warning">' + errorMessage + '</span>' ); // Show message
        }

    } else {

        if (    ( ! jQuery( "[name='" + element.name + "']" ).next().hasClass( 'alert-warning' ))
             || (
                      (   jQuery( "[name='" + element.name + "']" ).next().hasClass( 'alert-warning' ))
                   && ( ! jQuery( "[name='" + element.name + "']" ).next().is( ':visible' ) )
                )
        ){  //FixIn: 8.4.5.7
            jQuery( "[name='" + element.name + "']" )
                .after( '<span class="wpdev-help-message alert alert-warning">' + errorMessage + '</span>' ); // Show message
        }
    }
    jQuery(".wpdev-help-message")
            .css( {'padding' : '5px 5px 4px', 'margin' : '2px 2px 2px 10px', 'vertical-align': 'top', 'line-height': '32px' } );
    
    if ( element.type == 'checkbox' )
        jQuery(".wpdev-help-message").css( { 'vertical-align': 'middle'} );
            
    jQuery(".widget_wpdev_booking .booking_form .wpdev-help-message")
            .css( {'vertical-align': 'sub' } ) ;
    jQuery(".wpdev-help-message")
            .animate( {opacity: 1}, 10000 )
            .fadeOut( 2000 );

    if ( ! jQuery( "[name='" + element.name + "']" ).is( ':visible' ) ){
        makeScroll( jQuery(".wpdev-help-message") );
    }
    jQuery( element ).trigger( 'focus' );    //FixIn: 8.7.11.12
    return;

}

/**
 * Show message under specific element
 * 
 * @param {type} element - jQuery definition  of the element
 * @param {type} errorMessage - String message
 * @param {type} message_type "" | "alert-danger" | "alert-success" | "alert-info"
 */
function showMessageUnderElement( element , errorMessage , message_type) {
    //FixIn: 8.4.0.2
    // if(typeof( bk_form_step_click ) == 'function') {
    //     bk_form_step_click();                                                   // rollback  to 1st  step,  if system  will show warning and booking form  is using this customization: in the Exmaple #2 here: https://wpbookingcalendar.com/faq/customize-booking-form-for-having-several-steps-of-reservation/
    // }
    
     makeScroll( element );
    
     if ( jQuery( element ).attr('type') == "radio" ) {

        if (    ( ! jQuery( element ).parent().parent().parent().next().hasClass( 'alert-warning' ))
             || (
                      (   jQuery( element ).parent().parent().parent().next().hasClass( 'alert-warning' ))
                   && ( ! jQuery( element ).parent().parent().parent().next().is( ':visible' ) )
                )
        ){  //FixIn: 8.4.5.7
            jQuery( element ).parent().parent().parent()
                .after( '<span class="wpdev-help-message wpdev-element-message alert alert-warning ' + message_type + '">' + errorMessage + '</span>' ); // Show message
        }
    } else if (jQuery( element ).attr('type') == "checkbox") {

        if (    ( ! jQuery( element ).parent().next().hasClass( 'alert-warning' ))
             || (
                      (   jQuery( element ).parent().next().hasClass( 'alert-warning' ))
                   && ( ! jQuery( element ).parent().next().is( ':visible' ) )
                )
        ){  //FixIn: 8.4.5.7
            jQuery( element ).parent()
                .after( '<span class="wpdev-help-message wpdev-element-message alert alert-warning ' + message_type + '">' + errorMessage + '</span>' ); // Show message
        }
    } else {
        if (    ( ! jQuery( element ).next().hasClass( 'alert-warning' ))
                     || (
                              (   jQuery( element ).next().hasClass( 'alert-warning' ))
                           && ( ! jQuery( element ).next().is( ':visible' ) )
                        )
            ){  //FixIn: 8.4.5.7
            jQuery( element )
                .after( '<span class="wpdev-help-message wpdev-element-message alert alert-warning ' + message_type + '">' + errorMessage + '</span>' ); // Show message
        }
    }
    //    jQuery(".wpdev-help-message")
    //            .css( {'padding' : '5px 5px 4px', 'margin' : '10px 2px', 'vertical-align': 'middle' } );
    jQuery(".widget_wpdev_booking .booking_form .wpdev-help-message")
            .css( {'vertical-align': 'sub' } ) ;
    jQuery(".wpdev-help-message")
            .animate( {opacity: 1}, 10000 )
            .fadeOut( 2000 ); 
}

// Hint labels inside of input boxes
jQuery(document).ready( function(){

    jQuery('div.inside_hint').on( 'click', function(){                   //FixIn: 8.7.11.12
            jQuery(this).css('visibility', 'hidden').siblings('.has-inside-hint').trigger( 'focus' );   //FixIn: 8.7.11.12
    });

    jQuery('input.has-inside-hint').on( 'blur', function(){                   //FixIn: 8.7.11.12
        if ( this.value == '' )
            jQuery(this).siblings('.inside_hint').css('visibility', '');
    }).on( 'focus', function(){                                                 //FixIn: 8.7.11.12
            jQuery(this).siblings('.inside_hint').css('visibility', 'hidden');
    });

    jQuery('.booking_form_div input[type=button]').prop("disabled", false);
});


////////////////////////////////////////////////////////////////////////////
// Support Functions
////////////////////////////////////////////////////////////////////////////

// Scroll to script
function makeScroll(object_name) {
     var targetOffset = jQuery( object_name ).offset().top;
     //targetOffset = targetOffset - 50;
     if (targetOffset<0) targetOffset = 0;
     if ( jQuery('#wpadminbar').length > 0 ) targetOffset = targetOffset - 50;
     else  targetOffset = targetOffset - 20;
     jQuery('html,body').animate({scrollTop: targetOffset}, 500);
}


//FixIn: 8.8.1.3
/**
 * Trim  strings and array joined with  (,)
 *
 * @param string_to_trim   string / array
 * @returns string
 */
function wpbc_trim( string_to_trim ){

    if ( Array.isArray( string_to_trim ) ){
        string_to_trim = string_to_trim.join( ',' );
    }

    if ( 'string' == typeof (string_to_trim) ){
        string_to_trim = string_to_trim.trim();
    }

    return string_to_trim;
}


function wpdev_in_array (array_here, p_val) {
   for(var i = 0, l = array_here.length; i < l; i++) {
       if(array_here[i] == p_val) {
           return true;
       }
   }
   return false;
}


function days_between(date1, date2) {

    // The number of milliseconds in one day
    var ONE_DAY = 1000 * 60 * 60 * 24;

    // Convert both dates to milliseconds
    var date1_ms = date1.getTime();
    var date2_ms = date2.getTime();

    // Calculate the difference in milliseconds
    var difference_ms =  date1_ms - date2_ms;

    // Convert back to days and return
    return Math.round(difference_ms/ONE_DAY);

}


function daysInMonth(month,year) {
    var m = [31,28,31,30,31,30,31,31,30,31,30,31];
    if (month != 2) return m[month - 1];
    if (year%4 != 0) return m[1];
    if (year%100 == 0 && year%400 != 0) return m[1];
    return m[1] + 1;
}


function wpbc_timeline_nav( timeline_obj, nav_step ){
    
    jQuery( ".wpbc_timeline_front_end" ).trigger( "timeline_nav" , [ timeline_obj, nav_step ] );        //FixIn:7.0.1.48
    
    jQuery( '#'+timeline_obj.html_client_id + ' .wpbc_tl_prev,#'+timeline_obj.html_client_id + ' .wpbc_tl_next').remove();
    
    jQuery('#'+timeline_obj.html_client_id + ' .wpbc_tl_title').html( '<span class="glyphicon glyphicon-refresh wpbc_spin"></span> &nbsp Loading...' );      // '<div style="height:20px;width:100%;text-align:center;margin:15px auto;">Loading ... <img style="vertical-align:middle;box-shadow:none;width:14px;" src="'+wpdev_bk_plugin_url+'/assets/img/ajax-loader.gif"><//div>'

    if ( 'function' === typeof( jQuery(".popover_click.popover_bottom" ).popover )  )       //FixIn: 7.0.1.2  - 2016-12-10
        jQuery('.popover_click.popover_bottom').popover( 'hide' );                      //Hide all opned popovers
        
    jQuery.ajax({                                       
        url: wpbc_ajaxurl, 
        type:'POST',                                                            
        success: function ( data, textStatus ){                                 // Note,  here we direct show HTML to TimeLine frame
                    if( textStatus == 'success') {
                        jQuery('#' + timeline_obj.html_client_id + ' .wpbc_timeline_ajax_replace' ).html( data ); 
                        return true;
                    }
                },
        error:  function ( XMLHttpRequest, textStatus, errorThrown){ 
                    window.status = 'Ajax Error! Status: ' + textStatus;
                    alert( 'Ajax Error! Status: ' + XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText );
                },
        // beforeSend: someFunction,
        data:{
                action:             'WPBC_TIMELINE_NAV',
                timeline_obj:       timeline_obj,
                nav_step:           nav_step,
                wpdev_active_locale:wpbc_active_locale,
                wpbc_nonce:         document.getElementById('wpbc_nonce_'+ timeline_obj.html_client_id).value 
        }
    });     
}



/**
	 * Unselect all days in calendar
 *
 * @param bk_type - ID of booking resource
 */
function wpbc_unselect_all_days( bk_type ){																				//FixIn: 8.0.1.2

	var is_calendar_exist = jQuery( '#calendar_booking' + bk_type );

	if ( is_calendar_exist.length > 0 ){

		var inst = jQuery.datepick._getInst( document.getElementById( 'calendar_booking' + bk_type ) );					// Unselect all dates 	and set properties of Datepick

		jQuery( '#date_booking' + bk_type ).val( '' );
		inst.stayOpen = false;
		inst.dates = [];
		jQuery.datepick._updateDatepick( inst );
	}
}



//FixIn: 8.4.0.2
/**
 * Check errors in booking form  fields, and show warnings if some errors exist.
 * Check  errors,  like not selected dates or not filled requred form  fields, or not correct entering email or phone fields,  etc...
 *
 * @param bk_type  int (ID of booking resource)
 */
function wpbc_check_errors_in_booking_form( bk_type ) {

    var is_error_in_field = false;  // By default all  is good - no error

    var my_form = jQuery( '#booking_form' + bk_type );

    if ( my_form.length ) {

        // Pseudo-selector that get form elements <input , <textarea , <select, <button...
        my_form.find( ':input' ).each( function( index, el ) {

            // Skip some elements
            var skip_elements = [ 'hidden', 'button' ];

            if (  -1 == skip_elements.indexOf( jQuery( el ).attr( 'type' ) )  ){

				// Check Calendar Dates Selection
                if ( ( 'date_booking' + bk_type ) == jQuery( el ).attr( 'name' ) ) {

                    // Show Warning only  if the calendar visible ( we are at step with  calendar)
                    if (
                            (  ( jQuery( '#calendar_booking' + bk_type ).is( ':visible' )  ) && ( '' == jQuery( el ).val() )  )
                         && ( wpbc_get_arr_of_selected_additional_calendars( bk_type ).length == 0 )                    //FixIn: 8.5.2.26
                    ){            //FixIn: 8.4.4.5
						showMessageUnderElement( '#date_booking' + bk_type, message_verif_selectdts, '' );
						makeScroll('#calendar_booking' + bk_type);            // Scroll to the calendar    		//FixIn: 8.5.1.3
						is_error_in_field = true;    // Error
                    }
                }

                // Check only visible elements at this step
                if ( jQuery( el ).is( ':visible' )  ){
// console.log( '|id, type, val, visible|::', jQuery( el ).attr( 'name' ), '|' + jQuery( el ).attr( 'type' ) + '|', jQuery( el ).val(), jQuery( el ).is( ':visible' ) );

					// Is Required
					if ( jQuery( el ).hasClass( 'wpdev-validates-as-required' ) ){

						// Checkboxes
						if ( 'checkbox' == jQuery( el ).attr( 'type' ) ){

                            if (
                                    ( ! jQuery( el ).is( ':checked' ))
                                 && ( ! jQuery( ':checkbox[name="' + el.name + '"]', my_form ).is( ":checked" ) )       //FixIn: 8.5.2.12
                            ){
								showErrorMessage( el, message_verif_requred_for_check_box, is_error_in_field );
								is_error_in_field = true;    // Error
							}

							// Radio boxes
						} else if ( 'radio' == jQuery( el ).attr( 'type' ) ){

							if ( !jQuery( ':radio[name="' + jQuery( el ).attr( 'name' ) + '"]', my_form ).is( ':checked' ) ){
								showErrorMessage( el, message_verif_requred_for_radio_box, is_error_in_field );
								is_error_in_field = true;    // Error
							}

							// Other elements
						} else {

							var inp_value = jQuery( el ).val();

                            if ( '' === wpbc_trim( inp_value ) ){                                                       //FixIn: 8.8.1.3        //FixIn: 8.7.11.12
								showErrorMessage( el, message_verif_requred, is_error_in_field );
								is_error_in_field = true;    // Error
							}
						}
					}

					// Validate Email
					if ( jQuery( el ).hasClass( 'wpdev-validates-as-email' ) ){
						var inp_value = jQuery( el ).val();
						inp_value = inp_value.replace( /^\s+|\s+$/gm, '' );                // Trim  white space //FixIn: 5.4.5
						var reg = /^([A-Za-z0-9_\-\.\+])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,})$/;
						if ( (inp_value != '') && (reg.test( inp_value ) == false) ){
							showErrorMessage( el, message_verif_emeil, is_error_in_field );
							is_error_in_field = true;    // Error
						}
					}

					// Validate For digit entering - for example for - Phone
					// <p>Digit Field:<br />[text* dig_field class:validate_as_digit] </p>
					// <p>Phone:<br />[text* phone class:validate_digit_8] </p>

					var classList = jQuery( el ).attr( 'class' );

					if ( classList ){

						classList = classList.split( /\s+/ );

                        jQuery.each( classList, function ( cl_index, cl_item ){

                            ////////////////////////////////////////////////////////////////////////////////////////////

                            // Validate field value as "Date"   [CSS class - 'validate_as_digit']
                            if ( 'validate_as_date' === cl_item ) {

                                // Valid values: 09-25-2018, 09/25/2018, 09-25-2018,  31-9-1918  ---   m/d/Y, m.d.Y, m-d-Y, d/m/Y, d.m.Y, d-m-Y
                                var regex = new RegExp( '^[0-3]?\\d{1}[\\/\\.\\-]+[0-3]?\\d{1}[\\/\\.\\-]+[0-2]+\\d{3}$' );       // Check for Date 09/25/2018
                                var message_verif_phone = 'This field must be valid date like this ' + '09/25/2018';
                                var inp_value = jQuery( el ).val();

                                if (  ( inp_value != '' ) && ( regex.test( inp_value ) == false )  ){
                                    showErrorMessage( el, message_verif_phone, is_error_in_field );
                                    is_error_in_field = true;    // Error
                                }
                            }

                            ////////////////////////////////////////////////////////////////////////////////////////////

                            // Validate field value as "DIGIT"   [CSS class - 'validate_as_digit']
                            if ( 'validate_as_digit' === cl_item ) {

                                var regex = new RegExp( '^[0-9]+\\.?[0-9]*$' );       // Check for digits
                                var message_verif_phone = 'This field must contain only digits';
                                var inp_value = jQuery( el ).val();

                                if (  ( inp_value != '' ) && ( regex.test( inp_value ) == false )  ){
                                    showErrorMessage( el, message_verif_phone, is_error_in_field );
                                    is_error_in_field = true;    // Error
                                }
                            }

                            ////////////////////////////////////////////////////////////////////////////////////////////

                            // Validate field value as "Phone" number or any other valid number wth specific number of digits [CSS class - 'validate_digit_8' || 'validate_digit_10' ]
                            var is_validate_digit = cl_item.substring( 0, 15 );

                            // Check  if class start  with 'validate_digit_'
                            if ( 'validate_digit_' === is_validate_digit ){

                                // Get  number of digit in class: validate_digit_8 => 8 or validate_digit_10 => 10
                                var digits_to_check = parseInt( cl_item.substring( 15 ) );

                                // Check  about any errors in
                                if ( !isNaN( digits_to_check ) ){

                                    var regex = new RegExp( '^\\d{' + digits_to_check + '}$' );       // We was valid it as parseInt - only integer variable - digits_to_check
                                    var message_verif_phone = 'This field must contain ' + digits_to_check + ' digits';
                                    var inp_value = jQuery( el ).val();

									if (  ( inp_value != '' ) && ( regex.test( inp_value ) == false )  ){
                                        showErrorMessage( el, message_verif_phone, is_error_in_field );
                                        is_error_in_field = true;    // Error
                                    }
                                }
                            }

                            ////////////////////////////////////////////////////////////////////////////////////////////

                        });
    				}
                }
			}
        } );

	}
    return is_error_in_field;
}


//FixIn: 8.4.4.4
function bk_calendar_step_click( el ){
    var br_id = jQuery( el ).closest( 'form' ).find( 'input[name^="bk_type"]' ).val();
    var is_error = wpbc_check_errors_in_booking_form( br_id );
    if ( is_error ){
        return false;
    }
    if ( br_id != undefined ){
        jQuery( "#booking_form" + br_id + " .bk_calendar_step" ).css( {"display": "none"} );
        jQuery( "#booking_form" + br_id + " .bk_form_step" ).css( {"display": "block"} );
    } else {
        jQuery( ".bk_calendar_step" ).css( {"display": "none"} );
        jQuery( ".bk_form_step" ).css( {"display": "block"} );
    }
}

function bk_form_step_click( el ){
    var br_id = jQuery( el ).closest( 'form' ).find( 'input[name^="bk_type"]' ).val();
    var is_error = false; // wpbc_check_errors_in_booking_form( br_id );          //FixIn: 8.4.5.6
    if ( is_error ){
        return false;
    }
    if ( br_id != undefined ){
        jQuery( "#booking_form" + br_id + " .bk_calendar_step" ).css( {"display": "block"} );
        jQuery( "#booking_form" + br_id + " .bk_form_step" ).css( {"display": "none"} );
        makeScroll( "#bklnk" + br_id );
    } else {
        jQuery( ".bk_calendar_step" ).css( {"display": "block"} );
        jQuery( ".bk_form_step" ).css( {"display": "none"} );
    }
}

//FixIn: 8.6.1.15
/**
 * Go to next  specific step in Wizard style booking form, with
 * check all required elements specific step, otherwise show warning message!
 *
 * @param el
 * @param step_num
 * @returns {boolean}
 */
function wpbc_wizard_step( el, step_num, step_from ){
    var br_id = jQuery( el ).closest( 'form' ).find( 'input[name^="bk_type"]' ).val();

    //FixIn: 8.8.1.5
    if ( ( undefined == step_from ) || ( step_num > step_from ) ){
        if ( 1 != step_num ){                                                                       //FixIn: 8.7.7.8
            var is_error = wpbc_check_errors_in_booking_form( br_id );
            if ( is_error ){
                return false;
            }
        }
    }

    if ( wpbc_is_some_elements_visible( br_id, ['rangetime', 'durationtime', 'starttime', 'endtime'] ) ){
        if ( is_this_time_selections_not_available( br_id, document.getElementById( 'booking_form' + br_id ) ) ){
            return false;
        }
    }
    
    if ( br_id != undefined ){
        jQuery( "#booking_form" + br_id + " .wpbc_wizard_step" ).css( {"display": "none"} );
        jQuery( "#booking_form" + br_id + " .wpbc_wizard_step" + step_num ).css( {"display": "block"} );
    }
}


//FixIn: 8.6.1.15
/**
 * Check if at least  one element from  array  of  elements names in booking form  visible  or not.
 * Usage Example:   if ( wpbc_is_some_elements_visible( br_id, ['rangetime', 'durationtime', 'starttime', 'endtime'] ) ){ ... }
 *
 * @param bk_type
 * @param elements_names
 * @returns {boolean}
 */
function wpbc_is_some_elements_visible( bk_type, elements_names ){

    var is_some_elements_visible = false;

    var my_form = jQuery( '#booking_form' + bk_type );

    if ( my_form.length ){

        // Pseudo-selector that get form elements <input , <textarea , <select, <button...
        my_form.find( ':input' ).each( function ( index, el ){

            // Skip some elements
            var skip_elements = ['hidden', 'button'];

            if ( -1 == skip_elements.indexOf( jQuery( el ).attr( 'type' ) ) ){

                for ( var ei = 0; ei < ( elements_names.length - 1) ; ei++ ){

                    // Check Calendar Dates Selection
                    if ( (elements_names[ ei ] + bk_type) == jQuery( el ).attr( 'name' ) ){

                        if ( jQuery( el ).is( ':visible' ) ){
                            is_some_elements_visible = true;
                        }
                    }
                }
            }
        } );
    }
    return is_some_elements_visible;
}