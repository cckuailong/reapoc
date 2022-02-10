/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import ContainerPanel, { LAYOUT } from '@moderntribe/tickets/elements/container-panel/element';

describe( 'Container Panel Element', () => {
	it( 'renders container panel in rsvp layout', () => {
		const component = renderer.create( <ContainerPanel layout={ LAYOUT.rsvp } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders container panel in ticket layout', () => {
		const component = renderer.create( <ContainerPanel layout={ LAYOUT.ticket } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders container panel with icon, header, and content', () => {
		const props = {
			icon: 'icon',
			header: 'header',
			content: 'content',
			layout: LAYOUT.rsvp,
		};
		const component = renderer.create( <ContainerPanel { ...props } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders container panel with classes', () => {
		const props = {
			className: 'test-class-name',
			layout: LAYOUT.rsvp,
		};
		const component = renderer.create( <ContainerPanel { ...props } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
