/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';

/**
 * Wordpress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import QuantityBar from './quantity-bar/element';
import './style.pcss';

const TicketContainerHeaderDescription = ( {
	isDisabled,
	isSelected,
	isShared,
	isUnlimited,
	sold,
	capacity,
	sharedSold,
	sharedCapacity,
} ) => {
	const total = isShared ? sharedCapacity : capacity;

	const getLabel = () => (
		isUnlimited
			? sprintf( __( '%d sold', 'event-tickets' ), sold )
			: sprintf( __( '%d of %d sold', 'event-tickets' ), sold, total )
	);

	const getQuantityBar = () => (
		isUnlimited
			? (
				<span className="tribe-editor__ticket__container-header-quantity-unlimited">
					{ __( 'unlimited', 'event-tickets' ) }
				</span>
			)
			: (
				<QuantityBar
					sold={ sold }
					sharedSold={ sharedSold }
					capacity={ capacity }
					total={ total }
					isDisabled={ isDisabled }
				/>
			)
	);

	return ! isSelected && (
		<div
			className="tribe-editor__ticket__container-header-quantity tribe-tooltip"
			title={ __( 'This pertains to Orders that have been marked Completed.', 'event-tickets' ) }
		>
			<span className="tribe-editor__ticket__container-header-quantity-label">
				{ getLabel() }<span class="dashicons dashicons-info"></span>
			</span>
			{ getQuantityBar() }
		</div>
	);
};

TicketContainerHeaderDescription.propTypes = {
	isDisabled: PropTypes.bool,
	isSelected: PropTypes.bool,
	isShared: PropTypes.bool,
	isUnlimited: PropTypes.bool,
	sold: PropTypes.number,
	capacity: PropTypes.number,
	sharedSold: PropTypes.number,
	sharedCapacity: PropTypes.number,
};

TicketContainerHeaderDescription.defaultProps = {
	sold: 0,
	sharedCapacity: 0,
	capacity: 0,
};

export default TicketContainerHeaderDescription;
