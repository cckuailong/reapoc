/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import Template from './template';
import { withStore } from '@moderntribe/common/hoc';
import { selectors } from '@moderntribe/tickets/data/blocks/ticket';

const mapStateToProps = ( state ) => ( {
	total: selectors.getIndependentAndSharedTicketsCapacity( state ),
	available: selectors.getIndependentAndSharedTicketsAvailable( state ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( Template );

