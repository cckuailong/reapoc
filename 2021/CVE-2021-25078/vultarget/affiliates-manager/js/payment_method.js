jQuery(function() {
	function updatePaymentMethodDivs()
	{
		if (jQuery("#ddPaymentMethod").val() == 'paypal')
		{
			jQuery("#paypalDetails").show();
			jQuery("#checkDetails").hide();
		}
		else if (jQuery("#ddPaymentMethod").val() == 'check')
		{
			jQuery("#paypalDetails").hide();
			jQuery("#checkDetails").show();
		}
		else
		{
			jQuery("#paypalDetails").hide();
			jQuery("#checkDetails").hide();
		}
	}

	updatePaymentMethodDivs();

	jQuery("#ddPaymentMethod").change(updatePaymentMethodDivs);
});
