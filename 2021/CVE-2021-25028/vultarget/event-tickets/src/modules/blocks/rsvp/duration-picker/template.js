/**
 * External dependencies
 */
import React from 'react';
import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import { DateTimeRangePicker } from '@moderntribe/tickets/elements';
import './style.pcss';

const RSVPDurationPicker = ( props ) => (
	<DateTimeRangePicker
		className="tribe-editor__rsvp-duration__duration-picker"
		{ ...props }
	/>
);

RSVPDurationPicker.propTypes = {
	fromDate: PropTypes.instanceOf( Date ),
	fromDateInput: PropTypes.string,
	fromDateDisabled: PropTypes.bool,
	fromTime: PropTypes.string,
	fromTimeDisabled: PropTypes.bool,
	onFromDateChange: PropTypes.func,
	onFromTimePickerBlur: PropTypes.func,
	onFromTimePickerChange: PropTypes.func,
	onFromTimePickerClick: PropTypes.func,
	onToDateChange: PropTypes.func,
	onToTimePickerBlur: PropTypes.func,
	onToTimePickerChange: PropTypes.func,
	onToTimePickerClick: PropTypes.func,
	toDate: PropTypes.instanceOf( Date ),
	toDateInput: PropTypes.string,
	toDateDisabled: PropTypes.bool,
	toTime: PropTypes.string,
	toTimeDisabled: PropTypes.bool,
};

export default RSVPDurationPicker;
