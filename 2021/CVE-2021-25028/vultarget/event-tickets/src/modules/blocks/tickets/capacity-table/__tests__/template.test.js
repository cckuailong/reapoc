/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import CapacityTable from './../template';

describe( '<CapacityTable />', () => {
	test( 'shared property', () => {
		const component = renderer.create(
			<CapacityTable
				title="Modern Tribe"
				sharedTickets={ [
					{ name: 'Early Bird', quantity: 10 },
					{ name: 'Balcony', quantity: 20 },
				] }
				totalCapacity={ 30 }
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'shared input', () => {
		const onChange = jest.fn();
		const mounted = mount(
			<CapacityTable
				title="Modern Tribe"
				sharedTickets={ [
					{ name: 'Early Bird', quantity: 10 },
					{ name: 'Balcony', quantity: 20 },
				] }
				sharedCapacity={ 30 }
				onSharedCapacityChange={ onChange }
			/>,
		);
		mounted.find( 'input' ).simulate( 'change' );
		expect( onChange ).toBeCalled();
	} );

	test( 'independent property', () => {
		const component = renderer.create(
			<CapacityTable
				title="Modern Tribe"
				independentTickets={ [
					{ name: 'Floor', quantity: 25 },
					{ name: 'VIP', quantity: 45 },
				] }
				totalCapacity={ 70 }
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
