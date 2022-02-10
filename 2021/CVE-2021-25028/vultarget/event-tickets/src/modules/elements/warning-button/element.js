/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { Dashicon } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { Button } from '@moderntribe/common/elements';
import './style.pcss';

const WarningButton = ( {
	children,
	className,
	icon,
	...props
} ) => {
	return (
		<Button
			className={ classNames( 'tribe-editor__warning-button', className ) }
			{ ...props }
		>
			<Dashicon
				className="tribe-editor__warning-button-icon"
				icon={ icon }
			/>
			<span className="tribe-editor__warning-button-text">
				{ children }
			</span>
		</Button>
	);
};

WarningButton.propTypes = {
	className: PropTypes.string,
	icon: PropTypes.string.isRequired,
};

export default WarningButton;
