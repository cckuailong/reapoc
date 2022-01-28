<?php

namespace MailchimpAPI\Resources\EcommerceStores;

use MailchimpAPI\Resources\EcommerceStores\Products\Variants;
use MailchimpAPI\Resources\EcommerceStores\Products\Images;
use MailchimpAPI\Requests\MailchimpRequest;
use MailchimpAPI\Resources\ApiResource;
use MailchimpAPI\Settings\MailchimpSettings;

/**
 * Class Products
 * @package MailchimpAPI\Resources\EcommerceStores
 */
class Products extends ApiResource
{
    /**
     * The url component for this endpoint
     */
    const URL_COMPONENT = '/products/';


    /**
     * Products constructor.
     * @param MailchimpRequest $request
     * @param MailchimpSettings $settings
     * @param null $product_id
     */
    public function __construct(MailchimpRequest $request, MailchimpSettings $settings, $product_id = null)
    {
        parent::__construct($request, $settings);
        $request->appendToEndpoint(self::URL_COMPONENT . $product_id);
    }

    //SUBCLASS FUNCTIONS ------------------------------------------------------------


    /**
     * @param null $variant_id
     * @return Variants
     */
    public function variants($variant_id = null)
    {
        return new Variants(
            $this->getRequest(),
            $this->getSettings(),
            $variant_id
        );
    }

    /**
     * @param null $image_id
     * @return Images
     */
    public function images($image_id = null)
    {
        return new Images(
            $this->getRequest(),
            $this->getSettings(),
            $image_id
        );
    }
}
