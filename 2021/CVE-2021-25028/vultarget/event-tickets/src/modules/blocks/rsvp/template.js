/**
 * External dependencies
 */
import React, { PureComponent, Fragment } from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';

/**
 * Internal dependencies
 */
import RSVPContainer from './container/container';
import RSVPDashboard from './dashboard/container';
import RSVPInactiveBlock from './inactive-block/container';
import MoveModal from '@moderntribe/tickets/elements/move-modal';
import './style.pcss';

class RSVP extends PureComponent {
	static propTypes = {
		clientId: PropTypes.string.isRequired,
		created: PropTypes.bool.isRequired,
		initializeRSVP: PropTypes.func.isRequired,
		isInactive: PropTypes.bool.isRequired,
		isLoading: PropTypes.bool.isRequired,
		isModalShowing: PropTypes.bool.isRequired,
		isSelected: PropTypes.bool.isRequired,
		rsvpId: PropTypes.number.isRequired,
	};

	componentDidMount() {
		! this.props.rsvpId && this.props.initializeRSVP();
	}

	render() {
		const {
			created,
			isInactive,
			isLoading,
			isSelected,
			clientId,
			isModalShowing,
		} = this.props;

		return (
			<Fragment>
				{
					! isSelected && ( ( created && isInactive ) || ! created )
						? <RSVPInactiveBlock />
						: (
							<div className={
								classNames(
									'tribe-editor__rsvp',
									{ 'tribe-editor__rsvp--selected': isSelected },
									{ 'tribe-editor__rsvp--loading': isLoading },
								) }
							>
								<RSVPContainer isSelected={ isSelected } clientId={ clientId } />
								<RSVPDashboard isSelected={ isSelected } />
								{ isLoading && <Spinner /> }
							</div>
						)
				}
				{ isModalShowing && <MoveModal /> }
			</Fragment>
		);
	}
}

export default RSVP;
