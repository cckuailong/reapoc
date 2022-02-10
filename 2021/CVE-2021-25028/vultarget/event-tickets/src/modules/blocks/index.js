/**
 * Wordpress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import { initStore } from '@moderntribe/tickets/data';
import rsvp from '@moderntribe/tickets/blocks/rsvp';
import tickets from '@moderntribe/tickets/blocks/tickets';
import ticket from '@moderntribe/tickets/blocks/ticket';
import attendees from '@moderntribe/tickets/blocks/attendees';

const blocks = [
	rsvp,
	tickets,
	ticket,
	attendees,
];

blocks.forEach( ( block ) => registerBlockType( `tribe/${ block.id }`, block ) );

// Initialize AFTER blocks are registered
// to avoid plugin shown as available in reducer
// but not having block available for use
initStore();

export default blocks;
