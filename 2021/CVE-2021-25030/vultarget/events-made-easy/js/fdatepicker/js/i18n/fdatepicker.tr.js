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
	$.fn.fdatepicker.language['tr'] = {
		days: ['Pazar', 'Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi'],
		daysShort: ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cts'],
		daysMin: ['Pz', 'Pzt', 'Sa', 'Ça', 'Pe', 'Cu', 'Ct'],
		months: ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran', 'Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
		monthsShort: ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'],
		today: 'Bugün',
		clear: 'Temizle',
		dateFormat: 'd/m/Y',
		timeFormat: 'H:i',
		firstDay: 0
	};
}));
