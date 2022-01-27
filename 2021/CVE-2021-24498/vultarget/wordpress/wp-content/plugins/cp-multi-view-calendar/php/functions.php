<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define("DC_MV_CALENDARS",$wpdb->prefix."dc_mv_calendars");
define("DC_MV_CALENDARS_ID","id");
define("DC_MV_CALENDARS_NAME","name");
define("DC_MV_CALENDARS_USER","uname");
define("DC_MV_CALENDARS_PASS","passwd");
define("DC_MV_CALENDARS_PARAMS","params");
define("DC_MV_CALENDARS_DELETED","caldeleted");

define("DC_MV_CAL",$wpdb->prefix."dc_mv_events");
define("DC_MV_CAL_ID","id");
define("DC_MV_CAL_IDCAL","calid");
define("DC_MV_CAL_FROM","starttime");
define("DC_MV_CAL_TO","endtime");
define("DC_MV_CAL_TITLE","title");
define("DC_MV_CAL_LOCATION","location");
define("DC_MV_CAL_DESCRIPTION","description");
define("DC_MV_CAL_ISALLDAY","isalldayevent");
define("DC_MV_CAL_COLOR","color");

define("DC_MV_PREFIX","cal");

function js2PhpTime($jsdate){
  if(preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)((am|pm)*)@', $jsdate, $matches)==1){
    if ($matches[6]=="pm")
        if ($matches[4]<12)
            $matches[4] += 12;
    $ret = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
  }else if(preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches)==1){
    $ret = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
  }
  return $ret;
}

function php2JsTime($phpDate){
    return @date("m/d/Y H:i", $phpDate);
}

function php2MySqlTime($phpDate){
    return @date("Y-m-d H:i:s", $phpDate);
}

function mySql2PhpTime($sqlDate){
    $a1 = explode (" ",$sqlDate);
    $a2 = explode ("-",$a1[0]);
    $a3 = explode (":",$a1[1]);
    $t = mktime($a3[0],$a3[1],$a3[2],$a2[1],$a2[2],$a2[0]);
    return $t;


}
?>