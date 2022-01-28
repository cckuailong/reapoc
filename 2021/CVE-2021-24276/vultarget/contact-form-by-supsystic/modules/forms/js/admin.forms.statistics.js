var g_cfsChart = {};
jQuery(document).ready(function(){
	if(typeof(google) === 'undefined') {
		alert('Please check your Internet connection - we need it to load Google Charts Library from Google Server');
		return false;
	}
	cfsInitChartsControls();
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(cfsInitCharts);
});
function cfsInitCharts() {
	// Main charts
	if(typeof(cfsAllStats) !== 'undefined' && cfsAllStats.length) {
		cfsDrawChart( cfsAllStats, 'cfsMainStats' );
	} else {
		_cfsSwitchToNoStats('cfsMainStats');
	}
	jQuery(document).trigger('cfsAfterAdminStatsInit');
}
function _cfsSwitchToNoStats(chartId) {
	jQuery('.cfsChartShell[data-chart="'+ chartId+ '"]').hide();
	jQuery('.cfsNoStatsMsg[data-chart="'+ chartId+ '"]').show();
}
function cfsDrawPie( data, elmId, params ) {
	var dataForTbl = [[ toeLangCfs('Agent'), params.header ]];
	for(var i = 0; i < data.length; i++) {
		dataForTbl.push([ data[ i ].name+ ' ['+ data[ i ].email+ ']', parseInt(data[ i ].total_requests) ]);
	}
	var dataTbl = google.visualization.arrayToDataTable( dataForTbl )
	,	options = {
			legend: { position: 'right' }
		,	height: 250
		,	chartArea: {top: 10, left: 30}
	};

	var chart = new google.visualization.PieChart(document.getElementById( elmId ));

	chart.draw(dataTbl, options);
}
function cfsStrToMs(strDate) {
	var dateHours = strDate.split(' ');
	if(dateHours.length == 2) {
		strDate = dateHours[0]+ ' ';
		var hms = dateHours[1].split(':');

		for(var i = 0; i < 3; i++) {
			strDate += hms[ i ] ? hms[ i ] : '00';
			if(i < 2)
				strDate += ':';
		}
	}
	var strDateNew = str_replace(strDate, '-', '/');
	strDateNew = str_replace(strDateNew, '%', '');
	var date = new Date( strDateNew )
	,	res = 0;
	if(date) {
		res = date.getTime();
	}
	return res;
}
function cfsDrawChart( data, elmId, params ) {
	var dataForTbl = [['Date']]
	,	datesExists = {}
	,	dateFrom = jQuery('#cfsFormStatistics input[name=stat_from_txt][data-chart="'+ elmId+ '"]').val()
	,	dateTo = jQuery('#cfsFormStatistics input[name=stat_to_txt][data-chart="'+ elmId+ '"]').val()
	,	dateFromTs = 0
	,	dateToTs = 0
	,	$chartTypeBtn = jQuery('#cfsFormStatistics').find('.cfsStatChartTypeBtn[data-chart="'+ elmId +'"].focus')
	,	chartType = $chartTypeBtn && $chartTypeBtn.length ? $chartTypeBtn.data('type') : 'line';
	if(dateFrom) {
		dateFromTs = new Date( dateFrom ).getTime();
	}
	if(dateTo) {
		dateToTs = new Date( dateTo ).getTime();
	}
	for(var i = 0; i < data.length; i++) {
		dataForTbl[ 0 ].push( data[ i ].label );
		for(var j = 0; j < data[ i ].points.length; j++) {
			datesExists[ data[ i ].points[ j ].date ] = 1;
		}
	}
	var i = 1
	,	datesExistsSorted = [],
		isPointsOnly;
	for(var date in datesExists) {
		var newDate = cfsStrToMs(date);
		var currDate = new Date(newDate);
		var	currTs = currDate.getTime();
		if((dateFromTs && currTs < dateFromTs) || (dateToTs && currTs > dateToTs)) {
			continue;
		}
		datesExistsSorted.push({str: date, dateObj: currDate});
		datesExists[ date ] = i;
		i++;
	}
	datesExistsSorted.sort( _cfsSortByDateClb );
	isPointsOnly = datesExistsSorted.length < 2;
	for(var i = 0; i < data.length; i++) {
		for(var k = 0; k < datesExistsSorted.length; k++) {

			var dateFound = false
			,	date = datesExistsSorted[ k ].str
			,	chartDate = isPointsOnly ? _cfsFormatDate(datesExistsSorted[ k ].dateObj) : datesExistsSorted[ k ].dateObj
			,	pI = k + 1;
			for(var j = 0; j < data[ i ].points.length; j++) {
				if(data[ i ].points[ j ].date == date) {
					dateFound = parseInt(data[ i ].points[ j ].total_requests);
					break;
				}
			}
			var currPoints = dataForTbl[ pI ] ? dataForTbl[ pI ] : [ chartDate ];
			currPoints[ i + 1 ] = dateFound ? dateFound : 0;
			dataForTbl[ pI ] = currPoints;
		}
	}
	var baseInitData = {
		tbl: null, chart: null, allData: data
	};
	if(g_cfsChart[ elmId ] && g_cfsChart[ elmId ].viewportRefreshed) {
		baseInitData.viewportRefreshed = g_cfsChart[ elmId ].viewportRefreshed;
	}
	g_cfsChart[ elmId ] = baseInitData;
	g_cfsChart[ elmId ].tbl = google.visualization.arrayToDataTable( dataForTbl );
	var options = {
		legend: { position: 'right' }
	,	height: 350
	,	chartArea: {top: 10, left: 30}
	,	animation:{
			duration: 1000
		,	easing: 'out'
		}
	,	isStacked: true
	};
	if(isPointsOnly) {
		// To show points
		options.pointSize = 5;
	} else {
		// The explorer option allows users to pan and zoom Google charts. This feature is experimental and may change in future releases.
		// see https://developers.google.com/chart/interactive/docs/gallery/linechart#configuration-options
		// Note: The explorer only works with continuous axes (such as numbers or dates).
		// On practice: this featureis not work if there are only points on chart.
		options.explorer = {
			actions: ['dragToZoom', 'rightClickToReset']
		,	axis: 'horizontal'
		};
	}
	switch(chartType) {
		case 'bar':
			g_cfsChart[ elmId ].chart = new google.visualization.ColumnChart(document.getElementById( elmId ));
			break;
		case 'line': default:
			g_cfsChart[ elmId ].chart = new google.visualization.LineChart(document.getElementById( elmId ));
			break;
	}
	g_cfsChart[ elmId ].options = options;
	g_cfsChart[ elmId ].chart.draw(g_cfsChart[ elmId ].tbl, options);
}
function cfsRefreshCharts() {
	for(var elmId in g_cfsChart) {
		if(g_cfsChart[ elmId ]
			&& g_cfsChart[ elmId ].chart
			&& g_cfsChart[ elmId ].tbl
			&& !g_cfsChart[ elmId ].viewportRefreshed
		) {
			g_cfsChart[ elmId ].chart.draw(g_cfsChart[ elmId ].tbl, g_cfsChart[ elmId ].options);
			g_cfsChart[ elmId ].viewportRefreshed = true;	// To refresh it only once - when first time open statistics tab
		}
	}
}
function _cfsFormatDate(date) {
	var monthNames = ["Jan", "Feb", "Mar","Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct","Nov", "Dec"]
	,	day = date.getDate()
	,	monthIndex = date.getMonth()
	,	year = date.getFullYear();
	return monthNames[monthIndex] + ' ' + day + ', ' + year;
}
function _cfsSortByDateClb(a, b) {
	var aTime = a.dateObj.getTime()
	,	bTime = b.dateObj.getTime();
	if(aTime > bTime)
		return 1;
	if(aTime < bTime)
		return -1;
	return 0;
}
function cfsInitChartsControls() {
	// Date selection controls
	jQuery('#cfsFormStatistics').find('input[name=stat_from_txt],input[name=stat_to_txt]').datepicker({
		onSelect: function() {
			var chartId = jQuery(this).data('chart');
			jQuery('#cfsFormStatistics').find('.cfsStatClearDateBtn[data-chart="'+ chartId+ '"]').show();
			cfsDrawChart( g_cfsChart[chartId].allData, chartId );
		}
	});
	jQuery('.cfsStatClearDateBtn').click(function(){
		var chartId = jQuery(this).data('chart');
		jQuery('#cfsFormStatistics')
			.find('input[name=stat_from_txt][data-chart="'+ chartId+ '"],input[name=stat_to_txt][data-chart="'+ chartId+ '"]')
			.val('');
		jQuery(this).hide();
		cfsDrawChart( g_cfsChart[chartId].allData, chartId );
		return false;
	});
	// Chart type controls
	jQuery('#cfsFormStatistics').find('.cfsStatChartTypeBtn').click(function(){
		var chartId = jQuery(this).data('chart')
		,	type = jQuery(this).data('type');
		jQuery('#cfsFormStatistics').find('.cfsStatChartTypeBtn[data-chart="'+ chartId+ '"]').removeClass('focus');
		jQuery(this).addClass('focus');
		setCookieCfs('cfsChartType_'+ chartId, type);
		cfsDrawChart( g_cfsChart[chartId].allData, chartId );
		return false;
	});
	// Chart group controls
	jQuery('#cfsFormStatistics').find('.cfsStatChartGroupBtn').click(function(){
		var chartId = jQuery(this).data('chart')
		,	group = jQuery(this).data('stat-group')
		,	self = this;
		jQuery('#cfsFormStatistics').find('.cfsStatChartGroupBtn[data-chart="'+ chartId+ '"]').removeClass('focus');
		cfsGetStats(chartId, function(data) {
			g_cfsChart[chartId].allData = data;
			jQuery(self).addClass('focus');
			setCookieCfs('cfsChartGroup_'+ chartId, group);
			cfsDrawChart( g_cfsChart[chartId].allData, chartId );
		}, this, {group: group});
		return false;
	});
	// Export data in CSV
	jQuery('.cfsStatExportCsv').click(function(){
		var chartId = jQuery(this).data('chart')
		,	baseUrl = ''
		,	group = getCookieCfs( 'cfsChartGroup_'+ chartId );
		if(!group)
			group = 'day';
		if(jQuery(this).data('base-url')) {
			baseUrl = jQuery(this).data('base-url');
		} else {
			baseUrl = jQuery(this).attr('href');
			jQuery(this).data('base-url', baseUrl);
		}
		jQuery(this).attr('href', baseUrl+ '&group='+ group+ '&chart_id='+ chartId+ '&id='+ cfsForm.id);
	});
	// Clear stats btn
	jQuery('#cfsStatClear').click(function(){
		if(confirm(toeLangCfs('Are you sure want to clear all Form Statistics?'))) {
			jQuery.sendFormCfs({
				btn: this
			,	data: {mod: 'statistics', action: 'clearForForm', id: cfsForm.id}
			,	onSuccess: function(res) {
					if(!res.error) {
						toeReload();
					}
				}
			});
		}
		return false;
	});
	// Setup loaded defaults options
	jQuery('.cfsChartArea').each(function(){
		var chartId = jQuery(this).attr('id')
		,	sdChartType = getCookieCfs( 'cfsChartType_'+ chartId )
		,	sdChartGroup = getCookieCfs( 'cfsChartGroup_'+ chartId );
		// Setup loaded chart type
		if(!sdChartType)
			sdChartType = 'line';	// By default
		if(sdChartType) {
			jQuery('#cfsFormStatistics')
				.find('.cfsStatChartTypeBtn[data-chart="'+ chartId+ '"][data-type="'+ sdChartType+ '"]')
				.addClass('focus');
		}
		// Setup loaded chart group
		if(!sdChartGroup)
			sdChartGroup = 'day';
		if(sdChartGroup) {
			jQuery('#cfsFormStatistics')
				.find('.cfsStatChartGroupBtn[data-chart="'+ chartId+ '"][data-stat-group="'+ sdChartGroup+ '"]')
				.addClass('focus');
		}
	});
}
function cfsGetStats(chartId, clb, btn, addData) {
	var sendData = {mod: 'statistics', action: 'getStats', chart_id: chartId, id: cfsForm.id};
	if(addData) {
		sendData = jQuery.extend(sendData, addData);
	}
	jQuery.sendFormCfs({
		btn: btn
	,	data: sendData
	,	onSuccess: function(res) {
			if(!res.error && res.data) {
				clb( res.data );
			}
		}
	});
}
