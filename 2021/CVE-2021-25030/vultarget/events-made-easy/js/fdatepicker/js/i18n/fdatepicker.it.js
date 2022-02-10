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
	$.fn.fdatepicker.language['it'] = {
		days: ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'],
		daysShort: ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'],
		daysMin: ['Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa'],
		months: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
		monthsShort: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
		today: 'Oggi',
		clear: 'Cancella',
		dateFormat: 'd/m/Y',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
