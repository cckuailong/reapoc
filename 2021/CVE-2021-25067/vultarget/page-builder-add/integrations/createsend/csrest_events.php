<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to send event data to the create send API.
 * @author cameronn
 *
 */
if (!class_exists('CS_REST_Events')) {
    class CS_REST_Events extends CS_REST_Wrapper_Base {

        /**
         * The base route of the clients resource.
         * @var string
         * @access private
         */
        var $_events_base_route;


        /**
         * Constructor.
         *
         * @param $auth_details array Authentication details to use for API calls.
         *        This array must take one of the following forms:
         *        If using OAuth to authenticate:
         *        array(
         *          'access_token' => 'your access token',
         *          'refresh_token' => 'your refresh token')
         *
         *        Or if using an API key:
         *        array('api_key' => 'your api key')
         * @param $client_id string The client id to send event to
         * @param $protocol string The protocol to use for requests (http|https)
         * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
         * @param $host string The host to send API requests to. There is no need to change this
         * @param $log CS_REST_Log The logger to use. Used for dependency injection
         * @param $serialiser The serialiser to use. Used for dependency injection
         * @param $transport The transport to use. Used for dependency injection
         * @access public
         */
        function __construct (
        $auth_details,
        $client_id,
        $protocol = 'https',
        $debug_level = CS_REST_LOG_NONE,
        $host = 'api.createsend.com',
        $log = NULL,
        $serialiser = NULL,
        $transport = NULL) {
            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
            $this->set_client_id($client_id);
        }


        /**
         * Change the client id used for calls after construction
         * @param $client_id
         * @access public
         */
        function set_client_id($client_id) {
            if (!isset($client_id)) {
                trigger_error('$client_id needs to be set');
            }
            $this->_events_base_route = $this->_base_route.'events/'.$client_id.'/';
        }

        /**
         * Tracks an event
         * @param string $email required email in the form "user@example.com"
         * 
         * @param string $event_name. Name to group events by for reporting max length 1000 
         *    For example "Page View", "Order confirmation"
         *
         * @param array $data optional. Event payload.
         *      This should be an array, with details of the event
         *          array(
         *              'RandomFieldObject'  => array(
         *                                      'Example'' => 'test'
         *                                  ),
         *              'RandomFieldURL' => 'Example',
         *              'RandomArray' => array(1,3,5,6,7),
         *          )
         *
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will include an Event ID.
         *      array(
         *          array(
         *              'EventID' => 'string'
         *          )
         *      )
         */
        function track($email, $event_name, $data = NULL) {
            if (!isset($email)) {
                trigger_error('$email needs to be set');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                trigger_error('$email needs to be a valid email address');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (!isset($event_name)) {
                trigger_error('$event_name needs to be set');
                return new CS_REST_Wrapper_Result(null, 400);
            }
            if (strlen($event_name) > 1000 ) {
                trigger_error('$event_name needs to be shorter, max length is 1000 character');
                return new CS_REST_Wrapper_Result(null, 400);
            }   
            if (isset($data)) {
                if (!is_array($data)){
                trigger_error('$data needs to be a valid array');
                return new CS_REST_Wrapper_Result(null, 400);
                } 
            }
            $payload = array('ContactID' => array('Email' => $email), 'EventName' => $event_name, 'Data' => $data);
            return $this->post_request($this->_events_base_route. 'track', $payload);
        }
    }
}

