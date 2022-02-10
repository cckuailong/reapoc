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
import RSVPAttendeeRegistration from './template';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';
import { globals } from '@moderntribe/common/utils';

const getAttendeeRegistrationUrl = ( state ) => {
	const adminURL = globals.adminUrl();
	const postType = select( 'core/editor' ).getCurrentPostType();
	const rsvpId = selectors.getRSVPId( state );

	return `${ adminURL }edit.php?post_type=${ postType }&page=attendee-registration&ticket_id=${ rsvpId }&tribe_events_modal=1`; // eslint-disable-line max-len
};

const getIsDisabled = ( state ) => (
	selectors.getRSVPIsLoading( state ) ||
		selectors.getRSVPSettingsOpen( state ) ||
		! selectors.getRSVPCreated( state )
);

const mapStateToProps = ( state ) => ( {
	attendeeRegistrationURL: getAttendeeRegistrationUrl( state ),
	hasAttendeeInfoFields: selectors.getRSVPHasAttendeeInfoFields( state ),
	isCreated: selectors.getRSVPCreated( state ),
	isDisabled: getIsDisabled( state ),
	isModalOpen: selectors.getRSVPIsModalOpen( state ),
} );

const mapDispatchToProps = ( dispatch, ownProps ) => {
	return {
		onClick: () => {
			dispatch( actions.setRSVPIsModalOpen( true ) );
		},
		onClose: ( e ) => {
			if ( ! e.target.classList.contains( 'components-modal__content' ) ) {
				dispatch( actions.setRSVPIsModalOpen( ownProps.clientId, false ) );
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
				dispatch( actions.setRSVPHasAttendeeInfoFields( hasFields ) );
				dispatch( actions.setRSVPIsModalOpen( false ) );
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
)( RSVPAttendeeRegistration );
