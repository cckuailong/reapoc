<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class wsmStatistics{
    private $objDatabase;
    function __construct(){
        $this->wsm_addShortCodes();
        $this->objDatabase=new wsmDatabase();
    }
    function wsm_addShortCodes(){
        add_shortcode( WSM_PREFIX.'_showDayStats', array($this,WSM_PREFIX.'_showDayStats') );
        add_shortcode( WSM_PREFIX.'_showDayStatBox', array($this,WSM_PREFIX.'_showDayStatBox') );
        add_shortcode( WSM_PREFIX.'_showGenStats', array($this,WSM_PREFIX.'_showGenStats') );
        add_shortcode( WSM_PREFIX.'_showLastDaysStats', array($this,WSM_PREFIX.'_showLastDaysStats') );
        add_shortcode( WSM_PREFIX.'_showForeCast', array($this,WSM_PREFIX.'_showForeCast') );
        add_shortcode( WSM_PREFIX.'_showGeoLocation', array($this,WSM_PREFIX.'_showGeoLocation') );
        add_shortcode( WSM_PREFIX.'_showCurrentStats', array($this,WSM_PREFIX.'_showCurrentStats') );
        add_shortcode( WSM_PREFIX.'_showDayStatsGraph', array($this,WSM_PREFIX.'_showDayStatsGraph') );
        add_shortcode( WSM_PREFIX.'_showLastDaysStatsChart', array($this,WSM_PREFIX.'_showLastDaysStatsChart') );
        add_shortcode( WSM_PREFIX.'_showRecentVisitedPages', array($this,WSM_PREFIX.'_showRecentVisitedPages') );
        add_shortcode( WSM_PREFIX.'_showPopularPages', array($this,WSM_PREFIX.'_showPopularPages') );
        add_shortcode( WSM_PREFIX.'_showPopularReferrers', array($this,WSM_PREFIX.'_showPopularReferrers') );
        add_shortcode( WSM_PREFIX.'_showMostActiveVisitors', array($this,WSM_PREFIX.'_showMostActiveVisitors') );
        add_shortcode( WSM_PREFIX.'_showMostActiveVisitorsDetails', array($this,WSM_PREFIX.'_showMostActiveVisitorsDetails') );
        add_shortcode( WSM_PREFIX.'_showMostActiveVisitorsGeo', array($this,WSM_PREFIX.'_showMostActiveVisitorsGeo') );
        add_shortcode( WSM_PREFIX.'_showActiveVisitorsByCountry', array($this,WSM_PREFIX.'_showActiveVisitorsByCountry') );
        add_shortcode( WSM_PREFIX.'_showActiveVisitorsByCity', array($this,WSM_PREFIX.'_showActiveVisitorsByCity') );
        add_shortcode( WSM_PREFIX.'_showMostActiveVisitorsByCity', array($this,WSM_PREFIX.'_showMostActiveVisitorsByCity') );
        add_shortcode( WSM_PREFIX.'_showRecentVisitedPagesDetails', array($this,WSM_PREFIX.'_showRecentVisitedPagesDetails') );
        add_shortcode( WSM_PREFIX.'_showStatFilterBox', array($this,WSM_PREFIX.'_showStatFilterBox') );
        add_shortcode( WSM_PREFIX.'_showTrafficStatsList', array($this,WSM_PREFIX.'_showTrafficStatsList') );
        add_shortcode( WSM_PREFIX.'_showRefferStatsBox', array($this,WSM_PREFIX.'_showRefferStatsBox') );
        add_shortcode( WSM_PREFIX.'_showTopReferrerList', array($this,WSM_PREFIX.'_showTopReferrerList') );
		add_shortcode( WSM_PREFIX.'_showSearchEngineSummary', array($this,WSM_PREFIX.'_showSearchEngineSummary') );
		add_shortcode( WSM_PREFIX.'_showVisitorsDetail', array($this,WSM_PREFIX.'_showVisitorsDetail') );
		add_shortcode( WSM_PREFIX.'_showVisitorsDetailGraph', array($this,WSM_PREFIX.'_showVisitorsDetailGraph') );
		add_shortcode( WSM_PREFIX.'_showStatKeywords', array($this,WSM_PREFIX.'_showStatKeywords') );
		add_shortcode( WSM_PREFIX.'_showGeoLocationGraph', array( $this, WSM_PREFIX.'_showGeoLocationGraph'));
		add_shortcode( WSM_PREFIX.'_showGeoLocationDetails', array( $this, WSM_PREFIX.'_showGeoLocationDetails'));
		add_shortcode( WSM_PREFIX.'_showContentByURL', array( $this, WSM_PREFIX.'_showContentByURL'));
		add_shortcode( WSM_PREFIX.'_showContentByURLStats', array( $this, WSM_PREFIX.'_showContentByURLStats'));
		add_shortcode( WSM_PREFIX.'_showTitleCloud', array( $this, WSM_PREFIX.'_showTitleCloud'));
		add_shortcode( WSM_PREFIX.'_showIPExclustion', array( $this, WSM_PREFIX.'_showIPExclustion'));
		add_shortcode( WSM_PREFIX.'_showGeneralStats', array( $this, WSM_PREFIX.'_showGeneralStats'));
		add_shortcode( WSM_PREFIX.'_showEachVisitorsDetailGraph', array($this,WSM_PREFIX.'_showEachVisitorsDetailGraph') );
    }
    function wsm_getDatabaseObject(){
        return $this->objDatabase;
    }       
    function wsm_showDayStats($atts, $content=""){
        global $wsmAdminJavaScript;
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $atts = shortcode_atts( array(
            'title' => __('Today','wp-stats-manager'),
            'date' => wsmGetCurrentDateByTimeZone('Y-m-d')
            ), $atts,WSM_PREFIX.'_showDayStats');
        $todayPageViews=$this->objDatabase->fnGetTotalPageViewCount('Today');
        $todayVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Today');
        $onlineVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Online');
        $pageViews=0;
        if($todayPageViews>0 && $todayVisitors>0){
            $pageViews=($todayPageViews/$todayVisitors);
        }
        $averageVisit=$this->objDatabase->fnGetAverageVisitLength('Today');
        if($averageVisit>0){
            $averageVisit=wsmConvertSecondsToTimeFormat($averageVisit);
        }else{
            $averageVisit=0;
        }
        $todayPageViews=number_format_i18n($todayPageViews,0);
        $todayVisitors=number_format_i18n($todayVisitors,0);
        $onlineVisitors=number_format_i18n($onlineVisitors,0);
        $firstTimeVisitors=number_format_i18n($this->objDatabase->fnGetFirstTimeVisitorCount('Today'),0);
        $html='<ul class="wsmUL wsmTodaysStats">
        <li><div class="wsmCounters"><span class="wsmH2Number">'.$todayPageViews.'</span><span id="'.WSM_PREFIX.'TodayTotalPageViews" class="wsmBoxUPDdataTIP" data-value="'.$todayPageViews.'"></span></div><label>'.__('Page Views','wp-stats-manager').'</label></li>
        <li><div class="wsmCounters"><span class="wsmH2Number">'.$todayVisitors.'</span><span id="'.WSM_PREFIX.'TodayTotalVisitors" class="wsmBoxUPDdataTIP" data-value="'.$todayVisitors.'"></span></div><label>'.__('Visitors','wp-stats-manager').'</label></li>
        <li><div class="wsmCounters"><span class="wsmH2Number">'.$firstTimeVisitors.'</span><span id="'.WSM_PREFIX.'TodayTotalFirstVisitors" class="wsmBoxUPDdataTIP"  data-value="'.$firstTimeVisitors.'"></span></div><label>'.__('First Time Visitors','wp-stats-manager').'</label></li>
        <li style="display:none"><div class="wsmCounters"><span class="wsmH2Number">'.$onlineVisitors.'</span><span id="'.WSM_PREFIX.'TodayOnlineUsers" class="wsmBoxUPDdataTIP"  data-value="'.$onlineVisitors.'"></span></div><label><img src="'.WSM_URL.'/images/online-red.gif" alt="Online"/>&nbsp;'.__('Users Online','wp-stats-manager').'</label></li>
		<li><div class="wsmCounters"><span class="wsmH2Number">'.$averageVisit.'</span></div><label>'.__('Average Visit Length','wp-stats-manager').'</label></li>
        <li><div class="wsmCounters"><span class="wsmH2Number">'.number_format_i18n($pageViews,1).'</span></div><label>'.__('Page Views Per Visit','wp-stats-manager').'</label></li>
        </ul>';
        $wsmAdminJavaScript.='arrLiveStats.push(\''.WSM_PREFIX.'TodayTotalPageViews\',\''.WSM_PREFIX.'TodayTotalVisitors\',\''.WSM_PREFIX.'TodayTotalFirstVisitors\',\''.WSM_PREFIX.'TodayOnlineUsers\');';
        return $html;
    }
    function wsm_showGenStats($atts, $content=""){
        global $wsmAdminJavaScript;
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $atts = shortcode_atts( array(
            'title' => __('Today','wp-stats-manager')
            ), $atts, WSM_PREFIX.'_showGenStats');
        $totalPageViews=$this->objDatabase->fnGetTotalPageViewCount();
        $totalVisitors=$this->objDatabase->fnGetTotalVisitorsCount();
        $onlineVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Online');
        $pageViews=0;
        if($totalPageViews>0 && $totalVisitors>0){
            $pageViews=($totalPageViews/$totalVisitors);
        }
        $totalPageViews=number_format_i18n($totalPageViews,0);
        $totalVisitors=number_format_i18n($totalVisitors,0);
        $pageViews=number_format_i18n($pageViews,2);
        $onlineVisitors=number_format_i18n($onlineVisitors,0);
        $html='<ul class="wsmUL wsmGenStats">
		<li class="greenTxt" style="display:none"><label>'.__('User Online','wp-stats-manager').'</label><div class="wsmCounters wsmPushRight"><span id="'.WSM_PREFIX.'GenUserOnline" class="wsmBoxUPDdataTIP" data-value="'.$onlineVisitors.'"></span><span class="wsmH2Number"><a target="_blank" href="?page=wsm_traffic&subPage=UsersOnline&subTab=summary">'.$onlineVisitors.'</a></span></div></li>
        <li><label>'.__('Total Page Views','wp-stats-manager').'</label><div class="wsmCounters wsmPushRight"><span id="'.WSM_PREFIX.'GenTotalPageViews" class="wsmBoxUPDdataTIP" data-value="'.$totalPageViews.'"></span><span class="wsmH2Number">'.$totalPageViews.'</span></div></li>
        <li><label>'.__('Total Visitors','wp-stats-manager').'</label><div class="wsmCounters wsmPushRight"><span id="'.WSM_PREFIX.'GenTotalVisitors" class="wsmBoxUPDdataTIP"  data-value="'.$totalVisitors.'"></span><span class="wsmH2Number">'.$totalVisitors.'</span></div></li>
        <li><label>'.__('Page Views Per Visit','wp-stats-manager').'</label><div class="wsmCounters wsmPushRight"><span class="wsmH2Number">'.$pageViews.'</span></div></li>
        <li><label>'.__('Last Hits Time','wp-stats-manager').'</label><div class="wsmCounters wsmPushRight"><span class="wsmH2Number">'.wsmConvertDateUTCtoTimeZone(get_option(WSM_PREFIX.'_lastHitTime'),'H:i:s d F Y').'</span></div></li>
        </ul>';
        $wsmAdminJavaScript.='arrLiveStats.push(\''.WSM_PREFIX.'GenTotalPageViews\',\''.WSM_PREFIX.'GenTotalVisitors\',\''.WSM_PREFIX.'GenUserOnline\');';
        return $html;
    }    
    function wsm_showLastDaysStats($atts, $content=""){
        global $wsmAdminJavaScript;
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $atts = shortcode_atts( array(
            'title' => __('Today','wp-stats-manager'),
            'days' =>''
            ), $atts, WSM_PREFIX.'_showLastDaysStats');
        $nDays=$atts['days'];
        if($nDays=='' || $nDays<1){
            $oDays=get_option(WSM_PREFIX.'ChartDays');
            $nDays=($oDays!='' && $oDays>0) ?$oDays:60;
        }
        $totalPageViews=$this->objDatabase->fnGetTotalPageViewCount($nDays);
        $totalVisitors=$this->objDatabase->fnGetTotalVisitorsCount($nDays);
        $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($nDays);
        $pageViews=0;
        if($totalPageViews>0 && $totalVisitors>0){
            $pageViews=($totalPageViews/$totalVisitors);
        }
        $html='<ul class="wsmUL wsmLast2months">
        <li><div class="wsmH2Number"><span class="wsmH2Number">'.number_format_i18n($totalPageViews,0).'</span><span id="'.WSM_PREFIX.'LastMonthsTotalPageViews" class="wsmBoxUPDdataTIP" data-value="'.$totalPageViews.'"></span></div><label>'.__('Page Views','wp-stats-manager').'</label></li>
        <li><div class="wsmH2Number"><span class="wsmH2Number">'.number_format_i18n($totalVisitors,0).'</span><span id="'.WSM_PREFIX.'LastMonthsTotalVisitors" class="wsmBoxUPDdataTIP" data-value="'.$totalVisitors.'"></div><label>'.__('Visitors','wp-stats-manager').'</label></li>
        <li><div class="wsmH2Number"><span class="wsmH2Number">'.number_format_i18n($firstTimeVisitors,0).'</span><span id="'.WSM_PREFIX.'LastMonthsTotalFirstVisitors" class="wsmBoxUPDdataTIP"  data-value="'.$firstTimeVisitors.'"></span></div><label>'.__('First Time Visitors','wp-stats-manager').'</label></li>
        <li><div class="wsmH2Number">'.number_format_i18n($pageViews,2).'</div><label>'.__('Page Views Per Visit','wp-stats-manager').'</label></li>
        </ul>';
		 		$wsmAdminJavaScript.='arrLiveStats.push(\''.WSM_PREFIX.'LastMonthsTotalPageViews\',\''.WSM_PREFIX.'LastMonthsTotalVisitors\',\''.WSM_PREFIX.'LastMonthsTotalFirstVisitors\');';
        return $html;
    }
    function wsm_showForeCast($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $atts = shortcode_atts( array(
            'title' => __('Forecast','wp-stats-manager')
            ), $atts, WSM_PREFIX.'_showForeCast');
        $nDays=get_option(WSM_PREFIX.'ChartDays');
        $nDays=($nDays!='' && $nDays>0) ?$nDays:30;        
        $arrForeCast=$this->objDatabase->fnGetTodaysForeCastData();       
        $h=ltrim(wsmGetCurrentDateByTimeZone('H'),'0');        
        $hourVisitors=(int)$arrForeCast['visitors'][$h];
        $arrLineData=$this->objDatabase->fnGetHistoricalDayStatsByDays($nDays);
        
        $dayWiseVisitors=array_slice($arrLineData['visitors'], -8,7);
        $yArray=array();
        foreach($dayWiseVisitors as $key=>$day){
            array_push($yArray,$day[1]);
        }     
        $toDayForeCast=round(wsmFnCalculateForeCastData(array_keys($yArray),$yArray,7),0);
        $currentVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Hour');
        if($hourVisitors<$currentVisitors){
            $hourVisitors=$currentVisitors;
        }
        $todayVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Today');
        if($toDayForeCast>$todayVisitors){
            $todayVisitors=$toDayForeCast;
        }
        $visitors7DaysHour=$this->objDatabase->fnGetTotalVisitorsCount('7dayBeforeHour');
        $visitors14DaysHour=$this->objDatabase->fnGetTotalVisitorsCount('14dayBeforeHour');
        $visitors7Days=$this->objDatabase->fnGetTotalVisitorsCount('7dayBefore');
        $visitors14Days=$this->objDatabase->fnGetTotalVisitorsCount('14dayBefore');
        $hours7Class=$hours14Class=$day7Class=$day14Class="";        
        $hours7Change=wsmGetChangeInPercentage($visitors7DaysHour,$hourVisitors);
        $hours7Class=$hours7Change<0?'wsmColorRed':'';
        $day7Change=wsmGetChangeInPercentage($visitors7Days,$todayVisitors);
        $day7Class=$day7Change<0?'wsmColorRed':'';
        $hours14Change=wsmGetChangeInPercentage($visitors14DaysHour,$hourVisitors);
        $hours14Class=$hours14Change<0?'wsmColorRed':'';
        $day14Change=wsmGetChangeInPercentage($visitors14Days,$todayVisitors);
        $day14Class=$day14Change<0?'wsmColorRed':'';
        $html='<div class="wsmForeCast">
        <div class="wsmForecastHeader">'.__($atts['title'],'wp-stats-manager').'</div>
        <ul class="wsmUL">
        <li><div class="wsmLeftBlock"><label>'.__('Current Hour','wp-stats-manager').'</label><div class="wsmH2Number">'.number_format_i18n($hourVisitors,0).'</div><label>'.__('Visitors','wp-stats-manager').'</label></div><div class="wsmRightBlock"><div class="wsmTop"><span class="wsmH2Number '.$hours7Class.'">'.number_format_i18n($hours7Change,2).'%</span><span class="wsmLabel">'.__('than 7 days ago','wp-stats-manager').'</span></div><div class="wsmBottom"><span class="wsmH2Number '.$hours14Class.'">'.number_format_i18n($hours14Change,2).'%</span><span>'.__('than 14 days ago','wp-stats-manager').'</span></div></div></li>
        <li><div class="wsmLeftBlock"><label>'.__('Current Day','wp-stats-manager').'</label><div class="wsmH2Number">'.number_format_i18n($todayVisitors,0).'</div><label>'.__('Visitors','wp-stats-manager').'</label></div><div class="wsmRightBlock"><div class="wsmTop"><span class="wsmH2Number '.$day7Class.'">'.number_format_i18n($day7Change,2).'%</span><span class="wsmLabel">'.__('than 7 days ago','wp-stats-manager').'</span></div><div class="wsmBottom"><span class="wsmH2Number '.$day14Class.'">'.number_format_i18n($day14Change,2).'%</span><span>'.__('than 14 days ago','wp-stats-manager').'</span></div></div></li>
        </ul>
        </div>';
        return $html;
    }
    function wsm_showGeoLocation($atts, $content=""){

		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts( array(
            'title' => __('GeoLocation','wp-stats-manager'),
            'width' =>'400px',
            'height' =>'200px',
            'id'=>'showGeoLocationChart',
            ), $atts, WSM_PREFIX.'_showGeoLocation');
        //$arrStats=$this->objDatabase->fnGetTotalPageViewsByCountries(10);
        $arrStats=$this->objDatabase->fnGetTotalVisitorsByCountries(10);
        $html='<div id="pieGeolocation"></div>';
      
        if(is_array($arrStats) && count($arrStats)>0)
        {
            $arrCountries=array();
            foreach($arrStats as $key=>$row){
                array_push($arrCountries,array($row['name']." (".number_format($row['visitors']).")",(int)$row['visitors']));
                //array_push($arrCountries['labels'],$row['name'].' : '.$row['pageViews']);
            }
            $wsmAdminJavaScript.="var colors = ['#4573a7','#aa4644','#89a54e','#806a9b','#3d97af','#d9853c','#91a7ce','#a47c7c','#5cb85c','#74d6fe'];
            jQuery.jqplot.config.enablePlugins = true;
            var data = ".json_encode($arrCountries).";
            var piePlot = jQuery.jqplot('pieGeolocation', [data], {
            seriesColors :colors,
            height: 220,
            textColor: \"#ffffff\",
            fontFamily:'-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Oxygen-Sans,Ubuntu,Cantarell,\"Helvetica Neue\",sans-serif',
            grid:{
            drawBorder: false,
            drawGridlines: false,
            background: '#ffffff',
            shadow:false
            },
            seriesDefaults: {
            renderer: jQuery.jqplot.PieRenderer,
            rendererOptions: {
            showDataLabels: false,
            padding: 2,
            sliceMargin: 3,
            shadow: false,
            diameter: 180
            },
            pointLabels: { show: true }
            },
            legend: {
            show: true,
            location :'e',
            marginRight:80
            },
            highlighter: {
            show: true,
            useAxesFormatters: false,
            tooltipFormatString: '%s',
			tooltipContentEditor: function (str, seriesIndex, pointIndex, plot) {
					var result = str.split(',');
					var ind = parseInt(result.length)-1;
					result.splice(ind,1);
					var str = result.join();
					return str;
				}
            },
            cursor :{
            show : false,
            followMouse : false,
            useAxesFormatters:false
            }
            });
            jQuery(window).on('resize',function(){
            piePlot.replot();
            });
            ";
        }
        else
			$html = "<p class='wsmCenter'>".__('Data / Statistics are not available.','wp-stats-manager')."</p>";
			
        return $html;
    }
    function wsm_getTopChartBar($chart='Today'){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $html='';
        $html.='<div class="wsmTopChartBar"><div class="wsmChartLegend"></div><div class="wsmBarCompare"><a href="#" class="wsmButton" style="pointer-events: none;cursor: default;"><img src="'.WSM_URL.'images/page_white_copy.png" alt="'.__('COMPARE','wp-stats-manager').'"/>'.__('COMPARE','wp-stats-manager').'</a><a href="#" class="wsmButton" data-chart="No"><img src="'.WSM_URL.'images/prohibition_button.png" alt="'.__('No','wp-stats-manager').'"/>'.__('No','wp-stats-manager').'</a><a title="'.__('Bounce Rate (%)','wp-stats-manager').'" href="#" class="wsmButton" data-chart="Bounce"><img src="'.WSM_URL.'images/arrow_rotate_anticlockwise.png" alt="'.__('Bounce Rate (%)','wp-stats-manager').'"/>'.__('Bounce','wp-stats-manager').'</a><a title="'.__('Page Views per Visit','wp-stats-manager').'" href="#" class="wsmButton" data-chart="Ppv"><img src="'.WSM_URL.'images/chart_line.png" alt="'.__('Page Views per Visit','wp-stats-manager').'"/>'.__('Ppv','wp-stats-manager').'</a><a title="'.__('New Visitors (%)','wp-stats-manager').'" href="#" class="wsmButton" data-chart="Nvis"><img src="'.WSM_URL.'images/chart_line_add.png" alt="'.__('New Visitors (%)','wp-stats-manager').'"/>'.__('Nvis','wp-stats-manager').'</a><a title="'.__('Average Online','wp-stats-manager').'" href="#" class="wsmButton" data-chart="Online"><img src="'.WSM_URL.'images/network_wireless.png" alt="'.__('Average Online','wp-stats-manager').'"/>'.__('Online','wp-stats-manager').'</a>';
        if($chart=='Today'){
            $html.='<a title="'.__('Yesterday','wp-stats-manager').'" href="#" class="wsmButton" data-chart="yesterday"><img src="'.WSM_URL.'images/calendar.png" alt="'.__('yesterday','wp-stats-manager').'"/>'.__('-1','wp-stats-manager').'</a><a title="'.__('7 Days Back','wp-stats-manager').'" href="#" class="wsmButton" data-chart="7daysback"><img src="'.WSM_URL.'images/calendar.png" alt="'.__('7 Days Back','wp-stats-manager').'"/>'.__('-7','wp-stats-manager').'</a><a title="'.__('14 Days Back','wp-stats-manager').'" href="#" class="wsmButton" data-chart="14daysback"><img src="'.WSM_URL.'images/calendar.png" alt="'.__('14 Days Back','wp-stats-manager').'"/>'.__('-14','wp-stats-manager').'</a>';
        }
        $html.='</div></div>';
        return $html;
    }
    function wsm_showCurrentStats($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts( array(
            'title' => __('Get Current Stats','wp-stats-manager'),
            'id' =>'barStacked',
            'width' =>'1140px',
            'height' =>'400px'
            ), $atts, WSM_PREFIX.'_showCurrentStats');
        $arrChartStats=$this->objDatabase->fnGetCurrentDayHourlyStats();
        $tDate=wsmGetDateByInterval('-1 days','d');
        $p7Date=wsmGetDateByInterval('-7 days','d');
        $p14Date=wsmGetDateByInterval('-14 days','d');
        $html='<div class="chartContainer">';
        $html.=$this->wsm_getTopChartBar();
        $html.='<div id="'.$atts['id'].'"></div></div>';
        $arrBarData=array('firstTime'=>array(),'XLabels'=>array(),'colors'=>array(),'visitors'=>array(),'pageViews'=>array(),'Bounce'=>array(),'ppv'=>array(),'newVisitor'=>array(),'avgOnline'=>array());
        $h=wsmGetCurrentDateByTimeZone('H');

        $arrColors=array('rgba(244,81,81,1)','rgba(251,194,70,1)','rgba(87,135,184,1)');
        $arrLineColors=array('rgba(153, 0, 0, 1)','rgba(202, 121, 0, 1)','rgba(18, 56, 114, 1)');
        $keyLabels=array(__('First Time Visitor','wp-stats-manager'),__('Visitors','wp-stats-manager'),__('Page Views','wp-stats-manager'));

        $wsmAdminJavaScript.="arrLiveStats.push('".WSM_PREFIX."CurrentHourStats_".$atts['id']."_".$h."');
        var {$atts['id']}_firstTime = ".json_encode($arrChartStats['today']['firstTime']).";
        var {$atts['id']}_visitors = ".json_encode($arrChartStats['today']['visitors']).";
        var {$atts['id']}_pageViews = ".json_encode($arrChartStats['today']['pageViews']).";
        var {$atts['id']}_bounceRate = ".json_encode($arrChartStats['today']['Bounce']).";
        var {$atts['id']}_ppv = ".json_encode($arrChartStats['today']['ppv']).";
        var {$atts['id']}_newVisitor = ".json_encode($arrChartStats['today']['newVisitor']).";
        var {$atts['id']}_avgOnline = ".json_encode($arrChartStats['today']['avgOnline']).";
        var {$atts['id']}_legendIndex=[];
        var colors = ".json_encode($arrColors).";
        var legendLabels=".json_encode($keyLabels).";
        var keyColors=colors.slice();
        var XLabels=".json_encode($arrChartStats['today']['XLabels']).";
        var {$atts['id']}_Data=[{$atts['id']}_firstTime,{$atts['id']}_visitors,{$atts['id']}_pageViews ];
        var seriesRenderee=[{renderer:jQuery.jqplot.BarRenderer,fillAndStroke:true},{renderer:jQuery.jqplot.BarRenderer,fillAndStroke:true},{renderer:jQuery.jqplot.BarRenderer,fillAndStroke:true}];
        //var seriesRenderee=[{fill:true,fillAndStroke:true},{fill:true,fillAndStroke:true},{fill:true,fillAndStroke:true}];
        var {$atts['id']}_options=  {
        // Tell the plot to stack the bars.
        series:seriesRenderee,
        stackSeries: false,
        seriesColors :colors,
        height:'{$atts['height']}',
        gridPadding:{
        right:40,
        left:40,
        top:20,
        bottom:60
        },
        captureRightClick: true,
        seriesDefaults:{
        rendererOptions: {
        barMargin: 6,
        // Highlight bars when mouse button pressed.
        // Disables default highlighting on mouse over.
        highlightMouseDown: true,
        startAlpha:{$h},
        shadow:false,
        smooth: true,
        fillToZero: true,
        barPadding:0,
        pointLabels: { show: true }
        }
        },
        axes: {
        xaxis: {
        renderer: jQuery.jqplot.CategoryAxisRenderer,
        ticks : XLabels
        },
        yaxis: {
        padMin: 0,
        tickOptions: {
            formatString: '%d'
        }
        },
        y2axis: {
        padMin: 0,
        min:0,
        autoscale:true,
        tickOptions: {
        showGridline: false,
        formatString: '%d'
        }
        }
        },
        legend: {
        labels :legendLabels,
        show: true,
        location: 'nw',
        placement: 'inside',
        renderer: jQuery.jqplot.EnhancedLegendRenderer,
        rendererOptions: {
        numberRows: 1,
        numberColumns :4,
        seriesToggle: true
        }
        },
        highlighter: {
        show:true,
        tooltipLocation: 'ne',
        useAxesFormatters: true,
        sizeAdjust: 2.5,
        formatString:'%s, %P',
        tooltipContentEditor : function(str, seriesIndex, pointIndex, plot){
        return '<span style=\'background:'+plot.seriesColors[seriesIndex]+'\'></span>'+plot.legend.labels[seriesIndex]+ \": \" + plot.data[seriesIndex][pointIndex];
        }
        },
        cursor :{
        show : false,
        followMouse : false,
        useAxesFormatters:false
        }
        };
        var wsmMoveLegend;
        if(typeof wsmMoveLegend!='function'){
        wsmMoveLegend=function(parent){
        parent.find('.wsmTopChartBar').each(function(){
        legendDiv=jQuery(this).children('.wsmChartLegend');
        legendDiv.empty();
        parent.find('table.jqplot-table-legend').appendTo(legendDiv);
        legendDiv.children('table.jqplot-table-legend').removeAttr('style');
        });
        }
        }
        plot_{$atts['id']} = jQuery.jqplot('{$atts['id']}', {$atts['id']}_Data,{$atts['id']}_options);
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        var topButtons=jQuery('#{$atts['id']}').siblings('.wsmTopChartBar').find('.wsmButton');
        topButtons.on('click',function(e){
        e.preventDefault();
        jQuery(this).siblings().removeClass('active');
        chart=jQuery(this).data('chart');
        nLabels=legendLabels.slice();
        seriesData={$atts['id']}_Data.slice();
        arrSeries=seriesRenderee.slice();
        arrColors=colors.slice();
        newColors=['rgba(0,128,0,1)'];
        renderer=[{disableStack: true,yaxis:'y2axis'}];
        numberColumns=4;
        switch(chart){
        case 'Bounce':
        nLabels.push('".__('Bounce Rate(%)','wp-stats-manager')."');
        seriesData.push({$atts['id']}_bounceRate);
        break;
        case 'Ppv':
        nLabels.push('".__('Page Views Per Visit','wp-stats-manager')."');
        seriesData.push({$atts['id']}_ppv);
        break;
        case 'Nvis':
        nLabels.push('".__('% New Visitors','wp-stats-manager')."');
        seriesData.push({$atts['id']}_newVisitor);
        break;
        case 'Online':
        nLabels.push('".__('Average Online','wp-stats-manager')."');
        seriesData.push({$atts['id']}_avgOnline);
        break;
        case 'yesterday':
        var yesterDayPageViews=".json_encode($arrChartStats['yesterday']['pageViews']).";
        var yesterDayVisitors=".json_encode($arrChartStats['yesterday']['visitors']).";
        var yesterDayFirstVisitors=".json_encode($arrChartStats['yesterday']['firstTime']).";                                  renderer=[{disableStack: true,yaxis:'yaxis'},{disableStack: true,yaxis:'yaxis'},{disableStack: true,yaxis:'yaxis'}];
        newColors=".json_encode($arrLineColors).";
        nLabels=nLabels.concat(['{$tDate},".__('First Time Visitor','wp-stats-manager')."','{$tDate},".__('Visitors','wp-stats-manager')."','{$tDate},".__('Page Views','wp-stats-manager')."'])
        seriesData.push(yesterDayFirstVisitors);
        seriesData.push(yesterDayVisitors);
        seriesData.push(yesterDayPageViews);
        numberColumns=3;
        break;
        case '7daysback':
        var day7beforePageViews=".json_encode($arrChartStats['day7before']['pageViews']).";
        var day7beforeVisitors=".json_encode($arrChartStats['day7before']['visitors']).";
        var day7beforeFirstVisitors=".json_encode($arrChartStats['day7before']['firstTime']).";                                 renderer=[{disableStack: true,yaxis:'yaxis'},{disableStack: true,yaxis:'yaxis'},{disableStack: true,yaxis:'yaxis'}];
        newColors=".json_encode($arrLineColors).";
        nLabels=nLabels.concat(['{$p7Date},".__('First Time Visitor','wp-stats-manager')."','{$p7Date},".__('Visitors','wp-stats-manager')."','{$p7Date},".__('Page Views','wp-stats-manager')."'])
        seriesData.push(day7beforeFirstVisitors);
        seriesData.push(day7beforeVisitors);
        seriesData.push(day7beforePageViews);
        numberColumns=3;
        break;
        case '14daysback':
        var day14beforePageViews=".json_encode($arrChartStats['day14before']['pageViews']).";
        var day14beforeVisitors=".json_encode($arrChartStats['day14before']['visitors']).";
        var day14beforeFirstVisitors=".json_encode($arrChartStats['day14before']['firstTime']).";                              renderer=[{disableStack: true,yaxis:'yaxis'},{disableStack: true,yaxis:'yaxis'},{disableStack: true,yaxis:'yaxis'}];
        newColors=".json_encode($arrLineColors).";
        nLabels=nLabels.concat(['{$p14Date},".__('First Time Visitor','wp-stats-manager')."','{$p14Date},".__('Visitors','wp-stats-manager')."','{$p14Date},".__('Page Views','wp-stats-manager')."'])
        seriesData.push(day14beforeFirstVisitors);
        seriesData.push(day14beforeVisitors);
        seriesData.push(day14beforePageViews);
        numberColumns=3;

        break;
        default:
        break;
        }
        jQuery('#{$atts['id']}').empty();
        {$atts['id']}_options.legend.labels=nLabels;
        arrSeries=arrSeries.concat(renderer);
        arrColors=arrColors.concat(newColors);
        {$atts['id']}_options.series=arrSeries;
        {$atts['id']}_options.seriesColors=arrColors;
        {$atts['id']}_options.legend.rendererOptions.numberColumns=numberColumns;
        jQuery(this).addClass('active');
        plot_{$atts['id']} = jQuery.jqplot('{$atts['id']}', seriesData,{$atts['id']}_options);
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        });
        
        jQuery(window).on('resize',function(){
        plot_{$atts['id']}.replot({ resetAxes: true });
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        });
        jQuery('#{$atts['id']}').parent().find('.".WSM_PREFIX."ChartLegend').on('click','table.jqplot-table-legend tr td', function(event, mode){
            if(mode!='code'){
               var tI = {$atts['id']}_legendIndex.indexOf(jQuery(this).index());
                if(tI==-1){
                    {$atts['id']}_legendIndex.push(jQuery(this).index());
                }else{ 
                    {$atts['id']}_legendIndex.splice(tI, 1);
                }
            }
        });
        ";
        return $html;
    }    
    function wsm_showLastDaysStatsChart($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $nDays=get_option(WSM_PREFIX.'ChartDays');
        $nDays=($nDays!='' && $nDays>0) ?$nDays:30;
        $atts = shortcode_atts( array(
            'days' =>$nDays,
            'title' => __('Get Current Stats','wp-stats-manager'),
            'id'=>'lastDaysChart',
            'width' =>'1140px',
            'height' =>'400px'
            ), $atts, WSM_PREFIX.'_showLastDaysStatsChart');
        $html='<div class="chartContainer">';
        $html.=$this->wsm_getTopChartBar('lastdaychart');
        $html.='<div id="'.$atts['id'].'"></div></div>';
        $tDays=2;
        $atts['days']=intval($atts['days']);
        if($atts['days']>35){
            $tDays=3;
        }
        $arrLineData=$this->objDatabase->fnGetHistoricalDayStatsByDays($atts['days']);
        $yArray=array('visitors'=>array(),'pageViews'=>array(),'firstTimeVisitors'=>array());
        $todaysVisitors=array_slice($arrLineData['visitors'], -8,7);
        
        $todaysPageviews=array_slice($arrLineData['pageViews'], -8,7);
        $todaysFirstVisitors=array_slice($arrLineData['firstTimeVisitors'], -8,7);
        foreach($todaysVisitors as $key=>$day){
            array_push($yArray['visitors'],$day[1]);
            array_push($yArray['pageViews'],$todaysPageviews[$key][1]);
            array_push($yArray['firstTimeVisitors'],$todaysFirstVisitors[$key][1]);
        }     
        $toDayForeCast['visitors']=round(wsmFnCalculateForeCastData(array_keys($yArray['visitors']),$yArray['visitors'],7),0);
        $toDayForeCast['pageViews']=round(wsmFnCalculateForeCastData(array_keys($yArray['pageViews']),$yArray['pageViews'],7),0);
        $toDayForeCast['firstTimeVisitors']=round(wsmFnCalculateForeCastData(array_keys($yArray['firstTimeVisitors']),$yArray['firstTimeVisitors'],7),0);
        $colors=array('rgba(244,81,81,1)','rgba(251,194,70,1)','rgba(87,135,184,1)','rgba(0,128,0,1)');
        $wsmAdminJavaScript.="
        //arrLiveStats.push('".WSM_PREFIX."CurrentDayStats_".$atts['id']."_".wsmGetYesterdayDateByTimeZone('Yxmxd')."');
        //jQuery('#".WSM_PREFIX."_lastDaysChart h2.hndle').html('<span>".sprintf(__('Last %d Days','wp-stats-manager'),$atts['days'])."</span>');
        var {$atts['id']}_bpageViews=".json_encode($arrLineData['pageViews']).";
        var {$atts['id']}_bvisitors=".json_encode($arrLineData['visitors']).";
        var {$atts['id']}_bfirstVisitors=".json_encode($arrLineData['firstTimeVisitors']).";
        var {$atts['id']}_bBounce = ".json_encode($arrLineData['Bounce']).";
        var {$atts['id']}_bppv = ".json_encode($arrLineData['ppv']).";
        var {$atts['id']}_bnewVisitor = ".json_encode($arrLineData['newVisitor']).";
        var {$atts['id']}_bavgOnline = ".json_encode($arrLineData['avgOnline']).";
        var {$atts['id']}_legendIndex=[];
        var bcolors=".json_encode($colors).";
        var keyLabels=['".__('First Time Visitor','wp-stats-manager')."','".__('Visitors','wp-stats-manager')."','".__('Page Views','wp-stats-manager')."'];
        var {$atts['id']}_arrLineData=[{$atts['id']}_bfirstVisitors,{$atts['id']}_bvisitors,{$atts['id']}_bpageViews];
        var seriesRenderrer=[{yaxis:'yaxis'},{yaxis:'yaxis'},{yaxis:'yaxis'}, {yaxis:'y2axis'}];
        var {$atts['id']}_bOptions={
        // Tell the plot to stack the bars.
        series:seriesRenderrer,
        seriesColors :bcolors,
        height:'{$atts['height']}',
        gridPadding:{
        right:40,
        left:40,
        top:20,
        bottom:60
        },
        captureRightClick: true,
        seriesDefaults: {
        rendererOptions: {
        smooth: true
        }
        },
        axes:{
        xaxis:{
        renderer:jQuery.jqplot.DateAxisRenderer,
        tickRenderer:jQuery.jqplot.CanvasAxisTickRenderer,
        tickOptions:{
        formatString:'%a %e %b',
        fontSize:'10px'
        },
        numberTicks: ".count($arrLineData['pageViews']).",
        tickInterval:'{$tDays} days'
        },
        yaxis:{
        min:0,
        tickOptions: {
            formatString: '%d'
        }
        },
        y2axis: {
        padMin: 0,
        min:0,
        autoscale:true,
        tickOptions: {
        showGridline: false,
        }
        }
        },
        legend: {
        labels :keyLabels,
        show: true,
        location: 'nw',
        placement: 'inside',
        renderer: jQuery.jqplot.EnhancedLegendRenderer,
        rendererOptions: {
        numberRows: 1,
        seriesToggle:true
        }
        },
        highlighter: {
        tooltipLocation: 'ne',
        useAxesFormatters: true,
        sizeAdjust: 2.5,
        formatString:'%s, %P',
        tooltipContentEditor : function(str, seriesIndex, pointIndex, plot){
        return '<span style=\'background:'+bcolors[seriesIndex]+'\'></span>'+plot.legend.labels[seriesIndex]+ \": \" + plot.data[seriesIndex][pointIndex][1];
        }
        },
        cursor :{
        show : false,
        followMouse : false,
        useAxesFormatters:false
        }
        };
        plot_{$atts['id']} = jQuery.jqplot('{$atts['id']}', {$atts['id']}_arrLineData,{$atts['id']}_bOptions );
        if(typeof wsmMoveLegend!='function'){
        wsmMoveLegend=function(parent){
        parent.find('.wsmTopChartBar').each(function(){
        legendDiv=jQuery(this).children('.wsmChartLegend');
        legendDiv.empty();
        parent.find('table.jqplot-table-legend').appendTo(legendDiv);
        legendDiv.children('table.jqplot-table-legend').removeAttr('style');
        });
        }
        }
        var topButtons=jQuery('#{$atts['id']}').siblings('.wsmTopChartBar').find('.wsmButton');
        topButtons.on('click',function(e){
        e.preventDefault();
        jQuery(this).siblings().removeClass('active');
        bchart=jQuery(this).data('chart');
        nBLabels=keyLabels.slice();
        bSeriesData={$atts['id']}_arrLineData.slice();
        switch(bchart){
        case 'Bounce':
        nBLabels.push('".__('Bounce Rate(%)','wp-stats-manager')."');
        bSeriesData.push({$atts['id']}_bBounce);
        break;
        case 'Ppv':
        nBLabels.push('".__('Page Views Per Visit','wp-stats-manager')."');
        bSeriesData.push({$atts['id']}_bppv);
        break;
        case 'Nvis':
        nBLabels.push('".__('% New Visitors','wp-stats-manager')."');
        bSeriesData.push({$atts['id']}_bnewVisitor);
        break;
        case 'Online':
        nBLabels.push('".__('Average Online','wp-stats-manager')."');
        bSeriesData.push({$atts['id']}_bavgOnline);
        break;
        default:
        break;
        }
        jQuery('#{$atts['id']}').empty();
        {$atts['id']}_bOptions.legend.labels=nBLabels;
        jQuery(this).addClass('active');
        //console.log(bSeriesData);
        plot_{$atts['id']} = jQuery.jqplot('{$atts['id']}', bSeriesData,{$atts['id']}_bOptions);
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        });
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        jQuery(window).on('resize',function(){
        plot_{$atts['id']}.replot();
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        });
        jQuery('#{$atts['id']}').parent().find('.".WSM_PREFIX."ChartLegend').on('click','table.jqplot-table-legend tr td', function(event, mode){
            if(mode!='code'){
               var tI = {$atts['id']}_legendIndex.indexOf(jQuery(this).index());
                if(tI==-1){
                    {$atts['id']}_legendIndex.push(jQuery(this).index());
                }else{ 
                    {$atts['id']}_legendIndex.splice(tI, 1);
                }
            }
        });
        
        ";
        return $html;
    }
    function wsm_showRecentVisitedPages($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Get Current Stats','wp-stats-manager'),
            'id'=>'recentVisitedPages'
            ), $atts, WSM_PREFIX.'_showRecentVisitedPages');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetRecentVisitedPages($atts['limit']);
        $html.='<table class="wsmTableStriped">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){
                $page['city']=isset($page['city'])&&$page['city']!=""?$page['city']:"-";
                $page['osystem']=strtolower($page['osystem']);       
                $page['title']=$page['title']!=''?$page['title']:$page['url'];          
                if((strpos($page['osystem'], 'windows') !== false || $page['osystem']=='') && strtolower($page['deviceType'])=='desktop' ) {
                    $page['osystem']='windows';
                }
                if($page['browser']=='' && strtolower($page['deviceType'])=='desktop' ){
                    $page['browser']='Google Chrome';
                }
                if($page['browser']=='' && (strtolower($page['osystem'])=='ios' || strtolower($page['osystem'])=='mac')){
                    $page['browser']='Safari';
                }
				$location = $page['country'];
				if( isset($page['city']) && $page['city'] != '-' ){
					$location = $page['city']. ', '.$page['country'];
				}
                $html.='<tr><td class="wsmTimeDiff">'.wsmConvertTimeDifference($page['timeDiff'],'mm:ss').'</td><td class="wsmPageTitle"><a href="'.$page['url'].'" title="'.$page['title'].'">'.$page['title'].'</a></td><td class="wsmCityCountry"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="flag flag-'.strtolower($page['alpha2Code']).'" alt="'.$page['country'].'"  title="'.$page['country'].'"/>&nbsp;'.$location.'</td><td class="wsmIconSet wsmBrowerOS"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['browser'])).'" alt="'.$page['browser'].'" title="'.$page['browser'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.str_replace(" ","",$page['osystem']).'" alt="'.$page['osystem'].'" title="'.$page['osystem'].'"/></td></tr>';
            }            
        }else{
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        $html.='</table>';

        $html.='</div>';
        $wsmAdminJavaScript.='
        var '.$atts['id'].'Container=jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'").parent();
        '.$atts['id'].'Container.on({mouseenter:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("href"));},mouseleave:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("title"));}},".wsmPageTitle");
        arrLiveStats.push("'.WSM_PREFIX.'_'.$atts['id'].'");
        var wsmCounterTimer=function(){
            jQuery("#'.WSM_PREFIX.'_'.$atts['id'].' .wsmTableStriped .wsmTimeDiff").each(function(){
                var elm=jQuery(this);
                var cTime=elm.text().split(":");
                var totalSec=parseInt(cTime[1])+(parseInt(cTime[0])*60);
                totalSec=totalSec+1;
                var minutes = Math.floor(totalSec / 60);
                var seconds = totalSec % 60;
                seconds=seconds.toString().length==1?"0"+seconds:seconds
                elm.text(minutes+":"+ seconds);
            });
        };
        setInterval(wsmCounterTimer, 1000);        
        ';
        return $html;
    }
    function wsm_showRecentVisitedPagesDetails($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Get Current Stats','wp-stats-manager'),
            'id'=>'recentVisitedPagesdetailsList'
            ), $atts, WSM_PREFIX.'_showRecentVisitedPages');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetRecentVisitedPages($atts['limit']);
        $html.='<table class="wsmSpanTable">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){
                $page['city']=isset($page['city'])&&$page['city']!=""?$page['city']:"-";
                $page['osystem']=strtolower($page['osystem']);       
                $page['title']=$page['title']!=''?$page['title']:$page['url'];          
                if((strpos($page['osystem'], 'windows') !== false || $page['osystem']=='') && strtolower($page['deviceType'])=='desktop' ) {
                    $page['osystem']='windows';
                }
                if($page['browser']=='' && strtolower($page['deviceType'])=='desktop' ){
                    $page['browser']='Google Chrome';
                }
                if($page['browser']=='' && (strtolower($page['osystem'])=='ios' || strtolower($page['osystem'])=='mac')){
                    $page['browser']='Safari';
                }
                $isRef=!is_null($page['refUrl']) && $page['refUrl']!=='';
                $rowSpan=$isRef?3:2;
                $html.='<tr><td class="wsmTimeDiff" rowspan="'.$rowSpan.'">'.wsmConvertTimeDifference($page['timeDiff'],'mm:ss').'</td><td><strong>'.wsmMaskIPaddress($page['ipAddress']).'</strong></td><td class="wsmHits">'.__('Hits','wp-stats-manager').':&nbsp;<strong>'.$page['hits'].'</strong></td><td class="wsmCityCountry"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="flag flag-'.strtolower($page['alpha2Code']).'" alt="'.$page['country'].'"  title="'.$page['country'].'"/>&nbsp;'.$page['city'].'</td><td class="wsmIconSet wsmBrowerOS"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['browser'])).'" alt="'.$page['browser'].'" title="'.$page['browser'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.str_replace(" ","",$page['osystem']).'" alt="'.$page['osystem'].'" title="'.$page['osystem'].'"/></td><td class="wsmResolution">'.$page['resolution'].'</td></tr>
                <tr><td class="wsmPageTitle" colspan="5"><a href="'.$page['url'].'" title="'.$page['title'].'">'.$page['title'].'</a></td></tr>';
                if($isRef){
                    $html.='<tr><td colspan="5"><strong>Ref:&nbsp;</strong><a href="'.$page['refUrl'].'" >'.$page['refUrl'].'</a></td></tr>';
                }
            }            
        }else{
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        $html.='</table>';

        $html.='</div>';
        $wsmAdminJavaScript.=' arrLiveStats.push("'.WSM_PREFIX.'_'.$atts['id'].'");
        var '.$atts['id'].'Container=jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'").parent();
        '.$atts['id'].'Container.on({mouseenter:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("href"));},mouseleave:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("title"));}},".wsmPageTitle");
        var wsmCounterTimer=function(){
            jQuery("#'.WSM_PREFIX.'_'.$atts['id'].' .wsmSpanTable .wsmTimeDiff").each(function(){
                var elm=jQuery(this);
                var cTime=elm.text().split(":");
                var totalSec=parseInt(cTime[1])+(parseInt(cTime[0])*60);
                totalSec=totalSec+1;
                var minutes = Math.floor(totalSec / 60);
                var seconds = totalSec % 60;
                seconds=seconds.toString().length==1?"0"+seconds:seconds
                elm.text(minutes+":"+ seconds);
            });
        };
        setInterval(wsmCounterTimer, 1000); 
        ';
        return $html;
    }
    function wsm_showPopularPages($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Get Popular Pages','wp-stats-manager'),
            'id'=>'popularPagesList'
            ), $atts, WSM_PREFIX.'_showPopularPages');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetPopularPages($atts['limit']);
        $html.='<table class="wsmTableStriped">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){
                $page['title']=$page['title']!=''?$page['title']:$page['fullURL'];
                $html.='<tr><td class="wsmTimeDiff">'.$page['stotalViews'].'</td><td class="wsmPageTitle"><a href="'.$page['fullURL'].'" title="'.$page['title'].'">'.$page['title'].'</a></td></tr>';
            }             
        }else{
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        $html.='</table>';

        $html.='</div>';
        $wsmAdminJavaScript.='
        var '.$atts['id'].'Container=jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'").parent();
        '.$atts['id'].'Container.on({mouseenter:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("href"));},mouseleave:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("title"));}},".wsmPageTitle");
        arrLiveStats.push("'.WSM_PREFIX.'_'.$atts['id'].'");';
        
        return $html;
    }
    function wsm_showPopularReferrers($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Get Popular Referrers','wp-stats-manager'),
            'id'=>'popularReferrersList'
            ), $atts, WSM_PREFIX.'_showPopularReferrers');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetPopularReferrers($atts['limit']);
        $flagRef=true;
        $html.='<table class="wsmTableStriped">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){
                if(wsmFnIsCrossDomain($page['fullURL'])){
                    $flagRef=false;
                    $page['title']=$page['title']!=''?$page['title']:$page['fullURL'];
                    $html.='<tr><td class="wsmTimeDiff">'.$page['totalReferrers'].'</td><td class="wsmPageTitle"><a href="'.$page['fullURL'].'" title="'.$page['title'].'">'.$page['title'].'</a></td></tr>';
                }
            }             
        }
        if($flagRef){
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        
        $html.='</table>';

        $html.='</div>';
        $wsmAdminJavaScript.='
        var '.$atts['id'].'Container=jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'").parent();
        '.$atts['id'].'Container.on({mouseenter:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("href"));},mouseleave:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("title"));}},".wsmPageTitle");
        arrLiveStats.push("'.WSM_PREFIX.'_'.$atts['id'].'");';
        return $html;
    }
    function wsm_showMostActiveVisitors($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Get Most Active Visitors','wp-stats-manager'),
            'id'=>'mostActiveVisitorsList'
            ), $atts, WSM_PREFIX.'_showMostActiveVisitors');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetMostActiveVisitors($atts['limit']);

        $arrVisitIds=wp_list_pluck($arrPages,'visitId');
        $arrExist=array();
        $arrCountIds=array_count_values($arrVisitIds);
        $html.='<table class="wsmTableStriped">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){
                if(!in_array($page['visitId'], $arrExist)){
                    $page['title']=$page['title']!=''?$page['title']:$page['url'];                
                    $page['osystem']=strtolower($page['osystem']);
                    if (strpos($page['osystem'], 'windows') !== false || $page['osystem']=='') {
                        $page['osystem']='windows';
                    }
                    if($page['browser']=='' && strtolower($page['deviceType'])=='desktop' ){
                        $page['browser']='Google Chrome';
                    }
                    if($page['browser']=='' && (strtolower($page['osystem'])=='ios' || strtolower($page['osystem'])=='mac')){
                        $page['browser']='Safari';
                    }
                    $html.='<tr><td class="wsmTimeDiff">'.$arrCountIds[$page['visitId']].'</td><td><b>'.wsmMaskIPaddress($page['ipAddress']).'</b><div class="wsmPageTitle"><a href="'.$page['url'].'" title="'.$page['title'].'">'.$page['title'].'</a></div></td><td class="wsmIconSet"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="flag flag-'.strtolower($page['alpha2Code']).'" alt="'.$page['country'].'" title="'.$page['country'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['deviceType'])).'" alt="'.$page['deviceType'].'" title="'.$page['deviceType'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['browser'])).'" alt="'.$page['browser'].'" title="'.$page['browser'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['osystem'])).'" alt="'.$page['osystem'].'" title="'.$page['osystem'].'"/></td></tr>';
                    array_push($arrExist, $page['visitId']);
                }
            }

        }else{
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        $html.='</table>';
        $html.='</div>';
        $wsmAdminJavaScript.=' var '.$atts['id'].'Container=jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'").parent();
        '.$atts['id'].'Container.on({mouseenter:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("href"));},mouseleave:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("title"));}},".wsmPageTitle");
        arrLiveStats.push("'.WSM_PREFIX.'_'.$atts['id'].'");';
        return $html;
    }
    function wsm_showMostActiveVisitorsDetails($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Get Most Active Visitors','wp-stats-manager'),
            'id'=>'mostActiveVisitorsDetailsList'
            ), $atts, WSM_PREFIX.'_showMostActiveVisitors');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetMostActiveVisitors();
        $arrVisitIds=wp_list_pluck($arrPages,'visitId');
        $arrExist=array();
        $arrCountIds=array_count_values($arrVisitIds);
        $html.='<table class="wsmSpanTable">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){
                if(!in_array($page['visitId'], $arrExist)){
                    $page['title']=$page['title']!=''?$page['title']:$page['url'];                
                    $page['osystem']=strtolower($page['osystem']);
                    if (strpos($page['osystem'], 'windows') !== false || $page['osystem']=='') {
                        $page['osystem']='windows';
                    }
                    if($page['browser']=='' && strtolower($page['deviceType'])=='desktop' ){
                        $page['browser']='Google Chrome';
                    }
                    if($page['browser']=='' && (strtolower($page['osystem'])=='ios' || strtolower($page['osystem'])=='mac')){
                        $page['browser']='Safari';
                    }
                    $isRef=!is_null($page['refUrl']) && $page['refUrl']!=='';
                    $rowSpan=$isRef?3:2;
                    $html.='<tr><td class="wsmTimeDiff" rowspan="'.$rowSpan.'">'.$page['hits'].'</td><td><strong>'.wsmMaskIPaddress($page['ipAddress']).'</strong></td><td class="wsmHits">'.__('Last Hit','wp-stats-manager').':&nbsp;<strong>'.wsmConvertTimeDifference($page['timeDiff'],'mm:ss').'</strong></td><td class="wsmCityCountry"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="flag flag-'.strtolower($page['alpha2Code']).'" alt="'.$page['country'].'"  title="'.$page['country'].'"/>&nbsp;'.$page['city'].'</td><td class="wsmIconSet wsmBrowerOS"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['browser'])).'" alt="'.$page['browser'].'" title="'.$page['browser'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.str_replace(" ","",$page['osystem']).'" alt="'.$page['osystem'].'" title="'.$page['osystem'].'"/></td><td class="wsmResolution">'.$page['resolution'].'</td></tr>
                <tr><td class="wsmPageTitle" colspan="5"><a href="'.$page['url'].'" title="'.$page['title'].'">'.$page['title'].'</a></td></tr>';
                    if($isRef){
                        $html.='<tr><td colspan="5"><strong>Ref:&nbsp;</strong><a href="'.$page['refUrl'].'" >'.$page['refUrl'].'</a></td></tr>';
                    }
                    array_push($arrExist, $page['visitId']);
                }
            }

        }else{
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        $html.='</table>';
        $html.='</div>';
        $wsmAdminJavaScript.=' arrLiveStats.push("'.WSM_PREFIX.'_'.$atts['id'].'");
         var '.$atts['id'].'Container=jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'").parent();
        '.$atts['id'].'Container.on({mouseenter:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("href"));},mouseleave:function(){jQuery(this).children("a").text(jQuery(this).children("a").attr("title"));}},".wsmPageTitle");
        var wsmCounterTimer=function(){
            jQuery("#'.WSM_PREFIX.'_'.$atts['id'].' .wsmSpanTable .wsmHits").each(function(){
                var elm=jQuery(this).children("strong");
                var cTime=elm.text().split(":");
                var totalSec=parseInt(cTime[1])+(parseInt(cTime[0])*60);
                totalSec=totalSec+1;
                var minutes = Math.floor(totalSec / 60);
                var seconds = totalSec % 60;
                seconds=seconds.toString().length==1?"0"+seconds:seconds
                elm.text(minutes+":"+ seconds);
            });
        };
        setInterval(wsmCounterTimer, 1000);         
        ';
        return $html;
    }
    function wsm_showMostActiveVisitorsGeo($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Geo Location','wp-stats-manager'),
            'height' => '300px',
            'zoom' => '1',
            'call' => 'php',
            'id'=>'mostActiveVisitorsGeoLocation'
            ), $atts, WSM_PREFIX.'_showMostActiveVisitorsGeo');
        
        $arrPages=$this->objDatabase->fnGetMostActiveVisitors();        
        $arrVisitIds=wp_list_pluck($arrPages,'visitId');
        $arrExist=array();
        $arrCountIds=array_count_values($arrVisitIds);
        $ipAddress=wsmFnGetIPAddress();
        $objLocation=wsmFnGetLocationInfo($ipAddress);
        $googleMapAPI = get_option(WSM_PREFIX.'GoogleMapAPI');
        if(is_null($googleMapAPI) || $googleMapAPI==''){
            echo '<br><br>&nbsp;<i>' . __('Please enter map API key (check the settings page for more details)','wp-stats-manager').'</i><br><br>';
        }else{
        $subJs='';
        $arrJSMarkers=array();
        if(is_array($arrPages) && count($arrPages)>0){            
            foreach($arrPages as $page){
                if(!in_array($page['visitId'], $arrExist)){
                    array_push($arrJSMarkers,array('latitude'=>$page['latitude'],'longitude'=>$page['longitude'],'ipAdress'=>wsmMaskIPaddress($page['ipAddress']),'views'=>$arrCountIds[$page['visitId']]));
                    array_push($arrExist, $page['visitId']);
                }
            }            
        }
       
        if($atts['call']=='ajax'){
            return json_encode($arrJSMarkers);
        }
        $html='<style>#'.WSM_PREFIX.'_'.$atts['id'].'{height:'.$atts['height'].';}</style><div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmMapContainer" ></div>';
        $wsmAdminJavaScript.="arrLiveStats.push('".WSM_PREFIX.'_'.$atts['id']."'); var ".WSM_PREFIX."_locations=[]; var ".WSM_PREFIX."_lDetails=[];";
        if(is_array($arrJSMarkers) && count($arrJSMarkers)>0){               
            $wsmAdminJavaScript.="var arrPages=".json_encode($arrJSMarkers).";
            ";
        $wsmAdminJavaScript.='Array.prototype.forEach.call(arrPages, function(pages){                      
            var point={};
            var pointDetails={};
            pointDetails.ipAddress=pages.ipAdress;
            pointDetails.views=pages.views;
            point.lat=parseFloat(pages.latitude);                 
            point.lng=parseFloat(pages.longitude);                               
            '.WSM_PREFIX.'_locations.push(point);                          
            '.WSM_PREFIX.'_lDetails.push(pointDetails);                          
            }); ';             
        } 
        $wsmAdminJavaScript.='
        function wsmLoadScript(src,callback){
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.setAttribute("aynsc","");
        script.setAttribute("defer","");
        if(callback)script.onload=callback;
        document.body.appendChild(script);
        script.src = src;
        script.onLoad=wsmInitMap;
        }          
        ';
        $wsmAdminJavaScript.='
        var '.WSM_PREFIX.'ZoomLevel= '.$atts['zoom'].';                         
        var '.WSM_PREFIX.'centerObj= {lat: parseFloat('.$objLocation->geoplugin_latitude.'), lng: parseFloat('.$objLocation->geoplugin_longitude.')};                         
        window.wsmInitMap=function() {
        var infoWindow = new google.maps.InfoWindow;                
        var map_'.WSM_PREFIX.'_'.$atts['id'].' = new google.maps.Map(document.getElementById("'.WSM_PREFIX.'_'.$atts['id'].'"), {
        center: '.WSM_PREFIX.'centerObj ,
        zoom: '.WSM_PREFIX.'ZoomLevel,
        mapTypeId: \'satellite\',
        scrollwheel: false,
        mapTypeControl: false,
        navigationControl: false,
        scaleControl: false
        });
        google.maps.event.addListener(map_'.WSM_PREFIX.'_'.$atts['id'].', \'zoom_changed\',function() {
            '.WSM_PREFIX.'ZoomLevel=map_'.WSM_PREFIX.'_'.$atts['id'].'.getZoom();            
        });
        google.maps.event.addListener(map_'.WSM_PREFIX.'_'.$atts['id'].', \'center_changed\',function() {            
            '.WSM_PREFIX.'centerObj=map_'.WSM_PREFIX.'_'.$atts['id'].'.getCenter();
        });
        google.maps.event.addListener(map_'.WSM_PREFIX.'_'.$atts['id'].', \'drag\',function() {            
            '.WSM_PREFIX.'centerObj=map_'.WSM_PREFIX.'_'.$atts['id'].'.getCenter();
        });
        ';         
        $subJs.="        
        var circle ={
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: 'red',
            fillOpacity: 1,
            scale: 4.5,
            strokeColor: 'black',
            strokeWeight: 1
        };        
        var markers = ".WSM_PREFIX."_locations.map(function(location, i) {
        var tMarker= new google.maps.Marker({
        map:map_".WSM_PREFIX.'_'.$atts['id'].",
        position: location,
        label:'',
        icon:circle
        });
        var infowincontent = document.createElement('div');
        var strong = document.createElement('strong');
        strong.textContent = ".WSM_PREFIX."_lDetails[i].ipAddress;
        infowincontent.appendChild(strong);
        infowincontent.appendChild(document.createElement('br'));

        var text = document.createElement('text');
        text.textContent = 'Views:'+".WSM_PREFIX."_lDetails[i].views;
        infowincontent.appendChild(text);
        tMarker.addListener('click', function() {
        infoWindow.setContent(infowincontent);
        infoWindow.open(map_".WSM_PREFIX.'_'.$atts['id'].", tMarker);
        });
        return tMarker;
        });
        
        ";     

        $wsmAdminJavaScript.=$subJs.'}; 

        if (typeof wsmInitMap === "function") {
        wsmLoadScript("https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js");
        wsmLoadScript("https://maps.googleapis.com/maps/api/js?key='.$googleMapAPI.'&callback=wsmInitMap",
        function(){console.log(\'google-loader has been loaded, but not the maps-API \');});}';           
        return $html;
		}
    }
    function wsm_showActiveVisitorsByCountry($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Country','wp-stats-manager'),
            'id'=>'visitorsCountListByCountry'
            ), $atts, WSM_PREFIX.'_showActiveVisitorsByCountry');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetActiveVisitorsCount('country');        
        $html.='<table class="wsmTableStriped">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){                   
                    $html.='<tr><td class="wsmTimeDiff">'.$page['visitors'].'</td><td class="wsmIconSet"><img src="'.WSM_URL.'images/ICO_1px.gif" class="flag flag-'.strtolower($page['alpha2Code']).'" alt="'.$page['country'].'" title="'.$page['country'].'"/>&nbsp;'.$page['country'].'</td></tr>';                                    
            }

        }else{
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        $html.='</table>';
        $html.='</div>';   
        $wsmAdminJavaScript.="arrLiveStats.push('".WSM_PREFIX.'_'.$atts['id']."');";     
        return $html;
    }
    function wsm_showActiveVisitorsByCity($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('City','wp-stats-manager'),
            'id'=>'visitorsCountListByCity'
            ), $atts, WSM_PREFIX.'_showActiveVisitorsByCity');
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmTableContainer">';
        $arrPages=$this->objDatabase->fnGetActiveVisitorsCount('city');        
        $html.='<table class="wsmTableStriped">';
        if(is_array($arrPages) && count($arrPages)>0){             
            foreach($arrPages as $page){                
                    $page['city']=isset($page['city']) && $page['city']!=''?$page['city']:'-';                  
                    $html.='<tr><td class="wsmTimeDiff">'.$page['visitors'].'</td><td class="wsmIconSet"><img src="'.WSM_URL.'images/ICO_1px.gif" class="flag flag-'.strtolower($page['alpha2Code']).'" alt="'.$page['country'].'" title="'.$page['country'].'"/>&nbsp;'.$page['country'].',&nbsp;'.$page['city'].'</td></tr>';                    
            }

        }else{
            $html.='<tr><td class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</td></tr>'; 
        }
        $html.='</table>';
        $html.='</div>';    
        $wsmAdminJavaScript.="arrLiveStats.push('".WSM_PREFIX.'_'.$atts['id']."');";   
        return $html;
    }
    function wsm_showStatFilterBox($atts, $content=""){
        global $wsmAdminJavaScript;
        $atts = shortcode_atts(array(
            'limit' =>10,
            'title' => __('Filter','wp-stats-manager'),
            'hide' => '',
            'source' => '',
            'id'=>'filterStatBox'
        ), $atts, WSM_PREFIX.'_showStatFilterBox');
        $arrHide=array();
        $arrTabs=array('Hourly','Daily','Monthly');
        if($atts['hide']!=''){
            $arrHide=explode(';',$atts['hide']);
        }
        $arrPostData=wsmFnGetFilterPostData();
        if(in_array($arrPostData[WSM_PREFIX."FilterType"],$arrHide)){
            $tabs = array_values(array_diff($arrTabs, $arrHide));            
            $arrPostData[WSM_PREFIX."FilterType"]=$tabs[0];
        }   
        $titleHourly=__('Hourly','wp-stats-manager');  
        $titleDaily=__('Daily','wp-stats-manager'); 
        $titleMonthly=__('Monthly','wp-stats-manager');  
        if($atts['source']=='Referral'){
             $titleHourly=__('Daily','wp-stats-manager');  
             $titleDaily=__('Monthly','wp-stats-manager'); 
             $titleMonthly=__('Yearly','wp-stats-manager'); 
        }
        $firstVisitDate=$this->objDatabase->fnGetFirstVisitDate();       
        $installDays=$this->objDatabase->fnGetNDayFromFirstVisitActionTime(true);
        $html='<div id="'.WSM_PREFIX.'_'.$atts['id'].'" class="wsmFilterWrapper">';
            //$html.='<form name="'.WSM_PREFIX.'FrmFilterOptions" id="'.WSM_PREFIX.'FrmFilterOptions" method="post">';
                $html.='<div class="wsmRadioTabWrapper">';
                    if(!in_array('Hourly',$arrHide)){
                        $checked=$arrPostData[WSM_PREFIX."FilterType"]=='Hourly'?'Checked="checked"':'';
                        $html.='<input type="radio" class="wsmRadioTab" name="'.WSM_PREFIX.'FilterType" value="Hourly" id="'.WSM_PREFIX.'FilterHourly" '.$checked.' /><label for="'.WSM_PREFIX.'FilterHourly">'.$titleHourly.'</label>';
                    }
                    if(!in_array('Daily',$arrHide)){
                        $checked=$arrPostData[WSM_PREFIX."FilterType"]=='Daily'?'Checked="checked"':'';
                        $html.='<input type="radio" class="wsmRadioTab" name="'.WSM_PREFIX.'FilterType" value="Daily" id="'.WSM_PREFIX.'FilterDaily" '.$checked.' /><label for="'.WSM_PREFIX.'FilterDaily">'.$titleDaily.'</label>';
                    }
                    if(!in_array('Monthly',$arrHide)){
                        $checked=$arrPostData[WSM_PREFIX."FilterType"]=='Monthly'?'Checked="checked"':'';
                        $html.='<input type="radio" class="wsmRadioTab" name="'.WSM_PREFIX.'FilterType" value="Monthly" id="'.WSM_PREFIX.'FilterMonthly" '.$checked.' /><label for="'.WSM_PREFIX.'FilterMonthly">'.$titleMonthly.'</label>';                     
                    }
                $html.='</div>';
                $html.='<div class="wsmMiddleWrapper">';
                    if(!in_array('Hourly',$arrHide)){
                        $html.='<div class="wsmHourly">';
                            $html.='<div class="wsmNormal wsmHide">';                        
                                $html.='<div id="wsmShowCalendarNormal" class="wsmInlineBlock"></div><input type="hidden" Placeholder="yyyy-mm-dd" class="wsmTextBox" name="'.WSM_PREFIX.'TxtHourlyNormalDate" id="'.WSM_PREFIX.'TxtHourlyNormalDate" value="'.$arrPostData[WSM_PREFIX.'TxtHourlyNormalDate'].'"/>';          
                            $html.='</div>'; 
                            $html.='<div class="wsmRange wsmHide">';            
                                $html.='<table class="wsmWidth100p"><tr><td><div>'.__('From','wp-stats-manager').':&nbsp;</div><div id="wsmShowCalendarFrom"></div><input type="hidden" Placeholder="yyyy-mm-dd" name="'.WSM_PREFIX.'TxtHourlyRangeFromDate" id="'.WSM_PREFIX.'TxtHourlyRangeFromDate" value="'. $arrPostData[WSM_PREFIX.'TxtHourlyRangeFromDate'].'"/></td><td><div>'.__('To','wp-stats-manager').':&nbsp;</div><div id="wsmShowCalendarTo"></div><input type="hidden" Placeholder="yyyy-mm-dd" name="'.WSM_PREFIX.'TxtHourlyRangeToDate" id="'.WSM_PREFIX.'TxtHourlyRangeToDate" value="'. $arrPostData[WSM_PREFIX.'TxtHourlyRangeToDate'].'"/></td></tr></table>';
                            $html.='</div>';  
                            $html.='<div class="wsmCompare wsmHide">'; 
                                $html.='<table class="wsmWidth100p"><tr><td><div>'.__('First','wp-stats-manager').':&nbsp;</div><div id="wsmShowCalendarFirst"></div><input type="hidden" Placeholder="yyyy-mm-dd" class="wsmTextBox" name="'.WSM_PREFIX.'TxtHourlyCompareFirstDate" id="'.WSM_PREFIX.'TxtHourlyCompareFirstDate" value="'. $arrPostData[WSM_PREFIX.'TxtHourlyCompareFirstDate'].'"/></td><td><div>'.__('Second','wp-stats-manager').':&nbsp;</div><div id="wsmShowCalendarSecond"></div><input type="hidden" class="wsmTextBox" Placeholder="yyyy-mm-dd" name="'.WSM_PREFIX.'TxtHourlyCompareSecondDate" id="'.WSM_PREFIX.'TxtHourlyCompareSecondDate" value="'. $arrPostData[WSM_PREFIX.'TxtHourlyCompareSecondDate'].'"/></td></tr></table>';           
                            $html.='</div>';  
                        $html.='</div>'; 
                    }
                    if(!in_array('Daily',$arrHide)){
                        $html.='<div class="wsmDaily">';
                            $html.='<div class="wsmNormal wsmHide">';
                                $html.='<table>'; 
                                    $html.='<tr><td><label for="'.WSM_PREFIX.'SelectYear">'.__('Select Year','wp-stats-manager').':&nbsp;</label></td><td><label for="'.WSM_PREFIX.'SelectMonth">'.__('Select Month','wp-stats-manager').':&nbsp;</label></td></tr>';
                                    $html.='<tr><td>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectYear']).'</td><td>'.wsmFnGetMonthDropDown(WSM_PREFIX.'SelectMonth',$arrPostData[WSM_PREFIX.'SelectMonth']).'</td></tr>';
                                $html.='</table>';                                    
                            $html.='</div>'; 
                            $html.='<div class="wsmRange wsmHide">';            
                                $html.='<table class="wsmOuterTable">';
                                    $html.='<tr><td><strong>'.__('From','wp-stats-manager').':</strong></td>';
                                        $html.='<td><table>'; 
                                        $html.='<tr><td><label for="'.WSM_PREFIX.'SelectFromYear">'.__('Select Year','wp-stats-manager').':&nbsp;</label></td><td><label for="'.WSM_PREFIX.'SelectFromMonth">'.__('Select Month','wp-stats-manager').':&nbsp;</label></td></tr>';
                                        $html.='<tr><td>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectFromYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectFromYear']).'</td><td>'.wsmFnGetMonthDropDown(WSM_PREFIX.'SelectFromMonth',$arrPostData[WSM_PREFIX.'SelectFromMonth']).'</td></tr>';
                                        $html.='</table>';
                                        $html.='</td><td><strong>'.__('To','wp-stats-manager').':</strong></td>';
                                        $html.='<td><table>'; 
                                        $html.='<tr><td><label for="'.WSM_PREFIX.'SelectToYear">'.__('Select Year','wp-stats-manager').':&nbsp;</label></td><td><label for="'.WSM_PREFIX.'SelectToMonth">'.__('Select Month','wp-stats-manager').':&nbsp;</label></td></tr>';
                                        $html.='<tr><td>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectToYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectToYear']).'</td><td>'.wsmFnGetMonthDropDown(WSM_PREFIX.'SelectToMonth',$arrPostData[WSM_PREFIX.'SelectToMonth']).'</td></tr>';
                                        $html.='</table></td>';
                                $html.='</tr>';
                                $html.='</table>'; 
                            $html.='</div>';  
                            $html.='<div class="wsmCompare wsmHide">';
                                $html.='<table class="wsmOuterTable">';
                                $html.='<tr><td><strong>'.__('First','wp-stats-manager').':</strong></td>';
                                    $html.='<td><table>'; 
                                     $html.='<tr><td><label for="'.WSM_PREFIX.'SelectFirstYear">'.__('Select Year','wp-stats-manager').':&nbsp;</label></td><td><label for="'.WSM_PREFIX.'SelectFirstMonth">'.__('Select Month','wp-stats-manager').':&nbsp;</label></td></tr>';
                                    $html.='<tr><td>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectFirstYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectFirstYear']).'</td><td>'.wsmFnGetMonthDropDown(WSM_PREFIX.'SelectFirstMonth',$arrPostData[WSM_PREFIX.'SelectFirstMonth']).'</td></tr>';
                                    $html.='</table></td>';
                                    $html.='<td><strong>'.__('Second','wp-stats-manager').':</strong></td>';
                                    $html.='<td><table>'; 
                                    $html.='<tr><td><label for="'.WSM_PREFIX.'SelectSecondYear">'.__('Select Year','wp-stats-manager').':&nbsp;</label></td><td><label for="'.WSM_PREFIX.'SelectSecondMonth">'.__('Select Month','wp-stats-manager').':&nbsp;</label></td></tr>';
                                    $html.='<tr><td>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectSecondYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectSecondYear']).'</td><td>'.wsmFnGetMonthDropDown(WSM_PREFIX.'SelectSecondMonth',$arrPostData[WSM_PREFIX.'SelectSecondMonth']).'</td></tr>';
                                    $html.='</table></td>';
                                $html.='</tr>';
                                $html.='</table>';            
                            $html.='</div>';  
                        $html.='</div>'; 
                    }
                    if(!in_array('Monthly',$arrHide)){
                        $html.='<div class="wsmMonthly">';
                            $html.='<div class="wsmNormal wsmHide">';
                            $html.='<label for="'.WSM_PREFIX.'SelectMonthlyYear">'.__('Select Year','wp-stats-manager').':&nbsp;</label>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectMonthlyYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectMonthlyYear']);                      
                            $html.='</div>'; 
                            $html.='<div class="wsmRange wsmHide">'; 
                            $html.='<span><label for="'.WSM_PREFIX.'SelectMonthlyFromYear">'.__('From','wp-stats-manager').':&nbsp;</label>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectMonthlyFromYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectMonthlyFromYear']).'</span><span><label for="'.WSM_PREFIX.'SelectMonthlyToYear">'.__('To','wp-stats-manager').':&nbsp;</label>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectMonthlyToYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectMonthlyToYear']).'</span>';           
                            $html.='</div>';  
                            $html.='<div class="wsmCompare wsmHide">';  
                            $html.='<span><label for="'.WSM_PREFIX.'SelectMonthlyFirstYear">'.__('First','wp-stats-manager').':&nbsp;</label>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectMonthlyFirstYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectMonthlyFirstYear']).'</span><span><label for="'.WSM_PREFIX.'SelectMonthlySecondYear">'.__('Second','wp-stats-manager').':&nbsp;</label>'.wsmFnGetYearDropDown(WSM_PREFIX.'SelectMonthlySeondYear',$firstVisitDate,$arrPostData[WSM_PREFIX.'SelectMonthlySeondYear']).'</span>';                
                            $html.='</div>';
                        $html.='</div>';
                    }
                $html.='</div>'; 
                $html.='<div class="wsmbottomWrapper">';
                    $html.='<div class="wsmRadioWrapper">';
                    $checked=$arrPostData[WSM_PREFIX."FilterWay"]=='Normal'?'Checked="checked"':'';
                        $html.='<input type="radio" class="wsmRadioTab" name="'.WSM_PREFIX.'FilterWay" value="Normal" id="'.WSM_PREFIX.'FilterNormal" '.$checked.' /><label for="'.WSM_PREFIX.'FilterNormal">'.__('Normal','wp-stats-manager').'</label>';
                        $checked=$arrPostData[WSM_PREFIX."FilterWay"]=='Range'?'Checked="checked"':'';
                        $html.='<input type="radio" class="wsmRadioTab" name="'.WSM_PREFIX.'FilterWay" value="Range" id="'.WSM_PREFIX.'FilterRange" '.$checked.'  /><label for="'.WSM_PREFIX.'FilterRange">'.__('Range','wp-stats-manager').'</label>';
                        $checked=$arrPostData[WSM_PREFIX."FilterWay"]=='Compare'?'Checked="checked"':'';
                        $html.='<input type="radio" class="wsmRadioTab" name="'.WSM_PREFIX.'FilterWay" value="Compare" id="'.WSM_PREFIX.'FilterCompare" '.$checked.' /><label for="'.WSM_PREFIX.'FilterCompare">'.__('Compare','wp-stats-manager').'</label>'; 
                    $html.='</div>';
                     $html.='<input type="submit" name="'.WSM_PREFIX.'FilterSubmit" id="'.WSM_PREFIX.'FilterSubmit" class="button button-primary" value="'.__('Show Statistics','wp-stats-manager').'">';
                $html.='</div>';        
            //$html.='</form>';
        $html.='</div>';
        $wsmAdminJavaScript.='
        jQuery("#'.WSM_PREFIX.'_'.$atts['id'].' .'.WSM_PREFIX.$arrPostData[WSM_PREFIX.'FilterType'].' .'.WSM_PREFIX.$arrPostData[WSM_PREFIX.'FilterWay'].'").removeClass("wsmHide");
        var wsmDateFormat="yy-mm-dd";
        function wsmGetDate( element ) {
            var date;
            try {
                date = jQuery.datepicker.parseDate( wsmDateFormat, element.value );
            } catch( error ) {
                date = null;
            }
            return date;
        }
        jQuery(".wsmMiddleWrapper #wsmShowCalendarNormal").datepicker({
            dateFormat:wsmDateFormat,
            altFormat:wsmDateFormat,
            altField: "#'.WSM_PREFIX.'TxtHourlyNormalDate",
            defaultDate:"'.$arrPostData[WSM_PREFIX.'TxtHourlyNormalDate'].'",
            changeMonth: true,
            changeYear: true,
            minDate:-30,
            maxDate:0,
            constrainInput:true,
            showOtherMonths: true,
            selectOtherMonths: true
        });
        
        var wsmFrom=jQuery(".wsmMiddleWrapper #wsmShowCalendarFrom").datepicker({
            dateFormat:wsmDateFormat,
            altFormat:wsmDateFormat,
            altField: "#'.WSM_PREFIX.'TxtHourlyRangeFromDate",
            defaultDate:"'.$arrPostData[WSM_PREFIX.'TxtHourlyRangeFromDate'].'",
            changeMonth: true,
            changeYear: true,
            minDate:-30,
            maxDate:-1,
            constrainInput:true,
            showOtherMonths: true,
            selectOtherMonths: true
        }).on( "change", function() {
            wsmTo.datepicker( "option", "minDate", wsmGetDate( this ) );
        });
        var wsmTo=jQuery(".wsmMiddleWrapper #wsmShowCalendarTo").datepicker({
            dateFormat:wsmDateFormat,
            altFormat:wsmDateFormat,
            altField: "#'.WSM_PREFIX.'TxtHourlyRangeToDate",
            defaultDate:"'.$arrPostData[WSM_PREFIX.'TxtHourlyRangeToDate'].'",
            changeMonth: true,
            changeYear: true,
            minDate:-30,
            maxDate:-1,
            constrainInput:true,
            showOtherMonths: true,
            selectOtherMonths: true
        }).on( "change", function() {
            wsmFrom.datepicker( "option", "maxDate", wsmGetDate( this ) );
        });
        jQuery(".wsmMiddleWrapper #wsmShowCalendarFirst").datepicker({
            dateFormat:wsmDateFormat,
            altFormat:wsmDateFormat,
            altField: "#'.WSM_PREFIX.'TxtHourlyCompareFirstDate",
            defaultDate:"'.$arrPostData[WSM_PREFIX.'TxtHourlyCompareFirstDate'].'",
            changeMonth: true,
            changeYear: true,
            minDate:-'.$installDays.',
            maxDate:-1,
            constrainInput:true,
            showOtherMonths: true,
            selectOtherMonths: true
        });
        jQuery(".wsmMiddleWrapper #wsmShowCalendarSecond").datepicker({
            dateFormat:wsmDateFormat,
            altFormat:wsmDateFormat,
            altField: "#'.WSM_PREFIX.'TxtHourlyCompareSecondDate",
            defaultDate:"'.$arrPostData[WSM_PREFIX.'TxtHourlyCompareSecondDate'].'",
            changeMonth: true,
            changeYear: true,
            minDate:-'.$installDays.',
            maxDate:-1,
            constrainInput:true,
            showOtherMonths: true,
            selectOtherMonths: true
        });
        var wsmFnShowTabs=function(pElmValue,cElmValue){
            jQuery(".wsmMiddleWrapper ."+pElmValue).removeClass("wsmHide").siblings().addClass("wsmHide");
            jQuery(".wsmMiddleWrapper ."+pElmValue+" ."+cElmValue).removeClass("wsmHide").siblings().addClass("wsmHide");
        }
        jQuery(".wsmbottomWrapper .wsmRadioWrapper .wsmRadioTab").click(function(){
            pElmValue=WSM_PREFIX+jQuery(".wsmRadioTabWrapper input.wsmRadioTab:checked").val();
            cElmValue=WSM_PREFIX+jQuery(this).val();
            wsmFnShowTabs(pElmValue,cElmValue);
        });
        jQuery(".wsmRadioTabWrapper .wsmRadioTab").click(function(){
            pElmValue=WSM_PREFIX+jQuery(this).val();
            cElmValue=WSM_PREFIX+jQuery(".wsmbottomWrapper .wsmRadioWrapper input.wsmRadioTab:checked").val();  
            wsmFnShowTabs(pElmValue,cElmValue);
        });
        jQuery("#'.WSM_PREFIX.'mainMetboxForm").submit(function(e){
                  
            var tab=jQuery(".wsmRadioTabWrapper input.wsmRadioTab:checked").val();
            var radio=jQuery(".wsmbottomWrapper .wsmRadioWrapper input.wsmRadioTab:checked").val();  
            var wsmError="";
            switch(tab){
                case "Hourly":
                    if(radio=="Compare"){
                        if(jQuery("#'.WSM_PREFIX.'TxtHourlyCompareFirstDate").val()=="" || jQuery("#'.WSM_PREFIX.'TxtHourlyCompareSecondDate").val()==""){
                            wsmError="'.__('The \"First\" date OR \"Second\" date should not be blank.','wp-stats-manager').'";
                        }else if(jQuery("#'.WSM_PREFIX.'TxtHourlyCompareFirstDate").val()==jQuery("#'.WSM_PREFIX.'TxtHourlyCompareSecondDate").val()){
                            wsmError="'.__('The \"First\" date and \"Second\" date should not be same.','wp-stats-manager').'";
                        }
                    }                    
                    if(radio=="Range"){                        
                        if(jQuery("#'.WSM_PREFIX.'TxtHourlyRangeFromDate").val()=="" || jQuery("#'.WSM_PREFIX.'TxtHourlyRangeToDate").val()==""){
                            wsmError="'.__('The \"From\" date OR \"To\" date should not be blank.','wp-stats-manager').'";
                        }else if(jQuery("#'.WSM_PREFIX.'TxtHourlyRangeFromDate").val()==jQuery("#'.WSM_PREFIX.'TxtHourlyRangeToDate").val()){
                            wsmError="'.__('The \"From\" Year and \"To\" Year should not be same.','wp-stats-manager').'";
                        }
                    }
                break;
                case "Daily":                   
                     if(radio=="Compare"){
                         var fYM=jQuery("#'.WSM_PREFIX.'SelectFirstYear").val()+" "+jQuery("#'.WSM_PREFIX.'SelectFirstMonth").val();
                         var sYM=jQuery("#'.WSM_PREFIX.'SelectSecondYear").val()+" "+jQuery("#'.WSM_PREFIX.'SelectSecondMonth").val();
                        if(fYM==sYM){
                            wsmError="'.__('The \"First\" Year-Month and \"Second\" Year-Month should not be same.','wp-stats-manager').'";
                        }
                    }
                    if(radio=="Range"){
                         var fYM=jQuery("#'.WSM_PREFIX.'SelectFromYear").val()+" "+jQuery("#'.WSM_PREFIX.'SelectFromMonth").val();
                         var sYM=jQuery("#'.WSM_PREFIX.'SelectToYear").val()+" "+jQuery("#'.WSM_PREFIX.'SelectToMonth").val();
                        if(fYM==sYM){
                            wsmError="'.__('The \"From\" Year-Month and \"To\" Year-Month should not be same.','wp-stats-manager').'";
                        }
                    }
                break;
                case "Monthly":
                    if(radio=="Compare"){
                         var fY=jQuery("#'.WSM_PREFIX.'SelectMonthlyFirstYear").val();
                         var sY=jQuery("#'.WSM_PREFIX.'SelectMonthlySeondYear").val();
                        if(fY==sY){
                            wsmError="'.__('The \"First\" Year and \"Second\" Year should not be same.','wp-stats-manager').'";
                        }
                    }
                    if(radio=="Range"){
                         var fY=jQuery("#'.WSM_PREFIX.'SelectMonthlyFromYear").val();
                         var sY=jQuery("#'.WSM_PREFIX.'SelectMonthlyToYear").val();
                        if(fY==sY){
                            wsmError="'.__('The \"From\" Year and \"To\" Year should not be same.','wp-stats-manager').'";
                        }
                    }
                break;
            }
            if(wsmError==""){
                return true;
            }else{
                jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'.wsmFilterWrapper").prepend("<div class=\"wsmErrorBox\">"+wsmError+"</div>"); 
                setTimeout(function(){ jQuery("#'.WSM_PREFIX.'_'.$atts['id'].'.wsmFilterWrapper .wsmErrorBox").fadeOut().remove(); }, 3000);                            
            }
            return false;
        });
        ';        
        return $html;
    }      
    function wsm_showDayStatBox($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrPostData=wsmSanitizeFilteredPostData();
        $atts = shortcode_atts( array(
            'id' => '_dayStatBox',
            'title' => __('Daily Stats','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second']
        ), $atts,WSM_PREFIX.'_showDayStatBox');               
        $pageViews=$visitors=$firstTimeVisitors=$pvpv=$nvr=0;
        $CpageViews=$Cvisitors=$CfirstTimeVisitors=$Cpvpv=$Cnvr=0;
        $strPageViews=$strVisitors=$strFirstTimeVisitors=$strPvpv=$strNvr='';
        $arrAtts=array();
        
        $html='<div id="'.WSM_PREFIX.$atts['id'].'">';
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal' || $atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';             
                }                
                if($atts['condition']=='Compare'){
                    $arrAtts['date']=$atts['first'];
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';
                    
                    $arrAtts['date']=$atts['second'];                    
                    $CpageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $CfirstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($CpageViews>0 && $Cvisitors>0){
                        $Cpvpv=($CpageViews/$Cvisitors);
                    }
                    if($CfirstTimeVisitors>0 && $Cvisitors>0){
                        $Cnvr=($CfirstTimeVisitors*100)/$Cvisitors;
                    }
                    $strPageViews.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($CpageViews,0).'</span>';
                    $strVisitors.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cvisitors,0).'</span>';
                    $strFirstTimeVisitors.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($CfirstTimeVisitors,0).'</span>';
                    $strPvpv.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cpvpv,2).'</span>';
                    $strNvr.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cnvr,2).'%</span>';
                }  
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from'])); 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';             
                }     
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01')); 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';             
                }           
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount('Range',$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount('Range',$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';
                    
                    $arrAtts['from']=$atts['second'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                    $CpageViews=$this->objDatabase->fnGetTotalPageViewCount('Range',$arrAtts);
                    $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                    $CfirstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount('Range',$arrAtts);
                    if($CpageViews>0 && $Cvisitors>0){
                        $Cpvpv=($CpageViews/$Cvisitors);
                    }
                    if($CfirstTimeVisitors>0 && $Cvisitors>0){
                        $Cnvr=($CfirstTimeVisitors*100)/$Cvisitors;
                    }
                    $strPageViews.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($CpageViews,0).'</span>';
                    $strVisitors.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cvisitors,0).'</span>';
                    $strFirstTimeVisitors.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($CfirstTimeVisitors,0).'</span>';
                    $strPvpv.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cpvpv,2).'</span>';
                    $strNvr.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cnvr,2).'%</span>';
                }
            break;
            case 'Monthly':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01-01';
                    $arrAtts['to']=$atts['from'].'-12-31'; 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';             
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01-01';
                    $arrAtts['to']=$atts['to'].'-12-31'; 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';             
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01-01';
                    $arrAtts['to']=$atts['first'].'-12-31';
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount('Range',$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount('Range',$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }        
                    if($firstTimeVisitors>0 && $visitors>0){
                        $nvr=($firstTimeVisitors*100)/$visitors;
                    }
                    $strPageViews='<span>'.number_format_i18n($pageViews,0).'</span>';
                    $strVisitors='<span>'.number_format_i18n($visitors,0).'</span>';
                    $strFirstTimeVisitors='<span>'.number_format_i18n($firstTimeVisitors,0).'</span>';
                    $strPvpv='<span>'.number_format_i18n($pvpv,2).'</span>';
                    $strNvr='<span>'.number_format_i18n($nvr,2).'%</span>';
                    
                    $arrAtts['from']=$atts['second'].'-01-01';
                    $arrAtts['to']=$atts['second'].'-12-31';                    
                    $CpageViews=$this->objDatabase->fnGetTotalPageViewCount('Range',$arrAtts);
                    $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                    $CfirstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount('Range',$arrAtts);
                    if($CpageViews>0 && $Cvisitors>0){
                        $Cpvpv=($CpageViews/$Cvisitors);
                    }
                    if($CfirstTimeVisitors>0 && $Cvisitors>0){
                        $Cnvr=($CfirstTimeVisitors*100)/$Cvisitors;
                    }
                    $strPageViews.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($CpageViews,0).'</span>';
                    $strVisitors.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cvisitors,0).'</span>';
                    $strFirstTimeVisitors.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($CfirstTimeVisitors,0).'</span>';
                    $strPvpv.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cpvpv,2).'</span>';
                    $strNvr.='<span class="wsmColorGray">&nbsp;/&nbsp;'.number_format_i18n($Cnvr,2).'%</span>';
                }
            break;
        }
           
        $html.='<table><tr><td><span>'.$strPageViews.'</span><div>'.__('Total Page Views','wp-stats-manager').'</div></td><td><span>'.$strVisitors.'</span><div>'.__('Total Visitors','wp-stats-manager').'</div></td><td><span>'.$strFirstTimeVisitors.'</span><div>'.__('First Time Visitors','wp-stats-manager').'</div></tr><tr><td><span>'.$strPvpv.'</span><div>'.__('Page Views Per Visit','wp-stats-manager').'</div></td><td colspan="2"><span>'.$strNvr.'</span><div>'.__('New Visitors Ratio','wp-stats-manager').'</div></td></tr></table>';
        $html.='</div>'; 
        return $html;     
    }
    function wsm_showTrafficStatsList($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrPostData=wsmSanitizeFilteredPostData();
        $atts = shortcode_atts( array(
            'title' => __('Show Stats','wp-stats-manager'),
            'id' =>'TableTrafficStats',           
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second']
            ), $atts, WSM_PREFIX.'_showTrafficStatsList');
            $arrChartStats=array();            
            $arrChartStatSecond=array();
            $html='<div id="'.WSM_PREFIX."_".$atts['id'].'" class="wsmTableContainer">';
            $html.='<table class="wsmTableStriped">';
            $html.='<tr><th class="wsmTimeColumn" width="135">'.__('Time','wp-stats-manager').'</th><th  class="wsmInlineGraph" width="390">'.__('Visitors Graph','wp-stats-manager').'</th><th width="150">'.__('Visitors','wp-stats-manager').'</th><th  width="240">'.__('Pages','wp-stats-manager').' (Ppv)</th><th  width="150">'.__('New Visitors','wp-stats-manager').'(%)</th><th class="wsmBounceColumn"  width="105">%'.__('Bounce','wp-stats-manager').'</th></tr>';    
            $maxProgress=240;         
            switch($atts['type']){
                case 'Hourly': 
                    if($atts['condition']=='Normal') {
                        $arrChartStats=$this->objDatabase->fnGetHistoricalHourlyStatsByDate($atts['from']);
                        $arrAtts['from']=$atts['from'];
                        $arrAtts['to']=$atts['to']; 
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);                        
                        $maxVisitors=wsmGetMaxValueFromArray($arrChartStats['visitors']);
                        foreach($arrChartStats['pageViews'] as $key=>$value){
                            $chVisitors=intval($arrChartStats['visitors'][$key]);
                            $percent=0;
                            $width=0;
                            if($chVisitors >0 && $visitors>0){
                                $percent=number_format_i18n(($chVisitors*100)/$visitors,2);
                                $width=($chVisitors*$maxProgress)/$maxVisitors;
                            }                            
                            $html.='<tr>';
                            $html.='<td class="wsmTimeColumn">'.sprintf('%02d', $key).':00-'.sprintf('%02d', ($key)).':59</td>';
                            $html.='<td class="wsmInlineGraph"><div class="wsmProgressBar" style="width:'.$width.'px"></div><font> ('.$percent.') %</font></td>';  
                            $html.='<td>'.$arrChartStats['visitors'][$key].'</td>';  
                            $html.='<td>'.$value.'('.$arrChartStats['ppv'][$key].')</td>';  
                            $html.='<td>'.$arrChartStats['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)</td>';  
                            $html.='<td>'.$arrChartStats['Bounce'][$key].'%</td>';  
                            $html.='</tr>';
                        }
                    }     
                    if($atts['condition']=='Range') {
                        $arrChartStats=$this->objDatabase->fnGetHourlyStatsByDateRange($atts['from'],$atts['to']);
                        $arrAtts['from']=$atts['from'];
                        $arrAtts['to']=$atts['to']; 
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                        $maxVisitors=0;
                        foreach($arrChartStats as $dayStats){
                            $max=wsmGetMaxValueFromArray($dayStats['stats']['visitors']);
                            if($max>$maxVisitors){
                                $maxVisitors=$max;
                            }                          
                        }
                        foreach($arrChartStats as $dayStats){ 
                            
                            $html.='<tr><td class="wsmCommonColumn" colspan="6"><strong>'.$dayStats['date'].'</strong></td></tr>';
                            foreach($dayStats['stats']['pageViews'] as $key=>$value){
                                 $chVisitors=intval($dayStats['stats']['visitors'][$key]);
                                    $percent=0;
                                    $width=0;
                                    if($chVisitors >0 && $visitors>0){
                                        $percent=number_format_i18n(($chVisitors*100)/$visitors,2);
                                        $width=($chVisitors*$maxProgress)/$maxVisitors;
                                    } 
                                $html.='<tr>';
                                $html.='<td class="wsmTimeColumn">'.sprintf('%02d', $key).':00-'.sprintf('%02d', ($key)).':59</td>';
                                $html.='<td class="wsmInlineGraph"><div class="wsmProgressBar" style="width:'.$width.'px"></div><font> ('.$percent.') %</font></td>';  
                                $html.='<td>'.$dayStats['stats']['visitors'][$key].'</td>';  
                                $html.='<td>'.$value.'('.$dayStats['stats']['ppv'][$key].')</td>';  
                                $html.='<td>'.$dayStats['stats']['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)</td>';  
                                $html.='<td>'.$dayStats['stats']['Bounce'][$key].'%</td>';  
                                $html.='</tr>';
                            }
                            
                        }
                    }           
                    if($atts['condition']=='Compare') {
                        $arrChartStats=$this->objDatabase->fnGetHistoricalHourlyStatsByDate($atts['first']);
                        $arrChartStatSecond=$this->objDatabase->fnGetHistoricalHourlyStatsByDate($atts['second']);
                        $arrAtts['date']=$atts['first'];
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                        $arrAtts['date']=$atts['second'];
                        $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                        $halfMaxProgress=($maxProgress/2)-10;
                        $diffArray=array_map(function($first,$second){
                                $tP=0; 
                                $tDiff=abs(intval($first)-intval($second));
                                if($first>0){
                                    $tP=($tDiff*100)/$first;
                                }
                            return $tP;
                        }, $arrChartStats['visitors'], $arrChartStatSecond['visitors']); 
                        $maxVisitorsDiff=wsmGetMaxValueFromArray($diffArray);
                        foreach($arrChartStats['pageViews'] as $key=>$value){
                            $html.='<tr>';
                            $html.='<td class="wsmTimeColumn">'.sprintf('%02d', $key).':00-'.sprintf('%02d', ($key)).':59<div>'.sprintf('%02d', $key).':00-'.sprintf('%02d', ($key)).':59</div></td>';
                            
                            $firstV=intval($arrChartStats['visitors'][$key]);
                            $secondV=intval($arrChartStatSecond['visitors'][$key]);
                            $percent=0;
                            $width=0;
                            $progressHTML='<table width="100%"><tr>';
                            $diffV=$firstV-$secondV;
                            if($firstV>0 && $secondV>0){  
                                $percent=number_format_i18n(($diffV*100)/$firstV,2);           
                                $width=abs(($percent*$halfMaxProgress)/$maxVisitorsDiff);
                                if($diffV>=0){                                    
                                    $progressHTML.='<td width="50%"></td><td width="50%"><div class="wsmProgressBar wsmGreenBack" style="width:'.$width.'px"></div><font> ('.$percent.') % </font></td>';
                                }else{
                                     $progressHTML.='<td width="50%"><font> ('.$percent.') % </font><div class="wsmProgressBar wsmRedBack" style="width:'.$width.'px"></div></td><td width="50%"></td>';
                                }       
                            }                     
                            $progressHTML.='</tr></table></td>';
                            $html.='<td class="wsmInlineGraph">'.$progressHTML.'</td>';  
                            $html.='<td>'.$arrChartStats['visitors'][$key].'<div>'.$arrChartStatSecond['visitors'][$key].'</div></td>';  
                            $html.='<td>'.$value.'('.$arrChartStats['ppv'][$key].')<div>'.$arrChartStatSecond['pageViews'][$key].'('.$arrChartStatSecond['ppv'][$key].')</div></td>';  
                            $html.='<td>'.$arrChartStats['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)<div>'.$arrChartStatSecond['firstTime'][$key].'('.$arrChartStatSecond['newVisitor'][$key].'%)</div></td>';  
                            $html.='<td>'.$arrChartStats['Bounce'][$key].'%<div>'.$arrChartStatSecond['Bounce'][$key].'%</div></td>';     
                             $html.='</tr>';
                         }
                    }
                    
                break;
                case 'Daily':
                    if($atts['condition']=='Normal') {
                        $arrChartStats=$this->objDatabase->fnGetDailyReportByMonth($atts['from']);
                        $arrAtts['from']=$atts['from'].'-01';
                        $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from'])); 
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                        $maxVisitors=wsmGetMaxValueFromArray($arrChartStats['visitors']);
                        foreach($arrChartStats['pageViews'] as $key=>$value){
                            $chVisitors=intval($arrChartStats['visitors'][$key]);
                            $percent=0;
                            $width=0;
                            if($chVisitors >0 && $visitors>0){
                                $percent=number_format_i18n(($chVisitors*100)/$visitors,2);
                                $width=($chVisitors*$maxProgress)/$maxVisitors;
                            }
                            $html.='<tr>';
                            $html.='<td class="wsmTimeColumn">'.wsmGetDateByTimeStamp('d F Y',strtotime($atts['from'].'-'.($key+1))).'</td>';
                            $html.='<td class="wsmInlineGraph"><div class="wsmProgressBar" style="width:'.$width.'px"></div><font> ('.$percent.') %</font></td>';  
                            $html.='<td>'.$arrChartStats['visitors'][$key].'</td>';  
                            $html.='<td>'.$value.'('.$arrChartStats['ppv'][$key].')</td>';  
                            $html.='<td>'.$arrChartStats['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)</td>';  
                            $html.='<td>'.$arrChartStats['Bounce'][$key].'%</td>';  
                            $html.='</tr>';
                        }
                    } 
                    if($atts['condition']=='Range'){
                        $arrChartStats=$this->objDatabase->fnGetDailyStatsByMonthRange($atts['from'],$atts['to']);
                        $arrAtts['from']=$atts['from'].'-01';
                        $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01'));
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                        $maxVisitors=0;
                        foreach($arrChartStats as $dayStats){
                            $max=wsmGetMaxValueFromArray($dayStats['stats']['visitors']);
                            if($max>$maxVisitors){
                                $maxVisitors=$max;
                            }                          
                        }
                        foreach($arrChartStats as $dayStats){
                            $html.='<tr><td class="wsmCommonColumn" colspan="6"><strong>'.$dayStats['date'].'</strong></td></tr>';
                            foreach($dayStats['stats']['pageViews'] as $key=>$value){
                                $chVisitors=intval($dayStats['stats']['visitors'][$key]);
                                $percent=0;
                                $width=0;
                                if($chVisitors >0 && $visitors>0){
                                    $percent=number_format_i18n(($chVisitors*100)/$visitors,2);
                                    $width=($chVisitors*$maxProgress)/$maxVisitors;
                                } 
                                $html.='<tr>';
                                $html.='<td class="wsmTimeColumn">'.($key+1).' '.$dayStats['date'].'</td>';
                                $html.='<td class="wsmInlineGraph"><div class="wsmProgressBar" style="width:'.$width.'px"></div><font> ('.$percent.') %</font></td>';  
                                $html.='<td>'.$dayStats['stats']['visitors'][$key].'</td>';  
                                $html.='<td>'.$value.'('.$dayStats['stats']['ppv'][$key].')</td>';  
                                $html.='<td>'.$dayStats['stats']['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)</td>';  
                                $html.='<td>'.$dayStats['stats']['Bounce'][$key].'%</td>';  
                                $html.='</tr>';
                            }
                            
                        }
                    }
                    if($atts['condition']=='Compare') {
                        $arrChartStats=$this->objDatabase->fnGetDailyReportByMonth($atts['first']);                     
                        $arrChartStatSecond=$this->objDatabase->fnGetDailyReportByMonth($atts['second']);
                        $arrAtts['from']=$atts['first'].'-01';
                        $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                        $arrAtts['from']=$atts['second'].'-01';
                        $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                        $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);            
                        $arrLoop=count($arrChartStats['pageViews'])>count($arrChartStatSecond['pageViews'])?$arrChartStats['pageViews']:$arrChartStatSecond['pageViews'];
                        $halfMaxProgress=($maxProgress/2)-10;
                        $diffArray=array_map(function($first,$second){
                                $tP=0; 
                                $tDiff=abs(intval($first)-intval($second));
                                if($first>0){
                                    $tP=($tDiff*100)/$first;
                                }
                            return $tP;
                        }, $arrChartStats['visitors'], $arrChartStatSecond['visitors']); 
                        $maxVisitorsDiff=wsmGetMaxValueFromArray($diffArray);
                        
                        foreach($arrLoop as $key=>$value){
                            $firstV=isset($arrChartStats['visitors'][$key])?intval($arrChartStats['visitors'][$key]):0;
                            $secondV=isset($arrChartStatSecond['visitors'][$key])?intval($arrChartStatSecond['visitors'][$key]):0;
                            $percent=0;
                            $width=0;
                            $progressHTML='<table width="100%"><tr>';
                            $diffV=$firstV-$secondV;
                            if($firstV>0 && $secondV>0){                                
                                $percent=number_format_i18n(($diffV*100)/$firstV,2);    
                                $width=number_format_i18n(abs(($percent*$halfMaxProgress)/$maxVisitorsDiff),0);
                                if($diffV>=0){                                    
                                    $progressHTML.='<td width="50%"></td><td width="50%"><div class="wsmProgressBar wsmGreenBack" style="width:'.$width.'px"></div><font> ('.$percent.') % </font></td>';
                                }else{
                                     $progressHTML.='<td width="50%"><font> ('.$percent.') % </font><div class="wsmProgressBar wsmRedBack" style="width:'.$width.'px"></div></td><td width="50%"></td>';
                                }       
                            }                     
                            $progressHTML.='</tr></table></td>';
                            
                            $html.='<tr>';
                            $firstDate=isset($arrChartStats['visitors'][$key])?wsmGetDateByTimeStamp('d F Y',strtotime($atts['first'].'-'.($key+1))):'-';
                            $secondDate=isset($arrChartStatSecond['visitors'][$key])?wsmGetDateByTimeStamp('d F Y',strtotime($atts['second'].'-'.($key+1))):'-';
                            $html.='<td class="wsmTimeColumn">'.$firstDate.'<div>'.$secondDate.'</div></td>';
                            $html.='<td class="wsmInlineGraph">'.$progressHTML.'</td>'; 
                            $firstVisitors=isset($arrChartStats['visitors'][$key])?$arrChartStats['visitors'][$key]:'-'; 
                            $secondVisitors=isset($arrChartStatSecond['visitors'][$key])?$arrChartStatSecond['visitors'][$key]:'-'; 
                            $html.='<td>'.$firstVisitors.'<div>'.$secondVisitors.'</div></td>';
                            $firstPPV=isset($arrChartStats['ppv'][$key])?$value.'('.$arrChartStats['ppv'][$key].')':'-'; 
                            $secondPPV=isset($arrChartStatSecond['ppv'][$key])?$arrChartStatSecond['pageViews'][$key].'('.$arrChartStatSecond['ppv'][$key].')':'-';  
                            $html.='<td>'.$firstPPV.'<div>'.$secondPPV.'</div></td>';
                            $firstTimeVis=isset($arrChartStats['newVisitor'][$key])?$arrChartStats['firstTime'][$key].'('.$arrChartStats['newVisitor'][$key].'%)':'-'; 
                            $secondTimeVis=isset($arrChartStatSecond['newVisitor'][$key])?$arrChartStatSecond['firstTime'][$key].'('.$arrChartStatSecond['newVisitor'][$key].'%)':'-';  
                            $html.='<td>'.$firstTimeVis.'<div>'.$secondTimeVis.'</div></td>';  
                            $firstBounce=isset($arrChartStats['Bounce'][$key])?$arrChartStats['Bounce'][$key]:'-'; 
                            $secondBounce=isset($arrChartStatSecond['Bounce'][$key])?$arrChartStatSecond['Bounce'][$key]:'-'; 
                            $html.='<td>'.$firstBounce.'%<div>'.$secondBounce.'%</div></td>';     
                            $html.='</tr>';
                         }                    
                    }  
                break;
                case 'Monthly':
                    if($atts['condition']=='Normal') {
                        $arrChartStats=$this->objDatabase->fnGetMonthlyReportByYear($atts['from']);
                        $arrAtts['from']=$atts['from'].'-01-01';
                        $arrAtts['to']=$atts['from'].'-12-31'; 
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                        $maxVisitors=wsmGetMaxValueFromArray($arrChartStats['visitors']);
                        foreach($arrChartStats['pageViews'] as $key=>$value){
                            $chVisitors=intval($arrChartStats['visitors'][$key]);
                            $percent=0;
                            $width=0;
                            if($chVisitors >0 && $visitors>0){
                                $percent=number_format_i18n(($chVisitors*100)/$visitors,2);
                                $width=($chVisitors*$maxProgress)/$maxVisitors;
                            }
                            $html.='<tr>';
                            $html.='<td class="wsmTimeColumn">'.wsmGetDateByTimeStamp('F Y',strtotime($atts['from'].'-'.($key+1).'-01')).'</td>';
                            $html.='<td class="wsmInlineGraph"><div class="wsmProgressBar" style="width:'.$width.'px"></div><font> ('.$percent.') %</font></td>'; 
                            $html.='<td>'.$arrChartStats['visitors'][$key].'</td>';  
                            $html.='<td>'.$value.'('.$arrChartStats['ppv'][$key].')</td>';  
                            $html.='<td>'.$arrChartStats['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)</td>';  
                            $html.='<td>'.$arrChartStats['Bounce'][$key].'%</td>';  
                            $html.='</tr>';
                        }
                       }
                    if($atts['condition']=='Range'){
                        $arrChartStats=$this->objDatabase->fnGetMonthlyStatsByRange($atts['from'],$atts['to']);
                        $arrAtts['from']=$atts['from'].'-01-01';
                        $arrAtts['to']=$atts['to'].'-12-31'; 
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                        $maxVisitors=0;
                        foreach($arrChartStats as $dayStats){
                            $max=wsmGetMaxValueFromArray($dayStats['stats']['visitors']);
                            if($max>$maxVisitors){
                                $maxVisitors=$max;
                            }                          
                        }
                        foreach($arrChartStats as $dayStats){
                            $html.='<tr><td class="wsmCommonColumn" colspan="6"><strong>'.$dayStats['date'].'</strong></td></tr>';
                            foreach($dayStats['stats']['pageViews'] as $key=>$value){
                                $chVisitors=intval($dayStats['stats']['visitors'][$key]);
                                $percent=0;
                                $width=0;
                                if($chVisitors >0 && $visitors>0){
                                    $percent=number_format_i18n(($chVisitors*100)/$visitors,2);
                                    $width=($chVisitors*$maxProgress)/$maxVisitors;
                                } 
                                $html.='<tr>';
                                $html.='<td class="wsmTimeColumn">'.wsmGetDateByTimeStamp('F Y',strtotime($dayStats['date'].'-'.($key+1).'-01')).'</td>';
                                $html.='<td class="wsmInlineGraph"><div class="wsmProgressBar" style="width:'.$width.'px"></div><font> ('.$percent.') %</font></td>';  
                                $html.='<td>'.$dayStats['stats']['visitors'][$key].'</td>';  
                                $html.='<td>'.$value.'('.$dayStats['stats']['ppv'][$key].')</td>';  
                                $html.='<td>'.$dayStats['stats']['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)</td>';  
                                $html.='<td>'.$dayStats['stats']['Bounce'][$key].'%</td>';  
                                $html.='</tr>';
                            }
                            
                        }
                    }
                    if($atts['condition']=='Compare') {
                        $arrChartStats=$this->objDatabase->fnGetMonthlyReportByYear($atts['first']);
                        $arrChartStatSecond=$this->objDatabase->fnGetMonthlyReportByYear($atts['second']); 
                        $arrAtts['from']=$atts['first'].'-01-01';
                        $arrAtts['to']=$atts['first'].'-12-31';
                        $visitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                        $arrAtts['from']=$atts['second'].'-01-01';
                        $arrAtts['to']=$atts['second'].'-12-31';
                        $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);       
                        $halfMaxProgress=($maxProgress/2)-10;
                        $diffArray=array_map(function($first,$second){
                                $tP=0; 
                                $tDiff=abs(intval($first)-intval($second));
                                if($first>0){
                                    $tP=($tDiff*100)/$first;
                                }
                            return $tP;
                        }, $arrChartStats['visitors'], $arrChartStatSecond['visitors']); 
                        $maxVisitorsDiff=wsmGetMaxValueFromArray($diffArray);
                        
                        foreach($arrChartStats['pageViews'] as $key=>$value){
                            $firstV=intval($arrChartStats['visitors'][$key]);
                            $secondV=intval($arrChartStatSecond['visitors'][$key]);
                            $percent=0;
                            $width=0;
                            $progressHTML='<table width="100%"><tr>';
                            $diffV=$firstV-$secondV;
                            if($firstV>0 && $secondV>0){                                
                                $percent=number_format_i18n(($diffV*100)/$firstV,2);                           
                                $width=abs(($percent*$halfMaxProgress)/$maxVisitorsDiff);
                                if($diffV>=0){                                    
                                    $progressHTML.='<td width="50%"></td><td width="50%"><div class="wsmProgressBar wsmGreenBack" style="width:'.$width.'px"></div><font> ('.$percent.') % </font></td>';
                                }else{
                                     $progressHTML.='<td width="50%"><font> ('.$percent.') % </font><div class="wsmProgressBar wsmRedBack" style="width:'.$width.'px"></div></td><td width="50%"></td>';
                                }       
                            }                     
                            $progressHTML.='</tr></table></td>';
                            $html.='<tr>';
                            $html.='<td class="wsmTimeColumn">'.wsmGetDateByTimeStamp('F Y',strtotime($atts['first'].'-'.($key+1).'-01')).'<div>'.wsmGetDateByTimeStamp('F Y',strtotime($atts['second'].'-'.($key+1).'-01')).'</div></td>';
                            $html.='<td class="wsmInlineGraph">'.$progressHTML.'</td>';  
                            $html.='<td>'.$arrChartStats['visitors'][$key].'<div>'.$arrChartStatSecond['visitors'][$key].'</div></td>';  
                            $html.='<td>'.$value.'('.$arrChartStats['ppv'][$key].')<div>'.$arrChartStatSecond['pageViews'][$key].'('.$arrChartStatSecond['ppv'][$key].')</div></td>';  
                            $html.='<td>'.$arrChartStats['firstTime'][$key].'('.$arrChartStats['today']['newVisitor'][$key].'%)<div>'.$arrChartStatSecond['firstTime'][$key].'('.$arrChartStatSecond['newVisitor'][$key].'%)</div></td>';  
                            $html.='<td>'.$arrChartStats['Bounce'][$key].'<div>'.$arrChartStatSecond['Bounce'][$key].'%</div></td>';     
                             $html.='</tr>';
                         }                
                    } 
                break;
            }                
            $html.='</table>';
            $html.='</div>';
            return $html; 
    }
    function wsm_showDayStatsGraph($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
       global $wsmAdminJavaScript;
        $arrPostData=wsmSanitizeFilteredPostData();
        $atts = shortcode_atts( array(
            'title' => __('Get Current Stats','wp-stats-manager'),
            'id' =>'barGrouped',
            'width' =>'1140px',
            'height' =>'400px',
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second']
            ), $atts, WSM_PREFIX.'_showCurrentStats');
        $arrChartStats=array();            
        $arrChartStatSecond=array();   
        $yMax=0;         
        switch($atts['type']){
            case 'Hourly': 
                if($atts['condition']=='Normal') {
                    $arrChartStats=$this->objDatabase->fnGetHistoricalHourlyStatsByDate($atts['from']);  
                    $yMax=wsmGetMaxValueFromArray($arrChartStats['pageViews']);  
                }                
                if($atts['condition']=='Compare') {
                    $arrChartStats=$this->objDatabase->fnGetHistoricalHourlyStatsByDate($atts['first']);
                    $arrChartStatSecond=$this->objDatabase->fnGetHistoricalHourlyStatsByDate($atts['second']);  
                    $yMax=wsmGetMaxValueFromArray($arrChartStats['pageViews']); 
                    $y2Max=wsmGetMaxValueFromArray($arrChartStatSecond['pageViews']); 
                    if($y2Max>$yMax){
                        $yMax=$y2Max;
                    }
                    $wsmAdminJavaScript.="
                        var {$atts['id']}_firstTimeSecond = ".json_encode($arrChartStatSecond['firstTime']).";
                        var {$atts['id']}_visitorsSecond = ".json_encode($arrChartStatSecond['visitors']).";
                        var {$atts['id']}_pageViewsSecond = ".json_encode($arrChartStatSecond['pageViews']).";";
                                      
                }
            break;
            case 'Daily':
                if($atts['condition']=='Normal') {
                    $arrChartStats=$this->objDatabase->fnGetDailyReportByMonth($atts['from']);
                    $yMax=wsmGetMaxValueFromArray($arrChartStats['pageViews']); 
                } 
                 if($atts['condition']=='Compare') {
                    $arrChartStats=$this->objDatabase->fnGetDailyReportByMonth($atts['first']);
                    $arrChartStatSecond=$this->objDatabase->fnGetDailyReportByMonth($atts['second']); 
                      
                    $yMax=wsmGetMaxValueFromArray($arrChartStats['pageViews']); 
                    $y2Max=wsmGetMaxValueFromArray($arrChartStatSecond['pageViews']); 
                    if($y2Max>$yMax){
                        $yMax=$y2Max;
                    }              
                    $wsmAdminJavaScript.="
                        var {$atts['id']}_firstTimeSecond = ".json_encode($arrChartStatSecond['firstTime']).";
                        var {$atts['id']}_visitorsSecond = ".json_encode($arrChartStatSecond['visitors']).";
                        var {$atts['id']}_pageViewsSecond = ".json_encode($arrChartStatSecond['pageViews']).";";
                                      
                }                 
            break;
            case 'Monthly':
                if($atts['condition']=='Normal') {
                    $arrChartStats=$this->objDatabase->fnGetMonthlyReportByYear($atts['from']);
                    $yMax=wsmGetMaxValueFromArray($arrChartStats['pageViews']); 
                }
                if($atts['condition']=='Compare') {
                    $arrChartStats=$this->objDatabase->fnGetMonthlyReportByYear($atts['first']);
                    $arrChartStatSecond=$this->objDatabase->fnGetMonthlyReportByYear($atts['second']);
                    $yMax=wsmGetMaxValueFromArray($arrChartStats['pageViews']); 
                    $y2Max=wsmGetMaxValueFromArray($arrChartStatSecond['pageViews']); 
                    if($y2Max>$yMax){
                        $yMax=$y2Max;
                    }                      
                    $wsmAdminJavaScript.="
                        var {$atts['id']}_firstTimeSecond = ".json_encode($arrChartStatSecond['firstTime']).";
                        var {$atts['id']}_visitorsSecond = ".json_encode($arrChartStatSecond['visitors']).";
                        var {$atts['id']}_pageViewsSecond = ".json_encode($arrChartStatSecond['pageViews']).";";
                                      
                } 
                
            break;
        }    
        $yMax=($yMax*110)/100; 
        $html='<div class="chartContainer">';
        $html.=$this->wsm_getTopChartBar('DayStatsGraph');
        $html.='<div id="'.$atts['id'].'"></div></div>';
        
        $arrColors=array('rgba(244,81,81,1)','rgba(251,194,70,1)','rgba(87,135,184,1)');
        $arrLineColors=array('rgba(153, 0, 0, 1)','rgba(202, 121, 0, 1)','rgba(18, 56, 114, 1)');
        $keyLabels=array(__('First Time Visitor','wp-stats-manager'),__('Visitors','wp-stats-manager'),__('Page Views','wp-stats-manager'));
      
        $wsmAdminJavaScript.="
        var {$atts['id']}_firstTime = ".json_encode($arrChartStats['firstTime']).";
        var {$atts['id']}_visitors = ".json_encode($arrChartStats['visitors']).";
        var {$atts['id']}_pageViews = ".json_encode($arrChartStats['pageViews']).";
        var {$atts['id']}_bounceRate = ".json_encode($arrChartStats['Bounce']).";
        var {$atts['id']}_ppv = ".json_encode($arrChartStats['ppv']).";
        var {$atts['id']}_newVisitor = ".json_encode($arrChartStats['newVisitor']).";
        var {$atts['id']}_avgOnline = ".json_encode($arrChartStats['avgOnline']).";
        var XLabels=".json_encode($arrChartStats['XLabels']).";        
        var {$atts['id']}_legendIndex=[];
        var legendLabels=".json_encode($keyLabels).";
        var legendLabels2=[];
        var seriesRenderee=[{renderer:jQuery.jqplot.BarRenderer,fillAndStroke:true},{renderer:jQuery.jqplot.BarRenderer,fillAndStroke:true},{renderer:jQuery.jqplot.BarRenderer,fillAndStroke:true}];
         var {$atts['id']}_DataP=[{$atts['id']}_firstTime,{$atts['id']}_visitors,{$atts['id']}_pageViews ];
         var {$atts['id']}_Data={$atts['id']}_DataP;
         var colors = ".json_encode($arrColors).";
         var colors2 =[];
         ";
         if($atts['condition']=='Compare') {
             $wsmAdminJavaScript.=" 
             var legendLabels2=".json_encode($keyLabels).";            
             var colors2 = ".json_encode($arrLineColors).";
        var {$atts['id']}_Data=[{$atts['id']}_firstTime,{$atts['id']}_visitors,{$atts['id']}_pageViews,{$atts['id']}_firstTimeSecond,{$atts['id']}_visitorsSecond,{$atts['id']}_pageViewsSecond ];        
        ";
         }
        $wsmAdminJavaScript.="        
        var keyColors=colors.slice();
        //var seriesRenderee=[{fill:true,fillAndStroke:true},{fill:true,fillAndStroke:true},{fill:true,fillAndStroke:true}];
        var {$atts['id']}_options=  {
        // Tell the plot to stack the bars.
        series:seriesRenderee,
        stackSeries: false,
        seriesColors :colors.concat(colors2),
        height:'{$atts['height']}',
        gridPadding:{
        right:40,
        left:40,
        top:20,
        bottom:60
        },
        captureRightClick: true,
        seriesDefaults:{
        rendererOptions: {
        barMargin: 6,
        // Highlight bars when mouse button pressed.
        // Disables default highlighting on mouse over.
        highlightMouseDown: true,
        shadow:false,
        smooth: true,
        fillToZero: true,
        barPadding:0,
        pointLabels: { show: true }
        }
        },
        axes: {
        xaxis: {
        renderer: jQuery.jqplot.CategoryAxisRenderer,
        ticks : XLabels
        },
        yaxis: {
        padMin: 0,
        min:0,
        max:{$yMax},
        tickOptions: {
            formatString: '%d'
        }
        },
        y2axis: {
        padMin: 0,
        min:0,
        autoscale:true,
        tickOptions: {
        showGridline: false
        }
        }
        },
        legend: {
        labels :legendLabels.concat(legendLabels2),
        show: true,
        location: 'nw',
        placement: 'inside',
        renderer: jQuery.jqplot.EnhancedLegendRenderer,
        rendererOptions: {
        numberRows: 1,
        numberColumns :3,
        seriesToggle: true
        }
        },
        highlighter: {
        show:true,
        tooltipLocation: 'ne',
        useAxesFormatters: true,
        sizeAdjust: 2.5,
        formatString:'%s, %P',
        tooltipContentEditor : function(str, seriesIndex, pointIndex, plot){                
        return '<span style=\'background:'+plot.seriesColors[seriesIndex]+'\'></span>'+plot.legend.labels[seriesIndex]+ \": \" + plot.data[seriesIndex][pointIndex];
        }
        },
        cursor :{
        show : false,
        followMouse : false,
        useAxesFormatters:false
        }
        };
        var wsmMoveLegend;
        if(typeof wsmMoveLegend!='function'){
        wsmMoveLegend=function(parent){
        parent.find('.wsmTopChartBar').each(function(){
        legendDiv=jQuery(this).children('.wsmChartLegend');
        legendDiv.empty();
        parent.find('table.jqplot-table-legend').appendTo(legendDiv);
        legendDiv.children('table.jqplot-table-legend').removeAttr('style');
        });
        }
        }
        plot_{$atts['id']} = jQuery.jqplot('{$atts['id']}', {$atts['id']}_Data,{$atts['id']}_options);
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        var topButtons=jQuery('#{$atts['id']}').siblings('.wsmTopChartBar').find('.wsmButton');
        topButtons.on('click',function(e){
        e.preventDefault();
        //jQuery(this).siblings('[data-chart=No]').click();
        jQuery(this).siblings().removeClass('active');        
        chart=jQuery(this).data('chart');
        nLabels=legendLabels.slice();
        seriesData={$atts['id']}_DataP.slice();
        arrSeries=seriesRenderee.slice();
        arrColors=colors.slice();
        newColors=['rgba(0,128,0,1)'];
        renderer=[{disableStack: true,yaxis:'y2axis'}];
        numberColumns=4;
        switch(chart){
        case 'Bounce':
        nLabels.push('".__('Bounce Rate(%)','wp-stats-manager')."');
        seriesData.push({$atts['id']}_bounceRate);
        break;
        case 'Ppv':
        nLabels.push('".__('Page Views Per Visit','wp-stats-manager')."');
        seriesData.push({$atts['id']}_ppv);
        break;
        case 'Nvis':
        nLabels.push('".__('% New Visitors','wp-stats-manager')."');
        seriesData.push({$atts['id']}_newVisitor);
        break;
        case 'Online':
        nLabels.push('".__('Average Online','wp-stats-manager')."');
        seriesData.push({$atts['id']}_avgOnline);
        break;        
        default:
        break;
        }
        jQuery('#{$atts['id']}').empty();
        {$atts['id']}_options.legend.labels=nLabels;
        arrSeries=arrSeries.concat(renderer);
        arrColors=arrColors.concat(newColors);
        {$atts['id']}_options.series=arrSeries;
        {$atts['id']}_options.seriesColors=arrColors;
        {$atts['id']}_options.legend.rendererOptions.numberColumns=numberColumns;
        jQuery(this).addClass('active');
        plot_{$atts['id']} = jQuery.jqplot('{$atts['id']}', seriesData,{$atts['id']}_options);
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        });
        
        jQuery(window).on('resize',function(){
        plot_{$atts['id']}.replot({ resetAxes: true });
        wsmMoveLegend(jQuery('#{$atts['id']}').parents('.postbox'));
        });
        jQuery('#{$atts['id']}').parent().find('.".WSM_PREFIX."ChartLegend').on('click','table.jqplot-table-legend tr td', function(event, mode){
            if(mode!='code'){
               var tI = {$atts['id']}_legendIndex.indexOf(jQuery(this).index());
                if(tI==-1){
                    {$atts['id']}_legendIndex.push(jQuery(this).index());
                }else{ 
                    {$atts['id']}_legendIndex.splice(tI, 1);
                }
            }
        });
        ";
        return $html;
    }  
    function wsm_showRefferStatsBox($atts, $content=""){    
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrPostData=wsmSanitizeFilteredPostData();
        global $wsmAdminJavaScript;
        $atts = shortcode_atts( array(
            'id' => '_refferStatsBox',
            'title' => __('Daily Stats','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
            'searchengine' => ''
        ), $atts, WSM_PREFIX.'_showRefferStatsBox');        

        $pageViews=$visitors=$firstTimeVisitors=$pvpv=0;
        $CpageViews=$Cvisitors=$CfirstTimeVisitors=$Cpvpv=0;
        $CPpageViews=$CPvisitors=$CPfirstTimeVisitors=$CPpvpv=0;
        $PpageViews=$Pvisitors=$PfirstTimeVisitors=$Ppvpv=0;
        $RpageViews=$Rvisitors=$RfirstTimeVisitors=$Rpvpv=0;
        $RCpageViews=$RCvisitors=$RCfirstTimeVisitors=$RCpvpv=0;

		$arrAtts['searchengine']=$atts['searchengine']; 
        $html='<div id="'.WSM_PREFIX.$atts['id'].'" class="wsmTableContainer">';
        /*echo $atts['type']."<br/>";
        echo $atts['condition']."<br/>";
        echo $atts['from']."<br/>";
        echo $atts['to']."<br/>";*/
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal' || $atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $RpageViews=$this->objDatabase->fnGetReferralTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $Rvisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    $RfirstTimeVisitors=$this->objDatabase->fnGetReferralFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    } 
                    if($RpageViews>0 && $Rvisitors>0){
                        $Rpvpv=($RpageViews/$Rvisitors);
                    }  
                    if($pageViews>0 && $RpageViews>0){
                        $PpageViews=($RpageViews/$pageViews)*100;
                        $PpageViews=number_format_i18n($PpageViews,2); 
                    } 
                    if($visitors>0 && $Rvisitors>0){
                        $Pvisitors=($Rvisitors/$visitors)*100;
                        $Pvisitors=number_format_i18n($Pvisitors,2); 
                    } 
                    if($firstTimeVisitors>0 && $RfirstTimeVisitors>0){
                        $PfirstTimeVisitors=($RfirstTimeVisitors/$firstTimeVisitors)*100;
                        $PfirstTimeVisitors=number_format_i18n($PfirstTimeVisitors,2); 
                    } 
                    $pvDiff=$Rpvpv-$pvpv;                        
                    if($pvDiff>0 && $pvpv>0){
                       $Ppvpv= ($pvDiff/$pvpv)*100;
                       if($Ppvpv>=0){
                           $Ppvpv='+'.number_format_i18n($Ppvpv,2); 
                       }else{
                           $Ppvpv='-'.number_format_i18n($Ppvpv,2); 
                       }
                    }
                    $pageViews=number_format_i18n($pageViews,0);
                    $RpageViews=number_format_i18n($RpageViews,0);
                    $visitors=number_format_i18n($visitors,0);
                    $Rvisitors=number_format_i18n($Rvisitors,0);
                    $firstTimeVisitors=number_format_i18n($firstTimeVisitors,0);
                    $RfirstTimeVisitors=number_format_i18n($RfirstTimeVisitors,0);
                    $pvpv=number_format_i18n($pvpv,2);         
                    $Rpvpv=number_format_i18n($Rpvpv,2);         
                }                
                if($atts['condition']=='Compare'){
                    $arrAtts['date']=$atts['first'];
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $RpageViews=$this->objDatabase->fnGetReferralTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $Rvisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    $RfirstTimeVisitors=$this->objDatabase->fnGetReferralFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    } 
                    if($RpageViews>0 && $Rvisitors>0){
                        $Rpvpv=($RpageViews/$Rvisitors);
                    }  
                    if($pageViews>0 && $RpageViews>0){
                        $PpageViews=($RpageViews/$pageViews)*100;
                        $PpageViews=number_format_i18n($PpageViews,2); 
                    } 
                    if($visitors>0 && $Rvisitors>0){
                        $Pvisitors=($Rvisitors/$visitors)*100;
                        $Pvisitors=number_format_i18n($Pvisitors,2); 
                    } 
                    if($firstTimeVisitors>0 && $RfirstTimeVisitors>0){
                        $PfirstTimeVisitors=($RfirstTimeVisitors/$firstTimeVisitors)*100;
                        $PfirstTimeVisitors=number_format_i18n($PfirstTimeVisitors,2); 
                    } 
                    $pvDiff=$Rpvpv-$pvpv;                        
                    if($pvDiff>0 && $pvpv>0){
                       $Ppvpv= ($pvDiff/$pvpv)*100;
                       if($Ppvpv>=0){
                           $Ppvpv='+'.number_format_i18n($Ppvpv,2); 
                       }else{
                           $Ppvpv='-'.number_format_i18n($Ppvpv,2); 
                       }
                    }       
                    $pageViews=number_format_i18n($pageViews,0);
                    $RpageViews=number_format_i18n($RpageViews,0);
                    $visitors=number_format_i18n($visitors,0);
                    $Rvisitors=number_format_i18n($Rvisitors,0);
                    $firstTimeVisitors=number_format_i18n($firstTimeVisitors,0);
                    $RfirstTimeVisitors=number_format_i18n($RfirstTimeVisitors,0);
                    $pvpv=number_format_i18n($pvpv,2); 
                    $Rpvpv=number_format_i18n($Rpvpv,2); 
                    
                    $arrAtts['date']=$atts['second'];                    
                    $CpageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $RCpageViews=$this->objDatabase->fnGetReferralTotalPageViewCount($atts['condition'],$arrAtts);
                    $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $RCvisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount($atts['condition'],$arrAtts);
                    $CfirstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    $RCfirstTimeVisitors=$this->objDatabase->fnGetReferralFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($CpageViews>0 && $Cvisitors>0){
                        $Cpvpv=($CpageViews/$Cvisitors);
                    }
                    if($CpageViews>0 && $Cvisitors>0){
                        $Cpvpv=($CpageViews/$Cvisitors);
                    } 
                    if($RCpageViews>0 && $RCvisitors>0){
                        $RCpvpv=($RCpageViews/$RCvisitors);
                    }  
                    if($CpageViews>0 && $RCpageViews>0){
                        $CPpageViews=($RCpageViews/$CpageViews)*100;
                        $CPpageViews=number_format_i18n($CPpageViews,2); 
                    } 
                    if($Cvisitors>0 && $RCvisitors>0){
                        $CPvisitors=($RCvisitors/$Cvisitors)*100;
                        $CPvisitors=number_format_i18n($CPvisitors,2); 
                    } 
                    if($CfirstTimeVisitors>0 && $RCfirstTimeVisitors>0){
                        $CPfirstTimeVisitors=($RCfirstTimeVisitors/$CfirstTimeVisitors)*100;
                        $CPfirstTimeVisitors=number_format_i18n($CPfirstTimeVisitors,2); 
                    } 
                    $CpvDiff=$RCpvpv-$Cpvpv;                        
                    if($CpvDiff>0 && $Cpvpv>0){
                       $CPpvpv= ($CpvDiff/$Cpvpv)*100;
                       if($CPpvpv>=0){
                           $CPpvpv='+'.number_format_i18n($CPpvpv,2); 
                       }else{
                           $CPpvpv='-'.number_format_i18n($CPpvpv,2); 
                       }
                    }
                    $CpageViews=number_format_i18n($CpageViews,0);
                    $RCpageViews=number_format_i18n($RCpageViews,0);
                    $Cvisitors=number_format_i18n($Cvisitors,0);
                    $RCvisitors=number_format_i18n($RCvisitors,0);
                    $CfirstTimeVisitors=number_format_i18n($CfirstTimeVisitors,0);
                    $RCfirstTimeVisitors=number_format_i18n($RCfirstTimeVisitors,0);
                    $Cpvpv=number_format_i18n($Cpvpv,2);
                    $RCpvpv=number_format_i18n($RCpvpv,2);
                }  
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from'])); 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $RpageViews=$this->objDatabase->fnGetReferralTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $Rvisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    $RfirstTimeVisitors=$this->objDatabase->fnGetReferralFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }                           
                    if($RpageViews>0 && $Rvisitors>0){
                        $Rpvpv=($RpageViews/$Rvisitors);
                    }  
                    if($pageViews>0 && $RpageViews>0){
                        $PpageViews=($RpageViews/$pageViews)*100;
                        $PpageViews=number_format_i18n($PpageViews,2); 
                    } 
                    if($visitors>0 && $Rvisitors>0){
                        $Pvisitors=($Rvisitors/$visitors)*100;
                        $Pvisitors=number_format_i18n($Pvisitors,2); 
                    } 
                    if($firstTimeVisitors>0 && $RfirstTimeVisitors>0){
                        $PfirstTimeVisitors=($RfirstTimeVisitors/$firstTimeVisitors)*100;
                        $PfirstTimeVisitors=number_format_i18n($PfirstTimeVisitors,2); 
                    } 
                    $pvDiff=$Rpvpv-$pvpv;                        
                    if($pvDiff>0 && $pvpv>0){
                       $Ppvpv= ($pvDiff/$pvpv)*100;
                       if($Ppvpv>=0){
                           $Ppvpv='+'.number_format_i18n($Ppvpv,2); 
                       }else{
                           $Ppvpv='-'.number_format_i18n($Ppvpv,2); 
                       }
                    }
                    $pageViews=number_format_i18n($pageViews,0);
                    $RpageViews=number_format_i18n($RpageViews,0);
                    $visitors=number_format_i18n($visitors,0);
                    $Rvisitors=number_format_i18n($Rvisitors,0);
                    $firstTimeVisitors=number_format_i18n($firstTimeVisitors,0);
                    $RfirstTimeVisitors=number_format_i18n($RfirstTimeVisitors,0);
                    $pvpv=number_format_i18n($pvpv,2);         
                    $Rpvpv=number_format_i18n($Rpvpv,2);              
                }     
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01')); 
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
                    $RpageViews=$this->objDatabase->fnGetReferralTotalPageViewCount($atts['condition'],$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
                    $Rvisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount($atts['condition'],$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    $RfirstTimeVisitors=$this->objDatabase->fnGetReferralFirstTimeVisitorCount($atts['condition'],$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }                            
                    if($RpageViews>0 && $Rvisitors>0){
                        $Rpvpv=($RpageViews/$Rvisitors);
                    }  
                    if($pageViews>0 && $RpageViews>0){
                        $PpageViews=($RpageViews/$pageViews)*100;
                        $PpageViews=number_format_i18n($PpageViews,2); 
                    } 
                    if($visitors>0 && $Rvisitors>0){
                        $Pvisitors=($Rvisitors/$visitors)*100;
                        $Pvisitors=number_format_i18n($Pvisitors,2); 
                    } 
                    if($firstTimeVisitors>0 && $RfirstTimeVisitors>0){
                        $PfirstTimeVisitors=($RfirstTimeVisitors/$firstTimeVisitors)*100;
                        $PfirstTimeVisitors=number_format_i18n($PfirstTimeVisitors,2); 
                    } 
                    $pvDiff=$Rpvpv-$pvpv;                        
                    if($pvDiff>0 && $pvpv>0){
                       $Ppvpv= ($pvDiff/$pvpv)*100;
                       if($Ppvpv>=0){
                           $Ppvpv='+'.number_format_i18n($Ppvpv,2); 
                       }else{
                           $Ppvpv='-'.number_format_i18n($Ppvpv,2); 
                       }
                    }
                    $pageViews=number_format_i18n($pageViews,0);
                    $RpageViews=number_format_i18n($RpageViews,0);
                    $visitors=number_format_i18n($visitors,0);
                    $Rvisitors=number_format_i18n($Rvisitors,0);
                    $firstTimeVisitors=number_format_i18n($firstTimeVisitors,0);
                    $RfirstTimeVisitors=number_format_i18n($RfirstTimeVisitors,0);
                    $pvpv=number_format_i18n($pvpv,2);         
                    $Rpvpv=number_format_i18n($Rpvpv,2);           
                }           
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));                    
                    $pageViews=$this->objDatabase->fnGetTotalPageViewCount('Range',$arrAtts);
                    $RpageViews=$this->objDatabase->fnGetReferralTotalPageViewCount('Range',$arrAtts);
                    $visitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                    $Rvisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount('Range',$arrAtts);
                    $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount('Range',$arrAtts);
                    $RfirstTimeVisitors=$this->objDatabase->fnGetReferralFirstTimeVisitorCount('Range',$arrAtts);
                    if($pageViews>0 && $visitors>0){
                        $pvpv=($pageViews/$visitors);
                    }                            
                    if($RpageViews>0 && $Rvisitors>0){
                        $Rpvpv=($RpageViews/$Rvisitors);
                    }  
                    if($pageViews>0 && $RpageViews>0){
                        $PpageViews=($RpageViews/$pageViews)*100;
                        $PpageViews=number_format_i18n($PpageViews,2); 
                    } 
                    if($visitors>0 && $Rvisitors>0){
                        $Pvisitors=($Rvisitors/$visitors)*100;
                        $Pvisitors=number_format_i18n($Pvisitors,2); 
                    } 
                    if($firstTimeVisitors>0 && $RfirstTimeVisitors>0){
                        $PfirstTimeVisitors=($RfirstTimeVisitors/$firstTimeVisitors)*100;
                        $PfirstTimeVisitors=number_format_i18n($PfirstTimeVisitors,2); 
                    } 
                    $pvDiff=$Rpvpv-$pvpv;                        
                    if($pvpv>0){
                       $Ppvpv= ($pvDiff/$pvpv)*100;
                       if($Ppvpv>=0){
                           $Ppvpv='+'.number_format_i18n($Ppvpv,2); 
                       }else{
                           $Ppvpv=number_format_i18n($Ppvpv,2); 
                       }
                    }
                    $pageViews=number_format_i18n($pageViews,0);
                    $RpageViews=number_format_i18n($RpageViews,0);
                    $visitors=number_format_i18n($visitors,0);
                    $Rvisitors=number_format_i18n($Rvisitors,0);
                    $firstTimeVisitors=number_format_i18n($firstTimeVisitors,0);
                    $RfirstTimeVisitors=number_format_i18n($RfirstTimeVisitors,0);
                    $pvpv=number_format_i18n($pvpv,2);         
                    $Rpvpv=number_format_i18n($Rpvpv,2); 
                    
                    $arrAtts['from']=$atts['second'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                    $CpageViews=$this->objDatabase->fnGetTotalPageViewCount('Range',$arrAtts);
                    $RCpageViews=$this->objDatabase->fnGetReferralTotalPageViewCount('Range',$arrAtts);
                    $Cvisitors=$this->objDatabase->fnGetTotalVisitorsCount('Range',$arrAtts);
                    $RCvisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount('Range',$arrAtts);
                    $CfirstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount('Range',$arrAtts);
                    $RCfirstTimeVisitors=$this->objDatabase->fnGetReferralFirstTimeVisitorCount('Range',$arrAtts);
                    if($CpageViews>0 && $Cvisitors>0){
                        $Cpvpv=($CpageViews/$Cvisitors);
                    }
                    
                    if($CpageViews>0 && $Cvisitors>0){
                        $Cpvpv=($CpageViews/$Cvisitors);
                    } 
                    if($RCpageViews>0 && $RCvisitors>0){
                        $RCpvpv=($RCpageViews/$RCvisitors);
                    }  
                    if($CpageViews>0 && $RCpageViews>0){
                        $CPpageViews=($RCpageViews/$CpageViews)*100;
                        $CPpageViews=number_format_i18n($CPpageViews,2); 
                    } 
                    if($Cvisitors>0 && $RCvisitors>0){
                        $CPvisitors=($RCvisitors/$Cvisitors)*100;
                        $CPvisitors=number_format_i18n($CPvisitors,2); 
                    } 
                    if($CfirstTimeVisitors>0 && $RCfirstTimeVisitors>0){
                        $CPfirstTimeVisitors=($RCfirstTimeVisitors/$CfirstTimeVisitors)*100;
                        $CPfirstTimeVisitors=number_format_i18n($CPfirstTimeVisitors,2); 
                    } 
                    $CpvDiff=$RCpvpv-$Cpvpv;                        
                    if($Cpvpv>0){
                       $CPpvpv= ($CpvDiff/$Cpvpv)*100;
                       if($CPpvpv>=0){
                           $CPpvpv='+'.number_format_i18n($CPpvpv,2); 
                       }else{
                           $CPpvpv=number_format_i18n($CPpvpv,2); 
                       }
                    }
                    $CpageViews=number_format_i18n($CpageViews,0);
                    $RCpageViews=number_format_i18n($RCpageViews,0);
                    $Cvisitors=number_format_i18n($Cvisitors,0);
                    $RCvisitors=number_format_i18n($RCvisitors,0);
                    $CfirstTimeVisitors=number_format_i18n($CfirstTimeVisitors,0);
                    $RCfirstTimeVisitors=number_format_i18n($RCfirstTimeVisitors,0);
                    $Cpvpv=number_format_i18n($Cpvpv,2);
                    $RCpvpv=number_format_i18n($RCpvpv,2);
                }
            break;            
        }
        $html.='<table class="wsmTableStriped">';
        
        $header = '<th>'.__('Referrer Summary','wp-stats-manager').'</th><th>(%)</th>';
        if( $atts['searchengine'] ){
            $header = '';
        }
        
        $html.='<tr><th>'.__('Summary','wp-stats-manager').'</th><th>'.__('Stats Summary','wp-stats-manager').'</th>'.$header.'</tr>';
        
        if($atts['condition']=='Compare'){
			
			$totalPageViewRow = '<td>'.$RpageViews.'<div>'.$RCpageViews.'</div></td><td>'.$PpageViews.'%<div>'.$CPpageViews.'%</div></td>';
			$totalVisitorsRow = '<td>'.$Rvisitors.'<div>'.$RCvisitors.'</div></td><td>'.$Pvisitors.'%<div>'.$CPvisitors.'%</div></td>';
			$totalNewVisitorsRow = '<td>'.$RfirstTimeVisitors.'<div>'.$RCfirstTimeVisitors.'</div></td><td>'.$PfirstTimeVisitors.'%<div>'.$CPfirstTimeVisitors.'%</div></td>';
			$pageViewPerVisitRow = '<td>'.$Rpvpv.'<div>'.$RCpvpv.'</div></td><td>'.$Ppvpv.'%<div>'.$CPpvpv.'%</div></td>';
	        
			if( $atts['searchengine'] ){
	            $totalPageViewRow = $totalVisitorsRow = $totalNewVisitorsRow = $pageViewPerVisitRow = '';
	        }	
			
			
            $html.='<tr><td>'.__('Total Page Views','wp-stats-manager').'</td><td>'.$pageViews.'<div>'.$CpageViews.'</div></td>'.$totalPageViewRow.'</tr>';
            $html.='<tr><td>'.__('Total Visitors','wp-stats-manager').'</td><td>'.$visitors.'<div>'.$Cvisitors.'</div></td>'.$totalVisitorsRow.'</tr>';
            $html.='<tr><td>'.__('Total New Visitors','wp-stats-manager').'</td><td>'.$firstTimeVisitors.'<div>'.$CfirstTimeVisitors.'</div></td>'.$totalNewVisitorsRow.'</tr>';
            $html.='<tr><td>'.__('Page Views Per Visit','wp-stats-manager').'</td><td>'.$pvpv.'<div>'.$Cpvpv.'</div></td>'.$pageViewPerVisitRow.'</tr>';
        }else{
			
			$totalPageViewRow = '<td>'.$RpageViews.'</td><td>'.$PpageViews.'%</td>';
			$totalVisitorsRow = '<td>'.$Rvisitors.'</td><td>'.$Pvisitors.'%</td>';
			$totalNewVisitorsRow = '<td>'.$RfirstTimeVisitors.'</td><td>'.$PfirstTimeVisitors.'%</td>';
			$pageViewPerVisitRow = '<td>'.$Rpvpv.'</td><td>'.$Ppvpv.'%</td>';
	        
			if( $atts['searchengine'] ){
	            $totalPageViewRow = $totalVisitorsRow = $totalNewVisitorsRow = $pageViewPerVisitRow = '';
	        }	
			
            $html.='<tr><td>'.__('Total Page Views','wp-stats-manager').'</td><td>'.$pageViews.'</td>'.$totalPageViewRow.'</tr>';
            $html.='<tr><td>'.__('Total Visitors','wp-stats-manager').'</td><td>'.$visitors.'</td>'.$totalVisitorsRow.'</tr>';
            $html.='<tr><td>'.__('Total New Visitors','wp-stats-manager').'</td><td>'.$firstTimeVisitors.'</td>'.$totalNewVisitorsRow.'</tr>';
            $html.='<tr><td>'.__('Page Views Per Visit','wp-stats-manager').'</td><td>'.$pvpv.'</td>'.$pageViewPerVisitRow.'</tr>';
        }
        
        $html.='</table>'; 
        $html.='</div>';
        return $html;
    }
    function wsm_showTopReferrerListDetails($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrPostData=wsmSanitizeFilteredPostData();
        global $wsmAdminJavaScript;
        $atts = shortcode_atts( array(
            'id' => '_topRefferStatsList',
            'title' => __('Top Referrer Sites','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second']
        ), $atts, WSM_PREFIX.'_showTopReferrerList');
        $arrReferrerList=array();
        $arrReferrerListSecond=array();
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;
        $html='<div id="'.WSM_PREFIX.$atts['id'].'">';
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal' || $atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts); 
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['date']=$atts['first'];
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                    $arrAtts['date']=$atts['second'];
                    $arrReferrerListSecond=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                }
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01'));
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                    $arrReferrerList=$this->objDatabase->fnGetReferralList('Range',$arrAtts);      
                    $arrAtts['from']=$atts['second'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));  
                    $arrReferrerListSecond=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);       
                }
            break;
        }
        if(count($arrReferrerList)>0){
           /*echo '<pre>';   
                    print_r($arrReferrerList);
                    echo '</pre>';          */
            $counter=1;
            foreach($arrReferrerList as $referrer=>$referrers){                 
                $totalReferrences=count($referrers);                
                $html.='<div class="wsmAccordian">';
                $html.='<span>'.$counter++.'</span><span>'.$referrer.'</span><span class="wsmPushRight">'.__('References','wp-stats-manager').':&nbsp;'.$totalReferrences.'</span>';
                $html.='<div class="wsmTableContainer wsmPanel"><table class="wsmTableStriped">';
                foreach($referrers as $page){
                    $html.='<tr><td><b>'.wsmMaskIPaddress($page['ipAddress']).'</b><div class="wsmPageTitle"><a href="'.$page['url'].'" title="'.$page['title'].'">'.$page['title'].'</a></div></td><td class="wsmIconSet"><img src="'.WSM_URL.'/images/ICO_1px.gif" class="flag flag-'.strtolower($page['alpha2Code']).'" alt="'.$page['country'].'" title="'.$page['country'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['deviceType'])).'" alt="'.$page['deviceType'].'" title="'.$page['deviceType'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['browser'])).'" alt="'.$page['browser'].'" title="'.$page['browser'].'"/><img src="'.WSM_URL.'/images/ICO_1px.gif" class="wsmIcon '.strtolower(str_replace(" ","",$page['osystem'])).'" alt="'.$page['osystem'].'" title="'.$page['osystem'].'"/></td></tr>'; 
                }
                $html.='</table></div>';
                $html.='</div>';
            }
           // $html.='<div class="wsmPageContainer">';
           // $html.=wsmFnGetPagination(count($arrReferrerList),$currentPage,admin_url('admin.php?page='.WSM_PREFIX.'_trafficsrc').'&subPage=RefSites');
           // $html.='</div>';
        }
		else
		{
			$html .="<p class='wsmCenter'>".__('Data / Statistics are not available.','wp-stats-manager')."</p>";
		}
        //$html.='</div>';
        $wsmAdminJavaScript.='
            jQuery("#'.WSM_PREFIX.$atts['id'].' .wsmAccordian").click(function(){
                //jQuery("#'.WSM_PREFIX.$atts['id'].' .wsmAccordian .wsmPanel").hide();
                jQuery(this).find(".wsmPanel").toggle();
            });
        ';
        return $html;
    }  
    function wsm_showTopReferrerList($atts, $content=""){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
        global $wsmAdminJavaScript;
        $atts = shortcode_atts( array(
            'id' => '_topRefferStatsList',
            'title' => __('Top Referrer Sites','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
			'searchengine' => ''
        ), $atts, WSM_PREFIX.'_showTopReferrerList');
        $arrReferrerList=array();
        $arrReferrerListSecond=array();
        $arrYesterDayReferrerList=array();
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;        
      	$arrAtts['searchengine'] = $atts['searchengine']; 
	   $arrAtts['adminURL']=admin_url('admin.php?page='.WSM_PREFIX.'_trafficsrc').'&subPage='.( $atts['searchengine'] ? 'SearchEngines' : 'RefSites' ).'&'.http_build_query($arrRequestData);
        $html='<div id="'.WSM_PREFIX.$atts['id'].'" class="wsmTableContainer">';
        $lastFrom=$lastTo='';
        $format='d F Y';
        $arrCompare=array();
        $condition=$atts['condition'];
        $jsParamAarray=array();
		$jsParamAarray['searchengine'] = $atts['searchengine']; 
        $currentDayVisitors=$totalPrevDayVisitors=0;
        //echo $atts['type'];
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);              
                    $lastFrom=wsmGetDateByInterval('-1 days','Y-m-d', $arrAtts['from']); 
                    $lastTo=wsmGetDateByInterval('-1 days','Y-m-d', $arrAtts['to']);      
                    $currentDayVisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount($atts['condition'],$arrAtts);  
                    $totalPrevDayVisitors=$this->objDatabase->fnGetReferralTotalVisitorsCount($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo));  
                    $jsParamAarray['from']=$arrAtts['from'];
                    $jsParamAarray['to']=$arrAtts['to'];                    
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                    $jsParamAarray['from']=$arrAtts['from'];
                    $jsParamAarray['to']=$arrAtts['to'];
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['date']=$atts['first'];
                    $jsParamAarray['date']=$arrAtts['date'];                   
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                    $arrAtts['date']=$atts['second'];
                    $jsParamAarray['date2']=$arrAtts['date'];
                    unset($arrAtts['currentPage']);
                    unset($arrAtts['adminURL']);
                    $arrCompare=array('date'=>$atts['second']);
                    //$arrReferrerListSecond=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                }
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                    $lastFrom=wsmGetDateByInterval('-1 months','Y-m-d', $arrAtts['from']); 
                    $lastTo=wsmGetDateByInterval('-1 months','Y-m-d', $arrAtts['to']); 
                    $format='F Y';
                    $jsParamAarray['from']=$arrAtts['from'];
                    $jsParamAarray['to']=$arrAtts['to'];
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01'));
                    $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);
                    $jsParamAarray['from']=$arrAtts['from'];
                    $jsParamAarray['to']=$arrAtts['to'];
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                    $arrReferrerList=$this->objDatabase->fnGetReferralList('Range',$arrAtts);  
                    $jsParamAarray['from']=$arrAtts['from'];
                    $jsParamAarray['to']=$arrAtts['to'];  
                    unset($arrAtts['currentPage']);
                    unset($arrAtts['adminURL']);  
                    $arrAtts['from']=$atts['second'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));  
                    $arrCompare=array('from'=>$arrAtts['from'],'to'=>$arrAtts['to']);
                    $jsParamAarray['from2']=$arrAtts['from'];
                    $jsParamAarray['to2']=$arrAtts['to'];
                    //$arrReferrerListSecond=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts);      
                }
            break;
        }
       
        if(count($arrReferrerList['data'])>0){
           /*echo '<pre>';   
                    print_r($arrReferrerList['data']);
                    echo '</pre>';          */
            $counter=(($currentPage-1)*WSM_PAGE_LIMIT)+1;
			$searchengine = $arrAtts['searchengine'];
			$arrAtts['searchengine'] = '';
						
            $html.='<table class="wsmTableStriped">';
            $html.='<tr><th>'.__('Rank','wp-stats-manager').'</th><th>'.__('Referrer url','wp-stats-manager').'</th><th>&nbsp;</th><th>'.__('References','wp-stats-manager').'</th></tr>';
            foreach($arrReferrerList['data'] as $referrer=>$referrers){                 
                $totalReferrences=count($referrers);                
                $arrow='';  
                $lastReferrals=0;  
                $prevDayVisitors=0;  
                $compareReferrals='';            
                $toolTip='';            
                if($atts['condition']=='Normal'){
                    $lastReferrals=$this->objDatabase->fnGetTotalReferralsByRefURL($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo,'refUrl'=>$referrers['refUrl']));
                    //$prevDayVisitors=$this->objDatabase->fnGetReferralTotalVisitorsCountByOSBrowser($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo,'refUrl'=>$referrers['refUrl'])); 
                    //$diff=$referrers['total']-$lastReferrals;
                    $class="";
					$currentPer = $PrevPer= 0;
					if( $totalPrevDayVisitors ){
                    	$PrevPer=($lastReferrals*100)/$totalPrevDayVisitors;
					}
					if( $currentDayVisitors ){
						$currentPer=($referrers['total']*100)/$currentDayVisitors;
					}
                    $diff=$currentPer-$PrevPer;
                    $diffPer=$diff==0?100:$diff;
                    $percent=number_format_i18n($diffPer,0);
                    if($diff<0){
                        $arrow='<div class="wsmArrowDown"></div>'; 
                        $class="wsmColorRed";                        
                    }
                    if($diff>0){
                        $arrow='<div class="wsmArrowUp"></div>';
                        $class="wsmColorGreen";
                        $percent='+'.$percent;
                    }                    
                    $toolTip='<span class="wsmTooltipText">'.$referrers['refUrl'].'<font class="wsmColorYellow">'.wsmGetDateByTimeStamp($format,strtotime($arrAtts['from'])).'&nbsp;/&nbsp;'.wsmGetDateByTimeStamp($format,strtotime($lastFrom)).'</font><font>'.__('Visitors Performance','wp-stats-manager').':&nbsp;<b class="'.$class.'">'.$percent.'%</b></font></span>';
                }
                if($atts['condition']=='Compare'){
                    $arrCompare['refUrl']=$referrers['refUrl'];
                    $condition=isset($arrCompare['date'])?$atts['condition']:'Range';
                    $lastReferrals=$this->objDatabase->fnGetTotalReferralsByRefURL($condition,$arrCompare);
                    $compareReferrals='<div><strong>'.$lastReferrals.'</strong></div>';
                } 
                $jsParamAarray['condition']=$condition;
				$jsParamAarray['id']=$referrers['id'];
                $referrerUrlDetail = $jsParamAarray;
                $referrerUrlDetail['id'] = $referrers['id'];
                $referrerUrlDetail['action'] = 'refUrlDetails';
                
                $html.='<tr id="wsmRowParent_'.$counter.'" class="wsmReferralRow" data-url="'.$referrers['refUrl'].'">';
                $kTitle=(isset($referrers['keyword']) && $referrers['keyword']!=''  && $referrers['keyword']!='-')?$referrers['keyword']:$referrers['refUrl'];
                $html.='<td><div  class="wsmTooltip">'.$arrow.'&nbsp;'.$counter.$toolTip.'</div></td><td><a title="Click to view details" data-referrak_param=\''.(json_encode($referrerUrlDetail)).'\' class="linkReferralViewDetails" href="#">'.$kTitle.'</a></td><td width="15"><a href="#" class="wsmExpandCollapse" data-referral_prg=\''.(json_encode($jsParamAarray)).'\'></a></td><td><strong>'.number_format($referrers['total']).'</strong>'.$compareReferrals.'</td>';                
                $html.='</tr>';               
                $counter++;
            }
            if(isset($arrReferrerList['pagination'])){
                $html.='<tr><td colspan="4">';
                $html.='<div class="wsmPageContainer">';
                $html.=$arrReferrerList['pagination'];
                $html.='</div>';
                $html.='</tr>';
            }
			$html.='</table>';
			$html.='</div>';
        }
        else
        {
			$html ='<p class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</p>';
		}
        
        $wsmAdminJavaScript.='            
            jQuery("#'.WSM_PREFIX.$atts['id'].' .wsmExpandCollapse").click(function(e){
                e.preventDefault();
                var subcolumn=\'<td colspan="4">'.wsmGetSpinner().'</td>\';
                var parentRow=jQuery(this).parents(".wsmReferralRow");
                //var jsonParam=JSON.stringify('.json_encode($jsParamAarray).');
				var jsonParam=JSON.stringify(jQuery(this).data("referral_prg"));
                strRefUrl=\',"refUrl":"\'+parentRow.data("url")+\'"}\';
                jsonParam=jsonParam.replace("}",strRefUrl);                
                console.log(jsonParam);               
                rowId=parentRow.attr("id");
                var arrId = rowId.split("_");
                var childRowId="wsmRowChild_"+arrId[1];
                if(jQuery(this).hasClass("wsmCollapse")){
                    parentRow.siblings("#"+childRowId).slideUp().remove();
                    jQuery(this).removeClass("wsmCollapse");
                    return;
                }
                if(parentRow.siblings("#"+childRowId).length){                    
                    parentRow.siblings("#"+childRowId).html(subcolumn);
                }else{
                    parentRow.after(\'<tr id="\'+childRowId+\'" class="wsmSubRow">\'+subcolumn+\'</tr>\');
                }                
                var wsmFnGetRowDetails=function(){
                           jQuery.ajax({
                               type: "POST",
                               url: wsm_ajaxObject.ajax_url,
                               data: { action: \'refDetails\', requests: jsonParam, r: Math.random() }
                           }).done(function( strResponse ) {                                
                                parentRow.siblings("#"+childRowId).html("<td colspan=\"4\">"+strResponse+"</td>");
                           });                
                };
                wsmFnGetRowDetails();
                jQuery(this).addClass("wsmCollapse");
            });
        ';
        
        
        return $html;
    }   
	
	function wsm_showSearchEngineSummary( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
        $atts = shortcode_atts( array(
            'id' => '_topRefferStatsList',
            'title' => __('Top Referrer Sites','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
			'searchengine' => ''
        ), $atts, WSM_PREFIX.'_showTopReferrerList');
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;        
      	$arrAtts['searchengine'] = $atts['searchengine']; 
		$condition=$atts['condition'];
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['date']=$atts['first'];
                }
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01'));
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
            break;
        }
		/*$arrSearchEngineStatList = $this->objDatabase->fnGetTotalUserSeachEngineWise( $atts['condition'] ,$arrAtts);*/
		$arrSearchEngineStatList = $this->objDatabase->fnGetTotalUserBySeachEngineWise( $atts['condition'] ,$arrAtts);
		if(count($arrSearchEngineStatList) > 0)
		{
			$totalUsers = array_sum( $arrSearchEngineStatList );
			$html = '<div class="panelSearchEngineReview wsmTableContainer">';
			$html .= '<table class="wsmTableStriped">';
			$html .= '<tr><th></th><th></th><th>Visitors</th></tr>';
			$i=0;
			foreach( $arrSearchEngineStatList as $key => $arrSearchEngineStat ){
					
					if($i > 4)
						break;
					if($arrSearchEngineStat  > 0)
					{
						$number = $arrSearchEngineStat * 100 / $totalUsers;
					}
					else
						$number = 0;
					//echo "<br/>";	
					
					$user_per = sprintf('%.2f',$number);
					
					//$user_per = round($number);
					$name = sprintf( '<img class="wsmIcon %s" src="%s/images/ICO_1px.gif" alt="%s" title="%s"> %s', str_replace(' ','', strtolower($key)), WSM_URL,  $key, $key, $key );
					$html .= '<tr><td>'.$name.'</td><td><div class="bar_graph" style="width:'.( $number * 3 ).'px"></div>'.($user_per).'%</td><td>'.$arrSearchEngineStat.'</td></tr>';
				$i++;
			}
			$html .= '</table>';
			$html .= '</div>';
		}
		else
		{
			$html .="<p class='wsmCenter'>".__('Data / Statistics are not available.','wp-stats-manager')."</p>";
		}
		return $html;
		//print_r($arrSearchEngineStatList);
	}    
	
	function wsm_showVisitorsDetail( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
        $atts = shortcode_atts( array(
            'id' => '_topRefferStatsList',
            'title' => __('Top Referrer Sites','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
			'searchengine' => ''
        ), $atts, WSM_PREFIX.'_showTopReferrerList');
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;        
      	$arrAtts['searchengine'] = $atts['searchengine']; 
		$condition=$atts['condition'];
		$arrVisitorsInfo2 = array();
		
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'];
                    $arrAtts['to']=$atts['second'];
                }
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01'));
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
            break;
        }
		if( $condition == 'Compare' ){
			$compareAtts = $arrAtts;
			$compareAtts['from'] = $arrAtts['from'];
			$compareAtts['to'] = $arrAtts['from'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['from'] ) );
			}
			$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $condition, $compareAtts );
			$compareAtts['from'] = $arrAtts['to'];
			$compareAtts['to'] = $arrAtts['to'];
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['to'] ) );
			}
			//$arrVisitorsInfo2 = $this->objDatabase->getVisitorsInfo( $condition, $compareAtts );
		}else{
			$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $condition, $arrAtts );
		}
		$lastFrom=wsmGetDateByInterval('-1 days','Y-m-d', $arrAtts['from']); 
        $lastTo=wsmGetDateByInterval('-1 days','Y-m-d', $arrAtts['to']);  
		//$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $atts['condition'] ,$arrAtts);
		$header = '';
		$contentData = '';
		$seperator = '';
        $totaiVisitorsToday=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'] ,$arrAtts);
        $totaiVisitorsYesterday=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'] ,array('from'=>$lastFrom,'to'=>$lastTo));
		if( is_array($arrVisitorsInfo) && count($arrVisitorsInfo) ){
			$link_class = $class = 'active';
			foreach( $arrVisitorsInfo as $key => $data ){
				$unique_id = rand();
				$contentData .= '<div class="vistor_panel_data wsmTableContainer" id="vistor_panel_'.$unique_id.'">';
		        $contentData.='<table class="wsmTableStriped">';
				$title = '';
				switch( $key ){
					case 'OS':
						$title = 'Operation System';
						break;
					default:
						$title = $key;
						break;
				}
		        $contentData.='<tr><th>'.__('Rank','wp-stats-manager').'</th><th>'.$title.'</th><th>&nbsp;</th><th>'.__('References','wp-stats-manager').'</th></tr>';
     
                $counter = 1;
				
				$visitor_graph = array();
				if(count($data) > 0)
				{
					foreach( $data as $data_key => $row ){
						$arrAtts['rtype'] = $key;
						$arrAtts['id'] = $row['id'];
						if($atts['condition']=='Normal'){
							$currentDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByBroswerOS($atts['condition'],$arrAtts);
							 //$currentDayVisitors=$this->objDatabase->fnGetReferralTotalVisitorsCountByBroswerOS($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo, 'rtype' => $key ) );  
					   
							$prevDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByBroswerOS($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo,'id'=> $row['id'], 'rtype' => $key ));
							
							//$prevDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByOSBrowser($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo,'id'=> $row['id'], 'rtype' => $key )); 

							$arrow = $toolTip='';
							$class2 = "";
							$currentPer = $PrevPer= 0;
							if( $totaiVisitorsYesterday ){
								$PrevPer=($prevDayVisitors*100)/$totaiVisitorsYesterday;
							}
							if( $totaiVisitorsToday ){
								$currentPer=($row['total']*100)/$totaiVisitorsToday;
							}
							$diff=$currentPer-$PrevPer;
							$diffPer=$diff==0?100:$diff;
							$percent=number_format_i18n($diffPer,0);
							if($diff<0){
								$arrow='<div class="wsmArrowDown"></div>'; 
								$class2="wsmColorRed";                         
							}
							if($diff>0){
								$arrow='<div class="wsmArrowUp"></div>';
								$class2="wsmColorGreen";
								$percent='+'.$percent;
							}                    
							$format = 'd F Y';
							$toolTip='<span class="wsmTooltipText">'.$row['name'].'<font class="wsmColorYellow">'.wsmGetDateByTimeStamp($format,strtotime($arrAtts['from'])).'&nbsp;/&nbsp;'.wsmGetDateByTimeStamp($format,strtotime($lastFrom)).'</font><font>'.__('Visitors Performance','wp-stats-manager').':&nbsp;<b class="'.$class2.'">'.$percent.'%</b></font></span>';
						}
				
						$compareValue = '';
						if( $condition == 'Compare' ){
							$compareAtts['rtype'] = $key;
							$compareAtts['id'] = $row['id'];
							$count = $this->objDatabase->fnGetReferralTotalVisitorsCountByOSBrowser( $condition, $compareAtts );
							//$compareValue = '<div><span>'.( isset($arrVisitorsInfo2[$key][$data_key]['total']) && $arrVisitorsInfo2[$key][$data_key]['total'] ? $arrVisitorsInfo2[$key][$data_key]['total'] : 0).'</span></div>';	
							$compareValue = '<div><span>'.number_format($count).'</span></div>';	
						}
						
						$arrAtts['action'] = 'getReferralOSDetails';
					
						$contentData.='<tr id="wsmRowParent_'.$counter.'" class="wsmReferralRow"><td><div class="wsmTooltip">'.$arrow.'&nbsp;'.$counter.$toolTip.'</div></td><td><a href="#" data-referrak_param=\''.(json_encode($arrAtts)).'\' class="linkReferralVisitorDetails" title="'.__('Click here for details.','wp-stats-manager').'">'.$row['name'].'</a></td><td></td><td>'.number_format($row['total']).$compareValue.'</td></tr>';
	 
						$counter++;
						if( $counter <= 8 ){
							$visitor_graph[] = array( $row['name'].' ('.$row['total'].')', $row['total'] );
						}
					}
				}
				else
				{
					$contentData .="<tr><td colspan='3' class='wsmCenter'>".__('Data / Statistics are not available.','wp-stats-manager')."</td></tr>";
				}
				$contentData.='</table>';
				$contentData .= '</div>';
				
				$header .= $seperator.'<a class="'.$link_class.'" data-graph=\''.json_encode($visitor_graph).'\' href="#vistor_panel_'.$unique_id.'">'.$key.'</a>';
				$link_class = '';
				$seperator = ' | ';
				
			}
		}
		
		
		if( current_user_can('edit_others_pages') ){
			echo '<div class="stats_submenu">'. $header.'</div>'.$contentData;
		}else{
			return $contentData;
		}
		
	}
	
	function wsm_showEachVisitorsDetailGraph($atts)
	{
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
		$arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
        $atts = shortcode_atts( array(
            'id' => '_topRefferStatsList',
            'display' => '',
            'title' => __('Top Referrer Sites','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
			'searchengine' => ''
        ), $atts, WSM_PREFIX.'_showTopReferrerList');
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;        
      	$arrAtts['searchengine'] = $atts['searchengine']; 
		$condition=$atts['condition'];
		$arrVisitorsInfo2 = array();
		ob_start();
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'];
                    $arrAtts['to']=$atts['second'];
                }
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01'));
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
            break;
        }
		if( $condition == 'Compare' ){
			$compareAtts = $arrAtts;
			$compareAtts['from'] = $arrAtts['from'];
			$compareAtts['to'] = $arrAtts['from'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['from'] ) );
			}
			$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $condition, $compareAtts );
			$compareAtts['from'] = $arrAtts['to'];
			$compareAtts['to'] = $arrAtts['to'];
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['to'] ) );
			}
			//$arrVisitorsInfo2 = $this->objDatabase->getVisitorsInfo( $condition, $compareAtts );
		}else{
			$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $condition, $arrAtts );
		}
		
		
		$lastFrom=wsmGetDateByInterval('-1 days','Y-m-d', $arrAtts['from']); 
        $lastTo=wsmGetDateByInterval('-1 days','Y-m-d', $arrAtts['to']);  
		//$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $atts['condition'] ,$arrAtts);
		
		
		
        $totaiVisitorsToday=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'] ,$arrAtts);
        $totaiVisitorsYesterday=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'] ,array('from'=>$lastFrom,'to'=>$lastTo));
		if( is_array($arrVisitorsInfo) && count($arrVisitorsInfo) ){
			$link_class = $class = 'active';
			
			if(strtolower($atts['display'])=="os")
				$finalArrVisitorsInfo['OS'] = $arrVisitorsInfo['OS'];
			else if(strtolower($atts['display'])=="browser")
				$finalArrVisitorsInfo['Browser'] = $arrVisitorsInfo['Browser'];
			else if(strtolower($atts['display'])=="resolution")
				$finalArrVisitorsInfo['Screen Resolution'] = $arrVisitorsInfo['Screen Resolution'];
			else
				$finalArrVisitorsInfo = $arrVisitorsInfo;
			//print_r($finalArrVisitorsInfo);
			
			foreach( $finalArrVisitorsInfo as $key => $data ){
				$unique_id = rand();
				
				$title = '';
				switch( $key ){
					case 'OS':
						$title = 'Operation System';
						break;
					default:
						$title = $key;
						break;
				}
		      
                $counter = 1;
				
				$visitor_graph = array();
				foreach( $data as $data_key => $row ){
					$arrAtts['rtype'] = $key;
					$arrAtts['id'] = $row['id'];
                    if($atts['condition']=='Normal'){
                        $currentDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByBroswerOS($atts['condition'],$arrAtts);
					     //$currentDayVisitors=$this->objDatabase->fnGetReferralTotalVisitorsCountByBroswerOS($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo, 'rtype' => $key ) );  
                   
				   	    $prevDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByBroswerOS($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo,'id'=> $row['id'], 'rtype' => $key ));
					    
					    //$prevDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByOSBrowser($atts['condition'],array('from'=>$lastFrom,'to'=>$lastTo,'id'=> $row['id'], 'rtype' => $key )); 

	                    $arrow = $toolTip='';
                        $class2 = "";
					    $currentPer = $PrevPer= 0;
					    if( $totaiVisitorsYesterday ){
	                        $PrevPer=($prevDayVisitors*100)/$totaiVisitorsYesterday;
					    }
					    if( $totaiVisitorsToday ){
						    $currentPer=($row['total']*100)/$totaiVisitorsToday;
					    }
	                    $diff=$currentPer-$PrevPer;
	                    $diffPer=$diff==0?100:$diff;
	                    $percent=number_format_i18n($diffPer,0);
	                    if($diff<0){
	                        $arrow='<div class="wsmArrowDown"></div>'; 
	                        $class2="wsmColorRed";                         
	                    }
	                    if($diff>0){
	                        $arrow='<div class="wsmArrowUp"></div>';
	                        $class2="wsmColorGreen";
	                        $percent='+'.$percent;
	                    }                    
					    $format = 'd F Y';
	                    $toolTip='<span class="wsmTooltipText">'.$row['name'].'<font class="wsmColorYellow">'.wsmGetDateByTimeStamp($format,strtotime($arrAtts['from'])).'&nbsp;/&nbsp;'.wsmGetDateByTimeStamp($format,strtotime($lastFrom)).'</font><font>'.__('Visitors Performance','wp-stats-manager').':&nbsp;<b class="'.$class2.'">'.$percent.'%</b></font></span>';
                    }
			
					$compareValue = '';
					if( $condition == 'Compare' ){
						$compareAtts['rtype'] = $key;
						$compareAtts['id'] = $row['id'];
						$count = $this->objDatabase->fnGetReferralTotalVisitorsCountByOSBrowser( $condition, $compareAtts );
						//$compareValue = '<div><span>'.( isset($arrVisitorsInfo2[$key][$data_key]['total']) && $arrVisitorsInfo2[$key][$data_key]['total'] ? $arrVisitorsInfo2[$key][$data_key]['total'] : 0).'</span></div>';	
						$compareValue = '<div><span>'.number_format($count).'</span></div>';	
					}
					
					$arrAtts['action'] = 'getReferralOSDetails';
				
					
 
		            $counter++;
					if( $counter <= 8 ){
						$visitor_graph[] = array( $row['name'].' ('.$row['total'].')', $row['total'] );
					}
				}
				
				echo "<h5>".$title."</h5>";
				echo '<div id="vistor_panel_'.$unique_id.'" class="each_visior_info_graph" data-graph=\''.json_encode($visitor_graph).'\'>'. $header.'</div>';				
			}
		}
		
		$data = ob_get_contents();
		ob_clean();
		return $data;
		
	}
	function wsm_showVisitorsDetailGraph( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
		
        $atts = shortcode_atts( array(
            'id' => '_topRefferStatsList',
            'title' => __('Top Referrer Sites','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
			'searchengine' => ''
        ), $atts, WSM_PREFIX.'_showTopReferrerList');
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;        
      	$arrAtts['searchengine'] = $atts['searchengine']; 
		$condition=$atts['condition'];
        
        switch($atts['type']){
            case 'Hourly':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'];
                    $arrAtts['to']=$atts['to']; 
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['date']=$atts['first'];
                }
            break;
            case 'Daily':
                if($atts['condition']=='Normal'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
                if($atts['condition']=='Range'){
                    $arrAtts['from']=$atts['from'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($atts['to'].'-01'));
                }
                if($atts['condition']=='Compare'){
                    $arrAtts['from']=$atts['first'].'-01';
                    $arrAtts['to']=wsmGetDateByTimeStamp('Y-m-t',strtotime($arrAtts['from']));
                }
            break;
        }
		//$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $atts['condition'] ,$arrAtts);
		ob_start();
		echo '<div id="visitor_info_graph"><p class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</p></div>';
		$data = ob_get_contents();
		ob_clean();
		return $data;
		
	}
	
	function wsm_showStatKeywords($atts){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;
		if( current_user_can('edit_others_pages') ){
			$arrAtts['adminURL']=admin_url('admin.php?page='.WSM_PREFIX.'_trafficsrc').'&subPage=SearchKeywords';//'&'.http_build_query($arrRequestData);
		}
		
		$totalSearchKeywords = $this->objDatabase->fnGetReferralKeywords( $arrAtts );
		$html = '';
		
        if(count($totalSearchKeywords['data'])>0){
		   	$html .= '<div class="wsmTableContainer wsmSearchKeywords">';
			$html .= '<table class="wsmTableStriped">';
            			
            foreach($totalSearchKeywords['data'] as $searchKeyword ){                 
				$keyword = $searchKeyword['keyword'];
				if( !$keyword || $keyword == '-' ){
					$keyword = wsmGetSearchKeywords( 'http://'. $searchKeyword['url'] );
				}
				if( !$keyword ){
					$keyword = '-';
				}
                $html.='<tr class="wsmReferralRow">';
				$html.= sprintf('<td><div>%s<div class="pull-right">%s - <span>%s</span></div></div><a href="%s" target="_blank">%s</a></td>', $keyword, date('Y/m/d', strtotime( $searchKeyword['serverTime'] )), $searchKeyword['ipAddress'], $searchKeyword['protocol'].$searchKeyword['url'], $searchKeyword['url']);
                $html.='</tr>';               
                $counter++;
            }
            if(isset($totalSearchKeywords['pagination'])){
                $html.='<tr><td colspan="4">';
                $html.='<div class="wsmPageContainer">';
                $html.=$totalSearchKeywords['pagination'];
                $html.='</div>';
                $html.='</tr>';
            }
			$html .= '</table>';
			$html .= '</div>';
        }
		return $html;
	}
	
	function wsm_showGeoLocationGraph( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();

        global $wsmAdminJavaScript;
        $atts = shortcode_atts( array(
            'id' => '__geoLocation',
            'title' => __('Geo Location','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
        ), $atts, WSM_PREFIX.'_showTopReferrerList');		
		
		$condition=$atts['condition'];
        $arrAtts = array();
		
        switch($atts['type']){
            case 'Hourly':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['from'];
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['to'];
					break;
					case 'Compare':
						$arrAtts['from']=$atts['first'];
						$arrAtts['to']= $atts['second'];
					break;
				}
            break;
            case 'Daily':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['from'].'-01') );
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['to'].'-01') );
					break;
					case 'Compare':
						$arrAtts['from']= $atts['first'].'-01';
						$arrAtts['to']= $atts['second'].'-01';
					break;
				}
            break;
        }
		$arrAtts['limit'] = 8;
		
		if( isset( $_GET['location'] ) ){
			$arrAtts['location'] = 'city';
			$arrAtts['city'] = 0;
			$arrAtts['compare'] = '!=';
		}else{
			$arrAtts['countryId'] = 0;
			$arrAtts['compare'] = '>';
		}
		
		if( $condition == 'Compare' ){
			$compareAtts = $arrAtts;
			$compareAtts['from'] = $arrAtts['from'];
			$compareAtts['to'] = $arrAtts['from'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['from'] ) );
			}
			$totalGeoLocationDetails = $this->objDatabase->getGeoLocationInfo( $condition, $compareAtts );
			$compareAtts['from'] = $arrAtts['to'];
			$compareAtts['to'] = $arrAtts['to'];
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['to'] ) );
			}
			$totalGeoLocationDetails2 = $this->objDatabase->getGeoLocationInfo( $condition, $compareAtts );
		}else{
			$totalGeoLocationDetails = $this->objDatabase->getGeoLocationInfo( $condition, $arrAtts );
		}
		ob_start();
		
		if($totalGeoLocationDetails)
		{
			foreach( $totalGeoLocationDetails as $key => $row ){
				$total = $row['total_visitors'];
				if( $condition == 'Compare' ){
					$total += $totalGeoLocationDetails2[$key]['total_visitors'];
				}
				$visitor_graph[] = array( $row['name']. ' ('.$total.')', number_format( $total ) );
			}
			echo '<div id="country_visitor_info_graph" data-graph=\''.json_encode( $visitor_graph ).'\'></div>';
		}
		else
		{
			$visitor_graph = null;
			echo '<div id="country_visitor_info_graph" data-graph=\''.json_encode( $visitor_graph ).'\'><p class="wsmCenter">'.__('Data / Statistics are not available.','wp-stats-manager').'</p></div>';
		}
		
		
		$data = ob_get_contents();
		ob_clean();
		return $data;
	}
	
	function wsm_showGeoLocationDetails( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
		$totalGeoLocationDetails2 = array();
        global $wsmAdminJavaScript;
        $atts = shortcode_atts( array(
            'id' => '__geoLocation',
            'title' => __('Geo Location','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
        ), $atts, WSM_PREFIX.'_showTopReferrerList');		
		
		$condition=$atts['condition'];
		$locationType = isset( $_GET['location'] ) ? $_GET['location'] : '';

        $arrAtts = array();
		
		if( isset( $_GET['location'] ) ){
			$arrAtts['location'] = 'city';
			$arrAtts['city'] = 0;
			$arrAtts['compare'] = '!=';
		}else{
			$arrAtts['countryId'] = 0;
			$arrAtts['compare'] = '>';
		}
        switch($atts['type']){
            case 'Hourly':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['from'];
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['to'];
					break;
					case 'Compare':
						$arrAtts['from']=$atts['first'];
						$arrAtts['to']= $atts['second'];
					break;
				}
            break;
            case 'Daily':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['from'].'-01') );
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['to'].'-01') );
					break;
					case 'Compare':
						$arrAtts['from']= $atts['first'].'-01';
						$arrAtts['to']= $atts['second'].'-01';
					break;
				}
            break;
        }
		if( $condition == 'Compare' ){
			$compareAtts = $arrAtts;
			$compareAtts['from'] = $arrAtts['from'];
			$compareAtts['to'] = $arrAtts['from'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['from'] ) );
			}
			$totalGeoLocationDetails = $this->objDatabase->getGeoLocationInfo( $condition, $compareAtts );
			$compareAtts['from'] = $arrAtts['to'];
			$compareAtts['to'] = $arrAtts['to'];
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['to'] ) );
			}
			$totalGeoLocationDetails2 = $this->objDatabase->getGeoLocationInfo( $condition, $compareAtts );
		}else{
			$totalGeoLocationDetails = $this->objDatabase->getGeoLocationInfo( $condition, $arrAtts );
		}

		
		$graphAttr = $preArrAtts = $arrAtts;
		$preArrAtts['from'] = $lastFrom;
		$preArrAtts['to'] = $preArrAtts['from']; 					
		
		$totaiVisitorsToday = $this->objDatabase->fnGetReferralTotalVisitorsCountByCountry( $arrAtts );
		
		$startFrom = $arrAtts['from'];
		
		$lastFrom  = $preArrAtts['from'] = date('Y-m-d', strtotime($preArrAtts['from'] .' -1 day'));
		$preArrAtts['to'] = $preArrAtts['from']; 
		
		$graphAttr['from'] = date('Y-m-d', strtotime($graphAttr['from'] .' -30 day'));
		$graphAttr['action'] = 'getDateWiseLocationDetail';
			
		$totaiVisitorsYesterday = $this->objDatabase->fnGetReferralTotalVisitorsCountByCountry( $preArrAtts );
		$contentData = '';
		
		$contentData .= '<div class="wsmTableContainer wsmLocationList wsmReferenceList vistor_panel_data">';
		$contentData .= '<div class="geo_stats_submenu"><a class="'.($locationType == '' ? 'active' : '').'" href="admin.php?page='.WSM_PREFIX.'_visitors&subPage=GeoLocation">Country</a> | <a class="'.($locationType == 'city' ? 'active' : '').'" href="admin.php?page='.WSM_PREFIX.'_visitors&subPage=GeoLocation&location=city">City</a></div>';
        $contentData.='<table class="wsmTableStriped">';
 	   		$contentData.='<tr><th>'.__('Rank','wp-stats-manager').'</th><th>'.( isset($arrAtts['location']) ? __('City','wp-stats-manager') : __('Country','wp-stats-manager') ) . '</th><th class="width_100 align_center">'. __('New visitors','wp-stats-manager') .'</th><th class="width_100 align_center">' .__('Visitors','wp-stats-manager'). '</th><th class="width_100 align_center">' .__('Page viewed','wp-stats-manager'). '</th><th>'.__('Ppv','wp-stats-manager').'</th></tr>';

        $counter = 1;
   	    
		if(count($totalGeoLocationDetails) > 0)
		{
			$visitor_graph = array();
			foreach( $totalGeoLocationDetails as $key => $row ){
				$arrow = '';
				$locationGraph = '';
				if($condition=='Normal'){

					$arrAtts['compare'] = '=';
					
					$preArrAtts['compare'] = '=';
					
					if( isset( $_GET['location'] ) ){
						$arrAtts['location'] = 'city';
						$arrAtts['city'] = $row['name'];
						$preArrAtts['city'] = $row['name'];
					}else{
						$arrAtts['countryId'] = $row['countryId'];
						$preArrAtts['countryId'] = $row['countryId'];
					}
					
					$currentDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByCountry( $arrAtts );
					$prevDayVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCountByCountry( $preArrAtts );
					
					$arrow = $toolTip='';
					$class2 = "";
					$currentPer = $PrevPer= 0;
					if( $totaiVisitorsYesterday ){
						$PrevPer=($prevDayVisitors*100)/$totaiVisitorsYesterday;
					}
					if( $totaiVisitorsToday ){
						$currentPer=($row['total_visitors']*100)/$totaiVisitorsToday;
					}
					$diff=$currentPer-$PrevPer;
					$diffPer=$diff==0?100:$diff;
					$percent=number_format_i18n($diffPer,0);
					if($diff<0){
						$arrow='<div class="wsmArrowDown"></div>'; 
						$class2="wsmColorRed";                         
					}
					if($diff>0){
						$arrow='<div class="wsmArrowUp"></div>';
						$class2="wsmColorGreen";
						$percent='+'.$percent;
					}                    
					$format = 'd F Y';

					$user_per = number_format($currentPer, 2, '.', '');
					
					$locationGraph = '<div class="single_location_chart"><div class="bar_graph" style="width:'.($user_per * 3).'px"></div><span>'.$user_per.'%</span></div>';
					
					$toolTip='<span class="wsmTooltipText">'.$row['name'].'<font class="wsmColorYellow">'.wsmGetDateByTimeStamp($format,strtotime($startFrom)).'&nbsp;/&nbsp;'.wsmGetDateByTimeStamp($format,strtotime($lastFrom)).'</font><font>'.__('Visitors Performance','wp-stats-manager').':&nbsp;<b class="'.$class2.'">'.$percent.'%</b></font></span>';
				}
				
				$compare_total_unique_visitors = $compare_total_visitors = $compare_total_page_views = $compare_ppv = '';
				if( $condition == 'Compare' ){
					$compare_total_unique_visitors = '<div><span>'.( isset($totalGeoLocationDetails2[$key]['total_unique_visitors']) && $totalGeoLocationDetails2[$key]['total_unique_visitors'] ? $totalGeoLocationDetails2[$key]['total_unique_visitors'] : 0).'</span></div>';	
					$compare_total_visitors = '<div><span>'.( isset($totalGeoLocationDetails2[$key]['total_visitors']) && $totalGeoLocationDetails2[$key]['total_visitors'] ? $totalGeoLocationDetails2[$key]['total_visitors'] : 0).'</span></div>';	
					$compare_total_page_views = '<div><span>'.( isset($totalGeoLocationDetails2[$key]['total_page_views']) && $totalGeoLocationDetails2[$key]['total_page_views'] ? $totalGeoLocationDetails2[$key]['total_page_views'] : 0).'</span></div>';	

					if( isset($totalGeoLocationDetails2[$key]['total_page_views']) && $totalGeoLocationDetails2[$key]['total_page_views'] && isset($totalGeoLocationDetails2[$key]['total_visitors']) && $totalGeoLocationDetails2[$key]['total_visitors'] ){
						$compare_ppv = $totalGeoLocationDetails2[$key]['total_page_views'] / $totalGeoLocationDetails2[$key]['total_visitors'];
						$compare_ppv = '<div><span>'. number_format($compare_ppv, 1, ".", "" ) .'</span></div>';
					}
					
				}
				if( isset( $row['countryId'] ) ){
					$graphAttr['countryId'] = $row['countryId']; 
				}
				if( isset( $_GET['location'] ) ){
					$graphAttr['compare'] = '=';
					$graphAttr['city'] = $row['name']; 
				}
				$ppv = $row['total_page_views'] / $row['total_visitors'];
				$contentData.='<tr id="wsmRowParent_'.$counter.'" class="wsmReferralRow">
					<td><div class="wsmTooltip">'.$arrow.'&nbsp;'.$counter.$toolTip.'</div></td>
					<td><a href="#" data-referrak_param=\''.(json_encode($graphAttr)).'\' class="linkReferralLocationDetails" title="Click here for details."><img src="'.WSM_URL.'images/ICO_1px.gif" class="flag flag-'.strtolower($row['alpha2Code']).'" alt="'.$row['name'].'" title="'.$row['name'].'"/>&nbsp;'  .$row['name'].'</a>
					</td>
					<td class="width_100 align_center">'. number_format($row['total_unique_visitors']).$compare_total_unique_visitors. '</td>
					<td class="width_100 align_center">'. number_format($row['total_visitors']).$compare_total_visitors. '</td>
					<td class="width_100 align_center">' .number_format($row['total_page_views']).$compare_total_page_views. $locationGraph.'</td>
					<td>'. number_format($ppv, 1, ".", "" ).$compare_ppv .'</td></tr>';

				$counter++;
			}
		}
		else
		{
			$contentData .="<tr><td colspan='6' class='wsmCenter'>".__('Data / Statistics are not available.','wp-stats-manager')."</td></tr>";
		}	
		$contentData.='</table>';
		$contentData .= '</div>';


		return $contentData;		
	}
	
	function wsm_showContentByURL( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
		$totalGeoLocationDetails2 = array();
		
        $atts = shortcode_atts( array(
            'id' => '__contentByURL',
            'title' => __('Content by URL','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
        ), $atts, WSM_PREFIX.'_showContentByURL');		
		
		$condition=$atts['condition'];

        $arrAtts = array();
		
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;
		if( current_user_can('edit_others_pages') ){
			$arrAtts['adminURL']=admin_url('admin.php?page='.WSM_PREFIX.'_content');//'&'.http_build_query($arrRequestData);
		}
        switch($atts['type']){
            case 'Hourly':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['from'];
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['to'];
					break;
					case 'Compare':
						$arrAtts['from']=$atts['first'];
						$arrAtts['to']= $atts['second'];
					break;
				}
            break;
            case 'Daily':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['from'].'-01') );
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['to'].'-01') );
					break;
					case 'Compare':
						$arrAtts['from']= $atts['first'].'-01';
						$arrAtts['to']= $atts['second'].'-01';
					break;
				}
            break;
        }
		$arrAtts['date'] = $arrAtts['from'];
		//$totalHits = $this->objDatabase->fnGetTotalHitsCount( $arrAtts );
        $totalVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCount($condition, $arrAtts);
        $totalFirstTimeVisitors = $this->objDatabase->fnGetFirstTimeVisitorCount($condition, $arrAtts);
			

		if( $condition == 'Compare' ){
			$compareAtts = $arrAtts;
			$compareAtts['from'] = $arrAtts['from'];
			$compareAtts['to'] = $arrAtts['from'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['from'] ) );
			}
			$urlStatsResult = $this->objDatabase->getContentByURLStats( $compareAtts, 12 );
			$compareAtts['from'] = $arrAtts['to'];
			$compareAtts['to'] = $arrAtts['to'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['to'] ) );
			}
		}else{
			$urlStatsResult = $this->objDatabase->getContentByURLStats( $arrAtts, 12 );
		}
		//print_r($arrAtts);
		$preArrAtts = $arrAtts;
		$totaiVisitorsToday = $totaiVisitorsYesterday = 0;

		$totaiVisitorsToday = $this->objDatabase->getContentByURLTotalRecords( $arrAtts );
		
        if($condition=='Normal'){
		
			$startFrom = $arrAtts['from'];
		
			$lastFrom  = $preArrAtts['from'] = date('Y-m-d', strtotime($preArrAtts['from'] .' -1 day'));
			$preArrAtts['to'] = $preArrAtts['from']; 
		
			$totaiVisitorsYesterday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
			
		}
		if($condition=='Compare'){

			$preArrAtts['to'] =$arrAtts['from'];
			$totaiVisitorsToday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
			
			$startFrom = $arrAtts['to'];
		
			$lastFrom  = $preArrAtts['to'];
			$preArrAtts['from'] = $arrAtts['from']; 
		
			$totaiVisitorsYesterday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
		}
		$graphAttr['to'] = $arrAtts['from'];
		$graphAttr['from'] = date('Y-m-d', strtotime($arrAtts['from'] .' -30 day'));
		$graphAttr['action'] = 'getContentUrlDayView';
		$html = '';
		$site_url =  str_replace( array(  'http://www.', 'https://wwww.', 'http://', 'https://','www.'), array(), site_url() );
		
        if( is_array( $urlStatsResult ) && isset( $urlStatsResult['data'] ) && count($urlStatsResult['data'])>0){
		   	$html .= '<ul class="traffic_by_url_list_panel">';
			$counter = 1;					
            foreach($urlStatsResult['data'] as $row ){                 
				$arrow = $toolTip = '';
				
				$graphAttr['id'] = $row['id'];
				$arrAtts['id'] = $row['id'];
				$preArrAtts['id'] = $row['id'];
				
				if($condition=='Compare'){
					$temp = $arrAtts;
					$temp['to'] = $arrAtts['from'];
					$urlVisitors = $this->objDatabase->getContentByURLVisitors( $temp ); 
				}else{
					$urlVisitors = $this->objDatabase->getContentByURLVisitors( $arrAtts ); 
				}
				$url = str_replace( $site_url, '', $row['url'] );

				$hitsPer = $totalVisitorsPer = $totalfirstTimeVisitorsPer = 0;
				$hitsPer = number_format ( ( $row['hits'] * 100 ) / ( $totaiVisitorsToday * 10 ) , 2 );
				if( $urlVisitors['visitors'] && $totalVisitors ){
					$totalVisitorsPer = number_format ( ( $urlVisitors['visitors'] * 100 ) / ( $totalVisitors * 10), 2 );
				}
				if( $urlVisitors['newVisitors'] && $totalFirstTimeVisitors && $totalFirstTimeVisitors > 0 ){
					$totalfirstTimeVisitorsPer = number_format( ( $urlVisitors['newVisitors'] * 100 ) / ($totalFirstTimeVisitors * 10) , 2) ; 
				}
				
				$html .= '<li>'.$url.' <div class="hits_panel">(<span title="Hits">'.$hitsPer.'%</span> - <span title="Total visitors">'.$totalVisitorsPer.'%</span> - <span title="Total new visitors">'.$totalfirstTimeVisitorsPer.'%</span>)</div>'.'</li>';
            }
			$html .= '</ul>';
        }
		return $html;
	}

	function wsm_showContentByURLStats( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
		$totalGeoLocationDetails2 = array();
		
        $atts = shortcode_atts( array(
            'id' => '__contentByURL',
            'title' => __('Content by URL','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
			'content' => '',
			'limit' => 50,
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
        ), $atts, WSM_PREFIX.'_showContentByURL');		
		
		$condition=$atts['condition'];
		if( $atts['content'] == 'byTitle' ){
			$_GET['subPage'] = 'byTitle'; 
		}
        $arrAtts = array();
		
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;
		if( isset( $_REQUEST['search'] ) && $_REQUEST['search'] ){
			$arrAtts['search'] = $_REQUEST['search'];
		}
		if( current_user_can('edit_others_pages') ){
			$admin_url = admin_url('admin.php?page='.WSM_PREFIX.'_content');
			if( isset( $_GET['subPage'] ) && $_GET['subPage'] == 'byTitle' ){
				$admin_url .= '&subPage='.$_GET['subPage'];
			}
			$arrAtts['adminURL'] = $admin_url.'&'.http_build_query($arrRequestData);
		}
        switch($atts['type']){
            case 'Hourly':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['from'];
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['to'];
					break;
					case 'Compare':
						$arrAtts['from']=$atts['first'];
						$arrAtts['to']= $atts['second'];
					break;
				}
            break;
            case 'Daily':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['from'].'-01') );
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['to'].'-01') );
					break;
					case 'Compare':
						$arrAtts['from']= $atts['first'].'-01';
						$arrAtts['to']= $atts['second'].'-01';
					break;
				}
            break;
        }
		$arrAtts['date'] = $arrAtts['from'];
		//$totalHits = $this->objDatabase->fnGetTotalHitsCount( $arrAtts );
        $totalVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCount($condition, $arrAtts);
        $totalFirstTimeVisitors = $this->objDatabase->fnGetFirstTimeVisitorCount($condition, $arrAtts);
			

		if( $condition == 'Compare' ){
			$compareAtts = $arrAtts;
			$compareAtts['from'] = $arrAtts['from'];
			$compareAtts['to'] = $arrAtts['from'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['from'] ) );
			}
			$urlStatsResult = $this->objDatabase->getContentByURLStats( $compareAtts );
			$compareAtts['from'] = $arrAtts['to'];
			$compareAtts['to'] = $arrAtts['to'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['to'] ) );
			}
		}else{
			$urlStatsResult = $this->objDatabase->getContentByURLStats( $arrAtts, $atts['limit'] );
		}
		//print_r($arrAtts);
		$preArrAtts = $arrAtts;
		$totaiVisitorsToday = $totaiVisitorsYesterday = 0;

		$totaiVisitorsToday = $this->objDatabase->getContentByURLTotalRecords( $arrAtts );
		
        if($condition=='Normal'){
		
			$startFrom = $arrAtts['from'];
		
			$lastFrom  = $preArrAtts['from'] = date('Y-m-d', strtotime($preArrAtts['from'] .' -1 day'));
			$preArrAtts['to'] = $preArrAtts['from']; 
		
			$totaiVisitorsYesterday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
			
		}
		if($condition=='Compare'){

			$preArrAtts['to'] =$arrAtts['from'];
			$totaiVisitorsToday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
			
			$startFrom = $arrAtts['to'];
		
			$lastFrom  = $preArrAtts['to'];
			$preArrAtts['from'] = $arrAtts['from']; 
		
			$totaiVisitorsYesterday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
		}
		$graphAttr['to'] = $arrAtts['from'];
		$graphAttr['from'] = date('Y-m-d', strtotime($arrAtts['from'] .' -30 day'));
		$graphAttr['action'] = 'getContentUrlDayView';
		$html = '';
		$site_url =  str_replace( array(  'http://www.', 'https://wwww.', 'http://', 'https://','www.'), array(), site_url() );
		
        if( is_array( $urlStatsResult ) && isset( $urlStatsResult['data'] ) && count($urlStatsResult['data'])>0){
			
			$order_query = $arrAtts['adminURL'].'&orderby=hits&order=asc';
			$order_class = 'dashicons-arrow-down';
			if( isset( $_GET['order'] ) && $_GET['order'] == 'asc' ){
				$order_query = $arrAtts['adminURL'].'&orderby=hits&order=desc';
				$order_class = 'dashicons-arrow-up';
			}
			$urlTitle = __('Url','wp-stats-manager');
			if( isset( $_GET['subPage'] ) && $_GET['subPage'] == 'byTitle' ){
				$urlTitle = __('Title','wp-stats-manager');
			}	
		   	$html .= '<div class="wsmTableContainer wsmContentURLStats">';
			$html .= '<div class="title_search_panel">';
			$html .= '<input type="text" name="search" value="'.( isset( $_REQUEST['search'] ) ? $_REQUEST['search'] : '' ).'" />';
			$html .= '<span class="dashicons dashicons-search"></span>';
			$html .= '</div>';
			$html .= '<table class="wsmTableStriped">';
        	$html .= '<tr><th>'.__('Rank','wp-stats-manager').'</th>
						<th>'. $urlTitle . '</th>
						<th class="width_100 align_center"><a href="'.$order_query.'">'. __('Hits','wp-stats-manager') .' <span class="dashicons '.$order_class.'"></span></a></th>
						<th class="width_100 align_center">' .__('Visitors Entry','wp-stats-manager'). '</th>
						<th class="width_100 align_center">' .__('New Visitors Entry','wp-stats-manager'). '</th></tr>';
			$counter = 1;			
					
            foreach($urlStatsResult['data'] as $row ){                 
				$arrow = $toolTip = '';
				
				$graphAttr['id'] = $row['id'];
				$arrAtts['id'] = $row['id'];
				$preArrAtts['id'] = $row['id'];
				
				if($condition=='Compare'){
					$temp = $arrAtts;
					$temp['to'] = $arrAtts['from'];
					$urlVisitors = $this->objDatabase->getContentByURLVisitors( $temp ); 
				}else{
					$urlVisitors = $this->objDatabase->getContentByURLVisitors( $arrAtts ); 
				}
				$url = str_replace( $site_url, '', $row['url'] );
				
				$name = $url;
				if( isset( $_GET['subPage'] ) && $_GET['subPage'] == 'byTitle' ){
					$post_id = url_to_postid( $row['protocol'].$row['url'] );
					if( $post_id ){
						$name = get_the_title( $post_id );
					}else{
						$name = wsmArchiveTitleFromURL( $url );
					}
					if( $url == $name  && !empty( $row['title'] ) ){
						$name = $row['title'];
					}
				}
				if($condition=='Normal'){
				
	                $class2 = "";
				    $currentPer = $PrevPer= 0;

					$currentDayVisitors = $this->objDatabase->getContentByURLTotalRecords( $arrAtts );
					$prevDayVisitors = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
					
				    if( $totaiVisitorsYesterday ){
	                    $PrevPer=($prevDayVisitors*100)/$totaiVisitorsYesterday;
				    }
				    if( $totaiVisitorsToday ){
					    $currentPer=( $currentDayVisitors*100)/$totaiVisitorsToday;
				    }
					
	                $diff=$currentPer-$PrevPer;
	                $diffPer=$diff==0?100:$diff;
	                $percent=number_format_i18n($diffPer,0);
	                if($diff<0){
	                    $arrow='<div class="wsmArrowDown"></div>'; 
	                    $class2="wsmColorRed";                         
	                }
	                if($diff>0){
	                    $arrow='<div class="wsmArrowUp"></div>';
	                    $class2="wsmColorGreen";
	                    $percent='+'.$percent;
	                }                    
				    $format = 'd F Y';

					$user_per = number_format($currentPer, 2, '.', '');
				
	                $toolTip='<span class="wsmTooltipText">'. $name .'<font class="wsmColorYellow">'.wsmGetDateByTimeStamp($format,strtotime($startFrom)).'&nbsp;/&nbsp;'.wsmGetDateByTimeStamp($format,strtotime($lastFrom)).'</font><font>'.__('Visitors Performance','wp-stats-manager').':&nbsp;<b class="'.$class2.'">'.$percent.'%</b></font></span>';
				}
					
			
				$compare_total_unique_visitors = $compare_total_visitors = $compare_total_hits = '';
				
				if( $condition == 'Compare' ){
					$compareAtts['id'] = $row['id'];
					$hitsPer = $totalVisitorsPer = $totalfirstTimeVisitorsPer = 0;
					
					$urlStatsResult2 = $this->objDatabase->getContentByURLStats( $compareAtts );
					
					$hits = isset( $urlStatsResult2['data'][0]['hits'] ) ? $urlStatsResult2['data'][0]['hits'] : 0;
					
					$urlStatsResult2 = $this->objDatabase->getContentByURLVisitors( $compareAtts );
					
					$visitors = isset($urlStatsResult2['visitors']) ? $urlStatsResult2['visitors'] : 0;
					$newVisitors = isset($urlStatsResult2['newVisitors']) ? $urlStatsResult2['newVisitors'] : 0;
					
					$new_value = array( $hits, $visitors, $newVisitors );
					sort($new_value);
					$hits = $new_value[2];
					$visitors = $new_value[1];
					$newVisitors = $new_value[0];
						
					if( $newVisitors && $totaiVisitorsYesterday ){
						$totalfirstTimeVisitorsPer = number_format( ( $newVisitors * 100 ) / ($totaiVisitorsYesterday * 10) , 2) ; 
					}
					
					if( $hits && $totaiVisitorsYesterday ){
						$hitsPer = number_format ( ( $hits * 100 ) / ( $totaiVisitorsYesterday * 10 ) , 2 );
					}

					if( $visitors && $totaiVisitorsYesterday ){
						$totalVisitorsPer = number_format ( ( $visitors * 100 ) / ( $totaiVisitorsYesterday * 10), 2 );
					}
					
					$compare_total_hits = '<div class="compare_second_result"><span>'.$hits.' <small>( '.$hitsPer.' %)</small></span></div>';	
					$compare_total_unique_visitors = '<div class="compare_second_result"><span>'.$newVisitors.' <small>( '.$totalfirstTimeVisitorsPer.' %)</small></span></div>';	
					$compare_total_visitors = '<div class="compare_second_result"><span>'.$visitors.' <small>( '.$totalVisitorsPer.' %)</small></span></div>';	
				}

				$hitsPer = $totalVisitorsPer = $totalfirstTimeVisitorsPer = 0;
				$hitsPer = number_format ( ( $row['hits'] * 100 ) / ( $totaiVisitorsToday * 10 ) , 2 );
				if( $urlVisitors['visitors'] && $totalVisitors ){
					$totalVisitorsPer = number_format ( ( $urlVisitors['visitors'] * 100 ) / ( $totalVisitors * 10), 2 );
				}
				if( $urlVisitors['newVisitors'] && $totalFirstTimeVisitors && $totalFirstTimeVisitors > 0 ){
					$totalfirstTimeVisitorsPer = number_format( ( $urlVisitors['newVisitors'] * 100 ) / ($totalFirstTimeVisitors * 10) , 2) ; 
				}
				$row_number = ( $currentPage > 1 ? ( ($currentPage - 1) * 100 ) + $counter : $counter );
				$html .= '<tr id="wsmRowParent_'.$row_number.'" class="wsmReferralRow">
						<td><div class="wsmTooltip">'.$arrow.'&nbsp;'.$row_number.$toolTip.'</div><a class="link_url" href="'. $row['protocol'].$row['url'] .'" target="_blank"><span class="dashicons dashicons-arrow-up-alt"></span></a></td>
						<td><a href="#" data-referrak_param=\''.(json_encode($graphAttr)).'\' class="linkReferralLocationDetails" title="Click here for details.">'. $name .'</a></td>
						<td class="width_100 align_center">'. number_format($row['hits']).' <small>('.$hitsPer.'%)</small>'. $compare_total_hits. '</td>
						<td class="width_100 align_center">'. number_format($urlVisitors['visitors']).' <small>('.$totalVisitorsPer.'%)</small>'. $compare_total_visitors. '</td>
						<td class="width_100 align_center">' .number_format($urlVisitors['newVisitors']).' <small>('.$totalfirstTimeVisitorsPer.'%)</small>'. $compare_total_unique_visitors .'</td>
						</tr>';
						
				$counter++;		
            }
            if(isset($urlStatsResult['pagination'])){
                $html.='<tr><td colspan="5">';
                $html.='<div class="wsmPageContainer">';
                $html.=$urlStatsResult['pagination'];
                $html.='</div>';
                $html.='</tr>';
            }
			$html .= '</table>';
			$html .= '</div>';
        }
        else
			$html = "<p class='wsmCenter'>".__('Data / Statistics are not available.','wp-stats-manager')."</p>";
		return $html;
	}
	
	function wsm_showTitleCloud( $atts ){
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
        $arrRequestData=wsmFnGetFilterPostData();
        $arrPostData=wsmSanitizeFilteredPostData();
		
        $atts = shortcode_atts( array(
            'id' => '__contentByURL',
            'title' => __('Content by URL','wp-stats-manager'),
            'type' => $arrPostData['filterType'],
            'condition' => $arrPostData['filterWay'],
            'from' => $arrPostData[$arrPostData['filterWay']]['from'],
            'to' =>  $arrPostData[$arrPostData['filterWay']]['to'],
            'first' =>  $arrPostData[$arrPostData['filterWay']]['first'],
            'second' =>  $arrPostData[$arrPostData['filterWay']]['second'],
        ), $atts, WSM_PREFIX.'_showContentByURL');		
		
		$condition=$atts['condition'];

        $arrAtts = array();
		
        $currentPage=isset($_GET['wsmp'])&&$_GET['wsmp']!=''?$_GET['wsmp']:1;
        $arrAtts['currentPage']=$currentPage;

		if( isset( $_REQUEST['search'] ) && $_REQUEST['search'] ){
			$arrAtts['search'] = $_REQUEST['search'];
		}
		$admin_url = '';
		if( current_user_can('edit_others_pages') ){
			$admin_url = admin_url('admin.php?page='.WSM_PREFIX.'_content');
			if( isset( $_GET['subPage'] ) && $_GET['subPage'] == 'byTitle' ){
				$admin_url .= '&subPage='.$_GET['subPage'];
			}
			$arrAtts['adminURL'] = $admin_url.'&'.http_build_query($arrRequestData);
			$admin_url = $arrAtts['adminURL'];
		}
		
        switch($atts['type']){
            case 'Hourly':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['from'];
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'];
						$arrAtts['to']= $atts['to'];
					break;
					case 'Compare':
						$arrAtts['from']=$atts['first'];
						$arrAtts['to']= $atts['second'];
					break;
				}
            break;
            case 'Daily':
				switch( $condition ){
					case 'Normal':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['from'].'-01') );
					break;
					case 'Range':
						$arrAtts['from']=$atts['from'].'-01';
						$arrAtts['to']= date('Y-m-t', strtotime( $atts['to'].'-01') );
					break;
					case 'Compare':
						$arrAtts['from']= $atts['first'].'-01';
						$arrAtts['to']= $atts['second'].'-01';
					break;
				}
            break;
        }
		$arrAtts['date'] = $arrAtts['from'];
		//$totalHits = $this->objDatabase->fnGetTotalHitsCount( $arrAtts );
        $totalVisitors = $this->objDatabase->fnGetReferralTotalVisitorsCount($condition, $arrAtts);
        $totalFirstTimeVisitors = $this->objDatabase->fnGetFirstTimeVisitorCount($condition, $arrAtts);
			

		if( $condition == 'Compare' ){
			$compareAtts = $arrAtts;
			$compareAtts['from'] = $arrAtts['from'];
			$compareAtts['to'] = $arrAtts['from'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['from'] ) );
			}
			$urlStatsResult = $this->objDatabase->getContentByURLStats( $compareAtts );
			$compareAtts['from'] = $arrAtts['to'];
			$compareAtts['to'] = $arrAtts['to'];	
			if( $atts['type'] == 'Daily' ){
				$compareAtts['to'] = date('Y-m-t', strtotime( $arrAtts['to'] ) );
			}
		}else{
			$urlStatsResult = $this->objDatabase->getContentByURLStats( $arrAtts );
		}
		//print_r($arrAtts);
		$preArrAtts = $arrAtts;
		$totaiVisitorsToday = $totaiVisitorsYesterday = 0;

		$totaiVisitorsToday = $this->objDatabase->getContentByURLTotalRecords( $arrAtts );
		
        if($condition=='Normal'){
		
			$startFrom = $arrAtts['from'];
		
			$lastFrom  = $preArrAtts['from'] = date('Y-m-d', strtotime($preArrAtts['from'] .' -1 day'));
			$preArrAtts['to'] = $preArrAtts['from']; 
		
			$totaiVisitorsYesterday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
			
		}
		if($condition=='Compare'){

			$preArrAtts['to'] =$arrAtts['from'];
			$totaiVisitorsToday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
			
			$startFrom = $arrAtts['to'];
		
			$lastFrom  = $preArrAtts['to'];
			$preArrAtts['from'] = $arrAtts['from']; 
		
			$totaiVisitorsYesterday = $this->objDatabase->getContentByURLTotalRecords( $preArrAtts );
		}
		$graphAttr['to'] = $arrAtts['from'];
		$graphAttr['from'] = date('Y-m-d', strtotime($arrAtts['from'] .' -30 day'));
		$graphAttr['action'] = 'getContentUrlDayView';
		$html = '';
		$site_url =  str_replace( array(  'http://www.', 'https://wwww.', 'http://', 'https://','www.'), array(), site_url() );
		$title_cloud_array = array();
		$title_cloud_html = array();
        if( is_array( $urlStatsResult ) && isset( $urlStatsResult['data'] ) && count($urlStatsResult['data'])>0){
		   	$html .= '<ul class="title_colud_panel">';
			$counter = 1;					
            foreach($urlStatsResult['data'] as $row ){                 
				$arrow = $toolTip = '';
				
				$graphAttr['id'] = $row['id'];
				$arrAtts['id'] = $row['id'];
				$preArrAtts['id'] = $row['id'];
				
				if($condition=='Compare'){
					$temp = $arrAtts;
					$temp['to'] = $arrAtts['from'];
					$urlVisitors = $this->objDatabase->getContentByURLVisitors( $temp ); 
				}else{
					$urlVisitors = $this->objDatabase->getContentByURLVisitors( $arrAtts ); 
				}
				$url = str_replace( $site_url, '', $row['url'] );

				$hitsPer = $totalVisitorsPer = $totalfirstTimeVisitorsPer = 0;
				$hitsPer = number_format ( ( $row['hits'] * 100 ) / ( $totaiVisitorsToday * 10 ) , 2 );
				if( $urlVisitors['visitors'] && $totalVisitors ){
					$totalVisitorsPer = number_format ( ( $urlVisitors['visitors'] * 100 ) / ( $totalVisitors * 10), 2 );
				}
				if( $urlVisitors['newVisitors'] && $totalFirstTimeVisitors && $totalFirstTimeVisitors > 0 ){
					$totalfirstTimeVisitorsPer = number_format( ( $urlVisitors['newVisitors'] * 100 ) / ($totalFirstTimeVisitors * 10) , 2) ; 
				}
				$post_id = url_to_postid( $row['protocol'].$row['url'] );
				if( $post_id ){
					$name = get_the_title( $post_id );
				}else{
					$name = wsmArchiveTitleFromURL( $url );
				}
				if( $url == $name  && !empty( $row['title'] ) ){
					$name = $row['title'];
				}
				
				$name_array = explode(' ', $name );
				
				if( is_array( $name_array ) && count( $name_array ) ){
					foreach( $name_array as $name_part ){
						$name_part = strtolower( $name_part );
						$hits = $row['hits'];
						$visitors = $urlVisitors['visitors'];
						$newVisitors = $urlVisitors['newVisitors'];
						$oldEntry = false;
						if( key_exists( $name_part, $title_cloud_array ) ){
							$oldEntry = true;
							$hits += $title_cloud_array[$name_part][0];
							$visitors += $title_cloud_array[$name_part][1];
							$newVisitors += $title_cloud_array[$name_part][2];
						}
						
						$font_size = array( 14, 16, 18, 20, 22, 24 );
						shuffle( $font_size );
						$title_cloud_array[ $name_part ] = array($hits, $visitors, $newVisitors);							
						$toolTip='<div class="wsmTooltip"><a href="'.$admin_url.'&wsmp=1&search='.$name_part.'" style="font-size:'.$font_size[0].'px" data-search="'.$name_part.'">'.$name_part.'</a><span class="wsmTooltipText">
							<font>Hits: <span class="wsmColorYellow">'.$hits.'</span></font>
							<font>Visitors: <span class="wsmColorYellow">'.$visitors.'</span></font>
							<font>New Visitors: <span class="wsmColorYellow">'.$newVisitors.'</span></font>
						</span></div>';
						if( count( $title_cloud_html ) < 100 || $oldEntry ){
							$title_cloud_html[ $name_part ] = $toolTip;
						}
						
					}
					
				}
            }
			shuffle( $title_cloud_html );
			$html .= '<li>'. implode( '</li><li>', $title_cloud_html ) .'</li>';
			$html .= '</ul>';
        }
        else
        {
			$html = "<p class='wsmCenter'>".__('Data / Statistics are not available.','wp-stats-manager')."</p>";
		}
		return $html;
	}
	
	function wsm_showIPExclustion( $atts ){
		
		$ipAddress = get_option('exclusion_ip_address_list');
		
		$html = '<div class="wsmTableContainer">';
		$html .= '<div class="exclusion_id_form_panel">';
		$html .= '<div class="update_message"></div>';
		$html .= '<input type="text" name="ipadress" id="ipadress" value="" placeholder="000.000.000.000" />';
		$html .= '<input type="hidden" name="action" value="save_ipadress" />';
		$html .= '<input type="button" class="save_ipadress button button-primary" value="'.__('Save IP Address','wp-stats-manager').'" />';
		$html .= '</div>';
		$html .= '<table id="tblIPList" class="wsmTableStriped">';
		$html .= '<tr>';
		$html .= '<th>'.__('No','wp-stats-manager').'</th>';
		$html .= '<th>'.__('I.P. Address','wp-stats-manager').'</th>';
		$html .= '<th>'.__('Status','wp-stats-manager').'</th>';
		$html .= '<th>'.__('Action','wp-stats-manager').'</th>';
		$html .= '</tr>';
		$count = 1;
		if( $ipAddress && is_array($ipAddress) ){
			foreach( $ipAddress as $address => $status ){
				$html .= '<tr id="row_'.$count.'">';
				$html .= '<td>'.$count.'</td>';
				$html .= '<td>'.$address.'</td>';
				$html .= '<td><label class="switch"><input data-ipaddress="'.$address.'" type="checkbox" '.($status?'checked':'').'><div class="slider round"></div></label></td>';
				$html .= '<td><a href="#" data-row="'.$count.'" data-ipaddress="'.$address.'" class="deleteIP button button-secondary">'.__('Delete','wp-stats-manager').'</a></td>';
				$html .= '</tr>';
				$count++;
			}
		}else{
			$html .= '<tr><td align="center" colspan="3">'.__('No records found.').'</td></tr>';
		}
		$html .= '</table>';
		$html .= '</div>';
		return $html;
	}	
	
	function wsm_showGeneralStats()
	{
		add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
		$totalPageViews=$this->objDatabase->fnGetTotalPageViewCount();
        $totalVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Today');
        $todayVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Online');
        $onlineVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Online');
        $pageViews=0;
        if($totalPageViews>0 && $totalVisitors>0){
            $pageViews=($totalPageViews/$totalVisitors);
        }
        $totalPageViews=number_format_i18n($totalPageViews,0);
        $totalVisitors=number_format_i18n($totalVisitors,0);
        $pageViews=number_format_i18n($pageViews,2);
        $onlineVisitors=number_format_i18n($onlineVisitors,0);
        
        $html ="<div class='panel'>";
        $html ="<div class='panel_data greenClr'>";
        $html .="<div class='wsmCnt'>".$onlineVisitors."<br><label>Online Users</label></div>";
        $html .="</div>";
        $html .="<div class='panel_data redClr'>";
        $html .="<div class='wsmCnt'>".$todayVisitors."<br><label>Today Visitors</label></div>";
        $html .="</div>";
        $html .="<div class='panel_data blueClr'>";
        $html .="<div class='wsmCnt'>".$totalPageViews."<br><label>Today Visits</label></div>";
        $html .="</div>";
        $html .="<div class='panel_data violetClr'>";
        $html .="<div class='wsmCnt'>".$totalVisitors."<br><label>Today Visits</label></div>";
        $html .="</div>";
        $html .="</div>";
        return $html;	
	}
}
