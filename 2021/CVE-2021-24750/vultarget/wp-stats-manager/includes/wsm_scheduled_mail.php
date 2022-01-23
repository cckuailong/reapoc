<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class wsmScheduledMail{
    private $objDatabase;
    function __construct(){
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_ajax_send_test_mail', array( $this, 'send_test_mail_callback' ) );
		add_action( 'schedule_report_event', array( $this, 'generate_report' ));
        $this->objDatabase=new wsmDatabase();
			//wp_clear_scheduled_hook( 'schedule_report_event' );
    }
    
    function send_test_mail_callback()
    {
	
		if(null !== (sanitize_text_field($_POST['report']) ) && sanitize_text_field($_POST['report']) == 1 )
		{
			$this->generate_report( false );
			echo "1";
			die();
		}	
		else
		{
			echo "0"	;
			exit;
		}
		
	}
	
	function init(){
		if(isset( $_GET['report'] ) && $_GET['report'] == 1 ){
			$this->generate_report( false );
			die();
		}
	    if (! wp_next_scheduled ( 'schedule_report_event' )) {
			$schedule_time = wsmGetCurrentDateByTimeZone('Y-m-d').' 23:59:59';
			$schedule_time = get_gmt_from_date( $schedule_time, 'U' );
			wp_schedule_event($schedule_time, 'daily', 'schedule_report_event');
	    }
	}
	
	function generate_report( $live = true ){
			global $call_by_reports;
			$call_by_reports = true;
			$reportStats = get_option(WSM_PREFIX.'ReportStats');
			$reportScheduleTime = get_option(WSM_PREFIX.'ReportScheduleTime');
			$reportEmail = get_option(WSM_PREFIX.'ReportEmails');
			$report_time = get_option('report_time');
			
			if( !$reportEmail ){
				$reportEmail = get_option('admin_email');
			}
			$arrAtts = array();
			$newReportDate = date('Y-m-d');
			if( $reportScheduleTime ){
				switch($reportScheduleTime){
				case 1:
					$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));	
					$newReportDate = date('Y-m-d', strtotime("+1 day"));
					break;
				case 3:
					if( date('w') == 4 ){
						$arrAtts['from'] = date('Y-m-d', strtotime("-4 day"));
						$arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));	
						$newReportDate = date('Y-m-d', strtotime("+3 day"));
					}
					if( date('w') == 0 ){
						$arrAtts['from'] = date('Y-m-d', strtotime("-3 day"));
						$arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));
						$newReportDate = date('Y-m-d', strtotime("+4 day"));	
					}
					break;
				case 7:
					if( date('w') == 0 )
					{
						$arrAtts['from'] = date('Y-m-d', strtotime("-7 day"));
						$arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));	
						$newReportDate = date('Y-m-d', strtotime("+7 day"));
					}
				break;
				case 30:
					if( date('j') == 1 )
					{
						$month_ini = new DateTime("first day of last month");
						$month_end = new DateTime("last day of last month");
						
						$arrAtts['from'] = $month_ini->format('Y-m-d');
						$arrAtts['to'] = $month_end->format('Y-m-d');
						$newReportDate = date('Y-m-d', strtotime("first day of next month"));
					}
				break;
				}
			}
			if( $live && strtotime( $report_time ) > strtotime( date('Y-m-d') ) ){
				return;
			}
			if( count( $arrAtts) ){
				if( is_array($reportStats) && count( $reportStats ) ){
					ob_start();

					echo '
					<html>
					<head>	
					   <meta charset="utf-8">
					<style>.row_table th,h2{font-weight:700;font-family:verdana;color:#002199}img{display:none}table{border:1px solid #ccc;border-spacing:0;margin-bottom:20px;width:100%}.row_table th{background-color:#e6d1ff;font-size:12px}tbody{border:0}table td,table th{border-top:1px solid #fff;padding:5px}table tr,table tr:nth-child(2n+1){background-color:#e8edff;border-color:#fff}table tr:first-child td,table tr:first-child th{border-top:medium none}h2{background:#a3b7ff;border-top:4px solid #8ca5ff;font-size:14px;margin:0;padding:5px 10px;text-align:center}table th{font-weight:400;text-align:left}table td{text-align:right}.row_table tr td:first-child{text-align:center}#referral_website_stats_panel td:first-child,#referral_website_stats_panel tr td:first-child,#search_engine_stats_panel tr td:first-child,#search_engine_summary_panel tr td:first-child,.row_table tr td:nth-child(2){text-align:left}#referral_website_stats_panel tr td:nth-child(2),#search_engine_stats_panel tr td:nth-child(2){text-align:right}.main_panel{font-family:verdana;margin:auto;width:600px}.main_panel table{background-attachment:scroll;background-clip:border-box;background-image:none;background-origin:padding-box;background-position:0 0;background-repeat:repeat;background-size:auto auto;border:0;color:#002199;font-family:verdana;font-size:12px;font-weight:700;width:100%}.main_panel>table tr,.report_panel .stat_panel tr{background:0 0}.main_panel>table tr td:first-child{text-align:left}.report_panel td,.report_panel th{font-weight:400}.row_table tr td.integer.first_column{text-align:center;width:20px}.row_table tr td.string{text-align:left}.row_table tr td.integer{text-align:center}.report_panel>table{border:1px solid #ccc}.report_panel .panel_data{-moz-border-bottom-colors:none;-moz-border-left-colors:none;-moz-border-right-colors:none;-moz-border-top-colors:none;background-color:#e6d1ff;border-color:#ccc #ccc #ccc #002199;border-image:none;border-style:solid;border-width:1px 1px 1px 4px;color:#002199;font-size:14px;margin-right:10px;padding:10px;text-align:left}.stat_panel td{padding:0}.report_panel .panel_data .title{font-size:12px}.report_panel td:nth-child(2) .panel_data,.report_panel td:nth-child(3) .panel_data{border-left:4px solid #002199}.wsmMetaboxContainer .wsmIcon{display:none;width:16px;height:16px;background-image:url("'. WSM_URL.'images/icons.png")}.mozillafirefox{background-position:-112px 0}.googlechrome{background-position:-80px 0}.opera{background-position:-224px 0}.internetexplorer,.microsoftinternetexplorer{background-position:-144px 0}.safari{background-position:-256px 0}.desktop{background-position:-192px 0}.mobile{background-position:-336px 0}.windows,.windows98{background-position:-320px 0}.linux{background-position:-176px 0}.android{background-position:0 0}.google{background-position:-128px 0}.bing{background-position:-64px 0}.yahoo{background-position:-304px 0}.aol{background-position:-16px 0}.askcom{background-position:-48px 0}.microsoftedge{background-position:-368px 0}.ios,.mac{background-position:-32px 0}.flag{background-image:url('.WSM_URL.'images/flags.png);display:none;height:11px;width:16px}.flag-ma{background-position:-64px -88px}.flag-vi{background-position:-112px -154px}.flag-bo{background-position:-160px -11px}.flag-gr{background-position:-48px -55px}.flag-ne{background-position:-176px -99px}.flag-gl{background-position:-240px -44px}.flag-um{background-position:-256px -143px}.flag-ae{background-position:-16px 0}.flag-ng{background-position:-208px -99px}.flag-nl{background-position:-240px -99px}.flag-gp{background-position:-16px -55px}.flag-fk{background-position:-32px -44px}.flag-dj{background-position:-48px -33px}.flag-gt{background-position:-80px -55px}.flag-pw{background-position:0 -121px}.flag-np{background-position:0 -110px}.flag-cf{background-position:-64px -22px}.flag-pn{background-position:-208px -110px}.flag-an{background-position:-112px 0}.flag-dm{background-position:-80px -33px}.flag-cw{background-position:-256px -22px}.flag-pf{background-position:-112px -110px}.flag-mo{background-position:-224px -88px}.flag-az{background-position:-240px 0}.flag-tj{background-position:-48px -143px}.flag-nz{background-position:-48px -110px}.flag-ec{background-position:-128px -33px}.flag-al{background-position:-80px 0}.flag-jo{background-position:-208px -66px}.flag-dk{background-position:-64px -33px}.flag-aw{background-position:-208px 0}.flag-es{background-position:-224px -33px}.flag-eg{background-position:-160px -33px}.flag-sz{background-position:-208px -132px}.flag-do{background-position:-96px -33px}.flag-fj{background-position:-16px -44px}.flag-km{background-position:-32px -77px}.flag-yt{background-position:-240px -154px}.flag-bb{background-position:0 -11px}.flag-lu{background-position:-16px -88px}.flag-gn{background-position:0 -55px}.flag-kw{background-position:-112px -77px}.flag-kr{background-position:-80px -77px}.flag-sm{background-position:-48px -132px}.flag-ml{background-position:-176px -88px}.flag-ga{background-position:-96px -44px}.flag-ve{background-position:-80px -154px}.flag-ru{background-position:-96px -121px}.flag-la{background-position:-160px -77px}.flag-jm{background-position:-192px -66px}.flag-lc{background-position:-192px -77px}.flag-tk{background-position:-64px -143px}.flag-ws{background-position:-192px -154px}.flag-sh{background-position:-240px -121px}.flag-am{background-position:-96px 0}.flag-sl{background-position:-32px -132px}.flag-me{background-position:-112px -88px}.flag-ir{background-position:-112px -66px}.flag-mg{background-position:-128px -88px}.flag-sr{background-position:-112px -132px}.flag-ms{background-position:-16px -99px}.flag-rs{background-position:-80px -121px}.flag-gi{background-position:-224px -44px}.flag-bh{background-position:-80px -11px}.flag-sj{background-position:0 -132px}.flag-il{background-position:-16px -66px}.flag-hr{background-position:-192px -55px}.flag-bg{background-position:-64px -11px}.flag-mv{background-position:-64px -99px}.flag-bm{background-position:-128px -11px}.flag-bi{background-position:-96px -11px}.flag-om{background-position:-64px -110px}.flag-ky{background-position:-128px -77px}.flag-py{background-position:-16px -121px}.flag-kp{background-position:-64px -77px}.flag-gh{background-position:-208px -44px}.flag-lv{background-position:-32px -88px}.flag-im{background-position:-48px -66px}.flag-dz{background-position:-112px -33px}.flag-br{background-position:-176px -11px}.flag-sc{background-position:-160px -121px}.flag-zanzibar{background-position:0 -165px}.flag-tt{background-position:-160px -143px}.flag-it{background-position:-144px -66px}.flag-cr{background-position:-208px -22px}.flag-ni{background-position:-224px -99px}.flag-at{background-position:-176px 0}.flag-tw{background-position:-192px -143px}.flag-uz{background-position:-32px -154px}.flag-gg{background-position:-192px -44px}.flag-cy{background-position:0 -33px}.flag-de{background-position:-32px -33px}.flag-tl{background-position:-80px -143px}.flag-th{background-position:-16px -143px}.flag-eu{background-position:-256px -33px}.flag-pm{background-position:-192px -110px}.flag-id{background-position:-256px -55px}.flag-hm{background-position:-160px -55px}.flag-nc{background-position:-160px -99px}.flag-sx{background-position:-176px -132px}.flag-lt{background-position:0 -88px}.flag-ca{background-position:-16px -22px}.flag-in{background-position:-64px -66px}.flag-tr{background-position:-144px -143px}.flag-ss{background-position:-128px -132px}.flag-cv{background-position:-240px -22px}.flag-be{background-position:-32px -11px}.flag-ad{background-position:0 0}.flag-cz{background-position:-16px -33px}.flag-mk{background-position:-160px -88px}.flag-vn{background-position:-128px -154px}.flag-sb{background-position:-144px -121px}.flag-tf{background-position:-256px -132px}.flag-za{background-position:-256px -154px}.flag-li{background-position:-208px -77px}.flag-bz{background-position:0 -22px}.flag-sn{background-position:-64px -132px}.flag-tz{background-position:-208px -143px}.flag-bf{background-position:-48px -11px}.flag-nr{background-position:-16px -110px}.flag-pt{background-position:-256px -110px}.flag-se{background-position:-208px -121px}.flag-pk{background-position:-160px -110px}.flag-gu{background-position:-96px -55px}.flag-tn{background-position:-112px -143px}.flag-gf{background-position:-160px -44px}.flag-fm{background-position:-48px -44px}.flag-no{background-position:-256px -99px}.flag-ps{background-position:-240px -110px}.flag-ck{background-position:-128px -22px}.flag-tv{background-position:-176px -143px}.flag-gw{background-position:-112px -55px}.flag-wales{background-position:-160px -154px}.flag-mh{background-position:-144px -88px}.flag-nu{background-position:-32px -110px}.flag-sg{background-position:-224px -121px}.flag-tg{background-position:0 -143px}.flag-catalonia{background-position:-32px -22px}.flag-vc{background-position:-64px -154px}.flag-ee{background-position:-144px -33px}.flag-eh{background-position:-176px -33px}.flag-tm{background-position:-96px -143px}.flag-ar{background-position:-144px 0}.flag-td{background-position:-240px -132px}.flag-bv{background-position:-224px -11px}.flag-na{background-position:-144px -99px}.flag-england{background-position:-192px -33px}.flag-et{background-position:-240px -33px}.flag-us{background-position:0 -154px}.flag-md{background-position:-96px -88px}.flag-rw{background-position:-112px -121px}.flag-kh{background-position:0 -77px}.flag-sk{background-position:-16px -132px}.flag-cu{background-position:-224px -22px}.flag-ge{background-position:-144px -44px}.flag-gy{background-position:-128px -55px}.flag-re{background-position:-48px -121px}.flag-ua{background-position:-224px -143px}.flag-ht{background-position:-208px -55px}.flag-ch{background-position:-96px -22px}.flag-lr{background-position:-240px -77px}.flag-xk{background-position:-208px -154px}.flag-pr{background-position:-224px -110px}.flag-gb{background-position:-112px -44px}.flag-bt{background-position:-208px -11px}.flag-gs{background-position:-64px -55px}.flag-ci{background-position:-112px -22px}.flag-sd{background-position:-192px -121px}.flag-by{background-position:-256px -11px}.flag-ye{background-position:-224px -154px}.flag-nf{background-position:-192px -99px}.flag-zm{background-position:-16px -165px}.flag-gm{background-position:-256px -44px}.flag-ly{background-position:-48px -88px}.flag-cl{background-position:-144px -22px}.flag-vu{background-position:-144px -154px}.flag-pl{background-position:-176px -110px}.flag-er{background-position:-208px -33px}.flag-wf{background-position:-176px -154px}.flag-au{background-position:-192px 0}.flag-kg{background-position:-256px -66px}.flag-co{background-position:-192px -22px}.flag-mp{background-position:-240px -88px}.flag-lb{background-position:-176px -77px}.flag-my{background-position:-112px -99px}.flag-jp{background-position:-224px -66px}.flag-sa{background-position:-128px -121px}.flag-ba{background-position:-256px 0}.flag-so{background-position:-80px -132px}.flag-fi{background-position:0 -44px}.flag-kurdistan{background-position:-96px -77px}.flag-ag{background-position:-48px 0}.flag-mu{background-position:-48px -99px}.flag-mz{background-position:-128px -99px}.flag-mc{background-position:-80px -88px}.flag-ug{background-position:-240px -143px}.flag-mn{background-position:-208px -88px}.flag-pa{background-position:-80px -110px}.flag-pe{background-position:-96px -110px}.flag-va{background-position:-48px -154px}.flag-to{background-position:-128px -143px}.flag-mq{background-position:-256px -88px}.flag-vg{background-position:-96px -154px}.flag-ie{background-position:0 -66px}.flag-je{background-position:-176px -66px}.flag-mx{background-position:-96px -99px}.flag-hu{background-position:-224px -55px}.flag-bj{background-position:-112px -11px}.flag-gq{background-position:-32px -55px}.flag-cg{background-position:-80px -22px}.flag-cm{background-position:-160px -22px}.flag-fo{background-position:-64px -44px}.flag-qa{background-position:-32px -121px}.flag-sy{background-position:-192px -132px}.flag-ic{background-position:-240px -55px}.flag-mw{background-position:-80px -99px}.flag-ro{background-position:-64px -121px}.flag-gd{background-position:-128px -44px}.flag-ph{background-position:-144px -110px}.flag-fr{background-position:-80px -44px}.flag-si{background-position:-256px -121px}.flag-mr{background-position:0 -99px}.flag-kz{background-position:-144px -77px}.flag-hk{background-position:-144px -55px}.flag-mm{background-position:-192px -88px}.flag-zw{background-position:-32px -165px}.flag-pg{background-position:-128px -110px}.flag-hn{background-position:-176px -55px}.flag-ai{background-position:-64px 0}.flag-somaliland{background-position:-96px -132px}.flag-sv{background-position:-160px -132px}.flag-bd{background-position:-16px -11px}.flag-as{background-position:-160px 0}.flag-bn{background-position:-144px -11px}.flag-ki{background-position:-16px -77px}.flag-tibet{background-position:-32px -143px}.flag-cd{background-position:-48px -22px}.flag-io{background-position:-80px -66px}.flag-ax{background-position:-224px 0}.flag-is{background-position:-128px -66px}.flag-st{background-position:-144px -132px}.flag-bs{background-position:-192px -11px}.flag-iq{background-position:-96px -66px}.flag-ls{background-position:-256px -77px}.flag-mt{background-position:-32px -99px}.flag-ke{background-position:-240px -66px}.flag-ao{background-position:-128px 0}.flag-uy{background-position:-16px -154px}.flag-lk{background-position:-224px -77px}.flag-scotland{background-position:-176px -121px}.flag-af{background-position:-32px 0}.flag-cn{background-position:-176px -22px}.flag-bw{background-position:-240px -11px}.flag-tc{background-position:-224px -132px}.flag-kn{background-position:-48px -77px}</style>
						</head>
						<body><div class="main_panel wsmMetaboxContainer">';
					echo sprintf('<table><tr><td><center>%s, %s %s</center></td></tr></table>', 'Report', date('l M d, Y'), date('H:m A') );

					echo '<div class="report_panel">';
					$this->general_stats( $arrAtts );
					echo '</div>';
					
					foreach( $reportStats as $reportFunction ){
						echo '<div class="report_panel">';
						$this->$reportFunction( $arrAtts );
						echo '</div>';
					}
					echo '<p><small>'._('Reports generated by WordPress Stats Plugin').'</small></p>';
					echo '</div></body></html>';


					$content = ob_get_contents();
					ob_clean();
					
					$headers = array('Content-Type: text/html; charset=UTF-8');
					wp_mail($reportEmail,'Website Statistics Report - '.date('D, d M Y h:m:s'), $content, $headers);
					//wp_mail('mitesh@prismitsystems.com','Report', $content, $headers);
					//echo $content;
					update_option( 'report_time', $newReportDate );
				}
			}
	}
	function general_stats_new( $arrAtts ){
		echo  do_shortcode("[".WSM_PREFIX."_showGenStats]");
		
	}
	function general_stats( $arrAtts ){
        $totalPageViews=$this->objDatabase->fnGetTotalPageViewCount();
        $totalVisitors=$this->objDatabase->fnGetTotalVisitorsCount();
        $pageViews=0;
        if($totalPageViews>0 && $totalVisitors>0){
            $pageViews=($totalPageViews/$totalVisitors);
        }
        $totalPageViews=number_format_i18n($totalPageViews,0);
        $totalVisitors=number_format_i18n($totalVisitors,0);
        $pageViews=number_format_i18n($pageViews,2);
		
		$data = array(
			__('Total Page Views','wp-stats-manager') => $totalPageViews,
			__('Total Visitors','wp-stats-manager')	=>	$totalVisitors,
			__('Page Views Per Visit','wp-stats-manager') => $pageViews
		);
		
		echo $this->generate_section( $data, __('General Stats','wp-stats-manager') );		
	}
	function daily_stats( $arrAtts ){
		
		if( !is_array( $arrAtts ) ){
			$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));	
		}
        
        $pageViews=$this->objDatabase->fnGetTotalPageViewCount('Normal',$arrAtts);
        $visitors=$this->objDatabase->fnGetTotalVisitorsCount('Normal',$arrAtts);
        $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount('Normal',$arrAtts);
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
		
		$data = array(
			__('Total Page Views','wp-stats-manager') => $strPageViews,
			__('Total Visitors','wp-stats-manager')	=>	$strVisitors,
			__('First Time Visitors','wp-stats-manager')	=>	$strFirstTimeVisitors,
			__('Page Views Per Visit','wp-stats-manager')	=>	$strPvpv,
			__('New Visitors Ratio','wp-stats-manager') => $strNvr
		);
		
		echo $this->generate_table( $data, __('Daily Stats','wp-stats-manager') );		
	}
	
	function referral_website_stats( $arrAtts ){

		if( !is_array( $arrAtts ) ){
			$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));	
		}
		$atts['condition'] = 'Normal';
        
		
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
		
		$column = array(
			__('Summary','wp-stats-manager'), __('Stats Summary','wp-stats-manager'), __('Referrer Summary','wp-stats-manager') ,'(%)'
		);
		$rows = array(
			array( __('Total Page Views','wp-stats-manager'), $pageViews, $RpageViews, $PpageViews ),
			array( __('Total Visitors','wp-stats-manager'), $visitors, $Rvisitors, $Pvisitors ),
			array( __('Total New Visitors','wp-stats-manager'), $firstTimeVisitors, $RfirstTimeVisitors, $PfirstTimeVisitors ),
			array( __('Page Views Per Visit','wp-stats-manager'), $pvpv, $Rpvpv, $Ppvpv )
		);
		
		echo $this->generate_row_table( $column, $rows, __('Referral Website Stats','wp-stats-manager') );		
	}
	
	function search_engine_stats( $arrAtts ){
		$graph = false;
		global $call_by_reports;
		if( !is_array( $arrAtts ) ){
			$graph = true;
			if( $call_by_reports ){
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));		
			}else{
				$arrAtts['from'] = $arrAtts['to'] = wsmGetCurrentDateByTimeZone( 'Y-m-d' );			
			}
		}
		$atts['condition'] = 'Normal';
        
		$arrAtts['searchengine']=1; 
		
        $pageViews=$this->objDatabase->fnGetTotalPageViewCount($atts['condition'],$arrAtts);
        $visitors=$this->objDatabase->fnGetTotalVisitorsCount($atts['condition'],$arrAtts);
        $firstTimeVisitors=$this->objDatabase->fnGetFirstTimeVisitorCount($atts['condition'],$arrAtts);
        if($pageViews>0 && $visitors>0){
            $pvpv=($pageViews/$visitors);
        } 
        $pageViews=number_format_i18n($pageViews,0);
        $visitors=number_format_i18n($visitors,0);;
        $firstTimeVisitors=number_format_i18n($firstTimeVisitors,0);
        $pvpv=number_format_i18n($pvpv,2);         
		
		$column = array(
			__('Summary','wp-stats-manager'), __('Stats Summary','wp-stats-manager')
		);
		$rows = array(
			array( __('Total Page Views','wp-stats-manager'), $pageViews ),
			array( __('Total Visitors','wp-stats-manager'), $visitors ),
			array( __('Total New Visitors','wp-stats-manager'), $firstTimeVisitors ),
			array( __('Page Views Per Visit','wp-stats-manager'), $pvpv  )
		);
		
		$html = $this->generate_row_table( $column, $rows, __('Search Engine Stats','wp-stats-manager') );
		
		$arrSearchEngineStatList = $this->objDatabase->fnGetTotalUserSeachEngineWise( $atts['condition'] ,$arrAtts);
		$totalUsers = array_sum( $arrSearchEngineStatList );
		
		$column = array(
			'', __('Visitors','wp-stats-manager')
		);
		$rows = array();
		
		foreach( $arrSearchEngineStatList as $key => $arrSearchEngineStat ){
			if( $arrSearchEngineStat ){
				$number = $arrSearchEngineStat * 100 / $totalUsers;
				$user_per = number_format($number,2);
				$graph_div = '';
				if( $graph ){
					$graph_div = '<div class="bar_graph" style="width:'.($user_per*3).'px"></div>';	
				}

				$name = sprintf( '<img class="wsmIcon %s" src="%s/images/ICO_1px.gif" alt="%s" title="%s"> %s', str_replace(' ','', strtolower($key)), WSM_URL,  $key, $key, $key );
				$rows[] = array( '<span>'.$name.'</span> '.$graph_div.$user_per.'%', number_format($arrSearchEngineStat) );
			}
		}
		$html .= $this->generate_row_table( $column, $rows, __('Search Engine Summary','wp-stats-manager') );		
		echo $html;
	}
	
	function top_search_engine_stats( $arrAtts ){

		global $call_by_reports;
		if( !is_array( $arrAtts ) ){
			if( $call_by_reports ){
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));		
			}else{
				$arrAtts['from'] = $arrAtts['to'] = wsmGetCurrentDateByTimeZone( 'Y-m-d' );			
			}
		}
		$atts['condition'] = 'Normal';
        
		$arrAtts['searchengine']=1; 
		
        $arrReferrerList=$this->objDatabase->fnGetReferralList($atts['condition'],$arrAtts); 
		
		$column = $rows = array();
        if(count($arrReferrerList['data'])>0){
			$column = array( __('Rank','wp-stats-manager'), __('Referrer url','wp-stats-manager'), __('References','wp-stats-manager') );
			$count = 1;
			
			foreach($arrReferrerList['data'] as $referrer=>$referrers){    
				$name = sprintf( '<img class="wsmIcon %s" src="%s/images/ICO_1px.gif" alt="%s" title="%s"> %s', str_replace(' ','', strtolower($referrers['searchEngine'] )), WSM_URL,  $referrers['searchEngine'] ,  $referrers['searchEngine'] ,  $referrers['refUrl'] );
				$rows[] = array( $count++, $name, number_format($referrers['total'] ) );
			}
		}
		echo $this->generate_row_table( $column, $rows, __('Top Search Engine Summary','wp-stats-manager') );		
	}
	
	function os_wise_visitor_stats( $arrAtts ){
		global $call_by_reports;
		if( !is_array( $arrAtts ) ){
			if( $call_by_reports ){
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));		
			}else{
				$arrAtts['from'] = $arrAtts['to'] = wsmGetCurrentDateByTimeZone( 'Y-m-d' );			
			}
		}
		echo $this->get_visitor_stats( $arrAtts, __('OS','wp-stats-manager'), __('Operating System','wp-stats-manager') );
	}
	
	function browser_wise_visitor_stats( $arrAtts ){
		global $call_by_reports;
		if( !is_array( $arrAtts ) ){
			if( $call_by_reports ){
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));		
			}else{
				$arrAtts['from'] = $arrAtts['to'] = wsmGetCurrentDateByTimeZone( 'Y-m-d' );			
			}
		}
		echo $this->get_visitor_stats( $arrAtts, __('Browser','wp-stats-manager'), __('Browser','wp-stats-manager') );
	}
	
	function screen_wise_visitor_stats( $arrAtts ){
		global $call_by_reports;
		if( !is_array( $arrAtts ) ){
			if( $call_by_reports ){
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));		
			}else{
				$arrAtts['from'] = $arrAtts['to'] = wsmGetCurrentDateByTimeZone( 'Y-m-d' );			
			}
		}
		echo $this->get_visitor_stats( $arrAtts, __('Screen Resolution','wp-stats-manager'), __('Screen Resolution','wp-stats-manager') );
	}
	
	function country_wise_visitor_stats( $arrAtts ){
		global $call_by_reports;
		if( !is_array( $arrAtts ) ){
			if( $call_by_reports ){
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));		
			}else{
				$arrAtts['from'] = $arrAtts['to'] = wsmGetCurrentDateByTimeZone( 'Y-m-d' );			
			}
		}
		echo $this->get_location_stats( $arrAtts, __('Country Stats','wp-stats-manager') );	
	}
	
	function city_wise_visitor_stats( $arrAtts ){
		global $call_by_reports;
		if( !is_array( $arrAtts ) ){
			if( $call_by_reports ){
				$arrAtts['from'] = $arrAtts['to'] = date('Y-m-d', strtotime("-1 day"));		
			}else{
				$arrAtts['from'] = $arrAtts['to'] = wsmGetCurrentDateByTimeZone( 'Y-m-d' );			
			}
		}
		$arrAtts['location'] = 1;
		echo $this->get_location_stats( $arrAtts, __('City Stats','wp-stats-manager') );
	}
	
	function get_location_stats( $arrAtts, $title ){
		
		$condition = 'Normal';
        
		$totalGeoLocationDetails = $this->objDatabase->getGeoLocationInfo( $condition, $arrAtts );
		$column = $rows = array();
		if( $totalGeoLocationDetails ){
			$count = 1;
			if( isset( $arrAtts['location'] )){
			$column = array( __('Rank','wp-stats-manager'), __('City','wp-stats-manager'), __('New visitors','wp-stats-manager'), __('Visitors','wp-stats-manager'), __('Page viewed','wp-stats-manager'), __('Ppv','wp-stats-manager') );
		}else{
			$column = array( __('Rank','wp-stats-manager'), __('Country','wp-stats-manager'), __('New visitors','wp-stats-manager'), __('Visitors','wp-stats-manager'), __('Page viewed','wp-stats-manager'), __('Ppv','wp-stats-manager') );
		}
			foreach( $totalGeoLocationDetails as $key => $locationDetail ){
				if( $count < 11 ){
					$name = sprintf( '<img class="flag flag-%s" src="%s/images/ICO_1px.gif" alt="%s" title="%s"> %s', strtolower($locationDetail['alpha2Code']), WSM_URL,  $locationDetail['name'] ,  $locationDetail['name'] ,  $locationDetail['name'] );
					$ppv = $locationDetail['total_page_views'] / $locationDetail['total_visitors'];
					$rows[] = array( $count++, $name, number_format($locationDetail['total_unique_visitors']), number_format($locationDetail['total_visitors']), number_format($locationDetail['total_page_views']), number_format($ppv, 2) );
				}
			}
		}
		return $this->generate_row_table( $column, $rows, $title );		
	}
	
	function get_visitor_stats( $arrAtts, $keyName, $title ){
		
		$condition = 'Normal';
        
		$rows = array();
		$arrVisitorsInfo = $this->objDatabase->getVisitorsInfo( $condition, $arrAtts );
		
		if( isset( $arrVisitorsInfo ) && is_array($arrVisitorsInfo) && count($arrVisitorsInfo) && isset( $arrVisitorsInfo[ $keyName ] ) ){
			
			$column = array( __('Rank','wp-stats-manager'), $title, __('References','wp-stats-manager') );
			$count = 1;
			foreach( $arrVisitorsInfo[ $keyName ] as $os_detail ){
				$name = $os_detail['name'];
				if( !isset($arrAtts['icon']) ){
					if( isset( $os_detail['deviceType'] ) && $os_detail['name'] != '-' ){
						$name = sprintf( '<img class="wsmIcon %s" src="%s/images/ICO_1px.gif" alt="%s" title="%s"> %s', str_replace(' ','', strtolower($os_detail['deviceType'] )), WSM_URL,  $os_detail['name'] ,  $os_detail['name'] ,  $os_detail['name'] );
					}
					if( $keyName == __('OS','wp-stats-manager') && $os_detail['name'] != '-' ){
						$name = sprintf( '<img class="wsmIcon %s" src="%s/images/ICO_1px.gif" alt="%s" title="%s"> %s', str_replace(' ','', strtolower($os_detail['name'] )), WSM_URL,  $os_detail['name'] ,  $os_detail['name'] ,  $os_detail['name'] );
					}
					if( $keyName == __('Browser','wp-stats-manager') && $os_detail['name'] != '-' ){
						$name = sprintf( '<img class="wsmIcon %s" src="%s/images/ICO_1px.gif" alt="%s" title="%s"> %s', str_replace(' ','', strtolower($os_detail['name'] )), WSM_URL,  $os_detail['name'] ,  $os_detail['name'] ,  $os_detail['name'] );
					}
				}
				$rows[] = array( $count++, $name, number_format($os_detail['total']) );;
				if( $count == 6 ){
					break;
				}
			}	
		}
		return $this->generate_row_table( $column, $rows, $title.' '.__('Stats','wp-stats-manager') );		
	}
	
	function generate_row_table( $column, $rows, $title ){
		$html = '';
		if( isset( $rows) && is_array( $rows ) ){
			$html .= sprintf( '<h2>%s</h2>', $title );	
			$html .= '<div class="wsmTableContainer"><table id="'. str_replace(' ','_', strtolower( $title ) ) .'_panel" class="row_table '. str_replace(' ','_', strtolower( $title ) ) .'_panel">';
			$html .= '<tr><th>'.implode( '</th><th>', $column ).'</th></tr>';
			foreach( $rows as $value ){
				//$html .= '<tr><td>'.implode( '</td><td>', $value ).'</td></tr>';
				$html .= '<tr>';
				$count = 1;
				foreach( $value as $td ){
					$class = 'string';
					$alogn = 'left';
					$temp = str_replace(',','',$td);
					if( is_numeric( $temp ) ){
						$class = 'integer';
						$alogn = 'right';
					}
					if( is_numeric( $temp ) && $count == 1 ){
						$class = 'integer first_column';
						$alogn = 'center';
					}
					$count++;
					$html .= '<td class="'.$class.'">'.$td.'</td>';
				}
				$html .= '</tr>';
			}
			$html .= '</table></div>';
		}
		return $html;
	}
	
	function generate_table($data, $title){
		$html = '';
		if( isset( $data) && is_array( $data ) ){
			$html .= sprintf( '<h2>%s</h2>', $title );	
			$html .= '<table>';
			foreach( $data as $key => $value ){
				$html .= sprintf('<tr><th>%s</th><td>%s</td></tr>', $key, $value );
			}
			$html .= '</table>';
		}
		return $html;
	}
	
	function generate_section( $data, $title ){
		$html = '';
		$html .= '<div class="stat_panel"><table><tr>';
		foreach( $data as $key => $value ){
			$html .= sprintf('<td><div class="panel_data"><div class="title">%s</div>%s</div></td>', $key, $value );
		}
		$html .= '</tr></table></div>';	
		return $html;	
	}
	
	function traffic_by_title_stats( $arrAtts ){
		$html = do_shortcode("[".WSM_PREFIX."_showContentByURLStats content='byTitle' limit=10]");
		echo $html;
	}
	function recent_visit_pages(){
		$html = do_shortcode("[".WSM_PREFIX."_showRecentVisitedPages]");
		echo $html;
	}
	function recent_active_visitors(){
		$html = do_shortcode("[".WSM_PREFIX."_showMostActiveVisitorsGeo]");
		echo $html;
	}
}

new wsmScheduledMail();	
?>
