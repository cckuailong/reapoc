<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class ReviewAssignedLinksTag extends ReviewTag
{
    /**
     * @param mixed $value
     * @return array
     */
    public static function assignedLinks($value)
    {
        $links = [];
        $usedIds = [];
        foreach (Arr::consolidate($value) as $postId) {
            $postId = Helper::getPostId(glsr(Multilingual::class)->getPostId($postId));
            if (!empty($postId) && !in_array($postId, $usedIds)) {
                $title = get_the_title($postId);
                if (empty(trim($title))) {
                    $title = _x('No title', 'admin-text', 'site-reviews');
                }
                $links[] = glsr(Builder::class)->a([
                    'href' => get_the_permalink($postId),
                    'text' => $title,
                ]);
                $usedIds[] = $postId;
                $usedIds = Arr::unique($usedIds);
            }
        }
        return $links;
    }

    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden('reviews.assigned_links')) {
            $links = static::assignedLinks($value);
            $tagValue = !empty($links)
                ? sprintf(__('Review of %s', 'site-reviews'), Str::naturalJoin($links))
                : '';
            return $this->wrap($tagValue, 'span');
        }
    }
}
