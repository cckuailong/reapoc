<?php

// Make sure plugin remains secure if called directly
if( !defined( 'ABSPATH' ) ) {
  if( !headers_sent() ) { header('HTTP/1.1 403 Forbidden'); }
  die(__('ERROR: This plugin requires WordPress and will not function if called directly.','newstatpress'));
}

/**
 * API: Overview
 *
 * Return the overview according to the passed parameters as json encoded
 *
 * @param typ the type of result (Json/Html)
 * @param par the number of days for the graph (20 default, if 0 use the one in NewStatPress option)
 * @return the result
 */
function nsp_ApiOverview($typ, $par) {
  global $wpdb;
  global $nsp_option_vars;

  $offsets = get_option($nsp_option_vars['stats_offsets']['name']);

  $table_name = nsp_TABLENAME;

  $since = nsp_ExpandVarsInsideCode('%since%');
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


  // get the days of the graph
  $gdays=intval($par);
  if($gdays == 0) { $gdays=get_option('newstatpress_daysinoverviewgraph'); }
  if($gdays == 0) { $gdays=20; }

  // get result of dashboard as some date is shared with this
  $resultJ=nsp_ApiDashboard("JSON");
  
  $resultJ['days']=$gdays;  // export


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
    
    // not need prepare
    $resultJ[$row.'_total'] = $wpdb->get_row($sql_QueryTotal)->$row;  // export
    // use prepare
    $resultJ[$row.'_tyear'] = $wpdb->get_row($wpdb->prepare($sql_QueryTotal. " AND date LIKE %s", $thisyear.'%'))->$row;  // export
    
    switch($row) {
      case 'visitors' :
        $resultJ[$row.'_total']+=$offsets['alltotalvisits'];
        break;
      case 'visitors_feeds' :
        $resultJ[$row.'_total']+=$offsets['visitorsfeeds'];
        break;
      case 'pageview' :
        $resultJ[$row.'_total']+=$offsets['pageviews'];
        break;
      case 'spiders' :
        $resultJ[$row.'_total']+=$offsets['spy'];
        break;
      case 'feeds' :
        $resultJ[$row.'_total']+=$offsets['pageviewfeeds'];
        break;
    }
    
  }
  
  // make graph
  
  $maxxday = 0;
  for($gg=$gdays-1;$gg>=0;$gg--) {

    $date=gmdate('Ymd', current_time('timestamp')-86400*$gg);

    // use prepare
    $qry_visitors=$wpdb->get_row($wpdb->prepare(
       "SELECT count(DISTINCT ip) AS total FROM $table_name WHERE feed='' AND spider='' AND date = %s", $date));
    $visitors[$gg]=$qry_visitors->total;

    $qry_pageviews=$wpdb->get_row($wpdb->prepare(
       "SELECT count(date) AS total FROM $table_name WHERE feed='' AND spider='' AND date = %s", $date));
    $pageviews[$gg]=$qry_pageviews->total;

    $qry_spiders=$wpdb->get_row($wpdb->prepare(
       "SELECT count(date) AS total FROM $table_name WHERE feed='' AND spider<>'' AND date = %s", $date));
    $spiders[$gg]=$qry_spiders->total;

    $qry_feeds=$wpdb->get_row($wpdb->prepare(
       "SELECT count(date) AS total FROM $table_name WHERE feed<>'' AND spider='' AND date = %s", $date));
    $feeds[$gg]=$qry_feeds->total;

    $total= $visitors[$gg] + $pageviews[$gg] + $spiders[$gg] + $feeds[$gg];
    if ($total > $maxxday) $maxxday= $total;
  }
  if($maxxday == 0) { $maxxday = 1; }
  
  $resultJ['visitors']  = $visitors;  //export
  $resultJ['pageviews'] = $pageviews; //export
  $resultJ['spiders']   = $spiders;   //export
  $resultJ['feeds']     = $feeds;     //export
  $resultJ['max']       = $maxxday;   // export

  // output an HTML representation of the collected data


  $overview_table='';

  // dashboard
  $overview_table.="<table class='widefat center nsp'>
                     <thead>
                      <tr class='sup'>
                       <th></th>
                       <th>". __('Total since','newstatpress'). "</th>
                       <th scope='col'>". __('This year','newstatpress'). "</th>
                       <th scope='col'>". __('Last month','newstatpress'). "</th>
                       <th scope='col' colspan='2'>". __('This month','newstatpress'). "</th>
                       <th scope='col' colspan='2'>". __('Target This month','newstatpress'). "</th>
                       <th scope='col'>". __('Yesterday','newstatpress'). "</th>
                       <th scope='col'>". __('Today','newstatpress'). "</th>
                      </tr>
                      <tr class='inf'>
                       <th></th>
                       <th><span>$since</span></th>
                       <th><span>$thisyearHeader</span></th>
                       <th><span>$lastmonthHeader</span></th>
                       <th colspan='2'><span > $thismonthHeader </span></th>
                       <th colspan='2'><span > $thismonthHeader </span></th>
                       <th><span>$yesterdayHeader</span></th>
                       <th><span>$todayHeader</span></th>
                      </tr>
                     </thead>
                    <tbody class='overview-list'>";

  // build body table overview
  $overview_rows=array('visitors','visitors_feeds','pageview','feeds','spiders');

  foreach ($overview_rows as $row) {
    $result=nsp_CalculateVariation($resultJ[$row.'_tmonth'],$resultJ[$row.'_lmonth']);

    // build full current row
    $overview_table.="<tr><td class='row_title $row'>".$resultJ[$row.'_title']."</td>";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_total']."</td>\n";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_tyear']."</td>\n";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_lmonth']."</td>\n";
    $overview_table.="<td class='colr'>".$resultJ[$row.'_tmonth'].$result[0] ."</td>\n";
    $overview_table.="<td class='colr'> $result[1] $result[2] </td>\n";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_qry_y']."</td>\n";
    $overview_table.="<td class='colc'>".$resultJ[$row.'_qry_t']."</td>\n";
    $overview_table.="</tr>";
  }

  $overview_table.="</tr></table>";
  
  $start_of_week = get_option('start_of_week');
  $gd=(90/$gdays).'%';

  $overview_graph="<table class='graph'><tr>";
  
  for($gg=$gdays-1;$gg>=0;$gg--) {

    $scale_factor=2; //2 : 200px in CSS

    $date=gmdate('Ymd', current_time('timestamp')-86400*$gg);

    $px_visitors = $scale_factor*(round($resultJ['visitors'][ $gg]*100/$maxxday));
    $px_pageviews= $scale_factor*(round($resultJ['pageviews'][$gg]*100/$maxxday));
    $px_spiders  = $scale_factor*(round($resultJ['spiders'][$gg]*100/$maxxday));
    $px_feeds    = $scale_factor*(round($resultJ['feeds'][$gg]*100/$maxxday));

    $px_white = $scale_factor*100 - $px_feeds - $px_spiders - $px_pageviews - $px_visitors;

    $overview_graph.="<td width='$gd' valign='bottom'>";

    $overview_graph.="<div class='overview-graph'>
      <div style='border-left:1px; background:#ffffff;width:100%;height:".$px_white."px;'></div>
        <div class='visitors_bar' style='height:".$px_visitors."px;' title='".$resultJ['visitors'][$gg]." ".__('Visitors','newstatpress')."'></div>
        <div class='web_bar' style='height:".$px_pageviews."px;' title='".$resultJ['pageviews'][$gg]." ".__('Pageviews','newstatpress')."'></div>
        <div class='spiders_bar' style='height:".$px_spiders."px;' title='".$resultJ['spiders'][$gg]." ".__('Spiders','newstatpress')."'></div>
        <div class='feeds_bar' style='height:".$px_feeds."px;' title='".$resultJ['feeds'][$gg]." ".__('Feeds','newstatpress')."'></div>
        <div style='background:gray;width:100%;height:1px;'></div>";
      if($start_of_week == gmdate('w',current_time('timestamp')-86400*$gg)) $overview_graph.="<div class='legend-W'>";
      else $overview_graph.="<div class='legend'>";
      $overview_graph.=gmdate('d', current_time('timestamp')-86400*$gg) . ' ' . gmdate('M', current_time('timestamp')-86400*$gg) .     "</div></div></td>\n";
  }
  $overview_graph.="</tr></table>";

  $overview_table=$overview_table.$overview_graph;
  

  $resultH=$overview_table;
  return $resultH;

}?>
