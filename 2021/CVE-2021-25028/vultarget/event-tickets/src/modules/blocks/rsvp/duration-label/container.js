/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import RSVPDurationLabel from './template';
import { selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';

const getIsDisabled = ( state ) => (
	selectors.getRSVPIsLoading( state ) || selectors.getRSVPSettingsOpen( state )
);

const mapStateToProps = ( state ) => ( {
	isDisabled: getIsDisabled( state ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( RSVPDurationLabel );
