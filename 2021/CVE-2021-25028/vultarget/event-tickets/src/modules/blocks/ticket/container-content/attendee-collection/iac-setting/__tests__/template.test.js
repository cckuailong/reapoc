/**
 * External dependencies
 */
import React from 'react';

import IACSetting from './../template';

describe.skip( 'IACSetting', () => {
	test( 'Render the component with no errors', () => {
		const onChange = jest.fn();
		const iacDefault = 'hello';
		const iacOptions = [
			{
				label: 'Hello',
				value: 'hello',
			},
			{
				label: 'World',
				value: 'world',
			},
		];
		const component = renderer.create(
			<IACSetting
				onChange={ onChange }
				iac={ '' }
				iacDefault={ iacDefault }
				iacOptions={ iacOptions }
				isDisabled={ false }
			/>,
		);
		expect( component.toJSON() ).toMatchSnapshot();
	} );
} );
