/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';
import trim from 'lodash/trim';

/**
 * Internal dependencies
 */
import Template from './template';
import { plugins } from '@moderntribe/common/data';
import { withStore } from '@moderntribe/common/hoc';
import {
	constants,
	actions,
	selectors,
} from '@moderntribe/tickets/data/blocks/ticket';

const {
	UNLIMITED,
	INDEPENDENT,
	TICKET_TYPES,
} = constants;

const mapStateToProps = ( state, ownProps ) => ( {
	hasTicketsPlus: plugins.selectors.hasPlugin( state )( plugins.constants.TICKETS_PLUS ),
	isDisabled: selectors.isTicketDisabled( state, ownProps ),
	sharedCapacity: selectors.getTicketsSharedCapacity( state ),
	tempCapacity: selectors.getTicketTempCapacity( state, ownProps ),
	tempCapacityType: selectors.getTicketTempCapacityType( state, ownProps ),
	tempCapacityTypeOption: selectors.getTicketTempCapacityTypeOption( state, ownProps ),
	tempSharedCapacity: selectors.getTicketsTempSharedCapacity( state ),
} );

const mapDispatchToProps = ( dispatch, ownProps ) => {
	const { clientId } = ownProps;

	return {
		onTempCapacityChange: ( e ) => {
			dispatch( actions.setTicketTempCapacity( clientId, e.target.value ) );
			dispatch( actions.setTicketHasChanges( clientId, true ) );
		},
		onTempCapacityNoPlusChange: ( e ) => {
			const capacity = e.target.value;
			const capacityType = trim( capacity ) === ''
				? TICKET_TYPES[ UNLIMITED ]
				: TICKET_TYPES[ INDEPENDENT ];
			dispatch( actions.setTicketTempCapacityType( clientId, capacityType ) );
			dispatch( actions.setTicketTempCapacity( clientId, capacity ) );
			dispatch( actions.setTicketHasChanges( clientId, true ) );
		},
		onTempCapacityTypeChange: ( selectedOption ) => {
			dispatch( actions.setTicketTempCapacityType( clientId, selectedOption.value ) );
			dispatch( actions.setTicketHasChanges( clientId, true ) );
		},
		onTempSharedCapacityChange: ( e ) => {
			dispatch( actions.setTicketsTempSharedCapacity( e.target.value ) );
			dispatch( actions.setTicketHasChanges( clientId, true ) );
		},
	};
};

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
	),
)( Template );
