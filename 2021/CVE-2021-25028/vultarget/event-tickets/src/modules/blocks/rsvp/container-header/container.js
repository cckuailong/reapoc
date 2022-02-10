/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPContainerHeader from './template';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const getIsDisabled = ( state ) => (
	selectors.getRSVPIsLoading( state ) || selectors.getRSVPSettingsOpen( state )
);

const mapStateToProps = ( state ) => ( {
	available: selectors.getRSVPAvailable( state ),
	description: selectors.getRSVPDescription( state ),
	hasAttendeeInfoFields: selectors.getRSVPHasAttendeeInfoFields( state ),
	isCreated: selectors.getRSVPCreated( state ),
	isDisabled: getIsDisabled( state ),
	tempDescription: selectors.getRSVPTempDescription( state ),
	tempTitle: selectors.getRSVPTempTitle( state ),
	title: selectors.getRSVPTitle( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	onTempDescriptionChange: ( e ) => {
		dispatch( actions.setRSVPTempDescription( e.target.value ) );
		dispatch( actions.setRSVPHasChanges( true ) );
	},
	onTempTitleChange: ( e ) => {
		dispatch( actions.setRSVPTempTitle( e.target.value ) );
		dispatch( actions.setRSVPHasChanges( true ) );
	},
} );

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps ),
)( RSVPContainerHeader );
