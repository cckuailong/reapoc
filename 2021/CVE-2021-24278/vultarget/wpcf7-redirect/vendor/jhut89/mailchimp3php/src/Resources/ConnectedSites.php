<?php


namespace MailchimpAPI\Resources;


use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class ConnectedSites
 * @package MailchimpAPI\Resources
 */
class ConnectedSites extends ApiResource
{
    /**
     * @var null
     */
    private $site_id;

    /**
     * The connected sites endpoint url component
     */
    const URL_COMPONENT = "/connected-sites/";

    /**
     * The conversations url component for verifying a script install
     */
    const VERIFY_SCRIPT_INSTALL_URL_COMPONENT = "/actions/verify-script-installation/";

    /**
     * ConnectedSites constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $site_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $site_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $site_id);
        $this->site_id = $site_id;
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function verifyScriptInstallation()
    {
        $this->throwIfNot("id", $this->site_id);
        $this->postToActionEndpoint(self::VERIFY_SCRIPT_INSTALL_URL_COMPONENT);
    }
}
