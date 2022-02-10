/**
 * Internal dependencies
 */
import Tickets from '@moderntribe/tickets/blocks/tickets';

describe( 'Ticket block declaration', () => {
	it( 'register the ticket block', () => {
		expect( Tickets ).toMatchSnapshot();
	} );
} );
