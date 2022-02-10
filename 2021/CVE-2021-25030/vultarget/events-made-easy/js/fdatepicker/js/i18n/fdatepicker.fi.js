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
	$.fn.fdatepicker.language['fi'] = {
		days: ['Sunnuntai', 'Maanantai', 'Tiistai', 'Keskiviikko', 'Torstai', 'Perjantai', 'Lauantai'],
		daysShort: ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe', 'La'],
		daysMin: ['Su', 'Ma', 'Ti', 'Ke', 'To', 'Pe', 'La'],
		months: ['Tammikuu','Helmikuu','Maaliskuu','Huhtikuu','Toukokuu','Kesäkuu', 'Heinäkuu','Elokuu','Syyskuu','Lokakuu','Marraskuu','Joulukuu'],
		monthsShort: ['Tammi', 'Helmi', 'Maalis', 'Huhti', 'Touko', 'Kesä', 'Heinä', 'Elo', 'Syys', 'Loka', 'Marras', 'Joulu'],
		today: 'Tänään',
		clear: 'Tyhjennä',
		dateFormat: 'd.m.Y',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
