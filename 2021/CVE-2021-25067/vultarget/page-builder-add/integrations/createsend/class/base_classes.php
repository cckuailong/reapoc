<?php

require_once dirname(__FILE__).'/serialisation.php';
require_once dirname(__FILE__).'/transport.php';
require_once dirname(__FILE__).'/log.php';

defined('CS_REST_WRAPPER_VERSION') or define('CS_REST_WRAPPER_VERSION', '5.1.2');
defined('CS_HOST') or define('CS_HOST', 'api.createsend.com');
defined('CS_OAUTH_BASE_URI') or define('CS_OAUTH_BASE_URI', 'https://'.CS_HOST.'/oauth');
defined('CS_OAUTH_TOKEN_URI') or define('CS_OAUTH_TOKEN_URI', CS_OAUTH_BASE_URI.'/token');
defined('CS_REST_WEBHOOK_FORMAT_JSON') or define('CS_REST_WEBHOOK_FORMAT_JSON', 'json');
defined('CS_REST_WEBHOOK_FORMAT_XML') or define('CS_REST_WEBHOOK_FORMAT_XML', 'xml');

/**
 * A general result object returned from all Campaign Monitor API calls.
 * @author tobyb
 *
 */
if (!class_exists('CS_REST_Wrapper_Result')) {
    class CS_REST_Wrapper_Result {
        /**
         * The deserialised result of the API call
         * @var mixed
         */
        var $response;

        /**
         * The http status code of the API call
         * @var int
         */
        var $http_status_code;

        function __construct($response, $code) {
            $this->response = $response;
            $this->http_status_code = $code;
        }

        /**
         * Can be used to check if a call to the api resulted in a successful response.
         * @return boolean False if the call failed. Check the response property for the failure reason.
         * @access public
         */
        function was_successful() {
            return $this->http_status_code >= 200 && $this->http_status_code < 300;
        }
    }
}
/**
 * Base class for the create send PHP wrapper.
 * This class includes functions to access the general data,
 * i.e timezones, clients and getting your API Key from username and password
 * @author tobyb
 *
 */
