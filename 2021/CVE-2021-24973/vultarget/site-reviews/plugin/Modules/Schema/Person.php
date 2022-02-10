<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * A person (alive, dead, undead, or fictional).
 * @see http://schema.org/Person
 * @method static additionalName(string|string[] $additionalName)
 * @method static address(PostalAddress|PostalAddress[]|string|string[] $address)
 * @method static affiliation(Organization|Organization[] $affiliation)
 * @method static alumniOf(EducationalOrganization|EducationalOrganization[] $alumniOf)
 * @method static award(string|string[] $award)
 * @method static awards(string|string[] $awards)
 * @method static birthDate(\DateTimeInterface|\DateTimeInterface[] $birthDate)
 * @method static birthPlace(Place|Place[] $birthPlace)
 * @method static brand(Brand|Brand[]|Organization|Organization[] $brand)
 * @method static children(Person|Person[] $children)
 * @method static colleague(Person|Person[]|string|string[] $colleague)
 * @method static colleagues(Person|Person[] $colleagues)
 * @method static contactPoint(ContactPoint|ContactPoint[] $contactPoint)
 * @method static contactPoints(ContactPoint|ContactPoint[] $contactPoints)
 * @method static deathDate(\DateTimeInterface|\DateTimeInterface[] $deathDate)
 * @method static deathPlace(Place|Place[] $deathPlace)
 * @method static duns(string|string[] $duns)
 * @method static email(string|string[] $email)
 * @method static familyName(string|string[] $familyName)
 * @method static faxNumber(string|string[] $faxNumber)
 * @method static follows(Person|Person[] $follows)
 * @method static funder(Organization|Organization[]|Person|Person[] $funder)
 * @method static gender(GenderType|GenderType[]|string|string[] $gender)
 * @method static givenName(string|string[] $givenName)
 * @method static globalLocationNumber(string|string[] $globalLocationNumber)
 * @method static hasOfferCatalog(OfferCatalog|OfferCatalog[] $hasOfferCatalog)
 * @method static hasPOS(Place|Place[] $hasPOS)
 * @method static height(Distance|Distance[]|QuantitativeValue|QuantitativeValue[] $height)
 * @method static homeLocation(ContactPoint|ContactPoint[]|Place|Place[] $homeLocation)
 * @method static honorificPrefix(string|string[] $honorificPrefix)
 * @method static honorificSuffix(string|string[] $honorificSuffix)
 * @method static isicV4(string|string[] $isicV4)
 * @method static jobTitle(string|string[] $jobTitle)
 * @method static knows(Person|Person[] $knows)
 * @method static makesOffer(Offer|Offer[] $makesOffer)
 * @method static memberOf(Organization|Organization[]|ProgramMembership|ProgramMembership[] $memberOf)
 * @method static naics(string|string[] $naics)
 * @method static nationality(Country|Country[] $nationality)
 * @method static netWorth(MonetaryAmount|MonetaryAmount[]|PriceSpecification|PriceSpecification[] $netWorth)
 * @method static owns(OwnershipInfo|OwnershipInfo[]|Product|Product[] $owns)
 * @method static parent(Person|Person[] $parent)
 * @method static parents(Person|Person[] $parents)
 * @method static performerIn(Event|Event[] $performerIn)
 * @method static publishingPrinciples(CreativeWork|CreativeWork[]|string|string[] $publishingPrinciples)
 * @method static relatedTo(Person|Person[] $relatedTo)
 * @method static seeks(Demand|Demand[] $seeks)
 * @method static sibling(Person|Person[] $sibling)
 * @method static siblings(Person|Person[] $siblings)
 * @method static sponsor(Organization|Organization[]|Person|Person[] $sponsor)
 * @method static spouse(Person|Person[] $spouse)
 * @method static taxID(string|string[] $taxID)
 * @method static telephone(string|string[] $telephone)
 * @method static vatID(string|string[] $vatID)
 * @method static weight(QuantitativeValue|QuantitativeValue[] $weight)
 * @method static workLocation(ContactPoint|ContactPoint[]|Place|Place[] $workLocation)
 * @method static worksFor(Organization|Organization[] $worksFor)
 */
class Person extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'additionalName', 'address', 'affiliation', 'alumniOf', 'award', 'awards', 'birthDate',
        'birthPlace', 'brand', 'children', 'colleague', 'colleagues', 'contactPoint',
        'contactPoints', 'deathDate', 'deathPlace', 'duns', 'email', 'familyName', 'faxNumber',
        'follows', 'funder', 'gender', 'givenName', 'globalLocationNumber', 'hasOfferCatalog',
        'hasPOS', 'height', 'homeLocation', 'honorificPrefix', 'honorificSuffix', 'isicV4',
        'jobTitle', 'knows', 'makesOffer', 'memberOf', 'naics', 'nationality', 'netWorth', 'owns',
        'parent', 'parents', 'performerIn', 'publishingPrinciples', 'relatedTo', 'seeks', 'sibling',
        'siblings', 'sponsor', 'spouse', 'taxID', 'telephone', 'vatID', 'weight', 'workLocation',
        'worksFor',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Thing',
    ];
}
