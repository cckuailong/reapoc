/**
 * External dependencies
 */
import React from 'react';

import MoveDelete from '../template';

describe( 'MoveDelete', () => {
	let props;

	beforeEach( () => {
		props = {
			moveTicket: jest.fn(),
			removeTicket: jest.fn(),
			isDisabled: false,
		};
	} );

	it( 'should render', () => {
		const component = renderer.create( <MoveDelete { ...props } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
