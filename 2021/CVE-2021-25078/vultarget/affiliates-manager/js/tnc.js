jQuery(function($) {
	$("#mainForm").submit(function() {
		if ($("#chkAgreeTerms").is(':checked'))
		{
			return true;
		}
		else
		{
			$("#termsAgreeWarning").show();
			return false;
		}
	});

	$("#tncDialog").dialog({
		modal: true,
		autoOpen: false,
		width: 640,
		height: 480,
		resizable: false
	});

	$("#tncLink").click(function() {
		$("#tncDialog").dialog('open');
		return false;
	});
});