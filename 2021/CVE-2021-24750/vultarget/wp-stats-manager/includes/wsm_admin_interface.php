<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class wsmAdminInterface{
    private $startWrapper;
    private $endWrapper;
    private $startMetaBox;
    private $endMetaBox;
    private $wsmClear;
    private $objDatabase;
    function __construct(){
        $this->startWrapper='<div class="wrap">';
        $this->endWrapper='</div>';
        $this->startMetaBoxWrapper='<div id="dashboard-widgets-wrap" class="wsmMetaboxContainer"><div id="dashboard-widgets" class="metabox-holder">';
        $this->endMetaBoxWrapper='</div></div>';
        $this->wsmClear='<div class="clear"></div>';
        $this->objDatabase=new wsmDatabase();
		
    }
    
	
	function fnPrintTitle($title){
        return '<h1 class="wsmHead">'.WSM_NAME.'</h1>'.$this->fnPrintHeader($title).'
		<p>
		
			<div id="wsm_subscribe" class="notice notice-info is-dismissible" > 
				<p>
				<input type="text" value="'.get_bloginfo("admin_email").'" name="WSMsub_email" id="WSMsub_email" />
				<button type="button" class="btn btn-primary wsm_subscribe_btn" onclick="wsm_open_subscribe_page()" >'.__("Subscribe",'wp-stats-manager').'</button> '.__("to get latest news and updates, plugin recommendations & help, promotional email with",'wp-stats-manager').' <b style="color:red">'.__("discount codes :)" ,'wp-stats-manager').'</b> '.__("or" ,'wp-stats-manager').' <a href="#" onclick="wsm_dismiss_notice()">'.__('Dismiss this notice','wp-stats-manager').'</a>
				</p>
			</div></p>
			<script>
function wsm_dismiss_notice()
				{
					localStorage.setItem(\'wsm_subscribed\', \'subsbcusers\');
					document.getElementById("wsm_subscribe").style.display="none";
				}

				function wsm_open_subscribe_page()
				{
					if(localStorage.getItem(\'wsm_subscribed\') !=\'subsbcusers\')
					{
						var WSMsub_email = document.getElementById(\'WSMsub_email\').value;
					window.open(\'https://www.plugins-market.com/subscribe-now/?email=\'+WSMsub_email,\'_blank\');
					
					}
				}

				if(localStorage.getItem(\'wsm_subscribed\') ==\'subsbcusers\')
				{
					
					 document.getElementById("wsm_subscribe").style.display="none";
				}
				
				function wsm_upgrade_to_pro()
				{
					
					  jQuery(\'#wsm_modal\').modal();
					
				}
</script>

			';
		
			
    }

	function wsm_upgrade_to_pro()
	{
		return ' style="color:gray" onclick="javascript: wsm_upgrade_to_pro()  "';
	}
	
    function fnPrintHeader($active=""){
        global $wsmRequestArray,$wsmAdminJavaScript;
        
        $current=isset($wsmRequestArray['subPage']) && $wsmRequestArray['subPage']!=''?$wsmRequestArray['subPage']:'';
         
        $header='<div class="wmsHorizontalTab">
                    <ul class="wmsTabList">';
                        $class=$active=='Traffic'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic').'" '.$class.'>'.__('Traffic','wp-stats-manager').'</a>';
                        $current=$current==''?'Summary':$current;
                        $class=$current=='Summary'?'class="active"':'';
                        $header.='<ul class="wmsSubList sublisthover"><li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic&subPage=Summary').'" '.$class.'>'.__('Summary','wp-stats-manager').'</a></li>';
                        
                        $current=$current==''?'UsersOnline':$current;
                        $class=$current=='UsersOnline'?'class="active"':'';
                        $header .='<li><a '.$this->wsm_upgrade_to_pro().' href="#" '.$class.'>'.__('Users Online','wp-stats-manager').'</a></li>';
                        
                        $current=$current==''?'TrafStats':$current;
                        $class=$current=='TrafStats'?'class="active"':'';
                        $header.='<li><a '.$this->wsm_upgrade_to_pro().' href="#" '.$class.'>'.__('Traffic Stats','wp-stats-manager').'</a></li></ul>';
                        $header.='</li>';
                        
                        $class=$active=='Traffic Sources'?'class="active"':'';
                        
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_trafficsrc').'" '.$class.'>'.__('Traffic Sources','wp-stats-manager').'</a>';
                        
                        $current=$current==''?'RefSites':$current;
                        $class=$current=='RefSites'?'class="active"':'';
                        $header.='<ul class="wmsSubList sublisthover"><li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_trafficsrc&subPage=RefSites').'" '.$class.'>'.__('Refering Sites','wp-stats-manager').'</a></li>';
                        
                        $current=$current==''?'SearchEngines':$current;
                        $class=$current=='SearchEngines'?'class="active"':'';
                        $header.='<li><a '.$this->wsm_upgrade_to_pro().' href="#"'.$class.'>'.__('Search Engines','wp-stats-manager').'</a></li>';
                        
                        $current=$current==''?'SearchKeywords':$current;
                        $class=$current=='SearchKeywords'?'class="active"':'';
                        $header.='<li><a  '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('Search Keywords','wp-stats-manager').'</a></li></ul>';
                        
                        $header.='</li>';
							
                        $class=$active=='Visitors'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_visitors').'" '.$class.'>'.__('Visitors','wp-stats-manager').'</a>';
                        
                        $current=$current==''?'bosl':$current;
                        $class=$current=='bosl'?'class="active"':'';
                        $header.='<ul class="wmsSubList sublisthover"><li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_visitors&subPage=bosl').'" '.$class.'>'.__('Browser/OS/Languages','wp-stats-manager').'</a></li>';
                        
                        $current=$current==''?'GeoLocation':$current;
                        $class=$current=='GeoLocation'?'class="active"':'';
                        $header .='<li><a  '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('GeoLocation','wp-stats-manager').'</a></li></ul>';
                        $header.='</li>';
                        
                        $class=$active=='Content'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_content').'" '.$class.'>'.__('Content','wp-stats-manager').'</a>';
                        
                        $current=$current==''?'byURL':$current;
                        $class=$current=='byURL'?'class="active"':'';
                        $header.='<ul class="wmsSubList sublisthover"><li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_content&subPage=byURL').'" '.$class.'>'.__('Traffic By URL','wp-stats-manager').'</a></li>';
                        $current=$current==''?'byTitle':$current;
                        $class=$current=='byTitle'?'class="active"':'';
                        $header.='<li><a '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('Traffic By Title','wp-stats-manager').'</a></li></ul>';
                        $header.='</li>';
                        
                        //$class=$active=='I.P. Exclusion'?'class="active"':'';
                        //$header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_ipexc').'" '.$class.'>'.__('I.P. Exclusion','wp-stats-manager').'</a></li>';
                        
						$class=$active=='Settings'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_settings').'" '.$class.'>'.__('Settings','wp-stats-manager').'</a>';
                        $header.='<ul class="sublist-section wmsSubList sublisthover" data-url="'.admin_url('admin.php?page='.WSM_PREFIX.'_settings').'"><li><a class="" href="#generalsettings">'.__('General settings','wp-stats-manager').'</a></li><li><a class="" href="#ipexclusion">'.__('IP Exclusion','wp-stats-manager').'</a></li><li><a class="" '.$this->wsm_upgrade_to_pro().' href="#" >'.__('Email Reports','wp-stats-manager').'</a></li><li><a class="" '.$this->wsm_upgrade_to_pro().' href="#" >'.__('Admin dashboard','wp-stats-manager').'</a></li><li><a class="" '.$this->wsm_upgrade_to_pro().' href="#" >'.__('Plugin Main Page (statistics dashboard)','wp-stats-manager').'</a></li><li><a class="" '.$this->wsm_upgrade_to_pro().' href="#" >'.__('Short-Codes','wp-stats-manager').'</a></li></ul>';
                        $header.='</li>';
                        
						$class=$active=='Addons'?'class="active"':'';
                        $header.= '<li><a style="background-color:#2196F3; color:white" href="'.admin_url('admin.php?page='.WSM_PREFIX.'_addons').'" '.$class.'>'.__('Add ons','wp-stats-manager').'</a></li>';
                       
						$header.='<li><a href="http://plugins-market.com/product/visitor-statistics-pro/#upgrade" '.$class.' target="_blank" style="background-color:green; color:white">'.__('Upgrade to Pro','wp-stats-manager').'</a>';
                        $header.='</li>';
						
                        
                    $header.='</ul>'.$this->wsmClear;
                   
                    if($active=='Traffic'){
                        $header.='<ul class="wmsSubList">';
                        $current=$current==''?'Summary':$current;
                        $class=$current=='Summary'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic').'&subPage=Summary" '.$class.'>'.__('Summary','wp-stats-manager').'</a></li>';
                        $class=$current=='UsersOnline'?'class="active"':'';
                        $header.='<li><a '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('Users Online','wp-stats-manager').'</a></li>';
                        $class=$current=='TrafStats'?'class="active"':'';
                        $header.='<li><a '.$this->wsm_upgrade_to_pro().' href="#" '.$class.'>'.__('Traffic Stats','wp-stats-manager').'</a></li>';
                        $header.='</ul>'.$this->wsmClear;
                        if($current=='UsersOnline'){
                            $wsmAdminJavaScript.='arrLiveStats.push("wsmTopTitle");';
                            $onlineVisitors=$this->objDatabase->fnGetTotalVisitorsCount('Online');
                            $browsingPages=$this->objDatabase->fnGetTotalBrowsingPages();
                            $subTab=isset($wsmRequestArray['subTab']) && $wsmRequestArray['subTab']!=''?$wsmRequestArray['subTab']:'';
                           
                            $header.= '<div class="wsmTopTitle"><span class="wsmOnline">'.__('Users Online','wp-stats-manager').'&nbsp;:&nbsp;<b>'.$onlineVisitors.'</b></span><span class="wsmBrowsing">'.__('Browing','wp-stats-manager').':&nbsp;<b>'.$browsingPages.'</b>&nbsp;'.__('pages','wp-stats-manager').'</span></div>';
                            $subClass=$subTab=='summary'?'class="active"':'';
                            $header.= '<ul class="wmsTabList wsmSubTabList">
                            <li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic').'&subPage=UsersOnline&subTab=summary" '.$subClass.'>'.__('Summary','wp-stats-manager').'</a></li>';
                            $subClass=$subTab=='recent'?'class="active"':'';
                            $header.= '<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic').'&subPage=UsersOnline&subTab=recent" '.$subClass.'>'.__('Recent','wp-stats-manager').'</a></li>';
                            $subClass=$subTab=='mavis'?'class="active"':'';
                            $header.= '<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic').'&subPage=UsersOnline&subTab=mavis" '.$subClass.'>'.__('Most Active Visitors','wp-stats-manager').'</a></li>';
                            $subClass=$subTab=='popPages'?'class="active"':'';
                            $header.= '<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic').'&subPage=UsersOnline&subTab=popPages" '.$subClass.'>'.__('Popular Pages','wp-stats-manager').'</a></li>';
                            $subClass=$subTab=='popReferrer'?'class="active"':'';
                            $header.= '<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_traffic').'&subPage=UsersOnline&subTab=popReferrer" '.$subClass.'>'.__('Popular Referrers','wp-stats-manager').'</a></li>';
                            $subClass=$subTab=='geoLocation'?'class="active"':'';
                            $header.= '<li><a  '.$this->wsm_upgrade_to_pro().' href="#" '.$subClass.'>'.__('Geo Location','wp-stats-manager').'</a></li>';
                            $header.= '</ul>'.$this->wsmClear;
                        }
                    }
                    if($active=='Traffic Sources'){
                        $header.='<ul class="wmsSubList">';
                        $current=$current==''?'RefSites':$current;
                        $class=$current=='RefSites'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_trafficsrc').'&subPage=RefSites" '.$class.'>'.__('Refering Sites','wp-stats-manager').'</a></li>';
                        $class=$current=='SearchEngines'?'class="active"':'';
                        $header.='<li><a  '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('Search Engines','wp-stats-manager').'</a></li>';
                        $class=$current=='SearchKeywords'?'class="active"':'';
                        $header.='<li><a  '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('Search Keywords','wp-stats-manager').'</a></li>';
                        $header.='</ul>';
                    }
                    if($active=='Visitors'){
                        $header.='<ul class="wmsSubList">';
                        $current=$current==''?'bosl':$current;
                        $class=$current=='bosl'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_visitors').'&subPage=bosl" '.$class.'>'.__('Browser/OS/Languages','wp-stats-manager').'</a></li>';
                        $class=$current=='GeoLocation'?'class="active"':'';
                        $header.='<li><a  '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('GeoLocation','wp-stats-manager').'</a></li>';
                        $header.='</ul>';
                    }
                    if($active=='Content'){
                        $header.='<ul class="wmsSubList">';
                        $current=$current==''?'byURL':$current;
                        $class=$current=='byURL'?'class="active"':'';
                        $header.='<li><a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_content').'&subPage=byURL" '.$class.'>'.__('Traffic By URL','wp-stats-manager').'</a></li>';
                        $class=$current=='byTitle'?'class="active"':'';
                        $header.='<li><a '.$this->wsm_upgrade_to_pro().' href="#"  '.$class.'>'.__('Traffic By Title','wp-stats-manager').'</a></li>';
                        $header.='</ul>';
                    }
                  $header.='</div>'.$this->wsmClear;
        return $header;
                  //$color = get_user_meta(get_current_user_id(), 'admin_color', true);
    }
    function fnShowTodayStats(){
        echo  do_shortcode("[".WSM_PREFIX."_showDayStats]");
        echo  do_shortcode("[".WSM_PREFIX."_showCurrentStats]");
        echo  do_shortcode("[".WSM_PREFIX."_showForeCast]");
        //echo $html;
    }
    
    function fnShowDailyStatBox($post, $arrParam){
        echo do_shortcode("[".WSM_PREFIX."_showDayStatBox]");       
    }
    function fnStatFilterBox(){
        echo  do_shortcode("[".WSM_PREFIX."_showStatFilterBox]"); 
    }
    function fnStatFilterBox4Referral(){
        echo  do_shortcode("[".WSM_PREFIX."_showStatFilterBox hide='Monthly' source='Referral']"); 
    }
    function fnShowTopReferrerSites(){
        echo  do_shortcode("[".WSM_PREFIX."_showTopReferrerList searchengine='".( isset($_REQUEST['subPage']) && $_REQUEST['subPage'] == 'SearchEngines' ? 1 : '' )."' ]"); 
    }
	function fnStatsSearchKeywords(){
		echo do_shortcode("[".WSM_PREFIX."_showStatKeywords]");
	}
	function fnShowOsStatBox(){
		echo do_shortcode("[".WSM_PREFIX."_showVisitorsDetailGraph]");
	}
	function fnShowVisitorsDetails(){
		echo do_shortcode("[".WSM_PREFIX."_showVisitorsDetail]");
	}
	function fnShowGeoLocationStats(){
		echo do_shortcode("[".WSM_PREFIX."_showGeoLocationGraph]");
	}
	function fnShowGeoLocationDetails(){
		echo do_shortcode("[".WSM_PREFIX."_showGeoLocationDetails]");
	}
	
    function fnShowDaysStatsGraph(){
        $arrPostData=wsmSanitizeFilteredPostData();
        if($arrPostData['filterWay']!='Range'){
            echo  do_shortcode("[".WSM_PREFIX."_showDayStatsGraph]");
        }
        echo  do_shortcode("[".WSM_PREFIX."_showTrafficStatsList]");
    }
    function fnShowGenStats(){
        echo  do_shortcode("[".WSM_PREFIX."_showGenStats]");
        //echo $html;
    }
    function fnShowLastDaysStats(){
        echo  do_shortcode("[".WSM_PREFIX."_showLastDaysStats]");
        echo  do_shortcode("[".WSM_PREFIX."_showLastDaysStatsChart id='LastDaysChart2']");
        //echo $html;
    }
    function fnShowGeoLocationChart(){
        echo  do_shortcode("[".WSM_PREFIX."_showGeoLocation]");
    }
    function fnShowRecentVisitedPages(){
        echo  do_shortcode("[".WSM_PREFIX."_showRecentVisitedPages]");
    }
    function fnShowRecentVisitedPagesDetails(){
        echo  do_shortcode("[".WSM_PREFIX."_showRecentVisitedPagesDetails]");
    }
    function fnShowPopularPages(){
        echo  do_shortcode("[".WSM_PREFIX."_showPopularPages]");
    }
    function fnShowPopularReferrers(){
        echo  do_shortcode("[".WSM_PREFIX."_showPopularReferrers]");
    }
    function fnShowMostActiveVisitors(){
        echo  do_shortcode("[".WSM_PREFIX."_showMostActiveVisitors]");
    }
    function fnShowMostActiveVisitorsDetails(){
        echo  do_shortcode("[".WSM_PREFIX."_showMostActiveVisitorsDetails]");
    }
    function fnShowMostActiveVisitorsGeo(){
        echo  do_shortcode("[".WSM_PREFIX."_showMostActiveVisitorsGeo]");
    }
    function fnShowMostActiveVisitorsGeoDetails(){
        echo  do_shortcode("[".WSM_PREFIX."_showMostActiveVisitorsGeo height='450px' zoom='2']");       
    }
    function fnShowActiveVistiorsCountByCountry(){
         echo  do_shortcode("[".WSM_PREFIX."_showActiveVisitorsByCountry]");
    }
    function fnShowActiveVistiorsCountByCity(){
         echo  do_shortcode("[".WSM_PREFIX."_showActiveVisitorsByCity]");
    }
    function fnShowReffererStatBox(){
         echo  do_shortcode("[".WSM_PREFIX."_showRefferStatsBox searchengine='".( isset($_REQUEST['subPage']) && $_REQUEST['subPage'] == 'SearchEngines' ? 1 : '' )."' ]");
    }
    function fnShowReffererSearchEngineStatBox(){
         echo  do_shortcode("[".WSM_PREFIX."_showRefferStatsBox searchengine='1']");
    }
	function fnShowSearchEngineSummary(){
		echo do_shortcode('['.WSM_PREFIX.'_showSearchEngineSummary]');
	}
	function fnShowContentByURL(){
		echo do_shortcode("[".WSM_PREFIX."_showContentByURL]");
	}
	function fnShowContentURLStats(){
		echo do_shortcode("[".WSM_PREFIX."_showContentByURLStats]");
	}
	function fnIPExclusion(){
		echo do_shortcode("[".WSM_PREFIX."_showIPExclustion]");
	}
	function fnShowTitleCloud(){
		echo do_shortcode("[".WSM_PREFIX."_showTitleCloud]");
	}
    function wsmSavePluginSettings($arrPostData){
        $tzstring = get_option('wsmTimezoneString');
       
	   if(null !== (sanitize_text_field($_POST[WSM_PREFIX.'TimezoneString'])) && sanitize_text_field($_POST[WSM_PREFIX.'TimezoneString'])!=''){
            //if($tzstring!==$_POST[WSM_PREFIX.'TimezoneString']){
                update_option(WSM_PREFIX.'TimezoneString',sanitize_text_field($_POST[WSM_PREFIX.'TimezoneString']));
                wsmInitPlugin::wsm_fnCreateImportantViews();
                wsmInitPlugin::wsm_createMonthWiseViews();
				//}
        }
        if(null !== (sanitize_text_field($_POST[WSM_PREFIX.'ChartDays'])) && sanitize_text_field($_POST[WSM_PREFIX.'ChartDays'])!=''){
            update_option(WSM_PREFIX.'ChartDays',sanitize_text_field($_POST[WSM_PREFIX.'ChartDays']));
        }
        if(null !== (sanitize_text_field($_POST[WSM_PREFIX.'Country'])) && sanitize_text_field($_POST[WSM_PREFIX.'Country']) !=''){
            update_option(WSM_PREFIX.'Country', sanitize_text_field($_POST[WSM_PREFIX.'Country']));
        }
        
        if(null !== (sanitize_text_field($_POST[WSM_PREFIX.'ArchiveDays'])) && sanitize_text_field($_POST[WSM_PREFIX.'ArchiveDays'])!=''){
            update_option(WSM_PREFIX.'ArchiveDays', sanitize_text_field($_POST[WSM_PREFIX.'ArchiveDays']));
        }
        if(null !== (sanitize_text_field($_POST[WSM_PREFIX.'KeepData'])) && sanitize_text_field($_POST[WSM_PREFIX.'KeepData'])!=''){
            update_option(WSM_PREFIX.'KeepData',"1");
        }
        else
			update_option(WSM_PREFIX.'KeepData',"0");
			 
		update_option(WSM_PREFIX.'ReportScheduleTime',sanitize_text_field($_POST[WSM_PREFIX.'ReportScheduleTime']));
		
		update_option(WSM_PREFIX.'ReportEmails',sanitize_text_field($_POST[WSM_PREFIX.'ReportEmails']));
		update_option(WSM_PREFIX.'SiteDashboardNormalWidgets',sanitize_text_field($_POST[WSM_PREFIX.'SiteDashboardNormalWidgets']));
		update_option(WSM_PREFIX.'SiteDashboardSideWidgets',sanitize_text_field($_POST[WSM_PREFIX.'SiteDashboardSideWidgets']));
		
		update_option(WSM_PREFIX.'SitePluginNormalWidgets',sanitize_text_field($_POST[WSM_PREFIX.'SitePluginNormalWidgets']));
		update_option(WSM_PREFIX.'SitePluginSideWidgets',sanitize_text_field($_POST[WSM_PREFIX.'SitePluginSideWidgets']));
		
		
		if(is_admin())
		{
			if(!empty($_POST[WSM_PREFIX.'UserRoles']))
			{
				$wsmUserRoles = '';
				foreach ($_POST[WSM_PREFIX.'UserRoles'] as $v)
				{
					$wsmUserRoles .= $v.",";
				}
				
			$wsmUserRoles = substr($wsmUserRoles,0,-1);
			@update_option(WSM_PREFIX.'UserRoles',$wsmUserRoles);
			}
			
			if(!empty($_POST[WSM_PREFIX.'Robots']))
			{
				$wsmRobots = '';
				foreach ($_POST[WSM_PREFIX.'Robots'] as $v2)
				{
					$wsmRobots .= $v2.",";
				}
				
				$wsmRobots = substr($wsmRobots,0,-1);
				@update_option(WSM_PREFIX.'Robots',$wsmRobots);
			}
		}
		
	

    }
	function wsmViewAddOns(){
		$addons = apply_filters( 'wsm_addons', array() );
		$addons_settings = apply_filters( 'wsm_addons_settings', array() );
		
		if(isset($_POST['action']))
		{
			if( null !== (sanitize_text_field($_POST['action']) ) && sanitize_text_field($_POST['action']) == 'save_wsm_addons' )
			{
				echo sprintf( '<div class="notice updated"><p class="message">%s</p></div>', __( 'Settings saved.','wp-stats-manager') );
			}
		}
		
		echo $this->fnPrintTitle('Addons');
		echo '<div class="wsm_addons_panel">';
		if( is_array( $addons ) && count( $addons ) ){
			echo '<form method="post">';
			echo '<ul class="li-section">';
			$class = 'active';
			$visible = 'table';
			$active_tab = '';
			if( null !== (sanitize_text_field($_POST['tab-li-active']) ) && !empty( sanitize_text_field($_POST['tab-li-active']) ) ){
				$active_tab = sanitize_text_field($_POST['tab-li-active']);
				$visible = $class = '';
			}
			foreach( $addons as $key => $addon ){
				if( $active_tab == '#'.$key ){
					$class = 'active';
				}
				echo sprintf('<li><a class="%s" href="#%s">%s</a></li>', $class, $key, $addon );
				$class = '';
			}
			echo '</ul>';
			echo '<div class="li-section-table">';
			foreach( $addons_settings as $key => $addons_setting ){
				$setting_values = get_option( $key. '_settings' ); 
				if( $active_tab == '#'.$key ){
					$visible = 'table';
				}
				echo sprintf( '<table id="%s" style="display:%s" class="form-table">', $key, $visible );
				$visible = '';
				$field_name = $key.'_enable';
				$checked = isset( $setting_values[$field_name] ) && $setting_values[$field_name] ? 'checked' : ''; 
				echo sprintf( '<tr><th>%s</th><td><label class="switch"><input name="%s" type="checkbox" %s value="1"><div class="slider round"></div></label></td></tr>', __( 'Enable','wp-stats-manager'), $field_name, $checked );
				foreach( $addons_setting as $setting ){
					$field_name = $setting['id'];
					echo sprintf( '<tr><th>%s</th><td>', $setting['label'] );
					switch( $setting['type'] ){
						case 'checkbox';
							$checked = isset( $setting_values[$field_name] ) && $setting_values[$field_name] ? 'checked' : ''; 
							echo sprintf('<label class="switch"><input name="%s" type="checkbox" %s value="1"><div class="slider round"></div></label>', $setting['id'], $checked );
							break;
						case 'select':
							echo sprintf( '<select name="%s">', $setting['id'] );
							$selected_value = isset( $setting['default'] ) ?  $setting['default'] : '';
							$selected_value = isset( $setting_values[$field_name] ) && $setting_values[$field_name] ? $setting_values[$field_name] : $selected_value; 
							if( isset( $setting['values'] ) ){
								foreach( $setting['values'] as $key => $value ){
									$selected = '';
									if( $selected_value == $key ){
										$selected = 'selected';
									}
									echo sprintf( '<option %s value="%s">%s</option>', $selected, $key, $value );	
								}		
							}
							echo '</select>';
							break;	
						case 'post_type':
							echo sprintf( '<select multiple="multilple" class="posttype_dropdown" name="%s[]">', $setting['id'] );
							$selected_value = isset( $setting['default'] ) ?  $setting['default'] : array();
							$selected_value = isset( $setting_values[$field_name] ) && $setting_values[$field_name] ? $setting_values[$field_name] : $selected_value; 
							$args = array(
							   'public'   => true
							);
							$post_types = get_post_types($args, 'names');
								foreach( $post_types as $value ){
									if( $value != 'attachment' ){
										$selected = '';
										if( in_array( $value, $selected_value ) ){
											$selected = 'selected';
										}
										echo sprintf( '<option %s value="%s">%s</option>', $selected, $value, $value );	
									}
								}		
							echo '</select>';
							break;
					}
					echo '</td></tr>';
					
				}
				echo '</table>';
			}
			echo '</div>';
			echo '<input type="hidden" name="tab-li-active" id="tab-li-active" value="">';
			echo '<input type="hidden" name="action" value="save_wsm_addons" />';
			echo sprintf( '<p class="submit"><input type="submit" class="button button-primary" value="%s"></p>', __('Save changes','wp-stats-manager') );
			echo '</form>';
		}else{
			echo __( '<p><br /><i style="color:red">No addons installed yet.</i></p><p><table border="0" cellpadding="20px"><tr><td><a href="http://plugins-market.com/product/post-stats-add-on/" target="_blank"><img src="http://plugins-market.com/wp-content/uploads/2018/04/poststats-addon.png" width="150px"></a></td><td><a href="http://plugins-market.com/product/visitor-statistics-mini-chart-add-on/" target="_blank"><img src="http://plugins-market.com/wp-content/uploads/2018/04/minichart-addon.png" width="150px"></a></td></tr></table></p>','wp-stats-manager');
		}
		echo '</div>';
	}
    function wsmViewSettings(){
        global $wsmAdminJavaScript,$wsmAdminPageHooks;
		if(isset($_POST[WSM_PREFIX.'_form']))
		{
        if(null !== (sanitize_text_field($_POST[WSM_PREFIX.'_form'])) && sanitize_text_field($_POST[WSM_PREFIX.'_form'])==WSM_PREFIX.'_frmSettings'){
            $this->wsmSavePluginSettings($_POST);
        }
		}
        $html=$this->startWrapper;
        $html.=$this->fnPrintTitle('Settings');
        $current_offset = get_option('gmt_offset');
        $tzstring = get_option(WSM_PREFIX.'TimezoneString');
        $chartDays = get_option(WSM_PREFIX.'ChartDays');
        $country = get_option(WSM_PREFIX.'Country');
        $googleMapAPI = get_option(WSM_PREFIX.'GoogleMapAPI');
        $ArchiveDays = get_option(WSM_PREFIX.'ArchiveDays');
        $KeepData = get_option(WSM_PREFIX.'KeepData');
        $KeepDataChk="";
        if($KeepData=="1")
			$KeepDataChk = "checked='checked'";
		
        $reportScheduleTime = get_option(WSM_PREFIX.'ReportScheduleTime');
        $reportStats = get_option(WSM_PREFIX.'ReportStats');
        $reportEmails = get_option(WSM_PREFIX.'ReportEmails');
		$siteDashboardNormalWidgets = get_option(WSM_PREFIX.'SiteDashboardNormalWidgets');
		$siteDashboardSideWidgets = get_option(WSM_PREFIX.'SiteDashboardSideWidgets');
		$dashboard_widget = get_option(WSM_PREFIX.'Dashboard_widget');
		$sitePluginNormalWidgets = get_option(WSM_PREFIX.'SitePluginNormalWidgets');
		$sitePluginSideWidgets = get_option(WSM_PREFIX.'SitePluginSideWidgets');
		$plugin_widget = get_option(WSM_PREFIX.'Plugin_widget');
		
		$UserRoles = get_option(WSM_PREFIX.'UserRoles');
        $wsmRobots = get_option(WSM_PREFIX.'Robots');
		
        $check_zone_info = true;
        // Remove old Etc mappings. Fallback to gmt_offset.
        if ( false !== strpos($tzstring,'Etc/GMT') )
            $tzstring = '';
        if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
            $check_zone_info = false;
            if ( 0 == $current_offset )
                $tzstring = 'UTC+0';
            elseif ($current_offset < 0)
                $tzstring = 'UTC' . $current_offset;
            else
                $tzstring = 'UTC+' . $current_offset;
        }
		$mailTiming = '<select id="'.WSM_PREFIX.'ReportScheduleTime" name="'.WSM_PREFIX.'ReportScheduleTime"><option value=""></option>';
		$scheduleArray = array( 1 => 'Every Day', 3 => 'Every 3 Days', 7 => 'Every Week', 30 => 'Every Month' );
		foreach( $scheduleArray as $key => $value ){
			$mailTiming .= '<option '.($reportScheduleTime == $key ? 'selected' : '').' value="'.$key.'">'.$value.'</option>';
		}
		$mailTiming .= '</select>';
		
		$report_stats_list = array('general_stats_new' => __('General Stats','wp-stats-manager'),
									'daily_stats'	=>	__('Daily Stats','wp-stats-manager'),
									'referral_website_stats'	=>	__('Referral Website Stats','wp-stats-manager'),
									'search_engine_stats'	=>	__('Top Search Engine Stats','wp-stats-manager'),
									'traffic_by_title_stats' => __('Title Stats','wp-stats-manager'),
									'top_search_engine_stats' => __('Top Search Engine Stats','wp-stats-manager'),
									'os_wise_visitor_stats'	=>	__('OS base Visitor Stats','wp-stats-manager'),
									'browser_wise_visitor_stats'	=>	__('Browser base Visitor Stats','wp-stats-manager'),
									'screen_wise_visitor_stats'	=>	__('Screen base Visitor Stats','wp-stats-manager'),
									'country_wise_visitor_stats'	=>	__('Today Countries Stats','wp-stats-manager'),
									'city_wise_visitor_stats'	=>	__('Today Cities Stats','wp-stats-manager'),
									/*'recent_visit_pages' => __('Traffic By Title','wp-stats-manager'),*/
									'recent_active_visitors' => __('Users Online','wp-stats-manager')
							);
		
		//$reportStatsHTML = '<select id="'.WSM_PREFIX.'ReportStats" multiple name="'.WSM_PREFIX.'ReportStats[]">';
		$reportStatsHTML = '<table class="report_list_table">';
		$reportWidget = $dashboardNormalWidget = $dashboardSideWidget = $pluginNormalWidget = $pluginSideWidget = '';
		
		$dashboardNormalWidgetList = $siteDashboardNormalWidgets ? explode(',', $siteDashboardNormalWidgets) : array();
		$dashboardSideWidgetList = $siteDashboardSideWidgets ? explode(',', $siteDashboardSideWidgets) : array();
		
		$pluginNormalWidgetList = $sitePluginNormalWidgets ? explode(',', $sitePluginNormalWidgets) : array();
		$pluginSideWidgetList = $sitePluginSideWidgets ? explode(',', $sitePluginSideWidgets) : array();
		
		foreach( $report_stats_list as $key => $value ){
			//$reportStatsHTML .= '<option '.( (is_array($reportStats) && in_array( $key, $reportStats)) ? 'selected' : '' ).' value="'.$key.'">'.$value.'</option>';
			$status =(is_array($reportStats) && in_array( $key, $reportStats)) ? 1 : 0;
			if( !in_array($key, array( 'recent_visit_pages', 'traffic_by_title_stats', 'recent_active_visitors' ) ) ){
				$reportStatsHTML .= sprintf( '<tr><td>%s</td><td><label class="switch"><input name="%s" type="checkbox" %s value="%s" ><div class="slider round"></div></label></td></tr>', $value, WSM_PREFIX.'ReportStats[]', ($status?'checked':''), $key );
			}
			$reportWidget .= sprintf( '<div data-id="%s">%s</div>', $key, $value );
			$dashboardNormalWidget .= sprintf( '<li><i class="handle"></i> %s <label class="switch"><input name="%s" type="checkbox" %s value="%s" ><div class="slider round"></div></label></li>', $value, WSM_PREFIX.'Dashboard_widget[normal][]', ($status?'checked':''), $key );
			$pluginNormalWidget .= sprintf( '<li><i class="handle"></i> %s <label class="switch"><input name="%s" type="checkbox" %s value="%s" ><div class="slider round"></div></label></li>', $value, WSM_PREFIX.'Plugin_widget[normal][]', ($status?'checked':''), $key );
		}
		if( count( $dashboardNormalWidgetList ) ){
			$dashboardNormalWidget = '';
			foreach( $dashboardNormalWidgetList as $widget ){
				$status = 0;
				$widget = trim($widget);
				if( is_array( $dashboard_widget ) && isset( $dashboard_widget['normal'] ) && in_array( $widget, $dashboard_widget['normal'] ) ){
					$status = 1;
				}
				$dashboardNormalWidget .= sprintf( '<li><i class="handle"></i> %s <label class="switch"><input name="%s" type="checkbox" %s value="%s" ><div class="slider round"></div></label></li>', $report_stats_list[$widget], WSM_PREFIX.'Dashboard_widget[normal][]', ($status?'checked':''), $widget );
			}
		}
		if( count( $dashboardSideWidgetList ) ){
			foreach( $dashboardSideWidgetList as $widget ){
				$status = 0;
				$widget = trim($widget);
				if( is_array( $dashboard_widget ) && isset( $dashboard_widget['side'] ) && in_array( $widget, $dashboard_widget['side'] ) ){
					$status = 1;
				}
				$dashboardSideWidget .= sprintf( '<li><i class="handle"></i> %s <label class="switch"><input name="%s" type="checkbox" %s value="%s" ><div class="slider round"></div></label></li>', $report_stats_list[$widget], WSM_PREFIX.'Dashboard_widget[side][]', ($status?'checked':''), $widget );
			}
		}
		if( count( $pluginNormalWidgetList ) ){
			$pluginNormalWidget = '';
			foreach( $pluginNormalWidgetList as $widget ){
				$status = 0;
				$widget = trim($widget);
				if( is_array( $plugin_widget ) && isset( $plugin_widget['normal'] ) && in_array( $widget, $plugin_widget['normal'] ) ){
					$status = 1;
				}
				$pluginNormalWidget .= sprintf( '<li><i class="handle"></i> %s <label class="switch"><input name="%s" type="checkbox" %s value="%s" ><div class="slider round"></div></label></li>', $report_stats_list[$widget], WSM_PREFIX.'Plugin_widget[normal][]', ($status?'checked':''), $widget );
			}
		}
		if( count( $pluginSideWidgetList ) ){
			foreach( $pluginSideWidgetList as $widget ){
				$status = 0;
				$widget = trim($widget);
				if( is_array( $plugin_widget ) && isset( $plugin_widget['side'] ) && in_array( $widget, $plugin_widget['side'] ) ){
					$status = 1;
				}
				$pluginSideWidget .= sprintf( '<li><i class="handle"></i> %s <label class="switch"><input name="%s" type="checkbox" %s value="%s" ><div class="slider round"></div></label></li>', $report_stats_list[$widget], WSM_PREFIX.'Plugin_widget[side][]', ($status?'checked':''), $widget );
			}
		}
		//$reportStatsHTML .= '</select>';
		$reportStatsHTML .= '</table>';
		$wsmStatistics=new wsmStatistics;
		//print_r($_POST);
		//echo 'active'.$_POST['tab-li-active'];
        $html.='<form name="'.WSM_PREFIX.'_frmSettings" method="post">';
        $html.='<input type="hidden" name="'.WSM_PREFIX.'_form" value="'.WSM_PREFIX.'_frmSettings">';
        $generalsettings="active";
        $ipexclusion=$sitedashboard=$report=$summarywidget=$shortcodelist='';
		$ipexclusion1=$generalsettings1=$sitedashboard1=$report1=$summarywidget1=$shortcodelist1='';
        if(isset($_POST['tab-li-active']) && null !== (sanitize_text_field($_POST['tab-li-active'])) && sanitize_text_field($_POST['tab-li-active'])!=='')
        {
			if(sanitize_text_field($_POST['tab-li-active'])=="#generalsettings")
			{
					$generalsettings="active";
					$ipexclusion=$sitedashboard=$report=$summarywidget=$shortcodelist='';
					
					$generalsettings1='style="display: table;"';
					$ipexclusion1=$sitedashboard1=$report1=$summarywidget1=$shortcodelist1='style="display: none;"';
			}
			else if(sanitize_text_field($_POST['tab-li-active'])=="#ipexclusion")
			{
					$ipexclusion="active";
					$generalsettings=$report=$summarywidget=$shortcodelist='';
					
					$ipexclusion1='style="display: table;"';
					$generalsettings1=$report1=$summarywidget1=$shortcodelist1='style="display: none;"';
			}			
			else if(sanitize_text_field($_POST['tab-li-active'])=="#sitedashboard")
			{
					$sitedashboard="active";
					$ipexclusion=$generalsettings=$report=$summarywidget=$shortcodelist='';
					
					$sitedashboard1='style="display: table;"';
					$ipexclusion1=$generalsettings1=$report1=$summarywidget1=$shortcodelist1='style="display: none;"';
			}
			else if(sanitize_text_field($_POST['tab-li-active'])=="#report")
			{
					$report="active";
					$ipexclusion=$generalsettings=$sitedashboard=$summarywidget=$shortcodelist='';
					
					$report1='style="display: table;"';
					$ipexclusion1=$generalsettings1=$sitedashboard1=$summarywidget1=$shortcodelist1='style="display: none;"';
			}
			else if(sanitize_text_field($_POST['tab-li-active'])=="#summarywidget")
			{
					$summarywidget="active";
					$ipexclusion=$generalsettings=$sitedashboard=$report=$shortcodelist='';
					
					$summarywidget1='style="display: table;"';
					$ipexclusion1=$generalsettings1=$sitedashboard1=$report1=$shortcodelist1='style="display: none;"';
			}
			else if(sanitize_text_field($_POST['tab-li-active'])=="#shortcodelist")
			{
					$shortcodelist="active";
					$ipexclusion=$generalsettings=$sitedashboard=$report=$summarywidget='';
					
					$shortcodelist1='style="display: table;"';
					$ipexclusion1=$generalsettings1=$sitedashboard1=$report1=$summarywidget1='style="display: none;"';
			}
			
		}
		ob_start(); 
		include WSM_DIR."includes/wsm_shortcodeTable.php"; 
		$shortCodeData=ob_get_contents(); 
		ob_clean();
        $html.='<ul class="li-section">
					<li><a class="'.$generalsettings.'" href="#generalsettings">'.__('General settings','wp-stats-manager').'</a></li>
					<li><a class="'.$ipexclusion.'" href="#ipexclusion">'.__('IP Exclusion','wp-stats-manager').'</a></li>
					<li><a class="'.$report.'" href="#" '.$this->wsm_upgrade_to_pro().' >'.__('Email Reports','wp-stats-manager').'</a></li>
					<li><a class="'.$sitedashboard.'" href="#" '.$this->wsm_upgrade_to_pro().'>'.__('Admin dashboard','wp-stats-manager').'</a></li>
					<li><a class="'.$summarywidget.'" href="#" '.$this->wsm_upgrade_to_pro().'>'.__('Plugin Main Page (statistics dashboard)','wp-stats-manager').'</a></li>
					<li><a class="'.$shortcodelist.'" href="#" '.$this->wsm_upgrade_to_pro().'>'.__('Short-Codes','wp-stats-manager').'</a></li>
				</ul>';
        $html.='<div class="li-section-table"><table class="form-table" id="generalsettings" '.$generalsettings1.'><tbody>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'TimezoneString">'.__('Timezone','wp-stats-manager').'</label></th>
                    <td>'.$this->wsmGetCountryDropDown($country).$this->wsmGetTimeZoneDropDown($tzstring).'
                    <p class="description" id="timezone-description">'.__( 'Choose either a city in the same timezone as you or a UTC timezone offset.','wp-stats-manager').'</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'ChartDays">'.__('Chart Days','wp-stats-manager').'</label></th>
                    <td>'.$this->wsmGetChartDaysDropDown($chartDays).'
                    <p class="description">'.__( 'Choose number of days to show statistics on the summary page.','wp-stats-manager').'</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'ArchiveDays">'.__('Archive Data','wp-stats-manager').'</label></th>
                    <td>'.$this->wsmGetArchiteDaysDropDown($ArchiveDays).'
						<p class="description">'.__('You can set archive data setting for 30,60,90 or 180 day.','wp-stats-manager').'</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'KeepData1">'.__('Keep Data','wp-stats-manager').'</label></th>
                    <td>
						<input type="checkbox" name="'.WSM_PREFIX.'KeepData" id="'.WSM_PREFIX.'KeepData" value="1" '.$KeepDataChk.'/> <label for="'.WSM_PREFIX.'KeepData">'.__('Keep data after unistalling the plugin?','wp-stats-manager').'</label>
						<p class="description">'.__('Check to keep data and uncheck to remove data.','wp-stats-manager').'</p>
                    </td>
                </tr>';
				
				
				
				
				
				
				
				
				
				
								
	if(is_admin())
	{		
    global $wp_roles;

    if ( !isset( $wp_roles ) ) $wp_roles = new WP_Roles();
	$capabilites = array();

    $available_roles_names = $wp_roles->get_names();//we get all roles names

    $available_roles_capable = array();
    foreach ($available_roles_names as $role_key => $role_name) { //we iterate all the names
        $role_object = get_role( $role_key );//we get the Role Object
        $array_of_capabilities = $role_object->capabilities;//we get the array of capabilities for this role
       // print_r($array_of_capabilities);
		$available_roles_capable[$role_key] = $role_name; //we populate the array of capable roles
    }
    


		
		$html .= '<tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'KeepData1">'.__('Accessibility','wp-stats-manager').'</label></th>
                    <td>';
		$UserRoles = get_option(WSM_PREFIX.'UserRoles');
		
		$UserRoles_arr = explode(',',$UserRoles);
		
		
		$html .="<select id='wsmUserRoles' name='wsmUserRoles[]' multiple='true' style='width:50%;'>";

		foreach($available_roles_capable as $role=>$role_name)
		{
			$translated_role_name = $role_name;
			if(in_array($role, $UserRoles_arr) or $translated_role_name == 'Administrator' or $translated_role_name == 'Super Admin')
			{
				$selected_value = 'selected=selected';
			}else{
				$selected_value = '';
			}
			 $html .="<option ".$selected_value." value='".$role."'>".translate_user_role($translated_role_name)."</option>";
		}
		
		$html .='</select></tr><tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'KeepData1">'.__('IP/Robot Exclusions','wp-stats-manager').'</label></th>
                    <td>';
		
		
	
$wsm_robots = array(
	'007ac9',
	'bot',
    'slurp',
    'crawler',
    'spider',
    'curl',
    'facebook',
    'fetch',
	'5bot',
	'A6-Indexer',
	'AbachoBOT',
	'accoona',
	'cbot',
	'clamantivirus',
	'cliqzbot',
	'clumboot',
	'coccoc',
	'CrocCrawler',
	'crowsnest.tv',
	'dbot',
	'dl2bot',
	'dotbot',
	'downloadbot',
	'duckduckgo',
	'Dumbot',
	'EasouSpider',
	'eStyle',
	'EveryoneSocialBot',
	'Exabot',
	'ezooms',
	'facebook.com',
	'facebookexternalhit',
	'FAST',
	'Feedfetcher-Google',
	'feedzirra',
	'findxbot',
	'Firfly',
	'FriendFeedBot',
	'AcoiRobot',
	'AddThis.com',
	'ADmantX',
	'AdsBot-Google',
	'advbot',
	'AhrefsBot',
	'aiHitBot',
	'alexa',
	'alphabot',
	'AltaVista',
	'AntivirusPro',
	'anyevent',
	'appie',
	'Applebot',
	'archive.org_bot',
	'Ask Jeeves',
	'ASPSeek',
	'Baiduspider',
	'Benjojo',
	'BeetleBot',
	'bingbot',
	'Blekkobot',
	'blexbot',
	'BOT for JCE',
	'bubing',
	'Butterfly',
	'froogle',
	'GeonaBot',
	'Gigabot',
	'girafabot',
	'gimme60bot',
	'glbot',
	'Googlebot',
	'GroupHigh',
	'ia_archiver',
	'IDBot',
	'InfoSeek',
	'inktomi',
	'IstellaBot',
	'jetmon',
	'Kraken',
	'Leikibot',
	'linkapediabot',
	'linkdexbot',
	'LinkpadBot',
	'LoadTimeBot',
	'looksmart',
	'ltx71',
	'Lycos',
	'Mail.RU_Bot',
	'Me.dium',
	'meanpathbot',
	'mediabot',
	'medialbot',
	'Mediapartners-Google',
	'MJ12bot',
	'msnbot',
	'MojeekBot',
	'monobot',
	'moreover',
	'MRBOT',
	'NationalDirectory',
	'NerdyBot',
	'NetcraftSurveyAgent',
	'niki-bot',
	'nutch',
	'Openbot',
	'OrangeBot',
	'owler',
	'p4Bot',
	'PaperLiBot',
	'pageanalyzer',
	'PagesInventory',
	'Pimonster',
	'porkbun',
	'pr-cy',
	'proximic',
	'pwbot',
	'r4bot',
	'rabaz',
	'Rambler',
	'Rankivabot',
	'revip',
	'riddler',
	'rogerbot',
	'Scooter',
	'Scrubby',
	'scrapy.org',
	'SearchmetricsBot',
	'sees.co',
	'SemanticBot',
	'SemrushBot',
	'SeznamBot',
	'sfFeedReader',
	'shareaholic-bot',
	'sistrix',
	'SiteExplorer',
	'Socialradarbot',
	'SocialSearch',
	'Sogou web spider',
	'Spade',
	'spbot',
	'SpiderLing',
	'SputnikBot',
	'Superfeedr',
	'SurveyBot',
	'TechnoratiSnoop',
	'TECNOSEEK',
	'Teoma',
	'trendictionbot',
	'TweetmemeBot',
	'Twiceler',
	'Twitterbot',
	'Twitturls',
	'u2bot',
	'uMBot-LN',
	'uni5download',
	'unrulymedia',
	'UptimeRobot',
	'URL_Spider_SQL',
	'Vagabondo',
	'vBSEO',
	'WASALive-Bot',
	'WebAlta Crawler',
	'WebBug',
	'WebFindBot',
	'WebMasterAid',
	'WeSEE',
	'Wotbox',
	'wsowner',
	'wsr-agent',
	'www.galaxy.com',
	'x100bot',
	'XoviBot',
	'xzybot',
	'yandex',
	'Yahoo',
	'Yammybot',
	'YoudaoBot',
	'ZyBorg',
	'ZemlyaCrawl'
);

$wsmRobots = get_option(WSM_PREFIX.'Robots');
		
		$html .="<select id='wsmRobots' name='wsmRobots[]' multiple='true' style='width:50%;'>";
		
		
		$wsmRobots_arr = explode(',',$wsmRobots);
		
		foreach($wsm_robots as $k=>$v)
		{
			if(in_array($v, $wsmRobots_arr))
			{
				$selected_value = 'selected=selected';
			}else{
				$selected_value = '';
			}
			 $html .="<option value='".$v."' ".$selected_value .">".$v."</option>";
		}
		$html .='</select>
		

';


		
		$html .='<script>
new SlimSelect({
  select: \'#wsmUserRoles\'
})


new SlimSelect({
  select: \'#wsmRobots\'
})

</script>

</td>
                </tr>
';
	}
	
	
	
	
	
	
	
	
                $html .='</tbody></table>
                <table class="form-table" id="report" '.$report1.'><tbody>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'ReportScheduleTime">'.__('Scheduled Report Time','wp-stats-manager').'</label></th>
                    <td>'.$mailTiming.'
                    <p class="description">'.__( 'Select time for receiving report mail.','wp-stats-manager').'</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'ReportStats">'.__('Reports','wp-stats-manager').'</label></th>
                    <td>'.$reportStatsHTML.'
                    <p class="description">'.__( 'Select stats type which you want to receive as report in mail.','wp-stats-manager').'</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'ReportEmails">'.__('Report Notification','wp-stats-manager').'</label></th>
                    <td><textarea cols="50" id="'.WSM_PREFIX.'ReportEmails" name="'.WSM_PREFIX.'ReportEmails">'.$reportEmails.'</textarea>&nbsp;&nbsp;<a href="#" class="button button-primary send_test_mail">Send Test Mail</a>
                    <p class="description">'.__( 'Add more than one email by comma seperator.','wp-stats-manager').'</p>
                    </td>
                </tr></tbody></table>
                <table class="form-table" id="sitedashboard" '.$sitedashboard1.'><tbody>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'SiteDashboardWidgets">'.__('Site Dashboard Widgets','wp-stats-manager').'</label></th>
                    <td>
					<div class="site_dashboard_widget_panel"></div>
					<table><tr><th>'.__('Normal','wp-stats-manager').'</th><th>'.__('Side','wp-stats-manager').'</th></tr><tr><td><ul class="site_dashboard_widget_handler" id="site_dashboard_widget_handler_1">'.$dashboardNormalWidget.'</ul></td><td><ul id="site_dashboard_widget_handler_2" class="site_dashboard_widget_handler">'.$dashboardSideWidget.'</ul></td></tr></table>
                    <p class="description">'.__( 'You can drag and drop widget here.','wp-stats-manager').'</p>
					<input type="hidden" name="'.WSM_PREFIX.'SiteDashboardNormalWidgets"  value="" />
					<input type="hidden" name="'.WSM_PREFIX.'SiteDashboardSideWidgets" value="" />
                    </td>
                </tr></tbody></table>
                <table class="form-table" id="summarywidget" '.$summarywidget1.'><tbody>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'SitePluginWidgets">'.__('Plugin Summary Widgets','wp-stats-manager').'</label></th>
                    <td>
					<div class="site_dashboard_widget_panel"></div>
					<table><tr><th>'.__('Normal','wp-stats-manager').'</th><th>'.__('Side','wp-stats-manager').'</th></tr><tr><td><ul class="site_plugin_widget_handler" id="site_plugin_widget_handler_1">'.$pluginNormalWidget.'</ul></td><td><ul id="site_plugin_widget_handler_2" class="site_plugin_widget_handler">'.$pluginSideWidget.'</ul></td></tr></table>
                    <p class="description">'.__( 'You can drag and drop widget here.','wp-stats-manager').'</p>
					<input type="hidden" name="'.WSM_PREFIX.'SitePluginNormalWidgets"  value="" />
					<input type="hidden" name="'.WSM_PREFIX.'SitePluginSideWidgets" value="" />
                    </td>
                </tr>
                </tbody></table>
                 <table class="form-table myshortcodelist" id="shortcodelist" '.$shortcodelist1.'><tbody>
                <tr>
                    <th scope="row"><label for="'.WSM_PREFIX.'">'.__('Shortcodes','wp-stats-manager').'</label></th>
                </tr>
                <tr>
					<td>
					<div class="shortcode_panel">';
						$html.=$shortCodeData;
			$html.='</div>
                    <p class="description">'.__( 'Shortcode lists are going to display here.','wp-stats-manager').'</p>
					</td>
                </tr>
                
                </tbody></table>
                </div>
                
                <p class="submit"><input type="hidden" name="tab-li-active" id="tab-li-active" value=""><input type="submit" name="submit" id="submit" class="button button-primary" value="'.__('Save Changes','wp-stats-manager').'"></p>';
        $html.='</form>';
        
        $html .= '
                <table class="form-table" style="display:none" id="ipexclusion" '.$ipexclusion1.'><tbody>
                <tr>
					
                    <td>
						<div id="dashboard-widgets-wrap" class="wsmMetaboxContainer">
							<div id="dashboard-widgets" class="metabox-holder">
								<form name="wsmmainMetboxForm" id="wsmmainMetboxForm" method="post">
									<input id="_wpnonce" name="_wpnonce" value="100250cbd1" type="hidden"><input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=wsm_ipexc" />
									<div id="wsm-postbox-container-1" class="postbox-containe">
										<div id="bottom-sortables" class="meta-box-sortables ui-sortable">
											<div id="wsm_ipexc" class="postbox">
													<h2 class="hndle ui-sortable-handle test"><span>'.__('I.P. Exclution','wp-stats-manager').'</span></h2>					 
													<div class="inside">'.$wsmStatistics->wsm_showIPExclustion('').'</div>
											</div>
										</div>
									</div>
									<input type="hidden" id="meta-box-order-nonce" name="meta-box-order-nonce" value="9c03ae7329" /><input type="hidden" id="closedpostboxesnonce" name="closedpostboxesnonce" value="4f4cab2048" />
								</form>
							</div>
						</div>
					</td>
                </tr>
               </tbody></table>';
        $wsmAdminJavaScript.='
            jQuery("#'.WSM_PREFIX.'Country").on(\'change\', function() {
				jQuery(\'#submit\').prop(\'disabled\', true);
              jQuery.ajax({
                   type: "POST",
                   url: wsm_ajaxObject.ajax_url,
                   data: { action: \'timezoneByCountry\', r: Math.random(),code:this.value }
               }).done(function( timezone ) {
				   
				   
				  var mytimezone = "";
				  stringArray = timezone.trim().split(\'/n\'); 
				  for(var i = 0; i < stringArray.length; i++){
					var line = stringArray[i];
					if(line.match(\'/^\s/\') !== -1){
					  mytimezone += line.trim();
					}
				  }
				   
				   jQuery.trim(mytimezone);
				   jQuery(\'#wsmTimezoneString\').val(mytimezone);
                   jQuery(\'#submit\').prop(\'disabled\', false);
               });
            })
            ';
        echo $html.=$this->endWrapper;
    }
    function wsmGetTimeZoneDropDown($tzstring){
        $html='<select id="'.WSM_PREFIX.'TimezoneString" name="'.WSM_PREFIX.'TimezoneString" aria-describedby="timezone-description">'.     wp_timezone_choice( $tzstring, get_user_locale() ).'</select>';
        return $html;
    }
    function wsmGetCountryDropDown($code=''){
        $arrCountries=$this->objDatabase->fnGetAllCountries();
        $html='<select id="'.WSM_PREFIX.'Country" name="'.WSM_PREFIX.'Country" >';
        foreach($arrCountries as $country){
            $selected="";
            if($country['alpha2Code']==$code){
                $selected='selected="selected"';
            }
            $html.='<option value="'.$country['alpha2Code'].'" '.$selected.'>'.__($country['name'],'wp-stats-manager').'</option>';
        }
        return $html.='</select>';
    }
    function wsmGetArchiteDaysDropDown($days=30)
	{
		$html='<select id="'.WSM_PREFIX.'ArchiveDays" name="'.WSM_PREFIX.'ArchiveDays" >';
		if($days==30){
            $html.='<option value="30" selected="selected">'.__('Last 30 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="30">'.__('Last 30 Days','wp-stats-manager').'</option>';
        }
        if($days==60){
            $html.='<option value="60" selected="selected">'.__('Last 60 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="60">'.__('Last 60 Days','wp-stats-manager').'</option>';
        }
		
		if($days==90){
            $html.='<option value="90" selected="selected">'.__('Last 90 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="90">'.__('Last 90 Days','wp-stats-manager').'</option>';
        }
		
		if($days==180){
            $html.='<option value="180" selected="selected">'.__('Last 180 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="180">'.__('Last 180 Days','wp-stats-manager').'</option>';
        }
		
		
		$html.='</select>';
		return $html;
	}
    
    function wsmGetChartDaysDropDown($days=30){
        $html='<select id="'.WSM_PREFIX.'ChartDays" name="'.WSM_PREFIX.'ChartDays" >';
        if($days==15){
            $html.='<option value="15" selected="selected">'.__('Last 15 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="15">'.__('Last 15 Days','wp-stats-manager').'</option>';
        }
        if($days==30 || $days=='' ){
            $html.='<option value="30" selected="selected">'.__('Last 30 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="30">'.__('Last 30 Days','wp-stats-manager').'</option>';
        }
        if($days==45){
            $html.='<option value="45" selected="selected">'.__('Last 45 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="45">'.__('Last 45 Days','wp-stats-manager').'</option>';
        }
        if($days==60){
            $html.='<option value="60" selected="selected">'.__('Last 60 Days','wp-stats-manager').'</option>';
        }else{
            $html.='<option value="60">'.__('Last 60 Days','wp-stats-manager').'</option>';
        }
		

        return $html.='</select>';
    }
    function wsmCreateSubLayout($layout){
        global $wsmAdminPageHooks,$wsmRequestArray,$wp_meta_boxes;        
        switch($layout){
            case 'Summary':
                echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'left', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'right', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-4" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'bottom', null );
                echo '</div>';
            break;
            case 'UsersOnline':
                $tab=isset($wsmRequestArray['subTab'])&&$wsmRequestArray['subTab']!=""?$wsmRequestArray['subTab']:'';
                if($tab!=''){
                    switch($tab){
                        case 'summary':
                            echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'top', null );
                            echo '</div>';
                            echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'left', null );
                            echo '</div>';
                            echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'right', null );
                            echo '</div>';
                        break;
                        case 'recent':
                            echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'top', null );
                            echo '</div>';
                        break;
                        case 'mavis':
                            echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'top', null );
                            echo '</div>';
                        break;
                        case 'popPages':
                            echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'top', null );
                            echo '</div>';
                        break;
                        case 'popReferrer':
                            echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'top', null );
                            echo '</div>';
                        break;
                        case 'geoLocation':
                            echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'top', null );
                            echo '</div>';
                            echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'left', null );
                            echo '</div>';
                            echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                            @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'right', null );
                            echo '</div>';
                        break;
                    }
                }
            break;
            case 'TrafStats':
                echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'left', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'right', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_traffic'], 'bottom', null );
                echo '</div>';
            break;
           case 'RefSites':
                echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'], 'left', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'], 'right', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'], 'bottom', null );
                echo '</div>';  
				break;          
           case 'SearchEngines':
                echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'], 'left', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'], 'right', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'], 'bottom', null );
                echo '</div>';   
				break;          
           case 'SearchKeywords':
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_trafficsrc'], 'bottom', null );
                echo '</div>';   
				break;
           case 'bosl':
                echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_bosl'], 'left', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_bosl'], 'right', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_bosl'], 'bottom', null );
                echo '</div>';            
 			    break;
           case 'GeoLocation':
                echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_visitors'], 'left', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_visitors'], 'right', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_visitors'], 'bottom', null );
                echo '</div>';            
 			    break; 
           case 'byURL':
           case 'byTitle':
                echo '<div id="wsm-postbox-container-2" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_content'], 'left', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-3" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_content'], 'right', null );
                echo '</div>';
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_content'], 'bottom', null );
                echo '</div>';            
 			    break; 
           case 'ipexc':
                echo '<div id="wsm-postbox-container-1" class="postbox-container">';
                @do_meta_boxes( $wsmAdminPageHooks[WSM_PREFIX.'_ipexc'], 'bottom', null );
                echo '</div>';   
				break;
        }
    }
    function wsmShowMainPageLayout($page){
        global $wsmAdminPageHooks,$wsmAdminJavaScript,$wsmRequestArray;
        echo $this->startMetaBoxWrapper;
        echo '<form name="'.WSM_PREFIX.'mainMetboxForm" id="'.WSM_PREFIX.'mainMetboxForm" method="post">';
        wp_nonce_field( 'some-action-nonce' );        
        $subPage=isset($wsmRequestArray['subPage']) && $wsmRequestArray['subPage']!=''?$wsmRequestArray['subPage']:'bosl';
        if($subPage!=''){
            $this->wsmCreateSubLayout($subPage);
        }
        /* Used to save closed meta boxes and their order */
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
        echo '</form>';
        echo $this->endMetaBoxWrapper;        
        $wsmAdminJavaScript.='if(jQuery(".'.$wsmAdminPageHooks[$page].'").length){
                    postboxes.add_postbox_toggles("'.$wsmAdminPageHooks[$page].'");
                }';
    }
    function wsmViewTraffic(){
        global $wsmAdminPageHooks,$wsmAdminJavaScript,$wsmRequestArray;
        echo $this->startWrapper;
        echo $this->fnPrintTitle('Traffic');        
        $this->wsmShowMainPageLayout(WSM_PREFIX.'_traffic');
     /*   echo $html.=$this->startMetaBoxWrapper;
        echo '<form name="'.WSM_PREFIX.'mainMetboxForm" id="'.WSM_PREFIX.'mainMetboxForm" method="post">';
        wp_nonce_field( 'some-action-nonce' );        
        $subPage=isset($wsmRequestArray['subPage']) && $wsmRequestArray['subPage']!=''?$wsmRequestArray['subPage']:'';
        if($subPage!=''){
            $this->wsmCreateLayout($subPage);
        }
        /* Used to save closed meta boxes and their order 
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );

        echo '</form>';
        echo $this->endMetaBoxWrapper;
        echo $this->endWrapper;
        $wsmAdminJavaScript.='if(jQuery(".'.$wsmAdminPageHooks[WSM_PREFIX.'_traffic'].'").length){
                    postboxes.add_postbox_toggles("'.$wsmAdminPageHooks[WSM_PREFIX.'_traffic'].'");
                }';*/
        echo $this->endWrapper;
    }

    function wsmViewTrafficSources(){
        global $wsmAdminPageHooks,$wsmAdminJavaScript,$wsmRequestArray;
        echo $this->startWrapper;
        echo $this->fnPrintTitle('Traffic Sources');
        $this->wsmShowMainPageLayout(WSM_PREFIX.'_trafficsrc');
        echo $this->endWrapper;
    }
    function wsmViewVisitors(){
        echo $this->startWrapper;
        echo $this->fnPrintTitle('Visitors');
        $this->wsmShowMainPageLayout(WSM_PREFIX.'_visitors');
        echo $this->endWrapper;
    }
    function wsmViewContent(){
        echo $this->startWrapper;
        echo $this->fnPrintTitle('Content');
        $this->wsmShowMainPageLayout(WSM_PREFIX.'_content');
        echo $this->endWrapper;
    }
    function wsmViewIPExclusion(){
        echo $this->startWrapper;
        echo $this->fnPrintTitle('I.P. Exclusion');
        $this->wsmShowMainPageLayout(WSM_PREFIX.'_ipexc');
        echo $this->endWrapper;
    }
}
