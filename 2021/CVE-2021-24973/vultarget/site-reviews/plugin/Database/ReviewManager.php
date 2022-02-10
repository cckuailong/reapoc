<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Defaults\UpdateReviewDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;
use GeminiLabs\SiteReviews\Reviews;

class ReviewManager
{
    /**
     * @param int $postId
     * @return int|false
     */
    public function assignPost(Review $review, $postId)
    {
        $where = [
            'is_published' => 'publish' === get_post_status($postId),
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ];
        if ($result = glsr(Database::class)->insert('assigned_posts', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            if (!defined('WP_IMPORTING')) {
                glsr(CountManager::class)->posts($postId);
            }
        }
        return $result;
    }

    /**
     * @param int $termId
     * @return int|false
     */
    public function assignTerm(Review $review, $termId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ];
        if ($result = glsr(Database::class)->insert('assigned_terms', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            if (!defined('WP_IMPORTING')) {
                glsr(CountManager::class)->terms($termId);
            }
        }
        return $result;
    }

    /**
     * @param int $userId
     * @return int|false
     */
    public function assignUser(Review $review, $userId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ];
        if ($result = glsr(Database::class)->insert('assigned_users', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            if (!defined('WP_IMPORTING')) {
                glsr(CountManager::class)->users($userId);
            }
        }
        return $result;
    }

    /**
     * @return false|Review
     */
    public function create(CreateReview $command, $postId = null)
    {
        if (empty($postId)) {
            $postId = $this->createRaw($command);
        }
        $review = $this->get($postId);
        if ($review->isValid()) {
            glsr()->action('review/created', $review, $command);
            return $this->get($review->ID); // return a fresh copy of the review
        }
        return false;
    }

    /**
     * @param int $postId
     * @return false|Review
     */
    public function createFromPost($postId)
    {
        if (!Review::isReview($postId)) {
            return false;
        }
        $command = new CreateReview(new Request([]));
        glsr()->action('review/create', $postId, $command);
        return $this->create($command, $postId);
    }

    /**
     * @return false|int
     */
    public function createRaw(CreateReview $command)
    {
        $values = glsr()->args($command->toArray()); // this filters the values
        $postValues = [
            'comment_status' => 'closed',
            'meta_input' => ['_submitted' => $command->request->toArray()], // save the original submitted request in metadata
            'ping_status' => 'closed',
            'post_content' => $values->content,
            'post_date' => $values->date,
            'post_date_gmt' => $values->date_gmt,
            'post_name' => uniqid($values->type),
            'post_status' => $this->postStatus($command),
            'post_title' => $values->title,
            'post_type' => glsr()->post_type,
        ];
        $postId = wp_insert_post($postValues, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message())->debug($postValues);
            return false;
        }
        glsr()->action('review/create', $postId, $command);
        return $postId;
    }

    /**
     * This only deletes the entry in the ratings table!
     * @param int $reviewId
     * @return int|false
     */
    public function delete($reviewId)
    {
        $result = glsr(Database::class)->delete('ratings', ['review_id' => $reviewId]);
        if ($result) {
            glsr(Cache::class)->delete($reviewId, 'reviews');
        }
        return $result;
    }

    /**
     * @param int $reviewId
     * @return void
     */
    public function deleteRevisions($reviewId)
    {
        $revisionIds = glsr(Query::class)->revisionIds($reviewId);
        foreach ($revisionIds as $revisionId) {
            wp_delete_post_revision($revisionId);
        }
    }

    /**
     * @param int $reviewId
     * @return Review
     */
    public function get($reviewId)
    {
        $reviewId = Helper::getPostId($reviewId);
        $review = glsr(Query::class)->review($reviewId);
        glsr()->action('get/review', $review, $reviewId);
        return $review;
    }

    /**
     * @return Reviews
     */
    public function reviews(array $args = [])
    {
        $args = (new NormalizePaginationArgs($args))->toArray();
        $results = glsr(Query::class)->reviews($args);
        $total = $this->total($args, $results);
        $reviews = new Reviews($results, $total, $args);
        glsr()->action('get/reviews', $reviews, $args);
        return $reviews;
    }

    /**
     * @return int
     */
    public function total(array $args = [], array $reviews = [])
    {
        return glsr(Query::class)->totalReviews($args, $reviews);
    }

