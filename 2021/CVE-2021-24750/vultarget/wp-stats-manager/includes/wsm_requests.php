<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
class wsmRequests{
    private $siteId,$pageId,$userId,$objDatabase,$lookAheadSec,$lookBackSec,$defaultVisitTime;
    private $arrOriginal=array();
    private $arrUpdateProperties=array();
    private $arrVisitorProperties=array();
    function __construct($requests=''){
        $this->lookAheadSec=1800;
        $this->lookBackSec=2678400;
        $this->defaultVisitTime=0;
        if(!is_array($requests)){ 
            if(isset($_REQUEST) && is_array($_REQUEST)){ // We sanitize POST requests in the $requests string
                $requests=$_REQUEST;
            }
        }
        $this->arrOriginal=$requests;
        $this->objDatabase= new wsmDatabase();        
        $action=$this->fnGetParam('wmcAction');             
        if($action==='wmcTrack'){            
            $this->fnSetLastHitTime();
			$visitorId = ($requests['visitorId'] !='') ? ($requests['visitorId']) : '';
			
			
            if($visitorId !=''){
                $this->fnHandleVisit();
            }
           // header('Content-type: ' . "image/jpeg");
            die;
        }else if($action==='wmcAutoCron'){
            wsmInitPlugin::wsm_fnCreateImportantViews();
            //$this->fnGenerateDailyReports();
            die;
        }
    }
    function fnGenerateDailyReports(){
        $startDateTime=wsmGetDateByInterval('-1 days','Y-m-d');
        $newTimeZone=wsmCurrentGetTimezoneOffset();
        $hourWisePageViews=$this->objDatabase->fnGetHourlyReportByDateNameTimeZone($startDateTime,'hourWiseFirstVisitors',$newTimeZone);
       // print_r($hourWisePageViews);
    }
    function fnSetLastHitTime(){
        if($this->fnGetParam('ping')!=1){
            update_option(WSM_PREFIX.'_lastHitTime',wsmGetCurrentUTCDate());
        }
    }
    function fnSetTheNewProperties(){
        $this->fnSetSiteID();
        $this->fnSetPageID();
        $this->fnSetUserID();
        $this->fnSetVisitorId();
        $this->fnSetMiscProperties();
        $this->fnSetConfigId();
        $this->fnSetBrowserId();
        $this->fnSetOSId();
        $this->fnSetResolutionId();
        $this->fnSetURL();
        $this->fnSetRefURL();
        $this->fnSetVisitTotalActions(true);
        $this->fnSetTotalVisitTime(true);
        $this->fnSetExitURL();
        $this->fnSetVisitLastActionTime();
        $this->fnSetRefActionTime();
        $this->fnSetFirstActionVisitTime();
        $this->fnSetBrowserLanguage();
        $this->fnSetIPAddress();
        $this->fnSetVisitorLocationInfo();
    }
    function fnSetExistingProperties(){
        $this->fnSetSiteID();
        $this->fnSetPageID();
        $this->fnSetUserID();
       // $this->fnSetVisitorId();
        $this->fnSetURL();
        $this->fnSetRefURL();
        $this->fnSetTotalVisitTime();
        $this->fnSetExitURL();
        $this->fnSetRefActionTime();
        $this->fnSetVisitLastActionTime();
    }
    function fnSetVisitorLocationInfo(){
        if(!$this->fnGetVisitorProperty('latitude') || !$this->fnGetVisitorProperty('longitude') || !$this->fnGetVisitorProperty('countryId') || !$this->fnGetVisitorProperty('regionId')){
            $ip=$this->fnGetVisitorProperty('ipAddress');
            $arrLocation=wsmFnGetLocationInfo($ip);
            if(is_object($arrLocation)){
                if(isset($arrLocation->city)){
                    $this->fnSetVisitorProperty('city',$arrLocation->city);
                }
                if(isset($arrLocation->latitude)){
                    $this->fnSetVisitorProperty('latitude',$arrLocation->latitude);
                }
                if(isset($arrLocation->longitude)){
                    $this->fnSetVisitorProperty('longitude',$arrLocation->longitude);
                }
                if(isset($arrLocation->country_code)){
                    $countryID=$this->objDatabase->fnGetCountryIdByCode($arrLocation->country_code);
                    $this->fnSetVisitorProperty('countryId',$countryID);
                }
               /* if(isset($arrLocation->geoplugin_continentCode)){
                    $regionID=$this->objDatabase->fnGetRegionIdByCode($arrLocation->geoplugin_continentCode);
                    $this->fnSetVisitorProperty('regionId',$regionID);
                }*/
            }
        }
    }
    function fnGetVisitorProperties(){
        $arrProperties=array('id','siteId','pageId','browserId','visitLastActionTime','firstActionVisitTime','visitorId','visitId','userId','visitExitURLId','returningVisitor','daysSinceFirstVisit','daysSinceLastVisit','visitCount','countryId','regionId','city','latitude','longitude','siteId','visitEntryURLId','visitExitURLId','visitTotalActions','deviceType','refURLId','totalTimeVisit','ipAddress','browserLang','oSystemId','resolutionId','cookie','director','flash','gears','java','pdf','quicktime','realplayer','silverlight','windowsmedia','URLId','serverTime','timeSpentRef');
        return $arrProperties;
    }
    function fnHandleVisit(){
        $this->fnSetTheNewProperties();                
        $isNewVisit=$this->fnIsVisitNew();        
        if($isNewVisit){
            $this->fnHandleNewVisit();
        }else{
            $this->fnHandleExistingVisit();
        }
    }
    function fnSetSearchKeyword(){
        $fullRef=$this->fnGetParam('fullRef');
        $fullRef=($fullRef=='' || $fullRef=='undefined')?$this->fnGetParam('ref'):$fullRef;
        $protocol='http://';
        if($this->fnGetParam('refType')=='ssl'){
            $protocol='https://';
        }
        $fullRef=$protocol.$fullRef;
        $keyword='-';        
        if($fullRef!='' ){
            $keyword=wsmGetSearchKeywords($fullRef);            
        }    
        $keyword=$keyword?$keyword:'-';    
        $this->fnSetVisitorProperty('keyword', $keyword);       
    }
    
