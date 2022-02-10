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
import AttendeesActionButton from './template';
import { selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';
import { globals } from '@moderntribe/common/utils';

const mapStateToProps = ( state ) => {
	const adminURL = globals.adminUrl();
	const postType = select( 'core/editor' ).getCurrentPostType();
	const postId = select( 'core/editor' ).getCurrentPostId();

	return {
		href: `${ adminURL }edit.php?post_type=${ postType }&page=tickets-attendees&event_id=${ postId }`, // eslint-disable-line max-len
		isDisabled: selectors.getRSVPIsLoading( state ),
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( AttendeesActionButton );
