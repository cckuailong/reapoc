/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * WordPress dependencies
 */
import { select } from '@wordpress/data';

/**
 * Internal dependencies
 */
import AttendeeRegistration from './template';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/ticket';
import { withStore } from '@moderntribe/common/hoc';
import { globals } from '@moderntribe/common/utils';

const getAttendeeRegistrationUrl = ( state, ownProps ) => {
	const adminURL = globals.adminUrl();
	const postType = select( 'core/editor' ).getCurrentPostType();
	const ticketId = selectors.getTicketId( state, ownProps );

	return `${ adminURL }edit.php?post_type=${ postType }&page=attendee-registration&ticket_id=${ ticketId }&tribe_events_modal=1`; // eslint-disable-line max-len
};

const mapStateToProps = ( state, ownProps ) => {
	const isCreated = selectors.getTicketHasBeenCreated( state, ownProps );

	return {
		attendeeRegistrationURL: getAttendeeRegistrationUrl( state, ownProps ),
		hasAttendeeInfoFields: selectors.getTicketHasAttendeeInfoFields( state, ownProps ),
		isCreated,
		isDisabled: selectors.isTicketDisabled( state, ownProps ) || ! isCreated,
		isModalOpen: selectors.getTicketIsModalOpen( state, ownProps ),
	};
};

const mapDispatchToProps = ( dispatch, ownProps ) => {
	return {
		onClick: () => {
			dispatch( actions.setTicketIsModalOpen( ownProps.clientId, true ) );
		},
		onClose: ( e ) => {
			if ( ! e.target.classList.contains( 'components-modal__content' ) ) {
				dispatch( actions.setTicketIsModalOpen( ownProps.clientId, false ) );
			}

			if (
				e.type === 'click' &&
					e.target.classList.contains( 'components-modal__screen-overlay' )
			) {
				dispatch( actions.setTicketIsModalOpen( ownProps.clientId, false ) );
			}
		},
		onIframeLoad: ( iframe ) => {
			const iframeWindow = iframe.contentWindow;

			// show overlay
			const showOverlay = () => {
				iframe
					.nextSibling
					.classList
					.add( 'tribe-editor__attendee-registration__modal-overlay--show' );
			};

			// add event listener for form submit
			const form = iframeWindow.document.querySelector( '#event-tickets-attendee-information' );
			form.addEventListener( 'submit', showOverlay );

			// remove listeners
			const removeListeners = () => {
				iframeWindow.removeEventListener( 'unload', handleUnload ); // eslint-disable-line no-use-before-define,max-len
				form.removeEventListener( 'submit', showOverlay );
			};

			// handle unload on iframe unload
			const handleUnload = () => {
				// remove listeners
				removeListeners( iframeWindow );

				// check if there are meta fields
				const metaFields = iframeWindow
					.document
					.querySelector( '#tribe-tickets-attendee-sortables' );
				const hasFields = Boolean( metaFields.firstElementChild );

				// dispatch actions
				dispatch( actions.setTicketHasAttendeeInfoFields( ownProps.clientId, hasFields ) );
				dispatch( actions.setTicketIsModalOpen( ownProps.clientId, false ) );
			};

			// add handler to iframe window unload
			iframeWindow.addEventListener( 'unload', handleUnload );

			// add target blank to "Learn more" link
			const introLink = iframeWindow.document.querySelector( '.tribe-intro > a' );
			if ( introLink ) {
				introLink.setAttribute( 'target', '_blank' );
			}
		},
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps ),
)( AttendeeRegistration );
