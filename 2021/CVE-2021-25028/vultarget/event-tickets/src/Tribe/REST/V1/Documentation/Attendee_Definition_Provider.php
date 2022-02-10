<?php

/**
 * Class Tribe__Attendees__REST__V1__Documentation__Attendee_Definition_Provider
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Documentation__Attendee_Definition_Provider
	implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$documentation = array(
			'type'       => 'object',
			'properties' => array(
				'id'                => array(
					'type'        => 'integer',
					'description' => __( 'The attendee WordPress post ID', 'event-tickets' ),
				),
				'post_id'           => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post the attendee is associated to', 'event-tickets' ),
				),
				'ticket_id'         => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the ticket the attendee is associated with', 'event-tickets' ),
				),
				'global_id'         => array(
					'type'        => 'string',
					'description' => __( 'The attendee global ID', 'event-tickets' ),
				),
				'global_id_lineage' => array(
					'type'        => 'array',
					'items'       => array(
						'type' => 'string',
					),
					'description' => __( 'The attendee global ID lineage', 'event-tickets' ),
				),
				'author'            => array(
					'type'        => 'integer',
					'description' => __( 'The attendee post author ID', 'event-tickets' ),
				),
				'status'            => array(
					'type'        => 'string',
					'description' => __( 'The attendee post status', 'event-tickets' ),
				),
				'date'              => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The attendee creation date', 'event-tickets' ),
				),
				'date_utc'          => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The attendee creation UTC date', 'event-tickets' ),
				),
				'modified'          => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The attendee modification date', 'event-tickets' ),
				),
				'modified_utc'      => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The attendee modification UTC date', 'event-tickets' ),
				),
				'rest_url'          => array(
					'type'        => 'string',
					'format'      => 'uri',
					'description' => __( 'The attendee ET REST API URL', 'event-tickets' ),
				),
				'provider'          => array(
					'type'        => 'string',
					'description' => __( 'The ticket commerce provider', 'event-tickets' ),
					'enum'        => array( 'rsvp', 'tribe-commerce', 'woo', 'edd' ),
				),
				'order'             => array(
					'type'        => 'string',
					'description' => __( 'The order number, or identifier, of the ticket purchase that generated the attendee', 'event-tickets' ),
				),
				'sku'               => array(
					'type'        => 'string',
					'description' => __( 'The attendee ticket SKU', 'event-tickets' ),
				),
				'title'             => array(
					'type'        => 'string',
					'description' => __( 'The attendee title or name', 'event-tickets' ),
				),
				'email'             => array(
					'type'        => 'string',
					'format'      => 'email',
					'description' => __( 'The attendee email address', 'event-tickets' ),
				),
				'checked_in'        => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the attendee is checked-in or not', 'event-tickets' ),
				),
				'checkin_details'   => array(
					'$ref' => '#/components/schemas/CheckinDetails',
				),
				'rsvp_going'        => array(
					'type'        => 'boolean',
					'description' => __( 'If the attendee is for an RSVP ticket, this will be set to true if he/she is "Going", false otherwise', 'event-tickets' ),
				),
				'payment'           => array(
					'$ref' => '#/components/schemas/PaymentDetails',
				),
				'information'       => array(
					'type'        => 'object',
					'description' => __( 'The attendee information; requires ET+', 'event-tickets' ),
				),
			),
		);

		/**
		 * Filters the Swagger documentation generated for an attendee in the Event Tickets REST API.
		 *
		 * @since 4.7.5
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link  http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_tickets_rest_swagger_attendee_documentation', $documentation );

		return $documentation;
	}
}
