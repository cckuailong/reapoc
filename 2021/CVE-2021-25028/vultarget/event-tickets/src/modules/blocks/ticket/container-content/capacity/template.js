/**
 * External dependencies
 */
import React, { Fragment, PureComponent } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { includes } from 'lodash';
import uniqid from 'uniqid';

/**
 * Wordpress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Dashicon } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { constants, options } from '@moderntribe/tickets/data/blocks/ticket';
import { LabeledItem, NumberInput, Select } from '@moderntribe/common/elements';
import { LabelWithTooltip } from '@moderntribe/tickets/elements';
import { ReactSelectOption } from '@moderntribe/common/data/plugins/proptypes';
import './style.pcss';

const {
	INDEPENDENT,
	SHARED,
	TICKET_TYPES,
} = constants;
const { CAPACITY_TYPE_OPTIONS } = options;

// Custom input for this type of form
const LabeledNumberInput = ( {
	className,
	id,
	label,
	...props
} ) => (
	<LabeledItem
		className={ classNames(
			'tribe-editor__labeled-number-input',
			className,
		) }
		forId={ id }
		label={ label }
		isLabel={ true }
	>
		<NumberInput { ...props } />
	</LabeledItem>
);

LabeledNumberInput.propTypes = {
	className: PropTypes.string,
	id: PropTypes.string,
	label: PropTypes.string,
};

class Capacity extends PureComponent {
	static propTypes = {
		hasTicketsPlus: PropTypes.bool,
		isDisabled: PropTypes.bool,
		sharedCapacity: PropTypes.string,
		tempCapacity: PropTypes.string,
		tempCapacityType: PropTypes.string,
		tempCapacityTypeOption: ReactSelectOption,
		tempSharedCapacity: PropTypes.string,
		onTempCapacityChange: PropTypes.func,
		onTempCapacityNoPlusChange: PropTypes.func,
		onTempCapacityTypeChange: PropTypes.func,
		onTempSharedCapacityChange: PropTypes.func,
	};

	constructor( props ) {
		super( props );
		this.ids = {
			select: uniqid( 'capacity-type-' ),
			capacity: uniqid( 'capacity-' ),
			sharedCapacity: uniqid( 'shared-capacity-' ),
		};
	}

	getInputs = () => {
		const {
			isDisabled,
			sharedCapacity,
			tempCapacityType,
			tempCapacity,
			tempSharedCapacity,
			onTempCapacityChange,
			onTempSharedCapacityChange,
		} = this.props;

		const inputs = [];

		// If capacity type is shared and does not have shared capacity
		if ( tempCapacityType === TICKET_TYPES[ SHARED ] && sharedCapacity === '' ) {
			inputs.push(
				<LabeledNumberInput
					key="shared-capacity"
					className={ classNames(
						'tribe-editor__ticket__capacity-input-row',
						'tribe-editor__ticket__capacity-input-row--shared-capacity',
					) }
					id={ this.ids.sharedCapacity }
					label={ __( 'Set shared capacity:', 'event-tickets' ) }
					value={ tempSharedCapacity }
					onChange={ onTempSharedCapacityChange }
					disabled={ isDisabled }
					min={ 0 }
					required={ true }
				/>,
			);
		}

		// If capacity type is shared or independent
		if ( includes(
			[ TICKET_TYPES[ SHARED ], TICKET_TYPES[ INDEPENDENT ] ],
			tempCapacityType,
		) ) {
			const extraProps = {};
			const ticketType = tempCapacityType === TICKET_TYPES[ SHARED ] ? SHARED : INDEPENDENT;

			if (
				tempCapacityType === TICKET_TYPES[ SHARED ] &&
					( sharedCapacity || tempSharedCapacity )
			) {
				const max = sharedCapacity ? sharedCapacity : tempSharedCapacity;
				extraProps.max = parseInt( max, 10 ) || 0;
			}

			if ( tempCapacityType === TICKET_TYPES[ INDEPENDENT ] ) {
				extraProps.required = true;
			}

			extraProps.label = tempCapacityType === TICKET_TYPES[ SHARED ]
				? __( '(optional) Limit sales of this ticket to:', 'event-tickets' )
				: __( 'Number of tickets available', 'event-tickets' );

			inputs.push(
				<LabeledNumberInput
					key="capacity"
					className={ classNames(
						'tribe-editor__ticket__capacity-input-row',
						'tribe-editor__ticket__capacity-input-row--capacity',
						`tribe-editor__ticket__capacity-input-row--capacity-${ ticketType }`,
					) }
					id={ this.ids.capacity }
					value={ tempCapacity }
					onChange={ onTempCapacityChange }
					disabled={ isDisabled }
					min={ 0 }
					{ ...extraProps }
				/>,
			);
		}

		return inputs;
	};

	getCapacityForm = () => {
		const {
			isDisabled,
			tempCapacityTypeOption,
			onTempCapacityTypeChange,
		} = this.props;

		return (
			<Fragment>
				<Select
					id={ this.ids.select }
					className="tribe-editor__ticket__capacity-type-select"
					backspaceRemovesValue={ false }
					value={ tempCapacityTypeOption }
					isSearchable={ false }
					isDisabled={ isDisabled }
					options={ CAPACITY_TYPE_OPTIONS }
					onChange={ onTempCapacityTypeChange }
				/>
				{ this.getInputs() }
			</Fragment>
		);
	};

	getNoPlusCapacityForm = () => {
		const {
			isDisabled,
			tempCapacity,
			onTempCapacityNoPlusChange,
		} = this.props;

		return (
			<Fragment>
				<NumberInput
					className="tribe-editor__ticket__capacity-input"
					id={ this.ids.capacity }
					value={ tempCapacity }
					onChange={ onTempCapacityNoPlusChange }
					disabled={ isDisabled }
					min={ 0 }
				/>
				<span className="tribe-editor__ticket__capacity-input-helper-text">
					{ __( 'Leave blank for unlimited', 'event-tickets' ) }
				</span>
			</Fragment>
		);
	};

	render() {
		const { hasTicketsPlus } = this.props;

		return (
			<div className={ classNames(
				'tribe-editor__ticket__capacity',
				'tribe-editor__ticket__content-row',
				'tribe-editor__ticket__content-row--capacity',
			) }>
				<LabelWithTooltip
					className="tribe-editor__ticket__capacity-label-with-tooltip"
					forId={ hasTicketsPlus ? this.ids.select : this.ids.capacity }
					isLabel={ true }
					label={ __( 'Ticket Capacity', 'event-tickets' ) }
					tooltipText={ __(
						'Ticket capacity will only be used by attendees buying this ticket type',
						'event-tickets',
					) }
					tooltipLabel={
						<Dashicon
							className="tribe-editor__ticket__tooltip-label"
							icon="info-outline"
						/>
					}
				/>
				<div className="tribe-editor__ticket__capacity-form">
					{ hasTicketsPlus ? this.getCapacityForm() : this.getNoPlusCapacityForm() }
				</div>
			</div>
		);
	}
}

export default Capacity;
