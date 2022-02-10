/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPDashboard from './template';
import { selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const mapStateToProps = ( state ) => ( {
	isSettingsOpen: selectors.getRSVPSettingsOpen( state ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( RSVPDashboard );