if (!class_exists('CS_REST_Wrapper_Base')) {
    class CS_REST_Wrapper_Base {
        /**
         * The protocol to use while accessing the api
         * @var string http or https
         * @access private
         */
        var $_protocol;

        /**
         * The base route of the create send api.
         * @var string
         * @access private
         */
        var $_base_route;

        /**
         * The serialiser to use for serialisation and deserialisation
         * of API request and response data
         * @var CS_REST_JsonSerialiser or CS_REST_XmlSerialiser
         * @access private
         */
        var $_serialiser;

        /**
         * The transport to use to send API requests
         * @var CS_REST_CurlTransport or CS_REST_SocketTransport or your own custom transport.
         * @access private
         */
        var $_transport;

        /**
         * The logger to use for debugging of all API requests
         * @var CS_REST_Log
         * @access private
         */
        var $_log;

        /**
         * The default options to use for each API request.
         * These can be overridden by passing in an array as the call_options argument
         * to a single api request.
         * Valid options are:
         *
         * deserialise boolean:
         *     Set this to false if you want to get the raw response.
         *     This can be useful if your passing json directly to javascript.
         *
         * While there are clearly other options there is no need to change them.
         * @var array
         * @access private
         */
        var $_default_call_options;

        /**
         * Constructor.
         * @param $auth_details array Authentication details to use for API calls.
         *        This array must take one of the following forms:
         *        If using OAuth to authenticate:
         *        array(
         *          'access_token' => 'your access token',
         *          'refresh_token' => 'your refresh token')
         *
         *        Or if using an API key:
         *        array('api_key' => 'your api key')
         *
         *        Note that this method will continue to work in the deprecated
         *        case when $auth_details is passed in as a string containing an
         *        API key.
         * @param $protocol string The protocol to use for requests (http|https)
         * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
         * @param $host string The host to send API requests to. There is no need to change this
         * @param $log CS_REST_Log The logger to use. Used for dependency injection
         * @param $serialiser The serialiser to use. Used for dependency injection
         * @param $transport The transport to use. Used for dependency injection
         * @access public
         */
        function __construct(
            $auth_details,
            $protocol = 'https',
            $debug_level = CS_REST_LOG_NONE,
            $host = CS_HOST,
            $log = NULL,
            $serialiser = NULL,
            $transport = NULL) {

            if (is_string($auth_details)) {
                # If $auth_details is a string, assume it is an API key
                $auth_details = array('api_key' => $auth_details);
            }

            $this->_log = is_null($log) ? new CS_REST_Log($debug_level) : $log;

            $this->_protocol = $protocol;
            $this->_base_route = $protocol.'://'.$host.'/api/v3.1/';

            $this->_log->log_message('Creating wrapper for '.$this->_base_route, get_class($this), CS_REST_LOG_VERBOSE);

            $this->_transport = is_null($transport) ?
                CS_REST_TRANSPORT_get_available($this->is_secure(), $this->_log) :
                $transport;

            $transport_type = method_exists($this->_transport, 'get_type') ? $this->_transport->get_type() : 'Unknown';
            $this->_log->log_message('Using '.$transport_type.' for transport', get_class($this), CS_REST_LOG_WARNING);

            $this->_serialiser = is_null($serialiser) ?
                CS_REST_SERIALISATION_get_available($this->_log) : $serialiser;

            $this->_log->log_message('Using '.$this->_serialiser->get_type().' json serialising', get_class($this), CS_REST_LOG_WARNING);

            $this->_default_call_options = array (
                'authdetails' => $auth_details,
                'userAgent' => 'CS_REST_Wrapper v'.CS_REST_WRAPPER_VERSION.
                    ' PHPv'.phpversion().' over '.$transport_type.' with '.$this->_serialiser->get_type(),
                'contentType' => 'application/json; charset=utf-8',
                'deserialise' => true,
                'host' => $host,
                'protocol' => $protocol
            );
        }

        /**
         * Refresh the current OAuth token using the current refresh token.
         * @access public
         */
        function refresh_token() {
            if (!isset($this->_default_call_options['authdetails']) ||
                !isset($this->_default_call_options['authdetails']['refresh_token'])) {
                trigger_error(
                    'Error refreshing token. There is no refresh token set on this object.',
                    E_USER_ERROR);
                return array(NULL, NULL, NULL);
            }
            $body = "grant_type=refresh_token&refresh_token=".urlencode(
                $this->_default_call_options['authdetails']['refresh_token']);
            $options = array('contentType' => 'application/x-www-form-urlencoded');
            $wrap = new CS_REST_Wrapper_Base(
                NULL, 'https', CS_REST_LOG_NONE, CS_HOST, NULL,
                new CS_REST_DoNothingSerialiser(), NULL);

            $result = $wrap->post_request(CS_OAUTH_TOKEN_URI, $body, $options);
            if ($result->was_successful()) {
                $access_token = $result->response->access_token;
                $expires_in = $result->response->expires_in;
                $refresh_token = $result->response->refresh_token;
                $this->_default_call_options['authdetails'] = array(
                    'access_token' => $access_token,
                    'refresh_token' => $refresh_token
                );
                return array($access_token, $expires_in, $refresh_token);
            } else {
                trigger_error(
                    'Error refreshing token. '.$result->response->error.': '.$result->response->error_description,
                    E_USER_ERROR);
                return array(NULL, NULL, NULL);
            }
        }

        /**
         * @return boolean True if the wrapper is using SSL.
         * @access public
         */
        function is_secure() {
            return $this->_protocol === 'https';
        }

        function put_request($route, $data, $call_options = array()) {
            return $this->_call($call_options, CS_REST_PUT, $route, $data);
        }

        function post_request($route, $data, $call_options = array()) {
            return $this->_call($call_options, CS_REST_POST, $route, $data);
        }

        function delete_request($route, $call_options = array()) {
            return $this->_call($call_options, CS_REST_DELETE, $route);
        }

        function get_request($route, $call_options = array()) {
            return $this->_call($call_options, CS_REST_GET, $route);
        }

        function get_request_with_params($route, $params) {
          if(!is_null($params)) {
            # http_build_query coerces booleans to 1 and 0, not helpful
            foreach($params as $key=>$value) {
              if(is_bool($value)) {
                $params[$key] = ($value) ? 'true' : 'false';
              }
            }
            $route = $route . '?' . http_build_query($params);
          }
          return $this->get_request($route);
        }

        function get_request_paged($route, $page_number, $page_size, $order_field, $order_direction,
            $join_char = '&') {
            if(!is_null($page_number)) {
                $route .= $join_char.'page='.$page_number;
                $join_char = '&';
            }

            if(!is_null($page_size)) {
                $route .= $join_char.'pageSize='.$page_size;
                $join_char = '&';
            }

            if(!is_null($order_field)) {
                $route .= $join_char.'orderField='.$order_field;
                $join_char = '&';
            }

            if(!is_null($order_direction)) {
                $route .= $join_char.'orderDirection='.$order_direction;
                $join_char = '&';
            }

            return $this->get_request($route);
        }

        /**
         * Internal method to make a general API request based on the provided options
         * @param $call_options
         * @access private
         */
        function _call($call_options, $method, $route, $data = NULL) {
            $call_options['route'] = $route;
            $call_options['method'] = $method;

            if(!is_null($data)) {
                $call_options['data'] = $this->_serialiser->serialise($data);
            }

            $call_options = array_merge($this->_default_call_options, $call_options);
            $this->_log->log_message('Making '.$call_options['method'].' call to: '.$call_options['route'], get_class($this), CS_REST_LOG_WARNING);

            $call_result = $this->_transport->make_call($call_options);

            $this->_log->log_message('Call result: <pre>'.var_export($call_result, true).'</pre>',
                get_class($this), CS_REST_LOG_VERBOSE);

            if($call_options['deserialise']) {
                $call_result['response'] = $this->_serialiser->deserialise($call_result['response']);
            }

            return new CS_REST_Wrapper_Result($call_result['response'], $call_result['code']);
        }
    }
}