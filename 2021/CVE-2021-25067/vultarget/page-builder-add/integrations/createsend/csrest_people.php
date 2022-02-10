<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access the person resources from the create send API.
 * This class includes functions to add and remove people,
 * along with getting details for a single person
 * @author tobyb
 *
 */
if (!class_exists('CS_REST_People')) {
    class CS_REST_People extends CS_REST_Wrapper_Base {

        /**
         * The base route of the people resource.
         * @var string
         * @access private
         */
        var $_people_base_route;

        /**
         * Constructor.
         * @param $client_id string The client id that the people belong to
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
        $client_id,
        $auth_details,
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
            $this->_people_base_route = $this->_base_route.'clients/'.$client_id . '/people';
        }

        /**
         * Adds a new person to the specified client
         * @param array $person The person details to use during creation.
         *     This array should be of the form
         *     array (
         *         'EmailAddress' => The new person email address
         *         'Name' => The name of the new person
         *         'AccessLevel' => The access level of the new person. See http://www.campaignmonitor.com/api/clients/#setting_access_details for details
         *         'Password' => (optional) if not specified, an invitation will be sent to the person by email
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function add($person) {
            return $this->post_request($this->_people_base_route.'.json', $person);
        }

        /**
         * Updates details for an existing person associated with the specified client.
    	 * @param string $email The email address of the person to be updated
         * @param array $person The updated person details to use for the update. 
         *     This array should be of the form
         *     array (
         *         'EmailAddress' => The new  email address
         *         'Name' => The name of the person
         *         'AccessLevel' => the access level of the person
    	 *         'Password' => (optional) if specified, changes the password to the specified value
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function update($email, $person) {
            return $this->put_request($this->_people_base_route.'.json?email='.urlencode($email), $person);
        }

        /**
         * Gets the details for a specific person
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *     'EmailAddress' => The email address of the person
         *     'Name' => The name of the person
         *     'Status' => The status of the person
         *     'AccessLevel' => The access level of the person     
         *     )
         * }
         */
        function get($email) {
            return $this->get_request($this->_people_base_route.'.json?email='.urlencode($email));
        }


        /**
         * deletes the given person from the current client
         * @param string $email The email address of the person to delete
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function delete($email) {
            return $this->delete_request($this->_people_base_route.'.json?email='.urlencode($email));
        }
    }
}