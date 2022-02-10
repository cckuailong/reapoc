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
	$.fn.fdatepicker.language['hu'] = {
		days: ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
		daysShort: ['Va', 'Hé', 'Ke', 'Sze', 'Cs', 'Pé', 'Szo'],
		daysMin: ['V', 'H', 'K', 'Sz', 'Cs', 'P', 'Sz'],
		months: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
		monthsShort: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún', 'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
		today: 'Ma',
		clear: 'Törlés',
		dateFormat: 'Y-m-d',
		timeFormat: 'h:i a',
		firstDay: 1
	};
}));
