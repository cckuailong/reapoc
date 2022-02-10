/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import Template from './template';
import { plugins } from '@moderntribe/common/data';
import { withStore } from '@moderntribe/common/hoc';
import withSaveData from '@moderntribe/tickets/blocks/hoc/with-save-data';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/ticket';
import {
	isModalShowing,
	getModalTicketId,
} from '@moderntribe/tickets/data/shared/move/selectors';

const getShowTicket = ( state, ownProps ) => (
	selectors.getTicketsIsSelected( state ) ||
		selectors.hasATicketSelected( state ) ||
		selectors.isTicketOnSale( state, ownProps )
);

const mapStateToProps = ( state, ownProps ) => {
	return {
		hasTicketsPlus: plugins.selectors.hasPlugin( state )( plugins.constants.TICKETS_PLUS ),
		hasBeenCreated: selectors.getTicketHasBeenCreated( state, ownProps ),
		isDisabled: selectors.isTicketDisabled( state, ownProps ),
		isLoading: selectors.getTicketIsLoading( state, ownProps ),
		isModalShowing: isModalShowing( state ),
		modalTicketId: getModalTicketId( state ),
		showTicket: getShowTicket( state, ownProps ),
		ticketId: selectors.getTicketId( state, ownProps ),
	};
};

const mapDispatchToProps = ( dispatch, ownProps ) => {
	const { clientId } = ownProps;

	return {
		onBlockUpdate: ( isSelected ) => (
			dispatch( actions.setTicketIsSelected( clientId, isSelected ) )
		),
		setInitialState: ( props ) => {
			dispatch( actions.registerTicketBlock( clientId ) );
			dispatch( actions.setTicketInitialState( props ) );
		},
	};
};

const mergeProps = ( stateProps, dispatchProps, ownProps ) => ( {
	...stateProps,
	...dispatchProps,
	...ownProps,
	isModalShowing: stateProps.isModalShowing && stateProps.modalTicketId === stateProps.ticketId,
} );

export default compose(
	withStore( { isolated: true } ),
	connect(
		mapStateToProps,
		mapDispatchToProps,
		mergeProps,
	),
	withSaveData(),
)( Template );

