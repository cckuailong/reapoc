<?php


namespace MailchimpAPI\Resources;


use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class LandingPages
 * @package MailchimpAPI\Resources
 */
class LandingPages extends ApiResource
{
    /**
     * @var null The landing page id
     */
    private $page_id;

    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = "/landing-pages/";

    /**
     * the url component for publishing a landing page
     */
    const PUBLISH_URL_COMPONENT = "/actions/publish/";

    /**
     * the url component for unpublishing a landing page
     */
    const UNPUBLISH_URL_COMPONENT = "/actions/unpublish/";

    /**
     * LandingPages constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $page_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $page_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $page_id);
        $this->page_id = $page_id;
    }

    /**
     * @return \MailchimpAPI\Responses\MailchimpResponse
     * @throws \MailchimpAPI\MailchimpException
     */
    public function publish()
    {
        $this->throwIfNot("id", $this->page_id);
        return $this->postToActionEndpoint(self::PUBLISH_URL_COMPONENT);
    }

    /**
     * @return \MailchimpAPI\Responses\MailchimpResponse
     * @throws \MailchimpAPI\MailchimpException
     */
    public function unpublish()
    {
        $this->throwIfNot("id", $this->page_id);
        return $this->postToActionEndpoint(self::UNPUBLISH_URL_COMPONENT);
    }

    /**
     * @return LandingPages\Content
     */
    public function content()
    {
        return new LandingPages\Content(
            $this->getRequest(),
            $this->getSettings()
        );
    }
}
