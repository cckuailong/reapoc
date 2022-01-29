function draw_conversion_chart()
{ 
    var data = new google.visualization.DataTable(rm_chart_conversion_data.table);

    // Set chart options
    var options = {/*is3D : true,*/
        title: rm_chart_conversion_data.title,
                height: 300,
        fontName: 'Titillium Web',
        pieSliceTextStyle: {fontSize: 12},
        titleTextStyle: {fontSize: 18, color: '#87c2db', bold: false},
        legend: {position: 'bottom', maxLines: 1, textStyle: {fontSize: 12}},
        /*chartArea: {left:20,top:0,width:'50%',height:'75%'},*/
        colors: ['#e69f9f', '#d6c4df', '#87c2db']};

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('rm_conversion_chart_div'));
    chart.draw(data, options);
}



jQuery(document).ready(function(){
    // Load the Visualization API and the corechart package.
    google.charts.load('current', {'packages': ['corechart', 'bar']});
    
    if (typeof draw_conversion_chart == 'function' && jQuery("#rm_conversion_chart_div").length>0)
            google.charts.setOnLoadCallback(draw_conversion_chart);

    if (typeof browser_usage_chart == 'function' && jQuery("#rm_browser_usage_chart_div").length>0)
        google.charts.setOnLoadCallback(browser_usage_chart);

    if (typeof draw_browser_conversion == 'function' && jQuery("#rm_browser_conversion_div").length>0)
        google.charts.setOnLoadCallback(draw_browser_conversion);

    if (typeof draw_timewise_stat == 'function')
        google.charts.setOnLoadCallback(draw_timewise_stat);
});
