'use strict';
jQuery(document).ready(function ($) {
    //set icon
    $('.customize-control.customize-control-vi_wot_shipment_icon .vi_wot_radio_button_img').buttonset();
    $('.customize-control.customize-control-vi_wot_shipment_icon .vi_wot_radio_button_img input:radio').change(function () {
        var setting = $(this).attr('data-customize-setting-link');
        var image = $(this).val();
        wp.customize(setting, function (obj) {
            obj.set(image);
        });
    });
});