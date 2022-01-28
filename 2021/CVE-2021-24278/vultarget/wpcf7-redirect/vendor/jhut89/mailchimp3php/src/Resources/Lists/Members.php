<?php

namespace MailchimpAPI\Resources\Lists;


use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\Lists\Members\Notes;
use MailchimpAPI\Resources\Lists\Members\Goals;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Members
 * @package MailchimpAPI\Resources\Lists
 */
class Members extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/members/';

    /**
     * Members constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $member
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $member)
    {
        parent::__construct($request, $settings);
        if ($member && strpos($member, "@")) {
            $member = md5(strtolower($member));
        }
        $request->appendToEndpoint(self::URL_COMPONENT . $member);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @param null $note_id
     * @return Notes
     */
    public function notes($note_id = null)
    {
        return new Notes(
            $this->getRequest(),
            $this->getSettings(),
            $note_id
        );
    }

    /**
     * @return Goals
     */
    public function goals()
    {
        return new Goals(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @return Members\Activity
     */
    public function activity()
    {
        return new Members\Activity(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @return Members\Tags
     */
    public function tags()
    {
        return new Members\Tags(
            $this->getRequest(),
            $this->getSettings()
        );
    }

    /**
     * @return Members\Events
     */
    public function events()
    {
        return new Members\Events(
            $this->getRequest(),
            $this->getSettings()
        );
    }
}
