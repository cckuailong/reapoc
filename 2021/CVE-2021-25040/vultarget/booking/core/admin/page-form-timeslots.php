<?php /**
 * @version 1.0
 * @package Booking Calendar
 * @category Booking Form Settings - Timeslots Generator
 * @author wpdevelop
 *
 * @web-site https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2018-05-27
 */

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

//FixIn: TimeFreeGenerator


// <editor-fold     defaultstate="collapsed"                        desc=" S e t t i n g s    S t r u c t u r e "  >

/**
 * Update Field Type Selector in Toolbar
 *
 * If the 'rangetime' already  exist  in the booking form,  so  we do not show it as add new field in generator,  because it can exist  only  once in booking form.
 *
 * @param array $params
 * @param array $visual_form_structure
 *
 * @return mixed
 */
function wpbc_form_gen_free_fields_selection_rangetime( $params,  $visual_form_structure ){

	foreach ( $visual_form_structure as $form_field_el ) {
		if (  ( isset( $form_field_el['name'] ) ) && ( 'rangetime' == $form_field_el['name'] )  ){

			unset( $params['items'][1]['options']['rangetime'] );
		}
	}
    return $params;
}
//TODO: AfterFinish: Uncomment it after  finish  of development !
add_filter( 'wpbc_form_gen_free_fields_selection', 'wpbc_form_gen_free_fields_selection_rangetime', 10, 2 );



/**
 * Show Titles, Options textareas &  Time Slots Table 	in Field Generator section - during Adding or Editing of this field.
 *
 * This function  is running after  selection  of option  in selectbox in toolbar  from "private function generate_field("
 *
 * @param string $field_name
 * @param array $field_options
 */
