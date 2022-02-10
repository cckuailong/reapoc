<?php
/**
 * Define the XMLRPC Methods We Add
 * Since v2.0
 */
add_filter('xmlrpc_methods', 'pmpro_xmlrpc_methods');
function pmpro_xmlrpc_methods($methods)
{
	$methods['pmpro.getMembershipLevelForUser'] = 'pmpro_xmlrpc_getMembershipLevelForUser';
	$methods['pmpro.hasMembershipAccess'] = 'pmpro_xmlrpc_hasMembershipAccess';
	return $methods;
}

/**
 * API method to get the membership level info for a user.
 * Since v2.0
 */
function pmpro_xmlrpc_getMembershipLevelForUser($args)
{
	// Parse the arguments, assuming they're in the correct order
	$username	= $args[0];
	$password	= $args[1];
	$user_id = $args[2];	//optional user id passed in

	global $wp_xmlrpc_server;

	// Let's run a check to see if credentials are okay
	if ( !$user = $wp_xmlrpc_server->login($username, $password) ) {
		return $wp_xmlrpc_server->error;
	}

	// The user passed should be an admin or have the pmpro_xmlprc capability
	if(!user_can($user->ID, "manage_options") && !user_can($user->ID, "pmpro_xmlrpc"))
		return "ERROR: User does not have access to the PMPro XMLRPC methods.";
	
	// Default to logged in user if no user_id is given.
	if(empty($user_id))
	{		
		$user_id = $user->ID;
	}

	$membership_level = pmpro_getMembershipLevelForUser($user_id);
	
	return $membership_level;
}

/**
 * API method to check if a user has access to a certain post.
 * Since v2.0
 */
function pmpro_xmlrpc_hasMembershipAccess($args)
{
	// Parse the arguments, assuming they're in the correct order
	$username	= $args[0];
	$password	= $args[1];
	$post_id = $args[2];	//post id to check
	$user_id = $args[3];	//optional user id passed in
	$return_membership_levels = $args[4];	//option to also include an array of membership levels with access to the post

	global $wp_xmlrpc_server;

	// Let's run a check to see if credentials are okay
	if ( !$user = $wp_xmlrpc_server->login($username, $password) ) {
		return $wp_xmlrpc_server->error;
	}

	// The user passed should be an admin or have the pmpro_xmlprc capability
	if(!user_can($user->ID, "manage_options") && !user_can($user->ID, "pmpro_xmlrpc"))
		return "ERROR: User does not have access to the PMPro XMLRPC methods.";
	
	// Default to logged in user if no user_id is given.
	if(empty($user_id))
	{		
		$user_id = $user->ID;
	}

	$has_access = pmpro_has_membership_access($post_id, $user_id, $return_membership_levels);

	return $has_access;
}
