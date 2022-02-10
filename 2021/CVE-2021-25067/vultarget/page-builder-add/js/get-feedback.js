/*
Code Snippet from POST SMTP : https://wordpress.org/plugins/post-smtp/
License : GPL V2
*/
jQuery(document).ready(function($) {

	$( '#pluginops-plugin-deactivate-link' ).on('click',function(e) {
		e.preventDefault();

		var reason = $( '#POPB_feedback_form_container .POPB-reason' ),
			deactivateLink = $( this ).attr( 'href' );

	    $( "#POPB_feedback_form_container" ).dialog({
	    	title: 'PluginOps Feedback Form',
	    	dialogClass: '#POPB_feedback_form-form',
	      	resizable: false,
	      	minWidth: 400,
	      	minHeight: 300,
	      	modal: true,
	      	buttons: {	
	      		'go' : {
		        	text: 'Continue',
		        	id: 'POPB_feedback_form_go',
					class: 'button',
		        	click: function() {

		        		var dialog = $(this),
		        			go = $('#POPB_feedback_form_go'),
		          			form = dialog.find( 'form' ).serializeArray(),
							result = {};

						$.each( form, function() {
							if ( '' !== this.value )
						    	result[ this.name ] = this.value;										
						});

						if ( ! jQuery.isEmptyObject( result ) ) {
							result.action = 'popb_send_user_feedback';
						    $.ajax({
						        url: POPB_feedback_URL.admin_ajax,
						        type: 'POST',
						        data: result,
						        error: function(){},
						        success: function(msg){},
						        beforeSend: function() { 
						        	go.addClass('POPB-ajax-progress'); 
						        },
						        complete: function() { 
						        	go.removeClass('POPB-ajax-progress'); 
			        	
						        	dialog.dialog( "close" );
						            location.href = deactivateLink;
						        }
						    });		
	
						}


		        	},				      			
	      		},
	      		'cancel' : {
		        	text: 'Cancel',
		        	id: 'POPB_feedback_form-cancel',
		        	class: 'button button-primary',
		        	click: function() {
		          		$( this ).dialog( "close" );
		        	}				      			
	      		},
	      		'skip' : {
		        	text: 'Skip',
		        	id: 'POPB_feedback_form-skip',
		        	click: function() {
		          		$( this ).dialog( "close" );

		          		location.href = deactivateLink;
		        	}				      			
	      		},		      		
	      	}
	    });

		reason.change(function() {
			$( '.POPB-reason-input' ).hide();

			if ( $( this ).hasClass( 'POPB-custom-input' ) ) {
				$( '#POPB-deactivate-reasons' ).next( '.POPB-reason-input' ).show();
			}

			if ( $( this ).hasClass( 'POPB-support-input' ) ) {
				$( this ).find( '.POPB-reason-input' ).show();
			}			
		});
				    
	});
});