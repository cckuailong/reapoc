<?php

/**
 * Class Tribe__Tickets__REST__V1__Documentation__Capacity_Details_Definition_Provider
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Documentation__Capacity_Details_Definition_Provider
	implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$documentation = [
			'type'       => 'object',
			'properties' => [
				'available_percentage' => [
					'type'        => 'integer',
					'description' => esc_html( sprintf( __( 'The %s available capacity percentage', 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'capacity_details_documentation_available_percentage' ) ) ),
				],
				'max'                  => [
					'type'        => 'integer',
					'description' => esc_html( sprintf( __( 'The %s max capacity', 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'capacity_details_documentation_max' ) ) ),
				],
				'available'            => [
					'type'        => 'integer',
					'description' => esc_html( sprintf( __( 'The %s current available capacity', 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'capacity_details_documentation_available' ) ) ),
				],
				'sold'                 => [
					'type'        => 'integer',
					'description' => esc_html( sprintf( __( 'The %s sale count', 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'capacity_details_documentation_sold' ) ) ),
				],
				'pending'              => [
					'type'        => 'integer',
					'description' => esc_html( sprintf( __( 'The %s pending count', 'event-tickets' ), tribe_get_ticket_label_singular_lowercase( 'capacity_details_documentation_pending' ) ) ),
				],
			],
		];

		/**
		 * Filters the Swagger documentation generated for capacity details in the Event Tickets REST API.
		 *
		 * @since 4.8
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link  http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_tickets_rest_swagger_capacity_details_documentation', $documentation );

		return $documentation;
	}
}
