/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPDuration from './template';
import { selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const mapStateToProps = ( state ) => ( {
	hasDurationError: selectors.getRSVPHasDurationError( state ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( RSVPDuration );
