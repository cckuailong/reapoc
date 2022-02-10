/**
 * External dependencies
 */
import { connect } from 'react-redux';
import { compose } from 'redux';

/**
 * Internal dependencies
 */
import Template from './template';
import { selectors, actions } from '@moderntribe/tickets/data/blocks/ticket';
import { withStore } from '@moderntribe/common/hoc';
import {
	globals,
	moment as momentUtil,
} from '@moderntribe/common/utils';

const onFromDateChange = ( dispatch, ownProps ) => ( date, modifiers, dayPickerInput ) => {
	dispatch( actions.handleTicketStartDate( ownProps.clientId, date, dayPickerInput ) );
};

const onFromTimePickerChange = ( dispatch, ownProps ) => ( e ) => {
	dispatch( actions.setTicketTempStartTimeInput( ownProps.clientId, e.target.value ) );
};

const onFromTimePickerClick = ( dispatch, ownProps ) => ( value, onClose ) => {
	dispatch( actions.handleTicketStartTime( ownProps.clientId, value ) );
	onClose();
};

const onToDateChange = ( dispatch, ownProps ) => ( date, modifiers, dayPickerInput ) => {
	dispatch( actions.handleTicketEndDate( ownProps.clientId, date, dayPickerInput ) );
};

const onToTimePickerChange = ( dispatch, ownProps ) => ( e ) => {
	dispatch( actions.setTicketTempEndTimeInput( ownProps.clientId, e.target.value ) );
};

const onToTimePickerClick = ( dispatch, ownProps ) => ( value, onClose ) => {
	dispatch( actions.handleTicketEndTime( ownProps.clientId, value ) );
	onClose();
};

const onFromTimePickerBlur = ( state, dispatch, ownProps ) => ( e ) => {
	let startTimeMoment = momentUtil.toMoment( e.target.value, momentUtil.TIME_FORMAT, false );
	if ( ! startTimeMoment.isValid() ) {
		const startTimeInput = selectors.getTicketStartTimeInput( state, ownProps );
		startTimeMoment = momentUtil.toMoment( startTimeInput, momentUtil.TIME_FORMAT, false );
	}
	const seconds = momentUtil.totalSeconds( startTimeMoment );
	dispatch( actions.handleTicketStartTime( ownProps.clientId, seconds ) );
};

const onToTimePickerBlur = ( state, dispatch, ownProps ) => ( e ) => {
	let endTimeMoment = momentUtil.toMoment( e.target.value, momentUtil.TIME_FORMAT, false );
	if ( ! endTimeMoment.isValid() ) {
		const endTimeInput = selectors.getTicketEndTimeInput( state, ownProps );
		endTimeMoment = momentUtil.toMoment( endTimeInput, momentUtil.TIME_FORMAT, false );
	}
	const seconds = momentUtil.totalSeconds( endTimeMoment );
	dispatch( actions.handleTicketEndTime( ownProps.clientId, seconds ) );
};

const mapStateToProps = ( state, ownProps ) => {
	const datePickerFormat = globals.tecDateSettings().datepickerFormat
		? momentUtil.toFormat( globals.tecDateSettings().datepickerFormat )
		: 'LL';
	const isDisabled = selectors.isTicketDisabled( state, ownProps );

	const startDateMoment = selectors.getTicketTempStartDateMoment( state, ownProps );
	const endDateMoment = selectors.getTicketTempEndDateMoment( state, ownProps );
	const fromDate = startDateMoment && startDateMoment.toDate();
	const toDate = endDateMoment && endDateMoment.toDate();

	return {
		fromDate,
		fromDateInput: selectors.getTicketTempStartDateInput( state, ownProps ),
		fromDateDisabled: isDisabled,
		fromDateFormat: datePickerFormat,
		fromTime: selectors.getTicketTempStartTimeInput( state, ownProps ),
		fromTimeDisabled: isDisabled,
		hasDurationError: selectors.getTicketHasDurationError( state, ownProps ),
		toDate,
		toDateInput: selectors.getTicketTempEndDateInput( state, ownProps ),
		toDateDisabled: isDisabled,
		toDateFormat: datePickerFormat,
		toTime: selectors.getTicketTempEndTimeInput( state, ownProps ),
		toTimeDisabled: isDisabled,
		state,
	};
};

const mapDispatchToProps = ( dispatch, ownProps ) => ( {
	onFromDateChange: onFromDateChange( dispatch, ownProps ),
	onFromTimePickerChange: onFromTimePickerChange( dispatch, ownProps ),
	onFromTimePickerClick: onFromTimePickerClick( dispatch, ownProps ),
	onToDateChange: onToDateChange( dispatch, ownProps ),
	onToTimePickerChange: onToTimePickerChange( dispatch, ownProps ),
	onToTimePickerClick: onToTimePickerClick( dispatch, ownProps ),
	dispatch,
} );

const mergeProps = ( stateProps, dispatchProps, ownProps ) => {
	const { state, ...restStateProps } = stateProps;
	const { dispatch, ...restDispatchProps } = dispatchProps;

	return {
		...ownProps,
		...restStateProps,
		...restDispatchProps,
		onFromTimePickerBlur: onFromTimePickerBlur( state, dispatch, ownProps ),
		onToTimePickerBlur: onToTimePickerBlur( state, dispatch, ownProps ),
	};
};

export default compose(
	withStore(),
	connect(
		mapStateToProps,
		mapDispatchToProps,
		mergeProps,
	),
)( Template );
