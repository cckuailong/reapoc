<?php
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'ebfrtbExport' ) ) {
/**
 * Base class to handle exports
 *
 * @since 1.4.1
 */
abstract class ebfrtbExport {

	/**
	 * Bookings
	 *
	 * @since 0.1
	 */
	public $bookings;

	/**
	 * Export
	 *
	 * A fully-rendered export ready for delivery
	 *
	 * @since 0.1
	 */
	public $export;

	/**
	 * Instantiate the export
	 *
	 * @since 0.1
	 */
	abstract function __construct( $bookings, $args = array() );

	/**
	 * Render the export file
	 *
	 * The export should be constructed and compiled here,
	 * then stored in $export for retrieval and returned.
	 *
	 * @since 0.1
	 */
	abstract function export();

	/**
	 * Deliver the export file by printing it to the buffer
	 *
	 * @since 0.1
	 */
	abstract function deliver();

	/**
	 * Locate a template file
	 *
	 * @since 0.1
	 */
	public function locate_template( $file ) {

		$template_dirs = apply_filters(
			'ebfrtb_template_directories',
			array(
				get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'ebfrtb-templates', // Child theme
				get_template_directory() . DIRECTORY_SEPARATOR . 'ebfrtb-templates', // Parent theme
				RTB_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'templates', // Plugin dir
			)
		);

		foreach( $template_dirs as $dir ) {
			if ( file_exists( $dir . DIRECTORY_SEPARATOR . $file ) ) {
				return $dir . DIRECTORY_SEPARATOR . $file;
			}
		}

		return false;
	}

	/**
	 * Include a template file
	 *
	 * @since 0.1
	 */
	public function include_template( $file ) {

		$template = $this->locate_template( $file );

		if ( empty( $template ) ) {
			return;
		}

		global $rtb_controller;
		$bookings = $this->bookings;
		include( $template );
	}

	/**
	 * Generate a string representing the date range
	 * for this export (if it exists )
	 *
	 * @since 0.1
	 */
	public function get_date_phrase() {

		if ( empty( $this->query_args['date_range'] ) ) {
			return '';
		}

		if ( $this->query_args['date_range'] === 'today' || $this->query_args['date_range'] === 'upcoming' ) {
			$date = date( get_option( 'date_format' ) );

			if ( $this->query_args['date_range'] === 'today' ) {
				return sprintf( _x( 'Bookings for %s', 'Subject for some export documents', 'restaurant-reservations' ), $date );
			} else {
				return sprintf( _x( "Bookings from %s", 'Subject for some export documents', 'restaurant-reservations' ), $date );
			}
		}

		if ( !empty( $this->query_args['start_date'] ) || !empty( $this->query_args['end_date'] ) ) {
			$any_date = _x( '*', 'No date limit in a date range, eg 2014-* would mean any date from 2014 or after', 'restaurant-reservations' );
			$start_date = empty( $this->query_args['start_date'] ) ? $any_date : mysql2date( get_option( 'date_format' ), $this->query_args['start_date'] . ' 00:00:00' );
			$end_date = empty( $this->query_args['end_date'] ) ? $any_date : mysql2date( get_option( 'date_format' ), $this->query_args['end_date'] . ' 00:00:00' );

			return sprintf( $start_date . _x( ' - ', 'Separator between two dates in a date range to be used in exports', 'restaurant-reservations' ) . $end_date );
		}

		return '';
	}

}
} // endif
