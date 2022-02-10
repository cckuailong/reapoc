<?php

/**
 * Class Tribe__Tickets__REST__V1__Documentation__Checkin_Details_Definition_Provider
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Documentation__Checkin_Details_Definition_Provider
	implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$documentation = array(
			'type'       => 'object',
			'properties' => array(
				'date'         => array(
					'type'        => 'string',
					'description' => __( 'The time the attendee checked in', 'event-tickets' ),
				),
				'date_details' => array(
					'$ref' => '#/components/schemas/DateDetails',
				),
				'source'       => array(
					'type'        => 'string',
					'description' => __( 'The check-in source for the attendee; e.g. "kiosk" or "site"', 'event-tickets' ),
				),
				'author'       => array(
					'type'        => 'string',
					'description' => __( 'The ID or identifying string of the site user or operator that checked in the attendee', 'event-tickets' ),
				),
			),
		);

		/**
		 * Filters the Swagger documentation generated for checkin details in the Event Tickets REST API.
		 *
		 * @since 4.8
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link  http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_tickets_rest_swagger_checkin_details_documentation', $documentation );

		return $documentation;
	}
}
