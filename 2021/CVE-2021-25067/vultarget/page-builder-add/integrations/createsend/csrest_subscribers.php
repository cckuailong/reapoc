<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access a subscribers resources from the create send API.
 * This class includes functions to add and remove subscribers ,
 * along with accessing statistics for a single subscriber
 * @author tobyb
 *
 */
if (!class_exists('CS_REST_Subscribers')) {
    class CS_REST_Subscribers extends CS_REST_Wrapper_Base {

        /**
         * The base route of the subscriber resource.
         * @var string
         * @access private
         */
        var $_subscribers_base_route;

        /**
         * Constructor.
         * @param $list_id string The list id to access (Ignored for create requests)
         * @param $auth_details array Authentication details to use for API calls.
         *        This array must take one of the following forms:
         *        If using OAuth to authenticate:
         *        array(
         *          'access_token' => 'your access token',
         *          'refresh_token' => 'your refresh token')
         *
         *        Or if using an API key:
         *        array('api_key' => 'your api key')
         * @param string $protocol The protocol to use for requests (http|https)
         * @param int $debug_level The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
         * @param string $host The host to send API requests to. There is no need to change this
         * @param CS_REST_Log $log The logger to use. Used for dependency injection
         * @param object|null $serialiser The serialiser to use. Used for dependency injection
         * @param object|null $transport The transport to use. Used for dependency injection
         * @access public
         */
        function __construct (
        $list_id,
        $auth_details,
        $protocol = 'https',
        $debug_level = CS_REST_LOG_NONE,
        $host = 'api.createsend.com',
        $log = NULL,
        $serialiser = NULL,
        $transport = NULL) {

            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
            $this->set_list_id($list_id);

        }

        /**
         * Change the list id used for calls after construction
         * @param $list_id
         * @access public
         */
        function set_list_id($list_id) {
            $this->_subscribers_base_route = $this->_base_route.'subscribers/'.$list_id;
        }

        /**
         * Adds a new subscriber to the specified list
         * @param array $subscriber The subscriber details to use during creation.
         *     This array should be of the form
         *     array (
         *         'EmailAddress' => The new subscribers email address
         *         'Name' => The name of the new subscriber
         *         'CustomFields' => array(
         *             array(
         *                 'Key' => The custom fields personalisation tag
         *                 'Value' => The value for this subscriber
         *             )
         *         )
         *         'Resubscribe' => Whether we should resubscribe this subscriber if they already exist in the list
         *         'RestartSubscriptionBasedAutoResponders' => Whether we should restart subscription based auto responders which are sent when the subscriber first subscribes to a list.
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function add($subscriber) {
            return $this->post_request($this->_subscribers_base_route.'.json', $subscriber);
        }

        /**
         * Updates an existing subscriber (email, name, state, or custom fields) in the specified list.
         * The update is performed even for inactive subscribers, but will return an error in the event of the
         * given email not existing in the list.
          * @param string $email The email address of the susbcriber to be updated
         * @param array $subscriber The subscriber details to use for the update. Empty parameters will remain unchanged
         *     This array should be of the form
         *     array (
         *         'EmailAddress' => The new  email address
         *         'Name' => The name of the subscriber
         *         'CustomFields' => array(
         *             array(
         *                 'Key' => The custom fields personalisation tag
         *                 'Value' => The value for this subscriber
         *                 'Clear' => true/false (pass true to remove this custom field. in the case of a [multi-option, select many] field, pass an option in the 'Value' field to clear that option or leave Value blank to remove all options)
         *             )
         *         )
         *         'Resubscribe' => Whether we should resubscribe this subscriber if they already exist in the list
         *         'RestartSubscriptionBasedAutoResponders' => Whether we should restart subscription based auto responders which are sent when the subscriber first subscribes to a list.
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function update($email, $subscriber) {
            return $this->put_request($this->_subscribers_base_route.'.json?email='.urlencode($email), $subscriber);
        }

        /**
         * Imports an array of subscribers into the current list
         * @param array $subscribers An array of subscribers to import.
         *     This array should be of the form
         *     array (
         *         array (
         *             'EmailAddress' => The new subscribers email address
         *             'Name' => The name of the new subscriber
         *             'CustomFields' => array(
         *                 array(
         *                     'Key' => The custom fields personalisation tag
         *                     'Value' => The value for this subscriber
         *                     'Clear' => true/false (pass true to remove this custom field. in the case of a [multi-option, select many] field, pass an option in the 'Value' field to clear that option or leave Value blank to remove all options)
         *                 )
         *             )
         *         )
         *     )
         * @param bool $resubscribe Whether we should resubscribe any existing subscribers
         * @param bool $queueSubscriptionBasedAutoResponders By default, subscription based auto responders do not trigger during an import. Pass a value of true to override this behaviour
         * @param bool $restartSubscriptionBasedAutoResponders By default, subscription based auto responders will not be restarted
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *     'TotalUniqueEmailsSubmitted' => The number of unique emails submitted in the call
         *     'TotalExistingSubscribers' => The number of subscribers who already existed in the list
         *     'TotalNewSubscribers' => The number of new subscriptions to the list
         *     'DuplicateEmailsInSubmission' => array<string> The emails which appeared more than once in the batch
         *     'FailureDetails' => array (
         *         {
         *             'EmailAddress' => The email address which failed
         *             'Code' => The Create Send API Error code
         *             'Message' => The reason for the failure
         *         }
         *     )
         * }
         *
         */
        function import($subscribers, $resubscribe, $queueSubscriptionBasedAutoResponders = false, $restartSubscriptionBasedAutoResponders = false) {
            $subscribers = array(
    		    'Resubscribe' => $resubscribe,
    			'QueueSubscriptionBasedAutoResponders' => $queueSubscriptionBasedAutoResponders,
    		    'Subscribers' => $subscribers,
                'RestartSubscriptionBasedAutoresponders' => $restartSubscriptionBasedAutoResponders
            );
            
            return $this->post_request($this->_subscribers_base_route.'/import.json', $subscribers);
        }

        /**
         * Gets a subscriber details, including custom fields
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *     'EmailAddress' => The subscriber email address
         *     'Name' => The subscribers name
         *     'Date' => The date the subscriber was added to the list
         *     'State' => The current state of the subscriber
         *     'CustomFields' => array(
         *         {
         *             'Key' => The custom fields personalisation tag
         *             'Value' => The custom field value for this subscriber
         *         }
         *     )
         * }
         */
        function get($email) {
            return $this->get_request($this->_subscribers_base_route.'.json?email='.urlencode($email));
        }

        /**
         * Gets the sending history to a specific subscriber
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         ID => The id of the email which was sent
         *         Type => 'Campaign'
         *         Name => The name of the email
         *         Actions => array(
         *             {
         *                 Event => The type of action (Click, Open, Unsubscribe etc)
         *                 Date => The date the event occurred
         *                 IPAddress => The IP that the event originated from
         *                 Detail => Any available details about the event i.e the URL for clicks
         *             }
         *         )
         *     }
         * )
         */
        function get_history($email) {
            return $this->get_request($this->_subscribers_base_route.'/history.json?email='.urlencode($email));
        }

        /**
         * Unsubscribes the given subscriber from the current list
         * @param string $email The email address to unsubscribe
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function unsubscribe($email) {
            // We need to build the subscriber data structure.
            $email = array(
    		    'EmailAddress' => $email 
            );
            
            return $this->post_request($this->_subscribers_base_route.'/unsubscribe.json', $email);
        }

        /**
         * deletes the given subscriber from the current list
         * @param string $email The email address to delete
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function delete($email) {
            return $this->delete_request($this->_subscribers_base_route.'.json?email='.urlencode($email));
        }
    }
}