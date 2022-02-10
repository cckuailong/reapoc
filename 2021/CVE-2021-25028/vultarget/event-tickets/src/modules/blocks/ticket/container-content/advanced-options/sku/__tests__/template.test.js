/**
 * External dependencies
 */
import React from 'react';

import SKU from './../template';

describe( 'SKU', () => {
	test( 'Render the component with no errors', () => {
		const onChange = jest.fn();
		const component = renderer.create( <SKU onChange={ onChange } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'Triggers the onChange callback', () => {
		const onChange = jest.fn();
		const component = mount( <SKU onChange={ onChange } value={ 'modern-tribe' } /> );
		component.find( 'input' ).simulate( 'change' );
		expect( onChange ).toHaveBeenCalled();
	} );
} );