    function fnHandleNewVisit(){
       // echo 'New Visit : <pre>';
        $this->fnSetVisitorProperty('firstActionVisitTime', wsmGetCurrentUTCDate());
        $this->fnSetVisitorProperty('visitLastActionTime', wsmGetCurrentUTCDate());
        $this->fnSetURL();
        $this->fnSetRefURL();   
        if($this->arrVisitorProperties['visitorId']==0 || $this->arrVisitorProperties['visitorId']==''){
            $this->fnSetVisitorId();
        }     
        //$refURL=get_transient('wsm_'.$this->fnGetVisitorId());
        
        //$this->fnSetVisitorProperty('referrerUrl', $refURL); 
       // wsmFNUpdateLogFile('Transient',$refURL);
        // wsmFNUpdateLogFile('SERVER NEW REQUEST',print_r($_SERVER,true));
       // wsmFNUpdateLogFile('VISIT PARAMETERS', print_r($this->arrVisitorProperties,true));                 
        $visitId=$this->objDatabase->fnInsertNewUniqueVisit($this->arrVisitorProperties);
        $this->fnSetVisitorProperty('visitId', $visitId);
        $this->fnSetSearchKeyword();
        $this->objDatabase->fnInsertNewVisit($this->arrVisitorProperties);
    }
    
