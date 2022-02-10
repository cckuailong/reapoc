jQuery(document).ready( function($) {
	
	// Date picker
	$('.dilaz-mb-date').each(function() {
		
		var $this = $(this);
		
		$this.datepicker({
			dateFormat : 'DD, d MM, yy',
			defaultDate : '+1w',
			changeMonth : true,
			changeYear : true,
			numberOfMonths : 1,
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',
		});
	});
	
	// Date picker (From - To)
	$('.dilaz-mb-date-from-to').each(function() {
		
		var $this     = $(this),
			$fromDate = $this.find('.from-date'),
			$toDate   = $this.find('.to-date');
			
		$fromDate.datepicker({
			defaultDate : '+1w',
			changeMonth : true,
			changeYear : true,
			numberOfMonths : 1,
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',
			onClose : function( selectedDate ) {
				$toDate.datepicker( 'option', 'minDate', selectedDate );
			}
		});
		
		$toDate.datepicker({
			defaultDate : '+1w',
			changeMonth : false,
			numberOfMonths : 1,
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',			
			onClose : function( selectedDate ) {
				$fromDate.datepicker( 'option', 'maxDate', selectedDate );
			}
		});
	});
	
	// Month picker
	$('.dilaz-mb-month').each(function() {
		
		var $this = $(this);
		
		$this.monthpicker({
			dateFormat : 'MM, yy',
			changeYear : false,
			stepYears : 1,
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',
			showButtonPanel : true,
		});
	});
	
	// Month picker (From - To)
	$('.dilaz-mb-month-from-to').each(function() {
		
		var $this  = $(this),
			$month = $this.find('.from-month, .to-month');
			
		$month.monthpicker({
			dateFormat : 'MM, yy',
			changeYear : false,
			stepYears : 1,
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',
			showButtonPanel : true,
		});
	});
	
	// Time picker
	$('.dilaz-mb-time').each(function() {
		
		var $this = $(this);
		
		$this.timepicker({
			timeFormat: 'hh:mm:ss TT',
			showSecond: true,
		});
	});
	
	// Time picker (From - To)
	$('.dilaz-mb-time-from-to').each(function() {
		
		var $this = $(this),
			$time = $this.find('.from-time, .to-time');
			
		$time.timepicker({
			timeFormat : 'hh:mm:ss TT',
			showSecond : true,
		});
	});
	
	// Date Time picker
	$('.dilaz-mb-date-time').each(function() {
		
		var $this = $(this);
		
		$this.datetimepicker({
			timeFormat : 'hh:mm:ss TT',
			dateFormat : 'DD, MM d yy',
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',	
		});
	});
	
	// Date Time picker (From - To)
	$('.dilaz-mb-date-time-from-to').each(function() {
		
		var $this     = $(this),
			$dateTime = $this.find('.from-date-time, .to-date-time');
			
		$dateTime.datetimepicker({
			timeFormat : 'hh:mm:ss TT',
			dateFormat : 'DD, MM d yy',
			prevText : '<i class="fa fa-chevron-left"></i>',
			nextText : '<i class="fa fa-chevron-right"></i>',	
		});
	});
});