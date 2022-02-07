jQuery.fn.extend( {
	dlm_reports_date_range: function ( start_date, end_date, url ) {
		new DLM_Reports_Date_Range_Selector( this, start_date, end_date, url );
		return this;
	}
} );

var DLM_Reports_Date_Range_Selector = function ( c, sd, ed, u ) {

	this.container = c;
	this.startDate = new Date( sd );
	this.endDate = new Date( ed );
	this.url = u;
	this.el = null;
	this.opened = false;

	this.startDateInput = null;
	this.endDateInput = null;

	this.setup = function () {
		var instance = this;
		this.container.click( function () {
			instance.toggleDisplay();
			return false;
		} );
	};

	this.setup();

};

DLM_Reports_Date_Range_Selector.prototype.toggleDisplay = function () {
	if ( this.opened ) {
		this.hide();
	} else {
		this.display();
	}
};

DLM_Reports_Date_Range_Selector.prototype.display = function () {
	if ( this.opened ) {
		return;
	}
	this.opened = true;
	this.el = this.createElement();
	this.container.append( this.el );
};

DLM_Reports_Date_Range_Selector.prototype.hide = function () {
	this.opened = false;
	this.el.remove();
};

DLM_Reports_Date_Range_Selector.prototype.apply = function () {

	var sd = new Date( this.startDateInput.val() + "T00:00:00" );
	var ed = new Date( this.endDateInput.val() + "T00:00:00" );
	var sds = sd.getFullYear()+ "-"+(sd.getMonth()+1)+"-"+sd.getDate();
	var eds = ed.getFullYear()+ "-"+(ed.getMonth()+1)+"-"+ed.getDate();
	this.hide();
	window.location.replace( this.url + "&date_from=" + sds + "&date_to=" + eds );
};

DLM_Reports_Date_Range_Selector.prototype.createElement = function () {
	var instance = this;
	var el = jQuery( '<div>' ).addClass( 'dlm_rdrs_overlay' );
	var startDate = jQuery( '<div>' ).addClass( 'dlm_rdrs_date' ).attr( 'id', 'dlm_rdrs_date_start' );
	var endDate = jQuery( '<div>' ).addClass( 'dlm_rdrs_date' ).attr( 'id', 'dlm_rdrs_date_end' );
	this.startDateInput = jQuery( '<input>' ).attr( 'type', 'hidden' );
	this.endDateInput = jQuery( '<input>' ).attr( 'type', 'hidden' );
	var actions = jQuery( '<div>' ).addClass( 'dlm_rdrs_actions' );
	var applyButton = jQuery( '<a>' ).addClass( 'button' ).html( 'Apply' ).click( function () {
		instance.apply();
		return false;
	} );
	actions.append( applyButton );
	el.append( startDate ).append( endDate ).append( actions ).append( this.startDateInput ).append( this.endDateInput );
	startDate.datepicker( {inline: true, altField: this.startDateInput, dateFormat: "yy-mm-dd", defaultDate: this.startDate } );
	endDate.datepicker( {inline: true, altField: this.endDateInput, dateFormat: "yy-mm-dd", defaultDate: this.endDate } );
	el.click( function () {
		return false
	} );
	return el;
};