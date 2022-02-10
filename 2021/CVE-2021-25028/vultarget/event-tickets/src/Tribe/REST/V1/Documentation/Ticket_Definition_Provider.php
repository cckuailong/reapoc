<?php

/**
 * Class Tribe__Tickets__REST__V1__Documentation__Ticket_Definition_Provider
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Documentation__Ticket_Definition_Provider
	implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * {@inheritdoc}
	 */
	public function get_documentation() {
		$documentation = array(
			'type'       => 'object',
			'properties' => array(
				'id'                            => array(
					'type'        => 'integer',
					'description' => __( 'The ticket WordPress post ID', 'event-tickets' ),
				),
				'post_id'                       => array(
					'type'        => 'integer',
					'description' => __( 'The ID of the post the ticket is associated to', 'event-tickets' ),
				),
				'global_id'                     => array(
					'type'        => 'string',
					'description' => __( 'The ticket global ID', 'event-tickets' ),
				),
				'global_id_lineage'             => array(
					'type'        => 'array',
					'items'       => array(
						'type' => 'string',
					),
					'description' => __( 'The ticket global ID lineage', 'event-tickets' ),
				),
				'author'                        => array(
					'type'        => 'integer',
					'description' => __( 'The ticket post author ID', 'event-tickets' ),
				),
				'status'                        => array(
					'type'        => 'string',
					'description' => __( 'The ticket post status', 'event-tickets' ),
				),
				'date'                          => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The ticket creation date', 'event-tickets' ),
				),
				'date_utc'                      => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The ticket creation UTC date', 'event-tickets' ),
				),
				'modified'                      => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The ticket modification date', 'event-tickets' ),
				),
				'modified_utc'                  => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The ticket modification UTC date', 'event-tickets' ),
				),
				'rest_url'                      => array(
					'type'        => 'string',
					'format'      => 'uri',
					'description' => __( 'The ticket ET REST API URL', 'event-tickets' ),
				),
				'provider'                      => array(
					'type'        => 'string',
					'description' => __( 'The ticket commerce provider', 'event-tickets' ),
					'enum'        => array( 'rsvp', 'tribe-commerce', 'woo', 'edd' ),
				),
				'title'                         => array(
					'type'        => 'string',
					'description' => __( 'The ticket title', 'event-tickets' ),
				),
				'description'                   => array(
					'type'        => 'string',
					'description' => __( 'The ticket description', 'event-tickets' ),
				),
				'image'                         => array(
					'$ref' => '#/components/schemas/Image',
				),
				'available_from'                => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The date the ticket will be available', 'event-tickets' ),
				),
				'available_from_details'        => array(
					'$ref' => '#/components/schemas/DateDetails',
				),
				'available_until'               => array(
					'type'        => 'string',
					'format'      => 'date',
					'description' => __( 'The date the ticket will be available', 'event-tickets' ),
				),
				'available_until_details'       => array(
					'$ref' => '#/components/schemas/DateDetails',
				),
				'capacity'                      => array(
					'type'        => 'integer',
					'description' => __( 'The formatted ticket current capacity', 'event-tickets' ),
				),
				'capacity_details'              => array(
					'$ref' => '#/components/schemas/CapacityDetails',
				),
				'is_available'                  => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the ticket is currently available or not due to capacity or date constraints', 'event-tickets' ),
				),
				'cost'                          => array(
					'type'        => 'integer',
					'description' => __( 'The formatted cost string', 'event-tickets' ),
				),
				'cost_details'                  => array(
					'$ref' => '#/components/schemas/CostDetails',
				),
				'attendees'                     => array(
					'type'        => 'array',
					'items'       => array(
						'$ref' => '#/components/schemas/Attendee',
					),
					'description' => __( 'A list of attendees for the ticket, ', 'event-tickets' ),
				),
				'supports_attendee_information' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the ticket supports at least one attendee information field, ET+ required', 'event-tickets' ),
				),
				'requires_attendee_information' => array(
					'type'        => 'boolean',
					'description' => __( 'Whether the ticket requires at least one attendee information field, ET+ required', 'event-tickets' ),
				),
				'attendee_information_fields'   => array(
					'type'        => 'object',
					'description' => __( 'A list of attendee information fields supported/required by the ticket in the format [ <field-slug>: label, required, type, extra ]', 'event-tickets' ),
				),
				'rsvp'                          => array(
					'$ref' => '#/components/schemas/RSVPReport',
				),
				'checkin'                       => array(
					'$ref' => '#/components/schemas/CheckinReport',
				),
			),
		);

		/**
		 * Filters the Swagger documentation generated for a ticket in the Event Tickets REST API.
		 *
		 * @since 4.7.5
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link  http://swagger.io/
		 */
		$documentation = apply_filters( 'tribe_tickets_rest_swagger_ticket_documentation', $documentation );

		return $documentation;
	}
}
