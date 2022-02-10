/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';
import { isEmpty } from 'lodash';

/**
 * Internal dependencies
 */
import Template from './template';
import { plugins } from '@moderntribe/common/data';
import { withStore } from '@moderntribe/common/hoc';
import { globals } from '@moderntribe/common/utils';

const mapStateToProps = ( state ) => ( {
	hasTicketsPlus: plugins.selectors.hasPlugin( state )( plugins.constants.TICKETS_PLUS ),
	hasIacVars: ! isEmpty( globals.iacVars() ),
} );

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( Template );

