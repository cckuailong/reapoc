<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;

/**
 * @property string $avatar
 * @property string $content
 * @property string $date
 * @property string $author
 * @property int $rating
 * @property string $response
 * @property string $title
 * etc.
 */
class ReviewHtml extends \ArrayObject
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var array
     */
    public $context;

    /**
     * @var Review
     */
    public $review;

    public function __construct(Review $review, array $args = [])
    {
        $this->args = glsr(SiteReviewsDefaults::class)->unguardedMerge($args);
        $this->context = $this->buildContext($review);
        $this->review = $review;
        parent::__construct($this->context, \ArrayObject::STD_PROP_LIST|\ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string|void
     */
    public function __toString()
    {
        if (empty($this->context)) {
            return '';
        }
        return glsr(Template::class)->build('templates/review', [
            'args' => $this->args,
            'context' => $this->context,
            'review' => $this->review,
        ]);
    }

    /**
     * @return array
     */
    public function buildContext(Review $review)
    {
        $context = $this->buildTemplateTags($review);
        return glsr()->filterArray('review/build/context', $context, $review, $this);
    }

    /**
     * @param string $tag
     * @param string|array $value
     * @return string
     */
    public function buildTemplateTag(Review $review, $tag, $value)
    {
        $args = $this->args;
        $className = Helper::buildClassName(['review', $tag, 'tag'], 'Modules\Html\Tags');
        $className = glsr()->filterString('review/tag/'.$tag, $className, $this);
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('review', $value, $review)
            : Cast::toString($value, false);
        return glsr()->filterString('review/build/tag/'.$tag, $field, $value, $review, $this);
    }

    /**
     * @return array
     */
    public function buildTemplateTags(Review $review)
    {
        glsr()->action('review/build/before', $review, $this);
        $templateTags = [];
        $assignedTag = array_filter([
            'assigned_posts' => $review->assigned_posts,
            'assigned_terms' => $review->assigned_terms,
            'assigned_users' => $review->assigned_users,
        ]);
        $templateTags['assigned'] = json_encode($assignedTag);
        foreach ($review as $key => $value) {
            $tag = $this->normalizeTemplateTag($key);
            $templateTags[$tag] = $this->buildTemplateTag($review, $tag, $value);
        }
        return glsr()->filterArray('review/build/after', $templateTags, $review, $this);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->context)) {
            return $this->context[$key];
        }
        $key = Helper::ifTrue('values' === $key, 'context', $key); // @deprecated in v5.0
        return Helper::ifTrue(property_exists($this, $key), $this->$key);
    }

    /**
     * @param string $tag
     * @return string
     */
    protected function normalizeTemplateTag($tag)
    {
        $mappedTags = [
            'ID' => 'review_id',
        ];
        return Arr::get($mappedTags, $tag, $tag);
    }
}
