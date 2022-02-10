/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import CapacityRow from './../template';

describe( '<CapacityRow />', () => {
	test( 'label', () => {
		const component = renderer.create( <CapacityRow label="Modern Tribe" /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'Label with items', () => {
		const component = renderer.create( <CapacityRow label="Modern Tribe" items="(20 items)" /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'Custom right value', () => {
		const Button = () => <button>Click Me!</button>;
		const component = renderer.create(
			<CapacityRow label="Modern Tribe" items="(20 items)" right={ <Button /> } />,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
