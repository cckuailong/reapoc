<?php

namespace MailchimpTests\Reports;

use MailchimpAPI\Resources\Reports\TopLocations;
use MailchimpTests\MailChimpTestCase;
use MailchimpAPI\Resources\Reports;

/**
 * Class TopLocationsTest
 * @package MailchimpTests\Reports
 */
class TopLocationsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Reports::URL_COMPONENT . 1 . TopLocations::URL_COMPONENT,
            $this->mailchimp->reports(1)->locations(),
            "The Top Locations collection endpoint should be constructed correctly"
        );
    }
}
