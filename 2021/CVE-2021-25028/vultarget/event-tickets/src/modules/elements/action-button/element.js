/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import { Button, Link } from '@moderntribe/common/elements';
import './style.pcss';

export const positions = {
	right: 'right',
	left: 'left',
};

const components = {
	button: Button,
	link: Link,
};

const ActionButton = ( {
	asLink,
	children,
	className,
	disabled,
	href,
	icon,
	onClick,
	position,
	target,
	...props
} ) => {
	const containerClass = classNames(
		'tribe-editor__action-button',
		`tribe-editor__action-button--icon-${ position }`,
		className,
	);

	const Element = asLink && ! disabled ? components.link : components.button;

	const getProps = () => {
		const elemProps = { ...props };

		if ( asLink && ! disabled ) {
			elemProps.href = href;
			elemProps.target = target;
		} else {
			elemProps.disabled = disabled;
			elemProps.onClick = onClick;
		}

		return elemProps;
	};

	return (
		<Element
			className={ containerClass }
			{ ...getProps() }
		>
			{ icon }
			<span className="tribe-editor__action-button__label">{ children }</span>
		</Element>
	);
};

ActionButton.propTypes = {
	asLink: PropTypes.bool,
	children: PropTypes.node,
	className: PropTypes.string,
	disabled: PropTypes.bool,
	href: PropTypes.string,
	icon: PropTypes.node.isRequired,
	onClick: PropTypes.func,
	position: PropTypes.oneOf( Object.keys( positions ) ),
	target: PropTypes.string,
};

ActionButton.defaultProps = {
	asLink: false,
	position: positions.left,
};

export default ActionButton;
