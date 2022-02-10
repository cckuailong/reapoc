<?php    
//## NextScripts Diigo Connection Class
$nxs_snapAPINts[] = array('code'=>'DI', 'lcode'=>'di', 'name'=>'Diigo');

if (!class_exists("nxs_class_SNAP_DI")) { class nxs_class_SNAP_DI {
    
    var $ntCode = 'DI';
    var $ntLCode = 'di';     
    
    function doPost($options, $message){ if (!is_array($options)) return false; $out = array();
      foreach ($options as $ii=>$ntOpts) $out[$ii] = $this->doPostToNT($ntOpts, $message);
      return $out;
    }    
    function nxs_getDIHeaders($ref, $uname, $pass, $post=false){ $hdrsArr = array(); 
      $hdrsArr['X-Requested-With']='XMLHttpRequest'; $hdrsArr['Connection']='keep-alive'; $hdrsArr['Referer']=$ref;
      $hdrsArr['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.22 Safari/537.11';
      if($post) $hdrsArr['Content-Type']='application/x-www-form-urlencoded'; 
      $hdrsArr['Accept']='application/json, text/javascript, */*; q=0.01'; 
      $hdrsArr['Authorization']= 'Basic '.base64_encode($uname.':'.$pass);
      $hdrsArr['Accept-Encoding']='gzip,deflate,sdch'; $hdrsArr['Accept-Language']='en-US,en;q=0.8'; $hdrsArr['Accept-Charset']='ISO-8859-1,utf-8;q=0.7,*;q=0.3'; return $hdrsArr;
    }
    function doPostToNT($options, $message){ $badOut = array('pgID'=>'', 'isPosted'=>0, 'pDate'=>date('Y-m-d H:i:s'), 'Error'=>''); 
      //## Check settings
      if (!is_array($options)) { $badOut['Error'] = 'No Options'; return $badOut; }      
      if (!isset($options['uName']) || trim($options['uPass'])=='') { $badOut['Error'] = 'Not Configured'; return $badOut; }      
      $pass = (substr($options['uPass'], 0, 5)=='n5g9a'||substr($options['uPass'], 0, 5)=='g9c1a'||substr($options['uPass'], 0, 5)=='b4d7s')?nsx_doDecode(substr($options['uPass'], 5)):$options['uPass'];       
      //## Format
      if (!empty($message['pText'])) $msg = $message['pText']; else $msg = nxs_doFormatMsg($options['msgFormat'], $message); 
      if (!empty($message['pTitle'])) $msgT = $message['pTitle']; else $msgT = nxs_doFormatMsg($options['msgTFormat'], $message);       
      $flds = array(); $flds['key']=$options['apiKey']; $flds['url']=$message['url']; $flds['title']=nsTrnc($msgT, 250); $flds['desc']=nsTrnc($msg, 250); $flds['tags']=$message['tags']; $flds['shared']='yes';   //   prr($flds); die();
      $hdrsArr = $this->nxs_getDIHeaders('https://www.diigo.com/api/v2/bookmarks', $options['uName'], $pass, true);
      $cnt = nxs_remote_post( 'https://www.diigo.com/api/v2/bookmarks', array( 'method' => 'POST', 'timeout' => 45, 'redirection' => 0, 'headers' => $hdrsArr, 'body' => $flds)); 
      //## Return      
      if (is_array($cnt) &&  stripos($cnt['body'],'"code":1')!==false) {         
         return array('postID'=>'DI', 'isPosted'=>1, 'postURL'=>'https://www.diigo.com/user/'.$options['uName'], 'pDate'=>date('Y-m-d H:i:s'));          
      } else { $badOut['Error'] .= print_r($cnt, true); 
        return $badOut;
      }
    }
}}
?>