<?php
// Function to generate filename of CSV file based on the Export type
function woo_ce_generate_csv_filename( $export_type = '' ) {

	// Get the filename from WordPress options
	$filename = woo_ce_get_option( 'export_filename', 'woo-export_%dataset%-%date%-%random%.csv' );

	// Strip other file extensions if present
	$filename = str_replace( array( 'xml', 'xls' ), 'csv', $filename );
	if( ( strpos( $filename, '.xml' ) !== false ) || ( strpos( $filename, '.xls' ) !== false ) )
		$filename = str_replace( array( '.xml', '.xls' ), '.csv', $filename );

	// Add file extension if it has been removed
	if( strpos( $filename, '.csv' ) === false )
		$filename .= '.csv';

	// Populate the available tags
	$date = date( 'Y_m_d' );
	$time = date( 'H_i_s' );
	$random = mt_rand( 10000000, 99999999 );
	$store_name = sanitize_title( get_bloginfo( 'name' ) );

	// Switch out the tags for filled values
	$filename = str_replace( '%dataset%', $export_type, $filename );
	$filename = str_replace( '%date%', $date, $filename );
	$filename = str_replace( '%time%', $time, $filename );
	$filename = str_replace( '%random%', $random, $filename );
	$filename = str_replace( '%store_name%', $store_name, $filename );

	// Return the filename
	return $filename;

}

// File output header for CSV file
function woo_ce_generate_csv_header( $export_type = '' ) {

	global $export;

	if( $filename = woo_ce_generate_csv_filename( $export_type ) ) {
		$mime_type = 'text/csv';
		header( sprintf( 'Content-Encoding: %s', esc_attr( $export->encoding ) ) );
		header( sprintf( 'Content-Type: %s; charset=%s', $mime_type, esc_attr( $export->encoding ) ) );
		header( 'Content-Transfer-Encoding: binary' );
		header( sprintf( 'Content-Disposition: attachment; filename=%s', $filename ) );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
	}

}