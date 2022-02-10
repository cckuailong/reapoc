/**
 * External dependencies
 */
import React, { Fragment, PureComponent } from 'react';
import PropTypes from 'prop-types';
import uniqid from 'uniqid';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import RSVPDuration from './../duration/container';
import MoveDelete from './../move-delete/container';
import RSVPAttendeeRegistration from '../attendee-registration/container';
import { Checkbox, NumberInput } from '@moderntribe/common/elements';
import './style.pcss';

const RSVPContainerContentLabels = () => (
	<div className="tribe-editor__rsvp-container-content__labels">
		<span className="tribe-editor__rsvp-container-content__capacity-label">
			{ __( 'RSVP Capacity', 'event-tickets' ) }
		</span>
		<span className="tribe-editor__rsvp-container-content__capacity-label-help">
			{ __( 'Leave blank if unlimited', 'event-tickets' ) }
		</span>
	</div>
);

const RSVPContainerContentOptions = ( {
	capacityId,
	isDisabled,
	notGoingId,
	onTempCapacityChange,
	onTempNotGoingResponsesChange,
	tempCapacity,
	tempNotGoingResponses,
} ) => (
	<div className="tribe-editor__rsvp-container-content__options">
		<NumberInput
			className="tribe-editor__rsvp-container-content__capacity-input"
			disabled={ isDisabled }
			id={ capacityId }
			min={ 0 }
			onChange={ onTempCapacityChange }
			value={ tempCapacity }
		/>
		<Checkbox
			checked={ tempNotGoingResponses }
			className="tribe-editor__rsvp-container-content__not-going-responses"
			disabled={ isDisabled }
			id={ notGoingId }
			label={ __( 'Enable "Not Going" responses', 'event-tickets' ) }
			onChange={ onTempNotGoingResponsesChange }
		/>
	</div>
);

RSVPContainerContentOptions.propTypes = {
	capacityId: PropTypes.string.isRequired,
	isDisabled: PropTypes.bool.isRequired,
	notGoingId: PropTypes.string.isRequired,
	onTempCapacityChange: PropTypes.func.isRequired,
	onTempNotGoingResponsesChange: PropTypes.func.isRequired,
	tempCapacity: PropTypes.string.isRequired,
	tempNotGoingResponses: PropTypes.bool.isRequired,
};

class RSVPContainerContent extends PureComponent {
	static propTypes = {
		clientId: PropTypes.string,
		hasTicketsPlus: PropTypes.bool,
		onTempCapacityChange: PropTypes.func,
		onTempNotGoingResponsesChange: PropTypes.func,
		tempCapacity: PropTypes.string,
		tempNotGoingResponses: PropTypes.bool,
		hasBeenCreated: PropTypes.bool,
	}

	constructor( props ) {
		super( props );
		this.capacityId = uniqid();
		this.notGoingId = uniqid();
	}

	render() {
		const {
			isDisabled,
			onTempCapacityChange,
			onTempNotGoingResponsesChange,
			tempCapacity,
			tempNotGoingResponses,
			clientId,
		} = this.props;
		const optionsProps = {
			capacityId: this.capacityId,
			isDisabled,
			notGoingId: this.notGoingId,
			onTempCapacityChange,
			onTempNotGoingResponsesChange,
			tempCapacity,
			tempNotGoingResponses,
		};

		return (
			<Fragment>
				<RSVPContainerContentLabels />
				<RSVPContainerContentOptions { ...optionsProps } />
				<RSVPDuration />
				{ this.props.hasBeenCreated && (
					<MoveDelete clientId={ clientId } />
				) }
				{ this.props.hasTicketsPlus && <RSVPAttendeeRegistration /> }
			</Fragment>
		);
	}
}

export default RSVPContainerContent;
