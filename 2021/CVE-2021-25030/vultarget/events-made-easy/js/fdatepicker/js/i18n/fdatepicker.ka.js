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
	$.fn.fdatepicker.language['ka'] = {
		days: ['კვირა', 'ორშაბათი', 'სამშაბათი', 'ოთხშაბათი', 'ხუთშაბათი', 'პარასკევი', 'შაბათი'],
		daysShort: ['კვი', 'ორშ', 'სამ', 'ოთხ', 'ხუთ', 'პარ', 'შაბ'],
		daysMin: ['კვ', 'ორ', 'სა', 'ოთ', 'ხუ', 'პა', 'შა'],
		months: ['იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი', 'ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი'],
		monthsShort: ['იან', 'თებ', 'მარ', 'აპრ', 'მაი', 'ივნ', 'ივლ', 'აგვ', 'სექ', 'ოქტ', 'ნოე', 'დეკ'],
		today: 'დღეს',
		clear: 'Clear',
		dateFormat: 'd/m/Y',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
