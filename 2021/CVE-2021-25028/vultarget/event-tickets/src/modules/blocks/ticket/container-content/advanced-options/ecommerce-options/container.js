/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';
import includes from 'lodash/includes';

/**
 * Internal dependencies
 */
import EcommerceOptions from './template';
import { constants, selectors } from '@moderntribe/tickets/data/blocks/ticket';
import { withStore } from '@moderntribe/common/hoc';
import { globals } from '@moderntribe/common/utils';

const { EDD, WOO } = constants;

const showEcommerceOptions = ( provider ) => includes( [ EDD, WOO ], provider );

const getEditTicketLink = ( state, ownProps, provider ) => {
	let editTicketLink = '';

	if ( showEcommerceOptions( provider ) ) {
		const adminURL = globals.adminUrl();
		const ticketId = selectors.getTicketId( state, ownProps );
		editTicketLink = `${ adminURL }post.php?post=${ ticketId }&action=edit`;
	}

	return editTicketLink;
};

const getReportLink = ( state, ownProps, provider ) => {
	let reportLink = '';

	if ( showEcommerceOptions( provider ) ) {
		const adminURL = globals.adminUrl();
		const ticketId = selectors.getTicketId( state, ownProps );
		let path = '';

		if ( provider === EDD ) {
			path = `edit.php?page=edd-reports&view=sales&post_type=download&tab=logs&download=${ ticketId }`; // eslint-disable-line max-len
		} else if ( provider === WOO ) {
			path = `admin.php?page=wc-reports&tab=orders&report=sales_by_product&product_ids=${ ticketId }`; // eslint-disable-line max-len
		}

		reportLink = `${ adminURL }${ path }`;
	}

	return reportLink;
};

const mapStateToProps = ( state, ownProps ) => {
	const provider = selectors.getTicketProvider( state, ownProps );

	return {
		isDisabled: selectors.isTicketDisabled( state, ownProps ),
		provider,
		editTicketLink: getEditTicketLink( state, ownProps, provider ),
		reportLink: getReportLink( state, ownProps, provider ),
		showEcommerceOptions: showEcommerceOptions( provider ),
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps ),
)( EcommerceOptions );