    function fnHandleExistingVisit(){
        $this->fnSetExistingProperties();        
       // echo 'Existing Visit : <pre>';
        //print_r($this->arrVisitorProperties);
        $properties=array();
        $visitId=$this->fnGetVisitorProperty('visitId');
        if($this->fnGetParam('ping')==1){
            $properties=array('visitorId'=>$this->fnGetVisitorProperty('visitorId'),'totalTimeVisit'=>$this->fnGetVisitorProperty('totalTimeVisit'),'timeSpentRef'=>$this->fnGetVisitorProperty('timeSpentRef'));
            $linkId=$this->objDatabase->fnGetLastLinkVisited($visitId);
            if($linkId!=''){
                $this->objDatabase->fnUpdateExistingLinkVisit($properties, $linkId);
            }
        }else {
            $properties=array('visitorId'=>$this->fnGetVisitorProperty('visitorId'),'totalTimeVisit'=>$this->fnGetVisitorProperty('totalTimeVisit'),'visitLastActionTime'=>$this->fnGetVisitorProperty('visitLastActionTime'),'visitTotalActions'=>$this->fnGetVisitorProperty('visitTotalActions'),'visitExitURLId'=>$this->fnGetVisitorProperty('visitExitURLId'),'timeSpentRef'=>$this->fnGetVisitorProperty('timeSpentRef') );
            $properties=array_merge($properties,$this->arrUpdateProperties);
            $urlId=$this->fnGetVisitorProperty('URLId');
            $dupId=$this->objDatabase->fnIsNotDuplicateLinkVisit($this->arrVisitorProperties,$this->fnGetVisitorProperty('URLId'));            
            if($dupId=='yes'){
                $this->fnSetVisitTotalActions();
                $properties['visitTotalActions']= $this->fnGetVisitorProperty('visitTotalActions');
                $this->fnSetSearchKeyword();
                if(!array_key_exists('URLId',$this->arrVisitorProperties) || !isset($this->arrVisitorProperties['URLId']) || $this->arrVisitorProperties['URLId']=='' || $this->arrVisitorProperties['URLId']==0){
                    $this->fnSetURL();
                }
                $this->objDatabase->fnInsertNewVisit($this->arrVisitorProperties);
            }else if(is_numeric($dupId)){
                $this->objDatabase->fnUpdateExistingLinkVisit($properties, $dupId);
            }
        }    
           
         $this->objDatabase->fnUpdateExistingVisit($properties,$visitId);
    }
    function fnFindVisitor(){
        $idVisitor=$this->fnGetVisitorId();
        $configId=$this->fnGetConfigId();
        $timeLookBack  = wsmGetDateByTimeStamp('Y-m-d H:i:s', wsmGetCurrentTimeStamp() - $this->lookBackSec);
        $timeLookAhead = wsmGetDateByTimeStamp('Y-m-d H:i:s', wsmGetCurrentTimeStamp() + $this->lookAheadSec);
        $rowVisit=0;

        if(!is_null($idVisitor) || $idVisitor!='' || $idVisitor!=0){
            $rowVisit=$this->objDatabase->fnFindVisitorById($idVisitor,$timeLookBack,$timeLookAhead);
        }else{
            $rowVisit=$this->objDatabase->fnFindVisitorByConfigId($configId,$timeLookBack,$timeLookAhead);
        }
        $visitAttributes=$this->fnGetVisitorProperties();
        if($rowVisit && count($rowVisit) > 0) {
            foreach ($visitAttributes as $field) {
                if(isset($rowVisit[$field])){
                    $this->fnSetVisitorProperty($field, $rowVisit[$field]);
                }
            }
            if($this->fnGetVisitorProperty('visitorId')=='' || $this->fnGetVisitorProperty('visitorId')==0){
                $this->fnSetVisitorId();
            }
            $this->fnSetVisitorProperty('visitLastActionTime', $rowVisit['visitLastActionTime']);
            $this->fnSetVisitorProperty('firstActionVisitTime', $rowVisit['firstActionVisitTime']);
            $this->fnSetVisitorProperty('visitId', $rowVisit['id']);
            $this->fnSetVisitorProperty('refererUrlId', NULL);
            return true;
        }
        return false;
    }

