// Used by plugns that hide/show the billing fields.
pmpro_require_billing = true;

jQuery(document).ready(function() {
    //choosing payment method
    jQuery('input[name=gateway]').click(function() {
        if(jQuery(this).val() == 'paypal') {
            jQuery('#pmpro_paypalexpress_checkout').hide();
            jQuery('#pmpro_billing_address_fields').show();
            jQuery('#pmpro_payment_information_fields').show();
            jQuery('#pmpro_submit_span').show();
        } else {
            jQuery('#pmpro_billing_address_fields').hide();
            jQuery('#pmpro_payment_information_fields').hide();
            jQuery('#pmpro_submit_span').hide();
            jQuery('#pmpro_paypalexpress_checkout').show();
        }
    });

    //select the radio button if the label is clicked on
    jQuery('a.pmpro_radio').click(function() {
        jQuery(this).prev().click();
    });
});