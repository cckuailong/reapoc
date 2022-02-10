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
import withSaveData from '@moderntribe/tickets/blocks/hoc/with-save-data';
import { selectors, actions } from '@moderntribe/tickets/data/blocks/ticket';

const mapStateToProps = ( state ) => ( {
	hasMultipleProviders: selectors.hasMultipleTicketProviders(),
	providers: selectors.getTicketProviders(),
	selectedProvider: selectors.getTicketsProvider( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	onProviderChange: ( e ) => (
		dispatch( actions.setTicketsProvider( e.target.name ) )
	),
} );

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
	),
	withSaveData(),
)( Template );
