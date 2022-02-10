/**
 * Internal dependencies
 */
import TicketBlock from '@moderntribe/tickets/blocks/ticket';

describe( 'Single ticket block declaration', () => {
	test( 'Block declaration', () => {
		expect( TicketBlock ).toMatchSnapshot();
	} );
} );
