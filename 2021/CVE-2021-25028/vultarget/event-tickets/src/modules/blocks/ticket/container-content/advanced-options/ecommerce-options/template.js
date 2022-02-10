/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * Wordpress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { LabeledItem, Link } from '@moderntribe/common/elements';
import { constants } from '@moderntribe/tickets/data/blocks/ticket';
import './style.pcss';

const { EDD, WOO, PROVIDER_TYPES } = constants;
const EDIT_TICKET = 'edit-ticket';
const REPORT = 'report';
const LINK_TYPES = [ EDIT_TICKET, REPORT ];

const EcommerceOptions = ( {
	editTicketLink,
	isDisabled,
	provider,
	reportLink,
	showEcommerceOptions,
} ) => {
	const getEditTicketLinkLabel = ( ticketProvider ) => {
		let label = '';

		if ( ticketProvider === EDD ) {
			label = __( 'Edit Ticket in Easy Digital Downloads', 'event-tickets' );
		} else if ( ticketProvider === WOO ) {
			label = __( 'Edit Ticket in WooCommerce', 'event-tickets' );
		}

		return label;
	};

	const getLink = ( linkType ) => {
		const className = classNames(
			'tribe-editor__ticket__ecommerce-options-link',
			`tribe-editor__ticket__ecommerce-options-link--${ linkType }`,
		);
		const href = linkType === REPORT ? reportLink : editTicketLink;
		const label = linkType === REPORT
			? __( 'View Sales Report', 'event-tickets' )
			: getEditTicketLinkLabel( provider );

		return (
			isDisabled
				? <span className={ className }>{ label }</span>
				: (
					<Link
						className={ className }
						href={ href }
						target="_blank"
					>
						{ label }
					</Link>
				)
		);
	};

	return (
		showEcommerceOptions &&
			(
				<LabeledItem
					className={ classNames(
						'tribe-editor__ticket__ecommerce-options',
						'tribe-editor__ticket__content-row',
						'tribe-editor__ticket__content-row--ecommerce-options',
					) }
					label={ __( 'Ecommerce', 'event-tickets' ) }
				>
					<div className="tribe-editor__ticket__ecommerce-options-links">
						{ LINK_TYPES.map( ( linkType ) => (
							<span
								key={ linkType }
								className="tribe-editor__ticket__ecommerce-options-link-wrapper"
							>
								{ getLink( linkType ) }
							</span>
						) ) }
					</div>
				</LabeledItem>
			)
	);
};

EcommerceOptions.propTypes = {
	editTicketLink: PropTypes.string,
	isDisabled: PropTypes.bool,
	provider: PropTypes.oneOf( [ ...PROVIDER_TYPES, '' ] ),
	reportLink: PropTypes.string,
	showEcommerceOptions: PropTypes.bool,
};

export default EcommerceOptions;
