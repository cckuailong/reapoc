<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\RevisionFieldsDefaults;
use GeminiLabs\SiteReviews\Review;

class RevisionController extends Controller
{
    /**
     * @param bool $performCheck
     * @param \WP_Post $lastRevision
     * @param \WP_Post $post
     * @return bool
     * @filter wp_save_post_revision_check_for_changes
     */
    public function filterCheckForChanges($performCheck, $lastRevision, $post)
    {
        return !Review::isReview($post)
            ? $performCheck
            : true;
    }

    /**
     * @param bool $hasChanged
     * @param \WP_Post $lastRevision
     * @param \WP_Post $post
     * @return bool
     * @filter wp_save_post_revision_post_has_changed
     */
    public function filterReviewHasChanged($hasChanged, $lastRevision, $post)
    {
        if (!Review::isReview($post)) {
            return $hasChanged;
        }
        $review = glsr(Query::class)->review($post->ID, true); // bypass the cache
        $revision = glsr(Database::class)->meta($lastRevision->ID, 'review');
        foreach ($revision as $key => $value) {
            if ((string) $review->$key !== (string) $value) {
                return true;
            }
        }
        return $hasChanged;
    }

    /**
     * @param \WP_Post|null $compareFrom
     * @param \WP_Post|null $compareTo
     * @return array
     * @filter wp_get_revision_ui_diff
     */
    public function filterRevisionUiDiff(array $return, $compareFrom, $compareTo)
    {
        $fields = glsr(RevisionFieldsDefaults::class)->defaults();
        $oldReview = $this->reviewFromRevision($compareFrom);
        $newReview = $this->reviewFromRevision($compareTo);
        foreach ($fields as $field => $name) {
            if ($diff = wp_text_diff($oldReview->$field, $newReview->$field, ['show_split_view' => true])) {
                $return[] = [
                    'diff' => $diff,
                    'id' => $field,
                    'name' => $name,
                ];
            }
        }
        return $return;
    }

    /**
     * @param int $reviewId
     * @param int $revisionId
     * @return void
     * @action wp_restore_post_revision
     */
    public function restoreRevision($reviewId, $revisionId)
    {
        if (!Review::isReview($reviewId)) {
            return;
        }
        if (is_array($revision = glsr(Database::class)->meta($revisionId, 'review'))) {
            glsr(ReviewManager::class)->updateRating($reviewId, $revision);
        }
    }

    /**
     * @param int $revisionId
     * @return void
     * @action _wp_put_post_revision
     */
    public function saveRevision($revisionId)
    {
        $postId = wp_is_post_revision($revisionId);
        if (Review::isReview($postId)) {
            $review = glsr(Query::class)->review($postId);
            $revision = glsr(RevisionFieldsDefaults::class)->defaults();
            foreach ($revision as $field => &$value) {
                $value = $review->$field;
            }
            glsr(Database::class)->metaSet($revisionId, 'review', $revision);
        }
    }

    /**
     * @param \WP_Post|null $post
     * @return Arguments|Review
     */
    protected function reviewFromRevision($post)
    {
        if (!get_post($post)) {
            return new Arguments([]);
        }
        if (wp_is_post_revision($post->ID)) {
            $meta = glsr(Database::class)->meta($post->ID, 'review');
            return new Review($meta);
        }
        return glsr(Query::class)->review($post->ID);
    }
}
