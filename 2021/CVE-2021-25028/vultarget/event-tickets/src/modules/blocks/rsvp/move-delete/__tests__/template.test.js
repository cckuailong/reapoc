/**
 * External dependencies
 */
import React from 'react';

import MoveDelete from '../template';

describe( 'MoveDelete', () => {
	let props;

	beforeEach( () => {
		props = {
			moveRSVP: jest.fn(),
			removeRSVP: jest.fn(),
			isDisabled: false,
		};
	} );

	it( 'should render', () => {
		const component = renderer.create( <MoveDelete { ...props } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
