/**
 * External dependencies
 */
import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import AutosizeInput from 'react-input-autosize';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	ToggleControl,
	PanelBody,
} from '@wordpress/components';
import { InspectorControls } from '@wordpress/editor';

/**
 * Internal dependencies
 */
import { AttendeesGravatar } from '@moderntribe/tickets/icons';
import './style.pcss';

/**
 * Module Code
 */

const placeholder = __( 'Who\'s Attending?', 'event-tickets' );
const subtitle = __( '(X) people are attending this event', 'event-tickets' );

const renderLabelInput = ( { isSelected, isEmpty, title, setTitle } ) => {
	const containerClassNames = classNames( {
		'tribe-editor__event-attendees__title': true,
		'tribe-editor__event-attendees__title--selected': isSelected,
	} );

	const inputClassNames = classNames( {
		'tribe-editor__event-attendees__title-text': true,
		'tribe-editor__event-attendees__title-text--empty': isEmpty && isSelected,
	} );

	return (
		<div
			key="tribe-events-attendees-label"
			className={ containerClassNames }
		>
			<AutosizeInput
				id="tribe-events-attendees-link"
				className={ inputClassNames }
				value={ title }
				placeholder={ placeholder }
				onChange={ setTitle }
			/>
		</div>
	);
};

const renderPlaceholder = () => {
	const classes = [
		'tribe-editor__event-attendees__title',
		'tribe-editor__event-attendees__title--placeholder',
	];

	return (
		<span className={ classNames( classes ) }>
			{ placeholder }
		</span>
	);
};

const RenderGravatars = () => (
	<div className="tribe-editor__event-attendees__gravatars">
		<AttendeesGravatar />
		<AttendeesGravatar />
		<AttendeesGravatar />
		<AttendeesGravatar />
		<AttendeesGravatar />
	</div>
);

const RenderSubtitle = () => (
	<div className="tribe-editor__event-attendees__subtitle">
		<p>{ subtitle }</p>
	</div>
);

const UI = ( props ) => {
	const { isSelected, title, displayTitle, displaySubtitle } = props;
	const blockTitle = ! ( isSelected || title )
		? renderPlaceholder()
		: renderLabelInput( props );

	return (
		<div className="tribe-editor__block tribe-editor__event-attendees">
			{ displayTitle ? blockTitle : '' }
			{ displaySubtitle ? <RenderSubtitle /> : '' }
			{ <RenderGravatars /> }
		</div>
	);
};

const Controls = ( {
	isSelected,
	displayTitle,
	displaySubtitle,
	onSetDisplayTitleChange,
	onSetDisplaySubtitleChange,
} ) => (
	isSelected && (
		<InspectorControls key="inspector">
			<PanelBody title={ __( 'Attendees Settings', 'event-tickets' ) }>
				<ToggleControl
					label={ __( 'Display Title', 'event-tickets' ) }
					checked={ displayTitle }
					onChange={ onSetDisplayTitleChange }
				/>
				<ToggleControl
					label={ __( 'Display Subtitle', 'event-tickets' ) }
					checked={ displaySubtitle }
					onChange={ onSetDisplaySubtitleChange }
				/>
			</PanelBody>
		</InspectorControls>
	)
);

const Attendees = ( props ) => (
	<Fragment>
		<UI { ...props } />
		<Controls { ...props } />
	</Fragment>
);

Attendees.propTypes = {
	setTitle: PropTypes.func,
	title: PropTypes.string,
	isSelected: PropTypes.bool,
	isEmpty: PropTypes.bool,
	displayTitle: PropTypes.bool,
	displaySubtitle: PropTypes.bool,
	onSetDisplaySubtitleChange: PropTypes.func,
	onSetDisplayTitleChange: PropTypes.func,
};

export default Attendees;
