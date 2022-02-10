<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * An offer to transfer some rights to an item or to provide a service — for example, an offer to
 * sell tickets to an event, to rent the DVD of a movie, to stream a TV show over the internet, to
 * repair a motorcycle, or to loan a book.
 * For [GTIN](http://www.gs1.org/barcodes/technical/idkeys/gtin)-related fields,
 * see [Check Digit calculator](http://www.gs1.org/barcodes/support/check_digit_calculator)
 * and [validation guide](http://www.gs1us.org/resources/standards/gtin-validation-guide)
 * from [GS1](http://www.gs1.org/).
 * @see http://schema.org/Offer
 * @method static acceptedPaymentMethod(LoanOrCredit|LoanOrCredit[]|PaymentMethod|PaymentMethod[] $acceptedPaymentMethod)
 * @method static addOn(Offer|Offer[] $addOn)
 * @method static advanceBookingRequirement(QuantitativeValue|QuantitativeValue[] $advanceBookingRequirement)
 * @method static aggregateRating(AggregateRating|AggregateRating[] $aggregateRating)
 * @method static areaServed(AdministrativeArea|AdministrativeArea[]|GeoShape|GeoShape[]|Place|Place[]|string|string[] $areaServed)
 * @method static availability(ItemAvailability|ItemAvailability[] $availability)
 * @method static availabilityEnds(\DateTimeInterface|\DateTimeInterface[] $availabilityEnds)
 * @method static availabilityStarts(\DateTimeInterface|\DateTimeInterface[] $availabilityStarts)
 * @method static availableAtOrFrom(Place|Place[] $availableAtOrFrom)
 * @method static availableDeliveryMethod(DeliveryMethod|DeliveryMethod[] $availableDeliveryMethod)
 * @method static businessFunction(BusinessFunction|BusinessFunction[] $businessFunction)
 * @method static category(Thing|Thing[]|string|string[] $category)
 * @method static deliveryLeadTime(QuantitativeValue|QuantitativeValue[] $deliveryLeadTime)
 * @method static eligibleCustomerType(BusinessEntityType|BusinessEntityType[] $eligibleCustomerType)
 * @method static eligibleDuration(QuantitativeValue|QuantitativeValue[] $eligibleDuration)
 * @method static eligibleQuantity(QuantitativeValue|QuantitativeValue[] $eligibleQuantity)
 * @method static eligibleRegion(GeoShape|GeoShape[]|Place|Place[]|string|string[] $eligibleRegion)
 * @method static eligibleTransactionVolume(PriceSpecification|PriceSpecification[] $eligibleTransactionVolume)
 * @method static gtin12(string|string[] $gtin12)
 * @method static gtin13(string|string[] $gtin13)
 * @method static gtin14(string|string[] $gtin14)
 * @method static gtin8(string|string[] $gtin8)
 * @method static includesObject(TypeAndQuantityNode|TypeAndQuantityNode[] $includesObject)
 * @method static ineligibleRegion(GeoShape|GeoShape[]|Place|Place[]|string|string[] $ineligibleRegion)
 * @method static inventoryLevel(QuantitativeValue|QuantitativeValue[] $inventoryLevel)
 * @method static itemCondition(OfferItemCondition|OfferItemCondition[] $itemCondition)
 * @method static itemOffered(Product|Product[]|Service|Service[] $itemOffered)
 * @method static mpn(string|string[] $mpn)
 * @method static price(float|float[]|int|int[]|string|string[] $price)
 * @method static priceCurrency(string|string[] $priceCurrency)
 * @method static priceSpecification(PriceSpecification|PriceSpecification[] $priceSpecification)
 * @method static priceValidUntil(\DateTimeInterface|\DateTimeInterface[] $priceValidUntil)
 * @method static review(Review|Review[] $review)
 * @method static reviews(Review|Review[] $reviews)
 * @method static seller(Organization|Organization[]|Person|Person[] $seller)
 * @method static serialNumber(string|string[] $serialNumber)
 * @method static sku(string|string[] $sku)
 * @method static validFrom(\DateTimeInterface|\DateTimeInterface[] $validFrom)
 * @method static validThrough(\DateTimeInterface|\DateTimeInterface[] $validThrough)
 * @method static warranty(WarrantyPromise|WarrantyPromise[] $warranty)
 */
class Offer extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'acceptedPaymentMethod', 'addOn', 'advanceBookingRequirement', 'aggregateRating',
        'areaServed', 'availability', 'availabilityEnds', 'availabilityStarts', 'availableAtOrFrom',
        'availableDeliveryMethod', 'businessFunction', 'category', 'deliveryLeadTime',
        'eligibleCustomerType', 'eligibleDuration', 'eligibleQuantity', 'eligibleRegion',
        'eligibleTransactionVolume', 'gtin12', 'gtin13', 'gtin14', 'gtin8', 'includesObject',
        'ineligibleRegion', 'inventoryLevel', 'itemCondition', 'itemOffered', 'mpn', 'price',
        'priceCurrency', 'priceSpecification', 'priceValidUntil', 'review', 'reviews', 'seller',
        'serialNumber', 'sku', 'validFrom', 'validThrough', 'warranty',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Intangible',
    ];
}
