/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import './style.pcss';

const CapacityRow = ( { label, items, right } ) => (
	<div className="tribe-editor__tickets__capacity-row">
		<span className="tribe-editor__tickets__capacity-row-left">
			{ label && <span className="tribe-editor__tickets__capacity-row-label">{ label }</span> }
			{ items && <span className="tribe-editor__tickets__capacity-row-items">{ items }</span> }
		</span>
		<span className="tribe-editor__tickets__capacity-row-right">{ right }</span>
	</div>
);

CapacityRow.propTypes = {
	label: PropTypes.string,
	items: PropTypes.string,
	right: PropTypes.node,
};

CapacityRow.defaultProps = {
	label: '',
	items: '',
	right: '',
};

export default CapacityRow;
