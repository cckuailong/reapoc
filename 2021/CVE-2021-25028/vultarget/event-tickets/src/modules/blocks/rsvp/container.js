/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';
import moment from 'moment';

/**
 * WordPress dependencies
 */
import { select } from '@wordpress/data';

/**
 * Internal dependencies
 */
import RSVP from './template';
import {
	actions,
	selectors,
	thunks,
} from '@moderntribe/tickets/data/blocks/rsvp';
import {
	isModalShowing,
	getModalTicketId,
} from '@moderntribe/tickets/data/shared/move/selectors';
import { withStore } from '@moderntribe/common/hoc';
import withSaveData from '@moderntribe/tickets/blocks/hoc/with-save-data';
import { moment as momentUtil, time } from '@moderntribe/common/utils';

const getIsInactive = ( state ) => {
	const startDateMoment = selectors.getRSVPStartDateMoment( state );
	const startTime = selectors.getRSVPStartTimeNoSeconds( state );
	const endDateMoment = selectors.getRSVPEndDateMoment( state );
	const endTime = selectors.getRSVPEndTimeNoSeconds( state );

	if ( ! startDateMoment || ! endDateMoment ) {
		return false;
	}

	const startMoment = momentUtil.setTimeInSeconds(
		startDateMoment.clone(),
		time.toSeconds( startTime, time.TIME_FORMAT_HH_MM ),
	);
	const endMoment = momentUtil.setTimeInSeconds(
		endDateMoment.clone(),
		time.toSeconds( endTime, time.TIME_FORMAT_HH_MM ),
	);
	const currentMoment = moment();

	return ! ( currentMoment.isAfter( startMoment ) && currentMoment.isBefore( endMoment ) );
};

const setInitialState = ( dispatch, ownProps ) => () => {
	const postId = select( 'core/editor' ).getCurrentPostId();
	dispatch( thunks.getRSVP( postId ) );
	const { attributes = {} } = ownProps;
	if ( parseInt( attributes.headerImageId, 10 ) ) {
		dispatch( actions.fetchRSVPHeaderImage( attributes.headerImageId ) );
	}
	if ( attributes.goingCount ) {
		dispatch( actions.setRSVPGoingCount( parseInt( attributes.goingCount, 10 ) ) );
	}
	if ( attributes.notGoingCount ) {
		dispatch( actions.setRSVPNotGoingCount(
			parseInt( attributes.notGoingCount, 10 ),
		) );
	}
};

const mapStateToProps = ( state ) => {
	const rsvpId = selectors.getRSVPId( state );

	return {
		created: selectors.getRSVPCreated( state ),
		isInactive: getIsInactive( state ),
		isLoading: selectors.getRSVPIsLoading( state ),
		isModalShowing: isModalShowing( state ) && getModalTicketId( state ) === rsvpId,
		rsvpId,
	};
};

const mapDispatchToProps = ( dispatch, ownProps ) => ( {
	initializeRSVP: () => dispatch( actions.initializeRSVP() ),
	onBlockRemoved: () => dispatch( actions.deleteRSVP() ),
	setInitialState: setInitialState( dispatch, ownProps ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps ),
	withSaveData(),
)( RSVP );
