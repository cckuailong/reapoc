/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import Template from './template';
import { withStore } from '@moderntribe/common/hoc';
import { selectors } from '@moderntribe/tickets/data/blocks/ticket';

const getSharedSold = ( state, isShared ) => (
	isShared ? selectors.getSharedTicketsSold( state ) : 0
);

const mapStateToProps = ( state, ownProps ) => {
	const isShared = selectors.isSharedTicket( state, ownProps );

	return {
		isDisabled: selectors.isTicketDisabled( state, ownProps ),
		isShared,
		isUnlimited: selectors.isUnlimitedTicket( state, ownProps ),
		sold: selectors.getTicketSold( state, ownProps ),
		capacity: selectors.getTicketCapacityInt( state, ownProps ),
		sharedSold: getSharedSold( state, isShared ),
		sharedCapacity: selectors.getTicketsSharedCapacityInt( state ),
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( Template );
