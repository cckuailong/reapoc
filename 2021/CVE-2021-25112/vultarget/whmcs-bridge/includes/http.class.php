<?php
//v2.02.06
//removed cc_whmcs_log call
//need wpabspath for mailz
//mailz returns full URL in case of redirection!!
//made redirect generic, detect if location string contains protocol and server or not
//added option to enable repost of $_POST variables
//fixed issue with redirection location string
//added support for content-type
//fixed issue with $this->$headers wrong, should be $this->headers
//fixed issue with handling of $repost
//added check on $headers['location'] existence
//added initialisation of $post and $apost
//fixed issue with checkConnection()
//added support for multiple cookies
//check if session exists before starting a new one
//changed return test value of checkConnection()
//replaced display of error and notice by triggering PHP error or notice
//added mime type info to uploaded files
//added option to disable following redirect links
//fixed issue with HTTP 417 errors on some web servers
//fixed redirect link parsing issue
//removed check on cainfo
//added redirection fix for Windows
//removed _params variable
//fixed issue with redirect urls duplicating url path
//added http return code
//improvide error management
//store remote PHP session id local session to avoid overwrite in case the session id is not returned

class bridgeHttpRequest
{
	var $_fp;        // HTTP socket
	var $_url;        // full URL
	var $_host;        // HTTP host
	var $_protocol;    // protocol (HTTP/HTTPS)
	var $_uri;        // request URI
	var $_port;        // port
	var $_path;
	var $error=false;
	var $errno=false;
	var $post=array();	//post variables, defaults to $_POST
	var $redirect=false;
	var $forceWithRedirect=array();
	var $errors=array();
	var $countRedirects=0;
	var $sid;
	var $httpCode;
	var $repost=false;
	var $type; //content-type
	var $follow=true; //whether to follow redirect links or not
	var $noErrors=false; //whether to trigger an error in case of a curl error
	var $errorMessage;
	var $httpHeaders=array('Expect:','bridgeon: 1'); //avoid 417 errors
	var $debugFunction;
	var $time;
	var $cookieArray=array();
	var $cookieCache='';

	// constructor
	function __construct($url="",$sid='', $repost=false)
	{
		if (!$url) return;
		$this->sid=$sid;
		$this->_url = $url;
		$this->_scan_url();
		$this->post=$_POST;
		$this->repost=$repost;
	}


	private function time($action) {
		$t=function_exists('microtime') ? 'microtime' :'time';
		if ($action=='reset') $this->time=$t(true);
		elseif ($action=='delta') return round(($t(true)-$this->time)*100,0);
	}
	private function forceWithRedirectToString($url) {
		$s='';
		if (count($this->forceWithRedirect)) {
			foreach ($this->forceWithRedirect as $n => $v) {
				if (stristr($url,$n.'=')) continue;
				if ($s) $s.='&';
				$s.=$n.'='.$v;
			}
		}
		return $s;
	}

	private function debug($type=0,$msg='',$filename="",$linenum=0) {
		if ($f=$this->debugFunction) $f($type,$msg,$filename,$linenum);
	}

	private function os() {
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') return 'WINDOWS';
		else return 'LINUX';
	}

