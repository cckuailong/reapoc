<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\MailchimpException;
use MailchimpAPI\Resources\Campaigns\Content;
use MailchimpAPI\Resources\Campaigns\Feedback;
use MailchimpAPI\Resources\Campaigns\SendChecklist;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Responses\MailchimpResponse;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Campaigns
 * @package Mailchimp_API\Resources
 */
class Campaigns extends ApiResource
{

    /**
     * @var string The campaign id
     */
    private $campaign_id;

    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/campaigns/';

    /**
     * The url component for canceling a campaign send
     */
    const CANCEL_URL_COMPONENT = '/actions/cancel-send/';

    /**
     * The url component for pausing a campaign
     */
    const PAUSE_URL_COMPONENT = '/actions/pause';

    /**
     * The url component for replicating a campaign
     */
    const REPLICATE_URL_COMPONENT = '/actions/replicate';

    /**
     * The url component for resuming a paused campaign
     */
    const RESUME_URL_COMPONENT = '/actions/resume';

    /**
     * The url component for scheduling a campaign
     */
    const SCHEDULE_URL_COMPONENT = '/actions/schedule';

    /**
     * The url component for sending a campaign
     */
    const SEND_URL_COMPONENT = '/actions/send';

    /**
     * The url component for test sending a campaign
     */
    const TEST_URL_COMPONENT = '/actions/test';

    /**
     * The url component for un-scheduling a campaign
     */
    const UNSCHEDULE_URL_COMPONENT = '/actions/unschedule';


    /**
     * Campaigns constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $campaign_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $campaign_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $campaign_id);
        $this->campaign_id = $campaign_id;
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function cancel()
    {
        $this->throwIfNot("id", $this->campaign_id);
        return $this->postToActionEndpoint(self::CANCEL_URL_COMPONENT);
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function pause()
    {
        $this->throwIfNot("id", $this->campaign_id);
        return $this->postToActionEndpoint(self::PAUSE_URL_COMPONENT);
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function replicate()
    {
        $this->throwIfNot("id", $this->campaign_id);
        return $this->postToActionEndpoint(self::REPLICATE_URL_COMPONENT);
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function resume()
    {
        $this->throwIfNot("id", $this->campaign_id);
        return $this->postToActionEndpoint(self::RESUME_URL_COMPONENT);
    }

    /**
     * @param $schedule_time
     * @param array $optional_parameters
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function schedule($schedule_time, $optional_parameters = [])
    {
        $this->throwIfNot("id", $this->campaign_id);
        $params = ["schedule_time" => $schedule_time];
        $params = array_merge($params, $optional_parameters);

        return $this->postToActionEndpoint(self::SCHEDULE_URL_COMPONENT, $params);
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function send()
    {
        $this->throwIfNot("id", $this->campaign_id);
        return $this->postToActionEndpoint(self::SEND_URL_COMPONENT);
    }

    /**
     * @param array $test_addresses
     * @param string $send_type
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function test($test_addresses, $send_type)
    {
        $this->throwIfNot("id", $this->campaign_id);
        $params = ["test_emails" => $test_addresses, "send_type" => $send_type];

        return $this->postToActionEndpoint(self::TEST_URL_COMPONENT, $params);
    }

    /**
     * @return MailchimpResponse
     * @throws MailchimpException
     */
    public function unschedule()
    {
        $this->throwIfNot("id", $this->campaign_id);
        return $this->postToActionEndpoint(self::UNSCHEDULE_URL_COMPONENT);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @return SendChecklist
     */
    public function checklist()
    {
        return new SendChecklist(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @param null $feedback_id
     * @return Feedback
     */
    public function feedback($feedback_id = null)
    {
        return new Feedback(
            $this->getRequest(),
            $this->getSettings(),
            $feedback_id
        );
    }

    /**
     * @return Content
     */
    public function content()
    {
        return new Content(
            $this->getRequest(),
            $this->getSettings()
        );
    }
}
