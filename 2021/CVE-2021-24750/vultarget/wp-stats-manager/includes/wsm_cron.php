<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class wsmCron{
	
	private $tablePrefix;
	
	function __construct(){
		global $wpdb;
		$this->tablePrefix=$wpdb->base_prefix.WSM_PREFIX;
		
		add_action( 'init', array( $this, 'init' ) );	
		//add_action( 'schedule_report', array( $this, 'record_stats' ) );	
		add_action( 'schedule_daily_report', array( $this, 'record_stats' ) );	
		//add_action( 'schedule_hour', array( $this, 'check_hours' ) );	
	}
	
	function init(){
		
		if( isset( $_GET['record_stats'] ) ){
			$this->record_stats();	
			die();
		}
		if( isset( $_GET['clear_schedule'] ) ){
			wp_clear_scheduled_hook('schedule_hour');	
			wp_clear_scheduled_hook('schedule_daily_report');
			wp_clear_scheduled_hook('schedule_report_event');
		}
		if( isset( $_GET['check_schedule'] ) ){
			echo date('Y-m-d h:i:s').'<br />';
			echo $schedule_time = wsmGetCurrentDateByTimeZone('Y-m-d').' 23:59:59';
			echo '<br />'.$schedule_time_now = wsmGetCurrentDateByTimeZone('Y-m-d').' 10:43:00';
			die();
		}
	    if (! wp_next_scheduled ( 'schedule_report' )) {
			//wp_schedule_event(time(), 'hourly', 'schedule_report');
	    }
		$schedule_time = wsmGetCurrentDateByTimeZone('Y-m-d').' 23:59:59';
		$schedule_time_now = wsmGetCurrentDateByTimeZone('Y-m-d').' 10:43:00';
		
	    //if (! wp_next_scheduled ( 'schedule_hour' )) {
		//	wp_schedule_event(strtotime($schedule_time_now), 'hourly', 'schedule_hour');
		//}
	    if (! wp_next_scheduled ( 'schedule_daily_report' )) {
			$schedule_time = get_gmt_from_date( $schedule_time, 'U' );
			wp_schedule_event($schedule_time, 'daily', 'schedule_daily_report');
	    }
	}
	
	function check_hours(){

		$message = 'Date: '. wsmGetCurrentDateByTimeZone('Y-m-d g:i:s');
		$message .= ' Hour: '.wsmGetCurrentDateByTimeZone('g');
		mail('mitesh@prismitsystems.com', 'Hourly Cron', $message);
	}
	
	function record_stats(){
		//$date = date('Y-m-d', strtotime( '-6 days' ));
		$day = 1;
		if( isset( $_GET['record_stats'] ) ){
			$day = $_GET['record_stats'];
		}else{
			//return;
			
		}
		$date = date('Y-m-d', strtotime( '-'.$day.' days' ));
		//mail('mitesh@prismitsolutions.com','Daily Cron Job', 'Execution Time: '. $date );
		update_option('Schedule_time', $date );
		if( $this->check_report_exist( $date, 'search_engine' ) ){
			return;
		}
		$ArchiveDays = get_option(WSM_PREFIX.'ArchiveDays');
		if( $ArchiveDays ){
			$archiveDate = date('Y-m-d', strtotime( '-'.( $ArchiveDays + $day ).' days' ));
			$this->delete_archive_data( $archiveDate );
		}
		$this->record_normal_stats( $date );
		$this->record_hour_stats( $date );
		$this->record_search_engines( $date );
		$this->record_browser( $date );
		$this->record_screen( $date );
		$this->record_country( $date );
		$this->record_city( $date );
		$this->record_os( $date );
		$this->record_url( $date );
		
		$timestamp = strtotime($date);
		$currentDate = date( 'j', $timestamp );
		$currentMonth = date( 'n', $timestamp );
		if( $currentDate == 1 )
		{
			$table = '_monthwise_report';
			$ref_table = $this->tablePrefix.'_datewise_report';
			$date_formate = '%Y-%m-00';
			
			$datestring= $date.' first day of last month';
			$dt=date_create($datestring);
			$first_day = $dt->format('Y-m-d');
		
			$datestring= $date.' last day of last month';
			$dt=date_create($datestring);
			$last_day = $dt->format('Y-m-d');
		
			$this->record_month_year( $date, $table, $ref_table, $first_day, $last_day, $date_formate );
		}
		if( $currentDate == 1 && $currentMonth == 1 )
		{
			
			$table = '_yearwise_report';
			$ref_table = $this->tablePrefix.'_monthwise_report';
			$date_formate = '%Y-00-00';
			
			$datestring='first day of last year';
			$dt=date_create($datestring);
			$first_day = $dt->format('Y-00-00');
		
			$datestring='last day of last year';
			$dt=date_create($datestring);
			$last_day = $dt->format('Y-00-00');
		
			$this->record_month_year( $date, $table, $ref_table, $first_day, $last_day, $date_formate );
		}
		
		if( isset( $_GET['record_stats'] ) ){
			//die();
			sleep(3);
			if( $day ){
			$day--;
			
			?>
			<script>
				window.setTimeout(function(){
					window.location.href = '<?php echo site_url(); ?>?record_stats=<?php echo $day; ?>';
				}, 3000);
			</script>
			<?php
			
			}
			//echo 'http://www.freedownloadprogs.com/?record_stats='. $day; 
			//die();
			//header('Location: http://www.freedownloadprogs.com/?record_stats='. $day++ );
			//die();
		}
	}
	
	function delete_archive_data( $date ){
		global $wpdb;
		$wpdb->query("DELETE FROM `".$this->tablePrefix."_dailyHourlyReport` WHERE `reportDate` <= '$date'");
		$wpdb->query("DELETE FROM `".$this->tablePrefix."_logUniqueVisit` WHERE `visitLastActionTime` <= '$date'");
		$wpdb->query("DELETE FROM `".$this->tablePrefix."_logVisit` WHERE `serverTime` <= '$date'");
	}
	
	function check_report_exist( $date, $field ){
		global $wpdb;
		$sql = 'SELECT count(*) FROM '.$this->tablePrefix.'_datewise_report WHERE date = "'.$date.'" AND '.$field.' != ""';
		return $wpdb->get_var( $sql );
	}
	
	function insert_stats( $records, $table = '_datewise_report' ){
		global $wpdb;
		
		if( $records ){
			try{
				$fieldString = '';
				$valueString = '';
				$rowSeperator = '';
				$sql = '';
				foreach( $records as $parentRow => $row ){
					$columnSeperator = '';
					$columnString = '';
					$total_visitors = $row['total_visitors'];
					$total_first_time_visitors = $row['total_first_time_visitors'];
					foreach( $row as $key => $columnValue ){
						if( !$rowSeperator ){
							$fieldString .= $columnSeperator.'`'.$key.'`';
						}
						if( $key == 'total_visitors' && $total_visitors < $total_first_time_visitors ){
							$columnValue = $total_first_time_visitors;
						}
						if( $key == 'total_first_time_visitors' && $total_visitors < $total_first_time_visitors ){
							$columnValue = $total_visitors;
						}
						$columnString .= $columnSeperator.'"'.$columnValue.'"';	
						$columnSeperator = ',';
					}
					$valueString .= $rowSeperator.'('.$columnString.')';
					$rowSeperator = ',';
				}
				$sql = 'INSERT INTO '.$this->tablePrefix.$table.' ('.$fieldString.') VALUES '.$valueString;
				$wpdb->query( $sql );	
			}catch(Exception $e){
				error_log( date('Y-m-d h:i:s').': '.$e->getMessage()."\r\n", 3, WSM_DIR."errors.log");
			}
		}
	}
	
	function generate_recordset( $date, $field_name, $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query ){
		global $wpdb;
		
		$total_visitors = $wpdb->get_results($total_visitors_query, ARRAY_A);	
		
		$total_page_views = $wpdb->get_results($total_page_views_query, ARRAY_A);
		
		$temp = array();
		if( $total_page_views ){
			foreach( $total_page_views as $key => $page_views ){
				$temp[$page_views[ $field_name ] ] = $page_views['total_page_views'];
			}
		}
		$total_page_views = $temp;

		$total_first_time_visitors = $wpdb->get_results($total_first_time_visitors_query, ARRAY_A);

		$temp = array();
		if( $total_first_time_visitors ){
			foreach( $total_first_time_visitors as $key => $first_time_visitors ){
				$temp[$first_time_visitors[ $field_name ]] = $first_time_visitors['total_first_time_visitors'];
			}
		}
		$total_first_time_visitors = $temp;

		$records = array();
		if( $total_visitors ){
			foreach( $total_visitors as $key => $visitors ){
				$field_value = $visitors[ $field_name ];
				if( $field_value ){
					
					$total_visitors = $visitors['total_visitors'];
					$total_page_views = isset( $total_page_views[ $field_value ] ) ? $total_page_views[ $field_value ] : 0;
					$total_first_time_visitors = isset( $total_first_time_visitors[ $field_value ] ) ? $total_first_time_visitors[ $field_value ] : 0;
					$total_visitors = $total_first_time_visitors > $total_visitors ? $total_first_time_visitors : $total_visitors;
					$total_page_views = $total_page_views > $total_visitors ? $total_page_views : $total_visitors;
						
					$records[$key]['date'] = $date;
					$records[$key][$field_name] = $field_value;
					$records[$key]['total_visitors'] = $total_visitors;
					$records[$key]['total_page_views'] = $total_page_views;
					$records[$key]['total_first_time_visitors'] = $total_first_time_visitors;
					
				}
			}
		}
		
		return $records;
		
	}
	
	function record_normal_stats( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'normal' ) ){
			return;
		}

		$records['date'] = $date;
		$records['normal'] = 1;
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$sql = "select count(*) from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query";	
		$records['total_visitors'] = $wpdb->get_var($sql);
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$sql = "select count(*) from `{$this->tablePrefix}_logVisit` `LV` WHERE $conditional_query";	
		$records['total_page_views'] = $wpdb->get_var($sql);
		
		$sql = "select count(*) from `{$this->tablePrefix}_logUniqueVisit` `LU` left join `{$this->tablePrefix}_logVisit` `LV` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query group by `LU`.`visitorId`";	
		$wpdb->query( $sql );
		$records['total_first_time_visitors'] = $wpdb->num_rows;
		
		$serverTime = "CONVERT_TZ(BV.visitLastActionTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$sql = "select count(*) from `{$this->tablePrefix}_bounceVisits` `BV` WHERE $conditional_query";	
		$records['total_bounce'] = $wpdb->get_var($sql);
		$tempRecords[] = $records;
		$this->insert_stats( $tempRecords );
	}
	
	function record_hour_stats( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'hour' ) ){
			return;
		}

		for( $i = 0; $i < 24; $i++ ){
			$tempRecords = $records = array();
			$records['date'] = $date;
			$records['hour'] = $i + 1;
			$time = ( $i < 10 ? '0'.$i : $i );
			$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
			$conditional_query =" {$serverTime} >= '".$date.' '.$time.':00:00'."' AND {$serverTime}<='".$date.' '.$time.':59:59'."'";	
		
			$sql = "select count(*) from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query";	
			$records['total_visitors'] = $wpdb->get_var($sql);
		
			$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
			$conditional_query =" {$serverTime} >= '".$date.' '.$time.':00:00'."' AND {$serverTime}<='".$date.' '.$time.':59:59'."'";	
		
			$sql = "select count(*) from `{$this->tablePrefix}_logVisit` `LV` WHERE $conditional_query";	
			$records['total_page_views'] = $wpdb->get_var($sql);
		
			$sql = "select count(*) from `{$this->tablePrefix}_logUniqueVisit` `LU` left join `{$this->tablePrefix}_logVisit` `LV` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query group by `LU`.`visitorId`";	
			$wpdb->query( $sql );
			$records['total_first_time_visitors'] = $wpdb->num_rows;
		
			$serverTime = "CONVERT_TZ(BV.visitLastActionTime,'+00:00','".WSM_TIMEZONE."')";
			$conditional_query =" {$serverTime} >= '".$date.' '.$time.':00:00'."' AND {$serverTime}<='".$date.' '.$time.':59:59'."'";	
		
			$sql = "select count(*) from `{$this->tablePrefix}_bounceVisits` `BV` WHERE $conditional_query";	
			$records['total_bounce'] = $wpdb->get_var($sql);
			$tempRecords[] = $records;
			$this->insert_stats( $tempRecords );	
		}
	}
	
	function record_search_engines( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'search_engine' ) ){
			return;
		}
        $searchEngineQuery = '';
		
        $sqlQuery = "SELECT name as oName, LCASE(replace(name, ' ','')) AS name FROM `{$this->tablePrefix}_searchEngines`";
        $searchEngineResult= $wpdb->get_results($sqlQuery,ARRAY_A);
        if( $searchEngineResult ){
		
	        $searchEngineQuery .= ' AND ( ';
            $sepeartor = '';
            foreach( $searchEngineResult as $searchEngine ){
                $searchEngineQuery .= " $sepeartor UL.url LIKE '%".$searchEngine['name']."%' ";
                $sepeartor = ' OR ';
            }
			$searchEngineQuery .= ' ) GROUP BY UL.searchEngine';
        }
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_visitors_query = "select SUBSTRING_INDEX(UL.url,'/',1) AS search_engine, count(*) AS total_visitors from `{$this->tablePrefix}_logUniqueVisit` `LU` LEFT JOIN {$this->tablePrefix}_url_log AS UL on UL.id = LU.refererUrlId WHERE $conditional_query $searchEngineQuery";	
		$total_visitors = $wpdb->get_results($total_visitors_query, ARRAY_A);
		
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_page_views_query = "select SUBSTRING_INDEX(UL.url,'/',1) AS search_engine, count(*) AS total_page_views from `{$this->tablePrefix}_logVisit` `LV` LEFT JOIN {$this->tablePrefix}_url_log AS UL on UL.id = LV.refererUrlId WHERE $conditional_query $searchEngineQuery";	
		
		if( $total_visitors ){
			$searchEngineQuery = ' AND ( ';
            $sepeartor = '';
            foreach( $total_visitors as $visitors ){
                $searchEngineQuery .= " $sepeartor UL.url LIKE '%".$visitors['search_engine']."'";
                $sepeartor = ' OR ';
            }
			$searchEngineQuery .= ' ) GROUP BY UL.searchEngine';
		}
			
		$serverTime = "CONVERT_TZ(UV.firstVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_first_time_visitors_query = "select SUBSTRING_INDEX(UL.url,'/',1) AS search_engine, count(*) AS total_first_time_visitors from `{$this->tablePrefix}_uniqueVisitors` `UV` LEFT JOIN {$this->tablePrefix}_url_log AS UL on UL.id = UV.refererUrlId WHERE $conditional_query $searchEngineQuery";
		
		$records = $this->generate_recordset( $date, 'search_engine', $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query );
		
		$this->insert_stats( $records );
	}

	function record_browser( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'browser' ) ){
			return;
		}
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query2 = $conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_visitors_query = "select browserId AS browser, count(*) AS total_visitors from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query GROUP BY browser";	
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_page_views_query = "select LU.browserId AS browser, count(*) AS total_page_views from `{$this->tablePrefix}_logVisit` `LV` LEFT JOIN `{$this->tablePrefix}_logUniqueVisit` `LU` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query GROUP BY LU.browserId";	
			
		$serverTime = "CONVERT_TZ(UV.firstVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
	
		$total_first_time_visitors_query = "select LU.browserId AS browser, count(*) AS total_first_time_visitors from `{$this->tablePrefix}_uniqueVisitors` `UV` LEFT JOIN {$this->tablePrefix}_logUniqueVisit AS LU on LU.visitorId  = UV.visitorId WHERE $conditional_query AND $conditional_query2 GROUP BY LU.browserId";

		$records = $this->generate_recordset( $date, 'browser', $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query );
		
		$this->insert_stats( $records );
	}

	function record_screen( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'screen' ) ){
			return;
		}
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query2 = $conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_visitors_query = "select resolutionId AS screen, count(*) AS total_visitors from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query GROUP BY screen";	
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_page_views_query = "select LU.resolutionId AS screen, count(*) AS total_page_views from `{$this->tablePrefix}_logVisit` `LV` LEFT JOIN `{$this->tablePrefix}_logUniqueVisit` `LU` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query GROUP BY LU.resolutionId";	
			
		$serverTime = "CONVERT_TZ(UV.firstVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
	
		$total_first_time_visitors_query = "select LU.resolutionId AS screen, count(*) AS total_first_time_visitors from `{$this->tablePrefix}_uniqueVisitors` `UV` LEFT JOIN {$this->tablePrefix}_logUniqueVisit AS LU on LU.visitorId  = UV.visitorId WHERE $conditional_query AND $conditional_query2 GROUP BY LU.resolutionId";

		$records = $this->generate_recordset( $date, 'screen', $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query );
		
		$this->insert_stats( $records );
	}

	function record_country( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'country' ) ){
			return;
		}
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query2 = $conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_visitors_query = "select countryId AS country, count(*) AS total_visitors from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query GROUP BY country";	
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_page_views_query = "select LU.countryId AS country, count(*) AS total_page_views from `{$this->tablePrefix}_logVisit` `LV` LEFT JOIN `{$this->tablePrefix}_logUniqueVisit` `LU` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query GROUP BY LU.countryId";	
			
		$serverTime = "CONVERT_TZ(UV.firstVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
	
		$total_first_time_visitors_query = "select LU.countryId AS country, count(*) AS total_first_time_visitors from `{$this->tablePrefix}_uniqueVisitors` `UV` LEFT JOIN {$this->tablePrefix}_logUniqueVisit AS LU on LU.visitorId  = UV.visitorId WHERE $conditional_query AND $conditional_query2 GROUP BY LU.countryId";

		$records = $this->generate_recordset( $date, 'country', $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query );
		
		$this->insert_stats( $records );
	}

	function record_city( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'city' ) ){
			return;
		}
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query2 = $conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_visitors_query = "select city, count(*) AS total_visitors from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query GROUP BY city";	
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_page_views_query = "select LU.city, count(*) AS total_page_views from `{$this->tablePrefix}_logVisit` `LV` LEFT JOIN `{$this->tablePrefix}_logUniqueVisit` `LU` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query GROUP BY LU.city";	
			
		$serverTime = "CONVERT_TZ(UV.firstVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_first_time_visitors_query = "select LU.city, count(*) AS total_first_time_visitors from `{$this->tablePrefix}_uniqueVisitors` `UV` LEFT JOIN {$this->tablePrefix}_logUniqueVisit AS LU on LU.visitorId  = UV.visitorId WHERE $conditional_query AND $conditional_query2 GROUP BY LU.city";

		$records = $this->generate_recordset( $date, 'city', $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query );
		
		$this->insert_stats( $records );
	}
	
	function record_os( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'operating_system' ) ){
			return;
		}
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query2 = $conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_visitors_query = "select oSystemId AS operating_system, count(*) AS total_visitors from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query GROUP BY operating_system";	
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_page_views_query = "select LU.oSystemId AS operating_system, count(*) AS total_page_views from `{$this->tablePrefix}_logVisit` `LV` LEFT JOIN `{$this->tablePrefix}_logUniqueVisit` `LU` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query GROUP BY LU.oSystemId";	
			
		$serverTime = "CONVERT_TZ(UV.firstVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
	
		$total_first_time_visitors_query = "select LU.oSystemId AS operating_system, count(*) AS total_first_time_visitors from `{$this->tablePrefix}_uniqueVisitors` `UV` LEFT JOIN {$this->tablePrefix}_logUniqueVisit AS LU on LU.visitorId  = UV.visitorId WHERE $conditional_query AND $conditional_query2 GROUP BY LU.oSystemId";

		$records = $this->generate_recordset( $date, 'operating_system', $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query );
		
		$this->insert_stats( $records );
	}

	function record_url( $date ){
		global $wpdb;
		
		if( $this->check_report_exist( $date, 'url_id' ) ){
			return;
		}
		
		$serverTime = "CONVERT_TZ(LU.firstActionVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query2 = $conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_visitors_query = "select refererUrlId AS url_id, count(*) AS total_visitors from `{$this->tablePrefix}_logUniqueVisit` `LU` WHERE $conditional_query GROUP BY url_id";	
		
		$serverTime = "CONVERT_TZ(LV.serverTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
		
		$total_page_views_query = "select LU.refererUrlId AS url_id, count(*) AS total_page_views from `{$this->tablePrefix}_logVisit` `LV` LEFT JOIN `{$this->tablePrefix}_logUniqueVisit` `LU` on `LV`.`visitId` = `LU`.`id` WHERE $conditional_query GROUP BY LU.refererUrlId";	
			
		$serverTime = "CONVERT_TZ(UV.firstVisitTime,'+00:00','".WSM_TIMEZONE."')";
		$conditional_query =" {$serverTime} >= '".$date.' 00:00:00'."' AND {$serverTime}<='".$date.' 23:59:59'."'";	
	
		$total_first_time_visitors_query = "select LU.refererUrlId AS url_id, count(*) AS total_first_time_visitors from `{$this->tablePrefix}_uniqueVisitors` `UV` LEFT JOIN {$this->tablePrefix}_logUniqueVisit AS LU on LU.visitorId  = UV.visitorId WHERE $conditional_query AND $conditional_query2 GROUP BY LU.refererUrlId";

		$records = $this->generate_recordset( $date, 'url_id', $total_visitors_query, $total_page_views_query, $total_first_time_visitors_query );
		
		$this->insert_stats( $records );
	}
	
	function record_month_year( $date, $table, $ref_table, $first_day, $last_day, $date_formate ){
		global $wpdb;
		
		$serverTime = "dr.date";
		$conditional_query =" {$serverTime} >= '$first_day' AND {$serverTime}<='$last_day' ";	
		
		$report_types = array( 'normal', 'browser', 'screen', 'country', 'operating_system', 'url_id' );
		
		foreach( $report_types as $report_type ){
		
			$sql = "SELECT DATE_FORMAT(dr.`date`, '$date_formate') AS date, dr.$report_type, sum(dr.total_page_views) AS total_page_views , sum(dr.total_visitors) AS total_visitors, sum(dr.total_first_time_visitors) AS total_first_time_visitors , sum(dr.total_bounce) AS total_bounce FROM $ref_table AS dr WHERE $conditional_query AND dr.$report_type > 0 GROUP BY dr.$report_type";
		
			$records = $wpdb->get_results( $sql, ARRAY_A );
			$this->insert_stats( $records, $table );
		}
		
		$report_types = array( 'search_engine', 'city' );
		
		foreach( $report_types as $report_type ){
			
			$sql = "SELECT DATE_FORMAT(dr.`date`, '$date_formate') AS date, dr.$report_type, sum(dr.total_page_views) AS total_page_views , sum(dr.total_visitors) AS total_visitors, sum(dr.total_first_time_visitors) AS total_first_time_visitors, sum(dr.total_bounce) AS total_bounce FROM $ref_table AS dr WHERE $conditional_query AND dr.$report_type != '' GROUP BY dr.$report_type";
		
			$records = $wpdb->get_results( $sql, ARRAY_A );
			$this->insert_stats( $records, $table );
		}
		
	
		$sql = "SELECT DATE_FORMAT(dr.`date`, '%Y-%m-%d') AS date, dr.hour, sum(dr.total_page_views) AS total_page_views , sum(dr.total_visitors) AS total_visitors, sum(dr.total_first_time_visitors) AS total_first_time_visitors , sum(dr.total_bounce) AS total_bounce FROM $ref_table AS dr WHERE $conditional_query AND dr.hour > 0 GROUP BY dr.date";
	
		$records = $wpdb->get_results( $sql, ARRAY_A );
		$this->insert_stats( $records, $table );
		
	}
	
}
new wsmCron();
?>
