<?php
if ( ! defined( 'ABSPATH' ) ) exit; 



function wsm_free_top_bar_enqueue_style() {

echo '<style>
#wpadminbar #wp-admin-bar-wsm_free_top_button .ab-icon:before {
	content: "\f239";
	color: #FF9800;
	top: 3px;
}
</style>';

}


function wsm_free_add_items($admin_bar)
{
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $pluginsurl;
	//The properties of the new item. Read More about the missing 'parent' parameter below
	$args = array(
			'id'    => 'wsm_free_top_button',
			'parent' => null,
			'group'  => null,
			'title' => '<span class="ab-icon"></span>'.'' . __('Stats', 'wp-stats-manager'),
			'href'  => admin_url('admin.php?page=wsm_traffic'),
			'meta'  => array('title' => __('visitor statistics', 'wp-stats-manager'),
							'class' => '')
			);
 
	//This is where the magic works.
	$admin_bar->add_menu( $args);
}

	
function wsmGetAverageOfArray($array){
    $average=0;
    if(count($array)>0){
        $sum=array_sum($array);
        if($sum>0){
            $average = array_sum($array) / count($array);
        }
    }
    return $average;
}

function wsm_add_async_defer_attribute($tag, $handle) {
	
	if ( 'wsm-front-js' !== $handle )
	return $tag;
	return str_replace( ' src', ' async defer src', $tag );
	}
	
	
function wsmMaskIPaddress($ip='')
{
	$ip = explode('.', $ip);
	
	return $ip[0].'.'.$ip[1].'.'.$ip[2].'.***';
}