function wpbc_settings_form_page_after_values_timeslots_titles( $field_name, $field_options ){

	if ( 'rangetime_field_generator' === $field_name ) {

		// 2 Textarea for hiding: titles|options
		wpbc_timeslots_free__settings_value_row( $field_name . '_options', $field_options );

		// Timeslots Table Frame/template only
		// Fill by timeslots - after  page loaded from  JavaScript in this function wpbc_timeslots_table_config_js() ...
		wpbc_timeslots_free__settings_timeslots_table( $field_name . '_options', $field_options );

	}

}
add_action( 'wpbc_settings_form_page_after_values', 'wpbc_settings_form_page_after_values_timeslots_titles', 10, 2 );


	//TODO: AfterFinish: hide this field in settings
	/**
	 * Show Titles and Options for 'rangetime' field in Field Generator section - during Adding or Editing of this field.
	 *
	 * @param string $field_name
	 * @param array $field
	 */
	function wpbc_timeslots_free__settings_value_row( $field_name, $field ){

		$defaults = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => 'display:block;',
				'placeholder'       => '',
				'type'              => 'text',
				'description'       => '',
				'attr'              => array(),
				'rows'              => 7,
				'cols'              => 28,
				'show_in_2_cols'    => false,
				'group'             => 'general',
				'tr_class'          => '',
				'only_field'        => false,
				'description_tag'   => 'p'
				, 'validate_as'     => array()
		);

		$field = wp_parse_args( $field, $defaults );
		?>

		<tr class="wpbc_tr_rangetime_field_generator_options_titles"><td colspan="2" style="border-top:1px solid #eee;padding:10px 0 0;">

			<div class="parameter-group" style="float:left;margin-right:2em;">
				<legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>
				<label for="<?php echo esc_attr( $field_name ); ?>_options" class="control-label"><strong><?php
						_e( 'Time Slots', 'booking' ); ?></strong> (<?php _e( 'in 24 hour format', 'booking' ); ?>):</label>
				<textarea
								rows="<?php echo esc_attr( $field['rows'] ); ?>"
								cols="<?php echo esc_attr( $field['cols'] ); ?>"
								<?php /* type="<?php echo esc_attr( $field['type'] ); ?>" */ ?>
								id="<?php echo esc_attr( $field_name ); ?>_options"
								name="<?php echo esc_attr( $field_name ); ?>_options"
								class="input-text wide-input <?php echo esc_attr( $field['class'] );
																   echo ( ! empty($field['validate_as']) ) ? ' validate-' . implode( ' validate-', $field['validate_as'] ) : ''; ?>"
								style="<?php echo esc_attr( $field['css'] ); ?>"
								placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
								<?php disabled( $field['disabled'], true ); ?>
								autocomplete="off"
									oninput="javascript:this.onchange();"
									onpaste=javascript:this.onchange();"
									onkeypress="javascript:this.onchange();"
									onchange="javascript:wpbc_parse_time_options_titles__to_values( this );"

							><?php
							echo esc_textarea( $field['value'] ); ?></textarea>
				<p class="help-block"><?php _e( 'One option per line', 'booking' ); ?></p>
			</div>

			<div class="parameter-group" style="float:left">
				<legend class="screen-reader-text"><span><?php echo wp_kses_post( $field['title'] ); ?></span></legend>
				<label for="<?php echo esc_attr( $field_name ); ?>_options" class="control-label"><strong><?php
						 _e('Titles' ,'booking'); ?></strong>  (<?php _e('optional' ,'booking'); ?>):</label>
				<textarea
								rows="<?php echo esc_attr( $field['rows'] ); ?>"
								cols="<?php echo esc_attr( $field['cols'] ); ?>"
								<?php /* type="<?php echo esc_attr( $field['type'] ); ?>" */ ?>
								id="<?php echo esc_attr( $field_name ); ?>_titles"
								name="<?php echo esc_attr( $field_name ); ?>_titles"
								class="input-text wide-input <?php echo esc_attr( $field['class'] );
																   echo ( ! empty($field['validate_as']) ) ? ' validate-' . implode( ' validate-', $field['validate_as'] ) : ''; ?>"
								style="<?php echo esc_attr( $field['css'] ); ?>"
								placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
								<?php disabled( $field['disabled'], true ); ?>
								autocomplete="off"
									oninput="javascript:this.onchange();"
									onpaste=javascript:this.onchange();"
									onkeypress="javascript:this.onchange();"
									onchange="javascript:wpbc_parse_time_options_titles__to_values( this );"
							><?php
							echo esc_textarea( $field['value'] ); ?></textarea>
				<p class="help-block"><?php _e( 'One title per line', 'booking' ); ?>. <?php _e( 'Visible options in selectbox', 'booking' ); ?></p>
			</div>
			<div class="clear"></div>
		</td></tr>
		<?php
	}


	/**
	 * Show Time Slots Table  in Field Generator section - during Adding or Editing of this field.
	 *
	 * @param string $field_name
	 * @param array $field
	 */
	function wpbc_timeslots_free__settings_timeslots_table( $field_name, $field ){
	//debuge($field);
		$defaults = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => 'display:block;',
				'placeholder'       => '',
				'type'              => 'text',
				'description'       => '',
				'attr'              => array(),
				'rows'              => 7,
				'cols'              => 28,
				'show_in_2_cols'    => false,
				'group'             => 'general',
				'tr_class'          => '',
				'only_field'        => false,
				'description_tag'   => 'p'
				, 'validate_as'     => array()
		);

		$field = wp_parse_args( $field, $defaults );
	//debuge($field);
		?><tr class="wpbc_tr_rangetime_field_generator_timetable"><td colspan="2" style="padding:0;"><?php


			?><div style="margin: 25px 0 0;vertical-align: middle;line-height: 1.8em;"><?php

				?><label for="fill_timetable_5min" class="wpbc-form-text"><?php _e( 'Reset times by' ,'booking'); ?> </label> <?php

				?><a href="javascript:void(0)" class="fill_timetable fill_timetable_2hours button">2 <?php _e( 'hours' ,'booking');  ?></a> <?php
				?><a href="javascript:void(0)" class="fill_timetable fill_timetable_60min button">1 <?php _e( 'hour' ,'booking');  ?></a> <?php
				?><a href="javascript:void(0)" class="fill_timetable fill_timetable_30min button">30 <?php _e( 'minutes' ,'booking');  ?></a> <?php
				?><a href="javascript:void(0)" class="fill_timetable fill_timetable_15min button">15 <?php _e( 'minutes' ,'booking');  ?></a> <?php
				?><a href="javascript:void(0)" class="fill_timetable fill_timetable_5min button">5 <?php _e( 'minutes' ,'booking');  ?></a> <?php

				?><label for="fill_timetable_5min" class="wpbc-form-text"> <?php echo strtolower( __( 'Time Slots' ,'booking') ); ?> </label> <?php

				?><a href="javascript:void(0)" onclick="jQuery('.wpbc_tr_rangetime_field_generator_options_titles, .wpbc_tr_rangetime_field_generator_name, .wpbc_tr_rangetime_field_generator_value').toggle();"
					 style="float:right;font-size:08px;color: #aaa;text-decoration: none;">Timeslots Beta</a><?php
			?></div><?php

			?><div id="wpbc_timeslots_table_config">
				<table class="widefat wpbc_input_table sortable wpdevelop wpbc_table_form_free wpbc_table_form_free_timeslots" cellspacing="0" cellpadding="0"></table>
			</div><?php

		?></td></tr><?php
	}


