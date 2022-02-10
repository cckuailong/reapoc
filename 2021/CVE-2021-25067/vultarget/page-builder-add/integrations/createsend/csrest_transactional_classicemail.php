<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access transactional from the create send API.
 * @author philoye
 *
 */
if (!class_exists('CS_REST_Transactional_ClassicEmail')) {
    class CS_REST_Transactional_ClassicEmail extends CS_REST_Wrapper_Base {

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
         * Sends a new classic transactional email
         * @param array $message The details of the template
         *     This should be an array of the form
         *         array(
         *             'From' => string required The From name/email in the form "first last <user@example.com>"
         *             'ReplyTo' => string optional The Reply-To address
         *             'To' => array(
         *                "First Last <user@example.com>", "another@example.com"
         *             ) optional To recipients
         *             'CC' => array(
         *                "First Last <user@example.com>", "another@example.com"
         *             ) optional CC recipients
         *             'BCC' => array(
         *                "First Last <user@example.com>", "another@example.com"
         *             ) optional BCC recipients
         *             'Subject' => string required The subject of the email
         *             'Html' => string The HTML content of the message
         *             'Text' => string optional The text content of the message
         *             'Attachments' => array
         *                "Name" => string required
         *                "Type" => string required
         *                "Content" => string required
         *             ) optional
         *         )
         * @param string $group Optional. Name to group emails by for reporting
         *    For example "Password reset", "Order confirmation"
         * @param array $options optional. Advanced options for sending this email (optional)
         *      This should be an array, each property is optionals
         *          array(
         *            TrackOpens  => whether to track opens, defaults to true
         *            TrackClicks => whether to track clicks, defaults to true
         *            InlineCSS   => whether inline CSS, defaults to true
         *            AddRecipientsToListID => ID of a list to add all recipeints to
         *          )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be the include the details of the action, including a Message ID.
         *      array(
         *          array(
         *              "MessageID" => string
         *              "Recipient" => string
         *              "Status" => string
         *          )
         *      )
         */
        function send($message, $group = NULL, $add_to_list_ID = NULL, $options = array()) {
            $group_param = array( "Group" => $group);
            $add_to_list_param = array( "AddRecipientsToListID" => $add_to_list_ID);
            $data = array_merge($this->_client_id_param, $message, $group_param, $add_to_list_param, $options);
            return $this->post_request($this->_base_route.'transactional/classicemail/send', $data);
        }

        /**
         * Gets the list of Classic Groups
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an array of the form
         *     array(
         *         array(
         *             "Group" => string
         *             "CreatedAt" => string
         *         )
         *     )
         */
        function groups() {
            $data = array_merge($this->_client_id_param);
            return $this->get_request($this->_base_route . 'transactional/classicemail/groups', $data);
        }

    }
}

