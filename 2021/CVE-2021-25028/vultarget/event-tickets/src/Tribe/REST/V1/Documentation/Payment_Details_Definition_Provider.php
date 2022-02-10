<?php

/**
 * Class Tribe__Tickets__REST__V1__Documentation__Payment_Details_Definition_Provider
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Documentation__Payment_Details_Definition_Provider
	implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$documentation = array(
			'type'       => 'object',
			'properties' => array(
				'provider'     => array(
					'type'        => 'string',
					'description' => __( 'The payment provider/gateway', 'event-tickets' ),
				),
				'price'        => array(
					'type'        => 'integer',
					'description' => __( 'The price paid by the attendee when he/she purchased the ticket', 'event-tickets' ),
				),
				'currency'     => array(
					'type'        => 'string',
					'description' => __( 'The currency used by the attendee to pay', 'event-tickets' ),
				),
				'date'         => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The payment date', 'event-tickets' ),
				),
				'date_details' => array(
					'$ref' => '#/components/schemas/DateDetails',
				),
			),
		);

		/**
		 * Filters the Swagger documentation generated for payment details in the Event Tickets REST API.
		 *
		 * @since 4.8
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link  http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_tickets_rest_swagger_payment_details_documentation', $documentation );

		return $documentation;
	}
}
