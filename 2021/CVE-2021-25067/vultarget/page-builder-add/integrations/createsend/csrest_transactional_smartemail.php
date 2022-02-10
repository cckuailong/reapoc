<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access transactional from the create send API.
 * @author philoye
 *
 */

if (!class_exists('CS_REST_Transactional_SmartEmail')) {
    class CS_REST_Transactional_SmartEmail extends CS_REST_Wrapper_Base {

        /**
         * The client id to use for this mailer. Optional if using a client api key.
         * @var array
         * @access private
         */
        var $_client_id_param;

        /**
         * The base route of the smartemail resource.
         * @var string
         * @access private
         */
        var $_smartemail_base_route;

        /**
         * Constructor.
         * @param $client_id string The client id to send email on behalf of
         *        Optional if using a client api key
         * @param $smartemail_id string The smart email id to access (Ignored for list requests)
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
        $smartemail_id,
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
            $this->set_smartemail_id($smartemail_id);
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
         * Change the smart email id used for calls after construction
         * @param $smartemail_id
         * @access public
         */
        function set_smartemail_id($smartemail_id) {
            $this->_smartemail_base_route = $this->_base_route . 'transactional/smartEmail/' . $smartemail_id;
        }

        /**
         * Gets a list of smart emails
         * @access public
         * @param $options optional array Query params to filter list
         *     This should be an array of the form
         *         array(
         *             "status" => "all|drafts|active"
         *         )
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         *     array(
         *        array (
         *             'ID' => string
         *             'Name' => string
         *             'CreatedAt' => datestring
         *             'Status' => string
         *        )
         *     )
         */
        function get_list($options = array()) {
            $data = array_merge($this->_client_id_param, $options);
            return $this->get_request_with_params($this->_base_route . 'transactional/smartemail', $data);
        }

        /**
         * Sends a new smart transactional email
         * @param array $message The details of the template
         *     This should be an array of the form
         *         array(
         *             'To' => array(
         *                  "First Last <user@example.com>", "another@example.com"
         *             ) optional To recipients
         *             'CC' => array(
         *                  "First Last <user@example.com>", "another@example.com"
         *             ) optional CC recipients
         *             'BCC' => array(
         *                  "First Last <user@example.com>", "another@example.com"
         *             ) optional BCC recipients
         *             'Attachments' => array(
         *                  array(
         *                      "Name" => string
         *                      "Type" => string mime type
         *                      "Content" => string base64-encoded
         *                  )
         *             ) optional
         *             'Data' => array(
         *                  "variable" => "value",
         *                  "variable" => "value",
         *             )
         *         )
         * @param boolean $add_to_list optional. Whether to add all recipients to the list specified for the smart email
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
        function send($message, $add_to_list = true) {
            $data = array_merge($message, array("AddRecipientsToList" => $add_to_list));
            return $this->post_request($this->_smartemail_base_route . '/send.json', $data);
        }

        /**
         * Gets the details of Smart Email
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an array of the form
         *     array(
         *        "SmartEmailID" => string
         *        "Name" => string
         *        "CreatedAt" => string
         *        "Status" => stirng
         *        "Properties" => array (
         *            "From" =. string
         *            "ReplyTo" => string
         *            "Subject" => string
         *            "Content": array(
         *                "HTML": string
         *                "Text": string
         *                "EmailVariables": array(
         *                    "username",
         *                    "reset_token"
         *                ),
         *                "InlineCss": boolean
         *            },
         *            "TextPreviewUrl": string
         *            "HtmlPreviewUrl": string
         *        ),
         *        "AddRecipientsToList": string
         *    }
         */
        function get_details() {
            return $this->get_request($this->_smartemail_base_route);
        }

    }
}
