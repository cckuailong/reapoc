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
	$.fn.fdatepicker.language['sk'] = {
		days: ['Nedeľa', 'Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok', 'Sobota'],
		daysShort: ['Ned', 'Pon', 'Uto', 'Str', 'Štv', 'Pia', 'Sob'],
		daysMin: ['Ne', 'Po', 'Ut', 'St', 'Št', 'Pi', 'So'],
		months: ['Január','Február','Marec','Apríl','Máj','Jún', 'Júl','August','September','Október','November','December'],
		monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Máj', 'Jún', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
		today: 'Dnes',
		clear: 'Vymazať',
		dateFormat: 'd.m.Y',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
