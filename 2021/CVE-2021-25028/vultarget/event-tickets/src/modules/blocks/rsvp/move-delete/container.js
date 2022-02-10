/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';
import { dispatch as wpDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Template from './template';

import { withStore } from '@moderntribe/common/hoc';
import {
	actions,
	selectors,
	thunks,
} from '@moderntribe/tickets/data/blocks/rsvp';
import {
	showModal,
} from '@moderntribe/tickets/data/shared/move/actions';

const mapStateToProps = ( state ) => ( {
	created: selectors.getRSVPCreated( state ),
	rsvpId: selectors.getRSVPId( state ),
	isDisabled: selectors.getRSVPIsLoading( state ) || selectors.getRSVPSettingsOpen( state ),
} );

const mapDispatchToProps = ( dispatch, ownProps ) => ( {
	moveRSVP: ( rsvpId ) => dispatch( showModal( rsvpId, ownProps.clientId ) ),
	dispatch,
} );

const mergeProps = ( stateProps, dispatchProps, ownProps ) => {
	const { dispatch, ...restDispatchProps } = dispatchProps;

	return {
		...ownProps,
		...stateProps,
		...restDispatchProps,
		removeRSVP: () => {
			if ( window.confirm( // eslint-disable-line no-alert
				__( 'Are you sure you want to delete this RSVP? It cannot be undone.', 'event-tickets' ),
			) ) {
				dispatch( actions.deleteRSVP() );
				if ( stateProps.created && stateProps.rsvpId ) {
					dispatch( thunks.deleteRSVP( stateProps.rsvpId ) );
				}
				wpDispatch( 'core/editor' ).removeBlocks( [ ownProps.clientId ] );
			}
		},
		moveRSVP: () => dispatchProps.moveRSVP( stateProps.rsvpId ),
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps, mergeProps ),
)( Template );