// </editor-fold>


// <editor-fold     defaultstate="collapsed"                        desc=" C S S "  >

	function wpbc_timeslots_free_css(){

		?><style type="text/css">
			.wpbc_tr_rangetime_field_generator_options_titles,
			.wpbc_tr_rangetime_field_generator_name ,
			.wpbc_tr_rangetime_field_generator_value  {
				opacity: 0.55;
				display: none;
			}
			.wpbc_table_form_free a.button.add_time_bk_link.button{
				margin: 10px;
				font-weight: 600;
			}
			table.wpbc_table_form_free_timeslots.sortable {
				margin:20px 0;
			}
			.wpbc_table_form_free_timeslots.sortable tr th,
			.wpbc_table_form_free_timeslots.sortable tr td{
				padding: 4px 0;
				text-align: center;
				border-bottom: 1px solid #eee;
			}
			.wpbc_table_form_free_timeslots.sortable tr th {
					padding: 0;
			}
			.wpbc_table_form_free_timeslots.sortable select {
					/*padding: 0 10px;*/
			}
			.wpbc_time_divider {
				padding: 0 10px 6px;
				font-size: 22px;
				vertical-align: middle;
			}
			.wpbc_timeslots_divider {
				padding: 0 20px 6px;
			}
			wpbc_table_form_free_timeslots.sortable tfoot tr th {
				text-align: center;
			}
			a.fill_timetable.button {
				margin: 0 5px;
			}

			@media (max-width: 782px) {
				.wpbc_table_form_free_timeslots td select {
					width:38%;
					display: inline-block;
				}
				.wpbc_table_form_free tr td.field_label input{
					width:98%;
					display: inline-block;
				}
				.wpbc_time_divider {
					padding: 0 2px 6px;
				}
				.wpbc_table_form_free_timeslots.sortable td.field_time{
					border-bottom:none;
				}
				.wpbc_table_form_free_timeslots.sortable td.field_label {
					display: inline-block;
					margin: 0 10%;
					width: 59%;
					border-bottom:none;
				}
				.wpbc_table_form_free_timeslots.sortable td.field_actions {
					display: inline-block;
				}
				.wpbc_table_form_free_timeslots.sortable td.sort{
					border-top:1px solid #eee;
					border-bottom:1px solid #eee;
					text-align: left;
					padding-left: 1em;
					border-bottom: 1px dashed #ddd;
				}
			}
		</style><?php
	}

// </editor-fold>


// <editor-fold     defaultstate="collapsed"                        desc=" J a v a   S c r i p t "  >



