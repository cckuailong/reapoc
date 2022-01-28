// BPS MScan AJAX
// CAUTION: The AJAX post object/url: $.post(bps_mscan_ajax.ajaxurl... is different than BPS Pro.
// This simply means that the AJAX url is different in the BPS free (wp_localize_script call). All other actions/code is the same.
jQuery(document).ready( function($) {
	
	// MScan Malware Scanner: Start. MScan Status: 1
	// MScan Stop is handled in mscan-ajax-functions.php by using a PHP file contents check: /bps-backup/master-backups/mscan-stop.txt
	$( "input#bps-mscan-start-button" ).on({ "click": function() { 
	
		var data = {
			action: 'bps_mscan_scan_processing', 
			post_var: 'bps_mscan'
		};

		$.post(bps_mscan_ajax.ajaxurl, data, function(response) {
		// Object {action: "bps_mscan_scan_processing", post_var: "bps_mscan"}
		//console.log( data );
	 	});	
		console.log( "clicked!" ); 
	},
	"mouseover": function() { 
		console.log( "hovered!" );
	}
	});

	// MScan Malware Scanner: Scan Time Estimate Tool. MScan Status: 5
	$( "input#bps-mscan-time-estimate-button" ).on({ "click": function() { 
	
		var data = {
			action: 'bps_mscan_scan_estimate', 
			post_var: 'bps_mscan_estimate'
		};

		$.post(bps_mscan_ajax.ajaxurl, data, function(response) {
		// Object {action: "bps_mscan_scan_estimate", post_var: "bps_mscan_estimate"}
		//console.log( data );
	 	});	
		console.log( "clicked!" ); 
	},
	"mouseover": function() { 
		console.log( "hovered!" );
	}
	});
});