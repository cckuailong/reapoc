/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * WordPress dependencies
 */
import { dispatch as wpDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import Template from './template';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/ticket';
import { withStore } from '@moderntribe/common/hoc';

const getIsConfirmDisabled = ( state, ownProps ) => (
	! selectors.isTicketValid( state, ownProps ) ||
		! selectors.getTicketHasChanges( state, ownProps ) ||
		selectors.isTicketDisabled( state, ownProps ) ||
		selectors.getTicketHasDurationError( state, ownProps )
);

const onCancelClick = ( state, dispatch, ownProps ) => () => {
	if ( selectors.getTicketHasBeenCreated( state, ownProps ) ) {
		dispatch( actions.setTicketTempDetails( ownProps.clientId, {
			title: selectors.getTicketTitle( state, ownProps ),
			description: selectors.getTicketDescription( state, ownProps ),
			price: selectors.getTicketPrice( state, ownProps ),
			sku: selectors.getTicketSku( state, ownProps ),
			iac: selectors.getTicketIACSetting( state, ownProps ),
			startDate: selectors.getTicketStartDate( state, ownProps ),
			startDateInput: selectors.getTicketStartDateInput( state, ownProps ),
			startDateMoment: selectors.getTicketStartDateMoment( state, ownProps ),
			endDate: selectors.getTicketEndDate( state, ownProps ),
			endDateInput: selectors.getTicketEndDateInput( state, ownProps ),
			endDateMoment: selectors.getTicketEndDateMoment( state, ownProps ),
			startTime: selectors.getTicketStartTime( state, ownProps ),
			endTime: selectors.getTicketEndTime( state, ownProps ),
			startTimeInput: selectors.getTicketStartTimeInput( state, ownProps ),
			endTimeInput: selectors.getTicketEndTimeInput( state, ownProps ),
			capacityType: selectors.getTicketCapacityType( state, ownProps ),
			capacity: selectors.getTicketCapacity( state, ownProps ),
		} ) );
		dispatch( actions.setTicketsTempSharedCapacity(
			selectors.getTicketsSharedCapacity( state ),
		) );
		dispatch( actions.setTicketHasChanges( ownProps.clientId, false ) );
	} else {
		dispatch( actions.removeTicketBlock( ownProps.clientId ) );
		wpDispatch( 'core/editor' ).removeBlocks( ownProps.clientId );
	}
	wpDispatch( 'core/editor' ).clearSelectedBlock();
};

const onConfirmClick = ( state, dispatch, ownProps ) => () => (
	selectors.getTicketHasBeenCreated( state, ownProps )
		? dispatch( actions.updateTicket( ownProps.clientId ) )
		: dispatch( actions.createNewTicket( ownProps.clientId ) )
);

const mapStateToProps = ( state, ownProps ) => ( {
	hasBeenCreated: selectors.getTicketHasBeenCreated( state, ownProps ),
	isCancelDisabled: selectors.isTicketDisabled( state, ownProps ),
	isConfirmDisabled: getIsConfirmDisabled( state, ownProps ),
	state,
} );

const mergeProps = ( stateProps, dispatchProps, ownProps ) => {
	const { state, ...restStateProps } = stateProps;
	const { dispatch } = dispatchProps;

	return {
		...ownProps,
		...restStateProps,
		onCancelClick: onCancelClick( state, dispatch, ownProps ),
		onConfirmClick: onConfirmClick( state, dispatch, ownProps ),
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps, null, mergeProps ),
)( Template );
