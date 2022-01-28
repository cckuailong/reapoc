<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>MScan Scan Status</title>

<style>
body {background:white}
html.wp-toolbar{padding:0px}
#wpcontent{margin-left:0px}
#wpadminbar{display:none}
#adminmenuback{display:none}
#adminmenuwrap{display:none}
#footer-thankyou{display:none}
div#wpfooter{display:none}
div#bps-inpage-message{display:none}
div.update-nag{display:none}
div.notice{display:none}
div#bps-status-display{display:none}
div#query-monitor-main{visibility:hidden}
div#MScan-Time-Container {z-index:999999999;position:relative;top:0px;left:0px;background-color:#fff}
div#mscantimer {z-index:999999999;color:#000;font-size:13px!important;font-weight:600!important;line-height:18px;padding:4px 5px 0px 0px;position:relative;top:0px;left:0px;}
#MscanProgressBar {z-index:999999999;position:relative;top:0px;left:0px;width:98%;height:25px;background-color:#e8e8e8;border-radius:2px;-webkit-box-shadow:inset 0 2px 3px rgba(0, 0, 0, 0.25);-moz-box-shadow:inset 0 2px 3px rgba(0, 0, 0, 0.25);box-shadow:inset 0 2px 3px rgba(0, 0, 0, 0.25);}
#MscanBar {z-index:999999999;width:0%;height:25px;font-size:12px!important;font-weight:600!important;text-align:center;line-height:25px;color:white;}
.mscan-progress-bar {z-index:999999999;width:0;height:100%;background:#0e8bcb;background:-moz-linear-gradient(top, #0e8bcb 0%, #08496b 100%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,#0e8bcb), color-stop(100%,#08496b));background:-webkit-linear-gradient(top, #0e8bcb 0%,#08496b 100%);background:-o-linear-gradient(top, #0e8bcb 0%,#08496b 100%);background:-ms-linear-gradient(top, #0e8bcb 0%,#08496b 100%);background:linear-gradient(to bottom, #0e8bcb 0%,#08496b 100%);-webkit-transition:width 1s ease-in-out;-moz-transition:width 1s ease-in-out;-o-transition:width 1s ease-in-out;transition:width 1s ease-in-out;}

@media screen and (min-width: 280px) and (max-width: 1043px){
div#bps-status-display{display:none}
}
@media screen and (min-width: 280px) and (max-width: 960px){
div#wpadminbar{display:none}
div#adminmenu, div#adminmenu .wp-submenu, div#adminmenuback, div#adminmenuwrap{display:none}
}
</style>

<script type="text/javascript">
<!--
function AutoRefreshOnce( m ) {
	   
	// The hash is not seen on initial page load, but is seen after the first reload.
	if ( !window.location.hash ) {
		window.location = window.location + '#loaded';
		setTimeout( "location.reload(true);", m );
    }
}
//-->
</script>
</head>

<body onload="JavaScript:AutoRefreshOnce(1000);">
<?php
	// don't add any commented out CSS code in the CSS code above - The commented out code will still be processed.
	// Note if someone is displaying the BPS Pro status display in BPS plugin pages only it throws off the iframe section Don't adjust for that and don't use any CSS
	// because it causes other problems.
	
	// A typical site will load wp-load.php using $wp_load_file6.
	// The conditions need to check for the nearest wp-load.php file to load the correct file for the site.
	if ( ! function_exists( 'get_option' ) ) {
		$wp_load_file1 = dirname(__FILE__) . '/wp-load.php';
		$wp_load_file2 = dirname(dirname(__FILE__)) . '/wp-load.php';
		$wp_load_file3 = dirname(dirname(dirname(__FILE__))) . '/wp-load.php';
		$wp_load_file4 = dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';
		$wp_load_file5 = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php';
		$wp_load_file6 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/wp-load.php';
		$wp_load_file7 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/wp-load.php';		
		$wp_load_file8 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))) . '/wp-load.php';		
		$wp_load_file9 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))) . '/wp-load.php';		
		$wp_load_file10 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))))) . '/wp-load.php';		
		$wp_load_file11 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))))))) . '/wp-load.php';		
		$wp_load_file12 = dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))))))) . '/wp-load.php';		

		if ( file_exists( $wp_load_file1 ) ) {
			require_once $wp_load_file1;		
		} elseif ( file_exists( $wp_load_file2 ) ) {
			require_once $wp_load_file2;		
		} elseif ( file_exists( $wp_load_file3 ) ) {
			require_once $wp_load_file3;
		} elseif ( file_exists( $wp_load_file4 ) ) {
			require_once $wp_load_file4;	
		} elseif ( file_exists( $wp_load_file5 ) ) {		
			require_once $wp_load_file5;		
		} elseif ( file_exists( $wp_load_file6 ) ) {
			require_once $wp_load_file6;
		} elseif ( file_exists( $wp_load_file7 ) ) {
			require_once $wp_load_file7;
		} elseif ( file_exists( $wp_load_file8 ) ) {
			require_once $wp_load_file8;
		} elseif ( file_exists( $wp_load_file9 ) ) {
			require_once $wp_load_file9;
		} elseif ( file_exists( $wp_load_file10 ) ) {
			require_once $wp_load_file10;		
		} elseif ( file_exists( $wp_load_file11 ) ) {
			require_once $wp_load_file11;		
		} elseif ( file_exists( $wp_load_file12 ) ) {
			require_once $wp_load_file12;		
		} else {
			echo '<strong><font color="#fb0101">BPS cannot find and load the WordPress wp-load.php file. MScan cannot be used on this website until that problem is fixed.</font></strong>';
			exit();
		}
	}

