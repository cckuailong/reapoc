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

const getHasOverlay = ( state, ownProps ) => (
	selectors.getTicketsIsSettingsOpen( state ) ||
		(
			! selectors.hasATicketSelected( state ) &&
				! ownProps.isSelected
		)
);

const getShowInactiveBlock = ( state, ownProps ) => {
	const showIfBlockIsSelected = ownProps.isSelected && ! selectors.hasTickets( state );
	const showIfBlockIsNotSelected = ! ownProps.isSelected &&
		! selectors.hasATicketSelected( state ) &&
		( ! selectors.hasCreatedTickets( state ) || ! selectors.hasTicketOnSale( state ) );

	return showIfBlockIsSelected || showIfBlockIsNotSelected;
};

const mapStateToProps = ( state, ownProps ) => ( {
	allTicketsPast: selectors.allTicketsPast( state ),
	canCreateTickets: selectors.canCreateTickets(),
	hasCreatedTickets: selectors.hasCreatedTickets( state ),
	hasOverlay: getHasOverlay( state, ownProps ),
	showAvailability: ownProps.isSelected && selectors.hasCreatedTickets( state ),
	showInactiveBlock: getShowInactiveBlock( state, ownProps ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( Template );

