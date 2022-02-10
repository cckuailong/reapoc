<?php

$path1 = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
include($path1.'wp-load.php');


if (current_user_can('activate_plugins')) {
	
	$file = "mystickyelements_contact_leads.csv";
	$fp = fopen($file, "a")or die("Error Couldn't open $file for writing!");
	
    global $wpdb;
    $contact_lists_table = $wpdb->prefix.'mystickyelement_contact_lists';
    $contact_lists_to_write = $wpdb->get_results( "SELECT * FROM $contact_lists_table");
	$all_data = '';
	foreach ($contact_lists_to_write as $res) {
		$res_ID = $res->ID;
		$res_name = $res->contact_name;
		$res_phone = $res->contact_phone;
		$res_email = $res->contact_email;
		$res_option = $res->contact_option;
		$res_message = $res->contact_message;
		$res_message_date = $res->message_date;
		$page_link = $res->page_link;		

		$current_row = $res_ID.' , '.$res_name.' , '.$res_phone.' , '.$res_email.' , '.$res_option.' , '.$res_message.' , '.$res_message_date. PHP_EOL;
		$all_data = $all_data." ".$current_row . "\r\n";
		$fields = array($res_ID, $res_name, $res_phone, $res_email, $res_option, $res_message, $res_message_date,$page_link);
		fputcsv($fp, $fields);
	}


	//fwrite($fp, $all_data)or die("Error Couldn't write values to file!"); 
	fclose($fp); 
	
	
	
	if (file_exists($file)) {
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="' . basename($file) . '"');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		unlink($file);
		exit;
	}

}