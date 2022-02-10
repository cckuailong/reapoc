<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Review;

class UnassignUsers implements Contract
{
    public $review;
    public $userIds;

    public function __construct(Review $review, array $userIds)
    {
        $this->review = $review;
        $this->userIds = $userIds;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->userIds as $userId) {
            glsr(ReviewManager::class)->unassignUser($this->review, $userId);
        }
    }
}
