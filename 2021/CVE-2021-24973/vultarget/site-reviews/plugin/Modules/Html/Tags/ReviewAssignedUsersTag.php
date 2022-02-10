<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAssignedUsersTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        $users = wp_list_pluck($this->review->assignedUsers(), 'display_name');
        $tagValue = Str::naturalJoin($users);
        return $this->wrap($tagValue, 'span');
    }
}
