<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access a clients resources from the create send API.
 * This class includes functions to create and edit clients,
 * along with accessing lists of client specific resources e.g campaigns
 * @author tobyb
 *
 */
if (!class_exists('CS_REST_Clients')) {
    class CS_REST_Clients extends CS_REST_Wrapper_Base {

        /**
         * The base route of the clients resource.
         * @var string
         * @access private
         */
        var $_clients_base_route;

        /**
         * Constructor.
         * @param $client_id string The client id to access (Ignored for create requests)
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
        function __construct(
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
            $this->_clients_base_route = $this->_base_route.'clients/'.$client_id.'/';
        }

        /**
         * Gets a list of sent campaigns for the current client
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         'WebVersionURL' => The web version url of the campaign
         *         'WebVersionTextURL' => The web version url of the text version of the campaign
         *         'CampaignID' => The id of the campaign
         *         'Subject' => The campaign subject
         *         'Name' => The name of the campaign
         *         'FromName' => The from name for the campaign
         *         'FromEmail' => The from email address for the campaign
         *         'ReplyTo' => The reply to email address for the campaign
         *         'SentDate' => The sent data of the campaign
         *         'TotalRecipients' => The number of recipients of the campaign
         *     }
         * )
         */
        function get_campaigns() {
            return $this->get_request($this->_clients_base_route.'campaigns.json');
        }

        /**
         * Gets a list of scheduled campaigns for the current client
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         'CampaignID' => The id of the campaign
         *         'Name' => The name of the campaign
         *         'Subject' => The subject of the campaign
         *         'FromName' => The from name for the campaign
         *         'FromEmail' => The from email address for the campaign
         *         'ReplyTo' => The reply to email address for the campaign
         *         'DateCreated' => The date the campaign was created
         *         'PreviewURL' => The preview url of the campaign
         *         'PreviewTextURL' => The preview url of the text version of the campaign
         *         'DateScheduled' => The date the campaign is scheduled to be sent
         *         'ScheduledTimeZone' => The time zone in which the campaign is scheduled to be sent at 'DateScheduled'
         *     }
         * )
         */
        function get_scheduled() {
            return $this->get_request($this->_clients_base_route.'scheduled.json');
        }

        /**
         * Gets a list of draft campaigns for the current client
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         'CampaignID' => The id of the campaign
         *         'Name' => The name of the campaign
         *         'Subject' => The subject of the campaign
         *         'FromName' => The from name for the campaign
         *         'FromEmail' => The from email address for the campaign
         *         'ReplyTo' => The reply to email address for the campaign
         *         'DateCreated' => The date the campaign was created
         *         'PreviewURL' => The preview url of the draft campaign
         *         'PreviewTextURL' => The preview url of the text version of the campaign
         *     }
         * )
         */
        function get_drafts() {
            return $this->get_request($this->_clients_base_route.'drafts.json');
        }

        /**
         * Gets all subscriber lists the current client has created
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         'ListID' => The id of the list
         *         'Name' => The name of the list
         *     }
         * )
         */
        function get_lists() {
            return $this->get_request($this->_clients_base_route.'lists.json');
        }

        /**
         * Gets the lists across a client to which a subscriber with a particular
         * email address belongs.
         * @param string $email_address Subscriber's email address.
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         'ListID' => The id of the list
         *         'ListName' => The name of the list
         *         'SubscriberState' => The state of the subscriber in the list
         *         'DateSubscriberAdded' => The date the subscriber was added
         *     }
         * )
         */
        function get_lists_for_email($email_address) {
            return $this->get_request($this->_clients_base_route . 
              'listsforemail.json?email='.urlencode($email_address));
        }

        /**
         * Gets all list segments the current client has created
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         'ListID' => The id of the list owning this segment
         *         'SegmentID' => The id of this segment
         *         'Title' => The title of this segment
         *     }
         * )
         */
        function get_segments() {
            return $this->get_request($this->_clients_base_route.'segments.json');
        }

        /**
         * Gets all email addresses on the current client's suppression list
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'DATE')
         * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *     'ResultsOrderedBy' => The field the results are ordered by
         *     'OrderDirection' => The order direction
         *     'PageNumber' => The page number for the result set
         *     'PageSize' => The page size used
         *     'RecordsOnThisPage' => The number of records returned
         *     'TotalNumberOfRecords' => The total number of records available
         *     'NumberOfPages' => The total number of pages for this collection
         *     'Results' => array(
         *         {
         *             'EmailAddress' => The suppressed email address
         *             'Date' => The date the email was suppressed
         *             'State' => The state of the suppressed email
         *         }
         *     )
         * }
         */
        function get_suppressionlist($page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {
                
            return $this->get_request_paged($this->_clients_base_route.'suppressionlist.json', 
                $page_number, $page_size, $order_field, $order_direction, '?');
        }

        /**
         * Adds email addresses to a client's suppression list.
         * @param array<string> $emails The email addresses to suppress.
         * @access public
         */
        function suppress($emails) {
          $data = array('EmailAddresses' => $emails);
          return $this->post_request($this->_clients_base_route.'suppress.json', $data);
        }

        /**
         * Unsuppresses an email address by removing it from the the client's
         * suppression list.
         * @param string $email The email address to be unsuppressed
         * @access public
         */
        function unsuppress($email) {
          return $this->put_request($this->_clients_base_route.'unsuppress.json?email=' . urlencode($email), '');
        }

        /**
         * Gets all templates the current client has access to
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         'TemplateID' => The id of the template
         *         'Name' => The name of the template
         *         'PreviewURL' => The url to preview the template from
         *         'ScreenshotURL' => The url of the template screenshot
         *     }
         * )
         */
        function get_templates() {
            return $this->get_request($this->_clients_base_route.'templates.json');
        }

        /**
         * Gets all templates the current client has access to
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *     'ApiKey' => The clients API Key, THIS IS NOT THE CLIENT ID
         *     'BasicDetails' => 
         *     {
         *         'ClientID' => The id of the client
         *         'CompanyName' => The company name of the client
         *         'ContactName' => The contact name of the client
         *         'EmailAddress' => The clients contact email address
         *         'Country' => The clients country
         *         'TimeZone' => The clients timezone
         *     }
         *     'BillingDetails' =>
         *     If on monthly billing
         *     {
         *         'CurrentTier' => The current monthly tier the client sits in
         *         'CurrentMonthlyRate' => The current pricing rate the client pays per month
         *         'MarkupPercentage' => The percentage markup applied to the base rates
         *         'Currency' => The currency paid in
         *         'ClientPays' => Whether the client pays for themselves,
         *         'MonthlyScheme' => Basic or Unlimited
         *     }
         *     If paying per campaign
         *     {
         *         'CanPurchaseCredits' => Whether the client can purchase credits
         *         'Credits' => The number of credits belonging to the client
         *         'BaseDeliveryFee' => The base fee payable per campaign
         *         'BaseRatePerRecipient' => The base fee payable per campaign recipient
         *         'BaseDesignSpamTestRate' => The base fee payable per design and spam test
         *         'MarkupOnDelivery' => The markup applied per campaign
         *         'MarkupPerRecipient' => The markup applied per campaign recipient
         *         'MarkupOnDesignSpamTest' => The markup applied per design and spam test
         *         'Currency' => The currency fees are paid in
         *         'ClientPays' => Whether client client pays for themselves
         *     }     
         * }
         */
        function get() {
            return $this->get_request(trim($this->_clients_base_route, '/').'.json');
        }

        /**
         * Deletes an existing client from the system
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function delete() {
            return $this->delete_request(trim($this->_clients_base_route, '/').'.json');
        }

        /**
         * Creates a new client based on the provided information
         * @param array $client Basic information of the new client.
         *     This should be an array of the form
         *         array(
         *             'CompanyName' => The company name of the client
         *             'Country' => The clients country
         *             'TimeZone' => The clients timezone
         *         )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be the ID of the newly created client
         */
        function create($client) {
          	if(isset($client['ContactName'])) {
          		trigger_error('[DEPRECATION] Use Person->add to set name on a new person in a client. For now, we will create a default person with the name provided.', E_USER_NOTICE);
          	}
          	if(isset($client['EmailAddress'])) {
          		trigger_error('[DEPRECATION] Use Person->add to set email on a new person in a client. For now, we will create a default person with the email provided.', E_USER_NOTICE);
          	}
            return $this->post_request($this->_base_route.'clients.json', $client);
        }

        /**
         * Updates the basic information for a client
         * @param array $client_basics Basic information of the client.
         *     This should be an array of the form
         *         array(
         *             'CompanyName' => The company name of the client
         *             'Country' => The clients country
         *             'TimeZone' => The clients timezone
         *         )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function set_basics($client_basics) {
          	if(isset($client['ContactName'])) {
          		trigger_error('[DEPRECATION] Use person->update to set name on a particular person in a client. For now, we will update the default person with the name provided.', E_USER_NOTICE);
          	}
          	if(isset($client['EmailAddress'])) {
          		trigger_error('[DEPRECATION] Use person->update to set email on a particular person in a client. For now, we will update the default person with the email address provided.', E_USER_NOTICE);
          	}
            return $this->put_request($this->_clients_base_route.'setbasics.json', $client_basics);
        }

        /**
         * Updates the billing details of the current client, setting the client to the payg billing model
         * For clients not set to pay themselves then all fields below ClientPays are ignored
         * All Markup fields are optional
         * @param array $client_billing Payg billing details of the client.
         *     This should be an array of the form
         *         array(
         *             'Currency' => The currency fees are paid in
         *             'ClientPays' => Whether client client pays for themselves
         *             'MarkupPercentage' => Can be used to set the percentage markup for all unset fees
         *             'CanPurchaseCredits' => Whether the client can purchase credits
         *             'MarkupOnDelivery' => The markup applied per campaign
         *             'MarkupPerRecipient' => The markup applied per campaign recipient
         *             'MarkupOnDesignSpamTest' => The markup applied per design and spam test
         *         )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function set_payg_billing($client_billing) {
            return $this->put_request($this->_clients_base_route.'setpaygbilling.json', $client_billing);
        }

        /**
         * Updates the billing details of the current client, setting the client to the monthly billing model
         * For clients not set to pay themselves then the markup percentage field is ignored
         * @param array $client_billing Payg billing details of the client.
         *     This should be an array of the form
         *         array(
         *             'Currency' => The currency fees are paid in
         *             'ClientPays' => Whether client client pays for themselves
         *             'MarkupPercentage' => Sets the percentage markup used for all monthly tiers
         *         )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function set_monthly_billing($client_billing) {
            return $this->put_request($this->_clients_base_route.'setmonthlybilling.json', $client_billing);
        }

        /**
         * Transfer credits to or from this client.
         * 
         * @param array $transfer_data Details for the credit transfer. This array
         *   should be of the form:
         *     array(
         *      'Credits' => An in representing the number of credits to transfer.
         *        This value may be either positive if you want to allocate credits
         *        from your account to the client, or negative if you want to
         *        deduct credits from the client back into your account.
         *      'CanUseMyCreditsWhenTheyRunOut' => A boolean value which if set
         *        to true, will allow the client to continue sending using your
         *        credits or payment details once they run out of credits, and if
         *        set to false, will prevent the client from using your credits to
         *        continue sending until you allocate more credits to them.
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object
         * of the form:
         * {
         *   'AccountCredits' => Integer representing credits in your account now
         *   'ClientCredits' => Integer representing credits in this client's
         *                      account now
         * }
         */
        function transfer_credits($transfer_data) {
            return $this->post_request($this->_clients_base_route.'credits.json',
            $transfer_data);
        }

        /**
         * returns the people associated with this client.
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form 
         *     array({
         *     		'EmailAddress' => the email address of the person
         *     		'Name' => the name of the person
         *     		'AccessLevel' => the access level of the person
         *     		'Status' => the status of the person
         *     })
         */
        function get_people() {
        	return $this->get_request($this->_clients_base_route.'people.json');
        } 
        
        /**
         * retrieves the email address of the primary contact for this client
         * @return CS_REST_Wrapper_Result a successful response will be an array in the form:
         * 		array('EmailAddress'=> email address of primary contact)
         */
        function get_primary_contact() {
        	return $this->get_request($this->_clients_base_route.'primarycontact.json');
        }
        
        /**
         * assigns the primary contact for this client to the person with the specified email address
         * @param string $emailAddress the email address of the person designated to be the primary contact
         * @return CS_REST_Wrapper_Result a successful response will be an array in the form:
         * 		array('EmailAddress'=> email address of primary contact)
         */
        function set_primary_contact($emailAddress) {
        	return $this->put_request($this->_clients_base_route.'primarycontact.json?email=' . urlencode($emailAddress), '');
        }
    }
}