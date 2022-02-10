/**
 * External dependencies
 */
import React from 'react';
import renderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import Availability from '../template';

describe( 'Availability', () => {
	it( 'render component', () => {
		const component = renderer.create( <Availability /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'render component with available property', () => {
		const component = renderer.create( <Availability available={ 10 } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders component with total property', () => {
		const component = renderer.create( <Availability total={ 20 } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders component with available and total properties', () => {
		const component = renderer.create( <Availability available={ 1 } total={ 20 } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
