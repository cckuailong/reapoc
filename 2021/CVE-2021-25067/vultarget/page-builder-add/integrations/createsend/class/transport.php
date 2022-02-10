<?php

defined('CS_REST_GET') or define('CS_REST_GET', 'GET');
defined('CS_REST_POST') or define('CS_REST_POST', 'POST');
defined('CS_REST_PUT') or define('CS_REST_PUT', 'PUT');
defined('CS_REST_DELETE') or define('CS_REST_DELETE', 'DELETE');
if (false === defined('CS_REST_SOCKET_TIMEOUT')) {
    define('CS_REST_SOCKET_TIMEOUT', 10);
}
if (false === defined('CS_REST_CALL_TIMEOUT')) {
    define('CS_REST_CALL_TIMEOUT', 10);
}

if(!function_exists("CS_REST_TRANSPORT_get_available")) {    
    function CS_REST_TRANSPORT_get_available($requires_ssl, $log) {
        if(function_exists('curl_init') && function_exists('curl_exec')) {
            return new CS_REST_CurlTransport($log);
        } else if(CS_REST_TRANSPORT_can_use_raw_socket($requires_ssl)) {
            return new CS_REST_SocketTransport($log);
        } else { 
            $log->log_message('No transport is available', __FUNCTION__, CS_REST_LOG_ERROR);
            trigger_error('No transport is available.'.
                ($requires_ssl ? ' Try using non-secure (http) mode or ' : ' Please ').
                'ensure the cURL extension is loaded', E_USER_ERROR);
        }    
    }
}

if(!function_exists("CS_REST_TRANSPORT_can_use_raw_socket")) {
    function CS_REST_TRANSPORT_can_use_raw_socket($requires_ssl) {
        if(function_exists('fsockopen')) {
            if($requires_ssl) {
                return extension_loaded('openssl');
            }

            return true;
        }

        return false;
    }
}

if (!class_exists('CS_REST_BaseTransport')) {
    class CS_REST_BaseTransport {
        
        var $_log;
        
        function __construct($log) {
            $this->_log = $log;
        }
        
        function split_and_inflate($response, $may_be_compressed) {        
            $ra = explode("\r\n\r\n", $response);
            
            $result = array_pop($ra);
            $headers = array_pop($ra);
            
            if($may_be_compressed && preg_match('/^Content-Encoding:\s+gzip\s+$/im', $headers)) {        
                $original_length = strlen($response);
                $result = gzinflate(substr($result, 10, -8));
        
                $this->_log->log_message('Inflated gzipped response: '.$original_length.' bytes ->'.
                    strlen($result).' bytes', get_class(), CS_REST_LOG_VERBOSE);
            }
            
            return array($headers, $result); 
        }

    }
}
/**
 * Provide HTTP request functionality via cURL extensions
 *
 * @author tobyb
 * @since 1.0
 */
