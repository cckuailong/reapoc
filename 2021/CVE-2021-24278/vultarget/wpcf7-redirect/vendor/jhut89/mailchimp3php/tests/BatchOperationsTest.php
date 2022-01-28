<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\BatchOperations;

/**
 * Class BatchOperationsTest
 * @package MailchimpTests
 */
class BatchOperationsTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testBatchOperationsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            BatchOperations::URL_COMPONENT,
            $this->mailchimp->batches(),
            "The batch operations collection url should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testBatchOperationsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            BatchOperations::URL_COMPONENT . 1,
            $this->mailchimp->batches(1),
            "The batch operations instance url should be constructed correctly"
        );
    }
}
