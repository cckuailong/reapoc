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
	$.fn.fdatepicker.language['uk'] = {
		days: ['Неділя', 'Понеділок', 'Вівторок', 'Середа', 'Четверг', 'Пятниця', 'Субота'],
		daysShort: ['Нд', 'Пн', 'Вв', 'Ср', 'Чт', 'Пт', 'Сб'],
		daysMin: ['Нд', 'Пн', 'Вв', 'Ср', 'Чт', 'Пт', 'Сб'],
		months: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'],
		monthsShort: ['Січ', 'Лют', 'Бер', 'Кві', 'Тра', 'Чер', 'Лип', 'Сер', 'Вер', 'Жов', 'Лис', 'Гру'],
		today: 'Сьогодні',
		clear: 'Очистити',
		dateFormat: 'm/d/Y',
		timeFormat: 'h:i a',
		firstDay: 1
	};
}));
