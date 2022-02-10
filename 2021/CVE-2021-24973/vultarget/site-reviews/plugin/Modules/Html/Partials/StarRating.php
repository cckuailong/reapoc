<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Partials;

use GeminiLabs\SiteReviews\Contracts\PartialContract;
use GeminiLabs\SiteReviews\Defaults\StarRatingDefaults;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Rating;

class StarRating implements PartialContract
{
    /**
     * @var \GeminiLabs\SiteReviews\Arguments
     */
    public $data;

    /**
     * {@inheritdoc}
     */
    public function build(array $data = [])
    {
        $this->data($data);
        $maxRating = glsr()->constant('MAX_RATING', Rating::class);
        $numFull = intval(floor($this->data->rating));
        $numHalf = intval(ceil($this->data->rating - $numFull));
        $numEmpty = max(0, $maxRating - $numFull - $numHalf);
        $title = $this->data->count > 0
            ? __('Rated <strong>%s</strong> out of %s based on %s ratings', 'site-reviews')
            : __('Rated <strong>%s</strong> out of %s', 'site-reviews');
        return glsr(Template::class)->build('templates/rating/stars', [
            'args' => glsr()->args($this->data->args),
            'context' => [
                'empty_stars' => $this->getTemplate('empty-star', $numEmpty),
                'full_stars' => $this->getTemplate('full-star', $numFull),
                'half_stars' => $this->getTemplate('half-star', $numHalf),
                'prefix' => $this->data->prefix,
                'rating' => $this->data->rating,
                'title' => sprintf($title, $this->data->rating, $maxRating, $this->data->count),
            ],
            'partial' => $this,
        ]);
    }

    /**
     * @return static
     */
    public function data(array $data = [])
    {
        $data = glsr(StarRatingDefaults::class)->merge($data);
        $this->data = glsr()->args($data);
        return $this;
    }

    /**
     * @param string $templateName
     * @param int $timesRepeated
     * @return string
     */
    protected function getTemplate($templateName, $timesRepeated)
    {
        $template = glsr(Template::class)->build('templates/rating/'.$templateName, [
            'args' => $this->data->args,
            'context' => [
                'prefix' => $this->data->prefix,
            ],
            'partial' => $this,
        ]);
        return str_repeat($template, $timesRepeated);
    }
}
