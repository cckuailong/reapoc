/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPHeaderImage from './template';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

/**
 * Full payload from gutenberg media upload is not used,
 * only id, alt, and src are used for this specific case.
 */

const mapStateToProps = ( state ) => ( {
	image: {
		id: selectors.getRSVPHeaderImageId( state ),
		alt: selectors.getRSVPHeaderImageAlt( state ),
		src: selectors.getRSVPHeaderImageSrc( state ),
	},
	isSettingsLoading: selectors.getRSVPIsSettingsLoading( state ),
} );

const mapDispatchToProps = ( dispatch ) => ( {
	/**
	 * Full payload from gutenberg media upload is not used,
	 * only id, alt, and medium src are used for this specific case.
	 */

	onSelect: ( image ) => dispatch( actions.updateRSVPHeaderImage( image ) ),
	onRemove: () => dispatch( actions.deleteRSVPHeaderImage() ),

} );

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps ),
)( RSVPHeaderImage );
