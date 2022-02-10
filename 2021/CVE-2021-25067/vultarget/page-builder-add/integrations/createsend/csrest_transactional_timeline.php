<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access transactional from the create send API.
 * @author philoye
 *
 */
if (!class_exists('CS_REST_Transactional_Timeline')) {
    class CS_REST_Transactional_Timeline extends CS_REST_Wrapper_Base {

        /**
         * The client id to use for the timeline. Optional if using a client api key
         * @var array
         * @access private
         */
        var $_client_id_param;

        /**
         * Constructor.
         * @param $client_id string The client id to send email on behalf of
         *        Optional if using a client api key
         * @param $auth_details array Authentication details to use for API calls.
         *        This array must take one of the following forms:
         *        If using OAuth to authenticate:
         *        array(
         *          'access_token' => 'your access token',
         *          'refresh_token' => 'your refresh token')
         *
         *        Or if using an API key:
         *        array('api_key' => 'your api key')
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
        $client_id = NULL,
        $protocol = 'https',
        $debug_level = CS_REST_LOG_NONE,
        $host = 'api.createsend.com',
        $log = NULL,
        $serialiser = NULL,
        $transport = NULL) {
            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
            $this->set_client($client_id);
        }

        /**
         * Change the client id used for calls after construction
         * Only required if using OAuth or an Account level API Key
         * @param $client_id
         * @access public
         */
        function set_client($client_id) {
          $this->_client_id_param = array("clientID" => $client_id);
        }

        /**
         * Gets the list of sent messages
         * @access public
         * @param $params, array Parameters used to filter results
         *     This should be an array of the form
         *         array(
         *             "status" => string delivered|bounced|spam|all
         *             "count" => integer optional, maximum number of results to return in a single request (default: 50, max: 200)
         *             "sentBeforeID" => string optional,  messageID used for pagination, returns emails sent before the specified message
         *             "sentAfterID" => string optional,  messageID used for pagination, returns emails sent after the specified message
         *             "smartEmaiLID" => string optional, smart email to filter by
         *             "group" => string optional, classic group name to filter by
         *         )
         * @return CS_REST_Wrapper_Result A successful response will be an array of the form
         *     array(
         *         array(
         *             "MessageID" => string
         *             "Status" => string
         *             "SentAt" => string
         *             "Recipient" => string
         *             "From" => string
         *             "Subject" => string
         *             "TotalOpens" => integer
         *             "TotalClicks" => integer
         *             "CanBeResent" => boolean
         *             "Group" => string, optional
         *             "SmartEmailID" => string, optional
         *         )
         *     )
         */
        function messages($query = array()) {
            $params = array_merge($this->_client_id_param, $query);
            return $this->get_request_with_params($this->_base_route . 'transactional/messages', $params);
        }

        /**
         * Gets the list of details of a sent message
         * @access public
         * @param $message_id, string Message ID to get the details for
         * @return CS_REST_Wrapper_Result The details of the message
         */
        function details($message_id, $show_details = false) {
            $params = array_merge($this->_client_id_param, array("statistics" => $show_details));
            return $this->get_request_with_params($this->_base_route . 'transactional/messages/' . $message_id, $params);
        }

        /**
         * Resend a sent message
         * @access public
         * @param $message_id, string Message ID to resend
         * @return CS_REST_Wrapper_Result The details of the message
         *      array(
         *          "MessageID" => string
         *          "Recipient" => string
         *          "Status" => string
         *      )
         */
        function resend($message_id) {
            $data = array_merge($this->_client_id_param);
            return $this->post_request($this->_base_route.'transactional/messages/' . $message_id . '/resend', $data);
        }

        /**
         * Gets statistics for sends/bounces/opens/clicks
         * @access public
         * @param $params, array Parameters used to filter results
         *     This should be an array of the form
         *         array(
         *             "from" => iso-8601 date, optional, default 30 days ago
         *             "to" => iso-8601 date, optional, default today
         *             "timezone" => client|utc, optional, how to handle date boundaries
         *             "group" => string optional, classic group name to filter by
         *             "smartEmailID" => string optional. smart email to filter results by
         *         )
         * @return CS_REST_Wrapper_Result A successful response will be an array of the form
         *     array(
         *         array(
         *             "MessageID" => string
         *             "Status" => string
         *             "SentAt" => string
         *             "Recipient" => string
         *             "From" => string
         *             "Subject" => string
         *             "TotalOpens" => integer
         *             "TotalClicks" => integer
         *             "CanBeResent" => boolean
         *             "Group" => string, optional
         *             "SmartEmailID" => string, optional
         *         )
         *     )
         */
        function statistics($query = array()) {
            $params = array_merge($this->_client_id_param, $query);
            return $this->get_request_with_params($this->_base_route.'transactional/statistics', $params);
        }

    }
}

