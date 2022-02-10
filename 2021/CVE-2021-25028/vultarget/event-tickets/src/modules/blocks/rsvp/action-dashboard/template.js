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
} from '@moderntribe/tickets/blocks/rsvp/action-buttons';
import { ActionDashboard, WarningButton } from '@moderntribe/tickets/elements';
import './style.pcss';

const confirmLabel = ( created ) => (
	created
		? __( 'Update RSVP', 'event-tickets' )
		: __( 'Create RSVP', 'event-tickets' )
);

const cancelLabel = __( 'Cancel', 'event-tickets' );

class RSVPActionDashboard extends PureComponent {
	static propTypes = {
		created: PropTypes.bool.isRequired,
		hasRecurrenceRules: PropTypes.bool.isRequired,
		isCancelDisabled: PropTypes.bool.isRequired,
		isConfirmDisabled: PropTypes.bool.isRequired,
		isLoading: PropTypes.bool.isRequired,
		onCancelClick: PropTypes.func.isRequired,
		onConfirmClick: PropTypes.func.isRequired,
		showCancel: PropTypes.bool.isRequired,
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
			created,
			hasRecurrenceRules,
			isLoading,
		} = this.props;

		const actions = [ <SettingsActionButton /> ];
		if ( created ) {
			actions.push( <AttendeesActionButton /> );
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
					isDisabled={ isLoading }
				>
					{ text }
				</WarningButton>,
			);
		}
		return actions;
	}

	render() {
		const {
			created,
			isCancelDisabled,
			isConfirmDisabled,
			onCancelClick,
			onConfirmClick,
			showCancel,
		} = this.props;

		/* eslint-disable max-len */
		return (
			<Fragment>
				<ActionDashboard
					className="tribe-editor__rsvp__action-dashboard"
					actions={ this.getActions() }
					cancelLabel={ cancelLabel }
					confirmLabel={ confirmLabel( created ) }
					isCancelDisabled={ isCancelDisabled }
					isConfirmDisabled={ isConfirmDisabled }
					onCancelClick={ onCancelClick }
					onConfirmClick={ onConfirmClick }
					showCancel={ showCancel }
				/>
				{ this.state.isWarningOpen && (
					<div className="tribe-editor__rsvp__warning">
						{ __( 'This is a recurring event. If you add tickets they will only show up on the next upcoming event in the recurrence pattern. The same ticket form will appear across all events in the series. Please configure your events accordingly.', 'event-tickets' ) }
					</div>
				) }
			</Fragment>
		);
		/* eslint-enable max-len */
	}
}

export default RSVPActionDashboard;
