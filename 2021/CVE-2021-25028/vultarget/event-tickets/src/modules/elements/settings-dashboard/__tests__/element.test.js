/**
 * External dependencies
 */
import React from 'react';
import renderer from 'react-test-renderer';

/**
 * Internal dependencies
 */
import { SettingsDashboard } from '@moderntribe/tickets/elements';

// Mock to overwrite the default SVG icons mock
jest.mock( '@moderntribe/common/icons', () => ( {
	Close: () => <span>Close Icon</span>,
	Cog: () => <span>Cog Icon</span>,
} ) );

describe( 'Settings Dashboard Element', () => {
	it( 'renders settings dashboard', () => {
		const component = renderer.create( <SettingsDashboard /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders settings dashboard with class', () => {
		const className = 'test-class';
		const component = renderer.create( <SettingsDashboard className={ className } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders settings dashboard with content', () => {
		const content = <span>Content</span>;
		const component = renderer.create( <SettingsDashboard content={ content } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders settings dashboard with close button disabled', () => {
		const closeButtonDisabled = true;
		const component = renderer.create(
			<SettingsDashboard closeButtonDisabled={ closeButtonDisabled } />,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders settings dashboard with close button label', () => {
		const closeButtonLabel = <span>Close Button Label</span>;
		const component = renderer.create(
			<SettingsDashboard closeButtonLabel={ closeButtonLabel } />,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders settings dashboard with header left', () => {
		const headerLeft = <span>Header Left</span>;
		const component = renderer.create( <SettingsDashboard headerLeft={ headerLeft } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'renders settings dashboard with close click handler', () => {
		const onCloseClick = jest.fn();
		const component = renderer.create( <SettingsDashboard onCloseClick={ onCloseClick } /> );
		expect( component.toJSON() ).toMatchSnapshot();
	} );

	it( 'executes the close click handler', () => {
		const onCloseClick = jest.fn();
		const component = mount( <SettingsDashboard onCloseClick={ onCloseClick } /> );
		component.find( 'button.tribe-editor__settings-dashboard__close-button' ).simulate( 'click' );
		expect( onCloseClick ).toHaveBeenCalled();
		expect( onCloseClick ).toHaveBeenCalledTimes( 1 );
	} );
} );
