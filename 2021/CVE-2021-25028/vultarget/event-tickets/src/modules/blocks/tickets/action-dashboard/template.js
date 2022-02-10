/**
 * External dependencies
 */
import React, { Fragment, PureComponent } from 'react';
import PropTypes from 'prop-types';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import {
	SettingsActionButton,
	AttendeesActionButton,
	OrdersActionButton,
} from '@moderntribe/tickets/blocks/tickets/action-buttons';
import { ActionDashboard, WarningButton } from '@moderntribe/tickets/elements';
import './style.pcss';

const confirmLabel = __( 'Add Tickets', 'event-tickets' );

class TicketsDashboardAction extends PureComponent {
	static propTypes = {
		hasCreatedTickets: PropTypes.bool,
		hasOrdersPage: PropTypes.bool,
		hasRecurrenceRules: PropTypes.bool,
		onConfirmClick: PropTypes.func,
	};

	constructor( props ) {
		super( props );
		this.state = {
			isWarningOpen: false,
		};
	}

	onWarningClick = () => {
		this.setState( { isWarningOpen: ! this.state.isWarningOpen } );
	};

	getActions = () => {
		const {
			hasCreatedTickets,
			hasOrdersPage,
			hasRecurrenceRules,
		} = this.props;

		const actions = [ <SettingsActionButton /> ];
		if ( hasCreatedTickets ) {
			actions.push( <AttendeesActionButton /> );

			if ( hasOrdersPage ) {
				actions.push( <OrdersActionButton /> );
			}
		}
		if ( hasRecurrenceRules ) {
			const icon = this.state.isWarningOpen ? 'no' : 'info-outline';
			const text = this.state.isWarningOpen
				? __( 'Hide Warning', 'event-tickets' )
				: __( 'Warning', 'event-tickets' );
			actions.push(
				<WarningButton
					icon={ icon }
					onClick={ this.onWarningClick }
				>
					{ text }
				</WarningButton>,
			);
		}
		return actions;
	};

	render() {
		const { onConfirmClick } = this.props;

		return (
			<Fragment>
				<ActionDashboard
					className="tribe-editor__tickets__action-dashboard"
					actions={ this.getActions() }
					confirmLabel={ confirmLabel }
					onConfirmClick={ onConfirmClick }
					showCancel={ false }
				/>
				{ this.state.isWarningOpen && (
					<div className="tribe-editor__tickets__warning">
						{ __(
							'This is a recurring event. If you add tickets they will only show up on the next upcoming event in the recurrence pattern. The same ticket form will appear across all events in the series. Please configure your events accordingly.', // eslint-disable-line max-len
							'event-tickets',
						) }
					</div>
				) }
			</Fragment>
		);
	}
}

export default TicketsDashboardAction;
