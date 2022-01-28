<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class FileManagerFolders
 * @package MailchimpAPI\Resources
 */
class FileManagerFolders extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/file-manager/folders/';

    /**
     * FileManagerFolders constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $folder_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $folder_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $folder_id);
    }
}
