/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import TicketDurationPicker from './../template';

describe( 'Ticket Duration picker and label', () => {
	test( 'default properties', () => {
		const component = renderer.create(
			<TicketDurationPicker
				fromTime="00:00"
				toTime="23:59"
				current="12:34"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
