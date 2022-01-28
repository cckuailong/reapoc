<?php

namespace MailchimpAPI\Resources\Lists;

use MailchimpAPI\MailchimpException;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Segments
 * @package MailchimpAPI\Resources\Lists
 */
class Segments extends ApiResource
{
    /**
     * @var string
     */
    private $segment_id;

    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/segments/';

    /**
     * Segments constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $segment_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $segment_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $segment_id);
        $this->segment_id = $segment_id;
    }

    /**
     * @param array $add
     * @param array $remove
     * @return \MailchimpAPI\Responses\MailchimpResponse
     * @throws MailchimpException
     */
    public function batch($add = [], $remove = [])
    {
        $this->throwIfNot("id", $this->segment_id);
        $params = ['members_to_add' => $add, 'members_to_remove' => $remove];

        return $this->postToActionEndpoint('', $params);
    }


    //SUBCLASS FUNCTIONS ------------------------------------------------------------

    /**
     * @param null $member
     * @return Segments\Members
     */
    public function members($member = null)
    {
        return new Segments\Members(
            $this->getRequest(),
            $this->getSettings(),
            $member
        );
    }
}
