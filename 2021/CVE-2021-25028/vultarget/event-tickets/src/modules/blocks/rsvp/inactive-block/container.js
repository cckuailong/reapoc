/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPInactiveBlock from './template';
import { selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const mapStateToProps = ( state ) => ( {
	created: selectors.getRSVPCreated( state ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( RSVPInactiveBlock );
