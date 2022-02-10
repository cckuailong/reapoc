/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import { withStore } from '@moderntribe/common/hoc';
import withSaveData from '@moderntribe/tickets/blocks/hoc/with-save-data';
import * as actions from '@moderntribe/tickets/data/blocks/attendees/actions';
import * as selectors from '@moderntribe/tickets/data/blocks/attendees/selectors';
import Attendees from './template';

/**
 * Module Code
 */

const mapStateToProps = ( state ) => ( {
	title: selectors.getTitle( state ),
	displayTitle: selectors.getDisplayTitle( state ),
	displaySubtitle: selectors.getDisplaySubtitle( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	setInitialState: ( props ) => dispatch( actions.setInitialState( props ) ),
	setTitle: ( e ) => dispatch( actions.setTitle( e.target.value ) ),
	onSetDisplayTitleChange: ( checked ) => ( dispatch( actions.setDisplayTitle( checked ) ) ),
	onSetDisplaySubtitleChange: ( checked ) => ( dispatch( actions.setDisplaySubtitle( checked ) ) ),
} );

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
	),
	withSaveData(),
)( Attendees );