/** JS for Sorting, removing form fields */
function wpbc_timeslots_table_config_js() {
	?>
	<script type="text/javascript">

//TODO: How to  work  with  autofill some predefined templates::
// 1. Set  template to field #rangetime_field_generator_value   -- Its Values textarea
// 2. Then  run this function wpbc_timeslots_table__fill_rows(),  whcih  is get objects from time values textare,  and then  fill  the time table !!!

		/**
		 *  Generate TimeSlots string.
		 *    time_interval		- minutes interval (ex. 30, 15, 60)
		 *  , start_hour_time   - start  of hours
		 *  , end_hour_time		- end of hours
		 *  , hour_interval		- in case,  if need to  show only  hours,  so  instead of default 1,  define here 2 or 3 hours (time_interval in this case muxt  be 60)
		 *
		 *  return 7:00 AM - 7:30 AM@@07:00 - 07:30\n7:30 AM - 8:00 AM@@07:30 - 08:00...
		 */
		function wpbc_get_timeslot_list( time_interval, start_hour_time, end_hour_time, hour_interval ){

			var timeslot = '';
			var tslot_last = '';
			var time_rows = [];

			_.each( _.range( start_hour_time, end_hour_time,  hour_interval ), function ( elh,ih, row_datah ) {
				_.each( _.range( 0, 60, time_interval ), function ( elm,im, row_datam ){

					if ( elh < 10 )  elh = '0' + parseInt(elh);
					if ( elm < 10 )  elm = '0' + parseInt(elm);

					if ( '' !== tslot_last ){

						timeslot = tslot_last + ' - ' + elh + ':' + elm;
						time_rows.push(   wpbc_timeslot_to_am_pm( tslot_last ) + ' - ' + wpbc_timeslot_to_am_pm( elh + ':' + elm ) + '@@' + timeslot );
					}

					tslot_last = elh + ':' + elm;

				});
			});

			var time_rows_str = time_rows.join( "\n" );

			return time_rows_str;
		}


		/**
		 * Convert 24 hour timeslot to  AM/PM format
		 *
		 *  timeslot '07:00 - 07:30'
		 *
		 *  return  '7:00 AM - 7:30 AM'
		 */
		function wpbc_timeslot_to_am_pm( timeslot ){

			var sufix = '';

			timeslot = timeslot.split(':');

			timeslot[0] = parseInt( timeslot );
			if ( timeslot[0] < 12 ) {
				timeslot[0] = timeslot[0] ;
				sufix = ' AM';
			} else if ( timeslot[0] == 12 ) {
				timeslot[0] = 12;
				sufix = ' PM';
			} else {
				timeslot[0] = ( timeslot[0] - 12 );
				sufix = ' PM';
			}

			timeslot = timeslot.join( ':' ) + sufix ;

			return timeslot;
		}


		/**
		 *  Add actions to  the template buttons,  for reset  timeslot table by  times with  specific time interval
			var tslots =    '06:00 - 06:30' + "\n" +
							'06:30 - 07:00' + "\n" +
							'07:00 - 07:30' + "\n" +
							'07:30 - 08:00' + "\n" +
							'08:00 - 08:30' + "\n" +
							'08:30 - 09:00' + "\n" +
							'09:00 - 09:30' + "\n" +
							'09:30 - 10:00' + "\n" +
							'10:00 - 10:30' + "\n" +
							'10:30 - 11:00' + "\n" +
							'11:00 - 11:30' + "\n" +
							'11:30 - 12:00' + "\n" +
							'12:00 - 12:30' + "\n" +
							'12:30 - 13:00' + "\n" +
							'13:00 - 13:30' + "\n" +
							'13:30 - 14:00' + "\n" +
							'14:00 - 14:30' + "\n" +
							'14:30 - 15:00' + "\n" +
							'15:00 - 15:30' + "\n" +
							'15:30 - 16:00' + "\n" +
							'16:00 - 16:30' + "\n" +
							'16:30 - 17:00' + "\n" +
							'17:00 - 17:30' + "\n" +
							'17:30 - 18:00' + "\n" +
							'18:00 - 18:30' + "\n" +
							'18:30 - 19:00' + "\n" +
							'19:00 - 19:30' + "\n" +
							'19:30 - 20:00' + "\n" +
							'20:00 - 20:30' + "\n" +
							'20:30 - 21:00' + "\n" +
							'21:00 - 21:30';
		 */
		jQuery(document).ready(function(){

			// 30 minutes
			jQuery('.fill_timetable_30min' ).on( 'click', function() {			//FixIn: 8.7.11.12

				jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4" style="font-weight: 600;text-align: left;padding: 10px;font-size: 1em;"><?php echo esc_js( __('Processing' ,'booking') ) ?>...</th></tr></tfoot>' );
				setTimeout(function(){
					var tslots = wpbc_get_timeslot_list( 30, 6, 22 , 1);
					jQuery( '#rangetime_field_generator_value' ).val( tslots );
					wpbc_check_typed_values('rangetime_field_generator'); 													// Update textareas - titles / options
					wpbc_timeslots_table__fill_rows();
				}, 10);
			});

			jQuery('.fill_timetable_60min' ).on( 'click', function() {
				jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4" style="font-weight: 600;text-align: left;padding: 10px;font-size: 1em;"><?php echo esc_js( __('Processing' ,'booking') ) ?>...</th></tr></tfoot>' );
				setTimeout(function(){

					jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4"><?php echo esc_js( __('Processing' ,'booking') ) ?></th></tr></tfoot>' );
					var tslots = wpbc_get_timeslot_list( 60, 6, 22 , 1);
					jQuery( '#rangetime_field_generator_value' ).val( tslots );
					wpbc_check_typed_values('rangetime_field_generator'); 													// Update textareas - titles / options
					wpbc_timeslots_table__fill_rows();
				}, 10);
			});

			jQuery('.fill_timetable_2hours' ).on( 'click', function() {
				jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4" style="font-weight: 600;text-align: left;padding: 10px;font-size: 1em;"><?php echo esc_js( __('Processing' ,'booking') ) ?>...</th></tr></tfoot>' );
				setTimeout(function(){
					jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4"><?php echo esc_js( __('Processing' ,'booking') ) ?></th></tr></tfoot>' );
					var tslots = wpbc_get_timeslot_list( 60, 6, 22 , 2);
					jQuery( '#rangetime_field_generator_value' ).val( tslots );
					wpbc_check_typed_values('rangetime_field_generator'); 													// Update textareas - titles / options
					wpbc_timeslots_table__fill_rows();
				}, 10);
			});

			jQuery('.fill_timetable_15min' ).on( 'click', function() {
				jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4" style="font-weight: 600;text-align: left;padding: 10px;font-size: 1em;"><?php echo esc_js( __('Processing' ,'booking') ) ?>...</th></tr></tfoot>' );
				setTimeout(function(){
					jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4"><?php echo esc_js( __('Processing' ,'booking') ) ?></th></tr></tfoot>' );
					var tslots = wpbc_get_timeslot_list( 15, 6, 22 , 1);
					jQuery( '#rangetime_field_generator_value' ).val( tslots );
					wpbc_check_typed_values('rangetime_field_generator'); 													// Update textareas - titles / options
					wpbc_timeslots_table__fill_rows();
				}, 10);
			});

			jQuery('.fill_timetable_5min' ).on( 'click', function() {
				jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4" style="font-weight: 600;text-align: left;padding: 10px;font-size: 1em;"><?php echo esc_js( __('Processing' ,'booking') ) ?>...</th></tr></tfoot>' );
				setTimeout(function(){
					jQuery( '.wpbc_table_form_free_timeslots' ).html( '<tfoot><tr><th colspan="4"><?php echo esc_js( __('Processing' ,'booking') ) ?></th></tr></tfoot>' );
					var tslots = wpbc_get_timeslot_list( 5, 6, 22 , 1);
					jQuery( '#rangetime_field_generator_value' ).val( tslots );
					wpbc_check_typed_values('rangetime_field_generator'); 													// Update textareas - titles / options
					wpbc_timeslots_table__fill_rows();
				}, 10);
			});

		});


 /*
 * TODO:
 *
 * 1) Run  init cration  of timetable after  selecting of timeslots in toolba!
 *
 * 2) Run  wpbc_reupdate_indexes_in_fields_at_timetable()
 * after  each  delete
 * and each  append of new rows
 * otherwise possible issue with  adding or removing new rows.
 */


		/**
		 *   Finish Saving edited TimeTable
		 */
		function wpbc_get_saved_value_from_timeslots_table(){

			// Update Fields   I N D E X E S    at  TimesTable	(useful,  during adding or removing of some rows,  when  we can have same indexes in fields)
			wpbc_reupdate_indexes_in_fields_at_timetable();

			// Get  number of rows
			var size = jQuery('#wpbc_timeslots_table_config tbody tr').size();

			var time_s, time_e, time_label;
			var time_rows = [];

			// Get parsed time-slots rows with labels,  if exist
			for ( var i = 0; i < size; i++ ){
				time_s = jQuery( "select[name='timeslotS[" + i + "]']" ).val();		//FixIn: 8.4.2.7
				time_e = jQuery( "select[name='timeslotE[" + i + "]']" ).val();		//FixIn: 8.4.2.7
				time_label = jQuery( "input[name='form_field_timeslot_label[" + i + "]']" ).val();	//FixIn: 8.4.2.7
				time_label = time_label.trim();

				if ( time_label.length > 0 ){
					time_label += '@@';
				}
				time_rows.push( time_label + time_s + ' - ' + time_e );
			}

			var time_rows_str = time_rows.join( "\n" );

			// Insert TimeTables slots into Value textarea
			var field_name = 'rangetime_field_generator';
			jQuery( '#' + field_name + '_value' ).val( time_rows_str );

			// Remove all  times
			jQuery('#wpbc_timeslots_table_config tbody').empty();

		    return true;
		}



		/**
		 * After page loaded!
		 *
		 * Create time-slots table from  initial Data
   		 */
		jQuery(document).ready(function(){

			var timetable_rows = [
				                  { selected_time_slot: "09:00 - 10:00", time_label: "9:00 AM - 10:00 AM" }
								, { selected_time_slot: "10:00 - 11:00", time_label: "10:00 AM - 11:00 AM" }
								, { selected_time_slot: "11:00 - 12:00", time_label: "11:00 AM - 12:00 PM (Noon)" }
								, { selected_time_slot: "12:00 - 13:00", time_label: "12:00 PM (Noon) - 1:00 PM" }
								, { selected_time_slot: "13:00 - 14:00", time_label: "1:00 PM - 2:00 PM" }
								, { selected_time_slot: "14:00 - 15:00", time_label: "2:00 PM - 3:00 PM" }
								, { selected_time_slot: "15:00 - 16:00", time_label: "3:00 PM - 4:00 PM" }
								, { selected_time_slot: "16:00 - 17:00", time_label: "4:00 PM - 5:00 PM" }
								, { selected_time_slot: "17:00 - 18:00", time_label: "5:00 PM - 6:00 PM" }
								, { selected_time_slot: "18:00 - 19:00", time_label: "6:00 PM - 7:00 PM" }
								];

			wpbc_fill_timeslots_table_by_rows( timetable_rows );
		});


		/**
		 *  Start Edit exist timeslot -- Fill TimeTable
		 */
		function wpbc_timeslots_table__fill_rows() {

			var data = wpbc_get_timeslots_arr_from__field_values_string();

			wpbc_fill_timeslots_table_by_rows( data );

		}


		/**
		 *  Fill    TimeTable     by     data
		 */
		function wpbc_fill_timeslots_table_by_rows( timetable_rows_data ){

			var wpbc_table_header_tpl = wp.template( "table-timelots-header" );			// direct underscore templates.
			var wpbc_table_rows_tpl   = wp.template( "table-timelots-rows" 	 );
			var wpbc_timeslots_tpl    = wp.template( "table-timelots-select" );

			// Add DropDown times list to object
			_.each( timetable_rows_data, function( el, indx, data_row ) {
				el.i = indx;
				timetable_rows_data[ indx ].times_dropdown = wpbc_timeslots_tpl( el );

			});

			var timeslots_table = '<thead>'
											+ wpbc_table_header_tpl()
								+ '</thead>'
								+ '<tbody>';

			_.each( timetable_rows_data, function( el, indx, data_row ) {
				el.i = indx;
				timeslots_table += wpbc_table_rows_tpl(  el  );
			});

			timeslots_table +='</tbody>'
							+ '<tfoot><tr><th colspan="4"><a href="javascript:void(0)" class="add_time_bk_link button"><?php _e( '+ Add Time Slot' ,'booking'); ?></a></th></tr></tfoot>'

			jQuery( '.wpbc_table_form_free_timeslots' ).html( timeslots_table );

			wpbc_make_table_sortable();

			jQuery( '.wpbc_input_table .delete_bk_link' ).off( 'click' );
			var is_show_confirm = true;
			wpbc_activate_table_row_delete( '.wpbc_input_table .delete_bk_link', is_show_confirm );

			jQuery( '.wpbc_input_table .delete_time_bk_link' ).off( 'click' );
			is_show_confirm = false;
			wpbc_activate_table_row_delete( '.wpbc_input_table .delete_time_bk_link', is_show_confirm );

			wpbc_timeslots_free_add_row();
		}


		/**
		 * 	Add    A C T I O N    to  "Add button"  for    "Append    1    Row    to    TimeTable"
		 */
		function wpbc_timeslots_free_add_row(){

			// Append    1    Row    to    TimeTable
			jQuery('.wpbc_input_table .add_time_bk_link').on( 'click', function(){                   //FixIn: 8.7.11.12

				var size = jQuery('#wpbc_timeslots_table_config tbody tr').size();

				var wpbc_timeslots_tpl    = wp.template( "table-timelots-select" );

				var row = { selected_time_slot: "10:00 - 11:00", time_label: "10:00 AM - 11:00 AM" };

				row.i = size;

				row.times_dropdown = wpbc_timeslots_tpl( row );

				var wpbc_table_rows_tpl   = wp.template( "table-timelots-rows" );

				var row_to_add = wpbc_table_rows_tpl(  row  );

				jQuery('#wpbc_timeslots_table_config tbody').append( row_to_add );

				// Update    A C T I O N    for Delete buttons :: firstly Remove all "Delete" actions and then  Add "Delete" button  actions.
				jQuery( '.wpbc_input_table .delete_time_bk_link' ).off( 'click' );
				is_show_confirm = false;
				wpbc_activate_table_row_delete( '.wpbc_input_table .delete_time_bk_link', is_show_confirm );

			});
		}


		// Support /////////////////////////////////////////////////////////////////////////////////////////////////////

		/**
		 *  Parse data from    '#rangetime_field_generator_value'    ->    array   with  values and titles
		 *
		 * return array [    Object { selected_time_slot: "10:00 - 12:00", time_label: "10:00 AM - 12:00 PM" }
                           , Object { selected_time_slot: "11:00 - 15:00", time_label: "" } ]
		 *
		 * For testing! :: Click  Edit timeslots and then Run  in console this: wpbc_get_timeslots_arr_from__field_values_string();
		 */
		function wpbc_get_timeslots_arr_from__field_values_string(){

			var field_name = 'rangetime_field_generator';

			/* Parse Saved TimeSlots values
			* array[  [ "10:00 - 12:00", 			"11:00 - 15:00", 	"14:00 - 16:00", 	… ]
			*         [ "10:00 AM - 12:00 PM", 	"", 				"Afternoon", 		… ] ]
			*/
			var tslots_arr = wpbc_get_titles_options_from_values( '#' + field_name + '_value' );

			var data = [];

			var row_obj = {};
			for ( var i = 0; i < tslots_arr[0].length; i++ ) {
				row_obj = { selected_time_slot: "", time_label: "" };
				row_obj.selected_time_slot = tslots_arr[0][ i ];
				row_obj.time_label 		   = tslots_arr[1][ i ];
				data.push( row_obj );
			}

			return data;
		}


		/**
		 * Transform "Options and Titles"  ->  "Values"
		 *
		 * Run "onchange" action  in text-areas "Titles" and "Options"
		 */
		function wpbc_parse_time_options_titles__to_values( el ){


			var field_name = 'rangetime_field_generator';

			// if ( 'rangetime_field_generator_options_options' === el.name ) { }

			var t_options = jQuery('#' + field_name + '_options_options').val();
			var t_titles  = jQuery('#' + field_name + '_options_titles').val();

			t_options = t_options.split("\n");
			t_titles  = t_titles.split("\n");
			var tslots  = '';

			var fin_el = '';
			for (var i = 0; i < t_options.length; i++) {

				if ( i ===  ( t_options.length - 1 ) ) {
					fin_el = '';
				} else {
					fin_el = "\n";
				}
				if (  ( typeof( t_titles[i] ) !== 'undefined' ) && ( t_titles[i] !== '' )  ) {
					tslots += '' + t_titles[i] + '@@' + t_options[i] + fin_el;
				} else {
					tslots += '' + t_options[i] + fin_el;
				}
			}
			jQuery('#' + field_name + '_value').val( tslots );
		}


		/**
		 * Update Fields   I N D E X E S    at  TimesTable	(useful,  during adding or removing of some rows,  when  we can have same indexes in fields)
		 */
		function wpbc_reupdate_indexes_in_fields_at_timetable(){

			var elemts_to_update = [ '#wpbc_timeslots_table_config table .form_field_timeslot_start'
									, '#wpbc_timeslots_table_config table .form_field_timeslot_end'
									, '#wpbc_timeslots_table_config table .form_field_timeslot_label' ];

			jQuery.each( elemts_to_update, function ( indx, arr_el ){

				jQuery( arr_el ).each( function( i ){

					var name = jQuery(this).attr('name');

					name = name.replace(/\[\d+\]/g, '[' + i + ']');

					jQuery( this ).attr({
					  	 //  'id': id,
						 'name': name
					});
				});

			});
		}


	</script>
	<?php
}

