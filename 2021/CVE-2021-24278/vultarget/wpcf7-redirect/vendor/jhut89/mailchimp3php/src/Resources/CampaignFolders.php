<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class CampaignFolders
 * @package MailchimpAPI\Resources
 */
class CampaignFolders extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/campaign-folders/';

    /**
     * CampaignFolders constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param $folder_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $folder_id)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $folder_id);
    }
}
