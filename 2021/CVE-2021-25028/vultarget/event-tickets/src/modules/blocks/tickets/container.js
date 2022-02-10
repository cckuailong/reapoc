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
import { actions, selectors } from '@moderntribe/tickets/data/blocks/ticket';

const mapStateToProps = ( state ) => {
	const headerImageId = selectors.getTicketsHeaderImageId( state );
	return {
		header: headerImageId ? `${ headerImageId }` : '',
		hasProviders: selectors.hasTicketProviders(),
		isSettingsOpen: selectors.getTicketsIsSettingsOpen( state ),
		provider: selectors.getTicketsProvider( state ),
		sharedCapacity: selectors.getTicketsSharedCapacity( state ),
		canCreateTickets: selectors.canCreateTickets(),
	};
};

const mapDispatchToProps = ( dispatch ) => ( {
	setInitialState: ( props ) => {
		dispatch( actions.setTicketsInitialState( props ) );
	},
	onBlockUpdate: ( isSelected ) => {
		dispatch( actions.setTicketsIsSelected( isSelected ) );
	},
	onBlockRemoved: () => {
		dispatch( actions.resetTicketsBlock() );
	},
} );

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps ),
	withSaveData(),
)( Template );
