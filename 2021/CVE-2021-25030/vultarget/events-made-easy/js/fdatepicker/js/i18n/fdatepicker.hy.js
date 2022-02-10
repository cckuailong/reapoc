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
	$.fn.fdatepicker.language['hy'] = {
		days: ['Կիրակի', 'Երկուշաբթի', 'Երեքշաբթի', 'Չորեքշաբթի', 'Հինգշաբթի', 'ՈՒրբաթ', 'Շաբաթ'],
		daysShort: ['Կիր','Երկ', 'Երք', 'Չրք', 'Հնգ', 'Ուր', 'Շբթ'],
		daysMin: ['Կ','Եր', 'Եք', 'Չ', 'Հ', 'Ու', 'Շ'],
		months: ['Հունվար','Փետրվար','Մարտ','Ապրիլ','Մայիս','Հունիս', 'Հուլիս','Օգոստոս','Սեպտեմբեր','Հոկտեմբեր','Նոյեմբեր','Դեկտեմբեր'],
		monthsShort: ['Հունվ', 'Փետ', 'Մար', 'Ապր', 'Մայ', 'Հուն', 'Հուլ', 'Օգ', 'Սեպ', 'Հոկ', 'Նոյ', 'Դեկ'],
		today: 'Այսօր',
		clear: 'Մաքրել',
		dateFormat: 'm/d/Y',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
