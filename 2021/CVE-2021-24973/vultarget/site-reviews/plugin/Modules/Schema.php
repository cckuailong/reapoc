<?php

namespace GeminiLabs\SiteReviews\Modules;

use DateTime;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Schema\UnknownType;
use GeminiLabs\SiteReviews\Review;

class Schema
{
    /**
     * @var array
     */
    protected $args;

    /**
     * @var array
     */
    protected $keyValues = [];

    /**
     * @var array|null
     */
    protected $ratingCounts;

    /**
     * @var \GeminiLabs\SiteReviews\Reviews|array
     */
    protected $reviews;

    /**
     * @return array
     */
    public function build(array $args = [], $reviews = [])
    {
        $this->args = $args;
        $this->reviews = $reviews;
        $schema = $this->buildSummary($args);
        if (!empty($schema)) {
            $reviewSchema = $this->buildReviews();
            array_walk($reviewSchema, function (&$review) {
                unset($review['@context']);
                unset($review['itemReviewed']);
            });
        }
        if (!empty($reviewSchema)) {
            $schema['review'] = $reviewSchema;
        }
        return $schema;
    }

    /**
     * @param array|null $args
     * @return array
     */
    public function buildSummary($args = null, array $ratings = [])
    {
        if (is_array($args)) {
            $this->args = $args;
        }
        $buildSummary = Helper::buildMethodName($this->getSchemaOptionValue('type'), 'buildSummaryFor');
        if ($count = array_sum($this->getRatingCounts($ratings))) {
            $schema = Helper::ifTrue(method_exists($this, $buildSummary),
                [$this, $buildSummary],
                [$this, 'buildSummaryForCustom']
            );
            $schema->aggregateRating(
                $this->getSchemaType('AggregateRating')
                    ->ratingValue($this->getRatingValue())
                    ->reviewCount($count)
                    ->bestRating(glsr()->constant('MAX_RATING', Rating::class))
                    ->worstRating(glsr()->constant('MIN_RATING', Rating::class))
            );
            $schema = $schema->toArray();
            return glsr()->filterArray('schema/'.$schema['@type'], $schema, $args);
        }
        return [];
    }

    /**
     * @return mixed
     */
    public function buildSummaryForCustom()
    {
        return $this->buildSchemaValues($this->getSchemaType(), [
            'description', 'identifier', 'image', 'name', 'url',
        ]);
    }

    /**
     * @return mixed
     */
    public function buildSummaryForLocalBusiness()
    {
        return $this->buildSchemaValues($this->buildSummaryForCustom(), [
            'address', 'priceRange', 'telephone',
        ]);
    }

    /**
     * @return mixed
     */
    public function buildSummaryForProduct()
    {
        $offerType = $this->getSchemaOption('offerType', 'AggregateOffer');
        $offers = $this->buildSchemaValues($this->getSchemaType($offerType), [
            'highPrice', 'lowPrice', 'price', 'priceCurrency',
        ]);
        $schema = $this->buildSummaryForCustom();
        if (empty($schema->toArray()['@id'])) {
            $schema->setProperty('identifier', $this->getSchemaOptionValue('url').'#product'); // this is converted to @id
        }
        return $schema->doIf(!empty($offers->getProperties()), function ($schema) use ($offers) {
            $schema->offers($offers);
        });
    }

