<?php

namespace MailchimpAPI\Resources;

use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class FileManagerFiles
 * @package MailchimpAPI\Resources
 */
class FileManagerFiles extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/file-manager/files/';

    /**
     * FileManagerFiles constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $file_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $file_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $file_id);
    }
}
