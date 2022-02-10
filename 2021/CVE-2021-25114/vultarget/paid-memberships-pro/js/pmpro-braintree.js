jQuery(document).ready(function() {
    //set up braintree encryption
    var braintree = Braintree.create( pmpro_braintree.encryptionkey );
    braintree.onSubmitEncryptForm('pmpro_form');

    //pass expiration dates in original format
    function pmpro_updateBraintreeCardExp()
    {
        jQuery('#credit_card_exp').val(jQuery('#ExpirationMonth').val() + "/" + jQuery('#ExpirationYear').val());
    }
    jQuery('#ExpirationMonth, #ExpirationYear').change(function() {
        pmpro_updateBraintreeCardExp();
    });
    pmpro_updateBraintreeCardExp();

    //pass last 4 of credit card
    function pmpro_updateBraintreeAccountNumber()
    {
        jQuery('#BraintreeAccountNumber').val('XXXXXXXXXXXXX' + jQuery('#AccountNumber').val().substr(jQuery('#AccountNumber').val().length - 4));
    }
    jQuery('#AccountNumber').change(function() {
        pmpro_updateBraintreeAccountNumber();
    });
    pmpro_updateBraintreeAccountNumber();
});