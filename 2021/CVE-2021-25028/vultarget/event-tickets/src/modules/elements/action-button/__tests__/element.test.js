/**
 * External dependencies
 */
import React from 'react';
import renderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import { ActionButton } from '@moderntribe/tickets/elements';
import { positions } from '@moderntribe/tickets/elements/action-button/element';
import { Button } from '@moderntribe/common/elements';

const Icon = () => ( <span role="img" aria-label="Emoji">🦖</span> );

describe( 'ActionButton', () => {
	test( 'component rendered', () => {
		const component = renderer.create(
			<ActionButton icon={ <Icon /> }>Custom Action</ActionButton>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'component rendered with the correct class when icon is on the right', () => {
		const component = renderer.create(
			<ActionButton icon={ <Icon /> } position={ positions.right }>Custom Action</ActionButton>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	test( 'component positions', () => {
		expect( positions.right ).toBe( 'right' );
		expect( positions.left ).toBe( 'left' );
	} );

	test( 'component has class', () => {
		const component = mount( <ActionButton icon={ <Icon /> }>Custom Action</ActionButton> );
		const button = component.find( Button );
		expect( button.hasClass( 'tribe-editor__action-button' ) ).toBe( true );
	} );

	test( 'component rendered as link', () => {
		const component = renderer.create(
			<ActionButton asLink={ true } icon={ <Icon /> } href="#">Test Action</ActionButton>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
