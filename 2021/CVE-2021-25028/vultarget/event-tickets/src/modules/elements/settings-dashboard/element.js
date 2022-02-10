/**
 * External dependencies
 */
import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { noop } from 'lodash';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Button } from '@moderntribe/common/elements';
import { Close as CloseIcon, Cog as CogIcon } from '@moderntribe/common/icons';
import './style.pcss';

const SettingsDashboard = ( {
	className,
	closeButtonDisabled,
	closeButtonLabel,
	content,
	headerLeft,
	onCloseClick,
} ) => (
	<div className={ classNames(
		'tribe-editor__settings-dashboard',
		className,
	) }>
		<header className="tribe-editor__settings-dashboard__header">
			<span className="tribe-editor__settings-dashboard__header-left">
				{ headerLeft }
			</span>
			<Button
				className="tribe-editor__settings-dashboard__close-button"
				onClick={ onCloseClick }
				disabled={ closeButtonDisabled }
			>
				{ closeButtonLabel }
			</Button>
		</header>
		<div className="tribe-editor__settings-dashboard__content">
			{ content }
		</div>
	</div>
);

SettingsDashboard.defaultProps = {
	closeButtonLabel: (
		<Fragment>
			<CloseIcon />
			<span className="tribe-editor__settings-dashboard__close-button-text">
				{ __( 'close', 'event-tickets' ) }
			</span>
		</Fragment>
	),
	headerLeft: (
		<Fragment>
			<CogIcon />
			<span className="tribe-editor__settings-dashboard__header-left-text">
				{ __( 'Settings', 'event-tickets' ) }
			</span>
		</Fragment>
	),
	onCloseClick: noop,
};

SettingsDashboard.propTypes = {
	className: PropTypes.string,
	closeButtonDisabled: PropTypes.bool,
	closeButtonLabel: PropTypes.node,
	headerLeft: PropTypes.node,
	onCloseClick: PropTypes.func,
	content: PropTypes.node,
};

export default SettingsDashboard;
