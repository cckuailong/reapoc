/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import {
	number,
	TribePropTypes,
} from '@moderntribe/common/utils';

const Bar = ( { children, className, value, total } ) => {
	if ( value === 0 || total === 0 ) {
		return null;
	}

	let valuePercentage;
	try {
		valuePercentage = number.percentage( value, total );
	} catch ( e ) {
		valuePercentage = 0;
	}

	// Prevent numbers above 100 and below 0
	const limitedValuePercentage = Math.max(
		0,
		Math.min( 100, valuePercentage ),
	);

	const style = {
		width: `${ limitedValuePercentage.toFixed( 2 ) }%`,
	};

	return (
		<span
			className={ classNames( 'tribe-editor__quantity-bar__bar', className ) }
			style={ style }
		>
			{ children }
		</span>
	);
};

Bar.propTypes = {
	children: PropTypes.node,
	className: PropTypes.oneOfType( [
		PropTypes.string,
		PropTypes.arrayOf( PropTypes.string ),
		TribePropTypes.nullType,
	] ),
	value: PropTypes.number,
	total: PropTypes.number,
};

Bar.defaultProps = {
	className: null,
	value: 0,
	total: 0,
};

export default Bar;
