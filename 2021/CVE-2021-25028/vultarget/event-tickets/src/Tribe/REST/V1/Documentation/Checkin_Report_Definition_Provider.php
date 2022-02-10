<?php

/**
 * Class Tribe__Tickets__REST__V1__Documentation__Checkin_Report_Definition_Provider
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Documentation__Checkin_Report_Definition_Provider
	implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$documentation = array(
			'type'       => 'object',
			'properties' => array(
				'checked_in'              => array(
					'type'        => 'integer',
					'description' => __( 'The number of checked-in attendees', 'event-tickets' ),
				),
				'unchecked_in'            => array(
					'type'        => 'integer',
					'description' => __( 'The number of unchecked-in attendees', 'event-tickets' ),
				),
				'checked_in_percentage'   => array(
					'type'        => 'integer',
					'description' => __( 'The percentage of checked-in attendees', 'event-tickets' ),
				),
				'unchecked_in_percentage' => array(
					'type'        => 'integer',
					'description' => __( 'The number of unchecked-in attendee', 'event-tickets' ),
				),
			),
		);

		/**
		 * Filters the Swagger documentation generated for the ticket checkin report in the Event Tickets REST API.
		 *
		 * @since 4.8
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link  http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_tickets_rest_swagger_checkin_report_documentation', $documentation );

		return $documentation;
	}
}
