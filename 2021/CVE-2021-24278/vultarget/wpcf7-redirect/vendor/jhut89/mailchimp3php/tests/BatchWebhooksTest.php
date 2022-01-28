<?php

namespace MailchimpTests;


use MailchimpAPI\Resources\BatchWebhooks;

/**
 * Class BatchWebhooksTest
 * @package MailchimpTests
 */
class BatchWebhooksTest extends MailChimpTestCase
{
    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testBatchWebhookCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            BatchWebhooks::URL_COMPONENT,
            $this->mailchimp->batchWebhooks(),
            "The batch webhooks collection url should be constructed correctly"
        );
    }

    /**
     * @throws \MailchimpAPI\MailchimpException
     */
    public function testBatchWebhooksInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            BatchWebhooks::URL_COMPONENT . 1,
            $this->mailchimp->batchWebhooks(1),
            "The batch webhooks instance url should be constructed correctly"
        );
    }
}
