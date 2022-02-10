/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage Getenberg integration
 * @category inserting into posts
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2018-08-23Probably you updated your paid version of Booking Calendar
 */

//FixIn: 8.3.3.99

/*
		window.wp.blocks,
		window.wp.components,
		window.wp.element

 */
	//( function( blocks, components, element ) {

	( function( wp ) {
		/**
		 * Registers a new block provided a unique name and an object defining its behavior.
		 * @see https://github.com/WordPress/gutenberg/tree/master/blocks#api
		 */
		var registerBlockType = wp.blocks.registerBlockType;

		/**
		 * Returns a new element of given type. Element is an abstraction layer atop React.
		 * @see https://github.com/WordPress/gutenberg/tree/master/packages/element#element
		 */
		var el = wp.element.createElement;

		/**
		 * Retrieves the translation of text.
		 * @see https://github.com/WordPress/gutenberg/tree/master/i18n#api
		 */
		var __ = wp.i18n.__;

		//FixIn: 8.4.3.1
		/*
		var source = wp.blocks.source,
		    RichText = wp.editor.RichText,
			BlockControls = wp.editor.BlockControls,
			AlignmentToolbar = wp.editor.AlignmentToolbar;*/


		registerBlockType( 'booking/booking', {

			title: 'Booking Calendar',

			description: __( 'Show a booking form, availability calendar or other elements from Booking Calendar plugin.' ),

			icon:  {
						// Specifying a background color to appear with the icon e.g.: in the inserter.
						background: 'rgb(129, 142, 160)',
						// Specifying a color for the icon (optional: if not set, a readable color will be automatically defined)
						foreground: '#fff',
						// Specifying a dashicon for the block
						src: 'calendar-alt'
					},

			category: 'common',					// common | formatting | layout | widgets | embed

			/*
			// Use the block just once per post 	// its possible to use several Booking Calendar forms for different booking resources
			multiple: false,
			*/

			// // Add the support for block's alignment (left, center, right, wide, full).
			// align: true,
			//
			// // Pick which alignment options to display.
			// align: [ 'left', 'right', 'full' ],

			keywords: [ 'wpbc' , 'oplugins', 'form' ],

			// // Specifying my block attributes
			attributes: {
							/*content: {
											type: 'string',
											source: 'children',
											selector: 'p',
										},*/

							wpbc_shortcode: {
												type: 'string',
												default: ''
											}
			},


			edit: function( props ) {

//console.log( 'WPBC-Gb :: Edit :', props );

					jQuery( '.wpbc-gutenberg-update-view').remove();

					var children = [],
					cid =  props.clientId;	// its reference to unique 'data-block' attribute in section: <div class="editor-block-list__block-edit" data-block="8a1b713a-6981-43d3-a1f5-ce98b0e611d4"> ...

					var btnClassName = 'button wpbc-gutenberg-open-btn';


					////////////////////////////////////////////////////////////////////////////////////////////////////
					////////////////////////////////////////////////////////////////////////////////////////////////////

					// Old value from  attribute
					var _val = props.attributes.wpbc_shortcode;

					// Possibly new value, set to the text field programmatically from popup
					var _valNew = jQuery( 'div[data-block="' + cid + '"] .wpbc_gb_text_shortcode' ).val();

//console.log( '%cWPBC-Gb :: E d i t  >>> _valNew , _val , cid, obj', 'color: green; font-weight: bold;', _valNew , _val  , cid , jQuery( 'div[data-block="' + cid + '"] .wpbc_gb_text_shortcode' ) );

					// Default value here
					if ( typeof _val == typeof undefined ) {
						_val = '';
					}
					if ( ( typeof _valNew != typeof undefined ) &&  ( _val !== _valNew ) ) {
						_val = _valNew;
						props.setAttributes( { wpbc_shortcode: _val } );
					}

					////////////////////////////////////////////////////////////////////////////////////////////////////
					////////////////////////////////////////////////////////////////////////////////////////////////////


					children.push(
						el( 'a',
						   {
								className : btnClassName,
								// href      : 'javascript:void(0)',													//FixIn: 8.7.3.17	href: '#!'
							    href         : '#!',
								data_block_id: cid,
							    popup_tab_index: 0						// Will be index for active tab in popup dialog
							   , key: 'configure_' + cid																//FixIn: 8.7.3.18
							},
						__( 'Configure Booking Calendar Block' )
						)
					);

					// Visual Preview of Block
					children = wpbc_gt_parse_shortcode( props.attributes.wpbc_shortcode, children , cid );				//FixIn: 8.7.3.18

//console.log( 'WPBC children', children) ;

					children.push(

						el(
							'input',
							{
								key: 'onchangewpbcinput_' + cid,														//FixIn: 8.7.3.18
								value	  : _val, //props.attributes.wpbc_shortcode,
								onChange  : function ( event ) {
												props.setAttributes( {wpbc_shortcode: event.target.value } );
//console.log( '%cWPBC-Gb :: o n C h a n  g e  !!!! Y E S  !!! event for onChangeWPBCinputShortcode', 'color: orange; font-weight: bold;', event );
											},
								onClick: function (event) {																//FixIn: 8.8.2.10
										props.setAttributes( {wpbc_shortcode: event.target.value } );
								},
								className: 'wpbc_gb_text_shortcode',
								type     : 'text',
								//readOnly : 'readonly',																//FixIn: 8.8.2.10
								//disabled : 'disabled',																//FixIn: 8.8.2.10
								// onFocus  : function ( event ){
								// 								event.target.select();
								// 		}
							}
						)

					);


					// Show Hided Block (after configuration) -- React  update Block preview
					jQuery( '.wpbc_gb_div_block' ).parent().removeClass( 'hidden' );


					var wpbc_gb_div_block_css = 'wpbc_gb_div_block';
					if ( '' == props.attributes.wpbc_shortcode ) {
						// If no shortcode at all,  then  do not hide configure button
						wpbc_gb_div_block_css += ' wpbc_gb_div_block_no_shortcode';
					}

					return el( 'div', { className: wpbc_gb_div_block_css }, children );
			},


			save: function( props ) {

//console.log( 'WPBC-Gb :: Saving ', props );

				return el( 'div', null, props.attributes.wpbc_shortcode );
			}


		} );


	} )(
		window.wp
	);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	( function( $ ) {

		$( document ).on( 'click', '.wpbc_gb_block_preview_inner_title_edit', function( e ) {
//jQuery( '.wpbc-gutenberg-open-btn' ).hide();
			$( this ).closest( '.wpbc_gb_div_block' ).find( '.wpbc-gutenberg-open-btn' ).trigger( 'click' );			//FixIn: 8.7.11.12

		});

		/**
		 *  Open popup window for configuration Booking Calendar shortcode
		 */
		$( document ).on( 'click', '.wpbc-gutenberg-open-btn', function( e ) {

			e.preventDefault();

			// Set ID of currently edited section in post (just in case if we will have several sections
			var _id = $( this ).attr( 'data_block_id' );


			// Remove CSS class 'is-selected' (remove blue focus) in main DIV (after popup opened), because of that hide via CSS BIG blue button: "Configure Booking Calendar Block"
			jQuery( 'div[data-block="' + _id + '"]' ).removeClass( 'is-selected' );										//FixIn: 8.8.2.10

			// Get num. of popup active tab to  set
			var popup_tab_index = $( this ).attr( 'popup_tab_index' );

			var wpbc_tag = '';

			wpbc_tiny_btn_click( wpbc_tag );

			jQuery( "#wpbc_text_gettenberg_section_id" ).val( _id );

			// Select specific TAB in poup dialog
			jQuery( "#wpbc_tiny_modal .wpdvlp-top-tabs a.nav-tab" ).eq( popup_tab_index ).trigger( 'click' );			//FixIn: 8.7.11.12


//console.log( 'WPBC-Gb :: Popup window for configuration Booking Calendar shortcode. Section #', _id );

		});


		/**
		 *  Remove Update view button  after clicking on it.
		 */
		$( document ).on( 'click', '.wpbc-gutenberg-update-preview-btn', function( e ) {

			e.preventDefault();

			jQuery( '.wpbc-gutenberg-update-view').remove();

//console.log( 'WPBC-Gb :: Preview button clicked. Section #' );

		});


	} ) ( jQuery );


	/**
	 * Send shortcode from popup dialog into the gutenberg sections.
	 *
	 * @param shortcode_text
	 * @returns {boolean}
	 */
	function wpbc_send_text_to_gutenberg( shortcode_text ){

		// Get ID of section, where to  insert  shortcode configuraiton
		var block_section_id = jQuery( "#wpbc_text_gettenberg_section_id" ).val();

//console.log( 'WPBC-Gb :: wpbc_send_text_to_gutenberg' , shortcode_text, block_section_id );

		if ( '' == block_section_id ) {

			return false;		// if no such  block then just return  false, its means tha inserting in Classic block - TinyMCE
		}


		// Code to  insert into Gutenberg section in our text field
		jQuery( 'div[data-block="' + block_section_id + '"] .wpbc_gb_text_shortcode' ).val( shortcode_text );

		//Its does not work for automatic generating "Edit" event :((( , so we make some workarround in Edit block event
		jQuery( 'div[data-block="' + block_section_id + '"] .wpbc_gb_text_shortcode' ).trigger( 'focus' ).trigger('mousedown').trigger( 'click' ).trigger('mouseup').trigger('change');

		//FixIn: 8.4.2.10
		//FixIn: 8.7.3.17	href: '#!'
		//FixIn: 8.7.3.19		- chnaged <a href="#!" to '<div href="#!"	- its make update of block  after  clicking on DIV (and not A) element

//FixIn: 8.8.2.10	- commenting these 2 blocks
// jQuery( 'div[data-block="' + block_section_id + '"]' ).parent().parent().before(
// 	'<div class="editor-block-list__block wpbc-gutenberg-update-view" style="cursor: pointer;text-align: center;">' +
// 	'<div href="#!" class="button wpbc-gutenberg-update-preview-btn" ' +
// 	'>' + wp.i18n.__( 'Click to Preview Block' ) + '</div></div>'
// );
// 			//FixIn: 8.7.6.11
// jQuery( 'div[data-block="' + block_section_id + '"]' ).before(
// 	'<div class="editor-block-list__block wpbc-gutenberg-update-view" style="cursor: pointer;text-align: center;">' +
// 	'<div href="#!" class="button wpbc-gutenberg-update-preview-btn" ' +
// 	'>' + wp.i18n.__( 'Click to Preview Block' ) + '</div></div>'
// );



		// Hide entire Block -- until React does not update Block preview
		//jQuery( 'div[data-block="' + block_section_id + '"]' ).addClass( 'hidden' );									//FixIn: 8.8.2.10		- commenting

		// Neet to return true, to prevent insertion into some other TinyMCE block, if exist, because we have inserted it into Gutenberg
		return true;
	}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Parse shortcode in text  field and show Visual Preview of element
	 *
	 * @param shortcode_in_text	- shortcode from  text field
	 * @param children			- array of el for gnerating preview
	 * @returns 				- array of el
	 */
	function wpbc_gt_parse_shortcode( shortcode_in_text, children, cid ){												//FixIn: 8.7.3.18

//console.log( 'wpbc_gt_parse_shortcode cid', cid );

		var   wpbc_shortcode_type
			, shortcode_obj
			, block_preview_el = ''
			, el = wp.element.createElement;

		var block_header_txt, block_text_txt;

		var wpbc_shortcode_type_arr = [   'booking'
										, 'bookingcalendar'
										, 'bookingtimeline'
										, 'bookingselect'
										, 'bookingform'
										, 'bookingsearch'
										, 'bookingsearchresults'
										, 'bookingedit'
										, 'bookingcustomerlisting'
										, 'bookingresource'
									  ];
		var wpbc_shortcode_type_arr_length = wpbc_shortcode_type_arr.length;

		for ( var i = 0; i < wpbc_shortcode_type_arr_length ; i++ ){

			wpbc_shortcode_type = wpbc_shortcode_type_arr[ i ];

			shortcode_obj = wp.shortcode.next( wpbc_shortcode_type, shortcode_in_text, 0 );				// Parse shortcode

			if ( undefined != shortcode_obj ){

				block_preview_el = '';
//console.log( 'wpbc_shortcode_type' , wpbc_shortcode_type);
				// Get Preview
				switch ( wpbc_shortcode_type ){

					case 'booking':
						block_preview_el = wpbc_gt_get_visual_block_for_booking( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid		//FixIn: 8.7.3.18
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = 0;									// Set index of Active tab in popup dialog
						break;

					case 'bookingcalendar':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingcalendar( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = 2;									// Set index of Active tab in popup dialog
						break;

					case 'bookingtimeline':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingtimeline( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = 1;									// Set index of Active tab in popup dialog
						break;

					case 'bookingselect':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingselect( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = 3;									// Set index of Active tab in popup dialog
						break;

					case 'bookingform':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingform( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = 5;									// Set index of Active tab in popup dialog
						break;

					case 'bookingsearch':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingsearch( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = 4;									// Set index of Active tab in popup dialog
						break;

					case 'bookingsearchresults':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingsearchresults( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = 4;									// Set index of Active tab in popup dialog
						break;

					case 'bookingedit':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingedit( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = -1;									// Set index of Active tab in popup dialog
						break;

					case 'bookingcustomerlisting':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingcustomerlisting( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = -1;									// Set index of Active tab in popup dialog
						break;

					case 'bookingresource':
						block_preview_el = wpbc_gt_get_visual_block_for_bookingresource( shortcode_obj.shortcode, {
																									'shortcode_in_text': shortcode_in_text
																									, 'cid_key': wpbc_shortcode_type+ '_' + cid
																						} );
						children[ (children.length - 1) ].props.popup_tab_index = -1;									// Set index of Active tab in popup dialog
						break;

					default:
						block_preview_el = wpbc_gt_get_visual_block_for_default( shortcode_obj.shortcode
												, {
													'shortcode_in_text': shortcode_in_text,
													'block_header'     : block_header_txt,
													'block_text'       : block_text_txt
													, 'cid_key': wpbc_shortcode_type+ '_' + cid
												}
						);

				}

//console.log( 'WPBC-Gb :: block_preview_el', block_preview_el);

				if ( '' != block_preview_el ){
					children.push( block_preview_el );
				}
			}

		}

		return children;
	}


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**
	 *  Generate Visual Preview Block  - just general Shortcode
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_default( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			type      : 1,
			nummonths : 1,
			form_type : 'standard',
			aggregate : null,
			startmonth: null,
			options   : null
		};

		// // Calendar Parameters
		// var shortcode_defaults = {
		// 	type      : 1,
		// 	nummonths : 1,
		// 	aggregate : null,
		// 	startmonth: null,
		// 	options   : null
		// };

		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: params[ 'block_header' ], cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters(
											[
												  { block_text: params[ 'block_text' ] }
												, { name: 'Booking form', value: 'super-booking-admin'}
												, { name: 'Number of months to show', value: '2'}
											]
											, { cid_key: 'body_' + params[ 'cid_key' ] }
									  )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_default' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End
	}


	/**
	 *  Generate Visual Preview Block of Booking form
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_booking( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			type      : 1,
			nummonths : 1,
			form_type : 'standard',
			aggregate : null,
			startmonth: null,
			options   : null
		};


		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		var inner_header = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_header'
										, key: 'header_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Booking Form' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_body'
										, key: 'body_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_booking( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_footer'
										, key: 'footer_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);


		return  el( 'div', {
							className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_booking'
							, key: 'preview_wrapper_' + params[ 'cid_key' ]												//FixIn: 8.7.3.18
						}
					, el( 'div', {
									className: 'wpbc_gb_block_shortcode_preview_content'
									, key: 'preview_content_' + params[ 'cid_key' ]										//FixIn: 8.7.3.18
								}
								, [ inner_header, inner_body ]
						)
					, inner_footer
				);
	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_booking( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			if ( undefined != props[ 'type' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Booking resource' ), value: 'ID = ' + props[ 'type' ]} );
			}
			if ( undefined != props[ 'nummonths' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Visible months number' ), value: props[ 'nummonths' ]} );
			}
			if ( undefined != props[ 'startmonth' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Start month' ), value: props[ 'startmonth' ] } );
			}
			if ( ( undefined != props[ 'form_type' ] ) && ( 'standard' != props[ 'form_type' ] )  ){
				rows_in_content.push( { name: wp.i18n.__( 'Custom booking form' ), value: props[ 'form_type' ] } );
			}
			if ( undefined != props[ 'aggregate' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Unavailable dates from other booking resources' ), value: 'ID = ' + props[ 'aggregate' ] } );
			}
			if ( undefined != props[ 'options' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Options' ), value: props[ 'options' ] } );
			}

			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of Booking form
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingcalendar( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			type      : 1,
			nummonths : 1,
			aggregate : null,
			startmonth: null,
			options   : null
		};


		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		var inner_header = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_header'
										, key: 'header_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Availability Calendar' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_body'
										, key: 'body_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingcalendar( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_footer'
										, key: 'footer_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);


		return  el( 'div', {
							  className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingcalendar'
							, key: 'preview_wrapper_' + params[ 'cid_key' ]												//FixIn: 8.7.3.18
						   }

						, el( 'div', {
										  className: 'wpbc_gb_block_shortcode_preview_content'
										, key: 'preview_content_' + params[ 'cid_key' ]										//FixIn: 8.7.3.18
									 }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingcalendar( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			if ( undefined != props[ 'type' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Booking resource' ), value: 'ID = ' + props[ 'type' ]} );
			}
			if ( undefined != props[ 'nummonths' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Visible months number' ), value: props[ 'nummonths' ]} );
			}
			if ( undefined != props[ 'startmonth' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Start month' ), value: props[ 'startmonth' ] } );
			}
			if ( undefined != props[ 'aggregate' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Unavailable dates from other booking resources' ), value: 'ID = ' + props[ 'aggregate' ] } );
			}
			if ( undefined != props[ 'options' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Options' ), value: props[ 'options' ] } );
			}

			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of TimeLine
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingtimeline( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			type      		: 'Default',		// 1,
			view_days_num 	: 30,		// 30,

			scroll_start_date : null,		// '',
			scroll_day		: null,		// 0,
			scroll_month   	: null,		// 0,
			header_title   	: null,		// '',
			limit_hours		: null,		// '0,24'
		};
		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;


		var inner_header = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_header'
										, key: 'header_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Timeline' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_body'
										, key: 'body_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingtimeline( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {
										className: 'wpbc_gb_block_preview_inner_footer'
										, key: 'footer_' + params[ 'cid_key' ]											//FixIn: 8.7.3.18
									}
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingtimeline' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingtimeline( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			if ( undefined != props[ 'type' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Booking resource(s)' ), value: 'ID = ' + props[ 'type' ]} );
			}
			if ( undefined != props[ 'view_days_num' ] ){

				if ( '1' == props[ 'view_days_num' ] ) {
					props[ 'view_days_num' ] = 'Day';
				}
				if ( '7' == props[ 'view_days_num' ] ) {
					props[ 'view_days_num' ] = 'Week';
				}
				if ( '30' == props[ 'view_days_num' ] ) {
					props[ 'view_days_num' ] = 'Month';
				}
				if ( '60' == props[ 'view_days_num' ] ) {
					props[ 'view_days_num' ] = '2 Months';
				}
				if ( '90' == props[ 'view_days_num' ] ) {
					props[ 'view_days_num' ] = '3 Months';
				}
				if ( '365' == props[ 'view_days_num' ] ){
					props[ 'view_days_num' ] = 'Year';
				}
				rows_in_content.push( {name: wp.i18n.__( 'View mode' ), value: props[ 'view_days_num' ]} );
			}
			if ( undefined != props[ 'header_title' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Title' ), value: props[ 'header_title' ] } );
			}
			if ( undefined != props[ 'scroll_day' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Number of days to scroll' ), value:  props[ 'scroll_day' ] } );
			}
			if ( undefined != props[ 'scroll_month' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Number of months to scroll' ), value: props[ 'scroll_month' ] } );
			}
			if ( undefined != props[ 'scroll_start_date' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Start Date' ), value: props[ 'scroll_start_date' ] } );
			}
			if ( undefined != props[ 'limit_hours' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Show from/to' ), value: props[ 'limit_hours' ] } );
			}

			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of Booking form
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingselect( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			type              : wp.i18n.__( 'All booking resources' ),
			nummonths         : 1,
			form_type         : null,		// : 'standard',
			selected_type     : null,		// : '',
			label             : null,		// : '',
			first_option_title: wp.i18n.__( 'Please Select' ),
			startmonth        : null,
			options           : null
		};


		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Selection of Resources' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingselect( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingselect' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End

	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingselect( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			if ( undefined != props[ 'type' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Booking resource(s)' ), value: props[ 'type' ]} );
			}
			if ( undefined != props[ 'label' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Label' ), value: props[ 'label' ] } );
			}
			if ( undefined != props[ 'selected_type' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Selected booking resource (by default)' ), value: 'ID = ' + props[ 'selected_type' ] } );
			}
			if ( undefined != props[ 'first_option_title' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Title of first option in list' ), value: props[ 'first_option_title' ] } );
			}
			if ( undefined != props[ 'nummonths' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Visible months number' ), value: props[ 'nummonths' ]} );
			}
			if ( undefined != props[ 'startmonth' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Start month' ), value: props[ 'startmonth' ] } );
			}
			if ( ( undefined != props[ 'form_type' ] ) && ( 'standard' != props[ 'form_type' ] )  ){
				rows_in_content.push( { name: wp.i18n.__( 'Custom booking form for all booking resources' ), value: props[ 'form_type' ] } );
			}
			if ( undefined != props[ 'aggregate' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Unavailable dates from other booking resources' ), value: 'ID = ' + props[ 'aggregate' ] } );
			}
			if ( undefined != props[ 'options' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Options' ), value: props[ 'options' ] } );
			}

			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of Booking form
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingform( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			type      : 1,
			selected_dates : null,
			form_type : 'standard'
		};


		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Booking Form (without calendar)' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingform( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingform' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End
	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingform( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			if ( undefined != props[ 'type' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Booking resource' ), value: 'ID = ' + props[ 'type' ]} );
			}
			if ( undefined != props[ 'selected_dates' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Date for submit booking' ), value: props[ 'selected_dates' ]} );
			}
			if ( ( undefined != props[ 'form_type' ] ) && ( 'standard' != props[ 'form_type' ] )  ){
				rows_in_content.push( { name: wp.i18n.__( 'Custom booking form' ), value: props[ 'form_type' ] } );
			}

			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of Search Availability Form
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingsearch( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			searchresultstitle 	: '',		// searchresultstitle='{searchresults} Result(s) Found'
			noresultstitle 		: '',		// noresultstitle='Nothing Found'
			users 				: null,		// users='3,55'
			searchresults 		: null		// searchresults='http://test.com/search-results'
		};

		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Search Availability form' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingsearch( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingsearch' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End

	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingsearch( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			if ( undefined != props[ 'searchresults' ] ){
				rows_in_content.push( {	  name:  wp.i18n.__( 'Show search results on other page' )
										, value: wp.element.createElement( 'a', { href: props[ 'searchresults' ] }, props[ 'searchresults' ] )
									} );
				rows_in_content.push( {name: wp.i18n.__( 'Note' ), value: wp.i18n.__( 'Search results page must have this shortcode' ) +  ' [bookingsearchresults]' } );
				rows_in_content.push( { block_text: '---' } );
			} else {
				rows_in_content.push( { block_text: wp.i18n.__( 'Show search results in the same page' ) } );
			}
			if ( undefined != props[ 'searchresultstitle' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Search Results Title' ), value: props[ 'searchresultstitle' ]} );
			}
			if ( undefined != props[ 'noresultstitle' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Title, if no search results' ), value: props[ 'noresultstitle' ]} );
			}
			if ( undefined != props[ 'users' ] ){
				rows_in_content.push( {name: wp.i18n.__( 'Search in booking resources of WP users' ), value: 'ID = ' + props[ 'users' ]} );
			}

			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of Search Results
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingsearchresults( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
		};

		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Search Results' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingsearchresults( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingsearch' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End
	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingsearchresults( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			rows_in_content.push( { block_text: wp.i18n.__( 'Show search results on this page, after redirection from search form at other page.' ) } );

			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of Booking Edit - system shortcode
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingedit( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
		};

		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'System Block' ) + ' (' + wp.i18n.__( 'Booking Calendar Editing' ) + ')', cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingedit( props ,'body_' + params[ 'cid_key' ] ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingedit' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End
	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingedit( props, cid_key ){

			var el = wp.element.createElement;

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			rows_in_content.push( { block_text: wp.i18n.__( 'This block required for ability to edit, cancel the booking by visitor, who made the booking, or for ability to show payment form, after sending payment request.' ) } );

			rows_in_content.push( {	block_text:

										el( 'div', { key: 'wpbc_be1_' + cid_key }
												, el( 'span', { key: 'wpbc_be2_' + cid_key }, wp.i18n.__( 'Link to this page must be defined' ) )
												, ' '
												, el( 'a', { href: 'admin.php?page=wpbc-settings#wpbc_general_settings_advanced_metabox', key: 'wpbc_be3_' + cid_key }, 'on this page' )
												, ', '
												, el( 'span', { key: 'wpbc_be4_' + cid_key }, wp.i18n.__( 'at this option' ) )
												, ': "'
												, el( 'strong', { key: 'wpbc_be5_' + cid_key }, wp.i18n.__( 'URL to edit bookings' ) )
												, '".'
											)
							  	 } );
			rows_in_content.push( { block_text:
										el( 'div', { style: { marginTop: '20px' }, key: 'wpbc_be6_' + cid_key }
												, el( 'strong', { key: 'wpbc_be7_' + cid_key }, wp.i18n.__( 'Important!' ) )
												, ' '
												, el( 'span', { key: 'wpbc_be8_' + cid_key }, wp.i18n.__( 'You can not open this page directly. Please, use links in ' ) )
												, ' '
												, el( 'a', { href: 'admin.php?page=wpbc-settings&tab=email' , key: 'wpbc_be9_' + cid_key }, 'email' )
												, '.'
											)
							  	 } );
			rows_in_content.push( { block_text:
										el( 'div', { key: 'wpbc_be10_' + cid_key }
												, el( 'span', { key: 'wpbc_be11_' + cid_key }, wp.i18n.__( 'If you open this page directly, then you will see this error' ) )
												, ': "'
												, el( 'strong', { key: 'wpbc_be12_' + cid_key }, wp.i18n.__( 'You do not set any parameters for booking editing' ) )
												, '".'
											)
							  	 } );
			return rows_in_content;
		}


	/**
	 *  Generate Visual Preview Block of Customer Bookings Listing - system shortcode
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingcustomerlisting( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
		};

		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Customer Bookings Listing' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingcustomerlisting( props, 'body_' + params[ 'cid_key' ] ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingcustomerlisting' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End
	}


		/**
		 * Parse parameters into array of rows objects for showing in conetnt of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingcustomerlisting( props, cid_key ){

			var el = wp.element.createElement;

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			rows_in_content.push( { block_text: wp.i18n.__( 'Visitors of your website, can view previous (own) bookings, by clicking on secret link in email, which is sending after booking created.' ) } );

			rows_in_content.push( {	block_text:

										el( 'div', { key: 'wpbc_bcl1_' + cid_key }
												, el( 'span', { key: 'wpbc_bcl2_' + cid_key }, wp.i18n.__( 'Link to this page must be defined' ) )
												, ' '
												, el( 'a', { href: 'admin.php?page=wpbc-settings#wpbc_general_settings_advanced_metabox' , key: 'wpbc_bcl2_2_' + cid_key }, 'on this page' )
												, ', '
												, el( 'span', { key: 'wpbc_bcl3_' + cid_key }, wp.i18n.__( 'at this option' ) )
												, ': "'
												, el( 'strong', { key: 'wpbc_bcl4_' + cid_key }, wp.i18n.__( 'URL of page for customer bookings listing' ) )
												, '".'
											)
							  	 } );
			rows_in_content.push( { block_text:
										el( 'div', { style: { marginTop: '20px' }, key: 'wpbc_bcl5_' + cid_key }
												, el( 'strong', { key: 'wpbc_bcl6_' + cid_key }, wp.i18n.__( 'Important!' ) )
												, ' '
												, el( 'span', { key: 'wpbc_bcl7_' + cid_key }, wp.i18n.__( 'You can not open this page directly. Please, use links in ' ) )
												, ' '
												, el( 'a', { href: 'admin.php?page=wpbc-settings&tab=email' , key: 'wpbc_bcl8_' + cid_key }, 'email' )
												, '.'
											)
							  	 } );
			rows_in_content.push( { block_text:
										el( 'div', { key: 'wpbc_bcl9_' + cid_key }
												, el( 'span', { key: 'wpbc_bcl10_' + cid_key }, wp.i18n.__( 'If you open this page directly, then you will see this error' ) )
												, ': "'
												, el( 'strong', { key: 'wpbc_bcl11_' + cid_key }, wp.i18n.__( 'You do not set any parameters for booking editing' ) )
												, '".'
											)
							  	 } );
			return rows_in_content;
		}




	/**
	 *  Generate Visual Preview Block of Showing booking resource Info
	 *
	 * @param shortcode_obj	- shortcode  JavaScript obj.
	 * @returns
	 */
	function wpbc_gt_get_visual_block_for_bookingresource( shortcode_obj, params ){

		// Booking Form Parameters
		var shortcode_defaults = {
			type              : 1,
			show         	  : 'title'
		};


		var props = _.defaults( shortcode_obj.attrs.named, shortcode_defaults );

		var el = wp.element.createElement;

		//FixIn: 8.7.3.18 Start
		var inner_header = el( 'div', {className: 'wpbc_gb_block_preview_inner_header', key: 'header_' + params[ 'cid_key' ]}
									, wpbc_gb_tpl_header( { header: wp.i18n.__( 'Show Info of Booking Resource' ), cid_key: 'header_' + params[ 'cid_key' ] } )
							);
		var inner_body   = el( 'div', {className: 'wpbc_gb_block_preview_inner_body', key: 'body_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_shortcode_parameters( wpbc_parse_params_into_rows_arr_for_bookingresource( props ), { cid_key: 'body_' + params[ 'cid_key' ] } )
							);
		var inner_footer = el( 'div', {className: 'wpbc_gb_block_preview_inner_footer' , key: 'footer_' + params[ 'cid_key' ] }
									, wpbc_gb_tpl_footer( { shortcode_in_text: params[ 'shortcode_in_text' ], cid_key: 'footer_' + params[ 'cid_key' ] } )
							);

		return  el( 'div', { className: 'wpbc_gb_block_shortcode_preview_wrapper wpbc_gb_block_preview_bookingresource' , key: 'preview_wrapper_' + params[ 'cid_key' ] }

						, el( 'div', { className: 'wpbc_gb_block_shortcode_preview_content' , key: 'preview_content_' + params[ 'cid_key' ] }
								, [ inner_header, inner_body ]
							)
						, inner_footer
				);
		//FixIn: 8.7.3.18 End
	}


		/**
		 * Parse parameters into array of rows objects for showing in content of block
		 *
		 * @param props
		 */
		function wpbc_parse_params_into_rows_arr_for_bookingresource( props ){

			// Parameters Description /////////////////////////////////
			var rows_in_content = [];
			if ( undefined != props[ 'type' ] ){
				rows_in_content.push( { name: wp.i18n.__( 'Booking resource' ), value: ' ID = ' + props[ 'type' ] } );
			}
			if ( undefined != props[ 'show' ] ) {
				rows_in_content.push( { name: wp.i18n.__( 'Show' ), value: props[ 'show' ] } );
				/*
				if ( 'title' == props[ 'show' ] ) {
					rows_in_content.push( { name: wp.i18n.__( 'Show' ), value: props[ 'show' ] } );
				}
				if ( 'cost' == props[ 'show' ] ) {
					rows_in_content.push( { name: wp.i18n.__( 'Show' ), value: props[ 'show' ] } );
				}
				if ( 'capacity' == props[ 'show' ] ) {
					rows_in_content.push( { name: wp.i18n.__( 'Show' ), value: props[ 'show' ] } );
				}
				*/
			}
			return rows_in_content;
		}



// Templates ///////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Header template for Block Preview
 *
 * @param props - object of parameters
 * @returns array of createElements - react elements
 */
function wpbc_gb_tpl_header( props ){

	var el = wp.element.createElement;
																		//FixIn: 8.7.3.18
	return [
		el( 'h3',  {className: 'wpbc_gb_block_preview_inner_title_text', key: 'h3header_' + props[ 'cid_key' ] }, props.header  ),

		el( 'a',   {className: 'wpbc_gb_block_preview_inner_title_edit', key: 'a_clickedit_' + props[ 'cid_key' ] }, wp.i18n.__( 'Click to edit' ) ),

		el( 'div', {className: 'wpbc_gb_block_preview_inner_title_desc', key: 'div_notreal_' + props[ 'cid_key' ] }, wp.i18n.__( 'This is not real preview. Its configuration block of "Booking Calendar".' ) )
	];
}


/**
 * Parameters template for shortcode params in Body of Block Preview
 *
 * @param props - array of objects of parameters [ {name: 'title', value: 'data'}, ... ]
 * @returns array of createElements - react elements
 */
function wpbc_gb_tpl_shortcode_parameters( props , params ){			//FixIn: 8.7.3.18

	var el = wp.element.createElement;

	var shortcode_parameters_arr = [];

	var propsLength = props.length;

	var cid_key = params[ 'cid_key' ];	//FixIn: 8.7.3.18
	for ( var i = 0; i < propsLength; i++ ){

		cid_key = 'internal' + i + params[ 'cid_key' ];

		if ( undefined != props[i]['block_text'] ) {

			shortcode_parameters_arr.push(
				el( 'div', {className: 'wpbc_gb_block_preview_inner_params_row', key: 'div_text' + cid_key }
					, el( 'span', { key: 'inner_params_row_span' + cid_key }, props[ i ]['block_text'] )
				)
			);

		}

		if ( ( undefined != props[i]['name'] ) && ( undefined != props[i]['value'] )  ) {

			shortcode_parameters_arr.push(
				el( 'div', {className: 'wpbc_gb_block_preview_inner_params_row', key: 'div_name' + cid_key }
					, el( 'strong', { key: 'strong_name' + cid_key }, 	props[ i ].name )
					, el( 'span', 	{ key: 'span_name' + cid_key }, 	': ' )
					, el( 'em', 	{ key: 'em_value' + cid_key }, 		props[ i ].value )
				)
			);
		}
	}

	return shortcode_parameters_arr;
}


/**
 * Header template for Block Preview
 *
 * @param props - object of parameters
 * @returns array of createElements - react elements
 */
function wpbc_gb_tpl_footer( props ){

	var el = wp.element.createElement;
																				//FixIn: 8.7.3.18
	return [
				el( 'div', { className: 'wpbc_gb_block_preview_inner_shortcode', key: 'div_foot_' + props[ 'cid_key' ] }, props.shortcode_in_text )
	       ];
}