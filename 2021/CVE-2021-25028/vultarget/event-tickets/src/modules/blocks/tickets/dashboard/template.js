/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import TicketsActionDashboard from '@moderntribe/tickets/blocks/tickets/action-dashboard/container';
import TicketsSettingsDashboard from '@moderntribe/tickets/blocks/tickets/settings-dashboard/container'; // eslint-disable-line max-len

const TicketsDashboard = ( {
	clientId,
	isSelected,
	isSettingsOpen,
} ) => {
	if ( ! isSelected ) {
		return null;
	}

	return ( isSettingsOpen
		? <TicketsSettingsDashboard />
		: <TicketsActionDashboard clientId={ clientId } />
	);
};

TicketsDashboard.propTypes = {
	clientId: PropTypes.string,
	isSelected: PropTypes.bool,
	isSettingsOpen: PropTypes.bool,
};

export default TicketsDashboard;
