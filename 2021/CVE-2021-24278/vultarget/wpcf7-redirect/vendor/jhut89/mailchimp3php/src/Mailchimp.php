<?php

namespace MailchimpAPI;

use MailchimpAPI\Requests\MailchimpConnection;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Mailchimp
 *
 * @package Mailchimp_API
 */
class Mailchimp
{
    /**
     * @var MailchimpRequest $request
     */
    public $request;

    /**
     * @var MailchimpSettings $settings
     */
    public $settings;

    /**
     * @var string
     */
    public $apikey;

    /**
     * Mailchimp constructor.
     *
     * @param string $apikey
     *
     * @throws MailchimpException
     */
    public function __construct($apikey)
    {
        $this->apikey = $apikey;
        $this->request = new MailchimpRequest($this->apikey);
        $this->settings = new MailchimpSettings();
    }

    /**
     * Retrieves a new Account
     * @return Resources\Account
     */
    public function account()
    {
        return new Resources\Account($this->request, $this->settings);
    }

    /**
     * Retrieves a new AuthorizedApps instance.
     *
     * @param null $app_id The ID for an app if retrieving an instance
     *
     * @return Resources\AuthorizedApps
     */
    public function apps($app_id = null)
    {
        return new Resources\AuthorizedApps($this->request, $this->settings, $app_id);
    }

    /**
     * @param null $workflow_id
     *
     * @return Resources\Automations
     */
    public function automations($workflow_id = null)
    {
        return new Resources\Automations($this->request, $this->settings, $workflow_id);
    }

    /**
     * @param null $batch_id
     *
     * @return Resources\BatchOperations
     */
    public function batches($batch_id = null)
    {
        return new Resources\BatchOperations($this->request, $this->settings, $batch_id);
    }

    /**
     * @param null $batch_webhook_id
     *
     * @return Resources\BatchWebhooks
     */
    public function batchWebhooks($batch_webhook_id = null)
    {
        return new Resources\BatchWebhooks($this->request, $this->settings, $batch_webhook_id);
    }

    /**
     * @param null $folder_id
     *
     * @return Resources\CampaignFolders
     */
    public function campaignFolders($folder_id = null)
    {
        return new Resources\CampaignFolders($this->request, $this->settings, $folder_id);
    }

    /**
     * @param null $campaign_id
     *
     * @return Resources\Campaigns
     */
    public function campaigns($campaign_id = null)
    {
        return new Resources\Campaigns($this->request, $this->settings, $campaign_id);
    }

    /**
     * @param null $site_id
     *
     * @return Resources\ConnectedSites
     */
    public function connectedSites($site_id = null)
    {
        return new Resources\ConnectedSites($this->request, $this->settings, $site_id);
    }

    /**
     * @param null $conversation_id
     *
     * @return Resources\Conversations
     */
    public function conversations($conversation_id = null)
    {
        return new Resources\Conversations($this->request, $this->settings, $conversation_id);
    }


    /**
     * @param null $store_id
     *
     * @return Resources\EcommerceStores
     */
    public function ecommerceStores($store_id = null)
    {
        return new Resources\EcommerceStores($this->request, $this->settings, $store_id);
    }

    /**
     * @param null $outreach_id
     *
     * @return Resources\FacebookAds
     */
    public function facebookAds($outreach_id = null)
    {
        return new Resources\FacebookAds($this->request, $this->settings, $outreach_id);
    }

    /**
     * @param null $file_id
     *
     * @return Resources\FileManagerFiles
     */
    public function fileManagerFiles($file_id = null)
    {
        return new Resources\FileManagerFiles($this->request, $this->settings, $file_id);
    }

    /**
     * @param null $folder_id
     *
     * @return Resources\FileManagerFolders
     */
    public function fileManagerFolders($folder_id = null)
    {
        return new Resources\FileManagerFolders($this->request, $this->settings, $folder_id);
    }

    /**
     * @param null $outreach_id
     *
     * @return Resources\GoogleAds
     */
    public function googleAds($outreach_id = null)
    {
        return new Resources\GoogleAds($this->request, $this->settings, $outreach_id);
    }

    /**
     * @param null $page_id
     *
     * @return Resources\LandingPages
     */
    public function landingPages($page_id = null)
    {
        return new Resources\LandingPages($this->request, $this->settings, $page_id);
    }

