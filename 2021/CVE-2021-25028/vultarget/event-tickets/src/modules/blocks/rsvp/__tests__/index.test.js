/**
 * Internal dependencies
 */
import RSVP from '@moderntribe/tickets/blocks/rsvp';

describe( 'RSVP block declaration', () => {
	it( 'register the RSVP block', () => {
		expect( RSVP ).toMatchSnapshot();
	} );
} );
