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
	$.fn.fdatepicker.language['zh'] = {
		days: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
		daysShort: ['日', '一', '二', '三', '四', '五', '六'],
		daysMin: ['日', '一', '二', '三', '四', '五', '六'],
		months: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
		monthsShort: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
		today: '今天',
		clear: '清除',
		dateFormat: 'Y-m-d',
		timeFormat: 'H:i',
		firstDay: 1
	};
}));
