/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import TicketsDashboard from './template';
import { selectors } from '@moderntribe/tickets/data/blocks/ticket';
import { withStore } from '@moderntribe/common/hoc';

const mapStateToProps = ( state ) => ( {
	isSettingsOpen: selectors.getTicketsIsSettingsOpen( state ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( TicketsDashboard );
