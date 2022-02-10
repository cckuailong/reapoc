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
	$.fn.fdatepicker.language['da'] = {
		days: ['Søndag', 'Mandag', 'Tirsdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lørdag'],
		daysShort: ['Søn', 'Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør'],
		daysMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
		months: ['Januar','Februar','Marts','April','Maj','Juni', 'Juli','August','September','Oktober','November','December'],
		monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Maj', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
		today: 'I dag',
		clear: 'Nulstil',
		dateFormat: 'd/m/Y',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
