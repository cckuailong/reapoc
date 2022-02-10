/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import DateTimeRangePicker from './template';
import { actions, selectors } from '@moderntribe/tickets/data/blocks/rsvp';
import { withStore } from '@moderntribe/common/hoc';
import {
	globals,
	moment as momentUtil,
} from '@moderntribe/common/utils';

const onFromDateChange = ( dispatch ) => ( date, modifiers, dayPickerInput ) => {
	const payload = {
		date,
		dayPickerInput,
	};
	dispatch( actions.handleRSVPStartDate( payload ) );
};

const onFromTimePickerChange = ( dispatch ) => ( e ) => (
	dispatch( actions.setRSVPTempStartTimeInput( e.target.value ) )
);

const onFromTimePickerClick = ( dispatch ) => ( value, onClose ) => {
	dispatch( actions.handleRSVPStartTime( value ) );
	onClose();
};

const onToDateChange = ( dispatch ) => ( date, modifiers, dayPickerInput ) => {
	const payload = {
		date,
		dayPickerInput,
	};
	dispatch( actions.handleRSVPEndDate( payload ) );
};

const onToTimePickerChange = ( dispatch ) => ( e ) => (
	dispatch( actions.setRSVPTempEndTimeInput( e.target.value ) )
);

const onToTimePickerClick = ( dispatch ) => ( value, onClose ) => {
	dispatch( actions.handleRSVPEndTime( value ) );
	onClose();
};

const onFromTimePickerBlur = ( state, dispatch ) => ( e ) => {
	let startTimeMoment = momentUtil.toMoment( e.target.value, momentUtil.TIME_FORMAT, false );
	if ( ! startTimeMoment.isValid() ) {
		const startTimeInput = selectors.getRSVPStartTimeInput( state );
		startTimeMoment = momentUtil.toMoment( startTimeInput, momentUtil.TIME_FORMAT, false );
	}
	const seconds = momentUtil.totalSeconds( startTimeMoment );
	dispatch( actions.handleRSVPStartTime( seconds ) );
};

const onToTimePickerBlur = ( state, dispatch ) => ( e ) => {
	let endTimeMoment = momentUtil.toMoment( e.target.value, momentUtil.TIME_FORMAT, false );
	if ( ! endTimeMoment.isValid() ) {
		const endTimeInput = selectors.getRSVPEndTimeInput( state );
		endTimeMoment = momentUtil.toMoment( endTimeInput, momentUtil.TIME_FORMAT, false );
	}
	const seconds = momentUtil.totalSeconds( endTimeMoment );
	dispatch( actions.handleRSVPEndTime( seconds ) );
};

const mapStateToProps = ( state ) => {
	const datePickerFormat = globals.tecDateSettings().datepickerFormat
		? momentUtil.toFormat( globals.tecDateSettings().datepickerFormat )
		: 'LL';
	const isDisabled = selectors.getRSVPIsLoading( state ) ||
		selectors.getRSVPSettingsOpen( state );

	const startDateMoment = selectors.getRSVPTempStartDateMoment( state );
	const endDateMoment = selectors.getRSVPTempEndDateMoment( state );
	const fromDate = startDateMoment && startDateMoment.toDate();
	const toDate = endDateMoment && endDateMoment.toDate();

	return {
		fromDate,
		fromDateInput: selectors.getRSVPTempStartDateInput( state ),
		fromDateDisabled: isDisabled,
		fromDateFormat: datePickerFormat,
		fromTime: selectors.getRSVPTempStartTimeInput( state ),
		fromTimeDisabled: isDisabled,
		toDate,
		toDateInput: selectors.getRSVPTempEndDateInput( state ),
		toDateDisabled: isDisabled,
		toDateFormat: datePickerFormat,
		toTime: selectors.getRSVPTempEndTimeInput( state ),
		toTimeDisabled: isDisabled,
		state,
	};
};

const mapDispatchToProps = ( dispatch ) => ( {
	onFromDateChange: onFromDateChange( dispatch ),
	onFromTimePickerChange: onFromTimePickerChange( dispatch ),
	onFromTimePickerClick: onFromTimePickerClick( dispatch ),
	onToDateChange: onToDateChange( dispatch ),
	onToTimePickerChange: onToTimePickerChange( dispatch ),
	onToTimePickerClick: onToTimePickerClick( dispatch ),
	dispatch,
} );

const mergeProps = ( stateProps, dispatchProps, ownProps ) => {
	const { state, ...restStateProps } = stateProps;
	const { dispatch, ...restDispatchProps } = dispatchProps;

	return {
		...ownProps,
		...restStateProps,
		...restDispatchProps,
		onFromTimePickerBlur: onFromTimePickerBlur( state, dispatch ),
		onToTimePickerBlur: onToTimePickerBlur( state, dispatch ),
	};
};

export default compose(
	withStore(),
	connect( mapStateToProps, mapDispatchToProps, mergeProps ),
)( DateTimeRangePicker );
