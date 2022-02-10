jQuery(document).ready(function($) {
 	$(function(){
    		$( "#sortable1, #sortable2" ).sortable({
			cursor: 'move',
      			connectWith: ".connectedSortable"
    		}).disableSelection();
  	} );

	if ($( "#woosea-progress-table" ).hasClass( "progress-bar" )) {

		$(function() {
    			var progressbar = $( "#progressbar" ),
      			progressLabel = $( ".progress-label" );

			var project_hash = $("#project_hash").val();
 
    			progressbar.progressbar({
      				value: false,
      				change: function() {
        			progressLabel.text( progressbar.progressbar( "value" ) + "%" );
      			},
      
			complete: function() {
        			progressLabel.text( "100%, done!" );
      			}
		});
 
	    	function progress() {
			var val = progressbar.progressbar( "value" ) || 0;

	     		jQuery.ajax({
                        	method: "POST",
                        	url: ajaxurl,
                        	data: { 'action': 'woosea_progress_bar', 'project_hash': project_hash }
                	})
                	.done(function( data ) {	
				data = JSON.parse( data );
				data = parseInt(data);
		
     				progressbar.progressbar( "value", data );

   	   			if ( data < 99 ) {
        				setTimeout( progress, 80 );
      				} else {
					$( '#woosea-progress-table').append('<tr><td colspan="2">Jippie gelukt</td></tr>');
				}
			})
                	.fail(function( data ) {
                        	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                	});
    	
		}
    		setTimeout( progress, 2000 );
  		});
	}
});


