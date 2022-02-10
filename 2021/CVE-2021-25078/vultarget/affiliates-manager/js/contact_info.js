jQuery(document).ready(function($) {

	$("#saveInfoButton").click(function() {
		$("#infoForm").submit();
	});

	$("#email_help").dialog({
		resizable: false,
		height: 200,
		width: 500,
		autoOpen: false,
		modal: true,
		draggable: false,
		buttons: [ {
			text : currencyL10n.okLabel,
			click : function() { $(this).dialog('close'); }
		} ]
	});

	$("#emailInfo").click(function() {
		$("#email_help").dialog('open');
	});

	$("#ddBountyType").change(function() {
		var type = $(this).val();
		if (type == 'fixed')
		{
			$('#lblBountyAmount').html(currencyL10n.fixedLabel + " *");
		}
		else if (type == 'percent')
		{
			$('#lblBountyAmount').html(currencyL10n.percentLabel + " *");
		}
	});
	
	$("#ddPaymentMethod").change(function() {
		var type = $(this).val();
		if (type == 'paypal') {
			$('#rowPaypalEmail').show();
		} else {
			$('#rowPaypalEmail').hide();
		}
	});
});
