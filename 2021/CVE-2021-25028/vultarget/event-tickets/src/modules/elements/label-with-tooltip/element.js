/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { LabeledItem, Tooltip } from '@moderntribe/common/elements';
import './style.pcss';

const LabelWithTooltip = ( {
	className,
	forId,
	isLabel,
	label,
	tooltipDisabled,
	tooltipLabel,
	tooltipPosition,
	tooltipText,
} ) => (
	<LabeledItem
		className={ classNames( 'tribe-editor__label-with-tooltip', className ) }
		forId={ forId }
		isLabel={ isLabel }
		label={ label }
	>
		<Tooltip
			disabled={ tooltipDisabled }
			label={ tooltipLabel }
			labelClassName="tribe-editor__label-with-tooltip__tooltip-label"
			position={ tooltipPosition }
			text={ tooltipText }
		/>
	</LabeledItem>
);

LabelWithTooltip.defaultProps = {
	label: '',
	tooltipPosition: 'top right',
};

LabelWithTooltip.propTypes = {
	className: PropTypes.string,
	forId: PropTypes.string,
	isLabel: PropTypes.bool,
	label: PropTypes.node,
	tooltipDisabled: PropTypes.bool,
	tooltipLabel: PropTypes.node,
	tooltipPosition: PropTypes.oneOf( [
		'top left',
		'top center',
		'top right',
		'bottom left',
		'bottom center',
		'bottom right',
	] ),
	tooltipText: PropTypes.string,
};

export default LabelWithTooltip;
