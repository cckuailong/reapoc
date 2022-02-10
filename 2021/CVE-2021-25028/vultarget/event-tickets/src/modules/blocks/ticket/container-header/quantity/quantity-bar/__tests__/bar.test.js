/**
 * External dependencies
 */
import React from 'react';
import renderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import Bar from './../bar';

describe( '<Bar />', () => {
	test( 'render null on default values', () => {
		const component = renderer.create( <Bar /> );
		expect( component.toJSON() ).toBe( null );
	} );

	test( 'render null on zero values', () => {
		const component = renderer.create( <Bar total={ 0 } value={ 0 } /> );
		expect( component.toJSON() ).toBe( null );
	} );

	test( 'renders 100 when percentage is above 100', () => {
		const component = shallow( <Bar value={ 200 } total={ 100 } /> );
		const span = component.get( 0 );
		const { style } = span.props;
		expect( style ).toEqual( { width: '100.00%' } );
	} );

	test( 'renders 0 when percentage is negative', () => {
		const component = shallow( <Bar value={ -200 } total={ 100 } /> );
		const span = component.get( 0 );
		const { style } = span.props;
		expect( style ).toEqual( { width: '0.00%' } );
	} );

	test( 'render percentage as inline style', () => {
		const component = shallow( <Bar value={ 33 } total={ 100 } /> );
		const span = component.get( 0 );
		const { style } = span.props;
		expect( style ).toEqual( { width: '33.00%' } );
	} );

	test( 'render percentage with custom class name', () => {
		const component = renderer.create( <Bar value={ 45 } total={ 100 } className="jest-test" /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'render percentage with children', () => {
		const component = renderer.create( <Bar value={ 45 } total={ 100 }>test</Bar> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
