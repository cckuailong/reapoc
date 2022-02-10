<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access a campaigns resources from the create send API.
 * This class includes functions to create and send campaigns,
 * along with accessing lists of campaign specific resources i.e reporting statistics
 * @author tobyb
 *
 */
if (!class_exists('CS_REST_Campaigns')) {
    class CS_REST_Campaigns extends CS_REST_Wrapper_Base {

        /**
         * The base route of the campaigns resource.
         * @var string
         * @access private
         */
        var $_campaigns_base_route;

        /**
         * Constructor.
         * @param $campaign_id string The campaign id to access (Ignored for create requests)
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
        $campaign_id,
        $auth_details,
        $protocol = 'https',
        $debug_level = CS_REST_LOG_NONE,
        $host = 'api.createsend.com',
        $log = NULL,
        $serialiser = NULL,
        $transport = NULL) {
            	
            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
            $this->set_campaign_id($campaign_id);
        }

        /**
         * Change the campaign id used for calls after construction
         * @param $campaign_id
         * @access public
         */
        function set_campaign_id($campaign_id) {
            $this->_campaigns_base_route = $this->_base_route.'campaigns/'.$campaign_id.'/';
        }

        /**
         * Creates a new campaign based on the provided campaign info.
         * At least on of the ListIDs and Segments parameters must be provided
         * @param string $client_id The client to create the campaign for
         * @param array $campaign_info The campaign information to use during creation.
         *     This array should be of the form
         *     array(
         *         'Subject' => string required The campaign subject
         *         'Name' => string required The campaign name
         *         'FromName' => string required The From name for the campaign
         *         'FromEmail' => string required The From address for the campaign
         *         'ReplyTo' => string required The Reply-To address for the campaign
         *         'HtmlUrl' => string required A url to download the campaign HTML from
         *         'TextUrl' => string optional A url to download the campaign
         *           text version from. If not provided, text content will be
         *           automatically generated from HTML content.
         *         'ListIDs' => array<string> optional An array of list ids to send the campaign to
         *         'SegmentIDs' => array<string> optional An array of segment ids to send the campaign to.
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be the ID of the newly created campaign
         */
        function create($client_id, $campaign_info) {
            return $this->post_request($this->_base_route.'campaigns/'.$client_id.'.json', $campaign_info);
        }

        /**
         * Creates a new campaign from a template based on the info provided.
         * At least on of the ListIDs and Segments parameters must be provided
         * @param string $client_id The client to create the campaign for
         * @param array $campaign_info The campaign information to use during creation.
         *     This array should be of the form
         *     array(
         *         'Subject' => string required The campaign subject
         *         'Name' => string required The campaign name
         *         'FromName' => string required The From name for the campaign
         *         'FromEmail' => string required The From address for the campaign
         *         'ReplyTo' => string required The Reply-To address for the campaign
         *         'ListIDs' => array<string> optional An array of list ids to send the campaign to
         *         'SegmentIDs' => array<string> optional An array of segment ids to send the campaign to
         *         'TemplateID' => string required The ID of the template to use
         *         'TemplateContent' => array required The content which will be used to fill the editable areas of the template
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be the ID of the newly created campaign
         */
        function create_from_template($client_id, $campaign_info) {
            return $this->post_request($this->_base_route.'campaigns/'.$client_id.'/fromtemplate.json', $campaign_info);
        }

        /**
         * Sends a preview of an existing campaign to the specified recipients. 
         * @param array<string> $recipients The recipients to send the preview to. 
         * @param string $personalize How to personalize the campaign content. Valid options are:
         *     'Random': Choose a random campaign recipient and use their personalisation data
         *     'Fallback': Use the fallback terms specified in the campaign content
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function send_preview($recipients, $personalize = 'Random') { 
            $preview_data = array(
                'PreviewRecipients' => $recipients,
                'Personalize' => $personalize
            );
            
            return $this->post_request($this->_campaigns_base_route.'sendpreview.json', $preview_data);
        }

        /**
         * Sends an existing campaign based on the scheduling information provided
         * @param array $schedule The campaign scheduling information.
         *     This array should be of the form
         *     array (
         *        'ConfirmationEmail' => string required The email address to send a confirmation email to,
         *        'SendDate' => string required The date to send the campaign or 'immediately'.
         *                      The date should be in the format 'y-M-d'
         *     )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function send($schedule) {
            return $this->post_request($this->_campaigns_base_route.'send.json', $schedule);
        }

        /**
         * Unschedules the campaign, moving it back into the drafts. If the campaign has been sent or is   
         * in the process of sending, this api request will fail.
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function unschedule() {
            return $this->post_request($this->_campaigns_base_route.'unschedule.json', NULL);
        }

        /**
         * Deletes an existing campaign from the system
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be empty
         */
        function delete() {
            return $this->delete_request(trim($this->_campaigns_base_route, '/').'.json');
        }

        /**
         * Gets all email addresses on the current clients suppression list
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST')
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
         *             'ListID' => The ID of the list this subscriber comes from
         *         }
         *     )
         * }
         */
        function get_recipients($page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {            
            return $this->get_request_paged($this->_campaigns_base_route.'recipients.json', $page_number, 
                $page_size, $order_field, $order_direction, '?');
        }

        /**
         * Gets all bounces recorded for a campaign
         * @param string $since The date to start getting bounces from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
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
         *             'EmailAddress' => The email that bounced
         *             'ListID' => The ID of the list the subscriber was on
         *             'BounceType' => The type of bounce
         *             'Date' => The date the bounce message was received
         *             'Reason' => The reason for the bounce
         *         }
         *     )
         * }
         * )
         */
        function get_bounces($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {
            return $this->get_request_paged($this->_campaigns_base_route.'bounces.json?date='.urlencode($since),
                $page_number, $page_size, $order_field, $order_direction);
        }

        /**
         * Gets the lists a campaign was sent to
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *     'Lists' =>  array(
         *         {
         *             'ListID' => The list id
         *             'Name' => The list name
         *         }
         *     ), 
         *     'Segments' => array(
         *         {
         *             'ListID' => The list id of the segment
         *             'SegmentID' => The id of the segment
         *             'Title' => The title of the segment
         *         }
         *     )
         * }
         */
        function get_lists_and_segments() {
            return $this->get_request($this->_campaigns_base_route.'listsandsegments.json');
        }

        /**
         * Gets a summary of all campaign reporting statistics
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *     'Recipients' => The total recipients of the campaign
         *     'TotalOpened' => The total number of opens recorded
         *     'Clicks' => The total number of recorded clicks
         *     'Unsubscribed' => The number of recipients who unsubscribed
         *     'Bounced' => The number of recipients who bounced
         *     'UniqueOpened' => The number of recipients who opened
         *     'WebVersionURL' => The url of the web version of the campaign
         *     'WebVersionTextURL' => The url of the web version of the text version of the campaign
         *     'WorldviewURL' => The public Worldview URL for the campaign
         *     'Forwards' => The number of times the campaign has been forwarded to a friend
         *     'Likes' => The number of times the campaign has been 'liked' on Facebook
         *     'Mentions' => The number of times the campaign has been tweeted about
         *     'SpamComplaints' => The number of recipients who marked the campaign as spam
         * }
         */
        function get_summary() {
            return $this->get_request($this->_campaigns_base_route.'summary.json');
        }

        /**
         * Gets the email clients that subscribers used to open the campaign
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * array(
         *     {
         *         Client => The email client name
         *         Version => The email client version
         *         Percentage => The percentage of subscribers who used this email client
         *         Subscribers => The actual number of subscribers who used this email client
         *     }
         * )
         */
        function get_email_client_usage() {
          return $this->get_request($this->_campaigns_base_route.'emailclientusage.json');
        }

        /**
         * Gets all opens recorded for a campaign since the provided date
         * @param string $since The date to start getting opens from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
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
         *             'EmailAddress' => The email address of the subscriber who opened
         *             'ListID' => The list id of the list containing the subscriber
         *             'Date' => The date of the open
         *             'IPAddress' => The ip address where the open originated
         *             'Latitude' => The geocoded latitude from the IP address
         *             'Longitude' => The geocoded longitude from the IP address
         *             'City' => The geocoded city from the IP address
         *             'Region' => The geocoded region from the IP address
         *             'CountryCode' => The geocoded two letter country code from the IP address
         *             'CountryName' => The geocoded full country name from the IP address
         *         }
         *     )
         * }
         */
        function get_opens($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {
            return $this->get_request_paged($this->_campaigns_base_route.'opens.json?date='.urlencode($since), 
                $page_number, $page_size, $order_field, $order_direction);
        }

        /**
         * Gets all clicks recorded for a campaign since the provided date
         * @param string $since The date to start getting clicks from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
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
         *             'EmailAddress' => The email address of the subscriber who clicked
         *             'ListID' => The list id of the list containing the subscriber
         *             'Date' => The date of the click
         *             'IPAddress' => The ip address where the click originated
         *             'URL' => The url that the subscriber clicked on
         *             'Latitude' => The geocoded latitude from the IP address
         *             'Longitude' => The geocoded longitude from the IP address
         *             'City' => The geocoded city from the IP address
         *             'Region' => The geocoded region from the IP address
         *             'CountryCode' => The geocoded two letter country code from the IP address
         *             'CountryName' => The geocoded full country name from the IP address
         *         }
         *     )
         * }
         */
        function get_clicks($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {
            return $this->get_request_paged($this->_campaigns_base_route.'clicks.json?date='.urlencode($since), 
                $page_number, $page_size, $order_field, $order_direction);
        }

        /**
         * Gets all unsubscribes recorded for a campaign since the provided date
         * @param string $since The date to start getting unsubscribes from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
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
         *             'EmailAddress' => The email address of the subscriber who unsubscribed
         *             'ListID' => The list id of the list containing the subscriber
         *             'Date' => The date of the unsubscribe
         *             'IPAddress' => The ip address where the unsubscribe originated
         *         }
         *     )
         * }
         */
        function get_unsubscribes($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {
            return $this->get_request_paged($this->_campaigns_base_route.'unsubscribes.json?date='.urlencode($since), 
                $page_number, $page_size, $order_field, $order_direction);
        }

        /**
         * Gets all spam complaints recorded for a campaign since the provided date
         * @param string $since The date to start getting spam complaints from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
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
         *             'EmailAddress' => The email address of the subscriber who unsubscribed
         *             'ListID' => The list id of the list containing the subscriber
         *             'Date' => The date of the unsubscribe
         *         }
         *     )
         * }
         */
        function get_spam($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {
            return $this->get_request_paged($this->_campaigns_base_route.'spam.json?date='.urlencode($since), 
                $page_number, $page_size, $order_field, $order_direction);
        }
    }
}