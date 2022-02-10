/**
 * External dependencies
 */
import React, { Fragment } from 'react';
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

const ContainerPanel = ( {
	className,
	content,
	header,
	icon,
	layout,
} ) => {
	const headerAndContent = (
		<Fragment>
			<div className="tribe-editor__container-panel__header">
				{ header }
			</div>
			{ content && (
				<div className="tribe-editor__container-panel__content">
					{ content }
				</div>
			) }
		</Fragment>
	);

	const getHeaderAndContent = () => (
		layout === LAYOUT.ticket
			? headerAndContent
			: (
				<div className="tribe-editor__container-panel__header-content-wrapper">
					{ headerAndContent }
				</div>
			)
	);

	return (
		<div
			className={ classNames(
				'tribe-editor__container-panel',
				`tribe-editor__container-panel--${ layout }`,
				className,
			) }
		>
			<div className="tribe-editor__container-panel__icon">
				{ icon }
			</div>
			{ getHeaderAndContent() }
		</div>
	);
};

ContainerPanel.propTypes = {
	className: PropTypes.string,
	content: PropTypes.node,
	header: PropTypes.node,
	icon: PropTypes.node,
	layout: PropTypes.oneOf( Object.keys( LAYOUT ) ).isRequired,
};

export default ContainerPanel;
