/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.pcss';

export const LAYOUT = {
	rsvp: 'rsvp',
	ticket: 'ticket',
};

const InactiveBlock = ( {
	className,
	description,
	icon,
	layout,
	title,
} ) => (
	<section className={ classNames(
		'tribe-editor__inactive-block',
		`tribe-editor__inactive-block--${ layout }`,
		className,
	) }>
		<div className="tribe-editor__inactive-block__icon">
			{ icon }
		</div>
		{ ( title || description ) && (
			<div className="tribe-editor__inactive-block__content">
				{ title && <h2 className="tribe-editor__inactive-block__title">{ title }</h2> }
				{ description && (
					<p className="tribe-editor__inactive-block__description">
						{ description }
					</p>
				) }
			</div>
		) }
	</section>
);

InactiveBlock.propTypes = {
	className: PropTypes.string,
	description: PropTypes.string,
	icon: PropTypes.node,
	layout: PropTypes.oneOf( Object.keys( LAYOUT ) ).isRequired,
	title: PropTypes.string,
};

export default InactiveBlock;