// This function is executed in the js below after the actual scan has completed with MScan Status value: 3 or 5.
// IMPORTANT: Do not echo anything directly in this function. It will break the js timer.
function bpsPro_mscan_completed() {

	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	$MScan_options = get_option('bulletproof_security_options_MScan');
	$mstime = ! isset($MScan_options['mscan_max_time_limit']) ? '' : $MScan_options['mscan_max_time_limit'];
	ini_set('max_execution_time', $mstime);	 

	if ( isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '3' || isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '5' ) {
	 
		$MScan_status_db = array( 
		'bps_mscan_time_start' 					=> $MScan_status['bps_mscan_time_start'],  
		'bps_mscan_time_stop' 					=> $MScan_status['bps_mscan_time_stop'], 
		'bps_mscan_time_end' 					=> $MScan_status['bps_mscan_time_end'],  
		'bps_mscan_time_remaining' 				=> $MScan_status['bps_mscan_time_remaining'],
		'bps_mscan_status' 						=> '4', 
		'bps_mscan_last_scan_timestamp' 		=> $MScan_status['bps_mscan_last_scan_timestamp'], 
		'bps_mscan_total_time' 					=> $MScan_status['bps_mscan_total_time'], 
		'bps_mscan_total_website_files' 		=> '', 
		'bps_mscan_total_wp_core_files' 		=> $MScan_status['bps_mscan_total_wp_core_files'], 
		'bps_mscan_total_non_image_files' 		=> $MScan_status['bps_mscan_total_non_image_files'], 
		'bps_mscan_total_image_files' 			=> '', 
		'bps_mscan_total_all_scannable_files' 	=> $MScan_status['bps_mscan_total_all_scannable_files'], 
		'bps_mscan_total_skipped_files' 		=> $MScan_status['bps_mscan_total_skipped_files'], 
		'bps_mscan_total_suspect_files' 		=> $MScan_status['bps_mscan_total_suspect_files'], 
		'bps_mscan_suspect_skipped_files' 		=> $MScan_status['bps_mscan_suspect_skipped_files'], 
		'bps_mscan_total_suspect_db' 			=> $MScan_status['bps_mscan_total_suspect_db'], 
		'bps_mscan_total_ignored_files' 		=> $MScan_status['bps_mscan_total_ignored_files'],
		'bps_mscan_total_plugin_files' 			=> $MScan_status['bps_mscan_total_plugin_files'], 			 
		'bps_mscan_total_theme_files' 			=> $MScan_status['bps_mscan_total_theme_files'] 
		);		
		
		foreach( $MScan_status_db as $key => $value ) {
			update_option('bulletproof_security_options_MScan_status', $MScan_status_db);
		}	 
	}
}

	$MScan_status = get_option('bulletproof_security_options_MScan_status');
	$MScan_options = get_option('bulletproof_security_options_MScan');

	$mscan_start_time = ! isset($MScan_status['bps_mscan_time_start']) ? '' : $MScan_status['bps_mscan_time_start']; 
	$mscan_time_stop = ! isset($MScan_status['bps_mscan_time_stop']) ? '' : $MScan_status['bps_mscan_time_stop'];
	$mscan_future_time = ! isset($MScan_status['bps_mscan_time_remaining']) ? '' : $MScan_status['bps_mscan_time_remaining'];
	$mscan_status = ! isset($MScan_status['bps_mscan_status']) ? '' : $MScan_status['bps_mscan_status'];
	$mscan_timestamp = ! isset($MScan_status['bps_mscan_last_scan_timestamp']) ? '' : $MScan_status['bps_mscan_last_scan_timestamp'];
	$mscan_total_time = ! isset($MScan_status['bps_mscan_total_time']) ? '' : $MScan_status['bps_mscan_total_time'];	
	$mscan_suspect_files = ! isset($MScan_status['bps_mscan_total_suspect_files']) ? '' : $MScan_status['bps_mscan_total_suspect_files'];
	$mscan_suspect_skipped_files = ! isset($MScan_status['bps_mscan_suspect_skipped_files']) ? '' : $MScan_status['bps_mscan_suspect_skipped_files'];	
	$mscan_suspect_db = ! isset($MScan_status['bps_mscan_total_suspect_db']) ? '' : $MScan_status['bps_mscan_total_suspect_db'];
	$mscan_skipped_files = ! isset($MScan_status['bps_mscan_total_skipped_files']) ? '' : $MScan_status['bps_mscan_total_skipped_files']; 

	if ( isset($MScan_options['mscan_scan_skipped_files']) && $MScan_options['mscan_scan_skipped_files'] == 'On' ) {
		$mscan_total_files = $MScan_status['bps_mscan_total_skipped_files'];
		$skipped_scan = 1;
	} else {
		$mscan_total_files = ! isset($MScan_status['bps_mscan_total_all_scannable_files']) ? '' : $MScan_status['bps_mscan_total_all_scannable_files'];
		$skipped_scan = 0;
	}

	if ( isset($MScan_options['mscan_scan_database']) && $MScan_options['mscan_scan_database'] == 'On' ) {
		$mscan_db_scan = 1;
	} else {
		$mscan_db_scan = 0;
	}


if ( isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '2' || isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '3' || isset($MScan_status['bps_mscan_status']) && $MScan_status['bps_mscan_status'] == '5' ) { ?>

<div id="MscanProgressBar">
  	<div id="MscanBar" class="mscan-progress-bar"></div>
</div>

<?php } ?>

<div id="MScan-Time-Container">
	<div id="mscantimer"></div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
	var currentTimeI = new Date().getTime() / 1000;
	var futureTimeI = <?php echo json_encode( $mscan_future_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var scanStartI = <?php echo json_encode( $mscan_start_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var mscanStatusI = <?php echo json_encode( $mscan_status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var timeStampI = <?php echo json_encode( $mscan_timestamp, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var totalScanTimeI = <?php echo json_encode( $mscan_total_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var totalFilesI = <?php echo json_encode( $mscan_total_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var skippedFilesI = <?php echo json_encode( $mscan_skipped_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var skippedScanI = <?php echo json_encode( $skipped_scan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var dbScanI = <?php echo json_encode( $mscan_db_scan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var suspectI = <?php echo json_encode( $mscan_suspect_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var suspectSkipI = <?php echo json_encode( $mscan_suspect_skipped_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var suspectDBI = <?php echo json_encode( $mscan_suspect_db, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;

	var timeRemainingI = futureTimeI - currentTimeI;
	var minuteI = 60;
	var hourI = 60 * 60;
	var dayI = 60 * 60 * 24;
	var dayFloorI = Math.floor(totalScanTimeI / dayI);
	var hourFloorI = Math.floor((totalScanTimeI - dayFloorI * dayI) / hourI);
	var minuteFloorI = Math.floor((totalScanTimeI - dayFloorI * dayI - hourFloorI * hourI) / minuteI);
	var secondFloorI = Math.floor((totalScanTimeI - dayFloorI * dayI - hourFloorI * hourI - minuteFloorI * minuteI));
	var hourFloorFI = ("0" + hourFloorI).slice(-2);	
	var minuteFloorFI = ("0" + minuteFloorI).slice(-2);	
	var secondFloorFI = ("0" + secondFloorI).slice(-2);

	// 1 = On | 0 = Off or 0 in the case of Total Files or Suspect Files | blank value = 0|Off

	if ( totalFilesI == "" ) {
		totalFilesI = 0;
	}

	if ( skippedFilesI == "" ) {
		skippedFilesI = 0;
	}

	if ( suspectI == "" ) {
		suspectI = 0;
	}

	if ( suspectSkipI == "" ) {
		suspectSkipI = 0;
	}

	if ( suspectDBI == "" ) {
		suspectDBI = 0;
	}

	// This condition prevents displaying the previous scan results if the scan finishes very quickly.
	if ( mscanStatusI == 4 && futureTimeI > currentTimeI ) {
		document.getElementById("mscantimer").innerHTML = "The Scan Completed Before The Scan Estimate Time Finished. Reload/Refresh The MScan Page For Scan Results.<br />Note: For best scan results reload/refresh the MScan page before running a new scan.";
		console.log( "Status: 4 : Future Time > Time : Scan Completed Before The Scan Estimate Time Finished" );

	} else {

		if ( mscanStatusI == 4 && skippedScanI == 0 ) {
			
			if ( dbScanI == 1 ) {		
				document.getElementById("mscantimer").innerHTML = "Scan Completed [" + timeStampI + "] : Total Scan Time: "  + hourFloorFI + ":" + minuteFloorFI + ":" + secondFloorFI + " : Total Files Scanned: " + totalFilesI + " : Skipped Files: " + skippedFilesI + " : Suspicious Files: " + suspectI + " : Suspicious DB Entries: " + suspectDBI + "<br />" + "To view the detailed Scan Report click the View Report button below. Please view the Scan Report before clicking the Suspicious Files and DB Entries accordion tabs below.";
				window.opener.location.reload();
				console.log( "Status: 4 : Future Time < Time : Skipped Files: Off : DB Scan: On" );			
			
			} else {
				
				document.getElementById("mscantimer").innerHTML = "Scan Completed [" + timeStampI + "] : Total Scan Time: "  + hourFloorFI + ":" + minuteFloorFI + ":" + secondFloorFI + " : Total Files Scanned: " + totalFilesI + " : Skipped Files: " + skippedFilesI + " : Suspicious Files: " + suspectI + "<br />" + "To view the detailed Scan Report click the View Report button below. Please view the Scan Report before clicking the Suspicious Files and DB Entries accordion tabs below.";
				window.opener.location.reload();
				console.log( "Status: 4 : Future Time < Time : Skipped Files: Off : DB Scan: Off" );
			}
		}
		
		if ( mscanStatusI == 4 && skippedScanI == 1 ) {
			document.getElementById("mscantimer").innerHTML = "Skipped File Scan Completed [" + timeStampI + "] : Total Scan Time: "  + hourFloorFI + ":" + minuteFloorFI + ":" + secondFloorFI + " : Total Files Scanned: " + totalFilesI + " : Suspicious Files: " + suspectSkipI + "<br />" + "To view the detailed Scan Report click the View Report button below. Please view the Scan Report before clicking the Suspicious Files and DB Entries accordion tabs below.";
			window.opener.location.reload();
			console.log( "Status: 4 : Future Time < Time : Skipped Files: On : DB Scan: NA" );		
		}
	}
	
var MScan = setInterval(function(){ MScanTimer() }, 1000);

function MScanTimer() {

	var currentTime = new Date().getTime() / 1000;
	var futureTime = <?php echo json_encode( $mscan_future_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var scanStart = <?php echo json_encode( $mscan_start_time, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var scanStop = <?php echo json_encode( $mscan_time_stop, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var totalFiles = <?php echo json_encode( $mscan_total_files, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;
	var mscanStatus = <?php echo json_encode( $mscan_status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?>;	

	var timeRemaining = futureTime - (currentTime - 10); // - 10 forces the scan time caculation to appear to end earlier. Just a visual gimmick.
	var timeRemainingTE = futureTime - (currentTime + 30); // + 30 second countdown used in Estimated Scan Time Exceeded. Just a visual gimmick.
	var timeRemainingPB = futureTime - currentTime;
	
	var minute = 60;
	var hour = 60 * 60;
	var day = 60 * 60 * 24;
	
	// Right to left direction decrease - 100% to 0% width and used in the pBarPercentWidthIncrease math below - don't comment this var out.
	var pBarPercentWidthDecrease = Math.round(timeRemainingPB/(futureTime - scanStart) * 100);
	// Left to right direction increase - 0% to 100% width
	var pBarPercentWidthIncrease = 100 - pBarPercentWidthDecrease;

	var dayFloor = Math.floor(timeRemaining / day);
	var hourFloor = Math.floor((timeRemaining - dayFloor * day) / hour);
	var minuteFloor = Math.floor((timeRemaining - dayFloor * day - hourFloor * hour) / minute);
	var secondFloor = Math.floor((timeRemaining - dayFloor * day - hourFloor * hour - minuteFloor * minute));
	var hourFloorF = ("0" + hourFloor).slice(-2);	
	var minuteFloorF = ("0" + minuteFloor).slice(-2);	
	var secondFloorF = ("0" + secondFloor).slice(-2);

	var dayFloorPB = Math.floor(timeRemainingPB / day);
	var hourFloorPB = Math.floor((timeRemainingPB - dayFloorPB * day) / hour);
	var minuteFloorPB = Math.floor((timeRemainingPB - dayFloorPB * day - hourFloorPB * hour) / minute);
	var secondFloorPB = Math.floor((timeRemainingPB - dayFloorPB * day - hourFloorPB * hour - minuteFloorPB * minute));
	var hourFloorFPB = ("0" + hourFloorPB).slice(-2);	
	var minuteFloorFPB = ("0" + minuteFloorPB).slice(-2);	
	var secondFloorFPB = ("0" + secondFloorPB).slice(-2);

	var dayFloorTE = Math.floor(timeRemainingTE / day);
	var hourFloorTE = Math.floor((timeRemainingTE - dayFloorTE * day) / hour);
	var minuteFloorTE = Math.floor((timeRemainingTE - dayFloorTE * day - hourFloorTE * hour) / minute);
	var secondFloorTE = Math.floor((timeRemainingTE - dayFloorTE * day - hourFloorTE * hour - minuteFloorTE * minute));
	var hourFloorFTE = ("0" + hourFloorTE).slice(-2);	
	var minuteFloorFTE = ("0" + minuteFloorTE).slice(-2);	
	var secondFloorFTE = ("0" + secondFloorTE).slice(-2);

	var ScanCompleted = "<?php bpsPro_mscan_completed(); ?>";
	
	// IMPORTANT: Reloading the page during any Progress bar conditions breaks the display of the Progress bar.
	// Scan times can vary significantly depending on caching and other factors. The visual stuff is primarily for letting someone know things are still happening.
	// The only time a scan will not complete successfully will be if the mscan-ajax-functions.php file functions fail for some reason. ie folder/Ownership, etc. problem.
	// MScan Status 1 is set when the Start button is clicked and means the scan estimate function is being processed. Has an AJAX action.
	// MScan Status 2 is set at the end of the scan estimate function and means the file scanning function and other functions are still being processed.
	// MScan Status 3 is set at the end of the file scanning function and means all scanning is completed.
	// MScan Status 3 Process the ScanCompleted var, which executes the PHP bpsPro_mscan_completed() function.
	// MScan Status 4 is set when the MScan Stop button is clicked and on Scan Completion.
	// MScan Status 4 is a "resting/completed" state that displays the scan results.
	// MScan Status 5 is set when the Scan Time Estimate Tool button is clicked. Has an AJAX action. 1 > 5 > 4. No longer used.
	// futureTime is the current time + the scan estimate time total (time remaining).
	// A typical/average file scan range is: 3,000 to 8,000 files.

	if ( futureTime > currentTime ) {
		
		if ( mscanStatus == 1 && secondFloorF <= 10 ) {
			window.location.reload(true);
			console.log( "Status: 1 : Future Time > Time : secondFloor <= 10 : " + secondFloorF );		
		}
		
		if ( mscanStatus == 1 && secondFloorF > 9 ) {
			document.getElementById("mscantimer").innerHTML = "Calculating Scan Time: " + hourFloorF + ":" + minuteFloorF + ":" + secondFloorF + "<br />" + "You can leave the MScan page while a scan is in progress and the scan will continue until it is completed.";
			console.log( "Status: 1 : Future Time > Time : Calculating Scan Time : secondFloorF > 9 : " + secondFloorF );			
		}

		// Removing the status 5 condition: mscanStatus == 5 && totalFiles != "". Status 5 is no longer used.
		if ( mscanStatus == 2 && totalFiles != "" || mscanStatus == 3 && totalFiles != ""  ) {	
			document.getElementById("MscanBar").style.width = pBarPercentWidthIncrease + '%';
			document.getElementById("MscanBar").innerHTML = pBarPercentWidthIncrease + '%';
			document.getElementById("mscantimer").innerHTML = "Scan Completion Time Remaining: " + hourFloorFPB + ":" + minuteFloorFPB + ":" + secondFloorFPB + " : Scanning " + totalFiles + " Files";
			console.log( "Status: 2 or 3: Future Time > Time : Total Files: not blank" );
		}

		// A blank value is set on MScan Start button click for the total scannable files DB option.
		// Removing the status 5 condition:  || mscanStatus == 5 && totalFiles == "". Status 5 is no longer used.
		if ( mscanStatus == 2 && totalFiles == "" ) {
			document.getElementById("MscanBar").style.width = pBarPercentWidthIncrease + '%';
			document.getElementById("MscanBar").innerHTML = pBarPercentWidthIncrease + '%';
			document.getElementById("mscantimer").innerHTML = "Processing Total File Count: Still scanning files: 00:00:" + secondFloorFTE;
			console.log( "Status: 2: Future Time > Time : Total Files: blank" );
		}
	
	} else {

		// Status 5 is no longer used
		if ( mscanStatus == 5 && futureTime < currentTime ) {
			window.location.reload(true);
			//clearInterval(MScan); // for testing ONLY
			console.log( "Status: 5 : Future Time < Time" );
		}
	
		// Clicking MScan Reset sets scanStart to a blank value. scanStop != "stop" prevents an endless reload loop from occurring.
		if ( mscanStatus == 4 && futureTime < currentTime && totalFiles == "" && scanStart != "" && scanStop != "stop" ) {
			window.location.reload(true);
			//window.location=window.location;
			console.log( "Status: 4 : Future Time < Time : Total Files: blank : Start: not blank : Stop: not stop" );
		}
	
		if ( mscanStatus == 3 && futureTime < currentTime ) {
			window.location.reload(true);
			//window.opener.location.reload();
			document.getElementById("mscantimer").innerHTML = ScanCompleted;
			console.log( "Status: 3 : Future Time < Time : Scan Completed" );
		}
	
		// Unfortunately, this condition goes over and under time. Not much I can do about that.
		if ( mscanStatus == 2 && futureTime < currentTime ) {
			window.location.reload(true);
			document.getElementById("mscantimer").innerHTML = "Estimated Scan Time Exceeded: Still scanning files: 00:00:" + secondFloorFTE;		
			console.log( "Status: 2 : Future Time < Time : Scan Time Estimate Exceeded. Still Scanning Files." );
		}
	
		if ( mscanStatus == 1 && futureTime < currentTime || mscanStatus == 1 && secondFloorF <= 10 ) {
			window.location.reload(true);
			console.log( "Status: 1 : Future Time < Time : secondFloorF <= 10 : " + secondFloorF );
		}
	}	
}
/* ]]> */
</script>
</body>
</html>