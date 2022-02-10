<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * Entities that have a somewhat fixed, physical extension.
 * @see http://schema.org/Place
 * @method static additionalProperty(PropertyValue|PropertyValue[] $additionalProperty)
 * @method static address(PostalAddress|PostalAddress[]|string|string[] $address)
 * @method static aggregateRating(AggregateRating|AggregateRating[] $aggregateRating)
 * @method static amenityFeature(LocationFeatureSpecification|LocationFeatureSpecification[] $amenityFeature)
 * @method static branchCode(string|string[] $branchCode)
 * @method static containedIn(Place|Place[] $containedIn)
 * @method static containedInPlace(Place|Place[] $containedInPlace)
 * @method static containsPlace(Place|Place[] $containsPlace)
 * @method static event(Event|Event[] $event)
 * @method static events(Event|Event[] $events)
 * @method static faxNumber(string|string[] $faxNumber)
 * @method static geo(GeoCoordinates|GeoCoordinates[]|GeoShape|GeoShape[] $geo)
 * @method static globalLocationNumber(string|string[] $globalLocationNumber)
 * @method static hasMap(Map|Map[]|string|string[] $hasMap)
 * @method static isAccessibleForFree(bool|bool[] $isAccessibleForFree)
 * @method static isicV4(string|string[] $isicV4)
 * @method static logo(ImageObject|ImageObject[]|string|string[] $logo)
 * @method static map(string|string[] $map)
 * @method static maps(string|string[] $maps)
 * @method static maximumAttendeeCapacity(int|int[] $maximumAttendeeCapacity)
 * @method static openingHoursSpecification(OpeningHoursSpecification|OpeningHoursSpecification[] $openingHoursSpecification)
 * @method static photo(ImageObject|ImageObject[]|Photograph|Photograph[] $photo)
 * @method static photos(ImageObject|ImageObject[]|Photograph|Photograph[] $photos)
 * @method static publicAccess(bool|bool[] $publicAccess)
 * @method static review(Review|Review[] $review)
 * @method static reviews(Review|Review[] $reviews)
 * @method static smokingAllowed(bool|bool[] $smokingAllowed)
 * @method static specialOpeningHoursSpecification(OpeningHoursSpecification|OpeningHoursSpecification[] $specialOpeningHoursSpecification)
 * @method static telephone(string|string[] $telephone)
 */
class Place extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'additionalProperty', 'address', 'aggregateRating', 'amenityFeature', 'branchCode',
        'containedIn', 'containedInPlace', 'containsPlace', 'event', 'events', 'faxNumber', 'geo',
        'globalLocationNumber', 'hasMap', 'isAccessibleForFree', 'isicV4', 'logo', 'map', 'maps',
        'maximumAttendeeCapacity', 'openingHoursSpecification', 'photo', 'photos', 'publicAccess',
        'review', 'reviews', 'smokingAllowed', 'specialOpeningHoursSpecification', 'telephone',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Thing',
    ];
}
