/**
 * CashCred Withdraw
 * @since 2.0
 * @version 1.0
 */
jQuery(function($){

	$(document).ready(function() {



		if ( $( "#cashcred_pay_method" ).length ) {
			exchange_calculation();
			display_cashcred_gateway_notice();
		}
		 
		$( "#cashcred_point_type" ).change(function() {
			exchange_calculation();
		});
		 
		$( "#cashcred_pay_method" ).change(function() {
			exchange_calculation();
			display_cashcred_gateway_notice();
		});

		$( '.mycred-cashcred-form' ).on( 'keyup change', '#withdraw_points', function( e ){
  
  			exchange_calculation();
			
		});

		
		$( 'body' ).on( 'submit', '.mycred-cashcred-form', function( e ){

	

			withdraw_points = $( "#withdraw_points" ).val();
			if( parseFloat( withdraw_points ) <= 0 ) {
				e.preventDefault();
			}
		});

		$('.cashcred-nav-tabs li').click( function(){
			var id = $(this).attr('id');
			$('.cashcred-nav-tabs li').removeClass('active');
			$('.cashcred-tab').hide();
			$(this).addClass('active');           
			$('#'+ id + 'c').show();
		});

		$('.cashcred-tab').hide();
		$('#tab1c').show();

		var elementType = $('#cashcred_save_settings').prop('nodeName');

		if ( elementType != 'INPUT' ) {
			first_tab_active = $("#cashcred_save_settings option:first").val();
			$('.cashcred_panel').hide();
			$('#panel_'+first_tab_active).show();
		}
		 
		$("select#cashcred_save_settings").change( function(){
			id = $(this).val();
			$('.cashcred_panel').hide();
			$('#panel_'+id).show();
		});
	
	});
		
	function exchange_calculation(){
		
		cashcred_point_type = $( "#cashcred_point_type" ).val();
		cashcred_pay_method = $( "#cashcred_pay_method" ).val();
		withdraw_points     = $( "#withdraw_points" ).val();
		
		currency_code   = cashcred.exchange[cashcred_pay_method].currency;
		conversion_rate = cashcred.exchange[cashcred_pay_method].point_type[cashcred_point_type];
		
		min = cashcred.exchange[cashcred_pay_method].min;
		max = cashcred.exchange[cashcred_pay_method].max
					 
		amount = withdraw_points * conversion_rate;
		
		$( "#withdraw_points" ).attr({"max" : max , "min" : min });

		$( '.cashcred-min span' ).html( min );
		
		$( "#cashcred_currency_symbol" ).html(currency_code);
		$( "#cashcred_total_amount" ).html(amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		
	}

	function display_cashcred_gateway_notice() {
		
		if ( cashcred.gateway_notices[ $('#cashcred_pay_method').val() ] ) {

			$('.cashcred_gateway_notice').show();

		}
		else {

			$('.cashcred_gateway_notice').hide();

		}
		
	}





});




 
