<?php

namespace MailchimpAPI\Resources\Reports;

use MailchimpAPI\Resources\Reports\ClickReports\Members;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class ClickReports
 * @package MailchimpAPI\Resources\Reports
 */
class ClickReports extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/click-details/';

    /**
     * ClickReports constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $link_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $link_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $link_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @param null $member
     * @return Members
     */
    public function members($member = null)
    {
        return new Members(
            $this->getRequest(),
            $this->getSettings(),
            $member
        );
    }
}
