<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryPercentagesTag extends SummaryTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            return $this->wrap($this->percentages());
        }
    }

    /**
     * @return void|string
     */
    protected function percentages()
    {
        $percentages = preg_filter('/$/', '%', glsr(Rating::class)->percentages($this->ratings));
        $ratingRange = range(glsr()->constant('MAX_RATING', Rating::class), 1);
        return array_reduce($ratingRange, function ($carry, $level) use ($percentages) {
            $label = $this->ratingLabel($level);
            $bar = $this->ratingBar($level, $percentages);
            $info = $this->ratingInfo($level, $percentages);
            $value = $label.$bar.$info;
            $value = glsr()->filterString('summary/wrap/bar', $value, $this->args, [
                'info' => wp_strip_all_tags($info, true),
                'rating' => $level,
            ]);
            return $carry.glsr(Builder::class)->div([
                'class' => 'glsr-bar',
                'data-level' => $level,
                'text' => $value,
            ]);
        });
    }

    /**
     * @param int $level
     * @return string
     */
    protected function ratingBar($level, array $percentages)
    {
        $background = glsr(Builder::class)->span([
            'class' => 'glsr-bar-background-percent',
            'style' => 'width:'.$percentages[$level],
        ]);
        return glsr(Builder::class)->span([
            'class' => 'glsr-bar-background',
            'text' => $background,
        ]);
    }

    /**
     * @param int $level
     * @return string
     */
    protected function ratingInfo($level, array $percentages)
    {
        $count = glsr()->filterString('summary/counts', $percentages[$level], $this->ratings[$level]);
        return glsr(Builder::class)->span([
            'class' => 'glsr-bar-percent',
            'text' => $count,
        ]);
    }

    /**
     * @param int $level
     * @return string
     */
    protected function ratingLabel($level)
    {
        $label = $this->args->get('labels.'.$level);
        return glsr(Builder::class)->span([
            'class' => 'glsr-bar-label',
            'text' => $label,
        ]);
    }
}
