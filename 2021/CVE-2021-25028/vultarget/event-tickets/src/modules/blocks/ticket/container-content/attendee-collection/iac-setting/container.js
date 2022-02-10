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
import { globals } from '@moderntribe/common/utils';

const mapStateToProps = ( state, ownProps ) => ( {
	isDisabled: selectors.isTicketDisabled( state, ownProps ),
	iac: selectors.getTicketTempIACSetting( state, ownProps ),
	iacOptions: globals.iacVars().iacOptions,
} );

const mapDispatchToProps = ( dispatch, ownProps ) => ( {
	onChange: ( value ) => {
		const { clientId } = ownProps;
		dispatch( actions.setTicketTempIACSetting( clientId, value ) );
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
