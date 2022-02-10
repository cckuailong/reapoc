<?php
/*
	Shortcode to hide/show content based on membership level
*/
function pmpro_shortcode_membership($atts, $content=null, $code="")
{
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [membership level="3"]...[/membership]

	extract(shortcode_atts(array(
		'level' => NULL,
		'levels' => NULL,
		'delay' => NULL,
		'show_noaccess' => NULL
	), $atts));

	//if levels is used instead of level
	if(isset($levels) && !isset($level))
		$level = $levels;
	
	global $wpdb, $current_user;

	//guilty until proven innocent :)
	$hasaccess = false;

	//figure out which level/levels to check
	if(!empty($level) || $level === "0" || $level === 0)
	{
	   //they specified a level(s)
	   if(strpos($level, ","))
	   {
		   //they specified many levels
		   $levels = explode(",", $level);
	   }
	   else
	   {
		   //they specified just one level
		   $levels = array($level);
	   }	   
	}
	else
	{
		//didn't specify a membership level, so use false so pmpro_hasMembershipLevel checks for any level
		$levels = false;
	}

	//check their level
	if(pmpro_hasMembershipLevel($levels))
		   $hasaccess = true;

	//is there a delay?
	if($hasaccess && !empty($delay))
	{		
		//okay, this post requires membership. start by getting the user's startdate
		if(!empty($levels))
			$sqlQuery = "SELECT UNIX_TIMESTAMP(CONVERT_TZ(startdate, '+00:00', @@global.time_zone)) FROM $wpdb->pmpro_memberships_users WHERE status = 'active' AND membership_id IN(" . implode(",", $levels) . ") AND user_id = '" . $current_user->ID . "' ORDER BY id LIMIT 1";
		else
			$sqlQuery = "SELECT UNIX_TIMESTAMP(CONVERT_TZ(startdate, '+00:00', @@global.time_zone)) FROM $wpdb->pmpro_memberships_users WHERE status = 'active' AND user_id = '" . $current_user->ID . "' ORDER BY id LIMIT 1";

		$startdate = $wpdb->get_var($sqlQuery);

		//adjust start date to 12AM
		$startdate = strtotime(date_i18n("Y-m-d", $startdate));

		if(empty($startdate))
		{
			//user doesn't have an active membership level
			$hasaccess = false;
		}
		else
		{
			//how many days has this user been a member?
			$now = current_time('timestamp');
			$days = ($now - $startdate)/3600/24;

			if($days < intval($delay))
				$hasaccess = false;	//they haven't been around long enough yet
		}
	}

	// Filter the $hasaccess so we can overwrite this for other add ons.
	$hasaccess = apply_filters( 'pmpro_member_shortcode_access', $hasaccess, $content, $levels, $delay );

	//to show or not to show
	if($hasaccess)
		return do_shortcode($content);	//show content
	else {
		if ( empty( $show_noaccess ) ) {
			return '';
		} else {
			$content = '';
			return pmpro_get_no_access_message( $content, $levels );
		}
	}
}
add_shortcode("membership", "pmpro_shortcode_membership");
