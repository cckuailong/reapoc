<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Schema;

class SiteReviewsSummaryShortcode extends Shortcode
{
    /**
     * @var array
     */
    public $args;

    /**
     * @var array
     */
    protected $ratings;

    /**
     * {@inheritdoc}
     */
    public function buildTemplate(array $args = [])
    {
        $this->args = $args;
        $this->ratings = glsr(RatingManager::class)->ratings($args);
        if ($this->isEmpty()) {
            return;
        }
        $this->generateSchema();
        return glsr(Template::class)->build('templates/reviews-summary', [
            'args' => $this->args,
            'context' => [
                'class' => $this->getClasses(),
                'id' => '', // @deprecated in v5.0
                'percentages' => $this->buildTemplateTag('percentages'),
                'rating' => $this->buildTemplateTag('rating'),
                'stars' => $this->buildTemplateTag('stars'),
                'text' => $this->buildTemplateTag('text'),
            ],
        ]);
    }

    /**
     * @param string $tag
     * @return string
     */
    protected function buildTemplateTag($tag)
    {
        $args = $this->args;
        $className = Helper::buildClassName(['summary', $tag, 'tag'], 'Modules\Html\Tags');
        $className = glsr()->filterString('summary/tag/'.$tag, $className, $this);
        $field = class_exists($className)
            ? glsr($className, compact('tag', 'args'))->handleFor('summary', null, $this->ratings)
            : null;
        return glsr()->filterString('summary/build/'.$tag, $field, $this->ratings, $this);
    }

    /**
     * @return void
     */
    protected function generateSchema()
    {
        if (Cast::toBool($this->args['schema'])) {
            glsr(Schema::class)->store(
                glsr(Schema::class)->buildSummary($this->args, $this->ratings)
            );
        }
    }

    /**
     * @return string
     */
    protected function getClasses()
    {
        return trim('glsr-summary '.$this->args['class']);
    }

    /**
     * @return array
     */
    protected function hideOptions()
    {
        return [
            'rating' => _x('Hide the rating', 'admin-text', 'site-reviews'),
            'stars' => _x('Hide the stars', 'admin-text', 'site-reviews'),
            'summary' => _x('Hide the summary', 'admin-text', 'site-reviews'),
            'bars' => _x('Hide the percentage bars', 'admin-text', 'site-reviews'),
            'if_empty' => _x('Hide if no reviews are found', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return bool
     */
    protected function isEmpty()
    {
        return !array_sum($this->ratings) && in_array('if_empty', $this->args['hide']);
    }
}
