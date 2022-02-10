/**
 * External dependencies
 */
import React, { createRef, Component } from 'react';
import PropTypes from 'prop-types';
import { noop } from 'lodash';
import classNames from 'classnames';
import { formatDate, parseDate } from 'react-day-picker/moment';

/**
 * Internal dependencies
 */
import { DayPickerInput, TimePicker } from '@moderntribe/common/elements';
import {
	date,
	time,
} from '@moderntribe/common/utils';
import './style.pcss';

class DateTimeRangePicker extends Component {
	static defaultProps = {
		fromDateFormat: 'LL',
		onFromDateChange: noop,
		onToDateChange: noop,
		separatorDateTime: 'at',
		separatorTimeRange: 'to',
		toDateFormat: 'LL',
	};

	static propTypes = {
		className: PropTypes.string,
		fromDate: PropTypes.instanceOf( Date ),
		fromDateInput: PropTypes.string,
		fromDateDisabled: PropTypes.bool,
		fromDateFormat: PropTypes.string,
		fromTime: PropTypes.string,
		fromTimeDisabled: PropTypes.bool,
		onFromDateChange: PropTypes.func,
		onFromTimePickerBlur: PropTypes.func,
		onFromTimePickerChange: PropTypes.func,
		onFromTimePickerClick: PropTypes.func,
		onFromTimePickerFocus: PropTypes.func,
		onToDateChange: PropTypes.func,
		onToTimePickerBlur: PropTypes.func,
		onToTimePickerChange: PropTypes.func,
		onToTimePickerClick: PropTypes.func,
		onToTimePickerFocus: PropTypes.func,
		separatorDateTime: PropTypes.string,
		separatorTimeRange: PropTypes.string,
		shiftFocus: PropTypes.bool,
		toDate: PropTypes.instanceOf( Date ),
		toDateInput: PropTypes.string,
		toDateDisabled: PropTypes.bool,
		toDateFormat: PropTypes.string,
		toTime: PropTypes.string,
		toTimeDisabled: PropTypes.bool,
	};

	constructor( props ) {
		super( props );
		this.toDayPickerInput = createRef();
	}

	getFromDayPickerInputProps = () => {
		const {
			fromDate,
			fromDateInput,
			fromDateDisabled,
			fromDateFormat,
			onFromDateChange,
			shiftFocus,
			toDate,
		} = this.props;

		const props = {
			value: fromDateInput,
			format: fromDateFormat,
			formatDate: formatDate,
			parseDate: parseDate,
			dayPickerProps: {
				selectedDays: [ fromDate, { from: fromDate, to: toDate } ],
				disabledDays: { after: toDate },
				modifiers: {
					start: fromDate,
					end: toDate,
				},
				toMonth: toDate,
			},
			onDayChange: onFromDateChange,
			inputProps: {
				disabled: fromDateDisabled,
			},
		};

		/**
		 * If shiftFocus is true, selection of date on fromDayPickerInput
		 * automatically focuses on toDayPickerInput
		 */
		if ( shiftFocus ) {
			props.dayPickerProps.onDayClick = () => (
				this.toDayPickerInput.current.focus()
			);
		}

		return props;
	};

	getToDayPickerInputProps = () => {
		const {
			fromDate,
			onToDateChange,
			shiftFocus,
			toDate,
			toDateInput,
			toDateDisabled,
			toDateFormat,
		} = this.props;

		const props = {
			value: toDateInput,
			format: toDateFormat,
			formatDate: formatDate,
			parseDate: parseDate,
			dayPickerProps: {
				selectedDays: [ fromDate, { from: fromDate, to: toDate } ],
				disabledDays: { before: fromDate },
				modifiers: {
					start: fromDate,
					end: toDate,
				},
				month: fromDate,
				fromMonth: fromDate,
			},
			onDayChange: onToDateChange,
			inputProps: {
				disabled: toDateDisabled,
			},
		};

		/**
		 * If shiftFocus is true, selection of date on fromDayPickerInput
		 * automatically focuses on toDayPickerInput
		 */
		if ( shiftFocus ) {
			props.ref = this.toDayPickerInput;
		}

		return props;
	};

	getFromTimePickerProps = () => {
		const {
			fromTime,
			fromTimeDisabled,
			onFromTimePickerBlur,
			onFromTimePickerChange,
			onFromTimePickerClick,
			onFromTimePickerFocus,
		} = this.props;

		const props = {
			current: fromTime,
			start: time.START_OF_DAY,
			end: time.END_OF_DAY,
			onBlur: onFromTimePickerBlur,
			onChange: onFromTimePickerChange,
			onClick: onFromTimePickerClick,
			onFocus: onFromTimePickerFocus,
			timeFormat: date.FORMATS.WP.time,
			disabled: fromTimeDisabled,
		};

		return props;
	};

	getToTimePickerProps = () => {
		const {
			onToTimePickerBlur,
			onToTimePickerChange,
			onToTimePickerClick,
			onToTimePickerFocus,
			toTime,
			toTimeDisabled,
		} = this.props;

		const props = {
			current: toTime,
			start: time.START_OF_DAY,
			end: time.END_OF_DAY,
			onBlur: onToTimePickerBlur,
			onChange: onToTimePickerChange,
			onClick: onToTimePickerClick,
			onFocus: onToTimePickerFocus,
			timeFormat: date.FORMATS.WP.time,
			disabled: toTimeDisabled,
		};

		return props;
	};

	render() {
		const {
			className,
			separatorDateTime,
			separatorTimeRange,
		} = this.props;

		return (
			<div className={ classNames( 'tribe-editor__date-time-range-picker', className ) }>
				<div className="tribe-editor__date-time-range-picker__start">
					<DayPickerInput { ...this.getFromDayPickerInputProps() } />
					<span
						className={ classNames(
							'tribe-editor__date-time-range-picker__separator',
							'tribe-editor__date-time-range-picker__separator--date-time',
						) }
					>
						{ separatorDateTime }
					</span>
					<TimePicker { ...this.getFromTimePickerProps() } />
				</div>
				<div className="tribe-editor__date-time-range-picker__end">
					<span
						className={ classNames(
							'tribe-editor__date-time-range-picker__separator',
							'tribe-editor__date-time-range-picker__separator--time-range',
						) }
					>
						{ separatorTimeRange }
					</span>
					<DayPickerInput { ...this.getToDayPickerInputProps() } />
					<span
						className={ classNames(
							'tribe-editor__date-time-range-picker__separator',
							'tribe-editor__date-time-range-picker__separator--date-time',
						) }
					>
						{ separatorDateTime }
					</span>
					<TimePicker { ...this.getToTimePickerProps() } />
				</div>
			</div>
		);
	}
}

export default DateTimeRangePicker;
