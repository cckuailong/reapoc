<?php    
//## NextScripts FriendFeed Connection Class
$nxs_snapAPINts[] = array('code'=>'LJ', 'lcode'=>'lj', 'name'=>'LiveJournal');

if (!class_exists("nxs_class_SNAP_LJ")) { class nxs_class_SNAP_LJ {
    
    var $ntCode = 'LJ';
    var $ntLCode = 'lj';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function nxs_getLJHeaders($up){ $hdrsArr = array(); 
      $hdrsArr['Cache-Control']='no-cache'; $hdrsArr['Connection']='keep-alive'; 
      $hdrsArr['User-Agent']='SNAP for Wordpress; Ver '.NextScripts_SNAP_Version;
      $hdrsArr['Accept']='text/html, application/xhtml+xml, */*'; $hdrsArr['DNT']='1';
      $hdrsArr['Authorization'] = 'Basic ' . base64_encode("$up");
      if (function_exists('gzdeflate')) $hdrsArr['Accept-Encoding']='gzip,deflate'; 
      $hdrsArr['Accept-Language']='en-US,en;q=0.8'; $hdrsArr['Accept-Charset']='ISO-8859-1,utf-8;q=0.7,*;q=0.3'; return $hdrsArr;
    }
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>'');
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }            
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message);      
      
      require_once ('apis/xmlrpc-client.php'); if (!empty($options['ljSrv']) && $options['ljSrv']=='DW') $server = 'dreamwidth.org'; else $server = 'livejournal.com';      
      $nxsToLJclient = new NXS_XMLRPC_Client('http://www.'.$server.'/interface/xmlrpc'); $nxsToLJclient->debug = false;             
      
      $date = time(); $year = date("Y", $date); $mon = date("m", $date); $day = date("d", $date); $hour = date("G", $date); $min = date("i", $date);
      $nxsToLJContent = array( "username" => $options['uName'], "password" => $pass, "event" => $msg, "subject" => $msgT, "lineendings" => "unix", "year" => $year, "mon" => $mon, "day" => $day, "hour" => $hour, "min" => $min, "ver" => 2);      
      if (!empty($options['commID']) && $options['commID']!='') $nxsToLJContent["usejournal"] = $options['commID'];  
      if (!empty($options['inclTags']) && $options['inclTags']=='1' && !empty($message['tags'])) $nxsToLJContent['props'] = array('taglist' => $message['tags']);      
        // prr($nxsToLJContent);
      if (!$nxsToLJclient->query('LJ.XMLRPC.postevent', $nxsToLJContent)) {  prr($nxsToLJclient); $ret = 'Something went wrong - '.$nxsToLJclient->getErrorCode().' : '.$nxsToLJclient->getErrorMessage();} else $ret = 'OK';      
      $pid = $nxsToLJclient->getResponse(); 
      if (is_array($pid) && !empty($pid['url'])) return array('postID'=>$pid['url'], 'isPosted'=>1, 'postURL'=>$pid['url'], 'pDate'=>date('Y-m-d H:i:s'));  
        else $badOut['Error'] .= 'Something went wrong - NO PID '.print_r($pid, true); 
      return $badOut;      
   }    
}}
?>