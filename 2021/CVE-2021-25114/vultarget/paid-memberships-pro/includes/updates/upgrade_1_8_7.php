<?php
/*
	Remove extra cron jobs inserted in version 1.8.7 and 1.8.7.1
*/
function pmpro_upgrade_1_8_7() {
	
	//fix cron jobs
    $jobs = _get_cron_array();
	
    // Remove all pmpro cron jobs (for now).
    foreach( $jobs as $when => $job_array ) {

        foreach($job_array as $name => $job) {
	        //delete pmpro cron
	        if ( false !== stripos( $name, 'pmpro_cron') )
	            unset($jobs[$when][$name]);	     
    	}

    	//delete empty cron time slots
    	if( empty($jobs[$when]) )
	        unset($jobs[$when]);
    }

    // Save the data
    _set_cron_array($jobs);

    //add the three we want back
	pmpro_maybe_schedule_event(current_time('timestamp'), 'daily', 'pmpro_cron_expire_memberships');
	pmpro_maybe_schedule_event(current_time('timestamp')+1, 'daily', 'pmpro_cron_expiration_warnings');
	pmpro_maybe_schedule_event(current_time('timestamp'), 'monthly', 'pmpro_cron_credit_card_expiring_warnings');

	pmpro_setOption("db_version", "1.87");	

	return 1.87;
}
