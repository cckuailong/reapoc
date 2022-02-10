<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Review;

class AssignTerms implements Contract
{
    public $review;
    public $termIds;

    public function __construct(Review $review, array $termIds)
    {
        $this->review = $review;
        $this->termIds = $termIds;
    }

    /**
     * @return void
     */
    public function handle()
    {
        foreach ($this->termIds as $termId) {
            glsr(ReviewManager::class)->assignTerm($this->review, $termId);
        }
    }
}
