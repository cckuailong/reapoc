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
	hasAttendeeInfoFields: selectors.getTicketHasAttendeeInfoFields( state, ownProps ),
	isDisabled: selectors.isTicketDisabled( state, ownProps ),
	tempTitle: selectors.getTicketTempTitle( state, ownProps ),
	title: selectors.getTicketTitle( state, ownProps ),
} );

const mapDispatchToProps = ( dispatch, ownProps ) => ( {
	onTempTitleChange: ( e ) => {
		const { clientId } = ownProps;
		dispatch( actions.setTicketTempTitle( clientId, e.target.value ) );
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
