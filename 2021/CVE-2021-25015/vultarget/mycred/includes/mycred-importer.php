<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Register Importer: Log Entries
 * @since 1.4
 * @version 1.0
 */
register_importer(
	'mycred_import_log',
	sprintf( __( '%s Log Import', 'mycred' ), mycred_label() ),
	__( 'Import log entries via a CSV file.', 'mycred' ),
	'mycred_importer_log_entries'
);

/**
 * Load Importer: Log Entries
 * @since 1.4
 * @version 1.0
 */
function mycred_importer_log_entries() {
	require_once( ABSPATH . 'wp-admin/includes/import.php' );

	if ( ! class_exists( 'WP_Importer' ) ) {
		$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		if ( file_exists( $class_wp_importer ) )
			require $class_wp_importer;
	}

	require_once( myCRED_INCLUDES_DIR . 'importers/mycred-log-entries.php' );
	
	$importer = new myCRED_Importer_Log_Entires();
	$importer->load();
}

/**
 * Register Importer: Balances
 * @since 1.4.2
 * @version 1.0
 */
register_importer(
	'mycred_import_balance',
	sprintf( __( '%s Balance Import', 'mycred' ), mycred_label() ),
	__( 'Import balances.', 'mycred' ),
	'mycred_importer_point_balances'
);

/**
 * Load Importer: Point Balances
 * @since 1.4
 * @version 1.0
 */
function mycred_importer_point_balances() {
	require_once( ABSPATH . 'wp-admin/includes/import.php' );

	if ( ! class_exists( 'WP_Importer' ) ) {
		$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		if ( file_exists( $class_wp_importer ) )
			require $class_wp_importer;
	}

	require_once( myCRED_INCLUDES_DIR . 'importers/mycred-balances.php' );
	
	$importer = new myCRED_Importer_Balances();
	$importer->load();
}

/**
 * Register Importer: CubePoints
 * @since 1.4
 * @version 1.0
 */
register_importer(
	'mycred_import_cp',
	sprintf( __( '%s CubePoints Import', 'mycred' ), mycred_label() ),
	__( 'Import CubePoints log entries and / or balances.', 'mycred' ),
	'mycred_importer_cubepoints'
);

/**
 * Load Importer: CubePoints
 * @since 1.4
 * @version 1.0
 */
function mycred_importer_cubepoints() {
	require_once( ABSPATH . 'wp-admin/includes/import.php' );

	global $wpdb;

	// No use continuing if there is no log to import
	if ( $wpdb->query( $wpdb->prepare( "SHOW TABLES LIKE %s;", $wpdb->prefix . 'cp' ) ) == 0 ) {
		echo '<p>' . __( 'No CubePoints log exists.', 'mycred' ) . '</p>';
		return;
	}

	if ( ! class_exists( 'WP_Importer' ) ) {
		$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
		if ( file_exists( $class_wp_importer ) )
			require $class_wp_importer;
	}

	require_once( myCRED_INCLUDES_DIR . 'importers/mycred-cubepoints.php' );
	
	$importer = new myCRED_Importer_CubePoints();
	$importer->load();
}
?>