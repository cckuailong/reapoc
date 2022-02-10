/**
 * External dependencies
 */
import React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Attendees from './container';
import { Attendees as AttendeesIcon } from '@moderntribe/tickets/icons';

/**
 * Module Code
 */
export default {
	id: 'attendees',
	title: __( 'Attendee List', 'event-tickets' ),
	description: __(
		'Show the gravatars of people coming to this event.',
		'event-tickets',
	),
	icon: <AttendeesIcon />,
	category: 'tribe-tickets',
	keywords: [ 'event', 'events-gutenberg', 'tribe' ],

	supports: {
		html: false,
		customClassName: false,
	},

	attributes: {
		title: {
			type: 'html',
			default: __( 'Who\'s Attending?', 'event-tickets' ),
		},
		displayTitle: {
			type: 'boolean',
			default: true,
		},
		displaySubtitle: {
			type: 'boolean',
			default: true,
		},
	},

	edit: Attendees,

	save: () => null,
};
