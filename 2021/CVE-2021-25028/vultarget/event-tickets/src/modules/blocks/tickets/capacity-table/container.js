/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import CapacityTable from './template';
import { withStore } from '@moderntribe/common/hoc';
import { selectors, actions } from '@moderntribe/tickets/data/blocks/ticket';

const getTicketItems = ( tickets ) => {
	const items = tickets
		.filter( ( ticket ) => ticket.details.title )
		.map( ( ticket ) => ticket.details.title )
		.join( ', ' );
	return items ? ` (${ items }) ` : '';
};

const getIndependentTicketItems = ( state ) => {
	const independentTickets = selectors.getIndependentTickets( state );
	return getTicketItems( independentTickets );
};

const getSharedTicketItems = ( state ) => {
	const sharedTickets = selectors.getSharedTickets( state );
	return getTicketItems( sharedTickets );
};

const mapStateToProps = ( state ) => ( {
	isSettingsLoading: selectors.getTicketsIsSettingsLoading( state ),
	independentCapacity: selectors.getIndependentTicketsCapacity( state ),
	sharedCapacity: selectors.getTicketsSharedCapacity( state ),
	independentAndSharedCapacity: selectors.getIndependentAndSharedTicketsCapacity( state ),
	independentTicketItems: getIndependentTicketItems( state ),
	sharedTicketItems: getSharedTicketItems( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	onSharedCapacityChange: ( e ) => {
		dispatch( actions.setTicketsSharedCapacity( e.target.value ) );
		dispatch( actions.setTicketsTempSharedCapacity( e.target.value ) );
	},
} );

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
	),
)( CapacityTable );
