jQuery(document).ready(function ($) {
    $(document).on('click', '.vi-woocommerce-orders-tracking-form-search-tracking-number-btnclick', function () {
        let tracking_number = $('.vi-woocommerce-orders-tracking-form-search-tracking-number').val();
        if (!tracking_number) {
            alert(vi_wot_frontend_form_search.error_empty_text);
            return false;
        }
    });
});