<?php

namespace MailchimpTests;

use MailchimpAPI\Resources\Automations;
use MailchimpAPI\MailchimpException;

/**
 * Class AutomationsTest
 * @package MailchimpTests
 */
final class AutomationsTest extends MailChimpTestCase
{
    /**
     * @throws MailchimpException
     */
    public function testAutomationsCollectionUrl()
    {
        $this->endpointUrlBuildTest(
            Automations::URL_COMPONENT,
            $this->mailchimp->automations(),
            "The automations collection url should be constructed correctly"
        );
    }

    /**
     * @throws MailchimpException
     */
    public function testAutomationsInstanceUrl()
    {
        $this->endpointUrlBuildTest(
            Automations::URL_COMPONENT . 1,
            $this->mailchimp->automations(1),
            "The automations instance url should be constructed correctly"
        );
    }

    /**
     *
     */
    public function testAutomationsPauseAllWithoutId()
    {
        $error = null;
        try {
            $this->stub_mailchimp
                ->automations()
                ->pauseAll();
        } catch (MailchimpException $e) {
            $error = $e;
        }

        self::assertInstanceOf(MailchimpException::class, $error);
    }

    /**
     *
     */
    public function testAutomationsStartAllWithoutId()
    {
        $error = null;
        try {
            $this->stub_mailchimp
                ->automations()
                ->startAll();
        } catch (MailchimpException $e) {
            $error = $e;
        }

        self::assertInstanceOf(MailchimpException::class, $error);
    }
}