function wsmGetDateByInterval($interval,$format='Y-m-d H:i:s',$dateTime='now'){    
	try{
		$timeZone=  new DateTimeZone(wsmGetTimeZone());
		$date = new DateTime($dateTime,$timeZone );
		$newTimeZone=  new DateTimeZone(wsmGetTimezoneString());
		$date->setTimezone($newTimeZone);
		$date->modify($interval);
		return $date->format($format);
	}catch(Exception $e){
		return $format;
	}
}
function wsmValidateDateTime($dateStr, $format='Y-m-d'){ 
	$timezone = wsmGetTimezoneString();
	try{
		$newTimeZone=  new DateTimeZone($timezone);	
	    $date = DateTime::createFromFormat($format, $dateStr);
	    //DateTime::setTimezone($newTimeZone);   
	    return $date && ($date->format($format) === $dateStr);
	}catch(Exception $e){
		echo sprintf( '<p>'. __( 'Message: This timezone(%s) is not supported in this PHP version %s.','wp-stats-manager') .'</p>', $timezone, phpversion() );
	}
}
function wsmDateDifference($pastDate,$zone='UTC',$returnBy='day',$futureDate='now'){
    if($zone=='UTC'){
        $pastDate=wsmConvertDateUTCtoTimeZone($pastDate);
        $futureDate=$futureDate!='now'?wsmConvertDateUTCtoTimeZone($futureDate):wsmGetCurrentDateByTimeZone();
    }    
    $newTimeZone=  new DateTimeZone(wsmGetTimezoneString());
    $pastDate = new DateTime($pastDate,$newTimeZone );
    $futureDate = new DateTime($futureDate,$newTimeZone );
    $pastDate->setTimezone($newTimeZone);
    $futureDate->setTimezone($newTimeZone);
    $difference=$futureDate->diff($pastDate);
    switch($returnBy){
        case 'years':
            return $y=$difference->y;
        break;
        case 'day':
            $y=$difference->y;
            $m=$difference->m;
            $d=$difference->d;
            $totalDays=($y*365)+($m*30)+$d;
            return $totalDays;
        break;
        case 'mm:ss':
            $y=$difference->y;
            $m=$difference->m;
            $d=$difference->d;
            $totalHours=($y*8760)+($m*720)+$d*24;
            return $totalDays;
        break;
    }
}
function wsmFormatHourlyStats($type,$result){
    $retArray=array();
    for($i=0;$i<24;$i++){
        $retArray[$i][$type]=0;
    }
    $rowColumn=$type;
    if($type=='bounceRate'){
        $rowColumn='bRateVisitors';
    }    
    if(!is_null($result) && is_array($result)){
        foreach($result as $key=>$row){
            $retArray[$row['hour']][$type]=$row[$rowColumn];
        }
    }
    return $retArray;
}
function wsmGetChangeInPercentage($from,$to){
    $percent=0;
    if($from!=0 && $to!=0){
       $diff=$to-$from;
       $percent=($diff*100)/$from;
    }
    return $percent;
}
function wsmConvertTimeDifference($diff, $toFormat="mm:ss"){
    $arrDiff=explode(':',$diff);
    $minutes=0;
    if(count($arrDiff)>2){
        $minutes=(($arrDiff[0]*60)+$arrDiff[1]);
    }
    if(count($arrDiff)==2){
        $arrDiff=$arrDiff[0];
    }
    $seconds=$arrDiff[count($arrDiff)-1];
    $time=str_replace('mm',$minutes,$toFormat);
    $time=str_replace('ss',$seconds,$time);
    return $time;
}
function wsmConvertSecondsToTimeFormat($time){
    $hours = floor($time / 3600);
    $minutes = floor(($time / 60) % 60);
    $seconds = $time % 60;
    $string=$hours>0?$hours.'h ':'';
    $string.=($hours<1 && $minutes <1)?'':$minutes.'m ';
    $string.=($seconds>0)?$seconds.'s':'';
    return $string;
}
function wsmConvertDateUTCtoTimeZone($dateTime,$format="Y-m-d H:i:s" ){
    try{
	$timeZone=  new DateTimeZone(wsmGetTimeZone());
    $date = new DateTime($dateTime,$timeZone );
	
	
    $timeZone=  new DateTimeZone(wsmGetTimezoneString());
	
    $date->setTimezone($timeZone);
    return $date->format($format);
	}
	catch (Exception $e) {
    
	die('<div class="error" style="padding:20px; margin:10px">Pease check the settings tab > timezone section <a href="'.admin_url('admin.php?page='.WSM_PREFIX.'_settings').'">here</a> </div>');
	
	}
}
function wsmGetCurrentUTCDate($format = "Y-m-d H:i:s"){
    $dateTime='now';
    $timeZone=  new DateTimeZone(wsmGetTimeZone());
    $date = new DateTime($dateTime,$timeZone );
    $date->setTimezone($timeZone);
    return $date->format($format);
}
function wsmGetCurrentDateByTimeZone($format = "Y-m-d H:i:s"){
    $dateTime='now';
    $timeZone=  new DateTimeZone(wsmGetTimeZone());
	try{
		$date = new DateTime($dateTime,$timeZone );
		$timeZone=  new DateTimeZone(wsmGetTimezoneString());
		$date->setTimezone($timeZone);
	}catch( Exception $e ){
		$date = new DateTime($dateTime);
	}
    return $date->format($format);
}
function wsmGetYesterdayDateByTimeZone($format = "Y-m-d H:i:s"){
    $dateTime='yesterday';
    $timeZone=  new DateTimeZone(wsmGetTimeZone());
	try{
		$date = new DateTime($dateTime,$timeZone );
	}catch( Exception $e ){
		$date = new DateTime($dateTime);
	}
    $timeZone=  new DateTimeZone(wsmGetTimezoneString());
    $date->setTimezone($timeZone);
    return $date->format($format);
}
function wsmGetDateByTimeStamp($format='Y-m-d H:i:s',$timestamp){
    $timeZone=  new DateTimeZone(wsmGetTimeZone());
    $date = new DateTime(date_i18n('Y-m-d H:i:s',$timestamp), $timeZone);
    $date->setTimezone($timeZone);
    return $date->format($format);
}
function wsmGetTimeZone($tz=""){
	
    if($tz==""){
        return 'UTC';
    }else{
        return wsmGetTimezoneString();
    }
}
function wsmCurrentGetTimezoneOffset(){
    global $arrCashedStats;
    if(isset($arrCashedStats['timeZoneOffset']) && $arrCashedStats['timeZoneOffset']!=''){
        return $arrCashedStats['timeZoneOffset'];
    }
    $tz=wsmGetTimezoneString();
	try{
	    $timeZone=  new DateTimeZone($tz);
	    $date = new DateTime('now',$timeZone );
	    $date->setTimezone($timeZone);	
	}catch( Exception $e ){
		$date = new DateTime('now');
	}
    $arrCashedStats['timeZoneOffset']=$date->format('P');
    return $date->format('P');
}
function wsmGetTimezoneString() {
    /*global $arrCashedStats;
    if(isset($arrCashedStats['timeZone']) && $arrCashedStats['timeZone']!='' && $arrCashedStats['timeZone']!='00:0000:00'){
        return $arrCashedStats['timeZone'];
    }
    $wsmTimeZone=get_option(WSM_PREFIX.'TimezoneString' );
    if(is_null($wsmTimeZone) || $wsmTimeZone==''){
        $wsmTimeZone=wsmGetWPTimezoneString();
    }
    $wsmTimeZone= wsmCleanupTimeZoneString($wsmTimeZone);
    $arrCashedStats['timeZone']=$wsmTimeZone;
    return $wsmTimeZone;*/
	
	 global $arrCashedStats;
	
    if(isset($arrCashedStats['timeZone']) && $arrCashedStats['timeZone']!='' && $arrCashedStats['timeZone']!='00:0000:00' && $arrCashedStats['timeZone']!='00:00'){
        return $arrCashedStats['timeZone'];
    }
    $wsmTimeZone=get_option(WSM_PREFIX.'TimezoneString' );
	
    if(is_null($wsmTimeZone) || $wsmTimeZone==''){
        $wsmTimeZone=wsmGetWPTimezoneString();
    }
    $wsmTimeZone= wsmCleanupTimeZoneString($wsmTimeZone);
	
	if($wsmTimeZone == '00:00')
	{
		$wsmTimeZone = date_default_timezone_get();
	}
    $arrCashedStats['timeZone']=$wsmTimeZone;
    return $wsmTimeZone;
}
function wsmGetWPTimezoneString() {
    // if site timezone string exists, return it
    if ( $timezone = get_option( 'timezone_string' ) )
        return $timezone;

    // get UTC offset, if it isn't set then return UTC
    if ( 0 === ( $utc_offset = get_option( 'gmt_offset', 0 ) ) )
        return 'UTC';

    // adjust UTC offset from hours to seconds
    $utc_offset *= 3600;

    // attempt to guess the timezone string from the UTC offset
    if ( $timezone = timezone_name_from_abbr( '', $utc_offset, 0 ) ) {
        return $timezone;
    }

    // last try, guess timezone string manually
    $is_dst = date( 'I' );

    foreach ( timezone_abbreviations_list() as $abbr ) {
        foreach ( $abbr as $city ) {
            if ( $city['dst'] == $is_dst && $city['offset'] == $utc_offset )
                return $city['timezone_id'];
        }
    }

    // fallback to UTC
    return 'UTC';
}
function wsmCleanupTimeZoneString($tzString){
    $offset=$tzString;

    if (preg_match('/^UTC[+-]/', $tzString)) {
       $tzString= preg_replace('/UTC\+?/', '', $tzString);
    }
    if(is_numeric($tzString)){
        $offset=sprintf('%02d:%02d', (int) $tzString, fmod(abs($tzString), 1) * 60);
        if((int) $tzString>0){
            $offset='+'.$offset;
        }
    }
    return $offset;
}
function wsmGetCurrentTimeStamp(){
    return current_time('timestamp',1);
}
function wsmIsTimestampValid($time, $now = null){
    if (empty($now)) {
        $now = wsmGetCurrentTimeStamp();
    }
    return $time <= $now
        && $time > $now - 10 * 365 * 86400;
}
function wsmFnCalculateForeCastData($xArray,$yArray,$next){
    $xAvg=wsmGetAverageOfArray($xArray);
    $yAvg=wsmGetAverageOfArray($yArray);
    $tempTop = 0;
    $tempBottom =0;
    for ($i = 0; $i < count($yArray); $i++){
        $tempTop += ($xArray[$i] - $xAvg) * ($yArray[$i] - $yAvg);
        $tempBottom += pow(($xArray[$i] - $xAvg), 2);
    }
    $b=0;
    if($tempTop!=0 && $tempBottom!=0){
        $b = $tempTop / $tempBottom;
    }
    $a = $yAvg - $b * $xAvg;
    $forecast=array();
    if(is_array($next) && count($next)>0){
        foreach($next as $n){
            $fCast=($a + ($b*$n));
            $fCast=$fCast<0?0:$fCast;
            array_push($forecast,$fCast);
        }
    }else{
        $forecast = $a + $b*$next;
        $forecast = $forecast<0?0:$forecast;
    }
    return $forecast;
}
function wsmFnGetTimeZoneByCountry($country, $region='01'){
    $timezone = null;
    switch ($country) {
        case "AD":
            $timezone = "Europe/Andorra";
            break;
        case "AE":
            $timezone = "Asia/Dubai";
            break;
        case "AF":
            $timezone = "Asia/Kabul";
            break;
        case "AG":
            $timezone = "America/Antigua";
            break;
        case "AI":
            $timezone = "America/Anguilla";
            break;
        case "AL":
            $timezone = "Europe/Tirane";
            break;
        case "AM":
            $timezone = "Asia/Yerevan";
            break;
        case "AN":
            $timezone = "America/Curacao";
            break;
        case "AO":
            $timezone = "Africa/Luanda";
            break;
        case "AQ":
            $timezone = "Antarctica/South_Pole";
            break;
        case "AR":
            switch ($region) {
                case "01":
                    $timezone = "America/Argentina/Buenos_Aires";
                    break;
                case "02":
                    $timezone = "America/Argentina/Catamarca";
                    break;
                case "03":
                    $timezone = "America/Argentina/Tucuman";
                    break;
                case "04":
                    $timezone = "America/Argentina/Rio_Gallegos";
                    break;
                case "05":
                    $timezone = "America/Argentina/Cordoba";
                    break;
                case "06":
                    $timezone = "America/Argentina/Tucuman";
                    break;
                case "07":
                    $timezone = "America/Argentina/Buenos_Aires";
                    break;
                case "08":
                    $timezone = "America/Argentina/Buenos_Aires";
                    break;
                case "09":
                    $timezone = "America/Argentina/Tucuman";
                    break;
                case "10":
                    $timezone = "America/Argentina/Jujuy";
                    break;
                case "11":
                    $timezone = "America/Argentina/San_Luis";
                    break;
                case "12":
                    $timezone = "America/Argentina/La_Rioja";
                    break;
                case "13":
                    $timezone = "America/Argentina/Mendoza";
                    break;
                case "14":
                    $timezone = "America/Argentina/Buenos_Aires";
                    break;
                case "15":
                    $timezone = "America/Argentina/San_Luis";
                    break;
                case "16":
                    $timezone = "America/Argentina/Buenos_Aires";
                    break;
                case "17":
                    $timezone = "America/Argentina/Salta";
                    break;
                case "18":
                    $timezone = "America/Argentina/San_Juan";
                    break;
                case "19":
                    $timezone = "America/Argentina/San_Luis";
                    break;
                case "20":
                    $timezone = "America/Argentina/Rio_Gallegos";
                    break;
                case "21":
                    $timezone = "America/Argentina/Buenos_Aires";
                    break;
                case "22":
                    $timezone = "America/Argentina/Catamarca";
                    break;
                case "23":
                    $timezone = "America/Argentina/Ushuaia";
                    break;
                case "24":
                    $timezone = "America/Argentina/Tucuman";
                    break;
        }
        break;
        case "AS":
            $timezone = "Pacific/Pago_Pago";
            break;
        case "AT":
            $timezone = "Europe/Vienna";
            break;
        case "AU":
            switch ($region) {
                case "01":
                    $timezone = "Australia/Sydney";
                    break;
                case "02":
                    $timezone = "Australia/Sydney";
                    break;
                case "03":
                    $timezone = "Australia/Darwin";
                    break;
                case "04":
                    $timezone = "Australia/Brisbane";
                    break;
                case "05":
                    $timezone = "Australia/Adelaide";
                    break;
                case "06":
                    $timezone = "Australia/Hobart";
                    break;
                case "07":
                    $timezone = "Australia/Melbourne";
                    break;
                case "08":
                    $timezone = "Australia/Perth";
                    break;
        }
        break;
        case "AW":
            $timezone = "America/Aruba";
            break;
        case "AX":
            $timezone = "Europe/Mariehamn";
            break;
        case "AZ":
            $timezone = "Asia/Baku";
            break;
        case "BA":
            $timezone = "Europe/Sarajevo";
            break;
        case "BB":
            $timezone = "America/Barbados";
            break;
        case "BD":
            $timezone = "Asia/Dhaka";
            break;
        case "BE":
            $timezone = "Europe/Brussels";
            break;
        case "BF":
            $timezone = "Africa/Ouagadougou";
            break;
        case "BG":
            $timezone = "Europe/Sofia";
            break;
        case "BH":
            $timezone = "Asia/Bahrain";
            break;
        case "BI":
            $timezone = "Africa/Bujumbura";
            break;
        case "BJ":
            $timezone = "Africa/Porto-Novo";
            break;
        case "BL":
            $timezone = "America/St_Barthelemy";
            break;
        case "BM":
            $timezone = "Atlantic/Bermuda";
            break;
        case "BN":
            $timezone = "Asia/Brunei";
            break;
        case "BO":
            $timezone = "America/La_Paz";
            break;
        case "BQ":
            $timezone = "America/Curacao";
            break;
        case "BR":
            switch ($region) {
                case "01":
                    $timezone = "America/Rio_Branco";
                    break;
                case "02":
                    $timezone = "America/Maceio";
                    break;
                case "03":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "04":
                    $timezone = "America/Manaus";
                    break;
                case "05":
                    $timezone = "America/Bahia";
                    break;
                case "06":
                    $timezone = "America/Fortaleza";
                    break;
                case "07":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "08":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "11":
                    $timezone = "America/Campo_Grande";
                    break;
                case "13":
                    $timezone = "America/Belem";
                    break;
                case "14":
                    $timezone = "America/Cuiaba";
                    break;
                case "15":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "16":
                    $timezone = "America/Belem";
                    break;
                case "17":
                    $timezone = "America/Recife";
                    break;
                case "18":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "20":
                    $timezone = "America/Fortaleza";
                    break;
                case "21":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "22":
                    $timezone = "America/Recife";
                    break;
                case "23":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "24":
                    $timezone = "America/Porto_Velho";
                    break;
                case "25":
                    $timezone = "America/Boa_Vista";
                    break;
                case "26":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "27":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "28":
                    $timezone = "America/Maceio";
                    break;
                case "29":
                    $timezone = "America/Sao_Paulo";
                    break;
                case "30":
                    $timezone = "America/Recife";
                    break;
                case "31":
                    $timezone = "America/Araguaina";
                    break;
        }
        break;
        case "BS":
            $timezone = "America/Nassau";
            break;
        case "BT":
            $timezone = "Asia/Thimphu";
            break;
        case "BV":
            $timezone = "Antarctica/Syowa";
            break;
        case "BW":
            $timezone = "Africa/Gaborone";
            break;
        case "BY":
            $timezone = "Europe/Minsk";
            break;
        case "BZ":
            $timezone = "America/Belize";
            break;
        case "CA":
            $region='AB';
            switch ($region) {
                case "AB":
                    $timezone = "America/Edmonton";
                    break;
                case "BC":
                    $timezone = "America/Vancouver";
                    break;
                case "MB":
                    $timezone = "America/Winnipeg";
                    break;
                case "NB":
                    $timezone = "America/Halifax";
                    break;
                case "NL":
                    $timezone = "America/St_Johns";
                    break;
                case "NS":
                    $timezone = "America/Halifax";
                    break;
                case "NT":
                    $timezone = "America/Yellowknife";
                    break;
                case "NU":
                    $timezone = "America/Rankin_Inlet";
                    break;
                case "ON":
                    $timezone = "America/Toronto";
                    break;
                case "PE":
                    $timezone = "America/Halifax";
                    break;
                case "QC":
                    $timezone = "America/Montreal";
                    break;
                case "SK":
                    $timezone = "America/Regina";
                    break;
                case "YT":
                    $timezone = "America/Whitehorse";
                    break;
        }
        break;
        case "CC":
            $timezone = "Indian/Cocos";
            break;
        case "CD":
            switch ($region) {
                case "01":
                    $timezone = "Africa/Kinshasa";
                    break;
                case "02":
                    $timezone = "Africa/Kinshasa";
                    break;
                case "03":
                    $timezone = "Africa/Kinshasa";
                    break;
                case "04":
                    $timezone = "Africa/Lubumbashi";
                    break;
                case "05":
                    $timezone = "Africa/Lubumbashi";
                    break;
                case "06":
                    $timezone = "Africa/Kinshasa";
                    break;
                case "07":
                    $timezone = "Africa/Lubumbashi";
                    break;
                case "08":
                    $timezone = "Africa/Kinshasa";
                    break;
                case "09":
                    $timezone = "Africa/Lubumbashi";
                    break;
                case "10":
                    $timezone = "Africa/Lubumbashi";
                    break;
                case "11":
                    $timezone = "Africa/Lubumbashi";
                    break;
                case "12":
                    $timezone = "Africa/Lubumbashi";
                    break;
        }
        break;
        case "CF":
            $timezone = "Africa/Bangui";
            break;
        case "CG":
            $timezone = "Africa/Brazzaville";
            break;
        case "CH":
            $timezone = "Europe/Zurich";
            break;
        case "CI":
            $timezone = "Africa/Abidjan";
            break;
        case "CK":
            $timezone = "Pacific/Rarotonga";
            break;
        case "CL":
            $timezone = "America/Santiago";
            break;
        case "CM":
            $timezone = "Africa/Lagos";
            break;
        case "CN":
            switch ($region) {
                case "01":
                    $timezone = "Asia/Shanghai";
                    break;
                case "02":
                    $timezone = "Asia/Shanghai";
                    break;
                case "03":
                    $timezone = "Asia/Shanghai";
                    break;
                case "04":
                    $timezone = "Asia/Shanghai";
                    break;
                case "05":
                    $timezone = "Asia/Harbin";
                    break;
                case "06":
                    $timezone = "Asia/Chongqing";
                    break;
                case "07":
                    $timezone = "Asia/Shanghai";
                    break;
                case "08":
                    $timezone = "Asia/Harbin";
                    break;
                case "09":
                    $timezone = "Asia/Shanghai";
                    break;
                case "10":
                    $timezone = "Asia/Shanghai";
                    break;
                case "11":
                    $timezone = "Asia/Chongqing";
                    break;
                case "12":
                    $timezone = "Asia/Shanghai";
                    break;
                case "13":
                    $timezone = "Asia/Urumqi";
                    break;
                case "14":
                    $timezone = "Asia/Chongqing";
                    break;
                case "15":
                    $timezone = "Asia/Chongqing";
                    break;
                case "16":
                    $timezone = "Asia/Chongqing";
                    break;
                case "18":
                    $timezone = "Asia/Chongqing";
                    break;
                case "19":
                    $timezone = "Asia/Harbin";
                    break;
                case "20":
                    $timezone = "Asia/Harbin";
                    break;
                case "21":
                    $timezone = "Asia/Chongqing";
                    break;
                case "22":
                    $timezone = "Asia/Harbin";
                    break;
                case "23":
                    $timezone = "Asia/Shanghai";
                    break;
                case "24":
                    $timezone = "Asia/Chongqing";
                    break;
                case "25":
                    $timezone = "Asia/Shanghai";
                    break;
                case "26":
                    $timezone = "Asia/Chongqing";
                    break;
                case "28":
                    $timezone = "Asia/Shanghai";
                    break;
                case "29":
                    $timezone = "Asia/Chongqing";
                    break;
                case "30":
                    $timezone = "Asia/Chongqing";
                    break;
                case "31":
                    $timezone = "Asia/Chongqing";
                    break;
                case "32":
                    $timezone = "Asia/Chongqing";
                    break;
                case "33":
                    $timezone = "Asia/Chongqing";
                    break;
        }
        break;
        case "CO":
            $timezone = "America/Bogota";
            break;
        case "CR":
            $timezone = "America/Costa_Rica";
            break;
        case "CU":
            $timezone = "America/Havana";
            break;
        case "CV":
            $timezone = "Atlantic/Cape_Verde";
            break;
        case "CW":
            $timezone = "America/Curacao";
            break;
        case "CX":
            $timezone = "Indian/Christmas";
            break;
        case "CY":
            $timezone = "Asia/Nicosia";
            break;
        case "CZ":
            $timezone = "Europe/Prague";
            break;
        case "DE":
            $timezone = "Europe/Berlin";
            break;
        case "DJ":
            $timezone = "Africa/Djibouti";
            break;
        case "DK":
            $timezone = "Europe/Copenhagen";
            break;
        case "DM":
            $timezone = "America/Dominica";
            break;
        case "DO":
            $timezone = "America/Santo_Domingo";
            break;
        case "DZ":
            $timezone = "Africa/Algiers";
            break;
        case "EC":
            switch ($region) {
                case "01":
                    $timezone = "Pacific/Galapagos";
                    break;
                case "02":
                    $timezone = "America/Guayaquil";
                    break;
                case "03":
                    $timezone = "America/Guayaquil";
                    break;
                case "04":
                    $timezone = "America/Guayaquil";
                    break;
                case "05":
                    $timezone = "America/Guayaquil";
                    break;
                case "06":
                    $timezone = "America/Guayaquil";
                    break;
                case "07":
                    $timezone = "America/Guayaquil";
                    break;
                case "08":
                    $timezone = "America/Guayaquil";
                    break;
                case "09":
                    $timezone = "America/Guayaquil";
                    break;
                case "10":
                    $timezone = "America/Guayaquil";
                    break;
                case "11":
                    $timezone = "America/Guayaquil";
                    break;
                case "12":
                    $timezone = "America/Guayaquil";
                    break;
                case "13":
                    $timezone = "America/Guayaquil";
                    break;
                case "14":
                    $timezone = "America/Guayaquil";
                    break;
                case "15":
                    $timezone = "America/Guayaquil";
                    break;
                case "17":
                    $timezone = "America/Guayaquil";
                    break;
                case "18":
                    $timezone = "America/Guayaquil";
                    break;
                case "19":
                    $timezone = "America/Guayaquil";
                    break;
                case "20":
                    $timezone = "America/Guayaquil";
                    break;
                case "22":
                    $timezone = "America/Guayaquil";
                    break;
                case "24":
                    $timezone = "America/Guayaquil";
                    break;
        }
        break;
        case "EE":
            $timezone = "Europe/Tallinn";
            break;
        case "EG":
            $timezone = "Africa/Cairo";
            break;
        case "EH":
            $timezone = "Africa/El_Aaiun";
            break;
        case "ER":
            $timezone = "Africa/Asmara";
            break;
        case "ES":
            $region='07';
            switch ($region) {
                case "07":
                    $timezone = "Europe/Madrid";
                    break;
                case "27":
                    $timezone = "Europe/Madrid";
                    break;
                case "29":
                    $timezone = "Europe/Madrid";
                    break;
                case "31":
                    $timezone = "Europe/Madrid";
                    break;
                case "32":
                    $timezone = "Europe/Madrid";
                    break;
                case "34":
                    $timezone = "Europe/Madrid";
                    break;
                case "39":
                    $timezone = "Europe/Madrid";
                    break;
                case "51":
                    $timezone = "Africa/Ceuta";
                    break;
                case "52":
                    $timezone = "Europe/Madrid";
                    break;
                case "53":
                    $timezone = "Atlantic/Canary";
                    break;
                case "54":
                    $timezone = "Europe/Madrid";
                    break;
                case "55":
                    $timezone = "Europe/Madrid";
                    break;
                case "56":
                    $timezone = "Europe/Madrid";
                    break;
                case "57":
                    $timezone = "Europe/Madrid";
                    break;
                case "58":
                    $timezone = "Europe/Madrid";
                    break;
                case "59":
                    $timezone = "Europe/Madrid";
                    break;
                case "60":
                    $timezone = "Europe/Madrid";
                    break;
        }
        break;
        case "ET":
            $timezone = "Africa/Addis_Ababa";
            break;
        case "FI":
            $timezone = "Europe/Helsinki";
            break;
        case "FJ":
            $timezone = "Pacific/Fiji";
            break;
        case "FK":
            $timezone = "Atlantic/Stanley";
            break;
        case "FM":
            $timezone = "Pacific/Pohnpei";
            break;
        case "FO":
            $timezone = "Atlantic/Faroe";
            break;
        case "FR":
            $timezone = "Europe/Paris";
            break;
        case "FX":
            $timezone = "Europe/Paris";
            break;
        case "GA":
            $timezone = "Africa/Libreville";
            break;
        case "GB":
            $timezone = "Europe/London";
            break;
        case "GD":
            $timezone = "America/Grenada";
            break;
        case "GE":
            $timezone = "Asia/Tbilisi";
            break;
        case "GF":
            $timezone = "America/Cayenne";
            break;
        case "GG":
            $timezone = "Europe/Guernsey";
            break;
        case "GH":
            $timezone = "Africa/Accra";
            break;
        case "GI":
            $timezone = "Europe/Gibraltar";
            break;
        case "GL":
            switch ($region) {
                case "01":
                    $timezone = "America/Thule";
                    break;
                case "02":
                    $timezone = "America/Godthab";
                    break;
                case "03":
                    $timezone = "America/Godthab";
                    break;
        }
        break;
        case "GM":
            $timezone = "Africa/Banjul";
            break;
        case "GN":
            $timezone = "Africa/Conakry";
            break;
        case "GP":
            $timezone = "America/Guadeloupe";
            break;
        case "GQ":
            $timezone = "Africa/Malabo";
            break;
        case "GR":
            $timezone = "Europe/Athens";
            break;
        case "GS":
            $timezone = "Atlantic/South_Georgia";
            break;
        case "GT":
            $timezone = "America/Guatemala";
            break;
        case "GU":
            $timezone = "Pacific/Guam";
            break;
        case "GW":
            $timezone = "Africa/Bissau";
            break;
        case "GY":
            $timezone = "America/Guyana";
            break;
        case "HK":
            $timezone = "Asia/Hong_Kong";
            break;
        case "HN":
            $timezone = "America/Tegucigalpa";
            break;
        case "HR":
            $timezone = "Europe/Zagreb";
            break;
        case "HT":
            $timezone = "America/Port-au-Prince";
            break;
        case "HU":
            $timezone = "Europe/Budapest";
            break;
        case "ID":
            switch ($region) {
                case "01":
                    $timezone = "Asia/Pontianak";
                    break;
                case "02":
                    $timezone = "Asia/Makassar";
                    break;
                case "03":
                    $timezone = "Asia/Jakarta";
                    break;
                case "04":
                    $timezone = "Asia/Jakarta";
                    break;
                case "05":
                    $timezone = "Asia/Jakarta";
                    break;
                case "06":
                    $timezone = "Asia/Jakarta";
                    break;
                case "07":
                    $timezone = "Asia/Jakarta";
                    break;
                case "08":
                    $timezone = "Asia/Jakarta";
                    break;
                case "09":
                    $timezone = "Asia/Jayapura";
                    break;
                case "10":
                    $timezone = "Asia/Jakarta";
                    break;
                case "11":
                    $timezone = "Asia/Pontianak";
                    break;
                case "12":
                    $timezone = "Asia/Makassar";
                    break;
                case "13":
                    $timezone = "Asia/Makassar";
                    break;
                case "14":
                    $timezone = "Asia/Makassar";
                    break;
                case "15":
                    $timezone = "Asia/Jakarta";
                    break;
                case "16":
                    $timezone = "Asia/Makassar";
                    break;
                case "17":
                    $timezone = "Asia/Makassar";
                    break;
                case "18":
                    $timezone = "Asia/Makassar";
                    break;
                case "19":
                    $timezone = "Asia/Pontianak";
                    break;
                case "20":
                    $timezone = "Asia/Makassar";
                    break;
                case "21":
                    $timezone = "Asia/Makassar";
                    break;
                case "22":
                    $timezone = "Asia/Makassar";
                    break;
                case "23":
                    $timezone = "Asia/Makassar";
                    break;
                case "24":
                    $timezone = "Asia/Jakarta";
                    break;
                case "25":
                    $timezone = "Asia/Pontianak";
                    break;
                case "26":
                    $timezone = "Asia/Pontianak";
                    break;
                case "28":
                    $timezone = "Asia/Jayapura";
                    break;
                case "29":
                    $timezone = "Asia/Makassar";
                    break;
                case "30":
                    $timezone = "Asia/Jakarta";
                    break;
                case "31":
                    $timezone = "Asia/Makassar";
                    break;
                case "32":
                    $timezone = "Asia/Jakarta";
                    break;
                case "33":
                    $timezone = "Asia/Jakarta";
                    break;
                case "34":
                    $timezone = "Asia/Makassar";
                    break;
                case "35":
                    $timezone = "Asia/Pontianak";
                    break;
                case "36":
                    $timezone = "Asia/Jayapura";
                    break;
                case "37":
                    $timezone = "Asia/Pontianak";
                    break;
                case "38":
                    $timezone = "Asia/Makassar";
                    break;
                case "39":
                    $timezone = "Asia/Jayapura";
                    break;
                case "40":
                    $timezone = "Asia/Pontianak";
                    break;
                case "41":
                    $timezone = "Asia/Makassar";
                    break;
        }
        break;
        case "IE":
            $timezone = "Europe/Dublin";
            break;
        case "IL":
            $timezone = "Asia/Jerusalem";
            break;
        case "IM":
            $timezone = "Europe/Isle_of_Man";
            break;
        case "IN":
            $timezone = "Asia/Kolkata";
            break;
        case "IO":
            $timezone = "Indian/Chagos";
            break;
        case "IQ":
            $timezone = "Asia/Baghdad";
            break;
        case "IR":
            $timezone = "Asia/Tehran";
            break;
        case "IS":
            $timezone = "Atlantic/Reykjavik";
            break;
        case "IT":
            $timezone = "Europe/Rome";
            break;
        case "JE":
            $timezone = "Europe/Jersey";
            break;
        case "JM":
            $timezone = "America/Jamaica";
            break;
        case "JO":
            $timezone = "Asia/Amman";
            break;
        case "JP":
            $timezone = "Asia/Tokyo";
            break;
        case "KE":
            $timezone = "Africa/Nairobi";
            break;
        case "KG":
            $timezone = "Asia/Bishkek";
            break;
        case "KH":
            $timezone = "Asia/Phnom_Penh";
            break;
        case "KI":
            $timezone = "Pacific/Tarawa";
            break;
        case "KM":
            $timezone = "Indian/Comoro";
            break;
        case "KN":
            $timezone = "America/St_Kitts";
            break;
        case "KP":
            $timezone = "Asia/Pyongyang";
            break;
        case "KR":
            $timezone = "Asia/Seoul";
            break;
        case "KW":
            $timezone = "Asia/Kuwait";
            break;
        case "KY":
            $timezone = "America/Cayman";
            break;
        case "KZ":
            switch ($region) {
                case "01":
                    $timezone = "Asia/Almaty";
                    break;
                case "02":
                    $timezone = "Asia/Almaty";
                    break;
                case "03":
                    $timezone = "Asia/Qyzylorda";
                    break;
                case "04":
                    $timezone = "Asia/Aqtobe";
                    break;
                case "05":
                    $timezone = "Asia/Qyzylorda";
                    break;
                case "06":
                    $timezone = "Asia/Aqtau";
                    break;
                case "07":
                    $timezone = "Asia/Oral";
                    break;
                case "08":
                    $timezone = "Asia/Qyzylorda";
                    break;
                case "09":
                    $timezone = "Asia/Aqtau";
                    break;
                case "10":
                    $timezone = "Asia/Qyzylorda";
                    break;
                case "11":
                    $timezone = "Asia/Almaty";
                    break;
                case "12":
                    $timezone = "Asia/Qyzylorda";
                    break;
                case "13":
                    $timezone = "Asia/Aqtobe";
                    break;
                case "14":
                    $timezone = "Asia/Qyzylorda";
                    break;
                case "15":
                    $timezone = "Asia/Almaty";
                    break;
                case "16":
                    $timezone = "Asia/Aqtobe";
                    break;
                case "17":
                    $timezone = "Asia/Almaty";
                    break;
        }
        break;
        case "LA":
            $timezone = "Asia/Vientiane";
            break;
        case "LB":
            $timezone = "Asia/Beirut";
            break;
        case "LC":
            $timezone = "America/St_Lucia";
            break;
        case "LI":
            $timezone = "Europe/Vaduz";
            break;
        case "LK":
            $timezone = "Asia/Colombo";
            break;
        case "LR":
            $timezone = "Africa/Monrovia";
            break;
        case "LS":
            $timezone = "Africa/Maseru";
            break;
        case "LT":
            $timezone = "Europe/Vilnius";
            break;
        case "LU":
            $timezone = "Europe/Luxembourg";
            break;
        case "LV":
            $timezone = "Europe/Riga";
            break;
        case "LY":
            $timezone = "Africa/Tripoli";
            break;
        case "MA":
            $timezone = "Africa/Casablanca";
            break;
        case "MC":
            $timezone = "Europe/Monaco";
            break;
        case "MD":
            $timezone = "Europe/Chisinau";
            break;
        case "ME":
            $timezone = "Europe/Podgorica";
            break;
        case "MF":
            $timezone = "America/Marigot";
            break;
        case "MG":
            $timezone = "Indian/Antananarivo";
            break;
        case "MH":
            $timezone = "Pacific/Kwajalein";
            break;
        case "MK":
            $timezone = "Europe/Skopje";
            break;
        case "ML":
            $timezone = "Africa/Bamako";
            break;
        case "MM":
            $timezone = "Asia/Rangoon";
            break;
        case "MN":
            $region='06';
            switch ($region) {
                case "06":
                    $timezone = "Asia/Choibalsan";
                    break;
                case "11":
                    $timezone = "Asia/Ulaanbaatar";
                    break;
                case "17":
                    $timezone = "Asia/Choibalsan";
                    break;
                case "19":
                    $timezone = "Asia/Hovd";
                    break;
                case "20":
                    $timezone = "Asia/Ulaanbaatar";
                    break;
                case "21":
                    $timezone = "Asia/Ulaanbaatar";
                    break;
                case "25":
                    $timezone = "Asia/Ulaanbaatar";
                    break;
        }
        break;
        case "MO":
            $timezone = "Asia/Macau";
            break;
        case "MP":
            $timezone = "Pacific/Saipan";
            break;
        case "MQ":
            $timezone = "America/Martinique";
            break;
        case "MR":
            $timezone = "Africa/Nouakchott";
            break;
        case "MS":
            $timezone = "America/Montserrat";
            break;
        case "MT":
            $timezone = "Europe/Malta";
            break;
        case "MU":
            $timezone = "Indian/Mauritius";
            break;
        case "MV":
            $timezone = "Indian/Maldives";
            break;
        case "MW":
            $timezone = "Africa/Blantyre";
            break;
        case "MX":
            switch ($region) {
                case "01":
                    $timezone = "America/Mexico_City";
                    break;
                case "02":
                    $timezone = "America/Tijuana";
                    break;
                case "03":
                    $timezone = "America/Hermosillo";
                    break;
                case "04":
                    $timezone = "America/Merida";
                    break;
                case "05":
                    $timezone = "America/Mexico_City";
                    break;
                case "06":
                    $timezone = "America/Chihuahua";
                    break;
                case "07":
                    $timezone = "America/Monterrey";
                    break;
                case "08":
                    $timezone = "America/Mexico_City";
                    break;
                case "09":
                    $timezone = "America/Mexico_City";
                    break;
                case "10":
                    $timezone = "America/Mazatlan";
                    break;
                case "11":
                    $timezone = "America/Mexico_City";
                    break;
                case "12":
                    $timezone = "America/Mexico_City";
                    break;
                case "13":
                    $timezone = "America/Mexico_City";
                    break;
                case "14":
                    $timezone = "America/Mazatlan";
                    break;
                case "15":
                    $timezone = "America/Chihuahua";
                    break;
                case "16":
                    $timezone = "America/Mexico_City";
                    break;
                case "17":
                    $timezone = "America/Mexico_City";
                    break;
                case "18":
                    $timezone = "America/Mazatlan";
                    break;
                case "19":
                    $timezone = "America/Monterrey";
                    break;
                case "20":
                    $timezone = "America/Mexico_City";
                    break;
                case "21":
                    $timezone = "America/Mexico_City";
                    break;
                case "22":
                    $timezone = "America/Mexico_City";
                    break;
                case "23":
                    $timezone = "America/Cancun";
                    break;
                case "24":
                    $timezone = "America/Mexico_City";
                    break;
                case "25":
                    $timezone = "America/Mazatlan";
                    break;
                case "26":
                    $timezone = "America/Hermosillo";
                    break;
                case "27":
                    $timezone = "America/Merida";
                    break;
                case "28":
                    $timezone = "America/Monterrey";
                    break;
                case "29":
                    $timezone = "America/Mexico_City";
                    break;
                case "30":
                    $timezone = "America/Mexico_City";
                    break;
                case "31":
                    $timezone = "America/Merida";
                    break;
                case "32":
                    $timezone = "America/Monterrey";
                    break;
        }
        break;
        case "MY":
            switch ($region) {
                case "01":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "02":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "03":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "04":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "05":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "06":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "07":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "08":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "09":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "11":
                    $timezone = "Asia/Kuching";
                    break;
                case "12":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "13":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "14":
                    $timezone = "Asia/Kuala_Lumpur";
                    break;
                case "15":
                    $timezone = "Asia/Kuching";
                    break;
                case "16":
                    $timezone = "Asia/Kuching";
                    break;
        }
        break;
        case "MZ":
            $timezone = "Africa/Maputo";
            break;
        case "NA":
            $timezone = "Africa/Windhoek";
            break;
        case "NC":
            $timezone = "Pacific/Noumea";
            break;
        case "NE":
            $timezone = "Africa/Niamey";
            break;
        case "NF":
            $timezone = "Pacific/Norfolk";
            break;
        case "NG":
            $timezone = "Africa/Lagos";
            break;
        case "NI":
            $timezone = "America/Managua";
            break;
        case "NL":
            $timezone = "Europe/Amsterdam";
            break;
        case "NO":
            $timezone = "Europe/Oslo";
            break;
        case "NP":
            $timezone = "Asia/Kathmandu";
            break;
        case "NR":
            $timezone = "Pacific/Nauru";
            break;
        case "NU":
            $timezone = "Pacific/Niue";
            break;
        case "NZ":
            $region='F1';
            switch ($region) {
                case "85":
                    $timezone = "Pacific/Auckland";
                    break;
                case "E7":
                    $timezone = "Pacific/Auckland";
                    break;
                case "E8":
                    $timezone = "Pacific/Auckland";
                    break;
                case "E9":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F1":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F2":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F3":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F4":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F5":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F6":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F7":
                    $timezone = "Pacific/Chatham";
                    break;
                case "F8":
                    $timezone = "Pacific/Auckland";
                    break;
                case "F9":
                    $timezone = "Pacific/Auckland";
                    break;
                case "G1":
                    $timezone = "Pacific/Auckland";
                    break;
                case "G2":
                    $timezone = "Pacific/Auckland";
                    break;
                case "G3":
                    $timezone = "Pacific/Auckland";
                    break;
        }
        break;
        case "OM":
            $timezone = "Asia/Muscat";
            break;
        case "PA":
            $timezone = "America/Panama";
            break;
        case "PE":
            $timezone = "America/Lima";
            break;
        case "PF":
            $timezone = "Pacific/Marquesas";
            break;
        case "PG":
            $timezone = "Pacific/Port_Moresby";
            break;
        case "PH":
            $timezone = "Asia/Manila";
            break;
        case "PK":
            $timezone = "Asia/Karachi";
            break;
        case "PL":
            $timezone = "Europe/Warsaw";
            break;
        case "PM":
            $timezone = "America/Miquelon";
            break;
        case "PN":
            $timezone = "Pacific/Pitcairn";
            break;
        case "PR":
            $timezone = "America/Puerto_Rico";
            break;
        case "PS":
            $timezone = "Asia/Gaza";
            break;
        case "PT":
            $region="02";
            switch ($region) {
                case "02":
                    $timezone = "Europe/Lisbon";
                    break;
                case "03":
                    $timezone = "Europe/Lisbon";
                    break;
                case "04":
                    $timezone = "Europe/Lisbon";
                    break;
                case "05":
                    $timezone = "Europe/Lisbon";
                    break;
                case "06":
                    $timezone = "Europe/Lisbon";
                    break;
                case "07":
                    $timezone = "Europe/Lisbon";
                    break;
                case "08":
                    $timezone = "Europe/Lisbon";
                    break;
                case "09":
                    $timezone = "Europe/Lisbon";
                    break;
                case "10":
                    $timezone = "Atlantic/Madeira";
                    break;
                case "11":
                    $timezone = "Europe/Lisbon";
                    break;
                case "13":
                    $timezone = "Europe/Lisbon";
                    break;
                case "14":
                    $timezone = "Europe/Lisbon";
                    break;
                case "16":
                    $timezone = "Europe/Lisbon";
                    break;
                case "17":
                    $timezone = "Europe/Lisbon";
                    break;
                case "18":
                    $timezone = "Europe/Lisbon";
                    break;
                case "19":
                    $timezone = "Europe/Lisbon";
                    break;
                case "20":
                    $timezone = "Europe/Lisbon";
                    break;
                case "21":
                    $timezone = "Europe/Lisbon";
                    break;
                case "22":
                    $timezone = "Europe/Lisbon";
                    break;
                case "23":
                    $timezone = "Atlantic/Azores";
                    break;
        }
        break;
        case "PW":
            $timezone = "Pacific/Palau";
            break;
        case "PY":
            $timezone = "America/Asuncion";
            break;
        case "QA":
            $timezone = "Asia/Qatar";
            break;
        case "RE":
            $timezone = "Indian/Reunion";
            break;
        case "RO":
            $timezone = "Europe/Bucharest";
            break;
        case "RS":
            $timezone = "Europe/Belgrade";
            break;
        case "RU":
            switch ($region) {
                case "01":
                    $timezone = "Europe/Volgograd";
                    break;
                case "02":
                    $timezone = "Asia/Irkutsk";
                    break;
                case "03":
                    $timezone = "Asia/Novokuznetsk";
                    break;
                case "04":
                    $timezone = "Asia/Novosibirsk";
                    break;
                case "05":
                    $timezone = "Asia/Vladivostok";
                    break;
                case "06":
                    $timezone = "Europe/Moscow";
                    break;
                case "07":
                    $timezone = "Europe/Volgograd";
                    break;
                case "08":
                    $timezone = "Europe/Samara";
                    break;
                case "09":
                    $timezone = "Europe/Moscow";
                    break;
                case "10":
                    $timezone = "Europe/Moscow";
                    break;
                case "11":
                    $timezone = "Asia/Irkutsk";
                    break;
                case "12":
                    $timezone = "Europe/Volgograd";
                    break;
                case "13":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "14":
                    $timezone = "Asia/Irkutsk";
                    break;
                case "15":
                    $timezone = "Asia/Anadyr";
                    break;
                case "16":
                    $timezone = "Europe/Samara";
                    break;
                case "17":
                    $timezone = "Europe/Volgograd";
                    break;
                case "18":
                    $timezone = "Asia/Krasnoyarsk";
                    break;
                case "20":
                    $timezone = "Asia/Irkutsk";
                    break;
                case "21":
                    $timezone = "Europe/Moscow";
                    break;
                case "22":
                    $timezone = "Europe/Volgograd";
                    break;
                case "23":
                    $timezone = "Europe/Kaliningrad";
                    break;
                case "24":
                    $timezone = "Europe/Volgograd";
                    break;
                case "25":
                    $timezone = "Europe/Moscow";
                    break;
                case "26":
                    $timezone = "Asia/Kamchatka";
                    break;
                case "27":
                    $timezone = "Europe/Volgograd";
                    break;
                case "28":
                    $timezone = "Europe/Moscow";
                    break;
                case "29":
                    $timezone = "Asia/Novokuznetsk";
                    break;
                case "30":
                    $timezone = "Asia/Vladivostok";
                    break;
                case "31":
                    $timezone = "Asia/Krasnoyarsk";
                    break;
                case "32":
                    $timezone = "Asia/Omsk";
                    break;
                case "33":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "34":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "35":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "36":
                    $timezone = "Asia/Anadyr";
                    break;
                case "37":
                    $timezone = "Europe/Moscow";
                    break;
                case "38":
                    $timezone = "Europe/Volgograd";
                    break;
                case "39":
                    $timezone = "Asia/Krasnoyarsk";
                    break;
                case "40":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "41":
                    $timezone = "Europe/Moscow";
                    break;
                case "42":
                    $timezone = "Europe/Moscow";
                    break;
                case "43":
                    $timezone = "Europe/Moscow";
                    break;
                case "44":
                    $timezone = "Asia/Magadan";
                    break;
                case "45":
                    $timezone = "Europe/Samara";
                    break;
                case "46":
                    $timezone = "Europe/Samara";
                    break;
                case "47":
                    $timezone = "Europe/Moscow";
                    break;
                case "48":
                    $timezone = "Europe/Moscow";
                    break;
                case "49":
                    $timezone = "Europe/Moscow";
                    break;
                case "50":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "51":
                    $timezone = "Europe/Moscow";
                    break;
                case "52":
                    $timezone = "Europe/Moscow";
                    break;
                case "53":
                    $timezone = "Asia/Novosibirsk";
                    break;
                case "54":
                    $timezone = "Asia/Omsk";
                    break;
                case "55":
                    $timezone = "Europe/Samara";
                    break;
                case "56":
                    $timezone = "Europe/Moscow";
                    break;
                case "57":
                    $timezone = "Europe/Samara";
                    break;
                case "58":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "59":
                    $timezone = "Asia/Vladivostok";
                    break;
                case "60":
                    $timezone = "Europe/Kaliningrad";
                    break;
                case "61":
                    $timezone = "Europe/Volgograd";
                    break;
                case "62":
                    $timezone = "Europe/Moscow";
                    break;
                case "63":
                    $timezone = "Asia/Yakutsk";
                    break;
                case "64":
                    $timezone = "Asia/Sakhalin";
                    break;
                case "65":
                    $timezone = "Europe/Samara";
                    break;
                case "66":
                    $timezone = "Europe/Moscow";
                    break;
                case "67":
                    $timezone = "Europe/Samara";
                    break;
                case "68":
                    $timezone = "Europe/Volgograd";
                    break;
                case "69":
                    $timezone = "Europe/Moscow";
                    break;
                case "70":
                    $timezone = "Europe/Volgograd";
                    break;
                case "71":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "72":
                    $timezone = "Europe/Moscow";
                    break;
                case "73":
                    $timezone = "Europe/Samara";
                    break;
                case "74":
                    $timezone = "Asia/Krasnoyarsk";
                    break;
                case "75":
                    $timezone = "Asia/Novosibirsk";
                    break;
                case "76":
                    $timezone = "Europe/Moscow";
                    break;
                case "77":
                    $timezone = "Europe/Moscow";
                    break;
                case "78":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "79":
                    $timezone = "Asia/Irkutsk";
                    break;
                case "80":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "81":
                    $timezone = "Europe/Samara";
                    break;
                case "82":
                    $timezone = "Asia/Irkutsk";
                    break;
                case "83":
                    $timezone = "Europe/Moscow";
                    break;
                case "84":
                    $timezone = "Europe/Volgograd";
                    break;
                case "85":
                    $timezone = "Europe/Moscow";
                    break;
                case "86":
                    $timezone = "Europe/Moscow";
                    break;
                case "87":
                    $timezone = "Asia/Novosibirsk";
                    break;
                case "88":
                    $timezone = "Europe/Moscow";
                    break;
                case "89":
                    $timezone = "Asia/Vladivostok";
                    break;
                case "90":
                    $timezone = "Asia/Yekaterinburg";
                    break;
                case "91":
                    $timezone = "Asia/Krasnoyarsk";
                    break;
                case "92":
                    $timezone = "Asia/Anadyr";
                    break;
                case "93":
                    $timezone = "Asia/Irkutsk";
                    break;
        }
        break;
        case "RW":
            $timezone = "Africa/Kigali";
            break;
        case "SA":
            $timezone = "Asia/Riyadh";
            break;
        case "SB":
            $timezone = "Pacific/Guadalcanal";
            break;
        case "SC":
            $timezone = "Indian/Mahe";
            break;
        case "SD":
            $timezone = "Africa/Khartoum";
            break;
        case "SE":
            $timezone = "Europe/Stockholm";
            break;
        case "SG":
            $timezone = "Asia/Singapore";
            break;
        case "SH":
            $timezone = "Atlantic/St_Helena";
            break;
        case "SI":
            $timezone = "Europe/Ljubljana";
            break;
        case "SJ":
            $timezone = "Arctic/Longyearbyen";
            break;
        case "SK":
            $timezone = "Europe/Bratislava";
            break;
        case "SL":
            $timezone = "Africa/Freetown";
            break;
        case "SM":
            $timezone = "Europe/San_Marino";
            break;
        case "SN":
            $timezone = "Africa/Dakar";
            break;
        case "SO":
            $timezone = "Africa/Mogadishu";
            break;
        case "SR":
            $timezone = "America/Paramaribo";
            break;
        case "SS":
            $timezone = "Africa/Juba";
            break;
        case "ST":
            $timezone = "Africa/Sao_Tome";
            break;
        case "SV":
            $timezone = "America/El_Salvador";
            break;
        case "SX":
            $timezone = "America/Curacao";
            break;
        case "SY":
            $timezone = "Asia/Damascus";
            break;
        case "SZ":
            $timezone = "Africa/Mbabane";
            break;
        case "TC":
            $timezone = "America/Grand_Turk";
            break;
        case "TD":
            $timezone = "Africa/Ndjamena";
            break;
        case "TF":
            $timezone = "Indian/Kerguelen";
            break;
        case "TG":
            $timezone = "Africa/Lome";
            break;
        case "TH":
            $timezone = "Asia/Bangkok";
            break;
        case "TJ":
            $timezone = "Asia/Dushanbe";
            break;
        case "TK":
            $timezone = "Pacific/Fakaofo";
            break;
        case "TL":
            $timezone = "Asia/Dili";
            break;
        case "TM":
            $timezone = "Asia/Ashgabat";
            break;
        case "TN":
            $timezone = "Africa/Tunis";
            break;
        case "TO":
            $timezone = "Pacific/Tongatapu";
            break;
        case "TR":
            $timezone = "Asia/Istanbul";
            break;
        case "TT":
            $timezone = "America/Port_of_Spain";
            break;
        case "TV":
            $timezone = "Pacific/Funafuti";
            break;
        case "TW":
            $timezone = "Asia/Taipei";
            break;
        case "TZ":
            $timezone = "Africa/Dar_es_Salaam";
            break;
        case "UA":
            switch ($region) {
                case "01":
                    $timezone = "Europe/Kiev";
                    break;
                case "02":
                    $timezone = "Europe/Kiev";
                    break;
                case "03":
                    $timezone = "Europe/Uzhgorod";
                    break;
                case "04":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "05":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "06":
                    $timezone = "Europe/Uzhgorod";
                    break;
                case "07":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "08":
                    $timezone = "Europe/Simferopol";
                    break;
                case "09":
                    $timezone = "Europe/Kiev";
                    break;
                case "10":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "11":
                    $timezone = "Europe/Simferopol";
                    break;
                case "12":
                    $timezone = "Europe/Kiev";
                    break;
                case "13":
                    $timezone = "Europe/Kiev";
                    break;
                case "14":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "15":
                    $timezone = "Europe/Uzhgorod";
                    break;
                case "16":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "17":
                    $timezone = "Europe/Simferopol";
                    break;
                case "18":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "19":
                    $timezone = "Europe/Kiev";
                    break;
                case "20":
                    $timezone = "Europe/Simferopol";
                    break;
                case "21":
                    $timezone = "Europe/Kiev";
                    break;
                case "22":
                    $timezone = "Europe/Uzhgorod";
                    break;
                case "23":
                    $timezone = "Europe/Kiev";
                    break;
                case "24":
                    $timezone = "Europe/Uzhgorod";
                    break;
                case "25":
                    $timezone = "Europe/Uzhgorod";
                    break;
                case "26":
                    $timezone = "Europe/Zaporozhye";
                    break;
                case "27":
                    $timezone = "Europe/Kiev";
                    break;
        }
        break;
        case "UG":
            $timezone = "Africa/Kampala";
            break;
        case "UM":
            $timezone = "Pacific/Wake";
            break;
        case "US":
            $region="MA";
            switch ($region) {
                case "AK":
                    $timezone = "America/Anchorage";
                    break;
                case "AL":
                    $timezone = "America/Chicago";
                    break;
                case "AR":
                    $timezone = "America/Chicago";
                    break;
                case "AZ":
                    $timezone = "America/Phoenix";
                    break;
                case "CA":
                    $timezone = "America/Los_Angeles";
                    break;
                case "CO":
                    $timezone = "America/Denver";
                    break;
                case "CT":
                    $timezone = "America/New_York";
                    break;
                case "DC":
                    $timezone = "America/New_York";
                    break;
                case "DE":
                    $timezone = "America/New_York";
                    break;
                case "FL":
                    $timezone = "America/New_York";
                    break;
                case "GA":
                    $timezone = "America/New_York";
                    break;
                case "HI":
                    $timezone = "Pacific/Honolulu";
                    break;
                case "IA":
                    $timezone = "America/Chicago";
                    break;
                case "ID":
                    $timezone = "America/Denver";
                    break;
                case "IL":
                    $timezone = "America/Chicago";
                    break;
                case "IN":
                    $timezone = "America/Indiana/Indianapolis";
                    break;
                case "KS":
                    $timezone = "America/Chicago";
                    break;
                case "KY":
                    $timezone = "America/New_York";
                    break;
                case "LA":
                    $timezone = "America/Chicago";
                    break;
                case "MA":
                    $timezone = "America/New_York";
                    break;
                case "MD":
                    $timezone = "America/New_York";
                    break;
                case "ME":
                    $timezone = "America/New_York";
                    break;
                case "MI":
                    $timezone = "America/New_York";
                    break;
                case "MN":
                    $timezone = "America/Chicago";
                    break;
                case "MO":
                    $timezone = "America/Chicago";
                    break;
                case "MS":
                    $timezone = "America/Chicago";
                    break;
                case "MT":
                    $timezone = "America/Denver";
                    break;
                case "NC":
                    $timezone = "America/New_York";
                    break;
                case "ND":
                    $timezone = "America/Chicago";
                    break;
                case "NE":
                    $timezone = "America/Chicago";
                    break;
                case "NH":
                    $timezone = "America/New_York";
                    break;
                case "NJ":
                    $timezone = "America/New_York";
                    break;
                case "NM":
                    $timezone = "America/Denver";
                    break;
                case "NV":
                    $timezone = "America/Los_Angeles";
                    break;
                case "NY":
                    $timezone = "America/New_York";
                    break;
                case "OH":
                    $timezone = "America/New_York";
                    break;
                case "OK":
                    $timezone = "America/Chicago";
                    break;
                case "OR":
                    $timezone = "America/Los_Angeles";
                    break;
                case "PA":
                    $timezone = "America/New_York";
                    break;
                case "RI":
                    $timezone = "America/New_York";
                    break;
                case "SC":
                    $timezone = "America/New_York";
                    break;
                case "SD":
                    $timezone = "America/Chicago";
                    break;
                case "TN":
                    $timezone = "America/Chicago";
                    break;
                case "TX":
                    $timezone = "America/Chicago";
                    break;
                case "UT":
                    $timezone = "America/Denver";
                    break;
                case "VA":
                    $timezone = "America/New_York";
                    break;
                case "VT":
                    $timezone = "America/New_York";
                    break;
                case "WA":
                    $timezone = "America/Los_Angeles";
                    break;
                case "WI":
                    $timezone = "America/Chicago";
                    break;
                case "WV":
                    $timezone = "America/New_York";
                    break;
                case "WY":
                    $timezone = "America/Denver";
                    break;
        }
        break;
        case "UY":
            $timezone = "America/Montevideo";
            break;
        case "UZ":
            switch ($region) {
                case "01":
                    $timezone = "Asia/Tashkent";
                    break;
                case "02":
                    $timezone = "Asia/Samarkand";
                    break;
                case "03":
                    $timezone = "Asia/Tashkent";
                    break;
                case "05":
                    $timezone = "Asia/Samarkand";
                    break;
                case "06":
                    $timezone = "Asia/Tashkent";
                    break;
                case "07":
                    $timezone = "Asia/Samarkand";
                    break;
                case "08":
                    $timezone = "Asia/Samarkand";
                    break;
                case "09":
                    $timezone = "Asia/Samarkand";
                    break;
                case "10":
                    $timezone = "Asia/Samarkand";
                    break;
                case "12":
                    $timezone = "Asia/Samarkand";
                    break;
                case "13":
                    $timezone = "Asia/Tashkent";
                    break;
                case "14":
                    $timezone = "Asia/Tashkent";
                    break;
        }
        break;
        case "VA":
            $timezone = "Europe/Vatican";
            break;
        case "VC":
            $timezone = "America/St_Vincent";
            break;
        case "VE":
            $timezone = "America/Caracas";
            break;
        case "VG":
            $timezone = "America/Tortola";
            break;
        case "VI":
            $timezone = "America/St_Thomas";
            break;
        case "VN":
            $timezone = "Asia/Phnom_Penh";
            break;
        case "VU":
            $timezone = "Pacific/Efate";
            break;
        case "WF":
            $timezone = "Pacific/Wallis";
            break;
        case "WS":
            $timezone = "Pacific/Pago_Pago";
            break;
        case "YE":
            $timezone = "Asia/Aden";
            break;
        case "YT":
            $timezone = "Indian/Mayotte";
            break;
        case "YU":
            $timezone = "Europe/Belgrade";
            break;
        case "ZA":
            $timezone = "Africa/Johannesburg";
            break;
        case "ZM":
            $timezone = "Africa/Lusaka";
            break;
        case "ZW":
            $timezone = "Africa/Harare";
            break;
    }
    return $timezone;
}
function wsmUrlToPostid( $url ) {
    // Try the core function
    $post_id = url_to_postid( $url );
    if ( $post_id == 0 ) {
        // Try custom post types
        $cpts = get_post_types(array(),'object');
        $siteURL=str_replace('www.','',site_url());
        // Get path from URL
        $url=str_replace($siteURL,"",$url);
        $url_parts = explode( '/', trim( $url, '/' ) );
       // echo parse_url($url)['path'];
        $url_parts = array_splice( $url_parts, 3 );
        $path = implode( '/', $url_parts );
        // Test against each CPT's rewrite slug
        foreach ( $cpts as $cpt_name => $cpt ) {
            $cpt_slug = $cpt->rewrite['slug'];
            if($cpt_slug==''){
                continue;
            }
            if ( strlen( $path ) > strlen( $cpt_slug ) && substr( $path, 0, strlen( $cpt_slug ) ) == $cpt_slug ) {
                $slug = substr( $path, strlen( $cpt_slug ) );
                $query = new WP_Query( array(
                    'post_type'         => $cpt_name,
                    'name'              => $slug,
                    'posts_per_page'    => 1
                ));
                if ( is_object( $query->post ) )
                    $post_id = $query->post->ID;
            }
        }
    }
    return $post_id;
}
function wsmFnGetIPAddress(){
    if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
function wsmFnSendCurlRequest($url){
    $jsonData='';
    $objLocation=null;
   	
	$response = wp_remote_get($url);
 
	if ( is_array( $response ) && ! is_wp_error( $response ) ) {
		$headers = $response['headers']; // array of http header lines
		$body    = $response['body']; // use the content
	}
	$body = isset($body) ? json_decode($body) : '';
    return $body;
	
}
function wsmFnIsExtensionLoaded($extension_name){
    return extension_loaded($extension_name);
}
function wsmFnGetLocationInfo($ip){        
    $infoURL='http://geoip-db.com/json/';
    if($ip!=''){
        $infoURL.=$ip;
    }
    $infoResult=wsmFnSendCurlRequest($infoURL);
    if(empty($infoResult)){
            return null;
    }
    return $infoResult;
}
function wsmFnIsCrossDomain($url){
    $homeURL=str_replace('www.','',site_url());
    $arrHome=parse_url($homeURL);
    $arrURL=parse_url($url);   
    if($arrHome['host']!=$arrURL['host']){
        return true;
    }
    return false;
}
function wsmFnGetYearDropDown($name,$fromDate,$selected=''){
    $firstVisitDate=$fromDate;
    //$firstVisitDate='2015-03-22';        
   // $diffInYears=wsmDateDifference($firstVisitDate,'years');
    $currentYear=wsmGetCurrentDateByTimeZone('Y');
    if($selected==''){
        $selected=$currentYear;
    }
    $yearDropDown="<select class='wsmSelectBox' name='{$name}' id='{$name}'>";
    for($cy=wsmGetDateByTimeStamp('Y',strtotime($firstVisitDate));$cy<=$currentYear;$cy++){
        $s='';
        if($cy==$selected){
            $s="selected='selected'";
        }
        $yearDropDown.="<option {$s} value='{$cy}'>{$cy}</option>";
    }
    $yearDropDown.="</select>";
    return $yearDropDown;
}
function wsmFnGetMonthDropDown($name,$selected='',$selectedYear=''){
     $currentYearMonth=wsmGetCurrentDateByTimeZone('Y-F-m');     
     $arrYearMonth=explode('-',$currentYearMonth);
     if($selected==''){
        $selected=$arrYearMonth[1];
     }
     if($selectedYear==''){
        $selectedYear=$arrYearMonth[0];
    }
     $arrMonths=array('01'=>__('January','wp-stats-manager'),'02'=>__('February','wp-stats-manager'),'03'=>__('March','wp-stats-manager'),'04'=>__('April','wp-stats-manager'),'05'=>__('May','wp-stats-manager'),'06'=>__('June','wp-stats-manager'),'07'=>__('July','wp-stats-manager'),'08'=>__('August','wp-stats-manager'),'09'=>__('September','wp-stats-manager'),'10'=>__('October','wp-stats-manager'),'11'=>__('November','wp-stats-manager'),'12'=>__('December','wp-stats-manager'));
     $index=0;    
     $monthDropDown="<select class='wsmSelectBox' name='{$name}' id='{$name}'>"; 
     foreach($arrMonths as $key=>$month){
        $s=$d='';
        if($key==$selected){
            $s="selected='selected'";
        }
        if($selectedYear==$arrYearMonth[0] && $index>=intVal($arrYearMonth[2])){
           $d="disabled='disabled'"; 
        }
        $monthDropDown.="<option {$s} value='{$key}' {$d}>{$month}</option>";
        $index++;
     }     
     $monthDropDown.="</select>";
     return $monthDropDown;    
}
function wsmFnGetFilterPostData(){
    global $arrCashedStats;
    $prefix=WSM_PREFIX;
    if(isset($arrCashedStats[WSM_PREFIX.'FilterPostData'])){
        return $arrCashedStats[WSM_PREFIX.'FilterPostData'];
    }
    $arrPostData=array("{$prefix}FilterType"=>'Hourly',"{$prefix}TxtHourlyNormalDate"=>wsmGetCurrentDateByTimeZone('Y-m-d'),"{$prefix}TxtHourlyRangeFromDate"=>'',"{$prefix}TxtHourlyRangeToDate"=>'',"{$prefix}TxtHourlyCompareFirstDate"=>'',"{$prefix}TxtHourlyCompareSecondDate"=>'',"{$prefix}SelectYear"=>'',"{$prefix}SelectMonth"=>'',"{$prefix}SelectFromYear"=>'',"{$prefix}SelectFromMonth"=>'',"{$prefix}SelectToYear"=>'',"{$prefix}SelectToMonth"=>'',"{$prefix}SelectFirstYear"=>'',"{$prefix}SelectFirstMonth"=>'',"{$prefix}SelectSecondYear"=>'',"{$prefix}SelectSecondMonth"=>'',"{$prefix}SelectMonthlyYear"=>'',"{$prefix}SelectMonthlyFromYear"=>'',"{$prefix}SelectMonthlyToYear"=>'',"{$prefix}SelectMonthlyFirstYear"=>'',"{$prefix}SelectMonthlySeondYear"=>'',"{$prefix}FilterWay"=>'Normal');
    if(isset($_REQUEST["{$prefix}FilterSubmit"]) || isset($_REQUEST["{$prefix}p"])){
        foreach($arrPostData as $key=>$value){
            $arrPostData[$key]=(isset($_REQUEST[$key])&& $_REQUEST[$key])!=''?$_REQUEST[$key]:$arrPostData[$key];
        }
    }   
    return $arrPostData;
}
function wsmSanitizeFilteredPostData($hide=''){
    global $arrCashedStats;
    $arrPostData=wsmFnGetFilterPostData();    
    $arrSanitize=array('filterType'=>$arrPostData[WSM_PREFIX.'FilterType'],'filterWay'=>$arrPostData[WSM_PREFIX.'FilterWay']);    
    $prefix=WSM_PREFIX;
    if(isset($arrCashedStats[WSM_PREFIX.'SanitizeFilterPostData'])){        
        return $arrCashedStats[WSM_PREFIX.'SanitizeFilterPostData'];
    }
    switch($arrPostData[WSM_PREFIX.'FilterType']){
        case 'Hourly':
            switch($arrPostData[WSM_PREFIX.'FilterWay']){
                case 'Normal':
                   $arrSanitize['Normal']=array('from'=>$arrPostData[WSM_PREFIX.'TxtHourlyNormalDate'], 'to'=>$arrPostData[WSM_PREFIX.'TxtHourlyNormalDate'],'first'=>'','second'=>''); 
                break;
                case 'Range':                    
                    if($arrPostData[WSM_PREFIX.'TxtHourlyRangeFromDate']>$arrPostData[WSM_PREFIX.'TxtHourlyRangeToDate']){
                         $temp=$arrPostData[WSM_PREFIX.'TxtHourlyRangeFromDate'];
                         $arrPostData[WSM_PREFIX.'TxtHourlyRangeFromDate']=$arrPostData[WSM_PREFIX.'TxtHourlyRangeToDate'];
                         $arrPostData[WSM_PREFIX.'TxtHourlyRangeToDate']=$temp;
                     } 
                    $arrSanitize['Range']=array('from'=>$arrPostData[WSM_PREFIX.'TxtHourlyRangeFromDate'],'to'=>$arrPostData[WSM_PREFIX.'TxtHourlyRangeToDate'],'first'=>'','second'=>'');
                break;
                case 'Compare':
                    $arrSanitize['Compare']=array('first'=>$arrPostData[WSM_PREFIX.'TxtHourlyCompareFirstDate'],'second'=>$arrPostData[WSM_PREFIX.'TxtHourlyCompareSecondDate'],'from'=>'','to'=>'');
                break;
            }
        break;
        case 'Daily':
            switch($arrPostData[WSM_PREFIX.'FilterWay']){
                case 'Normal':
                    $f=$arrPostData[WSM_PREFIX.'SelectYear'].'-'.$arrPostData[WSM_PREFIX.'SelectMonth'];
                    
                   $arrSanitize['Normal']=array('from'=>$f,'to'=>'','first'=>'','second'=>''); 
                break;
                case 'Range':
                    $f=$arrPostData[WSM_PREFIX.'SelectFromYear'].'-'.$arrPostData[WSM_PREFIX.'SelectFromMonth'].'-01';
                    $t=$arrPostData[WSM_PREFIX.'SelectToYear'].'-'.$arrPostData[WSM_PREFIX.'SelectToMonth'].'-01';
                    if($f > $t){
                         $temp=$arrPostData[WSM_PREFIX.'SelectFromYear'].'-'.$arrPostData[WSM_PREFIX.'SelectFromMonth'];
                         $f=$arrPostData[WSM_PREFIX.'SelectToYear'].'-'.$arrPostData[WSM_PREFIX.'SelectToMonth'];
                         $t=$temp;
                     }else{
                        $f=$arrPostData[WSM_PREFIX.'SelectFromYear'].'-'.$arrPostData[WSM_PREFIX.'SelectFromMonth'];
                        $t=$arrPostData[WSM_PREFIX.'SelectToYear'].'-'.$arrPostData[WSM_PREFIX.'SelectToMonth']; 
                     }                    
                   $arrSanitize['Range']=array('from'=>$f,'to'=>$t,'first'=>'','second'=>'');                      
                break;
                case 'Compare':
                    $f=$arrPostData[WSM_PREFIX.'SelectFirstYear'].'-'.$arrPostData[WSM_PREFIX.'SelectFirstMonth'];
                    $t=$arrPostData[WSM_PREFIX.'SelectSecondYear'].'-'.$arrPostData[WSM_PREFIX.'SelectSecondMonth'];
                   $arrSanitize['Compare']=array('first'=>$f,'second'=>$t,'from'=>'','to'=>'');  
                break;
            }
        break;
        case 'Monthly':
            switch($arrPostData[WSM_PREFIX.'FilterWay']){
                case 'Normal':
                    $f=$arrPostData[WSM_PREFIX.'SelectMonthlyYear'];
                   $arrSanitize['Normal']=array('from'=>$f,'to'=>'','first'=>'','second'=>''); 
                break;
                case 'Range':
                    $f=$arrPostData[WSM_PREFIX.'SelectMonthlyFromYear'];
                    $t=$arrPostData[WSM_PREFIX.'SelectMonthlyToYear'];
                    if($f > $t){
                         $temp=$arrPostData[WSM_PREFIX.'SelectMonthlyFromYear'];
                         $f=$t;
                         $t=$temp;
                     }
                   $arrSanitize['Range']=array('from'=>$f,'to'=>$t,'first'=>'','second'=>'');                      
                break;
                case 'Compare':
                    $f=$arrPostData[WSM_PREFIX.'SelectMonthlyFirstYear'];
                    $t=$arrPostData[WSM_PREFIX.'SelectMonthlySeondYear'];
                   $arrSanitize['Compare']=array('first'=>$f,'second'=>$t,'from'=>'','to'=>''); 
                break;
            }
        break;
    }
    $arrCashedStats[WSM_PREFIX.'SanitizeFilterPostData']=$arrSanitize;
    return $arrSanitize;
}
function wsmFnStatCalculations($type, $pageViews, $firstTimeVisitors, $visitors){
    $arrReturn= @array('ppv'=>0,'newVisitor'=>0,'avgOnline'=>0);
    if($pageViews>0 && $visitors>0 ){
        $arrReturn['ppv']=$pageViews/$visitors;
    }
    if($firstTimeVisitors>0 && $visitors>0){
        $arrReturn['newVisitor']=($firstTimeVisitors/$visitors)*100;
    }
    $factor=1;
    if($type=='Daily'){
       $factor=24; 
    }
    if($type=='Monthly'){
       $factor=24*30; 
    }
    if($visitors>0){
        $arrReturn['avgOnline']=($visitors/($factor*60))*WSM_ONLINE_SESSION;
    }
    return $arrReturn;
}
function wsmGetMaxValueFromArray($arrMulti){
    return max($arrMulti);
}
function wsmFNUpdateLogFile($type,$data){
    $fileName=WSM_DIR.'log.txt';
    file_put_contents($fileName,"[".wsmGetCurrentDateByTimeZone()."] ".$type.PHP_EOL,FILE_APPEND);
    file_put_contents($fileName,"==============================================================".PHP_EOL,FILE_APPEND);
    file_put_contents($fileName,$data.PHP_EOL.PHP_EOL,FILE_APPEND);
}
function wsmGetVisitorIdFromCookie(){
    $configCookieNamePrefix = '_'.WSM_PREFIX.'_';
    $siteId=1;
    $domainHas='fa4f';
    $cookieId=$configCookieNamePrefix.'id_'.$siteId.'_'.$domainHas;
    $strVisitor=isset($_COOKIE[$cookieId])?$_COOKIE[$cookieId]:'';
    $arrVisitor=explode('.',$strVisitor);
    wsmFNUpdateLogFile('COOKIE INFO',$cookieId.'=>'.print_r($arrVisitor,true));
    return $arrVisitor[0];
}
function wsmFnGetDateRangeTitle($arrPostData){
    $dateRangeTitle=wsmGetCurrentDateByTimeZone('d F Y');
    switch($arrPostData['filterType']){
        case 'Hourly':
            switch($arrPostData['filterWay']){
                case 'Normal':
                    $dateRangeTitle=wsmGetDateByTimeStamp('d F Y',strtotime($arrPostData[$arrPostData['filterWay']]['from']));                                        
                break;
                case 'Range':
                    $dateRangeTitle=wsmGetDateByTimeStamp('d F Y',strtotime($arrPostData[$arrPostData['filterWay']]['from'])).'&nbsp;To&nbsp;'.wsmGetDateByTimeStamp('d F Y',strtotime($arrPostData[$arrPostData['filterWay']]['to']));
                break; 
                case 'Compare':
                    $dateRangeTitle=wsmGetDateByTimeStamp('d F Y',strtotime($arrPostData[$arrPostData['filterWay']]['first'])).'&nbsp;and&nbsp;'.wsmGetDateByTimeStamp('d F Y',strtotime($arrPostData[$arrPostData['filterWay']]['second']));
                break;   
            } 
        break;
        case 'Daily':
            switch($arrPostData['filterWay']){
                case 'Normal':
                    $dateRangeTitle=wsmGetDateByTimeStamp('F Y',strtotime($arrPostData[$arrPostData['filterWay']]['from'].'-01'));
                break;
                case 'Range':
                    $dateRangeTitle=wsmGetDateByTimeStamp('F Y',strtotime($arrPostData[$arrPostData['filterWay']]['from'].'-01')).'&nbsp;To&nbsp;'.wsmGetDateByTimeStamp('F Y',strtotime($arrPostData[$arrPostData['filterWay']]['to'].'-01'));
                break; 
                case 'Compare':
                    $dateRangeTitle=wsmGetDateByTimeStamp('F Y',strtotime($arrPostData[$arrPostData['filterWay']]['first'].'-01')).'&nbsp;and&nbsp;'.wsmGetDateByTimeStamp('F Y',strtotime($arrPostData[$arrPostData['filterWay']]['second'].'-01'));
                break;   
            } 
        break;
        case 'Monthly':
            switch($arrPostData['filterWay']){
                case 'Normal':
                    $dateRangeTitle=$arrPostData[$arrPostData['filterWay']]['from'];
                break;
                case 'Range':
                    $dateRangeTitle=$arrPostData[$arrPostData['filterWay']]['from'].'&nbsp;To&nbsp;'.$arrPostData[$arrPostData['filterWay']]['to'];
                break; 
                case 'Compare':
                    $dateRangeTitle=$arrPostData[$arrPostData['filterWay']]['first'].'&nbsp;and&nbsp;'.$arrPostData[$arrPostData['filterWay']]['second'];
                break;   
            }
        break;
    }
    return $dateRangeTitle='[&nbsp;'.$dateRangeTitle.'&nbsp;]';
}
function wsmFnGetPagination($totalRecords, $currentPage, $url, $limit = false ){
	
	if( !$limit ){
		$limit = WSM_PAGE_LIMIT;
	}
    $totalPages=intval($totalRecords / $limit);
    if(($totalRecords % $limit)>0){
       $totalPages+=1; 
    }
    $startPage = $currentPage - 4;
    $endPage = $currentPage + 4;
    if ($startPage <= 0) {        
        $startPage = 1;
    }
    if ($endPage > $totalPages){
        $endPage = $totalPages;
    }
    $html.='<ul class="wsmPagination">';   
    if ($currentPage > 1) {
        $html.='<li><a href="'.$url.'&wsmp='.($currentPage-1).'">'.__('Previous','wp-stats-manager').'</a></li>';
    }    
    for($i=$startPage; $i<=$endPage; $i++){
        $html.='<li>';
        if($i==$currentPage){            
            $html.='<span class="wsmCurrent">'.__($i,'wp-stats-manager').'</span>';
        }else{
            $html.='<a href="'.$url.'&wsmp='.$i.'">'.__($i,'wp-stats-manager').'</a>';
        }
        $html.='</li>';
    }
    if ($currentPage < $endPage) {
        $html.='<li><a href="'.$url.'&wsmp='.($currentPage+1).'">'.__('Next','wp-stats-manager').'</a></li>';
    }    
    $html.='</ul>';
    return $html;
}
function wsmGetSpinner(){
    $html='<div class="wsmSpinner"><div class="wsmRect1"></div><div class="wsmRect2"></div><div class="wsmRect3"></div><div class="wsmRect4"></div><div class="wsmRect5"></div></div>';
    return $html;
}
function wsmGetSearchKeywords($referrer = ''){
    // Parse the referrer URL
    $newURL=wsmFnDecodeURL($referrer);
    $parsed_url = parse_url($newURL);    
    if (empty($parsed_url['host']))
        return false;
    $host = $parsed_url['host'];
    $query_str = (!empty($parsed_url['query'])) ? $parsed_url['query'] : '';
    $query_str = (empty($query_str) && !empty($parsed_url['fragment'])) ? $parsed_url['fragment'] : $query_str;
    if (empty($query_str))
        return false;
    // Parse the query string into a query array
    parse_str($query_str, $query);
    // Check some major search engines to get the correct query var
    $search_engines = array(
        'q' => 'alltheweb|aol|ask|bing|google',
        'p' => 'yahoo',
        'wd' => 'baidu'
    );
    foreach ($search_engines as $query_var => $se){
        $se = trim($se);
        preg_match('/(' . $se . ')\./', $host, $matches);
        if (!empty($matches[1]) && !empty($query[$query_var]))
            return $query[$query_var];
    }
    return '-';
}
function wsmFnDecodeURL($url){   
    $smth = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($url)); 
    $smth = html_entity_decode($smth,null,'UTF-8');
    return $smth ;
}
function wsmArchiveTitleFromURL( $url ){
	$url_path = explode( '/', $url );
	$search_slug = '';
	$search_follow = true;
	$title = $url;
	foreach( $url_path as $key => $path ){
		if( $path == 'tag' ){
			$search_filter = 'tag';
		}
		if( $path == 'category' ){
			$search_filter = 'category';
		}
		if( $path == 'page' ){
			$search_follow = false;
		}
		if( $search_follow && !empty( $path ) ){
			$search_slug = $path;
		}
	}
	switch( $search_filter ){
		case 'tag':
			$tag = get_term_by( 'slug', $search_slug, 'post_tag' );
			if( $tag ){
				$title = $tag->name;
			}
			break;
		case 'category':
			$tag = get_term_by( 'slug', $search_slug, 'category' );
			if( $tag ){
				$title = $tag->name;
			}
			break;
	}
	if( $title == $url ){
		$title = str_replace('/?s=', '', $title );
	}
	return $title;
}
