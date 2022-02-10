<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Notice;

class TogglePinned implements Contract
{
    public $isPinned;
    public $review;

    public function __construct($input)
    {
        $this->review = glsr(Query::class)->review($input['id']);
        $this->isPinned = isset($input['pinned'])
            ? wp_validate_boolean($input['pinned'])
            : !$this->review->is_pinned;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (!glsr()->can('edit_others_posts')) {
            return wp_validate_boolean($this->review->is_pinned);
        }
        if ($this->isPinned !== $this->review->is_pinned) {
            glsr(ReviewManager::class)->updateRating($this->review->ID, [
                'is_pinned' => $this->isPinned,
            ]);
            $notice = $this->isPinned
                ? _x('Review pinned.', 'admin-text', 'site-reviews')
                : _x('Review unpinned.', 'admin-text', 'site-reviews');
            glsr(Notice::class)->addSuccess($notice);
        }
        return $this->isPinned;
    }
}