	private function processHeaders($headers) {
        $this->debug(0, 'Processing headers: '.json_encode($headers));

		// split headers, one per array element
		if ( is_string($headers) ) {
			// tolerate line terminator: CRLF = LF (RFC 2616 19.3)
			$headers = str_replace("\r\n", "\n", $headers);
			// unfold folded header fields. LWS = [CRLF] 1*( SP | HT ) <US-ASCII SP, space (32)>, <US-ASCII HT, horizontal-tab (9)> (RFC 2616 2.2)
			$headers = preg_replace('/\n[ \t]/', ' ', $headers);
			// create the headers array
			$headers = explode("\n", $headers);
		}

		$response = array('code' => 0, 'message' => '');

		// If a redirection has taken place, The headers for each page request may have been passed.
		// In this case, determine the final HTTP header and parse from there.
		for ( $i = count($headers)-1; $i >= 0; $i-- ) {
			if ( !empty($headers[$i]) && false === strpos($headers[$i], ':') ) {
				$headers = array_splice($headers, $i);
				break;
			}
		}

		$cookies = '';
		$newheaders = array();

		foreach ( $headers as $tempheader ) {
			if ( empty($tempheader) )
			continue;

			if ( false === strpos($tempheader, ':') ) {
				list( , $response['code'], $response['message']) = explode(' ', $tempheader, 3);
				continue;
			}

			list($key, $value) = explode(':', $tempheader, 2);

            if ( !empty( $value ) ) {
				$key = strtolower( $key );
				if ( isset( $newheaders[$key] ) ) {
					if ( !is_array($newheaders[$key]) )
					$newheaders[$key] = array($newheaders[$key]);
					$newheaders[$key][] = trim( $value );
				} else {
					$newheaders[$key] = trim( $value );
				}
				if ('set-cookie' == $key) {
					if ($cookies) $cookies.=' ;';
					$cookies .= $value;
					list($k,$rest)=explode('=',$value,2);
					$this->cookieArray[trim($k)]=$value;
					if (stristr($value,'=deleted')) unset($_SESSION[$this->sid]['cookie-array'][trim($k)]);
					else $_SESSION[$this->sid]['cookie-array'][trim($k)]=$value;
				}
			}
		}
		return array('response' => $response, 'headers' => $newheaders, 'cookies' => $cookies);
	}

	// scan url
	private function _scan_url() {
		$req = $this->_url;

		$pos = strpos($req, '://');
		$this->_protocol = strtolower(substr($req, 0, $pos));

		$req = substr($req, $pos+3);
		$pos = strpos($req, '/');
		if($pos === false)
		$pos = strlen($req);
		$host = substr($req, 0, $pos);

		if(strpos($host, ':') !== false)  {
			list($this->_host, $this->_port) = explode(':', $host);
		} else {
			$this->_host = $host;
			$this->_port = ($this->_protocol == 'https') ? 443 : 80;
		}

		$this->_uri = substr($req, $pos);
		if($this->_uri == '') {
			$this->_uri = '/';
		} else {
			$params=substr(strrchr($this->_uri,'/'),1);
			$this->_path=str_replace($params,'',$this->_uri);
		}
	}

	//check if server is live
	function live() {
        //return true;
		if (ip2long($this->_host)) return true; //in case using an IP instead of a host name
		$url=$this->_host;
		if (gethostbyname($url) == $url) return false;
		else return true;
	}

	//get mime type of uploaded file
	function mimeType($file) {
		$mime='';
		if (function_exists('finfo_open')) {
			if ($finfo = finfo_open(FILEINFO_MIME_TYPE)) {
				$mime=finfo_file($finfo, $file);
				finfo_close($finfo);
			}
		}
		if ($mime) return ';type='.$mime;
		else return '';
	}

	//check if cURL installed
	function curlInstalled() {
		if (!function_exists('curl_init')) return false;
		else return true;
	}

	//check destination is reachable
	function checkConnection() {
		$this->post['checkconnection']=1;
		$output=$this->connect($this->_protocol.'://'.$this->_host.$this->_uri);
		if ($output=='zingiri' || $output=='connected') return true;
		else return false;
	}

	//error logging
	function error($msg) {
		$this->errorMsg=$msg;
		$this->error=true;
		//if (!$this->noErrors) trigger_error($msg,E_USER_WARNING);
		$this->debug(E_USER_WARNING,$msg);
	}

	//notification logging
	function notify($msg) {
		$this->errorMsg=$msg;
		$this->error=true;
		if (!$this->noErrors) trigger_error($msg,E_USER_NOTICE);
		$this->debug(E_USER_NOTICE,$msg);
	}

	// download URL to string
	function DownloadToString($withHeaders=true,$withCookies=false) {
	    if ($this->_port == 80 || $this->_port == 443)
		    $html = $this->connect($this->_protocol.'://'.$this->_host.$this->_uri,$withHeaders,$withCookies);
	    else
            $html = $this->connect($this->_protocol.'://'.$this->_host.':'.$this->_port.$this->_uri,$withHeaders,$withCookies);

        return $html;
	}

