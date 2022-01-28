<?php

namespace MailchimpAPI\Resources\Automations;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Emails
 * @package MailchimpAPI\Resources\Automations
 */
class Emails extends ApiResource
{
    /**
     * An email ID
     */
    protected $email_id;

    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/emails/';

    /**
     * The URL component for pausing a workflow email
     */
    const PAUSE_URL_COMPONENT = '/actions/pause';

    /**
     * The URL component for starting a workflow email
     */
    const START_URL_COMPONENT = '/actions/start';


    /**
     * Emails constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $email_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $email_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $email_id);
        $this->email_id = $email_id;
    }

    /**
     * @return MailchimpResponse
     * @throws \MailchimpAPI\MailchimpException
     */
    public function pause()
    {
        $this->throwIfNot("id", $this->email_id);
        return $this->postToActionEndpoint(self::PAUSE_URL_COMPONENT);
    }

    /**
     * @return MailchimpResponse
     * @throws \MailchimpAPI\MailchimpException
     */
    public function start()
    {
        $this->throwIfNot("id", $this->email_id);
        return $this->postToActionEndpoint(self::START_URL_COMPONENT);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @param null $member
     * @return Emails\Queue
     */
    public function queue($member = null)
    {
        return new Emails\Queue(
            $this->getRequest(),
            $this->getSettings(),
            $member
        );
    }
}
