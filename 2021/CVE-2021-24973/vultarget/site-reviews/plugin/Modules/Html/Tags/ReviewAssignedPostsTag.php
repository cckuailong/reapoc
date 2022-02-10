<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Multilingual;

class ReviewAssignedPostsTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $postIds = glsr(Multilingual::class)->getPostIds(Arr::consolidate($value));
        if (!empty($postIds)) {
            $posts = get_posts([
                'post__in' => $postIds,
                'post_type' => 'any',
                'posts_per_page' => -1,
            ]);
            $titles = wp_list_pluck($posts, 'post_title');
            $tagValue = Str::naturalJoin(Arr::unique($titles));
            return $this->wrap($tagValue, 'span');
        }
    }
}