    /**
     * @return void
     */
    public function render()
    {
        if ($schemas = glsr()->retrieve('schemas', [])) {
            printf('<script type="application/ld+json" class="%s-schema">%s</script>', 
                glsr()->id,
                json_encode(
                    glsr()->filterArray('schema/all', $schemas),
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
            );
        }
    }

    /**
     * @return void
     */
    public function store(array $schema)
    {
        if (!empty($schema)) {
            $schemas = Arr::consolidate(glsr()->retrieve('schemas'));
            $schemas[] = $schema;
            $schemas = array_map('unserialize', array_unique(array_map('serialize', $schemas)));
            glsr()->store('schemas', $schemas);
        }
    }

    /**
     * @param Review $review
     * @return array
     */
    protected function buildReview($review)
    {
        $schema = $this->getSchemaType('Review')
            ->doIf(!in_array('title', $this->args['hide']), function ($schema) use ($review) {
                $schema->name($review->title);
            })
            ->doIf(!in_array('excerpt', $this->args['hide']), function ($schema) use ($review) {
                $schema->reviewBody($review->content);
            })
            ->datePublished((new DateTime($review->date)))
            ->author($this->getSchemaType('Person')->name($review->author))
            ->itemReviewed($this->getSchemaType()->name($this->getSchemaOptionValue('name')));
        if (!empty($review->rating)) {
            $schema->reviewRating(
                $this->getSchemaType('Rating')
                    ->ratingValue($review->rating)
                    ->bestRating(glsr()->constant('MAX_RATING', Rating::class))
                    ->worstRating(glsr()->constant('MIN_RATING', Rating::class))
            );
        }
        return glsr()->filterArray('schema/review', $schema->toArray(), $review, $this->args);
    }

    /**
     * @return array
     */
    protected function buildReviews()
    {
        $reviews = [];
        foreach ($this->reviews as $review) {
            // Only include critic reviews that have been directly produced by your site, not reviews from third-party sites or syndicated reviews.
            // @see https://developers.google.com/search/docs/data-types/review
            if ('local' === $review->type) {
                $reviews[] = $this->buildReview($review);
            }
        }
        return $reviews;
    }

    /**
     * @param mixed $schema
     * @return mixed
     */
    protected function buildSchemaValues($schema, array $values = [])
    {
        foreach ($values as $value) {
            $option = $this->getSchemaOptionValue($value);
            if (!empty($option)) {
                $schema->$value($option);
            }
        }
        return $schema;
    }

    /**
     * @return array
     */
    protected function getRatingCounts(array $ratings = [])
    {
        if (!isset($this->ratingCounts)) {
            $this->ratingCounts = Helper::ifTrue(!empty($ratings), $ratings, function () {
                return glsr(RatingManager::class)->ratings($this->args);
            });
        }
        return $this->ratingCounts;
    }

    /**
     * @return int|float
     */
    protected function getRatingValue()
    {
        return glsr(Rating::class)->average($this->getRatingCounts());
    }

    /**
     * @param string $option
     * @param string $fallback
     * @return string
     */
    protected function getSchemaOption($option, $fallback)
    {
        $option = strtolower($option);
        if ($schemaOption = trim((string) get_post_meta(intval(get_the_ID()), 'schema_'.$option, true))) {
            return $schemaOption;
        }
        $setting = glsr(OptionManager::class)->get('settings.schema.'.$option);
        if (is_array($setting)) {
            return $this->getSchemaOptionDefault($setting, $fallback);
        }
        return Helper::ifEmpty($setting, $fallback, $strict = true);
    }

    /**
     * @param string $fallback
     * @return string
     */
    protected function getSchemaOptionDefault(array $setting, $fallback)
    {
        $setting = wp_parse_args($setting, [
            'custom' => '',
            'default' => $fallback,
        ]);
        return Helper::ifTrue('custom' === $setting['default'], 
            $setting['custom'], 
            $setting['default']
        );
    }

    /**
     * @param string $option
     * @param string $fallback
     * @return void|string
     */
    protected function getSchemaOptionValue($option, $fallback = 'post')
    {
        if (array_key_exists($option, $this->keyValues)) {
            return $this->keyValues[$option];
        }
        $value = $this->getSchemaOption($option, $fallback);
        if ($value !== $fallback) {
            return $this->setAndGetKeyValue($option, $value);
        }
        if (!is_singular()) {
            return;
        }
        $method = Helper::buildMethodName($option, 'getThing');
        if (method_exists($this, $method)) {
            return $this->setAndGetKeyValue($option, $this->$method());
        }
    }

    /**
     * @param string|null $type
     * @return mixed
     */
    protected function getSchemaType($type = null)
    {
        if (!is_string($type)) {
            $type = $this->getSchemaOption('type', 'LocalBusiness');
        }
        $className = Helper::buildClassName($type, 'Modules\Schema');
        return Helper::ifTrue(class_exists($className),
            function () use ($className) {
                return new $className();
            },
            function () use ($type) {
                return new UnknownType($type);
            }
        );
    }

    /**
     * @return string
     */
    protected function getThingDescription()
    {
        $post = get_post();
        $text = Arr::get($post, 'post_excerpt');
        if (empty($text)) {
            $text = Arr::get($post, 'post_content');
        }
        if (function_exists('excerpt_remove_blocks')) {
            $text = excerpt_remove_blocks($text);
        }
        $text = strip_shortcodes($text);
        $text = wpautop($text);
        $text = wptexturize($text);
        $text = wp_strip_all_tags($text);
        $text = str_replace(']]>', ']]&gt;', $text);
        return wp_trim_words($text, apply_filters('excerpt_length', 55));
    }

    /**
     * @return string
     */
    protected function getThingImage()
    {
        return (string) get_the_post_thumbnail_url(null, 'large');
    }

    /**
     * @return string
     */
    protected function getThingName()
    {
        return get_the_title();
    }

    /**
     * @return string
     */
    protected function getThingUrl()
    {
        return (string) get_the_permalink();
    }

    /**
     * @param string $option
     * @param string $value
     * @return string
     */
    protected function setAndGetKeyValue($option, $value)
    {
        $this->keyValues[$option] = $value;
        return $value;
    }
}
