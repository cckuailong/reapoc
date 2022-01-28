<?php

namespace MailchimpAPI\Resources;


use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class VerifiedDomains
 * Represents the Verified Domains Mailchimp API endpoint
 * @package MailchimpAPI\Resources
 */
class VerifiedDomains extends ApiResource
{
    /* @var string */
    private $domain_name;

    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/verified-domains/';

    const VERIFY_URL_COMPONENT = '/actions/verify';

    /**
     * VerifiedDomains constructor.
     *
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $domain_name
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $domain_name = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $domain_name);
        $this->domain_name = $domain_name;
    }

    /**
     * @return \MailchimpAPI\Responses\MailchimpResponse
     * @throws \MailchimpAPI\MailchimpException
     */
    public function verify()
    {
        $this->throwIfNot("domain-name", $this->domain_name);
        return $this->postToActionEndpoint(self::VERIFY_URL_COMPONENT);
    }
}
