/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { NumberInput } from '@moderntribe/common/elements';
import Row from './row/template';
import './style.pcss';

const CapacityTable = ( {
	isSettingsLoading,
	independentCapacity,
	sharedCapacity,
	independentAndSharedCapacity,
	independentTicketItems,
	sharedTicketItems,
	onSharedCapacityChange,
} ) => {
	const sharedInput = (
		<NumberInput
			onChange={ onSharedCapacityChange }
			value={ sharedCapacity }
			disabled={ isSettingsLoading }
			min={ 0 }
		/>
	);

	return (
		<div className="tribe-editor__tickets__capacity-table">
			<h3 className="tribe-editor__tickets__capacity-table-title">
				{ __( 'Capacity', 'event-tickets' ) }
			</h3>
			<Row
				label={ __( 'Shared capacity', 'event-tickets' ) }
				items={ sharedTicketItems }
				right={ sharedInput }
			/>
			<Row
				label={ __( 'Independent capacity', 'event-tickets' ) }
				items={ independentTicketItems }
				right={ independentCapacity }
			/>
			<Row
				label={ __( 'Total Capacity', 'event-tickets' ) }
				right={ independentAndSharedCapacity }
			/>
		</div>
	);
};

CapacityTable.propTypes = {
	isSettingsLoading: PropTypes.bool,
	independentCapacity: PropTypes.number,
	sharedCapacity: PropTypes.string,
	independentAndSharedCapacity: PropTypes.number,
	independentTicketItems: PropTypes.string,
	sharedTicketItems: PropTypes.string,
	onSharedCapacityChange: PropTypes.func,
};

export default CapacityTable;