    /**
     * @param int $postId
     * @return int|false
     */
    public function unassignPost(Review $review, $postId)
    {
        $where = [
            'post_id' => $postId,
            'rating_id' => $review->rating_id,
        ];
        if ($result = glsr(Database::class)->delete('assigned_posts', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->posts($postId);
        }
        return $result;
    }

    /**
     * @param int $termId
     * @return int|false
     */
    public function unassignTerm(Review $review, $termId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'term_id' => $termId,
        ];
        if ($result = glsr(Database::class)->delete('assigned_terms', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->terms($termId);
        }
        return $result;
    }

    /**
     * @param int $userId
     * @return int|false
     */
    public function unassignUser(Review $review, $userId)
    {
        $where = [
            'rating_id' => $review->rating_id,
            'user_id' => $userId,
        ];
        if ($result = glsr(Database::class)->delete('assigned_users', $where)) {
            glsr(Cache::class)->delete($review->ID, 'reviews');
            glsr(CountManager::class)->users($userId);
        }
        return $result;
    }

    /**
     * @param int $reviewId
     * @return Review|false  Return false on failure
     */
    public function update($reviewId, array $data = [])
    {
        if (false === $this->updateRating($reviewId, $data)) {
            return false;
        }
        if (false === $this->updateReview($reviewId, $data)) {
            return false;
        }
        $this->updateCustom($reviewId, $data);
        $this->updateResponse($reviewId, Arr::get($data, 'response'));
        $review = glsr(Query::class)->review($reviewId);
        if ($assignedPosts = Arr::uniqueInt(Arr::get($data, 'assigned_posts'))) {
            glsr()->action('review/updated/post_ids', $review, $assignedPosts); // trigger a recount of assigned posts
        }
        if ($assignedUsers = Arr::uniqueInt(Arr::get($data, 'assigned_users'))) {
            glsr()->action('review/updated/user_ids', $review, $assignedUsers); // trigger a recount of assigned posts
        }
        $review = glsr(Query::class)->review($reviewId); // get a fresh copy of the review
        glsr()->action('review/saved', $review, $data);
        return $review;
    }

    /**
     * @param int $postId
     * @param bool $isPublished
     * @return int|bool
     */
    public function updateAssignedPost($postId, $isPublished)
    {
        $isPublished = wp_validate_boolean($isPublished);
        $postId = Cast::toInt($postId);
        return glsr(Database::class)->update('assigned_posts',
            ['is_published' => $isPublished],
            ['post_id' => $postId]
        );
    }

    /**
     * @param int $reviewId
     * @return array
     */
    public function updateCustom($reviewId, array $data = [])
    {
        $data = glsr(CustomFieldsDefaults::class)->merge($data);
        $data = Arr::prefixKeys($data, 'custom_');
        foreach ($data as $metaKey => $metaValue) {
            glsr(Database::class)->metaSet($reviewId, $metaKey, $metaValue);
        }
        return $data;
    }

    /**
     * @param int $reviewId
     * @return int|false  Returns false on error
     */
    public function updateRating($reviewId, array $data = [])
    {
        glsr(Cache::class)->delete($reviewId, 'reviews');
        $sanitized = glsr(RatingDefaults::class)->restrict($data);
        if ($data = array_intersect_key($sanitized, $data)) {
            return glsr(Database::class)->update('ratings', $data, [
                'review_id' => $reviewId,
            ]);
        }
        return 0;
    }

    /**
     * @param int $reviewId
     * @param string $response
     * @return int|bool
     */
    public function updateResponse($reviewId, $response = '')
    {
        $response = Cast::toString($response);
        $response = glsr(Sanitizer::class)->sanitizeTextHtml($response);
        $userId = Helper::ifTrue(empty($response), 0, get_current_user_id());
        $review = glsr_get_review($reviewId);
        glsr()->action('review/responded', $review, $response, $userId);
        glsr(Database::class)->metaSet($reviewId, 'response_by', $userId); // prefixed metakey
        $result = glsr(Database::class)->metaSet($reviewId, 'response', $response); // prefixed metakey
        glsr(Cache::class)->delete($review->ID, 'reviews');
        return $result;
    }

    /**
     * @param int $reviewId
     * @return int|false  Returns false on failure
     */
    public function updateReview($reviewId, array $data = [])
    {
        if (glsr()->post_type !== get_post_type($reviewId)) {
            return 0;
        }
        glsr(Cache::class)->delete($reviewId, 'reviews');
        $sanitized = glsr(UpdateReviewDefaults::class)->restrict($data);
        if ($data = array_intersect_key($sanitized, $data)) {
            $data = array_filter([
                'post_content' => Arr::get($data, 'content'),
                'post_date' => Arr::get($data, 'date'),
                'post_date_gmt' => Arr::get($data, 'date_gmt'),
                'post_status' => Arr::get($data, 'status'),
                'post_title' => Arr::get($data, 'title'),
            ]);
        }
        if (!empty($data)) {
            $result = wp_update_post(wp_parse_args(['ID' => $reviewId], $data), true);
            if (is_wp_error($result)) {
                glsr_log()->error($result->get_error_message());
                return false;
            }
        }
        return 0;
    }

    /**
     * @return string
     */
    protected function postStatus(CreateReview $command)
    {
        $isApproved = $command->is_approved;
        if (!defined('WP_IMPORTING')) {
            $requireApproval = glsr(OptionManager::class)->getBool('settings.general.require.approval');
            $requireApprovalForRating = glsr(OptionManager::class)->getInt('settings.general.require.approval_for', 5);
            $isApproved = !$requireApproval || $command->rating > $requireApprovalForRating;
        }
        return !$isApproved || ('local' === $command->type && $command->blacklisted)
            ? 'pending'
            : 'publish';
    }
}
