<?php


class Tribe__Tickets__CSV_Importer__Column_Names {

	/**
	 * @var self
	 */
	protected static $instance;

	/**
	 * The class singleton constructor.
	 *
	 * @return Tribe__Tickets__CSV_Importer__Column_Names
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Adds RSVP column names to the importer mapping options.
	 *
	 * @param array $column_names
	 *
	 * @return array
	 */
	public function filter_rsvp_column_names( array $column_names ) {
		$column_names = array_merge( $column_names,
			array(
				'event_name'              => esc_html__( 'Event Name or ID or Slug', 'event-tickets' ),
				'ticket_name'             => esc_html( sprintf( __( '%s Name', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_name' ) ) ),
				'ticket_description'      => esc_html( sprintf( __( '%s Description', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_description' ) ) ),
				'ticket_show_description' => esc_html( sprintf( __( '%s Show Description', 'event-tickets-plus' ), tribe_get_ticket_label_singular( 'rsvp_column_name_show_description' ) ) ),
				'ticket_start_sale_date'  => esc_html( sprintf( __( '%s Start Sale Date', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_start_sale_date' ) ) ),
				'ticket_start_sale_time'  => esc_html( sprintf( __( '%s Start Sale Time', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_start_sale_time' ) ) ),
				'ticket_end_sale_date'    => esc_html( sprintf( __( '%s End Sale Date', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_end_sale_date' ) ) ),
				'ticket_end_sale_time'    => esc_html( sprintf( __( '%s End Sale Time', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_end_sale_time' ) ) ),
				'ticket_stock'            => esc_html( sprintf( __( '%s Stock', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_stock' ) ) ),
				'ticket_capacity'         => esc_html( sprintf( __( '%s Capacity', 'event-tickets' ), tribe_get_ticket_label_singular( 'rsvp_column_name_capacity' ) ) ),
			) );

		return $column_names;
	}

	/**
	 * Adds RSVP column mapping data to the csv_column_mapping array that gets output via JSON
	 *
	 * @param array $mapping Mapping data indexed by CSV import type
	 *
	 * @return array
	 */
	public function filter_rsvp_column_mapping( $mapping ) {
		$mapping['rsvp_tickets'] = get_option( 'tribe_events_import_column_mapping_rsvp_tickets', array() );
		return $mapping;
	}
}
