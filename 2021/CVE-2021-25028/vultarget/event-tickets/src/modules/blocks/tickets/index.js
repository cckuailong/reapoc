/**
 * External dependencies
 */
import React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { InnerBlocks } from '@wordpress/editor';

/**
 * Internal dependencies
 */
import { Tickets as TicketsIcon } from '@moderntribe/tickets/icons';
import {
	KEY_TICKET_HEADER,
	KEY_TICKET_CAPACITY,
	KEY_TICKET_DEFAULT_PROVIDER,
	KEY_TICKETS_LIST,
} from '@moderntribe/tickets/data/utils';
import Tickets from './container';

/**
 * Module Code
 */
export default {
	id: 'tickets',
	title: __( 'Tickets', 'event-tickets' ),
	description: __( 'Sell tickets and register attendees.', 'event-tickets' ),
	icon: <TicketsIcon />,
	category: 'tribe-tickets',
	keywords: [ 'event', 'events-gutenberg', 'tribe' ],

	supports: {
		html: false,
		multiple: false,
		customClassName: false,
	},

	attributes: {
		sharedCapacity: {
			type: 'string',
			source: 'meta',
			meta: KEY_TICKET_CAPACITY,
		},
		header: {
			type: 'string',
			source: 'meta',
			meta: KEY_TICKET_HEADER,
		},
		provider: {
			type: 'string',
			source: 'meta',
			meta: KEY_TICKET_DEFAULT_PROVIDER,
		},
		tickets: {
			type: 'array',
			source: 'meta',
			meta: KEY_TICKETS_LIST,
		},
	},

	edit: Tickets,
	save: () => (
		<div><InnerBlocks.Content /></div>
	),
};
