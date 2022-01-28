/**
 * Check Exist Dom
 */
wps_js.exist_tag = function (tag) {
    return (jQuery(tag).length);
};

/**
 * Jquery UI Picker
 */
wps_js.date_picker = function () {
    if (jQuery.fn.datepicker && typeof wps_i18n_jquery_datepicker !== 'undefined') {
        jQuery("input[data-wps-date-picker]").datepicker({
            monthNames: wps_i18n_jquery_datepicker.monthNames,
            monthNamesShort: wps_i18n_jquery_datepicker.monthNamesShort,
            dayNames: wps_i18n_jquery_datepicker.dayNames,
            dayNamesShort: wps_i18n_jquery_datepicker.dayNamesShort,
            dayNamesMin: wps_i18n_jquery_datepicker.dayNamesMin,
            dateFormat: wps_i18n_jquery_datepicker.dateFormat,
            firstDay: wps_i18n_jquery_datepicker.firstDay,
            isRTL: wps_i18n_jquery_datepicker.isRTL,
            onSelect: function (selectedDate) {
                let ID = jQuery(this).attr("data-wps-date-picker");
                if (selectedDate.length > 0) {
                    jQuery("input[id=date-" + ID + "]").val(selectedDate);
                }
            }
        });
    }
};

/**
 * Set Select2
 */
wps_js.select2 = function () {
    jQuery("select[data-type-show=select2]").select2();
};

/**
 * Redirect To Custom Url
 *
 * @param url
 */
wps_js.redirect = function (url) {
    window.location.replace(url);
};

/**
 * Create Line Chart JS
 */
wps_js.line_chart = function (tag_id, title, label, data) {

    // Get Element By ID
    let ctx = document.getElementById(tag_id).getContext('2d');

    // Check is RTL Mode
    if (wps_js.is_active('rtl')) {
        Chart.defaults.global.defaultFontFamily = "tahoma";
    }

    // Create Chart
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: label,
            datasets: data
        },
        options: {
            responsive: true,
            legend: {
                position: 'bottom',
            },
            animation: {
                duration: 1500,
            },
            title: {
                display: true,
                text: title
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
};

/**
 * Create pie Chart JS
 */
wps_js.pie_chart = function (tag_id, label, data, label_callback = false) {

    // Get Element By ID
    let ctx = document.getElementById(tag_id).getContext('2d');

    // Check is RTL Mode
    if (wps_js.is_active('rtl')) {
        Chart.defaults.global.defaultFontFamily = "tahoma";
    }

    // Set Default Label Callback
    if (label_callback === false) {
        label_callback = function (tooltipItem, data) {
            let dataset = data.datasets[tooltipItem.datasetIndex];
            let total = dataset.data.reduce(function (previousValue, currentValue, currentIndex, array) {
                return previousValue + currentValue;
            });
            let currentValue = dataset.data[tooltipItem.index];
            let percentage = Math.floor(((currentValue / total) * 100) + 0.5);
            return percentage + "% - " + data.labels[tooltipItem.index];
        };
    }

    // Create Chart
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: label,
            datasets: data
        },
        options: {
            responsive: true,
            legend: {
                position: 'bottom',
            },
            animation: {
                duration: 1500,
            },
            tooltips: {
                callbacks: {
                    label: label_callback
                }
            }
        },
        plugins: [{
            afterDraw: function (chart) {
                if (chart.data.datasets[0].data.every(x => x == 0) === true) {
                    let ctx = chart.chart.ctx;
                    let width = chart.chart.width;
                    let height = chart.chart.height;
                    chart.clear();
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = "14px normal 'tahoma'";
                    ctx.fillText(wps_js._('no_data'), width / 2, height / 2);
                    ctx.restore();
                }
            }
        }]
    });
};

/**
 * Create Chart ID by Meta Box name
 *
 * @param meta_box
 */
wps_js.chart_id = function (meta_box) {
    return 'wp-statistics-' + meta_box + '-meta-box-chart';
};

/**
 * Generate Flat Random Color
 */