    /**
     * @param null $list_id
     *
     * @return Resources\Lists
     */
    public function lists($list_id = null)
    {
        return new Resources\Lists($this->request, $this->settings, $list_id);
    }

    /**
     * @return Resources\Ping
     */
    public function ping()
    {
        return new Resources\Ping($this->request, $this->settings);
    }

    /**
     * @param null $campaign_id
     *
     * @return Resources\Reports
     */
    public function reports($campaign_id = null)
    {
        return new Resources\Reports($this->request, $this->settings, $campaign_id);
    }

    /**
     * @return Resources\SearchCampaigns
     */
    public function searchCampaigns()
    {
        return new Resources\SearchCampaigns($this->request, $this->settings);
    }

    /**
     * @return Resources\SearchMembers
     */
    public function searchMembers()
    {
        return new Resources\SearchMembers($this->request, $this->settings);
    }

    /**
     * @param null $folder_id
     *
     * @return Resources\TemplateFolders
     */
    public function templateFolders($folder_id = null)
    {
        return new Resources\TemplateFolders($this->request, $this->settings, $folder_id);
    }

    /**
     * @param null $template_id
     *
     * @return Resources\Templates
     */
    public function templates($template_id = null)
    {
        return new Resources\Templates($this->request, $this->settings, $template_id);
    }

    /**
     * @param null $domain_name
     *
     * @return Resources\VerifiedDomains
     */
    public function verifiedDomains($domain_name = null)
    {
        return new Resources\VerifiedDomains($this->request, $this->settings, $domain_name);
    }

    /**
     * Concatenate the auth URL given a client_id and redirect URI
     *
     * @param $client_id
     * @param $redirect_uri
     * @param $state
     *
     * @return string
     */
    public static function getAuthUrl(
        $client_id,
        $redirect_uri,
        $state = null
    ) {
        $encoded_uri = urlencode($redirect_uri);

        $authUrl = "https://login.mailchimp.com/oauth2/authorize";
        $authUrl .= "?client_id=" . $client_id;
        $authUrl .= "&redirect_uri=" . $encoded_uri;
        $authUrl .= "&response_type=code";

        if ($state !== null) {
            $authUrl .= "&state=" . $state;
        }

        return $authUrl;
    }

    /**
     * Handle the "handshake" to retrieve an api key via OAuth
     *
     * @param $code
     * @param $client_id
     * @param $client_sec
     * @param $redirect_uri
     *
     * @return string
     * @throws MailchimpException
     */
    public static function oauthExchange(
        $code,
        $client_id,
        $client_sec,
        $redirect_uri
    ) {
        $encoded_uri = urldecode($redirect_uri);

        $oauth_string = "grant_type=authorization_code";
        $oauth_string .= "&client_id=" . $client_id;
        $oauth_string .= "&client_secret=" . $client_sec;
        $oauth_string .= "&redirect_uri=" . $encoded_uri;
        $oauth_string .= "&code=" . $code;

        $request = new MailchimpRequest();

        $access_token = self::requestAccessToken($oauth_string, $request);
        $request->reset();

        $apiKey = self::requestKeyFromToken($access_token, $request);

        return $apiKey;
    }

    /**
     * Request an access token from Mailchimp
     *
     * @param string           $oauth_string
     * @param MailchimpRequest $request
     *
     * @return mixed
     * @throws MailchimpException
     */
    private static function requestAccessToken($oauth_string, MailchimpRequest $request)
    {
        $request->setMethod("POST");
        $request->setPayload($oauth_string, false);
        $request->setBaseUrl(MailchimpConnection::TOKEN_REQUEST_URL);

        $connection = new MailchimpConnection($request);
        $response = $connection->execute();

        $access_token = $response->deserialize()->access_token;

        if (!$access_token) {
            throw new MailchimpException(
                'MailChimp did not return an access token'
            );
        }

        return $access_token;
    }

    /**
     * Construct an API key by requesting an access tokens data center
     *
     * @param string           $access_token
     * @param MailchimpRequest $request
     *
     * @return string
     * @throws MailchimpException
     */
    private static function requestKeyFromToken($access_token, MailchimpRequest $request)
    {
        $request->setMethod("GET");
        $request->setBaseUrl(MailchimpConnection::OAUTH_METADATA_URL);
        $request->addHeader('Authorization: OAuth ' . $access_token);

        $connection = new MailchimpConnection($request);
        $response = $connection->execute();

        $dc = $response->deserialize()->dc;

        return $access_token . '-' . $dc;
    }
}
