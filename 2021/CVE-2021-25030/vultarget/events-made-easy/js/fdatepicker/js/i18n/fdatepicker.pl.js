(function (factory) {
        if (typeof define === "function" && define.amd) {
                // AMD. Register as an anonymous module.
                define([
                        "jquery",
                ], factory);
        } else {
                // Browser globals
                factory(jQuery);
        }
}(function ($) {
	$.fn.fdatepicker.language['pl'] = {
		days: ['Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota'],
		daysShort: ['Nie', 'Pon', 'Wto', 'Śro', 'Czw', 'Pią', 'Sob'],
		daysMin: ['Nd', 'Pn', 'Wt', 'Śr', 'Czw', 'Pt', 'So'],
		months: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec', 'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
		monthsShort: ['Sty', 'Lut', 'Mar', 'Kwi', 'Maj', 'Cze', 'Lip', 'Sie', 'Wrz', 'Paź', 'Lis', 'Gru'],
		today: 'Dzisiaj',
		clear: 'Wyczyść',
		dateFormat: 'Y-m-d',
		timeFormat: 'h:i:a',
		firstDay: 1
	};
}));
