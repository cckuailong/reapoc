/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * Internal dependencies
 */

/**
 * Generate a label with singular, plural values based on the count provided, the function
 * returns a fallback value (`undefined`) by default when the value is either 0 or lower.
 *
 * Labels need to have a %d on it where the number will be replaced
 *
 * @param {object} props The props passed to this component
 * @param {string | Array | object} props.className The class of the element
 * @param {number} props.count The amount to be compared
 * @param {boolean} props.includeZero If true, zero is included in count
 * @param {string} props.singular The label for the singular case
 * @param {string} props.plural The label for the plural case
 * @param {*} props.fallback The value to be returned if count is zero or negative
 * @param {boolean} props.useFallback If true, fallback is used.
 * @returns {*} return fallback if count is zero or negative otherwise singular or plural
 */
const NumericLabel = ( {
	className,
	count,
	includeZero,
	singular,
	plural,
	fallback,
	useFallback,
} ) => {
	if (
		useFallback &&
		(
			( includeZero && ! ( count >= 0 ) ) ||
			( ! includeZero && ! ( count > 0 ) )
		)
	) {
		return fallback;
	}

	const targetStr = count === 1 ? singular : plural;
	const [ before, after ] = targetStr.split( '%d' );
	return (
		<span className={ classNames( 'tribe-editor__numeric-label', className ) }>
			{ before && <span className="tribe-editor__numeric-label--before">{ before }</span> }
			{ <span className="tribe-editor__numeric-label--count">{ count }</span> }
			{ after && <span className="tribe-editor__numeric-label--after">{ after }</span> }
		</span>
	);
};

NumericLabel.propTypes = {
	className: PropTypes.oneOfType( [
		PropTypes.string,
		PropTypes.arrayOf( PropTypes.string ),
		PropTypes.object,
	] ),
	count: PropTypes.number.isRequired,
	includeZero: PropTypes.bool,
	singular: PropTypes.string,
	plural: PropTypes.string,
	useFallback: PropTypes.any,
};

NumericLabel.defaultProps = {
	count: 0,
	includeZero: false,
	singular: '',
	plural: '',
	className: '',
	fallback: null,
	useFallback: true,
};

export default NumericLabel;
