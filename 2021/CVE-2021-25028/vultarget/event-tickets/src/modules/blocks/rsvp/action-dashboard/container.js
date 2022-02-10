/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * WordPress dependencies
 */
import { select, dispatch as wpDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import RSVPActionDashboard from './template';
import { plugins } from '@moderntribe/common/data';
import { actions, selectors, thunks } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const getHasRecurrenceRules = ( state ) => {
	let hasRules = false;
	try {
		hasRules = window.tribe[ plugins.constants.EVENTS_PRO_PLUGIN ]
			.data.blocks.recurring.selectors.hasRules( state );
	} catch ( e ) {
		// ¯\_(ツ)_/¯
	}
	return hasRules;
};

const getIsConfirmDisabled = ( state ) => (
	! selectors.getRSVPTempTitle( state ) ||
		! selectors.getRSVPHasChanges( state ) ||
		selectors.getRSVPIsLoading( state ) ||
		selectors.getRSVPHasDurationError( state )
);

const onCancelClick = ( state, dispatch ) => () => {
	dispatch( actions.setRSVPTempDetails( {
		tempTitle: selectors.getRSVPTitle( state ),
		tempDescription: selectors.getRSVPDescription( state ),
		tempCapacity: selectors.getRSVPCapacity( state ),
		tempNotGoingResponses: selectors.getRSVPNotGoingResponses( state ),
		tempStartDate: selectors.getRSVPStartDate( state ),
		tempStartDateInput: selectors.getRSVPStartDateInput( state ),
		tempStartDateMoment: selectors.getRSVPStartDateMoment( state ),
		tempEndDate: selectors.getRSVPEndDate( state ),
		tempEndDateInput: selectors.getRSVPEndDateInput( state ),
		tempEndDateMoment: selectors.getRSVPEndDateMoment( state ),
		tempStartTime: selectors.getRSVPStartTime( state ),
		tempEndTime: selectors.getRSVPEndTime( state ),
		tempStartTimeInput: selectors.getRSVPStartTimeInput( state ),
		tempEndTimeInput: selectors.getRSVPEndTimeInput( state ),
	} ) );
	dispatch( actions.setRSVPHasChanges( false ) );
	wpDispatch( 'core/editor' ).clearSelectedBlock();
};

const onConfirmClick = ( state, dispatch ) => () => {
	const payload = {
		title: selectors.getRSVPTempTitle( state ),
		description: selectors.getRSVPTempDescription( state ),
		capacity: selectors.getRSVPTempCapacity( state ),
		notGoingResponses: selectors.getRSVPTempNotGoingResponses( state ),
		startDate: selectors.getRSVPTempStartDate( state ),
		startDateInput: selectors.getRSVPTempStartDateInput( state ),
		startDateMoment: selectors.getRSVPTempStartDateMoment( state ),
		endDate: selectors.getRSVPTempEndDate( state ),
		endDateInput: selectors.getRSVPTempEndDateInput( state ),
		endDateMoment: selectors.getRSVPTempEndDateMoment( state ),
		startTime: selectors.getRSVPTempStartTime( state ),
		endTime: selectors.getRSVPTempEndTime( state ),
		startTimeInput: selectors.getRSVPTempStartTimeInput( state ),
		endTimeInput: selectors.getRSVPTempEndTimeInput( state ),
	};

	if ( ! selectors.getRSVPCreated( state ) ) {
		dispatch( thunks.createRSVP( {
			...payload,
			postId: select( 'core/editor' ).getCurrentPostId(),
		} ) );
	} else {
		dispatch( thunks.updateRSVP( {
			...payload,
			id: selectors.getRSVPId( state ),
		} ) );
	}
};

const mapStateToProps = ( state ) => ( {
	created: selectors.getRSVPCreated( state ),
	hasRecurrenceRules: getHasRecurrenceRules( state ),
	isCancelDisabled: selectors.getRSVPIsLoading( state ),
	isConfirmDisabled: getIsConfirmDisabled( state ),
	isLoading: selectors.getRSVPIsLoading( state ),
	showCancel: selectors.getRSVPCreated( state ),
	state,
} );

const mergeProps = ( stateProps, dispatchProps, ownProps ) => {
	const { state, ...restStateProps } = stateProps;
	const { dispatch } = dispatchProps;

	return {
		...ownProps,
		...restStateProps,
		onCancelClick: onCancelClick( state, dispatch ),
		onConfirmClick: onConfirmClick( state, dispatch, ownProps ),
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps, null, mergeProps ),
)( RSVPActionDashboard );
