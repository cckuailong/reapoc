/**
 * External dependencies
 */
import React, { Fragment } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */
import RSVPHeaderImage from '@moderntribe/tickets/blocks/rsvp/header-image/container';
import { SettingsDashboard } from '@moderntribe/tickets/elements';
import './style.pcss';

const RSVPSettingsDashboard = ( { isSettingsLoading, onCloseClick } ) => (
	<SettingsDashboard
		className={ classNames(
			'tribe-editor__rsvp__settings-dashboard',
			{ 'tribe-editor__rsvp__settings-dashboard--loading': isSettingsLoading },
		) }
		closeButtonDisabled={ isSettingsLoading }
		content={ (
			<Fragment>
				<RSVPHeaderImage />
				{ isSettingsLoading && <Spinner /> }
			</Fragment>
		) }
		onCloseClick={ onCloseClick }
	/>
);

RSVPSettingsDashboard.propTypes = {
	isSettingsLoading: PropTypes.bool.isRequired,
	onCloseClick: PropTypes.func.isRequired,
};

export default RSVPSettingsDashboard;