wps_js.random_color = function (i = false) {
    let colors = [
        [243, 156, 18, "#f39c12"],
        [52, 152, 219, "#3498db"],
        [192, 57, 43, "#c0392b"],
        [155, 89, 182, "#9b59b6"],
        [39, 174, 96, "#27ae60"],
        [230, 126, 34, "#e67e22"],
        [142, 68, 173, "#8e44ad"],
        [46, 204, 113, "#2ecc71"],
        [41, 128, 185, "#2980b9"],
        [22, 160, 133, "#16a085"],
        [211, 84, 0, "#d35400"],
        [44, 62, 80, "#2c3e50"],
        [241, 196, 15, "#f1c40f"],
        [231, 76, 60, "#e74c3c"],
        [26, 188, 156, "#1abc9c"],
        [46, 204, 113, "#2ecc71"],
        [52, 152, 219, "#3498db"],
        [155, 89, 182, "#9b59b6"],
        [52, 73, 94, "#34495e"],
        [22, 160, 133, "#16a085"],
        [39, 174, 96, "#27ae60"],
        [44, 62, 80, "#2c3e50"],
        [241, 196, 15, "#f1c40f"],
        [230, 126, 34, "#e67e22"],
        [231, 76, 60, "#e74c3c"],
        [236, 240, 241, "#9b9e9f"],
        [149, 165, 166, "#a65d20"]
    ];
    return colors[(i === false ? Math.floor(Math.random() * colors.length) : i)];
};

/**
 * Show Domain Icon
 */
wps_js.site_icon = function (domain) {
    return `<img src="https://www.google.com/s2/favicons?domain=${domain}" width="16" height="16" alt="${domain}" style="vertical-align: -3px;" />`;
};

/**
 * Enable/Disable WordPress Admin PostBox Ajax Request
 *
 * @param type
 */
wps_js.wordpress_postbox_ajax = function (type = 'enable') {
    let wordpress_postbox = jQuery('.postbox .hndle, .postbox .handlediv');
    if (type === 'enable') {
        wordpress_postbox.on('click', window.postboxes.handle_click);
    } else {
        wordpress_postbox.off('click', window.postboxes.handle_click);
    }
};

/**
 * Isset Property in Object
 *
 * @param obj
 */
wps_js.isset = function (obj) {
    let args = Array.prototype.slice.call(arguments, 1);

    for (let i = 0; i < args.length; i++) {
        if (!obj || !obj.hasOwnProperty(args[i])) {
            return false;
        }
        obj = obj[args[i]];
    }
    return true;
};

/**
 * Number Format
 *
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_point
 * @returns {*}
 */
wps_js.number_format = function (number, decimals, dec_point, thousands_point) {
    if (number == null || !isFinite(number)) {
        throw new TypeError("number is not valid");
    }
    if (!decimals) {
        let len = number.toString().split('.').length;
        decimals = len > 1 ? len : 0;
    }
    if (!dec_point) {
        dec_point = '.';
    }
    if (!thousands_point) {
        thousands_point = ',';
    }
    number = parseFloat(number).toFixed(decimals);
    number = number.replace(".", dec_point);

    let splitNum = number.split(dec_point);
    splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousands_point);
    number = splitNum.join(dec_point);
    return number;
};

/**
 * Set Equal Bigger Div Height For WordPress PostBox
 *
 * @param Dom_1
 * @param Dom_2
 */
wps_js.set_equal_height = function (Dom_1, Dom_2) {
    let tbl_h = jQuery(Dom_1).height();
    let ch_h = jQuery(Dom_2).height();
    let ex = Dom_2;
    let val = tbl_h;
    if (tbl_h < ch_h) {
        ex = Dom_1;
        val = ch_h;
    }
    jQuery(ex).css('height', val + 'px');
};

/**
 * Create Half WordPress Post Box
 *
 * @param div_class
 * @param div_id
 * @returns {string}
 */
wps_js.Create_Half_PostBox = function (div_class, div_id) {
    return `<div class="postbox-container wps-postbox-half ${div_class}"><div class="metabox-holder"><div class="meta-box-sortables"> <div class="postbox" id="${div_id}"> <div class="inside"></div></div></div></div></div>`;
};

/**
 * Check IS IP
 *
 * @param str
 * @returns {boolean}
 */
wps_js.isIP = function (str) {
    const octet = '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]?|0)';
    const regex = new RegExp(`^${octet}\\.${octet}\\.${octet}\\.${octet}$`);
    return regex.test(str);
};

/**
 * Get Link Params
 */
wps_js.getLinkParams = function (param, link = false) {
    if (!link) {
        link = window.location.href;
    }
    let v = link.match(new RegExp('(?:[\?\&]' + param + '=)([^&]+)'));
    return v ? v[1] : null;
};

/**
 * Sum array Of Item
 *
 * @param array
 * @returns {*}
 */
wps_js.sum = function(array) {
    return array.reduce(function (a, b) {
        return a + b;
    }, 0);
};