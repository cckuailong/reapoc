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
	$.fn.fdatepicker.language['cs'] = {
		days: ['Neděle', 'Pondělí', 'Úterý', 'Středa', 'Čtvrtek', 'Pátek', 'Sobota'],
		daysShort: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
		daysMin: ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'],
		months: ['Leden', 'Únor', 'Březen', 'Duben', 'Květen', 'Červen', 'Červenec', 'Srpen', 'Září', 'Říjen', 'Listopad', 'Prosinec'],
		monthsShort: ['Led', 'Úno', 'Bře', 'Dub', 'Kvě', 'Čvn', 'Čvc', 'Srp', 'Zář', 'Říj', 'Lis', 'Pro'],
		today: 'Dnes',
		clear: 'Vymazat',
		dateFormat: 'd.m.Y',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
