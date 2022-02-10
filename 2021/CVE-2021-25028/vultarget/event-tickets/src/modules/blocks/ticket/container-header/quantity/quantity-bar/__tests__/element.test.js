/**
 * External dependencies
 */
import React from 'react';
import renderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import QuantityBar from './../element';

describe( '<QuantityBar>', () => {
	test( 'independent capacity', () => {
		const component = renderer.create( <QuantityBar sold={ 20 } total={ 100 } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'shared capacity', () => {
		const component = renderer.create(
			<QuantityBar sold={ 20 } sharedSold={ 80 } total={ 100 } />,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'with limited capacity', () => {
		const component = renderer.create(
			<QuantityBar sold={ 20 } sharedSold={ 80 } capacity={ 50 } total={ 100 } />,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'avoid render percentage on disabled', () => {
		const component = renderer.create(
			<QuantityBar
				sold={ 20 }
				sharedSold={ 80 }
				capacity={ 50 }
				total={ 100 }
				isDisabled={ true }
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
