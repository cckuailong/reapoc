/**
 * External dependencies
 */
import React from 'react';
import renderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import LabelWithTooltip from '@moderntribe/tickets/elements/label-with-tooltip/element';

jest.mock( '@wordpress/components', () => ( {
	Tooltip: ( { text, position, children } ) => (
		<div>
			<span>{ text }</span>
			<span>{ position }</span>
			<span>{ children }</span>
		</div>
	),
} ) );

describe( 'Tooltip Element', () => {
	it( 'renders a tooltip', () => {
		const props = {
			className: 'element-class',
			label: 'some label',
			tooltipLabel: 'tooltip label',
			tooltipPosition: 'bottom left',
			tooltipText: 'here is the tooltip text',
		};
		const component = renderer.create( <LabelWithTooltip { ...props } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