if (!class_exists('CS_REST_CurlTransport')) {
    class CS_REST_CurlTransport extends CS_REST_BaseTransport {

        var $_curl_zlib;

        function __construct($log) {
            parent::__construct($log);
            
            $curl_version = curl_version();
            $this->_curl_zlib = isset($curl_version['libz_version']);
        }

        /**
         * @return string The type of transport used
         */
        function get_type() {
            return 'cURL';
        }

        function make_call($call_options) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $call_options['route']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            $headers = array();
            $headers[] = 'Content-Type: '.$call_options['contentType'];
            

            if (array_key_exists('authdetails', $call_options) &&
                isset($call_options['authdetails'])) {
                if (array_key_exists('username', $call_options['authdetails']) &&
                    array_key_exists('password', $call_options['authdetails'])) {
                    # Authenticating using basic auth for retrieving user's API key.
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    curl_setopt($ch, CURLOPT_USERPWD, $call_options['authdetails']['username'].':'.$call_options['authdetails']['password']);
                } elseif (array_key_exists('access_token', $call_options['authdetails'])) {
                    # Authenticating using OAuth.
                    $access_token = $call_options['authdetails']['access_token'];
                    $headers[] = 'Authorization: Bearer '.$access_token;
                } elseif (array_key_exists('api_key', $call_options['authdetails'])) {
                    # Authenticating using an API key.
                    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                    $api_key = $call_options['authdetails']['api_key'];
                    curl_setopt($ch, CURLOPT_USERPWD, $api_key.':nopass');
                }
            }

            curl_setopt($ch, CURLOPT_USERAGENT, $call_options['userAgent']);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CS_REST_SOCKET_TIMEOUT);
            curl_setopt($ch, CURLOPT_TIMEOUT, CS_REST_CALL_TIMEOUT);

            $inflate_response = false;
            if($this->_curl_zlib) {
                $this->_log->log_message('curl+zlib support available. Requesting gzipped response.',
                    get_class($this), CS_REST_LOG_VERBOSE);
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            } else if(function_exists('gzinflate')) {
                $headers[] = 'Accept-Encoding: gzip';
                $inflate_response = true;
            }
            
            if($call_options['protocol'] === 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

                if(strlen(ini_get('curl.cainfo')) === 0) {
                    curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
                }
            }

            switch($call_options['method']) {
                case CS_REST_PUT:
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, CS_REST_PUT);
                    $headers[] = 'Content-Length: '.strlen($call_options['data']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $call_options['data']);
                    break;
                case CS_REST_POST:
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, isset($call_options['data']) ? $call_options['data'] : '');
                    break;
                case CS_REST_DELETE:
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, CS_REST_DELETE);
                    break;
            }
            
            if(count($headers) > 0) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }

            $response = curl_exec($ch);

            if(!$response && $response !== '') {
                $this->_log->log_message('Error making request with curl_error: '.curl_errno($ch),
                    get_class($this), CS_REST_LOG_ERROR);

                require_once dirname(__FILE__).'/exceptions.php';
                throw new CurlException(curl_error($ch), curl_errno($ch));
            }
            
            list( $headers, $result ) = $this->split_and_inflate($response, $inflate_response);
            
            $this->_log->log_message('API Call Info for '.$call_options['method'].' '.
            curl_getinfo($ch, CURLINFO_EFFECTIVE_URL).': '.curl_getinfo($ch, CURLINFO_SIZE_UPLOAD).
    		    ' bytes uploaded. '.curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD).' bytes downloaded'.
    		    ' Total time (seconds): '.curl_getinfo($ch, CURLINFO_TOTAL_TIME), 
            get_class($this), CS_REST_LOG_VERBOSE);

            $result = array(
    			'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
    		    'response' => $result
            );

            curl_close($ch);

            return $result;
        }
    }
}

if (!class_exists('CS_REST_SocketWrapper')) {
    class CS_REST_SocketWrapper {
        var $socket;

        function open($domain, $port) {
            $this->socket = fsockopen($domain, $port, $errno, $errstr, CS_REST_SOCKET_TIMEOUT);

            if(!$this->socket) {
                die('Error making request with '.$errno.': '.$errstr);
                return false;
            } else if(function_exists('stream_set_timeout')) {
                stream_set_timeout($this->socket, CS_REST_SOCKET_TIMEOUT);
            }

            return true;
        }

        function write($data) {
            fwrite($this->socket, $data);
        }

        function read() {
            ob_start();
            fpassthru($this->socket);

            return ob_get_clean();
        }

        function close() {
            fclose($this->socket);
        }
    }
}

