/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import { ContainerPanel } from '@moderntribe/tickets/elements';
import TicketContainerHeader from '@moderntribe/tickets/blocks/ticket/container-header/template';
import TicketContainerContent from '@moderntribe/tickets/blocks/ticket/container-content/container';
import { LAYOUT } from '@moderntribe/tickets/elements/container-panel/element';
import {
	ClockActive,
	ClockInactive,
	TicketActive,
	TicketInactive,
} from '@moderntribe/tickets/icons';

const ClockIcon = ( { isDisabled } ) => (
	isDisabled ? <ClockInactive /> : <ClockActive />
);

const TicketIcon = ( { isDisabled } ) => (
	isDisabled ? <TicketInactive /> : <TicketActive />
);

const TicketContainerIcon = ( { isDisabled, isFuture, isPast } ) => (
	isFuture || isPast
		? <ClockIcon isDisabled={ isDisabled } />
		: <TicketIcon isDisabled={ isDisabled } />
);

TicketContainerIcon.propTypes = {
	isDisabled: PropTypes.bool.isRequired,
	isFuture: PropTypes.bool,
	isPast: PropTypes.bool,
};

const TicketContainer = ( { clientId, isDisabled, isFuture, isPast, isSelected } ) => (
	<ContainerPanel
		className="tribe-editor__ticket__container"
		layout={ LAYOUT.ticket }
		icon={
			<TicketContainerIcon
				isDisabled={ isDisabled }
				isFuture={ isFuture }
				isPast={ isPast }
			/>
		}
		header={ <TicketContainerHeader clientId={ clientId } isSelected={ isSelected } /> }
		content={ <TicketContainerContent clientId={ clientId } /> }
	/>
);

TicketContainer.propTypes = {
	clientId: PropTypes.string.isRequired,
	isDisabled: PropTypes.bool,
	isFuture: PropTypes.bool,
	isPast: PropTypes.bool,
	isSelected: PropTypes.bool,
};

export default TicketContainer;