	function makeQueryString($params, $prefix = '', $removeFinalAmp = true) {
		$queryString = '';
		if (is_array($params)) {
			foreach ($params as $key => $value) {
				$correctKey = $prefix;
				if ('' === $prefix) {
					$correctKey .= $key;
				} else {
					$correctKey .= "[" . $key . "]";
				}
				if (!is_array($value) && !is_object($value)) {
					$queryString .= urlencode($correctKey) . "="
						. urlencode($value) . "&";
				} else {
					$queryString .= $this->makeQueryString($value, $correctKey, false);
				}
			}
		}
		if ($removeFinalAmp === true) {
			return substr($queryString, 0, strlen($queryString) - 1);
		} else {
			return $queryString;
		}
	}

	function connect($url, $withHeaders=true, $withCookies=false) {
		$this->time('reset');
        global $wordpressPageName;

		$newfiles=array();

		$url = str_replace('?m=DNSManagerII', '?m=DNSManager2', $url);

        // 2co/SolusVM/Quantumvault callback requires get params
        if ((stristr($url, 'solusvm') !== false || stristr($url, 'quantumvault') !== false || stristr($url, 'twocheckout')) && count($_GET) > 0) {
            $ignore = array('ccce');
            $get_params = array();
            foreach ($_GET as $k => $v) {
                if (!in_array($k, $ignore)) {
                    $get_params[$k] = $v;
                }
            }
            if (count($get_params) > 0) {
                if (stristr($url, '?') !== false) {
                    $url .= '&' . http_build_query($get_params);
                } else {
                    $url .= '?' . http_build_query($get_params);
                }
            }
        }

        $this->debug(0, 'Not cached, processing file - '.$url);

        if (function_exists('cc_whmcsbridge_sso_session'))
            cc_whmcsbridge_sso_session();
        if (session_status() == PHP_SESSION_NONE && !headers_sent())
            session_start();

        $ch = curl_init();    // initialize curl handle

        $this->debug(0, 'CURL call: ' . $url . (is_array($this->post) ? ' with ' . json_encode($this->post) : ''));

        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        if ($withHeaders) curl_setopt($ch, CURLOPT_HEADER, 1);

        if (get_option("cc_whmcs_bridge_affiliate_id") && is_numeric(get_option("cc_whmcs_bridge_affiliate_id")) && get_option("cc_whmcs_bridge_affiliate_id") > 0) {
            $this->httpHeaders[] = 'bridgeaffiliate: ' . get_option("cc_whmcs_bridge_affiliate_id");
        }

        $this->httpHeaders[] = 'bridgeon: 1';

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->httpHeaders[] = 'X-Requested-With: XMLHttpRequest';
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->httpHeaders); //avoid 417 errors

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36 OPR/70.0.3728.189');

        //cloudflare debug
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

        //CURLOPT_REFERER -  The contents of the "Referer: " header to be used in a HTTP request.
        //CURLOPT_INTERFACE -  The name of the outgoing network interface to use. This can be an interface name, an IP address or a host name.

        curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 120s

