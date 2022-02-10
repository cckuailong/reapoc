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
import { selectors, actions } from '@moderntribe/tickets/data/blocks/ticket';

const mapStateToProps = ( state, ownProps ) => ( {
	isDisabled: selectors.isTicketDisabled( state, ownProps ),
	currencyPosition: selectors.getTicketCurrencyPosition( state, ownProps ),
	currencySymbol: selectors.getTicketCurrencySymbol( state, ownProps ),
	tempPrice: selectors.getTicketTempPrice( state, ownProps ),
	price: selectors.getTicketPrice( state, ownProps ) || '0',
} );

const mapDispatchToProps = ( dispatch, ownProps ) => ( {
	onTempPriceChange: ( e ) => {
		const { clientId } = ownProps;
		dispatch( actions.setTicketTempPrice( clientId, e.target.value ) );
		dispatch( actions.setTicketHasChanges( clientId, true ) );
	},
} );

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
	),
)( Template );
