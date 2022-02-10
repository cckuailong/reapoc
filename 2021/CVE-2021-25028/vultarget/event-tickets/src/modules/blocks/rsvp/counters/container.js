/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPCounters from './template';
import { selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const mapStateToProps = ( state ) => ( {
	goingCount: selectors.getRSVPGoingCount( state ),
	notGoingCount: selectors.getRSVPNotGoingCount( state ),
	showNotGoing: selectors.getRSVPNotGoingResponses( state ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( RSVPCounters );
