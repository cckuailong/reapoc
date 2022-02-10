<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Reviews;

class ReviewsHtml extends \ArrayObject
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $args;

    /**
     * @var int
     */
    public $max_num_pages;

    /**
     * @var Reviews
     */
    public $reviews;

    /**
     * @var array
     */
    public $rendered;

    /**
     * @var string
     */
    public $style;

    public function __construct(Reviews $reviews)
    {
        $this->args = glsr()->args($reviews->args);
        $this->max_num_pages = $reviews->max_num_pages;
        $this->reviews = $reviews;
        $this->rendered = $this->renderReviews($reviews);
        $this->style = glsr(Style::class)->styleClasses();
        parent::__construct($this->reviews, \ArrayObject::STD_PROP_LIST | \ArrayObject::ARRAY_AS_PROPS);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return glsr(Template::class)->build('templates/reviews', [
            'args' => $this->args,
            'context' => [
                'assigned_to' => $this->args->assigned_posts,
                'category' => $this->args->assigned_terms,
                'class' => $this->getClasses(),
                'id' => '', // @deprecated in v5.0
                'pagination' => Helper::ifTrue(!empty($this->args->pagination), $this->getPagination()),
                'reviews' => $this->getReviews(),
            ],
            'reviews' => $this->reviews,
        ]);
    }

    /**
     * @param bool $wrap
     * @return string
     */
    public function getPagination($wrap = true)
    {
        $html = glsr(Partial::class)->build('pagination', [
            'add_args' => $this->args->pageUrlParameters,
            'baseUrl' => $this->args->pageUrl,
            'current' => $this->args->page,
            'total' => $this->max_num_pages,
            'type' => $this->args->pagination, // @hack this is overridden by paginate_links, we use it to pass the pagination setting
        ]);
        if (!$wrap || empty($html)) { // only display the pagination when it's needed
            return $html;
        }
        $ajaxClass = Helper::ifTrue('ajax' == $this->args->pagination, 'glsr-ajax-pagination');
        $dataAttributes = glsr(SiteReviewsDefaults::class)->dataAttributes($this->args->toArray());
        return glsr(Builder::class)->div(wp_parse_args([
            'class' => trim('glsr-pagination '.$ajaxClass),
            'data-id' => $this->args->id,
            'text' => $html,
        ], $dataAttributes));
    }

    /**
     * @return string
     */
    public function getReviews()
    {
        return empty($this->rendered)
            ? $this->getReviewsFallback()
            : implode(PHP_EOL, $this->rendered);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (array_key_exists($key, $this->rendered)) {
            return $this->rendered[$key];
        }
        if (in_array($key, ['navigation', 'pagination'])) { // @deprecated in v5.0 (navigation)
            return $this->getPagination();
        }
        return property_exists($this, $key)
            ? $this->$key
            : glsr()->filter('reviews/html/'.$key, null, $this);
    }

    /**
     * @return string
     */
    protected function getClasses()
    {
        $defaults = ['glsr-reviews'];
        $classes = explode(' ', $this->args->class);
        $classes = array_unique(array_merge($defaults, array_filter($classes)));
        return implode(' ', $classes);
    }

    /**
     * @return string
     */
    protected function getReviewsFallback()
    {
        if (empty($this->args->fallback) && glsr(OptionManager::class)->getBool('settings.reviews.fallback')) {
            $this->args->fallback = __('There are no reviews yet. Be the first one to write one.', 'site-reviews');
        }
        $fallback = glsr(Builder::class)->p([
            'class' => 'glsr-no-margins',
            'text' => $this->args->fallback,
        ]);
        return glsr()->filterString('reviews/fallback', $fallback, $this->args->toArray());
    }

    /**
     * @return array
     */
    protected function renderReviews(Reviews $reviews)
    {
        $rendered = [];
        foreach ($reviews as $review) {
            $rendered[] = $review->build($this->args->toArray());
        }
        return $rendered;
    }
}