        if ($this->_protocol == "https") {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_CAINFO, NULL);
            curl_setopt($ch, CURLOPT_CAPATH, NULL);
        }
        // gzip
        $this->debug(0, 'Server Software: '.$_SERVER['SERVER_SOFTWARE']);
        if (stristr($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false) {
            curl_setopt($ch, CURLOPT_ENCODING, "");
        }

        $cookies = "";
        $cookies = apply_filters('bridgeHttpRequest_pre', $cookies);

        if (isset($_SESSION[$this->sid]['cookie-array']) && count($_SESSION[$this->sid]['cookie-array']) > 0) {
            foreach ($_SESSION[$this->sid]['cookie-array'] as $n => $v) {
                // CloudFlare
                if (stristr($n, '__cfduid') !== false) continue;
                if (stristr($n, '_cflb') !== false) continue;
                if (stristr($n, '_cf_bm') !== false) continue;

                if ($cookies) $cookies .= ';';
                $cookies .= $v;
            }
        }

        if ($cookies) {
            $this->debug(0, 'Cookie before:' . json_encode(explode("\r\n", $cookies)));
            if (stristr($cookies, '__cfduid') !== false) {
                $cookies = 'WHMCS'.substr($cookies, strpos($cookies, "WHMCS") + 1);
            }
            curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        }

        $_SESSION['cookieCache'] = $cookies;

        if (count($_FILES) > 0) {
            foreach ($_FILES as $name => $file) {
                if (is_array($file['tmp_name']) && count($file['tmp_name']) > 0) {
                    $c = count($file['tmp_name']);
                    for ($i = 0; $i < $c; $i++) {
                        if ($file['tmp_name'][$i]) {
                            $newfile = BLOGUPLOADDIR. $file['name'][$i];
                            $newfiles[] = $newfile;
                            copy($file['tmp_name'][$i], $newfile);
                            if (!file_exists($newfile)) {
                                cc_whmcs_log('Cant copy '.$file['tmp_name'][$i].' to '.$newfile);
                            } else {
                            	if (PHP_VERSION_ID >= 50500) {
		                    		$this->post[$name][$i] = new CurlFile($newfile, str_replace(';', '', $this->mimeType($newfile)), $newfile);
                            	} else {
                                	if ($file['tmp_name'][$i]) $this->post[$name][$i] = '@' . $newfile . $this->mimeType($newfile);                            		
                            	}
                            }
                        }
                    }
                } elseif ($file['tmp_name']) {
                    $newfile = BLOGUPLOADDIR. $file['name'];
                    $newfiles[] = $newfile;
                    copy($file['tmp_name'], $newfile);
                    if (!file_exists($newfile)) {
                        cc_whmcs_log('Cant copy '.$file['tmp_name'][$i].' to '.$newfile);
                    } else {
                    	if (PHP_VERSION_ID >= 50500 && class_exists('CurlFile')) {
                    		$this->post[$name] = new CurlFile($newfile, str_replace(';', '', $this->mimeType($newfile)), $newfile);
	                    } else {
	                        if ($file['tmp_name']) $this->post[$name] = '@' . $newfile . $this->mimeType($newfile);	                    	
	                    }
                    }
                }
            }
            cc_whmcs_log(0, 'There are files:  '.json_encode($newfiles));
        }

        $rawPost = file_get_contents('php://input');
        cc_whmcs_log(0, "RAW data: ".$rawPost);
        $apost = array();

        if (count($this->post) > 0) {
            cc_whmcs_log(0, "HTTP Method POST 2");
            curl_setopt($ch, CURLOPT_POST, 1); // set POST method
            $post = "";
            $apost = array();
            $this->post = stripslashes_deep($this->post);
            foreach ($this->post as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if (is_array($v2)) {
                            foreach ($v2 as $k3 => $v3) {
                                if (is_array($v3)) {
                                    foreach ($v3 as $k4 => $v4) {
                                        $apost[$k . '[' . $k2 . ']' . '[' . $k3 . '][' . $k4 . ']'] = ($v4);
                                    }
                                } else {
                                    $apost[$k . '[' . $k2 . ']' . '[' . $k3 . ']'] = ($v3);
                                }
                            }
                        } else {
                            $apost[$k . '[' . $k2 . ']'] = ($v2);
                        }
                    }

                } else {
                    $apost[$k] = ($v);
                }
            }
        } else if (stristr($url, 'two-factor') !== false && stristr($url, 'totp') === false) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // set POST method
            cc_whmcs_log(0, "HTTP customrequest Method POST 1");
        }

        if (count($apost) > 0) {
            if (stristr($url, 'clientarea.php?action=details') !== false && !isset($apost['save']) && isset($apost['firstname'], $apost['lastname'], $apost['companyname'], $apost['address1'])) {
                $apost['save'] = 'Save Changes';
                cc_whmcs_log(0, 'Safari patch for updating personal details');
            }

            if (count($newfiles) > 0) {
            	curl_setopt($ch, CURLOPT_POSTFIELDS, $apost);					
                cc_whmcs_log(0, 'Posting as [0]:  '.json_encode($apost));
            } else {
                $pfields = $this->makeQueryString($apost);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $pfields);
                cc_whmcs_log(0, 'Posting as [1]:  '.json_encode($pfields));
            }

        } else if (!empty($rawPost)) {
            if (in_array(substr($rawPost, 0, 1),  ['[', '{', '"'])) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // set POST method
            } else {
                parse_str($rawPost, $rawPost);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $rawPost);

            cc_whmcs_log(0, "Posting RAW: ".$rawPost);
            cc_whmcs_log(0, "HTTP customrequest Method POST 2");
        } else if (strtolower($_SERVER['REQUEST_METHOD']) == "post" && strstr($url, 'viewinvoice.php') === false) {
            curl_setopt($ch, CURLOPT_POST, 1); // set POST method
            cc_whmcs_log(0, "HTTP Method POST 1");
        }

        $data = curl_exec($ch); // run the whole process

        if (curl_errno($ch)) {
            $this->errno = curl_errno($ch);
            $this->error = curl_error($ch);
            $error_msg = 'An error has occurred: ' . $this->error;
            $this->error($this->errno . '/' . $error_msg.' ('.$url.')');
            cc_whmcs_log(0, 'HTTP Error:  '.$this->errno . '/' . $error_msg.' ('.$url.')');
            return '<body>'.$error_msg.'<br>Please try again later.</body>';
        }

        $info = curl_getinfo($ch);

        if (!empty($data)) {
            $headerLength = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $head = trim(substr($data, 0, $headerLength));
            if (strlen($data) > $headerLength) $body = substr($data, $headerLength);
            else $body = '';
            if (false !== strpos($head, "\r\n\r\n")) {
                $headerParts = explode("\r\n\r\n", $head);
                $head = $headerParts[count($headerParts) - 1];
            }

            cc_whmcs_log(0, "Head: ".json_encode($head));

            $head = $this->processHeaders($head);
            $headers = $head['headers'];
            $cookies = $head['cookies'];

            if (empty($cookies))
                $cookies = $_SESSION['cookieCache'];
        } else {
            $headers = array();
            $cookies = '';
            $body = '';
            $this->error('An undefined error occured');
            return '<body>An undefined error occured</body>';
        }

        if (isset($this->cookieArray['PHPSESSID']) && $this->cookieArray['PHPSESSID']) {
            $_SESSION[$this->sid]['sessid'] = $this->cookieArray['PHPSESSID'];
        }
        if ($cookies) {
            $this->debug(0, 'Cookie after:' . json_encode($cookies));
            if (!isset($_SESSION[$this->sid])) $_SESSION[$this->sid] = array();
            if (isset($_SESSION[$this->sid]['sessid'])) {
                if (!strstr($cookies, 'PHPSESSID') && $cookies) $cookies .= ';' . $_SESSION[$this->sid]['sessid'];
                elseif (!strstr($cookies, 'PHPSESSID')) $cookies = $_SESSION[$this->sid]['sessid'];
            }
            $_SESSION[$this->sid]['cookies'] = $cookies;
        }
        if (is_array($cookies)) $this->debug(0, 'Cookie after:' . json_encode($cookies));

        curl_close($ch);

        //remove temporary upload files
        if (count($newfiles) > 0) {
            foreach ($newfiles as $file) {
                @unlink($file);
            }
        }

        $this->headers = $headers;
        $this->data = $data;
        $this->cookies = $cookies;
        $this->body = $body;

        if ($headers['content-type']) {
            $this->type = $headers['content-type'];
        }

        $this->cookies = apply_filters('bridgeHttpRequest_post', $this->cookies);

        $this->debug(0, 'Call process completed in ' . $this->time('delta') . ' microseconds');

        if ($this->follow && isset ($headers['location']) && $headers['location']) {
            $this->debug(0, 'XX: redirect to:'.json_encode($headers));
            $this->debug(0, 'XX: protocol='.$this->_protocol);
            $this->debug(0, 'XX: path='.$this->_path);

            $redir = $headers['location'];

            $main_whmcs_url = parse_url(cc_whmcs_bridge_url());
            $this->debug(0, 'S0: '.json_encode($main_whmcs_url));

            if (strstr($this->_path, '/store/order') === false && strstr($this->_path, '/password/reset/change')) {
                if ($this->os() == 'WINDOWS') {
                    if (strpos($redir, $this->_protocol . '://' . $this->_host . $this->_path) === 0) {
                        //do nothing
                    } elseif (strstr($this->_protocol . '://' . $this->_host . $redir, $this->_protocol . '://' . $this->_host . $this->_path)) {
                        $new_redir = $this->_protocol . '://' . $this->_host . $this->_path;
                        if (strstr($new_redir, $redir) === false) {
                            $new_redir .= $redir;
                        }
                        $redir = $new_redir;
                    } elseif (!strstr($redir, $this->_host)) {
                        $redir = $this->_protocol . '://' . $this->_host . $this->_path . $redir;
                    }
                } else {
                    if (strpos($redir, $this->_protocol . '://' . $this->_host . $this->_path) === 0) {
                        //do nothing
                    } elseif (strstr($this->_protocol . '://' . $this->_host . $redir, $this->_protocol . '://' . $this->_host . $this->_path)) {
                        $redir = $this->_protocol . '://' . $this->_host . $redir;
                    } elseif (((strpos($redir, 'http://') === 0) || (strpos($redir, 'https://') === 0)) && !strstr($redir, $this->_host)) {
                        $this->redirect = true;
                        return $redir;
                    } elseif (!strstr($redir, $this->_host)) {
                        $redir = $this->_protocol . '://' . $this->_host . $this->_path . $redir;
                    }
                }
            } else {
                if (substr($redir, 0, 1) != '/' && stristr($redir, ':208') === false
                    && substr($redir, 0, 4) != 'http')
                    $redir = '/' . $redir;

                $redir_parts = parse_url($redir);
                if (!empty($redir_parts['path'])) {
                    $redir_parts = pathinfo($redir_parts['path']);
                    if (!empty($redir_parts['dirname']))
                        $redir_parts = $redir_parts['dirname'];
                    else
                        $redir_parts = $redir;
                } else
                    $redir_parts = $redir;

                $this->debug(0, "Redir: ".$redir);

                if ((stristr($this->_protocol . '://' . $this->_host . $this->_path, $redir) === false
                || (
                        stristr($redir, $main_whmcs_url['host']) === false &&
                        stristr($redir, $main_whmcs_url['path']) === false
                    )) && strstr($redir, '://') !== false
                ) {
                    // do nothing
                    $bounce = true;
                    $this->debug(0, 'S2: ' . $redir);
                } else if (stristr($redir, ':208') === false
                        && (!empty($main_whmcs_url['path']) && $main_whmcs_url['path'] != $redir_parts)
                        && stristr($redir, 'password/reset') === false
                    && strstr($redir, 'account/') === false
                    && strstr($redir, 'user/') === false
                    && strstr($redir, 'login/challenge') === false
                    && strstr($redir, 'store/') === false
                    && strstr($redir, 'clientarea.php') === false
                    && strstr($redir, 'rp=/login') === false
                        && stristr($redir, '://') === false
                ) {
                    $redir = $this->_host . $this->_path . $redir;
                    $this->debug(0, 'S3: '.$redir);
                } else if (stristr($redir, ':208') !== false) {
                    $bounce = true;
                    $this->debug(0, 'S4: ' . $redir);
                } else if ($redir == '/clientarea.php') {
                    if (empty($rawPost))
                        $bounce = true;
                    if (stristr($this->_path, '/user/accounts') !== false)
                        $redir = $this->_protocol . '://' .$this->_host .'/'. $redir;
                    else
                        $redir = $this->_protocol . '://' .$this->_host .$this->_path. $redir;
                    $this->debug(0, 'S4.1: '.$redir);
                } else {
                    $redir = $this->_host . $redir;
                    $this->debug(0, 'S5: '.$redir);
                }

                if (empty($bounce) && substr($redir, -15) != '/clientarea.php') {
                    $redir = $this->_protocol . '://' . str_replace('//', '/', $redir);
                    $this->debug(0, 'S6: '.$redir);
                }
            }
            $fwd = $this->forceWithRedirectToString($redir);
            if ($fwd) {
                if (strstr($redir, '&')) $redir .= '&';
                elseif (strstr($redir, '?')) $redir .= '&';
                else $redir .= '?';
                $redir .= $fwd;
            }
            $this->debug(0, '[3] Redirect to: ' . $redir);

            if (strstr($redir, 'viewinvoice.php') ||
                (strstr($this->_path, '/store/order') && strstr($redir, 'cart.php')) ||
                (strstr($redir, 'action=details&success')) ||
                !empty($rawPost)
            ) {
                if (empty($bounce)) {
                    $opt = 0;
                    if (strstr($redir, 'action=details&success') || (!empty($rawPost) && !strstr($redir, 'clientarea.php'))) {
                        $newRedir = cc_whmcs_bridge_parse_url($redir, true);
                        $opt = 1;
                    } else {
                        $newRedir = cc_whmcs_bridge_parse_url($redir);
                        $opt = 2;
                    }
                    if (strstr($this->_path, '/store/order') && strstr($redir, 'cart.php')) {
                        $newRedir = str_replace('/store/order', '', $newRedir);
                        $newRedir = cc_whmcs_bridge_parse_url($newRedir);
                        $opt = 3;
                    }

                    $this->debug(0, '[XX - '.$opt.'] New Redirect: ' . $newRedir . ' (' . $redir . ')');
                } else {
                    $newRedir = $redir;
                    $redir = false;
                }

                if ($redir != $newRedir || stristr($redir, '../viewinvoice')) {
                    header('Location:' . $newRedir);
                    die();
                }
            } else if (substr_count($redir, "knowledgebase") > 1 && isset($headers, $headers['location'])) {
                $newRedir = $headers['location'];
                $newRedir = cc_whmcs_bridge_parse_url($newRedir);

                $this->debug(0, '[XX] New Redirect: ' . $newRedir . ' (' . $redir . ')');
                header('Location:' . $newRedir);
                die();
            } else if (strstr($redir, 'cart.php?a=add&domain=register') || strstr($redir, 'cart.php?a=confproduct&i=')
                || strstr($redir, 'cart.php?a=view')
                || strstr($redir, 'cart.php?a=complete')
            ) {
                $newRedir = cc_whmcs_bridge_parse_url($redir);
                header('location: '.$newRedir);
                die();
            } else if (strstr($redir, 'cpsess') || strstr($redir, 'service-name') || stristr($redir, $main_whmcs_url['host']) === false) {
                header('location: '.$redir);
                die();
            } else if (strstr($redir, 'custom_page=reissue') ||
                strstr($redir, 'custom_page=manage_validation') || (strstr($url, 'login') !== false && !isset($this->post['bg']))
            ) {
                $newRedir =  cc_whmcs_bridge_parse_url($redir);
                if ($wordpressPageName) $p = $wordpressPageName;
                else $p = '/';

                $this->debug(0, 'Processing redirect...');

                if (strstr($url, 'login') !== false && class_exists('wpusers') && !empty($this->post['username'])) {
                    $this->debug(0, 'Logging in to WordPress with ' . $this->post['username'] . '/' . $this->post['password']);
                    $wpusers = new wpusers();
                    $wpusers->loginWpUser($this->post['username'], $this->post['password']);
                }

                cc_whmcs_bridge_home($home,$pid,false);

                if (get_option('cc_whmcs_bridge_permalinks') && function_exists('cc_whmcs_bridge_parser_with_permalinks')) {
                    if (substr($home, -1) == '/')
                        $link = substr($home, 0, -1);
                    else
                        $link = $home;
                    $f[] = '/.*\/([a-zA-Z\_]*?).php.(.*?)/';
                    $r[] = $link . '/$1?$2';
                    $f[] = "/([a-zA-Z0-9\_]*?).php.(.*?)/";
                    $r[] = $link . '/$1?$2';
                } else {
                    $f[] = '/.*\/([a-zA-Z\_]*?).php.(.*?)/';
                    $r[] = $home . '?ccce=$1&$2';
                    $f[] = "/([a-zA-Z0-9\_]*?).php.(.*?)/";
                    $r[] = $home . '?ccce=$1&$2';
                }

                $this->debug(0, 'Location [1]: '.$newRedir);

                $newRedir = preg_replace($f, $r, $newRedir, -1, $count);

                $this->debug(0, 'Location [P]: '.$newRedir);

                header('Location:' . $newRedir);

                die();
            }
            if (!$this->repost) $this->post = array();
            $this->countRedirects++;
            if ($this->countRedirects < 10) {
                //if ($redir != $url) {
                return $this->connect($redir, $withHeaders, $withCookies);
                //}
            } else {
                $error_msg = 'ERROR: Too many redirects ' . $url . ' > ' . $headers['location'];
                $this->error($error_msg, E_USER_ERROR);
                return '<body>'.$error_msg.'</body>';
            }
        }

        return $body;
	}
}

if (!class_exists('zHttpRequest')) {
	class zHttpRequest extends bridgeHttpRequest {
	}
}
