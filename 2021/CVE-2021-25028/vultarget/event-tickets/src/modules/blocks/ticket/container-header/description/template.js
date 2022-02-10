/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import TextareaAutosize from 'react-textarea-autosize';

/**
 * Wordpress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './style.pcss';

const TicketContainerHeaderDescription = ( {
	isDisabled,
	isSelected,
	onTempDescriptionChange,
	tempDescription,
	description,
} ) => (
	isSelected
		? (
			<TextareaAutosize
				className="tribe-editor__ticket__container-header-description-input"
				value={ tempDescription }
				placeholder={ __( 'Description', 'event-tickets' ) }
				onChange={ onTempDescriptionChange }
				disabled={ isDisabled }
			/>
		)
		: (
			<span className="tribe-editor__ticket__container-header-description">
				{ description }
			</span>
		)
);

TicketContainerHeaderDescription.propTypes = {
	isDisabled: PropTypes.bool,
	isSelected: PropTypes.bool,
	onTempDescriptionChange: PropTypes.func,
	tempDescription: PropTypes.string,
	description: PropTypes.string,
};

export default TicketContainerHeaderDescription;