    function fnIsVisitNew(){
        $rowVisit=$this->fnFindVisitor();        
        if(!$rowVisit){
            return true;
        }
        /*$isLastActionInTheSameVisit=$this->fnIsLastActionInTheSameVisit();
        if (!$isLastActionInTheSameVisit) {
            return true;
        }*/
		
        $wasLastActionYesterday = $this->fnWasLastActionNotToday();
        if($wasLastActionYesterday){
            return true;
        }
        return false;
    }
    function fnGetVisitorProperty($field){
        if(isset($this->arrVisitorProperties[$field])){
            return $this->arrVisitorProperties[$field];
        }
        return false;
    }
    function fnSetVisitorProperty($field,$value){
        $this->arrVisitorProperties[$field]=$value;
    }
    function fnIsLastActionInTheSameVisit(){
        $lastVisitTime=strtotime($this->fnGetVisitorProperty('visitLastActionTime'));
        return isset($lastVisitTime)
            && false !== $lastVisitTime
            && ($lastVisitTime > (wsmGetCurrentTimeStamp() - $this->lookAheadSec));
    }
    function fnWasLastActionNotToday(){
        $lastVisitTime=strtotime($this->fnGetVisitorProperty('visitLastActionTime'));
        if (empty($lastVisitTime)) {
            return false;
        }
        $now = wsmGetCurrentTimeStamp();
        $nDate=wsmGetDateByTimeStamp('Ymd',$now);
        $lDate=wsmGetDateByTimeStamp('Ymd',$lastVisitTime);
        return  $nDate!==$lDate;
    }
    function fnSetBrowserId(){
        $bID=$this->fnGetVisitorProperty('browserId');
        if(!is_null($bID) && $bID!='' && $bID!=0){
            return;
        }
        $browser=$this->fnGetParam('browser');
        if($browser!='' && !is_numeric($browser)){
            $arrTemp=explode('_',$browser);
            $bID=0;
            if($arrTemp[0]!=''){
                $bID=$this->objDatabase->fnGetBrowserIDByTitle($arrTemp[0]);
            }
            $this->arrUpdateProperties['browserId']=$bID;                        
            $this->fnSetVisitorProperty('browserId',$bID);
        }
    }
    function fnSetOSId(){
        if($this->fnGetVisitorProperty('oSystemId')){
            return;
        }
        $os=$this->fnGetParam('os');
        if($os!='' && !is_numeric($os)){
            $arrTemp=explode('_',$os);
            $oID=0;
            if($arrTemp[0]!=''){
                $oID=$this->objDatabase->fnGetOSIDByTitle($arrTemp[0]);
				if( $arrTemp[0] == 'Windows' ){
					$oID=$this->objDatabase->fnGetOSIDByTitle($arrTemp[0].' '.$arrTemp[1]);
				}
            }
			
            $this->arrUpdateProperties['oSystemId']=$oID;            
            $this->fnSetVisitorProperty('oSystemId',$oID);
        }
    }
    function fnSetResolutionId(){
        if($this->fnGetVisitorProperty('resolutionId')){
            return;
        }
        $res=$this->fnGetParam('res');
        $rID=0;
        if($res!='' && !is_numeric($res)){
            $rID=$this->objDatabase->fnGetResolutionIDByTitle($res);
            $this->arrUpdateProperties['resolutionId']=$rID;            
            $this->fnSetVisitorProperty('resolutionId',$rID);
        }
    }
    function fnSetURL(){
        if($this->fnGetParam('ping') ){
            return;
        }
        $pid=$this->fnGetParam('pid');
        $uID=0;
        $url=$this->fnGetParam('url');
        if($pid!=''){
            $title=$this->fnGetParam('action_name');
            if(is_null($title) || $title==''){
                $title=get_the_title($pid);
            }
            $uID=$this->objDatabase->fnGetURLogID(array('pageId'=>$pid,'url'=>$url,'title'=>$title));
            $this->fnSetVisitorProperty('visitEntryURLId',$uID);
        }else{
            $uID=$this->objDatabase->fnGetURLogID(array('url'=>$url));
        }
        $this->arrUpdateProperties['URLId']=$uID;        
        $this->fnSetVisitorProperty('URLId',$uID);
    }
    function fnSetExitURL(){
        $exitURL=0;
        if($this->fnGetParam('link')){
            $lID=$this->objDatabase->fnGetRefLogID($this->fnGetParam('link'));
            $exitURL=$lID;            
        }else{
            $exitURL=$this->fnGetVisitorProperty('URLId');            
        }
        if(is_null($exitURL) || $exitURL==''){
            $exitURL=0;
        }
        $this->fnSetVisitorProperty('visitExitURLId',$exitURL);
    }
    function fnSetRefURL(){
        if($this->fnGetParam('ping')){
            return;
        }
        $urlRef=$this->fnGetParam('ref');
        $scheme=$this->fnGetParam('refType');
        if($scheme=='ssl'){
           $urlRef='https://'.$urlRef; 
        }else{
            $urlRef='http://'.$urlRef;
        }        
        if(!is_null($urlRef) && $urlRef!=''){
            $refID=$this->objDatabase->fnGetRefLogID($urlRef);
            $this->fnSetVisitorProperty('refererUrlId',$refID);
        }else{
            $this->fnSetVisitorProperty('refererUrlId',0);
        }
    }
    function fnGetReturningVisitor(){
        $visitCount = $this->fnGetVisitCount();
        $daysSinceFirstVisit = $this->fnGetDaysSinceFirstVisit();
        $daysSinceLastVisit = $this->fnGetDaysSinceLastVisit();
        if ($visitCount > 1 || $daysSinceFirstVisit > 0 || $daysSinceLastVisit > 0) {
            return 1;
        }else{
            return 0;
        }
    }
    function fnSetBrowserLanguage(){
        if($this->fnGetVisitorProperty('browserLang')){
            return;
        }
        $this->fnSetVisitorProperty('browserLang',$this->fnGetBrowserLanguage());
    }
    function fnSetIPAddress(){
        if($this->fnGetVisitorProperty('ipAddress')){
            return;
        }
        $ipAddress=wsmFnGetIPAddress();        
        $this->arrUpdateProperties['ipAddress']=$ipAddress;
        $this->fnSetVisitorProperty('ipAddress',$ipAddress);
    }
    function fnSetMiscProperties(){
        $this->fnSetVisitorProperty('cookie',$this->fnGetParam('cookie'));
        $this->fnSetVisitorProperty('director',$this->fnGetParam('dir'));
        $this->fnSetVisitorProperty('flash',$this->fnGetParam('fla'));
        $this->fnSetVisitorProperty('gears',$this->fnGetParam('gears'));
        $this->fnSetVisitorProperty('java',$this->fnGetParam('java'));
        $this->fnSetVisitorProperty('quicktime',$this->fnGetParam('qt'));
        $this->fnSetVisitorProperty('realplayer',$this->fnGetParam('rp'));
        $this->fnSetVisitorProperty('pdf',$this->fnGetParam('pdf'));
        $this->fnSetVisitorProperty('windowsmedia',$this->fnGetParam('wma'));
        $this->fnSetVisitorProperty('silverlight',$this->fnGetParam('ag'));
        $this->fnSetVisitorProperty('serverTime',wsmGetCurrentUTCDate('Y-m-d H:i:s'));
        $this->fnSetVisitorProperty('deviceType',$this->fnGetParam('device'));
        $this->fnSetVisitorProperty('daysSinceLastVisit',$this->fnGetDaysSinceLastVisit());
        $this->fnSetVisitorProperty('daysSinceFirstVisit',$this->fnGetDaysSinceFirstVisit());
        $this->fnSetVisitorProperty('returningVisitor',$this->fnGetReturningVisitor());
        $this->fnSetVisitorProperty('visitCount',$this->fnGetVisitCount());
        $this->fnSetVisitorProperty('currentLocalTime',$this->fnGetLocalTime());
    }
    function fnSetFirstActionVisitTime(){
        if($this->fnGetVisitorProperty('firstActionVisitTime')){
            return;
        }
        $this->fnSetVisitorProperty('firstActionVisitTime',wsmGetCurrentUTCDate('Y-m-d H:i:s'));
    }
    function fnSetVisitLastActionTime(){
        $this->fnSetVisitorProperty('visitLastActionTime',wsmGetCurrentUTCDate('Y-m-d H:i:s'));
    }
    function fnSetVisitTotalActions($isNew=false){
        if(!$isNew){
            $this->fnSetVisitorProperty('visitTotalActions','visitTotalActions+1');
            return;
        }
        $this->fnSetVisitorProperty('visitTotalActions',1);
    }
    function fnSetTotalVisitTime($isNew=false){
        if($isNew){
            $totalTime=$this->defaultVisitTime;
        }else{
            $firstActionTime = strtotime($this->fnGetVisitorProperty('firstActionVisitTime'));
            $totalTime = 1 + wsmGetCurrentTimeStamp() - $firstActionTime;
            $totalTime = $this->fnCleanupVisitTotalTime($totalTime);
        }
        $this->fnSetVisitorProperty('totalTimeVisit',$totalTime);
    }
    function fnSetRefActionTime(){
        $this->fnSetVisitorProperty('timeSpentRef',$this->fnGetTimeSpentReferrerAction());
    }
    function fnSetConfigId(){
        if($this->fnGetVisitorProperty('configId')){
            return;
        }
        $configString =
              $this->fnGetParam('os')
            . $this->fnGetParam('browser')
            . $this->fnGetVisitorProperty('flash') . $this->fnGetVisitorProperty('java') . $this->fnGetVisitorProperty('director') . $this->fnGetVisitorProperty('quicktime') . $this->fnGetVisitorProperty('realplayer') . $this->fnGetVisitorProperty('pdf')
            . $this->fnGetVisitorProperty('windowsmedia') . $this->fnGetVisitorProperty('gears') . $this->fnGetVisitorProperty('silverlight') . $this->fnGetVisitorProperty('cookie')
            . $this->fnGetVisitorProperty('ipAddress')
            . $this->fnGetVisitorProperty('siteId');
        $hash = substr(md5($configString),0,16);
        $this->arrUpdateProperties['configId']=$hash;        
        $this->fnSetVisitorProperty('configId',$hash);
    }
    function fnSetSiteID(){
        if($this->fnGetVisitorProperty('siteId')){
            return;
        }
        $siteId=$this->fnGetSiteId();
        $this->arrUpdateProperties['siteId']=$siteId;        
        $this->fnSetVisitorProperty('siteId',$siteId);
    }
    function fnSetPageID(){
        if($this->fnGetVisitorProperty('pageId')){
            return;
        }
        $pageId=$this->fnGetPageId();        
        $this->fnSetVisitorProperty('pageId',$pageId);
    }
    function fnSetUserID(){
        if($this->fnGetVisitorProperty('userId')){
            return;
        }
        $userId=$this->fnGetUserId();
        $this->arrUpdateProperties['userId']=$userId;        
        $this->fnSetVisitorProperty('userId',$userId);
    }
    function fnSetVisitorId(){
        $this->fnSetVisitorProperty('visitorId',$this->fnGetVisitorId());
    }
    function fnGetSiteId(){
        return $this->fnGetParam('siteId');
    }
    function fnGetPageId(){
        return $this->fnGetParam('pid');
    }
    function fnGetUserId(){
        return $this->fnGetParam('uid');
    }
    function fnGetVisitorId(){
        return $this->fnGetParam('visitorId');
    }
    function fnGetConfigId(){
        return $this->fnGetParam('configId');
    }
    function fnGetParam($name){
        $arrSupported=array(
            'wmcAction'   => array('', 'string'),
            'action_name'   => array('', 'string'),
            'configId'   => array('', 'string'),
            'siteId'        => array(0, 'int'),
            'pid'           => array(0, 'int'),
            'rec'           => array(1, 'int'),
            'visitorId'     => array('', 'string'),
            'rand'          => array('', 'string'),
            'h'             => array(0, 'int'),
            'm'             => array(0, 'int'),
            's'             => array(0, 'int'),
            'uid'           => array(0, 'int'),
            'url'           => array('', 'string'),
            'ref'        => array('', 'string'),
            'refType'        => array('', 'string'),
            'fullRef'        => array('', 'string'),
            'refts'         => array('', 'string'),
            'res'           => array('', 'string'),
            'fvts'          => array(0, 'double'),
            'lvts'          => array(0, 'double'),
            'vc'            => array(0, 'int'),
            'os'            => array('', 'string'),
            'browser'       => array('', 'string'),
            'device'        => array('', 'string'),
            'pos'           => array('', 'string'),
            'ping'          => array(0, 'int'),
            'link'          => array('', 'string'),
            'pvId'          => array('', 'string'),
            'gtms'          => array(-1, 'int'),
            'cookie'        => array('0', 'int'),
            'pdf'           => array('0', 'int'),
            'qt'            => array('0', 'int'),
            'rp'            => array('0', 'int'),
            'wma'           => array('0', 'int'),
            'dir'           => array('0', 'int'),
            'fla'           => array('0', 'int'),
            'java'          => array('0', 'int'),
            'gears'         => array('0', 'int'),
            'ag'            => array('0', 'int')
        );
        if (!isset($arrSupported[$name])) {
            throw new Exception(__("Requested parameter $name is not a known Parameter.",'wp-stats-manager'));
        }
        if(!isset($this->arrOriginal[$name]) || is_null($this->arrOriginal[$name]) || $this->arrOriginal[$name]==''){
            return 0;
        }        
        if(isset($this->arrOriginal[$name])){
			
            return sanitize_text_field($this->arrOriginal[$name]);
        }
        
        return false;
    }    
    
