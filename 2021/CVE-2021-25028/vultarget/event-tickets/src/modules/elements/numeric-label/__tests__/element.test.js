/**
 * External dependencies
 */
import React from 'react';
import renderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import { NumericLabel } from '@moderntribe/tickets/elements';

describe( 'NumericLabel', () => {
	test( 'render component', () => {
		const component = renderer.create( <NumericLabel count={ 0 } /> );
		expect( component.toJSON() ).toBe( null );
	} );

	test( 'render component with fallback value', () => {
		const component = renderer.create( <NumericLabel count={ 0 } fallback="My fallback value" /> );
		expect( component.toJSON() ).toBe( 'My fallback value' );
	} );

	test( 'Disable fallback rendering', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 0 }
				fallback="My fallback value"
				useFallback={ false }
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'render with singular label', () => {
		const component = renderer.create(
			<NumericLabel count={ 0 } singular="Just %d item" />,
		);
		expect( component.toJSON() ).toBe( null );
	} );

	test( 'render with empty and fallback', () => {
		const component = renderer.create(
			<NumericLabel count={ 0 } singular="Just %d item" fallback={ '' } />,
		);
		expect( component.toJSON() ).toBe( '' );
	} );

	test( 'render with negative number', () => {
		const component = renderer.create(
			<NumericLabel count={ -10 } />,
		);
		expect( component.toJSON() ).toBe( null );
	} );

	test( 'singular item with no placeholder', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 1 }
				singular="Just Item"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'singular item with placeholder', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 1 }
				singular="Just %d Item"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'singular item with multiple placeholders', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 1 }
				singular="Just %d Item %d more text over here"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'plural without placeholder', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 3 }
				plural="We have items"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'plural with placeholder', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 3 }
				plural="We have %d items"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'plural with multiple placeholder', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 3 }
				plural="We have %d items, more %d items over here"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'include zero in numeric label', () => {
		const component = renderer.create(
			<NumericLabel
				count={ 0 }
				includeZero={ true }
				singular="We have %d item"
				plural="We have %d items"
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
