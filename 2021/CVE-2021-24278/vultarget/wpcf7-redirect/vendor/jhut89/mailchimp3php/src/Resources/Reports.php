<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\Reports\CampaignAbuse;
use MailchimpAPI\Resources\Reports\CampaignAdvice;
use MailchimpAPI\Resources\Reports\ClickReports;
use MailchimpAPI\Resources\Reports\DomainPerformance;
use MailchimpAPI\Resources\Reports\EepurlReports;
use MailchimpAPI\Resources\Reports\EmailActivity;
use MailchimpAPI\Resources\Reports\GoogleAnalytics;
use MailchimpAPI\Resources\Reports\OpenDetails;
use MailchimpAPI\Resources\Reports\SentTo;
use MailchimpAPI\Resources\Reports\SubReports;
use MailchimpAPI\Resources\Reports\TopLocations;
use MailchimpAPI\Resources\Reports\Unsubscribes;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Reports
 * @package MailchimpAPI\Resources
 */
class Reports extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/reports/';

    /**
     * Reports constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $campaign_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $campaign_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $campaign_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @param null $member
     * @return Unsubscribes
     */
    public function unsubscribes($member = null)
    {
        return new Unsubscribes(
            $this->getRequest(),
            $this->getSettings(),
            $member
        );
    }

    /**
     * @return SubReports
     */
    public function subReports()
    {
        return new SubReports(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @param null $member
     * @return SentTo
     */
    public function sentTo($member = null)
    {
        return new SentTo(
            $this->getRequest(),
            $this->getSettings(),
            $member
        );
    }

    /**
     * @return TopLocations
     */
    public function locations()
    {
        return new TopLocations(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @param null $member
     * @return EmailActivity
     */
    public function emailActivity($member = null)
    {
        return new EmailActivity(
            $this->getRequest(),
            $this->getSettings(),
            $member
        );
    }

    /**
     * @return EepurlReports
     */
    public function eepurlReports()
    {
        return new EepurlReports(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @return DomainPerformance
     */
    public function domainPerformance()
    {
        return new DomainPerformance(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @return CampaignAdvice
     */
    public function advice()
    {
        return new CampaignAdvice(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @param null $report_id
     * @return CampaignAbuse
     */
    public function abuse($report_id = null)
    {
        return new CampaignAbuse(
            $this->getRequest(),
            $this->getSettings(),
            $report_id
        );
    }

    /**
     * @param null $link_id
     * @return ClickReports
     */
    public function clickReports($link_id = null)
    {
        return new ClickReports(
            $this->getRequest(),
            $this->getSettings(),
            $link_id
        );
    }

    /**
     * @return OpenDetails
     */
    public function openReports()
    {
        return new OpenDetails(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @param null $profile_id
     * @return GoogleAnalytics
     */
    public function googleAnalytics($profile_id = null)
    {
        return new GoogleAnalytics(
            $this->getRequest(),
            $this->getSettings(),
            $profile_id
        );
    }
}
