<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * The most generic kind of creative work, including books, movies, photographs, software programs,
 * etc.
 * @see http://schema.org/CreativeWork
 * @method static about(Thing|Thing[] $about)
 * @method static accessMode(string|string[] $accessMode)
 * @method static accessModeSufficient(string|string[] $accessModeSufficient)
 * @method static accessibilityAPI(string|string[] $accessibilityAPI)
 * @method static accessibilityControl(string|string[] $accessibilityControl)
 * @method static accessibilityFeature(string|string[] $accessibilityFeature)
 * @method static accessibilityHazard(string|string[] $accessibilityHazard)
 * @method static accessibilitySummary(string|string[] $accessibilitySummary)
 * @method static accountablePerson(Person|Person[] $accountablePerson)
 * @method static aggregateRating(AggregateRating|AggregateRating[] $aggregateRating)
 * @method static alternativeHeadline(string|string[] $alternativeHeadline)
 * @method static associatedMedia(MediaObject|MediaObject[] $associatedMedia)
 * @method static audience(Audience|Audience[] $audience)
 * @method static audio(AudioObject|AudioObject[] $audio)
 * @method static author(Organization|Organization[]|Person|Person[] $author)
 * @method static award(string|string[] $award)
 * @method static awards(string|string[] $awards)
 * @method static character(Person|Person[] $character)
 * @method static citation(CreativeWork|CreativeWork[]|string|string[] $citation)
 * @method static comment(Comment|Comment[] $comment)
 * @method static commentCount(int|int[] $commentCount)
 * @method static contentLocation(Place|Place[] $contentLocation)
 * @method static contentRating(Rating|Rating[]|string|string[] $contentRating)
 * @method static contributor(Organization|Organization[]|Person|Person[] $contributor)
 * @method static copyrightHolder(Organization|Organization[]|Person|Person[] $copyrightHolder)
 * @method static copyrightYear(float|float[]|int|int[] $copyrightYear)
 * @method static creator(Organization|Organization[]|Person|Person[] $creator)
 * @method static dateCreated(\DateTimeInterface|\DateTimeInterface[] $dateCreated)
 * @method static dateModified(\DateTimeInterface|\DateTimeInterface[] $dateModified)
 * @method static datePublished(\DateTimeInterface|\DateTimeInterface[] $datePublished)
 * @method static discussionUrl(string|string[] $discussionUrl)
 * @method static editor(Person|Person[] $editor)
 * @method static educationalAlignment(AlignmentObject|AlignmentObject[] $educationalAlignment)
 * @method static educationalUse(string|string[] $educationalUse)
 * @method static encoding(MediaObject|MediaObject[] $encoding)
 * @method static encodingFormat(string|string[] $encodingFormat)
 * @method static encodings(MediaObject|MediaObject[] $encodings)
 * @method static exampleOfWork(CreativeWork|CreativeWork[] $exampleOfWork)
 * @method static expires(\DateTimeInterface|\DateTimeInterface[] $expires)
 * @method static fileFormat(string|string[] $fileFormat)
 * @method static funder(Organization|Organization[]|Person|Person[] $funder)
 * @method static genre(string|string[] $genre)
 * @method static hasPart(CreativeWork|CreativeWork[] $hasPart)
 * @method static headline(string|string[] $headline)
 * @method static inLanguage(Language|Language[]|string|string[] $inLanguage)
 * @method static interactionStatistic(InteractionCounter|InteractionCounter[] $interactionStatistic)
 * @method static interactivityType(string|string[] $interactivityType)
 * @method static isAccessibleForFree(bool|bool[] $isAccessibleForFree)
 * @method static isBasedOn(CreativeWork|CreativeWork[]|Product|Product[]|string|string[] $isBasedOn)
 * @method static isBasedOnUrl(CreativeWork|CreativeWork[]|Product|Product[]|string|string[] $isBasedOnUrl)
 * @method static isFamilyFriendly(bool|bool[] $isFamilyFriendly)
 * @method static isPartOf(CreativeWork|CreativeWork[] $isPartOf)
 * @method static keywords(string|string[] $keywords)
 * @method static learningResourceType(string|string[] $learningResourceType)
 * @method static license(CreativeWork|CreativeWork[]|string|string[] $license)
 * @method static locationCreated(Place|Place[] $locationCreated)
 * @method static mainEntity(Thing|Thing[] $mainEntity)
 * @method static material(Product|Product[]|string|string[] $material)
 * @method static mentions(Thing|Thing[] $mentions)
 * @method static offers(Offer|Offer[] $offers)
 * @method static position(int|int[]|string|string[] $position)
 * @method static producer(Organization|Organization[]|Person|Person[] $producer)
 * @method static provider(Organization|Organization[]|Person|Person[] $provider)
 * @method static publication(PublicationEvent|PublicationEvent[] $publication)
 * @method static publisher(Organization|Organization[]|Person|Person[] $publisher)
 * @method static publishingPrinciples(CreativeWork|CreativeWork[]|string|string[] $publishingPrinciples)
 * @method static recordedAt(Event|Event[] $recordedAt)
 * @method static releasedEvent(PublicationEvent|PublicationEvent[] $releasedEvent)
 * @method static review(Review|Review[] $review)
 * @method static reviews(Review|Review[] $reviews)
 * @method static schemaVersion(string|string[] $schemaVersion)
 * @method static sourceOrganization(Organization|Organization[] $sourceOrganization)
 * @method static spatialCoverage(Place|Place[] $spatialCoverage)
 * @method static sponsor(Organization|Organization[]|Person|Person[] $sponsor)
 * @method static temporalCoverage(\DateTimeInterface|\DateTimeInterface[]|string|string[] $temporalCoverage)
 * @method static text(string|string[] $text)
 * @method static thumbnailUrl(string|string[] $thumbnailUrl)
 * @method static timeRequired(Duration|Duration[] $timeRequired)
 * @method static translator(Organization|Organization[]|Person|Person[] $translator)
 * @method static typicalAgeRange(string|string[] $typicalAgeRange)
 * @method static version(float|float[]|int|int[]|string|string[] $version)
 * @method static video(VideoObject|VideoObject[] $video)
 * @method static workExample(CreativeWork|CreativeWork[] $workExample)
 */