if (!class_exists('CS_REST_SocketTransport')) {
    class CS_REST_SocketTransport extends CS_REST_BaseTransport {

        var $_socket_wrapper;

        function __construct($log, $socket_wrapper = NULL) {
            parent::__construct($log);

            if(is_null($socket_wrapper)) {
                $socket_wrapper = new CS_REST_SocketWrapper();
            }

            $this->_socket_wrapper = $socket_wrapper;
        }

        /**
         * @return string The type of transport used
         */
        function get_type() {
            return 'Socket';
        }

        function make_call($call_options) {
            $start_host = strpos($call_options['route'], $call_options['host']);
            $host_len = strlen($call_options['host']);

            $domain = substr($call_options['route'], $start_host, $host_len);
            $host = $domain;
            $path = substr($call_options['route'], $start_host + $host_len);
            $protocol = substr($call_options['route'], 0, $start_host);
            $port = 80;

            $this->_log->log_message('Creating socket to '.$domain.' over '.$protocol.' for request to '.$path,
                get_class($this), CS_REST_LOG_VERBOSE);

            if($protocol === 'https://') {
                $domain = 'ssl://'.$domain;
                $port = 443;
            }

            if($this->_socket_wrapper->open($domain, $port)) {
                $inflate_response = function_exists('gzinflate');
                
                $request = $this->_build_request($call_options, $host, $path, $inflate_response);
                $this->_log->log_message('Sending <pre>'.$request.'</pre> down the socket',
                get_class($this), CS_REST_LOG_VERBOSE);
                 
                $this->_socket_wrapper->write($request);
                $response = $this->_socket_wrapper->read();
                $this->_socket_wrapper->close();
                	
                $this->_log->log_message('API Call Info for '.$call_options['method'].' '.
                $call_options['route'].': '.strlen($request).
    	            ' bytes uploaded. '.strlen($response).' bytes downloaded', 
                get_class($this), CS_REST_LOG_VERBOSE);
                	
                list( $headers, $result ) = $this->split_and_inflate($response, $inflate_response);
                    
                $this->_log->log_message('Received headers <pre>'.$headers.'</pre>',
                    get_class($this), CS_REST_LOG_VERBOSE);
                	
                return array(
    			    'code' => $this->_get_status_code($headers),
    			    'response' => trim($result)
                );
            }
        }

        function _get_status_code($headers) {
            if (preg_match('%^\s*HTTP/1\.1 (?P<code>\d{3})%', $headers, $regs)) {
                $this->_log->log_message('Got HTTP Status Code: '.$regs['code'],
                get_class($this), CS_REST_LOG_VERBOSE);
                return $regs['code'];
            }

            $this->_log->log_message('Failed to get HTTP status code from request headers <pre>'.$headers.'</pre>',
                get_class($this), CS_REST_LOG_ERROR);
            trigger_error('Failed to get HTTP status code from request', E_USER_ERROR);        
        }

        function _build_request($call_options, $host, $path, $accept_gzip) {
            $request_auth_details = '';

            if (array_key_exists('authdetails', $call_options)) {
                if (array_key_exists('username', $call_options['authdetails']) &&
                    array_key_exists('password', $call_options['authdetails'])) {
                    # Authenticating using basic auth for retrieving user's API key.
                    $request_auth_details .= 'Authorization: Basic '.base64_encode($call_options['authdetails']['username'].':'.$call_options['authdetails']['password'])."\n";
                } elseif (array_key_exists('access_token', $call_options['authdetails'])) {
                    # Authenticating using OAuth.
                    $access_token = $call_options['authdetails']['access_token'];
                    $request_auth_details .= 'Authorization: Bearer '.$access_token."\n";
                } elseif (array_key_exists('api_key', $call_options['authdetails'])) {
                    # Authenticating using an API key.
                    $api_key = $call_options['authdetails']['api_key'];
                    $request_auth_details .= 'Authorization: Basic '.base64_encode($api_key.':nopass')."\n";
                }
            }

            $request =
    $call_options['method'].' '.$path." HTTP/1.1\n".
    'Host: '.$host."\n".
    $request_auth_details.
    'User-Agent: '.$call_options['userAgent']."\n".
    "Connection: Close\n".
    'Content-Type: '.$call_options['contentType']."\n";

            if($accept_gzip) {
                $request .=
    "Accept-Encoding: gzip\n";           
            }

            if(isset($call_options['data'])) {
                $request .=
    'Content-Length: '.strlen($call_options['data'])."\n\n".
    $call_options['data'];
            }

            return $request."\n\n";
        }
    }
}