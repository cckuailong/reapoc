<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
include_once(WSM_DIR.'/includes/'.WSM_PREFIX.'_functions.php');
include_once(WSM_DIR.'/includes/'.WSM_PREFIX.'_db.php');
include_once(WSM_DIR.'/includes/'.WSM_PREFIX.'_requests.php');
include_once(WSM_DIR.'/includes/'.WSM_PREFIX.'_statistics.php');
include_once(WSM_DIR.'/includes/'.WSM_PREFIX.'_admin_interface.php');
include_once(WSM_DIR.'/includes/'.WSM_PREFIX.'_scheduled_mail.php');
include_once(WSM_DIR.'/includes/'.WSM_PREFIX.'_cron.php');

define('WSM_TIMEZONE',wsmCurrentGetTimezoneOffset());
class wsmInitPlugin{
    private static $tablePrefix,$objAdminInterface,$objStats,$objWsmRequest,$objDatabase;
    public static function initWsm(){
        global $wpdb,$wsmAdminPageHooks;
        self::$tablePrefix=$wpdb->prefix.WSM_PREFIX;
        register_activation_hook( WSM_FILE, array( 'wsmInitPlugin',WSM_PREFIX.'_activate') );
        register_deactivation_hook( WSM_FILE, array( 'wsmInitPlugin',WSM_PREFIX.'_deactivate') );
        add_action( 'wpmu_new_blog',  array( 'wsmInitPlugin',WSM_PREFIX.'CreateDatabaseSchemaForNewSite') );
        add_action('init', array( 'wsmInitPlugin',WSM_PREFIX.'_plugin_init'));
        add_action('wp_head',array( 'wsmInitPlugin',WSM_PREFIX.'_addTrackerScript'));
        add_action('admin_init', array('wsmInitPlugin',WSM_PREFIX.'_admin_init'), 1);
        add_action('admin_menu', array('wsmInitPlugin',WSM_PREFIX.'_admin_menu'), 20);
        add_action('admin_head', array('wsmInitPlugin',WSM_PREFIX.'_admin_head'), 20);        
        add_action(WSM_PREFIX.'_dailyScheduler', array('wsmInitPlugin',WSM_PREFIX.'_dailyScheduler'));
        add_action('wp_footer', array('wsmInitPlugin',WSM_PREFIX.'_footerScripts'));
        add_action('admin_print_footer_scripts', array('wsmInitPlugin',WSM_PREFIX.'_footerScripts'));
        add_action('wp_ajax_liveStats', array('wsmInitPlugin',WSM_PREFIX.'_getLiveStats'));
        add_action('wp_ajax_uoSummary', array('wsmInitPlugin',WSM_PREFIX.'_getUOSummary'));
        add_action('wp_ajax_timezoneByCountry', array('wsmInitPlugin',WSM_PREFIX.'_getTimezoneByCountry'));
        add_action('wp_ajax_refDetails', array('wsmInitPlugin',WSM_PREFIX.'_getReferrerDetails'));
		add_action('wp_ajax_refUrlDetails', array('wsmInitPlugin',WSM_PREFIX.'_getReferrerUrlDetails'));
		add_action('wp_ajax_getReferralOSDetails', array('wsmInitPlugin',WSM_PREFIX.'_getReferralOSDetails'));
		add_action('wp_ajax_getDateWiseLocationDetail', array('wsmInitPlugin',WSM_PREFIX.'_getDateWiseLocationDetail'));
		add_action('wp_ajax_getContentUrlDayView', array('wsmInitPlugin',WSM_PREFIX.'_getContentUrlDayView'));
		add_action('wp_ajax_save_ipadress', array('wsmInitPlugin',WSM_PREFIX.'_save_ipadress'));
		add_action('wp_ajax_deleteIpAddress', array('wsmInitPlugin',WSM_PREFIX.'_deleteIpAddress'));
		add_action('wp_ajax_updateIpAddress', array('wsmInitPlugin',WSM_PREFIX.'_updateIpAddress'));
        add_filter( 'clean_url', array('wsmInitPlugin',WSM_PREFIX.'_async_scripts'), 11, 1 );
		add_action( 'wp_enqueue_scripts',  array('wsmInitPlugin',WSM_PREFIX.'_front_script_style'));
		add_action( 'admin_footer', array( 'wsmInitPlugin',WSM_PREFIX.'_setting_popup_func'));
		add_filter('script_loader_tag', WSM_PREFIX.'_add_async_defer_attribute', 10, 2);
		update_option(WSM_PREFIX.'KeepData',1);
		
		add_action('admin_bar_menu', 'wsm_free_add_items',  40);
		add_action('wp_enqueue_scripts', 'wsm_free_top_bar_enqueue_style');
		add_action('admin_enqueue_scripts', 'wsm_free_top_bar_enqueue_style');
			
	
	
        //self::wsm_createMonthWiseViews();
       // add_action('wp_head',array( 'wsmInitPlugin',WSM_PREFIX.'_add_custom_script'));
    }
  
