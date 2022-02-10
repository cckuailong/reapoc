<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * API: Dashboard
 *
 * Return the overview according to the passed parameters as json encoded
 *
 * @param typ the type of result (Json/Html)
 * @return the result
 */
function nsp_ApiDashboard($typ) {
  global $wpdb;
  global $nsp_option_vars;
  
  $table_name = nsp_TABLENAME;

  $lastmonth = nsp_Lastmonth();


  $thisyear = gmdate('Y', current_time('timestamp'));
  $thismonth = gmdate('Ym', current_time('timestamp'));
  $yesterday = gmdate('Ymd', current_time('timestamp')-86400);
  $today = gmdate('Ymd', current_time('timestamp'));
  $tlm[0]=substr($lastmonth,0,4); $tlm[1]=substr($lastmonth,4,2);

  $thisyearHeader = gmdate('Y', current_time('timestamp'));
  $lastmonthHeader = gmdate('M, Y',gmmktime(0,0,0,$tlm[1],1,$tlm[0]));
  $thismonthHeader = gmdate('M, Y', current_time('timestamp'));
  $yesterdayHeader = gmdate('d M', current_time('timestamp')-86400);
  $todayHeader = gmdate('d M', current_time('timestamp'));


  $resultJ['lastmonth']=$lastmonth;                       // export
  $resultJ['thisyear']=$thisyear;                         // export
  $resultJ['thismonth']=$thismonth;                       // export
  $resultJ['yesterday']=$yesterday;                       // export
  $resultJ['today']=$today;                               // export

  $thismonth1 = gmdate('Ym', current_time('timestamp')).'01';
  $thismonth31 = gmdate('Ymt', current_time('timestamp'));
  $lastmonth1 = $lastmonth.'01';
  $lastmonth31 = gmdate('Ymt', strtotime($lastmonth1));


  $overview_rows=array('visitors','visitors_feeds','pageview','feeds','spiders');

  foreach ($overview_rows as $row) {

    switch($row) {
      case 'visitors' :
        $row2='DISTINCT ip';
        $row_title=__('Visitors','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider=''";
        break;
      case 'visitors_feeds' :
        $row2='DISTINCT ip';
        $row_title=__('Visitors through Feeds','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed<>'' AND spider='' AND agent<>''";
        break;
      case 'pageview' :
        $row2='date';
        $row_title=__('Pageviews','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider=''";
        break;
      case 'spiders' :
        $row2='date';
        $row_title=__('Spiders','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed='' AND spider<>''";
        break;
      case 'feeds' :
        $row2='date';
        $row_title=__('Pageviews through Feeds','newstatpress');
        $sql_QueryTotal="SELECT count($row2) AS $row FROM $table_name WHERE feed<>'' AND spider=''";
        break;
    }

    if (get_option($nsp_option_vars['calculation']['name'])=='sum') {

      // alternative calculation by mouth: sum of unique visitors of each day
      $tot=0;
      $t = getdate(current_time('timestamp'));
      $year = $t['year'];
      $month = sprintf('%02d', $t['mon']);
      $day= $t['mday'];
      $totlm=0;

      for($k=$t['mon'];$k>0;$k--)
      {
        //current month

      }
      for($i=0;$i<$day;$i++)
      {
        // use prepare
        $qry_daylmonth=$wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $lastmonth.$i.'%'));
        $qry_day=$wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $year.$month.$i.'%'));
        $tot+=$qry_day->$row;
        $totlm+=$qry_daylmonth->$row;

      }
      // echo $totlm." ,";
      $qry_tmonth=new stdClass();
      $qry_lmonth=new stdClass();
      $qry_tmonth->$row=$tot;
      $qry_lmonth->$row=$totlm;

    }
    else { // classic
      // use prepare
      $qry_tmonth = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date BETWEEN %s AND %s", $thismonth1, $thismonth31));
      $qry_lmonth = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date BETWEEN %s AND %s", $lastmonth1, $lastmonth31));
    }

    $resultJ[$row.'_tmonth'] = $qry_tmonth->$row;  // export
    $resultJ[$row.'_lmonth'] = $qry_lmonth->$row;  // export

    // use prepare
    $qry_y = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $yesterday));
    $qry_t = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $today));

    $resultJ[$row.'_qry_y'] = $qry_y->$row;  // export
    $resultJ[$row.'_qry_t'] = $qry_t->$row;  // export

    if($resultJ[$row.'_lmonth'] <> 0) $resultJ[$row.'_perc_change'] = round( 100 * ($resultJ[$row.'_tmonth'] / $resultJ[$row.'_lmonth'] ) - 100,1)."%";  // export
    else $resultJ[$row.'_perc_change'] ='';
    
    $resultJ[$row.'_title']=$row_title;       // export
  }

  if ($typ=="JSON") return $resultJ;  // avoid to calculte HTML if not necessary

  // output a HTML representation of the collected data

  $overview_table='';

  // dashboard
  $overview_table.="<table class='widefat center nsp'>
                      <thead>
                      <tr class='sup dashboard'>
                      <th></th>
                          <th scope='col'>". __('M-1','newstatpress'). "</th>
                          <th scope='col' colspan='2'>". __('M','newstatpress'). "</th>
                          <th scope='col'>". __('Y','newstatpress'). "</th>
                          <th scope='col'>". __('T','newstatpress'). "</th>
                      </tr>
                      <tr class='inf dashboard'>
                      <th></th>
                          <th><span>$lastmonthHeader</span></th>
                          <th colspan='2'><span > $thismonthHeader </span></th>
                          <th><span>$yesterdayHeader</span></th>
                          <th><span>$todayHeader</span></th>
                      </tr></thead>
                      <tbody class='overview-list'>";

  foreach ($overview_rows as $row) {
    $result=nsp_CalculateVariation($resultJ[$row.'_tmonth'],$resultJ[$row.'_lmonth']);

    // build full current row
    $overview_table.="<tr><td class='row_title $row'>".$resultJ[$row.'_title']."</td>";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_lmonth']."</td>\n";
    $overview_table.="<td class='colr'>".$resultJ[$row.'_tmonth'].$result[0] ."</td>\n";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_qry_y']."</td>\n";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_qry_t']."</td>\n";
    $overview_table.="</tr>";
  }

  $overview_table.="</tr></table>\n";

  $resultH=$overview_table;  
  return $resultH;
}
?>
