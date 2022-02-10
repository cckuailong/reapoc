<?php

namespace GeminiLabs\SiteReviews\Shortcodes;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\ReviewsHtml;
use GeminiLabs\SiteReviews\Modules\Schema;
use GeminiLabs\SiteReviews\Reviews;

class SiteReviewsShortcode extends Shortcode
{
    /**
     * @var array
     */
    public $args;

    /**
     * @return ReviewsHtml
     */
    public function buildReviewsHtml(array $args = [])
    {
        $this->args = glsr(SiteReviewsDefaults::class)->unguardedMerge($args);
        $reviews = glsr(ReviewManager::class)->reviews($this->args);
        $this->generateSchema($reviews);
        if ('modal' === glsr_get_option('reviews.excerpts_action')) {
            glsr()->store('use_modal', true);
        }
        return new ReviewsHtml($reviews);
    }

    /**
     * {@inheritdoc}
     */
    public function buildTemplate(array $args = [])
    {
        return (string) $this->buildReviewsHtml($args);
    }

    /**
     * @return void
     */
    public function generateSchema(Reviews $reviews)
    {
        if (Cast::toBool($this->args['schema'])) {
            glsr(Schema::class)->store(
                glsr(Schema::class)->build($this->args, $reviews)
            );
        }
    }

    /**
     * @return array
     */
    protected function hideOptions()
    {
        return [
            'title' => _x('Hide the title', 'admin-text', 'site-reviews'),
            'rating' => _x('Hide the rating', 'admin-text', 'site-reviews'),
            'date' => _x('Hide the date', 'admin-text', 'site-reviews'),
            'assigned_links' => _x('Hide the assigned links (if shown)', 'admin-text', 'site-reviews'),
            'content' => _x('Hide the content', 'admin-text', 'site-reviews'),
            'avatar' => _x('Hide the avatar (if shown)', 'admin-text', 'site-reviews'),
            'author' => _x('Hide the author', 'admin-text', 'site-reviews'),
            'response' => _x('Hide the response', 'admin-text', 'site-reviews'),
        ];
    }
}