// </editor-fold>


// <editor-fold     defaultstate="collapsed"                        desc=" T e m p l a t e s   U n d e r s c o r e"  >

	/**
	 * Templates at footer of page
	 *
	 * @param $page string
	 */
	function wpbc_hook_settings_page_footer_templates( $page ){

		if ( 'form_field_free_settings'  === $page ) {

			// Header of Times Table
			?><script type="text/html" id="tmpl-table-timelots-header">
				<tr>
					<th class="sort"><span class="glyphicon glyphicon-sort" aria-hidden="true"></span></th>
					<th class="field_label"><?php   echo esc_js( __( 'Time Slots', 'booking' )  ); ?></th>
					<th class="field_options"><?php echo esc_js( __('Title', 'booking')  ) ; ?></th>
					<th class="field_actions"><?php echo esc_js( __('Actions', 'booking') ); ?></th>
				</tr>
			</script><?php


			// 1 row of Times Table
			?><script type="text/html" id="tmpl-table-timelots-rows">
				<tr class="account">
					<td class="sort" style="cursor: move;"><span class="glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span></td>
					<td class="field_time">{{{data.times_dropdown}}}</td>
					<td class="field_label">
					   <legend class="screen-reader-text"><span>{{data.time_label}}</span></legend>
					   <input  type="text"
							name="form_field_timeslot_label[{{data.i}}]"
							value="{{data.time_label}}"
							class="regular-text form_field_timeslot_label"
							placeholder="<?php _e( 'Label', 'booking'); ?>"
							autocomplete="off"
						/>
					</td>
					<td class="field_actions"><a href="javascript:void(0)" class="tooltip_top button-secondary button delete_time_bk_link" title="<?php _e('Remove' ,'booking'); ?>"><i class="glyphicon glyphicon-remove"></i></a></td>
				</tr>
			</script><?php


			// DropDown  for Times selection
			?><script type="text/html" id="tmpl-table-timelots-select">
				<#
				var prefxh = '';
				var prefxm = '';
				var is_selected = '';
				var slot = [ [10,0], [11,0] ];
																														// data.selected_time_slot = '10:00 - 12:00'
				if ( undefined != data.selected_time_slot ) {
				  slot = data.selected_time_slot.split('-');															// Array [ "10:00 ", " 12:00" ]

				  _.each( slot, function ( sl_el, sl_i, sl_data ) {

						slot[sl_i] = sl_el.split(':');																	// '10:00 ', ' 12:00'

						_.each( slot[sl_i], function ( sl_el_j, sl_i_j, sl_data_j ) {
							//slot[sl_i][ sl_i_j ] = sl_el_j.trim();  													// 0: Array [ "10", "00" ], 1: Array [ "12", "00" ]
							slot[sl_i][ sl_i_j ] = parseInt( sl_el_j );													// 0: Array [ 13, 0 ], 		1: Array [ 13, 23 ]
						});
				  });
																														// slot = Array [ Array [ 13, 0 ], Array [ 13, 23 ] ]
				}
				#>
				 <legend class="screen-reader-text"><span><?php  echo esc_attr( __('Time Slots', 'booking' ) ); ?></span></legend>
				 <select  name="timeslotS[{{data.i}}]" autocomplete="off"  class="form_field_timeslot_start" >
					 <# _.each( _.range( 0, 24, 1), function ( elh,ih, row_datah ) {  #>
					   <# _.each( _.range( 0, 60, 1), function ( el,i, row_data ) {  #>
					   <#
						is_selected = '';
						if (  ( slot[0][0] == elh ) && ( slot[0][1] == el )  ) {
							is_selected = 'selected="SELECTED"';
						}
					   prefxh = ( elh < 10 ) ? '0' : '';
					   prefxm = ( el < 10 ) ? '0' : '';
					   #>
						<option value="{{prefxh}}{{elh}}:{{prefxm}}{{el}}" {{is_selected}}>{{prefxh}}{{elh}} : {{prefxm}}{{el}}</option>
					   <# }); #>
					 <# }); #>
				 </select>
				 <strong class="wpbc_time_divider wpbc_timeslots_divider">&dash;</strong>
				 <select  name="timeslotE[{{data.i}}]" autocomplete="off" class="form_field_timeslot_end" >
					 <# _.each( _.range( 0, 24, 1), function ( elh,ih, row_datah ) {  #>
					   <# _.each( _.range( 0, 60, 1), function ( el,i, row_data ) {  #>
					   <#
						is_selected = '';
						if (  ( slot[1][0] == elh ) && ( slot[1][1] == el )  ) {
							is_selected = 'selected="SELECTED"';
						}
					   prefxh = ( elh < 10 ) ? '0' : '';
					   prefxm = ( el < 10 ) ? '0' : '';
					   #>
						<option value="{{prefxh}}{{elh}}:{{prefxm}}{{el}}" {{is_selected}}>{{prefxh}}{{elh}} : {{prefxm}}{{el}}</option>
					   <# }); #>
					 <# }); #>
				 </select>
			</script><?php

			wpbc_timeslots_table_config_js();
		}

	}
	add_action('wpbc_hook_settings_page_footer', 'wpbc_hook_settings_page_footer_templates');

// </editor-fold>