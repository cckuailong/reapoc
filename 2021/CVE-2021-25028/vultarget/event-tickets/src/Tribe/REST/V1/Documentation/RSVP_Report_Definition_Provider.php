<?php

/**
 * Class Tribe__Tickets__REST__V1__Documentation__RSVP_Report_Definition_Provider
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Documentation__RSVP_Report_Definition_Provider
	implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$documentation = array(
			'type'       => 'object',
			'properties' => array(
				'rsvp_going'     => array(
					'type'        => 'integer',
					'description' => __( 'How many attendees are "Going"', 'event-tickets' ),
				),
				'rsvp_not_going' => array(
					'type'        => 'integer',
					'description' => __( 'How many attendees are "Not going"', 'event-tickets' ),
				),
			),
		);

		/**
		 * Filters the Swagger documentation generated for the ticket RSVP report in the Event Tickets REST API.
		 *
		 * @since 4.8
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link  http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_tickets_rest_swagger_rsvp_report_documentation', $documentation );

		return $documentation;
	}
}
