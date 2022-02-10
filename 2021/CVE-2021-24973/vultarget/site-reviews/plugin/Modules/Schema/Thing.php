<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * The most generic type of item.
 * @see http://schema.org/Thing
 * @method static additionalType(string|string[] $additionalType)
 * @method static alternateName(string|string[] $alternateName)
 * @method static description(string|string[] $description)
 * @method static disambiguatingDescription(string|string[] $disambiguatingDescription)
 * @method static identifier(PropertyValue|PropertyValue[]|string|string[] $identifier)
 * @method static image(ImageObject|ImageObject[]|string|string[] $image)
 * @method static mainEntityOfPage(CreativeWork|CreativeWork[]|string|string[] $mainEntityOfPage)
 * @method static name(string|string[] $name)
 * @method static potentialAction(Action|Action[] $potentialAction)
 * @method static sameAs(string|string[] $sameAs)
 * @method static url(string|string[] $url)
 */
class Thing extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'additionalType', 'alternateName', 'description', 'disambiguatingDescription', 'identifier',
        'image', 'mainEntityOfPage', 'name', 'potentialAction', 'sameAs', 'url',
    ];
}
