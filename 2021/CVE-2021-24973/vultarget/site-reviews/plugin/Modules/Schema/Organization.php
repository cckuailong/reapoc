<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

/**
 * An organization such as a school, NGO, corporation, club, etc.
 * @see http://schema.org/Organization
 * @method static address(PostalAddress|PostalAddress[]|string|string[] $address)
 * @method static aggregateRating(AggregateRating|AggregateRating[] $aggregateRating)
 * @method static areaServed(AdministrativeArea|AdministrativeArea[]|GeoShape|GeoShape[]|Place|Place[]|string|string[] $areaServed)
 * @method static award(string|string[] $award)
 * @method static awards(string|string[] $awards)
 * @method static brand(Brand|Brand[]|Organization|Organization[] $brand)
 * @method static contactPoint(ContactPoint|ContactPoint[] $contactPoint)
 * @method static contactPoints(ContactPoint|ContactPoint[] $contactPoints)
 * @method static department(Organization|Organization[] $department)
 * @method static dissolutionDate(\DateTimeInterface|\DateTimeInterface[] $dissolutionDate)
 * @method static duns(string|string[] $duns)
 * @method static email(string|string[] $email)
 * @method static employee(Person|Person[] $employee)
 * @method static employees(Person|Person[] $employees)
 * @method static event(Event|Event[] $event)
 * @method static events(Event|Event[] $events)
 * @method static faxNumber(string|string[] $faxNumber)
 * @method static founder(Person|Person[] $founder)
 * @method static founders(Person|Person[] $founders)
 * @method static foundingDate(\DateTimeInterface|\DateTimeInterface[] $foundingDate)
 * @method static foundingLocation(Place|Place[] $foundingLocation)
 * @method static funder(Organization|Organization[]|Person|Person[] $funder)
 * @method static globalLocationNumber(string|string[] $globalLocationNumber)
 * @method static hasOfferCatalog(OfferCatalog|OfferCatalog[] $hasOfferCatalog)
 * @method static hasPOS(Place|Place[] $hasPOS)
 * @method static isicV4(string|string[] $isicV4)
 * @method static legalName(string|string[] $legalName)
 * @method static leiCode(string|string[] $leiCode)
 * @method static location(Place|Place[]|PostalAddress|PostalAddress[]|string|string[] $location)
 * @method static logo(ImageObject|ImageObject[]|string|string[] $logo)
 * @method static makesOffer(Offer|Offer[] $makesOffer)
 * @method static member(Organization|Organization[]|Person|Person[] $member)
 * @method static memberOf(Organization|Organization[]|ProgramMembership|ProgramMembership[] $memberOf)
 * @method static members(Organization|Organization[]|Person|Person[] $members)
 * @method static naics(string|string[] $naics)
 * @method static numberOfEmployees(QuantitativeValue|QuantitativeValue[] $numberOfEmployees)
 * @method static offeredBy(Offer|Offer[]|Person|Person[] $offeredBy)
 * @method static owns(OwnershipInfo|OwnershipInfo[]|Product|Product[] $owns)
 * @method static parentOrganization(Organization|Organization[] $parentOrganization)
 * @method static publishingPrinciples(CreativeWork|CreativeWork[]|string|string[] $publishingPrinciples)
 * @method static review(Review|Review[] $review)
 * @method static reviews(Review|Review[] $reviews)
 * @method static seeks(Demand|Demand[] $seeks)
 * @method static serviceArea(AdministrativeArea|AdministrativeArea[]|GeoShape|GeoShape[]|Place|Place[] $serviceArea)
 * @method static sponsor(Organization|Organization[]|Person|Person[] $sponsor)
 * @method static subOrganization(Organization|Organization[] $subOrganization)
 * @method static taxID(string|string[] $taxID)
 * @method static telephone(string|string[] $telephone)
 * @method static vatID(string|string[] $vatID)
 */
class Organization extends BaseType
{
    /**
     * The schema.org Actions mechanism benefited from extensive discussions across the Web
     * standards community around W3C, in particular from the [Hydra project](http://purl.org/hydra/)
     * community group.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_ActionCollabClass
     */
    const ActionCollabClass = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_ActionCollabClass';

    /**
     * This element is based on the work of the Automotive Ontology Working Group,
     * see [www.automotive-ontology.org](http://www.automotive-ontology.org) for details.
     * Many class and property definitions are inspired by or based on abstracts from Wikipedia,
     * the free encyclopedia.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#Automotive_Ontology_Working_Group
     */
    const AutomotiveOntologyWGClass = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#Automotive_Ontology_Working_Group';

    /**
     * The W3C [Schema Bib Extend](http://www.w3.org/community/schemabibex/) (BibEx) group led the
     * work to improve schema.org for bibliographic information, including terms for periodicals,
     * articles and multi-volume works. The design was inspired in places (e.g. [[pageStart]],
     * [[pageEnd]], [[pagination]]) by the [Bibliographic Ontology](http://bibliontology.com/),
     * 'bibo'.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_bibex
     */
    const BibExTerm = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_bibex';

    /**
     * This class is based upon W3C DCAT work, and benefits from collaboration around the DCAT, ADMS
     * and VoID vocabularies. See http://www.w3.org/wiki/WebSchemas/Datasets for full details and
     * mappings.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_DatasetClass
     */
    const DatasetClass = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_DatasetClass';