    static function wsm_front_script_style()
	{
		//wp_enqueue_script(P_PREFIX.'-front-js', WSM_URL . 'js/wsm_new.js',array(), '1.0.0');
		wp_register_style( WSM_PREFIX.'-style', WSM_URL . 'css/style.css', false, '1.2' );
        wp_enqueue_style( WSM_PREFIX.'-style' );
	}
    static function wsm_async_scripts($url){
      if( strpos( $url, '#asyncload') === false ){
        return $url;
      }else if ( current_user_can('edit_others_pages') ){
          return str_replace( '#asyncload', '', $url );
      }else{
    	   return str_replace( '#asyncload', '', $url )."' async='async";
      }
    }
    static function wsm_footerScripts(){
        global $wsmAdminJavaScript;
        $wsmAdminJavaScript.=self::wsm_allAjaxRequests();
        echo '<script type="text/javascript">
        jQuery(function(){
        var arrLiveStats=[];
        var WSM_PREFIX="'.WSM_PREFIX.'";
		
        jQuery(".if-js-closed").removeClass("if-js-closed").addClass("closed");
                '.$wsmAdminJavaScript.'});
        </script>';
    }
    static function wsm_allAjaxRequests(){
        $ajaxScript='';       
		
        $wsmAjaxRequestArray=$_REQUEST;
        $enable_site_stats = true;
        if(isset($wsmAjaxRequestArray['page']) && $wsmAjaxRequestArray['page']==WSM_PREFIX.'_traffic'){
            $subPage=isset($wsmAjaxRequestArray['subPage']) && $wsmAjaxRequestArray['subPage']!='' ?$wsmAjaxRequestArray['subPage']:'Summary';
            if($subPage!=''){
                switch($subPage){
                    case 'Summary':
                        $enable_site_stats = false;
                        $ajaxScript.='
                        jQuery(\'.wsm_days_filter\').change(function(){
                            wsmFnGetLiveStats();
                        });
                        var wsmFnGetLiveStats=function(){
                            var days_filter = "";
                            if( jQuery(\'.wsm_days_filter\').length ){
                                days_filter = jQuery(\'.wsm_days_filter\').val();
                            }
                           jQuery.ajax({
                               type: "POST",
                               url: wsm_ajaxObject.ajax_url,
                               data: { action: \'liveStats\', requests: JSON.stringify(arrLiveStats), days_filter: days_filter, r: Math.random() }
                           }).done(function( strResponse ) {
                                if(strResponse!="No"){
                                    arrResponse=JSON.parse(strResponse);
                                    jQuery.each(arrResponse, function(key,value){
                                   	
										if(key.startsWith(WSM_PREFIX+"CurrentHourStats")){
                                            arrKey=key.split("_");
                                            var objPlot=eval("plot_"+arrKey[1]);
                                            var objData=eval(arrKey[1]+"_Data");
                                            var objOptions=eval(arrKey[1]+"_options");
                                            var objFirstTime=eval(arrKey[1]+"_firstTime");
                                            var objPageViews=eval(arrKey[1]+"_pageViews");
                                            var objVisitors=eval(arrKey[1]+"_visitors");
                                            var objbounceRate=eval(arrKey[1]+"_bounceRate");
                                            var objPpv=eval(arrKey[1]+"_ppv");
                                            var objNvis=eval(arrKey[1]+"_newVisitor");
                                            var objAvgOnline=eval(arrKey[1]+"_avgOnline");
                                            currentStats=JSON.parse(value);
                                            objFirstTime[parseInt(arrKey[2])]=parseInt(currentStats.firstTime);
                                            objVisitors[parseInt(arrKey[2])]=parseInt(currentStats.visitors);
                                            objPageViews[parseInt(arrKey[2])]=parseInt(currentStats.pageViews);
                                            objbounceRate[parseInt(arrKey[2])]=parseFloat(currentStats.Bounce);
                                            objPpv[parseInt(arrKey[2])]=parseFloat(currentStats.ppv);
                                            objNvis[parseInt(arrKey[2])]=parseFloat(currentStats.newVisitor);
                                            objAvgOnline[parseInt(arrKey[2])]=parseFloat(currentStats.avgOnline);
                                            objData=[objFirstTime,objVisitors,objPageViews];
                                            jQuery("#"+arrKey[1]).empty();
                                            objPlot = jQuery.jqplot(arrKey[1], objData,objOptions );
                                            wsmMoveLegend(jQuery("#"+arrKey[1]).parents(\'.postbox\'));
                                            jQuery("#"+arrKey[1]).siblings(\'.wsmTopChartBar\').find(\'.wsmButton.active\').click();                          
                                            var arrLegendIndex=eval(arrKey[1]+"_legendIndex");               
                                            if(arrLegendIndex.length){
                                                var parentC=jQuery("#"+arrKey[1]).parent();
                                                arrLegendIndex.forEach(function(elementIndex) {
                                                    var StrElm="."+WSM_PREFIX+"TopChartBar table.jqplot-table-legend tr td:eq("+elementIndex+")";
                                                    var legendElm=parentC.find(StrElm);
                                                    legendElm.trigger("click","code");  
                                                });  
                                            }                                    
                                            return true;
                                        }
                                       if(key.startsWith(WSM_PREFIX+"CurrentDayStats")){
                                            arrKey=key.split("_");
                                            var objPlot=window["plot_"+arrKey[1]];
                                            dayStats=JSON.parse(value);
                                            arrKey[2]=arrKey[2].replace(/x/g, "-");
                                            var objData=arrKey[1]+"_arrLineData";
                                            var objFirstTime=arrKey[1]+"_bfirstVisitors";
                                            var objPageViews=arrKey[1]+"_bpageViews";
                                            var objVisitors=arrKey[1]+"_bvisitors";
                                            var objbounceRate=arrKey[1]+"_bBounce";
                                            var objPpv=arrKey[1]+"_bppv";
                                            var objNvis=arrKey[1]+"_bnewVisitor";
                                            var objAvgOnline=arrKey[1]+"_bavgOnline";
                                            var objOptions=eval(arrKey[1]+"_bOptions");
                                            var objGraphFormat = eval(arrKey[1]+"_graphFormat");
                                            /*objPageViews[objPageViews.length-1]=dayStats.pageViews[objPageViews.length-1];
                                            objVisitors[objVisitors.length-1]=dayStats.visitors[objVisitors.length-1];
                                            objFirstTime[objFirstTime.length-1]=dayStats.firstTimeVisitors[objFirstTime.length-1];
                                            objbounceRate[objbounceRate.length-1]=dayStats.Bounce[objbounceRate.length-1];
                                            objPpv[objPpv.length-1]=dayStats.ppv[objPpv.length-1];
                                            objNvis[objNvis.length-1]=dayStats.newVisitor[objNvis.length-1];
                                            objAvgOnline[objAvgOnline.length-1]=dayStats.avgOnline[objAvgOnline.length-1];*/

                                            /*objPageViews=dayStats.pageViews;
                                            objVisitors=dayStats.visitors;
                                            objFirstTime=dayStats.firstTimeVisitors;
                                            objbounceRate=dayStats.Bounce;
                                            objPpv=dayStats.ppv;
                                            objNvis=dayStats.newVisitor;
                                            objAvgOnline=dayStats.avgOnline;
                                            objData = [objFirstTime,objVisitors,objPageViews];*/
                                            
                                            window[objData] = [dayStats.firstTimeVisitors,dayStats.visitors,dayStats.pageViews];
                                            window[objbounceRate] = dayStats.Bounce;
                                            window[objPpv] = dayStats.ppv;
                                            window[objNvis] = dayStats.newVisitor;
                                            window[objAvgOnline] = dayStats.avgOnline;
                                            var objData2 = [dayStats.firstTimeVisitors,dayStats.visitors,dayStats.pageViews];
                                            if( jQuery("#"+arrKey[1]).parents(".inside").find(".wsm_days_filter") ){
                                                if( jQuery("#"+arrKey[1]).parents(".inside").find(".wsm_days_filter").val() == "Last24hours" ){
                                                        objOptions.axes.xaxis.tickOptions.formatString = objGraphFormat.hour.format;
                                                        objOptions.axes.xaxis.tickInterval = objGraphFormat.hour.interval;
                                                    }else{
                                                        objOptions.axes.xaxis.tickOptions.formatString = objGraphFormat.day.format;
                                                        objOptions.axes.xaxis.tickInterval = objGraphFormat.day.interval;
                                                    }
                                            }
                                            objOptions.axes.xaxis.numberTicks = dayStats.pageViews.length;
                                            jQuery("#"+arrKey[1]).empty();
                                            objPlot = jQuery.jqplot(arrKey[1], objData2,objOptions ).replot();
                                            wsmMoveLegend(jQuery("#"+arrKey[1]).parents(\'.postbox\'));
                                            jQuery("#"+arrKey[1]).siblings(\'.wsmTopChartBar\').find(\'.wsmButton.active\').click();
                                            var arrLegendIndex=eval(arrKey[1]+"_legendIndex");               
                                            if(arrLegendIndex.length){
                                                var parentC=jQuery("#"+arrKey[1]).parent();
                                                arrLegendIndex.forEach(function(elementIndex) {
                                                    var StrElm="."+WSM_PREFIX+"TopChartBar table.jqplot-table-legend tr td:eq("+elementIndex+")";
                                                    var legendElm=parentC.find(StrElm);
                                                    legendElm.trigger("click","code");  
                                                });  
                                            }      
                                            return true;
                                        }
                                        $element= document.getElementById(key);
                                        oldValue=parseInt($element.getAttribute("data-value").replace(/,/g, ""));
                                        diff=parseInt(value.replace(/,/g, ""))-oldValue;
                                        $class="";
										
										if(diff != 0)
										{
											if(diff>=0){
												diff="+"+diff;
											}else{
												$class="wmcRedBack";
											}

											$element.setAttribute("data-value",value);
											$element.innerHTML=diff;
											jQuery("#"+key).addClass($class).show().siblings(".wsmH2Number").text(value);
                                        }
                                        if(key=="wsmTodayOnlineUsers")
										{
											var onlineUserCnt = arrResponse.wsmTodayOnlineUsers;
                                            if(jQuery("#wsmGenUserOnline").length)
                                            {
                                                jQuery("#wsmGenUserOnline").attr("data-value",onlineUserCnt);   jQuery("#wsmGenUserOnline").next(".wsmH2Number").html("<a target=\"_blank\" href=\"?page=wsm_traffic&subPage=UsersOnline&subTab=summary\">"+onlineUserCnt+"</a>");
                                            }
                                            if(jQuery("#wsmSiteUserOnline").length)
                                            {
                                                jQuery("#wsmSiteUserOnline").attr("data-value",onlineUserCnt);   jQuery("#wsmSiteUserOnline").next(".wsmH2Number").html("<a target=\"_blank\" href=\"?page=wsm_traffic&subPage=UsersOnline&subTab=summary\">"+onlineUserCnt+"</a>");
                                            }
											
											if(jQuery("#wsmTodayOnlineUsers").lenth)
											{
												jQuery("#wsmTodayOnlineUsers").prev(".wsmH2Number").html("<a target=\"_blank\" href=\"?page=wsm_traffic&subPage=UsersOnline&subTab=summary\">"+onlineUserCnt+"</a>");
											}
										}
                                    });
                                    setTimeout(function() {
                                        jQuery.each(arrResponse, function(key,value){
                                            jQuery("#"+key).removeClass("wmcRedBack").hide();
                                        });
                                    }, 1500);
                                }
                           });
                       }
                       if(arrLiveStats.length>0){
                          setInterval(wsmFnGetLiveStats, 10000);
                       }';
                    break;
                    case 'UsersOnline':                        
                        if(isset($wsmAjaxRequestArray['subTab']) && $wsmAjaxRequestArray['subTab']!=''){
                            switch($wsmAjaxRequestArray['subTab']){
                                case 'summary':
                                case 'geoLocation':
                                    $ajaxScript.='var wsmFnGetLiveStats=function(){
                                           jQuery.ajax({
                                               type: "POST",
                                               url: wsm_ajaxObject.ajax_url,
                                               data: { action: \'uoSummary\', requests: JSON.stringify(arrLiveStats), r: Math.random() }
                                           }).done(function( strResponse ) {
                                                if(strResponse!="No"){
                                                    arrResponse=JSON.parse(strResponse);
                                                    jQuery.each(arrResponse, function(key,value){
                                                        if(key.startsWith(WSM_PREFIX+"TopTitle")){
                                                            var arrkeys=Object.keys(value);
                                                            jQuery(".wsmTopTitle .wsmOnline b").text(value.wsmOnline);
                                                            jQuery(".wsmTopTitle .wsmBrowsing b").text(value.wsmBrowsing);
                                                            return true;
                                                        }
                                                        if(key.startsWith(WSM_PREFIX+"_mostActiveVisitorsGeoLocation")){
                                                            wsm_locations=[];
                                                            wsm_lDetails=[];
                                                            var arrLocations=jQuery.parseJSON(value);
                                                            Array.prototype.forEach.call(arrLocations, function(pages){  
                                                                var point={};
                                                                var pointDetails={};                 
                                                                pointDetails.ipAddress=pages.ipAdress;
                                                                pointDetails.views=pages.views;
                                                                point.lat=parseFloat(pages.latitude);                 
                                                                point.lng=parseFloat(pages.longitude);                               
                                                                wsm_locations.push(point);                          
                                                                wsm_lDetails.push(pointDetails);                          
                                                                });
                                                                wsmInitMap();
                                                            return true;
                                                        }
                                                        jQuery("#"+key).parent().html(value);
                                                    });
                                                }
                                           });
                                    }
                                       if(arrLiveStats.length>0){
                                            setInterval(wsmFnGetLiveStats, 10000);
                                       }
                                       ';
                                break;
                                case 'recent':
                                case 'mavis':
                                case 'popPages':
                                case 'popReferrer':
                                     $ajaxScript.='var wsmFnGetLiveStats=function(){
                                        jQuery.ajax({
                                               type: "POST",
                                               url: wsm_ajaxObject.ajax_url,
                                               data: { action: \'uoSummary\', requests: JSON.stringify(arrLiveStats), r: Math.random() }
                                           }).done(function( strResponse ) {
                                                 arrResponse=JSON.parse(strResponse);
                                                    jQuery.each(arrResponse, function(key,value){
                                                        if(key.startsWith(WSM_PREFIX+"TopTitle")){
                                                            var arrkeys=Object.keys(value);
                                                            jQuery(".wsmTopTitle .wsmOnline b").text(value.wsmOnline);
                                                            jQuery(".wsmTopTitle .wsmBrowsing b").text(value.wsmBrowsing);
                                                            return true;
                                                        }
                                                        jQuery("#"+key).parent().html(value);
                                                    });
                                           });
                                     };
                                     if(arrLiveStats.length>0){
                                            setInterval(wsmFnGetLiveStats, 10000);
                                       }
                                     ';
                                break;
                                default:
                                break;                                  
                                  
                            }    
                        }
                    break;
                }
            }            
        }
        $ajaxSiteScript='var wsmFnSiteLiveStats=function(){
                           jQuery.ajax({
                               type: "POST",
                               url: wsm_ajaxObject.ajax_url,
                               data: { action: \'liveSiteStats\', requests: JSON.stringify(arrLiveStats), r: Math.random() }
                           }).done(function( strResponse ) {
                                if(strResponse!="No"){
                                    arrResponse=JSON.parse(strResponse);
                                    jQuery.each(arrResponse, function(key,value){
                                    
                                        $element= document.getElementById(key);
                                        oldValue=parseInt($element.getAttribute("data-value").replace(/,/g, ""));
                                        diff=parseInt(value.replace(/,/g, ""))-oldValue;
                                        $class="";
                                        
                                        if(diff>=0){
                                            diff="+"+diff;
                                        }else{
                                            $class="wmcRedBack";
                                        }

                                        $element.setAttribute("data-value",value);
                                        $element.innerHTML=diff;
                                        jQuery("#"+key).addClass($class).show().siblings(".wsmH2Number").text(value);
                                        
                                        if(key=="SiteUserOnline")
                                        {
                                            var onlineUserCnt = arrResponse.wsmSiteUserOnline;
                                            if(jQuery("#wsmSiteUserOnline").length)
                                            {
                                                jQuery("#wsmSiteUserOnline").attr("data-value",onlineUserCnt);   jQuery("#wsmSiteUserOnline").next(".wsmH2Number").html("<a target=\"_blank\" href=\"?page=wsm_traffic&subPage=UsersOnline&subTab=summary\">"+onlineUserCnt+"</a>");
                                            }
                                        }
                                    });
                                    setTimeout(function() {
                                        jQuery.each(arrResponse, function(key,value){
                                            jQuery("#"+key).removeClass("wmcRedBack").hide();
                                        });
                                    }, 1500);
                                }
                           });
                       }
                       if(arrLiveStats.length>0){
                          setInterval(wsmFnSiteLiveStats, 10000);
                       }';
        if( $enable_site_stats ){
            $ajaxScript .= $ajaxSiteScript;
        }
        return $ajaxScript;
    }
    static function wsm_admin_head(){
		/*echo '<style>';
		include(WSM_DIR . 'css/custom_admin.php');
		echo '</style>';*/
		$admin_url = get_admin_url() . "admin.php?page=";
		
		$widget_detail_page_links = array('referral_website_stats'	=>	$admin_url.WSM_PREFIX.'_trafficsrc',
									'search_engine_stats'	=>	$admin_url.WSM_PREFIX.'_trafficsrc',
									'traffic_by_title_stats' => $admin_url.WSM_PREFIX.'_content&subPage=byTitle',
									'top_search_engine_stats' => $admin_url.WSM_PREFIX.'_trafficsrc&subPage=SearchEngines',
									'os_wise_visitor_stats'	=>	$admin_url.WSM_PREFIX.'_visitors',
									'browser_wise_visitor_stats'	=>	$admin_url.WSM_PREFIX.'_visitors',
									'screen_wise_visitor_stats'	=>	$admin_url.WSM_PREFIX.'_visitors',
									'country_wise_visitor_stats'	=>	$admin_url.WSM_PREFIX.'_visitors&subPage=GeoLocation',
									'city_wise_visitor_stats'	=>	$admin_url.WSM_PREFIX.'_visitors&subPage=GeoLocation&location=city'
							);
							
         echo '<script type="text/javascript">
            var WSM_PREFIX="'.WSM_PREFIX.'"; wsm_widgets='.json_encode( array_keys( $widget_detail_page_links ) ).'; wsm_widget_links = '.json_encode( array_values( $widget_detail_page_links ) ).';  </script>';
    }
    static function wsm_plugin_init() {
        global $wsmRequestArray;        
        if(isset($wsmRequestArray['wmcAction']) && ($wsmRequestArray['wmcAction']=='wmcTrack' || $wsmRequestArray['wmcAction']=='wmcAutoCron') ){
            self::$objWsmRequest= new wsmRequests($wsmRequestArray);
        }

        load_plugin_textdomain( WSM_PREFIX, false, dirname(plugin_basename(WSM_FILE)).'/languages/' );
        self::$objStats=new wsmStatistics();
        self::$objDatabase=self::$objStats->wsm_getDatabaseObject();
        $lastRunDate=get_option(WSM_PREFIX.'_dailyReportedTime');

        if(!$lastRunDate || $lastRunDate!=wsmGetCurrentDateByTimeZone('Y-m-d')){
            self::wsm_fnCreateImportantViews();
            $startDateTime=wsmGetDateByInterval('-1 days','Y-m-d');
            $newTimeZone=wsmCurrentGetTimezoneOffset();
            $pageViews=self::$objDatabase->fnGetHourlyReportByDateNameTimeZone($startDateTime,'hourWisePageViews',$newTimeZone);
            $visitors=self::$objDatabase->fnGetHourlyReportByDateNameTimeZone($startDateTime,'hourWiseVisitors',$newTimeZone);
            $firstTime=self::$objDatabase->fnGetHourlyReportByDateNameTimeZone($startDateTime,'hourWiseFirstVisitors',$newTimeZone);
            self::$objDatabase->fnCorrectDatabaseTables();
        }
        if(!$lastRunDate || $lastRunDate==wsmGetCurrentDateByTimeZone('Y-m-01')){
            self::wsm_createMonthWiseViews();
        }
        if(!current_user_can('edit_others_pages')){
            add_filter( 'do_shortcode_tag',array( 'wsmInitPlugin',WSM_PREFIX.'AddWrapperToShortCode'),10,3);
        }
    }
    static function wsmAddWrapperToShortCode($output, $tag, $attr){
        if(substr( $tag, 0, strlen(WSM_PREFIX) ) === WSM_PREFIX){
            return '<div class="wsmMetaboxContainer">'.$output.'</div>';
        }
        return $output;
    }
    static function wsm_getLiveStats(){
        $arrRequest=array();
        $arrResponse=array();
        if(isset($_REQUEST['requests'])){
            $strRequest=stripslashes($_REQUEST['requests']);
            $arrRequest=json_decode($strRequest);            
            $oDays=get_option(WSM_PREFIX.'ChartDays');
            $nDays=($oDays!='' && $oDays>0) ?$oDays:60;
            foreach($arrRequest as $request){
                switch($request){
                    case WSM_PREFIX.'GenTotalPageViews':
                        $arrResponse[WSM_PREFIX.'GenTotalPageViews']=number_format_i18n(self::$objDatabase->fnGetTotalPageViewCount(),0);
                    break;
                    case WSM_PREFIX.'GenTotalVisitors':
                        $arrResponse[WSM_PREFIX.'GenTotalVisitors']=number_format_i18n(self::$objDatabase->fnGetTotalVisitorsCount(),0);
                    break;
                    case WSM_PREFIX.'TodayTotalPageViews':
                        $arrResponse[WSM_PREFIX.'TodayTotalPageViews']=number_format_i18n(self::$objDatabase->fnGetTotalPageViewCount('Today'),0);
                    break;
                    case WSM_PREFIX.'TodayTotalVisitors':
                        $arrResponse[WSM_PREFIX.'TodayTotalVisitors']=number_format_i18n(self::$objDatabase->fnGetTotalVisitorsCount('Today'),0);
                    break;
                    case WSM_PREFIX.'TodayTotalFirstVisitors':
                        $arrResponse[WSM_PREFIX.'TodayTotalFirstVisitors']=number_format_i18n(self::$objDatabase->fnGetFirstTimeVisitorCount('Today'),0);
                    break;
                    case WSM_PREFIX.'TodayOnlineUsers':
                        $arrResponse[WSM_PREFIX.'TodayOnlineUsers']=number_format_i18n(self::$objDatabase->fnGetTotalVisitorsCount('Online'),0);
                    break;
                    case WSM_PREFIX.'LastMonthsTotalPageViews':
                        $arrResponse[WSM_PREFIX.'LastMonthsTotalPageViews']=number_format_i18n(self::$objDatabase->fnGetTotalPageViewCount($nDays),0);
                    break;
                    case WSM_PREFIX.'LastMonthsTotalVisitors':
                        $arrResponse[WSM_PREFIX.'LastMonthsTotalVisitors']=number_format_i18n(self::$objDatabase->fnGetTotalVisitorsCount($nDays),0);
                    break;
                    case WSM_PREFIX.'LastMonthsTotalFirstVisitors':
                        $arrResponse[WSM_PREFIX.'LastMonthsTotalFirstVisitors']=number_format_i18n(self::$objDatabase->fnGetFirstTimeVisitorCount($nDays),0);
                    break;
                    case strstr($request,WSM_PREFIX.'CurrentHourStats'):
                        $arrResponse[$request]=json_encode(self::$objDatabase->fnGetCurrentHourStats());
                    break;
                    case strstr($request,WSM_PREFIX.'CurrentDayStats'):
                        $arrResponse[$request]=json_encode(self::$objDatabase->fnGetCurrentDayStats());
                    break;
                }
            }
        }
        if(count($arrResponse)>0){
            echo json_encode($arrResponse);
        }else{
            echo 'No';
        }
        wp_die();
    }
    static function wsm_getReferrerDetails(){
        $arrRequest=array();
        $arrResponse=array();
        if(isset($_REQUEST['requests'])){
            $strRequest=stripslashes($_REQUEST['requests']);
            $arrRequest=(array)json_decode($strRequest);
            $arrDetails=self::$objDatabase->fnGetListOfUrlsByReferral($arrRequest['condition'],$arrRequest);
			$searchengine = $arrRequest['searchengine'];
			$arrRequest['searchengine'] = '';
            $html='<div><table class="wsmTableStriped">';
            foreach($arrDetails as $row){
				$text = $row['fullURL'];
				if( $searchengine ){
						$text = ($row['title']) ? $row['title']: 'Not found';
				}				
                $html.='<tr><td>'.$row['totalViews'].'</td><td><a href="'.$row['fullURL'].'" target="_blank">'. $text .'</a></td></tr>';
            }
            $html.='</table></div>';
            echo $html;
            //echo print_r(,true);
        }
        wp_die();
    }
	
	static function wsm_getDateWiseLocationDetail(){
		$arrRequest= $_REQUEST;
		/**
		 * Chart row data
		 */
		$nDays=get_option(WSM_PREFIX.'ChartDays');
        $nDays=($nDays!='' && $nDays>0) ?$nDays:30;
		//$nDays= 60;
		$tDays=3;
        
        $colors=array('rgba(244,81,81,1)','rgba(251,194,70,1)','rgba(87,135,184,1)','rgba(0,128,0,1)');
		/* End
		 *
		 */
		$arrDetails=self::$objDatabase->getReferralCountryStats( $arrRequest );
		if( isset( $arrRequest['city'] ) ){
			$urlVisitInfo = self::$objDatabase->getReferralCountryStartEndVisit( $arrRequest['city'], 'city' );
		}else{
			$urlVisitInfo = self::$objDatabase->getReferralCountryStartEndVisit( $arrRequest['countryId'] );
		}

		$table  = '';
		$total_unique_visitors = $total_page_views = $total_visitors = 0;
		$yArray=array('visitors'=>array(),'pageViews'=>array(),'firstTimeVisitors'=>array());
		foreach($arrDetails as $key => $row){
			$table .='<tr><td>'. date('D d F', strtotime( $key ) ).'</td><td>'.$row['total_unique_visitors'].'</td><td>'.$row['total_visitors'].'</td><td>'.$row['total_page_views'].'</td></tr>';
			$total_uniquedata += $row['total_unique_visitors'];
			$total_page_views += $row['total_page_views'];
			$total_visitors += $row['total_visitors'];
            array_push($yArray['visitors'], array( $key,  $row['total_visitors'] ) );
            array_push($yArray['pageViews'], array( $key, $row['total_page_views'] ) );
            array_push($yArray['firstTimeVisitors'], array( $key, $row['total_unique_visitors'] ) );
			array_push($yArray['newVisitor'], array( $key, (float) ( 100 * $row['total_unique_visitors'] )/ $row['total_visitors'] ) );
			
			if( $maxy < $row['total_unique_visitors'] ){
				$maxy = $row['total_unique_visitors'];
			}
			if( $maxy < $row['total_page_views'] ){
				$maxy = $row['total_page_views'];
			}
			if( $maxy < $row['total_visitors'] ){
				$maxy = $row['total_visitors'];
			}
		}
		//echo '<pre>';
		//print_r($yArray['pageViews']);
		//echo '</pre>';
		
		$html = '<div class="referral_url_history">';
		$html .= '<div class="referral_info_panel"><a href="#" class="linkRemoveData"><span class="dashicons dashicons-no"></span></a><table>';
		$html .= '<tr><td></td><td>New visitors</td><td>Visitors</td><td>Total pages viewed</td></tr>';
		$html .= '<tr><td>Cumulative data	</td><td>'. number_format( $total_uniquedata ) .'</td><td>'. number_format( $total_visitors) .'</td><td>'. number_format($total_page_views) .'</td></tr>';
		$html .= '<tr><td colspan="4"><div class="urlVisitInfo"><span>'.sprintf(_('First hits time %s'), $urlVisitInfo['first_visit'] ).'</span><span>'.sprintf(_('Last hits time %s'), $urlVisitInfo['last_visit'] ).'</span></div></td></tr>';
		$html .= '</table></div>';
		$html .= sprintf('<div class="chart_panel"><div id="referral_chart" data-maxy="%s" data-pageviews=\'%s\' data-visitors=\'%s\' data-firsttimevisitors=\'%s\' data-newvisitor=\'%s\' data-colors=\'%s\' data-totalpageviews=\'%s\' data-tdays=\'%s\'></div></div>',
						$maxy,
						 json_encode( array_reverse( $yArray['pageViews'] ) ),
						 json_encode( array_reverse( $yArray['visitors'] ) ),
						 json_encode( array_reverse( $yArray['firstTimeVisitors'] ) ),
						 json_encode( array_reverse( $yArray['newVisitor'] ) ),
						 json_encode($colors),
						 count($yArray['pageViews']),
						 $tDays);
		$html .= '<div class="referral_url_history_records">';
		$html .= '<table><thead><tr><th>Day</th><th>New visitors</th><th>Visitors</th><th>Pages viewed</th></tr></thead></table><div><table>';
		$html.=$table;
		$html.='</table></div></div></div>';
		echo $html;
		
        wp_die();
			
	}
	static function wsm_getContentUrlDayView(){
        $arrRequest=array();
        $arrResponse=array();
		
		$arrRequest= $_REQUEST;
		$nDays= 30;
		$tDays=3;
        
        $colors=array('rgba(244,81,81,1)','rgba(251,194,70,1)','rgba(87,135,184,1)','rgba(0,128,0,1)');

		$arrDetails=self::$objDatabase->getContentURLDayWiseStats($arrRequest); 
		$urlVisitInfo=self::$objDatabase->getReferralCountryStartEndVisit($arrRequest['id'], 'visitEntryURLId');

		//echo 2;		
		$table  = '';
		$maxy = 0;
		$total_unique_visitors = $total_page_views = $total_visitors = 0;
		$yArray=array('visitors'=>array(),'pageViews'=>array(),'firstTimeVisitors'=>array());
		foreach($arrDetails as $key => $row){
			$table .='<tr><td>'. date('D d F', strtotime( $key ) ).'</td><td>'. number_format( $row['total_unique_visitors'] ) .'</td><td>'.number_format( $row['total_visitors'] ).'</td><td>'. number_format( $row['total_page_views'] ) .'</td></tr>';
			$total_uniquedata += $row['total_unique_visitors'];
			$total_page_views += $row['total_page_views'];
			$total_visitors += $row['total_visitors'];
			
			if( $maxy < $row['total_unique_visitors'] ){
				$maxy = $row['total_unique_visitors'];
			}
			if( $maxy < $row['total_page_views'] ){
				$maxy = $row['total_page_views'];
			}
			if( $maxy < $row['total_visitors'] ){
				$maxy = $row['total_visitors'];
			}
            array_push($yArray['visitors'], array( $key,  $row['total_visitors'] ) );
            array_push($yArray['pageViews'], array( $key, $row['total_page_views'] ) );
            array_push($yArray['firstTimeVisitors'], array( $key, $row['total_unique_visitors'] ) );
			array_push($yArray['newVisitor'], array( $key, (float) ( 100 * $row['total_unique_visitors'] )/ $row['total_visitors'] ) );
		}
		
		
		$html = '<div class="referral_url_history">';
		$html .= '<div class="referral_info_panel"><a href="#" class="linkRemoveData"><span class="dashicons dashicons-no"></span></a><table>';
		$html .= '<tr><td></td><td>Hits</td><td>Visitors</td><td>New visitors</td></tr>';
		$html .= '<tr><td>Cumulative data	</td><td>'. number_format( $total_uniquedata ) .'</td><td>'. number_format( $total_visitors) .'</td><td>'. number_format($total_page_views) .'</td></tr>';
		$html .= '<tr><td colspan="4"><div class="urlVisitInfo"><span>'.sprintf(_('First hits time %s'), $urlVisitInfo['first_visit'] ).'</span><span>'.sprintf(_('Last hits time %s'), $urlVisitInfo['last_visit'] ).'</span></div></td></tr>';
		$html .= '</table></div>';
		$html .= sprintf('<div class="chart_panel"><div id="referral_chart" data-maxy="%s" data-pageviews=\'%s\' data-visitors=\'%s\' data-firsttimevisitors=\'%s\' data-newvisitor=\'%s\' data-colors=\'%s\' data-totalpageviews=\'%s\' data-tdays=\'%s\'></div></div>',
						$maxy,	
						 json_encode( array_reverse( $yArray['pageViews'] ) ),
						 json_encode( array_reverse( $yArray['visitors'] ) ),
						 json_encode( array_reverse( $yArray['firstTimeVisitors'] ) ),
						 json_encode( array_reverse( $yArray['newVisitor'] ) ),
						 json_encode($colors),
						 count($yArray['pageViews']),
						 $tDays);
		$html .= '<div class="referral_url_history_records">';
		$html .= '<table><thead><tr><th>Day</th><th>New visitors</th><th>Visitors</th><th>Hits</th></tr></thead></table><div><table>';
		$html.=$table;
		$html.='</table></div></div></div>';
		echo $html;
        
        wp_die();
	}
	static function wsm_getReferralOSDetails(){
        $arrRequest=array();
        $arrResponse=array();

		$arrRequest= $_REQUEST;
		$arrRequest['from']	= date('Y-m-d', strtotime( '-1 month', strtotime( $arrRequest['from'] ) ) );		
		/**
		 * Chart row data
		 */
		$nDays=get_option(WSM_PREFIX.'ChartDays');
        $nDays=($nDays!='' && $nDays>0) ?$nDays:30;
		//$nDays= 60;
		$tDays=3;
        
        $colors=array('rgba(244,81,81,1)','rgba(251,194,70,1)','rgba(87,135,184,1)','rgba(0,128,0,1)');
		/* End
		 *
		 */
		
		$arrDetails=self::$objDatabase->getReferralOSStats( 'Normal', $arrRequest);
		$urlVisitInfo = self::$objDatabase->getReferralDeviceStartEndVisit( $arrRequest['id'] , $arrRequest['rtype']);

		$table  = '';
		$total_unique_visitors = $total_page_views = $total_visitors = 0;
		$yArray=array('visitors'=>array(),'pageViews'=>array(),'firstTimeVisitors'=>array());
		foreach($arrDetails as $key => $row){
			$table .='<tr><td>'. date('D d F', strtotime( $key ) ).'</td><td>'.$row['total_unique_visitors'].'</td><td>'.$row['total_visitors'].'</td><td>'.$row['total_page_views'].'</td></tr>';
			$total_uniquedata += $row['total_unique_visitors'];
			$total_page_views += $row['total_page_views'];
			$total_visitors += $row['total_visitors'];
            array_push($yArray['visitors'], array( $key,  $row['total_visitors'] ) );
            array_push($yArray['pageViews'], array( $key, $row['total_page_views'] ) );
            array_push($yArray['firstTimeVisitors'], array( $key, $row['total_unique_visitors'] ) );
			array_push($yArray['newVisitor'], array( $key, (float) ( 100 * $row['total_unique_visitors'] )/ $row['total_visitors'] ) );
			
			if( $maxy < $row['total_unique_visitors'] ){
				$maxy = $row['total_unique_visitors'];
			}
			if( $maxy < $row['total_page_views'] ){
				$maxy = $row['total_page_views'];
			}
			if( $maxy < $row['total_visitors'] ){
				$maxy = $row['total_visitors'];
			}
		}
		//echo '<pre>';
		//print_r($yArray['pageViews']);
		//echo '</pre>';
		
		$html = '<div class="referral_url_history">';
		$html .= '<div class="referral_info_panel"><a href="#" class="linkRemoveData"><span class="dashicons dashicons-no"></span></a><table>';
		$html .= '<tr><td></td><td>New visitors</td><td>Visitors</td><td>Total pages viewed</td></tr>';
		$html .= '<tr><td>Cumulative data	</td><td>'. number_format( $total_uniquedata ) .'</td><td>'. number_format( $total_visitors) .'</td><td>'. number_format($total_page_views) .'</td></tr>';
		$html .= '<tr><td colspan="4"><div class="urlVisitInfo"><span>'.sprintf(_('First hits time %s'), $urlVisitInfo['first_visit'] ).'</span><span>'.sprintf(_('Last hits time %s'), $urlVisitInfo['last_visit'] ).'</span></div></td></tr>';
		$html .= '</table></div>';
		$html .= sprintf('<div class="chart_panel"><div id="referral_chart" data-maxy="%s" data-pageviews=\'%s\' data-visitors=\'%s\' data-firsttimevisitors=\'%s\' data-newvisitor=\'%s\' data-colors=\'%s\' data-totalpageviews=\'%s\' data-tdays=\'%s\'></div></div>',
						$maxy,
						 json_encode( array_reverse( $yArray['pageViews'] ) ),
						 json_encode( array_reverse( $yArray['visitors'] ) ),
						 json_encode( array_reverse( $yArray['firstTimeVisitors'] ) ),
						 json_encode( array_reverse( $yArray['newVisitor'] ) ),
						 json_encode($colors),
						 count($yArray['pageViews']),
						 $tDays);
		$html .= '<div class="referral_url_history_records">';
		$html .= '<table><thead><tr><th>Day</th><th>New visitors</th><th>Visitors</th><th>Pages viewed</th></tr></thead></table><div><table>';
		$html.=$table;
		$html.='</table></div></div></div>';
		echo $html;
		
        wp_die();
			
	}
    static function wsm_getReferrerUrlDetails(){
        $arrRequest=array();
        $arrResponse=array();
		
		$arrRequest= $_REQUEST;
		
		/**
		 * Chart row data
		 */
		$nDays=get_option(WSM_PREFIX.'ChartDays');
        $nDays=($nDays!='' && $nDays>0) ?$nDays:30;
		//$nDays= 60;
		$tDays=3;
        
        $colors=array('rgba(244,81,81,1)','rgba(251,194,70,1)','rgba(87,135,184,1)','rgba(0,128,0,1)');
		/* End
		 *
		 */
		
		$arrDetails=self::$objDatabase->getReferralSiteStats($arrRequest['condition'],$arrRequest);
		$urlVisitInfo=self::$objDatabase->getReferralSiteStartEndVisit($arrRequest['id']);
		$table  = '';
		$maxy = 0;
		$total_unique_visitors = $total_page_views = $total_visitors = 0;
		$yArray=array('visitors'=>array(),'pageViews'=>array(),'firstTimeVisitors'=>array());
		foreach($arrDetails as $key => $row){
			$table .='<tr><td>'. date('D d F', strtotime( $key ) ).'</td><td>'.$row['total_unique_visitors'].'</td><td>'.$row['total_visitors'].'</td><td>'.$row['total_page_views'].'</td></tr>';
			$total_uniquedata += $row['total_unique_visitors'];
			$total_page_views += $row['total_page_views'];
			$total_visitors += $row['total_visitors'];
			
			if( $maxy < $row['total_unique_visitors'] ){
				$maxy = $row['total_unique_visitors'];
			}
			if( $maxy < $row['total_page_views'] ){
				$maxy = $row['total_page_views'];
			}
			if( $maxy < $row['total_visitors'] ){
				$maxy = $row['total_visitors'];
			}
            array_push($yArray['visitors'], array( $key,  $row['total_visitors'] ) );
            array_push($yArray['pageViews'], array( $key, $row['total_page_views'] ) );
            array_push($yArray['firstTimeVisitors'], array( $key, $row['total_unique_visitors'] ) );
			array_push($yArray['newVisitor'], array( $key, (float) ( 100 * $row['total_unique_visitors'] )/ $row['total_visitors'] ) );
		}
		
		
		$html = '<div class="referral_url_history">';
		$html .= '<div class="referral_info_panel"><a href="#" class="linkRemoveData"><span class="dashicons dashicons-no"></span></a><table>';
		$html .= '<tr><td></td><td>New visitors</td><td>Visitors</td><td>Pages viewed</td></tr>';
		$html .= '<tr><td>Cumulative data	</td><td>'. number_format( $total_uniquedata ) .'</td><td>'. number_format( $total_visitors) .'</td><td>'. number_format($total_page_views) .'</td></tr>';
		$html .= '<tr><td colspan="4"><div class="urlVisitInfo"><span>'.sprintf(_('First hits time %s'), $urlVisitInfo['first_visit'] ).'</span><span>'.sprintf(_('Last hits time %s'), $urlVisitInfo['last_visit'] ).'</span></div></td></tr>';
		$html .= '</table></div>';
		$html .= sprintf('<div class="chart_panel"><div id="referral_chart" data-maxy="%s" data-pageviews=\'%s\' data-visitors=\'%s\' data-firsttimevisitors=\'%s\' data-newvisitor=\'%s\' data-colors=\'%s\' data-totalpageviews=\'%s\' data-tdays=\'%s\'></div></div>',
						$maxy,	
						 json_encode( array_reverse( $yArray['pageViews'] ) ),
						 json_encode( array_reverse( $yArray['visitors'] ) ),
						 json_encode( array_reverse( $yArray['firstTimeVisitors'] ) ),
						 json_encode( array_reverse( $yArray['newVisitor'] ) ),
						 json_encode($colors),
						 count($yArray['pageViews']),
						 $tDays);
		$html .= '<div class="referral_url_history_records">';
		$html .= '<table><thead><tr><th>Day</th><th>New visitors</th><th>Visitors</th><th>Pages viewed</th></tr></thead></table><div><table>';
		$html.=$table;
		$html.='</table></div></div></div>';
		echo $html;
        
        wp_die();
    }
    static function wsm_getUOSummary(){
        $arrRequest=array();
        $arrResponse=array();
        if(isset($_REQUEST['requests'])){
            $strRequest=stripslashes($_REQUEST['requests']);
            $arrRequest=json_decode($strRequest);            
            foreach($arrRequest as $request){                
                switch($request){
                    case WSM_PREFIX.'_recentVisitedPages':
                        $arrResponse[WSM_PREFIX.'_recentVisitedPages'] = do_shortcode("[".WSM_PREFIX."_showRecentVisitedPages]");
                    break;
                    case WSM_PREFIX.'_popularReferrersList':
                        $arrResponse[WSM_PREFIX.'_popularReferrersList'] =  do_shortcode("[".WSM_PREFIX."_showPopularReferrers]");
                    break;
                    case WSM_PREFIX.'_popularPagesList':
                        $arrResponse[WSM_PREFIX.'_popularPagesList'] =  do_shortcode("[".WSM_PREFIX."_showPopularPages]");
                    break;
                    case WSM_PREFIX.'_mostActiveVisitorsList':
                        $arrResponse[WSM_PREFIX.'_mostActiveVisitorsList'] =  do_shortcode("[".WSM_PREFIX."_showMostActiveVisitors]");
                    break;
                    case WSM_PREFIX.'_mostActiveVisitorsGeoLocation':
                        $jasArray=do_shortcode("[".WSM_PREFIX."_showMostActiveVisitorsGeo call='ajax']");
                        $arrResponse[WSM_PREFIX.'_mostActiveVisitorsGeoLocation'] = $jasArray ;
                    break;
                    case WSM_PREFIX.'_recentVisitedPagesdetailsList':
                        $arrResponse[WSM_PREFIX.'_recentVisitedPagesdetailsList']= do_shortcode("[".WSM_PREFIX."_showRecentVisitedPagesDetails]");
                    break;
                    case WSM_PREFIX.'_mostActiveVisitorsDetailsList':
                        $arrResponse[WSM_PREFIX.'_mostActiveVisitorsDetailsList']= do_shortcode("[".WSM_PREFIX."_showMostActiveVisitorsDetails]");
                    break;
                    case WSM_PREFIX.'_visitorsCountListByCity':
                        $arrResponse[WSM_PREFIX.'_visitorsCountListByCity']= do_shortcode("[".WSM_PREFIX."_showActiveVisitorsByCity]");
                    break;
                    case WSM_PREFIX.'_visitorsCountListByCountry':
                        $arrResponse[WSM_PREFIX.'_visitorsCountListByCountry']= do_shortcode("[".WSM_PREFIX."_showActiveVisitorsByCountry]");
                    break; 
                    case WSM_PREFIX.'TopTitle':
                        $onlineVisitors=self::$objDatabase->fnGetTotalVisitorsCount('Online');
                        $browsingPages=self::$objDatabase->fnGetTotalBrowsingPages();
                        $str=__('Users Online','wp-stats-manager').'&nbsp;:&nbsp;'.$onlineVisitors.$subTitle;
                        $arrResponse[WSM_PREFIX.'TopTitle']=array(WSM_PREFIX.'Browsing'=>$browsingPages,WSM_PREFIX.'Online'=>$onlineVisitors);
                    break;                   
                }
            }
        }
        if(count($arrResponse)>0){
            echo json_encode($arrResponse);
        }else{
            echo 'No';
        }
        wp_die();
    }
    static function wsm_getTimezoneByCountry(){
        $countryCode=(isset($_REQUEST['code']) && $_REQUEST['code']!='')?$_REQUEST['code']:'';
        echo wsmFnGetTimeZoneByCountry($countryCode);
        wp_die();
    }
    static function wsm_fnCreateImportantViews(){
		
        $newTimeZone=wsmCurrentGetTimezoneOffset();
        $visitLastActionTime="CONVERT_TZ(visitLastActionTime,'+00:00','".$newTimeZone."')";        
        $sql="SELECT DATE_FORMAT({$visitLastActionTime},'%Y-%m-%d') as recordDate, COUNT(*) as visitors FROM ".self::$tablePrefix."_logUniqueVisit GROUP BY DATE_FORMAT({$visitLastActionTime},'%Y-%m-%d')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_dateWiseVisitors',$sql);
        
        $firstVisitTime="CONVERT_TZ(firstVisitTime,'+00:00','".$newTimeZone."')";
        $sql="SELECT DATE_FORMAT({$firstVisitTime},'%Y-%m-%d') as recordDate, COUNT(visitorId) as visitors FROM ".self::$tablePrefix."_uniqueVisitors GROUP BY DATE_FORMAT({$firstVisitTime},'%Y-%m-%d')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_dateWiseFirstVisitors',$sql);
        
        $sql="SELECT DATE_FORMAT({$visitLastActionTime},'%Y-%m-%d') as recordDate, SUM(totalViews) as pageViews  FROM ".self::$tablePrefix."_pageViews  GROUP BY DATE_FORMAT({$visitLastActionTime},'%Y-%m-%d')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_dateWisePageViews',$sql);
        $sql="SELECT DATE_FORMAT({$visitLastActionTime},'%Y-%m-%d') as recordDate, COUNT(*) as bounce FROM ".self::$tablePrefix."_bounceVisits GROUP BY DATE_FORMAT({$visitLastActionTime},'%Y-%m-%d')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_dateWiseBounce',$sql);
        $sql="SELECT dwb.recordDate, dwb.bounce, dwp.pageViews, dwv.visitors, ((dwb.bounce/dwp.pageViews)*100) AS bRatePageViews, ((dwb.bounce/dwv.visitors)*100) AS bRateVisitors FROM ".self::$tablePrefix."_dateWiseBounce dwb LEFT JOIN ".self::$tablePrefix."_dateWisePageViews dwp ON dwb.recordDate=dwp.recordDate LEFT JOIN ".self::$tablePrefix."_dateWiseVisitors dwv ON dwb.recordDate=dwv.recordDate";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_dateWiseBounceRate',$sql);
        
        
                
        self::wsm_createHourWiseViews();
        update_option(WSM_PREFIX.'_dailyReportedTime',wsmGetCurrentDateByTimeZone('Y-m-d'));
    }
    static function wsm_createMonthWiseViews(){
        $newTimeZone=wsmCurrentGetTimezoneOffset();
        $visitLastActionTime="CONVERT_TZ(visitLastActionTime,'+00:00','".$newTimeZone."')";    
        $sql="SELECT DATE_FORMAT({$visitLastActionTime},'%Y-%m') as recordMonth, COUNT(*) as visitors FROM ".self::$tablePrefix."_logUniqueVisit GROUP BY DATE_FORMAT({$visitLastActionTime},'%Y-%m')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_monthWiseVisitors',$sql);
        
        $sql="SELECT DATE_FORMAT({$visitLastActionTime},'%Y-%m') as recordMonth, SUM(totalViews) as pageViews  FROM ".self::$tablePrefix."_pageViews  GROUP BY DATE_FORMAT({$visitLastActionTime},'%Y-%m')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_monthWisePageViews',$sql);
        
        $firstVisitTime="CONVERT_TZ(firstVisitTime,'+00:00','".$newTimeZone."')";
        $sql="SELECT DATE_FORMAT({$firstVisitTime},'%Y-%m') as recordMonth, COUNT(visitorId) as visitors FROM ".self::$tablePrefix."_uniqueVisitors GROUP BY DATE_FORMAT({$firstVisitTime},'%Y-%m')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_monthWiseFirstVisitors',$sql);
        
        $sql="SELECT DATE_FORMAT({$visitLastActionTime},'%Y-%m') as recordMonth, COUNT(*) as bounce FROM ".self::$tablePrefix."_bounceVisits GROUP BY DATE_FORMAT({$visitLastActionTime},'%Y-%m-')";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_monthWiseBounce',$sql);
        $sql="SELECT mwb.recordMonth, mwb.bounce, mwp.pageViews, mwv.visitors, ((mwb.bounce/mwp.pageViews)*100) AS bRatePageViews, ((mwb.bounce/mwv.visitors)*100) AS bRateVisitors FROM ".self::$tablePrefix."_monthWiseBounce mwb LEFT JOIN ".self::$tablePrefix."_monthWisePageViews mwp ON mwb.recordMonth=mwp.recordMonth LEFT JOIN ".self::$tablePrefix."_monthWiseVisitors mwv ON mwb.recordMonth=mwv.recordMonth";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_monthWiseBounceRate',$sql);
    }
    static function wsm_createHourWiseViews(){
        $newTimeZone=wsmCurrentGetTimezoneOffset();
        $visitLastActionTime="CONVERT_TZ(visitLastActionTime,'+00:00','".$newTimeZone."')";
        $firstActionVisitTime="CONVERT_TZ(firstActionVisitTime,'+00:00','".$newTimeZone."')";
        $sql="SELECT HOUR({$visitLastActionTime}) as hour, SUM(totalViews) as pageViews FROM ".self::$tablePrefix."_pageViews WHERE {$visitLastActionTime} >= '".wsmGetCurrentDateByTimeZone('Y-m-d 00:00:00')."' GROUP BY HOUR({$visitLastActionTime})";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_hourWisePageViews',$sql);
         $sql="SELECT HOUR({$visitLastActionTime}) as hour, COUNT(*) as bounce FROM ".self::$tablePrefix."_bounceVisits WHERE {$visitLastActionTime} >= '".wsmGetCurrentDateByTimeZone('Y-m-d 00:00:00')."' GROUP BY HOUR({$visitLastActionTime})";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_hourWiseBounce',$sql);
        $sql="SELECT HOUR({$firstActionVisitTime}) as hour, COUNT(*) as visitors FROM ".self::$tablePrefix."_logUniqueVisit WHERE {$firstActionVisitTime} >= '".wsmGetCurrentDateByTimeZone('Y-m-d 00:00:00')."' GROUP BY HOUR({$firstActionVisitTime})";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_hourWiseVisitors',$sql);
        $firstVisitTime="CONVERT_TZ(firstVisitTime,'+00:00','".$newTimeZone."')";
        $sql="SELECT hwb.hour, hwb.bounce, hwp.pageViews, hwv.visitors, ((hwb.bounce/hwp.pageViews)*100) AS bRatePageViews, ((hwb.bounce/hwv.visitors)*100) AS bRateVisitors FROM ".self::$tablePrefix."_hourWiseBounce hwb LEFT JOIN ".self::$tablePrefix."_hourWisePageViews hwp ON hwb.hour=hwp.hour LEFT JOIN ".self::$tablePrefix."_hourWiseVisitors hwv ON hwb.hour=hwv.hour";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_hourWiseBounceRate',$sql);
        $sql="SELECT HOUR({$firstVisitTime}) as hour, COUNT(*) as visitors FROM ".self::$tablePrefix."_uniqueVisitors WHERE {$firstVisitTime} >= '".wsmGetCurrentDateByTimeZone('Y-m-d 00:00:00')."' GROUP BY HOUR({$firstVisitTime})";
        self::wsmCreateDatabaseView(self::$tablePrefix.'_hourWiseFirstVisitors',$sql);      
    }
    static function wsm_dailyScheduler(){
        self::wsm_createHourWiseViews();
    }
    function wsm_correctDatabaseTables(){

    }
	static function wsm_deleteIpAddress(){
		$address = sanitize_text_field($_POST['ip']);
		
		$result = array();
		try{
			$ipAddresses = get_option('exclusion_ip_address_list');
			unset( $ipAddresses[ $address ] );
			update_option('exclusion_ip_address_list', $ipAddresses);
				$result['status'] = 1;
				$result['message'] = __('Record is deleted successfully');	
		}catch(Exception $e){
			$result['status'] = 0;
			$result['message'] = $e->getMessage();
		}
		echo json_encode($result);
		die();
	}
	
	static function wsm_updateIpAddress(){

		$address = sanitize_text_field($_POST['ip']);
		$status  = sanitize_text_field($_POST['status']);
		$result = array();
		try{
			$ipAddresses = get_option('exclusion_ip_address_list');
			$ipAddresses[ $address ] = $status;
			update_option('exclusion_ip_address_list', $ipAddresses);
			$result['status'] = 1;
		}catch(Exception $e){
			$result['status'] = 0;
			$result['message'] = $e->getMessage();
		}
		echo json_encode($result);
		die();
	}
	
	static function wsm_save_ipadress(){
		$address = sanitize_text_field($_POST['ipadress']);
		
		$result = array();
		try{
		if( filter_var( $address, FILTER_VALIDATE_IP ) ){
			$ipAddresses = get_option('exclusion_ip_address_list');
			if( !isset($ipAddresses) || !array_key_exists( $address, $ipAddresses ) ){
				$ipAddresses[$address] = 1;
				update_option('exclusion_ip_address_list', $ipAddresses);
				$count = count($ipAddresses);
				$result['data'] = '<tr id="row_'.$count.'"><td>'.$count.'</td><td>'.$address.'</td><td><label class="switch"><input data-ipaddress="'.$address.'" type="checkbox" checked><div class="slider round"></div></label></td><td><a href="#" data-row="'.$count.'" data-ipaddress="'.$address.'" class="deleteIP button button-secondary">'.__('Delete','wp-stats-manager').'</a></td></tr>';
				$result['status'] = 1;
				$result['message'] = __('ip address is successfully added.','wp-stats-manager');	
			}else{
				$result['status'] = 0;
				$result['message'] = __('Entered ip address is already exist in systems.','wp-stats-manager');
			}
		}else{
			$result['status'] = 0;
			$result['message'] = __('Entered ip address is already exist in systems.','wp-stats-manager');
		}
		}catch(Exception $e){
			$result['status'] = 0;
			$result['message'] = $e->getMessage();
		}
		echo json_encode($result);
		die();
	}
    static function wsm_admin_init(){
         global $_wp_admin_css_colors, $wsmAdminPageHooks;
         $theme= get_user_option('admin_color');
         if($theme=='' || is_null($theme) || $theme==0){
             $theme='fresh';
         }
         $wsmAdminColors = $_wp_admin_css_colors[$theme];
         update_option('wsmAdminColors',$wsmAdminColors);
         add_action('admin_enqueue_scripts', array('wsmInitPlugin',WSM_PREFIX. '_adminIncludeScripts'));
		 
		 if( ( isset($_REQUEST['page']) && array_key_exists($_REQUEST['page'],$wsmAdminPageHooks) ) )
		 {
         add_action('admin_enqueue_scripts', array('wsmInitPlugin',WSM_PREFIX. '_commonScript'));
		 }
		 
		 self::wsm_checkView();
         //add_action('admin_enqueue_scripts', array('wsmInitPlugin',WSM_PREFIX. '_registerWidgetStyle'));
         //add_action( 'add_meta_boxes', array('wsmInitPlugin',WSM_PREFIX.'_registerMetaboxes') );
       //  add_action('admin_print_styles', array('wsmInitPlugin',WSM_PREFIX. '_adminIncludeCSS'));
    }
	/*
	* Check view exist or not
	*/
	static function wsm_checkView(){
		global $wpdb, $missing_views;
		$sql = 'SELECT count(*) FROM INFORMATION_SCHEMA.VIEWS WHERE table_schema = "'.DB_NAME.'" AND table_name LIKE "%_wsm%"';
		
		$exist = $wpdb->get_var( $sql );
		if( !$exist || ( $exist && $exist < 19 ) ){
			add_action( 'admin_notices', array('wsmInitPlugin',WSM_PREFIX. '_viewError') );
			
			if( isset( $_GET['action'] ) && $_GET['action'] == 'fixed_db_issue' && !isset( $_GET['success'] ) ){
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_pageViews');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_uniqueVisitors');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_bounceVisits');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_visitorInfo');
			
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_monthWiseVisitors');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_monthWisePageViews');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_monthWiseFirstVisitors');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_monthWiseBounce');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_monthWiseBounceRate');
			
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_dateWiseVisitors');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_dateWiseFirstVisitors');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_dateWisePageViews');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_dateWiseBounce');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_dateWiseBounceRate');
			
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_hourWiseBounce');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_hourWiseBounceRate');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_hourWiseFirstVisitors');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_hourWisePageViews');
				$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_hourWiseVisitors');
				
				self::wsm_fnCreateImportantViews();
				self::wsm_createMonthWiseViews();
				self::wsm_createHourWiseViews();
		        $sql='SELECT LV.visitId, LV.URLId, LV.keyword, LV.refererUrlId, LU.countryId, LU.regionId, COUNT(*) As totalViews, max(LV.serverTime) AS visitLastActionTime FROM '.self::$tablePrefix.'_logVisit LV LEFT JOIN '.self::$tablePrefix.'_logUniqueVisit LU ON LV.visitId=LU.id GROUP BY LV.visitId, LV.URLId';
		        self::wsmCreateDatabaseView(self::$tablePrefix.'_pageViews',$sql);
                
		        $sql='SELECT LU.id, LU.visitorId,sum(LU.totalTimeVisit) as totalTimeVisit,MIN(LV.serverTime) as firstVisitTime, LU.refererUrlId FROM '.self::$tablePrefix.'_logUniqueVisit LU LEFT JOIN '.self::$tablePrefix.'_logVisit LV ON LV.visitId=LU.id GROUP BY LU.visitorId';
		        self::wsmCreateDatabaseView(self::$tablePrefix.'_uniqueVisitors',$sql);
		        $sql='SELECT visitId, visitLastActionTime FROM '.self::$tablePrefix.'_pageViews GROUP BY visitId HAVING COUNT(URLId)=1';
		        self::wsmCreateDatabaseView(self::$tablePrefix.'_bounceVisits',$sql);        
      
		        $sql='SELECT LV.visitId,LU.userId, LV.serverTime,LU.visitLastActionTime, LV.urlId, COUNT(LV.urlId) as hits, UL.title, CONCAT(UL.protocol, UL.url) as url, CONCAT(UL2.protocol, UL2.url) as refUrl, LU.visitorId, LU.ipAddress,LU.city, C.alpha2Code,C.name as country, LU.deviceType, B.name as browser,OS.name as osystem, LU.latitude, LU.longitude,R.name as resolution, SE.name as searchEngine, TB.name as toolBar FROM '.self::$tablePrefix.'_logVisit LV LEFT JOIN '.self::$tablePrefix.'_logUniqueVisit LU ON LU.id=LV.visitId LEFT JOIN '.self::$tablePrefix.'_countries C ON C.id=LU.countryId LEFT JOIN '.self::$tablePrefix.'_browsers B ON B.id=LU.browserId LEFT JOIN '.self::$tablePrefix.'_resolutions R ON R.id=LU.resolutionId LEFT JOIN '.self::$tablePrefix.'_url_log UL ON LV.urlId=UL.id LEFT JOIN '.self::$tablePrefix.'_url_log UL2 ON LV.refererUrlId=UL2.id  LEFT JOIN '.self::$tablePrefix.'_searchEngines SE ON SE.id=UL.searchEngine LEFT JOIN '.self::$tablePrefix.'_toolBars TB ON TB.id=UL.toolBar LEFT JOIN '.self::$tablePrefix.'_oSystems OS ON OS.id=LU.oSystemId GROUP BY LV.visitId,LV.urlId ORDER BY LV.visitId DESC ,LV.serverTime DESC';
		        self::wsmCreateDatabaseView(self::$tablePrefix.'_visitorInfo',$sql);
				
				$sql = 'SELECT count(*) FROM INFORMATION_SCHEMA.VIEWS WHERE table_schema = "'.DB_NAME.'" AND table_name IN ( "'.self::$tablePrefix.'_pageViews", "'.self::$tablePrefix.'_uniqueVisitors", "'.self::$tablePrefix.'_bounceVisits", "'.self::$tablePrefix.'_visitorInfo", "'.self::$tablePrefix.'_monthWiseVisitors", "'.self::$tablePrefix.'_monthWisePageViews", "'.self::$tablePrefix.'_monthWiseFirstVisitors", "'.self::$tablePrefix.'_monthWiseBounce", "'.self::$tablePrefix.'_monthWiseBounceRate", "'.self::$tablePrefix.'_dateWiseVisitors", "'.self::$tablePrefix.'_dateWiseFirstVisitors", "'.self::$tablePrefix.'_dateWisePageViews", "'.self::$tablePrefix.'_dateWiseBounce", "'.self::$tablePrefix.'_dateWiseBounceRate", "'.self::$tablePrefix.'_hourWiseBounce", "'.self::$tablePrefix.'_hourWiseBounceRate", "'.self::$tablePrefix.'_hourWiseFirstVisitors", "'.self::$tablePrefix.'_hourWisePageViews", "'.self::$tablePrefix.'_hourWiseVisitors" )';
				$exist = $wpdb->get_var( $sql );
				$missing_views = 19 - $exist;
				if( $exist == 19 ){
					wp_redirect(admin_url('index.php?action=fixed_db_issue&success=true'));
					exit();	
				}
			}
		}else{
			if( isset( $_GET['action'] ) && $_GET['action'] == 'fixed_db_issue' && isset( $_GET['success'] ) && $_GET['success'] ){
				add_action( 'admin_notices', array('wsmInitPlugin',WSM_PREFIX. '_viewSuccess') );
			}	
		}
	}
	static function wsm_viewSuccess(){
	    ?>
	    <div class="notice notice-success">
			<p><strong><?php echo WSM_NAME; ?></strong></p>
	        <p><?php _e( 'Great! The database issue is fixed now.', 'wphr' ); ?></p>
	    </div>
	    <?php		
	}
	static function wsm_viewError(){
		global $missing_views;
	    ?>
	    <div class="notice notice-error">
			<p><strong><?php echo WSM_NAME; ?></strong></p>
			<?php
				if( isset( $_GET['success'] ) && $_GET['success'] ){
					?>
					<p><?php _e('Sorry for the trouble. Please contact plugin developer to fix this issue.'); ?></p>
					<?php
				}else{
					if( $missing_views ){
					?>
	        <p><?php echo sprintf( __( 'There is still %d tables are missing. Please click on below button to fix the issue.', 'wphr' ), $missing_views ); ?></p>
			<p><a class="primary button button-primary" href="<?php echo admin_url('index.php?action=fixed_db_issue'); ?>"><?php _e('Fix now!','wphr');?></a></p>
					<?php
						
					}else{
					?>
	        <p><?php _e( 'There is some of the tables are missing. Please click on below button to fix the issue.', 'wphr' ); ?></p>
			<p><a class="primary button button-primary" href="<?php echo admin_url('index.php?action=fixed_db_issue'); ?>"><?php _e('Fix now!','wphr');?></a></p>
					<?php
					}
				}
			?>
	    </div>
	    <?php		
	}
    static function wsm_adminIncludeScripts(){
        global $wsmAdminPageHooks, $wsmRequestArray;
        $arrJqPlot=array();
		
		$page = isset($wsmRequestArray['page']) && $wsmRequestArray['page']!='' ? sanitize_text_field($wsmRequestArray['page']):'';
		
        if( ( isset($_REQUEST['page']) && array_key_exists($_REQUEST['page'],$wsmAdminPageHooks) ) ){
            
			if( current_user_can('edit_others_pages') ){
				wp_register_style( WSM_PREFIX.'-jqplot-css', WSM_URL . 'css/jquery.jqplot.css', false, '1.0.0' );
				wp_enqueue_style( WSM_PREFIX.'-jqplot-css' );
            
				wp_register_style( WSM_PREFIX.'-jquery-style', WSM_URL . 'css/jquery-ui.css');
            	wp_enqueue_style( WSM_PREFIX.'-jquery-style' );
			}
        }
		if( !isset($_REQUEST['page']) ){
        	wp_register_style( WSM_PREFIX.'-widget-admin-style', WSM_URL . 'css/dashboard_widget.css', false, '1.0.0' );
			wp_enqueue_style( WSM_PREFIX.'-widget-admin-style' );	
		}

        wp_register_style( WSM_PREFIX.'-flag-css', WSM_URL.'css/flags.min.css', false, '1.0.0' );
        wp_enqueue_style( WSM_PREFIX.'-flag-css' );
		
		
		if( strpos($page, WSM_PREFIX) !== false ){
           	wp_register_style( WSM_PREFIX.'-custom-admin-style', WSM_URL . 'css/custom_admin.css', false, '3.2' );
            wp_enqueue_style( WSM_PREFIX.'-custom-admin-style' );
			
			 wp_register_style( WSM_PREFIX.'-modal', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css', false, '1.0.0' );
			 wp_enqueue_style( WSM_PREFIX.'-modal' );
			
		}
    }
	static function wsm_commonScript(){
		global $wsmRequestArray;
		$page = isset($wsmRequestArray['page']) && $wsmRequestArray['page']!='' ? sanitize_text_field($wsmRequestArray['page']):'';
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'postbox' );
        wp_enqueue_media();            
        wp_register_script( WSM_PREFIX.'-jqplot-excanvas', WSM_URL. 'js/excanvas.js',array(),'1.0.0',true );
       // wp_enqueue_script( WSM_PREFIX.'-jqplot-excanvas' );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-main', WSM_URL. 'js/jquery.jqplot.js',array(),'1.0.0',true );
        wp_register_script( WSM_PREFIX.'-jqplot-mobile', WSM_URL. 'js/jqplot.mobile.js',array(),'1.0.0',true );
        //wp_enqueue_script( WSM_PREFIX.'-jqplot-mobile' );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-cursor', WSM_URL. 'js/jqplot.cursor.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-highlighter', WSM_URL. 'js/jqplot.highlighter.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-canvasAxisTickRenderer', WSM_URL. 'js/jqplot.canvasAxisTickRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-dateAxisRenderer', WSM_URL. 'js/jqplot.dateAxisRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-categoryAxisRenderer', WSM_URL. 'js/jqplot.categoryAxisRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-canvasTextRenderer', WSM_URL. 'js/jqplot.canvasTextRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-enhancedLegendRenderer', WSM_URL. 'js/jqplot.enhancedLegendRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-enhancedPieLegendRenderer', WSM_URL. 'js/jqplot.enhancedPieLegendRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-logAxisRenderer', WSM_URL. 'js/jqplot.logAxisRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-barRenderer', WSM_URL. 'js/jqplot.barRenderer.js',array(),'1.0.0',true );
        wp_enqueue_script( WSM_PREFIX.'-jqplot-pieRenderer', WSM_URL. 'js/jqplot.pieRendererjs.js',array(),'1.0.0',true );
		
		if( current_user_can('edit_others_pages') && (strpos($page, WSM_PREFIX) !== false) )
		{
			wp_enqueue_script( WSM_PREFIX.'-modal', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js',array(),'1.0.0',true );
		}
		
		
      
        wp_enqueue_script( WSM_PREFIX.'-custom-admin-script', WSM_URL. 'js/custom_admin.js', array('jquery-ui-sortable'), '3.2',true );
        wp_localize_script(  WSM_PREFIX.'-custom-admin-script', WSM_PREFIX.'_ajaxObject', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
		
		wp_enqueue_script( WSM_PREFIX.'_slimselect', 'https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.25.0/slimselect.min.js','1.0.0',true );
		wp_register_style(WSM_PREFIX.'_slimselect_css', 'https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.25.0/slimselect.min.css', false, '1.0.0' );
		wp_enqueue_style(WSM_PREFIX.'_slimselect_css');
		
	}
    static function wsm_adminIncludeCSS(){

    }
    static function wsm_admin_menu(){
		
		global $wsmAdminPageHooks, $current_user;   
		
		
		
		if(!current_user_can('administrator'))
		{
			
				$WSMUserRoles = get_option(WSM_PREFIX.'UserRoles');
				if(empty($WSMUserRoles))
				{
					$WSMUserRoles = 'Administrator';
				}
				$wsmUserRole = explode(',',$WSMUserRoles);
				
				$roles_arr = array();
				
				foreach($wsmUserRole as $v) {
				  $roles_arr[] = strtolower($v);
				}
				
				$current_use_roles_ = $current_user->roles;
				if(is_array($current_use_roles_))
				{
					foreach($current_use_roles_ as $k=>$v)
					{
						if (in_array( strtolower($v), $roles_arr ) ) {
							$bAccessGranted = true;
							break;
							}
					}
				}else{
					$bAccessGranted = in_array( $current_use_roles_, $roles_arr );
				}
				if (!$bAccessGranted) {
						return;
				}
		
		}
		
		$capability = 'read';
		
		
		
		
        
        self::$objAdminInterface=new wsmAdminInterface();
        $page_title = __('Visitor Statistics','wp-stats-manager');
        $sub_menu_title = __('Visitor Statistics','wp-stats-manager');
        $capability = 'read';
        $menu_slug = WSM_PREFIX;
        add_menu_page($page_title, $sub_menu_title, '', $menu_slug);
        $wsmAdminPageHooks[$menu_slug.'_traffic']=add_submenu_page($menu_slug, __('Traffic','wp-stats-manager'),  __('Traffic','wp-stats-manager'), $capability, $menu_slug.'_traffic', array(self::$objAdminInterface,WSM_PREFIX.'ViewTraffic'));
        //add_submenu_page($menu_slug.'_traffic', __('Summary','wp-stats-manager'),  __('Summary','wp-stats-manager'), $capability, $menu_slug.'_trafsummary', array($objAdminInterface,WSM_PREFIX.'ViewTrafSummary'));
        $wsmAdminPageHooks[$menu_slug.'_trafficsrc']=add_submenu_page($menu_slug, __('Traffic Sources','wp-stats-manager'), __('Traffic Sources','wp-stats-manager'), $capability, $menu_slug.'_trafficsrc', array(self::$objAdminInterface,WSM_PREFIX.'ViewTrafficSources'));
        $wsmAdminPageHooks[$menu_slug.'_visitors']=add_submenu_page($menu_slug, __('Visitors','wp-stats-manager'), __('Visitors','wp-stats-manager'), $capability, $menu_slug.'_visitors', array(self::$objAdminInterface,WSM_PREFIX.'ViewVisitors'));
        $wsmAdminPageHooks[$menu_slug.'_content']=add_submenu_page($menu_slug, __('Content','wp-stats-manager'),  __('Content','wp-stats-manager'), $capability, $menu_slug.'_content', array(self::$objAdminInterface,WSM_PREFIX.'ViewContent'));
        $wsmAdminPageHooks[$menu_slug.'_ipexc']=add_submenu_page($menu_slug, __('I.P. Exclusion','wp-stats-manager'), __('I.P. Exclusion','wp-stats-manager'), $capability, $menu_slug.'_ipexc', array(self::$objAdminInterface,WSM_PREFIX.'ViewIPExclusion'));
        $wsmAdminPageHooks[$menu_slug.'_addons']=add_submenu_page($menu_slug, __('Add ons','wp-stats-manager'), __('Add ons','wp-stats-manager'), $capability, $menu_slug.'_addons', array(self::$objAdminInterface,WSM_PREFIX.'ViewAddOns'));
        $wsmAdminPageHooks[$menu_slug.'_settings']=add_submenu_page($menu_slug, __('Settings','wp-stats-manager'), __('Settings','wp-stats-manager'), $capability, $menu_slug.'_settings', array(self::$objAdminInterface,WSM_PREFIX.'ViewSettings'));
		remove_submenu_page( $menu_slug, $menu_slug );
       self::wsm_registerMetaboxes();
    }
    static function wsm_registerMetaboxes(){
        global $wsmAdminPageHooks,$wsmRequestArray;
        $wsmMetaBoxArray=array();
        $currentPage=(isset($wsmRequestArray['page']) && $wsmRequestArray['page']!='')?$wsmRequestArray['page']:'';
        $currentSubPage=(isset($wsmRequestArray['subPage']) && $wsmRequestArray['subPage']!='')?$wsmRequestArray['subPage']:'';
        switch($currentPage){
            case WSM_PREFIX.'_settings':

            break;
            case WSM_PREFIX.'_traffic':
               $currentSubPage=($currentSubPage=='')?'Summary':$currentSubPage;
               $wsmRequestArray['subPage']=$currentSubPage;
                switch($currentSubPage){
                    case 'Summary':
                        add_meta_box(WSM_PREFIX.'_genStats', __('General Stats','wp-stats-manager'), array(self::$objAdminInterface, 'fnShowGenStats' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'left','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_genStats']=__('General Stats','wp-stats-manager');
                        add_meta_box(WSM_PREFIX.'_geoLocation', __('Top 10 countries','wp-stats-manager'), array(self::$objAdminInterface, 'fnShowGeoLocationChart' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'right','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_geoLocation']=__('Top 10 countries','wp-stats-manager');
                        add_meta_box(WSM_PREFIX.'_todaysStats', __('Today','wp-stats-manager').'&nbsp;&nbsp;'.wsmGetCurrentDateByTimeZone('d M Y ').' <span class="currenttime2"></span>', array(self::$objAdminInterface, 'fnShowTodayStats' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'bottom','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_todaysStats']=__('Today','wp-stats-manager');
                        $nDays=get_option(WSM_PREFIX.'ChartDays');
                        $nDays=($nDays!='' && $nDays>0) ?$nDays:30;
                        add_meta_box(WSM_PREFIX.'_lastDays', __(sprintf('Last %d Days',$nDays),'wp-stats-manager'), array(self::$objAdminInterface, 'fnShowLastDaysStats' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'bottom','low');
                        $wsmMetaBoxArray[WSM_PREFIX.'_lastDays']=__('Last 2 Months','wp-stats-manager');
						

						$plugin_widget = get_option(WSM_PREFIX.'Plugin_widget');
						$report_stats_list = array('general_stats_new'	=>	__('General Stats','wp-stats-manager'),
													'daily_stats'	=>	__('Today Stats','wp-stats-manager'),
													'referral_website_stats'	=>	__('Referral Website','wp-stats-manager'),
													'search_engine_stats'	=>	__('Today Search Engines','wp-stats-manager'),
													'traffic_by_title_stats' => __('Title Stats','wp-stats-manager'),
													'top_search_engine_stats' => __('Top Search Engine Summary','wp-stats-manager'),
													'os_wise_visitor_stats'	=>	__('Operating System Stats','wp-stats-manager'),
													'browser_wise_visitor_stats'	=>	__('Today Browser Stats','wp-stats-manager'),
													'screen_wise_visitor_stats'	=>	__('Screen Resolution Stats','wp-stats-manager'),
													'country_wise_visitor_stats'	=>	__('Countries Stats (Top 10)','wp-stats-manager'),
													'city_wise_visitor_stats'	=>	__('Today Cities Stats','wp-stats-manager'),
													'recent_visit_pages' => __('Traffic By Title','wp-stats-manager'),
													'recent_active_visitors' => __('Users Online','wp-stats-manager'),
													'total_recent_visitors_geo' => __('GEO Locations','wp-stats-manager')
												);
						$default_report_stats_list = array('general_stats_new'	=>	array('fnshowGenStats'),
															'daily_stats'	=>	array('fnShowDailyStatBox'),
															'referral_website_stats'	=>	array( 'fnShowReffererStatBox' ),
															'search_engine_stats'	=>	array( 'fnShowReffererSearchEngineStatBox', 'fnShowSearchEngineSummary' ),
															'recent_visit_pages'	=>	array( 'fnShowRecentVisitedPages' ),
															'recent_active_visitors' => array( 'fnShowMostActiveVisitorsGeo' )
											);
						if( is_array( $plugin_widget ) ){
							$objWsmScheduledMail = new wsmScheduledMail();
							foreach( $plugin_widget as $screen => $widgets  ){
								if( $screen == 'normal' ){
									$screen = 'left';
								}else{
									$screen = 'right';
								}
								foreach( $widgets as $widget ){
									//echo $widget;
									if( isset( $default_report_stats_list[ $widget ] ) )
									{
										foreach( $default_report_stats_list[ $widget ] as $key => $function ){
											$title = $report_stats_list[ $widget ];
											if( $title == 'fnShowSearchEngineSummary' ){
												$title = 'Search Engines Summary View';
											}
											add_meta_box( $widget.'_'.$key, $title, array(self::$objAdminInterface, $function ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], $screen, 'low' );
										}
									}else{
										add_meta_box($widget, $report_stats_list[ $widget ], array($objWsmScheduledMail, $widget ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], $screen, 'low');
									}	
								}
							}
						}					
                        //add_meta_box(WSM_PREFIX.'_currentDay', __('Current Day','wp-stats-manager'), array(self::$objAdminInterface, 'fnShowCurrentStasChart' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'bottom','high');
                        //$wsmMetaBoxArray[WSM_PREFIX.'_currentDay']=__('Current Day','wp-stats-manager');
                       // add_meta_box(WSM_PREFIX.'_forecast', __('Forecast','wp-stats-manager'), array(self::$objAdminInterface, 'fnShowforeCast' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'bottom','high');
                        //$wsmMetaBoxArray[WSM_PREFIX.'_forecast']=__('Forecast','wp-stats-manager');
                        //add_meta_box(WSM_PREFIX.'_lastDaysChart', __('&nbsp;','wp-stats-manager'), array(self::$objAdminInterface, 'fnShowLastDaysChart' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'bottom','high');
                        //$wsmMetaBoxArray[WSM_PREFIX.'_lastDaysChart']=__('Last Days Chart','wp-stats-manager');

                    break;
                    case 'UsersOnline':
                        $tab=isset($wsmRequestArray['subTab'])&& $wsmRequestArray['subTab']!=""?$wsmRequestArray['subTab']:'';
                        if($tab!=''){                            
                            switch($tab){
                                case 'summary':
                                    $tabURL='admin.php?page='.WSM_PREFIX.'_traffic&subPage=UsersOnline&subTab=';
                                    $imgMag='<img src="'.WSM_URL.'images/mag.png" alt="'. __('Details','wp-stats-manager').'"/>';
                                    
                                    $imgMagnifier='<a href="'.admin_url($tabURL.'recent').'" title="'. __('Recent','wp-stats-manager').'">'.$imgMag.'</a>'.__('Recent','wp-stats-manager');
                                    add_meta_box(WSM_PREFIX.'_recentVisPages',$imgMagnifier, array(self::$objAdminInterface, 'fnShowRecentVisitedPages'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'top','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_recentVisPages']=__('Recent','wp-stats-manager');
                                    
                                    $imgMagnifier='<a href="'.admin_url($tabURL.'popPages').'" title="'. __('Popular Pages','wp-stats-manager').'">'.$imgMag.'</a>'.__('Popular Pages','wp-stats-manager');
                                    add_meta_box(WSM_PREFIX.'_popularPages', $imgMagnifier,array(self::$objAdminInterface, 'fnShowPopularPages'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'left','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_popularPages']=__('Popular Pages','wp-stats-manager');
                                    
                                    $imgMagnifier='<a href="'.admin_url($tabURL.'popReferrer').'" title="'. __('Popular Referrer','wp-stats-manager').'">'.$imgMag.'</a>'.__('Popular Referrer','wp-stats-manager');
                                    add_meta_box(WSM_PREFIX.'_popularReferrer',$imgMagnifier,array(self::$objAdminInterface, 'fnShowPopularReferrers'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'left','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_popularReferrer']=__('Popular Referrer','wp-stats-manager');
                                    
                                    $imgMagnifier='<a href="'.admin_url($tabURL.'geoLocation').'" title="'. __('Geo Location','wp-stats-manager').'">'.$imgMag.'</a>'.__('Geo Location','wp-stats-manager');
                                    add_meta_box(WSM_PREFIX.'_mostActiveVisitorsGeo',$imgMagnifier,array(self::$objAdminInterface, 'fnShowMostActiveVisitorsGeo'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'right','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_mostActiveVisitorsGeo']=__('Most Active Visitors','wp-stats-manager');
                                    $imgMagnifier='<a href="'.admin_url($tabURL.'mavis').'" title="'. __('Most Active Visitors','wp-stats-manager').'">'.$imgMag.'</a>&nbsp;'.__('Most Active Visitors','wp-stats-manager');
                                    add_meta_box(WSM_PREFIX.'_mostActiveVisitors', $imgMagnifier,array(self::$objAdminInterface, 'fnShowMostActiveVisitors'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'right','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_mostActiveVisitors']=__('Most Active Visitors','wp-stats-manager');
                                break;
                                case 'recent':
                                    add_meta_box(WSM_PREFIX.'_recentVisPagesDetails', __('Recent','wp-stats-manager'), array(self::$objAdminInterface, 'fnShowRecentVisitedPagesDetails'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'top','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_recentVisPagesDetails']=__('Recent','wp-stats-manager');
                                break;
                                case 'mavis':
                                    add_meta_box(WSM_PREFIX.'_mostActiveVisitorsDetails', __('Most Active Visitors','wp-stats-manager'),array(self::$objAdminInterface, 'fnShowMostActiveVisitorsDetails'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'top','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_mostActiveVisitorsDetails']=__('Most Active Visitors','wp-stats-manager');
                                break;
                                case 'popPages':                                    
                                    add_meta_box(WSM_PREFIX.'_popularPagesDetails', __('Popular Pages','wp-stats-manager'),array(self::$objAdminInterface, 'fnShowPopularPages'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'top','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_popularPagesDetails']=__('Popular Pages','wp-stats-manager');
                                break;
                                case 'popReferrer':
                                    add_meta_box(WSM_PREFIX.'_popularReferrerDetails', __('Popular Referrers','wp-stats-manager'),array(self::$objAdminInterface, 'fnShowPopularReferrers'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'top','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_popularReferrerDetails']=__('Popular Referrers','wp-stats-manager');
                                break;
                                case 'geoLocation':
                                    add_meta_box(WSM_PREFIX.'_mostActiveVisitorsGeoDetails', __('Geo Location','wp-stats-manager'),array(self::$objAdminInterface, 'fnShowMostActiveVisitorsGeoDetails'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'top','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_mostActiveVisitorsGeoDetails']=__('Most Active Visitors','wp-stats-manager');
                                    add_meta_box(WSM_PREFIX.'_activeVisitorsCountByCountry', __('Country','wp-stats-manager'),array(self::$objAdminInterface, 'fnShowActiveVistiorsCountByCountry'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'right','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_activeVisitorsCountByCountry']=__('Country','wp-stats-manager');
                                    add_meta_box(WSM_PREFIX.'_activeVisitorsCountByCity', __('City','wp-stats-manager'),array(self::$objAdminInterface, 'fnShowActiveVistiorsCountByCity'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'left','high');
                                    $wsmMetaBoxArray[WSM_PREFIX.'_activeVisitorsCountByCity']=__('City','wp-stats-manager');
                                break;
                            }
                        }

                    break;
                    case 'TrafStats': 
                        $arrPostData=wsmSanitizeFilteredPostData();   
                        $dateRangeTitle=wsmFnGetDateRangeTitle($arrPostData);
                        add_meta_box(WSM_PREFIX.'_dailyStatBox', __('Daily Stats','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowDailyStatBox'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'left','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_dailyStatBox']=__('Daily Stats','wp-stats-manager');
                        
                        add_meta_box(WSM_PREFIX.'_statFilterBox', __('Filter Results','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatFilterBox'),$wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'right','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_statFilterBox']=__('Filter Results','wp-stats-manager');
                        
                        add_meta_box(WSM_PREFIX.'_daysStatsBox', __('Days Graph','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowDaysStatsGraph' ), $wsmAdminPageHooks[WSM_PREFIX.'_traffic'],'bottom','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_daysStatsBox']=__('Days Graph','wp-stats-manager');
                    break;
                }
            break;
            case WSM_PREFIX.'_trafficsrc':
                $arrPostData=wsmSanitizeFilteredPostData();   
                $dateRangeTitle=wsmFnGetDateRangeTitle($arrPostData);
                $currentSubPage=$currentSubPage==''?'RefSites':$currentSubPage;
                $wsmRequestArray['subPage']=$currentSubPage;
                switch($currentSubPage){
                    case 'RefSites':
                        add_meta_box(WSM_PREFIX.'_referrerStats', __('Referral Websites','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowReffererStatBox' ), $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'left','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_referrerStats']=__('Referral Websites','wp-stats-manager');
                        add_meta_box(WSM_PREFIX.'_statFilterBox4Referral', __('Filter Results','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatFilterBox4Referral'),$wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'right','high');
                         $wsmMetaBoxArray[WSM_PREFIX.'_statFilterBox4Referral']=__('Filter Results','wp-stats-manager');
                         add_meta_box(WSM_PREFIX.'_topReferringSites',__('Top Referrer Sites','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle,array(self::$objAdminInterface, 'fnShowTopReferrerSites'),$wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'bottom','high');
                         $wsmMetaBoxArray[WSM_PREFIX.'_topReferringSites']=__('Top Referrer Sites','wp-stats-manager');
                    break;
                    case 'SearchEngines':
                        add_meta_box(WSM_PREFIX.'_referrerStats', __('Search Engines','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowReffererStatBox' ), $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'left','high');
                        add_meta_box(WSM_PREFIX.'_referrerSearchEngineSummary', __('Search Engines Summary View','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowSearchEngineSummary' ), $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'left','high');
                        $wsmMetaBoxArray[WSM_PREFIX.'_referrerStats']=__('Search Engines','wp-stats-manager');
                        add_meta_box(WSM_PREFIX.'_statFilterBox4Referral', __('Filter Results','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatFilterBox4Referral'),$wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'right','high');
                         $wsmMetaBoxArray[WSM_PREFIX.'_statFilterBox4Referral']=__('Filter Results','wp-stats-manager');
                         add_meta_box(WSM_PREFIX.'_topReferringSites',__('Top Search Engines','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle,array(self::$objAdminInterface, 'fnShowTopReferrerSites'),$wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'bottom','high');
                         $wsmMetaBoxArray[WSM_PREFIX.'_topReferringSites']=__('Top Search Engines','wp-stats-manager');
                    break;
                    case 'SearchKeywords':
                        $wsmMetaBoxArray[WSM_PREFIX.'_referrerStats']=__('Search Keywords','wp-stats-manager');
                        add_meta_box(WSM_PREFIX.'_topReferringSites', __('Latest Search Word Statistics','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatsSearchKeywords'),$wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'],'bottom','high');
                    break;
                }
            break;
            case WSM_PREFIX.'_visitors':
                $arrPostData=wsmSanitizeFilteredPostData(); 
				$dateRangeTitle=wsmFnGetDateRangeTitle($arrPostData);
                $currentSubPage=$currentSubPage==''?'bosl':$currentSubPage;

                switch($currentSubPage){
                    case 'bosl':
						add_meta_box(WSM_PREFIX.'_osType', __('Operating System Type','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowOsStatBox' ), $wsmAdminPageHooks[WSM_PREFIX.'_visitors'],'left','high');
						$wsmMetaBoxArray[WSM_PREFIX.'_osType']=__('Operating System Type','wp-stats-manager');
						
						add_meta_box(WSM_PREFIX.'_statFilterBox4Referral', __('Filter Results','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatFilterBox4Referral'),$wsmAdminPageHooks[WSM_PREFIX.'_visitors'],'right','high');
						 $wsmMetaBoxArray[WSM_PREFIX.'_statFilterBox4Referral']=__('Filter Results','wp-stats-manager');
						 
						 add_meta_box(WSM_PREFIX.'_visitorsDetails',__('OS/Browser/Screen Resolution','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle,array(self::$objAdminInterface, 'fnShowVisitorsDetails'),$wsmAdminPageHooks[WSM_PREFIX.'_visitors'],'bottom','high');
						 $wsmMetaBoxArray[WSM_PREFIX.'_visitorsDetails']=__('OS/Browser/Screen Resolution','wp-stats-manager');
						 break;
                    case 'GeoLocation':
					
						add_meta_box(WSM_PREFIX.'_geoLocation', __('Geo Location','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowGeoLocationStats' ), $wsmAdminPageHooks[WSM_PREFIX.'_visitors'],'left','high');
						$wsmMetaBoxArray[WSM_PREFIX.'_geoLocation']=__('Geo Location','wp-stats-manager');
						
						add_meta_box(WSM_PREFIX.'_geoLocationStatFilter', __('Filter Results','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatFilterBox4Referral'),$wsmAdminPageHooks[WSM_PREFIX.'_visitors'],'right','high');
						 $wsmMetaBoxArray[WSM_PREFIX.'_geoLocationStatFilter']=__('Filter Results','wp-stats-manager');
						 
						 add_meta_box(WSM_PREFIX.'_geoLocationVisitorsDetails',__('Country/City','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle,array(self::$objAdminInterface, 'fnShowGeoLocationDetails'),$wsmAdminPageHooks[WSM_PREFIX.'_visitors'],'bottom','high');
						 $wsmMetaBoxArray[WSM_PREFIX.'_geoLocationVisitorsDetails']=__('Country/City','wp-stats-manager');
						 break;
                    break;
                }
            break;
            case WSM_PREFIX.'_content':
            	$arrPostData=wsmSanitizeFilteredPostData(); 
				$dateRangeTitle=wsmFnGetDateRangeTitle($arrPostData);
            	$currentSubPage = $currentSubPage=='' ? 'byURL' : $currentSubPage;
                switch($currentSubPage){
                    case 'byURL':
						add_meta_box(WSM_PREFIX.'_contentURL', __('Title Cloud','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowTitleCloud' ), $wsmAdminPageHooks[WSM_PREFIX.'_content'],'left','high');
					$wsmMetaBoxArray[WSM_PREFIX.'_contentByURL']=__('Title Cloud','wp-stats-manager');
					
						add_meta_box(WSM_PREFIX.'_contentStatFilter', __('Filter Results','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatFilterBox4Referral'),$wsmAdminPageHooks[WSM_PREFIX.'_content'],'right','high');
					 $wsmMetaBoxArray[WSM_PREFIX.'_contentStatFilter']=__('Filter Results','wp-stats-manager');
					 
						 add_meta_box(WSM_PREFIX.'_contentURLStats',__('URL Stats','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle,array(self::$objAdminInterface, 'fnShowContentURLStats'),$wsmAdminPageHooks[WSM_PREFIX.'_content'],'bottom','high');
					 $wsmMetaBoxArray[WSM_PREFIX.'_contentURLStats']=__('URL Stats','wp-stats-manager');
                    break;
                    case 'byTitle':
						add_meta_box(WSM_PREFIX.'_contentURL', __('Title Cloud','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnShowTitleCloud' ), $wsmAdminPageHooks[WSM_PREFIX.'_content'],'left','high');
						$wsmMetaBoxArray[WSM_PREFIX.'_contentByURL']=__('Title Cloud','wp-stats-manager');
					
						add_meta_box(WSM_PREFIX.'_contentStatFilter', __('Filter Results','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle, array(self::$objAdminInterface, 'fnStatFilterBox4Referral'),$wsmAdminPageHooks[WSM_PREFIX.'_content'],'right','high');
					 $wsmMetaBoxArray[WSM_PREFIX.'_contentStatFilter']=__('Filter Results','wp-stats-manager');
					 
						 add_meta_box(WSM_PREFIX.'_contentURLStats',__('Title Stats','wp-stats-manager').'&nbsp;&nbsp;'.$dateRangeTitle,array(self::$objAdminInterface, 'fnShowContentURLStats'),$wsmAdminPageHooks[WSM_PREFIX.'_content'],'bottom','high');
					 $wsmMetaBoxArray[WSM_PREFIX.'_contentURLStats']=__('Title Stats','wp-stats-manager');
                    break;
                }
            break;
            case WSM_PREFIX.'_ipexc':
                $arrPostData=wsmSanitizeFilteredPostData();   
                $dateRangeTitle=wsmFnGetDateRangeTitle($arrPostData);
				$wsmRequestArray['subPage']='ipexc';
				add_meta_box(WSM_PREFIX.'_ipexc',__('I.P. Exclution','wp-stats-manager') ,array(self::$objAdminInterface, 'fnIPExclusion'),$wsmAdminPageHooks[WSM_PREFIX.'_ipexc'],'bottom','high');
				$wsmMetaBoxArray[WSM_PREFIX.'_ipexc']=__('I.P. Exclution','wp-stats-manager');
            break;
			default:
				$dashboard_widget = get_option(WSM_PREFIX.'Dashboard_widget');
				$report_stats_list = array(
											'general_stats_new'	=>	__('General Stats','wp-stats-manager'),
											'daily_stats'	=>	__('Daily Stats','wp-stats-manager'),
											'referral_website_stats'	=>	__('Referral Website Stats','wp-stats-manager'),
											'search_engine_stats'	=>	__('Search Engine Stats','wp-stats-manager'),
											'traffic_by_title_stats' => __('Title Stats','wp-stats-manager'),
											'top_search_engine_stats' => __('Top Search Engine Stats','wp-stats-manager'),
											'os_wise_visitor_stats'	=>	__('OS base Visitor Stats','wp-stats-manager'),
											'browser_wise_visitor_stats'	=>	__('Browser base Visitor Stats','wp-stats-manager'),
											'screen_wise_visitor_stats'	=>	__('Screen base Visitor Stats','wp-stats-manager'),
											'country_wise_visitor_stats'	=>	__('Today Countries Stats','wp-stats-manager'),
											'city_wise_visitor_stats'	=>	__('Today Cities Stats','wp-stats-manager'),
											'recent_visit_pages' => __('Traffic By Title','wp-stats-manager'),
											'recent_active_visitors' => __('Users Online','wp-stats-manager')
									);
				$report_stats_list = array(
											'general_stats_new'	=>	__('General Stats','wp-stats-manager'),
											'daily_stats'	=>	__('Daily Stats','wp-stats-manager'),
											'referral_website_stats'	=>	__('Referral Website Stats','wp-stats-manager'),
											'search_engine_stats'	=>	__('Search Engine Stats','wp-stats-manager'),
											'traffic_by_title_stats' => __('Title Stats','wp-stats-manager'),
											'top_search_engine_stats' => __('Top Search Engine Stats','wp-stats-manager'),
											'os_wise_visitor_stats'	=>	__('OS base Visitor Stats','wp-stats-manager'),
											'browser_wise_visitor_stats'	=>	__('Browser base Visitor Stats','wp-stats-manager'),
											'screen_wise_visitor_stats'	=>	__('Screen base Visitor Stats','wp-stats-manager'),
											'country_wise_visitor_stats'	=>	__('Today Countries Stats','wp-stats-manager'),
											'city_wise_visitor_stats'	=>	__('Today Cities Stats','wp-stats-manager'),
											'recent_visit_pages' => __('Traffic By Title','wp-stats-manager'),
											'recent_active_visitors' => __('Users Online','wp-stats-manager')
									);
				$objWsmScheduledMail = new wsmScheduledMail();					
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));	
				if( is_array( $dashboard_widget ) ){
					$objWsmScheduledMail = new wsmScheduledMail();
					foreach( $dashboard_widget as $screen => $widgets  ){
						foreach( $widgets as $widget ){
							//echo $widget;
							if( isset( $report_stats_list[ $widget ] ) )
							{
								add_meta_box($widget, $report_stats_list[ $widget ], array($objWsmScheduledMail, $widget ), 'dashboard', $screen, 'high');
							}	
						}
					}
				}
			break;
        }
    }
    static function wsm_addTrackerScript(){
       global $post;
	   
	   $ipAddress = wsmFnGetIPAddress();
	   $blockedIpAdresses = get_option('exclusion_ip_address_list');
	   
	   
	    if( isset($blockedIpAdresses) && is_array( $blockedIpAdresses ) && array_key_exists($ipAddress,$blockedIpAdresses) ){

		   return;
	   }
	   
	   

	      // User lowercase string for comparison.
		  $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

		  // A list of some common words used only for bots and crawlers.
		  $wsmRobots = get_option(WSM_PREFIX.'Robots');
		  $bot_identifiers = explode(',', $wsmRobots);
		  
		  // See if one of the identifiers is in the UA string.
		  foreach ($bot_identifiers as $identifier) {
			//  echo $user_agent."<br>".$identifier."<br>#<br>";
			 if(!empty($identifier) && !empty($user_agent))
			 {
				if (strpos($user_agent, $identifier) !== false)
				{
				  return;
				}
			 }
		  }
		  
	   $postID = 0;
	   if( is_single() || is_page() ){
	   		$postID = $post->ID;	
	   }
       $urlReferrer=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
       $output='';
        $output.='<!-- Wordpress Stats Manager -->
    <script type="text/javascript">
          var _wsm = _wsm || [];
           _wsm.push([\'trackPageView\']);
           _wsm.push([\'enableLinkTracking\']);
           _wsm.push([\'enableHeartBeatTimer\']);
          (function() {
            var u="'.WSM_URL.'";
            _wsm.push([\'setUrlReferrer\', "'.$urlReferrer.'"]);
            _wsm.push([\'setTrackerUrl\',"'.site_url('/?wmcAction=wmcTrack').'"]);
            _wsm.push([\'setSiteId\', "'. get_current_blog_id().'"]);
            _wsm.push([\'setPageId\', "'.$postID.'"]);
            _wsm.push([\'setWpUserId\', "'.get_current_user_id().'"]);           
            var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0];
            g.type=\'text/javascript\'; g.async=true; g.defer=true; g.src=u+\'js/wsm_new.js\'; s.parentNode.insertBefore(g,s);
          })();
    </script>
    <!-- End Wordpress Stats Manager Code -->';
        echo $output;
    }
    static function wsmCreateDatabaseView($viewName, $sqlQuery){
        global $wpdb;
       // echo '<br>'.$sql="DROP VIEW {$viewName};";
        //$wpdb->query($sql);
        $sql="CREATE OR REPLACE VIEW {$viewName} AS {$sqlQuery}";
        $wpdb->query($sql);
    }
    static function wsmCreateDatabaseTables($tableName, $arrSQL){
        global $wpdb;
        require_once(ABSPATH . "wp-admin/includes/upgrade.php");
        $checkSQL = "show tables like '".self::$tablePrefix."{$tableName}'";
        if($wpdb->get_var($checkSQL) != self::$tablePrefix.$tableName){
            if(isset($arrSQL['create']) && $arrSQL['create']!=''){
                $res=dbDelta($arrSQL['create']);
            }
        }
        if(isset($arrSQL['truncate']) && $arrSQL['truncate']==true){
                $wpdb->query('TRUNCATE TABLE '.self::$tablePrefix.$tableName);
        }
        if(isset($arrSQL['insert']) && $arrSQL['insert']!=''){
                $wpdb->query($arrSQL['insert']);
            }
        return false;
    }
    static function wsm_activate( $networkwide){
        global $wpdb;
        if (function_exists( 'is_multisite' ) && is_multisite() ) {
         //check if it is network activation if so run the activation function for each id
         if( $networkwide ) {
            $old_blog =  $wpdb->blogid;
            //Get all blog ids
            $blogids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
            foreach ( $blogids as $blog_id ) {
               switch_to_blog($blog_id);
               //Create database table if not exists
               self::wsmCreateDatabaseSchema();
            }
            switch_to_blog( $old_blog );
            return;
         }
      }
      //Create database table if not exists
      self::wsmCreateDatabaseSchema();
        if (!wp_next_scheduled ( WSM_PREFIX.'_dailyScheduler' )) {
            wp_schedule_event(time(), 'daily', WSM_PREFIX.'_dailyScheduler');
        }
    }
	
	
	
	static function wsm_setting_popup_func() {
  
    global $pagenow, $wsmRequestArray;
 
    $page = isset($wsmRequestArray['page']) && $wsmRequestArray['page']!='' ? sanitize_text_field($wsmRequestArray['page']):'';
	
	//wsm_traffic
		
    if ( $pagenow == 'admin.php' && (strpos($page, WSM_PREFIX) !== false)) {
        
      
           require_once WSM_DIR . '/includes/wsm_modal.php';
           add_option('wsm_popup_status',1);    
		  
		   
	  }
	}

	


    static function wsmCreateDatabaseSchemaForNewSite($blog_id, $user_id, $domain, $path, $site_id, $meta){
        if ( is_plugin_active_for_network( 'wp-stats-manager/wp-stats-manager.php' ) ) {
            switch_to_blog( $blog_id );
            self::wsmCreateDatabaseSchema();
            restore_current_blog();
        }
    }
    static function wsmCreateDatabaseSchema(){
        $arrTables=array();
        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_url_log (
          id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          pageId int(10) UNSIGNED NULL,
          title text,
          hash VARCHAR(20) NOT NULL,
          protocol VARCHAR(10) NOT NULL,
          url text,
          searchEngine int(2) UNSIGNED NULL,
          toolBar int(2) UNSIGNED NULL,
          PRIMARY KEY (id),
          KEY index_type_hash (pageId,hash,searchEngine),
          KEY index_tb_hash (pageId,hash,searchEngine)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_url_log',array('create'=>$sql));
        $arrTables['LOG_URL']='_url_log';

        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_logUniqueVisit(
          id bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          siteId int(10) UNSIGNED NOT NULL,
          visitorId varchar(20) NOT NULL,
          visitLastActionTime datetime NOT NULL,
          configId varchar(20) NOT NULL,
          ipAddress varchar(16) NOT NULL,
          userId varchar(200) DEFAULT NULL,
          firstActionVisitTime datetime NOT NULL,
          daysSinceFirstVisit smallint(5) UNSIGNED DEFAULT NULL,
          returningVisitor tinyint(1) DEFAULT NULL,
          visitCount int(11) UNSIGNED NOT NULL,
          visitEntryURLId int(11) UNSIGNED DEFAULT NULL,
          visitExitURLId int(11) UNSIGNED DEFAULT \'0\',
          visitTotalActions int(11) UNSIGNED DEFAULT NULL,
          refererUrlId int(11),          
          browserLang varchar(20) DEFAULT NULL,
          browserId int(11) UNSIGNED DEFAULT NULL,
          deviceType varchar(20) DEFAULT NULL,
          oSystemId int(11) UNSIGNED DEFAULT NULL,
          currentLocalTime time DEFAULT NULL,
          daysSinceLastVisit smallint(5) UNSIGNED DEFAULT NULL,
          totalTimeVisit int(11) UNSIGNED NOT NULL,
          resolutionId int(11) UNSIGNED DEFAULT NULL,
          cookie tinyint(1) DEFAULT NULL,
          director tinyint(1) DEFAULT NULL,
          flash tinyint(1) DEFAULT NULL,
          gears tinyint(1) DEFAULT NULL,
          java tinyint(1) DEFAULT NULL,
          pdf tinyint(1) DEFAULT NULL,
          quicktime tinyint(1) DEFAULT NULL,
          realplayer tinyint(1) DEFAULT NULL,
          silverlight tinyint(1) DEFAULT NULL,
          windowsmedia tinyint(1) DEFAULT NULL,
          city varchar(255) DEFAULT NULL,
          countryId int(3) UNSIGNED NOT NULL,
          latitude decimal(9,6) DEFAULT NULL,
          longitude decimal(9,6) DEFAULT NULL,
          regionId tinyint(2) DEFAULT NULL,
          PRIMARY KEY (id),
          KEY index_config_datetime (configId,visitLastActionTime),
          KEY index_datetime (visitLastActionTime),
          KEY index_idvisitor (visitorId)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_logUniqueVisit',array('create'=>$sql));
        $arrTables['LOG_UNIQUE']='_logUniqueVisit';

        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_logVisit(
          id bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
          siteId int(10) UNSIGNED NOT NULL,
          visitorId varchar(20) NOT NULL,
          visitId bigint(10) UNSIGNED NOT NULL,
          refererUrlId int(10) UNSIGNED DEFAULT 0,
          keyword varchar(200) DEFAULT NULL,
          serverTime datetime NOT NULL,
          timeSpentRef int(11) UNSIGNED NOT NULL,
          URLId int(10) UNSIGNED DEFAULT NULL,
          PRIMARY KEY (id),
          KEY index_visitId (visitId),
          KEY index_siteId_serverTime (siteId,serverTime)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_logVisit',array('create'=>$sql));
        $arrTables['LOG_VISIT']='_logVisit';

        $sql='CREATE TABLE '.self::$tablePrefix.'_oSystems (
          id tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
          name varchar(255) DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;';
        $insertSQL="INSERT INTO ".self::$tablePrefix."_oSystems (id,name) VALUES (1,'Windows 98'),(2,'Windows CE'),(3,'Linux'),(4,'Unix'),(5,'Windows 2000'),(6,'Windows XP'),(7,'Windows 8'),(8,'Windows 10'),(9,'Mac OS'),(10,'Android'),(11,'IOS')";
        self::wsmCreateDatabaseTables('_oSystems',array('create'=>$sql,'insert'=>$insertSQL,'truncate'=>true));
        $arrTables['OS']='_oSystems';

        $sql='CREATE TABLE '.self::$tablePrefix.'_browsers (
          id tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
          name varchar(255) DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $insertSQL="INSERT INTO ".self::$tablePrefix."_browsers (id,name) VALUES (1,'Mozilla Firefox'),(2,'Google Chrome'),(3,'Opera'),(4,'Safari'),(5,'Internet Explorer'),(6,'Micorsoft Edge'),(7,'Torch'),(8,'Maxthon'),(9,'SeaMonkey'),(10,'Avant Browser'),(11,'Deepnet Explorer'),(12,'UE Browser')";
        self::wsmCreateDatabaseTables('_browsers',array('create'=>$sql,'insert'=>$insertSQL,'truncate'=>true));
        $arrTables['BROW']='_browsers';

        $sql='CREATE TABLE '.self::$tablePrefix.'_toolBars (
          id tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
          name varchar(255) DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $insertSQL="INSERT INTO ".self::$tablePrefix."_toolBars (id,name) VALUES (1,'Alexa'),(2,'AOL'),(3,'Bing'),(4,'Data'),(5,'Google'),(6,'Kiwee'),(7,'Mirar'),(8,'Windows Live'),(9,'Yahoo')";
        self::wsmCreateDatabaseTables('_toolBars',array('create'=>$sql,'insert'=>$insertSQL,'truncate'=>true));
        $arrTables['TOOL']='_toolBars';

        $sql='CREATE TABLE '.self::$tablePrefix.'_searchEngines (
          id tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
          name varchar(255) DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $insertSQL="INSERT INTO ".self::$tablePrefix."_searchEngines (id,name) VALUES (1,'Google'),(2,'Bing'),(3,'Yahoo'),(4,'Baidu'),(5,'AOL'),(6,'Ask'),(7,'Excite'),(8,'Duck Duck Go'),(9,'WolframAlpha'),(10,'Yandex'),(11,'Lycos'),(12,'Chacha')";
         self::wsmCreateDatabaseTables('_searchEngines',array('create'=>$sql,'insert'=>$insertSQL,'truncate'=>true));
         $arrTables['SE']='_searchEngines';

        $sql='CREATE TABLE '.self::$tablePrefix.'_regions (
          id tinyint(1) UNSIGNED NOT NULL AUTO_INCREMENT,
          code char(2) NOT NULL COMMENT \'Region code\',
          name varchar(255) DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
         $insertSQL="INSERT INTO ".self::$tablePrefix."_regions (id,code,name) VALUES (1,'AF', 'Africa'),(2,'AN', 'Antarctica'),(3,'AS', 'Asia'),(4,'EU', 'Europe'),(5,'NA', 'North America'),(6,'OC', 'Oceania'),(7,'SA', 'South America')";
        self::wsmCreateDatabaseTables('_regions',array('create'=>$sql,'insert'=>$insertSQL,'truncate'=>true));
        $arrTables['RG']='_regions';

        $sql='CREATE TABLE '.self::$tablePrefix.'_resolutions (
          id tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
          name varchar(255) DEFAULT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;';
        $insertSQL="INSERT INTO ".self::$tablePrefix."_resolutions (id, name) VALUES (1,'640x480'),(2,'800x600'),(3,'960x720'),(4,'1024x768'),(5,'1280x960'),(6,'1400x1050'),(7,'1440x1080'),(8,'1600x1200'),(9,'1856x1392'),(10,'1920x1440'),(11,'2048x1536'),(12,'1280x800'),(13,'1440x900'),(14,'1680x1050'),(15,'1920x1200'),(16,'2560x1600'),(17,'1024x576'),(18,'1152x648'),(19,'1280x720'),(20,'1366x768'),(21,'1600x900'),(22,'1920x1080'),(23,'2560x1440'),(24,'3840x2160')";
        self::wsmCreateDatabaseTables('_resolutions',array('create'=>$sql,'insert'=>$insertSQL,'truncate'=>true));
        $arrTables['RSOL']='_resolutions';

        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_countries(
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          name varchar(255) COLLATE utf8_bin NOT NULL,
          alpha2Code varchar(2) COLLATE utf8_bin NOT NULL,
          alpha3Code varchar(3) COLLATE utf8_bin NOT NULL,
          numericCode smallint(6) NOT NULL,
          PRIMARY KEY (id),
          UNIQUE KEY id (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
        $insertSQL="INSERT INTO ".self::$tablePrefix."_countries (id, name, alpha2Code,alpha3Code,numericCode) VALUES
            (1, 'Afghanistan', 'AF', 'AFG', 4),
            (2, '&Aring;land Islands', 'AX', 'ALA', 248),
            (3, 'Albania', 'AL', 'ALB', 8),
            (4, 'Algeria', 'DZ', 'DZA', 12),
            (5, 'American Samoa', 'AS', 'ASM', 16),
            (6, 'Andorra', 'AD', 'AND', 20),
            (7, 'Angola', 'AO', 'AGO', 24),
            (8, 'Anguilla', 'AI', 'AIA', 660),
            (9, 'Antarctica', 'AQ', 'ATA', 10),
            (10, 'Antigua and Barbuda', 'AG', 'ATG', 28),
            (11, 'Argentina', 'AR', 'ARG', 32),
            (12, 'Armenia', 'AM', 'ARM', 51),
            (13, 'Aruba', 'AW', 'ABW', 533),
            (14, 'Australia', 'AU', 'AUS', 36),
            (15, 'Austria', 'AT', 'AUT', 40),
            (16, 'Azerbaijan', 'AZ', 'AZE', 31),
            (17, 'Bahamas', 'BS', 'BHS', 44),
            (18, 'Bahrain', 'BH', 'BHR', 48),
            (19, 'Bangladesh', 'BD', 'BGD', 50),
            (20, 'Barbados', 'BB', 'BRB', 52),
            (21, 'Belarus', 'BY', 'BLR', 112),
            (22, 'Belgium', 'BE', 'BEL', 56),
            (23, 'Belize', 'BZ', 'BLZ', 84),
            (24, 'Benin', 'BJ', 'BEN', 204),
            (25, 'Bermuda', 'BM', 'BMU', 60),
            (26, 'Bhutan', 'BT', 'BTN', 64),
            (27, 'Bolivia, Plurinational State of', 'BO', 'BOL', 68),
            (28, 'Bonaire, Sint Eustatius and Saba', 'BQ', 'BES', 535),
            (29, 'Bosnia and Herzegovina', 'BA', 'BIH', 70),
            (30, 'Botswana', 'BW', 'BWA', 72),
            (31, 'Bouvet Island', 'BV', 'BVT', 74),
            (32, 'Brazil', 'BR', 'BRA', 76),
            (33, 'British Indian Ocean Territory', 'IO', 'IOT', 86),
            (34, 'Brunei Darussalam', 'BN', 'BRN', 96),
            (35, 'Bulgaria', 'BG', 'BGR', 100),
            (36, 'Burkina Faso', 'BF', 'BFA', 854),
            (37, 'Burundi', 'BI', 'BDI', 108),
            (38, 'Cambodia', 'KH', 'KHM', 116),
            (39, 'Cameroon', 'CM', 'CMR', 120),
            (40, 'Canada', 'CA', 'CAN', 124),
            (41, 'Cape Verde', 'CV', 'CPV', 132),
            (42, 'Cayman Islands', 'KY', 'CYM', 136),
            (43, 'Central African Republic', 'CF', 'CAF', 140),
            (44, 'Chad', 'TD', 'TCD', 148),
            (45, 'Chile', 'CL', 'CHL', 152),
            (46, 'China', 'CN', 'CHN', 156),
            (47, 'Christmas Island', 'CX', 'CXR', 162),
            (48, 'Cocos (Keeling) Islands', 'CC', 'CCK', 166),
            (49, 'Colombia', 'CO', 'COL', 170),
            (50, 'Comoros', 'KM', 'COM', 174),
            (51, 'Congo', 'CG', 'COG', 178),
            (52, 'Congo, the Democratic Republic of the', 'CD', 'COD', 180),
            (53, 'Cook Islands', 'CK', 'COK', 184),
            (54, 'Costa Rica', 'CR', 'CRI', 188),
            (55, 'C&ocirc;te d\'\'Ivoire', 'CI', 'CIV', 384),
            (56, 'Croatia', 'HR', 'HRV', 191),
            (57, 'Cuba', 'CU', 'CUB', 192),
            (58, 'Cura', 'CW', 'CUW', 531),
            (59, 'Cyprus', 'CY', 'CYP', 196),
            (60, 'Czech Republic', 'CZ', 'CZE', 203),
            (61, 'Denmark', 'DK', 'DNK', 208),
            (62, 'Djibouti', 'DJ', 'DJI', 262),
            (63, 'Dominica', 'DM', 'DMA', 212),
            (64, 'Dominican Republic', 'DO', 'DOM', 214),
            (65, 'Ecuador', 'EC', 'ECU', 218),
            (66, 'Egypt', 'EG', 'EGY', 818),
            (67, 'El Salvador', 'SV', 'SLV', 222),
            (68, 'Equatorial Guinea', 'GQ', 'GNQ', 226),
            (69, 'Eritrea', 'ER', 'ERI', 232),
            (70, 'Estonia', 'EE', 'EST', 233),
            (71, 'Ethiopia', 'ET', 'ETH', 231),
            (72, 'Falkland Islands (Malvinas)', 'FK', 'FLK', 238),
            (73, 'Faroe Islands', 'FO', 'FRO', 234),
            (74, 'Fiji', 'FJ', 'FJI', 242),
            (75, 'Finland', 'FI', 'FIN', 246),
            (76, 'France', 'FR', 'FRA', 250),
            (77, 'French Guiana', 'GF', 'GUF', 254),
            (78, 'French Polynesia', 'PF', 'PYF', 258),
            (79, 'French Southern Territories', 'TF', 'ATF', 260),
            (80, 'Gabon', 'GA', 'GAB', 266),
            (81, 'Gambia', 'GM', 'GMB', 270),
            (82, 'Georgia', 'GE', 'GEO', 268),
            (83, 'Germany', 'DE', 'DEU', 276),
            (84, 'Ghana', 'GH', 'GHA', 288),
            (85, 'Gibraltar', 'GI', 'GIB', 292),
            (86, 'Greece', 'GR', 'GRC', 300),
            (87, 'Greenland', 'GL', 'GRL', 304),
            (88, 'Grenada', 'GD', 'GRD', 308),
            (89, 'Guadeloupe', 'GP', 'GLP', 312),
            (90, 'Guam', 'GU', 'GUM', 316),
            (91, 'Guatemala', 'GT', 'GTM', 320),
            (92, 'Guernsey', 'GG', 'GGY', 831),
            (93, 'Guinea', 'GN', 'GIN', 324),
            (94, 'Guinea-Bissau', 'GW', 'GNB', 624),
            (95, 'Guyana', 'GY', 'GUY', 328),
            (96, 'Haiti', 'HT', 'HTI', 332),
            (97, 'Heard Island and McDonald Islands', 'HM', 'HMD', 334),
            (98, 'Holy See (Vatican City State)', 'VA', 'VAT', 336),
            (99, 'Honduras', 'HN', 'HND', 340),
            (100, 'Hong Kong', 'HK', 'HKG', 344),
            (101, 'Hungary', 'HU', 'HUN', 348),
            (102, 'Iceland', 'IS', 'ISL', 352),
            (103, 'India', 'IN', 'IND', 356),
            (104, 'Indonesia', 'ID', 'IDN', 360),
            (105, 'Iran, Islamic Republic of', 'IR', 'IRN', 364),
            (106, 'Iraq', 'IQ', 'IRQ', 368),
            (107, 'Ireland', 'IE', 'IRL', 372),
            (108, 'Isle of Man', 'IM', 'IMN', 833),
            (109, 'Israel', 'IL', 'ISR', 376),
            (110, 'Italy', 'IT', 'ITA', 380),
            (111, 'Jamaica', 'JM', 'JAM', 388),
            (112, 'Japan', 'JP', 'JPN', 392),
            (113, 'Jersey', 'JE', 'JEY', 832),
            (114, 'Jordan', 'JO', 'JOR', 400),
            (115, 'Kazakhstan', 'KZ', 'KAZ', 398),
            (116, 'Kenya', 'KE', 'KEN', 404),
            (117, 'Kiribati', 'KI', 'KIR', 296),
            (118, 'Korea, Democratic People''s Republic of', 'KP', 'PRK', 408),
            (119, 'Korea, Republic of', 'KR', 'KOR', 410),
            (120, 'Kuwait', 'KW', 'KWT', 414),
            (121, 'Kyrgyzstan', 'KG', 'KGZ', 417),
            (122, 'Lao People''s Democratic Republic', 'LA', 'LAO', 418),
            (123, 'Latvia', 'LV', 'LVA', 428),
            (124, 'Lebanon', 'LB', 'LBN', 422),
            (125, 'Lesotho', 'LS', 'LSO', 426),
            (126, 'Liberia', 'LR', 'LBR', 430),
            (127, 'Libya', 'LY', 'LBY', 434),
            (128, 'Liechtenstein', 'LI', 'LIE', 438),
            (129, 'Lithuania', 'LT', 'LTU', 440),
            (130, 'Luxembourg', 'LU', 'LUX', 442),
            (131, 'Macao', 'MO', 'MAC', 446),
            (132, 'Macedonia, the former Yugoslav Republic of', 'MK', 'MKD', 807),
            (133, 'Madagascar', 'MG', 'MDG', 450),
            (134, 'Malawi', 'MW', 'MWI', 454),
            (135, 'Malaysia', 'MY', 'MYS', 458),
            (136, 'Maldives', 'MV', 'MDV', 462),
            (137, 'Mali', 'ML', 'MLI', 466),
            (138, 'Malta', 'MT', 'MLT', 470),
            (139, 'Marshall Islands', 'MH', 'MHL', 584),
            (140, 'Martinique', 'MQ', 'MTQ', 474),
            (141, 'Mauritania', 'MR', 'MRT', 478),
            (142, 'Mauritius', 'MU', 'MUS', 480),
            (143, 'Mayotte', 'YT', 'MYT', 175),
            (144, 'Mexico', 'MX', 'MEX', 484),
            (145, 'Micronesia\, Federated States of', 'FM', 'FSM', 583),
            (146, 'Moldova, Republic of', 'MD', 'MDA', 498),
            (147, 'Monaco', 'MC', 'MCO', 492),
            (148, 'Mongolia', 'MN', 'MNG', 496),
            (149, 'Montenegro', 'ME', 'MNE', 499),
            (150, 'Montserrat', 'MS', 'MSR', 500),
            (151, 'Morocco', 'MA', 'MAR', 504),
            (152, 'Mozambique', 'MZ', 'MOZ', 508),
            (153, 'Myanmar', 'MM', 'MMR', 104),
            (154, 'Namibia', 'NA', 'NAM', 516),
            (155, 'Nauru', 'NR', 'NRU', 520),
            (156, 'Nepal', 'NP', 'NPL', 524),
            (157, 'Netherlands', 'NL', 'NLD', 528),
            (158, 'New Caledonia', 'NC', 'NCL', 540),
            (159, 'New Zealand', 'NZ', 'NZL', 554),
            (160, 'Nicaragua', 'NI', 'NIC', 558),
            (161, 'Niger', 'NE', 'NER', 562),
            (162, 'Nigeria', 'NG', 'NGA', 566),
            (163, 'Niue', 'NU', 'NIU', 570),
            (164, 'Norfolk Island', 'NF', 'NFK', 574),
            (165, 'Northern Mariana Islands', 'MP', 'MNP', 580),
            (166, 'Norway', 'NO', 'NOR', 578),
            (167, 'Oman', 'OM', 'OMN', 512),
            (168, 'Pakistan', 'PK', 'PAK', 586),
            (169, 'Palau', 'PW', 'PLW', 585),
            (170, 'Palestine, State of', 'PS', 'PSE', 275),
            (171, 'Panama', 'PA', 'PAN', 591),
            (172, 'Papua New Guinea', 'PG', 'PNG', 598),
            (173, 'Paraguay', 'PY', 'PRY', 600),
            (174, 'Peru', 'PE', 'PER', 604),
            (175, 'Philippines', 'PH', 'PHL', 608),
            (176, 'Pitcairn', 'PN', 'PCN', 612),
            (177, 'Poland', 'PL', 'POL', 616),
            (178, 'Portugal', 'PT', 'PRT', 620),
            (179, 'Puerto Rico', 'PR', 'PRI', 630),
            (180, 'Qatar', 'QA', 'QAT', 634),
            (181, 'R&eacute;union', 'RE', 'REU', 638),
            (182, 'Romania', 'RO', 'ROU', 642),
            (183, 'Russian Federation', 'RU', 'RUS', 643),
            (184, 'Rwanda', 'RW', 'RWA', 646),
            (185, 'Saint Barth&eacute;lemy', 'BL', 'BLM', 652),
            (186, 'Saint Helena, Ascension and Tristan da Cunha', 'SH', 'SHN', 654),
            (187, 'Saint Kitts and Nevis', 'KN', 'KNA', 659),
            (188, 'Saint Lucia', 'LC', 'LCA', 662),
            (189, 'Saint Martin (French part)', 'MF', 'MAF', 663),
            (190, 'Saint Pierre and Miquelon', 'PM', 'SPM', 666),
            (191, 'Saint Vincent and the Grenadines', 'VC', 'VCT', 670),
            (192, 'Samoa', 'WS', 'WSM', 882),
            (193, 'San Marino', 'SM', 'SMR', 674),
            (194, 'Sao Tome and Principe', 'ST', 'STP', 678),
            (195, 'Saudi Arabia', 'SA', 'SAU', 682),
            (196, 'Senegal', 'SN', 'SEN', 686),
            (197, 'Serbia', 'RS', 'SRB', 688),
            (198, 'Seychelles', 'SC', 'SYC', 690),
            (199, 'Sierra Leone', 'SL', 'SLE', 694),
            (200, 'Singapore', 'SG', 'SGP', 702),
            (201, 'Sint Maarten (Dutch part)', 'SX', 'SXM', 534),
            (202, 'Slovakia', 'SK', 'SVK', 703),
            (203, 'Slovenia', 'SI', 'SVN', 705),
            (204, 'Solomon Islands', 'SB', 'SLB', 90),
            (205, 'Somalia', 'SO', 'SOM', 706),
            (206, 'South Africa', 'ZA', 'ZAF', 710),
            (207, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS', 239),
            (208, 'South Sudan', 'SS', 'SSD', 728),
            (209, 'Spain', 'ES', 'ESP', 724),
            (210, 'Sri Lanka', 'LK', 'LKA', 144),
            (211, 'Sudan', 'SD', 'SDN', 729),
            (212, 'Suriname', 'SR', 'SUR', 740),
            (213, 'Svalbard and Jan Mayen', 'SJ', 'SJM', 744),
            (214, 'Swaziland', 'SZ', 'SWZ', 748),
            (215, 'Sweden', 'SE', 'SWE', 752),
            (216, 'Switzerland', 'CH', 'CHE', 756),
            (217, 'Syrian Arab Republic', 'SY', 'SYR', 760),
            (218, 'Taiwan, Province of China', 'TW', 'TWN', 158),
            (219, 'Tajikistan', 'TJ', 'TJK', 762),
            (220, 'Tanzania, United Republic of', 'TZ', 'TZA', 834),
            (221, 'Thailand', 'TH', 'THA', 764),
            (222, 'Timor-Leste', 'TL', 'TLS', 626),
            (223, 'Togo', 'TG', 'TGO', 768),
            (224, 'Tokelau', 'TK', 'TKL', 772),
            (225, 'Tonga', 'TO', 'TON', 776),
            (226, 'Trinidad and Tobago', 'TT', 'TTO', 780),
            (227, 'Tunisia', 'TN', 'TUN', 788),
            (228, 'Turkey', 'TR', 'TUR', 792),
            (229, 'Turkmenistan', 'TM', 'TKM', 795),
            (230, 'Turks and Caicos Islands', 'TC', 'TCA', 796),
            (231, 'Tuvalu', 'TV', 'TUV', 798),
            (232, 'Uganda', 'UG', 'UGA', 800),
            (233, 'Ukraine', 'UA', 'UKR', 804),
            (234, 'United Arab Emirates', 'AE', 'ARE', 784),
            (235, 'United Kingdom', 'GB', 'GBR', 826),
            (236, 'United States', 'US', 'USA', 840),
            (237, 'United States Minor Outlying Islands', 'UM', 'UMI', 581),
            (238, 'Uruguay', 'UY', 'URY', 858),
            (239, 'Uzbekistan', 'UZ', 'UZB', 860),
            (240, 'Vanuatu', 'VU', 'VUT', 548),
            (241, 'Venezuela\, Bolivarian Republic of', 'VE', 'VEN', 862),
            (242, 'Viet Nam', 'VN', 'VNM', 704),
            (243, 'Virgin Islands, British', 'VG', 'VGB', 92),
            (244, 'Virgin Islands, U.S.', 'VI', 'VIR', 850),
            (245, 'Wallis and Futuna', 'WF', 'WLF', 876),
            (246, 'Western Sahara', 'EH', 'ESH', 732),
            (247, 'Yemen', 'YE', 'YEM', 887),
            (248, 'Zambia', 'ZM', 'ZMB', 894),
            (249, 'Zimbabwe', 'ZW', 'ZWE', 716)";
        self::wsmCreateDatabaseTables('_countries',array('create'=>$sql,'insert'=>$insertSQL,'truncate'=>true));
        $arrTables['COUNTRY']='_countries';
        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_dailyHourlyReport(
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          name varchar(50) NOT NULL,
          reportDate datetime NOT NULL,
          content TEXT NOT NULL,
          timezone varchar(20) NOT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_dailyHourlyReport',array('create'=>$sql));
        $arrTables['DHR']='_dailyHourlyReport';
        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_monthlyDailyReport(
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          name varchar(50) NOT NULL,
          reportMonthYear varchar(50) NOT NULL,
          content TEXT NOT NULL,
          timezone varchar(20) NOT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_monthlyDailyReport',array('create'=>$sql));
        $arrTables['MDR']='_monthlyDailyReport';
        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_yearlyMonthlyReport(
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          name varchar(50) NOT NULL,
          reportYear varchar(10) NOT NULL,
          content TEXT NOT NULL,
          timezone varchar(20) NOT NULL,
          PRIMARY KEY (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_yearlyMonthlyReport',array('create'=>$sql));
        $arrTables['YMR']='_yearlyMonthlyReport';
        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_datewise_report(
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          date date NOT NULL,
          normal int(2) NOT NULL DEFAULT "0",
          hour int(2) NOT NULL DEFAULT "0",
          search_engine varchar(255) NOT NULL DEFAULT "",
          browser int(2) NOT NULL DEFAULT "0",
          screen int(2) NOT NULL DEFAULT "0",
          country int(3) NOT NULL DEFAULT "0",
          city varchar(255) NOT NULL DEFAULT "",
          operating_system int(2) NOT NULL DEFAULT "0",
          url_id int(11) NOT NULL DEFAULT "0",
          total_page_views int(11) NOT NULL DEFAULT "0",
          total_visitors int(11) NOT NULL DEFAULT "0",
          total_first_time_visitors int(11) NOT NULL DEFAULT "0",
          total_bounce int(11) NOT NULL DEFAULT "0",
          PRIMARY KEY (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_datewise_report',array('create'=>$sql));
        $arrTables['DWR']='_datewise_report';
        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_monthwise_report(
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          date date NOT NULL,
          normal int(2) NOT NULL DEFAULT "0",
          hour int(2) NOT NULL DEFAULT "0",
          search_engine varchar(255) NOT NULL DEFAULT "",
          browser int(2) NOT NULL DEFAULT "0",
          screen int(2) NOT NULL DEFAULT "0",
          country int(3) NOT NULL DEFAULT "0",
          city varchar(255) NOT NULL DEFAULT "",
          operating_system int(2) NOT NULL DEFAULT "0",
          url_id int(11) NOT NULL DEFAULT "0",
          total_page_views int(11) NOT NULL DEFAULT "0",
          total_visitors int(11) NOT NULL DEFAULT "0",
          total_first_time_visitors int(11) NOT NULL DEFAULT "0",
          total_bounce int(11) NOT NULL DEFAULT "0",
          PRIMARY KEY (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_monthwise_report',array('create'=>$sql));
        $arrTables['MWR']='_monthwise_report';
        $sql='CREATE TABLE IF NOT EXISTS '.self::$tablePrefix.'_yearwise_report(
          id int(10) unsigned NOT NULL AUTO_INCREMENT,
          date date NOT NULL,
          normal int(2) NOT NULL DEFAULT "0",
          hour int(2) NOT NULL DEFAULT "0",
          search_engine varchar(255) NOT NULL DEFAULT "",
          browser int(2) NOT NULL DEFAULT "0",
          screen int(2) NOT NULL DEFAULT "0",
          country int(3) NOT NULL DEFAULT "0",
          city varchar(255) NOT NULL DEFAULT "",
          operating_system int(2) NOT NULL DEFAULT "0",
          url_id int(11) NOT NULL DEFAULT "0",
          total_page_views int(11) NOT NULL DEFAULT "0",
          total_visitors int(11) NOT NULL DEFAULT "0",
          total_first_time_visitors int(11) NOT NULL DEFAULT "0",
          total_bounce int(11) NOT NULL DEFAULT "0",
          PRIMARY KEY (id)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8';
        self::wsmCreateDatabaseTables('_yearwise_report',array('create'=>$sql));
        $arrTables['YWR']='_yearwise_report';
        
        update_option(WSM_PREFIX.'_tables',$arrTables);
        
        $sql='SELECT LV.visitId, LV.URLId, LV.keyword, LV.refererUrlId, LU.countryId, LU.regionId, COUNT(*) As totalViews, max(LV.serverTime) AS visitLastActionTime FROM '.self::$tablePrefix.'_logVisit LV LEFT JOIN '.self::$tablePrefix.'_logUniqueVisit LU ON LV.visitId=LU.id GROUP BY LV.visitId, LV.URLId';
        self::wsmCreateDatabaseView(self::$tablePrefix.'_pageViews',$sql);
                
        $sql='SELECT LU.id, LU.visitorId,sum(LU.totalTimeVisit) as totalTimeVisit,MIN(LV.serverTime) as firstVisitTime, LU.refererUrlId FROM '.self::$tablePrefix.'_logUniqueVisit LU LEFT JOIN '.self::$tablePrefix.'_logVisit LV ON LV.visitId=LU.id GROUP BY LU.visitorId';
        self::wsmCreateDatabaseView(self::$tablePrefix.'_uniqueVisitors',$sql);
        $sql='SELECT visitId, visitLastActionTime FROM '.self::$tablePrefix.'_pageViews GROUP BY visitId HAVING COUNT(URLId)=1';
        self::wsmCreateDatabaseView(self::$tablePrefix.'_bounceVisits',$sql);        
      
        //left JOIN '.self::$tablePrefix.'_logVisit LV2 ON LV.visitId=LV2.visitId AND LV2.serverTime>LV.serverTime
        $sql='SELECT LV.visitId,LU.userId, LV.serverTime,LU.visitLastActionTime, LV.urlId, COUNT(LV.urlId) as hits, UL.title, CONCAT(UL.protocol, UL.url) as url, CONCAT(UL2.protocol, UL2.url) as refUrl, LU.visitorId, LU.ipAddress,LU.city, C.alpha2Code,C.name as country, LU.deviceType, B.name as browser,OS.name as osystem, LU.latitude, LU.longitude,R.name as resolution, SE.name as searchEngine, TB.name as toolBar FROM '.self::$tablePrefix.'_logVisit LV LEFT JOIN '.self::$tablePrefix.'_logUniqueVisit LU ON LU.id=LV.visitId LEFT JOIN '.self::$tablePrefix.'_countries C ON C.id=LU.countryId LEFT JOIN '.self::$tablePrefix.'_browsers B ON B.id=LU.browserId LEFT JOIN '.self::$tablePrefix.'_resolutions R ON R.id=LU.resolutionId LEFT JOIN '.self::$tablePrefix.'_url_log UL ON LV.urlId=UL.id LEFT JOIN '.self::$tablePrefix.'_url_log UL2 ON LV.refererUrlId=UL2.id  LEFT JOIN '.self::$tablePrefix.'_searchEngines SE ON SE.id=UL.searchEngine LEFT JOIN '.self::$tablePrefix.'_toolBars TB ON TB.id=UL.toolBar LEFT JOIN '.self::$tablePrefix.'_oSystems OS ON OS.id=LU.oSystemId GROUP BY LV.visitId,LV.urlId ORDER BY LV.visitId DESC ,LV.serverTime DESC';
        self::wsmCreateDatabaseView(self::$tablePrefix.'_visitorInfo',$sql);
        self::wsm_fnCreateImportantViews();
        self::wsm_createMonthWiseViews();
    }
    static function wsm_deactivate(){
		$keepData = get_option(WSM_PREFIX.'KeepData');
		if($keepData=="0")
		{
			global $wpdb;
			
					
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_bounceVisits');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateHourWiseBounce');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateHourWiseBounceRate');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateHourWiseFirstVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateHourWisePageViews');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateHourWiseVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateWiseBounce');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateWiseBounceRate');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateWiseFirstVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateWisePageViews');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_dateWiseVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_hourWiseBounce');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_hourWiseBounceRate');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_hourWiseFirstVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_hourWisePageViews');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_hourWiseVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_monthWiseBounce');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_monthWiseBounceRate');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_monthWiseFirstVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_monthWisePageViews');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_monthWiseVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_pageViews');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_uniqueVisitors');
			$wpdb->query( 'DROP VIEW IF EXISTS '.self::$tablePrefix.'_visitorInfo');
			
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_url_log');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_logUniqueVisit');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_logVisit');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_oSystems');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_browsers');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_toolBars');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_searchEngines');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_regions');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_resolutions');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_countries');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_dailyHourlyReport');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_monthlyDailyReport');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_yearlyMonthlyReport');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_datewise_report');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_monthwise_report');
			$wpdb->query( 'DROP TABLE IF EXISTS '.self::$tablePrefix.'_yearwise_report');
			
			delete_option(WSM_PREFIX.'_dailyReportedTime');
			delete_option(WSM_PREFIX.'_lastHitTime');
			delete_option(WSM_PREFIX.'_tables');
			delete_option(WSM_PREFIX.'AdminColors');
			delete_option(WSM_PREFIX.'ArchiveDays');
			delete_option(WSM_PREFIX.'ChartDays');
			delete_option(WSM_PREFIX.'Country');
			delete_option(WSM_PREFIX.'dashboard_widget');
			delete_option(WSM_PREFIX.'GoogleMapAPI');
			delete_option(WSM_PREFIX.'KeepData');
			delete_option(WSM_PREFIX.'Plugin_widget');
			delete_option(WSM_PREFIX.'ReportEmails');
			delete_option(WSM_PREFIX.'ReportScheduleTime');
			delete_option(WSM_PREFIX.'ReportStats');
			delete_option(WSM_PREFIX.'SiteDashboardNormalWidgets');
			delete_option(WSM_PREFIX.'SiteDashboardSideWidgets');
			delete_option(WSM_PREFIX.'SitePluginNormalWidgets');
			delete_option(WSM_PREFIX.'SitePluginSideWidgets');
			delete_option(WSM_PREFIX.'TimezoneString');
			
		}
    }
	
}
