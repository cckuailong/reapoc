<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\MailchimpException;
use MailchimpAPI\Resources\Automations\Emails;
use MailchimpAPI\Resources\Automations\RemovedSubscribers;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\Settings\MailchimpSettings;


/**
 * Class Automations
 * @package MailchimpAPI\Resources
 */
class Automations extends ApiResource
{
    /**
     * A workflow ID
     */
    private $workflow_id;

    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/automations/';

    /**
     * The URL component for pausing all emails in a workflow
     */
    const PAUSE_ALL_URL_COMPONENT = '/actions/pause-all-emails/';

    /**
     * The URL component for starting all emails in a workflow
     */
    const START_ALL_URL_COMPONENT = '/actions/start-all-emails/';


    /**
     * Automations constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $workflow_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $workflow_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $workflow_id);
        $this->workflow_id = $workflow_id;
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function pauseAll()
    {
        $this->throwIfNot("id", $this->workflow_id);
        return $this->postToActionEndpoint(self::PAUSE_ALL_URL_COMPONENT);
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function startAll()
    {
        $this->throwIfNot("id", $this->workflow_id);
        return $this->postToActionEndpoint(self::START_ALL_URL_COMPONENT);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @return RemovedSubscribers
     */
    public function removedSubscribers()
    {
        return new RemovedSubscribers(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @param null $email_id
     * @return Emails
     */
    public function emails($email_id = null)
    {
        return new Emails(
            $this->getRequest(),
            $this->getSettings(),
            $email_id
        );
    }
}
