/**
 * External dependencies
 */
import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import TextareaAutosize from 'react-textarea-autosize';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import RSVPCounters from '@moderntribe/tickets/blocks/rsvp/counters/container';
import { NumericLabel } from '@moderntribe/tickets/elements';
import { Tooltip } from '@moderntribe/common/elements';
import { Clipboard } from '@moderntribe/common/icons';
import './style.pcss';

const clipboard = (
	<Tooltip
		labelClassName="tribe-editor__ticket__container-header-clipboard-tooltip"
		label={ <Clipboard /> }
		text={ __(
			'This ticket has Attendee Information Fields configured.',
			'event-tickets',
		) }
	/>
);

const getTitle = (
	hasAttendeeInfoFields,
	isDisabled,
	isSelected,
	onTempTitleChange,
	tempTitle,
	title,
) => (
	isSelected
		? (
			<div className="tribe-editor__rsvp-container-header__title-input-wrapper">
				<TextareaAutosize
					className="tribe-editor__rsvp-container-header__title-input"
					value={ tempTitle }
					placeholder={ __( 'RSVP Title', 'event-tickets' ) }
					onChange={ onTempTitleChange }
					disabled={ isDisabled }
				/>
				{ hasAttendeeInfoFields && clipboard }
			</div>
		)
		: <h2 className="tribe-editor__rsvp-container-header__title">{ title }</h2>
);

const getDescription = (
	isDisabled,
	isSelected,
	onTempDescriptionChange,
	tempDescription,
	description,
) => (
	isSelected
		? (
			<TextareaAutosize
				className="tribe-editor__rsvp-container-header__description-input"
				value={ tempDescription }
				placeholder={ __( 'RSVP description', 'event-tickets' ) }
				onChange={ onTempDescriptionChange }
				disabled={ isDisabled }
			/>
		)
		: description && (
			<span className="tribe-editor__rsvp-container-header__description">
				{ description }
			</span>
		)
);

const getCapacityLabel = ( capacity ) => {
	// todo: should use _n to be translator friendly
	const singular = __( '%d available', 'event-tickets' );
	const plural = singular;
	const fallback = (
		<span className="tribe-editor__rsvp-container-header__capacity-label-fallback">
			{ __( 'Unlimited', 'event-tickets' ) }
		</span>
	);

	return (
		<NumericLabel
			className="tribe-editor__rsvp-container-header__capacity-label"
			count={ capacity }
			includeZero={ true }
			singular={ singular }
			plural={ plural }
			fallback={ fallback }
		/>
	);
};

const RSVPContainerHeader = ( {
	description,
	hasAttendeeInfoFields,
	isCreated,
	isDisabled,
	isSelected,
	onTempDescriptionChange,
	onTempTitleChange,
	tempDescription,
	tempTitle,
	title,
	available,
} ) => {
	return (
		<Fragment>
			<div className="tribe-editor__rsvp-container-header__header-details">
				{ getTitle(
					hasAttendeeInfoFields,
					isDisabled,
					isSelected,
					onTempTitleChange,
					tempTitle,
					title,
				) }
				{ getDescription(
					isDisabled,
					isSelected,
					onTempDescriptionChange,
					tempDescription,
					description,
				) }
				{ isCreated && getCapacityLabel( available ) }
			</div>
			<RSVPCounters />
		</Fragment>
	);
};

RSVPContainerHeader.propTypes = {
	available: PropTypes.number,
	description: PropTypes.string,
	hasAttendeeInfoFields: PropTypes.bool,
	isCreated: PropTypes.bool,
	isDisabled: PropTypes.bool.isRequired,
	isSelected: PropTypes.bool.isRequired,
	onTempDescriptionChange: PropTypes.func,
	onTempTitleChange: PropTypes.func,
	tempDescription: PropTypes.string,
	tempTitle: PropTypes.string,
	title: PropTypes.string,
};

export default RSVPContainerHeader;
