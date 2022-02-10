<?php
	global $isapage;
	$isapage = true;		
	
	//in case the file is loaded directly
	if(!function_exists("get_userdata"))
	{
		define('WP_USE_THEMES', false);
		require_once(dirname(__FILE__) . '/../../../../wp-load.php');
	}		
	
	//this script must be enabled to run
	if(!defined('PMPRO_GETFILE_ENABLED') || !PMPRO_GETFILE_ENABLED)
		die("The getfile script is not enabled.");
	
	//prevent loops when redirecting to .php files
	if(!empty($_REQUEST['noloop']))
	{
		status_header( 500 );
		die("This file cannot be loaded through the get file script.");
	}
	
	require_once(dirname(__FILE__) . '/../classes/class.mimetype.php');
	
	global $wpdb;

	// Get the file path.
	$uri = $_SERVER['REQUEST_URI'];

	// Remove the query string from the path.
	$uri_parts = explode( '?', $uri );
	$uri       = $uri_parts[0];

	// Take the / off of the 
	if ( '/' === $uri[0] ) {
		$uri = substr( $uri, 1, strlen( $uri ) - 1 );
	}
	
	// decode the file in case it's encoded.
	$uri = urldecode( $uri );
	
	/*
		Remove ../-like strings from the URI.
		Actually removes any combination of two or more ., /, and \.
		This will prevent traversal attacks and loading hidden files.
	*/
	$uri = preg_replace("/[\.\/\\\\]{2,}/", "", $uri);
	
	//if WP is installed in a subdirectory, that directory(s) will be in both the PATH and URI
	$home_url_parts = explode("/", str_replace("//", "", home_url()));	
	if(count($home_url_parts) > 1)
	{
		//found a directory or more
		$uri_parts = explode("/", $uri);
		
		//create new uri without the directories in front
		$new_uri_parts = array();		
		for($i = count($home_url_parts) - 1; $i < count($uri_parts); $i++)
			$new_uri_parts[] = $uri_parts[$i];
		$new_uri = implode("/", $new_uri_parts);
	}
	else
		$new_uri = $uri;
	
	$filename = ABSPATH . $new_uri;
	$pathParts = pathinfo($filename);			
		
	//only checking if the image is pulled from outside the admin
	if(!is_admin())
	{
		//get some info to use
		$upload_dir = wp_upload_dir();			//wp upload dir
		$filename_small = substr($filename, strlen($upload_dir['basedir']) + 1, strlen($filename) - strlen($upload_dir['basedir']) - 1);  //just the part wp saves							
		
		//look the file up in the db				
		$sqlQuery = "SELECT post_parent FROM $wpdb->posts WHERE ID = (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = '" . esc_sql($filename_small) . "' LIMIT 1) LIMIT 1";		
		$file_post_parent = $wpdb->get_var($sqlQuery);
		
		//has access?
		if($file_post_parent)
		{
			if(!pmpro_has_membership_access($file_post_parent))
			{
				//hook for users without access
				do_action("pmpro_getfile_before_error", $filename, $file_post_parent);
				
				//nope				
				header('HTTP/1.1 503 Service Unavailable', true, 503);
				echo "HTTP/1.1 503 Service Unavailable";
				exit;
			}
		}		
	}
		
	//get mimetype
	$mimetype = new pmpro_mimetype();       		
	$file_mimetype = $mimetype->getType($filename);
	
	//in case we want to do something else with the file
	do_action("pmpro_getfile_before_readfile", $filename, $file_mimetype);
	
	//if file is not found, die
	if(!file_exists($filename))
	{
		status_header( 404 );
        nocache_headers();        
        die("File not found.");
	}
	
	//if blocklisted file type, redirect to it instead
	$basename = basename($filename);
	$parts = explode('.', $basename);
	$ext = strtolower($parts[count($parts)-1]);
	
	//build blocklist and allow for filtering
	$blocklist = array("inc", "php", "php3", "php4", "php5", "phps", "phtml");
	$blocklist = apply_filters("pmpro_getfile_extension_blocklist", $blocklist);

	//check
	if(in_array($ext, $blocklist))
	{		
		//add a noloop param to avoid infinite loops
		$uri = add_query_arg("noloop", 1, $uri);
		
		//guess scheme and add host back to uri
		if(is_ssl())
			$uri = "https://" . $_SERVER['HTTP_HOST'] . "/" . $uri;
		else
			$uri = "http://" . $_SERVER['HTTP_HOST'] . "/" . $uri;
				
		wp_safe_redirect($uri);
		exit;
	}
		
	//okay show the file
	header("Content-type: " . $file_mimetype); 	
	readfile($filename);
	exit;
