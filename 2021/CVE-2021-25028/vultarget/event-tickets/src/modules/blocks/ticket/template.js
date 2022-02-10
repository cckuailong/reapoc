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
import './style.pcss';
import TicketContainer from './container/container';
import TicketDashboard from './dashboard/container';
import MoveModal from '@moderntribe/tickets/elements/move-modal';

class Ticket extends PureComponent {
	static propTypes = {
		clientId: PropTypes.string.isRequired,
		hasTicketsPlus: PropTypes.bool,
		isDisabled: PropTypes.bool,
		isLoading: PropTypes.bool,
		isModalShowing: PropTypes.bool,
		isSelected: PropTypes.bool,
		onBlockUpdate: PropTypes.func,
		removeTicketBlock: PropTypes.func,
		showTicket: PropTypes.bool,
	};

	componentDidMount() {
		this.props.onBlockUpdate( this.props.isSelected );
	}

	componentDidUpdate( prevProps ) {
		if ( prevProps.isSelected !== this.props.isSelected ) {
			this.props.onBlockUpdate( this.props.isSelected );
		}
	}

	render() {
		const {
			clientId,
			hasTicketsPlus,
			isDisabled,
			isLoading,
			isSelected,
			isModalShowing,
			showTicket,
		} = this.props;

		return (
			showTicket
				? (
					<Fragment>
						<article className={ classNames(
							'tribe-editor__ticket',
							{ 'tribe-editor__ticket--disabled': isDisabled },
							{ 'tribe-editor__ticket--selected': isSelected },
							{ 'tribe-editor__ticket--has-tickets-plus': hasTicketsPlus },
						) }
						>
							<TicketContainer clientId={ clientId } isSelected={ isSelected } />
							<TicketDashboard clientId={ clientId } isSelected={ isSelected } />
							{ isLoading && <Spinner /> }
						</article>
						{ isModalShowing && <MoveModal /> }
					</Fragment>
				)
				: null
		);
	}
}

export default Ticket;
