/**
 * External Dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';
import { __ } from '@wordpress/i18n';
import { Button } from '@moderntribe/common/elements';
import './style.pcss';

const MoveDelete = ( {
	moveRSVP,
	removeRSVP,
	isDisabled,
} ) => {
	return (
		<div className="tribe-editor__rsvp__content-row--move-delete">
			<Button type="button" onClick={ moveRSVP } disabled={ isDisabled }>
				{ __( 'Move RSVP', 'event-tickets' ) }
			</Button>
			<Button type="button" onClick={ removeRSVP } disabled={ isDisabled }>
				{ __( 'Remove RSVP', 'event-tickets' ) }
			</Button>
		</div>
	);
};

MoveDelete.propTypes = {
	moveRSVP: PropTypes.func.isRequired,
	removeRSVP: PropTypes.func.isRequired,
	isDisabled: PropTypes.bool.isRequired,
};

export default MoveDelete;
