/* Start Wp-statistics Admin Js */
var wps_js = {};

/* Get WP-Statistics global Data From Frontend */
wps_js.global = (typeof wps_global != 'undefined') ? wps_global : [];

/* WordPress Global Lang */
wps_js._ = function (key) {
    return (key in this.global.i18n ? this.global.i18n[key] : '');
};

/* Check Active Option */
wps_js.is_active = function (option) {
    return wps_js.global.options[option] === 1;
};