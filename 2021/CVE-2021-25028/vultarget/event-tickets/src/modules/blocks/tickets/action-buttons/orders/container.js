/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Wordpress dependencies
 */
import { select } from '@wordpress/data';

/**
 * Internal dependencies
 */
import AttendeesActionButton from './template';
import { selectors, constants } from '@moderntribe/tickets/data/blocks/ticket';
import { globals } from '@moderntribe/common/utils';
import { withStore } from '@moderntribe/common/hoc';

const mapStateToProps = ( state ) => {
	const adminURL = globals.adminUrl();
	const postType = select( 'core/editor' ).getCurrentPostType();
	const postId = select( 'core/editor' ).getCurrentPostId();
	const provider = selectors.getTicketsProvider( state );
	const page = constants.TICKET_ORDERS_PAGE_SLUG[ provider ];

	return {
		href: page
			? `${ adminURL }edit.php?post_type=${ postType }&page=${ page }&event_id=${ postId }`
			: '',
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( AttendeesActionButton );

