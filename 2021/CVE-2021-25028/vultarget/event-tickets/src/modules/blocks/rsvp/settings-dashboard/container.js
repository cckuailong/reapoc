/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPSettingsDashboard from './template';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const mapStateToProps = ( state ) => ( {
	isSettingsLoading: selectors.getRSVPIsSettingsLoading( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	onCloseClick: () => dispatch( actions.setRSVPSettingsOpen( false ) ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps ),
)( RSVPSettingsDashboard );
