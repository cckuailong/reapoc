/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import InactiveBlock, { LAYOUT } from '@moderntribe/tickets/elements/inactive-block/element';
import { RSVPInactive } from '@moderntribe/tickets/icons';

const inactiveBlockProps = {
	className: 'tribe-editor__rsvp__inactive-block',
	icon: <RSVPInactive />,
	layout: LAYOUT.rsvp,
};

const RSVPInactiveBlock = ( { created } ) => {
	inactiveBlockProps.title = created
		? __( 'RSVP is not currently active', 'event-tickets' )
		: __( 'There is no RSVP configured', 'event-tickets' );

	inactiveBlockProps.description = created
		? __( 'Edit this block to change RSVP settings.', 'event-tickets' )
		: __( 'Edit this block to create an RSVP form.', 'event-tickets' );

	return <InactiveBlock { ...inactiveBlockProps } />;
};

RSVPInactiveBlock.propTypes = {
	created: PropTypes.bool.isRequired,
};

export default RSVPInactiveBlock;