    function fnGetDaysSinceFirstVisit()    {
        $cookieFirstVisitTimestamp = $this->fnGetParam('fvts');
        if (!wsmIsTimestampValid($cookieFirstVisitTimestamp)) {
            $cookieFirstVisitTimestamp = wsmGetCurrentTimeStamp();
        }
        $daysSinceFirstVisit = round((wsmGetCurrentTimeStamp() - $cookieFirstVisitTimestamp) / 86400, $precision = 0);
        if ($daysSinceFirstVisit < 0) {
            $daysSinceFirstVisit = 0;
        }
        return $daysSinceFirstVisit;
    }
    function fnGetTimeSpentReferrerAction(){
        $timeSpent = wsmGetCurrentTimeStamp()-strtotime($this->fnGetVisitorProperty('visitLastActionTime'));
        if ($timeSpent < 0) {
            $timeSpent = 0;
        }
       // echo $timeSpent.'=v='.$this->lookAheadSec;
        if ($timeSpent > $this->lookAheadSec) {
            $timeSpent = $this->lookAheadSec;
        }
        return $timeSpent;
    }
    function fnGetDaysSinceLastVisit(){
        $daysSinceLastVisit = 0;
        $lastVisitTimestamp = $this->fnGetParam('lvts');
        if (wsmIsTimestampValid($lastVisitTimestamp)) {
            $daysSinceLastVisit = round((wsmGetCurrentTimeStamp() - $lastVisitTimestamp) / 86400, $precision = 0);
            if ($daysSinceLastVisit < 0) {
                $daysSinceLastVisit = 0;
            }
        }
        return $daysSinceLastVisit;
    }
    function fnGetVisitCount(){
        $visitCount = $this->fnGetParam('vc');
        if ($visitCount < 1) {
            $visitCount = 1;
        }
        return $visitCount;
    }
    function fnGetBrowserLanguage(){
        $browserLang = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])?$_SERVER['HTTP_ACCEPT_LANGUAGE']:'';
        if (empty($browserLang)) {
            $browserLang = @getenv('LANG');
        }
        if($browserLang!=''){
            $arr=explode(',',$browserLang);
            return $arr[0];
        }
        return 'en';
    }
    function fnGetLocalTime(){
        $localTimes = array(
            'h' => (string)$this->fnGetParam('h')?$this->fnGetParam('h'):wsmGetCurrentUTCDate("H"),
            'i' => (string)$this->fnGetParam('m')?$this->fnGetParam('m'):wsmGetCurrentUTCDate("i"),
            's' => (string)$this->fnGetParam('s')?$this->fnGetParam('s'):wsmGetCurrentUTCDate("s")
        );
        if($localTimes['h'] < 0 || $localTimes['h'] > 23) {
            $localTimes['h'] = 0;
        }
        if($localTimes['i'] < 0 || $localTimes['i'] > 59) {
            $localTimes['i'] = 0;
        }
        if($localTimes['s'] < 0 || $localTimes['s'] > 59) {
            $localTimes['s'] = 0;
        }
        foreach ($localTimes as $k => $time) {
            if (strlen($time) == 1) {
                $localTimes[$k] = '0' . $time;
            }
        }
        $localTime = $localTimes['h'] . ':' . $localTimes['i'] . ':' . $localTimes['s'];
        return $localTime;
    }
    function fnCleanupVisitTotalTime($t){
        $t = (int)$t;
        if ($t < 0) {
            $t = 0;
        }
        return $t;
    }
}