class CreativeWork extends BaseType
{
    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'about', 'accessMode', 'accessModeSufficient', 'accessibilityAPI', 'accessibilityControl',
        'accessibilityFeature', 'accessibilityHazard', 'accessibilitySummary', 'accountablePerson',
        'aggregateRating', 'alternativeHeadline', 'associatedMedia', 'audience', 'audio', 'author',
        'award', 'awards', 'character', 'citation', 'comment', 'commentCount', 'contentLocation',
        'contentRating', 'contributor', 'copyrightHolder', 'copyrightYear', 'creator',
        'dateCreated', 'dateModified', 'datePublished', 'discussionUrl', 'editor',
        'educationalAlignment', 'educationalUse', 'encoding', 'encodingFormat', 'encodings',
        'exampleOfWork', 'expires', 'fileFormat', 'funder', 'genre', 'hasPart', 'headline',
        'inLanguage', 'interactionStatistic', 'interactivityType', 'isAccessibleForFree',
        'isBasedOn', 'isBasedOnUrl', 'isFamilyFriendly', 'isPartOf', 'keywords',
        'learningResourceType', 'license', 'locationCreated', 'mainEntity', 'material', 'mentions',
        'offers', 'position', 'producer', 'provider', 'publication', 'publisher',
        'publishingPrinciples', 'recordedAt', 'releasedEvent', 'review', 'reviews', 'schemaVersion',
        'sourceOrganization', 'spatialCoverage', 'sponsor', 'temporalCoverage', 'text',
        'thumbnailUrl', 'timeRequired', 'translator', 'typicalAgeRange', 'version', 'video',
        'workExample',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Thing',
    ];
}