    /**
     * This element is based on the work of the Financial Industry Business Ontology project (see
     * [http://www.fibo.org/schema](http://www.fibo.org/schema) for details), in support of the W3C
     * Financial Industry Business Ontology Community Group
     * ([http://www.fibo.org/community](http://www.fibo.org/community)). Many class and property
     * definitions are inspired by or based on [http://www.fibo.org](http://www.fibo.org).
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#FIBO
     */
    const FIBO = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#FIBO';

    /**
     * The implementation and use of Legal Entity Identifier (LEI) is supported by Global Legal
     * Entity Identifier Foundation [https://www.gleif.org](https://www.gleif.org).
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#GLEIF
     */
    const GLEIF = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#GLEIF';

    /**
     * This class is derived from the GoodRelations Vocabulary for E-Commerce, created by Martin
     * Hepp. GoodRelations is a data model for sharing e-commerce data on the Web that can be
     * expressed in a variety of syntaxes, including RDFa and HTML5 Microdata. More information
     * about GoodRelations can be found at
     * [http://purl.org/goodrelations/](http://purl.org/goodrelations/).
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_GoodRelationsClass
     */
    const GoodRelationsClass = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_GoodRelationsClass';

    /**
     * This term [uses](http://blog.schema.org/2012/11/good-relations-and-schemaorg.html)
     * terminology from the GoodRelations Vocabulary for E-Commerce, created by Martin Hepp.
     * GoodRelations is a data model for sharing e-commerce data on the Web. More information about
     * GoodRelations can be found at
     * [http://purl.org/goodrelations/](http://purl.org/goodrelations/).
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_GoodRelationsTerms
     */
    const GoodRelationsTerms = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_GoodRelationsTerms';

    /**
     * This element is based on work by the Web Applications for the Future Internet Lab, Institute
     * of Informatics and Telematics, Pisa, Italy.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#IIT-CNR.it
     */
    const IITCNRit = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#IIT-CNR.it';

    /**
     * This class is based on the work of the LRMI project, see lrmi.net for details.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_LRMIClass
     */
    const LRMIClass = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_LRMIClass';

    /**
     * This vocabulary was improved through collaboration with the MusicBrainz project
     *     ([www.musicbrainz.org](http://www.musicbrainz.org)), and is partially inspired by the
     * MusicBrainz and
     *     [Music Ontology](http://musicontology.com/docs/getting-started.html) schemas.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#MBZ
     */
    const MBZ = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#MBZ';

    /**
     * This element is based on the STI Accommodation Ontology, see <a
     * href="http://ontologies.sti-innsbruck.at/acco/ns.html">http://ontologies.sti-innsbruck.at/acco/ns.html</a>
     * for details.
     *     Many class and property definitions are inspired by or based on abstracts from Wikipedia,
     * the free encyclopedia.
     * @see https://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#STI_Accommodation_Ontology
     */
    const STI_Accommodation_Ontology = 'https://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#STI_Accommodation_Ontology';

    /**
     * The Question/Answer types were [based
     * on](https://www.w3.org/wiki/WebSchemas/QASchemaResearch) the Stack Overflow API.
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_QAStackExchange
     */
    const Stack_Exchange = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_QAStackExchange';

    /**
     * This term and associated definitions draws upon the work of [The Trust
     * Project](http://thetrustproject.org/).
     * @see https://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#TP-draws
     */
    const The_Trust_Project = 'https://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#TP-draws';

    /**
     * This element is based on the work of the [Tourism Structured Web Data Community
     * Group](https://www.w3.org/community/tourismdata).
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#Tourism
     */
    const Tourism = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#Tourism';

    /**
     * This class contains information contributed by
     * [http://wikidoc.org>WikiDoc](http://wikidoc.org>WikiDoc).
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_WikiDoc
     */
    const WikiDoc = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_WikiDoc';

    /**
     * This class contains derivatives of IPTC rNews properties. rNews is a data model of publishing
     * metadata with serializations currently available for RDFa as well as HTML5 Microdata. More
     * information about the IPTC and rNews can be found at [rnews.org](http://rnews.org).
     * @see http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_rNews
     */
    const rNews = 'http://www.w3.org/wiki/WebSchemas/SchemaDotOrgSources#source_rNews';

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $allowed = [
        'address', 'aggregateRating', 'areaServed', 'award', 'awards', 'brand', 'contactPoint',
        'contactPoints', 'department', 'dissolutionDate', 'duns', 'email', 'employee', 'employees',
        'event', 'events', 'faxNumber', 'founder', 'founders', 'foundingDate', 'foundingLocation',
        'funder', 'globalLocationNumber', 'hasOfferCatalog', 'hasPOS', 'isicV4', 'legalName',
        'leiCode', 'location', 'logo', 'makesOffer', 'member', 'memberOf', 'members', 'naics',
        'numberOfEmployees', 'offeredBy', 'owns', 'parentOrganization', 'publishingPrinciples',
        'review', 'reviews', 'seeks', 'serviceArea', 'sponsor', 'subOrganization', 'taxID',
        'telephone', 'vatID',
    ];

    /**
     * @var array
     * @see http://schema.org/{property_name}
     */
    public $parents = [
        'Thing',
    ];
}
