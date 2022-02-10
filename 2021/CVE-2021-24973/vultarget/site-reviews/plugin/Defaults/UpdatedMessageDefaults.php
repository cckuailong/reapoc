<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class UpdatedMessageDefaults extends Defaults
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'approved' => _x('Review has been approved and published.', 'admin-text', 'site-reviews'),
            'draft_updated' => _x('Review draft updated.', 'admin-text', 'site-reviews'),
            'preview' => _x('Preview review', 'admin-text', 'site-reviews'),
            'published' => _x('Review approved and published.', 'admin-text', 'site-reviews'),
            'restored' => _x('Review restored to revision from %s.', 'admin-text', 'site-reviews'),
            'reverted' => _x('Review has been reverted to its original submission state (title, content, and submission date).', 'admin-text', 'site-reviews'),
            'saved' => _x('Review saved.', 'admin-text', 'site-reviews'),
            'scheduled' => _x('Review scheduled for: %s.', 'admin-text', 'site-reviews'),
            'submitted' => _x('Review submitted.', 'admin-text', 'site-reviews'),
            'unapproved' => _x('Review has been unapproved and is now pending.', 'admin-text', 'site-reviews'),
            'updated' => _x('Review updated.', 'admin-text', 'site-reviews'),
            'view' => _x('View review', 'admin-text', 'site-reviews'),
        ];
    }
}
